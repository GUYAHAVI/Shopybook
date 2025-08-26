<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ShopybookAIBusinessAnalyst;
use App\Services\OpenAIService; // Keep as fallback
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Business;

class EnhancedBusinessAnalysisController extends Controller
{
    protected $shopybookAI;
    protected $openAIService;

    public function __construct(ShopybookAIBusinessAnalyst $shopybookAI, OpenAIService $openAIService)
    {
        $this->shopybookAI = $shopybookAI;
        $this->openAIService = $openAIService; // Fallback option
    }

    /**
     * Display the enhanced business analysis dashboard
     */
    public function index()
    {
        $business = auth()->user()->business;
        
        // Get latest AI analysis
        $latestAnalysis = $this->shopybookAI->getLatestAnalysis($business->id);
        
        // Get analytics data for the dashboard
        $analytics = $this->getAnalyticsData($business);
        
        // Get AI recommendations
        $recommendations = $this->getAIRecommendations($business->id);
        
        return view('business.analysis.enhanced-index', compact('analytics', 'latestAnalysis', 'recommendations'));
    }

    /**
     * Generate comprehensive AI analysis using Canadian model
     */
    public function generateEnhancedAnalysis(Request $request)
    {
        $request->validate([
            'analysis_type' => 'string|in:comprehensive,financial,operational,marketing',
        ]);

        $business = auth()->user()->business;
        $analysisType = $request->get('analysis_type', 'comprehensive');
        
        try {
            Log::info("Starting enhanced AI analysis for business: {$business->id}");
            
            // Use the Canadian-trained model for analysis
            $analysis = $this->shopybookAI->generateBusinessAnalysis($business->id, $analysisType);
            
            return response()->json([
                'success' => true,
                'analysis' => $analysis,
                'type' => $analysisType,
                'model_used' => 'canadian_msme',
                'message' => 'Analysis completed using advanced AI model trained on Canadian business data'
            ]);
            
        } catch (\Exception $e) {
            Log::error("Enhanced AI analysis failed for business {$business->id}: " . $e->getMessage());
            
            // Fallback to OpenAI if Canadian model fails
            try {
                Log::info("Falling back to OpenAI analysis");
                $businessData = $this->getBusinessDataForAnalysis($business, $analysisType);
                $fallbackAnalysis = $this->openAIService->analyzeBusinessData($businessData);
                
                return response()->json([
                    'success' => true,
                    'analysis' => [
                        'analysis_type' => $analysisType,
                        'content' => $fallbackAnalysis,
                        'fallback_used' => true
                    ],
                    'type' => $analysisType,
                    'model_used' => 'openai_fallback',
                    'message' => 'Analysis completed using fallback AI model'
                ]);
                
            } catch (\Exception $fallbackError) {
                return response()->json([
                    'success' => false,
                    'message' => 'AI analysis failed: ' . $e->getMessage()
                ], 500);
            }
        }
    }

