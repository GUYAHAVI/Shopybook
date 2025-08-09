@extends('layouts.dash')

@section('content')
<div class="container-fluid">
    <!-- Sub-navigation for Sales -->
    <div class="sub-navigation mb-4">
        <div class="nav-tabs">
            <a href="{{ route('sales.customers') }}" class="nav-tab">
                <i class="fas fa-users me-1"></i> Customers
            </a>
            <a href="{{ route('sales.orders') }}" class="nav-tab">
                <i class="fas fa-shopping-cart me-1"></i> Orders
            </a>
            <a href="{{ route('sales.pos') }}" class="nav-tab active">
                <i class="fas fa-cash-register me-1"></i> POS
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card" style="background: var(--card-bg); border: 1px solid var(--border-color);">
                <div class="card-header" style="background: var(--bg-tertiary); border-bottom: 1px solid var(--border-color);">
                    <h5 class="mb-0" style="color: var(--text-primary);">Point of Sale</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="customer_search" class="form-label" style="color: var(--text-primary);">Customer</label>
                                <select class="form-control" id="customer_search" style="border: 1px solid var(--border-color); background: var(--card-bg); color: var(--text-primary);">
                                    <option value="">Walk-in Customer</option>
                                    @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="payment_method" class="form-label" style="color: var(--text-primary);">Payment Method</label>
                                <select class="form-control" id="payment_method" style="border: 1px solid var(--border-color); background: var(--card-bg); color: var(--text-primary);">
                                    <option value="cash">Cash</option>
                                    <option value="mpesa">M-Pesa</option>
                                    <option value="card">Card</option>
                                    <option value="bank_transfer">Bank Transfer</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="product_search" class="form-label" style="color: var(--text-primary);">Search Products</label>
                                <input type="text" class="form-control" id="product_search" placeholder="Search products by name, price, or stock..."
                                       style="border: 1px solid var(--border-color); background: var(--card-bg); color: var(--text-primary);">
                                <small class="text-muted">Found {{ $products->count() }} products</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row" id="products_grid">
                        @if($products->count() > 0)
                            @foreach($products as $product)
                                <div class="col-md-3 mb-3">
                                <div class="card product-card" data-product-id="{{ $product->id }}" 
                                     style="background: var(--card-bg); border: 1px solid var(--border-color); cursor: pointer;">
                                    <div class="product-image-container">
                                        @if($product->image)
                                            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="product-image">
                                        @else
                                            <div class="product-image-placeholder d-flex align-items-center justify-content-center" style="background: var(--bg-tertiary);">
                                                <i class="fas fa-image fa-2x" style="color: var(--text-muted);"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="card-body text-center">
                                        <h6 class="card-title" style="color: var(--text-primary);">{{ $product->name }}</h6>
                                        <p class="card-text" style="color: var(--text-secondary);">KSh {{ number_format($product->price, 2) }}</p>
                                        <small style="color: var(--text-muted);">Stock: {{ $product->stock_quantity }}</small>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        @else
                            <div class="col-12">
                                <div class="text-center py-4">
                                    <i class="fas fa-box-open fa-3x mb-3" style="color: var(--text-muted);"></i>
                                    <h5 style="color: var(--text-primary);">No Products Found</h5>
                                    <p style="color: var(--text-muted);">No active products with stock are available for sale.</p>
                                    <a href="{{ route('products.index') }}" class="btn btn-primary">
                                        <i class="fas fa-plus me-2"></i>Add Products
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card" style="background: var(--card-bg); border: 1px solid var(--border-color);">
                <div class="card-header" style="background: var(--bg-tertiary); border-bottom: 1px solid var(--border-color);">
                    <h5 class="mb-0" style="color: var(--text-primary);">Shopping Cart</h5>
                </div>
                <div class="card-body">
                    <div id="cart_items">
                        <p style="color: var(--text-muted); text-align: center;">No items in cart</p>
                    </div>
                    
                    <hr style="border-color: var(--border-color);">
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span style="color: var(--text-primary);">Subtotal:</span>
                        <span id="subtotal" style="color: var(--text-primary);">KSh 0.00</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span style="color: var(--text-primary);">Tax (16%):</span>
                        <span id="tax" style="color: var(--text-primary);">KSh 0.00</span>
                    </div>
                    <hr style="border-color: var(--border-color);">
                    <div class="d-flex justify-content-between mb-3">
                        <strong style="color: var(--text-primary);">Total:</strong>
                        <strong id="total" style="color: var(--text-primary);">KSh 0.00</strong>
                    </div>
                    
                    <button class="btn btn-success w-100 mb-2" id="checkout_btn" disabled>
                        <i class="fas fa-check me-2"></i>Complete Sale
                    </button>
                    
                    <button class="btn btn-outline-secondary w-100" id="clear_cart_btn">
                        <i class="fas fa-trash me-2"></i>Clear Cart
                    </button>
                 </div>
             </div>
         </div>
     </div>
 </div>
 
 <!-- Success Modal -->
 <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
     <div class="modal-dialog modal-dialog-centered">
         <div class="modal-content" style="background: var(--card-bg); border: 1px solid var(--border-color);">
             <div class="modal-header" style="background: var(--bg-tertiary); border-bottom: 1px solid var(--border-color);">
                 <h5 class="modal-title" id="successModalLabel" style="color: var(--text-primary);">
                     <i class="fas fa-check-circle text-success me-2"></i>Sale Completed!
                 </h5>
                 <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
             </div>
             <div class="modal-body" style="color: var(--text-primary);">
                 <div class="text-center">
                     <i class="fas fa-check-circle text-success fa-3x mb-3"></i>
                     <h6>Sale completed successfully!</h6>
                     <p class="mb-0">Order Number: <strong id="orderNumber"></strong></p>
                     <small class="text-muted">Receipt has been generated and is ready for printing.</small>
                 </div>
             </div>
             <div class="modal-footer" style="border-top: 1px solid var(--border-color);">
                 <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                 <button type="button" class="btn btn-primary" id="printReceiptBtn">
                     <i class="fas fa-print me-2"></i>Print Receipt
                 </button>
                 <a href="{{ route('sales.orders') }}" class="btn btn-outline-primary">
                     <i class="fas fa-list me-2"></i>View Orders
                 </a>
             </div>
         </div>
     </div>
 </div>
 
 <!-- Error Modal -->
 <div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
     <div class="modal-dialog modal-dialog-centered">
         <div class="modal-content" style="background: var(--card-bg); border: 1px solid var(--border-color);">
             <div class="modal-header" style="background: var(--bg-tertiary); border-bottom: 1px solid var(--border-color);">
                 <h5 class="modal-title" id="errorModalLabel" style="color: var(--text-primary);">
                     <i class="fas fa-exclamation-triangle text-warning me-2"></i>Error
                 </h5>
                 <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
             </div>
             <div class="modal-body" style="color: var(--text-primary);">
                 <div class="text-center">
                     <i class="fas fa-exclamation-triangle text-warning fa-3x mb-3"></i>
                     <h6>Order processing failed!</h6>
                     <p class="mb-0" id="errorMessage"></p>
                 </div>
             </div>
             <div class="modal-footer" style="border-top: 1px solid var(--border-color);">
                 <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
             </div>
         </div>
     </div>
 </div>

