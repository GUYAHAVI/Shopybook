@extends('layouts.dash')
@section('title', 'Shopybook Dashboard')
@section('content')
<style>
.dashboard-main-content {
    margin-top: 0;
}

.quick-action-card {
    background: #03104e;
    border: 2px solid #13e8e9;
    color: #fff;
    transition: all 0.3s;
}
.quick-action-card .card-title,
.quick-action-card .card-text {
    color: #13e8e9;
}
.quick-action-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(19, 232, 233, 0.2);
    border-color: #fff;
    background: #13e8e9;
    color: #020258;
}
.quick-action-card:hover .card-title,
.quick-action-card:hover .card-text {
    color: #020258 !important;
}

.card,
.card-header {
    background: #03104e !important;
    color: #fff !important;
    border: 2px solid #13e8e9 !important;
}
.card-header {
    border-bottom: 1px solid #13e8e9 !important;
}

.btn-primary, .btn-primary:active, .btn-primary:focus {
    background: #13e8e9 !important;
    color: #020258 !important;
    border: 2px solid #13e8e9 !important;
}
.btn-primary:hover {
    background: #020258 !important;
    color: #13e8e9 !important;
    border: 2px solid #13e8e9 !important;
}

.btn-outline-primary {
    border: 2px solid #13e8e9 !important;
    color: #13e8e9 !important;
    background: transparent !important;
}
.btn-outline-primary:hover {
    background: #13e8e9 !important;
    color: #020258 !important;
}

