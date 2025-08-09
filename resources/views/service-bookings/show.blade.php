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
            <a href="{{ route('service-bookings.index') }}" class="nav-tab">
                <i class="fas fa-calendar-check me-1"></i> Bookings
            </a>
            <a href="{{ route('service-bookings.create') }}" class="nav-tab">
                <i class="fas fa-plus-circle me-1"></i> New Booking
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="fw-bold" style="color: var(--text-primary);">Service Booking Details</h2>
                <div>
                    <a href="{{ route('service-bookings.edit', $serviceBooking) }}" class="btn btn-outline-primary me-2">
                        <i class="fas fa-edit me-1"></i> Edit
                    </a>
                    <a href="{{ route('service-bookings.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Back to List
                    </a>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert" style="background: var(--success-color); border: 1px solid var(--success-color); color: var(--white);">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="row">
                <div class="col-md-8">
                    <div class="card shadow-sm" style="background: var(--card-bg); border: 1px solid var(--border-color);">
                        <div class="card-header" style="background: var(--bg-tertiary); border-bottom: 1px solid var(--border-color);">
                            <h5 class="mb-0" style="color: var(--text-primary);">Booking Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p style="color: var(--text-primary);"><strong>Customer:</strong></p>
                                    <p style="color: var(--text-muted);">{{ $serviceBooking->customer->name ?? 'Walk-in Customer' }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p style="color: var(--text-primary);"><strong>Service Date:</strong></p>
                                    <p style="color: var(--text-muted);">{{ $serviceBooking->service_date ? $serviceBooking->service_date->format('M d, Y H:i') : 'Not set' }}</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <p style="color: var(--text-primary);"><strong>Payment Summary:</strong></p>
                                    @if($serviceBooking->discount_type && $serviceBooking->discount_type !== 'none' && $serviceBooking->discount_amount > 0)
                                        <p style="color: var(--text-muted); margin-bottom: 0.25rem;">Subtotal: KSh {{ number_format($serviceBooking->subtotal ?? $serviceBooking->total_amount, 2) }}</p>
                                        <p class="text-success mb-1">Discount ({{ $serviceBooking->discount_display }}): -KSh {{ number_format($serviceBooking->discount_amount, 2) }}</p>
                                        <p class="fw-bold" style="color: var(--text-primary);">Final Amount: KSh {{ number_format($serviceBooking->final_amount ?? $serviceBooking->total_amount, 2) }}</p>
                                    @else
                                        <p style="color: var(--text-muted);">Total Amount: KSh {{ number_format($serviceBooking->total_amount, 2) }}</p>
                                    @endif
                                </div>
                                <div class="col-md-6">
                                    <p style="color: var(--text-primary);"><strong>Payment Status:</strong></p>
                                    <p>
                                        @if($serviceBooking->payment_status == 'paid')
                                            <span class="badge bg-success">Paid</span>
                                        @elseif($serviceBooking->payment_status == 'pending')
                                            <span class="badge bg-warning">Pending</span>
                                        @else
                                            <span class="badge bg-danger">Cancelled</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                            @if($serviceBooking->notes)
                                <div class="row">
                                    <div class="col-12">
                                        <p style="color: var(--text-primary);"><strong>Notes:</strong></p>
                                        <p style="color: var(--text-muted);">{{ $serviceBooking->notes }}</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card shadow-sm" style="background: var(--card-bg); border: 1px solid var(--border-color);">
                        <div class="card-header" style="background: var(--bg-tertiary); border-bottom: 1px solid var(--border-color);">
                            <h5 class="mb-0" style="color: var(--text-primary);">Actions</h5>
                        </div>
                        <div class="card-body">
                            @if($serviceBooking->payment_status != 'paid')
                                <form action="{{ route('service-bookings.complete', $serviceBooking) }}" method="POST" class="mb-2">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="btn btn-success w-100" onclick="return confirm('Mark this booking as complete?')">
                                        <i class="fas fa-check me-1"></i> Mark as Complete
                                    </button>
                                </form>
                            @endif
                            
                            <form action="{{ route('service-bookings.destroy', $serviceBooking) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger w-100" onclick="return confirm('Delete this booking? This action cannot be undone.')">
                                    <i class="fas fa-trash me-1"></i> Delete Booking
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Services Section -->
            <div class="card shadow-sm mt-4" style="background: var(--card-bg); border: 1px solid var(--border-color);">
                <div class="card-header d-flex justify-content-between align-items-center" style="background: var(--bg-tertiary); border-bottom: 1px solid var(--border-color);">
                    <h5 class="mb-0" style="color: var(--text-primary);">Services Provided</h5>
                </div>
                <div class="card-body">
                    @if($serviceBooking->serviceItems && $serviceBooking->serviceItems->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead style="background-color: var(--bg-tertiary);">
                                    <tr>
                                        <th style="color: var(--text-secondary); border-bottom: 2px solid var(--border-color);">#</th>
                                        <th style="color: var(--text-secondary); border-bottom: 2px solid var(--border-color);">Service</th>
                                        <th style="color: var(--text-secondary); border-bottom: 2px solid var(--border-color);">Staff</th>
                                        <th style="color: var(--text-secondary); border-bottom: 2px solid var(--border-color);">Amount</th>
                                        <th style="color: var(--text-secondary); border-bottom: 2px solid var(--border-color);">Notes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($serviceBooking->serviceItems as $item)
                                    <tr style="color: var(--text-primary);">
                                        <td>{{ $item->sequence_order }}</td>
                                        <td>{{ $item->service->name ?? 'Unknown Service' }}</td>
                                        <td>
                                            @if($item->staff)
                                                {{ $item->staff->name }}
                                            @else
                                                <span class="badge bg-warning">Unassigned</span>
                                                <button type="button" class="btn btn-sm btn-outline-primary ms-2" 
                                                        onclick="assignStaff({{ $item->id }})">
                                                    <i class="fas fa-user-plus"></i> Assign
                                                </button>
                                            @endif
                                        </td>
                                        <td>KSh {{ number_format($item->amount, 2) }}</td>
                                        <td>{{ $item->notes ?? '-' }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p style="color: var(--text-muted); text-align: center; padding: 1rem 0;">No services recorded for this booking.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Staff Assignment Modal -->
<div class="modal fade" id="assignStaffModal" tabindex="-1" aria-labelledby="assignStaffModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="assignStaffModalLabel">Assign Staff</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="assignStaffForm">
                    @csrf
                    <input type="hidden" id="serviceItemId" name="service_item_id">
                    <div class="mb-3">
                        <label for="staffSelect" class="form-label">Select Staff Member</label>
                        <select class="form-select" id="staffSelect" name="staff_id" required>
                            <option value="">Choose a staff member...</option>
                            @foreach(auth()->user()->business->staff as $staff)
                                <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="submitStaffAssignment()">Assign Staff</button>
            </div>
        </div>
    </div>
</div>

<script>
function assignStaff(serviceItemId) {
    document.getElementById('serviceItemId').value = serviceItemId;
    const modal = new bootstrap.Modal(document.getElementById('assignStaffModal'));
    modal.show();
}

function submitStaffAssignment() {
    const form = document.getElementById('assignStaffForm');
    const formData = new FormData(form);
    
    fetch('{{ route("service-bookings.assign-staff") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Close modal
            bootstrap.Modal.getInstance(document.getElementById('assignStaffModal')).hide();
            // Reload page to show updated assignment
            location.reload();
        } else {
            alert('Error assigning staff: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error assigning staff. Please try again.');
    });
}
</script>

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

.table th {
    background-color: var(--bg-tertiary);
    border-bottom: 2px solid var(--border-color);
    color: var(--text-secondary);
}

.table td {
    color: var(--text-primary);
    border-color: var(--border-color);
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
