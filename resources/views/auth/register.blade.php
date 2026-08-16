<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Shopybook</title>
    <meta name="description" content="Create your free Shopybook account and start managing your business today.">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair:wght@400;500;600;700;800;900&family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="{{ asset('jeriah/css/shopybook.css?v=' . filemtime(public_path('jeriah/css/shopybook.css'))) }}" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Poppins', sans-serif;
            background: #f7f7f7;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            color: #333;
        }

        .auth-container {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.12);
            overflow: hidden;
            width: 100%;
            max-width: 980px;
            display: flex;
            min-height: 620px;
        }

        {{-- Left panel --}}
        .auth-aside {
            background: #7b2e2e;
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
            padding: 2.5rem;
        }

        .auth-aside::before {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: #ff511a;
        }

        .auth-aside-content {
            text-align: center;
            color: #fff;
            z-index: 1;
            position: relative;
        }

        .auth-aside-content h2 {
            font-family: 'Playfair', serif;
            font-size: 2.2rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .auth-aside-content p {
            font-size: 1rem;
            opacity: 0.92;
            margin-bottom: 1.5rem;
            line-height: 1.6;
        }

        .auth-aside-content .icon-wrap {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: rgba(255, 81, 26, 0.18);
            border: 2px solid #ff511a;
            margin-top: 0.5rem;
            margin-bottom: 1.5rem;
        }

        .auth-aside-content .icon-wrap i {
            font-size: 2rem;
            color: #ff511a;
        }

        .features-list {
            list-style: none;
            padding: 0;
            margin: 0;
            text-align: left;
            display: inline-block;
        }

        .features-list li {
            padding: 7px 0;
            font-size: 0.92rem;
            opacity: 0.95;
        }

        .features-list li i {
            color: #ff511a;
            margin-right: 10px;
        }

        {{-- Right panel (form) --}}
        .auth-form {
            flex: 1.2;
            padding: 2.5rem 2.75rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: #fff;
            overflow-y: auto;
            max-height: 100%;
        }

        .form-header {
            text-align: center;
            margin-bottom: 1.5rem;
        }

        .form-header h1 {
            font-family: 'Playfair', serif;
            font-size: 1.9rem;
            font-weight: 700;
            color: #7b2e2e;
            margin-bottom: 0.5rem;
        }

        .form-header p {
            color: #777;
            font-size: 0.95rem;
            margin: 0;
        }

        .form-row {
            display: flex;
            gap: 1rem;
        }

        .form-row .form-group {
            flex: 1;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-label {
            display: block;
            font-size: 0.82rem;
            font-weight: 600;
            color: #7b2e2e;
            margin-bottom: 0.35rem;
        }

        .input-wrap {
            position: relative;
        }

        .input-wrap .input-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #b09b9b;
            font-size: 0.95rem;
            z-index: 2;
        }

        .form-control {
            width: 100%;
            padding: 0.7rem 2.5rem 0.7rem 2.6rem;
            border: 1.5px solid #e0ded9;
            border-radius: 8px;
            font-size: 0.92rem;
            font-family: 'Poppins', sans-serif;
            color: #333;
            background: #faf8f7;
            transition: border-color 0.25s, box-shadow 0.25s, background 0.25s;
        }

        .form-control:focus {
            outline: none;
            border-color: #ff511a;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(255, 81, 26, 0.12);
        }

        .form-control.is-invalid {
            border-color: #dc3545;
        }

        .password-toggle-btn {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            z-index: 3;
            color: #b09b9b;
            border: none;
            background: none;
            cursor: pointer;
            padding: 4px;
        }

        .password-toggle-btn:hover { color: #ff511a; }

        .invalid-feedback {
            display: block;
            color: #dc3545;
            font-size: 0.78rem;
            margin-top: 4px;
        }

        .password-strength {
            margin-top: 0.5rem;
            font-size: 0.78rem;
            color: #777;
        }

        .strength-bar {
            height: 4px;
            background: #e0ded9;
            border-radius: 2px;
            margin-top: 4px;
            overflow: hidden;
        }

        .strength-fill {
            height: 100%;
            width: 0;
            border-radius: 2px;
            transition: width 0.3s, background 0.3s;
        }

        .strength-weak { width: 33%; background: #dc3545; }
        .strength-fair { width: 66%; background: #ffc107; }
        .strength-good { width: 80%; background: #43ba7f; }
        .strength-strong { width: 100%; background: #43ba7f; }

        .form-check {
            display: flex;
            align-items: flex-start;
            gap: 8px;
            margin: 0.75rem 0;
        }

        .form-check-input {
            width: 16px;
            height: 16px;
            border-color: #e0ded9;
            cursor: pointer;
            flex-shrink: 0;
            margin-top: 2px;
        }

        .form-check-input:checked {
            background-color: #ff511a;
            border-color: #ff511a;
        }

        .form-check-label {
            font-size: 0.85rem;
            color: #555;
            cursor: pointer;
        }

        .form-check-label a {
            color: #ff511a;
            text-decoration: none;
            font-weight: 500;
        }

        .form-check-label a:hover { text-decoration: underline; }

        .btn-auth {
            width: 100%;
            background: #ff511a;
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 0.85rem 1rem;
            font-size: 1rem;
            font-weight: 600;
            font-family: 'Poppins', sans-serif;
            cursor: pointer;
            transition: background 0.25s, transform 0.15s;
            margin-top: 0.5rem;
        }

        .btn-auth:hover {
            background: #e8450f;
            transform: translateY(-1px);
        }

        .btn-auth:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
        }

        .btn-auth .loading { display: none; }
        .btn-auth.loading .btn-text { display: none; }
        .btn-auth.loading .loading { display: inline; }

        .auth-divider {
            display: flex;
            align-items: center;
            text-align: center;
            margin: 1.25rem 0;
            color: #999;
            font-size: 0.85rem;
        }

        .auth-divider::before,
        .auth-divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #e0ded9;
        }

        .auth-divider span { padding: 0 14px; }

        .auth-alt-link {
            text-align: center;
        }

        .auth-alt-link a {
            display: inline-block;
            padding: 0.7rem 1.5rem;
            border: 1.5px solid #7b2e2e;
            border-radius: 8px;
            color: #7b2e2e;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.92rem;
            transition: background 0.25s, color 0.25s;
        }

        .auth-alt-link a:hover {
            background: #7b2e2e;
            color: #fff;
        }

        {{-- Loader --}}
        .loader-modal {
            position: fixed;
            inset: 0;
            background: rgba(123, 46, 46, 0.92);
            backdrop-filter: blur(8px);
            z-index: 9999;
            display: none;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.4s ease;
        }

        .loader-modal.active {
            display: flex;
            opacity: 1;
        }

        .loader-box {
            text-align: center;
            color: #fff;
            max-width: 360px;
            padding: 0 1rem;
        }

        .loader-spinner {
            width: 56px;
            height: 56px;
            border: 4px solid rgba(255, 81, 26, 0.25);
            border-top-color: #ff511a;
            border-radius: 50%;
            margin: 0 auto 1.25rem;
            animation: spin 0.9s linear infinite;
        }

        @keyframes spin { to { transform: rotate(360deg); } }

        .loader-title {
            font-family: 'Playfair', serif;
            font-size: 1.3rem;
            margin-bottom: 1rem;
        }

        .loading-progress {
            width: 280px;
            max-width: 100%;
            height: 6px;
            background: rgba(255, 255, 255, 0.15);
            border-radius: 3px;
            margin: 0 auto 1rem;
            overflow: hidden;
        }

        .progress-bar {
            height: 100%;
            width: 0;
            background: #ff511a;
            border-radius: 3px;
            transition: width 0.4s ease;
        }

        .progress-percentage {
            font-size: 0.85rem;
            opacity: 0.9;
            margin-bottom: 0.75rem;
        }

        .loading-steps {
            text-align: left;
            display: inline-block;
            font-size: 0.82rem;
        }

        .loading-steps .step {
            padding: 3px 0;
            opacity: 0.55;
            transition: opacity 0.3s;
        }

        .loading-steps .step i {
            margin-right: 8px;
            color: #ff511a;
        }

        .loading-steps .step.active { opacity: 1; }
        .loading-steps .step.completed { opacity: 0.85; }
        .loading-steps .step.completed i { color: #43ba7f; }

        @media (max-width: 768px) {
            body { padding: 0; }
            .auth-container {
                flex-direction: column;
                max-width: 480px;
                min-height: auto;
            }
            .auth-aside { padding: 2rem 1.5rem 2.5rem; }
            .auth-aside-content h2 { font-size: 1.7rem; }
            .features-list { display: none; }
            .auth-form { padding: 2rem 1.5rem; }
            .form-header h1 { font-size: 1.5rem; }
            .form-row { flex-direction: column; gap: 0; }
        }
    </style>
</head>
<body>
    <div class="auth-container">
        {{-- Left panel --}}
        <div class="auth-aside">
            <div class="auth-aside-content">
                <h2>Join Shopybook!</h2>
                <p>Start managing your business efficiently with our comprehensive platform.</p>
                <div class="icon-wrap">
                    <i class="fas fa-rocket"></i>
                </div>
                <ul class="features-list">
                    <li><i class="fas fa-check-circle"></i> Easy inventory management</li>
                    <li><i class="fas fa-check-circle"></i> Sales tracking &amp; analytics</li>
                    <li><i class="fas fa-check-circle"></i> Customer relationship tools</li>
                    <li><i class="fas fa-check-circle"></i> Marketing automation</li>
                    <li><i class="fas fa-check-circle"></i> M-Pesa payment processing</li>
                </ul>
            </div>
        </div>

        {{-- Right panel (form) --}}
        <div class="auth-form">
            <div class="form-header">
                <h1>Create Account</h1>
                <p>Fill in your details to get started</p>
            </div>

            <form method="POST" action="{{ route('register') }}" id="registerForm">
                @csrf

                <div class="form-row">
                    <div class="form-group">
                        <label for="name" class="form-label">First Name</label>
                        <div class="input-wrap">
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
                        <div class="input-wrap">
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
                    <div class="input-wrap">
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
                    <div class="input-wrap">
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
                    <div class="input-wrap">
                        <i class="fas fa-lock input-icon"></i>
                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
                               name="password" required autocomplete="new-password"
                               placeholder="Create a strong password">
                        <button type="button" class="password-toggle-btn" onclick="togglePassword('password', 'passwordToggle')">
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
                    <div class="input-wrap">
                        <i class="fas fa-lock input-icon"></i>
                        <input id="password_confirmation" type="password" class="form-control"
                               name="password_confirmation" required autocomplete="new-password"
                               placeholder="Confirm your password">
                        <button type="button" class="password-toggle-btn" onclick="togglePassword('password_confirmation', 'confirmPasswordToggle')">
                            <i class="fas fa-eye" id="confirmPasswordToggle"></i>
                        </button>
                    </div>
                </div>

                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="terms" id="terms" required>
                    <label class="form-check-label" for="terms">
                        I agree to the <a href="{{ route('terms-of-service') }}" target="_blank">Terms of Service</a> and <a href="{{ route('privacy-policy') }}" target="_blank">Privacy Policy</a>
                    </label>
                </div>

                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="newsletter" id="newsletter">
                    <label class="form-check-label" for="newsletter">
                        I would like to receive updates and marketing communications
                    </label>
                </div>

                <button type="submit" class="btn-auth" id="registerBtn">
                    <span class="btn-text">Create Account</span>
                    <span class="loading">Creating account...</span>
                </button>

                <div class="auth-divider">
                    <span>Already have an account?</span>
                </div>

                <div class="auth-alt-link">
                    <a href="{{ route('login') }}">
                        <i class="fas fa-sign-in-alt me-1"></i>
                        Sign in to your account
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Loader --}}
    <div id="futuristicLoader" class="loader-modal">
        <div class="loader-box">
            <div class="loader-spinner"></div>
            <h3 class="loader-title">Creating your account</h3>
            <div class="loading-progress">
                <div class="progress-bar"></div>
            </div>
            <div class="progress-percentage">0%</div>
            <p class="loading-status" style="font-size:0.9rem; opacity:0.9; margin-bottom:0.75rem;">Preparing registration system...</p>
            <div class="loading-steps">
                <div class="step" id="step1"><i class="fas fa-check"></i> Validating input data</div>
                <div class="step" id="step2"><i class="fas fa-hourglass-half"></i> Encrypting credentials</div>
                <div class="step" id="step3"><i class="fas fa-hourglass-half"></i> Creating user profile</div>
                <div class="step" id="step4"><i class="fas fa-hourglass-half"></i> Setting up workspace</div>
                <div class="step" id="step5"><i class="fas fa-hourglass-half"></i> Finalizing account</div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function togglePassword(fieldId, toggleId) {
            const input = document.getElementById(fieldId);
            const icon = document.getElementById(toggleId);
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
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

        document.getElementById('password').addEventListener('input', function () {
            checkPasswordStrength(this.value);
        });

        document.getElementById('registerForm').addEventListener('submit', function () {
            const btn = document.getElementById('registerBtn');
            const loader = document.getElementById('futuristicLoader');
            const statusText = loader.querySelector('.loading-status');
            const progressBar = loader.querySelector('.progress-bar');
            const progressPercentage = loader.querySelector('.progress-percentage');

            loader.classList.add('active');
            btn.classList.add('loading');
            btn.disabled = true;

            const steps = [
                { id: 'step1', message: 'Validating registration data...', duration: 800 },
                { id: 'step2', message: 'Encrypting user credentials...', duration: 1000 },
                { id: 'step3', message: 'Creating user profile...', duration: 1200 },
                { id: 'step4', message: 'Setting up user workspace...', duration: 900 },
                { id: 'step5', message: 'Finalizing account creation...', duration: 700 }
            ];

            let currentStep = 0;
            let progress = 0;

            function executeStep() {
                if (currentStep < steps.length) {
                    const step = steps[currentStep];
                    statusText.textContent = step.message;

                    const stepElement = document.getElementById(step.id);
                    stepElement.classList.add('active');
                    stepElement.querySelector('i').className = 'fas fa-cog fa-spin';

                    progress = ((currentStep + 1) / steps.length) * 100;
                    progressBar.style.width = progress + '%';
                    progressPercentage.textContent = Math.round(progress) + '%';

                    setTimeout(() => {
                        stepElement.classList.remove('active');
                        stepElement.classList.add('completed');
                        stepElement.querySelector('i').className = 'fas fa-check';
                        currentStep++;
                        executeStep();
                    }, step.duration);
                } else {
                    statusText.textContent = 'Registration complete! Redirecting...';
                }
            }

            setTimeout(executeStep, 500);
        });

        // Real-time validation
        const inputs = document.querySelectorAll('.form-control');
        inputs.forEach(input => {
            input.addEventListener('blur', function () {
                if (this.value.trim() === '' && this.hasAttribute('required')) {
                    this.classList.add('is-invalid');
                } else {
                    this.classList.remove('is-invalid');
                }
            });
        });
    </script>
</body>
</html>
