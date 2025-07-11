@extends('layouts.dash')
@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card p-4">
                <h2 class="fw-bold mb-4" style="color:#020258;">Edit Service</h2>
                <form method="POST" action="{{ route('services.update', $service) }}">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label for="name" class="form-label">Service Name *</label>
                        <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $service->name) }}" required>
                        @error('name')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label for="price" class="form-label">Price (KSh) *</label>
                        <input type="number" step="0.01" class="form-control" id="price" name="price" value="{{ old('price', $service->price) }}" required>
                        @error('price')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label for="duration" class="form-label">Duration (minutes)</label>
                        <input type="number" class="form-control" id="duration" name="duration" value="{{ old('duration', $service->duration) }}">
                        @error('duration')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label for="commission_rate" class="form-label">Commission Rate (%)</label>
                        <input type="number" step="0.01" class="form-control" id="commission_rate" name="commission_rate" value="{{ old('commission_rate', $service->commission_rate) }}">
                        @error('commission_rate')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description">{{ old('description', $service->description) }}</textarea>
                        @error('description')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <div class="d-flex justify-content-end">
                        <a href="{{ route('services.index') }}" class="btn btn-outline-secondary me-2">Cancel</a>
                        <button type="submit" class="btn btn-primary">Update Service</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection 