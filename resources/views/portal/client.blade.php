<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex,nofollow">
<meta name="theme-color" content="#0c1a20">
<title>{{ $client->company_name }} — Service portal</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<style>
  :root{
    --ink:#132029; --ink-deep:#0c1a20; --ink-soft:#41535e; --muted:#78909c;
    --paper:#f3f6f7; --card:#ffffff; --line:#dde5e9; --line-soft:#e9eff2;

    /* primary — petrol */
    --accent:#9a8053; --accent-deep:#9a8053; --accent-soft:#9a8053; --accent-line:#9a8053;
    /* secondary — brass, used only for warranty + ratings */
    --brass:#a97c2c; --brass-bg:#fbf2e0; --brass-line:#e0c58d;

    --ok:#12704a;   --ok-bg:#e4f2eb;   --ok-line:#a8d4c1;
    --warn:#a2611a; --warn-bg:#fcf0e0; --warn-line:#e6c391;
    --live:#0f5c73; --live-bg:#e4eff3; --live-line:#a9c8d3;
    --neutral:#5a6b75; --neutral-bg:#eef2f4; --neutral-line:#d3dee3;

    --mono: ui-monospace, "SF Mono", "JetBrains Mono", Menlo, Consolas, monospace;
    --shadow-sm: 0 1px 2px rgba(19,32,41,.05);
    --shadow-md: 0 4px 16px rgba(19,32,41,.08);
    --shadow-lg: 0 14px 38px rgba(19,32,41,.13);
  }
  *{box-sizing:border-box}
  body{
    margin:0;background:var(--paper);color:var(--ink);
    font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",sans-serif;
    font-size:15px;line-height:1.55;-webkit-font-smoothing:antialiased;text-rendering:optimizeLegibility;
  }
  .wrap{max-width:940px;margin:0 auto;padding:0 22px}

  /* ── Masthead ──────────────────────────────────────────────
     z-index 0 keeps it behind the stat strip, which overlaps it. */
 .masthead{
  position: relative;
  z-index: 0;
  overflow: hidden;
  color: #fff;
  padding: 44px 0 56px;
  background: linear-gradient(
    135deg,
    #7d6640 0%,
    #8b7249 25%,
    #9a8053 50%,
    #b19363 75%,
    #c4a777 100%
  );
}

.masthead::after{
  content: "";
  position: absolute;
  inset: auto 0 0 0;
  height: 1px;
  background: linear-gradient(
    90deg,
    transparent,
    rgba(255,255,255,.25) 30%,
    rgba(255,255,255,.15) 70%,
    transparent
  );
}

.eyebrow{
  font-family: var(--mono);
  font-size: 11px;
  letter-spacing: .17em;
  text-transform: uppercase;
  color: #f0e6d2;
}

.masthead h1{
  margin: 12px 0 7px;
  font-size: clamp(26px,4.6vw,37px);
  font-weight: 650;
  letter-spacing: -.028em;
  line-height: 1.12;
}

