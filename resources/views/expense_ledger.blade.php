{{-- resources/views/expense_ledger.blade.php --}}
@extends('layouts.layout')

@section('title', 'Expense Ledger | Matter Mind')
@section('page_title', 'Expense Ledger')
@section('page_icon', 'database')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600;700&display=swap" rel="stylesheet"/>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet"/>
<style>
/* ═══════════════════════════════════════
   EXPENSE LEDGER — scoped page styles
   Brand: #9a8053 gold · #b8975e gold-2
═══════════════════════════════════════ */
.exl-wrap { --gold:#9a8053; --gold-2:#b8975e; }

/* ── PAGE HEADER ── */
.exl-wrap .pg-header {
  background:linear-gradient(135deg,#9A7B4F 0%,#7A6140 100%);
  border-radius:12px; padding:22px 26px; margin-bottom:18px;
  color:#fff; position:relative; overflow:hidden;
}
.exl-wrap .pg-header::before {
  content:''; position:absolute; left:-40px; bottom:-40px; width:180px; height:180px;
  border-radius:50%; background:rgba(255,255,255,.05);
}
.exl-wrap .pg-header::after {
  content:''; position:absolute; right:-30px; top:-30px; width:160px; height:160px;
  border-radius:50%; background:rgba(255,255,255,.07);
}
.exl-wrap .pg-hdr-title { font-size:1.05rem; font-weight:600; margin:0 0 4px; position:relative; z-index:1; }
.exl-wrap .pg-hdr-desc  { font-size:.78rem; margin:0; opacity:.85; position:relative; z-index:1; }
.exl-wrap .pg-hdr-meta  { display:flex; align-items:center; gap:8px; margin-top:11px; position:relative; z-index:1; flex-wrap:wrap; }
.exl-wrap .meta-badge {
  background:rgba(255,255,255,.18); border:1px solid rgba(255,255,255,.3);
  border-radius:20px; font-size:.6875rem; padding:2px 10px; font-weight:500;
}

/* ── SEARCH + TOTAL STRIP ──
   The AED total sits inside the filter bar as a right-hand rail, so the
   number reflects whatever the current filter is showing rather than
   floating above it as a separate, always-static card. */
.exl-wrap .search-bar {
  background:var(--card-bg); border:1px solid var(--card-border); border-radius:12px;
  padding:14px 18px; margin-bottom:18px; box-shadow:var(--card-shadow);
  display:flex; flex-wrap:wrap; gap:14px; align-items:flex-end;
}
.exl-wrap .filter-group { display:flex; flex-direction:column; gap:5px; }
.exl-wrap .filter-group.grow { flex:1 1 320px; min-width:0; }
.exl-wrap .filter-group.cat  { flex:0 0 200px; }
.exl-wrap .filter-label {
  font-size:.68rem; font-weight:600; color:var(--text-muted);
  text-transform:uppercase; letter-spacing:.06em;
}

.exl-wrap .search-field { position:relative; }
.exl-wrap .search-field > i {
  position:absolute; left:12px; top:50%; transform:translateY(-50%);
  color:var(--text-muted); font-size:.85rem; pointer-events:none;
}
.exl-wrap .search-field .filter-control { padding-left:34px; }
.exl-wrap .search-clear {
  position:absolute; right:8px; top:50%; transform:translateY(-50%);
  background:none; border:none; color:var(--text-muted); cursor:pointer;
  padding:3px 5px; border-radius:5px; line-height:1; font-size:.8rem; display:none;
}
.exl-wrap .search-clear.show { display:block; }
.exl-wrap .search-clear:hover { background:var(--surface-2); color:var(--text-heading); }

.exl-wrap .filter-control {
  width:100%; height:38px; padding:0 12px;
  border:1px solid var(--border-color); border-radius:8px;
  background:var(--input-bg); color:var(--text-primary); font-size:.82rem;
  transition:border-color .15s, box-shadow .15s;
}
.exl-wrap select.filter-control { -webkit-appearance:none; appearance:none; padding-right:30px;
  background-image:linear-gradient(45deg,transparent 50%,var(--text-muted) 50%),
                   linear-gradient(135deg,var(--text-muted) 50%,transparent 50%);
  background-position:calc(100% - 15px) 17px, calc(100% - 10px) 17px;
  background-size:5px 5px, 5px 5px; background-repeat:no-repeat;
}
.exl-wrap .filter-control:focus {
  outline:none; border-color:var(--gold); box-shadow:0 0 0 3px rgba(154,128,83,.12);
}
[data-bs-theme="dark"] .exl-wrap select.filter-control option { background:#2a2820; color:#d4cfc8; }

.exl-wrap .total-rail {
  margin-left:auto; display:flex; align-items:center; gap:12px;
  padding-left:18px; border-left:1px solid var(--border-color); align-self:stretch;
}
.exl-wrap .total-icon {
  width:40px; height:40px; border-radius:9px; flex-shrink:0;
  display:flex; align-items:center; justify-content:center;
  background:rgba(154,128,83,.1); color:var(--gold); font-size:1.05rem;
}
.exl-wrap .total-num {
  font-size:1.5rem; font-weight:700; line-height:1.05; color:var(--text-heading);
  white-space:nowrap;
}
.exl-wrap .total-lbl { font-size:.67rem; color:var(--text-muted); text-transform:uppercase; letter-spacing:.06em; }

.exl-wrap .filter-actions { display:flex; gap:8px; align-items:flex-end; }

@media (max-width:991.98px) {
  .exl-wrap .total-rail {
    margin-left:0; padding-left:0; border-left:none; order:-1;
    flex:1 1 100%; padding-bottom:12px; border-bottom:1px solid var(--border-color);
  }
}
@media (max-width:575.98px) {
  .exl-wrap .filter-group,
  .exl-wrap .filter-group.cat { flex:1 1 100%; }
  .exl-wrap .filter-actions { width:100%; }
  .exl-wrap .filter-actions .btn-ghost { flex:1; justify-content:center; }
}

/* ── BUTTONS ── */
.exl-wrap .btn-ghost {
  display:inline-flex; align-items:center; gap:6px; height:38px; padding:0 14px;
  background:var(--surface-2); color:var(--text-muted);
  border:1px solid var(--border-color); border-radius:8px;
  font-size:.8rem; cursor:pointer; white-space:nowrap; transition:all .15s;
}
.exl-wrap .btn-ghost:hover { background:var(--surface-3); color:var(--text-heading); border-color:var(--text-muted); }

/* ── LEDGER TABLE ── */
.exl-wrap .tbl-card {
  background:var(--card-bg); border:1px solid var(--card-border); border-radius:12px;
  box-shadow:var(--card-shadow); overflow:hidden;
}
.exl-wrap .tbl-card-hdr {
  display:flex; align-items:center; justify-content:space-between;
  padding:14px 18px; border-bottom:1px solid var(--border-color); flex-wrap:wrap; gap:10px;
}
.exl-wrap .card-title { font-size:.875rem; font-weight:600; color:var(--text-heading); }
.exl-wrap .result-count {
  font-size:.75rem; color:var(--text-muted); background:var(--surface-2);
  padding:2px 10px; border-radius:9px;
}
.exl-wrap .tbl-scroll { overflow-x:auto; -webkit-overflow-scrolling:touch; }
.exl-wrap table.ledger { width:100%; border-collapse:collapse; min-width:680px; }
.exl-wrap table.ledger thead th {
  padding:10px 16px; font-size:.68rem; font-weight:700; text-transform:uppercase;
  letter-spacing:.07em; color:var(--text-muted); background:var(--surface-2);
  border-bottom:1px solid var(--border-color); white-space:nowrap; text-align:left;
}
.exl-wrap table.ledger tbody tr { border-bottom:1px solid var(--border-color); transition:background .1s; }
.exl-wrap table.ledger tbody tr:last-child { border-bottom:none; }
.exl-wrap table.ledger tbody tr:hover { background:rgba(154,128,83,.035); }
.exl-wrap table.ledger td { padding:11px 16px; font-size:.8rem; color:var(--text-primary); vertical-align:middle; }
.exl-wrap table.ledger td.mono  { font-size:.78rem; font-weight:600; color:var(--gold); white-space:nowrap; }
.exl-wrap table.ledger td.muted { color:var(--text-muted); font-size:.78rem; }
.exl-wrap table.ledger td.tech  { font-size:.8rem; font-weight:500; }
.exl-wrap table.ledger td.amt   { text-align:right; font-weight:600; color:var(--text-heading); white-space:nowrap; }
.exl-wrap table.ledger th.amt   { text-align:right; }
.exl-wrap .row-empty { text-align:center; padding:44px 20px; color:var(--text-muted); }
.exl-wrap .row-empty i { font-size:2.4rem; display:block; margin-bottom:10px; opacity:.22; }

/* ── RECEIPT THUMB ── */
.exl-wrap .receipt-thumb {
  width:44px; height:44px; border-radius:8px; overflow:hidden;
  border:1px solid var(--border-color); background:linear-gradient(135deg,#f5f0e8,#ede3d0);
  display:flex; align-items:center; justify-content:center; cursor:pointer;
  transition:all .15s; font-size:.95rem; color:var(--text-muted); padding:0;
}
.exl-wrap .receipt-thumb img { width:100%; height:100%; object-fit:cover; display:block; }
.exl-wrap .receipt-thumb:hover { border-color:var(--gold); color:var(--gold); transform:scale(1.06); }
.exl-wrap .receipt-thumb.no-rcpt { cursor:default; opacity:.45; }
.exl-wrap .receipt-thumb.no-rcpt:hover { border-color:var(--border-color); color:var(--text-muted); transform:none; }
[data-bs-theme="dark"] .exl-wrap .receipt-thumb { background:linear-gradient(135deg,#2a2820,#302e24); }

.exl-wrap .ledger-foot {
  display:flex; align-items:center; justify-content:flex-end;
  padding:12px 18px; border-top:1px solid var(--border-color);
  background:var(--surface-2); font-size:.72rem; color:var(--text-muted);
  flex-wrap:wrap; gap:10px;
}

/* ── RECEIPT MODAL ── */
.exl-modal-overlay {
  display:none; position:fixed; inset:0;
  background:var(--overlay-bg,rgba(9,15,35,.62)); z-index:900;
  align-items:center; justify-content:center; backdrop-filter:blur(4px); padding:20px;
}
.exl-modal-overlay.show { display:flex; }
.exl-modal-overlay .modal-box {
  background:var(--modal-bg,#fff); border-radius:12px; width:100%; max-width:620px;
  box-shadow:var(--modal-shadow,0 24px 64px rgba(0,0,0,.16));
  border:1px solid var(--card-border); overflow:hidden; animation:exlMIn .18s ease;
}
@keyframes exlMIn { from{opacity:0;transform:scale(.96);} to{opacity:1;transform:scale(1);} }
.exl-modal-overlay .modal-hdr {
  display:flex; align-items:center; justify-content:space-between;
  padding:15px 20px; border-bottom:1px solid var(--border-color);
}
.exl-modal-overlay .modal-hdr-left { display:flex; align-items:center; gap:10px; }
.exl-modal-overlay .modal-hdr-icon {
  width:32px; height:32px; border-radius:8px; flex-shrink:0;
  display:flex; align-items:center; justify-content:center;
  font-size:.9rem; background:rgba(245,158,11,.1);
}
.exl-modal-overlay .modal-hdr h6 { font-size:.9rem; font-weight:600; color:var(--text-heading); margin:0; }
.exl-modal-overlay .modal-close {
  background:none; border:none; color:var(--text-muted); cursor:pointer;
  padding:4px; border-radius:5px; font-size:1rem; line-height:1;
}
.exl-modal-overlay .modal-close:hover { background:var(--surface-2); }

.exl-modal-overlay .lb-stage {
  min-height:320px; max-height:66vh; background:linear-gradient(145deg,#f5f0e8,#ede3d0);
  display:flex; align-items:center; justify-content:center; padding:16px; overflow:auto;
}
[data-bs-theme="dark"] .exl-modal-overlay .lb-stage { background:linear-gradient(145deg,#2a2820,#302e24); }
.exl-modal-overlay .lb-stage img {
  max-width:100%; max-height:60vh; object-fit:contain; border-radius:8px; display:block;
}
.exl-modal-overlay .lb-fallback {
  display:flex; flex-direction:column; align-items:center; gap:10px;
  text-align:center; color:var(--text-muted);
}
.exl-modal-overlay .lb-fallback i { font-size:3rem; opacity:.2; }
.exl-modal-overlay .lb-foot {
  display:flex; align-items:center; justify-content:space-between;
  padding:11px 18px; border-top:1px solid var(--border-color);
  background:var(--surface-2); font-size:.78rem; color:var(--text-muted);
  flex-wrap:wrap; gap:8px;
}
.exl-modal-overlay .lb-dl {
  display:inline-flex; align-items:center; gap:5px; padding:5px 12px;
  background:rgba(154,128,83,.1); color:#9a8053;
  border:1px solid rgba(154,128,83,.25); border-radius:6px;
  font-size:.75rem; font-weight:500; cursor:pointer; text-decoration:none;
}
.exl-modal-overlay .lb-dl:hover { background:rgba(154,128,83,.18); color:#9a8053; }
/* ═══ PAGINATION BAR ═══ */
.exl-wrap .cd-pagination-bar {
  display:flex; align-items:center; justify-content:space-between;
  padding:13px 18px; border-top:1px solid var(--border-color);
  background:var(--surface-2); flex-wrap:wrap; gap:10px;
}
.exl-wrap .cd-page-info { font-size:.78rem; color:var(--text-muted); font-weight:500; }
.exl-wrap .cd-pager { display:flex; gap:5px; }
.exl-wrap .cd-page-btn {
  min-width:32px; height:32px; display:flex; align-items:center; justify-content:center;
  border-radius:8px; border:1px solid var(--border-color); background:var(--card-bg);
  color:var(--text-muted); font-size:.78rem; font-weight:600; padding:0 8px;
  cursor:pointer; transition:all .15s;
}
.exl-wrap .cd-page-btn:hover:not(:disabled) {
  border-color:var(--gold); color:var(--gold); background:rgba(154,128,83,.06);
}
.exl-wrap .cd-page-btn.active {
  background:linear-gradient(135deg,var(--gold),var(--gold-2));
  border-color:var(--gold); color:#fff; box-shadow:0 2px 8px rgba(154,128,83,.35);
}
.exl-wrap .cd-page-btn:disabled {
  color:var(--text-light); background:var(--card-bg); border-color:var(--border-color);
  opacity:.5; cursor:not-allowed;
}
.exl-wrap .cd-page-btn.dots { border:none; background:none; cursor:default; }

@media (max-width:575.98px) {
  .exl-wrap .cd-pagination-bar { flex-direction:column; align-items:flex-start; padding:12px 16px; }
  .exl-wrap .cd-pager { align-self:flex-end; }
}
/* ── TOAST ── */
.exl-toast-wrap {
  position:fixed; bottom:22px; right:22px; z-index:9999;
  display:flex; flex-direction:column; gap:8px;
  pointer-events:none; max-width:calc(100vw - 44px);
}
.exl-toast-wrap .toast-item {
  display:flex; align-items:flex-start; gap:10px; padding:12px 15px; border-radius:9px;
  background:var(--card-bg); border:1px solid var(--card-border);
  box-shadow:0 6px 24px rgba(0,0,0,.14); min-width:240px; max-width:320px;
  animation:exlToastIn .2s ease; pointer-events:auto;
}
@keyframes exlToastIn { from{opacity:0;transform:translateY(10px);} to{opacity:1;transform:translateY(0);} }
.exl-toast-wrap .t-ico { font-size:1rem; flex-shrink:0; margin-top:1px; }
.exl-toast-wrap .t-ico.ok   { color:#15803d; }
.exl-toast-wrap .t-ico.err  { color:#ef4444; }
.exl-toast-wrap .t-ico.info { color:var(--gold); }
.exl-toast-wrap .t-ico.warn { color:#d97706; }
.exl-toast-wrap .t-title { font-size:.8rem; font-weight:600; color:var(--text-heading); margin:0 0 2px; }
.exl-toast-wrap .t-body  { font-size:.75rem; color:var(--text-muted); margin:0; }
@media (max-width:575.98px) {
  .exl-toast-wrap { left:12px; right:12px; bottom:12px; }
  .exl-toast-wrap .toast-item { max-width:none; }
}
</style>
@endpush

@section('content')
<div class="exl-wrap">

  {{-- PAGE HEADER --}}
  <div class="pg-header">
    <h4 class="pg-hdr-title">
      <i class="bi bi-cash-stack me-2"></i>Internal Expense &amp; Material Reconciliation Ledger
    </h4>
    <p class="pg-hdr-desc">
      Audit field technician material expenses against uploaded receipts.
    </p>
    <div class="pg-hdr-meta">
      <span class="meta-badge"><i class="bi bi-shield-fill-check me-1"></i>Super Admin</span>
      <span class="meta-badge"><i class="bi bi-person-gear me-1"></i>Admin</span>
      <span class="meta-badge"><i class="bi bi-person-workspace me-1"></i>Head of Projects</span>
      <span class="meta-badge"><i class="bi bi-calculator me-1"></i>Accounts / AR</span>
    </div>
  </div>

  {{-- SEARCH + TOTAL --}}
  <div class="search-bar">
    <div class="filter-group grow">
      <label class="filter-label" for="led-search">Search SR / Technician</label>
      <div class="search-field">
        <i class="bi bi-search"></i>
        <input class="filter-control" type="search" id="led-search"
               placeholder="SR ID or technician name&hellip;" autocomplete="off"/>
        <button class="search-clear" id="led-clear" type="button" aria-label="Clear search">
          <i class="bi bi-x-lg"></i>
        </button>
      </div>
    </div>

   <div class="filter-group cat">
    <label class="filter-label" for="led-cat">Category</label>
    <select class="filter-control" id="led-cat">
        <option value="">All Categories</option>

        @foreach ($categories as $category)
            <option value="{{ $category }}">
                {{ $category }}
            </option>
        @endforeach
    </select>
</div>

    <div class="filter-actions">
      <button class="btn-ghost" type="button" id="led-reset">
        <i class="bi bi-x-circle"></i>Reset
      </button>
      <button class="btn-ghost" type="button" id="led-export">
        <i class="bi bi-download"></i>Export
      </button>
    </div>

    <div class="total-rail">
      <div class="total-icon"><i class="bi bi-cash-coin"></i></div>
      <div>
        <div class="total-num" id="led-total">AED 0</div>
        <div class="total-lbl">Total Field Expenses</div>
      </div>
    </div>
  </div>

  {{-- LEDGER TABLE --}}
  <div class="tbl-card">
    <div class="tbl-card-hdr">
      <div style="display:flex;align-items:center;gap:10px;">
        <span class="card-title" style="font-family:unset;">Expense Reconciliation Ledger</span>
        <span class="result-count" id="led-count">0 entries</span>
      </div>
    </div>

    <div class="tbl-scroll">
      <table class="ledger">
        <thead>
          <tr>
            <th style="width:48px;">#</th>
            <th>SR ID</th>
            <th>Technician</th>
            <th>Item</th>
            <th>Category</th>
            <th class="amt">Amount</th>
            <th style="width:70px;">Receipt</th>
            <th>Date</th>
          </tr>
        </thead>
        <tbody id="led-tbody"></tbody>
      </table>
    </div>

        <div class="cd-pagination-bar">
      <div class="cd-page-info" id="led-page-info">Showing 0 of 0 entries</div>
      <div class="cd-pager" id="led-pager"></div>
    </div>
  </div>

  {{-- RECEIPT MODAL --}}
  <div class="exl-modal-overlay" id="rcpt-modal">
    <div class="modal-box">
      <div class="modal-hdr">
        <div class="modal-hdr-left">
          <div class="modal-hdr-icon"><i class="bi bi-receipt" style="color:#d97706;"></i></div>
          <h6 id="rcpt-title" style="font-family:unset;">Receipt Image</h6>
        </div>
        <button class="modal-close" type="button" id="rcpt-close" aria-label="Close">
          <i class="bi bi-x-lg"></i>
        </button>
      </div>

      <div class="lb-stage" id="rcpt-stage"></div>

      <div class="lb-foot">
        <span id="rcpt-meta"></span>
        <a class="lb-dl" id="rcpt-dl" href="#" download target="_blank" rel="noopener">
          <i class="bi bi-download"></i>Download
        </a>
      </div>
    </div>
  </div>

  <div class="exl-toast-wrap" id="exlToastWrap"></div>
</div>
@endsection

@push('scripts')
<script>
'use strict';

/* =========================================================
   Expense Ledger

   Expected row shape from the controller:
     { id, sr, tech, name, cat, amt, receiptUrl, date }

   `receiptUrl` is the presence check *and* the image source — a
   separate `receipt` boolean would let the two drift apart.
   ========================================================= */
(function () {

const LEDGER = @json($ledger ?? []);

const $ = (id) => document.getElementById(id);

function esc(value) {
  return String(value ?? '')
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#39;');
}

const money = (n) => 'AED ' + Number(n || 0).toLocaleString('en-AE', {
  minimumFractionDigits: 2, maximumFractionDigits: 2,
});

/* ── RENDER ── */
/* ── PAGINATION STATE ── */
const PAGE_SIZE = 10;
let currentPage = 1;
let filteredList = LEDGER.slice();

/* ── RENDER (current page only) ── */
function render(list) {
  filteredList = list;

  const totalPages = Math.max(1, Math.ceil(list.length / PAGE_SIZE));
  if (currentPage > totalPages) currentPage = totalPages;
  if (currentPage < 1) currentPage = 1;

  const start = (currentPage - 1) * PAGE_SIZE;
  const pageItems = list.slice(start, start + PAGE_SIZE);

  const tbody = $('led-tbody');

  $('led-count').textContent = `${list.length} ${list.length === 1 ? 'entry' : 'entries'}`;
  $('led-total').textContent = money(list.reduce((sum, e) => sum + Number(e.amt || 0), 0));

  if (!list.length) {
    tbody.innerHTML =
      '<tr><td colspan="8" class="row-empty">' +
      '<i class="bi bi-inbox"></i>No entries match your filters</td></tr>';
    $('led-page-info').textContent = 'No entries';
    renderPager(0, 1);
    return;
  }

  tbody.innerHTML = pageItems.map((e, i) => {
    const receipt = e.receiptUrl
      ? `<button class="receipt-thumb" type="button" title="View receipt"
                 data-url="${esc(e.receiptUrl)}" data-sr="${esc(e.sr)}" data-tech="${esc(e.tech)}">
           <img src="${esc(e.receiptUrl)}" alt="" loading="lazy"
                onerror="this.replaceWith(Object.assign(document.createElement('i'),{className:'bi bi-image'}))"/>
         </button>`
      : '<div class="receipt-thumb no-rcpt" title="No receipt"><i class="bi bi-image-alt"></i></div>';

    return `<tr>
      <td class="muted">${start + i + 1}</td>
      <td class="mono">${esc(e.sr)}</td>
      <td class="tech">${esc(e.tech)}</td>
      <td>${esc(e.name ?? '—')}</td>
      <td class="muted">${esc(e.cat)}</td>
      <td class="amt">${money(e.amt)}</td>
      <td>${receipt}</td>
      <td class="muted">${esc(e.date)}</td>
    </tr>`;
  }).join('');

  const shownFrom = start + 1;
  const shownTo = Math.min(start + PAGE_SIZE, list.length);
  $('led-page-info').textContent = `Showing ${shownFrom}\u2013${shownTo} of ${list.length} entries`;

  renderPager(list.length, totalPages);
}

/* ── PAGER BUTTONS ── */
function renderPager(total, totalPages) {
  const pager = $('led-pager');

  if (totalPages <= 1) { pager.innerHTML = ''; return; }

  const buttons = [];

  buttons.push(`<button class="cd-page-btn" type="button" data-page="${currentPage - 1}" ${currentPage === 1 ? 'disabled' : ''}>
    <i class="bi bi-chevron-left"></i></button>`);

  const windowSize = 1;
  let lastPrinted = 0;

  for (let p = 1; p <= totalPages; p++) {
    const inWindow = p === 1 || p === totalPages || Math.abs(p - currentPage) <= windowSize;
    if (!inWindow) continue;

    if (lastPrinted && p - lastPrinted > 1) {
      buttons.push('<span class="cd-page-btn dots">&hellip;</span>');
    }
    buttons.push(`<button class="cd-page-btn ${p === currentPage ? 'active' : ''}" type="button" data-page="${p}">${p}</button>`);
    lastPrinted = p;
  }

  buttons.push(`<button class="cd-page-btn" type="button" data-page="${currentPage + 1}" ${currentPage === totalPages ? 'disabled' : ''}>
    <i class="bi bi-chevron-right"></i></button>`);

  pager.innerHTML = buttons.join('');
}

$('led-pager').addEventListener('click', (e) => {
  const btn = e.target.closest('.cd-page-btn[data-page]');
  if (!btn || btn.disabled) return;
  currentPage = Number(btn.dataset.page);
  render(filteredList);
});

/* ── FILTER ── */
function applyFilters() {
  const q   = $('led-search').value.trim().toLowerCase();
  const cat = $('led-cat').value;

  $('led-clear').classList.toggle('show', q.length > 0);

  currentPage = 1; // reset to page 1 on every new filter

  render(LEDGER.filter((e) => {
    const matchesQuery = !q
      || String(e.sr   ?? '').toLowerCase().includes(q)
      || String(e.tech ?? '').toLowerCase().includes(q)
      || String(e.name ?? '').toLowerCase().includes(q);
    return matchesQuery && (!cat || e.cat === cat);
  }));
}

$('led-search').addEventListener('input', applyFilters);
$('led-cat').addEventListener('change', applyFilters);

$('led-clear').addEventListener('click', () => {
  $('led-search').value = '';
  $('led-search').focus();
  applyFilters();
});

$('led-reset').addEventListener('click', () => {
  $('led-search').value = '';
  $('led-cat').value = '';
  applyFilters();
});

$('led-export').addEventListener('click', () => {
  toast('ok', 'Export', 'Generating ledger CSV\u2026');
});

/* ── RECEIPT MODAL ── */
$('led-tbody').addEventListener('click', (e) => {
  const thumb = e.target.closest('.receipt-thumb[data-url]');
  if (thumb) openReceipt(thumb.dataset.url, thumb.dataset.sr, thumb.dataset.tech);
});

function openReceipt(url, sr, tech) {
  $('rcpt-title').textContent = `Receipt \u2014 ${sr}`;
  $('rcpt-meta').textContent  = `${sr} \u00b7 ${tech}`;
  $('rcpt-dl').href = url;

  const isPdf = /\.pdf(\?|$)/i.test(url);

  $('rcpt-stage').innerHTML = isPdf
    ? `<div class="lb-fallback">
         <i class="bi bi-file-earmark-pdf"></i>
         <div>PDF receipt \u2014 use Download to open it.</div>
       </div>`
    : `<img src="${esc(url)}" alt="Receipt for ${esc(sr)}"/>`;

  // A broken path should say so, not leave an empty grey box.
  const img = $('rcpt-stage').querySelector('img');
  if (img) {
    img.addEventListener('error', () => {
      $('rcpt-stage').innerHTML =
        '<div class="lb-fallback"><i class="bi bi-image-alt"></i>' +
        '<div>Receipt image could not be loaded.</div></div>';
    });
  }

  $('rcpt-modal').classList.add('show');
}

function closeReceipt() { $('rcpt-modal').classList.remove('show'); }

$('rcpt-close').addEventListener('click', closeReceipt);
$('rcpt-modal').addEventListener('click', (e) => {
  if (e.target === $('rcpt-modal')) closeReceipt();
});
document.addEventListener('keydown', (e) => {
  if (e.key === 'Escape' && $('rcpt-modal').classList.contains('show')) closeReceipt();
});

/* ── TOAST ── */
function toast(type, title, body) {
  const icons = {
    ok:   'bi-check-circle-fill',
    err:  'bi-x-circle-fill',
    info: 'bi-info-circle-fill',
    warn: 'bi-exclamation-triangle-fill',
  };
  const el = document.createElement('div');
  el.className = 'toast-item';
  el.innerHTML =
    `<i class="bi ${icons[type] ?? icons.info} t-ico ${type}"></i>` +
    `<div><p class="t-title">${esc(title)}</p><p class="t-body">${esc(body)}</p></div>`;
  $('exlToastWrap').appendChild(el);

  setTimeout(() => {
    el.style.transition = 'opacity .3s';
    el.style.opacity = '0';
    setTimeout(() => el.remove(), 300);
  }, 3800);
}

/* ── INIT ── */
render(LEDGER);

})();
</script>
@endpush