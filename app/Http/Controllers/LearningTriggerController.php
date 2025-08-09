<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\ContinuousKnowledgeService;
use Illuminate\Support\Facades\Log;

class LearningTriggerController extends Controller
{
    protected $knowledgeService;
    
    public function __construct()
    {
        $this->knowledgeService = new ContinuousKnowledgeService();
    }
    
    /**
     * Manual trigger for learning system
     */
    public function triggerLearning(Request $request)
    {
        try {
            $type = $request->input('type', 'basic'); // basic, daily, weekly
            
            Log::info("Manual learning trigger started: {$type}");
            
            switch ($type) {
                case 'basic':
                    $this->runBasicLearning();
                    break;
                case 'daily':
                    $this->runDailyLearning();
                    break;
                case 'weekly':
                    $this->runWeeklyLearning();
                    break;
                default:
                    $this->runBasicLearning();
            }
            
            return response()->json([
                'success' => true,
                'message' => "Learning process completed successfully",
                'type' => $type,
                'timestamp' => now()
            ]);
            
        } catch (\Exception $e) {
            Log::error('Manual learning trigger error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error during learning process: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Run basic learning tasks
     */
    protected function runBasicLearning()
    {
        // Gather basic knowledge data
        $this->knowledgeService->gatherBasicKnowledge();
        
        // Update business insights
        $this->knowledgeService->updateBusinessInsights();
        
        Log::info('Basic learning tasks completed');
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
        $this->knowledgeService->generateRecommendationsForAllBusinesses();
        
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
    
    /**
     * Show learning dashboard
     */
    public function dashboard()
    {
        $user = Auth::user();
        $businesses = $user->businesses;
        
        // Get learning statistics
        $stats = $this->getLearningStats();
        
        return view('learning.dashboard', compact('businesses', 'stats'));
    }
    
    /**
     * Get learning statistics
     */
    protected function getLearningStats()
    {
        try {
            $knowledgeCount = \DB::table('knowledge_data')->count();
            $trendingCount = \DB::table('trending_analysis')->count();
            $insightsCount = \DB::table('knowledge_insights')->count();
            
            return [
                'knowledge_items' => $knowledgeCount,
                'trending_topics' => $trendingCount,
                'business_insights' => $insightsCount,
                'last_updated' => \DB::table('knowledge_data')->max('created_at')
            ];
        } catch (\Exception $e) {
            return [
                'knowledge_items' => 0,
                'trending_topics' => 0,
                'business_insights' => 0,
                'last_updated' => null
            ];
        }
    }
}

