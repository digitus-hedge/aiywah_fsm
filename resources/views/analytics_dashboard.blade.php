@extends('layouts.layout')

@section('title', 'Analytics Dashboard | Aiywah FSM')
@section('page_title', 'Analytics Dashboard')
@section('page_icon', 'database')
@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600;700&display=swap" rel="stylesheet"/>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet"/>
<style>
/* ===== Analytics Dashboard - scoped page styles ===== */
.an-wrap{--gold:#9a8053;--gold-2:#b8975e;--grid-line:#eef1f7;--track:#eef1f7;}
[data-bs-theme="dark"] .an-wrap{--grid-line:#302f2e;--track:#302f2e;}
.an-wrap h4,.an-wrap h5,.an-wrap h6,.an-wrap .pg-hdr-title,.an-wrap .stat-num,
.an-wrap .card-title,.an-wrap .section-title,.an-wrap .big-num{}

/* PAGE HEADER */
.an-wrap .pg-header{background:linear-gradient(135deg,#9A7B4F 0%,#7A6140 100%);border-radius:10px;padding:20px 24px;margin-bottom:20px;color:#fff;position:relative;overflow:hidden;}
.an-wrap .pg-header::before{content:'';position:absolute;left:-40px;bottom:-40px;width:180px;height:180px;border-radius:50%;background:rgba(255,255,255,.05);}
.an-wrap .pg-header::after{content:'';position:absolute;right:-30px;top:-30px;width:160px;height:160px;border-radius:50%;background:rgba(255,255,255,.07);}
.an-wrap .pg-hdr-title{font-size:1rem;font-weight:600;margin:0 0 3px;position:relative;z-index:1;}
.an-wrap .pg-hdr-desc{font-size:.78rem;margin:0;opacity:.88;position:relative;z-index:1;max-width:640px;line-height:1.5;}
.an-wrap .pg-hdr-meta{display:flex;align-items:center;gap:8px;margin-top:10px;position:relative;z-index:1;flex-wrap:wrap;}
.an-wrap .meta-badge{background:rgba(255,255,255,.18);border:1px solid rgba(255,255,255,.3);border-radius:20px;font-size:.6875rem;padding:2px 10px;font-weight:500;}

/* SCOPE / FILTER BAR */
.an-wrap .scope-bar{background:var(--card-bg);border:1px solid var(--card-border);border-radius:10px;padding:12px 16px;margin-bottom:18px;box-shadow:var(--card-shadow);display:flex;flex-wrap:wrap;gap:12px;align-items:flex-end;}
.an-wrap .filter-group{display:flex;flex-direction:column;gap:4px;}
.an-wrap .filter-label{font-size:.68rem;font-weight:600;color:var(--text-muted);text-transform:uppercase;letter-spacing:.05em;}
.an-wrap .filter-control{height:34px;padding:0 10px;border:1px solid var(--border-color);border-radius:7px;background:var(--input-bg);color:var(--text-primary);font-size:.8rem;min-width:150px;transition:border-color .15s;}
.an-wrap .filter-control:focus{outline:none;border-color:var(--gold);box-shadow:0 0 0 3px rgba(154,128,83,.12);}
.an-wrap .filter-actions{margin-left:auto;display:flex;gap:8px;align-items:flex-end;}
.an-wrap .btn-ghost{height:34px;display:inline-flex;align-items:center;gap:6px;padding:0 13px;background:var(--surface-2);border:1px solid var(--border-color);color:var(--text-primary);border-radius:7px;font-size:.78rem;cursor:pointer;transition:all .15s;}
.an-wrap .btn-ghost:hover{border-color:var(--gold);color:var(--gold);}
.an-wrap .btn-gold{height:34px;display:inline-flex;align-items:center;gap:6px;padding:0 14px;background:linear-gradient(135deg,var(--gold),var(--gold-2));color:#fff;border:none;border-radius:7px;font-size:.78rem;font-weight:500;cursor:pointer;}
@media(max-width:575.98px){
  .an-wrap .filter-group{flex:1 1 100%;}
  .an-wrap .filter-control{width:100%;min-width:0;}
  .an-wrap .filter-actions{margin-left:0;width:100%;}
  .an-wrap .filter-actions .btn-ghost,.an-wrap .filter-actions .btn-gold{flex:1;justify-content:center;}
}

/* KPI STRIP */
.an-wrap .kpi-strip{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:16px;}
@media(max-width:1100px){.an-wrap .kpi-strip{grid-template-columns:repeat(2,1fr);}}
@media(max-width:520px){.an-wrap .kpi-strip{grid-template-columns:1fr;}}
.an-wrap .kpi-card{background:var(--card-bg);border:1px solid var(--card-border);border-radius:10px;padding:15px 16px;box-shadow:var(--card-shadow);position:relative;overflow:hidden;}
.an-wrap .kpi-top{display:flex;align-items:center;justify-content:space-between;margin-bottom:8px;}
.an-wrap .kpi-ico{width:34px;height:34px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:.95rem;}
.an-wrap .kpi-delta{font-size:.7rem;font-weight:600;padding:2px 7px;border-radius:20px;display:inline-flex;align-items:center;gap:3px;}
.an-wrap .kpi-delta.up{background:rgba(22,163,74,.12);color:#16a34a;}
.an-wrap .kpi-delta.down{background:rgba(239,68,68,.12);color:#ef4444;}
.an-wrap .kpi-delta.flat{background:var(--surface-2);color:var(--text-muted);}
.an-wrap .big-num{font-size:1.7rem;font-weight:700;color:var(--text-heading);line-height:1;margin-bottom:3px;}
.an-wrap .kpi-lbl{font-size:.72rem;color:var(--text-muted);}

/* CARD / GRID */
.an-wrap .card{background:var(--card-bg);border:1px solid var(--card-border);border-radius:10px;box-shadow:var(--card-shadow);overflow:hidden;}
.an-wrap .card-hdr{padding:13px 18px;border-bottom:1px solid var(--border-color);display:flex;align-items:center;justify-content:space-between;gap:10px;}
.an-wrap .card-hdr-left{display:flex;align-items:center;gap:9px;min-width:0;}
.an-wrap .card-hdr-ico{width:30px;height:30px;border-radius:7px;display:flex;align-items:center;justify-content:center;font-size:.85rem;flex-shrink:0;}
.an-wrap .card-title{font-size:.86rem;font-weight:600;color:var(--text-heading);}
.an-wrap .card-sub{font-size:.68rem;color:var(--text-muted);}
.an-wrap .card-body{padding:16px 18px;}
.an-wrap .grid{display:grid;gap:14px;margin-bottom:14px;}
.an-wrap .g-2{grid-template-columns:1fr 1fr;}
.an-wrap .g-3{grid-template-columns:2fr 1fr;}
.an-wrap .g-32{grid-template-columns:1fr 1fr 1fr;}
@media(max-width:991px){.an-wrap .g-2,.an-wrap .g-3,.an-wrap .g-32{grid-template-columns:1fr;}}
.an-wrap .section-title{font-size:.95rem;font-weight:700;color:var(--text-heading);margin:22px 0 12px;display:flex;align-items:center;gap:8px;flex-wrap:wrap;}
.an-wrap .section-title i{color:var(--gold);}
.an-wrap .section-title .st-line{flex:1;height:1px;background:var(--border-color);min-width:30px;}

/* LEGEND */
.an-wrap .legend{display:flex;flex-wrap:wrap;gap:12px;margin-top:12px;}
.an-wrap .lg-item{display:flex;align-items:center;gap:6px;font-size:.73rem;color:var(--text-muted);}
.an-wrap .lg-dot{width:10px;height:10px;border-radius:3px;flex-shrink:0;}

/* SVG chart text */
.an-wrap .axis-txt{font-size:10px;fill:var(--text-muted);font-weight:400;}
.an-wrap .grid-line{stroke:var(--grid-line);stroke-width:1;}
.an-wrap .val-txt{font-size:10px;fill:var(--text-muted);font-weight:600;}
.an-wrap .donut-center-num{font-size:1.5rem;font-weight:700;fill:var(--text-heading);}
.an-wrap .donut-center-lbl{font-size:9px;fill:var(--text-muted);text-transform:uppercase;letter-spacing:.05em;}

/* HORIZONTAL BARS */
.an-wrap .hbar-row{display:flex;align-items:center;gap:10px;margin-bottom:11px;}
.an-wrap .hbar-row:last-child{margin-bottom:0;}
.an-wrap .hbar-lbl{font-size:.75rem;color:var(--text-primary);width:130px;flex-shrink:0;display:flex;align-items:center;gap:6px;}
.an-wrap .hbar-lbl i{font-size:.8rem;color:var(--text-muted);width:14px;}
.an-wrap .hbar-track{flex:1;height:9px;background:var(--track);border-radius:6px;overflow:hidden;}
.an-wrap .hbar-fill{height:100%;border-radius:6px;transition:width .6s cubic-bezier(.4,0,.2,1);}
.an-wrap .hbar-val{font-size:.75rem;font-weight:600;color:var(--text-heading);width:46px;text-align:right;flex-shrink:0;}

/* FUNNEL */
.an-wrap .funnel-stage{display:flex;align-items:center;gap:12px;margin-bottom:9px;}
.an-wrap .fn-lbl{font-size:.75rem;color:var(--text-primary);width:135px;flex-shrink:0;}
.an-wrap .fn-bar-wrap{flex:1;background:var(--track);border-radius:6px;height:26px;position:relative;overflow:hidden;}
.an-wrap .fn-bar{height:100%;border-radius:6px;display:flex;align-items:center;padding-left:10px;color:#fff;font-size:.72rem;font-weight:600;transition:width .6s;}
.an-wrap .fn-time{font-size:.72rem;color:var(--text-muted);width:70px;text-align:right;flex-shrink:0;}
@media(max-width:480px){.an-wrap .fn-lbl{width:96px;font-size:.68rem;}.an-wrap .fn-time{width:50px;}}

/* TECH TABLE */
.an-wrap .tech-scroll{overflow-x:auto;-webkit-overflow-scrolling:touch;}
.an-wrap .tech-tbl{width:100%;border-collapse:collapse;min-width:640px;}
.an-wrap .tech-tbl thead th{font-size:.66rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:var(--text-muted);padding:9px 12px;border-bottom:1px solid var(--border-color);text-align:left;white-space:nowrap;background:var(--surface-2);}
.an-wrap .tech-tbl thead th.num{text-align:center;}
.an-wrap .tech-tbl tbody tr{border-bottom:1px solid var(--border-color);transition:background .12s;cursor:pointer;}
.an-wrap .tech-tbl tbody tr:last-child{border-bottom:none;}
.an-wrap .tech-tbl tbody tr:hover{background:var(--table-hover);}
.an-wrap .tech-tbl tbody td{padding:10px 12px;font-size:.78rem;vertical-align:middle;}
.an-wrap .tech-tbl tbody td.num{text-align:center;font-weight:600;color:var(--text-heading);}
.an-wrap .tech-cell{display:flex;align-items:center;gap:9px;}
.an-wrap .tech-av{width:30px;height:30px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:.62rem;font-weight:700;color:#fff;flex-shrink:0;}
.an-wrap .tech-nm{font-weight:600;color:var(--text-heading);white-space:nowrap;}
.an-wrap .tech-dom{font-size:.66rem;color:var(--text-muted);}
.an-wrap .rank-badge{width:22px;height:22px;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;font-size:.7rem;font-weight:700;}
.an-wrap .rank-1{background:rgba(251,188,6,.18);color:#d97706;}
.an-wrap .rank-2{background:rgba(148,163,184,.2);color:#64748b;}
.an-wrap .rank-3{background:rgba(180,120,60,.18);color:#b4783c;}
.an-wrap .rank-n{background:var(--surface-2);color:var(--text-muted);}
.an-wrap .mini-stars{color:#fbbc06;font-size:.7rem;letter-spacing:-1px;}
.an-wrap .mini-stars .dim{color:#d7dbe4;}
[data-bs-theme="dark"] .an-wrap .mini-stars .dim{color:#3a3836;}
.an-wrap .chip{font-size:.66rem;font-weight:600;padding:2px 8px;border-radius:20px;display:inline-block;}
.an-wrap .chip-g{background:rgba(22,163,74,.12);color:#16a34a;}
.an-wrap .chip-a{background:rgba(217,119,6,.14);color:#d97706;}
.an-wrap .chip-r{background:rgba(239,68,68,.12);color:#ef4444;}
.an-wrap .load-pill{display:inline-flex;align-items:center;gap:5px;font-size:.72rem;}
.an-wrap .load-dot{width:7px;height:7px;border-radius:50%;}

/* GAUGE */
.an-wrap .gauge-wrap{display:flex;align-items:center;justify-content:center;flex-direction:column;}
.an-wrap .gauge-caption{font-size:.72rem;color:var(--text-muted);margin-top:4px;text-align:center;}

/* RATING DISTRIBUTION */
.an-wrap .rd-row{display:flex;align-items:center;gap:9px;margin-bottom:8px;}
.an-wrap .rd-star{font-size:.72rem;color:var(--text-primary);width:34px;display:flex;align-items:center;gap:2px;}
.an-wrap .rd-star i{color:#fbbc06;font-size:.7rem;}
.an-wrap .rd-track{flex:1;height:8px;background:var(--track);border-radius:5px;overflow:hidden;}
.an-wrap .rd-fill{height:100%;border-radius:5px;background:linear-gradient(90deg,#fbbc06,#f59e0b);}
.an-wrap .rd-cnt{font-size:.72rem;color:var(--text-muted);width:34px;text-align:right;}

/* ALERT LIST */
.an-wrap .alert-item{display:flex;align-items:center;gap:11px;padding:10px 12px;border:1px solid var(--border-color);border-radius:8px;margin-bottom:8px;background:var(--surface-2);}
.an-wrap .alert-item:last-child{margin-bottom:0;}
.an-wrap .alert-ico{width:32px;height:32px;border-radius:7px;background:rgba(239,68,68,.1);color:#ef4444;display:flex;align-items:center;justify-content:center;font-size:.9rem;flex-shrink:0;}
.an-wrap .alert-body{flex:1;min-width:0;}
.an-wrap .alert-sr{font-size:.76rem;font-weight:700;color:var(--gold);}
.an-wrap .alert-txt{font-size:.7rem;color:var(--text-muted);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
.an-wrap .alert-score{font-size:.72rem;font-weight:700;color:#ef4444;flex-shrink:0;}

/* TOAST */
.an-toast-wrap{position:fixed;bottom:20px;right:20px;z-index:9000;display:flex;flex-direction:column;gap:10px;max-width:calc(100vw - 40px);}
.an-toast-wrap .toast-item{background:var(--card-bg);border:1px solid var(--card-border);border-left:3px solid var(--gold);border-radius:9px;padding:11px 15px;box-shadow:var(--modal-shadow,0 24px 64px rgba(0,0,0,.16));display:flex;align-items:center;gap:10px;min-width:250px;animation:anSlideIn .3s ease;}
@keyframes anSlideIn{from{transform:translateX(30px);opacity:0;}to{transform:none;opacity:1;}}
.an-toast-wrap .toast-item.ok{border-left-color:#16a34a;}
@media(max-width:575.98px){.an-toast-wrap{left:12px;right:12px;bottom:12px;}.an-toast-wrap .toast-item{min-width:0;}}
</style>
@endpush

@section('content')
<div class="an-wrap">

  {{-- PAGE HEADER --}}
  <div class="pg-header">
    <h4 class="pg-hdr-title"><i class="bi bi-bar-chart-line me-2"></i>Analytics &amp; Performance Intelligence</h4>
    <p class="pg-hdr-desc">Executive monitoring station - system throughput, SLA health, technician performance, and financial reconciliation across the service lifecycle. Click any chart segment to drill into the filtered SR view.</p>
    <div class="pg-hdr-meta">
      <span class="meta-badge"><i class="bi bi-shield-fill-check me-1"></i>Super Admin</span>
      <span class="meta-badge"><i class="bi bi-person-gear me-1"></i>Admin</span>
      <span class="meta-badge"><i class="bi bi-person-workspace me-1"></i>Head of Projects (Filtered)</span>
    </div>
  </div>

  {{-- SCOPE BAR --}}
  <div class="scope-bar">
    <div class="filter-group">
      <div class="filter-label">Corporate Scope</div>
      <select class="filter-control" id="scopeSel" onchange="onScopeChange()">
        <option value="global">Global Company View</option>
        <option value="tech">Technician Portfolio Indexes</option>
      </select>
    </div>
    <div class="filter-group">
      <div class="filter-label">Date Range</div>
      <select class="filter-control" id="rangeSel" onchange="refreshAll()">
        <option>Last 30 Days</option>
        <option selected>Last 6 Months</option>
        <option>Year to Date</option>
        <option>Last 12 Months</option>
      </select>
    </div>
    <div class="filter-group">
      <div class="filter-label">Client / Project</div>
      <select class="filter-control" id="clientSel" onchange="refreshAll()">
        <option value="">All Clients</option>
      </select>
    </div>
    <div class="filter-group">
      <div class="filter-label">Warranty Scope</div>
      <select class="filter-control" id="warrSel" onchange="refreshAll()">
        <option value="">All</option>
        <option>In-Warranty</option>
        <option>Out-of-Warranty</option>
      </select>
    </div>
    <div class="filter-actions">
      <button class="btn-ghost" onclick="resetFilters()"><i class="bi bi-arrow-counterclockwise"></i>Reset</button>
      <button class="btn-gold" onclick="showToast('Export','Generating dashboard PDF snapshot…')"><i class="bi bi-download"></i>Export</button>
    </div>
  </div>

  {{-- KPI STRIP --}}
  <div class="kpi-strip" id="kpiStrip"></div>
  <div class="kpi-strip" id="kpiStrip2"></div>

  {{-- SECTION: VOLUME & TRENDS --}}
  <div class="section-title"><i class="bi bi-graph-up-arrow"></i>Ticket Volume &amp; Trends<span class="st-line"></span></div>

  <div class="grid g-3">
    <div class="card">
      <div class="card-hdr">
        <div class="card-hdr-left">
          <div class="card-hdr-ico" style="background:rgba(124,58,237,.1);"><i class="bi bi-activity" style="color:#7c3aed;"></i></div>
          <div><div class="card-title">Monthly Ticket Volume</div><div class="card-sub">Created vs Completed · last 6 months</div></div>
        </div>
      </div>
      <div class="card-body">
        <div id="trendChart"></div>
        <div class="legend">
          <div class="lg-item"><span class="lg-dot" style="background:#7c3aed;"></span>Created</div>
          <div class="lg-item"><span class="lg-dot" style="background:#16a34a;"></span>Completed</div>
        </div>
      </div>
    </div>
    <div class="card">
      <div class="card-hdr">
        <div class="card-hdr-left">
          <div class="card-hdr-ico" style="background:rgba(154,128,83,.12);"><i class="bi bi-pie-chart" style="color:#9a8053;"></i></div>
          <div><div class="card-title">Tickets by Status</div><div class="card-sub">Current lifecycle spread</div></div>
        </div>
      </div>
      <div class="card-body">
        <div id="statusDonut" style="display:flex;justify-content:center;"></div>
        <div class="legend" id="statusLegend"></div>
      </div>
    </div>
  </div>

  <div class="grid g-32">
    <div class="card">
      <div class="card-hdr"><div class="card-hdr-left">
        <div class="card-hdr-ico" style="background:rgba(37,99,235,.1);"><i class="bi bi-shield-check" style="color:#2563eb;"></i></div>
        <div><div class="card-title">Warranty Mix</div><div class="card-sub">Coverage split</div></div>
      </div></div>
      <div class="card-body">
        <div id="warrantyDonut" style="display:flex;justify-content:center;"></div>
        <div class="legend" style="justify-content:center;">
          <div class="lg-item"><span class="lg-dot" style="background:#2563eb;"></span>In-Warranty</div>
          <div class="lg-item"><span class="lg-dot" style="background:#f59e0b;"></span>Out-of-Warranty</div>
        </div>
      </div>
    </div>
    <div class="card">
      <div class="card-hdr"><div class="card-hdr-left">
        <div class="card-hdr-ico" style="background:rgba(16,185,129,.1);"><i class="bi bi-signpost-split" style="color:#10b981;"></i></div>
        <div><div class="card-title">Intake Channel</div><div class="card-sub">Front Desk vs Public</div></div>
      </div></div>
      <div class="card-body">
        <div id="channelDonut" style="display:flex;justify-content:center;"></div>
        <div class="legend" style="justify-content:center;">
          <div class="lg-item"><span class="lg-dot" style="background:#10b981;"></span>Front Desk</div>
          <div class="lg-item"><span class="lg-dot" style="background:#8b5cf6;"></span>Self-Submission</div>
        </div>
      </div>
    </div>
    <div class="card">
      <div class="card-hdr"><div class="card-hdr-left">
        <div class="card-hdr-ico" style="background:rgba(217,119,6,.12);"><i class="bi bi-bar-chart-steps" style="color:#d97706;"></i></div>
        <div><div class="card-title">Top Service Categories</div><div class="card-sub">By ticket volume</div></div>
      </div></div>
      <div class="card-body" id="categoryBars"></div>
    </div>
  </div>

  {{-- SECTION: SLA & LIFECYCLE --}}
  <div class="section-title"><i class="bi bi-stopwatch"></i>SLA &amp; Lifecycle Performance<span class="st-line"></span></div>

  <div class="grid g-2">
    <div class="card">
      <div class="card-hdr"><div class="card-hdr-left">
        <div class="card-hdr-ico" style="background:rgba(124,58,237,.1);"><i class="bi bi-funnel" style="color:#7c3aed;"></i></div>
        <div><div class="card-title">Lifecycle Stage Duration</div><div class="card-sub">Avg time spent per stage - spot the bottleneck</div></div>
      </div></div>
      <div class="card-body" id="funnelChart"></div>
    </div>
    <div class="card">
      <div class="card-hdr"><div class="card-hdr-left">
        <div class="card-hdr-ico" style="background:rgba(239,68,68,.1);"><i class="bi bi-hourglass-split" style="color:#ef4444;"></i></div>
        <div><div class="card-title">SLA Compliance &amp; Aging</div><div class="card-sub">Open-ticket age distribution</div></div>
      </div></div>
      <div class="card-body">
        <div style="display:flex;gap:18px;align-items:center;flex-wrap:wrap;">
          <div class="gauge-wrap">
            <div id="slaGauge"></div>
            <div class="gauge-caption">SLA Compliance</div>
          </div>
          <div style="flex:1;min-width:180px;" id="agingBars"></div>
        </div>
      </div>
    </div>
  </div>

  {{-- SECTION: TECHNICIAN PERFORMANCE --}}
  <div class="section-title"><i class="bi bi-people"></i>Technician / Maintenance Lead Performance<span class="st-line"></span></div>

  <div class="grid g-3">
    <div class="card">
      <div class="card-hdr"><div class="card-hdr-left">
        <div class="card-hdr-ico" style="background:rgba(251,188,6,.14);"><i class="bi bi-trophy" style="color:#d97706;"></i></div>
        <div><div class="card-title">Technician Leaderboard</div><div class="card-sub">Ranked by composite score (rating · SLA · rework)</div></div>
      </div></div>
      <div class="tech-scroll">
        <table class="tech-tbl">
          <thead>
            <tr>
              <th>#</th><th>Technician</th><th class="num">Jobs</th><th class="num">Rating</th>
              <th class="num">Rework</th><th class="num">SLA</th><th class="num">Load</th><th class="num">Avg Cost</th>
            </tr>
          </thead>
          <tbody id="techTbody"></tbody>
        </table>
      </div>
    </div>
    <div class="card">
      <div class="card-hdr"><div class="card-hdr-left">
        <div class="card-hdr-ico" style="background:rgba(16,185,129,.1);"><i class="bi bi-diagram-2" style="color:#10b981;"></i></div>
        <div><div class="card-title">Workload Balance</div><div class="card-sub">Active pipeline per tech</div></div>
      </div></div>
      <div class="card-body" id="workloadBars"></div>
    </div>
  </div>

  <div class="grid g-2">
    <div class="card">
      <div class="card-hdr"><div class="card-hdr-left">
        <div class="card-hdr-ico" style="background:rgba(37,99,235,.1);"><i class="bi bi-bezier2" style="color:#2563eb;"></i></div>
        <div><div class="card-title">Skill Domain - Demand vs Supply</div><div class="card-sub">Open tickets vs available technicians per domain</div></div>
      </div></div>
      <div class="card-body">
        <div id="domainChart"></div>
        <div class="legend" style="margin-top:14px;">
          <div class="lg-item"><span class="lg-dot" style="background:#7c3aed;"></span>Open Ticket Demand</div>
          <div class="lg-item"><span class="lg-dot" style="background:#10b981;"></span>Technician Supply</div>
        </div>
      </div>
    </div>
    <div class="card">
      <div class="card-hdr"><div class="card-hdr-left">
        <div class="card-hdr-ico" style="background:rgba(251,188,6,.14);"><i class="bi bi-star-half" style="color:#d97706;"></i></div>
        <div><div class="card-title">Customer Rating Distribution</div><div class="card-sub">All feedback · portfolio-wide</div></div>
      </div></div>
      <div class="card-body">
        <div style="display:flex;gap:20px;align-items:center;flex-wrap:wrap;">
          <div style="text-align:center;flex-shrink:0;">
            <div class="big-num" style="font-size:2.6rem;color:#d97706;" id="ratingAvg">0.0</div>
            <div class="mini-stars" style="font-size:1rem;" id="ratingStars"></div>
            <div style="font-size:.7rem;color:var(--text-muted);margin-top:3px;" id="ratingCount">0 reviews</div>
          </div>
          <div style="flex:1;min-width:180px;" id="ratingDist"></div>
        </div>
      </div>
    </div>
  </div>

  {{-- SECTION: FINANCIAL --}}
  <div class="section-title" id="finSection"><i class="bi bi-cash-coin"></i>Financial Analytics <span style="font-size:.66rem;font-weight:600;color:var(--text-muted);background:var(--surface-2);padding:2px 8px;border-radius:20px;">SA / Admin</span><span class="st-line"></span></div>

  <div class="grid g-3" id="finGrid1">
    <div class="card">
      <div class="card-hdr"><div class="card-hdr-left">
        <div class="card-hdr-ico" style="background:rgba(22,163,74,.1);"><i class="bi bi-cash-stack" style="color:#16a34a;"></i></div>
        <div><div class="card-title">Expense vs Invoice</div><div class="card-sub">Field cost against billed value (AED) · OoW jobs</div></div>
      </div></div>
      <div class="card-body">
        <div id="finChart"></div>
        <div class="legend">
          <div class="lg-item"><span class="lg-dot" style="background:#ef4444;"></span>Field Expenses</div>
          <div class="lg-item"><span class="lg-dot" style="background:#16a34a;"></span>Invoiced Value</div>
          <div class="lg-item"><span class="lg-dot" style="background:#9a8053;"></span>Gross Margin</div>
        </div>
      </div>
    </div>
    <div class="card">
      <div class="card-hdr"><div class="card-hdr-left">
        <div class="card-hdr-ico" style="background:rgba(217,119,6,.12);"><i class="bi bi-tags" style="color:#d97706;"></i></div>
        <div><div class="card-title">Expense by Category</div><div class="card-sub">Material spend split</div></div>
      </div></div>
      <div class="card-body">
        <div id="expenseDonut" style="display:flex;justify-content:center;"></div>
        <div class="legend" id="expenseLegend" style="justify-content:center;"></div>
      </div>
    </div>
  </div>

  <div class="grid g-32">
    <div class="card">
      <div class="card-hdr"><div class="card-hdr-left">
        <div class="card-hdr-ico" style="background:rgba(16,185,129,.1);"><i class="bi bi-clipboard-data" style="color:#10b981;"></i></div>
        <div><div class="card-title">Reconciliation Status</div><div class="card-sub">Field expense audit</div></div>
      </div></div>
      <div class="card-body" id="reconBars"></div>
    </div>
    <div class="card">
      <div class="card-hdr"><div class="card-hdr-left">
        <div class="card-hdr-ico" style="background:rgba(124,58,237,.1);"><i class="bi bi-arrow-left-right" style="color:#7c3aed;"></i></div>
        <div><div class="card-title">Quote → Approval</div><div class="card-sub">Conversion funnel</div></div>
      </div></div>
      <div class="card-body">
        <div class="gauge-wrap"><div id="convGauge"></div><div class="gauge-caption">Quote acceptance rate</div></div>
      </div>
    </div>
    <div class="card">
      <div class="card-hdr"><div class="card-hdr-left">
        <div class="card-hdr-ico" style="background:rgba(239,68,68,.1);"><i class="bi bi-star" style="color:#ef4444;"></i></div>
        <div><div class="card-title">Low-Rating Follow-ups</div><div class="card-sub">Tickets ≤ 2★</div></div>
      </div></div>
      <div class="card-body" id="lowRatingList"></div>
    </div>
  </div>

  <div class="an-toast-wrap" id="anToastWrap"></div>
</div>
@endsection

@push('scripts')
<script>
/* =========================================================
   Analytics Dashboard - page scripts
   NOTE: DATA is server-provided. Connect to DB later, e.g.:
   var DATA = @json($data ?? []);
   The ?? {} default below keeps every chart from erroring
   while the data is empty. Expected keys (all arrays/objects):
     kpi, kpi2 : [{ico,color,bg,num,lbl,delta,dir}]
     trend     : {created:[], completed:[], months:[]}
     status/warranty/channel : [{lbl,val,color}]
     categories: [{lbl,icon,val,color}]
     funnel    : [{lbl,val,time,color}]
     aging/recon : [{lbl,val,color}]
     techs     : [{nm,dom,jobs,rating,rework,sla,load,cost,color}]
     domains   : [{lbl,demand,supply}]
     ratingDist: [{s,cnt}]
     ratingSummary : {avg, count}
     fin       : {months:[],expense:[],invoice:[]}
     expenseCat: [{lbl,val,color}]
     lowRatings: [{sr,txt,score}]
     slaCompliance, convRate : number
   Role gating: set ROLE='HP' to hide revenue/margin cards.
   ========================================================= */
var SERVER_DATA = @json($data ?? []);

/* Safe defaults so charts never throw on empty data */
var DATA = Object.assign({
  kpi:[], kpi2:[],
  trend:{created:[],completed:[],months:[]},
  status:[], warranty:[], channel:[], categories:[], funnel:[], aging:[],
  techs:[], domains:[], ratingDist:[],
  ratingSummary:{avg:0,count:0},
  fin:{months:[],expense:[],invoice:[]},
  expenseCat:[], recon:[], lowRatings:[],
  slaCompliance:0, convRate:0
}, SERVER_DATA);

var ROLE = (SERVER_DATA.role) || 'SA';

/* ---------- TOAST ---------- */
function showToast(t,b){
  var w=document.getElementById('anToastWrap');
  var d=document.createElement('div');d.className='toast-item ok';
  d.innerHTML='<i class="bi bi-check-circle-fill" style="color:#16a34a;"></i><div><div style="font-size:.8rem;font-weight:600;color:var(--text-heading);">'+t+'</div><div style="font-size:.72rem;color:var(--text-muted);">'+(b||'')+'</div></div>';
  w.appendChild(d);
  setTimeout(function(){d.style.opacity='0';d.style.transition='opacity .3s';setTimeout(function(){d.remove();},300);},2600);
}

/* ---------- HELPERS ---------- */
function esc(t){return document.getElementById(t);}
function polar(cx,cy,r,deg){var a=(deg-90)*Math.PI/180;return[cx+r*Math.cos(a),cy+r*Math.sin(a)];}
function arc(cx,cy,r,start,end){var p1=polar(cx,cy,r,end),p2=polar(cx,cy,r,start);var large=end-start<=180?0:1;return'M '+p1[0]+' '+p1[1]+' A '+r+' '+r+' 0 '+large+' 0 '+p2[0]+' '+p2[1];}
function emptyMsg(el,txt){var e=esc(el);if(e)e.innerHTML='<div style="padding:24px;text-align:center;font-size:.78rem;color:var(--text-muted);"><i class="bi bi-inbox" style="display:block;font-size:1.4rem;margin-bottom:6px;color:var(--text-light);"></i>'+(txt||'No data')+'</div>';}

/* ---------- KPI ---------- */
function renderKPI(){
  function card(k){
    var arrow=k.dir==='up'?'bi-arrow-up':k.dir==='down'?'bi-arrow-down':'bi-dash';
    var cls=k.dir==='up'?'up':k.dir==='down'?'down':'flat';
    return '<div class="kpi-card"><div class="kpi-top"><div class="kpi-ico" style="background:'+k.bg+';color:'+k.color+';"><i class="bi '+k.ico+'"></i></div><span class="kpi-delta '+cls+'"><i class="bi '+arrow+'"></i>'+k.delta+'</span></div><div class="big-num">'+k.num+'</div><div class="kpi-lbl">'+k.lbl+'</div></div>';
  }
  esc('kpiStrip').innerHTML  = DATA.kpi.length  ? DATA.kpi.map(card).join('')  : '';
  esc('kpiStrip2').innerHTML = DATA.kpi2.length ? DATA.kpi2.map(card).join('') : '';
}

/* ---------- LINE / AREA TREND ---------- */
function renderTrend(){
  var d=DATA.trend;
  if(!d.months.length){emptyMsg('trendChart');return;}
  var w=560,h=210,pl=34,pr=14,pt=14,pb=28;
  var iw=w-pl-pr,ih=h-pt-pb;
  var max=Math.max.apply(null,d.created)*1.15||1;
  var n=d.months.length;
  function x(i){return pl+(iw*i/(n-1||1));}
  function y(v){return pt+ih-(ih*v/max);}
  var svg='<svg viewBox="0 0 '+w+' '+h+'" width="100%" style="display:block;">';
  for(var g=0;g<=4;g++){var gy=pt+ih*g/4;svg+='<line class="grid-line" x1="'+pl+'" y1="'+gy+'" x2="'+(w-pr)+'" y2="'+gy+'"/>';svg+='<text class="axis-txt" x="'+(pl-6)+'" y="'+(gy+3)+'" text-anchor="end">'+Math.round(max-max*g/4)+'</text>';}
  d.months.forEach(function(m,i){svg+='<text class="axis-txt" x="'+x(i)+'" y="'+(h-8)+'" text-anchor="middle">'+m+'</text>';});
  function series(arr,color,fill){
    var line='';
    arr.forEach(function(v,i){var px=x(i),py=y(v);line+=(i?'L':'M')+px+' '+py+' ';});
    var area=line+'L'+x(n-1)+' '+(pt+ih)+' L'+pl+' '+(pt+ih)+' Z';
    var s='';
    if(fill){s+='<path d="'+area+'" fill="'+color+'" opacity="0.10"/>';}
    s+='<path d="'+line+'" fill="none" stroke="'+color+'" stroke-width="2.5" stroke-linejoin="round" stroke-linecap="round"/>';
    arr.forEach(function(v,i){s+='<circle cx="'+x(i)+'" cy="'+y(v)+'" r="3.4" fill="var(--card-bg)" stroke="'+color+'" stroke-width="2"/>';});
    return s;
  }
  svg+=series(d.created,'#7c3aed',true);
  svg+=series(d.completed,'#16a34a',false);
  svg+='</svg>';
  esc('trendChart').innerHTML=svg;
}

/* ---------- DONUT ---------- */
function renderDonut(elId,legendId,data,centerLbl){
  if(!data||!data.length){emptyMsg(elId);if(legendId)esc(legendId).innerHTML='';return;}
  var total=data.reduce(function(a,b){return a+b.val;},0)||1;
  var cx=90,cy=90,r=68,sw=22;
  var svg='<svg viewBox="0 0 180 180" width="170" height="170">';
  var start=0;
  data.forEach(function(seg){
    var sweep=seg.val/total*360;var end=start+sweep;
    if(sweep>=359.9){
      svg+='<circle cx="'+cx+'" cy="'+cy+'" r="'+r+'" fill="none" stroke="'+seg.color+'" stroke-width="'+sw+'" style="cursor:pointer;" onclick="drill(\''+seg.lbl+'\')"><title>'+seg.lbl+': '+seg.val+'</title></circle>';
    } else if(sweep>0.5){
      svg+='<path d="'+arc(cx,cy,r,start,end-0.6)+'" fill="none" stroke="'+seg.color+'" stroke-width="'+sw+'" stroke-linecap="butt" style="cursor:pointer;" onclick="drill(\''+seg.lbl+'\')"><title>'+seg.lbl+': '+seg.val+'</title></path>';
    }
    start=end;
  });
  svg+='<text class="donut-center-num" x="90" y="88" text-anchor="middle">'+total.toLocaleString()+'</text>';
  svg+='<text class="donut-center-lbl" x="90" y="104" text-anchor="middle">'+(centerLbl||'Total')+'</text>';
  svg+='</svg>';
  esc(elId).innerHTML=svg;
  if(legendId){
    esc(legendId).innerHTML=data.map(function(s){var pct=Math.round(s.val/total*100);return '<div class="lg-item"><span class="lg-dot" style="background:'+s.color+';"></span>'+s.lbl+' <span style="color:var(--text-light);">'+pct+'%</span></div>';}).join('');
  }
}

/* ---------- HORIZONTAL BARS ---------- */
function renderHBars(elId,data,unit,showIcon){
  if(!data||!data.length){emptyMsg(elId);return;}
  var max=Math.max.apply(null,data.map(function(d){return d.val;}))||1;
  esc(elId).innerHTML=data.map(function(d){
    var pct=d.val/max*100;
    return '<div class="hbar-row"><div class="hbar-lbl">'+(showIcon&&d.icon?'<i class="bi '+d.icon+'"></i>':'')+d.lbl+'</div><div class="hbar-track"><div class="hbar-fill" style="width:'+pct+'%;background:'+(d.color||'#9a8053')+';"></div></div><div class="hbar-val">'+(unit||'')+d.val+'</div></div>';
  }).join('');
}

/* ---------- FUNNEL ---------- */
function renderFunnel(){
  if(!DATA.funnel.length){emptyMsg('funnelChart');return;}
  var max=Math.max.apply(null,DATA.funnel.map(function(f){return f.val;}))||1;
  esc('funnelChart').innerHTML=DATA.funnel.map(function(f){
    var pct=f.val/max*100;
    return '<div class="funnel-stage"><div class="fn-lbl">'+f.lbl+'</div><div class="fn-bar-wrap"><div class="fn-bar" style="width:'+pct+'%;background:'+f.color+';">'+f.val+'%</div></div><div class="fn-time">'+f.time+'</div></div>';
  }).join('');
}

/* ---------- GAUGE (semi) ---------- */
function renderGauge(elId,pct,color){
  pct=pct||0;
  var cx=80,cy=80,r=60;
  var svg='<svg viewBox="0 0 160 96" width="150" height="90">';
  svg+='<path d="'+arc(cx,cy,r,180,360)+'" fill="none" stroke="var(--track)" stroke-width="14" stroke-linecap="round"/>';
  var end=180+(pct/100*180);
  if(pct>0)svg+='<path d="'+arc(cx,cy,r,180,end)+'" fill="none" stroke="'+color+'" stroke-width="14" stroke-linecap="round"/>';
  svg+='<text x="80" y="74" text-anchor="middle" style="font-size:1.5rem;font-weight:700;fill:var(--text-heading);">'+pct+'%</text>';
  svg+='</svg>';
  esc(elId).innerHTML=svg;
}

/* ---------- TECH TABLE ---------- */
function stars(r){var full=Math.round(r);var s='';for(var i=1;i<=5;i++){s+=i<=full?'★':'<span class="dim">★</span>';}return '<span class="mini-stars">'+s+'</span>';}
function renderTechTable(){
  if(!DATA.techs.length){esc('techTbody').innerHTML='<tr><td colspan="8" style="text-align:center;padding:24px;color:var(--text-muted);">No technician data</td></tr>';return;}
  esc('techTbody').innerHTML=DATA.techs.map(function(t,i){
    var rank=i<3?'rank-'+(i+1):'rank-n';
    var reworkChip=t.rework<5?'chip-g':t.rework<8?'chip-a':'chip-r';
    var slaChip=t.sla>=90?'chip-g':t.sla>=85?'chip-a':'chip-r';
    var loadColor=t.load<=4?'#16a34a':t.load<=6?'#d97706':'#ef4444';
    var init=t.nm.split(' ').map(function(w){return w[0];}).join('');
    return '<tr onclick="drill(\''+t.nm+'\')">'
      +'<td><span class="rank-badge '+rank+'">'+(i+1)+'</span></td>'
      +'<td><div class="tech-cell"><div class="tech-av" style="background:linear-gradient(135deg,'+t.color+','+t.color+'bb);">'+init+'</div><div><div class="tech-nm">'+t.nm+'</div><div class="tech-dom">'+t.dom+'</div></div></div></td>'
      +'<td class="num">'+t.jobs+'</td>'
      +'<td class="num">'+stars(t.rating)+'<div style="font-size:.66rem;color:var(--text-muted);">'+Number(t.rating).toFixed(1)+'</div></td>'
      +'<td class="num"><span class="chip '+reworkChip+'">'+t.rework+'%</span></td>'
      +'<td class="num"><span class="chip '+slaChip+'">'+t.sla+'%</span></td>'
      +'<td class="num"><span class="load-pill"><span class="load-dot" style="background:'+loadColor+';"></span>'+t.load+'</span></td>'
      +'<td class="num">AED '+t.cost+'</td>'
      +'</tr>';
  }).join('');
}

/* ---------- WORKLOAD BARS ---------- */
function renderWorkload(){
  if(!DATA.techs.length){emptyMsg('workloadBars');return;}
  var data=DATA.techs.map(function(t){return{lbl:t.nm.split(' ')[0],val:t.load,color:t.load<=4?'#16a34a':t.load<=6?'#d97706':'#ef4444'};});
  renderHBars('workloadBars',data,'');
}

/* ---------- DOMAIN DEMAND vs SUPPLY ---------- */
function renderDomainChart(){
  var d=DATA.domains;
  if(!d.length){emptyMsg('domainChart');return;}
  var w=560,h=210,pl=34,pr=14,pt=14,pb=30;
  var iw=w-pl-pr,ih=h-pt-pb;
  var n=d.length;
  var max=Math.max.apply(null,d.map(function(x){return x.demand;}))*1.15||1;
  var grp=iw/n,bw=grp*0.28;
  function y(v){return pt+ih-(ih*v/max);}
  var svg='<svg viewBox="0 0 '+w+' '+h+'" width="100%" style="display:block;">';
  for(var g=0;g<=4;g++){var gy=pt+ih*g/4;svg+='<line class="grid-line" x1="'+pl+'" y1="'+gy+'" x2="'+(w-pr)+'" y2="'+gy+'"/>';svg+='<text class="axis-txt" x="'+(pl-6)+'" y="'+(gy+3)+'" text-anchor="end">'+Math.round(max-max*g/4)+'</text>';}
  d.forEach(function(x,i){
    var gx=pl+grp*i+grp/2;
    var x1=gx-bw-3,x2=gx+3;
    svg+='<rect x="'+x1+'" y="'+y(x.demand)+'" width="'+bw+'" height="'+(pt+ih-y(x.demand))+'" rx="3" fill="#7c3aed" style="cursor:pointer;"><title>'+x.lbl+' demand: '+x.demand+'</title></rect>';
    svg+='<rect x="'+x2+'" y="'+y(x.supply)+'" width="'+bw+'" height="'+(pt+ih-y(x.supply))+'" rx="3" fill="#10b981"><title>'+x.lbl+' supply: '+x.supply+'</title></rect>';
    svg+='<text class="axis-txt" x="'+gx+'" y="'+(h-9)+'" text-anchor="middle">'+x.lbl.replace(' & Systems','').replace(' & Safety','')+'</text>';
  });
  svg+='</svg>';
  esc('domainChart').innerHTML=svg;
}

/* ---------- RATING DISTRIBUTION ---------- */
function renderRatingDist(){
  var rs=DATA.ratingSummary||{avg:0,count:0};
  esc('ratingAvg').textContent=Number(rs.avg||0).toFixed(1);
  esc('ratingCount').textContent=(rs.count||0)+' reviews';
  esc('ratingStars').innerHTML=stars(rs.avg||0).replace('mini-stars','mini-stars');
  if(!DATA.ratingDist.length){emptyMsg('ratingDist');return;}
  var max=Math.max.apply(null,DATA.ratingDist.map(function(r){return r.cnt;}))||1;
  esc('ratingDist').innerHTML=DATA.ratingDist.map(function(r){
    var pct=r.cnt/max*100;
    return '<div class="rd-row"><div class="rd-star">'+r.s+'<i class="bi bi-star-fill"></i></div><div class="rd-track"><div class="rd-fill" style="width:'+pct+'%;"></div></div><div class="rd-cnt">'+r.cnt+'</div></div>';
  }).join('');
}

/* ---------- AGING ---------- */
function renderAging(){renderHBars('agingBars',DATA.aging,'');}

/* ---------- FINANCIAL grouped bars ---------- */
function renderFinChart(){
  var d=DATA.fin;
  if(!d.months.length){emptyMsg('finChart');return;}
  var w=560,h=210,pl=38,pr=14,pt=14,pb=28;
  var iw=w-pl-pr,ih=h-pt-pb;
  var n=d.months.length;
  var max=Math.max.apply(null,d.invoice)*1.15||1;
  var grp=iw/n,bw=grp*0.24;
  function y(v){return pt+ih-(ih*v/max);}
  var svg='<svg viewBox="0 0 '+w+' '+h+'" width="100%" style="display:block;">';
  for(var g=0;g<=4;g++){var gy=pt+ih*g/4;svg+='<line class="grid-line" x1="'+pl+'" y1="'+gy+'" x2="'+(w-pr)+'" y2="'+gy+'"/>';svg+='<text class="axis-txt" x="'+(pl-6)+'" y="'+(gy+3)+'" text-anchor="end">'+Math.round(max-max*g/4)+'k</text>';}
  d.months.forEach(function(m,i){
    var gx=pl+grp*i+grp/2;
    var exp=d.expense[i],inv=d.invoice[i];
    svg+='<rect x="'+(gx-bw-2)+'" y="'+y(exp)+'" width="'+bw+'" height="'+(pt+ih-y(exp))+'" rx="3" fill="#ef4444"><title>Expense: AED '+exp+'k</title></rect>';
    svg+='<rect x="'+(gx+2)+'" y="'+y(inv)+'" width="'+bw+'" height="'+(pt+ih-y(inv))+'" rx="3" fill="#16a34a"><title>Invoice: AED '+inv+'k</title></rect>';
    svg+='<text class="axis-txt" x="'+gx+'" y="'+(h-8)+'" text-anchor="middle">'+m+'</text>';
  });
  var ln='';d.months.forEach(function(m,i){var gx=pl+grp*i+grp/2;var mgn=d.invoice[i]-d.expense[i];ln+=(i?'L':'M')+gx+' '+y(mgn)+' ';});
  svg+='<path d="'+ln+'" fill="none" stroke="#9a8053" stroke-width="2" stroke-dasharray="4 3"/>';
  d.months.forEach(function(m,i){var gx=pl+grp*i+grp/2;var mgn=d.invoice[i]-d.expense[i];svg+='<circle cx="'+gx+'" cy="'+y(mgn)+'" r="3" fill="#9a8053"/>';});
  svg+='</svg>';
  esc('finChart').innerHTML=svg;
}

/* ---------- RECON + EXPENSE ---------- */
function renderRecon(){renderHBars('reconBars',DATA.recon,'',false);}
function renderExpenseDonut(){
  renderDonut('expenseDonut','expenseLegend',DATA.expenseCat.map(function(e){return{lbl:e.lbl,val:e.val,color:e.color};}),'AED');
}
function renderLowRatings(){
  if(!DATA.lowRatings.length){emptyMsg('lowRatingList','No low-rating tickets');return;}
  esc('lowRatingList').innerHTML=DATA.lowRatings.map(function(l){
    return '<div class="alert-item"><div class="alert-ico"><i class="bi bi-star-fill"></i></div><div class="alert-body"><div class="alert-sr">'+l.sr+'</div><div class="alert-txt">'+l.txt+'</div></div><div class="alert-score">'+l.score+'★</div></div>';
  }).join('');
}

/* ---------- DRILL / FILTERS ---------- */
function drill(seg){showToast('Drill-down','Opening SR Explorer filtered by: '+seg);}
function onScopeChange(){
  var v=esc('scopeSel').value;
  showToast('Scope',v==='global'?'Global Company View':'Technician Portfolio Indexes');
  refreshAll();
}
function refreshAll(){renderAll();}
function resetFilters(){esc('scopeSel').value='global';esc('rangeSel').value='Last 6 Months';esc('clientSel').value='';esc('warrSel').value='';showToast('Reset','Filters cleared');renderAll();}

/* ---------- ROLE GATING ---------- */
function applyRoleGating(){
  var hide=(ROLE==='HP');
  var fs=esc('finSection'),fg=esc('finGrid1');
  if(fs)fs.style.display=hide?'none':'';
  if(fg)fg.style.display=hide?'none':'';
}

/* ---------- RENDER ALL ---------- */
function renderAll(){
  renderKPI();
  renderTrend();
  renderDonut('statusDonut','statusLegend',DATA.status,'Tickets');
  renderDonut('warrantyDonut',null,DATA.warranty,'Tickets');
  renderDonut('channelDonut',null,DATA.channel,'Tickets');
  renderHBars('categoryBars',DATA.categories,'',true);
  renderFunnel();
  renderGauge('slaGauge',DATA.slaCompliance,'#16a34a');
  renderAging();
  renderTechTable();
  renderWorkload();
  renderDomainChart();
  renderRatingDist();
  renderFinChart();
  renderExpenseDonut();
  renderRecon();
  renderGauge('convGauge',DATA.convRate,'#7c3aed');
  renderLowRatings();
  applyRoleGating();
}

document.addEventListener('DOMContentLoaded', function(){
  renderAll();
});
</script>
@endpush