<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sign In · MatterMind Portal</title>

    {{-- Favicon --}}
    <link rel="icon" type="image/png" href="{{ asset('favicon-96x96.png') }}" sizes="96x96">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <meta name="apple-mobile-web-app-title" content="MatterMind">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">

    {{-- Self-hosted brand fonts: Cormorant Garamond (display) + SF Pro Display (body) --}}
    <link rel="stylesheet" href="{{ asset('assets/fonts/fonts.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/feather-icons@4.29.1/dist/feather.min.js"></script>

    <style>
        :root{
            /* ── MatterMind brand palette (unchanged) ── */
            --brand-700:#82693f;
            --brand-600:#8c7147;
            --brand-500:#9a8053;
            --brand-300:#c4a882;
            --ink:#393837;
            --ink-soft:#5f5d5a;
            --muted:#9b988f;
            --line:#e9e4dc;
            --field-bg:#faf8f5;
            --card:#ffffff;
            --danger:#c8455f;
            --danger-bg:rgba(200,69,95,.08);
            --success:#0f8f6a;
            --success-bg:rgba(15,143,106,.08);
            --page:#f2ede6;

            --font-head:'Cormorant Garamond',Georgia,'Times New Roman',serif;
            --font-body:'SF Pro Display',-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;
        }

        *{box-sizing:border-box;margin:0;padding:0;}

        body{
            font-family:var(--font-body);
            min-height:100vh;
            display:grid;
            grid-template-columns:46fr 54fr;
            background:var(--page);
            color:var(--ink);
            -webkit-font-smoothing:antialiased;
            text-rendering:optimizeLegibility;
        }

        @media (prefers-reduced-motion: reduce){
            *{animation-duration:.01ms !important; transition-duration:.01ms !important;}
        }

        /* ═══════════════════════════════════════════════════════════
           LEFT - brand panel, original gold gradient
           ═══════════════════════════════════════════════════════════ */
        .brand-panel{
            position:relative;
            overflow:hidden;
            background:linear-gradient(155deg, var(--brand-700) 0%, var(--brand-500) 62%, #a5885a 100%);
            color:#fff;
            display:flex;
            flex-direction:column;
            justify-content:space-between;
            padding:56px 54px;
        }

        /* fine grain, keeps the gold field from banding */
        .brand-panel::before{
            content:'';
            position:absolute;inset:0;z-index:0;
            background-image:radial-gradient(rgba(255,255,255,.5) .5px, transparent .5px);
            background-size:3px 3px;
            opacity:.05;
        }

        /* ── signature: concentric rings + chevron diamond, echoing
               the mark's own linework, bled off the corner ── */
        .ring-field{
            position:absolute;
            top:-190px; right:-230px;
            width:640px; height:640px;
            pointer-events:none;
            opacity:0;
            animation:ringIn 1.1s .15s cubic-bezier(.16,1,.3,1) forwards;
        }
        @keyframes ringIn{
            from{opacity:0; transform:scale(.92);}
            to{opacity:1; transform:scale(1);}
        }
        .ring{
            position:absolute; top:50%; left:50%;
            border:1px solid rgba(255,255,255,.16);
            border-radius:50%;
            transform:translate(-50%,-50%);
        }
        .ring.r1{ width:640px; height:640px; border-color:rgba(255,255,255,.10); }
        .ring.r2{ width:498px; height:498px; border-color:rgba(255,255,255,.14); }
        .ring.r3{ width:360px; height:360px; border-color:rgba(255,255,255,.20); }
        .ring.r4{ width:226px; height:226px; border-color:rgba(255,255,255,.30); }
        .ring-diamond{
            position:absolute; top:50%; left:50%;
            width:300px; height:300px;
            border:1px solid rgba(255,255,255,.16);
            transform:translate(-50%,-50%) rotate(45deg);
        }

        /* logo lockup is the only element up here now - no side text,
           so it just needs to sit clean and scale down gracefully */
        .bp-top{ position:relative; z-index:1; margin-bottom:44px; }
        .bp-mark{ width:200px; }
        .bp-mark img{ width:100%; height:auto; display:block; }

        .bp-mid{ position:relative; z-index:1; max-width:410px; }
        .bp-eyebrow{
            font-size:.68rem; font-weight:600; letter-spacing:.24em; text-transform:uppercase;
            color:rgba(255,255,255,.78); margin-bottom:20px;
        }
        .bp-head{
            font-family:var(--font-head);
            font-weight:600;
            font-size:2.9rem;
            line-height:1.16;
            letter-spacing:.003em;
            margin-bottom:18px;
            text-wrap:balance;
        }
        .bp-head em{
            font-style:italic; font-weight:500;
            color:#f3e4cb;
        }
        .bp-rule{
            width:44px; height:2px;
            background:linear-gradient(90deg, rgba(255,255,255,.85), rgba(255,255,255,.1));
            border-radius:2px;
            margin-bottom:18px;
        }
        .bp-sub{
            font-size:.9rem; line-height:1.75;
            color:rgba(255,255,255,.82);
            max-width:340px;
        }

        .bp-foot{
            position:relative; z-index:1;
            display:flex; align-items:baseline; justify-content:space-between;
            font-size:.68rem; letter-spacing:.04em;
            color:rgba(255,255,255,.55);
            border-top:1px solid rgba(255,255,255,.16);
            padding-top:18px;
        }

        /* ═══════════════════════════════════════════════════════════
           RIGHT - sign-in form, original card white
           ═══════════════════════════════════════════════════════════ */
        .form-panel{
            position:relative;
            display:flex;
            align-items:center;
            justify-content:center;
            padding:48px 40px;
            background:var(--card);
        }

        /* hallmark watermark - small, quiet, bottom corner */
        .fp-hallmark{
            position:absolute;
            width:150px;
            right:44px; bottom:36px;
            opacity:.06;
            pointer-events:none;
        }
        .fp-hallmark img{ width:100%; height:auto; display:block; }

        .form-inner{
            width:100%; max-width:352px;
            position:relative; z-index:1;
            opacity:0;
            animation:riseIn .7s .3s cubic-bezier(.16,1,.3,1) forwards;
        }
        @keyframes riseIn{
            from{opacity:0; transform:translateY(10px);}
            to{opacity:1; transform:translateY(0);}
        }

        .fp-kicker{
            font-size:.7rem; font-weight:600; letter-spacing:.18em; text-transform:uppercase;
            color:var(--brand-500); margin-bottom:12px;
        }
        .fp-title{
            font-family:var(--font-head);
            font-size:2.5rem; font-weight:600; letter-spacing:.01em;
            line-height:1.1;
            margin-bottom:8px; color:var(--ink);
        }
        .fp-lead{ font-size:.86rem; color:var(--ink-soft); margin-bottom:38px; }

        .field{ margin-bottom:28px; }
        .field-label{
            display:block;
            font-size:.7rem; font-weight:600; letter-spacing:.12em; text-transform:uppercase;
            color:var(--ink-soft); margin-bottom:10px;
        }

        .field-box{ position:relative; }

        /* underline-style inputs - now with real breathing room instead
           of text sitting flush against the hairline and each edge */
        .field-input{
            width:100%;
            font-family:var(--font-body); font-size:.95rem;
            color:var(--ink);
            background:var(--field-bg);
            border:none;
            border-bottom:1.5px solid var(--line);
            border-radius:6px 6px 0 0;
            padding:.9rem 1rem;
            transition:border-color .2s, background .2s;
        }
        .field-input::placeholder{ color:var(--muted); }
        .field-input:focus{
            outline:none;
            background:#fff;
            border-bottom-color:var(--brand-500);
        }
        .field-box::after{
            content:'';
            position:absolute; left:0; right:0; bottom:-1.5px;
            height:1.5px; background:var(--brand-500);
            transform:scaleX(0); transform-origin:left;
            transition:transform .28s cubic-bezier(.16,1,.3,1);
            pointer-events:none;
        }
        .field-box:focus-within::after{ transform:scaleX(1); }

        .field-input.pw{ padding-right:2.7rem; }
        .pw-toggle{
            position:absolute; right:.6rem; top:50%; transform:translateY(-50%);
            background:none; border:none; cursor:pointer; color:var(--muted);
            display:flex; align-items:center; padding:4px;
            transition:color .18s;
        }
        .pw-toggle:hover{ color:var(--brand-500); }
        .pw-toggle svg{ width:15px; height:15px; }

        .field-input.is-invalid{ border-bottom-color:var(--danger); }
        .field-error{ color:var(--danger); font-size:.73rem; margin-top:8px; }

        .btn-signin{
            width:100%;
            font-family:var(--font-body);
            font-size:.87rem; font-weight:600; letter-spacing:.01em;
            color:#fff;
            background:linear-gradient(135deg, var(--brand-600), var(--brand-500));
            border:none; border-radius:9px;
            padding:.85rem;
            margin-top:12px;
            cursor:pointer;
            display:flex; align-items:center; justify-content:center; gap:9px;
            transition:box-shadow .22s, transform .12s, opacity .2s;
        }
        .btn-signin:hover{ box-shadow:0 10px 26px rgba(130,105,63,.32); }
        .btn-signin:active{ transform:translateY(1px); }
        .btn-signin:disabled{ opacity:.6; cursor:not-allowed; box-shadow:none; }
        .btn-signin svg{ width:16px; height:16px; }

        .alert{
            padding:.7rem .85rem; border-radius:8px; font-size:.79rem;
            margin-bottom:26px; display:flex; align-items:flex-start; gap:9px; line-height:1.45;
        }
        .alert svg{ width:16px; height:16px; flex-shrink:0; margin-top:1px; }
        .alert-danger{ background:var(--danger-bg); color:var(--danger); border:1px solid rgba(200,69,95,.18); }
        .alert-success{ background:var(--success-bg); color:var(--success); border:1px solid rgba(15,143,106,.18); }

        .fp-foot{
            text-align:center; margin-top:32px; padding-top:20px;
            border-top:1px solid var(--line);
            font-size:.72rem; color:var(--muted);
        }

        /* ═══ Responsive ═══ */
        @media (max-width:900px){
            body{ grid-template-columns:1fr; }
            .brand-panel{ padding:40px 32px 28px; min-height:auto; }
            .ring-field{ width:420px; height:420px; top:-140px; right:-160px; }
            .ring.r1{ width:420px; height:420px; }
            .ring.r2{ width:328px; height:328px; }
            .ring.r3{ width:238px; height:238px; }
            .ring.r4{ width:150px; height:150px; }
            .ring-diamond{ width:198px; height:198px; }
            .bp-top{ margin-bottom:28px; }
            .bp-mark{ width:150px; }
            .bp-head{ font-size:2.1rem; }
            .bp-sub{ display:none; }
            .bp-foot{ display:none; }
            .fp-hallmark{ width:110px; right:24px; bottom:22px; }
        }
        @media (max-width:520px){
            .brand-panel{ padding:34px 24px 24px; }
            .form-panel{ padding:34px 24px; }
            .bp-top{ margin-bottom:22px; }
            .bp-mark{ width:120px; }
            .bp-head{ font-size:1.85rem; }
            .fp-title{ font-size:2.1rem; }
        }
    </style>
</head>
<body>

    {{-- ═══ LEFT - brand geometry ═══ --}}
    <aside class="brand-panel">

        <span class="ring-field" aria-hidden="true">
            <span class="ring r1"></span>
            <span class="ring r2"></span>
            <span class="ring r3"></span>
            <span class="ring r4"></span>
            <span class="ring-diamond"></span>
        </span>

        <div class="bp-top">
            <div class="bp-mark">
                <img src="{{ asset('assets/images/mmwhite.png') }}" alt="MatterMind">
            </div>
        </div>

        <div class="bp-mid">
            <div class="bp-eyebrow">Operations Portal</div>
            <h1 class="bp-head"><em>Perfection</em> is a state of mind.</h1>
            <div class="bp-rule"></div>
            <p class="bp-sub">Provision users, route service requests, and keep every operation moving - from one considered workspace.</p>
        </div>

        <div class="bp-foot">
            <span>&copy; {{ date('Y') }} Aiywah FSM</span>
            <span>All rights reserved</span>
        </div>
    </aside>

    {{-- ═══ RIGHT - sign in ═══ --}}
    <main class="form-panel">
        <span class="fp-hallmark" aria-hidden="true">
            <img src="{{ asset('assets/images/mattermind-mark-gold.png') }}" alt="">
        </span>

        <div class="form-inner">

            <div class="fp-kicker">Welcome back</div>
            <h2 class="fp-title">Sign in</h2>
            <p class="fp-lead">Enter your credentials to access the portal.</p>

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

            <form method="POST" action="{{ route('login.attempt') }}" novalidate>
                @csrf

                {{-- Email --}}
                <div class="field">
                    <label for="email" class="field-label">Email address</label>
                    <div class="field-box">
                        <input
                            type="email"
                            class="field-input @error('email') is-invalid @enderror"
                            id="email" name="email"
                            value="{{ old('email') }}"
                            placeholder="you@company.com"
                            autocomplete="email" required autofocus>
                    </div>
                    @error('email')
                        <div class="field-error">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Password --}}
                <div class="field">
                    <label for="password" class="field-label">Password</label>
                    <div class="field-box">
                        <input
                            type="password"
                            class="field-input pw @error('password') is-invalid @enderror"
                            id="password" name="password"
                            placeholder="Enter your password"
                            autocomplete="current-password" required>
                        <button type="button" class="pw-toggle" id="togglePwd" aria-label="Show password">
                            <i data-feather="eye" id="toggleIcon"></i>
                        </button>
                    </div>
                    @error('password')
                        <div class="field-error">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn-signin" id="loginBtn">
                    <span id="btnText">Sign in</span>
                    <i data-feather="arrow-right"></i>
                </button>
            </form>

            <div class="fp-foot">Protected access · authorised personnel only</div>

        </div>
    </main>

    <script>
        feather.replace();

        const pwdField  = document.getElementById('password');
        const toggleBtn = document.getElementById('togglePwd');
        toggleBtn.addEventListener('click', () => {
            const hidden = pwdField.type === 'password';
            pwdField.type = hidden ? 'text' : 'password';
            toggleBtn.innerHTML = hidden
                ? '<i data-feather="eye-off"></i>'
                : '<i data-feather="eye"></i>';
            feather.replace();
        });

        const form     = document.querySelector('form');
        const btn      = document.getElementById('loginBtn');
        const btnText  = document.getElementById('btnText');
        form.addEventListener('submit', () => {
            btn.disabled = true;
            btnText.textContent = 'Signing in…';
        });
    </script>
</body>
</html>