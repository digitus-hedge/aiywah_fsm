<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
  <title>Field Pipeline | Matter Mind</title>
  <link rel="icon" type="image/png" href="{{ asset('assets/images/favicon.png') }}">

  <meta name="csrf-token" content="{{ csrf_token() }}"/>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600;700&display=swap" rel="stylesheet"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet"/>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

  <style>
/* ═══════════════════════════════════════
   THEME TOKENS
   Brand: #9a8053 primary · #B8976A light · #8A6E47 dark
═══════════════════════════════════════ */
:root,
[data-bs-theme="light"] {
  --app-bg:#f0f4ff;      --surface-2:#f4f6fb;   --surface-3:#eaeff9;
  --card-bg:#fff;        --card-border:#e4e8f5; --input-bg:#fff;
  --text-primary:#1a2236; --text-heading:#0d1626;
  --text-muted:#7987a1;   --text-light:#b0bac9;
  --border-color:#e4e8f0;
  --card-shadow:0 2px 14px rgba(80,100,160,.1);
  --overlay-bg:rgba(9,15,35,.65);
  --drawer-bg:#fff;
  --bottom-bar:#fff;
  --nav-active-bg:rgba(154,128,83,.1);
}
[data-bs-theme="dark"] {
  --app-bg:#1c1b1a;      --surface-2:#2a2928;   --surface-3:#302f2e;
  --card-bg:#242220;     --card-border:#3a3836; --input-bg:#2a2928;
  --text-primary:#d4cfc8; --text-heading:#e8e3dc;
  --text-muted:#7a756e;   --text-light:#4a4642;
  --border-color:#3a3836;
  --card-shadow:0 2px 16px rgba(0,0,0,.45);
  --overlay-bg:rgba(0,0,0,.78);
  --drawer-bg:#242220;
  --bottom-bar:#1e1d1c;
  --nav-active-bg:rgba(154,128,83,.15);
}

/* ═══════════════════════════════════════ BASE ═══ */
*,*::before,*::after { box-sizing:border-box; -webkit-tap-highlight-color:transparent; }
html,body { height:100%; margin:0; padding:0; }
body {
  font-size:.875rem; background:var(--app-bg); color:var(--text-primary);
  transition:background .3s,color .3s; overflow-x:hidden;
}
.hidden { display:none !important; }

.pg-title, .ajb-client, .tw-time { letter-spacing:-.01em; }

/* ═══════════════════════════════════════ SHELL ═══ */
.app-shell {
  display:flex; flex-direction:column; min-height:100vh;
  max-width:480px; margin:0 auto; position:relative; background:var(--app-bg);
}

