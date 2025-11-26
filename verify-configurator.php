<?php

/**
 * Website Configurator Verification Script
 * 
 * Run: php verify-configurator.php
 * 
 * This script verifies all components are in place.
 */

echo "\n🎯 Website Configurator Verification\n";
echo "====================================\n\n";

$checks = [];

// Check views exist
$views = [
    'step1' => 'resources/views/website-configurator/step1.blade.php',
    'step2' => 'resources/views/website-configurator/step2.blade.php',
    'step3' => 'resources/views/website-configurator/step3.blade.php',
    'step4' => 'resources/views/website-configurator/step4.blade.php',
];

echo "📄 Checking Views...\n";
foreach ($views as $step => $path) {
    $exists = file_exists($path);
    $checks[$step . '_view'] = $exists;
    echo ($exists ? '  ✅' : '  ❌') . " $step: $path\n";
}

// Check controller exists
echo "\n🎮 Checking Controller...\n";
$controller = 'app/Http/Controllers/WebsiteConfiguratorController.php';
$controllerExists = file_exists($controller);
$checks['controller'] = $controllerExists;
echo ($controllerExists ? '  ✅' : '  ❌') . " WebsiteConfiguratorController: $controller\n";

// Check required methods in controller
if ($controllerExists) {
    $content = file_get_contents($controller);
    $methods = [
        'step1',
        'step1Submit',
        'step2View',
        'step2',
        'step3View',
        'step3',
        'step4View',
        'build',
        'process',
    ];
    
    echo "\n🔧 Checking Controller Methods...\n";
    foreach ($methods as $method) {
        $exists = strpos($content, "function $method") !== false;
        $checks['method_' . $method] = $exists;
        echo ($exists ? '  ✅' : '  ❌') . " $method()\n";
    }
}

// Check routes file
echo "\n🛤️  Checking Routes...\n";
$routes = 'routes/web.php';
$routesExist = file_exists($routes);
$checks['routes'] = $routesExist;
echo ($routesExist ? '  ✅' : '  ❌') . " Routes file: $routes\n";

if ($routesExist) {
    $routeContent = file_get_contents($routes);
    $routeChecks = [
        'website-configurator prefix' => strpos($routeContent, "prefix('website-configurator')") !== false,
        'step1 GET route' => strpos($routeContent, "get('/step1'") !== false,
        'step1 POST route' => strpos($routeContent, "post('/step1'") !== false,
        'step2 GET route' => strpos($routeContent, "get('/step2'") !== false,
        'step2 POST route' => strpos($routeContent, "post('/step2'") !== false,
        'step3 GET route' => strpos($routeContent, "get('/step3'") !== false,
        'step3 POST route' => strpos($routeContent, "post('/step3'") !== false,
        'step4 GET route' => strpos($routeContent, "get('/step4'") !== false,
        'build POST route' => strpos($routeContent, "post('/build'") !== false,
    ];
    
    echo "\n   Route Definitions:\n";
    foreach ($routeChecks as $name => $exists) {
        $checks['route_' . $name] = $exists;
        echo ($exists ? '    ✅' : '    ❌') . " $name\n";
    }
}

// Summary
echo "\n" . str_repeat("=", 40) . "\n";
echo "📊 Summary\n";
echo str_repeat("=", 40) . "\n";

$total = count($checks);
$passed = count(array_filter($checks));
$percentage = round(($passed / $total) * 100);

echo "\n  Total Checks: $total\n";
echo "  Passed: $passed\n";
echo "  Failed: " . ($total - $passed) . "\n";
echo "  Success Rate: $percentage%\n\n";

if ($percentage === 100) {
    echo "🎉 All checks passed! Your configurator is ready!\n\n";
    echo "📍 Access your configurator at:\n";
    echo "   http://your-domain.com/website-configurator/step1\n\n";
    echo "🚀 Next Steps:\n";
    echo "   1. Login to your application\n";
    echo "   2. Navigate to /website-configurator/step1\n";
    echo "   3. Follow the 4-step wizard\n";
    echo "   4. Watch your website being built!\n\n";
} else {
    echo "⚠️  Some checks failed. Please review the errors above.\n\n";
    
    $failed = array_keys(array_filter($checks, function($v) { return !$v; }));
    echo "❌ Failed Checks:\n";
    foreach ($failed as $check) {
        echo "   - $check\n";
    }
    echo "\n";
}

echo "💡 Quick Test Commands:\n";
echo "   php artisan route:list --name=website-configurator\n";
echo "   php artisan route:clear\n";
echo "   php artisan view:clear\n";
echo "   php artisan cache:clear\n";
echo "\n";
