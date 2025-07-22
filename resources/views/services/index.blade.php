@extends('layouts.dash')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold" style="color:#020258;">Services</h2>
        <a href="{{ route('services.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Add Service
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
            @if($services && count($services) > 0)
                <!-- Desktop Table View -->
                <div class="d-none d-md-block">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr style="color:#020258;">
                                    <th>Name</th>
                                    <th>Price</th>
                                    <th>Duration</th>
                                    <th>Commission (%)</th>
                                    <th>Description</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($services as $service)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div>
                                                <div class="fw-medium">{{ $service->name ?? 'Unknown Service' }}</div>
                                                @if($service->is_complimentary)
                                                    <small class="badge bg-info">Complimentary</small>
                                                @endif
                                                @if($service->is_bundle_trigger)
                                                    <small class="badge bg-warning">Bundle Trigger</small>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>KSh {{ number_format($service->price, 2) }}</td>
                                    <td>{{ $service->duration ? $service->duration.' min' : '-' }}</td>
                                    <td>{{ $service->commission_rate ? $service->commission_rate.'%' : '-' }}</td>
                                    <td>{{ $service->description ?? '-' }}</td>
                                    <td>
                                        <a href="{{ route('services.edit', $service) }}" 
                                           class="btn btn-sm btn-outline-primary me-1" 
                                           title="Edit Service">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-danger" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#deleteModal" 
                                                data-service-id="{{ $service->id }}"
                                                data-service-name="{{ $service->name }}"
                                                data-service-price="KSh {{ number_format($service->price, 2) }}"
                                                title="Delete Service">
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
                    @foreach($services as $service)
                        <div class="mobile-card-item mb-3">
                            <div class="mobile-card-header">
                                <h5 class="mobile-card-title">{{ $service->name }}</h5>
                                <span class="badge bg-success mobile-card-badge">
                                    KSh {{ number_format($service->price, 2) }}
                                </span>
                            </div>
                            <div class="mobile-card-content">
                                <div class="mobile-card-field">
                                    <label>Duration:</label>
                                    <span>{{ $service->duration ? $service->duration.' min' : '-' }}</span>
                                </div>
                                <div class="mobile-card-field">
                                    <label>Commission:</label>
                                    <span>{{ $service->commission_rate ? $service->commission_rate.'%' : '-' }}</span>
                                </div>
                                <div class="mobile-card-field">
                                    <label>Description:</label>
                                    <span>{{ $service->description ?? '-' }}</span>
                                </div>
                                <div class="mobile-card-field">
                                    <label>Special Features:</label>
                                    <span>
                                        @if($service->is_complimentary) Complimentary @endif
                                        @if($service->is_bundle_trigger) @if($service->is_complimentary), @endif Bundle Trigger @endif
                                        @if(!$service->is_complimentary && !$service->is_bundle_trigger) None @endif
                                    </span>
                                </div>
                            </div>
                            <div class="mobile-card-actions">
                                <a href="{{ route('services.edit', $service) }}" 
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-edit me-1"></i> Edit
                                </a>
                                <button type="button" 
                                        class="btn btn-sm btn-outline-danger"
                                        onclick="openDeleteServiceModal('{{ $service->id }}', '{{ addslashes($service->name) }}', 'KSh {{ number_format($service->price, 2) }}')">
                                    <i class="fas fa-trash me-1"></i> Delete
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-concierge-bell fa-3x text-muted mb-3"></i>
                    <h5>No Services Found</h5>
                    <p class="text-muted">Create your first service to get started.</p>
                    <a href="{{ route('services.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i> Create Service
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteModalLabel">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Confirm Service Deletion
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning" role="alert">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Warning:</strong> This action cannot be undone. The service will be permanently removed from your system.
                </div>

                <div class="bg-light p-3 rounded border">
                    <div class="row mb-2">
                        <div class="col-sm-4"><strong>Service Name:</strong></div>
                        <div class="col-sm-8" id="modal-service-name">-</div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4"><strong>Price:</strong></div>
                        <div class="col-sm-8" id="modal-service-price">-</div>
                    </div>
                </div>

                <div id="delete-error-alert" class="alert alert-danger d-none mt-3" role="alert">
                    <i class="fas fa-times-circle me-2"></i>
                    <span id="delete-error-message"></span>
                </div>

                <form id="deleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    
                    <div class="mt-3">
                        <label for="password" class="form-label">
                            <i class="fas fa-lock me-1"></i>
                            Enter your account password to confirm deletion:
                        </label>
                        <input type="password" 
                               class="form-control" 
                               id="password" 
                               name="password" 
                               required
                               placeholder="Your account password"
                               autocomplete="current-password">
                        <div class="form-text">
                            <i class="fas fa-info-circle me-1"></i>
                            This is a permanent action and cannot be undone.
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>
                    Cancel
                </button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                    <span class="spinner-border spinner-border-sm d-none me-2" id="delete-spinner"></span>
                    <i class="fas fa-trash me-1"></i>
                    Delete Service
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

