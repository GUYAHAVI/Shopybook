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
            <a href="{{ route('sales.supplier-debts') }}" class="nav-tab active">
                <i class="fas fa-file-invoice-dollar me-1"></i> Supplier Debts
            </a>
        </div>
    </div>

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 mb-3 mb-sm-0" style="color: var(--text-primary);">Supplier Debts (Accounts Payable)</h1>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSupplierDebtModal">
            <i class="fas fa-plus me-1"></i> Add Supplier Debt
        </button>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card summary-card">
                <div class="card-body">
                    <h6 class="text-muted mb-2">Total Owed</h6>
                    <h3 class="mb-0 text-danger">{{ number_format($summary['total_debts'], 2) }}</h3>
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
                    <h6 class="text-muted mb-2">Balance Due</h6>
                    <h3 class="mb-0 text-warning">{{ number_format($summary['total_balance'], 2) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card summary-card">
                <div class="card-body">
                    <h6 class="text-muted mb-2">Overdue</h6>
                    <h3 class="mb-0 text-danger">{{ $summary['overdue_count'] }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('sales.supplier-debts') }}" class="row g-3">
                <div class="col-md-4">
                    <label for="search" class="form-label">Search Supplier</label>
                    <input type="text" class="form-control" id="search" name="search" 
                           value="{{ request('search') }}" placeholder="Supplier name or reference">
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
                    <a href="{{ route('sales.supplier-debts') }}" class="btn btn-secondary w-100">
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
                            <th>Supplier</th>
                            <th>Reference</th>
                            <th>Description</th>
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
                                <strong>{{ $debt->supplier_name }}</strong>
                                @if($debt->supplier_phone)
                                <br><small class="text-muted">{{ $debt->supplier_phone }}</small>
                                @endif
                            </td>
                            <td>{{ $debt->reference_number ?? 'N/A' }}</td>
                            <td>
                                <small>{{ Str::limit($debt->description, 50) }}</small>
                            </td>
                            <td>{{ number_format($debt->total_amount, 2) }}</td>
                            <td class="text-success">{{ number_format($debt->amount_paid, 2) }}</td>
                            <td class="text-danger">{{ number_format($debt->balance, 2) }}</td>
                            <td>{{ $debt->due_date ? \Carbon\Carbon::parse($debt->due_date)->format('d M Y') : 'N/A' }}</td>
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
                                        onclick="recordSupplierPayment({{ $debt->id }}, {{ $debt->balance }})">
                                    <i class="fas fa-money-bill-wave"></i> Pay
                                </button>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-4">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No supplier debts found.</p>
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

<!-- Add Supplier Debt Modal -->
<div class="modal fade" id="addSupplierDebtModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('sales.supplier-debts.store') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Add Supplier Debt</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="supplier_name" class="form-label">Supplier Name *</label>
                        <input type="text" class="form-control" id="supplier_name" name="supplier_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="supplier_phone" class="form-label">Supplier Phone</label>
                        <input type="text" class="form-control" id="supplier_phone" name="supplier_phone">
                    </div>
                    <div class="mb-3">
                        <label for="supplier_email" class="form-label">Supplier Email</label>
                        <input type="email" class="form-control" id="supplier_email" name="supplier_email">
                    </div>
                    <div class="mb-3">
                        <label for="reference_number" class="form-label">Reference Number *</label>
                        <input type="text" class="form-control" id="reference_number" name="reference_number" required>
                        <small class="text-muted">Invoice or PO number</small>
                    </div>
                    <div class="mb-3">
                        <label for="total_amount" class="form-label">Total Amount *</label>
                        <input type="number" step="0.01" class="form-control" id="total_amount" name="total_amount" required>
                    </div>
                    <div class="mb-3">
                        <label for="due_date" class="form-label">Due Date *</label>
                        <input type="date" class="form-control" id="due_date" name="due_date" required min="{{ date('Y-m-d') }}">
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Debt</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Record Payment Modal -->
<div class="modal fade" id="recordSupplierPaymentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="supplierPaymentForm">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Record Payment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="debt_id" name="debt_id">
                    <div class="mb-3">
                        <label for="amount_paid" class="form-label">Amount to Pay *</label>
                        <input type="number" step="0.01" class="form-control" id="amount_paid" name="amount_paid" required>
                        <small class="text-muted">Balance due: <span id="balance_due_display"></span></small>
                    </div>
                    <div class="mb-3">
                        <label for="payment_method" class="form-label">Payment Method *</label>
                        <select class="form-select" id="payment_method" name="payment_method" required>
                            <option value="">Select method</option>
                            <option value="cash">Cash</option>
                            <option value="mpesa">M-Pesa</option>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="card">Card</option>
                            <option value="cheque">Cheque</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="transaction_reference" class="form-label">Transaction Reference</label>
                        <input type="text" class="form-control" id="transaction_reference" name="transaction_reference">
                    </div>
                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea class="form-control" id="notes" name="notes" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Record Payment</button>
                </div>
            </form>
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
function recordSupplierPayment(debtId, balance) {
    document.getElementById('debt_id').value = debtId;
    document.getElementById('amount_paid').value = balance;
    document.getElementById('amount_paid').max = balance;
    document.getElementById('balance_due_display').textContent = balance.toFixed(2);
    new bootstrap.Modal(document.getElementById('recordSupplierPaymentModal')).show();
}

document.getElementById('supplierPaymentForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const debtId = document.getElementById('debt_id').value;
    const formData = new FormData(this);
    const data = Object.fromEntries(formData.entries());
    
    try {
        const response = await fetch(`/sales/debts/suppliers/${debtId}/payment`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (result.success) {
            alert(result.message);
            location.reload();
        } else {
            alert(result.message || 'Failed to record payment');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('An error occurred while recording the payment');
    }
});
</script>

@endsection
