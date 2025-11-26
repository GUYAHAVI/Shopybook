@extends('layouts.dash')

@section('content')
<div class="container-fluid py-4">
    <!-- Supplier Header -->
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h5 class="mb-0">{{ $supplier->name }}</h5>
                            <p class="text-sm mb-0">
                                <span class="badge bg-{{ $supplier->status == 'active' ? 'success' : 'secondary' }}">
                                    {{ ucfirst($supplier->status) }}
                                </span>
                                @if($supplier->contact_person)
                                    <span class="ms-2">Contact: {{ $supplier->contact_person }}</span>
                                @endif
                            </p>
                        </div>
                        <div>
                            <a href="{{ route('suppliers.edit', $supplier) }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-edit me-2"></i>Edit
                            </a>
                            <a href="{{ route('suppliers.index') }}" class="btn btn-sm btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Back
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row">
        <div class="col-xl-3 col-sm-6 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-uppercase font-weight-bold">Total Orders</p>
                                <h5 class="font-weight-bolder">{{ $stats['total_orders'] }}</h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-primary shadow-primary text-center rounded-circle">
                                <i class="fas fa-shopping-cart text-lg opacity-10"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-uppercase font-weight-bold">Total Spent</p>
                                <h5 class="font-weight-bolder">KSh {{ number_format($stats['total_spent'], 2) }}</h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-success shadow-success text-center rounded-circle">
                                <i class="fas fa-money-bill-wave text-lg opacity-10"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-uppercase font-weight-bold">This Month</p>
                                <h5 class="font-weight-bolder">KSh {{ number_format($stats['month_spent'], 2) }}</h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-info shadow-info text-center rounded-circle">
                                <i class="fas fa-calendar text-lg opacity-10"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-uppercase font-weight-bold">Last Order</p>
                                <h5 class="font-weight-bolder">{{ $stats['last_order'] }}</h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-warning shadow-warning text-center rounded-circle">
                                <i class="fas fa-clock text-lg opacity-10"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Supplier Details and Purchase History -->
    <div class="row">
        <!-- Supplier Information -->
        <div class="col-lg-4 mb-4">
            <div class="card">
                <div class="card-header pb-0">
                    <h6>Supplier Information</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-uppercase text-secondary font-weight-bold">Contact Information</small>
                        @if($supplier->phone)
                            <p class="mb-1"><i class="fas fa-phone me-2 text-primary"></i>{{ $supplier->phone }}</p>
                        @endif
                        @if($supplier->email)
                            <p class="mb-1"><i class="fas fa-envelope me-2 text-primary"></i>{{ $supplier->email }}</p>
                        @endif
                    </div>

                    @if($supplier->address || $supplier->city || $supplier->country)
                        <div class="mb-3">
                            <small class="text-uppercase text-secondary font-weight-bold">Address</small>
                            <p class="mb-0">
                                @if($supplier->address){{ $supplier->address }}<br>@endif
                                @if($supplier->city){{ $supplier->city }}@if($supplier->country), @endif@endif
                                @if($supplier->country){{ $supplier->country }}@endif
                            </p>
                        </div>
                    @endif

                    @if($supplier->company_registration || $supplier->tax_number)
                        <div class="mb-3">
                            <small class="text-uppercase text-secondary font-weight-bold">Business Details</small>
                            @if($supplier->company_registration)
                                <p class="mb-1"><strong>Reg#:</strong> {{ $supplier->company_registration }}</p>
                            @endif
                            @if($supplier->tax_number)
                                <p class="mb-1"><strong>Tax#:</strong> {{ $supplier->tax_number }}</p>
                            @endif
                        </div>
                    @endif

                    <div class="mb-3">
                        <small class="text-uppercase text-secondary font-weight-bold">Payment Terms</small>
                        <p class="mb-1"><strong>Terms:</strong> {{ $supplier->payment_terms ?? 'Not specified' }}</p>
                        @if($supplier->credit_limit)
                            <p class="mb-0"><strong>Credit Limit:</strong> KSh {{ number_format($supplier->credit_limit, 2) }}</p>
                        @endif
                    </div>

                    @if($supplier->notes)
                        <div class="mb-3">
                            <small class="text-uppercase text-secondary font-weight-bold">Notes</small>
                            <p class="mb-0">{{ $supplier->notes }}</p>
                        </div>
                    @endif

                    <div class="text-muted text-xs">
                        <small>Added: {{ $supplier->created_at->format('M d, Y') }}</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Purchase History -->
        <div class="col-lg-8 mb-4">
            <div class="card">
                <div class="card-header pb-0">
                    <h6>Purchase History (Stock Receipts)</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Receipt #</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Date</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Product</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Quantity</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Total Cost</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($purchaseHistory as $receipt)
                                    <tr>
                                        <td>
                                            <p class="text-xs font-weight-bold mb-0">{{ $receipt->receipt_number }}</p>
                                        </td>
                                        <td>
                                            <p class="text-xs font-weight-bold mb-0">{{ $receipt->receipt_date->format('M d, Y') }}</p>
                                        </td>
                                        <td>
                                            <p class="text-xs font-weight-bold mb-0">{{ $receipt->product_name }}</p>
                                        </td>
                                        <td>
                                            <p class="text-xs font-weight-bold mb-0">{{ number_format($receipt->quantity_received) }}</p>
                                        </td>
                                        <td>
                                            <p class="text-xs font-weight-bold mb-0">KSh {{ number_format($receipt->total_cost, 2) }}</p>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4">
                                            <p class="text-secondary mb-0">No purchase history found</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($purchaseHistory->hasPages())
                        <div class="mt-3">
                            {{ $purchaseHistory->links() }}
                        </div>
                    @endif
                </div>
            </div>

            <!-- Products from this Supplier -->
            <div class="card mt-4">
                <div class="card-header pb-0">
                    <h6>Products from this Supplier ({{ $products->count() }})</h6>
                </div>
                <div class="card-body">
                    @if($products->count() > 0)
                        <div class="table-responsive">
                            <table class="table align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Product</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">SKU</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Stock</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Cost Price</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($products as $product)
                                        <tr>
                                            <td>
                                                <p class="text-xs font-weight-bold mb-0">{{ $product->name }}</p>
                                            </td>
                                            <td>
                                                <p class="text-xs text-secondary mb-0">{{ $product->sku ?? 'N/A' }}</p>
                                            </td>
                                            <td>
                                                <p class="text-xs font-weight-bold mb-0">{{ $product->stock_quantity }}</p>
                                            </td>
                                            <td>
                                                <p class="text-xs font-weight-bold mb-0">KSh {{ number_format($product->cost_price ?? 0, 2) }}</p>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-secondary mb-0">No products linked to this supplier yet</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection






