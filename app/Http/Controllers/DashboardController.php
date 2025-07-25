<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function dashboard()
    {
        $business = auth()->user()->business;
        if (!$business) {
            return redirect()->route('business.choose-type');
        }

        // Orders & Sales
        $todayOrders = $business->orders()->whereDate('created_at', today())->count();
        $todaySales = $business->orders()->whereDate('created_at', today())->where('status', 'completed')->sum('total_amount');
        $pendingOrders = $business->orders()->where('status', 'pending')->count();

        // Conversion Rate (completed/total)
        $totalOrders = $business->orders()->whereDate('created_at', today())->count();
        $completedOrders = $business->orders()->whereDate('created_at', today())->where('status', 'completed')->count();
        $conversionRate = $totalOrders > 0 ? round(($completedOrders / $totalOrders) * 100) : 0;

        // Net Profit (no cost column, so use total sales as net profit)
        $netProfit = $todaySales;

        // New Customers
        $newCustomers = $business->customers()->whereDate('created_at', today())->count();

        // Returning Rate (customers with >1 order)
        $returningCustomers = $business->customers()->has('orders', '>', 1)->count();
        $totalCustomers = $business->customers()->count();
        $returningRate = $totalCustomers > 0 ? round(($returningCustomers / $totalCustomers) * 100) : 0;

        // Avg. Order Value
        $avgOrderValue = $totalOrders > 0 ? round($todaySales / $totalOrders) : 0;


        // Service Businesses Section (use ServiceBooking and ServiceItem)
        $services = $business->services()->latest()->take(5)->get();
        $serviceBookings = \App\Models\ServiceBooking::where('business_id', $business->id)
            ->with(['customer', 'serviceItems.service', 'serviceItems.staff'])
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'business',
            'todayOrders',
            'todaySales',
            'pendingOrders',
            'conversionRate',
            'netProfit',
            'newCustomers',
            'returningRate',
            'avgOrderValue',
            'services',
            'serviceBookings'
        ));
    }
}
