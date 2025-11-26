@extends('layouts.dash')

@section('content')

<div class="container-fluid">
    <!-- Sub-navigation for Sales -->
    <div class="sub-navigation mb-4">
        <div class="nav-tabs">
            <a href="{{ route('sales.customers') }}" class="nav-tab">
                <i class="fas fa-users me-1"></i> Customers
            </a>
            <a href="{{ route('sales.orders') }}" class="nav-tab active">
                <i class="fas fa-shopping-cart me-1"></i> Orders
            </a>
            <a href="{{ route('sales.pos') }}" class="nav-tab">
                <i class="fas fa-cash-register me-1"></i> POS
            </a>
            <a href="{{ route('sales.customer-debts') }}" class="nav-tab">
                <i class="fas fa-money-bill-wave me-1"></i> Customer Debts
            </a>
            <a href="{{ route('sales.supplier-debts') }}" class="nav-tab">
                <i class="fas fa-file-invoice-dollar me-1"></i> Supplier Debts
            </a>
        </div>
    </div>

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 mb-3 mb-sm-0" style="color: var(--text-primary);">Orders</h1>
        <div class="d-flex gap-2 flex-wrap">
            <button type="button" class="btn btn-secondary" onclick="bulkArchivePaidOrders()">
                <i class="fas fa-archive me-1"></i><span class="btn-text">Archive Paid Orders</span>
            </button>
            <a href="{{ route('sales.archived-orders') }}" class="btn btn-info">
                <i class="fas fa-folder-open me-1"></i><span class="btn-text">View Archived</span>
            </a>
        </div>
    </div>

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div class="filter-buttons-container">
            <button type="button" class="btn btn-outline-primary filter-btn active" onclick="filterOrders('all')">
                <i class="fas fa-list me-1"></i><span class="btn-text">All Orders</span>
            </button>
            <button type="button" class="btn btn-outline-warning filter-btn" onclick="filterOrders('pending')">
                <i class="fas fa-clock me-1"></i><span class="btn-text">Pending</span>
            </button>
            <button type="button" class="btn btn-outline-info filter-btn" onclick="filterOrders('processing')">
                <i class="fas fa-spinner me-1"></i><span class="btn-text">Processing</span>
            </button>
            <button type="button" class="btn btn-outline-success filter-btn" onclick="filterOrders('completed')">
                <i class="fas fa-check me-1"></i><span class="btn-text">Completed</span>
            </button>
            <button type="button" class="btn btn-outline-danger filter-btn" onclick="filterOrders('cancelled')">
                <i class="fas fa-times me-1"></i><span class="btn-text">Cancelled</span>
            </button>
        </div>
    </div>

    <div class="card shadow mb-4" style="background: var(--card-bg); border: 1px solid var(--border-color);">
        <div class="card-header py-3" style="background: var(--bg-tertiary); border-bottom: 1px solid var(--border-color);">
            <h6 class="m-0 font-weight-bold" style="color: var(--text-primary);">Order Management</h6>
        </div>
        <div class="card-body">
            @if($orders->count() > 0)
                <!-- Desktop Table View -->
                <div class="table-responsive d-none d-lg-block">
                    <table class="table table-bordered" id="ordersTable" width="100%" cellspacing="0">
                        <thead style="background-color: var(--bg-tertiary);">
                            <tr>
                                <th style="color: var(--text-secondary); border-bottom: 2px solid var(--border-color);">Order #</th>
                                <th style="color: var(--text-secondary); border-bottom: 2px solid var(--border-color);">Type</th>
                                <th style="color: var(--text-secondary); border-bottom: 2px solid var(--border-color);">Customer</th>
                                <th style="color: var(--text-secondary); border-bottom: 2px solid var(--border-color);">Products</th>
                                <th style="color: var(--text-secondary); border-bottom: 2px solid var(--border-color);">Total</th>
                                
                                <th style="color: var(--text-secondary); border-bottom: 2px solid var(--border-color);">Date</th>
                                <th style="color: var(--text-secondary); border-bottom: 2px solid var(--border-color);">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($orders as $order)
                                <tr data-status="{{ $order->status }}" 
                                    data-type="{{ $order->order_type }}" 
                                    data-order-id="{{ $order->id }}"
                                    data-payment-status="{{ $order->payment_status ?? 'pending' }}"
                                    data-has-invoice="{{ $order->invoice_number ? 'true' : 'false' }}"
                                    data-is-archived="{{ $order->is_archived ? 'true' : 'false' }}"
                                    style="color: var(--text-primary);">
                                    <td>
                                        <strong>{{ $order->order_number ?? 'ORD-' . $order->id }}</strong>
                                        @if($order->order_type === 'public_order')
                                            <span class="badge badge-info">Public</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($order->order_type === 'public_order')
                                            <span style="color: var(--info-color);">Public Order</span>
                                        @else
                                            <span style="color: var(--primary-color);">Internal</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($order->order_type === 'public_order')
                                            <div>
                                                <strong>{{ $order->customer_name }}</strong><br>
                                                <small style="color: var(--text-muted);">{{ $order->customer_phone }}</small><br>
                                                <small style="color: var(--text-muted);">{{ $order->customer_email }}</small>
                                            </div>
                                        @else
                                            {{ $order->customer ? $order->customer->name : 'Walk-in Customer' }}
                                        @endif
                                    </td>
                                    <td>
                                        @if($order->order_type === 'public_order')
                                            <div>
                                                <strong>{{ $order->product ? $order->product->name : 'Product' }}</strong><br>
                                                <small style="color: var(--text-muted);">Qty: {{ $order->quantity }}</small>
                                            </div>
                                        @else
                                            @foreach($order->items as $item)
                                                <div>
                                                    <strong>{{ $item->product->name }}</strong><br>
                                                    <small style="color: var(--text-muted);">Qty: {{ $item->quantity }}</small>
                                                </div>
                                            @endforeach
                                        @endif
                                    </td>
                                    <td>
                                        @if($order->order_type === 'public_order')
                                            <strong>KSh {{ number_format($order->total_price, 2) }}</strong>
                                        @else
                                            <strong>{{ $order->formatted_total }}</strong>
                                        @endif
                                    </td>
                                    
                                    <td>
                                        {{ $order->created_at->format('M d, Y H:i') }}
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-sm btn-outline-info" onclick="viewOrderDetails({{ $order->id }})">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            @if($order->payment_status === 'pending' || !$order->payment_status)
                                            <a href="{{ route('sales.generate-invoice', $order->id) }}" target="_blank" class="btn btn-sm btn-outline-warning" title="Generate Invoice">
                                                <i class="fas fa-file-invoice"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-success" onclick="recordPayment({{ $order->id }}, {{ $order->total_amount }})" title="Record Payment">
                                                <i class="fas fa-money-bill-wave"></i>
                                            </button>
                                            @endif
                                            @if($order->invoice_number)
                                            <a href="{{ route('sales.credit-note.create', $order->id) }}" class="btn btn-sm btn-outline-danger" title="Credit Note">
                                                <i class="fas fa-file-invoice-dollar"></i>
                                            </a>
                                            @endif
                                            @if($order->payment_status === 'paid' && $order->invoice_number && !$order->is_archived)
                                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="archiveOrder({{ $order->id }})" title="Archive Order">
                                                <i class="fas fa-archive"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Card View -->
                <div class="d-lg-none" id="ordersCardView">
                    @foreach($orders as $order)
                        <div class="order-card" data-status="{{ $order->status }}" data-type="{{ $order->order_type }}">
                            <div class="order-card-header">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <h6 class="mb-1" style="color: var(--text-primary);">
                                            <strong>{{ $order->order_number ?? 'ORD-' . $order->id }}</strong>
                                            @if($order->order_type === 'public_order')
                                                <span class="badge badge-info ms-1">Public</span>
                                            @endif
                                        </h6>
                                        <small style="color: var(--text-muted);">
                                            <i class="fas fa-calendar me-1"></i>
                                            {{ $order->created_at->format('M d, Y H:i') }}
                                        </small>
                                    </div>
                                    <span class="badge badge-{{ $order->status_color }}">
                                        {{ $order->status_text }}
                                    </span>
                                </div>
                            </div>
                            <div class="order-card-body">
                                <div class="order-info-row">
                                    <span class="order-label"><i class="fas fa-tag me-1"></i>Type</span>
                                    <span class="order-value">
                                        @if($order->order_type === 'public_order')
                                            <span style="color: var(--info-color); font-weight: 500;">Public Order</span>
                                        @else
                                            <span style="color: var(--primary-color); font-weight: 500;">Internal</span>
                                        @endif
                                    </span>
                                </div>
                                <div class="order-info-row">
                                    <span class="order-label"><i class="fas fa-user me-1"></i>Customer</span>
                                    <span class="order-value">
                                        @if($order->order_type === 'public_order')
                                            <strong>{{ $order->customer_name }}</strong><br>
                                            <small style="color: var(--text-muted);">{{ $order->customer_phone }}</small>
                                        @else
                                            <strong>{{ $order->customer ? $order->customer->name : 'Walk-in Customer' }}</strong>
                                        @endif
                                    </span>
                                </div>
                                <div class="order-info-row">
                                    <span class="order-label"><i class="fas fa-box me-1"></i>Products</span>
                                    <span class="order-value">
                                        @if($order->order_type === 'public_order')
                                            <strong>{{ $order->product ? $order->product->name : 'Product' }}</strong><br>
                                            <small style="color: var(--text-muted);">Qty: {{ $order->quantity }}</small>
                                        @else
                                            @foreach($order->items as $item)
                                                <div class="mb-1">
                                                    <strong>{{ $item->product->name }}</strong><br>
                                                    <small style="color: var(--text-muted);">Qty: {{ $item->quantity }}</small>
                                                </div>
                                            @endforeach
                                        @endif
                                    </span>
                                </div>
                                <div class="order-info-row">
                                    <span class="order-label"><i class="fas fa-money-bill me-1"></i>Total</span>
                                    <span class="order-value">
                                        @if($order->order_type === 'public_order')
                                            <strong style="color: var(--success-color); font-size: 1rem;">KSh {{ number_format($order->total_price, 2) }}</strong>
                                        @else
                                            <strong style="color: var(--success-color); font-size: 1rem;">{{ $order->formatted_total }}</strong>
                                        @endif
                                    </span>
                                </div>
                                <div class="order-info-row">
                                    <span class="order-label"><i class="fas fa-credit-card me-1"></i>Payment</span>
                                    <span class="order-value">
                                        @if($order->order_type === 'public_order')
                                            <span class="badge badge-{{ $order->payment_status === 'paid' ? 'success' : ($order->payment_status === 'pending' ? 'warning' : 'danger') }}">
                                                {{ ucfirst($order->payment_status) }}
                                            </span>
                                        @else
                                            <span class="badge badge-{{ $order->payment_method ? 'info' : 'secondary' }}">
                                                {{ $order->payment_method ? ucfirst($order->payment_method) : 'Not Set' }}
                                            </span>
                                        @endif
                                    </span>
                                </div>
                            </div>
                            <div class="order-card-footer">
                                <button type="button" class="btn btn-sm btn-outline-info w-100" onclick="viewOrderDetails({{ $order->id }})">
                                    <i class="fas fa-eye me-1"></i> View Details
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-shopping-cart fa-3x" style="color: var(--text-muted);" class="mb-3"></i>
                    <h5 style="color: var(--text-primary);">No Orders Found</h5>
                    <p style="color: var(--text-muted);">No orders have been created yet.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Order Details Modal -->
