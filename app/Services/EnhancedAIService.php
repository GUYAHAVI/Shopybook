<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Business;
use App\Models\Order;
use App\Models\Product;
use App\Models\Service;
use App\Models\Customer;
use App\Models\KnowledgeData;

class EnhancedAIService
{
    protected $supportedLanguages = ['en', 'sw'];
    
    public function __construct()
    {
        // Initialize NLP components
    }
    
    /**
     * Process enhanced AI chat message with NLP
     */
    public function processEnhancedMessage($message, $businessId = null)
    {
        try {
            // Detect language
            $language = $this->detectLanguage($message);
            
            // Analyze sentiment
            $sentiment = $this->analyzeSentiment($message, $language);
            
            // Classify intent
            $intent = $this->classifyIntent($message, $language);
            
            // Generate response
            $response = $this->generateEnhancedResponse($intent, $sentiment, $businessId, $language);
            
            return [
                'response' => $response,
                'intent' => $intent,
                'sentiment' => $sentiment,
                'language' => $language,
                'confidence' => 0.8
            ];
            
        } catch (\Exception $e) {
            Log::error('Enhanced AI processing error: ' . $e->getMessage());
            return [
                'response' => $this->getFallbackResponse($language ?? 'en'),
                'intent' => 'error',
                'sentiment' => 'neutral',
                'language' => $language ?? 'en',
                'confidence' => 0.0
            ];
        }
    }
    
    /**
     * Detect language of the message
     */
    protected function detectLanguage($message)
    {
        $messageLower = strtolower($message);
        
        // Swahili keywords
        $swahiliKeywords = [
            'jambo', 'habari', 'asante', 'karibu', 'nzuri', 'sana', 'kubwa', 'kidogo',
            'biashara', 'pesa', 'mauzo', 'wateja', 'bidhaa', 'huduma', 'faida', 'hasara'
        ];
        
        foreach ($swahiliKeywords as $keyword) {
            if (strpos($messageLower, $keyword) !== false) {
                return 'sw';
            }
        }
        
        return 'en';
    }
    
    /**
     * Analyze sentiment of the message
     */
    protected function analyzeSentiment($message, $language)
    {
        $messageLower = strtolower($message);
        
        if ($language === 'sw') {
            $positiveWords = ['nzuri', 'bora', 'ajabu', 'mzuri', 'kamili'];
            $negativeWords = ['mbaya', 'huzuni', 'kutisha', 'maskini'];
        } else {
            $positiveWords = ['good', 'great', 'excellent', 'amazing', 'wonderful'];
            $negativeWords = ['bad', 'terrible', 'awful', 'horrible', 'worst'];
        }
        
        $positiveCount = 0;
        $negativeCount = 0;
        
        foreach ($positiveWords as $word) {
            if (strpos($messageLower, $word) !== false) {
                $positiveCount++;
            }
        }
        
        foreach ($negativeWords as $word) {
            if (strpos($messageLower, $word) !== false) {
                $negativeCount++;
            }
        }
        
        if ($positiveCount > $negativeCount) {
            return 'positive';
        } elseif ($negativeCount > $positiveCount) {
            return 'negative';
        } else {
            return 'neutral';
        }
    }
    
    /**
     * Classify intent of the message
     */
    protected function classifyIntent($message, $language)
    {
        $messageLower = strtolower($message);
        
        if ($language === 'sw') {
            $intents = [
                'greeting' => ['jambo', 'habari', 'salamu', 'hujambo'],
                'sales_analysis' => ['mauzo', 'mapato', 'faida', 'pesa'],
                'market_trends' => ['soko', 'mwelekeo', 'mpinzani', 'sekta'],
                'recommendations' => ['pendekeza', 'shauri', 'ushauri', 'lazima'],
                'help' => ['msaada', 'nini', 'jinsi gani', 'nieleze']
            ];
        } else {
            $intents = [
                'greeting' => ['hello', 'hi', 'hey', 'good morning'],
                'sales_analysis' => ['sales', 'revenue', 'profit', 'income', 'earnings'],
                'market_trends' => ['market', 'trend', 'competitor', 'industry'],
                'recommendations' => ['recommend', 'suggestion', 'advice', 'should'],
                'help' => ['help', 'what can', 'how can', 'tell me']
            ];
        }
        
        foreach ($intents as $intent => $patterns) {
            foreach ($patterns as $pattern) {
                if (strpos($messageLower, $pattern) !== false) {
                    return $intent;
                }
            }
        }
        
        return 'general';
    }
    
    /**
     * Generate enhanced response
     */
    protected function generateEnhancedResponse($intent, $sentiment, $businessId, $language)
    {
        switch ($intent) {
            case 'greeting':
                return $this->generateGreetingResponse($language, $sentiment);
                
            case 'sales_analysis':
                return $this->generateSalesAnalysisResponse($businessId, $language);
                
            case 'market_trends':
                return $this->generateMarketTrendsResponse($language);
                
            case 'recommendations':
                return $this->generateRecommendationsResponse($businessId, $language);
                
            case 'help':
                return $this->generateHelpResponse($language);
                
            default:
                return $this->generateGeneralResponse($language);
        }
    }
    
