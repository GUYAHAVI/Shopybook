<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Checking subscription_payments table...\n\n";

$count = DB::table('subscription_payments')->count();
echo "Total payments: $count\n\n";

if ($count > 0) {
    echo "Recent payments:\n";
    echo str_repeat('-', 80) . "\n";
    
    $payments = DB::table('subscription_payments')
        ->orderBy('created_at', 'desc')
        ->limit(5)
        ->get();
    
    foreach ($payments as $payment) {
        echo "Status: {$payment->status}\n";
        echo "Reference: {$payment->checkout_request_id}\n";
        echo "Phone: {$payment->phone_number}\n";
        echo "Amount: KES {$payment->amount}\n";
        echo "Plan: {$payment->plan}\n";
        echo "Created: {$payment->created_at}\n";
        echo str_repeat('-', 80) . "\n";
    }
} else {
    echo "No payments in database yet.\n";
}
