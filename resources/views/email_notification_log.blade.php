@extends('layouts.layout')

@section('title', 'Email Notification Log | Aiywah FSM')
@section('page_title', 'Email Notification Log')
@section('page_icon', 'mail')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600;700&display=swap" rel="stylesheet"/>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet"/>
<style>
/* ===== Email Notification Log - scoped page styles =====
   All classes use the em- prefix so this page is fully isolated from
   the WhatsApp log page - the two can share the same codebase without
   element-id collisions when one is open in a tab and the other reloads.
*/
.em-wrap{--gold:#9a8053;--gold-2:#b8975e;
  --table-header:#f7f9fd;
  --row-ok:rgba(16,185,129,.06);--row-warn:rgba(245,158,11,.07);--row-breach:rgba(239,68,68,.07);}
[data-bs-theme="dark"] .em-wrap{--table-header:#2a2928;
  --row-ok:rgba(16,185,129,.1);--row-warn:rgba(245,158,11,.1);--row-breach:rgba(239,68,68,.1);}
.em-wrap h4,.em-wrap h5,.em-wrap h6,.em-wrap .pg-header h4,.em-wrap .tbl-card-title,.em-wrap .stat-num{
  letter-spacing:-.01em;}

/* PAGE HEADER */
.em-wrap .pg-header{background:linear-gradient(135deg,#9A7B4F 0%,#7A6140 100%);border-radius:10px;padding:20px 24px;margin-bottom:20px;color:#fff;position:relative;overflow:hidden;}
.em-wrap .pg-header::before{content:'';position:absolute;left:-40px;bottom:-40px;width:180px;height:180px;border-radius:50%;background:rgba(255,255,255,.05);}
.em-wrap .pg-header::after{content:'';position:absolute;right:-30px;top:-30px;width:160px;height:160px;border-radius:50%;background:rgba(255,255,255,.07);}
.em-wrap .pg-header h4{font-size:1rem;font-weight:600;margin:0 0 3px;position:relative;z-index:1;}
.em-wrap .pg-header p{font-size:.78rem;margin:0;opacity:.85;position:relative;z-index:1;}
.em-wrap .pg-header .meta-row{display:flex;align-items:center;gap:8px;margin-top:9px;position:relative;z-index:1;flex-wrap:wrap;}
.em-wrap .meta-badge{background:rgba(255,255,255,.18);border:1px solid rgba(255,255,255,.3);border-radius:20px;font-size:.6875rem;padding:2px 10px;font-weight:500;}
.em-wrap td.mono .sr-ref-trigger{ cursor:pointer; }

/* STATS STRIP */
.em-wrap .stats-strip{display:grid;grid-template-columns:repeat(4,1fr);gap:10px;margin-bottom:18px;}
.em-wrap .stat-card{background:var(--card-bg);border:1px solid var(--card-border);border-radius:9px;padding:13px 15px;display:flex;align-items:center;gap:12px;box-shadow:var(--card-shadow);}
.em-wrap .stat-icon{width:38px;height:38px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:1rem;flex-shrink:0;}
.em-wrap .stat-num{font-size:1.5rem;font-weight:700;line-height:1;color:var(--text-heading);}
.em-wrap .stat-lbl{font-size:.7rem;color:var(--text-muted);}
@media(max-width:767px){.em-wrap .stats-strip{grid-template-columns:repeat(2,1fr);}}
@media(max-width:420px){.em-wrap .stats-strip{grid-template-columns:1fr;}}

/* FILTER BAR */
.em-wrap .filter-bar{background:var(--card-bg);border:1px solid var(--card-border);border-radius:10px;padding:14px 16px;margin-bottom:16px;box-shadow:var(--card-shadow);display:flex;flex-wrap:wrap;gap:10px;align-items:flex-end;}
.em-wrap .filter-group{display:flex;flex-direction:column;gap:4px;min-width:0;flex: 1 1 auto;}
.em-wrap .filter-label{font-size:.7rem;font-weight:600;color:var(--text-muted);text-transform:uppercase;letter-spacing:.05em;}
.em-wrap .filter-control{height:34px;padding:0 10px;border:1px solid var(--border-color);border-radius:7px;background:var(--input-bg);color:var(--text-primary);font-size:.8rem;min-width:130px;transition:border-color .15s,box-shadow .15s;}
.em-wrap .filter-control:focus{outline:none;border-color:var(--gold);box-shadow:0 0 0 3px rgba(154,128,83,.12);}
.em-wrap .filter-actions{margin-left:auto;display:flex;gap:8px;align-items:flex-end;flex-wrap:wrap;}
@media(max-width:575.98px){
  .em-wrap .filter-group{flex:1 1 100%;}
  .em-wrap .filter-control{width:100%!important;min-width:0;}
  .em-wrap .filter-actions{margin-left:0;width:100%;}
  .em-wrap .filter-actions>*{flex:1;justify-content:center;}
}

/* TABLE CARD */
.em-wrap .tbl-card{background:var(--card-bg);border:1px solid var(--card-border);border-radius:10px;box-shadow:var(--card-shadow);overflow:hidden;}
.em-wrap .tbl-card-hdr{display:flex;align-items:center;justify-content:space-between;padding:13px 18px;border-bottom:1px solid var(--border-color);flex-wrap:wrap;gap:10px;}
.em-wrap .tbl-card-hdr-left{display:flex;align-items:center;gap:10px;}
.em-wrap .tbl-card-title{font-size:.875rem;font-weight:600;color:var(--text-heading);}
.em-wrap .result-count{font-size:.75rem;color:var(--text-muted);background:var(--surface-2);padding:2px 9px;border-radius:9px;}
.em-wrap .tbl-wrap{overflow-x:auto;-webkit-overflow-scrolling:touch;}
.em-wrap table.listing{width:100%;border-collapse:collapse;min-width:900px;}
.em-wrap table.listing thead th{padding:10px 16px;font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--text-muted);background:var(--table-header);border-bottom:1px solid var(--border-color);white-space:nowrap;text-align:left;}
.em-wrap table.listing tbody tr{border-bottom:1px solid var(--border-color);transition:background .1s;cursor:pointer;}
.em-wrap table.listing tbody tr:last-child{border-bottom:none;}
.em-wrap table.listing tbody tr:hover{background:var(--table-hover);}
.em-wrap table.listing td{padding:11px 16px;font-size:.8125rem;color:var(--text-primary);vertical-align:middle;}
.em-wrap table.listing td.muted{color:var(--text-muted);font-size:.78rem;}
.em-wrap table.listing td.mono{font-size:.78rem;font-weight:600;color:var(--gold);}

/* BUTTONS */
.em-wrap .btn-gold{display:inline-flex;align-items:center;gap:6px;padding:7px 14px;background:linear-gradient(135deg,var(--gold),var(--gold-2));color:#fff;border:none;border-radius:7px;font-size:.8rem;font-weight:500;cursor:pointer;white-space:nowrap;}
.em-wrap .btn-gold:hover{opacity:.87;}
.em-wrap .btn-ghost{display:inline-flex;align-items:center;gap:6px;padding:7px 12px;background:var(--surface-2);color:var(--text-muted);border:1px solid var(--border-color);border-radius:7px;font-size:.8rem;cursor:pointer;white-space:nowrap;}
.em-wrap .btn-ghost:hover{background:var(--surface-3);}
.em-wrap .btn-xs{padding:4px 9px;border-radius:5px;font-size:.75rem;border:none;cursor:pointer;display:inline-flex;align-items:center;gap:4px;transition:background .12s;}
.em-wrap .btn-xs-view{background:rgba(37,99,235,.1);color:#3b82f6;}
.em-wrap .btn-xs-view:hover{background:rgba(37,99,235,.2);}
.em-wrap .btn-xs-retry{background:rgba(245,158,11,.1);color:#d97706;}
.em-wrap .btn-xs-retry:hover{background:rgba(245,158,11,.2);}

/* BADGES - email statuses are Sent / Failed / Pending (no 'Delivered' - SMTP has no delivery receipt) */
.em-wrap .sbadge{display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:20px;font-size:.7rem;font-weight:600;white-space:nowrap;}
.em-wrap .em-status-sent{background:rgba(16,185,129,.1);color:#059669;}
.em-wrap .em-status-failed{background:rgba(239,68,68,.1);color:#ef4444;}
.em-wrap .em-status-pending{background:rgba(245,158,11,.1);color:#d97706;}
.em-wrap .msg-preview{font-size:.75rem;color:var(--text-muted);max-width:260px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
.em-wrap .trigger-txt{font-size:.75rem;font-weight:500;color:var(--text-heading);}

/* PAGINATION */
.em-wrap .pagination-bar{display:flex;align-items:center;justify-content:space-between;padding:12px 18px;border-top:1px solid var(--border-color);flex-wrap:wrap;gap:8px;}
.em-wrap .page-info{font-size:.78rem;color:var(--text-muted);}
.em-wrap .page-btns{display:flex;gap:4px;flex-wrap:wrap;}
.em-wrap .page-btn{width:30px;height:30px;border-radius:6px;border:1px solid var(--border-color);background:var(--card-bg);color:var(--text-muted);cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:.78rem;transition:all .12s;flex-shrink:0;}
.em-wrap .page-btn.page-btn-nav{width:auto;min-width:30px;padding:0 10px;gap:4px;}
.em-wrap .page-btn:hover{border-color:var(--gold);color:var(--gold);}
.em-wrap .page-btn.active{background:var(--gold);color:#fff;border-color:var(--gold);}

/* EMPTY STATE */
.em-wrap .empty-row td{text-align:center;padding:40px 20px;color:var(--text-muted);font-size:.82rem;}
.em-wrap .empty-row td i{display:block;font-size:2rem;color:var(--text-light);margin-bottom:8px;}

/* TOAST */
.em-toast-wrap{position:fixed;bottom:22px;right:22px;z-index:9999;display:flex;flex-direction:column;gap:8px;pointer-events:none;max-width:calc(100vw - 44px);}
.em-toast-wrap .toast-item{display:flex;align-items:flex-start;gap:10px;padding:12px 15px;border-radius:9px;background:var(--card-bg);border:1px solid var(--card-border);box-shadow:0 6px 24px rgba(0,0,0,.14);min-width:240px;max-width:320px;animation:emToastIn .2s ease;pointer-events:auto;}
@keyframes emToastIn{from{opacity:0;transform:translateY(10px);}to{opacity:1;transform:translateY(0);}}
.em-toast-wrap .t-ico{font-size:1rem;flex-shrink:0;margin-top:1px;}
.em-toast-wrap .t-ico.ok{color:#10b981;}.em-toast-wrap .t-ico.err{color:#ef4444;}.em-toast-wrap .t-ico.info{color:var(--gold);}
.em-toast-wrap .t-title{font-size:.8rem;font-weight:600;color:var(--text-heading);margin:0 0 2px;}
.em-toast-wrap .t-body{font-size:.75rem;color:var(--text-muted);margin:0;}
@media(max-width:575.98px){.em-toast-wrap{left:12px;right:12px;bottom:12px;}.em-toast-wrap .toast-item{max-width:none;}}
</style>
@endpush

@section('content')
<div class="em-wrap">

  {{-- PAGE HEADER --}}
  <div class="pg-header">
    <h4><i class="bi bi-envelope me-2"></i>Email Notification Log</h4>
    <p>Audit trail of all outbound emails dispatched by the system. Monitor delivery status, retry failed emails and preview message content.</p>
    <div class="meta-row">
      <span class="meta-badge"><i class="bi bi-shield-fill-check me-1"></i>Super Admin</span>
      <span class="meta-badge"><i class="bi bi-person-gear me-1"></i>Admin</span>
    </div>
  </div>

  {{-- STATS --}}
  <div class="stats-strip">
    <div class="stat-card">
      <div class="stat-icon" style="background:rgba(16,185,129,.1);"><i class="bi bi-envelope-check" style="color:#059669;"></i></div>
      <div><div class="stat-num">{{ $stats['delivered'] ?? 0 }}</div><div class="stat-lbl">Sent Today</div></div>
    </div>
    <div class="stat-card">
      <div class="stat-icon" style="background:rgba(245,158,11,.1);"><i class="bi bi-hourglass-split" style="color:#f59e0b;"></i></div>
      <div><div class="stat-num">{{ $stats['pending'] ?? 0 }}</div><div class="stat-lbl">Pending</div></div>
    </div>
    <div class="stat-card">
      <div class="stat-icon" style="background:rgba(239,68,68,.1);"><i class="bi bi-envelope-x" style="color:#ef4444;"></i></div>
      <div><div class="stat-num">{{ $stats['failed'] ?? 0 }}</div><div class="stat-lbl">Failed - Action Needed</div></div>
    </div>
    <div class="stat-card">
      <div class="stat-icon" style="background:rgba(37,99,235,.1);"><i class="bi bi-envelope-paper" style="color:#3b82f6;"></i></div>
      <div><div class="stat-num">{{ $stats['total'] ?? 0 }}</div><div class="stat-lbl">Total This Month</div></div>
    </div>
  </div>

  {{-- FILTER BAR --}}
  <div class="filter-bar">
    <div class="filter-group">
      <div class="filter-label">SR ID / Recipient</div>
      <input class="filter-control" type="text" id="em-sr" placeholder="SR-2026-… or email" oninput="emFilter(true)"/>
    </div>
    <div class="filter-group">
      <div class="filter-label">Trigger Event</div>
      <select class="filter-control" id="em-event" onchange="emFilter(true)">
        <option value="">All Events</option>
        @foreach($events as $ev)
          <option value="{{ $ev }}">{{ $ev }}</option>
        @endforeach
      </select>
    </div>
    <div class="filter-group">
      <div class="filter-label">Delivery Status</div>
      <select class="filter-control" id="em-status" onchange="emFilter(true)">
        <option value="">All</option>
        <option>Sent</option>
        <option>Failed</option>
        <option>Pending</option>
      </select>
    </div>
    <div class="filter-group">
      <div class="filter-label">Date From</div>
      <input class="filter-control" type="date" id="em-date"/>
    </div>
    <div class="filter-group">
      <div class="filter-label">Date To</div>
      <input class="filter-control" type="date" id="em-date-to"/>
    </div>
    <div class="filter-actions">
      <button class="btn-ghost" onclick="emReset()"><i class="bi bi-x-circle"></i>Reset</button>
      <button class="btn-xs btn-xs-retry" style="padding:7px 13px;border-radius:7px;font-size:.8rem;" onclick="emRetryAll()"><i class="bi bi-arrow-clockwise"></i>Retry All Failed</button>
      <a class="btn-gold" id="em-export" href="{{ route('email_notification_log.export') }}"><i class="bi bi-download"></i>Export</a>
    </div>
  </div>

  {{-- TABLE --}}
  <div class="tbl-card">
    <div class="tbl-card-hdr">
      <div class="tbl-card-hdr-left">
        <span class="tbl-card-title">Email Log</span>
        <span class="result-count" id="em-count">0 emails</span>
      </div>
      <div style="display:flex;align-items:center;gap:6px;font-size:.75rem;color:var(--text-muted);">
        <i class="bi bi-arrow-clockwise" style="cursor:pointer;color:#9a8053;" onclick="emShowToast('info','Refreshed','Log updated.')"></i>Auto-refreshes every 60s
      </div>
    </div>
    <div class="tbl-wrap">
      <table class="listing">
        <thead>
          <tr>
            <th>SR ID</th>
            <th>Recipient</th>
            <th>Customer</th>
            <th>Trigger Event</th>
            <th>Delivery</th>
            <th style="max-width:260px;">Subject / Preview</th>
            <th>Timestamp</th>
            <th style="width:100px;">Actions</th>
          </tr>
        </thead>
        <tbody id="em-tbody"></tbody>
      </table>
    </div>
    <div class="pagination-bar" id="em-pagination-bar">
      <div class="page-info" id="em-page-info">Page {{ $paginator->currentPage() }} of {{ $paginator->lastPage() }} · {{ $paginator->total() }} total</div>
      <div class="page-btns" id="em-page-btns">
        @foreach($paginator->linkCollection() as $link)
          @php
            $isPrev = str_contains($link['label'], 'Previous');
            $isNext = str_contains($link['label'], 'Next');
            $page = null;
            if ($link['url']) {
              parse_str(parse_url($link['url'], PHP_URL_QUERY) ?? '', $q);
              $page = $q['page'] ?? 1;
            }
          @endphp
          <a class="page-btn {{ $link['active'] ? 'active' : '' }} {{ $isPrev || $isNext ? 'page-btn-nav' : '' }}"
            href="#"
            data-page="{{ $page }}"
            onclick="event.preventDefault(); if({{ $link['url'] ? 'true' : 'false' }}) emGoToPage({{ $page ?? 'null' }});"
            style="{{ $link['url'] ? '' : 'opacity:.4;pointer-events:none;' }}text-decoration:none;">
            @if($isPrev)
              <i class="bi bi-chevron-left"></i>
            @elseif($isNext)
              <i class="bi bi-chevron-right"></i>
            @else
              {!! $link['label'] !!}
            @endif
          </a>
        @endforeach
      </div>
    </div>
  </div>

  <div class="em-toast-wrap" id="emToastWrap"></div>
</div>
@include('partials.sr_tracking_modal')
@endsection

@push('scripts')
<script>
var EM_LOGS   = @json($logs);
var EM_ROUTES = {
  retry:    "{{ url('email_notification_log') }}",
  retryAll: "{{ route('email_notification_log.retryAll') }}",
  show:     "{{ url('email_notification_log') }}",
  index:    "{{ route('email_notification_log') }}",
  export:   "{{ route('email_notification_log.export') }}"
};
var EM_CSRF = "{{ csrf_token() }}";

var EM_STATUS_CLS = {
  'Sent':'em-status-sent',
  'Failed':'em-status-failed',
  'Pending':'em-status-pending'
};

var EM_TOTAL_MONTH    = {{ $totalThisMonth }};
var EM_FILTERED_TOTAL = {{ $paginator->total() }};
var EM_CURRENT_PAGE   = {{ $paginator->currentPage() }};

function emEsc(s){return String(s==null?'':s).replace(/[&<>"']/g,function(c){return{'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c];});}

function emRenderRows(list, filteredTotal){
  var tbody = document.getElementById('em-tbody');
  var total = (filteredTotal !== undefined && filteredTotal !== null) ? filteredTotal : list.length;
  document.getElementById('em-count').textContent = total + (total === 1 ? ' email' : ' emails');

  if(!list.length){
    tbody.innerHTML = '<tr class="empty-row"><td colspan="8"><i class="bi bi-inbox"></i>No emails found</td></tr>';
    return;
  }
  tbody.innerHTML = list.map(function(m){
    var cls = EM_STATUS_CLS[m.status] || 'em-status-pending';
    var retryBtn = (m.status === 'Failed')
      ? '<button class="btn-xs btn-xs-retry" onclick="event.stopPropagation();emRetry('+m.id+')"><i class="bi bi-arrow-clockwise"></i>Retry</button>'
      : '';
    // Subject preferred, fall back to message preview (both come from the EmailLog toRowArray()).
    var preview = m.subject || m.message || '';
    return '<tr>'+
      '<td class="mono">'+(m.srId
          ? '<span class="sr-ref-trigger" data-sr-id="'+m.srId+'" onclick="event.stopPropagation(); if(window.openSrTracking) openSrTracking('+m.srId+');">'+emEsc(m.sr)+'</span>'
          : emEsc(m.sr))+'</td>'+
      '<td class="muted">'+emEsc(m.recipient)+'</td>'+
      '<td style="font-size:.8rem;">'+emEsc(m.client)+'</td>'+
      '<td><span class="trigger-txt">'+emEsc(m.event)+'</span></td>'+
      '<td><span class="sbadge '+cls+'"><i class="bi bi-circle-fill" style="font-size:.4rem;"></i>'+emEsc(m.status)+'</span></td>'+
      '<td><div class="msg-preview">'+emEsc(preview)+'</div></td>'+
      '<td class="muted" style="font-size:.75rem;white-space:nowrap;">'+emEsc(m.time)+'</td>'+
      '<td><div style="display:flex;gap:5px;">'+retryBtn+
        '<button class="btn-xs btn-xs-view" onclick="event.stopPropagation();emView('+m.id+')"><i class="bi bi-eye"></i></button>'+
      '</div></td>'+
    '</tr>';
  }).join('');
}

function emParams(){
  var p = new URLSearchParams();
  var sr   = document.getElementById('em-sr').value;
  var ev   = document.getElementById('em-event').value;
  var st   = document.getElementById('em-status').value;
  var dt   = document.getElementById('em-date').value;
  var dtTo = document.getElementById('em-date-to').value;
  if(sr) p.set('sr', sr);
  if(ev) p.set('event', ev);
  if(st) p.set('status', st);
  if(dt) p.set('date_from', dt);
  if(dtTo) p.set('date_to', dtTo);
  if(EM_CURRENT_PAGE && EM_CURRENT_PAGE > 1) p.set('page', EM_CURRENT_PAGE);
  return p;
}

var emTimer = null;
function emFilter(resetPage){
  clearTimeout(emTimer);
  emTimer = setTimeout(function(){
    if(resetPage) EM_CURRENT_PAGE = 1;
    var p = emParams();
    document.getElementById('em-export').href = EM_ROUTES.export + '?' + p.toString();
    fetch(EM_ROUTES.index + '?' + p.toString(), {headers:{'X-Requested-With':'XMLHttpRequest'}})
      .then(function(r){return r.json();})
      .then(function(d){
        EM_LOGS = d.logs;
        EM_FILTERED_TOTAL = d.meta.total;
        emRenderRows(EM_LOGS, EM_FILTERED_TOTAL);
        emRenderPagination(d.meta);
      })
      .catch(function(){ emShowToast('err','Error','Could not load logs.'); });
  }, 250);
}

function emReset(){
  ['em-sr','em-event','em-status','em-date','em-date-to'].forEach(function(id){document.getElementById(id).value='';});
  EM_CURRENT_PAGE = 1;
  emFilter();
}

function emRetry(id){
  fetch(EM_ROUTES.retry + '/' + id + '/retry', {
    method:'POST', headers:{'X-CSRF-TOKEN':EM_CSRF,'X-Requested-With':'XMLHttpRequest'}
  })
  .then(function(r){return r.json();})
  .then(function(d){ emShowToast(d.ok?'ok':'err','Retry',d.message); emFilter(); })
  .catch(function(){ emShowToast('err','Retry','Request failed.'); });
}

function emRetryAll(){
  fetch(EM_ROUTES.retryAll, {
    method:'POST', headers:{'X-CSRF-TOKEN':EM_CSRF,'X-Requested-With':'XMLHttpRequest'}
  })
  .then(function(r){return r.json();})
  .then(function(d){ emShowToast('ok','Retry All',d.message); emFilter(); })
  .catch(function(){ emShowToast('err','Retry All','Request failed.'); });
}

function emView(id){
  fetch(EM_ROUTES.show + '/' + id, {headers:{'X-Requested-With':'XMLHttpRequest'}})
    .then(function(r){return r.json();})
    .then(function(d){
      var title = (d.sr || '—') + ' · ' + (d.status || '—');
      var body  = d.subject
        ? (d.subject + (d.error ? ' · ' + d.error : (d.message ? ' · ' + d.message : '')))
        : (d.error || d.message || 'No preview available.');
      emShowToast('info', title, body);
    })
    .catch(function(){ emShowToast('err','Preview','Could not load email.'); });
}

function emShowToast(type,title,body){
  var w = document.getElementById('emToastWrap');
  var icons = {ok:'bi-check-circle-fill',err:'bi-x-circle-fill',info:'bi-info-circle-fill'};
  var t = document.createElement('div');
  t.className = 'toast-item';
  t.innerHTML = '<i class="bi '+(icons[type]||icons.info)+' t-ico '+type+'"></i><div><p class="t-title">'+emEsc(title)+'</p><p class="t-body">'+emEsc(body)+'</p></div>';
  w.appendChild(t);
  setTimeout(function(){t.style.transition='opacity .3s';t.style.opacity='0';setTimeout(function(){t.remove();},300);},3500);
}

function emGoToPage(page){
  if(!page) return;
  EM_CURRENT_PAGE = page;
  emFilter();
}

function emRenderPagination(meta){
  EM_CURRENT_PAGE = meta.current_page;
  document.getElementById('em-page-info').textContent =
    'Page ' + meta.current_page + ' of ' + meta.last_page + ' · ' + meta.total + ' total';

  var btns = document.getElementById('em-page-btns');
  btns.innerHTML = meta.links.map(function(link){
    var isPrev = link.label.indexOf('Previous') !== -1;
    var isNext = link.label.indexOf('Next') !== -1;
    var page = null;
    if (link.url) {
      try {
        var u = new URL(link.url, window.location.origin);
        page = u.searchParams.get('page') || 1;
      } catch(e){}
    }
    var cls = 'page-btn' + (link.active ? ' active' : '') + ((isPrev || isNext) ? ' page-btn-nav' : '');
    var style = link.url ? '' : 'opacity:.4;pointer-events:none;';
    var label = isPrev
      ? '<i class="bi bi-chevron-left"></i>'
      : isNext
        ? '<i class="bi bi-chevron-right"></i>'
        : link.label;
    var onclick = link.url ? 'emGoToPage(' + (page || 1) + ')' : '';
    return '<a class="'+cls+'" href="#" style="'+style+'text-decoration:none;" onclick="event.preventDefault();'+onclick+'">'+label+'</a>';
  }).join('');
}

document.getElementById('em-date').addEventListener('change', function(){ emFilter(true); });
document.getElementById('em-date-to').addEventListener('change', function(){ emFilter(true); });
document.addEventListener('DOMContentLoaded', function(){
  emRenderRows(EM_LOGS, EM_FILTERED_TOTAL);
  setInterval(emFilter, 60000);
});
</script>
@endpush