<style>
.sub-navigation {
    background: var(--card-bg);
    border: 1px solid var(--border-color);
    border-radius: 0.5rem;
    padding: 1rem;
    margin-bottom: 1.5rem;
}

.nav-tabs {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.nav-tab {
    display: flex;
    align-items: center;
    padding: 0.5rem 1rem;
    color: var(--text-muted);
    text-decoration: none;
    border-radius: 0.375rem;
    font-size: 0.875rem;
    font-weight: 500;
    transition: all 0.2s ease;
    border: 1px solid transparent;
}

.nav-tab:hover {
    color: var(--text-primary);
    background: var(--bg-tertiary);
    border-color: var(--border-color);
}

.nav-tab.active {
    color: var(--white);
    background: var(--primary-color);
    border-color: var(--primary-color);
}

.form-control:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 0.2rem rgba(19, 232, 233, 0.25);
}

.product-card {
    transition: all 0.3s ease;
    background: var(--card-bg);
    border: 1px solid var(--border-color);
    cursor: pointer;
    position: relative;
}

.product-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px var(--shadow-color);
    border-color: var(--primary-color);
}

.product-card:active {
    transform: translateY(0px);
    box-shadow: 0 2px 4px var(--shadow-color);
}

.cart-item {
    border-bottom: 1px solid var(--border-color);
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
    border: 1px solid var(--border-color);
    background: var(--card-bg);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    color: var(--text-primary);
}

.quantity-btn:hover {
    background: var(--bg-tertiary);
}

