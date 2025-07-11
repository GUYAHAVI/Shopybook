<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\OpenAIService;
use App\Models\Order;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class BusinessAnalysisController extends Controller
{
    protected $openAIService;

    public function __construct(OpenAIService $openAIService)
    {
        $this->openAIService = $openAIService;
    }

    public function index()
    {
        $business = auth()->user()->business;
        
        // Get basic analytics
        $analytics = $this->getBasicAnalytics($business);
        
        return view('business.analysis.index', compact('analytics'));
    }

    public function generateAnalysis(Request $request)
    {
        $business = auth()->user()->business;
        $analysisType = $request->get('type', 'general');
        
        // Collect business data
        $businessData = $this->collectBusinessData($business, $analysisType);
        
        // Generate AI analysis
        $analysis = $this->openAIService->analyzeBusinessData($businessData);
        
        return response()->json([
            'success' => true,
            'analysis' => $analysis,
            'data' => $businessData
        ]);
    }

    public function financialReport()
    {
        $business = auth()->user()->business;
        $financialData = $this->collectFinancialData($business);
        
        $report = $this->openAIService->generateFinancialReport($financialData);
        
        return view('business.analysis.financial', compact('report', 'financialData'));
    }

    protected function getBasicAnalytics($business)
    {
        $now = Carbon::now();
        $lastMonth = $now->copy()->subMonth();
        
        // Sales Analytics
        $totalSales = Order::where('business_id', $business->id)
            ->where('status', 'completed')
            ->sum('total_amount');
            
        $monthlySales = Order::where('business_id', $business->id)
            ->where('status', 'completed')
            ->whereBetween('created_at', [$lastMonth, $now])
            ->sum('total_amount');
            
        $orderCount = Order::where('business_id', $business->id)
            ->where('status', 'completed')
            ->count();
            
        $averageOrder = $orderCount > 0 ? $totalSales / $orderCount : 0;

        // Product Analytics
        $totalProducts = Product::where('business_id', $business->id)->count();
        $lowStockProducts = Product::where('business_id', $business->id)
            ->where('stock_quantity', '<=', DB::raw('low_stock_threshold'))
            ->count();

        // Customer Analytics
        $totalCustomers = Customer::where('business_id', $business->id)->count();
        $newCustomers = Customer::where('business_id', $business->id)
            ->whereBetween('created_at', [$lastMonth, $now])
            ->count();

        return [
            'sales' => [
                'total' => number_format($totalSales, 2),
                'monthly' => number_format($monthlySales, 2),
                'orders' => $orderCount,
                'average' => number_format($averageOrder, 2)
            ],
            'products' => [
                'total' => $totalProducts,
                'low_stock' => $lowStockProducts
            ],
            'customers' => [
                'total' => $totalCustomers,
                'new' => $newCustomers
            ]
        ];
    }

    protected function collectBusinessData($business, $type = 'general')
    {
        $now = Carbon::now();
        $lastMonth = $now->copy()->subMonth();
        $lastQuarter = $now->copy()->subMonths(3);
        
        $data = [];

        if ($type === 'general' || $type === 'sales') {
            // Sales Data
            $sales = Order::where('business_id', $business->id)
                ->where('status', 'completed')
                ->get();
                
            $monthlySales = Order::where('business_id', $business->id)
                ->where('status', 'completed')
                ->whereBetween('created_at', [$lastMonth, $now])
                ->sum('total_amount');
                
            $quarterlySales = Order::where('business_id', $business->id)
                ->where('status', 'completed')
                ->whereBetween('created_at', [$lastQuarter, $now])
                ->sum('total_amount');

            $data['sales'] = [
                'total' => number_format($sales->sum('total_amount'), 2),
                'count' => $sales->count(),
                'average_order' => $sales->count() > 0 ? number_format($sales->avg('total_amount'), 2) : '0.00',
                'monthly' => number_format($monthlySales, 2),
                'quarterly' => number_format($quarterlySales, 2),
                'trend' => $monthlySales > 0 ? 'Positive' : 'Stable'
            ];
        }

        if ($type === 'general' || $type === 'products') {
            // Product Data
            $products = Product::where('business_id', $business->id)->get();
            $categories = $products->groupBy('category')->map->count()->sortDesc()->take(5)->keys();
            
            $data['products'] = [
                'total' => $products->count(),
                'active' => $products->where('is_active', true)->count(),
                'low_stock' => $products->where('stock_quantity', '<=', DB::raw('low_stock_threshold'))->count(),
                'top_categories' => $categories->toArray(),
                'total_value' => number_format($products->sum(function($p) { return $p->stock_quantity * $p->price; }), 2)
            ];
        }

        if ($type === 'general' || $type === 'customers') {
            // Customer Data
            $customers = Customer::where('business_id', $business->id)->get();
            $newCustomers = Customer::where('business_id', $business->id)
                ->whereBetween('created_at', [$lastMonth, $now])
                ->count();
                
            $repeatCustomers = Order::where('business_id', $business->id)
                ->where('status', 'completed')
                ->groupBy('customer_id')
                ->havingRaw('COUNT(*) > 1')
                ->count();

            $data['customers'] = [
                'total' => $customers->count(),
                'new' => $newCustomers,
                'repeat' => $repeatCustomers,
                'loyalty_rate' => $customers->count() > 0 ? round(($repeatCustomers / $customers->count()) * 100, 1) : 0
            ];
        }

        if ($type === 'financial') {
            // Financial Data
            $revenue = Order::where('business_id', $business->id)
                ->where('status', 'completed')
                ->sum('total_amount');
                
            $costs = Product::where('business_id', $business->id)
                ->sum(DB::raw('cost_price * stock_quantity'));
                
            $profit = $revenue - $costs;
            $profitMargin = $revenue > 0 ? ($profit / $revenue) * 100 : 0;

            $data['financial'] = [
                'revenue' => number_format($revenue, 2),
                'costs' => number_format($costs, 2),
                'profit' => number_format($profit, 2),
                'profit_margin' => round($profitMargin, 1) . '%',
                'monthly_revenue' => number_format($monthlySales, 2)
            ];
        }

        return $data;
    }

    protected function collectFinancialData($business)
    {
        $now = Carbon::now();
        $lastYear = $now->copy()->subYear();
        
        // Monthly revenue for the past year
        $monthlyRevenue = Order::where('business_id', $business->id)
            ->where('status', 'completed')
            ->whereBetween('created_at', [$lastYear, $now])
            ->selectRaw('MONTH(created_at) as month, SUM(total_amount) as revenue')
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->pluck('revenue', 'month')
            ->toArray();

        // Product performance
        $productPerformance = Product::where('business_id', $business->id)
            ->withCount(['orders as sales_count'])
            ->withSum(['orders as total_sales'], 'total_amount')
            ->orderByDesc('total_sales')
            ->take(10)
            ->get()
            ->map(function($product) {
                return [
                    'name' => $product->name,
                    'sales_count' => $product->sales_count,
                    'total_sales' => $product->total_sales ?? 0,
                    'stock_value' => $product->stock_quantity * $product->price
                ];
            });

        return [
            'monthly_revenue' => $monthlyRevenue,
            'product_performance' => $productPerformance,
            'summary' => $this->collectBusinessData($business, 'financial')['financial']
        ];
    }
}