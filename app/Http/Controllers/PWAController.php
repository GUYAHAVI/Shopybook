<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\Business;

class PWAController extends Controller
{
    /**
     * Get PWA status and configuration
     */
    public function getStatus()
    {
        $user = auth()->user();
        $business = $user->business;
        
        return response()->json([
            'status' => 'success',
            'data' => [
                'isOnline' => true,
                'isInstalled' => $this->checkIfInstalled(),
                'version' => '1.0.0',
                'lastSync' => Cache::get('pwa_last_sync_' . $user->id, null),
                'offlineData' => $this->getOfflineData($user),
                'business' => $business ? [
                    'id' => $business->id,
                    'name' => $business->name,
                    'type' => $business->business_type,
                    'hasProducts' => $business->products()->count() > 0,
                    'hasCustomers' => $business->customers()->count() > 0,
                    'hasOrders' => $business->orders()->count() > 0,
                ] : null,
                'features' => [
                    'offlineMode' => true,
                    'backgroundSync' => true,
                    'pushNotifications' => true,
                    'installPrompt' => true,
                ]
            ]
        ]);
    }
    
    /**
     * Sync offline data when connection is restored
     */
    public function syncOfflineData(Request $request)
    {
        $user = auth()->user();
        $data = $request->all();
        
        try {
            // Process offline actions
            if (isset($data['actions']) && is_array($data['actions'])) {
                foreach ($data['actions'] as $action) {
                    $this->processOfflineAction($action);
                }
            }
            
            // Update last sync time
            Cache::put('pwa_last_sync_' . $user->id, now(), 3600);
            
            return response()->json([
                'status' => 'success',
                'message' => 'Offline data synced successfully',
                'syncedAt' => now()->toISOString()
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to sync offline data: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get cached data for offline use
     */
    public function getOfflineData(User $user)
    {
        $business = $user->business;
        
        if (!$business) {
            return null;
        }
        
        // Cache key for offline data
        $cacheKey = 'pwa_offline_data_' . $business->id;
        
        // Check if we have cached data
        $cachedData = Cache::get($cacheKey);
        
        if ($cachedData) {
            return $cachedData;
        }
        
        // Generate offline data
        $offlineData = [
            'products' => $business->products()
                ->select('id', 'name', 'price', 'stock_quantity', 'description', 'image')
                ->get(),
            'customers' => $business->customers()
                ->select('id', 'name', 'email', 'phone', 'address')
                ->get(),
            'categories' => $business->categories()
                ->select('id', 'name', 'description')
                ->get(),
            'recentOrders' => $business->orders()
                ->with(['customer:id,name', 'items.product:id,name,price'])
                ->latest()
                ->limit(10)
                ->get(),
            'businessInfo' => [
                'name' => $business->name,
                'description' => $business->description,
                'address' => $business->address,
                'phone' => $business->phone,
                'email' => $business->email,
            ],
            'lastUpdated' => now()->toISOString()
        ];
        
        // Cache the data for 24 hours
        Cache::put($cacheKey, $offlineData, 86400);
        
        return $offlineData;
    }
    
    /**
     * Store offline action for later sync
     */
    public function storeOfflineAction(Request $request)
    {
        $user = auth()->user();
        $action = $request->all();
        
        // Add timestamp and user info
        $action['timestamp'] = now()->toISOString();
        $action['user_id'] = $user->id;
        $action['id'] = uniqid('offline_');
        
        // Store in cache for later processing
        $offlineActions = Cache::get('pwa_offline_actions_' . $user->id, []);
        $offlineActions[] = $action;
        
        // Keep only last 50 actions
        if (count($offlineActions) > 50) {
            $offlineActions = array_slice($offlineActions, -50);
        }
        
        Cache::put('pwa_offline_actions_' . $user->id, $offlineActions, 86400);
        
        return response()->json([
            'status' => 'success',
            'message' => 'Action stored for offline sync',
            'actionId' => $action['id']
        ]);
    }
    
    /**
     * Get offline actions for sync
     */
    public function getOfflineActions()
    {
        $user = auth()->user();
        $actions = Cache::get('pwa_offline_actions_' . $user->id, []);
        
        return response()->json([
            'status' => 'success',
            'data' => $actions
        ]);
    }
    
    /**
     * Clear offline actions after successful sync
     */
    public function clearOfflineActions()
    {
        $user = auth()->user();
        Cache::forget('pwa_offline_actions_' . $user->id);
        
        return response()->json([
            'status' => 'success',
            'message' => 'Offline actions cleared'
        ]);
    }
    
    /**
     * Update PWA cache
     */
    public function updateCache()
    {
        $user = auth()->user();
        $business = $user->business;
        
        if ($business) {
            // Clear old cache
            Cache::forget('pwa_offline_data_' . $business->id);
            
            // Generate new cache
            $this->getOfflineData($user);
            
            return response()->json([
                'status' => 'success',
                'message' => 'Cache updated successfully'
            ]);
        }
        
        return response()->json([
            'status' => 'error',
            'message' => 'No business found'
        ], 404);
    }
    
    /**
     * Check if PWA is installed
     */
    private function checkIfInstalled()
    {
        // This is a client-side check, but we can store the status
        return Cache::get('pwa_installed_' . auth()->id(), false);
    }
    
    /**
     * Process offline action
     */
    private function processOfflineAction($action)
    {
        $business = auth()->user()->business;
        
        switch ($action['type']) {
            case 'create_order':
                // Process offline order creation
                $this->processOfflineOrder($action['data']);
                break;
                
            case 'update_product':
                // Process offline product update
                $this->processOfflineProductUpdate($action['data']);
                break;
                
            case 'add_customer':
                // Process offline customer creation
                $this->processOfflineCustomer($action['data']);
                break;
                
            default:
                // Log unknown action type
                \Log::warning('Unknown offline action type: ' . $action['type']);
        }
    }
    
    /**
     * Process offline order
     */
    private function processOfflineOrder($data)
    {
        $business = auth()->user()->business;
        
        // Create order with offline flag
        $order = $business->orders()->create([
            'customer_id' => $data['customer_id'] ?? null,
            'total_amount' => $data['total_amount'],
            'status' => 'completed',
            'payment_method' => $data['payment_method'] ?? 'cash',
            'notes' => $data['notes'] ?? 'Order created offline',
            'created_at' => $data['timestamp'] ?? now(),
            'offline_created' => true
        ]);
        
        // Add order items
        if (isset($data['items']) && is_array($data['items'])) {
            foreach ($data['items'] as $item) {
                $order->items()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'total' => $item['total']
                ]);
                
                // Update product stock
                $product = $business->products()->find($item['product_id']);
                if ($product) {
                    $product->decrement('stock_quantity', $item['quantity']);
                }
            }
        }
    }
    
    /**
     * Process offline product update
     */
    private function processOfflineProductUpdate($data)
    {
        $business = auth()->user()->business;
        $product = $business->products()->find($data['product_id']);
        
        if ($product) {
            $product->update([
                'stock_quantity' => $data['stock_quantity'],
                'price' => $data['price'] ?? $product->price,
                'updated_at' => $data['timestamp'] ?? now()
            ]);
        }
    }
    
    /**
     * Process offline customer
     */
    private function processOfflineCustomer($data)
    {
        $business = auth()->user()->business;
        
        $business->customers()->create([
            'name' => $data['name'],
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
            'address' => $data['address'] ?? null,
            'created_at' => $data['timestamp'] ?? now(),
            'offline_created' => true
        ]);
    }
}
