{{--
|--------------------------------------------------------------------------
| Front Desk Executive Dashboard
|--------------------------------------------------------------------------
| resources/views/frontdesk/dashboard.blade.php
|
| No placeholder data — every value comes from the controller.
| See the variable contract at the bottom of this file.
--}}

@extends('layouts.layout')

@section('title', 'Front Desk Dashboard')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<style>
/* ==========================================================================
   FRONT DESK DASHBOARD — all rules scoped to #fdeDash so nothing leaks into
   the rest of the app (and Bootstrap's .card etc. can't override these).
   ========================================================================== */

footer.footer { display: none; }

#fdeDash{
  --card:#fff; --card2:#faf9f7;
  --border:rgba(0,0,0,.07);
  --shadow:0 2px 20px rgba(0,0,0,.06);
  --shadow-hover:0 8px 32px rgba(0,0,0,.11);
  --text:#1a1614; --muted:#8a8480; --light:#bbb8b4;
  --gold:#9a8053; --gold2:#b8975e; --gold-bg:rgba(154,128,83,.1);
  --ok:#15803d; --warn:#d97706; --danger:#dc2626;
  --blue:#2563eb; --violet:#7c3aed; --slate:#64748b; --ink:#393837;

  --a1:#9a8053; --a2:#b8975e; --a3:#7d6742; --a4:#c7ab7c;

  font-family:var(--font-header);
  font-size:.875rem;
  color:var(--text);
}
[data-theme="dark"] #fdeDash{
  --card:#1e1b18; --card2:#252220;
  --border:rgba(255,255,255,.07);
  --shadow:0 2px 20px rgba(0,0,0,.4);
  --shadow-hover:0 8px 32px rgba(0,0,0,.5);
  --text:#e8e0d4; --muted:#7a756e; --light:#4a4540;
  --gold-bg:rgba(154,128,83,.12);
  --a1:#b8975e; --a2:#c7ab7c; --a3:#9a8053; --a4:#d6c09a;
}

#fdeDash *,#fdeDash *::before,#fdeDash *::after{box-sizing:border-box;}
#fdeDash a{text-decoration:none;}
#fdeDash .cg,#fdeDash h1,#fdeDash h2,#fdeDash h3,#fdeDash h4,
#fdeDash .greeting,#fdeDash .big-num,#fdeDash .panel-heading{
  font-family:var(--font-body);letter-spacing:-.01em;}
#fdeDash button:focus-visible,#fdeDash select:focus-visible,
#fdeDash [tabindex]:focus-visible{outline:2px solid var(--gold);outline-offset:2px;}

/* ── HEADER / GREETING ── */
#fdeDash .greeting-row{display:flex;align-items:flex-start;justify-content:space-between;
  gap:20px;margin-bottom:24px;flex-wrap:wrap;}
#fdeDash .greeting{font-size:clamp(1.25rem,4vw,1.6rem);font-family:var(--font-header);
  font-weight:700;color:var(--text);margin-bottom:3px;}
#fdeDash .greeting-sub{font-size:.82rem;color:var(--muted);}

/* ── FILTER BAR ── */
#fdeDash .filter-bar{display:flex;align-items:center;gap:8px;flex-wrap:wrap;}
#fdeDash .fq-pill{padding:6px 13px;border-radius:20px;font-size:.78rem;font-weight:500;
  cursor:pointer;border:1px solid var(--border);background:var(--card);
  color:var(--muted);transition:all .15s;white-space:nowrap;}
#fdeDash .fq-pill:hover{border-color:var(--gold);color:var(--gold);}
#fdeDash .fq-pill.active{background:var(--gold);color:#fff;border-color:var(--gold);}

/* ── ALERT STRIP ── */
#fdeDash .alert-row{display:flex;gap:9px;margin-bottom:20px;flex-wrap:wrap;}
#fdeDash .a-chip{display:inline-flex;align-items:center;gap:6px;padding:7px 13px;
  border-radius:9px;font-size:.76rem;font-weight:500;border:1px solid;
  background:transparent;transition:opacity .15s;}
#fdeDash .a-red{background:rgba(220,38,38,.08);border-color:rgba(220,38,38,.16);color:var(--danger);}
#fdeDash .a-amb{background:rgba(217,119,6,.08);border-color:rgba(217,119,6,.16);color:var(--warn);}
#fdeDash .a-grn{background:rgba(21,128,61,.08);border-color:rgba(21,128,61,.16);color:var(--ok);}
#fdeDash .a-blu{background:rgba(37,99,235,.08);border-color:rgba(37,99,235,.16);color:var(--blue);}

/* ── CARDS ── */
#fdeDash .card{background:var(--card);border:none;border-radius:16px;box-shadow:var(--shadow);
  overflow:hidden;transition:box-shadow .2s,transform .2s;}
#fdeDash .card-pad{padding:20px 22px;}
#fdeDash .c-hdr{display:flex;align-items:center;justify-content:space-between;
  gap:8px;margin-bottom:16px;flex-wrap:wrap;}
#fdeDash .c-label{font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.09em;
  color:var(--muted);display:flex;align-items:center;gap:6px;}
#fdeDash .c-label i{color:var(--a1);font-size:.85rem;}
#fdeDash .c-more{font-size:.72rem;color:var(--gold);cursor:pointer;background:none;border:none;
  display:flex;align-items:center;gap:3px;white-space:nowrap;padding:0;}
#fdeDash .c-more:hover{opacity:.8;}

/* ── KPI CARDS ── */
#fdeDash .kpi-row{display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:20px;}
#fdeDash .kpi{background:var(--card);border-radius:16px;padding:20px;text-align:left;
  box-shadow:var(--shadow);transition:all .2s;border:none;width:100%;
  display:flex;flex-direction:column;gap:10px;position:relative;overflow:hidden;}
#fdeDash .kpi:hover{box-shadow:var(--shadow-hover);transform:translateY(-2px);}
#fdeDash .kpi::after{content:'';position:absolute;right:-20px;top:-20px;
  width:80px;height:80px;border-radius:50%;opacity:.06;}
#fdeDash .kpi.k1::after{background:var(--a1);}
#fdeDash .kpi.k2::after{background:var(--a2);}
#fdeDash .kpi.k3::after{background:var(--a3);}
#fdeDash .kpi.k4::after{background:var(--a4);}
#fdeDash .kpi-row-top{display:flex;align-items:center;justify-content:space-between;}
#fdeDash .kpi-ico{width:36px;height:36px;border-radius:10px;display:flex;align-items:center;
  justify-content:center;font-size:.95rem;flex-shrink:0;background:var(--gold-bg);}
#fdeDash .i1{color:var(--a1);} #fdeDash .i2{color:var(--a2);}
#fdeDash .i3{color:var(--a3);} #fdeDash .i4{color:var(--a4);}
#fdeDash .kpi-delta{font-size:.68rem;padding:2px 7px;border-radius:8px;font-weight:600;
  display:inline-flex;align-items:center;gap:2px;}
#fdeDash .du{background:rgba(21,128,61,.1);color:var(--ok);}
#fdeDash .dd{background:rgba(220,38,38,.1);color:var(--danger);}
#fdeDash .dn{background:rgba(0,0,0,.06);color:var(--muted);}
[data-theme="dark"] #fdeDash .dn{background:rgba(255,255,255,.07);}
#fdeDash .big-num{font-size:2rem;font-weight:700;color:var(--text);line-height:1;}
#fdeDash .kpi-lbl{font-size:.73rem;color:var(--muted);font-weight:500;}
#fdeDash .kpi-sub{font-size:.69rem;color:var(--light);}
#fdeDash .sp-wrap{height:38px;width:100%;position:relative;margin-top:4px;}
#fdeDash .sp-wrap canvas{position:absolute;inset:0;width:100%!important;height:100%!important;}

/* ── MAIN CHART CARD ── */
#fdeDash .main-chart-card{background:var(--card);border-radius:16px;
  box-shadow:var(--shadow);padding:24px 26px;}
#fdeDash .mc-top{display:flex;align-items:flex-start;justify-content:space-between;
  gap:20px;margin-bottom:20px;flex-wrap:wrap;}
#fdeDash .mc-nums{display:flex;gap:28px;flex-wrap:wrap;}
#fdeDash .mc-num-label{font-size:.7rem;font-weight:700;text-transform:uppercase;
  letter-spacing:.09em;color:var(--muted);margin-bottom:5px;
  display:flex;align-items:center;gap:6px;}
#fdeDash .mc-num-label span{display:inline-block;width:9px;height:9px;border-radius:3px;flex-shrink:0;}
#fdeDash .mc-num-big{font-family:var(--font-body);font-size:2rem;font-weight:700;line-height:1;}
#fdeDash .mc-num-sub{font-size:.7rem;color:var(--muted);margin-top:3px;
  display:flex;align-items:center;gap:4px;}

/* ── BOUNDED CHART WRAPPERS ── */
#fdeDash .ch-220{position:relative;width:100%;height:220px;}
#fdeDash .ch-180{position:relative;width:100%;height:180px;}
#fdeDash .ch-140{position:relative;width:100%;height:140px;}
#fdeDash .ch-half{position:relative;width:100%;max-width:180px;height:110px;}
#fdeDash .ch-d{position:relative;width:130px;height:130px;flex-shrink:0;}
#fdeDash .ch-d-sm{position:relative;width:110px;height:110px;flex-shrink:0;}
#fdeDash .ch-220 canvas,#fdeDash .ch-180 canvas,#fdeDash .ch-140 canvas,
#fdeDash .ch-half canvas,#fdeDash .ch-d canvas,#fdeDash .ch-d-sm canvas{
  position:absolute;inset:0;width:100%!important;height:100%!important;}
