<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Http;

echo "Testing Paystack Transaction Verification API...\n\n";

$secretKey = env('PAYSTACK_SECRET_KEY');
$testReference = 'SB-ENTERPRISE-' . time() . '-test'; // Use a test reference

echo "Secret Key: " . substr($secretKey, 0, 10) . "...\n";
echo "Test Reference: $testReference\n\n";

echo "Attempting to verify transaction...\n";

try {
    $response = Http::withHeaders([
        'Authorization' => 'Bearer ' . $secretKey,
    ])->get("https://api.paystack.co/transaction/verify/{$testReference}");

    echo "HTTP Status: " . $response->status() . "\n";
    echo "Response:\n";
    print_r($response->json());
    
    if ($response->successful()) {
        $data = $response->json();
        if (isset($data['data'])) {
            echo "\nTransaction Status: " . ($data['data']['status'] ?? 'unknown') . "\n";
        }
    }
    
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n\n--- Testing with a recent payment reference ---\n";
echo "Checking database for recent pending payments...\n\n";

$recentPayment = \Illuminate\Support\Facades\DB::table('subscription_payments')
    ->where('status', 'pending')
    ->orderBy('created_at', 'desc')
    ->first();

if ($recentPayment) {
    echo "Found pending payment:\n";
    echo "  Reference: {$recentPayment->checkout_request_id}\n";
    echo "  Phone: {$recentPayment->phone_number}\n";
    echo "  Amount: KES {$recentPayment->amount}\n";
    echo "  Created: {$recentPayment->created_at}\n\n";
    
    echo "Verifying with Paystack...\n";
    
    try {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $secretKey,
        ])->get("https://api.paystack.co/transaction/verify/{$recentPayment->checkout_request_id}");

        echo "HTTP Status: " . $response->status() . "\n";
        
        if ($response->successful()) {
            $data = $response->json();
            echo "Success: " . ($data['status'] ? 'true' : 'false') . "\n";
            echo "Message: " . ($data['message'] ?? 'N/A') . "\n";
            
            if (isset($data['data'])) {
                echo "\nTransaction Details:\n";
                echo "  Status: " . ($data['data']['status'] ?? 'unknown') . "\n";
                echo "  Amount: " . ($data['data']['amount'] ?? 'N/A') . "\n";
                echo "  Currency: " . ($data['data']['currency'] ?? 'N/A') . "\n";
                echo "  Gateway Response: " . ($data['data']['gateway_response'] ?? 'N/A') . "\n";
            }
        } else {
            echo "Response Body:\n";
            print_r($response->json());
        }
        
    } catch (\Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }
} else {
    echo "No pending payments found in database.\n";
}
