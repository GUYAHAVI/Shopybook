@extends('layouts.dash')
@section('title', 'Dashboard')

@section('content')
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
        <div class="col-xl-8 col-lg-7 col-md-12">
            <div class="chart-container">
                <div class="chart-header">
                    <h5 class="chart-title">Cash Flow</h5>
                    <div class="chart-legend">
                        <div class="legend-item">
                            <div class="legend-dot investments"></div>
                            <span>Investments</span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-dot reinvestment"></div>
                            <span>Reinvestment</span>
                        </div>
                    </div>
                </div>
                <div class="chart-content">
                    <canvas id="cashFlowChart"></canvas>
                </div>
            </div>
        </div>
        
        <div class="col-xl-4 col-lg-5 col-md-12">
            <div class="row g-4">
                <div class="col-12">
                    <div class="chart-container">
                        <div class="chart-header">
                            <h5 class="chart-title">Investor Reports</h5>
                        </div>
                        <div class="investor-list">
                            <div class="investor-item d-flex align-items-center mb-3">
                                <div class="investor-avatar me-3">
                                    <img src="https://via.placeholder.com/40x40" alt="Jakob Jones" class="rounded-circle">
                                </div>
                                <div class="investor-info">
                                    <h6 class="mb-0">Jakob Jones</h6>
                                    <small class="text-muted">CHIEF EXECUTIVE OFFICER</small>
                                </div>
                            </div>
                            
                            <div class="investor-item d-flex align-items-center mb-3">
                                <div class="investor-avatar me-3">
                                    <img src="https://via.placeholder.com/40x40" alt="Wode Warren" class="rounded-circle">
                                </div>
                                <div class="investor-info">
                                    <h6 class="mb-0">Wode Warren</h6>
                                    <small class="text-muted">MANAGING DIRECTOR</small>
                                </div>
                            </div>
                            
                            <div class="investor-item d-flex align-items-center mb-3">
                                <div class="investor-avatar me-3">
                                    <img src="https://via.placeholder.com/40x40" alt="Barbara John" class="rounded-circle">
                                </div>
                                <div class="investor-info">
                                    <h6 class="mb-0">Barbara John</h6>
                                    <small class="text-muted">TRADER</small>
                                </div>
                            </div>
                            
                            <button class="btn btn-primary w-100 mt-3">
                                <i class="fas fa-search me-2"></i>Search
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="col-12">
                    <div class="chart-container">
                        <div class="chart-header">
                            <h5 class="chart-title">Total Balance</h5>
                            <i class="fas fa-plus"></i>
                        </div>
                        <div class="balance-chart">
                            <canvas id="balanceChart"></canvas>
                            <div class="balance-info mt-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div class="d-flex align-items-center">
                                        <div class="legend-dot investments me-2"></div>
                                        <span>$57.436 Investments</span>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center">
                                        <div class="legend-dot reinvestment me-2"></div>
                                        <span>$40.564 Reinvestment</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.charts-section {
    margin-top: 1rem;
}

.investor-list {
    max-height: 300px;
    overflow-y: auto;
}

.investor-item {
    padding: 0.75rem;
    border-radius: 0.5rem;
    transition: background-color 0.2s ease;
}

.investor-item:hover {
    background-color: var(--gray-50);
}

.investor-avatar img {
    width: 40px;
    height: 40px;
    object-fit: cover;
}

.investor-info h6 {
    font-size: 0.875rem;
    font-weight: 600;
    margin: 0;
}