<div class="modal fade" id="orderDetailsModal" tabindex="-1" aria-labelledby="orderDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="background: var(--card-bg); border: 1px solid var(--border-color);">
            <div class="modal-header" style="background: var(--bg-tertiary); border-bottom: 1px solid var(--border-color);">
                <h5 class="modal-title" id="orderDetailsModalLabel" style="color: var(--text-primary);">Order Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="orderDetailsContent">
                <!-- Order details will be loaded here -->
            </div>
            <div class="modal-footer" style="border-top: 1px solid var(--border-color);">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Status Update Confirmation Modal -->
<div class="modal fade" id="statusUpdateModal" tabindex="-1" aria-labelledby="statusUpdateModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background: var(--card-bg); border: 1px solid var(--border-color);">
            <div class="modal-header" style="background: var(--bg-tertiary); border-bottom: 1px solid var(--border-color);">
                <h5 class="modal-title" id="statusUpdateModalLabel" style="color: var(--text-primary);">
                    <i class="fas fa-edit me-2"></i>Update Order Status
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="color: var(--text-primary);">
                <div class="text-center">
                    <i class="fas fa-question-circle text-info fa-3x mb-3"></i>
                    <h6>Update Order Status</h6>
                    <p class="mb-2">Current Status: <strong id="currentStatus"></strong></p>
                    <p class="mb-3">New Status: <strong id="newStatus"></strong></p>
                    
                                         <div class="form-group">
                         <label for="userPassword" class="form-label">Your Password</label>
                         <input type="password" class="form-control" id="userPassword" 
                                placeholder="Enter your password" required
                                style="border: 1px solid var(--border-color); background: var(--card-bg); color: var(--text-primary);">
                         <small class="text-muted">Enter your account password to confirm this action.</small>
                     </div>
                </div>
            </div>
            <div class="modal-footer" style="border-top: 1px solid var(--border-color);">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmStatusUpdate">
                    <i class="fas fa-check me-2"></i>Update Status
                </button>
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
                    <i class="fas fa-check-circle text-success me-2"></i>Status Updated!
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="color: var(--text-primary);">
                <div class="text-center">
                    <i class="fas fa-check-circle text-success fa-3x mb-3"></i>
                    <h6>Order status updated successfully!</h6>
                    <p class="mb-0">The order status has been changed to <strong id="updatedStatus"></strong></p>
                </div>
            </div>
            <div class="modal-footer" style="border-top: 1px solid var(--border-color);">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal" onclick="location.reload()">
                    <i class="fas fa-refresh me-2"></i>Refresh Page
                </button>
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
                    <h6>Failed to update order status!</h6>
                    <p class="mb-0" id="errorMessage"></p>
                </div>
            </div>
            <div class="modal-footer" style="border-top: 1px solid var(--border-color);">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Record Payment Modal -->
