@extends('layouts.dash')

@section('content')
<div class="py-12" style="background: #f8f9fa; min-height: 100vh;">
    <div class="max-w-md mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow-lg rounded-lg p-8 border border-cyan-200">
            <div class="text-center mb-6">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-cyan-100 mb-4">
                    <i class="fas fa-shield-alt text-cyan-600 text-xl"></i>
                </div>
                <h2 class="text-2xl font-bold" style="color:#7b2e2e;">Security Verification Required</h2>
                <p class="text-gray-600 mt-2">{{ $actionName }}</p>
            </div>

            <div class="alert alert-info mb-6">
                <i class="fas fa-info-circle me-2"></i>
                <strong>Security Notice:</strong> We've sent a 6-digit verification code to your email address. This code will expire in 10 minutes.
            </div>

            <form id="verificationForm" class="space-y-6">
                @csrf
                <input type="hidden" name="action" value="{{ $action }}">
                
                <div>
                    <label for="verification_code" class="block text-sm font-medium text-cyan-700 mb-2">
                        <i class="fas fa-key me-1"></i> Verification Code
                    </label>
                    <input 
                        type="text" 
                        id="verification_code" 
                        name="code" 
                        class="form-control block w-full text-center text-2xl font-mono tracking-widest" 
                        maxlength="6" 
                        placeholder="000000"
                        required
                        autocomplete="off"
                    >
                    <p class="mt-1 text-xs text-gray-500">Enter the 6-digit code sent to your email</p>
                </div>

                <div class="flex space-x-3">
                    <button type="submit" class="btn btn-primary flex-1">
                        <i class="fas fa-check me-1"></i> Verify Code
                    </button>
                    <button type="button" onclick="resendCode()" class="btn btn-outline-secondary">
                        <i class="fas fa-redo me-1"></i> Resend
                    </button>
                </div>

                <div class="text-center">
                    <button type="button" onclick="cancelVerification()" class="text-sm text-gray-500 hover:text-gray-700">
                        <i class="fas fa-times me-1"></i> Cancel Verification
                    </button>
                </div>
            </form>

            <div id="verificationStatus" class="mt-4" style="display: none;"></div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const codeInput = document.getElementById('verification_code');
    const form = document.getElementById('verificationForm');
    const statusDiv = document.getElementById('verificationStatus');

    // Auto-format the verification code input
    codeInput.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, ''); // Remove non-digits
        value = value.substring(0, 6); // Limit to 6 digits
        e.target.value = value;
    });

    // Handle form submission
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const code = codeInput.value;
        if (code.length !== 6) {
            showStatus('Please enter a 6-digit verification code.', 'error');
            return;
        }

        verifyCode(code);
    });

    // Focus on code input when page loads
    codeInput.focus();
});

function verifyCode(code) {
    const form = document.getElementById('verificationForm');
    const submitBtn = form.querySelector('button[type="submit"]');
    const action = form.querySelector('input[name="action"]').value;

    // Disable form
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Verifying...';

    fetch('{{ route("2fa.verify") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
        },
        body: JSON.stringify({
            action: action,
            code: code
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showStatus(data.message, 'success');
            setTimeout(() => {
                window.location.href = data.redirect;
            }, 1500);
        } else {
            showStatus(data.message, 'error');
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-check me-1"></i> Verify Code';
        }
    })
    .catch(error => {
        showStatus('An error occurred. Please try again.', 'error');
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-check me-1"></i> Verify Code';
    });
}

function resendCode() {
    const form = document.getElementById('verificationForm');
    const action = form.querySelector('input[name="action"]').value;
    const resendBtn = document.querySelector('button[onclick="resendCode()"]');

    // Disable resend button
    resendBtn.disabled = true;
    resendBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Sending...';

    fetch('{{ route("2fa.resend") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
        },
        body: JSON.stringify({
            action: action
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showStatus(data.message, 'success');
        } else {
            showStatus(data.message, 'error');
        }
        resendBtn.disabled = false;
        resendBtn.innerHTML = '<i class="fas fa-redo me-1"></i> Resend';
    })
    .catch(error => {
        showStatus('An error occurred. Please try again.', 'error');
        resendBtn.disabled = false;
        resendBtn.innerHTML = '<i class="fas fa-redo me-1"></i> Resend';
    });
}

function cancelVerification() {
    if (!confirm('Are you sure you want to cancel this verification?')) {
        return;
    }

    const form = document.getElementById('verificationForm');
    const action = form.querySelector('input[name="action"]').value;

    fetch('{{ route("2fa.cancel") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
        },
        body: JSON.stringify({
            action: action
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.href = data.redirect;
        }
    })
    .catch(error => {
        window.location.href = '{{ route("dashboard") }}';
    });
}

function showStatus(message, type) {
    const statusDiv = document.getElementById('verificationStatus');
    const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle';

    statusDiv.innerHTML = `
        <div class="alert ${alertClass}">
            <i class="fas ${icon} me-2"></i>
            ${message}
        </div>
    `;
    statusDiv.style.display = 'block';

    // Auto-hide success messages
    if (type === 'success') {
        setTimeout(() => {
            statusDiv.style.display = 'none';
        }, 5000);
    }
}
</script>

<style>
.form-control {
    background: #f8f9fa !important;
    color: #7b2e2e !important;
    border: 2px solid #ff511a !important;
    margin-bottom: 1rem !important;
    width: 100%;
}
.form-control:focus {
    border-color: #7b2e2e !important;
    box-shadow: 0 0 0 3px rgba(19, 232, 233, 0.1) !important;
}
.btn-primary {
    background: #7b2e2e !important;
    color: #fff !important;
    border: 2px solid #ff511a !important;
}
.btn-primary:hover {
    background: #ff511a !important;
    color: #7b2e2e !important;
    border: 2px solid #7b2e2e !important;
}
.btn-outline-secondary {
    border: 2px solid #6c757d !important;
    color: #6c757d !important;
    background: transparent !important;
}
.btn-outline-secondary:hover {
    background: #6c757d !important;
    color: #fff !important;
}
</style>
@endsection
