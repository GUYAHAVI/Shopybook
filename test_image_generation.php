<?php

/**
 * Test Image Generation Fix
 * Tests the corrected image download and validation
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

echo "Testing AI Image Generation Fix\n";
echo "================================\n\n";

// Test 1: Generate a simple test URL
$testPrompt = "A beautiful sunset over mountains";
$encodedPrompt = rawurlencode($testPrompt);
$imageUrl = "https://image.pollinations.ai/prompt/{$encodedPrompt}?width=512&height=512&nologo=true&model=flux";

echo "Test 1: Downloading test image\n";
echo "Prompt: {$testPrompt}\n";
echo "URL: {$imageUrl}\n\n";

try {
    $response = Http::timeout(30)
        ->withOptions([
            'verify' => false,
            'allow_redirects' => ['max' => 5],
        ])
        ->get($imageUrl);
    
    if ($response->successful()) {
        $imageContent = $response->body();
        echo "✓ HTTP request successful (Status: {$response->status()})\n";
        echo "✓ Content length: " . strlen($imageContent) . " bytes\n";
        
        // Check content type
        $contentType = $response->header('Content-Type');
        echo "✓ Content-Type: {$contentType}\n";
        
        // Validate image content
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->buffer($imageContent);
        echo "✓ Detected MIME type: {$mimeType}\n";
        
        if (in_array($mimeType, ['image/png', 'image/jpeg', 'image/jpg', 'image/webp'])) {
            echo "✓ Valid image format detected!\n";
            
            // Try to save it
            $testFile = storage_path('app/public/marketing/test-image-' . time() . '.png');
            $testDir = dirname($testFile);
            
            if (!file_exists($testDir)) {
                mkdir($testDir, 0755, true);
            }
            
            file_put_contents($testFile, $imageContent);
            
            if (file_exists($testFile) && filesize($testFile) > 0) {
                echo "✓ Image saved successfully to: {$testFile}\n";
                echo "✓ File size: " . filesize($testFile) . " bytes\n";
                echo "\n✅ TEST PASSED: Image generation is working correctly!\n";
            } else {
                echo "✗ Failed to save image file\n";
            }
        } else {
            echo "✗ Invalid image format: {$mimeType}\n";
            echo "Content preview: " . substr($imageContent, 0, 200) . "\n";
        }
    } else {
        echo "✗ HTTP request failed (Status: {$response->status()})\n";
        echo "Response: " . substr($response->body(), 0, 500) . "\n";
    }
} catch (\Exception $e) {
    echo "✗ Exception: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}

echo "\n================================\n";
echo "Test Complete\n";
