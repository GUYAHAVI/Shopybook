@extends('layouts.dash')

@section('content')
<style>
body, .container-fluid, .card, .main-content, .content {
    background: #fff !important;
    color: #020258 !important;
}
.btn-primary {
    background: #020258 !important;
    color: #fff !important;
    border: 2px solid #13e8e9 !important;
}
.btn-primary:hover {
    background: #13e8e9 !important;
    color: #020258 !important;
    border: 2px solid #020258 !important;
}
.btn-success {
    background: #28a745 !important;
    color: #fff !important;
    border: 2px solid #28a745 !important;
}
.btn-success:hover {
    background: #218838 !important;
    color: #fff !important;
}
.form-control {
    background: #f8f9fa !important;
    color: #020258 !important;
    border: 2px solid #13e8e9 !important;
}
.form-control:focus {
    border-color: #020258 !important;
    box-shadow: 0 0 0 3px rgba(19, 232, 233, 0.1) !important;
}
.card-header {
    background: #f8f9fa !important;
    color: #020258 !important;
    border-bottom: 1px solid #13e8e9 !important;
}
.cart-item {
    border-bottom: 1px solid #eee;
    padding: 10px 0;
}
.cart-item:last-child {
    border-bottom: none;
}
.quantity-control {
    display: flex;
    align-items: center;
    gap: 10px;
}
.quantity-btn {
    width: 30px;
    height: 30px;
    border: 1px solid #ddd;
    background: #fff;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
}
.quantity-btn:hover {
    background: #f8f9fa;
}
.product-card {
    cursor: pointer;
    transition: all 0.3s ease;
}
.product-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}
.product-image-container {
    position: relative;
    height: 150px;
    overflow: hidden;
}
.product-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.product-image-placeholder {
    width: 100%;
    height: 100%;
    background: #f8f9fa;
}
.loading-spinner {
    display: none;
    width: 20px;
    height: 20px;
    border: 2px solid #f3f3f3;
    border-top: 2px solid #020258;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}
@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>

<div class="container-fluid px-4">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Point of Sale</h1>
            <p class="text-muted">Process sales and manage transactions</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-secondary" onclick="clearCart()">
                <i class="fas fa-trash me-2"></i>Clear Cart
            </button>
            <a href="{{ route('sales.orders') }}" class="btn btn-outline-primary">
                <i class="fas fa-list me-2"></i>View Orders
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Products Grid -->
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">Products</h6>
                        <div class="d-flex gap-2">
                            <input type="text" class="form-control form-control-sm" id="productSearch" 
                                   placeholder="Search products..." style="width: 200px;">
                            <select class="form-select form-select-sm" id="categoryFilter" style="width: 150px;">
                                <option value="">All Categories</option>
                                @foreach($products->pluck('category')->unique()->filter() as $category)
                                    <option value="{{ $category }}">{{ $category }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row" id="productsGrid">
                        @foreach($products as $product)
                        <div class="col-md-4 col-lg-3 mb-3 product-item" 
                             data-name="{{ strtolower($product->name) }}" 
                             data-category="{{ strtolower($product->category ?? '') }}">
                            <div class="card h-100 product-card" 
                                 data-id="{{ $product->id }}"
                                 data-name="{{ $product->name }}"
                                 data-price="{{ $product->price }}"
                                 data-stock="{{ $product->stock_quantity }}"
                                 onclick="addToCart({{ $product->id }}, '{{ str_replace("'", "\\'", $product->name) }}', {{ $product->price }}, {{ $product->stock_quantity }})">
                                <div class="product-image-container">
                                    @if($product->main_image)
                                        <img src="{{ Storage::url($product->main_image) }}" 
                                             class="card-img-top product-image" alt="{{ $product->name }}">
                                    @else
                                        <div class="card-img-top product-image-placeholder d-flex align-items-center justify-content-center">
                                            <i class="fas fa-image fa-2x text-muted"></i>
                                        </div>
                                    @endif
                                    <div class="position-absolute top-0 end-0 m-2">
                                        <span class="badge bg-{{ $product->stock_quantity > 0 ? 'success' : 'danger' }}">
                                            {{ $product->stock_quantity > 0 ? 'In Stock' : 'Out of Stock' }}
                                        </span>
                                    </div>
                                </div>
                                <div class="card-body d-flex flex-column">
                                    <h6 class="card-title text-truncate">{{ $product->name }}</h6>
                                    <p class="card-text text-primary fw-bold mb-1">KSh {{ number_format($product->price, 2) }}</p>
                                    <small class="text-muted">Stock: {{ $product->stock_quantity }}</small>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Cart -->
        <div class="col-lg-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-shopping-cart me-2"></i>Shopping Cart
                    </h6>
                </div>
                <div class="card-body">
                    <div id="cartItems">
                        <div class="text-center py-4">
                            <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Your cart is empty</p>
                        </div>
                    </div>
                    
                    <div id="cartSummary" style="display: none;">
                        <hr>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal:</span>
                            <span id="subtotal">KSh 0.00</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Tax (16%):</span>
                            <span id="tax">KSh 0.00</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-3">
                            <strong>Total:</strong>
                            <strong id="total">KSh 0.00</strong>
                        </div>
                        
                        <!-- Customer Selection -->
                        <div class="mb-3">
                            <label for="customerSelect" class="form-label">Customer</label>
                            <div class="d-flex gap-2">
                                <select class="form-select" id="customerSelect">
                                    <option value="">Walk-in Customer</option>
                                    @if(isset($customers))
                                        @foreach($customers as $customer)
                                            <option value="{{ $customer->id }}">{{ $customer->name }} ({{ $customer->phone }})</option>
                                        @endforeach
                                    @endif
                                </select>
                                <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addCustomerModal">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Payment Method -->
                        <div class="mb-3">
                            <label for="paymentMethod" class="form-label">Payment Method</label>
                            <select class="form-select" id="paymentMethod">
                                <option value="cash">Cash</option>
                                <option value="card">Card</option>
                                <option value="mobile_money">Mobile Money</option>
                                <option value="bank_transfer">Bank Transfer</option>
                            </select>
                        </div>
                        
                        <!-- Notes -->
                        <div class="mb-3">
                            <label for="orderNotes" class="form-label">Notes</label>
                            <textarea class="form-control" id="orderNotes" rows="2" placeholder="Any special instructions..."></textarea>
                        </div>
                        
                        <button class="btn btn-success w-100" id="checkoutBtn">
                            <span class="loading-spinner me-2"></span>
                            <i class="fas fa-check me-2"></i>Complete Sale
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Customer Modal -->
<div class="modal fade" id="addCustomerModal" tabindex="-1" aria-labelledby="addCustomerModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addCustomerModalLabel">Add New Customer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addCustomerForm">
                    <div class="mb-3">
                        <label for="customerName" class="form-label">Name *</label>
                        <input type="text" class="form-control" id="customerName" required>
                    </div>
                    <div class="mb-3">
                        <label for="customerPhone" class="form-label">Phone *</label>
                        <input type="text" class="form-control" id="customerPhone" required>
                    </div>
                    <div class="mb-3">
                        <label for="customerEmail" class="form-label">Email</label>
                        <input type="email" class="form-control" id="customerEmail">
                    </div>
                    <div class="mb-3">
                        <label for="customerAddress" class="form-label">Address</label>
                        <textarea class="form-control" id="customerAddress" rows="2"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveCustomer()">Add Customer</button>
            </div>
        </div>
    </div>
</div>
<script>
    window.posConfig = {
        routes: {
            createOrder: "{{ route('sales.create-order') }}",
            storeCustomer: "{{ route('sales.store-customer') }}",
            orders: "{{ route('sales.orders') }}"
        },
        csrfToken: "{{ csrf_token() }}",
        businessName: "{{ auth()->user()->business->name ?? 'Business Name' }}",
        currencySymbol: "KSh "
    };
    </script>


<script src="{{ asset('js/pos.js') }}"></script>

@endsection