.stats-card {
    background: #03104e !important;
    border: 2px solid #13e8e9 !important;
    color: #fff !important;
}
.stats-card .card-title,
.stats-card .card-text {
    color: #13e8e9 !important;
}
.stats-card .icon {
    color: #13e8e9 !important;
}
</style>
<div class="dashboard-main-content">
<div class="container-fluid px-4">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0" style="font-family: 'Cinzel Decorative', serif; color: #13e8e9; font-weight: 700;">{{ t('dashboard') }}</h1>
        <a href="#" class="btn btn-primary btn-sm shadow-sm">
            <i class="fas fa-download fa-sm"></i> {{ t('generate_report') }}
        </a>
    </div>

    <!-- Stats Row -->
    <div class="row">
        <!-- Today's Orders -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stats-card h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-uppercase mb-1 card-title">
                                {{ t('today_orders') }}</div>
                            <div class="h5 mb-0 font-weight-bold card-text" id="today-orders">18</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-shopping-cart fa-2x icon"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Today's Sales -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stats-card h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-uppercase mb-1 card-title">
                                {{ t('today_sales') }}</div>
                            <div class="h5 mb-0 font-weight-bold card-text" id="today-sales">KSh 24,500</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-coins fa-2x icon"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Conversion Rate -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stats-card h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-uppercase mb-1 card-title">
                                {{ t('conversion_rate') }}</div>
                            <div class="row no-gutters align-items-center">
                                <div class="col-auto">
                                    <div class="h5 mb-0 mr-3 font-weight-bold card-text" id="conversion-rate">32%</div>
                                </div>
                                <div class="col">
                                    <div class="progress progress-sm mr-2" style="height: 8px; background: #fff;">
                                        <div class="progress-bar" role="progressbar" style="width: 32%; background-color: #13e8e9;" aria-valuenow="32" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-percent fa-2x icon"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Pending Orders -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stats-card h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-uppercase mb-1 card-title">
                                {{ t('pending_orders') }}</div>
                            <div class="h5 mb-0 font-weight-bold card-text">4</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x icon"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-bolt me-2" style="color: #13e8e9;"></i>
                        {{ t('quick_actions') }}
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Add New Product -->
                        <div class="col-lg-3 col-md-6 mb-3">
                            <a href="{{ route('products.create') }}" class="text-decoration-none">
                                <div class="card h-100 quick-action-card">
                                    <div class="card-body text-center">
                                        <div class="mb-3">
                                            <i class="fas fa-plus-circle fa-3x"></i>
                                        </div>
                                        <h6 class="card-title">{{ t('add_new_product') }}</h6>
                                        <p class="card-text small">{{ t('add_new_product_description') }}</p>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <!-- New Sale -->
                        <div class="col-lg-3 col-md-6 mb-3">
                            <a href="{{ route('sales.pos') }}" class="text-decoration-none">
                                <div class="card h-100 quick-action-card">
                                    <div class="card-body text-center">
                                        <div class="mb-3">
                                            <i class="fas fa-cash-register fa-3x"></i>
                                        </div>
                                        <h6 class="card-title">{{ t('new_sale') }}</h6>
                                        <p class="card-text small">{{ t('new_sale_description') }}</p>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <!-- Bulk Import -->
                        <div class="col-lg-3 col-md-6 mb-3">
                            <a href="{{ route('products.bulk-import') }}" class="text-decoration-none">
                                <div class="card h-100 quick-action-card">
                                    <div class="card-body text-center">
                                        <div class="mb-3">
                                            <i class="fas fa-file-import fa-3x"></i>
                                        </div>
                                        <h6 class="card-title">{{ t('bulk_import') }}</h6>
                                        <p class="card-text small">{{ t('bulk_import_description') }}</p>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <!-- Business Analysis -->
                        <div class="col-lg-3 col-md-6 mb-3">
                            <a href="{{ route('business.analysis.index') }}" class="text-decoration-none">
                                <div class="card h-100 quick-action-card">
                                    <div class="card-body text-center">
                                        <div class="mb-3">
                                            <i class="fas fa-chart-line fa-3x"></i>
                                        </div>
                                        <h6 class="card-title">{{ t('business_analytics') }}</h6>
                                        <p class="card-text small">{{ t('business_analytics_description') }}</p>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Business Analytics Section -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ t('business_analysis') }}</h1>
        <div class="d-flex">
            <select id="analytics-period" class="form-control form-control-sm">
                <option value="7">{{ t('last_7_days') }}</option>
                <option value="30" selected>{{ t('last_30_days') }}</option>
                <option value="90">{{ t('last_quarter') }}</option>
                <option value="365">{{ t('last_year') }}</option>
                <option value="custom">{{ t('custom_range') }}</option>
            </select>
        </div>
    </div>

    <!-- Performance Metrics Row -->
    <div class="row">
        <!-- Net Profit -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2" style="background: #020258; border: 2px solid rgba(19, 232, 233, 0.3) !important; border-left: 0.25rem solid #13e8e9 !important;">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1" style="color: #13e8e9 !important;">
                                {{ t('net_profit') }}</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="net-profit">KSh 0.00</div>
                            <div class="mt-2 text-success small">
                                <i class="fas fa-arrow-up"></i> 12% from last period
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-wallet fa-2x text-gray-300" style="color: #13e8e9 !important;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- New Customers -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2" style="background: #020258; border: 2px solid rgba(19, 232, 233, 0.3) !important; border-left: 0.25rem solid #13e8e9 !important;">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1" style="color: #13e8e9 !important;">
                                {{ t('new_customers') }}</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="new-customers">0</div>
                            <div class="mt-2 text-danger small">
                                <i class="fas fa-arrow-down"></i> 5% from last period
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-plus fa-2x text-gray-300" style="color: #13e8e9 !important;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Returning Rate -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2" style="background: #020258; border: 2px solid rgba(19, 232, 233, 0.3) !important; border-left: 0.25rem solid #13e8e9 !important;">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1" style="color: #13e8e9 !important;">
                                {{ t('returning_rate') }}</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="returning-rate">0%</div>
                            <div class="mt-2 text-success small">
                                <i class="fas fa-arrow-up"></i> 8% from last period
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-redo fa-2x text-gray-300" style="color: #13e8e9 !important;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Avg. Order Value -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2" style="background: #020258; border: 2px solid rgba(19, 232, 233, 0.3) !important; border-left: 0.25rem solid #13e8e9 !important;">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1" style="color: #13e8e9 !important;">
                                Avg. Order Value</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">KSh 1,450</div>
                            <div class="mt-2 text-success small">
                                <i class="fas fa-arrow-up"></i> 6% from last period
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-receipt fa-2x text-gray-300" style="color: #13e8e9 !important;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row">
        <!-- Profit/Loss Trend Chart -->
        <div class="col-xl-8 col-lg-7 mb-4">
            <div class="card shadow mb-4" style="background: #020258; border: 2px solid rgba(19, 232, 233, 0.3);">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between" style="background: rgba(19, 232, 233, 0.1); border-bottom: 1px solid #13e8e9;">
                    <h6 class="m-0 font-weight-bold text-primary" style="color: #13e8e9 !important;">Profit & Loss Trend</h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" 
                            data-bs-toggle="dropdown" aria-expanded="false" style="color: #13e8e9;">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow animated--fade-in"
                            aria-labelledby="dropdownMenuLink" style="background: rgba(2, 2, 88, 0.9); border: 1px solid #13e8e9;">
                            <li><a class="dropdown-item" href="#">This Week</a></li>
                            <li><a class="dropdown-item" href="#">This Month</a></li>
                            <li><a class="dropdown-item" href="#">This Year</a></li>
                        </ul>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="profitLossChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Customer Acquisition Chart -->
        <div class="col-xl-4 col-lg-5 mb-4">
            <div class="card shadow mb-4" style="background: #020258; border: 2px solid rgba(19, 232, 233, 0.3);">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between" style="background: rgba(19, 232, 233, 0.1); border-bottom: 1px solid #13e8e9;">
                    <h6 class="m-0 font-weight-bold text-primary" style="color: #13e8e9 !important;">Customer Acquisition</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="customerChart"></canvas>
                    </div>
                    <div class="mt-4 text-center small">
                        <span class="me-2" style="color: #13e8e9;">
                            <i class="fas fa-circle text-primary"></i> New
                        </span>
                        <span class="me-2" style="color: #13e8e9;">
                            <i class="fas fa-circle text-success"></i> Returning
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Secondary Charts Row -->
    <div class="row">
        <!-- Top Products Chart -->
        <div class="col-xl-6 col-lg-6 mb-4">
            <div class="card shadow mb-4" style="background: #020258; border: 2px solid rgba(19, 232, 233, 0.3);">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between" style="background: rgba(19, 232, 233, 0.1); border-bottom: 1px solid #13e8e9;">
                    <h6 class="m-0 font-weight-bold text-primary" style="color: #13e8e9 !important;">Top Selling Products</h6>
                    <a href="#" class="btn btn-sm btn-link" style="color: #13e8e9;">View All</a>
                </div>
                <div class="card-body">
                    <div class="chart-bar">
                        <canvas id="productsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sales Funnel Chart -->
        <div class="col-xl-6 col-lg-6 mb-4">
            <div class="card shadow mb-4" style="background: #020258; border: 2px solid rgba(19, 232, 233, 0.3);">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between" style="background: rgba(19, 232, 233, 0.1); border-bottom: 1px solid #13e8e9;">
                    <h6 class="m-0 font-weight-bold text-primary" style="color: #13e8e9 !important;">Sales Conversion Funnel</h6>
                </div>
                <div class="card-body">
                    <div class="chart-bar">
                        <canvas id="funnelChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- AI Recommendations Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow mb-4" style="background: #020258; border: 2px solid rgba(19, 232, 233, 0.3);">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between" style="background: rgba(19, 232, 233, 0.1); border-bottom: 1px solid #13e8e9;">
                    <h6 class="m-0 font-weight-bold text-primary" style="color: #13e8e9 !important;">
                        <i class="fas fa-robot me-2"></i>AI Business Recommendations
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-4">
                            <div class="card border-left-info h-100" style="background: #020258; border: 2px solid rgba(19, 232, 233, 0.3) !important;">
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="icon-circle bg-info me-3">
                                            <i class="fas fa-bolt text-white"></i>
                                        </div>
                                        <h5 class="mb-0" style="color: #13e8e9 !important;">Boost Product Visibility</h5>
                                    </div>
                                    <p class="card-text" style="color: #13e8e9 !important;">Your "Handmade Soaps" have high conversion but low views. Consider promoting them on social media.</p>
                                    <div class="d-flex">
                                        <button class="btn btn-sm btn-info me-2" style="background: #020258; border: 2px solid #13e8e9; color: #020258;">Create Promotion</button>
                                        <button class="btn btn-sm btn-outline-secondary" style="color: #13e8e9 !important;">Dismiss</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-4">
                            <div class="card border-left-warning h-100" style="background: #020258; border: 2px solid rgba(19, 232, 233, 0.3) !important;">
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="icon-circle bg-warning me-3">
                                            <i class="fas fa-clock text-white"></i>
                                        </div>
                                        <h5 class="mb-0" style="color: #13e8e9 !important;">Best Time to Post</h5>
                                    </div>
                                    <p class="card-text" style="color: #13e8e9 !important;">Your customers are most active between 7-9pm. Schedule posts and promotions during this window.</p>
                                    <button class="btn btn-sm btn-warning" style="background: #020258; border: 2px solid #13e8e9; color: #020258;">Set Reminder</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-4">
                            <div class="card border-left-success h-100" style="background: #020258; border: 2px solid rgba(19, 232, 233, 0.3) !important;">
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="icon-circle bg-success me-3">
                                            <i class="fas fa-box-open text-white"></i>
                                        </div>
                                        <h5 class="mb-0" style="color: #13e8e9 !important;">Product Suggestion</h5>
                                    </div>
                                    <p class="card-text" style="color: #13e8e9 !important;">Based on your soap sales, consider adding complementary products like loofahs or bath salts.</p>
                                    <button class="btn btn-sm btn-success" style="background: #020258; border: 2px solid #13e8e9; color: #020258;">View Suppliers</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Customer Trends and Quick Actions -->
    <div class="row">
        <!-- Customer Purchase Trends -->
        <div class="col-xl-8 col-lg-7 mb-4">
            <div class="card shadow mb-4" style="background: #020258; border: 2px solid rgba(19, 232, 233, 0.3);">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between" style="background: rgba(19, 232, 233, 0.1); border-bottom: 1px solid #13e8e9;">
                    <h6 class="m-0 font-weight-bold text-primary" style="color: #13e8e9 !important;">Customer Purchase Trends</h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" 
                            data-bs-toggle="dropdown" aria-expanded="false" style="color: #13e8e9;">
                            <i class="fas fa-filter fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="dropdownMenuLink" style="background: rgba(2, 2, 88, 0.9); border: 1px solid #13e8e9;">
                            <li><a class="dropdown-item" href="#">This Week</a></li>
                            <li><a class="dropdown-item" href="#">This Month</a></li>
                            <li><a class="dropdown-item" href="#">This Year</a></li>
                        </ul>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Customer</th>
                                    <th>Last Purchase</th>
                                    <th>Frequency</th>
                                    <th>Avg. Spend</th>
                                    <th>Trend</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="https://placehold.co/600x400/png" class="rounded-circle me-3" width="40" height="40">
                                            <div>James Mwangi</div>
                                        </div>
                                    </td>
                                    <td>2 days ago</td>
                                    <td>Weekly</td>
                                    <td>KSh 1,200</td>
                                    <td><span class="badge bg-success"><i class="fas fa-arrow-up me-1"></i>15%</span></td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="https://placehold.co/600x400/png" class="rounded-circle me-3" width="40" height="40">
                                            <div>Sarah Kamau</div>
                                        </div>
                                    </td>
                                    <td>1 week ago</td>
                                    <td>Monthly</td>
                                    <td>KSh 2,500</td>
                                    <td><span class="badge bg-secondary"><i class="fas fa-arrow-right me-1"></i>2%</span></td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="https://placehold.co/600x400/png" class="rounded-circle me-3" width="40" height="40">
                                            <div>David Ochieng</div>
                                        </div>
                                    </td>
                                    <td>3 weeks ago</td>
                                    <td>Quarterly</td>
                                    <td>KSh 3,800</td>
                                    <td><span class="badge bg-danger"><i class="fas fa-arrow-down me-1"></i>10%</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="col-xl-4 col-lg-5 mb-4">
            <div class="card shadow mb-4" style="background: #020258; border: 2px solid rgba(19, 232, 233, 0.3);">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between" style="background: rgba(19, 232, 233, 0.1); border-bottom: 1px solid #13e8e9;">
                    <h6 class="m-0 font-weight-bold text-primary" style="color: #13e8e9 !important;">Quick Actions</h6>
                    <button class="btn btn-sm btn-primary" style="background: #020258; border: 2px solid #13e8e9; color: #020258;">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Contact top 3 customers with personalized offers
                            <span class="badge bg-secondary">Pending</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Reorder best-selling products
                            <span class="badge bg-success">Completed</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Analyze weekend sales spike
                            <span class="badge bg-secondary">Pending</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Setup email campaign for returning customers
                            <span class="badge bg-secondary">Pending</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

