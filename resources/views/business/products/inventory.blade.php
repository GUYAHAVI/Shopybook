@extends('layouts.dash')

@section('content')
<div class="container-fluid px-4">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Inventory Management</h1>
            <p class="text-muted">Track and manage your product inventory</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-primary" onclick="exportInventory()">
                <i class="fas fa-download me-2"></i>Export
            </button>
            <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Products
            </a>
        </div>
    </div>

    <!-- Inventory Stats -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Products</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $products->total() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-boxes fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Stock Value</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">KSh {{ number_format($totalValue, 2) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-coins fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Low Stock Items</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $lowStockCount }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Out of Stock</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $outOfStockCount }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-times-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filters</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('products.inventory') }}" class="row g-3">
                <div class="col-md-3">
                    <input type="text" class="form-control" name="search" 
                           value="{{ request('search') }}" placeholder="Search products...">
                </div>
                <div class="col-md-2">
                    <select class="form-select" name="stock_status">
                        <option value="">All Stock</option>
                        <option value="in_stock" {{ request('stock_status') == 'in_stock' ? 'selected' : '' }}>In Stock</option>
                        <option value="low_stock" {{ request('stock_status') == 'low_stock' ? 'selected' : '' }}>Low Stock</option>
                        <option value="out_of_stock" {{ request('stock_status') == 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select" name="category">
                        <option value="">All Categories</option>
                        @foreach($categories ?? [] as $category)
                            <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>
                                {{ $category }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search me-2"></i>Filter
                    </button>
                </div>
                <div class="col-md-2">
                    <a href="{{ route('products.inventory') }}" class="btn btn-outline-secondary w-100">
                        <i class="fas fa-times me-2"></i>Clear
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Inventory Table -->
    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Inventory List</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>SKU</th>
                            <th>Category</th>
                            <th>Current Stock</th>
                            <th>Low Stock Threshold</th>
                            <th>Stock Value</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $product)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    @if($product->main_image)
                                        <img src="{{ Storage::url($product->main_image) }}" 
                                             class="rounded me-2" style="width: 40px; height: 40px; object-fit: cover;">
                                    @else
                                        <div class="bg-light rounded me-2 d-flex align-items-center justify-content-center" 
                                             style="width: 40px; height: 40px;">
                                            <i class="fas fa-image text-muted"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <div class="fw-bold">{{ $product->name }}</div>
                                        <small class="text-muted">{{ $product->brand ?? 'No Brand' }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $product->sku ?? 'N/A' }}</td>
                            <td>{{ $product->category ?? 'Uncategorized' }}</td>
                            <td>
                                <span class="fw-bold">{{ $product->stock_quantity }}</span>
                            </td>
                            <td>{{ $product->low_stock_threshold ?? 'Not Set' }}</td>
                            <td>KSh {{ number_format($product->stock_quantity * $product->price, 2) }}</td>
                            <td>
                                <span class="badge bg-{{ $product->stock_status_color }}">
                                    {{ $product->stock_status_text }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <button type="button" class="btn btn-outline-primary" 
                                            onclick="adjustStock({{ $product->id }}, '{{ $product->name }}', {{ $product->stock_quantity }})">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-info" 
                                            onclick="viewHistory({{ $product->id }})">
                                        <i class="fas fa-history"></i>
                                    </button>
                                    <a href="{{ route('products.show', $product) }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <i class="fas fa-boxes fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No products found</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($products->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $products->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Stock Adjustment Modal -->
<div class="modal fade" id="stockModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Adjust Stock</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Product</label>
                    <input type="text" class="form-control" id="productName" readonly>
                </div>
                <div class="mb-3">
                    <label for="newStock" class="form-label">New Stock Quantity</label>
                    <input type="number" class="form-control" id="newStock" min="0">
                </div>
                <div class="mb-3">
                    <label for="adjustmentReason" class="form-label">Reason for Adjustment</label>
                    <select class="form-select" id="adjustmentReason">
                        <option value="">Select a reason</option>
                        <option value="received">Received new shipment</option>
                        <option value="sold">Sold items</option>
                        <option value="damaged">Damaged items</option>
                        <option value="returned">Customer returns</option>
                        <option value="lost">Lost items</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="adjustmentNotes" class="form-label">Additional Notes</label>
                    <textarea class="form-control" id="adjustmentNotes" rows="3" placeholder="Any additional details..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveStockAdjustment()">Save Changes</button>
            </div>
        </div>
    </div>
</div>

<script>
let currentProductId = null;

function adjustStock(productId, productName, currentStock) {
    currentProductId = productId;
    document.getElementById('productName').value = productName;
    document.getElementById('newStock').value = currentStock;
    
    const modal = new bootstrap.Modal(document.getElementById('stockModal'));
    modal.show();
}

function saveStockAdjustment() {
    const newStock = document.getElementById('newStock').value;
    const reason = document.getElementById('adjustmentReason').value;
    const notes = document.getElementById('adjustmentNotes').value;
    
    if (!reason) {
        alert('Please select a reason for the adjustment');
        return;
    }
    
    fetch(`/products/${currentProductId}/stock`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            stock_quantity: newStock,
            adjustment_reason: reason,
            notes: notes
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error updating stock: ' + data.message);
        }
    })
    .catch(error => {
        alert('Error updating stock');
    });
}

function viewHistory(productId) {
    // Redirect to product history page or show modal
    alert('Product history feature coming soon!');
}

function exportInventory() {
    // Export inventory data
    window.location.href = '{{ route("products.inventory.export") }}?' + new URLSearchParams(window.location.search);
}
</script>

<style>
.table th {
    background-color: #f8f9fc;
    font-weight: 600;
}

.btn-group-sm .btn {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}
</style>
@endsection 