<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Shopybook</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Boldonse&family=Cinzel+Decorative:wght@400;700;900&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Roboto+Mono:ital,wght@0,100..700;1,100..700&display=swap" rel="stylesheet">
    <style>
        body, .register-container, .card {
            background: #fff !important;
            color: #020258 !important;
        }
        .btn-primary {
            background: #020258 !important;
            color: #fff !important;
            border: 2px solid #13e8e9 !important;
        }
        .btn-primary:hover {
            background: #13e8e9 !important;
            color: #020258 !important;
            border: 2px solid #020258 !important;
        }
        .form-control {
            background: #f8f9fa !important;
            color: #020258 !important;
            border: 2px solid #13e8e9 !important;
        }
        .form-control:focus {
            border-color: #020258 !important;
            box-shadow: 0 0 0 3px rgba(19, 232, 233, 0.1) !important;
        }
        body {
            font-family: 'Montserrat', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .register-container {
            background: rgba(2, 2, 88, 0.95);
            backdrop-filter: blur(10px);
            border: 2px solid #13e8e9;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(19, 232, 233, 0.2);
            overflow: hidden;
            width: 100%;
            max-width: 1000px;
            display: flex;
            min-height: 700px;
        }

        .register-image {
            background: #020258;
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            color: #13e8e9;
            text-align: center;
        }

        .register-image::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="white" opacity="0.1"/><circle cx="75" cy="75" r="1" fill="white" opacity="0.1"/><circle cx="50" cy="10" r="0.5" fill="white" opacity="0.1"/><circle cx="10" cy="60" r="0.5" fill="white" opacity="0.1"/><circle cx="90" cy="40" r="0.5" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
        }

        .register-image-content {
            text-align: center;
            color: white;
            z-index: 1;
            position: relative;
        }

        .register-image-content h2 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }

        .register-image-content p {
            font-size: 1.1rem;
            opacity: 0.9;
            margin-bottom: 2rem;
        }

        .features-list {
            text-align: left;
            margin-top: 2rem;
        }

        .features-list li {
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
        }

        .features-list i {
            margin-right: 0.5rem;
            color: #4ade80;
        }

        .register-form {
            flex: 1.2;
            padding: 3rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            overflow-y: auto;
            max-height: 700px;
            background: #020258;
        }

        .form-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .form-header h1 {
            font-family: "Cinzel Decorative", serif;
            color: #13e8e9;
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .form-header p {
            color: rgba(19, 232, 233, 0.8);
            font-size: 1rem;
        }

        .form-row {
            display: flex;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .form-group {
            flex: 1;
            position: relative;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            color: #13e8e9;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .form-control {
            width: 100%;
            padding: 0.75rem 1rem 0.75rem 3rem;
            border: 2px solid rgba(19, 232, 233, 0.3);
            border-radius: 12px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: #020258;
            color: #13e8e9;
        }

        .form-control:focus {
            outline: none;
            border-color: #13e8e9;
            background: rgba(2, 2, 88, 0.8);
            box-shadow: 0 0 0 3px rgba(19, 232, 233, 0.1);
        }

        .form-control::placeholder {
            color: rgba(19, 232, 233, 0.6);
        }

        .form-control.is-invalid {
            border-color: #dc3545;
            box-shadow: 0 0 0 3px rgba(220, 53, 69, 0.1);
        }

        .input-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #13e8e9;
            z-index: 2;
        }

        .password-toggle {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #6c757d;
            cursor: pointer;
            z-index: 3;
        }

        .password-toggle:hover {
            color: #667eea;
        }

        .form-check {
            display: flex;
            align-items: flex-start;
            margin-bottom: 1.5rem;
        }

        .form-check-input {
            margin-right: 0.5rem;
            margin-top: 0.2rem;
            width: 1.2rem;
            height: 1.2rem;
            border: 2px solid rgba(19, 232, 233, 0.3);
            border-radius: 4px;
            background: #020258;
        }

        .form-check-input:checked {
            background-color: #13e8e9;
            border-color: #13e8e9;
        }

        .form-check-label {
            color: rgba(19, 232, 233, 0.8);
            font-size: 0.9rem;
            line-height: 1.4;
        }

        .form-check-label a {
            color: #13e8e9;
            text-decoration: none;
        }

        .form-check-label a:hover {
            text-decoration: underline;
        }

        .btn-register {
            width: 100%;
            padding: 0.75rem;
            background: #020258;
            border: 2px solid #13e8e9;
            border-radius: 12px;
            color: #13e8e9;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-bottom: 1rem;
        }

        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(19, 232, 233, 0.3);
            background: linear-gradient(135deg, #020258 0%, #13e8e9 100%);
            color: #13e8e9;
        }

        .divider {
            text-align: center;
            margin: 1.5rem 0;
            position: relative;
        }

        .divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: rgba(19, 232, 233, 0.3);
        }

        .divider span {
            background: rgba(2, 2, 88, 0.8);
            padding: 0 1rem;
            color: rgba(19, 232, 233, 0.8);
            font-size: 0.9rem;
        }

        .login-link {
            text-align: center;
            margin-top: 1rem;
        }

        .login-link a {
            color: #13e8e9;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        .login-link a:hover {
            color: rgba(19, 232, 233, 0.7);
        }

        .invalid-feedback {
            display: block;
            color: #dc3545;
            font-size: 0.8rem;
            margin-top: 0.25rem;
        }

        .floating-shapes {
            position: absolute;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: 0;
        }

        .shape {
            position: absolute;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            animation: float 6s ease-in-out infinite;
        }

        .shape:nth-child(1) {
            width: 80px;
            height: 80px;
            top: 20%;
            left: 10%;
            animation-delay: 0s;
        }

        .shape:nth-child(2) {
            width: 60px;
            height: 60px;
            top: 60%;
            right: 20%;
            animation-delay: 2s;
        }

        .shape:nth-child(3) {
            width: 40px;
            height: 40px;
            bottom: 20%;
            left: 20%;
            animation-delay: 4s;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
        }

        .password-strength {
            margin-top: 0.5rem;
            font-size: 0.8rem;
        }

        .strength-bar {
            height: 4px;
            border-radius: 2px;
            background: #e1e5e9;
            margin-top: 0.25rem;
            overflow: hidden;
        }

        .strength-fill {
            height: 100%;
            transition: all 0.3s ease;
            border-radius: 2px;
        }

        .strength-weak { background: #dc3545; width: 25%; }
        .strength-fair { background: #ffc107; width: 50%; }
        .strength-good { background: #17a2b8; width: 75%; }
        .strength-strong { background: #28a745; width: 100%; }

        @media (max-width: 768px) {
            .register-container {
                flex-direction: column;
                max-width: 400px;
            }

            .register-image {
                display: none;
            }

            .register-form {
                padding: 2rem;
            }

            .form-row {
                flex-direction: column;
                gap: 0;
            }
        }

        .loading {
            display: none;
        }

        .btn-register.loading {
            position: relative;
            color: transparent;
        }

        .btn-register.loading::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 20px;
            height: 20px;
            border: 2px solid transparent;
            border-top: 2px solid white;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: translate(-50%, -50%) rotate(0deg); }
            100% { transform: translate(-50%, -50%) rotate(360deg); }
        }

        .progress-indicator {
            display: flex;
            justify-content: center;
            margin-bottom: 2rem;
        }

        .progress-step {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #e1e5e9;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #666;
            font-weight: 600;
            margin: 0 0.5rem;
            transition: all 0.3s ease;
        }

        .progress-step.active {
            background: #667eea;
            color: white;
        }

        .progress-step.completed {
            background: #28a745;
            color: white;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-image">
            <div class="floating-shapes">
                <div class="shape"></div>
                <div class="shape"></div>
                <div class="shape"></div>
            </div>
            <div class="register-image-content">
                <h2>Join Shopybook!</h2>
                <p>Start managing your business efficiently with our comprehensive platform.</p>
                <div class="mt-4">
                    <i class="fas fa-rocket fa-3x opacity-75"></i>
                </div>
                <ul class="features-list">
                    <li><i class="fas fa-check-circle"></i> Easy inventory management</li>
                    <li><i class="fas fa-check-circle"></i> Sales tracking & analytics</li>
                    <li><i class="fas fa-check-circle"></i> Customer relationship tools</li>
                    <li><i class="fas fa-check-circle"></i> Marketing automation</li>
                    <li><i class="fas fa-check-circle"></i> Payment processing</li>
                </ul>
            </div>
        </div>
        
        <div class="register-form">
            <div class="form-header">
                <h1>Create Account</h1>
                <p>Fill in your details to get started</p>
            </div>

            <form method="POST" action="{{ route('register') }}" id="registerForm">
                @csrf

                <div class="form-row">
                    <div class="form-group">
                        <label for="name" class="form-label">First Name</label>
                        <div class="position-relative">
                            <i class="fas fa-user input-icon"></i>
                            <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" 
                                   name="name" value="{{ old('name') }}" required autocomplete="name" autofocus
                                   placeholder="Enter your first name">
                        </div>
                        @error('name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="last_name" class="form-label">Last Name</label>
                        <div class="position-relative">
                            <i class="fas fa-user input-icon"></i>
                            <input id="last_name" type="text" class="form-control @error('last_name') is-invalid @enderror" 
                                   name="last_name" value="{{ old('last_name') }}" required autocomplete="family-name"
                                   placeholder="Enter your last name">
                        </div>
                        @error('last_name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label for="email" class="form-label">Email Address</label>
                    <div class="position-relative">
                        <i class="fas fa-envelope input-icon"></i>
                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" 
                               name="email" value="{{ old('email') }}" required autocomplete="email"
                               placeholder="Enter your email address">
                    </div>
                    @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="phone" class="form-label">Phone Number</label>
                    <div class="position-relative">
                        <i class="fas fa-phone input-icon"></i>
                        <input id="phone" type="tel" class="form-control @error('phone') is-invalid @enderror" 
                               name="phone" value="{{ old('phone') }}" required autocomplete="tel"
                               placeholder="Enter your phone number">
                    </div>
                    @error('phone')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <div class="position-relative">
                        <i class="fas fa-lock input-icon"></i>
                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" 
                               name="password" required autocomplete="new-password"
                               placeholder="Create a strong password">
                        <button type="button" class="password-toggle" onclick="togglePassword('password', 'passwordToggle')">
                            <i class="fas fa-eye" id="passwordToggle"></i>
                        </button>
                    </div>
                    <div class="password-strength">
                        <span id="strengthText">Password strength: </span>
                        <div class="strength-bar">
                            <div class="strength-fill" id="strengthBar"></div>
                        </div>
                    </div>
                    @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password_confirmation" class="form-label">Confirm Password</label>
                    <div class="position-relative">
                        <i class="fas fa-lock input-icon"></i>
                        <input id="password_confirmation" type="password" class="form-control" 
                               name="password_confirmation" required autocomplete="new-password"
                               placeholder="Confirm your password">
                        <button type="button" class="password-toggle" onclick="togglePassword('password_confirmation', 'confirmPasswordToggle')">
                            <i class="fas fa-eye" id="confirmPasswordToggle"></i>
                        </button>
                    </div>
                </div>

                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="terms" id="terms" required>
                    <label class="form-check-label" for="terms">
                        I agree to the <a href="#" target="_blank">Terms of Service</a> and <a href="#" target="_blank">Privacy Policy</a>
                    </label>
                </div>

                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="newsletter" id="newsletter">
                    <label class="form-check-label" for="newsletter">
                        I would like to receive updates and marketing communications
                    </label>
                </div>

                <button type="submit" class="btn-register" id="registerBtn">
                    <span class="btn-text">Create Account</span>
                    <span class="loading">Creating account...</span>
                </button>

                <div class="divider">
                    <span>Already have an account?</span>
                </div>

                <div class="login-link">
                    <a href="{{ route('login') }}">
                        <i class="fas fa-sign-in-alt me-1"></i>
                        Sign in to your account
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function togglePassword(inputId, toggleId) {
            const passwordInput = document.getElementById(inputId);
            const passwordToggle = document.getElementById(toggleId);
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                passwordToggle.classList.remove('fa-eye');
                passwordToggle.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                passwordToggle.classList.remove('fa-eye-slash');
                passwordToggle.classList.add('fa-eye');
            }
        }

        function checkPasswordStrength(password) {
            let strength = 0;
            const strengthText = document.getElementById('strengthText');
            const strengthBar = document.getElementById('strengthBar');
            
            if (password.length >= 8) strength++;
            if (/[a-z]/.test(password)) strength++;
            if (/[A-Z]/.test(password)) strength++;
            if (/[0-9]/.test(password)) strength++;
            if (/[^A-Za-z0-9]/.test(password)) strength++;
            
            strengthBar.className = 'strength-fill';
            
            if (strength <= 2) {
                strengthText.textContent = 'Password strength: Weak';
                strengthBar.classList.add('strength-weak');
            } else if (strength === 3) {
                strengthText.textContent = 'Password strength: Fair';
                strengthBar.classList.add('strength-fair');
            } else if (strength === 4) {
                strengthText.textContent = 'Password strength: Good';
                strengthBar.classList.add('strength-good');
            } else {
                strengthText.textContent = 'Password strength: Strong';
                strengthBar.classList.add('strength-strong');
            }
        }

        document.getElementById('password').addEventListener('input', function() {
            checkPasswordStrength(this.value);
        });

        document.getElementById('registerForm').addEventListener('submit', function() {
            const btn = document.getElementById('registerBtn');
            const btnText = btn.querySelector('.btn-text');
            const loading = btn.querySelector('.loading');
            
            btn.classList.add('loading');
            btnText.style.display = 'none';
            loading.style.display = 'inline';
            btn.disabled = true;
        });

        // Add floating animation to form elements
        const formGroups = document.querySelectorAll('.form-group');
        formGroups.forEach((group, index) => {
            group.style.animationDelay = `${index * 0.1}s`;
            group.style.animation = 'fadeInUp 0.6s ease forwards';
            group.style.opacity = '0';
            group.style.transform = 'translateY(20px)';
        });

        // Add CSS for fadeInUp animation
        const style = document.createElement('style');
        style.textContent = `
            @keyframes fadeInUp {
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
        `;
        document.head.appendChild(style);

        // Real-time validation
        const inputs = document.querySelectorAll('.form-control');
        inputs.forEach(input => {
            input.addEventListener('blur', function() {
                if (this.value.trim() === '') {
                    this.classList.add('is-invalid');
                } else {
                    this.classList.remove('is-invalid');
                }
            });
        });
    </script>
</body>
</html>
