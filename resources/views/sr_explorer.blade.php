@extends('layouts.layout')

@section('title', 'SR Explorer — Digit-Us Portal')
@section('page_title', 'SR Explorer')
@section('page_icon', 'clipboard-check')




  @push('styles')
  <style>
  /* SIDEBAR */
.sidebar{width:var(--sidebar-width);min-height:100vh;background:var(--sidebar-bg);border-right:1px solid var(--border-color);display:flex;flex-direction:column;position:fixed;top:0;left:0;z-index:300;box-shadow:var(--sidebar-shadow);transition:transform .28s cubic-bezier(.4,0,.2,1);}
.sb-brand{display:flex;align-items:center;gap:11px;padding:18px 20px 15px;border-bottom:1px solid var(--border-color);flex-shrink:0;}
.sb-brand-icon{width:38px;height:38px;flex-shrink:0;background:linear-gradient(135deg,#9A7B4F,#C4A882);border-radius:9px;display:flex;align-items:center;justify-content:center;color:#fff;font-size:1.05rem;}
.sb-brand-name{font-size:.9375rem;font-weight:700;color:var(--text-heading);line-height:1.2;}
.sb-brand-sub{font-size:.6875rem;color:var(--text-muted);}
.sb-nav{flex:1;overflow-y:auto;padding:10px 0;}
.sb-nav::-webkit-scrollbar{width:3px;}
.sb-nav::-webkit-scrollbar-thumb{background:var(--border-color);border-radius:2px;}
.sb-section{font-size:.625rem;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:var(--text-light);padding:14px 20px 4px;}
.sb-nav a{display:flex;align-items:center;gap:10px;padding:9px 20px;font-size:.8125rem;color:var(--nav-link);border-right:3px solid transparent;transition:background .15s,color .15s;}
.sb-nav a:hover{background:var(--surface-2);color:#9A7B4F;}
.sb-nav a.active{background:rgba(154,123,79,.1);color:#9A7B4F;font-weight:500;border-right-color:#9A7B4F;}
.sb-nav a i{font-size:1rem;width:18px;text-align:center;flex-shrink:0;}
.sb-badge{font-size:.6rem;font-weight:700;padding:1px 6px;border-radius:9px;margin-left:auto;}
.sb-badge.amber{background:rgba(217,119,6,.15);color:#d97706;}
.sb-badge.red{background:rgba(239,68,68,.12);color:#ef4444;}
.sb-footer{padding:14px 18px;border-top:1px solid var(--border-color);flex-shrink:0;}
.sb-user{display:flex;align-items:center;gap:10px;}
.sb-avatar{width:34px;height:34px;flex-shrink:0;background:linear-gradient(135deg,#9A7B4F,#C4A882);border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fff;font-size:.75rem;font-weight:700;}
.sb-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:299;backdrop-filter:blur(2px);}
.sb-overlay.show{display:block;}
@media(max-width:991.98px){.sidebar{transform:translateX(-100%);}.sidebar.open{transform:translateX(0);}}
/* TOPBAR */
.topbar{position:fixed;top:0;left:var(--sidebar-width);right:0;height:60px;background:var(--topbar-bg);border-bottom:1px solid var(--border-color);display:flex;align-items:center;justify-content:space-between;padding:0 22px;z-index:200;box-shadow:var(--topbar-shadow);}
.topbar-left{display:flex;align-items:center;gap:12px;}
.hamburger{display:none;background:none;border:none;padding:6px;color:var(--text-heading);cursor:pointer;border-radius:6px;font-size:1.25rem;line-height:1;}
.hamburger:hover{background:var(--surface-2);}
.mobile-brand{display:none;}
.pt-main{font-size:.9375rem;font-weight:600;color:var(--text-heading);display:flex;align-items:center;gap:7px;}
.topbar .breadcrumb{margin:0;font-size:.72rem;padding:0;}
.topbar .breadcrumb-item+.breadcrumb-item::before{content:"/";color:var(--text-light);}
.topbar .breadcrumb-item.active{color:var(--text-muted);}
.topbar .breadcrumb-item a{color:#9A7B4F;}
.topbar-right{display:flex;align-items:center;gap:10px;}
.role-badge{font-size:.72rem;background:rgba(154,123,79,.12);color:#9A7B4F;padding:3px 10px;border-radius:20px;font-weight:500;white-space:nowrap;}
.clock-d{font-size:.72rem;color:var(--text-muted);white-space:nowrap;}
.t-div{width:1px;height:22px;background:var(--border-color);flex-shrink:0;}
.av-btn{width:34px;height:34px;background:linear-gradient(135deg,#9A7B4F,#C4A882);border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fff;font-size:.8rem;font-weight:700;cursor:pointer;}
.th-toggle{display:flex;align-items:center;gap:7px;cursor:pointer;user-select:none;}
.th-sun{color:#fbbc06;font-size:.8rem;}.th-moon{color:#C4A882;font-size:.8rem;}
.tt-track{width:42px;height:22px;background:var(--toggle-track);border-radius:11px;position:relative;transition:background .3s;border:1px solid var(--border-color);}
.tt-thumb{width:16px;height:16px;background:#fff;border-radius:50%;position:absolute;top:2px;left:2px;transition:transform .3s,background .3s;box-shadow:0 1px 4px rgba(0,0,0,.2);display:flex;align-items:center;justify-content:center;}
[data-bs-theme="dark"] .tt-thumb{transform:translateX(20px);background:#9A7B4F;}
.ts-sun{font-size:8px;color:#fbbc06;}.ts-moon{font-size:8px;color:#fff;display:none;}
[data-bs-theme="dark"] .ts-sun{display:none;}[data-bs-theme="dark"] .ts-moon{display:block;}
@media(max-width:991.98px){.topbar{left:0;}.hamburger{display:flex;align-items:center;justify-content:center;}.mobile-brand{display:flex;align-items:center;gap:9px;}.pt-wrap{display:none;}.role-badge,.clock-d,.t-div{display:none;}}
/* MAIN */
.main-content{margin-left:var(--sidebar-width);margin-top:60px;padding:22px 22px 48px;min-height:calc(100vh - 60px);}
@media(max-width:991.98px){.main-content{margin-left:0;}}
@media(max-width:575.98px){.main-content{padding:14px 12px 48px;}}
/* PAGE HEADER */
.pg-header{border-radius:10px;padding:18px 24px;margin-bottom:20px;color:#fff;position:relative;overflow:hidden;}
.pg-header::before{content:'';position:absolute;left:-40px;bottom:-40px;width:180px;height:180px;border-radius:50%;background:rgba(255,255,255,.05);}
.pg-header::after{content:'';position:absolute;right:-30px;top:-30px;width:160px;height:160px;border-radius:50%;background:rgba(255,255,255,.07);}
.pg-header h4{font-size:1rem;font-weight:600;margin:0 0 3px;position:relative;z-index:1;}
.pg-header p{font-size:.78rem;margin:0;opacity:.85;position:relative;z-index:1;}
.pg-header .meta-row{display:flex;align-items:center;gap:8px;margin-top:9px;position:relative;z-index:1;flex-wrap:wrap;}
.meta-badge{background:rgba(255,255,255,.18);border:1px solid rgba(255,255,255,.3);border-radius:20px;font-size:.6875rem;padding:2px 10px;font-weight:500;}
/* STATS STRIP */
.stats-strip{display:grid;grid-template-columns:repeat(4,1fr);gap:10px;margin-bottom:18px;}
.stat-card{background:var(--card-bg);border:1px solid var(--card-border);border-radius:9px;padding:13px 15px;display:flex;align-items:center;gap:12px;box-shadow:var(--card-shadow);}
.stat-icon{width:38px;height:38px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:1rem;flex-shrink:0;}
.stat-num{font-size:1.5rem;font-weight:700;line-height:1;color:var(--text-heading);}
.stat-lbl{font-size:.7rem;color:var(--text-muted);}
@media(max-width:767px){.stats-strip{grid-template-columns:repeat(2,1fr);}}
/* FILTER BAR */
.filter-bar{background:var(--card-bg);border:1px solid var(--card-border);border-radius:10px;padding:14px 16px;margin-bottom:16px;box-shadow:var(--card-shadow);display:flex;flex-wrap:wrap;gap:10px;align-items:flex-end;}
.filter-group{display:flex;flex-direction:column;gap:4px;min-width:0;}
.filter-label{font-size:.7rem;font-weight:600;color:var(--text-muted);text-transform:uppercase;letter-spacing:.05em;}
.filter-control{height:34px;padding:0 10px;border:1px solid var(--border-color);border-radius:7px;background:var(--input-bg);color:var(--text-primary);font-size:.8rem;font-family:'Roboto',sans-serif;min-width:130px;transition:border-color .15s,box-shadow .15s;}
.filter-control:focus{outline:none;border-color:#9A7B4F;box-shadow:var(--input-focus-shadow);}
.filter-search{min-width:220px;}
.filter-actions{margin-left:auto;display:flex;gap:8px;align-items:flex-end;}
/* TABLE CARD */
.tbl-card{background:var(--card-bg);border:1px solid var(--card-border);border-radius:10px;box-shadow:var(--card-shadow);overflow:hidden;}
.tbl-card-hdr{display:flex;align-items:center;justify-content:space-between;padding:13px 18px;border-bottom:1px solid var(--border-color);flex-wrap:wrap;gap:10px;}
.tbl-card-hdr-left{display:flex;align-items:center;gap:10px;}
.tbl-card-title{font-size:.875rem;font-weight:600;color:var(--text-heading);}
.result-count{font-size:.75rem;color:var(--text-muted);background:var(--surface-2);padding:2px 9px;border-radius:9px;}
.tbl-wrap{overflow-x:auto;}
table.listing{width:100%;border-collapse:collapse;}
table.listing thead th{padding:10px 16px;font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--text-muted);background:var(--table-header);border-bottom:1px solid var(--border-color);white-space:nowrap;}
table.listing thead th.sortable{cursor:pointer;user-select:none;}
table.listing thead th.sortable:hover{color:#9A7B4F;}
table.listing thead th i{font-size:.65rem;margin-left:3px;opacity:.5;}
table.listing tbody tr{border-bottom:1px solid var(--border-color);transition:background .1s;cursor:pointer;}
table.listing tbody tr:last-child{border-bottom:none;}
table.listing tbody tr:hover{background:var(--table-hover);}
table.listing tbody tr.sla-ok{background:var(--row-ok);}
table.listing tbody tr.sla-warn{background:var(--row-warn);}
table.listing tbody tr.sla-breach{background:var(--row-breach);}
table.listing tbody tr.sla-ok:hover{filter:brightness(.97);}
table.listing tbody tr.sla-warn:hover{filter:brightness(.97);}
table.listing tbody tr.sla-breach:hover{filter:brightness(.97);}
table.listing td{padding:11px 16px;font-size:.8125rem;color:var(--text-primary);vertical-align:middle;}
table.listing td.muted{color:var(--text-muted);font-size:.78rem;}
table.listing td.mono{font-family:monospace;font-size:.78rem;font-weight:600;color:#9A7B4F;}
/* BUTTONS */
.btn-gold{display:inline-flex;align-items:center;gap:6px;padding:7px 14px;background:linear-gradient(135deg,#9A7B4F,#C4A882);color:#fff;border:none;border-radius:7px;font-size:.8rem;font-weight:500;cursor:pointer;white-space:nowrap;}
.btn-gold:hover{opacity:.87;}
.btn-ghost{display:inline-flex;align-items:center;gap:6px;padding:7px 12px;background:var(--surface-2);color:var(--text-muted);border:1px solid var(--border-color);border-radius:7px;font-size:.8rem;cursor:pointer;white-space:nowrap;}
.btn-ghost:hover{background:var(--surface-3);}
.btn-xs{padding:4px 9px;border-radius:5px;font-size:.75rem;border:none;cursor:pointer;display:inline-flex;align-items:center;gap:4px;font-family:'Roboto',sans-serif;transition:background .12s;}
.btn-xs-edit{background:rgba(154,123,79,.1);color:#9A7B4F;}
.btn-xs-edit:hover{background:rgba(154,123,79,.2);}
.btn-xs-view{background:rgba(37,99,235,.1);color:#3b82f6;}
.btn-xs-view:hover{background:rgba(37,99,235,.2);}
.btn-xs-del{background:rgba(239,68,68,.08);color:#ef4444;}
.btn-xs-del:hover{background:rgba(239,68,68,.16);}
.btn-xs-off{background:rgba(156,163,175,.1);color:#9ca3af;}
.btn-xs-off:hover{background:rgba(156,163,175,.2);}
.btn-xs-retry{background:rgba(245,158,11,.1);color:#d97706;}
.btn-xs-retry:hover{background:rgba(245,158,11,.2);}
/* BADGES */
.sbadge{display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:20px;font-size:.7rem;font-weight:600;white-space:nowrap;}
.sb-pending{background:rgba(107,114,128,.1);color:#6b7280;}
.sb-quoted{background:rgba(139,92,246,.12);color:#7c3aed;}
.sb-approved{background:rgba(16,185,129,.12);color:#059669;}
.sb-assigned{background:rgba(37,99,235,.12);color:#2563eb;}
.sb-inprog{background:rgba(6,182,212,.12);color:#0891b2;}
.sb-review{background:rgba(245,158,11,.12);color:#d97706;}
.sb-rework{background:rgba(239,68,68,.12);color:#ef4444;}
.sb-completed{background:rgba(16,185,129,.15);color:#059669;}
.sb-cancelled{background:rgba(107,114,128,.1);color:#6b7280;text-decoration:line-through;}
.sb-active{background:rgba(16,185,129,.12);color:#059669;}
.sb-inactive{background:rgba(156,163,175,.1);color:#9ca3af;}
.sb-warranty{background:rgba(16,185,129,.1);color:#059669;}
.sb-oow{background:rgba(239,68,68,.1);color:#ef4444;}
/* SLA INDICATOR */
.sla-ind{display:inline-flex;align-items:center;gap:5px;font-size:.75rem;font-weight:500;}
.sla-dot{width:7px;height:7px;border-radius:50%;flex-shrink:0;}
.sla-ok-dot{background:#10b981;}.sla-warn-dot{background:#f59e0b;}.sla-breach-dot{background:#ef4444;}
.sla-ok-text{color:#059669;}.sla-warn-text{color:#d97706;}.sla-breach-text{color:#ef4444;}
/* ROLE PILL */
.role-pill{display:inline-flex;align-items:center;gap:5px;padding:3px 9px;border-radius:5px;font-size:.7rem;font-weight:600;}
/* AVATAR */
.user-cell{display:flex;align-items:center;gap:10px;}
.u-av{width:30px;height:30px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:.72rem;font-weight:700;color:#fff;flex-shrink:0;}
/* PAGINATION */
.pagination-bar{display:flex;align-items:center;justify-content:space-between;padding:12px 18px;border-top:1px solid var(--border-color);flex-wrap:wrap;gap:8px;}
.page-info{font-size:.78rem;color:var(--text-muted);}
.page-btns{display:flex;gap:4px;}
.page-btn{width:30px;height:30px;border-radius:6px;border:1px solid var(--border-color);background:var(--card-bg);color:var(--text-muted);cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:.78rem;transition:all .12s;}
.page-btn:hover{border-color:#9A7B4F;color:#9A7B4F;}
.page-btn.active{background:#9A7B4F;color:#fff;border-color:#9A7B4F;}
/* KANBAN */
.kanban-scroll{overflow-x:auto;padding-bottom:12px;}
.kanban-scroll::-webkit-scrollbar{height:5px;}
.kanban-scroll::-webkit-scrollbar-thumb{background:var(--border-color);border-radius:3px;}
.kanban-board{display:flex;gap:12px;min-width:max-content;padding:2px;}
.kb-col{width:240px;flex-shrink:0;display:flex;flex-direction:column;gap:8px;}
.kb-col-hdr{display:flex;align-items:center;justify-content:space-between;padding:10px 12px;border-radius:8px;background:var(--surface-2);border:1px solid var(--border-color);position:sticky;top:0;}
.kb-col-label{font-size:.75rem;font-weight:700;color:var(--text-heading);}
.kb-count{font-size:.68rem;background:var(--card-bg);padding:1px 7px;border-radius:9px;color:var(--text-muted);border:1px solid var(--border-color);font-weight:600;}
.kb-card{background:var(--card-bg);border:1px solid var(--card-border);border-radius:8px;padding:12px 13px;cursor:pointer;transition:box-shadow .15s,transform .12s;box-shadow:var(--card-shadow);}
.kb-card:hover{box-shadow:0 6px 20px rgba(100,120,160,.14);transform:translateY(-1px);}
.kb-sr-id{font-size:.72rem;font-family:monospace;font-weight:700;color:#9A7B4F;margin-bottom:5px;}
.kb-client{font-size:.8rem;font-weight:600;color:var(--text-heading);margin-bottom:2px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
.kb-site{font-size:.72rem;color:var(--text-muted);margin-bottom:8px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
.kb-foot{display:flex;align-items:center;justify-content:space-between;}
.kb-time{font-size:.68rem;color:var(--text-muted);display:flex;align-items:center;gap:3px;}
.kb-proof-icons{display:flex;gap:5px;}
.kb-proof-icons button{background:none;border:none;padding:3px;cursor:pointer;color:var(--text-muted);font-size:.8rem;border-radius:4px;line-height:1;transition:color .12s,background .12s;}
.kb-proof-icons button:hover{color:#9A7B4F;background:rgba(154,123,79,.1);}
/* TREE (project/site) */
.tree-row-project{background:var(--surface-2);font-weight:600;}
.tree-row-site{background:var(--card-bg);}
.tree-row-site td:first-child{padding-left:40px;}
.expand-btn{background:none;border:none;color:var(--text-muted);cursor:pointer;padding:3px 6px;border-radius:5px;font-size:.8rem;transition:color .12s;}
.expand-btn:hover{color:#9A7B4F;}
/* WA LOG */
.wa-status-sent{background:rgba(16,185,129,.1);color:#059669;}
.wa-status-delivered{background:rgba(37,99,235,.1);color:#2563eb;}
.wa-status-failed{background:rgba(239,68,68,.1);color:#ef4444;}
.wa-status-pending{background:rgba(245,158,11,.1);color:#d97706;}
.msg-preview{font-size:.75rem;color:var(--text-muted);max-width:260px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
/* TOAST */
#toastWrap{position:fixed;bottom:22px;right:22px;z-index:9999;display:flex;flex-direction:column;gap:8px;pointer-events:none;}
.toast-item{display:flex;align-items:flex-start;gap:10px;padding:12px 15px;border-radius:9px;background:var(--card-bg);border:1px solid var(--card-border);box-shadow:0 6px 24px rgba(0,0,0,.14);min-width:240px;max-width:320px;animation:toastIn .2s ease;pointer-events:auto;}
@keyframes toastIn{from{opacity:0;transform:translateY(10px);}to{opacity:1;transform:translateY(0);}}
.t-ico{font-size:1rem;flex-shrink:0;margin-top:1px;}
.t-ico.ok{color:#10b981;}.t-ico.err{color:#ef4444;}.t-ico.info{color:#9A7B4F;}
.t-title{font-size:.8rem;font-weight:600;color:var(--text-heading);margin:0 0 2px;}
.t-body{font-size:.75rem;color:var(--text-muted);margin:0;}
/* EMPTY STATE */
.empty-row td{text-align:center;padding:40px 20px;color:var(--text-muted);font-size:.82rem;}
.empty-row td i{display:block;font-size:2rem;color:var(--text-light);margin-bottom:8px;}
/* RLS BANNER */
.rls-banner{display:flex;align-items:center;gap:9px;padding:9px 14px;border-radius:7px;font-size:.78rem;color:var(--text-muted);margin-bottom:14px;background:rgba(37,99,235,.07);border:1px solid rgba(37,99,235,.15);}
.rls-banner i{color:#3b82f6;flex-shrink:0;}


  </style>
  @endpush



@section('content')


<div class="sb-overlay" id="sbOverlay" onclick="closeSidebar()"></div>




<div class="pg-header" style="background:linear-gradient(135deg,#1e3a5f,#2563eb);">
  <h4><i class="bi bi-ticket-detailed me-2"></i>SR Explorer — Service Request Listing</h4>
  <p>Centralised grid for monitoring, filtering and drilling into service tickets across their full lifecycle.</p>
  <div class="meta-row">
    <span class="meta-badge"><i class="bi bi-shield-fill-check me-1"></i>Super Admin</span>
    <span class="meta-badge"><i class="bi bi-person-gear me-1"></i>Admin</span>
    <span class="meta-badge"><i class="bi bi-person-workspace me-1"></i>Head of Projects</span>
    <span class="meta-badge"><i class="bi bi-person-badge me-1"></i>Front Desk (own SRs only)</span>
    <span class="meta-badge"><i class="bi bi-calculator me-1"></i>Accounts (OoW only)</span>
  </div>
</div>

<div id="rls-banner" class="rls-banner" style="display:none;">
  <i class="bi bi-funnel-fill"></i>
  <span id="rls-msg">Row-level filter active — showing filtered view.</span>
</div>

<div class="stats-strip">
  <div class="stat-card">
    <div class="stat-icon" style="background:rgba(37,99,235,.1);"><i class="bi bi-ticket-detailed" style="color:#2563eb;"></i></div>
    <div><div class="stat-num">47</div><div class="stat-lbl">Total Active SRs</div></div>
  </div>
  <div class="stat-card">
    <div class="stat-icon" style="background:rgba(107,114,128,.1);"><i class="bi bi-hourglass-split" style="color:#6b7280;"></i></div>
    <div><div class="stat-num">12</div><div class="stat-lbl">Pending Review</div></div>
  </div>
  <div class="stat-card">
    <div class="stat-icon" style="background:rgba(6,182,212,.1);"><i class="bi bi-wrench-adjustable-circle" style="color:#0891b2;"></i></div>
    <div><div class="stat-num">9</div><div class="stat-lbl">In Progress Today</div></div>
  </div>
  <div class="stat-card">
    <div class="stat-icon" style="background:rgba(239,68,68,.1);"><i class="bi bi-exclamation-triangle" style="color:#ef4444;"></i></div>
    <div><div class="stat-num">3</div><div class="stat-lbl">SLA Breached</div></div>
  </div>
</div>

<div class="filter-bar">
  <div class="filter-group">
    <div class="filter-label">Search</div>
    <input class="filter-control filter-search" type="text" placeholder="SR ID, client name, site…" oninput="filterTable()"/>
  </div>
  <div class="filter-group">
    <div class="filter-label">Status</div>
    <select class="filter-control" id="filter-status" onchange="filterTable()">
      <option value="">All Statuses</option>
      <option>Pending</option><option>Quoted</option><option>Approved</option>
      <option>Assigned</option><option>In Progress</option><option>Pending Review</option>
      <option>Rework</option><option>Completed</option><option>Cancelled</option>
    </select>
  </div>
  <div class="filter-group">
    <div class="filter-label">Date From</div>
    <input class="filter-control" type="date"/>
  </div>
  <div class="filter-group">
    <div class="filter-label">Date To</div>
    <input class="filter-control" type="date"/>
  </div>
  <div class="filter-actions">
    <button class="btn-ghost" onclick="resetFilters()"><i class="bi bi-x-circle"></i>Reset</button>
    <button class="btn-gold" onclick="showToast('ok','Export Started','Generating CSV for current filtered view…')"><i class="bi bi-download"></i>Export CSV</button>
  </div>
</div>

<div class="tbl-card">
  <div class="tbl-card-hdr">
    <div class="tbl-card-hdr-left">
      <span class="tbl-card-title">Service Requests</span>
      <span class="result-count" id="result-count">Showing 10 of 47</span>
    </div>
    <div class="sr-legend">
      <div class="legend-item"><div class="legend-swatch" style="background:var(--row-ok);border:1px solid rgba(16,185,129,.3);"></div>On Track</div>
      <div class="legend-item"><div class="legend-swatch" style="background:var(--row-warn);border:1px solid rgba(245,158,11,.3);"></div>SLA Warning</div>
      <div class="legend-item"><div class="legend-swatch" style="background:var(--row-breach);border:1px solid rgba(239,68,68,.3);"></div>SLA Breach</div>
    </div>
  </div>
  <div class="tbl-wrap">
    <table class="listing" id="sr-table">
      <thead>
        <tr>
          <th class="sortable">SR ID <i class="bi bi-chevron-expand"></i></th>
          <th class="sortable">Client <i class="bi bi-chevron-expand"></i></th>
          <th>Site / Location</th>
          <th>Assigned To</th>
          <th class="sortable">Status <i class="bi bi-chevron-expand"></i></th>
          <th>SLA Status</th>
          <th class="sortable">Created <i class="bi bi-chevron-expand"></i></th>
          <th style="width:70px;">Action</th>
        </tr>
      </thead>
     

      <tbody id="sr-tbody">

@foreach($sr_explorer as $sr)

@php

    $hours = \Carbon\Carbon::parse($sr->created_at)->diffInHours(now());

    if($hours <= 8){
        $rowClass='sla-ok';
        $dot='sla-ok-dot';
        $text='sla-ok-text';
        $sla='On Track';
    }
    elseif($hours <=24){
        $rowClass='sla-warn';
        $dot='sla-warn-dot';
        $text='sla-warn-text';
        $sla='Warning';
    }
    else{
        $rowClass='sla-breach';
        $dot='sla-breach-dot';
        $text='sla-breach-text';
        $sla='Breach';
    }

@endphp

<tr class="{{ $rowClass }}" onclick="goToDetail('SR-{{ date('Y') }}-{{ str_pad($sr->id,5,'0',STR_PAD_LEFT) }}')">

<td class="mono">
SR-{{ date('Y') }}-{{ str_pad($sr->id,5,'0',STR_PAD_LEFT) }}
</td>

<td>
<strong style="font-size:.82rem">
{{ optional($sr->client)->company_name }}
</strong>
</td>

<td class="muted">
{{ $sr->project_site ?? optional($sr->project)->project_name }}
</td>

<td>
    @if($sr->assignedUser)
        {{ $sr->assignedUser->name }}
    @else
        <span style="color:#999;">Unassigned</span>
    @endif
</td>

<td>

@php

$statusClass=[

'Pending'=>'sb-pending',

'Approved'=>'sb-approved',

'Assigned'=>'sb-assigned',

'In Progress'=>'sb-inprog',

'Pending Review'=>'sb-review',

'Completed'=>'sb-completed',

'Cancelled'=>'sb-cancelled',

'Quoted'=>'sb-quoted',

'Rework'=>'sb-rework'

];

@endphp

<span class="sbadge {{ $statusClass[$sr->status] ?? 'sb-pending' }}">
<i class="bi bi-circle-fill" style="font-size:.4rem;"></i>

{{ $sr->status }}

</span>

</td>

<td>

<div class="sla-ind">

<span class="sla-dot {{ $dot }}"></span>

<span class="{{ $text }}">
{{ $sla }} · {{ $hours }}h
</span>

</div>

</td>

<td class="muted">

{{ $sr->created_at->diffForHumans() }}

</td>

<td onclick="event.stopPropagation()">

<div style="display:flex;gap:5px;">

<button class="btn-xs btn-xs-view"
onclick="goToDetail('SR-{{ date('Y') }}-{{ str_pad($sr->id,5,'0',STR_PAD_LEFT) }}')">

<i class="bi bi-eye"></i>

View

</button>

</div>

</td>

</tr>

@endforeach

</tbody>

    </table>
  </div>
  <div class="pagination-bar">
    <div class="page-info">Page 1 of 5 · 47 total records</div>
    <div class="page-btns">
      <button class="page-btn"><i class="bi bi-chevron-left"></i></button>
      <button class="page-btn active">1</button>
      <button class="page-btn">2</button>
      <button class="page-btn">3</button>
      <button class="page-btn"><i class="bi bi-chevron-right"></i></button>
    </div>
  </div>
</div>


<div id="toastWrap"></div>

@endsection

@push('scripts')
<script>

function toggleTheme(){
  const h=document.documentElement;
  h.setAttribute('data-bs-theme',h.getAttribute('data-bs-theme')==='dark'?'light':'dark');
}
function toggleSidebar(){
  document.getElementById('sidebar').classList.toggle('open');
  document.getElementById('sbOverlay').classList.toggle('show');
}
function closeSidebar(){
  document.getElementById('sidebar').classList.remove('open');
  document.getElementById('sbOverlay').classList.remove('show');
}
function updateClock(){
  document.getElementById('clock').textContent=new Date().toLocaleTimeString([],{hour:'2-digit',minute:'2-digit',second:'2-digit'});
}
setInterval(updateClock,1000);updateClock();
function showToast(type,title,body){
  const w=document.getElementById('toastWrap');
  const icons={ok:'bi-check-circle-fill',err:'bi-x-circle-fill',info:'bi-info-circle-fill'};
  const t=document.createElement('div');t.className='toast-item';
  t.innerHTML=`<i class="bi ${icons[type]||icons.info} t-ico ${type}"></i><div><p class="t-title">${title}</p><p class="t-body">${body}</p></div>`;
  w.appendChild(t);
  setTimeout(()=>{t.style.transition='opacity .3s';t.style.opacity='0';setTimeout(()=>t.remove(),300);},3500);
}


function goToDetail(id){showToast('info','Navigating','Opening SR timeline for '+id+'…');}
function filterTable(){
  const q=document.querySelector('.filter-search').value.toLowerCase();
  const rows=document.querySelectorAll('#sr-tbody tr');
  let visible=0;
  rows.forEach(r=>{
    const txt=r.textContent.toLowerCase();
    const show=txt.includes(q);
    r.style.display=show?'':'none';
    if(show)visible++;
  });
  document.getElementById('result-count').textContent='Showing '+visible+' of 47';
}
function resetFilters(){
  document.querySelector('.filter-search').value='';
  document.getElementById('filter-status').value='';
  document.querySelectorAll('#sr-tbody tr').forEach(r=>r.style.display='');
  document.getElementById('result-count').textContent='Showing 10 of 47';
}

</script>
@endpush


