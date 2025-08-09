<?php

require_once 'vendor/autoload.php';

use App\Services\AIBusinessAdvisor;

echo "🧪 Testing AI Web Integration...\n\n";

try {
    $advisor = new AIBusinessAdvisor();
    echo "✅ AI Business Advisor initialized\n";
    
    // Test Python environment
    $pythonOk = $advisor->checkPythonEnvironment();
    echo "Python Environment: " . ($pythonOk ? "✅ OK" : "❌ Not Available") . "\n";
    
    // Test packages
    $packagesOk = $advisor->checkRequiredPackages();
    echo "Required Packages: " . ($packagesOk ? "✅ Installed" : "❌ Missing") . "\n";
    
    if ($pythonOk && $packagesOk) {
        echo "\n🎉 AI System is READY TO USE!\n";
        echo "📱 Start your server: php artisan serve\n";
        echo "🌐 Visit: http://127.0.0.1:8000/ai/dashboard\n";
        echo "📊 Try: http://127.0.0.1:8000/ai/analysis/comprehensive\n";
        echo "📝 Try: http://127.0.0.1:8000/ai/marketing\n";
    } else {
        echo "\n⚠️ Some components need attention\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