.loading-spinner {
    display: none;
    width: 20px;
    height: 20px;
    border: 2px solid var(--border-color);
    border-top: 2px solid var(--primary-color);
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Mobile responsiveness */
@media (max-width: 768px) {
    .nav-tabs {
        flex-direction: column;
        gap: 0.25rem;
    }
    
    .nav-tab {
        justify-content: center;
        padding: 0.75rem 1rem;
    }
    
    .col-md-3 {
        flex: 0 0 50%;
        max-width: 50%;
    }
}
</style>

<script>
// Cart functionality
let cart = [];
let products = @json($products);

// Debug logging
console.log('Products loaded:', products);
console.log('Number of products:', products.length);

// Test if products are properly loaded
if (products && products.length > 0) {
    console.log('First product:', products[0]);
} else {
    console.error('No products loaded!');
}

document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, setting up event listeners');
    
    // Product search
    const searchInput = document.getElementById('product_search');
    if (searchInput) {
        searchInput.addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const productCards = document.querySelectorAll('.product-card');
            
            console.log('Searching for:', searchTerm);
            console.log('Found product cards:', productCards.length);
            
            productCards.forEach(card => {
                const productName = card.querySelector('.card-title').textContent.toLowerCase();
                const productPrice = card.querySelector('.card-text').textContent.toLowerCase();
                const productStock = card.querySelector('small').textContent.toLowerCase();
                
                // Search in name, price, and stock info
                if (productName.includes(searchTerm) || 
                    productPrice.includes(searchTerm) || 
                    productStock.includes(searchTerm)) {
                    card.style.display = 'block';
                    card.parentElement.style.display = 'block';
                } else {
                    card.style.display = 'none';
                    card.parentElement.style.display = 'none';
                }
            });
        });
    } else {
        console.error('Search input not found!');
    }
    
    // Add to cart - try multiple approaches
    const productCards = document.querySelectorAll('.product-card');
    console.log('Setting up click events for', productCards.length, 'product cards');
    
    if (productCards.length === 0) {
        console.error('No product cards found!');
        // Try alternative selector
        const altCards = document.querySelectorAll('[data-product-id]');
        console.log('Alternative cards found:', altCards.length);
    }
    
    productCards.forEach((card, index) => {
        const productId = card.dataset.productId;
        console.log(`Setting up click for card ${index}:`, productId);
        
        // Remove any existing listeners
        card.removeEventListener('click', card.clickHandler);
        
        // Create new click handler
        card.clickHandler = function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            console.log('Product clicked:', productId);
            console.log('Card element:', this);
            
            // Add visual feedback
            this.style.transform = 'scale(0.95)';
            setTimeout(() => {
                this.style.transform = '';
            }, 150);
            
            addToCart(productId);
        };
        
        // Add click listener
        card.addEventListener('click', card.clickHandler);
        
        // Also add mousedown for better responsiveness
        card.addEventListener('mousedown', function(e) {
            e.preventDefault();
            this.style.transform = 'scale(0.98)';
        });
        
        card.addEventListener('mouseup', function(e) {
            e.preventDefault();
            this.style.transform = '';
        });
    });
    
    // Clear cart
    document.getElementById('clear_cart_btn').addEventListener('click', function() {
        cart = [];
        updateCartDisplay();
    });
    
    // Checkout
    document.getElementById('checkout_btn').addEventListener('click', function() {
        if (cart.length > 0) {
            processCheckout();
        }
    });
});

function addToCart(productId) {
    console.log('addToCart called with productId:', productId);
    console.log('Available products:', products);
    
    if (!products || products.length === 0) {
        console.error('No products available!');
        document.getElementById('errorMessage').textContent = 'No products available to add to cart.';
        const errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
        errorModal.show();
        return;
    }
    
    const product = products.find(p => p.id == productId);
    console.log('Found product:', product);
    
    if (!product) {
        console.log('Product not found!');
        document.getElementById('errorMessage').textContent = 'Product not found. Please try again.';
        const errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
        errorModal.show();
        return;
    }
    
    const existingItem = cart.find(item => item.id == productId);
    if (existingItem) {
        existingItem.quantity++;
        console.log('Updated existing item quantity to:', existingItem.quantity);
    } else {
        cart.push({
            id: product.id,
            name: product.name,
            price: parseFloat(product.price) || 0,
            quantity: 1
        });
        console.log('Added new item to cart:', product.name);
    }
    
    console.log('Current cart:', cart);
    updateCartDisplay();
}

// Test function to manually add a product
function testAddToCart() {
    console.log('Testing addToCart function...');
    if (products && products.length > 0) {
        const firstProduct = products[0];
        console.log('Adding first product to cart:', firstProduct);
        addToCart(firstProduct.id);
    } else {
        console.error('No products available for testing');
        document.getElementById('errorMessage').textContent = 'No products available for testing';
        const errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
        errorModal.show();
    }
}

