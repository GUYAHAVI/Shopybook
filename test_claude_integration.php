<?php

/**
 * Test Claude API Integration
 * 
 * Run this script to verify Claude API is working correctly:
 * php test_claude_integration.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Services\ClaudeAPIService;
use Illuminate\Support\Facades\Log;

echo "=== Testing Claude API Integration ===\n\n";

// Initialize Claude service
$claudeService = new ClaudeAPIService();

// Test 1: Simple Quick Insight
echo "Test 1: Generating quick insight...\n";
try {
    $insight = $claudeService->generateQuickInsight(
        'Average Order Value',
        'KSh 2,500',
        ['industry_average' => 'KSh 1,800', 'business_type' => 'retail']
    );
    echo "✅ Quick Insight Generated:\n";
    echo $insight . "\n\n";
} catch (Exception $e) {
    echo "❌ Quick Insight Failed: " . $e->getMessage() . "\n\n";
}

// Test 2: Sample Business Analysis
echo "Test 2: Generating sample business analysis...\n";
try {
    $sampleBusinessData = [
        'business_name' => 'Test Store',
        'business_type' => 'retail',
        'analysis_type' => 'sales',
        'sales_data' => [
            'total_sales' => 125000,
            'total_orders' => 85,
            'recent_sales' => 45000,
            'average_order_value' => 1470.59,
            'top_selling_products' => [
                ['name' => 'Product A', 'quantity_sold' => 45],
                ['name' => 'Product B', 'quantity_sold' => 32],
                ['name' => 'Product C', 'quantity_sold' => 28],
            ]
        ],
        'products_data' => [
            'total_products' => 150,
            'low_stock_items' => 8,
            'out_of_stock' => 3,
            'average_price' => 1850.00,
            'inventory_value' => 275000
        ]
    ];
    
    $analysis = $claudeService->analyzeBusinessData($sampleBusinessData);
    echo "✅ Business Analysis Generated:\n";
    echo substr($analysis, 0, 500) . "...\n\n";
    echo "(Full analysis length: " . strlen($analysis) . " characters)\n\n";
} catch (Exception $e) {
    echo "❌ Business Analysis Failed: " . $e->getMessage() . "\n\n";
}

// Test 3: Check API Configuration
echo "Test 3: Checking configuration...\n";
$apiKey = config('services.claude.api_key', env('CLAUDE_API_KEY'));
if ($apiKey && strlen($apiKey) > 20) {
    echo "✅ Claude API Key is configured\n";
    echo "   Key starts with: " . substr($apiKey, 0, 15) . "...\n";
} else {
    echo "❌ Claude API Key not properly configured\n";
}

$model = config('services.claude.model', 'claude-sonnet-4-20250514');
echo "   Model: " . $model . "\n";

echo "\n=== Test Complete ===\n";
echo "\nIf all tests passed, your Claude integration is working correctly!\n";
echo "You can now use the Business Analysis feature in the dashboard.\n";
