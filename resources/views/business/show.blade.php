@extends('layouts.public')
@section('title', $business->name . ' - Shopybook')
@section('meta_description', $business->description)
@section('content')

@php
    $hasServices = $services->count() > 0;
    $hasProducts = $products->count() > 0;
    $isServiceOnly = $hasServices && !$hasProducts;
    $isProductOnly = $hasProducts && !$hasServices;
    $isHybrid = $hasServices && $hasProducts;
@endphp

{{-- ═══════════════ BUSINESS HEADER ═══════════════ --}}
<section class="hero-section text-light">
    <div class="container">
        <div class="row align-items-center g-4">
            <div class="col-md-3 text-center">
                <img src="{{ $business->logo_path ? asset('storage/'.$business->logo_path) : asset('img/default-business.png') }}"
                     alt="{{ $business->name }}"
                     class="sb-business-logo">
            </div>
            <div class="col-md-9">
                <h1>{{ $business->name }}</h1>
                <p class="mb-3">{{ $business->description }}</p>
                <div class="sb-contact-info">
                    <div class="row">
                        <div class="col-md-6">
                            <p class="mb-1"><i class="fas fa-map-marker-alt me-2"></i> {{ $business->address }}, {{ $business->city }}, {{ $business->country }}</p>
                            <p class="mb-1"><i class="fas fa-phone me-2"></i> {{ $business->phone }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1"><i class="fas fa-envelope me-2"></i> {{ $business->email }}</p>
                            <p class="mb-1"><i class="fas fa-tag me-2"></i> {{ ucfirst($business->business_type) }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ═══════════════ SERVICES & PRODUCTS ═══════════════ --}}
