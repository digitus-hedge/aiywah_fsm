@extends('layouts.layout')

@section('title', 'Quotation Desk | Matter Mind')
@section('page_title', 'Quotation Desk')
@section('page_icon', 'database')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600;700&display=swap" rel="stylesheet"/>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet"/>
<style>
/* ===== Quotation Desk — scoped page styles ===== */
.qd-wrap{--gold:#9a8053;--gold-2:#b8975e;--queue-width:290px;}
.qd-wrap h4,.qd-wrap h5,.qd-wrap h6,.qd-wrap .pg-hdr-title,.qd-wrap .card-title,
.qd-wrap .ws-sr-id,.qd-wrap .qi-id,.qd-wrap .stat-num,.qd-wrap .pa-card-title{letter-spacing:-.01em;}

.qd-wrap .qi-id.sr-ref-trigger,
.qd-wrap .ws-sr-id.sr-ref-trigger,
.qd-wrap td.mono .sr-ref-trigger{ cursor:pointer; }

/* PAGE HEADER */
.qd-wrap .pg-header{background:linear-gradient(135deg,#9A7B4F 0%,#7A6140 100%);border-radius:10px;padding:20px 24px;margin-bottom:20px;color:#fff;position:relative;overflow:hidden;}
.qd-wrap .pg-header::before{content:'';position:absolute;left:-40px;bottom:-40px;width:180px;height:180px;border-radius:50%;background:rgba(255,255,255,.05);}
.qd-wrap .pg-header::after{content:'';position:absolute;right:-30px;top:-30px;width:160px;height:160px;border-radius:50%;background:rgba(255,255,255,.07);}
.qd-wrap .pg-hdr-title{font-size:1rem;font-weight:600;margin:0 0 3px;position:relative;z-index:1;}
.qd-wrap .pg-hdr-desc{font-size:.78rem;margin:0;opacity:.85;position:relative;z-index:1;}
.qd-wrap .pg-hdr-meta{display:flex;align-items:center;gap:8px;margin-top:9px;position:relative;z-index:1;flex-wrap:wrap;}
.qd-wrap .meta-badge{background:rgba(255,255,255,.18);border:1px solid rgba(255,255,255,.3);border-radius:20px;font-size:.6875rem;padding:2px 10px;font-weight:500;}

/* STATS */
.qd-wrap .stats-strip{display:grid;grid-template-columns:repeat(4,1fr);gap:10px;margin-bottom:18px;}
.qd-wrap .stat-card{background:var(--card-bg);border:1px solid var(--card-border);border-radius:9px;padding:13px 15px;display:flex;align-items:center;gap:12px;box-shadow:var(--card-shadow);}
.qd-wrap .stat-icon{width:38px;height:38px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:1rem;flex-shrink:0;}
.qd-wrap .stat-num{font-size:1.4rem;font-weight:700;line-height:1;color:var(--text-heading);}
.qd-wrap .stat-lbl{font-size:.7rem;color:var(--text-muted);}
@media(max-width:767px){.qd-wrap .stats-strip{grid-template-columns:repeat(2,1fr);}}
@media(max-width:420px){.qd-wrap .stats-strip{grid-template-columns:1fr;}}

/* FILTER BAR */
.qd-wrap .filter-bar{background:var(--card-bg);border:1px solid var(--card-border);border-radius:10px;padding:12px 16px;margin-bottom:16px;box-shadow:var(--card-shadow);display:flex;flex-wrap:wrap;gap:10px;align-items:flex-end;}
.qd-wrap .filter-group{display:flex;flex-direction:column;gap:4px;min-width: 0;flex: 1 1 auto;}
.qd-wrap .filter-label{font-size:.7rem;font-weight:600;color:var(--text-muted);text-transform:uppercase;letter-spacing:.05em;}
.qd-wrap .filter-control{height:34px;padding:0 10px;border:1px solid var(--border-color);border-radius:7px;background:var(--input-bg);color:var(--text-primary);font-size:.8rem;min-width:140px;transition:border-color .15s;}
.qd-wrap .filter-control:focus{outline:none;border-color:var(--gold);box-shadow:0 0 0 3px rgba(154,128,83,.12);}
.qd-wrap .filter-actions{margin-left:auto;display:flex;gap:8px;align-items:flex-end;}
@media(max-width:575.98px){
  .qd-wrap .filter-group{flex:1 1 100%;}
  .qd-wrap .filter-control{width:100%;min-width:0;}
  .qd-wrap .filter-actions{margin-left:0;width:100%;}
  .qd-wrap .filter-actions .btn-ghost{flex:1;justify-content:center;}
}

/* TWO-PANEL */
.qd-wrap .two-panel{display:grid;grid-template-columns:var(--queue-width) 1fr;gap:14px;align-items:start;}
@media(max-width:899px){.qd-wrap .two-panel{grid-template-columns:1fr;}}

/* QUEUE */
.qd-wrap .queue-panel{background:var(--card-bg);border:1px solid var(--card-border);border-radius:10px;box-shadow:var(--card-shadow);overflow:hidden;position:sticky;top:80px;}
@media(max-width:899px){.qd-wrap .queue-panel{position:static;}}
.qd-wrap .queue-hdr{padding:12px 16px;border-bottom:1px solid var(--border-color);display:flex;align-items:center;justify-content:space-between;}
.qd-wrap .queue-hdr-title{font-size:.8rem;font-weight:600;color:var(--text-heading);display:flex;align-items:center;gap:7px;}
.qd-wrap .q-count{font-size:.7rem;background:rgba(154,128,83,.12);color:var(--gold);padding:2px 8px;border-radius:9px;font-weight:700;}
.qd-wrap .queue-list{max-height:calc(100vh - 320px);overflow-y:auto;}
@media(max-width:899px){.qd-wrap .queue-list{max-height:none;}}
.qd-wrap .queue-list::-webkit-scrollbar{width:3px;}
.qd-wrap .queue-list::-webkit-scrollbar-thumb{background:var(--border-color);border-radius:2px;}
.qd-wrap .queue-item{padding:11px 15px;border-bottom:1px solid var(--border-color);cursor:pointer;transition:background .12s;}
.qd-wrap .queue-item:last-child{border-bottom:none;}
.qd-wrap .queue-item:hover{background:var(--surface-2);}
.qd-wrap .queue-item.active{background:rgba(154,128,83,.07);border-left:3px solid var(--gold);}
.qd-wrap .qi-id{font-size:.78rem;font-weight:700;color:var(--gold);margin-bottom:2px;}
.qd-wrap .qi-client{font-size:.79rem;font-weight:500;color:var(--text-heading);margin-bottom:1px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
.qd-wrap .qi-sub{font-size:.71rem;color:var(--text-muted);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;margin-bottom:5px;}
.qd-wrap .qi-foot{display:flex;align-items:center;justify-content:space-between;}
.qd-wrap .qi-time{font-size:.68rem;color:var(--text-muted);display:flex;align-items:center;gap:3px;}

/* WORKSPACE */
.qd-wrap .ws-panel{display:flex;flex-direction:column;gap:14px;}
.qd-wrap .ws-empty{background:var(--card-bg);border:1px solid var(--card-border);border-radius:10px;box-shadow:var(--card-shadow);padding:60px 24px;text-align:center;}
.qd-wrap .ws-empty-icon{width:56px;height:56px;border-radius:14px;background:var(--surface-2);display:flex;align-items:center;justify-content:center;margin:0 auto 14px;font-size:1.5rem;color:var(--text-light);}
.qd-wrap .ws-empty h6{font-size:.9rem;font-weight:600;color:var(--text-muted);margin-bottom:4px;}
.qd-wrap .ws-empty p{font-size:.78rem;color:var(--text-light);margin:0;}
.qd-wrap .ws-card{background:var(--card-bg);border:1px solid var(--card-border);border-radius:10px;box-shadow:var(--card-shadow);overflow:hidden;}
.qd-wrap .ws-card-hdr{padding:13px 18px;border-bottom:1px solid var(--border-color);display:flex;align-items:center;gap:10px;}
.qd-wrap .ws-card-icon{width:30px;height:30px;border-radius:7px;display:flex;align-items:center;justify-content:center;font-size:.85rem;flex-shrink:0;}
.qd-wrap .ws-card-hdr h6{font-size:.85rem;font-weight:600;color:var(--text-heading);margin:0;}
.qd-wrap .ws-card-body{padding:18px;}

/* SR header in workspace */
.qd-wrap .sr-hdr-card{background:var(--card-bg);border:1px solid var(--card-border);border-radius:10px;box-shadow:var(--card-shadow);padding:16px 20px;}
.qd-wrap .ws-sr-id{font-size:1rem;font-weight:700;color:var(--gold);margin-bottom:2px;}
.qd-wrap .ws-sr-client{font-size:.9rem;font-weight:600;color:var(--text-heading);margin-bottom:2px;}
.qd-wrap .ws-sr-site{font-size:.8rem;color:var(--text-muted);}
.qd-wrap .meta-chips{display:flex;gap:8px;flex-wrap:wrap;margin-top:12px;}
.qd-wrap .meta-chip{background:var(--surface-2);border-radius:7px;padding:7px 12px;}
.qd-wrap .meta-chip-label{font-size:.67rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--text-muted);margin-bottom:2px;}
.qd-wrap .meta-chip-value{font-size:.79rem;font-weight:600;color:var(--text-heading);}

/* FORM */
.qd-wrap .form-label-sm{font-size:.78rem;font-weight:600;color:var(--text-heading);margin-bottom:5px;display:block;}
.qd-wrap .form-label-sm .req{color:#ef4444;margin-left:2px;}
.qd-wrap .form-group{margin-bottom:14px;}
.qd-wrap .form-group:last-child{margin-bottom:0;}
.qd-wrap .fc{width:100%;padding:8px 11px;border:1px solid var(--border-color);border-radius:7px;background:var(--input-bg);color:var(--text-primary);font-size:.8125rem;transition:border-color .15s,box-shadow .15s;}
.qd-wrap .fc:focus{outline:none;border-color:var(--gold);box-shadow:0 0 0 3px rgba(154,128,83,.12);}
.qd-wrap .field-hint{font-size:.72rem;color:var(--text-muted);margin-top:4px;}

/* DROPZONE */
.qd-wrap .dropzone{border:2px dashed var(--border-color);border-radius:10px;background:var(--dz-bg,#f7f9fd);padding:26px 20px;text-align:center;cursor:pointer;transition:all .2s;position:relative;}
.qd-wrap .dropzone:hover,.qd-wrap .dropzone.dragover{border-color:var(--gold);background:rgba(154,128,83,.04);}
.qd-wrap .dropzone.has-file{border-color:#15803d;background:rgba(21,128,61,.04);border-style:solid;}
.qd-wrap .dropzone.dz-err{border-color:#ef4444;background:rgba(239,68,68,.04);}
.qd-wrap .dz-icon{font-size:2rem;color:var(--text-light);margin-bottom:8px;display:block;transition:color .2s;}
.qd-wrap .dropzone:hover .dz-icon,.qd-wrap .dropzone.dragover .dz-icon{color:var(--gold);}
.qd-wrap .dropzone.has-file .dz-icon{color:#15803d;}
.qd-wrap .dropzone.dz-err .dz-icon{color:#ef4444;}
.qd-wrap .dz-title{font-size:.82rem;font-weight:500;color:var(--text-heading);margin-bottom:3px;}
.qd-wrap .dz-sub{font-size:.75rem;color:var(--text-muted);}
.qd-wrap .dz-file-row{display:none;align-items:center;gap:10px;justify-content:center;margin-top:10px;}
.qd-wrap .dz-file-row.show{display:flex;}
.qd-wrap .dz-file-ico{width:32px;height:32px;background:rgba(21,128,61,.1);border-radius:7px;display:flex;align-items:center;justify-content:center;color:#15803d;font-size:.9rem;flex-shrink:0;}
.qd-wrap .dz-fname{font-size:.8rem;font-weight:500;color:#15803d;text-align:left;word-break:break-all;}
.qd-wrap .dz-fsize{font-size:.72rem;color:var(--text-muted);}
.qd-wrap .dz-rm{background:none;border:none;color:var(--text-muted);cursor:pointer;font-size:.85rem;padding:2px;border-radius:4px;line-height:1;}
.qd-wrap .dz-rm:hover{color:#ef4444;}
.qd-wrap .dz-err-msg{display:none;font-size:.75rem;color:#ef4444;margin-top:8px;font-weight:500;}
.qd-wrap .dz-err-msg.show{display:block;}
.qd-wrap .dz-input{position:absolute;inset:0;opacity:0;cursor:pointer;width:100%;height:100%;}

/* TABS */
.qd-wrap .qd-tabs{display:flex;gap:6px;margin:22px 0 12px;border-bottom:1px solid var(--border-color);flex-wrap:wrap;}
.qd-wrap .qd-tab{display:inline-flex;align-items:center;gap:7px;padding:9px 16px;background:none;border:none;border-bottom:2px solid transparent;font-size:.8rem;font-weight:600;color:var(--text-muted);cursor:pointer;margin-bottom:-1px;transition:color .15s,border-color .15s;}
.qd-wrap .qd-tab:hover{color:var(--text-heading);}
.qd-wrap .qd-tab.active{color:var(--gold);border-bottom-color:var(--gold);}
.qd-wrap .qd-tab-count{font-size:.65rem;padding:1px 7px;border-radius:9px;font-weight:700;background:var(--surface-2);color:var(--text-muted);}
.qd-wrap .qd-tab.active .qd-tab-count{background:rgba(154,128,83,.12);color:var(--gold);}
.qd-wrap .qd-pane{display:none;}
.qd-wrap .qd-pane.active{display:block;}
.qd-wrap .sb-rej{background:rgba(239,68,68,.1);color:#ef4444;}
.qd-modal-overlay .btn-reject{flex:1;padding:9px;border:1px solid rgba(239,68,68,.3);background:rgba(239,68,68,.08);color:#ef4444;border-radius:7px;font-size:.82rem;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;gap:6px;}
.qd-modal-overlay .btn-reject:hover{background:rgba(239,68,68,.15);}

/* BUTTONS */
.qd-wrap .btn-gold{display:inline-flex;align-items:center;gap:6px;padding:7px 14px;background:linear-gradient(135deg,var(--gold),var(--gold-2));color:#fff;border:none;border-radius:7px;font-size:.8rem;font-weight:500;cursor:pointer;white-space:nowrap;}
.qd-wrap .btn-gold:hover{opacity:.87;}
.qd-wrap .btn-ghost{display:inline-flex;align-items:center;gap:6px;padding:7px 12px;background:var(--surface-2);color:var(--text-muted);border:1px solid var(--border-color);border-radius:7px;font-size:.8rem;cursor:pointer;}
.qd-wrap .btn-ghost:hover{background:var(--surface-3);}
.qd-wrap .btn-submit{width:100%;padding:12px;border:none;border-radius:8px;font-size:.875rem;font-weight:600;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px;transition:opacity .15s;margin-top:4px;}
.qd-wrap .btn-submit:hover{opacity:.88;}
.qd-wrap .btn-submit:disabled{opacity:.38;cursor:not-allowed;}
.qd-wrap .btn-amber{background:linear-gradient(135deg,#b45309,#d97706);color:#fff;}

/* PENDING APPROVAL SECTION */
.qd-wrap .section-divider{display:flex;align-items:center;gap:12px;margin:22px 0 14px;}
.qd-wrap .section-divider-line{flex:1;height:1px;background:var(--border-color);}
.qd-wrap .section-label{display:flex;align-items:center;gap:8px;font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--text-muted);white-space:nowrap;}
.qd-wrap .section-count{font-size:.65rem;padding:1px 7px;border-radius:9px;font-weight:700;}

/* APPROVAL TABLE */
.qd-wrap .pa-card{background:var(--card-bg);border:1px solid var(--card-border);border-radius:10px;box-shadow:var(--card-shadow);overflow:hidden;}
.qd-wrap .pa-card-hdr{display:flex;align-items:center;justify-content:space-between;padding:13px 18px;border-bottom:1px solid var(--border-color);flex-wrap:wrap;gap:8px;}
.qd-wrap .pa-card-hdr-left{display:flex;align-items:center;gap:10px;}
.qd-wrap .pa-card-icon{width:30px;height:30px;border-radius:7px;display:flex;align-items:center;justify-content:center;font-size:.85rem;flex-shrink:0;}
.qd-wrap .pa-card-title{font-size:.85rem;font-weight:600;color:var(--text-heading);}
.qd-wrap .pa-card-sub{font-size:.72rem;color:var(--text-muted);margin-top:1px;}
.qd-wrap table.pa-tbl{width:100%;border-collapse:collapse;min-width:740px;}
.qd-wrap table.pa-tbl thead th{padding:9px 14px;font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--text-muted);background:var(--surface-2);border-bottom:1px solid var(--border-color);white-space:nowrap;text-align:left;}
.qd-wrap table.pa-tbl tbody tr{border-bottom:1px solid var(--border-color);transition:background .1s;}
.qd-wrap table.pa-tbl tbody tr:last-child{border-bottom:none;}
.qd-wrap table.pa-tbl tbody tr:hover{background:var(--table-hover);}
.qd-wrap table.pa-tbl td{padding:11px 14px;font-size:.8rem;color:var(--text-primary);vertical-align:middle;}
.qd-wrap table.pa-tbl td.mono{font-size:.77rem;font-weight:600;color:var(--gold);}
.qd-wrap table.pa-tbl td.muted{color:var(--text-muted);font-size:.78rem;}
.qd-wrap .pa-empty{padding:30px;text-align:center;font-size:.8rem;color:var(--text-muted);}
.qd-wrap .pa-empty i{display:block;font-size:1.4rem;color:var(--text-light);margin-bottom:7px;}
.qd-wrap .pa-scroll{overflow-x:auto;-webkit-overflow-scrolling:touch;}

/* APPROVE BUTTON */
.qd-wrap .btn-mark{display:inline-flex;align-items:center;gap:5px;padding:5px 11px;border-radius:6px;font-size:.76rem;font-weight:600;cursor:pointer;border:1px solid;transition:opacity .15s;white-space:nowrap;}
.qd-wrap .btn-mark:hover{opacity:.82;}
.qd-wrap .btn-mark-green{background:rgba(154,128,83,.1);color:var(--gold);border-color:rgba(154,128,83,.28);}

/* BADGES */
.qd-wrap .sbadge{display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:20px;font-size:.7rem;font-weight:600;}
.qd-wrap .sb-oow{background:rgba(239,68,68,.1);color:#ef4444;}
.qd-wrap .sb-pq{background:rgba(139,92,246,.1);color:#7c3aed;}

/* SUCCESS */
.qd-wrap .ws-success{background:var(--card-bg);border:1px solid var(--card-border);border-radius:10px;box-shadow:var(--card-shadow);padding:48px 24px;text-align:center;display:none;}
.qd-wrap .ws-success.show{display:block;}
.qd-wrap .s-icon{width:60px;height:60px;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 14px;font-size:1.6rem;}

/* MODAL */
.qd-modal-overlay{display:none;position:fixed;inset:0;background:var(--overlay-bg,rgba(9,15,35,.62));z-index:900;align-items:center;justify-content:center;backdrop-filter:blur(4px);padding:20px;}
.qd-modal-overlay.show{display:flex;}
.qd-modal-overlay .modal-box{background:var(--modal-bg,#fff);border-radius:12px;width:100%;max-width:440px;box-shadow:var(--modal-shadow,0 24px 64px rgba(0,0,0,.16));border:1px solid var(--card-border);overflow:hidden;animation:qdMIn .18s ease;}
@keyframes qdMIn{from{opacity:0;transform:scale(.96);}to{opacity:1;transform:scale(1);}}
.qd-modal-overlay .modal-hdr{display:flex;align-items:center;justify-content:space-between;padding:15px 20px;border-bottom:1px solid var(--border-color);}
.qd-modal-overlay .modal-hdr-left{display:flex;align-items:center;gap:10px;}
.qd-modal-overlay .modal-hdr-icon{width:32px;height:32px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:.9rem;flex-shrink:0;}
.qd-modal-overlay .modal-hdr h6{font-size:.9rem;font-weight:600;color:var(--text-heading);margin:0;}
.qd-modal-overlay .modal-close{background:none;border:none;color:var(--text-muted);cursor:pointer;padding:4px;border-radius:5px;font-size:1rem;line-height:1;}
.qd-modal-overlay .modal-close:hover{background:var(--surface-2);}
.qd-modal-overlay .modal-body{padding:20px;}
.qd-modal-overlay .modal-foot{display:flex;gap:10px;padding:14px 20px;border-top:1px solid var(--border-color);background:var(--surface-2);}
.qd-modal-overlay .btn-confirm{flex:1;padding:9px;border:none;border-radius:7px;font-size:.82rem;font-weight:600;cursor:pointer;color:#fff;}
.qd-modal-overlay .btn-cancel{flex:1;padding:9px;background:var(--surface-2);color:var(--text-muted);border:1px solid var(--border-color);border-radius:7px;font-size:.82rem;cursor:pointer;}

/* TOAST */
.qd-toast-wrap{position:fixed;bottom:22px;right:22px;z-index:9999;display:flex;flex-direction:column;gap:8px;pointer-events:none;max-width:calc(100vw - 44px);}
.qd-toast-wrap .toast-item{display:flex;align-items:flex-start;gap:10px;padding:12px 15px;border-radius:9px;background:var(--card-bg);border:1px solid var(--card-border);box-shadow:0 6px 24px rgba(0,0,0,.14);min-width:240px;max-width:320px;animation:qdToastIn .2s ease;pointer-events:auto;}
@keyframes qdToastIn{from{opacity:0;transform:translateY(10px);}to{opacity:1;transform:translateY(0);}}
.qd-toast-wrap .t-ico{font-size:1rem;flex-shrink:0;margin-top:1px;}
.qd-toast-wrap .t-ico.ok{color:#15803d;}.qd-toast-wrap .t-ico.err{color:#ef4444;}
.qd-toast-wrap .t-ico.info{color:var(--gold);}.qd-toast-wrap .t-ico.warn{color:#d97706;}
.qd-toast-wrap .t-title{font-size:.8rem;font-weight:600;color:var(--text-heading);margin:0 0 2px;}
.qd-toast-wrap .t-body{font-size:.75rem;color:var(--text-muted);margin:0;}
@media(max-width:575.98px){.qd-toast-wrap{left:12px;right:12px;bottom:12px;}.qd-toast-wrap .toast-item{max-width:none;}}



</style>
@endpush

@section('content')
<div class="qd-wrap">

  {{-- PAGE HEADER --}}
  <div class="pg-header">
    <h4 class="pg-hdr-title"><i class="bi bi-file-earmark-text me-2"></i>Out-of-Warranty Quotation Desk</h4>
    <p class="pg-hdr-desc">Upload ERP quote references and PDF quotations for out-of-warranty SRs. Track customer approval status before routing to operations.</p>
    <div class="pg-hdr-meta">
      <span class="meta-badge"><i class="bi bi-shield-fill-check me-1"></i>Super Admin</span>
      <span class="meta-badge"><i class="bi bi-person-gear me-1"></i>Admin</span>
      <span class="meta-badge"><i class="bi bi-calculator me-1"></i>Accounts / AR</span>
    </div>
  </div>

  {{-- STATS --}}
  <div class="stats-strip">
    <div class="stat-card"><div class="stat-icon" style="background:rgba(139,92,246,.1);"><i class="bi bi-hourglass-split" style="color:#7c3aed;"></i></div><div><div class="stat-num" id="stat-pq">0</div><div class="stat-lbl">Pending Quote Upload</div></div></div>
    <div class="stat-card"><div class="stat-icon" style="background:rgba(245,158,11,.1);"><i class="bi bi-clock-history" style="color:#d97706;"></i></div><div><div class="stat-num" id="stat-pa">0</div><div class="stat-lbl">Pending Client Approval</div></div></div>
    <div class="stat-card"><div class="stat-icon" style="background:rgba(21,128,61,.1);"><i class="bi bi-check-circle" style="color:#15803d;"></i></div><div><div class="stat-num">{{ $clientApproved ?? 0 }}</div><div class="stat-lbl">Client Approved</div></div></div>
    <div class="stat-card"><div class="stat-icon" style="background:rgba(239,68,68,.1);"><i class="bi bi-x-circle" style="color:#ef4444;"></i></div><div><div class="stat-num">{{ $quoteRejected ?? 0 }}</div><div class="stat-lbl">Quote Rejected</div></div></div>
  </div>

  {{-- FILTER BAR --}}
  <div class="filter-bar">
    <div class="filter-group"><div class="filter-label">Search</div><input class="filter-control" type="text" id="q-search" placeholder="SR ID, customer name…"/></div>
    <div class="filter-group"><div class="filter-label">Date From</div><input class="filter-control" type="date" id="q-date-from"/></div>
    <div class="filter-group"><div class="filter-label">Date To</div><input class="filter-control" type="date" id="q-date-to"/></div>
    <div class="filter-actions"><button class="btn-ghost" onclick="qdResetFilters()"><i class="bi bi-x-circle"></i>Reset</button></div>
  </div>

  {{-- TWO-PANEL --}}
  <div class="two-panel">
    <div class="queue-panel">
      <div class="queue-hdr">
        <div class="queue-hdr-title"><i class="bi bi-list-ul" style="color:#9a8053;"></i>OoW Queue</div>
        <span class="q-count" id="q-count">0</span>
      </div>
      <div class="queue-list" id="q-list"></div>
    </div>
    <div class="ws-panel">
      <div class="ws-empty" id="q-empty">
        <div class="ws-empty-icon"><i class="bi bi-file-earmark-text"></i></div>
        <h6 style="font-family:unset;">Select a Ticket</h6>
        <p>Choose a pending OoW SR from the queue to begin the quotation upload process.</p>
      </div>
      <div class="ws-success" id="q-success">
        <div class="s-icon" style="background:rgba(154,128,83,.1);"><i class="bi bi-file-earmark-check" style="color:#9a8053;font-size:1.6rem;"></i></div>
        <h5 style="font-size:1rem;color:var(--text-heading);margin-bottom:6px;" id="q-success-title"></h5>
        <p style="font-size:.82rem;color:var(--text-muted);margin-bottom:18px;" id="q-success-body"></p>
        <button onclick="qdNext()" class="btn-gold"><i class="bi bi-arrow-right"></i>Next Ticket</button>
      </div>
      <div id="q-detail" style="display:none;flex-direction:column;gap:14px;">
        <div class="sr-hdr-card">
          <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:12px;margin-bottom:12px;flex-wrap:wrap;">
            <div><div class="ws-sr-id" id="q-sr-id"></div><div class="ws-sr-client" id="q-sr-client"></div><div class="ws-sr-site" id="q-sr-site"></div></div>
            <div style="display:flex;flex-direction:column;align-items:flex-end;gap:6px;">
              <span class="sbadge sb-pq"><i class="bi bi-circle-fill" style="font-size:.4rem;"></i>Pending Quote</span>
              <span class="sbadge sb-oow"><i class="bi bi-shield-exclamation"></i>Out of Warranty</span>
            </div>
          </div>
          <div class="meta-chips" id="q-chips"></div>
        </div>

        @if (auth()->user()?->role?->code !== 'HP')

        <div class="ws-card">
          <div class="ws-card-hdr">
            <div class="ws-card-icon" style="background:rgba(154,128,83,.1);"><i class="bi bi-tag" style="color:#9a8053;"></i></div>
            <h6>Quotation Upload</h6>
          </div>
          <div class="ws-card-body">
            <div class="form-group">
              <label class="form-label-sm">ERP Quotation Reference Token <span class="req">*</span></label>
              <input type="text" class="fc" id="q-ref" placeholder="e.g. QT-2025-ERP-00441" oninput="q_validate()" style="text-transform:uppercase;letter-spacing:.03em;"/>
              <div class="field-hint">Alphanumeric ERP reference — links the PDF to the external quotation record.</div>
            </div>
            <div class="form-group">
              <label class="form-label-sm">Quotation Package PDF <span class="req">*</span></label>
              <div class="dropzone" id="q-dz" onclick="dz_click('q-fi')"
                ondragover="dz_dragover(event,'q-dz')" ondragleave="dz_dragleave('q-dz')"
                ondrop="dz_drop(event,'q-dz','q-fi','q')">
                <input type="file" class="dz-input" id="q-fi" accept=".pdf" onchange="dz_change(event,'q-dz','q')"/>
                <i class="bi bi-cloud-upload dz-icon" id="q-dz-icon"></i>
                <div class="dz-title" id="q-dz-title">Drag &amp; Drop PDF here or click to browse</div>
                <div class="dz-sub" id="q-dz-sub">Accepted: PDF only · Max 25MB</div>
                <div class="dz-file-row" id="q-frow">
                  <div class="dz-file-ico"><i class="bi bi-file-earmark-pdf-fill"></i></div>
                  <div><div class="dz-fname" id="q-fname"></div><div class="dz-fsize" id="q-fsize"></div></div>
                  <button class="dz-rm" onclick="event.stopPropagation();dz_remove('q-dz','q-fi','q','Drag &amp; Drop PDF here or click to browse')" title="Remove"><i class="bi bi-x-circle"></i></button>
                </div>
                <div class="dz-err-msg" id="q-dz-err">Only PDF files are accepted.</div>
              </div>
            </div>

            
            <div id="q-val-msg" style="display:none;padding:8px 12px;border-radius:7px;background:rgba(239,68,68,.07);border:1px solid rgba(239,68,68,.2);color:#ef4444;font-size:.78rem;margin-bottom:10px;"></div>
           
            <button class="btn-submit btn-amber" id="q-btn" onclick="submitQuote()" disabled>
              <i class="bi bi-send-check-fill"></i>Upload Quote &amp; Forward to Customer
            </button>
          </div>
        </div>
        @endif
      </div>
    </div>
  </div>

  {{-- TABS: PENDING / REJECTED --}}
  <div class="qd-tabs">
    <button class="qd-tab active" id="tab-pending" onclick="qdTab('pending')">
      <i class="bi bi-clock-history"></i>Pending Customer Approval
      <span class="qd-tab-count" id="pa-count">0</span>
    </button>
    <button class="qd-tab" id="tab-rejected" onclick="qdTab('rejected')">
      <i class="bi bi-x-circle"></i>Quotation Rejected
      <span class="qd-tab-count" id="rj-count">0</span>
    </button>
  </div>

  {{-- PANE: PENDING --}}
  <div class="qd-pane active" id="pane-pending">
    <div class="pa-card">
      <div class="pa-card-hdr">
        <div class="pa-card-hdr-left">
          <div class="pa-card-icon" style="background:rgba(245,158,11,.1);"><i class="bi bi-hourglass-split" style="color:#d97706;"></i></div>
          <div><div class="pa-card-title">Awaiting Customer Response</div><div class="pa-card-sub">Quote sent to customer — mark approved or rejected when customer responds</div></div>
        </div>
        <div style="display:flex;align-items:center;gap:6px;font-size:.75rem;color:var(--text-muted);">
          <i class="bi bi-whatsapp" style="color:#25d366;"></i>Customer notified via WhatsApp on submission
        </div>
      </div>
      <div class="pa-scroll">
        <table class="pa-tbl">
          <thead>
            <tr><th>SR ID</th><th>Customer</th><th>Site</th><th>ERP Quote Ref</th><th>Quote Submitted</th><th>Waiting</th><th style="text-align:center;width:160px;">Action</th></tr>
          </thead>
          <tbody id="pa-tbody"></tbody>
        </table>
      </div>
    </div>
  </div>

  {{-- PANE: REJECTED --}}
  <div class="qd-pane" id="pane-rejected">
    <div class="pa-card">
      <div class="pa-card-hdr">
        <div class="pa-card-hdr-left">
          <div class="pa-card-icon" style="background:rgba(239,68,68,.1);"><i class="bi bi-x-circle" style="color:#ef4444;"></i></div>
          <div><div class="pa-card-title">Quotations Rejected by Customer</div><div class="pa-card-sub">Quotes the customer declined — revise and re-submit if required</div></div>
        </div>
      </div>
      <div class="pa-scroll">
        <table class="pa-tbl">
          <thead>
            <tr><th>SR ID</th><th>Customer</th><th>Site</th><th>ERP Quote Ref</th><th>Rejected On</th><th>Since</th><th style="text-align:center;width:120px;">Status</th></tr>
          </thead>
          <tbody id="rj-tbody"></tbody>
        </table>
      </div>
    </div>
  </div>
  {{-- APPROVE MODAL --}}
  <div class="qd-modal-overlay" id="qa-modal" onclick="if(event.target===this)this.classList.remove('show')">
    <div class="modal-box">
      <div class="modal-hdr">
        <div class="modal-hdr-left">
          <div class="modal-hdr-icon" style="background:rgba(154,128,83,.1);"><i class="bi bi-check-circle-fill" style="color:#9a8053;"></i></div>
          <h6>Mark Quote as customer Approved</h6>
        </div>
        <button class="modal-close" onclick="document.getElementById('qa-modal').classList.remove('show')"><i class="bi bi-x-lg"></i></button>
      </div>
      <div class="modal-body">
        <p style="font-size:.8rem;color:var(--text-muted);margin-bottom:14px;">Confirm customer has approved the quote for <strong id="qa-sr" style="color:var(--text-heading);"></strong>.</p>
       <div style="padding:12px 14px;border-radius:8px;background:rgba(154,128,83,.07);border:1px solid rgba(154,128,83,.2);display:flex;align-items:flex-start;gap:10px;margin-bottom:12px;">
          <i class="bi bi-arrow-right-circle-fill" style="color:#9a8053;flex-shrink:0;margin-top:2px;"></i>
          <div style="font-size:.8rem;color:#9a8053;">
            <strong>Status: Quoted → Approved</strong><br/>
            <span style="opacity:.8;font-size:.76rem;">SR becomes available in Head of Projects dispatch queue for technician assignment.</span>
          </div>
        </div>
        <div style="padding:9px 12px;background:rgba(154,128,83,.07);border:1px solid rgba(154,128,83,.2);border-radius:7px;font-size:.78rem;color:#9a8053;display:flex;align-items:center;gap:8px;">
          <i class="bi bi-whatsapp" style="color:#9a8053;"></i>WhatsApp confirmation dispatched to client stakeholders.
        </div>
      </div>
     <div class="modal-foot">
        <button class="btn-reject" onclick="execQRejection()"><i class="bi bi-x-circle"></i> Quotation Rejected</button>
        <button class="btn-confirm" style="background:linear-gradient(135deg,#9A7B4F,#7A6140);" onclick="execQApproval()"><i class="bi bi-check-lg"></i> Confirm Approval</button>
      </div>
    </div>
  </div>

  <div class="qd-toast-wrap" id="qdToastWrap"></div>
</div>
@include('partials.sr_tracking_modal')
@endsection

@push('scripts')
<script>
/* =========================================================
   Quotation Desk — page scripts
   Q_QUEUE row shape:
   { id, dbId, client, site, logged, createdAt, issue }
   PENDING_APPROVAL row shape:
   { id, sr, dbId, client, site, ref, submitted, waiting, createdAt }
   ========================================================= */
var Q_QUEUE          = @json($qQueue ?? []);
var PENDING_APPROVAL = @json($pendingApproval ?? []);
var CSRF             = '{{ csrf_token() }}';
var USER_ROLE        = '{{ auth()->user()?->role?->code }}';   // ← add this
/* filtered views — what actually gets rendered */
var Q_FILTERED  = Q_QUEUE.slice();
var PA_FILTERED = PENDING_APPROVAL.slice();

var selQ = null;
var q_fileOk = false;

var REJECTED    = @json($rejectedQuotes ?? []);
var RJ_FILTERED = REJECTED.slice();
var QD_TAB      = 'pending';
/* ---------- TOAST ---------- */
function showToast(type,title,body){
  var w = document.getElementById('qdToastWrap');
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
  if(!dz) return;
  document.getElementById(prefix+'-dz-icon').className = 'bi bi-cloud-upload dz-icon';
  document.getElementById(prefix+'-dz-title').textContent = placeholder||'Drag & Drop PDF here or click to browse';
  document.getElementById(prefix+'-dz-sub').textContent = 'Accepted format: PDF only · Max 25MB';
  document.getElementById(prefix+'-frow').classList.remove('show');
  var inp = document.getElementById(inputId);if(inp)inp.value='';
  window[prefix+'_fileOk'] = false;
  if(typeof window[prefix+'_validate']==='function')window[prefix+'_validate']();
}

/* ---------- VALIDATION ---------- */
function q_validate(){
  var refEl = document.getElementById('q-ref');
  var btn   = document.getElementById('q-btn');
  if(!refEl || !btn) return;
  var ref = refEl.value.trim();
  var ok  = ref.length >= 3 && q_fileOk;
  btn.disabled = !ok; btn.style.opacity = ok ? '1' : '.38';
}
window.q_validate = q_validate;

/* ---------- QUEUE ---------- */
function renderQQueue(list){
  list = list || Q_FILTERED;
  var ul = document.getElementById('q-list');
  document.getElementById('q-count').textContent = list.length;
  document.getElementById('stat-pq').textContent = Q_QUEUE.length;   // KPI = total, not filtered
  if(!list.length){
    ul.innerHTML = '<div style="padding:28px;text-align:center;font-size:.8rem;color:var(--text-muted);"><i class="bi bi-inbox" style="display:block;font-size:1.6rem;margin-bottom:8px;color:var(--text-light);"></i>No matching tickets</div>';
    return;
  }
  ul.innerHTML = list.map(function(sr){
    var ac = selQ && selQ.id===sr.id ? ' active':'';
    return '<div class="queue-item'+ac+'" data-id="'+sr.id+'" onclick="selectQ(this.dataset.id)">'+
      '<div class="qi-id sr-ref-trigger" data-sr-id="'+sr.dbId+'" onclick="event.stopPropagation(); openSrTracking('+sr.dbId+');">'+sr.id+'</div>'+
      '<div class="qi-client">'+sr.client+'</div>'+
      '<div class="qi-sub"><i class="bi bi-geo-alt" style="font-size:.7rem;"></i> '+sr.site+'</div>'+
      '<div class="qi-foot">'+
        '<span class="sbadge sb-oow" style="font-size:.65rem;">OoW</span>'+
        '<span class="qi-time"><i class="bi bi-clock" style="font-size:.65rem;"></i>'+(sr.logged||'')+'</span>'+
      '</div></div>';
  }).join('');
}

function selectQ(id){
  selQ = Q_QUEUE.find(function(s){return s.id===id;});
  if(!selQ) return;
  q_fileOk = false;
  dz_remove('q-dz','q-fi','q','Drag & Drop PDF here or click to browse');

  var refEl = document.getElementById('q-ref');
  if (refEl) refEl.value = '';   // ← guard, q-ref doesn't exist for HP

  q_validate();
  renderQQueue();
  document.getElementById('q-empty').style.display   = 'none';
  document.getElementById('q-success').classList.remove('show');
  document.getElementById('q-detail').style.display = 'flex';
  var qSrIdEl = document.getElementById('q-sr-id');
qSrIdEl.textContent = selQ.id;
qSrIdEl.classList.add('sr-ref-trigger');
qSrIdEl.onclick = function(){ openSrTracking(selQ.dbId); };
  document.getElementById('q-sr-client').textContent = selQ.client;
  document.getElementById('q-sr-site').innerHTML     = '<i class="bi bi-geo-alt" style="color:#9a8053;font-size:.8rem;"></i> '+selQ.site;
  document.getElementById('q-chips').innerHTML =
    '<div class="meta-chip"><div class="meta-chip-label">Issue</div><div class="meta-chip-value" style="font-size:.78rem;font-weight:400;">'+(selQ.issue||'—')+'</div></div>'+
    '<div class="meta-chip"><div class="meta-chip-label">Logged</div><div class="meta-chip-value">'+(selQ.logged||'—')+'</div></div>'+
    '<div class="meta-chip"><div class="meta-chip-label">Scope</div><div class="meta-chip-value" style="color:#ef4444;">Out of Warranty</div></div>';
}

/* ---------- PENDING APPROVAL ---------- */
function renderPA(list){
  list = list || PA_FILTERED;
  var tbody = document.getElementById('pa-tbody');
  document.getElementById('pa-count').textContent = list.length;
  document.getElementById('stat-pa').textContent  = PENDING_APPROVAL.length;   // KPI = total
  if(!list.length){
    tbody.innerHTML = '<tr><td colspan="7"><div class="pa-empty"><i class="bi bi-inbox"></i><p>No quotes awaiting client approval</p></div></td></tr>';
    return;
  }
  var canDecide = USER_ROLE !== 'HP';   // ← guard
  tbody.innerHTML = list.map(function(item){
    var actionCell = canDecide
      ? '<button class="btn-mark btn-mark-green" data-id="'+item.id+'" data-sr="'+item.sr+'" onclick="openQAModal(this.dataset.id,this.dataset.sr)"><i class="bi bi-check-circle-fill"></i>Record Decision</button>'
      : '<span class="muted" style="font-size:.75rem;">—</span>';
    return '<tr>'+
      '<td class="mono"><span class="sr-ref-trigger" data-sr-id="'+item.dbId+'" onclick="openSrTracking('+item.dbId+');">'+item.sr+'</span></td>'+
      '<td style="font-weight:500;">'+item.client+'</td>'+
      '<td class="muted">'+item.site+'</td>'+
      '<td><span style="font-size:.77rem;font-weight:600;color:#9a8053;background:rgba(154,128,83,.08);padding:2px 7px;border-radius:4px;">'+item.ref+'</span></td>'+
      '<td class="muted">'+item.submitted+'</td>'+
      '<td><span style="font-size:.75rem;color:#d97706;display:inline-flex;align-items:center;gap:4px;"><i class="bi bi-clock"></i>'+item.waiting+'</span></td>'+
      '<td style="text-align:center;">'+actionCell+'</td>'+
    '</tr>';
  }).join('');
}

/* ---------- FILTERS ---------- */
function qdFilter(){
  var q    = document.getElementById('q-search').value.trim().toLowerCase();
  var from = document.getElementById('q-date-from').value;   // '' or 'YYYY-MM-DD'
  var to   = document.getElementById('q-date-to').value;

  function match(row){
    if (from && (!row.createdAt || row.createdAt < from)) return false;
    if (to   && (!row.createdAt || row.createdAt > to))   return false;
    if (q) {
      var hay = [row.id, row.sr, row.client, row.site, row.ref, row.issue]
        .filter(Boolean).join(' ').toLowerCase();
      if (hay.indexOf(q) === -1) return false;
    }
    return true;
  }

  Q_FILTERED  = Q_QUEUE.filter(match);
  PA_FILTERED = PENDING_APPROVAL.filter(match);
  RJ_FILTERED = REJECTED.filter(match);   
  renderRJ(RJ_FILTERED);
  renderQQueue(Q_FILTERED);
  renderPA(PA_FILTERED);
}

function qdResetFilters(){
  document.getElementById('q-search').value = '';
  document.getElementById('q-date-from').value = '';
  document.getElementById('q-date-to').value = '';
  Q_FILTERED  = Q_QUEUE.slice();
  PA_FILTERED = PENDING_APPROVAL.slice();
  RJ_FILTERED = REJECTED.slice();         
  renderRJ(RJ_FILTERED);
  renderQQueue(Q_FILTERED);
  renderPA(PA_FILTERED);
}

/* ---------- SUBMIT ---------- */
function submitQuote(){
  var ref = document.getElementById('q-ref').value.trim().toUpperCase();
  if(!ref || !q_fileOk){ showValMsg('q','Please fill the ERP Reference and attach a PDF.'); return; }
  var sr  = selQ;
  var btn = document.getElementById('q-btn');
  btn.disabled = true;

  var fd = new FormData();
  fd.append('erp_quote_ref', ref);
  fd.append('quote_pdf', document.getElementById('q-fi').files[0]);

  fetch('/quotation_desk/'+sr.dbId+'/quote', {
    method:'POST',
    headers:{'X-CSRF-TOKEN':CSRF,'Accept':'application/json'},
    body: fd
  })
  .then(function(r){ return r.json().then(function(d){ if(!r.ok) throw new Error(d.message||'Server error'); return d; }); })
  .then(function(){
    var i = Q_QUEUE.findIndex(function(s){return s.id===sr.id;});
    if(i > -1) Q_QUEUE.splice(i,1);

    PENDING_APPROVAL.push({
      id:'PA-'+sr.dbId, dbId:sr.dbId, sr:sr.id, client:sr.client, site:sr.site,
      ref:ref, submitted:'Just now', waiting:'0m',
      createdAt: sr.createdAt || new Date().toISOString().slice(0,10)
    });

    document.getElementById('q-detail').style.display = 'none';
    document.getElementById('q-success-title').textContent = sr.id+' — Quote Submitted';
    document.getElementById('q-success-body').textContent  = 'Quote PDF uploaded with ERP ref '+ref+'. Client notified via WhatsApp.';
    document.getElementById('q-success').classList.add('show');
    selQ = null; q_fileOk = false;
    showToast('ok','Quote Submitted',sr.id+' moved to Pending Client Approval.');
    qdFilter();
  })
  .catch(function(e){ btn.disabled = false; showToast('err','Upload Failed', e.message); });
}

/* ---------- APPROVAL ---------- */
var pendingQAId = null;
function openQAModal(id,sr){pendingQAId=id;document.getElementById('qa-sr').textContent=sr;document.getElementById('qa-modal').classList.add('show');}

function execQApproval(){
  var idx  = PENDING_APPROVAL.findIndex(function(i){return i.id===pendingQAId;});
  var item = PENDING_APPROVAL[idx];
  document.getElementById('qa-modal').classList.remove('show');
  if(!item) return;

  fetch('/quotation_desk/'+item.dbId+'/approve', {
    method:'POST',
    headers:{'X-CSRF-TOKEN':CSRF,'Accept':'application/json'}
  })
  .then(function(r){ return r.json().then(function(d){ if(!r.ok) throw new Error(d.message||'Server error'); return d; }); })
  .then(function(){
    PENDING_APPROVAL.splice(idx,1);
    qdFilter();
    showToast('ok','Client Approved',item.sr+' — status set to Approved.');
  })
  .catch(function(e){ showToast('err','Approval Failed', e.message); });
}

function execQRejection(){
  var idx  = PENDING_APPROVAL.findIndex(function(i){return i.id===pendingQAId;});
  var item = PENDING_APPROVAL[idx];
  document.getElementById('qa-modal').classList.remove('show');
  if(!item) return;

  fetch('/quotation_desk/'+item.dbId+'/reject', {
    method:'POST',
    headers:{'X-CSRF-TOKEN':CSRF,'Accept':'application/json'}
  })
  .then(function(r){ return r.json().then(function(d){ if(!r.ok) throw new Error(d.message||'Server error'); return d; }); })
  .then(function(){
    PENDING_APPROVAL.splice(idx,1);
    REJECTED.unshift({
      id:'QR-'+item.dbId, dbId:item.dbId, sr:item.sr, client:item.client, site:item.site,
      ref:item.ref, rejected:'Just now', ago:'0m',
      createdAt:item.createdAt || new Date().toISOString().slice(0,10)
    });
    qdFilter();
    qdTab('rejected');
    showToast('warn','Quotation Rejected',item.sr+' — status set to Quote Rejected.');
  })
  .catch(function(e){ showToast('err','Rejection Failed', e.message); });
}
function qdNext(){document.getElementById('q-success').classList.remove('show');document.getElementById('q-empty').style.display='';}

function showValMsg(prefix,msg){
  var el = document.getElementById(prefix+'-val-msg');
  el.textContent = msg; el.style.display = 'block';
  setTimeout(function(){el.style.display='none';},3500);
}

/* ---------- INIT ---------- */
document.addEventListener('DOMContentLoaded', function(){
  ['q-search','q-date-from','q-date-to'].forEach(function(id){
    var el = document.getElementById(id);
    if(!el) return;
    el.addEventListener(el.type === 'date' ? 'change' : 'input', qdFilter);
  });
  renderQQueue();
  renderPA();
  renderRJ();
});

function renderRJ(list){
  list = list || RJ_FILTERED;
  var tbody = document.getElementById('rj-tbody');
  document.getElementById('rj-count').textContent = list.length;
  if(!list.length){
    tbody.innerHTML = '<tr><td colspan="7"><div class="pa-empty"><i class="bi bi-inbox"></i><p>No rejected quotations</p></div></td></tr>';
    return;
  }
  tbody.innerHTML = list.map(function(item){
    return '<tr>'+
      '<td class="mono"><span class="sr-ref-trigger" data-sr-id="'+item.dbId+'" onclick="openSrTracking('+item.dbId+');">'+item.sr+'</span></td>'+
      '<td style="font-weight:500;">'+item.client+'</td>'+
      '<td class="muted">'+item.site+'</td>'+
      '<td><span style="font-size:.77rem;font-weight:600;color:#9a8053;background:rgba(154,128,83,.08);padding:2px 7px;border-radius:4px;">'+(item.ref||'—')+'</span></td>'+
      '<td class="muted">'+(item.rejected||'—')+'</td>'+
      '<td class="muted">'+(item.ago||'—')+'</td>'+
      '<td style="text-align:center;"><span class="sbadge sb-rej"><i class="bi bi-x-circle-fill" style="font-size:.65rem;"></i>Rejected</span></td>'+
    '</tr>';
  }).join('');
}

function qdTab(which){
  QD_TAB = which;
  document.getElementById('tab-pending').classList.toggle('active', which==='pending');
  document.getElementById('tab-rejected').classList.toggle('active', which==='rejected');
  document.getElementById('pane-pending').classList.toggle('active', which==='pending');
  document.getElementById('pane-rejected').classList.toggle('active', which==='rejected');
}
</script>
@endpush