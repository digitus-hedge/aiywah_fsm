{{--
|--------------------------------------------------------------------------
| Front Desk Executive Dashboard
|--------------------------------------------------------------------------
| resources/views/front_dashboard.blade.php
|
| Structure, spacing and palette mirror Front_desk_executive_dash.html.
| No placeholder data — every value comes from FrontDashboardController.
| Variable contract is at the bottom of this file.
--}}

@extends('layouts.layout')

@section('title', 'Front Desk Dashboard')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<style>
/* ==========================================================================
   FRONT DESK DASHBOARD
   Everything is scoped to #fdeDash so Bootstrap (.card, .pill …) can't
   override it and nothing leaks into the rest of the app.
   ========================================================================== */

footer.footer { display: none; }

/* Page canvas — matches --bg in the mock-up. */
body { background: #f4f2ef; }
[data-theme="dark"] body,
[data-bs-theme="dark"] body { background: #141210; }

#fdeDash{
  --card:#fff; --card2:#faf9f7; --surface-2:#f4f2ef;
  --border:rgba(0,0,0,.07);
  --shadow:0 2px 20px rgba(0,0,0,.06);
  --shadow-hover:0 8px 32px rgba(0,0,0,.11);
  --text:#1a1614; --muted:#8a8480; --light:#bbb8b4;
  --gold:#9a8053; --gold2:#b8975e; --gold-bg:rgba(154,128,83,.1);
  --ok:#15803d; --warn:#d97706; --danger:#dc2626;
  --blue:#2563eb; --violet:#7c3aed; --ink:#393837;
  --track:rgba(0,0,0,.05);

  /* The layout's own faces win; the mock-up's faces are the fallback. */
  --serif:var(--font-body,'Cormorant Garamond',Georgia,serif);
  --sans:var(--font-header,'SF Pro Display','Inter',system-ui,sans-serif);

  font-family:var(--sans);
  font-size:.875rem;
  color:var(--text);
}
[data-theme="dark"] #fdeDash,
[data-bs-theme="dark"] #fdeDash{
  --card:#1e1b18; --card2:#252220; --surface-2:#252220;
  --border:rgba(255,255,255,.07);
  --shadow:0 2px 20px rgba(0,0,0,.4);
  --shadow-hover:0 8px 32px rgba(0,0,0,.5);
  --text:#e8e0d4; --muted:#7a756e; --light:#4a4540;
  --gold-bg:rgba(154,128,83,.12);
  --track:rgba(255,255,255,.07);
}

#fdeDash *,#fdeDash *::before,#fdeDash *::after{
  box-sizing:border-box;-webkit-tap-highlight-color:transparent;}
#fdeDash a{text-decoration:none;}
#fdeDash .cg,#fdeDash h1,#fdeDash h2,#fdeDash h3,#fdeDash h4,
#fdeDash .greeting,#fdeDash .big-num,#fdeDash .mm-val,#fdeDash .sr-id,
#fdeDash .kb-sr,#fdeDash .dp-sr-id,#fdeDash .donut-center-val,
#fdeDash .stat-big,#fdeDash .rate-big,#fdeDash .rating-big{
  font-family:var(--serif);letter-spacing:-.01em;}
#fdeDash button:focus-visible,#fdeDash a:focus-visible,
#fdeDash [tabindex]:focus-visible{outline:2px solid var(--gold);outline-offset:2px;}

/* ── GREETING ── */
#fdeDash .greet-row{display:flex;align-items:flex-start;justify-content:space-between;
  gap:16px;margin-bottom:22px;flex-wrap:wrap;}
#fdeDash .greeting{font-size:clamp(1.25rem,4vw,1.55rem);font-weight:700;
  color:var(--text);margin-bottom:3px;}
#fdeDash .greet-sub{font-size:.8rem;color:var(--muted);display:flex;
  align-items:center;gap:6px;flex-wrap:wrap;}

/* ── FILTER PILLS ── */
#fdeDash .filter-bar{display:flex;align-items:center;gap:8px;flex-wrap:wrap;}
#fdeDash .fq-pill{padding:6px 13px;border-radius:20px;font-size:.76rem;font-weight:500;
  cursor:pointer;border:1px solid var(--border);background:var(--card);
  color:var(--muted);transition:all .15s;white-space:nowrap;font-family:var(--sans);}
#fdeDash .fq-pill:hover{border-color:var(--gold);color:var(--gold);}
#fdeDash .fq-pill.active{background:var(--gold);color:#fff;border-color:var(--gold);}

/* ── ALERT STRIP ── */
#fdeDash .alert-row{display:flex;gap:9px;margin-bottom:20px;flex-wrap:wrap;}
#fdeDash .a-chip{display:inline-flex;align-items:center;gap:7px;padding:7px 13px;
  border-radius:9px;font-size:.76rem;font-weight:500;border:1px solid;
  background:transparent;cursor:pointer;transition:opacity .15s;}
#fdeDash .a-chip:hover{opacity:.82;}
#fdeDash .a-red{background:rgba(220,38,38,.08);border-color:rgba(220,38,38,.16);color:var(--danger);}
#fdeDash .a-amb{background:rgba(217,119,6,.08);border-color:rgba(217,119,6,.16);color:var(--warn);}
#fdeDash .a-grn{background:rgba(21,128,61,.08);border-color:rgba(21,128,61,.16);color:var(--ok);}

/* ── SECTION LABEL ── */
#fdeDash .sec-row{display:flex;align-items:center;gap:12px;margin:24px 0 14px;}
#fdeDash .sec-line{flex:1;height:1px;background:var(--border);}
#fdeDash .sec-ttl{font-size:.68rem;font-weight:700;text-transform:uppercase;
  letter-spacing:.1em;color:var(--light);white-space:nowrap;
  display:flex;align-items:center;gap:5px;}
#fdeDash .sec-ttl i{color:var(--gold);}

/* ── CARDS ── */
#fdeDash .card{background:var(--card);border:none;border-radius:16px;
  box-shadow:var(--shadow);overflow:hidden;transition:box-shadow .2s,transform .2s;}
#fdeDash .card.clickable{cursor:pointer;}
#fdeDash .card.clickable:hover{box-shadow:var(--shadow-hover);transform:translateY(-2px);}
#fdeDash .card-pad{padding:20px 22px;}
#fdeDash .c-hdr{display:flex;align-items:center;justify-content:space-between;
  margin-bottom:16px;gap:8px;flex-wrap:wrap;}
#fdeDash .c-label{font-size:.7rem;font-weight:700;text-transform:uppercase;
  letter-spacing:.08em;color:var(--muted);display:flex;align-items:center;gap:5px;}
#fdeDash .c-label i{color:var(--gold);}
#fdeDash .c-more{font-size:.72rem;color:var(--gold);display:flex;
  align-items:center;gap:3px;white-space:nowrap;}
#fdeDash .c-more:hover{opacity:.8;}

