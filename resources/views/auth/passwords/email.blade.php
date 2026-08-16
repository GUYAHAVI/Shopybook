@extends('layouts.master')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">{{ __('Reset Password') }}</h4>
                </div>

                <div class="card-body p-4">
                    @if (session('status'))
                        <div class="alert alert-success d-flex align-items-center" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            {{ session('status') }}
                        </div>
                    @endif

                    <div class="text-center mb-4">
                        <i class="fas fa-envelope-open-text text-primary" style="font-size: 3rem;"></i>
                        <h5 class="mt-3">Forgot your password?</h5>
                        <p class="text-muted">No worries! Enter your email address and we'll send you a link to reset your password.</p>
                    </div>

                    <form method="POST" action="{{ route('password.email') }}" id="passwordEmailForm">
                        @csrf

                        <div class="row mb-4">
                            <label for="email" class="col-md-4 col-form-label text-md-end fw-bold">{{ __('Email Address') }}</label>
                            <div class="col-md-6">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" 
                                           name="email" value="{{ old('email') }}" required autocomplete="email" autofocus
                                           placeholder="Enter your email address"
                                           style="color: #333 !important; background-color: #fff !important;">
                                </div>
                                @error('email')
                                    <span class="invalid-feedback d-block" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary btn-lg px-4" id="submitBtn">
                                    <i class="fas fa-paper-plane me-2"></i>{{ __('Send Password Reset Link') }}
                                </button>
                                <a href="{{ route('login') }}" class="btn btn-outline-secondary btn-lg px-4 ms-2">
                                    <i class="fas fa-arrow-left me-2"></i>{{ __('Back to Login') }}
                                </a>
                            </div>
                        </div>
                    </form>

                    <div class="text-center mt-4">
                        <small class="text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            Don't have an account? <a href="{{ route('register') }}" class="text-decoration-none">Sign up here</a>
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Reset any conflicting styles */
* {
    box-sizing: border-box;
}

.card {
    border: none;
    border-radius: 15px;
}

.card-header {
    border-radius: 15px 15px 0 0 !important;
    border-bottom: none;
}

.input-group-text {
    background-color: #f8f9fa;
    border-right: none;
}

.form-control {
    border-left: none;
    color: #333 !important;
    background-color: #fff !important;
    font-size: 16px !important;
    line-height: 1.5 !important;
}

.form-control:focus {
    border-color: #7b2e2e;
    box-shadow: 0 0 0 0.2rem rgba(79, 70, 229, 0.25);
    color: #333 !important;
    background-color: #fff !important;
    outline: none !important;
}

.form-control::placeholder {
    color: #6c757d !important;
    opacity: 1;
}

/* Fix autofill styling */
.form-control:-webkit-autofill,
.form-control:-webkit-autofill:hover,
.form-control:-webkit-autofill:focus,
.form-control:-webkit-autofill:active {
    -webkit-text-fill-color: #333 !important;
    -webkit-box-shadow: 0 0 0 30px #fff inset !important;
    transition: background-color 5000s ease-in-out 0s;
    background-color: #fff !important;
}

/* Ensure text is visible in all states */
input[type="email"],
input[type="password"],
input[type="text"] {
    color: #333 !important;
    background-color: #fff !important;
}

input[type="email"]:focus,
input[type="password"]:focus,
input[type="text"]:focus {
    color: #333 !important;
    background-color: #fff !important;
}

.input-group:focus-within .input-group-text {
    border-color: #7b2e2e;
}

.btn-outline-secondary:hover {
    background-color: #7b2e2e;
    border-color: #7b2e2e;
    color: white;
}

.alert {
    border-radius: 10px;
    border: none;
}

.alert-success {
    background-color: #d1edff;
    color: #0c5460;
}

.btn {
    border-radius: 8px;
    font-weight: 500;
}

.btn-lg {
    padding: 12px 24px;
}

/* Additional fixes for form field visibility */
.form-control,
.form-control:focus,
.form-control:active,
.form-control:hover {
    color: #333 !important;
    background-color: #fff !important;
    -webkit-text-fill-color: #333 !important;
}

/* Override any dark mode or theme conflicts */
@media (prefers-color-scheme: dark) {
    .form-control,
    .form-control:focus,
    .form-control:active,
    .form-control:hover {
        color: #333 !important;
        background-color: #fff !important;
        -webkit-text-fill-color: #333 !important;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('passwordEmailForm');
    const emailInput = document.getElementById('email');
    const submitBtn = document.getElementById('submitBtn');
    
    // Force text color on all form inputs
    const inputs = document.querySelectorAll('input[type="email"], input[type="password"], input[type="text"]');
    inputs.forEach(input => {
        input.style.color = '#333';
        input.style.backgroundColor = '#fff';
        input.style.webkitTextFillColor = '#333';
    });
    
    // Add loading state to submit button
    form.addEventListener('submit', function() {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Sending...';
    });
    
    // Email validation
    emailInput.addEventListener('input', function() {
        const email = this.value;
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        
        if (email && !emailRegex.test(email)) {
            this.classList.add('is-invalid');
        } else {
            this.classList.remove('is-invalid');
        }
    });
});
</script>
@endsection
