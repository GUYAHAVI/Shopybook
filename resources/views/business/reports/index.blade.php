@extends('layouts.dash')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0">
                    <h5 class="mb-0">📊 Comprehensive Reports</h5>
                    <p class="text-sm mb-0">Generate detailed analytics and insights for your business</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row">
        <div class="col-xl-3 col-sm-6 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-uppercase font-weight-bold">Total Sales</p>
                                <h5 class="font-weight-bolder">
                                    KSh {{ number_format($stats['total_sales'], 2) }}
                                </h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-success shadow-success text-center rounded-circle">
                                <i class="ni ni-money-coins text-lg opacity-10" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-uppercase font-weight-bold">Total Orders</p>
                                <h5 class="font-weight-bolder">
                                    {{ number_format($stats['total_orders']) }}
                                </h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-primary shadow-primary text-center rounded-circle">
                                <i class="ni ni-cart text-lg opacity-10" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-uppercase font-weight-bold">Total Customers</p>
                                <h5 class="font-weight-bolder">
                                    {{ number_format($stats['total_customers']) }}
                                </h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-info shadow-info text-center rounded-circle">
                                <i class="ni ni-single-02 text-lg opacity-10" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-uppercase font-weight-bold">Total Products</p>
                                <h5 class="font-weight-bolder">
                                    {{ number_format($stats['total_products']) }}
                                </h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-warning shadow-warning text-center rounded-circle">
                                <i class="ni ni-box-2 text-lg opacity-10" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Report Cards -->
    <div class="row">
        <!-- Sales Report -->
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon icon-shape bg-gradient-primary shadow text-center rounded-circle me-3">
                            <i class="fas fa-chart-line text-lg opacity-10 text-white" aria-hidden="true"></i>
                        </div>
                        <h5 class="mb-0">Sales Performance</h5>
                    </div>
                    <p class="text-sm mb-3">
                        Detailed sales analytics with revenue trends, payment methods, peak hours, and period comparisons.
                    </p>
                    <ul class="list-unstyled text-sm mb-3">
                        <li><i class="fas fa-check text-success me-2"></i>Revenue by period</li>
                        <li><i class="fas fa-check text-success me-2"></i>Payment method breakdown</li>
                        <li><i class="fas fa-check text-success me-2"></i>Peak hours analysis</li>
                        <li><i class="fas fa-check text-success me-2"></i>Growth comparison</li>
                    </ul>
                    <a href="{{ route('reports.sales') }}" class="btn btn-primary btn-sm w-100">
                        <i class="fas fa-eye me-2"></i>View Report
                    </a>
                </div>
            </div>
        </div>

        <!-- Product Performance -->
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon icon-shape bg-gradient-success shadow text-center rounded-circle me-3">
                            <i class="fas fa-box text-lg opacity-10 text-white" aria-hidden="true"></i>
                        </div>
                        <h5 class="mb-0">Product Performance</h5>
                    </div>
                    <p class="text-sm mb-3">
                        Analyze product sales, profitability, stock turnover, and identify top/worst performers.
                    </p>
                    <ul class="list-unstyled text-sm mb-3">
                        <li><i class="fas fa-check text-success me-2"></i>Top selling products</li>
                        <li><i class="fas fa-check text-success me-2"></i>Profit margins</li>
                        <li><i class="fas fa-check text-success me-2"></i>Category performance</li>
                        <li><i class="fas fa-check text-success me-2"></i>Low stock alerts</li>
                    </ul>
                    <a href="{{ route('reports.products') }}" class="btn btn-success btn-sm w-100">
                        <i class="fas fa-eye me-2"></i>View Report
                    </a>
                </div>
            </div>
        </div>

        <!-- Customer Analytics -->
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon icon-shape bg-gradient-info shadow text-center rounded-circle me-3">
                            <i class="fas fa-users text-lg opacity-10 text-white" aria-hidden="true"></i>
                        </div>
                        <h5 class="mb-0">Customer Analytics</h5>
                    </div>
                    <p class="text-sm mb-3">
                        Understand customer behavior, identify VIPs, track retention, and analyze lifetime value.
                    </p>
                    <ul class="list-unstyled text-sm mb-3">
                        <li><i class="fas fa-check text-success me-2"></i>Top customers</li>
                        <li><i class="fas fa-check text-success me-2"></i>Customer segments</li>
                        <li><i class="fas fa-check text-success me-2"></i>Lifetime value</li>
                        <li><i class="fas fa-check text-success me-2"></i>Retention metrics</li>
                    </ul>
                    <a href="{{ route('reports.customers') }}" class="btn btn-info btn-sm w-100">
                        <i class="fas fa-eye me-2"></i>View Report
                    </a>
                </div>
            </div>
        </div>

        <!-- Inventory Report -->
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon icon-shape bg-gradient-warning shadow text-center rounded-circle me-3">
                            <i class="fas fa-boxes text-lg opacity-10 text-white" aria-hidden="true"></i>
                        </div>
                        <h5 class="mb-0">Inventory Analysis</h5>
                    </div>
                    <p class="text-sm mb-3">
                        Monitor stock levels, turnover rates, slow-moving items, and optimize inventory management.
                    </p>
                    <ul class="list-unstyled text-sm mb-3">
                        <li><i class="fas fa-check text-success me-2"></i>Stock valuation</li>
                        <li><i class="fas fa-check text-success me-2"></i>Turnover analysis</li>
                        <li><i class="fas fa-check text-success me-2"></i>Reorder alerts</li>
                        <li><i class="fas fa-check text-success me-2"></i>Fast/slow movers</li>
                    </ul>
                    <a href="{{ route('reports.inventory') }}" class="btn btn-warning btn-sm w-100">
                        <i class="fas fa-eye me-2"></i>View Report
                    </a>
                </div>
            </div>
        </div>

        <!-- Profit & Loss -->
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon icon-shape bg-gradient-danger shadow text-center rounded-circle me-3">
                            <i class="fas fa-file-invoice-dollar text-lg opacity-10 text-white" aria-hidden="true"></i>
                        </div>
                        <h5 class="mb-0">Profit & Loss Statement</h5>
                    </div>
                    <p class="text-sm mb-3">
                        Comprehensive P&L statement with revenue, COGS, operating expenses, and net profit analysis.
                    </p>
                    <ul class="list-unstyled text-sm mb-3">
                        <li><i class="fas fa-check text-success me-2"></i>Revenue breakdown</li>
                        <li><i class="fas fa-check text-success me-2"></i>Expense analysis</li>
                        <li><i class="fas fa-check text-success me-2"></i>Profit margins</li>
                        <li><i class="fas fa-check text-success me-2"></i>Monthly trends</li>
                    </ul>
                    <a href="{{ route('reports.profit-loss') }}" class="btn btn-danger btn-sm w-100">
                        <i class="fas fa-eye me-2"></i>View Report
                    </a>
                </div>
            </div>
        </div>

        <!-- Tax Report -->
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon icon-shape bg-gradient-dark shadow text-center rounded-circle me-3">
                            <i class="fas fa-percentage text-lg opacity-10 text-white" aria-hidden="true"></i>
                        </div>
                        <h5 class="mb-0">Tax Reports</h5>
                    </div>
                    <p class="text-sm mb-3">
                        Track VAT/tax collection, view tax by period, and export reports for compliance and filing.
                    </p>
                    <ul class="list-unstyled text-sm mb-3">
                        <li><i class="fas fa-check text-success me-2"></i>Tax collection trends</li>
                        <li><i class="fas fa-check text-success me-2"></i>Period comparisons</li>
                        <li><i class="fas fa-check text-success me-2"></i>Export for filing</li>
                        <li><i class="fas fa-check text-success me-2"></i>Compliance tracking</li>
                    </ul>
                    <a href="{{ route('tax.reports') }}" class="btn btn-dark btn-sm w-100">
                        <i class="fas fa-eye me-2"></i>View Report
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header pb-0">
                    <h6>Quick Report Generation</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Report Type</label>
                            <select class="form-select" id="quickReportType">
                                <option value="sales">Sales Performance</option>
                                <option value="products">Product Performance</option>
                                <option value="customers">Customer Analytics</option>
                                <option value="inventory">Inventory Analysis</option>
                                <option value="profit-loss">Profit & Loss</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Start Date</label>
                            <input type="date" class="form-control" id="quickStartDate" value="{{ now()->startOfMonth()->format('Y-m-d') }}">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">End Date</label>
                            <input type="date" class="form-control" id="quickEndDate" value="{{ now()->format('Y-m-d') }}">
                        </div>
                        <div class="col-md-2 mb-3 d-flex align-items-end">
                            <button class="btn btn-primary w-100" onclick="generateQuickReport()">
                                <i class="fas fa-chart-bar me-2"></i>Generate
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function generateQuickReport() {
    const type = document.getElementById('quickReportType').value;
    const startDate = document.getElementById('quickStartDate').value;
    const endDate = document.getElementById('quickEndDate').value;
    
    const url = `/reports/${type}?start_date=${startDate}&end_date=${endDate}`;
    window.location.href = url;
}
</script>
@endsection






