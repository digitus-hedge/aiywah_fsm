{{--
|--------------------------------------------------------------------------
| Admin Dashboard
|--------------------------------------------------------------------------
| resources/views/admin/dashboard.blade.php
|
| No placeholder data — every value comes from the controller.
| See the variable contract at the bottom of this file.
--}}

@extends('layouts.layout')

@section('title', 'Admin Dashboard')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<style>
/* ==========================================================================
   ADMIN DASHBOARD — all rules scoped to #adminDash so nothing leaks into
   the rest of the app (and Bootstrap's .card etc. can't override these).
   ========================================================================== */

footer.footer { display: none; }

#adminDash{
  --card:#fff; --card2:#faf9f7;
  --border:rgba(0,0,0,.07);
  --shadow:0 2px 20px rgba(0,0,0,.06);
  --shadow-hover:0 8px 32px rgba(0,0,0,.11);
  --text:#1a1614; --muted:#8a8480; --light:#bbb8b4;
  --gold:#9a8053; --gold2:#b8975e; --gold-bg:rgba(154,128,83,.1);
  --ok:#15803d; --warn:#d97706; --danger:#dc2626;
  --blue:#2563eb; --violet:#7c3aed; --slate:#64748b; --ink:#393837;

  font-family:var(--font-header);
  font-size:.875rem;
  color:var(--text);
}
[data-theme="dark"] #adminDash{
  --card:#1e1b18; --card2:#252220;
  --border:rgba(255,255,255,.07);
  --shadow:0 2px 20px rgba(0,0,0,.4);
  --shadow-hover:0 8px 32px rgba(0,0,0,.5);
  --text:#e8e0d4; --muted:#7a756e; --light:#4a4540;
  --gold-bg:rgba(154,128,83,.12);
}

#adminDash *,#adminDash *::before,#adminDash *::after{box-sizing:border-box;}
#adminDash a{text-decoration:none;}
#adminDash .cg,#adminDash h1,#adminDash h2,#adminDash h3,#adminDash h4,
#adminDash .greeting,#adminDash .big-num,#adminDash .panel-heading{
  font-family:var(--font-body);letter-spacing:-.01em;}
#adminDash button:focus-visible,#adminDash select:focus-visible,
#adminDash [tabindex]:focus-visible{outline:2px solid var(--gold);outline-offset:2px;}

/* ── HEADER / GREETING ── */
#adminDash .greeting-row{display:flex;align-items:flex-start;justify-content:space-between;
  gap:20px;margin-bottom:24px;flex-wrap:wrap;}
#adminDash .greeting{font-size:clamp(1.25rem,4vw,1.6rem);font-family:var(--font-header);font-weight:700;color:var(--text);margin-bottom:3px;}
#adminDash .greeting-sub{font-size:.82rem;color:var(--muted);}

/* ── FILTER BAR ── */
#adminDash .filter-bar{display:flex;align-items:center;gap:8px;flex-wrap:wrap;}
#adminDash .fq-pill{padding:6px 13px;border-radius:20px;font-size:.78rem;font-weight:500;
  cursor:pointer;border:1px solid var(--border);background:var(--card);
  color:var(--muted);transition:all .15s;white-space:nowrap;}
#adminDash .fq-pill:hover{border-color:var(--gold);color:var(--gold);}
#adminDash .fq-pill.active{background:var(--gold);color:#fff;border-color:var(--gold);}
#adminDash .f-sel{height:32px;padding:0 10px;border:1px solid var(--border);border-radius:20px;
  background:var(--card);color:var(--text);font-size:.78rem;cursor:pointer;
  max-width:190px;transition:border-color .15s;}
#adminDash .f-sel:focus{outline:none;border-color:var(--gold);}

/* ── ALERT STRIP ── */
#adminDash .alert-row{display:flex;gap:9px;margin-bottom:20px;flex-wrap:wrap;}
#adminDash .a-chip{display:inline-flex;align-items:center;gap:6px;padding:7px 13px;
  border-radius:9px;font-size:.76rem;font-weight:500;cursor:pointer;border:1px solid;
  background:transparent;transition:opacity .15s;}
#adminDash .a-chip:hover{opacity:.82;}
#adminDash .a-red{background:rgba(220,38,38,.08);border-color:rgba(220,38,38,.16);color:var(--danger);}
#adminDash .a-amb{background:rgba(217,119,6,.08);border-color:rgba(217,119,6,.16);color:var(--warn);}
#adminDash .a-grn{background:rgba(21,128,61,.08);border-color:rgba(21,128,61,.16);color:var(--ok);}
#adminDash .a-blu{background:rgba(37,99,235,.08);border-color:rgba(37,99,235,.16);color:var(--blue);}

/* ── CARDS ── */
#adminDash .card{background:var(--card);border:none;border-radius:16px;box-shadow:var(--shadow);
  overflow:hidden;transition:box-shadow .2s,transform .2s;}
#adminDash .card.clickable{cursor:pointer;}
#adminDash .card.clickable:hover{box-shadow:var(--shadow-hover);transform:translateY(-2px);}
#adminDash .card-pad{padding:20px 22px;}
#adminDash .c-hdr{display:flex;align-items:center;justify-content:space-between;
  gap:8px;margin-bottom:16px;flex-wrap:wrap;}
#adminDash .c-label{font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.09em;
  color:var(--muted);display:flex;align-items:center;gap:6px;}
#adminDash .c-label i{color:var(--gold);font-size:.85rem;}
#adminDash .c-more{font-size:.72rem;color:var(--gold);cursor:pointer;background:none;border:none;
  display:flex;align-items:center;gap:3px;white-space:nowrap;padding:0;}
#adminDash .c-more:hover{opacity:.8;}

/* ── KPI CARDS ── */
#adminDash .kpi-row{display:grid;grid-template-columns:repeat(5,1fr);gap:14px;margin-bottom:20px;}
#adminDash .kpi{background:var(--card);border-radius:16px;padding:20px;text-align:left;
  box-shadow:var(--shadow);cursor:pointer;transition:all .2s;border:none;width:100%;
  display:flex;flex-direction:column;gap:10px;position:relative;overflow:hidden;}
#adminDash .kpi:hover{box-shadow:var(--shadow-hover);transform:translateY(-2px);}
#adminDash .kpi::after{content:'';position:absolute;right:-20px;top:-20px;
  width:80px;height:80px;border-radius:50%;opacity:.06;}
#adminDash .kpi.k1::after{background:#9a8053;}
#adminDash .kpi.k2::after{background:#2563eb;}
#adminDash .kpi.k3::after{background:#15803d;}
#adminDash .kpi.k4::after{background:#d97706;}
#adminDash .kpi.k5::after{background:#7c3aed;}
#adminDash .kpi-row-top{display:flex;align-items:center;justify-content:space-between;}
#adminDash .kpi-ico{width:36px;height:36px;border-radius:10px;display:flex;align-items:center;
  justify-content:center;font-size:.95rem;flex-shrink:0;
  background:var(--gold-bg);color:var(--gold);}
#adminDash .i1{background:rgba(154,128,83,.12);color:var(--gold);}
#adminDash .i2{background:rgba(37,99,235,.10);color:#2563eb;}
#adminDash .i3{background:rgba(21,128,61,.10);color:#15803d;}
#adminDash .i4{background:rgba(217,119,6,.12);color:#d97706;}
#adminDash .i5{background:rgba(124,58,237,.10);color:#7c3aed;}
#adminDash .kpi-delta{font-size:.68rem;padding:2px 7px;border-radius:8px;font-weight:600;
  display:inline-flex;align-items:center;gap:2px;}
#adminDash .du{background:rgba(21,128,61,.1);color:var(--ok);}
#adminDash .dd{background:rgba(220,38,38,.1);color:var(--danger);}
#adminDash .dn{background:rgba(0,0,0,.06);color:var(--muted);}
[data-theme="dark"] #adminDash .dn{background:rgba(255,255,255,.07);}
#adminDash .big-num{font-size:2rem;font-weight:700;color:var(--text);line-height:1;}
#adminDash .kpi-lbl{font-size:.73rem;color:var(--muted);font-weight:500;}
#adminDash .kpi-sub{font-size:.69rem;color:var(--light);}
#adminDash .sp-wrap{height:38px;width:100%;position:relative;margin-top:4px;}
#adminDash .sp-wrap canvas{position:absolute;inset:0;width:100%!important;height:100%!important;}

/* ── MAIN CHART CARD ── */
#adminDash .main-chart-card{background:var(--card);border-radius:16px;
  box-shadow:var(--shadow);padding:24px 26px;}
#adminDash .mc-top{display:flex;align-items:flex-start;justify-content:space-between;
  gap:20px;margin-bottom:20px;flex-wrap:wrap;}
#adminDash .mc-nums{display:flex;gap:28px;flex-wrap:wrap;}
#adminDash .mc-num-label{font-size:.7rem;font-weight:700;text-transform:uppercase;
  letter-spacing:.09em;color:var(--muted);margin-bottom:5px;
  display:flex;align-items:center;gap:6px;}
#adminDash .mc-num-label span{display:inline-block;width:9px;height:9px;border-radius:3px;flex-shrink:0;}
#adminDash .mc-num-big{font-family:var(--font-body);font-size:2rem;
  font-weight:700;line-height:1;}