#fdeDash .donut-center{position:absolute;inset:0;display:flex;flex-direction:column;
  align-items:center;justify-content:center;pointer-events:none;}
#fdeDash .donut-center-val{font-family:var(--font-body);font-size:1.4rem;
  font-weight:700;color:var(--text);line-height:1;}
#fdeDash .donut-center-lbl{font-size:.6rem;color:var(--muted);}

/* ── GRIDS ── */
#fdeDash .g2{display:grid;grid-template-columns:1fr 1fr;gap:16px;}
#fdeDash .g3{display:grid;grid-template-columns:repeat(3,1fr);gap:16px;}
#fdeDash .g2-3{display:grid;grid-template-columns:2fr 3fr;gap:16px;}
#fdeDash .mb-block{margin-bottom:16px;}

/* ── SECTION LABEL ── */
#fdeDash .sec-row{display:flex;align-items:center;gap:12px;margin:24px 0 14px;}
#fdeDash .sec-line{flex:1;height:1px;background:var(--border);}
#fdeDash .sec-ttl{font-size:.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.1em;
  color:var(--light);white-space:nowrap;}

/* ── STATUS DISTRIBUTION ── */
#fdeDash .status-dist{display:flex;flex-direction:column;gap:8px;}
#fdeDash .sd-row{display:flex;align-items:center;gap:10px;}
#fdeDash .sd-label{font-size:.73rem;color:var(--muted);width:110px;flex-shrink:0;
  white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
#fdeDash .sd-track{flex:1;height:7px;background:rgba(0,0,0,.06);border-radius:4px;overflow:hidden;}
[data-theme="dark"] #fdeDash .sd-track{background:rgba(255,255,255,.07);}
#fdeDash .sd-fill{height:100%;width:0;border-radius:4px;display:block;
  transition:width 1s cubic-bezier(.4,0,.2,1);}
#fdeDash .sd-n{font-size:.72rem;font-weight:600;color:var(--text);min-width:22px;
  text-align:right;flex-shrink:0;}

/* ── MINI METRICS ── */
#fdeDash .mini-metric{background:var(--card2);border-radius:10px;padding:12px 14px;}
#fdeDash .mm-label{font-size:.68rem;color:var(--muted);font-weight:500;margin-bottom:4px;}
#fdeDash .mm-val{font-family:var(--font-body);font-size:1.4rem;font-weight:700;
  color:var(--text);line-height:1;}
#fdeDash .mm-sub{font-size:.68rem;color:var(--muted);margin-top:3px;}
#fdeDash .metric-stack{display:flex;flex-direction:column;gap:8px;flex:1;min-width:130px;}
#fdeDash .metric-grid{display:grid;grid-template-columns:1fr 1fr;gap:8px;}

/* ── TRIAGE TABLE ── */
#fdeDash .tbl-scroll{overflow-x:auto;-webkit-overflow-scrolling:touch;}
#fdeDash .t-tbl{width:100%;min-width:720px;border-collapse:collapse;}
#fdeDash .t-tbl thead th{padding:9px 14px;font-size:.67rem;font-weight:700;text-align:left;
  text-transform:uppercase;letter-spacing:.07em;color:var(--muted);
  background:var(--card2);border-bottom:1px solid var(--border);white-space:nowrap;}
#fdeDash .t-tbl tbody tr{border-bottom:1px solid var(--border);transition:background .1s;}
#fdeDash .t-tbl tbody tr:last-child{border-bottom:none;}
#fdeDash .t-tbl tbody tr:hover{background:var(--gold-bg);}
#fdeDash .t-tbl td{padding:10px 14px;font-size:.79rem;vertical-align:middle;}
#fdeDash .sr-id{font-family:var(--font-body);font-size:.9rem;font-weight:700;color:var(--gold);}
#fdeDash .t-pill{font-size:.65rem;font-weight:700;padding:2px 8px;border-radius:20px;
  display:inline-flex;align-items:center;gap:3px;}
#fdeDash .p-red{background:rgba(220,38,38,.1);color:var(--danger);}
#fdeDash .p-amber{background:rgba(217,119,6,.1);color:var(--warn);}
#fdeDash .p-green{background:rgba(21,128,61,.1);color:var(--ok);}
#fdeDash .p-gold{background:var(--gold-bg);color:var(--gold);}
#fdeDash .p-muted{background:rgba(0,0,0,.06);color:var(--muted);}
[data-theme="dark"] #fdeDash .p-muted{background:rgba(255,255,255,.07);}

/* ── LIST ROWS ── */
#fdeDash .lrow{display:flex;align-items:center;justify-content:space-between;gap:8px;
  font-size:.75rem;padding:8px;border-radius:7px;transition:background .12s;
  background:none;border:none;width:100%;text-align:left;color:inherit;}
#fdeDash .lrow-rule{border-bottom:1px solid var(--border);border-radius:0;}
#fdeDash .lrow-rule:last-child{border-bottom:none;}
#fdeDash .rank{width:22px;height:22px;border-radius:6px;background:var(--gold-bg);
  display:flex;align-items:center;justify-content:center;font-size:.6rem;
  font-weight:700;color:var(--a1);flex-shrink:0;}

/* ── LEGEND ── */
#fdeDash .leg{display:flex;align-items:center;gap:6px;font-size:.73rem;color:var(--muted);}
#fdeDash .leg-dot{width:9px;height:9px;border-radius:3px;flex-shrink:0;}
#fdeDash .leg-row{display:flex;flex-wrap:wrap;gap:12px;}

/* ── EMPTY STATE ── */
#fdeDash .empty{padding:22px 14px;text-align:center;color:var(--muted);font-size:.78rem;margin:0;}
#fdeDash .empty i{display:block;font-size:1.25rem;color:var(--light);margin-bottom:6px;}

/* ── KANBAN BOARD ── */
#fdeDash .kb-board{display:flex;gap:10px;overflow-x:auto;padding-bottom:10px;}
#fdeDash .kb-board::-webkit-scrollbar{height:5px;}
#fdeDash .kb-board::-webkit-scrollbar-thumb{background:var(--border);border-radius:3px;}
#fdeDash .kb-board::-webkit-scrollbar-thumb:hover{background:var(--gold);}
#fdeDash .kb-col{flex-shrink:0;width:210px;display:flex;flex-direction:column;}
#fdeDash .kb-col-hdr{display:flex;align-items:center;gap:7px;padding:8px 10px;
  border-radius:9px 9px 0 0;font-size:.73rem;font-weight:700;}
#fdeDash .kb-col-body{flex:1;background:rgba(0,0,0,.025);border-radius:0 0 9px 9px;
  padding:7px;display:flex;flex-direction:column;gap:7px;min-height:60px;}
[data-theme="dark"] #fdeDash .kb-col-body{background:rgba(255,255,255,.025);}
#fdeDash .kb-card{background:var(--card);border:1px solid var(--border);border-radius:9px;
  padding:10px 11px;cursor:pointer;transition:transform .15s,box-shadow .15s;
  position:relative;overflow:hidden;text-align:left;width:100%;}
#fdeDash .kb-card:hover{transform:translateY(-2px);box-shadow:0 4px 16px rgba(0,0,0,.1);}
#fdeDash .kb-sr{font-family:var(--font-body);font-size:.82rem;font-weight:700;
  color:var(--gold);margin-bottom:3px;}
#fdeDash .kb-client{font-size:.76rem;font-weight:600;color:var(--text);margin-bottom:2px;
  white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
#fdeDash .kb-site{font-size:.67rem;color:var(--muted);white-space:nowrap;
  overflow:hidden;text-overflow:ellipsis;margin-bottom:6px;}
#fdeDash .kb-foot{display:flex;align-items:center;justify-content:space-between;gap:4px;}
#fdeDash .kb-empty{font-size:.72rem;color:var(--light);text-align:center;
  padding:16px 8px;display:flex;flex-direction:column;align-items:center;gap:4px;}

/* ── SLIDE-IN DETAIL PANEL ── */
#fdeDash .panel-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.35);
  z-index:1040;backdrop-filter:blur(3px);}
#fdeDash .panel-overlay.open{display:block;}
#fdeDash .detail-panel{position:fixed;top:0;right:0;width:480px;max-width:100%;
  height:100vh;height:100dvh;background:var(--card);z-index:1050;
  box-shadow:-6px 0 40px rgba(0,0,0,.14);transform:translateX(105%);
  transition:transform .35s cubic-bezier(.4,0,.2,1);
  display:flex;flex-direction:column;overflow:hidden;}
#fdeDash .detail-panel.open{transform:translateX(0);}
#fdeDash .dp-hdr{padding:18px 22px 16px;border-bottom:1px solid var(--border);flex-shrink:0;
  background:linear-gradient(135deg,#9a8053,#b8975e);color:#fff;}
#fdeDash .dp-hdr-top{display:flex;align-items:flex-start;justify-content:space-between;
  gap:12px;margin-bottom:10px;}
#fdeDash .dp-sr-id{font-family:var(--font-body);font-size:1.3rem;font-weight:700;
  color:#fff;line-height:1;}
#fdeDash .dp-close{background:rgba(255,255,255,.2);border:1px solid rgba(255,255,255,.3);
  color:#fff;width:30px;height:30px;border-radius:7px;display:flex;
  align-items:center;justify-content:center;cursor:pointer;font-size:.85rem;flex-shrink:0;}
