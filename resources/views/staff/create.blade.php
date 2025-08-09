@extends('layouts.dash')
@section('content')
<div class="container py-4">
    <!-- Sub-navigation for Staff -->
    <div class="sub-navigation mb-4">
        <div class="nav-tabs">
            <a href="{{ route('staff.index') }}" class="nav-tab">
                <i class="fas fa-users me-1"></i> All Staff
            </a>
            <a href="{{ route('staff.create') }}" class="nav-tab active">
                <i class="fas fa-plus me-1"></i> Add Staff
            </a>
            <a href="{{ route('salary-advances.index') }}" class="nav-tab">
                <i class="fas fa-money-bill-wave me-1"></i> Salary Advances
            </a>
            <a href="{{ route('staff.salary-calculations') }}" class="nav-tab">
                <i class="fas fa-calculator me-1"></i> Salary Calculations
            </a>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card p-4" style="background: var(--card-bg); border: 1px solid var(--border-color);">
                <h2 class="fw-bold mb-4" style="color: var(--text-primary);">Add Staff</h2>
                <form method="POST" action="{{ route('staff.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label for="name" class="form-label" style="color: var(--text-primary);">Name *</label>
                        <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required
                               style="border: 1px solid var(--border-color); background: var(--card-bg); color: var(--text-primary);">
                        @error('name')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label for="role" class="form-label" style="color: var(--text-primary);">Role *</label>
                        <input type="text" class="form-control" id="role" name="role" value="{{ old('role') }}" required
                               style="border: 1px solid var(--border-color); background: var(--card-bg); color: var(--text-primary);">
                        @error('role')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label for="salary" class="form-label" style="color: var(--text-primary);">Salary (KSh)</label>
                        <input type="number" class="form-control" id="salary" name="salary" value="{{ old('salary') }}" placeholder="Enter monthly salary"
                               style="border: 1px solid var(--border-color); background: var(--card-bg); color: var(--text-primary);">
                        @error('salary')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label for="contact" class="form-label" style="color: var(--text-primary);">Contact</label>
                        <input type="text" class="form-control" id="contact" name="contact" value="{{ old('contact') }}"
                               style="border: 1px solid var(--border-color); background: var(--card-bg); color: var(--text-primary);">
                        @error('contact')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('staff.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Back to Staff
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Add Staff
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.sub-navigation {
    background: var(--card-bg);
    border: 1px solid var(--border-color);
    border-radius: 0.5rem;
    padding: 1rem;
    margin-bottom: 1.5rem;
}

.nav-tabs {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.nav-tab {
    display: flex;
    align-items: center;
    padding: 0.5rem 1rem;
    color: var(--text-muted);
    text-decoration: none;
    border-radius: 0.375rem;
    font-size: 0.875rem;
    font-weight: 500;
    transition: all 0.2s ease;
    border: 1px solid transparent;
}

.nav-tab:hover {
    color: var(--text-primary);
    background: var(--bg-tertiary);
    border-color: var(--border-color);
}

.nav-tab.active {
    color: var(--white);
    background: var(--primary-color);
    border-color: var(--primary-color);
}

.form-control:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 0.2rem rgba(19, 232, 233, 0.25);
}

/* Mobile responsiveness */
@media (max-width: 768px) {
    .nav-tabs {
        flex-direction: column;
        gap: 0.25rem;
    }
    
    .nav-tab {
        justify-content: center;
        padding: 0.75rem 1rem;
    }
    
    .d-flex.justify-content-between {
        flex-direction: column;
        gap: 1rem;
    }
    
    .d-flex.justify-content-between .btn {
        width: 100%;
        justify-content: center;
    }
}
</style>
@endsection 