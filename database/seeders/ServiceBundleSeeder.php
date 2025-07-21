<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ServiceBundleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // This seeder sets up the shave/aftershave bundling example
        // Run this after creating your services manually
        
        // Find or create services (example for first business)
        $business = \App\Models\Business::first();
        
        if (!$business) {
            $this->command->info('No business found. Create a business first.');
            return;
        }

        // Create or update Shave service
        $shaveService = \App\Models\Service::updateOrCreate(
            ['business_id' => $business->id, 'name' => 'Shave'],
            [
                'price' => 150.00,
                'duration' => 20,
                'commission_rate' => 15.0,
                'description' => 'Professional beard and mustache shaving',
                'is_bundle_trigger' => true,
            ]
        );

        // Create or update Aftershave service
        $aftershaveService = \App\Models\Service::updateOrCreate(
            ['business_id' => $business->id, 'name' => 'Aftershave'],
            [
                'price' => 0.00, // Free service
                'duration' => 5,
                'commission_rate' => 10.0, // Commission calculated from shave service cost
                'description' => 'Complimentary aftershave application',
                'is_complimentary' => true,
                'parent_service_id' => $shaveService->id,
            ]
        );

        // Set up the bundling relationship
        $shaveService->update([
            'bundled_services' => [$aftershaveService->id]
        ]);

        $this->command->info('Service bundling set up successfully!');
        $this->command->info("Shave service (ID: {$shaveService->id}) will automatically include Aftershave (ID: {$aftershaveService->id})");
        $this->command->info("Aftershave is complimentary and commission is calculated from shave cost");
    }
}
