@extends('layouts.layout')

@section('title', 'Invoice Panel | Matter Mind')
@section('page_title', 'Invoice Panel')
@section('page_icon', 'database')
@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600;700&display=swap" rel="stylesheet"/>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet"/>
<style>
/* ===== Invoice Panel — scoped page styles ===== */
.inv-wrap{--gold:#9a8053;--gold-2:#b8975e;--queue-width:290px;}
.inv-wrap h4,.inv-wrap h5,.inv-wrap h6,.inv-wrap .pg-hdr-title,.inv-wrap .card-title,
.inv-wrap .ws-sr-id,.inv-wrap .qi-id,.inv-wrap .stat-num,.inv-wrap .pa-card-title{letter-spacing:-.01em;}

/* PAGE HEADER */
.inv-wrap .pg-header{background:linear-gradient(135deg,#9A7B4F 0%,#7A6140 100%);border-radius:10px;padding:20px 24px;margin-bottom:20px;color:#fff;position:relative;overflow:hidden;}
.inv-wrap .pg-header::before{content:'';position:absolute;left:-40px;bottom:-40px;width:180px;height:180px;border-radius:50%;background:rgba(255,255,255,.05);}
.inv-wrap .pg-header::after{content:'';position:absolute;right:-30px;top:-30px;width:160px;height:160px;border-radius:50%;background:rgba(255,255,255,.07);}
.inv-wrap .pg-hdr-title{font-size:1rem;font-weight:600;margin:0 0 3px;position:relative;z-index:1;}
.inv-wrap .pg-hdr-desc{font-size:.78rem;margin:0;opacity:.85;position:relative;z-index:1;}
.inv-wrap .pg-hdr-meta{display:flex;align-items:center;gap:8px;margin-top:9px;position:relative;z-index:1;flex-wrap:wrap;}
.inv-wrap .meta-badge{background:rgba(255,255,255,.18);border:1px solid rgba(255,255,255,.3);border-radius:20px;font-size:.6875rem;padding:2px 10px;font-weight:500;}

.inv-wrap .qi-id.sr-ref-trigger,
.inv-wrap .ws-sr-id.sr-ref-trigger,
.inv-wrap td.mono .sr-ref-trigger{ cursor:pointer; }
/* STATS */
.inv-wrap .stats-strip{display:grid;grid-template-columns:repeat(4,1fr);gap:10px;margin-bottom:18px;}
.inv-wrap .stat-card{background:var(--card-bg);border:1px solid var(--card-border);border-radius:9px;padding:13px 15px;display:flex;align-items:center;gap:12px;box-shadow:var(--card-shadow);}
.inv-wrap .stat-icon{width:38px;height:38px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:1rem;flex-shrink:0;}
.inv-wrap .stat-num{font-size:1.4rem;font-weight:700;line-height:1;color:var(--text-heading);}
.inv-wrap .stat-lbl{font-size:.7rem;color:var(--text-muted);}
@media(max-width:767px){.inv-wrap .stats-strip{grid-template-columns:repeat(2,1fr);}}
@media(max-width:420px){.inv-wrap .stats-strip{grid-template-columns:1fr;}}

/* FILTER BAR */
.inv-wrap .filter-bar{background:var(--card-bg);border:1px solid var(--card-border);border-radius:10px;padding:12px 16px;margin-bottom:16px;box-shadow:var(--card-shadow);display:flex;flex-wrap:wrap;gap:10px;align-items:flex-end;}
.inv-wrap .filter-group{display:flex;flex-direction:column;gap:4px;min-width: 0;
    flex: 1 1 auto;}
.inv-wrap .filter-label{font-size:.7rem;font-weight:600;color:var(--text-muted);text-transform:uppercase;letter-spacing:.05em;}
.inv-wrap .filter-control{height:34px;padding:0 10px;border:1px solid var(--border-color);border-radius:7px;background:var(--input-bg);color:var(--text-primary);font-size:.8rem;min-width:140px;transition:border-color .15s;}
.inv-wrap .filter-control:focus{outline:none;border-color:var(--gold);box-shadow:0 0 0 3px rgba(154,128,83,.12);}
.inv-wrap .filter-actions{margin-left:auto;display:flex;gap:8px;align-items:flex-end;}
@media(max-width:575.98px){
  .inv-wrap .filter-group{flex:1 1 100%;}
  .inv-wrap .filter-control{width:100%;min-width:0;}
  .inv-wrap .filter-actions{margin-left:0;width:100%;}
  .inv-wrap .filter-actions .btn-ghost{flex:1;justify-content:center;}
}

/* TWO-PANEL */
.inv-wrap .two-panel{display:grid;grid-template-columns:var(--queue-width) 1fr;gap:14px;align-items:start;}
@media(max-width:899px){.inv-wrap .two-panel{grid-template-columns:1fr;}}

/* QUEUE */
.inv-wrap .queue-panel{background:var(--card-bg);border:1px solid var(--card-border);border-radius:10px;box-shadow:var(--card-shadow);overflow:hidden;position:sticky;top:80px;}
@media(max-width:899px){.inv-wrap .queue-panel{position:static;}}
.inv-wrap .queue-hdr{padding:12px 16px;border-bottom:1px solid var(--border-color);display:flex;align-items:center;justify-content:space-between;}
.inv-wrap .queue-hdr-title{font-size:.8rem;font-weight:600;color:var(--text-heading);display:flex;align-items:center;gap:7px;}
.inv-wrap .q-count{font-size:.7rem;background:rgba(154,128,83,.12);color:var(--gold);padding:2px 8px;border-radius:9px;font-weight:700;}
.inv-wrap .queue-list{max-height:calc(100vh - 320px);overflow-y:auto;}
@media(max-width:899px){.inv-wrap .queue-list{max-height:none;}}
.inv-wrap .queue-list::-webkit-scrollbar{width:3px;}
.inv-wrap .queue-list::-webkit-scrollbar-thumb{background:var(--border-color);border-radius:2px;}
.inv-wrap .queue-item{padding:11px 15px;border-bottom:1px solid var(--border-color);cursor:pointer;transition:background .12s;}
.inv-wrap .queue-item:last-child{border-bottom:none;}
.inv-wrap .queue-item:hover{background:var(--surface-2);}
.inv-wrap .queue-item.active{background:rgba(154,128,83,.07);border-left:3px solid var(--gold);}
.inv-wrap .qi-id{font-size:.78rem;font-weight:700;color:var(--gold);margin-bottom:2px;}
.inv-wrap .qi-client{font-size:.79rem;font-weight:500;color:var(--text-heading);margin-bottom:1px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
.inv-wrap .qi-sub{font-size:.71rem;color:var(--text-muted);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;margin-bottom:5px;}
.inv-wrap .qi-foot{display:flex;align-items:center;justify-content:space-between;}
.inv-wrap .qi-time{font-size:.68rem;color:var(--text-muted);display:flex;align-items:center;gap:3px;}

/* WORKSPACE */
.inv-wrap .ws-panel{display:flex;flex-direction:column;gap:14px;}
.inv-wrap .ws-empty{background:var(--card-bg);border:1px solid var(--card-border);border-radius:10px;box-shadow:var(--card-shadow);padding:60px 24px;text-align:center;}
.inv-wrap .ws-empty-icon{width:56px;height:56px;border-radius:14px;background:var(--surface-2);display:flex;align-items:center;justify-content:center;margin:0 auto 14px;font-size:1.5rem;color:var(--text-light);}
.inv-wrap .ws-empty h6{font-size:.9rem;font-weight:600;color:var(--text-muted);margin-bottom:4px;}
.inv-wrap .ws-empty p{font-size:.78rem;color:var(--text-light);margin:0;}
.inv-wrap .ws-card{background:var(--card-bg);border:1px solid var(--card-border);border-radius:10px;box-shadow:var(--card-shadow);overflow:hidden;}
.inv-wrap .ws-card-hdr{padding:13px 18px;border-bottom:1px solid var(--border-color);display:flex;align-items:center;gap:10px;}
.inv-wrap .ws-card-icon{width:30px;height:30px;border-radius:7px;display:flex;align-items:center;justify-content:center;font-size:.85rem;flex-shrink:0;}
.inv-wrap .ws-card-hdr h6{font-size:.85rem;font-weight:600;color:var(--text-heading);margin:0;}
.inv-wrap .ws-card-body{padding:18px;}

/* SR header in workspace */
.inv-wrap .sr-hdr-card{background:var(--card-bg);border:1px solid var(--card-border);border-radius:10px;box-shadow:var(--card-shadow);padding:16px 20px;}
.inv-wrap .ws-sr-id{font-size:1rem;font-weight:700;color:var(--gold);margin-bottom:2px;}
.inv-wrap .ws-sr-client{font-size:.9rem;font-weight:600;color:var(--text-heading);margin-bottom:2px;}
.inv-wrap .ws-sr-site{font-size:.8rem;color:var(--text-muted);}
.inv-wrap .meta-chips{display:flex;gap:8px;flex-wrap:wrap;margin-top:12px;}
.inv-wrap .meta-chip{background:var(--surface-2);border-radius:7px;padding:7px 12px;}
.inv-wrap .meta-chip-label{font-size:.67rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--text-muted);margin-bottom:2px;}
.inv-wrap .meta-chip-value{font-size:.79rem;font-weight:600;color:var(--text-heading);}

/* FORM */
.inv-wrap .form-label-sm{font-size:.78rem;font-weight:600;color:var(--text-heading);margin-bottom:5px;display:block;}
.inv-wrap .form-label-sm .req{color:#ef4444;margin-left:2px;}
.inv-wrap .form-label-sm .hint{font-size:.7rem;color:var(--text-muted);font-weight:400;margin-left:6px;}
.inv-wrap .form-group{margin-bottom:14px;}
.inv-wrap .form-group:last-child{margin-bottom:0;}
.inv-wrap .fc{width:100%;padding:8px 11px;border:1px solid var(--border-color);border-radius:7px;background:var(--input-bg);color:var(--text-primary);font-size:.8125rem;transition:border-color .15s,box-shadow .15s;}
.inv-wrap .fc:focus{outline:none;border-color:var(--gold);box-shadow:0 0 0 3px rgba(154,128,83,.12);}
.inv-wrap .field-hint{font-size:.72rem;color:var(--text-muted);margin-top:4px;}

/* DROPZONE */
.inv-wrap .dropzone{border:2px dashed var(--border-color);border-radius:10px;background:var(--dz-bg,#f7f9fd);padding:26px 20px;text-align:center;cursor:pointer;transition:all .2s;position:relative;}
.inv-wrap .dropzone:hover,.inv-wrap .dropzone.dragover{border-color:var(--gold);background:rgba(154,128,83,.04);}
.inv-wrap .dropzone.has-file{border-color:#15803d;background:rgba(21,128,61,.04);border-style:solid;}
.inv-wrap .dropzone.dz-err{border-color:#ef4444;background:rgba(239,68,68,.04);}
.inv-wrap .dz-icon{font-size:2rem;color:var(--text-light);margin-bottom:8px;display:block;transition:color .2s;}
.inv-wrap .dropzone:hover .dz-icon,.inv-wrap .dropzone.dragover .dz-icon{color:var(--gold);}
.inv-wrap .dropzone.has-file .dz-icon{color:#15803d;}
.inv-wrap .dropzone.dz-err .dz-icon{color:#ef4444;}
.inv-wrap .dz-title{font-size:.82rem;font-weight:500;color:var(--text-heading);margin-bottom:3px;}
.inv-wrap .dz-sub{font-size:.75rem;color:var(--text-muted);}
.inv-wrap .dz-file-row{display:none;align-items:center;gap:10px;justify-content:center;margin-top:10px;}
.inv-wrap .dz-file-row.show{display:flex;}
.inv-wrap .dz-file-ico{width:32px;height:32px;background:rgba(21,128,61,.1);border-radius:7px;display:flex;align-items:center;justify-content:center;color:#15803d;font-size:.9rem;flex-shrink:0;}
.inv-wrap .dz-fname{font-size:.8rem;font-weight:500;color:#15803d;text-align:left;word-break:break-all;}
.inv-wrap .dz-fsize{font-size:.72rem;color:var(--text-muted);}
.inv-wrap .dz-rm{background:none;border:none;color:var(--text-muted);cursor:pointer;font-size:.85rem;padding:2px;border-radius:4px;line-height:1;}
.inv-wrap .dz-rm:hover{color:#ef4444;}
.inv-wrap .dz-err-msg{display:none;font-size:.75rem;color:#ef4444;margin-top:8px;font-weight:500;}
.inv-wrap .dz-err-msg.show{display:block;}
.inv-wrap .dz-input{position:absolute;inset:0;opacity:0;cursor:pointer;width:100%;height:100%;}

/* BUTTONS */
.inv-wrap .btn-gold{display:inline-flex;align-items:center;gap:6px;padding:7px 14px;background:linear-gradient(135deg,var(--gold),var(--gold-2));color:#fff;border:none;border-radius:7px;font-size:.8rem;font-weight:500;cursor:pointer;white-space:nowrap;}
.inv-wrap .btn-gold:hover{opacity:.87;}
.inv-wrap .btn-ghost{display:inline-flex;align-items:center;gap:6px;padding:7px 12px;background:var(--surface-2);color:var(--text-muted);border:1px solid var(--border-color);border-radius:7px;font-size:.8rem;cursor:pointer;}
.inv-wrap .btn-ghost:hover{background:var(--surface-3);}
.inv-wrap .btn-submit{width:100%;padding:12px;border:none;border-radius:8px;font-size:.875rem;font-weight:600;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px;transition:opacity .15s;margin-top:4px;}
.inv-wrap .btn-submit:hover{opacity:.88;}
.inv-wrap .btn-submit:disabled{opacity:.38;cursor:not-allowed;}
.inv-wrap .btn-green{background:linear-gradient(135deg,#9A7B4F,#7A6140);color:#fff;}

/* CLOSEOUT LOCK */
.inv-wrap .lock-banner{display:flex;align-items:center;gap:8px;padding:10px 13px;border-radius:7px;font-size:.78rem;margin-bottom:12px;background:rgba(245,158,11,.07);border:1px solid rgba(245,158,11,.2);color:#d97706;}

/* RESOURCE BLOCKS */
.inv-wrap .resource-grid{display:grid;grid-template-columns:1fr 1fr;gap:12px;}
@media(max-width:640px){.inv-wrap .resource-grid{grid-template-columns:1fr;}}
.inv-wrap .resource-block{background:var(--surface-2);border:1px solid var(--border-color);border-radius:8px;padding:13px 14px;}
.inv-wrap .rb-label{font-size:.67rem;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--text-muted);margin-bottom:10px;display:flex;align-items:center;gap:6px;}
.inv-wrap .rb-row{display:flex;align-items:center;justify-content:space-between;padding:5px 0;border-bottom:1px solid var(--border-color);font-size:.79rem;}
.inv-wrap .rb-row:last-child{border-bottom:none;padding-bottom:0;}
.inv-wrap .rb-key{color:var(--text-muted);}
.inv-wrap .rb-val{font-weight:500;color:var(--text-heading);}
.inv-wrap .rb-total{font-weight:700;color:var(--gold);font-size:.85rem;}
.inv-wrap .readonly-tag{font-size:.65rem;background:var(--surface-3);color:var(--text-muted);padding:1px 6px;border-radius:4px;font-weight:600;margin-left:auto;}

/* SECTION DIVIDER */
.inv-wrap .section-divider{display:flex;align-items:center;gap:12px;margin:22px 0 14px;}
.inv-wrap .section-divider-line{flex:1;height:1px;background:var(--border-color);}
.inv-wrap .section-label{display:flex;align-items:center;gap:8px;font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--text-muted);white-space:nowrap;}
.inv-wrap .section-count{font-size:.65rem;padding:1px 7px;border-radius:9px;font-weight:700;}

/* APPROVAL TABLE */
.inv-wrap .pa-card{background:var(--card-bg);border:1px solid var(--card-border);border-radius:10px;box-shadow:var(--card-shadow);overflow:hidden;}
.inv-wrap .pa-card-hdr{display:flex;align-items:center;justify-content:space-between;padding:13px 18px;border-bottom:1px solid var(--border-color);flex-wrap:wrap;gap:8px;}
.inv-wrap .pa-card-hdr-left{display:flex;align-items:center;gap:10px;}
.inv-wrap .pa-card-icon{width:30px;height:30px;border-radius:7px;display:flex;align-items:center;justify-content:center;font-size:.85rem;flex-shrink:0;}
.inv-wrap .pa-card-title{font-size:.85rem;font-weight:600;color:var(--text-heading);}
.inv-wrap .pa-card-sub{font-size:.72rem;color:var(--text-muted);margin-top:1px;}
.inv-wrap table.pa-tbl{width:100%;border-collapse:collapse;min-width:760px;}
.inv-wrap table.pa-tbl thead th{padding:9px 14px;font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--text-muted);background:var(--surface-2);border-bottom:1px solid var(--border-color);white-space:nowrap;text-align:left;}
.inv-wrap table.pa-tbl tbody tr{border-bottom:1px solid var(--border-color);transition:background .1s;}
.inv-wrap table.pa-tbl tbody tr:last-child{border-bottom:none;}
.inv-wrap table.pa-tbl tbody tr:hover{background:var(--table-hover);}
.inv-wrap table.pa-tbl td{padding:11px 14px;font-size:.8rem;color:var(--text-primary);vertical-align:middle;}
.inv-wrap table.pa-tbl td.mono{font-size:.77rem;font-weight:600;color:var(--gold);}
.inv-wrap table.pa-tbl td.muted{color:var(--text-muted);font-size:.78rem;}
.inv-wrap .pa-empty{padding:30px;text-align:center;font-size:.8rem;color:var(--text-muted);}
.inv-wrap .pa-empty i{display:block;font-size:1.4rem;color:var(--text-light);margin-bottom:7px;}
.inv-wrap .pa-scroll{overflow-x:auto;-webkit-overflow-scrolling:touch;}

/* APPROVE BUTTON */
.inv-wrap .btn-mark{display:inline-flex;align-items:center;gap:5px;padding:5px 11px;border-radius:6px;font-size:.76rem;font-weight:600;cursor:pointer;border:1px solid;transition:opacity .15s;white-space:nowrap;}
.inv-wrap .btn-mark:hover{opacity:.82;}
.inv-wrap .btn-mark-blue{background:rgba(154,128,83,.1);color:var(--gold);border-color:rgba(154,128,83,.28);}

/* BADGES */
.inv-wrap .sbadge{display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:20px;font-size:.7rem;font-weight:600;}
.inv-wrap .sb-oow{background:rgba(239,68,68,.1);color:#ef4444;}
.inv-wrap .sb-pi{background:rgba(154,128,83,.12);color:var(--gold);}

/* SUCCESS */
.inv-wrap .ws-success{background:var(--card-bg);border:1px solid var(--card-border);border-radius:10px;box-shadow:var(--card-shadow);padding:48px 24px;text-align:center;display:none;}
.inv-wrap .ws-success.show{display:block;}
.inv-wrap .s-icon{width:60px;height:60px;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 14px;font-size:1.6rem;}

/* MODALS */
.inv-modal-overlay{display:none;position:fixed;inset:0;background:var(--overlay-bg,rgba(9,15,35,.62));z-index:900;align-items:center;justify-content:center;backdrop-filter:blur(4px);padding:20px;}
.inv-modal-overlay.show{display:flex;}
.inv-modal-overlay .modal-box{background:var(--modal-bg,#fff);border-radius:12px;width:100%;max-width:440px;box-shadow:var(--modal-shadow,0 24px 64px rgba(0,0,0,.16));border:1px solid var(--card-border);overflow:hidden;animation:invMIn .18s ease;}
@keyframes invMIn{from{opacity:0;transform:scale(.96);}to{opacity:1;transform:scale(1);}}
.inv-modal-overlay .modal-hdr{display:flex;align-items:center;justify-content:space-between;padding:15px 20px;border-bottom:1px solid var(--border-color);}
.inv-modal-overlay .modal-hdr-left{display:flex;align-items:center;gap:10px;}
.inv-modal-overlay .modal-hdr-icon{width:32px;height:32px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:.9rem;flex-shrink:0;}
.inv-modal-overlay .modal-hdr h6{font-size:.9rem;font-weight:600;color:var(--text-heading);margin:0;}
.inv-modal-overlay .modal-close{background:none;border:none;color:var(--text-muted);cursor:pointer;padding:4px;border-radius:5px;font-size:1rem;line-height:1;}
.inv-modal-overlay .modal-close:hover{background:var(--surface-2);}
.inv-modal-overlay .modal-body{padding:20px;}
.inv-modal-overlay .modal-foot{display:flex;gap:10px;padding:14px 20px;border-top:1px solid var(--border-color);background:var(--surface-2);}
.inv-modal-overlay .btn-confirm{flex:1;padding:9px;border:none;border-radius:7px;font-size:.82rem;font-weight:600;cursor:pointer;color:#fff;}
.inv-modal-overlay .btn-cancel{flex:1;padding:9px;background:var(--surface-2);color:var(--text-muted);border:1px solid var(--border-color);border-radius:7px;font-size:.82rem;cursor:pointer;}

/* TOAST */
.inv-toast-wrap{position:fixed;bottom:22px;right:22px;z-index:9999;display:flex;flex-direction:column;gap:8px;pointer-events:none;max-width:calc(100vw - 44px);}
.inv-toast-wrap .toast-item{display:flex;align-items:flex-start;gap:10px;padding:12px 15px;border-radius:9px;background:var(--card-bg);border:1px solid var(--card-border);box-shadow:0 6px 24px rgba(0,0,0,.14);min-width:240px;max-width:320px;animation:invToastIn .2s ease;pointer-events:auto;}
@keyframes invToastIn{from{opacity:0;transform:translateY(10px);}to{opacity:1;transform:translateY(0);}}
.inv-toast-wrap .t-ico{font-size:1rem;flex-shrink:0;margin-top:1px;}
.inv-toast-wrap .t-ico.ok{color:#15803d;}.inv-toast-wrap .t-ico.err{color:#ef4444;}
.inv-toast-wrap .t-ico.info{color:var(--gold);}.inv-toast-wrap .t-ico.warn{color:#d97706;}
.inv-toast-wrap .t-title{font-size:.8rem;font-weight:600;color:var(--text-heading);margin:0 0 2px;}
.inv-toast-wrap .t-body{font-size:.75rem;color:var(--text-muted);margin:0;}
@media(max-width:575.98px){.inv-toast-wrap{left:12px;right:12px;bottom:12px;}.inv-toast-wrap .toast-item{max-width:none;}}
</style>
@endpush

@section('content')
<div class="inv-wrap">

  {{-- PAGE HEADER --}}
  <div class="pg-header">
    <h4 class="pg-hdr-title"><i class="bi bi-receipt me-2"></i>Invoice Upload &amp; Mark Approve</h4>
    <p class="pg-hdr-desc">Upload finalised invoices for QC-passed OoW SRs. SR closure is locked until a verified Invoice PDF is committed. Mark HoP approval to trigger final completion.</p>
    <div class="pg-hdr-meta">
      <span class="meta-badge"><i class="bi bi-shield-fill-check me-1"></i>Super Admin</span>
      <span class="meta-badge"><i class="bi bi-person-gear me-1"></i>Admin</span>
      <span class="meta-badge"><i class="bi bi-calculator me-1"></i>Accounts / AR</span>
    </div>
  </div>

  {{-- STATS --}}
  <div class="stats-strip">
    <div class="stat-card"><div class="stat-icon" style="background:rgba(37,99,235,.1);"><i class="bi bi-hourglass-split" style="color:#2563eb;"></i></div><div><div class="stat-num" id="stat-pi">0</div><div class="stat-lbl">Pending Invoice Upload</div></div></div>
   <div class="stat-card"><div class="stat-icon" style="background:rgba(154,128,83,.1);"><i class="bi bi-person-workspace" style="color:#9a8053;"></i></div><div><div class="stat-num" id="stat-ph">0</div><div class="stat-lbl">Pending HoP Approval</div></div></div>
    <div class="stat-card"><div class="stat-icon" style="background:rgba(21,128,61,.1);"><i class="bi bi-check-circle" style="color:#15803d;"></i></div><div><div class="stat-num">{{ $completedThisMonth ?? 0 }}</div><div class="stat-lbl">Completed This Month</div></div></div>
    <div class="stat-card"><div class="stat-icon" style="background:rgba(154,128,83,.1);"><i class="bi bi-currency-exchange" style="color:#9a8053;"></i></div><div><div class="stat-num">AED {{ $invoicedThisMonth ?? '0' }}</div><div class="stat-lbl">Invoiced This Month</div></div></div>
  </div>

  {{-- FILTER BAR --}}
  <div class="filter-bar">
    <div class="filter-group"><div class="filter-label">Search</div><input class="filter-control" type="text" id="inv-search" placeholder="SR ID, customer name…"/></div>
    <div class="filter-group"><div class="filter-label">Date From</div><input class="filter-control" type="date" id="inv-date"/></div>
    <div class="filter-actions"><button class="btn-ghost" onclick="invResetFilters()"><i class="bi bi-x-circle"></i>Reset</button></div>
  </div>

  {{-- TWO-PANEL --}}
  <div class="two-panel">
    <div class="queue-panel">
      <div class="queue-hdr">
        <div class="queue-hdr-title"><i class="bi bi-list-ul" style="color:#9a8053;"></i>Pending Invoice</div>
        <span class="q-count" id="inv-count">0</span>
      </div>
      <div class="queue-list" id="inv-list"></div>
    </div>
    <div class="ws-panel">
      <div class="ws-empty" id="inv-empty">
        <div class="ws-empty-icon"><i class="bi bi-receipt"></i></div>
        <h6>Select a Ticket</h6>
        <p>Choose a QC-passed OoW SR to upload its invoice and initiate the finalisation process.</p>
      </div>
      <div class="ws-success" id="inv-success">
        <div class="s-icon" style="background:rgba(154,128,83,.1);"><i class="bi bi-receipt" style="color:#9a8053;font-size:1.6rem;"></i></div>
        <h5 style="font-size:1rem;color:var(--text-heading);margin-bottom:6px;font-family:unset;" id="inv-success-title"></h5>
        <p style="font-size:.82rem;color:var(--text-muted);margin-bottom:18px;" id="inv-success-body"></p>
        <button onclick="invNext()" class="btn-gold"><i class="bi bi-arrow-right"></i>Next Ticket</button>
      </div>
      <div id="inv-detail" style="display:none;flex-direction:column;gap:14px;">
        <div class="sr-hdr-card">
          <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:12px;margin-bottom:12px;flex-wrap:wrap;">
            <div><div class="ws-sr-id" id="inv-sr-id"></div><div class="ws-sr-client" id="inv-sr-client"></div><div class="ws-sr-site" id="inv-sr-site"></div></div>
            <div style="display:flex;flex-direction:column;align-items:flex-end;gap:6px;">
              <span class="sbadge sb-pi"><i class="bi bi-circle-fill" style="font-size:.4rem;"></i>Pending Invoice</span>
              <span class="sbadge sb-oow"><i class="bi bi-shield-exclamation"></i>Out of Warranty</span>
            </div>
          </div>
          <div class="meta-chips" id="inv-chips"></div>
        </div>
        <div class="ws-card">
          <div class="ws-card-hdr">
            <div class="ws-card-icon" style="background:rgba(37,99,235,.1);"><i class="bi bi-person-badge" style="color:#2563eb;"></i></div>
            <h6>Technician Resource Summary</h6>
            <span class="readonly-tag" style="margin-left:auto;"><i class="bi bi-lock-fill" style="font-size:.6rem;"></i> Read Only</span>
          </div>
          <div class="ws-card-body"><div class="resource-grid" id="inv-resources"></div></div>
        </div>
        <div class="ws-card">
          <div class="ws-card-hdr">
            <div class="ws-card-icon" style="background:rgba(154,128,83,.1);"><i class="bi bi-receipt" style="color:#9a8053;"></i></div>
            <h6>Invoice Upload</h6>
          </div>
          <div class="ws-card-body">
            <div class="lock-banner" id="inv-lock"><i class="bi bi-lock-fill"></i>Final closure is locked until a verified Invoice PDF is committed to this record.</div>
            <div class="form-group">
              <label class="form-label-sm">Final External Invoice Code <span class="req">*</span><span class="hint">Visible to Admin &amp; HoP only</span></label>
              <input type="text" class="fc" id="inv-code" placeholder="e.g. INV-2025-ERP-00882" oninput="inv_validate()" style="text-transform:uppercase;letter-spacing:.03em;"/>
              <div class="field-hint">Alphanumeric invoice reference from the external ERP system.</div>
            </div>
            <div class="form-group">
              <label class="form-label-sm">Invoice Total (AED) <span class="req">*</span><span class="hint">Final billed amount</span></label>
              <input type="number"   name="invoice_total" class="fc" id="inv-total" placeholder="0.00" min="0" step="0.01"
                    inputmode="decimal" oninput="inv_validate()"/>
            </div>
            <div class="form-group">
              <label class="form-label-sm">Invoice Document PDF <span class="req">*</span></label>
              <div class="dropzone" id="inv-dz" onclick="dz_click('inv-fi')"
                ondragover="dz_dragover(event,'inv-dz')" ondragleave="dz_dragleave('inv-dz')"
                ondrop="dz_drop(event,'inv-dz','inv-fi','inv')">
                <input type="file" class="dz-input" id="inv-fi" accept=".pdf" onchange="dz_change(event,'inv-dz','inv')"/>
                <i class="bi bi-cloud-upload dz-icon" id="inv-dz-icon"></i>
                <div class="dz-title" id="inv-dz-title">Drag &amp; Drop Invoice PDF here or click to browse</div>
                <div class="dz-sub" id="inv-dz-sub">Accepted: PDF only · Max 25MB</div>
                <div class="dz-file-row" id="inv-frow">
                  <div class="dz-file-ico"><i class="bi bi-file-earmark-pdf-fill"></i></div>
                  <div><div class="dz-fname" id="inv-fname"></div><div class="dz-fsize" id="inv-fsize"></div></div>
                  <button class="dz-rm" onclick="event.stopPropagation();dz_remove('inv-dz','inv-fi','inv','Drag &amp; Drop Invoice PDF here or click to browse')" title="Remove"><i class="bi bi-x-circle"></i></button>
                </div>
                <div class="dz-err-msg" id="inv-dz-err">Only PDF files are accepted.</div>
              </div>
            </div>
            <div id="inv-val-msg" style="display:none;padding:8px 12px;border-radius:7px;background:rgba(239,68,68,.07);border:1px solid rgba(239,68,68,.2);color:#ef4444;font-size:.78rem;margin-bottom:10px;"></div>
            <button class="btn-submit btn-green" id="inv-btn" onclick="openInvConfirm()" disabled>
              <i class="bi bi-check-circle-fill"></i>Upload Invoice &amp; Finalise SR
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- PENDING HOP APPROVAL SECTION --}}
  <div class="section-divider">
    <div class="section-divider-line"></div>
    <div class="section-label">
      <i class="bi bi-person-workspace" style="color:#9a8053;"></i>Pending HoP Approval
      <span class="section-count" style="background:rgba(154,128,83,.12);color:#9a8053;" id="ph-count">0</span>
    </div>
    <div class="section-divider-line"></div>
  </div>

  <div class="pa-card">
    <div class="pa-card-hdr">
      <div class="pa-card-hdr-left">
        <div class="pa-card-icon" style="background:rgba(154,128,83,.1);"><i class="bi bi-person-check" style="color:#9a8053;"></i></div>
        <div><div class="pa-card-title">Submitted — Awaiting Head of Projects</div><div class="pa-card-sub">Invoice committed — mark when HoP confirms final closure</div></div>
      </div>
      <div style="font-size:.75rem;color:var(--text-muted);display:flex;align-items:center;gap:5px;"><i class="bi bi-bell" style="color:#9a8053;"></i>HoP notified on submission</div>
    </div>
    <div class="pa-scroll">
      <table class="pa-tbl">
        <thead>
          <tr><th>SR ID</th><th>Customer</th><th>Site</th><th>Invoice Code</th><th>Submitted</th><th>Pending</th><th style="text-align:center;width:180px;">Action</th></tr>
        </thead>
        <tbody id="ph-tbody"></tbody>
      </table>
    </div>
  </div>

  {{-- INVOICE SUBMIT CONFIRM MODAL --}}
  <div class="inv-modal-overlay" id="inv-conf-modal" onclick="if(event.target===this)this.classList.remove('show')">
    <div class="modal-box">
      <div class="modal-hdr">
        <div class="modal-hdr-left">
          <div class="modal-hdr-icon" style="background:rgba(154,128,83,.1);"><i class="bi bi-check-circle-fill" style="color:#9a8053;"></i></div>
          <h6>Confirm Invoice Upload &amp; Submission</h6>
        </div>
        <button class="modal-close" onclick="document.getElementById('inv-conf-modal').classList.remove('show')"><i class="bi bi-x-lg"></i></button>
      </div>
      <div class="modal-body">
        <p style="font-size:.8rem;color:var(--text-muted);margin-bottom:14px;">Submitting invoice for <strong id="inv-conf-sr" style="color:var(--text-heading);"></strong>. This will:</p>
        <div style="display:flex;flex-direction:column;gap:8px;margin-bottom:12px;">
          <div style="display:flex;align-items:center;gap:8px;font-size:.8rem;"><i class="bi bi-1-circle-fill" style="color:#9a8053;flex-shrink:0;"></i>Commit the Invoice PDF to the SR record.</div>
          <div style="display:flex;align-items:center;gap:8px;font-size:.8rem;"><i class="bi bi-2-circle-fill" style="color:#9a8053;flex-shrink:0;"></i>Forward to <strong>Head of Projects</strong> for final approval.</div>
          <div style="display:flex;align-items:center;gap:8px;font-size:.8rem;"><i class="bi bi-3-circle-fill" style="color:#9a8053;flex-shrink:0;"></i>On HoP approval → Status:<strong>Completed</strong> + WhatsApp summary to customer.</div>
        </div>
        <div style="padding:9px 12px;background:rgba(154,128,83,.07);border:1px solid rgba(154,128,83,.2);border-radius:7px;font-size:.78rem;color:#9a8053;display:flex;align-items:center;gap:8px;">
          <i class="bi bi-info-circle"></i>Cannot be undone once the invoice is committed.
        </div>
      </div>
      <div class="modal-foot">
        <button class="btn-cancel" onclick="document.getElementById('inv-conf-modal').classList.remove('show')">Cancel</button>
        <button class="btn-confirm" style="background:linear-gradient(135deg,#9A7B4F,#7A6140);" onclick="execInvSubmit()"><i class="bi bi-check-lg"></i> Confirm &amp; Submit</button>
      </div>
    </div>
  </div>

  {{-- HOP APPROVAL MODAL --}}
  <div class="inv-modal-overlay" id="hop-modal" onclick="if(event.target===this)this.classList.remove('show')">
    <div class="modal-box">
      <div class="modal-hdr">
        <div class="modal-hdr-left">
          <div class="modal-hdr-icon" style="background:rgba(154,128,83,.1);"><i class="bi bi-patch-check-fill" style="color:#9a8053;"></i></div>
          <h6 style="font-family:unset;">Mark Invoice as HoP Approved</h6>
        </div>
        <button class="modal-close" onclick="document.getElementById('hop-modal').classList.remove('show')"><i class="bi bi-x-lg"></i></button>
      </div>
      <div class="modal-body">
        <p style="font-size:.8rem;color:var(--text-muted);margin-bottom:14px;">Confirm Head of Projects has approved the invoice for <strong id="hop-sr" style="color:var(--text-heading);"></strong>.</p>
        <div style="padding:12px 14px;border-radius:8px;background:rgba(154,128,83,.07);border:1px solid rgba(154,128,83,.2);display:flex;align-items:flex-start;gap:10px;margin-bottom:12px;">
          <i class="bi bi-arrow-right-circle-fill" style="color:#9a8053;flex-shrink:0;margin-top:2px;"></i>
          <div style="font-size:.8rem;color:#9a8053;"><strong>Status: Pending Invoice → Completed</strong><br/><span style="opacity:.8;font-size:.76rem;">SR fully closed. Digital summary and feedback link dispatched to customer via WhatsApp.</span></div>
        </div>
      </div>
      <div class="modal-foot">
        <button class="btn-cancel" onclick="document.getElementById('hop-modal').classList.remove('show')">Cancel</button>
        <button class="btn-confirm" style="background:linear-gradient(135deg,#9A7B4F,#7A6140);" onclick="execHopApproval()"><i class="bi bi-check-lg"></i> Confirm HoP Approval &amp; Close SR</button>
      </div>
    </div>
  </div>

  <div class="inv-toast-wrap" id="invToastWrap"></div>
</div>
@include('partials.sr_tracking_modal')
@endsection

@push('scripts')
<script>
/* =========================================================
   Invoice Panel — page scripts
   INV_QUEUE row shape:
   { id, dbId, client, site, technician, logged, createdAt,
     punchIn, punchOut, duration, expenses:[{cat, amt}], totalExp }
   PENDING_HOP row shape:
   { id, sr, dbId, client, site, code, submitted, waiting, createdAt }
   ========================================================= */
var INV_QUEUE   = @json($invQueue ?? []);
var PENDING_HOP = @json($pendingHop ?? []);
var CSRF        = '{{ csrf_token() }}';
var CAN_HOP_APPROVE = @json($canHopApprove ?? false);
/* filtered views — what actually gets rendered */
var INV_FILTERED = INV_QUEUE.slice();
var PH_FILTERED  = PENDING_HOP.slice();

var selInv = null;
var inv_fileOk = false;

/* ---------- TOAST ---------- */
function showToast(type,title,body){
  var w = document.getElementById('invToastWrap');
  var icons = {ok:'bi-check-circle-fill',err:'bi-x-circle-fill',info:'bi-info-circle-fill',warn:'bi-exclamation-triangle-fill'};
  var t = document.createElement('div');
  t.className = 'toast-item';
  t.innerHTML = '<i class="bi '+(icons[type]||icons.info)+' t-ico '+type+'"></i><div><p class="t-title">'+title+'</p><p class="t-body">'+body+'</p></div>';
  w.appendChild(t);
  setTimeout(function(){t.style.transition='opacity .3s';t.style.opacity='0';setTimeout(function(){t.remove();},300);},3800);
}

/* ---------- DROPZONE ---------- */
function dz_dragover(e,id){e.preventDefault();document.getElementById(id).classList.add('dragover');}
function dz_dragleave(id){document.getElementById(id).classList.remove('dragover');}
function dz_click(inputId){document.getElementById(inputId).click();}
function dz_drop(e,dzId,inputId,prefix){e.preventDefault();document.getElementById(dzId).classList.remove('dragover');var f=e.dataTransfer.files[0];if(f)dz_process(f,dzId,prefix);}
function dz_change(e,dzId,prefix){var f=e.target.files[0];if(f)dz_process(f,dzId,prefix);}
function dz_process(file,dzId,prefix){
  var dz = document.getElementById(dzId);
  var errEl = document.getElementById(prefix+'-dz-err');
  if(!file.name.toLowerCase().endsWith('.pdf')){
    dz.className = 'dropzone dz-err';
    document.getElementById(prefix+'-dz-icon').className = 'bi bi-x-circle dz-icon';
    document.getElementById(prefix+'-dz-title').textContent = 'Invalid file — PDF only';
    if(errEl)errEl.classList.add('show');
    window[prefix+'_fileOk'] = false;
    if(typeof window[prefix+'_validate']==='function')window[prefix+'_validate']();
    return;
  }
  var mb = (file.size/1024/1024).toFixed(2);
  dz.className = 'dropzone has-file';
  document.getElementById(prefix+'-dz-icon').className = 'bi bi-file-earmark-pdf-fill dz-icon';
  document.getElementById(prefix+'-dz-title').textContent = 'File attached successfully';
  document.getElementById(prefix+'-dz-sub').textContent = '';
  document.getElementById(prefix+'-fname').textContent = file.name;
  document.getElementById(prefix+'-fsize').textContent = mb+' MB · PDF';
  document.getElementById(prefix+'-frow').classList.add('show');
  if(errEl)errEl.classList.remove('show');
  window[prefix+'_fileOk'] = true;
  if(typeof window[prefix+'_validate']==='function')window[prefix+'_validate']();
}
function dz_remove(dzId,inputId,prefix,placeholder){
  var dz = document.getElementById(dzId);
  dz.className = 'dropzone';
  document.getElementById(prefix+'-dz-icon').className = 'bi bi-cloud-upload dz-icon';
  document.getElementById(prefix+'-dz-title').textContent = placeholder||'Drag & Drop PDF here or click to browse';
  document.getElementById(prefix+'-dz-sub').textContent = 'Accepted format: PDF only · Max 25MB';
  document.getElementById(prefix+'-frow').classList.remove('show');
  var inp = document.getElementById(inputId);if(inp)inp.value='';
  window[prefix+'_fileOk'] = false;
  if(typeof window[prefix+'_validate']==='function')window[prefix+'_validate']();
}

/* ---------- VALIDATION ---------- */
function inv_validate(){
  var code  = document.getElementById('inv-code').value.trim();
  var total = document.getElementById('inv-total').value.trim();
  var btn   = document.getElementById('inv-btn');
  var msg   = document.getElementById('inv-val-msg');

  var totalNum = parseFloat(total);
  var totalOk  = total !== '' && !isNaN(totalNum) && totalNum >= 0;

  if(total !== '' && !totalOk){
    msg.style.display = 'block';
    msg.textContent   = 'Invoice total must be a number of 0 or more.';
  } else {
    msg.style.display = 'none';
  }

  var ok = code.length >= 3 && totalOk && inv_fileOk;
  btn.disabled = !ok;
  btn.style.opacity = ok ? '1' : '.38';
}
window.inv_validate = inv_validate;

/* ---------- QUEUE ---------- */
function renderInvQueue(list){
  list = list || INV_FILTERED;
  var ul = document.getElementById('inv-list');
  document.getElementById('inv-count').textContent = list.length;
  var pi = document.getElementById('stat-pi');
  if(pi) pi.textContent = INV_QUEUE.length;   // KPI = total, not filtered
  if(!list.length){
    ul.innerHTML = '<div style="padding:28px;text-align:center;font-size:.8rem;color:var(--text-muted);"><i class="bi bi-inbox" style="display:block;font-size:1.6rem;margin-bottom:8px;color:var(--text-light);"></i>No matching tickets</div>';
    return;
  }
  ul.innerHTML = list.map(function(sr){
    var ac = selInv && selInv.id===sr.id ? ' active':'';
    return '<div class="queue-item'+ac+'" data-id="'+sr.id+'" onclick="selectInv(this.dataset.id)">'+
      '<div class="qi-id sr-ref-trigger" data-sr-id="'+sr.dbId+'" onclick="event.stopPropagation(); openSrTracking('+sr.dbId+');">'+sr.id+'</div>'+
      '<div class="qi-client">'+sr.client+'</div>'+
      '<div class="qi-sub"><i class="bi bi-geo-alt" style="font-size:.7rem;"></i> '+sr.site+'</div>'+
      '<div class="qi-foot">'+
        '<span class="sbadge sb-pi" style="font-size:.65rem;">Pending Invoice</span>'+
        '<span class="qi-time"><i class="bi bi-clock" style="font-size:.65rem;"></i>'+(sr.logged||'')+'</span>'+
      '</div></div>';
  }).join('');
}

function selectInv(id){
  selInv = INV_QUEUE.find(function(s){return s.id===id;});
  if(!selInv) return;
  inv_fileOk = false;
  dz_remove('inv-dz','inv-fi','inv','Drag & Drop Invoice PDF here or click to browse');
  document.getElementById('inv-code').value = '';
  document.getElementById('inv-total').value = Number(selInv.totalExp || 0).toFixed(2);
  inv_validate();
  renderInvQueue();
  document.getElementById('inv-empty').style.display = 'none';
  document.getElementById('inv-success').classList.remove('show');
  document.getElementById('inv-detail').style.display = 'flex';
  var invSrIdEl = document.getElementById('inv-sr-id');
invSrIdEl.textContent = selInv.id;
invSrIdEl.classList.add('sr-ref-trigger');
invSrIdEl.onclick = function(){ openSrTracking(selInv.dbId); };
  document.getElementById('inv-sr-client').textContent = selInv.client;
  document.getElementById('inv-sr-site').innerHTML     = '<i class="bi bi-geo-alt" style="color:#9a8053;font-size:.8rem;"></i> '+selInv.site;
  document.getElementById('inv-chips').innerHTML =
    '<div class="meta-chip"><div class="meta-chip-label">Technician</div><div class="meta-chip-value">'+(selInv.technician||'—')+'</div></div>'+
    '<div class="meta-chip"><div class="meta-chip-label">Punch In</div><div class="meta-chip-value">'+(selInv.punchIn||'—')+'</div></div>'+
    '<div class="meta-chip"><div class="meta-chip-label">Punch Out</div><div class="meta-chip-value">'+(selInv.punchOut||'—')+'</div></div>'+
    '<div class="meta-chip"><div class="meta-chip-label">Duration</div><div class="meta-chip-value">'+(selInv.duration||'—')+'</div></div>';

  var expenses = selInv.expenses || [];
  var expRows = expenses.length
    ? expenses.map(function(e){return '<div class="rb-row"><span class="rb-key">'+e.cat+'</span><span class="rb-val">AED '+Number(e.amt||0).toLocaleString()+'</span></div>';}).join('')
    : '<div class="rb-row"><span class="rb-key">No expenses logged</span><span class="rb-val">—</span></div>';

  document.getElementById('inv-resources').innerHTML =
    '<div class="resource-block"><div class="rb-label"><i class="bi bi-clock"></i>Time on Site</div>'+
    '<div class="rb-row"><span class="rb-key">Punch In</span><span class="rb-val">'+(selInv.punchIn||'—')+'</span></div>'+
    '<div class="rb-row"><span class="rb-key">Punch Out</span><span class="rb-val">'+(selInv.punchOut||'—')+'</span></div>'+
    '<div class="rb-row"><span class="rb-key">Duration</span><span class="rb-total">'+(selInv.duration||'—')+'</span></div></div>'+
    '<div class="resource-block"><div class="rb-label"><i class="bi bi-receipt"></i>Material Expenses</div>'+expRows+
    '<div class="rb-row"><span class="rb-key">Total</span><span class="rb-total">AED '+Number(selInv.totalExp||0).toLocaleString()+'</span></div></div>';
}

/* ---------- PENDING HOP ---------- */
function renderPH(list){
  list = list || PH_FILTERED;
  var tbody = document.getElementById('ph-tbody');
  document.getElementById('ph-count').textContent = list.length;
  var ph = document.getElementById('stat-ph');
  if(ph) ph.textContent = PENDING_HOP.length;   // KPI = total
  if(!list.length){
    tbody.innerHTML = '<tr><td colspan="7"><div class="pa-empty"><i class="bi bi-inbox"></i><p>No invoices pending HoP approval</p></div></td></tr>';
    return;
  }
  tbody.innerHTML = list.map(function(item){
  var actionCell = CAN_HOP_APPROVE
    ? '<button class="btn-mark btn-mark-blue" data-id="'+item.id+'" data-sr="'+item.sr+'" onclick="openHopModal(this.dataset.id,this.dataset.sr)"><i class="bi bi-patch-check-fill"></i>Mark HoP Approved &amp; Close</button>'
    : '<span style="font-size:.72rem;color:var(--text-muted);"><i class="bi bi-lock" style="margin-right:4px;"></i>No access</span>';

  return '<tr>'+
    '<td class="mono"><span class="sr-ref-trigger" data-sr-id="'+item.dbId+'" onclick="openSrTracking('+item.dbId+');">'+item.sr+'</span></td>'+
    '<td style="font-weight:500;">'+item.client+'</td>'+
    '<td class="muted">'+item.site+'</td>'+
    '<td><span style="font-size:.77rem;font-weight:600;color:#9a8053;background:rgba(154,128,83,.08);padding:2px 7px;border-radius:4px;">'+item.code+'</span></td>'+
    '<td class="muted">'+item.submitted+'</td>'+
    '<td><span style="font-size:.75rem;color:#9a8053;display:inline-flex;align-items:center;gap:4px;"><i class="bi bi-clock"></i>'+item.waiting+'</span></td>'+
    '<td style="text-align:center;">'+actionCell+'</td>'+
  '</tr>';
}).join('');
}

/* ---------- FILTERS ---------- */
function invFilter(){
  var q     = document.getElementById('inv-search').value.trim().toLowerCase();
  var from  = document.getElementById('inv-date').value;          // '' or 'YYYY-MM-DD'
  var toEl  = document.getElementById('inv-date-to');             // optional second input
  var to    = toEl ? toEl.value : '';

  function match(row){
    if (from && (!row.createdAt || row.createdAt < from)) return false;
    if (to   && (!row.createdAt || row.createdAt > to))   return false;
    if (q) {
      var hay = [row.id, row.sr, row.client, row.site, row.code, row.technician]
        .filter(Boolean).join(' ').toLowerCase();
      if (hay.indexOf(q) === -1) return false;
    }
    return true;
  }

  INV_FILTERED = INV_QUEUE.filter(match);
  PH_FILTERED  = PENDING_HOP.filter(match);
  renderInvQueue(INV_FILTERED);
  renderPH(PH_FILTERED);
}

function invResetFilters(){
  document.getElementById('inv-search').value = '';
  document.getElementById('inv-date').value = '';
  var toEl = document.getElementById('inv-date-to');
  if(toEl) toEl.value = '';
  INV_FILTERED = INV_QUEUE.slice();
  PH_FILTERED  = PENDING_HOP.slice();
  renderInvQueue(INV_FILTERED);
  renderPH(PH_FILTERED);
}

/* ---------- SUBMIT ---------- */
function openInvConfirm(){
  document.getElementById('inv-conf-sr').textContent = selInv ? selInv.id : '';
  document.getElementById('inv-conf-modal').classList.add('show');
}

function execInvSubmit(){
  document.getElementById('inv-conf-modal').classList.remove('show');
  var sr = selInv;
  if(!sr) return;
  var code = document.getElementById('inv-code').value.trim().toUpperCase();
  var total = document.getElementById('inv-total').value.trim();   // ← read the field

  var btn  = document.getElementById('inv-btn');
  btn.disabled = true;

  var fd = new FormData();
  fd.append('invoice_code',  code);
  // fd.append('invoice_total', sr.totalExp || 0);
  fd.append('invoice_total', total);                                // ← send it

  fd.append('invoice_pdf',   document.getElementById('inv-fi').files[0]);

  fetch('/invoice_panel/'+sr.dbId+'/submit', {
    method:'POST',
    headers:{'X-CSRF-TOKEN': CSRF, 'Accept':'application/json'},
    body: fd
  })
  .then(function(r){ return r.json().then(function(d){ if(!r.ok) throw new Error(d.message||'Server error'); return d; }); })
  .then(function(){
    var i = INV_QUEUE.findIndex(function(s){return s.id===sr.id;});
    if(i > -1) INV_QUEUE.splice(i,1);

    PENDING_HOP.push({
      id:'IA-'+sr.dbId, dbId:sr.dbId, sr:sr.id, client:sr.client, site:sr.site,
      code:code, submitted:'Just now', waiting:'0m',
      createdAt: sr.createdAt || new Date().toISOString().slice(0,10)
    });

    document.getElementById('inv-detail').style.display = 'none';
    document.getElementById('inv-success-title').textContent = sr.id+' — Invoice Submitted';
    document.getElementById('inv-success-body').textContent  = 'Invoice committed. Head of Projects notified. Ticket now appears in Pending HoP Approval below.';
    document.getElementById('inv-success').classList.add('show');
    selInv = null; inv_fileOk = false;
    invFilter();
    showToast('ok','Invoice Submitted',sr.id+' moved to Pending HoP Approval.');
  })
  .catch(function(e){
    btn.disabled = false;
    showToast('err','Upload Failed', e.message);
  });
}

function invNext(){document.getElementById('inv-success').classList.remove('show');document.getElementById('inv-empty').style.display='';}

/* ---------- HOP APPROVAL ---------- */
var pendingHopId = null;
function openHopModal(id,sr){pendingHopId=id;document.getElementById('hop-sr').textContent=sr;document.getElementById('hop-modal').classList.add('show');}

function execHopApproval(){
  var idx  = PENDING_HOP.findIndex(function(i){return i.id===pendingHopId;});
  var item = PENDING_HOP[idx];
  document.getElementById('hop-modal').classList.remove('show');
  if(!item) return;

  fetch('/invoice_panel/'+item.dbId+'/hop-approve', {
    method:'POST',
    headers:{'X-CSRF-TOKEN': CSRF, 'Accept':'application/json'}
  })
  .then(function(r){ return r.json().then(function(d){ if(!r.ok) throw new Error(d.message||'Server error'); return d; }); })
  .then(function(){
    PENDING_HOP.splice(idx,1);
    invFilter();
    showToast('ok','SR Closed',item.sr+' — Completed. WhatsApp summary sent to customer.');
  })
  .catch(function(e){ showToast('err','Approval Failed', e.message); });
}

/* ---------- INIT ---------- */
document.addEventListener('DOMContentLoaded', function(){
  ['inv-search','inv-date','inv-date-to'].forEach(function(id){
    var el = document.getElementById(id);
    if(!el) return;
    el.addEventListener(el.type === 'date' ? 'change' : 'input', invFilter);
  });
  renderInvQueue();
  renderPH();
});
</script>
@endpush