<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Test scenarios
$testScenarios = [
    [
        'name' => 'Social Media Modal - Exact Settings',
        'data' => [
            'content' => 'Check out our new product!',
            'content_type' => 'social-media',
            'tone' => 'engaging'
        ]
    ],
    [
        'name' => 'Social Media Post - Casual',
        'data' => [
            'content' => 'Check out our new product!',
            'content_type' => 'social_media_post',
            'tone' => 'casual'
        ]
    ],
    [
        'name' => 'Product Description - Professional',
        'data' => [
            'content' => 'This is a test product',
            'content_type' => 'product_description',
            'tone' => 'professional'
        ]
    ]
];

echo "Testing AI Content Enhancement API - Multiple Scenarios\n";
echo "======================================================\n\n";

foreach ($testScenarios as $scenario) {
    echo "Scenario: " . $scenario['name'] . "\n";
    echo "Test Data: " . json_encode($scenario['data']) . "\n";
    
    try {
        // Create a request
        $request = new \Illuminate\Http\Request();
        $request->merge($scenario['data']);
        
        // Get the controller
        $controller = app('App\Http\Controllers\AIContentController');
        
        // Call the enhance method
        $response = $controller->enhance($request);
        
        // Get the response content
        $content = $response->getContent();
        $data = json_decode($content, true);
        
        echo "Response Status: " . $response->getStatusCode() . "\n";
        
        if (isset($data['improvement_percentage'])) {
            echo "Improvement Percentage: " . $data['improvement_percentage'] . "\n";
            echo "Type: " . gettype($data['improvement_percentage']) . "\n";
        } else {
            echo "ERROR: improvement_percentage not found in response!\n";
            echo "Available keys: " . implode(', ', array_keys($data)) . "\n";
        }
        
        echo "Success: " . ($data['success'] ? 'Yes' : 'No') . "\n";
        echo "Word Count: " . ($data['word_count'] ?? 'N/A') . "\n";
        echo "---\n\n";
        
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
        echo "---\n\n";
    }
}
