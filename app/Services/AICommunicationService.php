<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Business;
use App\Models\AIBusinessAdvice;
use App\Models\AILearningCache;

class AICommunicationService
{
    protected $pythonScriptPath;
    protected $conversationHistory = [];

    public function __construct()
    {
        $this->pythonScriptPath = base_path('ai_models/ai_communication_system.py');
    }

    /**
     * Process user message and generate AI response
     */
    public function processMessage($userId, $message, $businessId = null)
    {
        try {
            // Store conversation history
            if (!isset($this->conversationHistory[$userId])) {
                $this->conversationHistory[$userId] = [];
            }

            $this->conversationHistory[$userId][] = [
                'message' => $message,
                'timestamp' => now()->toISOString(),
                'type' => 'user'
            ];

            // Analyze intent
            $intent = $this->analyzeIntent($message);

            // Generate response based on intent
            $response = $this->generateResponse($intent, $message, $businessId);

            // Store AI response
            $this->conversationHistory[$userId][] = [
                'message' => $response['text'],
                'timestamp' => now()->toISOString(),
                'type' => 'ai',
                'intent' => $intent
            ];

            return [
                'response' => $response['text'],
                'intent' => $intent,
                'confidence' => $response['confidence'] ?? 0.8,
                'suggestions' => $response['suggestions'] ?? []
            ];

        } catch (\Exception $e) {
            Log::error('Error processing AI message: ' . $e->getMessage());
            return [
                'response' => "I'm having trouble processing your request right now. Please try again.",
                'intent' => 'error',
                'confidence' => 0.0,
                'suggestions' => []
            ];
        }
    }

    /**
     * Analyze user intent from message
     */
    protected function analyzeIntent($message)
    {
        $messageLower = strtolower($message);

        if (preg_match('/\b(hi|hello|hey|start)\b/', $messageLower)) {
            return 'greeting';
        } elseif (preg_match('/\b(sales|revenue|profit|income|earnings|performance|selling)\b/', $messageLower)) {
            return 'sales';
        } elseif (preg_match('/\b(market|trend|competitor|industry|current|latest|what.*trend|trending)\b/', $messageLower)) {
            return 'market';
        } elseif (preg_match('/\b(recommend|suggestion|advice|should|improve|better|optimize|enhance)\b/', $messageLower)) {
            return 'recommendation';
        } elseif (preg_match('/\b(bye|goodbye|thanks|thank)\b/', $messageLower)) {
            return 'goodbye';
        } elseif (preg_match('/\b(help|what.*can|how.*can|tell.*me)\b/', $messageLower)) {
            return 'help';
        } else {
            return 'general';
        }
    }

    /**
     * Generate response based on intent
     */
    protected function generateResponse($intent, $message, $businessId = null)
    {
        switch ($intent) {
            case 'greeting':
                return $this->generateGreeting();
            case 'sales':
                return $this->generateSalesResponse($businessId);
            case 'market':
                return $this->generateMarketResponse($businessId);
            case 'recommendation':
                return $this->generateRecommendationResponse($businessId);
            case 'help':
                return $this->generateHelpResponse($businessId);
            case 'goodbye':
                return $this->generateGoodbye();
            default:
                return $this->generateGeneralResponse();
        }
    }

    /**
     * Generate greeting response
     */
    protected function generateGreeting()
    {
        $greetings = [
            "Hello! I'm your AI business assistant. I can help you analyze sales, track market trends, and provide personalized recommendations. What would you like to explore today?",
            "Hi there! I'm here to help you grow your business with data-driven insights and strategic advice. What aspect would you like to focus on?",
            "Welcome! I'm your AI business advisor, ready to help you optimize performance and identify opportunities. What can I assist you with?"
        ];

        return [
            'text' => $greetings[array_rand($greetings)],
            'confidence' => 0.9,
            'suggestions' => [
                "Analyze my sales performance",
                "What are the latest market trends?",
                "Give me business improvement tips",
                "How can I optimize my operations?"
            ]
        ];
    }

    /**
     * Generate sales response
     */
    protected function generateSalesResponse($businessId)
    {
        try {
            $salesData = $this->getSalesData($businessId);
            
            if (!empty($salesData)) {
                $analysis = $this->analyzeSales($salesData);
                $templates = [
                    "Based on your sales data, {$analysis['insight']}. Here's what I recommend: {$analysis['recommendation']}",
                    "Your sales performance shows {$analysis['insight']}. Consider this: {$analysis['recommendation']}",
                    "Looking at your sales trends, {$analysis['insight']}. My suggestion: {$analysis['recommendation']}"
                ];
                
                $text = $templates[array_rand($templates)];
            } else {
                $text = "I don't have enough sales data yet. Start recording your sales to get detailed insights and recommendations.";
            }

            return [
                'text' => $text,
                'confidence' => 0.8,
                'suggestions' => [
                    "Show me sales trends",
                    "What's my best product?",
                    "How can I improve sales?"
                ]
            ];

        } catch (\Exception $e) {
            Log::error('Error generating sales response: ' . $e->getMessage());
            return $this->generateErrorResponse();
        }
    }