#adminDash .mc-num-sub{font-size:.7rem;color:var(--muted);margin-top:3px;
  display:flex;align-items:center;gap:4px;}
#adminDash .mc-period-tabs{display:flex;gap:4px;}
#adminDash .mc-tab{padding:5px 12px;border-radius:20px;font-size:.73rem;font-weight:500;
  cursor:pointer;border:1px solid var(--border);background:transparent;
  color:var(--muted);transition:all .15s;}
#adminDash .mc-tab.active{background:var(--gold);color:#fff;border-color:var(--gold);}

/* ── BOUNDED CHART WRAPPERS ── */
#adminDash .ch-220{position:relative;width:100%;height:220px;}
#adminDash .ch-180{position:relative;width:100%;height:180px;}
#adminDash .ch-160{position:relative;width:100%;height:160px;}
#adminDash .ch-140{position:relative;width:100%;height:140px;}
#adminDash .ch-half{position:relative;width:100%;max-width:180px;height:110px;}
#adminDash .ch-d-sm{position:relative;width:110px;height:110px;flex-shrink:0;}
#adminDash .ch-220 canvas,#adminDash .ch-180 canvas,#adminDash .ch-160 canvas,
#adminDash .ch-140 canvas,#adminDash .ch-half canvas,#adminDash .ch-d-sm canvas{
  position:absolute;inset:0;width:100%!important;height:100%!important;}

/* ── GRIDS ── */
#adminDash .g2{display:grid;grid-template-columns:1fr 1fr;gap:16px;}
#adminDash .g3{display:grid;grid-template-columns:repeat(3,1fr);gap:16px;}
#adminDash .g2-3{display:grid;grid-template-columns:2fr 3fr;gap:16px;}
#adminDash .mb-block{margin-bottom:16px;}

/* ── SECTION LABEL ── */
#adminDash .sec-row{display:flex;align-items:center;gap:12px;margin:24px 0 14px;}
#adminDash .sec-line{flex:1;height:1px;background:var(--border);}
#adminDash .sec-ttl{font-size:.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.1em;
  color:var(--light);white-space:nowrap;}

/* ── STATUS DISTRIBUTION ── */
#adminDash .status-dist{display:flex;flex-direction:column;gap:8px;}
#adminDash .sd-row{display:flex;align-items:center;gap:10px;}
#adminDash .sd-label{font-size:.73rem;color:var(--muted);width:110px;flex-shrink:0;
  white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
#adminDash .sd-track{flex:1;height:7px;background:rgba(0,0,0,.06);border-radius:4px;overflow:hidden;}
[data-theme="dark"] #adminDash .sd-track{background:rgba(255,255,255,.07);}
#adminDash .sd-fill{height:100%;width:0;border-radius:4px;transition:width 1s cubic-bezier(.4,0,.2,1);}
#adminDash .sd-n{font-size:.72rem;font-weight:600;color:var(--text);min-width:22px;text-align:right;flex-shrink:0;}

/* ── MINI METRICS ── */
#adminDash .mini-metric{background:var(--card2);border-radius:10px;padding:12px 14px;}
#adminDash .mm-label{font-size:.68rem;color:var(--muted);font-weight:500;margin-bottom:4px;}
#adminDash .mm-val{font-family:var(--font-body);font-size:1.4rem;
  font-weight:700;color:var(--text);line-height:1;}
#adminDash .mm-sub{font-size:.68rem;color:var(--muted);margin-top:3px;}
#adminDash .metric-stack{display:flex;flex-direction:column;gap:8px;flex:1;min-width:130px;}

/* ── FINANCIAL ── */
#adminDash .fin-num-row{display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:14px;}
#adminDash .fin-block{background:var(--card2);border-radius:10px;padding:12px 14px;}
#adminDash .fin-lbl{font-size:.68rem;color:var(--muted);font-weight:500;margin-bottom:4px;}
#adminDash .fin-val{font-family:var(--font-body);font-size:1.35rem;
  font-weight:700;color:var(--text);line-height:1;}
#adminDash .fin-val.g{color:var(--ok);}
#adminDash .fin-val.r{color:var(--danger);}
#adminDash .fin-val.gold{color:var(--gold);}

/* ── TECHNICIAN TABLE ── */
#adminDash .tbl-scroll{overflow-x:auto;-webkit-overflow-scrolling:touch;}
#adminDash .t-tbl{width:100%;min-width:820px;border-collapse:collapse;}
#adminDash .t-tbl thead th{padding:9px 14px;font-size:.67rem;font-weight:700;text-align:left;
  text-transform:uppercase;letter-spacing:.07em;color:var(--muted);
  background:var(--card2);border-bottom:1px solid var(--border);white-space:nowrap;}
#adminDash .t-tbl tbody tr{border-bottom:1px solid var(--border);cursor:pointer;transition:background .1s;}
#adminDash .t-tbl tbody tr:last-child{border-bottom:none;}
#adminDash .t-tbl tbody tr:hover{background:var(--gold-bg);}
#adminDash .t-tbl td{padding:10px 14px;font-size:.79rem;vertical-align:middle;}
#adminDash .t-av{width:28px;height:28px;border-radius:50%;
  background:linear-gradient(135deg,#9a8053,#b8975e);
  display:flex;align-items:center;justify-content:center;color:#fff;
  font-size:.65rem;font-weight:700;flex-shrink:0;}
#adminDash .t-name{font-weight:600;color:var(--text);font-size:.8rem;}
#adminDash .t-domain{font-size:.69rem;color:var(--muted);}
#adminDash .t-bar-wrap{height:6px;background:rgba(0,0,0,.06);border-radius:3px;
  overflow:hidden;min-width:70px;}
[data-theme="dark"] #adminDash .t-bar-wrap{background:rgba(255,255,255,.07);}
#adminDash .t-bar-fill{height:100%;width:0;background:linear-gradient(90deg,#9a8053,#b8975e);
  border-radius:3px;transition:width 1s cubic-bezier(.4,0,.2,1);}
#adminDash .t-stars{color:#f59e0b;font-size:.68rem;display:flex;gap:1px;}
#adminDash .t-pill{font-size:.67rem;font-weight:700;padding:2px 7px;border-radius:8px;
  display:inline-flex;align-items:center;gap:3px;}
#adminDash .p0{background:rgba(21,128,61,.1);color:var(--ok);}
#adminDash .p1{background:rgba(217,119,6,.1);color:var(--warn);}
#adminDash .p2{background:rgba(220,38,38,.1);color:var(--danger);}

/* ── LIST ROWS (clients / front desk / triggers) ── */
#adminDash .lrow{display:flex;align-items:center;justify-content:space-between;gap:8px;
  font-size:.75rem;padding:6px 8px;border-radius:7px;transition:background .12s;
  background:none;border:none;width:100%;text-align:left;color:inherit;}
#adminDash .lrow.clickable{cursor:pointer;}
#adminDash .lrow.clickable:hover{background:var(--gold-bg);}
#adminDash .lrow-rule{border-bottom:1px solid var(--border);border-radius:0;}
#adminDash .lrow-rule:last-child{border-bottom:none;}
#adminDash .rank{width:22px;height:22px;border-radius:6px;background:var(--gold-bg);
  display:flex;align-items:center;justify-content:center;font-size:.6rem;
  font-weight:700;color:var(--gold);flex-shrink:0;}
#adminDash .avatar-sm{width:28px;height:28px;border-radius:50%;background:var(--gold-bg);
  display:flex;align-items:center;justify-content:center;font-size:.65rem;
  font-weight:700;color:var(--gold);flex-shrink:0;}

/* ── RATING HISTOGRAM ── */
#adminDash .h-row{display:flex;align-items:center;gap:8px;margin-bottom:7px;}
#adminDash .h-row:last-child{margin-bottom:0;}
#adminDash .h-lbl{font-size:.7rem;color:var(--muted);width:18px;text-align:right;flex-shrink:0;}
#adminDash .h-track{flex:1;height:8px;background:rgba(0,0,0,.06);border-radius:4px;overflow:hidden;}
[data-theme="dark"] #adminDash .h-track{background:rgba(255,255,255,.07);}
#adminDash .h-fill{height:100%;width:0;background:linear-gradient(90deg,#f59e0b,#fbbf24);
  border-radius:4px;transition:width 1s cubic-bezier(.4,0,.2,1);}
#adminDash .h-n{font-size:.7rem;color:var(--muted);width:22px;flex-shrink:0;}

/* ── LEGEND ── */
#adminDash .leg{display:flex;align-items:center;gap:6px;font-size:.73rem;color:var(--muted);}
#adminDash .leg-dot{width:9px;height:9px;border-radius:3px;flex-shrink:0;}
#adminDash .leg-row{display:flex;flex-wrap:wrap;gap:12px;}

/* ── EMPTY STATE ── */
#adminDash .empty{padding:22px 14px;text-align:center;color:var(--muted);font-size:.78rem;}
#adminDash .empty i{display:block;font-size:1.25rem;color:var(--light);margin-bottom:6px;}

/* ── SLIDE-IN DETAIL PANEL ── */
#adminDash .panel-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.35);
  z-index:1040;backdrop-filter:blur(3px);}
