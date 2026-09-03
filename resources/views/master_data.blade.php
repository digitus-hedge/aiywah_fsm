@extends('layouts.layout')

@section('title', 'Master Data Management — Digit-Us Portal')
@section('page_title', 'Master Data')
@section('page_icon', 'building')

@push('styles')
<style>
 


/* ── SIDEBAR ── */
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
.sb-badge{font-size:.6rem;font-weight:700;padding:1px 6px;border-radius:9px;margin-left:auto;background:rgba(217,119,6,.15);color:#d97706;}
.sb-footer{padding:14px 18px;border-top:1px solid var(--border-color);flex-shrink:0;}
.sb-user{display:flex;align-items:center;gap:10px;}
.sb-avatar{width:34px;height:34px;flex-shrink:0;background:linear-gradient(135deg,#9A7B4F,#C4A882);border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fff;font-size:.75rem;font-weight:700;}
.sb-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:299;backdrop-filter:blur(2px);}
.sb-overlay.show{display:block;}
@media(max-width:991.98px){.sidebar{transform:translateX(-100%);}.sidebar.open{transform:translateX(0);}}

/* ── TOPBAR ── */
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

/* ── MAIN ── */
.main-content{margin-left:var(--sidebar-width);margin-top:60px;padding:22px 22px 48px;min-height:calc(100vh - 60px);}
@media(max-width:991.98px){.main-content{margin-left:0;}}
@media(max-width:575.98px){.main-content{padding:14px 12px 48px;}}

/* ── PAGE HEADER ── */
.pg-header{background:linear-gradient(135deg,#9A7B4F 0%,#7A6140 100%);border-radius:10px;padding:20px 24px;margin-bottom:22px;color:#fff;position:relative;overflow:hidden;}
.pg-header::before{content:'';position:absolute;left:-40px;bottom:-40px;width:180px;height:180px;border-radius:50%;background:rgba(255,255,255,.05);}
.pg-header::after{content:'';position:absolute;right:-30px;top:-30px;width:160px;height:160px;border-radius:50%;background:rgba(255,255,255,.07);}
.pg-header h4{font-size:1rem;font-weight:600;margin:0 0 3px;position:relative;z-index:1;}
.pg-header p{font-size:.78rem;margin:0;opacity:.85;position:relative;z-index:1;}
.pg-header .meta-row{display:flex;align-items:center;gap:8px;margin-top:10px;position:relative;z-index:1;flex-wrap:wrap;}
.meta-badge{background:rgba(255,255,255,.18);border:1px solid rgba(255,255,255,.3);border-radius:20px;font-size:.6875rem;padding:2px 10px;font-weight:500;}

/* ── TAB RAIL ── */
.tab-rail{display:flex;gap:4px;background:var(--card-bg);border:1px solid var(--card-border);border-radius:10px;padding:5px;margin-bottom:20px;box-shadow:var(--card-shadow);overflow-x:auto;scrollbar-width:none;}
.tab-rail::-webkit-scrollbar{display:none;}
.trb{flex:1;min-width:110px;display:flex;flex-direction:column;align-items:center;gap:3px;padding:10px 8px;border-radius:7px;cursor:pointer;border:none;background:none;transition:background .15s,color .15s;color:var(--text-muted);}
.trb:hover{background:var(--surface-2);color:var(--text-primary);}
.trb.active{background:linear-gradient(135deg,rgba(154,123,79,.15),rgba(196,168,130,.1));color:#9A7B4F;box-shadow:0 1px 4px rgba(154,123,79,.15);}
.trb i{font-size:1.15rem;}
.trb-label{font-size:.72rem;font-weight:600;white-space:nowrap;}
.trb-count{font-size:.62rem;background:rgba(154,123,79,.15);color:#9A7B4F;padding:1px 7px;border-radius:9px;font-weight:700;}
.trb:not(.active) .trb-count{background:var(--surface-3);color:var(--text-muted);}

/* ── PANEL ── */
.master-panel{display:none;animation:panelIn .18s ease;}
.master-panel.active{display:block;}
@keyframes panelIn{from{opacity:0;transform:translateY(6px);}to{opacity:1;transform:translateY(0);}}

/* ── CARD ── */
.card{background:var(--card-bg);border:1px solid var(--card-border);border-radius:10px;box-shadow:var(--card-shadow);overflow:hidden;transition:background .3s,border-color .3s;}
.card-hdr{display:flex;align-items:center;gap:12px;padding:14px 18px;border-bottom:1px solid var(--border-color);}
.card-hdr-icon{width:34px;height:34px;border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:.95rem;}
.card-hdr h6{font-size:.875rem;font-weight:600;color:var(--text-heading);margin:0 0 1px;}
.card-hdr .csub{font-size:.72rem;color:var(--text-muted);}
.card-hdr-actions{margin-left:auto;display:flex;gap:8px;align-items:center;}

/* ── SERVICE CAT+DOMAIN MASTER-DETAIL ── */
.md-layout{display:grid;grid-template-columns:300px 1fr;gap:0;border-radius:10px;border:1px solid var(--card-border);overflow:hidden;box-shadow:var(--card-shadow);background:var(--card-bg);}
@media(max-width:899px){.md-layout{grid-template-columns:1fr;}}
.md-left{border-right:1px solid var(--border-color);display:flex;flex-direction:column;min-height:480px;}
.md-left-hdr{display:flex;align-items:center;justify-content:space-between;padding:14px 16px;border-bottom:1px solid var(--border-color);flex-shrink:0;}
.md-left-hdr-title{display:flex;align-items:center;gap:9px;}
.md-left-hdr-title i{font-size:1rem;color:#9A7B4F;}
.md-left-hdr-title span{font-size:.85rem;font-weight:600;color:var(--text-heading);}
.md-list{flex:1;overflow-y:auto;padding:6px;}
.md-list::-webkit-scrollbar{width:3px;}
.md-list::-webkit-scrollbar-thumb{background:var(--border-color);border-radius:2px;}
.cat-row{display:flex;align-items:center;gap:10px;padding:10px 12px;border-radius:8px;cursor:pointer;transition:background .12s;margin-bottom:2px;border:1.5px solid transparent;}
.cat-row:hover{background:var(--surface-2);}
.cat-row.selected{border-radius:10px;border:1px solid var(--card-border);overflow:hidden;box-shadow:var(--card-shadow);background:var(--app-bg);}
.cat-icon{width:34px;height:34px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:.95rem;flex-shrink:0;}
.cat-name{font-size:.8125rem;font-weight:500;color:var(--text-heading);flex:1;}
.cat-count-badge{font-size:.65rem;background:var(--surface-3);color:var(--text-muted);padding:1px 7px;border-radius:9px;font-weight:600;white-space:nowrap;}
.cat-row.selected .cat-count-badge{background:rgba(154,123,79,.2);color:#9A7B4F;}
.cat-actions{
  display:flex;gap:2px;
  /* opacity:0; */
  transition:opacity .12s;}
.cat-row:hover .cat-actions{}
.cat-status-dot{width:6px;height:6px;border-radius:50%;flex-shrink:0;}
.md-right{display:flex;flex-direction:column;}
.md-right-hdr{display:flex;align-items:center;justify-content:space-between;padding:14px 18px;border-bottom:1px solid var(--border-color);flex-shrink:0;gap:10px;}
.md-right-hdr-title{display:flex;align-items:center;gap:8px;}
.md-right-hdr-title .cat-label-pill{display:inline-flex;align-items:center;gap:6px;padding:4px 12px;border-radius:20px;font-size:.75rem;font-weight:600;}
.md-right-body{flex:1;padding:16px 18px;}
.domain-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:10px;}
.domain-card{background:var(--surface-2);border:1px solid var(--border-color);border-radius:8px;padding:13px 14px;display:flex;align-items:flex-start;gap:11px;transition:border-color .15s,background .15s;}
.domain-card:hover{border-color:rgba(154,123,79,.35);background:var(--surface-3);}
.domain-card-icon{width:32px;height:32px;border-radius:7px;display:flex;align-items:center;justify-content:center;font-size:.9rem;flex-shrink:0;}
.domain-card-body{flex:1;min-width:0;}
.domain-card-name{font-size:.8125rem;font-weight:600;color:var(--text-heading);margin-bottom:2px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
.domain-card-desc{font-size:.72rem;color:var(--text-muted);margin-bottom:6px;line-height:1.4;}
.domain-card-foot{display:flex;align-items:center;justify-content:space-between;}
.domain-card-actions{display:flex;gap:4px;transition:opacity .12s;}
.domain-card:hover .domain-card-actions{}

/* ── NO SELECTION PLACEHOLDER ── */
.no-sel{flex:1;display:flex;flex-direction:column;align-items:center;justify-content:center;padding:48px 24px;text-align:center;}
.no-sel-icon{width:56px;height:56px;border-radius:14px;background:var(--surface-2);display:flex;align-items:center;justify-content:center;font-size:1.5rem;color:var(--text-light);margin:0 auto 14px;}
.no-sel h6{font-size:.875rem;font-weight:500;color:var(--text-muted);margin-bottom:4px;}
.no-sel p{font-size:.78rem;color:var(--text-light);margin:0;}

/* ── SIMPLE TABLE (expense, priority) ── */
.simple-card{background:var(--card-bg);border:1px solid var(--card-border);border-radius:10px;box-shadow:var(--card-shadow);overflow:hidden;}
.simple-card-hdr{display:flex;align-items:center;gap:12px;padding:14px 18px;border-bottom:1px solid var(--border-color);}
.data-table{width:100%;border-collapse:collapse;}
.data-table thead th{padding:10px 16px;font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--text-muted);background:var(--table-header);border-bottom:1px solid var(--border-color);white-space:nowrap;}
.data-table tbody tr{border-bottom:1px solid var(--border-color);transition:background .1s;}
.data-table tbody tr:hover{background:var(--table-stripe);}
.data-table tbody tr:last-child{border-bottom:none;}
.data-table td{padding:11px 16px;font-size:.8125rem;color:var(--text-primary);vertical-align:middle;}
.data-table td.muted{color:var(--text-muted);font-size:.78rem;}
.row-actions{display:flex;gap:4px;transition:opacity .12s;}
.data-table tr:hover .row-actions{}

/* ── STATUS PILL ── */
.spill{display:inline-flex;align-items:center;gap:5px;padding:3px 10px;border-radius:20px;font-size:.7rem;font-weight:600;}
.spill-on{background:rgba(16,185,129,.1);color:#10b981;}
.spill-off{background:rgba(156,163,175,.1);color:#9ca3af;}

/* ── PRIORITY DOT ── */
.pdot{width:9px;height:9px;border-radius:50%;display:inline-block;flex-shrink:0;}

/* ── COLOUR SWATCHES ── */
.swatches{display:flex;gap:6px;flex-wrap:wrap;margin-top:5px;}
.swatch{width:24px;height:24px;border-radius:6px;cursor:pointer;border:2px solid transparent;transition:transform .12s,border-color .12s;flex-shrink:0;}
.swatch:hover{transform:scale(1.18);}
.swatch.sel{border-color:var(--text-heading);transform:scale(1.12);}

/* ── ICON PICKER ── */
.icon-grid{display:flex;gap:5px;flex-wrap:wrap;margin-top:5px;}
.ico-opt{width:30px;height:30px;border-radius:6px;display:flex;align-items:center;justify-content:center;cursor:pointer;border:1px solid var(--border-color);background:var(--surface-2);color:var(--text-muted);transition:all .12s;font-size:.9rem;}
.ico-opt:hover,.ico-opt.sel{border-color:#9A7B4F;color:#9A7B4F;background:rgba(154,123,79,.1);}

/* ── TOGGLE ── */
.tog-wrap{display:flex;align-items:center;gap:9px;cursor:pointer;user-select:none;}
.tog-track{width:38px;height:20px;border-radius:10px;background:#dde1ec;position:relative;transition:background .2s;flex-shrink:0;}
.tog-track.on{background:#9A7B4F;}
[data-bs-theme="dark"] .tog-track{background:#1e3050;}
[data-bs-theme="dark"] .tog-track.on{background:#9A7B4F;}
.tog-thumb{width:14px;height:14px;background:#fff;border-radius:50%;position:absolute;top:3px;left:3px;transition:transform .2s;box-shadow:0 1px 3px rgba(0,0,0,.2);}
.tog-track.on .tog-thumb{transform:translateX(18px);}
.tog-label{font-size:.78rem;color:var(--text-muted);}

/* ── FORM ── */
.form-label{font-size:.78rem;font-weight:500;color:var(--text-heading);margin-bottom:5px;display:block;}
.form-label .req{color:#ef4444;margin-left:2px;}
.form-label .hint{font-size:.7rem;color:var(--text-muted);font-weight:400;margin-left:6px;}
.form-control,.form-select{background:var(--input-bg);border:1px solid var(--border-color);color:var(--text-primary);border-radius:7px;font-size:.8125rem;padding:7px 11px;width:100%;transition:border-color .15s,box-shadow .15s;}
.form-control:focus,.form-select:focus{outline:none;border-color:#9A7B4F;box-shadow:var(--input-focus-shadow);}
[data-bs-theme="dark"] .form-control,[data-bs-theme="dark"] .form-select{background:var(--input-bg);color:var(--text-primary);}
textarea.form-control{resize:vertical;min-height:72px;}

/* ── BUTTONS ── */
.btn-primary-gold{display:inline-flex;align-items:center;gap:7px;padding:7px 15px;background:linear-gradient(135deg,#9A7B4F,#C4A882);color:#fff;border:none;border-radius:7px;font-size:.8rem;font-weight:500;cursor:pointer;transition:opacity .15s;white-space:nowrap;}
.btn-primary-gold:hover{opacity:.87;}
.btn-ghost{display:inline-flex;align-items:center;gap:6px;padding:7px 13px;background:var(--surface-2);color:var(--text-muted);border:1px solid var(--border-color);border-radius:7px;font-size:.8rem;cursor:pointer;transition:background .15s;white-space:nowrap;}
.btn-ghost:hover{background:var(--surface-3);}
.btn-icon-edit{background:none;border:none;color:#9A7B4F;cursor:pointer;padding:4px 7px;border-radius:5px;font-size:.82rem;transition:background .12s;line-height:1;}
.btn-icon-edit:hover{background:rgba(154,123,79,.1);}
.btn-icon-del{background:none;border:none;color:#ef4444;cursor:pointer;padding:4px 7px;border-radius:5px;font-size:.82rem;transition:background .12s;line-height:1;}
.btn-icon-del:hover{background:rgba(239,68,68,.08);}
.btn-save-sm{display:inline-flex;align-items:center;gap:6px;padding:6px 14px;background:#9A7B4F;color:#fff;border:none;border-radius:7px;font-size:.78rem;font-weight:500;cursor:pointer;}
.btn-save-sm:hover{background:#7A6140;}

/* ── SLA TABLE ── */
.sla-tbl{width:100%;border-collapse:collapse;}
.sla-tbl thead th{padding:10px 16px;font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--text-muted);background:var(--table-header);border-bottom:1px solid var(--border-color);}
.sla-tbl td{padding:12px 16px;border-bottom:1px solid var(--border-color);vertical-align:middle;font-size:.8125rem;}
.sla-tbl tr:last-child td{border-bottom:none;}
.sla-inp{width:80px;padding:5px 8px;border-radius:6px;border:1px solid var(--border-color);background:var(--input-bg);color:var(--text-primary);font-size:.8rem;text-align:center;transition:border-color .15s;}
.sla-inp:focus{outline:none;border-color:#9A7B4F;box-shadow:var(--input-focus-shadow);}

/* ── MODAL ── */
.modal-overlay{display:none;position:fixed;inset:0;background:var(--overlay-bg);z-index:900;align-items:center;justify-content:center;backdrop-filter:blur(3px);}
.modal-overlay.show{display:flex;}
.modal-box{background:var(--modal-bg);border-radius:12px;width:90%;max-width:480px;box-shadow:var(--modal-shadow);border:1px solid var(--card-border);overflow:hidden;animation:modalIn .2s ease;}
@keyframes modalIn{from{opacity:0;transform:scale(.96);}to{opacity:1;transform:scale(1);}}
.modal-hdr{display:flex;align-items:center;justify-content:space-between;padding:16px 20px;border-bottom:1px solid var(--border-color);}
.modal-hdr-left{display:flex;align-items:center;gap:10px;}
.modal-hdr-icon{width:32px;height:32px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:.9rem;}
.modal-hdr h6{font-size:.875rem;font-weight:600;color:var(--text-heading);margin:0;}
.modal-close{background:none;border:none;color:var(--text-muted);cursor:pointer;padding:4px;border-radius:5px;font-size:1rem;line-height:1;}
.modal-close:hover{background:var(--surface-2);color:var(--text-primary);}
.modal-body{padding:20px;}
.modal-foot{display:flex;align-items:center;justify-content:flex-end;gap:9px;padding:14px 20px;border-top:1px solid var(--border-color);background:var(--surface-2);}
.form-group{margin-bottom:14px;}
.form-group:last-child{margin-bottom:0;}
.field-hint{font-size:.72rem;color:var(--text-muted);margin-top:4px;}

/* ── DELETE CONFIRM ── */
.del-modal{background:var(--modal-bg);border-radius:12px;max-width:360px;width:90%;box-shadow:var(--modal-shadow);border:1px solid var(--card-border);overflow:hidden;animation:modalIn .2s ease;}
.del-modal-body{padding:28px 24px;text-align:center;}
.del-modal-icon{width:52px;height:52px;border-radius:14px;background:rgba(239,68,68,.1);display:flex;align-items:center;justify-content:center;margin:0 auto 16px;font-size:1.4rem;color:#ef4444;}
.del-modal-body h6{font-size:.95rem;font-weight:600;margin-bottom:6px;color:var(--text-heading);}
.del-modal-body p{font-size:.8rem;color:var(--text-muted);margin-bottom:0;}
.del-modal-foot{display:flex;gap:10px;padding:14px 20px;border-top:1px solid var(--border-color);}
.btn-del{flex:1;padding:8px;background:#ef4444;color:#fff;border:none;border-radius:7px;font-size:.82rem;font-weight:500;cursor:pointer;}
.btn-del:hover{background:#dc2626;}
.btn-del-cancel{flex:1;padding:8px;background:var(--surface-2);color:var(--text-muted);border:1px solid var(--border-color);border-radius:7px;font-size:.82rem;cursor:pointer;}

/* ── TOAST ── */
#toastWrap{position:fixed;bottom:22px;right:22px;z-index:9999;display:flex;flex-direction:column;gap:8px;pointer-events:none;}
.toast-item{display:flex;align-items:flex-start;gap:10px;padding:12px 15px;border-radius:9px;background:var(--card-bg);border:1px solid var(--card-border);box-shadow:0 6px 24px rgba(0,0,0,.14);min-width:250px;max-width:320px;animation:toastIn .2s ease;pointer-events:auto;}
@keyframes toastIn{from{opacity:0;transform:translateY(10px);}to{opacity:1;transform:translateY(0);}}
.t-ico{font-size:1rem;flex-shrink:0;margin-top:1px;}
.t-ico.ok{color:#10b981;}.t-ico.err{color:#ef4444;}.t-ico.info{color:#9A7B4F;}
.t-title{font-size:.8rem;font-weight:600;color:var(--text-heading);margin:0 0 2px;}
.t-body{font-size:.75rem;color:var(--text-muted);margin:0;}

/* ── INFO BANNER ── */
.info-banner{display:flex;align-items:flex-start;gap:9px;padding:10px 14px;border-radius:7px;font-size:.78rem;color:var(--text-muted);margin-bottom:14px;}
.info-banner.blue{background:rgba(37,99,235,.07);border:1px solid rgba(37,99,235,.15);}
.info-banner.green{background:rgba(37,211,102,.07);border:1px solid rgba(37,211,102,.2);}
.info-banner i{flex-shrink:0;margin-top:1px;}
.info-banner.blue i{color:#2563eb;}
.info-banner.green i{color:#25d366;}
  </style>
@endpush

@section('content')
 
  <div class="pg-header">
    <h4><i class="bi bi-table me-2"></i>Master Data Management</h4>
    <p>Configure system-wide lookup tables — service categories &amp; domains, expense categories, SR priority levels, SLA targets, and WhatsApp notification templates.</p>
    <div class="meta-row">
      <span class="meta-badge"><i class="bi bi-shield-fill-check me-1"></i>Super Admin</span>
      <span class="meta-badge"><i class="bi bi-person-gear me-1"></i>Admin</span>
    </div>
  </div>
 
  <!-- TAB RAIL -->
  <div class="tab-rail">
    <button class="trb active" onclick="switchTab('service')" data-tab="service">
      <i class="bi bi-diagram-3"></i>
      <span class="trb-label">Service Catalogue</span>
      <span class="trb-count" id="cnt-service">{{ $counts['service'] ?? 0 }}</span>
    </button>
    <button class="trb" onclick="switchTab('expense')" data-tab="expense">
      <i class="bi bi-receipt"></i>
      <span class="trb-label">Expense Categories</span>
      <span class="trb-count" id="cnt-expense">{{ $counts['expense'] ?? 0 }}</span>
    </button>


    <button class="trb" onclick="switchTab('warranty')" data-tab="warranty">
      <i class="bi bi-receipt"></i>
      <span class="trb-label">Warranty Categories</span>
      <span class="trb-count" id="cnt-warranty">{{ $counts['warranty'] ?? 0 }}</span>
    </button>


    <button class="trb" onclick="switchTab('priority')" data-tab="priority">
      <i class="bi bi-flag"></i>
      <span class="trb-label">Priority Levels</span>
      <span class="trb-count" id="cnt-priority">{{ $counts['priority'] ?? 0 }}</span>
    </button>
    <button class="trb" onclick="switchTab('sla')" data-tab="sla">
      <i class="bi bi-stopwatch"></i>
      <span class="trb-label">SLA Matrix</span>
      <span class="trb-count" id="cnt-sla">{{ $counts['sla'] ?? 0 }}</span>
    </button>

    <button class="trb" onclick="switchTab('summary-alert')" data-tab="summary-alert">
      <i class="bi bi-whatsapp"></i>
      <span class="trb-label">Summary Alerts</span>
    </button>
  </div>
 
  <!-- ════════════════════════════════
       PANEL 1: SERVICE CATALOGUE (JS-rendered from DB)
  ════════════════════════════════ -->
  <div class="master-panel active" id="panel-service">
    <div class="md-layout">
      <div class="md-left">
        <div class="md-left-hdr">
          <div class="md-left-hdr-title">
            <i class="bi bi-folder2-open"></i>
            <span>Service Categories</span>
          </div>
          @if (auth()->user()?->role?->code !== 'HP')

          <button class="btn-primary-gold" style="padding:5px 11px;font-size:.75rem;" onclick="openModal('modal-cat','add')">
            <i class="bi bi-plus-lg"></i>Add
          </button>
          @endif
        </div>
        <div class="md-list" id="cat-list"><!-- rendered by JS --></div>
      </div>
 
      <div class="md-right" id="md-right">
        <div class="no-sel" id="no-sel-state">
          <div class="no-sel-icon"><i class="bi bi-arrow-left"></i></div>
          <h6>Select a Category</h6>
          <p>Click any service category on the left to view and manage its domains.</p>
        </div>
        <div id="domain-panel" style="display:none;flex-direction:column;flex:1;">
          <div class="md-right-hdr">
            <div class="md-right-hdr-title">
              <span style="font-size:.8rem;color:var(--text-muted);">Domains in</span>
              <div id="selected-cat-pill" class="cat-label-pill"></div>
            </div>
            @if (auth()->user()?->role?->code !== 'HP')

            <button class="btn-primary-gold" style="padding:5px 13px;font-size:.75rem;" onclick="openModal('modal-domain','add')">
              <i class="bi bi-plus-lg"></i>Add Domain
            </button>
            @endif
          </div>
          <div class="md-right-body">
            <div class="domain-grid" id="domain-grid"><!-- rendered by JS --></div>
            <div id="domain-empty" class="no-sel" style="display:none;">
              <div class="no-sel-icon"><i class="bi bi-tools"></i></div>
              <h6>No Domains Yet</h6>
              <p>Click "Add Domain" to create the first domain under this category.</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
 
  <!-- ════════════════════════════════
       PANEL 2: EXPENSE CATEGORIES (dynamic list)
  ════════════════════════════════ -->
  <div class="master-panel" id="panel-expense">
    <div class="simple-card">
      <div class="simple-card-hdr">
        <div class="card-hdr-icon" style="background:rgba(236,72,153,.1);"><i class="bi bi-receipt" style="color:#ec4899;"></i></div>
        <div>
          <h6 style="font-size:.875rem;font-weight:600;color:var(--text-heading);margin:0 0 1px;">Expense Categories</h6>
          <div class="csub" style="font-size:.72rem;color:var(--text-muted);">Options available to Maintenance Leads on the Field Expenditure module</div>
        </div>
        <div class="card-hdr-actions">
          @if (auth()->user()?->role?->code !== 'HP')

          <button class="btn-primary-gold" onclick="openModal('modal-expense','add')"><i class="bi bi-plus-lg"></i>Add Category</button>
         @endif
        </div>
      </div>
      <div style="overflow-x:auto;">
        <table class="data-table">
          <thead>
            <tr>
              <th style="width:40px;">#</th>
              <th>Category Name</th>
              <th>Description</th>
              <th>Status</th>
              <!-- <th style="width:110px;text-align:center;">Actions</th> -->
                @if (auth()->user()?->role?->code !== 'HP')
      <th style="width:110px;text-align:center;">Actions</th>
      @endif
            </tr>
          </thead>
          <tbody id="tbody-expense">
            @forelse($expenseCategories as $exp)
              <tr>
                <td class="muted">{{ $loop->iteration }}</td>
                <td><strong>{{ $exp->name }}</strong></td>
                <td class="muted">{{ $exp->description ?: '—' }}</td>
                <td>
                  <span class="spill {{ $exp->status ? 'spill-on' : 'spill-off' }}" id="exp-status-{{ $exp->id }}">
                    <i class="bi bi-circle-fill" style="font-size:.4rem;"></i>{{ $exp->status ? 'Active' : 'Inactive' }}
                  </span>
                </td>

                        @if (auth()->user()?->role?->code !== 'HP')

                <td style="text-align:center;">
                  <div class="row-actions" style="justify-content:center;">
                    <!-- <button class="btn-icon-status" title="Toggle status"
                      onclick="toggleStatus('expense',{{ $exp->id }})"><i class="bi bi-toggle-on"></i></button> -->
                    <button class="btn-icon-edit" title="Edit"
                      data-id="{{ $exp->id }}"
                      data-name="{{ $exp->name }}"
                      data-desc="{{ $exp->description }}"
                      data-active="{{ $exp->status ? 1 : 0 }}"
                      onclick="editExpenseBtn(this)"><i class="bi bi-pencil"></i></button>
                    <button class="btn-icon-del" title="Delete"
                      onclick="confirmDel('expense',{{ $exp->id }},'{{ addslashes($exp->name) }}')"><i class="bi bi-trash3"></i></button>
                  </div>
                </td>

                        @endif

              </tr>
            @empty
              <tr><td colspan="5" class="muted" style="text-align:center;padding:20px;">No expense categories yet.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>


<!-- Warranties Categories -->
  <div class="master-panel" id="panel-warranty">
    <div class="simple-card">
      <div class="simple-card-hdr">
        <div class="card-hdr-icon" style="background:rgba(236,72,153,.1);"><i class="bi bi-receipt" style="color:#ec4899;"></i></div>
        <div>
          <h6 style="font-size:.875rem;font-weight:600;color:var(--text-heading);margin:0 0 1px;">Warranty Categories</h6>
          <div class="csub" style="font-size:.72rem;color:var(--text-muted);">Options available to Maintenance Leads on the Field Expenditure module</div>
        </div>
        <div class="card-hdr-actions">
                @if (auth()->user()?->role?->code !== 'HP')
          <button class="btn-primary-gold" onclick="openModal('modal-warranty','add')"><i class="bi bi-plus-lg"></i>Add Warranty</button>
          @endif
        </div>
      </div>
      <div style="overflow-x:auto;">
        <table class="data-table">
          <thead>
            <tr>
              <th style="width:40px;">#</th>
              <th>Warranty Name</th>
              <th>Value</th>
              <th>Status</th>
               @if (auth()->user()?->role?->code !== 'HP')
              <th style="width:110px;text-align:center;">Actions</th>
              @endif

            </tr>
          </thead>
          <tbody id="tbody-warranty">
            @forelse($warranties as $war)
              <tr>
                <td class="muted">{{ $loop->iteration }}</td>
                <td><strong>{{ $war->name }}</strong></td>
                <td class="muted">{{ $war->value ?: '—' }}</td>
                <td>
                  <span class="spill {{ $war->status ? 'spill-on' : 'spill-off' }}" id="exp-status-{{ $war->id }}">
                    <i class="bi bi-circle-fill" style="font-size:.4rem;"></i>{{ $war->status ? 'Active' : 'Inactive' }}
                  </span>
                </td>

                @if (auth()->user()?->role?->code !== 'HP')

                <td style="text-align:center;">
                  <div class="row-actions" style="justify-content:center;">
                    <!-- <button class="btn-icon-status" title="Toggle status"
                      onclick="toggleStatus('warranty',{{ $war->id }})"><i class="bi bi-toggle-on"></i></button> -->
                    <button class="btn-icon-edit" title="Edit"
                      data-id="{{ $war->id }}"
                      data-name="{{ $war->name }}"
                      data-value="{{ $war->value }}"
                      data-active="{{ $war->status ? 1 : 0 }}"
                      onclick="editWarrantyBtn(this)"><i class="bi bi-pencil"></i></button>
                    <button class="btn-icon-del" title="Delete"
                      onclick="confirmDel('warranty',{{ $war->id }},'{{ addslashes($war->name) }}')"><i class="bi bi-trash3"></i></button>
                  </div>
                </td>
                @endif

              </tr>
            @empty
              <tr><td colspan="5" class="muted" style="text-align:center;padding:20px;">No warranties categories yet.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

 
  <!-- ════════════════════════════════
       PANEL 3: PRIORITY LEVELS (dynamic list)
  ════════════════════════════════ -->
  <div class="master-panel" id="panel-priority">
    <div class="simple-card">
      <div class="simple-card-hdr">
        <div class="card-hdr-icon" style="background:rgba(239,68,68,.1);"><i class="bi bi-flag" style="color:#ef4444;"></i></div>
        <div>
          <h6 style="font-size:.875rem;font-weight:600;color:var(--text-heading);margin:0 0 1px;">SR Priority Levels</h6>
          <div class="csub" style="font-size:.72rem;color:var(--text-muted);">Drives SLA row colouring on SR Explorer and unattended pipeline alerts</div>
        </div>
        <div class="card-hdr-actions">
   @if (auth()->user()?->role?->code !== 'HP')
          <button class="btn-primary-gold" onclick="openModal('modal-priority','add')"><i class="bi bi-plus-lg"></i>Add Priority</button>
        @endif
        </div>
      </div>
      <div style="overflow-x:auto;">
        <table class="data-table">
          <thead>
            <tr>
              <th style="width:60px;">Order</th>
              <th>Priority Level</th>
              <th>Colour</th>
              <th>Status</th>
              @if (auth()->user()?->role?->code !== 'HP')
              <th style="width:110px;text-align:center;">Actions</th>
              @endif
            </tr>
          </thead>
          <tbody id="tbody-priority">
            @forelse($priorities as $p)
              <tr>
                <td><span style="display:inline-flex;align-items:center;justify-content:center;width:26px;height:26px;border-radius:6px;background:var(--surface-2);font-size:.78rem;font-weight:700;color:var(--text-muted);">{{ $p->display_order }}</span></td>
                <td><span style="display:inline-flex;align-items:center;gap:7px;"><span class="pdot" style="background:{{ $p->color }};"></span><strong>{{ $p->name }}</strong></span></td>
                <td><span style="display:inline-flex;align-items:center;gap:6px;"><span style="width:18px;height:18px;border-radius:5px;background:{{ $p->color }};display:inline-block;"></span><span class="muted" style="font-size:.75rem;">{{ $p->color }}</span></span></td>
                <td>
                  <span class="spill {{ $p->status ? 'spill-on' : 'spill-off' }}" id="pri-status-{{ $p->id }}">
                    <i class="bi bi-circle-fill" style="font-size:.4rem;"></i>{{ $p->status ? 'Active' : 'Inactive' }}
                  </span>
                </td>

                                @if (auth()->user()?->role?->code !== 'HP')

                <td style="text-align:center;">
                  <div class="row-actions" style="justify-content:center;">
                    <!-- <button class="btn-icon-status" title="Toggle status"
                      onclick="toggleStatus('priority',{{ $p->id }})"><i class="bi bi-toggle-on"></i></button> -->
                    <button class="btn-icon-edit" title="Edit"
                      data-id="{{ $p->id }}"
                      data-name="{{ $p->name }}"
                      data-color="{{ $p->color }}"
                      data-order="{{ $p->display_order }}"
                      data-active="{{ $p->status ? 1 : 0 }}"
                      onclick="editPriorityBtn(this)"><i class="bi bi-pencil"></i></button>
                    <button class="btn-icon-del" title="Delete"
                      onclick="confirmDel('priority',{{$p->id }},'{{ addslashes($p->name) }}')"><i class="bi bi-trash3"></i></button>
                  </div>
                </td>
                @endif
              </tr>
            @empty
              <tr><td colspan="5" class="muted" style="text-align:center;padding:20px;">No priority levels yet.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
 
  <!-- ════════════════════════════════
       PANEL 4: SLA MATRIX (dynamic, one row per priority)
  ════════════════════════════════ -->
  <div class="master-panel" id="panel-sla">
  <div class="simple-card">
    <div class="simple-card-hdr">
      <div class="card-hdr-icon" style="background:rgba(8,145,178,.1);"><i class="bi bi-stopwatch" style="color:#0891b2;"></i></div>
      <div>
        <h6 style="font-size:.875rem;font-weight:600;color:var(--text-heading);margin:0 0 1px;">SLA Duration Matrix</h6>
        <div class="csub" style="font-size:.72rem;color:var(--text-muted);">Approve, dispatch and QC targets per criticality. All values in <strong>hours</strong>.</div>
      </div>
      <div class="card-hdr-actions">
          @if (auth()->user()?->role?->code !== 'HP')
        
        <button class="btn-primary-gold" onclick="saveSLA()"><i class="bi bi-floppy"></i>Save Changes</button>
          @endif
      </div>
    </div>

    <div style="padding:14px 18px 4px;">
      <div class="info-banner blue">
        <i class="bi bi-info-circle"></i>
        <span>Each stage clock starts when the previous one closes — <strong>Approve</strong> from SR creation, <strong>Dispatch</strong> from approval, <strong>QC</strong> from job completion. Values are whole hours (1–8760).</span>
      </div>
    </div>

    <div style="overflow-x:auto;">
      <table class="sla-tbl">
        <thead>
          <tr>
            <th>Criticality</th>
            <th>Approve (hrs)</th>
            <th>Dispatch (hrs)</th>
            <th>QC (hrs)</th>
            <th>Row Colour</th>
          </tr>
        </thead>
        <tbody>
          @forelse($priorities as $p)
            @php $row = $slaMatrix->firstWhere('priority_id', $p->id); @endphp
            <tr data-priority-id="{{ $p->id }}">
              <td><span style="display:inline-flex;align-items:center;gap:7px;"><span class="pdot" style="background:{{ $p->color }};"></span><strong>{{ $p->name }}</strong></span></td>
              <td><input class="sla-inp" type="number" min="1" max="8760" step="1" data-field="response_time"   value="{{ optional($row)->response_time   ?? 4 }}"/></td>
              <td><input class="sla-inp" type="number" min="1" max="8760" step="1" data-field="assignment_time" value="{{ optional($row)->assignment_time ?? 8 }}"/></td>
              <td><input class="sla-inp" type="number" min="1" max="8760" step="1" data-field="resolution_time" value="{{ optional($row)->resolution_time ?? 24 }}"/></td>
              <td><span style="display:inline-block;width:22px;height:22px;border-radius:6px;background:{{ $p->color }};"></span></td>
            </tr>
          @empty
            <tr><td colspan="5" class="muted" style="text-align:center;padding:20px;">Add criticality levels first to configure SLA targets.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div style="padding:12px 18px;border-top:1px solid var(--border-color);display:flex;align-items:center;gap:8px;">
      <i class="bi bi-lightbulb" style="color:#9A7B4F;font-size:.85rem;"></i>
      <span style="font-size:.72rem;color:var(--text-muted);">24 hrs = 1 day · 72 hrs = 3 days · 168 hrs = 1 week</span>
    </div>
  </div>
</div>
 
 @include('summary_alert')
<!-- ════ MODAL: SERVICE CATEGORY ════ -->
<div class="modal-overlay" id="modal-cat" onclick="handleOverlayClick(event,'modal-cat')">
  <div class="modal-box">
    <div class="modal-hdr">
      <div class="modal-hdr-left">
        <div class="modal-hdr-icon" style="background:rgba(154,123,79,.1);"><i class="bi bi-folder-plus" style="color:#9A7B4F;"></i></div>
        <h6 id="modal-cat-title">Add Service Category</h6>
      </div>
      <button class="modal-close" onclick="closeModal('modal-cat')"><i class="bi bi-x-lg"></i></button>
    </div>
    <div class="modal-body">
      <div class="form-group">
        <label class="form-label">Category Name <span class="req">*</span></label>
        <input type="text" class="form-control" id="cat-name" placeholder="e.g. Electrical Systems"/>
        <div class="field-hint">This name will be shown as a grouping header above its domains.</div>
      </div>
      <div class="form-group">
        <label class="form-label">Description <span class="hint">(optional)</span></label>
        <input type="text" class="form-control" id="cat-desc" placeholder="Brief description of the service category"/>
      </div>
      <div class="form-group">
        <label class="form-label">Colour Tag</label>
        <div class="swatches" id="cat-swatches">
          <div class="swatch sel" style="background:#3b82f6;" data-c="#3b82f6" onclick="pickSwatch('cat-swatches',this)"></div>
          <div class="swatch" style="background:#10b981;" data-c="#10b981" onclick="pickSwatch('cat-swatches',this)"></div>
          <div class="swatch" style="background:#f59e0b;" data-c="#f59e0b" onclick="pickSwatch('cat-swatches',this)"></div>
          <div class="swatch" style="background:#ef4444;" data-c="#ef4444" onclick="pickSwatch('cat-swatches',this)"></div>
          <div class="swatch" style="background:#8b5cf6;" data-c="#8b5cf6" onclick="pickSwatch('cat-swatches',this)"></div>
          <div class="swatch" style="background:#ec4899;" data-c="#ec4899" onclick="pickSwatch('cat-swatches',this)"></div>
          <div class="swatch" style="background:#06b6d4;" data-c="#06b6d4" onclick="pickSwatch('cat-swatches',this)"></div>
          <div class="swatch" style="background:#9A7B4F;" data-c="#9A7B4F" onclick="pickSwatch('cat-swatches',this)"></div>
          <div class="swatch" style="background:#6366f1;" data-c="#6366f1" onclick="pickSwatch('cat-swatches',this)"></div>
          <div class="swatch" style="background:#64748b;" data-c="#64748b" onclick="pickSwatch('cat-swatches',this)"></div>
        </div>
      </div>
      <div class="form-group">
        <label class="form-label">Icon</label>
        <div class="icon-grid" id="cat-icons">
          <div class="ico-opt sel" data-i="bi-plug" onclick="pickIcon('cat-icons',this)"><i class="bi bi-plug"></i></div>
          <div class="ico-opt" data-i="bi-lightning-charge" onclick="pickIcon('cat-icons',this)"><i class="bi bi-lightning-charge"></i></div>
          <div class="ico-opt" data-i="bi-wrench-adjustable" onclick="pickIcon('cat-icons',this)"><i class="bi bi-wrench-adjustable"></i></div>
          <div class="ico-opt" data-i="bi-droplet" onclick="pickIcon('cat-icons',this)"><i class="bi bi-droplet"></i></div>
          <div class="ico-opt" data-i="bi-wind" onclick="pickIcon('cat-icons',this)"><i class="bi bi-wind"></i></div>
          <div class="ico-opt" data-i="bi-house" onclick="pickIcon('cat-icons',this)"><i class="bi bi-house"></i></div>
          <div class="ico-opt" data-i="bi-fire" onclick="pickIcon('cat-icons',this)"><i class="bi bi-fire"></i></div>
          <div class="ico-opt" data-i="bi-gear" onclick="pickIcon('cat-icons',this)"><i class="bi bi-gear"></i></div>
          <div class="ico-opt" data-i="bi-thermometer" onclick="pickIcon('cat-icons',this)"><i class="bi bi-thermometer"></i></div>
          <div class="ico-opt" data-i="bi-camera-video" onclick="pickIcon('cat-icons',this)"><i class="bi bi-camera-video"></i></div>
          <div class="ico-opt" data-i="bi-wifi" onclick="pickIcon('cat-icons',this)"><i class="bi bi-wifi"></i></div>
          <div class="ico-opt" data-i="bi-building" onclick="pickIcon('cat-icons',this)"><i class="bi bi-building"></i></div>
          <div class="ico-opt" data-i="bi-cpu" onclick="pickIcon('cat-icons',this)"><i class="bi bi-cpu"></i></div>
          <div class="ico-opt" data-i="bi-shield-check" onclick="pickIcon('cat-icons',this)"><i class="bi bi-shield-check"></i></div>
          <div class="ico-opt" data-i="bi-tools" onclick="pickIcon('cat-icons',this)"><i class="bi bi-tools"></i></div>
          <div class="ico-opt" data-i="bi-layers" onclick="pickIcon('cat-icons',this)"><i class="bi bi-layers"></i></div>
        </div>
      </div>
      <div class="form-group" style="margin-bottom:0;">
        <div class="tog-wrap" onclick="toggleTog('cat-tog-track',this)">
          <div class="tog-track on" id="cat-tog-track"><div class="tog-thumb"></div></div>
          <span class="tog-label">Active — visible to Dispatch Engine and User Provisioning</span>
        </div>
      </div>
    </div>
    <div class="modal-foot">
      <button class="btn-ghost" onclick="closeModal('modal-cat')">Cancel</button>
      <button class="btn-primary-gold" onclick="saveCategory()"><i class="bi bi-floppy"></i>Save Category</button>
    </div>
  </div>
</div>
 
<!-- ════ MODAL: SERVICE DOMAIN ════ -->
<div class="modal-overlay" id="modal-domain" onclick="handleOverlayClick(event,'modal-domain')">
  <div class="modal-box">
    <div class="modal-hdr">
      <div class="modal-hdr-left">
        <div class="modal-hdr-icon" style="background:rgba(37,99,235,.1);"><i class="bi bi-tools" style="color:#3b82f6;"></i></div>
        <h6 id="modal-domain-title">Add Service Domain</h6>
      </div>
      <button class="modal-close" onclick="closeModal('modal-domain')"><i class="bi bi-x-lg"></i></button>
    </div>
    <div class="modal-body">
      <div class="info-banner blue" style="margin-bottom:14px;">
        <i class="bi bi-folder2"></i>
        <span>Adding under category: <strong id="domain-cat-label">—</strong></span>
      </div>
      <div class="form-group">
        <label class="form-label">Domain Name <span class="req">*</span></label>
        <input type="text" class="form-control" id="dom-name" placeholder="e.g. Low Voltage Wiring"/>
      </div>
      <div class="form-group">
        <label class="form-label">Description <span class="hint">(optional)</span></label>
        <input type="text" class="form-control" id="dom-desc" placeholder="Short description of this domain"/>
      </div>
      <div class="form-group">
        <div class="tog-wrap" onclick="toggleTog('dom-tog-track',this)">
          <div class="tog-track on" id="dom-tog-track"><div class="tog-thumb"></div></div>
          <span class="tog-label">Active</span>
        </div>
      </div>
    </div>
    <div class="modal-foot">
      <button class="btn-ghost" onclick="closeModal('modal-domain')">Cancel</button>
      <button class="btn-primary-gold" onclick="saveDomain()"><i class="bi bi-floppy"></i>Save Domain</button>
    </div>
  </div>
</div>
 
<!-- ════ MODAL: EXPENSE CATEGORY ════ -->
<div class="modal-overlay" id="modal-expense" onclick="handleOverlayClick(event,'modal-expense')">
  <div class="modal-box" style="max-width:420px;">
    <div class="modal-hdr">
      <div class="modal-hdr-left">
        <div class="modal-hdr-icon" style="background:rgba(236,72,153,.1);"><i class="bi bi-receipt" style="color:#ec4899;"></i></div>
        <h6 id="modal-expense-title">Add Expense Category</h6>
      </div>
      <button class="modal-close" onclick="closeModal('modal-expense')"><i class="bi bi-x-lg"></i></button>
    </div>
    <div class="modal-body">
      <div class="form-group">
        <label class="form-label">Category Name <span class="req">*</span></label>
        <input type="text" class="form-control" id="exp-name" placeholder="e.g. Spare Parts"/>
      </div>
      <div class="form-group">
        <label class="form-label">Description</label>
        <input type="text" class="form-control" id="exp-desc" placeholder="Brief description"/>
      </div>
      <div class="form-group" style="margin-bottom:0;">
        <div class="tog-wrap" onclick="toggleTog('exp-tog-track',this)">
          <div class="tog-track on" id="exp-tog-track"><div class="tog-thumb"></div></div>
          <span class="tog-label">Active</span>
        </div>
      </div>
    </div>
    <div class="modal-foot">
      <button class="btn-ghost" onclick="closeModal('modal-expense')">Cancel</button>
      <button class="btn-primary-gold" onclick="saveExpense()"><i class="bi bi-floppy"></i>Save</button>
    </div>
  </div>
</div>


<!-- ════ MODAL: WARRANTY CATEGORIES ════ -->
<div class="modal-overlay" id="modal-warranty" onclick="handleOverlayClick(event,'modal-warranty')">
  <div class="modal-box" style="max-width:420px;">
    <div class="modal-hdr">
      <div class="modal-hdr-left">
        <div class="modal-hdr-icon" style="background:rgba(236,72,153,.1);"><i class="bi bi-receipt" style="color:#ec4899;"></i></div>
        <h6 id="modal-warranty-title">Add Warranty Category</h6>
      </div>
      <button class="modal-close" onclick="closeModal('modal-warranty')"><i class="bi bi-x-lg"></i></button>
    </div>
    <div class="modal-body">
      <div class="form-group">
        <label class="form-label">Warranty Name <span class="req">*</span></label>
        <input type="text" class="form-control" id="warranty-name" placeholder="Enter warranty name"/>
      </div>
      <div class="form-group">
  <label class="form-label">Value <span class="req">*</span></label>
  <input type="text" class="form-control" id="warranty-value"
         inputmode="numeric" placeholder="Warranty Value (days)"
         oninput="this.value=this.value.replace(/[^0-9]/g,'')"/>
</div>
      <div class="form-group" style="margin-bottom:0;">
        <div class="tog-wrap" onclick="toggleTog('warranty-tog-track',this)">
          <div class="tog-track on" id="warranty-tog-track"><div class="tog-thumb"></div></div>
          <span class="tog-label">Active</span>
        </div>
      </div>
    </div>
    <div class="modal-foot">
      <button class="btn-ghost" onclick="closeModal('modal-warranty')">Cancel</button>
      <button class="btn-primary-gold" onclick="saveWarranty()"><i class="bi bi-floppy"></i>Save</button>
    </div>
  </div>
</div>

 
<!-- ════ MODAL: PRIORITY ════ -->
<div class="modal-overlay" id="modal-priority" onclick="handleOverlayClick(event,'modal-priority')">
  <div class="modal-box" style="max-width:440px;">
    <div class="modal-hdr">
      <div class="modal-hdr-left">
        <div class="modal-hdr-icon" style="background:rgba(239,68,68,.1);"><i class="bi bi-flag" style="color:#ef4444;"></i></div>
        <h6 id="modal-priority-title">Add Priority Level</h6>
      </div>
      <button class="modal-close" onclick="closeModal('modal-priority')"><i class="bi bi-x-lg"></i></button>
    </div>
    <div class="modal-body">
      <div class="row g-3">
        <div class="col-8">
          <div class="form-group" style="margin-bottom:0;">
            <label class="form-label">Priority Name <span class="req">*</span></label>
            <input type="text" class="form-control" id="pri-name" placeholder="e.g. Critical"/>
          </div>
        </div>
        <div class="col-4">
          <div class="form-group" style="margin-bottom:0;">
            <label class="form-label">Order <span class="req">*</span></label>
            <input type="number" class="form-control" id="pri-order" placeholder="1" min="1"/>
          </div>
        </div>
      </div>
      <div class="form-group" style="margin-top:14px;">
        <label class="form-label">Colour Tag</label>
        <div class="swatches" id="pri-swatches">
          <div class="swatch" style="background:#ef4444;" data-c="#ef4444" onclick="pickSwatch('pri-swatches',this)"></div>
          <div class="swatch" style="background:#f97316;" data-c="#f97316" onclick="pickSwatch('pri-swatches',this)"></div>
          <div class="swatch sel" style="background:#f59e0b;" data-c="#f59e0b" onclick="pickSwatch('pri-swatches',this)"></div>
          <div class="swatch" style="background:#3b82f6;" data-c="#3b82f6" onclick="pickSwatch('pri-swatches',this)"></div>
          <div class="swatch" style="background:#10b981;" data-c="#10b981" onclick="pickSwatch('pri-swatches',this)"></div>
          <div class="swatch" style="background:#6b7280;" data-c="#6b7280" onclick="pickSwatch('pri-swatches',this)"></div>
        </div>
      </div>
      <div class="form-group" style="margin-bottom:0;">
        <div class="tog-wrap" onclick="toggleTog('pri-tog-track',this)">
          <div class="tog-track on" id="pri-tog-track"><div class="tog-thumb"></div></div>
          <span class="tog-label">Active</span>
        </div>
      </div>
    </div>
    <div class="modal-foot">
      <button class="btn-ghost" onclick="closeModal('modal-priority')">Cancel</button>
      <button class="btn-primary-gold" onclick="savePriority()"><i class="bi bi-floppy"></i>Save Priority</button>
    </div>
  </div>
</div>
 
<!-- ════ DELETE CONFIRM ════ -->
<div class="modal-overlay" id="modal-del" onclick="handleOverlayClick(event,'modal-del')">
  <div class="del-modal">
    <div class="del-modal-body">
      <div class="del-modal-icon"><i class="bi bi-trash3"></i></div>
      <h6>Delete Entry</h6>
      <p id="del-msg">Are you sure you want to delete this entry?</p>
    </div>
    <div class="del-modal-foot">
      <button class="btn-del-cancel" onclick="closeModal('modal-del')">Cancel</button>
      <button class="btn-del" onclick="execDel()">Yes, Delete</button>
    </div>
  </div>
</div>
 
<!-- TOAST -->
<div id="toastWrap"></div>
@endsection
 
 
@php
    $catsSeed = $categories->map(fn ($c) => [
        'id'      => $c->id,
        'name'    => $c->category_name,
        'desc'    => $c->description,
        'color'   => $c->color_code ?: '#9A7B4F',
        'icon'    => $c->icon ?: 'bi-tools',
        'active'  => (bool) $c->status,
        'domains' => $c->domains->map(fn ($d) => [
            'id'     => $d->id,
            'name'   => $d->domain_name,
            'desc'   => $d->description,
            'active' => (bool) $d->status,
        ])->values(),
    ])->values();
@endphp
 
@push('scripts')
<script>
/* ─── LARAVEL GLUE ─── */
const CSRF = "{{ csrf_token() }}";
window.M_ROUTES = {
  catStore:  "{{ route('masters.service-category.store') }}",
  catUpdate: (id) => `{{ url('masters/service-category/update') }}/${id}`,
  catDelete: (id) => `{{ url('masters/service-category/delete') }}/${id}`,
  catStatus: (id) => `{{ url('masters/service-category/status') }}/${id}`,
 
  domStore:  "{{ route('masters.service-domain.store') }}",
  domUpdate: (id) => `{{ url('masters/service-domain/update') }}/${id}`,
  domDelete: (id) => `{{ url('masters/service-domain/delete') }}/${id}`,
  domStatus: (id) => `{{ url('masters/service-domain/status') }}/${id}`,
 
  expStore:  "{{ route('masters.expense-category.store') }}",
  expUpdate: (id) => `{{ url('masters/expense-category/update') }}/${id}`,
  expDelete: (id) => `{{ url('masters/expense-category/delete') }}/${id}`,
  expStatus: (id) => `{{ url('masters/expense-category/status') }}/${id}`,

  warrantyStore:  "{{ route('masters.warranty-category.store') }}",
  warrantyUpdate: (id) => `{{ url('masters/warranty-category/update') }}/${id}`,
  warrantyDelete: (id) => `{{ url('masters/warranty-category/delete') }}/${id}`,
  warrantyStatus: (id) => `{{ url('masters/warranty-category/status') }}/${id}`,
 

 
  priStore:  "{{ route('masters.priority.store') }}",
  priUpdate: (id) => `{{ url('masters/priority/update') }}/${id}`,
  priDelete: (id) => `{{ url('masters/priority/delete') }}/${id}`,
  priStatus: (id) => `{{ url('masters/priority/status') }}/${id}`,
 
  slaSaveAll: "{{ route('masters.sla-matrix.save-all') }}",
};
 
/* ─── DATA (from DB) ─── */
const CATS      = @json($catsSeed);

const USER_ROLE = "{{ auth()->user()?->role?->code }}";
const CAN_MANAGE_CATS = USER_ROLE !== 'HP';   // adjust roles as needed
 
let selectedCatId = null;


/* edit-mode trackers: null = create, otherwise the id being edited */
let editMode = { cat:null, domain:null, expense:null, priority:null };
 
/* ─── AJAX HELPER ─── */
async function api(url, method, payload) {
  const opts = { method, headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' } };
  if (payload !== undefined) {
    opts.headers['Content-Type'] = 'application/json';
    opts.body = JSON.stringify(payload);
  }
  const res = await fetch(url, opts);
  let data = {};
  try { data = await res.json(); } catch (e) {}
  if (!res.ok || data.status === false) {
    const msg = data.message || (data.errors ? Object.values(data.errors).flat().join(' ') : 'Request failed.');
    throw new Error(msg);
  }
  return data;
}
function esc(s){ return String(s ?? '').replace(/'/g, "\\'"); }
 
/* ─── RENDER CATS ─── */
function renderCats(){
  const list = document.getElementById('cat-list');
  list.innerHTML = CATS.map(c=>`
    <div class="cat-row${selectedCatId===c.id?' selected':''}" onclick="selectCat(${c.id})" id="cat-row-${c.id}">
      <div class="cat-icon" style="background:${c.color}18;"><i class="bi ${c.icon}" style="color:${c.color};"></i></div>
      <div style="flex:1;min-width:0;">
        <div class="cat-name">${c.name}</div>
        <div style="font-size:.7rem;color:var(--text-muted);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">${c.desc||'—'}</div>
      </div>
      <span class="cat-count-badge">${c.domains.length}</span>
      <span class="cat-status-dot" style="background:${c.active?'#10b981':'#9ca3af'};"></span>
      ${CAN_MANAGE_CATS ? `
      <div class="cat-actions">
        <button class="btn-icon-edit" onclick="event.stopPropagation();editCat(${c.id})" title="Edit"><i class="bi bi-pencil"></i></button>
        <button class="btn-icon-del" onclick="event.stopPropagation();confirmDel('cat',${c.id},'${esc(c.name)}')" title="Delete"><i class="bi bi-trash3"></i></button>
      </div>` : ''}
    </div>
  `).join('');
  const el = document.getElementById('cnt-service'); if(el) el.textContent = CATS.length;
}
 
/* ─── SELECT CATEGORY ─── */
function selectCat(id){
  selectedCatId = id;
  renderCats();
  const cat = CATS.find(c=>c.id===id);
  document.getElementById('no-sel-state').style.display='none';
  const dp = document.getElementById('domain-panel');
  dp.style.display='flex';
  const pill = document.getElementById('selected-cat-pill');
  pill.style.background = cat.color+'18';
  pill.style.color = cat.color;
  pill.innerHTML = `<i class="bi ${cat.icon}"></i>${cat.name}`;
  document.getElementById('domain-cat-label').textContent = cat.name;
  renderDomains(cat);
}
 
/* ─── RENDER DOMAINS ─── */
function renderDomains(cat){
  const grid = document.getElementById('domain-grid');
  const empty = document.getElementById('domain-empty');
  if(!cat.domains.length){grid.innerHTML='';empty.style.display='flex';return;}
  empty.style.display='none';
  grid.innerHTML = cat.domains.map(d=>`
    <div class="domain-card">
      <div class="domain-card-icon" style="background:${cat.color}18;"><i class="bi bi-tools" style="color:${cat.color};font-size:.8rem;"></i></div>
      <div class="domain-card-body">
        <div class="domain-card-name">${d.name}</div>
        <div class="domain-card-desc">${d.desc||'—'}</div>
        <div class="domain-card-foot">
          <span class="spill ${d.active?'spill-on':'spill-off'}" style="font-size:.65rem;padding:2px 8px;">
            <i class="bi bi-circle-fill" style="font-size:.35rem;"></i>${d.active?'Active':'Inactive'}
          </span>
          ${CAN_MANAGE_CATS ? `
          <div class="domain-card-actions">
            <button class="btn-icon-edit" title="Edit" onclick="editDomain(${d.id})"><i class="bi bi-pencil"></i></button>
            <button class="btn-icon-del" title="Delete" onclick="confirmDel('domain',${d.id},'${esc(d.name)}')"><i class="bi bi-trash3"></i></button>
          </div>` : ''}
        </div>
      </div>
    </div>
  `).join('');
}
 
/* ─── MODALS ─── */
function openModal(id,mode,data){
  document.getElementById(id).classList.add('show');
 
  if(id==='modal-domain'){
    editMode.domain = (mode==='edit' && data) ? data.id : null;
    const cat=CATS.find(c=>c.id===selectedCatId);
    document.getElementById('domain-cat-label').textContent=cat?cat.name:'—';
    document.getElementById('modal-domain-title').textContent = mode==='edit'?'Edit Service Domain':'Add Service Domain';
    document.getElementById('dom-name').value = data?.name || '';
    document.getElementById('dom-desc').value = data?.desc || '';
    setTog('dom-tog-track', data ? !!data.active : true);
  }
 
  if(id==='modal-cat'){
    editMode.cat = (mode==='edit' && data) ? data.id : null;
    document.getElementById('modal-cat-title').textContent = mode==='edit'?'Edit Service Category':'Add Service Category';
    document.getElementById('cat-name').value = data?.name || '';
    document.getElementById('cat-desc').value = data?.desc || '';
    setTog('cat-tog-track', data ? !!data.active : true);
    // preselect colour + icon
    const sw = data?.color ? document.querySelector(`#cat-swatches .swatch[data-c="${data.color}"]`) : document.querySelector('#cat-swatches .swatch');
    pickSwatch('cat-swatches', sw || document.querySelector('#cat-swatches .swatch'));
    const ic = data?.icon ? document.querySelector(`#cat-icons .ico-opt[data-i="${data.icon}"]`) : document.querySelector('#cat-icons .ico-opt');
    pickIcon('cat-icons', ic || document.querySelector('#cat-icons .ico-opt'));
  }
 
  if(id==='modal-expense'){
    editMode.expense = (mode==='edit' && data) ? data.id : null;
    document.getElementById('modal-expense-title').textContent = mode==='edit'?'Edit Expense Category':'Add Expense Category';
    document.getElementById('exp-name').value = data?.name || '';
    document.getElementById('exp-desc').value = data?.desc || '';
    setTog('exp-tog-track', data ? !!data.active : true);
  }

    if(id==='modal-warranty'){
    editMode.warranty = (mode==='edit' && data) ? data.id : null;
  document.getElementById('modal-warranty-title').textContent = mode==='edit'?'Edit Warranty Category':'Add Warranty Category';
  document.getElementById('warranty-name').value = data?.name || '';
  document.getElementById('warranty-value').value = data?.value || '';
  setTog('warranty-tog-track', data ? !!data.active : true);
  }
 
  if(id==='modal-priority'){
    editMode.priority = (mode==='edit' && data) ? data.id : null;
    document.getElementById('modal-priority-title').textContent = mode==='edit'?'Edit Priority Level':'Add Priority Level';
    document.getElementById('pri-name').value = data?.name || '';
    document.getElementById('pri-order').value = data?.order || '';
    setTog('pri-tog-track', data ? !!data.active : true);
    const sw = data?.color ? document.querySelector(`#pri-swatches .swatch[data-c="${data.color}"]`) : document.querySelector('#pri-swatches .swatch.sel');
    pickSwatch('pri-swatches', sw || document.querySelector('#pri-swatches .swatch'));
  }
}
function closeModal(id){document.getElementById(id).classList.remove('show');}
function handleOverlayClick(e,id){if(e.target===document.getElementById(id))closeModal(id);}
 
/* ─── EDIT-FROM-ROW HELPERS (read data-attributes) ─── */
function editExpenseBtn(btn){
  openModal('modal-expense','edit',{
    id:     parseInt(btn.dataset.id,10),
    name:   btn.dataset.name,
    desc:   btn.dataset.desc,
    active: btn.dataset.active === '1',
  });
}

function editWarrantyBtn(btn){
  openModal('modal-warranty','edit',{
    id:     parseInt(btn.dataset.id,10),
    name:   btn.dataset.name,
    value:   btn.dataset.value,
    active: btn.dataset.active === '1',
  });
}

function editPriorityBtn(btn){
  openModal('modal-priority','edit',{
    id:     parseInt(btn.dataset.id,10),
    name:   btn.dataset.name,
    color:  btn.dataset.color,
    order:  parseInt(btn.dataset.order,10),
    active: btn.dataset.active === '1',
  });
}
 
/* ─── SWATCH / ICON ─── */
function pickSwatch(wrap,el){
  if(!el)return;
  document.querySelectorAll(`#${wrap} .swatch`).forEach(s=>s.classList.remove('sel'));
  el.classList.add('sel');
}
function pickIcon(wrap,el){
  if(!el)return;
  document.querySelectorAll(`#${wrap} .ico-opt`).forEach(i=>i.classList.remove('sel'));
  el.classList.add('sel');
}
 
/* ─── TOGGLE (modal switches) ─── */
function toggleTog(trackId,wrap){
  const t=document.getElementById(trackId);
  t.classList.toggle('on');
  const lbl=wrap.querySelector('.tog-label');
  if(lbl && lbl.textContent.includes('Active')) lbl.textContent=t.classList.contains('on')?(trackId==='cat-tog-track'?'Active — visible to Dispatch Engine and User Provisioning':'Active'):'Inactive';
}
function setTog(trackId,on){
  const t=document.getElementById(trackId);
  t.classList.toggle('on', !!on);
  const lbl=t.parentElement.querySelector('.tog-label');
  if(lbl && lbl.textContent.includes('Active') || lbl && lbl.textContent.includes('Inactive'))
    lbl.textContent = on ? (trackId==='cat-tog-track'?'Active — visible to Dispatch Engine and User Provisioning':'Active') : 'Inactive';
}
 
/* ─── SAVE CATEGORY (create OR update) ─── */
async function saveCategory(){
  const name=document.getElementById('cat-name').value.trim();
  if(!name){showToast('err','Missing Field','Please enter a category name.');return;}
  const payload={
    category_name: name,
    description:   document.getElementById('cat-desc').value.trim(),
    color_code:    document.querySelector('#cat-swatches .sel')?.dataset.c||'#9A7B4F',
    icon:          document.querySelector('#cat-icons .sel')?.dataset.i||'bi-tools',
    sort_order:    editMode.cat ? undefined : CATS.length+1,
    status:        document.getElementById('cat-tog-track').classList.contains('on')?1:0,
  };
  try{
    if(editMode.cat){
      const r=await api(window.M_ROUTES.catUpdate(editMode.cat),'PUT',payload);
      const c=CATS.find(x=>x.id===editMode.cat);
      Object.assign(c,{name:r.data.category_name,desc:r.data.description,color:r.data.color_code||'#9A7B4F',icon:r.data.icon||'bi-tools',active:!!r.data.status});
      showToast('ok','Category Updated',`"${name}" has been updated.`);
    }else{
      const r=await api(window.M_ROUTES.catStore,'POST',payload);
      CATS.push({id:r.data.id,name:r.data.category_name,desc:r.data.description,color:r.data.color_code||'#9A7B4F',icon:r.data.icon||'bi-tools',active:!!r.data.status,domains:[]});
      showToast('ok','Category Added',`"${name}" has been saved.`);
    }
    closeModal('modal-cat'); renderCats();
    if(selectedCatId) selectCat(selectedCatId);
  }catch(e){showToast('err','Error',e.message);}
}
function editCat(id){
  const c=CATS.find(x=>x.id===id);
  if(c) openModal('modal-cat','edit',{id:c.id,name:c.name,desc:c.desc,color:c.color,icon:c.icon,active:c.active});
}
 
/* ─── SAVE DOMAIN (create OR update) ─── */
async function saveDomain(){
  const name=document.getElementById('dom-name').value.trim();
  if(!name){showToast('err','Missing Field','Please enter a domain name.');return;}
  if(!selectedCatId){showToast('err','No Category','Please select a category first.');return;}
  const payload={
    service_category_id: selectedCatId,
    domain_name:         name,
    description:         document.getElementById('dom-desc').value.trim(),
    sort_order:          0,
    status:              document.getElementById('dom-tog-track').classList.contains('on')?1:0,
  };
  try{
    const cat=CATS.find(c=>c.id===selectedCatId);
    if(editMode.domain){
      const r=await api(window.M_ROUTES.domUpdate(editMode.domain),'PUT',payload);
      const d=cat.domains.find(x=>x.id===editMode.domain);
      Object.assign(d,{name:r.data.domain_name,desc:r.data.description,active:!!r.data.status});
      showToast('ok','Domain Updated',`"${name}" updated.`);
    }else{
      const r=await api(window.M_ROUTES.domStore,'POST',payload);
      cat.domains.push({id:r.data.id,name:r.data.domain_name,desc:r.data.description,active:!!r.data.status});
      showToast('ok','Domain Added',`"${name}" added under ${cat.name}.`);
    }
    renderCats(); renderDomains(cat); closeModal('modal-domain');
  }catch(e){showToast('err','Error',e.message);}
}
function editDomain(id){
  const cat=CATS.find(c=>c.id===selectedCatId);
  const d=cat?.domains.find(x=>x.id===id);
  if(d) openModal('modal-domain','edit',{id:d.id,name:d.name,desc:d.desc,active:d.active});
}
 
/* ─── SAVE EXPENSE (create OR update) ─── */
async function saveExpense(){
  const name=document.getElementById('exp-name').value.trim();
  if(!name){showToast('err','Missing Field','Please enter a category name.');return;}
  const payload={
    name,
    description: document.getElementById('exp-desc').value.trim(),
    status:      document.getElementById('exp-tog-track').classList.contains('on')?1:0,
  };
  try{
    if(editMode.expense){
      await api(window.M_ROUTES.expUpdate(editMode.expense),'PUT',payload);
      showToast('ok','Updated',`"${name}" has been updated.`);
    }else{
      await api(window.M_ROUTES.expStore,'POST',payload);
      showToast('ok','Saved',`"${name}" has been saved.`);
    }
    closeModal('modal-expense');
    setTimeout(()=>location.reload(),700);
  }catch(e){showToast('err','Error',e.message);}
}



async function saveWarranty(){
  const name  = document.getElementById('warranty-name').value.trim();
  const value = document.getElementById('warranty-value').value.trim();

  if(!name){ showToast('err','Missing Field','Please enter name.'); return; }
  if(!value){ showToast('err','Missing Field','Please enter the warranty value.'); return; }
  if(!/^\d+$/.test(value)){ showToast('err','Invalid Value','Warranty value must be digits only.'); return; }

  const payload = {
    name,
    value,
    status: document.getElementById('warranty-tog-track').classList.contains('on') ? 1 : 0,
  };

  try{
    if(editMode.warranty){
      await api(window.M_ROUTES.warrantyUpdate(editMode.warranty),'PUT',payload);
      showToast('ok','Updated',`"${name}" has been updated.`);
    }else{
      await api(window.M_ROUTES.warrantyStore,'POST',payload);
      showToast('ok','Saved',`"${name}" has been saved.`);
    }
    closeModal('modal-warranty');
    setTimeout(()=>location.reload(),700);
  }catch(e){ showToast('err','Error', e.message || 'Could not save.'); }
}


 
/* ─── SAVE PRIORITY (create OR update) ─── */
async function savePriority(){
  const name=document.getElementById('pri-name').value.trim();
  const order=document.getElementById('pri-order').value.trim();
  if(!name||!order){showToast('err','Missing Field','Please fill all required fields.');return;}
  const payload={
    name,
    display_order: parseInt(order,10),
    color:         document.querySelector('#pri-swatches .sel')?.dataset.c||'#3b82f6',
    status:        document.getElementById('pri-tog-track').classList.contains('on')?1:0,
  };
  try{
    if(editMode.priority){
      await api(window.M_ROUTES.priUpdate(editMode.priority),'PUT',payload);
      showToast('ok','Updated',`Priority "${name}" has been updated.`);
    }else{
      await api(window.M_ROUTES.priStore,'POST',payload);
      showToast('ok','Saved',`Priority "${name}" has been saved.`);
    }
    closeModal('modal-priority');
    setTimeout(()=>location.reload(),700);
  }catch(e){showToast('err','Error',e.message);}
}
 
/* ─── STATUS TOGGLE (expense / priority table rows) ─── */
async function toggleStatus(type,id){
  const map={ expense:window.M_ROUTES.expStatus, priority:window.M_ROUTES.priStatus,
              cat:window.M_ROUTES.catStatus, domain:window.M_ROUTES.domStatus };
  try{
    const r=await api(map[type](id),'POST');
    const pillId = type==='expense'?`exp-status-${id}`:`pri-status-${id}`;
    const pill=document.getElementById(pillId);
    if(pill){
      const on=!!r.current_status;
      pill.className=`spill ${on?'spill-on':'spill-off'}`;
      pill.innerHTML=`<i class="bi bi-circle-fill" style="font-size:.4rem;"></i>${on?'Active':'Inactive'}`;
    }
    showToast('ok','Status Updated', r.message||'Status changed.');
  }catch(e){showToast('err','Error',e.message);}
}
 
/* ─── SAVE SLA (whole table) ─── */
async function saveSLA(){
  const trs = [...document.querySelectorAll('.sla-tbl tbody tr[data-priority-id]')];
  const rows = [];

  for (const tr of trs) {
    const read = f => {
      const el = tr.querySelector(`input[data-field="${f}"]`);
      return el ? parseInt(el.value, 10) : NaN;
    };
    const row = {
      priority_id:     parseInt(tr.dataset.priorityId, 10),
      response_time:   read('response_time'),
      assignment_time: read('assignment_time'),
      resolution_time: read('resolution_time'),
    };

    const bad = ['response_time','assignment_time','resolution_time']
      .find(k => isNaN(row[k]) || row[k] < 1 || row[k] > 8760);

    if (bad) {
      const name = tr.querySelector('strong')?.textContent || 'this row';
      showToast('err','Invalid Value',`Enter 1–8760 hours for every field in "${name}".`);
      tr.querySelector(`input[data-field="${bad}"]`)?.focus();
      return;
    }
    rows.push(row);
  }

  if(!rows.length){ showToast('err','Nothing to save','Add criticality levels first.'); return; }

  try{
    await api(window.M_ROUTES.slaSaveAll,'POST',{rows});
    showToast('ok','SLA Updated','SLA Duration Matrix saved successfully.');
  }catch(e){ showToast('err','Error', e.message); }
}
 
/* ─── DELETE ─── */
let pendingDel={};
function confirmDel(type,id,name){
  pendingDel={type,id,name};
  document.getElementById('del-msg').textContent=`Are you sure you want to delete "${name}"? This action cannot be undone.`;
  openModal('modal-del');
}
async function execDel(){
  const {type,id,name}=pendingDel;
  const map={ cat:window.M_ROUTES.catDelete, domain:window.M_ROUTES.domDelete,
              expense:window.M_ROUTES.expDelete, warranty:window.M_ROUTES.warrantyDelete, priority:window.M_ROUTES.priDelete };
  const urlFn=map[type];
  if(!urlFn){closeModal('modal-del');return;}
  try{
    await api(urlFn(id),'DELETE');
    closeModal('modal-del');
    showToast('ok','Deleted',`"${name}" has been removed.`);
    if(type==='cat'){
      const i=CATS.findIndex(c=>c.id===id); if(i>-1)CATS.splice(i,1);
      selectedCatId=null;
      document.getElementById('domain-panel').style.display='none';
      document.getElementById('no-sel-state').style.display='flex';
      renderCats();
    }else if(type==='domain'){
      const cat=CATS.find(c=>c.id===selectedCatId);
      if(cat){const i=cat.domains.findIndex(d=>d.id===id);if(i>-1)cat.domains.splice(i,1);renderCats();renderDomains(cat);}
    }else{
      setTimeout(()=>location.reload(),600);
    }
  }catch(e){closeModal('modal-del');showToast('err','Error',e.message);}
}
 
/* ─── TAB SWITCH ─── */
function switchTab(tab){
  document.querySelectorAll('.trb').forEach(b=>b.classList.toggle('active',b.dataset.tab===tab));
  document.querySelectorAll('.master-panel').forEach(p=>p.classList.toggle('active',p.id===`panel-${tab}`));
  try { sessionStorage.setItem('masterActiveTab', tab); } catch(e){}
}
function restoreTab(){
  let tab='service';
  try { tab = sessionStorage.getItem('masterActiveTab') || 'service'; } catch(e){}
  if(document.getElementById(`panel-${tab}`)) switchTab(tab);
}
 
/* ─── TOAST ─── */
function showToast(type,title,body){
  const w=document.getElementById('toastWrap');
  const icons={ok:'bi-check-circle-fill',err:'bi-x-circle-fill',info:'bi-info-circle-fill'};
  const t=document.createElement('div');t.className='toast-item';
  t.innerHTML=`<i class="bi ${icons[type]||icons.info} t-ico ${type}"></i><div><p class="t-title">${title}</p><p class="t-body">${body}</p></div>`;
  w.appendChild(t);
  setTimeout(()=>{t.style.transition='opacity .3s';t.style.opacity='0';setTimeout(()=>t.remove(),300);},3500);
}
 
/* ─── INIT ─── */
renderCats();
restoreTab();
</script>
@endpush