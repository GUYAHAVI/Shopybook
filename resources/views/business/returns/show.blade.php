@extends('layouts.dash')

@section('content')
<div class="container-fluid py-4">
    <div class="card mb-4">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0">Return Details: {{ $return->return_number }}</h5>
                    <p class="text-sm mb-0">Status: <span class="badge bg-{{ $return->status_color }}">{{ $return->status_text }}</span></p>
                </div>
                <a href="{{ route('returns.index') }}" class="btn btn-sm btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to List
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h6>Return Information</h6>
                    <table class="table table-sm">
                        <tr>
                            <th>Return Number:</th>
                            <td>{{ $return->return_number }}</td>
                        </tr>
                        <tr>
                            <th>Order Number:</th>
                            <td>{{ $return->order->order_number }}</td>
                        </tr>
                        <tr>
                            <th>Customer:</th>
                            <td>{{ $return->customer ? $return->customer->name : 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Return Type:</th>
                            <td><span class="badge bg-{{ $return->return_type == 'full' ? 'danger' : 'warning' }}">{{ ucfirst($return->return_type) }}</span></td>
                        </tr>
                        <tr>
                            <th>Reason Category:</th>
                            <td>{{ $return->reason_category_text }}</td>
                        </tr>
                        <tr>
                            <th>Reason:</th>
                            <td>{{ $return->reason }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h6>Financial Details</h6>
                    <table class="table table-sm">
                        <tr>
                            <th>Original Amount:</th>
                            <td>{{ $return->formatted_original_amount }}</td>
                        </tr>
                        <tr>
                            <th>Restocking Fee:</th>
                            <td>{{ $return->formatted_restocking_fee }}</td>
                        </tr>
                        <tr>
                            <th>Refund Amount:</th>
                            <td><strong class="text-danger">{{ $return->formatted_refund_amount }}</strong></td>
                        </tr>
                        <tr>
                            <th>Refund Method:</th>
                            <td>{{ $return->refund_method ? ucfirst(str_replace('_', ' ', $return->refund_method)) : 'Not Set' }}</td>
                        </tr>
                        <tr>
                            <th>Refund Processed:</th>
                            <td>
                                @if($return->refund_processed)
                                    <span class="badge bg-success">Yes</span> - {{ $return->refund_processed_at->format('M d, Y h:i A') }}
                                @else
                                    <span class="badge bg-secondary">No</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            @if($return->notes)
                <div class="alert alert-info mt-3">
                    <strong>Customer Notes:</strong> {{ $return->notes }}
                </div>
            @endif

            @if($return->internal_notes)
                <div class="alert alert-warning mt-3">
                    <strong>Internal Notes:</strong> {{ $return->internal_notes }}
                </div>
            @endif

            <!-- Actions -->
            <div class="mt-4">
                @if($return->status == 'pending')
                    <form action="{{ route('returns.approve', $return) }}" method="POST" class="d-inline" id="approveForm">
                        @csrf
                        <button type="button" class="btn btn-success" onclick="showPasswordModal('approve')">
                            <i class="fas fa-check me-2"></i>Approve Return
                        </button>
                    </form>
                    
                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">
                        <i class="fas fa-times me-2"></i>Reject Return
                    </button>
                @endif

                @if($return->status == 'approved' && !$return->refund_processed)
                    <button type="button" class="btn btn-primary" onclick="showPasswordModal('complete')">
                        <i class="fas fa-money-bill-wave me-2"></i>Process Refund
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Password Verification Modal -->
<div class="modal fade" id="passwordModal" tabindex="-1" aria-labelledby="passwordModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="passwordModalLabel">Verify Your Password</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted" id="passwordModalMessage">Please enter your password to confirm this action.</p>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                    <div id="passwordError" class="text-danger mt-2" style="display: none;"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="verifyBtn" onclick="verifyAndProceed()">
                    <i class="fas fa-check me-2"></i>Verify & Continue
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reject Return</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('returns.reject', $return) }}" method="POST" id="rejectForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Reason for Rejection</label>
                        <textarea class="form-control" name="internal_notes" id="rejectReason" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" onclick="submitReject()">Reject Return</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Complete Modal -->
<div class="modal fade" id="completeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Process Refund</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('returns.complete', $return) }}" method="POST" id="completeForm">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info">
                        <strong>Refund Amount:</strong> {{ $return->formatted_refund_amount }}
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Refund Method</label>
                        <select class="form-select" name="refund_method" id="refundMethod" required>
                            <option value="cash">Cash</option>
                            <option value="card">Card</option>
                            <option value="mobile_money">Mobile Money</option>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="store_credit">Store Credit</option>
                        </select>
                    </div>
                    @if($return->return_to_stock)
                        <div class="alert alert-warning">
                            <i class="fas fa-info-circle me-2"></i>
                            Items will be returned to inventory.
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="submitComplete()">Process Refund</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let passwordModalInstance;
let completeModalInstance;
let rejectModalInstance;
let currentAction = '';

document.addEventListener('DOMContentLoaded', function() {
    // Initialize modals
    passwordModalInstance = new bootstrap.Modal(document.getElementById('passwordModal'));
    completeModalInstance = new bootstrap.Modal(document.getElementById('completeModal'));
    rejectModalInstance = new bootstrap.Modal(document.getElementById('rejectModal'));
    
    // Clear password field when modal is hidden
    document.getElementById('passwordModal').addEventListener('hidden.bs.modal', function () {
        document.getElementById('password').value = '';
        document.getElementById('passwordError').style.display = 'none';
        currentAction = '';
    });
    
    // Allow Enter key to submit password
    document.getElementById('password').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            verifyAndProceed();
        }
    });
});

