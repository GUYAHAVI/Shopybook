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
            overflow-x: hidden;
        }

        @media (max-width: 768px) {
            body {
                padding: 10px;
                align-items: flex-start;
                padding-top: 20px;
            }
        }

        .register-container {
            background: rgba(2, 2, 88, 0.95);
            backdrop-filter: blur(10px);
            border: 2px solid #13e8e9;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(19, 232, 233, 0.2);
            overflow: visible;
            width: 100%;
            max-width: 1200px;
            display: flex;
            min-height: 80vh;
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
            padding: 2rem;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            background: rgba(2, 2, 88, 0.9);
            backdrop-filter: blur(15px);
            position: relative;
            overflow: visible;
        }

        .register-form::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(19, 232, 233, 0.05) 0%, rgba(255, 107, 157, 0.05) 50%, rgba(78, 205, 196, 0.05) 100%);
            z-index: 1;
        }

        .register-form > * {
            position: relative;
            z-index: 2;
        }

        .form-header {
            text-align: center;
            margin-bottom: 2rem;
            animation: slideInDown 0.8s ease;
        }

        .form-header h1 {
            font-family: "Cinzel Decorative", serif;
            color: #13e8e9;
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            text-shadow: 0 0 20px rgba(19, 232, 233, 0.5);
            animation: glow 2s ease-in-out infinite alternate;
        }

        .form-header p {
            color: rgba(19, 232, 233, 0.8);
            font-size: 1rem;
            opacity: 0;
            animation: fadeIn 1s ease 0.5s forwards;
        }

        .form-row {
            display: flex;
            gap: 1rem;
            margin-bottom: 1.5rem;
            opacity: 0;
            transform: translateY(30px);
            animation: slideInUp 0.6s ease forwards;
        }

        .form-group {
            flex: 1;
            position: relative;
            margin-bottom: 1.5rem;
            opacity: 0;
            transform: translateY(30px);
            animation: slideInUp 0.6s ease forwards;
        }

        .form-group:nth-child(1) { animation-delay: 0.1s; }
        .form-group:nth-child(2) { animation-delay: 0.2s; }
        .form-group:nth-child(3) { animation-delay: 0.3s; }
        .form-group:nth-child(4) { animation-delay: 0.4s; }
        .form-group:nth-child(5) { animation-delay: 0.5s; }
        .form-group:nth-child(6) { animation-delay: 0.6s; }

        .form-row:nth-child(1) { animation-delay: 0.1s; }
        .form-row:nth-child(2) { animation-delay: 0.2s; }
        .form-row:nth-child(3) { animation-delay: 0.3s; }

        .form-label {
            display: block;
            margin-bottom: 0.8rem;
            color: #13e8e9;
            font-weight: 600;
            font-size: 0.95rem;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .form-control {
            width: 100%;
            padding: 0.875rem 1rem 0.875rem 3rem;
            border: 2px solid rgba(19, 232, 233, 0.3);
            border-radius: 15px;
            font-size: 1rem;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            background: rgba(19, 232, 233, 0.05);
            backdrop-filter: blur(10px);
            color: #13e8e9;
            box-shadow: 0 8px 32px rgba(19, 232, 233, 0.1);
        }

        .form-control:focus {
            outline: none;
            border-color: #13e8e9;
            background: rgba(19, 232, 233, 0.1);
            box-shadow: 0 0 0 4px rgba(19, 232, 233, 0.2), 0 15px 35px rgba(19, 232, 233, 0.2);
            transform: translateY(-2px);
        }

        .form-control::placeholder {
            color: rgba(19, 232, 233, 0.5);
            font-style: italic;
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
            z-index: 3;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus + .input-icon {
            color: #fff;
            transform: translateY(-50%) scale(1.1);
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
            padding: 0.875rem;
            background: linear-gradient(135deg, #13e8e9 0%, #ff6b9d 50%, #4ecdc4 100%);
            background-size: 200% 200%;
            border: none;
            border-radius: 15px;
            color: #020258;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            margin-bottom: 1.5rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            box-shadow: 0 10px 30px rgba(19, 232, 233, 0.3);
            position: relative;
            overflow: hidden;
            opacity: 0;
            transform: translateY(30px);
            animation: slideInUp 0.6s ease 0.8s forwards;
        }

        .btn-register::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .btn-register:hover {
            transform: translateY(-3px);
            box-shadow: 0 20px 40px rgba(19, 232, 233, 0.4);
            background-position: 100% 0;
        }

        .btn-register:hover::before {
            left: 100%;
        }

        .btn-register:active {
            transform: translateY(-1px);
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
            opacity: 0;
            transform: translateY(20px);
            animation: slideInUp 0.6s ease 0.9s forwards;
        }

        .login-link a {
            color: #13e8e9;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
        }

        .login-link a::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 2px;
            background: linear-gradient(135deg, #13e8e9, #ff6b9d);
            transition: width 0.3s ease;
        }

        .login-link a:hover {
            color: #ff6b9d;
            text-shadow: 0 0 10px rgba(255, 107, 157, 0.5);
        }

        .login-link a:hover::after {
            width: 100%;
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

        @keyframes slideInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInScale {
            0% {
                opacity: 0;
                transform: scale(0.95);
            }
            100% {
                opacity: 1;
                transform: scale(1);
            }
        }

        @keyframes glow {
            0%, 100% { 
                box-shadow: 0 0 20px rgba(19, 232, 233, 0.3);
            }
            50% { 
                box-shadow: 0 0 30px rgba(19, 232, 233, 0.6);
            }
        }

        @keyframes quantumOrbit {
            0% { transform: rotate(0deg) translateX(15px) rotate(0deg); }
            100% { transform: rotate(360deg) translateX(15px) rotate(-360deg); }
        }

        @keyframes quantumSpin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        @keyframes quantumPulse {
            0%, 100% { 
                transform: scale(1);
                opacity: 1;
            }
            50% { 
                transform: scale(1.3);
                opacity: 0.7;
            }
        }

        @keyframes progressBar {
            0% { width: 0%; }
            25% { width: 25%; }
            50% { width: 50%; }
            75% { width: 75%; }
            100% { width: 100%; }
        }

        @keyframes loadingDots {
            0%, 80%, 100% { transform: scale(0.8); opacity: 0.5; }
            40% { transform: scale(1.2); opacity: 1; }
        }

        @keyframes waveEffect {
            0% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
            100% { transform: translateY(0px) rotate(360deg); }
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
                max-width: 100%;
                min-height: auto;
                border-radius: 15px;
                margin: 10px;
            }

            .register-image {
                display: none;
            }

            .register-form {
                padding: 2rem 1.5rem;
                min-height: auto;
                justify-content: flex-start;
            }

            .form-header h1 {
                font-size: 1.8rem;
                margin-bottom: 0.5rem;
            }

            .form-header {
                margin-bottom: 1.5rem;
            }

            .form-row {
                flex-direction: column;
                gap: 0;
                margin-bottom: 1rem;
            }

            .form-group {
                margin-bottom: 1rem;
            }
        }

        @media (max-width: 480px) {
            body {
                padding: 5px;
            }

            .register-container {
                margin: 5px;
                border-radius: 10px;
            }

            .register-form {
                padding: 1.5rem 1rem;
            }

            .form-header h1 {
                font-size: 1.5rem;
            }

            .form-control {
                padding: 0.875rem 1rem 0.875rem 3rem;
                font-size: 0.95rem;
            }

            .input-icon {
                font-size: 1rem;
                left: 0.875rem;
            }

            .btn-register {
                padding: 0.875rem;
                font-size: 1rem;
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

    <!-- Futuristic Registration Loader Modal -->
    <div id="futuristicLoader" class="loader-modal">
        <div class="loader-container">
            <div class="quantum-loader">
                <div class="quantum-ring">
                    <div class="quantum-particle"></div>
                    <div class="quantum-particle"></div>
                    <div class="quantum-particle"></div>
                </div>
                <div class="quantum-ring ring-2">
                    <div class="quantum-particle"></div>
                    <div class="quantum-particle"></div>
                </div>
                <div class="quantum-ring ring-3">
                    <div class="quantum-particle"></div>
                </div>
                <div class="quantum-core">
                    <div class="core-pulse"></div>
                    <div class="core-inner">
                        <i class="fas fa-user-plus"></i>
                    </div>
                </div>
                <div class="orbital-elements">
                    <div class="data-node" style="--angle: 0deg;"><i class="fas fa-envelope"></i></div>
                    <div class="data-node" style="--angle: 72deg;"><i class="fas fa-lock"></i></div>
                    <div class="data-node" style="--angle: 144deg;"><i class="fas fa-user"></i></div>
                    <div class="data-node" style="--angle: 216deg;"><i class="fas fa-shield-alt"></i></div>
                    <div class="data-node" style="--angle: 288deg;"><i class="fas fa-database"></i></div>
                </div>
            </div>
            <div class="loader-text">
                <h3 class="loading-title">Initializing Account Creation Matrix</h3>
                <div class="loading-progress">
                    <div class="progress-bar"></div>
                    <div class="progress-percentage">0%</div>
                </div>
                <p class="loading-status">Preparing registration system...</p>
                <div class="loading-steps">
                    <div class="step" id="step1"><i class="fas fa-check"></i> Validating input data</div>
                    <div class="step" id="step2"><i class="fas fa-hourglass-half"></i> Encrypting credentials</div>
                    <div class="step" id="step3"><i class="fas fa-hourglass-half"></i> Creating user profile</div>
                    <div class="step" id="step4"><i class="fas fa-hourglass-half"></i> Setting up workspace</div>
                    <div class="step" id="step5"><i class="fas fa-hourglass-half"></i> Finalizing account</div>
                </div>
            </div>
            <div class="particle-field">
                <div class="particle"></div>
                <div class="particle"></div>
                <div class="particle"></div>
                <div class="particle"></div>
                <div class="particle"></div>
                <div class="particle"></div>
                <div class="particle"></div>
                <div class="particle"></div>
                <div class="particle"></div>
                <div class="particle"></div>
            </div>
        </div>
    </div>

    <style>
        .loader-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(2, 2, 88, 0.95);
            backdrop-filter: blur(10px);
            z-index: 9999;
            display: none;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: all 0.5s ease;
        }

        .loader-modal.active {
            display: flex;
            opacity: 1;
        }

        .loader-container {
            text-align: center;
            position: relative;
            z-index: 2;
            max-width: 90%;
        }

        .quantum-loader {
            position: relative;
            width: 250px;
            height: 250px;
            margin: 0 auto 2rem;
        }

        .quantum-ring {
            position: absolute;
            top: 50%;
            left: 50%;
            border: 2px solid transparent;
            border-top: 2px solid #13e8e9;
            border-radius: 50%;
            animation: quantumSpin 2s linear infinite;
        }

        .quantum-ring.ring-2 {
            border-top: 2px solid #ff6b9d;
            animation: quantumSpin 1.5s linear infinite reverse;
        }

        .quantum-ring.ring-3 {
            border-top: 2px solid #4ecdc4;
            animation: quantumSpin 1s linear infinite;
        }

        .quantum-ring:nth-child(1) {
            width: 220px;
            height: 220px;
            margin: -110px 0 0 -110px;
        }

        .quantum-ring:nth-child(2) {
            width: 170px;
            height: 170px;
            margin: -85px 0 0 -85px;
        }

        .quantum-ring:nth-child(3) {
            width: 120px;
            height: 120px;
            margin: -60px 0 0 -60px;
        }

        .quantum-particle {
            position: absolute;
            width: 8px;
            height: 8px;
            background: #13e8e9;
            border-radius: 50%;
            box-shadow: 0 0 10px #13e8e9, 0 0 20px #13e8e9, 0 0 30px #13e8e9;
            animation: particleOrbit 3s linear infinite;
        }

        .ring-2 .quantum-particle {
            background: #ff6b9d;
            box-shadow: 0 0 10px #ff6b9d, 0 0 20px #ff6b9d, 0 0 30px #ff6b9d;
            animation: particleOrbit 2s linear infinite reverse;
        }

        .ring-3 .quantum-particle {
            background: #4ecdc4;
            box-shadow: 0 0 10px #4ecdc4, 0 0 20px #4ecdc4, 0 0 30px #4ecdc4;
            animation: particleOrbit 1.5s linear infinite;
        }

        .quantum-particle:nth-child(1) { animation-delay: 0s; }
        .quantum-particle:nth-child(2) { animation-delay: -1s; }
        .quantum-particle:nth-child(3) { animation-delay: -2s; }

        .quantum-core {
            position: absolute;
            top: 50%;
            left: 50%;
            width: 80px;
            height: 80px;
            margin: -40px 0 0 -40px;
            background: radial-gradient(circle, #13e8e9, #020258);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .core-pulse {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle, transparent 40%, #13e8e9 41%, #13e8e9 43%, transparent 44%);
            border-radius: 50%;
            animation: corePulse 2s ease-in-out infinite;
        }

        .core-inner {
            position: relative;
            z-index: 2;
            color: #fff;
            font-size: 2rem;
            animation: coreFloat 3s ease-in-out infinite;
        }

        .orbital-elements {
            position: absolute;
            top: 50%;
            left: 50%;
            width: 280px;
            height: 280px;
            margin: -140px 0 0 -140px;
        }

        .data-node {
            position: absolute;
            width: 30px;
            height: 30px;
            background: rgba(19, 232, 233, 0.2);
            border: 2px solid #13e8e9;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #13e8e9;
            font-size: 0.8rem;
            animation: nodeOrbit 8s linear infinite;
            transform-origin: 140px 140px;
            transform: rotate(var(--angle)) translateX(140px) rotate(calc(-1 * var(--angle)));
        }

        .data-node:nth-child(2) { animation-delay: -1.6s; }
        .data-node:nth-child(3) { animation-delay: -3.2s; }
        .data-node:nth-child(4) { animation-delay: -4.8s; }
        .data-node:nth-child(5) { animation-delay: -6.4s; }

        .loader-text {
            color: #13e8e9;
            margin-bottom: 1rem;
        }

        .loading-title {
            font-family: "Cinzel Decorative", serif;
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
            background: linear-gradient(45deg, #13e8e9, #ff6b9d, #4ecdc4);
            background-size: 300% 300%;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: gradientShift 3s ease infinite;
        }

        .loading-progress {
            width: 350px;
            height: 6px;
            background: rgba(19, 232, 233, 0.2);
            border-radius: 3px;
            margin: 0 auto 1rem;
            overflow: hidden;
            position: relative;
        }

        .progress-bar {
            height: 100%;
            background: linear-gradient(90deg, #13e8e9, #ff6b9d, #4ecdc4, #13e8e9);
            background-size: 300% 100%;
            border-radius: 3px;
            width: 0%;
            animation: progressFill 6s ease-in-out, gradientMove 2s linear infinite;
        }

        .progress-percentage {
            position: absolute;
            top: -25px;
            right: 0;
            font-size: 0.8rem;
            color: #13e8e9;
            font-weight: 600;
        }

        .loading-status {
            font-size: 1rem;
            opacity: 0.9;
            margin-bottom: 1.5rem;
            animation: statusPulse 2s ease-in-out infinite;
        }

        .loading-steps {
            text-align: left;
            max-width: 300px;
            margin: 0 auto;
        }

        .step {
            display: flex;
            align-items: center;
            margin-bottom: 0.5rem;
            font-size: 0.85rem;
            opacity: 0.5;
            transition: all 0.3s ease;
        }

        .step i {
            margin-right: 0.5rem;
            width: 16px;
        }

        .step.active {
            opacity: 1;
            color: #13e8e9;
        }

        .step.completed {
            opacity: 1;
            color: #4ecdc4;
        }

        .step.completed i {
            color: #4ecdc4;
        }

        .particle-field {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: 1;
        }

        .particle {
            position: absolute;
            width: 2px;
            height: 2px;
            background: #13e8e9;
            border-radius: 50%;
            opacity: 0;
            animation: particleFloat 8s linear infinite;
        }

        .particle:nth-child(odd) { background: #ff6b9d; }
        .particle:nth-child(3n) { background: #4ecdc4; }
        .particle:nth-child(1) { left: 10%; animation-delay: 0s; }
        .particle:nth-child(2) { left: 15%; animation-delay: -1s; }
        .particle:nth-child(3) { left: 25%; animation-delay: -2s; }
        .particle:nth-child(4) { left: 35%; animation-delay: -3s; }
        .particle:nth-child(5) { left: 45%; animation-delay: -4s; }
        .particle:nth-child(6) { left: 55%; animation-delay: -5s; }
        .particle:nth-child(7) { left: 65%; animation-delay: -1.5s; }
        .particle:nth-child(8) { left: 75%; animation-delay: -2.5s; }
        .particle:nth-child(9) { left: 85%; animation-delay: -3.5s; }
        .particle:nth-child(10) { left: 90%; animation-delay: -4.5s; }

        @keyframes quantumSpin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        @keyframes particleOrbit {
            0% { transform: rotate(0deg) translateX(110px) rotate(0deg); }
            100% { transform: rotate(360deg) translateX(110px) rotate(-360deg); }
        }

        @keyframes nodeOrbit {
            0% { transform: rotate(var(--angle)) translateX(140px) rotate(calc(-1 * var(--angle))); }
            100% { transform: rotate(calc(var(--angle) + 360deg)) translateX(140px) rotate(calc(-1 * (var(--angle) + 360deg))); }
        }

        @keyframes corePulse {
            0%, 100% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.2); opacity: 0.7; }
        }

        @keyframes coreFloat {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-5px); }
        }

        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        @keyframes progressFill {
            0% { width: 0%; }
            100% { width: 100%; }
        }

        @keyframes gradientMove {
            0% { background-position: 0% 50%; }
            100% { background-position: 100% 50%; }
        }

        @keyframes statusPulse {
            0%, 100% { opacity: 0.9; }
            50% { opacity: 1; }
        }

        @keyframes particleFloat {
            0% {
                transform: translateY(100vh) scale(0);
                opacity: 0;
            }
            10% {
                opacity: 1;
            }
            90% {
                opacity: 1;
            }
            100% {
                transform: translateY(-100px) scale(1);
                opacity: 0;
            }
        }

        @media (max-width: 768px) {
            .quantum-loader {
                width: 200px;
                height: 200px;
            }

            .quantum-ring:nth-child(1) {
                width: 180px;
                height: 180px;
                margin: -90px 0 0 -90px;
            }

            .quantum-ring:nth-child(2) {
                width: 140px;
                height: 140px;
                margin: -70px 0 0 -70px;
            }

            .quantum-ring:nth-child(3) {
                width: 100px;
                height: 100px;
                margin: -50px 0 0 -50px;
            }

            .orbital-elements {
                width: 220px;
                height: 220px;
                margin: -110px 0 0 -110px;
            }

            .data-node {
                transform-origin: 110px 110px;
                transform: rotate(var(--angle)) translateX(110px) rotate(calc(-1 * var(--angle)));
            }

            .loading-progress {
                width: 280px;
            }

            .loading-title {
                font-size: 1.2rem;
            }

            .loading-steps {
                max-width: 250px;
            }
        }
    </style>

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
            const loader = document.getElementById('futuristicLoader');
            const statusText = loader.querySelector('.loading-status');
            const progressBar = loader.querySelector('.progress-bar');
            const progressPercentage = loader.querySelector('.progress-percentage');
            
            // Show futuristic loader
            loader.classList.add('active');
            
            // Disable button
            btn.classList.add('loading');
            btnText.style.display = 'none';
            loading.style.display = 'inline';
            btn.disabled = true;
            
            // Registration steps with timing
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
                    
                    // Update status message
                    statusText.textContent = step.message;
                    
                    // Mark current step as active
                    const stepElement = document.getElementById(step.id);
                    stepElement.classList.add('active');
                    stepElement.querySelector('i').className = 'fas fa-cog fa-spin';
                    
                    // Update progress
                    progress = ((currentStep + 1) / steps.length) * 100;
                    progressBar.style.width = progress + '%';
                    progressPercentage.textContent = Math.round(progress) + '%';
                    
                    setTimeout(() => {
                        // Mark step as completed
                        stepElement.classList.remove('active');
                        stepElement.classList.add('completed');
                        stepElement.querySelector('i').className = 'fas fa-check';
                        
                        currentStep++;
                        executeStep();
                    }, step.duration);
                } else {
                    // All steps completed, update final message
                    statusText.textContent = 'Registration complete! Redirecting...';
                }
            }
            
            // Start the step execution after a short delay
            setTimeout(executeStep, 500);
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