#adminDash .panel-overlay.open{display:block;}
#adminDash .detail-panel{position:fixed;top:0;right:0;width:480px;max-width:100%;
  height:100vh;height:100dvh;background:var(--card);z-index:1050;
  box-shadow:-6px 0 40px rgba(0,0,0,.14);transform:translateX(105%);
  transition:transform .35s cubic-bezier(.4,0,.2,1);
  display:flex;flex-direction:column;overflow:hidden;}
#adminDash .detail-panel.open{transform:translateX(0);}
#adminDash .dp-hdr{display:flex;align-items:center;justify-content:space-between;
  padding:18px 22px;border-bottom:1px solid var(--border);flex-shrink:0;
  background:linear-gradient(135deg,#9a8053,#b8975e);color:#fff;}
#adminDash .dp-hdr-left{display:flex;align-items:center;gap:10px;}
#adminDash .dp-hdr-icon{width:34px;height:34px;border-radius:8px;background:rgba(255,255,255,.2);
  display:flex;align-items:center;justify-content:center;font-size:.9rem;flex-shrink:0;}
#adminDash .panel-heading{font-size:1rem;font-weight:700;margin-bottom:1px;}
#adminDash .dp-hdr-sub{font-size:.72rem;opacity:.85;}
#adminDash .dp-close{background:rgba(255,255,255,.2);border:1px solid rgba(255,255,255,.3);
  color:#fff;width:30px;height:30px;border-radius:7px;display:flex;
  align-items:center;justify-content:center;cursor:pointer;font-size:.85rem;}
#adminDash .dp-close:hover{background:rgba(255,255,255,.3);}
#adminDash .dp-body{flex:1;overflow-y:auto;padding:16px 22px;}
#adminDash .dp-sec{font-size:.67rem;font-weight:700;text-transform:uppercase;letter-spacing:.09em;
  color:var(--light);margin:14px 0 8px;padding-bottom:6px;border-bottom:1px solid var(--border);}
#adminDash .dp-sec:first-child{margin-top:0;}
#adminDash .pr{padding:12px 14px;border-radius:10px;border:1px solid var(--border);
  margin-bottom:9px;transition:all .15s;background:var(--card2);}
#adminDash .pr:last-child{margin-bottom:0;}
#adminDash .pr-top{display:flex;align-items:center;justify-content:space-between;
  margin-bottom:5px;gap:8px;}
#adminDash .pr-id{font-size:.8rem;font-weight:700;color:var(--gold);
  font-family:var(--font-body);}
#adminDash .pr-badge{font-size:.65rem;font-weight:700;padding:2px 7px;border-radius:8px;}
#adminDash .pr-client{font-size:.8rem;font-weight:500;color:var(--text);margin-bottom:2px;}
#adminDash .pr-meta{font-size:.72rem;color:var(--muted);display:flex;align-items:center;gap:10px;flex-wrap:wrap;}

/* ── TOASTS ── */
#adminDash .toast-wrap{position:fixed;bottom:22px;right:22px;z-index:1060;
  display:flex;flex-direction:column;gap:8px;pointer-events:none;max-width:calc(100vw - 44px);}
#adminDash .ti{display:flex;align-items:flex-start;gap:9px;padding:11px 14px;border-radius:10px;
  background:var(--card);border:1px solid var(--border);
  box-shadow:0 4px 20px rgba(0,0,0,.1);min-width:220px;max-width:290px;
  animation:mmToastIn .2s ease;pointer-events:auto;}
@keyframes mmToastIn{from{opacity:0;transform:translateY(8px);}to{opacity:1;transform:none;}}
#adminDash .ti-t{font-size:.79rem;font-weight:600;color:var(--text);margin:0 0 2px;}
#adminDash .ti-b{font-size:.73rem;color:var(--muted);margin:0;}
#adminDash .ok{color:var(--ok);} #adminDash .warn{color:var(--warn);} #adminDash .info{color:var(--gold);}

/* ── RESPONSIVE ── */
@media (max-width:1200px){
  #adminDash .kpi-row{grid-template-columns:repeat(3,1fr);}
  #adminDash .g3{grid-template-columns:1fr 1fr;}
  #adminDash .g2-3{grid-template-columns:1fr;}
}
@media (max-width:900px){
  #adminDash .g3{grid-template-columns:1fr;}
  #adminDash .main-chart-card{padding:20px 18px;}
}
@media (max-width:700px){
  #adminDash .kpi-row{grid-template-columns:1fr 1fr;}
  #adminDash .g2{grid-template-columns:1fr;}
  #adminDash .greeting-row{flex-direction:column;gap:14px;}
  #adminDash .filter-bar{width:100%;overflow-x:auto;flex-wrap:nowrap;padding-bottom:4px;}
  #adminDash .filter-bar > *{flex:0 0 auto;}
}
@media (max-width:575px){
  #adminDash .card-pad{padding:16px;}
  #adminDash .main-chart-card{padding:18px 14px;}
  #adminDash .mc-nums{gap:18px;}
  #adminDash .ch-220{height:190px;}
  #adminDash .fin-num-row{grid-template-columns:1fr 1fr;gap:8px;}
  #adminDash .toast-wrap{left:14px;right:14px;bottom:14px;}
  #adminDash .ti{max-width:100%;}
  #adminDash .dp-body{padding:14px 16px;}
}
@media (max-width:420px){
  #adminDash .kpi-row{grid-template-columns:1fr;}
  #adminDash .sd-label{width:88px;}
}

@media (prefers-reduced-motion:reduce){
  #adminDash *,#adminDash *::before,#adminDash *::after{
    transition-duration:.01ms!important;animation-duration:.01ms!important;}
}

#adminDash{
  --a1:#9a8053;   /* brand gold      */
  --a2:#b8975e;   /* gold, lifted    */
  --a3:#7d6742;   /* gold, deepened  */
  --a4:#c7ab7c;   /* gold, softened  */
  --a5:#6a583a;   /* gold, darkest   */
}
[data-theme="dark"] #adminDash{
  --a1:#b8975e;
  --a2:#c7ab7c;
  --a3:#9a8053;
  --a4:#d6c09a;
  --a5:#8a7049;
}
 
/* ── KPI CORNER WASH ── */
#adminDash .kpi.k1::after{background:var(--a1);}
#adminDash .kpi.k2::after{background:var(--a2);}
#adminDash .kpi.k3::after{background:var(--a3);}
#adminDash .kpi.k4::after{background:var(--a4);}
#adminDash .kpi.k5::after{background:var(--a5);}
 
/* ── KPI ICON TILES ── */
#adminDash .i1{background:var(--gold-bg);color:var(--a1);}
#adminDash .i2{background:var(--gold-bg);color:var(--a2);}
#adminDash .i3{background:var(--gold-bg);color:var(--a3);}
#adminDash .i4{background:var(--gold-bg);color:var(--a4);}
#adminDash .i5{background:var(--gold-bg);color:var(--a5);}
 
/* ── RATING HISTOGRAM + STARS ──
   Were amber (#f59e0b → #fbbf24), the only other warm hue on the page. */
#adminDash .h-fill{background:linear-gradient(90deg,var(--a1),var(--a2));}
#adminDash .t-stars{color:var(--a2);}
 
/* ── SECTION HEADINGS + CARD LABEL ICONS ──
   Already gold; restated so the whole theme lives in one block. */
#adminDash .c-label i{color:var(--a1);}
#adminDash .rank,
#adminDash .avatar-sm{background:var(--gold-bg);color:var(--a1);}
</style>
@endpush

@section('content')
@php
    // ---- normalise inputs so the view never breaks while you wire up the DB ----
    $kpis            = $kpis            ?? [];
    $alertCounts     = $alertCounts     ?? [];
    $statusBreakdown = $statusBreakdown ?? [];
    $technicians     = $technicians     ?? [];
    $clients         = $clients         ?? [];
    $frontDesk       = $frontDesk       ?? [];
    $waTriggers      = $waTriggers      ?? [];
    $ratingBuckets   = $ratingBuckets   ?? [];
    $filters         = $filters         ?? [];

    $maxStatus  = collect($statusBreakdown)->max(fn ($r) => (int) data_get($r, 'count'))  ?: 1;
    $maxJobs    = collect($technicians)->max(fn ($r) => (int) data_get($r, 'jobs'))       ?: 1;
    $maxRating  = collect($ratingBuckets)->max(fn ($r) => (int) data_get($r, 'count'))    ?: 1;
@endphp

