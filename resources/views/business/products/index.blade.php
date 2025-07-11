@extends('layouts.dash')

@section('content')
<div class="container-fluid px-4">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Products Management</h1>
            <p class="text-muted">Manage your product catalog and inventory</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('products.bulk-import') }}" class="btn btn-outline-primary">
                <i class="fas fa-file-import me-2"></i>Bulk Import
            </a>
            <a href="{{ route('products.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Add Product
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
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
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">In Stock</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $products->where('stock_quantity', '>', 0)->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Low Stock</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $products->where('stock_quantity', '<=', 'low_stock_threshold')->where('stock_quantity', '>', 0)->count() }}</div>
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
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $products->where('stock_quantity', '<=', 0)->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-times-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Search & Filters</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('products.index') }}" class="row g-3">
                <div class="col-md-4">
                    <input type="text" class="form-control" name="search" 
                           value="{{ request('search') }}" placeholder="Search products...">
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
                    <select class="form-select" name="stock_status">
                        <option value="">All Stock</option>
                        <option value="in_stock" {{ request('stock_status') == 'in_stock' ? 'selected' : '' }}>In Stock</option>
                        <option value="low_stock" {{ request('stock_status') == 'low_stock' ? 'selected' : '' }}>Low Stock</option>
                        <option value="out_of_stock" {{ request('stock_status') == 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search me-2"></i>Search
                    </button>
                </div>
                <div class="col-md-2">
                    <a href="{{ route('products.index') }}" class="btn btn-outline-secondary w-100">
                        <i class="fas fa-times me-2"></i>Clear
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Products Grid -->
    <div class="row">
        @forelse($products as $product)
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card product-card h-100 shadow-sm">
                <div class="product-image-container">
                    @if($product->main_image)
                        <img src="{{ Storage::url($product->main_image) }}" 
                             class="card-img-top product-image" 
                             alt="{{ $product->name }}">
                    @else
                        <div class="card-img-top product-image-placeholder d-flex align-items-center justify-content-center">
                            <i class="fas fa-image fa-3x text-muted"></i>
                        </div>
                    @endif
                    
                    <div class="position-absolute top-0 end-0 m-2">
                        <span class="badge bg-{{ $product->stock_status_color }}">
                            {{ $product->stock_status_text }}
                        </span>
                    </div>
                    
                    @if($product->is_featured)
                        <div class="position-absolute top-0 start-0 m-2">
                            <span class="badge bg-warning">
                                <i class="fas fa-star me-1"></i>Featured
                            </span>
                        </div>
                    @endif
                </div>

                <div class="card-body d-flex flex-column">
                    <h6 class="card-title text-truncate mb-1">{{ $product->name }}</h6>
                    <p class="card-text text-muted small mb-2">{{ Str::limit($product->description, 60) }}</p>
                    
                    <div class="mb-2">
                        @if($product->sku)
                            <small class="text-muted">SKU: {{ $product->sku }}</small><br>
                        @endif
                        @if($product->category)
                            <small class="text-muted">Category: {{ $product->category }}</small>
                        @endif
                    </div>

                    <div class="mt-auto">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="h6 mb-0 text-primary">{{ $product->formatted_price }}</span>
                            <span class="badge bg-{{ $product->stock_quantity > 0 ? 'success' : 'danger' }}">
                                {{ $product->stock_quantity }} in stock
                            </span>
                        </div>
                        
                        <div class="btn-group w-100" role="group">
                            <a href="{{ route('products.show', $product) }}" 
                               class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('products.edit', $product) }}" 
                               class="btn btn-outline-warning btn-sm">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button type="button" 
                                    class="btn btn-outline-danger btn-sm"
                                    onclick="deleteProduct({{ $product->id }}, '{{ $product->name }}')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="text-center py-5">
                <i class="fas fa-boxes fa-4x text-muted mb-3"></i>
                <h4 class="text-muted">No products found</h4>
                <p class="text-muted">Get started by adding your first product to your catalog.</p>
                <a href="{{ route('products.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Add Your First Product
                </a>
            </div>
        </div>
        @endforelse
    </div>

    @if($products->hasPages())
    <div class="d-flex justify-content-center mt-4">
        {{ $products->links() }}
    </div>
    @endif
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteProductModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Deletion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete <strong id="productName"></strong>?</p>
                <p class="text-danger">This action cannot be undone!</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteProductForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete Product</button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.product-card {
    transition: transform 0.2s, box-shadow 0.2s;
    border: 1px solid #e3e6f0;
}

.product-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}

.product-image-container {
    position: relative;
    height: 200px;
    overflow: hidden;
}

.product-image {
    height: 200px;
    object-fit: cover;
    transition: transform 0.3s;
}

.product-card:hover .product-image {
    transform: scale(1.05);
}

.product-image-placeholder {
    height: 200px;
    background-color: #f8f9fc;
    color: #858796;
}
</style>

<script>
function deleteProduct(productId, productName) {
    document.getElementById('productName').textContent = productName;
    document.getElementById('deleteProductForm').action = `/products/${productId}`;
    
    const modal = new bootstrap.Modal(document.getElementById('deleteProductModal'));
    modal.show();
}
</script>
@endsection 