<section class="sb-section sb-section-gray">
    <div class="container">

        @if($isServiceOnly)
            <h2 class="sb-heading mb-4">Our Services</h2>
            <div class="row g-4">
                @foreach($services as $service)
                    <div class="col-md-4">
                        <div class="sb-product-card">
                            <div class="sb-product-img sb-product-img-icon">
                                <i class="fas fa-concierge-bell fa-3x"></i>
                            </div>
                            <div class="sb-product-body">
                                <h5>{{ $service->name }}</h5>
                                <p>{{ Str::limit($service->description, 100) }}</p>
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span class="sb-price">KSh {{ number_format($service->price, 2) }}</span>
                                    @if($service->duration)
                                        <span class="sb-badge">{{ $service->duration }} min</span>
                                    @endif
                                </div>
                                <button class="btn1 btn-block-j" onclick="openServiceModal('{{ $service->id }}', '{{ $service->name }}', '{{ $service->price }}')">
                                    <i class="fas fa-calendar-check me-2"></i>Book Service
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

        @elseif($isProductOnly)
            <h2 class="sb-heading mb-4">Our Products</h2>
            <div class="row g-4">
                @foreach($products as $product)
                    <div class="col-md-4">
                        <div class="sb-product-card">
                            <img src="{{ $product->images && count($product->images) > 0 ? asset('storage/'.$product->images[0]) : asset('img/default-product.png') }}"
                                 class="sb-product-img"
                                 alt="{{ $product->name }}">
                            <div class="sb-product-body">
                                <h5>{{ $product->name }}</h5>
                                <p>{{ Str::limit($product->description, 100) }}</p>
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span class="sb-price">KSh {{ number_format($product->price, 2) }}</span>
                                    @if($product->stock_quantity > 0 && $product->is_active)
                                        <span class="sb-badge sb-badge-success">In Stock</span>
                                    @else
                                        <span class="sb-badge sb-badge-danger">Out of Stock</span>
                                    @endif
                                </div>
                                @if($product->stock_quantity > 0 && $product->is_active)
                                    <button class="btn1 btn-block-j" onclick="openOrderModal('{{ $product->id }}', '{{ $product->name }}', '{{ $product->price }}')">
                                        <i class="fas fa-shopping-cart me-2"></i>Order Now
                                    </button>
                                @else
                                    <button class="btnb btn-block-j" disabled style="opacity:.6; cursor:not-allowed;">
                                        <i class="fas fa-times me-2"></i>{{ $product->stock_quantity <= 0 ? 'Out of Stock' : 'Not Available' }}
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

        @elseif($isHybrid)
            @if($services->count() > 0)
                <h2 class="sb-heading mb-4">Our Services</h2>
                <div class="row g-4">
                    @foreach($services as $service)
                        <div class="col-md-4">
                            <div class="sb-product-card">
                                <div class="sb-product-img sb-product-img-icon">
                                    <i class="fas fa-concierge-bell fa-3x"></i>
                                </div>
                                <div class="sb-product-body">
                                    <h5>{{ $service->name }}</h5>
                                    <p>{{ Str::limit($service->description, 100) }}</p>
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <span class="sb-price">KSh {{ number_format($service->price, 2) }}</span>
                                        @if($service->duration)
                                            <span class="sb-badge">{{ $service->duration }} min</span>
                                        @endif
                                    </div>
                                    <button class="btn1 btn-block-j" onclick="openServiceModal('{{ $service->id }}', '{{ $service->name }}', '{{ $service->price }}')">
                                        <i class="fas fa-calendar-check me-2"></i>Book Service
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            @if($products->count() > 0)
                <h2 class="sb-heading mb-4 mt-5">Our Products</h2>
                <div class="row g-4">
                    @foreach($products as $product)
                        <div class="col-md-4">
                            <div class="sb-product-card">
                                <img src="{{ $product->images && count($product->images) > 0 ? asset('storage/'.$product->images[0]) : asset('img/default-product.png') }}"
                                     class="sb-product-img"
                                     alt="{{ $product->name }}">
                                <div class="sb-product-body">
                                    <h5>{{ $product->name }}</h5>
                                    <p>{{ Str::limit($product->description, 100) }}</p>
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <span class="sb-price">KSh {{ number_format($product->price, 2) }}</span>
                                        @if($product->stock_quantity > 0 && $product->is_active)
                                            <span class="sb-badge sb-badge-success">In Stock</span>
                                        @else
                                            <span class="sb-badge sb-badge-danger">Out of Stock</span>
                                        @endif
                                    </div>
                                    @if($product->stock_quantity > 0 && $product->is_active)
                                        <button class="btn1 btn-block-j" onclick="openOrderModal('{{ $product->id }}', '{{ $product->name }}', '{{ $product->price }}')">
                                            <i class="fas fa-shopping-cart me-2"></i>Order Now
                                        </button>
                                    @else
                                        <button class="btnb btn-block-j" disabled style="opacity:.6; cursor:not-allowed;">
                                            <i class="fas fa-times me-2"></i>{{ $product->stock_quantity <= 0 ? 'Out of Stock' : 'Not Available' }}
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

        @else
            <div class="sb-empty">
                <i class="fas fa-box-open fa-3x mb-3" style="color:#ff511a;"></i>
                <h4>No Services or Products Available</h4>
                <p>This business hasn't added any services or products yet.</p>
            </div>
        @endif
    </div>
</section>

{{-- ═══════════════ ORDER MODAL ═══════════════ --}}
<div class="modal fade" id="orderModal" tabindex="-1" aria-labelledby="orderModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content sb-modal-content">
            <div class="modal-header sb-modal-header">
                <h5 class="modal-title" id="orderModalLabel">Place Order</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
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
            <div class="modal-footer sb-modal-footer">
                <button type="button" class="btnb" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="orderForm" class="btn1">Place Order</button>
            </div>
        </div>
    </div>
</div>

