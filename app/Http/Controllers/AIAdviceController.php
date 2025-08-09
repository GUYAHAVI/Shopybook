<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AutomatedLearningService;
use App\Models\Business;
use App\Models\AIBusinessAdvice;
use Illuminate\Support\Facades\DB;

class AIAdviceController extends Controller
{
    protected $learningService;

    public function __construct(AutomatedLearningService $learningService)
    {
        $this->learningService = $learningService;
    }

    /**
     * Show AI advice dashboard
     */
    public function index(Business $business)
    {
        // Get learning status
        $learningStatus = $this->learningService->getLearningStatus($business->id);
        
        // Get latest advice
        $advice = $this->learningService->getLatestAdvice($business->id, 10);
        
        // Get business performance
        $performance = $this->getBusinessPerformance($business->id);
        
        return view('business.ai-advice', compact('business', 'learningStatus', 'advice', 'performance'));
    }

    /**
     * Trigger learning for a business
     */
    public function triggerLearning(Request $request, Business $business)
    {
        try {
            $success = $this->learningService->startLearningForBusiness($business->id);
            
            if ($success) {
                return redirect()->back()->with('success', 'AI learning triggered successfully. Check back in a few minutes for new insights.');
            } else {
                return redirect()->back()->with('error', 'Failed to trigger AI learning. Please try again.');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error triggering AI learning: ' . $e->getMessage());
        }
    }

    /**
     * Update AI learning settings
     */
    public function updateSettings(Request $request, Business $business)
    {
        $request->validate([
            'automated_learning_enabled' => 'boolean',
            'competitor_analysis_enabled' => 'boolean',
            'market_trends_enabled' => 'boolean',
            'social_media_learning_enabled' => 'boolean',
        ]);

        try {
            $settings = [
                'automated_learning_enabled' => $request->has('automated_learning_enabled'),
                'competitor_analysis_enabled' => $request->has('competitor_analysis_enabled'),
                'market_trends_enabled' => $request->has('market_trends_enabled'),
                'social_media_learning_enabled' => $request->has('social_media_learning_enabled'),
            ];

            $this->learningService->updateLearningSettings($business->id, $settings);

            return redirect()->back()->with('success', 'AI learning settings updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error updating settings: ' . $e->getMessage());
        }
    }

    /**
     * Mark advice as read
     */
    public function markAsRead(Request $request, $adviceId)
    {
        try {
            $success = $this->learningService->markAdviceAsRead($adviceId);
            
            if ($success) {
                return response()->json(['success' => true]);
            } else {
                return response()->json(['success' => false, 'message' => 'Failed to mark as read']);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Get business performance data
     */
    private function getBusinessPerformance($businessId)
    {
        try {
            $performance = DB::select("
                SELECT 
                    COUNT(DISTINCT o.id) as total_orders,
                    SUM(CASE WHEN o.status = 'completed' THEN o.total_amount ELSE 0 END) as revenue,
                    COUNT(DISTINCT c.id) as total_customers,
                    AVG(CASE WHEN o.status = 'completed' THEN o.total_amount ELSE NULL END) as avg_order_value
                FROM businesses b
                LEFT JOIN orders o ON b.id = o.business_id
                LEFT JOIN customers c ON b.id = c.business_id
                WHERE b.id = ?
            ", [$businessId]);

            return $performance[0] ?? null;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Get competitor insights
     */
    public function getCompetitorInsights(Business $business)
    {
        try {
            $insights = $this->learningService->getCompetitorInsights($business->id);
            
            return response()->json([
                'success' => true,
                'insights' => $insights
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Get trending topics
     */
    public function getTrendingTopics(Business $business)
    {
        try {
            $topics = $this->learningService->getTrendingTopics($business->business_type);
            
            return response()->json([
                'success' => true,
                'topics' => $topics
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Get unread advice count
     */
    public function getUnreadCount(Business $business)
    {
        try {
            $count = $this->learningService->getUnreadAdviceCount($business->id);
            
            return response()->json([
                'success' => true,
                'count' => $count
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Start automated learning system
     */
    public function startAutomatedSystem()
    {
        try {
            $success = $this->learningService->startAutomatedSystem();
            
            if ($success) {
                return response()->json(['success' => true, 'message' => 'Automated learning system started']);
            } else {
                return response()->json(['success' => false, 'message' => 'Failed to start automated system']);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Generate performance advice
     */
    public function generatePerformanceAdvice(Business $business)
    {
        try {
            $advice = $this->learningService->generatePerformanceAdvice($business->id);
            
            if ($advice) {
                return response()->json([
                    'success' => true,
                    'advice' => $advice
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to generate advice'
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}