<div class="modal fade" id="recordPaymentModal" tabindex="-1" aria-labelledby="recordPaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background: var(--card-bg); border: 1px solid var(--border-color);">
            <div class="modal-header" style="background: var(--bg-tertiary); border-bottom: 1px solid var(--border-color);">
                <h5 class="modal-title" id="recordPaymentModalLabel" style="color: var(--text-primary);">
                    <i class="fas fa-money-bill-wave text-success me-2"></i>Record Payment
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="color: var(--text-primary);">
                <form id="recordPaymentForm">
                    <input type="hidden" id="paymentOrderId">
                    
                    <div class="alert alert-info">
                        <strong>Invoice Amount:</strong> KSh <span id="invoiceAmount">0.00</span>
                    </div>

                    <div class="mb-3">
                        <label for="paymentMethod" class="form-label">Payment Method <span class="text-danger">*</span></label>
                        <select class="form-select" id="paymentMethod" name="payment_method" required>
                            <option value="">Select payment method</option>
                            <option value="cash">Cash</option>
                            <option value="mpesa">M-Pesa</option>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="card">Card</option>
                            <option value="cheque">Cheque</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="amountPaid" class="form-label">Amount Paid <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">KSh</span>
                            <input type="number" class="form-control" id="amountPaid" name="amount_paid" step="0.01" min="0" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="transactionReference" class="form-label">Transaction Reference (Optional)</label>
                        <input type="text" class="form-control" id="transactionReference" name="transaction_reference" placeholder="e.g., M-Pesa code, cheque number">
                    </div>

                    <div class="mb-3">
                        <label for="paymentNotes" class="form-label">Notes (Optional)</label>
                        <textarea class="form-control" id="paymentNotes" name="notes" rows="2" placeholder="Any additional notes about this payment"></textarea>
                    </div>

                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <small>Recording this payment will mark the invoice as PAID.</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer" style="border-top: 1px solid var(--border-color);">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" id="confirmPaymentBtn">
                    <i class="fas fa-check me-2"></i>Record Payment
                </button>
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

