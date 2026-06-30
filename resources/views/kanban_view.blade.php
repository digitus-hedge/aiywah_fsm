@extends('layouts.layout')

@section('title', 'Ticket Summary & History — Digit-Us Portal')
@section('page_title', 'Ticket Summary')
@section('page_icon', 'kanban')

@push('styles')
<style>
/* ═══════════════════════════════════════
   THEME TOKENS
═══════════════════════════════════════ */
:root,[data-theme="light"]{
  --app-bg:#f4f6fb;--surface:#fff;--surface-2:#f0f3f9;
  --card-bg:#fff;--card-border:#eaeef6;--modal-bg:#fff;--input-bg:#fff;
  --text-primary:#1a2236;--text-heading:#0d1626;--text-muted:#7987a1;
  --text-light:#b0bac9;--nav-link:#4a5568;--border-color:#e4e8f0;
  --card-shadow:0 2px 12px rgba(100,120,160,.09);
  --overlay-bg:rgba(9,15,35,.6);--modal-shadow:0 24px 64px rgba(0,0,0,.16);
  /* Lane colours */
  --lane-pending-bg:#fff8e6; --lane-pending-border:#f5c842;
  --lane-inprog-bg:#e8f3ff;  --lane-inprog-border:#4895ef;
  --lane-review-bg:#fff0fb;  --lane-review-border:#b44fd4;
  --lane-rework-bg:#fff1f1;  --lane-rework-border:#ff3366;
  --lane-done-bg:#edfaf3;    --lane-done-border:#05a34a;
  --lane-cancel-bg:#f4f5f7;  --lane-cancel-border:#aeb7c5;
}
[data-theme="dark"]{
  --app-bg:#060d1c;--surface:#0c1427;--surface-2:#101e33;
  --card-bg:#0d1829;--card-border:#16243d;--modal-bg:#0d1829;--input-bg:#101e33;
  --text-primary:#c8d4e8;--text-heading:#e4ecf8;--text-muted:#6b7fa0;
  --text-light:#3a4d66;--nav-link:#8aa0be;--border-color:#16243d;
  --card-shadow:0 2px 16px rgba(0,0,0,.4);
  --overlay-bg:rgba(0,0,0,.75);--modal-shadow:0 24px 64px rgba(0,0,0,.55);
  --lane-pending-bg:rgba(245,200,66,.07);  --lane-pending-border:#a8882a;
  --lane-inprog-bg:rgba(72,149,239,.07);   --lane-inprog-border:#2a5fa8;
  --lane-review-bg:rgba(180,79,212,.07);   --lane-review-border:#7a2f96;
  --lane-rework-bg:rgba(255,51,102,.07);   --lane-rework-border:#8a1b36;
  --lane-done-bg:rgba(5,163,74,.07);       --lane-done-border:#0a5c2e;
  --lane-cancel-bg:rgba(174,183,197,.05);  --lane-cancel-border:#3a4d66;
}

