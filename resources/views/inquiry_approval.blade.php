@extends('layouts.layout')

@section('title', 'Inquiry Approval — Digit-Us Portal')
@section('page_title', 'Inquiry Approval')
@section('page_icon', 'clipboard-check')




  @push('styles')
  <style>
    /* ═════

.main-content{margin-left:var(--sidebar-width);margin-top:60px;padding:22px 22px 40px;min-height:calc(100vh - 60px);transition:margin-left .28s;}
@media(max-width:991.98px){.main-content{margin-left:0;}}
@media(max-width:575.98px){.main-content{padding:14px 12px 40px;}}

/* ═══════════════════════════════════════
   PAGE HEADER
═══════════════════════════════════════ */
.pg-header{background:linear-gradient(135deg,#9A7B4F 0%,#7A6140 100%);border-radius:10px;padding:20px 24px;margin-bottom:20px;color:#fff;position:relative;overflow:hidden;}
.pg-header::before{content:'';position:absolute;left:-40px;bottom:-40px;width:180px;height:180px;border-radius:50%;background:rgba(255,255,255,.05);}
.pg-header::after{content:'';position:absolute;right:-30px;top:-30px;width:160px;height:160px;border-radius:50%;background:rgba(255,255,255,.07);}
.pg-header h4{font-size:1rem;font-weight:600;margin:0 0 3px;position:relative;z-index:1;}
.pg-header p{font-size:.78rem;margin:0;opacity:.85;position:relative;z-index:1;}
.pg-header .meta-row{display:flex;align-items:center;gap:8px;margin-top:10px;position:relative;z-index:1;flex-wrap:wrap;}
.meta-badge{background:rgba(255,255,255,.18);border:1px solid rgba(255,255,255,.3);border-radius:20px;font-size:.6875rem;padding:2px 10px;font-weight:500;}
@media(max-width:575.98px){.pg-header{padding:14px 16px;}.pg-header h4{font-size:.9rem;}}

/* ═══════════════════════════════════════
   STATS STRIP
═══════════════════════════════════════ */
.stats-strip{display:grid;grid-template-columns:repeat(4,1fr);gap:10px;margin-bottom:18px;}
.stat-card{background:var(--card-bg);border:1px solid var(--card-border);border-radius:8px;padding:14px 16px;display:flex;align-items:center;gap:12px;box-shadow:var(--card-shadow);}
.stat-icon{width:40px;height:40px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:1.1rem;flex-shrink:0;}
.stat-num{font-size:1.5rem;font-weight:700;line-height:1;}
.stat-lbl{font-size:.72rem;color:var(--text-muted);margin-top:2px;}
@media(max-width:767.98px){.stats-strip{grid-template-columns:repeat(2,1fr);}}
@media(max-width:399px){.stats-strip{grid-template-columns:1fr 1fr;gap:8px;}}

/* ═══════════════════════════════════════
   LAYOUT: TABLE + DETAIL PANEL
═══════════════════════════════════════ */
.workspace{display:grid;grid-template-columns:1fr 400px;gap:16px;align-items:start;}
@media(max-width:1199.98px){.workspace{grid-template-columns:1fr 360px;}}
@media(max-width:991.98px){.workspace{grid-template-columns:1fr;}}

/* ═══════════════════════════════════════
   FILTER BAR
═══════════════════════════════════════ */
.filter-bar{background:var(--card-bg);border:1px solid var(--card-border);border-radius:8px;padding:12px 14px;margin-bottom:12px;box-shadow:var(--card-shadow);display:flex;align-items:center;gap:10px;flex-wrap:wrap;}
.filter-search{position:relative;flex:1;min-width:180px;}
.filter-search i{position:absolute;left:10px;top:50%;transform:translateY(-50%);color:var(--text-muted);font-size:.85rem;}
.filter-search input{padding-left:32px;width:100%;}
.form-control-sm,.form-select-sm{font-size:.78rem;border:1px solid var(--border-color);border-radius:6px;padding:.35rem .7rem;color:var(--text-primary);background:var(--input-bg);height:34px;}
.form-control-sm:focus,.form-select-sm:focus{border-color:rgba(101,113,255,.5);box-shadow:0 0 0 3px rgba(101,113,255,.12);outline:none;}
.form-control-sm::placeholder{color:var(--text-light);}
[data-bs-theme="dark"] .form-select-sm option{background:#101e33;color:#c8d4e8;}
@media(max-width:575.98px){.filter-search{min-width:100%;order:-1;}}

/* ═══════════════════════════════════════
   SELECTION GRID TABLE
═══════════════════════════════════════ */
.grid-card{background:var(--card-bg);border:1px solid var(--card-border);border-radius:8px;box-shadow:var(--card-shadow);overflow:hidden;}
.grid-card-header{padding:13px 16px;border-bottom:1px solid var(--card-border);display:flex;align-items:center;justify-content:space-between;gap:10px;}
.grid-card-header h6{margin:0;font-size:.875rem;font-weight:600;color:var(--text-heading);display:flex;align-items:center;gap:8px;}
.pending-badge{background:rgba(249,115,22,.12);color:#ea580c;font-size:.65rem;font-weight:700;padding:2px 8px;border-radius:10px;}
.table-wrap{overflow-x:auto;-webkit-overflow-scrolling:touch;}
.data-table{width:100%;border-collapse:collapse;min-width:640px;}
.data-table thead tr{background:var(--table-header);}
.data-table thead th{font-size:.7rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--text-muted);padding:10px 12px;border-bottom:1px solid var(--card-border);white-space:nowrap;}
.data-table thead th:first-child{padding-left:14px;}
.data-table tbody tr{border-bottom:1px solid var(--card-border);cursor:pointer;transition:background .15s;}
.data-table tbody tr:last-child{border-bottom:none;}
.data-table tbody tr:hover{background:var(--table-hover);}
.data-table tbody tr.selected{background:var(--table-selected);border-left:3px solid #6571ff;}
.data-table tbody td{padding:11px 12px;font-size:.8rem;vertical-align:middle;color:var(--text-primary);}
.data-table tbody td:first-child{padding-left:14px;}
.sr-id-link{color:#6571ff;font-weight:700;font-size:.8rem;}
.priority-chip{font-size:.62rem;font-weight:700;padding:2px 8px;border-radius:10px;white-space:nowrap;}
.p-high{background:rgba(255,51,102,.12);color:#ff3366;}
.p-med{background:rgba(251,188,6,.12);color:#a8802a;}
.p-low{background:rgba(5,163,74,.12);color:#05a34a;}
.warranty-chip{font-size:.62rem;font-weight:700;padding:2px 8px;border-radius:10px;white-space:nowrap;}
.w-cov{background:rgba(5,163,74,.1);color:#05a34a;}
.w-unk{background:rgba(174,183,197,.15);color:#5a6a7e;}
.sla-wrap{display:flex;align-items:center;gap:5px;font-size:.75rem;}
.sla-ok{color:#05a34a;}.sla-warn{color:#a8802a;}.sla-crit{color:#ff3366;}
.select-row-radio{accent-color:#6571ff;width:15px;height:15px;cursor:pointer;}
.empty-state{text-align:center;padding:40px 20px;color:var(--text-muted);}
.empty-state i{font-size:2.2rem;display:block;margin-bottom:8px;opacity:.3;}

/* Pagination */
.tbl-footer{padding:10px 14px;border-top:1px solid var(--card-border);display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;}
.tbl-footer span{font-size:.72rem;color:var(--text-muted);}
.page-btns{display:flex;gap:4px;}
.pg-btn{background:var(--surface-2);border:1px solid var(--border-color);border-radius:5px;width:28px;height:28px;display:flex;align-items:center;justify-content:center;cursor:pointer;font-size:.75rem;color:var(--text-muted);transition:all .15s;}
.pg-btn:hover,.pg-btn.active{background:rgba(101,113,255,.1);border-color:#6571ff;color:#6571ff;}

/* ═══════════════════════════════════════
   DETAIL PANEL (RIGHT)
═══════════════════════════════════════ */
.detail-panel{display:flex;flex-direction:column;gap:12px;position:sticky;top:80px;}
.dp-card{background:var(--card-bg);border:1px solid var(--card-border);border-radius:8px;box-shadow:var(--card-shadow);overflow:hidden;}
.dp-hdr{padding:12px 16px;border-bottom:1px solid var(--card-border);display:flex;align-items:center;gap:9px;}
.dp-hdr-icon{width:30px;height:30px;border-radius:6px;display:flex;align-items:center;justify-content:center;font-size:.85rem;flex-shrink:0;}
.dp-hdr h6{margin:0;font-size:.8375rem;font-weight:600;color:var(--text-heading);}
.dp-hdr .sub{font-size:.7rem;color:var(--text-muted);display:block;}
.dp-body{padding:14px 16px;}
.dp-empty{text-align:center;padding:28px 16px;color:var(--text-muted);font-size:.8rem;}
.dp-empty i{font-size:1.8rem;display:block;margin-bottom:8px;opacity:.3;}

/* Info rows inside panel */
.dp-row{display:flex;justify-content:space-between;align-items:baseline;margin-bottom:8px;font-size:.78rem;}
.dp-row span:first-child{color:var(--text-muted);}
.dp-row span:last-child{font-weight:500;color:var(--text-heading);text-align:right;max-width:58%;}
hr.dp-hr{border-color:var(--card-border);margin:10px 0;}


/* Description block */
.desc-block {
  font-size: .8rem;
  line-height: 1.55;
  color: var(--text-body, #333);
  background: var(--surface-2, #f7f7f9);
  border: 1px solid var(--border, #e5e7eb);
  border-radius: 8px;
  padding: 12px 14px;
  /* key fixes for mobile: */
  word-break: break-word;      /* long words/URLs wrap instead of overflowing */
  overflow-wrap: anywhere;
  white-space: pre-wrap;       /* preserves line breaks from the description */
  max-width: 100%;
  box-sizing: border-box;
}.desc-block::-webkit-scrollbar{width:3px;}
.desc-block::-webkit-scrollbar-thumb{background:var(--border-color);}

/* ═══════════════════════════════════════
   ACTION BUTTONS
═══════════════════════════════════════ */
.action-panel{background:var(--card-bg);border:1px solid var(--card-border);border-radius:8px;box-shadow:var(--card-shadow);padding:14px 16px;}
.action-panel .ap-title{font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--text-muted);margin-bottom:12px;display:flex;align-items:center;gap:6px;}
.action-panel .ap-title::after{content:'';flex:1;height:1px;background:var(--border-color);}
.btn-action{width:100%;border:none;border-radius:7px;padding:.6rem 1rem;font-size:.8125rem;font-weight:500;cursor:pointer;display:flex;align-items:center;gap:8px;margin-bottom:8px;transition:all .18s;position:relative;overflow:hidden;}
.btn-action:last-child{margin-bottom:0;}
.btn-action:disabled{opacity:.5;cursor:not-allowed;}
.btn-action::after{content:'';position:absolute;inset:0;background:#fff;opacity:0;transition:opacity .15s;}
.btn-action:hover:not(:disabled)::after{opacity:.06;}
.btn-approve{background:linear-gradient(135deg,#05a34a,#0abf56);color:#fff;}
.btn-approve:hover:not(:disabled){box-shadow:0 4px 16px rgba(5,163,74,.35);}
.btn-accounts{background:linear-gradient(135deg,#fbbc06,#f59e0b);color:#1a2236;}
.btn-accounts:hover:not(:disabled){box-shadow:0 4px 16px rgba(251,188,6,.35);}
.btn-reject{background:linear-gradient(135deg,#ff3366,#e02050);color:#fff;}
.btn-reject:hover:not(:disabled){box-shadow:0 4px 16px rgba(255,51,102,.35);}
.btn-action i{font-size:.95rem;flex-shrink:0;}
.btn-action .btn-sub{font-size:.65rem;opacity:.8;display:block;margin-top:1px;font-weight:400;}

/* Rejection text area */
.rejection-wrap{margin-top:10px;}
.rejection-label{font-size:.75rem;font-weight:500;color:#ff3366;margin-bottom:5px;display:flex;align-items:center;gap:5px;}
.rejection-label i{font-size:.8rem;}
.rejection-ta{width:100%;font-size:.78rem;border:1px solid rgba(255,51,102,.4);border-radius:6px;padding:.45rem .7rem;color:var(--text-primary);background:rgba(255,51,102,.04);resize:vertical;min-height:80px;transition:border-color .15s,box-shadow .15s,background .3s,color .3s;font-family:inherit;}
.rejection-ta:focus{border-color:#ff3366;box-shadow:0 0 0 3px rgba(255,51,102,.12);outline:none;}
.rejection-ta::placeholder{color:var(--text-light);}
.rejection-ta:disabled{opacity:.45;cursor:not-allowed;background:var(--surface-2);}
.char-hint{font-size:.68rem;color:var(--text-muted);text-align:right;margin-top:3px;}
.char-hint.warn{color:#ff3366;}


.pg-header {
    background: linear-gradient(135deg,#9A7B4F 0%,#7A6140 100%);
    border-radius: 10px;
    padding: 20px 24px;
    margin-bottom: 20px;
    color: #fff;
    position: relative;
    overflow: hidden;
}


/* ═══════════════════════════════════════
   CONFIRM MODAL
═══════════════════════════════════════ */
.modal-overlay{display:none;position:fixed;inset:0;background:var(--overlay-bg);z-index:9000;align-items:center;justify-content:center;padding:16px;}
.modal-overlay.show{display:flex;}
.modal-box{background:var(--modal-bg);border-radius:12px;border:1px solid var(--card-border);max-width:440px;width:100%;box-shadow:var(--modal-shadow);animation:popIn .25s ease;overflow:hidden;}
@keyframes popIn{from{transform:scale(.88);opacity:0;}to{transform:none;opacity:1;}}
.modal-hdr{padding:16px 18px;border-bottom:1px solid var(--card-border);display:flex;align-items:center;justify-content:space-between;}
.modal-hdr h6{margin:0;font-size:.9rem;font-weight:600;color:var(--text-heading);}
.modal-close{background:none;border:none;color:var(--text-muted);cursor:pointer;font-size:1.1rem;padding:4px;border-radius:4px;line-height:1;}
.modal-close:hover{color:var(--text-heading);background:var(--surface-2);}
.modal-body-c{padding:18px;}
.modal-icon-ring{width:56px;height:56px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:1.6rem;margin:0 auto 14px;}
.modal-title-t{font-size:.9375rem;font-weight:600;color:var(--text-heading);text-align:center;margin-bottom:6px;}
.modal-sub-t{font-size:.78rem;color:var(--text-muted);text-align:center;line-height:1.5;}
.modal-sr-highlight{background:var(--surface-2);border:1px solid var(--border-color);border-radius:6px;padding:10px 14px;margin:14px 0;font-size:.78rem;}
.modal-sr-highlight .sr-row{display:flex;justify-content:space-between;margin-bottom:4px;}
.modal-sr-highlight .sr-row:last-child{margin-bottom:0;}
.modal-sr-highlight .sk{color:var(--text-muted);}
.modal-sr-highlight .sv{font-weight:500;color:var(--text-heading);}
.modal-ftr{padding:14px 18px;border-top:1px solid var(--card-border);display:flex;gap:10px;justify-content:flex-end;}
.btn-modal-cancel{background:var(--surface-2);border:1px solid var(--border-color);border-radius:6px;padding:.45rem 1.1rem;font-size:.8rem;cursor:pointer;color:var(--text-muted);transition:all .15s;}
.btn-modal-cancel:hover{border-color:var(--text-muted);color:var(--text-heading);}
.btn-modal-confirm{border:none;border-radius:6px;padding:.45rem 1.3rem;font-size:.8rem;font-weight:500;cursor:pointer;color:#fff;transition:all .15s;display:flex;align-items:center;gap:6px;}
.btn-modal-confirm:disabled{opacity:.6;cursor:not-allowed;}

/* ═══════════════════════════════════════
   TOAST
═══════════════════════════════════════ */
.toast-wrap{position:fixed;top:70px;right:16px;z-index:9999;display:flex;flex-direction:column;gap:8px;max-width:310px;}
.toast-item{background:var(--card-bg);border-left:4px solid #6571ff;border-radius:7px;padding:12px 14px;box-shadow:0 6px 24px rgba(0,0,0,.18);display:flex;align-items:flex-start;gap:10px;animation:toastIn .3s ease;}
.toast-item.success{border-color:#05a34a;}.toast-item.error{border-color:#ff3366;}.toast-item.warning{border-color:#fbbc06;}
@keyframes toastIn{from{transform:translateX(40px);opacity:0;}to{transform:none;opacity:1;}}
.ti-icon{font-size:1.1rem;flex-shrink:0;margin-top:1px;}
.ti-icon.primary{color:#6571ff;}.ti-icon.success{color:#05a34a;}.ti-icon.error{color:#ff3366;}.ti-icon.warning{color:#fbbc06;}
.ti-title{font-size:.8125rem;font-weight:600;margin:0 0 2px;color:var(--text-heading);}
.ti-body{font-size:.72rem;margin:0;color:var(--text-muted);}
@media(max-width:575.98px){.toast-wrap{left:12px;right:12px;max-width:none;}}

/* ═══════════════════════════════════════
   WHATSAPP SENT BADGE
═══════════════════════════════════════ */
.wa-sent{display:inline-flex;align-items:center;gap:5px;background:rgba(37,211,102,.1);border:1px solid rgba(37,211,102,.25);border-radius:6px;padding:4px 10px;font-size:.72rem;color:#19a34a;font-weight:500;}
.wa-sent i{color:#25d366;}

/* Mobile: collapse detail below table */
@media(max-width:991.98px){
  .detail-panel{position:static;}
  .dp-card,.action-panel{margin-bottom:0;}
}



/* ═══════════════════════════════════════
   MOBILE RESPONSIVE OVERRIDES
═══════════════════════════════════════ */
@media(max-width:991.98px){
  .workspace{gap:14px;}
  .detail-panel{position:static;top:auto;}
}

/* Tablet & down: tighten filter bar */
@media(max-width:767.98px){
  .filter-bar{flex-direction:column;align-items:stretch;gap:8px;}
  .filter-bar .form-select-sm{width:100%!important;}
  .filter-search{min-width:100%;}
  .stats-strip{grid-template-columns:repeat(2,1fr);}
}

/* Phone: convert table rows into stacked cards */
@media(max-width:575.98px){
  .stats-strip{grid-template-columns:1fr 1fr;gap:8px;}
  .stat-card{padding:11px 12px;gap:9px;}
  .stat-num{font-size:1.25rem;}
  .stat-icon{width:34px;height:34px;font-size:1rem;}

  /* Kill the forced min-width so no horizontal scroll */
  .data-table{min-width:0;}
  .table-wrap{overflow-x:visible;}

  /* Hide the table header — labels move into each cell */
  .data-table thead{display:none;}

  /* Each row becomes a card */
  .data-table,
  .data-table tbody,
  .data-table tr,
  .data-table td{display:block;width:100%;}

  .data-table tbody tr{
    border:1px solid var(--card-border);
    border-radius:8px;
    margin-bottom:10px;
    padding:8px 4px;
    background:var(--card-bg);
  }
  .data-table tbody tr.selected{
    border-left:3px solid #6571ff;
    background:var(--table-selected);
  }

  .data-table tbody td{
    display:flex;
    justify-content:space-between;
    align-items:center;
    gap:12px;
    padding:7px 12px;
    border:none;
    text-align:right;
    max-width:none!important;
    white-space:normal!important;
    overflow:visible!important;
  }

  /* Inject a label before each cell using data-label */
  .data-table tbody td::before{
    content:attr(data-label);
    font-size:.65rem;
    font-weight:600;
    text-transform:uppercase;
    letter-spacing:.05em;
    color:var(--text-muted);
    flex-shrink:0;
  }

  /* The radio cell: hide its empty label, left-align */
  .data-table tbody td.cell-radio{justify-content:flex-end;}
  .data-table tbody td.cell-radio::before{content:'';}

  /* Let client name cell wrap fully */
  .data-table tbody td > div{max-width:none!important;white-space:normal!important;overflow:visible!important;text-align:right;}

  /* Action buttons a touch larger for tapping */
  .btn-action{padding:.7rem 1rem;}

  /* Modal full-width comfort */
  .modal-box{max-width:100%;}
  .pg-header h4{font-size:.9rem;}
}

/* Very small phones */
@media(max-width:380px){
  .stats-strip{grid-template-columns:1fr;}
}


@media (max-width: 576px) {
  .desc-block {
    font-size: .78rem;
    padding: 10px 12px;
    line-height: 1.5;
  }
}
  
  </style>
  @endpush



@section('content')


<!-- ══ SUCCESS MODAL ══ -->

<div class="sb-overlay" id="sbOverlay" onclick="closeSidebar()"></div>

<!-- ══ SUCCESS MODAL ══ -->
<div class="modal-overlay" id="confirmModal">
  <div class="modal-box">
    <div class="modal-hdr">
      <h6 id="modalTitle">Confirm Action</h6>
      <button class="modal-close" onclick="closeModal()"><i class="bi bi-x-lg"></i></button>
    </div>
    <div class="modal-body-c">
      <div class="modal-icon-ring" id="modalIconRing"></div>
      <div class="modal-title-t" id="modalTitleText"></div>
      <div class="modal-sub-t" id="modalSubText"></div>
      <div class="modal-sr-highlight" id="modalSrHighlight"></div>
      <div id="modalWaNote" style="display:none;" class="mt-2"></div>
    </div>
    <div class="modal-ftr">
      <button class="btn-modal-cancel" onclick="closeModal()">Cancel</button>
      <button class="btn-modal-confirm" id="modalConfirmBtn" onclick="executeAction()">Confirm</button>
    </div>
  </div>
</div>

<!-- ══ TOAST ══ -->
<div class="toast-wrap" id="toastWrap"></div>

<!-- ══ SIDEBAR ══ -->


<!-- ══ TOPBAR ══ -->


<!-- ══ MAIN ══ -->

  <!-- Page Header -->
  <div class="pg-header">
    <h4><i class="bi bi-clipboard-check me-2"></i>Inquiry Approval — Triage Panel</h4>
    <p>Review incoming Pending service requests against active contracts. Approve, forward to Accounts, or reject with documented reason.</p>
    <div class="meta-row">
      <span class="meta-badge"><i class="bi bi-briefcase me-1"></i>Head of Projects</span>
      <span class="meta-badge"><i class="bi bi-person-gear me-1"></i>Admin</span>
      <span class="meta-badge"><i class="bi bi-shield-fill-check me-1"></i>Super Admin</span>
    </div>
  </div>

  <!-- Stats -->
  <div class="stats-strip">
    <div class="stat-card">
      <div class="stat-icon" style="background:rgba(249,115,22,.1);"><i class="bi bi-hourglass-split" style="color:#f97316;"></i></div>
      <div><div class="stat-num" style="color:#f97316;" id="stat-pending">0</div><div class="stat-lbl">Pending Triage</div></div>
    </div>
    <div class="stat-card">
      <div class="stat-icon" style="background:rgba(5,163,74,.1);"><i class="bi bi-check2-circle" style="color:#05a34a;"></i></div>
      <div><div class="stat-num" style="color:#05a34a;" id="stat-approved">0</div><div class="stat-lbl">Approved Today</div></div>
    </div>
    <div class="stat-card">
      <div class="stat-icon" style="background:rgba(251,188,6,.1);"><i class="bi bi-calculator" style="color:#fbbc06;"></i></div>
      <div><div class="stat-num" style="color:#fbbc06;" id="stat-fwd">0</div><div class="stat-lbl">Sent to Accounts</div></div>
    </div>
    <div class="stat-card">
      <div class="stat-icon" style="background:rgba(255,51,102,.1);"><i class="bi bi-x-circle" style="color:#ff3366;"></i></div>
      <div><div class="stat-num" style="color:#ff3366;" id="stat-rejected">0</div><div class="stat-lbl">Rejected Today</div></div>
    </div>
  </div>

  <!-- Workspace -->
  <div class="workspace">

    <!-- LEFT: Selection Grid -->
    <div>
      <!-- Filter Bar -->
      <div class="filter-bar">
        <div class="filter-search">
          <i class="bi bi-search"></i>
          <input type="text" class="form-control-sm" id="searchInput" placeholder="Search SR_ID, client, site, category…" oninput="applyFilter()"/>
        </div>
        <select class="form-select-sm" style="width:130px;" id="priorityFilter" onchange="applyFilter()">
          <option value="">All Priorities</option>
          <option value="High">High</option>
          <option value="Medium">Medium</option>
          <option value="Low">Low</option>
        </select>
        <select class="form-select-sm" style="width:140px;" id="warrantyFilter" onchange="applyFilter()">
          <option value="">All Warranty</option>
          <option value="In Warranty">In Warranty</option>
          <option value="Out of Warranty">Out of Warranty</option>
        </select>
        <span style="font-size:.72rem;color:var(--text-muted);white-space:nowrap;" id="filterCount"></span>
      </div>

      <!-- Table Card -->
      <div class="grid-card">
        <div class="grid-card-header">
          <h6><i class="bi bi-table" style="color:#f97316;"></i>Pending Inquiries <span class="pending-badge" id="tableCount">0 records</span></h6>
          <span style="font-size:.72rem;color:var(--text-muted);">Click a row to review</span>
        </div>
        <div class="table-wrap">
          <table class="data-table">
            <thead>
              <tr>
                <th></th>
                <th>SR_ID</th>
                <th>Client</th>
                <th>Category</th>
                <th>Site</th>
                <th>Priority</th>
                <th>Warranty</th>
                <th style="text-align:center;"><i class="bi bi-clock" style="font-size:.85rem;"></i></th>
                <th>Submitted</th>
              </tr>
            </thead>
            <tbody id="tableBody"></tbody>
          </table>
          <div class="empty-state" id="emptyState" style="display:none;">
            <i class="bi bi-inbox"></i>No pending inquiries match your filter.
          </div>
        </div>
        <div class="tbl-footer">
          <span id="tblFooterLabel"></span>
          <div class="page-btns" id="pageBtns"></div>
        </div>
      </div>
    </div>

    <!-- RIGHT: Detail Panel -->
    <div class="detail-panel" id="detailPanel">

      <!-- Contract Cross-Examination -->
      <div class="dp-card">
        <div class="dp-hdr">
          <div class="dp-hdr-icon" style="background:rgba(72,149,239,.1);"><i class="bi bi-file-earmark-check-fill" style="color:#4895ef;"></i></div>
          <div><h6>Contract Cross-Examination</h6><span class="sub">Contract & warranty status vs inquiry</span></div>
        </div>
        <div class="dp-body" id="contractBody">
          <div class="dp-empty"><i class="bi bi-mouse2"></i>Select a row to load contract data</div>
        </div>
      </div>

      <!-- Inquiry Description -->
      <!-- <div class="dp-card">
        <div class="dp-hdr">
          <div class="dp-hdr-icon" style="background:rgba(101,113,255,.1);"><i class="bi bi-card-text" style="color:#6571ff;"></i></div>
          <div><h6>Inquiry Description</h6><span class="sub">Client-submitted details</span></div>
        </div>
        <div class="dp-body" id="descBody">
          <div class="dp-empty"><i class="bi bi-chat-left-text"></i>No inquiry selected</div>
        </div>
      </div> -->


      <div class="dp-card">
  <div class="dp-hdr" onclick="toggleDescCard(this)" style="cursor:pointer;">
    <div class="dp-hdr-icon" style="background:rgba(101,113,255,.1);"><i class="bi bi-card-text" style="color:#6571ff;"></i></div>
    <div style="flex:1;"><h6>Inquiry Description and Attachments</h6><span class="sub">Client-submitted details</span></div>
    <i class="bi bi-chevron-down dp-toggle-icon" style="transition:transform .2s;color:var(--text-muted);"></i>
  </div>
  <div class="dp-body" id="descBody">
    <div class="dp-empty"><i class="bi bi-chat-left-text"></i>No inquiry selected</div>
  </div>
</div>



      <!-- Action Panel -->
      <div class="action-panel">
        <div class="ap-title"><i class="bi bi-lightning-charge-fill" style="color:#f97316;"></i>Triage Actions</div>

        <button class="btn-action btn-approve" id="btnApprove" onclick="triggerAction('approve')" disabled>
          <i class="bi bi-check2-circle"></i>
          <div>Approve (In-Warranty)<span class="btn-sub">→ Dispatch Engine</span></div>
        </button>

        <button class="btn-action btn-accounts" id="btnAccounts" onclick="triggerAction('accounts')" disabled>
          <i class="bi bi-calculator-fill"></i>
          <div>Forward to Accounts (OoW)<span class="btn-sub">→ Quotation Desk</span></div>
        </button>

        <button class="btn-action btn-reject" id="btnReject" onclick="enableRejection()" disabled>
          <i class="bi bi-x-circle-fill"></i>
          <div>Reject Ticket<span class="btn-sub">→ WhatsApp notify + Archive</span></div>
        </button>

        <!-- Rejection Reason -->
        <div class="rejection-wrap" id="rejectionWrap" style="display:none;">
          <div class="rejection-label"><i class="bi bi-exclamation-triangle-fill"></i>Mandatory Rejection Explanation</div>
          <textarea class="rejection-ta" id="rejectionText" rows="3"
                    placeholder="Enter the rejection reason. This will be sent to the client via WhatsApp…"
                    oninput="onRejectionInput(this)" maxlength="500"></textarea>
          <div class="char-hint" id="rejCharHint">0 / 500</div>
          <div style="display:flex;gap:8px;margin-top:8px;">
            <button class="btn-modal-cancel" style="flex:1;" onclick="cancelRejection()">Cancel</button>
            <button class="btn-modal-confirm" id="btnConfirmReject" style="flex:2;background:#ff3366;" onclick="triggerAction('reject')" disabled>
              <i class="bi bi-send-fill"></i>Confirm Reject &amp; Notify
            </button>
          </div>
        </div>

        <div id="noSelectionNote" style="text-align:center;padding:10px 0;font-size:.75rem;color:var(--text-muted);">
          <i class="bi bi-arrow-left me-1"></i>Select a row from the table to enable actions
        </div>
      </div>

    </div>
  </div>



@endsection

@push('scripts')
<script>
/* ════════════════════════════════
    ROUTES + SEED (real DB data)
════════════════════════════════ */
const CSRF = "{{ csrf_token() }}";
window.ROUTES = {
  approveBase: "{{ url('service-requests') }}"   // Matches your exact web.php declaration mapping
};

const ALL_TICKETS = [
  @foreach($inquiries as $t)
@php
    $srRef = 'SR-' . ($t->created_at ? $t->created_at->year : now()->year) . '-' . str_pad($t->id, 5, '0', STR_PAD_LEFT);
@endphp
  {
    id:           @json($srRef),
    dbId:         {{ $t->id }},
    client:       @json($t->client?->company_name ?? '—'),
    contract:     @json($t->client?->unique_code ?? '—'),
    category:     @json($t->category?->category_name ?? '—'),
    site:         @json($t->project?->site_name ?? '—'),
    project:      @json($t->project?->project_name ?? '—'),
    priority:     @json(ucfirst($t->priority_level)),
    warranty:     @json(($t->project && $t->project->warranty_end_date && \Carbon\Carbon::parse($t->project->warranty_end_date)->endOfDay()->isFuture()) ? 'In Warranty' : 'Out of Warranty'),
    description:  @json($t->issue_description ?? ''),
    submitter:    @json($t->reported_by ?? '—'),
    submittedStr: @json($t->created_at?->format('d M H:i')),
    hrsAgo:       {{ (int) ($t->created_at ? $t->created_at->diffInHours(now()) : 0) }},
    status:       @json($t->status),
        attachments:  @json($t->attachments ?? []),

  },
@endforeach
];

/* ════════════════════════════════
    CLOCK
════════════════════════════════ */
function tick(){const el=document.getElementById('clock');if(el)el.textContent=new Date().toLocaleTimeString('en-IN',{hour:'2-digit',minute:'2-digit',second:'2-digit'});}
setInterval(tick,1000);tick();

/* ════════════════════════════════
    STATE
════════════════════════════════ */
let tickets=[...ALL_TICKETS];
let filtered=[...tickets];
let selectedId=null;
let pendingAction=null;

// Load real counts provided directly from controller data stack
let todayApproved = {{ $stats['approved'] }};
let todayFwd = {{ $stats['forwarded'] }};
let todayRejected = {{ $stats['rejected'] }};

/* ════════════════════════════════
    STATS
════════════════════════════════ */
function updateStats(){
  document.getElementById('stat-pending').textContent=tickets.length;
  document.getElementById('stat-approved').textContent=todayApproved;
  document.getElementById('stat-fwd').textContent=todayFwd;
  document.getElementById('stat-rejected').textContent=todayRejected;
  const nav=document.getElementById('pendingNavBadge');
  if(nav) nav.textContent=tickets.length;
}

/* ════════════════════════════════
    FILTER & RENDER
════════════════════════════════ */
let currentPage=1;
const PER_PAGE=10;

function applyFilter(){
  const q=document.getElementById('searchInput').value.toLowerCase();
  const pf=document.getElementById('priorityFilter').value;
  const wf=document.getElementById('warrantyFilter').value;
  filtered=tickets.filter(t=>{
    const mq=!q||(t.id.toLowerCase().includes(q)||t.client.toLowerCase().includes(q)||t.site.toLowerCase().includes(q)||t.category.toLowerCase().includes(q));
    const mp=!pf||t.priority===pf;
    const mw=!wf||t.warranty===wf;
    return mq&&mp&&mw;
  });
  currentPage=1;
  renderTable();
}

function renderTable(){
  const tbody=document.getElementById('tableBody');
  const empty=document.getElementById('emptyState');
  const start=(currentPage-1)*PER_PAGE;
  const page=filtered.slice(start,start+PER_PAGE);
  document.getElementById('filterCount').textContent=`${filtered.length} of ${tickets.length}`;
  document.getElementById('tableCount').textContent=`${filtered.length} records`;

  if(!filtered.length){tbody.innerHTML='';empty.style.display='block';document.getElementById('tblFooterLabel').textContent='No records';renderPager();return;}
  empty.style.display='none';
  document.getElementById('tblFooterLabel').textContent=`Showing ${start+1}–${Math.min(start+PER_PAGE,filtered.length)} of ${filtered.length}`;

  tbody.innerHTML=page.map(t=>{
    const hrs=t.hrsAgo;
    const slaCls=hrs>24?'sla-crit':hrs>8?'sla-warn':'sla-ok';
    const slaIcon=hrs>24?'bi-exclamation-triangle-fill':hrs>8?'bi-clock-history':'bi-check-circle';
    const pCls=t.priority==='High'?'p-high':t.priority==='Medium'?'p-med':'p-low';
    const wCls=t.warranty==='In Warranty'?'w-cov':'w-unk';
    const sel=t.id===selectedId;
    return `<tr class="${sel?'selected':''}" onclick="selectRow('${t.id}')">
      <td class="cell-radio"><input type="radio" class="select-row-radio" ${sel?'checked':''} onclick="event.stopPropagation();selectRow('${t.id}')"/></td>
      <td data-label="SR_ID"><span class="sr-id-link">${t.id}</span></td>
      <td data-label="Client"><div style="font-weight:500;color:var(--text-heading);font-size:.78rem;">${t.client}</div><div style="font-size:.68rem;color:var(--text-muted);">${t.contract}</div></td>
      <td data-label="Category" style="font-size:.78rem;">${t.category}</td>
      <td data-label="Site" style="font-size:.72rem;color:var(--text-muted);">${t.site}</td>
      <td data-label="Priority"><span class="priority-chip ${pCls}">${t.priority}</span></td>
      <td data-label="Warranty"><span class="warranty-chip ${wCls}">${t.warranty === 'In Warranty' ? '✓ In Warranty' : '✗ Out of Warranty'}</span></td>
      <td data-label="SLA"><div class="sla-wrap ${slaCls}"><i class="bi ${slaIcon}"></i>${hrs}h</div></td>
      <td data-label="Submitted" style="font-size:.72rem;color:var(--text-muted);">${t.submittedStr}</td>
    </tr>`;
  }).join('');
  renderPager();
}

function renderPager(){
  const total=Math.ceil(filtered.length/PER_PAGE);
  const cont=document.getElementById('pageBtns');
  if(total<=1){cont.innerHTML='';return;}
  cont.innerHTML=Array.from({length:total},(_,i)=>`<div class="pg-btn ${i+1===currentPage?'active':''}" onclick="goPage(${i+1})">${i+1}</div>`).join('');
}
function goPage(p){currentPage=p;renderTable();}

/* ════════════════════════════════
    ROW SELECT → DETAIL PANEL
════════════════════════════════ */
function selectRow(id){
  selectedId=id;
  const t=tickets.find(x=>x.id===id);
  renderTable();
  loadContractPanel(t);
  loadDescPanel(t);
  enableActionButtons();
  cancelRejection();
  if(window.innerWidth<992){
    setTimeout(()=>document.getElementById('detailPanel').scrollIntoView({behavior:'smooth',block:'start'}),100);
  }
}

function loadContractPanel(t){
  const covered=t.warranty==='In Warranty';
  const slaCls=t.hrsAgo>24?'#ff3366':t.hrsAgo>8?'#fbbc06':'#05a34a';
  const slaIcon=t.hrsAgo>24?'bi-exclamation-triangle-fill':t.hrsAgo>8?'bi-clock-history':'bi-check-circle-fill';
  document.getElementById('contractBody').innerHTML=`
    <div class="dp-row"><span>SR_ID</span><span style="color:#6571ff;font-weight:700;">${t.id}</span></div>
    <div class="dp-row"><span>Client Code</span><span>${t.contract}</span></div>
    <div class="dp-row"><span>Client</span><span>${t.client}</span></div>
    <div class="dp-row"><span>Project</span><span>${t.project}</span></div>
    <div class="dp-row"><span>Category</span><span>${t.category}</span></div>
    <hr class="dp-hr"/>
    <div class="dp-row">
        <span>Warranty Status</span>
        <span style="color:${covered ? '#05a34a' : '#dc3545'};font-weight:600;">
            ${t.warranty === 'In Warranty' ? '✓ In Warranty' : '✗ Out of Warranty'}
        </span>
    </div>    
    <div class="dp-row"><span>SLA Elapsed</span><span style="color:${slaCls};font-weight:600;display:flex;align-items:center;gap:4px;"><i class="bi ${slaIcon}"></i>${t.hrsAgo}h ago</span></div>
    <div class="dp-row" style="margin:0;"><span>Submitted by</span><span>${t.submitter}</span></div>
    <hr class="dp-hr"/>
    <div style="font-size:.72rem;color:var(--text-muted);background:${covered?'rgba(5,163,74,.07)':'rgba(219,53,69,.07)'};border:1px solid ${covered?'rgba(5,163,74,.2)':'rgba(219,53,69,.2)'};border-radius:6px;padding:8px 10px;display:flex;align-items:flex-start;gap:7px;">
      <i class="bi ${covered?'bi-shield-check':'bi-shield-exclamation'}" style="color:${covered?'#05a34a':'#dc3545'};margin-top:1px;flex-shrink:0;"></i>
      <span>${covered?'Contract is active and covers this category. Recommend <strong>In-Warranty Approval</strong>.':'Warranty coverage expired or unregistered. Review terms or route to <strong>Accounts</strong> for active client configuration pricing.'}</span>
    </div>`;
}



function toggleDescCard(hdr){
  const body = hdr.parentElement.querySelector('.dp-body');
  const icon = hdr.querySelector('.dp-toggle-icon');
  const collapsed = body.style.display === 'none';
  body.style.display = collapsed ? '' : 'none';
  if(icon) icon.style.transform = collapsed ? 'rotate(0deg)' : 'rotate(-90deg)';
}



function loadDescPanel(t){
  let attachHtml = '';
  const files = t.attachments || [];

  if (files.length) {
    attachHtml = `
      <div style="font-size:.7rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--text-muted);margin:14px 0 7px;">Attachments (${files.length})</div>
      <div style="display:flex;gap:8px;flex-wrap:wrap;">
        ${files.map(f => {
          const path  = typeof f === 'string' ? f : (f.path || '');
          const label = typeof f === 'string' ? path.split('/').pop() : (f.name || path.split('/').pop());
          const url   = `/storage/${path}`;
          const isImg = /\.(png|jpe?g|gif|webp|svg)$/i.test(path);

          return isImg
            ? `<a href="${url}" target="_blank" style="display:block;">
                 <img src="${url}" style="width:64px;height:64px;object-fit:cover;border-radius:6px;border:1px solid var(--border);"/>
               </a>`
            : `<a href="${url}" target="_blank" style="display:flex;align-items:center;gap:5px;padding:6px 10px;border:1px solid var(--border);border-radius:6px;font-size:.72rem;">
                 <i class="bi bi-paperclip"></i>${label}
               </a>`;
        }).join('')}
      </div>`;
  }

  document.getElementById('descBody').innerHTML = `
    <div style="font-size:.7rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--text-muted);margin-bottom:7px;">Issue Description</div>
    <div class="desc-block">${t.description || '—'}</div>
    <div style="display:flex;gap:10px;margin-top:10px;flex-wrap:wrap;">
      <span style="font-size:.72rem;color:var(--text-muted);display:flex;align-items:center;gap:4px;"><i class="bi bi-geo-alt"></i>${t.site}</span>
      <span style="font-size:.72rem;color:var(--text-muted);display:flex;align-items:center;gap:4px;"><i class="bi bi-person"></i>${t.submitter}</span>
      <span style="font-size:.72rem;color:var(--text-muted);display:flex;align-items:center;gap:4px;"><i class="bi bi-calendar3"></i>${t.submittedStr}</span>
    </div>
    ${attachHtml}`;
}


// function loadDescPanel(t){
//   document.getElementById('descBody').innerHTML=`
//     <div style="font-size:.7rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--text-muted);margin-bottom:7px;">Issue Description</div>
//     <div class="desc-block">${t.description||'—'}</div>
//     <div style="display:flex;gap:10px;margin-top:10px;flex-wrap:wrap;">
//       <span style="font-size:.72rem;color:var(--text-muted);display:flex;align-items:center;gap:4px;"><i class="bi bi-geo-alt"></i>${t.site}</span>
//       <span style="font-size:.72rem;color:var(--text-muted);display:flex;align-items:center;gap:4px;"><i class="bi bi-person"></i>${t.submitter}</span>
//       <span style="font-size:.72rem;color:var(--text-muted);display:flex;align-items:center;gap:4px;"><i class="bi bi-calendar3"></i>${t.submittedStr}</span>
//     </div>`;
// }

function enableActionButtons(){
  ['btnApprove','btnAccounts','btnReject'].forEach(id=>{document.getElementById(id).disabled=false;});
  document.getElementById('noSelectionNote').style.display='none';
}

/* ════════════════════════════════
    REJECTION FLOW
════════════════════════════════ */
function enableRejection(){
  if(!selectedId)return;
  document.getElementById('rejectionWrap').style.display='block';
  document.getElementById('rejectionText').disabled=false;
  document.getElementById('rejectionText').focus();
  document.getElementById('btnReject').style.display='none';
}
function cancelRejection(){
  document.getElementById('rejectionWrap').style.display='none';
  document.getElementById('btnReject').style.display='flex';
  document.getElementById('rejectionText').value='';
  document.getElementById('rejCharHint').textContent='0 / 500';
  document.getElementById('rejCharHint').className='char-hint';
  document.getElementById('btnConfirmReject').disabled=true;
}
function onRejectionInput(el){
  const len=el.value.length;
  const hint=document.getElementById('rejCharHint');
  hint.textContent=`${len} / 500`;
  hint.className='char-hint'+(len>450?' warn':'');
  document.getElementById('btnConfirmReject').disabled=len<10;
}

/* ════════════════════════════════
    ACTION TRIGGERS → MODAL
════════════════════════════════ */
function triggerAction(type){
  if(!selectedId)return;
  const t=tickets.find(x=>x.id===selectedId);
  pendingAction=type;

  const configs={
    approve:{title:'Confirm Approval',icon:'<i class="bi bi-check2-circle" style="font-size:1.6rem;color:#05a34a;"></i>',ring:'background:rgba(5,163,74,.1);',titleText:'Approve In-Warranty Ticket',subText:`This will set status to <strong>Approved</strong> and route the ticket to the <strong>Dispatch Engine</strong>.`,btnColor:'#05a34a',btnLabel:'Approve & Dispatch',waNote:null},
    accounts:{title:'Forward to Accounts',icon:'<i class="bi bi-calculator-fill" style="font-size:1.6rem;color:#fbbc06;"></i>',ring:'background:rgba(251,188,6,.1);',titleText:'Forward as Out-of-Warranty',subText:`This will set scope to <strong>Out-of-Warranty</strong> and transition the ticket to the <strong>Quotation Desk</strong>.`,btnColor:'#f59e0b',btnLabel:'Forward to Accounts',waNote:null},
    reject:{title:'Reject & Archive Ticket',icon:'<i class="bi bi-x-circle-fill" style="font-size:1.6rem;color:#ff3366;"></i>',ring:'background:rgba(255,51,102,.1);',titleText:'Reject This Ticket',subText:`Status will be set to <strong>Cancelled</strong> and the client notified via <strong>WhatsApp</strong> with your reason.`,btnColor:'#ff3366',btnLabel:'Reject & Notify Client',waNote:'whatsapp'},
  };

  const cfg=configs[type];
  document.getElementById('modalTitle').textContent=cfg.title;
  document.getElementById('modalIconRing').innerHTML=cfg.icon;
  document.getElementById('modalIconRing').style.cssText=cfg.ring+';width:56px;height:56px;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 14px;';
  document.getElementById('modalTitleText').textContent=cfg.titleText;
  document.getElementById('modalSubText').innerHTML=cfg.subText;
  document.getElementById('modalSrHighlight').innerHTML=`
    <div class="sr-row"><span class="sk">SR_ID</span><span class="sv" style="color:#6571ff;font-weight:700;">${t.id}</span></div>
    <div class="sr-row"><span class="sk">Client</span><span class="sv">${t.client}</span></div>
    <div class="sr-row"><span class="sk">Category</span><span class="sv">${t.category}</span></div>
    <div class="sr-row"><span class="sk">Site</span><span class="sv">${t.site}</span></div>
    ${type==='reject'?`<div class="sr-row"><span class="sk">Reason</span><span class="sv" style="color:#ff3366;">${document.getElementById('rejectionText').value.substring(0,60)}${document.getElementById('rejectionText').value.length>60?'…':''}</span></div>`:''}`;

  const waNote=document.getElementById('modalWaNote');
  if(cfg.waNote==='whatsapp'){
    waNote.style.display='block';
    waNote.innerHTML=`<span class="wa-sent"><i class="bi bi-whatsapp"></i>WhatsApp notification will be sent to client with rejection reason</span>`;
  } else { waNote.style.display='none'; }

  const btn=document.getElementById('modalConfirmBtn');
  btn.style.background=cfg.btnColor;
  btn.innerHTML=`<i class="bi bi-check2"></i>${cfg.btnLabel}`;
  document.getElementById('confirmModal').classList.add('show');
}

function closeModal(){document.getElementById('confirmModal').classList.remove('show');pendingAction=null;}
document.getElementById('confirmModal').addEventListener('click',function(e){if(e.target===this)closeModal();});

/* ════════════════════════════════
    EXECUTE ACTION — Real POST payload dispatch
════════════════════════════════ */
function executeAction(){
  if(!pendingAction||!selectedId)return;
  const t=tickets.find(x=>x.id===selectedId);
  const btn=document.getElementById('modalConfirmBtn');
  btn.disabled=true;
  btn.innerHTML='<span class="spinner-border" style="width:13px;height:13px;border-width:2px;"></span> Processing…';

  let url, body={};
  if(pendingAction==='approve'){ url=`${window.ROUTES.approveBase}/${t.dbId}/approve`; }
  else if(pendingAction==='accounts'){ url=`${window.ROUTES.approveBase}/${t.dbId}/forward`; }
  else if(pendingAction==='reject'){
    url=`${window.ROUTES.approveBase}/${t.dbId}/reject`;
    body.reason=document.getElementById('rejectionText').value;
  }

  fetch(url,{
    method:'POST',
    headers:{'X-CSRF-TOKEN':CSRF,'Accept':'application/json','Content-Type':'application/json'},
    body:JSON.stringify(body)
  })
  .then(r=>r.json().then(j=>({status:r.status,j})))
  .then(({status,j})=>{
    if(status>=200 && status<300 && j.ok){
      tickets=tickets.filter(x=>x.id!==selectedId);
      if(pendingAction==='approve')todayApproved++;
      else if(pendingAction==='accounts')todayFwd++;
      else if(pendingAction==='reject')todayRejected++;

      const toastType  = pendingAction==='reject' ? 'error'
                       : pendingAction==='accounts' ? 'warning'
                       : 'success';
      const toastTitle = pendingAction==='reject' ? 'Ticket Rejected'
                       : pendingAction==='accounts' ? 'Forwarded to Accounts'
                       : 'Ticket Approved';
      showToast(toastType, toastTitle, j.message);

      closeModal();
      selectedId=null;
      filtered=[...tickets];
      applyFilter();
      updateStats();
      document.getElementById('contractBody').innerHTML='<div class="dp-empty"><i class="bi bi-mouse2"></i>Select a row to load contract data</div>';
      document.getElementById('descBody').innerHTML='<div class="dp-empty"><i class="bi bi-chat-left-text"></i>No inquiry selected</div>';
      ['btnApprove','btnAccounts','btnReject'].forEach(id=>{document.getElementById(id).disabled=true;});
      document.getElementById('noSelectionNote').style.display='block';
      cancelRejection();
    } else if(status===422 && j.errors){
      showToast('error','Validation Failed',Object.values(j.errors)[0][0]);
    } else {
      showToast('error','Failed',j.message||'Action failed.');
    }
    btn.disabled=false;
    btn.innerHTML='<i class="bi bi-check2"></i>Confirm';
  })
  .catch(()=>{
    showToast('error','Network Error','Could not reach the server.');
    btn.disabled=false;
    btn.innerHTML='<i class="bi bi-check2"></i>Confirm';
  });
}

/* ════════════════════════════════
    TOAST
════════════════════════════════ */
function showToast(type,title,body){
  const w=document.getElementById('toastWrap');
  const icons={success:'bi-check-circle-fill',error:'bi-x-circle-fill',primary:'bi-info-circle-fill',warning:'bi-exclamation-circle-fill'};
  const t=document.createElement('div');
  t.className=`toast-item ${type}`;
  t.innerHTML=`<i class="bi ${icons[type]||'bi-info-circle-fill'} ti-icon ${type}"></i><div><p class="ti-title">${title}</p><p class="ti-body">${body}</p></div>`;
  w.appendChild(t);
  setTimeout(()=>{t.style.opacity='0';t.style.transition='opacity .3s';setTimeout(()=>t.remove(),300);},4000);
}

/* ════════════════════════════════
    INIT
════════════════════════════════ */
updateStats();
applyFilter();
</script>
@endpush


