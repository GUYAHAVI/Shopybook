<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Product;
use App\Models\Business;
use App\Models\User;
use Illuminate\Support\Facades\DB;

echo "=== POS Debug Information ===\n\n";

try {
    // Check if user is authenticated
    $user = auth()->user();
    if (!$user) {
        echo "❌ No authenticated user found\n";
        exit;
    }
    echo "✅ User authenticated: {$user->name} (ID: {$user->id})\n";
    
    // Check if user has a business
    $business = $user->business;
    if (!$business) {
        echo "❌ User has no business\n";
        exit;
    }
    echo "✅ Business found: {$business->name} (ID: {$business->id})\n";
    
    // Check total products in database
    $totalProducts = Product::count();
    echo "📊 Total products in database: {$totalProducts}\n";
    
    // Check products for this business
    $businessProducts = $business->products()->count();
    echo "📊 Products for this business: {$businessProducts}\n";
    
    // Check active products
    $activeProducts = $business->products()->active()->count();
    echo "📊 Active products: {$activeProducts}\n";
    
    // Check in-stock products
    $inStockProducts = $business->products()->active()->inStock()->count();
    echo "📊 In-stock products: {$inStockProducts}\n";
    
    // Show sample products
    $sampleProducts = $business->products()->active()->inStock()->limit(5)->get();
    echo "\n📋 Sample Products:\n";
    foreach ($sampleProducts as $product) {
        echo "  - {$product->name} (Stock: {$product->stock_quantity}, Price: KSh {$product->price})\n";
    }
    
    // Check if products have correct business_id
    $orphanedProducts = Product::whereNull('business_id')->count();
    echo "\n⚠️  Products without business_id: {$orphanedProducts}\n";
    
    // Check products with wrong business_id
    $wrongBusinessProducts = Product::where('business_id', '!=', $business->id)->count();
    echo "⚠️  Products with wrong business_id: {$wrongBusinessProducts}\n";
    
    echo "\n=== End Debug ===\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
