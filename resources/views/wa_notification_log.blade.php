@extends('layouts.layout')

@section('title', 'WhatsApp Notification Log | Matter Mind')
@section('page_title', 'WhatsApp Notification Log')
@section('page_icon', 'database')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600;700&display=swap" rel="stylesheet"/>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet"/>
<style>
/* ===== WA Notification Log — scoped page styles ===== */
.wa-wrap{--gold:#9a8053;--gold-2:#b8975e;
  --table-header:#f7f9fd;
  --row-ok:rgba(16,185,129,.06);--row-warn:rgba(245,158,11,.07);--row-breach:rgba(239,68,68,.07);}
[data-bs-theme="dark"] .wa-wrap{--table-header:#2a2928;
  --row-ok:rgba(16,185,129,.1);--row-warn:rgba(245,158,11,.1);--row-breach:rgba(239,68,68,.1);}
.wa-wrap h4,.wa-wrap h5,.wa-wrap h6,.wa-wrap .pg-header h4,.wa-wrap .tbl-card-title,.wa-wrap .stat-num{
  font-family:'Cormorant Garamond', Georgia, serif;letter-spacing:-.01em;}

/* PAGE HEADER */
.wa-wrap .pg-header{background:linear-gradient(135deg,#9A7B4F 0%,#7A6140 100%);border-radius:10px;padding:20px 24px;margin-bottom:20px;color:#fff;position:relative;overflow:hidden;}
.wa-wrap .pg-header::before{content:'';position:absolute;left:-40px;bottom:-40px;width:180px;height:180px;border-radius:50%;background:rgba(255,255,255,.05);}
.wa-wrap .pg-header::after{content:'';position:absolute;right:-30px;top:-30px;width:160px;height:160px;border-radius:50%;background:rgba(255,255,255,.07);}
.wa-wrap .pg-header h4{font-size:1rem;font-weight:600;margin:0 0 3px;position:relative;z-index:1;}
.wa-wrap .pg-header p{font-size:.78rem;margin:0;opacity:.85;position:relative;z-index:1;}
.wa-wrap .pg-header .meta-row{display:flex;align-items:center;gap:8px;margin-top:9px;position:relative;z-index:1;flex-wrap:wrap;}
.wa-wrap .meta-badge{background:rgba(255,255,255,.18);border:1px solid rgba(255,255,255,.3);border-radius:20px;font-size:.6875rem;padding:2px 10px;font-weight:500;}

/* STATS STRIP */
.wa-wrap .stats-strip{display:grid;grid-template-columns:repeat(4,1fr);gap:10px;margin-bottom:18px;}
.wa-wrap .stat-card{background:var(--card-bg);border:1px solid var(--card-border);border-radius:9px;padding:13px 15px;display:flex;align-items:center;gap:12px;box-shadow:var(--card-shadow);}
.wa-wrap .stat-icon{width:38px;height:38px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:1rem;flex-shrink:0;}
.wa-wrap .stat-num{font-size:1.5rem;font-weight:700;line-height:1;color:var(--text-heading);}
.wa-wrap .stat-lbl{font-size:.7rem;color:var(--text-muted);}
@media(max-width:767px){.wa-wrap .stats-strip{grid-template-columns:repeat(2,1fr);}}
@media(max-width:420px){.wa-wrap .stats-strip{grid-template-columns:1fr;}}

/* FILTER BAR */
.wa-wrap .filter-bar{background:var(--card-bg);border:1px solid var(--card-border);border-radius:10px;padding:14px 16px;margin-bottom:16px;box-shadow:var(--card-shadow);display:flex;flex-wrap:wrap;gap:10px;align-items:flex-end;}
.wa-wrap .filter-group{display:flex;flex-direction:column;gap:4px;min-width:0;flex: 1 1 auto;}
.wa-wrap .filter-label{font-size:.7rem;font-weight:600;color:var(--text-muted);text-transform:uppercase;letter-spacing:.05em;}
.wa-wrap .filter-control{height:34px;padding:0 10px;border:1px solid var(--border-color);border-radius:7px;background:var(--input-bg);color:var(--text-primary);font-size:.8rem;min-width:130px;transition:border-color .15s,box-shadow .15s;}
.wa-wrap .filter-control:focus{outline:none;border-color:var(--gold);box-shadow:0 0 0 3px rgba(154,128,83,.12);}
.wa-wrap .filter-actions{margin-left:auto;display:flex;gap:8px;align-items:flex-end;flex-wrap:wrap;}
@media(max-width:575.98px){
  .wa-wrap .filter-group{flex:1 1 100%;}
  .wa-wrap .filter-control{width:100%!important;min-width:0;}
  .wa-wrap .filter-actions{margin-left:0;width:100%;}
  .wa-wrap .filter-actions>*{flex:1;justify-content:center;}
}

/* TABLE CARD */
.wa-wrap .tbl-card{background:var(--card-bg);border:1px solid var(--card-border);border-radius:10px;box-shadow:var(--card-shadow);overflow:hidden;}
.wa-wrap .tbl-card-hdr{display:flex;align-items:center;justify-content:space-between;padding:13px 18px;border-bottom:1px solid var(--border-color);flex-wrap:wrap;gap:10px;}
.wa-wrap .tbl-card-hdr-left{display:flex;align-items:center;gap:10px;}
.wa-wrap .tbl-card-title{font-size:.875rem;font-weight:600;color:var(--text-heading);}
.wa-wrap .result-count{font-size:.75rem;color:var(--text-muted);background:var(--surface-2);padding:2px 9px;border-radius:9px;}
.wa-wrap .tbl-wrap{overflow-x:auto;-webkit-overflow-scrolling:touch;}
.wa-wrap table.listing{width:100%;border-collapse:collapse;min-width:900px;}
.wa-wrap table.listing thead th{padding:10px 16px;font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--text-muted);background:var(--table-header);border-bottom:1px solid var(--border-color);white-space:nowrap;text-align:left;}
.wa-wrap table.listing tbody tr{border-bottom:1px solid var(--border-color);transition:background .1s;cursor:pointer;}
.wa-wrap table.listing tbody tr:last-child{border-bottom:none;}
.wa-wrap table.listing tbody tr:hover{background:var(--table-hover);}
.wa-wrap table.listing td{padding:11px 16px;font-size:.8125rem;color:var(--text-primary);vertical-align:middle;}
.wa-wrap table.listing td.muted{color:var(--text-muted);font-size:.78rem;}
.wa-wrap table.listing td.mono{font-family:monospace;font-size:.78rem;font-weight:600;color:var(--gold);}

/* BUTTONS */
.wa-wrap .btn-gold{display:inline-flex;align-items:center;gap:6px;padding:7px 14px;background:linear-gradient(135deg,var(--gold),var(--gold-2));color:#fff;border:none;border-radius:7px;font-size:.8rem;font-weight:500;cursor:pointer;white-space:nowrap;}
.wa-wrap .btn-gold:hover{opacity:.87;}
.wa-wrap .btn-ghost{display:inline-flex;align-items:center;gap:6px;padding:7px 12px;background:var(--surface-2);color:var(--text-muted);border:1px solid var(--border-color);border-radius:7px;font-size:.8rem;cursor:pointer;white-space:nowrap;}
.wa-wrap .btn-ghost:hover{background:var(--surface-3);}
.wa-wrap .btn-xs{padding:4px 9px;border-radius:5px;font-size:.75rem;border:none;cursor:pointer;display:inline-flex;align-items:center;gap:4px;transition:background .12s;}
.wa-wrap .btn-xs-view{background:rgba(37,99,235,.1);color:#3b82f6;}
.wa-wrap .btn-xs-view:hover{background:rgba(37,99,235,.2);}
.wa-wrap .btn-xs-retry{background:rgba(245,158,11,.1);color:#d97706;}
.wa-wrap .btn-xs-retry:hover{background:rgba(245,158,11,.2);}

/* BADGES */
.wa-wrap .sbadge{display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:20px;font-size:.7rem;font-weight:600;white-space:nowrap;}
.wa-wrap .wa-status-sent{background:rgba(16,185,129,.1);color:#059669;}
.wa-wrap .wa-status-delivered{background:rgba(37,99,235,.1);color:#2563eb;}
.wa-wrap .wa-status-failed{background:rgba(239,68,68,.1);color:#ef4444;}
.wa-wrap .wa-status-pending{background:rgba(245,158,11,.1);color:#d97706;}
.wa-wrap .msg-preview{font-size:.75rem;color:var(--text-muted);max-width:260px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
.wa-wrap .trigger-txt{font-size:.75rem;font-weight:500;color:var(--text-heading);}

/* PAGINATION */
.wa-wrap .pagination-bar{display:flex;align-items:center;justify-content:space-between;padding:12px 18px;border-top:1px solid var(--border-color);flex-wrap:wrap;gap:8px;}
.wa-wrap .page-info{font-size:.78rem;color:var(--text-muted);}
.wa-wrap .page-btns{display:flex;gap:4px;flex-wrap:wrap;}
.wa-wrap .page-btn{width:30px;height:30px;border-radius:6px;border:1px solid var(--border-color);background:var(--card-bg);color:var(--text-muted);cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:.78rem;transition:all .12s;}
.wa-wrap .page-btn:hover{border-color:var(--gold);color:var(--gold);}
.wa-wrap .page-btn.active{background:var(--gold);color:#fff;border-color:var(--gold);}

/* EMPTY STATE */
.wa-wrap .empty-row td{text-align:center;padding:40px 20px;color:var(--text-muted);font-size:.82rem;}
.wa-wrap .empty-row td i{display:block;font-size:2rem;color:var(--text-light);margin-bottom:8px;}

/* TOAST */
.wa-toast-wrap{position:fixed;bottom:22px;right:22px;z-index:9999;display:flex;flex-direction:column;gap:8px;pointer-events:none;max-width:calc(100vw - 44px);}
.wa-toast-wrap .toast-item{display:flex;align-items:flex-start;gap:10px;padding:12px 15px;border-radius:9px;background:var(--card-bg);border:1px solid var(--card-border);box-shadow:0 6px 24px rgba(0,0,0,.14);min-width:240px;max-width:320px;animation:waToastIn .2s ease;pointer-events:auto;}
@keyframes waToastIn{from{opacity:0;transform:translateY(10px);}to{opacity:1;transform:translateY(0);}}
.wa-toast-wrap .t-ico{font-size:1rem;flex-shrink:0;margin-top:1px;}
.wa-toast-wrap .t-ico.ok{color:#10b981;}.wa-toast-wrap .t-ico.err{color:#ef4444;}.wa-toast-wrap .t-ico.info{color:var(--gold);}
.wa-toast-wrap .t-title{font-size:.8rem;font-weight:600;color:var(--text-heading);margin:0 0 2px;}
.wa-toast-wrap .t-body{font-size:.75rem;color:var(--text-muted);margin:0;}
@media(max-width:575.98px){.wa-toast-wrap{left:12px;right:12px;bottom:12px;}.wa-toast-wrap .toast-item{max-width:none;}}
</style>
@endpush

@section('content')
<div class="wa-wrap">

  {{-- PAGE HEADER --}}
  <div class="pg-header">
    <h4><i class="bi bi-whatsapp me-2"></i>WhatsApp Notification Log</h4>
    <p>Audit trail of all outbound WhatsApp messages dispatched by the system. Monitor delivery status, retry failed messages and preview message content.</p>
    <div class="meta-row">
      <span class="meta-badge"><i class="bi bi-shield-fill-check me-1"></i>Super Admin</span>
      <span class="meta-badge"><i class="bi bi-person-gear me-1"></i>Admin</span>
    </div>
  </div>

  {{-- STATS --}}
  <div class="stats-strip">
    <div class="stat-card">
      <div class="stat-icon" style="background:rgba(37,211,102,.1);"><i class="bi bi-send-check" style="color:#25d366;"></i></div>
      <div><div class="stat-num">{{ $stats['delivered'] ?? 0 }}</div><div class="stat-lbl">Delivered Today</div></div>
    </div>
    <div class="stat-card">
      <div class="stat-icon" style="background:rgba(245,158,11,.1);"><i class="bi bi-hourglass-split" style="color:#f59e0b;"></i></div>
      <div><div class="stat-num">{{ $stats['pending'] ?? 0 }}</div><div class="stat-lbl">Pending</div></div>
    </div>
    <div class="stat-card">
      <div class="stat-icon" style="background:rgba(239,68,68,.1);"><i class="bi bi-send-x" style="color:#ef4444;"></i></div>
      <div><div class="stat-num">{{ $stats['failed'] ?? 0 }}</div><div class="stat-lbl">Failed — Action Needed</div></div>
    </div>
    <div class="stat-card">
      <div class="stat-icon" style="background:rgba(37,99,235,.1);"><i class="bi bi-chat-dots" style="color:#3b82f6;"></i></div>
      <div><div class="stat-num">{{ $stats['total'] ?? 0 }}</div><div class="stat-lbl">Total This Month</div></div>
    </div>
  </div>

  {{-- FILTER BAR --}}
  <div class="filter-bar">
    <div class="filter-group">
      <div class="filter-label">SR ID</div>
      <input class="filter-control" type="text" id="wa-sr" placeholder="SR-2025-…" oninput="waFilter()"/>
    </div>
    <div class="filter-group">
      <div class="filter-label">Trigger Event</div>
      <select class="filter-control" id="wa-event" onchange="waFilter()">
        <option value="">All Events</option>
        <option>Inquiry Logged</option>
        <option>ETA Confirmed</option>
        <option>Punch In — Work Started</option>
        <option>SR Completed</option>
        <option>SR Cancelled / Rejected</option>
        <option>Invoice Finalized</option>
        <option>Feedback Request</option>
      </select>
    </div>
    <div class="filter-group">
      <div class="filter-label">Delivery Status</div>
      <select class="filter-control" id="wa-status" onchange="waFilter()">
        <option value="">All</option>
        <option>Delivered</option>
        <option>Sent</option>
        <option>Failed</option>
        <option>Pending</option>
      </select>
    </div>
    <div class="filter-group">
      <div class="filter-label">Date From</div>
      <input class="filter-control" type="date" id="wa-date"/>
    </div>
    <div class="filter-actions">
      <button class="btn-ghost" onclick="waReset()"><i class="bi bi-x-circle"></i>Reset</button>
      <button class="btn-xs btn-xs-retry" style="padding:7px 13px;border-radius:7px;font-size:.8rem;" onclick="showToast('ok','Retry All','All failed messages queued for retry.')"><i class="bi bi-arrow-clockwise"></i>Retry All Failed</button>
      <button class="btn-gold" onclick="showToast('ok','Export','Generating notification log CSV…')"><i class="bi bi-download"></i>Export</button>
    </div>
  </div>

  {{-- TABLE --}}
  <div class="tbl-card">
    <div class="tbl-card-hdr">
      <div class="tbl-card-hdr-left">
        <span class="tbl-card-title">Notification Log</span>
        <span class="result-count" id="wa-count">0 messages</span>
      </div>
      <div style="display:flex;align-items:center;gap:6px;font-size:.75rem;color:var(--text-muted);">
        <i class="bi bi-arrow-clockwise" style="cursor:pointer;color:#9a8053;" onclick="showToast('info','Refreshed','Log updated.')"></i>Auto-refreshes every 60s
      </div>
    </div>
    <div class="tbl-wrap">
      <table class="listing">
        <thead>
          <tr>
            <th>SR ID</th>
            <th>Recipient</th>
            <th>Client</th>
            <th>Trigger Event</th>
            <th>Delivery</th>
            <th style="max-width:260px;">Message Preview</th>
            <th>Timestamp</th>
            <th style="width:100px;">Actions</th>
          </tr>
        </thead>
        <tbody id="wa-tbody"></tbody>
      </table>
    </div>
    <div class="pagination-bar">
      <div class="page-info" id="wa-page-info">Page 1 of 1</div>
      <div class="page-btns">
        <button class="page-btn"><i class="bi bi-chevron-left"></i></button>
        <button class="page-btn active">1</button>
        <button class="page-btn"><i class="bi bi-chevron-right"></i></button>
      </div>
    </div>
  </div>

  <div class="wa-toast-wrap" id="waToastWrap"></div>
</div>
@endsection

@push('scripts')
<script>
/* =========================================================
   WA Notification Log — page scripts
   NOTE: LOGS is empty. Connect to DB later, e.g.:
   var LOGS = @json($logs ?? []);

   Row shape expected:
   { sr, recipient, client, event, status('Delivered'|'Sent'|'Failed'|'Pending'),
     message, time }
   ========================================================= */
var LOGS = @json($logs ?? []);
var WA_TOTAL = {{ $totalThisMonth ?? 0 }};

/* map status text -> css class */
var WA_STATUS_CLS = {
  'Delivered':'wa-status-delivered',
  'Sent':'wa-status-sent',
  'Failed':'wa-status-failed',
  'Pending':'wa-status-pending'
};

function esc(s){return String(s==null?'':s).replace(/[&<>"']/g,function(c){return{'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c];});}

function waRenderRows(list){
  var tbody = document.getElementById('wa-tbody');
  document.getElementById('wa-count').textContent = list.length + ' of ' + (WA_TOTAL || list.length) + ' this month';
  if(!list.length){
    tbody.innerHTML = '<tr class="empty-row"><td colspan="8"><i class="bi bi-inbox"></i>No notifications found</td></tr>';
    return;
  }
  tbody.innerHTML = list.map(function(m){
    var cls = WA_STATUS_CLS[m.status] || 'wa-status-pending';
    var retryBtn = (m.status === 'Failed')
      ? '<button class="btn-xs btn-xs-retry" onclick="event.stopPropagation();showToast(\'ok\',\'Retry Queued\',\'Message re-queued for '+esc(m.sr)+'\')"><i class="bi bi-arrow-clockwise"></i>Retry</button>'
      : '';
    return '<tr>'+
      '<td class="mono">'+esc(m.sr)+'</td>'+
      '<td class="muted">'+esc(m.recipient)+'</td>'+
      '<td style="font-size:.8rem;">'+esc(m.client)+'</td>'+
      '<td><span class="trigger-txt">'+esc(m.event)+'</span></td>'+
      '<td><span class="sbadge '+cls+'"><i class="bi bi-circle-fill" style="font-size:.4rem;"></i>'+esc(m.status)+'</span></td>'+
      '<td><div class="msg-preview">'+esc(m.message)+'</div></td>'+
      '<td class="muted" style="font-size:.75rem;white-space:nowrap;">'+esc(m.time)+'</td>'+
      '<td><div style="display:flex;gap:5px;">'+retryBtn+
        '<button class="btn-xs btn-xs-view" onclick="event.stopPropagation();showToast(\'info\',\'Preview\',\'Message preview for '+esc(m.sr)+'\')"><i class="bi bi-eye"></i></button>'+
      '</div></td>'+
    '</tr>';
  }).join('');
}

function waFilter(){
  var sr     = (document.getElementById('wa-sr').value || '').toLowerCase();
  var event  = document.getElementById('wa-event').value;
  var status = document.getElementById('wa-status').value;
  var list = LOGS.filter(function(m){
    var ms = !sr || (m.sr||'').toLowerCase().includes(sr) || (m.client||'').toLowerCase().includes(sr);
    var me = !event  || m.event === event;
    var mt = !status || m.status === status;
    return ms && me && mt;
  });
  waRenderRows(list);
}

function waReset(){
  document.getElementById('wa-sr').value = '';
  document.getElementById('wa-event').value = '';
  document.getElementById('wa-status').value = '';
  document.getElementById('wa-date').value = '';
  waRenderRows(LOGS);
}

function showToast(type,title,body){
  var w = document.getElementById('waToastWrap');
  var icons = {ok:'bi-check-circle-fill',err:'bi-x-circle-fill',info:'bi-info-circle-fill'};
  var t = document.createElement('div');
  t.className = 'toast-item';
  t.innerHTML = '<i class="bi '+(icons[type]||icons.info)+' t-ico '+type+'"></i><div><p class="t-title">'+title+'</p><p class="t-body">'+body+'</p></div>';
  w.appendChild(t);
  setTimeout(function(){t.style.transition='opacity .3s';t.style.opacity='0';setTimeout(function(){t.remove();},300);},3500);
}

document.addEventListener('DOMContentLoaded', function(){
  waRenderRows(LOGS);
});
</script>
@endpush