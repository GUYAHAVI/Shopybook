@extends('layouts.dash')
@section('content')
<div class="container py-4">
    <!-- Sub-navigation for Services -->
    <div class="sub-navigation mb-4">
        <div class="nav-tabs">
            <a href="{{ route('services.index') }}" class="nav-tab">
                <i class="fas fa-list me-1"></i> All Services
            </a>
            <a href="{{ route('services.create') }}" class="nav-tab">
                <i class="fas fa-plus me-1"></i> Add Service
            </a>
            <a href="{{ route('service-bookings.index') }}" class="nav-tab">
                <i class="fas fa-calendar-check me-1"></i> Bookings
            </a>
            <a href="{{ route('service-bookings.create') }}" class="nav-tab">
                <i class="fas fa-plus-circle me-1"></i> New Booking
            </a>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card p-4" style="background: var(--card-bg); border: 1px solid var(--border-color);">
                <h2 class="fw-bold mb-4" style="color: var(--text-primary);">Edit Service Booking</h2>
                <form method="POST" action="{{ route('service-bookings.update', $serviceBooking) }}">
                    @csrf
                    @method('PUT')
                    
                    @php
                        $firstItem = $serviceBooking->serviceItems->first();
                    @endphp
                    
                    <div class="mb-3">
                        <label for="service_id" class="form-label" style="color: var(--text-primary);">Service *</label>
                        <select class="form-control" id="service_id" name="service_id" required
                                style="border: 1px solid var(--border-color); background: var(--card-bg); color: var(--text-primary);">
                            <option value="">Select Service</option>
                            @foreach($services as $service)
                                <option value="{{ $service->id }}" 
                                    @if(old('service_id', $firstItem->service_id ?? '') == $service->id) selected @endif>
                                    {{ $service->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('service_id')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="staff_id" class="form-label" style="color: var(--text-primary);">Staff *</label>
                        <select class="form-control" id="staff_id" name="staff_id" required
                                style="border: 1px solid var(--border-color); background: var(--card-bg); color: var(--text-primary);">
                            <option value="">Select Staff</option>
                            @foreach($staff as $member)
                                <option value="{{ $member->id }}" 
                                    @if(old('staff_id', $firstItem->staff_id ?? '') == $member->id) selected @endif>
                                    {{ $member->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('staff_id')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="customer_id" class="form-label" style="color: var(--text-primary);">Customer</label>
                        <select class="form-control" id="customer_id" name="customer_id"
                                style="border: 1px solid var(--border-color); background: var(--card-bg); color: var(--text-primary);">
                            <option value="">Walk-in/Anonymous</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}" 
                                    @if(old('customer_id', $serviceBooking->customer_id) == $customer->id) selected @endif>
                                    {{ $customer->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('customer_id')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="amount" class="form-label" style="color: var(--text-primary);">Amount (KSh) *</label>
                        <input type="number" step="0.01" class="form-control" id="amount" name="amount" 
                               value="{{ old('amount', $serviceBooking->total_amount) }}" required
                               style="border: 1px solid var(--border-color); background: var(--card-bg); color: var(--text-primary);">
                        @error('amount')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="commission" class="form-label" style="color: var(--text-primary);">Commission (KSh) *</label>
                        <input type="number" step="0.01" class="form-control" id="commission" name="commission" 
                               value="{{ old('commission', $firstItem->amount ?? 0) }}" required
                               style="border: 1px solid var(--border-color); background: var(--card-bg); color: var(--text-primary);">
                        @error('commission')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label" style="color: var(--text-primary);">Service Date & Time</label>
                        <input type="text" class="form-control" 
                               value="{{ $serviceBooking->service_date ? $serviceBooking->service_date->format('M d, Y \a\t g:i A') : 'N/A' }}" 
                               readonly style="border: 1px solid var(--border-color); background: var(--bg-tertiary); color: var(--text-primary);">
                        <small style="color: var(--text-muted);">Service date and time cannot be changed after booking.</small>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="payment_status" class="form-label" style="color: var(--text-primary);">Payment Status *</label>
                                <select class="form-control" id="payment_status" name="payment_status" required
                                        style="border: 1px solid var(--border-color); background: var(--card-bg); color: var(--text-primary);">
                                    <option value="paid" @if(old('payment_status', $serviceBooking->payment_status) == 'paid') selected @endif>Paid</option>
                                    <option value="pending" @if(old('payment_status', $serviceBooking->payment_status) == 'pending') selected @endif>Pending</option>
                                    <option value="cancelled" @if(old('payment_status', $serviceBooking->payment_status) == 'cancelled') selected @endif>Cancelled</option>
                                </select>
                                @error('payment_status')<div class="text-danger small">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="payment_method" class="form-label" style="color: var(--text-primary);">Payment Method</label>
                                <select class="form-control" id="payment_method" name="payment_method"
                                        style="border: 1px solid var(--border-color); background: var(--card-bg); color: var(--text-primary);">
                                    <option value="">Select Payment Method</option>
                                    <option value="cash" @if(old('payment_method', $serviceBooking->payment_method) == 'cash') selected @endif>Cash</option>
                                    <option value="mpesa" @if(old('payment_method', $serviceBooking->payment_method) == 'mpesa') selected @endif>M-Pesa</option>
                                    <option value="card" @if(old('payment_method', $serviceBooking->payment_method) == 'card') selected @endif>Card</option>
                                    <option value="bank_transfer" @if(old('payment_method', $serviceBooking->payment_method) == 'bank_transfer') selected @endif>Bank Transfer</option>
                                    <option value="other" @if(old('payment_method', $serviceBooking->payment_method) == 'other') selected @endif>Other</option>
                                </select>
                                @error('payment_method')<div class="text-danger small">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="notes" class="form-label" style="color: var(--text-primary);">Notes</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3" 
                                  placeholder="Any additional notes about this booking"
                                  style="border: 1px solid var(--border-color); background: var(--card-bg); color: var(--text-primary);">{{ old('notes', $serviceBooking->notes) }}</textarea>
                        @error('notes')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('service-bookings.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Back to Bookings
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Update Booking
                        </button>
                    </div>
                </form>
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

.form-control:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 0.2rem rgba(19, 232, 233, 0.25);
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
    
    .d-flex.justify-content-between {
        flex-direction: column;
        gap: 1rem;
    }
    
    .d-flex.justify-content-between .btn {
        width: 100%;
        justify-content: center;
    }
}
</style>
@endsection
