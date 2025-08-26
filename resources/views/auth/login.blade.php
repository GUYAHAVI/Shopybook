<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Shopybook</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Boldonse&family=Cinzel+Decorative:wght@400;700;900&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Roboto+Mono:ital,wght@0,100..700;1,100..700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Montserrat', sans-serif;
            background: #020258 !important;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 10px;
            color: #fff !important;
        }

        @media (max-width: 768px) {
            body {
                padding: 0;
            }
        }

        .login-container {
            background: #fff !important;
            backdrop-filter: blur(10px);
            border: 2px solid #13e8e9;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(19, 232, 233, 0.2);
            overflow: hidden;
            width: 100%;
            max-width: 900px;
            display: flex;
            min-height: 600px;
        }

        .login-image {
            background: #020258 !important;
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        .login-image::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="%2313e8e9" opacity="0.1"/><circle cx="75" cy="75" r="1" fill="%2313e8e9" opacity="0.1"/><circle cx="50" cy="10" r="0.5" fill="%2313e8e9" opacity="0.1"/><circle cx="10" cy="60" r="0.5" fill="%2313e8e9" opacity="0.1"/><circle cx="90" cy="40" r="0.5" fill="%2313e8e9" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
        }

        .login-image-content {
            text-align: center;
            color: #13e8e9;
            z-index: 1;
            position: relative;
        }

        .login-image-content h2 {
            font-family: "Cinzel Decorative", serif;
            font-size: 2.5rem;
            font-weight: 900;
            margin-bottom: 1rem;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }

        .login-image-content p {
            font-size: 1.1rem;
            opacity: 0.9;
            margin-bottom: 2rem;
        }

        .login-form {
            flex: 1;
            padding: 3rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: rgba(2, 2, 88, 0.9);
            backdrop-filter: blur(15px);
            position: relative;
            overflow: hidden;
        }

        .login-form::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(19, 232, 233, 0.05) 0%, rgba(255, 107, 157, 0.05) 50%, rgba(78, 205, 196, 0.05) 100%);
            z-index: 1;
        }

        .login-form > * {
            position: relative;
            z-index: 2;
        }

        .form-header {
            text-align: center;
            margin-bottom: 3rem;
            animation: slideInDown 0.8s ease;
        }

        .form-header h1 {
            font-family: "Cinzel Decorative", serif;
            color: #13e8e9;
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            text-shadow: 0 0 20px rgba(19, 232, 233, 0.5);
            animation: glow 2s ease-in-out infinite alternate;
        }

        .form-header p {
            color: rgba(19, 232, 233, 0.8);
            font-size: 1.1rem;
            opacity: 0;
            animation: fadeIn 1s ease 0.5s forwards;
        }

        .form-group {
            margin-bottom: 2rem;
            position: relative;
            opacity: 0;
            transform: translateY(30px);
            animation: slideInUp 0.6s ease forwards;
        }

        .form-group:nth-child(1) { animation-delay: 0.2s; }
        .form-group:nth-child(2) { animation-delay: 0.4s; }
        .form-group:nth-child(3) { animation-delay: 0.6s; }

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
            padding: 1rem 1.2rem 1rem 3.5rem;
            border: 2px solid rgba(19, 232, 233, 0.3);
            border-radius: 15px;
            font-size: 1.1rem;
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
            left: 1.2rem;
            top: 50%;
            transform: translateY(-50%);
            color: #13e8e9;
            z-index: 3;
            font-size: 1.1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus + .input-icon {
            color: #fff;
            transform: translateY(-50%) scale(1.1);
        }

        .form-check {
            display: flex;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .form-check-input {
            margin-right: 0.5rem;
            width: 1.2rem;
            height: 1.2rem;
            border: 2px solid rgba(19, 232, 233, 0.3);
            border-radius: 4px;
            background: rgba(2, 2, 88, 0.5);
        }

        .form-check-input:checked {
            background-color: #13e8e9;
            border-color: #13e8e9;
        }

        .form-check-label {
            color: rgba(19, 232, 233, 0.8);
            font-size: 0.9rem;
        }

        .btn-login {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(135deg, #13e8e9 0%, #ff6b9d 50%, #4ecdc4 100%);
            background-size: 200% 200%;
            border: none;
            border-radius: 15px;
            color: #020258;
            font-size: 1.1rem;
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

        .btn-login::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .btn-login:hover {
            transform: translateY(-3px);
            box-shadow: 0 20px 40px rgba(19, 232, 233, 0.4);
            background-position: 100% 0;
        }

        .btn-login:hover::before {
            left: 100%;
        }

        .btn-login:active {
            transform: translateY(-1px);
        }

        .forgot-password {
            text-align: center;
            margin-bottom: 1.5rem;
        }

        .forgot-password a {
            color: #13e8e9;
            text-decoration: none;
            font-size: 0.9rem;
            transition: color 0.3s ease;
        }

        .forgot-password a:hover {
            color: rgba(19, 232, 233, 0.7);
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

        .register-link {
            text-align: center;
            margin-top: 1rem;
        }

        .register-link a {
            color: #13e8e9;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        .register-link a:hover {
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
            background: rgba(19, 232, 233, 0.1);
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

        @media (max-width: 768px) {
            .login-container {
                flex-direction: column;
                max-width: 100%;
                min-height: 100vh;
                border-radius: 0;
                border: none;
                box-shadow: none;
            }

            .login-image {
                display: none;
            }

            .login-form {
                padding: 1.5rem;
                min-height: 100vh;
                justify-content: center;
            }

            .form-header h1 {
                font-size: 1.75rem;
            }
        }

        .loading {
            display: none;
        }

        .btn-login.loading {
            position: relative;
            color: transparent;
        }

        .btn-login.loading::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 20px;
            height: 20px;
            border: 2px solid transparent;
            border-top: 2px solid #020258;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: translate(-50%, -50%) rotate(0deg); }
            100% { transform: translate(-50%, -50%) rotate(360deg); }
        }

        @keyframes glow {
            from { text-shadow: 0 0 20px rgba(19, 232, 233, 0.5); }
            to { text-shadow: 0 0 30px rgba(19, 232, 233, 0.8), 0 0 40px rgba(19, 232, 233, 0.6); }
        }

        @keyframes slideInDown {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        body, .login-container, .card {
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
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-image">
            <div class="floating-shapes">
                <div class="shape"></div>
                <div class="shape"></div>
                <div class="shape"></div>
            </div>
            <div class="login-image-content">
                <h2>Welcome Back!</h2>
                <p>Sign in to your Shopybook account and manage your business with ease.</p>
                <div class="mt-4">
                    <i class="fas fa-store fa-3x opacity-75"></i>
                </div>
            </div>
        </div>
        
        <div class="login-form">
            <div class="form-header">
                <h1>Sign In</h1>
                <p>Enter your credentials to access your account</p>
            </div>

            <form method="POST" action="{{ route('login') }}" id="loginForm">
                @csrf

                <div class="form-group">
                    <label for="email" class="form-label">Email Address</label>
                    <div class="position-relative">
                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" 
                               name="email" value="{{ old('email') }}" required autocomplete="email" autofocus
                               placeholder="Enter your email address">
                        <i class="fas fa-envelope input-icon"></i>
                    </div>
                    @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <div class="position-relative">
                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" 
                               name="password" required autocomplete="current-password"
                               placeholder="Enter your password">
                        <i class="fas fa-lock input-icon"></i>
                        <button type="button" class="btn btn-link position-absolute" style="right: 15px; top: 50%; transform: translateY(-50%); z-index: 3; color: #13e8e9; border: none; background: none;" onclick="togglePassword()">
                            <i class="fas fa-eye" id="passwordToggle"></i>
                        </button>
                    </div>
                    @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                    <label class="form-check-label" for="remember">
                        Remember me for 30 days
                    </label>
                </div>

                <button type="submit" class="btn-login" id="loginBtn">
                    <span class="btn-text">Sign In</span>
                    <span class="loading">Signing in...</span>
                </button>

                @if (Route::has('password.request'))
                    <div class="forgot-password">
                        <a href="{{ route('password.request') }}">
                            <i class="fas fa-question-circle me-1"></i>
                            Forgot your password?
                        </a>
                    </div>
                @endif

                <div class="divider">
                    <span>New to Shopybook?</span>
                </div>

                <div class="register-link">
                    <a href="{{ route('register') }}">
                        <i class="fas fa-user-plus me-1"></i>
                        Create a new account
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Futuristic Loader Modal -->
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
                        <i class="fas fa-store"></i>
                    </div>
                </div>
            </div>
            <div class="loader-text">
                <h3 class="loading-title">Initializing Quantum Login</h3>
                <div class="loading-progress">
                    <div class="progress-bar"></div>
                </div>
                <p class="loading-status">Authenticating credentials...</p>
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
        }

        .quantum-loader {
            position: relative;
            width: 200px;
            height: 200px;
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

        .quantum-particle:nth-child(1) {
            animation-delay: 0s;
        }

        .quantum-particle:nth-child(2) {
            animation-delay: -1s;
        }

        .quantum-particle:nth-child(3) {
            animation-delay: -2s;
        }

        .quantum-core {
            position: absolute;
            top: 50%;
            left: 50%;
            width: 60px;
            height: 60px;
            margin: -30px 0 0 -30px;
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
            font-size: 1.5rem;
            animation: coreFloat 3s ease-in-out infinite;
        }

        .loader-text {
            color: #13e8e9;
            margin-bottom: 1rem;
        }

        .loading-title {
            font-family: "Cinzel Decorative", serif;
            font-size: 1.5rem;
            margin-bottom: 1rem;
            background: linear-gradient(45deg, #13e8e9, #ff6b9d, #4ecdc4);
            background-size: 300% 300%;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: gradientShift 3s ease infinite;
        }

        .loading-progress {
            width: 300px;
            height: 4px;
            background: rgba(19, 232, 233, 0.2);
            border-radius: 2px;
            margin: 0 auto 1rem;
            overflow: hidden;
            position: relative;
        }

        .progress-bar {
            height: 100%;
            background: linear-gradient(90deg, #13e8e9, #ff6b9d, #4ecdc4, #13e8e9);
            background-size: 300% 100%;
            border-radius: 2px;
            width: 0%;
            animation: progressFill 3s ease-in-out, gradientMove 2s linear infinite;
        }

        .loading-status {
            font-size: 0.9rem;
            opacity: 0.8;
            animation: statusPulse 2s ease-in-out infinite;
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
            animation: particleFloat 6s linear infinite;
        }

        .particle:nth-child(odd) {
            background: #ff6b9d;
        }

        .particle:nth-child(3n) {
            background: #4ecdc4;
        }

        .particle:nth-child(1) { left: 10%; animation-delay: 0s; }
        .particle:nth-child(2) { left: 20%; animation-delay: -1s; }
        .particle:nth-child(3) { left: 30%; animation-delay: -2s; }
        .particle:nth-child(4) { left: 40%; animation-delay: -3s; }
        .particle:nth-child(5) { left: 50%; animation-delay: -4s; }
        .particle:nth-child(6) { left: 60%; animation-delay: -5s; }
        .particle:nth-child(7) { left: 70%; animation-delay: -1.5s; }
        .particle:nth-child(8) { left: 80%; animation-delay: -2.5s; }

        @keyframes quantumSpin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        @keyframes particleOrbit {
            0% { transform: rotate(0deg) translateX(90px) rotate(0deg); }
            100% { transform: rotate(360deg) translateX(90px) rotate(-360deg); }
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
            0%, 100% { opacity: 0.8; }
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
                width: 150px;
                height: 150px;
            }

            .quantum-ring:nth-child(1) {
                width: 130px;
                height: 130px;
                margin: -65px 0 0 -65px;
            }

            .quantum-ring:nth-child(2) {
                width: 100px;
                height: 100px;
                margin: -50px 0 0 -50px;
            }

            .quantum-ring:nth-child(3) {
                width: 70px;
                height: 70px;
                margin: -35px 0 0 -35px;
            }

            .loading-progress {
                width: 250px;
            }

            .loading-title {
                font-size: 1.2rem;
            }
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const passwordToggle = document.getElementById('passwordToggle');
            
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

        document.getElementById('loginForm').addEventListener('submit', function() {
            const btn = document.getElementById('loginBtn');
            const btnText = btn.querySelector('.btn-text');
            const loading = btn.querySelector('.loading');
            const loader = document.getElementById('futuristicLoader');
            const statusText = loader.querySelector('.loading-status');
            
            // Show futuristic loader
            loader.classList.add('active');
            
            // Disable button
            btn.classList.add('loading');
            btnText.style.display = 'none';
            loading.style.display = 'inline';
            btn.disabled = true;
            
            // Dynamic status text changes
            const statusMessages = [
                'Authenticating credentials...',
                'Establishing quantum connection...',
                'Validating security protocols...',
                'Accessing business matrix...',
                'Synchronizing user data...',
                'Finalizing authentication...'
            ];
            
            let messageIndex = 0;
            const statusInterval = setInterval(() => {
                messageIndex = (messageIndex + 1) % statusMessages.length;
                statusText.textContent = statusMessages[messageIndex];
            }, 800);
            
            // Clear interval after 5 seconds (in case form takes too long)
            setTimeout(() => {
                clearInterval(statusInterval);
            }, 5000);
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
    </script>
</body>
</html>
