<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Business;
use App\Models\Product;
use App\Models\ProductConversion;

echo "=== Testing Dynamic Conversion System ===\n\n";

// Test 1: Check all businesses
echo "1. Checking all businesses for eligibility...\n";
$businesses = Business::all();

if ($businesses->count() > 0) {
    echo "   Found {$businesses->count()} businesses:\n\n";
    foreach ($businesses as $business) {
        echo "   Business: {$business->name}\n";
        echo "   Email: {$business->email}\n";
        echo "   Phone: {$business->phone}\n";
        echo "   Eligible for dynamic conversions: " . ($business->isEligibleForDynamicConversions() ? 'YES' : 'NO') . "\n";
        
        if ($business->isEligibleForDynamicConversions()) {
            echo "   ✓ This business is eligible!\n";
            
            // Check products with conversions
            $productsWithConversions = Product::whereHas('conversions')->where('business_id', $business->id)->get();
            if ($productsWithConversions->count() > 0) {
                echo "   ✓ Found {$productsWithConversions->count()} products with conversion rules:\n";
                foreach ($productsWithConversions as $product) {
                    echo "     - {$product->name} (ID: {$product->id})\n";
                    $conversions = $product->conversions;
                    foreach ($conversions as $conversion) {
                        echo "       * {$conversion->purchase_unit} → {$conversion->sale_unit} (Factor: {$conversion->conversion_factor})\n";
                    }
                }
            } else {
                echo "   ✗ No products with conversion rules found\n";
            }
        }
        echo "\n";
    }
} else {
    echo "   ✗ No businesses found\n";
}

echo "=== Test Complete ===\n";
