@extends('layouts.dash')

@section('title', $customer->name . ' — Customer')

@section('content')
<div class="container-fluid px-4">
    {{-- Breadcrumb --}}
    <div class="mb-3">
        <a href="{{ route('sales.customers') }}" class="text-muted small text-decoration-none">
            <i class="fas fa-arrow-left me-1"></i>Back to customers
        </a>
    </div>

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-start mb-4 flex-wrap gap-2">
        <div class="d-flex align-items-center gap-3">
            <div style="width:56px;height:56px;border-radius:50%;background:linear-gradient(135deg,#7b2e2e,#ff511a);color:#fff;display:flex;align-items:center;justify-content:center;font-size:1.4rem;font-weight:700;font-family:'Playfair Display',serif;">
                {{ strtoupper(substr($customer->name, 0, 1)) }}
            </div>
            <div>
                <h1 class="h3 mb-0" style="font-family:'Playfair Display',serif;color:var(--primary-color);">{{ $customer->name }}</h1>
                <p class="text-muted mb-0 small">
                    @if($type === 'organization') Company / organization @else Individual customer @endif
                    &middot; joined {{ $customer->created_at?->format('M j, Y') ?? 'unknown' }}
                </p>
            </div>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-secondary btn-sm" onclick="window.print()">
                <i class="fas fa-print me-1"></i>Print
            </button>
            <a href="{{ route('sales.customers') }}" class="btn btn-outline-primary btn-sm">
                <i class="fas fa-users me-1"></i>All customers
            </a>
        </div>
    </div>

    <div class="row g-4">
        {{-- Left: contact info --}}
        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header">
                    <i class="fas fa-id-card me-2"></i>Contact
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="text-muted small mb-1">Email</div>
                        <div>{{ $customer->email ?? 'Not provided' }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="text-muted small mb-1">Phone</div>
                        <div>{{ $customer->phone ?? 'Not provided' }}</div>
                    </div>
                    @if($type === 'organization')
                    <div class="mb-3">
                        <div class="text-muted small mb-1">KRA PIN</div>
                        <div>{{ $customer->kra_pin ?? 'Not provided' }}</div>
                    </div>
                    @endif
                    <div class="mb-3">
                        <div class="text-muted small mb-1">Address</div>
                        <div>{{ $customer->address ?? 'Not provided' }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="text-muted small mb-1">City</div>
                        <div>{{ $customer->city ?? 'Not provided' }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="text-muted small mb-1">Country</div>
                        <div>{{ $customer->country ?? 'Not provided' }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right: summary + orders --}}
        <div class="col-lg-8">
            {{-- Stats --}}
            <div class="row g-3 mb-3">
                <div class="col-6">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="text-muted small text-uppercase">Orders</div>
                            <div style="font-family:'Playfair Display',serif;font-size:1.8rem;color:var(--primary-color);font-weight:700;">{{ $orderCount }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="text-muted small text-uppercase">Total spent</div>
                            <div style="font-family:'Playfair Display',serif;font-size:1.8rem;color:var(--primary-color);font-weight:700;">KSh {{ number_format($totalSpent, 0) }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Orders table --}}
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-shopping-bag me-2"></i>Order history</span>
                    @if($orders->hasPages())
                        <span class="text-muted small">{{ $orders->firstItem() }}–{{ $orders->lastItem() }} of {{ $orders->total() }}</span>
                    @endif
                </div>
                <div class="card-body p-0">
                    @if($orders->isNotEmpty())
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Order #</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th class="text-end">Total</th>
                                    <th width="60"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($orders as $order)
                                <tr>
                                    <td>{{ $order->order_number ?? '#' . $order->id }}</td>
                                    <td class="small">{{ $order->created_at?->format('M j, Y') ?? '-' }}</td>
                                    <td>
                                        @php $status = $order->status ?? 'unknown'; @endphp
                                        @if($status === 'completed')
                                            <span class="badge" style="background:var(--success-color);">{{ ucfirst($status) }}</span>
                                        @elseif($status === 'pending')
                                            <span class="badge bg-warning">{{ ucfirst($status) }}</span>
                                        @elseif($status === 'cancelled')
                                            <span class="badge bg-danger">{{ ucfirst($status) }}</span>
                                        @else
                                            <span class="badge bg-secondary">{{ ucfirst($status) }}</span>
                                        @endif
                                    </td>
                                    <td class="text-end">KSh {{ number_format($order->total_amount ?? 0, 0) }}</td>
                                    <td>
                                        <a href="{{ route('sales.order-details', $order->id) }}" class="btn btn-sm btn-outline-primary" title="View order">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($orders->hasPages())
                    <div class="p-3">{{ $orders->links('pagination::bootstrap-5') }}</div>
                    @endif
                    @else
                    <div class="text-center py-5">
                        <i class="fas fa-shopping-bag fa-3x mb-3" style="color:var(--text-muted);opacity:0.4;"></i>
                        <h5>No orders yet</h5>
                        <p class="text-muted">When this customer buys something, their orders will appear here.</p>
                        <a href="{{ route('sales.pos') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-cash-register me-1"></i>Open till
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
