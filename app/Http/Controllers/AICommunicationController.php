<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\EnhancedAIService;
use App\Models\Business;

class AICommunicationController extends Controller
{
    protected $aiService;

    public function __construct()
    {
        $this->aiService = new EnhancedAIService();
    }

    /**
     * Show the AI chat interface
     */
    public function chat()
    {
        $user = Auth::user();
        $businesses = Business::where('user_id', $user->id)->get();
        
        return view('ai.chat', compact('businesses'));
    }

    /**
     * Process user message and get AI response
     */
    public function processMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
            'business_id' => 'nullable|exists:businesses,id'
        ]);

        try {
            $userId = Auth::id();
            $message = $request->input('message');
            $businessId = $request->input('business_id');

            // Process message through enhanced AI service
            $response = $this->aiService->processEnhancedMessage($message, $businessId);

            return response()->json([
                'success' => true,
                'response' => $response['response'],
                'intent' => $response['intent'],
                'confidence' => $response['confidence'],
                'suggestions' => $response['suggestions']
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error processing message: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get conversation history
     */
    public function getHistory(Request $request)
    {
        try {
            $userId = Auth::id();
            $history = $this->aiService->getConversationHistory($userId);

            return response()->json([
                'success' => true,
                'history' => $history
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error getting history: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Clear conversation history
     */
    public function clearHistory(Request $request)
    {
        try {
            $userId = Auth::id();
            $this->aiService->clearConversationHistory($userId);

            return response()->json([
                'success' => true,
                'message' => 'Conversation history cleared successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error clearing history: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get AI suggestions based on business type
     */
    public function getSuggestions(Request $request)
    {
        $request->validate([
            'business_id' => 'required|exists:businesses,id'
        ]);

        try {
            $business = Business::findOrFail($request->business_id);
            
            $suggestions = $this->getBusinessSuggestions($business);

            return response()->json([
                'success' => true,
                'suggestions' => $suggestions
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error getting suggestions: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get business-specific suggestions
     */
    protected function getBusinessSuggestions($business)
    {
        $suggestions = [
            "How are my sales performing?",
            "What market trends should I know?",
            "Give me business recommendations"
        ];

        // Add business-specific suggestions
        switch ($business->business_type) {
            case 'retail':
                $suggestions[] = "What are my best-selling products?";
                $suggestions[] = "How can I optimize my inventory?";
                $suggestions[] = "What pricing strategy should I use?";
                break;
            case 'service':
                $suggestions[] = "How can I improve customer retention?";
                $suggestions[] = "What service trends should I know?";
                $suggestions[] = "How can I increase customer satisfaction?";
                break;
            case 'technology':
                $suggestions[] = "What tech trends are affecting my business?";
                $suggestions[] = "How can I improve my digital presence?";
                $suggestions[] = "What cybersecurity measures should I take?";
                break;
            case 'food':
                $suggestions[] = "What are the latest food industry trends?";
                $suggestions[] = "How can I improve my menu?";
                $suggestions[] = "What marketing strategies work for restaurants?";
                break;
            case 'health':
                $suggestions[] = "What healthcare trends should I know?";
                $suggestions[] = "How can I improve patient care?";
                $suggestions[] = "What compliance requirements should I focus on?";
                break;
            default:
                $suggestions[] = "What are the latest industry trends?";
                $suggestions[] = "How can I improve my business efficiency?";
                $suggestions[] = "What marketing strategies should I use?";
        }

        return $suggestions;
    }

    /**
     * Get AI system status
     */
    public function getStatus()
    {
        try {
            // Check if knowledge data exists
            $knowledgeCount = \DB::table('knowledge_data')->count();
            $businessCount = Business::where('user_id', Auth::id())->count();

            $status = [
                'knowledge_available' => $knowledgeCount > 0,
                'knowledge_count' => $knowledgeCount,
                'businesses_count' => $businessCount,
                'system_ready' => true
            ];

            return response()->json([
                'success' => true,
                'status' => $status
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error getting status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get quick insights for dashboard
     */
    public function getQuickInsights(Request $request)
    {
        $request->validate([
            'business_id' => 'required|exists:businesses,id'
        ]);

        try {
            $businessId = $request->business_id;
            
            // Get sales insights
            $salesData = \DB::table('sales')
                ->where('business_id', $businessId)
                ->select('total_amount', 'created_at')
                ->orderBy('created_at', 'desc')
                ->limit(30)
                ->get();

            $totalSales = $salesData->sum('total_amount');
            $avgOrder = $salesData->count() > 0 ? $totalSales / $salesData->count() : 0;

            // Get market insights
            $marketData = \DB::table('knowledge_data')
                ->whereIn('data_type', ['news', 'market_data'])
                ->select('sentiment_score', 'relevance_score')
                ->orderBy('created_at', 'desc')
                ->limit(20)
                ->get();

            $avgSentiment = $marketData->count() > 0 ? $marketData->avg('sentiment_score') : 0;

            $insights = [
                'sales' => [
                    'total' => number_format($totalSales, 2),
                    'avg_order' => number_format($avgOrder, 2),
                    'orders_count' => $salesData->count()
                ],
                'market' => [
                    'sentiment' => round($avgSentiment, 2),
                    'trend' => $avgSentiment > 0.1 ? 'positive' : ($avgSentiment < -0.1 ? 'negative' : 'neutral')
                ],
                'recommendations' => $this->getQuickRecommendations($businessId, $totalSales, $avgSentiment)
            ];

            return response()->json([
                'success' => true,
                'insights' => $insights
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error getting insights: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get quick recommendations based on data
     */
    protected function getQuickRecommendations($businessId, $totalSales, $avgSentiment)
    {
        $recommendations = [];

        if ($totalSales == 0) {
            $recommendations[] = "Start recording your sales to get detailed insights";
        } else {
            $recommendations[] = "Continue tracking sales to identify trends";
        }

        if ($avgSentiment > 0.2) {
            $recommendations[] = "Market sentiment is positive - consider expanding";
        } elseif ($avgSentiment < -0.2) {
            $recommendations[] = "Market sentiment is negative - focus on cost optimization";
        } else {
            $recommendations[] = "Market is stable - maintain current strategies";
        }

        $recommendations[] = "Regular analysis helps identify improvement opportunities";

        return $recommendations;
    }
}
