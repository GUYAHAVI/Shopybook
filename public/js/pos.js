// POS System JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Wait for configuration to be loaded
    if (typeof window.isEligibleForDynamicConversions === 'undefined') {
        console.log('Waiting for configuration to load...');
        setTimeout(() => {
            initializePOS();
        }, 100);
        return;
    }
    
    initializePOS();
});

function initializePOS() {
    // Initialize cart and variables
    let cart = [];
    let cartTotal = 0;
    let isProcessing = false;
    const config = window.posConfig || {};
    const taxConfig = Object.assign({ enabled: false, rate: 0, name: 'Tax', inclusive: false }, config.tax || {});

    // Mirrors App\Services\TaxCalculator. The server recalculates on checkout; this is for display only.
    function computeTotals(items) {
        const enabled = !!taxConfig.enabled && parseFloat(taxConfig.rate) > 0;
        const rate = enabled ? parseFloat(taxConfig.rate) : 0;
        const defaultTreatment = taxConfig.inclusive ? 'inclusive' : 'exclusive';
        let subtotal = 0, tax = 0, total = 0;

        items.forEach(item => {
            const gross = Math.round(item.price * item.quantity * 100) / 100;
            const treatment = ['inclusive', 'exclusive', 'exempt'].includes(item.taxTreatment) ? item.taxTreatment : defaultTreatment;
            let net = gross, lineTax = 0, lineGross = gross;

            if (enabled && treatment !== 'exempt') {
                if (treatment === 'inclusive') {
                    lineTax = Math.round(gross * rate / (100 + rate) * 100) / 100;
                    net = Math.round((gross - lineTax) * 100) / 100;
                } else {
                    lineTax = Math.round(net * rate / 100 * 100) / 100;
                    lineGross = Math.round((net + lineTax) * 100) / 100;
                }
            }
            subtotal += net; tax += lineTax; total += lineGross;
        });

        return {
            subtotal: Math.round(subtotal * 100) / 100,
            tax: Math.round(tax * 100) / 100,
            total: Math.round(total * 100) / 100,
            enabled, rate, inclusive: defaultTreatment === 'inclusive'
        };
    }

    function unitSuffix(item) {
        return item.unit && item.unit !== 'piece' ? ` ${item.unit}` : '';
    }

    // Units that may be sold fractionally (0.5 kg nails, 2.5 m rope, etc.).
    // Count-based units (piece/pack/box/dozen) stay whole.
    const FRACTIONAL_UNITS = ['kg', 'g', 'lb', 'l', 'ml', 'm', 'cm', 'mm', 'sqm', 'ft', 'in', 'other'];
    function isFractional(item) {
        return FRACTIONAL_UNITS.includes(item.unit);
    }
    function formatQty(q) {
        return parseFloat(Number(q).toFixed(3));
    }
    

    
    // DOM Elements
    const elements = {
        productSearch: document.getElementById('product_search'),
        categoryFilter: document.getElementById('categoryFilter'),
        productsGrid: document.getElementById('products_grid'),
        cartItems: document.getElementById('cart_items'),
        cartSummary: document.getElementById('cartSummary'),
        subtotal: document.getElementById('subtotal'),
        tax: document.getElementById('tax'),
        total: document.getElementById('total'),
        customerSelect: document.getElementById('customer_search'),
        paymentMethod: document.getElementById('payment_method'),
        orderNotes: document.getElementById('orderNotes'),
        checkoutBtn: document.getElementById('checkout_btn'),
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
            elements.checkoutBtn.addEventListener('click', checkout);
        }
        
        // Product search functionality
        const searchInput = document.getElementById('product_search');
        if (searchInput) {
            searchInput.addEventListener('input', function(e) {
                const searchTerm = e.target.value.toLowerCase();
                const productCards = document.querySelectorAll('.product-card');
                
                productCards.forEach(card => {
                    const productName = card.querySelector('.card-title').textContent.toLowerCase();
                    const productPrice = card.querySelector('.card-text').textContent.toLowerCase();
                    const productStock = card.querySelector('small').textContent.toLowerCase();
                    
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
        }

        // Add product card click listeners
        document.addEventListener('click', function(e) {
            const productCard = e.target.closest('.product-card');
            if (productCard) {
                handleProductClick(e);
            }
        });
    }

    // Product grid click handler
    function handleProductClick(e) {
        const productCard = e.target.closest('.product-card');
        if (!productCard) return;

        const productId = parseInt(productCard.dataset.productId);
        const productName = productCard.dataset.name || productCard.querySelector('.card-title').textContent;
        const productPrice = parseFloat(productCard.dataset.price);
        const productStock = parseFloat(productCard.dataset.stock);
        const productMeta = { unit: productCard.dataset.unit || 'piece', taxTreatment: productCard.dataset.taxTreatment || null };

        // Check if this product has conversion options (for eligible users)
        const hasConversionBadge = productCard.querySelector('.conversion-badge') !== null;
        const isEligible = window.isEligibleForDynamicConversions;
        
        console.log('Product click debug:', {
            productId,
            productName,
            isEligible,
            hasConversionBadge,
            conversionBadge: productCard.querySelector('.conversion-badge'),
            productCardHTML: productCard.outerHTML.substring(0, 200) + '...'
        });

        if (isEligible && hasConversionBadge) {
            // Ask user if they want to convert or add normally
            const shouldConvert = confirm(`Do you want to convert ${productName} to a different unit, or add it normally?\n\nClick OK to convert, Cancel to add normally.`);
            
            if (shouldConvert) {
                console.log('Opening conversion modal for:', productName);
                e.preventDefault();
                e.stopPropagation();
                showDynamicConversionModal(productId, productName, productPrice, productStock);
            } else {
                console.log('Adding to cart normally:', productName);
                addToCart(productId, productName, productPrice, productStock, productMeta);
            }
        } else {
            console.log('Adding to cart normally:', productName);
            // Regular add to cart
            addToCart(productId, productName, productPrice, productStock, productMeta);
        }
    }

    // Cart functions
    function addToCart(productId, name, price, stock, meta = {}) {
        
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
                stock: stock,
                unit: meta.unit || 'piece',
                taxTreatment: meta.taxTreatment || null
            });
        }
        
        updateCartDisplay();
        showAlert(`${name} added to cart!`, 'success');
    }

    function updateCartDisplay() {
        if (cart.length === 0) {
            if (elements.cartItems) {
                elements.cartItems.innerHTML = `
                    <div class="text-center py-4">
                        <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Your cart is empty</p>
                    </div>
                `;
            }
            return;
        }
        cartTotal = 0;
        
        let cartHtml = cart.map((item, index) => {
            const itemTotal = item.price * item.quantity;
            cartTotal += itemTotal;
            
            return `
                <div class="cart-item">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <h6 class="mb-1">${item.name}</h6>
                            <small class="text-muted">${config.currencySymbol}${item.price.toFixed(2)} × ${item.quantity}${unitSuffix(item)}</small>
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
                        <input type="number" class="quantity-input" value="${item.quantity}" min="0.001" step="${isFractional(item) ? '0.01' : '1'}" max="${item.stock}" data-index="${index}" style="width: ${isFractional(item) ? '75px' : '60px'}; text-align: center; margin: 0 5px;">
                        <button class="quantity-btn increase" data-index="${index}">+</button>
                    </div>
                </div>
            `;
        }).join('');
        
        if (elements.cartItems) {
            elements.cartItems.innerHTML = cartHtml;
        }
        
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
         
         // Add event listeners for quantity input fields
         document.querySelectorAll('.quantity-input').forEach(input => {
             input.addEventListener('change', function() {
                 const index = parseInt(this.dataset.index);
                 const item = cart[index];
                 // Decimals are accepted for every product — a cashier typing
                 // 0.5 means 0.5, regardless of the product's unit.
                 const newQuantity = parseFloat(this.value);
                 
                 if (isNaN(newQuantity) || newQuantity <= 0) {
                     this.value = item.quantity; // restore last valid value
                 } else if (newQuantity > item.stock) {
                     this.value = item.stock;
                     item.quantity = item.stock;
                     showAlert('Quantity cannot exceed available stock!', 'warning');
                 } else {
                     item.quantity = formatQty(newQuantity);
                 }
                 
                 updateCartDisplay();
             });
             
             input.addEventListener('keypress', function(e) {
                 if (e.key === 'Enter') {
                     this.blur(); // Trigger change event
                 }
             });
         });
        
        const totals = computeTotals(cart);
        const { subtotal, tax, total } = totals;

        if (elements.subtotal) elements.subtotal.textContent = `${config.currencySymbol}${subtotal.toFixed(2)}`;
        if (elements.tax) elements.tax.textContent = `${config.currencySymbol}${tax.toFixed(2)}`;

        const taxRow = document.getElementById('taxRow');
        if (taxRow) taxRow.style.display = totals.enabled ? '' : 'none';

        const taxLabelElement = document.getElementById('taxLabel');
        if (taxLabelElement && totals.enabled) {
            const rateText = `${taxConfig.name} (${parseFloat(totals.rate).toFixed(totals.rate % 1 === 0 ? 0 : 2)}%)`;
            taxLabelElement.textContent = totals.inclusive ? `Incl. ${rateText}:` : `${rateText}:`;
        }
        if (elements.total) elements.total.textContent = `${config.currencySymbol}${total.toFixed(2)}`;
         
         // Enable/disable checkout button based on cart items
         if (elements.checkoutBtn) {
             if (cart.length > 0) {
                 elements.checkoutBtn.disabled = false;
                 elements.checkoutBtn.classList.remove('btn-secondary');
                 elements.checkoutBtn.classList.add('btn-success');
             } else {
                 elements.checkoutBtn.disabled = true;
                 elements.checkoutBtn.classList.remove('btn-success');
                 elements.checkoutBtn.classList.add('btn-secondary');
             }
         }
         
         if (window.updatePartialPaymentTotals) {
             window.updatePartialPaymentTotals();
         }
    }

    function updateQuantity(index, change) {
        const item = cart[index];
        // Fractional units step by 0.5 so half-kilos/litres are quick to tap;
        // precise amounts (e.g. 0.25 kg) are typed into the input.
        const step = isFractional(item) ? 0.5 : 1;
        const newQuantity = formatQty(item.quantity + change * step);
        
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

    // Checkout function
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
        if (elements.checkoutBtn) {
            elements.checkoutBtn.disabled = true;
            elements.checkoutBtn.innerHTML = '<span class="loading-spinner me-2" style="display:inline-block;"></span>Processing...';
        }
        
        // Totals are recalculated server-side; these are sent for logging/comparison only
        const isEligibleBusiness = window.isEligibleForDynamicConversions;
        const totals = computeTotals(cart);
        const subtotal = totals.subtotal;
        const taxAmount = totals.tax;
        const totalAmount = totals.total;
        
        // Validate cart items
        const validatedItems = cart.map(item => ({
            product_id: parseInt(item.id),
            quantity: parseFloat(item.quantity),
            price: parseFloat(item.price)
        }));
        
        // Get payment status
        const paymentStatus = document.getElementById('payment_status') && document.getElementById('payment_status').value ? document.getElementById('payment_status').value : 'paid';
        
        // Get partial payment amount if applicable
        let amountPaid = totalAmount;
        if (paymentStatus === 'partial') {
            const partialAmountInput = document.getElementById('partial_amount');
            amountPaid = partialAmountInput && partialAmountInput.value ? parseFloat(partialAmountInput.value) : 0;
            
            if (amountPaid <= 0 || amountPaid > totalAmount) {
                throw new Error(`Invalid partial payment amount. Must be between 0 and ${totalAmount.toFixed(2)}`);
            }
        } else if (paymentStatus === 'unpaid') {
            amountPaid = 0;
        }
        
        // Build order data with proper validation
        const orderData = {
            customer_id: elements.customerSelect && elements.customerSelect.value ? parseInt(elements.customerSelect.value) : null,
            items: validatedItems,
            payment_method: elements.paymentMethod && elements.paymentMethod.value ? elements.paymentMethod.value : 'cash',
            payment_status: paymentStatus,
            amount_paid: amountPaid,
            mpesa_reference: (document.getElementById('mpesa_reference') || {}).value || null,
            notes: elements.orderNotes && elements.orderNotes.value ? elements.orderNotes.value : '',
            subtotal: subtotal,
            tax: taxAmount,
            total: totalAmount,
            // Include cart data for receipt generation
            cart_data: {
                cart_items: cart,
                currency_symbol: config.currencySymbol,
                business_name: config.businessName,
                is_eligible_for_conversions: isEligibleBusiness,
                customer_name: elements.customerSelect && elements.customerSelect.value ? elements.customerSelect.options[elements.customerSelect.selectedIndex].text : 'Walk-in Customer',
                customer_phone: '',
                customer_email: '',
                customer_address: ''
            }
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
                // Clear cart and reset form
                cart = [];
                updateCartDisplay();
                if (elements.orderNotes) elements.orderNotes.value = '';
                if (elements.customerSelect) elements.customerSelect.value = '';
                if (elements.paymentMethod) elements.paymentMethod.value = 'cash';
                if (document.getElementById('payment_status')) document.getElementById('payment_status').value = 'paid';
                
                // Store order data for receipt printing and invoice generation
                window.lastOrderData = {
                    ...data.order,
                    order_id: data.order.id,
                    order_number: data.order_number,
                    payment_status: orderData.payment_status
                };
                
                // Show success modal with appropriate buttons
                const successModal = new bootstrap.Modal(document.getElementById('successModal'));
                document.getElementById('orderNumber').textContent = data.order_number;
                
                // Show/hide buttons based on payment status
                // partial → invoice (balance still owed); unpaid/pending → invoice; paid → receipt
                const isInvoice = orderData.payment_status === 'pending'
                               || orderData.payment_status === 'unpaid'
                               || orderData.payment_status === 'partial';
                const paymentStatusDisplay = document.getElementById('paymentStatusDisplay');
                const receiptMessage = document.getElementById('receiptMessage');
                const invoiceMessage = document.getElementById('invoiceMessage');
                const printReceiptBtnCol = document.getElementById('printReceiptBtnCol');
                const generateInvoiceBtnCol = document.getElementById('generateInvoiceBtnCol');
                
                if (isInvoice) {
                    // Show invoice — covers partial payment and fully unpaid
                    if (paymentStatusDisplay) paymentStatusDisplay.style.display = 'block';
                    if (receiptMessage) receiptMessage.style.display = 'none';
                    if (invoiceMessage) invoiceMessage.style.display = 'block';
                    if (printReceiptBtnCol) printReceiptBtnCol.style.display = 'none';
                    if (generateInvoiceBtnCol) generateInvoiceBtnCol.style.display = 'block';
                } else {
                    // Fully paid — show receipt
                    if (paymentStatusDisplay) paymentStatusDisplay.style.display = 'none';
                    if (receiptMessage) receiptMessage.style.display = 'block';
                    if (invoiceMessage) invoiceMessage.style.display = 'none';
                    if (printReceiptBtnCol) printReceiptBtnCol.style.display = 'block';
                    if (generateInvoiceBtnCol) generateInvoiceBtnCol.style.display = 'none';
                }
                
                successModal.show();
            } else {
                throw new Error(data.message || 'Unknown error occurred');
            }
            
        } catch (error) {
            console.error('Checkout error:', error);
            showAlert(`Error creating order: ${error.message}`, 'danger');
        } finally {
            isProcessing = false;
            if (elements.checkoutBtn) {
                elements.checkoutBtn.disabled = false;
                elements.checkoutBtn.innerHTML = '<i class="fas fa-check me-2"></i>Complete Sale';
            }
        }
    }

         function printReceipt(order) {
         // Validate order parameter
         if (!order || !order.order_number) {
             console.error('Invalid order data for receipt:', order);
             showAlert('Invalid order data for receipt generation.', 'danger');
             return;
         }
         
         // Use the same receipt route as the orders page for consistency
         const receiptUrl = `/orders/${order.id}/receipt`;
         const receiptWindow = window.open(receiptUrl, '_blank', 'width=400,height=800');
         
         // Check if window was opened successfully
         if (!receiptWindow) {
             console.error('Failed to open receipt window. Popup may be blocked.');
             showAlert('Receipt window could not be opened. Please check your popup blocker settings.', 'warning');
             return;
         }
         
         // The receipt will automatically print when loaded
         receiptWindow.onload = function() {
             setTimeout(() => {
                 receiptWindow.print();
                 setTimeout(() => receiptWindow.close(), 1000);
             }, 500);
         };
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

    // Custom confirmation function with cancel callback
    function showConfirm(message, onConfirm, onCancel = null, isConversion = false) {
        const modalElement = document.getElementById('confirmModal');
        const titleElement = document.getElementById('confirmModalTitle');
        const messageElement = document.getElementById('confirmModalMessage');
        const yesBtn = document.getElementById('confirmModalYesBtn');
        const cancelBtn = document.getElementById('confirmModalCancelBtn');
        
        if (modalElement && titleElement && messageElement && yesBtn) {
            messageElement.textContent = message;
            
            // Remove existing event listeners
            const newYesBtn = yesBtn.cloneNode(true);
            yesBtn.parentNode.replaceChild(newYesBtn, yesBtn);
            
            // Add new event listener for confirm
            newYesBtn.addEventListener('click', function() {
                if (isConversion) {
                    window.conversionChosen = true;
                }
                if (typeof bootstrap !== 'undefined') {
                    const modal = bootstrap.Modal.getInstance(modalElement);
                    if (modal) {
                        modal.hide();
                    }
                }
                if (onConfirm) onConfirm();
            });
            
            // Handle cancel button if provided
            if (cancelBtn && onCancel) {
                const newCancelBtn = cancelBtn.cloneNode(true);
                cancelBtn.parentNode.replaceChild(newCancelBtn, cancelBtn);
                
                newCancelBtn.addEventListener('click', function() {
                    if (typeof bootstrap !== 'undefined') {
                        const modal = bootstrap.Modal.getInstance(modalElement);
                        if (modal) {
                            modal.hide();
                        }
                    }
                    if (onCancel) onCancel();
                });
            }
            
            // Check if Bootstrap is available
            if (typeof bootstrap !== 'undefined') {
                const modal = new bootstrap.Modal(modalElement);
                modal.show();
            } else {
                // Fallback to browser confirm
                if (confirm(message)) {
                    if (isConversion) {
                        window.conversionChosen = true;
                    }
                    if (onConfirm) onConfirm();
                } else if (onCancel) {
                    onCancel();
                }
            }
        } else {
            // Fallback to browser confirm
            if (confirm(message)) {
                if (isConversion) {
                    window.conversionChosen = true;
                }
                if (onConfirm) onConfirm();
            } else if (onCancel) {
                onCancel();
            }
        }
    }

    // Dynamic Conversion Functions (for eligible users)
    let currentConversionProduct = null;
    let conversionOptions = [];

    // Show dynamic conversion modal
    async function showDynamicConversionModal(productId, productName, productPrice, productStock) {
        console.log('showDynamicConversionModal called with:', { productId, productName, productPrice, productStock });
        
        const modalElement = document.getElementById('dynamicConversionModal');
        if (!modalElement) {
            console.error('Dynamic conversion modal not found!');
            showAlert('Conversion modal not available. Please refresh the page.', 'danger');
            return;
        }

        // Check if Bootstrap is available
        if (typeof bootstrap === 'undefined') {
            console.error('Bootstrap is not available!');
            showAlert('Bootstrap not loaded. Please refresh the page.', 'danger');
            return;
        }

        currentConversionProduct = {
            id: productId,
            name: productName,
            price: productPrice,
            stock: productStock
        };

        // Update modal content
        const nameElement = document.getElementById('conversionProductName');
        if (nameElement) nameElement.textContent = productName;
        
        // Update product info
        const productInfoElement = document.getElementById('conversionProductInfo');
        if (productInfoElement) {
            productInfoElement.textContent = `Base Price: KSh ${productPrice.toFixed(2)} | Stock: ${productStock}`;
        }
        
        // Set the product ID in the hidden field
        const productIdElement = document.getElementById('conversionProductId');
        if (productIdElement) {
            productIdElement.value = productId;
        }

        // Reset form
        resetConversionForm();

        // Show modal
        try {
            console.log('Creating Bootstrap modal...');
            const modal = new bootstrap.Modal(modalElement);
            console.log('Showing modal...');
            modal.show();
            console.log('Modal shown successfully');
        } catch (error) {
            console.error('Error showing modal:', error);
            showAlert('Error opening conversion modal. Please try again.', 'danger');
        }
    }

    // Reset conversion form
    function resetConversionForm() {
        const sellUnitSelect = document.getElementById('posSellUnit');
        const materialTypeSelect = document.getElementById('posMaterialType');
        const quantityInput = document.getElementById('posQuantity');
        const pricePerUnitInput = document.getElementById('posPricePerUnit');
        const autoCalculationDiv = document.getElementById('posAutoCalculation');
        
        if (sellUnitSelect) sellUnitSelect.value = '';
        if (materialTypeSelect) materialTypeSelect.value = '';
        if (quantityInput) quantityInput.value = '';
        if (pricePerUnitInput) pricePerUnitInput.value = '';
        if (autoCalculationDiv) autoCalculationDiv.style.display = 'none';
        
        // Update help text
        updateHelpText();
    }
    
    // Update help text based on selections
    function updateHelpText() {
        const sellUnit = document.getElementById('posSellUnit')?.value;
        const materialType = document.getElementById('posMaterialType')?.value;
        const quantityHelp = document.getElementById('quantityHelp');
        const priceHelp = document.getElementById('priceHelp');
        
        if (quantityHelp) {
            if (sellUnit) {
                quantityHelp.textContent = `Enter quantity in ${sellUnit}`;
            } else {
                quantityHelp.textContent = 'Enter quantity in the selected unit';
            }
        }
        
        if (priceHelp) {
            if (sellUnit) {
                priceHelp.textContent = `Price per ${sellUnit}`;
            } else {
                priceHelp.textContent = 'Price per kg or per sqm';
            }
        }
    }
    
    // Auto-calculate conversion when form changes
    function autoCalculateConversion() {
        const sellUnit = document.getElementById('posSellUnit')?.value;
        const materialType = document.getElementById('posMaterialType')?.value;
        const quantity = parseFloat(document.getElementById('posQuantity')?.value) || 0;
        const pricePerUnit = parseFloat(document.getElementById('posPricePerUnit')?.value) || 0;
        
        if (!sellUnit || !materialType || quantity <= 0 || pricePerUnit <= 0) {
            document.getElementById('posAutoCalculation').style.display = 'none';
            return;
        }
        
        // Calculate conversion based on material type
        const conversionResult = calculateMaterialConversion(quantity, sellUnit, materialType);
        
        if (conversionResult) {
            displayAutoCalculation(conversionResult, pricePerUnit, materialType);
        }
    }
    
    // Calculate material conversion
    function calculateMaterialConversion(quantity, sellUnit, materialType) {
        // Extract micron value from material type
        const micronMatch = materialType.match(/(\d+\.\d+)/);
        if (!micronMatch) return null;
        
        const microns = parseFloat(micronMatch[1]);
        
        if (sellUnit === 'kg') {
            // Converting kg to sqm: kg / microns = sqm
            const sqm = quantity / microns;
            return {
                originalQuantity: quantity,
                originalUnit: 'kg',
                convertedQuantity: sqm,
                convertedUnit: 'sqm',
                conversionFactor: `1 kg = ${(1/microns).toFixed(2)} sqm (${microns} microns)`,
                formula: `sqm = kg ÷ ${microns}`
            };
        } else if (sellUnit === 'sqm') {
            // Converting sqm to kg: sqm * microns = kg
            const kg = quantity * microns;
            return {
                originalQuantity: quantity,
                originalUnit: 'sqm',
                convertedQuantity: kg,
                convertedUnit: 'kg',
                conversionFactor: `1 sqm = ${microns.toFixed(2)} kg (${microns} microns)`,
                formula: `kg = sqm × ${microns}`
            };
        }
        
        return null;
    }
    
    // Display auto-calculation results
    function displayAutoCalculation(conversionResult, pricePerUnit, materialType) {
        const totalAmount = conversionResult.originalQuantity * pricePerUnit;
        const materialTypeDisplay = materialType.replace('_', ' ').replace('0.', '0.');
        
        document.getElementById('posOriginalQuantity').textContent = `${conversionResult.originalQuantity} ${conversionResult.originalUnit}`;
        document.getElementById('posConvertedQuantity').textContent = `${conversionResult.convertedQuantity.toFixed(2)} ${conversionResult.convertedUnit}`;
        document.getElementById('posConversionFactor').textContent = conversionResult.conversionFactor;
        document.getElementById('posTotalAmount').textContent = `KSh ${totalAmount.toFixed(2)}`;
        document.getElementById('posMaterialTypeDisplay').textContent = materialTypeDisplay;
        
        document.getElementById('posAutoCalculation').style.display = 'block';
    }



    // Add converted product to cart
    function addConvertedProductToCart() {
        if (!currentConversionProduct) return;

        const sellUnit = document.getElementById('posSellUnit')?.value;
        const materialType = document.getElementById('posMaterialType')?.value;
        const quantity = parseFloat(document.getElementById('posQuantity')?.value) || 0;
        const pricePerUnit = parseFloat(document.getElementById('posPricePerUnit')?.value) || 0;

        if (!sellUnit || !materialType || quantity <= 0 || pricePerUnit <= 0) {
            showAlert('Please fill in all required fields', 'warning');
            return;
        }

        // Calculate conversion
        const conversionResult = calculateMaterialConversion(quantity, sellUnit, materialType);
        if (!conversionResult) {
            showAlert('Error calculating conversion. Please try again.', 'danger');
            return;
        }

        const totalAmount = quantity * pricePerUnit;
        const materialTypeDisplay = materialType.replace('_', ' ').replace('0.', '0.');

        // Add to cart with conversion info
        const cartItem = {
            id: currentConversionProduct.id,
            name: `${currentConversionProduct.name} (${quantity} ${sellUnit} - ${materialTypeDisplay})`,
            price: totalAmount,
            quantity: 1,
            stock: currentConversionProduct.stock,
            conversion: {
                sell_unit: sellUnit,
                material_type: materialType,
                original_quantity: quantity,
                converted_quantity: conversionResult.convertedQuantity,
                converted_unit: conversionResult.convertedUnit,
                price_per_unit: pricePerUnit,
                total_amount: totalAmount
            }
        };

        cart.push(cartItem);
        updateCartDisplay();
        
        // Close modal
        bootstrap.Modal.getInstance(document.getElementById('dynamicConversionModal')).hide();
        
        showAlert(`${cartItem.name} added to cart!`, 'success');
    }

    // Initialize dynamic conversion event listeners
    function initDynamicConversionListeners() {
        if (!window.isEligibleForDynamicConversions) return;

        // Sell unit change
        const sellUnitSelect = document.getElementById('posSellUnit');
        if (sellUnitSelect) {
            sellUnitSelect.addEventListener('change', function() {
                updateHelpText();
                autoCalculateConversion();
            });
        }

        // Material type change
        const materialTypeSelect = document.getElementById('posMaterialType');
        if (materialTypeSelect) {
            materialTypeSelect.addEventListener('change', autoCalculateConversion);
        }

        // Quantity input change
        const quantityInput = document.getElementById('posQuantity');
        if (quantityInput) {
            quantityInput.addEventListener('input', autoCalculateConversion);
        }

        // Price per unit input change
        const pricePerUnitInput = document.getElementById('posPricePerUnit');
        if (pricePerUnitInput) {
            pricePerUnitInput.addEventListener('input', autoCalculateConversion);
        }

        // Add to cart button
        const addToCartBtn = document.getElementById('addConvertedToCart');
        if (addToCartBtn) {
            addToCartBtn.addEventListener('click', addConvertedProductToCart);
        }
    }

    // Expose functions to global scope for HTML onclick handlers
    window.clearCart = clearCart;
    window.saveCustomer = saveCustomer;
    
    // Add function to update partial payment display
    function calculateTotal() {
        return computeTotals(cart).total;
    }

    window.updatePartialPaymentTotals = function() {
        const total = calculateTotal();
        const partialAmountInput = document.getElementById('partial_amount');
        const totalDisplay = document.getElementById('partial_total_display');
        const balanceDisplay = document.getElementById('partial_balance_display');
        
        if (totalDisplay) totalDisplay.textContent = total.toFixed(2);
        
        if (partialAmountInput && balanceDisplay) {
            const amountPaid = parseFloat(partialAmountInput.value) || 0;
            const balance = total - amountPaid;
            balanceDisplay.textContent = balance.toFixed(2);
            
            // Validate amount
            if (amountPaid > total) {
                partialAmountInput.setCustomValidity('Amount cannot exceed total');
                balanceDisplay.style.color = 'red';
            } else if (amountPaid < 0) {
                partialAmountInput.setCustomValidity('Amount must be positive');
                balanceDisplay.style.color = 'red';
            } else {
                partialAmountInput.setCustomValidity('');
                balanceDisplay.style.color = balance > 0 ? 'red' : 'green';
            }
        }
    };
    window.checkout = checkout;
    window.addToCart = addToCart;
    

    

    
    // Add event listener for clear cart button
    const clearCartBtn = document.getElementById('clear_cart_btn');
    if (clearCartBtn) {
        clearCartBtn.addEventListener('click', clearCart);
    }

    // Initialize the POS system
    initEventListeners();
    initDynamicConversionListeners();
}