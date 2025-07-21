@extends('layouts.dash')
@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card p-4">
                <h2 class="fw-bold mb-4" style="color:#020258;">Edit Service Booking</h2>
                <form method="POST" action="{{ route('service-bookings.update', $serviceBooking) }}">
                    @csrf
                    @method('PUT')
                    
                    @php
                        $firstItem = $serviceBooking->serviceItems->first();
                    @endphp
                    
                    <div class="mb-3">
                        <label for="service_id" class="form-label">Service *</label>
                        <select class="form-control" id="service_id" name="service_id" required>
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
                        <label for="staff_id" class="form-label">Staff *</label>
                        <select class="form-control" id="staff_id" name="staff_id" required>
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
                        <label for="customer_id" class="form-label">Customer</label>
                        <select class="form-control" id="customer_id" name="customer_id">
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
                        <label for="amount" class="form-label">Amount (KSh) *</label>
                        <input type="number" step="0.01" class="form-control" id="amount" name="amount" 
                               value="{{ old('amount', $serviceBooking->total_amount) }}" required>
                        @error('amount')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="commission" class="form-label">Commission (KSh) *</label>
                        <input type="number" step="0.01" class="form-control" id="commission" name="commission" 
                               value="{{ old('commission', $firstItem->amount ?? 0) }}" required>
                        @error('commission')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Service Date & Time</label>
                        <input type="text" class="form-control" 
                               value="{{ $serviceBooking->service_date ? $serviceBooking->service_date->format('M d, Y \a\t g:i A') : 'N/A' }}" 
                               readonly>
                        <small class="text-muted">Service date and time cannot be changed after booking.</small>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="payment_status" class="form-label">Payment Status *</label>
                                <select class="form-control" id="payment_status" name="payment_status" required>
                                    <option value="paid" @if(old('payment_status', $serviceBooking->payment_status) == 'paid') selected @endif>Paid</option>
                                    <option value="pending" @if(old('payment_status', $serviceBooking->payment_status) == 'pending') selected @endif>Pending</option>
                                    <option value="cancelled" @if(old('payment_status', $serviceBooking->payment_status) == 'cancelled') selected @endif>Cancelled</option>
                                </select>
                                @error('payment_status')<div class="text-danger small">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="payment_method" class="form-label">Payment Method</label>
                                <select class="form-control" id="payment_method" name="payment_method">
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
                        <label for="notes" class="form-label">Notes</label>
                        <textarea class="form-control" id="notes" name="notes">{{ old('notes', $serviceBooking->notes) }}</textarea>
                        @error('notes')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    
                    <div class="d-flex justify-content-end">
                        <a href="{{ route('service-bookings.index') }}" class="btn btn-outline-secondary me-2">Cancel</a>
                        <button type="submit" class="btn btn-primary">Update Booking</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
