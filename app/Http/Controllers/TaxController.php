<?php

namespace App\Http\Controllers;

use App\Models\TaxSetting;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TaxController extends Controller
{
    /**
     * Display tax settings form
     */
    public function settings()
    {
        $business = auth()->user()->business;
        $taxSettings = $business->getTaxSettingsOrCreate();
        
        return view('business.tax.settings', compact('taxSettings'));
    }

    /**
     * Update tax settings
     */
    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'tax_enabled' => 'required|boolean',
            'tax_type' => 'required|string|max:255',
            'tax_rate' => 'required|numeric|min:0|max:100',
            'tax_number' => 'nullable|string|max:255',
            'tax_name' => 'required|string|max:255',
            'tax_inclusive' => 'required|boolean',
            'tax_period' => 'required|in:monthly,quarterly,annual',
            'show_tax_on_receipt' => 'required|boolean',
            'separate_tax_column' => 'required|boolean',
        ]);

        $business = auth()->user()->business;
        $taxSettings = $business->getTaxSettingsOrCreate();
        
        $taxSettings->update($validated);

        return redirect()->route('tax.settings')
            ->with('success', 'Tax settings updated successfully!');
    }

    /**
     * Display tax reports dashboard
     */
    public function reports(Request $request)
    {
        $business = auth()->user()->business;
        $taxSettings = $business->taxSettings;

        // Get date range from request or default to current month
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->endOfMonth()->format('Y-m-d'));
        
        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->endOfDay();

        // Get orders with tax data
        $orders = Order::where('business_id', $business->id)
            ->where('status', 'completed')
            ->whereBetween('created_at', [$start, $end])
            ->orderBy('created_at', 'desc')
            ->get();

        // Calculate tax summary
        $totalSales = $orders->sum('total_amount');
        $totalTaxCollected = $orders->sum('tax');
        $totalSubtotal = $orders->sum('subtotal');
        $ordersCount = $orders->count();
        $averageTaxPerOrder = $ordersCount > 0 ? $totalTaxCollected / $ordersCount : 0;

        // Group by date for chart
        $taxByDate = $orders->groupBy(function($order) {
            return $order->created_at->format('Y-m-d');
        })->map(function($dayOrders) {
            return [
                'date' => $dayOrders->first()->created_at->format('M d'),
                'tax' => $dayOrders->sum('tax'),
                'sales' => $dayOrders->sum('total_amount'),
            ];
        })->values();

        // Group by tax type
        $taxByType = $orders->groupBy('tax_type')->map(function($typeOrders, $type) {
            return [
                'type' => $type ?: 'N/A',
                'tax' => $typeOrders->sum('tax'),
                'count' => $typeOrders->count(),
            ];
        })->values();

        // Monthly comparison (last 6 months)
        $monthlyData = [];
        for ($i = 5; $i >= 0; $i--) {
            $monthStart = now()->subMonths($i)->startOfMonth();
            $monthEnd = now()->subMonths($i)->endOfMonth();
            
            $monthOrders = Order::where('business_id', $business->id)
                ->where('status', 'completed')
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->get();
                
            $monthlyData[] = [
                'month' => $monthStart->format('M Y'),
                'tax' => $monthOrders->sum('tax'),
                'sales' => $monthOrders->sum('total_amount'),
            ];
        }

        return view('business.tax.reports', compact(
            'taxSettings',
            'orders',
            'totalSales',
            'totalTaxCollected',
            'totalSubtotal',
            'ordersCount',
            'averageTaxPerOrder',
            'taxByDate',
            'taxByType',
            'monthlyData',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Export tax report as CSV
     */
    public function exportReport(Request $request)
    {
        $business = auth()->user()->business;
        
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->endOfMonth()->format('Y-m-d'));
        
        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->endOfDay();

        $orders = Order::where('business_id', $business->id)
            ->where('status', 'completed')
            ->whereBetween('created_at', [$start, $end])
            ->orderBy('created_at', 'desc')
            ->get();

        $filename = "tax_report_{$startDate}_to_{$endDate}.csv";
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($orders) {
            $file = fopen('php://output', 'w');
            
            // Header row
            fputcsv($file, [
                'Order Number',
                'Date',
                'Customer',
                'Subtotal',
                'Tax Rate',
                'Tax Amount',
                'Total',
                'Tax Type',
                'Payment Method'
            ]);
            
            // Data rows
            foreach ($orders as $order) {
                fputcsv($file, [
                    $order->order_number,
                    $order->created_at->format('Y-m-d H:i'),
                    $order->customer ? $order->customer->name : ($order->customer_name ?? 'Walk-in'),
                    number_format($order->subtotal, 2),
                    number_format($order->tax_rate, 2) . '%',
                    number_format($order->tax, 2),
                    number_format($order->total_amount, 2),
                    $order->tax_type ?? 'N/A',
                    $order->payment_method ?? 'N/A'
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get tax dashboard data for quick view
     */
    public function dashboard()
    {
        $business = auth()->user()->business;
        $taxSettings = $business->taxSettings;

        // Current month tax data
        $currentMonthStart = now()->startOfMonth();
        $currentMonthEnd = now()->endOfMonth();
        
        $currentMonthOrders = Order::where('business_id', $business->id)
            ->where('status', 'completed')
            ->whereBetween('created_at', [$currentMonthStart, $currentMonthEnd])
            ->get();
            
        $currentMonthTax = $currentMonthOrders->sum('tax');
        $currentMonthSales = $currentMonthOrders->sum('total_amount');

        // Previous month for comparison
        $previousMonthStart = now()->subMonth()->startOfMonth();
        $previousMonthEnd = now()->subMonth()->endOfMonth();
        
        $previousMonthOrders = Order::where('business_id', $business->id)
            ->where('status', 'completed')
            ->whereBetween('created_at', [$previousMonthStart, $previousMonthEnd])
            ->get();
            
        $previousMonthTax = $previousMonthOrders->sum('tax');

        // Calculate growth
        $taxGrowth = $previousMonthTax > 0 
            ? (($currentMonthTax - $previousMonthTax) / $previousMonthTax) * 100 
            : 0;

        // Year to date
        $yearStart = now()->startOfYear();
        $ytdOrders = Order::where('business_id', $business->id)
            ->where('status', 'completed')
            ->whereBetween('created_at', [$yearStart, now()])
            ->get();
            
        $ytdTax = $ytdOrders->sum('tax');

        return response()->json([
            'current_month_tax' => number_format($currentMonthTax, 2),
            'current_month_sales' => number_format($currentMonthSales, 2),
            'tax_growth' => number_format($taxGrowth, 1),
            'ytd_tax' => number_format($ytdTax, 2),
            'tax_enabled' => $taxSettings ? $taxSettings->tax_enabled : false,
            'tax_rate' => $taxSettings ? $taxSettings->tax_rate : 0,
        ]);
    }
}