function updateCartDisplay() {
    const cartContainer = document.getElementById('cart_items');
    const subtotalElement = document.getElementById('subtotal');
    const taxElement = document.getElementById('tax');
    const totalElement = document.getElementById('total');
    const checkoutBtn = document.getElementById('checkout_btn');
    
    if (cart.length === 0) {
        cartContainer.innerHTML = '<p style="color: var(--text-muted); text-align: center;">No items in cart</p>';
        subtotalElement.textContent = 'KSh 0.00';
        taxElement.textContent = 'KSh 0.00';
        totalElement.textContent = 'KSh 0.00';
        checkoutBtn.disabled = true;
        return;
    }
    
    let cartHTML = '';
    let subtotal = 0;
    
    cart.forEach(item => {
        // Convert price to number to ensure .toFixed() works
        const price = parseFloat(item.price) || 0;
        const itemTotal = price * item.quantity;
        subtotal += itemTotal;
        
        cartHTML += `
            <div class="cart-item">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 style="color: var(--text-primary); margin-bottom: 0;">${item.name}</h6>
                        <small style="color: var(--text-muted);">KSh ${price.toFixed(2)} x ${item.quantity}</small>
                    </div>
                    <div class="quantity-control">
                        <button class="quantity-btn" onclick="updateQuantity(${item.id}, -1)">-</button>
                        <input type="number" class="quantity-input" value="${item.quantity}" min="1" 
                               onchange="updateQuantityDirect(${item.id}, this.value)" 
                               style="width: 50px; text-align: center; border: 1px solid var(--border-color); border-radius: 3px; padding: 2px;">
                        <button class="quantity-btn" onclick="updateQuantity(${item.id}, 1)">+</button>
                    </div>
                </div>
            </div>
        `;
    });
    
    cartContainer.innerHTML = cartHTML;
    
    const tax = subtotal * 0.16;
    const total = subtotal + tax;
    
    subtotalElement.textContent = `KSh ${subtotal.toFixed(2)}`;
    taxElement.textContent = `KSh ${tax.toFixed(2)}`;
    totalElement.textContent = `KSh ${total.toFixed(2)}`;
    checkoutBtn.disabled = false;
}

function updateQuantity(productId, change) {
    const item = cart.find(item => item.id == productId);
    if (item) {
        const newQuantity = item.quantity + change;
        if (newQuantity <= 0) {
            cart = cart.filter(item => item.id != productId);
        } else {
            item.quantity = newQuantity;
        }
        updateCartDisplay();
    }
}

function updateQuantityDirect(productId, newQuantity) {
    const item = cart.find(item => item.id == productId);
    if (item) {
        const quantity = parseInt(newQuantity);
        if (quantity <= 0) {
            cart = cart.filter(item => item.id != productId);
        } else {
            item.quantity = quantity;
        }
        updateCartDisplay();
    }
}

function processCheckout() {
    const customerId = document.getElementById('customer_search').value;
    const paymentMethod = document.getElementById('payment_method').value;
    
    if (cart.length === 0) {
        document.getElementById('errorMessage').textContent = 'Please add items to cart before checkout';
        const errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
        errorModal.show();
        return;
    }
    
    // Show loading
    const checkoutBtn = document.getElementById('checkout_btn');
    const originalText = checkoutBtn.innerHTML;
    checkoutBtn.innerHTML = '<span class="loading-spinner"></span> Processing...';
    checkoutBtn.disabled = true;
    
    // Prepare order data
    const orderData = {
        customer_id: customerId || null,
        payment_method: paymentMethod,
        items: cart.map(item => ({
            product_id: item.id,
            quantity: item.quantity,
            price: item.price
        })),
        subtotal: parseFloat(document.getElementById('subtotal').textContent.replace('KSh ', '')),
        tax: parseFloat(document.getElementById('tax').textContent.replace('KSh ', '')),
        total: parseFloat(document.getElementById('total').textContent.replace('KSh ', '')),
        notes: ''
    };
    
    // Send order to server
    fetch('{{ route("sales.create-order") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(orderData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Store order data for receipt printing
            window.lastOrderData = data;
            // Show success modal
            document.getElementById('orderNumber').textContent = data.order_number;
            const successModal = new bootstrap.Modal(document.getElementById('successModal'));
            successModal.show();
            cart = [];
            updateCartDisplay();
        } else {
            // Show error modal
            document.getElementById('errorMessage').textContent = data.message || 'An error occurred while processing the order.';
            const errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
            errorModal.show();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        // Show error modal
        document.getElementById('errorMessage').textContent = 'Error processing order. Please try again.';
        const errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
        errorModal.show();
    })
    .finally(() => {
        checkoutBtn.innerHTML = originalText;
        checkoutBtn.disabled = true;
    });
}

// Handle receipt printing
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('printReceiptBtn').addEventListener('click', function() {
        if (window.lastOrderData && window.lastOrderData.order_id) {
            // Open receipt in new window for printing
            const receiptUrl = `/sales/orders/${window.lastOrderData.order_id}/receipt`;
            window.open(receiptUrl, '_blank', 'width=400,height=600');
        } else {
            console.error('No order data available for receipt printing');
        }
    });
});
</script>
@endsection