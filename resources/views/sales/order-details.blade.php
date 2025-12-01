<div class="order-details-container">
    <!-- Desktop View - Tables -->
    <div class="row d-none d-md-flex">
        <div class="col-md-6">
            <h6 class="font-weight-bold">Order Information</h6>
            <table class="table table-sm">
                <tr>
                    <td><strong>Order #:</strong></td>
                    <td>{{ $order->order_number ?? 'ORD-' . $order->id }}</td>
                </tr>
                <tr>
                    <td><strong>Date:</strong></td>
                    <td>{{ $order->created_at->format('M d, Y H:i') }}</td>
                </tr>
                <tr>
                    <td><strong>Status:</strong></td>
                    <td>
                        <span class="badge badge-{{ $order->status_color }}">
                            {{ $order->status_text }}
                        </span>
                    </td>
                </tr>
                <tr>
                    <td><strong>Type:</strong></td>
                    <td>
                        @if($order->order_type === 'public_order')
                            <span class="badge badge-info">Public Order</span>
                        @else
                            <span class="badge badge-primary">Internal Order</span>
                        @endif
                    </td>
                </tr>
            </table>
        </div>
        
        <div class="col-md-6">
            <h6 class="font-weight-bold">Customer Information</h6>
            @if($order->order_type === 'public_order')
                <table class="table table-sm">
                    <tr>
                        <td><strong>Name:</strong></td>
                        <td>{{ $order->customer_name }}</td>
                    </tr>
                    <tr>
                        <td><strong>Phone:</strong></td>
                        <td>{{ $order->customer_phone }}</td>
                    </tr>
                    <tr>
                        <td><strong>Email:</strong></td>
                        <td>{{ $order->customer_email ?? 'Not provided' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Address:</strong></td>
                        <td>{{ $order->delivery_address }}</td>
                    </tr>
                </table>
            @else
                <table class="table table-sm">
                    <tr>
                        <td><strong>Name:</strong></td>
                        <td>{{ $order->customer ? $order->customer->name : 'Walk-in Customer' }}</td>
                    </tr>
                    @if($order->customer)
                        <tr>
                            <td><strong>Phone:</strong></td>
                            <td>{{ $order->customer->phone ?? 'Not provided' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Email:</strong></td>
                            <td>{{ $order->customer->email ?? 'Not provided' }}</td>
                        </tr>
                    @endif
                </table>
            @endif
        </div>
    </div>

    <!-- Mobile View - Cards -->
    <div class="d-md-none">
        <div class="detail-cards-grid">
            <!-- Order Information Card -->
            <div class="detail-section-card">
                <h6 class="detail-section-title"><i class="fas fa-file-invoice me-2"></i>Order Information</h6>
                <div class="detail-info-grid">
                    <div class="detail-info-item">
                        <span class="detail-label">Order #</span>
                        <span class="detail-value">{{ $order->order_number ?? 'ORD-' . $order->id }}</span>
                    </div>
                    <div class="detail-info-item">
                        <span class="detail-label">Date</span>
                        <span class="detail-value">{{ $order->created_at->format('M d, Y H:i') }}</span>
                    </div>
                    <div class="detail-info-item">
                        <span class="detail-label">Status</span>
                        <span class="detail-value">
                            <span class="badge badge-{{ $order->status_color }}">
                                {{ $order->status_text }}
                            </span>
                        </span>
                    </div>
                    <div class="detail-info-item">
                        <span class="detail-label">Type</span>
                        <span class="detail-value">
                            @if($order->order_type === 'public_order')
                                <span class="badge badge-info">Public Order</span>
                            @else
                                <span class="badge badge-primary">Internal Order</span>
                            @endif
                        </span>
                    </div>
                </div>
            </div>

            <!-- Customer Information Card -->
            <div class="detail-section-card">
                <h6 class="detail-section-title"><i class="fas fa-user me-2"></i>Customer Information</h6>
                <div class="detail-info-grid">
                    @if($order->order_type === 'public_order')
                        <div class="detail-info-item">
                            <span class="detail-label">Name</span>
                            <span class="detail-value">{{ $order->customer_name }}</span>
                        </div>
                        <div class="detail-info-item">
                            <span class="detail-label">Phone</span>
                            <span class="detail-value">{{ $order->customer_phone }}</span>
                        </div>
                        <div class="detail-info-item">
                            <span class="detail-label">Email</span>
                            <span class="detail-value">{{ $order->customer_email ?? 'Not provided' }}</span>
                        </div>
                        <div class="detail-info-item">
                            <span class="detail-label">Address</span>
                            <span class="detail-value">{{ $order->delivery_address }}</span>
                        </div>
                    @else
                        <div class="detail-info-item">
                            <span class="detail-label">Name</span>
                            <span class="detail-value">{{ $order->customer ? $order->customer->name : 'Walk-in Customer' }}</span>
                        </div>
                        @if($order->customer)
                            <div class="detail-info-item">
                                <span class="detail-label">Phone</span>
                                <span class="detail-value">{{ $order->customer->phone ?? 'Not provided' }}</span>
                            </div>
                            <div class="detail-info-item">
                                <span class="detail-label">Email</span>
                                <span class="detail-value">{{ $order->customer->email ?? 'Not provided' }}</span>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-12">
        <h6 class="font-weight-bold"><i class="fas fa-box me-2"></i>Order Items</h6>
        
        <!-- Desktop Table View -->
        <div class="d-none d-md-block">
            @if($order->order_type === 'public_order')
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Quantity</th>
                            <th>Unit Price</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{{ $order->product ? $order->product->name : 'Product' }}</td>
                            <td>{{ $order->quantity }}</td>
                            <td>KSh {{ number_format($order->unit_price, 2) }}</td>
                            <td><strong>KSh {{ number_format($order->total_price, 2) }}</strong></td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="text-right"><strong>Total:</strong></td>
                            <td><strong>KSh {{ number_format($order->total_price, 2) }}</strong></td>
                        </tr>
                    </tfoot>
                </table>
            @else
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Quantity</th>
                            <th>Price</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $item)
                            <tr>
                                <td>{{ $item->product->name }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>KSh {{ number_format($item->price, 2) }}</td>
                                <td>KSh {{ number_format($item->total, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="text-right"><strong>Total:</strong></td>
                            <td><strong>{{ $order->formatted_total }}</strong></td>
                        </tr>
                    </tfoot>
                </table>
            @endif
        </div>

        <!-- Mobile Card View -->
        <div class="d-md-none">
            @if($order->order_type === 'public_order')
                <div class="order-item-card">
                    <div class="order-item-header">
                        <strong>{{ $order->product ? $order->product->name : 'Product' }}</strong>
                    </div>
                    <div class="order-item-details">
                        <div class="order-item-info">
                            <span class="item-label">Quantity:</span>
                            <span class="item-value">{{ $order->quantity }}</span>
                        </div>
                        <div class="order-item-info">
                            <span class="item-label">Unit Price:</span>
                            <span class="item-value">KSh {{ number_format($order->unit_price, 2) }}</span>
                        </div>
                        <div class="order-item-info">
                            <span class="item-label">Total:</span>
                            <span class="item-value"><strong>KSh {{ number_format($order->total_price, 2) }}</strong></span>
                        </div>
                    </div>
                </div>
                <div class="order-total-card">
                    <span class="total-label">Total:</span>
                    <span class="total-value"><strong>KSh {{ number_format($order->total_price, 2) }}</strong></span>
                </div>
            @else
                @foreach($order->items as $item)
                    <div class="order-item-card">
                        <div class="order-item-header">
                            <strong>{{ $item->product->name }}</strong>
                        </div>
                        <div class="order-item-details">
                            <div class="order-item-info">
                                <span class="item-label">Quantity:</span>
                                <span class="item-value">{{ $item->quantity }}</span>
                            </div>
                            <div class="order-item-info">
                                <span class="item-label">Price:</span>
                                <span class="item-value">KSh {{ number_format($item->price, 2) }}</span>
                            </div>
                            <div class="order-item-info">
                                <span class="item-label">Total:</span>
                                <span class="item-value"><strong>KSh {{ number_format($item->total, 2) }}</strong></span>
                            </div>
                        </div>
                    </div>
                @endforeach
                <div class="order-total-card">
                    <span class="total-label">Total:</span>
                    <span class="total-value"><strong>{{ $order->formatted_total }}</strong></span>
                </div>
            @endif
        </div>
    </div>
</div>

@if($order->notes)
<div class="row mt-4">
    <div class="col-12">
        <h6 class="font-weight-bold">Notes</h6>
        <p class="text-muted">{{ $order->notes }}</p>
    </div>
</div>
@endif

<!-- Order Actions -->
<div class="row mt-4">
    <div class="col-12">
        <h6 class="font-weight-bold"><i class="fas fa-cog me-2"></i>Actions</h6>
        <div class="action-buttons-container">
            <!-- Print Receipt Button -->
            <a href="{{ route('sales.print-receipt', $order) }}" target="_blank" class="btn btn-outline-primary action-btn">
                <i class="fas fa-print me-2"></i><span class="btn-text">Print Receipt</span>
            </a>
            
            <!-- Status Change Buttons -->
            @if($order->status === 'pending')
                <button type="button" class="btn btn-outline-success action-btn" onclick="updateOrderStatusFromModal({{ $order->id }}, 'processing')">
                    <i class="fas fa-play me-2"></i><span class="btn-text">Mark as Processing</span>
                </button>
            @elseif($order->status === 'processing')
                <button type="button" class="btn btn-outline-success action-btn" onclick="updateOrderStatusFromModal({{ $order->id }}, 'completed')">
                    <i class="fas fa-check me-2"></i><span class="btn-text">Mark as Completed</span>
                </button>
            @elseif($order->status === 'completed')
                <button type="button" class="btn btn-outline-warning action-btn" onclick="updateOrderStatusFromModal({{ $order->id }}, 'pending')">
                    <i class="fas fa-clock me-2"></i><span class="btn-text">Mark as Pending</span>
                </button>
            @endif
            
            <!-- Cancel Order Button (if not already cancelled) -->
            @if($order->status !== 'cancelled')
                <button type="button" class="btn btn-outline-danger action-btn" onclick="updateOrderStatusFromModal({{ $order->id }}, 'cancelled')">
                    <i class="fas fa-times me-2"></i><span class="btn-text">Cancel Order</span>
                </button>
            @endif
        </div>
    </div>
</div>

<style>
/* Desktop View - Keep existing table styles */
.table {
    color: var(--text-primary);
}

.table th {
    background-color: var(--bg-tertiary);
    border-color: var(--border-color);
    color: var(--text-secondary);
}

.table td {
    border-color: var(--border-color);
}

/* Mobile Card Styles */
.detail-cards-grid {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.detail-section-card {
    background: var(--card-bg);
    border: 1px solid var(--border-color);
    border-radius: 0.5rem;
    padding: 1rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.detail-section-title {
    color: var(--text-primary);
    font-weight: 600;
    margin-bottom: 1rem;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid var(--border-color);
}

.detail-info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 0.75rem;
}

.detail-info-item {
    display: flex;
    flex-direction: column;
    padding: 0.5rem;
    background: var(--bg-tertiary);
    border-radius: 0.375rem;
}

.detail-label {
    font-size: 0.75rem;
    font-weight: 600;
    color: var(--text-secondary);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 0.25rem;
}

.detail-value {
    color: var(--text-primary);
    font-size: 0.875rem;
}

/* Order Items Mobile Cards */
.order-item-card {
    background: var(--card-bg);
    border: 1px solid var(--border-color);
    border-radius: 0.5rem;
    margin-bottom: 0.75rem;
    overflow: hidden;
}

.order-item-header {
    background: var(--bg-tertiary);
    padding: 0.75rem;
    border-bottom: 1px solid var(--border-color);
    color: var(--text-primary);
}

.order-item-details {
    padding: 0.75rem;
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 0.5rem;
}

.order-item-info {
    display: flex;
    flex-direction: column;
    padding: 0.5rem;
    background: var(--bg-tertiary);
    border-radius: 0.375rem;
}

.item-label {
    font-size: 0.75rem;
    font-weight: 600;
    color: var(--text-secondary);
    margin-bottom: 0.25rem;
}

.item-value {
    font-size: 0.875rem;
    color: var(--text-primary);
}

.order-total-card {
    background: var(--primary-color);
    color: white;
    padding: 1rem;
    border-radius: 0.5rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 0.5rem;
    box-shadow: 0 2px 6px rgba(0,0,0,0.15);
}

.total-label {
    font-size: 1rem;
    font-weight: 600;
}

.total-value {
    font-size: 1.25rem;
    font-weight: 700;
}

/* Action Buttons */
.action-buttons-container {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.action-btn {
    display: inline-flex;
    align-items: center;
    padding: 0.5rem 1rem;
    font-size: 0.875rem;
    transition: all 0.2s ease;
}

/* Responsive Adjustments */
@media (max-width: 768px) {
    .detail-info-grid {
        grid-template-columns: 1fr;
    }
    
    .order-item-details {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 576px) {
    .detail-section-card {
        padding: 0.75rem;
    }
    
    .detail-section-title {
        font-size: 0.95rem;
    }
    
    .action-buttons-container {
        flex-direction: column;
    }
    
    .action-btn {
        width: 100%;
        justify-content: center;
    }
    
    .order-total-card {
        padding: 0.75rem;
    }
    
    .total-label {
        font-size: 0.9rem;
    }
    
    .total-value {
        font-size: 1.1rem;
    }
}
</style> 