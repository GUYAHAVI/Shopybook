@extends('layouts.dash')

@section('content')
<div class="container-fluid px-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="h4 mb-0">Customer Details</h2>
                <a href="{{ route('sales.customers') }}" class="btn btn-secondary">Back to Customers</a>
            </div>
            <div class="mb-3">
                <div class="btn-group" role="group">
                    <a href="?type=individual" class="btn btn-outline-primary @if($type === 'individual') active @endif">Individual Customers</a>
                    <a href="?type=organization" class="btn btn-outline-primary @if($type === 'organization') active @endif">Company/Organization Customers</a>
                </div>
            </div>
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">{{ $customer->name }}</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Email:</strong> {{ $customer->email ?? 'N/A' }}<br>
                            <strong>Phone:</strong> {{ $customer->phone ?? 'N/A' }}<br>
                            @if($type === 'organization')
                                <strong>KRA PIN:</strong> {{ $customer->kra_pin ?? 'N/A' }}<br>
                            @endif
                            <strong>Address:</strong> {{ $customer->address ?? 'N/A' }}<br>
                            <strong>City:</strong> {{ $customer->city ?? 'N/A' }}<br>
                            <strong>Country:</strong> {{ $customer->country ?? 'N/A' }}<br>
                        </div>
                        <div class="col-md-6 text-end">
                            <a href="#" class="btn btn-outline-info mb-2"><i class="fas fa-file-invoice"></i> Generate Invoice</a>
                            <a href="#" class="btn btn-outline-success mb-2"><i class="fas fa-print"></i> Print Report</a>
                        </div>
                    </div>
                    <hr>
                    <h6>Purchases/Orders</h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead>
                                <tr>
                                    <th>Order #</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Total</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($orders as $order)
                                    <tr>
                                        <td>{{ $order->order_number ?? '-' }}</td>
                                        <td>{{ $order->created_at ? $order->created_at->format('Y-m-d') : '-' }}</td>
                                        <td>{{ ucfirst($order->status ?? '-') }}</td>
                                        <td>KSh {{ number_format($order->total_amount ?? 0, 2) }}</td>
                                        <td>
                                            <a href="{{ route('sales.order-details', $order->id) }}" class="btn btn-sm btn-outline-info" title="View Order"><i class="fas fa-eye"></i></a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="text-center text-muted">No purchases found.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 