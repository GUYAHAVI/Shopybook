<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Auth\Events\Registered;

echo "=== User Registration & Verification Email Test ===\n\n";

// Test email - using Gmail + trick for unique address
$testEmail = 'harveyelvis24+verifytest@gmail.com';

echo "Testing with: {$testEmail}\n";
echo "(This goes to harveyelvis24@gmail.com inbox)\n\n";

// 1. Check if user already exists
$existingUser = User::where('email', $testEmail)->first();

if ($existingUser) {
    echo "User already exists. Deleting old test user...\n";
    $existingUser->delete();
    echo "✓ Old user deleted\n\n";
}

// 2. Create new user (simulating registration)
echo "Creating new user...\n";

try {
    $user = User::create([
        'name' => 'Test User Verification',
        'email' => $testEmail,
        'password' => Hash::make('TestPassword123!'),
        'email_verified_at' => null, // Not verified yet
    ]);
    
    echo "✓ User created successfully (ID: {$user->id})\n";
    echo "  Email: {$user->email}\n";
    echo "  Verified: " . ($user->email_verified_at ? 'Yes' : 'No') . "\n\n";
    
} catch (\Exception $e) {
    echo "✗ Error creating user: " . $e->getMessage() . "\n";
    exit(1);
}

// 3. Trigger verification email (this is what Laravel does automatically)
echo "Triggering verification email...\n";

try {
    // This is what happens when a user registers
    event(new Registered($user));
    
    echo "✓ Registered event fired\n";
    echo "  Laravel should send verification email automatically\n\n";
    
} catch (\Exception $e) {
    echo "✗ Error sending verification: " . $e->getMessage() . "\n";
}

// 4. Alternative: Manually send verification email
echo "Alternatively, manually sending verification notification...\n";

try {
    $user->sendEmailVerificationNotification();
    echo "✓ Verification notification sent\n\n";
    
} catch (\Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n\n";
}

// 5. Show verification URL (for testing)
echo "=== Verification Details ===\n";
echo "Check your Gmail inbox for:\n";
echo "  - From: support@shopybook.com\n";
echo "  - Subject: Verify Email Address\n";
echo "  - Look in: Inbox, Spam, or Promotions tab\n\n";

// 6. Clean up option
echo "To clean up this test user, run:\n";
echo "php artisan tinker\n";
echo "User::where('email', '{$testEmail}')->delete();\n\n";

echo "=== Test Complete ===\n";
echo "Did you receive the verification email?\n";
