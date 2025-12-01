@extends('layouts.dash')
@section('title', 'Dashboard')

@section('content')

{{-- Subscription Upgrade Banner for Non-Enterprise Users --}}
@if(Auth::user()->business && !Auth::user()->business->isEnterprise())
    <div class="alert alert-dismissible fade show mb-4" role="alert" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; border-radius: 12px; box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);">
        <div class="row align-items-center">
            <div class="col-md-9">
                <div class="d-flex align-items-center">
                    <div style="font-size: 2.5rem; margin-right: 20px;">
                        <i class="fas fa-crown text-warning"></i>
                    </div>
                    <div>
                        @if(Auth::user()->business->isOnTrial())
                            <h5 class="mb-1" style="color: white; font-weight: 700;">
                                <i class="fas fa-gift"></i> You're on Enterprise Trial!
                            </h5>
                            <p class="mb-2" style="color: rgba(255,255,255,0.95); font-size: 0.95rem;">
                                {{ Auth::user()->business->trialDaysRemaining() }} days left in your free trial. Upgrade now to continue enjoying all features!
                            </p>
                        @else
                            <h5 class="mb-1" style="color: white; font-weight: 700;">
                                <i class="fas fa-sparkles"></i> Unlock Premium Features with Enterprise Plan
                            </h5>
                            <p class="mb-2" style="color: rgba(255,255,255,0.95); font-size: 0.95rem;">
                                Get AI-powered website builder, advanced analytics, priority support, and much more!
                            </p>
                        @endif
                        <div class="d-flex gap-3 flex-wrap" style="color: rgba(255,255,255,0.9); font-size: 0.85rem;">
                            <span><i class="fas fa-check-circle me-1" style="color: #10b981;"></i>AI Website Auto-Build</span>
                            <span><i class="fas fa-check-circle me-1" style="color: #10b981;"></i>Advanced Reports</span>
                            <span><i class="fas fa-check-circle me-1" style="color: #10b981;"></i>Priority Support</span>
                            <span><i class="fas fa-check-circle me-1" style="color: #10b981;"></i>Unlimited Features</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 text-center mt-3 mt-md-0">
                <button type="button" class="btn btn-light btn-lg d-block mb-2" data-bs-toggle="modal" data-bs-target="#subscriptionUpgradeModal" style="font-weight: 700; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
                    <i class="fas fa-rocket me-2"></i>{{ Auth::user()->business->isOnTrial() ? 'Continue with Paid Plan' : 'Upgrade Now' }}
                </button>
                <small style="color: rgba(255,255,255,0.85); font-size: 0.75rem;">
                    <i class="fas fa-tag me-1"></i>From KSH 5/month (Test Mode)
                </small>
            </div>
        </div>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close" style="filter: brightness(0) invert(1);"></button>
    </div>
@endif

{{-- Missing Logo Alert --}}
@if(Auth::user()->business && (empty(Auth::user()->business->logo_path) || !Auth::user()->business->logo_path))
    <div class="alert alert-warning alert-dismissible fade show mb-4" role="alert" style="border-left: 4px solid #ffc107; background: #fff3cd; border-radius: 8px;">
        <div class="d-flex align-items-center">
            <div style="font-size: 2rem; margin-right: 15px;">
                <i class="fas fa-exclamation-triangle text-warning"></i>
            </div>
            <div class="flex-grow-1">
                <h5 class="alert-heading mb-2" style="color: #856404; font-weight: bold;">
                    <i class="fas fa-image"></i> Business Logo Missing
                </h5>
                <p class="mb-2" style="color: #856404;">
                    Your business logo is not currently set. Upload your logo to improve your business profile visibility and professional appearance.
                </p>
                 <div class="d-flex gap-2">
                    <a href="{{ route('business.edit') }}" class="btn btn-warning btn-sm">
                        <i class="fas fa-upload"></i> Upload Logo
                    </a>
                    <button type="button" class="btn btn-info btn-sm" onclick="openLogoGenerator()">
                        <i class="fas fa-magic"></i> Generate with AI
                    </button>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>
@endif

<!-- KPI Cards -->
<div class="kpi-grid">
    <div class="kpi-card">
        <div class="kpi-header">
            <h6 class="kpi-title">Today's Sales</h6>
            <i class="fas fa-dollar-sign"></i>
        </div>
        <h2 class="kpi-value">KSh {{ number_format($todaySales ?? 0, 0) }}</h2>
        <div class="kpi-change positive">
            <i class="fas fa-arrow-up"></i>
            <span>{{ $todayOrders ?? 0 }} orders</span>
        </div>
    </div>
    
    <div class="kpi-card">
        <div class="kpi-header">
            <h6 class="kpi-title">Today's Orders</h6>
            <i class="fas fa-shopping-cart"></i>
        </div>
        <h2 class="kpi-value">{{ $todayOrders ?? 0 }}</h2>
        <div class="kpi-change positive">
            <i class="fas fa-arrow-up"></i>
            <span>{{ $conversionRate ?? 0 }}% completed</span>
        </div>
    </div>
    
    <div class="kpi-card">
        <div class="kpi-header">
            <h6 class="kpi-title">Pending Orders</h6>
            <i class="fas fa-clock"></i>
        </div>
        <h2 class="kpi-value">{{ $pendingOrders ?? 0 }}</h2>
        <div class="kpi-change neutral">
            <i class="fas fa-minus"></i>
            <span>Awaiting completion</span>
        </div>
    </div>
    
    <div class="kpi-card">
        <div class="kpi-header">
            <h6 class="kpi-title">New Customers</h6>
            <i class="fas fa-users"></i>
        </div>
        <h2 class="kpi-value">{{ $newCustomers ?? 0 }}</h2>
        <div class="kpi-change positive">
            <i class="fas fa-arrow-up"></i>
            <span>Today</span>
        </div>
    </div>
</div>

<!-- Website Builder Promo Banner -->
@if(auth()->user()->business && !auth()->user()->business->website)
<div class="row mb-4">
    <div class="col-12">
        <div class="alert quick-start-banner" style="background: linear-gradient(135deg, #4F46E5, #7C3AED); border: none; border-radius: 15px; box-shadow: 0 10px 25px -5px rgba(79, 70, 229, 0.3);">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h4 class="text-white mb-2">
                        <i class="fas fa-globe me-2"></i>🌟 Create Your FREE Website in 5 Minutes!
                    </h4>
                    <p class="text-white mb-2">
                        Build a stunning website for <strong>{{ auth()->user()->business->name }}</strong> with our drag-and-drop builder. No coding required!
                    </p>
                    <p class="text-white mb-0 small">
                        ✨ Your website will be live at: <strong>{{ auth()->user()->business->slug }}.shopybook.com</strong>
                    </p>
                </div>
                <div class="col-md-4 text-center mt-3 mt-md-0">
                    <a href="{{ route('website.builder.index') }}" class="btn btn-light btn-lg pulse-animation">
                        <i class="fas fa-rocket me-2"></i>Build My Website
                    </a>
                    <div class="text-white mt-2 small">
                        <i class="fas fa-check me-1"></i>8 Beautiful Themes
                        <i class="fas fa-check ms-2 me-1"></i>100% Free
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Quick Add Product Banner for New Users -->
@if(auth()->user()->business && auth()->user()->business->products()->count() == 0)
<div class="row mb-4">
    <div class="col-12">
        <div class="alert alert-info quick-start-banner" style="background: linear-gradient(135deg, #17a2b8, #138496); border: none; border-radius: 15px;">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h4 class="text-white mb-2">
                        <i class="fas fa-rocket me-2"></i>Welcome to Shopybook! 🎉
                    </h4>
                    <p class="text-white mb-0">
                        Let's get you started by adding your first product. It takes less than 30 seconds with our Quick Add feature.
                    </p>
                </div>
                <div class="col-md-4 text-center mt-3 mt-md-0">
                    <a href="{{ route('products.quick-create') }}" class="btn btn-light btn-lg me-2">
                        <i class="fas fa-bolt me-2"></i>Quick Add Product
                    </a>
                    <a href="{{ route('products.create') }}" class="btn btn-outline-light">
                        <i class="fas fa-cog me-1"></i>Advanced
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Business Overview Cards -->
<div class="business-overview mb-4">
    <div class="row g-4">
        <!-- Product Metrics -->
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="overview-card product-card">
                <div class="overview-icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <div class="overview-content">
                    <h3 class="overview-value">{{ $todayOrders ?? 0 }}</h3>
                    <p class="overview-label">Product Orders</p>
                    <small class="overview-subtitle">Today</small>
                </div>
            </div>
        </div>
        
        <!-- Service Metrics -->
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="overview-card service-card">
                <div class="overview-icon">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="overview-content">
                    <h3 class="overview-value">{{ $todayServiceBookings ?? 0 }}</h3>
                    <p class="overview-label">Service Bookings</p>
                    <small class="overview-subtitle">Today</small>
                </div>
            </div>
        </div>
        
        <!-- Combined Revenue -->
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="overview-card revenue-card">
                <div class="overview-icon">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <div class="overview-content">
                    <h3 class="overview-value">KSh {{ number_format($totalTodayRevenue ?? 0, 0) }}</h3>
                    <p class="overview-label">Total Revenue</p>
                    <small class="overview-subtitle">Today</small>
                </div>
            </div>
        </div>
        
        <!-- Customer Metrics -->
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="overview-card customer-card">
                <div class="overview-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="overview-content">
                    <h3 class="overview-value">{{ $newCustomers ?? 0 }}</h3>
                    <p class="overview-label">New Customers</p>
                    <small class="overview-subtitle">Today</small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Detailed Metrics Section -->
