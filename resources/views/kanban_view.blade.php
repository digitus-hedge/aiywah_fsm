@extends('layouts.layout')

@section('title', 'Ticket Summary — Digit-Us Portal')
@section('page_title', 'Ticket Summary')
@section('page_icon', 'kanban')

@push('styles')
<style>
/* ═══════════════ THEME TOKENS ═══════════════ */
:root,[data-theme="light"]{
  --brand:#9a8053; --brand-dark:#7d6740; --brand-soft:rgba(154,128,83,.12);
  --app-bg:#f6f5f2; --surface:#fff; --surface-2:#f1efe9;
  --card-bg:#fff; --card-border:#eae6dd;
  --text-primary:#2b2820; --text-heading:#1a1810; --text-muted:#8a8372;
  --text-light:#c2bbab; --border-color:#e6e1d6;
  --card-shadow:0 2px 12px rgba(120,105,70,.09);
  --overlay-bg:rgba(30,26,16,.6); --modal-shadow:0 24px 64px rgba(0,0,0,.16);

  --lane-pending-bg:#fdf9f0;  --lane-pending-border:#d4b876;
  --lane-approved-bg:#f2f6ef; --lane-approved-border:#7ba85e;
  --lane-assigned-bg:#eef3f8; --lane-assigned-border:#5b8bbf;
  --lane-forward-bg:#f6f0f8;  --lane-forward-border:#9a6fb0;
  --lane-reject-bg:#faf0f0;   --lane-reject-border:#c25a5a;
}
[data-theme="dark"]{
  --brand:#b89968; --brand-dark:#9a8053; --brand-soft:rgba(184,153,104,.14);
  --app-bg:#14120c; --surface:#1c1a12; --surface-2:#221f16;
  --card-bg:#1c1a12; --card-border:#332e20;
  --text-primary:#ded8c8; --text-heading:#f0ead9; --text-muted:#8a8372;
  --text-light:#4a4535; --border-color:#332e20;
  --card-shadow:0 2px 16px rgba(0,0,0,.4);
  --overlay-bg:rgba(0,0,0,.75); --modal-shadow:0 24px 64px rgba(0,0,0,.55);

  --lane-pending-bg:rgba(212,184,118,.07);  --lane-pending-border:#8a7440;
  --lane-approved-bg:rgba(123,168,94,.07);  --lane-approved-border:#4d6b3a;
  --lane-assigned-bg:rgba(91,139,191,.07);  --lane-assigned-border:#3a5878;
  --lane-forward-bg:rgba(154,111,176,.07);  --lane-forward-border:#63456f;
  --lane-reject-bg:rgba(194,90,90,.07);     --lane-reject-border:#7a3838;
}

