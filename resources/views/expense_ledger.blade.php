
{{-- resources/views/user_directory.blade.php --}}
@extends('layouts.layout')

@section('title', 'Expense Ledger | Matter Mind')
@section('page_title', 'Expense Ledger')
@section('page_icon', 'database')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600;700&display=swap" rel="stylesheet"/>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet"/>
<style>
/* ===== Expense Ledger — scoped page styles ===== */
.exl-wrap{--gold: #9a8053;--gold-2:#b8975e;}
.exl-wrap h4,.exl-wrap h5,.exl-wrap h6,.exl-wrap .pg-hdr-title,.exl-wrap .card-title{}

/* PAGE HEADER */
.exl-wrap .pg-header{background:linear-gradient(135deg,#9A7B4F 0%,#7A6140 100%);border-radius:10px;padding:20px 24px;margin-bottom:20px;color:#fff;position:relative;overflow:hidden;}
.exl-wrap .pg-header::before{content:'';position:absolute;left:-40px;bottom:-40px;width:180px;height:180px;border-radius:50%;background:rgba(255,255,255,.05);}
.exl-wrap .pg-header::after{content:'';position:absolute;right:-30px;top:-30px;width:160px;height:160px;border-radius:50%;background:rgba(255,255,255,.07);}
.exl-wrap .pg-hdr-title{font-size:1rem;font-weight:600;margin:0 0 3px;position:relative;z-index:1;}
.exl-wrap .pg-hdr-desc{font-size:.78rem;margin:0;opacity:.85;position:relative;z-index:1;}
.exl-wrap .pg-hdr-meta{display:flex;align-items:center;gap:8px;margin-top:9px;position:relative;z-index:1;flex-wrap:wrap;}
.exl-wrap .meta-badge{background:rgba(255,255,255,.18);border:1px solid rgba(255,255,255,.3);border-radius:20px;font-size:.6875rem;padding:2px 10px;font-weight:500;}

/* STATS */
.exl-wrap .stats-strip{display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,240px));gap:10px;margin-bottom:18px;}
.exl-wrap .stat-card{background:var(--card-bg);border:1px solid var(--card-border);border-radius:9px;padding:13px 15px;display:flex;align-items:center;gap:12px;box-shadow:var(--card-shadow);}
.exl-wrap .stat-icon{width:38px;height:38px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:1rem;flex-shrink:0;background:rgba(154,128,83,.1);}
.exl-wrap .stat-icon i{color:var(--gold);}
.exl-wrap .stat-num{font-size:1.4rem;font-weight:700;line-height:1;color:var(--text-heading);}
.exl-wrap .stat-lbl{font-size:.7rem;color:var(--text-muted);}
@media(max-width:767px){.exl-wrap .stats-strip{grid-template-columns:1fr;}}

/* FILTER BAR */
.exl-wrap .filter-bar{background:var(--card-bg);border:1px solid var(--card-border);border-radius:10px;padding:12px 16px;margin-bottom:16px;box-shadow:var(--card-shadow);display:flex;flex-wrap:wrap;gap:10px;align-items:flex-end;}
.exl-wrap .filter-group{display:flex;flex-direction:column;gap:4px;}
.exl-wrap .filter-label{font-size:.7rem;font-weight:600;color:var(--text-muted);text-transform:uppercase;letter-spacing:.05em;}
.exl-wrap .filter-control{height:34px;padding:0 10px;border:1px solid var(--border-color);border-radius:7px;background:var(--input-bg);color:var(--text-primary);font-size:.8rem;min-width:140px;transition:border-color .15s;}
.exl-wrap .filter-control:focus{outline:none;border-color:var(--gold);box-shadow:0 0 0 3px rgba(154,128,83,.12);}
.exl-wrap .filter-actions{margin-left:auto;display:flex;gap:8px;align-items:flex-end;flex-wrap:wrap;}
@media(max-width:575.98px){
  .exl-wrap .filter-group{flex:1 1 100%;}
  .exl-wrap .filter-control{width:100%;min-width:0;}
  .exl-wrap .filter-actions{margin-left:0;width:100%;}
  .exl-wrap .filter-actions .btn-ghost{flex:1;justify-content:center;}
}

/* BUTTONS */
.exl-wrap .btn-gold{display:inline-flex;align-items:center;gap:6px;padding:7px 14px;background:linear-gradient(135deg,var(--gold),var(--gold-2));color:#fff;border:none;border-radius:7px;font-size:.8rem;font-weight:500;cursor:pointer;white-space:nowrap;}
.exl-wrap .btn-gold:hover{opacity:.87;}
.exl-wrap .btn-ghost{display:inline-flex;align-items:center;gap:6px;padding:7px 12px;background:var(--surface-2);color:var(--text-muted);border:1px solid var(--border-color);border-radius:7px;font-size:.8rem;cursor:pointer;}
.exl-wrap .btn-ghost:hover{background:var(--surface-3);}

/* LEDGER TABLE CARD */
.exl-wrap .tbl-card{background:var(--card-bg);border:1px solid var(--card-border);border-radius:10px;box-shadow:var(--card-shadow);overflow:hidden;}
.exl-wrap .tbl-card-hdr{display:flex;align-items:center;justify-content:space-between;padding:13px 18px;border-bottom:1px solid var(--border-color);flex-wrap:wrap;gap:10px;}
.exl-wrap .card-title{font-size:.875rem;font-weight:600;color:var(--text-heading);}
.exl-wrap .result-count{font-size:.75rem;color:var(--text-muted);background:var(--surface-2);padding:2px 9px;border-radius:9px;}
.exl-wrap table.ledger{width:100%;border-collapse:collapse;}
.exl-wrap table.ledger thead th{padding:10px 14px;font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--text-muted);background:var(--surface-2);border-bottom:1px solid var(--border-color);white-space:nowrap;}
.exl-wrap table.ledger tbody tr{border-bottom:1px solid var(--border-color);transition:background .1s;}
.exl-wrap table.ledger tbody tr:last-child{border-bottom:none;}
.exl-wrap table.ledger tbody tr:hover{background:rgba(154,128,83,.035);}
.exl-wrap table.ledger td{padding:11px 14px;font-size:.8rem;color:var(--text-primary);vertical-align:middle;}
.exl-wrap table.ledger td.mono{font-size:.78rem;font-weight:600;color:var(--gold);}
.exl-wrap table.ledger td.muted{color:var(--text-muted);font-size:.78rem;}
.exl-wrap .receipt-thumb{width:42px;height:42px;border-radius:7px;background:linear-gradient(135deg,#f5f0e8,#ede3d0);border:1px solid var(--border-color);display:flex;align-items:center;justify-content:center;cursor:pointer;transition:all .15s;font-size:.95rem;color:var(--text-muted);}
.exl-wrap .receipt-thumb:hover{border-color:var(--gold);color:var(--gold);transform:scale(1.06);}
.exl-wrap .receipt-thumb.no-rcpt{cursor:default;opacity:.45;}
.exl-wrap .receipt-thumb.no-rcpt:hover{border-color:var(--border-color);color:var(--text-muted);transform:none;}
[data-bs-theme="dark"] .exl-wrap .receipt-thumb{background:linear-gradient(135deg,#2a2820,#302e24);}
.exl-wrap .ledger-foot{display:flex;align-items:center;justify-content:space-between;padding:13px 18px;border-top:1px solid var(--border-color);flex-wrap:wrap;gap:10px;background:var(--surface-2);}
.exl-wrap .ledger-totals{display:flex;align-items:center;gap:20px;flex-wrap:wrap;}
.exl-wrap .lt-label{font-size:.67rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:.06em;margin-bottom:1px;}
.exl-wrap .lt-val{font-size:.875rem;font-weight:700;color:var(--text-heading);}
.exl-wrap .tbl-scroll{overflow-x:auto;-webkit-overflow-scrolling:touch;}
.exl-wrap table.ledger{min-width:640px;}
@media(max-width:575.98px){
  .exl-wrap .ledger-foot{flex-direction:column;align-items:flex-start;}
  .exl-wrap .ledger-totals{gap:14px;}
}

/* MODAL */
.exl-wrap .modal-overlay,.exl-modal-overlay{display:none;position:fixed;inset:0;background:var(--overlay-bg,rgba(9,15,35,.62));z-index:900;align-items:center;justify-content:center;backdrop-filter:blur(4px);padding:20px;}
.exl-modal-overlay.show{display:flex;}
.exl-modal-overlay .modal-box{background:var(--modal-bg,#fff);border-radius:12px;width:100%;max-width:560px;box-shadow:var(--modal-shadow,0 24px 64px rgba(0,0,0,.16));border:1px solid var(--card-border);overflow:hidden;animation:exlMIn .18s ease;}
@keyframes exlMIn{from{opacity:0;transform:scale(.96);}to{opacity:1;transform:scale(1);}}
.exl-modal-overlay .modal-hdr{display:flex;align-items:center;justify-content:space-between;padding:15px 20px;border-bottom:1px solid var(--border-color);}
.exl-modal-overlay .modal-hdr-left{display:flex;align-items:center;gap:10px;}
.exl-modal-overlay .modal-hdr-icon{width:32px;height:32px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:.9rem;flex-shrink:0;background:rgba(245,158,11,.1);}
.exl-modal-overlay .modal-hdr h6{font-size:.9rem;font-weight:600;color:var(--text-heading);margin:0;}
.exl-modal-overlay .modal-close{background:none;border:none;color:var(--text-muted);cursor:pointer;padding:4px;border-radius:5px;font-size:1rem;line-height:1;}
.exl-modal-overlay .modal-close:hover{background:var(--surface-2);}
.exl-modal-overlay .lb-img{aspect-ratio:4/3;background:linear-gradient(145deg,#f5f0e8,#ede3d0);display:flex;flex-direction:column;align-items:center;justify-content:center;gap:10px;padding:20px;}
[data-bs-theme="dark"] .exl-modal-overlay .lb-img{background:linear-gradient(145deg,#2a2820,#302e24);}
.exl-modal-overlay .lb-foot{display:flex;align-items:center;justify-content:space-between;padding:11px 18px;border-top:1px solid var(--border-color);background:var(--surface-2);font-size:.78rem;color:var(--text-muted);flex-wrap:wrap;gap:8px;}

/* TOAST */
.exl-toast-wrap{position:fixed;bottom:22px;right:22px;z-index:9999;display:flex;flex-direction:column;gap:8px;pointer-events:none;max-width:calc(100vw - 44px);}
.exl-toast-wrap .toast-item{display:flex;align-items:flex-start;gap:10px;padding:12px 15px;border-radius:9px;background:var(--card-bg);border:1px solid var(--card-border);box-shadow:0 6px 24px rgba(0,0,0,.14);min-width:240px;max-width:320px;animation:exlToastIn .2s ease;pointer-events:auto;}
@keyframes exlToastIn{from{opacity:0;transform:translateY(10px);}to{opacity:1;transform:translateY(0);}}
.exl-toast-wrap .t-ico{font-size:1rem;flex-shrink:0;margin-top:1px;}
.exl-toast-wrap .t-ico.ok{color:#15803d;}.exl-toast-wrap .t-ico.err{color:#ef4444;}
.exl-toast-wrap .t-ico.info{color:var(--gold);}.exl-toast-wrap .t-ico.warn{color:#d97706;}
.exl-toast-wrap .t-title{font-size:.8rem;font-weight:600;color:var(--text-heading);margin:0 0 2px;}
.exl-toast-wrap .t-body{font-size:.75rem;color:var(--text-muted);margin:0;}
@media(max-width:575.98px){.exl-toast-wrap{left:12px;right:12px;bottom:12px;}.exl-toast-wrap .toast-item{max-width:none;}}
</style>
@endpush

@section('content')
<div class="exl-wrap">

  {{-- PAGE HEADER --}}
  <div class="pg-header">
    <h4 class="pg-hdr-title"><i class="bi bi-cash-stack me-2"></i>Internal Expense &amp; Material Reconciliation Ledger</h4>
    <p class="pg-hdr-desc">Audit and reconcile field technician material expenses against uploaded receipts. Save ledger changes.</p>
    <div class="pg-hdr-meta">
      <span class="meta-badge"><i class="bi bi-shield-fill-check me-1"></i>Super Admin</span>
      <span class="meta-badge"><i class="bi bi-person-gear me-1"></i>Admin</span>
      <span class="meta-badge"><i class="bi bi-person-workspace me-1"></i>Head of Projects</span>
      <span class="meta-badge"><i class="bi bi-calculator me-1"></i>Accounts / AR</span>
    </div>
  </div>

  {{-- STATS --}}
  <div class="stats-strip">
    <div class="stat-card">
      <div class="stat-icon"><i class="bi bi-cash-coin"></i></div>
      <div>
        <div class="stat-num">AED {{ number_format($totalExpenses ?? 0) }}</div>
        <div class="stat-lbl">Total Field Expenses</div>
      </div>
    </div>
  </div>

  {{-- FILTER BAR --}}
  <div class="filter-bar">
    <div class="filter-group">
      <div class="filter-label">Search SR / Technician</div>
      <input class="filter-control" type="text" id="led-search" placeholder="SR ID or technician…" oninput="exlFilterLedger(this.value)"/>
    </div>
    <div class="filter-group">
      <div class="filter-label">Category</div>
      <select class="filter-control" id="led-cat" onchange="exlFilterLedger(document.getElementById('led-search').value)">
        <option value="">All Categories</option>
        <option>Spare Parts</option>
        <option>Local Hardware Purchase</option>
        <option>Emergency Valve Fittings</option>
        <option>Consumables</option>
      </select>
    </div>
    <div class="filter-actions">
      <button class="btn-ghost" onclick="exlResetLedgerFilters()"><i class="bi bi-x-circle"></i>Reset</button>
      <button class="btn-ghost" onclick="exlShowToast('ok','Export','Generating ledger CSV…')"><i class="bi bi-download"></i>Export</button>
    </div>
  </div>

  {{-- LEDGER TABLE --}}
  <div class="tbl-card">
    <div class="tbl-card-hdr">
      <div style="display:flex;align-items:center;gap:10px;">
        <span class="card-title">Expense Reconciliation Ledger</span>
        <span class="result-count" id="led-count">0 entries</span>
      </div>
      <button class="btn-gold" onclick="exlSaveLedger()">
        <i class="bi bi-floppy"></i>Save Ledger Changes
      </button>
    </div>
    <div class="tbl-scroll">
      <table class="ledger">
        <thead>
          <tr>
            <th>#</th>
            <th>SR ID</th>
            <th>Technician</th>
            <th>Category</th>
            <th style="text-align:right;">Amount</th>
            <th>Receipt</th>
            <th>Date</th>
          </tr>
        </thead>
        <tbody id="led-tbody"></tbody>
      </table>
    </div>
    <div class="ledger-foot">
      <div class="ledger-totals">
        <div><div class="lt-label">Total</div><div class="lt-val">AED {{ number_format($totals['total'] ?? 0) }}</div></div>
        <div><div class="lt-label">Pending</div><div class="lt-val" style="color:#d97706;">AED {{ number_format($totals['pending'] ?? 0) }}</div></div>
        <div><div class="lt-label">Approved</div><div class="lt-val" style="color:#15803d;">AED {{ number_format($totals['approved'] ?? 0) }}</div></div>
        <div><div class="lt-label">Disputed</div><div class="lt-val" style="color:#ef4444;">AED {{ number_format($totals['disputed'] ?? 0) }}</div></div>
      </div>
      <div style="font-size:.72rem;color:var(--text-muted);">Last saved: {{ $lastSaved ?? '—' }}</div>
    </div>
  </div>

  {{-- RECEIPT MODAL --}}
  <div class="exl-modal-overlay" id="rcpt-modal" onclick="if(event.target===this)this.classList.remove('show')">
    <div class="modal-box">
      <div class="modal-hdr">
        <div class="modal-hdr-left">
          <div class="modal-hdr-icon"><i class="bi bi-receipt" style="color:#d97706;"></i></div>
          <h6 id="rcpt-title">Receipt Image</h6>
        </div>
        <button class="modal-close" onclick="document.getElementById('rcpt-modal').classList.remove('show')"><i class="bi bi-x-lg"></i></button>
      </div>
      <div class="lb-img">
        <i class="bi bi-receipt" style="font-size:3rem;opacity:.2;"></i>
        <div style="text-align:center;">
          <div id="rcpt-label" style="font-size:.85rem;font-weight:500;opacity:.5;"></div>
          <div style="font-size:.72rem;opacity:.3;margin-top:6px;">[Receipt image renders here in production]</div>
        </div>
      </div>
      <div class="lb-foot">
        <span id="rcpt-meta"></span>
        <button onclick="exlShowToast('ok','Downloaded','Receipt saved.')" style="display:inline-flex;align-items:center;gap:5px;padding:5px 12px;background:rgba(154,128,83,.1);color:#9a8053;border:1px solid rgba(154,128,83,.25);border-radius:6px;font-size:.75rem;font-weight:500;cursor:pointer;">
          <i class="bi bi-download"></i>Download
        </button>
      </div>
    </div>
  </div>

  <div class="exl-toast-wrap" id="exlToastWrap"></div>
</div>
@endsection

@push('scripts')
<script>
/* =========================================================
   Expense Ledger — page scripts
   NOTE: LEDGER is empty. Connect to DB later, e.g.:
   var LEDGER = @json($ledger ?? []);
   Each row shape expected:
   { id, sr, tech, cat, amt, receipt(bool), date }
   ========================================================= */
var LEDGER = @json($ledger ?? []);

function exlRenderLedger(list){
  var tbody = document.getElementById('led-tbody');
  document.getElementById('led-count').textContent = list.length + ' entries';
  if(!list.length){
    tbody.innerHTML = '<tr><td colspan="7" style="text-align:center;padding:36px;color:var(--text-muted);">No entries found</td></tr>';
    return;
  }
  tbody.innerHTML = list.map(function(e,i){
    var rcpt = e.receipt
      ? '<div class="receipt-thumb" data-id="'+e.id+'" data-sr="'+e.sr+'" data-tech="'+e.tech+'" onclick="exlOpenRcpt(this.dataset.id,this.dataset.sr,this.dataset.tech)" title="View Receipt"><i class="bi bi-image"></i></div>'
      : '<div class="receipt-thumb no-rcpt" title="No receipt"><i class="bi bi-image-slash"></i></div>';
    return '<tr>'+
      '<td class="muted">'+(i+1)+'</td>'+
      '<td class="mono">'+e.sr+'</td>'+
      '<td style="font-size:.8rem;font-weight:500;">'+e.tech+'</td>'+
      '<td class="muted">'+e.cat+'</td>'+
      '<td style="text-align:right;font-weight:600;color:var(--text-heading);">AED '+Number(e.amt).toLocaleString()+'</td>'+
      '<td>'+rcpt+'</td>'+
      '<td class="muted">'+e.date+'</td>'+
    '</tr>';
  }).join('');
}

function exlFilterLedger(q){
  var cat = document.getElementById('led-cat').value;
  var list = LEDGER.filter(function(e){
    var mq = !q || (e.sr||'').toLowerCase().includes(q.toLowerCase()) || (e.tech||'').toLowerCase().includes(q.toLowerCase());
    var mc = !cat || e.cat === cat;
    return mq && mc;
  });
  exlRenderLedger(list);
}

function exlResetLedgerFilters(){
  document.getElementById('led-search').value = '';
  document.getElementById('led-cat').value = '';
  exlRenderLedger(LEDGER);
}

function exlSaveLedger(){
  exlShowToast('ok','Ledger Saved','Ledger changes saved across all edited lines.');
}

function exlOpenRcpt(id, sr, tech){
  document.getElementById('rcpt-title').textContent = 'Receipt — ' + sr;
  document.getElementById('rcpt-label').textContent = 'Receipt submitted by ' + tech;
  document.getElementById('rcpt-meta').textContent  = sr + ' · ' + tech;
  document.getElementById('rcpt-modal').classList.add('show');
}

function exlShowToast(type, title, body){
  var w = document.getElementById('exlToastWrap');
  var icons = {ok:'bi-check-circle-fill', err:'bi-x-circle-fill', info:'bi-info-circle-fill', warn:'bi-exclamation-triangle-fill'};
  var t = document.createElement('div');
  t.className = 'toast-item';
  t.innerHTML = '<i class="bi '+(icons[type]||icons.info)+' t-ico '+type+'"></i><div><p class="t-title">'+title+'</p><p class="t-body">'+body+'</p></div>';
  w.appendChild(t);
  setTimeout(function(){
    t.style.transition = 'opacity .3s';
    t.style.opacity = '0';
    setTimeout(function(){ t.remove(); }, 300);
  }, 3800);
}

document.addEventListener('DOMContentLoaded', function(){
  exlRenderLedger(LEDGER);
});
</script>
@endpush