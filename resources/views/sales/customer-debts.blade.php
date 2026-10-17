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
                                @if($debt->status !== 'paid' && $debt->order_id)
                                <button type="button" class="btn btn-sm btn-success"
                                        onclick="openDebtPayment({{ $debt->order_id }}, {{ $debt->balance }}, '{{ addslashes($debt->customer->name) }}')">
                                    <i class="fas fa-money-bill-wave me-1"></i>Record Payment
                                </button>
                                @elseif($debt->status === 'paid')
                                <span class="text-success"><i class="fas fa-check-circle"></i></span>
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
                {{ $debts->appends(request()->query())->links('pagination::bootstrap-5') }}
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

<!-- Record Debt Payment Modal -->
<div class="modal fade" id="debtPaymentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background: var(--card-bg); border: 1px solid var(--border-color);">
            <div class="modal-header" style="background: var(--bg-tertiary); border-bottom: 1px solid var(--border-color);">
                <h5 class="modal-title" style="color: var(--text-primary);">
                    <i class="fas fa-money-bill-wave text-success me-2"></i>Record Payment
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="color: var(--text-primary);">
                <form id="debtPaymentForm">
                    <input type="hidden" id="debtOrderId">

                    <div class="alert alert-info">
                        <strong>Customer:</strong> <span id="debtCustomerName"></span><br>
                        <strong>Balance Due:</strong> KSh <span id="debtBalance">0.00</span>
                    </div>

                    <div class="mb-3">
                        <label for="debtPaymentMethod" class="form-label">Payment Method <span class="text-danger">*</span></label>
                        <select class="form-select" id="debtPaymentMethod" name="payment_method" required>
                            <option value="">Select payment method</option>
                            <option value="cash">Cash</option>
                            <option value="mpesa">M-Pesa</option>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="card">Card</option>
                            <option value="cheque">Cheque</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="debtAmountPaid" class="form-label">Amount Paid <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">KSh</span>
                            <input type="number" class="form-control" id="debtAmountPaid" name="amount_paid" step="0.01" min="0.01" required>
                        </div>
                        <small class="text-muted">Enter a partial amount if the customer is paying in installments.</small>
                    </div>

                    <div class="mb-3">
                        <label for="debtTransactionRef" class="form-label">Transaction Reference (Optional)</label>
                        <input type="text" class="form-control" id="debtTransactionRef" name="transaction_reference" placeholder="e.g., M-Pesa code, cheque number">
                    </div>

                    <div class="mb-3">
                        <label for="debtPaymentNotes" class="form-label">Notes (Optional)</label>
                        <textarea class="form-control" id="debtPaymentNotes" name="notes" rows="2" placeholder="Any additional notes about this payment"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer" style="border-top: 1px solid var(--border-color);">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" id="confirmDebtPaymentBtn">
                    <i class="fas fa-check me-2"></i>Record Payment
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function openDebtPayment(orderId, balance, customerName) {
    document.getElementById('debtPaymentForm').reset();
    document.getElementById('debtOrderId').value = orderId;
    document.getElementById('debtCustomerName').textContent = customerName;
    document.getElementById('debtBalance').textContent = parseFloat(balance).toFixed(2);
    document.getElementById('debtAmountPaid').value = parseFloat(balance).toFixed(2);
    document.getElementById('debtAmountPaid').max = parseFloat(balance).toFixed(2);

    new bootstrap.Modal(document.getElementById('debtPaymentModal')).show();
}

document.getElementById('confirmDebtPaymentBtn').addEventListener('click', async function() {
    const form = document.getElementById('debtPaymentForm');
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }

    const orderId = document.getElementById('debtOrderId').value;
    const data = Object.fromEntries(new FormData(form).entries());

    const btn = this;
    const originalText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';

    try {
        const response = await fetch(`/sales/orders/${orderId}/record-payment`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        });

        const result = await response.json();

        if (result.success) {
            bootstrap.Modal.getInstance(document.getElementById('debtPaymentModal')).hide();
            alert(result.message || 'Payment recorded successfully!');
            location.reload();
        } else {
            alert('Error: ' + (result.message || 'Failed to record payment'));
        }
    } catch (error) {
        alert('Error: ' + error.message);
    } finally {
        btn.disabled = false;
        btn.innerHTML = originalText;
    }
});
</script>

@endsection
