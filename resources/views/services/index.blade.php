@extends('layouts.dash')
@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold" style="color:#020258;">Services</h2>
        <a href="{{ route('services.create') }}" class="btn btn-primary"><i class="fas fa-plus me-1"></i> Add Service</a>
    </div>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <div class="card p-3">
        <table class="table table-hover align-middle">
            <thead>
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
                @forelse($services as $service)
                <tr>
                    <td>
                        <div class="d-flex align-items-center">
                            <div>
                                <div class="fw-medium">{{ $service->name }}</div>
                                @if($service->is_complimentary)
                                    <small class="badge bg-info">Complimentary</small>
                                @endif
                                @if($service->is_bundle_trigger)
                                    <small class="badge bg-warning">Bundle Trigger</small>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td>KSh {{ number_format($service->price,2) }}</td>
                    <td>{{ $service->duration ? $service->duration.' min' : '-' }}</td>
                    <td>{{ $service->commission_rate ?? '-' }}</td>
                    <td>{{ $service->description }}</td>
                    <td>
                        <a href="{{ route('services.edit', $service) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a>
                        <button type="button" class="btn btn-sm btn-outline-danger" 
                                data-bs-toggle="modal" 
                                data-bs-target="#deleteModal" 
                                data-service-id="{{ $service->id }}"
                                data-service-name="{{ $service->name }}"
                                data-service-price="KSh {{ number_format($service->price, 2) }}">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center">No services found.</td></tr>
                @endforelse
            </tbody>
        </table>
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
                    <i class="fas fa-shield-alt me-2"></i>
                    <strong>Security Required:</strong> This action requires your account password for verification.
                </div>
                
                <div class="mb-3">
                    <p class="fw-bold mb-2">You are about to delete:</p>
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
                </div>

                <div class="alert alert-info" role="alert">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Important:</strong> Deleting this service may affect existing service bookings and records. Services with active bookings cannot be deleted.
                </div>

                <div id="delete-error-alert" class="alert alert-danger d-none" role="alert">
                    <i class="fas fa-times-circle me-2"></i>
                    <span id="delete-error-message"></span>
                </div>

                <form id="deleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    
                    <div class="mb-3">
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
    const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
    const passwordInput = document.getElementById('password');
    const deleteSpinner = document.getElementById('delete-spinner');
    const errorAlert = document.getElementById('delete-error-alert');
    const errorMessage = document.getElementById('delete-error-message');

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
        errorAlert.classList.add('d-none');
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
            return;
        }

        // Get the service ID from the form action
        const formAction = deleteForm.action;
        const serviceId = formAction.split('/').pop();

        // Show loading state
        confirmDeleteBtn.disabled = true;
        deleteSpinner.classList.remove('d-none');
        errorAlert.classList.add('d-none');

        // Create form data and ensure method override is included
        const formData = new FormData(deleteForm);
        
        // Ensure the DELETE method is properly set
        if (!formData.has('_method')) {
            formData.append('_method', 'DELETE');
        }
        
        console.log('Form data entries:');
        for (let [key, value] of formData.entries()) {
            console.log(key, value);
        }

        // Submit via fetch
        fetch(deleteForm.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => {
            console.log('Response status:', response.status);
            console.log('Response headers:', response.headers);
            console.log('Response content-type:', response.headers.get('content-type'));
            
            // Check if response is actually JSON
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                // Log the actual response text for debugging
                return response.text().then(text => {
                    console.log('Response text:', text.substring(0, 200) + '...');
                    throw new Error('Server returned HTML instead of JSON. This usually indicates a server error or redirect.');
                });
            }
            
            if (!response.ok) {
                // Handle different HTTP status codes
                if (response.status === 422) {
                    return response.json().then(data => {
                        throw new Error(data.message || 'Validation failed');
                    });
                } else if (response.status === 403) {
                    return response.json().then(data => {
                        throw new Error(data.message || 'Access denied');
                    });
                } else if (response.status === 500) {
                    return response.json().then(data => {
                        throw new Error(data.message || 'Server error occurred');
                    });
                }
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Close modal
                const modal = bootstrap.Modal.getInstance(deleteModal);
                modal.hide();
                
                // Create and show success alert
                const alertDiv = document.createElement('div');
                alertDiv.className = 'alert alert-success alert-dismissible fade show';
                alertDiv.innerHTML = `
                    <i class="fas fa-check-circle me-2"></i>
                    ${data.message || 'Service deleted successfully.'}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                `;
                
                // Insert alert at the top of the container
                const container = document.querySelector('.container.py-4');
                const firstChild = container.firstElementChild;
                container.insertBefore(alertDiv, firstChild);
                
                // Remove the deleted row from the table
                const deletedRow = document.querySelector(`button[data-service-id="${serviceId}"]`)?.closest('tr');
                if (deletedRow) {
                    deletedRow.style.transition = 'opacity 0.5s';
                    deletedRow.style.opacity = '0';
                    setTimeout(() => {
                        deletedRow.remove();
                        
                        // Check if table is now empty
                        const tbody = document.querySelector('tbody');
                        if (tbody && tbody.children.length === 1 && tbody.children[0].cells[0].textContent.includes('No services found')) {
                            // Already shows "no services" message
                        } else if (tbody && tbody.children.length === 0) {
                            // Add "no services" row
                            const noServicesRow = document.createElement('tr');
                            noServicesRow.innerHTML = '<td colspan="6" class="text-center">No services found.</td>';
                            tbody.appendChild(noServicesRow);
                        }
                    }, 500);
                }
                
                // Auto-dismiss alert after 5 seconds
                setTimeout(() => {
                    if (alertDiv && alertDiv.parentNode) {
                        alertDiv.remove();
                    }
                }, 5000);
                
            } else {
                showError(data.message || 'An error occurred while deleting the service.');
            }
        })
        .catch(error => {
            console.error('Error details:', error);
            
            // Handle different types of errors
            if (error.message.includes('HTML instead of JSON')) {
                showError('Server error: The server returned an error page instead of the expected response. Please check the server logs and try again.');
            } else if (error.message.includes('Failed to fetch')) {
                showError('Network error: Please check your connection and try again.');
            } else if (error.message.includes('422') || error.message.includes('validation')) {
                showError(error.message);
            } else {
                showError(error.message || 'An unexpected error occurred. Please try again.');
            }
        })
        .finally(() => {
            // Reset loading state
            confirmDeleteBtn.disabled = false;
            deleteSpinner.classList.add('d-none');
        });
    });

    // Handle Enter key in password field
    passwordInput.addEventListener('keypress', function(event) {
        if (event.key === 'Enter') {
            event.preventDefault();
            confirmDeleteBtn.click();
        }
    });

    function showError(message) {
        errorMessage.textContent = message;
        errorAlert.classList.remove('d-none');
    }
});
</script>
@endsection 