<div id="adminDash">

    {{-- ── GREETING + FILTERS ─────────────────────────────────────────── --}}
    <div class="greeting-row">
        <div>
            <div class="greeting">{{ $greeting ?? 'Admin Dashboard' }}</div>
            <div class="greeting-sub">
                <i class="bi bi-calendar3" style="color:var(--gold);margin-right:4px;"></i>
                {{ $today ?? now()->format('l, d F Y') }}
                @isset($greetingSub)
                    &nbsp;·&nbsp; {{ $greetingSub }}
                @endisset
            </div>
        </div>

        {{-- A GET form so the pills and selects actually filter the queries. --}}
        <form method="GET" action="{{ url()->current() }}" class="filter-bar" id="dashFilters">
            <input type="hidden" name="period" value="{{ $filters['period'] ?? '6M' }}">

            <button type="submit" name="range" value="today"
                    class="fq-pill {{ ($filters['range'] ?? null) === 'today' ? 'active' : '' }}">Today</button>
            <button type="submit" name="range" value="month"
                    class="fq-pill {{ ($filters['range'] ?? null) === 'month' ? 'active' : '' }}">This Month</button>
            <button type="submit" name="range" value="quarter"
                    class="fq-pill {{ ($filters['range'] ?? null) === 'quarter' ? 'active' : '' }}">This Quarter</button>

            <select class="f-sel" name="status" onchange="this.form.submit()" aria-label="Filter by status">
                <option value="">All Statuses</option>
                @foreach (($statusOptions ?? []) as $value => $label)
                    <option value="{{ $value }}" @selected(($filters['status'] ?? null) == $value)>{{ $label }}</option>
                @endforeach
            </select>

            <select class="f-sel" name="client" onchange="this.form.submit()" aria-label="Filter by client">
                <option value="">All Clients</option>
                @foreach (($clientOptions ?? []) as $value => $label)
                    <option value="{{ $value }}" @selected(($filters['client'] ?? null) == $value)>{{ $label }}</option>
                @endforeach
            </select>

            <select class="f-sel" name="service" onchange="this.form.submit()" aria-label="Filter by service">
                <option value="">All Services</option>
                @foreach (($serviceOptions ?? []) as $value => $label)
                    <option value="{{ $value }}" @selected(($filters['service'] ?? null) == $value)>{{ $label }}</option>
                @endforeach
            </select>
        </form>
    </div>

    {{-- ── ALERT STRIP · 5 chips ──────────────────────────────────────── --}}
    <div class="alert-row">
        <button type="button" class="a-chip a-red" data-panel="sla-breach">
            <i class="bi bi-exclamation-triangle-fill"></i>
            {{ $alertCounts['breaches'] ?? 0 }} SLA {{ \Illuminate\Support\Str::plural('breach', $alertCounts['breaches'] ?? 0) }} this period
        </button>

        <button type="button" class="a-chip a-amb" data-panel="pending-actions">
            <i class="bi bi-hourglass-split"></i>
            {{ $alertCounts['pending'] ?? 0 }} {{ \Illuminate\Support\Str::plural('action', $alertCounts['pending'] ?? 0) }} pending across roles
        </button>

        <button type="button" class="a-chip a-amb" data-panel="slow-srs">
            <i class="bi bi-clock-history"></i>
            {{ $alertCounts['stalled'] ?? 0 }} SRs stalled 24h+
        </button>

        <button type="button" class="a-chip a-grn" data-panel="wa-failures">
            <i class="bi bi-whatsapp"></i>
            {{ $alertCounts['wa_failures'] ?? 0 }} WA delivery failures this period
        </button>

        <span class="a-chip a-blu">
            <i class="bi bi-people-fill"></i>
            {{ $alertCounts['on_site'] ?? 0 }} technicians currently on-site
        </span>
    </div>

    {{-- ── KPI ROW · 5 cards ──────────────────────────────────────────── --}}
    <div class="kpi-row">

        {{-- 1 · Total SRs --}}
        <button type="button" class="kpi k1" data-panel="month-srs">
            <div class="kpi-row-top">
                <span class="kpi-ico i1"><i class="bi bi-ticket-detailed"></i></span>
                @if (data_get($kpis, 'total.delta'))
                    <span class="kpi-delta {{ data_get($kpis, 'total.delta_tone', 'dn') }}">{{ data_get($kpis, 'total.delta') }}</span>
                @endif
            </div>
            <span class="big-num">{{ data_get($kpis, 'total.value', 0) }}</span>
            <span class="kpi-lbl">Total SRs This Period</span>
            <span class="kpi-sub">{{ data_get($kpis, 'total.sub') }}</span>
            <span class="sp-wrap"><canvas id="mmSpark0"></canvas></span>
        </button>

        {{-- 2 · Active open SRs --}}
        <button type="button" class="kpi k2" data-panel="active-srs">
            <div class="kpi-row-top">
                <span class="kpi-ico i2"><i class="bi bi-activity"></i></span>
                <span class="kpi-delta dn">Live</span>
            </div>
            <span class="big-num">{{ data_get($kpis, 'open.value', 0) }}</span>
            <span class="kpi-lbl">Active Open SRs</span>
            <span class="kpi-sub">{{ data_get($kpis, 'open.sub') }}</span>
            <span class="sp-wrap"><canvas id="mmSpark1"></canvas></span>
        </button>

        {{-- 3 · SLA compliance --}}
        <button type="button" class="kpi k3" data-panel="sla-breach">
            <div class="kpi-row-top">
                <span class="kpi-ico i3"><i class="bi bi-speedometer2"></i></span>
                @if (data_get($kpis, 'sla.delta'))
                    <span class="kpi-delta {{ data_get($kpis, 'sla.delta_tone', 'dn') }}">{{ data_get($kpis, 'sla.delta') }}</span>
                @endif
            </div>
            <span class="big-num">{{ data_get($kpis, 'sla.value', '—') }}</span>
            <span class="kpi-lbl">SLA Compliance</span>
            <span class="kpi-sub">{{ data_get($kpis, 'sla.sub') }}</span>
            <span class="sp-wrap"><canvas id="mmSpark2"></canvas></span>
        </button>

        {{-- 4 · Invoiced --}}
        <button type="button" class="kpi k4" data-panel="invoices">
            <div class="kpi-row-top">
                <span class="kpi-ico i4"><i class="bi bi-cash-coin"></i></span>
                @if (data_get($kpis, 'invoiced.delta'))
                    <span class="kpi-delta {{ data_get($kpis, 'invoiced.delta_tone', 'dn') }}">{{ data_get($kpis, 'invoiced.delta') }}</span>
                @endif
            </div>
            <span class="big-num">{{ data_get($kpis, 'invoiced.value', '—') }}</span>
            <span class="kpi-lbl">Invoiced This Period</span>
            <span class="kpi-sub">{{ data_get($kpis, 'invoiced.sub') }}</span>
            @if (data_get($kpis, 'invoiced.spark'))
                <span class="sp-wrap"><canvas id="mmSpark3"></canvas></span>
            @endif
        </button>

        {{-- 5 · Avg completion time --}}
        <button type="button" class="kpi k5" data-panel="slow-srs">
            <div class="kpi-row-top">
                <span class="kpi-ico i5"><i class="bi bi-clock-history"></i></span>
                @if (data_get($kpis, 'turnaround.delta'))
                    <span class="kpi-delta {{ data_get($kpis, 'turnaround.delta_tone', 'dn') }}">{{ data_get($kpis, 'turnaround.delta') }}</span>
                @endif
            </div>
            <span class="big-num">{{ data_get($kpis, 'turnaround.value', '—') }}</span>
            <span class="kpi-lbl">Avg Completion Time</span>
            <span class="kpi-sub">{{ data_get($kpis, 'turnaround.sub') }}</span>
            <span class="sp-wrap"><canvas id="mmSpark4"></canvas></span>
        </button>
    </div>

    {{-- ── STATUS DISTRIBUTION + SR TREND ─────────────────────────────── --}}
    <div class="g2-3 mb-block">

        <div class="card card-pad">
            <div class="c-hdr">
                <div class="c-label"><i class="bi bi-bar-chart-steps"></i>SR status distribution</div>
                <button type="button" class="c-more" data-panel="active-srs">View all <i class="bi bi-arrow-right"></i></button>
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

            <div class="leg-row" style="padding-top:12px;border-top:1px solid var(--border);">
                <span class="leg"><span class="leg-dot" style="background:#9a8053;"></span>Operations</span>
                <span class="leg"><span class="leg-dot" style="background:#2563eb;"></span>Intake</span>
                <span class="leg"><span class="leg-dot" style="background:#15803d;"></span>Completed</span>
                <span class="leg"><span class="leg-dot" style="background:#dc2626;"></span>Issues</span>
            </div>
        </div>

        <div class="main-chart-card">
            <div class="mc-top">
                <div class="mc-nums">
                    <div>
                        <div class="mc-num-label"><span style="background:#9a8053;"></span>In-warranty SRs</div>
                        <div class="mc-num-big" style="color:#9a8053;">{{ data_get($srTrend ?? [], 'in_warranty_total', 0) }}</div>
                        @if (! is_null(data_get($srTrend ?? [], 'in_warranty_change')))
                            @php $iwChange = (float) data_get($srTrend, 'in_warranty_change'); @endphp
                            <div class="mc-num-sub">
                                <i class="bi bi-arrow-{{ $iwChange >= 0 ? 'up' : 'down' }}"
                                   style="color:{{ $iwChange >= 0 ? 'var(--ok)' : 'var(--danger)' }};"></i>
                                <span style="color:{{ $iwChange >= 0 ? 'var(--ok)' : 'var(--danger)' }};">
                                    {{ $iwChange >= 0 ? '+' : '' }}{{ $iwChange }}%
                                </span>&nbsp;vs last period
                            </div>
                        @endif
                    </div>
                    <div>
                        <div class="mc-num-label"><span style="background:#393837;"></span>Out-of-warranty SRs</div>
                        <div class="mc-num-big" style="color:#393837;">{{ data_get($srTrend ?? [], 'out_warranty_total', 0) }}</div>
                        @if (! is_null(data_get($srTrend ?? [], 'out_warranty_change')))
                            @php $owChange = (float) data_get($srTrend, 'out_warranty_change'); @endphp
                            <div class="mc-num-sub">
                                <i class="bi bi-arrow-{{ $owChange >= 0 ? 'up' : 'down' }}"
                                   style="color:{{ $owChange >= 0 ? 'var(--ok)' : 'var(--danger)' }};"></i>
                                <span style="color:{{ $owChange >= 0 ? 'var(--ok)' : 'var(--danger)' }};">
                                    {{ $owChange >= 0 ? '+' : '' }}{{ $owChange }}%
                                </span>&nbsp;vs last period
                            </div>
                        @endif
                    </div>
                </div>

                <div class="mc-period-tabs">
                    @foreach (($trendPeriods ?? []) as $value => $label)
                        <button type="button" class="mc-tab {{ ($filters['period'] ?? null) === $value ? 'active' : '' }}"
                                data-period="{{ $value }}">{{ $label }}</button>
                    @endforeach
                </div>
            </div>

            <div class="leg-row" style="margin-bottom:8px;">
                <span class="leg"><span class="leg-dot" style="background:var(--warn);"></span>Pending</span>
                <span class="leg"><span class="leg-dot" style="background:var(--danger);"></span>Rejected</span>
                <span class="leg"><span class="leg-dot" style="background:var(--ok);"></span>Completed</span>
            </div>

            <div class="ch-220"><canvas id="mmSrTrend"></canvas></div>
        </div>
    </div>

    {{-- ── FINANCE + QC + WHATSAPP ────────────────────────────────────── --}}
    <div class="g3 mb-block">

        {{-- Finance --}}
        <div class="card card-pad">
            <div class="c-hdr">
                <div class="c-label"><i class="bi bi-cash-coin"></i>Financial overview</div>
                <button type="button" class="c-more" data-panel="invoices">Details <i class="bi bi-arrow-right"></i></button>
            </div>

            <div class="fin-num-row">
                <div class="fin-block">
                    <div class="fin-lbl">Invoiced</div>
                    <div class="fin-val g">{{ data_get($finance ?? [], 'invoiced_formatted', '—') }}</div>
                </div>
                <div class="fin-block">
                    <div class="fin-lbl">Field expense</div>
                    <div class="fin-val r">{{ data_get($finance ?? [], 'expense_formatted', '—') }}</div>
                </div>
                
            </div>

            <div class="ch-140"><canvas id="mmFinChart"></canvas></div>
            <div class="leg-row" style="margin-top:9px;">
                <span class="leg"><span class="leg-dot" style="background:#9a8053;"></span>Invoiced</span>
                <span class="leg"><span class="leg-dot" style="background:#39383788;"></span>Expenses</span>
            </div>
        </div>

        {{-- QC --}}
        <div class="card card-pad">
            <div class="c-hdr">
                <div class="c-label"><i class="bi bi-patch-check"></i>QC &amp; quality</div>
            </div>

            <div style="display:flex;align-items:center;gap:16px;flex-wrap:wrap;">
            <div>
                <div class="ch-half"><canvas id="mmSlaGauge"></canvas></div>
                <div style="text-align:center;margin-top:-14px;">
                    <div style="font-family:var(--font-body);font-size:1.6rem;font-weight:700;line-height:1;">
                        {{ ! is_null(data_get($qc ?? [], 'qc_rate')) ? data_get($qc, 'qc_rate') . '%' : '—' }}
                    </div>
                    <div style="font-size:.7rem;color:var(--muted);">
                        QC reviewed · {{ data_get($qc ?? [], 'qc_reviewed', 0) }} of {{ data_get($qc ?? [], 'qc_reached', 0) }}
                    </div>
                </div>
            </div>

            <div class="metric-stack">
                <div class="mini-metric" data-panel="pending-qc" style="cursor:pointer;">
                    <div class="mm-label">Pending QC</div>
                    <div class="mm-val" style="color:var(--warn);">{{ data_get($qc ?? [], 'pending_qc', 0) }}</div>
                    <div class="mm-sub">{{ data_get($qc ?? [], 'pending_qc_sub') }}</div>
                </div>
                <div class="mini-metric">
                    <div class="mm-label">Rework this period</div>
                    <div class="mm-val" style="color:var(--danger);">{{ data_get($qc ?? [], 'rework_count', 0) }}</div>
                    <div class="mm-sub">{{ data_get($qc ?? [], 'rework_sub') }}</div>
                </div>
                <div class="mini-metric" data-panel="sla-breach" style="cursor:pointer;"
                    title="{{ collect(data_get($qc ?? [], 'stage_breaches', []))->map(fn ($s) => $s['label'].': '.$s['count'].' over '.$s['target'].'h')->implode("\n") }}">
                    <div class="mm-label">SLA breaches</div>
                    <div class="mm-val" style="color:var(--danger);">{{ data_get($qc ?? [], 'sla_breaches', 0) }}</div>
                    <div class="mm-sub">{{ data_get($qc ?? [], 'sla_breach_sub') }}</div>
                </div>
            </div>
        </div>
        </div>

        {{-- WhatsApp --}}
        <div class="card card-pad">
            <div class="c-hdr">
                <div class="c-label"><i class="bi bi-whatsapp"></i>WhatsApp engine</div>
                <button type="button" class="c-more" data-panel="wa-failures">Failures <i class="bi bi-arrow-right"></i></button>
            </div>

            <div style="display:flex;align-items:center;gap:14px;margin-bottom:14px;flex-wrap:wrap;">
                <div class="ch-d-sm"><canvas id="mmWaDonut"></canvas></div>
                <div>
                    <div style="font-family:var(--font-body);font-size:1.6rem;font-weight:700;color:var(--gold);line-height:1;">
                        {{ ! is_null(data_get($whatsapp ?? [], 'delivery_rate')) ? data_get($whatsapp, 'delivery_rate') . '%' : '—' }}
                    </div>
                    <div style="font-size:.72rem;color:var(--muted);margin-bottom:8px;">Delivery rate</div>
                    <div class="leg" style="margin-bottom:4px;">
                        <span class="leg-dot" style="background:#9a8053;"></span>Delivered: {{ data_get($whatsapp ?? [], 'delivered', 0) }}
                    </div>
                    <div class="leg">
                        <span class="leg-dot" style="background:rgba(220,38,38,.5);"></span>Failed: {{ data_get($whatsapp ?? [], 'failed', 0) }}
                    </div>
                </div>
            </div>

            <div class="sec-ttl" style="margin-bottom:7px;">By trigger</div>
            @forelse ($waTriggers as $trigger)
                <div class="lrow lrow-rule">
                    <span style="color:var(--muted);">{{ data_get($trigger, 'label') }}</span>
                    <span style="display:flex;align-items:center;gap:8px;">
                        <strong style="color:var(--text);">{{ data_get($trigger, 'sent', 0) }}</strong>
                        @if ((int) data_get($trigger, 'failed') > 0)
                            <span style="color:var(--danger);font-size:.67rem;">{{ data_get($trigger, 'failed') }} failed</span>
                        @else
                            <span style="color:var(--ok);font-size:.67rem;" aria-label="All delivered">&check;</span>
                        @endif
                    </span>
                </div>
            @empty
                <p class="empty"><i class="bi bi-chat-dots"></i>No messages sent in this period.</p>
            @endforelse
        </div>
    </div>

    {{-- ── TECHNICIAN SCORECARD ───────────────────────────────────────── --}}
    <div class="sec-row">
        <span class="sec-line"></span>
        <span class="sec-ttl"><i class="bi bi-people" style="color:var(--gold);margin-right:4px;"></i>Technician scorecard</span>
        <span class="sec-line"></span>
    </div>

    <div class="card mb-block">
        <div class="c-hdr" style="padding:14px 18px;margin-bottom:0;">
            <div class="c-label"><i class="bi bi-trophy"></i>Performance ranked</div>
            <div class="leg-row">
                <span class="leg"><span class="leg-dot" style="background:#9a8053;"></span>Jobs</span>
                <span class="leg"><span class="leg-dot" style="background:#15803d;"></span>No rework</span>
                <span class="leg"><span class="leg-dot" style="background:#d97706;"></span>1 rework</span>
                <span class="leg"><span class="leg-dot" style="background:#dc2626;"></span>2+ rework</span>
            </div>
        </div>

        <div class="tbl-scroll">
            <table class="t-tbl">
                <thead>
                    <tr>
                        <th style="padding-left:18px;">#</th>
                        <th>Technician</th>
                        <th>Jobs completed</th>
                        <th>Field hours</th>
                        <th>Client rating</th>
                        <th>Rework</th>
                        <th>Pending</th>
                        <th>SLA breaches</th>
                        <th>Field expenses</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($technicians as $tech)
                        @php
                            $jobs      = (int) data_get($tech, 'jobs');
                            $rework    = (int) data_get($tech, 'rework');
                            $rating    = (float) data_get($tech, 'rating');
                            $reworkCls = $rework === 0 ? 'p0' : ($rework <= 1 ? 'p1' : 'p2');
                        @endphp
                        <tr data-panel="technician" data-panel-id="{{ data_get($tech, 'id') }}">
                            <td style="color:var(--light);font-size:.78rem;padding-left:18px;">{{ $loop->iteration }}</td>
                            <td>
                                <div style="display:flex;align-items:center;gap:9px;">
                                    <span class="t-av">{{ data_get($tech, 'initials') }}</span>
                                    <span>
                                        <span class="t-name">{{ data_get($tech, 'name') }}</span>
                                        <span class="t-domain" style="display:block;">{{ data_get($tech, 'department') }}</span>
                                    </span>
                                </div>
                            </td>
                            <td>
                                <span class="t-bar-wrap" style="display:block;">
                                    <span class="t-bar-fill" style="display:block;" data-width="{{ round($jobs / $maxJobs * 100) }}%"></span>
                                </span>
                                <span style="font-size:.69rem;color:var(--muted);margin-top:2px;display:block;">{{ $jobs }} jobs</span>
                            </td>
                            <td style="font-weight:600;">{{ data_get($tech, 'hours') }}h</td>
                            <td>
                                <span class="t-stars">
                                    @for ($star = 1; $star <= 5; $star++)
                                        <i class="bi bi-star{{ $star <= round($rating) ? '-fill' : '' }}"></i>
                                    @endfor
                                </span>
                                <span style="font-size:.69rem;color:var(--muted);">{{ number_format($rating, 1) }}</span>
                            </td>
                            <td><span class="t-pill {{ $reworkCls }}"><i class="bi bi-arrow-counterclockwise"></i>{{ $rework }}</span></td>
                            <td title="{{ implode("\n", data_get($tech, 'pending_items', [])) ?: 'Nothing outstanding' }}"
                                style="font-size:.8rem;font-weight:600;cursor:help;
                                    color:{{ (int) data_get($tech, 'pending') === 0 ? 'var(--ok)' : 'var(--warn)' }};">
                                {{ data_get($tech, 'pending', 0) }}
                                @if ((int) data_get($tech, 'pending') > 0)
                                    <i class="bi bi-info-circle" style="font-size:.7rem;opacity:.6;"></i>
                                @endif
                            </td>

                            <td title="{{ implode("\n", data_get($tech, 'breach_items', [])) ?: 'No breaches' }}">
                                <span class="t-pill {{ data_get($tech, 'critical') ? 'p2' : ((int) data_get($tech, 'sla_breaches') > 0 ? 'p1' : 'p0') }}"
                                    style="cursor:help;">
                                    @if (data_get($tech, 'critical'))
                                        <i class="bi bi-exclamation-triangle-fill"></i>
                                    @endif
                                    {{ data_get($tech, 'sla_breaches', 0) }}
                                </span>
                            </td>
                            <td style="font-size:.78rem;color:var(--muted);">{{ data_get($tech, 'expenses_formatted') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8"><p class="empty"><i class="bi bi-person-badge"></i>No technician activity in this period.</p></td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ── CLIENTS / FRONT DESK / SATISFACTION ────────────────────────── --}}
    <div class="sec-row">
        <span class="sec-line"></span>
        <span class="sec-ttl"><i class="bi bi-buildings" style="color:var(--gold);margin-right:4px;"></i>Clients, front desk &amp; satisfaction</span>
        <span class="sec-line"></span>
    </div>

    <div class="g3 mb-block">

        {{-- Clients --}}
        <div class="card card-pad">
            <div class="c-hdr">
                <div class="c-label"><i class="bi bi-buildings"></i>Top clients by SR volume</div>
                @isset($clientsIndexUrl)
                    <a href="{{ $clientsIndexUrl }}" class="c-more">All clients <i class="bi bi-arrow-right"></i></a>
                @endisset
            </div>

            <div class="ch-180"><canvas id="mmClientChart"></canvas></div>
            <div class="leg-row" style="margin-top:9px;">
                <span class="leg"><span class="leg-dot" style="background:#9a8053;"></span>In-warranty</span>
                <span class="leg"><span class="leg-dot" style="background:#39383788;"></span>Out-of-warranty</span>
            </div>

            <div style="margin-top:12px;display:flex;flex-direction:column;gap:2px;">
                @forelse ($clients as $client)
                    <button type="button" class="lrow clickable"
                            data-panel="client" data-panel-id="{{ data_get($client, 'id') }}">
                        <span style="display:flex;align-items:center;gap:7px;">
                            <span class="rank">{{ $loop->iteration }}</span>
                            <span style="font-weight:500;color:var(--text);">{{ data_get($client, 'name') }}</span>
                        </span>
                        <span style="display:flex;align-items:center;gap:6px;">
                            <strong style="color:var(--text);">{{ data_get($client, 'srs') }}</strong>
                            <span style="color:var(--muted);">SRs</span>
                        </span>
                    </button>
                @empty
                    <p class="empty"><i class="bi bi-building"></i>No client activity in this period.</p>
                @endforelse
            </div>
        </div>

        {{-- Front desk --}}
        <div class="card card-pad">
            <div class="c-hdr">
                <div class="c-label"><i class="bi bi-headset"></i>Front desk performance</div>
            </div>

            <div class="ch-140"><canvas id="mmFrontDeskChart"></canvas></div>

            <div style="margin-top:12px;">
                @forelse ($frontDesk as $exec)
                    <div class="lrow lrow-rule">
                        <span class="avatar-sm">{{ data_get($exec, 'initials') }}</span>
                        <span style="flex:1;">
                            <span style="font-size:.78rem;font-weight:500;color:var(--text);display:block;">{{ data_get($exec, 'name') }}</span>
                            <span style="font-size:.68rem;color:var(--muted);">{{ data_get($exec, 'srs') }} SRs logged</span>
                        </span>
                        <span style="display:flex;align-items:center;gap:10px;font-size:.7rem;font-weight:700;">
                            <span style="color:var(--warn);"  title="Pending">{{ data_get($exec, 'pending', 0) }}</span>
                            <span style="color:var(--danger);" title="Rejected">{{ data_get($exec, 'rejected', 0) }}</span>
                            <span style="color:var(--ok);"    title="Completed">{{ data_get($exec, 'completed', 0) }}</span>
                        </span>
                    </div>
                @empty
                    <p class="empty"><i class="bi bi-headset"></i>No front desk activity in this period.</p>
                @endforelse
            </div>
        </div>

        {{-- Satisfaction --}}
        <div class="card card-pad">
            <div class="c-hdr">
                <div class="c-label"><i class="bi bi-star-half"></i>Client satisfaction</div>
            </div>

            <div style="display:flex;align-items:center;gap:14px;margin-bottom:16px;
                        padding-bottom:14px;border-bottom:1px solid var(--border);flex-wrap:wrap;">
                <div>
                    <div style="font-family:var(--font-body);font-size:3rem;
                                font-weight:700;color:var(--gold);line-height:1;">
                        {{ ! is_null(data_get($satisfaction ?? [], 'average')) ? number_format((float) data_get($satisfaction, 'average'), 1) : '—' }}
                    </div>
                    <div style="display:flex;gap:2px;color:#f59e0b;font-size:.85rem;margin-bottom:3px;">
                        @for ($star = 1; $star <= 5; $star++)
                            <i class="bi bi-star{{ $star <= round((float) data_get($satisfaction ?? [], 'average')) ? '-fill' : '' }}"></i>
                        @endfor
                    </div>
                    <div style="font-size:.7rem;color:var(--muted);">
                        {{ data_get($satisfaction ?? [], 'responses', 0) }} responses
                        @if (! is_null(data_get($satisfaction ?? [], 'response_rate')))
                            · {{ data_get($satisfaction, 'response_rate') }}% rate
                        @endif
                    </div>
                </div>

                <div class="metric-stack">
                    <div class="mini-metric">
                        <div class="mm-label">Completed SRs</div>
                        <div class="mm-val">{{ data_get($satisfaction ?? [], 'completed_srs', 0) }}</div>
                    </div>
                    <div class="mini-metric">
                        <div class="mm-label">Feedback submitted</div>
                        <div class="mm-val" style="color:var(--gold);">{{ data_get($satisfaction ?? [], 'responses', 0) }}</div>
                    </div>
                </div>
            </div>
                @if (! empty($clientReviews))
    <div class="sec-ttl" style="margin:4px 0 8px;">Latest reviews</div>

    @foreach ($clientReviews as $review)
        <div class="lrow lrow-rule" style="align-items:flex-start;padding:8px;">
            <span class="avatar-sm">{{ data_get($review, 'initials') }}</span>
            <span style="flex:1;min-width:0;">
                <span style="display:flex;align-items:center;gap:6px;flex-wrap:wrap;">
                    <strong style="font-size:.75rem;color:var(--text);">{{ data_get($review, 'client') }}</strong>
                    <span style="font-size:.68rem;color:var(--gold);">{{ data_get($review, 'code') }}</span>
                </span>
                <span class="t-stars" style="margin:2px 0;">
                    @for ($star = 1; $star <= 5; $star++)
                        <i class="bi bi-star{{ $star <= data_get($review, 'stars') ? '-fill' : '' }}"></i>
                    @endfor
                </span>
                @if (data_get($review, 'comment'))
                    <span style="font-size:.7rem;color:var(--muted);display:block;">{{ data_get($review, 'comment') }}</span>
                @endif
                <span style="font-size:.66rem;color:var(--light);display:block;">
                    ML: {{ data_get($review, 'ml') }} · {{ data_get($review, 'when') }}
                </span>
            </span>
        </div>
    @endforeach

    <div class="sec-ttl" style="margin:12px 0 8px;">Rating distribution</div>
@endif
            @forelse ($ratingBuckets as $bucket)
                <div class="h-row">
                    <span class="h-lbl">{{ data_get($bucket, 'stars') }}</span>
                    <span class="h-track">
                        <span class="h-fill" data-width="{{ round((int) data_get($bucket, 'count') / $maxRating * 100) }}%"></span>
                    </span>
                    <span class="h-n">{{ data_get($bucket, 'count') }}</span>
                </div>
            @empty
                <p class="empty"><i class="bi bi-star"></i>No ratings submitted yet.</p>
            @endforelse

            @if (data_get($satisfaction ?? [], 'flagged'))
                <div style="margin-top:12px;padding:9px 12px;background:rgba(217,119,6,.07);
                            border:1px solid rgba(217,119,6,.15);border-radius:8px;
                            font-size:.74rem;color:var(--warn);display:flex;align-items:center;gap:6px;">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    {{ data_get($satisfaction, 'flagged') }}
                </div>
            @endif
        </div>
    </div>

    {{-- ── SLIDE-IN DETAIL PANEL ──────────────────────────────────────── --}}
    <div class="panel-overlay" id="mmPanelOverlay"></div>
    <aside class="detail-panel" id="mmDetailPanel" role="dialog" aria-modal="true" aria-labelledby="mmPanelTitle">
        <div class="dp-hdr">
            <div class="dp-hdr-left">
                <span class="dp-hdr-icon" id="mmPanelIcon"><i class="bi bi-list"></i></span>
                <span>
                    <span class="panel-heading" id="mmPanelTitle">Details</span>
                    <span class="dp-hdr-sub" id="mmPanelSub"></span>
                </span>
            </div>
            <button type="button" class="dp-close" id="mmPanelClose" aria-label="Close panel"><i class="bi bi-x-lg"></i></button>
        </div>
        <div class="dp-body" id="mmPanelBody"></div>
    </aside>

    <div class="toast-wrap" id="mmToasts" aria-live="polite"></div>
</div>
@endsection

@push('scripts')
@php
    // Built here rather than inline: the json directive splits its argument on
    // commas, so any expression containing a comma must be a variable first.
    $mmChartData = [
        'panelUrl'  => $panelUrl ?? null,

        'sparks'    => collect($kpis ?? [])
            ->map(fn ($k) => data_get($k, 'spark', []))
            ->values()
            ->all(),

        'srTrend'   => $srTrend  ?? null,
        'finance'   => $finance  ?? null,
        'whatsapp'  => $whatsapp ?? null,
        'qc'        => $qc       ?? null,

        'clients'   => collect($clients ?? [])
            ->map(fn ($c) => [
                'name' => data_get($c, 'short_name') ?? data_get($c, 'name'),
                'iw'   => (int) data_get($c, 'in_warranty'),
                'oow'  => (int) data_get($c, 'out_warranty'),
            ])
            ->values()
            ->all(),

            'frontDesk' => collect($frontDesk ?? [])
            ->map(fn ($e) => [
                'initials'  => data_get($e, 'initials'),
                'pending'   => (int) data_get($e, 'pending'),
                'rejected'  => (int) data_get($e, 'rejected'),
                'completed' => (int) data_get($e, 'completed'),
            ])
            ->values()
            ->all(),
    ];
@endphp
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
window.MM_DASH = @json($mmChartData);
</script>
<script>
(function () {
    'use strict';

    const root   = document.getElementById('adminDash');
    if (!root) return;

    const DATA   = window.MM_DASH || {};
    const GOLD   = '#9a8053';
    const INK    = '#393837';
    const charts = {};

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
            const canvas = document.getElementById('mmSpark' + i);
            if (!canvas || !series || !series.length) return;
            const colors = [GOLD, '#2563eb', '#15803d', '#d97706', '#7c3aed'];
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

        /* SR volume trend */
        const trendEl = document.getElementById('mmSrTrend');
        if (trendEl && DATA.srTrend && DATA.srTrend.labels) {
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
                    labels: DATA.srTrend.labels,
                    datasets: [
                        {
                            label: 'In-warranty', data: DATA.srTrend.in_warranty || [],
                            borderColor: GOLD, borderWidth: 2.5, backgroundColor: gIw,
                            fill: true, tension: .4, pointRadius: 4,
                            pointBackgroundColor: GOLD, pointHoverRadius: 6,
                        },
                        {
                            label: 'Out-of-warranty', data: DATA.srTrend.out_warranty || [],
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

        /* Invoiced vs expense */
        const finEl = document.getElementById('mmFinChart');
        if (finEl && DATA.finance && DATA.finance.labels) {
            charts.finance = new Chart(finEl, {
                type: 'bar',
                data: {
                    labels: DATA.finance.labels,
                    datasets: [
                        { label: 'Invoiced', data: DATA.finance.invoiced_series || [], backgroundColor: 'rgba(154,128,83,.8)', borderRadius: 5, borderSkipped: false },
                        { label: 'Expense',  data: DATA.finance.expense_series  || [], backgroundColor: 'rgba(57,56,55,.4)',  borderRadius: 5, borderSkipped: false },
                    ],
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: Object.assign({}, tooltipStyle, {
                            callbacks: {
                                label: (c) => ' ' + (DATA.finance.currency || '') + ' ' + c.parsed.y.toLocaleString(),
                            },
                        }),
                    },
                    scales: {
                        x: { grid: { display: false }, ticks: { color: C.muted, font: { size: 10 } } },
                        y: {
                            grid: { color: C.grid },
                            ticks: { color: C.muted, font: { size: 10 }, callback: (v) => v >= 1000 ? (v / 1000) + 'k' : v },
                        },
                    },
                },
            });
        }

        /* WhatsApp donut */
        const waEl = document.getElementById('mmWaDonut');
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

        /* SLA gauge */
        const slaEl = document.getElementById('mmSlaGauge');
        if (slaEl && DATA.qc && DATA.qc.qc_rate !== null && DATA.qc.qc_rate !== undefined) {
            const met = Number(DATA.qc.qc_rate);
            charts.sla = new Chart(slaEl, {
                type: 'doughnut',
                data: {
                    labels: ['Met', 'Breached'],
                    datasets: [{
                        data: [met, Math.max(0, 100 - met)],
                        backgroundColor: [GOLD, 'rgba(220,38,38,.2)'],
                        borderColor: [GOLD, 'rgba(220,38,38,.3)'],
                        borderWidth: 2, hoverOffset: 0,
                    }],
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    cutout: '78%', rotation: -90, circumference: 180,
                    plugins: { legend: { display: false }, tooltip: { enabled: false } },
                },
            });
        }

        /* Clients (stacked horizontal) */
        const clientEl = document.getElementById('mmClientChart');
        if (clientEl && (DATA.clients || []).length) {
            charts.clients = new Chart(clientEl, {
                type: 'bar',
                data: {
                    labels: DATA.clients.map((c) => c.name),
                    datasets: [
                        { label: 'In-warranty',     data: DATA.clients.map((c) => c.iw),  backgroundColor: 'rgba(154,128,83,.75)', borderRadius: 4, borderSkipped: false },
                        { label: 'Out-of-warranty', data: DATA.clients.map((c) => c.oow), backgroundColor: 'rgba(57,56,55,.45)',  borderRadius: 4, borderSkipped: false },
                    ],
                },
                options: {
                    responsive: true, maintainAspectRatio: false, indexAxis: 'y',
                    plugins: { legend: { display: false }, tooltip: tooltipStyle },
                    scales: {
                        x: { stacked: true, grid: { color: C.grid }, ticks: { color: C.muted, font: { size: 10 } } },
                        y: { stacked: true, grid: { display: false }, ticks: { color: C.text, font: { size: 10 } } },
                    },
                },
            });
        }

        /* Front desk */
        const fdEl = document.getElementById('mmFrontDeskChart');
        if (fdEl && (DATA.frontDesk || []).length) {
            charts.frontDesk = new Chart(fdEl, {
                type: 'bar',
                data: {
                    labels: DATA.frontDesk.map((e) => e.initials),
                    datasets: [
                        { label: 'Completed', data: DATA.frontDesk.map((e) => e.completed), backgroundColor: 'rgba(154,128,83,.8)', borderRadius: 5, borderSkipped: false },
                        { label: 'Pending',   data: DATA.frontDesk.map((e) => e.pending),   backgroundColor: 'rgba(217,119,6,.45)', borderRadius: 5, borderSkipped: false },
                        { label: 'Rejected',  data: DATA.frontDesk.map((e) => e.rejected),  backgroundColor: 'rgba(220,38,38,.35)', borderRadius: 5, borderSkipped: false },
                    ],
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    plugins: { legend: { display: false }, tooltip: tooltipStyle },
                    scales: {
                        x: { grid: { display: false }, ticks: { color: C.muted, font: { size: 11 } } },
                        y: { grid: { color: C.grid }, ticks: { color: C.muted, font: { size: 10 } }, beginAtZero: true },
                    },
                },
            });
        }
    }

    /* Re-tint charts when the app's theme toggle flips data-bs-theme */
    function refreshCharts() {
        const C = palette();
        Object.values(charts).forEach((chart) => {
            if (!chart || !chart.options || !chart.options.scales) return;
            ['x', 'y'].forEach((axis) => {
                const scale = chart.options.scales[axis];
                if (!scale) return;
                if (scale.grid && scale.grid.color)  scale.grid.color  = C.grid;
                if (scale.ticks) scale.ticks.color = axis === 'y' && chart === charts.clients ? C.text : C.muted;
            });
            chart.update('none');
        });
    }

    new MutationObserver(refreshCharts).observe(document.documentElement, {
        attributes: true, attributeFilter: ['data-theme'],
    });

    /* ------------------------------------------------------------ toast */
    function toast(tone, title, body) {
        const wrap = document.getElementById('mmToasts');
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

    /* ------------------------------------------------------ detail panel */
    const panel    = document.getElementById('mmDetailPanel');
    const overlay  = document.getElementById('mmPanelOverlay');
    const panelBody = document.getElementById('mmPanelBody');

    function escapeHtml(value) {
        return String(value ?? '').replace(/[&<>"']/g, (ch) => ({
            '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;',
        }[ch]));
    }

    function renderPanelItems(items) {
        if (!items || !items.length) {
            return '<p class="empty"><i class="bi bi-inbox"></i>Nothing to show here yet.</p>';
        }
        return items.map((item) => {
            const colour = item.color || GOLD;
            return '<div class="pr">'
                +   '<div class="pr-top">'
                +     '<span class="pr-id">' + escapeHtml(item.reference) + '</span>'
                +     (item.badge
                        ? '<span class="pr-badge" style="background:' + escapeHtml(colour) + '22;color:' + escapeHtml(colour) + ';">'
                          + escapeHtml(item.badge) + '</span>'
                        : '')
                +   '</div>'
                +   (item.title ? '<div class="pr-client">' + escapeHtml(item.title) + '</div>' : '')
                +   (item.meta  ? '<div class="pr-meta"><i class="bi bi-geo-alt"></i>' + escapeHtml(item.meta) + '</div>' : '')
                + '</div>';
        }).join('');
    }

    function openPanel(type, id) {
        if (!DATA.panelUrl) return;

        document.getElementById('mmPanelTitle').textContent = 'Loading…';
        document.getElementById('mmPanelSub').textContent   = '';
        panelBody.innerHTML = '<p class="empty"><i class="bi bi-hourglass-split"></i>Fetching records…</p>';
        overlay.classList.add('open');
        panel.classList.add('open');
        document.getElementById('mmPanelClose').focus();

        const url = new URL(DATA.panelUrl, window.location.origin);
        // keep the panel in sync with the active dashboard filters
        new URLSearchParams(window.location.search).forEach((v, k) => url.searchParams.set(k, v));
        url.searchParams.set('type', type);
        if (id) url.searchParams.set('id', id);

        fetch(url, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
            .then((res) => { if (!res.ok) throw new Error(res.status); return res.json(); })
            .then((payload) => {
                document.getElementById('mmPanelTitle').textContent = payload.title || 'Details';
                document.getElementById('mmPanelSub').textContent   = payload.subtitle || '';
                document.getElementById('mmPanelIcon').innerHTML    = '<i class="bi ' + (payload.icon || 'bi-list') + '"></i>';
                panelBody.innerHTML = (payload.section ? '<div class="dp-sec">' + escapeHtml(payload.section) + '</div>' : '')
                                    + renderPanelItems(payload.items);
            })
            .catch(() => {
                document.getElementById('mmPanelTitle').textContent = 'Could not load details';
                panelBody.innerHTML = '<p class="empty"><i class="bi bi-wifi-off"></i>The request failed. Close the panel and try again.</p>';
            });
    }

    function closePanel() {
        overlay.classList.remove('open');
        panel.classList.remove('open');
    }

    root.addEventListener('click', (e) => {
        const trigger = e.target.closest('[data-panel]');
        if (trigger) {
            openPanel(trigger.dataset.panel, trigger.dataset.panelId || null);
            return;
        }
        if (e.target.closest('#mmPanelClose') || e.target === overlay) closePanel();
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && panel.classList.contains('open')) closePanel();
    });

    /* --------------------------------------------------- trend period tabs */
    root.querySelectorAll('.mc-tab').forEach((tab) => {
        tab.addEventListener('click', () => {
            const url = new URL(window.location.href);
            url.searchParams.set('period', tab.dataset.period);
            window.location.assign(url);
        });
    });

    /* ------------------------------------------------------------- boot */
    buildCharts();
    animateBars();

    window.mmDashToast = toast; // available if you want to flash server messages
})();
</script>
@endpush

{{--
|--------------------------------------------------------------------------
| Controller contract
|--------------------------------------------------------------------------
| return view('admin.dashboard', [
|     'greeting'    => 'Good morning, '.auth()->user()->first_name.'!',
|     'greetingSub' => "Here's your operations overview",
|     'today'       => now()->format('l, d F Y'),
|     'panelUrl'    => route('admin.dashboard.panel'),   // optional; drives the slide-in panel
|
|     'filters'       => ['range' => 'month', 'status' => null, 'client' => null, 'service' => null, 'period' => '6M'],
|     'rangeOptions'  => ['today' => 'Today', 'month' => 'This month', 'quarter' => 'This quarter'],
|     'trendPeriods'  => ['1M' => '1M', '6M' => '6M', '1Y' => '1Y'],
|     'statusOptions' => Status::pluck('name', 'id'),
|     'clientOptions' => Client::pluck('name', 'id'),
|     'serviceOptions'=> Service::pluck('name', 'id'),
|
|     'alerts' => [   // tone: red | amb | grn | blu
|         ['tone' => 'red', 'icon' => 'bi-exclamation-triangle-fill', 'label' => "$breaches SLA breaches", 'panel' => 'sla-breach'],
|     ],
|
|     'kpis' => [     // delta_tone: du (up/good) | dd (down/bad) | dn (neutral)
|         ['icon' => 'bi-ticket-detailed', 'value' => $totalSrs, 'label' => 'Total SRs',
|          'sub' => "vs {$lastMonth} last month", 'delta' => '+12%', 'delta_tone' => 'du',
|          'spark' => [32, 36, 38, 35, 40, 44, 47], 'panel' => 'month-srs'],
|     ],
|
|     'statusBreakdown' => [['label' => 'In progress', 'count' => 7, 'color' => '#9a8053']],
|
|     'srTrend' => [
|         'labels' => ['Feb','Mar','Apr','May','Jun','Jul'],
|         'in_warranty' => [...], 'out_warranty' => [...],
|         'in_warranty_total' => 31, 'out_warranty_total' => 16,
|         'in_warranty_change' => 7.2, 'out_warranty_change' => 14.3,
|     ],
|
|     'finance' => [
|         'invoiced_formatted' => 'AED 38.4k', 'expense_formatted' => 'AED 14.8k',
|         'net_formatted' => 'AED 23.6k', 'recovery_rate' => 61.5, 'currency' => 'AED',
|         'labels' => [...], 'invoiced_series' => [...], 'expense_series' => [...],
|     ],
|
|     'qc' => ['sla_compliance' => 84, 'first_pass_rate' => 78, 'first_pass_sub' => '33 of 43 jobs',
|              'rework_count' => 10, 'rework_sub' => '2 currently open',
|              'sla_breaches' => 3, 'sla_breach_sub' => 'All high priority'],
|
|     'whatsapp'   => ['delivered' => 182, 'failed' => 11, 'delivery_rate' => 94.3],
|     'waTriggers' => [['label' => 'Inquiry logged', 'sent' => 47, 'failed' => 2]],
|
|     'technicians' => [['id' => 1, 'initials' => 'MK', 'name' => '...', 'department' => 'Electrical',
|                        'jobs' => 12, 'hours' => 51, 'rating' => 4.8, 'rework' => 1,
|                        'punch_rate' => 98, 'expenses_formatted' => 'AED 5,840']],
|
|     'clients'   => [['id' => 1, 'name' => '...', 'short_name' => '...', 'srs' => 9,
|                      'in_warranty' => 6, 'out_warranty' => 3]],
|     'frontDesk' => [['initials' => 'SM', 'name' => '...', 'srs' => 18, 'completed' => 16, 'pending' => 2]],
|
|     'satisfaction'  => ['average' => 4.6, 'responses' => 31, 'response_rate' => 66,
|                         'completed_srs' => 43, 'flagged' => 'SR-2025-0033 rated 2 stars — flagged for review'],
|     'ratingBuckets' => [['stars' => 5, 'count' => 18], ['stars' => 4, 'count' => 8]],
| ]);
|
| Panel endpoint should return JSON:
| ['title' => '...', 'subtitle' => '...', 'icon' => 'bi-activity', 'section' => 'Open requests',
|  'items' => [['reference' => 'SR-2025-0041', 'badge' => 'In progress', 'color' => '#0891b2',
|               'title' => 'Client name', 'meta' => 'Electrical · Dubai Mall']]]
--}}