.modal-content {
    box-shadow: 0 10px 30px rgba(0,0,0,0.3);
}

.modal-header.bg-danger {
    border-bottom: 2px solid #c82333;
}

#deleteModal .form-control:focus {
    border-color: #dc3545;
    box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
}

#deleteModal .alert-warning {
    border-left: 4px solid #ffc107;
}

#deleteModal .alert-info {
    border-left: 4px solid #0dcaf0;
}

.fade-out-row {
    opacity: 0;
    transition: opacity 0.5s ease-out;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const deleteModal = document.getElementById('deleteModal');
    const deleteForm = document.getElementById('deleteForm');
    const passwordInput = document.getElementById('password');
    const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
    const deleteSpinner = document.getElementById('delete-spinner');
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
    deleteModal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const serviceId = button.getAttribute('data-service-id');
        const serviceName = button.getAttribute('data-service-name');
        const servicePrice = button.getAttribute('data-service-price');

        // Update modal content
        document.getElementById('modal-service-name').textContent = serviceName;
        document.getElementById('modal-service-price').textContent = servicePrice;
        
        // Set form action
        deleteForm.action = `/services/${serviceId}`;
        
        // Reset form and hide error
        passwordInput.value = '';
        hideError();
        confirmDeleteBtn.disabled = false;
        deleteSpinner.classList.add('d-none');
        
        // Focus password field after modal is fully shown
        setTimeout(() => {
            passwordInput.focus();
        }, 500);
    });

    // Handle delete confirmation
    confirmDeleteBtn.addEventListener('click', function() {
        const password = passwordInput.value.trim();
        
        if (!password) {
            showError('Please enter your password.');
            passwordInput.focus();
            return;
        }

        // Show loading state
        confirmDeleteBtn.disabled = true;
        deleteSpinner.classList.remove('d-none');
        hideError();

        // Create form data
        const formData = new FormData(deleteForm);

        // Submit via fetch
        fetch(deleteForm.action, {
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
                const modal = bootstrap.Modal.getInstance(deleteModal);
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
                showError(data.message || 'An error occurred while deleting the service.');
                confirmDeleteBtn.disabled = false;
                deleteSpinner.classList.add('d-none');
            }
        })
        .catch(error => {
            console.error('Delete error:', error);
            showError('An unexpected error occurred. Please try again.');
            confirmDeleteBtn.disabled = false;
            deleteSpinner.classList.add('d-none');
        });
    });

    // Handle Enter key in password field
    passwordInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            confirmDeleteBtn.click();
        }
    });

    // Helper function for mobile cards
    window.openDeleteServiceModal = function(serviceId, serviceName, servicePrice) {
        const modal = new bootstrap.Modal(deleteModal);
        
        // Set the data attributes on the modal trigger
        const tempButton = document.createElement('button');
        tempButton.setAttribute('data-service-id', serviceId);
        tempButton.setAttribute('data-service-name', serviceName);
        tempButton.setAttribute('data-service-price', servicePrice);
        
        // Trigger the modal show event
        const event = new Event('show.bs.modal');
        event.relatedTarget = tempButton;
        deleteModal.dispatchEvent(event);
        
        modal.show();
    };
});
</script>
@endsection