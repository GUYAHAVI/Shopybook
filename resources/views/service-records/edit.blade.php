@extends('layouts.dash')
@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card p-4">
                <h2 class="fw-bold mb-4" style="color:#020258;">Edit Service Record</h2>
                <form method="POST" action="{{ route('service-records.update', $serviceRecord) }}">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label for="service_id" class="form-label">Service *</label>
                        <select class="form-control" id="service_id" name="service_id" required>
                            <option value="">Select Service</option>
                            @foreach($services as $service)
                                <option value="{{ $service->id }}" @if(old('service_id', $serviceRecord->service_id) == $service->id) selected @endif>{{ $service->name }}</option>
                            @endforeach
                        </select>
                        @error('service_id')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label for="staff_id" class="form-label">Staff *</label>
                        <select class="form-control" id="staff_id" name="staff_id" required>
                            <option value="">Select Staff</option>
                            @foreach($staff as $member)
                                <option value="{{ $member->id }}" @if(old('staff_id', $serviceRecord->staff_id) == $member->id) selected @endif>{{ $member->name }}</option>
                            @endforeach
                        </select>
                        @error('staff_id')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label for="customer_id" class="form-label">Customer</label>
                        <select class="form-control" id="customer_id" name="customer_id">
                            <option value="">Walk-in/Anonymous</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}" @if(old('customer_id', $serviceRecord->customer_id) == $customer->id) selected @endif>{{ $customer->name }}</option>
                            @endforeach
                        </select>
                        @error('customer_id')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label for="amount" class="form-label">Amount (KSh) *</label>
                        <input type="number" step="0.01" class="form-control" id="amount" name="amount" value="{{ old('amount', $serviceRecord->amount) }}" required>
                        @error('amount')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label for="commission" class="form-label">Commission (KSh) *</label>
                        <input type="number" step="0.01" class="form-control" id="commission" name="commission" value="{{ old('commission', $serviceRecord->commission) }}" required>
                        @error('commission')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label for="performed_at" class="form-label">Date & Time *</label>
                        <input type="datetime-local" class="form-control" id="performed_at" name="performed_at" value="{{ old('performed_at', $serviceRecord->performed_at ? $serviceRecord->performed_at->format('Y-m-d\TH:i') : '') }}" required>
                        @error('performed_at')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea class="form-control" id="notes" name="notes">{{ old('notes', $serviceRecord->notes) }}</textarea>
                        @error('notes')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <div class="d-flex justify-content-end">
                        <a href="{{ route('service-records.index') }}" class="btn btn-outline-secondary me-2">Cancel</a>
                        <button type="submit" class="btn btn-primary">Update Record</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection 