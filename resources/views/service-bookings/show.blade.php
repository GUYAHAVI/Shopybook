@extends('layouts.dash')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="fw-bold" style="color:#020258;">Service Booking Details</h2>
                <div>
                    <a href="{{ route('service-bookings.edit', $serviceBooking) }}" class="btn btn-outline-primary me-2">
                        <i class="fas fa-edit me-1"></i> Edit
                    </a>
                    <a href="{{ route('service-bookings.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Back to List
                    </a>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="row">
                <div class="col-md-8">
                    <div class="card shadow-sm">
                        <div class="card-header">
                            <h5 class="mb-0">Booking Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Customer:</strong></p>
                                    <p class="text-muted">{{ $serviceBooking->customer->name ?? 'Walk-in Customer' }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Service Date:</strong></p>
                                    <p class="text-muted">{{ $serviceBooking->service_date ? $serviceBooking->service_date->format('M d, Y H:i') : 'Not set' }}</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Total Amount:</strong></p>
                                    <p class="text-muted">KSh {{ number_format($serviceBooking->total_amount, 2) }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Payment Status:</strong></p>
                                    <p>
                                        @if($serviceBooking->payment_status == 'paid')
                                            <span class="badge bg-success">Paid</span>
                                        @elseif($serviceBooking->payment_status == 'pending')
                                            <span class="badge bg-warning">Pending</span>
                                        @else
                                            <span class="badge bg-danger">Cancelled</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                            @if($serviceBooking->notes)
                                <div class="row">
                                    <div class="col-12">
                                        <p><strong>Notes:</strong></p>
                                        <p class="text-muted">{{ $serviceBooking->notes }}</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card shadow-sm">
                        <div class="card-header">
                            <h5 class="mb-0">Actions</h5>
                        </div>
                        <div class="card-body">
                            @if($serviceBooking->payment_status != 'paid')
                                <form action="{{ route('service-bookings.complete', $serviceBooking) }}" method="POST" class="mb-2">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="btn btn-success w-100" onclick="return confirm('Mark this booking as complete?')">
                                        <i class="fas fa-check me-1"></i> Mark as Complete
                                    </button>
                                </form>
                            @endif
                            
                            <form action="{{ route('service-bookings.destroy', $serviceBooking) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger w-100" onclick="return confirm('Delete this booking? This action cannot be undone.')">
                                    <i class="fas fa-trash me-1"></i> Delete Booking
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Services Section -->
            <div class="card shadow-sm mt-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Services Provided</h5>
                </div>
                <div class="card-body">
                    @if($serviceBooking->serviceItems && $serviceBooking->serviceItems->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Service</th>
                                        <th>Staff</th>
                                        <th>Amount</th>
                                        <th>Notes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($serviceBooking->serviceItems as $item)
                                    <tr>
                                        <td>{{ $item->sequence_order }}</td>
                                        <td>{{ $item->service->name ?? 'Unknown Service' }}</td>
                                        <td>{{ $item->staff->name ?? 'Unknown Staff' }}</td>
                                        <td>KSh {{ number_format($item->amount, 2) }}</td>
                                        <td>{{ $item->notes ?? '-' }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted text-center py-3">No services recorded for this booking.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
