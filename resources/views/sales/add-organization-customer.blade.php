@extends('layouts.dash')

@section('content')
<div class="container-fluid px-4">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card shadow mt-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Add Company/Organization Customer</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('sales.organization-customers.store') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="name" class="form-label">Company/Organization Name *</label>
                            <input type="text" class="form-control" id="name" name="name" required value="{{ old('name') }}">
                            @error('name')<div class="text-danger small">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}">
                            @error('email')<div class="text-danger small">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone Number *</label>
                            <input type="text" class="form-control" id="phone" name="phone" required value="{{ old('phone') }}">
                            @error('phone')<div class="text-danger small">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label for="kra_pin" class="form-label">KRA PIN</label>
                            <input type="text" class="form-control" id="kra_pin" name="kra_pin" value="{{ old('kra_pin') }}">
                            @error('kra_pin')<div class="text-danger small">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label for="address" class="form-label">Address</label>
                            <textarea class="form-control" id="address" name="address" rows="2">{{ old('address') }}</textarea>
                            @error('address')<div class="text-danger small">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label for="city" class="form-label">City</label>
                            <input type="text" class="form-control" id="city" name="city" value="{{ old('city') }}">
                            @error('city')<div class="text-danger small">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label for="country" class="form-label">Country</label>
                            <input type="text" class="form-control" id="country" name="country" value="{{ old('country', 'Kenya') }}">
                            @error('country')<div class="text-danger small">{{ $message }}</div>@enderror
                        </div>
                        <div class="d-flex justify-content-end">
                            <a href="{{ route('sales.customers') }}" class="btn btn-secondary me-2">Cancel</a>
                            <button type="submit" class="btn btn-primary">Add Company/Organization</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 