<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Shopybook</title>
    <meta name="description" content="Sign in to your Shopybook account to manage your business.">
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
            max-width: 920px;
            display: flex;
            min-height: 560px;
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
        }

        .auth-aside-content .icon-wrap i {
            font-size: 2rem;
            color: #ff511a;
        }

        .auth-aside-content ul {
            list-style: none;
            padding: 0;
            margin-top: 1.75rem;
            text-align: left;
            display: inline-block;
        }

        .auth-aside-content ul li {
            padding: 6px 0;
            font-size: 0.92rem;
            opacity: 0.95;
        }

        .auth-aside-content ul li i {
            color: #ff511a;
            margin-right: 10px;
        }

        {{-- Right panel (form) --}}
        .auth-form {
            flex: 1;
            padding: 3rem 2.75rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: #fff;
        }

        .form-header {
            text-align: center;
            margin-bottom: 2rem;
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

        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-label {
            display: block;
            font-size: 0.85rem;
            font-weight: 600;
            color: #7b2e2e;
            margin-bottom: 0.4rem;
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
            padding: 0.75rem 2.5rem 0.75rem 2.6rem;
            border: 1.5px solid #e0ded9;
            border-radius: 8px;
            font-size: 0.95rem;
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
            font-size: 0.8rem;
            margin-top: 4px;
        }

        .form-check {
            display: flex;
            align-items: center;
            gap: 8px;
            margin: 1rem 0 1.5rem;
        }

        .form-check-input {
            width: 16px;
            height: 16px;
            border-color: #e0ded9;
            cursor: pointer;
        }

        .form-check-input:checked {
            background-color: #ff511a;
            border-color: #ff511a;
        }

        .form-check-label {
            font-size: 0.88rem;
            color: #555;
            cursor: pointer;
        }

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

        .forgot-password {
            text-align: center;
            margin-top: 1.25rem;
            font-size: 0.88rem;
        }

        .forgot-password a {
            color: #ff511a;
            text-decoration: none;
            font-weight: 500;
        }

        .forgot-password a:hover { text-decoration: underline; }

        .auth-divider {
            display: flex;
            align-items: center;
            text-align: center;
            margin: 1.5rem 0;
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
            margin-bottom: 0.75rem;
        }

        .loader-status {
            font-size: 0.92rem;
            opacity: 0.9;
        }

        @media (max-width: 768px) {
            body { padding: 0; }
            .auth-container {
                flex-direction: column;
                max-width: 480px;
                min-height: auto;
            }
            .auth-aside { padding: 2rem 1.5rem 2.5rem; }
            .auth-aside-content h2 { font-size: 1.7rem; }
            .auth-aside-content ul { display: none; }
            .auth-form { padding: 2rem 1.5rem; }
            .form-header h1 { font-size: 1.5rem; }
        }
    </style>
</head>
<body>
    <div class="auth-container">
        {{-- Left panel --}}
        <div class="auth-aside">
            <div class="auth-aside-content">
                <h2>Welcome Back!</h2>
                <p>Sign in to your Shopybook account and manage your business with ease.</p>
                <div class="icon-wrap">
                    <i class="fas fa-store"></i>
                </div>
                <ul>
                    <li><i class="fas fa-check-circle"></i> Manage inventory &amp; sales</li>
                    <li><i class="fas fa-check-circle"></i> Track customers &amp; staff</li>
                    <li><i class="fas fa-check-circle"></i> Real-time reports &amp; analytics</li>
                </ul>
            </div>
        </div>

        {{-- Right panel (form) --}}
        <div class="auth-form">
            <div class="form-header">
                <h1>Sign In</h1>
                <p>Enter your credentials to access your account</p>
            </div>

            <form method="POST" action="{{ route('login') }}" id="loginForm">
                @csrf

                <div class="form-group">
                    <label for="email" class="form-label">Email Address</label>
                    <div class="input-wrap">
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
                    <div class="input-wrap">
                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
                               name="password" required autocomplete="current-password"
                               placeholder="Enter your password">
                        <i class="fas fa-lock input-icon"></i>
                        <button type="button" class="password-toggle-btn" onclick="togglePassword()">
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

                <button type="submit" class="btn-auth" id="loginBtn">
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

                <div class="auth-divider">
                    <span>New to Shopybook?</span>
                </div>

                <div class="auth-alt-link">
                    <a href="{{ route('register') }}">
                        <i class="fas fa-user-plus me-1"></i>
                        Create a new account
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Loader --}}
    <div id="futuristicLoader" class="loader-modal">
        <div class="loader-box">
            <div class="loader-spinner"></div>
            <h3 class="loader-title">Signing you in</h3>
            <p class="loader-status">Authenticating credentials...</p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const passwordToggle = document.getElementById('passwordToggle');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                passwordToggle.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                passwordToggle.classList.replace('fa-eye-slash', 'fa-eye');
            }
        }

        document.getElementById('loginForm').addEventListener('submit', function () {
            const btn = document.getElementById('loginBtn');
            const loader = document.getElementById('futuristicLoader');
            const statusText = loader.querySelector('.loader-status');

            loader.classList.add('active');
            btn.classList.add('loading');
            btn.disabled = true;

            const statusMessages = [
                'Authenticating credentials...',
                'Verifying account...',
                'Loading your dashboard...',
                'Almost there...'
            ];
            let i = 0;
            const interval = setInterval(() => {
                i = (i + 1) % statusMessages.length;
                statusText.textContent = statusMessages[i];
            }, 800);
            setTimeout(() => clearInterval(interval), 6000);
        });
    </script>
</body>
</html>
