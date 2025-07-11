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
            background: #fff !important;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            color: #020258 !important;
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
            background: linear-gradient(135deg, #020258 0%, #13e8e9 100%);
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
            background: rgba(2, 2, 88, 0.8);
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

        .form-group {
            margin-bottom: 1.5rem;
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
            border: 2px solid #13e8e9 !important;
            border-radius: 12px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: #f8f9fa !important;
            color: #020258 !important;
        }

        .form-control:focus {
            outline: none;
            border-color: #020258 !important;
            background: rgba(2, 2, 88, 0.8);
            box-shadow: 0 0 0 3px rgba(19, 232, 233, 0.1) !important;
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
            padding: 0.75rem;
            background: linear-gradient(135deg, #13e8e9 0%, #020258 100%);
            border: 2px solid #13e8e9;
            border-radius: 12px;
            color: #020258;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-bottom: 1rem;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(19, 232, 233, 0.3);
            background: linear-gradient(135deg, #020258 0%, #13e8e9 100%);
            color: #13e8e9;
        }

        .btn-login:active {
            transform: translateY(0);
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
                max-width: 400px;
            }

            .login-image {
                display: none;
            }

            .login-form {
                padding: 2rem;
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
                        <i class="fas fa-envelope input-icon"></i>
                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" 
                               name="email" value="{{ old('email') }}" required autocomplete="email" autofocus
                               placeholder="Enter your email address">
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
                        <i class="fas fa-lock input-icon"></i>
                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" 
                               name="password" required autocomplete="current-password"
                               placeholder="Enter your password">
                        <button type="button" class="btn btn-link position-absolute" style="right: 10px; top: 50%; transform: translateY(-50%); z-index: 3; color: #13e8e9;" onclick="togglePassword()">
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
    </script>
</body>
</html>
