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
        </div>
    </div>

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0" style="color: var(--text-primary);">Orders</h1>
        <div class="btn-group" role="group">
            <button type="button" class="btn btn-outline-primary" onclick="filterOrders('all')">All Orders</button>
            <button type="button" class="btn btn-outline-warning" onclick="filterOrders('pending')">Pending</button>
            <button type="button" class="btn btn-outline-info" onclick="filterOrders('processing')">Processing</button>
            <button type="button" class="btn btn-outline-success" onclick="filterOrders('completed')">Completed</button>
            <button type="button" class="btn btn-outline-danger" onclick="filterOrders('cancelled')">Cancelled</button>
        </div>
    </div>

    <div class="card shadow mb-4" style="background: var(--card-bg); border: 1px solid var(--border-color);">
        <div class="card-header py-3" style="background: var(--bg-tertiary); border-bottom: 1px solid var(--border-color);">
            <h6 class="m-0 font-weight-bold" style="color: var(--text-primary);">Order Management</h6>
        </div>
        <div class="card-body">
            @if($orders->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered" id="ordersTable" width="100%" cellspacing="0">
                        <thead style="background-color: var(--bg-tertiary);">
                            <tr>
                                <th style="color: var(--text-secondary); border-bottom: 2px solid var(--border-color);">Order #</th>
                                <th style="color: var(--text-secondary); border-bottom: 2px solid var(--border-color);">Type</th>
                                <th style="color: var(--text-secondary); border-bottom: 2px solid var(--border-color);">Customer</th>
                                <th style="color: var(--text-secondary); border-bottom: 2px solid var(--border-color);">Products</th>
                                <th style="color: var(--text-secondary); border-bottom: 2px solid var(--border-color);">Total</th>
                                <th style="color: var(--text-secondary); border-bottom: 2px solid var(--border-color);">Status</th>
                                <th style="color: var(--text-secondary); border-bottom: 2px solid var(--border-color);">Payment</th>
                                <th style="color: var(--text-secondary); border-bottom: 2px solid var(--border-color);">Date</th>
                                <th style="color: var(--text-secondary); border-bottom: 2px solid var(--border-color);">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($orders as $order)
                                <tr data-status="{{ $order->status }}" data-type="{{ $order->order_type }}" style="color: var(--text-primary);">
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
                                        <span class="badge badge-{{ $order->status_color }}">
                                            {{ $order->status_text }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($order->order_type === 'public_order')
                                            <span class="badge badge-{{ $order->payment_status === 'paid' ? 'success' : ($order->payment_status === 'pending' ? 'warning' : 'danger') }}">
                                                {{ ucfirst($order->payment_status) }}
                                            </span>
                                        @else
                                            <span class="badge badge-{{ $order->payment_method ? 'info' : 'secondary' }}">
                                                {{ $order->payment_method ? ucfirst($order->payment_method) : 'Not Set' }}
                                            </span>
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
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
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
    
    .btn-group {
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .btn-group .btn {
        width: 100%;
    }
}
</style>

<script>
function filterOrders(status) {
    const rows = document.querySelectorAll('#ordersTable tbody tr');
    rows.forEach(row => {
        const rowStatus = row.getAttribute('data-status');
        if (status === 'all' || rowStatus === status) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
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
</script>
@endsection