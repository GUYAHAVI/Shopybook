<?php
/**
 * Recreate Storage Directory Structure
 * 
 * This script recreates the entire Laravel storage directory structure
 * that was deleted. Run this once, then delete the file.
 * 
 * Usage: php recreate-storage-structure.php
 */

echo "🔧 Recreating Laravel Storage Structure\n";
echo "========================================\n\n";

$base = __DIR__;
$directories = [
    // Main storage directories
    'storage/app',
    'storage/app/public',
    'storage/app/public/business',
    'storage/app/public/business/logos',
    'storage/app/public/products',
    'storage/app/public/brands',
    'storage/framework',
    'storage/framework/cache',
    'storage/framework/cache/data',
    'storage/framework/sessions',
    'storage/framework/testing',
    'storage/framework/views',
    'storage/logs',
];

$created = 0;
$existing = 0;

foreach ($directories as $dir) {
    $fullPath = $base . '/' . $dir;
    
    if (file_exists($fullPath)) {
        echo "✓ Already exists: $dir\n";
        $existing++;
    } else {
        if (mkdir($fullPath, 0755, true)) {
            echo "✓ Created: $dir\n";
            $created++;
            
            // Add .gitignore to certain directories
            if (in_array($dir, ['storage/framework/cache/data', 'storage/framework/sessions', 'storage/framework/views'])) {
                file_put_contents($fullPath . '/.gitignore', "*\n!.gitignore\n");
            } else {
                file_put_contents($fullPath . '/.gitignore', "*\n!.gitignore\n");
            }
        } else {
            echo "✗ Failed to create: $dir\n";
        }
    }
}

echo "\n========================================\n";
echo "Summary:\n";
echo "  Created: $created directories\n";
echo "  Existing: $existing directories\n\n";

// Check/Fix permissions
echo "🔒 Checking Permissions...\n";
$storageDir = $base . '/storage';
if (file_exists($storageDir)) {
    // Make storage writable
    chmod($storageDir, 0755);
    echo "✓ Set storage directory to 755\n";
    
    // Recursively set permissions
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($storageDir, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );
    
    foreach ($iterator as $item) {
        if ($item->isDir()) {
            chmod($item->getPathname(), 0755);
        } else {
            chmod($item->getPathname(), 0644);
        }
    }
    echo "✓ Recursively set permissions on storage directory\n";
}

// Check bootstrap/cache
echo "\n🔍 Checking bootstrap/cache...\n";
$bootstrapCache = $base . '/bootstrap/cache';
if (!file_exists($bootstrapCache)) {
    mkdir($bootstrapCache, 0755, true);
    echo "✓ Created bootstrap/cache\n";
} else {
    chmod($bootstrapCache, 0755);
    echo "✓ bootstrap/cache exists\n";
}

// Check storage symlink
echo "\n🔗 Checking storage symlink...\n";
$publicStorage = $base . '/public/storage';

if (is_link($publicStorage)) {
    echo "✓ Symlink exists: public/storage\n";
    $target = readlink($publicStorage);
    echo "  → Points to: $target\n";
} else {
    echo "⚠ Symlink does NOT exist\n";
    echo "  Run: php artisan storage:link\n";
}

echo "\n========================================\n";
echo "✅ Storage structure recreated!\n\n";

echo "📋 Next Steps:\n";
echo "1. Run: php artisan storage:link\n";
echo "2. Run: php artisan images:cleanup-missing --dry-run\n";
echo "3. Run: php artisan images:cleanup-missing --reset\n";
echo "4. Notify users to re-upload their images\n";
echo "5. Delete this script: recreate-storage-structure.php\n\n";

echo "⚠️  IMPORTANT: The actual image files are gone and cannot be recovered.\n";
echo "   Users will need to re-upload their business logos and product images.\n\n";
?>

