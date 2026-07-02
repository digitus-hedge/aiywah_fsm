@extends('layouts.layout')
@section('title', 'Client Directory— Digit-Us Portal')
@section('page_title', 'Client Directory')
@section('page_icon', 'bi bi-buildings')

{{-- ─────────────────────────────────────────
     Per-page styles (scoped to this page only)
     The shared theme.css already provides the design tokens
     (surfaces, borders, text colours, shadows, dark theme…),
     so this page only adds the directory-specific pieces.
───────────────────────────────────────── --}}
@push('styles')
<style>
/* PAGE HEADER GRADIENT */
.cd-pg-header{background:linear-gradient(135deg,#9A7B4F 0%,#7A6140 100%);border-radius:10px;padding:20px 24px;margin-bottom:20px;color:#fff;position:relative;overflow:hidden;}
.cd-pg-header::before{content:'';position:absolute;left:-40px;bottom:-40px;width:180px;height:180px;border-radius:50%;background:rgba(255,255,255,.05);}
.cd-pg-header::after{content:'';position:absolute;right:-30px;top:-30px;width:160px;height:160px;border-radius:50%;background:rgba(255,255,255,.07);}
.cd-pg-header h4{font-size:1rem;font-weight:600;margin:0 0 3px;position:relative;z-index:1;}
.cd-pg-header p{font-size:.78rem;margin:0;opacity:.9;position:relative;z-index:1;}
.cd-pg-header .meta-row{display:flex;align-items:center;gap:8px;margin-top:9px;position:relative;z-index:1;flex-wrap:wrap;}
.cd-meta-badge{background:rgba(255,255,255,.18);border:1px solid rgba(255,255,255,.3);border-radius:20px;font-size:.6875rem;padding:2px 10px;font-weight:500;}

/* STATS STRIP */
.cd-stats-strip{display:grid;grid-template-columns:repeat(4,1fr);gap:10px;margin-bottom:18px;}
.cd-stat-card{background:var(--card-bg);border:1px solid var(--card-border);border-radius:9px;padding:13px 15px;display:flex;align-items:center;gap:12px;box-shadow:var(--card-shadow);}
.cd-stat-icon{width:38px;height:38px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:1rem;flex-shrink:0;}
.cd-stat-num{font-size:1.5rem;font-weight:700;line-height:1;color:var(--text-heading);}
.cd-stat-lbl{font-size:.7rem;color:var(--text-muted);}
@media(max-width:767px){.cd-stats-strip{grid-template-columns:repeat(2,1fr);}}
@media(max-width:400px){.cd-stats-strip{grid-template-columns:1fr;}}

/* FILTER BAR */
.cd-filter-bar{background:var(--card-bg);border:1px solid var(--card-border);border-radius:10px;padding:14px 16px;margin-bottom:16px;box-shadow:var(--card-shadow);display:flex;flex-wrap:wrap;gap:10px;align-items:flex-end;}
.cd-filter-group{display:flex;flex-direction:column;gap:4px;min-width:0;flex:1 1 auto;}
.cd-filter-label{font-size:.7rem;font-weight:600;color:var(--text-muted);text-transform:uppercase;letter-spacing:.05em;}
.cd-filter-control{height:34px;padding:0 10px;border:1px solid var(--input-border,var(--border-color));border-radius:7px;background:var(--input-bg);color:var(--text-primary);font-size:.8rem;font-family:var(--font-body);width:100%;transition:border-color .15s,box-shadow .15s;}
.cd-filter-control:focus{outline:none;border-color:var(--color-primary);box-shadow:0 0 0 3px rgba(var(--color-primary-rgb),.14);}
.cd-filter-search{min-width:200px;}
.cd-filter-actions{margin-left:auto;display:flex;gap:8px;align-items:flex-end;flex-wrap:wrap;}
@media(max-width:575.98px){.cd-filter-actions{margin-left:0;width:100%;}.cd-filter-actions .cd-btn{flex:1;justify-content:center;}}

/* BUTTONS */
.cd-btn-gold{display:inline-flex;align-items:center;gap:6px;padding:7px 14px;background:linear-gradient(135deg,#9A7B4F,#C4A882);color:#fff;border:none;border-radius:7px;font-size:.8rem;font-weight:500;cursor:pointer;white-space:nowrap;text-decoration:none;}
.cd-btn-gold:hover{opacity:.87;color:#fff;}
.cd-btn-ghost{display:inline-flex;align-items:center;gap:6px;padding:7px 12px;background:var(--surface-2);color:var(--text-muted);border:1px solid var(--border-color);border-radius:7px;font-size:.8rem;cursor:pointer;white-space:nowrap;text-decoration:none;}
.cd-btn-ghost:hover{background:var(--surface-3);color:var(--text-muted);}
.cd-btn-xs{padding:4px 9px;border-radius:5px;font-size:.75rem;border:none;cursor:pointer;display:inline-flex;align-items:center;gap:4px;font-family:var(--font-body);text-decoration:none;transition:background .12s;}
.cd-btn-xs-edit{background:rgba(154,123,79,.1);color:#9A7B4F;}
.cd-btn-xs-edit:hover{background:rgba(154,123,79,.2);color:#9A7B4F;}
.cd-btn-xs-view{background:rgba(37,99,235,.1);color:#3b82f6;}
.cd-btn-xs-view:hover{background:rgba(37,99,235,.2);color:#3b82f6;}
.cd-btn-xs-off{background:rgba(156,163,175,.1);color:#9ca3af;}
.cd-btn-xs-off:hover{background:rgba(156,163,175,.2);color:#9ca3af;}

/* TABLE CARD */
.cd-tbl-card{background:var(--card-bg);border:1px solid var(--card-border);border-radius:10px;box-shadow:var(--card-shadow);overflow:hidden;}
.cd-tbl-card-hdr{display:flex;align-items:center;justify-content:space-between;padding:13px 18px;border-bottom:1px solid var(--border-color);flex-wrap:wrap;gap:10px;}
.cd-tbl-card-hdr-left{display:flex;align-items:center;gap:10px;}
.cd-tbl-card-title{font-size:.875rem;font-weight:600;color:var(--text-heading);}
.cd-result-count{font-size:.75rem;color:var(--text-muted);background:var(--surface-2);padding:2px 9px;border-radius:9px;}
.cd-tbl-wrap{overflow-x:auto;}
table.cd-listing{width:100%;border-collapse:collapse;}
table.cd-listing thead th{padding:10px 16px;font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--text-muted);background:var(--surface-2);border-bottom:1px solid var(--border-color);white-space:nowrap;}
table.cd-listing tbody tr{border-bottom:1px solid var(--border-color);transition:background .1s;cursor:pointer;}
table.cd-listing tbody tr:last-child{border-bottom:none;}
table.cd-listing tbody tr:hover{background:rgba(var(--color-primary-rgb),.04);}
table.cd-listing td{padding:11px 16px;font-size:.8125rem;color:var(--text-primary);vertical-align:middle;}
table.cd-listing td.muted{color:var(--text-muted);font-size:.78rem;}
.cd-client-name{font-weight:600;font-size:.8125rem;color:var(--text-heading);}
.cd-client-token{font-size:.7rem;color:var(--text-muted);}

/* BADGES */
.cd-sbadge{display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:20px;font-size:.7rem;font-weight:600;white-space:nowrap;}
.cd-sb-active{background:rgba(16,185,129,.12);color:#059669;}
.cd-sb-inactive{background:rgba(156,163,175,.1);color:#9ca3af;}

/* PAGINATION */
.cd-pagination-bar{display:flex;align-items:center;justify-content:space-between;padding:12px 18px;border-top:1px solid var(--border-color);flex-wrap:wrap;gap:8px;}
.cd-page-info{font-size:.78rem;color:var(--text-muted);}

/* Laravel pagination links restyle */
.cd-pagination-bar .pagination{margin:0;gap:4px;}
.cd-pagination-bar .page-link{border-radius:6px;border:1px solid var(--border-color);background:var(--card-bg);color:var(--text-muted);font-size:.78rem;padding:4px 10px;}
.cd-pagination-bar .page-link:hover{border-color:#9A7B4F;color:#9A7B4F;background:var(--card-bg);}
.cd-pagination-bar .page-item.active .page-link{background:#9A7B4F;color:#fff;border-color:#9A7B4F;}
.cd-pagination-bar .page-item.disabled .page-link{color:var(--text-light);background:var(--card-bg);}

/* EMPTY STATE */
.cd-empty-row td{text-align:center;padding:40px 20px;color:var(--text-muted);font-size:.82rem;}
.cd-empty-row td i{display:block;font-size:2rem;color:var(--text-light);margin-bottom:8px;}

/* TOAST */
#cdToastWrap{position:fixed;bottom:22px;right:22px;z-index:9999;display:flex;flex-direction:column;gap:8px;pointer-events:none;}
.cd-toast-item{display:flex;align-items:flex-start;gap:10px;padding:12px 15px;border-radius:9px;background:var(--card-bg);border:1px solid var(--card-border);box-shadow:0 6px 24px rgba(0,0,0,.14);min-width:240px;max-width:320px;animation:cdToastIn .2s ease;pointer-events:auto;}
@keyframes cdToastIn{from{opacity:0;transform:translateY(10px);}to{opacity:1;transform:translateY(0);}}
.cd-t-ico{font-size:1rem;flex-shrink:0;margin-top:1px;}
.cd-t-ico.ok{color:#10b981;}.cd-t-ico.err{color:#ef4444;}.cd-t-ico.info{color:#9A7B4F;}
.cd-t-title{font-size:.8rem;font-weight:600;color:var(--text-heading);margin:0 0 2px;}
.cd-t-body{font-size:.75rem;color:var(--text-muted);margin:0;}
</style>
@endpush

@section('content')

{{-- PAGE HEADER --}}
<div class="cd-pg-header">
  <h4><i class="bi bi-buildings me-2"></i>Client Directory</h4>
  <p>Browse, search and manage all registered enterprise client accounts, their contact details, project counts and access status.</p>
  <div class="meta-row">
    <span class="cd-meta-badge"><i class="bi bi-shield-fill-check me-1"></i>Super Admin</span>
    <span class="cd-meta-badge"><i class="bi bi-person-gear me-1"></i>Admin</span>
    <span class="cd-meta-badge"><i class="bi bi-person-badge me-1"></i>Front Desk</span>
  </div>
</div>

{{-- STATS STRIP --}}
<div class="cd-stats-strip">
  <div class="cd-stat-card">
    <div class="cd-stat-icon" style="background:rgba(5,150,105,.1);"><i class="bi bi-buildings" style="color:#059669;"></i></div>
    <div><div class="cd-stat-num">{{ $totalClients }}</div><div class="cd-stat-lbl">Total Clients</div></div>
  </div>
  <div class="cd-stat-card">
    <div class="cd-stat-icon" style="background:rgba(37,99,235,.1);"><i class="bi bi-diagram-3" style="color:#2563eb;"></i></div>
    <div><div class="cd-stat-num">{{ $totalProjects }}</div><div class="cd-stat-lbl">Total Projects</div></div>
  </div>
  <div class="cd-stat-card">
    <div class="cd-stat-icon" style="background:rgba(124,58,237,.1);"><i class="bi bi-people" style="color:#7c3aed;"></i></div>
    <div><div class="cd-stat-num">{{ $totalContacts }}</div><div class="cd-stat-lbl">Total Contacts</div></div>
  </div>
  <div class="cd-stat-card">
    <div class="cd-stat-icon" style="background:rgba(156,163,175,.1);"><i class="bi bi-clock-history" style="color:#9ca3af;"></i></div>
    <div><div class="cd-stat-num">{{ $recentCount }}</div><div class="cd-stat-lbl">Added This Month</div></div>
  </div>
</div>

{{-- FILTER BAR --}}
<form method="GET" action="{{ route('clients.directory') }}" class="cd-filter-bar">
  <div class="cd-filter-group">
    <div class="cd-filter-label">Search</div>
    <input class="cd-filter-control cd-filter-search" type="text" name="q"
           value="{{ request('q') }}" placeholder="Client name, token, contact…"/>
  </div>
  <div class="cd-filter-group">
    <div class="cd-filter-label">Country</div>
    <select class="cd-filter-control" name="country">
      <option value="">All Countries</option>
      @foreach($countries as $c)
        <option value="{{ $c }}" @selected(request('country') === $c)>{{ $c }}</option>
      @endforeach
    </select>
  </div>
  <div class="cd-filter-actions">
    <a href="{{ route('clients.directory') }}" class="cd-btn cd-btn-ghost"><i class="bi bi-x-circle"></i>Reset</a>
    <button type="submit" class="cd-btn cd-btn-gold"><i class="bi bi-search"></i>Apply</button>
    <a href="{{ route('clients.create') }}" class="cd-btn cd-btn-gold"><i class="bi bi-plus-lg"></i>New Client</a>
  </div>
</form>

{{-- TABLE CARD --}}
<div class="cd-tbl-card">
  <div class="cd-tbl-card-hdr">
    <div class="cd-tbl-card-hdr-left">
      <span class="cd-tbl-card-title">Registered Clients</span>
      <span class="cd-result-count">{{ $clients->total() }} records</span>
    </div>
  </div>

  <div class="cd-tbl-wrap">
    <table class="cd-listing">
      <thead>
        <tr>
          <th style="width:36px;">#</th>
          <th>Client Name / Token</th>
          <th>Primary Contact</th>
          <th>Phone (WhatsApp)</th>
          <th style="text-align:center;">Projects</th>
          <th style="text-align:center;">Contacts</th>
          <th>Status</th>
          <th style="width:160px;">Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($clients as $index => $client)
        <tr onclick="window.location.href='{{ route('clients.edit', $client) }}'">
          <td class="muted">{{ $clients->firstItem() + $index }}</td>
          <td>
            <div class="cd-client-name">{{ $client->company_name }}</div>
            <div class="cd-client-token">{{ $client->unique_code }}</div>
          </td>
          <td class="muted">{{ $client->contact_name ?: '—' }}</td>
          <td class="muted">
            @if($client->primary_country || $client->primary_mobile)
              {{ trim(($client->primary_country ? '+'.ltrim($client->primary_country,'+').' ' : '').$client->primary_mobile) }}
            @else
              —
            @endif
          </td>
          <td style="text-align:center;"><strong>{{ $client->projects_count }}</strong></td>
          <td style="text-align:center;"><strong>{{ $client->mobiles_count + 1 }}</strong></td>
          <td>
            <span class="cd-sbadge cd-sb-active">
              <i class="bi bi-circle-fill" style="font-size:.4rem;"></i>Active
            </span>
          </td>
          <td onclick="event.stopPropagation()">
            <div style="display:flex;gap:5px;">
              <a href="{{ route('clients.edit', $client) }}" class="cd-btn-xs cd-btn-xs-view"><i class="bi bi-eye"></i>View</a>
              <a href="{{ route('clients.edit', $client) }}" class="cd-btn-xs cd-btn-xs-edit"><i class="bi bi-pencil"></i>Edit</a>
            </div>
          </td>
        </tr>
        @empty
        <tr class="cd-empty-row">
          <td colspan="8">
            <i class="bi bi-inboxes"></i>
            No clients found. Try adjusting your search or
            <a href="{{ route('clients.create') }}" style="color:#9A7B4F;">create a new client</a>.
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="cd-pagination-bar">
    <div class="cd-page-info">
      @if($clients->total() > 0)
        Showing {{ $clients->firstItem() }}–{{ $clients->lastItem() }} of {{ $clients->total() }} records
      @else
        0 records
      @endif
    </div>
    <div>
      {{ $clients->onEachSide(1)->links('pagination::bootstrap-5') }}
    </div>
  </div>
</div>

<div id="cdToastWrap"></div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  @if(session('success'))
    cdShowToast('ok', 'Success', @json(session('flash_message', 'Action completed successfully.')));
  @endif
});

function cdShowToast(type, title, body){
  const w = document.getElementById('cdToastWrap');
  if(!w) return;
  const icons = { ok:'bi-check-circle-fill', err:'bi-x-circle-fill', info:'bi-info-circle-fill' };
  const t = document.createElement('div');
  t.className = 'cd-toast-item';
  t.innerHTML = `<i class="bi ${icons[type]||icons.info} cd-t-ico ${type}"></i>
                 <div><p class="cd-t-title">${title}</p><p class="cd-t-body">${body}</p></div>`;
  w.appendChild(t);
  setTimeout(() => {
    t.style.transition = 'opacity .3s';
    t.style.opacity = '0';
    setTimeout(() => t.remove(), 300);
  }, 3500);
}
</script>
@endpush