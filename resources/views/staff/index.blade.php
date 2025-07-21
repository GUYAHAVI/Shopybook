@extends('layouts.dash')
@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold" style="color:#020258;">Staff</h2>
        <a href="{{ route('staff.create') }}" class="btn btn-primary"><i class="fas fa-plus me-1"></i> Add Staff</a>
    </div>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <div class="card p-3">
        <table class="table table-hover align-middle">
            <thead>
                <tr style="color:#020258;">
                    <th>Name</th>
                    <th>Role</th>
                    <th>Commission (%)</th>
                    <th>Contact</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($staff as $member)
                <tr>
                    <td>{{ $member->name }}</td>
                    <td>{{ $member->role }}</td>
                    <td>{{ $member->commission_rate ?? '-' }}</td>
                    <td>{{ $member->contact }}</td>
                    <td>
                        <a href="{{ route('staff.edit', $member) }}" class="btn btn-sm btn-outline-primary" title="Edit Staff">
                            <i class="fas fa-edit"></i>
                        </a>
                        <button type="button" class="btn btn-sm btn-outline-danger" 
                                onclick="openDeleteStaffModal('{{ $member->id }}', '{{ $member->name }}')" 
                                title="Delete Staff">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center">No staff found.</td></tr>
                @endforelse
            </tbody>
        </table>
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
                <p>Are you sure you want to delete <strong id="staffNameToDelete"></strong>?</p>
                <p class="text-danger fw-bold">This action cannot be undone!</p>
                
                <form id="deleteStaffForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="password" id="staffFormPassword">
                    <div class="mb-3">
                        <label for="staffPasswordConfirmation" class="form-label">Enter your password to confirm:</label>
                        <input type="password" class="form-control" id="staffPasswordConfirmation" required>
                        <div id="staffPasswordError" class="invalid-feedback"></div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" id="confirmDeleteStaffBtn" class="btn btn-danger">Delete Staff</button>
            </div>
        </div>
    </div>
</div>

<script>
function openDeleteStaffModal(staffId, staffName) {
    // Set the staff name in the modal
    document.getElementById('staffNameToDelete').textContent = staffName;
    
    // Set the form action
    document.getElementById('deleteStaffForm').action = `/staff/${staffId}`;
    
    // Clear previous password input and errors
    document.getElementById('staffPasswordConfirmation').value = '';
    document.getElementById('staffPasswordError').style.display = 'none';
    
    // Show the modal
    const modal = new bootstrap.Modal(document.getElementById('deleteStaffModal'));
    modal.show();
}

// Handle delete confirmation
document.getElementById('confirmDeleteStaffBtn').addEventListener('click', function() {
    const password = document.getElementById('staffPasswordConfirmation').value;
    const passwordError = document.getElementById('staffPasswordError');
    
    if (!password) {
        passwordError.textContent = 'Password is required';
        passwordError.style.display = 'block';
        return;
    }
    
    // Set the password in the hidden field
    document.getElementById('staffFormPassword').value = password;
    
    // Submit the form
    document.getElementById('deleteStaffForm').submit();
});

// Clear password error when user types
document.getElementById('staffPasswordConfirmation').addEventListener('input', function() {
    document.getElementById('staffPasswordError').style.display = 'none';
});
</script>

<style>
/* Modal styling to match business delete modal */
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

.modal-danger .modal-body .text-danger {
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
@endsection 