{{-- ═══════════════ SERVICE BOOKING MODAL ═══════════════ --}}
<div class="modal fade" id="serviceModal" tabindex="-1" aria-labelledby="serviceModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content sb-modal-content">
            <div class="modal-header sb-modal-header">
                <h5 class="modal-title" id="serviceModalLabel">Book Service</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
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
            <div class="modal-footer sb-modal-footer">
                <button type="button" class="btnb" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="serviceForm" class="btn1">Book Service</button>
            </div>
        </div>
    </div>
</div>

{{-- ═══════════════ SUCCESS MODAL ═══════════════ --}}
<div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content sb-modal-content">
            <div class="modal-header sb-modal-header">
                <h5 class="modal-title"><i class="fas fa-check-circle me-2"></i>Success!</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <i class="fas fa-check-circle fa-3x mb-3" style="color:#43ba7f;"></i>
                <h6 id="successMessage">Operation completed successfully!</h6>
            </div>
            <div class="modal-footer sb-modal-footer">
                <button type="button" class="btnb" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

{{-- ═══════════════ ERROR MODAL ═══════════════ --}}
<div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content sb-modal-content">
            <div class="modal-header sb-modal-header" style="background:#7b2e2e;">
                <h5 class="modal-title"><i class="fas fa-exclamation-triangle me-2"></i>Error</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <i class="fas fa-exclamation-triangle fa-3x mb-3" style="color:#dc3545;"></i>
                <h6 id="errorMessage">An error occurred. Please try again.</h6>
            </div>
            <div class="modal-footer sb-modal-footer">
                <button type="button" class="btnb" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

{{-- ═══════════════ LOADING MODAL ═══════════════ --}}
<div class="modal fade" id="loadingModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content sb-modal-content">
            <div class="modal-body text-center py-4">
                <div class="spinner-border mb-3" style="color:#ff511a;" role="status">
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
    new bootstrap.Modal(document.getElementById('orderModal')).show();
}

function openServiceModal(serviceId, serviceName, price) {
    document.getElementById('serviceId').value = serviceId;
    document.getElementById('serviceName').value = serviceName;
    document.getElementById('servicePrice').value = 'KSh ' + parseFloat(price).toFixed(2);
    new bootstrap.Modal(document.getElementById('serviceModal')).show();
}

function showSuccessModal(message) {
    document.getElementById('successMessage').textContent = message;
    new bootstrap.Modal(document.getElementById('successModal')).show();
}

function showErrorModal(message) {
    document.getElementById('errorMessage').textContent = message;
    new bootstrap.Modal(document.getElementById('errorModal')).show();
}

function showLoadingModal(message = 'Processing your request...') {
    document.getElementById('loadingMessage').textContent = message;
    new bootstrap.Modal(document.getElementById('loadingModal')).show();
}

function hideLoadingModal() {
    const m = bootstrap.Modal.getInstance(document.getElementById('loadingModal'));
    if (m) m.hide();
}

document.getElementById('quantity').addEventListener('input', function() {
    const quantity = parseInt(this.value) || 0;
    document.getElementById('totalPrice').value = 'KSh ' + (currentPrice * quantity).toFixed(2);
});

document.getElementById('orderForm').addEventListener('submit', function(e) {
    e.preventDefault();
    showLoadingModal('Placing your order...');
    fetch(this.action, {
        method: 'POST',
        body: new FormData(this),
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'), 'Accept': 'application/json' }
    })
    .then(r => { if (!r.ok) throw new Error('Network response was not ok: ' + r.statusText); return r.json(); })
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
        showErrorModal('Error placing order: ' + error.message + '. Please try again.');
    });
});

document.getElementById('serviceForm').addEventListener('submit', function(e) {
    e.preventDefault();
    showLoadingModal('Booking your service...');
    fetch(this.action, {
        method: 'POST',
        body: new FormData(this),
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'), 'Accept': 'application/json' }
    })
    .then(r => { if (!r.ok) throw new Error('Network response was not ok: ' + r.statusText); return r.json(); })
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
        showErrorModal('Error booking service: ' + error.message + '. Please try again.');
    });
});
</script>

@endsection
