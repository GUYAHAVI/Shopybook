@extends('layouts.dash')

@section('content')
<div class="container-fluid py-4">
    <!-- Summary Cards -->
    <div class="row">
        <div class="col-xl-3 col-sm-6 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-uppercase font-weight-bold">Total Tax Collected</p>
                                <h5 class="font-weight-bolder">
                                    KSh {{ number_format($totalTaxCollected, 2) }}
                                </h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-primary shadow-primary text-center rounded-circle">
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
                                <p class="text-sm mb-0 text-uppercase font-weight-bold">Total Sales</p>
                                <h5 class="font-weight-bolder">
                                    KSh {{ number_format($totalSales, 2) }}
                                </h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-success shadow-success text-center rounded-circle">
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
                                <p class="text-sm mb-0 text-uppercase font-weight-bold">Subtotal</p>
                                <h5 class="font-weight-bolder">
                                    KSh {{ number_format($totalSubtotal, 2) }}
                                </h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-info shadow-info text-center rounded-circle">
                                <i class="ni ni-chart-bar-32 text-lg opacity-10" aria-hidden="true"></i>
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
                                <p class="text-sm mb-0 text-uppercase font-weight-bold">Orders</p>
                                <h5 class="font-weight-bolder">
                                    {{ number_format($ordersCount) }}
                                </h5>
                                <p class="text-xs mb-0">Avg: KSh {{ number_format($averageTaxPerOrder, 2) }}/order</p>
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

    <!-- Filters and Actions -->
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h5 class="mb-0">Tax Reports</h5>
                            <p class="text-sm mb-0">View and export tax data for compliance</p>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('tax.settings') }}" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-cog me-2"></i>Settings
                            </a>
                            <form action="{{ route('tax.export') }}" method="GET" class="d-inline">
                                <input type="hidden" name="start_date" value="{{ $startDate }}">
                                <input type="hidden" name="end_date" value="{{ $endDate }}">
                                <button type="submit" class="btn btn-sm btn-success">
                                    <i class="fas fa-file-excel me-2"></i>Export CSV
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Date Range Filter -->
                    <form method="GET" action="{{ route('tax.reports') }}" class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label for="start_date" class="form-label">Start Date</label>
                            <input type="date" class="form-control" id="start_date" name="start_date" value="{{ $startDate }}">
                        </div>
                        <div class="col-md-4">
                            <label for="end_date" class="form-label">End Date</label>
                            <input type="date" class="form-control" id="end_date" name="end_date" value="{{ $endDate }}">
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-filter me-2"></i>Filter
                            </button>
                            <a href="{{ route('tax.reports') }}" class="btn btn-secondary ms-2">
                                <i class="fas fa-redo me-2"></i>Reset
                            </a>
                        </div>
                    </form>

                    @if($taxSettings)
                        <div class="alert alert-info">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-info-circle me-2"></i>
                                <div>
                                    <strong>Tax Settings:</strong> {{ $taxSettings->tax_name }} at {{ $taxSettings->tax_rate }}% 
                                    ({{ $taxSettings->tax_inclusive ? 'Tax Inclusive' : 'Tax Exclusive' }})
                                    @if($taxSettings->tax_number)
                                        <br><strong>Tax Number:</strong> {{ $taxSettings->tax_number }}
                                    @endif
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Tax settings have not been configured. <a href="{{ route('tax.settings') }}">Configure now</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Charts -->
    <div class="row">
        <div class="col-lg-8 mb-4">
            <div class="card">
                <div class="card-header pb-0">
                    <h6>Tax Collection Trend</h6>
                </div>
                <div class="card-body p-3">
                    <canvas id="taxTrendChart" height="300"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-4 mb-4">
            <div class="card">
                <div class="card-header pb-0">
                    <h6>Tax by Type</h6>
                </div>
                <div class="card-body p-3">
                    <canvas id="taxTypeChart" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Monthly Comparison -->
    <div class="row">
        <div class="col-12 mb-4">
            <div class="card">
                <div class="card-header pb-0">
                    <h6>6-Month Tax Comparison</h6>
                </div>
                <div class="card-body p-3">
                    <canvas id="monthlyComparisonChart" height="100"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header pb-0">
                    <h6>Tax Transactions</h6>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Order</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Date</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Customer</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Subtotal</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Tax</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Total</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Payment</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($orders as $order)
                                    <tr>
                                        <td>
                                            <div class="d-flex px-2 py-1">
                                                <div class="d-flex flex-column justify-content-center">
                                                    <h6 class="mb-0 text-sm">{{ $order->order_number }}</h6>
                                                    @if($order->tax_type)
                                                        <p class="text-xs text-secondary mb-0">{{ $order->tax_type }} @ {{ $order->tax_rate }}%</p>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <p class="text-xs font-weight-bold mb-0">{{ $order->created_at->format('M d, Y') }}</p>
                                            <p class="text-xs text-secondary mb-0">{{ $order->created_at->format('h:i A') }}</p>
                                        </td>
                                        <td>
                                            <p class="text-xs font-weight-bold mb-0">
                                                {{ $order->customer ? $order->customer->name : ($order->customer_name ?? 'Walk-in') }}
                                            </p>
                                        </td>
                                        <td>
                                            <p class="text-xs font-weight-bold mb-0">KSh {{ number_format($order->subtotal, 2) }}</p>
                                        </td>
                                        <td>
                                            <p class="text-xs font-weight-bold mb-0">KSh {{ number_format($order->tax, 2) }}</p>
                                        </td>
                                        <td>
                                            <p class="text-xs font-weight-bold mb-0">KSh {{ number_format($order->total_amount, 2) }}</p>
                                        </td>
                                        <td>
                                            <span class="badge badge-sm bg-gradient-success">{{ ucfirst($order->payment_method ?? 'N/A') }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4">
                                            <p class="text-secondary mb-0">No tax transactions found for the selected period</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Tax Trend Chart
const taxTrendCtx = document.getElementById('taxTrendChart').getContext('2d');
const taxTrendData = @json($taxByDate);
new Chart(taxTrendCtx, {
    type: 'line',
    data: {
        labels: taxTrendData.map(item => item.date),
        datasets: [
            {
                label: 'Tax Collected',
                data: taxTrendData.map(item => item.tax),
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.1)',
                tension: 0.4,
                fill: true
            },
            {
                label: 'Total Sales',
                data: taxTrendData.map(item => item.sales),
                borderColor: 'rgb(54, 162, 235)',
                backgroundColor: 'rgba(54, 162, 235, 0.1)',
                tension: 0.4,
                fill: true
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'top',
            }
        },
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

// Tax by Type Chart
const taxTypeCtx = document.getElementById('taxTypeChart').getContext('2d');
const taxTypeData = @json($taxByType);
new Chart(taxTypeCtx, {
    type: 'doughnut',
    data: {
        labels: taxTypeData.map(item => item.type),
        datasets: [{
            data: taxTypeData.map(item => item.tax),
            backgroundColor: [
                'rgb(255, 99, 132)',
                'rgb(54, 162, 235)',
                'rgb(255, 205, 86)',
                'rgb(75, 192, 192)',
                'rgb(153, 102, 255)',
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom',
            }
        }
    }
});

// Monthly Comparison Chart
const monthlyCtx = document.getElementById('monthlyComparisonChart').getContext('2d');
const monthlyData = @json($monthlyData);
new Chart(monthlyCtx, {
    type: 'bar',
    data: {
        labels: monthlyData.map(item => item.month),
        datasets: [
            {
                label: 'Tax Collected',
                data: monthlyData.map(item => item.tax),
                backgroundColor: 'rgba(75, 192, 192, 0.7)',
            },
            {
                label: 'Total Sales',
                data: monthlyData.map(item => item.sales),
                backgroundColor: 'rgba(54, 162, 235, 0.7)',
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'top',
            }
        },
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});
</script>
@endsection

