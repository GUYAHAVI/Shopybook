@extends('layouts.dash')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold" style="color:#020258;">Staff</h2>
        <a href="{{ route('staff.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Add Staff
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            @if($staff && count($staff) > 0)
                <!-- Desktop Table View -->
                <div class="d-none d-md-block">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr style="color:#020258;">
                                    <th>Name</th>
                                    <th>Role</th>
                                    <th>Commission (%)</th>
                                    <th>Contact</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($staff as $member)
                                <tr>
                                    <td>{{ $member->name ?? 'Unknown' }}</td>
                                    <td>{{ $member->role ?? '-' }}</td>
                                    <td>{{ ($member->commission_rate ?? null) ? $member->commission_rate.'%' : '-' }}</td>
                                    <td>{{ $member->contact ?? '-' }}</td>
                                    <td>
                                        <a href="{{ route('staff.edit', $member) }}" 
                                           class="btn btn-sm btn-outline-primary me-1" 
                                           title="Edit Staff">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-danger" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#deleteStaffModal" 
                                                data-staff-id="{{ $member->id }}"
                                                data-staff-name="{{ $member->name ?? 'Unknown' }}"
                                                title="Delete Staff">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Mobile Card View -->
                <div class="d-block d-md-none">
                    @foreach($staff as $member)
                        <div class="mobile-card-item mb-3">
                            <div class="mobile-card-header">
                                <h5 class="mobile-card-title">{{ $member->name ?? 'Unknown' }}</h5>
                                <span class="badge bg-primary mobile-card-badge">
                                    {{ $member->role ?? 'Staff' }}
                                </span>
                            </div>
                            <div class="mobile-card-content">
                                <div class="mobile-card-field">
                                    <label>Commission:</label>
                                    <span>{{ ($member->commission_rate ?? null) ? $member->commission_rate.'%' : '-' }}</span>
                                </div>
                                <div class="mobile-card-field">
                                    <label>Contact:</label>
                                    <span>{{ $member->contact ?? '-' }}</span>
                                </div>
                            </div>
                            <div class="mobile-card-actions">
                                <a href="{{ route('staff.edit', $member) }}" 
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-edit me-1"></i> Edit
                                </a>
                                <button type="button" 
                                        class="btn btn-sm btn-outline-danger"
                                        data-bs-toggle="modal" 
                                        data-bs-target="#deleteStaffModal"
                                        data-staff-id="{{ $member->id }}"
                                        data-staff-name="{{ $member->name ?? 'Unknown' }}">
                                    <i class="fas fa-trash me-1"></i> Delete
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                    <h5>No Staff Members Found</h5>
                    <p class="text-muted">Add your first staff member to get started.</p>
                    <a href="{{ route('staff.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i> Add Staff Member
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Delete Staff Modal -->
<div class="modal fade" id="deleteStaffModal" tabindex="-1" aria-labelledby="deleteStaffModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content modal-danger">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteStaffModalLabel">
                    <i class="fas fa-exclamation-triangle"></i> Confirm Staff Deletion
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning mb-3">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Warning:</strong> This action cannot be undone. The staff member will be permanently removed.
                </div>

                <p>You are about to delete:</p>
                <div class="bg-light p-3 rounded mb-3">
                    <strong id="staffNameToDelete"></strong>
                </div>
                
                <form id="deleteStaffForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="password" id="staffFormPassword">
                    <div class="mb-3">
                        <label for="staffPasswordConfirmation" class="form-label">
                            <i class="fas fa-lock me-1"></i>
                            Enter your password to confirm:
                        </label>
                        <input type="password" 
                               class="form-control" 
                               id="staffPasswordConfirmation" 
                               required
                               placeholder="Your account password"
                               autocomplete="current-password">
                        <div id="staffPasswordError" class="invalid-feedback"></div>
                        <div class="form-text text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            This is a security measure to prevent accidental deletions.
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>
                    Cancel
                </button>
                <button type="button" id="confirmDeleteStaffBtn" class="btn btn-danger">
                    <i class="fas fa-trash me-1"></i>
                    Delete Staff
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.mobile-card-item {
    background: #ffffff;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 1rem;
    margin-bottom: 1rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.mobile-card-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 0.75rem;
}