<div class="detailed-metrics mb-4">
    <div class="row g-4">
        <!-- Product Performance -->
        <div class="col-xl-6 col-lg-6 col-md-12">
            <div class="metric-card">
                <div class="metric-header">
                    <h5 class="metric-title">
                        <i class="fas fa-shopping-cart me-2"></i>Product Performance
                    </h5>
                </div>
                <div class="metric-content">
                    <div class="metric-row">
                        <span class="metric-label">Revenue:</span>
                        <span class="metric-value">KSh {{ number_format($todaySales ?? 0, 0) }}</span>
                    </div>
                    <div class="metric-row">
                        <span class="metric-label">Pending Orders:</span>
                        <span class="metric-value">{{ $pendingOrders ?? 0 }}</span>
                    </div>
                    <div class="metric-row">
                        <span class="metric-label">Conversion Rate:</span>
                        <span class="metric-value">{{ $conversionRate ?? 0 }}%</span>
                    </div>
                    <div class="metric-row">
                        <span class="metric-label">Avg. Order Value:</span>
                        <span class="metric-value">KSh {{ number_format($avgOrderValue ?? 0, 0) }}</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Service Performance -->
        <div class="col-xl-6 col-lg-6 col-md-12">
            <div class="metric-card">
                <div class="metric-header">
                    <h5 class="metric-title">
                        <i class="fas fa-calendar-check me-2"></i>Service Performance
                    </h5>
                </div>
                <div class="metric-content">
                    <div class="metric-row">
                        <span class="metric-label">Revenue:</span>
                        <span class="metric-value">KSh {{ number_format($todayServiceRevenue ?? 0, 0) }}</span>
                    </div>
                    <div class="metric-row">
                        <span class="metric-label">Pending Bookings:</span>
                        <span class="metric-value">{{ $pendingServiceBookings ?? 0 }}</span>
                    </div>
                    <div class="metric-row">
                        <span class="metric-label">Conversion Rate:</span>
                        <span class="metric-value">{{ $serviceConversionRate ?? 0 }}%</span>
                    </div>
                    <div class="metric-row">
                        <span class="metric-label">Avg. Booking Value:</span>
                        <span class="metric-value">KSh {{ number_format($avgServiceValue ?? 0, 0) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="quick-actions mb-4">
    <div class="row g-4">
        <!-- Product Management -->
        <div class="col-xl-6 col-lg-6 col-md-12">
            <div class="action-card product-action">
                <div class="action-header">
                    <h5 class="action-title">
                        <i class="fas fa-shopping-cart me-2"></i>Product Management
                    </h5>
                    <a href="{{ route('sales.orders') }}" class="btn btn-sm btn-primary">View Orders</a>
                </div>
                <div class="action-content">
                    <div class="action-stat">
                        <span class="stat-label">Pending Orders:</span>
                        <span class="stat-value">{{ $pendingOrders ?? 0 }}</span>
                    </div>
                    <div class="action-stat">
                        <span class="stat-label">Today's Revenue:</span>
                        <span class="stat-value">KSh {{ number_format($todaySales ?? 0, 0) }}</span>
                    </div>
                    <div class="action-stat">
                        <span class="stat-label">Conversion Rate:</span>
                        <span class="stat-value">{{ $conversionRate ?? 0 }}%</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Service Management -->
        <div class="col-xl-6 col-lg-6 col-md-12">
            <div class="action-card service-action">
                <div class="action-header">
                    <h5 class="action-title">
                        <i class="fas fa-calendar-check me-2"></i>Service Management
                    </h5>
                    <a href="{{ route('service-bookings.index') }}" class="btn btn-sm btn-primary">View Bookings</a>
                </div>
                <div class="action-content">
                    <div class="action-stat">
                        <span class="stat-label">Pending Bookings:</span>
                        <span class="stat-value">{{ $pendingServiceBookings ?? 0 }}</span>
                    </div>
                    <div class="action-stat">
                        <span class="stat-label">Today's Revenue:</span>
                        <span class="stat-value">KSh {{ number_format($todayServiceRevenue ?? 0, 0) }}</span>
                    </div>
                    <div class="action-stat">
                        <span class="stat-label">Conversion Rate:</span>
                        <span class="stat-value">{{ $serviceConversionRate ?? 0 }}%</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Service Performance Section -->
@if($topServices && $topServices->count() > 0)
<div class="service-performance mb-4">
    <div class="row g-4">
        <!-- Top Performing Services -->
        <div class="col-xl-6 col-lg-6 col-md-12">
            <div class="performance-card">
                <div class="performance-header">
                    <h5 class="performance-title">
                        <i class="fas fa-star me-2"></i>Top Performing Services
                    </h5>
                </div>
                <div class="performance-content">
                    @foreach($topServices as $service)
                    <div class="performance-item">
                        <div class="performance-info">
                            <h6 class="service-name">{{ $service->service->name ?? 'Unknown Service' }}</h6>
                            <small class="service-stats">
                                {{ $service->booking_count }} bookings • KSh {{ number_format($service->total_revenue, 0) }}
                            </small>
                        </div>
                        <div class="performance-value">
                            <span class="revenue-amount">KSh {{ number_format($service->total_revenue, 0) }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        
        <!-- Staff Performance -->
        @if($staffPerformance && $staffPerformance->count() > 0)
        <div class="col-xl-6 col-lg-6 col-md-12">
            <div class="performance-card">
                <div class="performance-header">
                    <h5 class="performance-title">
                        <i class="fas fa-user-tie me-2"></i>Top Performing Staff
                    </h5>
                </div>
                <div class="performance-content">
                    @foreach($staffPerformance as $staff)
                    <div class="performance-item">
                        <div class="performance-info">
                            <h6 class="staff-name">{{ $staff->staff->name ?? 'Unknown Staff' }}</h6>
                            <small class="staff-stats">
                                {{ $staff->service_count }} services • KSh {{ number_format($staff->total_revenue, 0) }}
                            </small>
                        </div>
                        <div class="performance-value">
                            <span class="revenue-amount">KSh {{ number_format($staff->total_revenue, 0) }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endif

<!-- Charts Section -->
<div class="charts-section">
    <div class="row g-4">
        <!-- Left Side: Product/Service Sales Charts -->
        <div class="col-xl-8 col-lg-7 col-md-12">
            @if($topProducts && $topProducts->count() > 0)
            <!-- Product Sales Bar Chart -->
            <div class="chart-container mb-4">
                <div class="chart-header">
                    <h5 class="chart-title">
                        <i class="fas fa-chart-bar me-2"></i>Top Product Sales (Last 30 Days)
                    </h5>
                </div>
                <div class="chart-content">
                    <canvas id="productSalesChart"></canvas>
                </div>
            </div>
            @endif
            
            @if($topServices && $topServices->count() > 0)
            <!-- Service Sales Chart -->
            <div class="chart-container">
                <div class="chart-header">
                    <h5 class="chart-title">
                        <i class="fas fa-chart-line me-2"></i>Top Service Bookings
                    </h5>
                </div>
                <div class="chart-content">
                    <canvas id="serviceSalesChart"></canvas>
                </div>
            </div>
            @endif
            
            @if((!$topProducts || $topProducts->count() == 0) && (!$topServices || $topServices->count() == 0))
            <!-- Empty State -->
            <div class="chart-container">
                <div class="chart-header">
                    <h5 class="chart-title">Sales Analytics</h5>
                </div>
                <div class="text-center py-5">
                    <i class="fas fa-chart-bar fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No sales data available yet. Start selling to see analytics here!</p>
                </div>
            </div>
            @endif
        </div>
        
        <!-- Right Side: Top Customers -->
        <div class="col-xl-4 col-lg-5 col-md-12">
            <div class="chart-container">
                <div class="chart-header">
                    <h5 class="chart-title">
                        <i class="fas fa-users me-2"></i>Top Customers
                    </h5>
                </div>
                <div class="customer-list">
                    @if($topCustomers && $topCustomers->count() > 0)
                        @foreach($topCustomers as $customer)
                        <div class="customer-item d-flex align-items-center justify-content-between mb-3">
                            <div class="d-flex align-items-center">
                                <div class="customer-avatar me-3">
                                    <div class="avatar-circle">
                                        {{ strtoupper(substr($customer->name, 0, 2)) }}
                                    </div>
                                </div>
                                <div class="customer-info">
                                    <h6 class="mb-0">{{ $customer->name }}</h6>
                                    <small class="text-muted">
                                        @if($customer->orders_count > 0 && $customer->bookings_count > 0)
                                            {{ $customer->orders_count }} orders • {{ $customer->bookings_count }} bookings
                                        @elseif($customer->orders_count > 0)
                                            {{ $customer->orders_count }} {{ Str::plural('order', $customer->orders_count) }}
                                        @else
                                            {{ $customer->bookings_count }} {{ Str::plural('booking', $customer->bookings_count) }}
                                        @endif
                                    </small>
                                </div>
                            </div>
                            <div class="customer-spend">
                                <span class="badge bg-success">KSh {{ number_format($customer->total_spent, 0) }}</span>
                            </div>
                        </div>
                        @endforeach
                        
                        <a href="{{ route('sales.customers') }}" class="btn btn-primary w-100 mt-3">
                            <i class="fas fa-users me-2"></i>View All Customers
                        </a>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                            <p class="text-muted mb-3">No customers yet</p>
                            <a href="{{ route('sales.pos') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-cash-register me-2"></i>Make First Sale
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.charts-section {
    margin-top: 1rem;
}

.customer-list {
    max-height: 500px;
    overflow-y: auto;
}

.customer-item {
    padding: 0.75rem;
    border-radius: 0.5rem;
    transition: background-color 0.2s ease;
    border-bottom: 1px solid var(--border-color);
}

.customer-item:last-child {
    border-bottom: none;
}

.customer-item:hover {
    background-color: var(--gray-50);
}

.customer-avatar .avatar-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 0.875rem;
}

.customer-info h6 {
    font-size: 0.875rem;
    font-weight: 600;
    margin: 0;
    color: var(--text-primary);
}

.customer-info small {
    font-size: 0.75rem;
    color: var(--text-muted);
}

.customer-spend .badge {
    font-size: 0.75rem;
    font-weight: 600;
    padding: 0.375rem 0.75rem;
}

.chart-content {
    position: relative;
    height: 300px;
    width: 100%;
}

.balance-chart {
    text-align: center;
    height: 300px;
    position: relative;
}

.balance-info {
    font-size: 0.875rem;
}

.balance-info .legend-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    display: inline-block;
}

.legend-dot.investments {
    background: var(--primary-color);
}

.legend-dot.reinvestment {
    background: var(--primary-light);
}

/* Business Overview Cards */
.business-overview {
    margin-bottom: 2rem;
}

.overview-card {
    background: var(--card-bg);
    border: 1px solid var(--border-color);
    border-radius: 12px;
    padding: 1.5rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px var(--shadow-color);
}

.overview-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 16px var(--shadow-color);
}

.overview-icon {
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.5rem;
}

.overview-content {
    flex: 1;
}

.overview-value {
    font-size: 1.75rem;
    font-weight: 700;
    color: var(--text-primary);
    margin: 0;
    line-height: 1.2;
}

.overview-label {
    color: var(--text-muted);
    margin: 0;
    font-size: 0.875rem;
    font-weight: 500;
}

/* Quick Actions */
.quick-actions {
    margin-bottom: 2rem;
}

.action-card {
    background: var(--card-bg);
    border: 1px solid var(--border-color);
    border-radius: 12px;
    padding: 1.5rem;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px var(--shadow-color);
}

.action-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 16px var(--shadow-color);
}

