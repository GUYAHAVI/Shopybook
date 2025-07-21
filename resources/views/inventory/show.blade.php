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
                <h2 class="fw-bold text-primary mb-1">{{ $inventory->name }}</h2>
                <p class="text-muted mb-0">
                    {{ $inventory->category ? ucfirst(str_replace('_', ' ', $inventory->category)) : 'No Category' }}
                    @if($inventory->brand) • {{ $inventory->brand }} @endif
                    @if($inventory->model) • {{ $inventory->model }} @endif
                </p>
            </div>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#adjustModal">
                <i class="fas fa-plus-minus me-1"></i> Adjust Stock
            </button>
            <a href="{{ route('inventory.edit', $inventory) }}" class="btn btn-primary">
                <i class="fas fa-edit me-1"></i> Edit Item
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Item Details Card -->
        <div class="col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-info-circle me-2"></i>Item Details
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-6">
                            <small class="text-muted">Current Stock</small>
                            <div class="fw-bold fs-4 text-primary">
                                {{ $inventory->current_quantity }}
                                <small class="text-muted fs-6">{{ $inventory->unit_type }}</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">Status</small>
                            <div>
                                <span class="badge {{ $inventory->status_badge }} fs-6">
                                    {{ ucfirst(str_replace('_', ' ', $inventory->status)) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-6">
                            <small class="text-muted">Unit Cost</small>
                            <div class="fw-semibold">KSh {{ number_format($inventory->unit_cost, 2) }}</div>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">Total Value</small>
                            <div class="fw-semibold text-success">KSh {{ number_format($inventory->total_value, 2) }}</div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-6">
                            <small class="text-muted">Minimum Stock</small>
                            <div class="fw-semibold">{{ $inventory->minimum_quantity }} {{ $inventory->unit_type }}</div>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">Maximum Stock</small>
                            <div class="fw-semibold">
                                {{ $inventory->maximum_quantity ? $inventory->maximum_quantity . ' ' . $inventory->unit_type : 'Not Set' }}
                            </div>
                        </div>
                    </div>

                    @if($inventory->storage_location)
                    <div class="mb-3">
                        <small class="text-muted">Storage Location</small>
                        <div class="fw-semibold">
                            <i class="fas fa-map-marker-alt me-1 text-muted"></i>
                            {{ $inventory->storage_location }}
                        </div>
                    </div>
                    @endif

                    @if($inventory->supplier)
                    <div class="mb-3">
                        <small class="text-muted">Supplier</small>
                        <div class="fw-semibold">{{ $inventory->supplier }}</div>
                    </div>
                    @endif

                    @if($inventory->purchase_date)
                    <div class="mb-3">
                        <small class="text-muted">Purchase Date</small>
                        <div class="fw-semibold">{{ $inventory->purchase_date->format('M j, Y') }}</div>
                    </div>
                    @endif

                    @if($inventory->expiry_date)
                    <div class="mb-3">
                        <small class="text-muted">Expiry Date</small>
                        <div class="fw-semibold {{ $inventory->is_expiring_soon ? 'text-warning' : '' }}">
                            {{ $inventory->expiry_date->format('M j, Y') }}
                            @if($inventory->is_expiring_soon)
                                <i class="fas fa-exclamation-triangle ms-1"></i>
                            @endif
                        </div>
                    </div>
                    @endif

                    @if($inventory->description)
                    <div class="mb-3">
                        <small class="text-muted">Description</small>
                        <div>{{ $inventory->description }}</div>
                    </div>
                    @endif

                    @if($inventory->notes)
                    <div class="mb-3">
                        <small class="text-muted">Notes</small>
                        <div class="small">{{ $inventory->notes }}</div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Transaction History -->
        <div class="col-lg-8 mb-4">
            <div class="card h-100">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-history me-2"></i>Transaction History
                    </h5>
                    <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#adjustModal">
                        <i class="fas fa-plus me-1"></i> New Transaction
                    </button>
                </div>
                <div class="card-body p-0">
                    @if($transactions->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Date</th>
                                        <th>Type</th>
                                        <th>Quantity</th>
                                        <th>Cost</th>
                                        <th>Staff</th>
                                        <th>Notes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($transactions as $transaction)
                                    <tr>
                                        <td>
                                            <div class="fw-semibold">{{ $transaction->transaction_date->format('M j, Y') }}</div>
                                            <small class="text-muted">{{ $transaction->created_at->format('g:i A') }}</small>
                                        </td>
                                        <td>
                                            <span class="badge {{ $transaction->transaction_type_badge }}">
                                                {{ ucfirst(str_replace('_', ' ', $transaction->transaction_type)) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="{{ $transaction->quantity > 0 ? 'text-success' : 'text-danger' }}">
                                                {{ $transaction->quantity > 0 ? '+' : '' }}{{ $transaction->quantity }}
                                            </span>
                                            <small class="text-muted">{{ $inventory->unit_type }}</small>
                                        </td>
                                        <td>
                                            @if($transaction->total_cost)
                                                KSh {{ number_format($transaction->total_cost, 2) }}
                                                @if($transaction->unit_cost)
                                                    <br><small class="text-muted">@ KSh {{ number_format($transaction->unit_cost, 2) }}</small>
                                                @endif
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($transaction->staff)
                                                {{ $transaction->staff->name }}
                                            @else
                                                <span class="text-muted">System</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($transaction->notes)
                                                <span data-bs-toggle="tooltip" title="{{ $transaction->notes }}">
                                                    {{ Str::limit($transaction->notes, 30) }}
                                                </span>
                                            @endif
                                            @if($transaction->reference_number)
                                                <br><small class="text-muted">Ref: {{ $transaction->reference_number }}</small>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        @if($transactions->hasPages())
                            <div class="d-flex justify-content-center p-3">
                                {{ $transactions->links() }}
                            </div>
                        @endif
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-history fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No transactions recorded yet.</p>
                            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#adjustModal">
                                <i class="fas fa-plus me-1"></i> Add First Transaction
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Adjust Quantity Modal -->
<div class="modal fade" id="adjustModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" action="{{ route('inventory.adjust', $inventory) }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Adjust {{ $inventory->name }} Stock</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="alert alert-info">
                                <strong>Current Stock:</strong> {{ $inventory->current_quantity }} {{ $inventory->unit_type }}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="alert alert-warning">
                                <strong>Minimum Level:</strong> {{ $inventory->minimum_quantity }} {{ $inventory->unit_type }}
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Action Type</label>
                            <select name="adjustment_type" class="form-select" required>
                                <option value="add">Add Stock (+)</option>
                                <option value="subtract">Remove Stock (-)</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Quantity</label>
                            <input type="number" name="quantity" class="form-control" min="1" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Unit Cost (Optional)</label>
                            <input type="number" name="unit_cost" class="form-control" step="0.01" min="0" 
                                   placeholder="{{ $inventory->unit_cost }}">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Transaction Type</label>
                        <select name="transaction_type" class="form-select" required>
                            @foreach(\App\Models\InventoryTransaction::getTransactionTypes() as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Reference Number (Optional)</label>
                        <input type="text" name="reference_number" class="form-control" 
                               placeholder="Invoice number, receipt, etc.">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-control" rows="3" 
                                  placeholder="Additional details about this transaction..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Stock</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Adjustment type change handler
    const adjustmentType = document.querySelector('select[name="adjustment_type"]');
    const transactionType = document.querySelector('select[name="transaction_type"]');
    
    if (adjustmentType && transactionType) {
        adjustmentType.addEventListener('change', function() {
            const options = transactionType.querySelectorAll('option');
            options.forEach(option => {
                if (this.value === 'add') {
                    // Show addition types
                    option.style.display = ['purchase', 'return', 'repair_in', 'adjustment'].includes(option.value) ? 'block' : 'none';
                } else {
                    // Show subtraction types
                    option.style.display = ['usage', 'wastage', 'transfer', 'repair_out', 'sale', 'adjustment'].includes(option.value) ? 'block' : 'none';
                }
            });
        });
    }
});
</script>
@endpush