/* ═══════════════════════════════════════
   PAGE HEADER
═══════════════════════════════════════ */
.pg-header{
  background:linear-gradient(135deg,#6571ff 0%,#8b5cf6 100%);
  border-radius:10px;padding:20px 24px;margin-bottom:20px;
  color:#fff;position:relative;overflow:hidden;
}
.pg-header::before{content:'';position:absolute;left:-40px;bottom:-40px;width:180px;height:180px;border-radius:50%;background:rgba(255,255,255,.05);}
.pg-header::after {content:'';position:absolute;right:-30px;top:-30px;width:160px;height:160px;border-radius:50%;background:rgba(255,255,255,.07);}
.pg-header h4{font-size:1rem;font-weight:600;margin:0 0 3px;position:relative;z-index:1;color:#fff;}
.pg-header p  {font-size:.78rem;margin:0;opacity:.85;position:relative;z-index:1;color:#fff;}
.pg-header .meta-row{display:flex;align-items:center;gap:10px;margin-top:10px;position:relative;z-index:1;flex-wrap:wrap;}
.meta-badge{background:rgba(255,255,255,.18);border:1px solid rgba(255,255,255,.3);border-radius:20px;font-size:.6875rem;padding:2px 10px;font-weight:500;}
.view-only-tag{background:rgba(255,255,255,.25);border:1px solid rgba(255,255,255,.4);border-radius:6px;font-size:.6875rem;padding:3px 10px;font-weight:600;display:inline-flex;align-items:center;gap:5px;}
@media(max-width:575.98px){.pg-header{padding:14px 16px;}.pg-header h4{font-size:.9rem;}}

/* ═══════════════════════════════════════
   STATS STRIP
═══════════════════════════════════════ */
.stats-strip{display:grid;grid-template-columns:repeat(6,1fr);gap:10px;margin-bottom:18px;}
.stat-card{background:var(--card-bg);border:1px solid var(--card-border);border-radius:8px;padding:12px 14px;text-align:center;box-shadow:var(--card-shadow);transition:transform .2s;}
.stat-card:hover{transform:translateY(-2px);}
.stat-num{font-size:1.4rem;font-weight:700;line-height:1;margin-bottom:2px;}
.stat-lbl{font-size:.6875rem;color:var(--text-muted);font-weight:500;}
@media(max-width:991.98px){.stats-strip{grid-template-columns:repeat(3,1fr);}}
@media(max-width:575.98px){.stats-strip{grid-template-columns:repeat(2,1fr);}}

/* ═══════════════════════════════════════
   FILTER BAR
═══════════════════════════════════════ */
.filter-bar{background:var(--card-bg);border:1px solid var(--card-border);border-radius:8px;padding:14px 16px;margin-bottom:18px;box-shadow:var(--card-shadow);display:flex;align-items:center;gap:10px;flex-wrap:wrap;}
.filter-bar .form-control,.filter-bar .form-select{font-size:.8rem;border:1px solid var(--border-color);border-radius:6px;padding:.38rem .75rem;color:var(--text-primary);background:var(--input-bg);height:36px;}
.filter-bar .form-control:focus,.filter-bar .form-select:focus{border-color:rgba(101,113,255,.5);box-shadow:0 0 0 3px rgba(101,113,255,.12);outline:none;}
.filter-bar .form-control::placeholder{color:var(--text-light);}
.filter-search{position:relative;flex:1;min-width:200px;}
.filter-search i{position:absolute;left:10px;top:50%;transform:translateY(-50%);color:var(--text-muted);font-size:.85rem;}
.filter-search input{padding-left:32px;width:100%;}
.filter-bar label{font-size:.72rem;color:var(--text-muted);font-weight:500;white-space:nowrap;}
[data-theme="dark"] .form-select option{background:#101e33;color:#c8d4e8;}
.filter-count{font-size:.72rem;color:var(--text-muted);white-space:nowrap;margin-left:auto;}
.btn-sm-outline{background:transparent;border:1px solid var(--border-color);border-radius:6px;padding:.3rem .75rem;font-size:.78rem;color:var(--text-muted);cursor:pointer;display:inline-flex;align-items:center;gap:5px;transition:all .15s;height:36px;}
.btn-sm-outline:hover{border-color:#6571ff;color:#6571ff;}
.view-switcher{display:flex;gap:6px;align-items:center;}
.vs-btn{width:32px;height:32px;display:flex;align-items:center;justify-content:center;border-radius:6px;border:1px solid var(--border-color);background:var(--surface-2);cursor:pointer;color:var(--text-muted);font-size:.9rem;transition:all .15s;}
.vs-btn.active,.vs-btn:hover{background:rgba(101,113,255,.1);border-color:#6571ff;color:#6571ff;}
@media(max-width:767.98px){.filter-bar{gap:8px;}.filter-search{min-width:100%;order:-1;}}

/* Mobile scroll hint */
.scroll-hint{display:none;font-size:.72rem;color:var(--text-muted);text-align:center;padding:6px 0 10px;margin-top:-8px;}
@media(max-width:767.98px){.scroll-hint{display:block;}}

/* ═══════════════════════════════════════
   KANBAN BOARD
═══════════════════════════════════════ */
.kanban-wrapper{overflow-x:auto;padding-bottom:16px;-webkit-overflow-scrolling:touch;}
.kanban-wrapper::-webkit-scrollbar{height:6px;}
.kanban-wrapper::-webkit-scrollbar-thumb{background:var(--border-color);border-radius:3px;}
.kanban-board{display:flex;gap:14px;min-width:max-content;align-items:flex-start;padding:2px 2px 8px;}

/* Lane */
.lane{width:290px;flex-shrink:0;display:flex;flex-direction:column;}
.lane-header{border-radius:8px 8px 0 0;padding:11px 14px;display:flex;align-items:center;justify-content:space-between;}
.lane-title{display:flex;align-items:center;gap:8px;font-size:.8125rem;font-weight:600;color:var(--text-heading);}
.lane-dot{width:9px;height:9px;border-radius:50%;flex-shrink:0;}
.lane-count{font-size:.6875rem;font-weight:700;padding:2px 8px;border-radius:10px;}
.lane-body{background:var(--surface-2);border:1px solid var(--card-border);border-top:none;border-radius:0 0 8px 8px;padding:10px 9px;min-height:200px;display:flex;flex-direction:column;gap:10px;}

.lane-pending .lane-header{background:var(--lane-pending-bg);border:1px solid var(--lane-pending-border);border-bottom:none;}
.lane-pending .lane-dot{background:#f5c842;}
.lane-pending .lane-count{background:rgba(245,200,66,.2);color:#a8802a;}
.lane-inprog  .lane-header{background:var(--lane-inprog-bg);border:1px solid var(--lane-inprog-border);border-bottom:none;}
.lane-inprog  .lane-dot{background:#4895ef;}
.lane-inprog  .lane-count{background:rgba(72,149,239,.2);color:#1a5fad;}
.lane-review  .lane-header{background:var(--lane-review-bg);border:1px solid var(--lane-review-border);border-bottom:none;}
.lane-review  .lane-dot{background:#b44fd4;}
.lane-review  .lane-count{background:rgba(180,79,212,.2);color:#7a2f96;}
.lane-rework  .lane-header{background:var(--lane-rework-bg);border:1px solid var(--lane-rework-border);border-bottom:none;}
.lane-rework  .lane-dot{background:#ff3366;}
.lane-rework  .lane-count{background:rgba(255,51,102,.2);color:#8a1b36;}
.lane-done    .lane-header{background:var(--lane-done-bg);border:1px solid var(--lane-done-border);border-bottom:none;}
.lane-done    .lane-dot{background:#05a34a;}
.lane-done    .lane-count{background:rgba(5,163,74,.2);color:#0a5c2e;}
.lane-cancel  .lane-header{background:var(--lane-cancel-bg);border:1px solid var(--lane-cancel-border);border-bottom:none;}
.lane-cancel  .lane-dot{background:#aeb7c5;}
.lane-cancel  .lane-count{background:rgba(174,183,197,.2);color:#5a6a7e;}

/* ═══════════════════════════════════════
   KANBAN CARD
═══════════════════════════════════════ */
.kcard{background:var(--card-bg);border:1px solid var(--card-border);border-radius:8px;padding:12px 13px;cursor:default;box-shadow:var(--card-shadow);transition:box-shadow .2s,transform .2s,border-color .2s;position:relative;}
.kcard:hover{box-shadow:0 6px 24px rgba(100,120,160,.18);transform:translateY(-2px);}
[data-theme="dark"] .kcard:hover{box-shadow:0 6px 24px rgba(0,0,0,.5);}
.kc-top{display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:9px;gap:6px;}
.kc-sr{font-size:.8rem;font-weight:700;color:#6571ff;letter-spacing:.02em;}
.kc-warranty{font-size:.6rem;font-weight:700;padding:2px 7px;border-radius:10px;white-space:nowrap;flex-shrink:0;}
.warranty-covered{background:rgba(5,163,74,.12);color:#05a34a;}
.warranty-not{background:rgba(255,51,102,.12);color:#ff3366;}
.kc-client{font-size:.78rem;font-weight:500;color:var(--text-heading);margin-bottom:2px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;}
.kc-contract{font-size:.7rem;color:var(--text-muted);margin-bottom:9px;}
.kc-divider{border-color:var(--card-border);margin:8px 0;}
.kc-timer-row{display:flex;align-items:center;gap:6px;margin-bottom:8px;}
.kc-timer{font-size:.7rem;font-weight:600;padding:3px 8px;border-radius:6px;display:flex;align-items:center;gap:4px;}
.timer-ok  {background:rgba(5,163,74,.1); color:#05a34a;}
.timer-warn{background:rgba(251,188,6,.12);color:#a8802a;}
.timer-crit{background:rgba(255,51,102,.12);color:#ff3366;}
.kc-site{font-size:.7rem;color:var(--text-muted);margin-left:auto;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:100px;text-align:right;}
.kc-staff{background:var(--surface-2);border:1px solid var(--border-color);border-radius:6px;padding:7px 9px;margin-bottom:8px;font-size:.7rem;display:none;}
.kcard:hover .kc-staff{display:block;}
.kc-staff .staff-label{color:var(--text-muted);font-size:.65rem;text-transform:uppercase;letter-spacing:.05em;margin-bottom:3px;}
.kc-staff .staff-name{font-weight:600;color:var(--text-heading);display:flex;align-items:center;gap:5px;}
.kc-staff .staff-meta{color:var(--text-muted);margin-top:2px;}
.kc-actions{display:flex;align-items:center;gap:6px;margin-top:8px;}
.kc-action-btn{width:30px;height:30px;border-radius:6px;border:1px solid var(--border-color);background:var(--surface-2);display:flex;align-items:center;justify-content:center;cursor:pointer;font-size:.85rem;transition:all .15s;flex-shrink:0;color:var(--text-muted);}
.kc-action-btn:hover          {border-color:#6571ff;color:#6571ff;background:rgba(101,113,255,.08);}
.kc-action-btn.maps:hover     {border-color:#4caf50;color:#4caf50;background:rgba(76,175,80,.08);}
.kc-action-btn.before-ph:hover{border-color:#fbbc06;color:#a8802a;background:rgba(251,188,6,.1);}
.kc-action-btn.after-ph:hover {border-color:#4895ef;color:#1a5fad;background:rgba(72,149,239,.1);}
.kc-action-btn.dl-btn:hover   {border-color:#b44fd4;color:#7a2f96;background:rgba(180,79,212,.1);}
.kc-action-label{font-size:.65rem;color:var(--text-muted);margin-left:2px;}
.kc-tech{display:flex;align-items:center;gap:6px;margin-top:7px;}
.tech-av{width:22px;height:22px;border-radius:50%;background:linear-gradient(135deg,#6571ff,#a78bfa);display:flex;align-items:center;justify-content:center;color:#fff;font-size:.6rem;font-weight:700;flex-shrink:0;}
.tech-name{font-size:.7rem;color:var(--text-muted);}
.lane-empty{text-align:center;padding:28px 16px;color:var(--text-light);font-size:.78rem;}
.lane-empty i{font-size:1.6rem;display:block;margin-bottom:6px;opacity:.4;}

/* ═══════════════════════════════════════
   PHOTO LIGHTBOX
═══════════════════════════════════════ */
.lightbox-overlay{display:none;position:fixed;inset:0;background:var(--overlay-bg);z-index:9000;align-items:center;justify-content:center;padding:20px;}
.lightbox-overlay.show{display:flex;}
.lightbox-modal{background:var(--modal-bg);border-radius:12px;border:1px solid var(--card-border);max-width:680px;width:100%;box-shadow:var(--modal-shadow);animation:popIn .25s ease;overflow:hidden;}
.lb-header{display:flex;align-items:center;justify-content:space-between;padding:14px 18px;border-bottom:1px solid var(--card-border);}
.lb-header h6{margin:0;font-size:.875rem;font-weight:600;color:var(--text-heading);}
.lb-close{background:none;border:none;color:var(--text-muted);cursor:pointer;font-size:1.1rem;padding:4px;border-radius:4px;line-height:1;}
.lb-close:hover{color:var(--text-heading);background:var(--surface-2);}
.lb-body{padding:18px;}
.lb-img-wrap{background:var(--surface-2);border-radius:8px;overflow:hidden;text-align:center;min-height:280px;display:flex;align-items:center;justify-content:center;}
.lb-img-wrap img{max-width:100%;max-height:400px;object-fit:contain;}
.lb-meta{margin-top:12px;font-size:.75rem;color:var(--text-muted);display:flex;gap:14px;flex-wrap:wrap;}
.lb-meta span i{margin-right:4px;}
@keyframes popIn{from{transform:scale(.88);opacity:0;}to{transform:none;opacity:1;}}

/* ═══════════════════════════════════════
   MOBILE LIST VIEW
═══════════════════════════════════════ */
.mobile-list-view{display:none;}
.mobile-list-card{background:var(--card-bg);border:1px solid var(--card-border);border-radius:8px;padding:14px;margin-bottom:10px;box-shadow:var(--card-shadow);}
.mlc-top{display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:8px;}
.mlc-sr{font-size:.875rem;font-weight:700;color:#6571ff;}
.mlc-status{font-size:.65rem;font-weight:700;padding:3px 9px;border-radius:20px;}
.mlc-client{font-size:.82rem;font-weight:500;color:var(--text-heading);}
.mlc-meta{display:flex;gap:10px;margin-top:5px;flex-wrap:wrap;}
.mlc-meta span{font-size:.7rem;color:var(--text-muted);display:flex;align-items:center;gap:4px;}
.mlc-actions{display:flex;gap:8px;margin-top:10px;padding-top:10px;border-top:1px solid var(--card-border);}
.mlc-action-btn{flex:1;display:flex;flex-direction:column;align-items:center;gap:3px;padding:6px;border-radius:6px;border:1px solid var(--border-color);background:var(--surface-2);cursor:pointer;font-size:.65rem;color:var(--text-muted);transition:all .15s;}
.mlc-action-btn i{font-size:1rem;}
.mlc-action-btn:hover      {border-color:#6571ff;color:#6571ff;background:rgba(101,113,255,.07);}
.mlc-action-btn.maps:hover {border-color:#4caf50;color:#4caf50;}
.mlc-action-btn.before-ph:hover{border-color:#fbbc06;color:#a8802a;}
.mlc-action-btn.after-ph:hover {border-color:#4895ef;color:#1a5fad;}
.mlc-action-btn.dl-btn:hover   {border-color:#b44fd4;color:#7a2f96;}

/* ═══════════════════════════════════════
   TOAST
═══════════════════════════════════════ */
.toast-wrap{position:fixed;top:70px;right:16px;z-index:9999;display:flex;flex-direction:column;gap:8px;max-width:300px;}
.toast-item{background:var(--card-bg);border-left:4px solid #6571ff;border-radius:7px;padding:12px 14px;box-shadow:0 6px 24px rgba(0,0,0,.18);display:flex;align-items:flex-start;gap:10px;animation:toastIn .3s ease;}
.toast-item.success{border-color:#05a34a;}.toast-item.error{border-color:#ff3366;}
@keyframes toastIn{from{transform:translateX(40px);opacity:0;}to{transform:none;opacity:1;}}
.ti-icon{font-size:1.1rem;flex-shrink:0;margin-top:1px;}
.ti-icon.primary{color:#6571ff;}.ti-icon.success{color:#05a34a;}.ti-icon.error{color:#ff3366;}
.ti-title{font-size:.8125rem;font-weight:600;margin:0 0 2px;color:var(--text-heading);}
.ti-body{font-size:.72rem;margin:0;color:var(--text-muted);}
@media(max-width:575.98px){.toast-wrap{left:12px;right:12px;max-width:none;}}
</style>
@endpush

@section('content')

{{-- Toast shelf --}}
<div class="toast-wrap" id="toastWrap"></div>

{{-- Photo Lightbox --}}
<div class="lightbox-overlay" id="lightbox">
  <div class="lightbox-modal">
    <div class="lb-header">
      <h6 id="lbTitle">Photo Viewer</h6>
      <button class="lb-close" onclick="closeLightbox()"><i class="bi bi-x-lg"></i></button>
    </div>
    <div class="lb-body">
      <div class="lb-img-wrap" id="lbImgWrap">
        <div style="display:flex;flex-direction:column;align-items:center;gap:8px;color:var(--text-muted);font-size:.85rem;">
          <i class="bi bi-image" style="font-size:2.5rem;opacity:.3;"></i>
          <span id="lbPlaceholderText">Loading photo…</span>
        </div>
      </div>
      <div class="lb-meta" id="lbMeta"></div>
    </div>
  </div>
</div>

{{-- ══════════════════════════════════════════
     PAGE HEADER
══════════════════════════════════════════ --}}
<div class="pg-header">
  <h4><i class="bi bi-kanban me-2"></i>Ticket Summary &amp; History — Kanban Card View</h4>
  <p>Real-time pipeline view of all service requests across lifecycle stages. Cards update automatically based on field actions.</p>
  <div class="meta-row">
    <span class="meta-badge"><i class="bi bi-person-badge me-1"></i>Super Admin</span>
    <span class="meta-badge"><i class="bi bi-person-gear me-1"></i>Admin</span>
    <span class="meta-badge"><i class="bi bi-briefcase me-1"></i>Head of Projects</span>
    <span class="meta-badge"><i class="bi bi-calculator me-1"></i>Accounts</span>
    <span class="view-only-tag"><i class="bi bi-eye-slash"></i>View Only — No Drag</span>
  </div>
</div>

{{-- ══════════════════════════════════════════
     STATS STRIP
══════════════════════════════════════════ --}}
<div class="stats-strip">
  <div class="stat-card"><div class="stat-num" style="color:#f5c842;" id="cnt-pending">0</div><div class="stat-lbl">Pending</div></div>
  <div class="stat-card"><div class="stat-num" style="color:#4895ef;" id="cnt-inprog">0</div><div class="stat-lbl">In Progress</div></div>
  <div class="stat-card"><div class="stat-num" style="color:#b44fd4;" id="cnt-review">0</div><div class="stat-lbl">Pending Review</div></div>
  <div class="stat-card"><div class="stat-num" style="color:#ff3366;" id="cnt-rework">0</div><div class="stat-lbl">Rework</div></div>
  <div class="stat-card"><div class="stat-num" style="color:#05a34a;" id="cnt-done">0</div><div class="stat-lbl">Completed</div></div>
  <div class="stat-card"><div class="stat-num" style="color:#aeb7c5;" id="cnt-cancel">0</div><div class="stat-lbl">Cancelled</div></div>
</div>

{{-- ══════════════════════════════════════════
     FILTER BAR
══════════════════════════════════════════ --}}
<div class="filter-bar">
  <div class="filter-search">
    <i class="bi bi-search"></i>
    <input type="text" class="form-control" id="searchInput"
           placeholder="Search SR_ID, client, site…" oninput="applyFilter()"/>
  </div>
  <div style="display:flex;align-items:center;gap:6px;">
    <label>Status</label>
    <select class="form-select" style="width:160px;" id="statusFilter" onchange="applyFilter()">
      <option value="">All Statuses</option>
      <option>Pending</option>
      <option>In Progress</option>
      <option>Pending Review</option>
      <option>Rework</option>
      <option>Completed</option>
      <option>Cancelled</option>
    </select>
  </div>
  <div style="display:flex;align-items:center;gap:6px;">
    <label>Warranty</label>
    <select class="form-select" style="width:140px;" id="warrantyFilter" onchange="applyFilter()">
      <option value="">All</option>
      <option value="Covered">Covered</option>
      <option value="Not Covered">Not Covered</option>
    </select>
  </div>
  <span class="filter-count" id="filterCount"></span>
  <div class="view-switcher">
    <button class="vs-btn active" data-view="kanban" onclick="switchView('kanban')" title="Kanban View">
      <i class="bi bi-kanban"></i>
    </button>
    <button class="vs-btn" data-view="list" onclick="switchView('list')" title="List View">
      <i class="bi bi-list-ul"></i>
    </button>
  </div>
  <button class="btn-sm-outline"
          onclick="showToast('success','Exported','Filtered view exported to CSV.')">
    <i class="bi bi-download"></i>Export
  </button>
</div>

<p class="scroll-hint"><i class="bi bi-arrow-left-right me-1"></i>Swipe left / right to see all columns</p>

{{-- Kanban board --}}
<div class="kanban-wrapper" id="kanbanView">
  <div class="kanban-board" id="kanbanBoard"></div>
</div>

{{-- Mobile list view --}}
<div class="mobile-list-view" id="listView"></div>

@endsection

@push('scripts')
<script>
/* ════════════════════════════════════════════
   TICKET SUMMARY · Kanban View JS
════════════════════════════════════════════ */

/* ── Mock data ── */
const TECHS = [
  {id:'USR-001',name:'Rahul Mehta',  initials:'RM'},
  {id:'USR-002',name:'Sana Patel',   initials:'SP'},
  {id:'USR-003',name:'James Okoye',  initials:'JO'},
  {id:'USR-004',name:'Lin Wei',      initials:'LW'},
];
const STATUSES = ['Pending','In Progress','Pending Review','Rework','Completed','Cancelled'];
const CLIENTS  = [
  {name:'Skyline Technologies Pvt Ltd',contract:'CTR-20241001',code:'CUST-1234'},
  {name:'Meridian Constructions Ltd',  contract:'CTR-20242002',code:'CUST-2002'},
  {name:'Apex Retail Group',           contract:'CTR-20243003',code:'CUST-3003'},
  {name:'Gulf Maritime Corp',          contract:'CTR-20244004',code:'CUST-4004'},
  {name:'Nova Healthcare LLC',         contract:'CTR-20245005',code:'CUST-5005'},
];
const SITES      = ['Site A - HQ Tower','Site B - Warehouse','Site C - Data Centre','Site D - Branch Office','Site E - Mall Outlet'];
const CATEGORIES = ['HVAC Service','Electrical Fault','Plumbing Repair','IT Infrastructure','Civil Work'];

function rnd(a)    { return a[Math.floor(Math.random()*a.length)]; }
function rndInt(a,b){ return Math.floor(Math.random()*(b-a+1))+a; }
function rndDate(d) { const x=new Date(); x.setDate(x.getDate()-rndInt(0,d)); x.setHours(rndInt(8,18),rndInt(0,59)); return x; }
function fmtDate(d) { return d.toLocaleDateString('en-GB',{day:'2-digit',month:'short',year:'numeric'})+' '+d.toLocaleTimeString('en-IN',{hour:'2-digit',minute:'2-digit'}); }
function elapsed(d) {
  const h=Math.floor((Date.now()-d.getTime())/3600000);
  const m=Math.floor(((Date.now()-d.getTime())%3600000)/60000);
  if(h>48)  return {label:`${Math.floor(h/24)}d ${h%24}h`,cls:'timer-crit'};
  if(h>8)   return {label:`${h}h ${m}m`,cls:'timer-warn'};
  return {label:`${h}h ${m}m`,cls:'timer-ok'};
}

/* Generate 24 tickets */
const TICKETS = [];
for(let i=1;i<=24;i++){
  const status  = STATUSES[rndInt(0,5)];
  const client  = rnd(CLIENTS);
  const created = rndDate(30);
  const tech    = rnd(TECHS);
  const mover   = rnd(TECHS);
  const movedAt = rndDate(7);
  TICKETS.push({
    id: `SR-2024-${String(10000+i).slice(1)}`,
    client: client.name, contract: client.contract, code: client.code,
    site: rnd(SITES), category: rnd(CATEGORIES), status,
    warranty: Math.random() > 0.4 ? 'Covered' : 'Not Covered',
    created, createdStr: fmtDate(created),
    tech, mover, movedAt, movedAtStr: fmtDate(movedAt),
    hasPhotos:    ['In Progress','Pending Review','Rework','Completed'].includes(status),
    hasSignature: status === 'Completed',
    hasMapPin:    ['In Progress','Pending Review','Rework','Completed'].includes(status),
    lat: (25.2 + Math.random()*.1).toFixed(6),
    lng: (55.27+ Math.random()*.1).toFixed(6),
  });
}

let filteredTickets = [...TICKETS];

/* ── Status config ── */
const STATUS_CFG = {
  'Pending':        {cls:'lane-pending',color:'#f5c842',countId:'cnt-pending', badgeStyle:'background:rgba(245,200,66,.15);color:#a8802a;'},
  'In Progress':    {cls:'lane-inprog', color:'#4895ef',countId:'cnt-inprog',  badgeStyle:'background:rgba(72,149,239,.15);color:#1a5fad;'},
  'Pending Review': {cls:'lane-review', color:'#b44fd4',countId:'cnt-review',  badgeStyle:'background:rgba(180,79,212,.15);color:#7a2f96;'},
  'Rework':         {cls:'lane-rework', color:'#ff3366',countId:'cnt-rework',  badgeStyle:'background:rgba(255,51,102,.15);color:#8a1b36;'},
  'Completed':      {cls:'lane-done',   color:'#05a34a',countId:'cnt-done',    badgeStyle:'background:rgba(5,163,74,.15);color:#0a5c2e;'},
  'Cancelled':      {cls:'lane-cancel', color:'#aeb7c5',countId:'cnt-cancel',  badgeStyle:'background:rgba(174,183,197,.15);color:#5a6a7e;'},
};

/* ── Build kanban card ── */
function buildCard(t) {
  const el   = elapsed(t.created);
  const wCls = t.warranty === 'Covered' ? 'warranty-covered' : 'warranty-not';
  const wIco = t.warranty === 'Covered' ? 'bi-shield-check'  : 'bi-shield-x';

  const photoBtns = t.hasPhotos
    ? `<div class="kc-action-btn before-ph" onclick="openPhoto('before','${t.id}')" title="Before Photo"><i class="bi bi-camera-fill"></i></div>
       <div class="kc-action-btn after-ph"  onclick="openPhoto('after','${t.id}')"  title="After Photo"><i class="bi bi-camera-video-fill"></i></div>`
    : `<div class="kc-action-btn" title="Before Photo (N/A)" style="opacity:.35;cursor:default;"><i class="bi bi-camera-fill"></i></div>
       <div class="kc-action-btn" title="After Photo (N/A)"  style="opacity:.35;cursor:default;"><i class="bi bi-camera-video-fill"></i></div>`;

  const dlBtn = t.hasSignature
    ? `<div class="kc-action-btn dl-btn" onclick="downloadAcceptance('${t.id}')" title="Download Acceptance Sheet"><i class="bi bi-file-earmark-arrow-down-fill"></i></div>`
    : `<div class="kc-action-btn" title="Acceptance Sheet (N/A)" style="opacity:.35;cursor:default;"><i class="bi bi-file-earmark-arrow-down-fill"></i></div>`;

  const mapBtn = t.hasMapPin
    ? `<div class="kc-action-btn maps" onclick="openMap('${t.lat}','${t.lng}','${t.id}')" title="View on Google Maps"><i class="bi bi-geo-alt-fill"></i></div>`
    : `<div class="kc-action-btn" title="Map Pin (N/A)" style="opacity:.35;cursor:default;"><i class="bi bi-geo-alt-fill"></i></div>`;

  return `<div class="kcard">
    <div class="kc-top">
      <span class="kc-sr">${t.id}</span>
      <span class="kc-warranty ${wCls}"><i class="bi ${wIco} me-1"></i>${t.warranty}</span>
    </div>
    <div class="kc-client">${t.client}</div>
    <div class="kc-contract"><i class="bi bi-file-earmark-text me-1"></i>${t.contract}</div>
    <hr class="kc-divider"/>
    <div class="kc-timer-row">
      <div class="kc-timer ${el.cls}"><i class="bi bi-clock me-1"></i>${el.label}</div>
      <span class="kc-site"><i class="bi bi-geo me-1"></i>${t.site.split(' - ')[0]}</span>
    </div>
    <div class="kc-staff">
      <div class="staff-label">Last moved by</div>
      <div class="staff-name">
        <div style="width:18px;height:18px;border-radius:50%;background:linear-gradient(135deg,#6571ff,#a78bfa);display:flex;align-items:center;justify-content:center;color:#fff;font-size:.55rem;font-weight:700;">${t.mover.initials}</div>
        ${t.mover.name} <span style="color:var(--text-muted);font-weight:400;">(${t.mover.id})</span>
      </div>
      <div class="staff-meta"><i class="bi bi-calendar3 me-1"></i>${t.movedAtStr}</div>
    </div>
    <div class="kc-actions">
      ${photoBtns}${dlBtn}${mapBtn}
      <span class="kc-action-label" style="margin-left:auto;font-size:.62rem;">${t.category}</span>
    </div>
    <div class="kc-tech">
      <div class="tech-av">${t.tech.initials}</div>
      <span class="tech-name">${t.tech.name}</span>
    </div>
  </div>`;
}

/* ── Build mobile list card ── */
function buildMobileCard(t) {
  const scfg = STATUS_CFG[t.status];
  const el   = elapsed(t.created);
  const wCls = t.warranty === 'Covered' ? 'warranty-covered' : 'warranty-not';

  const photoBtns = t.hasPhotos
    ? `<button class="mlc-action-btn before-ph" onclick="openPhoto('before','${t.id}')"><i class="bi bi-camera-fill"></i><span>Before</span></button>
       <button class="mlc-action-btn after-ph"  onclick="openPhoto('after','${t.id}')"><i class="bi bi-camera-video-fill"></i><span>After</span></button>`
    : `<button class="mlc-action-btn" style="opacity:.35;cursor:default;"><i class="bi bi-camera-fill"></i><span>Before</span></button>
       <button class="mlc-action-btn" style="opacity:.35;cursor:default;"><i class="bi bi-camera-video-fill"></i><span>After</span></button>`;

  const dlBtn = t.hasSignature
    ? `<button class="mlc-action-btn dl-btn" onclick="downloadAcceptance('${t.id}')"><i class="bi bi-file-earmark-arrow-down-fill"></i><span>Sheet</span></button>`
    : `<button class="mlc-action-btn" style="opacity:.35;cursor:default;"><i class="bi bi-file-earmark-arrow-down-fill"></i><span>Sheet</span></button>`;

  const mapBtn = t.hasMapPin
    ? `<button class="mlc-action-btn maps" onclick="openMap('${t.lat}','${t.lng}','${t.id}')"><i class="bi bi-geo-alt-fill"></i><span>Map</span></button>`
    : `<button class="mlc-action-btn" style="opacity:.35;cursor:default;"><i class="bi bi-geo-alt-fill"></i><span>Map</span></button>`;

  return `<div class="mobile-list-card">
    <div class="mlc-top">
      <div>
        <div class="mlc-sr">${t.id}</div>
        <div class="mlc-client">${t.client}</div>
      </div>
      <div style="display:flex;flex-direction:column;align-items:flex-end;gap:4px;">
        <span class="mlc-status" style="${scfg.badgeStyle}">${t.status}</span>
        <span class="kc-warranty ${wCls}" style="font-size:.6rem;">${t.warranty === 'Covered' ? '✓ Covered' : '✗ Not Covered'}</span>
      </div>
    </div>
    <div class="mlc-meta">
      <span><i class="bi bi-file-earmark-text"></i>${t.contract}</span>
      <span><i class="bi bi-geo"></i>${t.site.split(' - ')[0]}</span>
      <span><i class="bi bi-clock"></i>${el.label}</span>
      <span><i class="bi bi-person"></i>${t.tech.name}</span>
    </div>
    <div class="mlc-actions">${photoBtns}${dlBtn}${mapBtn}</div>
  </div>`;
}

/* ── Render board ── */
function renderBoard() {
  const board = document.getElementById('kanbanBoard');
  board.innerHTML = '';

  /* Update stats */
  STATUSES.forEach(s => {
    const el = document.getElementById(STATUS_CFG[s].countId);
    if(el) el.textContent = filteredTickets.filter(t => t.status === s).length;
  });

  document.getElementById('filterCount').textContent =
    `${filteredTickets.length} of ${TICKETS.length} tickets`;

  /* Build lanes */
  STATUSES.forEach(s => {
    const cfg   = STATUS_CFG[s];
    const cards = filteredTickets.filter(t => t.status === s);
    const lane  = document.createElement('div');
    lane.className = `lane ${cfg.cls}`;
    lane.innerHTML = `
      <div class="lane-header">
        <div class="lane-title"><div class="lane-dot"></div>${s}</div>
        <span class="lane-count">${cards.length}</span>
      </div>
      <div class="lane-body">
        ${cards.length
          ? cards.map(buildCard).join('')
          : '<div class="lane-empty"><i class="bi bi-inbox"></i>No tickets</div>'}
      </div>`;
    board.appendChild(lane);
  });

  /* Mobile list */
  const listView = document.getElementById('listView');
  listView.innerHTML = filteredTickets.length
    ? filteredTickets.map(buildMobileCard).join('')
    : '<div style="text-align:center;padding:40px;color:var(--text-muted);"><i class="bi bi-inbox" style="font-size:2rem;display:block;margin-bottom:8px;opacity:.3;"></i>No tickets match your filter.</div>';
}

/* ── Filter ── */
function applyFilter() {
  const q  = document.getElementById('searchInput').value.toLowerCase().trim();
  const st = document.getElementById('statusFilter').value;
  const wr = document.getElementById('warrantyFilter').value;
  filteredTickets = TICKETS.filter(t => {
    const mq = !q  || t.id.toLowerCase().includes(q) || t.client.toLowerCase().includes(q) || t.site.toLowerCase().includes(q);
    const ms = !st || t.status === st;
    const mw = !wr || t.warranty === wr;
    return mq && ms && mw;
  });
  renderBoard();
}

/* ── View switcher ── */
let currentView = 'kanban';
function switchView(v) {
  currentView = v;
  document.querySelectorAll('.vs-btn').forEach(b => b.classList.toggle('active', b.dataset.view === v));
  document.getElementById('kanbanView').style.display = v === 'kanban' ? 'block' : 'none';
  document.getElementById('listView').style.display   = v === 'list'   ? 'block' : 'none';
}

/* ── Photo lightbox ── */
function openPhoto(type, srId) {
  const t = TICKETS.find(x => x.id === srId);
  if(!t) return;
  document.getElementById('lbTitle').textContent = `${type === 'before' ? 'Before' : 'After'} Photo — ${srId}`;
  const colors = ['#6571ff','#4895ef','#05a34a','#b44fd4','#fbbc06'];
  const col    = colors[Math.floor(Math.random() * colors.length)];
  document.getElementById('lbImgWrap').innerHTML = `
    <div style="width:100%;height:280px;background:linear-gradient(135deg,${col}22,${col}44);display:flex;flex-direction:column;align-items:center;justify-content:center;gap:10px;border-radius:8px;">
      <i class="bi bi-${type === 'before' ? 'camera-fill' : 'camera-video-fill'}" style="font-size:3rem;color:${col};opacity:.6;"></i>
      <span style="color:var(--text-muted);font-size:.85rem;">${type === 'before' ? 'Before' : 'After'} Photo · ${srId}</span>
      <span style="font-size:.72rem;color:var(--text-light);">[Demo placeholder — production loads real image]</span>
    </div>`;
  document.getElementById('lbMeta').innerHTML = `
    <span><i class="bi bi-person me-1"></i>${t.tech.name} (${t.tech.id})</span>
    <span><i class="bi bi-geo-alt me-1"></i>${t.site}</span>
    <span><i class="bi bi-calendar3 me-1"></i>${t.movedAtStr}</span>
    <span><i class="bi bi-building me-1"></i>${t.client}</span>`;
  document.getElementById('lightbox').classList.add('show');
}
function closeLightbox() { document.getElementById('lightbox').classList.remove('show'); }
document.getElementById('lightbox').addEventListener('click', function(e) { if(e.target === this) closeLightbox(); });

/* ── Download acceptance sheet ── */
function downloadAcceptance(srId) {
  showToast('success','Downloading','Acceptance sheet for ' + srId + ' downloading as PDF…');
}

/* ── Google Maps ── */
function openMap(lat, lng, srId) {
  window.open(`https://www.google.com/maps?q=${lat},${lng}`, '_blank');
  showToast('primary','Maps Opened','Technician punch-in location for ' + srId + '.');
}

/* ── Toast ── */
function showToast(type, title, body) {
  const w   = document.getElementById('toastWrap');
  const ico = {success:'bi-check-circle-fill', error:'bi-x-circle-fill', primary:'bi-info-circle-fill'};
  const t   = document.createElement('div');
  t.className = `toast-item${type === 'error' ? ' error' : type === 'success' ? ' success' : ''}`;
  t.innerHTML = `<i class="bi ${ico[type] || 'bi-info-circle-fill'} ti-icon ${type}"></i>
    <div><p class="ti-title">${title}</p><p class="ti-body">${body}</p></div>`;
  w.appendChild(t);
  setTimeout(() => { t.style.opacity='0'; t.style.transition='opacity .3s'; setTimeout(()=>t.remove(),300); }, 3500);
}

/* ── Init ── */
renderBoard();
</script>
@endpush