.table th {
    background-color: var(--bg-tertiary);
    border-bottom: 2px solid var(--border-color);
    color: var(--text-secondary);
}

.table td {
    color: var(--text-primary);
    border-color: var(--border-color);
}

.modal-content {
    box-shadow: 0 10px 30px var(--shadow-color);
}

/* Filter Buttons */
.filter-buttons-container {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.filter-btn {
    display: inline-flex;
    align-items: center;
    padding: 0.5rem 1rem;
    font-size: 0.875rem;
    font-weight: 500;
    border-radius: 0.375rem;
    transition: all 0.2s ease;
}

.filter-btn.active {
    background-color: var(--primary-color) !important;
    border-color: var(--primary-color) !important;
    color: white !important;
}

/* Mobile Order Cards */
.order-card {
    background: var(--card-bg);
    border: 1px solid var(--border-color);
    border-radius: 0.5rem;
    overflow: hidden;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
    margin-bottom: 1rem;
}

.order-card:hover {
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    transform: translateY(-2px);
}

.order-card-header {
    padding: 1rem;
    background: var(--bg-tertiary);
    border-bottom: 1px solid var(--border-color);
}

.order-card-body {
    padding: 1rem;
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 0.75rem;
}

.order-card-footer {
    padding: 0.75rem 1rem;
    background: var(--bg-tertiary);
    border-top: 1px solid var(--border-color);
}

.order-info-row {
    display: flex;
    flex-direction: column;
    padding: 0.5rem;
    background: var(--bg-tertiary);
    border-radius: 0.375rem;
}

.order-label {
    font-weight: 600;
    color: var(--text-secondary);
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 0.25rem;
}

.order-value {
    color: var(--text-primary);
    font-size: 0.875rem;
}

/* Tablet and Mobile responsiveness */
@media (max-width: 991px) {
    .nav-tabs {
        flex-direction: column;
        gap: 0.25rem;
    }
    
    .nav-tab {
        justify-content: center;
        padding: 0.75rem 1rem;
    }
}

@media (max-width: 768px) {
    .filter-buttons-container {
        width: 100%;
        justify-content: center;
    }
    
    .filter-btn {
        flex: 1 1 auto;
        min-width: calc(50% - 0.25rem);
        justify-content: center;
    }
    
    .filter-btn .btn-text {
        display: inline;
    }
}

@media (max-width: 768px) {
    .order-card-body {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 576px) {
    .filter-btn {
        padding: 0.5rem 0.75rem;
        font-size: 0.8125rem;
    }
    
    .filter-btn .btn-text {
        display: none;
    }
    
    .filter-btn i {
        margin: 0 !important;
    }
    
    .order-card {
        margin-bottom: 0.75rem;
    }
    
    .order-card-header {
        padding: 0.75rem;
    }
    
    .order-card-body {
        padding: 0.75rem;
        gap: 0.5rem;
    }
    
    .order-info-row {
        padding: 0.4rem;
    }
    
    .order-label {
        font-size: 0.7rem;
    }
    
    .order-value {
        font-size: 0.8125rem;
    }
}
</style>

<script>
function filterOrders(status) {
    // Filter table rows (desktop view)
    const rows = document.querySelectorAll('#ordersTable tbody tr');
    rows.forEach(row => {
        const rowStatus = row.getAttribute('data-status');
        if (status === 'all' || rowStatus === status) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
    
    // Filter cards (mobile view)
    const cards = document.querySelectorAll('.order-card');
    cards.forEach(card => {
        const cardStatus = card.getAttribute('data-status');
        if (status === 'all' || cardStatus === status) {
            card.style.display = '';
        } else {
            card.style.display = 'none';
        }
    });
    
    // Update active button state
    const filterBtns = document.querySelectorAll('.filter-btn');
    filterBtns.forEach(btn => {
        btn.classList.remove('active');
    });
    event.target.closest('.filter-btn').classList.add('active');
}

function viewOrderDetails(orderId) {
    // Load order details via AJAX
    fetch(`/sales/orders/${orderId}/details`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.text();
        })
        .then(html => {
            document.getElementById('orderDetailsContent').innerHTML = html;
            new bootstrap.Modal(document.getElementById('orderDetailsModal')).show();
        })
        .catch(error => {
            console.error('Error loading order details:', error);
            // Show error modal
            document.getElementById('errorMessage').textContent = 'Error loading order details. Please try again.';
            const errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
            errorModal.show();
        });
}

// Global variables for status update
let currentOrderId = null;
let currentOrderStatus = null;
let newOrderStatus = null;

function updateOrderStatus(orderId, status) {
    currentOrderId = orderId;
    currentOrderStatus = getCurrentStatusText(orderId);
    newOrderStatus = getStatusText(status);
    
    // Update modal content
    document.getElementById('currentStatus').textContent = currentOrderStatus;
    document.getElementById('newStatus').textContent = newOrderStatus;
    
    // Show confirmation modal
    const statusUpdateModal = new bootstrap.Modal(document.getElementById('statusUpdateModal'));
    statusUpdateModal.show();
}

// Function to update order status from the order details modal
function updateOrderStatusFromModal(orderId, status) {
    currentOrderId = orderId;
    currentOrderStatus = getStatusText(status === 'pending' ? 'pending' : 
                                     status === 'processing' ? 'processing' : 
                                     status === 'completed' ? 'completed' : 'cancelled');
    newOrderStatus = getStatusText(status);
    
    // Update modal content
    document.getElementById('currentStatus').textContent = currentOrderStatus;
    document.getElementById('newStatus').textContent = newOrderStatus;
    
    // Close the order details modal
    bootstrap.Modal.getInstance(document.getElementById('orderDetailsModal')).hide();
    
    // Show confirmation modal
    const statusUpdateModal = new bootstrap.Modal(document.getElementById('statusUpdateModal'));
    statusUpdateModal.show();
}

// Helper function to get status text
function getStatusText(status) {
    const statusMap = {
        'pending': 'Pending',
        'processing': 'Processing',
        'completed': 'Completed',
        'cancelled': 'Cancelled'
    };
    return statusMap[status] || status;
}

// Helper function to get current status text
function getCurrentStatusText(orderId) {
    const row = document.querySelector(`tr[data-order-id="${orderId}"]`) || 
                document.querySelector(`tr:has(button[onclick*="${orderId}"])`);
    if (row) {
        const statusBadge = row.querySelector('.badge');
        return statusBadge ? statusBadge.textContent.trim() : 'Unknown';
    }
    return 'Unknown';
}

// Handle status update confirmation
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('confirmStatusUpdate').addEventListener('click', function() {
        if (!currentOrderId || !newOrderStatus) {
            console.error('Order ID or status not set');
            return;
        }
        
        // Get the status value from the text
        const statusMap = {
            'Pending': 'pending',
            'Processing': 'processing',
            'Completed': 'completed',
            'Cancelled': 'cancelled'
        };
        const statusValue = statusMap[newOrderStatus] || newOrderStatus.toLowerCase();
        
        // Hide confirmation modal
        bootstrap.Modal.getInstance(document.getElementById('statusUpdateModal')).hide();
        
        // Show loading state
        const confirmBtn = this;
        const originalText = confirmBtn.innerHTML;
        confirmBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Updating...';
        confirmBtn.disabled = true;
        
        // Get password from input
        const userPassword = document.getElementById('userPassword').value;
        if (!userPassword) {
            alert('Please enter your password');
            return;
        }
        
        // Make the API call
        fetch(`/sales/orders/${currentOrderId}/status`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ 
                status: statusValue,
                password: userPassword
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success modal
                document.getElementById('updatedStatus').textContent = newOrderStatus;
                const successModal = new bootstrap.Modal(document.getElementById('successModal'));
                successModal.show();
                
                // Refresh the page after a short delay
                setTimeout(() => {
                    location.reload();
                }, 2000);
            } else {
                // Show error modal
                document.getElementById('errorMessage').textContent = data.message || 'Error updating order status';
                const errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
                errorModal.show();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            // Show error modal
            document.getElementById('errorMessage').textContent = 'Error updating order status. Please try again.';
            const errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
            errorModal.show();
        })
        .finally(() => {
            // Reset button state
            confirmBtn.innerHTML = originalText;
            confirmBtn.disabled = false;
            // Clear password field
            document.getElementById('userPassword').value = '';
        });
    });
});