#fdeDash .dp-close:hover{background:rgba(255,255,255,.35);}
#fdeDash .dp-chips{display:flex;align-items:center;gap:7px;flex-wrap:wrap;}
#fdeDash .dp-chip{font-size:.72rem;font-weight:600;padding:3px 10px;border-radius:20px;
  background:rgba(255,255,255,.18);color:#fff;border:1px solid rgba(255,255,255,.25);
  display:inline-flex;align-items:center;gap:4px;}
#fdeDash .dp-body{flex:1;overflow-y:auto;}
#fdeDash .dp-sec{padding:16px 22px;border-bottom:1px solid var(--border);}
#fdeDash .dp-sec:last-child{border-bottom:none;}
#fdeDash .dp-sec-lbl{font-size:.66rem;font-weight:700;text-transform:uppercase;
  letter-spacing:.09em;color:var(--light);margin-bottom:10px;
  display:flex;align-items:center;gap:5px;}
#fdeDash .dp-sec-lbl i{color:var(--gold);}
#fdeDash .dp-grid{display:grid;grid-template-columns:1fr 1fr;gap:9px;}
#fdeDash .dp-cell{background:var(--card2);border:1px solid var(--border);
  border-radius:8px;padding:9px 11px;}
#fdeDash .dp-cell-lbl{font-size:.66rem;color:var(--muted);font-weight:500;margin-bottom:3px;}
#fdeDash .dp-cell-val{font-size:.8rem;font-weight:600;color:var(--text);}
#fdeDash .dp-cell-val.gold{color:var(--gold);font-family:var(--font-body);}
#fdeDash .issue-block{background:var(--card2);border:1px solid var(--border);
  border-radius:9px;padding:12px 14px;font-size:.8rem;color:var(--text);line-height:1.55;}
#fdeDash .tech-card{display:flex;align-items:center;gap:12px;background:var(--card2);
  border:1px solid var(--border);border-radius:9px;padding:12px 14px;flex-wrap:wrap;}
#fdeDash .tech-av{width:38px;height:38px;border-radius:50%;flex-shrink:0;
  background:linear-gradient(135deg,#9a8053,#b8975e);display:flex;
  align-items:center;justify-content:center;color:#fff;font-size:.8rem;font-weight:700;}
#fdeDash .tech-nm{font-size:.85rem;font-weight:600;color:var(--text);}
#fdeDash .tech-rl{font-size:.72rem;color:var(--muted);}
#fdeDash .unassigned-box{background:var(--card2);border:1px solid var(--border);
  border-radius:9px;padding:14px;text-align:center;font-size:.78rem;color:var(--muted);}
#fdeDash .unassigned-box i{display:block;font-size:1.4rem;color:var(--light);margin-bottom:6px;}
#fdeDash .tl-item{display:flex;gap:10px;padding:7px 0;}
#fdeDash .tl-col{display:flex;flex-direction:column;align-items:center;width:12px;flex-shrink:0;}
#fdeDash .tl-dot{width:10px;height:10px;border-radius:50%;flex-shrink:0;}
#fdeDash .tl-line{width:2px;flex:1;background:var(--border);margin-top:3px;
  min-height:12px;border-radius:1px;}
#fdeDash .tl-right{flex:1;padding-top:1px;}
#fdeDash .tl-event{font-size:.78rem;font-weight:500;color:var(--text);margin-bottom:2px;}
#fdeDash .tl-time{font-size:.69rem;color:var(--muted);}
#fdeDash .rating-row{display:flex;align-items:center;gap:12px;background:var(--card2);
  border:1px solid var(--border);border-radius:9px;padding:12px 14px;flex-wrap:wrap;}
#fdeDash .rating-stars{display:flex;gap:3px;}
#fdeDash .rating-stars i{color:var(--a2);font-size:.95rem;}
#fdeDash .rating-big{font-family:var(--font-body);font-size:1.6rem;font-weight:700;color:var(--gold);}

/* ── TOASTS ── */
#fdeDash .toast-wrap{position:fixed;bottom:22px;right:22px;z-index:1060;
  display:flex;flex-direction:column;gap:8px;pointer-events:none;max-width:calc(100vw - 44px);}
#fdeDash .ti{display:flex;align-items:flex-start;gap:9px;padding:11px 14px;border-radius:10px;
  background:var(--card);border:1px solid var(--border);
  box-shadow:0 4px 20px rgba(0,0,0,.1);min-width:220px;max-width:290px;
  animation:fdeToastIn .2s ease;pointer-events:auto;}
@keyframes fdeToastIn{from{opacity:0;transform:translateY(8px);}to{opacity:1;transform:none;}}
#fdeDash .ti-t{font-size:.79rem;font-weight:600;color:var(--text);margin:0 0 2px;}
#fdeDash .ti-b{font-size:.73rem;color:var(--muted);margin:0;}
#fdeDash .ok{color:var(--ok);} #fdeDash .warn{color:var(--warn);} #fdeDash .info{color:var(--gold);}

/* ── RESPONSIVE ── */
@media (max-width:1200px){
  #fdeDash .kpi-row{grid-template-columns:repeat(2,1fr);}
  #fdeDash .g3{grid-template-columns:1fr 1fr;}
  #fdeDash .g2-3{grid-template-columns:1fr;}
}
@media (max-width:900px){
  #fdeDash .g3{grid-template-columns:1fr;}
  #fdeDash .main-chart-card{padding:20px 18px;}
}
@media (max-width:700px){
  #fdeDash .g2{grid-template-columns:1fr;}
  #fdeDash .greeting-row{flex-direction:column;gap:14px;}
  #fdeDash .filter-bar{width:100%;overflow-x:auto;flex-wrap:nowrap;padding-bottom:4px;}
  #fdeDash .filter-bar > *{flex:0 0 auto;}
}
@media (max-width:575px){
  #fdeDash .card-pad{padding:16px;}
  #fdeDash .main-chart-card{padding:18px 14px;}
  #fdeDash .mc-nums{gap:18px;}
  #fdeDash .ch-220{height:190px;}
  #fdeDash .toast-wrap{left:14px;right:14px;bottom:14px;}
  #fdeDash .ti{max-width:100%;}
}
@media (max-width:420px){
  #fdeDash .kpi-row{grid-template-columns:1fr;}
  #fdeDash .sd-label{width:88px;}
}
@media (prefers-reduced-motion:reduce){
  #fdeDash *,#fdeDash *::before,#fdeDash *::after{
    transition-duration:.01ms!important;animation-duration:.01ms!important;}
}
</style>
@endpush

@section('content')
@php
    // ---- normalise inputs so the view never breaks while you wire up the DB ----
    $kpis            = $kpis            ?? [];
    $alertCounts     = $alertCounts     ?? [];
    $statusBreakdown = $statusBreakdown ?? [];
    $triage          = $triage          ?? [];
    $clients         = $clients         ?? [];
    $clientStats     = $clientStats     ?? [];
    $cancellation    = $cancellation    ?? [];
    $whatsapp        = $whatsapp        ?? [];
    $kanban          = $kanban          ?? [];
    $scope           = $scope           ?? [];
    $intakeStats     = $intakeStats     ?? [];
    $filters         = $filters         ?? [];
    $periodOptions   = $periodOptions   ?? ['today' => 'Today', 'week' => 'This Week', 'month' => 'This Month'];

    $maxStatus   = collect($statusBreakdown)->max(fn ($r) => (int) data_get($r, 'count')) ?: 1;
    $priorityCls = ['High' => 'p-red', 'Medium' => 'p-amber', 'Low' => 'p-green'];
@endphp

