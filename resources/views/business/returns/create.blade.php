@extends('layouts.dash')

@section('content')
<div class="container-fluid py-4">
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between">
                <h5>Create Return/Refund</h5>
                <a href="{{ route('returns.index') }}" class="btn btn-sm btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back
                </a>
            </div>
        </div>
        <div class="card-body">
             <form action="{{ route('returns.store') }}" method="POST" id="returnForm">
                @csrf
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Select Order <span class="text-danger">*</span></label>
                        <select class="form-select" name="order_id" id="orderSelect" required>
                            <option value="">Choose an order...</option>
                            @foreach($recentOrders as $recentOrder)
                                <option value="{{ $recentOrder->id }}" {{ $order && $order->id == $recentOrder->id ? 'selected' : '' }}>
                                    {{ $recentOrder->order_number }} - {{ $recentOrder->customer ? $recentOrder->customer->name : 'Walk-in' }} - KSh {{ number_format($recentOrder->total_amount, 2) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Return Type <span class="text-danger">*</span></label>
                        <select class="form-select" name="return_type" required>
                            <option value="full">Full Return</option>
                            <option value="partial">Partial Return</option>
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Reason Category <span class="text-danger">*</span></label>
                        <select class="form-select" name="reason_category" required>
                            <option value="defective">Defective Product</option>
                            <option value="wrong_item">Wrong Item Sent</option>
                            <option value="not_as_described">Not As Described</option>
                            <option value="customer_changed_mind">Customer Changed Mind</option>
                            <option value="damaged_in_shipping">Damaged in Shipping</option>
                            <option value="other">Other</option>
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Restocking Fee (Optional)</label>
                        <input type="number" step="0.01" class="form-control" name="restocking_fee" value="0" min="0">
                    </div>

                    <div class="col-md-12 mb-3">
                        <label class="form-label">Reason Details <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="reason" rows="3" required placeholder="Please provide detailed reason for the return..."></textarea>
                    </div>

                    <div class="col-md-12 mb-3">
                        <label class="form-label">Customer Notes (Optional)</label>
                        <textarea class="form-control" name="notes" rows="2" placeholder="Any additional notes from customer..."></textarea>
                    </div>

                    <div class="col-md-12 mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="return_to_stock" value="1" checked>
                            <label class="form-check-label">
                                Return items to stock (restore inventory)
                            </label>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="button" class="btn btn-primary" onclick="showPasswordModal()">
                        <i class="fas fa-save me-2"></i>Create Return Request
                    </button>
                </div>
            </form>
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
                <p class="text-muted">Please enter your password to confirm this return request.</p>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                    <div id="passwordError" class="text-danger mt-2" style="display: none;"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="verifyAndSubmit()">
                    <i class="fas fa-check me-2"></i>Verify & Submit
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
let passwordModalInstance;

document.addEventListener('DOMContentLoaded', function() {
    // Initialize the modal
    passwordModalInstance = new bootstrap.Modal(document.getElementById('passwordModal'));
    
    // Clear password field when modal is hidden
    document.getElementById('passwordModal').addEventListener('hidden.bs.modal', function () {
        document.getElementById('password').value = '';
        document.getElementById('passwordError').style.display = 'none';
    });
    
    // Allow Enter key to submit
    document.getElementById('password').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            verifyAndSubmit();
        }
    });
});

function showPasswordModal() {
    // Validate form first
    const form = document.getElementById('returnForm');
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    
    // Show password modal
    passwordModalInstance.show();
    
    // Focus on password field after modal is shown
    setTimeout(() => {
        document.getElementById('password').focus();
    }, 500);
}

function verifyAndSubmit() {
    const password = document.getElementById('password').value;
    const errorDiv = document.getElementById('passwordError');
    
    if (!password) {
        errorDiv.textContent = 'Please enter your password';
        errorDiv.style.display = 'block';
        return;
    }
    
    // Disable submit button to prevent double submission
    const submitBtn = event.target;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Verifying...';
    
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
            // Password is correct, submit the form
            passwordModalInstance.hide();
            document.getElementById('returnForm').submit();
        } else {
            // Password is incorrect
            errorDiv.textContent = 'Invalid password. Please try again.';
            errorDiv.style.display = 'block';
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-check me-2"></i>Verify & Submit';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        errorDiv.textContent = 'Verification failed. Please try again.';
        errorDiv.style.display = 'block';
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-check me-2"></i>Verify & Submit';
    });
}
</script>
@endpush

