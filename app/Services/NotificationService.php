<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\ServiceBooking;
use App\Models\Order;
use App\Models\Product;
use App\Mail\NewServiceBookingMail;
use App\Mail\NewOrderMail;
use App\Mail\LowStockAlertMail;
use App\Mail\TestimonialReceivedMail;
use App\Models\Testimonial;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class NotificationService
{
    /**
     * Send notifications for a new service booking
     */
    public function notifyNewServiceBooking(ServiceBooking $serviceBooking)
    {
        try {
            $business = $serviceBooking->business;
            $customer = $serviceBooking->customer;
            
            // Create dashboard notification
            $notification = Notification::create([
                'business_id' => $business->id,
                'type' => 'service_booking',
                'title' => 'New Service Booking',
                'message' => $this->getServiceBookingMessage($serviceBooking, $customer),
                'data' => [
                    'service_booking_id' => $serviceBooking->id,
                    'customer_name' => $customer ? $customer->name : 'Walk-in Customer',
                    'amount' => $serviceBooking->final_amount,
                    'services_count' => $serviceBooking->serviceItems()->count(),
                    'payment_status' => $serviceBooking->payment_status,
                ],
                'icon' => 'fas fa-calendar-check',
                'color' => 'success'
            ]);

            // Send email notification to business owner
            if ($business->user && $business->user->email) {
                Mail::to($business->user->email)->send(new NewServiceBookingMail($serviceBooking));
                Log::info('Service booking email sent', [
                    'business_id' => $business->id,
                    'booking_id' => $serviceBooking->id,
                    'email' => $business->user->email
                ]);
            }

            return $notification;

        } catch (\Exception $e) {
            Log::error('Failed to send service booking notifications', [
                'booking_id' => $serviceBooking->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Send notifications for a new order
     */
    public function notifyNewOrder(Order $order)
    {
        try {
            $business = $order->business;
            
            // Create dashboard notification
            $notification = Notification::create([
                'business_id' => $business->id,
                'type' => 'order',
                'title' => 'New Order Received',
                'message' => $this->getOrderMessage($order),
                'data' => [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number ?? null,
                    'customer_name' => $this->getOrderCustomerName($order),
                    'amount' => $this->getOrderTotalAmount($order),
                    'status' => $order->status,
                    'order_type' => $order->order_type ?? 'regular',
                ],
                'icon' => 'fas fa-shopping-cart',
                'color' => 'info'
            ]);

            // Send email notification to business owner
            if ($business->user && $business->user->email) {
                Mail::to($business->user->email)->send(new NewOrderMail($order));
                Log::info('Order email sent', [
                    'business_id' => $business->id,
                    'order_id' => $order->id,
                    'email' => $business->user->email
                ]);
            }

            return $notification;

        } catch (\Exception $e) {
            Log::error('Failed to send order notifications', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Get unread notifications count for a business
     */
    public function getUnreadCount($businessId)
    {
        return Notification::forBusiness($businessId)->unread()->count();
    }

    /**
     * Get recent notifications for a business
     */
    public function getRecentNotifications($businessId, $limit = 10)
    {
        return Notification::forBusiness($businessId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Mark notification as read
     */
    public function markAsRead($notificationId, $businessId)
    {
        $notification = Notification::forBusiness($businessId)->findOrFail($notificationId);
        $notification->markAsRead();
        return $notification;
    }

    /**
     * Mark all notifications as read for a business
     */
    public function markAllAsRead($businessId)
    {
        return Notification::forBusiness($businessId)
            ->unread()
            ->update([
                'read' => true,
                'read_at' => now()
            ]);
    }

    /**
     * Generate service booking notification message
     */
    private function getServiceBookingMessage(ServiceBooking $serviceBooking, $customer = null)
    {
        $customerName = $customer ? $customer->name : 'Walk-in Customer';
        $servicesCount = $serviceBooking->serviceItems()->count();
        $amount = number_format((float)$serviceBooking->final_amount, 2);
        
        return "New service booking from {$customerName} for {$servicesCount} service(s). Total: KSh {$amount}";
    }

    /**
     * Generate order notification message
     */
    private function getOrderMessage(Order $order)
    {
        $customerName = $this->getOrderCustomerName($order);
        $amount = number_format($this->getOrderTotalAmount($order), 2);
        $orderType = $order->order_type === 'public_order' ? 'online' : 'in-store';
        
        return "New {$orderType} order from {$customerName}. Total: KSh {$amount}";
    }

    /**
     * Get customer name from order
     */
    private function getOrderCustomerName(Order $order)
    {
        if ($order->order_type === 'public_order') {
            return $order->customer_name ?? 'Unknown Customer';
        }
        
        return $order->customer ? $order->customer->name : 'Walk-in Customer';
    }

    /**
     * Get total amount from order
     */
    private function getOrderTotalAmount(Order $order)
    {
        if ($order->order_type === 'public_order') {
            return $order->total_price ?? 0;
        }
        
        return $order->total_amount ?? 0;
    }

    /**
     * Send low stock alert notifications
     */
    public function notifyLowStock(Product $product)
    {
        try {
            $business = $product->business;
            
            // Check if we've already sent a notification for this product recently (within 24 hours)
            $cacheKey = "low_stock_notified_{$business->id}_{$product->id}";
            if (Cache::has($cacheKey)) {
                Log::info('Low stock notification skipped (already sent recently)', [
                    'product_id' => $product->id,
                    'product_name' => $product->name
                ]);
                return null;
            }

            $isOutOfStock = $product->stock_quantity <= 0;
            $status = $isOutOfStock ? 'Out of Stock' : 'Low Stock';
            $color = $isOutOfStock ? 'danger' : 'warning';
            $icon = $isOutOfStock ? 'fas fa-exclamation-circle' : 'fas fa-exclamation-triangle';
            
            // Create dashboard notification
            $notification = Notification::create([
                'business_id' => $business->id,
                'type' => 'low_stock',
                'title' => "{$status} Alert",
                'message' => $this->getLowStockMessage($product, $isOutOfStock),
                'data' => [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'sku' => $product->sku,
                    'current_stock' => $product->stock_quantity,
                    'low_stock_threshold' => $product->low_stock_threshold ?? 10,
                    'category' => $product->category,
                    'is_out_of_stock' => $isOutOfStock,
                ],
                'icon' => $icon,
                'color' => $color
            ]);

            // Send email notification to business owner or business email
            $emailAddress = $business->email ?? ($business->user ? $business->user->email : null);
            
            if ($emailAddress) {
                Mail::to($emailAddress)->send(new LowStockAlertMail($product));
                Log::info('Low stock email sent', [
                    'business_id' => $business->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'stock_level' => $product->stock_quantity,
                    'email' => $emailAddress
                ]);
            }

            // Cache that we've sent this notification (expires in 24 hours)
            Cache::put($cacheKey, true, now()->addHours(24));

            return $notification;

        } catch (\Exception $e) {
            Log::error('Failed to send low stock notifications', [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            // Don't throw exception - we don't want to break the order/update process
            return null;
        }
    }

    /**
     * Check and notify for low stock products
     * This can be called after orders or stock updates
     */
    public function checkAndNotifyLowStock(Product $product)
    {
        try {
            // Only send notification if stock is low or out of stock
            if ($product->stock_status === 'low_stock' || $product->stock_status === 'out_of_stock') {
                return $this->notifyLowStock($product);
            }
            
            return null;
        } catch (\Exception $e) {
            Log::error('checkAndNotifyLowStock failed', [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'stock_status' => $product->stock_status,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            // Re-throw in debug mode so we can see the issue
            if (config('app.debug')) {
                throw $e;
            }
            return null;
        }
    }

    // ── Testimonial ───────────────────────────────────────────────────────

    /**
     * Notify the business owner about a new testimonial (in-app + email).
     */
    public function notifyNewTestimonial(Testimonial $testimonial, $business): void
    {
        try {
            $stars = str_repeat('★', $testimonial->rating) . str_repeat('☆', 5 - $testimonial->rating);

            Notification::create([
                'business_id' => $business->id,
                'type'        => 'testimonial',
                'title'       => 'New Customer Review',
                'message'     => "{$testimonial->name} left a {$testimonial->rating}-star review: \"{$testimonial->quote}\"",
                'data'        => [
                    'testimonial_id' => $testimonial->id,
                    'reviewer_name'  => $testimonial->name,
                    'rating'         => $testimonial->rating,
                    'stars'          => $stars,
                    'manage_url'     => route('testimonials.owner.index'),
                ],
                'icon'  => 'fas fa-star',
                'color' => 'warning',
            ]);

            if ($business->user && $business->user->email) {
                Mail::to($business->user->email)
                    ->send(new TestimonialReceivedMail($testimonial, $business));
            }
        } catch (\Exception $e) {
            Log::error('Failed to send testimonial notification', [
                'testimonial_id' => $testimonial->id,
                'error'          => $e->getMessage(),
            ]);
        }
    }

    /**
     * Generate low stock notification message
     */
    private function getLowStockMessage(Product $product, $isOutOfStock = false)
    {
        if ($isOutOfStock) {
            return "{$product->name} is OUT OF STOCK! Please reorder immediately to avoid lost sales.";
        }
        
        $threshold = $product->low_stock_threshold ?? 10;
        return "{$product->name} is running low ({$product->stock_quantity} units remaining, threshold: {$threshold}). Consider reordering soon.";
    }
}