.investor-info small {
    font-size: 0.75rem;
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
    
    .investor-list {
        max-height: 200px;
    }
    
    .investor-item {
        padding: 0.5rem;
    }
    
    .investor-avatar img {
        width: 32px;
        height: 32px;
    }
    
    .investor-info h6 {
        font-size: 0.8rem;
    }
    
    .investor-info small {
        font-size: 0.7rem;
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
    
    .investor-list {
        max-height: 150px;
    }
    
    .investor-item {
        padding: 0.375rem;
    }
    
    .investor-avatar img {
        width: 28px;
        height: 28px;
    }
    
    .investor-info h6 {
        font-size: 0.75rem;
    }
    
    .investor-info small {
        font-size: 0.65rem;
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
    
    .investor-item {
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
    
    // Responsive chart options
    const responsiveOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                max: 50,
                ticks: {
                    callback: function(value) {
                        return value + 'K';
                    },
                    color: isDarkMode ? '#cbd5e1' : '#475569'
                },
                grid: {
                    color: isDarkMode ? '#334155' : '#e2e8f0'
                }
            },
            x: {
                ticks: {
                    color: isDarkMode ? '#cbd5e1' : '#475569'
                },
                grid: {
                    color: isDarkMode ? '#334155' : '#e2e8f0'
                }
            }
        },
        elements: {
            point: {
                radius: window.innerWidth < 768 ? 3 : 4,
                hoverRadius: window.innerWidth < 768 ? 5 : 6
            }
        }
    };
    
    // Cash Flow Chart
    const cashFlowCtx = document.getElementById('cashFlowChart').getContext('2d');
    const cashFlowChart = new Chart(cashFlowCtx, {
        type: 'line',
        data: {
            labels: ['JUL', 'JUN', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC', 'JAN'],
            datasets: [{
                label: 'Investments',
                data: [10, 15, 20, 35, 25, 30, 40, 35],
                borderColor: '#020258',
                backgroundColor: 'rgba(2, 2, 88, 0.1)',
                tension: 0.4,
                fill: false
            }, {
                label: 'Reinvestment',
                data: [5, 10, 15, 25, 20, 25, 30, 28],
                borderColor: '#13e8e9',
                backgroundColor: 'rgba(19, 232, 233, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: responsiveOptions
    });
    
    // Balance Chart (Donut)
    const balanceCtx = document.getElementById('balanceChart').getContext('2d');
    const balanceChart = new Chart(balanceCtx, {
        type: 'doughnut',
        data: {
            labels: ['Investments', 'Reinvestment'],
            datasets: [{
                data: [57.436, 40.564],
                backgroundColor: ['#020258', '#13e8e9'],
                borderWidth: 0,
                cutout: window.innerWidth < 768 ? '60%' : '70%'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.label + ': $' + context.parsed.toFixed(3) + 'k';
                        }
                    }
                }
            },
            elements: {
                arc: {
                    borderWidth: 0
                }
            }
        }
    });
    
    // Add center text to donut chart
    const centerText = {
        id: 'centerText',
        afterDatasetsDraw(chart, args, options) {
            const { ctx, chartArea: { left, right, top, bottom } } = chart;
            
            ctx.save();
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            
            const centerX = (left + right) / 2;
            const centerY = (top + bottom) / 2;
            
            const fontSize = window.innerWidth < 768 ? '14px' : '16px';
            const smallFontSize = window.innerWidth < 768 ? '10px' : '12px';
            
            ctx.font = `bold ${fontSize} Inter`;
            ctx.fillStyle = isDarkMode ? '#f1f5f9' : '#1e293b';
            ctx.fillText('98k', centerX, centerY - 8);
            
            ctx.font = `${smallFontSize} Inter`;
            ctx.fillStyle = isDarkMode ? '#94a3b8' : '#64748b';
            ctx.fillText('Amount', centerX, centerY + 8);
            
            ctx.restore();
        }
    };
    
    balanceChart.options.plugins.centerText = centerText;
    balanceChart.update();
    
    // Handle window resize for responsive charts
    window.addEventListener('resize', function() {
        cashFlowChart.resize();
        balanceChart.resize();
        balanceChart.update();
    });
    
    // Global function to update charts for theme changes
    window.updateChartsForTheme = function() {
        const isDarkMode = document.body.getAttribute('data-theme') === 'dark';
        
        // Update chart colors
        if (cashFlowChart) {
            cashFlowChart.options.scales.y.ticks.color = isDarkMode ? '#cbd5e1' : '#475569';
            cashFlowChart.options.scales.y.grid.color = isDarkMode ? '#334155' : '#e2e8f0';
            cashFlowChart.options.scales.x.ticks.color = isDarkMode ? '#cbd5e1' : '#475569';
            cashFlowChart.options.scales.x.grid.color = isDarkMode ? '#334155' : '#e2e8f0';
            cashFlowChart.update();
        }
        
        if (balanceChart) {
            balanceChart.update();
        }
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
@endsection