.action-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.action-title {
    color: var(--text-primary);
    font-weight: 600;
    margin: 0;
    font-size: 1rem;
}

.action-content {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.action-stat {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.5rem 0;
    border-bottom: 1px solid var(--border-color);
}

.action-stat:last-child {
    border-bottom: none;
}

.stat-label {
    color: var(--text-muted);
    font-size: 0.875rem;
}

.stat-value {
    color: var(--text-primary);
    font-weight: 600;
    font-size: 0.875rem;
}

/* Enhanced Mobile Responsive Design */
@media (max-width: 1200px) {
    .kpi-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
    }
    
    .chart-content {
        height: 250px;
    }
    
    .balance-chart {
        height: 250px;
    }
}

@media (max-width: 768px) {
    .dashboard-grid {
        padding: 1rem;
    }
    
    .kpi-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
        margin-bottom: 1.5rem;
    }
    
    .overview-card {
        padding: 1rem;
    }
    
    .overview-icon {
        width: 50px;
        height: 50px;
        font-size: 1.25rem;
    }
    
    .overview-value {
        font-size: 1.5rem;
    }
    
    .action-card {
        padding: 1rem;
    }
    
    .action-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
    }
    
    .kpi-card {
        padding: 1.25rem;
    }
    
    .kpi-value {
        font-size: 1.75rem;
    }
    
    .kpi-title {
        font-size: 0.8rem;
    }
    
    .charts-section {
        margin-top: 0.5rem;
    }
    
    .chart-container {
        padding: 1.25rem;
        margin-bottom: 1rem;
    }
    
    .chart-header {
        flex-direction: column;
        gap: 0.75rem;
        align-items: flex-start;
        margin-bottom: 1rem;
    }
    
    .chart-legend {
        flex-wrap: wrap;
        gap: 0.5rem;
    }
    
    .legend-item {
        font-size: 0.8rem;
    }
    
    .chart-content {
        height: 200px;
    }
    
    .balance-chart {
        height: 200px;
    }
    
    .customer-list {
        max-height: 300px;
    }
    
    .customer-item {
        padding: 0.5rem;
    }
    
    .customer-avatar .avatar-circle {
        width: 32px;
        height: 32px;
        font-size: 0.75rem;
    }
    
    .customer-info h6 {
        font-size: 0.8rem;
    }
    
    .customer-info small {
        font-size: 0.7rem;
    }
    
    .customer-spend .badge {
        font-size: 0.7rem;
        padding: 0.25rem 0.5rem;
    }
    
    .balance-info {
        font-size: 0.8rem;
    }
}

