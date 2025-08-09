@extends('layouts.dash')

@section('content')
<div class="container py-4">
    <!-- Sub-navigation for Staff -->
    <div class="sub-navigation mb-4">
        <div class="nav-tabs">
            <a href="{{ route('staff.index') }}" class="nav-tab active">
                <i class="fas fa-users me-1"></i> All Staff
            </a>
            <a href="{{ route('staff.create') }}" class="nav-tab">
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

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold" style="color: var(--text-primary);">Staff Management</h2>
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

    <div class="card shadow-sm" style="background: var(--card-bg); border: 1px solid var(--border-color);">
        <div class="card-body">
            @if($staff && count($staff) > 0)
                <!-- Desktop Table View -->
                <div class="d-none d-md-block">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead style="background-color: var(--bg-tertiary);">
                                <tr>
                                    <th style="color: var(--text-secondary); border-bottom: 2px solid var(--border-color);">Name</th>
                                    <th style="color: var(--text-secondary); border-bottom: 2px solid var(--border-color);">Role</th>
                                    <th style="color: var(--text-secondary); border-bottom: 2px solid var(--border-color);">Salary (KSh)</th>
                                    <th style="color: var(--text-secondary); border-bottom: 2px solid var(--border-color);">Contact</th>
                                    <th style="color: var(--text-secondary); border-bottom: 2px solid var(--border-color);">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($staff as $member)
                                <tr>
                                    <td style="color: var(--text-primary);">{{ $member->name ?? 'Unknown' }}</td>
                                    <td style="color: var(--text-primary);">{{ $member->role ?? '-' }}</td>
                                    <td style="color: var(--text-primary);">{{ ($member->salary ?? null) ? 'KSh ' . number_format($member->salary) : '-' }}</td>
                                    <td style="color: var(--text-primary);">{{ $member->contact ?? '-' }}</td>
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
                        <div class="mobile-card-item mb-3" style="background: var(--card-bg); border: 1px solid var(--border-color); box-shadow: 0 1px 3px var(--shadow-color);">
                            <div class="mobile-card-header">
                                <h5 class="mobile-card-title" style="color: var(--text-primary);">{{ $member->name ?? 'Unknown' }}</h5>
                                <span class="badge bg-primary mobile-card-badge">
                                    {{ $member->role ?? 'Staff' }}
                                </span>
                            </div>
                            <div class="mobile-card-content">
                                <div class="mobile-card-field">
                                    <label style="color: var(--text-secondary);">Salary:</label>
                                    <span style="color: var(--text-primary);">{{ ($member->salary ?? null) ? 'KSh ' . number_format($member->salary) : '-' }}</span>
                                </div>
                                <div class="mobile-card-field">
                                    <label style="color: var(--text-secondary);">Contact:</label>
                                    <span style="color: var(--text-primary);">{{ $member->contact ?? '-' }}</span>
                                </div>
                            </div>
                            <div class="mobile-card-actions">
                                <a href="{{ route('staff.edit', $member) }}" 
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-edit me-1"></i> Edit
                                </a>
                                <button type="button" 
                                        class="btn btn-sm btn-outline-danger"
                                        onclick="openDeleteStaffModal('{{ $member->id }}', '{{ addslashes($member->name ?? 'Unknown') }}')">
                                    <i class="fas fa-trash me-1"></i> Delete
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-users fa-3x" style="color: var(--text-muted);" class="mb-3"></i>
                    <h5 style="color: var(--text-primary);">No Staff Found</h5>
                    <p style="color: var(--text-muted);">Add your first staff member to get started.</p>
                    <a href="{{ route('staff.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i> Add Staff
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteStaffModal" tabindex="-1" aria-labelledby="deleteStaffModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background: var(--card-bg); border: 1px solid var(--border-color);">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteStaffModalLabel">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Confirm Staff Deletion
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning" role="alert">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Warning:</strong> This action cannot be undone. The staff member will be permanently removed from your system.
                </div>

                <div class="bg-light p-3 rounded border" style="background: var(--bg-tertiary) !important; border: 1px solid var(--border-color) !important;">
                    <div class="row mb-2">
                        <div class="col-sm-4"><strong style="color: var(--text-primary);">Staff Name:</strong></div>
                        <div class="col-sm-8" id="modal-staff-name" style="color: var(--text-primary);">-</div>
                    </div>
                </div>

                <div id="delete-error-alert" class="alert alert-danger d-none mt-3" role="alert">
                    <i class="fas fa-times-circle me-2"></i>
                    <span id="delete-error-message"></span>
                </div>

                <form id="deleteStaffForm" method="POST">
                    @csrf
                    @method('DELETE')
                    
                    <div class="mt-3">
                        <label for="password" class="form-label" style="color: var(--text-primary);">
                            <i class="fas fa-lock me-1"></i>
                            Enter your account password to confirm deletion:
                        </label>
                        <input type="password" 
                               class="form-control" 
                               id="password" 
                               name="password" 
                               required
                               placeholder="Your account password"
                               autocomplete="current-password"
                               style="border: 1px solid var(--border-color); background: var(--card-bg); color: var(--text-primary);">
                        <div class="form-text" style="color: var(--text-muted);">
                            <i class="fas fa-info-circle me-1"></i>
                            This is a permanent action and cannot be undone.
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer" style="border-top: 1px solid var(--border-color);">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>
                    Cancel
                </button>
                <button type="button" class="btn btn-danger" id="confirmDeleteStaffBtn">
                    <span class="spinner-border spinner-border-sm d-none me-2" id="delete-staff-spinner"></span>
                    <i class="fas fa-trash me-1"></i>
                    Delete Staff
                </button>
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

