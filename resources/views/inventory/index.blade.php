@extends('layouts.dash')

@section('content')
<div class="container-fluid py-4">
    <!-- Header Section - Now Responsive -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div class="mb-3 mb-md-0">
            <h2 class="fw-bold text-primary mb-1">
                <i class="fas fa-boxes me-2"></i>Inventory Management
            </h2>
            <p class="text-muted mb-0">Track and manage items used in your services</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('inventory.reports') }}" class="btn btn-outline-info btn-sm">
                <i class="fas fa-chart-line me-1"></i> Reports
            </a>
            <a href="{{ route('inventory.low-stock') }}" class="btn btn-outline-warning btn-sm">
                <i class="fas fa-exclamation-triangle me-1"></i> Low Stock
            </a>
            <a href="{{ route('inventory.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus me-1"></i> Add Item
            </a>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="d-flex align-items-center justify-content-center mb-2">
                        <div class="rounded-circle bg-primary bg-opacity-10 p-3">
                            <i class="fas fa-boxes fa-2x text-primary"></i>
                        </div>
                    </div>
                    <h5 class="card-title mb-1">{{ $summary['total_items'] }}</h5>
                    <p class="card-text text-muted small">Total Items</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="d-flex align-items-center justify-content-center mb-2">
                        <div class="rounded-circle bg-warning bg-opacity-10 p-3">
                            <i class="fas fa-exclamation-triangle fa-2x text-warning"></i>
                        </div>
                    </div>
                    <h5 class="card-title mb-1">{{ $summary['low_stock_items'] }}</h5>
                    <p class="card-text text-muted small">Low Stock</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="d-flex align-items-center justify-content-center mb-2">
                        <div class="rounded-circle bg-danger bg-opacity-10 p-3">
                            <i class="fas fa-times-circle fa-2x text-danger"></i>
                        </div>
                    </div>
                    <h5 class="card-title mb-1">{{ $summary['out_of_stock_items'] }}</h5>
                    <p class="card-text text-muted small">Out of Stock</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="d-flex align-items-center justify-content-center mb-2">
                        <div class="rounded-circle bg-success bg-opacity-10 p-3">
                            <i class="fas fa-dollar-sign fa-2x text-success"></i>
                        </div>
                    </div>
                    <h5 class="card-title mb-1">KSh {{ number_format($summary['total_value'], 2) }}</h5>
                    <p class="card-text text-muted small">Total Value</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3 col-sm-6">
                    <label class="form-label">Search</label>
                    <input type="text" name="search" class="form-control" placeholder="Search items..." 
                           value="{{ request('search') }}">
                </div>
                <div class="col-md-3 col-sm-6">
                    <label class="form-label">Category</label>
                    <select name="category" class="form-select">
                        <option value="">All Categories</option>
                        @foreach(\App\Models\InventoryItem::getCategories() as $key => $label)
                            <option value="{{ $key }}" {{ request('category') == $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 col-sm-6">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="low_stock" {{ request('status') == 'low_stock' ? 'selected' : '' }}>Low Stock</option>
                        <option value="out_of_stock" {{ request('status') == 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
                        <option value="discontinued" {{ request('status') == 'discontinued' ? 'selected' : '' }}>Discontinued</option>
                    </select>
                </div>
                <div class="col-md-3 col-sm-6 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2 flex-grow-1">
                        <i class="fas fa-search me-1"></i> Filter
                    </button>
                    <a href="{{ route('inventory.index') }}" class="btn btn-outline-secondary flex-grow-1">
                        <i class="fas fa-times me-1"></i> Clear
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Inventory Items -->
    <div class="card">
        <div class="card-body">
            @if($items && $items->count() > 0)
                <!-- Desktop Table View -->
                <div class="d-none d-md-block">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Item</th>
                                    <th>Category</th>
                                    <th>Current Stock</th>
                                    <th>Unit Cost</th>
                                    <th>Total Value</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($items as $item)
                                <tr>
                                    <td>
                                        <div>
                                            <div class="fw-semibold">{{ $item->name }}</div>
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
                                        <div>
                                            <span class="fw-semibold">{{ $item->current_quantity }}</span>
                                            <small class="text-muted">{{ $item->unit_type }}</small>
                                        </div>
                                        @if($item->minimum_quantity > 0)
                                            <small class="text-muted">Min: {{ $item->minimum_quantity }}</small>
                                        @endif
                                    </td>
                                    <td>KSh {{ number_format($item->unit_cost, 2) }}</td>
                                    <td>KSh {{ number_format($item->total_value, 2) }}</td>
                                    <td>
                                        <span class="badge {{ $item->status_badge }}">
                                            {{ ucfirst(str_replace('_', ' ', $item->status)) }}
                                        </span>
                                        @if($item->is_expiring_soon)
                                            <br><small class="text-warning">
                                                <i class="fas fa-exclamation-triangle"></i> Expiring Soon
                                            </small>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('inventory.show', $item) }}" 
                                               class="btn btn-sm btn-outline-info" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('inventory.edit', $item) }}" 
                                               class="btn btn-sm btn-outline-primary" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-success" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#adjustModal{{ $item->id }}" 
                                                    title="Adjust Quantity">
                                                <i class="fas fa-plus-minus"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="fas fa-boxes fa-3x mb-3"></i>
                                            <p>No inventory items found.</p>
                                            <a href="{{ route('inventory.create') }}" class="btn btn-primary">
                                                <i class="fas fa-plus me-1"></i> Add Your First Item
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Mobile Card View -->
                <div class="d-block d-md-none">
                    @foreach($items as $item)
                        <div class="mobile-card card border-0 shadow-sm mb-3">
                            <div class="card-body p-3">
                                <!-- Header Section -->
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div class="flex-grow-1">
                                        <h6 class="fw-bold mb-1 text-primary mobile-card-title">
                                            {{ $item->name }}{{ $item->brand ? ' - ' . $item->brand : '' }}
                                        </h6>
                                    </div>
                                    <span class="badge {{ $item->status_badge }} mobile-card-badge">
                                        {{ ucfirst(str_replace('_', ' ', $item->status)) }}
                                    </span>
                                </div>

                                <!-- Details Section - Vertical Layout -->
                                <div class="mobile-card-details">
                                    <div class="row g-2">
                                        <div class="col-6">
                                            <div class="detail-item">
                                                <small class="text-muted fw-medium">Category</small>
                                                <div class="detail-value">
                                                    <span class="badge bg-secondary">{{ ucfirst(str_replace('_', ' ', $item->category)) }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="detail-item">
                                                <small class="text-muted fw-medium">Current Stock</small>
                                                <div class="detail-value">
                                                    <strong>{{ $item->current_quantity }}</strong> {{ $item->unit_type }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="detail-item">
                                                <small class="text-muted fw-medium">Unit Cost</small>
                                                <div class="detail-value">KSh {{ number_format($item->unit_cost, 2) }}</div>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="detail-item">
                                                <small class="text-muted fw-medium">Total Value</small>
                                                <div class="detail-value text-success fw-semibold">
                                                    KSh {{ number_format($item->total_value, 2) }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Actions Section -->
                                <div class="mobile-card-actions mt-3 pt-3 border-top">
                                    <div class="d-flex gap-2 flex-wrap">
                                        <a href="{{ route('inventory.show', $item) }}" 
                                           class="btn btn-outline-info btn-sm mobile-action-btn">
                                            <i class="fas fa-eye me-1"></i> View
                                        </a>
                                        <a href="{{ route('inventory.edit', $item) }}" 
                                           class="btn btn-outline-primary btn-sm mobile-action-btn">
                                            <i class="fas fa-edit me-1"></i> Edit
                                        </a>
                                        <button type="button" 
                                                class="btn btn-outline-success btn-sm mobile-action-btn"
                                                data-bs-toggle="modal" 
                                                data-bs-target="#adjustModal{{ $item->id }}">
                                            <i class="fas fa-plus-minus me-1"></i> Adjust
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-boxes fa-3x text-muted mb-3"></i>
                    <h5>No Inventory Items Found</h5>
                    <p class="text-muted">Add your first inventory item to get started.</p>
                    <a href="{{ route('inventory.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i> Add Your First Item
                    </a>
                </div>
            @endif

            <!-- Modals for Adjustments -->
            @foreach($items as $item)
                <!-- Adjust Quantity Modal -->
                <div class="modal fade" id="adjustModal{{ $item->id }}" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form method="POST" action="{{ route('inventory.adjust', $item) }}">
                                @csrf
                                <div class="modal-header">
                                    <h5 class="modal-title">Adjust {{ $item->name }} Quantity</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label class="form-label">Current Stock</label>
                                        <input type="text" class="form-control" value="{{ $item->current_quantity }} {{ $item->unit_type }}" readonly>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-6">
                                            <label class="form-label">Action</label>
                                            <select name="adjustment_type" class="form-select" required>
                                                <option value="add">Add Stock</option>
                                                <option value="subtract">Remove Stock</option>
                                            </select>
                                        </div>
                                        <div class="col-6">
                                            <label class="form-label">Quantity</label>
                                            <input type="number" name="quantity" class="form-control" min="1" required>
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
                                        <label class="form-label">Unit Cost (optional)</label>
                                        <input type="number" name="unit_cost" class="form-control" step="0.01" min="0" placeholder="{{ $item->unit_cost }}">
                                        <small class="text-muted">Leave empty to use current unit cost</small>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Notes</label>
                                        <textarea name="notes" class="form-control" rows="2" placeholder="Optional notes..."></textarea>
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
            @endforeach

            <!-- Pagination -->
            @if($items->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $items->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

@if($summary['expiring_soon'] > 0)
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1050;">
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle me-2"></i>
        <strong>{{ $summary['expiring_soon'] }}</strong> item(s) expiring soon!
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
</div>
@endif

<style>
/* Enhanced Mobile Card Styles */
.mobile-card {
    transition: all 0.2s ease;
    border-radius: 12px;
    background: #fff;
}

.mobile-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15) !important;
}

.mobile-card-title {
    font-size: 1rem;
    color: #495057;
    margin-bottom: 0.25rem;
}

.mobile-card-badge {
    font-size: 0.875rem;
    padding: 0.375rem 0.75rem;
    border-radius: 8px;
    font-weight: 600;
}

.mobile-card-details {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 0.75rem;
    margin: 0.75rem 0;
}

.detail-item {
    margin-bottom: 0.5rem;
}

.detail-item:last-child {
    margin-bottom: 0;
}

.detail-item small {
    display: block;
    font-size: 0.75rem;
    margin-bottom: 0.25rem;
    color: #6c757d;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.detail-value {
    font-size: 0.875rem;
    color: #212529;
    font-weight: 500;
    line-height: 1.4;
    word-break: break-word;
}

.mobile-card-actions {
    border-top: 1px solid #e9ecef;
}

.mobile-action-btn {
    font-size: 0.8rem;
    padding: 0.5rem 0.75rem;
    border-radius: 6px;
    font-weight: 500;
    transition: all 0.2s ease;
}

.mobile-action-btn:hover {
    transform: translateY(-1px);
}

/* Responsive Header Styles */
@media (max-width: 576px) {
    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.8rem;
    }
    
    .btn-sm i {
        margin-right: 0.25rem !important;
    }
}

@media (max-width: 400px) {
    .d-flex.flex-wrap {
        width: 100%;
    }
    
    .d-flex.flex-wrap .btn {
        flex: 1 0 auto;
        min-width: 100%;
        margin-bottom: 0.5rem;
    }
    
    .d-flex.flex-wrap .btn:last-child {
        margin-bottom: 0;
    }
}

/* Mobile Responsive Enhancements */
@media (max-width: 576px) {
    .mobile-card {
        margin: 0 -0.5rem 0.75rem -0.5rem;
        border-radius: 8px;
    }
    
    .mobile-card-title {
        font-size: 0.9rem;
    }
    
    .mobile-card-badge {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
    }
    
    .detail-value {
        font-size: 0.8rem;
    }
    
    .mobile-action-btn {
        font-size: 0.75rem;
        padding: 0.4rem 0.6rem;
    }
}

@media (max-width: 375px) {
    .mobile-card-title {
        font-size: 0.85rem;
    }
    
    .mobile-card-badge {
        font-size: 0.7rem;
    }
    
    .detail-value {
        font-size: 0.75rem;
    }
    
    .mobile-action-btn {
        font-size: 0.7rem;
    }
}
</style>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-submit form on filter change
    document.querySelectorAll('select[name="category"], select[name="status"]').forEach(function(select) {
        select.addEventListener('change', function() {
            this.form.submit();
        });
    });
});
</script>
@endpush