@extends('layouts.layout')

@section('title', 'Inquiry Approval — Digit-Us Portal')
@section('page_title', 'Inquiry Approval')
@section('page_icon', 'clipboard-check')

@push('styles')
<style>
/* ── Page header (matches sr_registration gold theme) ── */
.ia-header { background: linear-gradient(135deg,#9A7B4F 0%,#C4A882 100%); border-radius: 12px; padding: 24px 26px; margin-bottom: 22px; color: #fff; position: relative; overflow: hidden; box-shadow: 0 6px 28px rgba(154,123,79,.28); }
.ia-header::before,.ia-header::after { content: ''; position: absolute; border-radius: 50%; pointer-events: none; }
.ia-header::before { width:200px;height:200px;background:rgba(255,255,255,.06); left:-50px;bottom:-50px; }
.ia-header::after  { width:160px;height:160px;background:rgba(255,255,255,.08); right:-30px;top:-30px; }
.ia-header-inner { position:relative; z-index:1; }
.ia-header h5 { font-size:1.0625rem;font-weight:700;margin:0 0 5px;letter-spacing:-.01em; }
.ia-header p  { font-size:.8rem;margin:0;opacity:.88;line-height:1.6; }
.ia-htags { display:flex;gap:6px;margin-top:12px;flex-wrap:wrap; }
.ia-htag { background:rgba(255,255,255,.17);border:1px solid rgba(255,255,255,.28);border-radius:20px;font-size:.6875rem;padding:3px 10px;font-weight:500; }
@media(max-width:575.98px){ .ia-header{padding:18px 16px;} .ia-header h5{font-size:.9375rem;} }

/* ── Stat cards ── */
.stat-card{background:var(--card-bg);border:1px solid var(--card-border);border-radius:10px;padding:14px 16px;display:flex;align-items:center;gap:12px;box-shadow:0 1px 12px rgba(100,120,160,.08);}
.stat-ico{width:46px;height:46px;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;}
.stat-num{font-size:1.625rem;font-weight:700;line-height:1;}
.stat-lbl{font-size:.72rem;color:var(--text-muted);margin-top:2px;}

/* ── Card shell ── */
.ia-card{background:var(--card-bg);border:1px solid var(--card-border);border-radius:12px;box-shadow:0 1px 12px rgba(100,120,160,.08);overflow:hidden;}

/* ── Filter bar ── */
.filter-bar{padding:12px 16px;border-bottom:1px solid var(--card-border);display:flex;flex-wrap:wrap;gap:8px;align-items:center;}
.search-wrap{position:relative;flex:1;min-width:180px;}
.search-wrap i{position:absolute;left:9px;top:50%;transform:translateY(-50%);color:var(--text-muted);font-size:.82rem;}
.search-wrap input{padding-left:30px;width:100%;}
.f-input,.f-select{
  font-size:.78rem;border:1.5px solid var(--border-color);border-radius:7px;
  padding:.4rem .75rem;color:var(--text-primary);background:var(--input-bg);height:36px;
  transition:border-color .15s,box-shadow .15s;
}
.f-input:focus,.f-select:focus{border-color:#9A7B4F;box-shadow:0 0 0 3px rgba(154,123,79,.13);outline:none;}
.f-input::placeholder{color:var(--text-light);}
[data-theme="dark"] .f-select option{background:#101e33;color:#c8d4e8;}
.rec-badge{font-size:.7rem;background:var(--surface-2);color:var(--text-muted);border:1px solid var(--border-color);border-radius:20px;padding:3px 10px;white-space:nowrap;}

/* ── Table ── */
.ia-table{width:100%;border-collapse:collapse;min-width:620px;}
.ia-table thead tr{background:var(--table-header, var(--surface-2));}
.ia-table thead th{font-size:.66rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--text-muted);padding:9px 10px;border-bottom:1px solid var(--card-border);white-space:nowrap;}
.ia-table tbody tr{border-bottom:1px solid var(--card-border);cursor:pointer;transition:background .15s;}
.ia-table tbody tr:last-child{border-bottom:none;}
.ia-table tbody tr:hover{background:rgba(154,123,79,.05);}
.ia-table tbody tr.selected{background:rgba(154,123,79,.08);border-left:3px solid #9A7B4F;}
[data-theme="dark"] .ia-table tbody tr:hover{background:rgba(154,123,79,.1);}
[data-theme="dark"] .ia-table tbody tr.selected{background:rgba(154,123,79,.12);}
.ia-table tbody td{padding:10px 10px;font-size:.78rem;vertical-align:middle;color:var(--text-heading);}
.sr-id{color:#9A7B4F;font-weight:700;}
.client-name{font-weight:600;color:var(--text-heading);font-size:.78rem;}
.client-ctr{font-size:.67rem;color:var(--text-muted);}

/* chips */
.chip{font-size:.62rem;font-weight:700;padding:2px 9px;border-radius:10px;white-space:nowrap;}
.chip-high  {background:rgba(255,51,102,.1); color:#ff3366;}
.chip-med   {background:rgba(251,188,6,.12);color:#b88b00;}
.chip-low   {background:rgba(5,163,74,.1); color:#05a34a;}
.chip-cov   {background:rgba(5,163,74,.1); color:#05a34a;}
.chip-unk   {background:rgba(176,186,201,.12);color:var(--text-muted);}

/* table footer */
.tbl-foot{padding:10px 16px;border-top:1px solid var(--card-border);display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;}

/* ── Right panels ── */
.rp-card{background:var(--card-bg);border:1px solid var(--card-border);border-radius:12px;box-shadow:0 1px 12px rgba(100,120,160,.08);padding:16px;margin-bottom:14px;}
.rp-head{display:flex;align-items:center;gap:10px;margin-bottom:14px;}
.rp-ico{width:32px;height:32px;border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:.88rem;background:rgba(154,123,79,.1);color:#9A7B4F;}
.rp-title{font-size:.8375rem;font-weight:600;color:var(--text-heading);}
.rp-sub{font-size:.7rem;color:var(--text-muted);}
.rp-empty{text-align:center;padding:24px 12px;color:var(--text-muted);font-size:.8rem;}
.rp-empty i{font-size:1.8rem;display:block;margin-bottom:8px;opacity:.28;}
.data-cell{background:var(--surface-2);border-radius:7px;padding:9px 11px;margin-bottom:8px;}
.data-cell:last-child{margin-bottom:0;}
.dc-lbl{font-size:.65rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:var(--text-muted);margin-bottom:2px;}
.dc-val{font-size:.8rem;font-weight:600;color:var(--text-heading);}
.desc-box{background:var(--surface-2);border-radius:7px;padding:12px;font-size:.79rem;color:var(--text-heading);line-height:1.6;}

/* ── Triage action buttons ── */
.triage-btn{
  width:100%;border:none;border-radius:10px;padding:13px 16px;
  text-align:left;cursor:pointer;display:flex;align-items:center;gap:10px;
  margin-bottom:8px;transition:opacity .15s,box-shadow .15s,transform .1s;
  color:#fff;font-size:.8125rem;
}
.triage-btn:last-of-type{margin-bottom:0;}
.triage-btn:disabled{opacity:.45;cursor:not-allowed;}
.triage-btn:not(:disabled):hover{box-shadow:0 4px 14px rgba(0,0,0,.2);}
.triage-btn:not(:disabled):active{transform:scale(.98);}
.tb-approve{background:linear-gradient(135deg,#9A7B4F,#C4A882);}
.tb-forward{background:linear-gradient(135deg,#d9a400,#f0c050);}
.tb-reject {background:linear-gradient(135deg,#ff6f6f,#ff3366);}
.tb-icon{font-size:1.05rem;flex-shrink:0;}
.tb-label{font-weight:700;font-size:.8125rem;line-height:1.2;}
.tb-sub  {font-size:.7rem;opacity:.88;margin-top:1px;}
.triage-hint{text-align:center;font-size:.75rem;color:var(--text-muted);margin-top:10px;margin-bottom:0;}

/* ── Toast ── */
.toast-shelf{position:fixed;top:68px;right:15px;z-index:9999;display:flex;flex-direction:column;gap:7px;max-width:300px;}
.toast-el{border-radius:9px;padding:12px 14px;display:flex;align-items:flex-start;gap:10px;color:#fff;box-shadow:0 6px 24px rgba(0,0,0,.2);animation:tIn .28s ease;}
@keyframes tIn{from{transform:translateX(36px);opacity:0;}to{transform:none;opacity:1;}}
.te-ico{font-size:1rem;flex-shrink:0;margin-top:1px;}
.te-msg{font-size:.8rem;font-weight:600;}
@media(max-width:575.98px){.toast-shelf{left:12px;right:12px;max-width:none;}}
</style>
@endpush

@section('content')

<div class="toast-shelf" id="toastShelf"></div>

{{-- Hero Banner --}}
<div class="ia-header">
  <div class="ia-header-inner">
    <h5><i class="bi bi-clipboard-check me-2"></i>Inquiry Approval — Triage Panel</h5>
    <p>Review incoming Pending service requests. Approve, forward to Accounts, or reject with a documented reason.</p>
    <div class="ia-htags">
      <span class="ia-htag"><i class="bi bi-briefcase me-1"></i>Head of Projects</span>
      <span class="ia-htag"><i class="bi bi-person me-1"></i>Admin</span>
      <span class="ia-htag"><i class="bi bi-shield-check me-1"></i>Super Admin</span>
    </div>
  </div>
</div>

{{-- Stats --}}
<div class="row g-3 mb-4">
  <div class="col-6 col-xl-3">
    <div class="stat-card">
      <div class="stat-ico" style="background:rgba(154,123,79,.1);"><i class="bi bi-clock" style="color:#9A7B4F;font-size:1.1rem;"></i></div>
      <div><div class="stat-num" style="color:#9A7B4F;" id="statPending">{{ $stats['pending'] }}</div><div class="stat-lbl">Pending Triage</div></div>
    </div>
  </div>
  <div class="col-6 col-xl-3">
    <div class="stat-card">
      <div class="stat-ico" style="background:rgba(5,163,74,.1);"><i class="bi bi-check-circle" style="color: #05a34a;font-size:1.1rem;"></i></div>
      <div><div class="stat-num" style="color: #05a34a;" id="statApproved">{{ $stats['approved'] }}</div><div class="stat-lbl">Approved Today</div></div>
    </div>
  </div>
  <div class="col-6 col-xl-3">
    <div class="stat-card">
      <div class="stat-ico" style="background:rgba(217,164,0,.12);"><i class="bi bi-send" style="color:#d9a400;font-size:1.1rem;"></i></div>
      <div><div class="stat-num" style="color:#d9a400;" id="statForwarded">{{ $stats['forwarded'] }}</div><div class="stat-lbl">Sent to Accounts</div></div>
    </div>
  </div>
  <div class="col-6 col-xl-3">
    <div class="stat-card">
      <div class="stat-ico" style="background:rgba(255,51,102,.1);"><i class="bi bi-x-circle" style="color:#ff3366;font-size:1.1rem;"></i></div>
      <div><div class="stat-num" style="color:#ff3366;" id="statRejected">{{ $stats['rejected'] }}</div><div class="stat-lbl">Rejected Today</div></div>
    </div>
  </div>
</div>

{{-- Workspace --}}
<div class="row g-3">

  {{-- LEFT: Table --}}
  <div class="col-12 col-xl-8">
    <div class="ia-card">

      <div class="filter-bar">
        <div class="search-wrap">
          <i class="bi bi-search"></i>
          <input type="text" class="f-input" id="searchInput" placeholder="Search SR ID, client, site, type…"/>
        </div>
        <select class="f-select" style="width:130px;" id="priorityFilter">
          <option value="">All Priorities</option>
          <option>Critical</option><option>High</option><option>Medium</option><option>Low</option>
        </select>
        <span class="rec-badge" id="recordCount">{{ $inquiries->count() }} of {{ $inquiries->count() }}</span>
      </div>

      <div class="px-4 pt-3 pb-2 d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div class="d-flex align-items-center gap-2">
          <i class="bi bi-list-ul" style="color:#9A7B4F;font-size:.95rem;"></i>
          <span style="font-weight:600;font-size:.875rem;color:var(--text-heading);">Pending Inquiries</span>
          <span style="background:#9A7B4F;color:#fff;font-size:.65rem;font-weight:700;padding:2px 8px;border-radius:10px;" id="pendingBadge">{{ $inquiries->count() }} records</span>
        </div>
        <span style="font-size:.72rem;color:var(--text-muted);">Click a row to review</span>
      </div>

      <div style="overflow-x:auto;-webkit-overflow-scrolling:touch;">
        <table class="ia-table">
          <thead>
            <tr>
              <th style="width:30px;"></th>
              <th>SR_ID</th>
              <th>Client</th>
              <th>Type</th>
              <th>Site</th>
              <th>Priority</th>
              <th>Reported By</th>
              <th>Submitted</th>
            </tr>
          </thead>
          <tbody id="tableBody">
            @forelse($inquiries as $sr)
            @php
              $srRef = 'SR-' . $sr->created_at->year . '-' . str_pad($sr->id, 5, '0', STR_PAD_LEFT);
              $typeLabel = $sr->service_type_id == 1 ? 'Installation & Maintenance' : 'Repair & Inspection';
            @endphp
            <tr class="inquiry-row"
                data-id="{{ $sr->id }}"
                data-priority="{{ $sr->priority_level }}">
              <td>
                <input type="radio" name="selectedRow" class="row-radio" value="{{ $sr->id }}"
                       style="accent-color:#9A7B4F;cursor:pointer;width:14px;height:14px;"/>
              </td>
              <td><span class="sr-id">{{ $srRef }}</span></td>
              <td>
                <div class="client-name">{{ Str::limit($sr->client->company_name ?? '—', 22) }}</div>
                <div class="client-ctr">{{ $sr->client->unique_code ?? '' }}</div>
              </td>
              <td style="color:var(--text-muted);">{{ $typeLabel }}</td>
              <td style="color:var(--text-muted);font-size:.74rem;max-width:110px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $sr->project_site }}</td>
              <td>
                @if($sr->priority_level==='Critical')
                  <span class="chip chip-high">Critical</span>
                @elseif($sr->priority_level==='High')
                  <span class="chip chip-high">High</span>
                @elseif($sr->priority_level==='Medium')
                  <span class="chip chip-med">Medium</span>
                @else
                  <span class="chip chip-low">Low</span>
                @endif
              </td>
              <td style="color:var(--text-muted);font-size:.76rem;">{{ $sr->reported_by }}</td>
              <td style="font-size:.72rem;color:var(--text-muted);white-space:nowrap;">{{ $sr->created_at->format('d M, H:i') }}</td>
            </tr>
            @empty
            {{-- handled by emptyState below --}}
            @endforelse
          </tbody>
        </table>

        <div id="emptyState" style="{{ $inquiries->count() ? 'display:none;' : '' }}text-align:center;padding:40px 20px;color:var(--text-muted);font-size:.8rem;">
          <i class="bi bi-inbox" style="font-size:2rem;display:block;margin-bottom:6px;opacity:.3;"></i>
          No pending requests right now.
        </div>
      </div>

      <div class="tbl-foot">
        <span style="font-size:.75rem;color:var(--text-muted);" id="tblLbl">Showing {{ $inquiries->count() }} of {{ $inquiries->count() }}</span>
      </div>

    </div>
  </div>

  {{-- RIGHT: Side panels --}}
  <div class="col-12 col-xl-4">

    <div class="rp-card">
      <div class="rp-head">
        <div class="rp-ico"><i class="bi bi-file-earmark-magnify"></i></div>
        <div><div class="rp-title">Request Details</div><div class="rp-sub">Type &amp; priority vs inquiry</div></div>
      </div>
      <div id="contractEmpty" class="rp-empty"><i class="bi bi-mouse2"></i>Select a row to load details</div>
      <div id="contractData" style="display:none;">
        <div class="row g-2">
          <div class="col-6"><div class="data-cell"><div class="dc-lbl">SR Reference</div><div class="dc-val" id="dcContract">—</div></div></div>
          <div class="col-6"><div class="data-cell"><div class="dc-lbl">Priority</div><div class="dc-val" id="dcWarranty">—</div></div></div>
          <div class="col-6"><div class="data-cell"><div class="dc-lbl">Client</div><div class="dc-val" id="dcClient">—</div></div></div>
          <div class="col-6"><div class="data-cell"><div class="dc-lbl">Type</div><div class="dc-val" id="dcCategory">—</div></div></div>
        </div>
      </div>
    </div>

    <div class="rp-card">
      <div class="rp-head">
        <div class="rp-ico"><i class="bi bi-chat-text"></i></div>
        <div><div class="rp-title">Inquiry Description</div><div class="rp-sub">Client-submitted details</div></div>
      </div>
      <div id="descEmpty" class="rp-empty"><i class="bi bi-chat-text"></i>No inquiry selected</div>
      <div id="descData" style="display:none;"><div class="desc-box" id="descText">—</div></div>
    </div>

    <div class="rp-card" style="margin-bottom:0;">
      <div class="rp-head">
        <i class="bi bi-lightning-charge-fill" style="color:#9A7B4F;font-size:1rem;"></i>
        <div class="rp-title" style="text-transform:uppercase;letter-spacing:.05em;">Triage Actions</div>
      </div>

      <button class="triage-btn tb-approve" id="btnApprove" disabled>
        <i class="bi bi-check-circle-fill tb-icon"></i>
        <div><div class="tb-label">Approve</div><div class="tb-sub">→ Dispatch Engine</div></div>
      </button>

      <button class="triage-btn tb-forward" id="btnForward" disabled>
        <i class="bi bi-file-earmark-text-fill tb-icon"></i>
        <div><div class="tb-label">Forward to Accounts</div><div class="tb-sub">→ Quotation Desk</div></div>
      </button>

      <button class="triage-btn tb-reject" id="btnReject" disabled style="margin-bottom:0;">
        <i class="bi bi-x-circle-fill tb-icon"></i>
        <div><div class="tb-label">Reject Ticket</div><div class="tb-sub">→ Notify + Archive</div></div>
      </button>

      <p class="triage-hint" id="triageHint">← Select a row from the table to enable actions</p>
    </div>

  </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

  const CSRF_TOKEN = "{{ csrf_token() }}";

  // Build a lookup keyed by SR id from server-rendered rows
  const DETAILS = {};
  document.querySelectorAll('.inquiry-row').forEach(row => {
    DETAILS[row.dataset.id] = {
      contract: row.querySelector('.sr-id').textContent.trim(),
      priority: row.dataset.priority,
      client:   row.querySelector('.client-name').textContent.trim(),
      category: row.children[3].textContent.trim(),
      desc:     row.dataset.desc || '',
    };
  });

  let selectedId = null;
  let approvedCount = {{ $stats['approved'] }}, forwardedCount = {{ $stats['forwarded'] }}, rejectedCount = {{ $stats['rejected'] }};

  document.querySelectorAll('.inquiry-row').forEach(row => {
    row.addEventListener('click', function () {
      document.querySelectorAll('.inquiry-row').forEach(r => r.classList.remove('selected'));
      this.classList.add('selected');
      this.querySelector('.row-radio').checked = true;
      selectedId = this.dataset.id;
      loadPanel(selectedId);
      enableActions(true);
    });
  });

  function loadPanel(id) {
    const d = DETAILS[id];
    if (!d) return;
    document.getElementById('contractEmpty').style.display = 'none';
    document.getElementById('contractData').style.display  = 'block';
    document.getElementById('dcContract').textContent  = d.contract;
    document.getElementById('dcWarranty').textContent  = d.priority;
    document.getElementById('dcClient').textContent    = d.client;
    document.getElementById('dcCategory').textContent  = d.category;
    document.getElementById('descEmpty').style.display = 'none';
    document.getElementById('descData').style.display  = 'block';
    document.getElementById('descText').textContent    = d.desc || '—';
  }

  function resetPanel() {
    document.getElementById('contractEmpty').style.display = 'block';
    document.getElementById('contractData').style.display  = 'none';
    document.getElementById('descEmpty').style.display     = 'block';
    document.getElementById('descData').style.display      = 'none';
    enableActions(false);
    selectedId = null;
  }

  function enableActions(on) {
    ['btnApprove','btnForward','btnReject'].forEach(id => document.getElementById(id).disabled = !on);
    document.getElementById('triageHint').style.display = on ? 'none' : 'block';
  }

  function removeRow(id) {
    const row = document.querySelector(`.inquiry-row[data-id="${id}"]`);
    if (row) {
      row.style.transition = 'opacity .3s';
      row.style.opacity = '0';
      setTimeout(() => { row.remove(); updateCounts(); }, 300);
    }
    resetPanel();
  }

  function updateCounts() {
    const remaining = document.querySelectorAll('.inquiry-row').length;
    document.getElementById('statPending').textContent  = remaining;
    document.getElementById('statApproved').textContent = approvedCount;
    document.getElementById('statForwarded').textContent= forwardedCount;
    document.getElementById('statRejected').textContent = rejectedCount;
    document.getElementById('pendingBadge').textContent = remaining + ' records';
    document.getElementById('recordCount').textContent  = remaining + ' of ' + remaining;
    document.getElementById('tblLbl').textContent       = 'Showing ' + remaining + ' of ' + remaining;
    if (remaining === 0) document.getElementById('emptyState').style.display = 'block';
  }

  async function triage(action, color) {
    if (!selectedId) return;
    try {
      const res = await fetch(`/service-requests/${selectedId}/${action}`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json', 'Content-Type': 'application/json' },
        body: JSON.stringify({}),
      });
      const data = await res.json();
      if (!data.success) throw new Error(data.message || 'Action failed');

      if (action === 'approve') approvedCount++;
      if (action === 'forward') forwardedCount++;
      if (action === 'reject')  rejectedCount++;

      showToast(data.message, color);
      removeRow(selectedId);
    } catch (e) {
      showToast(e.message || 'Action failed', '#ff3366');
    }
  }

  document.getElementById('btnApprove').addEventListener('click', () => triage('approve', '#9A7B4F'));
  document.getElementById('btnForward').addEventListener('click', () => triage('forward', '#d9a400'));
  document.getElementById('btnReject').addEventListener('click', () => triage('reject', '#ff3366'));

  ['searchInput','priorityFilter'].forEach(id => {
    document.getElementById(id).addEventListener(id === 'searchInput' ? 'input' : 'change', filterTable);
  });

  function filterTable() {
    const q  = document.getElementById('searchInput').value.toLowerCase();
    const pr = document.getElementById('priorityFilter').value;
    let vis  = 0;
    document.querySelectorAll('.inquiry-row').forEach(row => {
      const match = (!q  || row.textContent.toLowerCase().includes(q))
                 && (!pr || row.dataset.priority === pr);
      row.style.display = match ? '' : 'none';
      if (match) vis++;
    });
    const total = document.querySelectorAll('.inquiry-row').length;
    document.getElementById('recordCount').textContent = vis + ' of ' + total;
    document.getElementById('emptyState').style.display = vis === 0 ? 'block' : 'none';
  }

  function showToast(msg, bg) {
    const shelf = document.getElementById('toastShelf');
    const el    = document.createElement('div');
    el.className  = 'toast-el';
    el.style.background = bg;
    el.innerHTML  = `<i class="bi bi-check-circle-fill te-ico"></i><span class="te-msg">${msg}</span>`;
    shelf.appendChild(el);
    setTimeout(() => { el.style.opacity='0'; el.style.transition='opacity .3s'; setTimeout(()=>el.remove(),300); }, 3800);
  }

});
</script>
@endpush