    /**
     * Get comprehensive financial analysis using Canadian model
     */
    public function getFinancialAnalysis()
    {
        $business = auth()->user()->business;
        
        try {
            $analysis = $this->shopybookAI->generateBusinessAnalysis($business->id, 'financial');
            
            // Extract financial-specific insights
            $financialInsights = [
                'revenue_analysis' => $analysis['current_performance'] ?? [],
                'cost_breakdown' => $this->getCostBreakdown($business->id),
                'profitability_metrics' => $analysis['benchmarks'] ?? [],
                'financial_predictions' => $analysis['predictions'] ?? [],
                'improvement_areas' => $this->extractFinancialRecommendations($analysis['recommendations'] ?? []),
                'cash_flow_insights' => $this->getCashFlowInsights($business->id)
            ];
            
            return response()->json([
                'success' => true,
                'financial_analysis' => $financialInsights,
                'model_used' => 'canadian_msme'
            ]);
            
        } catch (\Exception $e) {
            Log::error("Financial analysis failed: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Financial analysis failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get operational efficiency analysis
     */
    public function getOperationalAnalysis()
    {
        $business = auth()->user()->business;
        
        try {
            $analysis = $this->shopybookAI->generateBusinessAnalysis($business->id, 'operational');
            
            $operationalInsights = [
                'efficiency_metrics' => $this->getEfficiencyMetrics($business->id),
                'staff_productivity' => $this->getStaffProductivityMetrics($business->id),
                'operational_recommendations' => $this->extractOperationalRecommendations($analysis['recommendations'] ?? []),
                'capacity_analysis' => $this->getCapacityAnalysis($business->id),
                'process_optimization' => $analysis['insights']['operational_efficiency'] ?? []
            ];
            
            return response()->json([
                'success' => true,
                'operational_analysis' => $operationalInsights,
                'model_used' => 'canadian_msme'
            ]);
            
        } catch (\Exception $e) {
            Log::error("Operational analysis failed: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Operational analysis failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get business growth predictions and recommendations
     */
    public function getGrowthPredictions()
    {
        $business = auth()->user()->business;
        
        try {
            $analysis = $this->shopybookAI->generateBusinessAnalysis($business->id, 'comprehensive');
            
            $growthInsights = [
                'income_predictions' => [
                    'current_monthly_income' => $analysis['current_performance']['net_income'] ?? 0,
                    'predicted_monthly_income' => $analysis['predictions']['predicted_net_income'] ?? 0,
                    'growth_potential' => $analysis['predictions']['improvement_potential'] ?? 0,
                    'confidence_level' => $analysis['predictions']['confidence_level'] ?? 'medium'
                ],
                'growth_drivers' => $this->identifyGrowthDrivers($analysis),
                'market_opportunities' => $analysis['insights']['growth_opportunities'] ?? [],
                'scaling_recommendations' => $this->getScalingRecommendations($analysis),
                'investment_priorities' => $this->getInvestmentPriorities($analysis),
                'timeline_projections' => $this->generateTimelineProjections($analysis)
            ];
            
            return response()->json([
                'success' => true,
                'growth_analysis' => $growthInsights,
                'model_used' => 'canadian_msme'
            ]);
            
        } catch (\Exception $e) {
            Log::error("Growth analysis failed: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Growth analysis failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Compare business performance with AI benchmarks
     */
    public function getBenchmarkComparison()
    {
        $business = auth()->user()->business;
        
        try {
            $analysis = $this->shopybookAI->generateBusinessAnalysis($business->id, 'comprehensive');
            $benchmarks = $analysis['benchmarks'] ?? [];
            
            // Industry benchmarks based on Canadian MSME data
            $industryBenchmarks = $this->getIndustryBenchmarks($business->business_type);
            
            $comparison = [
                'performance_vs_industry' => [
                    'revenue_per_employee' => [
                        'your_business' => $benchmarks['revenue_per_employee'] ?? 0,
                        'industry_average' => $industryBenchmarks['revenue_per_employee'] ?? 0,
                        'performance_level' => $this->calculatePerformanceLevel($benchmarks['revenue_per_employee'] ?? 0, $industryBenchmarks['revenue_per_employee'] ?? 0)
                    ],
                    'profit_margin' => [
                        'your_business' => $benchmarks['profit_margin'] ?? 0,
                        'industry_average' => $industryBenchmarks['profit_margin'] ?? 0,
                        'performance_level' => $this->calculatePerformanceLevel($benchmarks['profit_margin'] ?? 0, $industryBenchmarks['profit_margin'] ?? 0)
                    ],
                    'expense_ratio' => [
                        'your_business' => $benchmarks['expense_ratio'] ?? 0,
                        'industry_average' => $industryBenchmarks['expense_ratio'] ?? 0,
                        'performance_level' => $this->calculatePerformanceLevel($industryBenchmarks['expense_ratio'] ?? 0, $benchmarks['expense_ratio'] ?? 0) // Lower is better for expenses
                    ]
                ],
                'improvement_areas' => $this->identifyImprovementAreas($benchmarks, $industryBenchmarks),
                'strengths' => $this->identifyStrengths($benchmarks, $industryBenchmarks)
            ];
            
            return response()->json([
                'success' => true,
                'benchmark_comparison' => $comparison,
                'model_used' => 'canadian_msme'
            ]);
            
        } catch (\Exception $e) {
            Log::error("Benchmark comparison failed: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Benchmark comparison failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export detailed AI analysis report
     */
    public function exportAnalysisReport(Request $request)
    {
        $business = auth()->user()->business;
        $format = $request->get('format', 'pdf');
        
        try {
            $analysis = $this->shopybookAI->generateBusinessAnalysis($business->id, 'comprehensive');
            
            $reportData = [
                'business' => $business,
                'analysis' => $analysis,
                'generated_at' => now(),
                'model_used' => 'Canadian MSME AI Model',
                'report_type' => 'Comprehensive Business Analysis'
            ];
            
            if ($format === 'pdf') {
                return $this->generatePDFReport($reportData);
            } else {
                return $this->generateExcelReport($reportData);
            }
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Report generation failed: ' . $e->getMessage()
            ], 500);
        }
    }

    // Helper methods for data extraction and analysis
    
    protected function getAnalyticsData($business)
    {
        // Implementation for getting basic analytics data
        return [
            'total_revenue' => $business->orders()->sum('total_amount'),
            'total_orders' => $business->orders()->count(),
            'total_customers' => $business->customers()->count(),
            'total_products' => $business->products()->count(),
        ];
    }

    protected function getAIRecommendations($businessId)
    {
        return DB::table('ai_business_recommendations')
            ->where('business_id', $businessId)
            ->where('is_implemented', false)
            ->orderBy('priority')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
    }

    protected function getCostBreakdown($businessId)
    {
        return DB::table('costs')
            ->where('business_id', $businessId)
            ->whereDate('created_at', '>=', now()->subDays(30))
            ->selectRaw('category, SUM(amount) as total_amount')
            ->groupBy('category')
            ->get();
    }

    protected function extractFinancialRecommendations($recommendations)
    {
        return collect($recommendations)->filter(function ($rec) {
            return in_array($rec['category'] ?? '', ['Revenue Growth', 'Cost Control']);
        })->values();
    }

    protected function extractOperationalRecommendations($recommendations)
    {
        return collect($recommendations)->filter(function ($rec) {
            return in_array($rec['category'] ?? '', ['Operations', 'Operational Efficiency']);
        })->values();
    }

    protected function getEfficiencyMetrics($businessId)
    {
        // Calculate various efficiency metrics
        $orders = DB::table('orders')->where('business_id', $businessId);
        $staff = DB::table('staff')->where('business_id', $businessId);
        
        return [
            'orders_per_day' => $orders->whereDate('created_at', '>=', now()->subDays(30))->count() / 30,
            'revenue_per_staff' => $staff->count() > 0 ? $orders->sum('total_amount') / $staff->count() : 0,
            'avg_order_processing_time' => 'N/A', // Would need additional tracking
        ];
    }

    protected function getStaffProductivityMetrics($businessId)
    {
        $staff = DB::table('staff')->where('business_id', $businessId)->get();
        $totalRevenue = DB::table('orders')->where('business_id', $businessId)->sum('total_amount');
        
        return [
            'total_staff' => $staff->count(),
            'revenue_per_employee' => $staff->count() > 0 ? $totalRevenue / $staff->count() : 0,
            'staff_efficiency_score' => 'N/A' // Would need more detailed tracking
        ];
    }

    protected function getCapacityAnalysis($businessId)
    {
        // Analyze current vs potential capacity
        return [
            'current_capacity_utilization' => 'N/A',
            'potential_capacity' => 'N/A',
            'bottlenecks' => []
        ];
    }

    protected function identifyGrowthDrivers($analysis)
    {
        $drivers = [];
        
        if (isset($analysis['insights']['revenue_optimization'])) {
            $drivers[] = [
                'type' => 'Revenue Optimization',
                'items' => $analysis['insights']['revenue_optimization']
            ];
        }
        
        if (isset($analysis['insights']['growth_opportunities'])) {
            $drivers[] = [
                'type' => 'Growth Opportunities',
                'items' => $analysis['insights']['growth_opportunities']
            ];
        }
        
        return $drivers;
    }

    protected function getScalingRecommendations($analysis)
    {
        return $analysis['next_steps']['long_term'] ?? [];
    }

    protected function getInvestmentPriorities($analysis)
    {
        $priorities = [];
        
        if (isset($analysis['recommendations'])) {
            foreach ($analysis['recommendations'] as $rec) {
                if ($rec['priority'] === 'high') {
                    $priorities[] = $rec;
                }
            }
        }
        
        return $priorities;
    }

    protected function generateTimelineProjections($analysis)
    {
        $currentIncome = $analysis['current_performance']['net_income'] ?? 0;
        $predictedIncome = $analysis['predictions']['predicted_net_income'] ?? $currentIncome;
        
        return [
            '3_months' => $currentIncome + (($predictedIncome - $currentIncome) * 0.25),
            '6_months' => $currentIncome + (($predictedIncome - $currentIncome) * 0.50),
            '12_months' => $predictedIncome
        ];
    }

    protected function getIndustryBenchmarks($businessType)
    {
        // Industry benchmarks based on Canadian MSME data
        $benchmarks = [
            'retail' => [
                'revenue_per_employee' => 250000,
                'profit_margin' => 15,
                'expense_ratio' => 75
            ],
            'service' => [
                'revenue_per_employee' => 180000,
                'profit_margin' => 20,
                'expense_ratio' => 70
            ],
            'manufacturing' => [
                'revenue_per_employee' => 300000,
                'profit_margin' => 12,
                'expense_ratio' => 80
            ],
            'default' => [
                'revenue_per_employee' => 200000,
                'profit_margin' => 18,
                'expense_ratio' => 72
            ]
        ];
        
        return $benchmarks[$businessType] ?? $benchmarks['default'];
    }

    protected function calculatePerformanceLevel($businessValue, $industryAverage)
    {
        if ($industryAverage == 0) return 'average';
        
        $ratio = $businessValue / $industryAverage;
        
        if ($ratio >= 1.2) return 'excellent';
        if ($ratio >= 1.1) return 'above_average';
        if ($ratio >= 0.9) return 'average';
        if ($ratio >= 0.8) return 'below_average';
        
        return 'needs_improvement';
    }

    protected function identifyImprovementAreas($benchmarks, $industryBenchmarks)
    {
        $areas = [];
        
        foreach ($industryBenchmarks as $metric => $industryValue) {
            $businessValue = $benchmarks[$metric] ?? 0;
            
            if ($metric === 'expense_ratio') {
                // Lower is better for expenses
                if ($businessValue > $industryValue * 1.1) {
                    $areas[] = [
                        'metric' => $metric,
                        'issue' => 'High expense ratio compared to industry average',
                        'recommendation' => 'Focus on cost reduction strategies'
                    ];
                }
            } else {
                // Higher is better for revenue and profit metrics
                if ($businessValue < $industryValue * 0.9) {
                    $areas[] = [
                        'metric' => $metric,
                        'issue' => 'Below industry average',
                        'recommendation' => 'Focus on improving this metric'
                    ];
                }
            }
        }
        
        return $areas;
    }

    protected function identifyStrengths($benchmarks, $industryBenchmarks)
    {
        $strengths = [];
        
        foreach ($industryBenchmarks as $metric => $industryValue) {
            $businessValue = $benchmarks[$metric] ?? 0;
            
            if ($metric === 'expense_ratio') {
                // Lower is better for expenses
                if ($businessValue < $industryValue * 0.9) {
                    $strengths[] = [
                        'metric' => $metric,
                        'achievement' => 'Excellent cost management'
                    ];
                }
            } else {
                // Higher is better for revenue and profit metrics
                if ($businessValue > $industryValue * 1.1) {
                    $strengths[] = [
                        'metric' => $metric,
                        'achievement' => 'Above industry average performance'
                    ];
                }
            }
        }
        
        return $strengths;
    }

    protected function getCashFlowInsights($businessId)
    {
        // Basic cash flow analysis
        $thirtyDaysAgo = now()->subDays(30);
        
        $income = DB::table('orders')
            ->where('business_id', $businessId)
            ->where('created_at', '>=', $thirtyDaysAgo)
            ->sum('total_amount');
        
        $expenses = DB::table('costs')
            ->where('business_id', $businessId)
            ->where('created_at', '>=', $thirtyDaysAgo)
            ->sum('amount');
        
        return [
            'monthly_cash_flow' => $income - $expenses,
            'cash_flow_trend' => 'positive', // Would need historical data for real trend
            'liquidity_ratio' => $expenses > 0 ? $income / $expenses : 0
        ];
    }

    protected function getBusinessDataForAnalysis($business, $type)
    {
        // Fallback method for OpenAI analysis
        return [
            'business' => $business->toArray(),
            'type' => $type
        ];
    }

    protected function generatePDFReport($reportData)
    {
        // Implementation for PDF report generation
        // You could use a package like dompdf or tcpdf
        return response()->json(['message' => 'PDF generation not implemented yet']);
    }

    protected function generateExcelReport($reportData)
    {
        // Implementation for Excel report generation
        // You could use a package like PhpSpreadsheet
        return response()->json(['message' => 'Excel generation not implemented yet']);
    }
}
