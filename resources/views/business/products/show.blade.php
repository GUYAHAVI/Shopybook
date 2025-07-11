@extends('layouts.dash')

@section('content')
<div class="container-fluid px-4">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">{{ $product->name }}</h1>
            <p class="text-muted">Product Details</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('products.edit', $product) }}" class="btn btn-warning">
                <i class="fas fa-edit me-2"></i>Edit Product
            </a>
            <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Products
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Product Images -->
        <div class="col-lg-6">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Product Images</h6>
                </div>
                <div class="card-body">
                    @if($product->images && count($product->images) > 0)
                        <div id="productCarousel" class="carousel slide" data-bs-ride="carousel">
                            <div class="carousel-indicators">
                                @foreach($product->images as $index => $image)
                                    <button type="button" data-bs-target="#productCarousel" 
                                            data-bs-slide-to="{{ $index }}" 
                                            class="{{ $index === 0 ? 'active' : '' }}"></button>
                                @endforeach
                            </div>
                            <div class="carousel-inner">
                                @foreach($product->images as $index => $image)
                                    <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                                        <img src="{{ Storage::url($image) }}" class="d-block w-100" 
                                             style="height: 400px; object-fit: cover;" alt="Product Image">
                                    </div>
                                @endforeach
                            </div>
                            @if(count($product->images) > 1)
                                <button class="carousel-control-prev" type="button" data-bs-target="#productCarousel" data-bs-slide="prev">
                                    <span class="carousel-control-prev-icon"></span>
                                </button>
                                <button class="carousel-control-next" type="button" data-bs-target="#productCarousel" data-bs-slide="next">
                                    <span class="carousel-control-next-icon"></span>
                                </button>
                            @endif
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-image fa-4x text-muted mb-3"></i>
                            <p class="text-muted">No images available</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Product Details -->
        <div class="col-lg-6">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Product Information</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Name:</strong></div>
                        <div class="col-sm-8">{{ $product->name }}</div>
                    </div>

                    @if($product->description)
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Description:</strong></div>
                        <div class="col-sm-8">{{ $product->description }}</div>
                    </div>
                    @endif

                    @if($product->sku)
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>SKU:</strong></div>
                        <div class="col-sm-8">{{ $product->sku }}</div>
                    </div>
                    @endif

                    @if($product->barcode)
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Barcode:</strong></div>
                        <div class="col-sm-8">{{ $product->barcode }}</div>
                    </div>
                    @endif

                    @if($product->category)
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Category:</strong></div>
                        <div class="col-sm-8">{{ $product->category }}</div>
                    </div>
                    @endif

                    @if($product->brand)
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Brand:</strong></div>
                        <div class="col-sm-8">{{ $product->brand }}</div>
                    </div>
                    @endif

                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Price:</strong></div>
                        <div class="col-sm-8">
                            <span class="h5 text-primary">{{ $product->formatted_price }}</span>
                        </div>
                    </div>

                    @if($product->cost_price)
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Cost Price:</strong></div>
                        <div class="col-sm-8">{{ $product->formatted_cost_price }}</div>
                    </div>
                    @endif

                    @if($product->profit_margin)
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Profit Margin:</strong></div>
                        <div class="col-sm-8">
                            <span class="badge bg-success">{{ $product->profit_margin_formatted }}</span>
                        </div>
                    </div>
                    @endif

                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Stock Quantity:</strong></div>
                        <div class="col-sm-8">
                            <span class="badge bg-{{ $product->stock_status_color }}">
                                {{ $product->stock_quantity }} units
                            </span>
                        </div>
                    </div>

                    @if($product->low_stock_threshold)
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Low Stock Threshold:</strong></div>
                        <div class="col-sm-8">{{ $product->low_stock_threshold }} units</div>
                    </div>
                    @endif

                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Status:</strong></div>
                        <div class="col-sm-8">
                            @if($product->is_active)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-secondary">Inactive</span>
                            @endif

                            @if($product->is_featured)
                                <span class="badge bg-warning ms-1">Featured</span>
                            @endif
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Created:</strong></div>
                        <div class="col-sm-8">{{ $product->created_at->format('M d, Y H:i') }}</div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Last Updated:</strong></div>
                        <div class="col-sm-8">{{ $product->updated_at->format('M d, Y H:i') }}</div>
                    </div>

                    <dt class="col-sm-3">Measurement Unit</dt>
                    <dd class="col-sm-9">{{ $product->measurement_unit ? ucfirst($product->measurement_unit) : 'Not specified' }}</dd>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <button class="btn btn-outline-primary w-100" onclick="adjustStock()">
                                <i class="fas fa-warehouse me-2"></i>Adjust Stock
                            </button>
                        </div>
                        <div class="col-md-3 mb-3">
                            <button class="btn btn-outline-success w-100" onclick="duplicateProduct()">
                                <i class="fas fa-copy me-2"></i>Duplicate Product
                            </button>
                        </div>
                        <div class="col-md-3 mb-3">
                            <button class="btn btn-outline-info w-100" onclick="viewHistory()">
                                <i class="fas fa-history me-2"></i>View History
                            </button>
                        </div>
                        <div class="col-md-3 mb-3">
                            <button class="btn btn-outline-danger w-100" onclick="deleteProduct()">
                                <i class="fas fa-trash me-2"></i>Delete Product
                            </button>
                        </div>
                    </div>
                </div>
            </div>
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
                    <label for="newStock" class="form-label">New Stock Quantity</label>
                    <input type="number" class="form-control" id="newStock" value="{{ $product->stock_quantity }}" min="0">
                </div>
                <div class="mb-3">
                    <label for="adjustmentReason" class="form-label">Reason for Adjustment</label>
                    <textarea class="form-control" id="adjustmentReason" rows="3" placeholder="e.g., Received new shipment, Damaged items, etc."></textarea>
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
function adjustStock() {
    const modal = new bootstrap.Modal(document.getElementById('stockModal'));
    modal.show();
}

function saveStockAdjustment() {
    const newStock = document.getElementById('newStock').value;
    const reason = document.getElementById('adjustmentReason').value;
    
    // Here you would make an AJAX call to update the stock
    fetch(`/products/${@json($product->id)}/stock`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            stock_quantity: newStock,
            adjustment_reason: reason
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error updating stock');
        }
    });
}

function duplicateProduct() {
    if (confirm('Create a copy of this product?')) {
        // Redirect to create form with pre-filled data
        window.location.href = `{{ route('products.create') }}?duplicate={{ $product->id }}`;
    }
}

function viewHistory() {
    alert('Product history feature coming soon!');
}

function deleteProduct() {
    if (confirm('Are you sure you want to delete this product? This action cannot be undone.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route('products.destroy', $product) }}';
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'DELETE';
        
        form.appendChild(csrfToken);
        form.appendChild(methodField);
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endsection 