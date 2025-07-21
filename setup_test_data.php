<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Get the first business
$business = \App\Models\Business::first();

if (!$business) {
    echo "No business found. Please create a business first.\n";
    exit;
}

echo "Setting up test data for business: " . $business->name . "\n";

// Create or update services with bundling
$shaveService = \App\Models\Service::firstOrCreate(
    ['business_id' => $business->id, 'name' => 'Shave'],
    [
        'price' => 150.00,
        'duration' => 30,
        'commission_rate' => 15.00,
        'description' => 'Professional shave service',
        'is_bundle_trigger' => true,
        'bundled_services' => null, // Will be set after aftershave is created
        'is_complimentary' => false,
        'parent_service_id' => null
    ]
);

$aftershaveService = \App\Models\Service::firstOrCreate(
    ['business_id' => $business->id, 'name' => 'Aftershave'],
    [
        'price' => 0.00, // Free service
        'duration' => 5,
        'commission_rate' => 5.00, // 5% commission from parent service
        'description' => 'Aftershave treatment (complimentary with shave)',
        'is_bundle_trigger' => false,
        'bundled_services' => null,
        'is_complimentary' => true,
        'parent_service_id' => $shaveService->id
    ]
);

// Update shave service to include aftershave in bundled services
$shaveService->update([
    'bundled_services' => [$aftershaveService->id]
]);

// Create additional services
$haircutService = \App\Models\Service::firstOrCreate(
    ['business_id' => $business->id, 'name' => 'Haircut'],
    [
        'price' => 200.00,
        'duration' => 45,
        'commission_rate' => 20.00,
        'description' => 'Professional haircut',
        'is_bundle_trigger' => false,
        'bundled_services' => null,
        'is_complimentary' => false,
        'parent_service_id' => null
    ]
);

$massageService = \App\Models\Service::firstOrCreate(
    ['business_id' => $business->id, 'name' => 'Head Massage'],
    [
        'price' => 100.00,
        'duration' => 20,
        'commission_rate' => 10.00,
        'description' => 'Relaxing head massage',
        'is_bundle_trigger' => false,
        'bundled_services' => null,
        'is_complimentary' => false,
        'parent_service_id' => null
    ]
);

// Create test staff if none exist
$staff1 = \App\Models\Staff::firstOrCreate(
    ['business_id' => $business->id, 'name' => 'John Barber'],
    [
        'role' => 'Senior Barber',
        'commission_rate' => 20.00,
        'contact' => '+254700123456'
    ]
);

$staff2 = \App\Models\Staff::firstOrCreate(
    ['business_id' => $business->id, 'name' => 'Mary Stylist'],
    [
        'role' => 'Hair Stylist',
        'commission_rate' => 18.00,
        'contact' => '+254700654321'
    ]
);

echo "Test data setup complete!\n";
echo "Services created:\n";
echo "- Shave (KSh 150, 15% commission) - Bundle trigger for Aftershave\n";
echo "- Aftershave (Free, 5% commission from parent) - Complimentary with Shave\n";
echo "- Haircut (KSh 200, 20% commission)\n";
echo "- Head Massage (KSh 100, 10% commission)\n";
echo "\nStaff created:\n";
echo "- John Barber (Senior Barber)\n";
echo "- Mary Stylist (Hair Stylist)\n";
echo "\nNow you can test the service booking with automatic bundling!\n";
