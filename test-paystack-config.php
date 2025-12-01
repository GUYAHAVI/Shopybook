<?php
// Test Paystack API configuration
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Testing Paystack Configuration...\n\n";

$secretKey = config('services.paystack.secret_key');
$publicKey = config('services.paystack.public_key');

echo "Paystack Secret Key: " . ($secretKey ? (substr($secretKey, 0, 10) . '...') : 'NOT SET') . "\n";
echo "Paystack Public Key: " . ($publicKey ? (substr($publicKey, 0, 10) . '...') : 'NOT SET') . "\n\n";

if (!$secretKey) {
    echo "ERROR: Paystack secret key is not configured!\n";
    echo "Please set PAYSTACK_SECRET_KEY in your .env file.\n";
    exit(1);
}

// Test API connection
echo "Testing Paystack API connection...\n";

try {
    $response = \Illuminate\Support\Facades\Http::withHeaders([
        'Authorization' => 'Bearer ' . $secretKey,
        'Content-Type' => 'application/json'
    ])->get('https://api.paystack.co/bank');
    
    if ($response->successful()) {
        echo "✓ Paystack API connection successful!\n";
        echo "Status: " . $response->status() . "\n";
    } else {
        echo "✗ Paystack API connection failed!\n";
        echo "Status: " . $response->status() . "\n";
        echo "Response: " . $response->body() . "\n";
    }
} catch (\Exception $e) {
    echo "✗ Exception: " . $e->getMessage() . "\n";
}

echo "\n";

// Test M-Pesa charge endpoint
echo "Testing M-Pesa charge endpoint with test data...\n";

$testData = [
    'email' => 'test@example.com',
    'amount' => 1000, // 10 KES in cents
    'currency' => 'KES',
    'reference' => 'TEST-' . time(),
    'mobile_money' => [
        'phone' => '254717745891',
        'provider' => 'mpesa'
    ]
];

echo "Request data: " . json_encode($testData, JSON_PRETTY_PRINT) . "\n\n";

try {
    $response = \Illuminate\Support\Facades\Http::withHeaders([
        'Authorization' => 'Bearer ' . $secretKey,
        'Content-Type' => 'application/json'
    ])->post('https://api.paystack.co/charge', $testData);
    
    echo "Status: " . $response->status() . "\n";
    echo "Response: " . json_encode($response->json(), JSON_PRETTY_PRINT) . "\n";
    
    if (!$response->successful()) {
        echo "\n✗ M-Pesa charge request failed!\n";
        echo "This might indicate:\n";
        echo "1. M-Pesa is not enabled for your Paystack account\n";
        echo "2. Your account is not verified\n";
        echo "3. Invalid phone number format\n";
        echo "4. Currency (KES) not supported\n";
    }
} catch (\Exception $e) {
    echo "✗ Exception: " . $e->getMessage() . "\n";
}
