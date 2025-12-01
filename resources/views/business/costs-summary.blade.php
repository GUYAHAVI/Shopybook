@extends('layouts.dash')

@section('content')
<div class="container-fluid px-4">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Costs Summary</h1>
            <p class="text-muted">Complete overview of all business costs including inventory purchases</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('products.receive') }}" class="btn btn-info">
                <i class="fas fa-truck-loading me-2"></i>Record Stock Receipt
            </a>
            <a href="{{ route('costs.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Add Cost
            </a>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Total Inventory Costs</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                KSh {{ number_format(auth()->user()->business->total_inventory_costs, 2) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-box-open fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Salary Costs</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                KSh {{ number_format(auth()->user()->business->total_salary_costs, 2) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Operating Expenses</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                KSh {{ number_format(auth()->user()->business->costs()->where('type', '!=', 'salary')->sum('amount'), 2) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-file-invoice-dollar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-dark shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-dark text-uppercase mb-1">Total Costs</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                KSh {{ number_format(
                                    auth()->user()->business->total_inventory_costs + 
                                    auth()->user()->business->total_salary_costs + 
                                    auth()->user()->business->costs()->where('type', '!=', 'salary')->sum('amount'), 
                                    2
                                ) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calculator fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Cost Breakdown Chart -->
    <div class="row mb-4">
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Cost Breakdown</h6>
                </div>
                <div class="card-body">
                    <canvas id="costBreakdownChart"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Monthly Inventory Purchases</h6>
                </div>
                <div class="card-body">
                    <canvas id="inventoryTrendChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Links -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Cost Management</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <a href="{{ route('products.receive.history') }}" class="text-decoration-none">
                                <div class="card bg-info text-white">
                                    <div class="card-body">
                                        <i class="fas fa-receipt fa-2x mb-2"></i>
                                        <h5>Stock Receipt History</h5>
                                        <p class="mb-0">View all inventory purchases</p>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-4 mb-3">
                            <a href="{{ route('costs.index') }}" class="text-decoration-none">
                                <div class="card bg-warning text-white">
                                    <div class="card-body">
                                        <i class="fas fa-list fa-2x mb-2"></i>
                                        <h5>Operating Costs</h5>
                                        <p class="mb-0">View utilities, rent, and other expenses</p>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-4 mb-3">
                            <a href="{{ route('staff.index') }}" class="text-decoration-none">
                                <div class="card bg-success text-white">
                                    <div class="card-body">
                                        <i class="fas fa-users fa-2x mb-2"></i>
                                        <h5>Staff & Salaries</h5>
                                        <p class="mb-0">Manage staff and salary costs</p>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Cost Breakdown Chart
    const costBreakdownCtx = document.getElementById('costBreakdownChart').getContext('2d');
    const costBreakdownChart = new Chart(costBreakdownCtx, {
        type: 'doughnut',
        data: {
            labels: ['Inventory Purchases', 'Salaries', 'Operating Expenses'],
            datasets: [{
                data: [
                    {{ auth()->user()->business->total_inventory_costs }},
                    {{ auth()->user()->business->total_salary_costs }},
                    {{ auth()->user()->business->costs()->where('type', '!=', 'salary')->sum('amount') }}
                ],
                backgroundColor: ['#e74a3b', '#f6c23e', '#36b9cc']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // Inventory Trend Chart (Last 6 months)
    const inventoryTrendCtx = document.getElementById('inventoryTrendChart').getContext('2d');
    const inventoryTrendChart = new Chart(inventoryTrendCtx, {
        type: 'bar',
        data: {
            labels: [
                @for($i = 5; $i >= 0; $i--)
                    '{{ now()->subMonths($i)->format("M Y") }}'{{ $i > 0 ? ',' : '' }}
                @endfor
            ],
            datasets: [{
                label: 'Inventory Purchases (KSh)',
                data: [
                    @for($i = 5; $i >= 0; $i--)
                        {{ auth()->user()->business->getInventoryCostsForMonth(now()->subMonths($i)->year, now()->subMonths($i)->month) }}{{ $i > 0 ? ',' : '' }}
                    @endfor
                ],
                backgroundColor: '#4e73df'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>
@endsection



