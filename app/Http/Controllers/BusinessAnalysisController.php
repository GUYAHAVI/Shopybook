<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ClaudeAPIService;
use Illuminate\Support\Facades\DB;

class BusinessAnalysisController extends Controller
{
    protected $claudeService;

    public function __construct(ClaudeAPIService $claudeService)
    {
        $this->claudeService = $claudeService;
    }

    /**
     * Display the business analysis dashboard
     */
    public function index()
    {
        $business = auth()->user()->business;
        
        // Get analytics data for the dashboard
        $analytics = $this->getAnalyticsData($business);
        
        return view('business.analysis.index', compact('analytics'));
    }

    /**
     * Generate AI-powered business analysis
     */
    public function generateAnalysis(Request $request)
    {
        $type = $request->get('type', 'general');
        $business = auth()->user()->business;
        
        try {
            // Get business data based on analysis type
            $businessData = $this->getBusinessDataForAnalysis($business, $type);
            
            // Generate analysis using Claude AI service with industry comparison
            $analysis = $this->claudeService->analyzeBusinessData($businessData);
            
            return response()->json([
                'success' => true,
                'analysis' => $analysis,
                'type' => $type
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate analysis: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display financial report
     */
    public function financialReport()
    {
        $business = auth()->user()->business;
        
        // Get financial data
        $financialData = $this->getFinancialData($business);
        
        // Generate AI financial analysis report using Claude
        $report = $this->generateFinancialReportWithClaude($business, $financialData);
        
        return view('business.analysis.financial', compact('financialData', 'report'));
    }

    /**
     * Generate industry comparison analysis
     */
    public function compareWithIndustry(Request $request)
    {
        $business = auth()->user()->business;
        
        try {
            // Get comprehensive business data
            $businessData = $this->getBusinessDataForAnalysis($business, 'general');
            
            // Generate industry comparison using Claude
            $comparison = $this->claudeService->compareWithIndustry(
                $businessData, 
                $business->business_type ?? 'retail'
            );
            
            return response()->json([
                'success' => true,
                'comparison' => $comparison
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate comparison: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get analytics data for the dashboard
     */
    private function getAnalyticsData($business)
    {
        $currentMonth = now()->startOfMonth();
        $lastMonth = now()->subMonth()->startOfMonth();
        
        // Product Sales analytics - ensure we're only getting data for this business
        $totalSales = $business->orders()
            ->where('status', 'completed')
            ->sum('total_amount');
            
        $monthlySales = $business->orders()
            ->where('status', 'completed')
            ->where('created_at', '>=', $currentMonth)
            ->sum('total_amount');
            
        $totalOrders = $business->orders()
            ->where('status', 'completed')
            ->count();
            
        $averageOrderValue = $totalOrders > 0 ? $totalSales / $totalOrders : 0;
        
        // Service analytics
        $totalServiceRevenue = \App\Models\ServiceBooking::where('business_id', $business->id)
            ->where('payment_status', 'paid')
            ->sum('final_amount');
            
        $monthlyServiceRevenue = \App\Models\ServiceBooking::where('business_id', $business->id)
            ->where('payment_status', 'paid')
            ->where('created_at', '>=', $currentMonth)
            ->sum('final_amount');
            
        $totalServiceBookings = \App\Models\ServiceBooking::where('business_id', $business->id)
            ->where('payment_status', 'paid')
            ->count();
            
        $averageServiceValue = $totalServiceBookings > 0 ? $totalServiceRevenue / $totalServiceBookings : 0;
        
        // Combined metrics
        $totalRevenue = $totalSales + $totalServiceRevenue;
        $monthlyRevenue = $monthlySales + $monthlyServiceRevenue;
        
        // Products analytics
        $totalProducts = $business->products()->count();
        $lowStockItems = $business->products()
            ->whereColumn('stock_quantity', '<=', 'low_stock_threshold')
            ->where('stock_quantity', '>', 0)
            ->count();
        
        // Services analytics
        $totalServices = $business->services()->count();
        $activeServices = $business->services()->where('is_active', true)->count();
        
        // Customers analytics (combined for products and services)
        $totalCustomers = $business->customers()->count();
        $newCustomers = $business->customers()
            ->where('created_at', '>=', $currentMonth)
            ->count();
        
        return [
            'sales' => [
                'total' => number_format($totalSales, 2),
                'monthly' => number_format($monthlySales, 2),
                'orders' => $totalOrders,
                'average' => number_format($averageOrderValue, 2)
            ],
            'services' => [
                'total_revenue' => number_format($totalServiceRevenue, 2),
                'monthly_revenue' => number_format($monthlyServiceRevenue, 2),
                'bookings' => $totalServiceBookings,
                'average' => number_format($averageServiceValue, 2)
            ],
            'combined' => [
                'total_revenue' => number_format($totalRevenue, 2),
                'monthly_revenue' => number_format($monthlyRevenue, 2)
            ],
            'products' => [
                'total' => $totalProducts,
                'low_stock' => $lowStockItems
            ],
            'services_list' => [
                'total' => $totalServices,
                'active' => $activeServices
            ],
            'customers' => [
                'total' => $totalCustomers,
                'new' => $newCustomers
            ]
        ];
    }

    /**
     * Get business data for AI analysis based on type
     */
    private function getBusinessDataForAnalysis($business, $type)
    {
        $data = [
            'business_name' => $business->name,
            'business_type' => $business->business_type,
            'analysis_type' => $type
        ];

        switch ($type) {
            case 'sales':
                $data['sales_data'] = $this->getSalesData($business);
                break;
                
            case 'products':
                $data['products_data'] = $this->getProductsData($business);
                break;
                
            case 'services':
                $data['services_data'] = $this->getServicesData($business);
                break;
                
            case 'customers':
                $data['customers_data'] = $this->getCustomersData($business);
                break;
                
            case 'financial':
                $data['financial_data'] = $this->getFinancialData($business);
                break;
                
            default:
                // General analysis includes all data
                $data['sales_data'] = $this->getSalesData($business);
                $data['products_data'] = $this->getProductsData($business);
                $data['services_data'] = $this->getServicesData($business);
                $data['customers_data'] = $this->getCustomersData($business);
                break;
        }

        return $data;
    }

    /**
     * Get sales data for analysis
     */
    private function getSalesData($business)
    {
        $last30Days = now()->subDays(30);
        
        $totalSales = $business->orders()
            ->where('status', 'completed')
            ->sum('total_amount');
        $totalOrders = $business->orders()
            ->where('status', 'completed')
            ->count();
        $recentSales = $business->orders()
            ->where('status', 'completed')
            ->where('created_at', '>=', $last30Days)
            ->sum('total_amount');
        $averageOrderValue = $business->orders()
            ->where('status', 'completed')
            ->avg('total_amount');
        
        return [
            'total_sales' => $totalSales,
            'total_orders' => $totalOrders,
            'recent_sales' => $recentSales,
            'average_order_value' => $averageOrderValue,
            'top_selling_products' => $business->products()
                ->withCount(['orderItems as total_sold' => function($query) {
                    $query->select(DB::raw('sum(quantity)'));
                }])
                ->orderByDesc('total_sold')
                ->limit(5)
                ->get()
                ->map(function($product) {
                    return [
                        'name' => $product->name,
                        'quantity_sold' => $product->total_sold ?? 0
                    ];
                })
        ];
    }

    /**
     * Get products data for analysis
     */
    private function getProductsData($business)
    {
        return [
            'total_products' => $business->products()->count(),
            'low_stock_items' => $business->products()
                ->whereColumn('stock_quantity', '<=', 'low_stock_threshold')
                ->where('stock_quantity', '>', 0)
                ->count(),
            'out_of_stock' => $business->products()
                ->where('stock_quantity', '<=', 0)
                ->count(),
            'categories' => $business->products()
                ->select('category')
                ->distinct()
                ->whereNotNull('category')
                ->pluck('category'),
            'average_price' => $business->products()->avg('price'),
            'inventory_value' => $business->products()
                ->selectRaw('sum(stock_quantity * cost_price) as value')
                ->value('value') ?? 0
        ];
    }

    /**
     * Get services data for analysis
     */
    private function getServicesData($business)
    {
        $totalServices = $business->services()->count();
        $activeServices = $business->services()->where('is_active', true)->count();
        
        $totalServiceRevenue = \App\Models\ServiceBooking::where('business_id', $business->id)
            ->where('payment_status', 'paid')
            ->sum('final_amount');
        
        $totalServiceBookings = \App\Models\ServiceBooking::where('business_id', $business->id)
            ->where('payment_status', 'paid')
            ->count();
        
        $averageServiceValue = $totalServiceBookings > 0 ? $totalServiceRevenue / $totalServiceBookings : 0;
        
        $topServices = \App\Models\ServiceItem::whereHas('serviceBooking', function($query) use ($business) {
            $query->where('business_id', $business->id)
                  ->where('payment_status', 'paid');
        })
        ->with('service')
        ->selectRaw('service_id, COUNT(*) as booking_count, SUM(amount) as total_revenue')
        ->groupBy('service_id')
        ->orderByDesc('total_revenue')
        ->limit(5)
        ->get();
        
        $staffPerformance = \App\Models\ServiceItem::whereHas('serviceBooking', function($query) use ($business) {
            $query->where('business_id', $business->id)
                  ->where('payment_status', 'paid');
        })
        ->with('staff')
        ->selectRaw('staff_id, COUNT(*) as service_count, SUM(amount) as total_revenue')
        ->groupBy('staff_id')
        ->orderByDesc('total_revenue')
        ->limit(5)
        ->get();
        
        return [
            'total_services' => $totalServices,
            'active_services' => $activeServices,
            'total_revenue' => $totalServiceRevenue,
            'total_bookings' => $totalServiceBookings,
            'average_service_value' => $averageServiceValue,
            'top_services' => $topServices,
            'staff_performance' => $staffPerformance
        ];
    }

    /**
     * Get customers data for analysis
     */
    private function getCustomersData($business)
    {
        $last30Days = now()->subDays(30);
        
        return [
            'total_customers' => $business->customers()->count(),
            'new_customers_30_days' => $business->customers()
                ->where('created_at', '>=', $last30Days)
                ->count(),
            'customers_with_orders' => $business->customers()
                ->whereHas('orders')
                ->count(),
            'average_orders_per_customer' => $business->customers()
                ->withCount('orders')
                ->avg('orders_count') ?? 0,
            'top_customers' => $business->customers()
                ->withCount('orders')
                ->withSum('orders', 'total_amount')
                ->orderByDesc('orders_sum_total_amount')
                ->limit(5)
                ->get()
                ->map(function($customer) {
                    return [
                        'name' => $customer->name,
                        'total_spent' => $customer->orders_sum_total_amount ?? 0,
                        'order_count' => $customer->orders_count ?? 0
                    ];
                })
        ];
    }

    /**
     * Get financial data for analysis
     */
    private function getFinancialData($business)
    {
        $totalRevenue = $business->orders()
            ->where('status', 'completed')
            ->sum('total_amount');
            
        // Calculate inventory purchase costs (actual money spent on receiving stock)
        $inventoryPurchaseCosts = $business->total_inventory_costs;
            
        // Calculate business expenses (rent, utilities, etc.)
        $businessExpenses = $business->costs()
            ->where('type', '!=', 'salary')
            ->sum('amount') ?? 0;
            
        // Calculate total salary costs using the business model method
        $totalSalaryCosts = $business->total_salary_costs;
            
        // Total costs = inventory purchases + business expenses + salary costs
        $totalCosts = $inventoryPurchaseCosts + $businessExpenses + $totalSalaryCosts;
            
        $profit = $totalRevenue - $totalCosts;
        $profitMargin = $totalRevenue > 0 ? ($profit / $totalRevenue) * 100 : 0;

        // Monthly revenue for the last 6 months
        $monthlyRevenue = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $revenue = $business->orders()
                ->where('status', 'completed')
                ->whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->sum('total_amount');
                
            $monthlyRevenue[] = [
                'month' => $month->format('M Y'),
                'revenue' => $revenue
            ];
        }

        // Top performing products by revenue
        $topProducts = $business->orders()
            ->where('status', 'completed')
            ->join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->selectRaw('products.name, products.category, sum(order_items.total) as total_sales, sum(order_items.quantity * products.cost_price) as stock_value')
            ->groupBy('products.id', 'products.name', 'products.category')
            ->orderByDesc('total_sales')
            ->limit(5)
            ->get()
            ->map(function($product) {
                return [
                    'name' => $product->name,
                    'category' => $product->category ?? 'General',
                    'revenue' => number_format($product->total_sales, 0),
                    'sales' => $product->total_sales > 0 ? number_format($product->total_sales / 1000, 0) : 0
                ];
            });

        // Calculate insights
        $monthlyRevenueCount = count($monthlyRevenue);
        $currentMonthRevenue = $monthlyRevenueCount > 0 ? ($monthlyRevenue[$monthlyRevenueCount - 1]['revenue'] ?? 0) : 0;
        $previousMonthRevenue = $monthlyRevenueCount > 1 ? ($monthlyRevenue[$monthlyRevenueCount - 2]['revenue'] ?? 0) : 0;
        $revenueGrowth = $previousMonthRevenue > 0 ? (($currentMonthRevenue - $previousMonthRevenue) / $previousMonthRevenue) * 100 : 0;

        // Calculate cost trends (simplified)
        $costReduction = 5; // Placeholder - could be calculated from historical data
        $marginImprovement = $profitMargin > 15 ? 2.5 : 1.5; // Placeholder
        $cashFlowMonths = 3; // Placeholder

        return [
            'summary' => [
                'revenue' => number_format($totalRevenue, 2),
                'costs' => number_format($totalCosts, 2),
                'inventory_purchase_costs' => number_format($inventoryPurchaseCosts, 2),
                'business_expenses' => number_format($businessExpenses, 2),
                'salary_costs' => number_format($totalSalaryCosts, 2),
                'profit' => number_format($profit, 2),
                'profit_margin' => number_format($profitMargin, 1) . '%'
            ],
            'monthly_revenue' => $monthlyRevenue,
            'top_products' => $topProducts,
            'insights' => [
                'revenue_growth' => number_format($revenueGrowth, 1),
                'cost_reduction' => $costReduction,
                'margin_improvement' => $marginImprovement,
                'cash_flow_months' => $cashFlowMonths
            ]
        ];
    }

    /**
     * Generate AI financial analysis report
     */
    private function generateFinancialReport($business, $financialData)
    {
        $totalRevenue = (float) str_replace(',', '', $financialData['summary']['revenue']);
        $totalCosts = (float) str_replace(',', '', $financialData['summary']['costs']);
        $profit = (float) str_replace(',', '', $financialData['summary']['profit']);
        $profitMargin = (float) str_replace('%', '', $financialData['summary']['profit_margin']);

        // Calculate growth rates
        $monthlyRevenueCount = count($financialData['monthly_revenue']);
        $currentMonthRevenue = $monthlyRevenueCount > 0 ? ($financialData['monthly_revenue'][$monthlyRevenueCount - 1]['revenue'] ?? 0) : 0;
        $previousMonthRevenue = $monthlyRevenueCount > 1 ? ($financialData['monthly_revenue'][$monthlyRevenueCount - 2]['revenue'] ?? 0) : 0;
        $revenueGrowth = $previousMonthRevenue > 0 ? (($currentMonthRevenue - $previousMonthRevenue) / $previousMonthRevenue) * 100 : 0;

        // Generate insights
        $insights = [];
        
        if ($profitMargin > 20) {
            $insights[] = "Excellent profit margin of {$profitMargin}% indicates strong pricing strategy and cost control.";
        } elseif ($profitMargin > 10) {
            $insights[] = "Good profit margin of {$profitMargin}% shows healthy business operations.";
        } else {
            $insights[] = "Profit margin of {$profitMargin}% suggests room for improvement in pricing or cost management.";
        }

        if ($revenueGrowth > 10) {
            $insights[] = "Strong revenue growth of " . number_format($revenueGrowth, 1) . "% this month indicates expanding market presence.";
        } elseif ($revenueGrowth > 0) {
            $insights[] = "Moderate revenue growth of " . number_format($revenueGrowth, 1) . "% shows steady business development.";
        } else {
            $insights[] = "Revenue decline of " . number_format(abs($revenueGrowth), 1) . "% this month requires attention to sales strategies.";
        }

        if ($totalRevenue > 100000) {
            $insights[] = "High revenue volume of KSh " . number_format($totalRevenue, 0) . " demonstrates strong market position.";
        } elseif ($totalRevenue > 50000) {
            $insights[] = "Moderate revenue of KSh " . number_format($totalRevenue, 0) . " shows stable business performance.";
        } else {
            $insights[] = "Revenue of KSh " . number_format($totalRevenue, 0) . " indicates early-stage business with growth potential.";
        }

        // Top product analysis
        if (!empty($financialData['top_products'])) {
            $topProduct = $financialData['top_products'][0] ?? null;
            if ($topProduct) {
                $insights[] = "Top performing product '{$topProduct['name']}' generated KSh {$topProduct['revenue']} in revenue.";
            }
        }

        // Recommendations
        $recommendations = [];
        
        if ($profitMargin < 15) {
            $recommendations[] = "Consider reviewing pricing strategies to improve profit margins.";
        }
        
        if ($revenueGrowth < 5) {
            $recommendations[] = "Focus on marketing and sales strategies to boost revenue growth.";
        }
        
        if (empty($recommendations)) {
            $recommendations[] = "Continue current business strategies as they are performing well.";
        }

        // Get cost breakdown
        $inventoryPurchaseCosts = (float) str_replace(',', '', $financialData['summary']['inventory_purchase_costs']);
        $businessExpenses = (float) str_replace(',', '', $financialData['summary']['business_expenses']);
        $salaryCosts = (float) str_replace(',', '', $financialData['summary']['salary_costs']);
        
        // Build the report
        $report = "FINANCIAL ANALYSIS REPORT\n\n";
        $report .= "BUSINESS PERFORMANCE SUMMARY\n";
        $report .= "Total Revenue: KSh " . number_format($totalRevenue, 0) . "\n";
        $report .= "Total Costs: KSh " . number_format($totalCosts, 0) . "\n";
        $report .= "  - Inventory Purchase Costs: KSh " . number_format($inventoryPurchaseCosts, 0) . "\n";
        $report .= "  - Business Expenses: KSh " . number_format($businessExpenses, 0) . "\n";
        $report .= "  - Staff Salaries: KSh " . number_format($salaryCosts, 0) . "\n";
        $report .= "Net Profit: KSh " . number_format($profit, 0) . "\n";
        $report .= "Profit Margin: {$profitMargin}%\n";
        $report .= "Monthly Growth: " . number_format($revenueGrowth, 1) . "%\n\n";
        
        $report .= "KEY INSIGHTS\n";
        foreach ($insights as $insight) {
            $report .= "• {$insight}\n";
        }
        
        $report .= "\nRECOMMENDATIONS\n";
        foreach ($recommendations as $recommendation) {
            $report .= "• {$recommendation}\n";
        }

        return $report;
    }

    /**
     * Generate AI financial analysis report using Claude
     */
    private function generateFinancialReportWithClaude($business, $financialData)
    {
        try {
            // Prepare comprehensive financial data for Claude
            $financialAnalysisData = [
                'business_name' => $business->name,
                'business_type' => $business->business_type ?? 'retail',
                'financial_data' => $financialData,
                'analysis_type' => 'financial'
            ];

            // Generate detailed financial analysis using Claude
            $claudeReport = $this->claudeService->analyzeBusinessData($financialAnalysisData);
            
            return $claudeReport;
            
        } catch (\Exception $e) {
            \Log::error('Claude Financial Report Error: ' . $e->getMessage());
            
            // Fallback to basic report if Claude fails
            return $this->generateFinancialReport($business, $financialData);
        }
    }
}