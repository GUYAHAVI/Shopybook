<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ServiceBooking;
use App\Models\ServiceItem;

class DashboardController extends Controller
{
    public function dashboard()
    {
        $business = auth()->user()->business;
        if (!$business) {
            return redirect()->route('business.choose-type');
        }

        // Product-based metrics - ensure we're only getting data for this business
        $todayOrders = $business->orders()
            ->whereDate('created_at', today())
            ->count();
        $todaySales = $business->orders()
            ->whereDate('created_at', today())
            ->where('status', 'completed')
            ->sum('total_amount');
        $pendingOrders = $business->orders()
            ->where('status', 'pending')
            ->count();

        // Product conversion rate
        $totalOrders = $business->orders()
            ->whereDate('created_at', today())
            ->count();
        $completedOrders = $business->orders()
            ->whereDate('created_at', today())
            ->where('status', 'completed')
            ->count();
        $conversionRate = $totalOrders > 0 ? round(($completedOrders / $totalOrders) * 100) : 0;

        // Product profit
        $netProfit = $todaySales;

        // Service-based metrics
        $todayServiceBookings = ServiceBooking::where('business_id', $business->id)
            ->whereDate('created_at', today())
            ->count();
        
        $todayServiceRevenue = ServiceBooking::where('business_id', $business->id)
            ->whereDate('created_at', today())
            ->where('payment_status', 'paid')
            ->sum('final_amount');
        
        $pendingServiceBookings = ServiceBooking::where('business_id', $business->id)
            ->where('payment_status', 'pending')
            ->count();
        
        // Service conversion rate
        $totalServiceBookings = ServiceBooking::where('business_id', $business->id)
            ->whereDate('created_at', today())
            ->count();
        $completedServiceBookings = ServiceBooking::where('business_id', $business->id)
            ->whereDate('created_at', today())
            ->where('payment_status', 'paid')
            ->count();
        $serviceConversionRate = $totalServiceBookings > 0 ? round(($completedServiceBookings / $totalServiceBookings) * 100) : 0;

        // Service profit calculation
        $serviceProfit = $todayServiceRevenue;

        // Combined metrics
        $totalTodayRevenue = $todaySales + $todayServiceRevenue;
        $totalTodayBookings = $todayOrders + $todayServiceBookings;
        $totalPending = $pendingOrders + $pendingServiceBookings;

        // Customer metrics (combined for products and services)
        $newCustomers = $business->customers()->whereDate('created_at', today())->count();
        
        // Returning customers (those with multiple orders OR service bookings)
        $returningCustomers = $business->customers()
            ->where(function($query) {
                $query->has('orders', '>', 1)
                      ->orHas('serviceBookings', '>', 1);
            })
            ->count();
        $totalCustomers = $business->customers()->count();
        $returningRate = $totalCustomers > 0 ? round(($returningCustomers / $totalCustomers) * 100) : 0;

        // Average values
        $avgOrderValue = $totalOrders > 0 ? round($todaySales / $totalOrders) : 0;
        $avgServiceValue = $totalServiceBookings > 0 ? round($todayServiceRevenue / $totalServiceBookings) : 0;

        // Service-specific data
        $services = $business->services()->latest()->take(5)->get();
        $serviceBookings = ServiceBooking::where('business_id', $business->id)
            ->with(['customer', 'serviceItems.service', 'serviceItems.staff'])
            ->latest()
            ->take(5)
            ->get();

        // Service performance metrics
        $topServices = ServiceItem::whereHas('serviceBooking', function($query) use ($business) {
            $query->where('business_id', $business->id);
        })
        ->with('service')
        ->selectRaw('service_id, COUNT(*) as booking_count, SUM(amount) as total_revenue')
        ->groupBy('service_id')
        ->orderByDesc('total_revenue')
        ->limit(5)
        ->get();

        // Staff performance
        $staffPerformance = ServiceItem::whereHas('serviceBooking', function($query) use ($business) {
            $query->where('business_id', $business->id);
        })
        ->with('staff')
        ->selectRaw('staff_id, COUNT(*) as service_count, SUM(amount) as total_revenue')
        ->groupBy('staff_id')
        ->orderByDesc('total_revenue')
        ->limit(5)
        ->get();

        return view('dashboard', compact(
            'business',
            // Product metrics
            'todayOrders',
            'todaySales',
            'pendingOrders',
            'conversionRate',
            'netProfit',
            'avgOrderValue',
            // Service metrics
            'todayServiceBookings',
            'todayServiceRevenue',
            'pendingServiceBookings',
            'serviceConversionRate',
            'serviceProfit',
            'avgServiceValue',
            // Combined metrics
            'totalTodayRevenue',
            'totalTodayBookings',
            'totalPending',
            // Customer metrics
            'newCustomers',
            'returningRate',
            // Service data
            'services',
            'serviceBookings',
            'topServices',
            'staffPerformance'
        ));
    }
}