.mobile-card-item {
    background: var(--card-bg);
    border: 1px solid var(--border-color);
    border-radius: 8px;
    padding: 1rem;
    margin-bottom: 1rem;
    box-shadow: 0 1px 3px var(--shadow-color);
}

.mobile-card-item:hover {
    box-shadow: 0 4px 6px var(--shadow-color);
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
    color: var(--text-primary);
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
    border-bottom: 1px solid var(--border-color);
}

.mobile-card-field:last-child {
    border-bottom: none;
}

.mobile-card-field label {
    font-weight: 500;
    color: var(--text-secondary);
    margin: 0;
    width: 40%;
}

.mobile-card-field span {
    color: var(--text-primary);
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

.modal-content {
    box-shadow: 0 10px 30px var(--shadow-color);
}

.modal-header.bg-danger {
    border-bottom: 2px solid #c82333;
}

#deleteStaffModal .form-control:focus {
    border-color: #dc3545;
    box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
}

#deleteStaffModal .alert-warning {
    border-left: 4px solid #ffc107;
}

.fade-out-row {
    opacity: 0;
    transition: opacity 0.5s ease-out;
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
    
    .mobile-card-actions {
        flex-direction: column;
    }
    
    .mobile-card-actions .btn {
        width: 100%;
        justify-content: center;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const deleteStaffModal = document.getElementById('deleteStaffModal');
    const deleteStaffForm = document.getElementById('deleteStaffForm');
    const passwordInput = document.getElementById('password');
    const confirmDeleteStaffBtn = document.getElementById('confirmDeleteStaffBtn');
    const deleteStaffSpinner = document.getElementById('delete-staff-spinner');
    const errorAlert = document.getElementById('delete-error-alert');
    const errorMessage = document.getElementById('delete-error-message');

    function showError(message) {
        errorMessage.textContent = message;
        errorAlert.classList.remove('d-none');
    }

    function hideError() {
        errorAlert.classList.add('d-none');
    }

    // Handle modal show event
    deleteStaffModal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const staffId = button.getAttribute('data-staff-id');
        const staffName = button.getAttribute('data-staff-name');

        // Update modal content
        document.getElementById('modal-staff-name').textContent = staffName;
        
        // Set form action
        deleteStaffForm.action = `/staff/${staffId}`;
        
        // Reset form and hide error
        passwordInput.value = '';
        hideError();
        confirmDeleteStaffBtn.disabled = false;
        deleteStaffSpinner.classList.add('d-none');
        
        // Focus password field after modal is fully shown
        setTimeout(() => {
            passwordInput.focus();
        }, 500);
    });

    // Handle delete confirmation
    confirmDeleteStaffBtn.addEventListener('click', function() {
        const password = passwordInput.value.trim();
        
        if (!password) {
            showError('Please enter your password.');
            passwordInput.focus();
            return;
        }

        // Show loading state
        confirmDeleteStaffBtn.disabled = true;
        deleteStaffSpinner.classList.remove('d-none');
        hideError();

        // Create form data
        const formData = new FormData(deleteStaffForm);

        // Submit via fetch
        fetch(deleteStaffForm.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Close modal
                const modal = bootstrap.Modal.getInstance(deleteStaffModal);
                modal.hide();
                
                // Show success message and reload page
                const successAlert = document.createElement('div');
                successAlert.className = 'alert alert-success alert-dismissible fade show position-fixed';
                successAlert.style.cssText = 'top: 20px; right: 20px; z-index: 1060; min-width: 300px;';
                successAlert.innerHTML = `
                    <i class="fas fa-check-circle me-2"></i>
                    ${data.message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                `;
                document.body.appendChild(successAlert);
                
                // Reload page after short delay
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else {
                showError(data.message || 'An error occurred while deleting the staff member.');
                confirmDeleteStaffBtn.disabled = false;
                deleteStaffSpinner.classList.add('d-none');
            }
        })
        .catch(error => {
            console.error('Delete error:', error);
            showError('An unexpected error occurred. Please try again.');
            confirmDeleteStaffBtn.disabled = false;
            deleteStaffSpinner.classList.add('d-none');
        });
    });

    // Handle Enter key in password field
    passwordInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            confirmDeleteStaffBtn.click();
        }
    });

    // Helper function for mobile cards
    window.openDeleteStaffModal = function(staffId, staffName) {
        const modal = new bootstrap.Modal(deleteStaffModal);
        
        // Set the data attributes on the modal trigger
        const tempButton = document.createElement('button');
        tempButton.setAttribute('data-staff-id', staffId);
        tempButton.setAttribute('data-staff-name', staffName);
        
        // Trigger the modal show event
        const event = new Event('show.bs.modal');
        event.relatedTarget = tempButton;
        deleteStaffModal.dispatchEvent(event);
        
        modal.show();
    };
});
</script>
@endsection