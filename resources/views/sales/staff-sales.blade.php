@extends('layouts.dash')

@section('content')

<div class="container-fluid">
    <!-- Sub-navigation for Sales -->
    <div class="sub-navigation mb-4">
        <div class="nav-tabs">
            <a href="{{ route('sales.customers') }}" class="nav-tab">
                <i class="fas fa-users me-1"></i> Customers
            </a>
            <a href="{{ route('sales.orders') }}" class="nav-tab">
                <i class="fas fa-shopping-cart me-1"></i> Orders
            </a>
            <a href="{{ route('sales.pos') }}" class="nav-tab">
                <i class="fas fa-cash-register me-1"></i> POS
            </a>
            <a href="{{ route('sales.customer-debts') }}" class="nav-tab">
                <i class="fas fa-money-bill-wave me-1"></i> Customer Debts
            </a>
            <a href="{{ route('sales.supplier-debts') }}" class="nav-tab">
                <i class="fas fa-file-invoice-dollar me-1"></i> Supplier Debts
            </a>
            <a href="{{ route('sales.staff-sales') }}" class="nav-tab active">
                <i class="fas fa-user-tie me-1"></i> Staff Sales
            </a>
        </div>
    </div>

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 mb-3 mb-sm-0" style="color: var(--text-primary);">Sales by Staff</h1>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card summary-card">
                <div class="card-body">
                    <h6 class="text-muted mb-2">Total Sales</h6>
                    <h3 class="mb-0">{{ number_format($summary['total_sales'], 2) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card summary-card">
                <div class="card-body">
                    <h6 class="text-muted mb-2">Orders</h6>
                    <h3 class="mb-0">{{ $summary['total_orders'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card summary-card">
                <div class="card-body">
                    <h6 class="text-muted mb-2">Staff Selling</h6>
                    <h3 class="mb-0">{{ $summary['staff_count'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card summary-card">
                <div class="card-body">
                    <h6 class="text-muted mb-2">Top Performer</h6>
                    <h5 class="mb-0 text-success">{{ $summary['top_performer'] ?? '—' }}</h5>
                </div>
            </div>
        </div>
    </div>

    <!-- Period Filter -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('sales.staff-sales') }}" class="row g-3 align-items-end">
                <div class="col-auto">
                    <label class="form-label">Period</label>
                    <div class="btn-group" role="group">
                        @foreach(['today' => 'Today', 'week' => 'This Week', 'month' => 'This Month', 'all' => 'All Time'] as $key => $label)
                            <button type="submit" name="period" value="{{ $key }}"
                                    class="btn btn-sm {{ $period === $key ? 'btn-primary' : 'btn-outline-primary' }}">
                                {{ $label }}
                            </button>
                        @endforeach
                    </div>
                </div>
                <div class="col-auto">
                    <label for="from" class="form-label">From</label>
                    <input type="date" class="form-control form-control-sm" id="from" name="from" value="{{ request('from') }}">
                </div>
                <div class="col-auto">
                    <label for="to" class="form-label">To</label>
                    <input type="date" class="form-control form-control-sm" id="to" name="to" value="{{ request('to') }}">
                </div>
                <div class="col-auto">
                    <button type="submit" name="period" value="custom" class="btn btn-sm btn-secondary">
                        <i class="fas fa-filter me-1"></i> Apply Range
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Staff Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Staff Member</th>
                            <th>Orders</th>
                            <th>Total Sales</th>
                            <th>Collected</th>
                            <th>Balance Due</th>
                            <th>Avg / Order</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($staffGroups as $i => $staff)
                        <tr data-bs-toggle="collapse" data-bs-target="#staff-orders-{{ $i }}" style="cursor:pointer;">
                            <td>
                                <i class="fas fa-user-circle me-2 text-muted"></i>
                                <strong>{{ $staff['user']?->name ?? 'Not recorded' }}</strong>
                                @if(!$staff['user'])
                                    <br><small class="text-muted">older sales or non-POS orders</small>
                                @endif
                            </td>
                            <td>{{ $staff['count'] }}</td>
                            <td class="text-success fw-bold">{{ number_format($staff['total'], 2) }}</td>
                            <td>{{ number_format($staff['paid'], 2) }}</td>
                            <td class="{{ $staff['balance'] > 0 ? 'text-danger' : '' }}">{{ number_format($staff['balance'], 2) }}</td>
                            <td>{{ number_format($staff['count'] ? $staff['total'] / $staff['count'] : 0, 2) }}</td>
                            <td><i class="fas fa-chevron-down text-muted"></i></td>
                        </tr>
                        <tr class="collapse" id="staff-orders-{{ $i }}">
                            <td colspan="7" class="p-0">
                                <div class="p-3" style="background: var(--bg-tertiary);">
                                    <table class="table table-sm mb-0">
                                        <thead>
                                            <tr>
                                                <th>Order #</th>
                                                <th>Customer</th>
                                                <th>Items</th>
                                                <th>Total</th>
                                                <th>Paid</th>
                                                <th>Status</th>
                                                <th>Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($staff['orders'] as $order)
                                            <tr>
                                                <td>{{ $order->order_number }}</td>
                                                <td>{{ $order->customer?->name ?? 'Walk-in Customer' }}</td>
                                                <td>{{ $order->items->sum('quantity') }}</td>
                                                <td>{{ number_format($order->total_amount, 2) }}</td>
                                                <td>{{ number_format($order->amount_paid, 2) }}</td>
                                                <td>
                                                    <span class="badge bg-{{ $order->payment_status === 'paid' ? 'success' : ($order->payment_status === 'partial' ? 'warning' : 'secondary') }}">
                                                        {{ ucfirst($order->payment_status ?? 'pending') }}
                                                    </span>
                                                </td>
                                                <td>{{ $order->created_at->format('M d, H:i') }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No sales in this period.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
.summary-card {
    border-left: 4px solid var(--primary-color);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.sub-navigation {
    background: var(--surface-color, white);
    border-radius: 8px;
    padding: 10px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.nav-tabs {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

.nav-tab {
    padding: 10px 20px;
    border-radius: 6px;
    text-decoration: none;
    color: var(--text-secondary, #666);
    transition: all 0.2s;
    display: flex;
    align-items: center;
    font-weight: 500;
}

.nav-tab:hover {
    background: rgba(var(--primary-color-rgb, 0, 123, 255), 0.1);
    color: var(--primary-color, #007bff);
}

.nav-tab.active {
    background: var(--primary-color, #007bff);
    color: white;
}
</style>

@endsection
