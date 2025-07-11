@extends('layouts.dash')

@section('content')
<div class="container-fluid px-4">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Financial Report</h1>
            <p class="text-muted">Comprehensive financial analysis and insights</p>
        </div>
        <a href="{{ route('business.analysis.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Analysis
        </a>
    </div>

    <!-- Financial Summary -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Revenue</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">KSh {{ $financialData['summary']['revenue'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Total Costs</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">KSh {{ $financialData['summary']['costs'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-minus-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Net Profit</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">KSh {{ $financialData['summary']['profit'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-line fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Profit Margin</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $financialData['summary']['profit_margin'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-percentage fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- AI Financial Analysis -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-robot me-2"></i>AI Financial Analysis
                    </h6>
                </div>
                <div class="card-body">
                    <div class="financial-report">
                        {!! nl2br(e($report)) !!}
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Top Performing Products -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-trophy me-2"></i>Top Performing Products
                    </h6>
                </div>
                <div class="card-body">
                    @if(count($financialData['product_performance']) > 0)
                        @foreach($financialData['product_performance']->take(5) as $product)
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="font-weight-bold">{{ $product['name'] }}</div>
                                    <small class="text-muted">{{ $product['sales_count'] }} sales</small>
                                </div>
                                <div class="text-right">
                                    <div class="font-weight-bold text-success">KSh {{ number_format($product['total_sales'], 2) }}</div>
                                    <small class="text-muted">Stock: KSh {{ number_format($product['stock_value'], 2) }}</small>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <p class="text-muted text-center">No product performance data available</p>
                    @endif
                </div>
            </div>

            <!-- Monthly Revenue Chart -->
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-bar me-2"></i>Monthly Revenue Trend
                    </h6>
                </div>
                <div class="card-body">
                    @if(count($financialData['monthly_revenue']) > 0)
                        <canvas id="monthlyRevenueChart" width="400" height="200"></canvas>
                    @else
                        <p class="text-muted text-center">No monthly revenue data available</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@if(count($financialData['monthly_revenue']) > 0)
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('monthlyRevenueChart').getContext('2d');
    
    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    const revenueData = [];
    
    // Fill revenue data for all months
    for (let i = 1; i <= 12; i++) {
        revenueData.push({{ $financialData['monthly_revenue'][$i] ?? 0 }});
    }
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: months,
            datasets: [{
                label: 'Revenue (KSh)',
                data: revenueData,
                borderColor: '#4e73df',
                backgroundColor: 'rgba(78, 115, 223, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
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
                    ticks: {
                        callback: function(value) {
                            return 'KSh ' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    });
});
</script>
@endif

<style>
.financial-report {
    line-height: 1.6;
    font-size: 0.95rem;
}

.financial-report h1, .financial-report h2, .financial-report h3 {
    color: #4e73df;
    margin-top: 1.5rem;
    margin-bottom: 0.5rem;
}

.financial-report ul, .financial-report ol {
    padding-left: 1.5rem;
}

.financial-report li {
    margin-bottom: 0.5rem;
}

.financial-report strong {
    color: #2e59d9;
}
</style>
@endsection 