@media (max-width: 480px) {
    .dashboard-grid {
        padding: 0.75rem;
    }
    
    .kpi-card {
        padding: 1rem;
    }
    
    .kpi-value {
        font-size: 1.5rem;
    }
    
    .kpi-title {
        font-size: 0.75rem;
    }
    
    .chart-container {
        padding: 1rem;
    }
    
    .chart-content {
        height: 180px;
    }
    
    .balance-chart {
        height: 180px;
    }
    
    .customer-list {
        max-height: 250px;
    }
    
    .customer-item {
        padding: 0.375rem;
    }
    
    .customer-avatar .avatar-circle {
        width: 28px;
        height: 28px;
        font-size: 0.7rem;
    }
    
    .customer-info h6 {
        font-size: 0.75rem;
    }
    
    .customer-info small {
        font-size: 0.65rem;
    }
    
    .customer-spend .badge {
        font-size: 0.65rem;
        padding: 0.2rem 0.4rem;
    }
    
    .balance-info {
        font-size: 0.75rem;
    }
    
    .legend-item {
        font-size: 0.7rem;
    }
}

/* Touch-friendly improvements */
@media (max-width: 768px) {
    .btn {
        min-height: 44px;
        padding: 0.75rem 1rem;
    }
    
    .customer-item {
        cursor: pointer;
        min-height: 48px;
    }
    
    .chart-container {
        border-radius: 0.75rem;
    }
    
    .kpi-card {
        border-radius: 0.75rem;
    }
}

/* Fix for blank space */
.dashboard-grid {
    min-height: auto;
    padding-bottom: 2rem;
}

.charts-section {
    margin-bottom: 0;
}

/* Ensure proper chart responsiveness */
canvas {
    max-width: 100%;
    height: auto !important;
}
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Get current theme
    const isDarkMode = document.body.getAttribute('data-theme') === 'dark';
    
    // Common chart colors
    const chartColors = {
        primary: '#020258',
        secondary: '#13e8e9',
        success: '#28a745',
        info: '#17a2b8',
        warning: '#ffc107',
        purple: '#6f42c1',
        orange: '#fd7e14',
        pink: '#e83e8c',
        red: '#dc3545',
        indigo: '#6610f2',
        teal: '#20c997',
        cyan: '#17a2b8'
    };
    
    // Array of vibrant colors for products
    const productColors = [
        '#28a745', // Green
        '#17a2b8', // Cyan
        '#ffc107', // Yellow
        '#6f42c1', // Purple
        '#fd7e14', // Orange
        '#e83e8c', // Pink
        '#dc3545', // Red
        '#6610f2', // Indigo
        '#20c997', // Teal
        '#020258', // Primary Blue
        '#13e8e9', // Secondary
        '#198754'  // Success variant
    ];
    
    // Base responsive chart options
    const baseChartOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: true,
                position: 'top',
                labels: {
                    color: isDarkMode ? '#cbd5e1' : '#475569',
                    padding: 15,
                    font: {
                        size: window.innerWidth < 768 ? 10 : 12
                    }
                }
            },
            tooltip: {
                backgroundColor: isDarkMode ? '#1e293b' : '#ffffff',
                titleColor: isDarkMode ? '#f1f5f9' : '#1e293b',
                bodyColor: isDarkMode ? '#cbd5e1' : '#475569',
                borderColor: isDarkMode ? '#334155' : '#e2e8f0',
                borderWidth: 1,
                padding: 12,
                displayColors: true
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    color: isDarkMode ? '#cbd5e1' : '#475569',
                    font: {
                        size: window.innerWidth < 768 ? 9 : 11
                    }
                },
                grid: {
                    color: isDarkMode ? '#334155' : '#e2e8f0'
                }
            },
            x: {
                ticks: {
                    color: isDarkMode ? '#cbd5e1' : '#475569',
                    font: {
                        size: window.innerWidth < 768 ? 9 : 11
                    },
                    maxRotation: 45,
                    minRotation: 0
                },
                grid: {
                    color: isDarkMode ? '#334155' : '#e2e8f0'
                }
            }
        }
    };
    
    // Product Sales Bar Chart
    @if($topProducts && $topProducts->count() > 0)
    const productSalesCtx = document.getElementById('productSalesChart');
    if (productSalesCtx) {
        const productsCount = {{ $topProducts->count() }};
        
        // Generate color arrays for each product
        const revenueColors = [];
        const unitColors = [];
        
        for (let i = 0; i < productsCount; i++) {
            // Use different shades for revenue vs units
            revenueColors.push(productColors[i % productColors.length]);
            // Create a lighter/transparent version for units
            const color = productColors[i % productColors.length];
            unitColors.push(color + '80'); // Add 50% transparency
        }
        
        const productSalesChart = new Chart(productSalesCtx.getContext('2d'), {
            type: 'bar',
            data: {
                labels: {!! json_encode($topProducts->pluck('name')->map(function($name) {
                    return strlen($name) > 20 ? substr($name, 0, 20) . '...' : $name;
                })) !!},
                datasets: [{
                    label: 'Revenue (KSh)',
                    data: {!! json_encode($topProducts->pluck('total_revenue')) !!},
                    backgroundColor: revenueColors,
                    borderColor: revenueColors,
                    borderWidth: 2
                }, {
                    label: 'Units Sold',
                    data: {!! json_encode($topProducts->pluck('total_sold')) !!},
                    backgroundColor: unitColors,
                    borderColor: revenueColors,
                    borderWidth: 1
                }]
            },
            options: {
                ...baseChartOptions,
                plugins: {
                    ...baseChartOptions.plugins,
                    legend: {
                        ...baseChartOptions.plugins.legend,
                        display: true,
                        labels: {
                            ...baseChartOptions.plugins.legend.labels,
                            generateLabels: function(chart) {
                                return [
                                    {
                                        text: 'Revenue (KSh)',
                                        fillStyle: chartColors.success,
                                        strokeStyle: chartColors.success,
                                        lineWidth: 2
                                    },
                                    {
                                        text: 'Units Sold',
                                        fillStyle: chartColors.info + '80',
                                        strokeStyle: chartColors.info,
                                        lineWidth: 1
                                    }
                                ];
                            }
                        }
                    },
                    tooltip: {
                        ...baseChartOptions.plugins.tooltip,
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.datasetIndex === 0) {
                                    label += 'KSh ' + context.parsed.y.toLocaleString();
                                } else {
                                    label += context.parsed.y.toLocaleString() + ' units';
                                }
                                return label;
                            }
                        }
                    }
                }
            }
        });
    }
    @endif
    
    // Service Sales Chart
    @if($topServices && $topServices->count() > 0)
    const serviceSalesCtx = document.getElementById('serviceSalesChart');
    if (serviceSalesCtx) {
        const serviceSalesChart = new Chart(serviceSalesCtx.getContext('2d'), {
            type: 'line',
            data: {
                labels: {!! json_encode($topServices->pluck('service.name')->map(function($name) {
                    return strlen($name) > 20 ? substr($name, 0, 20) . '...' : $name;
                })) !!},
                datasets: [{
                    label: 'Revenue (KSh)',
                    data: {!! json_encode($topServices->pluck('total_revenue')) !!},
                    borderColor: chartColors.purple,
                    backgroundColor: 'rgba(111, 66, 193, 0.1)',
                    tension: 0.4,
                    fill: true,
                    borderWidth: 2
                }, {
                    label: 'Bookings',
                    data: {!! json_encode($topServices->pluck('booking_count')) !!},
                    borderColor: chartColors.orange,
                    backgroundColor: 'rgba(253, 126, 20, 0.1)',
                    tension: 0.4,
                    fill: true,
                    borderWidth: 2
                }]
            },
            options: {
                ...baseChartOptions,
                plugins: {
                    ...baseChartOptions.plugins,
                    tooltip: {
                        ...baseChartOptions.plugins.tooltip,
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.datasetIndex === 0) {
                                    label += 'KSh ' + context.parsed.y.toLocaleString();
                                } else {
                                    label += context.parsed.y.toLocaleString() + ' bookings';
                                }
                                return label;
                            }
                        }
                    }
                }
            }
        });
    }
    @endif
    
    // Global function to update charts for theme changes
    window.updateChartsForTheme = function() {
        const isDarkMode = document.body.getAttribute('data-theme') === 'dark';
        const textColor = isDarkMode ? '#cbd5e1' : '#475569';
        const gridColor = isDarkMode ? '#334155' : '#e2e8f0';
        
        // This function can be called when theme changes
        // You would need to recreate or update all charts here
        console.log('Theme changed to:', isDarkMode ? 'dark' : 'light');
    };
});
</script>

