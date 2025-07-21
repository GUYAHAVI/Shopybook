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
                <h2 class="fw-bold text-warning mb-1">
                    <i class="fas fa-exclamation-triangle me-2"></i>Low Stock Alert
                </h2>
                <p class="text-muted mb-0">Items that need restocking</p>
            </div>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('inventory.reports') }}" class="btn btn-outline-info">
                <i class="fas fa-chart-line me-1"></i> Reports
            </a>
            <a href="{{ route('inventory.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i> Add Item
            </a>
        </div>
    </div>

    @if($items->count() > 0)
        <!-- Alert Summary -->
        <div class="alert alert-warning border-0 shadow-sm mb-4">
            <div class="d-flex align-items-center">
                <div class="rounded-circle bg-warning bg-opacity-10 p-3 me-3">
                    <i class="fas fa-exclamation-triangle fa-2x text-warning"></i>
                </div>
                <div>
                    <h5 class="alert-heading mb-1">{{ $items->count() }} Item(s) Need Attention</h5>
                    <p class="mb-0">These items are at or below their minimum stock levels and need to be restocked.</p>
                </div>
            </div>
        </div>

        <!-- Low Stock Items -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light">
                <h5 class="card-title mb-0">
                    <i class="fas fa-list me-2"></i>Items Requiring Restock
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Item</th>
                                <th>Category</th>
                                <th>Current Stock</th>
                                <th>Minimum Level</th>
                                <th>Shortage</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($items as $item)
                            <tr class="{{ $item->current_quantity == 0 ? 'table-danger' : 'table-warning' }}">
                                <td>
                                    <div>
                                        <div class="fw-semibold">
                                            <a href="{{ route('inventory.show', $item) }}" class="text-decoration-none">
                                                {{ $item->name }}
                                            </a>
                                        </div>
                                        @if($item->brand)
                                            <small class="text-muted">{{ $item->brand }}</small>
                                        @endif
                                        @if($item->model)
                                            <small class="text-muted"> - {{ $item->model }}</small>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">
                                        {{ ucfirst(str_replace('_', ' ', $item->category)) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <span class="fw-bold {{ $item->current_quantity == 0 ? 'text-danger' : 'text-warning' }}">
                                            {{ $item->current_quantity }}
                                        </span>
                                        <small class="text-muted ms-1">{{ $item->unit_type }}</small>
                                    </div>
                                </td>
                                <td>
                                    <span class="text-muted">{{ $item->minimum_quantity }} {{ $item->unit_type }}</span>
                                </td>
                                <td>
                                    @php
                                        $shortage = $item->minimum_quantity - $item->current_quantity;
                                    @endphp
                                    <span class="badge {{ $shortage > 0 ? 'bg-danger' : 'bg-success' }}">
                                        {{ $shortage > 0 ? $shortage : 0 }} {{ $item->unit_type }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge {{ $item->status_badge }}">
                                        {{ $item->current_quantity == 0 ? 'Out of Stock' : 'Low Stock' }}
                                    </span>
                                    @if($item->is_expiring_soon)
                                        <br><small class="text-warning">
                                            <i class="fas fa-clock"></i> Expiring Soon
                                        </small>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-sm btn-success" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#restockModal{{ $item->id }}" 
                                                title="Restock">
                                            <i class="fas fa-plus"></i> Restock
                                        </button>
                                        <a href="{{ route('inventory.show', $item) }}" 
                                           class="btn btn-sm btn-outline-info" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </div>

                                    <!-- Quick Restock Modal -->
                                    <div class="modal fade" id="restockModal{{ $item->id }}" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form method="POST" action="{{ route('inventory.adjust', $item) }}">
                                                    @csrf
                                                    <input type="hidden" name="adjustment_type" value="add">
                                                    <input type="hidden" name="transaction_type" value="purchase">
                                                    
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">
                                                            <i class="fas fa-plus me-2"></i>Restock {{ $item->name }}
                                                        </h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="alert alert-info">
                                                            <strong>Current:</strong> {{ $item->current_quantity }} {{ $item->unit_type }}<br>
                                                            <strong>Minimum:</strong> {{ $item->minimum_quantity }} {{ $item->unit_type }}<br>
                                                            <strong>Suggested:</strong> {{ $item->minimum_quantity - $item->current_quantity + 10 }} {{ $item->unit_type }}
                                                        </div>

                                                        <div class="row mb-3">
                                                            <div class="col-6">
                                                                <label class="form-label">Quantity to Add</label>
                                                                <input type="number" name="quantity" class="form-control" 
                                                                       min="1" required 
                                                                       value="{{ $item->minimum_quantity - $item->current_quantity + 10 }}">
                                                            </div>
                                                            <div class="col-6">
                                                                <label class="form-label">Unit Cost</label>
                                                                <input type="number" name="unit_cost" class="form-control" 
                                                                       step="0.01" min="0" 
                                                                       value="{{ $item->unit_cost }}">
                                                            </div>
                                                        </div>

                                                        <div class="mb-3">
                                                            <label class="form-label">Reference Number</label>
                                                            <input type="text" name="reference_number" class="form-control" 
                                                                   placeholder="Invoice/Receipt number">
                                                        </div>

                                                        <div class="mb-3">
                                                            <label class="form-label">Notes</label>
                                                            <textarea name="notes" class="form-control" rows="2" 
                                                                      placeholder="Restock notes...">Restocked due to low inventory</textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                            Cancel
                                                        </button>
                                                        <button type="submit" class="btn btn-success">
                                                            <i class="fas fa-plus me-1"></i> Add Stock
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($items->hasPages())
                    <div class="d-flex justify-content-center p-3">
                        {{ $items->links() }}
                    </div>
                @endif
            </div>
        </div>

        <!-- Restock Recommendations -->
        <div class="card border-0 shadow-sm mt-4">
            <div class="card-header bg-light">
                <h5 class="card-title mb-0">
                    <i class="fas fa-lightbulb me-2"></i>Restock Recommendations
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    @php
                        $totalValue = $items->sum(function($item) {
                            $shortage = max(0, $item->minimum_quantity - $item->current_quantity);
                            return $shortage * $item->unit_cost;
                        });
                        $outOfStock = $items->where('current_quantity', 0)->count();
                        $lowStock = $items->where('current_quantity', '>', 0)->count();
                    @endphp
                    
                    <div class="col-md-3">
                        <div class="text-center">
                            <div class="h4 text-danger">{{ $outOfStock }}</div>
                            <small class="text-muted">Out of Stock</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <div class="h4 text-warning">{{ $lowStock }}</div>
                            <small class="text-muted">Low Stock</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <div class="h4 text-info">KSh {{ number_format($totalValue, 2) }}</div>
                            <small class="text-muted">Estimated Restock Cost</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#bulkRestockModal">
                                <i class="fas fa-shopping-cart me-1"></i> Bulk Restock
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    @else
        <!-- No Low Stock Items -->
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <div class="mb-4">
                    <i class="fas fa-check-circle fa-4x text-success"></i>
                </div>
                <h4 class="text-success mb-3">All Stock Levels Are Good!</h4>
                <p class="text-muted mb-4">All your inventory items are above their minimum stock levels.</p>
                <div class="d-flex justify-content-center gap-2">
                    <a href="{{ route('inventory.index') }}" class="btn btn-outline-primary">
                        <i class="fas fa-list me-1"></i> View All Items
                    </a>
                    <a href="{{ route('inventory.reports') }}" class="btn btn-outline-info">
                        <i class="fas fa-chart-line me-1"></i> View Reports
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>

<!-- Bulk Restock Modal -->
<div class="modal fade" id="bulkRestockModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-shopping-cart me-2"></i>Bulk Restock Planning
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    This is a planning tool. Use individual restock buttons to actually update inventory.
                </div>
                
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Current</th>
                                <th>Need</th>
                                <th>Cost per Unit</th>
                                <th>Total Cost</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($items as $item)
                                @php
                                    $shortage = max(0, $item->minimum_quantity - $item->current_quantity);
                                    $totalCost = $shortage * $item->unit_cost;
                                @endphp
                                <tr>
                                    <td>{{ $item->name }}</td>
                                    <td>{{ $item->current_quantity }} {{ $item->unit_type }}</td>
                                    <td>{{ $shortage }} {{ $item->unit_type }}</td>
                                    <td>KSh {{ number_format($item->unit_cost, 2) }}</td>
                                    <td>KSh {{ number_format($totalCost, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="table-primary">
                                <th colspan="4">Total Estimated Cost:</th>
                                <th>KSh {{ number_format($totalValue ?? 0, 2) }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="window.print()">
                    <i class="fas fa-print me-1"></i> Print Shopping List
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-focus quantity input when restock modal opens
    document.querySelectorAll('[id^="restockModal"]').forEach(function(modal) {
        modal.addEventListener('shown.bs.modal', function() {
            const quantityInput = modal.querySelector('input[name="quantity"]');
            if (quantityInput) {
                quantityInput.focus();
                quantityInput.select();
            }
        });
    });
});
</script>
@endpush
