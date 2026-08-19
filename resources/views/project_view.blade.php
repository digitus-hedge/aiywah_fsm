@extends('layouts.layout')
@section('title', 'Project View — Digit-Us Portal')
@section('page_title')
  Project View<span class="hide-mobile"> Directory</span>
@endsection
@section('page_icon', 'briefcase')


@push('styles')
<style>

/* ── SIDEBAR ── */
.sb-brand{display:flex;align-items:center;gap:11px;padding:18px 20px 15px;border-bottom:1px solid var(--border-color);flex-shrink:0;}
.sb-brand-icon{width:38px;height:38px;flex-shrink:0;background:linear-gradient(135deg,#9a8053,#b8975e);border-radius:9px;display:flex;align-items:center;justify-content:center;color:#fff;font-size:.85rem;font-weight:700;letter-spacing:-.5px;line-height:1;}
.sb-brand-name{font-size:.9375rem;font-weight:700;color:var(--text-heading);line-height:1.2;}
.sb-brand-sub{font-size:.6875rem;color:var(--text-muted);}
.sb-nav{flex:1;overflow-y:auto;padding:10px 0;}
.sb-nav::-webkit-scrollbar{width:3px;}
.sb-nav::-webkit-scrollbar-thumb{background:var(--border-color);border-radius:2px;}
.sb-section{font-size:.625rem;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:var(--text-light);padding:14px 20px 4px;}
.sb-nav a{display:flex;align-items:center;gap:10px;padding:9px 20px;font-size:.8125rem;color:var(--nav-link);border-right:3px solid transparent;transition:background .15s,color .15s;}
.sb-nav a:hover{background:var(--surface-2);color:#9a8053;}
.sb-nav a.active{background:rgba(154,128,83,.1);color:#9a8053;font-weight:500;border-right-color:#9a8053;}
.sb-nav a i{font-size:1rem;width:18px;text-align:center;flex-shrink:0;}
.sb-badge{font-size:.6rem;font-weight:700;padding:1px 6px;border-radius:9px;margin-left:auto;}
.sb-badge.amber{background:rgba(217,119,6,.15);color:#d97706;}
.sb-footer{padding:14px 18px;border-top:1px solid var(--border-color);flex-shrink:0;}
.sb-user{display:flex;align-items:center;gap:10px;}
.sb-avatar{width:34px;height:34px;flex-shrink:0;background:linear-gradient(135deg,#9a8053,#b8975e);border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fff;font-size:.75rem;font-weight:700;}
.sb-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:299;backdrop-filter:blur(2px);}
.sb-overlay.show{display:block;}
@media(max-width:991.98px){.sidebar{transform:translateX(-100%);}.sidebar.open{transform:translateX(0);}}

/* ── TOPBAR ── */
.topbar{position:fixed;top:0;left:var(--sidebar-width);right:0;height:60px;background:var(--topbar-bg);border-bottom:1px solid var(--border-color);display:flex;align-items:center;justify-content:space-between;padding:0 22px;z-index:200;box-shadow:var(--topbar-shadow);}
.topbar-left{display:flex;align-items:center;gap:12px;}
.hamburger{display:none;background:none;border:none;padding:6px;color:var(--text-heading);cursor:pointer;border-radius:6px;font-size:1.25rem;line-height:1;}
.hamburger:hover{background:var(--surface-2);}
.mobile-brand{display:none;}
.pg-title{font-size:.9375rem;font-weight:600;color:var(--text-heading);display:flex;align-items:center;gap:7px;}
.breadcrumb{margin:0;font-size:.72rem;padding:0;}
.breadcrumb-item+.breadcrumb-item::before{content:"/";color:var(--text-light);}
.breadcrumb-item.active{color:var(--text-muted);}
.breadcrumb-item a{color:#9a8053;}
.topbar-right{display:flex;align-items:center;gap:10px;}
.role-badge{font-size:.72rem;background:rgba(154,128,83,.12);color:#9a8053;padding:3px 10px;border-radius:20px;font-weight:500;white-space:nowrap;}
.clock-d{font-size:.72rem;color:var(--text-muted);white-space:nowrap;}
.t-div{width:1px;height:22px;background:var(--border-color);flex-shrink:0;}
.av-btn{width:34px;height:34px;background:linear-gradient(135deg,#9a8053,#b8975e);border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fff;font-size:.8rem;font-weight:700;cursor:pointer;}
.th-toggle{display:flex;align-items:center;gap:7px;cursor:pointer;user-select:none;}
.th-sun{color:#fbbc06;font-size:.8rem;}.th-moon{color:#b8975e;font-size:.8rem;}
.tt-track{width:42px;height:22px;background:var(--toggle-track);border-radius:11px;position:relative;transition:background .3s;border:1px solid var(--border-color);}
.tt-thumb{width:16px;height:16px;background:#fff;border-radius:50%;position:absolute;top:2px;left:2px;transition:transform .3s,background .3s;box-shadow:0 1px 4px rgba(0,0,0,.2);display:flex;align-items:center;justify-content:center;}
[data-bs-theme="dark"] .tt-thumb{transform:translateX(20px);background:#9a8053;}
.ts-sun{font-size:8px;color:#fbbc06;}.ts-moon{font-size:8px;color:#fff;display:none;}
[data-bs-theme="dark"] .ts-sun{display:none;}[data-bs-theme="dark"] .ts-moon{display:block;}
@media(max-width:991.98px){.topbar{left:0;}.hamburger{display:flex;align-items:center;justify-content:center;}.mobile-brand{display:flex;align-items:center;gap:9px;}.pt-wrap{display:none;}.role-badge,.clock-d,.t-div{display:none;}}

/* ── MAIN ── */
.main-content{margin-left:var(--sidebar-width);margin-top:60px;padding:22px 22px 56px;min-height:calc(100vh - 60px);}
@media(max-width:991.98px){.main-content{margin-left:0;}}
@media(max-width:575.98px){.main-content{padding:14px 12px 56px;}}

/* ── PROJECT HERO ── */
.proj-hero{border-radius:12px;padding:24px 28px;margin-bottom:20px;color:#fff;position:relative;overflow:hidden;background:linear-gradient(135deg,#9A7B4F 0%,#7A6140 100%);}
.proj-hero::before{content:'';position:absolute;left:-50px;bottom:-50px;width:220px;height:220px;border-radius:50%;background:rgba(255,255,255,.05);}
.proj-hero::after{content:'';position:absolute;right:-40px;top:-40px;width:200px;height:200px;border-radius:50%;background:rgba(255,255,255,.07);}
.proj-hero-top{display:flex;align-items:flex-start;justify-content:space-between;gap:16px;position:relative;z-index:1;flex-wrap:wrap;}
.proj-hero-code{font-size:.75rem;font-weight:700;background:rgba(255,255,255,.2);border:1px solid rgba(255,255,255,.3);border-radius:20px;padding:3px 12px;display:inline-flex;align-items:center;gap:6px;margin-bottom:8px;letter-spacing:.03em;}
.proj-hero-name{font-size:1.55rem;font-weight:700;line-height:1.15;margin:0 0 6px;letter-spacing:-.02em;}
.proj-hero-client{display:flex;align-items:center;gap:8px;font-size:.82rem;opacity:.9;}
.proj-hero-client .cdot{width:22px;height:22px;border-radius:6px;background:rgba(255,255,255,.25);display:flex;align-items:center;justify-content:center;font-size:.65rem;font-weight:700;flex-shrink:0;}
.proj-hero-meta{display:flex;align-items:center;gap:8px;margin-top:14px;flex-wrap:wrap;position:relative;z-index:1;}
.hero-badge{background:rgba(255,255,255,.18);border:1px solid rgba(255,255,255,.28);border-radius:20px;font-size:.7rem;padding:3px 11px;font-weight:500;display:inline-flex;align-items:center;gap:5px;}
.hero-badge.green{background:rgba(16,185,129,.3);border-color:rgba(16,185,129,.4);}
.proj-hero-actions{display:flex;gap:8px;position:relative;z-index:1;flex-shrink:0;flex-wrap:wrap;}
.btn-hero-outline{display:inline-flex;align-items:center;gap:6px;padding:8px 15px;border:1px solid rgba(255,255,255,.4);border-radius:8px;color:#fff;background:rgba(255,255,255,.12);font-size:.8rem;font-weight:500;cursor:pointer;transition:background .15s;white-space:nowrap;}
.btn-hero-outline:hover{background:rgba(255,255,255,.22);}
.btn-hero-primary{display:inline-flex;align-items:center;gap:6px;padding:8px 15px;border:none;border-radius:8px;color:#7c3aed;background:#fff;font-size:.8rem;font-weight:600;cursor:pointer;transition:opacity .15s;white-space:nowrap;}
.btn-hero-primary:hover{opacity:.9;}

/* ── STATS ── */
.stats-strip{display:grid;grid-template-columns:repeat(5,1fr);gap:10px;margin-bottom:20px;}
.stat-card{background:var(--card-bg);border:1px solid var(--card-border);border-radius:10px;padding:14px 15px;display:flex;align-items:center;gap:12px;box-shadow:var(--card-shadow);}
.stat-icon{width:38px;height:38px;border-radius:9px;display:flex;align-items:center;justify-content:center;font-size:1rem;flex-shrink:0;}
.stat-num{font-size:1.45rem;font-weight:700;line-height:1;color:var(--text-heading);}
.stat-lbl{font-size:.7rem;color:var(--text-muted);margin-top:2px;}
@media(max-width:900px){.stats-strip{grid-template-columns:repeat(3,1fr);}}
@media(max-width:575px){.stats-strip{grid-template-columns:repeat(2,1fr);}}

/* ── TWO-COL LAYOUT ── */
.view-layout{display:grid;grid-template-columns:1fr 300px;gap:16px;align-items:start;}
.view-layout>*{min-width:0;}
@media(max-width:1200px){.view-layout{grid-template-columns:1fr 280px;}}
@media(max-width:1100px){.view-layout{grid-template-columns:1fr;}}

/* ── CARD ── */
.card{background:var(--card-bg);border:1px solid var(--card-border);border-radius:10px;box-shadow:var(--card-shadow);overflow:hidden;margin-bottom:0;}
.card-hdr{display:flex;align-items:center;justify-content:space-between;padding:13px 18px;border-bottom:1px solid var(--border-color);flex-wrap:wrap;gap:10px;}
.card-hdr-left{display:flex;align-items:center;gap:10px;}
.card-hdr-title{font-size:.9rem;font-weight:600;color:var(--text-heading);}
.result-count{font-size:.72rem;color:var(--text-muted);background:var(--surface-2);padding:2px 9px;border-radius:9px;}

/* ── FILTER BAR ── */
.sr-filter-bar{display:flex;flex-wrap:wrap;gap:8px;align-items:flex-end;padding:12px 18px;border-bottom:1px solid var(--border-color);background:var(--surface-2);}
.filter-group{display:flex;flex-direction:column;gap:4px;min-width:0;flex: 1 1 auto;}
.filter-label{font-size:.65rem;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.06em;}
.filter-control{height:32px;padding:0 9px;border:1px solid var(--border-color);border-radius:7px;background:var(--input-bg);color:var(--text-primary);font-size:.78rem;min-width:120px;transition:border-color .15s;}
.filter-control:focus{outline:none;border-color:#9a8053;box-shadow:var(--input-focus-shadow);}
.filter-actions{margin-left:auto;display:flex;gap:6px;align-items:flex-end;}

/* ── TABLE ── */
.tbl-wrap{overflow-x:auto;}
table.listing{width:100%;border-collapse:collapse;}
table.listing thead th{padding:9px 14px;font-size:.67rem;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--text-muted);background:var(--table-header);border-bottom:1px solid var(--border-color);white-space:nowrap;}
table.listing tbody tr{border-bottom:1px solid var(--border-color);transition:background .1s;cursor:default;}
table.listing tbody tr:last-child{border-bottom:none;}
table.listing tbody tr:hover{background:var(--table-hover);}
table.listing td{padding:11px 14px;font-size:.8rem;color:var(--text-primary);vertical-align:middle;}
table.listing td.muted{color:var(--text-muted);font-size:.75rem;}
table.listing td.mono{font-size:.78rem;font-weight:600;color:#9a8053;}
.row-actions{display:flex;gap:5px;transition:opacity .12s;}
table.listing tr:hover .row-actions{opacity:1;}

/* ── STATUS BADGES ── */
.sbadge{display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:20px;font-size:.69rem;font-weight:600;white-space:nowrap;}
.sb-pending{background:rgba(107,114,128,.1);color:#6b7280;}
.sb-quoted{background:rgba(139,92,246,.12);color:#7c3aed;}
.sb-approved{background:rgba(16,185,129,.12);color:#059669;}
.sb-assigned{background:rgba(37,99,235,.12);color:#2563eb;}
.sb-inprog{background:rgba(6,182,212,.12);color:#0891b2;}
.sb-review{background:rgba(245,158,11,.12);color:#d97706;}
.sb-rework{background:rgba(239,68,68,.12);color:#ef4444;}
.sb-completed{background:rgba(16,185,129,.15);color:#059669;}
.sb-cancelled{background:rgba(107,114,128,.1);color:#6b7280;text-decoration:line-through;}
.sb-active-g{background:rgba(16,185,129,.12);color:#059669 !important;}
.sla-ok{color:#10b981;font-size:.73rem;font-weight:600;}
.sla-warn{color:#d97706;font-size:.73rem;font-weight:600;}
.sla-breach{color:#ef4444;font-size:.73rem;font-weight:600;}

/* ── PAGINATION ── */
.pagination-bar{display:flex;align-items:center;justify-content:space-between;padding:11px 18px;border-top:1px solid var(--border-color);flex-wrap:wrap;gap:8px;}
.page-info{font-size:.75rem;color:var(--text-muted);}
.page-btns{display:flex;gap:4px;}
.page-btn{width:28px;height:28px;border-radius:6px;border:1px solid var(--border-color);background:var(--card-bg);color:var(--text-muted);cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:.75rem;transition:all .12s;}
.page-btn:hover{border-color:#9a8053;color:#9a8053;}
.page-btn.active{background:#9a8053;color:#fff;border-color:#9a8053;}

/* ── BUTTONS ── */
.btn-gold{display:inline-flex;align-items:center;gap:6px;padding:7px 14px;background:linear-gradient(135deg,#9a8053,#b8975e);color:#fff;border:none;border-radius:7px;font-size:.8rem;font-weight:500;cursor:pointer;white-space:nowrap;transition:opacity .15s;}
.btn-gold:hover{opacity:.87;}
.btn-ghost{display:inline-flex;align-items:center;gap:6px;padding:7px 12px;background:var(--surface-2);color:var(--text-muted);border:1px solid var(--border-color);border-radius:7px;font-size:.8rem;cursor:pointer;white-space:nowrap;}
.btn-ghost:hover{background:var(--surface-3);}
.btn-xs{padding:4px 9px;border-radius:5px;font-size:.73rem;border:none;cursor:pointer;display:inline-flex;align-items:center;gap:4px;transition:background .12s;}
.btn-xs-view{background:rgba(37,99,235,.1);color:#3b82f6;}.btn-xs-view:hover{background:rgba(37,99,235,.2);}
.btn-xs-edit{background:rgba(154,128,83,.1);color:#9a8053;}.btn-xs-edit:hover{background:rgba(154,128,83,.2);}
.btn-xs-danger{background:rgba(239,68,68,.08);color:#ef4444;}.btn-xs-danger:hover{background:rgba(239,68,68,.15);}

/* ── RIGHT PANEL CARDS ── */
.info-card{background:var(--card-bg);border:1px solid var(--card-border);border-radius:10px;box-shadow:var(--card-shadow);overflow:hidden;margin-bottom:14px;min-width:0;max-width:100%;}
.info-card-hdr{padding:12px 16px;border-bottom:1px solid var(--border-color);display:flex;align-items:center;gap:8px;}
.info-card-hdr i{color:#9a8053;font-size:.95rem;}
.info-card-title{font-size:.82rem;font-weight:600;color:var(--text-heading);}
.info-row{display:flex;align-items:flex-start;gap:8px;padding:9px 16px;border-bottom:1px solid var(--border-color);}
.info-row:last-child{border-bottom:none;}
.info-key{font-size:.71rem;color:var(--text-muted);font-weight:500;white-space:nowrap;padding-top:1px;flex-shrink:0;min-width:90px;}
.info-val{font-size:.78rem;color:var(--text-heading);font-weight:500;text-align:right;flex:1;min-width:0;word-break:break-word;overflow-wrap:anywhere;}
.info-val.mono{color:#9a8053;word-break:break-all;}

/* ── TIMELINE ── */
.timeline{padding:14px 16px;}
.tl-item{display:flex;gap:12px;padding-bottom:18px;position:relative;}
.tl-item:last-child{padding-bottom:0;}
.tl-item::before{content:'';position:absolute;left:13px;top:28px;bottom:0;width:1px;background:var(--timeline-line);}
.tl-item:last-child::before{display:none;}
.tl-dot{width:28px;height:28px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:.7rem;flex-shrink:0;border:2px solid var(--border-color);}
.tl-content{flex:1;min-width:0;overflow:hidden;}
.tl-action{font-size:.78rem;font-weight:500;color:var(--text-heading);line-height:1.35;word-break:break-word;}
.tl-action{font-size:.78rem;font-weight:500;color:var(--text-heading);line-height:1.3;}
.tl-by{font-size:.7rem;color:var(--text-muted);margin-top:2px;}
.tl-time{font-size:.65rem;color:var(--text-light);margin-top:3px;}

/* ── DONUT ── */
.donut-wrap{padding:16px;text-align:center;}
.donut-legend{display:flex;flex-direction:column;gap:6px;margin-top:12px;text-align:left;}
.legend-row{display:flex;align-items:center;gap:8px;font-size:.72rem;color:var(--text-muted);}
.legend-dot{width:8px;height:8px;border-radius:50%;flex-shrink:0;}

/* ── SR DETAIL DRAWER ── */
.drawer-overlay{display:none;position:fixed;inset:0;background:var(--overlay-bg);z-index:500;backdrop-filter:blur(3px);}
.drawer-overlay.show{display:block;}
.drawer{position:fixed;top:0;right:0;bottom:0;width:520px;max-width:100vw;background:var(--modal-bg);border-left:1px solid var(--card-border);box-shadow:var(--drawer-shadow);z-index:501;transform:translateX(100%);transition:transform .3s cubic-bezier(.4,0,.2,1);display:flex;flex-direction:column;}
.drawer.open{transform:translateX(0);}
.drawer-hdr{display:flex;align-items:center;justify-content:space-between;padding:16px 20px;border-bottom:1px solid var(--border-color);flex-shrink:0;}
.drawer-hdr-left{display:flex;align-items:center;gap:10px;}
.dhdr-icon{width:36px;height:36px;border-radius:9px;display:flex;align-items:center;justify-content:center;font-size:.95rem;flex-shrink:0;}
.drawer-title{font-size:1rem;font-weight:600;color:var(--text-heading);}
.drawer-sub{font-size:.72rem;color:var(--text-muted);margin-top:1px;}
.drawer-close{background:none;border:none;color:var(--text-muted);cursor:pointer;padding:5px;border-radius:6px;font-size:1rem;line-height:1;}
.drawer-close:hover{background:var(--surface-2);color:var(--text-primary);}
.drawer-body{flex:1;overflow-y:auto;}
.drawer-body::-webkit-scrollbar{width:5px;}
.drawer-body::-webkit-scrollbar-thumb{background:var(--border-color);border-radius:3px;}
.d-section{padding:16px 20px;border-bottom:1px solid var(--border-color);}
.d-section:last-child{border-bottom:none;}
.d-sec-title{font-size:.72rem;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.07em;margin-bottom:12px;display:flex;align-items:center;gap:6px;}
.d-sec-title i{color:#9a8053;font-size:.82rem;}
.det-grid{display:grid;grid-template-columns:1fr 1fr;gap:9px;}
.det-cell{background:var(--surface-2);border-radius:8px;padding:9px 11px;}
.det-cell.full{grid-column:1/-1;}
.det-key{font-size:.63rem;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.06em;margin-bottom:3px;}
.det-val{font-size:.81rem;color:var(--text-heading);font-weight:500;line-height:1.4;}
.det-val.mono{color:#9a8053;font-size:.84rem;}
.photo-grid{display:grid;grid-template-columns:1fr 1fr;gap:9px;}
.photo-thumb{background:var(--surface-2);border:1px solid var(--border-color);border-radius:8px;aspect-ratio:4/3;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:5px;cursor:pointer;transition:border-color .15s;}
.photo-thumb:hover{border-color:#9a8053;}
.photo-thumb i{font-size:1.5rem;color:var(--text-light);}
.photo-thumb .ph-lbl{font-size:.69rem;color:var(--text-muted);font-weight:500;}
.photo-thumb.has-photo i{color:#9a8053;}
.exp-row{display:flex;align-items:center;gap:9px;padding:7px 0;border-bottom:1px solid var(--border-color);}
.exp-row:last-child{border-bottom:none;}
.exp-cat{font-size:.68rem;font-weight:600;background:rgba(154,128,83,.1);color:#9a8053;border-radius:5px;padding:2px 6px;white-space:nowrap;}
.exp-desc{font-size:.77rem;color:var(--text-heading);flex:1;min-width:0;}
.exp-amt{font-size:.81rem;font-weight:700;color:var(--text-heading);white-space:nowrap;}

/* ── INQUIRY DRAWER ── */
.inq-drawer{position:fixed;top:0;right:0;bottom:0;width:540px;max-width:100vw;background:var(--modal-bg);border-left:1px solid var(--card-border);box-shadow:var(--drawer-shadow);z-index:600;transform:translateX(100%);transition:transform .3s cubic-bezier(.4,0,.2,1);display:flex;flex-direction:column;}
.inq-drawer.open{transform:translateX(0);}
.inq-overlay{display:none;position:fixed;inset:0;background:var(--overlay-bg);z-index:599;backdrop-filter:blur(3px);}
.inq-overlay.show{display:block;}
.inq-hdr{display:flex;align-items:center;justify-content:space-between;padding:16px 20px;border-bottom:1px solid var(--border-color);flex-shrink:0;}
.inq-hdr-icon{width:38px;height:38px;border-radius:9px;background:linear-gradient(135deg,#9a8053,#b8975e);display:flex;align-items:center;justify-content:center;color:#fff;font-size:1rem;flex-shrink:0;}
.inq-body{flex:1;overflow-y:auto;padding:0;}
.inq-body::-webkit-scrollbar{width:5px;}
.inq-body::-webkit-scrollbar-thumb{background:var(--border-color);border-radius:3px;}
.inq-section{padding:17px 20px;border-bottom:1px solid var(--border-color);}
.inq-section:last-child{border-bottom:none;}
.sec-title{font-size:.72rem;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.07em;margin-bottom:12px;display:flex;align-items:center;gap:6px;}
.sec-title i{color:#9a8053;}
.locked-banner{display:flex;align-items:center;gap:10px;padding:10px 13px;background:rgba(154,128,83,.09);border:1px solid rgba(154,128,83,.22);border-radius:8px;margin-bottom:13px;}
.locked-banner i{color:#9a8053;font-size:1rem;flex-shrink:0;}
.locked-val{font-size:.8rem;font-weight:600;color:var(--text-heading);}
.locked-sub{font-size:.7rem;color:var(--text-muted);}
.form-label{font-size:.78rem;font-weight:500;color:var(--text-heading);margin-bottom:5px;display:block;}
.form-label .req{color:#ef4444;margin-left:2px;}
.auto-tag{font-size:.63rem;background:rgba(154,128,83,.12);color:#9a8053;padding:1px 6px;border-radius:4px;font-weight:600;margin-left:6px;}
.form-control,.form-select{background:var(--input-bg);border:1px solid var(--border-color);color:var(--text-primary);border-radius:7px;font-size:.8125rem;padding:7px 11px;width:100%;transition:border-color .15s,box-shadow .15s;}
.form-control:focus,.form-select:focus{outline:none;border-color:#9a8053;box-shadow:var(--input-focus-shadow);}
[data-bs-theme="dark"] .form-control,[data-bs-theme="dark"] .form-select{background:var(--input-bg);color:var(--text-primary);}
textarea.form-control{resize:vertical;min-height:88px;}
.form-group{margin-bottom:12px;}
.form-group:last-child{margin-bottom:0;}
.field-hint{font-size:.71rem;color:var(--text-muted);margin-top:4px;}
.char-hint{font-size:.69rem;color:var(--text-light);text-align:right;margin-top:3px;}
.warranty-toggle{display:flex;border:1px solid var(--border-color);border-radius:8px;overflow:hidden;}
.wt-btn{flex:1;padding:9px 10px;text-align:center;font-size:.8rem;font-weight:500;cursor:pointer;background:var(--input-bg);color:var(--text-muted);border:none;transition:background .15s,color .15s;}
.wt-btn.iw{background:rgba(16,185,129,.14);color:#059669;font-weight:600;}
.wt-btn.oow{background:rgba(139,92,246,.12);color:#7c3aed;font-weight:600;}
.wt-div{width:1px;background:var(--border-color);flex-shrink:0;}
.upload-zone{border:2px dashed var(--border-color);border-radius:8px;padding:18px;text-align:center;cursor:pointer;transition:border-color .15s,background .15s;}
.upload-zone:hover{border-color:#9a8053;background:rgba(154,128,83,.04);}
.upload-zone i{font-size:1.5rem;color:var(--text-light);display:block;margin-bottom:6px;}
.upload-zone p{font-size:.78rem;color:var(--text-muted);margin:0;}
.upload-zone span{font-size:.69rem;color:var(--text-light);}
.file-chips{display:flex;flex-wrap:wrap;gap:6px;margin-top:9px;}
.file-chip{display:inline-flex;align-items:center;gap:5px;padding:3px 8px;background:var(--surface-2);border:1px solid var(--border-color);border-radius:6px;font-size:.71rem;color:var(--text-muted);}
.file-chip button{background:none;border:none;color:var(--text-light);cursor:pointer;padding:0;line-height:1;font-size:.68rem;}
.file-chip button:hover{color:#ef4444;}
.wa-banner{display:flex;align-items:center;gap:10px;padding:10px 13px;background:rgba(37,211,102,.07);border:1px solid rgba(37,211,102,.2);border-radius:8px;}
.inq-foot{display:flex;align-items:center;justify-content:flex-end;gap:9px;padding:13px 20px;border-top:1px solid var(--border-color);background:var(--surface-2);flex-shrink:0;}

/* ── EMPTY STATE ── */
.empty-st{padding:44px 24px;text-align:center;}
.empty-st i{font-size:2rem;color:var(--text-light);display:block;margin-bottom:10px;}
.empty-st h6{font-size:1.05rem;color:var(--text-heading);margin-bottom:5px;}
.empty-st p{font-size:.79rem;color:var(--text-muted);max-width:300px;margin:0 auto 14px;}
.inq-drawer > form {
  display: flex;
  flex-direction: column;
  flex: 1;
  min-height: 0;   /* critical — lets the body actually shrink */
  overflow: hidden;
}

.inq-body {
  flex: 1;
  min-height: 0;
  overflow-y: auto;
}

.inq-foot {
  flex-shrink: 0;
}
/* ── TOAST ── */
#toastWrap{position:fixed;bottom:22px;right:22px;z-index:9999;display:flex;flex-direction:column;gap:8px;pointer-events:none;}
.toast-item{display:flex;align-items:flex-start;gap:10px;padding:12px 15px;border-radius:9px;background:var(--card-bg);border:1px solid var(--card-border);box-shadow:0 6px 24px rgba(0,0,0,.14);min-width:240px;max-width:320px;animation:toastIn .2s ease;pointer-events:auto;}
@keyframes toastIn{from{opacity:0;transform:translateY(10px);}to{opacity:1;transform:translateY(0);}}
.t-ico{font-size:1rem;flex-shrink:0;margin-top:1px;}
.t-ico.ok{color:#10b981;}.t-ico.err{color:#ef4444;}.t-ico.info{color:#9a8053;}
.t-title{font-size:.8rem;font-weight:600;color:var(--text-heading);margin:0 0 2px;}
.t-body{font-size:.75rem;color:var(--text-muted);margin:0;}

span#cds
{
  display: flex;
  justify-content: center;
  align-items: center;
  text-align: center;
}


.wt-static {
  display: flex; align-items: flex-start; gap: 10px;
  padding: 12px 14px; border-radius: 10px;
  border: 1px solid transparent;
}
.wt-static i { font-size: 1.05rem; flex: 0 0 auto; margin-top: 2px; }
.wt-static-title { font-size: .84rem; font-weight: 600; }
.wt-static-sub   { font-size: .72rem; opacity: .85; margin-top: 2px; line-height: 1.4; }

.wt-static.iw {
  background: rgba(16,185,129,.08);
  border-color: rgba(16,185,129,.25);
  color: #059669;
}
.wt-static.oow {
  background: rgba(139,92,246,.08);
  border-color: rgba(139,92,246,.25);
  color: #7c3aed;
}



.file-chips {
  display: flex; flex-direction: column; gap: 7px;
  margin-top: 10px;
}

.file-chip {
  display: flex; align-items: center; gap: 9px;
  padding: 7px 9px; min-width: 0;
  background: #fff;
  border: 1px solid #ececf2;
  border-radius: 9px;
}

.fc-thumb {
  flex: 0 0 auto;
  width: 36px; height: 36px;
  border-radius: 6px; overflow: hidden;
  background: #f4f5f9;
  display: flex; align-items: center; justify-content: center;
}
.fc-thumb img { width: 100%; height: 100%; object-fit: cover; display: block; }
.fc-thumb.pdf { background: rgba(239,68,68,.08); }
.fc-thumb.pdf i { color: #ef4444; font-size: 1.05rem; }

.fc-name {
  flex: 1 1 auto; min-width: 0;
  font-size: .74rem; font-weight: 600; color: #22252d;
  white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}

.fc-size {
  flex: 0 0 auto;
  font-size: .66rem; color: #9aa0ae;
}

.fc-x {
  flex: 0 0 auto;
  width: 22px;
  height: 22px;
  border: 0;
  border-radius: 50%;
  background: #f1f2f5;
   color: #6b7280;
  font-size: .58rem;
   cursor: pointer;
  display: flex;
   align-items: center; 
   justify-content: center;
}
.fc-x:hover { color: #fff; }


/* Laravel pagination links restyle */
  .cd-pagination-bar .pagination {
    margin: 0;
    gap: 4px;
  }

  .cd-pagination-bar .page-link {
    border-radius: 6px;
    border: 1px solid var(--border-color);
    background: var(--card-bg);
    color: var(--text-muted);
    font-size: .78rem;
    padding: 4px 10px;
  }

  .cd-pagination-bar .page-link:hover {
    border-color: #9A7B4F;
    color: #9A7B4F;
    background: var(--card-bg);
  }

  .cd-pagination-bar .page-item.active .page-link {
    background: #9A7B4F;
    color: #fff;
    border-color: #9A7B4F;
  }

  .cd-pagination-bar .page-item.disabled .page-link {
    color: var(--text-light);
    background: var(--card-bg);
  }

</style>
@endpush

<!-- SIDEBAR -->
<!-- <div class="sb-overlay" id="sbOverlay" onclick="closeSidebar()"></div> -->



  <!-- HERO -->
  {{-- resources/views/projects/show.blade.php --}}

@php
    $statusCfg = [
        'Pending'           => ['cls' => 'sb-pending',   'icon' => 'bi-hourglass'],
        'Approved'          => ['cls' => 'sb-approved',  'icon' => 'bi-check-circle'],
        'Forwarded'         => ['cls' => 'sb-assigned',  'icon' => 'bi-send'],
        'Rejected'          => ['cls' => 'sb-cancelled', 'icon' => 'bi-x-circle'],
        'Assigned'          => ['cls' => 'sb-assigned',  'icon' => 'bi-person-check'],
        'Quoted'            => ['cls' => 'sb-quoted',    'icon' => 'bi-receipt'],
        'In Progress'       => ['cls' => 'sb-inprog',    'icon' => 'bi-activity'],
        'Quote Rejected'    => ['cls' => 'sb-cancelled', 'icon' => 'bi-x-octagon'],
        'Qc Review'         => ['cls' => 'sb-review',    'icon' => 'bi-eye'],
        'Rework'            => ['cls' => 'sb-rework',    'icon' => 'bi-arrow-repeat'],
        'Reschedule'        => ['cls' => 'sb-rework',    'icon' => 'bi-calendar-event'],
        'Accepted'          => ['cls' => 'sb-approved',  'icon' => 'bi-hand-thumbs-up'],
        'Pending Invoice'   => ['cls' => 'sb-quoted',    'icon' => 'bi-receipt'],
        'Invoice Submitted' => ['cls' => 'sb-assigned',  'icon' => 'bi-file-earmark-check'],
        'Completed'         => ['cls' => 'sb-completed', 'icon' => 'bi-check2-all'],
        'On Hold'           => ['cls' => 'sb-pending',   'icon' => 'bi-pause-circle'],
    ];
@endphp

@section('content')

  {{-- HERO --}}
<div class="proj-hero">
  <div class="proj-hero-top">
    <div style="position:relative;z-index:1;">
      <div class="proj-hero-code"><i class="bi bi-diagram-3"></i>{{ $project->project_code }}</div>
      <div class="proj-hero-name">{{ $project->project_name }}</div>
      <div class="proj-hero-client">
        <div class="cdot">{{ strtoupper(substr($project->client?->company_name ?? '?', 0, 1)) }}</div>
        {{ $project->client?->company_name ?? '—' }} &nbsp;·&nbsp; {{ $project->client?->unique_code }}
      </div>
    </div>
  </div>
  <div class="proj-hero-meta">
    <span class="hero-badge {{ $project->status === 'Active' ? 'green' : '' }}">
      <i class="bi bi-check-circle-fill"></i>{{ $project->status }}
    </span>
    <span class="hero-badge"><i class="bi bi-geo-alt-fill"></i>{{ $project->site_name }}</span>
    <span class="hero-badge"><i class="bi bi-calendar3"></i>Since {{ $project->completion_date?->format('M Y') ?? '—' }}</span>
    <span class="hero-badge"><i class="bi bi-shield-check"></i>{{ $project->warranty?->name ?? '—' }}</span>
    @if($project->client?->primary_mobile)
      <span class="hero-badge"><i class="bi bi-telephone-fill"></i>{{ $project->client->primary_country }} {{ $project->client->primary_mobile }}</span>
    @endif
  </div>
</div>


  {{-- STATS --}}
  <div class="stats-strip">
    <div class="stat-card">
      <div class="stat-icon" style="background:rgba(124,58,237,.1);"><i class="bi bi-ticket-detailed" style="color:#7c3aed;"></i></div>
      <div><div class="stat-num">{{ $stats['total'] }}</div><div class="stat-lbl">Total SRs</div></div>
    </div>
    <div class="stat-card">
      <div class="stat-icon" style="background:rgba(6,182,212,.1);"><i class="bi bi-activity" style="color:#0891b2;"></i></div>
      <div><div class="stat-num">{{ $stats['active'] }}</div><div class="stat-lbl">Active Now</div></div>
    </div>
    <div class="stat-card">
      <div class="stat-icon" style="background:rgba(16,185,129,.1);"><i class="bi bi-check2-all" style="color:#10b981;"></i></div>
      <div><div class="stat-num">{{ $stats['completed'] }}</div><div class="stat-lbl">Completed</div></div>
    </div>
    <div class="stat-card">
      <div class="stat-icon" style="background:rgba(239,68,68,.1);"><i class="bi bi-slash-circle" style="color:#ef4444;"></i></div>
      <div><div class="stat-num">{{ $stats['cancelled'] }}</div><div class="stat-lbl">Cancelled</div></div>
    </div>
    <div class="stat-card">
      <div class="stat-icon" style="background:rgba(154,128,83,.12);"><i class="bi bi-star-fill" style="color:#9a8053;"></i></div>
      <div><div class="stat-num">{{ $stats['rating'] ?: '—' }}</div><div class="stat-lbl">Avg Rating</div></div>
    </div>
  </div>

  <div class="view-layout">

    {{-- SR LOG --}}
    <div style="min-width:0;">
      <div class="card">
        <div class="card-hdr">
          <div class="card-hdr-left">
            <span class="card-hdr-title">Service Request Log</span>
            <span class="result-count" id="sr-count">{{ $srs->count() }} records</span>
          </div>
          <div style="display:flex;gap:7px;">
            <a class="btn-ghost" href=""><i class="bi bi-download"></i>Export</a>

                  @if (auth()->user()?->role?->code !== 'SE')
            <!-- <button class="btn-gold" onclick="openInquiry()"><i class="bi bi-plus-lg"></i>New Inquiry</button> -->
            @endif
          </div>
        </div>

        <div class="sr-filter-bar">
          <div class="filter-group">
            <div class="filter-label">Search</div>
            <input class="filter-control" id="sr-search" type="text" placeholder="SR ID, description…" oninput="filterSR()"/>
          </div>
          <div class="filter-group">
            <div class="filter-label">Status</div>
            <select class="filter-control" id="sr-status" onchange="filterSR()">
  <option value="">All Statuses</option>
  @foreach(array_keys($statusCfg) as $st)
    <option value="{{ $st }}">{{ Str::headline($st) }}</option>
  @endforeach
</select>
          </div>
          <div class="filter-group">
            <div class="filter-label">Warranty</div>
            <select class="filter-control" id="sr-warranty" onchange="filterSR()">
              <option value="">All</option>
              <option value="iw">In-Warranty</option>
              <option value="oow">Out-of-Warranty</option>
            </select>
          </div>
          <div class="filter-group">
            <div class="filter-label">From Date</div>
            <input class="filter-control" id="sr-date" type="date" onchange="filterSR()"/>
          </div>
          <div class="filter-actions">
            <button class="btn-ghost" onclick="resetSRFilters()"><i class="bi bi-x-circle"></i>Reset</button>
          </div>
        </div>

        <div class="tbl-wrap">
          <table class="listing">
  <thead>
    <tr>
      <th>SR ID</th>
      <th>Raised</th>
      <th>Category</th>
      <!-- <th>Description</th> -->
      <th>Technician</th>
      <th>Warranty</th>
      <!-- <th>SLA</th> -->
      <th>Status</th>
      <th style="width:90px;">Actions</th>
    </tr>
  </thead>
  <tbody id="sr-tbody">
    @forelse($srsPaginated  as $sr)
      @php
        $cfg = $statusCfg[$sr->status] ?? ['cls' => 'sb-pending'];
        $catName = $sr->category?->category_name ?? '—';
        $closed  = in_array($sr->status, ['Completed', 'Rejected']);
        $elapsed = $sr->created_at ? (int) $sr->created_at->diffInHours(now()) : 0;
        $target  = match($sr->priority_level) {
            'Emergency' => 4,
            'High'      => 24,
            default     => 48,
        };

         $warrantyEnd  = optional($sr->project)->warranty_end_date;
  $isInWarranty = $warrantyEnd
      && \Carbon\Carbon::parse($warrantyEnd)->endOfDay()->isFuture();
      
      @endphp
      <tr class="sr-row"
          data-id="{{ strtolower($sr->code) }}"
          data-desc="{{ strtolower($sr->issue_description) }}"
          data-category="{{ strtolower($catName) }}"
          data-status="{{ $sr->status }}"
          data-warranty="{{ $isInWarranty ? 'iw' : 'oow' }}"
          data-date="{{ $sr->created_at?->toDateString() }}">

        <td class="mono">{{ $sr->code }}</td>
        <td class="muted">{{ $sr->created_at?->format('d M Y') ?? '—' }}</td>

        <td style="font-size:.76rem;max-width:105px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
          @if($sr->category)
            <!-- <i class="bi {{ $sr->category->icon }}" style="color:{{ $sr->category->color_code }};font-size:.7rem;"></i> -->
          @endif
          {{ $catName }}
        </td>

        <!-- <td style="max-width:190px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;font-size:.77rem;"
            title="{{ $sr->issue_description }}">{{ $sr->issue_description }}</td> -->

        <td style="font-size:.77rem;">
          @if($sr->assignedUser)
            <div style="line-height:1.25;">{{ $sr->assignedUser->name }}
              <div style="font-size:.66rem;color:var(--text-muted);">ML-00{{ $sr->assigned_user_id }}</div>
            </div>
          @else
            <span style="color:var(--text-light);font-size:.77rem;">Unassigned</span>
          @endif
        </td>

       <td>
  @if($isInWarranty)
    <span class="sbadge" style="background:rgba(16,185,129,.1);color:#059669;font-size:.66rem;"><i class="bi bi-shield-fill-check" style="font-size:.55rem;"></i>In Warranty</span>
  @else
    <span class="sbadge" style="background:rgba(139,92,246,.1);color:#7c3aed;font-size:.66rem;"><i class="bi bi-currency-dollar" style="font-size:.55rem;"></i> Non-Warranty</span>
  @endif
</td>

      

        <td>
 <span class="sbadge {{ $cfg['cls'] }}">
    <i class="bi bi-circle-fill" style="font-size:.32rem;"></i>{{ Str::headline($sr->status) }}
  </span>
</td>

        <td>
          <div class="row-actions">
            <button class="btn-xs btn-xs-view" onclick="openDrawer({{ $sr->id }})"><i class="bi bi-eye"></i>View</button>
          </div>
        </td>
      </tr>
    @empty
      <tr>
        <td colspan="9">
          <div class="empty-st">
            <i class="bi bi-inbox"></i><h6>No Records Found</h6>
            <p>No service requests raised against this project yet.</p>
          </div>
        </td>
      </tr>
    @endforelse
  </tbody>
</table>

        </div>

        <!-- <div class="pagination-bar">
          <div class="page-info" id="sr-page-info">{{ $srs->count() }} records</div>
        </div> -->

        
        <div class="cd-pagination-bar">
    <div class="cd-page-info">
      @if($srsPaginated->total() > 0)
      @else
   
      @endif
    </div>
    <div>
      {{ $srsPaginated->onEachSide(1)->links('pagination::bootstrap-5') }}
    </div>
  </div>


      </div>
    </div>
    

    {{-- RIGHT PANEL --}}
    <div style="min-width:0;overflow:hidden;">

   <div class="info-card">
  <div class="info-card-hdr"><i class="bi bi-info-circle-fill"></i><span class="info-card-title">Project Details</span></div>

  <div class="info-row"><span class="info-key">Project Code</span><span class="info-val mono">{{ $project->project_code }}</span></div>
  <div class="info-row"><span class="info-key">Project Name</span><span class="info-val">{{ $project->project_name }}</span></div>
  <div class="info-row"><span class="info-key">Customer</span><span class="info-val">{{ $project->client?->company_name ?? '—' }}</span></div>
  <div class="info-row"><span class="info-key">Customer Token</span><span class="info-val mono">{{ $project->client?->unique_code ?? '—' }}</span></div>
  <div class="info-row"><span class="info-key">Contact Person</span><span class="info-val">{{ $project->client?->contact_name ?? '—' }}</span></div>
  <!-- <div class="info-row"><span class="info-key">Designation</span><span class="info-val">{{ $project->client?->designation ?? '—' }}</span></div> -->
  <div class="info-row"><span class="info-key">Primary Contact</span><span class="info-val">{{ $project->client?->primary_country }} {{ $project->client?->primary_mobile ?? '—' }}</span></div>

  <div class="info-row"><span class="info-key">Site Name</span><span class="info-val">{{ $project->site_name }}</span></div>
  <div class="info-row"><span class="info-key">Site Address</span><span class="info-val" style="font-size:.72rem;line-height:1.4;">{{ $project->site_address }}</span></div>


  @if(filled($project->project_engineer))
  <div class="info-row"><span class="info-key">Project Engineer</span><span class="info-val">{{ $project->project_engineer }}</span></div>
@endif

@if(filled($project->engineer_contact))
  <div class="info-row"><span class="info-key">Engineer Contact</span>
      <span class="info-val mono">{{ trim(($project->engineer_country ?? '') . ' ' . $project->engineer_contact) }}</span>
</div>
@endif
  <div class="info-row"><span class="info-key">Project Date</span><span class="info-val">{{ $project->updated_at?->format('d M Y') ?? '—' }}</span></div>

  <div class="info-row"><span class="info-key">Completion Date</span><span class="info-val">{{ $project->completion_date?->format('d M Y') ?? '—' }}</span></div>

  <div class="info-row">
    <span class="info-key">Status</span>
    <span class="info-val">
      <span class="sbadge sb-active-g" style="font-size:.68rem;color:#059669 !important"><i class="bi bi-circle-fill" style="font-size:.32rem;"></i>{{ $project->status }}</span>
    </span>
  </div>

    <div class="info-row"><span class="info-key">Warranty Type</span>  <span class="info-val">{{ $project->warranty?->name ?? '—' }}</span></div>
  <div class="info-row"><span class="info-key">Warranty Start</span><span class="info-val">{{ $project->completion_date?->format('d M Y') ?? '—' }}</span></div>
  <div class="info-row"><span class="info-key">Warranty End</span><span class="info-val">{{ $project->warranty_end_date?->format('d M Y') ?? '—' }}</span></div>

  @if($project->site_address)
    <!-- <div style="padding:11px 16px;">
      <a class="btn-ghost" style="width:100%;justify-content:center;font-size:.78rem;" target="_blank"
         href="https://www.google.com/maps/search/?api=1&query={{ urlencode($project->site_address) }}">
        <i class="bi bi-geo-alt"></i>View on Google Maps
      </a>
    </div> -->
  @endif
</div>


      {{-- DONUT --}}
     <div class="info-card">
  <div class="info-card-hdr">
    <i class="bi bi-pie-chart-fill"></i>
    <span class="info-card-title">SR Status Breakdown</span>
  </div>

  @if($stats['total'] === 0)
    <div style="padding:24px 16px;text-align:center;font-size:.78rem;color:var(--text-muted);">
      <i class="bi bi-pie-chart" style="font-size:1.6rem;display:block;margin-bottom:8px;opacity:.35;"></i>
      No service requests yet.
    </div>
  @else
    @php $offset = 25; @endphp

    <div class="donut-wrap">
      <svg width="100" height="100" viewBox="0 0 36 36" xmlns="http://www.w3.org/2000/svg">
        <circle cx="18" cy="18" r="15.915" fill="none" stroke="var(--surface-2)" stroke-width="3.5"/>

        @foreach($breakdown as $slice)
          @php $pct = round($slice['count'] / $stats['total'] * 100, 2); @endphp
          <circle cx="18" cy="18" r="15.915" fill="none"
                  stroke="{{ $slice['color'] }}" stroke-width="3.5"
                  stroke-dasharray="{{ $pct }} {{ 100 - $pct }}"
                  stroke-dashoffset="{{ $offset }}"
                  stroke-linecap="butt">
            <title>{{ $slice['label'] }} — {{ $slice['count'] }}</title>
          </circle>
          @php $offset -= $pct; @endphp
        @endforeach

        <text x="18" y="20" text-anchor="middle" font-size="6.5" font-weight="700"
              fill="var(--text-heading)">{{ $stats['total'] }}</text>
      </svg>

      <div class="donut-legend">
        @foreach($breakdown as $slice)
          <div class="legend-row">
            <div class="legend-dot" style="background:{{ $slice['color'] }};"></div>
            {{ $slice['label'] }} — {{ $slice['count'] }} ({{ round($slice['count'] / $stats['total'] * 100) }}%)
          </div>
        @endforeach
      </div>
    </div>
  @endif
</div>
      {{-- ACTIVITY --}}
      <div class="info-card">
  <div class="info-card-hdr">
    <i class="bi bi-clock-history"></i>
    <span class="info-card-title">Recent Activity</span>
  </div>

  <div class="timeline">
    @forelse($activities as $act)
      <div class="tl-item">
        <div class="tl-dot" style="background:{{ $act['bg'] }};border-color:{{ $act['color'] }}4d;">
          <i class="bi {{ $act['icon'] }}" style="color:{{ $act['color'] }};font-size:.65rem;"></i>
        </div>
        <div class="tl-content">
          <div class="tl-action">{{ $act['title'] }}</div>
          <div class="tl-by">by {{ $act['by'] }}</div>
          <div class="tl-time">
            @if($act['at']->isToday())
              Today, {{ $act['at']->format('h:i A') }}
            @elseif($act['at']->isYesterday())
              Yesterday, {{ $act['at']->format('h:i A') }}
            @else
              {{ $act['at']->diffForHumans() }}
            @endif
          </div>
        </div>
      </div>
    @empty
      <div style="font-size:.78rem;color:var(--text-muted);padding:8px 0;">No recent activity.</div>
    @endforelse
  </div>
</div>

    </div>
  </div>

  {{-- SR DETAIL DRAWER --}}
  <div class="drawer-overlay" id="drawerOverlay" onclick="closeDrawer()"></div>
  <div class="drawer" id="srDrawer">
    <div class="drawer-hdr">
      <div class="drawer-hdr-left">
        <div class="dhdr-icon" id="dhdrIcon" style="background:rgba(6,182,212,.1);color:#0891b2;"><i class="bi bi-ticket-detailed"></i></div>
        <div>
          <div class="drawer-title" id="dTitle">—</div>
          <div class="drawer-sub" id="dSub">—</div>
        </div>
      </div>
      <button class="drawer-close" onclick="closeDrawer()"><i class="bi bi-x-lg"></i></button>
    </div>
    <div class="drawer-body" id="drawerBody"></div>
  </div>

  {{-- INQUIRY DRAWER --}}
  <div class="inq-overlay" id="inqOverlay" onclick="closeInquiry()"></div>
  <div class="inq-drawer" id="inqDrawer">
    <div class="inq-hdr">
      <div style="display:flex;align-items:center;gap:11px;">
        <div class="inq-hdr-icon"><i class="bi bi-plus-lg"></i></div>
        <div>
          <div class="drawer-title">New Service Inquiry</div>
          <div class="drawer-sub">Raising against: {{ $project->name }}</div>
        </div>
      </div>
      <button class="drawer-close" onclick="closeInquiry()"><i class="bi bi-x-lg"></i></button>
    </div>

   <form id="inqForm" action="{{ route('inquiries.store', $project) }}" method="POST" enctype="multipart/form-data">
  @csrf
  <div class="inq-body">

    <div class="inq-section">
      <div class="sec-title"><i class="bi bi-lock-fill"></i>Project Context — Auto-filled</div>
      <div class="locked-banner">
        <i class="bi bi-diagram-3"></i>
        <div>
          <div class="locked-val">{{ $project->project_name }} · {{ $project->project_code }}</div>
          <div class="locked-sub">{{ $project->site_name }} · {{ $project->client?->company_name  }}</div>
        </div>
      </div>
    <div class="form-group" style="margin-bottom:0;">
  <label class="form-label">SR Reference <span class="auto-tag">AUTO</span></label>
  <input type="text" class="form-control" value="{{ $nextSrCode }}" readonly
    style="background:var(--surface-2);color:#9a8053;font-weight:600;cursor:not-allowed;"/>
</div>
    </div>

    <div class="inq-section">
      <div class="sec-title"><i class="bi bi-tools"></i>Issue Details</div>

      <div class="form-group">
        <label class="form-label">Service Category <span class="req">*</span></label>
        <select class="form-select" name="service_type_id" id="inqCat" required>
          <option value="">— Select category —</option>
          @foreach($categories as $cat)
            <option value="{{ $cat->id }}" @selected(old('service_type_id') == $cat->id)>{{ $cat->category_name }}</option>
          @endforeach
        </select>
        @error('service_type_id')<div class="field-hint" style="color:#ef4444;">{{ $message }}</div>@enderror
      </div>

      <div class="form-group">
        <label class="form-label">Problem Description <span class="req">*</span></label>
        <textarea class="form-control" name="issue_description" id="inqDesc" maxlength="500" required
          placeholder="Describe the issue — what is failing, where, any symptoms observed…"
          oninput="document.getElementById('inqChar').textContent=this.value.length">{{ old('issue_description') }}</textarea>
        <div class="char-hint"><span id="inqChar">0</span>/500</div>
        <div class="field-hint">Minimum 20 characters required.</div>
        @error('issue_description')<div class="field-hint" style="color:#ef4444;">{{ $message }}</div>@enderror
      </div>

      <div class="form-group" style="margin-bottom:0;">
        <label class="form-label">Remarks / Additional Notes</label>
        <textarea class="form-control" name="internal_remark" id="inqRemark" rows="2"
          placeholder="Access instructions, preferred time windows, or context for the technician…">{{ old('internal_remark') }}</textarea>
        @error('internal_remark')<div class="field-hint" style="color:#ef4444;">{{ $message }}</div>@enderror
      </div>
    </div>

    <div class="inq-section">
      <div class="sec-title"><i class="bi bi-shield-check"></i>Warranty &amp; Priority</div>

      @php
  $warrantyEnd  = $project->warranty_end_date ?? null;
  $isInWarranty = $warrantyEnd
      && \Carbon\Carbon::parse($warrantyEnd)->endOfDay()->isFuture();
  $scope = $isInWarranty ? 'iw' : 'oow';
@endphp

     <div class="form-group">
  <label class="form-label">Warranty Coverage</label>

  <input type="hidden" name="warranty_scope" id="warrantyInput" value="{{ $scope }}">

  <div class="wt-static {{ $scope }}">
    @if($isInWarranty)
      <i class="bi bi-shield-fill-check"></i>
      <div>
        <div class="wt-static-title">In-Warranty</div>
        <div class="wt-static-sub">
          Covered until {{ \Carbon\Carbon::parse($warrantyEnd)->format('d M Y') }}
       
        </div>
      </div>
    @else
      <i class="bi bi-currency-dollar"></i>
      <div>
        <div class="wt-static-title">Out-of-Warranty</div>
        <div class="wt-static-sub">
          @if($warrantyEnd)
            Warranty expired on {{ \Carbon\Carbon::parse($warrantyEnd)->format('d M Y') }}. Quotation required.
          @else
            No warranty period recorded for this contract. Quotation required.
          @endif
        </div>
      </div>
    @endif
  </div>
</div>


   <div class="form-group" style="margin-bottom:0;">
  <label class="form-label">Priority Level</label>
  <select class="form-select" name="priority_level" id="inqPriority">
    <option value="">— Select priority —</option>
    @foreach(($priorities ?? []) as $p)
      <option value="{{ $p->name }}"
              data-color="{{ $p->color }}"
              @selected(old('priority_level', $serviceRequest->priority_level ?? '') === $p->name)>
        {{ $p->name }}
      </option>
    @endforeach
  </select>
</div>
    </div>

    <div class="inq-section">
      <div class="sec-title"><i class="bi bi-images"></i>Asset Photos <span style="font-weight:400;text-transform:none;font-size:.72rem;letter-spacing:0;color:var(--text-light);">(optional)</span></div>
      <div class="upload-zone" onclick="document.getElementById('inqFileInput').click()">
        <i class="bi bi-cloud-arrow-up"></i>
        <p>Click to attach photos or drag &amp; drop here</p>
        <span>.jpg · .png · .pdf &nbsp;|&nbsp; Max 10 MB each</span>
      </div>
      <input type="file" id="inqFileInput" name="photos[]" multiple accept=".jpg,.jpeg,.png,.pdf"
        style="display:none;" onchange="handleFiles(this)"/>
      <div class="file-chips" id="fileChips"></div>
      @error('photos.*')<div class="field-hint" style="color:#ef4444;">{{ $message }}</div>@enderror
    </div>

    <div class="inq-section">
      <div class="sec-title"><i class="bi bi-whatsapp"></i>WhatsApp Notification</div>
      <div class="wa-banner">
        <i class="bi bi-whatsapp" style="color:#25d366;font-size:1.1rem;flex-shrink:0;"></i>
        <div style="flex:1;">
          <div style="font-size:.78rem;font-weight:600;color:var(--text-heading);">Ticket receipt will be auto-sent</div>
          <div style="font-size:.7rem;color:var(--text-muted);">  {{ $project->client?->company_name }} · {{ $project->client?->primary_country }} {{ $project->client?->primary_mobile }}
</div>
        </div>
        <span class="sbadge sb-approved">Auto</span>
      </div>
    </div>

  </div>
  <div class="inq-foot">
    <button type="button" class="btn-ghost" onclick="closeInquiry()">Cancel</button>
    <button type="submit" class="btn-gold" id="inqSubmitBtn"><i class="bi bi-send-fill"></i>Submit Inquiry</button>
  </div>
</form>
  </div>

  <div id="toastWrap"></div>
@endsection


@push('scripts')
<script>
/* SR payload for the drawer, straight from the DB */
const SR_MAP = @json($srMap);
const STATUS_CFG = @json($statusCfg);

/* THEME */
function toggleTheme() {
  const dark = document.documentElement.getAttribute('data-bs-theme') === 'dark';
  document.documentElement.setAttribute('data-bs-theme', dark ? 'light' : 'dark');
  try { ['du_theme','sp_theme','mm_theme'].forEach(k => localStorage.setItem(k, dark ? 'light' : 'dark')); } catch(e){}
}
(function(){
  try {
    let s = null;
    for (const k of ['sp_theme','mm_theme','du_theme']) { s = localStorage.getItem(k); if (s) break; }
    if (s) document.documentElement.setAttribute('data-bs-theme', s);
    else if (matchMedia('(prefers-color-scheme:dark)').matches) document.documentElement.setAttribute('data-bs-theme','dark');
  } catch(e){}
})();

/* FILTERS — operate on server-rendered rows */
function filterSR() {
  const q  = document.getElementById('sr-search').value.toLowerCase();
  const st = document.getElementById('sr-status').value;
  const wt = document.getElementById('sr-warranty').value;
  const dt = document.getElementById('sr-date').value;
  let shown = 0;

  document.querySelectorAll('.sr-row').forEach(row => {
    const d = row.dataset;
    let ok = true;
    if (q && !d.id.includes(q) && !d.desc.includes(q) && !d.category.includes(q)) ok = false;
    if (st && d.status !== st) ok = false;
    if (wt && d.warranty !== wt) ok = false;
    if (dt && d.date < dt) ok = false;
    row.style.display = ok ? '' : 'none';
    if (ok) shown++;
  });

  document.getElementById('sr-count').textContent = shown + (shown === 1 ? ' record' : ' records');
  document.getElementById('sr-page-info').textContent = shown ? shown + ' records' : 'No results';
}

function resetSRFilters() {
  ['sr-search','sr-status','sr-warranty','sr-date'].forEach(id => document.getElementById(id).value = '');
  filterSR();
}

/* SR DRAWER */
function statusBadge(st) {
  const cfg = STATUS_CFG[st] || {cls:'sb-pending'};
  return `<span class="sbadge ${cfg.cls}"><i class="bi bi-circle-fill" style="font-size:.32rem;"></i>${st}</span>`;
}

function slaLabel(sr) {
  if (sr.status === 'Completed' || sr.status === 'Accepted') return '<span class="sla-ok"><i class="bi bi-check-lg"></i> Closed</span>';
  if (sr.sla_hours < 24)  return `<span class="sla-ok"><i class="bi bi-check-circle"></i> ${sr.sla_hours}h left</span>`;
  if (sr.sla_hours < 52)  return `<span class="sla-warn"><i class="bi bi-exclamation-circle"></i> ${sr.sla_hours}h elapsed</span>`;
  return `<span class="sla-breach"><i class="bi bi-x-circle"></i> +${sr.sla_hours - 52}h breach</span>`;
}

function photoCell(url, label) {
  return url
    ? `<a class="photo-thumb has-photo" href="${url}" target="_blank"><i class="bi bi-image-fill"></i><span class="ph-lbl">${label}</span><span style="font-size:.64rem;color:#9a8053;font-weight:600;">View</span></a>`
    : `<div class="photo-thumb"><i class="bi bi-camera"></i><span class="ph-lbl">${label}</span></div>`;
}

function openDrawer(id) {
  var sr = SR_MAP[id];
  if (!sr) { console.warn('SR not found:', id); return; }

  var PROJECT_NAME = @js($project->project_name);
  var PROJECT_SITE = @js($project->site_name);

  /* ── header ── */
  document.getElementById('dTitle').textContent = sr.code;
  document.getElementById('dSub').textContent   = sr.category;
  var cfg = STATUS_CFG[sr.status] || { icon: 'bi-ticket-detailed' };
  document.getElementById('dhdrIcon').innerHTML = '<i class="bi ' + cfg.icon + '"></i>';

  /* ── work evidence photos (from punches table) ── */
  var bPh = sr.before
    ? '<a class="photo-thumb has-photo" href="' + sr.before + '" target="_blank"><i class="bi bi-image-fill"></i><span class="ph-lbl">Before Photo</span><span style="font-size:.64rem;color:#9a8053;font-weight:600;">View</span></a>'
    : '<div class="photo-thumb" onclick="showToast(\'info\',\'No Photo\',\'Before photo not yet uploaded\')"><i class="bi bi-camera"></i><span class="ph-lbl">Before Photo</span></div>';

  var aPh = sr.after
    ? '<a class="photo-thumb has-photo" href="' + sr.after + '" target="_blank"><i class="bi bi-image-fill"></i><span class="ph-lbl">After Photo</span><span style="font-size:.64rem;color:#9a8053;font-weight:600;">View</span></a>'
    : '<div class="photo-thumb" onclick="showToast(\'info\',\'No Photo\',\'After photo not yet uploaded\')"><i class="bi bi-camera"></i><span class="ph-lbl">After Photo</span></div>';

  /* ── completion summary ── */
  var summaryHtml = sr.summary
    ? '<div class="det-cell full" style="margin-bottom:10px;"><div class="det-key">Completion Summary</div><div class="det-val" style="font-size:.79rem;line-height:1.5;">' + sr.summary + '</div></div>'
    : '';

  /* ── acceptance sheet (customer signature) ── */
  var acceptBtn = sr.signature
    ? '<div style="margin-top:9px;"><a class="btn-ghost" style="width:100%;justify-content:center;" href="' + sr.signature + '" target="_blank" download><i class="bi bi-file-earmark-arrow-down"></i>Download Acceptance Sheet</a></div>'
    : '<div style="margin-top:9px;"><button class="btn-ghost" style="width:100%;justify-content:center;opacity:.45;cursor:not-allowed;" disabled><i class="bi bi-file-earmark-x"></i>Acceptance Sheet Not Signed</button></div>';

  /* ── expenses ── */
  var expRows;
var items = sr.items || [];

if (items.length) {
  expRows = items.map(function (i) {
    return '<div class="exp-row">' +
      '<span class="exp-cat">' + (i.category || i.name) + '</span>' +
      '<span class="exp-desc" id="cds">' +
        i.name +
      '</span>' +
      '<span class="exp-amt">AED ' + i.total + '</span>' +
    '</div>';
  }).join('');

  expRows +='<div style="display:flex;justify-content:flex-end;margin-top:8px;font-size:.78rem;font-weight:700;color:var(--text-heading);">Total: AED ' + sr.total + '</div>';
} else if (sr.total && Number(sr.total) > 0) {
  expRows =
    
    '<div style="display:flex;justify-content:flex-end;margin-top:8px;font-size:.78rem;font-weight:700;color:var(--text-heading);">Total: AED ' + sr.total + '</div>';
} else {
  expRows = '<div style="font-size:.78rem;color:var(--text-muted);padding:4px 0;">No material expenses logged.</div>';
}
  /* ── rating ── */
 /* ── rating ── */
  var stars = '';
  for (var s = 1; s <= 5; s++) {
    stars += (sr.rating && s <= sr.rating)
      ? '<i class="bi bi-star-fill" style="color:#fbbc06;font-size:.85rem;"></i>'
      : '<i class="bi bi-star" style="color:var(--text-light);font-size:.85rem;"></i>';
  }
  var ratingHtml = sr.rating
    ? '<div style="display:flex;align-items:center;gap:6px;margin-top:3px;">' +
        '<div style="display:flex;gap:3px;">' + stars + '</div>' +
        '<span style="font-size:.75rem;font-weight:600;color:var(--text-heading);">' + sr.rating + '/5</span>' +
      '</div>'
    : '<span style="font-size:.75rem;color:var(--text-light);">Not yet rated</span>';

  /* ── conditional assignment rows ── */
  var extraRows = '';
 
  
  /* ── customer block (only if punch captured it) ── */
  var customerRows = '';
  if (sr.customer) {
    customerRows =
      '<div class="det-cell"><div class="det-key">Customer Name</div><div class="det-val">' + sr.customer + '</div></div>' +
      (sr.customer_phone ? '<div class="det-cell"><div class="det-key">Customer Phone</div><div class="det-val mono">' + sr.customer_phone + '</div></div>' : '');
  }

  document.getElementById('drawerBody').innerHTML =
    /* Overview */
    '<div class="d-section">' +
      '<div class="d-sec-title"><i class="bi bi-info-circle"></i>Ticket Overview</div>' +
      '<div class="det-grid">' +
        '<div class="det-cell"><div class="det-key">SR ID</div><div class="det-val mono">' + sr.code + '</div></div>' +
        '<div class="det-cell"><div class="det-key">Current Status</div><div class="det-val">' + statusBadge(sr.status) + '</div></div>' +
        '<div class="det-cell"><div class="det-key">Date Raised</div><div class="det-val">' + (sr.date || '—') + '</div></div>' +
        '<div class="det-cell"><div class="det-key">Priority</div><div class="det-val">' + (sr.priority || '—') + '</div></div>' +
        '<div class="det-cell"><div class="det-key">Warranty Scope</div><div class="det-val">' + (sr.iw
            ? '<span style="color:#059669;font-weight:600;">In-Warranty</span>'
            : '<span style="color:#7c3aed;font-weight:600;">Out-of-Warranty</span>') + '</div></div>' +
        '<div class="det-cell"><div class="det-key">Category</div><div class="det-val">' + sr.category + '</div></div>' +
        '<div class="det-cell full"><div class="det-key">Problem Description</div><div class="det-val" style="font-size:.79rem;line-height:1.5;">' + sr.desc + '</div></div>' +
        
      '</div>' +
    '</div>' +

    /* Assignment & Site */
    '<div class="d-section">' +
      '<div class="d-sec-title"><i class="bi bi-person-workspace"></i>Assignment &amp; Site</div>' +
      '<div class="det-grid">' +
        '<div class="det-cell"><div class="det-key">Project</div><div class="det-val">' + PROJECT_NAME + '</div></div>' +
        '<div class="det-cell"><div class="det-key">Site</div><div class="det-val">' + PROJECT_SITE + '</div></div>' +
        '<div class="det-cell"><div class="det-key">Technician</div><div class="det-val">' + (sr.tech || '<span style="color:var(--text-light);">Unassigned</span>') + '</div></div>' +
        '<div class="det-cell"><div class="det-key">Tech ID</div><div class="det-val mono">' + (sr.tech_id ? 'ML-' + String(sr.tech_id).padStart(3, '0') : '—') + '</div></div>' +
        '<div class="det-cell"><div class="det-key">Punch-In</div><div class="det-val">' + (sr.punch_in || '<span style="color:var(--text-light);">Not punched in</span>') + '</div></div>' +
        // '<div class="det-cell"><div class="det-key">Punch-Out</div><div class="det-val">' + (sr.punch_out || '<span style="color:var(--text-light);">On site</span>') + '</div></div>' +
        '<div class="det-cell"><div class="det-key">Duration On-Site</div><div class="det-val">' + (sr.duration || '—') + '</div></div>' +
        
        extraRows +
      '</div>' +
    '</div>' +

    /* Work Evidence */
    '<div class="d-section">' +
      '<div class="d-sec-title"><i class="bi bi-images"></i>Work Evidence</div>' +
      // '<div class="det-grid">' + summaryHtml + '</div>' +
      '<div class="photo-grid">' + bPh + aPh + '</div>' +
      acceptBtn +
    '</div>' +

    /* Expenses */
    '<div class="d-section">' +
      '<div class="d-sec-title"><i class="bi bi-cash-stack"></i>Material Expenses</div>' +
       expRows +
    '</div>' +
    /* Feedback */


    '<div class="d-section">' +
      '<div class="d-sec-title"><i class="bi bi-star-half"></i>Customers Feedback</div>' +
      '<div class="det-cell" style="display:block;">' +
        '<div class="det-key">Technician Rating</div>' +
        ratingHtml +
        (sr.rating_comment
          ? '<div class="det-key" style="margin-top:12px;">Evaluation Comment</div>' +
            '<div class="det-val" style="font-size:.79rem;line-height:1.5;margin-top:3px;">' + escapeHtml(sr.rating_comment) + '</div>'
          : '') +
        (sr.rating
          ? '<div style="font-size:.71rem;color:var(--text-muted);margin-top:8px;"><i class="bi bi-lock-fill" style="font-size:.65rem;"></i> Feedback locked — submitted ' + (sr.rated_at || 'by client') + '</div>'
          : '') +
      '</div>' +
    '</div>';
    /* Feedback */
   

  document.getElementById('drawerOverlay').classList.add('show');
  document.getElementById('srDrawer').classList.add('open');
  document.body.style.overflow = 'hidden';
}


function escapeHtml(str){
  var d = document.createElement('div');
  d.textContent = str == null ? '' : str;
  return d.innerHTML;
}


function closeDrawer() {
  document.getElementById('drawerOverlay').classList.remove('show');
  document.getElementById('srDrawer').classList.remove('open');
  if (!document.getElementById('inqDrawer').classList.contains('open')) document.body.style.overflow = '';
}

/* INQUIRY DRAWER */
function openInquiry() {
  document.getElementById('inqOverlay').classList.add('show');
  document.getElementById('inqDrawer').classList.add('open');
  document.body.style.overflow = 'hidden';
}
function closeInquiry() {
  document.getElementById('inqOverlay').classList.remove('show');
  document.getElementById('inqDrawer').classList.remove('open');
  document.body.style.overflow = '';
}

function setWarranty(mode) {
  document.getElementById('warrantyInput').value = mode;
  document.getElementById('wtIW').className  = 'wt-btn' + (mode === 'iw'  ? ' iw'  : '');
  document.getElementById('wtOOW').className = 'wt-btn' + (mode === 'oow' ? ' oow' : '');
  document.getElementById('warrantyHint').textContent = mode === 'iw'
    ? 'Covered under active contract. No quotation required.'
    : 'Out-of-warranty: Accounts will prepare a quotation before approval.';
}


const MAX_FILES = 5;
const MAX_SIZE  = 10 * 1024 * 1024;

let pickedFiles = [];   // the real source of truth

function handleFiles(input) {
  const incoming = Array.from(input.files);

  for (const f of incoming) {
    if (f.size > MAX_SIZE) {
      showToast('err', 'File Too Large', `${f.name} exceeds 10 MB.`);
      continue;
    }
    // skip exact duplicates
    if (pickedFiles.some(p => p.name === f.name && p.size === f.size && p.lastModified === f.lastModified)) {
      continue;
    }
    if (pickedFiles.length >= MAX_FILES) {
      showToast('err', 'Limit Reached', `Max ${MAX_FILES} files per inquiry.`);
      break;
    }
    pickedFiles.push(f);
  }

  syncInput();
  renderChips();

  // CRITICAL: lets the user re-pick the same file later
  input.value = '';
}

function syncInput() {
  const input = document.getElementById('inqFileInput');
  const dt = new DataTransfer();
  pickedFiles.forEach(f => dt.items.add(f));
  input.files = dt.files;
}

function removeFile(index) {
  pickedFiles.splice(index, 1);
  syncInput();
  renderChips();
}

function renderChips() {
  const chips = document.getElementById('fileChips');
  chips.innerHTML = '';

  const counter = document.getElementById('fileCounter');
  if (counter) counter.textContent = pickedFiles.length
    ? `${pickedFiles.length} of ${MAX_FILES} files attached`
    : '';

  pickedFiles.forEach((f, i) => {
    const isPdf = f.type === 'application/pdf' || /\.pdf$/i.test(f.name);

    const chip = document.createElement('div');
    chip.className = 'file-chip';
    chip.innerHTML = `
      <span class="fc-thumb ${isPdf ? 'pdf' : ''}">
        ${isPdf ? '<i class="bi bi-file-earmark-pdf-fill"></i>' : ''}
      </span>
      <span class="fc-name" title="${escHtml(f.name)}">${escHtml(f.name)}</span>
      <span class="fc-size">${fmtSize(f.size)}</span>
      <button type="button" class="fc-x" title="Remove"><i class="bi bi-x-lg"></i></button>`;

    if (!isPdf) {
      const url = URL.createObjectURL(f);
      const img = new Image();
      img.src = url;
      img.onload = () => URL.revokeObjectURL(url);
      chip.querySelector('.fc-thumb').appendChild(img);
    }

    chip.querySelector('.fc-x').addEventListener('click', () => removeFile(i));
    chips.appendChild(chip);
  });
}

function fmtSize(b) {
  if (b < 1024) return b + ' B';
  if (b < 1048576) return (b / 1024).toFixed(0) + ' KB';
  return (b / 1048576).toFixed(1) + ' MB';
}

function escHtml(s) {
  return String(s).replace(/[&<>"']/g,
    c => ({ '&':'&amp;', '<':'&lt;', '>':'&gt;', '"':'&quot;', "'":'&#39;' }[c]));
}


// Files Uploaded

document.getElementById('inqForm').addEventListener('submit', function(e) {
  const desc = document.getElementById('inqDesc').value.trim();
  if (desc.length < 20) {
    e.preventDefault();
    showToast('err','Too Short','Description must be at least 20 characters.');
    return;
  }
  const btn = document.getElementById('inqSubmitBtn');
  btn.disabled = true;
  btn.innerHTML = '<i class="bi bi-arrow-repeat"></i> Submitting…';
});

/* CLOCK */
function updateClock() {
  const el = document.getElementById('clock');
  if (el) el.textContent = new Date().toLocaleTimeString([], {hour:'2-digit', minute:'2-digit', second:'2-digit'});
}
setInterval(updateClock, 1000); updateClock();

/* TOAST */
function showToast(type, title, body) {
  const icons = {ok:'bi-check-circle-fill', err:'bi-x-circle-fill', info:'bi-info-circle-fill'};
  const t = document.createElement('div');
  t.className = 'toast-item';
  t.innerHTML = `<i class="bi ${icons[type]||icons.info} t-ico ${type}"></i><div><p class="t-title">${title}</p><p class="t-body">${body}</p></div>`;
  document.getElementById('toastWrap').appendChild(t);
  setTimeout(() => { t.style.transition='opacity .3s'; t.style.opacity='0'; setTimeout(()=>t.remove(),300); }, 3500);
}

/* Flash messages from server */
@if(session('success')) showToast('ok','Success', @js(session('success'))); @endif
@if(session('error'))   showToast('err','Error',  @js(session('error')));   @endif
</script>
@endpush
