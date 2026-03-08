@extends('layouts.master')
@section('title', $business->name . ' - Shopybook')
@section('meta_description', $business->description)
@section('content')

<style>
body, html {
    background: #020258 !important;
    color: #fff !important;
}

.hero-section,
.hero-section *,
.hero-section .container-fluid,
.hero-section .brandtext,
.hero-section .morphing-bubbles-hero,
.hero-section .morphing-bubble,
.hero-section .main-bubble,
.hero-section .secondary-bubble,
.hero-section .accent-bubble {
    background: #020258 !important;
    background-color: #020258 !important;
    color: #fff !important;
}

h1, h2, h3, h4, h5, h6, .display-4, .text-primary {
    color: #13e8e9 !important;
}

.lead, p, .card-text, .contact-info {
    color: #fff !important;
}

.btn-primary, .btn-primary:active, .btn-primary:focus {
    background: #13e8e9 !important;
    color: #020258 !important;
    border: 2px solid #13e8e9 !important;
}

.btn-primary:hover {
    background: #020258 !important;
    color: #13e8e9 !important;
    border: 2px solid #13e8e9 !important;
}

.card {
    background: #fff !important;
    color: #333 !important;
    border: 2px solid #13e8e9 !important;
}

.card-title {
    color: #13e8e9 !important;
}

.product-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    border: 2px solid #13e8e9 !important;
}

.product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(19, 232, 233, 0.3) !important;
}

.business-header {
    background: linear-gradient(135deg, #020258 0%, #13e8e9 100%);
    padding: 3rem 0;
    margin-bottom: 2rem;
}

.business-logo {
    width: 120px;
    height: 120px;
    object-fit: cover;
    border-radius: 50%;
    border: 4px solid #13e8e9;
}

.contact-info {
    background: rgba(255, 255, 255, 0.1);
    border-radius: 10px;
    padding: 1rem;
    margin: 1rem 0;
}

.order-form {
    background: rgba(255, 255, 255, 0.05);
    border-radius: 10px;
    padding: 1.5rem;
    border: 1px solid #13e8e9;
}

.form-control {
    background: #fff !important;
    color: #020258 !important;
    border: 2px solid #13e8e9 !important;
}

.form-control:focus {
    border-color: #13e8e9 !important;
    box-shadow: 0 0 0 0.2rem rgba(19, 232, 233, 0.25) !important;
}

/* Custom Modal Styles */
.modal-content {
    border-radius: 15px !important;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3) !important;
}

.modal-header {
    border-radius: 15px 15px 0 0 !important;
}

.modal-footer {
    border-radius: 0 0 15px 15px !important;
}

.btn-close-white {
    filter: invert(1) grayscale(100%) brightness(200%);
}

.spinner-border {
    width: 3rem;
    height: 3rem;
}
    border-color: #13e8e9 !important;
    box-shadow: 0 0 0 2px rgba(19, 232, 233, 0.2) !important;
}
</style>

