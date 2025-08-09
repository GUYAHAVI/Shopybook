<?php

/**
 * cPanel Continuous Learning Script
 * This script can be triggered by cPanel cron jobs to run the AI learning system
 */

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Services\ContinuousKnowledgeService;
use Illuminate\Support\Facades\Log;

class CPanelContinuousLearning
{
    protected $knowledgeService;
    
    public function __construct()
    {
        $this->knowledgeService = new ContinuousKnowledgeService();
    }
    
    /**
     * Run the continuous learning process
     */
    public function runLearning()
    {
        try {
            Log::info('Starting cPanel continuous learning process...');
            
            // Run different learning tasks based on time
            $hour = (int) date('H');
            $dayOfWeek = (int) date('w'); // 0 = Sunday
            
            // Hourly tasks (every hour)
            $this->runHourlyLearning();
            
            // Daily tasks (once per day at 2 AM)
            if ($hour === 2) {
                $this->runDailyLearning();
            }
            
            // Weekly tasks (Sundays at 3 AM)
            if ($dayOfWeek === 0 && $hour === 3) {
                $this->runWeeklyLearning();
            }
            
            Log::info('cPanel continuous learning completed successfully');
            
        } catch (\Exception $e) {
            Log::error('cPanel continuous learning error: ' . $e->getMessage());
        }
    }
    
    /**
     * Run hourly learning tasks
     */
    protected function runHourlyLearning()
    {
        // Gather basic knowledge data
        $this->knowledgeService->gatherBasicKnowledge();
        
        // Update business insights
        $this->knowledgeService->updateBusinessInsights();
        
        Log::info('Hourly learning tasks completed');
    }
    
    /**
     * Run daily learning tasks
     */
    protected function runDailyLearning()
    {
        // Gather comprehensive market data
        $this->knowledgeService->gatherMarketData();
        
        // Analyze trends
        $this->knowledgeService->analyzeTrends();
        
        // Generate business recommendations
        $this->knowledgeService->generateRecommendations();
        
        Log::info('Daily learning tasks completed');
    }
    
    /**
     * Run weekly learning tasks
     */
    protected function runWeeklyLearning()
    {
        // Deep analysis and model updates
        $this->knowledgeService->performDeepAnalysis();
        
        // Generate comprehensive reports
        $this->knowledgeService->generateReports();
        
        // Clean up old data
        $this->knowledgeService->cleanupOldData();
        
        Log::info('Weekly learning tasks completed');
    }
}

// Run the learning process
$learning = new CPanelContinuousLearning();
$learning->runLearning();

echo "Continuous learning process completed at " . date('Y-m-d H:i:s') . "\n";

