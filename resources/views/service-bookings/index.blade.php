@extends('layouts.dash')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="fw-bold" style="color:#020258;">Service Bookings</h2>
                <a href="{{ route('service-bookings.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i> Book Service
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
                <div class="card-header">
                    <h5 class="mb-0">Service Bookings List</h5>
                </div>
                <div class="card-body">
                    @if($serviceBookings && $serviceBookings->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Customer</th>
                                        <th>Services</th>
                                        <th>Staff</th>
                                        <th>Amount</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($serviceBookings as $booking)
                                    <tr>
                                        <td>{{ $booking->customer->name ?? 'Walk-in Customer' }}</td>
                                        <td>
                                            @if($booking->serviceItems && $booking->serviceItems->count() > 0)
                                                @foreach($booking->serviceItems as $item)
                                                    <span class="badge bg-primary me-1">
                                                        {{ $item->service->name ?? 'Service' }}
                                                    </span>
                                                @endforeach
                                            @else
                                                <span class="text-muted">No services</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($booking->serviceItems && $booking->serviceItems->count() > 0)
                                                {{ $booking->serviceItems->pluck('staff.name')->filter()->unique()->join(', ') }}
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>KSh {{ number_format($booking->total_amount ?? 0, 2) }}</td>
                                        <td>
                                            {{ $booking->service_date ? $booking->service_date->format('M d, Y H:i') : ($booking->created_at ? $booking->created_at->format('M d, Y') : 'Unknown') }}
                                        </td>
                                        <td>
                                            @if($booking->payment_status == 'paid')
                                                <span class="badge bg-success">Paid</span>
                                            @elseif($booking->payment_status == 'pending')
                                                <span class="badge bg-warning">Pending</span>
                                            @else
                                                <span class="badge bg-danger">Cancelled</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('service-bookings.show', $booking) }}" class="btn btn-sm btn-outline-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('service-bookings.edit', $booking) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-danger" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#deleteModal" 
                                                    data-booking-id="{{ $booking->id }}"
                                                    data-customer-name="{{ $booking->customer->name ?? 'Walk-in Customer' }}"
                                                    data-service-date="{{ $booking->service_date ? $booking->service_date->format('M d, Y H:i') : 'Unknown' }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        @if($serviceBookings->hasPages())
                            <div class="d-flex justify-content-center mt-3">
                                {{ $serviceBookings->links() }}
                            </div>
                        @endif
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-calendar-check fa-3x text-muted mb-3"></i>
                            <h5>No Service Bookings Found</h5>
                            <p class="text-muted">Create your first service booking to get started.</p>
                            <a href="{{ route('service-bookings.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-1"></i> Create Service Booking
                            </a>
                        </div>
                    @endif
                </div>
            </div>
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
                    Confirm Service Booking Deletion
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
                            <div class="col-sm-4"><strong>Customer:</strong></div>
                            <div class="col-sm-8" id="modal-customer-name">-</div>
                        </div>
                        <div class="row">
                            <div class="col-sm-4"><strong>Service Date:</strong></div>
                            <div class="col-sm-8" id="modal-service-date">-</div>
                        </div>
                    </div>
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
                    Delete Service Booking
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
        const bookingId = button.getAttribute('data-booking-id');
        const customerName = button.getAttribute('data-customer-name');
        const serviceDate = button.getAttribute('data-service-date');

        // Update modal content
        document.getElementById('modal-customer-name').textContent = customerName;
        document.getElementById('modal-service-date').textContent = serviceDate;
        
        // Set form action
        deleteForm.action = `/service-bookings/${bookingId}`;
        
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

        // Get the booking ID from the form action
        const formAction = deleteForm.action;
        const bookingId = formAction.split('/').pop();

        // Show loading state
        confirmDeleteBtn.disabled = true;
        deleteSpinner.classList.remove('d-none');
        errorAlert.classList.add('d-none');

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
                
                // Create and show success alert
                const alertDiv = document.createElement('div');
                alertDiv.className = 'alert alert-success alert-dismissible fade show';
                alertDiv.innerHTML = `
                    <i class="fas fa-check-circle me-2"></i>
                    ${data.message || 'Service booking deleted successfully.'}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                `;
                
                // Insert alert at the top of the container
                const container = document.querySelector('.container.py-4');
                const firstChild = container.firstElementChild;
                container.insertBefore(alertDiv, firstChild);
                
                // Remove the deleted row from the table
                const deletedRow = document.querySelector(`button[data-booking-id="${bookingId}"]`)?.closest('tr');
                if (deletedRow) {
                    deletedRow.style.transition = 'opacity 0.5s';
                    deletedRow.style.opacity = '0';
                    setTimeout(() => {
                        deletedRow.remove();
                        
                        // Check if table is now empty
                        const tbody = document.querySelector('tbody');
                        if (tbody && tbody.children.length === 0) {
                            // Reload page to show "no bookings" message
                            setTimeout(() => {
                                window.location.reload();
                            }, 1000);
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
                showError(data.message || 'An error occurred while deleting the service booking.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showError('An unexpected error occurred. Please try again.');
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

    // Check for commission summary from localStorage (after successful booking)
    document.addEventListener('DOMContentLoaded', function() {
        const commissionSummary = localStorage.getItem('commission_summary');
        
        if (commissionSummary) {
            try {
                const summary = JSON.parse(commissionSummary);
                showCommissionSummary(summary);
                localStorage.removeItem('commission_summary');
            } catch (e) {
                console.error('Error parsing commission summary:', e);
            }
        }
    });

    function showCommissionSummary(summary) {
        if (Object.keys(summary).length === 0) return;

        let summaryHtml = '<h6 class="mb-3">Commission Breakdown by Staff Member:</h6>';
        let totalCommission = 0;

        Object.values(summary).forEach(staff => {
            summaryHtml += `
                <div class="card mb-3">
                    <div class="card-header">
                        <h6 class="mb-0">${staff.name}</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Service</th>
                                        <th>Price</th>
                                        <th>Commission Rate</th>
                                        <th>Commission Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
            `;
            
            staff.services.forEach(service => {
                summaryHtml += `
                    <tr>
                        <td>${service.serviceName}${service.isComplimentary ? ' (Complimentary)' : ''}</td>
                        <td>KSh ${service.servicePrice.toFixed(2)}</td>
                        <td>${service.commissionRate}%</td>
                        <td>KSh ${service.commissionAmount.toFixed(2)}</td>
                    </tr>
                `;
            });
            
            summaryHtml += `
                                </tbody>
                                <tfoot>
                                    <tr class="table-primary">
                                        <th colspan="3">Total Commission for ${staff.name}:</th>
                                        <th>KSh ${staff.totalCommission.toFixed(2)}</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            `;
            
            totalCommission += staff.totalCommission;
        });
        
        summaryHtml += `
            <div class="alert alert-success">
                <strong>Total Commission for All Staff: KSh ${totalCommission.toFixed(2)}</strong>
            </div>
        `;
        
        document.getElementById('commission-summary-content').innerHTML = summaryHtml;
        const modal = new bootstrap.Modal(document.getElementById('commissionSummaryModal'));
        modal.show();
    }
});

<!-- Commission Summary Modal -->
<div class="modal fade" id="commissionSummaryModal" tabindex="-1" aria-labelledby="commissionSummaryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="commissionSummaryModalLabel">Commission Summary - Last Booking</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="commission-summary-content">
                    <!-- Commission summary will be populated here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

</script>
@endsection