    /**
     * Generate market response
     */
    protected function generateMarketResponse($businessId)
    {
        try {
            $marketData = $this->getMarketKnowledge();
            
            if (!empty($marketData)) {
                $analysis = $this->analyzeMarket($marketData);
                $templates = [
                    "Based on current market analysis, I can see {$analysis['trend']}. This trend suggests {$analysis['impact']} for businesses like yours.",
                    "Market intelligence shows {$analysis['trend']}. This could be an opportunity for {$analysis['impact']}.",
                    "Recent market data indicates {$analysis['trend']}. For your business strategy, this means {$analysis['impact']}."
                ];
                
                $text = $templates[array_rand($templates)];
            } else {
                $text = "I'm currently analyzing market trends and gathering intelligence. Here are some general insights:

• **Digital Transformation**: Businesses are increasingly moving online
• **Customer Experience**: Personalization and convenience are key drivers
• **Sustainability**: Eco-friendly practices are becoming more important
• **Data-Driven Decisions**: Analytics and insights are crucial for growth

Would you like me to focus on any specific industry or trend area?";
            }

            return [
                'text' => $text,
                'confidence' => 0.8,
                'suggestions' => [
                    "Tell me more about digital trends",
                    "What are the latest customer preferences?",
                    "Show me industry-specific insights",
                    "How can I adapt to these trends?"
                ]
            ];

        } catch (\Exception $e) {
            Log::error('Error generating market response: ' . $e->getMessage());
            return $this->generateErrorResponse();
        }
    }

    /**
     * Generate recommendation response
     */
    protected function generateRecommendationResponse($businessId)
    {
        try {
            $businessData = $this->getBusinessData($businessId);
            $recommendations = $this->generateRecommendations($businessData);
            
            if ($businessData) {
                $businessType = $businessData->business_type ?? 'general';
                $businessName = $businessData->name ?? 'your business';
                
                $templates = [
                    "For {$businessName}, I recommend {$recommendations['action']} because {$recommendations['reason']}",
                    "Based on your {$businessType} business, I suggest {$recommendations['action']}. Here's why: {$recommendations['reason']}",
                    "For optimal growth of {$businessName}, consider {$recommendations['action']}. The reasoning: {$recommendations['reason']}"
                ];
            } else {
                $templates = [
                    "I recommend {$recommendations['action']} because {$recommendations['reason']}",
                    "Here's my suggestion: {$recommendations['action']}. The reasoning: {$recommendations['reason']}",
                    "For business improvement, I suggest {$recommendations['action']} based on {$recommendations['reason']}"
                ];
            }
            
            $text = $templates[array_rand($templates)];

            return [
                'text' => $text,
                'confidence' => 0.8,
                'suggestions' => [
                    "Tell me more about this recommendation",
                    "What are the implementation steps?",
                    "How can I measure the results?",
                    "Are there any risks to consider?"
                ]
            ];

        } catch (\Exception $e) {
            Log::error('Error generating recommendation response: ' . $e->getMessage());
            return $this->generateErrorResponse();
        }
    }

    /**
     * Generate help response
     */
    protected function generateHelpResponse($businessId)
    {
        $helpText = "I'm here to help you with your business! Here's what I can assist you with:

• **Sales Analysis**: Track performance, identify trends, and optimize revenue
• **Market Intelligence**: Get insights on industry trends and competitor analysis  
• **Business Recommendations**: Receive personalized advice for growth and improvement
• **Product Optimization**: Analyze your offerings and suggest improvements
• **Customer Insights**: Understand your customer behavior and preferences

What specific aspect of your business would you like to explore?";

        return [
            'text' => $helpText,
            'confidence' => 0.9,
            'suggestions' => [
                "Analyze my sales performance",
                "What are the latest market trends?",
                "Give me business improvement tips",
                "How can I optimize my products?"
            ]
        ];
    }

    /**
     * Generate goodbye response
     */
    protected function generateGoodbye()
    {
        return [
            'text' => "Thanks for chatting! Feel free to ask me anything about your business anytime.",
            'confidence' => 0.9,
            'suggestions' => []
        ];
    }

    /**
     * Generate general response
     */
    protected function generateGeneralResponse()
    {
        $responses = [
            "I'm here to help you with your business! I can analyze your sales, provide market insights, and give personalized recommendations. What would you like to focus on?",
            "Great question! I can help you with sales analysis, market trends, business optimization, and strategic recommendations. What aspect interests you most?",
            "I'm your AI business assistant, ready to help you grow! I can provide insights on performance, trends, and opportunities. What would you like to explore?"
        ];
        
        return [
            'text' => $responses[array_rand($responses)],
            'confidence' => 0.7,
            'suggestions' => [
                "Analyze my sales performance",
                "What are the current market trends?",
                "Give me business improvement tips",
                "How can I optimize my operations?"
            ]
        ];
    }

