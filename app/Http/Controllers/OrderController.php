<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Business;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\NotificationService;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        try {
            // Log the incoming request for debugging
            Log::info('Order creation request received', [
                'data' => $request->all()
            ]);

            // First validate
            try {
                $validated = $request->validate([
                    'business_id' => 'required|exists:businesses,id',
                    'product_id' => 'required',
                    'quantity' => 'required|integer|min:1',
                    'customer_name' => 'required|string|max:255',
                    'customer_phone' => 'required|string|max:20',
                    'customer_email' => 'nullable|email|max:255',
                    'delivery_address' => 'required|string|max:500',
                ]);
                
                Log::info('Validation passed', ['validated' => $validated]);
            } catch (\Illuminate\Validation\ValidationException $e) {
                Log::error('Validation failed immediately', [
                    'errors' => $e->errors(),
                    'request_data' => $request->all()
                ]);
                throw $e;
            }

            // Get the product to calculate total price
            $product = Product::where('id', $validated['product_id'])
                ->where('business_id', $validated['business_id'])
                ->first();
            
            if (!$product) {
                Log::error('Product not found', [
                    'product_id' => $validated['product_id'],
                    'business_id' => $validated['business_id']
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Product not found for this business.'
                ], 404);
            }
            
            Log::info('Product found', [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'price' => $product->price,
                'stock' => $product->stock_quantity
            ]);
            
            // Check if product is available (using correct column name)
            if (isset($product->stock_quantity) && $product->stock_quantity < $validated['quantity']) {
                Log::warning('Insufficient stock', [
                    'available' => $product->stock_quantity,
                    'requested' => $validated['quantity']
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Insufficient stock. Only ' . $product->stock_quantity . ' items available.'
                ], 400);
            }

            $totalPrice = $product->price * $validated['quantity'];
            
            Log::info('Creating order', [
                'total_price' => $totalPrice,
                'quantity' => $validated['quantity']
            ]);

            // Create the order
            $order = Order::create([
                'business_id' => $validated['business_id'],
                'product_id' => $validated['product_id'],
                'customer_name' => $validated['customer_name'],
                'customer_phone' => $validated['customer_phone'],
                'customer_email' => $validated['customer_email'],
                'delivery_address' => $validated['delivery_address'],
                'quantity' => $validated['quantity'],
                'unit_price' => $product->price,
                'total_price' => $totalPrice,
                'total_amount' => $totalPrice,
                'status' => 'pending',
                'order_type' => 'public_order',
                'payment_status' => 'unpaid',  // Changed from 'pending' to 'unpaid'
            ]);

            Log::info('Order created successfully', ['order_id' => $order->id]);

            // Send notifications after successful order creation
            try {
                $notificationService = new NotificationService();
                $notificationService->notifyNewOrder($order);
            } catch (\Exception $e) {
                // Log error but don't fail the order creation
                Log::error('Failed to send order notifications', [
                    'order_id' => $order->id,
                    'error' => $e->getMessage(),
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Order placed successfully',
                'order_id' => $order->id
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Order validation failed', [
                'errors' => $e->errors(),
                'request' => $request->all()
            ]);
            
            $errorMessages = [];
            foreach ($e->errors() as $field => $messages) {
                $errorMessages[] = $field . ': ' . implode(', ', $messages);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Validation failed: ' . implode(' | ', $errorMessages)
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error creating public order: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error placing order: ' . $e->getMessage()
            ], 500);
        }
    }
} 