<div id="fdeDash">

    {{-- ── GREETING + FILTERS ─────────────────────────────────────────── --}}
    <div class="greeting-row">
        <div>
            <div class="greeting">{{ $greeting ?? 'Front Desk Dashboard' }}</div>
            <div class="greeting-sub">
                <i class="bi bi-calendar3" style="color:var(--gold);margin-right:4px;"></i>
                {{ $today ?? now()->format('l, d F Y') }}
                @isset($greetingSub)
                    &nbsp;·&nbsp; {{ $greetingSub }}
                @endisset
            </div>
        </div>

        {{-- A GET form so the pills actually filter the queries. --}}
        <form method="GET" action="{{ url()->current() }}" class="filter-bar">
            @foreach ($periodOptions as $value => $label)
                <button type="submit" name="period" value="{{ $value }}"
                        class="fq-pill {{ ($filters['period'] ?? 'month') === $value ? 'active' : '' }}">{{ $label }}</button>
            @endforeach
        </form>
    </div>

    {{-- ── ALERT STRIP ────────────────────────────────────────────────── --}}
    <div class="alert-row">
        @if (($alertCounts['triage'] ?? 0) > 0)
            <span class="a-chip a-red">
                <i class="bi bi-hourglass-split"></i>
                {{ $alertCounts['triage'] }} {{ \Illuminate\Support\Str::plural('SR', $alertCounts['triage']) }} awaiting HoP triage — logged by you
            </span>
        @endif

        @if (($alertCounts['stale'] ?? 0) > 0)
            <span class="a-chip a-amb">
                <i class="bi bi-clock-history"></i>
                {{ $alertCounts['stale'] }} waiting 5h or more
            </span>
        @endif

        @if (($alertCounts['wa_failures'] ?? 0) > 0)
            <span class="a-chip a-amb">
                <i class="bi bi-whatsapp"></i>
                {{ $alertCounts['wa_failures'] }} WhatsApp {{ \Illuminate\Support\Str::plural('confirmation', $alertCounts['wa_failures']) }} failed on your SRs
            </span>
        @endif

        @if (($alertCounts['triage'] ?? 0) === 0 && ($alertCounts['wa_failures'] ?? 0) === 0)
            <span class="a-chip a-grn">
                <i class="bi bi-check-circle-fill"></i>
                Nothing needs your attention right now
            </span>
        @endif
    </div>

    {{-- ── KPI ROW · 4 cards ──────────────────────────────────────────── --}}
    <div class="kpi-row">

        <div class="kpi k1">
            <div class="kpi-row-top">
                <span class="kpi-ico i1"><i class="bi bi-ticket-perforated"></i></span>
                @if (data_get($kpis, 'total.delta'))
                    <span class="kpi-delta {{ data_get($kpis, 'total.delta_tone', 'dn') }}">{{ data_get($kpis, 'total.delta') }}</span>
                @endif
            </div>
            <span class="big-num">{{ data_get($kpis, 'total.value', 0) }}</span>
            <span class="kpi-lbl">SRs Logged · {{ $periodLabel ?? 'This Month' }}</span>
            <span class="kpi-sub">{{ data_get($kpis, 'total.sub') }}</span>
            <span class="sp-wrap"><canvas id="fdeSpark0"></canvas></span>
        </div>

        <div class="kpi k2">
            <div class="kpi-row-top">
                <span class="kpi-ico i2"><i class="bi bi-hourglass-split"></i></span>
                @if (data_get($kpis, 'pending.delta'))
                    <span class="kpi-delta {{ data_get($kpis, 'pending.delta_tone', 'dn') }}">{{ data_get($kpis, 'pending.delta') }}</span>
                @endif
            </div>
            <span class="big-num">{{ data_get($kpis, 'pending.value', 0) }}</span>
            <span class="kpi-lbl">Pending Triage</span>
            <span class="kpi-sub">{{ data_get($kpis, 'pending.sub') }}</span>
            <span class="sp-wrap"><canvas id="fdeSpark1"></canvas></span>
        </div>

        <div class="kpi k3">
            <div class="kpi-row-top">
                <span class="kpi-ico i3"><i class="bi bi-x-circle"></i></span>
                @if (data_get($kpis, 'cancelled.delta'))
                    <span class="kpi-delta {{ data_get($kpis, 'cancelled.delta_tone', 'dn') }}">{{ data_get($kpis, 'cancelled.delta') }}</span>
                @endif
            </div>
            <span class="big-num">{{ data_get($kpis, 'cancelled.value', 0) }}</span>
            <span class="kpi-lbl">Cancelled from My Intake</span>
            <span class="kpi-sub">{{ data_get($kpis, 'cancelled.sub') }}</span>
            <span class="sp-wrap"><canvas id="fdeSpark2"></canvas></span>
        </div>

        <div class="kpi k4">
            <div class="kpi-row-top">
                <span class="kpi-ico i4"><i class="bi bi-patch-check"></i></span>
                @if (data_get($kpis, 'completed.delta'))
                    <span class="kpi-delta {{ data_get($kpis, 'completed.delta_tone', 'dn') }}">{{ data_get($kpis, 'completed.delta') }}</span>
                @endif
            </div>
            <span class="big-num">{{ data_get($kpis, 'completed.value', 0) }}</span>
            <span class="kpi-lbl">Completed from My Intake</span>
            <span class="kpi-sub">{{ data_get($kpis, 'completed.sub') }}</span>
            <span class="sp-wrap"><canvas id="fdeSpark3"></canvas></span>
        </div>
    </div>

    {{-- ── STATUS DISTRIBUTION + INTAKE TREND ─────────────────────────── --}}
    <div class="g2-3 mb-block">

        <div class="card card-pad">
            <div class="c-hdr">
                <div class="c-label"><i class="bi bi-bar-chart-steps"></i>My SRs by status</div>
                @if (Route::has('kanban_view'))
                    <a href="{{ route('kanban_view') }}" class="c-more">Ticket summary <i class="bi bi-arrow-right"></i></a>
                @endif
            </div>

            <div class="status-dist" style="margin-bottom:14px;">
                @forelse ($statusBreakdown as $row)
                    <div class="sd-row">
                        <span class="sd-label" title="{{ data_get($row, 'label') }}">{{ data_get($row, 'label') }}</span>
                        <span class="sd-track">
                            <span class="sd-fill"
                                  data-width="{{ round((int) data_get($row, 'count') / $maxStatus * 100) }}%"
                                  style="background:{{ data_get($row, 'color', '#9a8053') }};"></span>
                        </span>
                        <span class="sd-n">{{ data_get($row, 'count') }}</span>
                    </div>
                @empty
                    <p class="empty"><i class="bi bi-inbox"></i>No service requests in this period.</p>
                @endforelse
            </div>

            <div class="metric-grid" style="padding-top:12px;border-top:1px solid var(--border);">
                <div class="mini-metric">
                    <div class="mm-label">Completed from my intake</div>
                    <div class="mm-val" style="color:var(--ok);">{{ $completedCount ?? 0 }}</div>
                    <div class="mm-sub">Fully closed this period</div>
                </div>
                <div class="mini-metric">
                    <div class="mm-label">Cancelled / rejected</div>
                    <div class="mm-val" style="color:var(--danger);">{{ data_get($cancellation, 'count', 0) }}</div>
                    <div class="mm-sub">{{ data_get($cancellation, 'rate', 0) }}% of my total</div>
                </div>
            </div>
        </div>

        <div class="main-chart-card">
            <div class="mc-top">
                <div class="mc-nums">
                    <div>
                        <div class="mc-num-label"><span style="background:#9a8053;"></span>In-warranty</div>
                        <div class="mc-num-big" style="color:#9a8053;">{{ data_get($srTrend ?? [], 'in_warranty_total', 0) }}</div>
                        @if (! is_null(data_get($srTrend ?? [], 'in_warranty_change')))
                            @php $iwChange = (float) data_get($srTrend, 'in_warranty_change'); @endphp
                            <div class="mc-num-sub">
                                <i class="bi bi-arrow-{{ $iwChange >= 0 ? 'up' : 'down' }}"
                                   style="color:{{ $iwChange >= 0 ? 'var(--ok)' : 'var(--danger)' }};"></i>
                                <span style="color:{{ $iwChange >= 0 ? 'var(--ok)' : 'var(--danger)' }};">
                                    {{ $iwChange >= 0 ? '+' : '' }}{{ $iwChange }}%
                                </span>&nbsp;vs last month
                            </div>
                        @endif
                    </div>
                    <div>
                        <div class="mc-num-label"><span style="background:#393837;"></span>Out-of-warranty</div>
                        <div class="mc-num-big" style="color:#393837;">{{ data_get($srTrend ?? [], 'out_warranty_total', 0) }}</div>
                        @if (! is_null(data_get($srTrend ?? [], 'out_warranty_change')))
                            @php $owChange = (float) data_get($srTrend, 'out_warranty_change'); @endphp
                            <div class="mc-num-sub">
                                <i class="bi bi-arrow-{{ $owChange >= 0 ? 'up' : 'down' }}"
                                   style="color:{{ $owChange >= 0 ? 'var(--ok)' : 'var(--danger)' }};"></i>
                                <span style="color:{{ $owChange >= 0 ? 'var(--ok)' : 'var(--danger)' }};">
                                    {{ $owChange >= 0 ? '+' : '' }}{{ $owChange }}%
                                </span>&nbsp;vs last month
                            </div>
                        @endif
                    </div>
                    <div>
                        <div class="mc-num-label">Avg / day</div>
                        <div class="mc-num-big" style="color:var(--text);">{{ data_get($intakeStats, 'perDay', 0) }}</div>
                        <div class="mc-num-sub">{{ data_get($intakeStats, 'today', 0) }} logged today</div>
                    </div>
                </div>

                <span class="c-label" style="margin:0;">Last 6 months</span>
            </div>

            <div class="ch-220"><canvas id="fdeTrend"></canvas></div>
        </div>
    </div>

    {{-- ── SCOPE + CANCELLATIONS + WHATSAPP ───────────────────────────── --}}
    <div class="g3 mb-block">

        {{-- Scope split --}}
        <div class="card card-pad">
            <div class="c-hdr">
                <div class="c-label"><i class="bi bi-pie-chart"></i>Warranty scope split</div>
            </div>

            <div style="display:flex;align-items:center;gap:16px;flex-wrap:wrap;justify-content:center;">
                <div class="ch-d">
                    <canvas id="fdeScope"></canvas>
                    <div class="donut-center">
                        <div class="donut-center-val">{{ data_get($scope, 'total', 0) }}</div>
                        <div class="donut-center-lbl">Total</div>
                    </div>
                </div>
                <div class="metric-stack" style="min-width:120px;">
                    <div class="mini-metric">
                        <div class="mm-label"><span class="leg-dot" style="background:#9a8053;display:inline-block;margin-right:5px;"></span>In-warranty</div>
                        <div class="mm-val" style="color:var(--gold);">{{ data_get($scope, 'iw', 0) }}</div>
                    </div>
                    <div class="mini-metric">
                        <div class="mm-label"><span class="leg-dot" style="background:#393837;display:inline-block;margin-right:5px;"></span>Out-of-warranty</div>
                        <div class="mm-val">{{ data_get($scope, 'oow', 0) }}</div>
                    </div>
                </div>
            </div>

            <div class="metric-grid" style="margin-top:14px;padding-top:12px;border-top:1px solid var(--border);">
                <div class="mini-metric">
                    <div class="mm-label">New clients</div>
                    <div class="mm-val" style="color:var(--gold);">{{ data_get($clientStats, 'new_clients', 0) }}</div>
                    <div class="mm-sub">Accounts created</div>
                </div>
                <div class="mini-metric">
                    <div class="mm-label">New sites</div>
                    <div class="mm-val" style="color:var(--gold);">{{ data_get($clientStats, 'new_sites', 0) }}</div>
                    <div class="mm-sub">Project sites added</div>
                </div>
            </div>
        </div>

        {{-- Cancellations --}}
        <div class="card card-pad">
            <div class="c-hdr">
                <div class="c-label"><i class="bi bi-x-circle"></i>Cancellation visibility</div>
            </div>

            <div style="display:flex;align-items:center;gap:14px;margin-bottom:16px;
                        padding-bottom:14px;border-bottom:1px solid var(--border);flex-wrap:wrap;">
                <div>
                    <div style="font-family:var(--font-body);font-size:2.6rem;
                                font-weight:700;color:var(--danger);line-height:1;">{{ data_get($cancellation, 'rate', 0) }}%</div>
                    <div style="font-size:.72rem;color:var(--muted);margin-top:3px;">
                        Cancellation rate<br>this period
                    </div>
                </div>
                <div style="flex:1;min-width:140px;">
                    <div class="ch-half"><canvas id="fdeCancelGauge"></canvas></div>
                    <div style="text-align:center;margin-top:-10px;font-size:.68rem;color:var(--muted);">
                        {{ data_get($cancellation, 'count', 0) }} of {{ data_get($cancellation, 'total', 0) }} SRs
                    </div>
                </div>
            </div>

            <div style="display:flex;flex-direction:column;gap:8px;">
                @forelse (data_get($cancellation, 'items', []) as $item)
                    <div style="background:var(--card2);border-radius:9px;padding:10px 12px;border:1px solid var(--border);">
                        <div style="font-size:.67rem;color:var(--muted);margin-bottom:3px;">Cancelled from my intake</div>
                        <div style="display:flex;align-items:center;justify-content:space-between;gap:8px;flex-wrap:wrap;">
                            <span class="sr-id" style="font-size:.85rem;">{{ data_get($item, 'code') }}</span>
                            <span class="t-pill p-muted">{{ data_get($item, 'client') }} · {{ data_get($item, 'reason') }}</span>
                        </div>
                    </div>
                @empty
                    <p class="empty"><i class="bi bi-check2-circle"></i>No cancellations in this period.</p>
                @endforelse
            </div>
        </div>

        {{-- WhatsApp --}}
        <div class="card card-pad">
            <div class="c-hdr">
                <div class="c-label"><i class="bi bi-whatsapp"></i>WhatsApp confirmations</div>
                @if (Route::has('wa_notification_log'))
                    <a href="{{ route('wa_notification_log') }}" class="c-more">View log <i class="bi bi-arrow-right"></i></a>
                @endif
            </div>

            <div style="display:flex;align-items:center;gap:14px;margin-bottom:14px;flex-wrap:wrap;">
                <div class="ch-d-sm">
                    <canvas id="fdeWaDonut"></canvas>
                    <div class="donut-center">
                        <div class="donut-center-val" style="font-size:1.2rem;color:var(--gold);">{{ data_get($whatsapp, 'delivery_rate', 0) }}%</div>
                        <div class="donut-center-lbl">Delivered</div>
                    </div>
                </div>
                <div>
                    <div class="leg" style="margin-bottom:6px;">
                        <span class="leg-dot" style="background:#9a8053;"></span>Delivered: <strong style="color:var(--ok);">{{ data_get($whatsapp, 'delivered', 0) }}</strong>
                    </div>
                    <div class="leg">
                        <span class="leg-dot" style="background:rgba(220,38,38,.5);"></span>Failed: <strong style="color:var(--danger);">{{ data_get($whatsapp, 'failed', 0) }}</strong>
                    </div>
                </div>
            </div>

            <div class="sec-ttl" style="margin-bottom:8px;">Failed — needs manual follow-up</div>
            @forelse (data_get($whatsapp, 'failedList', []) as $failure)
                <div style="background:rgba(220,38,38,.05);border:1px solid rgba(220,38,38,.12);
                            border-radius:8px;padding:9px 11px;margin-bottom:7px;">
                    <div style="display:flex;align-items:center;justify-content:space-between;gap:6px;margin-bottom:3px;">
                        <span class="sr-id" style="font-size:.82rem;">{{ data_get($failure, 'code') }}</span>
                        <span class="t-pill p-red" style="font-size:.62rem;">Failed</span>
                    </div>
                    <div style="font-size:.74rem;color:var(--muted);">{{ data_get($failure, 'client') }} &middot; {{ data_get($failure, 'reason') }}</div>
                </div>
            @empty
                <p class="empty">
                    <i class="bi bi-{{ data_get($whatsapp, 'available') ? 'check2-circle' : 'plug' }}"></i>
                    {{ data_get($whatsapp, 'available') ? 'All confirmations delivered.' : 'WhatsApp log not wired up yet.' }}
                </p>
            @endforelse
        </div>
    </div>

    {{-- ── TRIAGE QUEUE ───────────────────────────────────────────────── --}}
    <div class="sec-row">
        <span class="sec-line"></span>
        <span class="sec-ttl"><i class="bi bi-hourglass-split" style="color:var(--gold);margin-right:4px;"></i>Pending triage queue</span>
        <span class="sec-line"></span>
    </div>

    <div class="card mb-block">
        <div class="c-hdr" style="padding:14px 18px;margin-bottom:0;">
            <div class="c-label"><i class="bi bi-list-check"></i>SRs you logged that HoP hasn't reviewed — oldest first</div>
            <span class="t-pill p-red">{{ $alertCounts['triage'] ?? 0 }} awaiting HoP</span>
        </div>

        <div class="tbl-scroll">
            <table class="t-tbl">
                <thead>
                    <tr>
                        <th style="padding-left:18px;">SR ID</th>
                        <th>Client</th>
                        <th>Site</th>
                        <th>Category</th>
                        <th>Priority</th>
                        <th>Waiting</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($triage as $row)
                        <tr>
                            <td style="padding-left:18px;"><span class="sr-id">{{ data_get($row, 'code') }}</span></td>
                            <td style="font-weight:500;color:var(--text);">{{ data_get($row, 'client') }}</td>
                            <td style="font-size:.76rem;color:var(--muted);">{{ data_get($row, 'site') }}</td>
                            <td style="font-size:.76rem;color:var(--muted);">{{ data_get($row, 'category') }}</td>
                            <td>
                                <span class="t-pill {{ $priorityCls[data_get($row, 'priority')] ?? 'p-muted' }}">{{ data_get($row, 'priority') }}</span>
                            </td>
                            <td>
                                <span style="font-size:.76rem;font-weight:{{ data_get($row, 'stale') ? '700' : '500' }};
                                             color:{{ data_get($row, 'stale') ? 'var(--gold)' : 'var(--muted)' }};">
                                    @if (data_get($row, 'stale'))
                                        <i class="bi bi-exclamation-circle-fill" style="margin-right:3px;"></i>
                                    @endif
                                    {{ data_get($row, 'wait') }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6"><p class="empty"><i class="bi bi-inbox"></i>Nothing waiting on triage.</p></td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ── CLIENTS + DAILY INTAKE ─────────────────────────────────────── --}}
    <div class="sec-row">
        <span class="sec-line"></span>
        <span class="sec-ttl"><i class="bi bi-buildings" style="color:var(--gold);margin-right:4px;"></i>Client activity &amp; live board</span>
        <span class="sec-line"></span>
    </div>

    <div class="g2 mb-block">

        {{-- Top clients --}}
        <div class="card card-pad">
            <div class="c-hdr">
                <div class="c-label"><i class="bi bi-buildings"></i>Top clients — my intake</div>
                @if (Route::has('clients.directory'))
                    <a href="{{ route('clients.directory') }}" class="c-more">All clients <i class="bi bi-arrow-right"></i></a>
                @endif
            </div>

            <div class="ch-180"><canvas id="fdeClientChart"></canvas></div>

            <div style="margin-top:12px;">
                @forelse ($clients as $client)
                    <div class="lrow lrow-rule">
                        <span style="display:flex;align-items:center;gap:8px;min-width:0;">
                            <span class="rank">{{ $loop->iteration }}</span>
                            <span style="min-width:0;">
                                <span style="font-weight:500;color:var(--text);display:block;">{{ data_get($client, 'name') }}</span>
                                @if (data_get($client, 'is_new'))
                                    <span style="font-size:.68rem;color:var(--gold);">New client this period</span>
                                @endif
                            </span>
                        </span>
                        <span style="display:flex;align-items:center;gap:6px;">
                            <strong style="color:var(--text);">{{ data_get($client, 'srs') }}</strong>
                            <span style="color:var(--muted);">SRs</span>
                        </span>
                    </div>
                @empty
                    <p class="empty"><i class="bi bi-building"></i>No client activity in this period.</p>
                @endforelse
            </div>
        </div>

        {{-- Daily intake --}}
        <div class="card card-pad">
            <div class="c-hdr">
                <div class="c-label"><i class="bi bi-graph-up-arrow"></i>My intake, last 7 days</div>
            </div>
            <div class="ch-180"><canvas id="fdeDailyChart"></canvas></div>
            <div class="metric-grid" style="margin-top:14px;padding-top:12px;border-top:1px solid var(--border);">
                <div class="mini-metric">
                    <div class="mm-label">Logged this period</div>
                    <div class="mm-val" style="color:var(--gold);">{{ data_get($intakeStats, 'total', 0) }}</div>
                </div>
                <div class="mini-metric">
                    <div class="mm-label">Logged today</div>
                    <div class="mm-val">{{ data_get($intakeStats, 'today', 0) }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── KANBAN BOARD ───────────────────────────────────────────────── --}}
    <div class="card mb-block">
        <div class="c-hdr" style="padding:14px 18px;margin-bottom:0;">
            <div class="c-label"><i class="bi bi-kanban"></i>SRs you logged — across all stages</div>
            @if (Route::has('kanban_view'))
                <a href="{{ route('kanban_view') }}" class="t-pill p-gold" style="padding:5px 12px;font-size:.73rem;">
                    <i class="bi bi-fullscreen"></i> Open full board
                </a>
            @endif
        </div>
        <div style="padding:0 16px 16px;">
            <div class="kb-board" id="fdeBoard"></div>
        </div>
    </div>

    {{-- ── SLIDE-IN DETAIL PANEL ──────────────────────────────────────── --}}
    <div class="panel-overlay" id="fdePanelOverlay"></div>
    <aside class="detail-panel" id="fdeDetailPanel" role="dialog" aria-modal="true" aria-labelledby="fdePanelId">
        <div class="dp-hdr">
            <div class="dp-hdr-top">
                <div>
                    <div class="dp-sr-id" id="fdePanelId"></div>
                    <div class="dp-chips" id="fdePanelChips" style="margin-top:8px;"></div>
                </div>
                <button type="button" class="dp-close" id="fdePanelClose" aria-label="Close panel"><i class="bi bi-x-lg"></i></button>
            </div>
        </div>
        <div class="dp-body" id="fdePanelBody"></div>
    </aside>

    <div class="toast-wrap" id="fdeToasts" aria-live="polite"></div>
