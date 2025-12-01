<?php

/**
 * Verify all webbuilder images are accessible
 */

$webbuilderPath = __DIR__ . '/public/webbuilder';
$images = glob($webbuilderPath . '/*.{jpg,jpeg,png,gif,svg}', GLOB_BRACE);

echo "🖼️  Checking webbuilder images...\n\n";

if (empty($images)) {
    echo "❌ No images found in public/webbuilder/\n";
    exit(1);
}

echo "✅ Found " . count($images) . " images:\n\n";

foreach ($images as $imagePath) {
    $filename = basename($imagePath);
    $size = filesize($imagePath);
    $sizeKB = round($size / 1024, 2);
    
    // Check if image is valid
    $imageInfo = @getimagesize($imagePath);
    
    if ($imageInfo) {
        $dimensions = $imageInfo[0] . 'x' . $imageInfo[1];
        echo "  ✓ {$filename}\n";
        echo "    Size: {$sizeKB} KB | Dimensions: {$dimensions}\n";
        echo "    URL: /webbuilder/{$filename}\n\n";
    } else {
        echo "  ❌ {$filename} - Invalid or corrupted image\n\n";
    }
}

echo "\n📊 Summary:\n";
echo "  Total images: " . count($images) . "\n";
echo "  Location: public/webbuilder/\n";
echo "  Web accessible: Yes (via /webbuilder/filename.jpg)\n\n";

// Check if images are used in themes
echo "🎨 Checking theme assignments...\n\n";

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\WebsiteTheme;

$themes = WebsiteTheme::all();

foreach ($themes as $theme) {
    echo "  {$theme->name}:\n";
    echo "    Preview: {$theme->preview_image}\n";
    
    $previewPath = public_path($theme->preview_image);
    if (file_exists($previewPath)) {
        echo "    ✅ Image exists\n";
    } else {
        echo "    ❌ Image missing!\n";
    }
    echo "\n";
}

echo "\n✨ Image verification complete!\n";
