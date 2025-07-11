@extends('layouts.dash')
@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card p-4">
                <h2 class="fw-bold mb-4" style="color:#020258;">Service Record Details</h2>
                <dl class="row">
                    <dt class="col-sm-4">Service</dt>
                    <dd class="col-sm-8">{{ $serviceRecord->service->name ?? '-' }}</dd>
                    <dt class="col-sm-4">Staff</dt>
                    <dd class="col-sm-8">{{ $serviceRecord->staff->name ?? '-' }}</dd>
                    <dt class="col-sm-4">Customer</dt>
                    <dd class="col-sm-8">{{ $serviceRecord->customer->name ?? '-' }}</dd>
                    <dt class="col-sm-4">Amount</dt>
                    <dd class="col-sm-8">KSh {{ number_format($serviceRecord->amount,2) }}</dd>
                    <dt class="col-sm-4">Commission</dt>
                    <dd class="col-sm-8">KSh {{ number_format($serviceRecord->commission,2) }}</dd>
                    <dt class="col-sm-4">Date</dt>
                    <dd class="col-sm-8">{{ $serviceRecord->performed_at ? $serviceRecord->performed_at->format('Y-m-d H:i') : '-' }}</dd>
                    <dt class="col-sm-4">Notes</dt>
                    <dd class="col-sm-8">{{ $serviceRecord->notes }}</dd>
                </dl>
                <div class="d-flex justify-content-end">
                    <a href="{{ route('service-records.edit', $serviceRecord) }}" class="btn btn-primary me-2">Edit</a>
                    <a href="{{ route('service-records.index') }}" class="btn btn-outline-secondary">Back</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 