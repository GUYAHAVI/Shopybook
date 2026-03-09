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

        @if($isEligibleForDynamicConversions)
        <div class="alert alert-info alert-dismissible fade show mb-4" role="alert">
            <i class="fas fa-magic me-2"></i>
            <strong>Dynamic Conversion System Active!</strong> 
            Click products with <span class="badge bg-warning text-dark mx-1"><i class="fas fa-exchange-alt"></i> Convert</span> badges to sell in different units.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <i class="fas fa-info-circle me-2"></i>
            <strong>Special Tax Arrangement:</strong> 
            This business operates under a flat yearly tax of KSh 5,000. No VAT is charged on individual sales.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        

    @endif

    <div class="row">
        <div class="col-md-8">
            <div class="card" style="background: var(--card-bg); border: 1px solid var(--border-color);">
                <div class="card-header" style="background: var(--bg-tertiary); border-bottom: 1px solid var(--border-color);">
                    <h5 class="mb-0" style="color: var(--text-primary);">
                        Point of Sale
                        @if($isEligibleForDynamicConversions)
                            <span class="badge bg-warning text-dark ms-2">
                                <i class="fas fa-exchange-alt me-1"></i>Dynamic Conversions
                            </span>
                        @endif
                    </h5>
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
                                    <option value="mobile_money">Mobile Money (Generic)</option>
                                    <option value="airtel_money">Airtel Money</option>
                                    <option value="equitel">Equitel</option>
                                    <option value="card">Card</option>
                                    <option value="bank_transfer">Bank Transfer</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- M-Pesa extras (phone + STK toggle) – only when Paystack configured --}}
                    @php $paystackCfg = $business->getSettingsOrCreate(); @endphp
                    @if($paystackCfg->paystack_enabled)
                    <div class="row" id="mpesa_phone_row" style="display:none;">
                        <div class="col-md-12">
                            <div class="mb-2">
                                {{-- STK Push toggle --}}
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" id="use_stk_push" checked>
                                    <label class="form-check-label" for="use_stk_push" style="color: var(--text-primary);">
                                        <strong>Send STK Push</strong>
                                        <span class="text-muted small"> — prompt customer's phone via Paystack</span>
                                    </label>
                                </div>
                                {{-- Phone field (only needed for STK push) --}}
                                <div id="stk_phone_area">
                                    <label for="mpesa_phone" class="form-label" style="color: var(--text-primary);">
                                        <i class="fas fa-mobile-alt me-1"></i>Customer Phone Number
                                    </label>
                                    <input type="tel" class="form-control" id="mpesa_phone"
                                           placeholder="e.g. 0712345678"
                                           style="border: 1px solid var(--border-color); background: var(--card-bg); color: var(--text-primary);">
                                    <small class="text-muted">Number that will receive the STK push (07XXXXXXXX)</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="payment_status" class="form-label" style="color: var(--text-primary);">Payment Status</label>
                                <select class="form-control" id="payment_status" onchange="togglePartialPayment()" style="border: 1px solid var(--border-color); background: var(--card-bg); color: var(--text-primary);">
                                    <option value="paid">Paid in Full</option>
                                    <option value="partial">Partial Payment</option>
                                    <option value="unpaid">Unpaid (Generate Invoice)</option>
                                </select>
                                <small class="text-muted">Select payment status for this order</small>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Partial Payment Amount Field -->
                    <div class="row" id="partial_payment_row" style="display: none;">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="partial_amount" class="form-label" style="color: var(--text-primary);">Amount Received</label>
                                <input type="number" class="form-control" id="partial_amount" step="0.01" min="0" placeholder="Enter amount received"
                                       style="border: 1px solid var(--border-color); background: var(--card-bg); color: var(--text-primary);">
                                <small class="text-muted">Total: <span id="partial_total_display">0.00</span> | Balance: <span id="partial_balance_display">0.00</span></small>
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
                                <div class="card product-card" 
                                     data-id="{{ $product->id }}"
                                     data-product-id="{{ $product->id }}" 
                                     data-name="{{ $product->name }}"
                                     data-price="{{ $product->price }}"
                                     data-stock="{{ $product->stock_quantity }}"
                                     style="background: var(--card-bg); border: 1px solid var(--border-color); cursor: pointer;"
                                     @if($isEligibleForDynamicConversions && $product->conversions()->exists())
                                     title="Click to open conversion options - Sell in different units!"
                                     @endif>
                                    <div class="product-image-container">
                                        @if($product->image)
                                            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="product-image">
                                        @else
                                            <div class="product-image-placeholder d-flex align-items-center justify-content-center" style="background: var(--bg-tertiary);">
                                                <i class="fas fa-image fa-2x" style="color: var(--text-muted);"></i>
                                            </div>
                                        @endif
                                        @if($isEligibleForDynamicConversions && $product->conversions()->exists())
                                            <div class="conversion-badge" style="position: absolute; top: 5px; right: 5px; background: linear-gradient(45deg, #ffc107, #ff9800); color: #000; padding: 4px 8px; border-radius: 15px; font-size: 11px; font-weight: bold; box-shadow: 0 2px 4px rgba(0,0,0,0.2); animation: pulse 2s infinite;">
                                                <i class="fas fa-exchange-alt me-1"></i>Convert
                                            </div>
                                        @endif
                                    </div>
                                    <div class="card-body text-center">
                                        <h6 class="card-title" style="color: var(--text-primary);">{{ $product->name }}</h6>
                                        <p class="card-text" style="color: var(--text-secondary);">KSh {{ number_format($product->price, 2) }}</p>
                                        <small style="color: var(--text-muted);">Stock: {{ $product->stock_quantity }}</small>
                                        @if($isEligibleForDynamicConversions && $product->conversions()->exists())
                                            <div class="mt-2">
                                                <div class="alert alert-warning alert-sm py-1 px-2 mb-0" style="font-size: 11px; border-radius: 8px;">
                                                    <i class="fas fa-calculator me-1"></i><strong>Click to convert units!</strong>
                                                </div>
                                            </div>
                                        @endif
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
                        <span style="color: var(--text-primary);" id="taxLabel">Tax (16%):</span>
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

                    @if($paystackCfg->paystack_enabled)
                    <button class="btn btn-success w-100 mb-2" id="mpesa_stk_btn" style="display:none;" disabled>
                        <i class="fas fa-mobile-alt me-2"></i>Send M-Pesa STK Push
                    </button>
                    @endif
                    
                    <button class="btn btn-outline-secondary w-100 mb-2" id="clear_cart_btn">
                        <i class="fas fa-trash me-2"></i>Clear Cart
                    </button>
                    

                    

                    
                    <!-- Reprint Last Receipt Button -->
                    <button type="button" class="btn btn-outline-info w-100" id="reprint_btn" style="display: none;">
                        <i class="fas fa-print me-2"></i>Reprint Last Receipt
                    </button>
                 </div>
             </div>
         </div>
     </div>
 </div>
 
 <!-- Custom Success Modal -->
 <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
     <div class="modal-dialog modal-dialog-centered modal-lg">
         <div class="modal-content" style="background: var(--card-bg); border: 1px solid var(--border-color); border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.3);">
             <div class="modal-header text-center" style="background: linear-gradient(135deg, #28a745, #20c997); border-bottom: none; border-radius: 15px 15px 0 0; padding: 2rem 1.5rem 1rem;">
                 <div class="w-100">
                     <div class="success-icon-container mb-3">
                         <div class="success-icon">
                             <i class="fas fa-check-circle"></i>
                         </div>
                     </div>
                     <h4 class="modal-title text-white mb-0" id="successModalLabel">
                         <strong>Sale Completed Successfully!</strong>
                     </h4>
                 </div>
                 <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" aria-label="Close"></button>
             </div>
             
             <div class="modal-body text-center p-4" style="color: var(--text-primary);">
                 <div class="success-details mb-4">
                     <div class="order-number-display mb-3">
                         <span class="order-label">Order Number:</span>
                         <span class="order-value" id="orderNumber"></span>
                     </div>
                     <div class="payment-status-display mb-3" id="paymentStatusDisplay" style="display: none;">
                         <span class="badge bg-warning text-dark" style="font-size: 1rem; padding: 0.5rem 1rem;">
                             <i class="fas fa-clock me-2"></i>Payment Pending
                         </span>
                     </div>
                     <div class="success-message" id="receiptMessage">
                         <p class="mb-0 text-success">
                             <i class="fas fa-receipt me-2"></i>
                             Receipt has been generated and is ready for printing
                         </p>
                     </div>
                     <div class="invoice-message" id="invoiceMessage" style="display: none;">
                         <p class="mb-0 text-info">
                             <i class="fas fa-file-invoice me-2"></i>
                             Invoice has been generated for this unpaid order
                         </p>
                     </div>
                 </div>
                 
                 <div class="action-buttons">
                     <div class="row g-3" id="actionButtonsRow">
                         <div class="col-md-4">
                             <button type="button" class="btn btn-outline-secondary w-100" data-bs-dismiss="modal">
                                 <i class="fas fa-times me-2"></i>Close
                             </button>
                         </div>
                         <div class="col-md-4" id="printReceiptBtnCol">
                             <button type="button" class="btn btn-primary w-100" id="printReceiptBtn">
                                 <i class="fas fa-print me-2"></i>Print Receipt
                             </button>
                         </div>
                         <div class="col-md-4" id="generateInvoiceBtnCol" style="display: none;">
                             <button type="button" class="btn btn-warning w-100" id="generateInvoiceBtn">
                                 <i class="fas fa-file-invoice me-2"></i>Generate Invoice
                             </button>
                         </div>
                         <div class="col-md-4">
                             <a href="{{ route('sales.orders') }}" class="btn btn-outline-primary w-100">
                                 <i class="fas fa-list me-2"></i>View Orders
                             </a>
                         </div>
                     </div>
                 </div>
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

 <!-- Custom Alert Modals -->
 <!-- Success Modal -->
 <div class="modal fade" id="successAlertModal" tabindex="-1" aria-labelledby="successAlertModalLabel" aria-hidden="true">
     <div class="modal-dialog modal-dialog-centered">
         <div class="modal-content" style="background: var(--card-bg); border: 1px solid var(--border-color);">
             <div class="modal-header" style="background: var(--bg-tertiary); border-bottom: 1px solid var(--border-color);">
                 <h5 class="modal-title" id="successAlertModalLabel" style="color: var(--text-primary);">
                     <i class="fas fa-check-circle text-success me-2"></i>Success
                 </h5>
                 <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
             </div>
             <div class="modal-body" style="color: var(--text-primary);">
                 <div class="text-center">
                     <i class="fas fa-check-circle text-success fa-3x mb-3"></i>
                     <h6 id="successAlertTitle">Success!</h6>
                     <p class="mb-0" id="successAlertMessage"></p>
                 </div>
             </div>
             <div class="modal-footer" style="border-top: 1px solid var(--border-color);">
                 <button type="button" class="btn btn-success" data-bs-dismiss="modal">OK</button>
             </div>
         </div>
     </div>
 </div>

 <!-- Warning Modal -->
 <div class="modal fade" id="warningAlertModal" tabindex="-1" aria-labelledby="warningAlertModalLabel" aria-hidden="true">
     <div class="modal-dialog modal-dialog-centered">
         <div class="modal-content" style="background: var(--card-bg); border: 1px solid var(--border-color);">
             <div class="modal-header" style="background: var(--bg-tertiary); border-bottom: 1px solid var(--border-color);">
                 <h5 class="modal-title" id="warningAlertModalLabel" style="color: var(--text-primary);">
                     <i class="fas fa-exclamation-triangle text-warning me-2"></i>Warning
                 </h5>
                 <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
             </div>
             <div class="modal-body" style="color: var(--text-primary);">
                 <div class="text-center">
                     <i class="fas fa-exclamation-triangle text-warning fa-3x mb-3"></i>
                     <h6 id="warningAlertTitle">Warning!</h6>
                     <p class="mb-0" id="warningAlertMessage"></p>
                 </div>
             </div>
             <div class="modal-footer" style="border-top: 1px solid var(--border-color);">
                 <button type="button" class="btn btn-warning" data-bs-dismiss="modal">OK</button>
             </div>
         </div>
     </div>
 </div>

 <!-- Info Modal -->
 <div class="modal fade" id="infoAlertModal" tabindex="-1" aria-labelledby="infoAlertModalLabel" aria-hidden="true">
     <div class="modal-dialog modal-dialog-centered">
         <div class="modal-content" style="background: var(--card-bg); border: 1px solid var(--border-color);">
             <div class="modal-header" style="background: var(--bg-tertiary); border-bottom: 1px solid var(--border-color);">
                 <h5 class="modal-title" id="infoAlertModalLabel" style="color: var(--text-primary);">
                     <i class="fas fa-info-circle text-info me-2"></i>Information
                 </h5>
                 <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
             </div>
             <div class="modal-body" style="color: var(--text-primary);">
                 <div class="text-center">
                     <i class="fas fa-info-circle text-info fa-3x mb-3"></i>
                     <h6 id="infoAlertTitle">Information</h6>
                     <p class="mb-0" id="infoAlertMessage"></p>
                 </div>
             </div>
             <div class="modal-footer" style="border-top: 1px solid var(--border-color);">
                 <button type="button" class="btn btn-info" data-bs-dismiss="modal">OK</button>
             </div>
         </div>
     </div>
 </div>

 <!-- Danger Modal -->
 <div class="modal fade" id="dangerAlertModal" tabindex="-1" aria-labelledby="dangerAlertModalLabel" aria-hidden="true">
     <div class="modal-dialog modal-dialog-centered">
         <div class="modal-content" style="background: var(--card-bg); border: 1px solid var(--border-color);">
             <div class="modal-header" style="background: var(--bg-tertiary); border-bottom: 1px solid var(--border-color);">
                 <h5 class="modal-title" id="dangerAlertModalLabel" style="color: var(--text-primary);">
                     <i class="fas fa-times-circle text-danger me-2"></i>Error
                 </h5>
                 <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
             </div>
             <div class="modal-body" style="color: var(--text-primary);">
                 <div class="text-center">
                     <i class="fas fa-times-circle text-danger fa-3x mb-3"></i>
                     <h6 id="dangerAlertTitle">Error!</h6>
                     <p class="mb-0" id="dangerAlertMessage"></p>
                 </div>
             </div>
             <div class="modal-footer" style="border-top: 1px solid var(--border-color);">
                 <button type="button" class="btn btn-danger" data-bs-dismiss="modal">OK</button>
             </div>
         </div>
     </div>
 </div>

 <!-- Confirmation Modal -->
 <div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
     <div class="modal-dialog modal-dialog-centered">
         <div class="modal-content" style="background: var(--card-bg); border: 1px solid var(--border-color);">
             <div class="modal-header" style="background: var(--bg-tertiary); border-bottom: 1px solid var(--border-color);">
                 <h5 class="modal-title" id="confirmModalLabel" style="color: var(--text-primary);">
                     <i class="fas fa-question-circle text-primary me-2"></i>Confirm Action
                 </h5>
                 <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
             </div>
             <div class="modal-body" style="color: var(--text-primary);">
                 <div class="text-center">
                     <i class="fas fa-question-circle text-primary fa-3x mb-3"></i>
                     <h6 id="confirmModalTitle">Confirm Action</h6>
                     <p class="mb-0" id="confirmModalMessage"></p>
                 </div>
             </div>
             <div class="modal-footer" style="border-top: 1px solid var(--border-color);">
                 <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                 <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="confirmModalCancelBtn">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmModalYesBtn">Yes, Continue</button>
             </div>
         </div>
     </div>
 </div>