/* ── GRIDS ── */
#fdeDash .g4{display:grid;grid-template-columns:repeat(4,1fr);gap:14px;}
#fdeDash .g3{display:grid;grid-template-columns:repeat(3,1fr);gap:14px;}
#fdeDash .g2{display:grid;grid-template-columns:1fr 1fr;gap:14px;}
#fdeDash .span2{grid-column:span 2;}
#fdeDash .mb-block{margin-bottom:16px;}

/* ── KPI CARDS ── */
#fdeDash .kpi{background:var(--card);border:none;border-radius:16px;
  padding:18px;box-shadow:var(--shadow);position:relative;overflow:hidden;
  width:100%;text-align:left;font:inherit;color:inherit;cursor:pointer;
  transition:box-shadow .2s,transform .2s;}
#fdeDash .kpi:hover{box-shadow:var(--shadow-hover);transform:translateY(-2px);}
#fdeDash .kpi::after{content:'';position:absolute;right:-18px;top:-18px;
  width:72px;height:72px;border-radius:50%;opacity:.07;}
#fdeDash .k1::after{background:#9a8053;} #fdeDash .k2::after{background:#2563eb;}
#fdeDash .k3::after{background:#dc2626;} #fdeDash .k4::after{background:#15803d;}
#fdeDash .kpi-top{display:flex;align-items:flex-start;justify-content:space-between;
  gap:8px;margin-bottom:10px;}
#fdeDash .kpi-ico{width:36px;height:36px;border-radius:10px;display:flex;
  align-items:center;justify-content:center;font-size:.95rem;flex-shrink:0;}
#fdeDash .i1{background:rgba(154,128,83,.12);color:var(--gold);}
#fdeDash .i2{background:rgba(37,99,235,.1);color:var(--blue);}
#fdeDash .i3{background:rgba(220,38,38,.1);color:var(--danger);}
#fdeDash .i4{background:rgba(21,128,61,.1);color:var(--ok);}
#fdeDash .kpi-delta{font-size:.68rem;padding:2px 7px;border-radius:8px;font-weight:600;
  display:inline-flex;align-items:center;gap:2px;white-space:nowrap;}
#fdeDash .du{background:rgba(21,128,61,.1);color:var(--ok);}
#fdeDash .dd{background:rgba(220,38,38,.1);color:var(--danger);}
#fdeDash .dn{background:var(--surface-2);color:var(--muted);}
#fdeDash .big-num{font-size:2.1rem;font-weight:700;color:var(--text);
  line-height:1;margin-bottom:3px;}
#fdeDash .kpi-lbl{font-size:.73rem;color:var(--muted);font-weight:500;margin-bottom:2px;}
#fdeDash .kpi-sub{font-size:.68rem;color:var(--light);}
#fdeDash .sp-wrap{height:36px;width:100%;position:relative;margin-top:10px;}
#fdeDash .sp-wrap canvas{position:absolute;inset:0;width:100%!important;height:100%!important;}

/* ── CHART CONTAINERS ── */
#fdeDash .ch{position:relative;width:100%;}
#fdeDash .ch-160{height:160px;}
#fdeDash .ch-half{position:relative;width:100%;height:110px;}
#fdeDash .ch-d{position:relative;width:140px;height:140px;flex-shrink:0;}
#fdeDash .ch-d-sm{position:relative;width:110px;height:110px;flex-shrink:0;}
#fdeDash .ch canvas,#fdeDash .ch-half canvas,
#fdeDash .ch-d canvas,#fdeDash .ch-d-sm canvas{
  position:absolute;inset:0;width:100%!important;height:100%!important;}

/* ── TREND HEADLINE NUMBERS ── */
#fdeDash .stat-row{display:flex;gap:20px;margin-bottom:14px;flex-wrap:wrap;}
#fdeDash .stat-lbl{font-size:.68rem;color:var(--muted);margin-bottom:2px;}
#fdeDash .stat-big{font-size:1.6rem;font-weight:700;line-height:1;color:var(--text);}
#fdeDash .leg-row{display:flex;gap:14px;margin-top:10px;flex-wrap:wrap;}
#fdeDash .leg{display:flex;align-items:center;gap:6px;font-size:.72rem;color:var(--muted);}
#fdeDash .leg-dot{width:9px;height:9px;border-radius:3px;flex-shrink:0;display:inline-block;}

/* ── STATUS DISTRIBUTION ── */
#fdeDash .sd-row{display:flex;align-items:center;gap:10px;margin-bottom:8px;
  width:100%;background:none;border:0;padding:4px 6px;border-radius:8px;
  font:inherit;color:inherit;text-align:left;cursor:pointer;transition:background .12s;}
#fdeDash .sd-row:hover{background:var(--gold-bg);}
#fdeDash .sd-row:last-child{margin-bottom:0;}
#fdeDash .sd-lbl{font-size:.72rem;color:var(--muted);width:120px;flex-shrink:0;
  white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
#fdeDash .sd-track{flex:1;height:7px;background:var(--track);border-radius:4px;overflow:hidden;}
#fdeDash .sd-fill{height:100%;width:0;border-radius:4px;display:block;
  transition:width 1s cubic-bezier(.4,0,.2,1);}
#fdeDash .sd-n{font-size:.72rem;font-weight:600;color:var(--text);
  min-width:22px;text-align:right;flex-shrink:0;}

/* ── TRIAGE TABLE ── */
#fdeDash .tbl-scroll{overflow-x:auto;-webkit-overflow-scrolling:touch;}
#fdeDash .triage-tbl{width:100%;min-width:520px;border-collapse:collapse;}
#fdeDash .triage-tbl thead th{padding:9px 14px;font-size:.67rem;font-weight:700;
  text-align:left;text-transform:uppercase;letter-spacing:.07em;color:var(--muted);
  background:var(--card2);border-bottom:1px solid var(--border);white-space:nowrap;}
#fdeDash .triage-tbl tbody tr{border-bottom:1px solid var(--border);transition:background .1s;}
#fdeDash .triage-tbl tbody tr:last-child{border-bottom:none;}
#fdeDash .triage-tbl tbody tr:hover{background:var(--gold-bg);}
#fdeDash .triage-tbl td{padding:10px 14px;font-size:.79rem;vertical-align:middle;}
#fdeDash .sr-id{font-size:.9rem;font-weight:700;color:var(--gold);}

/* ── PILLS ── */
#fdeDash .pill{font-size:.65rem;font-weight:700;padding:2px 8px;border-radius:20px;
  display:inline-flex;align-items:center;gap:3px;}
#fdeDash .p-red{background:rgba(220,38,38,.1);color:var(--danger);}
#fdeDash .p-amber{background:rgba(217,119,6,.1);color:var(--warn);}
#fdeDash .p-green{background:rgba(21,128,61,.1);color:var(--ok);}
#fdeDash .p-gold{background:var(--gold-bg);color:var(--gold);}
#fdeDash .p-muted{background:var(--surface-2);color:var(--muted);}

/* ── CLIENT RANK LIST ── */
#fdeDash .client-row{display:flex;align-items:center;gap:10px;padding:8px;
  width:100%;background:none;border:0;border-bottom:1px solid var(--border);
  border-radius:7px;font:inherit;color:inherit;text-align:left;cursor:pointer;
  transition:background .1s;}
