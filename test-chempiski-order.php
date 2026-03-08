<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Business;
use App\Models\Product;
use App\Models\Order;

echo "=== Testing Chempiski Order Creation ===\n\n";

// 1. Find the business
$business = Business::where('slug', 'chempiski')->first();
if (!$business) {
    die("ERROR: Business 'chempiski' not found!\n");
}
echo "✓ Business found: {$business->name} (ID: {$business->id})\n\n";

// 2. Check products for this business
$products = Product::where('business_id', $business->id)->get();
echo "Products for this business: " . $products->count() . "\n";
if ($products->count() > 0) {
    foreach ($products as $product) {
        echo "  - Product ID: {$product->id}, Name: {$product->name}, Price: KSh {$product->price}, Stock: {$product->stock_quantity}\n";
    }
} else {
    echo "  WARNING: No products found for this business!\n";
}
echo "\n";

// 3. Try to find product with ID = 1
$product = Product::where('id', 1)->where('business_id', $business->id)->first();
if ($product) {
    echo "✓ Product with ID=1 found: {$product->name}\n";
} else {
    echo "✗ Product with ID=1 NOT found for this business\n";
    echo "  This is likely why orders are failing!\n";
    
    // Check if product ID=1 exists at all
    $anyProduct1 = Product::find(1);
    if ($anyProduct1) {
        echo "  Product ID=1 exists but belongs to business: {$anyProduct1->business_id}\n";
    } else {
        echo "  Product ID=1 doesn't exist in the database at all\n";
    }
}
echo "\n";

// 4. Check if orders table exists and show structure
try {
    $orderCount = Order::count();
    echo "✓ Orders table exists with {$orderCount} orders\n";
} catch (\Exception $e) {
    echo "✗ Orders table error: {$e->getMessage()}\n";
}

echo "\n=== End of Test ===\n";
