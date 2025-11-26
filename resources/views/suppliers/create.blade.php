@extends('layouts.dash')

@section('content')
<div class="container-fluid py-4">
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5>Add New Supplier</h5>
                <a href="{{ route('suppliers.index') }}" class="btn btn-sm btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Suppliers
                </a>
            </div>
        </div>
        <div class="card-body">
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong><i class="fas fa-exclamation-circle me-2"></i>Error!</strong> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong><i class="fas fa-exclamation-triangle me-2"></i>Please fix the following errors:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <form action="{{ route('suppliers.store') }}" method="POST">
                @csrf
                
                <div class="row">
                    <!-- Basic Information -->
                    <div class="col-md-12 mb-3">
                        <h6 class="text-primary mb-3"><i class="fas fa-info-circle me-2"></i>Basic Information</h6>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Supplier Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               name="name" value="{{ old('name') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Contact Person</label>
                        <input type="text" class="form-control @error('contact_person') is-invalid @enderror" 
                               name="contact_person" value="{{ old('contact_person') }}">
                        @error('contact_person')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" 
                               name="email" value="{{ old('email') }}">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Phone</label>
                        <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                               name="phone" value="{{ old('phone') }}">
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Address Information -->
                    <div class="col-md-12 mb-3 mt-3">
                        <h6 class="text-primary mb-3"><i class="fas fa-map-marker-alt me-2"></i>Address Information</h6>
                    </div>

                    <div class="col-md-12 mb-3">
                        <label class="form-label">Address</label>
                        <textarea class="form-control @error('address') is-invalid @enderror" 
                                  name="address" rows="2">{{ old('address') }}</textarea>
                        @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">City</label>
                        <input type="text" class="form-control @error('city') is-invalid @enderror" 
                               name="city" value="{{ old('city') }}">
                        @error('city')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Country</label>
                        <input type="text" class="form-control @error('country') is-invalid @enderror" 
                               name="country" value="{{ old('country', 'Kenya') }}">
                        @error('country')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Business Details -->
                    <div class="col-md-12 mb-3 mt-3">
                        <h6 class="text-primary mb-3"><i class="fas fa-building me-2"></i>Business Details</h6>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Company Registration Number</label>
                        <input type="text" class="form-control @error('company_registration') is-invalid @enderror" 
                               name="company_registration" value="{{ old('company_registration') }}">
                        @error('company_registration')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tax Number / KRA PIN</label>
                        <input type="text" class="form-control @error('tax_number') is-invalid @enderror" 
                               name="tax_number" value="{{ old('tax_number') }}">
                        @error('tax_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Payment Terms -->
                    <div class="col-md-12 mb-3 mt-3">
                        <h6 class="text-primary mb-3"><i class="fas fa-money-check-alt me-2"></i>Payment Terms</h6>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Payment Terms</label>
                        <select class="form-select @error('payment_terms') is-invalid @enderror" name="payment_terms">
                            <option value="">Select payment terms...</option>
                            <option value="Net 0 (Immediate)" {{ old('payment_terms') == 'Net 0 (Immediate)' ? 'selected' : '' }}>Net 0 (Immediate)</option>
                            <option value="Net 7" {{ old('payment_terms') == 'Net 7' ? 'selected' : '' }}>Net 7 (7 days)</option>
                            <option value="Net 15" {{ old('payment_terms') == 'Net 15' ? 'selected' : '' }}>Net 15 (15 days)</option>
                            <option value="Net 30" {{ old('payment_terms') == 'Net 30' ? 'selected' : '' }}>Net 30 (30 days)</option>
                            <option value="Net 60" {{ old('payment_terms') == 'Net 60' ? 'selected' : '' }}>Net 60 (60 days)</option>
                            <option value="Net 90" {{ old('payment_terms') == 'Net 90' ? 'selected' : '' }}>Net 90 (90 days)</option>
                            <option value="COD (Cash on Delivery)" {{ old('payment_terms') == 'COD (Cash on Delivery)' ? 'selected' : '' }}>COD (Cash on Delivery)</option>
                            <option value="Advance Payment" {{ old('payment_terms') == 'Advance Payment' ? 'selected' : '' }}>Advance Payment</option>
                        </select>
                        @error('payment_terms')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Credit Limit (KSh)</label>
                        <input type="number" step="0.01" class="form-control @error('credit_limit') is-invalid @enderror" 
                               name="credit_limit" value="{{ old('credit_limit', 0) }}" min="0">
                        @error('credit_limit')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Additional Information -->
                    <div class="col-md-12 mb-3 mt-3">
                        <h6 class="text-primary mb-3"><i class="fas fa-clipboard me-2"></i>Additional Information</h6>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Status <span class="text-danger">*</span></label>
                        <select class="form-select @error('status') is-invalid @enderror" name="status" required>
                            <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-12 mb-3">
                        <label class="form-label">Notes</label>
                        <textarea class="form-control @error('notes') is-invalid @enderror" 
                                  name="notes" rows="3">{{ old('notes') }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Create Supplier
                    </button>
                    <a href="{{ route('suppliers.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times me-2"></i>Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

