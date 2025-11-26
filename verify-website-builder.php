#!/usr/bin/env php
<?php
/**
 * Website Builder Setup Verification Script
 * 
 * This script verifies that all components for the website builder
 * preview functionality are properly set up.
 */

echo "╔════════════════════════════════════════════════════════════╗\n";
echo "║     Website Builder Preview - Setup Verification          ║\n";
echo "╚════════════════════════════════════════════════════════════╝\n\n";

$checks = [];
$errors = [];

// Check 1: Controller exists and has preview methods
echo "🔍 Checking WebsiteBuilderController...\n";
$controllerPath = __DIR__ . '/app/Http/Controllers/WebsiteBuilderController.php';
if (file_exists($controllerPath)) {
    $controllerContent = file_get_contents($controllerPath);
    
    if (strpos($controllerContent, 'public function preview()') !== false) {
        echo "   ✅ preview() method exists\n";
        $checks[] = 'preview_method';
    } else {
        echo "   ❌ preview() method NOT found\n";
        $errors[] = 'preview() method missing in WebsiteBuilderController';
    }
    
    if (strpos($controllerContent, 'public function previewTheme') !== false) {
        echo "   ✅ previewTheme() method exists\n";
        $checks[] = 'preview_theme_method';
    } else {
        echo "   ❌ previewTheme() method NOT found\n";
        $errors[] = 'previewTheme() method missing in WebsiteBuilderController';
    }
    
    if (strpos($controllerContent, 'protected function showPreviewPage') !== false) {
        echo "   ✅ showPreviewPage() helper exists\n";
        $checks[] = 'show_preview_page_method';
    } else {
        echo "   ❌ showPreviewPage() helper NOT found\n";
        $errors[] = 'showPreviewPage() helper missing in WebsiteBuilderController';
    }
} else {
    echo "   ❌ WebsiteBuilderController.php NOT found\n";
    $errors[] = 'WebsiteBuilderController.php file not found';
}
echo "\n";

// Check 2: Routes exist
echo "🔍 Checking routes/web.php...\n";
$routesPath = __DIR__ . '/routes/web.php';
if (file_exists($routesPath)) {
    $routesContent = file_get_contents($routesPath);
    
    if (strpos($routesContent, "Route::get('/preview'") !== false) {
        echo "   ✅ Preview route exists\n";
        $checks[] = 'preview_route';
    } else {
        echo "   ❌ Preview route NOT found\n";
        $errors[] = 'Preview route missing in web.php';
    }
    
    if (strpos($routesContent, "Route::post('/preview-theme'") !== false) {
        echo "   ✅ Preview theme route exists\n";
        $checks[] = 'preview_theme_route';
    } else {
        echo "   ❌ Preview theme route NOT found\n";
        $errors[] = 'Preview theme route missing in web.php';
    }
} else {
    echo "   ❌ routes/web.php NOT found\n";
    $errors[] = 'routes/web.php file not found';
}
echo "\n";

// Check 3: Views exist
echo "🔍 Checking blade views...\n";
$setupView = __DIR__ . '/resources/views/website-builder/setup.blade.php';
if (file_exists($setupView)) {
    $setupContent = file_get_contents($setupView);
    
    if (strpos($setupContent, 'preview-theme-btn') !== false) {
        echo "   ✅ Preview button in setup.blade.php\n";
        $checks[] = 'setup_preview_button';
    } else {
        echo "   ⚠️  Preview button NOT found in setup.blade.php\n";
        echo "       (May be using different class name)\n";
    }
} else {
    echo "   ❌ setup.blade.php NOT found\n";
    $errors[] = 'setup.blade.php view not found';
}

$themePreviewView = __DIR__ . '/resources/views/website-builder/theme-preview.blade.php';
if (file_exists($themePreviewView)) {
    echo "   ✅ theme-preview.blade.php exists\n";
    $checks[] = 'theme_preview_view';
} else {
    echo "   ❌ theme-preview.blade.php NOT found\n";
    $errors[] = 'theme-preview.blade.php view not found';
}

$publicWebsiteView = __DIR__ . '/resources/views/public-website/page.blade.php';
if (file_exists($publicWebsiteView)) {
    echo "   ✅ public-website/page.blade.php exists\n";
    $checks[] = 'public_page_view';
} else {
    echo "   ❌ public-website/page.blade.php NOT found\n";
    $errors[] = 'public-website/page.blade.php view not found';
}
echo "\n";

// Check 4: Models exist
echo "🔍 Checking models...\n";
$websiteModel = __DIR__ . '/app/Models/Website.php';
if (file_exists($websiteModel)) {
    echo "   ✅ Website model exists\n";
    $checks[] = 'website_model';
} else {
    echo "   ❌ Website model NOT found\n";
    $errors[] = 'Website.php model not found';
}

$websitePageModel = __DIR__ . '/app/Models/WebsitePage.php';
if (file_exists($websitePageModel)) {
    echo "   ✅ WebsitePage model exists\n";
    $checks[] = 'website_page_model';
} else {
    echo "   ❌ WebsitePage model NOT found\n";
    $errors[] = 'WebsitePage.php model not found';
}

$websiteSectionModel = __DIR__ . '/app/Models/WebsiteSection.php';
if (file_exists($websiteSectionModel)) {
    echo "   ✅ WebsiteSection model exists\n";
    $checks[] = 'website_section_model';
} else {
    echo "   ❌ WebsiteSection model NOT found\n";
    $errors[] = 'WebsiteSection.php model not found';
}

$websiteThemeModel = __DIR__ . '/app/Models/WebsiteTheme.php';
if (file_exists($websiteThemeModel)) {
    echo "   ✅ WebsiteTheme model exists\n";
    $checks[] = 'website_theme_model';
} else {
    echo "   ❌ WebsiteTheme model NOT found\n";
    $errors[] = 'WebsiteTheme.php model not found';
}
echo "\n";

// Check 5: Service exists
echo "🔍 Checking services...\n";
$websiteService = __DIR__ . '/app/Services/WebsiteBuilderService.php';
if (file_exists($websiteService)) {
    echo "   ✅ WebsiteBuilderService exists\n";
    $checks[] = 'website_builder_service';
} else {
    echo "   ❌ WebsiteBuilderService NOT found\n";
    $errors[] = 'WebsiteBuilderService.php not found';
}
echo "\n";

// Summary
echo "╔════════════════════════════════════════════════════════════╗\n";
echo "║                    VERIFICATION SUMMARY                    ║\n";
echo "╚════════════════════════════════════════════════════════════╝\n\n";

$totalChecks = count($checks);
$totalErrors = count($errors);

echo "Total Checks Passed: $totalChecks\n";
echo "Total Issues Found: $totalErrors\n\n";

if ($totalErrors === 0) {
    echo "🎉 SUCCESS! All components are in place!\n\n";
    echo "Next steps:\n";
    echo "1. Clear cache: php artisan cache:clear\n";
    echo "2. Clear views: php artisan view:clear\n";
    echo "3. Test the preview functionality\n";
    echo "4. Refer to WEBSITE_BUILDER_TEST_GUIDE.md for testing steps\n\n";
} else {
    echo "⚠️  ISSUES FOUND:\n\n";
    foreach ($errors as $i => $error) {
        echo ($i + 1) . ". $error\n";
    }
    echo "\nPlease fix these issues before testing.\n\n";
}

echo "╔════════════════════════════════════════════════════════════╗\n";
echo "║                 Verification Complete                      ║\n";
echo "╚════════════════════════════════════════════════════════════╝\n";

exit($totalErrors === 0 ? 0 : 1);