.mobile-card-title {
    font-weight: 600;
    margin: 0;
    flex: 1;
}

.mobile-card-badge {
    font-size: 0.75rem;
    margin-left: 0.5rem;
}

.mobile-card-content {
    margin-bottom: 0.75rem;
}

.mobile-card-field {
    display: flex;
    justify-content: space-between;
    padding: 0.25rem 0;
    border-bottom: 1px solid #f8f9fa;
}

.mobile-card-field:last-child {
    border-bottom: none;
}

.mobile-card-field label {
    font-weight: 500;
    color: #6c757d;
    margin: 0;
    width: 40%;
}

.mobile-card-field span {
    color: #212529;
    width: 60%;
    text-align: right;
}

.mobile-card-actions {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.mobile-card-actions .btn {
    font-size: 0.875rem;
}

.modal-danger .modal-content {
    background: var(--white);
    border: 1px solid var(--danger-color);
    border-radius: 10px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
}

.modal-danger .modal-header {
    background: var(--danger-color);
    color: var(--white);
    border-bottom: none;
    border-radius: 10px 10px 0 0;
}

.modal-danger .modal-title {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: 600;
}

.modal-danger .modal-title .fa-exclamation-triangle {
    color: var(--white);
    font-size: 1.2rem;
}

.modal-danger .modal-body {
    background: var(--white);
    color: var(--gray-800);
    padding: 2rem;
}

.modal-danger .text-danger {
    color: var(--danger-color) !important;
}

.modal-danger .form-label {
    color: var(--gray-700);
    font-weight: 500;
}

.modal-danger .form-control {
    border: 1px solid var(--gray-300);
    background: var(--white);
    color: var(--gray-800);
}

.modal-danger .form-control:focus {
    border-color: var(--danger-color);
    box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
}

.modal-danger .btn-danger {
    background: var(--danger-color);
    border: 1px solid var(--danger-color);
    color: var(--white);
    font-weight: 600;
    padding: 0.5rem 1.5rem;
}

.modal-danger .btn-danger:hover {
    background: #c82333;
    border-color: #c82333;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const deleteStaffModal = document.getElementById('deleteStaffModal');
    const deleteStaffForm = document.getElementById('deleteStaffForm');
    const confirmDeleteStaffBtn = document.getElementById('confirmDeleteStaffBtn');
    const passwordInput = document.getElementById('staffPasswordConfirmation');
    const passwordError = document.getElementById('staffPasswordError');

    // Handle modal show event
    deleteStaffModal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const staffId = button.getAttribute('data-staff-id');
        const staffName = button.getAttribute('data-staff-name');

        // Update modal content
        document.getElementById('staffNameToDelete').textContent = staffName;
        
        // Set form action
        deleteStaffForm.action = `/staff/${staffId}`;
        
        // Reset form and hide error
        passwordInput.value = '';
        passwordError.style.display = 'none';
        
        // Focus password field after modal is fully shown
        setTimeout(() => {
            passwordInput.focus();
        }, 500);
    });

    // Handle delete confirmation
    confirmDeleteStaffBtn.addEventListener('click', function() {
        const password = passwordInput.value.trim();
        
        if (!password) {
            passwordError.textContent = 'Password is required';
            passwordError.style.display = 'block';
            return;
        }
        
        // Set the password in the hidden field
        document.getElementById('staffFormPassword').value = password;
        
        // Submit the form
        deleteStaffForm.submit();
    });

    // Clear password error when user types
    passwordInput.addEventListener('input', function() {
        passwordError.style.display = 'none';
    });
});
</script>
@endsection