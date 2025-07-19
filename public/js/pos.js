// POS System JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Initialize cart and variables
    let cart = [];
    let cartTotal = 0;
    let isProcessing = false;
    const config = window.posConfig || {};
    
    // DOM Elements
    const elements = {
        productSearch: document.getElementById('productSearch'),
        categoryFilter: document.getElementById('categoryFilter'),
        productsGrid: document.getElementById('productsGrid'),
        cartItems: document.getElementById('cartItems'),
        cartSummary: document.getElementById('cartSummary'),
        subtotal: document.getElementById('subtotal'),
        tax: document.getElementById('tax'),
        total: document.getElementById('total'),
        customerSelect: document.getElementById('customerSelect'),
        paymentMethod: document.getElementById('paymentMethod'),
        orderNotes: document.getElementById('orderNotes'),
        checkoutBtn: document.getElementById('checkoutBtn'),
        customerName: document.getElementById('customerName'),
        customerPhone: document.getElementById('customerPhone'),
        customerEmail: document.getElementById('customerEmail'),
        customerAddress: document.getElementById('customerAddress'),
        addCustomerForm: document.getElementById('addCustomerForm')
    };

    // Initialize event listeners
    function initEventListeners() {
        if (elements.productSearch) {
            elements.productSearch.addEventListener('input', filterProducts);
        }
        
        if (elements.categoryFilter) {
            elements.categoryFilter.addEventListener('change', filterProducts);
        }
        
        if (elements.checkoutBtn) {
            // Remove onclick from HTML and add event listener here
            elements.checkoutBtn.addEventListener('click', checkout);
        }
    }

    // Product grid click handler
    function handleProductClick(e) {
        const productCard = e.target.closest('.product-card');
        if (productCard) {
            addToCart(
                parseInt(productCard.dataset.id),
                productCard.dataset.name,
                parseFloat(productCard.dataset.price),
                parseInt(productCard.dataset.stock)
            );
        }
    }

    // Cart functions
    function addToCart(productId, name, price, stock) {
        console.log('Adding to cart:', { productId, name, price, stock });
        
        if (stock <= 0) {
            showAlert('This product is out of stock!', 'warning');
            return;
        }
        
        const existingItem = cart.find(item => item.id === productId);
        
        if (existingItem) {
            if (existingItem.quantity >= stock) {
                showAlert('Cannot add more items. Stock limit reached!', 'warning');
                return;
            }
            existingItem.quantity++;
        } else {
            cart.push({
                id: productId,
                name: name,
                price: parseFloat(price),
                quantity: 1,
                stock: stock
            });
        }
        
        updateCartDisplay();
        showAlert(`${name} added to cart!`, 'success');
    }

    function updateCartDisplay() {
        if (cart.length === 0) {
            elements.cartItems.innerHTML = `
                <div class="text-center py-4">
                    <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Your cart is empty</p>
                </div>
            `;
            elements.cartSummary.style.display = 'none';
            return;
        }
        
        elements.cartSummary.style.display = 'block';
        cartTotal = 0;
        
        let cartHtml = cart.map((item, index) => {
            const itemTotal = item.price * item.quantity;
            cartTotal += itemTotal;
            
            return `
                <div class="cart-item">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <h6 class="mb-1">${item.name}</h6>
                            <small class="text-muted">${config.currencySymbol}${item.price.toFixed(2)} × ${item.quantity}</small>
                        </div>
                        <div class="text-end">
                            <div class="fw-bold">${config.currencySymbol}${itemTotal.toFixed(2)}</div>
                            <button class="btn btn-sm btn-outline-danger remove-item" data-index="${index}">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                    <div class="quantity-control mt-2">
                        <button class="quantity-btn decrease" data-index="${index}">-</button>
                        <span class="fw-bold">${item.quantity}</span>
                        <button class="quantity-btn increase" data-index="${index}">+</button>
                    </div>
                </div>
            `;
        }).join('');
        
        elements.cartItems.innerHTML = cartHtml;
        
        // Add event listeners to cart buttons
        document.querySelectorAll('.remove-item').forEach(btn => {
            btn.addEventListener('click', function() {
                removeFromCart(parseInt(this.dataset.index));
            });
        });
        
        document.querySelectorAll('.quantity-btn.decrease').forEach(btn => {
            btn.addEventListener('click', function() {
                updateQuantity(parseInt(this.dataset.index), -1);
            });
        });
        
        document.querySelectorAll('.quantity-btn.increase').forEach(btn => {
            btn.addEventListener('click', function() {
                updateQuantity(parseInt(this.dataset.index), 1);
            });
        });
        
        const subtotal = cartTotal;
        const tax = subtotal * 0.16;
        const total = subtotal + tax;
        
        elements.subtotal.textContent = `${config.currencySymbol}${subtotal.toFixed(2)}`;
        elements.tax.textContent = `${config.currencySymbol}${tax.toFixed(2)}`;
        elements.total.textContent = `${config.currencySymbol}${total.toFixed(2)}`;
    }

    function updateQuantity(index, change) {
        const item = cart[index];
        const newQuantity = item.quantity + change;
        
        if (newQuantity <= 0) {
            removeFromCart(index);
            return;
        }
        
        if (newQuantity > item.stock) {
            showAlert('Cannot add more items. Stock limit reached!', 'warning');
            return;
        }
        
        item.quantity = newQuantity;
        updateCartDisplay();
    }

    function removeFromCart(index) {
        cart.splice(index, 1);
        updateCartDisplay();
        showAlert('Item removed from cart', 'info');
    }

    function clearCart() {
        if (confirm('Are you sure you want to clear the cart?')) {
            cart = [];
            updateCartDisplay();
            showAlert('Cart cleared', 'info');
        }
    }

    // Filter functionality
    function filterProducts() {
        const searchTerm = elements.productSearch.value.toLowerCase();
        const categoryFilter = elements.categoryFilter.value.toLowerCase();
        
        document.querySelectorAll('.product-item').forEach(item => {
            const name = (item.dataset.name || '').toLowerCase();
            const category = (item.dataset.category || '').toLowerCase();
            
            const matchesSearch = !searchTerm || name.includes(searchTerm);
            const matchesCategory = !categoryFilter || category.includes(categoryFilter);
            
            item.style.display = (matchesSearch && matchesCategory) ? 'block' : 'none';
        });
    }

    // FIXED Checkout function
    async function checkout() {
        if (isProcessing) {
            console.log('Already processing...');
            return;
        }
        
        if (cart.length === 0) {
            showAlert('Cart is empty!', 'warning');
            return;
        }
        
        console.log('Starting checkout process...');
        
        isProcessing = true;
        elements.checkoutBtn.disabled = true;
        elements.checkoutBtn.innerHTML = '<span class="loading-spinner me-2" style="display:inline-block;"></span>Processing...';
        
        // Calculate totals with proper validation
        const subtotal = parseFloat(cartTotal.toFixed(2));
        const taxAmount = parseFloat((subtotal * 0.16).toFixed(2));
        const totalAmount = parseFloat((subtotal + taxAmount).toFixed(2));
        
        // Validate cart items
        const validatedItems = cart.map(item => ({
            product_id: parseInt(item.id),
            quantity: parseInt(item.quantity),
            price: parseFloat(item.price)
        }));
        
        // Build order data with proper validation
        const orderData = {
            customer_id: elements.customerSelect.value ? parseInt(elements.customerSelect.value) : null,
            items: validatedItems,
            payment_method: elements.paymentMethod.value || 'cash',
            notes: elements.orderNotes.value || '',
            subtotal: subtotal,
            tax: taxAmount,
            total: totalAmount
        };
        
        console.log('Order data being sent:', orderData);
        console.log('Cart items:', cart);
        console.log('Validated items:', validatedItems);
        
        try {
            // Check if config exists
            if (!config.routes || !config.routes.createOrder) {
                throw new Error('Configuration missing: createOrder route not found');
            }
            
            if (!config.csrfToken) {
                throw new Error('CSRF token not found');
            }
            
            console.log('Sending request to:', config.routes.createOrder);
            
            const response = await fetch(config.routes.createOrder, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': config.csrfToken,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify(orderData)
            });
            
            console.log('Response status:', response.status);
            console.log('Response headers:', response.headers);
            
            // Check if response is JSON
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                const textResponse = await response.text();
                console.error('Non-JSON response:', textResponse);
                throw new Error('Server returned non-JSON response');
            }
            
            const data = await response.json();
            console.log('Response data:', data);
            
            if (!response.ok) {
                // Handle validation errors
                if (response.status === 422 && data.errors) {
                    const errorMessages = Object.values(data.errors).flat().join(', ');
                    throw new Error(`Validation errors: ${errorMessages}`);
                } else if (data.message) {
                    throw new Error(data.message);
                } else {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
            }
            
            if (data.success) {
                showAlert(`Order created successfully! Order #: ${data.order_number}`, 'success');
                
                // Clear cart and reset form
                cart = [];
                updateCartDisplay();
                elements.orderNotes.value = '';
                elements.customerSelect.value = '';
                elements.paymentMethod.value = 'cash';
                
                if (confirm('Order created successfully! Would you like to print the receipt?')) {
                    printReceipt(data.order);
                }
            } else {
                throw new Error(data.message || 'Unknown error occurred');
            }
            
        } catch (error) {
            console.error('Checkout error:', error);
            showAlert(`Error creating order: ${error.message}`, 'danger');
        } finally {
            isProcessing = false;
            elements.checkoutBtn.disabled = false;
            elements.checkoutBtn.innerHTML = '<i class="fas fa-check me-2"></i>Complete Sale';
        }
    }

    function printReceipt(order) {
        const receiptWindow = window.open('', '_blank', 'width=300,height=600');
        
        const subtotal = parseFloat(cartTotal.toFixed(2));
        const taxAmount = parseFloat((subtotal * 0.16).toFixed(2));
        const totalAmount = parseFloat((subtotal + taxAmount).toFixed(2));
        
        const receiptHtml = `
            <html>
            <head>
                <title>Receipt - Order #${order.order_number}</title>
                <style>
                    body { font-family: Arial, sans-serif; font-size: 12px; margin: 10px; }
                    .header { text-align: center; margin-bottom: 20px; }
                    .line { border-bottom: 1px dashed #000; margin: 10px 0; }
                    .total { font-weight: bold; font-size: 14px; }
                    .flex-between { display: flex; justify-content: space-between; margin: 5px 0; }
                </style>
            </head>
            <body>
                <div class="header">
                    <h3>${config.businessName}</h3>
                    <p>Receipt</p>
                    <p>Order #: ${order.order_number}</p>
                    <p>Date: ${new Date().toLocaleString()}</p>
                </div>
                <div class="line"></div>
                ${cart.map(item => `
                    <div class="flex-between">
                        <span>${item.name} x${item.quantity}</span>
                        <span>${config.currencySymbol}${(item.price * item.quantity).toFixed(2)}</span>
                    </div>
                `).join('')}
                <div class="line"></div>
                <div class="flex-between">
                    <span>Subtotal:</span>
                    <span>${config.currencySymbol}${subtotal.toFixed(2)}</span>
                </div>
                <div class="flex-between">
                    <span>Tax (16%):</span>
                    <span>${config.currencySymbol}${taxAmount.toFixed(2)}</span>
                </div>
                <div class="flex-between total">
                    <span>Total:</span>
                    <span>${config.currencySymbol}${totalAmount.toFixed(2)}</span>
                </div>
                <div class="line"></div>
                <p style="text-align: center;">Thank you for your business!</p>
                <script>
                    window.onload = function() {
                        window.print();
                        setTimeout(() => window.close(), 500);
                    }
                </script>
            </body>
            </html>
        `;
        
        receiptWindow.document.write(receiptHtml);
        receiptWindow.document.close();
    }

    // Customer functions
    async function saveCustomer() {
        const formData = {
            name: elements.customerName.value.trim(),
            phone: elements.customerPhone.value.trim(),
            email: elements.customerEmail.value.trim() || null,
            address: elements.customerAddress.value.trim() || null
        };
        
        if (!formData.name || !formData.phone) {
            showAlert('Name and phone are required!', 'warning');
            return;
        }
        
        try {
            const response = await fetch(config.routes.storeCustomer, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': config.csrfToken
                },
                body: JSON.stringify(formData)
            });
            
            const data = await response.json();
            
            if (data.success) {
                const option = new Option(`${data.customer.name} (${data.customer.phone})`, data.customer.id);
                elements.customerSelect.add(option);
                elements.customerSelect.value = data.customer.id;
                
                bootstrap.Modal.getInstance(document.getElementById('addCustomerModal')).hide();
                elements.addCustomerForm.reset();
                showAlert('Customer added successfully!', 'success');
            } else {
                showAlert(`Error: ${data.message || 'Unknown error'}`, 'danger');
            }
        } catch (error) {
            console.error('Customer save error:', error);
            showAlert(`Error: ${error.message}`, 'danger');
        }
    }

    // Utility functions
    function showAlert(message, type) {
        const toast = document.createElement('div');
        toast.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
        toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        toast.innerHTML = `${message}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>`;
        document.body.appendChild(toast);
        setTimeout(() => {
            if (toast.parentNode) {
                toast.parentNode.removeChild(toast);
            }
        }, 5000);
    }

    // Expose functions to global scope for HTML onclick handlers
    window.clearCart = clearCart;
    window.saveCustomer = saveCustomer;
    window.checkout = checkout;
    window.addToCart = addToCart;

    // Initialize the POS system
    initEventListeners();
    console.log('POS system initialized');
    console.log('Config:', config);
});