.masthead p{
  margin: 0;
  color: #f5eee1;
  font-size: 14.5px;
}

  /* ── Stat strip ── */
  .stats{
    position:relative;z-index:1;
    display:grid;grid-template-columns:repeat(3,1fr);gap:1px;background:var(--line);
    border:1px solid var(--line);border-radius:16px;overflow:hidden;
    margin:-30px 0 28px;box-shadow:var(--shadow-lg)
  }
  .stat{background:var(--card);padding:18px 20px}
  .stat b{display:block;font-size:25px;font-weight:650;letter-spacing:-.035em;line-height:1.15}
  .stat span{
    display:block;margin-top:4px;font-size:10.5px;letter-spacing:.1em;text-transform:uppercase;
    color:var(--muted);font-family:var(--mono)
  }

  .list-label{
    font-family:var(--mono);font-size:10.5px;letter-spacing:.15em;text-transform:uppercase;
    color:var(--muted);margin:0 0 13px;display:flex;align-items:center;gap:13px
  }
  .list-label::after{content:"";flex:1;height:1px;background:var(--line)}

  /* ── Project cards ── */
  .proj{
    position:relative;display:block;background:var(--card);
    border:1px solid var(--line);border-radius:16px;
    padding:20px 22px;margin-bottom:12px;text-decoration:none;color:inherit;
    box-shadow:var(--shadow-sm);
    transition:border-color .18s, box-shadow .18s, transform .18s
  }
  .proj::before{
    content:"";position:absolute;left:0;top:20px;bottom:20px;width:3px;
    border-radius:0 3px 3px 0;background:var(--accent);
    opacity:0;transition:opacity .18s
  }
  .proj:hover{border-color:var(--accent-line);box-shadow:var(--shadow-md);transform:translateY(-2px)}
  .proj:hover::before{opacity:1}
  .proj:hover .go{color:var(--accent);transform:translateX(3px)}
  .proj:active{transform:translateY(0)}

  .row{display:flex;gap:16px;align-items:flex-start}
  .code{font-family:var(--mono);font-size:11.5px;color:var(--muted);letter-spacing:.03em}
  .proj h2{margin:5px 0 5px;font-size:18px;font-weight:600;letter-spacing:-.018em;line-height:1.25}
  .site{color:var(--ink-soft);font-size:13.5px;margin:0;display:flex;gap:7px;align-items:flex-start}
  .site i{margin-top:3px;flex:none;color:var(--muted)}
  .go{margin-left:auto;color:#b6c6cd;align-self:center;flex:none;font-size:18px;transition:color .18s, transform .18s}

  .meta{display:flex;gap:7px;flex-wrap:wrap;margin-top:15px}
  .pill{
    display:inline-flex;align-items:center;gap:6px;
    font-size:12px;font-weight:600;padding:5px 11px;border-radius:999px;letter-spacing:-.005em;
    background:var(--neutral-bg);color:var(--neutral);border:1px solid var(--neutral-line)
  }
  .pill.ok{background:var(--ok-bg);color:var(--ok);border-color:var(--ok-line)}
  .pill.warn{background:var(--warn-bg);color:var(--warn);border-color:var(--warn-line)}
  .pill.live{background:var(--live-bg);color:var(--live);border-color:var(--live-line)}
  .pill.brass{background:var(--brass-bg);color:var(--brass);border-color:var(--brass-line)}

  /* ── Empty state ── */
  .empty{background:var(--card);border:1px dashed var(--accent-line);border-radius:16px;padding:60px 24px;text-align:center}
  .empty i{font-size:28px;color:var(--muted)}
  .empty strong{display:block;margin-top:13px;font-size:16px}
  .empty p{color:var(--muted);margin:5px 0 0;font-size:14px}

  footer{
    margin-top:30px;padding:26px 0 52px;border-top:1px solid var(--line);
    color:var(--muted);font-size:11.5px;text-align:center;font-family:var(--mono);letter-spacing:.03em
  }

  @media (max-width:700px){
    .stats{grid-template-columns:1fr;margin-top:-24px}
    .stat{display:flex;align-items:baseline;gap:12px;padding:14px 18px}
    .stat b{font-size:20px}
    .stat span{margin-top:0}
    .go{display:none}
    .masthead{padding:36px 0 46px}
  }
  @media (prefers-reduced-motion:reduce){*{transition:none!important}}
  :focus-visible{outline:2px solid var(--accent);outline-offset:3px;border-radius:10px}
</style>
</head>
<body>

<header class="masthead">
  <div class="wrap">
    <div class="eyebrow">{{ $client->unique_code }}</div>
    <h1>{{ $client->company_name }}</h1>
    <p>Service portal · {{ $rows->count() }} {{ Str::plural('project', $rows->count()) }}</p>
  </div>
</header>

<main class="wrap">

  <section class="stats" aria-label="Summary">
    <div class="stat"><b>{{ $rows->count() }}</b><span>Projects</span></div>
    <div class="stat"><b>{{ $openTotal }}</b><span>Open requests</span></div>
    <div class="stat"><b>{{ $coveredCount }}</b><span>Under warranty</span></div>
  </section>

  @if($rows->isNotEmpty())
    <p class="list-label">Your sites</p>
  @endif

  @forelse($rows as $p)
    <a class="proj" href="{{ $p['url'] }}">
      <div class="row">
        <div style="flex:1;min-width:0">
          <div class="code">{{ $p['code'] }}</div>
          <h2>{{ $p['name'] }}</h2>
          <p class="site">
            <i class="bi bi-geo-alt"></i>
            <span>{{ $p['site'] ?: 'Site not recorded' }}</span>
          </p>
        </div>
        <i class="bi bi-chevron-right go" aria-hidden="true"></i>
      </div>

      <div class="meta">
        @if($p['inWarranty'])
          <span class="pill brass"><i class="bi bi-shield-check"></i> Under warranty</span>
        @elseif($p['warrantyEnd'])
          <span class="pill"><i class="bi bi-shield"></i> Warranty ended {{ $p['warrantyEnd']->format('M Y') }}</span>
        @endif

        @if($p['open'])
          <span class="pill live"><i class="bi bi-arrow-repeat"></i> {{ $p['open'] }} in progress</span>
        @endif

        <span class="pill">
          <i class="bi bi-clipboard-check"></i>
          {{ $p['total'] }} {{ Str::plural('service visit', $p['total']) }}
        </span>

        @if($p['handover'])
          <span class="pill"><i class="bi bi-calendar-check"></i> Handed over {{ $p['handover'] }}</span>
        @endif
      </div>
    </a>
  @empty
    <div class="empty">
      <i class="bi bi-folder2-open"></i>
      <strong>No projects yet</strong>
      <p>Projects registered for your account will appear here.</p>
    </div>
  @endforelse

  <footer>
    {{ $client->company_name }} · Updated {{ now()->format('d M Y, h:i A') }}
  </footer>
</main>
</body>
</html>