<style>
/* New Dashboard Styles */
.overview-subtitle {
    color: var(--text-muted);
    font-size: 0.75rem;
    margin-top: 0.25rem;
}

.product-card {
    border-left: 4px solid #28a745;
}

.service-card {
    border-left: 4px solid #17a2b8;
}

.revenue-card {
    border-left: 4px solid #ffc107;
}

.customer-card {
    border-left: 4px solid #6f42c1;
}

.metric-card {
    background: var(--card-bg);
    border: 1px solid var(--border-color);
    border-radius: 12px;
    padding: 1.5rem;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px var(--shadow-color);
}

.metric-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 16px var(--shadow-color);
}

.metric-header {
    margin-bottom: 1rem;
}

.metric-title {
    color: var(--text-primary);
    font-weight: 600;
    margin: 0;
    font-size: 1rem;
}

.metric-content {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.metric-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.5rem 0;
    border-bottom: 1px solid var(--border-color);
}

.metric-row:last-child {
    border-bottom: none;
}

.metric-label {
    color: var(--text-muted);
    font-size: 0.875rem;
}

.metric-value {
    color: var(--text-primary);
    font-weight: 600;
    font-size: 0.875rem;
}

.product-action {
    border-left: 4px solid #28a745;
}

.service-action {
    border-left: 4px solid #17a2b8;
}

.performance-card {
    background: var(--card-bg);
    border: 1px solid var(--border-color);
    border-radius: 12px;
    padding: 1.5rem;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px var(--shadow-color);
}

.performance-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 16px var(--shadow-color);
}

.performance-header {
    margin-bottom: 1rem;
}

.performance-title {
    color: var(--text-primary);
    font-weight: 600;
    margin: 0;
    font-size: 1rem;
}

.performance-content {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.performance-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem;
    background: rgba(255, 255, 255, 0.05);
    border-radius: 8px;
    transition: all 0.3s ease;
}

.performance-item:hover {
    background: rgba(255, 255, 255, 0.1);
}

.performance-info {
    flex: 1;
}

.service-name, .staff-name {
    color: var(--text-primary);
    font-weight: 600;
    margin: 0;
    font-size: 0.9rem;
}

.service-stats, .staff-stats {
    color: var(--text-muted);
    font-size: 0.75rem;
    margin-top: 0.25rem;
}

.performance-value {
    text-align: right;
}

.revenue-amount {
    color: var(--text-primary);
    font-weight: 600;
    font-size: 0.875rem;
}

/* Mobile Responsive for New Elements */
@media (max-width: 768px) {
    .metric-card, .performance-card {
        margin-bottom: 1rem;
    }
    
    .performance-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
    }
    
    .performance-value {
        text-align: left;
    }
}
</style>

<!-- AI Logo Generation Modal -->
<div id="logoGeneratorModal" class="modal fade" tabindex="-1" style="display: none;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border: 2px solid #13e8e9; border-radius: 15px;">
            <div class="modal-header" style="background: linear-gradient(135deg, #020258, #13e8e9); color: white; border-radius: 13px 13px 0 0;">
                <h5 class="modal-title"><i class="fas fa-magic"></i> Generate Business Logo with AI</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="padding: 2rem;">
                <p class="text-muted mb-3">Choose a logo style and let AI create a unique logo for your business.</p>
                
                <div class="mb-3">
                    <label for="logoStyle" class="form-label"><i class="fas fa-palette"></i> Logo Style</label>
                    <select id="logoStyle" class="form-select">
                        <option value="modern">Modern - Clean and contemporary</option>
                        <option value="classic">Classic - Timeless and elegant</option>
                        <option value="minimal">Minimal - Simple and essential</option>
                        <option value="bold">Bold - Strong and vibrant</option>
                        <option value="playful">Playful - Fun and creative</option>
                        <option value="corporate">Corporate - Professional and formal</option>
                    </select>
                </div>
                
                <div id="generationStatus" class="alert alert-info" style="display: none;">
                    <i class="fas fa-spinner fa-spin"></i> <span id="statusMessage">Generating your logo...</span>
                </div>
                
                <div id="generatedLogoContainer" style="display: none; text-align: center; margin-top: 20px;">
                    <img id="generatedLogoPreview" src="" alt="Generated Logo" style="max-width: 300px; max-height: 300px; border-radius: 8px; border: 2px solid #e5e7eb; background: #f9fafb; padding: 20px;" />
                    <div class="mt-3">
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="regenerateLogoFromModal()">
                            <i class="fas fa-redo"></i> Regenerate
                        </button>
                        <button type="button" class="btn btn-sm btn-success" onclick="saveGeneratedLogo()">
                            <i class="fas fa-check"></i> Use This Logo
                        </button>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" id="generateLogoModalBtn" class="btn btn-primary" onclick="generateLogoFromModal()">
                    <i class="fas fa-magic"></i> Generate Logo
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let generatedLogoPath = null;
let logoGeneratorModal = null;

document.addEventListener('DOMContentLoaded', function() {
    logoGeneratorModal = new bootstrap.Modal(document.getElementById('logoGeneratorModal'));
});

function openLogoGenerator() {
    logoGeneratorModal.show();
    document.getElementById('generatedLogoContainer').style.display = 'none';
    document.getElementById('generationStatus').style.display = 'none';
}

