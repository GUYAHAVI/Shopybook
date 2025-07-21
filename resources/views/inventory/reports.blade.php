@extends('layouts.dash')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div class="d-flex align-items-center">
            <a href="{{ route('inventory.index') }}" class="btn btn-outline-secondary me-3">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h2 class="fw-bold text-primary mb-1">
                    <i class="fas fa-chart-line me-2"></i>Inventory Reports
                </h2>
                <p class="text-muted mb-0">Analyze your inventory usage and costs</p>
            </div>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-outline-primary" onclick="window.print()">
                <i class="fas fa-print me-1"></i> Print Report
            </button>
            <a href="{{ route('inventory.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i> Add Item
            </a>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="d-flex align-items-center justify-content-center mb-2">
                        <div class="rounded-circle bg-primary bg-opacity-10 p-3">
                            <i class="fas fa-shopping-cart fa-2x text-primary"></i>
                        </div>
                    </div>
                    <h4 class="text-primary mb-1">KSh {{ number_format($monthlyUsage, 2) }}</h4>
                    <p class="card-text text-muted small">This Month's Usage Cost</p>
                    <small class="text-muted">{{ now()->format('F Y') }}</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="d-flex align-items-center justify-content-center mb-2">
                        <div class="rounded-circle bg-success bg-opacity-10 p-3">
                            <i class="fas fa-boxes fa-2x text-success"></i>
                        </div>
                    </div>
                    <h4 class="text-success mb-1">{{ $topUsedItems->count() }}</h4>
                    <p class="card-text text-muted small">Active Items Used</p>
                    <small class="text-muted">This month</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="d-flex align-items-center justify-content-center mb-2">
                        <div class="rounded-circle bg-info bg-opacity-10 p-3">
                            <i class="fas fa-tags fa-2x text-info"></i>
                        </div>
                    </div>
                    <h4 class="text-info mb-1">{{ $categoryCosts->count() }}</h4>
                    <p class="card-text text-muted small">Categories with Activity</p>
                    <small class="text-muted">This month</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="d-flex align-items-center justify-content-center mb-2">
                        <div class="rounded-circle bg-warning bg-opacity-10 p-3">
                            <i class="fas fa-percentage fa-2x text-warning"></i>
                        </div>
                    </div>
                    <h4 class="text-warning mb-1">
                        {{ $monthlyUsage > 0 ? number_format(($monthlyUsage / ($monthlyUsage + 10000)) * 100, 1) : 0 }}%
                    </h4>
                    <p class="card-text text-muted small">Cost Impact</p>
                    <small class="text-muted">On total expenses</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Top Used Items -->
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-trophy me-2"></i>Most Used Items This Month
                    </h5>
                </div>
                <div class="card-body">
                    @if($topUsedItems->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Rank</th>
                                        <th>Item</th>
                                        <th>Category</th>
                                        <th>Quantity Used</th>
                                        <th>Total Cost</th>
                                        <th>Avg. Cost per Use</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($topUsedItems as $index => $usage)
                                    <tr>
                                        <td>
                                            <span class="badge {{ $index < 3 ? ['bg-warning', 'bg-secondary', 'bg-warning'][$index] : 'bg-light text-dark' }}">
                                                #{{ $index + 1 }}
                                            </span>
                                        </td>
                                        <td>
                                            <div>
                                                <div class="fw-semibold">{{ $usage->inventoryItem->name }}</div>
                                                @if($usage->inventoryItem->brand)
                                                    <small class="text-muted">{{ $usage->inventoryItem->brand }}</small>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">
                                                {{ ucfirst(str_replace('_', ' ', $usage->inventoryItem->category)) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="fw-semibold text-primary">{{ $usage->total_used }}</span>
                                            <small class="text-muted">{{ $usage->inventoryItem->unit_type }}</small>
                                        </td>
                                        <td class="fw-semibold">KSh {{ number_format($usage->total_cost, 2) }}</td>
                                        <td>KSh {{ number_format($usage->total_cost / $usage->total_used, 2) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-chart-bar fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No usage data for this month yet.</p>
                            <small class="text-muted">Start recording inventory usage in your services to see reports here.</small>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Category Breakdown -->
        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-pie me-2"></i>Cost by Category
                    </h5>
                </div>
                <div class="card-body">
                    @if($categoryCosts->count() > 0)
                        @php
                            $totalCategoryCost = $categoryCosts->sum();
                            $colors = ['primary', 'success', 'warning', 'info', 'secondary', 'danger'];
                        @endphp
                        
                        @foreach($categoryCosts as $category => $cost)
                            @php
                                $percentage = $totalCategoryCost > 0 ? ($cost / $totalCategoryCost) * 100 : 0;
                                $colorClass = $colors[$loop->index % count($colors)];
                            @endphp
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="fw-semibold">{{ ucfirst(str_replace('_', ' ', $category)) }}</span>
                                    <span class="text-muted">{{ number_format($percentage, 1) }}%</span>
                                </div>
                                <div class="progress mb-1" style="height: 8px;">
                                    <div class="progress-bar bg-{{ $colorClass }}" 
                                         style="width: {{ $percentage }}%"></div>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <small class="text-muted">KSh {{ number_format($cost, 2) }}</small>
                                </div>
                            </div>
                        @endforeach
                        
                        <div class="border-top pt-3 mt-3">
                            <div class="d-flex justify-content-between fw-bold">
                                <span>Total:</span>
                                <span>KSh {{ number_format($totalCategoryCost, 2) }}</span>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-chart-pie fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No category data available.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Monthly Analysis -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-calendar-alt me-2"></i>Monthly Inventory Analysis
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Monthly Trends -->
                        <div class="col-md-6">
                            <h6 class="text-primary mb-3">
                                <i class="fas fa-trending-up me-2"></i>Usage Trends
                            </h6>
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span>Most Used Category:</span>
                                    <span class="fw-semibold">
                                        @if($categoryCosts->count() > 0)
                                            {{ ucfirst(str_replace('_', ' ', $categoryCosts->keys()->first())) }}
                                        @else
                                            N/A
                                        @endif
                                    </span>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span>Average Cost per Item:</span>
                                    <span class="fw-semibold">
                                        KSh {{ $topUsedItems->count() > 0 ? number_format($monthlyUsage / $topUsedItems->count(), 2) : '0.00' }}
                                    </span>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span>Items Used This Month:</span>
                                    <span class="fw-semibold">{{ $topUsedItems->count() }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Cost Analysis -->
                        <div class="col-md-6">
                            <h6 class="text-success mb-3">
                                <i class="fas fa-dollar-sign me-2"></i>Cost Analysis
                            </h6>
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span>Total Monthly Usage:</span>
                                    <span class="fw-semibold text-primary">KSh {{ number_format($monthlyUsage, 2) }}</span>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span>Daily Average:</span>
                                    <span class="fw-semibold">KSh {{ number_format($monthlyUsage / now()->day, 2) }}</span>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span>Projected Monthly:</span>
                                    <span class="fw-semibold text-warning">
                                        KSh {{ number_format(($monthlyUsage / now()->day) * now()->daysInMonth, 2) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recommendations -->
                    <div class="border-top pt-4 mt-4">
                        <h6 class="text-info mb-3">
                            <i class="fas fa-lightbulb me-2"></i>Recommendations
                        </h6>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="alert alert-info">
                                    <h6 class="alert-heading">Cost Optimization</h6>
                                    @if($monthlyUsage > 5000)
                                        <p class="mb-0">Consider bulk purchasing for high-usage items to reduce costs.</p>
                                    @elseif($monthlyUsage > 0)
                                        <p class="mb-0">Monitor usage patterns to optimize inventory levels.</p>
                                    @else
                                        <p class="mb-0">Start tracking inventory usage to identify cost optimization opportunities.</p>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="alert alert-warning">
                                    <h6 class="alert-heading">Stock Management</h6>
                                    @if($topUsedItems->count() > 5)
                                        <p class="mb-0">Focus on managing top 5 most-used items for better efficiency.</p>
                                    @else
                                        <p class="mb-0">Maintain adequate stock levels for all active items.</p>
                                    @endif
                                </div>
                            </div>
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
document.addEventListener('DOMContentLoaded', function() {
    // Simple chart visualization could be added here
    // For now, we're using progress bars and tables
});
</script>
@endpush

@push('styles')
<style>
@media print {
    .btn, .sidebar, .navbar {
        display: none !important;
    }
    .container-fluid {
        margin: 0 !important;
        padding: 0 !important;
    }
    .card {
        border: 1px solid #ddd !important;
        box-shadow: none !important;
    }
}
</style>
@endpush
