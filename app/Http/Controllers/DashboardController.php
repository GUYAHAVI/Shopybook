<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ServiceBooking;
use App\Models\ServiceItem;
use App\Models\BusinessMember;

class DashboardController extends Controller
{
    public function dashboard()
    {
        $user     = auth()->user();
        $business = $user->business;

        // Team member path: no owned business, but may be a member of one
        if (!$business) {
            $membership = BusinessMember::where('user_id', $user->id)
                ->where('status', 'active')
                ->with('business')
                ->first();

            if ($membership && $membership->business) {
                $business = $membership->business;
                $user->setRelation('business', $business);
                $user->setRelation('activeMembership', $membership);
            } else {
                return redirect()->route('business.choose-type');
            }
        }

        // Determine permissions once (owners get everything)
        $isOwner   = $user->isOwnerOf($business->id);
        $can = fn(string $m) => $isOwner || $user->hasModulePermission($m);

        $canOrders    = $can('orders')    || $can('pos');
        $canServices  = $can('services');
        $canCustomers = $can('customers');
        $canProducts  = $can('products');
        $canExpenses  = $can('expenses');
        $canReports   = $can('reports');
        $canStaff     = $can('staff');

        // ── Product / Order metrics (only load if permitted) ───────────────
        $todayOrders     = $canOrders ? $business->orders()->whereDate('created_at', today())->count() : 0;
        $todaySales      = $canOrders ? $business->orders()->whereDate('created_at', today())->where('status', 'completed')->sum('total_amount') : 0;
        $pendingOrders   = $canOrders ? $business->orders()->where('status', 'pending')->count() : 0;
        $totalOrders     = $canOrders ? $business->orders()->whereDate('created_at', today())->count() : 0;
        $completedOrders = $canOrders ? $business->orders()->whereDate('created_at', today())->where('status', 'completed')->count() : 0;
        $conversionRate  = $totalOrders > 0 ? round(($completedOrders / $totalOrders) * 100) : 0;

        // Product profit (only for expense-level or owner)
        $todayInventoryCosts = ($canOrders && $canExpenses) ? $business->getTodayInventoryCosts() : 0;
        $todayOtherCosts     = $canExpenses ? ($business->costs()->whereDate('date', today())->sum('amount') ?? 0) : 0;
        $todayReturns        = ($canOrders && $can('returns')) ? $business->getTodayReturns() : 0;
        $netProfit = $todaySales - $todayInventoryCosts - $todayOtherCosts - $todayReturns;

        // Service-based metrics
        // ── Service metrics (only load if permitted) ──────────────────────
        $todayServiceBookings     = $canServices ? ServiceBooking::where('business_id', $business->id)->whereDate('created_at', today())->count() : 0;
        $todayServiceRevenue      = $canServices ? ServiceBooking::where('business_id', $business->id)->whereDate('created_at', today())->where('payment_status', 'paid')->sum('final_amount') : 0;
        $pendingServiceBookings   = $canServices ? ServiceBooking::where('business_id', $business->id)->where('payment_status', 'pending')->count() : 0;
        $totalServiceBookings     = $canServices ? ServiceBooking::where('business_id', $business->id)->whereDate('created_at', today())->count() : 0;
        $completedServiceBookings = $canServices ? ServiceBooking::where('business_id', $business->id)->whereDate('created_at', today())->where('payment_status', 'paid')->count() : 0;
        $serviceConversionRate    = $totalServiceBookings > 0 ? round(($completedServiceBookings / $totalServiceBookings) * 100) : 0;
        $serviceProfit            = $todayServiceRevenue;

        // ── Combined metrics ───────────────────────────────────────────────
        $totalNetProfit    = $netProfit + $serviceProfit;
        $totalTodayRevenue = $todaySales + $todayServiceRevenue;
        $totalTodayBookings = $todayOrders + $todayServiceBookings;
        $totalPending      = $pendingOrders + $pendingServiceBookings;

        // ── Customer metrics (only load if permitted) ──────────────────────
        $newCustomers     = $canCustomers ? $business->customers()->whereDate('created_at', today())->count() : 0;
        $returningCustomers = $canCustomers ? $business->customers()->where(function($q) {
            $q->has('orders', '>', 1)->orHas('serviceBookings', '>', 1);
        })->count() : 0;
        $totalCustomers   = $canCustomers ? $business->customers()->count() : 0;
        $returningRate    = $totalCustomers > 0 ? round(($returningCustomers / $totalCustomers) * 100) : 0;

        // ── Average values ────────────────────────────────────────────────
        $avgOrderValue   = $totalOrders > 0 ? round($todaySales / $totalOrders) : 0;
        $avgServiceValue = $totalServiceBookings > 0 ? round($todayServiceRevenue / $totalServiceBookings) : 0;

        // ── Detailed data (gated by permission) ───────────────────────────
        $services = $canServices
            ? $business->services()->latest()->take(5)->get()
            : collect();

        $serviceBookings = $canServices
            ? ServiceBooking::where('business_id', $business->id)->with(['customer', 'serviceItems.service', 'serviceItems.staff'])->latest()->take(5)->get()
            : collect();

        $topServices = ($canServices && $canReports)
            ? ServiceItem::whereHas('serviceBooking', fn($q) => $q->where('business_id', $business->id))
                ->with('service')
                ->selectRaw('service_id, COUNT(*) as booking_count, SUM(amount) as total_revenue')
                ->groupBy('service_id')->orderByDesc('total_revenue')->limit(5)->get()
            : collect();

        $staffPerformance = $canStaff
            ? ServiceItem::whereHas('serviceBooking', fn($q) => $q->where('business_id', $business->id))
                ->with('staff')
                ->selectRaw('staff_id, COUNT(*) as service_count, SUM(amount) as total_revenue')
                ->groupBy('staff_id')->orderByDesc('total_revenue')->limit(5)->get()
            : collect();

        $topProducts = ($canProducts || $canOrders)
            ? $business->orders()
                ->where('orders.status', 'completed')
                ->whereDate('orders.created_at', '>=', now()->subDays(30))
                ->join('order_items', 'orders.id', '=', 'order_items.order_id')
                ->join('products', 'order_items.product_id', '=', 'products.id')
                ->selectRaw('products.id, products.name, SUM(order_items.quantity) as total_sold, SUM(order_items.total) as total_revenue')
                ->groupBy('products.id', 'products.name')
                ->orderByDesc('total_revenue')->limit(10)->get()
            : collect();

        $topCustomers = $canCustomers
            ? $business->customers()
                ->withSum(['orders' => fn($q) => $q->where('status', 'completed')], 'total_amount')
                ->withSum(['serviceBookings' => fn($q) => $q->where('payment_status', 'paid')], 'final_amount')
                ->get()
                ->map(function($customer) {
                    $customer->total_spent = ($customer->orders_sum_total_amount ?? 0) + ($customer->service_bookings_sum_final_amount ?? 0);
                    $customer->orders_count = $customer->orders()->count();
                    $customer->bookings_count = $customer->serviceBookings()->count();
                    return $customer;
                })
                ->where('total_spent', '>', 0)->sortByDesc('total_spent')->take(10)->values()
            : collect();

        return view('dashboard', compact(
            'business',
            // Permissions for the view
            'canOrders', 'canServices', 'canCustomers', 'canProducts', 'canExpenses', 'canReports', 'canStaff',
            // Product metrics
            'todayOrders', 'todaySales', 'pendingOrders', 'conversionRate',
            'netProfit', 'todayInventoryCosts', 'todayOtherCosts', 'todayReturns', 'avgOrderValue',
            // Service metrics
            'todayServiceBookings', 'todayServiceRevenue', 'pendingServiceBookings',
            'serviceConversionRate', 'serviceProfit', 'avgServiceValue',
            // Combined metrics
            'totalTodayRevenue', 'totalTodayBookings', 'totalPending', 'totalNetProfit',
            // Customer metrics
            'newCustomers', 'returningRate',
            // Service data
            'services', 'serviceBookings', 'topServices', 'staffPerformance',
            // Product & Customer data
            'topProducts', 'topCustomers'
        ));
    }
}

