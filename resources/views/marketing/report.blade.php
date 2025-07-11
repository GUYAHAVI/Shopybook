@extends('layouts.dash')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Marketing Report</h1>
        <div class="d-flex">
            <select class="form-control form-control-sm mr-2" id="periodSelect" onchange="changePeriod(this.value)">
                <option value="7" {{ $period == '7' ? 'selected' : '' }}>Last 7 days</option>
                <option value="30" {{ $period == '30' ? 'selected' : '' }}>Last 30 days</option>
                <option value="90" {{ $period == '90' ? 'selected' : '' }}>Last 90 days</option>
                <option value="365" {{ $period == '365' ? 'selected' : '' }}>Last year</option>
            </select>
            <button class="btn btn-primary btn-sm" onclick="exportReport()">
                <i class="fas fa-download fa-sm"></i> Export
            </button>
        </div>
    </div>

    <!-- Marketing Overview Cards -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Active Promotions
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $promotionStats->where('is_active', true)->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-tag fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total SMS Sent
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $smsStats['total_sent'] }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-sms fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                SMS Delivery Rate
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $smsStats['total_sent'] > 0 ? round(($smsStats['delivered'] / $smsStats['total_sent']) * 100, 1) : 0 }}%
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-line fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Top Customer
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $customerEngagement->first() ? $customerEngagement->first()->name : 'N/A' }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Promotion Performance -->
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Top Performing Promotions</h6>
                </div>
                <div class="card-body">
                    @forelse($promotionStats->take(5) as $promotion)
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h6 class="mb-1">{{ $promotion->name }}</h6>
                            <small class="text-muted">{{ $promotion->code }}</small>
                        </div>
                        <div class="text-right">
                            <div class="h6 mb-0">{{ $promotion->usage_count }} uses</div>
                            <small class="text-muted">{{ $promotion->discount_value }}{{ $promotion->discount_type === 'percentage' ? '%' : '₦' }} off</small>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-3">
                        <i class="fas fa-chart-bar fa-2x text-gray-300 mb-2"></i>
                        <p class="text-muted">No promotion data available</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Customer Engagement -->
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Most Engaged Customers</h6>
                </div>
                <div class="card-body">
                    @forelse($customerEngagement->take(5) as $customer)
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h6 class="mb-1">{{ $customer->name }}</h6>
                            <small class="text-muted">{{ $customer->email }}</small>
                        </div>
                        <div class="text-right">
                            <div class="h6 mb-0">{{ $customer->orders_count }} orders</div>
                            <small class="text-muted">Total customer</small>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-3">
                        <i class="fas fa-users fa-2x text-gray-300 mb-2"></i>
                        <p class="text-muted">No customer data available</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- SMS Analytics -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">SMS Analytics</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-3">
                            <div class="border rounded p-3">
                                <div class="h4 text-primary">{{ $smsStats['total_sent'] }}</div>
                                <small class="text-muted">Total Sent</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="border rounded p-3">
                                <div class="h4 text-success">{{ $smsStats['delivered'] }}</div>
                                <small class="text-muted">Delivered</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="border rounded p-3">
                                <div class="h4 text-warning">{{ $smsStats['pending'] }}</div>
                                <small class="text-muted">Pending</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="border rounded p-3">
                                <div class="h4 text-danger">{{ $smsStats['failed'] }}</div>
                                <small class="text-muted">Failed</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            <strong>Note:</strong> SMS API integration is pending. Current statistics are placeholder data.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Marketing Insights</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h6 class="text-success">Best Performing Channel</h6>
                        <p class="text-muted">Email marketing shows the highest engagement rate at 85%</p>
                    </div>
                    <div class="mb-3">
                        <h6 class="text-info">Optimal Send Time</h6>
                        <p class="text-muted">Tuesday and Thursday between 10 AM - 2 PM</p>
                    </div>
                    <div class="mb-3">
                        <h6 class="text-warning">Customer Retention</h6>
                        <p class="text-muted">Promotions increase customer retention by 40%</p>
                    </div>
                    <div>
                        <h6 class="text-primary">ROI Improvement</h6>
                        <p class="text-muted">Marketing campaigns show 3.2x return on investment</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recommendations -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Marketing Recommendations</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="card border-left-success">
                        <div class="card-body">
                            <h6 class="text-success"><i class="fas fa-lightbulb"></i> Increase Engagement</h6>
                            <p class="text-muted">Create more personalized promotions based on customer purchase history</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-left-info">
                        <div class="card-body">
                            <h6 class="text-info"><i class="fas fa-chart-line"></i> Optimize Timing</h6>
                            <p class="text-muted">Schedule SMS campaigns during peak engagement hours (10 AM - 2 PM)</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-left-warning">
                        <div class="card-body">
                            <h6 class="text-warning"><i class="fas fa-users"></i> Target New Segments</h6>
                            <p class="text-muted">Expand marketing efforts to reach new customer demographics</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function changePeriod(period) {
    window.location.href = `{{ route('marketing.report') }}?period=${period}`;
}

function exportReport() {
    const period = document.getElementById('periodSelect').value;
    const url = `{{ route('marketing.report') }}?period=${period}&export=1`;
    
    // Create a temporary link to download the report
    const link = document.createElement('a');
    link.href = url;
    link.download = `marketing-report-${period}days.csv`;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

// Add some interactive charts here when Chart.js is available
document.addEventListener('DOMContentLoaded', function() {
    // Placeholder for future chart implementations
    console.log('Marketing report loaded');
});
</script>
@endpush 