<!-- Dynamic Conversion Modal -->
@if($isEligibleForDynamicConversions)
<div class="modal fade" id="dynamicConversionModal" tabindex="-1" aria-labelledby="dynamicConversionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="background: var(--card-bg); border: 1px solid var(--border-color);">
            <div class="modal-header" style="background: var(--bg-tertiary); border-bottom: 1px solid var(--border-color);">
                <h5 class="modal-title" id="dynamicConversionModalLabel" style="color: var(--text-primary);">
                    <i class="fas fa-exchange-alt me-2"></i>Dynamic Unit Conversion
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <h6 id="conversionProductName" style="color: var(--text-primary);"></h6>
                    <p class="text-muted" id="conversionProductInfo"></p>
                </div>
                
                <form id="posConversionForm">
                    @csrf
                    <input type="hidden" id="conversionProductId" name="product_id">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="posSellUnit" class="form-label" style="color: var(--text-primary);">Sell In *</label>
                                <select id="posSellUnit" name="sell_unit" class="form-select" required 
                                        style="background: var(--bg-secondary); border: 1px solid var(--border-color); color: #000;">
                                    <option value="" style="color: #000;">Select unit to sell in</option>
                                    <option value="kg" style="color: #000;">Kilograms (kg)</option>
                                    <option value="sqm" style="color: #000;">Square Meters (sqm)</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="posMaterialType" class="form-label" style="color: var(--text-primary);">Material Type *</label>
                                <select id="posMaterialType" name="material_type" class="form-select" required 
                                        style="background: var(--bg-secondary); border: 1px solid var(--border-color); color: #000;">
                                    <option value="" style="color: #000;">Select material type</option>
                                    <option value="greenhouse_0.2" style="color: #000;">Greenhouse Film (0.2 microns)</option>
                                    <option value="damliner_0.3" style="color: #000;">Dam Liner (0.3 microns)</option>
                                    <option value="damliner_0.5" style="color: #000;">Dam Liner (0.5 microns)</option>
                                    <option value="damliner_0.75" style="color: #000;">Dam Liner (0.75 microns)</option>
                                    <option value="damliner_1.0" style="color: #000;">Dam Liner (1.0 microns)</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="posQuantity" class="form-label" style="color: var(--text-primary);">Quantity *</label>
                                <input type="number" step="0.01" min="0.01" id="posQuantity" name="quantity" 
                                       class="form-control" placeholder="Enter quantity" required 
                                       style="background: var(--bg-secondary); border: 1px solid var(--border-color); color: var(--text-primary);">
                                <small class="form-text text-muted" id="quantityHelp">Enter quantity in the selected unit</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="posPricePerUnit" class="form-label" style="color: var(--text-primary);">Price Per Unit (KSh) *</label>
                                <input type="number" step="0.01" min="0.01" id="posPricePerUnit" name="price_per_unit" 
                                       class="form-control" placeholder="Enter price per unit" required 
                                       style="background: var(--bg-secondary); border: 1px solid var(--border-color); color: var(--text-primary);">
                                <small class="form-text text-muted" id="priceHelp">Price per kg or per sqm</small>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Auto-calculation results -->
                    <div id="posAutoCalculation" class="mt-3" style="display: none;">
                        <div class="alert alert-info" style="background: var(--bg-tertiary); border: 1px solid var(--border-color);">
                            <h6 class="alert-heading">Conversion Results</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>Original Quantity:</strong> <span id="posOriginalQuantity"></span></p>
                                    <p class="mb-1"><strong>Converted Quantity:</strong> <span id="posConvertedQuantity"></span></p>
                                    <p class="mb-1"><strong>Conversion Factor:</strong> <span id="posConversionFactor"></span></p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>Total Amount:</strong> <span id="posTotalAmount"></span></p>
                                    <p class="mb-1"><strong>Material Type:</strong> <span id="posMaterialTypeDisplay"></span></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>

                <!-- Results Section -->
                <div id="posConversionResults" class="mt-4" style="display: none;">
                    <hr>
                    <h5 class="text-primary mb-3">Conversion Results</h5>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card border-primary" style="background: var(--card-bg); border: 1px solid var(--primary-color);">
                                <div class="card-body">
                                    <h6 class="text-primary">Conversion Details</h6>
                                    <p class="mb-1"><strong>Formula:</strong> <span id="posFormula"></span></p>
                                    <p class="mb-1"><strong>Converted Quantity:</strong> <span id="posConvertedQuantity"></span></p>
                                    <p class="mb-1"><strong>Conversion Factor:</strong> <span id="posConversionFactor"></span></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border-success" style="background: var(--card-bg); border: 1px solid var(--success-color);">
                                <div class="card-body">
                                    <h6 class="text-success">Pricing Information</h6>
                                    <p class="mb-1"><strong>Suggested Sale Price:</strong> <span id="posSuggestedPrice"></span></p>
                                    <p class="mb-1"><strong>Total Amount:</strong> <span id="posTotalAmount"></span></p>
                                    <p class="mb-1"><strong>Profit Margin:</strong> <span id="posProfitMarginResult"></span></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="background: var(--bg-tertiary); border-top: 1px solid var(--border-color);">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" id="addConvertedToCart">
                    <i class="fas fa-cart-plus me-2"></i>Add to Cart
                </button>
            </div>
        </div>
    </div>
