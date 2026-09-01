<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex,nofollow">
<meta name="theme-color" content="#0c1a20">
<title>{{ $project->project_name }} — Service record</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<link rel="icon" type="image/png" href="{{ asset('assets/images/favicon.png') }}">
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
    --bad:#a83c32;  --bad-bg:#fbeae8;  --bad-line:#e8b3ad;
    --info:#2b5f8a; --info-bg:#e8f0f7; --info-line:#b3cbe1;
    --live:#0f5c73; --live-bg:#e4eff3; --live-line:#a9c8d3;
    --neutral:#5a6b75; --neutral-bg:#eef2f4; --neutral-line:#d3dee3;

    --mono: ui-monospace, "SF Mono", "JetBrains Mono", Menlo, Consolas, monospace;
    --shadow-sm: 0 1px 2px rgba(19,32,41,.05);
    --shadow-md: 0 4px 16px rgba(19,32,41,.08);
    --shadow-lg: 0 14px 38px rgba(19,32,41,.13);
  }
  *{box-sizing:border-box}
  html{scroll-behavior:smooth}
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
    content:"";position:absolute;inset:auto 0 0 0;height:1px;
    background:linear-gradient(90deg, transparent, rgb(239, 247, 250) 30%, rgba(169,124,44,.3) 70%, transparent);
  }
  .mast-row{display:flex;flex-wrap:wrap;gap:22px;justify-content:space-between;align-items:flex-end}
  .eyebrow{
    font-family:var(--mono);font-size:11px;letter-spacing:.17em;text-transform:uppercase;
    color: #fdebd7;display:flex;align-items:center;gap:10px;flex-wrap:wrap
  }
  .eyebrow .dot{width:3px;height:3px;border-radius:50%;background: #fdebd7}
  .masthead h1{margin:12px 0 7px;font-size:clamp(26px,4.6vw,37px);font-weight:650;letter-spacing:-.028em;line-height:1.12}
  .masthead .site{color: #fdebd7;font-size:14.5px;margin:0;display:flex;gap:8px;align-items:flex-start}
  .masthead .site i{margin-top:3px;flex:none;opacity:.8}

  .badge-warranty{
    display:inline-flex;align-items:center;gap:9px;border-radius:999px;padding:9px 16px;
    font-size:13px;font-weight:600;letter-spacing:-.01em;
    background:rgba(255,255,255,.06);border:1px solid rgba(169,200,211,.24);color:#cfdde3
  }
  .badge-warranty.in{background:rgba(169,124,44,.24);border-color:rgba(224,197,141,.5);color:#f2e0bb}
  .badge-warranty.out{background:rgba(168,60,50,.2);border-color:rgba(232,179,173,.4);color:#f6d5d1}

  /* ── Stat strip ── */
  .stats{
    position:relative;z-index:1;
    display:grid;grid-template-columns:repeat(4,1fr);gap:1px;background:var(--line);
    border:1px solid var(--line);border-radius:16px;overflow:hidden;
    margin:-30px 0 26px;box-shadow:var(--shadow-lg)
  }
  .stat{background:var(--card);padding:18px 20px}
  .stat b{display:block;font-size:25px;font-weight:650;letter-spacing:-.035em;line-height:1.15}
  .stat span{
    display:block;margin-top:4px;font-size:10.5px;letter-spacing:.1em;text-transform:uppercase;
    color:var(--muted);font-family:var(--mono)
  }

  /* ── Buttons ── */
  .actions{display:flex;gap:10px;flex-wrap:wrap;margin-bottom:28px}
  .btn{
    display:inline-flex;align-items:center;gap:8px;border-radius:11px;padding:11px 17px;
    font-size:14px;font-weight:600;text-decoration:none;border:1px solid transparent;cursor:pointer;
    transition:border-color .16s, background .16s, color .16s, transform .16s
  }
  .btn:active{transform:translateY(1px)}
  .btn-primary{background:var(--accent);color:#fff;box-shadow:var(--shadow-sm)}
  .btn-primary:hover{background:var(--accent-deep)}
  .btn-ghost{background:var(--card);border-color:var(--line);color:var(--ink);box-shadow:var(--shadow-sm)}
  .btn-ghost:hover{border-color:var(--accent);color:var(--accent)}

  /* ── Service visit cards ── */
  .list-label{
    font-family:var(--mono);font-size:10.5px;letter-spacing:.15em;text-transform:uppercase;
    color:var(--muted);margin:0 0 13px;display:flex;align-items:center;gap:13px
  }
  .list-label::after{content:"";flex:1;height:1px;background:var(--line)}

  .card-x{
    position:relative;background:var(--card);border:1px solid var(--line);border-radius:16px;
    margin-bottom:12px;box-shadow:var(--shadow-sm);transition:box-shadow .18s, border-color .18s
  }
  .card-x:hover{box-shadow:var(--shadow-md)}
  .card-x.open{border-color:var(--accent-line);box-shadow:var(--shadow-md)}
  .card-x.open::before{
    content:"";position:absolute;left:0;top:18px;bottom:18px;width:3px;
    border-radius:0 3px 3px 0;background:var(--accent)
  }
  .card-head{
    display:flex;gap:14px;align-items:center;padding:18px 21px;cursor:pointer;
    border-radius:16px;transition:background .16s
  }
  .card-head:hover{background:#fafcfd}
  .ref{font-family:var(--mono);font-size:11.5px;color:var(--muted);letter-spacing:.03em}
  .card-title{font-weight:600;margin:4px 0 0;font-size:16.5px;letter-spacing:-.015em}
  .chip{
    display:inline-flex;align-items:center;gap:6px;padding:5px 12px;border-radius:999px;
    font-size:12.5px;font-weight:600;white-space:nowrap;letter-spacing:-.005em;
    background:var(--neutral-bg);color:var(--neutral);border:1px solid var(--neutral-line)
  }
  .chip.ok{background:var(--ok-bg);color:var(--ok);border-color:var(--ok-line)}
  .chip.warn{background:var(--warn-bg);color:var(--warn);border-color:var(--warn-line)}
  .chip.bad{background:var(--bad-bg);color:var(--bad);border-color:var(--bad-line)}
  .chip.info{background:var(--info-bg);color:var(--info);border-color:var(--info-line)}
  .chip.live{background:var(--live-bg);color:var(--live);border-color:var(--live-line)}
  .chip.wait{background:var(--neutral-bg);color:var(--neutral);border-color:var(--neutral-line)}
  .caret{margin-left:auto;color:var(--muted);transition:transform .2s ease;flex:none}
  .open .caret{transform:rotate(180deg)}
  .card-body{display:none;padding:0 21px 24px;border-top:1px solid var(--line-soft)}
  .open .card-body{display:block}

  .facts{display:grid;grid-template-columns:repeat(auto-fit,minmax(165px,1fr));gap:17px;padding:19px 0 4px}
  .fact span{
    display:block;font-size:10.5px;letter-spacing:.09em;text-transform:uppercase;
    color:var(--muted);font-family:var(--mono);margin-bottom:3px
  }
  .fact b{font-weight:550;letter-spacing:-.012em}

  .section-label{
    font-family:var(--mono);font-size:10.5px;letter-spacing:.15em;text-transform:uppercase;
    color:var(--muted);margin:26px 0 14px;display:flex;align-items:center;gap:13px
  }
  .section-label::after{content:"";flex:1;height:1px;background:var(--line)}

  /* ── Documents ── */
  .docs{display:grid;gap:8px}
  .doc{
    display:flex;align-items:center;gap:14px;padding:13px 16px;
    border:1px solid var(--line);border-radius:12px;text-decoration:none;color:inherit;
    background:#fafcfd;transition:border-color .16s, background .16s, transform .16s
  }
  .doc:hover{border-color:var(--accent);background:#fff;transform:translateX(2px)}
  .doc-ico{
    flex:none;width:38px;height:38px;border-radius:10px;display:grid;place-items:center;
    background:var(--accent-soft);color:var(--accent);font-size:16px
  }
  .doc-txt{display:flex;flex-direction:column;line-height:1.35;min-width:0}
  .doc-txt b{font-weight:600;font-size:14px;letter-spacing:-.012em}
  .doc-txt small{
    color:var(--muted);font-size:12px;font-family:var(--mono);
    overflow:hidden;text-overflow:ellipsis;white-space:nowrap
  }
  .doc .bi-download{margin-left:auto;color:var(--muted);flex:none}
  .doc:hover .bi-download{color:var(--accent)}

  /* ── Timeline ── */
  .steps{list-style:none;margin:0;padding:0 0 0 25px;border-left:2px solid var(--line)}
  .steps li{position:relative;padding:0 0 19px 19px}
  .steps li:last-child{padding-bottom:0}
  .steps li::before{
    content:"";position:absolute;left:-32px;top:5px;width:12px;height:12px;border-radius:50%;
    background:#fff;border:2px solid var(--line)
  }
  .steps li.done::before{background:var(--accent);border-color:var(--accent)}
  .steps li.now::before{background:#fff;border-color:var(--accent);box-shadow:0 0 0 4px rgba(15,92,115,.15)}
  .steps li b{font-weight:550;font-size:14.5px;letter-spacing:-.012em}
  .steps li.pending b{color:var(--muted);font-weight:450}
  .steps small{display:block;color:var(--muted);font-size:12.5px}
  .steps time{display:block;font-family:var(--mono);font-size:11.5px;color:var(--ink-soft);margin-top:1px}

  /* ── Signature element: before / after comparison ── */
  .visit{margin-bottom:22px}
  .visit:last-child{margin-bottom:0}
  .visit-cap{font-size:12.5px;color:var(--muted);margin-bottom:10px;display:flex;gap:10px;align-items:baseline;flex-wrap:wrap}
  .visit-cap strong{color:var(--ink);font-weight:600;font-size:13.5px}
  .compare{
    position:relative;border-radius:13px;overflow:hidden;background:var(--ink-deep);
    aspect-ratio:16/10;user-select:none;touch-action:none;cursor:ew-resize;border:1px solid var(--line)
  }
  .compare img{position:absolute;inset:0;width:100%;height:100%;object-fit:cover;display:block}
  .compare .after-lyr{clip-path:inset(0 0 0 50%)}
  .handle{
    position:absolute;top:0;bottom:0;left:50%;width:2px;background:#fff;
    box-shadow:0 0 0 1px rgba(0,0,0,.24);pointer-events:none
  }
  .handle::after{
    content:"\F282";font-family:"bootstrap-icons";position:absolute;top:50%;left:50%;
    transform:translate(-50%,-50%) rotate(90deg);width:38px;height:38px;border-radius:50%;
    background:#fff;color:var(--ink);display:grid;place-items:center;font-size:15px;
    box-shadow:0 3px 14px rgba(0,0,0,.34)
  }
  .tag{
    position:absolute;top:12px;padding:4px 10px;border-radius:7px;font-family:var(--mono);
    font-size:10px;letter-spacing:.12em;text-transform:uppercase;
    background:rgba(12,26,32,.76);color:#dce8ec;pointer-events:none
  }
  .tag.l{left:12px} .tag.r{right:12px}
  .single{border-radius:13px;overflow:hidden;border:1px solid var(--line);background:var(--ink-deep)}
  .single img{width:100%;display:block}

  /* ── Work notes ── */
  .work-note{background:#fafcfd;border:1px solid var(--line);border-radius:12px;padding:16px 18px;margin-bottom:10px}
  .work-note:last-child{margin-bottom:0}
  .work-note p{margin:0 0 7px}
  .work-note p:last-child{margin:0}
  .work-note .parts{color:var(--muted);font-size:13px}

  .rated{display:flex;align-items:center;gap:8px;margin-top:19px;color:var(--ink-soft);font-size:13.5px}
  .rated .bi-star-fill{color:var(--brass)}

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
    .stats{grid-template-columns:repeat(2,1fr);margin-top:-24px}
    .stat{padding:15px 16px}
    .stat b{font-size:21px}
    .masthead{padding:36px 0 46px}
    .card-head{flex-wrap:wrap;align-items:flex-start}
    .caret{order:-1}
  }
  @media (prefers-reduced-motion:reduce){*{transition:none!important;animation:none!important}html{scroll-behavior:auto}}
  :focus-visible{outline:2px solid var(--accent);outline-offset:3px;border-radius:8px}
</style>
</head>
<body>

<header class="masthead">
  <div class="wrap mast-row">
    <div>
      <div class="eyebrow">
        <span>{{ $project->project_code }}</span>
        <span class="dot"></span>
        <span>{{ $client->company_name }}</span>
      </div>
      <h1>{{ $project->project_name }}</h1>
      <p class="site">
        <i class="bi bi-geo-alt"></i>
        <span>{{ $project->site_name }}@if($project->site_address), {{ $project->site_address }}@endif</span>
      </p>
    </div>
    <div>
      @if($inWarranty)
        <span class="badge-warranty in"><i class="bi bi-shield-check"></i> Under warranty · {{ $daysLeft }} days left</span>
      @elseif($warrantyEnd)
        <span class="badge-warranty out"><i class="bi bi-shield-exclamation"></i> Warranty ended {{ $warrantyEnd->format('d M Y') }}</span>
      @else
        <span class="badge-warranty"><i class="bi bi-shield"></i> Warranty not recorded</span>
      @endif
    </div>
  </div>
</header>

<main class="wrap">

  <section class="stats" aria-label="Summary">
    <div class="stat"><b>{{ $cards->count() }}</b><span>Service visits</span></div>
    <div class="stat"><b>{{ $openCount }}</b><span>In progress</span></div>
    <div class="stat"><b>{{ $closedCount }}</b><span>Completed</span></div>
    <div class="stat"><b>{{ $photoCount }}</b><span>Photo sets</span></div>
  </section>

  <div class="actions">
    <a class="btn btn-ghost" href="{{ route('portal.client', ['code' => $client->unique_code]) }}">
      <i class="bi bi-arrow-left"></i> All projects
    </a>

    @if($project->project_engineer && $project->engineer_contact)
      <a class="btn btn-primary" href="tel:{{ $project->engineer_country }}{{ $project->engineer_contact }}">
        <i class="bi bi-telephone"></i> Call {{ $project->project_engineer }}
      </a>
    @endif
  </div>

  @if($cards->isNotEmpty())
    <p class="list-label">Service history</p>
  @endif

  @forelse($cards as $c)
    <article class="card-x" data-card id="sr-{{ $c['id'] }}">
      <div class="card-head" role="button" tabindex="0" data-toggle
          aria-expanded="false" aria-controls="body-{{ $c['id'] }}">
        <div style="flex:1;min-width:190px">
          <p class="card-title">{{ $c['ref'] }}</p>
          <div class="ref">{{ $c['category'] }} · logged {{ $c['logged'] }}</div>
        </div>
        <span class="chip {{ $c['tone'] }}"><i class="bi {{ $c['icon'] }}"></i> {{ $c['label'] }}</span>
        <i class="bi bi-chevron-down caret" aria-hidden="true"></i>
      </div>

      <div class="card-body" id="body-{{ $c['id'] }}">

        <div class="facts">
          <div class="fact"><span>Reported issue</span><b>{{ $c['issue'] ?: '—' }}</b></div>
          <div class="fact"><span>Priority</span><b>{{ $c['priority'] ?: '—' }}</b></div>
          <div class="fact"><span>Technician</span><b>{{ $c['technician'] ?: 'Being assigned' }}</b></div>
          <div class="fact"><span>Cost</span><b>{{ $c['coverage'] }}</b></div>
        </div>

        @if(!empty($c['docs']))
          <div class="section-label">Documents</div>
          <div class="docs">
            @foreach($c['docs'] as $d)
              <a class="doc" href="{{ $d['url'] }}" target="_blank" rel="noopener" download>
                <span class="doc-ico"><i class="bi {{ $d['icon'] }}"></i></span>
                <span class="doc-txt">
                  <b>{{ $d['kind'] }}</b>
                  <small>{{ $d['label'] }}</small>
                </span>
                <i class="bi bi-download" aria-hidden="true"></i>
              </a>
            @endforeach
          </div>
        @endif

        @if($c['photos'])
          <div class="section-label">Before and after</div>
          @foreach($c['photos'] as $set)
            <div class="visit">
              <div class="visit-cap">
                <strong>{{ $set['visit'] }}</strong>
                @if($set['when'])<span>{{ $set['when'] }}</span>@endif
              </div>

              @if($set['before'] && $set['after'])
                <div class="compare" data-compare
                     role="slider" tabindex="0"
                     aria-label="Drag to compare before and after"
                     aria-valuemin="0" aria-valuemax="100" aria-valuenow="50">
                  <img src="{{ $set['before'] }}" alt="Before the work" loading="lazy">
                  <img class="after-lyr" src="{{ $set['after'] }}" alt="After the work" loading="lazy">
                  <span class="tag l">Before</span>
                  <span class="tag r">After</span>
                  <div class="handle"></div>
                </div>
              @else
                <div class="single">
                  <img src="{{ $set['before'] ?: $set['after'] }}" loading="lazy"
                       alt="{{ $set['before'] ? 'Before the work' : 'After the work' }}">
                </div>
              @endif
            </div>
          @endforeach
        @endif

        @php $hasNotes = collect($c['punches'])->contains(fn($p) => $p['work'] || $p['summary']); @endphp
        @if($hasNotes)
          <div class="section-label">What the technician did</div>
          @foreach($c['punches'] as $pn)
            @if($pn['work'] || $pn['summary'])
              <div class="work-note">
                <div class="ref" style="margin-bottom:8px">
                  {{ $pn['technician'] }}@if($pn['duration']) · {{ $pn['duration'] }} on site @endif
                </div>
                @if($pn['work'])<p><strong>Work done.</strong> {{ $pn['work'] }}</p>@endif
                @if($pn['summary'])<p>{{ $pn['summary'] }}</p>@endif
                @if($pn['items'])
                  <p class="parts">
                    Parts used: {{ collect($pn['items'])->map(fn($i) => $i['name'].' ×'.$i['qty'])->join(', ') }}
                  </p>
                @endif
              </div>
            @endif
          @endforeach
        @endif

        <div class="section-label">Progress</div>
        @php $nowIndex = collect($c['timeline'])->search(fn($s) => ! $s['done']); @endphp
        <ul class="steps">
          @foreach($c['timeline'] as $i => $s)
            <li class="{{ $s['done'] ? 'done' : ($i === $nowIndex ? 'pending now' : 'pending') }}">
              <b>{{ $s['label'] }}</b>
              @if($s['time'])<time>{{ $s['time'] }}</time>@endif
              @if($s['note'])<small>{{ $s['note'] }}</small>@endif
            </li>
          @endforeach
        </ul>

        @if($c['can_rate'])
          <a class="btn btn-ghost" style="margin-top:19px" href="{{ $c['feedback_url'] }}">
            <i class="bi bi-star"></i> Rate this visit
          </a>
        @elseif($c['rated'])
          <p class="rated">
            <i class="bi bi-star-fill"></i> You rated this visit {{ $c['score'] }}/5. Thank you.
          </p>
        @endif

      </div>
    </article>
  @empty
    <div class="empty">
      <i class="bi bi-clipboard-check"></i>
      <strong>No service visits yet</strong>
      <p>Anything raised for this site will appear here with photos and progress.</p>
    </div>
  @endforelse

  <footer>
    {{ $client->company_name }} · {{ $project->project_code }} · Updated {{ now()->format('d M Y, h:i A') }}
  </footer>
</main>

<script>
(function () {
  'use strict';

  /* ── Expand / collapse a service visit ── */
  document.querySelectorAll('[data-toggle]').forEach(function (head) {
    var card = head.closest('[data-card]');

    function toggle() {
      var open = card.classList.toggle('open');
      head.setAttribute('aria-expanded', open ? 'true' : 'false');
    }

    head.addEventListener('click', toggle);
    head.addEventListener('keydown', function (e) {
      if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); toggle(); }
    });
  });

  /* ── Before / after slider ── */
  document.querySelectorAll('[data-compare]').forEach(function (box) {
    var after    = box.querySelector('.after-lyr'),
        handle   = box.querySelector('.handle'),
        dragging = false,
        pct      = 50;

    function paint() {
      after.style.clipPath = 'inset(0 0 0 ' + pct + '%)';
      handle.style.left = pct + '%';
      box.setAttribute('aria-valuenow', Math.round(pct));
    }

    function setFromX(clientX) {
      var r = box.getBoundingClientRect();
      pct = Math.min(100, Math.max(0, ((clientX - r.left) / r.width) * 100));
      paint();
    }

    box.addEventListener('pointerdown', function (e) {
      dragging = true;
      box.setPointerCapture(e.pointerId);
      setFromX(e.clientX);
    });
    box.addEventListener('pointermove',   function (e) { if (dragging) setFromX(e.clientX); });
    box.addEventListener('pointerup',     function () { dragging = false; });
    box.addEventListener('pointercancel', function () { dragging = false; });

    box.addEventListener('keydown', function (e) {
      var step = e.shiftKey ? 10 : 4;
      if (e.key === 'ArrowLeft')       { pct = Math.max(0, pct - step); }
      else if (e.key === 'ArrowRight') { pct = Math.min(100, pct + step); }
      else if (e.key === 'Home')       { pct = 0; }
      else if (e.key === 'End')        { pct = 100; }
      else { return; }
      e.preventDefault();
      paint();
    });
  });

  /* ── Open the requested visit, else the newest one ── */
  var focus  = @json($focusSr),
      target = focus ? document.getElementById('sr-' + focus) : null,
      card   = target || document.querySelector('[data-card]');

  if (card) {
    card.classList.add('open');
    var head = card.querySelector('[data-toggle]');
    if (head) head.setAttribute('aria-expanded', 'true');
    if (target) target.scrollIntoView({ behavior: 'smooth', block: 'start' });
  }
})();
</script>
</body>
</html>