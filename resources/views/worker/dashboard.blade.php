@extends('worker.layouts.console')

@section('title', 'My Dashboard')
@section('heading', 'My Dashboard')

@push('head')
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
@endpush

@php
  /**
   * Everything that leaves this page goes to the pipeline. The pipeline reads
   * ?filter= (which status tab), ?job= (which card to expand) and
   * #terminal / #history / #profile (which page to land on).
   */
  $pipelineUrl = $routes['pipeline'] ?? route('worker.pipeline');
  $go = function (array $params = [], string $hash = '') use ($pipelineUrl) {
      return $pipelineUrl
          . ($params ? '?' . http_build_query($params) : '')
          . ($hash ? '#' . $hash : '');
  };
@endphp

@push('styles')
<style>
/* ══════════════════════════════════════════════════════ JOB CARDS ═══ */
.job-card{background:var(--card);border:1px solid var(--border);border-radius:14px;
  box-shadow:var(--shadow);overflow:hidden;margin-bottom:12px;position:relative;}
.job-card-accent{position:absolute;left:0;top:0;bottom:0;width:4px;}
.job-card-body{padding:15px 15px 15px 19px;}
.job-card-top{display:flex;align-items:flex-start;justify-content:space-between;gap:10px;margin-bottom:8px;}
.job-sr-id{font-size:1.05rem;font-weight:700;
  color:var(--gold);line-height:1;}
.job-client{font-size:.9rem;font-weight:600;color:var(--text);margin-bottom:3px;}
.job-site{font-size:.77rem;color:var(--muted);display:flex;align-items:center;gap:4px;margin-bottom:10px;}
.job-issue{font-size:.78rem;color:var(--muted);line-height:1.5;margin-bottom:10px;overflow-wrap:anywhere;}
.job-meta-row{display:flex;align-items:center;gap:10px;flex-wrap:wrap;}
.job-meta-item{display:flex;align-items:center;gap:4px;font-size:.72rem;color:var(--muted);}
.job-meta-item i{font-size:.72rem;color:var(--gold);}

