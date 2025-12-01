<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Notification;
use App\Models\Business;

class CreateSampleNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notification:sample {business_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create sample notifications for testing';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $businessId = $this->argument('business_id');
        
        $business = Business::find($businessId);
        if (!$business) {
            $this->error('Business not found!');
            return;
        }

        // Create sample service booking notification
        Notification::create([
            'business_id' => $businessId,
            'type' => 'service_booking',
            'title' => 'New Service Booking',
            'message' => 'New service booking from John Doe for 2 service(s). Total: KSh 2,500.00',
            'data' => [
                'service_booking_id' => 1,
                'customer_name' => 'John Doe',
                'amount' => 2500.00,
                'services_count' => 2,
                'payment_status' => 'paid',
            ],
            'icon' => 'fas fa-calendar-check',
            'color' => 'success'
        ]);

        // Create sample order notification
        Notification::create([
            'business_id' => $businessId,
            'type' => 'order',
            'title' => 'New Order Received',
            'message' => 'New online order from Jane Smith. Total: KSh 1,200.00',
            'data' => [
                'order_id' => 1,
                'customer_name' => 'Jane Smith',
                'amount' => 1200.00,
                'status' => 'pending',
                'order_type' => 'public_order',
            ],
            'icon' => 'fas fa-shopping-cart',
            'color' => 'info'
        ]);

        $this->info('Sample notifications created successfully for business: ' . $business->business_name);
    }
}