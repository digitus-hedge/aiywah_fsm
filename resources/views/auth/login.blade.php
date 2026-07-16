<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sign In · MatterMind Portal</title>

    {{-- Self-hosted brand fonts: Cormorant Garamond (display) + SF Pro Display (body) --}}
    <link rel="stylesheet" href="{{ asset('assets/fonts/fonts.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/feather-icons@4.29.1/dist/feather.min.js"></script>

    <style>
        :root{
            /* ── MatterMind brand palette ── */
            --brand-700:#82693f;   /* darker gold (gradient partner) */
            --brand-600:#8c7147;   /* mid gold */
            --brand-500:#9a8053;   /* PRIMARY gold */
            --brand-300:#c4a882;   /* light gold accent */
            --ink:#393837;         /* TERTIARY charcoal — headings & body ink */
            --ink-soft:#5f5d5a;
            --muted:#9b988f;
            --line:#e9e4dc;
            --field-bg:#faf8f5;
            --card:#ffffff;        /* SECONDARY white */
            --danger:#c8455f;
            --danger-bg:rgba(200,69,95,.08);
            --success:#0f8f6a;
            --success-bg:rgba(15,143,106,.08);
            --page:#f2ede6;

            /* ── Type stacks (mirrors theme.css) ── */
            --font-head:'Cormorant Garamond',Georgia,'Times New Roman',serif;
            --font-body:'SF Pro Display',-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;
        }

        *{box-sizing:border-box;margin:0;padding:0;}

        body{
            font-family:var(--font-body);
            min-height:100vh;
            display:grid;
            grid-template-columns:1.05fr .95fr;
            background:var(--page);
            color:var(--ink);
            -webkit-font-smoothing:antialiased;
            text-rendering:optimizeLegibility;
        }

        /* ═══ LEFT: brand panel ═══
           3-row grid: mark / centred story / footer.
           The story row takes all free space and centres its own
           content, so it stays optically middle regardless of
           viewport height while mark + footer stay pinned. */
        .brand-panel{
            position:relative;
            overflow:hidden;
            isolation:isolate;
            background:
                radial-gradient(120% 130% at 12% 6%, #b1936180 0%, transparent 58%),
                radial-gradient(140% 140% at 92% 92%, #5e4c2e99 0%, transparent 62%),
                linear-gradient(155deg, var(--brand-700) 0%, var(--brand-500) 62%, #a5885a 100%);
            color:#fff;
            padding:52px 64px;
            display:grid;
            grid-template-rows:auto 1fr auto;
            gap:32px;
        }

        /* Fine grain — keeps the large gold field from banding */
        .brand-panel::before{
            content:'';
            position:absolute;inset:0;z-index:-1;
            background-image:radial-gradient(rgba(255,255,255,.5) .5px, transparent .5px);
            background-size:3px 3px;
            opacity:.05;
        }

        /* Concentric rings — recentred, softer falloff */
        .bp-rings{
            position:absolute;
            right:-260px; bottom:-260px;
            width:720px; height:720px;
            border-radius:50%;
            border:1px solid rgba(255,255,255,.14);
            box-shadow:
                0 0 0 1px rgba(255,255,255,.05) inset,
                0 0 0 70px rgba(255,255,255,.035),
                0 0 0 150px rgba(255,255,255,.025),
                0 0 0 240px rgba(255,255,255,.015);
            z-index:-1;
            pointer-events:none;
        }
        .bp-glow{
            position:absolute;
            left:-160px; top:-160px;
            width:420px; height:420px;
            border-radius:50%;
            background:radial-gradient(circle, rgba(255,255,255,.10), transparent 70%);
            z-index:-1;
            pointer-events:none;
        }

        .bp-top{
            display:flex;
            align-items:center;
            gap:14px;
        }
        .bp-mark{
            width:52px;height:52px;flex-shrink:0;
            display:flex;align-items:center;justify-content:center;
        }
        .bp-mark img{
            width:52px;
            height:52px;
            object-fit:contain;
            display:block;
            filter:brightness(0) invert(1);
            opacity:.96;
        }

        /* Centred story block */
        .bp-mid{
            align-self:center;
            max-width:430px;
        }
        .bp-eyebrow{
            font-family:var(--font-body);
            font-size:.68rem;font-weight:600;letter-spacing:.24em;text-transform:uppercase;
            opacity:.72;margin-bottom:22px;
            display:flex;align-items:center;gap:12px;
        }
        .bp-eyebrow::before{
            content:'';
            width:30px;height:1px;
            background:linear-gradient(90deg, rgba(255,255,255,.7), rgba(255,255,255,.15));
        }
        .bp-head{
            font-family:var(--font-head);
            font-weight:600;
            font-size:3.5rem;
            line-height:1.06;
            letter-spacing:.005em;
            margin-bottom:20px;
            text-wrap:balance;
        }
        .bp-head em{
            font-style:italic;
            font-weight:500;
            color:#f3e4cb;
        }
        .bp-rule{
            width:52px;height:2px;
            background:linear-gradient(90deg, rgba(255,255,255,.85), rgba(255,255,255,.1));
            border-radius:2px;
            margin-bottom:20px;
        }
        .bp-sub{
            font-size:.93rem;
            line-height:1.68;
            opacity:.84;
            font-weight:400;
            max-width:390px;
        }

        .bp-foot{
            font-size:.71rem;
            opacity:.55;
            letter-spacing:.03em;
        }

        /* ═══ RIGHT: form ═══ */
        .form-panel{
            display:flex;
            align-items:center;
            justify-content:center;
            padding:48px 40px;
            background:var(--card);
        }
        .form-inner{width:100%;max-width:376px;}

        .fp-kicker{
            font-size:.7rem;font-weight:600;letter-spacing:.18em;text-transform:uppercase;
            color:var(--brand-500);margin-bottom:12px;
        }
        .fp-title{
            font-family:var(--font-head);
            font-size:2.6rem;font-weight:600;letter-spacing:.01em;
            line-height:1.1;
            margin-bottom:8px;color:var(--ink);
        }
        .fp-lead{font-size:.86rem;color:var(--ink-soft);margin-bottom:30px;}

        .field{margin-bottom:18px;}
        .field-label{
            display:block;font-size:.76rem;font-weight:500;
            color:var(--ink-soft);margin-bottom:7px;letter-spacing:.01em;
        }
        .field-box{position:relative;}
        .field-box .fi{
            position:absolute;top:50%;left:14px;transform:translateY(-50%);
            width:17px;height:17px;color:var(--muted);pointer-events:none;
            transition:color .18s;
        }
        .field-input{
            width:100%;
            font-family:var(--font-body);font-size:.9rem;
            color:var(--ink);
            background:var(--field-bg);
            border:1.5px solid var(--line);
            border-radius:9px;
            padding:.72rem .9rem .72rem 2.6rem;
            transition:border-color .18s, box-shadow .18s, background .18s;
        }
        .field-input::placeholder{color:var(--muted);}
        .field-input:focus{
            outline:none;
            background:#fff;
            border-color:var(--brand-500);
            box-shadow:0 0 0 3.5px rgba(154,128,83,.14);
        }
        .field-box:focus-within .fi{color:var(--brand-500);}

        .field-input.pw{padding-right:2.7rem;}
        .pw-toggle{
            position:absolute;top:50%;right:12px;transform:translateY(-50%);
            background:none;border:none;cursor:pointer;color:var(--muted);
            display:flex;align-items:center;padding:4px;border-radius:5px;
            transition:color .18s;
        }
        .pw-toggle:hover{color:var(--brand-500);}
        .pw-toggle svg{width:16px;height:16px;}

        .field-input.is-invalid{border-color:var(--danger);background:#fff;}
        .field-input.is-invalid:focus{box-shadow:0 0 0 3.5px rgba(200,69,95,.13);}
        .field-error{color:var(--danger);font-size:.74rem;margin-top:6px;}

        .btn-signin{
            width:100%;
            font-family:var(--font-body);font-size:.9rem;font-weight:600;letter-spacing:.01em;
            color:#fff;
            background:linear-gradient(135deg, var(--brand-600), var(--brand-500));
            border:none;border-radius:9px;
            padding:.8rem;
            margin-top:6px;
            cursor:pointer;
            display:flex;align-items:center;justify-content:center;gap:9px;
            transition:box-shadow .2s, transform .1s, opacity .2s;
        }
        .btn-signin:hover{box-shadow:0 6px 20px rgba(130,105,63,.32);}
        .btn-signin:active{transform:translateY(1px);}
        .btn-signin:disabled{opacity:.6;cursor:not-allowed;box-shadow:none;}
        .btn-signin svg{width:16px;height:16px;}

        .alert{
            padding:.7rem .85rem;border-radius:8px;font-size:.79rem;
            margin-bottom:20px;display:flex;align-items:flex-start;gap:9px;line-height:1.45;
        }
        .alert svg{width:16px;height:16px;flex-shrink:0;margin-top:1px;}
        .alert-danger{background:var(--danger-bg);color:var(--danger);border:1px solid rgba(200,69,95,.18);}
        .alert-success{background:var(--success-bg);color:var(--success);border:1px solid rgba(15,143,106,.18);}

        .fp-foot{
            text-align:center;margin-top:26px;padding-top:20px;
            border-top:1px solid var(--line);
            font-size:.72rem;color:var(--muted);
        }

        /* ═══ Responsive ═══ */
        @media (max-width:900px){
            body{grid-template-columns:1fr;}
            .brand-panel{
                padding:40px 40px 34px;
                grid-template-rows:auto auto auto;
                gap:26px;
            }
            .bp-rings{width:460px;height:460px;right:-200px;bottom:-200px;}
            .bp-mid{max-width:none;align-self:start;}
            .bp-head{font-size:2.5rem;}
            .bp-foot{display:none;}
        }
        @media (max-width:520px){
            .brand-panel{padding:30px 26px 26px;}
            .form-panel{padding:34px 26px;}
            .bp-head{font-size:2.1rem;}
            .fp-title{font-size:2.1rem;}
        }
    </style>
</head>
<body>

    {{-- ═══ LEFT: brand story ═══ --}}
    <aside class="brand-panel">
        <span class="bp-rings" aria-hidden="true"></span>
        <span class="bp-glow" aria-hidden="true"></span>

        <div class="bp-top">
            <div class="bp-mark">
                <img src="{{ asset('assets/images/logo-main.webp') }}" alt="MatterMind">
            </div>
        </div>

        <div class="bp-mid">
            <div class="bp-eyebrow">MATTER MIND</div>
            <h1 class="bp-head">Service that <em>matters</em>. Always.</h1>
            <div class="bp-rule"></div>
            <p class="bp-sub">Provision users, route service requests, and keep every operation moving — all from one considered workspace.</p>
        </div>

        <div class="bp-foot">
            &copy; {{ date('Y') }} Matter Mind. All rights reserved.
        </div>
    </aside>

    {{-- ═══ RIGHT: sign in ═══ --}}
    <main class="form-panel">
        <div class="form-inner">

            <div class="fp-kicker">Welcome back</div>
            <h2 class="fp-title">Sign in</h2>
            <p class="fp-lead">Enter your credentials to access the portal.</p>

            {{-- Flash / non-field errors --}}
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
                        <i data-feather="mail" class="fi"></i>
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
                        <i data-feather="lock" class="fi"></i>
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

            <div class="fp-foot">
                Protected access · authorised personnel only
            </div>

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

        const form    = document.querySelector('form');
        const btn      = document.getElementById('loginBtn');
        const btnText  = document.getElementById('btnText');
        form.addEventListener('submit', () => {
            btn.disabled = true;
            btnText.textContent = 'Signing in…';
        });
    </script>
</body>
</html>