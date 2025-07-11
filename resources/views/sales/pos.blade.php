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

.payment-method-card {
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.payment-method-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    border-color: #007bff;
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
                            <div class="card h-100 product-card" onclick="addToCart({{ $product->id }}, '{{ $product->name }}', {{ $product->price }}, {{ $product->stock_quantity }})">
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
                                    <p class="card-text text-primary fw-bold mb-1">{{ $product->formatted_price }}</p>
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
                            <select class="form-select" id="customerSelect">
                                <option value="">Walk-in Customer</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}">{{ $customer->name }} ({{ $customer->phone }})</option>
                                @endforeach
                            </select>
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
                        
                        <button class="btn btn-success w-100" onclick="checkout()">
                            <i class="fas fa-check me-2"></i>Complete Sale
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Add Customer Modal -->
<div class="modal fade" id="addCustomerModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Customer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
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

<!-- Payment Method Modal -->
<div class="modal fade" id="paymentMethodModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Select Payment Method</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="card payment-method-card" onclick="selectPaymentMethod('mpesa')" style="cursor: pointer;">
                            <div class="card-body text-center">
                                <i class="fas fa-mobile-alt fa-3x text-success mb-2"></i>
                                <h6>M-Pesa</h6>
                                <small class="text-muted">Mobile Money</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="card payment-method-card" onclick="selectPaymentMethod('paypal')" style="cursor: pointer;">
                            <div class="card-body text-center">
                                <i class="fab fa-paypal fa-3x text-primary mb-2"></i>
                                <h6>PayPal</h6>
                                <small class="text-muted">Online Payment</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="card payment-method-card" onclick="selectPaymentMethod('cash')" style="cursor: pointer;">
                            <div class="card-body text-center">
                                <i class="fas fa-money-bill-wave fa-3x text-success mb-2"></i>
                                <h6>Cash</h6>
                                <small class="text-muted">Cash Payment</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="card payment-method-card" onclick="selectPaymentMethod('card')" style="cursor: pointer;">
                            <div class="card-body text-center">
                                <i class="fas fa-credit-card fa-3x text-info mb-2"></i>
                                <h6>Card</h6>
                                <small class="text-muted">Credit/Debit Card</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let cart = [];
let cartTotal = 0;

// Product search and filter
document.getElementById('productSearch').addEventListener('input', function() {
    filterProducts();
});

document.getElementById('categoryFilter').addEventListener('change', function() {
    filterProducts();
});

function filterProducts() {
    const searchTerm = document.getElementById('productSearch').value.toLowerCase();
    const categoryFilter = document.getElementById('categoryFilter').value.toLowerCase();
    
    document.querySelectorAll('.product-item').forEach(item => {
        const name = item.dataset.name;
        const category = item.dataset.category;
        
        const matchesSearch = name.includes(searchTerm);
        const matchesCategory = !categoryFilter || category.includes(categoryFilter);
        
        item.style.display = matchesSearch && matchesCategory ? 'block' : 'none';
    });
}

function addToCart(productId, name, price, stock) {
    if (stock <= 0) {
        alert('This product is out of stock!');
        return;
    }
    
    const existingItem = cart.find(item => item.id === productId);
    
    if (existingItem) {
        if (existingItem.quantity >= stock) {
            alert('Cannot add more items. Stock limit reached!');
            return;
        }
        existingItem.quantity++;
    } else {
        cart.push({
            id: productId,
            name: name,
            price: price,
            quantity: 1,
            stock: stock
        });
    }
    
    updateCartDisplay();
}

function updateCartDisplay() {
    const cartContainer = document.getElementById('cartItems');
    const cartSummary = document.getElementById('cartSummary');
    
    if (cart.length === 0) {
        cartContainer.innerHTML = `
            <div class="text-center py-4">
                <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                <p class="text-muted">Your cart is empty</p>
            </div>
        `;
        cartSummary.style.display = 'none';
        return;
    }
    
    cartSummary.style.display = 'block';
    
    let cartHtml = '';
    cartTotal = 0;
    
    cart.forEach((item, index) => {
        const itemTotal = item.price * item.quantity;
        cartTotal += itemTotal;
        
        cartHtml += `
            <div class="cart-item">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="flex-grow-1">
                        <h6 class="mb-1">${item.name}</h6>
                        <small class="text-muted">KSh ${item.price.toFixed(2)} × ${item.quantity}</small>
                    </div>
                    <div class="text-end">
                        <div class="fw-bold">KSh ${itemTotal.toFixed(2)}</div>
                        <button class="btn btn-sm btn-outline-danger" onclick="removeFromCart(${index})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
                <div class="quantity-control mt-2">
                    <button class="quantity-btn" onclick="updateQuantity(${index}, -1)">-</button>
                    <span class="fw-bold">${item.quantity}</span>
                    <button class="quantity-btn" onclick="updateQuantity(${index}, 1)">+</button>
                </div>
            </div>
        `;
    });
    
    cartContainer.innerHTML = cartHtml;
    
    const subtotal = cartTotal;
    const tax = subtotal * 0.16;
    const total = subtotal + tax;
    
    document.getElementById('subtotal').textContent = `KSh ${subtotal.toFixed(2)}`;
    document.getElementById('tax').textContent = `KSh ${tax.toFixed(2)}`;
    document.getElementById('total').textContent = `KSh ${total.toFixed(2)}`;
}