    /**
     * Generate error response
     */
    protected function generateErrorResponse()
    {
        $errorMessages = [
            "I'm having trouble accessing that information right now. Could you try rephrasing your question?",
            "I didn't quite understand that. Can you be more specific about what you're looking for?",
            "Let me help you better. Could you clarify what you'd like to know about your business?"
        ];

        return [
            'text' => $errorMessages[array_rand($errorMessages)],
            'confidence' => 0.3,
            'suggestions' => [
                "Try asking about sales",
                "Ask about market trends",
                "Request business advice"
            ]
        ];
    }

    /**
     * Get sales data from database
     */
    protected function getSalesData($businessId)
    {
        if (!$businessId) {
            return [];
        }

        try {
            return DB::table('orders')
                ->where('business_id', $businessId)
                ->select('id', 'total_amount', 'created_at')
                ->orderBy('created_at', 'desc')
                ->limit(50)
                ->get()
                ->toArray();
        } catch (\Exception $e) {
            Log::error('Error getting sales data: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get market knowledge from database
     */
    protected function getMarketKnowledge()
    {
        try {
            return DB::table('knowledge_data')
                ->whereIn('data_type', ['news', 'market_data', 'social_media'])
                ->select('data_type', 'source', 'category', 'data', 'relevance_score', 'sentiment_score')
                ->orderBy('created_at', 'desc')
                ->limit(20)
                ->get()
                ->toArray();
        } catch (\Exception $e) {
            Log::error('Error getting market knowledge: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get business data
     */
    protected function getBusinessData($businessId)
    {
        if (!$businessId) {
            return [];
        }

        try {
            return DB::table('businesses')
                ->where('id', $businessId)
                ->select('id', 'name', 'business_type', 'business_category')
                ->first();
        } catch (\Exception $e) {
            Log::error('Error getting business data: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Analyze sales data
     */
    protected function analyzeSales($salesData)
    {
        if (empty($salesData)) {
            return [
                'insight' => 'no sales data available',
                'recommendation' => 'start recording your sales'
            ];
        }

        try {
            $totalSales = collect($salesData)->sum('total_amount');
            $avgOrder = $totalSales / count($salesData);

            $insight = "total sales of $" . number_format($totalSales, 2) . " with average order value of $" . number_format($avgOrder, 2);
            $recommendation = "focus on customer retention and increasing order frequency";

            return [
                'insight' => $insight,
                'recommendation' => $recommendation
            ];

        } catch (\Exception $e) {
            Log::error('Error analyzing sales: ' . $e->getMessage());
            return [
                'insight' => 'unable to analyze sales',
                'recommendation' => 'check your data'
            ];
        }
    }

    /**
     * Analyze market data
     */
    protected function analyzeMarket($marketData)
    {
        if (empty($marketData)) {
            return [
                'trend' => 'insufficient market data',
                'impact' => 'continue monitoring'
            ];
        }

        try {
            // Calculate average sentiment
            $sentiments = collect($marketData)->pluck('sentiment_score')->filter();
            $avgSentiment = $sentiments->avg() ?: 0;

            // Get most relevant category
            $mostRelevant = collect($marketData)->sortByDesc('relevance_score')->first();
            $category = $mostRelevant->category ?? 'general';

            if ($avgSentiment > 0.1) {
                $trend = "positive trend in {$category}";
                $impact = "opportunities for growth";
            } elseif ($avgSentiment < -0.1) {
                $trend = "negative trend in {$category}";
                $impact = "challenges to address";
            } else {
                $trend = "stable conditions in {$category}";
                $impact = "steady business environment";
            }

            return [
                'trend' => $trend,
                'impact' => $impact
            ];

        } catch (\Exception $e) {
            Log::error('Error analyzing market: ' . $e->getMessage());
            return [
                'trend' => 'market analysis unavailable',
                'impact' => 'continue gathering data'
            ];
        }
    }

    /**
     * Generate business recommendations
     */
    protected function generateRecommendations($businessData)
    {
        try {
            $businessType = $businessData->business_type ?? 'general';

            switch ($businessType) {
                case 'retail':
                    $action = "implement inventory management";
                    $reason = "retail businesses benefit from optimized stock levels";
                    break;
                case 'service':
                    $action = "focus on customer retention";
                    $reason = "service businesses thrive on repeat customers";
                    break;
                case 'technology':
                    $action = "invest in digital marketing";
                    $reason = "tech businesses need strong online presence";
                    break;
                default:
                    $action = "analyze customer data regularly";
                    $reason = "data-driven decisions lead to better outcomes";
            }

            return [
                'action' => $action,
                'reason' => $reason
            ];

        } catch (\Exception $e) {
            Log::error('Error generating recommendations: ' . $e->getMessage());
            return [
                'action' => 'review business strategy',
                'reason' => 'periodic reviews help maintain business health'
            ];
        }
    }

    /**
     * Get conversation history
     */
    public function getConversationHistory($userId)
    {
        return $this->conversationHistory[$userId] ?? [];
    }

    /**
     * Clear conversation history
     */
    public function clearConversationHistory($userId)
    {
        unset($this->conversationHistory[$userId]);
        return true;
    }
}
