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
            $validated = $request->validate([
                'business_id' => 'required|exists:businesses,id',
                'product_id' => 'required|exists:products,id',
                'quantity' => 'required|integer|min:1',
                'customer_name' => 'required|string|max:255',
                'customer_phone' => 'required|string|max:20',
                'customer_email' => 'nullable|email|max:255',
                'delivery_address' => 'required|string|max:500',
            ]);

            // Get the product to calculate total price
            $product = Product::findOrFail($validated['product_id']);
            $totalPrice = $product->price * $validated['quantity'];

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
                'payment_status' => 'pending',
            ]);

            // Send notifications after successful order creation
            try {
                $notificationService = new NotificationService();
                $notificationService->notifyNewOrder($order);
            } catch (\Exception $e) {
                // Log error but don't fail the order creation
                Log::error('Failed to send order notifications: ' . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => 'Order placed successfully',
                'order_id' => $order->id
            ]);

        } catch (\Exception $e) {
            Log::error('Error creating public order: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error placing order. Please try again.'
            ], 500);
        }
    }
} 