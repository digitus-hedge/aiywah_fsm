
@extends('layouts.layout')

@section('title', 'Head of Project')
@section('page_title', 'Admin - Head of Project')
@push('styles')
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400&display=swap" rel="stylesheet"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet"/>
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
  <style>

/* ── SF PRO DISPLAY ── */

/* ── MODERN SKIN ── */
:root,[data-bs-theme="light"]{
  --page-bg:#f4f2ef;--card:#fff;--card2:#faf9f7;
  --border:rgba(0,0,0,.07);--shadow:0 2px 20px rgba(0,0,0,.06);
  --shadow-hover:0 8px 32px rgba(0,0,0,.11);
  --text:#1a1614;--muted:#8a8480;--light:#bbb8b4;
  --gold:#9a8053;--gold2:#b8975e;--gold-bg:rgba(154,128,83,.1);
  --sidebar-bg:#fff;--sidebar-shadow:2px 0 20px rgba(0,0,0,.06);
  --topbar-bg:#fff;--topbar-shadow:0 1px 0 rgba(0,0,0,.08);
  --nav-link:#6b6560;--sb-width:256px;
  --toggle-track:#e0dcd8;--border-color:rgba(0,0,0,.07);
  --surface-2:#f4f2ef;--card-bg:#fff;--card-border:rgba(0,0,0,.07);
  --text-heading:#1a1614;--text-muted:#8a8480;--text-light:#bbb8b4;
  --text-primary:#1a1614;--card-shadow:0 2px 20px rgba(0,0,0,.06);
  --app-bg:#f4f2ef;--surface:#fff;
}
[data-bs-theme="dark"]{
  --page-bg:#141210;--card:#1e1b18;--card2:#252220;
  --border:rgba(255,255,255,.07);--shadow:0 2px 20px rgba(0,0,0,.4);
  --shadow-hover:0 8px 32px rgba(0,0,0,.5);
  --text:#e8e0d4;--muted:#7a756e;--light:#4a4540;
  --gold:#9a8053;--gold2:#b8975e;--gold-bg:rgba(154,128,83,.12);
  --sidebar-bg:#161412;--sidebar-shadow:2px 0 24px rgba(0,0,0,.5);
  --topbar-bg:#1a1714;--topbar-shadow:0 1px 0 rgba(255,255,255,.06);
  --nav-link:#8a8480;--sb-width:256px;
  --toggle-track:rgba(154,128,83,.2);--border-color:rgba(255,255,255,.07);
  --surface-2:#252220;--card-bg:#1e1b18;--card-border:rgba(255,255,255,.07);
  --text-heading:#e8e0d4;--text-muted:#7a756e;--text-light:#4a4540;
  --text-primary:#e8e0d4;--card-shadow:0 2px 20px rgba(0,0,0,.4);
  --app-bg:#141210;--surface:#1e1b18;
}

*,*::before,*::after{box-sizing:border-box;-webkit-tap-highlight-color:transparent;}
body{font-size:.875rem;
  background:var(--page-bg);color:var(--text);margin:0;overflow-x:hidden;
  transition:background .3s,color .3s;}
a{text-decoration:none;}




.cg,h1,h2,h3,h4,h5,h6,.brand-name,.greeting,.big-num,.stat-big,.panel-heading
{
letter-spacing:-.01em;
}

/* ── SIDEBAR ── */
.sidebar{width:var(--sb-width);min-height:100vh;background:var(--sidebar-bg);
  border-right:1px solid var(--border);display:flex;flex-direction:column;
  position:fixed;top:0;left:0;z-index:300;box-shadow:var(--sidebar-shadow);
  transition:transform .28s cubic-bezier(.4,0,.2,1);}
.sb-brand{display:flex;align-items:center;gap:11px;padding:18px 20px 15px;
  border-bottom:1px solid var(--border);flex-shrink:0;}
