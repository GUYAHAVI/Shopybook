@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <!-- Header -->
            <div class="mb-4">
                <h1 class="h3 mb-0" style="color: var(--text-primary);">Create Contact Group</h1>
                <p class="text-muted">Organize your contacts into groups for better management</p>
            </div>

            <div class="card shadow-sm" style="background: var(--card-bg); border: 1px solid var(--border-color);">
                <div class="card-body">
                    <form action="{{ route('contacts.store-group') }}" method="POST">
                        @csrf

                        <div class="form-group">
                            <label for="name" style="color: var(--text-primary);">
                                Group Name <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name') }}" 
                                   required
                                   placeholder="e.g., VIP Customers, Sales Team"
                                   style="background: var(--bg-secondary); border: 1px solid var(--border-color); color: var(--text-primary);">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="type" style="color: var(--text-primary);">
                                Group Type <span class="text-danger">*</span>
                            </label>
                            <select class="form-control @error('type') is-invalid @enderror" 
                                    id="type" 
                                    name="type" 
                                    required
                                    style="background: var(--bg-secondary); border: 1px solid var(--border-color); color: var(--text-primary);">
                                <option value="customers" {{ old('type') === 'customers' ? 'selected' : '' }}>Customers</option>
                                <option value="staff" {{ old('type') === 'staff' ? 'selected' : '' }}>Staff/Employees</option>
                                <option value="suppliers" {{ old('type') === 'suppliers' ? 'selected' : '' }}>Suppliers</option>
                                <option value="custom" {{ old('type') === 'custom' ? 'selected' : '' }}>Custom</option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                Select the type of contacts this group will contain
                            </small>
                        </div>

                        <div class="form-group">
                            <label for="description" style="color: var(--text-primary);">
                                Description
                            </label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" 
                                      name="description" 
                                      rows="3"
                                      placeholder="Brief description of this contact group..."
                                      style="background: var(--bg-secondary); border: 1px solid var(--border-color); color: var(--text-primary);">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-0">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Create Group
                            </button>
                            <a href="{{ route('contacts.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection




