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
    background: linear-gradient(135deg, #9A7B4F 0%, #C4A882 100%);
    border-radius: 10px;
    padding: 20px 24px;
    margin-bottom: 18px;
    color: #fff;
    position: relative;
    overflow: hidden;
  }

  .pg-hdr::before {
    content: '';
    position: absolute;
    left: -40px;
    bottom: -40px;
    width: 180px;
    height: 180px;
    border-radius: 50%;
    background: rgba(255, 255, 255, .05);
  }

  .pg-hdr::after {
    content: '';
    position: absolute;
    right: -30px;
    top: -30px;
    width: 160px;
    height: 160px;
    border-radius: 50%;
    background: rgba(255, 255, 255, .07);
  }

  .pg-hdr h4 {
    font-size: 1rem;
    font-weight: 600;
    margin: 0 0 3px;
    position: relative;
    z-index: 1;
    color: #fff;
  }

  .pg-hdr p {
    font-size: .78rem;
    margin: 0;
    opacity: .88;
    position: relative;
    z-index: 1;
    color: #fff;
  }

  .m-box {
    background: var(--modal-bg);
    border-radius: 12px;
    border: 1px solid var(--card-border);
    max-width: 460px;
    width: 100%;
    box-shadow: var(--modal-shadow);
    animation: popIn .25s ease;
    overflow: hidden;
}


.m-overlay.show {
    display: flex;
}

.m-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: var(--overlay-bg);
    z-index: 9000;
    align-items: center;
    justify-content: center;
    padding: 16px;
}

