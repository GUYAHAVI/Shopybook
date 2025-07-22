@extends('layouts.dash')

@section('content')
<div class="container py-4">
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
                <!-- Desktop Table View -->
                <div class="d-none d-md-block">
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
                                        <a href="{{ route('service-bookings.show', $booking) }}" 
                                           class="btn btn-sm btn-outline-info me-1"
                                           title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('service-bookings.edit', $booking) }}" 
                                           class="btn btn-sm btn-outline-primary me-1"
                                           title="Edit Booking">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-danger" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#deleteModal" 
                                                data-booking-id="{{ $booking->id }}"
                                                data-customer-name="{{ $booking->customer->name ?? 'Walk-in Customer' }}"
                                                data-service-date="{{ $booking->service_date ? $booking->service_date->format('M d, Y H:i') : 'Unknown' }}"
                                                title="Delete Booking">
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
                    @foreach($serviceBookings as $booking)
                        @php
                            $statusClass = match($booking->payment_status) {
                                'paid' => 'bg-success',
                                'pending' => 'bg-warning',
                                default => 'bg-danger'
                            };
                            $statusText = ucfirst($booking->payment_status ?? 'unknown');
                            $services = $booking->serviceItems && $booking->serviceItems->count() > 0 
                                ? $booking->serviceItems->pluck('service.name')->filter()->join(', ')
                                : 'No services';
                            $staff = $booking->serviceItems && $booking->serviceItems->count() > 0
                                ? $booking->serviceItems->pluck('staff.name')->filter()->unique()->join(', ')
                                : '-';
                        @endphp
                        
                        <div class="mobile-card-item mb-3">
                            <div class="mobile-card-header">
                                <h6 class="mobile-card-title text-truncate">{{ $booking->customer->name ?? 'Walk-in Customer' }}</h6>
                                <span class="badge {{ $statusClass }} mobile-card-badge">{{ $statusText }}</span>
                            </div>
                            
                            <div class="mobile-card-content">
                                <!-- Services Section - Full Width -->
                                <div class="mobile-card-full-section mb-3">
                                    <small class="mobile-card-label">Services</small>
                                    <div class="mobile-card-services-block">
                                        @if($booking->serviceItems && $booking->serviceItems->count() > 0)
                                            <div class="services-list">
                                                @foreach($booking->serviceItems as $item)
                                                    <span class="service-badge">
                                                        {{ $item->service->name ?? 'Service' }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-muted">No services</span>
                                        @endif
                                    </div>
                                </div>
                                
                                <!-- Staff Section - Full Width Below Services -->
                                @if($staff !== '-')
                                <div class="mobile-card-full-section mb-3">
                                    <small class="mobile-card-label">Staff Members</small>
                                    <div class="mobile-card-staff-block">
                                        <i class="fas fa-users me-2 text-muted"></i>{{ $staff }}
                                    </div>
                                </div>
                                @endif
                                
                                <!-- Amount and Date in Grid -->
                                <div class="mobile-card-grid">
                                    <div class="mobile-card-grid-item">
                                        <small class="mobile-card-label">Amount</small>
                                        <div class="mobile-card-value text-success fw-semibold">
                                            <i class="fas fa-dollar-sign me-1"></i>KSh {{ number_format($booking->total_amount ?? 0, 2) }}
                                        </div>
                                    </div>
                                    <div class="mobile-card-grid-item">
                                        <small class="mobile-card-label">Date</small>
                                        <div class="mobile-card-value">
                                            <i class="fas fa-calendar me-1 text-muted"></i>{{ $booking->service_date ? $booking->service_date->format('M d, Y') : ($booking->created_at ? $booking->created_at->format('M d, Y') : 'Unknown') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mobile-card-actions">
                                <a href="{{ route('service-bookings.show', $booking) }}" class="btn btn-sm btn-outline-info">
                                    <i class="fas fa-eye"></i><span class="d-none d-sm-inline ms-1">View</span>
                                </a>
                                <a href="{{ route('service-bookings.edit', $booking) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-edit"></i><span class="d-none d-sm-inline ms-1">Edit</span>
                                </a>
                                <button type="button" class="btn btn-sm btn-outline-danger" 
                                        onclick="openDeleteBookingModal('{{ $booking->id }}', '{{ addslashes($booking->customer->name ?? 'Walk-in Customer') }}', '{{ $booking->service_date ? $booking->service_date->format('M d, Y H:i') : 'Unknown' }}')">
                                    <i class="fas fa-trash"></i><span class="d-none d-sm-inline ms-1">Delete</span>
                                </button>
                            </div>
                        </div>
                    @endforeach
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

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteModalLabel">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Confirm Booking Deletion
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning" role="alert">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Warning:</strong> This action cannot be undone. The booking will be permanently removed from your system.
                </div>

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
                    Delete Service Booking
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
    padding: 0.875rem;
    margin-bottom: 0.875rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.08);
}

.mobile-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.75rem;
    padding-bottom: 0.5rem;
    border-bottom: 1px solid #f1f3f4;
}