</div>
@endif

<style>
@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}

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
}

/* Ensure dropdown options are visible */
.form-select option {
    color: #000 !important;
    background-color: #fff !important;
}

.form-select {
    color: #000 !important;
}

/* Modal specific styles for better visibility */
#dynamicConversionModal .form-select option {
    color: #000 !important;
    background-color: #fff !important;
}

#dynamicConversionModal .form-select {
    color: #000 !important;
}

/* Custom Success Modal Styles */
.success-icon-container {
    display: flex;
    justify-content: center;
    align-items: center;
}

.success-icon {
    width: 80px;
    height: 80px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    animation: successPulse 2s infinite;
}

.success-icon i {
    font-size: 2.5rem;
    color: white;
}

@keyframes successPulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.1); }
    100% { transform: scale(1); }
}

.order-number-display {
    background: linear-gradient(135deg, #f8f9fa, #e9ecef);
    border-radius: 10px;
    padding: 1rem;
    border: 2px solid #dee2e6;
}

.order-label {
    font-size: 0.9rem;
    color: var(--text-muted);
    font-weight: 500;
}

.order-value {
    font-size: 1.2rem;
    font-weight: bold;
    color: var(--primary-color);
    margin-left: 0.5rem;
}

.success-message {
    background: rgba(40, 167, 69, 0.1);
    border: 1px solid rgba(40, 167, 69, 0.2);
    border-radius: 8px;
    padding: 1rem;
}

.action-buttons .btn {
    border-radius: 8px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.action-buttons .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
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

/* Custom Modal Styles */
.modal-content {
    border-radius: 0.5rem;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
}

.modal-header {
    border-radius: 0.5rem 0.5rem 0 0;
}

.modal-footer {
    border-radius: 0 0 0.5rem 0.5rem;
}

.modal-body {
    padding: 2rem 1.5rem;
}

.modal-body .fa-3x {
    margin-bottom: 1.5rem;
}

.modal-body h6 {
    margin-bottom: 1rem;
    font-weight: 600;
}

.modal-body p {
    font-size: 1rem;
    line-height: 1.5;
}

/* Modal animation */
.modal.fade .modal-dialog {
    transition: transform 0.3s ease-out;
    transform: translate(0, -50px);
}

.modal.show .modal-dialog {
    transform: none;
}

/* Button styles in modals */
.modal-footer .btn {
    padding: 0.5rem 1.5rem;
    font-weight: 500;
    border-radius: 0.375rem;
}

.modal-footer .btn-success {
    background-color: var(--success-color);
    border-color: var(--success-color);
}

.modal-footer .btn-warning {
    background-color: var(--warning-color);
    border-color: var(--warning-color);
    color: #000;
}

.modal-footer .btn-info {
    background-color: var(--info-color);
    border-color: var(--info-color);
    color: #fff;
}

.modal-footer .btn-danger {
    background-color: var(--danger-color);
    border-color: var(--danger-color);
}

.modal-footer .btn-primary {
    background-color: var(--primary-color);
    border-color: var(--primary-color);
}

/* Cart item styling for kg equivalents */
.cart-item .text-info {
    font-size: 0.8rem;
    font-style: italic;
}

.cart-item .quantity-control .fw-bold {
    font-size: 0.9rem;
}

/* Highlight converted items */
.cart-item.has-conversion {
    border-left: 3px solid var(--primary-color);
    background-color: rgba(var(--primary-color-rgb), 0.05);
}
</style>

<script>
// Dynamic Conversion Configuration - Set this early
window.isEligibleForDynamicConversions = {{ $isEligibleForDynamicConversions ? 'true' : 'false' }};
window.posConfig = {
    csrfToken: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
    currencySymbol: 'KSh ',
    businessName: '{{ $business->name ?? "Shopybook Business" }}',
    paystackEnabled: {{ $paystackCfg->paystack_enabled ? 'true' : 'false' }},
    paystackMpesaChargeUrl: '{{ route("payment.paystack.mpesa.charge") }}',
    paystackStatusUrl: '{{ url("/payment/paystack/status") }}',
    routes: {
        createOrder: '{{ route("sales.create-order") }}',
        storeCustomer: '{{ route("sales.store-customer") }}',
        calculateDynamicConversion: '{{ route("sales.calculate-dynamic-conversion") }}'
    }
};

// Handle receipt printing
document.addEventListener('DOMContentLoaded', function() {
    const printReceiptBtn = document.getElementById('printReceiptBtn');
    if (printReceiptBtn) {
        printReceiptBtn.addEventListener('click', function() {
            if (window.lastOrderData && window.lastOrderData.order_id) {
                // Open receipt in new window for printing
                const receiptUrl = `/sales/orders/${window.lastOrderData.order_id}/receipt`;
                window.open(receiptUrl, '_blank', 'width=400,height=600');
            } else {
                console.error('No order data available for receipt printing');
            }
        });
    }
    
    // Handle invoice generation
    const generateInvoiceBtn = document.getElementById('generateInvoiceBtn');
    if (generateInvoiceBtn) {
        generateInvoiceBtn.addEventListener('click', function() {
            if (window.lastOrderData && window.lastOrderData.order_id) {
                // Open invoice in new window for printing/download
                const invoiceUrl = `/sales/orders/${window.lastOrderData.order_id}/invoice`;
                window.open(invoiceUrl, '_blank', 'width=800,height=800');
            } else {
                console.error('No order data available for invoice generation');
            }
        });
    }
    
    // Update partial payment display when amount changes
    const partialAmountInput = document.getElementById('partial_amount');
    if (partialAmountInput) {
        partialAmountInput.addEventListener('input', updatePartialPaymentDisplay);
    }
});

function togglePartialPayment() {
    const paymentStatus = document.getElementById('payment_status').value;
    const partialRow = document.getElementById('partial_payment_row');
    
    if (paymentStatus === 'partial') {
        partialRow.style.display = 'block';
        updatePartialPaymentDisplay();
    } else {
        partialRow.style.display = 'none';
    }
}

function updatePartialPaymentDisplay() {
    // This function will be called from pos.js when cart total changes
    // We'll set it up to update the display
    if (window.updatePartialPaymentTotals) {
        window.updatePartialPaymentTotals();
    }
}
</script>

<!-- Load our custom POS JavaScript -->
<script src="{{ asset('js/pos.js') }}?v={{ time() }}"></script>

@if($paystackCfg->paystack_enabled)
<!-- ============================================================
     Paystack M-Pesa STK Push — Modal
============================================================ -->
<div class="modal fade" id="mpesaStkModal" tabindex="-1" data-bs-backdrop="static" aria-labelledby="mpesaStkModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="mpesaStkModalLabel">
                    <i class="fas fa-mobile-alt me-2 text-success"></i>M-Pesa STK Push
                </h5>
            </div>
            <div class="modal-body text-center py-4">
                <div id="stk_waiting">
                    <div class="spinner-border text-success mb-3" role="status"></div>
                    <h6 id="stk_status_text">Sending STK push to customer's phone&hellip;</h6>
                    <p class="text-muted small mb-0" id="stk_amount_text"></p>
                    <p class="text-muted small">Ask the customer to enter their M-Pesa PIN.</p>
                    <p class="text-muted small">Checking status in <span id="stk_countdown">5</span>s&hellip;</p>
                </div>
                <div id="stk_success" style="display:none;">
                    <i class="fas fa-check-circle fa-4x text-success mb-3"></i>
                    <h5 class="text-success">Payment Received!</h5>
                    <p class="text-muted small" id="stk_success_text">Completing the sale&hellip;</p>
                </div>
                <div id="stk_failed" style="display:none;">
                    <i class="fas fa-times-circle fa-4x text-danger mb-3"></i>
                    <h5 class="text-danger">Payment Failed</h5>
                    <p class="text-muted small" id="stk_failed_text">The customer did not complete the payment.</p>
                </div>
            </div>
            <div class="modal-footer justify-content-center" id="stk_modal_footer">
                <button type="button" class="btn btn-outline-secondary btn-sm" id="stk_cancel_btn" onclick="cancelStkPush()">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    'use strict';

    const cfg = window.posConfig;
    let stkReference   = null;
    let pollInterval   = null;
    let pollCount      = 0;
    const MAX_POLLS    = 18; // 18 × 5 s = 90 s timeout
    const POLL_EVERY   = 5000; // ms

    // ── Show phone row and swap buttons when M-Pesa is selected ──────────
    (function wirePaymentMethodChange() {
        const methodSel   = document.getElementById('payment_method');
        const phoneRow    = document.getElementById('mpesa_phone_row');
        const stkBtn      = document.getElementById('mpesa_stk_btn');
        const checkoutBtn = document.getElementById('checkout_btn');
        const stkCheckbox = document.getElementById('use_stk_push');
        const stkPhoneArea = document.getElementById('stk_phone_area');

        if (!methodSel) return;

        function toggleMpesaUI() {
            const isMpesa  = methodSel.value === 'mpesa';
            const useStk   = isMpesa && stkCheckbox && stkCheckbox.checked;

            // Show/hide the phone+toggle row
            if (phoneRow) phoneRow.style.display = isMpesa ? 'block' : 'none';

            // Show/hide the phone input area (only needed for STK)
            if (stkPhoneArea) stkPhoneArea.style.display = useStk ? 'block' : 'none';

            // Swap STK button vs Complete Sale button
            if (stkBtn)      stkBtn.style.display      = useStk ? 'block' : 'none';
            if (checkoutBtn) checkoutBtn.style.display = useStk ? 'none'  : 'block';
        }

        methodSel.addEventListener('change', toggleMpesaUI);
        if (stkCheckbox) stkCheckbox.addEventListener('change', toggleMpesaUI);
        toggleMpesaUI(); // run once on load
    }());

    // Keep STK button's disabled state in sync with the checkout button
    const observer = new MutationObserver(function () {
        const checkoutBtn = document.getElementById('checkout_btn');
        const stkBtn      = document.getElementById('mpesa_stk_btn');
        if (checkoutBtn && stkBtn) {
            stkBtn.disabled = checkoutBtn.disabled;
        }
    });
    const checkoutBtn = document.getElementById('checkout_btn');
    if (checkoutBtn) {
        observer.observe(checkoutBtn, { attributes: true, attributeFilter: ['disabled'] });
    }

    // ── Main STK push trigger ─────────────────────────────────────────────
    const stkBtn = document.getElementById('mpesa_stk_btn');
    if (stkBtn) {
        stkBtn.addEventListener('click', initiateStk);
    }

    function initiateStk() {
        const phone = (document.getElementById('mpesa_phone') || {}).value || '';
        if (!phone.trim()) {
            alert('Please enter the customer\'s M-Pesa phone number.');
            return;
        }

        // Use partial payment amount if entered, otherwise use cart total
        const paymentStatus = (document.getElementById('payment_status') || {}).value || 'paid';
        const partialInput  = document.getElementById('partial_amount');
        let amount;
        if (paymentStatus === 'partial' && partialInput && partialInput.value) {
            amount = parseFloat(partialInput.value) || 0;
        } else {
            const totalEl  = document.getElementById('total');
            const totalTxt = totalEl ? totalEl.textContent.replace(/[^0-9.]/g, '') : '0';
            amount = parseFloat(totalTxt) || 0;
        }
        if (amount <= 0) { alert('Cart is empty or partial amount is missing.'); return; }

        showStkModal('Sending STK push to ' + phone + '…', amount);

        fetch(cfg.paystackMpesaChargeUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': cfg.csrfToken,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ phone: phone, amount: amount }),
        })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            if (data.success) {
                stkReference = data.reference;
                document.getElementById('stk_status_text').textContent = data.message || 'Waiting for customer to approve…';
                startPolling();
            } else {
                showStkFailed(data.error || 'Could not send STK push.');
            }
        })
        .catch(function (err) {
            showStkFailed('Network error: ' + err.message);
        });
    }

    function startPolling() {
        pollCount = 0;
        let countdown = Math.floor(POLL_EVERY / 1000);
        const countdownEl = document.getElementById('stk_countdown');

        const countdownTimer = setInterval(function () {
            countdown--;
            if (countdownEl) countdownEl.textContent = countdown;
            if (countdown <= 0) {
                countdown = Math.floor(POLL_EVERY / 1000);
            }
        }, 1000);

        pollInterval = setInterval(function () {
            pollCount++;
            if (pollCount > MAX_POLLS) {
                clearInterval(pollInterval);
                clearInterval(countdownTimer);
                showStkFailed('Payment timed out. The customer did not respond in time.');
                return;
            }

            fetch(cfg.paystackStatusUrl + '/' + stkReference, {
                headers: {
                    'X-CSRF-TOKEN': cfg.csrfToken,
                    'Accept': 'application/json',
                },
            })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                const status = (data.status || '').toLowerCase();
                if (status === 'success') {
                    clearInterval(pollInterval);
                    clearInterval(countdownTimer);
                    onPaymentSuccess(data);
                } else if (status === 'failed') {
                    clearInterval(pollInterval);
                    clearInterval(countdownTimer);
                    showStkFailed('Customer declined or payment failed.');
                }
                // 'pay_offline' / 'pending' → keep polling
            })
            .catch(function () { /* network hiccup — retry next poll */ });
        }, POLL_EVERY);
    }

    function onPaymentSuccess(data) {
        document.getElementById('stk_waiting').style.display = 'none';
        document.getElementById('stk_failed').style.display  = 'none';
        document.getElementById('stk_success').style.display = 'block';
        document.getElementById('stk_success_text').textContent =
            'KSh ' + (data.amount || '') + ' received. Completing the sale…';
        document.getElementById('stk_cancel_btn').style.display = 'none';

        // Let pos.js complete the sale normally (it reads payment_method from #payment_method)
        setTimeout(function () {
            hideStkModal();
            const checkoutBtn = document.getElementById('checkout_btn');
            if (checkoutBtn) {
                // Temporarily show it and click it so pos.js creates the order
                checkoutBtn.style.display = 'block';
                checkoutBtn.click();
                checkoutBtn.style.display = 'none';
            }
        }, 1500);
    }

    // ── Modal helpers ─────────────────────────────────────────────────────
    function showStkModal(statusText, amount) {
        document.getElementById('stk_waiting').style.display = 'block';
        document.getElementById('stk_success').style.display = 'none';
        document.getElementById('stk_failed').style.display  = 'none';
        document.getElementById('stk_cancel_btn').style.display = 'inline-block';
        document.getElementById('stk_status_text').textContent = statusText;
        document.getElementById('stk_amount_text').textContent = 'Amount: KSh ' + amount.toFixed(2);
        document.getElementById('stk_countdown').textContent = '5';
        const modal = new bootstrap.Modal(document.getElementById('mpesaStkModal'));
        modal.show();
    }

    function hideStkModal() {
        const modalEl = document.getElementById('mpesaStkModal');
        const modal   = bootstrap.Modal.getInstance(modalEl);
        if (modal) modal.hide();
    }

    function showStkFailed(msg) {
        document.getElementById('stk_waiting').style.display = 'none';
        document.getElementById('stk_success').style.display = 'none';
        document.getElementById('stk_failed').style.display  = 'block';
        document.getElementById('stk_failed_text').textContent = msg;
        document.getElementById('stk_cancel_btn').textContent = 'Close';
    }

    window.cancelStkPush = function () {
        if (pollInterval) clearInterval(pollInterval);
        hideStkModal();
    };
}());
</script>
@endif

@endsection