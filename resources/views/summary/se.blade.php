<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0"/>
<title>Service Engineer · Previous Day Summary · Matter Mind SR Portal</title>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<link rel="icon" type="image/png" href="{{ asset('assets/images/favicon.png') }}">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
html{overflow-x:hidden;}
body{font-family:'DM Sans',system-ui,sans-serif;background:#f2f4f2;color:#1a1614;
  -webkit-font-smoothing:antialiased;min-height:100vh;overflow-x:hidden;
  font-size:clamp(14px,.9vw + 11px,16px);}

/* ── MATTERMIND BRAND PALETTE ── */
:root{
  --mm-gold:#9A7B4F;
  --mm-gold-light:#C4A882;
  --mm-gold-dark:#7A6140;
  --mm-ink:#1a1614;
}

/* ── HEADER ── */
.hdr{background:linear-gradient(135deg,var(--mm-gold) 0%,var(--mm-gold-dark) 100%);
  padding:clamp(18px,4vw,28px) clamp(16px,4vw,28px) clamp(20px,4vw,30px);position:relative;overflow:hidden;}
.hdr::before{content:'';position:absolute;right:-60px;top:-60px;width:220px;height:220px;
  border-radius:50%;background:rgba(255,255,255,.08);}
.hdr::after{content:'';position:absolute;left:-40px;bottom:-50px;width:180px;height:180px;
  border-radius:50%;background:rgba(255,255,255,.05);}
.hdr-inner{position:relative;z-index:1;display:flex;align-items:flex-start;
  justify-content:space-between;gap:12px;flex-wrap:wrap;}
.hdr-left{min-width:0;flex:1 1 220px;}
.hdr-eyebrow{font-size:.62rem;font-weight:800;text-transform:uppercase;
  letter-spacing:.13em;color:rgba(255,255,255,.65);margin-bottom:5px;}