#fdeDash .client-row:last-child{border-bottom:none;}
#fdeDash .client-row:hover{background:var(--gold-bg);}
#fdeDash .c-rank{width:22px;height:22px;border-radius:6px;background:var(--gold-bg);
  display:flex;align-items:center;justify-content:center;font-size:.6rem;
  font-weight:700;color:var(--gold);flex-shrink:0;}
#fdeDash .c-name{font-size:.8rem;font-weight:500;color:var(--text);
  white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
#fdeDash .c-count{font-size:.82rem;font-weight:700;color:var(--text);}
#fdeDash .c-sub{font-size:.68rem;color:var(--muted);}

/* ── MINI METRIC ── */
#fdeDash .mm{background:var(--card2);border-radius:10px;padding:12px 14px;
  width:100%;text-align:left;font:inherit;color:inherit;
  transition:background .12s,box-shadow .12s;}
#fdeDash .mm.clickable{cursor:pointer;}
#fdeDash .mm.clickable:hover{background:var(--gold-bg);}
#fdeDash .mm-lbl{font-size:.68rem;color:var(--muted);font-weight:500;margin-bottom:4px;}
#fdeDash .mm-val{font-size:1.5rem;font-weight:700;color:var(--text);line-height:1;}
#fdeDash .mm-sub{font-size:.68rem;color:var(--muted);margin-top:3px;}
#fdeDash .mm-grid{display:grid;grid-template-columns:1fr 1fr;gap:8px;}

/* ── DONUTS ── */
#fdeDash .donut-wrap{display:flex;align-items:center;gap:16px;flex-wrap:wrap;}
#fdeDash .donut-center{position:absolute;inset:0;display:flex;flex-direction:column;
  align-items:center;justify-content:center;pointer-events:none;}
#fdeDash .donut-center-val{font-size:1.4rem;font-weight:700;color:var(--text);line-height:1;}
#fdeDash .donut-center-lbl{font-size:.6rem;color:var(--muted);}
#fdeDash .donut-leg{display:flex;flex-direction:column;gap:8px;flex:1;min-width:120px;}
#fdeDash .leg-r{display:flex;align-items:center;gap:8px;width:100%;
  background:none;border:0;padding:4px 2px;border-radius:6px;font:inherit;
  color:inherit;text-align:left;cursor:pointer;transition:background .12s;}
#fdeDash .leg-r:hover{background:var(--gold-bg);}
#fdeDash .leg-n{font-size:.74rem;color:var(--muted);flex:1;}
#fdeDash .leg-v{font-size:.8rem;font-weight:600;color:var(--text);}

#fdeDash .pnl-item{padding:12px 14px;border-radius:10px;border:1px solid var(--border);
  margin-bottom:9px;background:var(--card2);cursor:pointer;transition:all .15s;}
#fdeDash .pnl-item:last-child{margin-bottom:0;}
#fdeDash .pnl-item:hover{border-color:var(--gold);background:var(--gold-bg);}
#fdeDash .pnl-item-top{display:flex;align-items:center;justify-content:space-between;
  margin-bottom:5px;gap:8px;}
#fdeDash .pnl-item-id{font-size:.85rem;font-weight:700;color:var(--gold);font-family:var(--serif);}
#fdeDash .pnl-badge{font-size:.65rem;font-weight:700;padding:2px 8px;border-radius:20px;}
#fdeDash .pnl-client{font-size:.8rem;font-weight:500;color:var(--text);margin-bottom:2px;}
#fdeDash .pnl-meta{font-size:.72rem;color:var(--muted);display:flex;align-items:center;gap:6px;}

#fdeDash .triage-tbl tbody tr[data-sr-code]{cursor:pointer;}

/* ── CANCELLATION ── */
#fdeDash .rate-big{font-size:2.6rem;font-weight:700;color:var(--danger);line-height:1;}
#fdeDash .cancel-item{background:var(--card2);border:1px solid var(--border);
  border-radius:9px;padding:10px 12px;width:100%;text-align:left;font:inherit;
  color:inherit;cursor:pointer;transition:background .12s,border-color .12s;}
#fdeDash .cancel-item:hover{background:var(--gold-bg);border-color:var(--gold);}

/* ── WHATSAPP FAILURES ── */
#fdeDash .wa-fail{background:rgba(220,38,38,.05);border:1px solid rgba(220,38,38,.12);
  border-radius:8px;padding:9px 11px;margin-bottom:7px;width:100%;text-align:left;
  font:inherit;color:inherit;cursor:pointer;transition:background .12s;}
#fdeDash .wa-fail:hover{background:rgba(220,38,38,.09);}
#fdeDash .wa-sub-ttl{font-size:.68rem;font-weight:700;text-transform:uppercase;
  letter-spacing:.08em;color:var(--light);margin-bottom:8px;}

/* ── EMPTY STATE ── */
#fdeDash .empty{padding:22px 14px;text-align:center;color:var(--muted);font-size:.78rem;margin:0;}
#fdeDash .empty i{display:block;font-size:1.25rem;color:var(--light);margin-bottom:6px;}

/* ── KANBAN ── */
#fdeDash .kb-board{display:flex;gap:10px;overflow-x:auto;padding-bottom:10px;}
#fdeDash .kb-board::-webkit-scrollbar{height:5px;}
#fdeDash .kb-board::-webkit-scrollbar-thumb{background:var(--border);border-radius:3px;}
#fdeDash .kb-board::-webkit-scrollbar-thumb:hover{background:var(--gold);}
#fdeDash .kb-col{flex-shrink:0;width:210px;display:flex;flex-direction:column;}
#fdeDash .kb-col-hdr{display:flex;align-items:center;gap:7px;padding:8px 10px;
  border-radius:9px 9px 0 0;font-size:.73rem;font-weight:700;}
#fdeDash .kb-col-body{flex:1;background:rgba(0,0,0,.025);border-radius:0 0 9px 9px;
  padding:7px;display:flex;flex-direction:column;gap:7px;min-height:60px;}
[data-theme="dark"] #fdeDash .kb-col-body,
[data-bs-theme="dark"] #fdeDash .kb-col-body{background:rgba(255,255,255,.025);}
#fdeDash .kb-card{background:var(--card);border:1px solid var(--border);border-radius:9px;
  padding:10px 11px;cursor:pointer;transition:transform .15s,box-shadow .15s;
  position:relative;overflow:hidden;text-align:left;width:100%;}
#fdeDash .kb-card:hover{transform:translateY(-2px);box-shadow:0 4px 16px rgba(0,0,0,.1);}
#fdeDash .kb-sr{font-size:.82rem;font-weight:700;color:var(--gold);margin-bottom:3px;}
#fdeDash .kb-client{font-size:.76rem;font-weight:600;color:var(--text);margin-bottom:2px;
  white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
#fdeDash .kb-site{font-size:.67rem;color:var(--muted);white-space:nowrap;
  overflow:hidden;text-overflow:ellipsis;margin-bottom:6px;}
