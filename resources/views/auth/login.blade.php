<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sign In | Smart SR Portal</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/feather-icons@4.29.1/dist/feather.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary: #727cf5;
            --primary-hover: #5a64f0;
            --success: #0acf97;
            --danger: #fa5c7c;
            --dark: #313a46;
            --light: #eef2f7;
            --body-bg: #f5f7fb;
            --text-muted: #98a6ad;
            --border-color: #eef2f7;
        }

        * { box-sizing: border-box; }

        body {
            font-family: 'Roboto', -apple-system, BlinkMacSystemFont, sans-serif;
            background: linear-gradient(135deg, #f5f7fb 0%, #eef2f7 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            color: var(--dark);
        }

        /* Decorative blobs in background */
        body::before, body::after {
            content: '';
            position: fixed;
            border-radius: 50%;
            opacity: 0.08;
            pointer-events: none;
            z-index: 0;
        }
        body::before {
            width: 480px; height: 480px;
            background: var(--primary);
            top: -120px; left: -120px;
        }
        body::after {
            width: 400px; height: 400px;
            background: var(--success);
            bottom: -100px; right: -100px;
        }

        .login-wrapper {
            width: 100%;
            max-width: 420px;
            position: relative;
            z-index: 1;
        }

        .login-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 24px rgba(49, 58, 70, 0.08);
            padding: 2.5rem 2rem;
            border: 1px solid var(--border-color);
        }

        .brand-row {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 1.75rem;
            justify-content: center;
        }

        .brand-mark {
            width: 44px;
            height: 44px;
            background: linear-gradient(135deg, var(--primary), #4f5dc4);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 16px;
        }

        .brand-text {
            font-size: 1.125rem;
            font-weight: 700;
            color: var(--dark);
            line-height: 1.15;
        }

        .brand-text small {
            display: block;
            font-size: 11px;
            color: var(--text-muted);
            font-weight: 400;
            margin-top: 2px;
        }

        .login-title {
            font-size: 1.25rem;
            font-weight: 500;
            margin-bottom: 0.25rem;
            color: var(--dark);
        }

        .login-subtitle {
            font-size: 0.85rem;
            color: var(--text-muted);
            margin-bottom: 1.75rem;
        }

        .form-label {
            font-size: 0.8rem;
            font-weight: 500;
            color: var(--dark);
            margin-bottom: 0.4rem;
        }

        .input-wrap {
            position: relative;
            margin-bottom: 1.1rem;
        }

        .form-control {
            border: 1px solid var(--border-color);
            background: #fafbfd;
            padding: 0.65rem 0.85rem 0.65rem 2.4rem;
            font-size: 0.875rem;
            border-radius: 6px;
            color: var(--dark);
        }

        .form-control:focus {
            background: white;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(114, 124, 245, 0.15);
        }

        .input-wrap .input-icon {
            position: absolute;
            top: 50%;
            left: 0.85rem;
            transform: translateY(-50%);
            color: var(--text-muted);
            width: 16px;
            height: 16px;
            pointer-events: none;
        }

        .input-wrap.has-label .input-icon {
            top: calc(50% + 12px);
        }

        .toggle-password {
            position: absolute;
            top: 50%;
            right: 0.85rem;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--text-muted);
            cursor: pointer;
            padding: 0;
            display: flex;
            align-items: center;
        }

        .input-wrap.has-label .toggle-password {
            top: calc(50% + 12px);
        }

        .toggle-password:hover { color: var(--dark); }
        .toggle-password svg { width: 16px; height: 16px; }

        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.25rem;
            font-size: 0.8rem;
        }

        .form-check-input:checked {
            background-color: var(--primary);
            border-color: var(--primary);
        }

        .form-check-input:focus {
            box-shadow: 0 0 0 3px rgba(114, 124, 245, 0.15);
            border-color: var(--primary);
        }

        .forgot-link {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
        }
        .forgot-link:hover { text-decoration: underline; }

        .btn-login {
            width: 100%;
            background: var(--primary);
            border: none;
            color: white;
            padding: 0.7rem;
            border-radius: 6px;
            font-size: 0.9rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-login:hover { background: var(--primary-hover); }
        .btn-login:disabled { opacity: 0.6; cursor: not-allowed; }

        .alert {
            padding: 0.7rem 0.9rem;
            border-radius: 6px;
            font-size: 0.8rem;
            margin-bottom: 1rem;
            display: flex;
            align-items: flex-start;
            gap: 8px;
        }

        .alert-danger {
            background: rgba(250, 92, 124, 0.1);
            color: #c8455f;
            border: 1px solid rgba(250, 92, 124, 0.2);
        }

        .alert-success {
            background: rgba(10, 207, 151, 0.1);
            color: #07a578;
            border: 1px solid rgba(10, 207, 151, 0.2);
        }

        .alert svg { width: 16px; height: 16px; flex-shrink: 0; margin-top: 1px; }

        .invalid-feedback {
            display: block;
            color: var(--danger);
            font-size: 0.75rem;
            margin-top: 0.3rem;
        }

        .is-invalid { border-color: var(--danger) !important; }
        .is-invalid:focus { box-shadow: 0 0 0 3px rgba(250, 92, 124, 0.15) !important; }

        .footer-text {
            text-align: center;
            margin-top: 1.5rem;
            font-size: 0.75rem;
            color: var(--text-muted);
        }

        .demo-creds {
            background: var(--light);
            border-radius: 6px;
            padding: 0.7rem 0.9rem;
            font-size: 0.75rem;
            color: var(--dark);
            margin-bottom: 1.25rem;
            line-height: 1.5;
        }

        .demo-creds strong { color: var(--primary); }

        @media (max-width: 575.98px) {
            .login-card { padding: 1.75rem 1.25rem; }
            .brand-text { font-size: 1rem; }
        }
    </style>
</head>
<body>

    <div class="login-wrapper">
        <div class="login-card">

            <div class="brand-row">
                <div class="brand-mark">SR</div>
                <div class="brand-text">
                    Smart SR Portal
                    <small>DIGIT-US IT SOLUTIONS</small>
                </div>
            </div>

            <h1 class="login-title">Welcome back</h1>
            <p class="login-subtitle">Sign in to your admin account to continue</p>

            {{-- Flash messages --}}
            @if (session('success'))
                <div class="alert alert-success">
                    <i data-feather="check-circle"></i>
                    <div>{{ session('success') }}</div>
                </div>
            @endif

            @if ($errors->any() && !$errors->has('email') && !$errors->has('password'))
                <div class="alert alert-danger">
                    <i data-feather="alert-circle"></i>
                    <div>{{ $errors->first() }}</div>
                </div>
            @endif

            {{-- Demo credentials (remove in production) --}}
            <div class="demo-creds">
                <strong>Demo:</strong> admin@smartsr.test &nbsp;/&nbsp; password
            </div>

            <form method="POST" action="{{ route('login.attempt') }}" novalidate>
                @csrf

                {{-- Email --}}
                <div class="input-wrap has-label">
                    <label for="email" class="form-label">Email address</label>
                    <i data-feather="mail" class="input-icon"></i>
                    <input
                        type="email"
                        class="form-control @error('email') is-invalid @enderror"
                        id="email"
                        name="email"
                        value="{{ old('email', 'admin@smartsr.test') }}"
                        placeholder="you@company.com"
                        autocomplete="email"
                        required
                        autofocus>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Password --}}
                <div class="input-wrap has-label">
                    <label for="password" class="form-label">Password</label>
                    <i data-feather="lock" class="input-icon"></i>
                    <input
                        type="password"
                        class="form-control @error('password') is-invalid @enderror"
                        id="password"
                        name="password"
                        placeholder="Enter your password"
                        autocomplete="current-password"
                        required>
                    <button type="button" class="toggle-password" id="togglePwd" aria-label="Show password">
                        <i data-feather="eye" id="toggleIcon"></i>
                    </button>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Remember + Forgot --}}
                <div class="form-options">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="remember" name="remember">
                        <label class="form-check-label" for="remember" style="font-size: 0.8rem;">
                            Remember me
                        </label>
                    </div>
                    <a href="#" class="forgot-link">Forgot password?</a>
                </div>

                <button type="submit" class="btn-login" id="loginBtn">
                    <span id="btnText">Sign in</span>
                    <i data-feather="arrow-right" style="width:16px; height:16px;"></i>
                </button>
            </form>

            <p class="footer-text">
                &copy; {{ date('Y') }} DIGIT-US IT Solutions. All rights reserved.
            </p>

        </div>
    </div>

    <script>
        feather.replace();

        // Toggle password visibility
        const pwdField = document.getElementById('password');
        const toggleBtn = document.getElementById('togglePwd');

        toggleBtn.addEventListener('click', () => {
            const isHidden = pwdField.type === 'password';
            pwdField.type = isHidden ? 'text' : 'password';
            toggleBtn.innerHTML = isHidden
                ? '<i data-feather="eye-off"></i>'
                : '<i data-feather="eye"></i>';
            feather.replace();
        });

        // Form submit — show loading state
        const form = document.querySelector('form');
        const btn = document.getElementById('loginBtn');
        const btnText = document.getElementById('btnText');

        form.addEventListener('submit', () => {
            btn.disabled = true;
            btnText.textContent = 'Signing in...';
        });
    </script>
</body>
</html>
