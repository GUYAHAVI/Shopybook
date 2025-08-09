<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Business;
use App\Models\AIBusinessAdvice;
use App\Models\AILearningCache;
use App\Models\AILearningSettings;
use Carbon\Carbon;

class AutomatedLearningService
{
    /**
     * Start automated learning for a business
     */
    public function startLearningForBusiness($businessId)
    {
        try {
            $business = Business::find($businessId);
            if (!$business) {
                return false;
            }

            // Create learning settings if they don't exist
            $this->createLearningSettings($businessId);

            // Trigger initial learning
            $this->triggerLearning($businessId);

            Log::info("Automated learning started for business: {$business->name}");
            return true;

        } catch (\Exception $e) {
            Log::error("Error starting automated learning: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Create learning settings for a business
     */
    public function createLearningSettings($businessId)
    {
        $settings = AILearningSettings::firstOrCreate(
            ['business_id' => $businessId],
            [
                'automated_learning_enabled' => true,
                'competitor_analysis_enabled' => true,
                'market_trends_enabled' => true,
                'social_media_learning_enabled' => true,
                'learning_keywords' => $this->generateKeywordsForBusiness($businessId),
                'excluded_competitors' => []
            ]
        );

        return $settings;
    }

    /**
     * Generate keywords for business learning
     */
    private function generateKeywordsForBusiness($businessId)
    {
        $business = Business::find($businessId);
        $keywords = [];

        // Add business type keywords
        switch ($business->business_type) {
            case 'retail':
                $keywords = ['retail store', 'shop', 'boutique', 'supermarket'];
                break;
            case 'service':
                $keywords = ['service business', 'consulting', 'professional services'];
                break;
            case 'restaurant':
                $keywords = ['restaurant', 'cafe', 'food service', 'dining'];
                break;
            case 'salon':
                $keywords = ['salon', 'beauty', 'spa', 'wellness'];
                break;
            case 'barbershop':
                $keywords = ['barbershop', 'haircut', 'grooming', 'men salon'];
                break;
        }

        // Add business category
        if ($business->business_category) {
            $keywords[] = $business->business_category;
        }

        // Add location-based keywords
        if ($business->location) {
            $keywords[] = $business->location;
        }

        return $keywords;
    }

    /**
     * Trigger learning for a business
     */
    public function triggerLearning($businessId)
    {
        try {
            // Call Python AI system
            $command = "cd " . base_path('ai_models') . " && python automated_learning_system.py --business-id {$businessId}";
            exec($command . " > /dev/null 2>&1 &");

            Log::info("Learning triggered for business: {$businessId}");
            return true;

        } catch (\Exception $e) {
            Log::error("Error triggering learning: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get latest advice for a business
     */
    public function getLatestAdvice($businessId, $limit = 5)
    {
        return AIBusinessAdvice::where('business_id', $businessId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get unread advice count
     */
    public function getUnreadAdviceCount($businessId)
    {
        return AIBusinessAdvice::where('business_id', $businessId)
            ->where('is_read', false)
            ->count();
    }

    /**
     * Mark advice as read
     */
    public function markAdviceAsRead($adviceId)
    {
        return AIBusinessAdvice::where('id', $adviceId)
            ->update(['is_read' => true]);
    }

    /**
     * Get learning status for a business
     */
    public function getLearningStatus($businessId)
    {
        $cache = AILearningCache::where('business_id', $businessId)
            ->orderBy('created_at', 'desc')
            ->first();

        $settings = AILearningSettings::where('business_id', $businessId)->first();

        return [
            'learning_active' => $settings ? $settings->automated_learning_enabled : false,
            'last_learned' => $cache ? $cache->created_at : null,
            'insights_count' => $cache ? count(json_decode($cache->learned_data, true)) : 0,
            'settings' => $settings
        ];
    }

    /**
     * Update learning settings
     */
    public function updateLearningSettings($businessId, $settings)
    {
        return AILearningSettings::updateOrCreate(
            ['business_id' => $businessId],
            $settings
        );
    }

    /**
     * Generate advice based on business performance
     */
    public function generatePerformanceAdvice($businessId)
    {
        try {
            $business = Business::find($businessId);
            $performance = $this->getBusinessPerformance($businessId);

            $advice = $this->analyzePerformance($performance, $business);

            // Store advice
            AIBusinessAdvice::create([
                'business_id' => $businessId,
                'advice_type' => 'performance_optimization',
                'priority' => $advice['priority'],
                'title' => $advice['title'],
                'description' => $advice['description'],
                'action_items' => json_encode($advice['action_items']),
                'expected_impact' => $advice['expected_impact'],
                'advice_data' => json_encode($advice)
            ]);

            return $advice;

        } catch (\Exception $e) {
            Log::error("Error generating performance advice: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Get business performance data
     */
    private function getBusinessPerformance($businessId)
    {
        $performance = DB::select("
            SELECT 
                COUNT(DISTINCT o.id) as total_orders,
                SUM(CASE WHEN o.status = 'completed' THEN o.total_amount ELSE 0 END) as revenue,
                COUNT(DISTINCT c.id) as total_customers,
                AVG(CASE WHEN o.status = 'completed' THEN o.total_amount ELSE NULL END) as avg_order_value,
                COUNT(DISTINCT p.id) as total_products,
                COUNT(DISTINCT s.id) as total_services
            FROM businesses b
            LEFT JOIN orders o ON b.id = o.business_id
            LEFT JOIN customers c ON b.id = c.business_id
            LEFT JOIN products p ON b.id = p.business_id
            LEFT JOIN services s ON b.id = s.business_id
            WHERE b.id = ?
        ", [$businessId]);

        return $performance[0] ?? null;
    }

    /**
     * Analyze business performance and generate advice
     */
    private function analyzePerformance($performance, $business)
    {
        $advice = [
            'priority' => 'medium',
            'title' => '',
            'description' => '',
            'action_items' => [],
            'expected_impact' => ''
        ];

        if (!$performance) {
            $advice = [
                'priority' => 'high',
                'title' => 'Start Recording Your Business Data',
                'description' => 'No business data found. Start recording sales, products, and services to get personalized advice.',
                'action_items' => [
                    'Add your first product or service',
                    'Record your first sale',
                    'Create customer profiles'
                ],
                'expected_impact' => 'Enable data-driven insights and recommendations'
            ];
        } elseif ($performance->revenue == 0) {
            $advice = [
                'priority' => 'high',
                'title' => 'Start Recording Sales',
                'description' => 'No sales recorded yet. Start tracking your sales to get personalized business advice.',
                'action_items' => [
                    'Record your first sale',
                    'Add products and services',
                    'Create customer profiles'
                ],
                'expected_impact' => 'Enable revenue tracking and insights'
            ];
        } elseif ($performance->total_orders < 5) {
            $advice = [
                'priority' => 'high',
                'title' => 'Focus on Customer Acquisition',
                'description' => 'You have few orders. Focus on attracting more customers to grow your business.',
                'action_items' => [
                    'Launch marketing campaigns',
                    'Offer first-time customer discounts',
                    'Improve your online presence',
                    'Network with potential customers'
                ],
                'expected_impact' => 'Increase customer base by 50%'
            ];
        } elseif ($performance->avg_order_value < 50) {
            $advice = [
                'priority' => 'medium',
                'title' => 'Increase Average Order Value',
                'description' => 'Your average order value is low. Consider upselling and bundling strategies.',
                'action_items' => [
                    'Create product bundles',
                    'Implement upselling techniques',
                    'Offer premium services',
                    'Add complementary products'
                ],
                'expected_impact' => 'Increase revenue by 20-30%'
            ];
        } else {
            $advice = [
                'priority' => 'low',
                'title' => 'Great Performance!',
                'description' => 'Your business is performing well. Consider expanding your offerings.',
                'action_items' => [
                    'Add new products or services',
                    'Explore new markets',
                    'Consider franchising opportunities'
                ],
                'expected_impact' => 'Further business growth'
            ];
        }

        return $advice;
    }

    /**
     * Get trending topics for business type
     */
    public function getTrendingTopics($businessType)
    {
        // This would integrate with external APIs for real trending data
        $trends = [
            'retail' => ['e-commerce', 'contactless payments', 'local shopping'],
            'service' => ['remote services', 'digital transformation', 'consulting'],
            'restaurant' => ['delivery services', 'healthy options', 'local ingredients'],
            'salon' => ['organic products', 'appointment booking', 'wellness services'],
            'barbershop' => ['grooming trends', 'men wellness', 'appointment systems']
        ];

        return $trends[$businessType] ?? [];
    }

    /**
     * Get competitor insights
     */
    public function getCompetitorInsights($businessId)
    {
        $business = Business::find($businessId);
        $cache = AILearningCache::where('business_id', $businessId)->first();

        if ($cache) {
            $data = json_decode($cache->learned_data, true);
            return $data['competitor_insights'] ?? [];
        }

        return [];
    }

    /**
     * Start automated learning system
     */
    public function startAutomatedSystem()
    {
        try {
            $command = "cd " . base_path('ai_models') . " && python automated_learning_system.py --start-daemon";
            exec($command . " > /dev/null 2>&1 &");

            Log::info("Automated learning system started");
            return true;

        } catch (\Exception $e) {
            Log::error("Error starting automated system: " . $e->getMessage());
            return false;
        }
    }
}
