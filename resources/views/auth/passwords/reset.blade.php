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
                    <form method="POST" action="{{ route('password.update') }}" id="passwordResetForm">
                        @csrf
                        <input type="hidden" name="token" value="{{ $token }}">

                        <!-- Email Field -->
                        <div class="row mb-4">
                            <label for="email" class="col-md-4 col-form-label text-md-end fw-bold">{{ __('Email Address') }}</label>
                            <div class="col-md-6">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" 
                                           name="email" value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus
                                           style="color: #333 !important; background-color: #fff !important;">
                                </div>
                                @error('email')
                                    <span class="invalid-feedback d-block" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <!-- Password Field -->
                        <div class="row mb-3">
                            <label for="password" class="col-md-4 col-form-label text-md-end fw-bold">{{ __('New Password') }}</label>
                            <div class="col-md-6">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" 
                                           name="password" required autocomplete="new-password" 
                                           onkeyup="checkPasswordStrength(this.value)"
                                           style="color: #333 !important; background-color: #fff !important;">
                                    <button class="btn btn-outline-secondary" type="button" id="togglePassword" 
                                            onclick="togglePasswordVisibility('password', 'togglePassword')">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                
                                <!-- Password Strength Indicator -->
                                <div class="mt-2">
                                    <div class="progress" style="height: 5px;">
                                        <div id="passwordStrengthBar" class="progress-bar" role="progressbar" style="width: 0%"></div>
                                    </div>
                                    <small id="passwordStrengthText" class="text-muted">Password strength</small>
                                </div>

                                <!-- Password Requirements -->
                                <div class="mt-2">
                                    <small class="text-muted">Password must contain:</small>
                                    <ul class="list-unstyled small text-muted mt-1">
                                        <li id="req-length"><i class="fas fa-circle text-muted"></i> At least 8 characters</li>
                                        <li id="req-uppercase"><i class="fas fa-circle text-muted"></i> One uppercase letter</li>
                                        <li id="req-lowercase"><i class="fas fa-circle text-muted"></i> One lowercase letter</li>
                                        <li id="req-number"><i class="fas fa-circle text-muted"></i> One number</li>
                                        <li id="req-special"><i class="fas fa-circle text-muted"></i> One special character</li>
                                    </ul>
                                </div>

                                @error('password')
                                    <span class="invalid-feedback d-block" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <!-- Confirm Password Field -->
                        <div class="row mb-4">
                            <label for="password-confirm" class="col-md-4 col-form-label text-md-end fw-bold">{{ __('Confirm Password') }}</label>
                            <div class="col-md-6">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                    <input id="password-confirm" type="password" class="form-control" 
                                           name="password_confirmation" required autocomplete="new-password"
                                           onkeyup="checkPasswordMatch()"
                                           style="color: #333 !important; background-color: #fff !important;">
                                    <button class="btn btn-outline-secondary" type="button" id="toggleConfirmPassword" 
                                            onclick="togglePasswordVisibility('password-confirm', 'toggleConfirmPassword')">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                <div id="passwordMatchIndicator" class="mt-2"></div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary btn-lg px-4" id="submitBtn" disabled>
                                    <i class="fas fa-key me-2"></i>{{ __('Reset Password') }}
                                </button>
                                <a href="{{ route('login') }}" class="btn btn-outline-secondary btn-lg px-4 ms-2">
                                    <i class="fas fa-arrow-left me-2"></i>{{ __('Back to Login') }}
                                </a>
                            </div>
                        </div>
                    </form>
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

.progress {
    border-radius: 10px;
    background-color: #e9ecef;
}

.progress-bar {
    border-radius: 10px;
    transition: all 0.3s ease;
}

.password-requirement {
    transition: all 0.3s ease;
}

.password-requirement.met {
    color: #28a745 !important;
}

.password-requirement.met i {
    color: #28a745 !important;
}

.password-match {
    color: #28a745;
    font-weight: 500;
}