async function generateLogoFromModal() {
    const style = document.getElementById('logoStyle').value;
    const statusDiv = document.getElementById('generationStatus');
    const statusMessage = document.getElementById('statusMessage');
    const generateBtn = document.getElementById('generateLogoModalBtn');
    const logoContainer = document.getElementById('generatedLogoContainer');
    
    statusDiv.style.display = 'block';
    statusMessage.textContent = 'Generating your logo... This may take 30-60 seconds.';
    generateBtn.disabled = true;
    logoContainer.style.display = 'none';
    
    try {
        const businessName = {!! json_encode(Auth::user()->business->name ?? '') !!};
        const businessDescription = {!! json_encode(Auth::user()->business->description ?? '') !!};
        const businessType = {!! json_encode(Auth::user()->business->business_type ?? '') !!};
        
        const response = await fetch('{{ route("business.generate-logo") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                business_name: businessName,
                business_description: businessDescription || 'A professional ' + businessType + ' business providing quality services',
                business_type: businessType,
                logo_style: style
            }),
            credentials: 'same-origin'
        });
        
        // Check for redirect (authentication failure)
        if (response.redirected) {
            console.error('❌ REQUEST REDIRECTED TO:', response.url);
            throw new Error('Your session has expired. Please refresh the page and log in again.');
        }
        
        // Check if response is ok before parsing
        if (!response.ok) {
            const errorText = await response.text();
            console.error('❌ SERVER ERROR:', response.status, errorText.substring(0, 500));
            throw new Error(`Server error (${response.status}). Please try again or contact support.`);
        }
        
        // Get response text first to debug
        const responseText = await response.text();
        console.log('=== LOGO GENERATION RESPONSE DEBUG ===');
        console.log('Response length:', responseText.length);
        console.log('First 500 chars:', responseText.substring(0, 500));
        console.log('Last 200 chars:', responseText.substring(Math.max(0, responseText.length - 200)));
        console.log('Is HTML?', responseText.trim().startsWith('<!DOCTYPE') || responseText.trim().startsWith('<html'));
        console.log('======================================');
        
        let data;
        try {
            data = JSON.parse(responseText);
            console.log('✓ JSON parsed successfully:', data);
        } catch (parseError) {
            console.error('✗ JSON PARSE ERROR:', parseError.message);
            console.error('Full response text:', responseText);
            
            // Try to identify the issue
            if (responseText.trim().startsWith('<!DOCTYPE') || responseText.trim().startsWith('<html')) {
                throw new Error('Server returned HTML instead of JSON. Check server logs for errors.');
            } else if (responseText.trim() === '') {
                throw new Error('Server returned empty response. Check server logs.');
            } else if (!responseText.trim().startsWith('{')) {
                throw new Error(`Server response doesn't start with JSON. Starts with: "${responseText.substring(0, 50)}"`);
            } else {
                throw new Error('Invalid JSON format from server. Check console for details.');
            }
        }
        
        if (data.success) {
            statusDiv.style.display = 'none';
            document.getElementById('generatedLogoPreview').src = data.logo_url;
            generatedLogoPath = data.logo_path;
            logoContainer.style.display = 'block';
        } else {
            statusDiv.className = 'alert alert-danger';
            statusMessage.innerHTML = '<i class="fas fa-exclamation-triangle"></i> ' + (data.message || 'Failed to generate logo');
        }
    } catch (error) {
        console.error('Logo generation error:', error);
        statusDiv.className = 'alert alert-danger';
        statusMessage.textContent = 'Error: ' + error.message;
    } finally {
        generateBtn.disabled = false;
    }
}

function regenerateLogoFromModal() {
    generateLogoFromModal();
}

async function saveGeneratedLogo() {
    if (!generatedLogoPath) {
        alert('No logo generated yet');
        return;
    }
    
    const statusDiv = document.getElementById('generationStatus');
    const statusMessage = document.getElementById('statusMessage');
    
    statusDiv.style.display = 'block';
    statusDiv.className = 'alert alert-info';
    statusMessage.textContent = 'Saving logo to your business profile...';
    
    try {
        const formData = new FormData();
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
        formData.append('_method', 'PUT');
        formData.append('generated_logo_path', generatedLogoPath);
        formData.append('name', {!! json_encode(Auth::user()->business->name ?? '') !!});
        formData.append('business_type', {!! json_encode(Auth::user()->business->business_type ?? '') !!});
        formData.append('phone', {!! json_encode(Auth::user()->business->phone ?? '') !!});
        formData.append('address', {!! json_encode(Auth::user()->business->address ?? '') !!});
        formData.append('city', {!! json_encode(Auth::user()->business->city ?? '') !!});
        
        const response = await fetch('{{ route("business.update") }}', {
            method: 'POST',
            body: formData
        });
        
        if (response.ok) {
            statusDiv.className = 'alert alert-success';
            statusMessage.innerHTML = '<i class="fas fa-check"></i> Logo saved successfully! Refreshing page...';
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            throw new Error('Failed to save logo');
        }
    } catch (error) {
        statusDiv.className = 'alert alert-danger';
        statusMessage.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Error saving logo: ' + error.message;
    }
}

// Auto-show subscription modal for non-enterprise users
@if(Auth::user()->business && !Auth::user()->business->isEnterprise())
document.addEventListener('DOMContentLoaded', function() {
    // Check if modal was dismissed in this session
    const modalDismissed = sessionStorage.getItem('subscriptionModalDismissed');
    
    // Check last shown time in localStorage (persistent across sessions)
    const lastShown = localStorage.getItem('subscriptionModalLastShown');
    const now = new Date().getTime();
    const threeDays = 3 * 24 * 60 * 60 * 1000; // 3 days in milliseconds
    
    // Show modal if:
    // 1. Not dismissed in this session
    // 2. Either never shown before OR last shown more than 3 days ago
    if (!modalDismissed && (!lastShown || (now - parseInt(lastShown)) > threeDays)) {
        // Wait 10 seconds after page load before showing modal
        setTimeout(function() {
            const modal = new bootstrap.Modal(document.getElementById('subscriptionUpgradeModal'));
            modal.show();
            
            // Record when modal was shown
            localStorage.setItem('subscriptionModalLastShown', now.toString());
        }, 10000); // 10 seconds delay
    }
    
    // Mark as dismissed when user closes the modal
    document.getElementById('subscriptionUpgradeModal')?.addEventListener('hidden.bs.modal', function() {
        sessionStorage.setItem('subscriptionModalDismissed', 'true');
    });
});
@endif
</script>

<!-- Subscription Upgrade Modal (Auto-popup for non-enterprise users) -->
@if(Auth::user()->business && !Auth::user()->business->isEnterprise())
<div class="modal fade" id="subscriptionUpgradeModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="true" data-bs-keyboard="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border: none; border-radius: 16px; overflow: hidden; box-shadow: 0 20px 60px rgba(0,0,0,0.3);">
            <!-- Header with gradient -->
            <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; padding: 2rem; position: relative; overflow: hidden;">
                <!-- Decorative elements -->
                <div style="position: absolute; top: -50px; right: -50px; width: 200px; height: 200px; background: rgba(255,255,255,0.1); border-radius: 50%;"></div>
                <div style="position: absolute; bottom: -30px; left: -30px; width: 150px; height: 150px; background: rgba(255,255,255,0.08); border-radius: 50%;"></div>
                
                <div style="position: relative; z-index: 1; width: 100%;">
                    <div class="text-center">
                        <div style="font-size: 3rem; margin-bottom: 0.5rem;">
                            <i class="fas fa-crown text-warning"></i>
                        </div>
                        <h4 class="mb-1" style="color: white; font-weight: 700;">Upgrade Your Plan</h4>
                        <p class="mb-0" style="color: rgba(255,255,255,0.9);">Unlock powerful features - Pay with M-Pesa via Paystack</p>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" style="position: absolute; top: 1rem; right: 1rem; z-index: 2;"></button>
            </div>
            
            <!-- Body -->
            <div class="modal-body p-4">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                
                <form action="{{ route('subscription.upgrade') }}" method="POST" id="dashboardUpgradeForm">
                    @csrf
                    
                    <!-- Plan Selection -->
                    <h6 class="mb-3">Choose Your Plan:</h6>
                    <div class="row g-3 mb-4">
                        <!-- Premium Plan -->
                        <div class="col-md-6">
                            <div class="card plan-card-dashboard h-100" data-plan="premium" style="cursor: pointer; transition: all 0.3s; border: 2px solid #e5e7eb;">
                                <div class="card-body text-center p-4">
                                    <div class="mb-3">
                                        <i class="fas fa-star fa-2x text-primary"></i>
                                    </div>
                                    <h5 class="mb-2">Premium Plan</h5>
                                    <h3 class="mb-1">
                                        <span style="font-size: 2rem; font-weight: 700; color: #667eea;">KSH 5</span>
                                        <span class="text-muted">/month</span>
                                    </h3>
                                    <p class="text-muted small mb-3">Perfect for growing businesses</p>
                                    <ul class="list-unstyled text-start small">
                                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>AI Website Builder</li>
                                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Advanced Analytics</li>
                                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Email Support</li>
                                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Up to 1,000 products</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Enterprise Plan -->
                        <div class="col-md-6">
                            <div class="card plan-card-dashboard h-100" data-plan="enterprise" style="cursor: pointer; transition: all 0.3s; border: 2px solid #10b981;">
                                <div class="card-body text-center p-4 position-relative">
                                    <span class="badge bg-success position-absolute top-0 end-0 m-2">Most Popular</span>
                                    <div class="mb-3">
                                        <i class="fas fa-crown fa-2x text-warning"></i>
                                    </div>
                                    <h5 class="mb-2">Enterprise Plan</h5>
                                    <h3 class="mb-1">
                                        <span style="font-size: 2rem; font-weight: 700; color: #10b981;">KSH 10</span>
                                        <span class="text-muted">/month</span>
                                    </h3>
                                    <p class="text-muted small mb-3">Everything you need to scale</p>
                                    <ul class="list-unstyled text-start small">
                                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Everything in Premium</li>
                                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Priority Support (2hrs)</li>
                                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Unlimited Products</li>
                                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Custom Branding</li>
                                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>API Access</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <input type="hidden" name="plan" id="selectedPlanDashboard" value="enterprise" required>
                    
                    <!-- Payment Details -->
                    <h6 class="mb-3">M-Pesa Payment Details:</h6>
                    <div class="mb-3">
                        <label for="phoneNumberDashboard" class="form-label">M-Pesa Phone Number <span class="text-danger">*</span></label>
                        <input type="tel" class="form-control" id="phoneNumberDashboard" name="phone_number" 
                               placeholder="07XXXXXXXX or 254XXXXXXXXX" 
                               pattern="^(254|0)[17]\d{8}$"
                               required>
                        <div class="form-text">Enter your Safaricom M-Pesa number</div>
                    </div>

                    <div class="alert alert-info">
                        <i class="fas fa-mobile-alt me-2"></i>
                        <strong>How it works:</strong>
                        <ol class="mb-0 mt-2 ps-3 small">
                            <li>Select your preferred plan above</li>
                            <li>Enter your M-Pesa phone number</li>
                            <li>Click "Pay with M-Pesa" below</li>
                            <li>You'll receive an STK Push prompt on your phone</li>
                            <li>Enter your M-Pesa PIN to authorize payment</li>
                            <li>Your plan will be upgraded instantly!</li>
                        </ol>
                    </div>

                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="agreeTermsDashboard" required>
                        <label class="form-check-label" for="agreeTermsDashboard">
                            I agree to the subscription terms and M-Pesa payment will be processed via Paystack
                        </label>
                    </div>
                </form>
            </div>
            
            <!-- Footer -->
            <div class="modal-footer" style="border: none; padding: 1.5rem 2rem;">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="dashboardUpgradeForm" class="btn btn-success btn-lg" style="font-weight: 700; padding: 0.75rem 2rem;">
                    <i class="fas fa-mobile-alt me-2"></i>Pay with M-Pesa
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Payment Status Modal (Small) -->
<div class="modal fade" id="paymentStatusModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content" style="border: none; border-radius: 12px; overflow: hidden;">
            <div class="modal-body text-center p-4" id="paymentStatusContent">
                <!-- Dynamic content will be inserted here -->
            </div>
        </div>
    </div>
