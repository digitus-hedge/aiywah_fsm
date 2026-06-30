@extends('layouts.layout')

@section('title', 'Dispatch Engine')
@section('page_title', 'Dispatch Engine')
@section('page_icon', 'person-gear')

@push('styles')
<style>
/* ══════════════════════════════════════════════════
   DISPATCH ENGINE  ·  Page-scoped styles
   Theme tokens (--app-bg, --card-bg, etc.) are
   already defined globally in theme.css / app.css.
══════════════════════════════════════════════════ */

/* ── Page header ── */
.pg-hdr {
  background: linear-gradient(135deg, #0ea5e9 0%, #6571ff 100%);
  border-radius: 10px; padding: 20px 24px; margin-bottom: 18px;
  color: #fff; position: relative; overflow: hidden;
}
.pg-hdr::before {
  content: ''; position: absolute; left: -40px; bottom: -40px;
  width: 180px; height: 180px; border-radius: 50%;
  background: rgba(255,255,255,.05);
}
.pg-hdr::after {
  content: ''; position: absolute; right: -30px; top: -30px;
  width: 160px; height: 160px; border-radius: 50%;
  background: rgba(255,255,255,.07);
}
.pg-hdr h4 { font-size: 1rem; font-weight: 600; margin: 0 0 3px; position: relative; z-index: 1; color: #fff; }
.pg-hdr p  { font-size: .78rem; margin: 0; opacity: .88; position: relative; z-index: 1; color: #fff; }
.pg-hdr .mrow { display: flex; align-items: center; gap: 8px; margin-top: 10px; position: relative; z-index: 1; flex-wrap: wrap; }
.mbadge { background: rgba(255,255,255,.18); border: 1px solid rgba(255,255,255,.3); border-radius: 20px; font-size: .6875rem; padding: 2px 10px; font-weight: 500; }
@media(max-width:575.98px) { .pg-hdr { padding: 14px 16px; } .pg-hdr h4 { font-size: .9rem; } }

/* ── Stats strip ── */
.stats { display: grid; grid-template-columns: repeat(4,1fr); gap: 10px; margin-bottom: 18px; }
.sc { background: var(--card-bg); border: 1px solid var(--card-border); border-radius: 8px; padding: 13px 15px; display: flex; align-items: center; gap: 12px; box-shadow: var(--card-shadow); }
.sc-ico { width: 38px; height: 38px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 1rem; flex-shrink: 0; }
.sc-num { font-size: 1.4rem; font-weight: 700; line-height: 1; }
.sc-lbl { font-size: .7rem; color: var(--text-muted); }
@media(max-width:767.98px) { .stats { grid-template-columns: repeat(2,1fr); } }

/* ── Workspace ── */
.workspace { display: grid; grid-template-columns: 1fr 400px; gap: 16px; align-items: start; }
@media(max-width:1199.98px) { .workspace { grid-template-columns: 1fr 360px; } }
@media(max-width:991.98px)  { .workspace { grid-template-columns: 1fr; } }

/* ── Card shell ── */
.card { background: var(--card-bg); border: 1px solid var(--card-border); border-radius: 8px; box-shadow: var(--card-shadow); overflow: hidden; }
.chdr { padding: 13px 16px; border-bottom: 1px solid var(--card-border); display: flex; align-items: center; gap: 9px; }
.chdr-ico { width: 30px; height: 30px; border-radius: 6px; display: flex; align-items: center; justify-content: center; font-size: .85rem; flex-shrink: 0; }
.chdr h6  { margin: 0; font-size: .8375rem; font-weight: 600; color: var(--text-heading); }
.chdr .csub { font-size: .7rem; color: var(--text-muted); display: block; }
.cbody { padding: 16px; }

/* ── Filter row ── */
.filter-row { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; margin-bottom: 12px; }
.fsearch { position: relative; flex: 1; min-width: 160px; }
.fsearch i { position: absolute; left: 9px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: .82rem; }
.fsearch input { padding-left: 30px; width: 100%; }
.finput, .fselect {
  font-size: .78rem; border: 1px solid var(--border-color); border-radius: 6px;
  padding: .35rem .7rem; color: var(--text-primary); background: var(--input-bg);
  height: 34px; transition: border-color .15s, box-shadow .15s;
}
.finput:focus, .fselect:focus { border-color: rgba(101,113,255,.5); box-shadow: 0 0 0 3px rgba(101,113,255,.12); outline: none; }
.finput::placeholder { color: var(--text-light); }
[data-theme="dark"] .fselect option { background: #101e33; color: #c8d4e8; }

/* ── Table ── */
.tbl-wrap { overflow-x: auto; -webkit-overflow-scrolling: touch; }
.dtbl { width: 100%; border-collapse: collapse; min-width: 520px; }
.dtbl thead tr { background: var(--surface-2); }
.dtbl thead th { font-size: .68rem; font-weight: 700; text-transform: uppercase; letter-spacing: .07em; color: var(--text-muted); padding: 9px 11px; border-bottom: 1px solid var(--card-border); white-space: nowrap; }
.dtbl tbody tr { border-bottom: 1px solid var(--card-border); cursor: pointer; transition: background .15s; }
.dtbl tbody tr:last-child { border-bottom: none; }
.dtbl tbody tr:hover { background: rgba(101,113,255,.04); }
.dtbl tbody tr.sel { background: rgba(101,113,255,.08); border-left: 3px solid #6571ff; }
.dtbl tbody td { padding: 10px 11px; font-size: .78rem; vertical-align: middle; }
[data-theme="dark"] .dtbl tbody tr:hover { background: rgba(101,113,255,.08); }
.sr-link { color: #6571ff; font-weight: 700; }
.chip { font-size: .62rem; font-weight: 700; padding: 2px 8px; border-radius: 10px; white-space: nowrap; }
.chip-g    { background: rgba(5,163,74,.1);    color: #05a34a; }
.chip-o    { background: rgba(249,115,22,.1);  color: #f97316; }
.chip-b    { background: rgba(101,113,255,.1); color: #6571ff; }
.chip-r    { background: rgba(255,51,102,.1);  color: #ff3366; }
.chip-y    { background: rgba(251,188,6,.1);   color: #a8802a; }
.chip-gray { background: rgba(174,183,197,.12);color: #5a6a7e; }
.sla-ok { color: #05a34a; font-weight: 600; }
.sla-w  { color: #a8802a; font-weight: 600; }
.sla-c  { color: #ff3366; font-weight: 600; }
.tbl-foot { padding: 9px 13px; border-top: 1px solid var(--card-border); font-size: .72rem; color: var(--text-muted); display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 6px; }
.pg-btn { background: var(--surface-2); border: 1px solid var(--border-color); border-radius: 5px; width: 26px; height: 26px; display: flex; align-items: center; justify-content: center; cursor: pointer; font-size: .72rem; color: var(--text-muted); transition: all .15s; }
.pg-btn:hover, .pg-btn.act { background: rgba(101,113,255,.1); border-color: #6571ff; color: #6571ff; }

/* ── Right panel ── */
.right-panel { display: flex; flex-direction: column; gap: 14px; position: sticky; top: 80px; }
@media(max-width:991.98px) { .right-panel { position: static; } }

/* Ticket snapshot */
.snap-empty { text-align: center; padding: 28px 12px; color: var(--text-muted); font-size: .8rem; }
.snap-empty i { display: block; font-size: 1.8rem; margin-bottom: 8px; opacity: .28; }
.tk-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 6px 12px; }
.tk-row .tk-l { color: var(--text-muted); font-size: .67rem; text-transform: uppercase; letter-spacing: .05em; margin-bottom: 1px; }
.tk-row .tk-v { font-weight: 500; color: var(--text-heading); font-size: .78rem; }
.tk-row.full  { grid-column: 1/-1; }

/* Tech cards */
.tech-list { display: flex; flex-direction: column; gap: 8px; max-height: 360px; overflow-y: auto; padding-right: 2px; }
.tech-list::-webkit-scrollbar       { width: 3px; }
.tech-list::-webkit-scrollbar-thumb { background: var(--border-color); border-radius: 2px; }
.tech-card { background: var(--surface-2); border: 1.5px solid var(--card-border); border-radius: 8px; padding: 11px 13px; cursor: pointer; transition: all .18s; display: flex; align-items: center; gap: 11px; }
.tech-card:hover    { background: rgba(101,113,255,.04); border-color: rgba(101,113,255,.35); }
.tech-card.selected { background: rgba(101,113,255,.08); border-color: #6571ff; box-shadow: 0 0 0 2px rgba(101,113,255,.15); }
.tech-card.unavail  { opacity: .5; cursor: not-allowed; }
.tech-card.unavail:hover { background: var(--surface-2); border-color: var(--card-border); }
[data-theme="dark"] .tech-card:hover { background: rgba(101,113,255,.08); }
.tech-av { width: 40px; height: 40px; border-radius: 50%; flex-shrink: 0; display: flex; align-items: center; justify-content: center; font-size: .75rem; font-weight: 700; color: #fff; position: relative; }
.online-dot { position: absolute; bottom: 0; right: 0; width: 10px; height: 10px; border-radius: 50%; border: 2px solid var(--card-bg); }
.dot-free { background: #05a34a; } .dot-busy { background: #fbbc06; } .dot-full { background: #ff3366; }
.tech-name  { font-size: .82rem; font-weight: 600; color: var(--text-heading); }
.tech-skill { font-size: .68rem; color: var(--text-muted); margin-top: 1px; }
.tech-id    { font-size: .67rem; color: var(--text-muted); margin-top: 2px; }
.tech-pipeline { font-size: .68rem; font-weight: 700; padding: 2px 7px; border-radius: 8px; margin-left: auto; flex-shrink: 0; }
.pl-0 { background: rgba(5,163,74,.1);    color: #05a34a; }
.pl-1 { background: rgba(251,188,6,.1);   color: #a8802a; }
.pl-2 { background: rgba(255,51,102,.1);  color: #ff3366; }
.no-tech { text-align: center; padding: 24px; color: var(--text-muted); font-size: .8rem; }
.no-tech i { font-size: 1.8rem; display: block; margin-bottom: 6px; opacity: .28; }

/* Skill filter chips */
.skill-row  { display: flex; gap: 6px; flex-wrap: wrap; margin-bottom: 10px; }
.skill-chip { font-size: .72rem; padding: 3px 11px; border-radius: 20px; border: 1px solid var(--border-color); background: var(--surface-2); color: var(--text-muted); cursor: pointer; transition: all .15s; user-select: none; }
.skill-chip:hover   { border-color: #6571ff; color: #6571ff; }
.skill-chip.picked  { background: #6571ff; color: #fff; border-color: #6571ff; }

/* Section label */
.sec-lbl { font-size: .67rem; font-weight: 700; text-transform: uppercase; letter-spacing: .07em; color: var(--text-muted); display: flex; align-items: center; gap: 8px; margin: 12px 0 8px; }
.sec-lbl::after { content: ''; flex: 1; height: 1px; background: var(--border-color); }

/* Assign summary */
.assign-summary { background: var(--surface-2); border: 1px solid var(--border-color); border-radius: 7px; padding: 12px 14px; margin-bottom: 14px; display: none; }
.assign-summary.show { display: block; }
.as-row { display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 5px; font-size: .78rem; }
.as-row:last-child { margin-bottom: 0; }
.as-k { color: var(--text-muted); }
.as-v { font-weight: 600; color: var(--text-heading); }

/* Dispatch button */
.btn-dispatch { width: 100%; border: none; border-radius: 7px; padding: .65rem 1rem; font-size: .875rem; font-weight: 600; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; background: linear-gradient(135deg,#6571ff,#8b5cf6); color: #fff; transition: all .2s; }
.btn-dispatch:hover:not(:disabled)  { box-shadow: 0 4px 18px rgba(101,113,255,.38); }
.btn-dispatch:active:not(:disabled) { transform: scale(.98); }
.btn-dispatch:disabled { opacity: .45; cursor: not-allowed; background: var(--surface-2); color: var(--text-muted); }

.notify-note { display: flex; align-items: center; gap: 6px; font-size: .7rem; color: var(--text-muted); margin-top: 8px; padding: 7px 10px; background: rgba(101,113,255,.06); border-radius: 6px; border: 1px solid rgba(101,113,255,.15); }
.notify-note i { color: #6571ff; flex-shrink: 0; }

/* ── Toast ── */
.toast-wrap { position: fixed; top: 70px; right: 16px; z-index: 9999; display: flex; flex-direction: column; gap: 8px; max-width: 310px; }
.toast-item { background: var(--card-bg); border-left: 4px solid #6571ff; border-radius: 7px; padding: 12px 14px; box-shadow: 0 6px 24px rgba(0,0,0,.18); display: flex; align-items: flex-start; gap: 10px; animation: toastIn .3s ease; }
.toast-item.success { border-color: #05a34a; } .toast-item.error { border-color: #ff3366; } .toast-item.warning { border-color: #fbbc06; }
@keyframes toastIn { from { transform: translateX(40px); opacity: 0; } to { transform: none; opacity: 1; } }
.ti-ico { font-size: 1.1rem; flex-shrink: 0; margin-top: 1px; }
.ti-ico.primary { color: #6571ff; } .ti-ico.success { color: #05a34a; } .ti-ico.error { color: #ff3366; } .ti-ico.warning { color: #fbbc06; }
.ti-t { font-size: .8125rem; font-weight: 600; margin: 0 0 2px; color: var(--text-heading); }
.ti-b { font-size: .72rem; margin: 0; color: var(--text-muted); }
@media(max-width:575.98px) { .toast-wrap { left: 12px; right: 12px; max-width: none; } }

/* ── Success overlay ── */
.success-overlay { display: none; position: fixed; inset: 0; background: var(--overlay-bg); z-index: 9000; align-items: center; justify-content: center; padding: 16px; }
.success-overlay.show { display: flex; }
.success-box { background: var(--card-bg); border-radius: 14px; border: 1px solid var(--card-border); max-width: 400px; width: 100%; box-shadow: var(--modal-shadow); animation: popIn .28s ease; padding: 32px 28px; text-align: center; }
@keyframes popIn { from { transform: scale(.88); opacity: 0; } to { transform: none; opacity: 1; } }
.ok-ring { width: 60px; height: 60px; border-radius: 50%; background: rgba(5,163,74,.1); display: flex; align-items: center; justify-content: center; font-size: 1.7rem; color: #05a34a; margin: 0 auto 14px; box-shadow: 0 0 0 8px rgba(5,163,74,.06); }
.success-box h5 { font-size: 1.0625rem; font-weight: 700; color: var(--text-heading); margin: 0 0 6px; }
.success-box p  { font-size: .8rem; color: var(--text-muted); margin: 0 0 14px; }
.sr-id-badge { background: rgba(101,113,255,.1); border: 1px solid rgba(101,113,255,.25); border-radius: 7px; padding: 9px 16px; font-size: .9375rem; font-weight: 700; color: #6571ff; display: inline-block; letter-spacing: .04em; margin-bottom: 14px; }
.btn-ok { background: #6571ff; color: #fff; border: none; border-radius: 7px; padding: .55rem 1.8rem; font-size: .875rem; font-weight: 600; cursor: pointer; transition: background .15s; }
.btn-ok:hover { background: #5660d9; }
</style>
@endpush

@section('content')

{{-- ── Toast shelf ── --}}
<div class="toast-wrap" id="toastWrap"></div>

{{-- ── Success overlay ── --}}
<div class="success-overlay" id="successOverlay">
  <div class="success-box">
    <div class="ok-ring"><i class="bi bi-check-lg"></i></div>
    <h5>Dispatched Successfully!</h5>
    <p>The ticket has been assigned and the technician will be notified immediately.</p>
    <div class="sr-id-badge" id="successSrId">SR-2024-0000</div>
    <p id="successDetail" style="font-size:.78rem;color:var(--text-muted);margin-bottom:20px;">—</p>
    <button class="btn-ok" onclick="closeSuccess()">Done</button>
  </div>
</div>

{{-- ══════════════════════════════════════════
     PAGE HEADER
══════════════════════════════════════════ --}}
<div class="pg-hdr">
  <h4><i class="bi bi-person-gear me-2"></i>Dispatch Engine — Assign Technicians</h4>
  <p>Select an approved ticket, pick a technician, and dispatch in one click.</p>
  <div class="mrow">
    <span class="mbadge"><i class="bi bi-briefcase me-1"></i>Head of Projects</span>
    <span class="mbadge"><i class="bi bi-person-gear me-1"></i>Admin</span>
    <span class="mbadge"><i class="bi bi-shield-fill-check me-1"></i>Super Admin</span>
  </div>
</div>

{{-- ══════════════════════════════════════════
     STATS STRIP
══════════════════════════════════════════ --}}
<div class="stats">
  <div class="sc">
    <div class="sc-ico" style="background:rgba(14,165,233,.1);">
      <i class="bi bi-check2-all" style="color:#0ea5e9;"></i>
    </div>
    <div>
      <div class="sc-num" style="color:#0ea5e9;" id="stat-approved">0</div>
      <div class="sc-lbl">Awaiting Dispatch</div>
    </div>
  </div>
  <div class="sc">
    <div class="sc-ico" style="background:rgba(101,113,255,.1);">
      <i class="bi bi-send-check" style="color:#6571ff;"></i>
    </div>
    <div>
      <div class="sc-num" style="color:#6571ff;" id="stat-dispatched">0</div>
      <div class="sc-lbl">Dispatched Today</div>
    </div>
  </div>
  <div class="sc">
    <div class="sc-ico" style="background:rgba(5,163,74,.1);">
      <i class="bi bi-person-check" style="color:#05a34a;"></i>
    </div>
    <div>
      <div class="sc-num" style="color:#05a34a;" id="stat-avail">0</div>
      <div class="sc-lbl">Techs Available</div>
    </div>
  </div>
  <div class="sc">
    <div class="sc-ico" style="background:rgba(255,51,102,.1);">
      <i class="bi bi-exclamation-triangle" style="color:#ff3366;"></i>
    </div>
    <div>
      <div class="sc-num" style="color:#ff3366;" id="stat-overdue">0</div>
      <div class="sc-lbl">SLA Overdue</div>
    </div>
  </div>
</div>

{{-- ══════════════════════════════════════════
     WORKSPACE
══════════════════════════════════════════ --}}
<div class="workspace">

  {{-- ── LEFT: Approved tickets table ── --}}
  <div>
    <div class="card">
      <div class="chdr">
        <div class="chdr-ico" style="background:rgba(14,165,233,.1);">
          <i class="bi bi-table" style="color:#0ea5e9;"></i>
        </div>
        <div>
          <h6>Approved Tickets — Awaiting Dispatch</h6>
          <span class="csub">Click a row to select a ticket, then assign a technician on the right</span>
        </div>
      </div>

      {{-- Filter row --}}
      <div class="cbody" style="padding-bottom:0;">
        <div class="filter-row">
          <div class="fsearch">
            <i class="bi bi-search"></i>
            <input type="text" class="finput" id="srSearch"
                   placeholder="Search SR, client, site…" oninput="applyFilter()"/>
          </div>
          <select class="fselect" style="width:150px;" id="domainFilter" onchange="applyFilter()">
            <option value="">All Domains</option>
            <option>HVAC</option>
            <option>Electrical</option>
            <option>Plumbing</option>
            <option>IT Infrastructure</option>
            <option>Civil</option>
            <option>Mechanical</option>
          </select>
          <select class="fselect" style="width:110px;" id="slaFilter" onchange="applyFilter()">
            <option value="">All SLA</option>
            <option value="ok">On Track</option>
            <option value="warn">At Risk</option>
            <option value="crit">Overdue</option>
          </select>
          <span style="font-size:.7rem;color:var(--text-muted);" id="filterLbl"></span>
        </div>
      </div>

      {{-- Table --}}
      <div class="tbl-wrap">
        <table class="dtbl">
          <thead>
            <tr>
              <th></th>
              <th>SR_ID</th>
              <th>Client</th>
              <th>Domain</th>
              <th>Site</th>
              <th>Priority</th>
              <th>SLA</th>
              <th>Approved</th>
            </tr>
          </thead>
          <tbody id="tableBody"></tbody>
        </table>
        <div id="emptyState" style="display:none;text-align:center;padding:36px 20px;color:var(--text-muted);font-size:.8rem;">
          <i class="bi bi-inbox" style="font-size:2rem;display:block;margin-bottom:6px;opacity:.3;"></i>
          No tickets match your filter.
        </div>
      </div>

      {{-- Table footer --}}
      <div class="tbl-foot">
        <span id="tblLbl"></span>
        <div style="display:flex;gap:4px;" id="pagerBtns"></div>
      </div>
    </div>
  </div>

  {{-- ── RIGHT: Assignment panel ── --}}
  <div class="right-panel" id="rightPanel">

    {{-- Step 1 · Selected ticket --}}
    <div class="card">
      <div class="chdr">
        <div class="chdr-ico" style="background:rgba(14,165,233,.1);">
          <i class="bi bi-file-earmark-check" style="color:#0ea5e9;"></i>
        </div>
        <div>
          <h6>
            <span style="display:inline-flex;align-items:center;justify-content:center;width:18px;height:18px;background:#0ea5e9;border-radius:50%;font-size:.65rem;font-weight:700;color:#fff;margin-right:6px;">1</span>
            Selected Ticket
          </h6>
          <span class="csub">Click any table row to load ticket details</span>
        </div>
      </div>
      <div class="cbody" id="snapBody">
        <div class="snap-empty">
          <i class="bi bi-hand-index-thumb"></i>
          Click a ticket row to start dispatch
        </div>
      </div>
    </div>

    {{-- Step 2 · Assign technician --}}
    <div class="card">
      <div class="chdr">
        <div class="chdr-ico" style="background:rgba(5,163,74,.1);">
          <i class="bi bi-people-fill" style="color:#05a34a;"></i>
        </div>
        <div>
          <h6>
            <span style="display:inline-flex;align-items:center;justify-content:center;width:18px;height:18px;background:#05a34a;border-radius:50%;font-size:.65rem;font-weight:700;color:#fff;margin-right:6px;">2</span>
            Assign Technician
          </h6>
          <span class="csub">Matched by skill domain</span>
        </div>
      </div>
      <div class="cbody">
        <div id="skillChipsWrap" style="display:none;">
          <div class="skill-row" id="skillChips"></div>
          <div class="sec-lbl">Available Technicians</div>
        </div>
        <div class="tech-list" id="techList">
          <div class="no-tech">
            <i class="bi bi-arrow-up-circle"></i>Select a ticket above first
          </div>
        </div>
      </div>
    </div>

    {{-- Step 3 · Dispatch --}}
    <div class="card">
      <div class="chdr">
        <div class="chdr-ico" style="background:rgba(101,113,255,.1);">
          <i class="bi bi-send-fill" style="color:#6571ff;"></i>
        </div>
        <div>
          <h6>
            <span style="display:inline-flex;align-items:center;justify-content:center;width:18px;height:18px;background:#6571ff;border-radius:50%;font-size:.65rem;font-weight:700;color:#fff;margin-right:6px;">3</span>
            Dispatch
          </h6>
          <span class="csub">Review and confirm assignment</span>
        </div>
      </div>
      <div class="cbody">
        <div class="assign-summary" id="assignSummary">
          <div class="as-row"><span class="as-k">Ticket</span><span class="as-v" id="asSrId" style="color:#6571ff;">—</span></div>
          <div class="as-row"><span class="as-k">Client</span><span class="as-v" id="asClient">—</span></div>
          <div class="as-row"><span class="as-k">Domain</span><span class="as-v" id="asDomain">—</span></div>
          <div class="as-row"><span class="as-k">Technician</span><span class="as-v" id="asTech">—</span></div>
          <div class="as-row" style="margin-bottom:0;"><span class="as-k">Priority</span><span class="as-v" id="asPriority">—</span></div>
        </div>
        <button class="btn-dispatch" id="dispatchBtn" onclick="doDispatch()" disabled>
          <i class="bi bi-send-fill"></i>Dispatch Now
        </button>
        <div class="notify-note" style="margin-top:10px;">
          <i class="bi bi-bell-fill"></i>
          <span>A push notification is sent to the technician instantly. Ticket status will change to <strong>Assigned</strong>.</span>
        </div>
      </div>
    </div>

  </div>{{-- /right-panel --}}
</div>{{-- /workspace --}}

@endsection

@push('scripts')
<script>

/* ── Mock data ─────────────────────────────────── */
const DOMAINS   = ['HVAC','Electrical','Plumbing','IT Infrastructure','Civil','Mechanical'];
const CLIENTS   = ['Skyline Technologies Pvt Ltd','Meridian Constructions Ltd','Apex Retail Group','Gulf Maritime Corp','Nova Healthcare LLC','Zenith Towers LLC','Falcon Industries'];
const CONTRACTS = ['CTR-20241001','CTR-20242002','CTR-20243003','CTR-20244004','CTR-20245005','CTR-20246006','CTR-20247007'];
const SITES     = ['Site A — HQ Tower','Site B — Warehouse','Site C — Data Centre','Site D — Branch Office','Site E — Mall Outlet'];
const PRIO      = ['High','Medium','Low'];

const TECHS = [
  {id:'ML-001',name:'Rahul Mehta',  initials:'RM',skills:['HVAC','Mechanical'],          pipeline:1,color:'#6571ff'},
  {id:'ML-002',name:'Sana Patel',   initials:'SP',skills:['Electrical','IT Infrastructure'],pipeline:3,color:'#f97316'},
  {id:'ML-003',name:'James Okoye',  initials:'JO',skills:['Plumbing','Civil'],            pipeline:0,color:'#05a34a'},
  {id:'ML-004',name:'Lin Wei',      initials:'LW',skills:['IT Infrastructure','Electrical'],pipeline:2,color:'#b44fd4'},
  {id:'ML-005',name:'Ahmed Hassan', initials:'AH',skills:['HVAC','Electrical'],           pipeline:0,color:'#0ea5e9'},
  {id:'ML-006',name:'Priya Nair',   initials:'PN',skills:['Civil','Mechanical'],          pipeline:1,color:'#fbbc06'},
  {id:'ML-007',name:'Carlos Lima',  initials:'CL',skills:['Plumbing','HVAC'],             pipeline:2,color:'#ff3366'},
  {id:'ML-008',name:'Anita Sharma', initials:'AS',skills:['Electrical','Mechanical'],     pipeline:0,color:'#20c997'},
];

function rnd(a)    { return a[Math.floor(Math.random()*a.length)]; }
function rndI(a,b) { return Math.floor(Math.random()*(b-a+1))+a;  }
function fmtDt(d)  { return d.toLocaleString('en-GB',{day:'2-digit',month:'short',hour:'2-digit',minute:'2-digit'}); }

const ALL_TK = [];
for (let i = 1; i <= 14; i++) {
  const ci  = rndI(0, CLIENTS.length-1);
  const sub = new Date(); sub.setHours(sub.getHours() - rndI(1,48));
  const hrs = Math.round((Date.now() - sub.getTime()) / 3600000);
  ALL_TK.push({
    id: `SR-2024-A${String(3000+i).slice(1)}`,
    client: CLIENTS[ci], contract: CONTRACTS[ci % CONTRACTS.length],
    domain: rnd(DOMAINS), site: rnd(SITES), priority: rnd(PRIO),
    hrsAgo: hrs, approvedStr: fmtDt(sub), status: 'Approved',
  });
}

let tickets        = [...ALL_TK];
let filtered       = [...tickets];
let selTkId        = null;
let selTechId      = null;
let dispatchedToday= 0;
let currentPage    = 1;
const PER          = 7;

/* ── Stats ─────────────────────────────────────── */
function updateStats() {
  document.getElementById('stat-approved').textContent   = tickets.length;
  document.getElementById('stat-dispatched').textContent = dispatchedToday;
  document.getElementById('stat-avail').textContent      = TECHS.filter(t => t.pipeline < 3).length;
  document.getElementById('stat-overdue').textContent    = tickets.filter(t => t.hrsAgo > 24).length;
}

/* ── Table ──────────────────────────────────────── */
function applyFilter() {
  const q   = document.getElementById('srSearch').value.toLowerCase();
  const dom = document.getElementById('domainFilter').value;
  const sla = document.getElementById('slaFilter').value;
  filtered = tickets.filter(t => {
    const mq = !q   || (t.id.toLowerCase().includes(q) || t.client.toLowerCase().includes(q) || t.site.toLowerCase().includes(q));
    const md = !dom || t.domain === dom;
    const ms = !sla || (sla==='ok'&&t.hrsAgo<=8) || (sla==='warn'&&t.hrsAgo>8&&t.hrsAgo<=24) || (sla==='crit'&&t.hrsAgo>24);
    return mq && md && ms;
  });
  currentPage = 1;
  renderTable();
}

function renderTable() {
  const body  = document.getElementById('tableBody');
  const empty = document.getElementById('emptyState');
  const start = (currentPage - 1) * PER;
  const page  = filtered.slice(start, start + PER);

  document.getElementById('filterLbl').textContent = `${filtered.length}/${tickets.length}`;
  document.getElementById('tblLbl').textContent    = filtered.length
    ? `Showing ${start+1}–${Math.min(start+PER,filtered.length)} of ${filtered.length}`
    : 'No records';

  if (!filtered.length) { body.innerHTML = ''; empty.style.display = 'block'; renderPager(); return; }
  empty.style.display = 'none';

  const pcls = {High:'chip-r', Medium:'chip-y', Low:'chip-g'};
  const dcls = {HVAC:'chip-b', Electrical:'chip-o', Plumbing:'chip-b', 'IT Infrastructure':'chip-b', Civil:'chip-gray', Mechanical:'chip-gray'};

  body.innerHTML = page.map(t => {
    const slaCls = t.hrsAgo>24 ? 'sla-c' : t.hrsAgo>8 ? 'sla-w' : 'sla-ok';
    const slaIco = t.hrsAgo>24 ? 'bi-exclamation-triangle-fill' : t.hrsAgo>8 ? 'bi-clock-history' : 'bi-check-circle';
    const sel    = t.id === selTkId;
    return `<tr class="${sel?'sel':''}" onclick="selectTicket('${t.id}')">
      <td><input type="radio" ${sel?'checked':''} onclick="event.stopPropagation();selectTicket('${t.id}')" style="accent-color:#6571ff;"/></td>
      <td><span class="sr-link">${t.id}</span></td>
      <td>
        <div style="font-weight:500;color:var(--text-heading);max-width:120px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">${t.client}</div>
        <div style="font-size:.67rem;color:var(--text-muted);">${t.contract}</div>
      </td>
      <td><span class="chip ${dcls[t.domain]||'chip-gray'}">${t.domain}</span></td>
      <td style="font-size:.72rem;color:var(--text-muted);max-width:90px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">${t.site.split(' — ')[0]}</td>
      <td><span class="chip ${pcls[t.priority]}">${t.priority}</span></td>
      <td><span class="${slaCls}" style="font-size:.75rem;display:flex;align-items:center;gap:4px;"><i class="bi ${slaIco}"></i>${t.hrsAgo}h</span></td>
      <td style="font-size:.7rem;color:var(--text-muted);white-space:nowrap;">${t.approvedStr}</td>
    </tr>`;
  }).join('');
  renderPager();
}

function renderPager() {
  const total = Math.ceil(filtered.length / PER);
  const cont  = document.getElementById('pagerBtns');
  if (total <= 1) { cont.innerHTML = ''; return; }
  cont.innerHTML = Array.from({length:total}, (_,i) =>
    `<div class="pg-btn ${i+1===currentPage?'act':''}" onclick="goPg(${i+1})">${i+1}</div>`
  ).join('');
}
function goPg(p) { currentPage = p; renderTable(); }

/* ── Select ticket ──────────────────────────────── */
function selectTicket(id) {
  selTkId   = id;
  selTechId = null;
  renderTable();
  const t = tickets.find(x => x.id === id);
  renderSnapshot(t);
  renderTechList(t.domain);
  updateDispatchBtn();
  if (window.innerWidth < 992)
    setTimeout(() => document.getElementById('rightPanel').scrollIntoView({behavior:'smooth',block:'start'}), 120);
}

function renderSnapshot(t) {
  const slaCls = t.hrsAgo>24 ? '#ff3366' : t.hrsAgo>8 ? '#fbbc06' : '#05a34a';
  const pcls   = {High:'chip-r', Medium:'chip-y', Low:'chip-g'};
  document.getElementById('snapBody').innerHTML = `
    <div class="tk-grid">
      <div class="tk-row"><div class="tk-l">SR_ID</div><div class="tk-v" style="color:#6571ff;font-weight:700;">${t.id}</div></div>
      <div class="tk-row"><div class="tk-l">Priority</div><div class="tk-v"><span class="chip ${pcls[t.priority]}">${t.priority}</span></div></div>
      <div class="tk-row full"><div class="tk-l">Client</div><div class="tk-v">${t.client}</div></div>
      <div class="tk-row"><div class="tk-l">Domain</div><div class="tk-v">${t.domain}</div></div>
      <div class="tk-row"><div class="tk-l">SLA Elapsed</div><div class="tk-v" style="color:${slaCls};font-weight:700;">${t.hrsAgo}h ago</div></div>
      <div class="tk-row full"><div class="tk-l">Site</div><div class="tk-v">${t.site}</div></div>
      <div class="tk-row full"><div class="tk-l">Approved At</div><div class="tk-v">${t.approvedStr}</div></div>
    </div>`;
}

/* ── Tech list ──────────────────────────────────── */
function renderTechList(domain) {
  const allSkills = [...new Set(TECHS.flatMap(t => t.skills))].sort();
  document.getElementById('skillChipsWrap').style.display = 'block';
  document.getElementById('skillChips').innerHTML =
    `<span class="skill-chip picked" data-skill="" onclick="filterSkill(this,'')">All</span>` +
    allSkills.map(s => `<span class="skill-chip" data-skill="${s}" onclick="filterSkill(this,'${s}')">${s}</span>`).join('');
  renderTechCards(domain, '');
}

function filterSkill(el, skill) {
  document.querySelectorAll('.skill-chip').forEach(c => c.classList.remove('picked'));
  el.classList.add('picked');
  const t = tickets.find(x => x.id === selTkId);
  renderTechCards(t ? t.domain : null, skill);
}

function renderTechCards(domain, skillOverride) {
  const list = document.getElementById('techList');
  let techs  = TECHS;
  if (skillOverride) techs = TECHS.filter(t => t.skills.includes(skillOverride));
  else if (domain)   techs = TECHS.filter(t => t.skills.some(s => s === domain || s.includes(domain.split(' ')[0])));

  if (!techs.length) { list.innerHTML = '<div class="no-tech"><i class="bi bi-person-x"></i>No technicians for this domain.</div>'; return; }

  list.innerHTML = techs.map(tc => {
    const unavail = tc.pipeline >= 4;
    const sel     = tc.id === selTechId;
    const dotCls  = tc.pipeline === 0 ? 'dot-free' : tc.pipeline <= 2 ? 'dot-busy' : 'dot-full';
    const plLbl   = tc.pipeline === 0 ? 'Free' : `${tc.pipeline} active`;
    const plCls   = tc.pipeline === 0 ? 'pl-0' : tc.pipeline <= 2 ? 'pl-1' : 'pl-2';
    return `<div class="tech-card ${unavail?'unavail':''} ${sel?'selected':''}" data-techid="${tc.id}">
      <div class="tech-av" style="background:linear-gradient(135deg,${tc.color},${tc.color}99);">
        ${tc.initials}<div class="online-dot ${dotCls}"></div>
      </div>
      <div style="flex:1;min-width:0;">
        <div class="tech-name">${tc.name}</div>
        <div class="tech-skill">${tc.skills.join(' · ')}</div>
        <div class="tech-id">${tc.id}</div>
      </div>
      <span class="tech-pipeline ${plCls}">${plLbl}</span>
    </div>`;
  }).join('');

  /* Delegation — avoids template-literal onclick escaping issues */
  list.querySelectorAll('.tech-card:not(.unavail)').forEach(card => {
    card.addEventListener('click', function () {
      selectTech(this.dataset.techid);
    });
  });
}

function selectTech(id) {
  selTechId = id;
  const t   = tickets.find(x => x.id === selTkId);
  if (t) {
    let activeSkill = '';
    document.querySelectorAll('.skill-chip').forEach(c => { if (c.classList.contains('picked')) activeSkill = c.dataset.skill; });
    renderTechCards(t.domain, activeSkill);
  }
  updateDispatchBtn();
}

/* ── Dispatch button state ──────────────────────── */
function updateDispatchBtn() {
  const btn     = document.getElementById('dispatchBtn');
  const summary = document.getElementById('assignSummary');
  const ready   = selTkId && selTechId;
  btn.disabled  = !ready;
  if (ready) {
    const t    = tickets.find(x => x.id === selTkId);
    const tech = TECHS.find(x => x.id === selTechId);
    summary.classList.add('show');
    document.getElementById('asSrId').textContent    = t.id;
    document.getElementById('asClient').textContent  = t.client;
    document.getElementById('asDomain').textContent  = t.domain;
    document.getElementById('asTech').textContent    = `${tech.name} (${tech.id})`;
    document.getElementById('asPriority').textContent= t.priority;
  } else {
    summary.classList.remove('show');
  }
}

/* ── Dispatch ───────────────────────────────────── */
function doDispatch() {
  if (!selTkId || !selTechId) return;
  const btn  = document.getElementById('dispatchBtn');
  const t    = tickets.find(x => x.id === selTkId);
  const tech = TECHS.find(x => x.id === selTechId);
  btn.disabled  = true;
  btn.innerHTML = '<span class="spinner-border" style="width:13px;height:13px;border-width:2px;"></span> Dispatching…';

  setTimeout(() => {
    const tRef = TECHS.find(x => x.id === selTechId);
    if (tRef) tRef.pipeline++;
    tickets  = tickets.filter(x => x.id !== selTkId);
    filtered = [...tickets];
    dispatchedToday++;

    document.getElementById('successSrId').textContent  = t.id;
    document.getElementById('successDetail').textContent = `Assigned to ${tech.name} · ${tech.id} · Status: Assigned`;
    document.getElementById('successOverlay').classList.add('show');

    btn.disabled  = false;
    btn.innerHTML = '<i class="bi bi-send-fill"></i>Dispatch Now';
    selTkId = null; selTechId = null;
    applyFilter(); updateStats();
    document.getElementById('snapBody').innerHTML   = '<div class="snap-empty"><i class="bi bi-hand-index-thumb"></i>Click a ticket row to start dispatch</div>';
    document.getElementById('techList').innerHTML   = '<div class="no-tech"><i class="bi bi-arrow-up-circle"></i>Select a ticket above first</div>';
    document.getElementById('skillChipsWrap').style.display = 'none';
    document.getElementById('skillChips').innerHTML = '';
    document.getElementById('assignSummary').classList.remove('show');
  }, 1400);
}

function closeSuccess() { document.getElementById('successOverlay').classList.remove('show'); }
document.getElementById('successOverlay').addEventListener('click', function (e) { if (e.target === this) closeSuccess(); });

/* ── Toast ──────────────────────────────────────── */
function showToast(type, title, body) {
  const w   = document.getElementById('toastWrap');
  const ico = {success:'bi-check-circle-fill', error:'bi-x-circle-fill', primary:'bi-info-circle-fill', warning:'bi-exclamation-circle-fill'};
  const t   = document.createElement('div');
  t.className = `toast-item ${type}`;
  t.innerHTML = `<i class="bi ${ico[type]||'bi-info-circle-fill'} ti-ico ${type}"></i><div><p class="ti-t">${title}</p><p class="ti-b">${body}</p></div>`;
  w.appendChild(t);
  setTimeout(() => { t.style.opacity='0'; t.style.transition='opacity .3s'; setTimeout(() => t.remove(), 300); }, 4000);
}

/* ── Init ───────────────────────────────────────── */
updateStats();
applyFilter();
</script>
@endpush