// Archive single order
async function archiveOrder(orderId) {
    if (!confirm('Are you sure you want to archive this paid invoice? It will be moved to the archived orders list.')) {
        return;
    }
    
    try {
        const response = await fetch(`/sales/orders/${orderId}/archive`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json'
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert('Error: ' + (data.message || 'Failed to archive order'));
        }
    } catch (error) {
        alert('Error: ' + error.message);
    }
}

// Bulk archive paid orders
async function bulkArchivePaidOrders() {
    if (!confirm('This will archive ALL paid orders with invoices. Are you sure you want to continue?')) {
        return;
    }
    
    // Get all paid order IDs
    const paidOrderIds = [];
    document.querySelectorAll('tr[data-order-id]').forEach(row => {
        const paymentStatus = row.getAttribute('data-payment-status');
        const hasInvoice = row.getAttribute('data-has-invoice');
        const isArchived = row.getAttribute('data-is-archived');
        
        if (paymentStatus === 'paid' && hasInvoice === 'true' && isArchived !== 'true') {
            paidOrderIds.push(row.getAttribute('data-order-id'));
        }
    });
    
    if (paidOrderIds.length === 0) {
        alert('No paid orders found to archive.');
        return;
    }
    
    try {
        const response = await fetch('/sales/orders/bulk-archive', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ order_ids: paidOrderIds })
        });
        
        const data = await response.json();
        
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert('Error: ' + (data.message || 'Failed to archive orders'));
        }
    } catch (error) {
        alert('Error: ' + error.message);
    }
}