</div>

<script>
// Plan selection for dashboard upgrade modal
document.addEventListener('DOMContentLoaded', function() {
    const planCards = document.querySelectorAll('.plan-card-dashboard');
    const selectedPlanInput = document.getElementById('selectedPlanDashboard');
    
    if (planCards.length > 0 && selectedPlanInput) {
        // Set Enterprise as default selection
        const enterpriseCard = document.querySelector('.plan-card-dashboard[data-plan="enterprise"]');
        if (enterpriseCard) {
            enterpriseCard.style.borderColor = '#10b981';
            enterpriseCard.style.boxShadow = '0 4px 16px rgba(16, 185, 129, 0.3)';
            enterpriseCard.style.transform = 'scale(1.02)';
        }
        
        planCards.forEach(card => {
            card.addEventListener('click', function() {
                const plan = this.getAttribute('data-plan');
                
                // Remove selection from all cards
                planCards.forEach(c => {
                    c.style.borderColor = '#e5e7eb';
                    c.style.boxShadow = 'none';
                    c.style.transform = 'scale(1)';
                });
                
                // Highlight selected card
                if (plan === 'premium') {
                    this.style.borderColor = '#667eea';
                    this.style.boxShadow = '0 4px 16px rgba(102, 126, 234, 0.3)';
                } else {
                    this.style.borderColor = '#10b981';
                    this.style.boxShadow = '0 4px 16px rgba(16, 185, 129, 0.3)';
                }
                this.style.transform = 'scale(1.02)';
                
                // Update hidden input
                selectedPlanInput.value = plan;
            });
        });
    }
    
    // Check if there's a payment_reference from M-Pesa payment initiation
    @if(session('payment_reference'))
        const paymentReference = '{{ session("payment_reference") }}';
        startPaymentStatusPolling(paymentReference);
    @endif
    
    // Handle form submission via AJAX to prevent page reload
    const upgradeForm = document.getElementById('dashboardUpgradeForm');
    if (upgradeForm) {
        upgradeForm.addEventListener('submit', function(e) {
            e.preventDefault(); // Prevent default form submission
            
            console.log('Form action URL:', this.action);
            console.log('Form method:', this.method);
            
            const formData = new FormData(this);
            console.log('Form data entries:');
            for (let [key, value] of formData.entries()) {
                console.log(key + ':', value);
            }
            
            const submitButton = document.querySelector('button[form="dashboardUpgradeForm"]');
            
            // Disable submit button
            if (submitButton) {
                submitButton.disabled = true;
                submitButton.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Sending STK Push...';
            }
            
            // Submit via AJAX
            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                console.log('Response status:', response.status);
                console.log('Response content-type:', response.headers.get('content-type'));
                
                // Check if response is JSON
                const contentType = response.headers.get('content-type');
                if (contentType && contentType.includes('application/json')) {
                    return response.json().then(data => ({
                        ok: response.ok,
                        status: response.status,
                        data: data
                    }));
                } else {
                    // Response is HTML, likely an error page
                    return response.text().then(html => {
                        console.error('Received HTML instead of JSON:', html.substring(0, 500));
                        throw new Error('Server returned HTML instead of JSON. Check if you are logged in and the route is correct.');
                    });
                }
            })
            .then(result => {
                console.log('Parsed result:', result);
                
                if (result.ok && result.data.success) {
                    console.log('Payment initiated successfully:', result.data);
                    // Start polling with the reference from response
                    startPaymentStatusPolling(result.data.reference);
                } else {
                    // Show error message
                    const errorMsg = result.data.message || result.data.error || 'Failed to initiate payment';
                    alert('Error: ' + errorMsg);
                    console.error('Payment initiation failed:', result.data);
                    
                    // Re-enable submit button
                    if (submitButton) {
                        submitButton.disabled = false;
                        submitButton.innerHTML = '<i class="fas fa-mobile-alt me-2"></i>Pay with M-Pesa';
                    }
                }
            })
            .catch(error => {
                console.error('Error submitting payment:', error);
                alert('An error occurred while initiating payment. Please check console for details.');
                
                // Re-enable submit button
                if (submitButton) {
                    submitButton.disabled = false;
                    submitButton.innerHTML = '<i class="fas fa-mobile-alt me-2"></i>Pay with M-Pesa';
                }
            });
        });
    }
});

// Payment status polling for M-Pesa via Paystack
let pollingInterval = null;
let pollingAttempts = 0;
const MAX_POLLING_ATTEMPTS = 90; // Poll for 3 minutes (90 attempts * 2 seconds)
let modalInstance = null;

