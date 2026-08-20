@extends('layouts.layout')

@section('title', 'SE Dashboard')
@section('page_title', 'SE Dashboard')
<link rel="icon" type="image/png" href="{{ asset('assets/images/favicon.png') }}">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet"/>
 

  @push('styles')
 <style>

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
  --grid-line:rgba(0,0,0,.05);
  --track:rgba(0,0,0,.07);
  --paper:#fbfaf8;
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
  --grid-line:rgba(255,255,255,.05);
  --track:rgba(255,255,255,.08);
  --paper:#191614;
}

*,*::before,*::after{box-sizing:border-box;-webkit-tap-highlight-color:transparent;}
body{font-family:'DM Sans',ui-sans-serif,system-ui,sans-serif;font-size:.875rem;
  background:var(--app-bg);color:var(--text-primary);margin:0;overflow-x:hidden;
  -webkit-font-smoothing:antialiased;}
a{text-decoration:none;}
.cg{font-family:'DM Sans',Georgia,serif;letter-spacing:-.01em;}
.num{font-family:'DM Sans',Georgia,serif;font-weight:700;
  font-variant-numeric:tabular-nums;line-height:1;}
@media(prefers-reduced-motion:reduce){*{animation:none!important;transition:none!important;}}
:focus-visible{outline:2px solid #9a8053;outline-offset:2px;border-radius:4px;}

/* ── SIDEBAR ── */
.sidebar{width:256px;min-height:100vh;background:var(--sidebar-bg);
  border-right:1px solid var(--border-color);display:flex;flex-direction:column;
  position:fixed;top:0;left:0;z-index:300;
  box-shadow:2px 0 20px rgba(0,0,0,.06);
  transition:transform .28s cubic-bezier(.4,0,.2,1);}
.sb-brand{display:flex;align-items:center;gap:11px;padding:18px 20px 15px;
  border-bottom:1px solid var(--border-color);flex-shrink:0;}
.sb-icon{width:38px;height:38px;background:linear-gradient(135deg,#9a8053,#b8975e);
  border-radius:9px;display:flex;align-items:center;justify-content:center;
  color:#fff;font-size:.85rem;font-weight:700;
  font-family:'DM Sans',Georgia,serif;flex-shrink:0;}
.sb-name{font-size:1rem;font-weight:700;color:var(--text-heading);line-height:1.2;
  font-family:'DM Sans',Georgia,serif;}
.sb-sub{font-size:.6875rem;color:var(--text-muted);}
.sb-nav{flex:1;overflow-y:auto;padding:10px 0;}
.sb-nav::-webkit-scrollbar{width:3px;}
.sb-nav::-webkit-scrollbar-thumb{background:var(--border-color);border-radius:2px;}
.sb-sec{font-size:.625rem;font-weight:700;text-transform:uppercase;
  letter-spacing:.1em;color:var(--text-light);padding:14px 20px 4px;}
.sb-nav a{display:flex;align-items:center;gap:10px;padding:9px 20px;
  font-size:.8125rem;color:var(--text-muted);border-right:3px solid transparent;
  transition:background .15s,color .15s;}
.sb-nav a:hover{background:rgba(154,128,83,.08);color:#9a8053;}
.sb-nav a.active{background:rgba(154,128,83,.1);color:#9a8053;font-weight:500;
  border-right-color:#9a8053;}
.sb-nav a i{font-size:1rem;width:18px;text-align:center;flex-shrink:0;}
.sb-badge{font-size:.6rem;font-weight:700;padding:1px 6px;border-radius:9px;margin-left:auto;}
.sb-badge.red{background:rgba(239,68,68,.12);color:#ef4444;}
.sb-badge.amber{background:rgba(217,119,6,.15);color:#d97706;}
.sb-badge.gold{background:rgba(154,128,83,.15);color:#9a8053;}
.sb-footer{padding:14px 18px;border-top:1px solid var(--border-color);flex-shrink:0;}
.sb-user{display:flex;align-items:center;gap:10px;}
.sb-av{width:34px;height:34px;background:linear-gradient(135deg,#9a8053,#b8975e);
  border-radius:50%;display:flex;align-items:center;justify-content:center;
  color:#fff;font-size:.75rem;font-weight:700;}
.sb-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.4);
  z-index:299;backdrop-filter:blur(3px);}
.sb-overlay.show{display:block;}
@media(max-width:991.98px){.sidebar{transform:translateX(-100%);}
  .sidebar.open{transform:translateX(0);}}

/* ── TOPBAR ── */
.topbar{position:fixed;top:0;left:256px;right:0;height:60px;
  background:var(--topbar-bg);border-bottom:1px solid var(--border-color);
  display:flex;align-items:center;justify-content:space-between;
  padding:0 26px;z-index:200;box-shadow:var(--topbar-shadow);}
.tb-l{display:flex;align-items:center;gap:12px;}
.hamburger{display:none;background:none;border:none;padding:6px;
  color:var(--text-heading);cursor:pointer;border-radius:8px;font-size:1.25rem;}
.hamburger:hover{background:rgba(154,128,83,.08);}
.tb-title{font-size:.9375rem;font-weight:600;color:var(--text-heading);
  display:flex;align-items:center;gap:7px;
  font-family:'DM Sans',Georgia,serif;}
.tb-bc{font-size:.72rem;color:var(--text-muted);}
.tb-bc a{color:#9a8053;}
.tb-r{display:flex;align-items:center;gap:10px;}
.role-pill{font-size:.72rem;background:rgba(154,128,83,.12);color:#9a8053;
  padding:4px 11px;border-radius:20px;font-weight:500;white-space:nowrap;}
.clk{font-size:.72rem;color:var(--text-muted);font-variant-numeric:tabular-nums;}
.tdiv{width:1px;height:22px;background:var(--border-color);}
.th-toggle{display:flex;align-items:center;gap:7px;cursor:pointer;user-select:none;}
.th-sun{color:#fbbc06;font-size:.8rem;}
.th-moon{color:#b8975e;font-size:.8rem;}
.tt-track{width:42px;height:22px;background:var(--toggle-track);border-radius:11px;
  position:relative;border:1px solid var(--border-color);}
.tt-thumb{width:16px;height:16px;background:#fff;border-radius:50%;
  position:absolute;top:2px;left:2px;transition:transform .3s,background .3s;
  box-shadow:0 1px 4px rgba(0,0,0,.15);
  display:flex;align-items:center;justify-content:center;}
[data-bs-theme="dark"] .tt-thumb{transform:translateX(20px);background:#9a8053;}
.ts-sun{font-size:8px;color:#fbbc06;}
.ts-moon{font-size:8px;color:#fff;display:none;}
[data-bs-theme="dark"] .ts-sun{display:none;}
[data-bs-theme="dark"] .ts-moon{display:block;}
.av-btn{width:34px;height:34px;background:linear-gradient(135deg,#9a8053,#b8975e);
  border-radius:50%;display:flex;align-items:center;justify-content:center;
  color:#fff;font-size:.8rem;font-weight:700;cursor:pointer;}
@media(max-width:991.98px){.topbar{left:0;}
  .hamburger{display:flex;align-items:center;justify-content:center;}
  .tb-bc,.role-pill,.clk,.tdiv{display:none;}}

/* ── MAIN ── */
.main{margin-left:256px;margin-top:60px;padding:24px 26px 80px;}
@media(max-width:991.98px){.main{margin-left:0;}}
@media(max-width:575px){.main{padding:14px 12px 80px;}}

/* ── GREET ── */
.greet-row{display:flex;align-items:flex-start;justify-content:space-between;
  gap:16px;margin-bottom:18px;flex-wrap:wrap;}
.greeting{font-family:'DM Sans',Georgia,serif;font-size:1.55rem;
  font-weight:700;color:var(--text-heading);margin-bottom:4px;}
.greet-sub{font-size:.8rem;color:var(--text-muted);display:flex;align-items:center;gap:6px;flex-wrap:wrap;}
.cat-tag{font-size:.68rem;font-weight:700;padding:2px 9px;border-radius:20px;
  background:rgba(154,128,83,.12);color:#9a8053;border:1px solid rgba(154,128,83,.25);}
.filter-bar{display:flex;align-items:center;gap:7px;flex-wrap:wrap;}
.fq-pill{padding:5px 13px;border-radius:20px;font-size:.76rem;font-weight:500;
  cursor:pointer;border:1px solid var(--border-color);
  background:var(--card-bg);color:var(--text-muted);
  transition:all .15s;white-space:nowrap;
  font-family:'DM Sans',sans-serif;}
.fq-pill:hover{border-color:#9a8053;color:#9a8053;}
.fq-pill.active{background:#9a8053;color:#fff;border-color:#9a8053;}

/* ── SECTION HEADS ── */
.sec-row{display:flex;align-items:center;gap:12px;margin:26px 0 13px;}
.sec-line{flex:1;height:1px;background:var(--border-color);}
.sec-ttl{font-size:.68rem;font-weight:700;text-transform:uppercase;
  letter-spacing:.1em;color:var(--text-light);white-space:nowrap;
  display:flex;align-items:center;gap:5px;}
.sec-ttl i{color:#9a8053;}

/* ── PANEL ── */
.panel{background:var(--card-bg);border:1px solid var(--border-color);
  border-radius:14px;box-shadow:0 2px 16px rgba(0,0,0,.06);padding:18px 20px;}
.p-hdr{display:flex;align-items:baseline;justify-content:space-between;gap:10px;
  margin-bottom:14px;flex-wrap:wrap;}
.p-ttl{font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.09em;
  color:var(--text-muted);display:flex;align-items:center;gap:6px;}
.p-ttl i{color:#9a8053;font-size:.85rem;}
.p-note{font-size:.68rem;color:var(--text-light);}

/* ── ACTION TILES ── */
.act-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;}
@media(max-width:1000px){.act-grid{grid-template-columns:1fr 1fr;}}
@media(max-width:520px){.act-grid{grid-template-columns:1fr;}}
.act{display:flex;align-items:center;gap:12px;padding:13px 15px;border-radius:12px;
  border:1px solid var(--border-color);background:var(--card-bg);cursor:pointer;
  text-align:left;font-family:inherit;width:100%;
  transition:transform .16s,box-shadow .16s;position:relative;overflow:hidden;}
.act:hover{transform:translateY(-2px);box-shadow:0 8px 24px rgba(0,0,0,.09);}
.act-rail{position:absolute;left:0;top:0;bottom:0;width:3px;}
.act-n{font-family:'DM Sans',Georgia,serif;font-size:1.75rem;
  font-weight:700;line-height:1;min-width:30px;text-align:center;}
.act-t{font-size:.76rem;font-weight:600;color:var(--text-heading);display:block;}
.act-s{font-size:.67rem;color:var(--text-muted);margin-top:1px;}

/* ── METRIC GAUGES ── */
.gauge-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;}
@media(max-width:1000px){.gauge-grid{grid-template-columns:1fr 1fr;}}
.gauge{background:var(--card-bg);border:1px solid var(--border-color);border-radius:13px;
  padding:14px 12px 12px;display:flex;flex-direction:column;align-items:center;}
.gauge svg{width:100%;max-width:132px;height:auto;display:block;}
.g-val{font-family:'DM Sans',Georgia,serif;font-weight:700;
  font-size:1.5rem;line-height:1;}
.g-lbl{font-size:.71rem;font-weight:600;color:var(--text-heading);margin-top:2px;text-align:center;}
.g-sub{font-size:.64rem;color:var(--text-light);margin-top:2px;text-align:center;}

/* ── SPLIT GRID ── */
.split{display:grid;grid-template-columns:1.15fr 1fr;gap:14px;align-items:start;}
@media(max-width:1000px){.split{grid-template-columns:1fr;}}

/* ── DONUT ── */
.donut-wrap{display:flex;align-items:center;gap:20px;flex-wrap:wrap;}
.donut-svg{width:168px;height:168px;flex-shrink:0;}
.donut-seg{transition:opacity .15s;cursor:pointer;}
.donut-seg:hover{opacity:.72;}
.dl{flex:1;min-width:170px;display:flex;flex-direction:column;gap:7px;}
.dl-row{display:flex;align-items:center;gap:8px;font-size:.75rem;}
.dl-dot{width:9px;height:9px;border-radius:3px;flex-shrink:0;}
.dl-name{color:var(--text-muted);flex:1;}
.dl-n{font-family:'DM Sans',Georgia,serif;font-weight:700;
  font-size:.95rem;color:var(--text-heading);}
.dl-pct{font-size:.66rem;color:var(--text-light);min-width:30px;text-align:right;}

/* ── COMPOSITION BARS ── */
.comp{display:flex;flex-direction:column;gap:15px;}
.comp-row-hdr{display:flex;align-items:baseline;justify-content:space-between;margin-bottom:6px;}
.comp-name{font-size:.71rem;font-weight:700;text-transform:uppercase;
  letter-spacing:.07em;color:var(--text-muted);}
.comp-tot{font-size:.66rem;color:var(--text-light);}
.comp-bar{display:flex;height:26px;border-radius:7px;overflow:hidden;
  background:var(--track);}
.comp-seg{display:flex;align-items:center;justify-content:center;flex-shrink:1;flex-basis:0;
  font-size:.66rem;font-weight:700;color:#fff;min-width:0;
  transition:flex-grow .3s;}
.comp-key{display:flex;gap:12px;flex-wrap:wrap;margin-top:6px;}
.comp-k{display:flex;align-items:center;gap:5px;font-size:.68rem;color:var(--text-muted);}
.comp-k i{width:8px;height:8px;border-radius:2px;}

/* ── TEAM CARDS ── */
.team-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:12px;}
@media(max-width:1100px){.team-grid{grid-template-columns:repeat(2,1fr);}}
@media(max-width:520px){.team-grid{grid-template-columns:1fr;}}
.tm{background:var(--card-bg);border:1px solid var(--border-color);border-radius:13px;
  padding:15px;transition:box-shadow .16s,transform .16s;}
.tm:hover{box-shadow:0 6px 22px rgba(0,0,0,.08);transform:translateY(-1px);}
.tm.offduty{opacity:.6;}

.tm-top{display:flex;align-items:flex-start;gap:11px;margin-bottom:12px;}
.tm-ring{position:relative;width:52px;height:52px;flex-shrink:0;}
.tm-ring svg{position:absolute;inset:0;transform:rotate(-90deg);}
.tm-av{position:absolute;inset:6px;border-radius:50%;
  background:linear-gradient(135deg,#9a8053,#b8975e);
  display:flex;align-items:center;justify-content:center;
  color:#fff;font-size:.7rem;font-weight:700;
  font-family:'DM Sans',Georgia,serif;}
.tm-id-col{flex:1;min-width:0;padding-top:1px;}
.tm-name{font-size:.82rem;font-weight:600;color:var(--text-heading);
  white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
.tm-dom{font-size:.66rem;color:var(--text-light);margin-top:1px;}
.tm-st{font-size:.62rem;font-weight:700;padding:2px 8px;border-radius:20px;
  white-space:nowrap;display:inline-block;margin-top:5px;}

/* score band — radial ring + numeral, matches proof-ring language */
.tm-score{display:flex;align-items:center;gap:10px;
  background:var(--surface-2);border-radius:10px;padding:8px 10px;margin-bottom:10px;}
.tm-score-ring{position:relative;width:38px;height:38px;flex-shrink:0;}
.tm-score-ring svg{transform:rotate(-90deg);}
.tm-score-ring span{position:absolute;inset:0;display:flex;align-items:center;
  justify-content:center;font-family:'DM Sans',Georgia,serif;
  font-weight:700;font-size:.78rem;}
.tm-score-mid{flex:1;min-width:0;}
.tm-score-lbl{font-size:.62rem;font-weight:700;text-transform:uppercase;
  letter-spacing:.07em;color:var(--text-light);margin-bottom:2px;}
.tm-stars{display:flex;gap:1px;font-size:.66rem;}

/* stat trio — cormorant numerals like fb-stat / act-n elsewhere */
.tm-stats{display:flex;align-items:stretch;justify-content:space-between;
  border-top:1px solid var(--border-color);border-bottom:1px solid var(--border-color);
  padding:9px 0;margin-bottom:11px;}
.tm-stat{flex:1;text-align:center;display:flex;flex-direction:column;
  align-items:center;gap:2px;}
.tm-stat-ico{font-size:.66rem;opacity:.7;}
.tm-stat-n{font-family:'DM Sans',Georgia,serif;font-weight:700;
  font-size:1.3rem;line-height:1.1;}
.tm-stat-l{font-size:.6rem;font-weight:600;text-transform:uppercase;
  letter-spacing:.05em;color:var(--text-light);}
.tm-stat-div{width:1px;background:var(--border-color);margin:2px 0;flex-shrink:0;}

.tm-btn{width:100%;padding:7px;border-radius:8px;
  font-size:.72rem;font-weight:600;border:1px solid var(--border-color);
  background:transparent;color:var(--text-muted);cursor:pointer;
  font-family:'DM Sans',sans-serif;transition:all .15s;}
.tm-btn:hover{border-color:#9a8053;color:#9a8053;background:rgba(154,128,83,.05);}
.tm-btn:disabled{opacity:.4;cursor:not-allowed;}

/* ── QUEUE CARDS (action zones) ── */
.q2{display:grid;grid-template-columns:1fr 1fr;gap:12px;}
@media(max-width:900px){.q2{grid-template-columns:1fr;}}
.qc-item{background:var(--card-bg);border:1px solid var(--border-color);
  border-radius:13px;padding:14px 16px 13px;position:relative;overflow:hidden;
  transition:box-shadow .15s;display:flex;flex-direction:column;}
.qc-item:hover{box-shadow:0 6px 22px rgba(0,0,0,.09);}
.qc-rail{position:absolute;left:0;top:0;bottom:0;width:4px;}
.qi-top{display:flex;align-items:flex-start;justify-content:space-between;gap:8px;}
.qi-id{font-size:.95rem;
  font-weight:700;color:#9a8053;letter-spacing:.01em;}
.qi-client{font-size:.83rem;font-weight:600;color:var(--text-heading);margin-top:1px;}
.qi-site{font-size:.72rem;color:var(--text-muted);display:flex;align-items:center;
  gap:4px;margin-top:3px;}
.qi-pills{display:flex;gap:5px;flex-wrap:wrap;justify-content:flex-end;}
.pill{font-size:.63rem;font-weight:700;padding:2px 8px;border-radius:20px;
  display:inline-flex;align-items:center;gap:3px;white-space:nowrap;}
.p-iw{background:rgba(154,128,83,.1);color:#9a8053;}
.p-oow{background:rgba(239,68,68,.1);color:#ef4444;}
.qi-issue{font-size:.73rem;color:var(--text-muted);line-height:1.45;margin:9px 0 10px;}

/* age meter */
.age{display:flex;align-items:center;gap:8px;margin-bottom:10px;}
.age-tr{flex:1;height:5px;border-radius:3px;background:var(--track);overflow:hidden;}
.age-fl{height:100%;border-radius:3px;}
.age-t{font-size:.66rem;color:var(--text-light);white-space:nowrap;
  font-variant-numeric:tabular-nums;}

.qi-foot{display:flex;align-items:center;justify-content:space-between;
  gap:8px;flex-wrap:wrap;margin-top:auto;padding-top:10px;
  border-top:1px solid var(--border-color);}
.btn-dispatch{padding:6px 14px;border-radius:20px;font-size:.73rem;font-weight:600;
  background:#9a8053;color:#fff;border:none;cursor:pointer;
  font-family:'DM Sans',sans-serif;transition:opacity .15s;
  display:flex;align-items:center;gap:5px;}
.btn-dispatch:hover{opacity:.86;}
.btn-outline{padding:5px 12px;border-radius:20px;font-size:.73rem;font-weight:600;
  background:transparent;color:var(--text-muted);
  border:1px solid var(--border-color);cursor:pointer;
  font-family:'DM Sans',sans-serif;transition:all .15s;}
.btn-outline:hover{border-color:#9a8053;color:#9a8053;}
.btn-red{background:transparent;color:#dc2626;border:1px solid rgba(220,38,38,.3);
  border-radius:20px;padding:5px 12px;font-size:.73rem;font-weight:600;cursor:pointer;
  font-family:'DM Sans',sans-serif;transition:all .15s;}
.btn-red:hover{background:rgba(220,38,38,.06);}
.qi-actions{display:flex;gap:7px;flex-wrap:wrap;}

/* rework specifics */
.rw-meta{display:flex;align-items:center;gap:10px;flex-wrap:wrap;margin:8px 0;}
.rw-mi{display:flex;align-items:center;gap:4px;font-size:.7rem;color:var(--text-muted);}
.rw-reason{background:rgba(220,38,38,.05);border:1px solid rgba(220,38,38,.14);
  border-radius:8px;padding:8px 10px;margin-bottom:9px;}
.rw-rl{font-size:.62rem;font-weight:700;text-transform:uppercase;
  letter-spacing:.07em;color:#dc2626;margin-bottom:3px;}
.rw-rt{font-size:.73rem;color:var(--text-primary);line-height:1.45;}
.attempt-dots{display:flex;gap:3px;align-items:center;}
.ad{width:6px;height:6px;border-radius:50%;background:var(--track);}
.ad.on{background:#dc2626;}

/* qc proof ring */
.proof-row{display:flex;align-items:center;gap:12px;margin:8px 0 10px;
  background:var(--paper);border:1px solid var(--border-color);
  border-radius:10px;padding:9px 11px;}
.proof-ring{width:44px;height:44px;flex-shrink:0;position:relative;}
.proof-ring svg{transform:rotate(-90deg);}
.proof-ring span{position:absolute;inset:0;display:flex;align-items:center;
  justify-content:center;font-family:'DM Sans',Georgia,serif;
  font-weight:700;font-size:.78rem;}
.proof-list{display:flex;flex-direction:column;gap:3px;flex:1;min-width:0;}
.proof-i{display:flex;align-items:center;gap:6px;font-size:.7rem;}
.proof-i.ok{color:#15803d;}
.proof-i.miss{color:#dc2626;}
.btn-approve{padding:6px 16px;border-radius:20px;font-size:.73rem;font-weight:700;
  background:#15803d;color:#fff;border:none;cursor:pointer;
  font-family:'DM Sans',sans-serif;transition:opacity .15s;
  display:flex;align-items:center;gap:5px;}
.btn-approve:hover{opacity:.86;}
.btn-reject{padding:6px 14px;border-radius:20px;font-size:.73rem;font-weight:700;
  background:rgba(220,38,38,.1);color:#dc2626;
  border:1px solid rgba(220,38,38,.25);cursor:pointer;
  font-family:'DM Sans',sans-serif;transition:all .15s;
  display:flex;align-items:center;gap:5px;}
.btn-reject:hover{background:rgba(220,38,38,.15);}

/* ── FIELD KANBAN ── */
.kanban{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;align-items:start;}
@media(max-width:1100px){.kanban{grid-template-columns:1fr 1fr;}}
@media(max-width:620px){.kanban{grid-template-columns:1fr;}}
.kb-col{background:var(--surface-2);border-radius:12px;border:1px solid var(--border-color);
  display:flex;flex-direction:column;min-height:120px;}
.kb-hdr{display:flex;align-items:center;justify-content:space-between;padding:11px 13px;
  border-bottom:1px solid var(--border-color);position:relative;overflow:hidden;}
.kb-hdr::before{content:'';position:absolute;left:0;top:0;bottom:0;width:4px;}
.kb-hdr-name{font-size:.76rem;font-weight:700;color:var(--text-heading);}
.kb-hdr-count{font-size:.65rem;font-weight:700;padding:2px 8px;border-radius:8px;
  display:inline-flex;align-items:center;justify-content:center;min-width:22px;}
.kb-body{padding:8px;display:flex;flex-direction:column;gap:8px;flex:1;}
.kb-card{background:var(--card-bg);border:1px solid var(--border-color);
  border-radius:10px;padding:11px 12px;transition:box-shadow .15s,transform .15s;
  cursor:default;position:relative;}
.kb-card:hover{box-shadow:0 4px 16px rgba(0,0,0,.08);transform:translateY(-1px);}
.kb-card-top{display:flex;align-items:center;justify-content:space-between;gap:6px;
  margin-bottom:5px;}
.kb-id{font-size:.84rem;
  font-weight:700;color:#9a8053;letter-spacing:.01em;}
.kb-sla{font-size:.6rem;font-weight:700;padding:2px 7px;border-radius:6px;}
.sla-ok{background:rgba(21,128,61,.1);color:#15803d;}
.sla-risk{background:rgba(217,119,6,.1);color:#d97706;}
.sla-br{background:rgba(220,38,38,.1);color:#dc2626;}
.kb-client{font-size:.76rem;font-weight:600;color:var(--text-heading);margin-bottom:2px;
  white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
.kb-ml{display:flex;align-items:center;gap:6px;font-size:.69rem;color:var(--text-muted);
  margin-bottom:4px;}
.kb-ml-av{width:18px;height:18px;border-radius:50%;
  background:linear-gradient(135deg,#9a8053,#b8975e);display:flex;
  align-items:center;justify-content:center;color:#fff;font-size:.5rem;
  font-weight:700;flex-shrink:0;}
.kb-site{font-size:.66rem;color:var(--text-light);margin-bottom:4px;
  display:flex;align-items:center;gap:4px;
  white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
.kb-time{font-size:.65rem;color:var(--text-light);display:flex;align-items:center;gap:4px;
  padding-top:5px;border-top:1px solid var(--border-color);margin-top:2px;}
.kb-empty{font-size:.72rem;color:var(--text-light);text-align:center;padding:20px 8px;
  font-style:italic;}

/* ── SLA STRIP ── */
/* ── CLIENT SPREAD (fills the slot where SLA strip was) ── */
.cs-panel{display:flex;flex-direction:column;}
.cs-list{display:flex;flex-direction:column;gap:11px;flex:1;justify-content:center;}
.cs-row{display:grid;grid-template-columns:26px 1fr 34px;align-items:center;gap:10px;}
.cs-av{width:26px;height:26px;border-radius:50%;flex-shrink:0;
  display:flex;align-items:center;justify-content:center;
  color:#fff;font-size:.6rem;font-weight:700;
  font-family:'DM Sans',Georgia,serif;}
.cs-mid{min-width:0;}
.cs-name{font-size:.76rem;font-weight:600;color:var(--text-heading);
  white-space:nowrap;overflow:hidden;text-overflow:ellipsis;margin-bottom:4px;}
.cs-tr{height:7px;border-radius:4px;background:var(--track);overflow:hidden;}
.cs-fl{height:100%;border-radius:4px;}
.cs-n{font-family:'DM Sans',Georgia,serif;font-weight:700;
  font-size:1.05rem;color:var(--text-heading);text-align:right;}

/* ── TREND (completed) ── */
.trend{display:flex;align-items:flex-end;gap:16px;}
.tbar-wrap{display:flex;flex-direction:column;align-items:center;gap:6px;}
.tbar{width:46px;border-radius:7px 7px 0 0;position:relative;}
.tbar-n{font-family:'DM Sans',Georgia,serif;font-weight:700;
  font-size:1.15rem;color:var(--text-heading);}
.tbar-l{font-size:.64rem;color:var(--text-muted);}
.trend-note{font-size:.72rem;color:var(--text-muted);line-height:1.5;}
.trend-up{color:#15803d;font-weight:700;}

/* ── MODALS (unchanged behaviour) ── */
.modal-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.4);
  z-index:700;backdrop-filter:blur(3px);}
.modal-overlay.open{display:flex;align-items:center;justify-content:center;}
.dispatch-modal{background:var(--card-bg);border-radius:16px;
  width:460px;max-width:95vw;max-height:90vh;overflow-y:auto;
  box-shadow:0 20px 60px rgba(0,0,0,.2);}
.dm-hdr{background:linear-gradient(135deg,#9a8053,#b8975e);padding:18px 22px;
  border-radius:16px 16px 0 0;display:flex;align-items:center;
  justify-content:space-between;}
.dm-title{font-family:'DM Sans',Georgia,serif;font-size:1.1rem;
  font-weight:700;color:#fff;}
.dm-close{background:rgba(255,255,255,.2);border:1px solid rgba(255,255,255,.3);
  color:#fff;width:28px;height:28px;border-radius:7px;display:flex;
  align-items:center;justify-content:center;cursor:pointer;font-size:.8rem;}
.dm-body{padding:18px 22px;}
.dm-sr-info{background:var(--surface-2);border-radius:9px;padding:11px 13px;margin-bottom:14px;}
.dm-sr-id{font-family:'DM Sans',Georgia,serif;font-size:.95rem;
  font-weight:700;color:#9a8053;margin-bottom:3px;}
.dm-sr-detail{font-size:.76rem;color:var(--text-muted);}
.dm-sec-label{font-size:.67rem;font-weight:700;text-transform:uppercase;
  letter-spacing:.08em;color:var(--text-light);margin-bottom:8px;}
.dm-ml-list{display:flex;flex-direction:column;gap:7px;margin-bottom:16px;}
.dm-ml-item{display:flex;align-items:center;gap:10px;padding:10px 12px;
  border-radius:9px;border:1px solid var(--border-color);cursor:pointer;
  transition:all .15s;background:var(--card-bg);}
.dm-ml-item:hover{border-color:#9a8053;background:rgba(154,128,83,.05);}
.dm-ml-item.selected{border-color:#9a8053;background:rgba(154,128,83,.08);}
.dm-ml-av{width:32px;height:32px;border-radius:50%;
  background:linear-gradient(135deg,#9a8053,#b8975e);
  display:flex;align-items:center;justify-content:center;
  color:#fff;font-size:.68rem;font-weight:700;flex-shrink:0;}
.dm-ml-av.busy{background:linear-gradient(135deg,#64748b,#94a3b8);}
.dm-ml-name{font-size:.82rem;font-weight:600;color:var(--text-heading);flex:1;}
.dm-ml-domain{font-size:.68rem;color:var(--text-muted);}
.dm-ml-load{font-size:.7rem;font-weight:600;}
.btn-confirm{width:100%;padding:11px;border-radius:10px;
  background:linear-gradient(135deg,#9a8053,#b8975e);color:#fff;
  border:none;font-size:.84rem;font-weight:700;cursor:pointer;
  font-family:'DM Sans',sans-serif;transition:opacity .15s;}
.btn-confirm:hover{opacity:.88;}
.reject-modal{background:var(--card-bg);border-radius:16px;
  width:440px;max-width:95vw;box-shadow:0 20px 60px rgba(0,0,0,.2);}
.rm-hdr{background:linear-gradient(135deg,#dc2626,#ef4444);padding:16px 20px;
  border-radius:16px 16px 0 0;display:flex;align-items:center;justify-content:space-between;}
.rm-title{font-family:'DM Sans',Georgia,serif;font-size:1rem;font-weight:700;color:#fff;}
.rm-body{padding:18px 20px;}
.rm-textarea{width:100%;min-height:100px;border:1px solid var(--border-color);
  border-radius:9px;padding:10px 12px;font-size:.8rem;
  background:var(--card-bg);color:var(--text-primary);resize:vertical;
  font-family:'DM Sans',sans-serif;}
.rm-textarea:focus{outline:none;border-color:#dc2626;}
.btn-reject-confirm{width:100%;padding:10px;border-radius:9px;
  background:#dc2626;color:#fff;border:none;font-size:.82rem;font-weight:700;
  cursor:pointer;font-family:'DM Sans',sans-serif;margin-top:10px;
  transition:opacity .15s;}
.btn-reject-confirm:hover{opacity:.88;}

/* ── TOAST ── */
#tw{position:fixed;bottom:22px;right:22px;z-index:9999;
  display:flex;flex-direction:column;gap:8px;pointer-events:none;}
.ti{display:flex;align-items:flex-start;gap:9px;padding:11px 14px;
  border-radius:10px;background:var(--card-bg);border:1px solid var(--border-color);
  box-shadow:0 4px 20px rgba(0,0,0,.1);max-width:290px;
  animation:tin .2s ease;pointer-events:auto;}
@keyframes tin{from{opacity:0;transform:translateY(8px);}to{opacity:1;transform:none;}}
.ti-t{font-size:.79rem;font-weight:600;color:var(--text-heading);margin:0 0 2px;}
.ti-b{font-size:.73rem;color:var(--text-muted);margin:0;}
.ok{color:#15803d;}.info{color:#9a8053;}.warn{color:#d97706;}.err{color:#dc2626;}

@keyframes grow{from{transform:scaleY(0);}to{transform:scaleY(1);}}
.animate-bar{animation:grow .55s cubic-bezier(.2,.7,.3,1) both;transform-origin:bottom;}

.page-content
{
    padding:unset;
}


.filter-bar {
  display: flex;
  align-items: center;
  gap: 8px;
  flex-wrap: wrap;
}

.fq-pill,
.fq-date {
  height: 34px;              /* one shared height */
  box-sizing: border-box;
  display: inline-flex;
  align-items: center;
  font-family: inherit;
  font-size: .75rem;
  border-radius: 999px;
  line-height: 1;
}

.fq-date {
  padding: 0 12px;
  font-weight: 500;
  color: var(--text-muted);
  border: 1px solid var(--border-color);
  background: var(--card-bg);
  cursor: pointer;
  transition: all .15s;
}

.fq-date:hover  { border-color: #9a8053; color: #9a8053; }
.fq-date:focus  {
  outline: none;
  border-color: #9a8053;
  color: #9a8053;
  box-shadow: 0 0 0 3px rgba(154,128,83,.12);
}

.fq-range {
  display: flex;
  align-items: center;
  gap: 6px;
  margin: 0;                 /* forms carry default margin */
}

.fq-sep {
  color: var(--muted);
  font-size: .7rem;
  opacity: .6;
  line-height: 1;
}

.fq-date::-webkit-calendar-picker-indicator {
  opacity: .45;
  cursor: pointer;
  margin-left: 6px;
}
.fq-date::-webkit-calendar-picker-indicator:hover { opacity: .8; }
.fq-clear {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 24px;
  height: 24px;
  border-radius: 50%;
  color: var(--muted);
  text-decoration: none;
  font-size: 1.05rem;
  line-height: 1;
  transition: background .15s, color .15s;
}

.fq-clear:hover {
  background: rgba(220,38,38,.1);
  color: #dc2626;
}

.sec-row {
  display: flex;
  align-items: center;
  gap: 12px;
}

.sec-line {
  flex: 1;
  height: 1px;
  background: var(--track);
}

.sec-more {
  margin-left: auto;
  flex-shrink: 0;
  display: inline-flex;
  align-items: center;
  gap: 5px;
  font-size: .7rem;
  font-weight: 600;
  color: #9a8053;
  text-decoration: none;
  white-space: nowrap;
  transition: color .15s, gap .15s;
}

.sec-more:hover { color: #7a6440; gap: 8px; }
.sec-more .bi   { font-size: .65rem; }
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
  </style>

  @endpush


<div class="sb-overlay" id="sbOverlay" onclick="closeSB()"></div>

<!-- ══ SIDEBAR ══ -->


<!-- ══ TOPBAR ══ -->
<header class="topbar" style="display:none;">
  <div class="tb-l">
    <button class="hamburger" onclick="toggleSB()"><i class="bi bi-list"></i></button>
    <div>
      <div class="tb-title"><i class="bi bi-grid-1x2" style="color:#9a8053;"></i>SE Dashboard</div>
      <div class="tb-bc"><a href="admin_dashboard.html">Home</a> / Service Engineer</div>
    </div>
  </div>
  <div class="tb-r">
    <span class="role-pill"><i class="bi bi-person-gear me-1"></i>Service Engineer</span>
    <span class="clk" id="clk"></span>
    <div class="tdiv"></div>
    <div class="th-toggle" onclick="toggleTheme()">
      <i class="bi bi-sun-fill th-sun"></i>
      <div class="tt-track"><div class="tt-thumb"><i class="bi bi-sun-fill ts-sun"></i><i class="bi bi-moon-stars-fill ts-moon"></i></div></div>
      <i class="bi bi-moon-stars-fill th-moon"></i>
    </div>
    <div class="tdiv"></div>
    <div class="av-btn">AZ</div>
  </div>
</header>

<!-- ══ MAIN ══ -->
@section('content')

  <!-- GREETING -->
  <div class="greet-row">
    <div>
      <div class="greeting">{{ $greeting }}</div>
      <div class="greet-sub">
        <i class="bi bi-calendar3" style="color:#9a8053;"></i>
        <span id="dateStr">{{ $today }}</span>
        &nbsp;·&nbsp;
        @foreach($categories as $cat)
  <span class="cat-tag" style="color:{{ $cat->color_code }};">
    <i class="bi {{ $cat->icon }}"></i> {{ $cat->category_name }}
  </span>
@endforeach
      </div>
    </div>
    

    <!-- <div class="filter-bar">
  @foreach($periodOptions as $key => $label)
    <button class="fq-pill {{ ($filters['period'] ?? 'month') === $key ? 'active' : '' }}"
            onclick="setPeriod('{{ $key }}')">{{ $label }}</button>
  @endforeach
</div> -->


<div class="filter-bar">
  @foreach($periodOptions as $key => $label)
    <button type="button"
            class="fq-pill {{ ($filters['period'] ?? 'month') === $key && empty($filters['from']) ? 'active' : '' }}"
            onclick="setPeriod('{{ $key }}')">{{ $label }}</button>
  @endforeach

  <form method="GET" class="fq-range" id="rangeForm">
    <input type="date" name="from" value="{{ $filters['from'] ?? '' }}"
           max="{{ now()->toDateString() }}" class="fq-date">
    <span class="fq-sep">→</span>
    <input type="date" name="to" value="{{ $filters['to'] ?? '' }}"
           max="{{ now()->toDateString() }}" class="fq-date">
    @if (! empty($filters['from']) || ! empty($filters['to']))
      <a href="{{ url()->current() }}" class="fq-reset" title="Clear all filters">
            <i class="bi bi-arrow-counterclockwise"></i>Reset
        </a>
    @endif
  </form>
</div>

  </div>

  <!-- ACTION TILES -->
  <div class="act-grid" id="actGrid" style="margin-bottom:16px;"></div>

  <!-- ══ PERFORMANCE ══ -->
  <div class="sec-row">
    <div class="sec-line"></div>
    <div class="sec-ttl"><i class="bi bi-speedometer2"></i>How the month is running</div>
    <div class="sec-line"></div>
  </div>

  <div class="gauge-grid" id="gaugeGrid" style="margin-bottom:14px;"></div>

  <div class="split">
    <!-- OPEN WORK DONUT -->
    <div class="panel">
      <div class="p-hdr">
        <div class="p-ttl"><i class="bi bi-pie-chart-fill"></i>Open workload</div>
        <div class="p-note" id="openNote"></div>
      </div>
      <div class="donut-wrap">
        <div id="donutHost"></div>
        <div class="dl" id="donutLegend"></div>
      </div>
    </div>

    <!-- COMPOSITION -->
    <div class="panel">
      <div class="p-hdr">
        <div class="p-ttl"><i class="bi bi-bar-chart-steps"></i>What's in your queue</div>
   <div class="p-note">Dispatch + Rework · {{ data_get($composition, 'total', 0) }} SRs</div>
      </div>
      <div class="comp" id="compHost"></div>
    </div>
  </div>

  <div class="split" style="margin-top:14px;">
    <!-- CLIENT SPREAD -->
    <div class="panel cs-panel">
      <div class="p-hdr">
        <div class="p-ttl"><i class="bi bi-building"></i>Where your SRs are</div>
        <div class="p-note" id="csNote"></div>
      </div>
      <div class="cs-list" id="csHost"></div>
    </div>

    <!-- COMPLETED TREND -->
    @php
    $cur   = (int) data_get($closedOut, 'current', 0);
    $prev  = (int) data_get($closedOut, 'previous', 0);
    $diff  = (int) data_get($closedOut, 'diff', 0);
    $pct   = data_get($closedOut, 'pct');
    $max   = max(1, (int) data_get($closedOut, 'max', 1));

    $hCur  = max(8, (int) round($cur  / $max * 79));
    $hPrev = max(8, (int) round($prev / $max * 79));

    $up    = $diff >= 0;
@endphp

<div class="panel">
    <div class="p-hdr">
        <div class="p-ttl"><i class="bi bi-check2-circle"></i>Closed out</div>
        <div class="p-note">{{ data_get($closedOut, 'label_previous', 'Last month') }} vs {{ data_get($closedOut, 'label_current', 'this month') }}</div>
    </div>
    <div class="trend">
        <div class="tbar-wrap">
            <div class="tbar-n">{{ $prev }}</div>
            <div class="tbar" style="height:{{ $hPrev }}px;background:var(--track);"></div>
            <div class="tbar-l">{{ data_get($closedOut, 'label_previous', 'Last month') }}</div>
        </div>
        <div class="tbar-wrap">
            <div class="tbar-n" style="color:{{ $up ? '#15803d' : '#dc2626' }};">{{ $cur }}</div>
            <div class="tbar" style="height:{{ $hCur }}px;background:linear-gradient(to top,{{ $up ? '#15803d,#3aa564' : '#dc2626,#ef4444' }});"></div>
            <div class="tbar-l">{{ data_get($closedOut, 'label_current', 'This month') }}</div>
        </div>
        <div class="trend-note" style="padding-bottom:20px;">
            @if ($diff > 0)
                <span class="trend-up"><i class="bi bi-arrow-up"></i> {{ $diff }} more</span>
                {{ \Illuminate\Support\Str::plural('SR', $diff) }} completed
                @if (! is_null($pct)) — a {{ $pct }}% lift @endif
            @elseif ($diff < 0)
                <span class="trend-down"><i class="bi bi-arrow-down"></i> {{ abs($diff) }} fewer</span>
                {{ \Illuminate\Support\Str::plural('SR', abs($diff)) }} completed
                @if (! is_null($pct)) — a {{ abs($pct) }}% drop @endif
            @else
                No change from the previous period.
            @endif
            @if (! is_null(data_get($metrics, 'reworkRate')))
                , with rework at {{ data_get($metrics, 'reworkRate') }}%.
            @endif
        </div>
    </div>
</div>
  </div>

  <!-- ══ TEAM ══ -->
  <div class="sec-row" id="mlSection">
    <div class="sec-line"></div>
    <div class="sec-ttl"><i class="bi bi-people-fill"></i>Your maintenance leads</div>
    <div class="sec-line"></div>
  </div>

  <div class="panel" style="margin-bottom:0;">
    <div class="p-hdr">
      <div class="p-ttl"><i class="bi bi-diagram-3-fill"></i>Team availability</div>
      <div class="p-note" id="teamNote"></div>
    </div>
    <div class="team-grid" id="teamGrid"></div>
  </div>

  <!-- ══ DISPATCH QUEUE ══ -->
  <div class="sec-row" id="pendingSection">
    <div class="sec-line"></div>
    <div class="sec-ttl"><i class="bi bi-send"></i>Waiting on you to dispatch — {{ $pendingItems->count() }} {{ \Illuminate\Support\Str::plural('SR', $pendingItems->count()) }}
</div>
  @if ($pendingItems->count())

 <a href="{{ route('dispatch_engine', array_filter([
        'range' => ! empty($filters['from']) ? 'custom' : ($filters['period'] ?? null),
        'from'  => $filters['from'] ?? null,
        'to'    => $filters['to'] ?? null,
   ])) }}" class="sec-more">
  View all <i class="bi bi-arrow-right"></i>
</a>
@endif

    <div class="sec-line"></div>
  </div>
  <div class="q2" id="pendingList"></div>

  <!-- ══ REWORK ══ -->
  <div class="sec-row" id="reworkSection">
    <div class="sec-line"></div>
    <div class="sec-ttl" style="color:#dc2626;"><i class="bi bi-arrow-counterclockwise" style="color:#dc2626;"></i>  Sent back by QC — {{ $reworkItems->count() }} {{ \Illuminate\Support\Str::plural('SR', $reworkItems->count()) }} · urgent
</div>
    <div class="sec-line"></div>
  </div>
  <div class="q2" id="reworkList"></div>

  <!-- ══ QC ══ -->
  <div class="sec-row" id="qcSection">
    <div class="sec-line"></div>
    <div class="sec-ttl" style="color:#7c3aed;"><i class="bi bi-patch-check" style="color:#7c3aed;"></i>Your QC review queue
      <span style="font-size:.62rem;padding:2px 8px;border-radius:20px;background:rgba(124,58,237,.1);color:#7c3aed;font-weight:700;margin-left:4px;">Permission enabled</span>

     <a href="{{ route('qc_review', array_filter([
        'range' => ! empty($filters['from']) ? 'custom' : ($filters['period'] ?? null),
        'from'  => $filters['from'] ?? null,
        'to'    => $filters['to'] ?? null,
   ])) }}" class="sec-more" style="text-transform:none;">
  View all <i class="bi bi-arrow-right"></i>
</a>
    </div>
    <div class="sec-line"></div>
  </div>
  <div class="q2" id="qcList"></div>

  <!-- ══ FIELD ══ -->
  <div class="sec-row" id="fieldSection">
    <div class="sec-line"></div>
    <div class="sec-ttl"><i class="bi bi-activity"></i>Live in the field <span style="opacity:.5;font-weight:400;">· read-only</span></div>
    <div class="sec-line"></div>
  </div>
  <div class="kanban" id="fieldKanban"></div>

</main>

<!-- ══ DISPATCH MODAL ══ -->
<div class="modal-overlay" id="dispatchModal">
  <div class="dispatch-modal">
    <div class="dm-hdr">
      <div class="dm-title cg">Assign Maintenance Lead</div>
      <button class="dm-close" onclick="closeDispatchModal()"><i class="bi bi-x-lg"></i></button>
    </div>
    <div class="dm-body">
      <div class="dm-sr-info" id="dmSrInfo"></div>
      <div class="dm-sec-label">Select ML — filtered to your categories</div>
      <div class="dm-ml-list" id="dmMlList"></div>
      <button class="btn-confirm" onclick="confirmDispatch()">
        <i class="bi bi-send"></i>&nbsp;&nbsp;Confirm Dispatch
      </button>
    </div>
  </div>
</div>

<!-- ══ REJECT MODAL ══ -->
<div class="modal-overlay" id="rejectModal">
  <div class="reject-modal">
    <div class="rm-hdr">
      <div class="rm-title cg">Reject QC — Enter Reason</div>
      <button class="dm-close" onclick="closeRejectModal()"><i class="bi bi-x-lg"></i></button>
    </div>
    <div class="rm-body">
      <div class="dm-sec-label" style="margin-bottom:6px;">SR: <span id="rejectSrId" style="color:#9a8053;font-weight:700;"></span></div>
      <div class="dm-sec-label">Rejection reason</div>
      <textarea class="rm-textarea" id="rejectReason" placeholder="Describe what was incorrect or incomplete in the submitted proof. Be specific — this will be visible to the ML."></textarea>
      <button class="btn-reject-confirm" onclick="confirmReject()">
        <i class="bi bi-x-circle"></i>&nbsp;&nbsp;Reject & Return to Field
      </button>
    </div>
  </div>
</div>

<div id="tw"></div>
@endsection

@push('scripts')
<script>
/* ══════════════════════════════════════════
   DATA — unchanged from the original dashboard
══════════════════════════════════════════ */
var PENDING_SRS = @json($pendingItems);
var REWORK_SRS = @json($reworkItems);
var ACTIVE_FIELD = {{ $activeFieldCount }};
var COMPOSITION = @json($composition);
var CLIENT_SPREAD = @json($clientSpread);

var FIELD_SRS = @json($field);

var MLS = [
  {init:'MK',name:'Mohammed Khalil',domain:'Electrical', status:'onsite',    jobs:2, available:false, score:4.2, completed:34, pending:2, rework:3},
  {init:'AR',name:'Ahmed Rashid',   domain:'Electrical', status:'available', jobs:0, available:true,  score:4.7, completed:41, pending:0, rework:1},
  {init:'KS',name:'Khalid Salem',   domain:'Mechanical', status:'available', jobs:1, available:true,  score:4.5, completed:29, pending:1, rework:2},
  {init:'FM',name:'Faisal Mohammed',domain:'Electrical', status:'onsite',    jobs:1, available:false, score:3.8, completed:26, pending:1, rework:4},
  {init:'NH',name:'Nasser Hassan',  domain:'Mechanical', status:'enroute',   jobs:1, available:false, score:4.4, completed:31, pending:1, rework:1},
  {init:'SM',name:'Saeed Mansoor',  domain:'Electrical', status:'offduty',   jobs:0, available:false, score:4.1, completed:18, pending:0, rework:2},
];

var QC_SRS = @json($qcItems);

var COMPLETED_THIS_MONTH = 28, COMPLETED_LAST_MONTH = 22;
var METRICS = @json($metrics);

var PCOL={'High':'#dc2626','Medium':'#d97706','Low':'#15803d'};
var PBG ={'High':'rgba(220,38,38,.1)','Medium':'rgba(217,119,6,.1)','Low':'rgba(21,128,61,.1)'};
var C = {gold:'#9a8053',gold2:'#b8975e',red:'#dc2626',amber:'#d97706',green:'#15803d',
         blue:'#2563eb',cyan:'#0891b2',orange:'#ea580c',purple:'#7c3aed'};

var currentDispatchSR = null, selectedML = null, currentRejectId = '';

var TEAM = @json($team);
/* helpers */
function el(id){return document.getElementById(id);}
function fieldAll(){return FIELD_SRS.assigned.concat(FIELD_SRS.eta,FIELD_SRS.inprog,FIELD_SRS.review);}
function activeInField(){return FIELD_SRS.assigned.length+FIELD_SRS.eta.length+FIELD_SRS.inprog.length;}
function slaAtRisk(){return fieldAll().filter(function(s){return s.sla==='risk';}).length;}

/* ══════════════════════════════════════════
   ACTION TILES
══════════════════════════════════════════ */
function renderActions(){
  var tiles = [
    {n:REWORK_SRS.length, t:'In rework', s:'QC rejected — needs re-dispatch', c:C.red,   ico:'bi-arrow-counterclockwise', go:'reworkSection'},
    {n:PENDING_SRS.length,t:'Pending your dispatch', s:'No ML assigned yet',  c:C.gold,  ico:'bi-send',                   go:'pendingSection'},
    {n:1,                 t:'At SLA breach risk today', s:'Escalate before the window closes', c:C.amber, ico:'bi-speedometer2', go:'fieldSection'},
    {n:QC_SRS.length,     t:'Awaiting your QC review', s:'Proof submitted, decision pending', c:C.purple,ico:'bi-patch-check', go:'qcSection'},
  ];
  el('actGrid').innerHTML = tiles.map(function(t){
    return '<button class="act" onclick="jumpTo(\''+t.go+'\')">'+
      '<span class="act-rail" style="background:'+t.c+';"></span>'+
      '<span class="act-n" style="color:'+t.c+';">'+t.n+'</span>'+
      '<span><span class="act-t">'+t.t+'</span>'+
      '<span class="act-s" style="display:block;">'+t.s+'</span></span>'+
      '<i class="bi '+t.ico+'" style="margin-left:auto;color:'+t.c+';opacity:.5;font-size:1.05rem;"></i>'+
    '</button>';
  }).join('');
}

/* ══════════════════════════════════════════
   GAUGES
══════════════════════════════════════════ */
function gaugeSVG(pct, color){
  var r=44, cx=60, cy=54, len=Math.PI*r;
  var d='M '+(cx-r)+' '+cy+' A '+r+' '+r+' 0 0 1 '+(cx+r)+' '+cy;
  return '<svg viewBox="0 0 120 68" role="img">'+
    '<path d="'+d+'" fill="none" stroke="var(--track)" stroke-width="9" stroke-linecap="round"/>'+
    '<path d="'+d+'" fill="none" stroke="'+color+'" stroke-width="9" stroke-linecap="round" '+
      'stroke-dasharray="'+(len*pct).toFixed(1)+' '+(len*2).toFixed(1)+'"/>'+
  '</svg>';
}

function renderGauges(){
  var gs = [
    {v:METRICS.reworkRate+'%',   pct:METRICS.reworkRate/100,            c:C.red,     lbl:'Rework rate',       sub:METRICS.reworkSub},
    {v:METRICS.dispatchLabel,    pct:Math.min(1, METRICS.dispatchMins/60), c:C.blue,  lbl:'Avg dispatch time', sub:'intake → ML assigned'},
    {v:METRICS.slaCompliance+'%',pct:METRICS.slaCompliance/100,         c:C.green,   lbl:'SLA compliance',    sub:'closed within window'},
    {v:METRICS.ratingLabel, pct:METRICS.rating/5, c:'#f59e0b', lbl:'Avg client rating', sub:METRICS.ratingSub},
  ];
  el('gaugeGrid').innerHTML = gs.map(function(g){
    return '<div class="gauge">'+gaugeSVG(g.pct,g.c)+
      '<div class="g-val" style="color:'+g.c+';margin-top:-14px;">'+g.v+'</div>'+
      '<div class="g-lbl">'+g.lbl+'</div>'+
      '<div class="g-sub">'+g.sub+'</div>'+
    '</div>';
  }).join('');
}

/* ══════════════════════════════════════════
   DONUT — open workload
══════════════════════════════════════════ */
function renderDonut(){
  var segs = [
    {name:'Pending dispatch', n:PENDING_SRS.length, c:C.gold,   go:'pendingSection'},
    {name:'In rework',        n:REWORK_SRS.length,  c:C.red,    go:'reworkSection'},
    {name:'Active in field',  n:ACTIVE_FIELD,       c:C.blue,   go:'fieldSection'},
    {name:'Awaiting QC',      n:QC_SRS.length,      c:C.purple, go:'qcSection'},
  ];
  var total = segs.reduce(function(a,s){return a+s.n;},0);

  if(!total){
    el('donutHost').innerHTML =
      '<svg class="donut-svg" viewBox="0 0 168 168" role="img" aria-label="Open workload by stage">'+
        '<circle cx="84" cy="84" r="62" fill="none" stroke="var(--track)" stroke-width="21"/>'+
        '<text x="84" y="80" text-anchor="middle" font-family="DM Sans,Georgia,serif" '+
          'font-size="34" font-weight="700" fill="var(--text-heading)">0</text>'+
        '<text x="84" y="98" text-anchor="middle" font-size="9.5" letter-spacing="1.4" '+
          'fill="var(--text-muted)">OPEN SRs</text>'+
      '</svg>';
    el('donutLegend').innerHTML = '<div class="kb-empty">Nothing open in this period</div>';
    el('openNote').textContent = '';
    return;
  }

  var r=62, cx=84, cy=84, circ=2*Math.PI*r, off=0;
  var arcs = segs.map(function(s){
    var len = circ*(s.n/total);
    var a = '<circle class="donut-seg" cx="'+cx+'" cy="'+cy+'" r="'+r+'" fill="none" '+
      'stroke="'+s.c+'" stroke-width="21" '+
      'stroke-dasharray="'+(len-2.5).toFixed(2)+' '+(circ-len+2.5).toFixed(2)+'" '+
      'stroke-dashoffset="'+(-off).toFixed(2)+'" transform="rotate(-90 '+cx+' '+cy+')">'+
      '<title>'+s.name+': '+s.n+'</title></circle>';
    off += len;
    return a;
  }).join('');

  el('donutHost').innerHTML =
    '<svg class="donut-svg" viewBox="0 0 168 168" role="img" aria-label="Open workload by stage">'+
      arcs+
      '<text x="84" y="80" text-anchor="middle" font-family="DM Sans,Georgia,serif" '+
        'font-size="34" font-weight="700" fill="var(--text-heading)">'+total+'</text>'+
      '<text x="84" y="98" text-anchor="middle" font-size="9.5" letter-spacing="1.4" '+
        'fill="var(--text-muted)">OPEN SRs</text>'+
    '</svg>';

  el('donutLegend').innerHTML = segs.map(function(s){
    return '<div class="dl-row" style="cursor:pointer;" onclick="jumpTo(\''+s.go+'\')">'+
      '<span class="dl-dot" style="background:'+s.c+';"></span>'+
      '<span class="dl-name">'+s.name+'</span>'+
      '<span class="dl-n">'+s.n+'</span>'+
      '<span class="dl-pct">'+Math.round(s.n/total*100)+'%</span>'+
    '</div>';
  }).join('');

  el('openNote').textContent = QC_SRS.length+' of these are also awaiting your review';
}

/* ══════════════════════════════════════════
   COMPOSITION BARS
══════════════════════════════════════════ */
function tally(arr,key){
  var m={};arr.forEach(function(x){m[x[key]]=(m[x[key]]||0)+1;});return m;
}
function compBar(title,map,order,colors,total){
  var segs = order.filter(function(k){return map[k];}).map(function(k){
    return '<div class="comp-seg" style="flex:'+map[k]+' 1 0;background:'+colors[k]+';" '+
      'title="'+k+': '+map[k]+'">'+map[k]+'</div>';
  }).join('');
  var key = order.filter(function(k){return map[k];}).map(function(k){
    return '<span class="comp-k"><i style="background:'+colors[k]+';"></i>'+k+'</span>';
  }).join('');
  return '<div>'+
    '<div class="comp-row-hdr"><span class="comp-name">'+title+'</span>'+
      '<span class="comp-tot">'+total+' SRs</span></div>'+
    '<div class="comp-bar">'+segs+'</div>'+
    '<div class="comp-key">'+key+'</div>'+
  '</div>';
}

var palette = ['#9a8053','#0891b2','#f59e0b','#8b5cf6','#06b6d4','#f97316'];

function colorsFor(data, known) {
  var map = {}, i = 0;
  Object.keys(data).forEach(function (k) {
    map[k] = (known && known[k]) || palette[i++ % palette.length];
  });
  return map;
}

function renderComposition(){
  var d = COMPOSITION;
  if (!d || !d.total) { el('compHost').innerHTML = ''; return; }

  var html = '';
  html += compBar('Priority', d.priority, Object.keys(d.priority),
    colorsFor(d.priority, {High:'#dc2626', Medium:'#d97706', Low:'#15803d'}), d.total);
  html += compBar('Warranty scope', d.scope, ['IW','OoW'],
    {IW:'#9a8053', OoW:'#ef4444'}, d.total);
  html += compBar('Category', d.cat, Object.keys(d.cat),
    colorsFor(d.cat), d.total);
  el('compHost').innerHTML = html;
}
/* ══════════════════════════════════════════
   CLIENT SPREAD
══════════════════════════════════════════ */
function renderClientSpread(){
  var d = CLIENT_SPREAD;

  if (!d || !d.top.length) {
    el('csHost').innerHTML = '<p class="empty">No open SRs in this period.</p>';
    el('csNote').textContent = '';
    return;
  }

  var max = d.top[0].n;

  el('csHost').innerHTML = d.top.map(function(row, i){
    var name = row.name || 'Unknown';
    var c    = palette[i % palette.length];
    var init = name.split(/\s+/).filter(Boolean)
                 .map(function(w){ return w[0]; })
                 .join('').substring(0,2).toUpperCase() || '?';
    var pct  = Math.max(14, Math.round(row.n / max * 100));

    return '<div class="cs-row">'+
      '<span class="cs-av" style="background:linear-gradient(135deg,'+c+','+c+'bb);">'+init+'</span>'+
      '<div class="cs-mid">'+
        '<div class="cs-name">'+name+'</div>'+
        '<div class="cs-tr"><div class="cs-fl" style="width:'+pct+'%;background:'+c+';"></div></div>'+
      '</div>'+
      '<span class="cs-n">'+row.n+'</span>'+
    '</div>';
  }).join('');

  el('csNote').textContent = d.clients + ' clients · ' + d.total + ' open SRs';
}

/* ══════════════════════════════════════════
   TEAM CAPACITY
══════════════════════════════════════════ */
function renderTeam(){
  var labels = {available:'Available', onsite:'On-site', enroute:'En-route', offduty:'Off-duty'};
  var cols   = {available:C.green, onsite:C.amber, enroute:C.blue, offduty:'#94a3b8'};

  var rows = (TEAM && TEAM.rows) || [];

  if (!rows.length) {
    el('teamGrid').innerHTML = '<p class="empty">No maintenance leads assigned in this period.</p>';
    el('teamNote').textContent = '';
    return;
  }

  el('teamGrid').innerHTML = rows.map(function(ml){
    var c      = cols[ml.status] || cols.available;
    var score  = (ml.score === null || ml.score === undefined) ? null : Number(ml.score);
    var scoreC = score === null ? 'var(--text-light)'
               : score >= 4.5 ? C.green
               : score >= 4.0 ? C.amber : C.red;

    var r1 = 23, circ1 = 2 * Math.PI * r1;
    var statusRing =
      '<svg viewBox="0 0 52 52" width="52" height="52">'+
        '<circle cx="26" cy="26" r="'+r1+'" fill="none" stroke="var(--track)" stroke-width="2.5"/>'+
        '<circle cx="26" cy="26" r="'+r1+'" fill="none" stroke="'+c+'" stroke-width="2.5" '+
          'stroke-linecap="round" stroke-dasharray="'+
          (ml.status === 'offduty' ? circ1 * 0.14 : circ1).toFixed(1)+' '+circ1.toFixed(1)+'"/>'+
      '</svg>';

    var r2 = 16, circ2 = 2 * Math.PI * r2, pct = score === null ? 0 : score / 5;
    var scoreRing =
      '<svg width="38" height="38" viewBox="0 0 38 38">'+
        '<circle cx="19" cy="19" r="'+r2+'" fill="none" stroke="var(--track)" stroke-width="3.5"/>'+
        '<circle cx="19" cy="19" r="'+r2+'" fill="none" stroke="'+scoreC+'" stroke-width="3.5" '+
          'stroke-linecap="round" stroke-dasharray="'+(circ2*pct).toFixed(1)+' '+circ2.toFixed(1)+'"/>'+
      '</svg><span style="color:'+scoreC+';">'+(score === null ? '—' : score.toFixed(1))+'</span>';

    var stars = '';
    for (var i = 1; i <= 5; i++) {
      if (score !== null && score >= i)          stars += '<i class="bi bi-star-fill" style="color:#f59e0b;"></i>';
      else if (score !== null && score >= i-0.5) stars += '<i class="bi bi-star-half" style="color:#f59e0b;"></i>';
      else                                       stars += '<i class="bi bi-star" style="color:var(--track);"></i>';
    }

    return '<div class="tm '+(ml.status === 'offduty' ? 'offduty' : '')+'">'+

      '<div class="tm-top">'+
        '<div class="tm-ring">'+statusRing+
          '<div class="tm-av">'+ml.init+'</div>'+
        '</div>'+
        '<div class="tm-id-col">'+
          '<div class="tm-name">'+ml.name+'</div>'+
          '<div class="tm-dom">'+(ml.domain || '—')+'</div>'+
          '<span class="tm-st" style="background:'+c+'1a;color:'+c+';">'+(labels[ml.status] || ml.status)+'</span>'+
        '</div>'+
      '</div>'+

      '<div class="tm-score">'+
        '<div class="tm-score-ring">'+scoreRing+'</div>'+
      '<div class="tm-score-lbl">Review Score'+
  // (ml.reviews ? ' · '+ml.reviews+' '+(ml.reviews === 1 ? 'review' : 'reviews') : '')+
'</div>'+
      '</div>'+

      '<div class="tm-stats">'+
        '<div class="tm-stat">'+
          '<i class="bi bi-check2-circle tm-stat-ico" style="color:'+C.green+';"></i>'+
          '<span class="tm-stat-n" style="color:'+C.green+';">'+ml.completed+'</span>'+
          '<span class="tm-stat-l">Completed</span>'+
        '</div>'+
        '<div class="tm-stat-div"></div>'+
        '<div class="tm-stat">'+
          '<i class="bi bi-hourglass-split tm-stat-ico" style="color:'+(ml.pending>0?C.amber:'var(--text-light)')+';"></i>'+
          '<span class="tm-stat-n" style="color:'+(ml.pending>0?C.amber:'var(--text-light)')+';">'+ml.pending+'</span>'+
          '<span class="tm-stat-l">Pending</span>'+
        '</div>'+
        '<div class="tm-stat-div"></div>'+
        '<div class="tm-stat">'+
          '<i class="bi bi-arrow-counterclockwise tm-stat-ico" style="color:'+(ml.rework>2?C.red:'var(--text-muted)')+';"></i>'+
          '<span class="tm-stat-n" style="color:'+(ml.rework>2?C.red:'var(--text-muted)')+';">'+ml.rework+'</span>'+
          '<span class="tm-stat-l">Rework</span>'+
        '</div>'+
      '</div>'+

      (ml.available
        ? '<button class="tm-btn" style="display:none;" data-ml="'+ml.id+'"><i class="bi bi-send" style="margin-right:5px;"></i>Select for dispatch</button>'
        : '<button class="tm-btn" style="display:none;"  disabled>Unavailable</button>')+
    '</div>';
  }).join('');

  el('teamNote').textContent = TEAM.available+' of '+TEAM.total+' free · '+
    TEAM.jobs+' active jobs · '+TEAM.completed+' completed';

  document.querySelectorAll('.tm-btn:not(:disabled)').forEach(function(b){
    b.addEventListener('click', function(){
      toast('info','Pick an SR first','Open a pending SR, then choose this ML in the dispatch panel.');
    });
  });
}
/* ══════════════════════════════════════════
   DISPATCH QUEUE CARDS
══════════════════════════════════════════ */
function renderPending(){
  var oldest = Math.max.apply(null, PENDING_SRS.map(function(s){return s.hrs;}));
  el('pendingList').innerHTML = PENDING_SRS.map(function(sr){
    var c = PCOL[sr.priority];
    var pct = Math.round(sr.hrs/oldest*100);
    return '<article class="qc-item">'+
      '<span class="qc-rail" style="background:'+c+';"></span>'+
      '<div class="qi-top">'+
        '<div><div class="qi-id">'+sr.id+'</div>'+
          '<div class="qi-client">'+sr.client+'</div>'+
          '<div class="qi-site"><i class="bi bi-geo-alt" style="color:#9a8053;font-size:.7rem;"></i>'+sr.site+'</div>'+
        '</div>'+
        '<div class="qi-pills">'+
          '<span class="pill" style="background:'+PBG[sr.priority]+';color:'+c+';">'+sr.priority+'</span>'+
          '<span class="pill '+(sr.scope==='IW'?'p-iw':'p-oow')+'">'+sr.scope+'</span>'+
          '<span class="pill" style="background:rgba(154,128,83,.08);color:#9a8053;">'+sr.cat+'</span>'+
        '</div>'+
      '</div>'+
      '<p class="qi-issue">'+sr.issue+'</p>'+
      '<div class="age">'+
        '<span class="age-t"><i class="bi bi-hourglass-split" style="color:'+c+';"></i> Logged '+sr.logged+'</span>'+
        '<span class="age-tr"><span class="age-fl" style="width:'+pct+'%;background:'+c+';display:block;"></span></span>'+
        '<span class="age-t">'+sr.hrs+'h</span>'+
      '</div>'+
      '<div class="qi-foot">'+
        '</div>'+
      '</div>'+
    '</article>';
  }).join('');
  document.querySelectorAll('#pendingList .btn-dispatch').forEach(function(b){
    b.addEventListener('click',function(){openDispatch(this.dataset.srid);});
  });
}

/* ══════════════════════════════════════════
   REWORK CARDS
══════════════════════════════════════════ */
function renderRework(){
  el('reworkList').innerHTML = REWORK_SRS.map(function(sr){
    var c = PCOL[sr.priority];
    var dots='';for(var i=0;i<3;i++){dots+='<span class="ad'+(i<sr.attempt?' on':'')+'"></span>';}
    return '<article class="qc-item" style="border-color:rgba(220,38,38,.25);">'+
      '<span class="qc-rail" style="background:'+C.red+';"></span>'+
      '<div class="qi-top">'+
        '<div><div class="qi-id">'+sr.id+'</div>'+
          '<div class="qi-client">'+sr.client+'</div>'+
          '<div class="qi-site"><i class="bi bi-geo-alt" style="color:#9a8053;font-size:.7rem;"></i>'+sr.site+'</div>'+
        '</div>'+
        '<div class="qi-pills">'+
          '<span class="pill" style="background:rgba(220,38,38,.1);color:#dc2626;">'+
            '<i class="bi bi-arrow-counterclockwise"></i>Attempt '+sr.attempt+'</span>'+
          '<span class="pill" style="background:'+PBG[sr.priority]+';color:'+c+';">'+sr.priority+'</span>'+
          '<span class="pill '+(sr.scope==='IW'?'p-iw':'p-oow')+'">'+sr.scope+'</span>'+
        '</div>'+
      '</div>'+
      '<div class="rw-meta">'+
        '<span class="rw-mi"><i class="bi bi-person" style="color:#9a8053;"></i>Was with <strong>'+sr.originalML+'</strong></span>'+
        '<span class="rw-mi"><i class="bi bi-clock"></i>Rejected '+sr.elapsed+'</span>'+
        '<span class="rw-mi attempt-dots" title="Attempt '+sr.attempt+' of 3">'+dots+'</span>'+
      '</div>'+
      '<div class="rw-reason">'+
        '<div class="rw-rl">Why QC sent it back</div>'+
        '<div class="rw-rt">'+sr.reason+'</div>'+
      '</div>'+
      '<div class="qi-foot">'+
        '<span class="age-t">'+sr.cat+' · re-dispatch needed</span>'+
       
        // '<div class="qi-actions">'+
        //   '<button class="btn-red" data-srid="'+sr.id+'" data-type="reassign">'+
        //     '<i class="bi bi-person-check"></i> Same ML</button>'+
        //   '<button class="btn-dispatch" data-srid="'+sr.id+'" data-type="reallocate">'+
        //     '<i class="bi bi-person-arrows"></i>Reallocate</button>'+
        // '</div>'+

      '</div>'+
    '</article>';
  }).join('');
  document.querySelectorAll('#reworkList .btn-dispatch, #reworkList .btn-red').forEach(function(b){
    b.addEventListener('click',function(){
      openDispatch(this.dataset.srid, this.dataset.type==='reassign');
    });
  });
}

/* ══════════════════════════════════════════
   QC CARDS
══════════════════════════════════════════ */
function renderQC(){
  if(!QC_SRS.length){
    el('qcList').innerHTML = '<div class="kb-empty">Nothing awaiting your review</div>';
    return;
  }

  el('qcList').innerHTML = QC_SRS.map(function(sr){
    var p = sr.proofs || {before:false, after:false, form:false};
    var done = [p.before,p.after,p.form].filter(Boolean).length;
    var ringC = done===3 ? C.green : C.red;
    var r=18, circ=2*Math.PI*r;
    var items=[['Before photos',p.before],['After photos',p.after],['Acceptance form',p.form]];
    return '<article class="qc-item" style="border-color:rgba(124,58,237,.25);">'+
      '<span class="qc-rail" style="background:linear-gradient(to bottom,#7c3aed,#9c65f7);"></span>'+
      '<div class="qi-top">'+
        '<div><div class="qi-id">'+sr.id+'</div>'+
          '<div class="qi-client">'+sr.client+'</div>'+
          '<div class="qi-site"><i class="bi bi-geo-alt" style="color:#9a8053;font-size:.7rem;"></i>'+sr.site+'</div>'+
        '</div>'+
        '<div class="qi-pills">'+
          '<span class="pill" style="background:rgba(124,58,237,.1);color:#7c3aed;">QC pending</span>'+
          '<span class="pill" style="background:'+PBG[sr.priority]+';color:'+PCOL[sr.priority]+';">'+sr.priority+'</span>'+
          '<span class="pill '+(sr.scope==='IW'?'p-iw':'p-oow')+'">'+sr.scope+'</span>'+
        '</div>'+
      '</div>'+
      '<p class="qi-issue">'+sr.issue+'</p>'+
      '<div class="proof-row">'+
        '<div class="proof-ring">'+
          '<svg width="44" height="44" viewBox="0 0 44 44">'+
            '<circle cx="22" cy="22" r="'+r+'" fill="none" stroke="var(--track)" stroke-width="4"/>'+
            '<circle cx="22" cy="22" r="'+r+'" fill="none" stroke="'+ringC+'" stroke-width="4" '+
              'stroke-linecap="round" stroke-dasharray="'+(circ*done/3).toFixed(1)+' '+circ.toFixed(1)+'"/>'+
          '</svg><span style="color:'+ringC+';">'+done+'/3</span>'+
        '</div>'+
        '<div class="proof-list">'+
          items.map(function(it){
            return '<span class="proof-i '+(it[1]?'ok':'miss')+'">'+
              '<i class="bi '+(it[1]?'bi-check-circle-fill':'bi-x-circle-fill')+'"></i>'+it[0]+'</span>';
          }).join('')+
        '</div>'+
      '</div>'+
      '<div class="rw-meta" style="margin:0 0 8px;">'+
        '<span class="rw-mi"><i class="bi bi-person" style="color:#9a8053;"></i>'+sr.ml+'</span>'+
        '<span class="rw-mi"><i class="bi bi-box-arrow-right"></i>Punched out '+sr.punchout+'</span>'+
      '</div>'+
      '<div class="qi-foot">'+
        // '<button class="btn-outline">View proof docs</button>'+
        // '<div class="qi-actions">'+
        //   '<button class="btn-reject" data-srid="'+sr.id+'"><i class="bi bi-x-lg"></i>Reject</button>'+
        //   '<button class="btn-approve" data-srid="'+sr.id+'"><i class="bi bi-check-lg"></i>Approve QC</button>'+
        // '</div>'+
      '</div>'+
    '</article>';
  }).join('');

  document.querySelectorAll('#qcList .btn-approve').forEach(function(b){
    b.addEventListener('click',function(){
      toast('ok','QC approved','SR '+this.dataset.srid+' approved — moving to next stage.');
    });
  });
  document.querySelectorAll('#qcList .btn-reject').forEach(function(b){
    b.addEventListener('click',function(){openRejectModal(this.dataset.srid);});
  });
}



document.querySelectorAll('#rangeForm .fq-date').forEach(function (input) {
  input.addEventListener('change', function () {
    var f = document.querySelector('#rangeForm [name="from"]').value;
    var t = document.querySelector('#rangeForm [name="to"]').value;
    if (f && t) document.getElementById('rangeForm').submit();
  });
});


/* ══════════════════════════════════════════
   FIELD KANBAN
══════════════════════════════════════════ */
function renderField(){
  var cols=[
    {key:'assigned',label:'Assigned',      note:'ML notified', c:C.blue},
    {key:'eta',     label:'ETA Confirmed', note:'On the way',  c:C.cyan},
    {key:'inprog',  label:'In Progress',   note:'Working',     c:C.amber},
    {key:'review',  label:'Pending Review',note:'Proof sent',  c:C.orange},
  ];
  el('fieldKanban').innerHTML = cols.map(function(col){
    var items = FIELD_SRS[col.key]||[];
    return '<div class="kb-col">'+
      '<div class="kb-hdr" style="--col:'+col.c+';">'+
        '<span style="position:absolute;left:0;top:0;bottom:0;width:4px;background:'+col.c+';border-radius:12px 0 0 0;"></span>'+
        '<span class="kb-hdr-name">'+col.label+'</span>'+
        '<span class="kb-hdr-count" style="background:'+col.c+'1a;color:'+col.c+';">'+items.length+'</span>'+
      '</div>'+
      '<div class="kb-body">'+
        (items.length===0
          ? '<div class="kb-empty">No SRs</div>'
          : items.map(function(sr){
              var cls = sr.sla==='ok'?'sla-ok':sr.sla==='risk'?'sla-risk':'sla-br';
              var lab = sr.sla==='ok'?'On track':sr.sla==='risk'?'At risk':'Breached';
              return '<div class="kb-card">'+
                '<div class="kb-card-top">'+
                  '<span class="kb-id">'+sr.id+'</span>'+
                  '<span class="kb-sla '+cls+'">'+lab+'</span>'+
                '</div>'+
                '<div class="kb-client">'+sr.client+'</div>'+
                '<div class="kb-site"><i class="bi bi-geo-alt" style="color:#9a8053;font-size:.6rem;"></i>'+sr.site+'</div>'+
                '<div class="kb-ml">'+
                  '<span class="kb-ml-av">'+sr.mlInit+'</span>'+sr.ml+
                '</div>'+
                '<div class="kb-time"><i class="bi bi-clock" style="font-size:.58rem;color:#9a8053;"></i>'+sr.time+'</div>'+
              '</div>';
            }).join('')
        )+
      '</div>'+
    '</div>';
  }).join('');
}

/* ══════════════════════════════════════════
   DISPATCH MODAL
══════════════════════════════════════════ */
function openDispatch(srId, reassign){
  var sr = PENDING_SRS.find(function(s){return s.id===srId;}) ||
           REWORK_SRS.find(function(s){return s.id===srId;});
  if(!sr) return;
  currentDispatchSR = sr; selectedML = null;

  el('dmSrInfo').innerHTML =
    '<div class="dm-sr-id">'+sr.id+(sr.attempt?' &nbsp;<span class="pill" style="background:rgba(220,38,38,.1);color:#dc2626;font-size:.62rem;">Rework attempt '+sr.attempt+'</span>':'')+'</div>'+
    '<div class="dm-sr-detail">'+sr.client+' &nbsp;·&nbsp; '+sr.site+'</div>';

  var availMls = reassign
    ? MLS.filter(function(m){return m.init === (sr.mlInit||'');})
    : MLS.filter(function(m){return m.status !== 'offduty';});

  el('dmMlList').innerHTML = availMls.map(function(ml){
    return '<div class="dm-ml-item" data-init="'+ml.init+'">'+
      '<div class="dm-ml-av'+(ml.status==='offduty'||ml.status==='onsite'?' busy':'')+'">'+ml.init+'</div>'+
      '<div style="flex:1;">'+
        '<div class="dm-ml-name">'+ml.name+'</div>'+
        '<div class="dm-ml-domain">'+ml.domain+'</div>'+
      '</div>'+
      '<div class="dm-ml-load" style="color:'+(ml.available?'#15803d':'#d97706')+';">'+
        (ml.available?'✓ Available':'⚡ '+ml.jobs+' active')+
      '</div>'+
    '</div>';
  }).join('');

  document.querySelectorAll('.dm-ml-item').forEach(function(e){
    e.addEventListener('click',function(){
      document.querySelectorAll('.dm-ml-item').forEach(function(x){x.classList.remove('selected');});
      this.classList.add('selected');
      selectedML = this.dataset.init;
    });
  });
  el('dispatchModal').classList.add('open');
}
function closeDispatchModal(){
  el('dispatchModal').classList.remove('open');
  currentDispatchSR=null; selectedML=null;
}
function confirmDispatch(){
  if(!selectedML){toast('warn','Pick a maintenance lead','Select an ML before confirming.');return;}
  var ml = MLS.find(function(m){return m.init===selectedML;});
  toast('ok','SR dispatched',currentDispatchSR.id+' assigned to '+ml.name+'. Status → Assigned.');
  closeDispatchModal();
}

/* ══════════════════════════════════════════
   REJECT MODAL
══════════════════════════════════════════ */
function openRejectModal(srId){
  currentRejectId = srId;
  el('rejectSrId').textContent = srId;
  el('rejectReason').value = '';
  el('rejectModal').classList.add('open');
}
function closeRejectModal(){el('rejectModal').classList.remove('open');currentRejectId='';}
function confirmReject(){
  var reason = el('rejectReason').value.trim();
  if(!reason){toast('warn','Reason required','Add a rejection reason so the ML knows what to fix.');return;}
  toast('err','QC rejected',currentRejectId+' returned to field — ML notified.');
  closeRejectModal();
}

/* ══════════════════════════════════════════
   UTILS
══════════════════════════════════════════ */
function jumpTo(id){
var e=el(id);if(e)e.scrollIntoView({behavior:'smooth',block:'start'});
}

// function setPeriod(e,v){
//   document.querySelectorAll('.fq-pill').forEach(function(p){p.classList.remove('active');});
//   e.classList.add('active');
//   toast('info','Filter applied','Showing: '+v);
// }


function setPeriod(v){
  var u = new URL(window.location);
  u.searchParams.set('period', v);
  window.location = u;
}


function toggleTheme(){
  var h=document.documentElement;
  h.setAttribute('data-bs-theme',h.getAttribute('data-bs-theme')==='dark'?'light':'dark');
}
function toggleSB(){
  el('sidebar').classList.toggle('open');
  el('sbOverlay').classList.toggle('show');
}
function closeSB(){
  el('sidebar').classList.remove('open');
  el('sbOverlay').classList.remove('show');
}
function tick(){var e=el('clk');if(e)e.textContent=new Date().toLocaleTimeString([],{hour:'2-digit',minute:'2-digit',second:'2-digit'});}
setInterval(tick,1000); tick();

function toast(tp,ti,bo){
  var w=el('tw');
  var ic={ok:'bi-check-circle-fill ok',warn:'bi-exclamation-triangle-fill warn',
          info:'bi-info-circle-fill info',err:'bi-x-circle-fill err'};
  var e=document.createElement('div'); e.className='ti';
  e.innerHTML='<i class="bi '+(ic[tp]||ic.info)+'" style="font-size:.95rem;flex-shrink:0;margin-top:1px;"></i>'+
    '<div><p class="ti-t">'+ti+'</p><p class="ti-b">'+bo+'</p></div>';
  w.appendChild(e);
  setTimeout(function(){e.style.transition='opacity .3s';e.style.opacity='0';
    setTimeout(function(){e.remove();},300);},3600);
}

document.querySelectorAll('.modal-overlay').forEach(function(m){
  m.addEventListener('click',function(ev){if(ev.target===this)this.classList.remove('open');});
});
document.addEventListener('keydown',function(ev){
  if(ev.key==='Escape'){document.querySelectorAll('.modal-overlay.open').forEach(function(m){m.classList.remove('open');});}
});

/* ══════════════════════════════════════════
   INIT
══════════════════════════════════════════ */
window.addEventListener('load', function(){
  renderActions();
  renderGauges();
  renderDonut();
  renderComposition();
  renderClientSpread();
  renderTeam();
  renderPending();
  renderRework();
  renderQC();
  renderField();
  toast('ok','Dashboard ready','2 rework SRs need attention before anything else.');
});
</script>
@endpush