.active-job-card{background:linear-gradient(135deg,#1a2c1a,#1f3d22);border:1px solid rgba(21,128,61,.3);
  border-radius:14px;box-shadow:0 4px 24px rgba(21,128,61,.15);margin-bottom:12px;
  overflow:hidden;position:relative;}
.active-job-card::before{content:'';position:absolute;right:-30px;top:-30px;width:140px;height:140px;
  border-radius:50%;background:rgba(21,128,61,.08);}
.ajc-inner{padding:16px 18px;position:relative;z-index:1;}
.ajc-top{display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;}
.ajc-live{display:flex;align-items:center;gap:6px;font-size:.72rem;font-weight:600;color:#4ade80;}
.ajc-live-dot{width:7px;height:7px;border-radius:50%;background:#22c55e;animation:ldot 1.8s infinite;}
.ajc-sr{font-size:1.1rem;font-weight:700;color:#fff;}
.ajc-client{font-size:.95rem;font-weight:700;color:#fff;margin-bottom:3px;}
.ajc-site{font-size:.77rem;color:rgba(255,255,255,.65);display:flex;align-items:center;gap:4px;margin-bottom:10px;}
.ajc-issue{font-size:.78rem;color:rgba(255,255,255,.7);line-height:1.5;margin-bottom:12px;overflow-wrap:anywhere;}
.ajc-foot{display:flex;align-items:center;justify-content:space-between;padding-top:12px;
  border-top:1px solid rgba(255,255,255,.1);flex-wrap:wrap;gap:8px;}
.ajc-clock{display:flex;align-items:center;gap:6px;}
.ajc-clock i{color:rgba(255,255,255,.5);font-size:.75rem;}
.ajc-clock-lbl{font-size:.72rem;color:rgba(255,255,255,.55);}
.ajc-timer{font-size:1.05rem;font-weight:700;
  color:#fff;font-variant-numeric:tabular-nums;}
.ajc-tag{font-size:.67rem;font-weight:700;padding:3px 8px;border-radius:20px;
  background:rgba(154,128,83,.3);color:#f5deb3;}
.ajc-tag.hot{background:rgba(220,38,38,.25);color:#fca5a5;}

.punchout-btn{width:100%;padding:14px;border-radius:12px;background:linear-gradient(135deg,#9a8053,#b8975e);
  color:#fff;border:none;font-size:.88rem;font-weight:700;cursor:pointer;display:flex;
  align-items:center;justify-content:center;gap:8px;margin-top:10px;
  box-shadow:0 4px 16px rgba(154,128,83,.35);
  transition:all .15s;text-decoration:none;}
.punchout-btn:hover{transform:translateY(-1px);box-shadow:0 6px 20px rgba(154,128,83,.45);}
.punchin-btn{background:linear-gradient(135deg,#15803d,#16a34a);box-shadow:0 4px 16px rgba(21,128,61,.35);}
.punchin-btn:hover{box-shadow:0 6px 20px rgba(21,128,61,.45);}

/* ══════════════════════════════════════════════════════ BARS + CHARTS ═══ */
.funnel-row{display:flex;align-items:center;gap:10px;margin-bottom:9px;text-decoration:none;
  color:inherit;border-radius:8px;padding:3px 4px;margin-left:-4px;transition:background .15s;}
.funnel-row:last-child{margin-bottom:0;}
a.funnel-row:hover{background:var(--card2);}
.funnel-lbl{font-size:.73rem;color:var(--muted);width:118px;flex-shrink:0;
  white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
.funnel-track{flex:1;height:8px;background:rgba(0,0,0,.06);border-radius:4px;overflow:hidden;}
[data-bs-theme="dark"] .funnel-track{background:rgba(255,255,255,.07);}
.funnel-fill{height:100%;border-radius:4px;width:0;transition:width 1s cubic-bezier(.4,0,.2,1);}
.funnel-n{font-size:.72rem;font-weight:600;color:var(--text);width:22px;text-align:right;flex-shrink:0;}

.star-row{display:flex;gap:2px;}
.star-row i{font-size:.75rem;color:#f59e0b;}
.rating-hist .h-row{display:flex;align-items:center;gap:8px;margin-bottom:7px;}
.rating-hist .h-row:last-child{margin-bottom:0;}
.rating-hist .h-lbl{font-size:.7rem;color:var(--muted);width:16px;text-align:right;flex-shrink:0;}
.rating-hist .h-track{flex:1;height:8px;background:rgba(0,0,0,.06);border-radius:4px;overflow:hidden;}
[data-bs-theme="dark"] .rating-hist .h-track{background:rgba(255,255,255,.07);}
.rating-hist .h-fill{height:100%;background:linear-gradient(90deg,#f59e0b,#fbbf24);
  border-radius:4px;width:0;transition:width 1s cubic-bezier(.4,0,.2,1);}
.rating-hist .h-n{font-size:.7rem;color:var(--muted);width:20px;flex-shrink:0;}

.ch{position:relative;width:100%;}
.ch canvas{position:absolute;inset:0;width:100%!important;height:100%!important;}
.ch-160{height:160px;}.ch-120{height:120px;}
.donut-wrap{display:flex;align-items:center;gap:16px;flex-wrap:wrap;}
.ch-donut{position:relative;width:130px;height:130px;flex-shrink:0;}
.ch-donut canvas{position:absolute;inset:0;width:100%!important;height:100%!important;}
.donut-center{position:absolute;inset:0;display:flex;flex-direction:column;
  align-items:center;justify-content:center;pointer-events:none;}
.donut-center-val{font-size:1.5rem;
  font-weight:700;color:var(--text);line-height:1;}
.donut-center-lbl{font-size:.6rem;color:var(--muted);}
.donut-legend{display:flex;flex-direction:column;gap:8px;flex:1;min-width:150px;}
.leg-row{display:flex;align-items:center;gap:8px;}
.leg-dot{width:9px;height:9px;border-radius:3px;flex-shrink:0;}
.leg-name{font-size:.74rem;color:var(--muted);}
.leg-val{font-size:.8rem;font-weight:600;color:var(--text);margin-left:auto;}

/* ══════════════════════════════════════════════════════ QC + SPEND ═══ */
.qc-item{background:var(--card2);border:1px solid var(--border);border-radius:12px;padding:13px 15px;
  margin-bottom:9px;position:relative;overflow:hidden;}
.qc-item:last-child{margin-bottom:0;}
.qc-item::before{content:'';position:absolute;left:0;top:0;bottom:0;width:3px;}
.qc-pending::before{background:var(--amber);}
.rework-item::before{background:var(--red);}
.hold-item::before{background:#a16207;}
.qc-item-top{display:flex;align-items:center;justify-content:space-between;gap:8px;margin-bottom:6px;}
.qc-sr-id{font-size:.95rem;font-weight:700;color:var(--gold);}
.qc-client{font-size:.82rem;font-weight:600;color:var(--text);margin-bottom:2px;}
.qc-site{font-size:.71rem;color:var(--muted);display:flex;align-items:center;gap:3px;margin-bottom:6px;}
.qc-note{font-size:.74rem;color:var(--muted);line-height:1.45;overflow-wrap:anywhere;}
.rework-note{font-size:.74rem;color:var(--red);background:var(--red-bg);border-radius:8px;
  padding:9px 11px;margin-top:7px;line-height:1.5;border:1px solid rgba(220,38,38,.15);overflow-wrap:anywhere;}
.rework-note strong{display:block;margin-bottom:3px;font-size:.72rem;text-transform:uppercase;letter-spacing:.05em;}
.qc-footer{display:flex;align-items:center;justify-content:space-between;margin-top:9px;
  padding-top:8px;border-top:1px solid var(--border);gap:6px;flex-wrap:wrap;}
.qc-time{font-size:.68rem;color:var(--light);}

.exp-item{display:flex;align-items:center;justify-content:space-between;padding:11px 0;
  border-bottom:1px solid var(--border);gap:10px;}
.exp-item:last-child{border-bottom:none;}
.exp-left{display:flex;align-items:center;gap:10px;flex:1;min-width:0;}
.exp-icon{width:34px;height:34px;border-radius:10px;background:var(--gold-bg);display:flex;
  align-items:center;justify-content:center;font-size:.85rem;color:var(--gold);flex-shrink:0;}
.exp-sr{font-size:.82rem;font-weight:600;color:var(--text);margin-bottom:1px;}
.exp-cat{font-size:.7rem;color:var(--muted);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;}
.exp-amount{font-size:1.1rem;font-weight:700;
  color:var(--text);white-space:nowrap;}
.exp-receipt{font-size:.65rem;font-weight:600;padding:2px 6px;border-radius:6px;}
.pending-receipt{background:var(--amber-bg);color:var(--amber);}
.uploaded-receipt{background:var(--green-bg);color:var(--green);}
</style>
@endpush

{{-- ══════════════════════════════════════════════════════ LIVE ═══ --}}
@section('live')
  @if(!empty($live['active']))
    <div class="live-chip">
      <div class="live-dot"></div>
      On site — {{ $live['active']['site'] }} · Punched in {{ $live['active']['since'] }}
    </div>
  @else
    <div class="live-chip"><i class="bi bi-cup-hot"></i>Not punched in</div>
  @endif

  @if(!empty($live['sla']))
    <a class="sla-warn {{ $live['sla']['state'] }}" href="{{ $go(['job' => $live['sla']['ref']]) }}">
      <i class="bi bi-{{ $live['sla']['state'] === 'crit' ? 'exclamation-triangle-fill' : 'clock' }}"></i>
      {{ $live['sla']['ref'] }} — {{ $live['sla']['label'] }}
    </a>
  @endif
@endsection

{{-- ══════════════════════════════════════════════════════ TABS ═══ --}}
@section('tabs')
  <button class="tab-btn active" data-tab="today">
    <i class="bi bi-lightning-charge-fill"></i><span>Today</span>
  </button>

  {{-- Leaves the page: the job list and terminal live in worker.pipeline --}}
  <a class="tab-btn jump" href="{{ $go() }}">
    <i class="bi bi-list-task"></i><span>Pipeline</span>
    @if(($scorecard['reworkCount'] ?? 0) > 0)
      <span class="tab-badge pill-red">{{ $scorecard['reworkCount'] }}</span>
    @endif
  </a>

  <button class="tab-btn" data-tab="workload">
    <i class="bi bi-diagram-3"></i><span>Workload</span>
  </button>
  <button class="tab-btn" data-tab="scorecard">
    <i class="bi bi-bar-chart-line"></i><span>Scorecard</span>
  </button>
  <button class="tab-btn" data-tab="qc">
    <i class="bi bi-patch-check"></i><span>QC &amp; Rework</span>
    @if(($qc['badge'] ?? 0) > 0)
      <span class="tab-badge pill-amber">{{ $qc['badge'] }}</span>
    @endif
  </button>
  <button class="tab-btn" data-tab="expenses">
    <i class="bi bi-receipt"></i><span>Expenses</span>
  </button>
@endsection

{{-- ══════════════════════════════════════════════════════ CONTENT ═══ --}}
@section('content')

<!-- ─────────── TODAY ─────────── -->
<div class="tab-pane active" id="tab-today">

  <div class="sec-label"><i class="bi bi-activity"></i>Currently on site</div>

  @if($today['active'])
    @php $a = $today['active']; @endphp
    <div class="active-job-card">
      <div class="ajc-inner">
        <div class="ajc-top">
          <div class="ajc-live"><div class="ajc-live-dot"></div>In progress</div>
          <span class="ajc-sr">{{ $a['ref'] }}</span>
        </div>
        <div class="ajc-client">{{ $a['client'] }}</div>
        <div class="ajc-site"><i class="bi bi-geo-alt-fill"></i>{{ $a['site'] }}</div>
        <div class="ajc-issue">{{ $a['issue'] }}</div>
        <div class="ajc-foot">
          <div class="ajc-clock">
            <i class="bi bi-clock"></i>
            <span class="ajc-clock-lbl">On site for</span>
            <span class="ajc-timer" id="onSiteTimer" data-since="{{ $a['punchInAt'] }}">&mdash;</span>
          </div>
          <div style="display:flex;gap:6px;">
            <span class="ajc-tag {{ in_array(strtolower($a['priority']), ['high','critical','urgent']) ? 'hot' : '' }}">{{ $a['priority'] }}</span>
            <span class="ajc-tag">{{ $a['domain'] }}</span>
          </div>
        </div>
        <a class="punchout-btn" href="{{ $go(['job' => $a['ref']], 'terminal') }}">
          <i class="bi bi-broadcast"></i>Open job terminal
        </a>
      </div>
    </div>
  @else
    <div class="card">
      <div class="empty-state">
        <i class="bi bi-geo-alt"></i>
        <h6>Not punched in</h6>
        <p>Pick up a job from the pipeline to start the clock.</p>
        <a href="{{ $go() }}">Open my pipeline</a>
      </div>
    </div>
  @endif

  <div class="sec-label"><i class="bi bi-calendar-event"></i>Next assigned job</div>

  @if($today['next'])
    @php $n = $today['next']; @endphp
    <div class="job-card">
      <div class="job-card-accent" style="background:{{ $n['accent'] }};"></div>
      <div class="job-card-body">
        <div class="job-card-top">
          <div>
            <div class="job-sr-id">{{ $n['ref'] }}</div>
            <div class="job-client">{{ $n['client'] }}</div>
          </div>
          <span class="pill {{ $n['pill'] }}"><i class="bi bi-calendar-check"></i>{{ $n['chip'] }}</span>
        </div>
        <div class="job-site"><i class="bi bi-geo-alt" style="color:var(--gold);font-size:.7rem;"></i>{{ $n['site'] }}</div>
        <div class="job-issue">{{ $n['issue'] }}</div>
        <div class="job-meta-row">
          <div class="job-meta-item"><i class="bi bi-tools"></i>{{ $n['domain'] }}</div>
          <div class="job-meta-item"><i class="bi bi-hourglass-split"></i>{{ $n['hrsAgo'] }}h since dispatch</div>
          <div class="job-meta-item"><i class="bi bi-exclamation-circle"></i>{{ $n['priority'] }}</div>
        </div>
        <a class="punchout-btn punchin-btn" style="margin-top:12px;" href="{{ $go(['job' => $n['ref']]) }}">
          <i class="bi bi-box-arrow-in-right"></i>{{ $n['status'] === 'Pending' ? 'Accept and set ETA' : 'Open this job' }}
        </a>
      </div>
    </div>
  @else
    <div class="card">
      <div class="empty-state">
        <i class="bi bi-calendar2-check"></i>
        <h6>Queue is clear</h6>
        <p>Nothing else is waiting on you right now.</p>
      </div>
    </div>
  @endif

  @if(count($today['others']))
    <div class="sec-label"><i class="bi bi-clock-history"></i>Also assigned</div>
    @foreach($today['others'] as $j)
      <a class="job-card as-link" href="{{ $go(['job' => $j['ref']]) }}">
        <div class="job-card-accent" style="background:{{ $j['accent'] }};"></div>
        <div class="job-card-body">
          <div class="job-card-top">
            <div>
              <div class="job-sr-id">{{ $j['ref'] }}</div>
              <div class="job-client">{{ $j['client'] }}</div>
            </div>
            <span class="pill {{ $j['pill'] }}"><i class="bi bi-hourglass-split"></i>{{ $j['chip'] }}</span>
          </div>
          <div class="job-site"><i class="bi bi-geo-alt" style="color:var(--gold);font-size:.7rem;"></i>{{ $j['site'] }}</div>
          <div class="job-issue">{{ \Illuminate\Support\Str::limit($j['issue'], 150) }}</div>
          <div class="job-meta-row">
            <div class="job-meta-item"><i class="bi bi-tools"></i>{{ $j['domain'] }}</div>
            <div class="job-meta-item"><i class="bi bi-flag"></i>{{ $j['status'] }}</div>
            <div class="job-meta-item"><i class="bi bi-exclamation-circle"></i>{{ $j['priority'] }}</div>
          </div>
        </div>
      </a>
    @endforeach
  @endif
</div>

<!-- ─────────── WORKLOAD ─────────── -->
<div class="tab-pane" id="tab-workload">

  <div class="stat-grid g3">
    <div class="stat-box">
      <div class="stat-label">Assigned</div>
      <div class="stat-val" style="color:var(--gold);">{{ $pipeline['stats']['assigned'] }}</div>
      <div class="stat-sub">This month</div>
    </div>
    <div class="stat-box">
      <div class="stat-label">Completed</div>
      <div class="stat-val" style="color:var(--green);">{{ $pipeline['stats']['completed'] }}</div>
      <div class="stat-sub">Closed out</div>
    </div>
    <div class="stat-box">
      <div class="stat-label">Active now</div>
      <div class="stat-val" style="color:var(--amber);">{{ $pipeline['stats']['activeNow'] }}</div>
      <div class="stat-sub">On site + queued</div>
    </div>
  </div>

  <div class="card card-pad">
    <div class="sec-label" style="margin-top:0;"><i class="bi bi-filter-left"></i>My SR status breakdown</div>
    @foreach($pipeline['funnel'] as $f)
      <a class="funnel-row" href="{{ $go(['filter' => $f['f']]) }}">
        <div class="funnel-lbl">{{ $f['lbl'] }}</div>
        <div class="funnel-track"><div class="funnel-fill" data-w="{{ $f['w'] }}%" style="background:{{ $f['c'] }};"></div></div>
        <div class="funnel-n">{{ $f['n'] }}</div>
      </a>
    @endforeach
  </div>

  @if($pipeline['scope'])
  <div class="card card-pad">
    <div class="sec-label" style="margin-top:0;"><i class="bi bi-pie-chart"></i>Warranty scope split</div>
    <div class="donut-wrap">
      <div class="ch-donut">
        <canvas id="scopeDonut"></canvas>
        <div class="donut-center">
          <div class="donut-center-val">{{ $pipeline['scope']['total'] }}</div>
          <div class="donut-center-lbl">Total</div>
        </div>
      </div>
      <div class="donut-legend">
        @foreach($pipeline['scope']['labels'] as $i => $label)
          <div class="leg-row">
            <div class="leg-dot" style="background:{{ $pipeline['scope']['colors'][$i] }};"></div>
            <span class="leg-name">{{ $label }}</span>
            <span class="leg-val">{{ $pipeline['scope']['data'][$i] }}</span>
          </div>
        @endforeach
      </div>
    </div>
  </div>
  @endif

  <div class="card card-pad">
    <div class="sec-label" style="margin-top:0;"><i class="bi bi-exclamation-circle"></i>Awaiting my action</div>
    @forelse($pipeline['action'] as $a)
      <a class="qc-item as-link {{ $a['kind'] === 'rework' ? 'rework-item' : 'hold-item' }}"
         href="{{ $go(['job' => $a['ref'], 'filter' => $a['kind'] === 'rework' ? 'Rework' : 'On Hold']) }}">
        <div class="qc-item-top">
          <div class="qc-sr-id">{{ $a['ref'] }}</div>
          <span class="pill {{ $a['pill'] }}"><i class="bi bi-arrow-counterclockwise"></i>{{ $a['label'] }}</span>
        </div>
        <div class="qc-client">{{ $a['client'] }}</div>
        <div class="qc-site"><i class="bi bi-geo-alt"></i>{{ $a['site'] }}</div>
        <div class="rework-note">
          <strong>{{ $a['kind'] === 'rework' ? 'QC instruction' : 'Hold reason' }}</strong>{{ $a['note'] }}
        </div>
      </a>
    @empty
      <div class="empty-state">
        <i class="bi bi-check2-circle"></i>
        <p>Nothing is waiting on you.</p>
      </div>
    @endforelse
  </div>
</div>

<!-- ─────────── SCORECARD ─────────── -->
<div class="tab-pane" id="tab-scorecard">

  <div class="stat-grid g3">
    <div class="stat-box">
      <div class="stat-label">Jobs done</div>
      <div class="stat-val" style="color:var(--gold);">{{ $scorecard['jobsDone'] }}</div>
      @if($scorecard['jobsDelta'] > 0)
        <div class="stat-delta d-up"><i class="bi bi-arrow-up"></i>+{{ $scorecard['jobsDelta'] }} vs last month</div>
      @elseif($scorecard['jobsDelta'] < 0)
        <div class="stat-delta d-dn"><i class="bi bi-arrow-down"></i>{{ $scorecard['jobsDelta'] }} vs last month</div>
      @else
        <div class="stat-sub">Level with last month</div>
      @endif
    </div>
    <div class="stat-box">
      <div class="stat-label">Hours on site</div>
      <div class="stat-val" style="color:#f59e0b;">{{ $scorecard['hours'] }}</div>
      <div class="stat-sub">Avg {{ $scorecard['avgDuration'] }} per job</div>
    </div>
    <div class="stat-box">
      <div class="stat-label">Rework</div>
      <div class="stat-val" style="color:var(--amber);">{{ $scorecard['reworkCount'] }}</div>
      <div class="stat-sub">Returned by QC</div>
    </div>
  </div>

  <div class="card card-pad">
    <div class="sec-label" style="margin-top:0;"><i class="bi bi-star-fill" style="color:#f59e0b;"></i>Client rating breakdown</div>
    @if($scorecard['rating'])
      @php $r = $scorecard['rating']; @endphp
      <div style="display:flex;align-items:center;gap:18px;margin-bottom:14px;padding-bottom:12px;
        border-bottom:1px solid var(--border);flex-wrap:wrap;">
        <div>
          <div class="cg" style="font-size:3rem;font-weight:700;color:var(--gold);line-height:1;">{{ $r['avg'] }}</div>
          <div class="star-row" style="margin:4px 0 3px;">
            @for($i = 1; $i <= 5; $i++)
              @if($r['avg'] >= $i)
                <i class="bi bi-star-fill"></i>
              @elseif($r['avg'] >= $i - 0.5)
                <i class="bi bi-star-half"></i>
              @else
                <i class="bi bi-star"></i>
              @endif
            @endfor
          </div>
          <div style="font-size:.7rem;color:var(--muted);">{{ $r['count'] }} {{ $r['count'] === 1 ? 'review' : 'reviews' }}</div>
        </div>
        <div style="flex:1;min-width:150px;">
          <div class="stat-box filled">
            <div class="stat-label">Response rate</div>
            <div class="cg" style="font-size:1.4rem;font-weight:700;color:var(--text);">{{ $r['response'] }}%</div>
            <div class="stat-sub">{{ $r['count'] }} of {{ $r['closed'] }} completed</div>
          </div>
        </div>
      </div>
      <div class="rating-hist">
        @php $peak = max(1, collect($r['hist'])->max('c')); @endphp
        @foreach($r['hist'] as $row)
          <div class="h-row">
            <div class="h-lbl">{{ $row['s'] }}</div>
            <div class="h-track"><div class="h-fill" data-w="{{ (int) round(($row['c'] / $peak) * 100) }}%"></div></div>
            <div class="h-n">{{ $row['c'] }}</div>
          </div>
        @endforeach
      </div>
    @else
      <div class="empty-state">
        <i class="bi bi-star"></i>
        <p>No client ratings have come back on your jobs yet.</p>
      </div>
    @endif
  </div>

  <div class="card card-pad">
    <div class="sec-label" style="margin-top:0;"><i class="bi bi-graph-up"></i>Monthly job trend</div>
    <div class="ch ch-120"><canvas id="trendChart"></canvas></div>
  </div>

  <div class="card card-pad">
    <div class="sec-label" style="margin-top:0;"><i class="bi bi-tools"></i>Jobs by service category</div>
    @if(count($scorecard['categories']['labels']))
      <div class="ch ch-160"><canvas id="catChart"></canvas></div>
    @else
      <div class="empty-state"><i class="bi bi-tools"></i><p>No completed jobs to break down yet.</p></div>
    @endif
  </div>
</div>

<!-- ─────────── QC & REWORK ─────────── -->
<div class="tab-pane" id="tab-qc">

  <div class="sec-label"><i class="bi bi-hourglass-split" style="color:var(--amber);"></i>QC pending
    @if(count($qc['pending']))<span class="pill pill-amber" style="margin-left:6px;">{{ count($qc['pending']) }} awaiting review</span>@endif
  </div>

  @if(count($qc['pending']))
    <div style="font-size:.74rem;color:var(--muted);margin-bottom:10px;line-height:1.5;">
      Submitted and with the reviewer. Nothing is needed from you on these right now.
    </div>
    @foreach($qc['pending'] as $p)
      <a class="qc-item qc-pending as-link" href="{{ $go(['job' => $p['ref'], 'filter' => 'Review']) }}">
        <div class="qc-item-top">
          <div class="qc-sr-id">{{ $p['ref'] }}</div>
          <span class="pill pill-amber"><i class="bi bi-hourglass"></i>QC pending</span>
        </div>
        <div class="qc-client">{{ $p['client'] }}</div>
        <div class="qc-site"><i class="bi bi-geo-alt"></i>{{ $p['site'] }}</div>
        <div class="qc-note">{{ \Illuminate\Support\Str::limit($p['note'], 200) }}</div>
        <div class="qc-footer">
          <span class="qc-time"><i class="bi bi-clock"></i> Submitted {{ $p['since'] }}</span>
          <span class="pill pill-amber">Waiting on QC</span>
        </div>
      </a>
    @endforeach
  @else
    <div class="card"><div class="empty-state"><i class="bi bi-inbox"></i><p>Nothing of yours is sitting in QC.</p></div></div>
  @endif

  <div class="sec-label" style="margin-top:22px;"><i class="bi bi-arrow-counterclockwise" style="color:var(--red);"></i>Rework required
    @if(count($qc['rework']))<span class="pill pill-red" style="margin-left:6px;">{{ count($qc['rework']) }} needs action</span>@endif
  </div>

  @if(count($qc['rework']))
    <div style="font-size:.74rem;color:var(--red);background:var(--red-bg);border:1px solid rgba(220,38,38,.15);
      border-radius:10px;padding:10px 12px;margin-bottom:10px;display:flex;align-items:center;gap:7px;">
      <i class="bi bi-exclamation-circle-fill"></i>
      QC returned the jobs below. Revisit the site and resubmit with fresh proof.
    </div>
    @foreach($qc['rework'] as $w)
      <div class="qc-item rework-item">
        <div class="qc-item-top">
          <div class="qc-sr-id">{{ $w['ref'] }}</div>
          <span class="pill pill-red"><i class="bi bi-x-circle"></i>Returned</span>
        </div>
        <div class="qc-client">{{ $w['client'] }}</div>
        <div class="qc-site"><i class="bi bi-geo-alt"></i>{{ $w['site'] }}</div>
        <div class="qc-note">{{ \Illuminate\Support\Str::limit($w['note'], 150) }}</div>
        <div class="rework-note">
          <strong><i class="bi bi-exclamation-triangle-fill"></i> QC rejection reason</strong>
          {{ $w['reason'] }}
        </div>
        <div class="qc-footer">
          <span class="qc-time"><i class="bi bi-clock"></i> Returned {{ $w['since'] }}</span>
          <a class="action-btn primary" href="{{ $go(['job' => $w['ref'], 'filter' => 'Rework']) }}">Start rework</a>
        </div>
      </div>
    @endforeach
  @else
    <div class="card"><div class="empty-state"><i class="bi bi-check2-all"></i><p>No rework outstanding. Nice.</p></div></div>
  @endif
</div>

<!-- ─────────── EXPENSES ─────────── -->
<div class="tab-pane" id="tab-expenses">

  <div class="stat-grid g2">
    <div class="stat-box">
      <div class="stat-label">Total this month</div>
      <div class="stat-val" style="color:var(--gold);">{{ number_format($expenses['total'], 0) }}</div>
      <div class="stat-sub">AED across {{ $expenses['lines'] }} {{ $expenses['lines'] === 1 ? 'line' : 'lines' }}</div>
    </div>
    <div class="stat-box">
      <div class="stat-label">Pending receipts</div>
      <div class="stat-val" style="color:var(--amber);">{{ $expenses['pendingReceipts'] }}</div>
      <div class="stat-sub">{{ $expenses['pendingReceipts'] ? 'Upload required' : 'All receipts in' }}</div>
    </div>
  </div>

  <div class="card card-pad">
    <div class="sec-label" style="margin-top:0;"><i class="bi bi-receipt"></i>Expense by job</div>
    @forelse($expenses['byJob'] as $e)
      <div class="exp-item">
        <div class="exp-left">
          <div class="exp-icon"><i class="bi bi-box-seam"></i></div>
          <div style="min-width:0;">
            <div class="exp-sr">{{ $e['ref'] }}</div>
            <div class="exp-cat">{{ $e['client'] }} &middot; {{ $e['category'] }}</div>
          </div>
        </div>
        <div style="text-align:right;">
          <div class="exp-amount">{{ number_format($e['amount'], 0) }}</div>
          <div class="exp-receipt {{ $e['receipt'] ? 'uploaded-receipt' : 'pending-receipt' }}">
            @if($e['receipt'])<i class="bi bi-check2-circle"></i> Receipt
            @else<i class="bi bi-exclamation-circle"></i> No receipt @endif
          </div>
        </div>
      </div>
    @empty
      <div class="empty-state"><i class="bi bi-receipt"></i><p>No material expenses logged this month.</p></div>
    @endforelse
  </div>

  @if(count($expenses['byCategory']['labels']))
  <div class="card card-pad">
    <div class="sec-label" style="margin-top:0;"><i class="bi bi-pie-chart"></i>By category</div>
    <div class="donut-wrap">
      <div class="ch-donut">
        <canvas id="expDonut"></canvas>
        <div class="donut-center">
          <div class="donut-center-val" style="font-size:1rem;">{{ $expenses['totalShort'] }}</div>
          <div class="donut-center-lbl">AED total</div>
        </div>
      </div>
      <div class="donut-legend">
        @foreach($expenses['byCategory']['labels'] as $i => $label)
          <div class="leg-row">
            <div class="leg-dot" style="background:{{ $expenses['byCategory']['colors'][$i] }};"></div>
            <span class="leg-name">{{ $label }}</span>
            <span class="leg-val">{{ number_format($expenses['byCategory']['data'][$i], 0) }}</span>
          </div>
        @endforeach
      </div>
    </div>
  </div>
  @endif

  <div class="card card-pad">
    <div class="sec-label" style="margin-top:0;"><i class="bi bi-graph-up"></i>Monthly expense trend</div>
    <div class="ch ch-120"><canvas id="expTrend"></canvas></div>
  </div>
</div>

@endsection

{{-- ══════════════════════════════════════════════════════ SCRIPT ═══ --}}
@push('scripts')
<script>
'use strict';

const SCOPE      = @json($pipeline['scope'] ?? null);
const TREND      = @json($scorecard['trend']);
const CATEGORIES = @json($scorecard['categories']);
const EXP_CATS   = @json($expenses['byCategory']);
const EXP_TREND  = @json($expenses['trend']);
const GREETING   = @json($userShort ?? ($userName ?? ''));
const TODO       = @json(($qc['badge'] ?? 0));

/* ══════════════════════════════════════════════════════
   ON-SITE TIMER
══════════════════════════════════════════════════════ */
function updateOnSiteTimer() {
  const el = $('onSiteTimer');
  if (!el || !el.dataset.since) return;

  const start = new Date(el.dataset.since);
  if (isNaN(start.getTime())) return;

  const diff = Math.max(0, Math.floor((Date.now() - start.getTime()) / 1000));
  const h = Math.floor(diff / 3600);
  const m = Math.floor((diff % 3600) / 60);
  el.textContent = `${h}h ${String(m).padStart(2, '0')}m`;
}
setInterval(updateOnSiteTimer, 10000);
updateOnSiteTimer();

/* ══════════════════════════════════════════════════════
   CHARTS
══════════════════════════════════════════════════════ */
const CHARTS = {};

function chartColors() {
  const dark = document.documentElement.getAttribute('data-bs-theme') === 'dark';
  return {
    grid:  dark ? 'rgba(255,255,255,.06)' : 'rgba(0,0,0,.05)',
    text:  dark ? '#e8e0d4' : '#1a1614',
    muted: dark ? '#7a756e' : '#8a8480',
  };
}

function buildCharts() {
  if (typeof Chart === 'undefined') return;
  const C = chartColors();

  const scope = $('scopeDonut');
  if (scope && SCOPE) CHARTS.scope = new Chart(scope, {
    type: 'doughnut',
    data: { labels: SCOPE.labels, datasets: [{ data: SCOPE.data, backgroundColor: SCOPE.colors, borderWidth: 0, hoverOffset: 4 }] },
    options: {
      responsive: true, maintainAspectRatio: false, cutout: '72%',
      plugins: { legend: { display: false }, tooltip: { callbacks: {
        label: (ctx) => ` ${ctx.label}: ${ctx.parsed} jobs` } } },
    },
  });

  const trend = $('trendChart');
  if (trend) {
    const g = trend.getContext('2d').createLinearGradient(0, 0, 0, 120);
    g.addColorStop(0, 'rgba(154,128,83,.35)');
    g.addColorStop(1, 'rgba(154,128,83,0)');

    CHARTS.trend = new Chart(trend, {
      type: 'line',
      data: { labels: TREND.labels, datasets: [{ label: 'Jobs', data: TREND.data,
        borderColor: '#9a8053', borderWidth: 2.5, backgroundColor: g, fill: true,
        tension: .4, pointRadius: 4, pointBackgroundColor: '#9a8053', pointHoverRadius: 6 }] },
      options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { display: false }, tooltip: { backgroundColor: 'rgba(0,0,0,.8)', cornerRadius: 8, padding: 9 } },
        scales: {
          x: { grid: { color: C.grid }, ticks: { color: C.muted, font: { size: 10 } } },
          y: { grid: { color: C.grid }, ticks: { color: C.muted, font: { size: 10 }, precision: 0 }, beginAtZero: true },
        },
      },
    });
  }

  const cat = $('catChart');
  if (cat && CATEGORIES.labels.length) CHARTS.cat = new Chart(cat, {
    type: 'bar',
    data: { labels: CATEGORIES.labels, datasets: [{ data: CATEGORIES.data,
      backgroundColor: CATEGORIES.colors, borderRadius: 7, borderSkipped: false }] },
    options: {
      responsive: true, maintainAspectRatio: false, indexAxis: 'y',
      plugins: { legend: { display: false }, tooltip: { backgroundColor: 'rgba(0,0,0,.8)', cornerRadius: 8, padding: 9,
        callbacks: { label: (ctx) => ` ${ctx.parsed.x} jobs` } } },
      scales: {
        x: { grid: { color: C.grid }, ticks: { color: C.muted, font: { size: 10 }, precision: 0 }, beginAtZero: true },
        y: { grid: { display: false }, ticks: { color: C.text, font: { size: 11 } } },
      },
    },
  });

  const donut = $('expDonut');
  if (donut && EXP_CATS.labels.length) CHARTS.expD = new Chart(donut, {
    type: 'doughnut',
    data: { labels: EXP_CATS.labels, datasets: [{ data: EXP_CATS.data, backgroundColor: EXP_CATS.colors, borderWidth: 0, hoverOffset: 4 }] },
    options: {
      responsive: true, maintainAspectRatio: false, cutout: '68%',
      plugins: { legend: { display: false }, tooltip: { callbacks: {
        label: (ctx) => ` AED ${ctx.parsed.toLocaleString()}` } } },
    },
  });

  const spend = $('expTrend');
  if (spend) {
    const g2 = spend.getContext('2d').createLinearGradient(0, 0, 0, 120);
    g2.addColorStop(0, 'rgba(154,128,83,.3)');
    g2.addColorStop(1, 'rgba(154,128,83,0)');

    CHARTS.expT = new Chart(spend, {
      type: 'line',
      data: { labels: EXP_TREND.labels, datasets: [{ label: 'AED', data: EXP_TREND.data,
        borderColor: '#9a8053', borderWidth: 2.5, backgroundColor: g2, fill: true,
        tension: .4, pointRadius: 4, pointBackgroundColor: '#9a8053', pointHoverRadius: 6 }] },
      options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { display: false }, tooltip: { backgroundColor: 'rgba(0,0,0,.8)', cornerRadius: 8, padding: 9,
          callbacks: { label: (ctx) => ` AED ${ctx.parsed.y.toLocaleString()}` } } },
        scales: {
          x: { grid: { color: C.grid }, ticks: { color: C.muted, font: { size: 10 } } },
          y: { grid: { color: C.grid }, ticks: { color: C.muted, font: { size: 10 },
            callback: (v) => (v >= 1000 ? (v / 1000) + 'k' : v) }, beginAtZero: true },
        },
      },
    });
  }
}

/** The layout calls this whenever the theme flips. */
window.onThemeChange = () => {
  const C = chartColors();
  Object.values(CHARTS).forEach((ch) => {
    if (!ch || !ch.options || !ch.options.scales) return;
    ['x', 'y'].forEach((axis) => {
      const s = ch.options.scales[axis];
      if (!s) return;
      if (s.grid) s.grid.color = C.grid;
      if (s.ticks) s.ticks.color = C.muted;
    });
    ch.update('none');
  });
};

/* A canvas inside a hidden pane measures 0x0, so build the charts the
   first time one of those tabs is opened. */
let chartsBuilt = false;

['workload', 'scorecard', 'expenses'].forEach((tab) => {
  onTabShow(tab, () => {
    if (chartsBuilt) return;
    chartsBuilt = true;
    buildCharts();
  });
});

/* ══════════════════════════════════════════════════════
   INIT
══════════════════════════════════════════════════════ */
window.addEventListener('load', () => {
  animateBars();

  const hour = new Date().getHours();
  const greet = hour < 12 ? 'Good morning' : hour < 17 ? 'Good afternoon' : 'Good evening';

  if (TODO > 0) {
    showToast('warning', `${greet}, ${GREETING}`,
      `${TODO} ${TODO === 1 ? 'job needs' : 'jobs need'} your attention in QC and rework.`);
  } else {
    showToast('success', `${greet}, ${GREETING}`, 'Nothing is waiting on you in QC or rework.');
  }
});
</script>
@endpush