function showPasswordModal(action) {
    currentAction = action;
    const messageMap = {
        'approve': 'Please enter your password to approve this return.',
        'complete': 'Please enter your password to process this refund.',
        'reject': 'Please enter your password to reject this return.'
    };
    
    document.getElementById('passwordModalMessage').textContent = messageMap[action] || 'Please enter your password to confirm this action.';
    
    // If action is complete, show complete modal first
    if (action === 'complete') {
        completeModalInstance.show();
    } else {
        passwordModalInstance.show();
        setTimeout(() => {
            document.getElementById('password').focus();
        }, 500);
    }
}

function submitComplete() {
    // Validate refund method
    const refundMethod = document.getElementById('refundMethod').value;
    if (!refundMethod) {
        alert('Please select a refund method');
        return;
    }
    
    // Hide complete modal and show password modal
    completeModalInstance.hide();
    setTimeout(() => {
        passwordModalInstance.show();
        setTimeout(() => {
            document.getElementById('password').focus();
        }, 300);
    }, 300);
}

function submitReject() {
    // Validate rejection reason
    const reason = document.getElementById('rejectReason').value;
    if (!reason.trim()) {
        alert('Please provide a reason for rejection');
        return;
    }
    
    // Hide reject modal and show password modal
    currentAction = 'reject';
    rejectModalInstance.hide();
    setTimeout(() => {
        passwordModalInstance.show();
        setTimeout(() => {
            document.getElementById('password').focus();
        }, 300);
    }, 300);
}

function verifyAndProceed() {
    const password = document.getElementById('password').value;
    const errorDiv = document.getElementById('passwordError');
    
    if (!password) {
        errorDiv.textContent = 'Please enter your password';
        errorDiv.style.display = 'block';
        return;
    }
    
    // Disable button to prevent double submission
    const verifyBtn = document.getElementById('verifyBtn');
    verifyBtn.disabled = true;
    verifyBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Verifying...';
    
    // Verify password via AJAX
    fetch('{{ route('password.verify') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ password: password })
    })
    .then(response => response.json())
    .then(data => {
        if (data.valid) {
            // Password is correct, proceed with the action
            passwordModalInstance.hide();
            
            // Submit the appropriate form
            if (currentAction === 'approve') {
                document.getElementById('approveForm').submit();
            } else if (currentAction === 'complete') {
                document.getElementById('completeForm').submit();
            } else if (currentAction === 'reject') {
                document.getElementById('rejectForm').submit();
            }
        } else {
            // Password is incorrect
            errorDiv.textContent = 'Invalid password. Please try again.';
            errorDiv.style.display = 'block';
            verifyBtn.disabled = false;
            verifyBtn.innerHTML = '<i class="fas fa-check me-2"></i>Verify & Continue';
            document.getElementById('password').focus();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        errorDiv.textContent = 'Verification failed. Please try again.';
        errorDiv.style.display = 'block';
        verifyBtn.disabled = false;
        verifyBtn.innerHTML = '<i class="fas fa-check me-2"></i>Verify & Continue';
    });
}
</script>
@endpush