function updateQuantity(index, change) {
    const item = cart[index];
    const newQuantity = item.quantity + change;
    
    if (newQuantity <= 0) {
        removeFromCart(index);
        return;
    }
    
    if (newQuantity > item.stock) {
        alert('Cannot add more items. Stock limit reached!');
        return;
    }
    
    item.quantity = newQuantity;
    updateCartDisplay();
}

function removeFromCart(index) {
    cart.splice(index, 1);
    updateCartDisplay();
}

function clearCart() {
    if (confirm('Are you sure you want to clear the cart?')) {
        cart = [];
        updateCartDisplay();
    }
}

function checkout() {
    if (cart.length === 0) {
        alert('Cart is empty!');
        return;
    }
    
    const customerId = document.getElementById('customerSelect').value;
    const paymentMethod = document.getElementById('paymentMethod').value;
    const notes = document.getElementById('orderNotes').value;
    
    // Show payment method selection modal
    $('#paymentMethodModal').modal('show');
    
    // Store order data for later use
    window.pendingOrderData = {
        customer_id: customerId || null,
        items: cart.map(item => ({
            product_id: item.id,
            quantity: item.quantity
        })),
        payment_method: paymentMethod,
        notes: notes
    };
}

function selectPaymentMethod(method) {
    $('#paymentMethodModal').modal('hide');
    
    if (method === 'cash' || method === 'card') {
        // For cash/card, create order directly
        createOrder(method);
    } else {
        // For M-Pesa/PayPal, create order first then process payment
        createOrderWithPayment(method);
    }
}

function createOrder(paymentMethod) {
    const orderData = {
        ...window.pendingOrderData,
        payment_method: paymentMethod
    };
    
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
            alert(`Order created successfully! Order #: ${data.order_number}`);
            cart = [];
            updateCartDisplay();
            document.getElementById('orderNotes').value = '';
        } else {
            alert('Error creating order: ' + data.message);
        }
    })
    .catch(error => {
        alert('Error creating order');
    });
}

function createOrderWithPayment(paymentMethod) {
    const orderData = {
        ...window.pendingOrderData,
        payment_method: paymentMethod
    };
    
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
            // Process payment
            processPayment(data.order_id, paymentMethod);
        } else {
            alert('Error creating order: ' + data.message);
        }
    })
    .catch(error => {
        alert('Error creating order');
    });
}

function processPayment(orderId, paymentMethod) {
    fetch('{{ route("payment.checkout") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            order_id: orderId,
            payment_method: paymentMethod
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            if (paymentMethod === 'mpesa') {
                alert(`M-Pesa payment initiated!\n\n${data.instructions}\n\nCheckout Request ID: ${data.checkout_request_id}`);
            } else if (paymentMethod === 'paypal') {
                if (data.approval_url) {
                    window.open(data.approval_url, '_blank');
                }
                alert('PayPal payment initiated! Please complete the payment in the new window.');
            }
            
            cart = [];
            updateCartDisplay();
            document.getElementById('orderNotes').value = '';
        } else {
            alert('Payment error: ' + data.error);
        }
    })
    .catch(error => {
        alert('Payment processing error');
    });
}

function saveCustomer() {
    const formData = {
        name: document.getElementById('customerName').value,
        phone: document.getElementById('customerPhone').value,
        email: document.getElementById('customerEmail').value,
        address: document.getElementById('customerAddress').value
    };
    
    fetch('{{ route("sales.store-customer") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(formData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Add new customer to select dropdown
            const select = document.getElementById('customerSelect');
            const option = new Option(`${data.customer.name} (${data.customer.phone})`, data.customer.id);
            select.add(option);
            select.value = data.customer.id;
            
            // Close modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('addCustomerModal'));
            modal.hide();
            
            // Clear form
            document.getElementById('addCustomerForm').reset();
        } else {
            alert('Error adding customer');
        }
    });
}
</script>

@endsection 