.password-mismatch {
    color: #dc3545;
    font-weight: 500;
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
function togglePasswordVisibility(fieldId, buttonId) {
    const field = document.getElementById(fieldId);
    const button = document.getElementById(buttonId);
    const icon = button.querySelector('i');
    
    if (field.type === 'password') {
        field.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        field.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

function checkPasswordStrength(password) {
    const strengthBar = document.getElementById('passwordStrengthBar');
    const strengthText = document.getElementById('passwordStrengthText');
    let strength = 0;
    let feedback = '';
    
    // Check requirements
    const requirements = {
        length: password.length >= 8,
        uppercase: /[A-Z]/.test(password),
        lowercase: /[a-z]/.test(password),
        number: /\d/.test(password),
        special: /[!@#$%^&*(),.?":{}|<>]/.test(password)
    };
    
    // Update requirement indicators
    Object.keys(requirements).forEach(req => {
        const element = document.getElementById('req-' + req);
        const icon = element.querySelector('i');
        if (requirements[req]) {
            element.classList.add('met');
            strength += 20;
        } else {
            element.classList.remove('met');
        }
    });
    
    // Set strength level
    if (strength <= 20) {
        strengthBar.className = 'progress-bar bg-danger';
        feedback = 'Very Weak';
    } else if (strength <= 40) {
        strengthBar.className = 'progress-bar bg-warning';
        feedback = 'Weak';
    } else if (strength <= 60) {
        strengthBar.className = 'progress-bar bg-info';
        feedback = 'Fair';
    } else if (strength <= 80) {
        strengthBar.className = 'progress-bar bg-primary';
        feedback = 'Good';
    } else {
        strengthBar.className = 'progress-bar bg-success';
        feedback = 'Strong';
    }
    
    strengthBar.style.width = strength + '%';
    strengthText.textContent = feedback;
    
    // Enable/disable submit button
    checkFormValidity();
}

function checkPasswordMatch() {
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('password-confirm').value;
    const indicator = document.getElementById('passwordMatchIndicator');
    
    if (confirmPassword === '') {
        indicator.innerHTML = '';
        return;
    }
    
    if (password === confirmPassword) {
        indicator.innerHTML = '<small class="password-match"><i class="fas fa-check-circle me-1"></i>Passwords match</small>';
    } else {
        indicator.innerHTML = '<small class="password-mismatch"><i class="fas fa-times-circle me-1"></i>Passwords do not match</small>';
    }
    
    checkFormValidity();
}

function checkFormValidity() {
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('password-confirm').value;
    const email = document.getElementById('email').value;
    const submitBtn = document.getElementById('submitBtn');
    
    // Check if all requirements are met
    const requirements = {
        length: password.length >= 8,
        uppercase: /[A-Z]/.test(password),
        lowercase: /[a-z]/.test(password),
        number: /\d/.test(password),
        special: /[!@#$%^&*(),.?":{}|<>]/.test(password)
    };
    
    const allRequirementsMet = Object.values(requirements).every(req => req);
    const passwordsMatch = password === confirmPassword && password !== '';
    const emailValid = email !== '';
    
    if (allRequirementsMet && passwordsMatch && emailValid) {
        submitBtn.disabled = false;
        submitBtn.classList.remove('btn-secondary');
        submitBtn.classList.add('btn-primary');
    } else {
        submitBtn.disabled = true;
        submitBtn.classList.remove('btn-primary');
        submitBtn.classList.add('btn-secondary');
    }
}

// Initialize form validation
document.addEventListener('DOMContentLoaded', function() {
    checkFormValidity();
    
    // Add event listeners
    document.getElementById('email').addEventListener('input', checkFormValidity);
    document.getElementById('password').addEventListener('input', checkFormValidity);
    document.getElementById('password-confirm').addEventListener('input', checkFormValidity);
    
    // Force text color on all form inputs
    const inputs = document.querySelectorAll('input[type="email"], input[type="password"], input[type="text"]');
    inputs.forEach(input => {
        input.style.color = '#333';
        input.style.backgroundColor = '#fff';
        input.style.webkitTextFillColor = '#333';
    });
});
</script>
@endsection
