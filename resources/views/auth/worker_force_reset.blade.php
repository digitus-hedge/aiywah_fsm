<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
  <title>Set New Password | Matter Mind</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    *{box-sizing:border-box;margin:0;padding:0;}
    body{
      font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;
      min-height:100vh;display:flex;align-items:center;justify-content:center;
      background:linear-gradient(135deg,#9A7B4F 0%,#7A6140 55%,#5c4930 100%);
      padding:20px;position:relative;overflow:hidden;
    }
    body::before{content:'';position:absolute;left:-120px;top:-120px;width:420px;height:420px;border-radius:50%;background:rgba(255,255,255,.05);}
    body::after{content:'';position:absolute;right:-100px;bottom:-140px;width:380px;height:380px;border-radius:50%;background:rgba(255,255,255,.04);}
    .wl-card{position:relative;z-index:1;width:100%;max-width:400px;background:#fff;border-radius:16px;padding:34px 30px 30px;box-shadow:0 20px 60px rgba(0,0,0,.28);}
    .wl-brand{display:flex;flex-direction:column;align-items:center;margin-bottom:26px;text-align:center;}
    .wl-mark{width:60px;height:60px;border-radius:16px;margin-bottom:14px;background:linear-gradient(135deg,#9A7B4F,#C4A882);display:flex;align-items:center;justify-content:center;color:#fff;font-size:1.6rem;box-shadow:0 6px 20px rgba(154,123,79,.35);}
    .wl-title{font-size:1.05rem;font-weight:700;color:#1f2937;}
    .wl-sub{font-size:.78rem;color:#6b7280;margin-top:4px;line-height:1.5;}
    .wl-alert{display:flex;align-items:flex-start;gap:8px;background:rgba(239,68,68,.08);border:1px solid rgba(239,68,68,.2);color:#b91c1c;font-size:.78rem;border-radius:8px;padding:10px 12px;margin-bottom:16px;}
    .wl-group{margin-bottom:15px;}
    .wl-label{display:block;font-size:.75rem;font-weight:600;color:#374151;margin-bottom:6px;}
    .wl-input-wrap{position:relative;}
    .wl-input-wrap>i{position:absolute;left:13px;top:50%;transform:translateY(-50%);color:#9ca3af;font-size:.92rem;pointer-events:none;}
    .wl-input{width:100%;height:46px;padding:0 40px 0 40px;border:1.5px solid #e5e7eb;border-radius:10px;font-size:.9rem;color:#111827;background:#fff;transition:border-color .15s,box-shadow .15s;}
    .wl-input:focus{outline:none;border-color:#9A7B4F;box-shadow:0 0 0 3px rgba(154,123,79,.13);}
    .wl-eye{position:absolute;right:10px;top:50%;transform:translateY(-50%);background:none;border:none;color:#9ca3af;cursor:pointer;padding:6px;font-size:.92rem;}
    .wl-hint{font-size:.72rem;color:#9ca3af;margin-top:6px;line-height:1.5;}
    .wl-btn{width:100%;height:47px;border:none;border-radius:10px;cursor:pointer;background:linear-gradient(135deg,#9A7B4F,#C4A882);color:#fff;font-size:.9rem;font-weight:600;display:flex;align-items:center;justify-content:center;gap:8px;margin-top:6px;transition:opacity .15s,transform .1s;}
    .wl-btn:hover{opacity:.9;}
    .wl-btn:active{transform:scale(.985);}
    .wl-btn:disabled{opacity:.6;cursor:not-allowed;}
    .wl-foot{text-align:center;margin-top:14px;padding-top:16px;border-top:1px solid #f3f4f6;}
    .wl-foot button{background:none;border:none;color:#9ca3af;font-size:.75rem;font-weight:600;cursor:pointer;padding:4px;}
    .wl-foot button:hover{color:#9A7B4F;text-decoration:underline;}
    @media(max-width:400px){.wl-card{padding:26px 20px 24px;}}
  </style>
</head>
<body>
  <div class="wl-card">

    <div class="wl-brand">
      <div class="wl-mark"><i class="bi bi-key"></i></div>
      <div class="wl-title">Set a New Password</div>
      <div class="wl-sub">Required before you can continue.</div>
    </div>

    @if($errors->any())
      <div class="wl-alert">
        <i class="bi bi-exclamation-triangle-fill" style="margin-top:1px;"></i>
        <span>{{ $errors->first() }}</span>
      </div>
    @endif

    <form method="POST" action="{{ route('worker.password.forced.update') }}" id="rsForm">
      @csrf

      <div class="wl-group">
        <label class="wl-label" for="password">New Password</label>
        <div class="wl-input-wrap">
          <i class="bi bi-lock"></i>
          <input class="wl-input" type="password" id="password" name="password"
                 placeholder="••••••••" required autofocus autocomplete="new-password">
          <button type="button" class="wl-eye" onclick="rsToggle('password','rsEye1')" aria-label="Show password">
            <i class="bi bi-eye" id="rsEye1"></i>
          </button>
        </div>
        <div class="wl-hint">At least 8 characters. Use something you haven't used before.</div>
      </div>

      <div class="wl-group">
        <label class="wl-label" for="password_confirmation">Confirm Password</label>
        <div class="wl-input-wrap">
          <i class="bi bi-lock-fill"></i>
          <input class="wl-input" type="password" id="password_confirmation" name="password_confirmation"
                 placeholder="••••••••" required autocomplete="new-password">
          <button type="button" class="wl-eye" onclick="rsToggle('password_confirmation','rsEye2')" aria-label="Show password">
            <i class="bi bi-eye" id="rsEye2"></i>
          </button>
        </div>
      </div>

      <button type="submit" class="wl-btn" id="rsBtn">
        <i class="bi bi-shield-check"></i>Save &amp; Continue
      </button>
    </form>

    <div class="wl-foot">
      <form method="POST" action="{{ route('worker.logout') }}">
        @csrf
        <button type="submit">Cancel and sign out</button>
      </form>
    </div>
  </div>

  <script>
    function rsToggle(fieldId, iconId){
      var p = document.getElementById(fieldId);
      var i = document.getElementById(iconId);
      var show = p.type === 'password';
      p.type = show ? 'text' : 'password';
      i.className = show ? 'bi bi-eye-slash' : 'bi bi-eye';
    }
    document.getElementById('rsForm').addEventListener('submit', function(){
      var b = document.getElementById('rsBtn');
      b.disabled = true;
      b.innerHTML = '<i class="bi bi-arrow-repeat"></i>Saving…';
    });
  </script>
</body>
</html>