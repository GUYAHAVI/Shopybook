<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Test Paystack M-Pesa API call
$secretKey = env('PAYSTACK_SECRET_KEY');
$phoneNumber = '+254712345678'; // Test phone
$amount = 500; // KES
$reference = 'TEST-' . time();

echo "Testing Paystack M-Pesa API...\n";
echo "Secret Key: " . substr($secretKey, 0, 10) . "...\n";
echo "Phone: $phoneNumber\n";
echo "Amount: KES $amount\n";
echo "Reference: $reference\n\n";

$requestData = [
    'email' => 'test@example.com',
    'amount' => $amount * 100, // Convert to cents
    'currency' => 'KES',
    'reference' => $reference,
    'mobile_money' => [
        'phone' => $phoneNumber,
        'provider' => 'mpesa'
    ],
    'metadata' => [
        'test' => true
    ]
];

echo "Request Data:\n";
print_r($requestData);
echo "\n";

$ch = curl_init('https://api.paystack.co/charge');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestData));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $secretKey,
    'Content-Type: application/json'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: $httpCode\n";
echo "Response:\n";
print_r(json_decode($response, true));