/* ═══════════════ PAGE HEADER ═══════════════ */
.pg-header{
  background:linear-gradient(135deg,#9a8053 0%,#7d6740 100%);
  border-radius:10px; padding:20px 24px; margin-bottom:20px;
  color:#fff; position:relative; overflow:hidden;
}
.pg-header::before{content:'';position:absolute;left:-40px;bottom:-40px;width:180px;height:180px;border-radius:50%;background:rgba(255,255,255,.05);}
.pg-header::after {content:'';position:absolute;right:-30px;top:-30px;width:160px;height:160px;border-radius:50%;background:rgba(255,255,255,.07);}
.pg-header h4{font-size:1rem;font-weight:600;margin:0 0 3px;position:relative;z-index:1;color:#fff;}
.pg-header p {font-size:.78rem;margin:0;opacity:.85;position:relative;z-index:1;color:#fff;}
@media(max-width:575.98px){.pg-header{padding:14px 16px;}.pg-header h4{font-size:.9rem;}}

/* ═══════════════ STATS STRIP ═══════════════ */
.stats-strip{display:grid;grid-template-columns:repeat(5,1fr);gap:10px;margin-bottom:18px;}
.stat-card{background:var(--card-bg);border:1px solid var(--card-border);border-radius:8px;padding:12px 14px;text-align:center;box-shadow:var(--card-shadow);transition:transform .2s;}
.stat-card:hover{transform:translateY(-2px);}
.stat-num{font-size:1.4rem;font-weight:700;line-height:1;margin-bottom:2px;}
.stat-lbl{font-size:.6875rem;color:var(--text-muted);font-weight:500;}
@media(max-width:991.98px){.stats-strip{grid-template-columns:repeat(3,1fr);}}
@media(max-width:575.98px){.stats-strip{grid-template-columns:repeat(2,1fr);}}

/* ═══════════════ FILTER BAR ═══════════════ */
.filter-bar{background:var(--card-bg);border:1px solid var(--card-border);border-radius:8px;padding:14px 16px;margin-bottom:18px;box-shadow:var(--card-shadow);display:flex;align-items:center;gap:10px;flex-wrap:wrap;}
.filter-bar .form-select,.filter-bar .form-control{font-size:.8rem;border:1px solid var(--border-color);border-radius:6px;padding:.38rem .75rem;color:var(--text-primary);background:var(--surface);height:36px;}
.filter-bar .form-select:focus,.filter-bar .form-control:focus{border-color:var(--brand);box-shadow:0 0 0 3px var(--brand-soft);outline:none;}
.filter-search{position:relative;flex:1;min-width:160px;}
.filter-search i{position:absolute;left:10px;top:50%;transform:translateY(-50%);color:var(--text-muted);font-size:.85rem;}
.filter-search input{padding-left:32px;width:100%;}
.filter-bar label{font-size:.72rem;color:var(--text-muted);font-weight:500;white-space:nowrap;}
[data-theme="dark"] .form-select option{background:#221f16;color:#ded8c8;}
.filter-count{font-size:.72rem;color:var(--text-muted);white-space:nowrap;margin-left:auto;}
@media(max-width:767.98px){.filter-search{min-width:100%;order:-1;}}

.scroll-hint{display:none;font-size:.72rem;color:var(--text-muted);text-align:center;padding:6px 0 10px;}
@media(max-width:767.98px){.scroll-hint{display:block;}}

/* ═══════════════ KANBAN BOARD ═══════════════ */
.kanban-wrapper{overflow-x:auto;padding-bottom:16px;-webkit-overflow-scrolling:touch;}
.kanban-wrapper::-webkit-scrollbar{height:6px;}
.kanban-wrapper::-webkit-scrollbar-thumb{background:var(--border-color);border-radius:3px;}
.kanban-board{display:flex;gap:14px;min-width:max-content;align-items:flex-start;padding:2px 2px 8px;}

.lane{width:290px;flex-shrink:0;display:flex;flex-direction:column;}
.lane-header{border-radius:8px 8px 0 0;padding:11px 14px;display:flex;align-items:center;justify-content:space-between;}
.lane-title{display:flex;align-items:center;gap:8px;font-size:.8125rem;font-weight:600;color:var(--text-heading);}
.lane-dot{width:9px;height:9px;border-radius:50%;flex-shrink:0;}
.lane-count{font-size:.6875rem;font-weight:700;padding:2px 8px;border-radius:10px;}
.lane-body{background:var(--surface-2);border:1px solid var(--card-border);border-top:none;border-radius:0 0 8px 8px;padding:10px 9px;min-height:200px;display:flex;flex-direction:column;gap:10px;}

.lane-Pending   .lane-header{background:var(--lane-pending-bg); border:1px solid var(--lane-pending-border); border-bottom:none;}
.lane-Pending   .lane-dot{background:#d4b876;} .lane-Pending  .lane-count{background:rgba(212,184,118,.2);color:#8a6f2a;}
.lane-Approved  .lane-header{background:var(--lane-approved-bg);border:1px solid var(--lane-approved-border);border-bottom:none;}
.lane-Approved  .lane-dot{background:#7ba85e;} .lane-Approved .lane-count{background:rgba(123,168,94,.2);color:#4d6b3a;}
.lane-Assigned  .lane-header{background:var(--lane-assigned-bg);border:1px solid var(--lane-assigned-border);border-bottom:none;}
.lane-Assigned  .lane-dot{background:#5b8bbf;} .lane-Assigned .lane-count{background:rgba(91,139,191,.2);color:#3a5878;}
.lane-Forwarded .lane-header{background:var(--lane-forward-bg); border:1px solid var(--lane-forward-border); border-bottom:none;}
.lane-Forwarded .lane-dot{background:#9a6fb0;} .lane-Forwarded.lane-count{background:rgba(154,111,176,.2);color:#63456f;}
.lane-Rejected  .lane-header{background:var(--lane-reject-bg);  border:1px solid var(--lane-reject-border);  border-bottom:none;}
.lane-Rejected  .lane-dot{background:#c25a5a;} .lane-Rejected .lane-count{background:rgba(194,90,90,.2);color:#7a3838;}

/* ═══════════════ KANBAN CARD ═══════════════ */
.kcard{background:var(--card-bg);border:1px solid var(--card-border);border-radius:8px;padding:12px 13px;box-shadow:var(--card-shadow);transition:box-shadow .2s,transform .2s;}
.kcard:hover{box-shadow:0 6px 24px rgba(120,105,70,.18);transform:translateY(-2px);}
[data-theme="dark"] .kcard:hover{box-shadow:0 6px 24px rgba(0,0,0,.5);}
.kc-top{display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:9px;gap:6px;}
.kc-sr{font-size:.8rem;font-weight:700;color:var(--brand);letter-spacing:.02em;}
.kc-priority{font-size:.6rem;font-weight:700;padding:2px 7px;border-radius:10px;white-space:nowrap;}
.prio-Critical{background:rgba(194,90,90,.15);color:#c25a5a;}
.prio-High{background:rgba(212,150,76,.15);color:#b57828;}
.prio-Medium{background:rgba(91,139,191,.15);color:#3a5878;}
.prio-Low{background:rgba(123,168,94,.15);color:#4d6b3a;}
.kc-client{font-size:.78rem;font-weight:500;color:var(--text-heading);margin-bottom:2px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;}
.kc-contract{font-size:.7rem;color:var(--text-muted);margin-bottom:9px;}
.kc-divider{border-color:var(--card-border);margin:8px 0;}
.kc-meta-row{display:flex;align-items:center;gap:6px;margin-bottom:8px;font-size:.7rem;color:var(--text-muted);}
.kc-site{margin-left:auto;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:120px;text-align:right;}
.kc-actions{display:flex;align-items:center;gap:6px;margin-top:8px;}
.kc-cat{font-size:.62rem;color:var(--text-muted);margin-left:auto;}
.kc-tech{display:flex;align-items:center;gap:6px;margin-top:7px;}
.tech-av{width:22px;height:22px;border-radius:50%;background:linear-gradient(135deg,var(--brand),var(--brand-dark));display:flex;align-items:center;justify-content:center;color:#fff;font-size:.6rem;font-weight:700;flex-shrink:0;}
.tech-name{font-size:.7rem;color:var(--text-muted);}
.lane-empty{text-align:center;padding:28px 16px;color:var(--text-light);font-size:.78rem;}
.lane-empty i{font-size:1.6rem;display:block;margin-bottom:6px;opacity:.4;}
</style>
@endpush

@section('content')

<div class="pg-header">
  <h4><i class="bi bi-kanban me-2"></i>Ticket Summary — Kanban View</h4>
  <p>Live pipeline view of all service requests across lifecycle stages.</p>
</div>

{{-- STATS STRIP --}}
<div class="stats-strip">
  @foreach($statuses as $s)
    <div class="stat-card">
      <div class="stat-num" id="cnt-{{ $s }}">{{ $tickets->where('status', $s)->count() }}</div>
      <div class="stat-lbl">{{ $s }}</div>
    </div>
  @endforeach
</div>

{{-- FILTER BAR --}}
<div class="filter-bar">
  <div class="filter-search">
    <input type="text" class="form-control" id="searchInput"
           placeholder="Search SR ID, client, site…" oninput="applyFilter()"/>
  </div>
  <div style="display:flex;align-items:center;gap:6px;">
    <label>Status</label>
    <select class="form-select" style="width:160px;" id="statusFilter" onchange="applyFilter()">
      <option value="">All Statuses</option>
      @foreach($statuses as $s)
        <option value="{{ $s }}">{{ $s }}</option>
      @endforeach
    </select>
  </div>
  <span class="filter-count" id="filterCount"></span>
</div>

<p class="scroll-hint"><i class="bi bi-arrow-left-right me-1"></i>Swipe left / right to see all columns</p>

{{-- KANBAN BOARD --}}
<div class="kanban-wrapper">
  <div class="kanban-board" id="kanbanBoard"></div>
</div>

@endsection

@push('scripts')
<script>
/* Data injected from the controller */
const TICKETS  = @json($tickets);
const STATUSES = @json($statuses);

let filtered = [...TICKETS];

function priorityClass(p){ return 'prio-' + (p || 'Medium'); }

function elapsed(iso){
  if(!iso) return '';
  const diff = Date.now() - new Date(iso).getTime();
  const h = Math.floor(diff / 3600000);
  const m = Math.floor((diff % 3600000) / 60000);
  if(h > 48) return `${Math.floor(h/24)}d ${h%24}h`;
  return `${h}h ${m}m`;
}

function buildCard(t){
  return `
    <div class="kcard">
      <div class="kc-top">
        <span class="kc-sr">${t.id}</span>
        <span class="kc-priority ${priorityClass(t.priority)}">${t.priority || '—'}</span>
      </div>
      <div class="kc-client">${t.client}</div>
      <div class="kc-contract"><i class="bi bi-file-earmark-text me-1"></i>${t.contract}</div>
      <hr class="kc-divider"/>
      <div class="kc-meta-row">
        <span><i class="bi bi-clock me-1"></i>${elapsed(t.createdRaw)}</span>
        <span class="kc-site"><i class="bi bi-geo me-1"></i>${t.site}</span>
      </div>
      <div class="kc-actions">
        <span class="kc-cat">${t.category}</span>
      </div>
      <div class="kc-tech">
        <div class="tech-av">${t.techInitials}</div>
        <span class="tech-name">${t.tech}</span>
      </div>
    </div>`;
}

function renderBoard(){
  const board = document.getElementById('kanbanBoard');
  board.innerHTML = '';

  // Update stat counts
  STATUSES.forEach(s => {
    const el = document.getElementById('cnt-' + s);
    if(el) el.textContent = filtered.filter(t => t.status === s).length;
  });

  document.getElementById('filterCount').textContent =
    `${filtered.length} of ${TICKETS.length} tickets`;

  // Build lanes
  STATUSES.forEach(s => {
    const cards = filtered.filter(t => t.status === s);
    const lane  = document.createElement('div');
    lane.className = `lane lane-${s}`;
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
}

function applyFilter(){
  const q  = document.getElementById('searchInput').value.toLowerCase().trim();
  const st = document.getElementById('statusFilter').value;
  filtered = TICKETS.filter(t => {
    const mq = !q  || t.id.toLowerCase().includes(q)
                   || (t.client || '').toLowerCase().includes(q)
                   || (t.site   || '').toLowerCase().includes(q);
    const ms = !st || t.status === st;
    return mq && ms;
  });
  renderBoard();
}

renderBoard();
</script>
@endpush