#fdeDash .kb-foot{display:flex;align-items:center;justify-content:space-between;gap:4px;}
#fdeDash .kb-empty{font-size:.72rem;color:var(--light);text-align:center;
  padding:16px 8px;display:flex;flex-direction:column;align-items:center;gap:4px;}

/* ── SLIDE-IN DETAIL PANEL ── */
#fdeDash .panel-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.38);
  z-index:2000;backdrop-filter:blur(3px);}
#fdeDash .panel-overlay.open{display:block;}
#fdeDash .detail-panel{position:fixed;top:0;right:0;width:480px;max-width:96vw;
  height:100vh;height:100dvh;background:var(--card);z-index:2010;
  box-shadow:-4px 0 40px rgba(0,0,0,.15);transform:translateX(105%);
  transition:transform .32s cubic-bezier(.4,0,.2,1);
  display:flex;flex-direction:column;overflow:hidden;}
#fdeDash .detail-panel.open{transform:translateX(0);}
#fdeDash .dp-hdr{padding:18px 22px 16px;border-bottom:1px solid var(--border);
  flex-shrink:0;background:linear-gradient(135deg,#9a8053,#b8975e);}
#fdeDash .dp-hdr-top{display:flex;align-items:flex-start;justify-content:space-between;
  gap:12px;margin-bottom:10px;}
#fdeDash .dp-sr-id{font-size:1.3rem;font-weight:700;color:#fff;line-height:1;}
#fdeDash .dp-close{background:rgba(255,255,255,.2);border:1px solid rgba(255,255,255,.3);
  color:#fff;width:30px;height:30px;border-radius:7px;display:flex;
  align-items:center;justify-content:center;cursor:pointer;font-size:.85rem;flex-shrink:0;}
#fdeDash .dp-close:hover{background:rgba(255,255,255,.35);}
#fdeDash .dp-chips{display:flex;align-items:center;gap:7px;flex-wrap:wrap;}
#fdeDash .dp-chip{font-size:.72rem;font-weight:600;padding:3px 10px;border-radius:20px;
  background:rgba(255,255,255,.18);color:#fff;border:1px solid rgba(255,255,255,.25);
  display:inline-flex;align-items:center;gap:4px;}
#fdeDash .dp-body{flex:1;overflow-y:auto;}
#fdeDash .dp-body::-webkit-scrollbar{width:4px;}
#fdeDash .dp-body::-webkit-scrollbar-thumb{background:var(--border);border-radius:2px;}
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
#fdeDash .dp-cell-val.gold{color:var(--gold);font-family:var(--serif);}
#fdeDash .dp-cell-val.mono{font-family:ui-monospace,SFMono-Regular,Menlo,monospace;font-size:.74rem;}
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
#fdeDash .rating-stars i{color:#f59e0b;font-size:.95rem;}
#fdeDash .rating-big{font-size:1.6rem;font-weight:700;color:var(--gold);}

/* ── TOASTS ── */
#fdeDash .toast-wrap{position:fixed;bottom:22px;right:22px;z-index:900;
  display:flex;flex-direction:column;gap:8px;pointer-events:none;
  max-width:calc(100vw - 44px);}
#fdeDash .ti{display:flex;align-items:flex-start;gap:9px;padding:11px 14px;border-radius:10px;
  background:var(--card);border:1px solid var(--border);box-shadow:var(--shadow);
  min-width:220px;max-width:290px;animation:fdeToastIn .2s ease;pointer-events:auto;}
@keyframes fdeToastIn{from{opacity:0;transform:translateY(8px);}to{opacity:1;transform:none;}}
#fdeDash .ti-t{font-size:.79rem;font-weight:600;color:var(--text);margin:0 0 2px;}
#fdeDash .ti-b{font-size:.73rem;color:var(--muted);margin:0;}
#fdeDash .ok{color:var(--ok);} #fdeDash .warn{color:var(--warn);} #fdeDash .info{color:var(--gold);}

/* ── RESPONSIVE ── */
@media (max-width:1100px){
  #fdeDash .g4{grid-template-columns:repeat(2,1fr);}
  #fdeDash .g3{grid-template-columns:1fr 1fr;}
  #fdeDash .span2{grid-column:span 2;}
}
@media (max-width:640px){
  #fdeDash .g4,#fdeDash .g3,#fdeDash .g2{grid-template-columns:1fr;}
  #fdeDash .span2{grid-column:span 1;}
  #fdeDash .card-pad{padding:16px;}
  #fdeDash .greet-row{flex-direction:column;gap:14px;}
  #fdeDash .filter-bar{width:100%;overflow-x:auto;flex-wrap:nowrap;padding-bottom:4px;}
  #fdeDash .filter-bar > *{flex:0 0 auto;}
  #fdeDash .toast-wrap{left:14px;right:14px;bottom:14px;}
  #fdeDash .ti{max-width:100%;}
}
@media (max-width:420px){
  #fdeDash .sd-lbl{width:92px;}
}
@media (prefers-reduced-motion:reduce){
  #fdeDash *,#fdeDash *::before,#fdeDash *::after{
    transition-duration:.01ms!important;animation-duration:.01ms!important;}
}
</style>
@endpush

@section('content')
@php
    // Defensive defaults so the view never breaks on a partial payload.
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
    $periodOptions   = $periodOptions   ?? ['month' => 'This Month', 'week' => 'This Week', 'today' => 'Today'];

    $maxStatus   = collect($statusBreakdown)->max(fn ($r) => (int) data_get($r, 'count')) ?: 1;
    $priorityCls = ['High' => 'p-red', 'Medium' => 'p-amber', 'Low' => 'p-green'];
    $kpiListMap = [
        'total'     => ['list' => 'all',             'title' => 'All SRs · '.($periodLabel ?? 'This period')],
        'pending'   => ['list' => 'triage-backlog',  'title' => 'Pending triage queue'],
        'cancelled' => ['list' => 'cancelled',        'title' => 'Cancelled / rejected SRs'],
        'completed' => ['list' => 'completed',        'title' => 'Completed SRs'],
    ];

    $kpiCards = [
        ['key' => 'total',     'ico' => 'bi-ticket-perforated', 'n' => 1, 'lbl' => 'SRs Logged · '.($periodLabel ?? 'This Month')],
        ['key' => 'pending',   'ico' => 'bi-hourglass-split',   'n' => 2, 'lbl' => 'Pending Review'],
        ['key' => 'cancelled', 'ico' => 'bi-x-circle',          'n' => 3, 'lbl' => 'Cancelled from My Intake'],
        ['key' => 'completed', 'ico' => 'bi-patch-check',       'n' => 4, 'lbl' => 'Completed from My Intake'],
    ];


  

@endphp

