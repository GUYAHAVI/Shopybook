@extends('layouts.dash')

@section('content')
<div class="container py-4">
    <!-- Sub-navigation for Services -->
    <div class="sub-navigation mb-4">
        <div class="nav-tabs">
            <a href="{{ route('services.index') }}" class="nav-tab">
                <i class="fas fa-list me-1"></i> All Services
            </a>
            <a href="{{ route('services.create') }}" class="nav-tab">
                <i class="fas fa-plus me-1"></i> Add Service
            </a>
            <a href="{{ route('service-bookings.index') }}" class="nav-tab active">
                <i class="fas fa-calendar-check me-1"></i> Bookings
            </a>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold" style="color: var(--text-primary);">Service Bookings</h2>
        <div class="btn-group" role="group">
            <a href="{{ route('service-bookings.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i> Book Service
            </a>
            <a href="{{ route('service-bookings.bulk-create') }}" class="btn btn-outline-primary">
                <i class="fas fa-layer-group me-1"></i> Bulk Entry
            </a>
        </div>
    </div>

    <!-- Daily Reports Section -->
    <div class="card shadow-sm mb-4" style="background: var(--card-bg); border: 1px solid var(--border-color);">
        <div class="card-header" style="background: var(--bg-tertiary); border-bottom: 1px solid var(--border-color);">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0" style="color: var(--text-primary);">
                    <i class="fas fa-chart-line me-2"></i>Daily Sales & Expense Reports
                </h5>
                <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#reportsSection">
                    <i class="fas fa-chevron-down"></i>
                </button>
            </div>
        </div>
        <div class="collapse show" id="reportsSection">
            <div class="card-body">
                <div class="row align-items-end">
                    <div class="col-md-3">
                        <label for="report-date" class="form-label" style="color: var(--text-primary);">Select Date</label>
                        <input type="date" class="form-control" id="report-date" value="{{ date('Y-m-d') }}"
                               style="border: 1px solid var(--border-color); background: var(--card-bg); color: var(--text-primary);">
                    </div>
                    <div class="col-md-3">
                        <div class="d-grid gap-2">
                            <button class="btn btn-primary" id="generate-report-btn">
                                <i class="fas fa-chart-bar me-1"></i> Generate Report
                            </button>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex gap-2">
                            <button class="btn btn-outline-success btn-sm" id="quick-today-btn">
                                <i class="fas fa-calendar-day me-1"></i> Today
                            </button>
                            <button class="btn btn-outline-info btn-sm" id="quick-yesterday-btn">
                                <i class="fas fa-calendar-minus me-1"></i> Yesterday
                            </button>
                            <button class="btn btn-outline-warning btn-sm" id="quick-week-btn">
                                <i class="fas fa-calendar-week me-1"></i> This Week
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Reports Display Area -->
                <div id="reports-display" class="mt-4" style="display: none;">
                    <div class="row">
                        <!-- Sales Summary -->
                        <div class="col-md-6 mb-3">
                            <div class="card border-success">
                                <div class="card-header bg-success text-white">
                                    <h6 class="mb-0"><i class="fas fa-dollar-sign me-2"></i>Sales Summary</h6>
                                </div>
                                <div class="card-body" id="sales-summary">
                                    <!-- Sales data will be loaded here -->
                                </div>
                            </div>
                        </div>
                        
                        <!-- Expenses Summary -->
                        <div class="col-md-6 mb-3">
                            <div class="card border-danger">
                                <div class="card-header bg-danger text-white">
                                    <h6 class="mb-0"><i class="fas fa-receipt me-2"></i>Expenses Summary</h6>
                                </div>
                                <div class="card-body" id="expenses-summary">
                                    <!-- Expenses data will be loaded here -->
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Net Profit/Loss -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card border-primary">
                                <div class="card-header bg-primary text-white">
                                    <h6 class="mb-0"><i class="fas fa-calculator me-2"></i>Daily Summary</h6>
                                </div>
                                <div class="card-body" id="daily-summary">
                                    <!-- Daily summary will be loaded here -->
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Export Options -->
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="d-flex gap-2 justify-content-end">
                                <button class="btn btn-outline-secondary btn-sm" id="print-report-btn">
                                    <i class="fas fa-print me-1"></i> Print Report
                                </button>
                                <button class="btn btn-outline-primary btn-sm" id="export-pdf-btn">
                                    <i class="fas fa-file-pdf me-1"></i> Export PDF
                                </button>
                                <button class="btn btn-outline-success btn-sm" id="export-excel-btn">
                                    <i class="fas fa-file-excel me-1"></i> Export Excel
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
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
        <div class="card-header" style="background: var(--bg-tertiary); border-bottom: 1px solid var(--border-color);">
            <h5 class="mb-0" style="color: var(--text-primary);">Service Bookings List</h5>
        </div>
        <div class="card-body">
            @if($serviceBookings && $serviceBookings->count() > 0)
                <!-- Desktop Table View -->
                <div class="d-none d-md-block">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead style="background-color: var(--bg-tertiary);">
                                <tr>
                                    <th style="color: var(--text-secondary); border-bottom: 2px solid var(--border-color);">Customer</th>
                                    <th style="color: var(--text-secondary); border-bottom: 2px solid var(--border-color);">Services</th>
                                    <th style="color: var(--text-secondary); border-bottom: 2px solid var(--border-color);">Staff</th>
                                    <th style="color: var(--text-secondary); border-bottom: 2px solid var(--border-color);">Amount</th>
                                    <th style="color: var(--text-secondary); border-bottom: 2px solid var(--border-color);">Date</th>
                                    <th style="color: var(--text-secondary); border-bottom: 2px solid var(--border-color);">Status</th>
                                    <th style="color: var(--text-secondary); border-bottom: 2px solid var(--border-color);">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($serviceBookings as $booking)
                                <tr>
                                    <td style="color: var(--text-primary);">{{ $booking->customer->name ?? 'Walk-in Customer' }}</td>
                                    <td>
                                        @if($booking->serviceItems && $booking->serviceItems->count() > 0)
                                            @foreach($booking->serviceItems as $item)
                                                <span class="badge bg-primary me-1">
                                                    {{ $item->service->name ?? 'Service' }}
                                                </span>
                                            @endforeach
                                        @else
                                            <span style="color: var(--text-muted);">No services</span>
                                        @endif
                                    </td>
                                    <td style="color: var(--text-primary);">
                                        @if($booking->serviceItems && $booking->serviceItems->count() > 0)
                                            @php
                                                $assignedStaff = $booking->serviceItems->pluck('staff.name')->filter()->unique();
                                                $unassignedCount = $booking->serviceItems->whereNull('staff_id')->count();
                                            @endphp
                                            @if($assignedStaff->count() > 0)
                                                {{ $assignedStaff->join(', ') }}
                                            @endif
                                            @if($unassignedCount > 0)
                                                @if($assignedStaff->count() > 0)
                                                    <br>
                                                @endif
                                                <span class="badge bg-warning">{{ $unassignedCount }} unassigned</span>
                                            @endif
                                        @else
                                            <span style="color: var(--text-muted);">-</span>
                                        @endif
                                    </td>
                                    <td style="color: var(--text-primary);">KSh {{ number_format($booking->total_amount ?? 0, 2) }}</td>
                                    <td style="color: var(--text-primary);">
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
                        <div class="mobile-card-item mb-3" style="background: var(--card-bg); border: 1px solid var(--border-color); box-shadow: 0 1px 3px var(--shadow-color);">
                            <div class="mobile-card-header">
                                <h5 class="mobile-card-title" style="color: var(--text-primary);">{{ $booking->customer->name ?? 'Walk-in Customer' }}</h5>
                                <span class="badge bg-success mobile-card-badge">
                                    KSh {{ number_format($booking->total_amount ?? 0, 2) }}
                                </span>
                            </div>
                            <div class="mobile-card-content">
                                <div class="mobile-card-field">
                                    <label style="color: var(--text-secondary);">Services:</label>
                                    <span style="color: var(--text-primary);">
                                        @if($booking->serviceItems && $booking->serviceItems->count() > 0)
                                            @foreach($booking->serviceItems as $item)
                                                <span class="badge bg-primary me-1">
                                                    {{ $item->service->name ?? 'Service' }}
                                                </span>
                                            @endforeach
                                        @else
                                            <span style="color: var(--text-muted);">No services</span>
                                        @endif
                                    </span>
                                </div>
                                <div class="mobile-card-field">
                                    <label style="color: var(--text-secondary);">Staff:</label>
                                    <span style="color: var(--text-primary);">
                                        @if($booking->serviceItems && $booking->serviceItems->count() > 0)
                                            @php
                                                $assignedStaff = $booking->serviceItems->pluck('staff.name')->filter()->unique();
                                                $unassignedCount = $booking->serviceItems->whereNull('staff_id')->count();
                                            @endphp
                                            @if($assignedStaff->count() > 0)
                                                {{ $assignedStaff->join(', ') }}
                                            @endif
                                            @if($unassignedCount > 0)
                                                @if($assignedStaff->count() > 0)
                                                    <br>
                                                @endif
                                                <span class="badge bg-warning">{{ $unassignedCount }} unassigned</span>
                                            @endif
                                        @else
                                            <span style="color: var(--text-muted);">-</span>
                                        @endif
                                    </span>
                                </div>
                                <div class="mobile-card-field">
                                    <label style="color: var(--text-secondary);">Date:</label>
                                    <span style="color: var(--text-primary);">
                                        {{ $booking->service_date ? $booking->service_date->format('M d, Y H:i') : ($booking->created_at ? $booking->created_at->format('M d, Y') : 'Unknown') }}
                                    </span>
                                </div>
                                <div class="mobile-card-field">
                                    <label style="color: var(--text-secondary);">Status:</label>
                                    <span>
                                        @if($booking->payment_status == 'paid')
                                            <span class="badge bg-success">Paid</span>
                                        @elseif($booking->payment_status == 'pending')
                                            <span class="badge bg-warning">Pending</span>
                                        @else
                                            <span class="badge bg-danger">Cancelled</span>
                                        @endif
                                    </span>
                                </div>
                            </div>
                            <div class="mobile-card-actions">
                                <a href="{{ route('service-bookings.show', $booking) }}" 
                                   class="btn btn-sm btn-outline-info">
                                    <i class="fas fa-eye me-1"></i> View
                                </a>
                                <a href="{{ route('service-bookings.edit', $booking) }}" 
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-edit me-1"></i> Edit
                                </a>
                                <button type="button" 
                                        class="btn btn-sm btn-outline-danger"
                                        onclick="openDeleteBookingModal('{{ $booking->id }}', '{{ addslashes($booking->customer->name ?? 'Walk-in Customer') }}')">
                                    <i class="fas fa-trash me-1"></i> Delete
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-calendar-check fa-3x" style="color: var(--text-muted);" class="mb-3"></i>
                    <h5 style="color: var(--text-primary);">No Service Bookings Found</h5>
                    <p style="color: var(--text-muted);">Create your first service booking to get started.</p>
                    <a href="{{ route('service-bookings.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i> Create Booking
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background: var(--card-bg); border: 1px solid var(--border-color);">
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

                <div class="bg-light p-3 rounded border" style="background: var(--bg-tertiary) !important; border: 1px solid var(--border-color) !important;">
                    <div class="row mb-2">
                        <div class="col-sm-4"><strong style="color: var(--text-primary);">Customer:</strong></div>
                        <div class="col-sm-8" id="modal-customer-name" style="color: var(--text-primary);">-</div>
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
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                    <span class="spinner-border spinner-border-sm d-none me-2" id="delete-spinner"></span>
                    <i class="fas fa-trash me-1"></i>
                    Delete Booking
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

        // Update modal content
        document.getElementById('modal-customer-name').textContent = customerName;
        
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

    // Helper function for mobile cards
    window.openDeleteBookingModal = function(bookingId, customerName) {
        const modal = new bootstrap.Modal(deleteModal);
        
        // Set the data attributes on the modal trigger
        const tempButton = document.createElement('button');
        tempButton.setAttribute('data-booking-id', bookingId);
        tempButton.setAttribute('data-customer-name', customerName);
        
        // Trigger the modal show event
        const event = new Event('show.bs.modal');
        event.relatedTarget = tempButton;
        deleteModal.dispatchEvent(event);
        
        modal.show();
    };

    // Reports functionality
    const reportDateInput = document.getElementById('report-date');
    const generateReportBtn = document.getElementById('generate-report-btn');
    const reportsDisplay = document.getElementById('reports-display');
    const salesSummary = document.getElementById('sales-summary');
    const expensesSummary = document.getElementById('expenses-summary');
    const dailySummary = document.getElementById('daily-summary');
    
    const quickTodayBtn = document.getElementById('quick-today-btn');
    const quickYesterdayBtn = document.getElementById('quick-yesterday-btn');
    const quickWeekBtn = document.getElementById('quick-week-btn');
    
    const printReportBtn = document.getElementById('print-report-btn');
    const exportPdfBtn = document.getElementById('export-pdf-btn');
    const exportExcelBtn = document.getElementById('export-excel-btn');

    // Quick date selection buttons
    quickTodayBtn.addEventListener('click', function() {
        const today = new Date().toISOString().split('T')[0];
        reportDateInput.value = today;
        generateReport();
    });

    quickYesterdayBtn.addEventListener('click', function() {
        const yesterday = new Date();
        yesterday.setDate(yesterday.getDate() - 1);
        reportDateInput.value = yesterday.toISOString().split('T')[0];
        generateReport();
    });

    quickWeekBtn.addEventListener('click', function() {
        const today = new Date();
        const startOfWeek = new Date(today.setDate(today.getDate() - today.getDay()));
        reportDateInput.value = startOfWeek.toISOString().split('T')[0];
        generateReport();
    });

    // Generate report button
    generateReportBtn.addEventListener('click', generateReport);

    // Export buttons
    printReportBtn.addEventListener('click', function() {
        window.print();
    });

    exportPdfBtn.addEventListener('click', function() {
        const date = reportDateInput.value;
        if (!date) {
            alert('Please select a date first');
            return;
        }
        window.open(`/service-bookings/reports/pdf?date=${date}`, '_blank');
    });

    exportExcelBtn.addEventListener('click', function() {
        const date = reportDateInput.value;
        if (!date) {
            alert('Please select a date first');
            return;
        }
        window.location.href = `/service-bookings/reports/excel?date=${date}`;
    });

    function generateReport() {
        const date = reportDateInput.value;
        if (!date) {
            alert('Please select a date');
            return;
        }

        // Show loading state
        generateReportBtn.disabled = true;
        generateReportBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Generating...';

        // Fetch report data
        fetch(`/service-bookings/reports/daily?date=${date}`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayReportData(data.data);
                reportsDisplay.style.display = 'block';
            } else {
                alert('Error generating report: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error generating report. Please try again.');
        })
        .finally(() => {
            // Reset button state
            generateReportBtn.disabled = false;
            generateReportBtn.innerHTML = '<i class="fas fa-chart-bar me-1"></i> Generate Report';
        });
    }

    function displayReportData(data) {
        // Display sales summary
        salesSummary.innerHTML = `
            <div class="row text-center">
                <div class="col-6">
                    <h4 class="text-success">${data.sales.count}</h4>
                    <small class="text-muted">Services Sold</small>
                </div>
                <div class="col-6">
                    <h4 class="text-success">KSh ${formatNumber(data.sales.total_amount)}</h4>
                    <small class="text-muted">Total Revenue</small>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-12">
                    <h6>Payment Methods:</h6>
                    ${data.sales.payment_methods.map(method => 
                        `<div class="d-flex justify-content-between">
                            <span>${method.method}:</span>
                            <span>KSh ${formatNumber(method.amount)}</span>
                        </div>`
                    ).join('')}
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-12">
                    <h6>Top Services:</h6>
                    ${data.sales.top_services.map(service => 
                        `<div class="d-flex justify-content-between">
                            <span>${service.name} (${service.count}x):</span>
                            <span>KSh ${formatNumber(service.total)}</span>
                        </div>`
                    ).join('')}
                </div>
            </div>
        `;

        // Display expenses summary
        expensesSummary.innerHTML = `
            <div class="row text-center">
                <div class="col-6">
                    <h4 class="text-danger">${data.expenses.count}</h4>
                    <small class="text-muted">Total Expenses</small>
                </div>
                <div class="col-6">
                    <h4 class="text-danger">KSh ${formatNumber(data.expenses.total_amount)}</h4>
                    <small class="text-muted">Total Cost</small>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-12">
                    <h6>Expense Categories:</h6>
                    ${data.expenses.by_type.map(type => 
                        `<div class="d-flex justify-content-between">
                            <span>${type.type}:</span>
                            <span>KSh ${formatNumber(type.amount)}</span>
                        </div>`
                    ).join('')}
                </div>
            </div>
        `;

        // Display daily summary
        const netAmount = data.sales.total_amount - data.expenses.total_amount;
        const netClass = netAmount >= 0 ? 'text-success' : 'text-danger';
        const netLabel = netAmount >= 0 ? 'Net Profit' : 'Net Loss';

        dailySummary.innerHTML = `
            <div class="row text-center">
                <div class="col-md-3">
                    <h5 class="text-success">KSh ${formatNumber(data.sales.total_amount)}</h5>
                    <small class="text-muted">Total Sales</small>
                </div>
                <div class="col-md-3">
                    <h5 class="text-danger">KSh ${formatNumber(data.expenses.total_amount)}</h5>
                    <small class="text-muted">Total Expenses</small>
                </div>
                <div class="col-md-3">
                    <h5 class="${netClass}">KSh ${formatNumber(Math.abs(netAmount))}</h5>
                    <small class="text-muted">${netLabel}</small>
                </div>
                <div class="col-md-3">
                    <h5 class="text-info">${data.sales.commission_total ? 'KSh ' + formatNumber(data.sales.commission_total) : 'KSh 0.00'}</h5>
                    <small class="text-muted">Staff Commission</small>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-12 text-center">
                    <p class="mb-0"><strong>Report Date:</strong> ${formatDate(data.report_date)}</p>
                    <p class="mb-0"><small class="text-muted">Generated on ${new Date().toLocaleString()}</small></p>
                </div>
            </div>
        `;
    }

    function formatNumber(number) {
        return parseFloat(number).toLocaleString('en-KE', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    function formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('en-KE', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
    }
});
</script>
@endsection