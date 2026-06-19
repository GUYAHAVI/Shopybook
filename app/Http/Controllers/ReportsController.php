<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Customer;
use App\Models\OrderItem;
use App\Models\StockReceipt;
use App\Models\Cost;
use App\Models\ServiceBooking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportsController extends Controller
{
    /**
     * Reports dashboard - overview of all reports
     */
    public function index()
    {
        $business = auth()->user()->business;
        
        // Quick stats for dashboard
        $stats = [
            'total_sales' => Order::where('business_id', $business->id)
                ->where('status', 'completed')
                ->sum('total_amount'),
            'total_orders' => Order::where('business_id', $business->id)
                ->where('status', 'completed')
                ->count(),
            'total_customers' => Customer::where('business_id', $business->id)->count(),
            'total_products' => Product::where('business_id', $business->id)->count(),
        ];
        
        return view('business.reports.index', compact('stats'));
    }

    /**
     * Sales Performance Report
     */
    public function salesReport(Request $request)
    {
        $business = auth()->user()->business;
        
        // Get date range from request or default to current month
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->endOfMonth()->format('Y-m-d'));
        $groupBy = $request->input('group_by', 'day'); // day, week, month
        
        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->endOfDay();

        // Get orders
        $orders = Order::where('business_id', $business->id)
            ->where('status', 'completed')
            ->whereBetween('created_at', [$start, $end])
            ->with(['items.product', 'customer'])
            ->get();

        // Calculate summary metrics
        $totalRevenue = $orders->sum('total_amount');
        $totalOrders = $orders->count();
        $averageOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;
        $totalTax = $orders->sum('tax');
        $totalSubtotal = $orders->sum('subtotal');

        // Group sales by period
        $salesByPeriod = $this->groupSalesByPeriod($orders, $groupBy);

        // Sales by payment method
        $salesByPayment = $orders->groupBy('payment_method')->map(function($group) {
            return [
                'count' => $group->count(),
                'total' => $group->sum('total_amount'),
            ];
        });

        // Top selling hours
        $salesByHour = $orders->groupBy(function($order) {
            return $order->created_at->format('H');
        })->map(function($group, $hour) {
            return [
                'hour' => $hour . ':00',
                'count' => $group->count(),
                'total' => $group->sum('total_amount'),
            ];
        })->sortKeys();

        // Compare with previous period
        $previousStart = $start->copy()->subDays($end->diffInDays($start) + 1);
        $previousEnd = $end->copy()->subDays($end->diffInDays($start) + 1);
        
        $previousOrders = Order::where('business_id', $business->id)
            ->where('status', 'completed')
            ->whereBetween('created_at', [$previousStart, $previousEnd])
            ->get();
            
        $previousRevenue = $previousOrders->sum('total_amount');
        $revenueGrowth = $previousRevenue > 0 
            ? (($totalRevenue - $previousRevenue) / $previousRevenue) * 100 
            : 0;
        $ordersGrowth = $previousOrders->count() > 0
            ? (($totalOrders - $previousOrders->count()) / $previousOrders->count()) * 100
            : 0;

        return view('business.reports.sales', compact(
            'orders',
            'totalRevenue',
            'totalOrders',
            'averageOrderValue',
            'totalTax',
            'totalSubtotal',
            'salesByPeriod',
            'salesByPayment',
            'salesByHour',
            'revenueGrowth',
            'ordersGrowth',
            'previousRevenue',
            'startDate',
            'endDate',
            'groupBy'
        ));
    }

    /**
     * Product Performance Report
     */
    public function productReport(Request $request)
    {
        $business = auth()->user()->business;
        
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->endOfMonth()->format('Y-m-d'));
        
        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->endOfDay();

        // Get product sales data
        $productSales = OrderItem::whereHas('order', function($query) use ($business, $start, $end) {
            $query->where('business_id', $business->id)
                  ->where('status', 'completed')
                  ->whereBetween('created_at', [$start, $end]);
        })
        ->select('product_id', 
            DB::raw('SUM(quantity) as total_quantity'),
            DB::raw('SUM(total) as total_revenue'),
            DB::raw('COUNT(DISTINCT order_id) as order_count')
        )
        ->groupBy('product_id')
        ->with('product')
        ->get()
        ->map(function($item) {
            $product = $item->product;
            $costPrice = $product ? $product->cost_price : 0;
            $profit = $item->total_revenue - ($item->total_quantity * $costPrice);
            $profitMargin = $item->total_revenue > 0 ? ($profit / $item->total_revenue) * 100 : 0;
            
            return [
                'product_id' => $item->product_id,
                'product_name' => $product ? $product->name : 'Unknown',
                'sku' => $product ? $product->sku : 'N/A',
                'category' => $product ? $product->category : 'N/A',
                'quantity_sold' => $item->total_quantity,
                'revenue' => $item->total_revenue,
                'orders' => $item->order_count,
                'cost' => $item->total_quantity * $costPrice,
                'profit' => $profit,
                'profit_margin' => $profitMargin,
                'current_stock' => $product ? $product->stock_quantity : 0,
            ];
        });

        // Sort by revenue (top sellers)
        $topProducts = $productSales->sortByDesc('revenue')->take(20);
        
        // Worst performers
        $worstProducts = $productSales->sortBy('revenue')->take(10);
        
        // Most profitable
        $mostProfitable = $productSales->sortByDesc('profit')->take(10);
        
        // Category performance
        $categoryPerformance = $productSales->groupBy('category')->map(function($group, $category) {
            return [
                'category' => $category,
                'products' => $group->count(),
                'revenue' => $group->sum('revenue'),
                'quantity' => $group->sum('quantity_sold'),
                'profit' => $group->sum('profit'),
            ];
        })->sortByDesc('revenue');

        // Low stock products
        $lowStockProducts = Product::where('business_id', $business->id)
            ->whereRaw('stock_quantity <= low_stock_threshold')
            ->get();

        return view('business.reports.products', compact(
            'productSales',
            'topProducts',
            'worstProducts',
            'mostProfitable',
            'categoryPerformance',
            'lowStockProducts',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Customer Analytics Report
     */
    public function customerReport(Request $request)
    {
        $business = auth()->user()->business;
        
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->endOfMonth()->format('Y-m-d'));
        
        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->endOfDay();

        // Get customer data with their orders
        $customers = Customer::where('business_id', $business->id)
            ->withCount(['orders' => function($query) use ($start, $end) {
                $query->where('status', 'completed')
                      ->whereBetween('created_at', [$start, $end]);
            }])
            ->with(['orders' => function($query) use ($start, $end) {
                $query->where('status', 'completed')
                      ->whereBetween('created_at', [$start, $end]);
            }])
            ->get()
            ->map(function($customer) {
                $orders = $customer->orders;
                $totalSpent = $orders->sum('total_amount');
                $avgOrderValue = $orders->count() > 0 ? $totalSpent / $orders->count() : 0;
                $lastOrder = $orders->sortByDesc('created_at')->first();
                
                return [
                    'id' => $customer->id,
                    'name' => $customer->name,
                    'email' => $customer->email,
                    'phone' => $customer->phone,
                    'total_orders' => $orders->count(),
                    'total_spent' => $totalSpent,
                    'average_order_value' => $avgOrderValue,
                    'last_order_date' => $lastOrder ? $lastOrder->created_at->format('Y-m-d') : 'N/A',
                    'days_since_last_order' => $lastOrder ? now()->diffInDays($lastOrder->created_at) : null,
                ];
            });

        // Top customers by revenue
        $topCustomers = $customers->sortByDesc('total_spent')->take(20);
        
        // Most frequent customers
        $frequentCustomers = $customers->sortByDesc('total_orders')->take(10);
        
        // Customer segments
        $segments = [
            'vip' => $customers->filter(fn($c) => $c['total_spent'] > 50000)->count(),
            'regular' => $customers->filter(fn($c) => $c['total_orders'] >= 5 && $c['total_spent'] <= 50000)->count(),
            'occasional' => $customers->filter(fn($c) => $c['total_orders'] >= 2 && $c['total_orders'] < 5)->count(),
            'one_time' => $customers->filter(fn($c) => $c['total_orders'] == 1)->count(),
        ];

        // New vs returning customers in period
        $newCustomers = Customer::where('business_id', $business->id)
            ->whereBetween('created_at', [$start, $end])
            ->count();
            
        $returningCustomers = Order::where('business_id', $business->id)
            ->where('status', 'completed')
            ->whereBetween('created_at', [$start, $end])
            ->whereHas('customer', function($query) use ($start) {
                $query->where('created_at', '<', $start);
            })
            ->distinct('customer_id')
            ->count('customer_id');

        // Customer lifetime value
        $avgLifetimeValue = $customers->avg('total_spent');

        return view('business.reports.customers', compact(
            'customers',
            'topCustomers',
            'frequentCustomers',
            'segments',
            'newCustomers',
            'returningCustomers',
            'avgLifetimeValue',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Inventory Report
     */
    public function inventoryReport(Request $request)
    {
        $business = auth()->user()->business;

        // Get all products with stock info
        $products = Product::where('business_id', $business->id)
            ->withCount(['stockReceipts'])
            ->get()
            ->map(function($product) {
                $stockValue = $product->stock_quantity * $product->cost_price;
                $reorderNeeded = $product->stock_quantity <= $product->low_stock_threshold;
                
                // Calculate stock turnover (last 30 days)
                $soldLast30Days = OrderItem::whereHas('order', function($query) use ($product) {
                    $query->where('business_id', $product->business_id)
                          ->where('status', 'completed')
                          ->where('created_at', '>=', now()->subDays(30));
                })
                ->where('product_id', $product->id)
                ->sum('quantity');
                
                $turnoverRate = $product->stock_quantity > 0 
                    ? ($soldLast30Days / $product->stock_quantity) * 100 
                    : 0;
                
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'sku' => $product->sku,
                    'category' => $product->category,
                    'current_stock' => $product->stock_quantity,
                    'low_threshold' => $product->low_stock_threshold,
                    'cost_price' => $product->cost_price,
                    'selling_price' => $product->price,
                    'stock_value' => $stockValue,
                    'reorder_needed' => $reorderNeeded,
                    'sold_30_days' => $soldLast30Days,
                    'turnover_rate' => $turnoverRate,
                    'receipts_count' => $product->stock_receipts_count,
                ];
            });

        // Summary stats
        $totalStockValue = $products->sum('stock_value');
        $lowStockCount = $products->where('reorder_needed', true)->count();
        $outOfStockCount = $products->where('current_stock', 0)->count();
        
        // Top value items
        $topValueItems = $products->sortByDesc('stock_value')->take(10);
        
        // Slow moving items (low turnover)
        $slowMoving = $products->sortBy('turnover_rate')->take(10);
        
        // Fast moving items (high turnover)
        $fastMoving = $products->sortByDesc('turnover_rate')->take(10);
        
        // Items needing reorder
        $reorderItems = $products->where('reorder_needed', true);

        // Stock movement summary (last 30 days)
        $stockReceipts = StockReceipt::where('business_id', $business->id)
            ->where('created_at', '>=', now()->subDays(30))
            ->get();
            
        $totalReceived = $stockReceipts->sum('quantity_received');
        $totalReceivedValue = $stockReceipts->sum('total_cost');

        return view('business.reports.inventory', compact(
            'products',
            'totalStockValue',
            'lowStockCount',
            'outOfStockCount',
            'topValueItems',
            'slowMoving',
            'fastMoving',
            'reorderItems',
            'totalReceived',
            'totalReceivedValue'
        ));
    }

    /**
     * Profit & Loss Statement
     */
    public function profitLossReport(Request $request)
    {
        $business = auth()->user()->business;
        
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->endOfMonth()->format('Y-m-d'));
        
        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->endOfDay();

        // REVENUE
        // Product sales revenue
        $productRevenue = Order::where('business_id', $business->id)
            ->where('status', 'completed')
            ->whereBetween('created_at', [$start, $end])
            ->sum('total_amount');
            
        // Service revenue
        $serviceRevenue = ServiceBooking::where('business_id', $business->id)
            ->where('payment_status', 'paid')
            ->whereBetween('service_date', [$start, $end])
            ->sum('final_amount');
            
        $totalRevenue = $productRevenue + $serviceRevenue;

        // COST OF GOODS SOLD
        // Inventory purchase costs (stock receipts)
        $cogs = StockReceipt::where('business_id', $business->id)
            ->whereBetween('receipt_date', [$start, $end])
            ->sum('total_cost');

        // Gross Profit
        $grossProfit = $totalRevenue - $cogs;
        $grossMargin = $totalRevenue > 0 ? ($grossProfit / $totalRevenue) * 100 : 0;

        // OPERATING EXPENSES
        // Business costs/expenses (excluding salaries)
        $operatingExpenses = Cost::where('business_id', $business->id)
            ->where('type', '!=', 'salary')
            ->whereBetween('date', [$start, $end])
            ->sum('amount');
            
        // Salary expenses
        $salaryExpenses = Cost::where('business_id', $business->id)
            ->where('type', 'salary')
            ->whereBetween('date', [$start, $end])
            ->sum('amount');
            
        $totalOperatingExpenses = $operatingExpenses + $salaryExpenses;

        // Operating Income (EBIT)
        $operatingIncome = $grossProfit - $totalOperatingExpenses;
        $operatingMargin = $totalRevenue > 0 ? ($operatingIncome / $totalRevenue) * 100 : 0;

        // NET PROFIT/LOSS
        $netProfit = $operatingIncome; // Can add other income/expenses here
        $netMargin = $totalRevenue > 0 ? ($netProfit / $totalRevenue) * 100 : 0;

        // Expense breakdown
        $expenseByCategory = Cost::where('business_id', $business->id)
            ->whereBetween('date', [$start, $end])
            ->select('type', DB::raw('SUM(amount) as total'))
            ->groupBy('type')
            ->get();

        // Monthly trend (last 6 months)
        $monthlyTrend = [];
        for ($i = 5; $i >= 0; $i--) {
            $monthStart = now()->subMonths($i)->startOfMonth();
            $monthEnd = now()->subMonths($i)->endOfMonth();
            
            $monthRevenue = Order::where('business_id', $business->id)
                ->where('status', 'completed')
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->sum('total_amount');
                
            $monthCogs = StockReceipt::where('business_id', $business->id)
                ->whereBetween('receipt_date', [$monthStart, $monthEnd])
                ->sum('total_cost');
                
            $monthExpenses = Cost::where('business_id', $business->id)
                ->whereBetween('date', [$monthStart, $monthEnd])
                ->sum('amount');
                
            $monthProfit = $monthRevenue - $monthCogs - $monthExpenses;
            
            $monthlyTrend[] = [
                'month' => $monthStart->format('M Y'),
                'revenue' => $monthRevenue,
                'cogs' => $monthCogs,
                'expenses' => $monthExpenses,
                'profit' => $monthProfit,
            ];
        }

        return view('business.reports.profit-loss', compact(
            'totalRevenue',
            'productRevenue',
            'serviceRevenue',
            'cogs',
            'grossProfit',
            'grossMargin',
            'operatingExpenses',
            'salaryExpenses',
            'totalOperatingExpenses',
            'operatingIncome',
            'operatingMargin',
            'netProfit',
            'netMargin',
            'expenseByCategory',
            'monthlyTrend',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Export report as PDF
     */
    public function exportPdf(Request $request)
    {
        $reportType = $request->input('type');
        $business = auth()->user()->business;
        
        // Generate appropriate report data based on type
        // This is a basic implementation - expand as needed
        
        $pdf = Pdf::loadView('business.reports.pdf.' . $reportType, [
            'business' => $business,
            'date' => now()->format('Y-m-d'),
        ]);
        
        return $pdf->download($reportType . '_report_' . now()->format('Y-m-d') . '.pdf');
    }

    /**
     * Helper: Group sales by period
     */
    private function groupSalesByPeriod($orders, $groupBy)
    {
        switch ($groupBy) {
            case 'day':
                return $orders->groupBy(function($order) {
                    return $order->created_at->format('Y-m-d');
                })->map(function($group, $date) {
                    return [
                        'period' => Carbon::parse($date)->format('M d'),
                        'count' => $group->count(),
                        'revenue' => $group->sum('total_amount'),
                    ];
                })->values();
                
            case 'week':
                return $orders->groupBy(function($order) {
                    return $order->created_at->format('Y-W');
                })->map(function($group, $week) {
                    return [
                        'period' => 'Week ' . substr($week, -2),
                        'count' => $group->count(),
                        'revenue' => $group->sum('total_amount'),
                    ];
                })->values();
                
            case 'month':
                return $orders->groupBy(function($order) {
                    return $order->created_at->format('Y-m');
                })->map(function($group, $month) {
                    return [
                        'period' => Carbon::parse($month)->format('M Y'),
                        'count' => $group->count(),
                        'revenue' => $group->sum('total_amount'),
                    ];
                })->values();
                
            default:
                return collect();
        }
    }
}
