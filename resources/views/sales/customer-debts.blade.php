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
            <a href="{{ route('sales.customer-debts') }}" class="nav-tab active">
                <i class="fas fa-money-bill-wave me-1"></i> Customer Debts
            </a>
            <a href="{{ route('sales.supplier-debts') }}" class="nav-tab">
                <i class="fas fa-file-invoice-dollar me-1"></i> Supplier Debts
            </a>
        </div>
    </div>

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 mb-3 mb-sm-0" style="color: var(--text-primary);">Customer Debts (Accounts Receivable)</h1>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card summary-card">
                <div class="card-body">
                    <h6 class="text-muted mb-2">Total Debts</h6>
                    <h3 class="mb-0">{{ number_format($summary['total_debts'], 2) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card summary-card">
                <div class="card-body">
                    <h6 class="text-muted mb-2">Total Paid</h6>
                    <h3 class="mb-0 text-success">{{ number_format($summary['total_paid'], 2) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card summary-card">
                <div class="card-body">
                    <h6 class="text-muted mb-2">Balance Owed</h6>
                    <h3 class="mb-0 text-danger">{{ number_format($summary['total_balance'], 2) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card summary-card">
                <div class="card-body">
                    <h6 class="text-muted mb-2">Overdue</h6>
                    <h3 class="mb-0 text-warning">{{ $summary['overdue_count'] }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('sales.customer-debts') }}" class="row g-3">
                <div class="col-md-4">
                    <label for="search" class="form-label">Search Customer</label>
                    <input type="text" class="form-control" id="search" name="search" 
                           value="{{ request('search') }}" placeholder="Name or phone">
                </div>
                <div class="col-md-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>All</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="partial" {{ request('status') == 'partial' ? 'selected' : '' }}>Partial</option>
                        <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>Overdue</option>
                        <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-filter me-1"></i> Filter
                    </button>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <a href="{{ route('sales.customer-debts') }}" class="btn btn-secondary w-100">
                        <i class="fas fa-redo me-1"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Debts Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Customer</th>
                            <th>Invoice</th>
                            <th>Total Amount</th>
                            <th>Amount Paid</th>
                            <th>Balance</th>
                            <th>Due Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($debts as $debt)
                        <tr>
                            <td>
                                <strong>{{ $debt->customer->name }}</strong><br>
                                <small class="text-muted">{{ $debt->customer->phone }}</small>
                            </td>
                            <td>{{ $debt->order->invoice_number ?? 'N/A' }}</td>
                            <td>{{ number_format($debt->total_amount, 2) }}</td>
                            <td class="text-success">{{ number_format($debt->amount_paid, 2) }}</td>
                            <td class="text-danger">{{ number_format($debt->balance, 2) }}</td>
                            <td>{{ $debt->due_date ? $debt->due_date->format('d M Y') : 'N/A' }}</td>
                            <td>
                                @if($debt->status === 'paid')
                                    <span class="badge bg-success">Paid</span>
                                @elseif($debt->status === 'overdue')
                                    <span class="badge bg-danger">Overdue</span>
                                @elseif($debt->status === 'partial')
                                    <span class="badge bg-warning">Partial</span>
                                @else
                                    <span class="badge bg-secondary">Pending</span>
                                @endif
                            </td>
                            <td>
                                @if($debt->status !== 'paid')
                                <button type="button" class="btn btn-sm btn-success" 
                                        onclick="viewInvoiceForPayment({{ $debt->order_id }})">
                                    <i class="fas fa-money-bill-wave"></i> View Invoice
                                </button>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No customer debts found.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-4">
                {{ $debts->appends(request()->query())->links() }}
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

<script>
function viewInvoiceForPayment(orderId) {
    // Redirect to orders page where the payment modal can be opened
    window.location.href = "{{ route('sales.orders') }}?highlight=" + orderId;
}
</script>

@endsection
