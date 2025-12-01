<?php
// Test different phone number formats with Paystack
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$secretKey = config('services.paystack.secret_key');

$testFormats = [
    '+254717745891',
    '254717745891',
    '0717745891',
    '717745891',
];

echo "Testing different phone number formats with Paystack M-Pesa...\n\n";

foreach ($testFormats as $phone) {
    echo "Testing phone: $phone\n";
    
    $testData = [
        'email' => 'test@example.com',
        'amount' => 1000,
        'currency' => 'KES',
        'reference' => 'TEST-' . time() . '-' . rand(1000, 9999),
        'mobile_money' => [
            'phone' => $phone,
            'provider' => 'mpesa'
        ]
    ];
    
    try {
        $response = \Illuminate\Support\Facades\Http::withHeaders([
            'Authorization' => 'Bearer ' . $secretKey,
            'Content-Type' => 'application/json'
        ])->post('https://api.paystack.co/charge', $testData);
        
        echo "Status: " . $response->status() . "\n";
        $data = $response->json();
        echo "Success: " . ($data['status'] ? 'YES' : 'NO') . "\n";
        echo "Message: " . ($data['message'] ?? 'N/A') . "\n";
        
        if ($response->successful() && isset($data['data'])) {
            echo "✓ THIS FORMAT WORKS!\n";
            echo "Charge Status: " . ($data['data']['status'] ?? 'N/A') . "\n";
        }
        
    } catch (\Exception $e) {
        echo "Exception: " . $e->getMessage() . "\n";
    }
    
    echo "\n" . str_repeat('-', 50) . "\n\n";
    
    sleep(1); // Avoid rate limiting
}
