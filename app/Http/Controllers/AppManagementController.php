<?php

namespace App\Http\Controllers;

use App\Models\BusinessApp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AppManagementController extends Controller
{
    /**
     * Show the app store where users can enable/disable apps
     */
    public function index()
    {
        $business = auth()->user()->business;
        
        if (!$business) {
            return redirect()->route('business.choose-type')
                ->with('error', 'Please create a business first');
        }

        // Get all available apps
        $availableApps = BusinessApp::availableApps();
        
        // Get currently enabled apps for this business
        $enabledApps = BusinessApp::where('business_id', $business->id)
            ->pluck('is_active', 'app_slug')
            ->toArray();

        // Group apps by category while preserving slug keys
        $groupedApps = [];
        foreach ($availableApps as $slug => $app) {
            $category = $app['category'];
            if (!isset($groupedApps[$category])) {
                $groupedApps[$category] = [];
            }
            $groupedApps[$category][$slug] = $app;
        }

        return view('apps.index', compact('availableApps', 'enabledApps', 'groupedApps', 'business'));
    }

    /**
     * Toggle an app on/off for the current business
     */
    public function toggle(Request $request)
    {
        try {
            $validated = $request->validate([
                'app_slug' => 'required|string'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 400);
        }

        $business = auth()->user()->business;
        
        if (!$business) {
            return response()->json(['success' => false, 'message' => 'No business found'], 404);
        }

        $appSlug = $request->app_slug;
        
        // Check if app exists in available apps
        $availableApps = BusinessApp::availableApps();
        if (!isset($availableApps[$appSlug])) {
            return response()->json([
                'success' => false, 
                'message' => 'Invalid app: ' . $appSlug,
                'available_apps' => array_keys($availableApps)
            ], 400);
        }

        try {
            // Find or create the app record
            $businessApp = BusinessApp::firstOrCreate(
                [
                    'business_id' => $business->id,
                    'app_slug' => $appSlug
                ],
                [
                    'is_active' => true,
                    'order' => 0
                ]
            );

            // Toggle the active state
            $businessApp->is_active = !$businessApp->is_active;
            $businessApp->save();

            return response()->json([
                'success' => true,
                'is_active' => $businessApp->is_active,
                'message' => $businessApp->is_active ? 'App enabled successfully' : 'App disabled successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to toggle app: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update app order for custom arrangement
     */
    public function updateOrder(Request $request)
    {
        $request->validate([
            'apps' => 'required|array',
            'apps.*.app_slug' => 'required|string',
            'apps.*.order' => 'required|integer'
        ]);

        $business = auth()->user()->business;
        
        if (!$business) {
            return response()->json(['success' => false, 'message' => 'No business found'], 404);
        }

        try {
            DB::beginTransaction();

            foreach ($request->apps as $appData) {
                BusinessApp::where('business_id', $business->id)
                    ->where('app_slug', $appData['app_slug'])
                    ->update(['order' => $appData['order']]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'App order updated successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update order: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get enabled apps for the current business (for sidebar)
     */
    public function getEnabledApps()
    {
        $business = auth()->user()->business;
        
        if (!$business) {
            return response()->json(['apps' => []]);
        }

        $enabledApps = BusinessApp::where('business_id', $business->id)
            ->active()
            ->ordered()
            ->get()
            ->map(function ($app) {
                return array_merge(
                    ['slug' => $app->app_slug],
                    $app->getAppMetadata() ?? []
                );
            });

        return response()->json(['apps' => $enabledApps]);
    }

    /**
     * Initialize default apps for a business based on business type
     */
    public static function initializeDefaultApps($business)
    {
        $businessCategory = $business->business_category; // 'product', 'service', or 'hybrid'
        $availableApps = BusinessApp::availableApps();

        $defaultApps = [];

        foreach ($availableApps as $slug => $appData) {
            // Enable core apps and apps required for this business type
            if (
                $appData['category'] === 'core' ||
                in_array($businessCategory, $appData['required_for'] ?? [])
            ) {
                $defaultApps[] = [
                    'business_id' => $business->id,
                    'app_slug' => $slug,
                    'is_active' => true,
                    'order' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        if (!empty($defaultApps)) {
            BusinessApp::insert($defaultApps);
        }
    }
}
