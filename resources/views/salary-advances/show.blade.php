@extends('layouts.dash')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="fw-bold" style="color:#020258;">Salary Advance Details</h2>
                <div>
                    <a href="{{ route('salary-advances.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Back to List
                    </a>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    @foreach($errors->all() as $error)
                        {{ $error }}<br>
                    @endforeach
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="row">
                <div class="col-md-8">
                    <div class="card shadow-sm">
                        <div class="card-header">
                            <h5 class="mb-0">Advance Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Staff Member:</strong></p>
                                    <p class="text-muted">{{ $salaryAdvance->staff->name }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Role:</strong></p>
                                    <p class="text-muted">{{ $salaryAdvance->staff->role }}</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Advance Amount:</strong></p>
                                    <p class="text-muted fs-5 fw-bold text-primary">{{ $salaryAdvance->formatted_amount }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Advance Date:</strong></p>
                                    <p class="text-muted">{{ $salaryAdvance->advance_date->format('M d, Y') }}</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Status:</strong></p>
                                    <p>{!! $salaryAdvance->status_badge !!}</p>
                                </div>
                                @if($salaryAdvance->status === 'paid')
                                <div class="col-md-6">
                                    <p><strong>Deduction Status:</strong></p>
                                    <p>{!! $salaryAdvance->deduction_status_badge !!}</p>
                                </div>
                                @endif
                            </div>
                            
                            @if($salaryAdvance->status === 'paid')
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Remaining Balance:</strong></p>
                                    <p class="text-muted fw-bold">{{ $salaryAdvance->formatted_remaining_balance }}</p>
                                </div>
                                @if($salaryAdvance->paid_at)
                                <div class="col-md-6">
                                    <p><strong>Paid At:</strong></p>
                                    <p class="text-muted">{{ $salaryAdvance->paid_at->format('M d, Y H:i') }}</p>
                                </div>
                                @endif
                            </div>
                            @endif

                            @if($salaryAdvance->reason)
                            <div class="row">
                                <div class="col-12">
                                    <p><strong>Reason:</strong></p>
                                    <p class="text-muted">{{ $salaryAdvance->reason }}</p>
                                </div>
                            </div>
                            @endif

                            @if($salaryAdvance->notes)
                            <div class="row">
                                <div class="col-12">
                                    <p><strong>Notes:</strong></p>
                                    <p class="text-muted">{{ $salaryAdvance->notes }}</p>
                                </div>
                            </div>
                            @endif

                            @if($salaryAdvance->approvedBy)
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Approved By:</strong></p>
                                    <p class="text-muted">{{ $salaryAdvance->approvedBy->name }}</p>
                                </div>
                                @if($salaryAdvance->approved_at)
                                <div class="col-md-6">
                                    <p><strong>Approved At:</strong></p>
                                    <p class="text-muted">{{ $salaryAdvance->approved_at->format('M d, Y H:i') }}</p>
                                </div>
                                @endif
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Staff Salary Information -->
                    <div class="card shadow-sm mt-4">
                        <div class="card-header">
                            <h5 class="mb-0">Staff Salary Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="text-center">
                                        <small class="text-muted">Monthly Salary</small>
                                        <div class="fs-6 fw-bold">KSh {{ number_format($salaryAdvance->staff->salary ?? 0, 2) }}</div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="text-center">
                                        <small class="text-muted">Pending Deductions</small>
                                        <div class="fs-6 fw-bold text-warning">KSh {{ number_format($salaryAdvance->staff->pending_advance_deductions, 2) }}</div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="text-center">
                                        <small class="text-muted">Available for Advance</small>
                                        <div class="fs-6 fw-bold text-success">KSh {{ number_format($salaryAdvance->staff->available_advance_amount, 2) }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card shadow-sm">
                        <div class="card-header">
                            <h5 class="mb-0">Actions</h5>
                        </div>
                        <div class="card-body">
                            @if($salaryAdvance->status === 'pending')
                                <button type="button" class="btn btn-success w-100 mb-2" data-bs-toggle="modal" data-bs-target="#approveModal">
                                    <i class="fas fa-check me-1"></i> Approve Request
                                </button>
                            @endif

                            @if($salaryAdvance->status === 'approved')
                                <button type="button" class="btn btn-primary w-100 mb-2" data-bs-toggle="modal" data-bs-target="#markAsPaidModal">
                                    <i class="fas fa-money-bill me-1"></i> Mark as Paid
                                </button>
                            @endif

                            @if(in_array($salaryAdvance->status, ['pending', 'approved']))
                                <button type="button" class="btn btn-warning w-100 mb-2" data-bs-toggle="modal" data-bs-target="#cancelModal">
                                    <i class="fas fa-times me-1"></i> Cancel Advance
                                </button>
                            @endif

                            @if($salaryAdvance->status !== 'paid')
                                <button type="button" class="btn btn-outline-danger w-100" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                    <i class="fas fa-trash me-1"></i> Delete Advance
                                </button>
                            @endif
                        </div>
                    </div>

                    <!-- Timeline -->
                    <div class="card shadow-sm mt-3">
                        <div class="card-header">
                            <h5 class="mb-0">Timeline</h5>
                        </div>
                        <div class="card-body">
                            <div class="timeline">
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-info"></div>
                                    <div class="timeline-content">
                                        <h6 class="timeline-title">Request Created</h6>
                                        <p class="timeline-text">{{ $salaryAdvance->created_at->format('M d, Y H:i') }}</p>
                                    </div>
                                </div>

                                @if($salaryAdvance->approved_at)
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-success"></div>
                                    <div class="timeline-content">
                                        <h6 class="timeline-title">Approved</h6>
                                        <p class="timeline-text">
                                            {{ $salaryAdvance->approved_at->format('M d, Y H:i') }}
                                            @if($salaryAdvance->approvedBy)
                                            <br><small class="text-muted">by {{ $salaryAdvance->approvedBy->name }}</small>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                @endif

                                @if($salaryAdvance->paid_at)
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-primary"></div>
                                    <div class="timeline-content">
                                        <h6 class="timeline-title">Paid</h6>
                                        <p class="timeline-text">{{ $salaryAdvance->paid_at->format('M d, Y H:i') }}</p>
                                    </div>
                                </div>
                                @endif

                                @if($salaryAdvance->status === 'cancelled')
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-warning"></div>
                                    <div class="timeline-content">
                                        <h6 class="timeline-title">Cancelled</h6>
                                        <p class="timeline-text">{{ $salaryAdvance->updated_at->format('M d, Y H:i') }}</p>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Password Verification Modals -->

<!-- Approve Modal -->
@if($salaryAdvance->status === 'pending')
    <div class="modal fade" id="approveModal" tabindex="-1" aria-labelledby="approveModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="approveModalLabel">
                        <i class="fas fa-shield-alt text-success me-2"></i>
                        Approve Salary Advance
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('salary-advances.approve', $salaryAdvance) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Security Verification Required</strong><br>
                            Please enter your login password to approve this salary advance for <strong>{{ $salaryAdvance->staff->name }}</strong>.
                        </div>
                        
                        <div class="mb-3">
                            <label for="approvePassword" class="form-label">Your Password</label>
                            <input type="password" 
                                   class="form-control @error('password') is-invalid @enderror" 
                                   id="approvePassword" 
                                   name="password" 
                                   required 
                                   autocomplete="new-password"
                                   placeholder="Enter your login password">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Amount:</strong> {{ $salaryAdvance->formatted_amount }}<br>
                            <strong>Date:</strong> {{ $salaryAdvance->advance_date->format('M d, Y') }}
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i> Cancel
                        </button>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-check me-1"></i> Approve Advance
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif

<!-- Mark as Paid Modal -->
@if($salaryAdvance->status === 'approved')
    <div class="modal fade" id="markAsPaidModal" tabindex="-1" aria-labelledby="markAsPaidModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="markAsPaidModalLabel">
                        <i class="fas fa-shield-alt text-primary me-2"></i>
                        Mark as Paid
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('salary-advances.mark-as-paid', $salaryAdvance) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Security Verification Required</strong><br>
                            Please enter your login password to mark this salary advance as paid for <strong>{{ $salaryAdvance->staff->name }}</strong>.
                        </div>
                        
                        <div class="mb-3">
                            <label for="markAsPaidPassword" class="form-label">Your Password</label>
                            <input type="password" 
                                   class="form-control @error('password') is-invalid @enderror" 
                                   id="markAsPaidPassword" 
                                   name="password" 
                                   required 
                                   autocomplete="new-password"
                                   placeholder="Enter your login password">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Amount:</strong> {{ $salaryAdvance->formatted_amount }}<br>
                            <strong>Date:</strong> {{ $salaryAdvance->advance_date->format('M d, Y') }}
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i> Cancel
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-money-bill me-1"></i> Mark as Paid
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif

<!-- Cancel Modal -->
@if(in_array($salaryAdvance->status, ['pending', 'approved']))
    <div class="modal fade" id="cancelModal" tabindex="-1" aria-labelledby="cancelModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cancelModalLabel">
                        <i class="fas fa-shield-alt text-warning me-2"></i>
                        Cancel Salary Advance
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('salary-advances.cancel', $salaryAdvance) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Security Verification Required</strong><br>
                            Please enter your login password to cancel this salary advance for <strong>{{ $salaryAdvance->staff->name }}</strong>.
                        </div>
                        
                        <div class="mb-3">
                            <label for="cancelPassword" class="form-label">Your Password</label>
                            <input type="password" 
                                   class="form-control @error('password') is-invalid @enderror" 
                                   id="cancelPassword" 
                                   name="password" 
                                   required 
                                   autocomplete="new-password"
                                   placeholder="Enter your login password">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Amount:</strong> {{ $salaryAdvance->formatted_amount }}<br>
                            <strong>Date:</strong> {{ $salaryAdvance->advance_date->format('M d, Y') }}
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i> Cancel
                        </button>
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-times me-1"></i> Cancel Advance
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif

<!-- Delete Modal -->
@if($salaryAdvance->status !== 'paid')
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">
                        <i class="fas fa-shield-alt text-danger me-2"></i>
                        Delete Salary Advance
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('salary-advances.destroy', $salaryAdvance) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="modal-body">
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Warning: This action cannot be undone!</strong><br>
                            Please enter your login password to permanently delete this salary advance for <strong>{{ $salaryAdvance->staff->name }}</strong>.
                        </div>
                        
                        <div class="mb-3">
                            <label for="deletePassword" class="form-label">Your Password</label>
                            <input type="password" 
                                   class="form-control @error('password') is-invalid @enderror" 
                                   id="deletePassword" 
                                   name="password" 
                                   required 
                                   autocomplete="new-password"
                                   placeholder="Enter your login password">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Amount:</strong> {{ $salaryAdvance->formatted_amount }}<br>
                            <strong>Date:</strong> {{ $salaryAdvance->advance_date->format('M d, Y') }}
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i> Cancel
                        </button>
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash me-1"></i> Delete Advance
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif

<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #dee2e6;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: -23px;
    top: 5px;
    width: 16px;
    height: 16px;
    border-radius: 50%;
    border: 2px solid #fff;
}

.timeline-content {
    padding-left: 10px;
}

.timeline-title {
    font-size: 0.9rem;
    font-weight: 600;
    margin-bottom: 2px;
}

.timeline-text {
    font-size: 0.8rem;
    color: #6c757d;
    margin: 0;
}
</style>

<!-- Password Verification Modals -->

<!-- Approve Modal -->
@if($salaryAdvance->status === 'pending')
    <div class="modal fade" id="approveModal" tabindex="-1" aria-labelledby="approveModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="approveModalLabel">
                        <i class="fas fa-shield-alt text-success me-2"></i>
                        Approve Salary Advance
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('salary-advances.approve', $salaryAdvance) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Security Verification Required</strong><br>
                            Please enter your login password to approve this salary advance for <strong>{{ $salaryAdvance->staff->name }}</strong>.
                        </div>
                        
                        <div class="mb-3">
                            <label for="approvePassword" class="form-label">Your Password</label>
                            <input type="password" 
                                   class="form-control @error('password') is-invalid @enderror" 
                                   id="approvePassword" 
                                   name="password" 
                                   required 
                                   autocomplete="new-password"
                                   placeholder="Enter your login password">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Amount:</strong> {{ $salaryAdvance->formatted_amount }}<br>
                            <strong>Date:</strong> {{ $salaryAdvance->advance_date->format('M d, Y') }}
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i> Cancel
                        </button>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-check me-1"></i> Approve Advance
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif

<!-- Mark as Paid Modal -->
@if($salaryAdvance->status === 'approved')
    <div class="modal fade" id="markAsPaidModal" tabindex="-1" aria-labelledby="markAsPaidModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="markAsPaidModalLabel">
                        <i class="fas fa-shield-alt text-primary me-2"></i>
                        Mark as Paid
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('salary-advances.mark-as-paid', $salaryAdvance) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Security Verification Required</strong><br>
                            Please enter your login password to mark this salary advance as paid for <strong>{{ $salaryAdvance->staff->name }}</strong>.
                        </div>
                        
                        <div class="mb-3">
                            <label for="markAsPaidPassword" class="form-label">Your Password</label>
                            <input type="password" 
                                   class="form-control @error('password') is-invalid @enderror" 
                                   id="markAsPaidPassword" 
                                   name="password" 
                                   required 
                                   autocomplete="new-password"
                                   placeholder="Enter your login password">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Amount:</strong> {{ $salaryAdvance->formatted_amount }}<br>
                            <strong>Date:</strong> {{ $salaryAdvance->advance_date->format('M d, Y') }}
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i> Cancel
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-money-bill me-1"></i> Mark as Paid
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif

<!-- Cancel Modal -->
@if(in_array($salaryAdvance->status, ['pending', 'approved']))
    <div class="modal fade" id="cancelModal" tabindex="-1" aria-labelledby="cancelModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cancelModalLabel">
                        <i class="fas fa-shield-alt text-warning me-2"></i>
                        Cancel Salary Advance
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('salary-advances.cancel', $salaryAdvance) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Security Verification Required</strong><br>
                            Please enter your login password to cancel this salary advance for <strong>{{ $salaryAdvance->staff->name }}</strong>.
                        </div>
                        
                        <div class="mb-3">
                            <label for="cancelPassword" class="form-label">Your Password</label>
                            <input type="password" 
                                   class="form-control @error('password') is-invalid @enderror" 
                                   id="cancelPassword" 
                                   name="password" 
                                   required 
                                   autocomplete="new-password"
                                   placeholder="Enter your login password">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Amount:</strong> {{ $salaryAdvance->formatted_amount }}<br>
                            <strong>Date:</strong> {{ $salaryAdvance->advance_date->format('M d, Y') }}
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i> Cancel
                        </button>
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-times me-1"></i> Cancel Advance
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif

<!-- Delete Modal -->
@if($salaryAdvance->status !== 'paid')
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">
                        <i class="fas fa-shield-alt text-danger me-2"></i>
                        Delete Salary Advance
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('salary-advances.destroy', $salaryAdvance) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="modal-body">
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Warning: This action cannot be undone!</strong><br>
                            Please enter your login password to permanently delete this salary advance for <strong>{{ $salaryAdvance->staff->name }}</strong>.
                        </div>
                        
                        <div class="mb-3">
                            <label for="deletePassword" class="form-label">Your Password</label>
                            <input type="password" 
                                   class="form-control @error('password') is-invalid @enderror" 
                                   id="deletePassword" 
                                   name="password" 
                                   required 
                                   autocomplete="new-password"
                                   placeholder="Enter your login password">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Amount:</strong> {{ $salaryAdvance->formatted_amount }}<br>
                            <strong>Date:</strong> {{ $salaryAdvance->advance_date->format('M d, Y') }}
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i> Cancel
                        </button>
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash me-1"></i> Delete Advance
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif

<script>
// Clear password fields when modals are closed
document.addEventListener('DOMContentLoaded', function() {
    const modals = document.querySelectorAll('.modal');
    modals.forEach(modal => {
        modal.addEventListener('hidden.bs.modal', function() {
            const passwordInputs = this.querySelectorAll('input[type="password"]');
            passwordInputs.forEach(input => {
                input.value = '';
            });
        });
    });
});
</script>

@endsection