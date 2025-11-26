@extends('layouts.dash')

@section('content')
<div class="container-fluid px-4">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Quick Add Product</h1>
            <p class="text-muted">Add a product quickly with essential details</p>
        </div>
        <div>
            <a href="{{ route('products.create') }}" class="btn btn-outline-primary me-2">
                <i class="fas fa-cog me-2"></i>Advanced Form
            </a>
            <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Products
            </a>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header py-3 bg-gradient-primary">
                    <h6 class="m-0 font-weight-bold text-white">
                        <i class="fas fa-plus-circle me-2"></i>Quick Product Entry
                    </h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('products.quick-store') }}" method="POST" enctype="multipart/form-data" id="quickProductForm">
                        @csrf
                        
                        <!-- Progress Indicator -->
                        <div class="alert alert-info mb-4">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Quick Setup:</strong> Fill in the essential details below. You can always add more details later by editing the product.
                        </div>

                        <div class="row">
                            <!-- Product Name -->
                            <div class="col-md-12 mb-3">
                                <label for="name" class="form-label required">
                                    <i class="fas fa-tag text-primary me-1"></i>Product Name
                                </label>
                                <input type="text" class="form-control form-control-lg @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name') }}" 
                                       placeholder="Enter product name (e.g., Samsung Galaxy A54)" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <!-- Cost Price -->
                            <div class="col-md-6 mb-3">
                                <label for="cost_price" class="form-label">
                                    <i class="fas fa-receipt text-warning me-1"></i>Cost Price (What you paid)
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">KSh</span>
                                    <input type="number" class="form-control @error('cost_price') is-invalid @enderror" 
                                           id="cost_price" name="cost_price" value="{{ old('cost_price') }}" 
                                           step="0.01" min="0" placeholder="0.00">
                                </div>
                                <small class="text-muted">Optional: Used to calculate profit margins</small>
                                @error('cost_price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Selling Price -->
                            <div class="col-md-6 mb-3">
                                <label for="price" class="form-label required">
                                    <i class="fas fa-money-bill text-success me-1"></i>Selling Price (Customer pays)
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">KSh</span>
                                    <input type="number" class="form-control @error('price') is-invalid @enderror" 
                                           id="price" name="price" value="{{ old('price') }}" 
                                           step="0.01" min="0" placeholder="0.00" required>
                                </div>
                                @error('price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <!-- Stock Quantity -->
                            <div class="col-md-6 mb-3">
                                <label for="stock_quantity" class="form-label required">
                                    <i class="fas fa-boxes text-info me-1"></i>Stock Quantity
                                </label>
                                <input type="number" class="form-control @error('stock_quantity') is-invalid @enderror" 
                                       id="stock_quantity" name="stock_quantity" value="{{ old('stock_quantity', 1) }}" 
                                       min="0" required placeholder="How many do you have?">
                                @error('stock_quantity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Category -->
                            <div class="col-md-6 mb-3">
                                <label for="category" class="form-label">
                                    <i class="fas fa-list text-secondary me-1"></i>Category
                                </label>
                                <select class="form-control @error('category') is-invalid @enderror" id="category" name="category">
                                    <option value="">Choose category (optional)</option>
                                    <option value="Electronics" {{ old('category') == 'Electronics' ? 'selected' : '' }}>Electronics</option>
                                    <option value="Clothing" {{ old('category') == 'Clothing' ? 'selected' : '' }}>Clothing</option>
                                    <option value="Food & Beverages" {{ old('category') == 'Food & Beverages' ? 'selected' : '' }}>Food & Beverages</option>
                                    <option value="Home & Garden" {{ old('category') == 'Home & Garden' ? 'selected' : '' }}>Home & Garden</option>
                                    <option value="Beauty & Health" {{ old('category') == 'Beauty & Health' ? 'selected' : '' }}>Beauty & Health</option>
                                    <option value="Sports & Fitness" {{ old('category') == 'Sports & Fitness' ? 'selected' : '' }}>Sports & Fitness</option>
                                    <option value="Books & Education" {{ old('category') == 'Books & Education' ? 'selected' : '' }}>Books & Education</option>
                                    <option value="Other" {{ old('category') == 'Other' ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('category')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Product Image -->
                        <div class="mb-3">
                            <label for="image" class="form-label">
                                <i class="fas fa-camera text-info me-1"></i>Product Image
                            </label>
                            <input type="file" class="form-control @error('image') is-invalid @enderror" 
                                   id="image" name="image" accept="image/*">
                            <small class="text-muted">Optional: Add a photo to help customers identify your product</small>
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Description (Optional) -->
                        <div class="mb-4">
                            <label for="description" class="form-label">
                                <i class="fas fa-align-left text-secondary me-1"></i>Description
                            </label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3" 
                                      placeholder="Brief description of your product (optional)">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Profit Preview -->
                        <div class="alert alert-light border mb-4" id="profitPreview" style="display: none;">
                            <h6 class="mb-2">
                                <i class="fas fa-chart-line text-success me-2"></i>Profit Analysis
                            </h6>
                            <div class="row">
                                <div class="col-6">
                                    <strong>Profit per unit:</strong> <span id="profitAmount" class="text-success">KSh 0</span>
                                </div>
                                <div class="col-6">
                                    <strong>Profit margin:</strong> <span id="profitMargin" class="text-info">0%</span>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <small class="text-muted">
                                    <i class="fas fa-clock me-1"></i>Takes less than 30 seconds
                                </small>
                            </div>
                            <div>
                                <button type="button" class="btn btn-outline-secondary me-2" onclick="history.back()">
                                    <i class="fas fa-times me-2"></i>Cancel
                                </button>
                                <button type="submit" class="btn btn-success btn-lg">
                                    <i class="fas fa-plus me-2"></i>Add Product
                                </button>
                            </div>
                        </div>

                        <!-- Help Text -->
                        <div class="mt-3 text-center">
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                Need more options? Use the <a href="{{ route('products.create') }}">Advanced Form</a> for detailed product setup
                            </small>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.required::after {
    content: " *";
    color: #dc3545;
    font-weight: bold;
}

.form-label {
    font-weight: 600;
    color: #5a5c69;
    margin-bottom: 0.5rem;
}

.form-control-lg {
    padding: 0.75rem 1rem;
    font-size: 1.1rem;
}

.input-group-text {
    background-color: #f8f9fc;
    border-color: #d1d3e2;
    color: #5a5c69;
    font-weight: 600;
}

.card {
    border: none;
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
}

.bg-gradient-primary {
    background: linear-gradient(135deg, #4e73df, #224abe);
}

.alert-light {
    background-color: #f8f9fc;
    border-color: #d1d3e2;
}

.btn-success {
    background: linear-gradient(135deg, #1cc88a, #17a673);
    border: none;
    font-weight: 600;
    padding: 0.7rem 1.5rem;
}

.btn-success:hover {
    background: linear-gradient(135deg, #17a673, #169b6b);
    transform: translateY(-1px);
    box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.1);
}

.text-success {
    color: #1cc88a !important;
}

.text-info {
    color: #36b9cc !important;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const costInput = document.getElementById('cost_price');
    const priceInput = document.getElementById('price');
    const profitPreview = document.getElementById('profitPreview');
    const profitAmount = document.getElementById('profitAmount');
    const profitMargin = document.getElementById('profitMargin');

    function calculateProfit() {
        const cost = parseFloat(costInput.value) || 0;
        const price = parseFloat(priceInput.value) || 0;
        
        if (price > 0) {
            const profit = price - cost;
            const margin = cost > 0 ? ((profit / cost) * 100) : 0;
            
            profitAmount.textContent = `KSh ${profit.toFixed(2)}`;
            profitMargin.textContent = `${margin.toFixed(1)}%`;
            
            // Show/hide profit preview
            if (cost > 0 || price > 0) {
                profitPreview.style.display = 'block';
            } else {
                profitPreview.style.display = 'none';
            }
            
            // Color coding for profit
            if (profit > 0) {
                profitAmount.className = 'text-success';
            } else if (profit < 0) {
                profitAmount.className = 'text-danger';
            } else {
                profitAmount.className = 'text-muted';
            }
        } else {
            profitPreview.style.display = 'none';
        }
    }

    // Auto-calculate profit when inputs change
    costInput.addEventListener('input', calculateProfit);
    priceInput.addEventListener('input', calculateProfit);

    // Form validation
    const form = document.getElementById('quickProductForm');
    form.addEventListener('submit', function(e) {
        const price = parseFloat(priceInput.value) || 0;
        const cost = parseFloat(costInput.value) || 0;
        
        if (cost > 0 && cost >= price) {
            e.preventDefault();
            alert('⚠️ Warning: Your cost price is equal to or higher than your selling price. You might not make a profit. Continue anyway?');
            // You could add a confirmation dialog here instead
        }
    });

    // Auto-focus on product name
    document.getElementById('name').focus();
});
</script>
@endsection
