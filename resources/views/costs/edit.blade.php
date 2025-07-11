@extends('layouts.dash')
@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card p-4">
                <h2 class="fw-bold mb-4" style="color:#020258;">Edit Cost</h2>
                <form method="POST" action="{{ route('costs.update', $cost) }}">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label for="type" class="form-label">Type *</label>
                        <select class="form-control" id="type" name="type" required>
                            <option value="">Select Type</option>
                            <option value="utility" @if(old('type', $cost->type)=='utility') selected @endif>Utility</option>
                            <option value="product" @if(old('type', $cost->type)=='product') selected @endif>Product</option>
                            <option value="rent" @if(old('type', $cost->type)=='rent') selected @endif>Rent</option>
                            <option value="water" @if(old('type', $cost->type)=='water') selected @endif>Water</option>
                            <option value="misc" @if(old('type', $cost->type)=='misc') selected @endif>Miscellaneous</option>
                            <option value="activity" @if(old('type', $cost->type)=='activity') selected @endif>Activity</option>
                            <option value="renovation" @if(old('type', $cost->type)=='renovation') selected @endif>Renovation</option>
                            <option value="other" @if(old('type', $cost->type)=='other') selected @endif>Other</option>
                        </select>
                        @error('type')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label for="amount" class="form-label">Amount (KSh) *</label>
                        <input type="number" step="0.01" class="form-control" id="amount" name="amount" value="{{ old('amount', $cost->amount) }}" required>
                        @error('amount')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <input type="text" class="form-control" id="description" name="description" value="{{ old('description', $cost->description) }}">
                        @error('description')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label for="date" class="form-label">Date *</label>
                        <input type="date" class="form-control" id="date" name="date" value="{{ old('date', $cost->date) }}" required>
                        @error('date')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <div class="d-flex justify-content-end">
                        <a href="{{ route('costs.index') }}" class="btn btn-outline-secondary me-2">Cancel</a>
                        <button type="submit" class="btn btn-primary">Update Cost</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection 