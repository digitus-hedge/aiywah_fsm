@extends('layouts.layout')

@section('title', 'Completed SRs — Digit-Us Portal')
@section('page_title', 'Completed SRs')
@section('page_icon', 'check2-circle')


@push('styles')
<style>
/* PAGE HEADER */
.pg-header{border-radius:10px;padding:18px 24px;margin-bottom:20px;color:#fff;position:relative;overflow:hidden;}
.pg-header::before{content:'';position:absolute;left:-40px;bottom:-40px;width:180px;height:180px;border-radius:50%;background:rgba(255,255,255,.05);}
.pg-header::after{content:'';position:absolute;right:-30px;top:-30px;width:160px;height:160px;border-radius:50%;background:rgba(255,255,255,.07);}
.pg-header h4{font-size:1rem;font-weight:600;margin:0 0 3px;position:relative;z-index:1;}
.pg-header p{font-size:.78rem;margin:0;opacity:.85;position:relative;z-index:1;}
.pg-header .meta-row{display:flex;align-items:center;gap:8px;margin-top:9px;position:relative;z-index:1;flex-wrap:wrap;}
.meta-badge{background:rgba(255,255,255,.18);border:1px solid rgba(255,255,255,.3);border-radius:20px;font-size:.6875rem;padding:2px 10px;font-weight:500;}
/* STATS STRIP */
.stats-strip{display:grid;grid-template-columns:repeat(4,1fr);gap:10px;margin-bottom:18px;}
.stat-card{background:var(--card-bg);border:1px solid var(--card-border);border-radius:9px;padding:13px 15px;display:flex;align-items:center;gap:12px;box-shadow:var(--card-shadow);}
.stat-icon{width:38px;height:38px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:1rem;flex-shrink:0;}
.stat-num{font-size:1.5rem;font-weight:700;line-height:1;color:var(--text-heading);}
.stat-lbl{font-size:.7rem;color:var(--text-muted);}
@media(max-width:767px){.stats-strip{grid-template-columns:repeat(2,1fr);}}
/* FILTER BAR */
.filter-bar{background:var(--card-bg);border:1px solid var(--card-border);border-radius:10px;padding:14px 16px;margin-bottom:16px;box-shadow:var(--card-shadow);display:flex;flex-wrap:wrap;gap:10px;align-items:flex-end;}
.filter-group{display:flex;flex-direction:column;gap:4px;min-width:0;flex: 1 1 auto;}
.filter-label{font-size:.7rem;font-weight:600;color:var(--text-muted);text-transform:uppercase;letter-spacing:.05em;}
.filter-control{height:34px;padding:0 10px;border:1px solid var(--border-color);border-radius:7px;background:var(--input-bg);color:var(--text-primary);font-size:.8rem;min-width:130px;transition:border-color .15s,box-shadow .15s;}
.filter-control:focus{outline:none;border-color:#9A7B4F;box-shadow:var(--input-focus-shadow);}
.filter-search{min-width:220px;}
.filter-actions{margin-left:auto;display:flex;gap:8px;align-items:flex-end;}
/* TABLE CARD */
.tbl-card{background:var(--card-bg);border:1px solid var(--card-border);border-radius:10px;box-shadow:var(--card-shadow);overflow:hidden;}
.tbl-card-hdr{display:flex;align-items:center;justify-content:space-between;padding:13px 18px;border-bottom:1px solid var(--border-color);flex-wrap:wrap;gap:10px;}
.tbl-card-hdr-left{display:flex;align-items:center;gap:10px;}
.tbl-card-title{font-size:.875rem;font-weight:600;color:var(--text-heading); font-family: var(--font-header);}
.result-count{font-size:.75rem;color:var(--text-muted);background:var(--surface-2);padding:2px 9px;border-radius:9px;}
.tbl-wrap{overflow-x:auto;}
table.listing{width:100%;border-collapse:collapse;}
table.listing thead th{padding:10px 16px;font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--text-muted);background:var(--table-header);border-bottom:1px solid var(--border-color);white-space:nowrap;}
table.listing tbody tr{border-bottom:1px solid var(--border-color);transition:background .1s;cursor:pointer;}
table.listing tbody tr:last-child{border-bottom:none;}
table.listing tbody tr:hover{background:var(--table-hover);}
table.listing td{padding:11px 16px;font-size:.8125rem;color:var(--text-primary);vertical-align:middle;}
table.listing td.muted{color:var(--text-muted);font-size:.78rem;}
table.listing td.mono{font-size:.78rem;font-weight:600;color:#9A7B4F;}
/* BUTTONS */
.btn-gold{display:inline-flex;align-items:center;gap:6px;padding:7px 14px;background:linear-gradient(135deg,#9A7B4F,#C4A882);color:#fff;border:none;border-radius:7px;font-size:.8rem;font-weight:500;cursor:pointer;white-space:nowrap;}
.btn-gold:hover{opacity:.87;}
.btn-ghost{display:inline-flex;align-items:center;gap:6px;padding:7px 12px;background:var(--surface-2);color:var(--text-muted);border:1px solid var(--border-color);border-radius:7px;font-size:.8rem;cursor:pointer;white-space:nowrap;}
.btn-ghost:hover{background:var(--surface-3);}
.btn-xs{padding:4px 9px;border-radius:5px;font-size:.75rem;border:none;cursor:pointer;display:inline-flex;align-items:center;gap:4px;transition:background .12s;}
.btn-xs-view{background:rgba(37,99,235,.1);color:#3b82f6;}
.btn-xs-view:hover{background:rgba(37,99,235,.2);}
/* BADGES */
.sbadge{display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:20px;font-size:.7rem;font-weight:600;white-space:nowrap;}
.sb-completed{background:rgba(16,185,129,.15);color:#059669;}
.sb-warranty{background:rgba(16,185,129,.1);color:#059669;}
.sb-oow{background:rgba(239,68,68,.1);color:#ef4444;}
/* MONEY / DURATION CELLS */
.cell-total{font-weight:700;color:var(--text-heading);}
.cell-dur{display:inline-flex;align-items:center;gap:5px;font-size:.78rem;color:var(--text-muted);}
.worker-cell{display:flex;align-items:center;gap:9px;}
.w-av{width:28px;height:28px;border-radius:50%;background:linear-gradient(135deg,#9A7B4F,#C4A882);display:flex;align-items:center;justify-content:center;font-size:.68rem;font-weight:700;color:#fff;flex-shrink:0;}
/* PAGINATION */
.pagination-bar{display:flex;align-items:center;justify-content:space-between;padding:12px 18px;border-top:1px solid var(--border-color);flex-wrap:wrap;gap:8px;}
.page-info{font-size:.78rem;color:var(--text-muted);}
.page-btns{display:flex;gap:4px;}
.page-btn{width:30px;height:30px;border-radius:6px;border:1px solid var(--border-color);background:var(--card-bg);color:var(--text-muted);cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:.78rem;transition:all .12s;}
.page-btn:hover{border-color:#9A7B4F;color:#9A7B4F;}
.page-btn.active{background:#9A7B4F;color:#fff;border-color:#9A7B4F;}
/* TOAST */
#toastWrap{position:fixed;bottom:22px;right:22px;z-index:9999;display:flex;flex-direction:column;gap:8px;pointer-events:none;}
.toast-item{display:flex;align-items:flex-start;gap:10px;padding:12px 15px;border-radius:9px;background:var(--card-bg);border:1px solid var(--card-border);box-shadow:0 6px 24px rgba(0,0,0,.14);min-width:240px;max-width:320px;animation:toastIn .2s ease;pointer-events:auto;}
@keyframes toastIn{from{opacity:0;transform:translateY(10px);}to{opacity:1;transform:translateY(0);}}
.t-ico{font-size:1rem;flex-shrink:0;margin-top:1px;}
.t-ico.ok{color:#10b981;}.t-ico.err{color:#ef4444;}.t-ico.info{color:#9A7B4F;}
.t-title{font-size:.8rem;font-weight:600;color:var(--text-heading);margin:0 0 2px;}
.t-body{font-size:.75rem;color:var(--text-muted);margin:0;}
/* EMPTY STATE */
.empty-row td{text-align:center;padding:40px 20px;color:var(--text-muted);font-size:.82rem;}
.empty-row td i{display:block;font-size:2rem;color:var(--text-light);margin-bottom:8px;}

/* ══════════════════════════════════════════
   SR DETAIL MODAL (completion view)
══════════════════════════════════════════ */
.sr-modal-overlay{display:none;position:fixed;inset:0;background:var(--overlay-bg,rgba(9,15,35,.6));z-index:1000;align-items:center;justify-content:center;backdrop-filter:blur(4px);padding:20px;}
.sr-modal-overlay.show{display:flex;}
.sr-modal-box{background:var(--modal-bg,var(--card-bg));border-radius:12px;width:100%;max-width:680px;max-height:90vh;display:flex;flex-direction:column;box-shadow:var(--modal-shadow,0 24px 64px rgba(0,0,0,.16));border:1px solid var(--card-border);overflow:hidden;animation:srModalIn .2s ease;}
@keyframes srModalIn{from{opacity:0;transform:scale(.96) translateY(6px);}to{opacity:1;transform:scale(1) translateY(0);}}
.sr-modal-hdr{border-bottom:1px solid var(--border-color);flex-shrink:0;}
.sr-modal-hdr-banner{background:var(--app-bg);padding:16px 22px;position:relative;overflow:hidden;}
.sr-modal-hdr-banner::after{content:'';position:absolute;right:-30px;top:-30px;width:130px;height:130px;border-radius:50%;background:rgba(255,255,255,.08);}
.sr-modal-close{position:absolute;top:14px;right:16px;z-index:2;background:rgba(255,255,255,.18);border:none;color:#fff;width:30px;height:30px;border-radius:7px;cursor:pointer;font-size:1rem;line-height:1;display:flex;align-items:center;justify-content:center;transition:background .15s;}
.sr-modal-close:hover{background:rgba(255,255,255,.32);}
.sr-modal-id{font-size:1.05rem;font-weight:700;margin:0 0 4px;position:relative;z-index:1;}
.sr-modal-client{font-size:.82rem;opacity:.9;position:relative;z-index:1;display:flex;align-items:center;gap:6px;}
.sr-modal-body{padding:20px 22px;overflow-y:auto;}
.sr-detail-grid{display:grid;grid-template-columns:1fr 1fr;gap:14px 20px;}
@media(max-width:560px){.sr-detail-grid{grid-template-columns:1fr;}}
.sr-detail-item{display:flex;flex-direction:column;gap:4px;min-width:0;}
.sr-detail-item.full{grid-column:1 / -1;}
.sr-detail-label{font-size:.66rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--text-muted);display:flex;align-items:center;gap:5px;}
.sr-detail-label i{font-size:.8rem;color:#9A7B4F;}
.sr-detail-value{font-size:.85rem;color:var(--text-heading);font-weight:500;word-break:break-word;}
.sr-detail-value.muted{color:var(--text-muted);font-weight:400;}
.sr-detail-divider{grid-column:1 / -1;height:1px;background:var(--border-color);margin:2px 0;}
.sr-modal-foot{padding:14px 22px;border-top:1px solid var(--border-color);display:flex;gap:10px;justify-content:flex-end;flex-shrink:0;flex-wrap:wrap;}
@media(max-width:480px){.sr-modal-foot{justify-content:stretch;}.sr-modal-foot > *{flex:1;justify-content:center;}}
/* PROOF THUMBS IN MODAL */
.proof-strip{grid-column:1 / -1;display:grid;grid-template-columns:repeat(3,1fr);gap:10px;}
@media(max-width:560px){.proof-strip{grid-template-columns:1fr;}}
.proof-tile{border:1px solid var(--border-color);border-radius:9px;overflow:hidden;background:var(--surface-2);cursor:pointer;transition:border-color .15s,transform .12s;}
.proof-tile:hover{border-color:#9A7B4F;transform:translateY(-1px);}
.proof-thumb{height:96px;background-size:cover;background-position:center;background-repeat:no-repeat;display:flex;align-items:center;justify-content:center;color:var(--text-light);}
.proof-thumb i{font-size:1.8rem;opacity:.5;}
.proof-cap{font-size:.68rem;font-weight:600;color:var(--text-muted);padding:6px 8px;display:flex;align-items:center;justify-content:space-between;gap:5px;}
.proof-cap .ok{color:#059669;}.proof-cap .missing{color:#ef4444;}
/* LIGHTBOX */
.lb-overlay{display:none;position:fixed;inset:0;background:rgba(9,15,35,.82);z-index:1100;align-items:center;justify-content:center;padding:24px;flex-direction:column;gap:12px;}
.lb-overlay.show{display:flex;}
.lb-stage{width:100%;max-width:820px;background:#525659;border-radius:10px;overflow:hidden;display:flex;align-items:center;justify-content:center;flex-direction:column;min-height:200px;}
.lb-stage img{max-width:100%;max-height:70vh;object-fit:contain;display:block;}
.lb-stage iframe{width:100%;height:72vh;border:none;}
.lb-bar{width:100%;max-width:820px;display:flex;align-items:center;justify-content:space-between;gap:10px;color:#fff;}
.lb-title{font-size:.85rem;font-weight:600;}
.lb-file{font-size:.72rem;opacity:.7;}
.lb-actions{display:flex;gap:8px;}
.lb-btn{display:inline-flex;align-items:center;gap:5px;padding:6px 13px;border-radius:7px;font-size:.78rem;font-weight:500;cursor:pointer;border:1px solid rgba(255,255,255,.25);background:rgba(255,255,255,.12);color:#fff;text-decoration:none;}
.lb-btn:hover{background:rgba(255,255,255,.22);}
.lb-btn.gold{background:linear-gradient(135deg,#9A7B4F,#C4A882);border-color:transparent;}
</style>
@endpush

@section('content')

<div class="pg-header" style="background:linear-gradient(135deg,#9A7B4F 0%,#7A6140 100%);">
  <h4><i class="bi bi-check2-circle me-2"></i>Completed Service Requests</h4>
  <p>Closed jobs with worker punch records, completion totals and signed client acceptance.</p>
  <div class="meta-row">
    <span class="meta-badge"><i class="bi bi-clock-history me-1"></i>Punch Log</span>
    <span class="meta-badge"><i class="bi bi-cash-coin me-1"></i>Completion Totals</span>
    <span class="meta-badge"><i class="bi bi-pen me-1"></i>Signed Acceptance</span>
  </div>
</div>

<div class="stats-strip">
  <div class="stat-card">
    <div class="stat-icon" style="background:rgba(16,185,129,.12);"><i class="bi bi-check2-all" style="color:#059669;"></i></div>
    <div><div class="stat-num">{{ $stats['total'] ?? 0 }}</div><div class="stat-lbl">Total Completed</div></div>
  </div>
  <div class="stat-card">
    <div class="stat-icon" style="background:rgba(37,99,235,.1);"><i class="bi bi-calendar-check" style="color:#2563eb;"></i></div>
    <div><div class="stat-num">{{ $stats['thisMonth'] ?? 0 }}</div><div class="stat-lbl">This Month</div></div>
  </div>
  <div class="stat-card">
    <div class="stat-icon" style="background:rgba(154,123,79,.12);"><i class="bi bi-cash-stack" style="color:#9A7B4F;"></i></div>
    <div><div class="stat-num">{{ number_format($stats['revenue'] ?? 0, 0) }}</div><div class="stat-lbl">Total Billed</div></div>
  </div>
  <div class="stat-card">
    <div class="stat-icon" style="background:rgba(245,158,11,.1);"><i class="bi bi-pen" style="color:#d97706;"></i></div>
    <div><div class="stat-num">{{ $stats['signed'] ?? 0 }}</div><div class="stat-lbl">Signed Off</div></div>
  </div>
</div>

<form id="filterForm" onsubmit="return false">
<div class="filter-bar">
  <div class="filter-group">
    <div class="filter-label">Search</div>
    <input class="filter-control filter-search" type="text" name="search"
           placeholder="SR ID, client, worker…" oninput="debounceFilter()"/>
  </div>
  <div class="filter-group">
    <div class="filter-label">Warranty</div>
    <select class="filter-control" name="warranty" onchange="applyFilters()">
      <option value="">All</option>
      <option value="warranty">In Warranty</option>
      <option value="oow">Out of Warranty</option>
    </select>
  </div>
  <div class="filter-group">
    <div class="filter-label">Completed From</div>
    <input class="filter-control" type="date" name="date_from" onchange="applyFilters()"/>
  </div>
  <div class="filter-group">
    <div class="filter-label">Completed To</div>
    <input class="filter-control" type="date" name="date_to" onchange="applyFilters()"/>
  </div>
  <div class="filter-actions">
    <button type="button" class="btn-ghost" onclick="resetFilters()"><i class="bi bi-x-circle"></i>Reset</button>
    <a class="btn-gold" id="exportBtn" href="#"><i class="bi bi-download"></i>Export CSV</a>
  </div>
</div>
</form>

<div class="tbl-card">
  <div class="tbl-card-hdr">
    <div class="tbl-card-hdr-left">
      <span class="tbl-card-title">Completed Jobs</span>
      <span class="result-count" id="result-count">
        Showing {{ $completed->count() }} of {{ $completed->total() }}
      </span>
    </div>
  </div>

  <div class="tbl-wrap">
    <table class="listing" id="sr-table">
      <thead>
        <tr>
          <th>SR ID</th>
          <th>Client</th>
          <th>Site / Location</th>
          <th>Worker</th>
          <th>Duration</th>
          <th>Total</th>
          <th>Completed</th>
          <th style="width:70px;">Action</th>
        </tr>
      </thead>
      <tbody id="sr-tbody">
        @fragment('rows')
        @forelse($completed as $sr)
          @php
            $punch = $sr->punch ?? $sr->punches->last() ?? null;

            $srCode = 'SR-'.\Carbon\Carbon::parse($sr->created_at)->format('Y').'-'.str_pad($sr->id,5,'0',STR_PAD_LEFT);

            $completedAt = $punch && $punch->punch_out_at
              ? \Carbon\Carbon::parse($punch->punch_out_at)
              : \Carbon\Carbon::parse($sr->updated_at);

            $grandTotal = $punch->grand_total ?? 0;
            $duration   = $punch->duration_label ?? '—';
            $worker     = optional($punch?->user)->name ?? 'Unassigned';

            $isOow = ($sr->is_oow ?? false) || ($sr->warranty_status ?? '') === 'Out of Warranty';

            // Proof URLs (before/after photos are images, signature is a PDF)
            $beforeUrl = $punch && $punch->start_photo_path  ? \Illuminate\Support\Facades\Storage::url($punch->start_photo_path)  : null;
            $afterUrl  = $punch && $punch->finish_photo_path ? \Illuminate\Support\Facades\Storage::url($punch->finish_photo_path) : null;
            $signUrl   = $punch && $punch->customer_signature_path ? \Illuminate\Support\Facades\Storage::url($punch->customer_signature_path) : null;

            $srPayload = [
              'code'        => $srCode,
              'client'      => optional($sr->client)->company_name ?? '—',
              'site'        => optional($sr->project)->site_name ?? '—',
              'worker'      => $worker,
              'issue'       => $sr->issue_description ?? '—',
              'warranty'    => $isOow ? 'Out of Warranty' : 'In Warranty',
              'contact'     => optional($sr->client)->primary_mobile ?? optional($sr->client)->contact_number ?? '—',
              'cust_name'   => $punch->customer_name ?? '—',
              'summary'     => $punch->completion_summary ?? '—',
              'punch_in'    => $punch && $punch->punch_in_at  ? \Carbon\Carbon::parse($punch->punch_in_at)->format('d M Y · h:i A')  : '—',
              'punch_out'   => $punch && $punch->punch_out_at ? \Carbon\Carbon::parse($punch->punch_out_at)->format('d M Y · h:i A') : '—',
              'duration'    => $duration,
              'materials'   => number_format($punch->materials_subtotal ?? 0, 2),
              'labour'      => number_format($punch->labour_charge ?? 0, 2),
              'total'       => number_format($grandTotal, 2),
              'completed'   => $completedAt->format('d M Y · h:i A'),
              'completed_h' => $completedAt->diffForHumans(),
              'before'      => $beforeUrl,
              'after'       => $afterUrl,
              'signature'   => $signUrl,
            ];
          @endphp
          <tr data-sr='@json($srPayload)' onclick="openSrModal(this)">
            <td class="mono">{{ $srCode }}</td>
            <td><strong style="font-size:.82rem">{{ optional($sr->client)->company_name ?? '—' }}</strong></td>
            <td class="muted">{{ optional($sr->project)->site_name ?? '—' }}</td>
            <td>
              <div class="worker-cell">
                <span class="w-av">{{ strtoupper(\Illuminate\Support\Str::substr($worker, 0, 2)) }}</span>
                <span style="font-size:.8rem;">{{ $worker }}</span>
              </div>
            </td>
            <td><span class="cell-dur"><i class="bi bi-stopwatch"></i>{{ $duration }}</span></td>
            <td class="cell-total">{{ number_format($grandTotal, 2) }}</td>
            <td class="muted">{{ $completedAt->diffForHumans() }}</td>
            <td onclick="event.stopPropagation()">
              <div style="display:flex;gap:5px;">
                <button class="btn-xs btn-xs-view" onclick="openSrModal(this.closest('tr'))">
                  <i class="bi bi-eye"></i> View
                </button>
              </div>
            </td>
          </tr>
        @empty
          <tr class="empty-row"><td colspan="8"><i class="bi bi-inbox"></i>No completed service requests found.</td></tr>
        @endforelse
        @endfragment
      </tbody>
    </table>
  </div>

  <div class="pagination-bar">
    @fragment('pager')
    <div class="page-info">
      Page {{ $completed->currentPage() }} of {{ $completed->lastPage() }} · {{ $completed->total() }} total records
    </div>
    <div class="page-btns">
      <button class="page-btn" {{ $completed->onFirstPage() ? 'disabled' : '' }}
        onclick="goToPage({{ $completed->currentPage() - 1 }})"><i class="bi bi-chevron-left"></i></button>
      @foreach($completed->getUrlRange(1, $completed->lastPage()) as $page => $url)
        <button class="page-btn {{ $page == $completed->currentPage() ? 'active' : '' }}"
          onclick="goToPage({{ $page }})">{{ $page }}</button>
      @endforeach
      <button class="page-btn" {{ $completed->hasMorePages() ? '' : 'disabled' }}
        onclick="goToPage({{ $completed->currentPage() + 1 }})"><i class="bi bi-chevron-right"></i></button>
    </div>
    @endfragment
  </div>
</div>

{{-- ══════════════ SR COMPLETION MODAL ══════════════ --}}
<div class="sr-modal-overlay" id="srModal" onclick="if(event.target===this)closeSrModal()">
  <div class="sr-modal-box" role="dialog" aria-modal="true" aria-labelledby="sr-m-id">
    <div class="sr-modal-hdr">
      <div class="sr-modal-hdr-banner">
        <button class="sr-modal-close" onclick="closeSrModal()" aria-label="Close"><i class="bi bi-x-lg"></i></button>
        <div class="sr-modal-id" id="sr-m-id">—</div>
        <div class="sr-modal-client"><i class="bi bi-building"></i><span id="sr-m-client">—</span></div>
      </div>
    </div>
    <div class="sr-modal-body">
      <div class="sr-detail-grid">
        <div class="sr-detail-item">
          <span class="sr-detail-label"><i class="bi bi-flag"></i>Status</span>
          <span class="sr-detail-value"><span class="sbadge sb-completed"><i class="bi bi-circle-fill" style="font-size:.4rem;"></i> Completed</span></span>
        </div>
        <div class="sr-detail-item">
          <span class="sr-detail-label"><i class="bi bi-shield-check"></i>Warranty Scope</span>
          <span class="sr-detail-value" id="sr-m-warranty">—</span>
        </div>

        <div class="sr-detail-divider"></div>

        <div class="sr-detail-item">
          <span class="sr-detail-label"><i class="bi bi-geo-alt"></i>Site / Location</span>
          <span class="sr-detail-value" id="sr-m-site">—</span>
        </div>
        <div class="sr-detail-item">
          <span class="sr-detail-label"><i class="bi bi-person-workspace"></i>Worker</span>
          <span class="sr-detail-value" id="sr-m-worker">—</span>
        </div>
        <div class="sr-detail-item">
          <span class="sr-detail-label"><i class="bi bi-telephone"></i>Client Contact</span>
          <span class="sr-detail-value" id="sr-m-contact">—</span>
        </div>
        <div class="sr-detail-item">
          <span class="sr-detail-label"><i class="bi bi-person-check"></i>Signed By</span>
          <span class="sr-detail-value" id="sr-m-custname">—</span>
        </div>

        <div class="sr-detail-item full">
          <span class="sr-detail-label"><i class="bi bi-card-text"></i>Reported Issue</span>
          <span class="sr-detail-value muted" id="sr-m-issue">—</span>
        </div>
        <div class="sr-detail-item full">
          <span class="sr-detail-label"><i class="bi bi-clipboard-check"></i>Completion Summary</span>
          <span class="sr-detail-value muted" id="sr-m-summary">—</span>
        </div>

        <div class="sr-detail-divider"></div>

        <div class="sr-detail-item">
          <span class="sr-detail-label"><i class="bi bi-box-arrow-in-right"></i>Punch In</span>
          <span class="sr-detail-value muted" id="sr-m-in">—</span>
        </div>
        <div class="sr-detail-item">
          <span class="sr-detail-label"><i class="bi bi-box-arrow-right"></i>Punch Out</span>
          <span class="sr-detail-value muted" id="sr-m-out">—</span>
        </div>
        <div class="sr-detail-item">
          <span class="sr-detail-label"><i class="bi bi-stopwatch"></i>Duration</span>
          <span class="sr-detail-value" id="sr-m-dur">—</span>
        </div>
        <div class="sr-detail-item">
          <span class="sr-detail-label"><i class="bi bi-calendar-check"></i>Completed</span>
          <span class="sr-detail-value muted" id="sr-m-completed">—</span>
        </div>

        <div class="sr-detail-divider"></div>

        <div class="sr-detail-item">
          <span class="sr-detail-label"><i class="bi bi-boxes"></i>Materials</span>
          <span class="sr-detail-value" id="sr-m-materials">—</span>
        </div>
        <div class="sr-detail-item">
          <span class="sr-detail-label"><i class="bi bi-tools"></i>Labour</span>
          <span class="sr-detail-value" id="sr-m-labour">—</span>
        </div>
        <div class="sr-detail-item full">
          <span class="sr-detail-label"><i class="bi bi-cash-coin"></i>Grand Total</span>
          <span class="sr-detail-value" id="sr-m-total" style="font-size:1.05rem;font-weight:700;color:#059669;">—</span>
        </div>

        <div class="sr-detail-divider"></div>

        <span class="sr-detail-label" style="grid-column:1 / -1;"><i class="bi bi-images"></i>Proof of Work</span>
        <div class="proof-strip">
          <div class="proof-tile" id="tile-before" onclick="openLightbox('before')">
            <div class="proof-thumb" id="thumb-before"><i class="bi bi-image"></i></div>
            <div class="proof-cap">Before <span id="cap-before" class="missing"><i class="bi bi-x-circle"></i></span></div>
          </div>
          <div class="proof-tile" id="tile-after" onclick="openLightbox('after')">
            <div class="proof-thumb" id="thumb-after"><i class="bi bi-image"></i></div>
            <div class="proof-cap">After <span id="cap-after" class="missing"><i class="bi bi-x-circle"></i></span></div>
          </div>
          <div class="proof-tile" id="tile-sign" onclick="openLightbox('signature')">
            <div class="proof-thumb" id="thumb-sign"><i class="bi bi-file-earmark-pdf"></i></div>
            <div class="proof-cap">Acceptance <span id="cap-sign" class="missing"><i class="bi bi-x-circle"></i></span></div>
          </div>
        </div>
      </div>
    </div>
    <div class="sr-modal-foot">
      <a class="btn-gold" id="sr-m-download-sign" href="#" target="_blank" style="display:none;">
        <i class="bi bi-download"></i>Download Signed PDF
      </a>
      <button class="btn-ghost" onclick="closeSrModal()"><i class="bi bi-x-circle"></i>Close</button>
    </div>
  </div>
</div>

{{-- ══════════════ LIGHTBOX ══════════════ --}}
<div class="lb-overlay" id="lightbox" onclick="if(event.target===this)closeLightbox()">
  <div class="lb-bar">
    <div>
      <div class="lb-title" id="lb-title">Preview</div>
      <div class="lb-file" id="lb-file">—</div>
    </div>
    <div class="lb-actions">
      <a class="lb-btn gold" id="lb-download" href="#" target="_blank"><i class="bi bi-download"></i>Download</a>
      <button class="lb-btn" onclick="closeLightbox()"><i class="bi bi-x-lg"></i>Close</button>
    </div>
  </div>
  <div class="lb-stage" id="lb-stage"></div>
</div>

<div id="toastWrap"></div>

@endsection


@push('scripts')
<script>
var currentPage = 1;
var filterTimer = null;
var currentSr = {};   // payload of the SR whose modal is open

// ---- Live clock ----
function updateClock(){
  var el = document.getElementById('clock');
  if (!el) return;
  el.textContent = new Date().toLocaleTimeString([], {hour:'2-digit', minute:'2-digit', second:'2-digit'});
}
setInterval(updateClock, 1000); updateClock();

// ---- Toast ----
function showToast(type, title, body){
  var w = document.getElementById('toastWrap');
  if (!w) return;
  var icons = {ok:'bi-check-circle-fill', err:'bi-x-circle-fill', info:'bi-info-circle-fill'};
  var t = document.createElement('div');
  t.className = 'toast-item';
  t.innerHTML = '<i class="bi '+(icons[type]||icons.info)+' t-ico '+type+'"></i><div><p class="t-title">'+title+'</p><p class="t-body">'+body+'</p></div>';
  w.appendChild(t);
  setTimeout(function(){ t.style.transition='opacity .3s'; t.style.opacity='0'; setTimeout(function(){t.remove();},300); }, 3500);
}

function _set(id, val){
  var el = document.getElementById(id);
  if (el) el.textContent = (val === null || val === undefined || val === '') ? '—' : val;
}

// ══════════════ COMPLETION MODAL ══════════════
function openSrModal(row){
  if (!row) return;
  var data;
  try { data = JSON.parse(row.getAttribute('data-sr') || '{}'); }
  catch (e) { data = {}; }
  currentSr = data;

  _set('sr-m-id', data.code);
  _set('sr-m-client', data.client);
  _set('sr-m-warranty', data.warranty);
  _set('sr-m-site', data.site);
  _set('sr-m-worker', data.worker);
  _set('sr-m-contact', data.contact);
  _set('sr-m-custname', data.cust_name);
  _set('sr-m-issue', data.issue);
  _set('sr-m-summary', data.summary);
  _set('sr-m-in', data.punch_in);
  _set('sr-m-out', data.punch_out);
  _set('sr-m-dur', data.duration);
  _set('sr-m-completed', data.completed);
  _set('sr-m-materials', data.materials);
  _set('sr-m-labour', data.labour);
  _set('sr-m-total', data.total);

  // Proof tiles: before/after are images, signature is a PDF.
  paintPhotoTile('thumb-before', 'cap-before', data.before);
  paintPhotoTile('thumb-after',  'cap-after',  data.after);
  paintPdfTile('thumb-sign', 'cap-sign', data.signature);

  // Footer download button for the signed PDF
  var dl = document.getElementById('sr-m-download-sign');
  if (dl){
    if (data.signature){
      dl.style.display = '';
      dl.href = data.signature;
      dl.setAttribute('download', (data.code || 'acceptance') + '.pdf');
    } else {
      dl.style.display = 'none';
    }
  }

  document.getElementById('srModal').classList.add('show');
  document.body.style.overflow = 'hidden';
}

function paintPhotoTile(thumbId, capId, url){
  var thumb = document.getElementById(thumbId);
  var cap   = document.getElementById(capId);
  if (url){
    thumb.style.backgroundImage = 'url("'+url+'")';
    thumb.innerHTML = '';
    cap.className = 'ok'; cap.innerHTML = '<i class="bi bi-check-circle-fill"></i>';
  } else {
    thumb.style.backgroundImage = 'none';
    thumb.innerHTML = '<i class="bi bi-image"></i>';
    cap.className = 'missing'; cap.innerHTML = '<i class="bi bi-x-circle"></i>';
  }
}

function paintPdfTile(thumbId, capId, url){
  var thumb = document.getElementById(thumbId);
  var cap   = document.getElementById(capId);
  if (url){
    thumb.style.backgroundImage = 'none';
    thumb.innerHTML = '<i class="bi bi-file-earmark-pdf-fill" style="color:#c0392b;"></i>';
    cap.className = 'ok'; cap.innerHTML = '<i class="bi bi-check-circle-fill"></i>';
  } else {
    thumb.style.backgroundImage = 'none';
    thumb.innerHTML = '<i class="bi bi-file-earmark-pdf"></i>';
    cap.className = 'missing'; cap.innerHTML = '<i class="bi bi-x-circle"></i>';
  }
}

function closeSrModal(){
  document.getElementById('srModal').classList.remove('show');
  document.body.style.overflow = '';
}

// ══════════════ LIGHTBOX ══════════════
function openLightbox(type){
  var url, title, isPdf = false;
  if (type === 'before'){ url = currentSr.before; title = 'Start Photo (Punch In)'; }
  else if (type === 'after'){ url = currentSr.after; title = 'Finish Photo (Punch Out)'; }
  else { url = currentSr.signature; title = 'Customer Acceptance (Signed PDF)'; isPdf = true; }

  if (!url){ showToast('info', 'Not available', 'This file was not captured for the job.'); return; }

  document.getElementById('lb-title').textContent = title;
  document.getElementById('lb-file').textContent  = url.split('/').pop();

  var dl = document.getElementById('lb-download');
  dl.href = url;
  dl.setAttribute('download', url.split('/').pop());

  var stage = document.getElementById('lb-stage');
  if (isPdf){
    stage.innerHTML = '<iframe src="'+url+'#toolbar=1" title="'+title+'"></iframe>';
  } else {
    stage.innerHTML = '<img src="'+url+'" alt="'+title+'">';
  }
  document.getElementById('lightbox').classList.add('show');
}

function closeLightbox(){
  document.getElementById('lightbox').classList.remove('show');
  document.getElementById('lb-stage').innerHTML = '';
}

document.addEventListener('keydown', function(e){
  if (e.key !== 'Escape') return;
  if (document.getElementById('lightbox').classList.contains('show')) closeLightbox();
  else closeSrModal();
});

// ══════════════ FILTERING / PAGINATION ══════════════
function debounceFilter(){
  clearTimeout(filterTimer);
  filterTimer = setTimeout(function(){ currentPage = 1; applyFilters(); }, 400);
}
function goToPage(p){ currentPage = p; applyFilters(); }

function applyFilters(){
  var form = document.getElementById('filterForm');
  var params = new URLSearchParams(new FormData(form));
  params.set('page', currentPage);

  var exportBtn = document.getElementById('exportBtn');
  if (exportBtn) exportBtn.href = window.location.pathname + "?" + params.toString() + "&export=csv";

  fetch(window.location.pathname + "?" + params.toString() + "&frag=rows", {
    headers: { 'X-Requested-With': 'XMLHttpRequest' }
  })
  .then(function(r){ return r.text(); })
  .then(function(html){ document.getElementById('sr-tbody').innerHTML = html; });

  fetch(window.location.pathname + "?" + params.toString() + "&frag=pager", {
    headers: { 'X-Requested-With': 'XMLHttpRequest' }
  })
  .then(function(r){ return r.text(); })
  .then(function(html){ document.querySelector('.pagination-bar').innerHTML = html; });
}

function resetFilters(){
  document.getElementById('filterForm').reset();
  currentPage = 1;
  applyFilters();
}
</script>
@endpush