.mobile-card-title {
    font-weight: 600;
    font-size: 0.95rem;
    margin: 0;
    color: #2c3e50;
    flex: 1;
    line-height: 1.2;
}

.mobile-card-badge {
    font-size: 0.7rem;
    padding: 0.25rem 0.5rem;
    margin-left: 0.5rem;
    font-weight: 500;
}

.mobile-card-content {
    margin-bottom: 0.75rem;
}

/* New responsive section layout */
.mobile-card-section {
    margin-bottom: 0.75rem;
}

/* Full-width sections for services and staff */
.mobile-card-full-section {
    margin-bottom: 1rem;
    background: #f8f9fa;
    padding: 0.75rem;
    border-radius: 8px;
    border-left: 4px solid #007bff;
}

.mobile-card-services-block {
    margin-top: 0.5rem;
}

.services-list {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    align-items: flex-start;
}

.service-badge {
    background: linear-gradient(135deg, #007bff, #0056b3);
    color: white;
    padding: 0.375rem 0.75rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 500;
    white-space: nowrap;
    box-shadow: 0 2px 4px rgba(0,123,255,0.3);
    transition: all 0.2s ease;
}

.service-badge:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0,123,255,0.4);
}

.mobile-card-staff-block {
    background: #fff;
    padding: 0.5rem 0.75rem;
    border-radius: 6px;
    border: 1px solid #e9ecef;
    font-size: 0.875rem;
    color: #495057;
    font-weight: 500;
    margin-top: 0.5rem;
}

.mobile-card-label {
    display: block;
    font-weight: 600;
    color: #6c757d;
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 0.25rem;
}

.mobile-card-value-block {
    background: #f8f9fa;
    padding: 0.5rem;
    border-radius: 4px;
    font-size: 0.85rem;
    line-height: 1.3;
    color: #495057;
    border-left: 3px solid #007bff;
}

.mobile-card-value {
    font-size: 0.85rem;
    color: #495057;
    font-weight: 500;
    line-height: 1.2;
}

/* Grid layout for compact items */
.mobile-card-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0.75rem;
}

.mobile-card-grid-item {
    text-align: center;
}

@media (max-width: 375px) {
    .mobile-card-grid {
        grid-template-columns: 1fr;
        gap: 0.5rem;
    }
    
    .mobile-card-grid-item {
        text-align: left;
    }
}

/* Legacy field styles for compatibility */
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
    font-size: 0.8rem;
}

.mobile-card-field span {
    color: #212529;
    width: 60%;
    text-align: right;
    font-size: 0.85rem;
}

.mobile-card-actions {
    display: flex;
    gap: 0.375rem;
    flex-wrap: wrap;
    padding-top: 0.5rem;
    border-top: 1px solid #f1f3f4;
}

.mobile-card-actions .btn {
    font-size: 0.8rem;
    padding: 0.25rem 0.5rem;
    flex: 1;
    min-width: 60px;
}

/* Small screen optimizations */
@media (max-width: 576px) {
    .mobile-card-item {
        padding: 0.75rem;
        margin-bottom: 0.75rem;
    }
    
    .mobile-card-title {
        font-size: 0.9rem;
    }
    
    .mobile-card-full-section {
        padding: 0.625rem;
        margin-bottom: 0.875rem;
    }
    
    .service-badge {
        font-size: 0.75rem;
        padding: 0.3rem 0.6rem;
    }
    
    .mobile-card-staff-block {
        font-size: 0.8rem;
        padding: 0.4rem 0.6rem;
    }
    
    .mobile-card-label {
        font-size: 0.7rem;
    }
    
    .mobile-card-value,
    .mobile-card-value-block {
        font-size: 0.8rem;
    }
    
    .mobile-card-actions .btn {
        font-size: 0.75rem;
        padding: 0.2rem 0.4rem;
    }
    
    .mobile-card-grid {
        grid-template-columns: 1fr;
        gap: 0.5rem;
    }
    
    .mobile-card-grid-item {
        text-align: left;
        background: #f8f9fa;
        padding: 0.5rem;
        border-radius: 6px;
    }
}

@media (max-width: 375px) {
    .service-badge {
        font-size: 0.7rem;
        padding: 0.25rem 0.5rem;
    }
    
    .mobile-card-staff-block {
        font-size: 0.75rem;
    }
    
    .mobile-card-full-section {
        padding: 0.5rem;
    }
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
                showError(data.message || 'An error occurred while deleting the booking.');
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
});

// Helper function for mobile cards to open delete modal
function openDeleteBookingModal(bookingId, customerName, serviceDate) {
    // Set form action
    document.getElementById('deleteForm').action = `/service-bookings/${bookingId}`;
    
    // Update modal content
    document.getElementById('modal-customer-name').textContent = customerName;
    document.getElementById('modal-service-date').textContent = serviceDate;
    
    // Show the modal
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    deleteModal.show();
}
</script>
@endsection