</div>
@endsection

@push('scripts')
@php
    // Built here rather than inline: the json directive splits its argument on
    // commas, so any expression containing a comma must be a variable first.
    $fdeChartData = [
        'sparks' => [
            data_get($kpis, 'total.spark', []),
            data_get($kpis, 'pending.spark', []),
            data_get($kpis, 'cancelled.spark', []),
            data_get($kpis, 'completed.spark', []),
        ],
        'trend'    => $srTrend ?? null,
        'scope'    => $scope ?? null,
        'whatsapp' => $whatsapp ?? null,
        'cancel'   => $cancellation ?? null,
        'clients'  => collect($clients ?? [])
            ->map(fn ($c) => [
                'name' => data_get($c, 'short_name') ?? data_get($c, 'name'),
                'srs'  => (int) data_get($c, 'srs'),
            ])
            ->values()
            ->all(),
        'kanban'   => $kanban ?? [],
    ];

    $triageAlert = (int) ($alertCounts['triage'] ?? 0);

    $fdeToast = [
        'tone'  => $triageAlert > 0 ? 'warn' : 'ok',
        'title' => 'Dashboard ready',
        'body'  => $triageAlert > 0
            ? $triageAlert.' '.\Illuminate\Support\Str::plural('SR', $triageAlert).' pending HoP review.'
            : 'Nothing pending review.',
    ];
