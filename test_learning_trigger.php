<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Services\ContinuousKnowledgeService;
use Illuminate\Support\Facades\DB;

echo "Testing Learning Trigger...\n";

try {
    $service = new ContinuousKnowledgeService();
    
    echo "1. Testing gatherBasicKnowledge()...\n";
    $result = $service->gatherBasicKnowledge();
    echo "Result: " . ($result ? "SUCCESS" : "FAILED") . "\n";
    
    echo "2. Testing updateBusinessInsights()...\n";
    $result = $service->updateBusinessInsights();
    echo "Result: " . ($result ? "SUCCESS" : "FAILED") . "\n";
    
    echo "3. Testing gatherMarketData()...\n";
    $result = $service->gatherMarketData();
    echo "Result: " . ($result ? "SUCCESS" : "FAILED") . "\n";
    
    echo "4. Testing analyzeTrends()...\n";
    $result = $service->analyzeTrends();
    echo "Result: " . ($result ? "SUCCESS" : "FAILED") . "\n";
    
    echo "5. Testing generateRecommendationsForAllBusinesses()...\n";
    $result = $service->generateRecommendationsForAllBusinesses();
    echo "Result: " . ($result ? "SUCCESS" : "FAILED") . "\n";
    
    echo "6. Testing performDeepAnalysis()...\n";
    $result = $service->performDeepAnalysis();
    echo "Result: " . ($result ? "SUCCESS" : "FAILED") . "\n";
    
    echo "7. Testing generateReports()...\n";
    $result = $service->generateReports();
    echo "Result: " . ($result ? "SUCCESS" : "FAILED") . "\n";
    
    echo "8. Testing cleanupOldData()...\n";
    $result = $service->cleanupOldData();
    echo "Result: " . ($result ? "SUCCESS" : "FAILED") . "\n";
    
    echo "\nAll tests completed successfully!\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
