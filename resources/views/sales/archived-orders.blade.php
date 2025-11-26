@extends('layouts.dash')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0" style="color: var(--text-primary);">
            <i class="fas fa-archive me-2"></i>Archived Orders
        </h1>
        <a href="{{ route('sales.orders') }}" class="btn btn-primary">
            <i class="fas fa-arrow-left me-2"></i>Back to Orders
        </a>
    </div>

    <div class="card shadow mb-4" style="background: var(--card-bg); border: 1px solid var(--border-color);">
        <div class="card-header py-3" style="background: var(--bg-tertiary); border-bottom: 1px solid var(--border-color);">
            <h6 class="m-0 font-weight-bold" style="color: var(--text-primary);">Archived Paid Orders</h6>
        </div>
        <div class="card-body">
            @if($orders->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Order #</th>
                                <th>Invoice #</th>
                                <th>Customer</th>
                                <th>Total</th>
                                <th>Order Date</th>
                                <th>Archived Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($orders as $order)
                                <tr>
                                    <td>{{ $order->order_number }}</td>
                                    <td>{{ $order->invoice_number }}</td>
                                    <td>
                                        @if($order->order_type === 'public_order')
                                            {{ $order->customer_name }}
                                        @else
                                            {{ $order->customer ? $order->customer->name : 'Walk-in' }}
                                        @endif
                                    </td>
                                    <td>KSh {{ number_format($order->total_amount, 2) }}</td>
                                    <td>{{ $order->created_at->format('M d, Y') }}</td>
                                    <td>{{ $order->archived_at ? $order->archived_at->format('M d, Y') : 'N/A' }}</td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('sales.view-invoice', $order->id) }}" target="_blank" class="btn btn-sm btn-outline-primary" title="View Invoice">
                                                <i class="fas fa-file-invoice"></i>
                                            </a>
                                            <a href="{{ route('sales.print-receipt', $order->id) }}" target="_blank" class="btn btn-sm btn-outline-secondary" title="View Receipt">
                                                <i class="fas fa-receipt"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $orders->links() }}
                </div>
            @else
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>No archived orders found.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
