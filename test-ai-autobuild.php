<?php

/**
 * Test script for AI Auto-Build Website Feature
 * 
 * This script helps diagnose issues with the AI auto-build functionality
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Business;
use App\Services\ClaudeAPIService;
use Illuminate\Support\Facades\Log;

echo "=== AI Auto-Build Website Test ===\n\n";

// 1. Check if Claude API key is configured
echo "1. Checking Claude API configuration...\n";
$claudeApiKey = config('services.claude.api_key');
if (empty($claudeApiKey)) {
    echo "   ❌ ERROR: Claude API key is NOT configured!\n";
    echo "   Please set CLAUDE_API_KEY in your .env file\n\n";
} else {
    $maskedKey = substr($claudeApiKey, 0, 10) . '...' . substr($claudeApiKey, -4);
    echo "   ✓ Claude API key is configured: {$maskedKey}\n\n";
}

// 2. Check if ClaudeAPIService can be instantiated
echo "2. Testing ClaudeAPIService instantiation...\n";
try {
    $claudeService = new ClaudeAPIService();
    echo "   ✓ ClaudeAPIService instantiated successfully\n\n";
} catch (\Exception $e) {
    echo "   ❌ ERROR instantiating ClaudeAPIService: " . $e->getMessage() . "\n\n";
    exit(1);
}

// 3. Find a user with enterprise business
echo "3. Looking for enterprise business...\n";
$enterpriseBusiness = Business::where('plan', 'enterprise')->first();

if (!$enterpriseBusiness) {
    echo "   ⚠ WARNING: No enterprise business found in database\n";
    echo "   Creating a test enterprise business...\n";
    
    // Get first user or create one
    $user = User::first();
    if (!$user) {
        echo "   ❌ ERROR: No users found in database\n";
        echo "   Please create a user first\n\n";
        exit(1);
    }
    
    $enterpriseBusiness = Business::create([
        'user_id' => $user->id,
        'name' => 'Test Enterprise Business',
        'slug' => 'test-enterprise-' . time(),
        'business_type' => 'retail',
        'plan' => 'enterprise',
        'email' => 'test@example.com',
        'phone' => '0712345678',
        'description' => 'A test business for AI auto-build testing',
    ]);
    echo "   ✓ Created test enterprise business: {$enterpriseBusiness->name}\n\n";
} else {
    echo "   ✓ Found enterprise business: {$enterpriseBusiness->name} (ID: {$enterpriseBusiness->id})\n\n";
}

// 4. Test AI generation with minimal data
echo "4. Testing AI website generation (this may take 30-60 seconds)...\n";
$businessData = [
    'name' => $enterpriseBusiness->name,
    'type' => $enterpriseBusiness->business_type ?? 'general',
    'description' => $enterpriseBusiness->description ?? 'A professional business',
    'location' => 'Nairobi, Kenya',
    'email' => $enterpriseBusiness->email,
    'phone' => $enterpriseBusiness->phone,
];

try {
    $websiteStructure = $claudeService->generateCompleteWebsite($businessData, 'Modern');
    
    if ($websiteStructure && isset($websiteStructure['pages'])) {
        echo "   ✓ AI generation successful!\n";
        echo "   Generated " . count($websiteStructure['pages']) . " pages\n";
        echo "   Pages:\n";
        foreach ($websiteStructure['pages'] as $page) {
            $sectionCount = count($page['sections'] ?? []);
            echo "     - {$page['title']} ({$sectionCount} sections)\n";
        }
        echo "\n";
    } else {
        echo "   ❌ ERROR: AI generation returned null or invalid structure\n";
        echo "   Check storage/logs/laravel.log for details\n\n";
    }
} catch (\Exception $e) {
    echo "   ❌ ERROR during AI generation: " . $e->getMessage() . "\n";
    echo "   File: " . $e->getFile() . "\n";
    echo "   Line: " . $e->getLine() . "\n\n";
}

echo "=== Test Complete ===\n";
echo "If you see errors, check:\n";
echo "1. storage/logs/laravel.log for detailed error logs\n";
echo "2. Your .env file has correct CLAUDE_API_KEY\n";
echo "3. Your internet connection allows API calls to api.anthropic.com\n";
