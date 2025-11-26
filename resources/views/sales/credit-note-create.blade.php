@extends('layouts.dash')

@section('title', 'Create Credit Note')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header bg-warning text-white">
                    <h4 class="mb-0"><i class="fas fa-file-invoice me-2"></i>Create Credit Note</h4>
                </div>
                <div class="card-body">
                    <!-- Order Information -->
                    <div class="alert alert-info">
                        <h5>Order Details</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Order Number:</strong> {{ $order->order_number }}</p>
                                <p><strong>Invoice Number:</strong> {{ $order->invoice_number }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Total Amount:</strong> KSh {{ number_format($order->total_amount, 2) }}</p>
                                <p><strong>Payment Status:</strong> 
                                    <span class="badge bg-{{ $order->payment_status === 'paid' ? 'success' : 'warning' }}">
                                        {{ ucfirst($order->payment_status) }}
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Credit Note Form -->
                    <form id="creditNoteForm">
                        @csrf
                        <div class="mb-3">
                            <label for="amount" class="form-label">Credit Amount <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">KSh</span>
                                <input type="number" 
                                       class="form-control" 
                                       id="amount" 
                                       name="amount" 
                                       step="0.01" 
                                       min="0" 
                                       max="{{ $order->total_amount }}" 
                                       required>
                            </div>
                            <small class="text-muted">Maximum: KSh {{ number_format($order->total_amount, 2) }}</small>
                        </div>

                        <div class="mb-3">
                            <label for="reason" class="form-label">Reason for Credit Note <span class="text-danger">*</span></label>
                            <textarea class="form-control" 
                                      id="reason" 
                                      name="reason" 
                                      rows="4" 
                                      required
                                      placeholder="Explain why this credit note is needed..."></textarea>
                        </div>

                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Admin Approval Required:</strong> This credit note request will require OTP verification from an administrator before it can be processed.
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('sales.orders') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-warning" id="submitBtn">
                                <i class="fas fa-paper-plane me-2"></i>Submit Request
                            </button>
                        </div>
                    </form>

                    <!-- OTP Verification Modal -->
                    <div id="otpVerificationSection" style="display: none;" class="mt-4">
                        <hr>
                        <div class="card border-primary">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0"><i class="fas fa-shield-alt me-2"></i>Admin Verification Required</h5>
                            </div>
                            <div class="card-body">
                                <p>A credit note request has been created. An OTP has been sent to the administrator's email.</p>
                                
                                <div class="mb-3">
                                    <button type="button" class="btn btn-info" id="sendOtpBtn">
                                        <i class="fas fa-envelope me-2"></i>Send OTP
                                    </button>
                                </div>

                                <form id="otpVerificationForm">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="otp" class="form-label">Enter OTP</label>
                                        <input type="text" 
                                               class="form-control" 
                                               id="otp" 
                                               name="otp" 
                                               maxlength="6" 
                                               pattern="\d{6}"
                                               placeholder="000000"
                                               required>
                                    </div>

                                    <div class="mb-3">
                                        <label for="action" class="form-label">Action</label>
                                        <select class="form-select" id="action" name="action" required>
                                            <option value="approve">Approve Credit Note</option>
                                            <option value="reject">Reject Credit Note</option>
                                        </select>
                                    </div>

                                    <div class="mb-3" id="rejectionReasonDiv" style="display: none;">
                                        <label for="rejection_reason" class="form-label">Rejection Reason</label>
                                        <textarea class="form-control" 
                                                  id="rejection_reason" 
                                                  name="rejection_reason" 
                                                  rows="3"></textarea>
                                    </div>

                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-check me-2"></i>Verify & Process
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let currentCreditNoteId = null;

// Submit credit note request
document.getElementById('creditNoteForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const submitBtn = document.getElementById('submitBtn');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';
    
    try {
        const formData = new FormData(this);
        const response = await fetch('{{ route("sales.credit-note.store", $order) }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            currentCreditNoteId = data.credit_note_id;
            document.getElementById('creditNoteForm').style.display = 'none';
            document.getElementById('otpVerificationSection').style.display = 'block';
            alert(data.message);
        } else {
            alert('Error: ' + (data.message || 'Failed to create credit note'));
        }
    } catch (error) {
        alert('Error: ' + error.message);
    } finally {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-paper-plane me-2"></i>Submit Request';
    }
});

// Send OTP
document.getElementById('sendOtpBtn').addEventListener('click', async function() {
    if (!currentCreditNoteId) return;
    
    this.disabled = true;
    this.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Sending...';
    
    try {
        const response = await fetch(`/sales/credit-notes/${currentCreditNoteId}/send-otp`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json'
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            alert(data.message);
            if (data.otp) {
                // For testing purposes
                document.getElementById('otp').value = data.otp;
            }
        } else {
            alert('Error: ' + (data.message || 'Failed to send OTP'));
        }
    } catch (error) {
        alert('Error: ' + error.message);
    } finally {
        this.disabled = false;
        this.innerHTML = '<i class="fas fa-envelope me-2"></i>Resend OTP';
    }
});

// Handle action change (show/hide rejection reason)
document.getElementById('action').addEventListener('change', function() {
    const rejectionDiv = document.getElementById('rejectionReasonDiv');
    if (this.value === 'reject') {
        rejectionDiv.style.display = 'block';
        document.getElementById('rejection_reason').required = true;
    } else {
        rejectionDiv.style.display = 'none';
        document.getElementById('rejection_reason').required = false;
    }
});

// Verify OTP and process
document.getElementById('otpVerificationForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    if (!currentCreditNoteId) return;
    
    const formData = new FormData(this);
    const data = Object.fromEntries(formData.entries());
    
    try {
        const response = await fetch(`/sales/credit-notes/${currentCreditNoteId}/verify-otp`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (result.success) {
            alert(result.message);
            window.location.href = '{{ route("sales.orders") }}';
        } else {
            alert('Error: ' + (result.message || 'Verification failed'));
        }
    } catch (error) {
        alert('Error: ' + error.message);
    }
});
</script>
@endsection