.status-bar {
  background:linear-gradient(135deg,#9a8053,#B8976A); color:#fff;
  padding:10px 18px 8px; display:flex; align-items:center;
  justify-content:space-between; flex-shrink:0;
}
.sb-left { display:flex; align-items:center; gap:10px; }
.sb-avatar {
  width:36px; height:36px; border-radius:50%;
  background:rgba(255,255,255,.25); border:2px solid rgba(255,255,255,.5);
  display:flex; align-items:center; justify-content:center;
  font-size:.78rem; font-weight:700; flex-shrink:0;
}
.sb-name { font-size:.875rem; font-weight:600; line-height:1.2; }
.sb-role { font-size:.65rem; opacity:.8; }
.sb-right { display:flex; align-items:center; gap:10px; }
.sb-time { font-size:.72rem; opacity:.85; }
.th-btn {
  background:rgba(255,255,255,.15); border:none; border-radius:6px; color:#fff;
  width:30px; height:30px; display:flex; align-items:center;
  justify-content:center; cursor:pointer; font-size:.9rem;
}
.th-btn:hover { background:rgba(255,255,255,.25); }
.job-card.is-accepted { border-color:rgba(5,163,74,.45); border-left:3px solid #05a34a; }
.page-band {
  background:var(--card-bg); border-bottom:1px solid var(--card-border);
  padding:12px 16px; display:flex; align-items:center;
  justify-content:space-between; flex-shrink:0; box-shadow:0 1px 8px rgba(0,0,0,.06);
}
.pg-title {
  font-size:.9375rem; font-weight:600; color:var(--text-heading);
  display:flex; align-items:center; gap:7px;
}
.pg-sub { font-size:.7rem; color:var(--text-muted); }
.pg-count {
  font-size:.7rem; font-weight:700; padding:3px 10px; border-radius:20px;
  background:rgba(154,128,83,.12); color:#9a8053;
}
.back-btn {
  background:none; border:none; color:var(--text-muted); cursor:pointer;
  font-size:1.2rem; padding:4px; display:flex; align-items:center;
  justify-content:center; border-radius:6px;
}
.back-btn:hover { background:var(--surface-2); color:var(--text-heading); }

.page-content { flex:1; overflow-y:auto; -webkit-overflow-scrolling:touch; padding:14px 14px 90px; }
.page-content::-webkit-scrollbar { width:0; }

.bottom-nav {
  background:var(--bottom-bar); border-top:1px solid var(--card-border);
  display:flex; align-items:center; justify-content:space-around;
  padding:6px 0 max(6px,env(safe-area-inset-bottom));
  flex-shrink:0; box-shadow:0 -2px 12px rgba(0,0,0,.08);
}
.bn-item {
  display:flex; flex-direction:column; align-items:center; gap:3px;
  cursor:pointer; padding:4px 14px; border-radius:10px; transition:background .15s;
  font-size:.62rem; color:var(--text-muted); background:none; border:none; min-width:60px;
}
.bn-item i { font-size:1.3rem; }
.bn-item.active { color:#9a8053; background:var(--nav-active-bg); }
.bn-item:hover { background:var(--surface-2); }
.bn-badge { position:relative; display:inline-block; }
.bn-dot {
  position:absolute; top:-3px; right:-4px; width:9px; height:9px;
  border-radius:50%; background:#ff3366; border:2px solid var(--bottom-bar);
}

/* ═══════════════════════════════════════ PIPELINE ═══ */
.tab-filter { display:flex; background:var(--surface-2); border-radius:10px; padding:3px; margin-bottom:14px;overflow-x:auto;  }
.tab-filter::-webkit-scrollbar { height:0; }
.tf-btn {
  flex:1; padding:7px 6px; font-size:.75rem; font-weight:500;
  border:none; background:none; border-radius:8px; cursor:pointer;
  color:var(--text-muted); transition:all .2s;flex:0 0 auto; white-space:nowrap; padding:7px 10px;
}
.badge-hold { background:rgba(251,188,6,.12); color:#a8802a; }
.tf-btn.active { background:var(--card-bg); color:#9a8053; box-shadow:0 1px 6px rgba(0,0,0,.1); }

.job-card {
  background:var(--card-bg); border:1px solid var(--card-border); border-radius:12px;
  margin-bottom:10px; box-shadow:var(--card-shadow); overflow:hidden;
  transition:transform .15s,box-shadow .15s;
}
.job-card:active { transform:scale(.985); }
.job-card.is-active { border-color:rgba(5,163,74,.4); }
.jc-main { padding:13px 14px; cursor:pointer; }
.jc-top { display:flex; align-items:flex-start; justify-content:space-between; margin-bottom:8px; gap:8px; }
.jc-sr { font-size:.8rem; font-weight:700; color:#9a8053; letter-spacing:.02em; }
.jc-badge { font-size:.6rem; font-weight:700; padding:3px 9px; border-radius:20px; white-space:nowrap; flex-shrink:0; }
.badge-assigned { background:rgba(154,128,83,.12); color:#9a8053; }
.badge-rework   { background:rgba(255,51,102,.12); color:#ff3366; }
.badge-live     { background:rgba(5,163,74,.12);  color:#05a34a; }
.badge-pending     { background:rgba(154,128,83,.12); color:#9a8053; }
.badge-rescheduled { background:rgba(88,120,220,.12); color:#5878dc; }
.badge-review      { background:rgba(251,188,6,.12);  color:#a8802a; }
.badge-completed   { background:rgba(5,163,74,.12);   color:#05a34a; }
.jc-client { font-size:.84rem; font-weight:600; color:var(--text-heading); margin-bottom:2px; }
.jc-contract { font-size:.68rem; color:var(--text-muted); margin-bottom:8px; }
.jc-meta { display:flex; gap:10px; flex-wrap:wrap; align-items:center; }
.jc-meta-item { display:flex; align-items:center; gap:4px; font-size:.7rem; color:var(--text-muted); }
.jc-sla { font-size:.68rem; font-weight:600; padding:2px 7px; border-radius:8px; margin-left:auto; flex-shrink:0; }
.sla-ok { background:rgba(5,163,74,.1);  color:#05a34a; }
.sla-w  { background:rgba(251,188,6,.1); color:#a8802a; }
.sla-c  { background:rgba(255,51,102,.1); color:#ff3366; }
.jc-chevron { color:var(--text-light); font-size:.9rem; margin-left:6px; transition:transform .25s; flex-shrink:0; }
.jc-chevron.open { transform:rotate(180deg); }

.jc-expand { display:none; border-top:1px solid var(--card-border); background:var(--surface-2); }
.jc-expand.open { display:block; }
.jc-exp-section { padding:12px 14px; border-bottom:1px solid var(--card-border); }
.jc-exp-section:last-child { border-bottom:none; }
.exp-label {
  font-size:.62rem; font-weight:700; text-transform:uppercase; letter-spacing:.08em;
  color:var(--text-muted); margin-bottom:7px; display:flex; align-items:center; gap:5px;
}
.exp-text { font-size:.76rem; color:var(--text-primary); line-height:1.5; }
.rework-note {
  background:rgba(255,51,102,.07); border:1px solid rgba(255,51,102,.2);
  border-radius:8px; padding:9px 11px; font-size:.76rem;
  color:var(--text-primary); line-height:1.5; margin-top:4px;
}
.hist-line {
  font-size:.72rem; color:var(--text-muted); padding:3px 0 3px 8px;
  border-left:2px solid var(--border-color); margin:0 0 4px 2px;
}
.photo-row { display:flex; gap:8px; flex-wrap:wrap; margin-top:4px; }
.photo-thumb {
  width:64px; height:64px; border-radius:8px; background:var(--surface-3);
  border:1px solid var(--card-border); display:flex; align-items:center;
  justify-content:center; font-size:1.4rem; cursor:pointer; overflow:hidden; flex-shrink:0;
}
.photo-strip { display:flex; gap:8px; flex-wrap:wrap; padding:4px 0 10px; }
.photo-strip:empty { display:none; }
.ps-item { position:relative; width:64px; height:64px; border-radius:8px; overflow:hidden;
  border:1px solid var(--card-border); flex-shrink:0; background:var(--surface-3); }
.ps-item img { width:100%; height:100%; object-fit:cover; cursor:pointer; display:block; }
.ps-del { position:absolute; top:2px; right:2px; width:18px; height:18px; border-radius:50%;
  background:rgba(0,0,0,.6); color:#fff; border:none; cursor:pointer; font-size:.6rem;
  display:flex; align-items:center; justify-content:center; padding:0; line-height:1; }
.ps-del:hover { background:#ff3366; }

.eta-form { padding:12px 14px; background:var(--surface-3); border-top:1px solid var(--card-border); }
.eta-title { font-size:.72rem; font-weight:700; color:var(--text-heading); margin-bottom:10px; display:flex; align-items:center; gap:6px; }
.eta-title i { color:#9a8053; }
.eta-grid { display:grid; grid-template-columns:1fr 1fr; gap:8px; margin-bottom:12px; }
.eta-field label {
  font-size:.68rem; font-weight:600; text-transform:uppercase; letter-spacing:.06em;
  color:var(--text-muted); display:block; margin-bottom:4px;
}
.eta-input {
  width:100%; font-size:.82rem; border:1.5px solid var(--border-color); border-radius:8px;
  padding:.45rem .7rem; color:var(--text-primary); background:var(--input-bg);
  -webkit-appearance:none; appearance:none; transition:border-color .15s,box-shadow .15s;
}
.eta-input:focus { border-color:#9a8053; box-shadow:0 0 0 3px rgba(154,128,83,.15); outline:none; }
.eta-input.err { border-color:#ff3366; }
.eta-input::-webkit-calendar-picker-indicator { opacity:.6; }
.accept-btn {
  width:100%; border:none; border-radius:10px; padding:.62rem 1rem;
  font-size:.875rem; font-weight:600; cursor:pointer; display:flex;
  align-items:center; justify-content:center; gap:7px; transition:all .2s;
  background:linear-gradient(135deg,#9a8053,#B8976A); color:#fff;
}
.accept-btn:hover:not(:disabled) { box-shadow:0 4px 16px rgba(154,128,83,.4); }
.accept-btn:active:not(:disabled) { transform:scale(.97); }
.accept-btn:disabled { opacity:.5; cursor:not-allowed; background:var(--surface-2); color:var(--text-muted); }
.wa-hint {
  font-size:.68rem; color:var(--text-muted); text-align:center; margin-top:7px;
  display:flex; align-items:center; justify-content:center; gap:4px;
}
.wa-hint i { color:#25d366; }
.err-msg { font-size:.68rem; color:#ff3366; margin-top:4px; display:none; }
.err-msg.show { display:block; }

.jc-active-note {
  padding:12px 14px; text-align:center; font-size:.78rem; color:#05a34a;
  font-weight:600; background:rgba(5,163,74,.06); border-top:1px solid var(--card-border);
}

.empty-state { text-align:center; padding:50px 20px; color:var(--text-muted); }
.empty-state i { font-size:3rem; display:block; margin-bottom:10px; opacity:.25; }
.empty-state h6 { font-weight:600; color:var(--text-heading); margin-bottom:4px; }

/* ═══════════════════════════════════════ TERMINAL ═══ */
.ajb {
  background:linear-gradient(135deg,#8A6E47,#9a8053); border-radius:12px;
  padding:14px 16px; margin-bottom:14px; color:#fff; position:relative; overflow:hidden;
}
.ajb::before {
  content:''; position:absolute; right:-20px; top:-20px; width:100px; height:100px;
  border-radius:50%; background:rgba(255,255,255,.08);
}
.ajb-sr { font-size:.72rem; opacity:.8; margin-bottom:2px; }
.ajb-client { font-size:1.15rem; font-weight:700; margin-bottom:2px; }
.ajb-site { font-size:.78rem; opacity:.85; display:flex; align-items:center; gap:5px; }
.ajb-meta { display:flex; gap:8px; margin-top:10px; flex-wrap:wrap; }
.ajb-chip {
  background:rgba(255,255,255,.18); border:1px solid rgba(255,255,255,.3);
  border-radius:20px; font-size:.65rem; padding:3px 10px; font-weight:500;
  display:flex; align-items:center; gap:4px;
}

.timer-widget {
  background:var(--card-bg); border:1px solid var(--card-border); border-radius:12px;
  padding:14px 16px; margin-bottom:12px; display:flex; align-items:center;
  gap:14px; box-shadow:var(--card-shadow);
}
.tw-icon {
  width:44px; height:44px; border-radius:10px; display:flex; align-items:center;
  justify-content:center; font-size:1.2rem; flex-shrink:0; background:rgba(174,183,197,.12);
}
.tw-time { font-size:1.6rem; font-weight:700; font-variant-numeric:tabular-nums; color:var(--text-heading); }
.tw-lbl { font-size:.68rem; color:var(--text-muted); }
.tw-status { margin-left:auto; font-size:.7rem; font-weight:600; padding:3px 10px; border-radius:20px; }
.status-idle { background:rgba(174,183,197,.12); color:#5a6a7e; }
.status-live { background:rgba(5,163,74,.12); color:#05a34a; animation:statusPulse 2s ease-in-out infinite; }
.status-done { background:rgba(154,128,83,.12); color:#9a8053; }
@keyframes statusPulse { 0%,100%{opacity:1;} 50%{opacity:.6;} }

.punch-row { display:grid; grid-template-columns:1fr 1fr; gap:10px; margin-bottom:14px; }
.punch-btn {
  border:none; border-radius:10px; padding:.65rem 1rem; font-size:.84rem; font-weight:600;
  cursor:pointer; display:flex; align-items:center; justify-content:center; gap:6px; transition:all .2s;
}
.punch-btn:active:not(:disabled) { transform:scale(.96); }
.punch-btn:disabled { opacity:.45; cursor:not-allowed; }
.btn-punchin { background:linear-gradient(135deg,#05a34a,#0abf56); color:#fff; }
.btn-punchin:hover:not(:disabled) { box-shadow:0 4px 14px rgba(5,163,74,.4); }
.btn-punchout { background:linear-gradient(135deg,#ff3366,#e02050); color:#fff; }
.btn-punchout:hover:not(:disabled) { box-shadow:0 4px 14px rgba(255,51,102,.4); }

.btn-outline {
  width:100%; border:1.5px dashed rgba(251,188,6,.5); border-radius:10px;
  padding:.6rem 1rem; font-size:.82rem; font-weight:500; cursor:pointer;
  display:flex; align-items:center; justify-content:center; gap:6px;
  background:rgba(251,188,6,.05); color:#a8802a; transition:all .2s; margin-bottom:14px;
}
.btn-outline:hover:not(:disabled) { background:rgba(251,188,6,.1); border-color:#fbbc06; }
.btn-outline:disabled { opacity:.45; cursor:not-allowed; }
.btn-outline.brand {
  border-color:rgba(154,128,83,.4); color:#9a8053; background:rgba(154,128,83,.04);
}
.btn-outline.brand:hover:not(:disabled) { background:rgba(154,128,83,.1); border-color:#9a8053; }

.compliance-card {
  background:var(--card-bg); border:1px solid var(--card-border); border-radius:12px;
  padding:14px; margin-bottom:12px; box-shadow:var(--card-shadow);
}
.comp-title { font-size:.78rem; font-weight:700; color:var(--text-heading); margin-bottom:12px; display:flex; align-items:center; gap:6px; }
.comp-title i { color:#9a8053; }
.comp-item { display:flex; align-items:center; gap:10px; padding:10px 0; border-bottom:1px solid var(--card-border); }
.comp-item:last-child { border-bottom:none; padding-bottom:0; }
.comp-icon-wrap { width:40px; height:40px; border-radius:9px; display:flex; align-items:center; justify-content:center; font-size:1.1rem; flex-shrink:0; }
.comp-info { flex:1; min-width:0; }
.comp-lbl { font-size:.78rem; font-weight:500; color:var(--text-heading); }
.comp-hint { font-size:.68rem; color:var(--text-muted); margin-top:1px; }
.comp-status { font-size:.68rem; font-weight:700; padding:3px 9px; border-radius:20px; flex-shrink:0; white-space:nowrap; }
.cs-done { background:rgba(5,163,74,.12); color:#05a34a; }
.cs-pending { background:rgba(174,183,197,.12); color:#5a6a7e; }
.comp-upload-btn {
  background:none; border:1.5px solid var(--border-color); border-radius:7px;
  color:var(--text-muted); font-size:.72rem; padding:4px 10px; cursor:pointer;
  display:flex; align-items:center; gap:4px; flex-shrink:0; transition:all .15s;
}
.comp-upload-btn:hover:not(:disabled) { border-color:#9a8053; color:#9a8053; }
.comp-upload-btn:disabled { opacity:.4; cursor:not-allowed; }
.upload-input { display:none; }

.lock-info {
  background:rgba(255,51,102,.06); border:1px solid rgba(255,51,102,.2);
  border-radius:8px; padding:9px 12px; font-size:.72rem; color:var(--text-muted);
  display:flex; align-items:flex-start; gap:7px; margin-bottom:12px;
}
.lock-info i { color:#ff3366; flex-shrink:0; margin-top:1px; }

.location-badge {
  background:var(--surface-2); border:1px solid var(--card-border); border-radius:8px;
  padding:8px 11px; font-size:.72rem; color:var(--text-muted);
  display:flex; align-items:center; gap:7px; margin-bottom:12px;
}
.location-badge i { color:#9a8053; }
.location-badge .coords { font-size:.68rem; font-weight:600; color:var(--text-heading); }

.section-card {
  background:var(--card-bg); border:1px solid var(--card-border); border-radius:12px;
  margin-bottom:12px; overflow:hidden; box-shadow:var(--card-shadow);
}
.section-card-hdr {
  padding:11px 14px; border-bottom:1px solid var(--card-border);
  font-size:.78rem; font-weight:700; color:var(--text-heading);
  display:flex; align-items:center; gap:7px;
}
.section-card-body { padding:12px 14px; }
.exp-item { display:flex; align-items:center; gap:8px; padding:7px 0; border-bottom:1px solid var(--card-border); font-size:.75rem; }
.exp-item:last-child { border-bottom:none; }
.exp-item .ea { font-weight:700; color:var(--text-heading); }
.exp-item .en { font-weight:500; color:var(--text-heading); overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
.exp-item .ec { font-size:.66rem; color:var(--text-muted); }
.exp-item .er { font-size:.68rem; color:var(--text-light); }
.exp-item .body { min-width:0; flex:1; }
.exp-receipt {
  width:28px; height:28px; border-radius:5px; object-fit:cover;
  border:1px solid var(--card-border); cursor:pointer; flex-shrink:0;
}
.exp-total {
  text-align:right; font-size:.75rem; font-weight:700; color:var(--text-heading);
  padding-top:6px; border-top:1px solid var(--card-border);
}
.exp-empty { text-align:center; padding:14px; font-size:.78rem; color:var(--text-muted); }

/* ═══════════════════════════════════════ DRAWERS ═══ */
.overlay { display:none; position:fixed; inset:0; background:var(--overlay-bg); z-index:800; }
.overlay.show { display:block; }
.drawer {
  position:fixed; bottom:0; left:50%; transform:translateX(-50%) translateY(100%);
  width:100%; max-width:480px; background:var(--drawer-bg);
  border-radius:20px 20px 0 0; z-index:900;
  padding:0 0 max(20px,env(safe-area-inset-bottom));
  box-shadow:0 -8px 40px rgba(0,0,0,.25);
  transition:transform .32s cubic-bezier(.4,0,.2,1);
  max-height:92vh; overflow-y:auto;
}
.drawer.open { transform:translateX(-50%) translateY(0); }
.drawer-handle { width:36px; height:4px; background:var(--border-color); border-radius:2px; margin:10px auto 0; }
.drawer-hdr {
  padding:14px 18px 10px; border-bottom:1px solid var(--card-border);
  display:flex; align-items:center; justify-content:space-between;
}
.drawer-hdr h6 { margin:0; font-size:.9rem; font-weight:600; color:var(--text-heading); display:flex; align-items:center; gap:7px; }
.drawer-close {
  background:none; border:none; color:var(--text-muted); cursor:pointer;
  font-size:1.1rem; padding:4px; border-radius:6px; line-height:1;
}
.drawer-close:hover { color:var(--text-heading); background:var(--surface-2); }
.drawer-body { padding:14px 18px; }
.drawer-sr {
  font-size:.7rem; color:var(--text-muted); background:var(--surface-2);
  border-radius:7px; padding:7px 10px; margin-bottom:12px;
  display:flex; align-items:center; gap:6px;
}
.drawer-sr i { color:#9a8053; }
.drawer-sr strong { color:#9a8053; }

.d-label {
  font-size:.72rem; font-weight:600; text-transform:uppercase; letter-spacing:.06em;
  color:var(--text-muted); display:block; margin:12px 0 5px;
}
.d-label:first-of-type { margin-top:0; }
.req { color:#ff3366; }
.d-input, .d-select, .d-remark {
  width:100%; font-size:.84rem; border:1.5px solid var(--border-color); border-radius:8px;
  padding:.48rem .75rem; color:var(--text-primary); background:var(--input-bg);
  -webkit-appearance:none; transition:border-color .15s,box-shadow .15s; 
}
.d-remark { resize:none; min-height:72px; }
.d-input:focus, .d-select:focus, .d-remark:focus {
  border-color:#9a8053; box-shadow:0 0 0 3px rgba(154,128,83,.15); outline:none;
}
.d-input::placeholder, .d-remark::placeholder { color:var(--text-light); }
[data-bs-theme="dark"] .d-select option { background:#2a2928; color:#d4cfc8; }

.rs-tabs { display:flex; background:var(--surface-2); border-radius:8px; padding:3px; margin-bottom:14px; }
.rs-tab {
  flex:1; padding:8px; font-size:.78rem; font-weight:500; border:none; background:none;
  border-radius:6px; cursor:pointer; color:var(--text-muted); transition:all .18s;
}
.rs-tab.active { background:var(--card-bg); color:#9a8053; box-shadow:0 1px 5px rgba(0,0,0,.1); }
.rs-panel { display:none; }
.rs-panel.show { display:block; }
.hold-note {
  background:rgba(251,188,6,.07); border:1px solid rgba(251,188,6,.2); border-radius:7px;
  padding:9px 11px; font-size:.72rem; color:var(--text-muted);
  display:flex; align-items:flex-start; gap:7px; margin-bottom:10px;
}
.hold-note i { color:#fbbc06; flex-shrink:0; margin-top:1px; }

.receipt-zone {
  border:2px dashed var(--border-color); border-radius:10px; padding:18px;
  text-align:center; cursor:pointer; background:var(--surface-2);
  transition:all .2s; position:relative; margin-top:4px;
}
.receipt-zone:hover { border-color:#9a8053; background:rgba(154,128,83,.05); }
.receipt-zone input { position:absolute; inset:0; opacity:0; cursor:pointer; }
.receipt-zone i { font-size:1.6rem; color:var(--text-muted); display:block; margin-bottom:5px; }
.receipt-zone p { font-size:.75rem; color:var(--text-muted); margin:0; }
.receipt-preview {
  display:none; background:rgba(5,163,74,.07); border:1px solid rgba(5,163,74,.2);
  border-radius:8px; padding:8px 11px; font-size:.75rem; color:#05a34a;
  align-items:center; gap:6px; margin-top:6px;
}
.receipt-preview.show { display:flex; }

.drawer-actions { display:grid; grid-template-columns:1fr 1fr; gap:10px; margin-top:16px; }
.btn-save {
  background:linear-gradient(135deg,#9a8053,#B8976A); color:#fff; border:none;
  border-radius:9px; padding:.58rem 1rem; font-size:.84rem; font-weight:600;
  cursor:pointer; transition:all .2s;
}
.btn-save:hover:not(:disabled) { box-shadow:0 4px 14px rgba(154,128,83,.4); }
.btn-save:disabled { opacity:.6; cursor:not-allowed; }
.btn-save.warn { background:linear-gradient(135deg,#fbbc06,#f59e0b); color:#1a2236; }
.btn-cancel {
  background:var(--surface-2); border:1.5px solid var(--border-color); border-radius:9px;
  padding:.58rem 1rem; font-size:.84rem; color:var(--text-muted); cursor:pointer; transition:all .2s;
}
.btn-cancel:hover { border-color:var(--text-muted); color:var(--text-heading); }

/* ═══════════════════════════════════════ TOAST ═══ */
.toast-wrap {
  position:fixed; top:16px; left:50%; transform:translateX(-50%); z-index:9999;
  width:calc(100% - 28px); max-width:440px; display:flex; flex-direction:column;
  gap:7px; pointer-events:none;
}
.toast-item {
  background:var(--card-bg); border-left:4px solid #9a8053; border-radius:10px;
  padding:11px 14px; box-shadow:0 6px 24px rgba(0,0,0,.18);
  display:flex; align-items:flex-start; gap:9px; animation:toastIn .3s ease; pointer-events:all;
}
.toast-item.success { border-color:#05a34a; }
.toast-item.error   { border-color:#ff3366; }
.toast-item.warning { border-color:#fbbc06; }
@keyframes toastIn { from{transform:translateY(-20px);opacity:0;} to{transform:none;opacity:1;} }
.ti-ico { font-size:1.05rem; flex-shrink:0; margin-top:1px; }
.ti-ico.primary { color:#9a8053; }
.ti-ico.success { color:#05a34a; }
.ti-ico.error   { color:#ff3366; }
.ti-ico.warning { color:#fbbc06; }
.ti-t { font-size:.8rem; font-weight:600; margin:0 0 1px; color:var(--text-heading); }
.ti-b { font-size:.7rem; margin:0; color:var(--text-muted); }



/* ═══════════════════════════════════════ LIGHTBOX ═══ */
.lb-overlay {
  display:none; position:fixed; inset:0; background:rgba(0,0,0,.88);
  z-index:9000; align-items:center; justify-content:center; padding:20px;
}
.lb-overlay.show { display:flex; }
.lb-inner { width:100%; max-width:420px; text-align:center; }
.lb-img { width:100%; max-height:60vh; object-fit:contain; border-radius:10px; }
.lb-ph {
  color:rgba(255,255,255,.5); font-size:.85rem; padding:60px 20px;
  background:rgba(255,255,255,.05); border-radius:10px;
  display:flex; flex-direction:column; align-items:center; gap:8px;
}
.lb-close {
  color:#fff; background:rgba(255,255,255,.15); border:none; border-radius:8px;
  padding:8px 18px; margin-top:12px; cursor:pointer; font-size:.84rem;
  display:inline-flex; align-items:center; gap:6px;
}

.spin {
  display:inline-block; width:14px; height:14px;
  border:2px solid rgba(255,255,255,.4); border-top-color:#fff;
  border-radius:50%; animation:spin .7s linear infinite;
}
@keyframes spin { to { transform:rotate(360deg); } }

/* ═══════════════════════════════════════ HISTORY ═══ */
.stat-row { display:grid; grid-template-columns:repeat(3,1fr); gap:8px; margin-bottom:14px; }
.stat-box {
  background:var(--card-bg); border:1px solid var(--card-border); border-radius:11px;
  padding:11px 8px; text-align:center; box-shadow:var(--card-shadow);
}
.stat-val { font-size:1.35rem; font-weight:700; color:var(--text-heading); line-height:1.1; }
.stat-lbl { font-size:.62rem; text-transform:uppercase; letter-spacing:.06em; color:var(--text-muted); margin-top:3px; }

.hist-card {
  background:var(--card-bg); border:1px solid var(--card-border); border-radius:12px;
  padding:12px 14px; margin-bottom:10px; box-shadow:var(--card-shadow);
}
.hist-top { display:flex; align-items:center; justify-content:space-between; margin-bottom:6px; gap:8px; }
.hist-ref { font-size:.78rem; font-weight:700; color:#9a8053; }
.hist-date { font-size:.68rem; color:var(--text-muted); }
.hist-client { font-size:.82rem; font-weight:600; color:var(--text-heading); margin-bottom:2px; }
.hist-site { font-size:.68rem; color:var(--text-muted); margin-bottom:8px; }
.hist-grid { display:flex; gap:10px; flex-wrap:wrap; align-items:center; }
.hist-chip {
  font-size:.68rem; color:var(--text-muted); display:flex; align-items:center;
  gap:4px; background:var(--surface-2); border-radius:7px; padding:3px 8px;
}
.hist-total { margin-left:auto; font-size:.76rem; font-weight:700; color:var(--text-heading); }
.badge-submitted { background:rgba(154,128,83,.12); color:#9a8053; }
.badge-approved  { background:rgba(5,163,74,.12);  color:#05a34a; }
.badge-rejected  { background:rgba(255,51,102,.12); color:#ff3366; }

/* ═══════════════════════════════════════ PROFILE ═══ */
.prof-hero {
  background:linear-gradient(135deg,#8A6E47,#9a8053); border-radius:14px;
  padding:22px 16px; text-align:center; color:#fff; margin-bottom:14px;
  position:relative; overflow:hidden;
}
.prof-hero::before {
  content:''; position:absolute; right:-30px; top:-30px; width:120px; height:120px;
  border-radius:50%; background:rgba(255,255,255,.08);
}
.prof-avatar {
  width:72px; height:72px; border-radius:50%; margin:0 auto 10px;
  background:rgba(255,255,255,.22); border:3px solid rgba(255,255,255,.45);
  display:flex; align-items:center; justify-content:center;
  font-size:1.5rem; font-weight:700; position:relative;
}
.prof-name {font-size:1.4rem; font-weight:700; }
.prof-role { font-size:.72rem; opacity:.85; margin-top:2px; }

.info-row { display:flex; align-items:center; gap:11px; padding:11px 0; border-bottom:1px solid var(--card-border); }
.info-row:last-child { border-bottom:none; padding-bottom:0; }
.info-ico {
  width:36px; height:36px; border-radius:9px; background:rgba(154,128,83,.1);
  display:flex; align-items:center; justify-content:center; color:#9a8053; flex-shrink:0;
}
.info-lbl { font-size:.66rem; text-transform:uppercase; letter-spacing:.06em; color:var(--text-muted); }
.info-val { font-size:.82rem; font-weight:500; color:var(--text-heading); word-break:break-word; }

.skel { background:var(--surface-2); border-radius:8px; height:70px; margin-bottom:10px; animation:statusPulse 1.4s ease-in-out infinite; }

@media (min-width:481px) {
  body { background:#e8ecf8; }
  [data-bs-theme="dark"] body { background:#030810; }
  .app-shell { box-shadow:0 0 40px rgba(0,0,0,.15); }
}

/* Modal Form Data */

.modal-backdrop{
  position:fixed;
  inset:0;
  z-index:99999;
  background:rgba(0,0,0,.55);
  display:flex;
  align-items:center;
  justify-content:center;
  padding:16px;
}
.modal-card{
  width:100%;
  max-width:380px;
  padding:20px;
  border-radius:16px;
  background:var(--bs-body-bg,#fff);
  color:var(--bs-body-color,inherit);
  display:flex;
  flex-direction:column;
  gap:10px;
  box-shadow:0 18px 50px rgba(0,0,0,.35);
}
.modal-card .inp{
  width:100%;
  padding:11px 13px;
  border-radius:9px;
  font-size:14px;
  border:1px solid rgba(128,128,128,.3);
  background:transparent;
  color:inherit;
}
.modal-card .inp:focus{
  outline:none;
  border-color:#b08d57;
}
.modal-actions{
  display:flex;
  gap:8px;
  margin-top:6px;
}
.modal-actions button{
  flex:1;
}
.pwd-msg{
  font-size:13px;
  padding:8px 10px;
  border-radius:8px;
}
.pwd-err{color:#ff3366;background:rgba(255,51,102,.08);}
.pwd-ok{color:#22c55e;background:rgba(34,197,94,.08);}


  </style>
</head>
<body>

<div class="toast-wrap" id="toastWrap"></div>

<!-- ══════════ LIGHTBOX ══════════ -->
<div class="lb-overlay" id="lbOverlay">
  <div class="lb-inner">
    <div id="lbContent"></div>
    <button class="lb-close" id="lbCloseBtn"><i class="bi bi-x-lg"></i>Close</button>
  </div>
</div>

<!-- ══════════ RESCHEDULE / HOLD DRAWER ══════════ -->
<div class="overlay" id="rsOverlay"></div>
<div class="drawer" id="rsDrawer">
  <div class="drawer-handle"></div>
  <div class="drawer-hdr">
    <h6><i class="bi bi-calendar2-event" style="color:#9a8053;"></i>Reschedule or Hold Job</h6>
    <button class="drawer-close" data-close="rs"><i class="bi bi-x-lg"></i></button>
  </div>
  <div class="drawer-body">
    <div class="drawer-sr">
      <i class="bi bi-link-45deg"></i>Job: <strong id="rsSrRef">&mdash;</strong>
    </div>

    <div class="rs-tabs">
      <button class="rs-tab active" data-rstab="reschedule">
        <i class="bi bi-calendar-check"></i> Reschedule
      </button>
      <button class="rs-tab" data-rstab="hold">
        <i class="bi bi-pause-circle"></i> Keep on Hold
      </button>
    </div>

    <div class="rs-panel show" id="rsPanel-reschedule">
      <label class="d-label" for="rsDate">New Date <span class="req">*</span></label>
      <input type="date" class="d-input" id="rsDate"/>

      <label class="d-label" for="rsTime">New Time <span class="req">*</span></label>
      <input type="time" class="d-input" id="rsTime"/>

      <label class="d-label" for="rsRemark">Reason / Remark <span class="req">*</span></label>
      <textarea class="d-remark" id="rsRemark" rows="3" placeholder="Reason for rescheduling&hellip;"></textarea>

      <div class="drawer-actions">
        <button class="btn-cancel" data-close="rs">Cancel</button>
        <button class="btn-save" id="rsConfirmBtn">
          <i class="bi bi-calendar-check"></i> Reschedule
        </button>
      </div>
    </div>

    <div class="rs-panel" id="rsPanel-hold">
      <div class="hold-note">
        <i class="bi bi-info-circle-fill"></i>
        Job will be placed on hold and the client notified. You can re-activate it from your pipeline.
      </div>

      <label class="d-label" for="holdRemark">Reason for Hold <span class="req">*</span></label>
      <textarea class="d-remark" id="holdRemark" rows="3" placeholder="Reason for placing job on hold&hellip;"></textarea>

      <div class="drawer-actions">
        <button class="btn-cancel" data-close="rs">Cancel</button>
        <button class="btn-save warn" id="holdConfirmBtn">
          <i class="bi bi-pause-circle"></i> Confirm Hold
        </button>
      </div>
    </div>
  </div>
</div>

<!-- ══════════ EXPENSE DRAWER ══════════ -->
<div class="overlay" id="expOverlay"></div>
<div class="drawer" id="expenseDrawer">
  <div class="drawer-handle"></div>
  <div class="drawer-hdr">
    <h6><i class="bi bi-receipt" style="color:#fbbc06;"></i>Log Material Expense</h6>
    <button class="drawer-close" data-close="exp"><i class="bi bi-x-lg"></i></button>
  </div>
  <div class="drawer-body">
    <div class="drawer-sr">
      <i class="bi bi-link-45deg"></i>Linked to SR: <strong id="expSrRef">&mdash;</strong>
    </div>

    <label class="d-label" for="expCategory">Expense Category <span class="req">*</span></label>
    <select class="d-select" id="expCategory">
      <option value="">&mdash; Select category &mdash;</option>
      @foreach (($expenseCategories ?? []) as $cat)
        <option value="{{ $cat }}">{{ $cat }}</option>
      @endforeach
    </select>

    <div id="expNameWrap" class="hidden" style="margin-top: 13px;">
      <label class="d-label" for="expName">Item Name <span class="req">*</span></label>
      <input type="text" class="d-input" id="expName" placeholder="e.g. 20mm PVC elbow"/>
    </div>

    <label class="d-label" for="expAmount">Amount (AED) <span class="req">*</span></label>
    <input type="number" class="d-input" id="expAmount" placeholder="0.00" min="0" step="0.01" inputmode="decimal"/>

    <span class="d-label" style="margin-top: 13px;">Receipt Photo</span>
    <div class="receipt-zone">
      <input type="file" id="expReceipt" accept="image/*" capture="environment"/>
      <i class="bi bi-camera-fill"></i>
      <p>Tap to capture receipt</p>
    </div>
    <div class="receipt-preview" id="receiptPreview">
      <i class="bi bi-check-circle-fill"></i><span id="receiptName"></span>
    </div>

    <div class="drawer-actions">
      <button class="btn-cancel" data-close="exp">Cancel</button>
      <button class="btn-save" id="expSaveBtn">Save Entry</button>
    </div>
  </div>
</div>

<!-- ══════════ FINISH JOB DRAWER ══════════ -->
<div class="overlay" id="finOverlay"></div>
<div class="drawer" id="finishDrawer">
  <div class="drawer-handle"></div>
  <div class="drawer-hdr">
    <h6><i class="bi bi-check2-square" style="color:#05a34a;"></i>Finish Job</h6>
    <button class="drawer-close" data-close="fin"><i class="bi bi-x-lg"></i></button>
  </div>
  <div class="drawer-body">
    <div class="drawer-sr">
      <i class="bi bi-link-45deg"></i>Job: <strong id="finSrRef">&mdash;</strong>
    </div>

    <label class="d-label" for="finSummary">Completion Summary</label>
    <textarea class="d-remark" id="finSummary" rows="3" placeholder="What was done, parts replaced, outcome&hellip;"></textarea>
    <div class="drawer-actions">
      <button class="btn-cancel" data-close="fin">Cancel</button>
      <button class="btn-save" id="finConfirmBtn">
        <i class="bi bi-check2-square"></i> Finish Job
      </button>
    </div>
  </div>
</div>

<!-- ══════════ CLIENT ACCEPTANCE + SIGNATURE DRAWER ══════════ -->
<div class="overlay" id="signOverlay"></div>
<div class="drawer" id="signDrawer">
  <div class="drawer-handle"></div>
  <div class="drawer-hdr">
    <h6><i class="bi bi-pen" style="color:#9a8053;"></i>Client Acceptance</h6>
    <button class="drawer-close" data-close="sign"><i class="bi bi-x-lg"></i></button>
  </div>
  <div class="drawer-body">
    <div class="drawer-sr">
      <i class="bi bi-link-45deg"></i>Job: <strong id="signSrRef">&mdash;</strong>
    </div>

    <div id="termsBlock">
      <label class="d-label">Terms &amp; Policy</label>
      <div id="termsScroll" style="max-height:200px;overflow-y:auto;border:1.5px solid var(--border-color);border-radius:8px;padding:12px;font-size:.76rem;line-height:1.55;color:var(--text-primary);background:var(--surface-2);">
        <p style="margin-top:0;"><strong>Service Completion Acceptance</strong></p>
        <p>By signing below, the client confirms the work described has been carried out to a satisfactory standard and the site has been left in acceptable condition.</p>
        <p>The client acknowledges the materials logged against this job and agrees these were used in the course of the work.</p>
        <p>Signing does not waive any manufacturer or workmanship warranty applicable to the service.</p>
        <p>Any dispute regarding the completed work must be raised within the warranty window stated in the service agreement.</p>
        <p style="margin-bottom:0;">This acceptance is recorded electronically with a timestamp and forms part of the service record.</p>
      </div>

      <label style="display:flex;align-items:flex-start;gap:8px;margin-top:12px;font-size:.78rem;cursor:pointer;color:var(--text-heading);">
        <input type="checkbox" id="policyCheck" style="margin-top:2px;width:16px;height:16px;flex-shrink:0;"/>
        <span>I have read and accept the terms and policy above on behalf of the client.</span>
      </label>
    </div>

    <div id="signBlock" class="hidden" style="margin-top: 15px;">
      <label class="d-label" for="clientNameInput">Client Name <span class="req">*</span></label>
      <input type="text" class="d-input" id="clientNameInput" placeholder="Name of person signing"/>

      <span class="d-label" style="margin-top:12px;">Signature <span class="req">*</span></span>
      <div style="border:1.5px solid var(--border-color);border-radius:8px;background:#fff;position:relative;">
        <canvas id="sigCanvas" style="width:100%;height:170px;display:block;touch-action:none;border-radius:8px;"></canvas>
      </div>
      <button type="button" id="sigClearBtn"
              style="margin-top:6px;background:none;border:1.5px solid var(--border-color);border-radius:7px;color:var(--text-muted);font-size:.72rem;padding:5px 12px;cursor:pointer;">
        <i class="bi bi-eraser"></i> Clear
      </button>
    </div>

    <div class="drawer-actions">
      <button class="btn-cancel" data-close="sign">Cancel</button>
      <button class="btn-save" id="signSubmitBtn" disabled>
        <i class="bi bi-check2-square"></i> Accept &amp; Sign
      </button>
    </div>
  </div>
</div>
<!-- ══════════ APP SHELL ══════════ -->
<div class="app-shell">

  <div class="status-bar">
    <div class="sb-left">
      <div class="sb-avatar">{{ $userInitials ?? '' }}</div>
      <div>
        <div class="sb-name">{{ $userName ?? '' }}</div>
        <div class="sb-role">
          {{ $userRole ?? '' }}@if (!empty($userCode)) &middot; {{ $userCode }}@endif
        </div>
      </div>
    </div>
   <div class="sb-right">
      <span class="sb-time" id="clock"></span>
      <button class="th-btn" id="themeBtn" title="Toggle theme" aria-label="Toggle theme">
        <i class="bi bi-sun-fill" id="themeIcon"></i>
      </button>
      <button class="th-btn" id="logoutBtn" title="Sign out" aria-label="Sign out">
        <i class="bi bi-box-arrow-right"></i>
      </button>
    </div>
  </div>

<!-- ══════════ LOGOUT DRAWER ══════════ -->
<div class="overlay" id="outOverlay"></div>
<div class="drawer" id="logoutDrawer">
  <div class="drawer-handle"></div>
  <div class="drawer-hdr">
    <h6><i class="bi bi-box-arrow-right" style="color:#ff3366;"></i>Sign Out</h6>
    <button class="drawer-close" data-close="out"><i class="bi bi-x-lg"></i></button>
  </div>
  <div class="drawer-body">
    <div class="hold-note" style="background:rgba(255,51,102,.07);border-color:rgba(255,51,102,.2);">
      <i class="bi bi-question-circle-fill" style="color:#ff3366;"></i>
      You'll need to sign in again to access your pipeline. Any unsaved work in the terminal will be lost.
    </div>

    <div class="drawer-actions">
      <button class="btn-cancel" data-close="out">Stay Signed In</button>
      <button class="btn-save" id="outConfirmBtn"
              style="background:linear-gradient(135deg,#ff3366,#e02050);">
        <i class="bi bi-box-arrow-right"></i> Sign Out
      </button>
    </div>
  </div>
</div>

  <!-- ── PIPELINE ── -->
  <section id="pagePipeline">
    <div class="page-band">
      <div>
        <div class="pg-title"><i class="bi bi-list-task" style="color:#9a8053;"></i>My Pipeline</div>
        <div class="pg-sub" id="pipelineSubtitle">Loading jobs&hellip;</div>
      </div>
      <span class="pg-count" id="pipelineCount">0 jobs</span>
    </div>

    <div class="page-content">
      <div class="tab-filter" id="tabFilter">
      <button class="tf-btn active" data-filter="Pending">Pending</button>
      <button class="tf-btn" data-filter="Accepted">Accepted</button>
      <button class="tf-btn" data-filter="Rework">Rework</button>
      <button class="tf-btn" data-filter="Rescheduled">Rescheduled</button>
      <button class="tf-btn" data-filter="On Hold">On Hold</button>
      <button class="tf-btn" data-filter="Review">Review</button>
    </div>
      <div id="jobList"></div>
    </div>
  </section>

  <!-- ── JOB TERMINAL ── -->
  <section id="pageTerminal" class="hidden">
    <div class="page-band">
      <button class="back-btn" id="backBtn" aria-label="Back"><i class="bi bi-arrow-left"></i></button>
      <div>
        <div class="pg-title" id="termTitle" style="font-size:.875rem;">
          <i class="bi bi-broadcast" style="color:#8A6E47;"></i>Job Terminal
        </div>
        <div class="pg-sub" id="termSub">Active job dashboard</div>
      </div>
      <div style="width:32px;"></div>
    </div>

    <div class="page-content">
      <div class="ajb" id="jobBanner"></div>

      <div class="timer-widget">
        <div class="tw-icon" id="timerIcon"><i class="bi bi-clock" style="color:#aeb7c5;"></i></div>
        <div>
          <div class="tw-time" id="timerDisplay">00:00:00</div>
          <div class="tw-lbl" id="timerLabel">Not started</div>
        </div>
        <div class="tw-status status-idle" id="timerStatus">Idle</div>
      </div>
      <div class="location-badge" id="geoBadge" style="flex-wrap:wrap;">
        <i class="bi bi-geo-alt"></i><span>Location not captured yet</span>
      </div>

      <div class="section-card">
        <div class="section-card-hdr">
          <i class="bi bi-card-text" style="color:#9a8053;"></i>Work Description
        </div>
        <div class="section-card-body">
          <textarea class="d-remark" id="workDesc" rows="3"
                    placeholder="What work is being carried out&hellip;"></textarea>
        </div>
      </div>

      <div class="punch-row">
        <button class="punch-btn btn-punchin" id="punchInBtn">
          <i class="bi bi-play-fill"></i>Start Job
        </button>
        <button class="punch-btn btn-punchout" id="punchOutBtn" disabled>
          <i class="bi bi-check2-square"></i>Finish Job
        </button>
      </div>

      <button class="btn-outline" id="expenseBtn" disabled>
        <i class="bi bi-receipt"></i>Log Material Expense
      </button>

      <button class="btn-outline brand hidden" id="rsBtn">
        <i class="bi bi-calendar2-event"></i>Reschedule / Hold Job
      </button>

      <div class="lock-info hidden" id="lockInfo">
        <i class="bi bi-lock-fill"></i>
       <span>
          Finish Job is locked. Upload at least one <strong>Before</strong> and
          one <strong>After</strong> photo to unlock.
        </span>
      </div>

      <div class="compliance-card">
        <div class="comp-title"><i class="bi bi-shield-check"></i>Compliance Uploads</div>

        <div class="comp-item">
          <div class="comp-icon-wrap" id="beforeIcon" style="background:rgba(251,188,6,.1);">
            <i class="bi bi-camera-fill" style="color:#fbbc06;"></i>
          </div>
          <div class="comp-info">
            <div class="comp-lbl">Before Photos</div>
            <div class="comp-hint">Site condition before work</div>
          </div>
          <span class="comp-status cs-pending" id="beforeStatus">Pending</span>
          <button class="comp-upload-btn" data-upload="before" disabled>
            <i class="bi bi-camera"></i>Add
          </button>
          <input type="file" id="beforeInput" class="upload-input" accept="image/*" capture="environment" multiple/>
        </div>
        <div class="photo-strip" id="beforeStrip"></div>

        <div class="comp-item">
          <div class="comp-icon-wrap" id="afterIcon" style="background:rgba(154,128,83,.1);">
            <i class="bi bi-camera-fill" style="color:#9a8053;"></i>
          </div>
          <div class="comp-info">
            <div class="comp-lbl">After Photos</div>
            <div class="comp-hint">Site condition after work</div>
          </div>
          <span class="comp-status cs-pending" id="afterStatus">Pending</span>
          <button class="comp-upload-btn" data-upload="after" disabled>
            <i class="bi bi-camera"></i>Add
          </button>
          <input type="file" id="afterInput" class="upload-input" accept="image/*" capture="environment" multiple/>
        </div>
        <div class="photo-strip" id="afterStrip"></div>
      </div>

      <div class="section-card">
        <div class="section-card-hdr">
          <i class="bi bi-receipt" style="color:#fbbc06;"></i>Material Expenses
        </div>
        <div class="section-card-body" id="expenseListWrap">
          <div class="exp-empty">No expenses logged yet.</div>
        </div>
      </div>
    </div>
  </section>

  <!-- ── HISTORY ── -->
  <section id="pageHistory" class="hidden">
    <div class="page-band">
      <div>
        <div class="pg-title"><i class="bi bi-clock-history" style="color:#9a8053;"></i>Work History</div>
        <div class="pg-sub" id="histSub">Loading&hellip;</div>
      </div>
      <span class="pg-count" id="histCount">0 jobs</span>
    </div>

    <div class="page-content">
      <div class="stat-row">
        <div class="stat-box"><div class="stat-val" id="statJobs">&mdash;</div><div class="stat-lbl">Jobs</div></div>
        <div class="stat-box"><div class="stat-val" id="statHours">&mdash;</div><div class="stat-lbl">Hours</div></div>
        <div class="stat-box"><div class="stat-val" id="statExp">&mdash;</div><div class="stat-lbl">AED Mat.</div></div>
      </div>
      <div id="histList">
        <div class="skel"></div><div class="skel"></div><div class="skel"></div>
      </div>
    </div>
  </section>

  <!-- ── PROFILE ── -->
  <section id="pageProfile" class="hidden">
    <div class="page-band">
      <div>
        <div class="pg-title"><i class="bi bi-person-circle" style="color:#9a8053;"></i>My Profile</div>
        <div class="pg-sub">Account &amp; performance</div>
      </div>
      <button class="th-btn" id="profRefresh" style="background:var(--surface-2);color:var(--text-muted);"
              title="Refresh" aria-label="Refresh">
        <i class="bi bi-arrow-clockwise"></i>
      </button>
    </div>

    <div class="page-content" id="profBody">
      <div class="skel" style="height:170px;"></div>
      <div class="skel" style="height:120px;"></div>
    </div>
  </section>

  <nav class="bottom-nav" id="bottomNav">
    <button class="bn-item active" data-nav="pipeline">
      <i class="bi bi-list-task"></i>Pipeline
    </button>
    <button class="bn-item" data-nav="terminal">
      <span class="bn-badge">
        <i class="bi bi-broadcast"></i>
        <span class="bn-dot hidden" id="activeDot"></span>
      </span>
      Active
    </button>
    <button class="bn-item" data-nav="history">
      <i class="bi bi-clock-history"></i>History
    </button>
    <button class="bn-item" data-nav="profile">
      <i class="bi bi-person-circle"></i>Profile
    </button>
  </nav>
<form method="POST" action="{{ route('worker.logout') }}" id="logoutForm" style="display:none;">
    @csrf
  </form>
</div><!-- /app-shell -->

<script>
'use strict';

/* ══════════════════════════════════════════════════════
   BACKEND DATA
══════════════════════════════════════════════════════ */
const JOBS      = @json($jobs ?? []);
const ACTIVE    = @json($activeJob ?? null);
const CSRF = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
const ROUTES = {
  accept:     @json($routes['accept']     ?? ''),
  punchIn:    @json($routes['punchIn']    ?? ''),
  punchOut:   @json($routes['punchOut']   ?? ''),
  upload:     @json($routes['upload']     ?? ''),
  expense:    @json($routes['expense']    ?? ''),
  reschedule: @json($routes['reschedule'] ?? ''),
  hold:       @json($routes['hold']       ?? ''),
  resume:     @json($routes['resume']     ?? ''),
  history:    @json($routes['history']    ?? ''),
  profile:    @json($routes['profile']    ?? ''),
  signature:  @json($routes['signature'] ?? ''),
  photoDelete: @json($routes['photoDelete'] ?? ''),
  changePassword: @json($routes['changePassword'] ?? ''),
};

const LETTERHEAD = @json($letterhead ?? ['header'=>null,'footer'=>null,'watermark'=>null]);
/* ══════════════════════════════════════════════════════
   STATE
   activeRef  = display ref ("SR-2026-000123") -> DOM ids
   activeSrId = numeric primary key            -> API calls
══════════════════════════════════════════════════════ */
let activeRef      = null;
let activeSrId     = null;
let punchInTime    = null;
let timerInterval  = null;
let expandedRef    = null;
let currentFilter  = 'Pending';
let uploads        = { before: [], after: [] };
let expenses       = [];
let historyLoaded  = false;
let profileLoaded  = false;
let signatureUploaded = false;
/* ══════════════════════════════════════════════════════
   HELPERS
══════════════════════════════════════════════════════ */
const $ = (id) => document.getElementById(id);

/** Escape for HTML text and quoted attribute contexts. */
function esc(value) {
  return String(value ?? '')
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#39;');
}

/* ══════════════════════════════════════════════════════
   GEOLOCATION
══════════════════════════════════════════════════════ */
let lastFix = null;   // { lat, lng, accuracy, address }

/**
 * Ask the browser for a position. Resolves to null rather than rejecting —
 * a denied permission must not block the punch.
 */
function getPosition({ timeout = 12000, highAccuracy = true } = {}) {
  return new Promise((resolve) => {
    if (!navigator.geolocation) { resolve(null); return; }

    navigator.geolocation.getCurrentPosition(
      (pos) => resolve({
        lat: +pos.coords.latitude.toFixed(7),
        lng: +pos.coords.longitude.toFixed(7),
        accuracy: pos.coords.accuracy != null ? +pos.coords.accuracy.toFixed(2) : null,
      }),
      () => resolve(null),
      { enableHighAccuracy: highAccuracy, timeout, maximumAge: 30000 }
    );
  });
}

/** Reverse-geocode via OSM. Failure is non-fatal — coords alone are enough. */
async function reverseGeocode(lat, lng) {
  try {
    const url = `https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lng}`;
    const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
    if (!res.ok) return null;
    const body = await res.json();
    return body.display_name ?? null;
  } catch { return null; }
}

/** Capture a fix and (best-effort) its address. Always resolves. */
const MAX_ACCEPTABLE_ACCURACY = 100;   // metres

async function captureLocation() {
  const fix = await getPosition();
  if (!fix) { lastFix = null; renderLocationBadge(null); return null; }

  // A coarse fix on desktop is usually IP-based and can be hundreds of km off.
  fix.coarse = fix.accuracy != null && fix.accuracy > MAX_ACCEPTABLE_ACCURACY;

  fix.address = await reverseGeocode(fix.lat, fix.lng);
  lastFix = fix;
  renderLocationBadge(fix);
  return fix;
}

/** Append lat/lng/accuracy/address to a payload object or FormData. */
function withGeo(payload, fix) {
  if (!fix) return payload;

  if (payload instanceof FormData) {
    payload.append('lat', fix.lat);
    payload.append('lng', fix.lng);
    if (fix.accuracy != null) payload.append('accuracy', fix.accuracy);
    if (fix.address) payload.append('address', fix.address);
    payload.append('coarse', fix.coarse ? 1 : 0);
    return payload;
  }
  return { ...payload, lat: fix.lat, lng: fix.lng, accuracy: fix.accuracy,
           address: fix.address, coarse: fix.coarse };
}

function renderLocationBadge(fix) {
  const el = $('geoBadge');
  if (!el) return;

  if (!fix) {
    el.innerHTML =
      '<i class="bi bi-geo-alt-slash"></i>' +
      '<span>Location unavailable &mdash; enable GPS for site verification</span>';
    return;
  }

 el.innerHTML =
    `<i class="bi bi-geo-alt-fill" style="color:${fix.coarse ? '#fbbc06' : '#9a8053'};"></i>` +
    `<span class="coords">${fix.lat.toFixed(5)}, ${fix.lng.toFixed(5)}</span>` +
    (fix.accuracy != null ? `<span style="margin-left:auto;">&plusmn;${Math.round(fix.accuracy)}m</span>` : '') +
    (fix.coarse ? '<div style="flex-basis:100%;margin-top:4px;color:#a8802a;">Approximate — network-based fix, not GPS. Use the mobile app on site.</div>' : '') +
    (fix.address ? `<div style="flex-basis:100%;margin-top:4px;">${esc(fix.address)}</div>` : '');
}

/** Today as YYYY-MM-DD in the *local* timezone (toISOString would shift it). */
function todayLocal() {
  const d = new Date();
  const pad = (n) => String(n).padStart(2, '0');
  return `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}`;
}

function hhmm(date) {
  return date.toLocaleTimeString('en-IN', { hour:'2-digit', minute:'2-digit' });
}

/** POST helper. Surfaces the server's validation message rather than a bare status. */
async function apiPost(url, payload, isForm = false) {
  if (!url) throw new Error('Endpoint not configured.');

  const opts = {
    method: 'POST',
    headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
    body: isForm ? payload : JSON.stringify(payload),
  };
  if (!isForm) opts.headers['Content-Type'] = 'application/json';

  const res = await fetch(url, opts);

  if (!res.ok) {
    let msg = `Request failed (${res.status})`;
    try {
      const body = await res.json();
      // Laravel 422 puts field errors under `errors`, the summary under `message`.
      if (body.errors) msg = Object.values(body.errors).flat()[0] ?? msg;
      else if (body.message) msg = body.message;
    } catch { /* non-JSON error body; keep the status message */ }
    throw new Error(msg);
  }
  return res.json();
}

async function apiGet(url) {
  if (!url) throw new Error('Endpoint not configured.');
  const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
  if (!res.ok) throw new Error(`Request failed (${res.status})`);
  return res.json();
}

/** Swap a button into a busy state; returns a restore() closure. */
function busy(btn, label) {
  const original = btn.innerHTML;
  const wasDisabled = btn.disabled;
  btn.disabled = true;
  btn.innerHTML = `<span class="spin"></span> ${esc(label)}`;
  return () => { btn.innerHTML = original; btn.disabled = wasDisabled; };
}

function showToast(type, title, body) {
  const icons = {
    success: 'bi-check-circle-fill',
    error:   'bi-x-circle-fill',
    warning: 'bi-exclamation-circle-fill',
    primary: 'bi-info-circle-fill',
  };
  const el = document.createElement('div');
  el.className = `toast-item ${type}`;
  el.innerHTML =
    `<i class="bi ${icons[type] ?? icons.primary} ti-ico ${type}"></i>` +
    `<div><p class="ti-t">${esc(title)}</p><p class="ti-b">${esc(body)}</p></div>`;
  $('toastWrap').appendChild(el);
  setTimeout(() => {
    el.style.transition = 'opacity .3s';
    el.style.opacity = '0';
    setTimeout(() => el.remove(), 300);
  }, 4000);
}

/* ══════════════════════════════════════════════════════
   THEME + CLOCK
══════════════════════════════════════════════════════ */
function applyTheme(dark) {
  document.documentElement.setAttribute('data-bs-theme', dark ? 'dark' : 'light');
  $('themeIcon').className = dark ? 'bi bi-moon-stars-fill' : 'bi bi-sun-fill';
  localStorage.setItem('ml_theme', dark ? 'dark' : 'light');
}

(function initTheme() {
  const saved = localStorage.getItem('ml_theme');
  applyTheme(saved ? saved === 'dark' : window.matchMedia('(prefers-color-scheme:dark)').matches);
})();

$('themeBtn').addEventListener('click', () => {
  applyTheme(document.documentElement.getAttribute('data-bs-theme') !== 'dark');
});

$('logoutBtn').addEventListener('click', () => {
  if (punchInTime) {
    showToast('warning', 'Job in progress',
      'Finish or hold the current job before signing out.');
    return;
  }
  openDrawer('out');
});

$('outConfirmBtn').addEventListener('click', () => {
  const btn = $('outConfirmBtn');
  btn.disabled = true;
  btn.innerHTML = '<span class="spin"></span> Signing out\u2026';
  $('logoutForm').submit();
});

(function initClock() {
  const tick = () => { $('clock').textContent = hhmm(new Date()); };
  tick();
  setInterval(tick, 1000);
})();

/* ══════════════════════════════════════════════════════
   NAVIGATION
══════════════════════════════════════════════════════ */
const PAGES = {
  pipeline: 'pagePipeline',
  terminal: 'pageTerminal',
  history:  'pageHistory',
  profile:  'pageProfile',
};

function setNav(target) {
  document.querySelectorAll('.bn-item').forEach((b) => {
    b.classList.toggle('active', b.dataset.nav === target);
  });
}

function showPage(target) {
  Object.values(PAGES).forEach((id) => $(id).classList.add('hidden'));
  $(PAGES[target]).classList.remove('hidden');
  setNav(target);
}

function goToPipeline() {
  showPage('pipeline');
  renderPipeline();
}

$('backBtn').addEventListener('click', goToPipeline);

$('bottomNav').addEventListener('click', (e) => {
  const btn = e.target.closest('[data-nav]');
  if (!btn) return;
  const target = btn.dataset.nav;

  if (target === 'pipeline') { goToPipeline(); return; }

  if (target === 'terminal') {
    if (!activeSrId) { showToast('warning', 'No Active Job', 'Accept a job first.'); return; }
    showPage('terminal');
    return;
  }

  if (target === 'history') { showPage('history'); loadHistory(); return; }
  if (target === 'profile') { showPage('profile'); loadProfile(); }
});

/* ══════════════════════════════════════════════════════
   DRAWERS
══════════════════════════════════════════════════════ */
const DRAWERS = {
  exp: { drawer: 'expenseDrawer', overlay: 'expOverlay' },
  rs:  { drawer: 'rsDrawer',      overlay: 'rsOverlay'  },
  fin: { drawer: 'finishDrawer',  overlay: 'finOverlay' },
  sign: { drawer: 'signDrawer',    overlay: 'signOverlay' },
  out:  { drawer: 'logoutDrawer',  overlay: 'outOverlay' },
};

function openDrawer(name) {
  $(DRAWERS[name].drawer).classList.add('open');
  $(DRAWERS[name].overlay).classList.add('show');
  document.body.style.overflow = 'hidden';
}

function closeDrawer(name) {
  $(DRAWERS[name].drawer).classList.remove('open');
  $(DRAWERS[name].overlay).classList.remove('show');
  document.body.style.overflow = '';
}

Object.entries(DRAWERS).forEach(([name, { overlay }]) => {
  $(overlay).addEventListener('click', () => closeDrawer(name));
});

document.querySelectorAll('[data-close]').forEach((btn) => {
  btn.addEventListener('click', () => closeDrawer(btn.dataset.close));
});

/* ══════════════════════════════════════════════════════
   PIPELINE RENDER
══════════════════════════════════════════════════════ */
function renderPipeline() {
  const list = $('jobList');
  const visible = JOBS.filter((j) => j.status === currentFilter);
  const reworkCount = JOBS.filter((j) => j.status === 'Rework').length;

  $('pipelineCount').textContent = `${visible.length} job${visible.length === 1 ? '' : 's'}`;
  $('pipelineSubtitle').textContent = `${JOBS.length} total \u00b7 ${reworkCount} need rework`;

  if (!visible.length) {
    list.innerHTML =
      '<div class="empty-state"><i class="bi bi-check2-all"></i>' +
      '<h6>All clear!</h6><p>No jobs in this category.</p></div>';
    return;
  }
  list.innerHTML = visible.map(buildJobCard).join('');
}

function buildJobCard(job) {
  const slaClass = job.hrsAgo > 24 ? 'sla-c' : job.hrsAgo > 8 ? 'sla-w' : 'sla-ok';
  const isResched = job.status === 'Rescheduled';
  const isAccepted = job.status === 'Accepted';
const BADGE_MAP = {
  'Pending': 'badge-assigned',
  'Rework': 'badge-rework',
  'Rescheduled': 'badge-rescheduled',
  'On Hold': 'badge-hold',
  'Review': 'badge-review',
  'Completed': 'badge-completed',
};
const badgeClass = BADGE_MAP[job.status] ?? 'badge-assigned';
  const isExpanded = expandedRef === job.id;
  const isActive   = activeRef === job.id;
  const ref = esc(job.id);
  const shortSite = String(job.site ?? '').split(',')[0];

  const reworkBlock = job.reworkNote
    ? `<div class="jc-exp-section">
         <div class="exp-label">
           <i class="bi bi-exclamation-triangle-fill" style="color:#ff3366;"></i>Rework Instructions
         </div>
         <div class="rework-note">${esc(job.reworkNote)}</div>
       </div>`
    : '';

  const historyBlock = (job.history ?? []).length
    ? `<div class="jc-exp-section">
         <div class="exp-label"><i class="bi bi-clock-history" style="color:#B8976A;"></i>History Log</div>
         ${job.history.map((h) => `<div class="hist-line">${esc(h)}</div>`).join('')}
       </div>`
    : '';
  const rescheduleBlock = job.rescheduleReason
  ? `<div class="jc-exp-section">
       <div class="exp-label">
         <i class="bi bi-calendar2-event" style="color:#5878dc;"></i>Reschedule Details
         ${job.rescheduleCount > 1 ? `<span style="margin-left:auto;font-weight:600;">×${Number(job.rescheduleCount)}</span>` : ''}
       </div>
       <div class="exp-text" style="margin-bottom:6px;">
         ${job.previousEta ? `<span style="text-decoration:line-through;opacity:.6;">${esc(job.previousEta)}</span> &rarr; ` : ''}
         <strong>${esc(job.eta ?? '—')}</strong>
       </div>
       <div class="rework-note" style="background:rgba(88,120,220,.07);border-color:rgba(88,120,220,.2);">
         ${esc(job.rescheduleReason)}
       </div>
       <div style="font-size:.66rem;color:var(--text-muted);margin-top:5px;">
         Logged ${esc(job.rescheduledAt ?? '—')}
       </div>
     </div>`
  : '';
  const photoBlock = (job.attachments ?? []).length
    ? `<div class="jc-exp-section">
         <div class="exp-label"><i class="bi bi-images" style="color:#fbbc06;"></i>Attached Photos</div>
         <div class="photo-row">
           ${job.attachments.map((a) => {
             const isBefore = a === 'before';
             return `<div class="photo-thumb" data-photo="${esc(a)}" data-ref="${ref}">
                       <i class="bi bi-${isBefore ? 'camera-fill' : 'image-fill'}"
                          style="color:${isBefore ? '#fbbc06' : '#9a8053'};"></i>
                     </div>`;
           }).join('')}
         </div>
       </div>`
    : '';
  const onHold = job.status === 'On Hold';

   const footer = isActive
   ? `<div class="jc-active-note">
       <i class="bi bi-check2-circle"></i> This job is currently active in the terminal
     </div>`
  : onHold
  ? `<div class="eta-form">
       <div class="eta-title"><i class="bi bi-pause-circle"></i>Job On Hold</div>
       <button class="accept-btn" data-resume="${ref}" data-srid="${Number(job.sr_id)}">
         <i class="bi bi-play-circle"></i>Resume Job
       </button>
     </div>`
  : isAccepted
  ? `<div class="eta-form">
       <div class="eta-title"><i class="bi bi-check2-circle" style="color:#05a34a;"></i>Accepted &middot; ETA ${esc(job.eta ?? '—')}</div>
       <button class="accept-btn" style="background:linear-gradient(135deg,#05a34a,#0abf56);"
               data-activate="${ref}" data-srid="${Number(job.sr_id)}">
         <i class="bi bi-broadcast"></i>Make Active
       </button>
     </div>`
    : isResched
? `<div class="eta-form">
     <div class="eta-title"><i class="bi bi-calendar2-event" style="color:#5878dc;"></i>Rescheduled &middot; ETA ${esc(job.eta ?? '—')}</div>
     <button class="accept-btn" style="background:linear-gradient(135deg,#5878dc,#7a95e8);"
             data-activate="${ref}" data-srid="${Number(job.sr_id)}">
       <i class="bi bi-broadcast"></i>Make Active
     </button>
     <button class="accept-btn" style="margin-top:8px;background:none;border:1.5px dashed rgba(88,120,220,.5);color:#5878dc;"
             data-rsagain="${ref}" data-srid="${Number(job.sr_id)}">
       <i class="bi bi-calendar2-event"></i>Reschedule Again
     </button>
   </div>`
    : `<div class="eta-form">
         <div class="eta-title"><i class="bi bi-calendar-check"></i>Set Expected Attendance (ETA)</div>
         <div class="eta-grid">
           <div class="eta-field">
             <label for="etaDate-${ref}">Date <span class="req">*</span></label>
             <input type="date" class="eta-input" id="etaDate-${ref}"
                    min="${todayLocal()}" data-eta="${ref}"/>
             <div class="err-msg" id="errDate-${ref}">Date required</div>
           </div>
           <div class="eta-field">
             <label for="etaTime-${ref}">Time <span class="req">*</span></label>
             <input type="time" class="eta-input" id="etaTime-${ref}" data-eta="${ref}"/>
             <div class="err-msg" id="errTime-${ref}">Time required</div>
           </div>
         </div>
         <button class="accept-btn" id="acceptBtn-${ref}"
                 data-accept="${ref}" data-srid="${Number(job.sr_id)}" disabled>
           <i class="bi bi-check2-circle"></i>Accept Job
         </button>
         <div class="wa-hint">
           <i class="bi bi-whatsapp"></i>Client receives a live tracking link on acceptance
         </div>
       </div>`;

  return `
    <article class="job-card${isActive ? ' is-active' : ''}${isAccepted && !isActive ? ' is-accepted' : ''}" id="jcard-${ref}">
      <div class="jc-main" data-toggle="${ref}">
        <div class="jc-top">
          <span class="jc-sr">${ref}</span>
          <div style="display:flex;gap:6px;align-items:center;">
            <span class="jc-badge ${badgeClass}">${esc(job.status)}</span>
            ${isActive ? '<span class="jc-badge badge-live">&#9679; Active</span>' : ''}
            <i class="bi bi-chevron-down jc-chevron${isExpanded ? ' open' : ''}"></i>
          </div>
        </div>
        <div class="jc-client">${esc(job.client)}</div>
        <div class="jc-contract">
          <i class="bi bi-file-earmark-text"></i> ${esc(job.contract)}
        </div>
        <div class="jc-meta">
          <span class="jc-meta-item"><i class="bi bi-tools"></i>${esc(job.domain)}</span>
          <span class="jc-meta-item"><i class="bi bi-geo-alt"></i>${esc(shortSite)}</span>
          <span class="jc-sla ${slaClass}"><i class="bi bi-clock"></i>${Number(job.hrsAgo)}h</span>
        </div>
      </div>

      <div class="jc-expand${isExpanded ? ' open' : ''}" id="jexp-${ref}">
        <div class="jc-exp-section">
          <div class="exp-label"><i class="bi bi-geo-alt-fill" style="color:#9a8053;"></i>Site Address</div>
          <div class="exp-text">${esc(job.site)}</div>
        </div>
        <div class="jc-exp-section">
          <div class="exp-label"><i class="bi bi-card-text" style="color:#9a8053;"></i>Issue Description</div>
          <div class="exp-text">${esc(job.description)}</div>
        </div>
        ${reworkBlock}
        ${historyBlock}
        ${photoBlock}
        ${footer}
      </div>
    </article>`;
}

/**
 * Toggle in place rather than re-rendering the list — a full re-render would
 * discard any ETA the user has typed into a sibling card.
 */
function toggleExpand(ref) {
  const opening = expandedRef !== ref;

  if (expandedRef) {
    $(`jexp-${expandedRef}`)?.classList.remove('open');
    document.querySelector(`#jcard-${CSS.escape(expandedRef)} .jc-chevron`)?.classList.remove('open');
  }
  expandedRef = opening ? ref : null;

  if (opening) {
    $(`jexp-${ref}`)?.classList.add('open');
    document.querySelector(`#jcard-${CSS.escape(ref)} .jc-chevron`)?.classList.add('open');
  }
}

/* ══════════════════════════════════════════════════════
   DELEGATED EVENTS — PIPELINE
══════════════════════════════════════════════════════ */
$('jobList').addEventListener('click', (e) => {
  const toggle = e.target.closest('[data-toggle]');
  if (toggle) { toggleExpand(toggle.dataset.toggle); return; }

  const photo = e.target.closest('[data-photo]');
  if (photo) { openLightbox(photo.dataset.photo, photo.dataset.ref); return; }

  const resume = e.target.closest('[data-resume]');
  if (resume) { resumeJob(resume.dataset.resume, Number(resume.dataset.srid), resume); return; }

  const rsAgain = e.target.closest('[data-rsagain]');
  if (rsAgain) {
    activeRef  = rsAgain.dataset.rsagain;
    activeSrId = Number(rsAgain.dataset.srid);
    $('rsSrRef').textContent = activeRef;
    $('rsDate').min = todayLocal();
    $('rsDate').value = ''; $('rsTime').value = '';
    $('rsRemark').value = ''; $('holdRemark').value = '';
    switchRsTab('reschedule');
    openDrawer('rs');
    return;
  }

  const activate = e.target.closest('[data-activate]');
  if (activate) { activateJob(activate.dataset.activate, Number(activate.dataset.srid)); return; }

  const accept = e.target.closest('[data-accept]');
  if (accept) { acceptJob(accept.dataset.accept, Number(accept.dataset.srid), accept); }
});

$('jobList').addEventListener('change', (e) => {
  const input = e.target.closest('[data-eta]');
  if (!input) return;

  const ref  = input.dataset.eta;
  const date = $(`etaDate-${ref}`)?.value;
  const time = $(`etaTime-${ref}`)?.value;
  const btn  = $(`acceptBtn-${ref}`);
  if (btn) btn.disabled = !(date && time);

  input.classList.remove('err');
  $(`errDate-${ref}`)?.classList.remove('show');
  $(`errTime-${ref}`)?.classList.remove('show');
});

$('tabFilter').addEventListener('click', (e) => {
  const btn = e.target.closest('[data-filter]');
  if (!btn) return;
  currentFilter = btn.dataset.filter;
  document.querySelectorAll('.tf-btn').forEach((b) => b.classList.remove('active'));
  btn.classList.add('active');
  renderPipeline();
});


/* ══════════════════════════════════════════════════════
   ACCEPT JOB
══════════════════════════════════════════════════════ */
async function acceptJob(ref, srId, btn) {
  const date = $(`etaDate-${ref}`).value;
  const time = $(`etaTime-${ref}`).value;

  if (!date) { $(`errDate-${ref}`).classList.add('show'); $(`etaDate-${ref}`).classList.add('err'); return; }
  if (!time) { $(`errTime-${ref}`).classList.add('show'); $(`etaTime-${ref}`).classList.add('err'); return; }

  const restore = busy(btn, 'Accepting\u2026');

  try {
    await apiPost(ROUTES.accept, { sr_id: srId, eta_date: date, eta_time: time });

    // Reset per-job terminal state so a second job never inherits the first's.
    activeRef   = ref;
    activeSrId  = srId;
    punchInTime = null;
    uploads     = { before: [], after: [] };
    expenses    = [];
    clearInterval(timerInterval);

    const job = JOBS.find((j) => j.id === ref);
    job.status   = 'Accepted';
    job.accepted = true;
    job.eta      = `${date} ${time}`;
    buildBanner(job, date, time);

    renderPipeline();   // repaint the card so it shows the "active" footer
    openTerminal();
  } catch (err) {
    restore();
    showToast('error', 'Could not accept job', err.message);
  }
}

async function resumeJob(ref, srId, btn) {
  const restore = busy(btn, 'Resuming\u2026');

  try {
    await apiPost(ROUTES.resume, { sr_id: srId });

    activeRef   = ref;
    activeSrId  = srId;
    punchInTime = null;
    uploads     = { before: [], after: [] };
    expenses    = [];
    clearInterval(timerInterval);

    const job = JOBS.find((j) => j.id === ref);
    job.status = 'Assigned';

    const [etaDate, etaTime] = String(job.eta ?? '').split(' ');
    buildBanner(job, etaDate || '\u2014', etaTime || '\u2014');

    $('activeDot').classList.remove('hidden');
    showToast('success', 'Job Resumed', 'Back in the terminal.');

    renderPipeline();
    openTerminal();
  } catch (err) {
    restore();
    showToast('error', 'Could not resume', err.message);
  }
}

function buildBanner(job, etaDate, etaTime) {
  const slaColor = job.hrsAgo > 24 ? '#ff5080' : job.hrsAgo > 8 ? '#fbbc06' : '#a8f0c0';
  const shortSite = String(job.site ?? '').split(',')[0];

  $('jobBanner').innerHTML = `
    <div class="ajb-sr">${esc(job.id)} \u00b7 ${esc(job.domain)}</div>
    <div class="ajb-client">${esc(job.client)}</div>
    <div class="ajb-site"><i class="bi bi-geo-alt-fill"></i>${esc(shortSite)}</div>
    <div class="ajb-meta">
      <span class="ajb-chip"><i class="bi bi-calendar3"></i>ETA ${esc(etaDate)} ${esc(etaTime)}</span>
      <span class="ajb-chip"><i class="bi bi-exclamation-circle"></i>${esc(job.priority)} Priority</span>
      <span class="ajb-chip" style="color:${slaColor};">
        <i class="bi bi-clock"></i>${Number(job.hrsAgo)}h elapsed
      </span>
    </div>`;

  $('termTitle').innerHTML =
    `<i class="bi bi-broadcast" style="color:#8A6E47;"></i>${esc(job.id)}`;
  $('termSub').textContent  = job.client;
  $('expSrRef').textContent = job.id;
  $('rsSrRef').textContent  = job.id;
  $('finSrRef').textContent = job.id;
}

/* ══════════════════════════════════════════════════════
   TERMINAL
══════════════════════════════════════════════════════ */
function openTerminal() {
  showPage('terminal');

  // Fresh terminal: nothing punched, nothing uploaded.
  $('punchInBtn').disabled  = false;
  $('punchInBtn').innerHTML = '<i class="bi bi-play-fill"></i>Start Job';
  $('punchOutBtn').disabled  = true;
  $('punchOutBtn').innerHTML = '<i class="bi bi-check2-square"></i>Finish Job';
  $('expenseBtn').disabled = true;
  $('rsBtn').classList.add('hidden');
  $('lockInfo').classList.add('hidden');

  $('timerDisplay').textContent = '00:00:00';
  $('timerLabel').textContent   = 'Not started';
  $('timerStatus').textContent  = 'Idle';
  $('timerStatus').className    = 'tw-status status-idle';
  $('timerIcon').innerHTML = '<i class="bi bi-clock" style="color:#aeb7c5;"></i>';
  $('timerIcon').style.background = 'rgba(174,183,197,.12)';

  uploads = { before: [], after: [] };
  ['before', 'after'].forEach((type) => {
    renderPhotoStrip(type);
    document.querySelector(`[data-upload="${type}"]`).disabled = true;
  });

  $('workDesc').value = '';
  $('finSummary').value = '';
}

/* ══════════════════════════════════════════════════════
   PUNCH IN
══════════════════════════════════════════════════════ */
$('punchInBtn').addEventListener('click', () => {
  const btn = $('punchInBtn');
  btn.disabled = true;
  btn.innerHTML = '<span class="spin"></span> Starting\u2026';
  punchIn();
});

async function punchIn() {
    const btn = $('punchInBtn');
    if (!activeSrId) {
      btn.disabled = false;
      btn.innerHTML = '<i class="bi bi-play-fill"></i>Start Job';
      showToast('error', 'No active job', 'Re-activate the job first.');
      return;
    }

    btn.innerHTML = '<span class="spin"></span> Locating\u2026';
    const fix = await captureLocation();
    btn.innerHTML = '<span class="spin"></span> Starting\u2026';

    if (!fix) {
      showToast('warning', 'No GPS fix', 'Starting without location. Enable GPS if possible.');
    }

    try {
      const res = await apiPost(ROUTES.punchIn, withGeo({
        sr_id: activeSrId,
        work_description: $('workDesc').value.trim() || null,
      }, fix));

    punchInTime = new Date(res.punch_in_at);

    clearInterval(timerInterval);
    timerInterval = setInterval(updateTimer, 1000);
    updateTimer();

    $('timerIcon').innerHTML = '<i class="bi bi-stopwatch-fill" style="color:#05a34a;"></i>';
    $('timerIcon').style.background = 'rgba(5,163,74,.1)';
    $('timerStatus').textContent = 'Live';
    $('timerStatus').className   = 'tw-status status-live';
    $('timerLabel').textContent  = 'Time on site';

    document.querySelectorAll('[data-upload]').forEach((b) => { b.disabled = false; });
    $('expenseBtn').disabled = false;
    $('rsBtn').classList.remove('hidden');
    btn.innerHTML = '<i class="bi bi-check2"></i>Job Started';

    showToast('success', 'Punched In', 'Job started.');
    refreshLock();
  } catch (err) {
    btn.disabled = false;
    btn.innerHTML = '<i class="bi bi-play-fill"></i>Start Job';
    showToast('error', 'Punch-in failed', err.message);
  }
}

function updateTimer() {
  if (!punchInTime) return;
  const secs = Math.max(0, Math.floor((Date.now() - punchInTime.getTime()) / 1000));
  const pad = (n) => String(n).padStart(2, '0');
  $('timerDisplay').textContent =
    `${pad(Math.floor(secs / 3600))}:${pad(Math.floor((secs % 3600) / 60))}:${pad(secs % 60)}`;
}

/* ══════════════════════════════════════════════════════
   COMPLIANCE UPLOADS
══════════════════════════════════════════════════════ */
document.querySelectorAll('[data-upload]').forEach((btn) => {
  btn.addEventListener('click', () => $(`${btn.dataset.upload}Input`).click());
});

['before', 'after'].forEach((type) => {
  $(`${type}Input`).addEventListener('change', (e) => uploadFile(type, e.target));
});

async function uploadFile(type, input) {
  if (!input.files.length) return;

  const btn = document.querySelector(`[data-upload="${type}"]`);
  const restore = busy(btn, '');

  const form = new FormData();
  form.append('sr_id', activeSrId);
  form.append('type', type);
  for (const f of input.files) form.append('files[]', f);

  const labels = { before:'Before Photos', after:'After Photos' };

  try {
    const res = await apiPost(ROUTES.upload, form, true);
    uploads[type].push(...res.photos);
    restore();
    renderPhotoStrip(type);
    showToast('success', `${labels[type]} Uploaded`,
      `${res.photos.length} photo${res.photos.length === 1 ? '' : 's'} added.`);
    refreshLock();
  } catch (err) {
    restore();
    showToast('error', `${labels[type]} failed`, err.message);
  } finally {
    input.value = '';
  }
}

function renderPhotoStrip(type) {
  const strip = $(`${type}Strip`);
  const list  = uploads[type];

  strip.innerHTML = list.map((p) => `
    <div class="ps-item">
      <img src="${esc(p.url)}" alt="${type} photo" data-lb="${esc(p.url)}"/>
      <button class="ps-del" data-del="${Number(p.id)}" data-type="${type}" title="Remove">
        <i class="bi bi-x-lg"></i>
      </button>
    </div>`).join('');

  const tints = { before:'rgba(251,188,6,.15)', after:'rgba(154,128,83,.15)' };
  const done  = list.length > 0;

  $(`${type}Status`).textContent = done ? `${list.length} \u2713` : 'Pending';
  $(`${type}Status`).className   = `comp-status ${done ? 'cs-done' : 'cs-pending'}`;
  $(`${type}Icon`).style.background = done ? tints[type] : '';
}

/* Strip clicks: enlarge or delete. */
['before', 'after'].forEach((type) => {
  $(`${type}Strip`).addEventListener('click', async (e) => {
    const del = e.target.closest('[data-del]');
    if (del) {
      const id = Number(del.dataset.del);
      const t  = del.dataset.type;
      try {
        await apiPost(ROUTES.photoDelete, { sr_id: activeSrId, photo_id: id });
        uploads[t] = uploads[t].filter((p) => p.id !== id);
        renderPhotoStrip(t);
        refreshLock();
      } catch (err) {
        showToast('error', 'Could not remove photo', err.message);
      }
      return;
    }

    const img = e.target.closest('[data-lb]');
    if (img) {
      $('lbContent').innerHTML = `<img src="${esc(img.dataset.lb)}" class="lb-img" alt="Photo"/>`;
      $('lbOverlay').classList.add('show');
    }
  });
});

function refreshLock() {
  const allUploaded = uploads.before.length > 0 && uploads.after.length > 0;
  const punched = Boolean(punchInTime);

  $('punchOutBtn').disabled = !(allUploaded && punched);
  $('lockInfo').classList.toggle('hidden', !punched || allUploaded);
}

/* ══════════════════════════════════════════════════════
   PUNCH OUT
══════════════════════════════════════════════════════ */
$('punchOutBtn').addEventListener('click', () => {
  if (!(uploads.before.length && uploads.after.length)) {
    showToast('error', 'Locked', 'Upload all compliance files first.');
    return;
  }
  if (!signatureUploaded) {
    openSignDrawer();
    return;
  }
  $('finSrRef').textContent = activeRef ?? '\u2014';
  openDrawer('fin');
});

$('finConfirmBtn').addEventListener('click', async () => {
  const btn = $('finConfirmBtn');
  const restore = busy(btn, 'Locating\u2026');

  const fix = await captureLocation();
  btn.innerHTML = '<span class="spin"></span> Processing\u2026';

  const payload = withGeo({
    sr_id:   activeSrId,
    summary: $('finSummary').value.trim() || null,
  }, fix);

  try {
    const res = await apiPost(ROUTES.punchOut, payload);

    closeDrawer('fin');
    clearInterval(timerInterval);

    $('timerStatus').textContent = 'Completed';
    $('timerStatus').className   = 'tw-status status-done';
    $('timerLabel').textContent  = `Total: ${res.duration ?? '\u2014'}`;
    $('punchOutBtn').innerHTML = '<i class="bi bi-check2"></i>Job Finished';
    $('punchOutBtn').disabled  = true;

    showToast('success', 'Job Finished',
      `Duration ${res.duration ?? '\u2014'} \u00b7 AED ${res.grand_total ?? '0.00'} \u00b7 Sent for review.`);

    // Drop the finished job from the local list, then reset.
    const index = JOBS.findIndex((j) => j.id === activeRef);
    if (index > -1) JOBS.splice(index, 1);

    activeRef = activeSrId = punchInTime = null;
    uploads  = { before: [], after: [] };
    expenses = [];
    historyLoaded = false;
    profileLoaded = false;

    $('activeDot').classList.add('hidden');
    $('punchInBtn').disabled = true;
    $('expenseBtn').disabled = true;
    $('rsBtn').classList.add('hidden');

    setTimeout(goToPipeline, 1800);
  } catch (err) {
    restore();
    showToast('error', 'Could not finish job', err.message);
  }
});

/* ══════════════════════════════════════════════════════
   EXPENSE DRAWER
══════════════════════════════════════════════════════ */
$('expenseBtn').addEventListener('click', () => openDrawer('exp'));

$('expCategory').addEventListener('change', (e) => {
  $('expNameWrap').classList.toggle('hidden', !e.target.value);
  if (e.target.value) $('expName').focus();
});

$('expReceipt').addEventListener('change', (e) => {
  if (!e.target.files.length) return;
  $('receiptName').textContent = e.target.files[0].name;
  $('receiptPreview').classList.add('show');
});

$('expSaveBtn').addEventListener('click', async () => {
  const category = $('expCategory').value;
  const name     = $('expName').value.trim();
  const amount   = $('expAmount').value;

  if (!category) { showToast('warning', 'Missing', 'Select an expense category.'); return; }
  if (!name)     { showToast('warning', 'Missing', 'Enter an item name.'); return; }
  if (!amount || parseFloat(amount) <= 0) { showToast('warning', 'Missing', 'Enter a valid amount.'); return; }

  const btn = $('expSaveBtn');
  const restore = busy(btn, 'Saving\u2026');

  const form = new FormData();
  form.append('sr_id', activeSrId);
  form.append('category', category);
  form.append('name', name);
  form.append('amount', amount);
  if ($('expReceipt').files.length) form.append('receipt', $('expReceipt').files[0]);

  try {
    const res = await apiPost(ROUTES.expense, form, true);

    expenses.push({
      category,
      name,
      amount: parseFloat(amount).toFixed(2),
      time: hhmm(new Date()),
      receiptUrl: res.receipt_url ?? null,
    });

    restore();
    resetExpenseForm();
    closeDrawer('exp');
    renderExpenses();
    showToast('success', 'Expense Saved', `AED ${parseFloat(amount).toFixed(2)} \u2014 ${name}`);
  } catch (err) {
    restore();
    showToast('error', 'Could not save expense', err.message);
  }
});

function resetExpenseForm() {
  $('expCategory').value = '';
  $('expName').value = '';
  $('expNameWrap').classList.add('hidden');
  $('expAmount').value = '';
  $('expReceipt').value = '';
  $('receiptPreview').classList.remove('show');
}

function renderExpenses() {
  const wrap = $('expenseListWrap');

  if (!expenses.length) {
    wrap.innerHTML = '<div class="exp-empty">No expenses logged yet.</div>';
    return;
  }

  const total = expenses.reduce((sum, e) => sum + parseFloat(e.amount), 0).toFixed(2);

  wrap.innerHTML =
    expenses.map((e) => `
      <div class="exp-item">
        ${e.receiptUrl
          ? `<img src="${esc(e.receiptUrl)}" class="exp-receipt" alt="Receipt"
                  data-receipt="${esc(e.receiptUrl)}"/>`
          : '<i class="bi bi-receipt" style="color:#fbbc06;"></i>'}
        <div class="body">
          <div class="en">${esc(e.name ?? e.category)}</div>
          <div class="ec">${esc(e.category)}</div>
        </div>
        <span class="ea">AED ${esc(e.amount)}</span>
        <span class="er">${esc(e.time)}</span>
      </div>`).join('') +
    `<div class="exp-total">Total: AED ${total}</div>`;
}

/* Tap a receipt thumbnail in the expense list to enlarge it. */
$('expenseListWrap').addEventListener('click', (e) => {
  const img = e.target.closest('[data-receipt]');
  if (!img) return;
  $('lbContent').innerHTML =
    `<img src="${esc(img.dataset.receipt)}" class="lb-img" alt="Receipt"/>`;
  $('lbOverlay').classList.add('show');
});

/* ══════════════════════════════════════════════════════
   RESCHEDULE / HOLD DRAWER
══════════════════════════════════════════════════════ */
$('rsBtn').addEventListener('click', () => {
  $('rsSrRef').textContent = activeRef ?? '\u2014';
  $('rsDate').min = todayLocal();
  $('rsDate').value = '';
  $('rsTime').value = '';
  $('rsRemark').value = '';
  $('holdRemark').value = '';
  switchRsTab('reschedule');
  openDrawer('rs');
});

document.querySelectorAll('[data-rstab]').forEach((tab) => {
  tab.addEventListener('click', () => switchRsTab(tab.dataset.rstab));
});

function switchRsTab(name) {
  document.querySelectorAll('.rs-tab').forEach((t) => {
    t.classList.toggle('active', t.dataset.rstab === name);
  });
  document.querySelectorAll('.rs-panel').forEach((p) => p.classList.remove('show'));
  $(`rsPanel-${name}`).classList.add('show');
}

$('rsConfirmBtn').addEventListener('click', async () => {
  const date   = $('rsDate').value;
  const time   = $('rsTime').value;
  const remark = $('rsRemark').value.trim();
  

  if (!date || !time) { showToast('warning', 'Required', 'Set a new date and time.'); return; }
  if (!remark)        { showToast('warning', 'Required', 'Enter a rescheduling reason.'); return; }

  const btn = $('rsConfirmBtn');
  const restore = busy(btn, 'Saving\u2026');

  try {
    await apiPost(ROUTES.reschedule, { sr_id: activeSrId, eta_date: date, eta_time: time, remark });
    closeDrawer('rs');

    const cameFromTerminal = !$('pageTerminal').classList.contains('hidden');
    if (cameFromTerminal) {
      clearInterval(timerInterval);
      $('timerStatus').textContent = 'Rescheduled';
      $('timerStatus').className   = 'tw-status status-idle';
      $('timerLabel').textContent  = `ETA ${date} ${time}`;
    }

    const index = JOBS.findIndex((j) => j.id === activeRef);
    if (index > -1) {
      JOBS[index].status           = 'Rescheduled';
      JOBS[index].previousEta      = JOBS[index].eta;
      JOBS[index].eta              = `${date} ${time}`;
      JOBS[index].rescheduleReason = remark;
      JOBS[index].rescheduleCount  = (JOBS[index].rescheduleCount ?? 0) + 1;
      JOBS[index].rescheduledAt    = new Date().toLocaleString('en-GB', {
        day:'2-digit', month:'short', year:'numeric', hour:'2-digit', minute:'2-digit'
      });
    }

    activeRef = activeSrId = punchInTime = null;
    uploads  = { before: [], after: [] };
    expenses = [];
    $('activeDot').classList.add('hidden');

    showToast('success', 'Job Rescheduled', `New ETA: ${date} at ${time}.`);
    setTimeout(goToPipeline, 1400);
  } catch (err) {
    restore();
    showToast('error', 'Could not reschedule', err.message);
  }
});

$('holdConfirmBtn').addEventListener('click', async () => {
  const remark = $('holdRemark').value.trim();
  if (!remark) { showToast('warning', 'Required', 'Enter a reason for the hold.'); return; }

  const btn = $('holdConfirmBtn');
  const restore = busy(btn, 'Saving\u2026');

  try {
    await apiPost(ROUTES.hold, { sr_id: activeSrId, remark });

    closeDrawer('rs');
    clearInterval(timerInterval);
    showToast('warning', 'Job On Hold', 'Placed on hold. Reason logged.');

    // Holding cancels the open punch server-side, so clear the terminal too.
    const index = JOBS.findIndex((j) => j.id === activeRef);
    if (index > -1) {
      JOBS[index].status = 'On Hold';
      JOBS[index].accepted = false;
    }
    activeRef = activeSrId = punchInTime = null;
    uploads  = { before: [], after: [] };
    expenses = [];
    $('activeDot').classList.add('hidden');

    setTimeout(goToPipeline, 1400);
  } catch (err) {
    restore();
    showToast('error', 'Could not hold job', err.message);
  }
});

/* ══════════════════════════════════════════════════════
   CLIENT ACCEPTANCE + SIGNATURE
══════════════════════════════════════════════════════ */
let sigCtx = null, sigDrawing = false, sigHasInk = false;

function openSignDrawer() {
  $('signSrRef').textContent = activeRef ?? '\u2014';
  $('policyCheck').checked = false;
  $('clientNameInput').value = '';
  $('signBlock').classList.add('hidden');
  $('signSubmitBtn').disabled = true;
  openDrawer('sign');
  // Canvas must be sized after the drawer is visible (needs layout width).
  requestAnimationFrame(initSigCanvas);
}

function initSigCanvas() {
  const canvas = $('sigCanvas');
  const ratio = window.devicePixelRatio || 1;
  const rect = canvas.getBoundingClientRect();
  canvas.width  = rect.width  * ratio;
  canvas.height = rect.height * ratio;
  sigCtx = canvas.getContext('2d');
  sigCtx.scale(ratio, ratio);
  sigCtx.lineWidth = 2;
  sigCtx.lineCap = 'round';
  sigCtx.lineJoin = 'round';
  sigCtx.strokeStyle = '#1a2236';
  sigHasInk = false;
}

function sigPos(e) {
  const canvas = $('sigCanvas');
  const rect = canvas.getBoundingClientRect();
  const t = e.touches ? e.touches[0] : e;
  return { x: t.clientX - rect.left, y: t.clientY - rect.top };
}

function sigStart(e) { e.preventDefault(); sigDrawing = true; const p = sigPos(e); sigCtx.beginPath(); sigCtx.moveTo(p.x, p.y); }
function sigMove(e)  { if (!sigDrawing) return; e.preventDefault(); const p = sigPos(e); sigCtx.lineTo(p.x, p.y); sigCtx.stroke(); sigHasInk = true; refreshSignSubmit(); }
function sigEnd()    { sigDrawing = false; }

(function bindSigCanvas() {
  const canvas = $('sigCanvas');
  canvas.addEventListener('mousedown', sigStart);
  canvas.addEventListener('mousemove', sigMove);
  window.addEventListener('mouseup', sigEnd);
  canvas.addEventListener('touchstart', sigStart, { passive:false });
  canvas.addEventListener('touchmove', sigMove, { passive:false });
  canvas.addEventListener('touchend', sigEnd);
})();

$('sigClearBtn').addEventListener('click', () => {
  const canvas = $('sigCanvas');
  sigCtx.clearRect(0, 0, canvas.width, canvas.height);
  sigHasInk = false;
  refreshSignSubmit();
});

$('policyCheck').addEventListener('change', (e) => {
  $('signBlock').classList.toggle('hidden', !e.target.checked);
  if (e.target.checked) requestAnimationFrame(initSigCanvas);
  refreshSignSubmit();
});

$('clientNameInput').addEventListener('input', refreshSignSubmit);

function refreshSignSubmit() {
  const ok = $('policyCheck').checked
    && $('clientNameInput').value.trim().length > 1
    && sigHasInk;
  $('signSubmitBtn').disabled = !ok;
}

$('signSubmitBtn').addEventListener('click', async () => {
  const clientName = $('clientNameInput').value.trim();
  if (!$('policyCheck').checked || !sigHasInk || !clientName) {
    showToast('warning', 'Incomplete', 'Accept the policy and sign first.');
    return;
  }

  const btn = $('signSubmitBtn');
  const restore = busy(btn, 'Locating\u2026');

  const fix = await captureLocation();
  btn.innerHTML = '<span class="spin"></span> Generating\u2026';

  try {
    let pdfBlob = buildAcceptancePdf(clientName, fix);
    pdfBlob = new Blob([pdfBlob], { type: 'application/pdf' });

    const form = new FormData();
    form.append('sr_id', activeSrId);
    form.append('client_name', clientName);
    form.append('signature', pdfBlob, `acceptance-${activeRef}.pdf`);
    withGeo(form, fix);

    const res = await apiPost(ROUTES.signature, form, true);

    signatureUploaded = true;
    restore();
    closeDrawer('sign');
    showToast('success', 'Acceptance Recorded', 'Signature saved. You can finish the job.');

    // Now open the finish drawer.
    $('finSrRef').textContent = activeRef ?? '\u2014';
    openDrawer('fin');
  } catch (err) {
    restore();
    showToast('error', 'Could not save signature', err.message);
  }
});

/** Compose the terms + signature into a one-page PDF. Returns a Blob. */

function buildAcceptancePdf(clientName, fix) {
  const { jsPDF } = window.jspdf;
  const doc = new jsPDF({ unit: 'mm', format: 'a4' });
  const pageW = doc.internal.pageSize.getWidth();
  const pageH = doc.internal.pageSize.getHeight();
  const margin = 18;

  // Match the source PNG aspect ratios so nothing stretches.
  const HEADER_H = pageW * (280 / 1108);   // ~47.3mm  (tall contact block on top)
  const FOOTER_H = pageW * (150 / 1600);   // ~17.5mm  (thin strip on bottom)

function paintChrome() {
  try {
    if (LETTERHEAD && LETTERHEAD.watermark) {
      const wmW = 110, wmH = 110;
      if (doc.setGState) doc.setGState(new doc.GState({ opacity: 0.08 }));
      doc.addImage(LETTERHEAD.watermark, 'PNG',
        (pageW - wmW) / 2,                    // still horizontally centred
        pageH - FOOTER_H - wmH - 6,           // sits 6mm above the footer strip
        wmW, wmH);
      if (doc.setGState) doc.setGState(new doc.GState({ opacity: 1 }));
    }
  } catch (e) { console.warn('watermark skipped:', e); }

  try {
    if (LETTERHEAD && LETTERHEAD.header) {
      doc.addImage(LETTERHEAD.header, 'PNG', 0, 0, pageW, HEADER_H);
    }
  } catch (e) { console.warn('header skipped:', e); }

  try {
    if (LETTERHEAD && LETTERHEAD.footer) {
      doc.addImage(LETTERHEAD.footer, 'PNG', 0, pageH - FOOTER_H, pageW, FOOTER_H);
    }
  } catch (e) { console.warn('footer skipped:', e); }
}

  paintChrome();
  let y = HEADER_H + 10;   // start content below the header

  doc.setFont('helvetica', 'bold');
  doc.setFontSize(15);
  doc.setTextColor(30);
  doc.text('Service Completion Acceptance', margin, y);
  y += 8;

  doc.setFont('helvetica', 'normal');
  doc.setFontSize(9);
  doc.setTextColor(90);
  doc.text(`Job Reference: ${activeRef ?? '-'}`, margin, y); y += 5;
  doc.text(`Date: ${new Date().toLocaleString('en-GB')}`, margin, y); y += 8;

  doc.setTextColor(30);
  doc.setFontSize(10);
  // Pull the terms text straight from the DOM so the PDF matches what was shown.
  const termsText = $('termsScroll').innerText.replace(/\n{2,}/g, '\n\n').trim();
  const lines = doc.splitTextToSize(termsText, pageW - margin * 2);
  doc.text(lines, margin, y);
  y += lines.length * 4.6 + 6;

  doc.setDrawColor(200);
  doc.line(margin, y, pageW - margin, y);
  y += 8;

  doc.setFont('helvetica', 'bold');
  doc.setFontSize(10);
  doc.text('Accepted and signed by:', margin, y);
  y += 6;
  doc.setFont('helvetica', 'normal');
  doc.text(clientName, margin, y);
  y += 6;

  // Signature image from the canvas.
  const sigData = $('sigCanvas').toDataURL('image/png');
  doc.setFontSize(9);
  doc.setTextColor(90);
  doc.text('Signature:', margin, y);
  y += 2;
  const sigW = 70, sigH = 30;
  doc.addImage(sigData, 'PNG', margin, y, sigW, sigH);
  y += sigH + 2;
  doc.setDrawColor(150);
  doc.line(margin, y, margin + sigW, y);
  y += 6;

  if (fix) {
    doc.setFontSize(8);
    doc.setTextColor(120);
    doc.text(`Signed at: ${fix.lat.toFixed(6)}, ${fix.lng.toFixed(6)}`
      + (fix.accuracy != null ? ` (±${Math.round(fix.accuracy)}m)` : ''), margin, y);
    y += 4;
    if (fix.address) {
      doc.text(doc.splitTextToSize(fix.address, pageW - margin * 2), margin, y);
    }
  }

  return doc.output('blob');
}


/* ══════════════════════════════════════════════════════
   LIGHTBOX
══════════════════════════════════════════════════════ */
function openLightbox(type, ref) {
  const isBefore = type === 'before';
  $('lbContent').innerHTML = `
    <div class="lb-ph">
      <i class="bi bi-${isBefore ? 'camera-fill' : 'image-fill'}"
         style="color:${isBefore ? '#fbbc06' : '#9a8053'};font-size:3rem;"></i>
      <div>
        ${isBefore ? 'Before Photo' : 'After Photo'}<br/>
        <span style="font-size:.72rem;opacity:.6;">${esc(ref)}</span>
      </div>
    </div>`;
  $('lbOverlay').classList.add('show');
}

function closeLightbox() { $('lbOverlay').classList.remove('show'); }

$('lbCloseBtn').addEventListener('click', closeLightbox);
$('lbOverlay').addEventListener('click', (e) => {
  if (e.target === $('lbOverlay')) closeLightbox();
});

/* Escape closes whatever is on top. */
document.addEventListener('keydown', (e) => {
  if (e.key !== 'Escape') return;
  if ($('lbOverlay').classList.contains('show'))    { closeLightbox(); return; }
  if ($('logoutDrawer').classList.contains('open')) { closeDrawer('out'); return; }
  if ($('finishDrawer').classList.contains('open')) { closeDrawer('fin'); return; }
  if ($('expenseDrawer').classList.contains('open')){ closeDrawer('exp'); return; }
  if ($('rsDrawer').classList.contains('open'))     { closeDrawer('rs'); }
});

/* ══════════════════════════════════════════════════════
   HISTORY
══════════════════════════════════════════════════════ */
async function loadHistory(force = false) {
  if (historyLoaded && !force) return;

  try {
    const res = await apiGet(ROUTES.history);
    historyLoaded = true;

    $('statJobs').textContent  = res.stats.jobs;
    $('statHours').textContent = res.stats.hours;
    $('statExp').textContent   = res.stats.expenses;

    $('histCount').textContent = `${res.items.length} job${res.items.length === 1 ? '' : 's'}`;
    $('histSub').textContent   = `${res.stats.hours}h logged \u00b7 last 50 jobs`;

    const list = $('histList');
    if (!res.items.length) {
      list.innerHTML =
        '<div class="empty-state"><i class="bi bi-inbox"></i>' +
        '<h6>Nothing yet</h6><p>Finished jobs will appear here.</p></div>';
      return;
    }

    list.innerHTML = res.items.map((h) => `
      <article class="hist-card">
        <div class="hist-top">
          <span class="hist-ref">${esc(h.ref)}</span>
          <div style="display:flex;gap:6px;align-items:center;">
            <span class="jc-badge badge-${esc(h.status)}">${esc(h.status)}</span>
            <span class="hist-date">${esc(h.date)}</span>
          </div>
        </div>
        <div class="hist-client">${esc(h.client)}</div>
        <div class="hist-site"><i class="bi bi-geo-alt"></i> ${esc(String(h.site).split(',')[0])}</div>
        <div class="hist-grid">
          <span class="hist-chip"><i class="bi bi-stopwatch"></i>${esc(h.duration)}</span>
          <span class="hist-chip"><i class="bi bi-clock"></i>${esc(h.in)}\u2013${esc(h.out)}</span>
          <span class="hist-chip"><i class="bi bi-receipt"></i>${Number(h.itemCount)}</span>
          <span class="hist-total">AED ${esc(h.total)}</span>
        </div>
      </article>`).join('');
  } catch (err) {
    $('histList').innerHTML =
      '<div class="empty-state"><i class="bi bi-wifi-off"></i>' +
      `<h6>Could not load</h6><p>${esc(err.message)}</p></div>`;
  }
}

/* ══════════════════════════════════════════════════════
   PROFILE
══════════════════════════════════════════════════════ */
async function loadProfile(force = false) {
  if (profileLoaded && !force) return;

  try {
    const res = await apiGet(ROUTES.profile);
    profileLoaded = true;
    const u = res.user, s = res.stats;

    $('profBody').innerHTML = `
      <div class="prof-hero">
        <div class="prof-avatar">${esc(u.initials)}</div>
        <div class="prof-name">${esc(u.name)}</div>
        <div class="prof-role">
          ${esc(u.role)}${u.code ? ' \u00b7 ' + esc(u.code) : ''}
        </div>
      </div>

      <div class="stat-row">
        <div class="stat-box"><div class="stat-val">${Number(s.open)}</div><div class="stat-lbl">Open</div></div>
        <div class="stat-box"><div class="stat-val">${Number(s.completed)}</div><div class="stat-lbl">Done</div></div>
        <div class="stat-box"><div class="stat-val">${Number(s.hours)}</div><div class="stat-lbl">Hours</div></div>
      </div>

      <div class="compliance-card">
        <div class="comp-title"><i class="bi bi-person-vcard"></i>Account Details</div>
        <div class="info-row">
          <div class="info-ico"><i class="bi bi-envelope-fill"></i></div>
          <div><div class="info-lbl">Email</div><div class="info-val">${esc(u.email)}</div></div>
        </div>
        <div class="info-row">
          <div class="info-ico"><i class="bi bi-telephone-fill"></i></div>
          <div><div class="info-lbl">Phone</div><div class="info-val">${esc(u.phone)}</div></div>
        </div>
        <div class="info-row">
          <div class="info-ico"><i class="bi bi-calendar-event-fill"></i></div>
          <div><div class="info-lbl">Member since</div><div class="info-val">${esc(u.joined)}</div></div>
        </div>
      </div>

      <button class="btn-outline brand" id="profPwdBtn">
        <i class="bi bi-key-fill"></i>Change Password
      </button>

      <button class="btn-outline brand" id="profThemeBtn">
        <i class="bi bi-circle-half"></i>Toggle Theme
      </button>

      <button class="btn-outline" id="profLogoutBtn"
              style="border-color:rgba(255,51,102,.5);color:#ff3366;background:rgba(255,51,102,.05);">
        <i class="bi bi-box-arrow-right"></i>Sign Out
      </button>`;

    $('profPwdBtn').addEventListener('click', openPwdModal);

    $('profThemeBtn').addEventListener('click', () => {
      applyTheme(document.documentElement.getAttribute('data-bs-theme') !== 'dark');
    });

    $('profLogoutBtn').addEventListener('click', () => $('logoutBtn').click());

  } catch (err) {
    profileLoaded = false;
    $('profBody').innerHTML =
      '<div class="empty-state"><i class="bi bi-wifi-off"></i>' +
      `<h6>Could not load</h6><p>${esc(err.message)}</p></div>`;
  }
}


/* ---------- Change password modal ---------- */

function openPwdModal() {
  if (document.getElementById('pwdModal')) return;

  const m = document.createElement('div');
  m.className = 'modal-backdrop';
  m.id = 'pwdModal';
  m.innerHTML = `
    <div class="modal-card" role="dialog" aria-modal="true" aria-label="Change password">
      <div class="comp-title"><i class="bi bi-key-fill"></i>Change Password</div>

      <div id="pwdMsg" class="pwd-msg" style="display:none"></div>

      <input type="password" id="pwdCur"  class="inp" placeholder="Current password"     autocomplete="current-password">
      <input type="password" id="pwdNew"  class="inp" placeholder="New password"         autocomplete="new-password">
      <input type="password" id="pwdConf" class="inp" placeholder="Confirm new password" autocomplete="new-password">

      <div class="modal-actions">
        <button class="btn-outline" id="pwdCancel" type="button">Cancel</button>
        <button class="btn-outline brand" id="pwdSave" type="button">Update</button>
      </div>
    </div>`;

  document.body.appendChild(m);
  document.body.style.overflow = 'hidden';
  $('pwdCur').focus();

  const msg = $('pwdMsg');

  function close() {
    document.removeEventListener('keydown', onKey);
    document.body.style.overflow = '';
    m.remove();
  }

  function onKey(e) {
    if (e.key === 'Escape') close();
    if (e.key === 'Enter' && m.contains(document.activeElement)) submit();
  }

  function show(text, ok = false) {
    msg.textContent = text;
    msg.className = ok ? 'pwd-msg pwd-ok' : 'pwd-msg pwd-err';
    msg.style.display = 'block';
  }

  async function submit() {
    const btn = $('pwdSave');
    if (btn.disabled) return;

    const cur  = $('pwdCur').value;
    const nw   = $('pwdNew').value;
    const conf = $('pwdConf').value;

    msg.style.display = 'none';

    if (!cur || !nw || !conf) return show('All fields are required.');
    if (nw.length < 8)        return show('New password must be at least 8 characters.');
    if (nw !== conf)          return show('New passwords do not match.');
    if (nw === cur)           return show('New password must differ from the current one.');

    btn.disabled = true;
    btn.textContent = 'Saving…';

    try {
      const r = await apiPost(ROUTES.changePassword, {
        current_password: cur,
        password: nw,
        password_confirmation: conf,
      });

      show(r.message || 'Password updated.', true);
      setTimeout(close, 1200);

    } catch (e) {
      show(
        (e.errors && (e.errors.current_password?.[0] || e.errors.password?.[0])) ||
        e.message ||
        'Could not update password.'
      );
      btn.disabled = false;
      btn.textContent = 'Update';
    }
  }

  document.addEventListener('keydown', onKey);
  m.addEventListener('click', e => { if (e.target === m) close(); });
  $('pwdCancel').addEventListener('click', close);
  $('pwdSave').addEventListener('click', submit);
}



$('profRefresh').addEventListener('click', () => loadProfile(true));

/* ══════════════════════════════════════════════════════
   INIT
══════════════════════════════════════════════════════ */
document.addEventListener('DOMContentLoaded', () => {
  try {
    renderPipeline();          // always paint the list first
    if (ACTIVE) restoreTerminal(ACTIVE);
    else showPage('pipeline');
  } catch (err) {
    console.error('Init failed:', err);
    showToast('error', 'Load error', err.message);
  }
});

function activateJob(ref, srId) {
  if (activeRef === ref) { showPage('terminal'); return; }

  if (punchInTime) {
    showToast('warning', 'Job in progress',
      'Finish or hold the current job before switching.');
    return;
  }

  activeRef   = ref;
  activeSrId  = srId;
  punchInTime = null;
  uploads     = { before: [], after: [] };
  expenses    = [];
  clearInterval(timerInterval);

  const job = JOBS.find((j) => j.id === ref);
  const [etaDate, etaTime] = String(job.eta ?? '').split(' ');
  buildBanner(job, etaDate || '\u2014', etaTime || '\u2014');

  $('activeDot').classList.remove('hidden');
  renderPipeline();
  openTerminal();
  renderExpenses();
}

/**
 * Rebuild the terminal from a server-supplied open punch. Runs instead of
 * the normal pipeline render so a reload mid-job doesn't lose the timer,
 * the uploads, or the logged expenses.
 */
function restoreTerminal(state) {
  const job = state.job;

  activeRef  = job.id;
  activeSrId = Number(job.sr_id);
  uploads = {
    before: Array.isArray(state.uploads?.before) ? state.uploads.before : [],
    after:  Array.isArray(state.uploads?.after)  ? state.uploads.after  : [],
  };
  expenses   = state.expenses.map((e) => ({ ...e }));

  buildBanner(job, state.etaDate ?? '\u2014', state.etaTime ?? '\u2014');
  renderPipeline();
  showPage('terminal');

  $('activeDot').classList.remove('hidden');
  $('workDesc').value = state.workDesc ?? '';

  // Punch already open: the timer is running, uploads are unlocked.
  if (state.punchInAt) {
    punchInTime = new Date(state.punchInAt);

    clearInterval(timerInterval);
    timerInterval = setInterval(updateTimer, 1000);
    updateTimer();

    $('timerIcon').innerHTML = '<i class="bi bi-stopwatch-fill" style="color:#05a34a;"></i>';
    $('timerIcon').style.background = 'rgba(5,163,74,.1)';
    $('timerStatus').textContent = 'Live';
    $('timerStatus').className   = 'tw-status status-live';
    $('timerLabel').textContent  = 'Time on site';

    $('punchInBtn').disabled  = true;
    $('punchInBtn').innerHTML = '<i class="bi bi-check2"></i>Job Started';
    if (state.punchInLocation) {
      renderLocationBadge(state.punchInLocation);
    }
    document.querySelectorAll('[data-upload]').forEach((b) => { b.disabled = false; });
    $('expenseBtn').disabled = false;
    $('rsBtn').classList.remove('hidden');
  }

  ['before', 'after'].forEach((type) => renderPhotoStrip(type));

  renderExpenses();
  refreshLock();
}
</script>
</body>
</html>