@endphp
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
window.FDE_DASH  = @json($fdeChartData);
window.FDE_TOAST = @json($fdeToast);
</script>
<script>
(function () {
    'use strict';

    const root = document.getElementById('fdeDash');
    if (!root) return;

    const DATA   = window.FDE_DASH || {};
    const GOLD   = '#9a8053';
    const INK    = '#393837';
    const charts = {};

    const PRIORITY_COLOR = { High: '#dc2626', Medium: '#d97706', Low: '#15803d' };
    const PRIORITY_BG    = { High: 'rgba(220,38,38,.1)', Medium: 'rgba(217,119,6,.1)', Low: 'rgba(21,128,61,.1)' };
    const SCOPE_COLOR    = { IW: '#9a8053', OoW: '#ef4444' };
    const SCOPE_BG       = { IW: 'rgba(154,128,83,.1)', OoW: 'rgba(239,68,68,.1)' };

    /* ---------------------------------------------------------- theming */
    function palette() {
        const dark = document.documentElement.getAttribute('data-theme') === 'dark';
        return {
            grid : dark ? 'rgba(255,255,255,.06)' : 'rgba(0,0,0,.05)',
            text : dark ? '#e8e0d4' : '#1a1614',
            muted: dark ? '#7a756e' : '#8a8480',
        };
    }

    const tooltipStyle = {
        backgroundColor: 'rgba(15,15,15,.9)',
        cornerRadius: 8,
        padding: 10,
        titleColor: '#fff',
        bodyColor: 'rgba(255,255,255,.75)',
    };

    /* --------------------------------------------------------- escaping */
    // The board and panel build markup with innerHTML from database values,
    // so everything interpolated has to be escaped.
    function esc(value) {
        return String(value ?? '').replace(/[&<>"']/g, (ch) => ({
            '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;',
        }[ch]));
    }

    /* ------------------------------------------------------ bar fill-in */
    function animateBars() {
        root.querySelectorAll('[data-width]').forEach((el) => {
            requestAnimationFrame(() => { el.style.width = el.dataset.width; });
        });
    }

    /* ----------------------------------------------------------- charts */
    function buildCharts() {
        if (typeof Chart === 'undefined') return;
        const C = palette();

        /* KPI sparklines */
        (DATA.sparks || []).forEach((series, i) => {
            const canvas = document.getElementById('fdeSpark' + i);
            if (!canvas || !series || !series.length) return;
            const colors = [GOLD, '#b8975e', '#7d6742', '#c7ab7c'];
            const color  = colors[i % colors.length];
            const grad   = canvas.getContext('2d').createLinearGradient(0, 0, 0, 38);
            grad.addColorStop(0, color + '55');
            grad.addColorStop(1, color + '00');

            charts['spark' + i] = new Chart(canvas, {
                type: 'line',
                data: {
                    labels: series.map(() => ''),
                    datasets: [{
                        data: series, borderColor: color, borderWidth: 2,
                        pointRadius: 0, fill: true, backgroundColor: grad, tension: .4,
                    }],
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    plugins: { legend: { display: false }, tooltip: { enabled: false } },
                    scales: { x: { display: false }, y: { display: false } },
                },
            });
        });

        /* Intake trend */
        const trendEl = document.getElementById('fdeTrend');
        if (trendEl && DATA.trend && DATA.trend.labels) {
            const ctx = trendEl.getContext('2d');
            const gIw = ctx.createLinearGradient(0, 0, 0, 220);
            gIw.addColorStop(0, 'rgba(154,128,83,.35)');
            gIw.addColorStop(1, 'rgba(154,128,83,0)');
            const gOw = ctx.createLinearGradient(0, 0, 0, 220);
            gOw.addColorStop(0, 'rgba(57,56,55,.3)');
            gOw.addColorStop(1, 'rgba(57,56,55,0)');

            charts.trend = new Chart(trendEl, {
                type: 'line',
                data: {
                    labels: DATA.trend.labels,
                    datasets: [
                        {
                            label: 'In-warranty', data: DATA.trend.in_warranty || [],
                            borderColor: GOLD, borderWidth: 2.5, backgroundColor: gIw,
                            fill: true, tension: .4, pointRadius: 4,
                            pointBackgroundColor: GOLD, pointHoverRadius: 6,
                        },
                        {
                            label: 'Out-of-warranty', data: DATA.trend.out_warranty || [],
                            borderColor: INK, borderWidth: 2.5, backgroundColor: gOw,
                            fill: true, tension: .4, pointRadius: 4,
                            pointBackgroundColor: INK, pointHoverRadius: 6,
                        },
                    ],
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    plugins: { legend: { display: false }, tooltip: tooltipStyle },
                    scales: {
                        x: { grid: { color: C.grid }, ticks: { color: C.muted, font: { size: 11 } } },
                        y: { grid: { color: C.grid }, ticks: { color: C.muted, font: { size: 11 } }, beginAtZero: true },
                    },
                },
            });
        }

        /* Scope donut */
        const scopeEl = document.getElementById('fdeScope');
        if (scopeEl && DATA.scope) {
            charts.scope = new Chart(scopeEl, {
                type: 'doughnut',
                data: {
                    labels: ['In-warranty', 'Out-of-warranty'],
                    datasets: [{
                        data: [DATA.scope.iw || 0, DATA.scope.oow || 0],
                        backgroundColor: [GOLD, INK], borderWidth: 0, hoverOffset: 4,
                    }],
                },
                options: {
                    responsive: true, maintainAspectRatio: false, cutout: '70%',
                    plugins: { legend: { display: false }, tooltip: tooltipStyle },
                },
            });
        }

        /* Cancellation gauge */
        const cancelEl = document.getElementById('fdeCancelGauge');
        if (cancelEl && DATA.cancel) {
            const rate = Number(DATA.cancel.rate || 0);
            charts.cancel = new Chart(cancelEl, {
                type: 'doughnut',
                data: {
                    labels: ['Cancelled', 'Active'],
                    datasets: [{
                        data: [rate, Math.max(0, 100 - rate)],
                        backgroundColor: ['rgba(220,38,38,.7)', 'rgba(0,0,0,.06)'],
                        borderWidth: 0, hoverOffset: 0,
                    }],
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    cutout: '78%', rotation: -90, circumference: 180,
                    plugins: { legend: { display: false }, tooltip: { enabled: false } },
                },
            });
        }

        /* WhatsApp donut */
        const waEl = document.getElementById('fdeWaDonut');
        if (waEl && DATA.whatsapp) {
            charts.wa = new Chart(waEl, {
                type: 'doughnut',
                data: {
                    labels: ['Delivered', 'Failed'],
                    datasets: [{
                        data: [DATA.whatsapp.delivered || 0, DATA.whatsapp.failed || 0],
                        backgroundColor: [GOLD, 'rgba(220,38,38,.25)'],
                        borderColor: [GOLD, 'rgba(220,38,38,.4)'],
                        borderWidth: 2, hoverOffset: 4,
                    }],
                },
                options: {
                    responsive: true, maintainAspectRatio: false, cutout: '72%',
                    plugins: { legend: { display: false }, tooltip: tooltipStyle },
                },
            });
        }

        /* Top clients (horizontal bars) */
        const clientEl = document.getElementById('fdeClientChart');
        if (clientEl && (DATA.clients || []).length) {
            charts.clients = new Chart(clientEl, {
                type: 'bar',
                data: {
                    labels: DATA.clients.map((c) => c.name),
                    datasets: [{
                        label: 'SRs', data: DATA.clients.map((c) => c.srs),
                        backgroundColor: 'rgba(154,128,83,.75)', borderRadius: 4, borderSkipped: false,
                    }],
                },
                options: {
                    responsive: true, maintainAspectRatio: false, indexAxis: 'y',
                    plugins: { legend: { display: false }, tooltip: tooltipStyle },
                    scales: {
                        x: { grid: { color: C.grid }, ticks: { color: C.muted, font: { size: 10 }, precision: 0 } },
                        y: { grid: { display: false }, ticks: { color: C.text, font: { size: 10 } } },
                    },
                },
            });
        }

        /* Daily intake — reuses the first KPI sparkline series */
        const dailyEl = document.getElementById('fdeDailyChart');
        const daily   = (DATA.sparks || [])[0] || [];
        if (dailyEl && daily.length) {
            const labels = daily.map((_, i) => {
                const d = new Date();
                d.setDate(d.getDate() - (daily.length - 1 - i));
                return d.toLocaleDateString([], { weekday: 'short' });
            });
            charts.daily = new Chart(dailyEl, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'SRs logged', data: daily,
                        backgroundColor: 'rgba(154,128,83,.75)', borderRadius: 5, borderSkipped: false,
                    }],
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    plugins: { legend: { display: false }, tooltip: tooltipStyle },
                    scales: {
                        x: { grid: { display: false }, ticks: { color: C.muted, font: { size: 10 } } },
                        y: { grid: { color: C.grid }, ticks: { color: C.muted, font: { size: 10 }, precision: 0 }, beginAtZero: true },
                    },
                },
            });
        }
    }

    /* Re-tint charts when the app's theme toggle flips data-theme */
    function refreshCharts() {
        const C = palette();
        Object.values(charts).forEach((chart) => {
            if (!chart || !chart.options || !chart.options.scales) return;
            ['x', 'y'].forEach((axis) => {
                const scale = chart.options.scales[axis];
                if (!scale) return;
                if (scale.grid && scale.grid.color) scale.grid.color = C.grid;
                if (scale.ticks) scale.ticks.color = (axis === 'y' && chart === charts.clients) ? C.text : C.muted;
            });
            chart.update('none');
        });
    }

    new MutationObserver(refreshCharts).observe(document.documentElement, {
        attributes: true, attributeFilter: ['data-theme'],
    });

    /* ------------------------------------------------------------ toast */
    function toast(tone, title, body) {
        const wrap = document.getElementById('fdeToasts');
        if (!wrap) return;
        const icons = { ok: 'bi-check-circle-fill ok', warn: 'bi-exclamation-triangle-fill warn', info: 'bi-info-circle-fill info' };
        const el = document.createElement('div');
        el.className = 'ti';
        el.innerHTML = '<i class="bi ' + (icons[tone] || icons.info) + '" style="font-size:.95rem;flex-shrink:0;margin-top:1px;"></i>'
                     + '<div><p class="ti-t"></p><p class="ti-b"></p></div>';
        el.querySelector('.ti-t').textContent = title;
        el.querySelector('.ti-b').textContent = body;
        wrap.appendChild(el);
        setTimeout(() => {
            el.style.transition = 'opacity .3s';
            el.style.opacity = '0';
            setTimeout(() => el.remove(), 300);
        }, 3600);
    }

    /* ----------------------------------------------------------- kanban */
    function renderBoard() {
        const board = document.getElementById('fdeBoard');
        if (!board) return;

        board.innerHTML = (DATA.kanban || []).map((col) => {
            const cards = !col.cards || !col.cards.length
                ? '<div class="kb-empty"><i class="bi bi-inbox" style="font-size:1.2rem;"></i><span>No tickets</span></div>'
                : col.cards.map((card, i) =>
                    '<button type="button" class="kb-card" data-col="' + esc(col.key) + '" data-index="' + i + '">'
                  +   '<span style="position:absolute;left:0;top:0;bottom:0;width:3px;background:' + esc(col.color) + ';border-radius:3px 0 0 3px;"></span>'
                  +   '<span class="kb-sr" style="display:block;">' + esc(card.code) + '</span>'
                  +   '<span class="kb-client" style="display:block;">' + esc(card.client) + '</span>'
                  +   '<span class="kb-site" style="display:block;"><i class="bi bi-geo-alt" style="font-size:.6rem;color:var(--gold);"></i> ' + esc(card.site) + '</span>'
                  +   '<span class="kb-foot">'
                  +     '<span class="t-pill" style="background:' + (PRIORITY_BG[card.priority] || 'rgba(0,0,0,.06)') + ';color:' + (PRIORITY_COLOR[card.priority] || '#8a8480') + ';font-size:.58rem;">' + esc(card.priority) + '</span>'
                  +     '<span class="t-pill" style="background:' + (SCOPE_BG[card.scope] || 'rgba(0,0,0,.06)') + ';color:' + (SCOPE_COLOR[card.scope] || '#8a8480') + ';font-size:.58rem;">' + esc(card.scope) + '</span>'
                  +   '</span>'
                  + '</button>').join('');

            return '<div class="kb-col">'
                 +   '<div class="kb-col-hdr" style="background:' + esc(col.color) + '18;border-bottom:2px solid ' + esc(col.color) + ';">'
                 +     '<span style="color:' + esc(col.color) + ';flex:1;">' + esc(col.label) + '</span>'
                 +     '<span style="font-size:.65rem;padding:1px 6px;border-radius:8px;background:' + esc(col.color) + '22;color:' + esc(col.color) + ';">' + (col.total || 0) + '</span>'
                 +   '</div>'
                 +   '<div class="kb-col-body">' + cards + '</div>'
                 + '</div>';
        }).join('');
    }

    /* ------------------------------------------------------ detail panel */
    const panel   = document.getElementById('fdeDetailPanel');
    const overlay = document.getElementById('fdePanelOverlay');

    function buildTimeline(card) {
        const events = [{ dot: '#64748b', event: 'SR logged by you', time: card.logged + ' ago' }];
        if (card.scope === 'OoW') events.push({ dot: '#7c3aed', event: 'Routed to Accounts — out-of-warranty scope', time: 'HoP reviewed' });
        if (card.erp)      events.push({ dot: '#9a8053', event: 'Quote uploaded · ' + card.erp, time: 'Awaiting client approval' });
        if (card.tech)     events.push({ dot: '#2563eb', event: 'Assigned to ' + card.tech, time: 'Dispatched by HoP' });
        if (card.punched)  events.push({ dot: '#d97706', event: 'Technician punched in on-site', time: card.punched });
        if (card.punchout) events.push({ dot: '#ea580c', event: 'Punch-out — submitted for QC review', time: card.punchout });
        if (card.amount && !card.invoice) events.push({ dot: '#7c3aed', event: 'QC approved — invoice pending upload', time: card.amount });
        if (card.invoice)  events.push({ dot: '#059669', event: 'Invoice finalised · ' + card.invoice, time: 'Client notified via WhatsApp' });
        if (card.rating)   events.push({ dot: '#f59e0b', event: 'Client submitted feedback — ' + card.rating + '\u2605', time: 'SR fully closed' });

        return events.map((e, i) =>
            '<div class="tl-item">'
          +   '<div class="tl-col">'
          +     '<div class="tl-dot" style="background:' + e.dot + ';"></div>'
          +     (i < events.length - 1 ? '<div class="tl-line" style="background:' + e.dot + '44;"></div>' : '')
          +   '</div>'
          +   '<div class="tl-right"><div class="tl-event">' + esc(e.event) + '</div><div class="tl-time">' + esc(e.time) + '</div></div>'
          + '</div>').join('');
    }

    function openPanel(card, stageLabel) {
        document.getElementById('fdePanelId').textContent = card.code;
        document.getElementById('fdePanelChips').innerHTML =
            '<span class="dp-chip"><i class="bi bi-circle-fill" style="font-size:.4rem;"></i>' + esc(stageLabel) + '</span>'
          + '<span class="dp-chip" style="background:' + (PRIORITY_COLOR[card.priority] || '#8a8480') + '33;">' + esc(card.priority) + ' priority</span>'
          + '<span class="dp-chip" style="background:' + (SCOPE_COLOR[card.scope] || '#8a8480') + '33;">' + esc(card.scope) + '</span>';

        let body = '';

        body += '<div class="dp-sec"><div class="dp-sec-lbl"><i class="bi bi-chat-text-fill"></i>Issue description</div>'
             +  '<div class="issue-block">' + esc(card.issue) + '</div></div>';

        body += '<div class="dp-sec"><div class="dp-sec-lbl"><i class="bi bi-info-circle-fill"></i>SR details</div><div class="dp-grid">'
             +    '<div class="dp-cell"><div class="dp-cell-lbl">SR reference</div><div class="dp-cell-val gold">' + esc(card.code) + '</div></div>'
             +    '<div class="dp-cell"><div class="dp-cell-lbl">Service category</div><div class="dp-cell-val">' + esc(card.category) + '</div></div>'
             +    '<div class="dp-cell"><div class="dp-cell-lbl">Priority</div><div class="dp-cell-val" style="color:' + (PRIORITY_COLOR[card.priority] || 'inherit') + ';">' + esc(card.priority) + '</div></div>'
             +    '<div class="dp-cell"><div class="dp-cell-lbl">Warranty scope</div><div class="dp-cell-val" style="color:' + (SCOPE_COLOR[card.scope] || 'inherit') + ';">' + esc(card.scope) + '</div></div>'
             +  '</div></div>';

        body += '<div class="dp-sec"><div class="dp-sec-lbl"><i class="bi bi-buildings"></i>Client &amp; site</div><div class="dp-grid">'
             +    '<div class="dp-cell" style="grid-column:1/-1"><div class="dp-cell-lbl">Client</div><div class="dp-cell-val">' + esc(card.client) + '</div></div>'
             +    '<div class="dp-cell" style="grid-column:1/-1"><div class="dp-cell-lbl">Site / location</div><div class="dp-cell-val">' + esc(card.site) + '</div></div>'
             +  '</div></div>';

        body += '<div class="dp-sec"><div class="dp-sec-lbl"><i class="bi bi-person-badge"></i>Technician</div>';
        if (card.tech) {
            const punchBlock = card.punchout
                ? '<div style="margin-left:auto;text-align:right;"><div style="font-size:.66rem;color:var(--muted);">Punched out</div><div style="font-size:.8rem;font-weight:600;color:var(--gold);">' + esc(card.punchout) + '</div></div>'
                : (card.punched
                    ? '<div style="margin-left:auto;text-align:right;"><div style="font-size:.66rem;color:var(--muted);">Punched in</div><div style="font-size:.8rem;font-weight:600;color:var(--gold);">' + esc(card.punched) + '</div></div>'
                    : '');
            body += '<div class="tech-card">'
                 +    '<div class="tech-av">' + esc(card.initials) + '</div>'
                 +    '<div><div class="tech-nm">' + esc(card.tech) + '</div><div class="tech-rl">' + esc(card.category) + ' specialist</div></div>'
                 +    punchBlock
                 +  '</div>';
        } else {
            body += '<div class="unassigned-box"><i class="bi bi-person-plus"></i>Not yet assigned</div>';
        }
        body += '</div>';

        body += '<div class="dp-sec"><div class="dp-sec-lbl"><i class="bi bi-clock-history"></i>Activity timeline</div>' + buildTimeline(card) + '</div>';

        if (card.erp || card.invoice || card.amount) {
            body += '<div class="dp-sec"><div class="dp-sec-lbl"><i class="bi bi-receipt"></i>Financial reference</div><div class="dp-grid">'
                 +    (card.erp     ? '<div class="dp-cell"><div class="dp-cell-lbl">ERP quote ref</div><div class="dp-cell-val gold" style="font-size:.74rem;">' + esc(card.erp) + '</div></div>' : '')
                 +    (card.invoice ? '<div class="dp-cell"><div class="dp-cell-lbl">Invoice ref</div><div class="dp-cell-val gold" style="font-size:.74rem;">' + esc(card.invoice) + '</div></div>' : '')
                 +    (card.amount  ? '<div class="dp-cell"><div class="dp-cell-lbl">Invoice total</div><div class="dp-cell-val gold">' + esc(card.amount) + '</div></div>' : '')
                 +  '</div></div>';
        }

        if (card.rating) {
            let stars = '';
            for (let s = 1; s <= 5; s++) stars += '<i class="bi bi-star' + (s <= card.rating ? '-fill' : '') + '"></i>';
            body += '<div class="dp-sec"><div class="dp-sec-lbl"><i class="bi bi-star-fill"></i>Client feedback</div>'
                 +    '<div class="rating-row">'
                 +      '<div class="rating-stars">' + stars + '</div>'
                 +      '<div class="rating-big">' + esc(card.rating) + '.0</div>'
                 +      '<span style="font-size:.75rem;color:var(--muted);">Client satisfaction score</span>'
                 +    '</div></div>';
        }

        document.getElementById('fdePanelBody').innerHTML = body;
        overlay.classList.add('open');
        panel.classList.add('open');
        document.getElementById('fdePanelClose').focus();
    }

    function closePanel() {
        overlay.classList.remove('open');
        panel.classList.remove('open');
    }

    root.addEventListener('click', (e) => {
        const cardEl = e.target.closest('.kb-card');
        if (cardEl) {
            const col  = (DATA.kanban || []).find((c) => c.key === cardEl.dataset.col);
            const card = col && col.cards[parseInt(cardEl.dataset.index, 10)];
            if (card) openPanel(card, col.label);
            return;
        }
        if (e.target.closest('#fdePanelClose') || e.target === overlay) closePanel();
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && panel.classList.contains('open')) closePanel();
    });

    /* ------------------------------------------------------------- boot */
    renderBoard();
    buildCharts();
    animateBars();

    if (window.FDE_TOAST) {
        toast(window.FDE_TOAST.tone, window.FDE_TOAST.title, window.FDE_TOAST.body);
    }

    window.fdeDashToast = toast;
})();
</script>
@endpush

