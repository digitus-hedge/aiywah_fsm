<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0"/>
<title>Head of Projects · Previous Day Summary · Matter Mind SR Portal</title>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<link rel="icon" type="image/png" href="{{ asset('assets/images/favicon.png') }}">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
html{overflow-x:hidden;}
body{font-family:'DM Sans',system-ui,sans-serif;background:#f2f4f7;color:#1a1a2e;
  -webkit-font-smoothing:antialiased;min-height:100vh;overflow-x:hidden;
  font-size:clamp(14px,.9vw + 11px,16px);}

:root{
  --mm-gold:#9A7B4F;
  --mm-gold-light:#C4A882;
  --mm-gold-dark:#7A6140;
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
  padding:10px 0;position:relative;z-index:1;}
.quick-bar-inner{margin:0 auto;padding:0 16px;display:flex;gap:8px;flex-wrap:wrap;}

.qb-pill{font-size:.67rem;font-weight:700;padding:4px 10px;border-radius:20px;
  display:flex;align-items:center;gap:4px;white-space:nowrap;}
.qp-red  {background:#fff0f0;color:#8a2020;}
.qp-blue {background:#eff6ff;color:#1d4ed8;}
.qp-amb  {background:#fffbeb;color:#b45309;}
.qp-green{background:#ecfdf5;color:#047857;}

/* ── BODY ── */
.body{padding:14px;max-width:1240px;margin:0 auto;}
@media(min-width:600px){.body{padding:18px;}}
@media(min-width:900px){.body{padding:22px 18px;}}
@media(min-width:1400px){.body{max-width:1800px;padding:28px 22px;}}

/* ── KPI STRIP ── */
.kpi-strip{display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:12px;}
@media(min-width:500px){.kpi-strip{grid-template-columns:repeat(3,1fr);}}
@media(min-width:800px){.kpi-strip{grid-template-columns:repeat(6,1fr);}}

.kpi{background:#fff;border-radius:12px;padding:14px 12px 12px;
  box-shadow:0 1px 6px rgba(0,0,0,.07);border:1px solid #e4e8f0;
  cursor:pointer;transition:all .18s;position:relative;overflow:hidden;}
.kpi:hover{box-shadow:0 4px 18px rgba(0,0,0,.11);transform:translateY(-2px);}
.kpi::before{content:'';position:absolute;top:0;left:0;right:0;height:3px;border-radius:12px 12px 0 0;}
.kpi.k-red  ::before{background:linear-gradient(90deg,#b91c1c,#ef4444);}
.kpi.k-blue ::before{background:linear-gradient(90deg,#1d4ed8,#3b82f6);}
.kpi.k-teal ::before{background:linear-gradient(90deg,#0891b2,#22d3ee);}
.kpi.k-amb  ::before{background:linear-gradient(90deg,#b45309,#f59e0b);}
.kpi.k-purp ::before{background:linear-gradient(90deg,#6d28d9,#8b5cf6);}
.kpi.k-green::before{background:linear-gradient(90deg,#047857,#34d399);}
.kpi-ico{width:30px;height:30px;border-radius:8px;display:flex;align-items:center;
  justify-content:center;font-size:.85rem;margin-bottom:8px;}
.ki-red  {background:#fff0f0;} .ki-blue {background:#eff6ff;}
.ki-teal {background:#ecfeff;} .ki-amb  {background:#fffbeb;}
.ki-purp {background:#f5f3ff;} .ki-green{background:#ecfdf5;}
.kpi-val{font-size:clamp(1.4rem,4.5vw,1.8rem);font-weight:900;line-height:1;margin-bottom:2px;}
.kv-red  {color:#b91c1c;} .kv-blue {color:#1d4ed8;}
.kv-teal {color:#0891b2;} .kv-amb  {color:#b45309;}
.kv-purp {color:#6d28d9;} .kv-green{color:#047857;}
.kpi-lbl{font-size:.63rem;font-weight:700;color:#8a94a0;line-height:1.3;}
.kpi-sub{font-size:.59rem;color:#aab4c0;margin-top:2px;}
.kpi-more{font-size:.6rem;font-weight:700;color:var(--mm-gold);margin-top:6px;
  display:flex;align-items:center;gap:3px;opacity:.75;}

/* ── CARDS ── */
.grid-2{display:grid;grid-template-columns:1fr;gap:12px;margin-bottom:12px;}
@media(min-width:640px){.grid-2{grid-template-columns:1fr 1fr;}}
.grid-3{display:grid;grid-template-columns:1fr;gap:12px;margin-bottom:12px;}
@media(min-width:640px){.grid-3{grid-template-columns:1fr 1fr;}}
@media(min-width:900px){.grid-3{grid-template-columns:1fr 1fr 1fr;}}

.card{background:#fff;border-radius:14px;padding:clamp(14px,3vw,18px);
  box-shadow:0 1px 8px rgba(0,0,0,.06);border:1px solid #e4e8f0;
  cursor:pointer;transition:all .18s;min-width:0;}
.card:hover{box-shadow:0 6px 22px rgba(0,0,0,.1);transform:translateY(-1px);}
.card-top{display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:14px;gap:8px;}
.card-title{font-size:.7rem;font-weight:800;color:#6a7a8a;text-transform:uppercase;letter-spacing:.09em;}
.card-more-btn{font-size:.65rem;font-weight:700;color:var(--mm-gold);display:flex;align-items:center;gap:3px;opacity:.85;white-space:nowrap;}
.card-big{font-size:clamp(1.9rem,5.5vw,2.6rem);font-weight:900;line-height:1;}

/* Chart containers */
.ch-wrap{position:relative;}
.ch-wrap canvas{position:absolute;inset:0;width:100%!important;height:100%!important;}
.h180{height:180px;} .h160{height:160px;} .h140{height:140px;} .h120{height:120px;}

/* Donut center */
.donut-center{position:absolute;inset:0;display:flex;flex-direction:column;
  align-items:center;justify-content:center;pointer-events:none;}
.dc-num{font-size:1.6rem;font-weight:900;color:#1a1a2e;line-height:1;}
.dc-lbl{font-size:.58rem;font-weight:700;color:#8a94a0;text-transform:uppercase;letter-spacing:.06em;}

/* Legend */
.legend{display:flex;flex-wrap:wrap;gap:10px;margin-top:12px;}
.leg-item{display:flex;align-items:center;gap:5px;font-size:.68rem;color:#6a7a8a;font-weight:600;}
.leg-dot{width:9px;height:9px;border-radius:50%;flex-shrink:0;}

/* Pending SR rows */
.sr-rows{display:flex;flex-direction:column;gap:6px;margin-top:4px;}
.sr-row{display:flex;align-items:center;justify-content:space-between;gap:8px;
  padding:7px 10px;border-radius:8px;background:#f8faff;border-left:3px solid;}
.sr-id{font-size:.7rem;font-weight:700;color:#1a1a2e;}
.sr-client{font-size:.65rem;color:#8a94a0;}
.sr-hrs{font-size:.68rem;font-weight:800;}
.pill-s{font-size:.62rem;font-weight:700;padding:2px 7px;border-radius:10px;white-space:nowrap;}
.ps-blue {background:#eff6ff;color:#1d4ed8;} .ps-green{background:#ecfdf5;color:#047857;}
.ps-red  {background:#fff0f0;color:#b91c1c;} .ps-amb  {background:#fffbeb;color:#b45309;}
.ps-purp {background:#f5f3ff;color:#6d28d9;} .ps-teal {background:#ecfeff;color:#0891b2;}
.ps-grey {background:#f8fafc;color:#475569;}

/* Star ratings */
.rating-row{display:flex;align-items:center;justify-content:space-between;gap:8px;
  padding:6px 0;border-bottom:1px solid #f4f6f8;}
.rating-row:last-child{border-bottom:none;}
.rr-left{display:flex;flex-direction:column;gap:2px;min-width:0;}
.rr-id{font-size:.7rem;font-weight:700;color:#1a1a2e;word-break:break-word;}
.rr-client{font-size:.63rem;color:#8a94a0;}
.rr-stars{font-size:.75rem;color:#f59e0b;letter-spacing:1px;white-space:nowrap;}

/* Category re-allocation rows */
.realloc-list{display:flex;flex-direction:column;gap:5px;margin-top:6px;}
.realloc-item{padding:8px 10px;border-radius:9px;background:#fafbff;
  border:1px solid #e4e8f0;}
.ri-top{display:flex;align-items:center;justify-content:space-between;gap:8px;margin-bottom:3px;}
.ri-sr{font-size:.7rem;font-weight:700;color:#1a1a2e;}
.ri-change{display:flex;align-items:center;gap:5px;margin-top:3px;flex-wrap:wrap;}
.ri-from{font-size:.65rem;color:#ef4444;font-weight:600;padding:1px 6px;
  border-radius:5px;background:#fff0f0;}
.ri-arrow{font-size:.6rem;color:#8a94a0;}
.ri-to{font-size:.65rem;color:#047857;font-weight:600;padding:1px 6px;
  border-radius:5px;background:#ecfdf5;}

/* Rework items */
.rework-list{display:flex;flex-direction:column;gap:6px;margin-top:4px;}
.rework-item{padding:9px 12px;border-radius:9px;background:#fff8f8;
  border-left:3px solid #ef4444;}
.rwi-top{display:flex;align-items:center;justify-content:space-between;gap:8px;margin-bottom:3px;}
.rwi-sr{font-size:.72rem;font-weight:700;color:#1a1a2e;word-break:break-word;}
.rwi-reason{font-size:.65rem;color:#7a3030;line-height:1.4;}
.rwi-meta{font-size:.62rem;color:#aab4c0;margin-top:3px;}

/* Approved funnel */
.funnel{display:flex;flex-direction:column;gap:6px;margin-top:8px;}
.funnel-row{display:flex;align-items:center;gap:10px;}
.fr-label{font-size:.68rem;font-weight:600;color:#3a4a5a;width:110px;flex-shrink:0;}
.fr-bar{flex:1;height:7px;border-radius:4px;background:#e4e8f0;overflow:hidden;}
.fr-fill{height:100%;border-radius:4px;transition:width .5s;}
.fr-num{font-size:.7rem;font-weight:800;width:24px;text-align:right;flex-shrink:0;}

/* QC breakdown */
.qc-items{display:flex;flex-direction:column;gap:5px;margin-top:6px;}
.qc-item{display:flex;align-items:center;justify-content:space-between;gap:8px;
  padding:6px 10px;border-radius:8px;}
.qi-label{font-size:.7rem;font-weight:600;color:#3a4a5a;}
.qi-count{font-size:.78rem;font-weight:800;white-space:nowrap;}

/* ── BOTTOM SHEET ── */
.sheet-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);
  z-index:800;backdrop-filter:blur(3px);}
.sheet-overlay.open{display:block;}
.bottom-sheet{position:fixed;left:0;right:0;bottom:-100%;background:#fff;
  z-index:900;border-radius:20px 20px 0 0;max-height:82vh;
  display:flex;flex-direction:column;
  box-shadow:0 -4px 30px rgba(0,0,0,.15);transition:bottom .35s cubic-bezier(.4,0,.2,1);}
.bottom-sheet.open{bottom:0;}
.bs-handle{width:40px;height:4px;background:#e0e4ec;border-radius:2px;margin:10px auto 0;}
.bs-hdr{padding:14px 20px 12px;border-bottom:1px solid #f0f2f8;
  display:flex;align-items:center;justify-content:space-between;flex-shrink:0;gap:10px;}
.bs-title{font-size:.9rem;font-weight:800;color:#1a1a2e;}
.bs-close{width:28px;height:28px;border-radius:7px;background:#f4f6f8;flex-shrink:0;
  border:none;cursor:pointer;font-size:.8rem;display:flex;align-items:center;justify-content:center;}
.bs-body{flex:1;overflow-y:auto;overflow-x:auto;padding:14px 20px 24px;}
.bs-body::-webkit-scrollbar{width:3px;}
.bs-body::-webkit-scrollbar-thumb{background:#e0e4ec;border-radius:2px;}

/* Detail table */
.detail-table{width:100%;min-width:520px;border-collapse:collapse;font-size:.78rem;}
.detail-table th{text-align:left;padding:7px 10px;font-size:.63rem;font-weight:800;
  text-transform:uppercase;letter-spacing:.08em;color:#9aa4b0;
  border-bottom:1px solid #f0f2f8;white-space:nowrap;}
.detail-table td{padding:8px 10px;border-bottom:1px solid #f8f9fc;color:#1a1a2e;vertical-align:middle;}
.detail-table tr:last-child td{border-bottom:none;}
.detail-table tr:hover td{background:#fafbff;}

/* CTA */
.cta-wrap{padding:14px 14px 28px;max-width:1240px;margin:0 auto;}
@media(min-width:1400px){.cta-wrap{max-width:1320px;}}
.cta-btn{display:flex;align-items:center;justify-content:center;gap:8px;
  background:linear-gradient(135deg,var(--mm-gold),var(--mm-gold-light));color:#fff;
  padding:14px 24px;border-radius:12px;font-size:.88rem;font-weight:800;
  text-decoration:none;border:none;cursor:pointer;width:100%;max-width:520px;margin:0 auto;
  box-shadow:0 4px 16px rgba(154,123,79,.3);transition:opacity .15s;}
.cta-btn:hover{opacity:.88;}

.footer{text-align:center;padding:10px 16px 20px;font-size:.62rem;color:#9aa4b0;}

@media(max-width:359px){
  .kpi-strip{grid-template-columns:1fr 1fr;}
  .hdr{padding:16px 0 0;}
  .hdr-inner,.quick-bar-inner{padding-left:14px;padding-right:14px;}
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
      <div class="role-pill">◈ {{ $user->role->name ?? 'Head of Projects' }}</div>
      <div class="hdr-time">Generated at {{ now()->format('h:i A') }}</div>
    </div>
  </div>
  <div class="quick-bar">
    <div class="quick-bar-inner">
    @if($data['enabled']['hop_pending'] ?? true)
      <div class="qb-pill qp-red">⏳ {{ $data['quick']['pending'] }} Pending Review</div>
    @endif
    @if($data['enabled']['hop_approved'] ?? true)
      <div class="qb-pill qp-blue">✔ {{ $data['quick']['approved'] }} Approved</div>
    @endif
    @if($data['enabled']['hop_qc'] ?? true)
      <div class="qb-pill qp-amb">🔍 {{ $data['quick']['qc'] }} QC Pending</div>
   @endif
   @if($data['enabled']['hop_completed'] ?? true)
      <div class="qb-pill qp-green">✅ {{ $data['quick']['completed'] }} Completed</div>
    @endif  
    </div>
  </div>
</div>

<div class="body">

  <!-- KPI STRIP -->
  <div class="kpi-strip">
  @if($data['enabled']['hop_pending'] ?? true)
    <div class="kpi k-red" onclick="openSheet('pending')">
      <div class="kpi-ico ki-red">⏳</div>
      <div class="kpi-val kv-red">{{ $data['kpis']['pending']['value'] }}</div>
      <div class="kpi-lbl">Pending Review</div>
      <div class="kpi-sub">{{ $data['kpis']['pending']['sub'] }}</div>
      <div class="kpi-more">View list ›</div>
    </div>
  @endif
  @if($data['enabled']['hop_approved'] ?? true)
    <div class="kpi k-blue" onclick="openSheet('approved')">
      <div class="kpi-ico ki-blue">✔</div>
      <div class="kpi-val kv-blue">{{ $data['kpis']['approved']['value'] }}</div>
      <div class="kpi-lbl">Approved</div>
      <div class="kpi-sub">{{ $data['kpis']['approved']['sub'] }}</div>
      <div class="kpi-more">View breakdown ›</div>
    </div>
  @endif
  @if($data['enabled']['hop_qc'] ?? true)
    <div class="kpi k-teal" onclick="openSheet('qc')">
      <div class="kpi-ico ki-teal">🔍</div>
      <div class="kpi-val kv-teal">{{ $data['kpis']['qc']['value'] }}</div>
      <div class="kpi-lbl">QC Pending</div>
      <div class="kpi-sub">{{ $data['kpis']['qc']['sub'] }}</div>
      <div class="kpi-more">View queue ›</div>
    </div>
  @endif
  @if($data['enabled']['hop_rework'] ?? true)
    <div class="kpi k-amb" onclick="openSheet('rework')">
      <div class="kpi-ico ki-amb">🔄</div>
      <div class="kpi-val kv-amb">{{ $data['kpis']['rework']['value'] }}</div>
      <div class="kpi-lbl">Rework Cases</div>
      <div class="kpi-sub">{{ $data['kpis']['rework']['sub'] }}</div>
      <div class="kpi-more">View details ›</div>
    </div>
  @endif
  @if($data['enabled']['hop_realloc'] ?? true)
    <div class="kpi k-purp" onclick="openSheet('realloc')">
      <div class="kpi-ico ki-purp">🔀</div>
      <div class="kpi-val kv-purp">{{ $data['kpis']['realloc']['value'] }}</div>
      <div class="kpi-lbl">Re-allocations</div>
      <div class="kpi-sub">{{ $data['kpis']['realloc']['sub'] }}</div>
      <div class="kpi-more">View log ›</div>
    </div>
  @endif
  @if($data['enabled']['hop_completed'] ?? true)
    <div class="kpi k-green" onclick="openSheet('completed')">
      <div class="kpi-ico ki-green">✅</div>
      <div class="kpi-val kv-green">{{ $data['kpis']['completed']['value'] }}</div>
      <div class="kpi-lbl">Completed</div>
      <div class="kpi-sub">{{ $data['kpis']['completed']['sub'] }}</div>
      <div class="kpi-more">View ratings ›</div>
    </div>
  @endif
  </div>

  <!-- ROW 1 — Pending Review + Approved Funnel -->
  <div class="grid-2">

  @if($data['enabled']['hop_pending'] ?? true)
    <div class="card" onclick="openSheet('pending')">
      <div class="card-top">
        <div class="card-title">Pending Review — Action Required</div>
        <div class="card-more-btn" style="color:#b91c1c;">View all ›</div>
      </div>
      <div class="sr-rows">
        @forelse($data['pending']['rows'] as $row)
        <div class="sr-row" style="border-color:{{ $row['border'] }};">
          <div><div class="sr-id">{{ $row['sr'] }}</div><div class="sr-client">{{ $row['client'] }} · {{ $row['category'] }}</div></div>
          <div style="text-align:right;"><div class="sr-hrs" style="color:{{ $row['hrs_color'] }};">{{ $row['hrs_ago'] }}</div><span class="pill-s {{ $row['priority_class'] }}">{{ $row['priority'] }}</span></div>
        </div>
        @empty
        <div style="padding:16px 4px;color:#aab4c0;font-size:.78rem;text-align:center;">Nothing pending review.</div>
        @endforelse
      </div>
    </div>
  @endif

  @if($data['enabled']['hop_approved'] ?? true)
    <div class="card" onclick="openSheet('approved')">
      <div class="card-top">
        <div class="card-title">Approved SRs — Forward Progress</div>
        <div class="card-more-btn">View all ›</div>
      </div>
      <div style="display:flex;align-items:baseline;gap:8px;margin-bottom:14px;">
        <div class="card-big" style="color:#1d4ed8;">{{ $data['approved']['total'] }}</div>
        <div style="font-size:.78rem;color:#8a94a0;font-weight:600;">approved yesterday</div>
      </div>
      <div class="funnel">
        <div class="funnel-row">
          <div class="fr-label">Dispatched by SE</div>
          <div class="fr-bar"><div class="fr-fill" style="width:{{ $data['approved']['funnel']['dispatched_pct'] }}%;background:#1d4ed8;"></div></div>
          <div class="fr-num" style="color:#1d4ed8;">{{ $data['approved']['funnel']['dispatched'] }}</div>
        </div>
        <div class="funnel-row">
          <div class="fr-label">Awaiting SE</div>
          <div class="fr-bar"><div class="fr-fill" style="width:{{ $data['approved']['funnel']['awaiting_pct'] }}%;background:#f59e0b;"></div></div>
          <div class="fr-num" style="color:#b45309;">{{ $data['approved']['funnel']['awaiting'] }}</div>
        </div>
        <div class="funnel-row">
          <div class="fr-label">Routed to Accts</div>
          <div class="fr-bar"><div class="fr-fill" style="width:{{ $data['approved']['funnel']['accounts_pct'] }}%;background:#7c3aed;"></div></div>
          <div class="fr-num" style="color:#6d28d9;">{{ $data['approved']['funnel']['accounts'] }}</div>
        </div>
      </div>
      <div style="height:140px;position:relative;margin-top:14px;">
        <canvas id="approvedDonut"></canvas>
        <div class="donut-center">
          <div class="dc-num" style="color:#1d4ed8;">{{ $data['approved']['total'] }}</div>
          <div class="dc-lbl">Approved</div>
        </div>
      </div>
    </div>
  @endif

  </div>

  <!-- ROW 2 — QC Pending + Rework -->
  <div class="grid-2">

  @if($data['enabled']['hop_qc'] ?? true)
    <div class="card" onclick="openSheet('qc')">
      <div class="card-top">
        <div class="card-title">QC Reviews Pending</div>
        <div class="card-more-btn" style="color:#0891b2;">View queue ›</div>
      </div>
      <div style="display:flex;align-items:baseline;gap:8px;margin-bottom:12px;">
        <div class="card-big" style="color:#0891b2;">{{ $data['qc']['total'] }}</div>
        <div style="font-size:.78rem;color:#8a94a0;font-weight:600;">carried forward</div>
      </div>
      <div style="height:160px;position:relative;margin-bottom:12px;">
        <canvas id="qcBar"></canvas>
      </div>
      <div class="qc-items">
        <div class="qc-item" style="background:#fff8f0;">
          <div class="qi-label">Awaiting HoP review</div>
          <div class="qi-count" style="color:#0891b2;">{{ $data['qc']['awaiting_hop'] }}</div>
        </div>
        <div class="qc-item" style="background:#f0fdf4;">
          <div class="qi-label">SE QC permission ON</div>
          <div class="qi-count" style="color:#047857;">{{ $data['qc']['se_qc_on'] }} pending</div>
        </div>
      </div>
    </div>
  @endif

  @if($data['enabled']['hop_rework'] ?? true)
    <div class="card" onclick="openSheet('rework')">
      <div class="card-top">
        <div class="card-title">Rework Cases — Yesterday</div>
        <div class="card-more-btn" style="color:#b45309;">View ›</div>
      </div>
      <div style="display:flex;align-items:baseline;gap:8px;margin-bottom:12px;">
        <div class="card-big" style="color:#b45309;">{{ $data['rework']['total'] }}</div>
        <div style="font-size:.78rem;color:#8a94a0;font-weight:600;">QC rejected</div>
      </div>
      <div class="rework-list">
        @forelse($data['rework']['rows'] as $row)
        <div class="rework-item">
          <div class="rwi-top">
            <div class="rwi-sr">{{ $row['sr'] }} · {{ $row['client'] }}</div>
            <span class="pill-s {{ $row['action_class'] }}">{{ $row['action'] }}</span>
          </div>
          <div class="rwi-reason">{{ $row['reason'] }}</div>
          <div class="rwi-meta">ML: {{ $row['ml'] }} · Rejected by HoP · {{ $row['time'] }}</div>
        </div>
        @empty
        <div style="padding:16px 4px;color:#aab4c0;font-size:.78rem;text-align:center;">No rework yesterday.</div>
        @endforelse
      </div>
    </div>
  @endif

  </div>

  <!-- ROW 3 — Re-allocations + Completed with Ratings -->
  <div class="grid-2">

  @if($data['enabled']['hop_realloc'] ?? true)
    <div class="card" onclick="openSheet('realloc')">
      <div class="card-top">
        <div class="card-title">Category Re-allocations</div>
        <div class="card-more-btn" style="color:#6d28d9;">View log ›</div>
      </div>
      <div style="display:flex;align-items:baseline;gap:8px;margin-bottom:12px;">
        <div class="card-big" style="color:#6d28d9;">{{ $data['realloc']['total'] }}</div>
        <div style="font-size:.78rem;color:#8a94a0;font-weight:600;">corrections made</div>
      </div>
      <div class="realloc-list">
        @forelse($data['realloc']['rows'] as $row)
        <div class="realloc-item">
          <div class="ri-top">
            <div class="ri-sr">{{ $row['sr'] }} · {{ $row['client'] }}</div>
            <span class="pill-s ps-grey" style="font-size:.6rem;">{{ $row['note'] }}</span>
          </div>
          <div class="ri-change">
            <div class="ri-from">{{ $row['from'] }}</div>
            <div class="ri-arrow">→</div>
            <div class="ri-to">{{ $row['to'] }}</div>
          </div>
        </div>
        @empty
        <div style="padding:16px 4px;color:#aab4c0;font-size:.78rem;text-align:center;">No re-allocations tracked for yesterday.</div>
        @endforelse
      </div>
    </div>
  @endif

  @if($data['enabled']['hop_completed'] ?? true)
    <div class="card" onclick="openSheet('completed')">
      <div class="card-top">
        <div class="card-title">Completed SRs — Ratings</div>
        <div class="card-more-btn" style="color:#047857;">View all ›</div>
      </div>
      <div style="display:flex;align-items:baseline;gap:8px;margin-bottom:6px;">
        <div class="card-big" style="color:#047857;">{{ $data['completed']['total'] }}</div>
        <div style="font-size:.78rem;color:#8a94a0;font-weight:600;">completed yesterday</div>
      </div>
      <div style="font-size:.7rem;color:#8a94a0;margin-bottom:12px;display:flex;align-items:center;gap:5px;flex-wrap:wrap;">
        <span style="color:#f59e0b;font-size:.85rem;">{{ $data['completed']['avg_stars'] }}</span>
        <span style="font-weight:700;color:#047857;">{{ $data['completed']['avg_rating'] }}</span> avg rating · {{ $data['completed']['rated_count'] }} of {{ $data['completed']['total'] }} rated
      </div>
      <div style="height:130px;position:relative;margin-bottom:12px;">
        <canvas id="ratingsBar"></canvas>
      </div>
      <div style="display:flex;flex-direction:column;gap:5px;">
        @forelse($data['completed']['rows'] as $row)
        <div class="rating-row">
          <div class="rr-left"><div class="rr-id">{{ $row['sr'] }} · {{ $row['client'] }}</div><div class="rr-client">{{ $row['ml'] }} · {{ $row['category'] }}</div></div>
          <div class="rr-stars">{{ $row['stars'] }}</div>
        </div>
        @empty
        <div style="padding:8px 4px;color:#aab4c0;font-size:.78rem;text-align:center;">No completions yesterday.</div>
        @endforelse
      </div>
    </div>
  @endif

  </div>

</div>

<!-- CTA -->
<div class="cta-wrap">
  <a class="cta-btn" href="{{ $loginUrl }}">Open Portal Dashboard →</a>
</div>
<div class="footer">Matter Mind SR Portal · Head of Projects Summary · {{ $summaryDate->format('d M Y') }} · Link valid 48 hours</div>

<!-- OVERLAY + SHEET -->
<div class="sheet-overlay" id="overlay" onclick="closeSheet()"></div>
<div class="bottom-sheet" id="bottomSheet">
  <div class="bs-handle"></div>
  <div class="bs-hdr">
    <div class="bs-title" id="bsTitle">Details</div>
    <button class="bs-close" onclick="closeSheet()">✕</button>
  </div>
  <div class="bs-body" id="bsBody"></div>
</div>

<script>
var PAGE_DATA = @json($data);
var CHARTS = {};

function buildCharts(){
  var c1 = document.getElementById('approvedDonut');
  if(c1) CHARTS.approved = new Chart(c1,{type:'doughnut',
    data:{labels:['Dispatched by SE','Awaiting SE','Routed to Accounts'],
      datasets:[{data:[
        PAGE_DATA.approved.funnel.dispatched,
        PAGE_DATA.approved.funnel.awaiting,
        PAGE_DATA.approved.funnel.accounts
      ],backgroundColor:['#1d4ed8','#f59e0b','#7c3aed'],
        borderWidth:3,borderColor:'#fff',hoverOffset:5}]},
    options:{responsive:true,maintainAspectRatio:false,cutout:'70%',
      plugins:{legend:{display:false},tooltip:{callbacks:{label:function(c){return ' '+c.label+': '+c.parsed;}}}}}
  });

  var c2 = document.getElementById('qcBar');
  if(c2) CHARTS.qc = new Chart(c2,{type:'bar',
    data:{labels:PAGE_DATA.qc.by_category_labels,
      datasets:[{data:PAGE_DATA.qc.by_category_values,
        backgroundColor:['#0891b2','#1d4ed8','#7c3aed','#6b7280','#f59e0b','#10b981'],
        borderRadius:5,borderWidth:0}]},
    options:{responsive:true,maintainAspectRatio:false,
      plugins:{legend:{display:false},tooltip:{callbacks:{label:function(c){return ' '+c.parsed.y+' SRs pending QC';}}}},
      scales:{x:{grid:{display:false},ticks:{color:'#6a7a8a',font:{size:10,family:'Nunito'}}},
        y:{display:false,grid:{display:false}}}}
  });

  var c3 = document.getElementById('ratingsBar');
  if(c3) CHARTS.ratings = new Chart(c3,{type:'bar',
    data:{labels:['★','★★','★★★','★★★★','★★★★★'],
      datasets:[{data:PAGE_DATA.completed.rating_distribution,
        backgroundColor:['#e5e7eb','#d1d5db','#fcd34d','#f59e0b','#047857'],
        borderRadius:4,borderWidth:0}]},
    options:{responsive:true,maintainAspectRatio:false,
      plugins:{legend:{display:false},tooltip:{callbacks:{label:function(c){return ' '+c.parsed.y+' SRs';}}}},
      scales:{x:{grid:{display:false},ticks:{color:'#6a7a8a',font:{size:10,family:'Nunito'}}},
        y:{display:false,grid:{display:false}}}}
  });
}

var SHEET_COLUMNS = {
  pending:   ['SR ID','Client','Category','Scope','Priority','Logged By','Waiting'],
  approved:  ['SR ID','Client','Scope','SE Assigned','Next Status'],
  qc:        ['SR ID','Client','Category','ML','Submitted','Proof'],
  rework:    ['SR ID','Client','ML','Rejection Reason','SE Action','Time'],
  realloc:   ['SR ID','Client','Original','Corrected To','Reason','Time'],
  completed: ['SR ID','Client','ML','Scope','Rating','Rework'],
};

function renderGenericTable(key){
  var sheet = PAGE_DATA.sheets[key];
  if(!sheet || !sheet.rows || !sheet.rows.length){
    return '<div style="padding:24px 4px;color:#9aa4b0;font-size:.8rem;text-align:center;">No detail rows for this section yet.</div>';
  }
  var cols = SHEET_COLUMNS[key] || sheet.rows[0].map(function(_,i){return 'Col '+(i+1);});
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

window.addEventListener('load', function(){ buildCharts(); });
</script>
</body>
</html>