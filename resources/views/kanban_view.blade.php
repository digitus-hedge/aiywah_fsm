@extends('layouts.layout')

@section('title', 'Ticket Summary - Digit-Us Portal')
@section('page_title', 'Ticket Summary')
@section('page_icon', 'kanban')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet"/>
<style>
/* ═══════════════════════════════════════
   KANBAN-SCOPED TOKENS
═══════════════════════════════════════ */
:root,[data-theme="light"],[data-bs-theme="light"]{
  --kb-overlay:rgba(9,15,35,.6);
  --kb-modal-shadow:0 24px 64px rgba(0,0,0,.16);
  --kb-hover-shadow:0 6px 24px rgba(100,120,160,.18);
}
[data-theme="dark"],[data-bs-theme="dark"]{
  --kb-overlay:rgba(0,0,0,.75);
  --kb-modal-shadow:0 24px 64px rgba(0,0,0,.55);
  --kb-hover-shadow:0 6px 24px rgba(0,0,0,.5);
}

/* ═══════════════════════════════════════
   PAGE HEADER
═══════════════════════════════════════ */
.pg-header{background: linear-gradient(2deg, #13a3d8 0%, #b5b7b9 100%);border-radius:10px;padding:20px 24px;margin-bottom:20px;color:#fff;position:relative;overflow:hidden;}
.pg-header::before{content:'';position:absolute;left:-40px;bottom:-40px;width:180px;height:180px;border-radius:50%;background:rgba(255,255,255,.05);}
.pg-header::after{content:'';position:absolute;right:-30px;top:-30px;width:160px;height:160px;border-radius:50%;background:rgba(255,255,255,.07);}
.pg-header h4{font-size:1rem;font-weight:600;margin:0 0 3px;position:relative;z-index:1;color:#fff;}
.pg-header p{font-size:.78rem;margin:0;opacity:.85;position:relative;z-index:1;color:#fff;}
.pg-header .meta-row{display:flex;align-items:center;gap:10px;margin-top:10px;position:relative;z-index:1;flex-wrap:wrap;}
.meta-badge{background:rgba(255,255,255,.18);border:1px solid rgba(255,255,255,.3);border-radius:20px;font-size:.6875rem;padding:2px 10px;font-weight:500;}
.view-only-tag{background:rgba(255,255,255,.25);border:1px solid rgba(255,255,255,.4);border-radius:6px;font-size:.6875rem;padding:3px 10px;font-weight:600;display:flex;align-items:center;gap:5px;}
@media(max-width:575.98px){.pg-header{padding:14px 16px;}.pg-header h4{font-size:.9rem;}}

/* ═══════════════════════════════════════
   STATS STRIP
═══════════════════════════════════════ */
.stats-strip{display:grid;grid-template-columns:repeat(7,1fr);gap:10px;margin-bottom:18px;}
.stat-card{background:var(--card-bg);border:1px solid var(--card-border);border-radius:8px;padding:12px 10px;text-align:center;box-shadow:var(--card-shadow);transition:transform .2s,border-color .15s;cursor:pointer;}
.stat-card:hover{transform:translateY(-2px);}
.stat-card.active{border-color:#9a8053;box-shadow:0 0 0 2px rgba(154,128,83,.16);}
.stat-num{font-size:1.4rem;font-weight:700;line-height:1;margin-bottom:2px;margin-right: 5px;}
.stat-lbl{font-size:.6875rem;color:var(--text-muted);font-weight:500;line-height:1.25;}
@media(max-width:1199.98px){.stats-strip{grid-template-columns:repeat(4,1fr);}}
@media(max-width:767.98px){.stats-strip{grid-template-columns:repeat(3,1fr);}}
@media(max-width:479.98px){.stats-strip{grid-template-columns:repeat(2,1fr);}}

/* ═══════════════════════════════════════
   FILTER BAR
═══════════════════════════════════════ */
.filter-bar{background:var(--card-bg);border:1px solid var(--card-border);border-radius:8px;padding:14px 16px;margin-bottom:18px;box-shadow:var(--card-shadow);display:flex;align-items:center;gap:10px;flex-wrap:wrap;}
.filter-bar .form-control,.filter-bar .form-select{font-size:.8rem;border:1px solid var(--border-color);border-radius:6px;padding:.38rem .75rem;color:var(--text-primary);background:var(--input-bg);height:36px;}
.filter-bar .form-control:focus,.filter-bar .form-select:focus{border-color:rgba(154,128,83,.5);box-shadow:0 0 0 3px rgba(154,128,83,.12);outline:none;}
.filter-bar .form-control::placeholder{color:var(--text-light);}
.filter-search{position:relative;flex:1;min-width:200px;}
.filter-search i{position:absolute;left:10px;top:50%;transform:translateY(-50%);color:var(--text-muted);font-size:.85rem;}
.filter-search input{padding-left:32px;width:100%;}
.filter-bar label{font-size:.72rem;color:var(--text-muted);font-weight:500;white-space:nowrap;}
[data-theme="dark"] .form-select option,[data-bs-theme="dark"] .form-select option{background:#221f16;color:#ded8c8;}
.filter-count{font-size:.72rem;color:var(--text-muted);white-space:nowrap;margin-left:auto;}
.btn-sm-outline{background:transparent;border:1px solid var(--border-color);border-radius:6px;padding:.3rem .75rem;font-size:.78rem;color:var(--text-muted);cursor:pointer;display:inline-flex;align-items:center;gap:5px;transition:all .15s;height:36px;}
.btn-sm-outline:hover{border-color:#9a8053;color:#9a8053;}
@media(max-width:767.98px){.filter-bar{gap:8px;}.filter-search{min-width:100%;order:-1;}}

.view-switcher{display:flex;gap:6px;align-items:center;}
.vs-btn{width:32px;height:32px;display:flex;align-items:center;justify-content:center;border-radius:6px;border:1px solid var(--border-color);background:var(--surface-2);cursor:pointer;color:var(--text-muted);font-size:.9rem;transition:all .15s;}
.vs-btn.active,.vs-btn:hover{background:rgba(154,128,83,.1);border-color:#13a3d8;color:#13a3d8;}

/* ═══════════════════════════════════════
   KANBAN BOARD
═══════════════════════════════════════ */
.kanban-wrapper{overflow-x:auto;padding-bottom:16px;-webkit-overflow-scrolling:touch;}
.kanban-wrapper::-webkit-scrollbar{height:6px;}
.kanban-wrapper::-webkit-scrollbar-thumb{background:var(--border-color);border-radius:3px;}
.kanban-board{display:flex;gap:14px;min-width:max-content;align-items:flex-start;padding:2px 2px 8px;}

.lane{width:290px;flex-shrink:0;display:flex;flex-direction:column;}
.lane-header{border-radius:8px 8px 0 0;padding:11px 14px;display:flex;align-items:center;justify-content:space-between;gap:8px;}
.lane-title{display:flex;align-items:center;gap:8px;font-size:.8125rem;font-weight:600;color:var(--text-heading);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
.lane-dot{width:9px;height:9px;border-radius:50%;flex-shrink:0;}
.lane-count{font-size:.6875rem;font-weight:700;padding:2px 8px;border-radius:10px;flex-shrink:0;}
.lane-body{background:var(--surface-2);border:1px solid var(--card-border);border-top:none;border-radius:0 0 8px 8px;padding:10px 9px;min-height:200px;display:flex;flex-direction:column;gap:10px;}
.lane-empty{text-align:center;padding:28px 16px;color:var(--text-light);font-size:.78rem;}
.lane-empty i{font-size:1.6rem;display:block;margin-bottom:6px;opacity:.4;}
.lane-note{font-size:.65rem;color:var(--text-muted);line-height:1.45;padding:0 2px;}

/* Show more / show less */
.lane-more{width:100%;padding:8px;background:var(--card-bg);border:1px dashed var(--border-color);border-radius:7px;color:#9a8053;font-size:.72rem;font-weight:600;cursor:pointer;transition:background .15s,border-color .15s;display:flex;align-items:center;justify-content:center;gap:5px;}
.lane-more:hover{background:rgba(154,128,83,.1);border-color:#9a8053;}

/* ═══════════════════════════════════════
   KANBAN CARD
═══════════════════════════════════════ */
.kcard{background:var(--card-bg);border:1px solid var(--card-border);border-radius:8px;padding:12px 13px;cursor:default;box-shadow:var(--card-shadow);transition:box-shadow .2s,transform .2s,border-color .2s;position:relative;}
.kcard:hover{box-shadow:var(--kb-hover-shadow);transform:translateY(-2px);}

.kc-top{display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:9px;gap:6px;}
.kc-sr{font-size:.8rem;font-weight:700;color:#9a8053;letter-spacing:.02em;}
.kc-warranty{font-size:.6rem;font-weight:700;padding:2px 7px;border-radius:10px;white-space:nowrap;flex-shrink:0;}
.warranty-covered{background:rgba(5,163,74,.12);color:#05a34a;}
.warranty-not{background:rgba(255,51,102,.12);color:#ff3366;}

.kc-client{font-size:.78rem;font-weight:500;color:var(--text-heading);margin-bottom:2px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;}
.kc-contract{font-size:.7rem;color:var(--text-muted);margin-bottom:9px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;}
.kc-divider{border-color:var(--card-border);margin:8px 0;}

.kc-timer-row{display:flex;align-items:center;gap:6px;margin-bottom:8px;}
.kc-timer{font-size:.7rem;font-weight:600;padding:3px 8px;border-radius:6px;display:flex;align-items:center;gap:4px;}
.timer-ok{background:rgba(5,163,74,.1);color:#05a34a;}
.timer-warn{background:rgba(251,188,6,.12);color:#a8802a;}
.timer-crit{background:rgba(255,51,102,.12);color:#ff3366;}
.kc-site{font-size:.7rem;color:var(--text-muted);margin-left:auto;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:110px;text-align:right;}

/* Staff credit - revealed on hover */
.kc-staff{background:var(--surface-2);border:1px solid var(--border-color);border-radius:6px;padding:7px 9px;margin-bottom:8px;font-size:.7rem;display:none;}
.kcard:hover .kc-staff{display:block;}
.kc-staff .staff-label{color:var(--text-muted);font-size:.65rem;text-transform:uppercase;letter-spacing:.05em;margin-bottom:3px;}
.kc-staff .staff-name{font-weight:600;color:var(--text-heading);display:flex;align-items:center;gap:5px;}
.kc-staff .staff-meta{color:var(--text-muted);margin-top:2px;}

/* Completion artefacts - only rendered on Completed / Archived cards */
.kc-actions{display:flex;align-items:center;gap:6px;margin-top:8px;}
.kc-action-btn{width:30px;height:30px;border-radius:6px;border:1px solid var(--border-color);background:var(--surface-2);display:flex;align-items:center;justify-content:center;cursor:pointer;font-size:.85rem;transition:all .15s;flex-shrink:0;color:var(--text-muted);}
.kc-action-btn.before-ph:hover{border-color:#fbbc06;color:#a8802a;background:rgba(251,188,6,.1);}
.kc-action-btn.after-ph:hover{border-color:#05a34a;color:#05a34a;background:rgba(5,163,74,.1);}
.kc-action-btn.pdf-btn:hover{border-color:#ff3366;color:#ff3366;background:rgba(255,51,102,.08);}
.kc-action-btn.off{opacity:.35;cursor:default;}
.kc-action-btn.off:hover{border-color:var(--border-color);color:var(--text-muted);background:var(--surface-2);}
.kc-action-label{font-size:.62rem;color:var(--text-muted);margin-left:auto;text-align:right;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:96px;}
.kc-cat-only{font-size:.62rem;color:var(--text-muted);margin-top:8px;text-align:right;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;}

.kc-tech{display:flex;align-items:center;gap:6px;margin-top:7px;}
.tech-av{width:22px;height:22px;border-radius:50%;background:linear-gradient(2deg, #13a3d8 0%, #b5b7b9 100%);display:flex;align-items:center;justify-content:center;color:#fff;font-size:.6rem;font-weight:700;flex-shrink:0;}
.tech-name{font-size:.7rem;color:var(--text-muted);}

/* ═══════════════════════════════════════
   LIST VIEW
═══════════════════════════════════════ */
.mobile-list-view{display:none;}
.mobile-list-card{background:var(--card-bg);border:1px solid var(--card-border);border-radius:8px;padding:14px;margin-bottom:10px;box-shadow:var(--card-shadow);}
.mlc-top{display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:8px;gap:8px;}
.mlc-sr{font-size:.875rem;font-weight:700;color:#9a8053;}
.mlc-status{font-size:.65rem;font-weight:700;padding:3px 9px;border-radius:20px;white-space:nowrap;}
.mlc-client{font-size:.82rem;font-weight:500;color:var(--text-heading);}
.mlc-meta{display:flex;gap:10px;margin-top:5px;flex-wrap:wrap;}
.mlc-meta span{font-size:.7rem;color:var(--text-muted);display:flex;align-items:center;gap:4px;}
.mlc-actions{display:flex;gap:8px;margin-top:10px;padding-top:10px;border-top:1px solid var(--card-border);}
.mlc-action-btn{flex:1;display:flex;flex-direction:column;align-items:center;gap:3px;padding:6px;border-radius:6px;border:1px solid var(--border-color);background:var(--surface-2);cursor:pointer;font-size:.65rem;color:var(--text-muted);transition:all .15s;}
.mlc-action-btn i{font-size:1rem;}
.mlc-action-btn:hover{border-color:#9a8053;color:#9a8053;background:rgba(154,128,83,.07);}
.mlc-action-btn.off{opacity:.35;cursor:default;}
.list-more{width:100%;padding:10px;margin-top:4px;background:var(--card-bg);border:1px dashed var(--border-color);border-radius:8px;color:#9a8053;font-size:.78rem;font-weight:600;cursor:pointer;}
.list-more:hover{background:rgba(154,128,83,.1);border-color:#9a8053;}

.kc-sr.sr-ref-trigger,
.mlc-sr.sr-ref-trigger{
  cursor:pointer;
  text-decoration:none;
  border-bottom:1px dashed rgba(154,128,83,.4);
  transition:border-color .12s;
}
.kc-sr.sr-ref-trigger:hover,
.mlc-sr.sr-ref-trigger:hover{
  border-bottom-style:solid;
}

.scroll-hint{display:none;font-size:.72rem;color:var(--text-muted);text-align:center;padding:6px 0 10px;margin-top:-8px;}
@media(max-width:767.98px){.scroll-hint{display:block;}}


.sr-ref-trigger
{
  color:#13a3d8 !important;
}

/* ═══════════════════════════════════════
   PHOTO LIGHTBOX
═══════════════════════════════════════ */
.lightbox-overlay{display:none;position:fixed;inset:0;background:var(--kb-overlay);z-index:9000;align-items:center;justify-content:center;padding:20px;}
.lightbox-overlay.show{display:flex;}
.lightbox-modal{background:var(--modal-bg,var(--card-bg));border-radius:12px;border:1px solid var(--card-border);max-width:680px;width:100%;box-shadow:var(--kb-modal-shadow);animation:popIn .25s ease;overflow:hidden;}
.lb-header{display:flex;align-items:center;justify-content:space-between;padding:14px 18px;border-bottom:1px solid var(--card-border);}
.lb-header h6{margin:0;font-size:.875rem;font-weight:600;color:var(--text-heading);}
.lb-close{background:none;border:none;color:var(--text-muted);cursor:pointer;font-size:1.1rem;padding:4px;border-radius:4px;line-height:1;}
.lb-close:hover{color:var(--text-heading);background:var(--surface-2);}
.lb-body{padding:18px;}
.lb-img-wrap{background:var(--surface-2);border-radius:8px;overflow:hidden;text-align:center;min-height:280px;display:flex;align-items:center;justify-content:center;}
.lb-img-wrap img{max-width:100%;max-height:400px;object-fit:contain;}
.lb-thumbs{display:flex;gap:6px;margin-top:10px;flex-wrap:wrap;}
.lb-thumb{width:52px;height:52px;border-radius:6px;overflow:hidden;border:2px solid transparent;cursor:pointer;flex-shrink:0;background:var(--surface-2);}
.lb-thumb img{width:100%;height:100%;object-fit:cover;}
.lb-thumb.active{border-color:#9a8053;}
.lb-meta{margin-top:12px;font-size:.75rem;color:var(--text-muted);display:flex;gap:14px;flex-wrap:wrap;}
.lb-meta span i{margin-right:4px;}
@keyframes popIn{from{transform:scale(.88);opacity:0;}to{transform:none;opacity:1;}}

/* ═══════════════════════════════════════
   TOAST
═══════════════════════════════════════ */
.toast-wrap{position:fixed;top:70px;right:16px;z-index:9999;display:flex;flex-direction:column;gap:8px;max-width:300px;}
.toast-item{background:var(--card-bg);border-left:4px solid #9a8053;border-radius:7px;padding:12px 14px;box-shadow:0 6px 24px rgba(0,0,0,.18);display:flex;align-items:flex-start;gap:10px;animation:toastIn .3s ease;}
.toast-item.success{border-color:#05a34a;}.toast-item.error{border-color:#ff3366;}
@keyframes toastIn{from{transform:translateX(40px);opacity:0;}to{transform:none;opacity:1;}}
.ti-icon{font-size:1.1rem;flex-shrink:0;margin-top:1px;}
.ti-icon.primary{color:#9a8053;}.ti-icon.success{color:#05a34a;}.ti-icon.error{color:#ff3366;}
.ti-title{font-size:.8125rem;font-weight:600;margin:0 0 2px;color:var(--text-heading);}
.ti-body{font-size:.72rem;margin:0;color:var(--text-muted);}
@media(max-width:575.98px){.toast-wrap{left:12px;right:12px;max-width:none;}}
</style>
@endpush

@section('content')

{{-- TOASTS --}}
<div class="toast-wrap" id="toastWrap"></div>

{{-- PHOTO LIGHTBOX --}}
<div class="lightbox-overlay" id="lightbox">
  <div class="lightbox-modal">
    <div class="lb-header">
      <h6 id="lbTitle">Photo viewer</h6>
      <button class="lb-close" onclick="closeLightbox()"><i class="bi bi-x-lg"></i></button>
    </div>
    <div class="lb-body">
      <div class="lb-img-wrap" id="lbImgWrap"></div>
      <div class="lb-thumbs" id="lbThumbs"></div>
      <div class="lb-meta" id="lbMeta"></div>
    </div>
  </div>
</div>

{{-- PAGE HEADER --}}
<div class="pg-header">
  <h4><i class="bi bi-kanban me-2"></i>Ticket Summary &amp; History - Kanban Card View</h4>
  <p>Live pipeline view of all service requests across lifecycle stages. Site photos and the signed sheet appear once a ticket is completed.</p>
  <div class="meta-row">
    <span class="meta-badge"><i class="bi bi-person-badge me-1"></i>Super Admin</span>
    <span class="meta-badge"><i class="bi bi-person-gear me-1"></i>Admin</span>
    <span class="meta-badge"><i class="bi bi-briefcase me-1"></i>Head of Projects</span>
    <span class="meta-badge"><i class="bi bi-calculator me-1"></i>Accounts</span>
    <span class="view-only-tag"><i class="bi bi-eye-slash"></i>View only - no drag</span>
  </div>
</div>

{{-- STATS STRIP --}}
<div class="stats-strip" id="statsStrip">
  @foreach($statGroups as $g)
    <div class="stat-card" data-group="{{ $g['key'] }}" onclick="filterByGroup('{{ $g['key'] }}')">
      <div class="stat-num" style="color:{{ $g['color'] }};" id="cnt-{{ $g['key'] }}">0</div>
      <div class="stat-lbl">{{ $g['label'] }}</div>
    </div>
  @endforeach
</div>



{{-- FILTER BAR --}}
<div class="filter-bar">
  <div class="filter-search">
    <i class="bi bi-search"></i>
    <input type="text" class="form-control" id="searchInput" placeholder="       Search SR ID, client, site…" oninput="applyFilter()"/>
  </div>
  <div style="display:flex;align-items:center;gap:6px;">
    <label>Status</label>
    <select class="form-select" style="width:170px;" id="statusFilter" onchange="applyFilter()">
      <option value="">All statuses</option>
      @foreach($statuses as $s)
        <option value="{{ $s }}">{{ $labels[$s] ?? $s }}</option>
      @endforeach
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
    <button class="vs-btn active" data-view="kanban" onclick="switchView('kanban')" title="Kanban view"><i class="bi bi-kanban"></i></button>
    <button class="vs-btn" data-view="list" onclick="switchView('list')" title="List view"><i class="bi bi-list-ul"></i></button>
  </div>
  <button class="btn-sm-outline" onclick="exportCsv()"><i class="bi bi-download"></i>Export</button>
</div>

<p class="scroll-hint"><i class="bi bi-arrow-left-right me-1"></i>Swipe left / right to see all columns</p>

{{-- KANBAN BOARD --}}
<div class="kanban-wrapper" id="kanbanView">
  <div class="kanban-board" id="kanbanBoard"></div>
</div>

{{-- LIST VIEW --}}
<div class="mobile-list-view" id="listView"></div>
@include('partials.sr_tracking_modal')
@endsection

@push('scripts')
<script>
/* ════════════════════════════════════
   SERVER DATA
════════════════════════════════════ */
const TICKETS     = @json($tickets);
const STATUSES    = @json($statuses);
const LABELS      = @json($labels);
const STATUS_CFG  = @json($statusCfg);
const STAT_GROUPS = @json($statGroups);

/* Cards rendered per lane before the "Show more" button appears. */
const PAGE_SIZE = 10;

let filteredTickets = [...TICKETS];
let expanded    = {};             // { "Completed": true } once a lane is expanded
let listLimit   = PAGE_SIZE;      // list view pages in the same 10 at a time
let activeGroup = null;           // stat-card drill-down

/* ════════════════════════════════════
   HELPERS
════════════════════════════════════ */
const esc = s => String(s ?? '').replace(/[&<>"']/g, c =>
  ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));

function cfg(status){
  return STATUS_CFG[status] || {color:'#aeb7c5', group:'cancel'};
}

/** Translucent variant of a hex - reads correctly on light and dark surfaces. */
function tint(hex, a){
  const h = String(hex || '#aeb7c5').replace('#','');
  const n = parseInt(h.length === 3 ? h.split('').map(c => c + c).join('') : h, 16);
  return `rgba(${(n>>16)&255},${(n>>8)&255},${n&255},${a})`;
}

function elapsed(iso){
  if(!iso) return {label:'-', cls:'timer-ok'};
  const ms = Date.now() - new Date(iso).getTime();
  if(ms < 0) return {label:'0h 0m', cls:'timer-ok'};
  const h = Math.floor(ms / 3600000);
  const m = Math.floor((ms % 3600000) / 60000);
  if(h > 48) return {label:`${Math.floor(h/24)}d ${h%24}h`, cls:'timer-crit'};
  if(h > 8)  return {label:`${h}h ${m}m`, cls:'timer-warn'};
  return {label:`${h}h ${m}m`, cls:'timer-ok'};
}

/* ════════════════════════════════════
   CARD
════════════════════════════════════ */
function buildCard(t){
  const el    = elapsed(t.createdRaw);
  const wCls  = t.warranty === 'Covered' ? 'warranty-covered' : 'warranty-not';
  const wIcon = t.warranty === 'Covered' ? 'bi-shield-check'  : 'bi-shield-x';

  const staff = t.mover.name
    ? `<div class="kc-staff">
         <div class="staff-label">Last moved by</div>
         <div class="staff-name">
           <div style="width:18px;height:18px;border-radius:50%;background:linear-gradient(2deg, #13a3d8 0%, #b5b7b9 100%);display:flex;align-items:center;justify-content:center;color:#fff;font-size:.55rem;font-weight:700;">${esc(t.mover.initials)}</div>
           ${esc(t.mover.name)}
         </div>
         <div class="staff-meta"><i class="bi bi-calendar3 me-1"></i>${esc(t.mover.at)}</div>
       </div>`
    : '';

  /* Two site photos and the signed sheet. These only exist once the job is
     closed, so anything still in flight shows the category on its own. */
  let footer;
  if(t.showProof){
    const beforeBtn = t.photosBefore.length
      ? `<div class="kc-action-btn before-ph" onclick="openPhoto('before','${esc(t.id)}')" title="Before photo (${t.photosBefore.length})"><i class="bi bi-image-fill"></i></div>`
      : `<div class="kc-action-btn off" title="No before photo"><i class="bi bi-image-fill"></i></div>`;

    const afterBtn = t.photosAfter.length
      ? `<div class="kc-action-btn after-ph" onclick="openPhoto('after','${esc(t.id)}')" title="After photo (${t.photosAfter.length})"><i class="bi bi-image-fill"></i></div>`
      : `<div class="kc-action-btn off" title="No after photo"><i class="bi bi-image-fill"></i></div>`;

    const pdfBtn = t.signedPdfUrl
      ? `<div class="kc-action-btn pdf-btn" onclick="openSignedSheet('${esc(t.id)}')" title="Signed acceptance sheet"><i class="bi bi-file-earmark-pdf-fill"></i></div>`
      : `<div class="kc-action-btn off" title="No signed sheet on file"><i class="bi bi-file-earmark-pdf-fill"></i></div>`;

    footer = `<div class="kc-actions">
        ${beforeBtn}${afterBtn}${pdfBtn}
        <span class="kc-action-label" title="${esc(t.category)}">${esc(t.category)}</span>
      </div>`;
  } else {
    footer = `<div class="kc-cat-only" title="${esc(t.category)}">${esc(t.category)}</div>`;
  }

  return `<div class="kcard">
    <div class="kc-top">
      <span class="kc-sr sr-ref-trigger" data-sr-id="${t.dbId}" onclick="event.stopPropagation(); openSrTracking(${t.dbId});">${esc(t.id)}</span>
      <span class="kc-warranty ${wCls}"><i class="bi ${wIcon} me-1"></i>${esc(t.warranty)}</span>
    </div>
    <div class="kc-client" title="${esc(t.client)}">${esc(t.client)}</div>
    <div class="kc-contract"><i class="bi bi-file-earmark-text me-1"></i>${esc(t.contract)}</div>
    <hr class="kc-divider"/>
    <div class="kc-timer-row">
      <div class="kc-timer ${el.cls}"><i class="bi bi-clock"></i>${el.label}</div>
      <span class="kc-site" title="${esc(t.site)}"><i class="bi bi-geo me-1"></i>${esc(t.site)}</span>
    </div>
    ${staff}
    ${footer}
    <div class="kc-tech">
      <div class="tech-av">${esc(t.techInitials)}</div>
      <span class="tech-name">${esc(t.tech)}</span>
    </div>
  </div>`;
}

/* ════════════════════════════════════
   LIST CARD
════════════════════════════════════ */
function buildListCard(t){
  const c    = cfg(t.status);
  const el   = elapsed(t.createdRaw);
  const wCls = t.warranty === 'Covered' ? 'warranty-covered' : 'warranty-not';

  const btn = (on, cls, handler, icon, label) => on
    ? `<button class="mlc-action-btn ${cls}" onclick="${handler}"><i class="bi ${icon}"></i><span>${label}</span></button>`
    : `<button class="mlc-action-btn off"><i class="bi ${icon}"></i><span>${label}</span></button>`;

  const actions = t.showProof
    ? `<div class="mlc-actions">
         ${btn(t.photosBefore.length, 'before-ph', `openPhoto('before','${esc(t.id)}')`, 'bi-image-fill', 'Before')}
         ${btn(t.photosAfter.length,  'after-ph',  `openPhoto('after','${esc(t.id)}')`,  'bi-image-fill', 'After')}
         ${btn(t.signedPdfUrl,        'pdf-btn',   `openSignedSheet('${esc(t.id)}')`,    'bi-file-earmark-pdf-fill', 'Signed')}
       </div>`
    : '';

  return `<div class="mobile-list-card">
    <div class="mlc-top">
      <div>
        <div class="mlc-sr sr-ref-trigger" data-sr-id="${t.dbId}" onclick="event.stopPropagation(); openSrTracking(${t.dbId});">${esc(t.id)}</div>
        <div class="mlc-client">${esc(t.client)}</div>
      </div>
      <div style="display:flex;flex-direction:column;align-items:flex-end;gap:4px;">
        <span class="mlc-status" style="background:${tint(c.color,.15)};color:${c.color};">${esc(LABELS[t.status] || t.status)}</span>
        <span class="kc-warranty ${wCls}" style="font-size:.6rem;">${t.warranty === 'Covered' ? '✓ Covered' : '✗ Not covered'}</span>
      </div>
    </div>
    <div class="mlc-meta">
      <span><i class="bi bi-file-earmark-text"></i>${esc(t.contract)}</span>
      <span><i class="bi bi-geo"></i>${esc(t.site)}</span>
      <span><i class="bi bi-clock"></i>${el.label}</span>
      <span><i class="bi bi-person"></i>${esc(t.tech)}</span>
    </div>
    ${actions}
  </div>`;
}

/* ════════════════════════════════════
   RENDER
════════════════════════════════════ */
function renderBoard(){
  const board = document.getElementById('kanbanBoard');
  board.innerHTML = '';

  STAT_GROUPS.forEach(g => {
    const n = filteredTickets.filter(t => cfg(t.status).group === g.key).length;
    const el = document.getElementById('cnt-' + g.key);
    if(el) el.textContent = n;
  });
  document.querySelectorAll('.stat-card').forEach(c =>
    c.classList.toggle('active', c.dataset.group === activeGroup));

  document.getElementById('filterCount').textContent =
    `${filteredTickets.length} of ${TICKETS.length} tickets`;

  STATUSES.forEach(s => {
    const c = cfg(s);
    if(activeGroup && c.group !== activeGroup) return;

    const all    = filteredTickets.filter(t => t.status === s);
    const isOpen = !!expanded[s];
    const shown  = isOpen ? all : all.slice(0, PAGE_SIZE);
    const hidden = all.length - shown.length;

    const lane = document.createElement('div');
    lane.className = 'lane';
    lane.innerHTML = `
      <div class="lane-header" style="background:${tint(c.color,.10)};border:1px solid ${tint(c.color,.45)};border-bottom:none;">
        <div class="lane-title" title="${esc(LABELS[s] || s)}">
          <div class="lane-dot" style="background:${c.color};"></div>${esc(LABELS[s] || s)}
        </div>
        <span class="lane-count" style="background:${tint(c.color,.2)};color:${c.color};">${all.length}</span>
      </div>
      <div class="lane-body">
        ${c.note ? `<div class="lane-note"><i class="bi bi-info-circle me-1"></i>${esc(c.note)}</div>` : ''}
        ${shown.length
          ? shown.map(buildCard).join('')
          : '<div class="lane-empty"><i class="bi bi-inbox"></i>No tickets</div>'}
      </div>`;
    board.appendChild(lane);

    if(hidden > 0 || (isOpen && all.length > PAGE_SIZE)){
      const btn = document.createElement('button');
      btn.className = 'lane-more';
      btn.innerHTML = hidden > 0
        ? `<i class="bi bi-chevron-down"></i>Show ${hidden} more`
        : `<i class="bi bi-chevron-up"></i>Show less`;
      btn.addEventListener('click', () => { expanded[s] = !expanded[s]; renderBoard(); });
      lane.querySelector('.lane-body').appendChild(btn);
    }
  });

  if(!board.children.length){
    board.innerHTML = `<div style="padding:40px;color:var(--text-muted);">No lanes match this filter.</div>`;
  }

  renderList();
}

function renderList(){
  const listView = document.getElementById('listView');

  if(!filteredTickets.length){
    listView.innerHTML = `<div style="text-align:center;padding:40px;color:var(--text-muted);">
        <i class="bi bi-inbox" style="font-size:2rem;display:block;margin-bottom:8px;opacity:.3;"></i>
        No tickets match your filter.
      </div>`;
    return;
  }

  const shown  = filteredTickets.slice(0, listLimit);
  const hidden = filteredTickets.length - shown.length;

  listView.innerHTML = shown.map(buildListCard).join('');

  if(hidden > 0){
    const btn = document.createElement('button');
    btn.className = 'list-more';
    btn.innerHTML = `<i class="bi bi-chevron-down me-1"></i>Show ${hidden} more`;
    btn.addEventListener('click', () => { listLimit += PAGE_SIZE; renderList(); });
    listView.appendChild(btn);
  }
}

/* ════════════════════════════════════
   FILTERS
════════════════════════════════════ */
function applyFilter(){
  const q  = document.getElementById('searchInput').value.toLowerCase().trim();
  const st = document.getElementById('statusFilter').value;
  const wr = document.getElementById('warrantyFilter').value;

  expanded  = {};            // collapse lanes whenever the result set changes
  listLimit = PAGE_SIZE;

  filteredTickets = TICKETS.filter(t => {
    const mq = !q  || t.id.toLowerCase().includes(q)
                   || (t.client || '').toLowerCase().includes(q)
                   || (t.site   || '').toLowerCase().includes(q);
    const ms = !st || t.status === st;
    const mw = !wr || t.warranty === wr;
    return mq && ms && mw;
  });
  renderBoard();
}
window.applyFilter = applyFilter;

function filterByGroup(key){
  activeGroup = (activeGroup === key) ? null : key;
  renderBoard();
}
window.filterByGroup = filterByGroup;

/* ════════════════════════════════════
   VIEW SWITCHER
════════════════════════════════════ */
function switchView(v){
  document.querySelectorAll('.vs-btn').forEach(b => b.classList.toggle('active', b.dataset.view === v));
  document.getElementById('kanbanView').style.display = v === 'kanban' ? 'block' : 'none';
  document.getElementById('listView').style.display   = v === 'list'   ? 'block' : 'none';
}
window.switchView = switchView;

/* ════════════════════════════════════
   PHOTO LIGHTBOX
════════════════════════════════════ */
let lbPhotos = [], lbIndex = 0;

function openPhoto(type, srId){
  const t = TICKETS.find(x => x.id === srId);
  if(!t) return;

  lbPhotos = type === 'before' ? t.photosBefore : t.photosAfter;
  lbIndex  = 0;
  if(!lbPhotos.length) return;

  document.getElementById('lbTitle').textContent =
    `${type === 'before' ? 'Before' : 'After'} - ${srId}`;

  document.getElementById('lbMeta').innerHTML = `
    <span><i class="bi bi-person"></i>${esc(t.tech)}</span>
    <span><i class="bi bi-geo-alt"></i>${esc(t.site)}</span>
    <span><i class="bi bi-building"></i>${esc(t.client)}</span>
    <span><i class="bi bi-images"></i>${lbPhotos.length} photo${lbPhotos.length > 1 ? 's' : ''}</span>`;

  paintLightbox();
  document.getElementById('lightbox').classList.add('show');
}
window.openPhoto = openPhoto;

function paintLightbox(){
  document.getElementById('lbImgWrap').innerHTML =
    `<img src="${esc(lbPhotos[lbIndex])}" alt="Service photo"/>`;
  document.getElementById('lbThumbs').innerHTML = lbPhotos.length > 1
    ? lbPhotos.map((p, i) =>
        `<div class="lb-thumb ${i === lbIndex ? 'active' : ''}" onclick="lbGo(${i})"><img src="${esc(p)}" alt=""/></div>`
      ).join('')
    : '';
}
function lbGo(i){ lbIndex = i; paintLightbox(); }
window.lbGo = lbGo;

function closeLightbox(){ document.getElementById('lightbox').classList.remove('show'); }
window.closeLightbox = closeLightbox;

document.getElementById('lightbox').addEventListener('click', function(e){
  if(e.target === this) closeLightbox();
});
document.addEventListener('keydown', e => { if(e.key === 'Escape') closeLightbox(); });

/* ════════════════════════════════════
   SIGNED ACCEPTANCE SHEET
════════════════════════════════════ */
function openSignedSheet(srId){
  const t = TICKETS.find(x => x.id === srId);
  if(!t || !t.signedPdfUrl) return;
  window.open(t.signedPdfUrl, '_blank');
  toast('success', 'Opening signed sheet', `Acceptance sheet for ${srId}.`);
}
window.openSignedSheet = openSignedSheet;

/* ════════════════════════════════════
   CSV EXPORT - exports exactly what's on screen
════════════════════════════════════ */
function exportCsv(){
  if(!filteredTickets.length){
    toast('error', 'Nothing to export', 'No tickets match the current filter.');
    return;
  }
  const head = ['SR ID','Client','Contract','Site','Category','Status','Warranty','Technician','Created'];
  const cell = v => `"${String(v ?? '').replace(/"/g,'""')}"`;
  const rows = filteredTickets.map(t => [
    t.id, t.client, t.contract, t.site, t.category,
    LABELS[t.status] || t.status, t.warranty, t.tech, t.createdAt
  ].map(cell).join(','));

  const blob = new Blob(['\uFEFF' + [head.map(cell).join(','), ...rows].join('\n')],
                        {type:'text/csv;charset=utf-8;'});
  const a = document.createElement('a');
  a.href = URL.createObjectURL(blob);
  a.download = `ticket_summary_${new Date().toISOString().slice(0,10)}.csv`;
  a.click();
  URL.revokeObjectURL(a.href);
  toast('success', 'Exported', `${filteredTickets.length} tickets written to CSV.`);
}
window.exportCsv = exportCsv;

/* ════════════════════════════════════
   TOAST
════════════════════════════════════ */
function toast(type, title, body){
  const w = document.getElementById('toastWrap');
  const icons = {success:'bi-check-circle-fill', error:'bi-x-circle-fill', primary:'bi-info-circle-fill'};
  const t = document.createElement('div');
  t.className = `toast-item ${type === 'error' ? 'error' : type === 'success' ? 'success' : ''}`;
  t.innerHTML = `<i class="bi ${icons[type] || 'bi-info-circle-fill'} ti-icon ${type}"></i>
    <div><p class="ti-title">${esc(title)}</p><p class="ti-body">${esc(body)}</p></div>`;
  w.appendChild(t);
  setTimeout(() => { t.style.opacity = '0'; t.style.transition = 'opacity .3s'; setTimeout(() => t.remove(), 300); }, 3500);
}
window.toast = toast;

/* ════════════════════════════════════
   INIT
════════════════════════════════════ */
renderBoard();
</script>
@endpush