.m-hdr {
    padding: 15px 18px;
    border-bottom: 1px solid var(--card-border);
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.m-close {
    background: none;
    border: none;
    color: var(--text-muted);
    cursor: pointer;
    font-size: 1.1rem;
    padding: 4px;
    border-radius: 4px;
    line-height: 1;
}

.m-body {
    padding: 18px 20px;
}
  .pg-hdr .mrow {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-top: 10px;
    position: relative;
    z-index: 1;
    flex-wrap: wrap;
  }

  .m-icon-ring {
    width: 58px;
    height: 58px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.6rem;
    margin: 0 auto 14px;
}

.m-title {
    font-size: .9375rem;
    font-weight: 600;
    color: var(--text-heading);
    text-align: center;
    margin-bottom: 5px;
}

.m-sub {
    font-size: .78rem;
    color: var(--text-muted);
    text-align: center;
    line-height: 1.5;
}
.m-summary {
    background: var(--surface-2);
    border: 1px solid var(--border-color);
    border-radius: 7px;
    padding: 12px 14px;
    margin: 14px 0;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 8px;
}

.push-note {
    display: flex;
    align-items: center;
    gap: 7px;
    font-size: .72rem;
    padding: 8px 11px;
    background: rgba(101, 113, 255, .07);
    border: 1px solid rgba(101, 113, 255, .18);
    border-radius: 6px;
    color: var(--text-primary);
    margin-top: 2px;
}
.m-ftr {
    padding: 13px 18px;
    border-top: 1px solid var(--card-border);
    display: flex;
    gap: 10px;
    justify-content: flex-end;
}

.btn-cancel-m {
    background: var(--surface-2);
    border: 1px solid var(--border-color);
    border-radius: 6px;
    padding: .42rem 1rem;
    font-size: .8rem;
    cursor: pointer;
    color: var(--text-muted);
    transition: all .15s;
}

.btn-confirm-m {
    border: none;
    border-radius: 6px;
    padding: .42rem 1.2rem;
    font-size: .8rem;
    font-weight: 600;
    cursor: pointer;
    color: #fff;
    background: linear-gradient(135deg, #6571ff, #8b5cf6);
    display: flex;
    align-items: center;
    gap: 6px;
    transition: all .15s;
}

  .mbadge {
    background: rgba(255, 255, 255, .18);
    border: 1px solid rgba(255, 255, 255, .3);
    border-radius: 20px;
    font-size: .6875rem;
    padding: 2px 10px;
    font-weight: 500;
  }

  /* ── Stats strip ── */
  .stats {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 10px;
    margin-bottom: 18px;
  }

  .sc {
    background: var(--card-bg);
    border: 1px solid var(--card-border);
    border-radius: 8px;
    padding: 13px 15px;
    display: flex;
    align-items: center;
    gap: 12px;
    box-shadow: var(--card-shadow);
  }

  .sc-ico {
    width: 38px;
    height: 38px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
    flex-shrink: 0;
  }

  .btn-dispatch .dsub{font-size:.68rem;font-weight:400;opacity:.85;display:block;margin-top:1px;}

  .sc-num {
    font-size: 1.4rem;
    font-weight: 700;
    line-height: 1;
  }

  .sc-lbl {
    font-size: .7rem;
    color: var(--text-muted);
  }

  /* ── Workspace ── */
  .workspace {
    display: grid;
    grid-template-columns: 1fr 400px;
    gap: 16px;
    align-items: start;
  }

  /* ── Card shell ── */
  .card {
    background: var(--card-bg);
    border: 1px solid var(--card-border);
    border-radius: 8px;
    box-shadow: var(--card-shadow);
    overflow: hidden;
    margin-bottom: 16px;
  }

  .chdr {
    padding: 13px 16px;
    border-bottom: 1px solid var(--card-border);
    display: flex;
    align-items: center;
    gap: 9px;
  }

  .chdr-ico {
    width: 30px;
    height: 30px;
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: .85rem;
    flex-shrink: 0;
  }

  .chdr h6 {
    margin: 0;
    font-size: .8375rem;
    font-weight: 600;
    color: var(--text-heading);
  }

  .chdr .csub {
    font-size: .7rem;
    color: var(--text-muted);
    display: block;
  }

  .cbody {
    padding: 16px;
  }

  /* Form Elements */
  .form-label {
    display: block;
    margin-bottom: .35rem;
    font-size: .78rem;
    font-weight: 500;
    color: var(--text-heading);
  }

  .cbody .form-select {
    width: 100%;
    font-size: .82rem;
    height: 38px;
    border: 1px solid var(--border-color);
    border-radius: 6px;
    background: var(--input-bg);
    color: var(--text-primary);
    padding: .35rem .6rem;
    transition: border-color .15s, box-shadow .15s;
  }

  .cbody .form-select:focus {
    border-color: rgba(101,113,255,.5);
    box-shadow: 0 0 0 3px rgba(101,113,255,.12);
    outline: none;
  }

  .cbody .form-select:disabled {
    background: var(--surface-2);
    color: var(--text-muted);
    cursor: not-allowed;
  }

  .mb-3 { margin-bottom: 1rem; }
  [data-theme="dark"] .cbody .form-select option { background:#101e33; color:#c8d4e8; }

  /* ── Filter row ── */
  .filter-row {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
    margin-bottom: 12px;
  }

  .fsearch {
    position: relative;
    flex: 1;
    min-width: 160px;
  }

  .fsearch i {
    position: absolute;
    left: 9px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--text-muted);
    font-size: .82rem;
  }

  .fsearch input {
    padding-left: 30px;
    width: 100%;
  }

  .finput,
  .fselect {
    font-size: .78rem;
    border: 1px solid var(--border-color);
    border-radius: 6px;
    padding: .35rem .7rem;
    color: var(--text-primary);
    background: var(--input-bg);
    height: 34px;
    transition: border-color .15s, box-shadow .15s;
  }

  .finput:focus,
  .fselect:focus {
    border-color: rgba(101, 113, 255, .5);
    box-shadow: 0 0 0 3px rgba(101, 113, 255, .12);
    outline: none;
  }

  .finput::placeholder {
    color: var(--text-light);
  }

  [data-theme="dark"] .fselect option {
    background: #101e33;
    color: #c8d4e8;
  }

  /* ── Table ── */
  .tbl-wrap {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
  }

  .dtbl {
    width: 100%;
    border-collapse: collapse;
    min-width: 520px;
  }

  .dtbl thead tr {
    background: var(--surface-2);
  }

  .ms-row .ml {
    color: var(--text-muted);
    font-size: .67rem;
    text-transform: uppercase;
    letter-spacing: .05em;
}
.ms-row .mv {
    font-weight: 500;
    color: var(--text-heading);
    margin-top: 1px;
}

  .m-hdr h6 {
    margin: 0;
    font-size: .9rem;
    font-weight: 600;
    color: var(--text-heading);
}

  .dtbl thead th {
    font-size: .68rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .07em;
    color: var(--text-muted);
    padding: 9px 11px;
    border-bottom: 1px solid var(--card-border);
    white-space: nowrap;
  }

  .dtbl tbody tr {
    border-bottom: 1px solid var(--card-border);
    cursor: pointer;
    transition: background .15s;
  }

  .dtbl tbody tr:last-child {
    border-bottom: none;
  }

  .dtbl tbody tr:hover {
    background: rgba(101, 113, 255, .04);
  }

  .dtbl tbody tr.sel {
    background: rgba(101, 113, 255, .08);
    border-left: 3px solid #6571ff;
  }

  .dtbl tbody td {
    padding: 10px 11px;
    font-size: .78rem;
    vertical-align: middle;
  }

  [data-theme="dark"] .dtbl tbody tr:hover {
    background: rgba(101, 113, 255, .08);
  }

  .sr-link {
    color: #6571ff;
    font-weight: 700;
  }

  .chip {
    font-size: .62rem;
    font-weight: 700;
    padding: 2px 8px;
    border-radius: 10px;
    white-space: nowrap;
  }

  .chip-g { background: rgba(5, 163, 74, .1); color: #05a34a; }
  .chip-o { background: rgba(249, 115, 22, .1); color: #f97316; }
  .chip-b { background: rgba(101, 113, 255, .1); color: #6571ff; }
  .chip-r { background: rgba(255, 51, 102, .1); color: #ff3366; }
  .chip-y { background: rgba(251, 188, 6, .1); color: #a8802a; }
  .chip-gray { background: rgba(174, 183, 197, .12); color: #5a6a7e; }

  .sla-ok { color: #05a34a; font-weight: 600; }
  .sla-w { color: #a8802a; font-weight: 600; }
  .sla-c { color: #ff3366; font-weight: 600; }

  .tbl-foot {
    padding: 9px 13px;
    border-top: 1px solid var(--card-border);
    font-size: .72rem;
    color: var(--text-muted);
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 6px;
  }

  .pg-btn {
    background: var(--surface-2);
    border: 1px solid var(--border-color);
    border-radius: 5px;
    width: 26px;
    height: 26px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: .72rem;
    color: var(--text-muted);
    transition: all .15s;
  }

  .pg-btn:hover,
  .pg-btn.act {
    background: rgba(101, 113, 255, .1);
    border-color: #6571ff;
    color: #6571ff;
  }

  /* ── Right panel ── */
  .right-panel {
    display: flex;
    flex-direction: column;
    gap: 14px;
    position: sticky;
    top: 80px;
  }

  /* Ticket snapshot */
  .snap-empty {
    text-align: center;
    padding: 28px 12px;
    color: var(--text-muted);
    font-size: .8rem;
  }

  .snap-empty i {
    display: block;
    font-size: 1.8rem;
    margin-bottom: 8px;
    opacity: .28;
  }

  .tk-grid { display: flex; flex-direction: column; gap: 10px; }
  .tk-row { display: flex; justify-content: space-between; align-items: baseline; gap: 12px; }
  .tk-row .tk-l { margin-bottom: 0; }
  .tk-row .tk-v { text-align: right; }
  

  /* Assign summary */
  .assign-summary {
    background: var(--surface-2);
    border: 1px solid var(--border-color);
    border-radius: 7px;
    padding: 12px 14px;
    margin-bottom: 14px;
    display: none;
  }

  .assign-summary.show {
    display: block;
  }

  .as-row {
    display: flex;
    justify-content: space-between;
    align-items: baseline;
    margin-bottom: 5px;
    font-size: .78rem;
  }

  .as-row:last-child {
    margin-bottom: 0;
  }

  .as-k { color: var(--text-muted); }
  .as-v { font-weight: 600; color: var(--text-heading); }

  /* Dispatch button */
  .btn-dispatch {
    width: 100%;
    border: none;
    border-radius: 7px;
    padding: .65rem 1rem;
    font-size: .875rem;
    font-weight: 600;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    background: linear-gradient(135deg, #6571ff, #8b5cf6);
    color: #fff;
    transition: all .2s;
  }

  .btn-dispatch:hover:not(:disabled) {
    box-shadow: 0 4px 18px rgba(101, 113, 255, .38);
  }

  .btn-dispatch:active:not(:disabled) {
    transform: scale(.98);
  }

  .btn-dispatch:disabled {
    opacity: .45;
    cursor: not-allowed;
    background: var(--surface-2);
    color: var(--text-muted);
  }

  .notify-note {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: .7rem;
    color: var(--text-muted);
    margin-top: 8px;
    padding: 7px 10px;
    background: rgba(101, 113, 255, .06);
    border-radius: 6px;
    border: 1px solid rgba(101, 113, 255, .15);
  }

  .notify-note i {
    color: #6571ff;
    flex-shrink: 0;
  }

  /* ── Toast ── */
  .toast-wrap {
    position: fixed;
    top: 70px;
    right: 16px;
    z-index: 9999;
    display: flex;
    flex-direction: column;
    gap: 8px;
    max-width: 310px;
  }

  .toast-item {
    background: var(--card-bg);
    border-left: 4px solid #6571ff;
    border-radius: 7px;
    padding: 12px 14px;
    box-shadow: 0 6px 24px rgba(0, 0, 0, .18);
    display: flex;
    align-items: flex-start;
    gap: 10px;
    animation: toastIn .3s ease;
  }

  .toast-item.success { border-color: #05a34a; }
  .toast-item.error { border-color: #ff3366; }
  .toast-item.warning { border-color: #fbbc06; }

  @keyframes toastIn {
    from { transform: translateX(40px); opacity: 0; }
    to { transform: none; opacity: 1; }
  }

  .ti-ico { font-size: 1.1rem; flex-shrink: 0; margin-top: 1px; }
  .ti-ico.primary { color: #6571ff; }
  .ti-ico.success { color: #05a34a; }
  .ti-ico.error { color: #ff3366; }
  .ti-ico.warning { color: #fbbc06; }

  .ti-t { font-size: .8125rem; font-weight: 600; margin: 0 0 2px; color: var(--text-heading); }
  .ti-b { font-size: .72rem; margin: 0; color: var(--text-muted); }

  /* ── Success overlay ── */
  .success-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: var(--overlay-bg);
    z-index: 9000;
    align-items: center;
    justify-content: center;
    padding: 16px;
  }

  .success-overlay.show {
    display: flex;
  }

  .success-box {
    background: var(--card-bg);
    border-radius: 14px;
    border: 1px solid var(--card-border);
    max-width: 400px;
    width: 100%;
    box-shadow: var(--modal-shadow);
    animation: popIn .28s ease;
    padding: 32px 28px;
    text-align: center;
  }

  @keyframes popIn {
    from { transform: scale(.88); opacity: 0; }
    to { transform: none; opacity: 1; }
  }

  .ok-ring {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: rgba(5, 163, 74, .1);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.7rem;
    color: #05a34a;
    margin: 0 auto 14px;
    box-shadow: 0 0 0 8px rgba(5, 163, 74, .06);
  }

  .success-box h5 { font-size: 1.0625rem; font-weight: 700; color: var(--text-heading); margin: 0 0 6px; }
  .success-box p { font-size: .8rem; color: var(--text-muted); margin: 0 0 14px; }

  .sr-id-badge {
    background: rgba(101, 113, 255, .1);
    border: 1px solid rgba(101, 113, 255, .25);
    border-radius: 7px;
    padding: 9px 16px;
    font-size: .9375rem;
    font-weight: 700;
    color: #6571ff;
    display: inline-block;
    letter-spacing: .04em;
    margin-bottom: 14px;
  }

  .btn-ok {
    background: #6571ff;
    color: #fff;
    border: none;
    border-radius: 7px;
    padding: .55rem 1.8rem;
    font-size: .875rem;
    font-weight: 600;
    cursor: pointer;
    transition: background .15s;
  }

  .btn-ok:hover { background: #5660d9; }


  /* ══════════════════════════════════════════════════
     RESPONSIVE BREAKPOINTS (Mobile & Tablet Fixes)
  ══════════════════════════════════════════════════ */

  /* ── Desktop & Large Tablet Breakpoint ── */
  @media(max-width: 1199.98px) {
    .workspace { grid-template-columns: 1fr 360px; }
  }

  /* ── Tablet View ── */
  @media (max-width: 991.98px) {
    .workspace { grid-template-columns: 1fr; gap: 14px; }
    .right-panel { margin-top: 16px; position: static; }
  }

  /* ── Phone Layout Stack ── */
  @media (max-width: 767.98px) {
    .stats { grid-template-columns: repeat(2, 1fr); }
    .filter-row { gap: 6px; }
    .fsearch { flex: 1 1 100%; }
    .fselect { flex: 1 1 calc(50% - 4px); width: auto !important; }
    #filterLbl { flex: 1 1 100%; }

    /* Convert standard HTML table to layout cards safely */
    .tbl-wrap { overflow-x: visible; }
    .dtbl, .dtbl tbody, .dtbl tr, .dtbl td { display: block; width: 100%; }
    .dtbl { min-width: 0; }
    .dtbl thead { display: none; }

    .dtbl tbody tr {
      border: 1px solid var(--card-border);
      border-radius: 8px;
      margin-bottom: 10px;
      padding: 10px 14px;
    }
    .dtbl tbody tr.sel { border-left: 3px solid #6571ff; background: rgba(101,113,255,.06); }

    .dtbl tbody td {
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 10px;
      padding: 6px 0;
      border: none;
      font-size: .8rem;
    }
    .dtbl tbody td::before {
      content: attr(data-label);
      font-size: .64rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: .05em;
      color: var(--text-muted);
      flex: 0 0 auto;
    }
    .dtbl tbody td:first-child {
      justify-content: flex-start;
      padding-bottom: 8px;
      margin-bottom: 4px;
      border-bottom: 1px dashed var(--card-border);
    }
    .dtbl tbody td:first-child::before { content: ''; }
    .dtbl tbody td > div, .dtbl tbody td > span { text-align: right; min-width: 0; }
  }

  /* ── Small Devices & Mobile Breakpoint ── */
  @media (max-width: 575.98px) {
    .pg-hdr { padding: 14px 16px; }
    .pg-hdr h4 { font-size: .9rem; }
    .toast-wrap { left: 12px; right: 12px; max-width: none; }
  }

  @media (max-width: 480px) {
    .stats { grid-template-columns: 1fr; gap: 8px; }
    .pg-hdr { padding: 14px 15px; }
    .pg-hdr h4 { font-size: .92rem; }
    .pg-hdr .mrow { gap: 6px; }
    .mbadge { font-size: .64rem; padding: 2px 8px; }

    .fselect { flex: 1 1 100%; }
    .cbody { padding: 14px; }
    .success-box { padding: 26px 20px; }
    .tk-grid { grid-template-columns: 1fr; }

    .btn-dispatch { padding: .75rem 1rem; font-size: .9rem; }
    .dtbl tbody td { font-size: .74rem; }
  }
</style>
@endpush

@section('content')

{{-- ── Toast shelf ── --}}
<div class="toast-wrap" id="toastWrap"></div>

{{-- ── Success overlay ── --}}
<!-- <div class="success-overlay" id="successOverlay">
  <div class="success-box">
    <div class="ok-ring"><i class="bi bi-check-lg"></i></div>
    <h5>Dispatched Successfully!</h5>
    <p>The ticket has been assigned and the technician will be notified immediately.</p>
    <div class="sr-id-badge" id="successSrId">SR-2024-0000</div>
    <p id="successDetail" style="font-size:.78rem;color:var(--text-muted);margin-bottom:20px;">—</p>
    <button class="btn-ok" onclick="closeSuccess()">Done</button>
  </div>
</div> -->


<div class="m-overlay" id="dispatchModal">
  <div class="m-box">
    <div class="m-hdr">
      <h6>Confirm Dispatch Assignment</h6>
      <button class="m-close" onclick="closeModal()"><i class="bi bi-x-lg"></i></button>
    </div>
    <div class="m-body">
      <div class="m-icon-ring" style="background:rgba(101,113,255,.1);"><i class="bi bi-send-fill" style="color:#6571ff;"></i></div>
      <div class="m-title">Confirm Technician Dispatch</div>
      <div class="m-sub">The following assignment will be committed. Ticket status will shift to <strong>Assigned</strong> and a push notification will be sent to the technician's mobile app.</div>
      <div class="m-summary" id="modalSummary"></div>
      <div class="push-note">
        <i class="bi bi-bell-fill"></i>
        <span>A real-time push notification will be fired to <strong id="notifTechName">—</strong>'s mobile application immediately upon confirmation.</span>
      </div>
    </div>
    <div class="m-ftr">
      <button class="btn-cancel-m" onclick="closeModal()">Cancel</button>
      <button class="btn-confirm-m" id="confirmBtn" onclick="executeDispatch()">
        <i class="bi bi-send-fill"></i>Confirm &amp; Dispatch
      </button>
    </div>
  </div>
</div>



{{-- ── PAGE HEADER ── --}}
<div class="pg-hdr">
  <h4><i class="bi bi-person-gear me-2"></i>Dispatch Engine — Assign Technicians</h4>
  <p>Select an approved ticket, pick a technician, and dispatch in one click.</p>
  <div class="mrow">
    <span class="mbadge"><i class="bi bi-briefcase me-1"></i>Head of Projects</span>
    <span class="mbadge"><i class="bi bi-person-gear me-1"></i>Admin</span>
    <span class="mbadge"><i class="bi bi-shield-fill-check me-1"></i>Super Admin</span>
  </div>
</div>

{{-- ── STATS STRIP ── --}}
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

{{-- ── WORKSPACE ── --}}
<div class="workspace">

  {{-- LEFT: Approved tickets table --}}
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
            <input type="text" class="finput" id="srSearch" placeholder="Search SR, client, site…" oninput="applyFilter()" />
          </div>
          <select class="fselect" style="width:150px;" id="domainFilter" onchange="applyFilter()">
            <option value="">All Categories</option>
            @foreach($categories as $cat)
              <option value="{{ $cat->category_name }}">{{ $cat->category_name }}</option>
            @endforeach
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

      {{-- Table view wrapper --}}
      <div class="tbl-wrap">
        <table class="dtbl">
          <thead>
            <tr>
              <th></th>
              <th>SR_ID</th>
              <th>Client</th>
              <th>Category</th>
              <th>Site</th>
              <th>Priority</th>
              <th style="text-align:center;"><i class="bi bi-clock" style="font-size:.85rem;"></i></th>
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

  {{-- RIGHT: Assignment panel --}}
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
        <div class="chdr-ico" style="background:rgba(5,163,74,.1);flex:0 0 auto;">
          <i class="bi bi-people-fill" style="color:#05a34a;"></i>
        </div>
        <div style="flex:1;min-width:0;">
          <h6 style="margin:0;display:flex;align-items:center;">
            <span style="display:inline-flex;align-items:center;justify-content:center;width:18px;height:18px;background:#05a34a;border-radius:50%;font-size:.65rem;font-weight:700;color:#fff;margin-right:6px;flex:0 0 auto;">2</span>
            Assign Technician
          </h6>
          <span class="csub">Matched by service category &amp; domain</span>
        </div>
      </div>

      <div class="cbody">
        <div class="mb-3">
          <label class="form-label">Service Category</label>
          <select class="form-select" id="dispCategory" onchange="onDispCategory(this.value)">
            <option value="">— Select Category —</option>
            @foreach($categories as $cat)
              <option value="{{ $cat->id }}">{{ $cat->category_name }}</option>
            @endforeach
          </select>
        </div>

        <div class="mb-3">
          <label class="form-label">Service Domain</label>
          <select class="form-select" id="dispDomain" disabled onchange="onDispDomain(this.value)">
            <option value="">— Select Domain First —</option>
          </select>
        </div>

        <div class="mb-3">
          <label class="form-label">Assign To (Technician)</label>
          <select class="form-select" id="dispTech" disabled onchange="onDispTech(this.value)">
            <option value="">— Select Technician First —</option>
          </select>
        </div>

        <div class="tech-hint" id="techHint" style="font-size:.75rem;color:var(--text-muted);display:flex;align-items:center;gap:6px;margin-top:2px;">
          <i class="bi bi-arrow-up-circle"></i>
          <span>Select a ticket above, then pick a category, domain and technician.</span>
        </div>
      </div>
    </div>

    {{-- Step 3 · Dispatch Summary --}}
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
          <div class="as-row"><span class="as-k">Category</span><span class="as-v" id="asDomain">—</span></div>
          <div class="as-row"><span class="as-k">Technician</span><span class="as-v" id="asTech">—</span></div>
          <div class="as-row" style="margin-bottom:0;"><span class="as-k">Priority</span><span class="as-v" id="asPriority">—</span></div>
        </div>
        <button class="btn-dispatch" id="dispatchBtn" onclick="openDispatchModal()" disabled>
        <i class="bi bi-send-fill" style="font-size:1rem;"></i>
             <div>
              Confirm Assignment Dispatch
              <span class="dsub" id="dispatchBtnSub">Select ticket and technician</span>
            </div>
        </button>
        <div class="notify-note" style="margin-top:10px;">
          <i class="bi bi-bell-fill"></i>
<span>Upon confirmation, a <strong>push notification</strong> is fired to the technician's mobile pipeline app and ticket status shifts to <strong>Assigned</strong>.</span>        </div>
      </div>
    </div>

  </div>{{-- /right-panel --}}
</div>{{-- /workspace --}}

@endsection

@push('scripts')
<script>
  /* Real engine data mappings */
  const ALL_TK = @json($tickets);

  const DOMAIN_MAP = {
    @foreach(($categories ?? []) as $cat)
      "{{ $cat->id }}": {
        name: @json($cat->category_name),
        domains: [
          @foreach($cat->domains as $d)
            { id: {{ $d->id }}, name: @json($d->domain_name) },
          @endforeach
        ]
      },
    @endforeach
  };

  const TECHS = @json($technicians ?? []);

  let tickets = [...ALL_TK];
  let filtered = [...tickets];
  let selTkId = null;
  let selTechId = null;
  let dispatchedToday = 0;
  let currentPage = 1;
  const PER = 7;

  function updateStats() {
    document.getElementById('stat-approved').textContent   = tickets.length;
    document.getElementById('stat-dispatched').textContent = dispatchedToday;
    document.getElementById('stat-avail').textContent      = new Set(TECHS.map(t => t.id)).size;
    document.getElementById('stat-overdue').textContent    = tickets.filter(t => t.hrsAgo > 24).length;
  }

  function applyFilter() {
    const q   = document.getElementById('srSearch').value.toLowerCase();
    const dom = document.getElementById('domainFilter').value;
    const sla = document.getElementById('slaFilter').value;
    filtered = tickets.filter(t => {
      const mq = !q || (t.id.toLowerCase().includes(q) || (t.client||'').toLowerCase().includes(q) || (t.site||'').toLowerCase().includes(q));
      const md = !dom || t.domain === dom;
      const ms = !sla || (sla==='ok' && t.hrsAgo<=8) || (sla==='warn' && t.hrsAgo>8 && t.hrsAgo<=24) || (sla==='crit' && t.hrsAgo>24);
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
    document.getElementById('tblLbl').textContent = filtered.length
      ? `Showing ${start+1}–${Math.min(start+PER,filtered.length)} of ${filtered.length}`
      : 'No records';

    if (!filtered.length) { body.innerHTML=''; empty.style.display='block'; renderPager(); return; }
    empty.style.display = 'none';

    const pcls = { High:'chip-r', Medium:'chip-y', Low:'chip-g' };

    body.innerHTML = page.map(t => {
      const slaCls = t.hrsAgo>24 ? 'sla-c' : t.hrsAgo>8 ? 'sla-w' : 'sla-ok';
      const slaIco = t.hrsAgo>24 ? 'bi-exclamation-triangle-fill' : t.hrsAgo>8 ? 'bi-clock-history' : 'bi-check-circle';
      const sel = t.id === selTkId;
      const siteShort = (t.site||'').split(' — ')[0];
      return `<tr class="${sel?'sel':''}" onclick="selectTicket('${t.id}')">
        <td><input type="radio" ${sel?'checked':''} onclick="event.stopPropagation();selectTicket('${t.id}')" style="accent-color:#6571ff;"/></td>
        <td data-label="SR ID"><span class="sr-link">${t.id}</span></td>
        <td data-label="Client">
          <div style="font-weight:500;color:var(--text-heading);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">${t.client}</div>
          <div style="font-size:.67rem;color:var(--text-muted);">${t.contract || ''}</div>
        </td>
        <td data-label="Category"><span class="chip chip-gray">${t.domain}</span></td>
        <td data-label="Site" style="color:var(--text-muted);">${siteShort}</td>
        <td data-label="Priority"><span class="chip ${pcls[t.priority]||'chip-gray'}">${t.priority}</span></td>
        <td data-label="SLA"><span class="${slaCls}" style="font-size:.75rem;display:flex;align-items:center;gap:4px;"><i class="bi ${slaIco}"></i>${t.hrsAgo}h</span></td>
        <td data-label="Approved" style="font-size:.7rem;color:var(--text-muted);white-space:nowrap;">${t.approvedStr}</td>
      </tr>`;
    }).join('');
    renderPager();
  }

  function renderPager() {
    const total = Math.ceil(filtered.length / PER);
    const cont = document.getElementById('pagerBtns');
    if (total <= 1) { cont.innerHTML=''; return; }
    cont.innerHTML = Array.from({length: total}, (_, i) =>
      `<div class="pg-btn ${i+1===currentPage?'act':''}" onclick="goPg(${i+1})">${i+1}</div>`
    ).join('');
  }
  
  function goPg(p){ currentPage=p; renderTable(); }

  function selectTicket(id) {
    selTkId = id;
    selTechId = null;
    renderTable();
    const t = tickets.find(x => x.id === id);
    renderSnapshot(t);
    syncCategoryDropdown(t);
    updateDispatchBtn();
    if (window.innerWidth < 992) {
      setTimeout(() => document.getElementById('rightPanel').scrollIntoView({behavior:'smooth',block:'start'}), 120);
    }
  }

function renderSnapshot(t) {
  const slaCls = t.hrsAgo>24 ? '#ff3366' : t.hrsAgo>8 ? '#fbbc06' : '#05a34a';
  const pcls = { High:'chip-r', Medium:'chip-y', Low:'chip-g' };
  document.getElementById('snapBody').innerHTML = `
  <div class="tk-grid">
    <div class="tk-row"><div class="tk-l">SR_ID</div><div class="tk-v" style="color:#6571ff;font-weight:700;">${t.id}</div></div>
    <div class="tk-row"><div class="tk-l">Priority</div><div class="tk-v"><span class="chip ${pcls[t.priority]||'chip-gray'}">${t.priority}</span></div></div>
    <div class="tk-row full"><div class="tk-l">Client</div><div class="tk-v">${t.client}</div></div>
    <div class="tk-row full"><div class="tk-l">Project</div><div class="tk-v">${t.contract || '—'}</div></div>
    <div class="tk-row"><div class="tk-l">Category</div><div class="tk-v">${t.domain}</div></div>
    <div class="tk-row"><div class="tk-l">SLA Elapsed</div><div class="tk-v" style="color:${slaCls};font-weight:700;">${t.hrsAgo}h ago</div></div>
    <div class="tk-row full"><div class="tk-l">Site</div><div class="tk-v">${t.site}</div></div>
    <div class="tk-row full"><div class="tk-l">Approved At</div><div class="tk-v">${t.approvedStr}</div></div>
  </div>`;
}

  function syncCategoryDropdown(t) {
    const catSel = document.getElementById('dispCategory');
    if (!catSel) return;
    if (t && t.service_type_id != null) catSel.value = String(t.service_type_id);
    onDispCategory(catSel.value);
  }

  function onDispCategory(catId) {
    const dd = document.getElementById('dispDomain');
    const entry = DOMAIN_MAP[catId];
    const domains = entry ? entry.domains : [];
    dd.innerHTML = '<option value="">— All domains —</option>';
    domains.forEach(d => {
      const o = document.createElement('option');
      o.value = d.id; o.textContent = d.name;
      dd.appendChild(o);
    });
    dd.disabled = domains.length === 0;
    loadTechs(catId, '');
  }

  function onDispDomain(domainId) {
    const catId = document.getElementById('dispCategory').value;
    loadTechs(catId, domainId);
  }

  function loadTechs(catId, domainId) {
    const ts = document.getElementById('dispTech');
    selTechId = null;

    let techs = TECHS.filter(t => String(t.category_id) === String(catId));
    if (domainId) techs = techs.filter(t => String(t.domain_id) === String(domainId));

    const seen = new Set();
    techs = techs.filter(t => (seen.has(t.id) ? false : seen.add(t.id)));

    if (!catId) {
      ts.innerHTML = '<option value="">— Select Category First —</option>';
      ts.disabled = true;
    } else if (!techs.length) {
      ts.innerHTML = '<option value="">No Technicians for this category</option>';
      ts.disabled = true;
    } else {
      ts.innerHTML = '<option value="">— Select Technician —</option>';
      techs.forEach(t => {
        const code = 'ML-' + String(t.id).padStart(3, '0');
        const o = document.createElement('option');
        o.value = t.id;
        o.textContent = `${t.name} (${code})`;
        ts.appendChild(o);
      });
      ts.disabled = false;
    }
    updateDispatchBtn();
  }

  function onDispTech(userId) {
    selTechId = userId || null;
    updateDispatchBtn();
  }

  function updateDispatchBtn() {
  const btn = document.getElementById('dispatchBtn');
  const sub = document.getElementById('dispatchBtnSub');
  const summary = document.getElementById('assignSummary');
  const ready = selTkId && selTechId;
  btn.disabled = !ready;

  // sub-label guidance
  if (sub) {
    if (!selTkId) {
      sub.textContent = 'Select a ticket first';
    } else if (!selTechId) {
      sub.textContent = 'Now select a technician';
    } else {
      const tech = TECHS.find(x => String(x.id) === String(selTechId));
      const code = tech ? 'ML-' + String(tech.id).padStart(3, '0') : '';
      sub.textContent = tech ? `→ ${tech.name} · ${code}` : '';
    }
  }

  // assignment summary
  if (ready) {
    const t = tickets.find(x => x.id === selTkId);
    const tech = TECHS.find(x => String(x.id) === String(selTechId));
    const code = tech ? 'ML-' + String(tech.id).padStart(3, '0') : '';
    summary.classList.add('show');
    document.getElementById('asSrId').textContent     = t.id;
    document.getElementById('asClient').textContent   = t.client;
    document.getElementById('asDomain').textContent   = t.domain;
    document.getElementById('asTech').textContent     = tech ? `${tech.name} (${code})` : '';
    document.getElementById('asPriority').textContent = t.priority;
  } else {
    summary.classList.remove('show');
  }
}

  /* ════════════════════════════════════
   DISPATCH MODAL
════════════════════════════════════ */
function openDispatchModal(){
  if(!selTkId||!selTechId)return;
  const t=tickets.find(x=>x.id===selTkId);
  const tech=TECHS.find(x=>String(x.id)===String(selTechId));
  if(!tech){ showToast('error','No technician','Please select a technician first.'); return; }

  const catSel = document.getElementById('dispCategory');
  const domSel = document.getElementById('dispDomain');
  const categoryName = catSel.value ? catSel.options[catSel.selectedIndex].text : (t.domain || '—');
  const domainName   = domSel.value ? domSel.options[domSel.selectedIndex].text : '—';

  const code='ML-'+String(tech.id).padStart(3,'0');
  const initials=(tech.name||'?').split(' ').map(s=>s[0]).join('').slice(0,2).toUpperCase();

  document.getElementById('notifTechName').textContent=tech.name;
  document.getElementById('modalSummary').innerHTML=`
    <div class="ms-row"><div class="ml">SR_ID</div><div class="mv" style="color:#6571ff;font-weight:700;">${t.id}</div></div>
    <div class="ms-row"><div class="ml">Priority</div><div class="mv">${t.priority}</div></div>
    <div class="ms-row full"><div class="ml">Client</div><div class="mv">${t.client}</div></div>
    <div class="ms-row full"><div class="ml">Site</div><div class="mv">${t.site}</div></div>
    <div class="ms-row"><div class="ml">Category</div><div class="mv">${categoryName}</div></div>
    <div class="ms-row"><div class="ml">Domain</div><div class="mv">${domainName}</div></div>
    <div class="ms-row"><div class="ml">SLA Elapsed</div><div class="mv" style="color:${t.hrsAgo>24?'#ff3366':t.hrsAgo>8?'#fbbc06':'#05a34a'}">${t.hrsAgo}h</div></div>
    <div class="ms-row full" style="border-top:1px solid var(--border-color);padding-top:8px;margin-top:4px;">
      <div class="ml">Assigned Technician</div>
      <div class="mv" style="display:flex;align-items:center;gap:7px;margin-top:3px;">
        <div style="width:24px;height:24px;border-radius:50%;background:#6571ff;display:flex;align-items:center;justify-content:center;color:#fff;font-size:.6rem;font-weight:700;flex-shrink:0;">${initials}</div>
        ${tech.name} · ${code}
      </div>
    </div>`;
  document.getElementById('dispatchModal').classList.add('show');
}
function closeModal(){document.getElementById('dispatchModal').classList.remove('show');}
document.getElementById('dispatchModal').addEventListener('click',function(e){if(e.target===this)closeModal();});

function executeDispatch(){
  const btn=document.getElementById('confirmBtn');
  const t=tickets.find(x=>x.id===selTkId);
  const tech=TECHS.find(x=>String(x.id)===String(selTechId));
  if(!t||!tech){ showToast('error','Error','Missing ticket or technician.'); return; }

  const categorySelect = document.getElementById('dispCategory');
const domainSelect   = document.getElementById('dispDomain');

const categoryName = categorySelect.value ? categorySelect.options[categorySelect.selectedIndex].text : '-';
const domainName   = domainSelect.value   ? domainSelect.options[domainSelect.selectedIndex].text   : '-';
const domainId     = domainSelect.value || null;

  btn.disabled=true;
  btn.innerHTML='<span class="spinner-border" style="width:13px;height:13px;border-width:2px;"></span> Dispatching…';

  fetch(`{{ url('service-requests') }}/${t.dbId}/dispatch`, {
    method:'POST',
    headers:{
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
      'Accept':'application/json',
      'Content-Type':'application/json'
    },
    body: JSON.stringify({
      assigned_user_id: tech.id,
      service_domain_id: domainId
    })
  })
  .then(r=>r.json().then(j=>({status:r.status,j})))
  .then(({status,j})=>{
    if(status>=200 && status<300 && j.ok){
      const code='ML-'+String(tech.id).padStart(3,'0');

      // toast in the requested style
      showToast(
    'success',
    'Dispatch Successful!',
    `
    SR: ${t.id}
    Technician: ${tech.name}
    Category: ${categoryName}
    Domain: ${domainName}
    Status: Assigned
    `
);

      tickets=tickets.filter(x=>x.id!==selTkId);
      dispatchedToday++;

      closeModal();
      selTkId=null; selTechId=null;
      filtered=[...tickets];
      applyFilter();
      updateStats();

      document.getElementById('snapBody').innerHTML='<div class="snap-empty"><i class="bi bi-hand-index-thumb"></i>Click a ticket row to start dispatch</div>';

      const domSel=document.getElementById('dispDomain'), techSel=document.getElementById('dispTech'), catSel=document.getElementById('dispCategory');
      if(catSel) catSel.value='';
      if(domSel){ domSel.innerHTML='<option value="">— Select Domain First —</option>'; domSel.disabled=true; }
      if(techSel){ techSel.innerHTML='<option value="">— Select Technician First —</option>'; techSel.disabled=true; }
      updateDispatchBtn();
    } else {
      showToast('error','Dispatch failed', j.message || 'Something went wrong.');
    }
  })
  .catch(err=>{ showToast('error','Error', err.message || 'Request failed.'); })
  .finally(()=>{
    btn.disabled=false;
    btn.innerHTML='<i class="bi bi-send-fill"></i>Confirm & Dispatch';
  });
}


  function showToast(type, title, body) {
    const w = document.getElementById('toastWrap');
    const ico = { success:'bi-check-circle-fill', error:'bi-x-circle-fill', primary:'bi-info-circle-fill', warning:'bi-exclamation-circle-fill' };
    const t = document.createElement('div');
    t.className = `toast-item ${type}`;
    t.innerHTML = `<i class="bi ${ico[type]||'bi-info-circle-fill'} ti-ico ${type}"></i><div><p class="ti-t">${title}</p><p class="ti-b">${body}</p></div>`;
    w.appendChild(t);
    setTimeout(() => { t.style.opacity='0'; t.style.transition='opacity .3s'; setTimeout(()=>t.remove(),300); }, 4000);
  }

  // Engine Init
  updateStats();
  applyFilter();
</script>
@endpush