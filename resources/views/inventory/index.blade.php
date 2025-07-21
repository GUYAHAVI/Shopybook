@extends('layouts.dash')

@section('content')
<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-primary mb-1">
                <i class="fas fa-boxes me-2"></i>Inventory Management
            </h2>
            <p class="text-muted mb-0">Track and manage items used in your services</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('inventory.reports') }}" class="btn btn-outline-info">
                <i class="fas fa-chart-line me-1"></i> Reports
            </a>
            <a href="{{ route('inventory.low-stock') }}" class="btn btn-outline-warning">
                <i class="fas fa-exclamation-triangle me-1"></i> Low Stock
            </a>
            <a href="{{ route('inventory.create') }}" class="btn btn-primary">
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
                <div class="col-md-3">
                    <label class="form-label">Search</label>
                    <input type="text" name="search" class="form-control" placeholder="Search items..." 
                           value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
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
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="low_stock" {{ request('status') == 'low_stock' ? 'selected' : '' }}>Low Stock</option>
                        <option value="out_of_stock" {{ request('status') == 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
                        <option value="discontinued" {{ request('status') == 'discontinued' ? 'selected' : '' }}>Discontinued</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-search me-1"></i> Filter
                    </button>
                    <a href="{{ route('inventory.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-1"></i> Clear
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Inventory Items -->
    <div class="card">
        <div class="card-body">
            <x-responsive-table :mobile-cards="$items->map(function($item) {
                return [
                    'title' => $item->name . ($item->brand ? ' - ' . $item->brand : ''),
                    'badge' => [
                        'text' => ucfirst(str_replace('_', ' ', $item->status)),
                        'class' => $item->status_badge
                    ],
                    'fields' => [
                        [
                            'label' => 'Category',
                            'value' => '<span class="badge bg-secondary">' . ucfirst(str_replace('_', ' ', $item->category)) . '</span>'
                        ],
                        [
                            'label' => 'Current Stock',
                            'value' => '<strong>' . $item->current_quantity . '</strong> ' . $item->unit_type
                        ],
                        [
                            'label' => 'Unit Cost',
                            'value' => 'KSh ' . number_format($item->unit_cost, 2)
                        ],
                        [
                            'label' => 'Total Value',
                            'value' => 'KSh ' . number_format($item->total_value, 2)
                        ]
                    ],
                    'actions' => [
                        [
                            'url' => route('inventory.show', $item),
                            'text' => 'View',
                            'icon' => 'fas fa-eye',
                            'class' => 'btn-outline-info btn-sm'
                        ],
                        [
                            'url' => route('inventory.edit', $item),
                            'text' => 'Edit',
                            'icon' => 'fas fa-edit',
                            'class' => 'btn-outline-primary btn-sm'
                        ],
                        [
                            'url' => '#',
                            'text' => 'Adjust',
                            'icon' => 'fas fa-plus-minus',
                            'class' => 'btn-outline-success btn-sm',
                            'onclick' => 'event.preventDefault(); document.getElementById(\'adjustModalBtn' . $item->id . '\').click();'
                        ]
                    ]
                ];
            })->toArray()">
                
                <x-slot name="table">
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
                                                id="adjustModalBtn{{ $item->id }}"
                                                data-bs-toggle="modal" 
                                                data-bs-target="#adjustModal{{ $item->id }}" 
                                                title="Adjust Quantity"
                                                style="display: none;">
                                            <i class="fas fa-plus-minus"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-success d-md-inline d-none" 
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
                </x-slot>
            </x-responsive-table>

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
