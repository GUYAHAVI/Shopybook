<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Product;
use App\Models\Business;
use Illuminate\Support\Facades\DB;

echo "=== Product Database Test ===\n\n";

try {
    // Check total products
    $totalProducts = Product::count();
    echo "📊 Total products in database: {$totalProducts}\n";
    
    if ($totalProducts == 0) {
        echo "❌ No products found in database\n";
        echo "💡 You need to add products first. Run the seeder:\n";
        echo "   php artisan db:seed --class=SampleDataSeeder\n";
        exit;
    }
    
    // Check products by status
    $activeProducts = Product::where('is_active', true)->count();
    $inactiveProducts = Product::where('is_active', false)->count();
    $inStockProducts = Product::where('stock_quantity', '>', 0)->count();
    $outOfStockProducts = Product::where('stock_quantity', '<=', 0)->count();
    
    echo "📊 Active products: {$activeProducts}\n";
    echo "📊 Inactive products: {$inactiveProducts}\n";
    echo "📊 In stock products: {$inStockProducts}\n";
    echo "📊 Out of stock products: {$outOfStockProducts}\n";
    
    // Check products with business_id
    $productsWithBusiness = Product::whereNotNull('business_id')->count();
    $productsWithoutBusiness = Product::whereNull('business_id')->count();
    
    echo "\n📊 Products with business_id: {$productsWithBusiness}\n";
    echo "📊 Products without business_id: {$productsWithoutBusiness}\n";
    
    // Show sample products
    $sampleProducts = Product::with('business')->limit(5)->get();
    echo "\n📋 Sample Products:\n";
    foreach ($sampleProducts as $product) {
        $businessName = $product->business ? $product->business->name : 'No Business';
        echo "  - {$product->name} (Business: {$businessName}, Stock: {$product->stock_quantity}, Active: " . ($product->is_active ? 'Yes' : 'No') . ")\n";
    }
    
    // Check businesses
    $businesses = Business::withCount('products')->get();
    echo "\n📋 Businesses and their products:\n";
    foreach ($businesses as $business) {
        echo "  - {$business->name}: {$business->products_count} products\n";
    }
    
    echo "\n=== End Test ===\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
