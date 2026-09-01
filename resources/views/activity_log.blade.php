@extends('layouts.layout')

@section('title', 'Activity Log | Matter Mind')
@section('page_title', 'Activity Log')
@section('page_icon', 'activity')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet"/>
<style>
/* ===== Activity Log — scoped page styles ===== */
.al-wrap{--gold:#9a8053;--gold-2:#b8975e;--table-header:#f7f9fd;}
[data-bs-theme="dark"] .al-wrap{--table-header:#2a2928;}
.al-wrap h4,.al-wrap h5,.al-wrap h6,.al-wrap .tbl-card-title,.al-wrap .stat-num{letter-spacing:-.01em;}

/* PAGE HEADER */
.al-wrap .pg-header{background:linear-gradient(135deg,#9A7B4F 0%,#7A6140 100%);border-radius:10px;padding:20px 24px;margin-bottom:20px;color:#fff;position:relative;overflow:hidden;}
.al-wrap .pg-header::before{content:'';position:absolute;left:-40px;bottom:-40px;width:180px;height:180px;border-radius:50%;background:rgba(255,255,255,.05);}
.al-wrap .pg-header::after{content:'';position:absolute;right:-30px;top:-30px;width:160px;height:160px;border-radius:50%;background:rgba(255,255,255,.07);}
.al-wrap .pg-header h4{font-size:1rem;font-weight:600;margin:0 0 3px;position:relative;z-index:1;}
.al-wrap .pg-header p{font-size:.78rem;margin:0;opacity:.85;position:relative;z-index:1;}
.al-wrap .pg-header .meta-row{display:flex;align-items:center;gap:8px;margin-top:9px;position:relative;z-index:1;flex-wrap:wrap;}
.al-wrap .meta-badge{background:rgba(255,255,255,.18);border:1px solid rgba(255,255,255,.3);border-radius:20px;font-size:.6875rem;padding:2px 10px;font-weight:500;}
.al-wrap td.mono .sr-ref-trigger,
.al-meta-val .sr-ref-trigger{ cursor:pointer; }
/* STATS STRIP */
.al-wrap .stats-strip{display:grid;grid-template-columns:repeat(4,1fr);gap:10px;margin-bottom:18px;}
.al-wrap .stat-card{background:var(--card-bg);border:1px solid var(--card-border);border-radius:9px;padding:13px 15px;display:flex;align-items:center;gap:12px;box-shadow:var(--card-shadow);}
.al-wrap .stat-icon{width:38px;height:38px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:1rem;flex-shrink:0;}
.al-wrap .stat-num{font-size:1.5rem;font-weight:700;line-height:1;color:var(--text-heading);}
.al-wrap .stat-lbl{font-size:.7rem;color:var(--text-muted);}
@media(max-width:767px){.al-wrap .stats-strip{grid-template-columns:repeat(2,1fr);}}
@media(max-width:420px){.al-wrap .stats-strip{grid-template-columns:1fr;}}

/* FILTER BAR */
.al-wrap .filter-bar{background:var(--card-bg);border:1px solid var(--card-border);border-radius:10px;padding:14px 16px;margin-bottom:16px;box-shadow:var(--card-shadow);display:flex;flex-wrap:wrap;gap:10px;align-items:flex-end;}
.al-wrap .filter-group{display:flex;flex-direction:column;gap:4px;min-width:0;flex:1 1 auto;}
.al-wrap .filter-label{font-size:.7rem;font-weight:600;color:var(--text-muted);text-transform:uppercase;letter-spacing:.05em;}
.al-wrap .filter-control{height:34px;padding:0 10px;border:1px solid var(--border-color);border-radius:7px;background:var(--input-bg);color:var(--text-primary);font-size:.8rem;min-width:130px;transition:border-color .15s,box-shadow .15s;}
.al-wrap .filter-control:focus{outline:none;border-color:var(--gold);box-shadow:0 0 0 3px rgba(154,128,83,.12);}
.al-wrap .filter-actions{margin-left:auto;display:flex;gap:8px;align-items:flex-end;flex-wrap:wrap;}
@media(max-width:575.98px){
  .al-wrap .filter-group{flex:1 1 100%;}
  .al-wrap .filter-control{width:100%!important;min-width:0;}
  .al-wrap .filter-actions{margin-left:0;width:100%;}
  .al-wrap .filter-actions>*{flex:1;justify-content:center;}
}

/* TABLE CARD */
.al-wrap .tbl-card{background:var(--card-bg);border:1px solid var(--card-border);border-radius:10px;box-shadow:var(--card-shadow);overflow:hidden;}
.al-wrap .tbl-card-hdr{display:flex;align-items:center;justify-content:space-between;padding:13px 18px;border-bottom:1px solid var(--border-color);flex-wrap:wrap;gap:10px;}
.al-wrap .tbl-card-title{font-size:.875rem;font-weight:600;color:var(--text-heading);}
.al-wrap .result-count{font-size:.75rem;color:var(--text-muted);background:var(--surface-2);padding:2px 9px;border-radius:9px;margin-left:10px;}
.al-wrap .tbl-wrap{overflow-x:auto;-webkit-overflow-scrolling:touch;}
.al-wrap table.listing{width:100%;border-collapse:collapse;min-width:960px;}
.al-wrap table.listing thead th{padding:10px 16px;font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--text-muted);background:var(--table-header);border-bottom:1px solid var(--border-color);white-space:nowrap;text-align:left;}
.al-wrap table.listing tbody tr{border-bottom:1px solid var(--border-color);transition:background .1s;cursor:pointer;}
.al-wrap table.listing tbody tr:last-child{border-bottom:none;}
.al-wrap table.listing tbody tr:hover{background:var(--table-hover);}
.al-wrap table.listing td{padding:11px 16px;font-size:.8125rem;color:var(--text-primary);vertical-align:middle;}
.al-wrap table.listing td.muted{color:var(--text-muted);font-size:.78rem;}
.al-wrap table.listing td.mono{font-size:.78rem;font-weight:600;color:var(--gold);white-space:nowrap;}

/* USER CHIP */
.al-wrap .u-chip{display:inline-flex;align-items:center;gap:7px;}
.al-wrap .u-av{width:26px;height:26px;border-radius:50%;background:linear-gradient(135deg,#9A7B4F,#C4A882);color:#fff;font-size:.65rem;font-weight:700;display:flex;align-items:center;justify-content:center;flex-shrink:0;}
.al-wrap .u-name{font-size:.8rem;color:var(--text-heading);white-space:nowrap;}
.al-wrap .u-sys{background:var(--surface-3);color:var(--text-muted);}

/* BADGES */
.al-wrap .sbadge{display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:20px;font-size:.7rem;font-weight:600;white-space:nowrap;text-transform:capitalize;}
.al-wrap .act-created{background:rgba(16,185,129,.1);color:#059669;}
.al-wrap .act-updated{background:rgba(245,158,11,.1);color:#d97706;}
.al-wrap .act-deleted{background:rgba(239,68,68,.1);color:#ef4444;}
.al-wrap .act-other{background:rgba(37,99,235,.1);color:#2563eb;}
.al-wrap .mod-pill{display:inline-block;padding:2px 9px;border-radius:6px;background:var(--surface-2);border:1px solid var(--border-color);font-size:.72rem;font-weight:600;color:var(--text-heading);white-space:nowrap;}
.al-wrap .desc-txt{font-size:.78rem;color:var(--text-muted);max-width:300px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
.al-wrap .chg-count{font-size:.7rem;color:var(--text-muted);background:var(--surface-2);padding:1px 8px;border-radius:9px;white-space:nowrap;}

/* BUTTONS */
.al-wrap .btn-gold{display:inline-flex;align-items:center;gap:6px;padding:7px 14px;background:linear-gradient(135deg,var(--gold),var(--gold-2));color:#fff;border:none;border-radius:7px;font-size:.8rem;font-weight:500;cursor:pointer;white-space:nowrap;text-decoration:none;}
.al-wrap .btn-gold:hover{opacity:.87;color:#fff;}
.al-wrap .btn-ghost{display:inline-flex;align-items:center;gap:6px;padding:7px 12px;background:var(--surface-2);color:var(--text-muted);border:1px solid var(--border-color);border-radius:7px;font-size:.8rem;cursor:pointer;white-space:nowrap;text-decoration:none;}
.al-wrap .btn-ghost:hover{background:var(--surface-3);}
.al-wrap .btn-xs-view{background:rgba(37,99,235,.1);color:#3b82f6;padding:4px 9px;border-radius:5px;font-size:.75rem;border:none;cursor:pointer;display:inline-flex;align-items:center;gap:4px;}
.al-wrap .btn-xs-view:hover{background:rgba(37,99,235,.2);}

/* PAGINATION */
.al-wrap .pagination-bar{display:flex;align-items:center;justify-content:space-between;padding:12px 18px;border-top:1px solid var(--border-color);flex-wrap:wrap;gap:8px;}
.al-wrap .page-info{font-size:.78rem;color:var(--text-muted);}
.al-wrap .page-btns{display:flex;gap:4px;flex-wrap:wrap;}
.al-wrap .page-btn{min-width:30px;height:30px;padding:0 8px;border-radius:6px;border:1px solid var(--border-color);background:var(--card-bg);color:var(--text-muted);cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:.78rem;transition:all .12s;text-decoration:none;}
.al-wrap .page-btn:hover{border-color:var(--gold);color:var(--gold);}
.al-wrap .page-btn.active{background:var(--gold);color:#fff;border-color:var(--gold);}

/* EMPTY */
.al-wrap .empty-row td{text-align:center;padding:40px 20px;color:var(--text-muted);font-size:.82rem;}
.al-wrap .empty-row td i{display:block;font-size:2rem;color:var(--text-light);margin-bottom:8px;}

/* DETAIL MODAL */
.al-modal{display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:1200;align-items:center;justify-content:center;backdrop-filter:blur(3px);padding:20px;}
.al-modal.show{display:flex;}
.al-modal-box{background:var(--card-bg);border:1px solid var(--card-border);border-radius:12px;width:100%;max-width:640px;max-height:88vh;display:flex;flex-direction:column;box-shadow:0 12px 48px rgba(0,0,0,.28);overflow:hidden;}
.al-modal-hdr{display:flex;align-items:center;justify-content:space-between;padding:15px 20px;border-bottom:1px solid var(--border-color);}
.al-modal-hdr h6{font-size:.9rem;font-weight:600;color:var(--text-heading);margin:0;display:flex;align-items:center;gap:9px;}
.al-modal-close{background:none;border:none;color:var(--text-muted);cursor:pointer;font-size:1rem;padding:4px 6px;border-radius:5px;line-height:1;}
.al-modal-close:hover{background:var(--surface-2);color:var(--text-primary);}
.al-modal-body{padding:18px 20px;overflow-y:auto;}
.al-meta-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:12px;margin-bottom:18px;}
@media(max-width:520px){.al-meta-grid{grid-template-columns:1fr;}}
.al-meta-item{background:var(--surface-2);border:1px solid var(--border-color);border-radius:8px;padding:10px 12px;}
.al-meta-lbl{font-size:.65rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--text-muted);margin-bottom:3px;}
.al-meta-val{font-size:.8rem;color:var(--text-heading);word-break:break-word;}
.al-sec-title{font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--text-muted);margin:0 0 8px;}
.al-diff{border:1px solid var(--border-color);border-radius:8px;overflow:hidden;}
.al-diff table{width:100%;border-collapse:collapse;}
.al-diff th{padding:8px 12px;font-size:.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:var(--text-muted);background:var(--table-header);border-bottom:1px solid var(--border-color);text-align:left;}
.al-diff td{padding:9px 12px;font-size:.78rem;border-bottom:1px solid var(--border-color);vertical-align:top;word-break:break-word;}
.al-diff tr:last-child td{border-bottom:none;}
.al-diff .f-name{font-weight:600;color:var(--text-heading);white-space:nowrap;}
.al-diff .v-old{color:#ef4444;text-decoration:line-through;opacity:.75;}
.al-diff .v-new{color:#059669;font-weight:500;}
.al-empty-sm{font-size:.78rem;color:var(--text-muted);padding:14px;text-align:center;background:var(--surface-2);border-radius:8px;}
.al-ua{font-size:.72rem;color:var(--text-muted);word-break:break-all;line-height:1.5;}
</style>
@endpush

@section('content')
<div class="al-wrap">

  {{-- PAGE HEADER --}}
  <div class="pg-header">
    <h4><i class="bi bi-clock-history me-2"></i>Activity Log</h4>
    <p>Complete audit trail of every record created, updated or deleted across the portal — who did it, when, and exactly what changed.</p>
    <div class="meta-row">
      <span class="meta-badge"><i class="bi bi-shield-fill-check me-1"></i>Super Admin</span>
      <span class="meta-badge"><i class="bi bi-person-gear me-1"></i>Admin</span>
    </div>
  </div>

  {{-- STATS --}}
  <div class="stats-strip">
    <div class="stat-card">
      <div class="stat-icon" style="background:rgba(37,99,235,.1);"><i class="bi bi-list-ul" style="color:#3b82f6;"></i></div>
      <div><div class="stat-num">{{ number_format($stats['total']) }}</div><div class="stat-lbl">Total Entries</div></div>
    </div>
    <div class="stat-card">
      <div class="stat-icon" style="background:rgba(16,185,129,.1);"><i class="bi bi-plus-circle" style="color:#10b981;"></i></div>
      <div><div class="stat-num">{{ number_format($stats['created']) }}</div><div class="stat-lbl">Created Today</div></div>
    </div>
    <div class="stat-card">
      <div class="stat-icon" style="background:rgba(245,158,11,.1);"><i class="bi bi-pencil-square" style="color:#f59e0b;"></i></div>
      <div><div class="stat-num">{{ number_format($stats['updated']) }}</div><div class="stat-lbl">Updated Today</div></div>
    </div>
    <div class="stat-card">
      <div class="stat-icon" style="background:rgba(239,68,68,.1);"><i class="bi bi-trash3" style="color:#ef4444;"></i></div>
      <div><div class="stat-num">{{ number_format($stats['deleted']) }}</div><div class="stat-lbl">Deleted Today</div></div>
    </div>
  </div>

  {{-- FILTER BAR --}}
  <form method="GET" class="filter-bar">
    <div class="filter-group">
      <div class="filter-label">Module</div>
      <select name="module" class="filter-control">
        <option value="">All Modules</option>
        @foreach($modules as $m)
          <option value="{{ $m }}" @selected(request('module') === $m)>{{ $m }}</option>
        @endforeach
      </select>
    </div>
    <div class="filter-group">
      <div class="filter-label">Action</div>
      <select name="type" class="filter-control">
        <option value="">All Actions</option>
        @foreach($types as $t)
          <option value="{{ $t }}" @selected(request('type') === $t)>{{ ucfirst($t) }}</option>
        @endforeach
      </select>
    </div>
    <div class="filter-group">
      <div class="filter-label">User</div>
      <select name="user_id" class="filter-control">
        <option value="">All Users</option>
        @foreach($users as $u)
          <option value="{{ $u->id }}" @selected(request('user_id') == $u->id)>{{ $u->name }}</option>
        @endforeach
      </select>
    </div>
    <div class="filter-group">
      <div class="filter-label">Date From</div>
      <input type="date" name="date_from" value="{{ request('date_from') }}" class="filter-control">
    </div>
    <div class="filter-group">
      <div class="filter-label">Date To</div>
      <input type="date" name="date_to" value="{{ request('date_to') }}" class="filter-control">
    </div>
    <div class="filter-group">
      <div class="filter-label">Search</div>
      <input type="text" name="search" value="{{ request('search') }}" class="filter-control" placeholder="Description or ID…">
    </div>
    <div class="filter-actions">
      <a class="btn-ghost" href="{{ route('activity-log') }}"><i class="bi bi-x-circle"></i>Reset</a>
      <button type="submit" class="btn-gold"><i class="bi bi-funnel"></i>Apply</button>
    </div>
  </form>

  {{-- TABLE --}}
  <div class="tbl-card">
    <div class="tbl-card-hdr">
      <div style="display:flex;align-items:center;">
        <span class="tbl-card-title">Audit Trail</span>
        <span class="result-count">{{ number_format($logs->total()) }} entries</span>
      </div>
      <div style="font-size:.75rem;color:var(--text-muted);">
        <i class="bi bi-info-circle me-1"></i>Click any row for full details
      </div>
    </div>
    <div class="tbl-wrap">
      <table class="listing">
        <thead>
          <tr>
            <th>When</th>
            <th>User</th>
            <th>Module</th>
            <th>Action</th>
            <th>Record</th>
            <th>Description</th>
            <th>Changes</th>
            <th style="width:60px;"></th>
          </tr>
        </thead>
        <tbody>
        @forelse($logs as $log)
          @php
            $act = strtolower($log->activity_type);
            $cls = ['created'=>'act-created','updated'=>'act-updated','deleted'=>'act-deleted'][$act] ?? 'act-other';
            $chg = is_array($log->new_values) ? count($log->new_values) : 0;
            $nm  = $log->user->name ?? null;
            $ini = $nm ? strtoupper(mb_substr($nm, 0, 1)) : '—';
          @endphp
          <tr onclick="alView({{ $log->id }})">
            <td class="mono">{{ $log->created_at?->format('d M Y') }}<br>
              <span style="font-weight:400;color:var(--text-muted);">{{ $log->created_at?->format('h:i A') }}</span>
            </td>
            <td>
              <span class="u-chip">
                <span class="u-av {{ $nm ? '' : 'u-sys' }}">{{ $ini }}</span>
                <span class="u-name">{{ $nm ?? 'System' }}</span>
              </span>
            </td>
            <td><span class="mod-pill">{{ $log->module ?? '—' }}</span></td>
            <td><span class="sbadge {{ $cls }}"><i class="bi bi-circle-fill" style="font-size:.4rem;"></i>{{ $act }}</span></td>
            <td class="mono">
              @if($log->module === 'ServiceRequest' && $log->subject_id)
                <span class="sr-ref-trigger" data-sr-id="{{ $log->subject_id }}" onclick="event.stopPropagation(); openSrTracking({{ $log->subject_id }});">
                  {{ $log->record_label ?? '—' }}
                </span>
              @else
                {{ $log->record_label ?? '—' }}
              @endif
            </td>
            <td><div class="desc-txt">{{ $log->description ?? '—' }}</div></td>
            <td>@if($chg)<span class="chg-count">{{ $chg }} field{{ $chg > 1 ? 's' : '' }}</span>@else<span class="muted">—</span>@endif</td>
            <td>
              <button class="btn-xs-view" onclick="event.stopPropagation();alView({{ $log->id }})"><i class="bi bi-eye"></i></button>
            </td>
          </tr>
        @empty
          <tr class="empty-row"><td colspan="8"><i class="bi bi-inbox"></i>No activity recorded yet.</td></tr>
        @endforelse
        </tbody>
      </table>
    </div>

    @if($logs->hasPages())
    <div class="pagination-bar">
      <div class="page-info">Page {{ $logs->currentPage() }} of {{ $logs->lastPage() }} · {{ number_format($logs->total()) }} total</div>
      <div class="page-btns">
        @foreach($logs->linkCollection() as $link)
          <a class="page-btn {{ $link['active'] ? 'active' : '' }}"
             href="{{ $link['url'] ?? '#' }}"
             style="{{ $link['url'] ? '' : 'opacity:.4;pointer-events:none;' }}">{!! $link['label'] !!}</a>
        @endforeach
      </div>
    </div>
    @endif
  </div>
</div>

{{-- DETAIL MODAL --}}
<div class="al-modal" id="alModal" onclick="if(event.target===this)alClose()">
  <div class="al-modal-box">
    <div class="al-modal-hdr">
      <h6><i class="bi bi-file-earmark-text"></i>Activity Detail</h6>
      <button class="al-modal-close" onclick="alClose()"><i class="bi bi-x-lg"></i></button>
    </div>
    <div class="al-modal-body" id="alModalBody">
      <div class="al-empty-sm">Loading…</div>
    </div>
  </div>
</div>
@include('partials.sr_tracking_modal')
@endsection

@push('scripts')
<script>
var AL_SHOW = "{{ url('activity-log') }}";

function alEsc(s){
  return String(s==null?'':s).replace(/[&<>"']/g,function(c){
    return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c];
  });
}
function alFmt(v){
  if(v===null||v===undefined||v==='') return '—';
  if(typeof v==='object') return JSON.stringify(v);
  return String(v);
}

function alClose(){ document.getElementById('alModal').classList.remove('show'); }

function alView(id){
  var m = document.getElementById('alModal');
  var b = document.getElementById('alModalBody');
  b.innerHTML = '<div class="al-empty-sm">Loading…</div>';
  m.classList.add('show');

  fetch(AL_SHOW + '/' + id, {headers:{'X-Requested-With':'XMLHttpRequest','Accept':'application/json'}})
    .then(function(r){ return r.json(); })
    .then(function(res){
      var d = res.data || res;
      var oldV = d.old_values || {};
      var newV = d.new_values || {};
      var keys = Object.keys(newV).length ? Object.keys(newV) : Object.keys(oldV);
      var act  = String(d.activity_type||'').toLowerCase();
      var cls  = {created:'act-created',updated:'act-updated',deleted:'act-deleted'}[act] || 'act-other';

      var rows = keys.map(function(k){
        return '<tr>'+
          '<td class="f-name">'+alEsc(k)+'</td>'+
          '<td class="v-old">'+alEsc(alFmt(oldV[k]))+'</td>'+
          '<td class="v-new">'+alEsc(alFmt(newV[k]))+'</td>'+
        '</tr>';
      }).join('');

      var diff = keys.length
        ? '<div class="al-diff"><table><thead><tr><th>Field</th><th>Before</th><th>After</th></tr></thead><tbody>'+rows+'</tbody></table></div>'
        : '<div class="al-empty-sm">No field-level changes recorded for this entry.</div>';

      b.innerHTML =
        '<div class="al-meta-grid">'+
          '<div class="al-meta-item"><div class="al-meta-lbl">User</div><div class="al-meta-val">'+alEsc(d.user && d.user.name ? d.user.name : 'System')+'</div></div>'+
          '<div class="al-meta-item"><div class="al-meta-lbl">When</div><div class="al-meta-val">'+alEsc(d.created_at_human || d.created_at || '—')+'</div></div>'+
          '<div class="al-meta-item"><div class="al-meta-lbl">Module</div><div class="al-meta-val">'+alEsc(d.module||'—')+'</div></div>'+
          '<div class="al-meta-item"><div class="al-meta-lbl">Action</div><div class="al-meta-val"><span class="sbadge '+cls+'">'+alEsc(act||'—')+'</span></div></div>'+
          '<div class="al-meta-item"><div class="al-meta-lbl">Record</div><div class="al-meta-val">'+
  (d.module === 'ServiceRequest' && d.srId
    ? '<span class="sr-ref-trigger" data-sr-id="'+d.srId+'" onclick="openSrTracking('+d.srId+');">'+alEsc(d.record_label||'—')+'</span>'
    : alEsc(d.record_label || '—'))+
'</div></div>'+
          '<div class="al-meta-item"><div class="al-meta-lbl">IP Address</div><div class="al-meta-val">'+alEsc(d.ip_address||'—')+'</div></div>'+
        '</div>'+
        '<p class="al-sec-title">Description</p>'+
        '<div class="al-empty-sm" style="text-align:left;">'+alEsc(d.description||'—')+'</div>'+
        '<p class="al-sec-title" style="margin-top:18px;">Field Changes</p>'+
        diff+
        '<p class="al-sec-title" style="margin-top:18px;">User Agent</p>'+
        '<div class="al-ua">'+alEsc(d.user_agent||'—')+'</div>';
    })
    .catch(function(){
      b.innerHTML = '<div class="al-empty-sm">Could not load this entry.</div>';
    });
}

document.addEventListener('keydown', function(e){ if(e.key==='Escape') alClose(); });
</script>
@endpush