{{--
|--------------------------------------------------------------------------
| Controller contract
|--------------------------------------------------------------------------
| return view('frontdesk.dashboard', [
|     'greeting'      => 'Good morning, Sara!',
|     'greetingSub'   => "Here's your intake overview",
|     'today'         => now()->format('l, d F Y'),
|     'periodLabel'   => 'This Month',
|     'filters'       => ['period' => 'month'],
|     'periodOptions' => ['today' => 'Today', 'week' => 'This Week', 'month' => 'This Month'],
|
|     'alertCounts' => ['triage' => 3, 'wa_failures' => 2, 'stale' => 1],
|
|     'kpis' => [   // delta_tone: du (good) | dd (bad) | dn (neutral)
|         'total'     => ['value' => 18, 'sub' => '...', 'delta' => '+2',  'delta_tone' => 'du', 'spark' => [...]],
|         'pending'   => ['value' => 3,  'sub' => '...', 'delta' => 'Oldest 18h', 'delta_tone' => 'dd', 'spark' => [...]],
|         'cancelled' => ['value' => 2,  'sub' => '...', 'delta' => '11%', 'delta_tone' => 'dn', 'spark' => [...]],
|         'completed' => ['value' => 16, 'sub' => '...', 'delta' => '+4',  'delta_tone' => 'du', 'spark' => [...]],
|     ],
|
|     'statusBreakdown' => [['key' => 'inquiry', 'label' => 'Inquiry Logged', 'count' => 3, 'color' => '#64748b']],
|
|     'triage' => [['code' => 'SR-2025-00048', 'client' => '...', 'site' => '...',
|                   'category' => 'Mechanical', 'priority' => 'High', 'wait' => '18h', 'stale' => true]],
|
|     'srTrend' => ['labels' => [...], 'in_warranty' => [...], 'out_warranty' => [...],
|                   'in_warranty_total' => 13, 'out_warranty_total' => 5,
|                   'in_warranty_change' => 18.2, 'out_warranty_change' => -16.7],
|
|     'intakeStats' => ['total' => 18, 'perDay' => 1.2, 'today' => 3],
|     'scope'       => ['iw' => 13, 'oow' => 5, 'total' => 18],
|
|     'cancellation' => ['count' => 2, 'total' => 18, 'rate' => 11,
|                        'items' => [['code' => '...', 'client' => '...', 'reason' => 'Quote rejected']]],
|
|     'whatsapp' => ['delivered' => 16, 'failed' => 2, 'delivery_rate' => 89, 'available' => true,
|                    'failedList' => [['code' => '...', 'client' => '...', 'reason' => 'Invalid number']]],
|
|     'clients'     => [['id' => 1, 'name' => '...', 'short_name' => '...', 'srs' => 5, 'is_new' => false]],
|     'clientStats' => ['new_clients' => 3, 'new_sites' => 2],
|
|     'completedCount' => 16,
|
|     'kanban' => [['key' => 'inquiry', 'label' => 'Inquiry Logged', 'color' => '#64748b', 'total' => 3,
|                   'cards' => [['code' => '...', 'client' => '...', 'site' => '...', 'priority' => 'High',
|                                'scope' => 'OoW', 'category' => '...', 'logged' => '18h', 'tech' => null,
|                                'initials' => null, 'issue' => '...', 'punched' => null, 'punchout' => null,
|                                'erp' => null, 'invoice' => null, 'amount' => null, 'rating' => null]]]],
| ]);
--}}