<!-- Chart.js Scripts -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize all charts
        initProfitLossChart();
        initCustomerChart();
        initProductsChart();
        initFunnelChart();
        
        // Simulate loading data
        setTimeout(() => {
            document.getElementById('today-orders').textContent = '18';
            document.getElementById('today-sales').textContent = 'KSh 24,500';
            document.getElementById('conversion-rate').textContent = '32%';
            document.querySelector('.progress-bar').style.width = '32%';
            document.getElementById('net-profit').textContent = 'KSh 56,800';
            document.getElementById('new-customers').textContent = '24';
            document.getElementById('returning-rate').textContent = '42%';
        }, 1000);
    });

    function initProfitLossChart() {
        const ctx = document.getElementById('profitLossChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'],
                datasets: [{
                    label: 'Profit',
                    data: [5000, 8000, 7000, 9000, 12000, 10000, 15000],
                    backgroundColor: 'rgba(28, 200, 138, 0.1)',
                    borderColor: 'rgba(28, 200, 138, 1)',
                    tension: 0.3
                }, {
                    label: 'Loss',
                    data: [2000, 3000, 2500, 4000, 3500, 4500, 3000],
                    backgroundColor: 'rgba(231, 74, 59, 0.1)',
                    borderColor: 'rgba(231, 74, 59, 1)',
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'KSh ' + value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
    }

    function initCustomerChart() {
        const ctx = document.getElementById('customerChart').getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['New Customers', 'Returning Customers'],
                datasets: [{
                    data: [35, 65],
                    backgroundColor: [
                        'rgba(78, 115, 223, 0.8)',
                        'rgba(28, 200, 138, 0.8)'
                    ],
                    hoverBorderColor: "rgba(234, 236, 244, 1)",
                }],
            },
            options: {
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                cutout: '70%',
            }
        });
    }

    function initProductsChart() {
        const ctx = document.getElementById('productsChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Handmade Soap', 'African Print', 'Wooden Crafts', 'Beaded Jewelry', 'Leather Bags'],
                datasets: [{
                    label: 'Units Sold',
                    data: [120, 90, 75, 60, 45],
                    backgroundColor: 'rgba(78, 115, 223, 0.8)',
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }

    function initFunnelChart() {
        const ctx = document.getElementById('funnelChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Visitors', 'Add to Cart', 'Checkout', 'Purchases'],
                datasets: [{
                    label: 'Conversion Funnel',
                    data: [1000, 500, 200, 120],
                    backgroundColor: [
                        'rgba(78, 115, 223, 0.8)',
                        'rgba(54, 185, 204, 0.8)',
                        'rgba(246, 194, 62, 0.8)',
                        'rgba(28, 200, 138, 0.8)'
                    ],
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true
                    }
                }
            }
        });
    }
</script>
@endsection