.sb-icon{width:38px;height:38px;flex-shrink:0;
  background:linear-gradient(135deg,#9a8053,#b8975e);border-radius:9px;
  display:flex;align-items:center;justify-content:center;color:#fff;
  font-size:.85rem;font-weight:700;}
.sb-name{font-size:1rem;font-weight:700;color:var(--text);line-height:1.2;}
.sb-sub{font-size:.6875rem;color:var(--muted);}
.sb-nav{flex:1;overflow-y:auto;padding:10px 0;}
.sb-nav::-webkit-scrollbar{width:3px;}
.sb-nav::-webkit-scrollbar-thumb{background:var(--border);border-radius:2px;}
.sb-sec{font-size:.625rem;font-weight:700;text-transform:uppercase;letter-spacing:.1em;
  color:var(--light);padding:14px 20px 4px;}
.sb-nav a{display:flex;align-items:center;gap:10px;padding:9px 20px;font-size:.8125rem;
  color:var(--nav-link);border-right:3px solid transparent;
  transition:background .15s,color .15s;}
.sb-nav a:hover{background:var(--gold-bg);color:var(--gold);}
.sb-nav a.active{background:var(--gold-bg);color:var(--gold);font-weight:500;
  border-right-color:var(--gold);}
.sb-nav a i{font-size:1rem;width:18px;text-align:center;flex-shrink:0;}
.sb-badge{font-size:.6rem;font-weight:700;padding:1px 6px;border-radius:9px;margin-left:auto;}
.sb-badge.orange{background:rgba(217,119,6,.15);color:#d97706;}
.sb-badge.red{background:rgba(239,68,68,.12);color:#ef4444;}
.sb-footer{padding:14px 18px;border-top:1px solid var(--border);flex-shrink:0;}
.sb-user{display:flex;align-items:center;gap:10px;}
.sb-av{width:34px;height:34px;flex-shrink:0;
  background:linear-gradient(135deg,#9a8053,#b8975e);border-radius:50%;
  display:flex;align-items:center;justify-content:center;color:#fff;
  font-size:.75rem;font-weight:700;}
.sb-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.4);
  z-index:299;backdrop-filter:blur(3px);}
.sb-overlay.show{display:block;}
@media(max-width:991.98px){.sidebar{transform:translateX(-100%);}
  .sidebar.open{transform:translateX(0);}}

/* ── TOPBAR ── */
.topbar{position:fixed;top:0;left:var(--sb-width);right:0;height:60px;
  background:var(--topbar-bg);border-bottom:1px solid var(--border);
  display:flex;align-items:center;justify-content:space-between;padding:0 28px;
  z-index:200;box-shadow:var(--topbar-shadow);backdrop-filter:blur(10px);}
.tb-l{display:flex;align-items:center;gap:12px;}
.hamburger{display:none;background:none;border:none;padding:6px;
  color:var(--text);cursor:pointer;border-radius:8px;font-size:1.25rem;line-height:1;}
.hamburger:hover{background:var(--gold-bg);}
.tb-title{font-size:.9375rem;font-weight:600;color:var(--text);
  display:flex;align-items:center;gap:7px;}
.tb-bc{font-size:.72rem;color:var(--muted);}
.tb-bc a{color:var(--gold);}
.tb-r{display:flex;align-items:center;gap:10px;}
.role-pill{font-size:.72rem;background:var(--gold-bg);color:var(--gold);
  padding:4px 11px;border-radius:20px;font-weight:500;white-space:nowrap;}
.clk{font-size:.72rem;color:var(--muted);font-variant-numeric:tabular-nums;white-space:nowrap;}
.tdiv{width:1px;height:22px;background:var(--border);}
.th-toggle{display:flex;align-items:center;gap:7px;cursor:pointer;user-select:none;}
.th-sun{color:#fbbc06;font-size:.8rem;}.th-moon{color:var(--gold2);font-size:.8rem;}
.tt-track{width:42px;height:22px;background:var(--toggle-track);border-radius:11px;
  position:relative;border:1px solid var(--border);}
.tt-thumb{width:16px;height:16px;background:#fff;border-radius:50%;
  position:absolute;top:2px;left:2px;transition:transform .3s,background .3s;
  box-shadow:0 1px 4px rgba(0,0,0,.15);
  display:flex;align-items:center;justify-content:center;}
[data-bs-theme="dark"] .tt-thumb{transform:translateX(20px);background:var(--gold);}
.ts-sun{font-size:8px;color:#fbbc06;}.ts-moon{font-size:8px;color:#fff;display:none;}
[data-bs-theme="dark"] .ts-sun{display:none;}[data-bs-theme="dark"] .ts-moon{display:block;}
.av-btn{width:34px;height:34px;background:linear-gradient(135deg,#9a8053,#b8975e);
  border-radius:50%;display:flex;align-items:center;justify-content:center;
  color:#fff;font-size:.8rem;font-weight:700;cursor:pointer;}
@media(max-width:991.98px){.topbar{left:0;}
  .hamburger{display:flex;align-items:center;justify-content:center;}
  .tb-bc,.role-pill,.clk,.tdiv{display:none;}}

/* ── MAIN ── */
.main{margin-left:var(--sb-width);margin-top:60px;padding:28px 28px 80px;
  min-height:calc(100vh - 60px);}
@media(max-width:991.98px){.main{margin-left:0;}}
@media(max-width:575px){.main{padding:16px 14px 80px;}}

/* ── GREETING ROW ── */
.greeting-row{display:flex;align-items:flex-start;justify-content:space-between;
  gap:20px;margin-bottom:24px;flex-wrap:wrap;}
.greeting{font-size:1.6rem;font-weight:700;color:var(--text);margin-bottom:3px;}
.greeting-sub{font-size:.82rem;color:var(--muted);}

/* ── FILTER BAR ── */
.filter-bar{display:flex;align-items:center;gap:8px;flex-wrap:wrap;}
.fq-pill{padding:6px 13px;border-radius:20px;font-size:.78rem;font-weight:500;
  cursor:pointer;border:1px solid var(--border);background:var(--card);
  color:var(--muted);transition:all .15s;white-space:nowrap;}
.fq-pill:hover{border-color:var(--gold);color:var(--gold);}
.fq-pill.active{background:var(--gold);color:#fff;border-color:var(--gold);}
.f-sel{height:32px;padding:0 10px;border:1px solid var(--border);border-radius:20px;
  background:var(--card);color:var(--text);font-size:.78rem;cursor:pointer;
  transition:border-color .15s;}
.f-sel:focus{outline:none;border-color:var(--gold);}

/* ── ALERT STRIP ── */
.alert-row{display:flex;gap:9px;margin-bottom:20px;flex-wrap:wrap;}
.a-chip{display:inline-flex;align-items:center;gap:6px;padding:7px 13px;
  border-radius:9px;font-size:.76rem;font-weight:500;cursor:pointer;
  transition:opacity .15s;}
.a-chip:hover{opacity:.82;}
.a-red{background:rgba(220,38,38,.08);border:1px solid rgba(220,38,38,.16);color:#dc2626;}
.a-amb{background:rgba(217,119,6,.08);border:1px solid rgba(217,119,6,.16);color:#d97706;}
.a-grn{background:rgba(21,128,61,.08);border:1px solid rgba(21,128,61,.16);color:#15803d;}
.a-blu{background:rgba(37,99,235,.08);border:1px solid rgba(37,99,235,.16);color:#2563eb;}

/* ── CARDS ── */
.card{background:var(--card);border-radius:16px;box-shadow:var(--shadow);
  overflow:hidden;transition:box-shadow .2s,transform .2s;}
.card.clickable{cursor:pointer;}
.card.clickable:hover{box-shadow:var(--shadow-hover);transform:translateY(-2px);}
.card-pad{padding:20px 22px;}
.c-hdr{display:flex;align-items:center;justify-content:space-between;
  gap:8px;margin-bottom:16px;flex-wrap:wrap;}
.c-label{font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.09em;
  color:var(--muted);display:flex;align-items:center;gap:6px;}
.c-label i{color:var(--gold);font-size:.85rem;}
.c-more{font-size:.72rem;color:var(--gold);cursor:pointer;
  display:flex;align-items:center;gap:3px;white-space:nowrap;}
.c-more:hover{opacity:.8;}

/* ── KPI CARDS ── */
.kpi-row{display:grid;grid-template-columns:repeat(5,1fr);gap:14px;margin-bottom:20px;}
@media(max-width:1200px){.kpi-row{grid-template-columns:repeat(3,1fr);}}
@media(max-width:700px){.kpi-row{grid-template-columns:1fr 1fr;}}
@media(max-width:420px){.kpi-row{grid-template-columns:1fr;}}
.kpi{background:var(--card);border-radius:16px;padding:20px;
  box-shadow:var(--shadow);cursor:pointer;transition:all .2s;
  display:flex;flex-direction:column;gap:10px;position:relative;overflow:hidden;}
.kpi:hover{box-shadow:var(--shadow-hover);transform:translateY(-2px);}
.kpi::after{content:'';position:absolute;right:-20px;top:-20px;
  width:80px;height:80px;border-radius:50%;opacity:.06;}
.kpi.k1::after{background:#9a8053;}
.kpi.k2::after{background:#2563eb;}
.kpi.k3::after{background:#15803d;}
.kpi.k4::after{background:#d97706;}
.kpi.k5::after{background:#7c3aed;}
.kpi-row-top{display:flex;align-items:center;justify-content:space-between;}
.kpi-ico{width:36px;height:36px;border-radius:10px;display:flex;align-items:center;
  justify-content:center;font-size:.95rem;flex-shrink:0;}
.i1{background:rgba(154,128,83,.12);color:var(--gold);}
.i2{background:rgba(37,99,235,.1);color:#2563eb;}
.i3{background:rgba(21,128,61,.1);color:#15803d;}
.i4{background:rgba(217,119,6,.12);color:#d97706;}
.i5{background:rgba(124,58,237,.1);color:#7c3aed;}
.kpi-delta{font-size:.68rem;padding:2px 7px;border-radius:8px;font-weight:600;
  display:inline-flex;align-items:center;gap:2px;}
.du{background:rgba(21,128,61,.1);color:#15803d;}
.dd{background:rgba(220,38,38,.1);color:#dc2626;}
.dn{background:rgba(0,0,0,.06);color:var(--muted);}
[data-bs-theme="dark"] .dn{background:rgba(255,255,255,.07);}
.big-num{font-size:2rem;font-weight:700;color:var(--text);line-height:1;}
.kpi-lbl{font-size:.73rem;color:var(--muted);font-weight:500;}
/* Bounded spark */
.sp-wrap{height:38px;width:100%;position:relative;margin-top:4px;}
.sp-wrap canvas{position:absolute;inset:0;width:100%!important;height:100%!important;}

/* ── MAIN CHART SECTION (like reference: two big numbers + chart) ── */
.main-chart-card{background:var(--card);border-radius:16px;
  box-shadow:var(--shadow);padding:24px 26px;}
.mc-top{display:flex;align-items:flex-start;justify-content:space-between;
  gap:20px;margin-bottom:20px;flex-wrap:wrap;}
.mc-nums{display:flex;gap:28px;flex-wrap:wrap;}
.mc-num-block{}
.mc-num-label{font-size:.7rem;font-weight:700;text-transform:uppercase;
  letter-spacing:.09em;color:var(--muted);margin-bottom:5px;
  display:flex;align-items:center;gap:6px;}
.mc-num-label span{display:inline-block;width:9px;height:9px;border-radius:3px;flex-shrink:0;}
.mc-num-big{font-size:2rem;
  font-weight:700;line-height:1;}
.mc-num-sub{font-size:.7rem;color:var(--muted);margin-top:3px;
  display:flex;align-items:center;gap:4px;}
.mc-period-tabs{display:flex;gap:4px;}
.mc-tab{padding:5px 12px;border-radius:20px;font-size:.73rem;font-weight:500;
  cursor:pointer;border:1px solid var(--border);background:transparent;
  color:var(--muted);transition:all .15s;}
.mc-tab.active{background:var(--gold);color:#fff;border-color:var(--gold);}
/* BOUNDED chart */
.ch-220{position:relative;width:100%;height:220px;}
.ch-220 canvas{position:absolute;inset:0;width:100%!important;height:100%!important;}
.ch-180{position:relative;width:100%;height:180px;}
.ch-180 canvas{position:absolute;inset:0;width:100%!important;height:100%!important;}
.ch-160{position:relative;width:100%;height:160px;}
.ch-160 canvas{position:absolute;inset:0;width:100%!important;height:100%!important;}
.ch-140{position:relative;width:100%;height:140px;}
.ch-140 canvas{position:absolute;inset:0;width:100%!important;height:100%!important;}
.ch-d{position:relative;width:160px;height:160px;flex-shrink:0;}
.ch-d canvas{position:absolute;inset:0;width:100%!important;height:100%!important;}
.ch-d-sm{position:relative;width:110px;height:110px;flex-shrink:0;}
.ch-d-sm canvas{position:absolute;inset:0;width:100%!important;height:100%!important;}
.ch-half{position:relative;width:100%;height:110px;}
.ch-half canvas{position:absolute;inset:0;width:100%!important;height:100%!important;}

/* ── GRIDS ── */
.g2{display:grid;grid-template-columns:1fr 1fr;gap:16px;}
.g3{display:grid;grid-template-columns:repeat(3,1fr);gap:16px;}
.g4{display:grid;grid-template-columns:repeat(4,1fr);gap:16px;}
.g3-1{display:grid;grid-template-columns:3fr 1fr;gap:16px;}
.g2-3{display:grid;grid-template-columns:2fr 3fr;gap:16px;}
@media(max-width:1200px){.g3,.g4{grid-template-columns:1fr 1fr;}
  .g3-1,.g2-3{grid-template-columns:1fr;}}
@media(max-width:700px){.g2,.g3,.g4,.g3-1,.g2-3{grid-template-columns:1fr;}}

/* ── SECTION LABEL ── */
.sec-row{display:flex;align-items:center;gap:12px;margin:24px 0 14px;}
.sec-line{flex:1;height:1px;background:var(--border);}
.sec-ttl{font-size:.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.1em;
  color:var(--light);white-space:nowrap;}

/* ── STATUS PILLS (distribution) ── */
.status-dist{display:flex;flex-direction:column;gap:8px;}
.sd-row{display:flex;align-items:center;gap:10px;}
.sd-label{font-size:.73rem;color:var(--muted);width:110px;flex-shrink:0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
.sd-track{flex:1;height:7px;background:rgba(0,0,0,.06);border-radius:4px;overflow:hidden;}
[data-bs-theme="dark"] .sd-track{background:rgba(255,255,255,.07);}
.sd-fill{height:100%;border-radius:4px;transition:width 1s cubic-bezier(.4,0,.2,1);}
.sd-n{font-size:.72rem;font-weight:600;color:var(--text);width:22px;text-align:right;flex-shrink:0;}

/* ── MINI METRIC BLOCKS ── */
.mini-metric{background:var(--card2);border-radius:10px;padding:12px 14px;}
.mm-label{font-size:.68rem;color:var(--muted);font-weight:500;margin-bottom:4px;}
.mm-val{font-size:1.4rem;
  font-weight:700;color:var(--text);line-height:1;}
.mm-sub{font-size:.68rem;color:var(--muted);margin-top:3px;}

/* ── TECH TABLE ── */
.t-tbl{width:100%;border-collapse:collapse;}
.t-tbl thead th{padding:9px 14px;font-size:.67rem;font-weight:700;
  text-transform:uppercase;letter-spacing:.07em;color:var(--muted);
  background:var(--card2);border-bottom:1px solid var(--border);white-space:nowrap;}
.t-tbl tbody tr{border-bottom:1px solid var(--border);cursor:pointer;transition:background .1s;}
.t-tbl tbody tr:last-child{border-bottom:none;}
.t-tbl tbody tr:hover{background:var(--gold-bg);}
.t-tbl td{padding:10px 14px;font-size:.79rem;vertical-align:middle;}
.t-av{width:28px;height:28px;border-radius:50%;background:linear-gradient(135deg,#9a8053,#b8975e);
  display:flex;align-items:center;justify-content:center;color:#fff;
  font-size:.65rem;font-weight:700;flex-shrink:0;}
.t-name{font-weight:600;color:var(--text);font-size:.8rem;}
.t-domain{font-size:.69rem;color:var(--muted);}
.t-bar-wrap{height:6px;background:rgba(0,0,0,.06);border-radius:3px;
  overflow:hidden;min-width:70px;}
[data-bs-theme="dark"] .t-bar-wrap{background:rgba(255,255,255,.07);}
.t-bar-fill{height:100%;background:linear-gradient(90deg,#9a8053,#b8975e);
  border-radius:3px;transition:width 1s cubic-bezier(.4,0,.2,1);}
.t-stars{color:#f59e0b;font-size:.68rem;display:flex;gap:1px;}
.t-pill{font-size:.67rem;font-weight:700;padding:2px 7px;border-radius:8px;
  display:inline-flex;align-items:center;gap:3px;}
.p0{background:rgba(21,128,61,.1);color:#15803d;}
.p1{background:rgba(217,119,6,.1);color:#d97706;}
.p2{background:rgba(220,38,38,.1);color:#dc2626;}

/* ── HIST ── */
.h-row{display:flex;align-items:center;gap:8px;margin-bottom:7px;}
.h-row:last-child{margin-bottom:0;}
.h-lbl{font-size:.7rem;color:var(--muted);width:18px;text-align:right;flex-shrink:0;}
.h-track{flex:1;height:8px;background:rgba(0,0,0,.06);border-radius:4px;overflow:hidden;}
[data-bs-theme="dark"] .h-track{background:rgba(255,255,255,.07);}
.h-fill{height:100%;background:linear-gradient(90deg,#f59e0b,#fbbf24);border-radius:4px;
  transition:width 1s cubic-bezier(.4,0,.2,1);}
.h-n{font-size:.7rem;color:var(--muted);width:22px;flex-shrink:0;}

/* ── LEGEND ROW ── */
.leg{display:flex;align-items:center;gap:6px;font-size:.73rem;color:var(--muted);}
.leg-dot{width:9px;height:9px;border-radius:3px;flex-shrink:0;}

/* ── SLIDE-IN DETAIL PANEL ── */
.panel-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.35);
  z-index:700;backdrop-filter:blur(3px);}
.panel-overlay.open{display:block;}
.detail-panel{position:fixed;top:0;right:-520px;width:480px;max-width:95vw;
  height:100vh;background:var(--card);z-index:800;
  box-shadow:-6px 0 40px rgba(0,0,0,.14);
  transition:right .35s cubic-bezier(.4,0,.2,1);
  display:flex;flex-direction:column;overflow:hidden;}
.detail-panel.open{right:0;}
.dp-hdr{display:flex;align-items:center;justify-content:space-between;
  padding:18px 22px;border-bottom:1px solid var(--border);flex-shrink:0;
  background:linear-gradient(135deg,#9a8053,#b8975e);color:#fff;}
.dp-hdr-left{display:flex;align-items:center;gap:10px;}
.dp-hdr-icon{width:34px;height:34px;border-radius:8px;background:rgba(255,255,255,.2);
  display:flex;align-items:center;justify-content:center;font-size:.9rem;flex-shrink:0;}
.panel-heading{font-size:1rem;font-weight:700;margin-bottom:1px;}
.dp-hdr-sub{font-size:.72rem;opacity:.85;}
.dp-close{background:rgba(255,255,255,.2);border:1px solid rgba(255,255,255,.3);
  color:#fff;width:30px;height:30px;border-radius:7px;display:flex;
  align-items:center;justify-content:center;cursor:pointer;
  font-size:.85rem;transition:background .15s;}
.dp-close:hover{background:rgba(255,255,255,.3);}
.dp-body{flex:1;overflow-y:auto;padding:16px 22px;}
.dp-body::-webkit-scrollbar{width:4px;}
.dp-body::-webkit-scrollbar-thumb{background:var(--border);border-radius:2px;}
/* Panel row */
.pr{padding:12px 14px;border-radius:10px;border:1px solid var(--border);
  margin-bottom:9px;cursor:pointer;transition:all .15s;background:var(--card2);}
.pr:last-child{margin-bottom:0;}
.pr:hover{border-color:var(--gold);background:var(--gold-bg);}
.pr-top{display:flex;align-items:center;justify-content:space-between;
  margin-bottom:5px;gap:8px;}
.pr-id{font-size:.8rem;font-weight:700;color:var(--gold);}
.pr-badge{font-size:.65rem;font-weight:700;padding:2px 7px;border-radius:8px;}
.pr-client{font-size:.8rem;font-weight:500;color:var(--text);margin-bottom:2px;}
.pr-meta{font-size:.72rem;color:var(--muted);display:flex;align-items:center;gap:10px;flex-wrap:wrap;}
.pr-meta i{font-size:.7rem;}
/* Panel section head */
.dp-sec{font-size:.67rem;font-weight:700;text-transform:uppercase;letter-spacing:.09em;
  color:var(--light);margin:14px 0 8px;padding-bottom:6px;
  border-bottom:1px solid var(--border);}
.dp-sec:first-child{margin-top:0;}

/* ── TOAST ── */
#tw{position:fixed;bottom:22px;right:22px;z-index:9999;
  display:flex;flex-direction:column;gap:8px;pointer-events:none;}
.ti{display:flex;align-items:flex-start;gap:9px;padding:11px 14px;border-radius:10px;
  background:var(--card);border:1px solid var(--border);
  box-shadow:0 4px 20px rgba(0,0,0,.1);min-width:220px;max-width:290px;
  animation:tin .2s ease;pointer-events:auto;}
@keyframes tin{from{opacity:0;transform:translateY(8px);}to{opacity:1;transform:none;}}
.ti-t{font-size:.79rem;font-weight:600;color:var(--text);margin:0 0 2px;}
.ti-b{font-size:.73rem;color:var(--muted);margin:0;}
.ok{color:#15803d;}.warn{color:#d97706;}.info{color:var(--gold);}

/* ── FINANCIAL ROW ── */
.fin-num-row{display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:14px;}
.fin-block{background:var(--card2);border-radius:10px;padding:12px 14px;}
.fin-lbl{font-size:.68rem;color:var(--muted);font-weight:500;margin-bottom:4px;}
.fin-val{font-size:1.35rem;
  font-weight:700;color:var(--text);line-height:1;}
.fin-val.g{color:#15803d;}.fin-val.r{color:#dc2626;}

/* ── GAUGE ── */
.gauge-wrap{text-align:center;}
.gauge-num{font-size:1.8rem;
  font-weight:700;color:var(--text);margin-top:-18px;position:relative;z-index:1;}
.gauge-lbl{font-size:.7rem;color:var(--muted);margin-top:2px;}


.q-item{padding:11px 13px;border-radius:10px;border:1px solid var(--border);background:var(--card2);
  margin-bottom:8px;cursor:pointer;transition:all .15s;}
.q-item:last-child{margin-bottom:0;}
.q-item:hover{border-color:var(--gold);background:var(--gold-bg);}
.q-top{display:flex;align-items:center;justify-content:space-between;gap:8px;margin-bottom:3px;}
.q-id{font-size:.78rem;font-weight:700;color:var(--gold);}
.q-badge{font-size:.62rem;font-weight:700;padding:2px 7px;border-radius:8px;white-space:nowrap;}
.q-client{font-size:.76rem;font-weight:500;color:var(--text);margin-bottom:1px;}
.q-meta{font-size:.68rem;color:var(--muted);}
.q-empty{font-size:.74rem;color:var(--muted);text-align:center;padding:14px 0;}
.util-row{display:flex;align-items:center;justify-content:space-between;padding:8px 0;
  border-bottom:1px solid var(--border);font-size:.76rem;}
.util-row:last-child{border-bottom:none;}
.util-dot{width:9px;height:9px;border-radius:50%;flex-shrink:0;display:inline-block;margin-right:7px;}
.eff-grid{display:grid;grid-template-columns:1fr 1fr;gap:10px;}
.eff-block{background:var(--card2);border-radius:10px;padding:12px 14px;}
.eff-lbl{font-size:.67rem;color:var(--muted);font-weight:500;margin-bottom:4px;}
.eff-val{font-size:1.35rem;font-weight:700;line-height:1;}
.eff-sub{font-size:.66rem;color:var(--muted);margin-top:3px;}
.zone-tag{font-size:.6rem;font-weight:700;padding:1px 6px;border-radius:6px;background:var(--gold-bg);color:var(--gold);}

.page-content
{
    padding:unset;
}



/* ── FILTER LAYOUT ── */
.filter-wrap{
  display:flex;
  flex-direction:column;
  gap:10px;
  align-items:flex-start;
}
.filter-bar{
  display:flex;
  align-items:center;
  gap:9px;
  flex-wrap:wrap;
}

/* ── DATE RANGE PILL ── */
.f-daterange{
  display:inline-flex;
  align-items:center;
  gap:8px;
  height:32px;
  padding:0 14px;
  border:1px solid var(--border);
  border-radius:20px;
  background:var(--card);
  transition:border-color .15s, background .15s;
  box-sizing:border-box;
}
.f-daterange.active{ border-color:var(--gold); background:var(--gold-bg); }
.f-daterange:focus-within{ border-color:var(--gold); }
.f-daterange > i.bi-calendar-range{
  font-size:.82rem;
  color:var(--gold);
  flex-shrink:0;
  line-height:1;
}

.f-daterange input[type="date"]{
  -webkit-appearance:none;
  appearance:none;
  border:0 !important;
  outline:0;
  background:transparent !important;
  box-shadow:none !important;
  color:var(--text);
  font-family:inherit;
  font-size:.75rem;
  line-height:1;
  padding:0;
  margin:0;
  width:88px;
  height:100%;
  cursor:pointer;
}
.f-daterange input[type="date"]:focus{ outline:none; box-shadow:none; }
.f-daterange input[type="date"]:not(:valid){ color:var(--muted); }

.f-daterange input[type="date"]::-webkit-calendar-picker-indicator{
  opacity:.45;
  cursor:pointer;
  padding:0;
  margin:0;
  width:13px;
  height:13px;
  transition:opacity .15s;
}
.f-daterange input[type="date"]::-webkit-calendar-picker-indicator:hover{ opacity:.9; }
[data-bs-theme="dark"] .f-daterange input[type="date"]::-webkit-calendar-picker-indicator{
  filter:invert(1);
}

.f-date-sep{
  font-size:.72rem;
  color:var(--muted);
  flex-shrink:0;
  line-height:1;
}

/* ── RESET ── */
.fq-reset{
  display:inline-flex;
  align-items:center;
  gap:5px;
  height:32px;
  padding:0 14px;
  border-radius:20px;
  border:1px solid rgba(220,38,38,.22);
  background:rgba(220,38,38,.07);
  color:#dc2626;
  font-size:.76rem;
  font-weight:500;
  white-space:nowrap;
  cursor:pointer;
  transition:background .15s, border-color .15s;
  box-sizing:border-box;
}
.fq-reset:hover{ background:rgba(220,38,38,.14); border-color:rgba(220,38,38,.35); }
.fq-reset i{ font-size:.8rem; line-height:1; }

/* ── GREETING SUB ── */
.greeting-sub{
  font-size:.82rem;
  color:var(--muted);
  display:flex;
  align-items:center;
  gap:7px;
  flex-wrap:wrap;
}
.greeting-sub > i{ color:var(--gold); font-size:.8rem; }
.gs-dot{ opacity:.35; }
.gs-range{
  display:inline-flex;
  align-items:center;
  gap:5px;
  padding:2px 10px;
  border-radius:20px;
  background:var(--gold-bg);
  color:var(--gold);
  font-size:.72rem;
  font-weight:500;
}
.gs-range i{ font-size:.68rem; color:var(--gold); }

@media(max-width:600px){
  .f-daterange{ width:100%; justify-content:space-between; }
  .f-daterange input[type="date"]{ width:auto; flex:1; }
}


.leg-row{ display:flex; flex-wrap:wrap; gap:12px; margin-top:9px; }
.client-list{ margin-top:12px; display:flex; flex-direction:column; gap:2px; }

.lrow{
  display:flex; align-items:center; justify-content:space-between;
  width:100%; padding:6px 9px;
  background:transparent; border:0; border-radius:8px;
  font-size:.75rem; text-align:left; cursor:pointer;
  transition:background .12s;
}
.lrow:hover{ background:var(--gold-bg); }
.lrow-l{ display:flex; align-items:center; gap:8px; min-width:0; }
.lrow-name{
  font-weight:500; color:var(--text);
  overflow:hidden; text-overflow:ellipsis; white-space:nowrap;
}
.lrow-r{ display:flex; align-items:center; gap:5px; flex-shrink:0; }
.lrow-r strong{ color:var(--text); font-weight:700; }
.lrow-unit{ color:var(--muted); }

.rank{
  display:inline-flex; align-items:center; justify-content:center;
  width:22px; height:22px; border-radius:6px;
  background:var(--gold-bg); color:var(--gold);
  font-size:.6rem; font-weight:700; flex-shrink:0;
}

.empty{ padding:22px 14px; text-align:center; color:var(--muted); font-size:.78rem; }
.empty i{ display:block; font-size:1.4rem; margin-bottom:6px; opacity:.4; }

/* workforce split cells */
.wf-split{ display:grid; grid-template-columns:1fr 1fr; gap:8px; }
.wf-cell{ background:var(--card2); border-radius:8px; padding:8px 10px; }
.wf-cell-val{ font-size:1.15rem; font-weight:700; color:var(--text); line-height:1; }
.wf-cell-lbl{ font-size:.66rem; color:var(--muted); margin-top:3px; }

.wf-sec-lbl{
  font-size:.68rem; font-weight:700; text-transform:uppercase;
  letter-spacing:.08em; color:var(--light); margin-bottom:9px;
}

/* capacity rows */
.util-row{
  display:flex; align-items:center; justify-content:space-between;
  padding:8px 0; border-bottom:1px solid var(--border); font-size:.76rem;
}
.util-row:last-child{ border-bottom:none; }
.util-dot{
  width:9px; height:9px; border-radius:50%;
  flex-shrink:0; display:inline-block; margin-right:7px;
}
</style>
@endpush

<div class="sb-overlay" id="sbOverlay" onclick="closeSB()"></div>


<header class="topbar" style="display:none;">
  <div class="tb-l">
    <button class="hamburger" onclick="toggleSB()"><i class="bi bi-list"></i></button>
    <div>
      <div class="tb-title"><i class="bi bi-grid-1x2" style="color:var(--gold);"></i>Head of Projects Dashboard</div>
      <div class="tb-bc"><a href="#">Home</a> / Dashboard / Projects Overview</div>
    </div>
  </div>
  <div class="tb-r">
    <span class="role-pill"><i class="bi bi-diagram-3 me-1"></i>Head of Projects</span>
    <span class="clk" id="clkTxt"></span>
    <div class="tdiv"></div>
    <div class="th-toggle" onclick="toggleTheme()">
      <i class="bi bi-sun-fill th-sun"></i>
      <div class="tt-track"><div class="tt-thumb"><i class="bi bi-sun-fill ts-sun"></i><i class="bi bi-moon-stars-fill ts-moon"></i></div></div>
      <i class="bi bi-moon-stars-fill th-moon"></i>
    </div>
    <div class="tdiv"></div>
    <div class="av-btn">TA</div>
  </div>
</header>



@section('content')
  <!-- GREETING + FILTERS -->
  <div class="greeting-row">
    <div>
     
<div class="greeting">{{ $greeting }}</div>
<div class="greeting-sub">
  <i class="bi bi-calendar3"></i>{{ $today }}
  <span class="gs-dot">·</span>{{ $greetingSub }}
  <span class="gs-range"><i class="bi bi-funnel"></i>{{ $rangeLabel }}</span>
</div>    </div>
    <!-- <div class="filter-bar">
      <button class="fq-pill" onclick="setDateFilter(this,'Today')">Today</button>
      <button class="fq-pill active" onclick="setDateFilter(this,'This Month')">This Month</button>
      <button class="fq-pill" onclick="setDateFilter(this,'This Quarter')">This Quarter</button>
      <select class="f-sel"><option>All Leads</option><option>Rashid Al-Habsi</option><option>Salim Nasser</option><option>Yousuf Rahman</option><option>Kareem Adel</option><option>Nadia Faris</option></select>
      <select class="f-sel"><option>All Trades</option><option>Electrical</option><option>Mechanical</option><option>HVAC</option><option>Plumbing</option></select>
      <select class="f-sel"><option>All Zones</option><option>Dubai</option><option>Abu Dhabi</option><option>Sharjah</option><option>Northern</option></select>
    </div> -->

   <form method="GET" action="{{ url()->current() }}" class="filter-bar" id="dashFilters">
    <input type="hidden" name="period" value="{{ $filters['period'] ?? '6M' }}">
    <input type="hidden" name="range" id="rangeField" value="{{ $filters['range'] ?? 'month' }}">

    <button type="button" onclick="setRange(this,'today')"
            class="fq-pill {{ ($filters['range'] ?? null) === 'today' ? 'active' : '' }}">Today</button>
    <button type="button" onclick="setRange(this,'month')"
            class="fq-pill {{ ($filters['range'] ?? null) === 'month' ? 'active' : '' }}">This Month</button>
    <button type="button" onclick="setRange(this,'quarter')"
            class="fq-pill {{ ($filters['range'] ?? null) === 'quarter' ? 'active' : '' }}">This Quarter</button>

    <div class="f-daterange {{ ($filters['range'] ?? null) === 'custom' ? 'active' : '' }}">
        <i class="bi bi-calendar-range"></i>
        <input type="date" name="from" value="{{ $filters['from'] ?? '' }}"
               max="{{ now()->toDateString() }}" onchange="applyCustomRange(this)" aria-label="From date">
        <span class="f-date-sep">→</span>
        <input type="date" name="to" value="{{ $filters['to'] ?? '' }}"
               max="{{ now()->toDateString() }}" onchange="applyCustomRange(this)" aria-label="To date">
    </div>

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

    @php
    $hasFilters = ($filters['range'] ?? 'today') !== 'today' || array_filter([
        $filters['status']  ?? null, $filters['client'] ?? null,
        $filters['service'] ?? null, $filters['from']   ?? null,
        $filters['to']      ?? null,
    ]);
@endphp
    @if ($hasFilters)
        <a href="{{ url()->current() }}" class="fq-reset" title="Clear all filters">
            <i class="bi bi-arrow-counterclockwise"></i>Reset
        </a>
    @endif
</form>
</div>
  <!-- ALERT STRIP -->
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

  <!-- KPI ROW -->
  <div class="kpi-row" style="margin-bottom:20px;" id="kpiRow"></div>

  <!-- STATUS DIST + THROUGHPUT TREND -->
  <div class="g2-3" style="margin-bottom:16px;">

    <!-- STATUS DISTRIBUTION -->
    <!-- <div class="card card-pad">
      <div class="c-hdr">
        <div class="c-label"><i class="bi bi-bar-chart-steps"></i>SR Pipeline by Stage</div>
        <div class="c-more" onclick="openPanel('active-srs',null)">View all <i class="bi bi-arrow-right"></i></div>
      </div>
      <div id="statusDist" style="margin-bottom:14px;"></div>
      <div style="display:flex;flex-wrap:wrap;gap:12px;padding-top:12px;border-top:1px solid var(--border);">
        <div class="leg"><span class="leg-dot" style="background:#9a8053;"></span>Field / Active</div>
        <div class="leg"><span class="leg-dot" style="background:#2563eb;"></span>Intake</div>
        <div class="leg"><span class="leg-dot" style="background:#15803d;"></span>Completed</div>
        <div class="leg"><span class="leg-dot" style="background:#dc2626;"></span>Issues</div>
      </div>
    </div> -->


    <div class="card card-pad">
  <div class="c-hdr">
    <div class="c-label"><i class="bi bi-bar-chart-steps"></i>SR Pipeline by Stage</div>
    <div class="c-more" onclick="openPanel('all-srs',null)">View all <i class="bi bi-arrow-right"></i></div>
  </div>

  <div id="statusDist" style="margin-bottom:14px;"></div>

  <div style="display:flex;flex-wrap:wrap;gap:12px;padding-top:12px;border-top:1px solid var(--border);">
    @foreach ($statusLegend as $group => $color)
      <div class="leg"><span class="leg-dot" style="background:{{ $color }};"></span>{{ $group }}</div>
    @endforeach
  </div>
</div>

    <!-- THROUGHPUT TREND -->
    <div class="main-chart-card">
      <div class="mc-top">
        <div class="mc-nums">
          <div class="mc-num-block">
               <div class="mc-num-label"><span style="background:#9a8053;"></span>Jobs Completed</div>
    <div class="mc-num-big" style="color:#9a8053;">{{ $srTrend2['doneTotal'] }}</div>
            
          </div>
          <div class="mc-num-block">
    <div class="mc-num-label"><span style="background:#393837;"></span>New Inquiries</div>
    <div class="mc-num-big" style="color:#393837;">{{ $srTrend2['inqTotal'] }}</div>
  </div>
</div>
      <div class="mc-period-tabs">
  @php $p = $filters['period'] ?? '6M'; @endphp

  <button class="mc-tab {{ $p === '1M' ? 'active' : '' }}" onclick="setPeriod(this,'1M')">1M</button>
  <button class="mc-tab {{ $p === '6M' ? 'active' : '' }}" onclick="setPeriod(this,'6M')">6M</button>
  <button class="mc-tab {{ $p === '1Y' ? 'active' : '' }}" onclick="setPeriod(this,'1Y')">1Y</button>
</div>
      </div>
      <div style="display:flex;gap:14px;margin-bottom:12px;flex-wrap:wrap;">
        <div class="leg"><span class="leg-dot" style="background:#9a8053;"></span>Completed</div>
        <div class="leg"><span class="leg-dot" style="background:#393837;"></span>New Inquiries</div>
      </div>
      <div class="ch-220"><canvas id="srTrend"></canvas></div>
    </div>
  </div>



  @php
    $carry = array_filter([
        'range'   => $filters['range']   ?? null,
        'from'    => $filters['from']    ?? null,
        'to'      => $filters['to']      ?? null,
        'client'  => $filters['client']  ?? null,
        'service' => $filters['service'] ?? null,
    ]);
@endphp


  <!-- MY ACTION CENTER -->
  <div class="sec-row">
    <div class="sec-line"></div>
    <div class="sec-ttl"><i class="bi bi-lightning-charge" style="color:var(--gold);margin-right:4px;"></i>My Action Center <span style="opacity:.5;">· Items waiting on you</span></div>
    <div class="sec-line"></div>
  </div>

  <div class="g3" style="margin-bottom:16px;">
    <!-- QC REVIEW QUEUE -->
      <div class="card card-pad">
      <div class="c-hdr">
      <div class="c-label"><i class="bi bi-patch-check"></i>QC Review Queue</div>
      <a href="{{ route('qc_review', $carry) }}" class="c-more">
      Open all <i class="bi bi-arrow-right"></i>
      </a>      </div>
      <div id="qcQueue"></div>
      </div>
      <!-- DISPATCH QUEUE -->
      <div class="card card-pad">
      <div class="c-hdr">
      <div class="c-label"><i class="bi bi-person-gear"></i>Dispatch Queue</div>
      <!-- <div class="c-more" onclick="openPanel('dispatch-queue',null)">Open all <i class="bi bi-arrow-right"></i> -->


      <a href="{{ route('dispatch_engine',$carry) }}" class="c-more">
      Open all <i class="bi bi-arrow-right"></i>
      </a>    


      </div>
      <div id="dispatchQueue"></div>
      </div>
      <!-- INQUIRY TRIAGE -->
      <div class="card card-pad">
      <div class="c-hdr">
      <div class="c-label"><i class="bi bi-clipboard-check"></i>Inquiry Triage</div>
      <!-- <div class="c-more" onclick="openPanel('inquiry-triage',null)">Open all <i class="bi bi-arrow-right"></i></div> -->

      <a href="{{ route('inquiry-approval.index',$carry) }}" class="c-more">
      Open all <i class="bi bi-arrow-right"></i>
      </a>    
      </div>
      <div id="inquiryQueue"></div>
      </div>
      </div>

  <!-- MAINTENANCE LEAD SCORECARD -->
  <!-- <div class="sec-row">
    <div class="sec-line"></div>
    <div class="sec-ttl"><i class="bi bi-diagram-3" style="color:var(--gold);margin-right:4px;"></i>Maintenance Lead Performance <span style="opacity:.5;">· Click a lead for team detail</span></div>
    <div class="sec-line"></div>
  </div> -->

  <div class="card" style="margin-bottom:16px;display:none;">
    <div class="c-hdr" style="padding:14px 18px 14px;">
      <div class="c-label"><i class="bi bi-trophy"></i>Leads Ranked · July 2026</div>
      <div style="display:flex;gap:12px;flex-wrap:wrap;align-items:center;"><div class="c-more" onclick="openPanel('all-leads',null)" style="margin-right:4px;">View all <i class="bi bi-arrow-right"></i></div>
        <div class="leg"><span class="leg-dot" style="background:#9a8053;"></span>Jobs bar</div>
        <div class="leg"><span class="leg-dot" style="background:#15803d;"></span>SLA &ge;95%</div>
        <div class="leg"><span class="leg-dot" style="background:#d97706;"></span>SLA 85&ndash;94%</div>
        <div class="leg"><span class="leg-dot" style="background:#dc2626;"></span>SLA &lt;85%</div>
      </div>
    </div>
    <div style="overflow-x:auto;">
      <table class="t-tbl">
        <thead>
          <tr>
            <th style="padding:9px 16px;">#</th>
            <th>Maintenance Lead</th>
            <th>Team Size</th>
            <th>Jobs Supervised</th>
            <th>Team SLA</th>
            <th>Avg Rating</th>
            <th>Rework</th>
            <th>Avg Response</th>
          </tr>
        </thead>
        <tbody id="leadTbl"></tbody>
      </table>
    </div>
  </div>

  <!-- TECHNICIAN SCORECARD -->
  <div class="sec-row">
    <div class="sec-line"></div>
    <div class="sec-ttl"><i class="bi bi-people" style="color:var(--gold);margin-right:4px;"></i>Technician Performance <span style="opacity:.5;">· Click row for details</span></div>
    <div class="sec-line"></div>
  </div>

  <div class="card" style="margin-bottom:16px;">
    <div class="c-hdr" style="padding:14px 18px 14px;">
      <div class="c-label"><i class="bi bi-tools"></i>Technicians Ranked</div>
      <div style="display:flex;gap:12px;flex-wrap:wrap;align-items:center;"><div class="c-more" onclick="openPanel('all-techs',null)" style="margin-right:4px;">View all <i class="bi bi-arrow-right"></i></div>
        <div class="leg"><span class="leg-dot" style="background:#9a8053;"></span>Jobs bar</div>
        <div class="leg"><span class="leg-dot" style="background:#15803d;"></span>0 rework</div>
        <div class="leg"><span class="leg-dot" style="background:#d97706;"></span>1 rework</div>
        <div class="leg"><span class="leg-dot" style="background:#dc2626;"></span>2+ rework</div>
      </div>
    </div>
    <div style="overflow-x:auto;">
      <table class="t-tbl">
        <thead>
          <tr>
                        <th style="padding-left:18px;">#</th>
                        <th>Technician</th>
                        <th>Jobs completed</th>
                        <th>Field hours</th>
                        <th>Client rating</th>
                        <th>Rework</th>
                        <th>Punch-in rate</th>
                        <th>Field expenses</th>
                    </tr>
        </thead>
        <tbody id="techTbl"></tbody>
      </table>
    </div>
  </div>

  <!-- QUALITY / DISPATCH / WORKFORCE -->
  <div class="sec-row">
    <div class="sec-line"></div>
    <div class="sec-ttl"><i class="bi bi-shield-check" style="color:var(--gold);margin-right:4px;"></i>Quality, Dispatch &amp; Workforce</div>
    <div class="sec-line"></div>
  </div>

  <div class="g3" style="margin-bottom:16px;">

    <!-- QC & QUALITY -->
  <div class="card card-pad">
  <div class="c-hdr">
    <div class="c-label"><i class="bi bi-patch-check"></i>QC &amp; Quality</div>
    <div class="c-more" onclick="openPanel('sla-breach',null)">SLA <i class="bi bi-arrow-right"></i></div>
  </div>

  <div style="display:flex;align-items:center;gap:16px;margin-bottom:14px;flex-wrap:wrap;">
    <div>
      <div class="ch-half" style="width:180px;"><canvas id="slaG"></canvas></div>
      <div style="text-align:center;margin-top:-14px;">
        <div style="font-size:1.6rem;font-weight:700;color:var(--text);line-height:1;">
          {{ $qc['sla_compliance'] !== null ? $qc['sla_compliance'].'%' : '—' }}
        </div>
        <div style="font-size:.7rem;color:var(--muted);">SLA Compliance</div>
      </div>
    </div>

    <div style="flex:1;min-width:120px;">
      <div class="mini-metric" style="margin-bottom:8px;cursor:pointer;" onclick="openPanel('qc-queue',null)">
        <div class="mm-label">QC Reviews Pending</div>
        <div class="mm-val" style="color:#d97706;">{{ $qc['pending_review'] }}</div>
        <div class="mm-sub">Awaiting your sign-off</div>
      </div>

      <div class="mini-metric" style="margin-bottom:8px;">
        <div class="mm-label">First-Pass QC</div>
        <div class="mm-val" style="color:#15803d;">
          {{ $qc['first_pass_rate'] !== null ? $qc['first_pass_rate'].'%' : '—' }}
        </div>
        <div class="mm-sub">{{ $qc['first_pass_sub'] }}</div>
      </div>

      <div class="mini-metric" style="margin-bottom:8px;cursor:pointer;" onclick="openPanel('rework',null)">
        <div class="mm-label">Rework This Period</div>
        <div class="mm-val" style="color:#dc2626;">{{ $qc['rework_count'] }}</div>
        <div class="mm-sub">{{ $qc['rework_sub'] }}</div>
      </div>

      <div class="mini-metric">
        <div class="mm-label">SLA Breaches</div>
        <div class="mm-val" style="color:#dc2626;">{{ $qc['sla_breaches'] }}</div>
        <div class="mm-sub">{{ $qc['sla_breach_sub'] }}</div>
      </div>
    </div>
  </div>
</div>

    <!-- DISPATCH EFFICIENCY -->
    <div class="card card-pad">
      <div class="c-hdr">
        <div class="c-label"><i class="bi bi-send"></i>Dispatch Efficiency</div>
        <div class="c-more" onclick="openPanel('dispatch',null)">View all <i class="bi bi-arrow-right"></i></div>
      </div>
      <div class="eff-grid" style="margin-bottom:14px;">
        <div class="eff-block">
          <div class="eff-lbl">Avg Dispatch Time</div>
          <div class="eff-val" style="color:#9a8053;">42m</div>
          <div class="eff-sub">Inquiry &rarr; assigned</div>
        </div>
        <div class="eff-block">
          <div class="eff-lbl">ETA Accuracy</div>
          <div class="eff-val" style="color:#15803d;">91%</div>
          <div class="eff-sub">Arrived within window</div>
        </div>
        <div class="eff-block">
          <div class="eff-lbl">On-Time Arrival</div>
          <div class="eff-val" style="color:#15803d;">88%</div>
          <div class="eff-sub">39 of 44 dispatches</div>
        </div>
        <div class="eff-block">
          <div class="eff-lbl">Auto-Assigned</div>
          <div class="eff-val" style="color:#9a8053;">64%</div>
          <div class="eff-sub">Engine vs manual</div>
        </div>
      </div>
      <div class="ch-140"><canvas id="dispatchChart"></canvas></div>
    </div>

    <!-- WORKFORCE UTILIZATION -->
    <div class="card card-pad">
    <div class="c-hdr">
        <div class="c-label"><i class="bi bi-people"></i>Workforce Utilization</div>
        <div class="c-more" onclick="openPanel('workforce',null)">View all <i class="bi bi-arrow-right"></i></div>
    </div>

    <div style="display:flex;align-items:center;gap:16px;margin-bottom:16px;">
        <div class="ch-d-sm"><canvas id="workforceDonut"></canvas></div>
        <div style="flex:1;min-width:0;">
            <div style="font-size:1.6rem;font-weight:700;color:#9a8053;line-height:1;">
                {{ $workforce['utilization'] }}%
            </div>
            <div style="font-size:.72rem;color:var(--muted);margin-bottom:10px;">Assigned rate</div>

            <div class="wf-split">
                <div class="wf-cell">
                    <div class="wf-cell-val">{{ $workforce['total'] }}</div>
                    <div class="wf-cell-lbl">Total SRs</div>
                </div>
                <div class="wf-cell">
                    <div class="wf-cell-val" style="color:#15803d;">{{ $workforce['available'] }}</div>
                    <div class="wf-cell-lbl">Unassigned</div>
                </div>
            </div>
        </div>
    </div>

    <div class="wf-sec-lbl">Capacity by Category</div>
    <div id="capacityList"></div>
</div>

  </div>

  <!-- TRADE MIX / SATISFACTION -->
  <div class="sec-row">
    <div class="sec-line"></div>
    <div class="sec-ttl"><i class="bi bi-clipboard-data" style="color:var(--gold);margin-right:4px;"></i>Trade Mix &amp; Client Satisfaction</div>
    <div class="sec-line"></div>
  </div>

  <div class="g3" style="margin-bottom:16px;">

    <!-- JOBS BY TRADE -->
    <div class="card card-pad">
      <div class="c-hdr">
        <div class="c-label"><i class="bi bi-pie-chart"></i>Jobs by Trade</div>
        <div class="c-more" onclick="openPanel('trades',null)">View all <i class="bi bi-arrow-right"></i></div>
      </div>
      <div class="ch-180"><canvas id="tradeChart"></canvas></div>
      <div style="margin-top:12px;display:flex;flex-direction:column;gap:6px;" id="tradeList"></div>
    </div>

    <!-- SLA BY LEAD -->
 <div class="card card-pad">
    <div class="c-hdr">
        <div class="c-label"><i class="bi bi-buildings"></i>Top clients by SR volume</div>
        <!-- <div class="c-more" onclick="openPanel('clients',null)">All clients <i class="bi bi-arrow-right"></i></div> -->
    </div>

    @if (count($clients))
        <div class="ch-180"><canvas id="mmClientChart"></canvas></div>
        <div class="leg-row">
            <span class="leg"><span class="leg-dot" style="background:#9a8053;"></span>In-warranty</span>
            <span class="leg"><span class="leg-dot" style="background:#39383788;"></span>Out-of-warranty</span>
        </div>
    @endif

    <div class="client-list">
        @forelse ($clients as $client)
            <button type="button" class="lrow"
                    onclick="openPanel('clients',null)">
                <span class="lrow-l">
                    <span class="rank">{{ $loop->iteration }}</span>
                    <span class="lrow-name">{{ data_get($client, 'n') }}</span>
                </span>
                <span class="lrow-r">
                    <strong>{{ data_get($client, 'srs') }}</strong>
                    <span class="lrow-unit">SRs</span>
                </span>
            </button>
        @empty
            <p class="empty"><i class="bi bi-building"></i>No client activity in this period.</p>
        @endforelse
    </div>
</div>



    <!-- SATISFACTION -->
    <div class="card card-pad">
  <div class="c-hdr">
    <div class="c-label"><i class="bi bi-star-half"></i>Client Satisfaction</div>
    <div class="c-more" onclick="openPanel('feedback',null)">View all <i class="bi bi-arrow-right"></i></div>
  </div>

  <div style="display:flex;align-items:center;gap:14px;margin-bottom:16px;
    padding-bottom:14px;border-bottom:1px solid var(--border);">
    <div>
      <div style="font-size:3rem;font-weight:700;color:#9a8053;line-height:1;">
        {{ $satisfaction2['avg_display'] }}
      </div>
      <div style="display:flex;gap:2px;color:#f59e0b;font-size:.85rem;margin-bottom:3px;">
        @for ($i = 0; $i < $satisfaction2['stars']['full']; $i++)<i class="bi bi-star-fill"></i>@endfor
        @if ($satisfaction2['stars']['half'])<i class="bi bi-star-half"></i>@endif
        @for ($i = 0; $i < $satisfaction2['stars']['empty']; $i++)<i class="bi bi-star"></i>@endfor
      </div>
      <div style="font-size:.7rem;color:var(--muted);">
        {{ $satisfaction2['responses'] }} responses · {{ $satisfaction2['response_rate'] }}% rate
      </div>
    </div>
    <div style="flex:1;">
      <div class="mini-metric" style="margin-bottom:6px;">
        <div class="mm-label">Completed SRs</div>
        <div class="mm-val">{{ $satisfaction2['completed'] }}</div>
      </div>
      <div class="mini-metric">
        <div class="mm-label">Feedback Submitted</div>
        <div class="mm-val" style="color:#9a8053;">{{ $satisfaction2['responses'] }}</div>
      </div>
    </div>
  </div>

  <div id="ratingHist"></div>

  @if ($satisfaction2['flagged'])
  <div style="margin-top:12px;padding:9px 12px;background:rgba(217,119,6,.07);
    border:1px solid rgba(217,119,6,.15);border-radius:8px;
    font-size:.74rem;color:#d97706;display:flex;align-items:center;gap:6px;cursor:pointer;"
    onclick="openPanel('qc-queue',{{ $satisfaction2['flagged']['id'] }})">
    <i class="bi bi-exclamation-triangle-fill"></i>
    {{ $satisfaction2['flagged']['code'] }} rated {{ $satisfaction2['flagged']['score'] }}★ — flagged for QC review
  </div>
  @endif
</div>
  </div>



<!-- SLIDE-IN PANEL -->
<div class="panel-overlay" onclick="closePanel()"></div>
<div class="detail-panel" id="detailPanel">
  <div class="dp-hdr">
    <div class="dp-hdr-left">
      <div class="dp-hdr-icon" id="dpIcon"><i class="bi bi-list"></i></div>
      <div>
        <div class="panel-heading" id="dpTitle">Details</div>
        <div class="dp-hdr-sub" id="dpSub"></div>
      </div>
    </div>
    <button class="dp-close" onclick="closePanel()"><i class="bi bi-x-lg"></i></button>
  </div>
  <div class="dp-body" id="dpBody"></div>
</div>

<div id="tw"></div>
@endsection

@push('scripts')
<script>
/* ============ DATA ============ */

var KPI = [
  {ico:'bi-diagram-3', cls:'i1 k1', val:{{ $activeCount }}, lbl:'Active SRs Under Mgmt',
   delta:'Live', dc:'dn', sub:'', sp:[8,11,9,14,10,13,12], panel:'active-srs'},

  {ico:'bi-patch-check', cls:'i2 k2', val:{{ $qcPendingCount }}, lbl:'QC Reviews Pending',
   delta:'Your queue', dc:'dd', sub:'', sp:[2,4,3,5,4,2,3], panel:'qc-queue'},

  {ico:'bi-person-gear', cls:'i3 k3', val:{{ $dispatchCount }}, lbl:'Awaiting Dispatch',
   delta:'Action', dc:'dd', sub:'', sp:[3,2,4,1,3,2,2], panel:'dispatch-queue'},

  {ico:'bi-speedometer2', cls:'i4 k4', val:'84%', lbl:'SLA Compliance',
   delta:'+3%', dc:'du', sub:'3 breaches this month', sp:[78,80,82,79,84,81,84], panel:'sla-breach'},

  {ico:'bi-check2-circle', cls:'i5 k5', val:{{$reworkCount}}, lbl:'SR Rework',
   delta:'Rework', dc:'du', sub:'', sp:[70,72,74,73,76,75,78], panel:'rework'},
];

// var SR_LABELS=['Feb','Mar','Apr','May','Jun','Jul'];
// var SR_DONE=[30,34,33,38,40,43];
// var SR_INQ=[32,36,38,35,42,47];


var SR_LABELS = @json($srTrend2['labels']);
var SR_DONE   = @json($srTrend2['done']);
var SR_INQ    = @json($srTrend2['inquiries']);

var STATUS_DATA = @json($statusBreakdown);
/* MAINTENANCE LEADS — each supervises a technician team */
var LEADS=[
  {i:'RH',n:'Rashid Al-Habsi', zone:'Electrical · Dubai',    team:5,jobs:34,sla:92,r:4.7,rw:2,resp:'38m'},
  {i:'YR',n:'Yousuf Rahman',   zone:'HVAC · Dubai',          team:4,jobs:21,sla:95,r:4.6,rw:1,resp:'41m'},
  {i:'SN',n:'Salim Nasser',    zone:'Mechanical · Abu Dhabi',team:4,jobs:26,sla:88,r:4.5,rw:3,resp:'52m'},
  {i:'NF',n:'Nadia Faris',     zone:'Multi-trade · Northern',team:4,jobs:18,sla:96,r:4.8,rw:0,resp:'35m'},
  {i:'KA',n:'Kareem Adel',     zone:'Plumbing · Sharjah',    team:3,jobs:15,sla:84,r:4.2,rw:4,resp:'58m'},
];

/* TECHNICIANS — each reports to a lead (leadIdx) */
var TECHS = @json($technicians);

/* techs per lead index */
var LEAD_TECHS={};
TECHS.forEach(function(t){ (LEAD_TECHS[t.leadIdx]=LEAD_TECHS[t.leadIdx]||[]).push(t); });

// var TRADES=[
//   {n:'Electrical',v:16,c:'#9a8053'},
//   {n:'Mechanical',v:12,c:'#393837'},
//   {n:'HVAC',v:9,c:'#b8975e'},
//   {n:'Plumbing',v:6,c:'#64748b'},
// ];


var CAPACITY = @json($workforce['capacity']);
var TRADES   = @json($workforce['trades']);
var WF       = @json($workforce);





var RATINGS    = @json($ratingBuckets2['rows']);
var RATING_MAX = {{ $ratingBuckets2['max'] }};

/* Job/SR lists per technician (for tech panel) */
var TECH_JOBS={
  0:[
    {id:'SR-2025-0041',client:'Al Futtaim Group',status:'In Progress',bc:'#0891b2',meta:'Electrical · Dubai Mall G12 · On-site now'},
    {id:'SR-2025-0036',client:'Emaar Properties',status:'Completed',bc:'#15803d',meta:'Electrical · Downtown · 5★ · On time'},
    {id:'SR-2025-0031',client:'ADNOC Distribution',status:'Completed',bc:'#15803d',meta:'Electrical · Al Quoz · Completed 3.5h'},
  ],
  1:[
    {id:'SR-2025-0038',client:'ADNOC Distribution',status:'In Progress',bc:'#0891b2',meta:'Mechanical · Al Quoz Depot · On-site 4h'},
    {id:'SR-2025-0034',client:'Emaar Properties',status:'Completed',bc:'#15803d',meta:'Plumbing · Downtown · Rated 5★'},
    {id:'SR-2025-0028',client:'DP World',status:'Completed',bc:'#15803d',meta:'Mechanical · Jebel Ali · On time'},
  ],
  2:[
    {id:'SR-2025-0043',client:'Emaar Properties',status:'In Progress',bc:'#0891b2',meta:'Electrical · Downtown Tower 3 · On-site 1h'},
    {id:'SR-2025-0033',client:'Emaar Properties',status:'Rework',bc:'#dc2626',meta:'Electrical · QC failed · Rework in progress'},
    {id:'SR-2025-0027',client:'ADNOC Distribution',status:'Completed',bc:'#15803d',meta:'Electrical · Ruwais · Rated 3★'},
  ],
  3:[
    {id:'SR-2025-0051',client:'DP World',status:'ETA Confirmed',bc:'#7c3aed',meta:'HVAC · Jebel Ali T1 · ETA 4:30 PM'},
    {id:'SR-2025-0042',client:'Emirates NBD',status:'Completed',bc:'#15803d',meta:'HVAC · DIFC Branch · Rated 5★'},
    {id:'SR-2025-0037',client:'Emaar Properties',status:'Completed',bc:'#15803d',meta:'HVAC · Downtown · Completed 3.8h'},
  ],
  4:[
    {id:'SR-2025-0049',client:'Emirates NBD',status:'Assigned',bc:'#2563eb',meta:'Plumbing · DIFC Branch B · ETA 3:00 PM'},
    {id:'SR-2025-0040',client:'Al Futtaim Group',status:'Completed',bc:'#15803d',meta:'Plumbing · Dubai Mall · Rated 4★'},
    {id:'SR-2025-0035',client:'ADNOC Distribution',status:'Completed',bc:'#15803d',meta:'Plumbing · Al Quoz · SLA met'},
  ],
  5:[
    {id:'SR-2025-0046',client:'Dubai Airports',status:'Completed',bc:'#15803d',meta:'Mechanical · T1 Concourse · Rated 5★'},
    {id:'SR-2025-0039',client:'ADNOC Distribution',status:'Completed',bc:'#15803d',meta:'Mechanical · Al Quoz · On time'},
    {id:'SR-2025-0032',client:'Emaar Properties',status:'Completed',bc:'#15803d',meta:'Mechanical · Downtown · Rated 5★'},
  ],
};

/* ACTION-CENTER queues */
var QC_QUEUE = @json(array_slice($qcItems, 0, 4));
var DISPATCH_QUEUE = @json(array_slice($dispatchItems, 0, 4));

var INQUIRY_QUEUE = @json(array_slice($inquiryItems, 0, 4));

/* PANEL DATA */
var CLIENTS = @json($clients);

  var PANEL_DATA = {
  'active-srs': {
    title: 'Active Open SRs',
    icon:  'bi-activity',
    sub:   '{{ $activeCount }} SRs currently open across all active stages',
    items: @json($activeItems)
  },
 'qc-queue': {
  title: 'QC Review Queue',
  icon:  'bi-patch-check',
  sub:   '{{ $qcPendingCount }} SRs awaiting your quality sign-off after field work',
  items: @json($qcItems)
},
'dispatch-queue': {
  title: 'Dispatch Queue',
  icon:  'bi-person-gear',
  sub:   '{{ $dispatchCount }} approved SRs awaiting technician assignment',
  items: @json($dispatchItems)
},

'rework': {
  title: 'Rework Queue',
  icon:  'bi-arrow-repeat',
  sub:   '{{ $reworkCount }} SRs sent back after QC rejection',
  items: @json($reworkItems)
},

'all-srs': {
  title: 'All Service Requests',
  icon:  'bi-bar-chart-steps',
  sub:   '{{ $allCount }} SRs across every stage',
  items: @json($allItems)
},

  'inquiry-triage': {
  title: 'Inquiry Triage',
  icon:  'bi-clipboard-check',
  sub:   '{{ $inquiryCount }} new inquiries awaiting review',
  items: @json($inquiryItems)
},
  'sla-breach': {
  title: 'SLA Breach Report',
  icon:  'bi-exclamation-triangle-fill',
  sub:   '{{ $slaBreachCount }} {{ Str::plural('SR', $slaBreachCount) }} breached SLA this period',
  items: @json($slaBreachItems)
},
};

/* dynamically-built list panels for View-all buttons */
PANEL_DATA['all-leads']={
  title:'All Maintenance Leads',icon:'bi-diagram-3',
  sub:LEADS.length+' leads · ranked by jobs supervised this month',
  items:LEADS.map(function(l){
    return {id:l.i+' · '+l.n,client:l.zone,
      badge:l.sla+'% SLA',bc:(l.sla>=95?'#15803d':l.sla>=85?'#9a8053':'#dc2626'),
      meta:l.team+' technicians · '+l.jobs+' jobs · '+l.r+'\u2605 avg rating · '+l.rw+' rework · '+l.resp+' avg response'};
  })
};
PANEL_DATA['all-techs']={
  title:'All Technicians',icon:'bi-people',
  sub:TECHS.length+' technicians · ranked by jobs completed',
  items:TECHS.map(function(t){
    return {id:t.i+' · '+t.n,client:t.d+' · reports to '+t.lead,
      badge:t.rw+' rework',bc:(t.rw===0?'#15803d':t.rw<=1?'#d97706':'#dc2626'),
      meta:t.j+' jobs · '+t.h+'h field · '+t.r+'\u2605 rating · '+t.p+'% punch-in rate'};
  })
};
PANEL_DATA['trades']={
  title:'Jobs by Trade',icon:'bi-pie-chart',
  sub:'47 jobs this month across service trades',
  items:(function(){var tot=TRADES.reduce(function(a,b){return a+b.v;},0);
    return TRADES.map(function(t){
      return {id:t.n,client:Math.round(t.v/tot*100)+'% of all jobs',
        badge:t.v+' jobs',bc:t.c,
        meta:'Field trade volume for July 2026 · '+t.v+' service requests handled'};
    });})()
};

PANEL_DATA['workforce'] = {
  title: 'SR Assignment Capacity',
  icon:  'bi-people',
  sub:   WF.total + ' SRs · ' + WF.taken + ' assigned · ' + WF.available + ' unassigned',
  items: CAPACITY.map(function(c){
    return {
      id:     c.t,
      client: c.avail + ' of ' + c.total + ' unassigned',
      badge:  (c.avail === 0 ? 'All assigned' : c.avail <= 1 ? 'Tight' : 'Open'),
      bc:     (c.avail === 0 ? '#dc2626' : c.avail <= 1 ? '#d97706' : '#15803d'),
      meta:   c.total + ' ' + c.t.toLowerCase() + ' SRs in this period · '
              + c.taken + ' assigned to a technician'
    };
  })
};

PANEL_DATA['dispatch']={
  title:'Dispatch Efficiency',icon:'bi-send',
  sub:'Field dispatch performance · July 2026',
  items:[
    {id:'Avg Dispatch Time',client:'42 minutes',badge:'On target',bc:'#9a8053',meta:'Median time from inquiry approval to technician assignment'},
    {id:'ETA Accuracy',client:'91%',badge:'Good',bc:'#15803d',meta:'Share of jobs where the technician arrived within the committed ETA window'},
    {id:'On-Time Arrival',client:'88% · 39 of 44',badge:'Good',bc:'#15803d',meta:'Dispatches where the technician reached site on schedule'},
    {id:'Auto-Assigned',client:'64%',badge:'Engine',bc:'#9a8053',meta:'Jobs routed automatically by the dispatch engine vs manual assignment'},
    {id:'Reassignments',client:'6 this month',badge:'Watch',bc:'#d97706',meta:'Dispatches changed after initial assignment (availability / skills mismatch)'}
  ]
};

PANEL_DATA['feedback'] = @json($feedbackPanel);

/* ============ CHARTS ============ */
var CHARTS={};
function gc(){
  var dk=document.documentElement.getAttribute('data-bs-theme')==='dark';
  return {gold:'#9a8053',grid:dk?'rgba(255,255,255,.06)':'rgba(0,0,0,.05)',
    text:dk?'#e8e0d4':'#1a1614',muted:dk?'#7a756e':'#8a8480'};
}
var TT={backgroundColor:'rgba(15,15,15,.9)',cornerRadius:8,padding:10,titleColor:'#fff',bodyColor:'rgba(255,255,255,.75)'};

function buildCharts(){
  var C=gc();

  /* Throughput trend */
  var c1=document.getElementById('srTrend');
  if(c1){
    var ctx=c1.getContext('2d');
    var g1=ctx.createLinearGradient(0,0,0,220);g1.addColorStop(0,'rgba(154,128,83,.35)');g1.addColorStop(1,'rgba(154,128,83,0)');
    var g2=ctx.createLinearGradient(0,0,0,220);g2.addColorStop(0,'rgba(57,56,55,.3)');g2.addColorStop(1,'rgba(57,56,55,0)');
    CHARTS.sr=new Chart(c1,{type:'line',data:{labels:SR_LABELS,datasets:[
      {label:'Completed',data:SR_DONE,borderColor:'#9a8053',borderWidth:2.5,backgroundColor:g1,fill:true,tension:.4,pointRadius:4,pointBackgroundColor:'#9a8053',pointHoverRadius:6},
      {label:'New Inquiries',data:SR_INQ,borderColor:'#393837',borderWidth:2.5,backgroundColor:g2,fill:true,tension:.4,pointRadius:4,pointBackgroundColor:'#393837',pointHoverRadius:6},
    ]},options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false},tooltip:TT},
      scales:{x:{grid:{color:C.grid},ticks:{color:C.muted,font:{size:11}}},y:{grid:{color:C.grid},ticks:{color:C.muted,font:{size:11}},beginAtZero:true}}}});
  }

  /* Dispatch source bar */
  var c2=document.getElementById('dispatchChart');
  if(c2){
    CHARTS.disp=new Chart(c2,{type:'bar',data:{labels:['Feb','Mar','Apr','May','Jun','Jul'],datasets:[
      {label:'Auto',data:[18,20,22,24,26,28],backgroundColor:'rgba(154,128,83,.8)',borderRadius:5,borderSkipped:false},
      {label:'Manual',data:[14,16,15,14,16,16],backgroundColor:'rgba(57,56,55,.4)',borderRadius:5,borderSkipped:false},
    ]},options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false},tooltip:TT},
      scales:{x:{stacked:true,grid:{display:false},ticks:{color:C.muted,font:{size:10}}},y:{stacked:true,grid:{color:C.grid},ticks:{color:C.muted,font:{size:10}}}}}});
  }

  /* Workforce donut */
  var c3 = document.getElementById('workforceDonut');
if (c3 && CAPACITY.length) {
  CHARTS.wf = new Chart(c3, {
    type: 'doughnut',
    data: {
      labels: CAPACITY.map(function(c){ return c.t; }),
      datasets: [{
        data: CAPACITY.map(function(c){ return c.total; }),
        backgroundColor: CAPACITY.map(function(c){ return c.c; }),
        borderColor: 'rgba(0,0,0,0)',
        borderWidth: 2,
        hoverOffset: 5
      }]
    },
    options: {
      responsive: true, maintainAspectRatio: false, cutout: '72%',
      plugins: {
        legend: { display: false },
        tooltip: Object.assign({}, TT, {
          callbacks: {
            label: function(ctx){
              var c = CAPACITY[ctx.dataIndex];
              return ' ' + c.total + ' SRs · ' + c.avail + ' free';
            }
          }
        })
      }
    }
  });
}

  /* SLA gauge */
  var c4=document.getElementById('slaG');
  if(c4){
    CHARTS.sla=new Chart(c4,{type:'doughnut',data:{labels:['Met','Breached'],
      datasets:[{data:[84,16],backgroundColor:['#9a8053','rgba(220,38,38,.2)'],borderColor:['#9a8053','rgba(220,38,38,.3)'],borderWidth:2,hoverOffset:0}]},
      options:{responsive:true,maintainAspectRatio:false,cutout:'78%',rotation:-90,circumference:180,plugins:{legend:{display:false},tooltip:{enabled:false}}}});
  }

  /* Jobs by trade donut */
  var c5=document.getElementById('tradeChart');
  if(c5){
    CHARTS.trade=new Chart(c5,{type:'doughnut',data:{labels:TRADES.map(function(t){return t.n;}),
      datasets:[{data:TRADES.map(function(t){return t.v;}),backgroundColor:TRADES.map(function(t){return t.c;}),borderColor:'rgba(0,0,0,0)',borderWidth:2,hoverOffset:5}]},
      options:{responsive:true,maintainAspectRatio:false,cutout:'62%',plugins:{legend:{display:false},tooltip:TT}}});
  }

  /* Team SLA by lead */
  var c6=document.getElementById('leadSlaChart');
  if(c6){
    CHARTS.leadSla=new Chart(c6,{type:'bar',data:{labels:LEADS.map(function(l){return l.i;}),
      datasets:[{label:'SLA',data:LEADS.map(function(l){return l.sla;}),
        backgroundColor:LEADS.map(function(l){return l.sla>=95?'#15803d':l.sla>=85?'rgba(154,128,83,.85)':'#dc2626';}),borderRadius:5,borderSkipped:false}]},
      options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false},
        tooltip:{backgroundColor:'rgba(15,15,15,.9)',cornerRadius:8,padding:10,callbacks:{label:function(x){return ' '+x.parsed.y+'% SLA';}}}},
        scales:{x:{grid:{display:false},ticks:{color:C.muted,font:{size:11}}},y:{grid:{color:C.grid},ticks:{color:C.muted,font:{size:10},callback:function(v){return v+'%';}},suggestedMin:70,suggestedMax:100}}}});
  }


  var c7 = document.getElementById('mmClientChart');
if (c7 && CLIENTS.length) {
  CHARTS.mmClient = new Chart(c7, {
    type:'bar',
    data:{
      labels: CLIENTS.map(function(c){ return c.n.split(' ')[0]; }),
      datasets:[
        {label:'In-warranty', data:CLIENTS.map(function(c){return c.iw;}),
         backgroundColor:'rgba(154,128,83,.75)', borderRadius:4, borderSkipped:false},
        {label:'Out-of-warranty', data:CLIENTS.map(function(c){return c.oow;}),
         backgroundColor:'rgba(57,56,55,.45)', borderRadius:4, borderSkipped:false},
      ]
    },
    options:{
      responsive:true, maintainAspectRatio:false, indexAxis:'y',
      plugins:{legend:{display:false}, tooltip:TT},
      scales:{
        x:{stacked:true, grid:{color:C.grid}, ticks:{color:C.muted, font:{size:10}, precision:0}},
        y:{stacked:true, grid:{display:false}, ticks:{color:C.text, font:{size:10}}}
      }
    }
  });
}

  buildSparks();
}

function buildSparks(){
  KPI.forEach(function(k,i){
    var c=document.getElementById('sp'+i);if(!c)return;
    var col=['#9a8053','#2563eb','#15803d','#d97706','#7c3aed'][i];
    var g=c.getContext('2d').createLinearGradient(0,0,0,38);g.addColorStop(0,col+'55');g.addColorStop(1,col+'00');
    CHARTS['sp'+i]=new Chart(c,{type:'line',data:{labels:k.sp.map(function(){return '';}),
      datasets:[{data:k.sp,borderColor:col,borderWidth:2,pointRadius:0,fill:true,backgroundColor:g,tension:.4}]},
      options:{responsive:false,maintainAspectRatio:false,plugins:{legend:{display:false},tooltip:{enabled:false}},scales:{x:{display:false},y:{display:false}}}});
  });
}

function refreshCharts(){
  var C=gc();
  Object.values(CHARTS).forEach(function(ch){
    if(!ch||!ch.options)return;
    ['x','y'].forEach(function(ax){
      if(ch.options.scales&&ch.options.scales[ax]){
        if(ch.options.scales[ax].grid)ch.options.scales[ax].grid.color=C.grid;
        if(ch.options.scales[ax].ticks)ch.options.scales[ax].ticks.color=C.muted;
      }
    });
    ch.update('none');
  });
}

/* ============ RENDER ============ */
function renderKPICards(){
  var row=document.getElementById('kpiRow');if(!row)return;row.innerHTML='';
  KPI.forEach(function(k,i){
    var el=document.createElement('div');el.className='kpi '+k.cls;
    el.onclick=function(){openPanel(k.panel,null);};
    el.innerHTML='<div class="kpi-row-top"><div class="kpi-ico '+k.cls.split(' ')[0]+'"><i class="bi '+k.ico+'"></i></div>'+
      '<span class="kpi-delta '+k.dc+'">'+k.delta+'</span></div>'+
      '<div class="big-num">'+k.val+'</div><div class="kpi-lbl">'+k.lbl+'</div>'+
      '<div style="font-size:.69rem;color:var(--light);">'+k.sub+'</div>'+
      '<div class="sp-wrap"><canvas id="sp'+i+'"></canvas></div>';
    row.appendChild(el);
  });
}

function renderStatusDist(){
  var box = document.getElementById('statusDist');
  if(!box || !STATUS_DATA.length) return;

  box.innerHTML = STATUS_DATA.map(function(s){
    return '<div class="sd-row" style="opacity:'+(s.n ? 1 : .38)+'">'+
      '<div class="sd-label">'+s.lbl+'</div>'+
      '<div class="sd-track"><div class="sd-fill" data-w="'+s.w+'%" style="width:0;background:'+s.c+'"></div></div>'+
      '<div class="sd-n">'+s.n+'</div></div>';
  }).join('');

  setTimeout(function(){
    box.querySelectorAll('.sd-fill').forEach(function(el){
      el.style.transition = 'width 1s cubic-bezier(.4,0,.2,1)';
      el.style.width = el.dataset.w;
    });
  }, 400);
}

function queueHTML(list,type){
  return list.map(function(q){
    return '<div class="q-item" onclick="openPanel(\''+type+'\',null)"><div class="q-top">'+
      '<span class="q-id">'+q.id+'</span>'+
      '<span class="q-badge" style="background:'+q.bc+'22;color:'+q.bc+';">'+q.badge+'</span></div>'+
      '<div class="q-client">'+q.client+'</div><div class="q-meta">'+q.meta+'</div></div>';
  }).join('');
}
function renderQueues(){
  var map = {qcQueue:[QC_QUEUE,'qc-queue'],
             dispatchQueue:[DISPATCH_QUEUE,'dispatch-queue'],
             inquiryQueue:[INQUIRY_QUEUE,'inquiry-triage']};

  Object.keys(map).forEach(function(elId){
    var box = document.getElementById(elId); if(!box) return;
    var list = map[elId][0];
    box.innerHTML = list.length
      ? queueHTML(list, map[elId][1])
      : '<div style="font-size:.75rem;color:var(--light);padding:6px 0;">Nothing pending</div>';
  });
}

function renderLeadTable(){
  var maxJ=LEADS[0].jobs;
  document.getElementById('leadTbl').innerHTML=LEADS.map(function(l,i){
    var bw=Math.round((l.jobs/maxJ)*100);
    var slaC=l.sla>=95?'#15803d':l.sla>=85?'#d97706':'#dc2626';
    var rc=l.rw===0?'p0':l.rw<=2?'p1':'p2';
    var med=['🥇','🥈','🥉','','',''][i];
    var st='';for(var s=1;s<=5;s++)st+='<i class="bi bi-star'+(s<=Math.round(l.r)?'-fill':'')+'"></i>';
    return '<tr data-li="'+i+'" style="cursor:pointer;">'+
      '<td style="color:var(--light);font-size:.78rem;">'+med+(i>2?i+1:'')+'</td>'+
      '<td><div style="display:flex;align-items:center;gap:9px;"><div class="t-av">'+l.i+'</div>'+
        '<div><div class="t-name">'+l.n+'</div><div class="t-domain"><span class="zone-tag">'+l.zone+'</span></div></div></div></td>'+
      '<td style="font-weight:600;color:var(--text);">'+l.team+' techs</td>'+
      '<td><div class="t-bar-wrap"><div class="t-bar-fill" data-w="'+bw+'%" style="width:0"></div></div>'+
        '<div style="font-size:.69rem;color:var(--muted);margin-top:2px;">'+l.jobs+' jobs</div></td>'+
      '<td style="font-weight:600;color:'+slaC+';">'+l.sla+'%</td>'+
      '<td><div class="t-stars">'+st+'</div><div style="font-size:.69rem;color:var(--muted);">'+l.r+'</div></td>'+
      '<td><span class="t-pill '+rc+'"><i class="bi bi-arrow-counterclockwise"></i>'+l.rw+'</span></td>'+
      '<td style="font-size:.8rem;color:var(--muted);">'+l.resp+'</td></tr>';
  }).join('');
  setTimeout(function(){document.querySelectorAll('#leadTbl .t-bar-fill').forEach(function(el){el.style.transition='width 1s cubic-bezier(.4,0,.2,1)';el.style.width=el.dataset.w;});},500);
  Array.from(document.querySelectorAll('#leadTbl tr')).forEach(function(tr){
    tr.addEventListener('click',function(){openPanel('lead',parseInt(this.dataset.li));});
  });
}



function setRange(el, val){
  var f = el.form;
  document.getElementById('rangeField').value = val;
  var from = f.querySelector('[name=from]');
  var to   = f.querySelector('[name=to]');
  if (from) from.value = '';
  if (to)   to.value   = '';
  f.submit();
}

function applyCustomRange(el){
  var f    = el.form;
  var from = f.querySelector('[name=from]').value;
  var to   = f.querySelector('[name=to]').value;
  if (!from || !to) return;

  if (from > to) {                                  // user picked them backwards
    f.querySelector('[name=from]').value = to;
    f.querySelector('[name=to]').value   = from;
  }

  document.getElementById('rangeField').value = 'custom';
  f.submit();
}

function renderTechTable(){
  var tbody = document.getElementById('techTbl');
  if(!tbody) return;

  if(!TECHS.length){
    tbody.innerHTML = '<tr><td colspan="8"><p class="empty" style=">'+
      '<i class="bi bi-person-badge"></i>No technician activity in this period.</p></td></tr>';
    return;
  }

  var maxJ = Math.max(1, ...TECHS.map(function(t){ return t.jobs || 0; }));

  tbody.innerHTML = TECHS.map(function(t,i){
    var jobs = t.jobs || 0, rw = t.rework || 0,
        rate = t.rating || 0, pr = t.punch_rate || 0;

    var bw = Math.round(jobs / maxJ * 100);
    var rc = rw === 0 ? 'p0' : rw <= 1 ? 'p1' : 'p2';

    var st = '';
    for(var s = 1; s <= 5; s++){
      st += '<i class="bi bi-star'+(s <= Math.round(rate) ? '-fill' : '')+'"></i>';
    }

    return '<tr data-ti="'+i+'" style="cursor:pointer;">'+
      '<td style="color:var(--light);font-size:.78rem;">'+(i+1)+'</td>'+
      '<td><div style="display:flex;align-items:center;gap:9px;"><div class="t-av">'+(t.initials||'')+'</div>'+
        '<div><div class="t-name">'+(t.name||'—')+'</div>'+
        '<div class="t-domain">'+(t.department||'')+'</div></div></div></td>'+
      '<td><div class="t-bar-wrap"><div class="t-bar-fill" data-w="'+bw+'%" style="width:0"></div></div>'+
        '<div style="font-size:.69rem;color:var(--muted);margin-top:2px;">'+jobs+' jobs</div></td>'+
      '<td style="font-weight:600;color:var(--text);">'+(t.hours||0)+'h</td>'+
      '<td><div class="t-stars">'+st+'</div>'+
        '<div style="font-size:.69rem;color:var(--muted);">'+rate.toFixed(1)+'</div></td>'+
      '<td><span class="t-pill '+rc+'"><i class="bi bi-arrow-counterclockwise"></i>'+rw+'</span></td>'+
      '<td style="font-size:.8rem;font-weight:600;color:'+(pr >= 95 ? '#15803d' : '#d97706')+';">'+
        Math.round(pr)+'%</td>'+
      '<td style="font-size:.78rem;color:var(--muted);">'+(t.expenses_formatted||'—')+'</td></tr>';
  }).join('');

  setTimeout(function(){
    tbody.querySelectorAll('.t-bar-fill').forEach(function(el){
      el.style.transition = 'width 1s cubic-bezier(.4,0,.2,1)';
      el.style.width = el.dataset.w;
    });
  }, 600);

  tbody.querySelectorAll('tr[data-ti]').forEach(function(tr){
    tr.addEventListener('click', function(){ openPanel('tech', parseInt(this.dataset.ti)); });
  });
}

function renderTradeList(){
  var tot=TRADES.reduce(function(a,b){return a+b.v;},0);
  document.getElementById('tradeList').innerHTML=TRADES.map(function(t){
    return '<div style="display:flex;align-items:center;justify-content:space-between;font-size:.75rem;">'+
      '<div style="display:flex;align-items:center;gap:7px;"><span class="leg-dot" style="background:'+t.c+';"></span>'+
      '<span style="color:var(--text);">'+t.n+'</span></div>'+
      '<div><span style="font-weight:700;color:var(--text);">'+t.v+'</span> <span style="color:var(--muted);">('+Math.round(t.v/tot*100)+'%)</span></div></div>';
  }).join('');
}

function renderCapacity(){
  var box = document.getElementById('capacityList');
  if (!box) return;

  if (!CAPACITY.length) {
    box.innerHTML = '<p class="empty" style="padding:14px 0;">No SRs in this period.</p>';
    return;
  }

  box.innerHTML = CAPACITY.map(function(c){
    var dot = c.c || '#9a8053';
    var num = c.avail === 0 ? '#dc2626' : c.avail <= 1 ? '#d97706' : '#15803d';

    return '<div style="display:flex;align-items:center;justify-content:space-between;'
      +   'padding:8px 0;border-bottom:1px solid var(--border);font-size:.76rem;">'
      + '<span style="display:flex;align-items:center;gap:7px;color:var(--muted);min-width:0;">'
      +   '<span style="width:9px;height:9px;border-radius:50%;background:' + dot + ';'
      +     'flex-shrink:0;display:inline-block;"></span>'
      +   '<span style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">' + c.t + '</span>'
      + '</span>'
      + '<span style="color:var(--text);font-weight:600;white-space:nowrap;flex-shrink:0;">'
      +   '<span style="color:' + num + ';">' + c.avail + '</span> / ' + c.total + ' free'
      + '</span>'
      + '</div>';
  }).join('');

  // remove the border from the last row
  var rows = box.children;
  if (rows.length) rows[rows.length - 1].style.borderBottom = 'none';
}
console.log(CAPACITY);
function renderRatings(){
  var max = RATING_MAX || 1;
  document.getElementById('ratingHist').innerHTML = RATINGS.map(function(r){
    var w = Math.round((r.c / max) * 100);
    return '<div class="h-row"><div class="h-lbl">' + r.s + '</div>' +
      '<div class="h-track"><div class="h-fill" data-w="' + w + '%" style="width:0;"></div></div>' +
      '<div class="h-n">' + r.c + '</div></div>';
  }).join('');
  setTimeout(function(){
    document.querySelectorAll('.h-fill').forEach(function(el){
      el.style.transition = 'width 1s cubic-bezier(.4,0,.2,1)';
      el.style.width = el.dataset.w;
    });
  }, 800);
}
renderRatings();

/* ============ SLIDE-IN PANEL ============ */
function prCard(id,badge,bc,client,meta){
  return '<div class="pr"><div class="pr-top"><span class="pr-id">'+id+'</span>'+
    '<span class="pr-badge" style="background:'+bc+'22;color:'+bc+';">'+badge+'</span></div>'+
    (client?'<div class="pr-client">'+client+'</div>':'')+
    '<div class="pr-meta"><i class="bi bi-geo-alt"></i>'+meta+'</div></div>';
}

function openPanel(type,id){
  var heading='',icon='bi-list',sub='',body='';

  if(type==='lead'){
    var l=LEADS[id];
    heading=l.n;icon='bi-diagram-3';sub=l.zone+' · '+l.team+' technicians';
    var slaC=l.sla>=95?'#15803d':l.sla>=85?'#d97706':'#dc2626';
    var st='';for(var s=1;s<=5;s++)st+='<i class="bi bi-star'+(s<=Math.round(l.r)?'-fill':'')+'" style="color:#f59e0b;font-size:.8rem;"></i>';
    body='<div class="dp-sec">Team Summary</div>'+
      '<div style="display:grid;grid-template-columns:1fr 1fr;gap:9px;margin-bottom:14px;">'+
        '<div class="mini-metric"><div class="mm-label">Team Size</div><div class="mm-val">'+l.team+'</div><div class="mm-sub">Technicians</div></div>'+
        '<div class="mini-metric"><div class="mm-label">Jobs Supervised</div><div class="mm-val">'+l.jobs+'</div><div class="mm-sub">This month</div></div>'+
        '<div class="mini-metric"><div class="mm-label">Team SLA</div><div class="mm-val" style="color:'+slaC+';">'+l.sla+'%</div><div class="mm-sub">'+(l.sla>=90?'On target':'Below target')+'</div></div>'+
        '<div class="mini-metric"><div class="mm-label">Avg Rating</div><div class="mm-val" style="color:#f59e0b;">'+l.r+'</div><div class="mm-sub" style="display:flex;gap:1px;">'+st+'</div></div>'+
        '<div class="mini-metric"><div class="mm-label">Rework</div><div class="mm-val" style="color:'+(l.rw===0?'#15803d':'#dc2626')+';">'+l.rw+'</div><div class="mm-sub">'+(l.rw===0?'Clean record':'Needs attention')+'</div></div>'+
        '<div class="mini-metric"><div class="mm-label">Avg Response</div><div class="mm-val">'+l.resp+'</div><div class="mm-sub">Inquiry &rarr; on-site</div></div>'+
      '</div><div class="dp-sec">Team Technicians</div>';
    var techs=LEAD_TECHS[id]||[];
    body+=techs.map(function(t){
      var trc=t.rw===0?'#15803d':t.rw<=1?'#d97706':'#dc2626';
      return '<div class="pr"><div class="pr-top"><span class="pr-id">'+t.i+' · '+t.n+'</span>'+
        '<span class="pr-badge" style="background:'+trc+'22;color:'+trc+';">'+t.rw+' rework</span></div>'+
        '<div class="pr-meta"><i class="bi bi-tools"></i>'+t.d+' · '+t.j+' jobs · '+t.h+'h field · '+t.r+'★ · '+t.p+'% punch-in</div></div>';
    }).join('');
    document.getElementById('dpBody').innerHTML=body;

  } else if(type==='tech'){
    var t=TECHS[id];
    heading=t.n;icon='bi-person-badge';sub=t.d+' · Reports to '+t.lead;
    var stars='';for(var s=1;s<=5;s++)stars+='<i class="bi bi-star'+(s<=Math.round(t.r)?'-fill':'')+'" style="color:#f59e0b;font-size:.8rem;"></i>';
    body='<div class="dp-sec">Performance Summary</div>'+
      '<div style="display:grid;grid-template-columns:1fr 1fr;gap:9px;margin-bottom:14px;">'+
        '<div class="mini-metric"><div class="mm-label">Jobs Completed</div><div class="mm-val">'+t.j+'</div><div class="mm-sub">This month</div></div>'+
        '<div class="mini-metric"><div class="mm-label">Field Hours</div><div class="mm-val">'+t.h+'h</div><div class="mm-sub">On-site total</div></div>'+
        '<div class="mini-metric"><div class="mm-label">Client Rating</div><div class="mm-val" style="color:#f59e0b;">'+t.r+'</div><div class="mm-sub" style="display:flex;gap:1px;">'+stars+'</div></div>'+
        '<div class="mini-metric"><div class="mm-label">Rework Count</div><div class="mm-val" style="color:'+(t.rw===0?'#15803d':'#dc2626')+';">'+t.rw+'</div><div class="mm-sub">'+(t.rw===0?'Clean record':'Needs attention')+'</div></div>'+
        '<div class="mini-metric"><div class="mm-label">Punch-in Rate</div><div class="mm-val" style="color:'+(t.p>=95?'#15803d':'#d97706')+';">'+t.p+'%</div><div class="mm-sub">'+(t.p>=95?'Excellent':'Monitor')+'</div></div>'+
        '<div class="mini-metric"><div class="mm-label">Reports To</div><div class="mm-val" style="font-size:.95rem;">'+t.i+'</div><div class="mm-sub">'+t.lead+'</div></div>'+
      '</div><div class="dp-sec">Recent Job Assignments</div>';
    var jobs=TECH_JOBS[id]||[];
    body+=jobs.map(function(j){return prCard(j.id,j.status,j.bc,j.client,j.meta);}).join('');
    document.getElementById('dpBody').innerHTML=body;

  } else {
    var data=PANEL_DATA[type];if(!data)return;
    heading=data.title;icon=data.icon;sub=data.sub;
    document.getElementById('dpBody').innerHTML=data.items.map(function(it){
      return '<div class="pr"><div class="pr-top"><span class="pr-id">'+it.id+'</span>'+
        '<span class="pr-badge" style="background:'+it.bc+'22;color:'+it.bc+';">'+it.badge+'</span></div>'+
        '<div class="pr-client">'+it.client+'</div><div class="pr-meta">'+it.meta+'</div></div>';
    }).join('');
  }

  document.getElementById('dpTitle').textContent=heading;
  document.getElementById('dpSub').textContent=sub;
  document.getElementById('dpIcon').className='bi '+icon+' dp-hdr-icon';
  document.querySelector('.panel-overlay').classList.add('open');
  document.getElementById('detailPanel').classList.add('open');
}
function closePanel(){
  document.querySelector('.panel-overlay').classList.remove('open');
  document.getElementById('detailPanel').classList.remove('open');
}

/* ============ MISC ============ */
function setPeriod(el, val){
  document.querySelectorAll('.mc-tab').forEach(function(t){ t.classList.remove('active'); });
  el.classList.add('active');
  var u = new URL(window.location);
  u.searchParams.set('period', val);
  window.location = u;
}


function setDateFilter(el,val){document.querySelectorAll('.fq-pill').forEach(function(p){p.classList.remove('active');});el.classList.add('active');toast('info','Filter Applied','Showing data for: '+val);}
function toggleTheme(){var h=document.documentElement;h.setAttribute('data-bs-theme',h.getAttribute('data-bs-theme')==='dark'?'light':'dark');setTimeout(refreshCharts,80);}
function toggleSB(){document.getElementById('sidebar').classList.toggle('open');document.getElementById('sbOverlay').classList.toggle('show');}
function closeSB(){document.getElementById('sidebar').classList.remove('open');document.getElementById('sbOverlay').classList.remove('show');}
function tick(){var t=new Date();document.getElementById('clkTxt').textContent=t.toLocaleTimeString([],{hour:'2-digit',minute:'2-digit',second:'2-digit'});}
setInterval(tick,1000);tick();
function toast(tp,ti,bo){
  var w=document.getElementById('tw');
  var ic={ok:'bi-check-circle-fill ok',warn:'bi-exclamation-triangle-fill warn',info:'bi-info-circle-fill info'};
  var el=document.createElement('div');el.className='ti';
  el.innerHTML='<i class="bi '+(ic[tp]||ic.info)+' ti-ico" style="font-size:.95rem;flex-shrink:0;margin-top:1px;"></i>'+
    '<div><p class="ti-t">'+ti+'</p><p class="ti-b">'+bo+'</p></div>';
  w.appendChild(el);
  setTimeout(function(){el.style.transition='opacity .3s';el.style.opacity='0';setTimeout(function(){el.remove();},300);},3600);
}

window.addEventListener('load',function(){
  renderKPICards();
  renderStatusDist();
  renderQueues();
  renderLeadTable();
  renderTechTable();
  renderTradeList();
  renderCapacity();
  renderRatings();
  buildCharts();
  toast('ok','Dashboard Ready','Projects overview loaded · July 2026');
});
</script>
@endpush