<!-- Business Header -->
<div class="business-header">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-3 text-center">
                <img src="{{ $business->logo_path ? asset('storage/'.$business->logo_path) : asset('img/default-business.png') }}" 
                     alt="{{ $business->name }}" 
                     class="business-logo">
            </div>
            <div class="col-md-9">
                <h1 class="display-4">{{ $business->name }}</h1>
                <p class="lead">{{ $business->description }}</p>
                <div class="contact-info">
                    <div class="row">
                        <div class="col-md-6">
                            <p><i class="fas fa-map-marker-alt me-2"></i> {{ $business->address }}, {{ $business->city }}, {{ $business->country }}</p>
                            <p><i class="fas fa-phone me-2"></i> {{ $business->phone }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><i class="fas fa-envelope me-2"></i> {{ $business->email }}</p>
                            <p><i class="fas fa-tag me-2"></i> {{ ucfirst($business->business_type) }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Services & Products Section -->
<div class="container">
    @php
        $hasServices = $services->count() > 0;
        $hasProducts = $products->count() > 0;
        $isServiceOnly = $hasServices && !$hasProducts;
        $isProductOnly = $hasProducts && !$hasServices;
        $isHybrid = $hasServices && $hasProducts;
    @endphp

    @if($isServiceOnly)
        <!-- Services Only -->
        <div class="row">
            <div class="col-12">
                <h2 class="mb-4">Our Services</h2>
            </div>
        </div>
        
        <div class="row">
            @foreach($services as $service)
                <div class="col-md-4 mb-4">
                    <div class="card product-card h-100">
                        <div class="card-img-top d-flex align-items-center justify-content-center" 
                             style="height: 200px; background: linear-gradient(135deg, #020258, #13e8e9);">
                            <i class="fas fa-concierge-bell fa-3x text-white"></i>
                        </div>
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">{{ $service->name }}</h5>
                            <p class="card-text">{{ Str::limit($service->description, 100) }}</p>
                            <div class="mt-auto">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span class="h5 text-primary mb-0">KSh {{ number_format($service->price, 2) }}</span>
                                    @if($service->duration)
                                        <span class="badge bg-info">{{ $service->duration }} min</span>
                                    @endif
                                </div>
                                
                                <button class="btn btn-primary w-100" 
                                        onclick="openServiceModal('{{ $service->id }}', '{{ $service->name }}', '{{ $service->price }}')">
                                    <i class="fas fa-calendar-check me-2"></i>Book Service
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

    @elseif($isProductOnly)
        <!-- Products Only -->
        <div class="row">
            <div class="col-12">
                <h2 class="mb-4">Our Products</h2>
            </div>
        </div>
        
        <div class="row">
            @foreach($products as $product)
                <div class="col-md-4 mb-4">
                    <div class="card product-card h-100">
                        <img src="{{ $product->images && count($product->images) > 0 ? asset('storage/'.$product->images[0]) : asset('img/default-product.png') }}" 
                             class="card-img-top" 
                             alt="{{ $product->name }}"
                             style="height: 200px; object-fit: cover;">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">{{ $product->name }}</h5>
                            <p class="card-text">{{ Str::limit($product->description, 100) }}</p>
                            <div class="mt-auto">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span class="h5 text-primary mb-0">KSh {{ number_format($product->price, 2) }}</span>
                                    @if($product->stock_quantity > 0 && $product->is_active)
                                        <span class="badge bg-success">In Stock</span>
                                    @else
                                        <span class="badge bg-danger">Out of Stock</span>
                                    @endif
                                </div>
                                
                                @if($product->stock_quantity > 0 && $product->is_active)
                                    <button class="btn btn-primary w-100" 
                                            onclick="openOrderModal('{{ $product->id }}', '{{ $product->name }}', '{{ $product->price }}')">
                                        <i class="fas fa-shopping-cart me-2"></i>Order Now
                                    </button>
                                @else
                                    <button class="btn btn-secondary w-100" disabled>
                                        <i class="fas fa-times me-2"></i>{{ $product->stock_quantity <= 0 ? 'Out of Stock' : 'Not Available' }}
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

    @elseif($isHybrid)
        <!-- Both Services and Products -->
        @if($services->count() > 0)
            <div class="row">
                <div class="col-12">
                    <h2 class="mb-4">Our Services</h2>
                </div>
            </div>
            
            <div class="row">
                @foreach($services as $service)
                    <div class="col-md-4 mb-4">
                        <div class="card product-card h-100">
                            <div class="card-img-top d-flex align-items-center justify-content-center" 
                                 style="height: 200px; background: linear-gradient(135deg, #020258, #13e8e9);">
                                <i class="fas fa-concierge-bell fa-3x text-white"></i>
                            </div>
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title">{{ $service->name }}</h5>
                                <p class="card-text">{{ Str::limit($service->description, 100) }}</p>
                                <div class="mt-auto">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <span class="h5 text-primary mb-0">KSh {{ number_format($service->price, 2) }}</span>
                                        @if($service->duration)
                                            <span class="badge bg-info">{{ $service->duration }} min</span>
                                        @endif
                                    </div>
                                    
                                    <button class="btn btn-primary w-100" 
                                            onclick="openServiceModal('{{ $service->id }}', '{{ $service->name }}', '{{ $service->price }}')">
                                        <i class="fas fa-calendar-check me-2"></i>Book Service
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        @if($products->count() > 0)
            <div class="row mt-5">
                <div class="col-12">
                    <h2 class="mb-4">Our Products</h2>
                </div>
            </div>
            
            <div class="row">
                @foreach($products as $product)
                    <div class="col-md-4 mb-4">
                        <div class="card product-card h-100">
                            <img src="{{ $product->images && count($product->images) > 0 ? asset('storage/'.$product->images[0]) : asset('img/default-product.png') }}" 
                                 class="card-img-top" 
                                 alt="{{ $product->name }}"
                                 style="height: 200px; object-fit: cover;">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title">{{ $product->name }}</h5>
                                <p class="card-text">{{ Str::limit($product->description, 100) }}</p>
                                <div class="mt-auto">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <span class="h5 text-primary mb-0">KSh {{ number_format($product->price, 2) }}</span>
                                        @if($product->stock_quantity > 0 && $product->is_active)
                                            <span class="badge bg-success">In Stock</span>
                                        @else
                                            <span class="badge bg-danger">Out of Stock</span>
                                        @endif
                                    </div>
                                    
                                    @if($product->stock_quantity > 0 && $product->is_active)
                                        <button class="btn btn-primary w-100" 
                                                onclick="openOrderModal('{{ $product->id }}', '{{ $product->name }}', '{{ $product->price }}')">
                                            <i class="fas fa-shopping-cart me-2"></i>Order Now
                                        </button>
                                    @else
                                        <button class="btn btn-secondary w-100" disabled>
                                            <i class="fas fa-times me-2"></i>{{ $product->stock_quantity <= 0 ? 'Out of Stock' : 'Not Available' }}
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

    @else
        <!-- No Services or Products -->
        <div class="row">
            <div class="col-12">
                <div class="text-center py-5">
                    <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                    <h4>No Services or Products Available</h4>
                    <p class="text-muted">This business hasn't added any services or products yet.</p>
                </div>
            </div>
        </div>
    @endif
</div>

<!-- Order Modal -->
<div class="modal fade" id="orderModal" tabindex="-1" aria-labelledby="orderModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="orderModalLabel">Place Order</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="orderForm" action="{{ route('orders.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="business_id" value="{{ $business->id }}">
                    <input type="hidden" name="product_id" id="productId">
                    
                    <div class="mb-3">
                        <label for="productName" class="form-label">Product</label>
                        <input type="text" class="form-control" id="productName" readonly>
                    </div>
                    
                    <div class="mb-3">
                        <label for="quantity" class="form-label">Quantity</label>
                        <input type="number" class="form-control" id="quantity" name="quantity" min="1" value="1" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="customerName" class="form-label">Your Name</label>
                        <input type="text" class="form-control" name="customer_name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="customerPhone" class="form-label">Phone Number</label>
                        <input type="tel" class="form-control" name="customer_phone" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="customerEmail" class="form-label">Email (Optional)</label>
                        <input type="email" class="form-control" name="customer_email">
                    </div>
                    
                    <div class="mb-3">
                        <label for="deliveryAddress" class="form-label">Delivery Address</label>
                        <textarea class="form-control" name="delivery_address" rows="3" required></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="totalPrice" class="form-label">Total Price</label>
                        <input type="text" class="form-control" id="totalPrice" readonly>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="orderForm" class="btn btn-primary">Place Order</button>
            </div>
        </div>
    </div>
</div>

<!-- Service Booking Modal -->
<div class="modal fade" id="serviceModal" tabindex="-1" aria-labelledby="serviceModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="serviceModalLabel">Book Service</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="serviceForm" action="{{ route('service-bookings.store-public') }}" method="POST">
                    @csrf
                    <input type="hidden" name="business_id" value="{{ $business->id }}">
                    <input type="hidden" name="service_id" id="serviceId">
                    
                    <div class="mb-3">
                        <label for="serviceName" class="form-label">Service</label>
                        <input type="text" class="form-control" id="serviceName" readonly>
                    </div>
                    
                    <div class="mb-3">
                        <label for="servicePrice" class="form-label">Service Price</label>
                        <input type="text" class="form-control" id="servicePrice" readonly>
                    </div>
                    
                    <div class="mb-3">
                        <label for="bookingDate" class="form-label">Preferred Date</label>
                        <input type="date" class="form-control" name="booking_date" required min="{{ date('Y-m-d') }}">
                    </div>
                    
                    <div class="mb-3">
                        <label for="bookingTime" class="form-label">Preferred Time</label>
                        <input type="time" class="form-control" name="booking_time" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="customerNameService" class="form-label">Your Name</label>
                        <input type="text" class="form-control" name="customer_name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="customerPhoneService" class="form-label">Phone Number</label>
                        <input type="tel" class="form-control" name="customer_phone" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="customerEmailService" class="form-label">Email (Optional)</label>
                        <input type="email" class="form-control" name="customer_email">
                    </div>
                    
                    <div class="mb-3">
                        <label for="specialRequirements" class="form-label">Special Requirements (Optional)</label>
                        <textarea class="form-control" name="special_requirements" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="serviceForm" class="btn btn-primary">Book Service</button>
            </div>
        </div>
    </div>
</div>

<!-- Success Modal -->
<div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background: linear-gradient(135deg, #020258 0%, #13e8e9 100%); border: 2px solid #13e8e9;">
            <div class="modal-header border-0">
                <h5 class="modal-title text-white" id="successModalLabel">
                    <i class="fas fa-check-circle me-2"></i>Success!
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-white">
                <div class="text-center mb-3">
                    <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                    <h6 id="successMessage">Operation completed successfully!</h6>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Error Modal -->
<div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background: linear-gradient(135deg, #020258 0%, #dc3545 100%); border: 2px solid #dc3545;">
            <div class="modal-header border-0">
                <h5 class="modal-title text-white" id="errorModalLabel">
                    <i class="fas fa-exclamation-triangle me-2"></i>Error
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-white">
                <div class="text-center mb-3">
                    <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                    <h6 id="errorMessage">An error occurred. Please try again.</h6>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Loading Modal -->
<div class="modal fade" id="loadingModal" tabindex="-1" aria-labelledby="loadingModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content" style="background: linear-gradient(135deg, #020258 0%, #13e8e9 100%); border: 2px solid #13e8e9;">
            <div class="modal-body text-white text-center py-4">
                <div class="spinner-border text-light mb-3" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <h6 id="loadingMessage">Processing your request...</h6>
            </div>
        </div>
    </div>
</div>

<!-- Success Modal -->
<div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background: linear-gradient(135deg, #020258 0%, #13e8e9 100%); border: 2px solid #13e8e9;">
            <div class="modal-header border-0">
                <h5 class="modal-title text-white" id="successModalLabel">
                    <i class="fas fa-check-circle me-2"></i>Success!
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-white">
                <div class="text-center mb-3">
                    <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                    <h6 id="successMessage">Operation completed successfully!</h6>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Error Modal -->
<div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background: linear-gradient(135deg, #020258 0%, #dc3545 100%); border: 2px solid #dc3545;">
            <div class="modal-header border-0">
                <h5 class="modal-title text-white" id="errorModalLabel">
                    <i class="fas fa-exclamation-triangle me-2"></i>Error
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-white">
                <div class="text-center mb-3">
                    <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                    <h6 id="errorMessage">An error occurred. Please try again.</h6>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Loading Modal -->
<div class="modal fade" id="loadingModal" tabindex="-1" aria-labelledby="loadingModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content" style="background: linear-gradient(135deg, #020258 0%, #13e8e9 100%); border: 2px solid #13e8e9;">
            <div class="modal-body text-white text-center py-4">
                <div class="spinner-border text-light mb-3" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <h6 id="loadingMessage">Processing your request...</h6>
            </div>
        </div>
    </div>
</div>

<script>
let currentPrice = 0;

function openOrderModal(productId, productName, price) {
    currentPrice = parseFloat(price);
    document.getElementById('productId').value = productId;
    document.getElementById('productName').value = productName;
    document.getElementById('totalPrice').value = 'KSh ' + currentPrice.toFixed(2);
    
    const modal = new bootstrap.Modal(document.getElementById('orderModal'));
    modal.show();
}

function openServiceModal(serviceId, serviceName, price) {
    document.getElementById('serviceId').value = serviceId;
    document.getElementById('serviceName').value = serviceName;
    document.getElementById('servicePrice').value = 'KSh ' + parseFloat(price).toFixed(2);
    
    const modal = new bootstrap.Modal(document.getElementById('serviceModal'));
    modal.show();
}

function showSuccessModal(message) {
    document.getElementById('successMessage').textContent = message;
    const modal = new bootstrap.Modal(document.getElementById('successModal'));
    modal.show();
}

function showErrorModal(message) {
    document.getElementById('errorMessage').textContent = message;
    const modal = new bootstrap.Modal(document.getElementById('errorModal'));
    modal.show();
}

function showLoadingModal(message = 'Processing your request...') {
    document.getElementById('loadingMessage').textContent = message;
    const modal = new bootstrap.Modal(document.getElementById('loadingModal'));
    modal.show();
}

function hideLoadingModal() {
    const modal = bootstrap.Modal.getInstance(document.getElementById('loadingModal'));
    if (modal) {
        modal.hide();
    }
}

document.getElementById('quantity').addEventListener('input', function() {
    const quantity = parseInt(this.value) || 0;
    const total = currentPrice * quantity;
    document.getElementById('totalPrice').value = 'KSh ' + total.toFixed(2);
});

document.getElementById('orderForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    showLoadingModal('Placing your order...');
    
    const formData = new FormData(this);
    
    fetch(this.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok: ' + response.statusText);
        }
        return response.json();
    })
    .then(data => {
        hideLoadingModal();
        if (data.success) {
            showSuccessModal('Order placed successfully! The business will contact you soon.');
            bootstrap.Modal.getInstance(document.getElementById('orderModal')).hide();
            this.reset();
        } else {
            showErrorModal('Error placing order: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        hideLoadingModal();
        console.error('Error:', error);
        showErrorModal('Error placing order: ' + error.message + '. Please try again.');
    });
});

document.getElementById('serviceForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    showLoadingModal('Booking your service...');
    
    const formData = new FormData(this);
    
    fetch(this.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok: ' + response.statusText);
        }
        return response.json();
    })
    .then(data => {
        hideLoadingModal();
        if (data.success) {
                                 showSuccessModal('Service booked successfully! The business will assign staff and contact you to confirm your appointment.');
            bootstrap.Modal.getInstance(document.getElementById('serviceModal')).hide();
            this.reset();
        } else {
            showErrorModal('Error booking service: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        hideLoadingModal();
        console.error('Error:', error);
        showErrorModal('Error booking service: ' + error.message + '. Please try again.');
    });
});
</script>

@endsection 