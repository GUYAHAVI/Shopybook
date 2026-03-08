<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;

echo "=== Direct Email Send Test ===\n\n";

// Force QUEUE_CONNECTION to sync to bypass queue
Config::set('queue.default', 'sync');
echo "✓ Queue driver set to 'sync' (immediate sending)\n\n";

$testEmail = 'harveyelvis24@gmail.com';

// Find or create test user
$user = User::where('email', $testEmail)->first();

if (!$user) {
    echo "Creating test user...\n";
    $user = User::create([
        'name' => 'Harvey Elvis',
        'email' => $testEmail,
        'password' => bcrypt('TestPassword123!'),
        'email_verified_at' => null,
    ]);
    echo "✓ User created (ID: {$user->id})\n\n";
} else {
    echo "Using existing user (ID: {$user->id})\n";
    // Reset verification
    $user->email_verified_at = null;
    $user->save();
    echo "✓ Verification reset\n\n";
}

// Send verification notification
echo "Sending verification email (synchronously)...\n";

try {
    $user->sendEmailVerificationNotification();
    echo "✓ Notification sent!\n\n";
    
    echo "Check your Gmail:\n";
    echo "  Email: {$testEmail}\n";
    echo "  From: support@shopybook.com\n";
    echo "  Subject: Verify Email Address\n";
    echo "  Check: Inbox, Spam, Promotions\n";
    
} catch (\Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    echo "\nFull trace:\n";
    echo $e->getTraceAsString() . "\n";
}

echo "\n=== Test Complete ===\n";
