<?php
/**
 * Host Pinnacle SMS Integration Test Script
 * 
 * Run this script to test your SMS configuration
 * Usage: php test_sms_integration.php
 */

require __DIR__.'/vendor/autoload.php';

use App\Services\HostPinnacleSmsService;

// Bootstrap Laravel
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "========================================\n";
echo "HOST PINNACLE SMS INTEGRATION TEST\n";
echo "========================================\n\n";

// Initialize SMS service
$smsService = new HostPinnacleSmsService();

// Test 1: Check Configuration
echo "Test 1: Checking Configuration...\n";
if ($smsService->isConfigured()) {
    echo "✅ SMS service is properly configured\n";
    echo "   - User ID: " . config('services.hostpinnacle.user_id') . "\n";
    echo "   - Sender ID: " . config('services.hostpinnacle.sender_id') . "\n";
    echo "   - API URL: " . config('services.hostpinnacle.api_url') . "\n";
} else {
    echo "❌ SMS service is NOT configured\n";
    echo "   Please add the following to your .env file:\n";
    echo "   HOSTPINNACLE_USER_ID=your_username\n";
    echo "   HOSTPINNACLE_PASSWORD=your_password\n";
    echo "   HOSTPINNACLE_SENDER_ID=YOUR_APPROVED_SENDER_ID\n";
    echo "\n";
    exit(1);
}

echo "\n";

// Test 2: Check Account Status
echo "Test 2: Checking Account Status...\n";
$status = $smsService->getAccountStatus();
if ($status['success']) {
    echo "✅ Successfully connected to Host Pinnacle API\n";
    if (isset($status['data'])) {
        echo "   Account Details:\n";
        foreach ($status['data'] as $key => $value) {
            if (!is_array($value)) {
                echo "   - " . ucfirst($key) . ": " . $value . "\n";
            }
        }
    }
} else {
    echo "⚠️  Could not retrieve account status\n";
    echo "   Message: " . ($status['message'] ?? 'Unknown error') . "\n";
    if (isset($status['error'])) {
        echo "   Error: " . $status['error'] . "\n";
    }
}

echo "\n";

// Test 3: Send Test SMS (optional)
echo "Test 3: Send Test SMS\n";
echo "Do you want to send a test SMS? (yes/no): ";
$handle = fopen("php://stdin", "r");
$line = trim(fgets($handle));

if (strtolower($line) === 'yes' || strtolower($line) === 'y') {
    echo "Enter phone number (e.g., 254712345678 or 0712345678): ";
    $phone = trim(fgets($handle));
    
    if (empty($phone)) {
        echo "❌ Phone number is required\n";
        exit(1);
    }
    
    echo "Sending test SMS to $phone...\n";
    
    $message = "Test SMS from Shopybook. Your SMS integration is working! Time: " . date('Y-m-d H:i:s');
    
    // Send with test mode enabled (won't actually deliver, just tests API)
    $result = $smsService->sendSms($phone, $message, ['test' => true]);
    
    if ($result['success']) {
        echo "✅ Test SMS sent successfully!\n";
        echo "   Recipients: " . ($result['recipients'] ?? 1) . "\n";
        if (isset($result['data'])) {
            echo "   API Response:\n";
            foreach ($result['data'] as $key => $value) {
                if (!is_array($value)) {
                    echo "   - " . ucfirst($key) . ": " . $value . "\n";
                }
            }
        }
        echo "\n";
        echo "⚠️  Note: Test mode was enabled, so the SMS was not actually delivered.\n";
        echo "   To send a real SMS, set 'test' => false or remove the test parameter.\n";
    } else {
        echo "❌ Failed to send test SMS\n";
        echo "   Message: " . ($result['message'] ?? 'Unknown error') . "\n";
        if (isset($result['error'])) {
            echo "   Error: " . $result['error'] . "\n";
        }
    }
} else {
    echo "⏭️  Skipping test SMS\n";
}

fclose($handle);

echo "\n";
echo "========================================\n";
echo "TEST COMPLETED\n";
echo "========================================\n";
echo "\n";
echo "Next Steps:\n";
echo "1. If configuration is correct, you're ready to send SMS!\n";
echo "2. Check HOST_PINNACLE_SMS_SETUP.md for usage examples\n";
echo "3. Visit Marketing → Bulk SMS in your application to send SMS\n";
echo "4. Monitor logs at: storage/logs/laravel.log\n";
echo "\n";

