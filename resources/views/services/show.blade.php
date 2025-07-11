@extends('layouts.dash')
@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card p-4">
                <h2 class="fw-bold mb-4" style="color:#020258;">Service Details</h2>
                <dl class="row">
                    <dt class="col-sm-4">Name</dt>
                    <dd class="col-sm-8">{{ $service->name }}</dd>
                    <dt class="col-sm-4">Price</dt>
                    <dd class="col-sm-8">KSh {{ number_format($service->price,2) }}</dd>
                    <dt class="col-sm-4">Duration</dt>
                    <dd class="col-sm-8">{{ $service->duration ? $service->duration.' min' : '-' }}</dd>
                    <dt class="col-sm-4">Commission Rate</dt>
                    <dd class="col-sm-8">{{ $service->commission_rate ?? '-' }}%</dd>
                    <dt class="col-sm-4">Description</dt>
                    <dd class="col-sm-8">{{ $service->description }}</dd>
                </dl>
                <div class="d-flex justify-content-end">
                    <a href="{{ route('services.edit', $service) }}" class="btn btn-primary me-2">Edit</a>
                    <a href="{{ route('services.index') }}" class="btn btn-outline-secondary">Back</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 