// Record payment for invoice
function recordPayment(orderId, amount) {
    document.getElementById('paymentOrderId').value = orderId;
    document.getElementById('invoiceAmount').textContent = parseFloat(amount).toFixed(2);
    document.getElementById('amountPaid').value = parseFloat(amount).toFixed(2);
    
    // Reset form
    document.getElementById('recordPaymentForm').reset();
    document.getElementById('paymentOrderId').value = orderId;
    document.getElementById('amountPaid').value = parseFloat(amount).toFixed(2);
    
    const modal = new bootstrap.Modal(document.getElementById('recordPaymentModal'));
    modal.show();
}

// Confirm payment recording
document.getElementById('confirmPaymentBtn').addEventListener('click', async function() {
    const form = document.getElementById('recordPaymentForm');
    
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    
    const orderId = document.getElementById('paymentOrderId').value;
    const formData = new FormData(form);
    const data = Object.fromEntries(formData.entries());
    
    const btn = this;
    const originalText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';
    
    try {
        const response = await fetch(`/sales/orders/${orderId}/record-payment`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (result.success) {
            const modal = bootstrap.Modal.getInstance(document.getElementById('recordPaymentModal'));
            modal.hide();
            
            alert('Payment recorded successfully!');
            location.reload();
        } else {
            alert('Error: ' + (result.message || 'Failed to record payment'));
        }
    } catch (error) {
        alert('Error: ' + error.message);
    } finally {
        btn.disabled = false;
        btn.innerHTML = originalText;
    }
});
</script>
@endsection