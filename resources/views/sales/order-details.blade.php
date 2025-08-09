<div class="row">
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

<div class="row mt-4">
    <div class="col-12">
        <h6 class="font-weight-bold">Order Items</h6>
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
        <h6 class="font-weight-bold">Actions</h6>
        <div class="d-flex flex-wrap gap-2">
            <!-- Print Receipt Button -->
            <a href="{{ route('sales.print-receipt', $order) }}" target="_blank" class="btn btn-outline-primary">
                <i class="fas fa-print me-2"></i>Print Receipt
            </a>
            
            <!-- Status Change Buttons -->
            @if($order->status === 'pending')
                <button type="button" class="btn btn-outline-success" onclick="updateOrderStatusFromModal({{ $order->id }}, 'processing')">
                    <i class="fas fa-play me-2"></i>Mark as Processing
                </button>
            @elseif($order->status === 'processing')
                <button type="button" class="btn btn-outline-success" onclick="updateOrderStatusFromModal({{ $order->id }}, 'completed')">
                    <i class="fas fa-check me-2"></i>Mark as Completed
                </button>
            @elseif($order->status === 'completed')
                <button type="button" class="btn btn-outline-warning" onclick="updateOrderStatusFromModal({{ $order->id }}, 'pending')">
                    <i class="fas fa-clock me-2"></i>Mark as Pending
                </button>
            @endif
            
            <!-- Cancel Order Button (if not already cancelled) -->
            @if($order->status !== 'cancelled')
                <button type="button" class="btn btn-outline-danger" onclick="updateOrderStatusFromModal({{ $order->id }}, 'cancelled')">
                    <i class="fas fa-times me-2"></i>Cancel Order
                </button>
            @endif
        </div>
    </div>
</div> 