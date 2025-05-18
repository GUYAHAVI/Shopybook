@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Create Business Profile</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('business.store') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="form-group">
                            <label for="name">Business Name *</label>
                            <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" 
                                   name="name" value="{{ old('name') }}" required autofocus>
                            @error('name')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>

                        <div class="form-group">
                            <label for="business_type">Business Type *</label>
                            <select id="business_type" class="form-control" name="business_type" required>
                                <option value="">Select type</option>
                                <option value="retail">Retail Shop</option>
                                <option value="restaurant">Restaurant</option>
                                <option value="service">Service Provider</option>
                                <option value="online">Online Store</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="phone">Phone Number *</label>
                            <input id="phone" type="text" class="form-control @error('phone') is-invalid @enderror" 
                                   name="phone" value="{{ old('phone') }}" required>
                            @error('phone')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>

                        <div class="form-group">
                            <label for="description">Business Description</label>
                            <textarea id="description" class="form-control" name="description">{{ old('description') }}</textarea>
                        </div>

                        <div class="form-group">
                            <label for="logo">Business Logo</label>
                            <input id="logo" type="file" class="form-control-file" name="logo">
                        </div>

                        <button type="submit" class="btn btn-primary">
                            Create Business
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection