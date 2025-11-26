@extends('layouts.dash')

@section('content')
<div class="container-fluid px-4">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Receive Products</h1>
            <p class="text-muted">Record incoming stock for existing or new products</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('products.receive.history') }}" class="btn btn-outline-primary">
                <i class="fas fa-history me-2"></i>Receipt History
            </a>
            <a href="{{ route('products.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Products
            </a>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Error!</strong>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-10 mx-auto">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Product Receipt Form</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('products.receive.process') }}" method="POST">
                        @csrf

                        <!-- Receipt Type Selection -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">Receipt Type <span class="text-danger">*</span></label>
                            <div class="btn-group w-100" role="group">
                                <input type="radio" class="btn-check" name="receipt_type" id="existing_product" value="existing_product" checked>
                                <label class="btn btn-outline-primary" for="existing_product">
                                    <i class="fas fa-box me-2"></i>Existing Product
                                </label>

                                <input type="radio" class="btn-check" name="receipt_type" id="new_product" value="new_product">
                                <label class="btn btn-outline-success" for="new_product">
                                    <i class="fas fa-plus-circle me-2"></i>New Product
                                </label>
                            </div>
                        </div>

                        <!-- Common Fields -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="receipt_date" class="form-label">Receipt Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="receipt_date" name="receipt_date" value="{{ old('receipt_date', date('Y-m-d')) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label for="supplier" class="form-label">Supplier</label>
                                <input type="text" class="form-control" id="supplier" name="supplier" value="{{ old('supplier') }}" placeholder="Enter supplier name">
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="invoice_number" class="form-label">Invoice/PO Number</label>
                                <input type="text" class="form-control" id="invoice_number" name="invoice_number" value="{{ old('invoice_number') }}" placeholder="Enter invoice or PO number">
                            </div>
                            <div class="col-md-6">
                                <label for="notes" class="form-label">Notes</label>
                                <input type="text" class="form-control" id="notes" name="notes" value="{{ old('notes') }}" placeholder="Optional notes">
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- Existing Product Form -->
                        <div id="existing-product-form">
                            <h5 class="mb-3"><i class="fas fa-box text-primary me-2"></i>Existing Product Details</h5>
                            
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <label for="product_id" class="form-label">Select Product <span class="text-danger">*</span></label>
                                    <select class="form-select" id="product_id" name="product_id">
                                        <option value="">-- Select a product --</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}" 
                                                    data-cost="{{ $product->cost_price }}"
                                                    data-stock="{{ $product->stock_quantity }}"
                                                    {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                                {{ $product->name }} 
                                                @if($product->sku) (SKU: {{ $product->sku }}) @endif
                                                - Current Stock: {{ $product->stock_quantity }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="quantity_received" class="form-label">Quantity Received <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="quantity_received" name="quantity_received" value="{{ old('quantity_received') }}" min="1" placeholder="Enter quantity">
                                </div>
                                <div class="col-md-6">
                                    <label for="unit_cost" class="form-label">Unit Cost (KSh)</label>
                                    <input type="number" class="form-control" id="unit_cost" name="unit_cost" value="{{ old('unit_cost') }}" min="0" step="0.01" placeholder="Cost per unit">
                                    <small class="text-muted">Leave blank to use existing cost price</small>
                                </div>
                            </div>

                            <div class="alert alert-info" id="existing-product-info" style="display: none;">
                                <strong>Selected Product Info:</strong>
                                <ul class="mb-0 mt-2">
                                    <li>Current Stock: <span id="current-stock">-</span> units</li>
                                    <li>Current Cost Price: KSh <span id="current-cost">-</span></li>
                                    <li>New Stock (after receipt): <span id="new-stock">-</span> units</li>
                                </ul>
                            </div>
                        </div>

                        <!-- New Product Form -->
                        <div id="new-product-form" style="display: none;">
                            <h5 class="mb-3"><i class="fas fa-plus-circle text-success me-2"></i>New Product Details</h5>
                            
                            <div class="row mb-3">
                                <div class="col-md-8">
                                    <label for="new_product_name" class="form-label">Product Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="new_product_name" name="new_product_name" value="{{ old('new_product_name') }}" placeholder="Enter product name">
                                </div>
                                <div class="col-md-4">
                                    <label for="category" class="form-label">Category</label>
                                    <input type="text" class="form-control" id="category" name="category" value="{{ old('category') }}" placeholder="e.g., Electronics">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="sku" class="form-label">SKU</label>
                                    <input type="text" class="form-control" id="sku" name="sku" value="{{ old('sku') }}" placeholder="Stock Keeping Unit">
                                </div>
                                <div class="col-md-6">
                                    <label for="barcode" class="form-label">Barcode</label>
                                    <input type="text" class="form-control" id="barcode" name="barcode" value="{{ old('barcode') }}" placeholder="Product barcode">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="brand" class="form-label">Brand</label>
                                    <input type="text" class="form-control" id="brand" name="brand" value="{{ old('brand') }}" placeholder="Product brand">
                                </div>
                                <div class="col-md-6">
                                    <label for="low_stock_threshold" class="form-label">Low Stock Threshold</label>
                                    <input type="number" class="form-control" id="low_stock_threshold" name="low_stock_threshold" value="{{ old('low_stock_threshold', 10) }}" min="0" placeholder="Minimum stock level">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="2" placeholder="Product description">{{ old('description') }}</textarea>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label for="new_quantity" class="form-label">Initial Quantity <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="new_quantity" name="new_quantity" value="{{ old('new_quantity') }}" min="1" placeholder="Quantity received">
                                </div>
                                <div class="col-md-4">
                                    <label for="new_unit_cost" class="form-label">Unit Cost (KSh) <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="new_unit_cost" name="new_unit_cost" value="{{ old('new_unit_cost') }}" min="0" step="0.01" placeholder="Cost per unit">
                                </div>
                                <div class="col-md-4">
                                    <label for="selling_price" class="form-label">Selling Price (KSh) <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="selling_price" name="selling_price" value="{{ old('selling_price') }}" min="0" step="0.01" placeholder="Retail price">
                                </div>
                            </div>

                            <div class="alert alert-warning">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Note:</strong> This will create a new product in your inventory with the specified details.
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- Submit Buttons -->
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('products.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-check me-2"></i>Record Receipt
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const existingProductRadio = document.getElementById('existing_product');
    const newProductRadio = document.getElementById('new_product');
    const existingProductForm = document.getElementById('existing-product-form');
    const newProductForm = document.getElementById('new-product-form');
    const productSelect = document.getElementById('product_id');
    const quantityInput = document.getElementById('quantity_received');
    const existingProductInfo = document.getElementById('existing-product-info');

    // Toggle forms based on receipt type
    existingProductRadio.addEventListener('change', function() {
        if (this.checked) {
            existingProductForm.style.display = 'block';
            newProductForm.style.display = 'none';
            // Make existing product fields required
            document.getElementById('product_id').required = true;
            document.getElementById('quantity_received').required = true;
            // Make new product fields not required
            document.getElementById('new_product_name').required = false;
            document.getElementById('new_quantity').required = false;
            document.getElementById('new_unit_cost').required = false;
            document.getElementById('selling_price').required = false;
        }
    });

    newProductRadio.addEventListener('change', function() {
        if (this.checked) {
            existingProductForm.style.display = 'none';
            newProductForm.style.display = 'block';
            // Make existing product fields not required
            document.getElementById('product_id').required = false;
            document.getElementById('quantity_received').required = false;
            // Make new product fields required
            document.getElementById('new_product_name').required = true;
            document.getElementById('new_quantity').required = true;
            document.getElementById('new_unit_cost').required = true;
            document.getElementById('selling_price').required = true;
        }
    });

    // Update info when product is selected
    productSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        if (selectedOption.value) {
            const currentStock = selectedOption.dataset.stock;
            const currentCost = selectedOption.dataset.cost || '0.00';
            
            document.getElementById('current-stock').textContent = currentStock;
            document.getElementById('current-cost').textContent = parseFloat(currentCost).toFixed(2);
            document.getElementById('unit_cost').value = currentCost;
            
            existingProductInfo.style.display = 'block';
            updateNewStock();
        } else {
            existingProductInfo.style.display = 'none';
        }
    });

    // Update new stock calculation
    quantityInput.addEventListener('input', updateNewStock);

    function updateNewStock() {
        const selectedOption = productSelect.options[productSelect.selectedIndex];
        if (selectedOption.value && quantityInput.value) {
            const currentStock = parseInt(selectedOption.dataset.stock) || 0;
            const quantityReceived = parseInt(quantityInput.value) || 0;
            const newStock = currentStock + quantityReceived;
            document.getElementById('new-stock').textContent = newStock;
        }
    }
});
</script>
@endsection