function startPaymentStatusPolling(reference) {
    if (!reference) return;
    
    console.log('Starting M-Pesa payment status polling for:', reference);
    
    pollingAttempts = 0;
    
    // Get or create modal instance and prevent closing
    const modalElement = document.getElementById('subscriptionUpgradeModal');
    if (modalElement) {
        modalInstance = bootstrap.Modal.getInstance(modalElement) || new bootstrap.Modal(modalElement);
        
        // Prevent modal from closing during payment processing
        modalElement.setAttribute('data-bs-backdrop', 'static');
        modalElement.setAttribute('data-bs-keyboard', 'false');
        
        // Hide the close button
        const closeButton = modalElement.querySelector('.btn-close');
        if (closeButton) closeButton.style.display = 'none';
        
        // Disable the cancel button
        const cancelButton = modalElement.querySelector('[data-bs-dismiss="modal"]');
        if (cancelButton) {
            cancelButton.disabled = true;
            cancelButton.style.opacity = '0.5';
        }
    }
    
    // Show loading message in modal
    let statusAlert = document.getElementById('paymentStatusAlert');
    if (!statusAlert) {
        statusAlert = document.createElement('div');
        statusAlert.id = 'paymentStatusAlert';
        statusAlert.className = 'alert alert-info';
        statusAlert.style.marginBottom = '1rem';
        
        const formElement = document.getElementById('dashboardUpgradeForm');
        if (formElement) {
            formElement.insertAdjacentElement('beforebegin', statusAlert);
        }
    }
    
    // Update status with animated spinner
    statusAlert.innerHTML = `
        <div class="d-flex align-items-center">
            <div class="spinner-border spinner-border-sm text-primary me-3" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <div>
                <strong><i class="fas fa-mobile-alt me-2"></i>Waiting for M-Pesa payment...</strong>
                <p class="mb-0 small mt-1">Please check your phone and enter your M-Pesa PIN to complete the payment. This may take a few moments.</p>
            </div>
        </div>
    `;
    
    // Disable the payment form
    const submitButton = document.querySelector('button[form="dashboardUpgradeForm"]');
    if (submitButton) {
        submitButton.disabled = true;
        submitButton.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';
    }
    
    const phoneInput = document.getElementById('phoneNumberDashboard');
    if (phoneInput) phoneInput.disabled = true;
    
    const planCards = document.querySelectorAll('.plan-card-dashboard');
    planCards.forEach(card => card.style.pointerEvents = 'none');
    
    // Clear any existing polling interval
    if (pollingInterval) {
        clearInterval(pollingInterval);
    }
    
    // Start polling every 2 seconds
    pollingInterval = setInterval(() => {
        pollingAttempts++;
        
        // Update status message with attempt count
        const timeElapsed = Math.floor(pollingAttempts * 2 / 60);
        const secondsElapsed = pollingAttempts * 2 % 60;
        statusAlert.innerHTML = `
            <div class="d-flex align-items-center">
                <div class="spinner-border spinner-border-sm text-primary me-3" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <div>
                    <strong><i class="fas fa-mobile-alt me-2"></i>Waiting for M-Pesa payment...</strong>
                    <p class="mb-0 small mt-1">Checking status... ${timeElapsed}m ${secondsElapsed}s elapsed. Please enter your M-Pesa PIN if you haven't already.</p>
                </div>
            </div>
        `;
        
        if (pollingAttempts > MAX_POLLING_ATTEMPTS) {
            stopPaymentStatusPolling();
            updateStatusAlert('warning', `
                <i class="fas fa-clock me-2"></i>
                <strong>Payment verification timed out</strong>
                <p class="mb-0 small mt-1">Your payment may still be processing. Please check your M-Pesa messages or contact support if money was deducted.</p>
            `, true);
            enableModalControls();
            return;
        }
        
        fetch('{{ route("subscription.check.status") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                reference: reference
            })
        })
        .then(response => response.json())
        .then(data => {
            console.log('Payment status:', data);
            
            if (data.status === 'completed') {
                stopPaymentStatusPolling();
                
                // Close main modal and show success modal
                const mainModal = bootstrap.Modal.getInstance(document.getElementById('subscriptionUpgradeModal'));
                if (mainModal) mainModal.hide();
                
                showPaymentStatusModal('success', 'Payment Successful!', 
                    `Your plan has been upgraded to <strong>${data.plan.toUpperCase()}</strong>.<br>Redirecting...`);
                
                // Reload page after 3 seconds to show updated plan
                setTimeout(() => {
                    window.location.href = '{{ route("dashboard") }}';
                }, 3000);
                
            } else if (data.status === 'failed') {
                stopPaymentStatusPolling();
                
                // Close main modal and show failure modal
                const mainModal = bootstrap.Modal.getInstance(document.getElementById('subscriptionUpgradeModal'));
                if (mainModal) mainModal.hide();
                
                const failureReason = data.result_desc || 'The payment could not be completed';
                showPaymentStatusModal('error', 'Payment Failed', failureReason, true);
            }
            // If status is still 'pending', continue polling
        })
        .catch(error => {
            console.error('Error checking payment status:', error);
        });
    }, 2000); // Poll every 2 seconds
}

function stopPaymentStatusPolling() {
    if (pollingInterval) {
        clearInterval(pollingInterval);
        pollingInterval = null;
    }
}

function enableModalControls() {
    // Re-enable modal closing
    const modalElement = document.getElementById('subscriptionUpgradeModal');
    if (modalElement) {
        modalElement.setAttribute('data-bs-backdrop', 'true');
        modalElement.setAttribute('data-bs-keyboard', 'true');
        
        // Show the close button
        const closeButton = modalElement.querySelector('.btn-close');
        if (closeButton) closeButton.style.display = 'block';
        
        // Enable the cancel button
        const cancelButton = modalElement.querySelector('[data-bs-dismiss="modal"]');
        if (cancelButton) {
            cancelButton.disabled = false;
            cancelButton.style.opacity = '1';
        }
    }
    
    // Re-enable form controls
    const submitButton = document.querySelector('button[form="dashboardUpgradeForm"]');
    if (submitButton) {
        submitButton.disabled = false;
        submitButton.innerHTML = '<i class="fas fa-mobile-alt me-2"></i>Pay with M-Pesa';
    }
    
    const phoneInput = document.getElementById('phoneNumberDashboard');
    if (phoneInput) phoneInput.disabled = false;
    
    const planCards = document.querySelectorAll('.plan-card-dashboard');
    planCards.forEach(card => card.style.pointerEvents = 'auto');
}

function updateStatusAlert(type, message, allowDismiss) {
    const statusAlert = document.getElementById('paymentStatusAlert');
    if (statusAlert) {
        statusAlert.className = 'alert alert-' + type + (allowDismiss ? ' alert-dismissible fade show' : '');
        statusAlert.innerHTML = message + (allowDismiss ? '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' : '');
    }
}

function showPaymentStatusModal(type, title, message, allowRetry = false) {
    const statusContent = document.getElementById('paymentStatusContent');
    const statusModal = new bootstrap.Modal(document.getElementById('paymentStatusModal'));
    
    let icon = '';
    let iconColor = '';
    let bgColor = '';
    
    if (type === 'success') {
        icon = 'fa-check-circle';
        iconColor = 'text-success';
        bgColor = '#d4edda';
    } else if (type === 'error') {
        icon = 'fa-times-circle';
        iconColor = 'text-danger';
        bgColor = '#f8d7da';
    } else {
        icon = 'fa-exclamation-triangle';
        iconColor = 'text-warning';
        bgColor = '#fff3cd';
    }
    
    let buttonHtml = '';
    if (allowRetry) {
        buttonHtml = '<button type="button" class="btn btn-primary btn-sm mt-3" onclick="location.reload()">Try Again</button>';
    }
    
    statusContent.innerHTML = `
        <div style="background: ${bgColor}; padding: 1.5rem; border-radius: 8px;">
            <div style="font-size: 3rem; margin-bottom: 1rem;">
                <i class="fas ${icon} ${iconColor}"></i>
            </div>
            <h5 class="mb-2">${title}</h5>
            <p class="mb-0 small text-muted">${message}</p>
            ${buttonHtml}
        </div>
    `;
    
    statusModal.show();
    
    // Auto-close on success after showing for a moment
    if (type === 'success') {
        setTimeout(() => {
            statusModal.hide();
        }, 2500);
    }
}

</script>
@endif

@endsection