    /**
     * Generate greeting response
     */
    protected function generateGreetingResponse($language, $sentiment)
    {
        if ($language === 'sw') {
            $greetings = [
                'positive' => 'Jambo! Nimefurahi kukutana nawe. Mimi ni msaidizi wako wa biashara. Ninaweza kukusaidia kuchambua mauzo, kufuatilia mwelekeo wa soko, na kutoa mapendekezo.',
                'neutral' => 'Jambo! Mimi ni msaidizi wako wa biashara. Ninaweza kukusaidia kuchambua mauzo, kufuatilia mwelekeo wa soko, na kutoa mapendekezo.',
                'negative' => 'Jambo! Ninaona una wasiwasi. Mimi ni msaidizi wako wa biashara na ninaweza kukusaidia kutatua matatizo yako.'
            ];
        } else {
            $greetings = [
                'positive' => 'Hello! I\'m excited to meet you. I\'m your AI business assistant and I can help you analyze sales, track market trends, and provide recommendations.',
                'neutral' => 'Hello! I\'m your AI business assistant. I can help you analyze sales, track market trends, and provide recommendations.',
                'negative' => 'Hello! I see you might be concerned. I\'m your AI business assistant and I can help you solve your business challenges.'
            ];
        }
        
        return $greetings[$sentiment];
    }
    
    /**
     * Generate sales analysis response
     */
    protected function generateSalesAnalysisResponse($businessId, $language)
    {
        $salesData = $this->analyzeSales($businessId);
        
        if ($language === 'sw') {
            if ($salesData['total_sales'] == 0) {
                return 'Bado sina data ya mauzo ya kutosha. Anza kurekodi mauzo yako ili upate uchambuzi wa kina na mapendekezo.';
            }
            
            return "Uchambuzi wa mauzo yako unaonyesha mauzo ya jumla ya TSh " . number_format($salesData['total_sales'], 0) . 
                   " na thamani ya wastani ya oda ya TSh " . number_format($salesData['avg_order_value'], 0) . 
                   ". {$salesData['trend']}";
        } else {
            if ($salesData['total_sales'] == 0) {
                return "I don't have enough sales data yet. Start recording your sales to get detailed insights and recommendations.";
            }
            
            return "Your sales performance shows total sales of $" . number_format($salesData['total_sales'], 2) . 
                   " with average order value of $" . number_format($salesData['avg_order_value'], 2) . 
                   ". {$salesData['trend']}";
        }
    }
    
    /**
     * Generate market trends response
     */
    protected function generateMarketTrendsResponse($language)
    {
        if ($language === 'sw') {
            return "Mwelekeo wa soko wa sasa unajumuisha: Mabadiliko ya Kidijitali, Uzoefu wa Mteja, Uendelezaji, na Maamuzi ya Data.";
        } else {
            return "Current market trends include: Digital Transformation, Customer Experience, Sustainability, and Data-Driven Decisions.";
        }
    }
    
    /**
     * Generate recommendations response
     */
    protected function generateRecommendationsResponse($businessId, $language)
    {
        $business = Business::find($businessId);
        $recommendations = $this->generateRecommendations($businessId, $business);
        
        if ($language === 'sw') {
            return "Kulingana na data ya biashara yako, napendekeza: " . implode('; ', $recommendations);
        } else {
            return "Based on your business data, I recommend: " . implode('; ', $recommendations);
        }
    }
    
    /**
     * Generate help response
     */
    protected function generateHelpResponse($language)
    {
        if ($language === 'sw') {
            return "Niko hapa kukusaidia na biashara yako! Ninaweza kuchambua mauzo, kutoa maarifa ya soko, na kutoa mapendekezo. Unaweza kuniuliza chochote kuhusu biashara yako.";
        } else {
            return "I'm here to help you with your business! I can analyze sales, provide market insights, and give recommendations. Feel free to ask me anything about your business.";
        }
    }
    
    /**
     * Generate general response
     */
    protected function generateGeneralResponse($language)
    {
        if ($language === 'sw') {
            return "Niko hapa kukusaidia na biashara yako! Ninaweza kuchambua mauzo yako, kutoa maarifa ya soko, na kutoa mapendekezo ya kibinafsi. Je, unataka kujifunza nini?";
        } else {
            return "I'm here to help you with your business! I can analyze your sales, provide market insights, and give personalized recommendations. What would you like to learn?";
        }
    }
    
    /**
     * Get fallback response
     */
    protected function getFallbackResponse($language)
    {
        return $language === 'sw' 
            ? 'Samahani, nina tatizo kuchakata ombi lako. Tafadhali jaribu tena.'
            : 'Sorry, I\'m having trouble processing your request. Please try again.';
    }
    
    // Analysis methods (reuse from LaravelOnlyAIService)
    protected function analyzeSales($businessId)
    {
        $orders = Order::where('business_id', $businessId)
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->get();

        if ($orders->isEmpty()) {
            return [
                'total_sales' => 0,
                'avg_order_value' => 0,
                'total_orders' => 0,
                'trend' => 'No sales data available'
            ];
        }

        $totalSales = $orders->sum('total_amount');
        $avgOrderValue = $totalSales / $orders->count();
        $totalOrders = $orders->count();

        return [
            'total_sales' => $totalSales,
            'avg_order_value' => $avgOrderValue,
            'total_orders' => $totalOrders,
            'trend' => 'Sales data available'
        ];
    }
    
    protected function generateRecommendations($businessId, $business)
    {
        $recommendations = [];
        
        if ($business) {
            switch ($business->business_type) {
                case 'electronics':
                    $recommendations[] = "Consider offering extended warranties and technical support";
                    break;
                case 'salon':
                case 'beauty_service':
                    $recommendations[] = "Focus on appointment booking and customer retention";
                    break;
                case 'other_service':
                    $recommendations[] = "Develop service packages and subscription models";
                    break;
                default:
                    $recommendations[] = "Analyze customer data regularly for better insights";
            }
        }
        
        return $recommendations;
    }
}
