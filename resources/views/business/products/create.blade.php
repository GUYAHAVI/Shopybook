@extends('layouts.dash')

@section('content')
<div class="container-fluid px-4">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Add New Product</h1>
            <p class="text-muted">Create a new product for your catalog</p>
        </div>
        <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Products
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Product Information</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data" id="productForm">
                        @csrf
                        
                        <div class="row">
                            <!-- Basic Information -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Product Name *</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name') }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="sku" class="form-label">SKU</label>
                                    <input type="text" class="form-control @error('sku') is-invalid @enderror" 
                                           id="sku" name="sku" value="{{ old('sku') }}">
                                    @error('sku')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="category" class="form-label">Category</label>
                                    <input type="text" class="form-control @error('category') is-invalid @enderror" 
                                           id="category" name="category" value="{{ old('category') }}" 
                                           list="categoryList">
                                    <datalist id="categoryList">
                                        <option value="Electronics">
                                        <option value="Clothing">
                                        <option value="Books">
                                        <option value="Home & Garden">
                                        <option value="Sports">
                                        <option value="Beauty">
                                        <option value="Food & Beverages">
                                    </datalist>
                                    @error('category')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="brand" class="form-label">Brand</label>
                                    <input type="text" class="form-control @error('brand') is-invalid @enderror" 
                                           id="brand" name="brand" value="{{ old('brand') }}" 
                                           list="brandList">
                                    <datalist id="brandList">
                                        <option value="Apple">
                                        <option value="Samsung">
                                        <option value="Nike">
                                        <option value="Adidas">
                                        <option value="Sony">
                                        <option value="LG">
                                    </datalist>
                                    @error('brand')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Pricing -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="price" class="form-label">Selling Price (KSh) *</label>
                                    <div class="input-group">
                                        <span class="input-group-text">KSh</span>
                                        <input type="number" class="form-control @error('price') is-invalid @enderror" 
                                               id="price" name="price" value="{{ old('price') }}" 
                                               step="0.01" min="0" required>
                                    </div>
                                    @error('price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="cost_price" class="form-label">Cost Price (KSh)</label>
                                    <div class="input-group">
                                        <span class="input-group-text">KSh</span>
                                        <input type="number" class="form-control @error('cost_price') is-invalid @enderror" 
                                               id="cost_price" name="cost_price" value="{{ old('cost_price') }}" 
                                               step="0.01" min="0">
                                    </div>
                                    @error('cost_price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Inventory -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="stock_quantity" class="form-label">Stock Quantity *</label>
                                    <input type="number" class="form-control @error('stock_quantity') is-invalid @enderror" 
                                           id="stock_quantity" name="stock_quantity" value="{{ old('stock_quantity', 0) }}" 
                                           min="0" required>
                                    @error('stock_quantity')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="low_stock_threshold" class="form-label">Low Stock Threshold</label>
                                    <input type="number" class="form-control @error('low_stock_threshold') is-invalid @enderror" 
                                           id="low_stock_threshold" name="low_stock_threshold" 
                                           value="{{ old('low_stock_threshold') }}" min="0">
                                    @error('low_stock_threshold')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="measurement_unit" class="form-label">Measurement Unit *</label>
                                <select class="form-control @error('measurement_unit') is-invalid @enderror" id="measurement_unit" name="measurement_unit" required>
                                    <option value="">Select Unit</option>
                                    <option value="piece" @if(old('measurement_unit')=='piece') selected @endif>Piece (pcs)</option>
                                    <option value="kg" @if(old('measurement_unit')=='kg') selected @endif>Kilogram (kg)</option>
                                    <option value="g" @if(old('measurement_unit')=='g') selected @endif>Gram (g)</option>
                                    <option value="lb" @if(old('measurement_unit')=='lb') selected @endif>Pound (lb)</option>
                                    <option value="l" @if(old('measurement_unit')=='l') selected @endif>Litre (l)</option>
                                    <option value="ml" @if(old('measurement_unit')=='ml') selected @endif>Millilitre (ml)</option>
                                    <option value="m" @if(old('measurement_unit')=='m') selected @endif>Metre (m)</option>
                                    <option value="cm" @if(old('measurement_unit')=='cm') selected @endif>Centimetre (cm)</option>
                                    <option value="mm" @if(old('measurement_unit')=='mm') selected @endif>Millimetre (mm)</option>
                                    <option value="ft" @if(old('measurement_unit')=='ft') selected @endif>Foot (ft)</option>
                                    <option value="in" @if(old('measurement_unit')=='in') selected @endif>Inch (in)</option>
                                    <option value="other" @if(old('measurement_unit')=='other') selected @endif>Other</option>
                                </select>
                                @error('measurement_unit')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Product Images -->
                        <div class="mb-3">
                            <label for="images" class="form-label">Product Images</label>
                            <input type="file" class="form-control @error('images.*') is-invalid @enderror" 
                                   id="images" name="images[]" multiple accept="image/*">
                            <div class="form-text">You can select multiple images. First image will be the main product image.</div>
                            @error('images.*')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Additional Information -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="barcode" class="form-label">Barcode</label>
                                    <input type="text" class="form-control @error('barcode') is-invalid @enderror" 
                                           id="barcode" name="barcode" value="{{ old('barcode') }}">
                                    @error('barcode')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="weight" class="form-label">Weight (kg)</label>
                                    <input type="number" class="form-control @error('weight') is-invalid @enderror" 
                                           id="weight" name="weight" value="{{ old('weight') }}" 
                                           step="0.01" min="0">
                                    @error('weight')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="dimensions" class="form-label">Dimensions</label>
                            <input type="text" class="form-control @error('dimensions') is-invalid @enderror" 
                                   id="dimensions" name="dimensions" value="{{ old('dimensions') }}" 
                                   placeholder="e.g., 10 x 5 x 2 cm">
                            @error('dimensions')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Status Options -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="is_active" 
                                               name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">
                                            Active Product
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="is_featured" 
                                               name="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_featured">
                                            Featured Product
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="d-flex justify-content-between">
                            <button type="button" class="btn btn-outline-secondary" onclick="history.back()">
                                <i class="fas fa-times me-2"></i>Cancel
                            </button>
                            <div>
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="fas fa-save me-2"></i>Save Product
                                </button>
                                <button type="submit" name="save_and_add" value="1" class="btn btn-success">
                                    <i class="fas fa-plus me-2"></i>Save & Add Another
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Quick Tips -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-lightbulb me-2"></i>Quick Tips
                    </h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            Use descriptive product names
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            Add high-quality product images
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            Set appropriate stock thresholds
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            Include detailed descriptions
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            Use SKUs for easy tracking
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Profit Calculator -->
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-calculator me-2"></i>Profit Calculator
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Selling Price</label>
                        <div class="input-group">
                            <span class="input-group-text">KSh</span>
                            <input type="number" class="form-control" id="calcPrice" step="0.01" min="0">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Cost Price</label>
                        <div class="input-group">
                            <span class="input-group-text">KSh</span>
                            <input type="number" class="form-control" id="calcCost" step="0.01" min="0">
                        </div>
                    </div>
                    <div class="alert alert-info">
                        <strong>Profit Margin: <span id="profitMargin">0%</span></strong><br>
                        <strong>Profit: KSh <span id="profitAmount">0.00</span></strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.form-label {
    font-weight: 600;
    color: #5a5c69;
}

.card {
    border: 1px solid #e3e6f0;
}

.card-header {
    background-color: #f8f9fc;
    border-bottom: 1px solid #e3e6f0;
}

.form-control:focus {
    border-color: #4e73df;
    box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
}

.btn {
    font-weight: 600;
}

.alert {
    border: none;
    border-radius: 0.35rem;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-calculate profit
    const priceInput = document.getElementById('price');
    const costInput = document.getElementById('cost_price');
    const calcPrice = document.getElementById('calcPrice');
    const calcCost = document.getElementById('calcCost');
    const profitMargin = document.getElementById('profitMargin');
    const profitAmount = document.getElementById('profitAmount');

    function calculateProfit() {
        const price = parseFloat(calcPrice.value) || 0;
        const cost = parseFloat(calcCost.value) || 0;
        
        if (cost > 0) {
            const profit = price - cost;
            const margin = (profit / cost) * 100;
            
            profitMargin.textContent = margin.toFixed(1) + '%';
            profitAmount.textContent = profit.toFixed(2);
        } else {
            profitMargin.textContent = '0%';
            profitAmount.textContent = '0.00';
        }
    }

    // Sync main form with calculator
    priceInput.addEventListener('input', function() {
        calcPrice.value = this.value;
        calculateProfit();
    });

    costInput.addEventListener('input', function() {
        calcCost.value = this.value;
        calculateProfit();
    });

    calcPrice.addEventListener('input', function() {
        priceInput.value = this.value;
        calculateProfit();
    });

    calcCost.addEventListener('input', function() {
        costInput.value = this.value;
        calculateProfit();
    });

    // Auto-generate SKU from product name
    const nameInput = document.getElementById('name');
    const skuInput = document.getElementById('sku');

    nameInput.addEventListener('input', function() {
        if (!skuInput.value) {
            const sku = this.value
                .toUpperCase()
                .replace(/[^A-Z0-9]/g, '')
                .substring(0, 8);
            skuInput.value = sku;
        }
    });

    // Form validation
    const form = document.getElementById('productForm');
    form.addEventListener('submit', function(e) {
        const price = parseFloat(priceInput.value) || 0;
        const cost = parseFloat(costInput.value) || 0;
        
        if (cost > price) {
            e.preventDefault();
            alert('Cost price cannot be higher than selling price!');
            return false;
        }
    });
});
</script>
@endsection 