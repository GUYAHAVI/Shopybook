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

class LaravelOnlyAIService
{
    /**
     * Generate business insights using only Laravel/PHP
     */
    public function generateBusinessInsights($businessId)
    {
        try {
            $business = Business::find($businessId);
            if (!$business) {
                return ['error' => 'Business not found'];
            }

            $insights = [
                'sales_analysis' => $this->analyzeSales($businessId),
                'customer_insights' => $this->analyzeCustomers($businessId),
                'product_performance' => $this->analyzeProducts($businessId),
                'recommendations' => $this->generateRecommendations($businessId, $business),
                'market_trends' => $this->getMarketTrends(),
                'business_health' => $this->assessBusinessHealth($businessId)
            ];

            return $insights;

        } catch (\Exception $e) {
            Log::error('Error generating business insights: ' . $e->getMessage());
            return ['error' => 'Unable to generate insights'];
        }
    }

    /**
     * Analyze sales data
     */
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

        // Calculate trend
        $recentOrders = $orders->where('created_at', '>=', Carbon::now()->subDays(7));
        $previousOrders = Order::where('business_id', $businessId)
            ->whereBetween('created_at', [
                Carbon::now()->subDays(14),
                Carbon::now()->subDays(7)
            ])
            ->get();

        $recentTotal = $recentOrders->sum('total_amount');
        $previousTotal = $previousOrders->sum('total_amount');
        
        $trend = $previousTotal > 0 
            ? (($recentTotal - $previousTotal) / $previousTotal) * 100 
            : 0;

        return [
            'total_sales' => $totalSales,
            'avg_order_value' => $avgOrderValue,
            'total_orders' => $totalOrders,
            'trend' => $trend > 0 ? "Sales up {$trend}%" : ($trend < 0 ? "Sales down " . abs($trend) . "%" : "Sales stable")
        ];
    }

    /**
     * Analyze customer data
     */
    protected function analyzeCustomers($businessId)
    {
        $customers = Customer::where('business_id', $businessId)->get();
        $orders = Order::where('business_id', $businessId)->get();

        if ($customers->isEmpty()) {
            return [
                'total_customers' => 0,
                'repeat_customers' => 0,
                'avg_orders_per_customer' => 0
            ];
        }

        // Count repeat customers
        $customerOrderCounts = $orders->groupBy('customer_id')->map->count();
        $repeatCustomers = $customerOrderCounts->filter(function($count) {
            return $count > 1;
        })->count();

        return [
            'total_customers' => $customers->count(),
            'repeat_customers' => $repeatCustomers,
            'avg_orders_per_customer' => $orders->count() / $customers->count()
        ];
    }

    /**
     * Analyze product performance
     */
    protected function analyzeProducts($businessId)
    {
        $products = Product::where('business_id', $businessId)->get();
        
        if ($products->isEmpty()) {
            return [
                'total_products' => 0,
                'low_stock_products' => 0,
                'top_performing' => null
            ];
        }

        $lowStockProducts = $products->filter(function($product) {
            return $product->stock_quantity < 10;
        })->count();

        // Find top performing product (highest price * stock)
        $topProduct = $products->sortByDesc(function($product) {
            return $product->price * $product->stock_quantity;
        })->first();

        return [
            'total_products' => $products->count(),
            'low_stock_products' => $lowStockProducts,
            'top_performing' => $topProduct ? $topProduct->name : null
        ];
    }

    /**
     * Generate business recommendations
     */
    protected function generateRecommendations($businessId, $business)
    {
        $recommendations = [];

        // Sales recommendations
        $salesData = $this->analyzeSales($businessId);
        if ($salesData['total_sales'] == 0) {
            $recommendations[] = "Start recording your sales to get detailed insights";
        } elseif ($salesData['trend'] < 0) {
            $recommendations[] = "Focus on marketing and customer retention to improve sales";
        }

        // Customer recommendations
        $customerData = $this->analyzeCustomers($businessId);
        if ($customerData['repeat_customers'] < $customerData['total_customers'] * 0.3) {
            $recommendations[] = "Implement customer loyalty programs to increase repeat business";
        }

        // Product recommendations
        $productData = $this->analyzeProducts($businessId);
        if ($productData['low_stock_products'] > 0) {
            $recommendations[] = "Restock products with low inventory to avoid stockouts";
        }

        // Business type specific recommendations
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
        }

        return $recommendations;
    }

    /**
     * Get market trends (simplified)
     */
    protected function getMarketTrends()
    {
        return [
            'digital_transformation' => 'Businesses are increasingly moving online',
            'customer_experience' => 'Personalization and convenience are key drivers',
            'sustainability' => 'Eco-friendly practices are becoming more important',
            'data_driven_decisions' => 'Analytics and insights are crucial for growth'
        ];
    }

    /**
     * Assess overall business health
     */
    protected function assessBusinessHealth($businessId)
    {
        $salesData = $this->analyzeSales($businessId);
        $customerData = $this->analyzeCustomers($businessId);
        $productData = $this->analyzeProducts($businessId);

        $score = 0;
        $factors = [];

        // Sales health
        if ($salesData['total_sales'] > 0) {
            $score += 25;
            $factors[] = 'Sales activity detected';
        }

        // Customer health
        if ($customerData['repeat_customers'] > 0) {
            $score += 25;
            $factors[] = 'Repeat customers present';
        }

        // Product health
        if ($productData['total_products'] > 0) {
            $score += 25;
            $factors[] = 'Product catalog established';
        }

        // Stock health
        if ($productData['low_stock_products'] == 0) {
            $score += 25;
            $factors[] = 'Good inventory levels';
        }

        $health = $score >= 75 ? 'Excellent' : ($score >= 50 ? 'Good' : ($score >= 25 ? 'Fair' : 'Needs Attention'));

        return [
            'score' => $score,
            'health' => $health,
            'factors' => $factors
        ];
    }

    /**
     * Process AI chat message (Laravel-only version)
     */
    public function processChatMessage($message, $businessId = null)
    {
        $messageLower = strtolower($message);
        
        if (preg_match('/\b(sales|revenue|profit|income|earnings)\b/', $messageLower)) {
            return $this->generateSalesResponse($businessId);
        } elseif (preg_match('/\b(market|trend|competitor|industry)\b/', $messageLower)) {
            return $this->generateMarketResponse();
        } elseif (preg_match('/\b(recommend|suggestion|advice|improve)\b/', $messageLower)) {
            return $this->generateRecommendationResponse($businessId);
        } else {
            return $this->generateGeneralResponse();
        }
    }

    protected function generateSalesResponse($businessId)
    {
        $salesData = $this->analyzeSales($businessId);
        
        if ($salesData['total_sales'] == 0) {
            return "I don't have enough sales data yet. Start recording your sales to get detailed insights.";
        }

        return "Your sales performance shows total sales of $" . number_format($salesData['total_sales'], 2) . 
               " with average order value of $" . number_format($salesData['avg_order_value'], 2) . 
               ". {$salesData['trend']}";
    }

    protected function generateMarketResponse()
    {
        $trends = $this->getMarketTrends();
        return "Current market trends include: " . implode(', ', array_values($trends));
    }

    protected function generateRecommendationResponse($businessId)
    {
        $business = Business::find($businessId);
        $recommendations = $this->generateRecommendations($businessId, $business);
        
        return "Based on your business data, I recommend: " . implode('; ', $recommendations);
    }

    protected function generateGeneralResponse()
    {
        return "I can help you analyze your sales, customers, and provide business recommendations. What would you like to know?";
    }
}