<div id="fdeDash">

    {{-- ── GREETING + PERIOD FILTER ───────────────────────────────────── --}}
    <div class="greet-row">
        <div>
            <div class="greeting">{{ $greeting ?? 'Front Desk Dashboard' }}</div>
            <div class="greet-sub">
                <i class="bi bi-calendar3" style="color:var(--gold);"></i>
                {{ $today ?? now()->format('l, d F Y') }}
                @isset($greetingSub)
                    <span>&nbsp;·&nbsp; {{ $greetingSub }}</span>
                @endisset
            </div>
        </div>

        {{-- GET form so the pills actually re-run the queries. --}}
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
                <button type="button" class="a-chip a-red" data-list="triage-backlog" data-list-title="Pending triage queue">
                <i class="bi bi-hourglass-split"></i>
                {{ $alertCounts['triage'] }} {{ \Illuminate\Support\Str::plural('SR', $alertCounts['triage']) }} awaiting HoP triage — logged by you
            </button>
        @endif

        @if (($alertCounts['wa_failures'] ?? 0) > 0)
            <button type="button" class="a-chip a-amb" data-list="wa-failures" data-list-title="Failed WhatsApp confirmations">
                <i class="bi bi-whatsapp"></i>
                {{ $alertCounts['wa_failures'] }} WhatsApp {{ \Illuminate\Support\Str::plural('confirmation', $alertCounts['wa_failures']) }} failed on your SRs
            </button>
        @endif

        @if (($alertCounts['triage'] ?? 0) === 0 && ($alertCounts['wa_failures'] ?? 0) === 0)
            <span class="a-chip a-grn">
                <i class="bi bi-check-circle-fill"></i>
                Nothing needs your attention right now
            </span>
        @endif
    </div>

    {{-- ── KPI ROW ────────────────────────────────────────────────────── --}}
    <div class="g4 mb-block" style="margin-bottom:20px;">
        @foreach ($kpiCards as $i => $c)
            <button type="button" class="kpi k{{ $c['n'] }}"
                    data-list="{{ $kpiListMap[$c['key']]['list'] }}"
                    data-list-title="{{ $kpiListMap[$c['key']]['title'] }}">
                <div class="kpi-top">
                    <span class="kpi-ico i{{ $c['n'] }}"><i class="bi {{ $c['ico'] }}"></i></span>
                    @if (data_get($kpis, $c['key'].'.delta'))
                        <span class="kpi-delta {{ data_get($kpis, $c['key'].'.delta_tone', 'dn') }}">{{ data_get($kpis, $c['key'].'.delta') }}</span>
                    @endif
                </div>
                <div class="big-num">{{ data_get($kpis, $c['key'].'.value', 0) }}</div>
                <div class="kpi-lbl">{{ $c['lbl'] }}</div>
                <div class="kpi-sub">{{ data_get($kpis, $c['key'].'.sub') }}</div>
                    <div class="sp-wrap"><canvas id="fdeSpark{{ $i }}"></canvas></div>
            </button>
        @endforeach
    </div>

    {{-- ── SECTION 1 · STATUS TRACKER + TRIAGE QUEUE ──────────────────── --}}
    <div class="sec-row">
        <span class="sec-line"></span>
        <span class="sec-ttl"><i class="bi bi-bar-chart-steps"></i>My SR status tracker &amp; triage queue</span>
        <span class="sec-line"></span>
    </div>

    <div class="g2 mb-block">

        {{-- Status distribution --}}
        <div class="card card-pad">
            <div class="c-hdr">
                <div class="c-label"><i class="bi bi-bar-chart-steps"></i>My SRs by status</div>
                @if (Route::has('kanban_view'))
                    <a href="{{ route('kanban_view') }}" class="c-more">Kanban view <i class="bi bi-arrow-right"></i></a>
                @endif
            </div>

            <div>
                               @forelse ($statusBreakdown as $row)
                    <button type="button" class="sd-row"
                            data-list="{{ data_get($row, 'key') }}"
                            data-list-title="{{ data_get($row, 'label') }} · SRs">
                        <span class="sd-lbl" title="{{ data_get($row, 'label') }}">{{ data_get($row, 'label') }}</span>
                        <span class="sd-track">
                            <span class="sd-fill"
                                  data-width="{{ round((int) data_get($row, 'count') / $maxStatus * 100) }}%"
                                  style="background:{{ data_get($row, 'color', '#9a8053') }};"></span>
                        </span>
                        <span class="sd-n">{{ data_get($row, 'count') }}</span>
                    </button>
                @empty
                    <p class="empty"><i class="bi bi-inbox"></i>No service requests in this period.</p>
                @endforelse
            </div>
        </div>

        {{-- Triage queue --}}
        <div class="card">
            <div class="card-pad" style="padding-bottom:10px;">
                <div class="c-hdr" style="margin-bottom:8px;">
                    <div class="c-label"><i class="bi bi-hourglass-split"></i>Pending triage queue</div>
                    <span class="pill p-red">{{ $alertCounts['triage'] ?? 0 }} awaiting HoP</span>
                </div>
                <div style="font-size:.73rem;color:var(--muted);">
                    SRs you logged that HoP hasn't reviewed yet — oldest first.
                </div>
            </div>

            <div class="tbl-scroll">
                <table class="triage-tbl">
                    <thead>
                        <tr>
                            <th style="padding-left:18px;">SR ID</th>
                            <th>Client</th>
                            <th>Category</th>
                            <th>Priority</th>
                            <th>Waiting</th>
                        </tr>
                    </thead>
                    <tbody>
                      @forelse ($triage as $row)
                            <tr data-sr-code="{{ data_get($row, 'code') }}">
                                <td style="padding-left:18px;"><span class="sr-id">{{ data_get($row, 'code') }}</span></td>
                                <td style="font-weight:500;color:var(--text);">{{ data_get($row, 'client') }}</td>
                                <td style="font-size:.76rem;color:var(--muted);">{{ data_get($row, 'category') }}</td>
                                <td><span class="pill {{ $priorityCls[data_get($row, 'priority')] ?? 'p-muted' }}">{{ data_get($row, 'priority') }}</span></td>
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
                                <td colspan="5"><p class="empty"><i class="bi bi-inbox"></i>Nothing waiting on triage.</p></td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ── SECTION 2 · INTAKE VOLUME ──────────────────────────────────── --}}
    <div class="sec-row">
        <span class="sec-line"></span>
        <span class="sec-ttl"><i class="bi bi-graph-up"></i>My intake volume</span>
        <span class="sec-line"></span>
    </div>

    <div class="g3 mb-block">

        {{-- Monthly trend --}}
        <div class="card card-pad span2">
            <div class="c-hdr">
                <div class="c-label"><i class="bi bi-graph-up-arrow"></i>Monthly SR intake trend</div>
                <span style="font-size:.7rem;color:var(--muted);">6 months</span>
            </div>

            <div class="stat-row">
                <div>
                    <div class="stat-lbl">{{ $periodLabel ?? 'This Month' }}</div>
                    <div class="stat-big" style="color:var(--gold);">{{ data_get($intakeStats, 'total', 0) }}</div>
                </div>
                <div>
                    <div class="stat-lbl">Avg / day</div>
                    <div class="stat-big">{{ data_get($intakeStats, 'perDay', 0) }}</div>
                </div>
                <div>
                    <div class="stat-lbl">Today</div>
                    <div class="stat-big">{{ data_get($intakeStats, 'today', 0) }}</div>
                </div>
            </div>

            <div class="ch ch-160"><canvas id="fdeTrend"></canvas></div>

            <div class="leg-row">
                <span class="leg"><span class="leg-dot" style="background:#9a8053;"></span>In-warranty</span>
                <span class="leg"><span class="leg-dot" style="background:#393837;"></span>Out-of-warranty</span>
            </div>
        </div>

        {{-- Scope split --}}
        <div class="card card-pad">
            <div class="c-hdr" style="margin-bottom:12px;">
                <div class="c-label"><i class="bi bi-pie-chart"></i>Scope split</div>
            </div>

            <div class="donut-wrap" style="flex-direction:column;align-items:center;gap:14px;">
                <div class="ch-d">
                    <canvas id="fdeScope"></canvas>
                    <div class="donut-center">
                        <div class="donut-center-val">{{ data_get($scope, 'total', 0) }}</div>
                        <div class="donut-center-lbl">Total</div>
                    </div>
                </div>
                <div class="donut-leg" style="width:100%;">
                    <button type="button" class="leg-r" data-list="scope" data-scope="IW" data-list-title="In-warranty SRs">
                        <span class="leg-dot" style="background:#9a8053;"></span>
                        <span class="leg-n">In-warranty</span>
                        <span class="leg-v" style="color:var(--gold);">{{ data_get($scope, 'iw', 0) }}</span>
                    </button>
                    <button type="button" class="leg-r" data-list="scope" data-scope="OoW" data-list-title="Out-of-warranty SRs">
                        <span class="leg-dot" style="background:#393837;"></span>
                        <span class="leg-n">Out-of-warranty</span>
                        <span class="leg-v">{{ data_get($scope, 'oow', 0) }}</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ── SECTION 3 · CLIENT ACTIVITY & NOTIFICATIONS ────────────────── --}}
    <div class="sec-row">
        <span class="sec-line"></span>
        <span class="sec-ttl"><i class="bi bi-buildings"></i>Client activity &amp; notifications</span>
        <span class="sec-line"></span>
    </div>

    <div class="g3 mb-block">

        {{-- Top clients --}}
        <div class="card card-pad">
            <div class="c-hdr">
                <div class="c-label"><i class="bi bi-buildings"></i>Top clients — my intake</div>
                @if (Route::has('clients.directory'))
                    <a href="{{ route('clients.directory') }}" class="c-more">All clients <i class="bi bi-arrow-right"></i></a>
                @endif
            </div>

            <div style="margin-bottom:14px;">
                               @forelse ($clients as $client)
                    <button type="button" class="client-row"
                            data-list="client" data-client-id="{{ data_get($client, 'id') }}"
                            data-list-title="{{ data_get($client, 'name') }} · SRs">
                        <span class="c-rank">{{ $loop->iteration }}</span>
                        <span style="flex:1;min-width:0;">
                            <span class="c-name" style="display:block;">{{ data_get($client, 'name') }}</span>
                            @if (data_get($client, 'is_new'))
                                <span class="c-sub" style="color:var(--gold);">New client this period</span>
                            @endif
                        </span>
                        <span style="text-align:right;">
                            <span class="c-count" style="display:block;">{{ data_get($client, 'srs') }}</span>
                            <span class="c-sub">SRs</span>
                        </span>
                    </button>
                @empty
                    <p class="empty"><i class="bi bi-building"></i>No client activity in this period.</p>
                @endforelse
            </div>

            <div class="mm-grid" style="padding-top:12px;border-top:1px solid var(--border);">
                <div class="mm">
                    <div class="mm-lbl">New clients</div>
                    <div class="mm-val" style="color:var(--gold);">{{ data_get($clientStats, 'new_clients', 0) }}</div>
                    <div class="mm-sub">Accounts I created</div>
                </div>
                <div class="mm">
                    <div class="mm-lbl">New sites</div>
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
                    <div class="rate-big">{{ data_get($cancellation, 'rate', 0) }}%</div>
                    <div style="font-size:.72rem;color:var(--muted);margin-top:3px;">
                        Cancellation rate<br>this period
                    </div>
                </div>
                <div style="flex:1;min-width:130px;">
                    <div class="ch-half"><canvas id="fdeCancelGauge"></canvas></div>
                    <div style="text-align:center;margin-top:-10px;font-size:.68rem;color:var(--muted);">
                        {{ data_get($cancellation, 'count', 0) }} of {{ data_get($cancellation, 'total', 0) }} SRs
                    </div>
                </div>
            </div>

            <div style="display:flex;flex-direction:column;gap:8px;">
                @forelse (data_get($cancellation, 'items', []) as $item)
                    <button type="button" class="cancel-item"
                            data-sr-code="{{ data_get($item, 'code') }}"
                            data-sr-client="{{ data_get($item, 'client') }}"
                            data-sr-meta="{{ data_get($item, 'reason') }}">
                        <div style="font-size:.67rem;color:var(--muted);margin-bottom:3px;">Cancelled SR from my intake</div>
                        <div style="display:flex;align-items:center;justify-content:space-between;gap:8px;flex-wrap:wrap;">
                            <span class="sr-id" style="font-size:.85rem;">{{ data_get($item, 'code') }}</span>
                            <span class="pill p-muted">{{ data_get($item, 'client') }} · {{ data_get($item, 'reason') }}</span>
                        </div>
                    </button>
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

            <div class="donut-wrap" style="margin-bottom:14px;">
                <div class="ch-d-sm">
                    <canvas id="fdeWaDonut"></canvas>
                    <div class="donut-center">
                        <div class="donut-center-val" style="font-size:1.2rem;color:var(--gold);">{{ data_get($whatsapp, 'delivery_rate', 0) }}%</div>
                        <div class="donut-center-lbl">Delivered</div>
                    </div>
                </div>
                <div class="donut-leg">
                    <div class="leg-r">
                        <span class="leg-dot" style="background:#9a8053;"></span>
                        <span class="leg-n">Delivered</span>
                        <span class="leg-v" style="color:var(--ok);">{{ data_get($whatsapp, 'delivered', 0) }}</span>
                    </div>
                    <div class="leg-r">
                        <span class="leg-dot" style="background:#dc2626;opacity:.5;"></span>
                        <span class="leg-n">Failed</span>
                        <span class="leg-v" style="color:var(--danger);">{{ data_get($whatsapp, 'failed', 0) }}</span>
                    </div>
                </div>
            </div>

            <div class="wa-sub-ttl">Failed — needs manual follow-up</div>
            @forelse (data_get($whatsapp, 'failedList', []) as $failure)
                <button type="button" class="wa-fail"
                        data-sr-code="{{ data_get($failure, 'code') }}"
                        data-sr-client="{{ data_get($failure, 'client') }}"
                        data-sr-meta="{{ data_get($failure, 'reason') }}">
                    <div style="display:flex;align-items:center;justify-content:space-between;gap:6px;margin-bottom:3px;">
                        <span class="sr-id" style="font-size:.82rem;">{{ data_get($failure, 'code') }}</span>
                        <span class="pill p-red" style="font-size:.62rem;">Failed</span>
                    </div>
                    <div style="font-size:.74rem;color:var(--muted);">{{ data_get($failure, 'client') }} &middot; {{ data_get($failure, 'reason') }}</div>
                </button>
            @empty
                <p class="empty">
                    <i class="bi bi-{{ data_get($whatsapp, 'available') ? 'check2-circle' : 'plug' }}"></i>
                    {{ data_get($whatsapp, 'available') ? 'All confirmations delivered.' : 'WhatsApp log not wired up yet.' }}
                </p>
            @endforelse
        </div>
    </div>

    {{-- ── SECTION 4 · LIVE KANBAN ────────────────────────────────────── --}}
    <div class="sec-row">
        <span class="sec-line"></span>
        <span class="sec-ttl"><i class="bi bi-kanban"></i>My SR kanban — live board</span>
        <span class="sec-line"></span>
    </div>

    <div class="card mb-block">
        <div class="card-pad" style="padding-bottom:10px;">
            <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;">
                <div class="c-label"><i class="bi bi-kanban"></i>SRs you logged — across all stages</div>
                @if (Route::has('kanban_view'))
                    <a href="{{ route('kanban_view') }}" class="pill p-gold" style="padding:5px 12px;font-size:.73rem;">
                        <i class="bi bi-fullscreen"></i> Open full board
                    </a>
                @endif
            </div>
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
    // commas, so any expression containing a comma has to be a variable first.
    $fdeChartData = [
        'panelUrl' => $panelUrl ?? null,
        'period'   => $filters['period'] ?? 'month',
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
    function isDark() {
        const html = document.documentElement;
        return html.getAttribute('data-theme') === 'dark'
            || html.getAttribute('data-bs-theme') === 'dark';
    }

    function palette() {
        const dark = isDark();
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

        /* KPI sparklines — gold / blue / red / green, matching each card */
        (DATA.sparks || []).forEach((series, i) => {
            const canvas = document.getElementById('fdeSpark' + i);
            if (!canvas || !series || !series.length) return;
            const color = ['#9a8053', '#2563eb', '#dc2626', '#15803d'][i % 4];
            const grad  = canvas.getContext('2d').createLinearGradient(0, 0, 0, 36);
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

        /* Monthly intake trend */
        const trendEl = document.getElementById('fdeTrend');
        if (trendEl && DATA.trend && DATA.trend.labels) {
            const ctx = trendEl.getContext('2d');
            const gIw = ctx.createLinearGradient(0, 0, 0, 160);
            gIw.addColorStop(0, 'rgba(154,128,83,.38)');
            gIw.addColorStop(1, 'rgba(154,128,83,0)');
            const gOw = ctx.createLinearGradient(0, 0, 0, 160);
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
                        y: { grid: { color: C.grid }, ticks: { color: C.muted, font: { size: 11 }, precision: 0 }, beginAtZero: true },
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
                        backgroundColor: [GOLD, 'rgba(220,38,38,.3)'],
                        borderWidth: 0, hoverOffset: 4,
                    }],
                },
                options: {
                    responsive: true, maintainAspectRatio: false, cutout: '70%',
                    plugins: { legend: { display: false }, tooltip: tooltipStyle },
                },
            });
        }
    }

    /* Re-tint charts when the app's theme toggle flips */
    function refreshCharts() {
        const C = palette();
        Object.values(charts).forEach((chart) => {
            if (!chart || !chart.options || !chart.options.scales) return;
            ['x', 'y'].forEach((axis) => {
                const scale = chart.options.scales[axis];
                if (!scale) return;
                if (scale.grid && scale.grid.color) scale.grid.color = C.grid;
                if (scale.ticks) scale.ticks.color = C.muted;
            });
            chart.update('none');
        });
    }

    new MutationObserver(refreshCharts).observe(document.documentElement, {
        attributes: true, attributeFilter: ['data-theme', 'data-bs-theme'],
    });

    /* ------------------------------------------------------------ toast */
    function toast(tone, title, body) {
        const wrap = document.getElementById('fdeToasts');
        if (!wrap) return;
        const icons = {
            ok  : 'bi-check-circle-fill ok',
            warn: 'bi-exclamation-triangle-fill warn',
            info: 'bi-info-circle-fill info',
        };
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
                  +     '<span class="pill" style="background:' + (PRIORITY_BG[card.priority] || 'rgba(0,0,0,.06)') + ';color:' + (PRIORITY_COLOR[card.priority] || '#8a8480') + ';font-size:.58rem;">' + esc(card.priority) + '</span>'
                  +     '<span class="pill" style="background:' + (SCOPE_BG[card.scope] || 'rgba(0,0,0,.06)') + ';color:' + (SCOPE_COLOR[card.scope] || '#8a8480') + ';font-size:.58rem;">' + esc(card.scope) + '</span>'
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
        if (card.scope === 'OoW') events.push({ dot: '#7c3aed', event: 'Routed to Accounts — out-of-warranty scope confirmed', time: 'HoP reviewed' });
        if (card.erp)      events.push({ dot: '#9a8053', event: 'Quote uploaded · ' + card.erp, time: 'Awaiting client approval' });
        if (card.tech)     events.push({ dot: '#2563eb', event: 'Assigned to ' + card.tech, time: 'Dispatched by HoP' });
        if (card.punched)  events.push({ dot: '#d97706', event: 'Technician punched in on-site', time: card.punched });
        if (card.punchout) events.push({ dot: '#ea580c', event: 'Punch-out — submitted for QC review', time: card.punchout });
        if (card.amount && !card.invoice) events.push({ dot: '#7c3aed', event: 'QC approved — invoice pending upload', time: 'Est. ' + card.amount });
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

        
        /* --------------------------------------------------- list/mini panels */
    function allCards() {
        return (DATA.kanban || []).flatMap(function (col) {
            return (col.cards || []).map(function (c) {
                return Object.assign({}, c, { stageKey: col.key, stageLabel: col.label, stageColor: col.color });
            });
        });
    }

    function findCardByCode(code) {
        return allCards().find(function (c) { return c.code === code; });
    }

        function openListPanel(type, title, id) {
        if (!DATA.panelUrl) return;

        document.getElementById('fdePanelId').textContent = 'Loading…';
        document.getElementById('fdePanelChips').innerHTML = '';
        document.getElementById('fdePanelBody').innerHTML =
            '<div class="dp-sec"><p class="empty"><i class="bi bi-hourglass-split"></i>Fetching records…</p></div>';
        overlay.classList.add('open');
        panel.classList.add('open');
        document.getElementById('fdePanelClose').focus();

        const url = new URL(DATA.panelUrl, window.location.origin);
        url.searchParams.set('period', DATA.period || 'month');
        url.searchParams.set('type', type);
        url.searchParams.set('title', title);
        if (id !== null && id !== undefined) url.searchParams.set('id', id);

        fetch(url, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
            .then((res) => { if (!res.ok) throw new Error(res.status); return res.json(); })
            .then((payload) => {
                document.getElementById('fdePanelId').textContent = payload.title || title;
                document.getElementById('fdePanelChips').innerHTML =
                    '<span class="dp-chip">' + esc(payload.subtitle || '') + '</span>';

                const items = payload.items || [];
                document.getElementById('fdePanelBody').innerHTML = '<div class="dp-sec">' + (
                    items.length
                        ? items.map((it) =>
                            '<div class="pnl-item" data-open-code="' + esc(it.code) + '">'
                          +   '<div class="pnl-item-top">'
                          +     '<span class="pnl-item-id">' + esc(it.code) + '</span>'
                          +     (it.badgeText ? '<span class="pnl-badge" style="background:' + esc(it.badgeColor || '#9a8053') + '22;color:' + esc(it.badgeColor || '#9a8053') + ';">' + esc(it.badgeText) + '</span>' : '')
                          +   '</div>'
                          +   (it.client ? '<div class="pnl-client">' + esc(it.client) + '</div>' : '')
                          +   (it.meta ? '<div class="pnl-meta"><i class="bi bi-geo-alt"></i>' + esc(it.meta) + '</div>' : '')
                          + '</div>'
                          ).join('')
                        : '<p class="empty"><i class="bi bi-inbox"></i>Nothing to show here.</p>'
                ) + '</div>';
            })
            .catch(() => {
                document.getElementById('fdePanelId').textContent = 'Could not load details';
                document.getElementById('fdePanelBody').innerHTML =
                    '<div class="dp-sec"><p class="empty"><i class="bi bi-wifi-off"></i>The request failed. Close and try again.</p></div>';
            });
    }

    function openMiniPanel(code, client, meta) {
        document.getElementById('fdePanelId').textContent = code || 'Service Request';
        document.getElementById('fdePanelChips').innerHTML = '';

        var body = '<div class="dp-sec">'
          +   (client ? '<div class="dp-cell" style="margin-bottom:9px;"><div class="dp-cell-lbl">Client</div><div class="dp-cell-val">' + esc(client) + '</div></div>' : '')
          +   (meta ? '<div class="dp-cell"><div class="dp-cell-lbl">Detail</div><div class="dp-cell-val">' + esc(meta) + '</div></div>' : '')
          +   '<p style="margin-top:12px;font-size:.74rem;color:var(--muted);">Full record details aren\'t loaded on this dashboard for older/closed SRs — open the SR explorer for the complete history.</p>'
          + '</div>';

        document.getElementById('fdePanelBody').innerHTML = body;
        overlay.classList.add('open');
        panel.classList.add('open');
        document.getElementById('fdePanelClose').focus();
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
            const amountLabel = card.invoice ? 'Invoice total' : 'Est. invoice';
            body += '<div class="dp-sec"><div class="dp-sec-lbl"><i class="bi bi-receipt"></i>Financial reference</div><div class="dp-grid">'
                 +    (card.erp     ? '<div class="dp-cell"><div class="dp-cell-lbl">ERP quote ref</div><div class="dp-cell-val gold mono">' + esc(card.erp) + '</div></div>' : '')
                 +    (card.invoice ? '<div class="dp-cell"><div class="dp-cell-lbl">Invoice ref</div><div class="dp-cell-val gold mono">' + esc(card.invoice) + '</div></div>' : '')
                 +    (card.amount  ? '<div class="dp-cell"><div class="dp-cell-lbl">' + amountLabel + '</div><div class="dp-cell-val gold">' + esc(card.amount) + '</div></div>' : '')
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
                const listTrigger = e.target.closest('[data-list]');
        if (listTrigger) {
            const key   = listTrigger.dataset.list;
            const title = listTrigger.dataset.listTitle || 'Details';

            if (key === 'client') {
                openListPanel('client', title, listTrigger.dataset.clientId);
            } else if (key === 'scope') {
                openListPanel('scope', title, listTrigger.dataset.scope);
            } else {
                openListPanel(key, title);
            }
            return;
        }

        const openCode = e.target.closest('[data-open-code]');
        if (openCode) {
            const card = findCardByCode(openCode.dataset.openCode);
            if (card) openPanel(card, card.stageLabel || 'Service Request');
            return;
        }

        const srTrigger = e.target.closest('[data-sr-code]');
        if (srTrigger) {
            const code  = srTrigger.dataset.srCode;
            const found = code ? findCardByCode(code) : null;
            if (found) {
                openPanel(found, found.stageLabel || 'Service Request');
            } else {
                openMiniPanel(code, srTrigger.dataset.srClient, srTrigger.dataset.srMeta);
            }
            return;
        }

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
| return view('front_dashboard', [
|     'greeting'      => 'Good morning, Sara!',
|     'greetingSub'   => "Here's your intake overview",
|     'today'         => now()->format('l, d F Y'),
|     'periodLabel'   => 'This Month',
|     'filters'       => ['period' => 'month'],
|     'periodOptions' => ['month' => 'This Month', 'week' => 'This Week', 'today' => 'Today'],
|
|     'alertCounts' => ['triage' => 3, 'wa_failures' => 2],
|
|     'kpis' => [   // delta_tone: du (good) | dd (bad) | dn (neutral)
|         'total'     => ['value' => 18, 'sub' => 'vs 16 last month', 'delta' => '+2', 'delta_tone' => 'du', 'spark' => [...]],
|         'pending'   => ['value' => 3,  'sub' => 'Not yet actioned', 'delta' => 'Oldest: 18h ago', 'delta_tone' => 'dn', 'spark' => [...]],
|         'cancelled' => ['value' => 2,  'sub' => 'Cancellation rate', 'delta' => '11%', 'delta_tone' => 'dn', 'spark' => [...]],
|         'completed' => ['value' => 16, 'sub' => 'Fully closed this month', 'delta' => '+4', 'delta_tone' => 'du', 'spark' => [...]],
|     ],
|
|     'statusBreakdown' => [['key' => 'inquiry', 'label' => 'Inquiry Logged', 'count' => 3, 'color' => '#64748b']],
|
|     'triage' => [['code' => 'SR-2025-00048', 'client' => '...', 'category' => 'Mechanical',
|                   'priority' => 'High', 'wait' => '18h', 'stale' => true]],
|
|     'srTrend'     => ['labels' => [...], 'in_warranty' => [...], 'out_warranty' => [...]],
|     'intakeStats' => ['total' => 18, 'perDay' => 1.2, 'today' => 3],
|     'scope'       => ['iw' => 13, 'oow' => 5, 'total' => 18],
|
|     'cancellation' => ['count' => 2, 'total' => 18, 'rate' => 11,
|                        'items' => [['code' => '...', 'client' => '...', 'reason' => 'Quote rejected']]],
|
|     'whatsapp' => ['delivered' => 16, 'failed' => 2, 'delivery_rate' => 89, 'available' => true,
|                    'failedList' => [['code' => '...', 'client' => '...', 'reason' => 'Invalid number']]],
|
|     'clients'     => [['id' => 1, 'name' => '...', 'srs' => 5, 'is_new' => false]],
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