.hdr-title{font-size:clamp(1.05rem,3.6vw,1.5rem);font-weight:900;color:#fff;line-height:1.2;margin-bottom:6px;}
.hdr-date{font-size:.78rem;color:rgba(255,255,255,.85);font-weight:500;}
.hdr-right{display:flex;flex-direction:column;align-items:flex-end;gap:6px;flex-shrink:0;}
.role-pill{font-size:.68rem;font-weight:800;padding:4px 12px;border-radius:20px;
  background:rgba(255,255,255,.18);color:#fff;border:1px solid rgba(255,255,255,.32);white-space:nowrap;}
.hdr-time{font-size:.64rem;color:rgba(255,255,255,.7);white-space:nowrap;}

/* QUICK PILLS */
.quick-bar{border-top:1px solid rgba(255,255,255,.15);
  padding:10px clamp(16px,4vw,28px);display:flex;gap:8px;flex-wrap:wrap;position:relative;z-index:1;}
.qb-pill{font-size:.67rem;font-weight:700;padding:4px 10px;border-radius:20px;
  display:flex;align-items:center;gap:4px;white-space:nowrap;}
.qp-green{background:#edf8f2;color:#1a6b47;}
.qp-gold {background:#fdf5e8;color:var(--mm-gold-dark);}
.qp-red  {background:#fff0f0; color:#8a2020;}
.qp-amb  {background:#fff8e8; color:#8a6020;}
.qp-blue {background:#eff6ff; color:#1d4ed8;}

/* ── BODY ── */
.body{padding:14px;max-width:1240px;margin:0 auto;}
@media(min-width:600px){.body{padding:18px;}}
@media(min-width:900px){.body{padding:22px 18px;}}
@media(min-width:1400px){.body{max-width:1800px;padding:28px 22px;}}

/* ── KPI STRIP ── */
.kpi-strip{display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:12px;}
@media(min-width:340px) and (max-width:499px){.kpi-strip{grid-template-columns:1fr 1fr;}}
@media(min-width:500px){.kpi-strip{grid-template-columns:repeat(3,1fr);}}
@media(min-width:800px){.kpi-strip{grid-template-columns:repeat(6,1fr);}}

.kpi{background:#fff;border-radius:12px;padding:14px 14px 12px;
  box-shadow:0 1px 6px rgba(0,0,0,.07);border:1px solid #e8e8e4;
  cursor:pointer;transition:all .18s;position:relative;overflow:hidden;}
.kpi:hover{box-shadow:0 4px 16px rgba(0,0,0,.1);transform:translateY(-2px);}
.kpi::before{content:'';position:absolute;top:0;left:0;right:0;height:3px;border-radius:12px 12px 0 0;}
.kpi.k-gold::before{background:linear-gradient(90deg,var(--mm-gold),var(--mm-gold-light));}
.kpi.k-green::before{background:linear-gradient(90deg,#2d6b4a,#4a8a6a);}
.kpi.k-blue::before{background:linear-gradient(90deg,#1d4ed8,#3b82f6);}
.kpi.k-red::before{background:linear-gradient(90deg,#b91c1c,#ef4444);}
.kpi.k-purp::before{background:linear-gradient(90deg,#6d28d9,#8b5cf6);}
.kpi.k-amb::before{background:linear-gradient(90deg,#b45309,#f59e0b);}
.kpi-ico{width:30px;height:30px;border-radius:8px;display:flex;align-items:center;
  justify-content:center;font-size:.85rem;margin-bottom:8px;}
.ki-gold{background:#fdf8f0;} .ki-green{background:#edf8f2;}
.ki-blue{background:#eff6ff;} .ki-red{background:#fff0f0;} .ki-purp{background:#f5f3ff;} .ki-amb{background:#fff8e8;}
.kpi-val{font-size:clamp(1.5rem,4.5vw,1.9rem);font-weight:900;line-height:1;margin-bottom:2px;}
.kv-gold{color:var(--mm-gold);} .kv-green{color:#2d6b4a;}
.kv-blue{color:#1d4ed8;} .kv-red{color:#b91c1c;} .kv-purp{color:#6d28d9;} .kv-amb{color:#b45309;}
.kpi-lbl{font-size:.66rem;font-weight:700;color:#8a9890;line-height:1.3;}
.kpi-sub{font-size:.6rem;color:#b0bab8;margin-top:2px;}
.kpi-more{font-size:.6rem;font-weight:700;color:var(--mm-gold);margin-top:6px;
  display:flex;align-items:center;gap:3px;opacity:.75;}

/* ── CARDS ── */
.grid-2{display:grid;grid-template-columns:1fr;gap:12px;margin-bottom:12px;}
@media(min-width:640px){.grid-2{grid-template-columns:1fr 1fr;}}
.grid-3{display:grid;grid-template-columns:1fr;gap:12px;margin-bottom:12px;}
@media(min-width:640px){.grid-3{grid-template-columns:1fr 1fr;}}
@media(min-width:900px){.grid-3{grid-template-columns:1fr 1fr 1fr;}}

.card{background:#fff;border-radius:14px;padding:clamp(14px,3vw,18px);
  box-shadow:0 1px 8px rgba(0,0,0,.06);border:1px solid #e8e8e4;
  cursor:pointer;transition:all .18s;min-width:0;}
.card:hover{box-shadow:0 6px 22px rgba(0,0,0,.1);transform:translateY(-1px);}
.card-top{display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:14px;gap:8px;}
.card-title{font-size:.72rem;font-weight:800;color:#6a7a70;text-transform:uppercase;letter-spacing:.08em;}
.card-more-btn{font-size:.65rem;font-weight:700;color:var(--mm-gold);display:flex;align-items:center;gap:3px;opacity:.85;white-space:nowrap;}
.card-big{font-size:clamp(1.9rem,5.5vw,2.6rem);font-weight:900;line-height:1;}

/* Chart containers */
.ch{position:relative;}
.ch canvas{position:absolute;inset:0;width:100%!important;height:100%!important;}
.ch-180{height:180px;}
.ch-160{height:160px;}
.ch-140{height:140px;}
.ch-120{height:120px;}

/* Donut center label */
.donut-wrap{position:relative;display:flex;align-items:center;justify-content:center;}
.donut-center{position:absolute;text-align:center;pointer-events:none;}
.dc-num{font-size:1.6rem;font-weight:900;color:#1a1614;line-height:1;}
.dc-lbl{font-size:.58rem;font-weight:700;color:#8a9890;text-transform:uppercase;letter-spacing:.06em;}

/* Legend */
.legend{display:flex;flex-wrap:wrap;gap:10px;margin-top:12px;}
.leg-item{display:flex;align-items:center;gap:5px;font-size:.68rem;color:#6a7a70;font-weight:600;}
.leg-dot{width:9px;height:9px;border-radius:50%;flex-shrink:0;}

/* Status rows */
.stat-rows{display:flex;flex-direction:column;gap:5px;margin-top:4px;}
.stat-row{display:flex;align-items:center;gap:8px;}
.sr-label{font-size:.7rem;font-weight:600;color:#3a4a40;width:130px;flex-shrink:0;}
.sr-bar-wrap{flex:1;}
.sr-bar{height:6px;border-radius:3px;transition:width .5s;}
.sr-num{font-size:.72rem;font-weight:800;width:28px;text-align:right;flex-shrink:0;}

/* List rows (rework / in-field) */
.item-list{display:flex;flex-direction:column;gap:6px;margin-top:6px;}
.list-item{display:flex;align-items:flex-start;gap:7px;padding:7px 9px;
  background:#fafaf8;border-radius:8px;border:1px solid #eee;}
.li-dot{width:6px;height:6px;border-radius:50%;flex-shrink:0;margin-top:5px;}
.li-body{flex:1;min-width:0;}
.li-sr{font-size:.7rem;font-weight:700;color:#1a1614;word-break:break-word;}
.li-reason{font-size:.63rem;color:#8a9890;line-height:1.4;}

/* QC boxes */
.add-boxes{display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-top:8px;}
.add-box{background:#f2faf5;border:1px solid #b8dcc8;border-radius:10px;
  padding:12px;text-align:center;}
.add-box.red{background:#fff5f5;border-color:#f3c9c9;}
.ab-num{font-size:2rem;font-weight:900;color:#2d6b4a;line-height:1;}
.add-box.red .ab-num{color:#b91c1c;}
.ab-lbl{font-size:.65rem;font-weight:700;color:#5a7060;margin-top:2px;}
.add-box.red .ab-lbl{color:#8a2020;}

/* ── BOTTOM SHEET ── */
.sheet-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);
  z-index:800;backdrop-filter:blur(3px);}
.sheet-overlay.open{display:block;}
.bottom-sheet{position:fixed;left:0;right:0;bottom:-100%;background:#fff;
  z-index:900;border-radius:20px 20px 0 0;
  max-height:80vh;display:flex;flex-direction:column;
  box-shadow:0 -4px 30px rgba(0,0,0,.15);transition:bottom .35s cubic-bezier(.4,0,.2,1);}
.bottom-sheet.open{bottom:0;}
.bs-handle{width:40px;height:4px;background:#e0e0dc;border-radius:2px;margin:10px auto 0;}
.bs-hdr{padding:14px 20px 12px;border-bottom:1px solid #f0f0ec;
  display:flex;align-items:center;justify-content:space-between;flex-shrink:0;gap:10px;}
.bs-title{font-size:.9rem;font-weight:800;color:#1a1614;}
.bs-close{width:28px;height:28px;border-radius:7px;background:#f5f5f0;flex-shrink:0;
  border:none;cursor:pointer;font-size:.8rem;display:flex;align-items:center;justify-content:center;}
.bs-body{flex:1;overflow-y:auto;overflow-x:auto;padding:14px 20px 24px;}
.bs-body::-webkit-scrollbar{width:3px;}
.bs-body::-webkit-scrollbar-thumb{background:#e0e0dc;border-radius:2px;}

/* Detail table */
.detail-table{width:100%;min-width:480px;border-collapse:collapse;font-size:.78rem;}
.detail-table th{text-align:left;padding:7px 10px;font-size:.65rem;font-weight:800;
  text-transform:uppercase;letter-spacing:.07em;color:#9a9890;
  border-bottom:1px solid #f0f0ec;white-space:nowrap;}
.detail-table td{padding:8px 10px;border-bottom:1px solid #f8f8f5;color:#1a1614;vertical-align:middle;}
.detail-table tr:last-child td{border-bottom:none;}
.detail-table tr:hover td{background:#fafaf8;}
.pill-s{font-size:.62rem;font-weight:700;padding:2px 7px;border-radius:10px;white-space:nowrap;}
.ps-green{background:#edf8f2;color:#2d6b4a;}
.ps-amber{background:#fff8e8;color:#8a6020;}
.ps-red  {background:#fff0f0;color:#8a2020;}
.ps-blue {background:#eff6ff;color:#1d4ed8;}
.ps-purp {background:#f5f3ff;color:#6d28d9;}

/* CTA */
.cta-wrap{padding:14px 14px 28px;max-width:1240px;margin:0 auto;}
@media(min-width:1400px){.cta-wrap{max-width:1320px;}}
.cta-btn{display:flex;align-items:center;justify-content:center;gap:8px;
  background:linear-gradient(135deg,var(--mm-gold),var(--mm-gold-light));color:#fff;
  padding:14px 24px;border-radius:12px;font-size:.88rem;font-weight:800;
  text-decoration:none;border:none;cursor:pointer;width:100%;max-width:520px;margin:0 auto;
  box-shadow:0 4px 16px rgba(154,123,79,.3);transition:opacity .15s;}
.cta-btn:hover{opacity:.88;}

/* Footer */
.footer{text-align:center;padding:10px 16px 20px;font-size:.62rem;color:#9a9890;}

/* ── FINE-TUNING FOR VERY SMALL / VERY NARROW SCREENS ── */
@media(max-width:359px){
  .kpi-strip{grid-template-columns:1fr 1fr;}
  .hdr{padding:16px 14px 20px;}
  .body{padding:10px;}
}
</style>
</head>
<body>

<!-- HEADER -->
<div class="hdr">
  <div class="hdr-inner">
    <div class="hdr-left">
      <div class="hdr-eyebrow">Matter Mind SR Portal</div>
      <div class="hdr-title">Previous Day Summary</div>
      <div class="hdr-date">{{ $summaryDate->format('l, F j, Y') }}</div>
    </div>
    <div class="hdr-right">
      <div class="role-pill">⬡ {{ $user->role->name ?? 'Service Engineer' }}</div>
      <div class="hdr-time">Generated at {{ now()->format('h:i A') }}</div>
    </div>
  </div>
  <div class="quick-bar">
  @if($data['enabled']['se_assigned'] ?? true)
    <div class="qb-pill qp-gold">📋 {{ $data['quick']['assigned'] }} Assigned</div>
    <div class="qb-pill qp-green">🚀 {{ $data['quick']['dispatched'] }} Dispatched</div>
    <div class="qb-pill qp-amb">⏳ {{ $data['quick']['pending'] }} Pending</div>
  @endif
  @if($data['enabled']['se_rework'] ?? true)
    <div class="qb-pill qp-red">🔄 {{ $data['quick']['rework'] }} Rework</div>
  @endif
  @if($data['enabled']['se_in_field'] ?? true)
    <div class="qb-pill qp-blue">📡 {{ $data['quick']['in_field'] }} In Field</div>
  @endif
</div>
</div>

<!-- BODY -->
<div class="body">

  <!-- KPI STRIP -->
  <div class="kpi-strip">
  @if($data['enabled']['se_assigned'] ?? true)
    <div class="kpi k-gold" onclick="openSheet('assigned')">
      <div class="kpi-ico ki-gold">📋</div>
      <div class="kpi-val kv-gold">{{ $data['kpis']['assigned']['value'] }}</div>
      <div class="kpi-lbl">Assigned</div>
      <div class="kpi-sub">{{ $data['kpis']['assigned']['sub'] }}</div>
      <div class="kpi-more">View list ›</div>
    </div>
    <div class="kpi k-green" onclick="openSheet('assigned')">
      <div class="kpi-ico ki-green">🚀</div>
      <div class="kpi-val kv-green">{{ $data['kpis']['dispatched']['value'] }}</div>
      <div class="kpi-lbl">Dispatched</div>
      <div class="kpi-sub">{{ $data['kpis']['dispatched']['sub'] }}</div>
      <div class="kpi-more">View list ›</div>
    </div>
    <div class="kpi k-amb" onclick="openSheet('assigned')">
      <div class="kpi-ico ki-amb">⏳</div>
      <div class="kpi-val kv-amb">{{ $data['kpis']['pending']['value'] }}</div>
      <div class="kpi-lbl">Pending Dispatch</div>
      <div class="kpi-sub">{{ $data['kpis']['pending']['sub'] }}</div>
      <div class="kpi-more">View list ›</div>
    </div>
  @endif
  @if($data['enabled']['se_rework'] ?? true)
    <div class="kpi k-purp" onclick="openSheet('rework')">
      <div class="kpi-ico ki-purp">🔄</div>
      <div class="kpi-val kv-purp">{{ $data['kpis']['rework']['value'] }}</div>
      <div class="kpi-lbl">Rework Queue</div>
      <div class="kpi-sub">{{ $data['kpis']['rework']['sub'] }}</div>
      <div class="kpi-more">View details ›</div>
    </div>
  @endif
  @if($data['enabled']['se_avg_dispatch'] ?? true)
    <div class="kpi k-blue">
      <div class="kpi-ico ki-blue">⏱</div>
      <div class="kpi-val kv-blue">{{ $data['kpis']['avg_time']['value'] }}</div>
      <div class="kpi-lbl">Avg Dispatch Time</div>
      <div class="kpi-sub">{{ $data['kpis']['avg_time']['sub'] }}</div>
    </div>
  @endif
  @if($data['enabled']['se_in_field'] ?? true)
    <div class="kpi k-red" onclick="openSheet('in_field')">
      <div class="kpi-ico ki-red">📡</div>
      <div class="kpi-val kv-red">{{ $data['kpis']['in_field']['value'] }}</div>
      <div class="kpi-lbl">Currently In Field</div>
      <div class="kpi-sub">{{ $data['kpis']['in_field']['sub'] }}</div>
      <div class="kpi-more">View list ›</div>
    </div>
  @endif
  </div>

  <!-- ROW 1: Assigned Split + QC (if enabled) -->
  <div class="grid-2">

  @if($data['enabled']['se_assigned'] ?? true)
    <div class="card" onclick="openSheet('assigned')">
      <div class="card-top">
        <div class="card-title">SRs Assigned - Dispatch Split</div>
        <div class="card-more-btn">View all ›</div>
      </div>
      <div class="donut-wrap" style="height:180px;">
        <canvas id="dispatchDonut"></canvas>
        <div class="donut-center">
          <div class="dc-num">{{ $data['kpis']['assigned']['value'] }}</div>
          <div class="dc-lbl">Total</div>
        </div>
      </div>
      <div class="legend">
        <div class="leg-item"><div class="leg-dot" style="background:#4a8a6a;"></div>Dispatched - {{ $data['kpis']['dispatched']['value'] }}</div>
        <div class="leg-item"><div class="leg-dot" style="background:#e8a030;"></div>Pending - {{ $data['kpis']['pending']['value'] }}</div>
      </div>
    </div>
  @endif

  @if(($data['enabled']['se_qc'] ?? true) && $data['qc_enabled'])
    <div class="card">
      <div class="card-top">
        <div class="card-title">QC Reviews Done Yesterday</div>
      </div>
      <div class="add-boxes">
        <div class="add-box">
          <div class="ab-num">{{ $data['qc']['approved'] }}</div>
          <div class="ab-lbl">Approved</div>
        </div>
        <div class="add-box red">
          <div class="ab-num">{{ $data['qc']['rejected'] }}</div>
          <div class="ab-lbl">Rejected</div>
        </div>
      </div>
      <div class="ch ch-140" style="margin-top:14px;"><canvas id="qcBar"></canvas></div>
    </div>
  @elseif($data['enabled']['se_in_field'] ?? true)
    <div class="card" onclick="openSheet('in_field')">
      <div class="card-top">
        <div class="card-title">Currently In Field</div>
        <div class="card-more-btn">View all ›</div>
      </div>
      <div class="card-big" style="color:#b91c1c;margin-bottom:12px;">{{ $data['kpis']['in_field']['value'] }}</div>
      <div class="item-list">
        @foreach(array_slice($data['sheets']['in_field']['rows'], 0, 6) as $row)
        <div class="list-item">
          <div class="li-dot" style="background:#1d4ed8;"></div>
          <div class="li-body">
            <div class="li-sr">{{ $row[0] }}</div>
            <div class="li-reason">{{ $row[1] }}</div>
          </div>
        </div>
        @endforeach
      </div>
    </div>
  @endif

  </div>

  <!-- ROW 2: Rework + In Field (only shown here if QC card took row 1's second slot) -->
  <div class="grid-2">

  @if($data['enabled']['se_rework'] ?? true)
    <div class="card" onclick="openSheet('rework')">
      <div class="card-top">
        <div class="card-title">Rework Queue</div>
        <div class="card-more-btn">View ›</div>
      </div>
      <div class="card-big" style="color:#6d28d9;margin-bottom:10px;">{{ $data['kpis']['rework']['value'] }}</div>
      <div class="item-list">
        @foreach(array_slice($data['sheets']['rework']['rows'], 0, 6) as $row)
        <div class="list-item">
          <div class="li-dot" style="background:#6d28d9;"></div>
          <div class="li-body">
            <div class="li-sr">{{ $row[0] }} · {{ $row[1] }}</div>
            <div class="li-reason">{{ $row[3] }}</div>
          </div>
        </div>
        @endforeach
        @if(!count($data['sheets']['rework']['rows']))
        <div style="padding:14px 4px;color:#9a9890;font-size:.75rem;text-align:center;">No rework cases in your queue.</div>
        @endif
      </div>
    </div>
  @endif

  @if($data['qc_enabled'] && ($data['enabled']['se_qc'] ?? true) && ($data['enabled']['se_in_field'] ?? true))
    <div class="card" onclick="openSheet('in_field')">
      <div class="card-top">
        <div class="card-title">Currently In Field</div>
        <div class="card-more-btn">View all ›</div>
      </div>
      <div class="card-big" style="color:#b91c1c;margin-bottom:12px;">{{ $data['kpis']['in_field']['value'] }}</div>
      <div class="item-list">
        @foreach(array_slice($data['sheets']['in_field']['rows'], 0, 6) as $row)
        <div class="list-item">
          <div class="li-dot" style="background:#1d4ed8;"></div>
          <div class="li-body">
            <div class="li-sr">{{ $row[0] }}</div>
            <div class="li-reason">{{ $row[1] }}</div>
          </div>
        </div>
        @endforeach
      </div>
    </div>
  @elseif($data['enabled']['se_avg_dispatch'] ?? true)
    <div class="card">
      <div class="card-top">
        <div class="card-title">Avg. Dispatch Time</div>
      </div>
      <div class="card-big" style="color:var(--mm-gold);margin-bottom:6px;">{{ $data['kpis']['avg_time']['value'] }}</div>
      <div style="font-size:.72rem;color:#8a9890;">from approval to technician dispatch, yesterday</div>
    </div>
  @endif

  </div>

</div>

<!-- CTA - no dashboard access without logging in, so this always sends to /login -->
<div class="cta-wrap">
  <a class="cta-btn" href="{{ $loginUrl }}">Open Portal Dashboard →</a>
</div>
<div class="footer">Matter Mind SR Portal · Service Engineer Summary · {{ $summaryDate->format('d M Y') }} · Link valid 24 hours</div>

<!-- OVERLAY -->
<div class="sheet-overlay" id="overlay" onclick="closeSheet()"></div>

<!-- BOTTOM SHEET -->
<div class="bottom-sheet" id="bottomSheet">
  <div class="bs-handle"></div>
  <div class="bs-hdr">
    <div class="bs-title" id="bsTitle">Details</div>
    <button class="bs-close" onclick="closeSheet()">✕</button>
  </div>
  <div class="bs-body" id="bsBody"></div>
</div>

<script>
// ── DATA FROM SERVER ─────────────────────────────────────────────────
var PAGE_DATA = @json($data);

// ── CHARTS ────────────────────────────────────────────────────────
var CHARTS = {};

function buildCharts(){

  var c1 = document.getElementById('dispatchDonut');
  if(c1) CHARTS.dispatch = new Chart(c1,{
    type:'doughnut',
    data:{labels:['Dispatched','Pending'],
      datasets:[{data:[PAGE_DATA.kpis.dispatched.value, PAGE_DATA.kpis.pending.value],
        backgroundColor:['#4a8a6a','#e8a030'],
        borderWidth:3,borderColor:'#fff',hoverOffset:6}]},
    options:{responsive:true,maintainAspectRatio:false,cutout:'70%',
      plugins:{legend:{display:false},tooltip:{callbacks:{label:function(c){return ' '+c.label+': '+c.parsed;}}}}}
  });

  var c2 = document.getElementById('qcBar');
  if(c2) CHARTS.qc = new Chart(c2,{
    type:'bar',
    data:{
      labels:['Approved','Rejected'],
      datasets:[{data:[PAGE_DATA.qc.approved, PAGE_DATA.qc.rejected],
        backgroundColor:['#4a8a6a','#ef4444'],
        borderRadius:4,borderWidth:0}]
    },
    options:{indexAxis:'y',responsive:true,maintainAspectRatio:false,
      plugins:{legend:{display:false},tooltip:{callbacks:{label:function(c){return ' '+c.parsed.x+' SRs';}}}},
      scales:{x:{display:false,grid:{display:false}},
        y:{grid:{display:false},ticks:{color:'#6a7a70',font:{size:10,family:'Nunito'},padding:4}}}}
  });
}

// ── SHEET DATA (from server, rendered generically) ─────────────────
var SHEET_COLUMNS = {
  assigned: ['SR ID','Client','Category','Status','Approved At'],
  rework:   ['SR ID','Client','Technician','Reason','Rejected At'],
  in_field: ['SR ID','Status','','',''],
};

function renderGenericTable(key){
  var sheet = PAGE_DATA.sheets[key];
  if(!sheet || !sheet.rows || !sheet.rows.length){
    return '<div style="padding:24px 4px;color:#9a9890;font-size:.8rem;text-align:center;">No detail rows for this section yet.</div>';
  }
  var cols = SHEET_COLUMNS[key] || ['SR ID','Client','Category','Status','Date'];
  var head = '<tr>'+cols.map(function(c){return '<th>'+c+'</th>';}).join('')+'</tr>';
  var body = sheet.rows.map(function(r){
    return '<tr>'+r.map(function(v){return '<td>'+v+'</td>';}).join('')+'</tr>';
  }).join('');
  return '<table class="detail-table"><thead>'+head+'</thead><tbody>'+body+'</tbody></table>';
}

function openSheet(key){
  var sheet = PAGE_DATA.sheets[key];
  if(!sheet) return;
  document.getElementById('bsTitle').textContent = sheet.title;
  document.getElementById('bsBody').innerHTML = renderGenericTable(key);
  document.getElementById('overlay').classList.add('open');
  document.getElementById('bottomSheet').classList.add('open');
  document.body.style.overflow = 'hidden';
}

function closeSheet(){
  document.getElementById('overlay').classList.remove('open');
  document.getElementById('bottomSheet').classList.remove('open');
  document.body.style.overflow = '';
}

document.addEventListener('keydown', function(e){ if(e.key === 'Escape') closeSheet(); });

window.addEventListener('load', function(){
  buildCharts();
});
</script>
</body>
</html>