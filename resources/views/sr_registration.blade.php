@extends('layouts.layout')

@section('title', 'SR Registration — Digit-Us Portal')
@section('page_title', 'SR Registration')
@section('page_icon', 'building-add')

@push('styles')
<style>
  /* ── Page header ─────────────────────────── */
  .sr-header {
    background:linear-gradient(135deg,#9A7B4F 0%,#7A6140 100%);border-radius:10px;padding:20px 24px;margin-bottom:20px;color:#fff;position:relative;overflow:hidden;
  }

  .sr-header::before,
  .sr-header::after {
    content: '';
    position: absolute;
    border-radius: 50%;
    pointer-events: none;
  }

  .sr-header::before {
    width: 200px;
    height: 200px;
    background: rgba(255, 255, 255, .06);
    left: -50px;
    bottom: -50px;
  }

  .sr-header::after {
    width: 160px;
    height: 160px;
    background: rgba(255, 255, 255, .08);
    right: -30px;
    top: -30px;
  }

  .sr-header-inner {
    position: relative;
    z-index: 1;
  }

  .sr-header h4 {
    font-size: 1.0625rem;
    font-weight: 700;
    margin: 0 0 5px;
    letter-spacing: -.01em;
  }

  .sr-header p {
    font-size: .8rem;
    margin: 0;
    opacity: .88;
    line-height: 1.6;
  }

  .sr-header-tags {
    display: flex;
    gap: 6px;
    margin-top: 12px;
    flex-wrap: wrap;
  }

  .sr-htag {
    background: rgba(255, 255, 255, .17);
    border: 1px solid rgba(255, 255, 255, .28);
    border-radius: 20px;
    font-size: .6875rem;
    padding: 3px 10px;
    font-weight: 500;
  }

  @media(max-width:575.98px) {
    .sr-header {
      padding: 18px 16px;
    }

    .sr-header h4 {
      font-size: .9375rem;
    }
  }

  /* ── Status bar ──────────────────────────── */
  .sr-alert {
    display: flex;
    align-items: center;
    gap: 9px;
    border-radius: 8px;
    padding: 10px 14px;
    font-size: .8rem;
    margin-bottom: 20px;
    border: 1.5px solid transparent;
    transition: all .3s;
  }

  /* ── Cards ───────────────────────────────── */
  .fc {
    background: var(--card-bg);
    border: 1px solid var(--card-border);
    border-radius: 12px;
    box-shadow: 0 1px 12px rgba(100, 120, 160, .08);
    overflow: hidden;
    margin-bottom: 18px;
    transition: background .3s, border-color .3s;
  }

  .fc-head {
    display: flex;
    align-items: center;
    gap: 11px;
    padding: 14px 20px;
    border-bottom: 1px solid var(--card-border);
  }

  .fc-icon {
    width: 34px;
    height: 34px;
    border-radius: 8px;
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: .9rem;
  }

  .fc-step {
    width: 20px;
    height: 20px;
    border-radius: 50%;
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: .65rem;
    font-weight: 700;
    color: #fff;
    margin-left: auto;
  }

  .fc-head h6 {
    font-size: .875rem;
    font-weight: 600;
    color: var(--text-heading);
    margin: 0;
  }

  .fc-head .fc-sub {
    font-size: .71rem;
    color: var(--text-muted);
    margin-top: 1px;
    display: block;
  }

  .fc-body {
    padding: 20px;
  }

  @media(max-width:575.98px) {
    .fc-head {
      padding: 12px 14px;
    }

    .fc-body {
      padding: 14px;
    }
  }

  /* ── Form controls ───────────────────────── */
  .form-label {
    font-size: .8rem;
    font-weight: 500;
    color: var(--nav-link);
    margin-bottom: 5px;
    display: block;
  }

  .req {
    color: #ff3366;
    margin-left: 2px;
  }

  .opt {
    font-size: .7rem;
    color: var(--text-muted);
    font-weight: 400;
    margin-left: 4px;
  }

  .form-control,
  .form-select {
    font-size: .8125rem;
    border: 1.5px solid var(--border-color);
    border-radius: 7px;
    padding: .48rem .85rem;
    color: var(--text-primary);
    background: var(--input-bg);
    width: 100%;
    transition: border-color .18s, box-shadow .18s, background .3s;
    line-height: 1.5;
  }

  .form-control:focus,
  .form-select:focus {
    border-color: #9A7B4F;
    box-shadow: 0 0 0 3px rgba(154, 123, 79, .13);
    outline: none;
  }

  .form-control::placeholder {
    color: var(--text-light);
  }

  .form-control:disabled,
  .form-select:disabled {
    background: var(--surface-2);
    opacity: .5;
    cursor: not-allowed;
  }

  .form-control[readonly] {
    background: var(--surface-2);
    cursor: default;
  }

  textarea.form-control {
    resize: vertical;
    min-height: 108px;
  }

  .form-hint {
    font-size: .71rem;
    color: var(--text-muted);
    margin-top: 4px;
    display: flex;
    align-items: center;
    gap: 4px;
  }

  /* Icon-prefixed inputs */
  .iw {
    position: relative;
  }

  .iw .ii {
    position: absolute;
    left: 10px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--text-muted);
    font-size: .85rem;
    pointer-events: none;
  }

  .iw .form-control,
  .iw .form-select {
    padding-left: 32px;
  }

  /* Lookup state */
  .lk-wrap {
    position: relative;
  }

  .lk-wrap .form-control {
    padding-left: 32px;
    padding-right: 36px;
  }

  .lk-spin {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    display: none;
  }

  .lk-spin .spinner-border {
    width: 15px;
    height: 15px;
    border-width: 2px;
    color: #9A7B4F;
  }

  .lk-ok {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    color: #05a34a;
    font-size: .95rem;
    display: none;
  }

  .lk-wrap.verifying .lk-spin {
    display: block;
  }

  .lk-wrap.verified .lk-ok {
    display: block;
  }

  .lk-wrap.verified .form-control {
    border-color: #05a34a;
    background: rgba(5, 163, 74, .03);
  }

  /* Client info reveal */
  .client-reveal {
    display: none;
    margin-top: 10px;
    background: rgba(5, 163, 74, .06);
    border: 1.5px solid rgba(5, 163, 74, .2);
    border-radius: 8px;
    padding: 12px 14px;
    animation: fadeUp .22s ease;
  }

  .client-reveal.show {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
    gap: 10px;
  }

  @keyframes fadeUp {
    from {
      opacity: 0;
      transform: translateY(5px);
    }

    to {
      opacity: 1;
      transform: none;
    }
  }

  .ci-lbl {
    font-size: .65rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .05em;
    color: var(--text-muted);
    display: block;
    margin-bottom: 2px;
  }

  .ci-val {
    font-size: .8rem;
    font-weight: 600;
    color: var(--text-heading);
  }

  /* Priority pills */
  .priority-row {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
  }

  .pr-pill.on {
    border-color: #9A7B4F;
    color: #9A7B4F;
    background: rgba(154, 123, 79, .05);
}

  .pr-pill {
    flex: 1;
    min-width: 72px;
    text-align: center;
    border: 1.5px solid var(--border-color);
    border-radius: 8px;
    cursor: pointer;
    font-size: .78rem;
    font-weight: 500;
    color: var(--text-muted);
    background: var(--input-bg);
    transition: all .18s;
    user-select: none;
  }

  .pr-pill .pi {
    font-size: 30px;
    display: block;
  }

  .pr-pill:hover {
    border-color: #9A7B4F;
    color: #9A7B4F;
    background: rgba(154, 123, 79, .05);
  }

  .pr-pill.sel-low.on {
    border-color: #05a34a;
    color: #05a34a;
    background: rgba(5, 163, 74, .08);
    box-shadow: 0 0 0 3px rgba(5, 163, 74, .1);
  }

  .pr-pill.sel-med.on {
    border-color: #fbbc06;
    color: #b88b00;
    background: rgba(251, 188, 6, .08);
    box-shadow: 0 0 0 3px rgba(251, 188, 6, .1);
  }

  .pr-pill.sel-high.on {
    border-color: #ff6f3c;
    color: #ff6f3c;
    background: rgba(255, 111, 60, .08);
    box-shadow: 0 0 0 3px rgba(255, 111, 60, .1);
  }

  .pr-pill.sel-crit.on {
    border-color: #ff3366;
    color: #ff3366;
    background: rgba(255, 51, 102, .08);
    box-shadow: 0 0 0 3px rgba(255, 51, 102, .1);
  }

  @media(max-width:420px) {
    .pr-pill {
      min-width: 60px;
      font-size: .72rem;
    }

    .pr-pill .pi {
      font-size: .9rem;
    }
  }

  /* Char counter */
  .cc {
    font-size: .7rem;
    font-weight: 600;
  }

  .cc.ok {
    color: #05a34a;
  }

  .cc.warn {
    color: #ff3366;
  }

  /* Dropzone */
  .dz {
    border: 2px dashed var(--dz-border);
    border-radius: 9px;
    padding: 24px 14px;
    text-align: center;
    cursor: pointer;
    background: var(--dz-bg);
    position: relative;
    transition: border-color .2s, background .2s;
    box-sizing: border-box;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    min-height: 140px;
  }

  .dz:hover,
  .dz.over {
    border-color: #9A7B4F;
    background: var(--dz-hover);
  }

  .dz input {
    position: absolute;
    inset: 0;
    opacity: 0;
    cursor: pointer;
    width: 100%;
    height: 100%;
  }

  .dz-ic {
    font-size: 1.8rem;
    color: #9A7B4F;
    margin-bottom: 6px;
    line-height: 1;
  }

  .dz-txt {
    font-size: .79rem;
    color: var(--nav-link);
    margin: 0;
  }

  .dz-hint {
    font-size: .69rem;
    color: var(--text-muted);
    margin-top: 4px;
  }

  /* File list */
  .file-list {
    margin-top: 10px;
  }

  .fitem {
    display: flex;
    align-items: center;
    gap: 8px;
    background: var(--file-bg);
    border: 1px solid var(--file-border);
    border-radius: 7px;
    padding: 7px 10px;
    margin-bottom: 6px;
    font-size: .79rem;
    animation: fadeUp .2s ease;
  }

  .fitem:hover {
    border-color: var(--text-muted);
  }

  .fi-ic {
    font-size: 1.05rem;
    flex-shrink: 0;
  }

  .fi-name {
    flex: 1;
    color: var(--text-heading);
    font-weight: 500;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
  }

  .fi-sz {
    color: var(--text-muted);
    font-size: .69rem;
    flex-shrink: 0;
  }

  .fi-rm {
    background: none;
    border: none;
    color: var(--text-light);
    cursor: pointer;
    font-size: .8rem;
    padding: 0 2px;
    flex-shrink: 0;
  }

  .fi-rm:hover {
    color: #ff3366;
  }

  .ftype-chips {
    display: flex;
    gap: 6px;
    margin-top: 10px;
    align-items: center;
    flex-wrap: wrap;
  }

  .ftc {
    padding: 2px 8px;
    border-radius: 4px;
    font-size: .68rem;
    font-weight: 700;
    letter-spacing: .03em;
  }

  /* ── Buttons ─────────────────────────────── */
  .btn-main {
    background: linear-gradient(135deg, #9A7B4F, #C4A882);
    color: #fff;
    border: none;
    border-radius: 8px;
    padding: .55rem 1.5rem;
    font-size: .875rem;
    font-weight: 600;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 7px;
    box-shadow: 0 4px 14px rgba(154, 123, 79, .32);
    transition: opacity .15s, transform .1s, box-shadow .15s;
    letter-spacing: .01em;
  }

  .btn-main:hover {
    opacity: .9;
    box-shadow: 0 6px 20px rgba(154, 123, 79, .42);
  }

  .btn-main:active {
    transform: scale(.98);
  }

  .btn-main:disabled {
    opacity: .55;
    cursor: not-allowed;
    box-shadow: none;
  }

  .btn-ghost {
    background: transparent;
    color: var(--text-muted);
    border: 1.5px solid var(--border-color);
    border-radius: 8px;
    padding: .55rem 1.5rem;
    font-size: .875rem;
    font-weight: 500;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 7px;
    transition: all .15s;
  }

  .btn-ghost:hover {
    border-color: var(--text-muted);
    color: var(--text-heading);
    background: var(--surface-2);
  }

  /* Mobile sticky bar */
  .mob-bar {
    display: none;
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    z-index: 250;
    background: var(--card-bg);
    border-top: 1px solid var(--card-border);
    padding: 11px 16px;
    gap: 10px;
    box-shadow: 0 -4px 16px rgba(0, 0, 0, .1);
  }

  @media(max-width:767.98px) {
    .mob-bar {
      display: flex;
    }

    .desk-actions {
      display: none !important;
    }

    .main-content {
      padding-bottom: 86px !important;
    }
  }

  /* ── Right panel ─────────────────────────── */
  .rp-card {
    background: var(--card-bg);
    border: 1px solid var(--card-border);
    border-radius: 12px;
    box-shadow: 0 1px 12px rgba(100, 120, 160, .08);
    overflow: hidden;
    margin-bottom: 14px;
    transition: background .3s, border-color .3s;
  }

  .rp-head {
    display: flex;
    align-items: center;
    gap: 9px;
    padding: 13px 17px;
    border-bottom: 1px solid var(--card-border);
  }

  .rp-head h6 {
    font-size: .85rem;
    font-weight: 600;
    color: var(--text-heading);
    margin: 0;
  }

  .rp-body {
    padding: 15px 17px;
  }

  .rp-badge {
    border-radius: 20px;
    font-size: .65rem;
    font-weight: 600;
    padding: 2px 9px;
    display: inline-flex;
    align-items: center;
    gap: 4px;
  }

  .pv-row {
    display: flex;
    justify-content: space-between;
    align-items: baseline;
    gap: 8px;
    margin-bottom: 9px;
    font-size: .79rem;
  }

  .pv-row:last-child {
    margin-bottom: 0;
  }

  .pv-k {
    color: var(--text-muted);
    white-space: nowrap;
  }

  .pv-v {
    font-weight: 600;
    color: var(--text-heading);
    text-align: right;
    max-width: 58%;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
  }

  .rp-hr {
    border: none;
    border-top: 1px solid var(--card-border);
    margin: 10px 0;
  }

  /* Tips */
  .tips-card {
    background: var(--card-bg);
    border: 1px solid var(--card-border);
    border-radius: 12px;
    padding: 15px 17px;
    box-shadow: 0 1px 12px rgba(100, 120, 160, .08);
    margin-bottom: 14px;
  }

  .tips-lbl {
    font-size: .65rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .08em;
    color: var(--text-muted);
    margin-bottom: 11px;
  }

  .tip {
    display: flex;
    align-items: flex-start;
    gap: 8px;
    font-size: .78rem;
    color: var(--text-primary);
    margin-bottom: 8px;
    line-height: 1.5;
  }

  .tip:last-child {
    margin-bottom: 0;
  }

  .tip i {
    color: #9A7B4F;
    flex-shrink: 0;
    margin-top: 2px;
  }

  /* Collapsible right panel (mobile) */
  .rp-toggle {
    display: none;
    width: 100%;
    background: var(--card-bg);
    border: 1px solid var(--card-border);
    border-radius: 10px;
    padding: 12px 15px;
    cursor: pointer;
    font-size: .8125rem;
    font-weight: 600;
    color: var(--text-heading);
    align-items: center;
    justify-content: space-between;
    margin-bottom: 13px;
    box-shadow: 0 1px 10px rgba(100, 120, 160, .08);
  }

  @media(max-width:991.98px) {
    .rp-toggle {
      display: flex;
    }

    .rp-body-wrap {
      display: none;
    }

    .rp-body-wrap.open {
      display: block;
    }
  }

  /* ── Toasts ──────────────────────────────── */
  .toast-shelf {
    position: fixed;
    top: 68px;
    right: 15px;
    z-index: 9999;
    display: flex;
    flex-direction: column;
    gap: 7px;
    max-width: 290px;
  }

  .toast-el {
    background: var(--card-bg);
    border-left: 4px solid #9A7B4F;
    border-radius: 8px;
    padding: 11px 13px;
    box-shadow: 0 6px 22px rgba(0, 0, 0, .16);
    display: flex;
    align-items: flex-start;
    gap: 9px;
    animation: tIn .26s ease;
  }

  .toast-el.success {
    border-color: #05a34a;
  }

  .toast-el.error {
    border-color: #ff3366;
  }

  @keyframes tIn {
    from {
      transform: translateX(36px);
      opacity: 0;
    }

    to {
      transform: none;
      opacity: 1;
    }
  }

  .ti {
    font-size: 1rem;
    margin-top: 1px;
    flex-shrink: 0;
  }

  .ti.primary {
    color: #9A7B4F;
  }

  .ti.success {
    color: #05a34a;
  }

  .ti.error {
    color: #ff3366;
  }

  .tt {
    font-size: .8rem;
    font-weight: 700;
    margin: 0 0 2px;
    color: var(--text-heading);
  }

  .tb {
    font-size: .7rem;
    margin: 0;
    color: var(--text-muted);
  }

  @media(max-width:575.98px) {
    .toast-shelf {
      left: 11px;
      right: 11px;
      max-width: none;
    }
  }

  /* ── Success overlay ─────────────────────── */
  .sr-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: var(--overlay-bg);
    z-index: 9998;
    align-items: center;
    justify-content: center;
    padding: 16px;
    backdrop-filter: blur(3px);
  }

  .sr-overlay.show {
    display: flex;
  }

  .sr-modal {
    background: var(--modal-bg);
    border-radius: 14px;
    padding: 32px 28px;
    text-align: center;
    max-width: 400px;
    width: 100%;
    box-shadow: 0 28px 72px rgba(0, 0, 0, .18);
    border: 1px solid var(--card-border);
    animation: popIn .3s cubic-bezier(.34, 1.56, .64, 1);
  }

  @keyframes popIn {
    from {
      transform: scale(.84);
      opacity: 0;
    }

    to {
      transform: none;
      opacity: 1;
    }
  }

  .ok-ring {
    width: 60px;
    height: 60px;
    background: rgba(5, 163, 74, .1);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.7rem;
    color: #05a34a;
    margin: 0 auto 14px;
    box-shadow: 0 0 0 8px rgba(5, 163, 74, .06);
  }

  .sr-modal h5 {
    font-size: 1.0625rem;
    font-weight: 700;
    color: var(--text-heading);
    margin: 0 0 5px;
  }

  .sr-modal p {
    font-size: .8rem;
    color: var(--text-muted);
    margin: 0 0 14px;
  }

  .sr-id {
    background: rgba(154, 123, 79, .1);
    border: 1.5px solid rgba(154, 123, 79, .25);
    border-radius: 8px;
    padding: 10px 18px;
    font-size: .9375rem;
    font-weight: 700;
    color: #9A7B4F;
    display: inline-block;
    letter-spacing: .06em;
    margin-bottom: 16px;
  }

  .fitem .fi-thumb{
  width:44px;height:44px;object-fit:cover;border-radius:6px;
  border:1px solid rgba(255,255,255,.12);flex:0 0 auto;display:block;
}
.fi-pdf{
  width:44px;height:44px;border-radius:6px;flex:0 0 auto;
  display:flex;align-items:center;justify-content:center;
  background:rgba(255,51,102,.1);color:#ff3366;font-size:1.2rem;
}




/* Needed */


.btn-add-ct{width:38px;flex-shrink:0;border:1px solid rgba(154,123,79,.4);background:rgba(154,123,79,.1);color:#9A7B4F;border-radius:8px;cursor:pointer}
.btn-add-ct:disabled{opacity:.4;cursor:not-allowed}
.ct-overlay{position:fixed;inset:0;background:rgba(0,0,0,.55);backdrop-filter:blur(3px);z-index:0;display:none;align-items:center;justify-content:center;padding:16px}
.ct-overlay.show{display:flex}
.ct-box{background:var(--bs-body-bg,#fff);border:1px solid rgba(154,123,79,.25);border-radius:14px;width:100%;max-width:420px;box-shadow:0 20px 60px rgba(0,0,0,.35)}
.ct-hdr{display:flex;align-items:center;justify-content:space-between;padding:14px 16px;border-bottom:1px solid rgba(154,123,79,.18)}
.ct-hdr h6{margin:0;font-size:.9rem;color:#9A7B4F}
.ct-x{background:none;border:0;cursor:pointer;color:inherit;opacity:.6}
.ct-body{padding:16px}
.ct-foot{
  display:flex;justify-content:flex-end;gap:10px;
  padding:12px 16px;border-top:1px solid rgba(154,123,79,.18);
}
.ct-foot .btn-ghost,
.ct-foot .btn-gold{
  display:inline-flex;align-items:center;gap:6px;
  padding:8px 16px;border-radius:8px;
  font-size:.78rem;font-weight:600;letter-spacing:.2px;
  cursor:pointer;transition:all .18s ease;line-height:1;
}
.ct-foot .btn-ghost{
  background:transparent;
  border:1px solid rgba(154,123,79,.35);
  color:var(--text-muted,#8a8a8a);
}
.ct-foot .btn-ghost:hover{
  background:rgba(154,123,79,.08);
  border-color:rgba(154,123,79,.55);
  color:#9A7B4F;
}
.ct-foot .btn-gold{
  background:linear-gradient(135deg,#9A7B4F,#c1a06a);
  border:1px solid rgba(154,123,79,.6);
  color:#fff;
  box-shadow:0 2px 8px rgba(154,123,79,.28);
}
.ct-foot .btn-gold:hover{
  background:linear-gradient(135deg,#8a6d45,#b0905c);
  box-shadow:0 4px 14px rgba(154,123,79,.4);
  transform:translateY(-1px);
}
.ct-foot .btn-gold:active{transform:translateY(0)}
.ct-foot .btn-gold:disabled{opacity:.55;cursor:not-allowed;transform:none;box-shadow:none}
</style>
@endpush

@section('content')

{{-- Toasts --}}
<div class="toast-shelf" id="toastShelf"></div>

{{-- Success overlay --}}
<div class="sr-overlay" id="srOverlay">
  <div class="sr-modal">
    <div class="ok-ring"><i class="bi bi-check-lg"></i></div>
    <h5>Ticket Submitted!</h5>
    <p>Your service request is logged and set to <strong>Pending</strong>.</p>
    <div class="sr-id" id="srIdOut">SR-0000-00000</div>
    <p style="font-size:.76rem;color:var(--text-muted);margin-bottom:18px;">
      <i class="bi bi-check-circle-fill me-1" style="color:#05a34a;"></i>Timestamped and saved in the system.
    </p>
    <button class="btn-main w-100 mb-2" onclick="goHub()">
      <i class="bi bi-grid-1x2"></i>Inquiry Approvels
    </button>
    <button class="btn-ghost w-100" onclick="newTicket()">
      <i class="bi bi-plus-circle"></i>Register Another SR
    </button>
  </div>
</div>

{{-- Main --}}
<main class="main-content">

  {{-- ── Page Header ── --}}
  <div class="sr-header">
    <div class="sr-header-inner">
      <h4><i class="bi bi-ticket-perforated me-2"></i>SR Registration</h4>
      <p>Log a new service request by verifying the customer, filling in service details, and submitting.</p>
      <div class="sr-header-tags">
        <span class="sr-htag"><i class="bi bi-person-badge me-1"></i>Front Desk</span>
        <span class="sr-htag"><i class="bi bi-person-gear me-1"></i>Admin</span>
        <span class="sr-htag"><i class="bi bi-shield-check me-1"></i>Super Admin</span>
      </div>
    </div>
  </div>

  {{-- ── Status Bar ── --}}
  <div class="sr-alert" id="srAlert"></div>

  <form id="srForm" enctype="multipart/form-data">
    @csrf
    <div class="row g-3 g-lg-4 align-items-start">

      {{-- ════════════════ LEFT COLUMN (3 cards) ════════════════ --}}
      <div class="col-lg-8">

        {{-- ── CARD 1 · Customer Verification ── --}}
        <div class="fc">
          <div class="fc-head">
            <div class="fc-icon" style="background:rgba(154,123,79,.1);">
              <i class="bi bi-person-badge-fill" style="color:#9A7B4F;"></i>
            </div>
            <div>
              <h6>Customer Verification</h6>
              <span class="fc-sub">Search by customer name, code or mobile — details load automatically</span>
            </div>
            <div class="fc-step" style="background:#9A7B4F;">1</div>
          </div>
          <div class="fc-body">
            <div class="row g-3">

              {{-- Lookup --}}
              <div class="col-12">
                <label class="form-label">Customer Lookup <span class="req">*</span></label>
                <div class="lk-wrap" id="lkWrap">
                  <span style="position:absolute;left:10px;top:50%;transform:translateY(-50%);color:var(--text-muted);font-size:.85rem;pointer-events:none;">
                    <i class="bi bi-search"></i>
                  </span>
                  <input type="text" class="form-control" id="custCode" name="customer_code"
                    placeholder="Customer name, unique code, or mobile number"
                    maxlength="60" autocomplete="off"
                    oninput="onCode(this.value)" />
                  <div class="lk-spin">
                    <div class="spinner-border"></div>
                  </div>
                  <i class="bi bi-check-circle-fill lk-ok"></i>
                </div>
                <div class="form-hint"><i class="bi bi-search"></i>Search by Customer Name, Unique Code or Primary Mobile.</div>
                {{-- hidden fields posted to controller --}}
                <input type="hidden" id="customerId" name="customer_id" />
                <input type="hidden" id="customerName" name="customer_name" />
                <div class="client-reveal" id="clientReveal">
                  <div><span class="ci-lbl">Customer Name</span><span class="ci-val" id="cName">—</span></div>
                  <div><span class="ci-lbl">Status</span><span class="ci-val" id="cStatus" style="color:#05a34a;">—</span></div>
                  <div><span class="ci-lbl">Contact Person</span><span class="ci-val" id="cFlag">—</span></div>
                  <div><span class="ci-lbl">Contact</span><span class="ci-val" id="cContact">—</span></div>
                </div>
              </div>

              {{-- Project --}}
              <div class="col-sm-6">
                <label class="form-label">Project <span class="req">*</span></label>
                <div class="iw">
                  <span class="ii"><i class="bi bi-folder2-open"></i></span>
                  <select class="form-select" id="projSel" name="project_id" disabled onchange="onProject(this.value)">
                    <option value="">— Select project —</option>
                  </select>
                </div>
                <div class="form-hint"><i class="bi bi-info-circle"></i>All projects for the customer load after verification</div>
              </div>

              {{-- Site (auto-filled, read-only) --}}
              <div class="col-sm-6">
                <label class="form-label">Site Location <span class="req">*</span></label>
                <div class="iw">
                  <span class="ii"><i class="bi bi-geo-alt"></i></span>
                  <input type="text" class="form-control" id="siteField" name="project_site"
                    placeholder="Auto-filled from project" readonly />
                </div>
                <div class="form-hint"><i class="bi bi-magic"></i>Auto-fetched from the selected project</div>
              </div>

            </div>
          </div>
        </div>

        {{-- ── CARD 2 · Service Info ── --}}
        <div class="fc">
          <div class="fc-head">
            <div class="fc-icon" style="background:rgba(5,163,74,.1);">
              <i class="bi bi-grid-3x3-gap-fill" style="color:#05a34a;"></i>
            </div>
            <div>
              <h6>Service Information</h6>
              <span class="fc-sub">Type, reporter and urgency level</span>
            </div>
            <div class="fc-step" style="background:#05a34a;">2</div>
          </div>
          <div class="fc-body">
            <div class="row g-3">

              {{-- Service Type (2 static options) --}}
              <div class="col-sm-6">
                <label class="form-label">Service Category <span class="req">*</span></label>
                <div class="iw">
                  <span class="ii"><i class="bi bi-tag"></i></span>

                  <select class="form-select" id="svcType" name="service_type_id"
                    onchange="pv('pvType', this.options[this.selectedIndex].text)">
                    <option value="">— Select Category —</option>

                    @foreach($categories as $category)
                    <option value="{{ $category->id }}">
                      {{ $category->category_name }}
                    </option>
                    @endforeach

                  </select>
                </div>
              </div>

              {{-- Reported By --}}
         
<div class="col-sm-6">
  <label class="form-label">Contact Person <span class="req">*</span></label>
  <div class="iw" style="display:flex;gap:6px;">
    <span class="ii"><i class="bi bi-person"></i></span>
    <select class="form-control" id="reporter" name="reported_by" onchange="onReporterChange(this)" style="flex:1;">
      <option value="">Select customer first…</option>
    </select>
    <button type="button" class="btn-add-ct" id="addCtBtn" onclick="openCtModal()" title="Add contact person" disabled>
      <i class="bi bi-plus-lg"></i>
    </button>
  </div>
  <input type="hidden" name="reported_by_mobile" id="reporterMobile">
</div>

{{-- Add Contact Modal --}}
<div class="ct-overlay" id="ctModal" onclick="if(event.target===this)closeCtModal()">
  <div class="ct-box" role="dialog" aria-modal="true">
    <div class="ct-hdr">
      <h6><i class="bi bi-person-plus"></i> Add Contact Person</h6>
      <button type="button" class="ct-x" onclick="closeCtModal()"><i class="bi bi-x-lg"></i></button>
    </div>
    <div class="ct-body">
      <label class="form-label">Name <span class="req">*</span></label>
      <input type="text" class="form-control" id="ctName" placeholder="e.g. John Facilities">

      <label class="form-label" style="margin-top:10px;">WhatsApp Number <span class="req">*</span></label>
      <div style="display:flex;gap:6px;">
        <select class="form-control" id="ctCountry" style="width:90px;flex-shrink:0;">
          <option value="+971">+971</option>
          <option value="+91" selected>+91</option>
          <option value="+1">+1</option>
          <option value="+44">+44</option>
          <option value="+966">+966</option>
          <option value="+974">+974</option>
        </select>
        <input type="tel" class="form-control" id="ctMobile" placeholder="50 123 4567"
               inputmode="numeric" maxlength="15"
               oninput="this.value=this.value.replace(/[^0-9]/g,'')" style="flex:1;">
      </div>

      <label class="ct-chk-lbl" style="margin-top:10px;display:none;">
        <input type="checkbox" id="ctNotify" value="1">
        <span>Send WhatsApp updates</span>
      </label>
    </div>
    <div class="ct-foot">
      <button type="button" class="btn-ghost" onclick="closeCtModal()">Cancel</button>
      <button type="button" class="btn-gold" id="ctSaveBtn" onclick="saveContact()">
        <i class="bi bi-check-lg"></i> Save Contact
      </button>
    </div>
  </div>
</div>
             {{-- Priority --}}
<div class="col-12">
  <label class="form-label">Priority Level <span class="req">*</span></label>
  <div class="priority-row" id="prGroup">
    @foreach($priorities as $priority)
      <div class="pr-pill"
           style="--pr-color: {{ $priority->color }}"
           onclick="setPriority(this, '{{ $priority->name }}')">
        <span class="pi" style="color: {{ $priority->color }}">●</span>{{ $priority->name }}
      </div>
    @endforeach
  </div>
  <input type="hidden" id="priorityVal" name="priority_level" />
</div>

            </div>
          </div>
        </div>

        {{-- ── CARD 3 · Description ── --}}
        <div class="fc">
          <div class="fc-head">
            <div class="fc-icon" style="background:rgba(251,188,6,.1);">
              <i class="bi bi-pencil-square" style="color:#d9a400;"></i>
            </div>
            <div>
              <h6>Description</h6>
              <span class="fc-sub">Describe the issue clearly for the service team</span>
            </div>
            <div class="fc-step" style="background:#d9a400;">3</div>
          </div>
          <div class="fc-body">
            <div class="mb-3">
              <label class="form-label">Issue Description <span class="req">*</span></label>
              <textarea class="form-control" id="svcDesc" name="issue_description" rows="5"
                placeholder="Describe the service issue clearly (min 20 characters)..."
                oninput="onDesc(this)"></textarea>
              <div class="d-flex justify-content-between align-items-center mt-1">
                <span class="form-hint"><i class="bi bi-info-circle"></i>Minimum 20 characters</span>
                <span class="cc warn" id="ccCount">0 / 20</span>
              </div>
            </div>
            <div>
              <label class="form-label">Internal Remark <span class="opt">(Optional)</span></label>
              <textarea class="form-control" id="remark" name="internal_remark" rows="3"
                placeholder="Notes for the service team — not visible to client..."></textarea>
              <div class="form-hint"><i class="bi bi-eye-slash"></i>Not shared with client</div>
            </div>
          </div>
        </div>

        {{-- Desktop actions --}}
        <div class="d-flex gap-3 justify-content-end mt-1 desk-actions">
          <button type="button" class="btn-ghost" onclick="clearAll()"><i class="bi bi-arrow-counterclockwise"></i>Clear Form</button>
          <button type="button" class="btn-main" onclick="doSubmit()" id="deskBtn"><i class="bi bi-send-fill"></i>Submit Ticket</button>
        </div>

      </div>{{-- /col-lg-8 --}}

      {{-- ════════════════ RIGHT COLUMN (3 cards) ════════════════ --}}
      <div class="col-lg-4">

        {{-- Mobile toggle --}}
        <button type="button" class="rp-toggle" onclick="toggleRP(this)">
          <span style="display:flex;align-items:center;gap:8px;"><i class="bi bi-eye" style="color:#9A7B4F;"></i>SR Preview</span>
          <i class="bi bi-chevron-down" id="rpChevron" style="transition:transform .25s;color:var(--text-muted);"></i>
        </button>

        <div class="rp-body-wrap" id="rpWrap">

          {{-- RIGHT CARD 1 · Preview --}}
          <div class="rp-card">
            <div class="rp-head">
              <div class="fc-icon" style="background:rgba(154,123,79,.1);width:30px;height:30px;border-radius:7px;font-size:.82rem;">
                <i class="bi bi-eye" style="color:#9A7B4F;"></i>
              </div>
              <h6>SR Preview</h6>
              <span class="ms-auto rp-badge" style="background:rgba(251,188,6,.12);color:#b88b00;">● Draft</span>
            </div>
            <div class="rp-body">
              <div class="pv-row"><span class="pv-k">Customer</span><span class="pv-v" id="pvCode">—</span></div>
              <div class="pv-row"><span class="pv-k">Project</span><span class="pv-v" id="pvProject">—</span></div>
              <div class="pv-row"><span class="pv-k">Site</span><span class="pv-v" id="pvSite">—</span></div>
              <hr class="rp-hr">
              <div class="pv-row"><span class="pv-k">Type</span><span class="pv-v" id="pvType">—</span></div>
              <div class="pv-row"><span class="pv-k">Reporter</span><span class="pv-v" id="pvReporter">—</span></div>
              <div class="pv-row"><span class="pv-k">Priority</span><span class="pv-v" id="pvPriority">—</span></div>
              <hr class="rp-hr">
              <div class="pv-row"><span class="pv-k">Files</span><span class="pv-v" id="pvFiles">0 files</span></div>
              <div class="pv-row" style="margin:0;"><span class="pv-k">Description</span><span class="pv-v" id="pvDesc">—</span></div>
            </div>
          </div>

          {{-- RIGHT CARD 2 · Tips --}}
          <div class="tips-card">
            <div class="tips-lbl">How to fill this form</div>
            <div class="tip"><i class="bi bi-1-circle-fill"></i>Search the customer by name, code or mobile — details load automatically.</div>
            <div class="tip"><i class="bi bi-2-circle-fill"></i>Select a project — the site auto-fills.</div>
            <div class="tip"><i class="bi bi-3-circle-fill"></i>Pick a service type, add the reporter name, and set priority.</div>
            <div class="tip"><i class="bi bi-4-circle-fill"></i>Write a clear description (min 20 characters).</div>
            <div class="tip"><i class="bi bi-5-circle-fill"></i>Attach photos or PDFs if needed — up to 10 MB each.</div>
          </div>

          {{-- RIGHT CARD 3 · Attachments --}}
          <div class="fc" style="margin-bottom:0;">
            <div class="fc-head">
              <div class="fc-icon" style="background:rgba(154,123,79,.1);">
                <i class="bi bi-paperclip" style="color:#9A7B4F;"></i>
              </div>
              <div>
                <h6>Attachments</h6>
                <span class="fc-sub">.jpg · .png · .pdf · Max 10 MB each</span>
              </div>
              <span class="opt" style="margin-left:auto;">Optional</span>
            </div>
            <div class="fc-body">
              <div class="dz" id="dzBox" ondragover="dzOn(event)" ondragleave="dzOff()" ondrop="dzDrop(event)">
                <input type="file" id="fileInput" multiple accept=".jpg,.jpeg,.png,.pdf" onchange="onFiles(this.files)" />
                <div class="dz-ic"><i class="bi bi-cloud-arrow-up-fill"></i></div>
                <p class="dz-txt">Drop files here or <strong style="color:#9A7B4F;">browse</strong></p>
                <p class="dz-hint">.jpg · .png · .pdf &nbsp;·&nbsp; Max 10 MB</p>
              </div>
              <div class="file-list" id="fileList"></div>
              <div class="ftype-chips pt-2">
                <span class="ftc" style="background:rgba(255,51,102,.1);color:#ff3366;">.PDF</span>
                <span class="ftc" style="background:rgba(154,123,79,.1);color:#9A7B4F;">.JPG</span>
                <span class="ftc" style="background:rgba(5,163,74,.1);color:#05a34a;">.PNG</span>
                <span style="font-size:.68rem;color:var(--text-muted);margin-left:2px;">Multiple files OK</span>
              </div>
            </div>
          </div>

        </div>{{-- /rp-body-wrap --}}
      </div>

    </div>{{-- /row --}}
  </form>
</main>

{{-- Mobile bottom bar --}}
<div class="mob-bar" id="mobBar">
  <button type="button" class="btn-ghost" style="flex:1;" onclick="clearAll()"><i class="bi bi-arrow-counterclockwise"></i>Clear</button>
  <button type="button" class="btn-main" style="flex:2;" onclick="doSubmit()" id="mobBtn"><i class="bi bi-send-fill"></i>Submit Ticket</button>
</div>

@endsection

@push('scripts')
<script>
  /* ═══════════════════════════════════════════
   ENDPOINTS
═══════════════════════════════════════════ */




  const LOOKUP_URL = "{{ url('/service-requests/lookup') }}"; // GET + /{code}
  const STORE_URL = "{{ route('service-requests.store') }}"; // POST
  const CSRF_TOKEN = document.querySelector('#srForm input[name="_token"]').value;

  let vTimer = null,
    uploads = [];

  /* ── Init ── */
  document.addEventListener('DOMContentLoaded', () => {
    setAlert('warning', '⏳ Search a customer to begin.');
  });

  /* ── Alert bar ── */
  function setAlert(type, msg) {
    const el = document.getElementById('srAlert');
    const m = {
      warning: {
        bg: 'rgba(251,188,6,.09)',
        bd: 'rgba(251,188,6,.3)',
        c: '#b88b00',
        i: 'bi-exclamation-circle-fill'
      },
      success: {
        bg: 'rgba(5,163,74,.07)',
        bd: 'rgba(5,163,74,.2)',
        c: '#05a34a',
        i: 'bi-check-circle-fill'
      },
      danger: {
        bg: 'rgba(255,51,102,.07)',
        bd: 'rgba(255,51,102,.22)',
        c: '#ff3366',
        i: 'bi-x-circle-fill'
      },
      primary: {
        bg: 'rgba(154,123,79,.07)',
        bd: 'rgba(154,123,79,.2)',
        c: '#9A7B4F',
        i: 'bi-info-circle-fill'
      },
    } [type] || {};
    el.style.cssText = `background:${m.bg};border-color:${m.bd};`;
    el.innerHTML = `<i class="bi ${m.i}" style="color:${m.c};font-size:.9rem;flex-shrink:0;"></i><span>${msg}</span>`;
  }

  /* ── Lookup (debounced) ── */



  const CONTACT_STORE_URL = "{{ url('/service-requests/contacts') }}"; // POST

function openCtModal() {
  if (!window.__clientId) { toast('error','Validation','Verify a customer first.'); return; }
  document.getElementById('ctName').value = '';
  document.getElementById('ctMobile').value = '';
  document.getElementById('ctNotify').checked = false;
  document.getElementById('ctModal').classList.add('show');
}

function closeCtModal() {
  document.getElementById('ctModal').classList.remove('show');
}

async function saveContact() {
  const name    = document.getElementById('ctName').value.trim();
  const country = document.getElementById('ctCountry').value;
  const mobile  = document.getElementById('ctMobile').value.trim();
  const notify  = document.getElementById('ctNotify').checked ? 1 : 0;

  if (!name)   { toast('error','Validation','Enter the contact name.'); return; }
  if (!mobile) { toast('error','Validation','Enter the WhatsApp number.'); return; }

  const btn = document.getElementById('ctSaveBtn');
  btn.disabled = true;

  try {
    const res = await fetch(CONTACT_STORE_URL, {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': CSRF_TOKEN,
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify({ client_id: window.__clientId, name, country, mobile, notify })
    });

    if (res.status === 422) {
      const err = await res.json();
      toast('error','Validation', Object.values(err.errors||{})[0]?.[0] || 'Invalid data.');
      return;
    }
    if (!res.ok) throw new Error('Server error ' + res.status);

    const data = await res.json();          // { contact:{ id, name, mobile, notify } }
    const sel  = document.getElementById('reporter');
    const o    = document.createElement('option');
    o.value = data.contact.name;
    o.textContent = data.contact.name;
    sel.appendChild(o);
    sel.value = data.contact.name;
    onReporterChange(sel);

    closeCtModal();
    toast('success','Contact Added', data.contact.name);
  } catch (e) {
    toast('error','Save Failed', e.message || 'Could not save the contact.');
  } finally {
    btn.disabled = false;
  }
}



function fillReporters(contacts) {
  const sel = document.getElementById('reporter');
  sel.innerHTML = '<option value="">Select contact person…</option>';
  (contacts || []).forEach(c => {
    const o = document.createElement('option');
    o.value = c.name || '';
    o.textContent = c.name || '';
    sel.appendChild(o);
  });
  document.getElementById('addCtBtn').disabled = false;
  pv('pvReporter', '—');
}

function onReporterChange(sel) {
  pv('pvReporter', sel.value || '—');
}

  let lastVerifiedTerm = null;

 function onCode(val) {
  clearTimeout(vTimer);
  const term = val.trim();

  if (term && term === lastVerifiedTerm) return;

  const w = document.getElementById('lkWrap');
  w.classList.remove('verifying', 'verified');
  document.getElementById('clientReveal').classList.remove('show');
  document.getElementById('customerId').value = '';
  document.getElementById('customerName').value = '';
  resetSel('projSel', 'project');
  document.getElementById('siteField').value = '';
  pv('pvCode', '—');
  pv('pvProject', '—');
  pv('pvSite', '—');
  setAlert('warning', '⏳ Search by customer name, code or mobile.');

  lastVerifiedTerm = null;

  if (term.length >= 2) {
    w.classList.add('verifying');
   vTimer = setTimeout(() => verify(term), 400);
  }
}

  // Expected JSON from LOOKUP_URL/{term}:
  // { found:true, client:{ id, name, status, flag, contact,
  //   projects:{ "Project Name":["Site 1","Site 2"] } } }


async function verify(val) {
    const w = document.getElementById('lkWrap');
    const term = val.trim();

    if (!term) {
      w.classList.remove('verifying', 'verified');
      lastVerifiedTerm = null;
      return;
    }

    // Already verified this exact value — don't re-run or re-toast
    if (term === lastVerifiedTerm) return;

    try {
      const res = await fetch(`${LOOKUP_URL}/${encodeURIComponent(term)}`, {
        headers: {
          'Accept': 'application/json'
        }
      });
     let data = res.ok ? await res.json() : { found: false };
      w.classList.remove('verifying');
      // Any matches → list them, wait for a click (even if there's only one)
      if (Array.isArray(data.clients) && data.clients.length) {
        lastVerifiedTerm = null;
        renderMatches(data.clients);
        return;
      }
      if (data.found && data.client) {
        const c = data.client;
       lastVerifiedTerm = document.getElementById('custCode').value.trim();
        w.classList.add('verified');
        const box = document.getElementById('clientReveal');
        box.style.gridTemplateColumns = '';   // back to the 4-column grid
        box.innerHTML = `
          <div><span class="ci-lbl">Customer Name</span><span class="ci-val" id="cName">—</span></div>
          <div><span class="ci-lbl">Status</span><span class="ci-val" id="cStatus" style="color:#05a34a;">—</span></div>
          <div><span class="ci-lbl">Contact Person</span><span class="ci-val" id="cFlag">—</span></div>
          <div><span class="ci-lbl">Contact</span><span class="ci-val" id="cContact">—</span></div>`;
        document.getElementById('cName').textContent = c.name ?? '—';
        document.getElementById('cStatus').textContent = c.status ?? '—';
        document.getElementById('cFlag').textContent = c.flag ?? '—';
        document.getElementById('cContact').textContent = c.contact ?? '—';
        document.getElementById('customerId').value = c.id ?? '';
        document.getElementById('customerName').value = c.name ?? '';
        window.__clientId = c.id;
        document.getElementById('clientReveal').classList.add('show');

        fillReporters(c.contacts);   // ← add this


        const ps = document.getElementById('projSel');
        ps.innerHTML = '<option value="">— Select project —</option>';

        // Normalize to an array of { id, name, sites }
        let raw = c.projects || [];
        if (!Array.isArray(raw)) {
          raw = Object.keys(raw).map(name => ({
            id: name,
            name,
            sites: raw[name]
          }));
        }
        window.__projects = raw;

        window.__projects.forEach(p => {
          const o = document.createElement('option');
          o.value = p.id;
          o.textContent = p.name;
          ps.appendChild(o);
        });
        ps.disabled = false;

        document.getElementById('siteField').value = '';
        pv('pvSite', '—');

        pv('pvCode', c.name ?? term);
        setAlert('success', `✅ Verified: <strong>${c.name}</strong> — select a project to continue.`);
        toast('success', 'Customer Verified', c.name);
      } else {
        lastVerifiedTerm = null;
        setAlert('danger', '❌ No customer found. Check the name, code or mobile.');
        toast('error', 'Not Found', 'No active customer for this search.');
      }
    } catch (e) {
      lastVerifiedTerm = null;
      w.classList.remove('verifying');
      setAlert('danger', '❌ Lookup failed. Please try again.');
      toast('error', 'Error', e.message || 'Lookup request failed.');
    }
  }

  /* ── Multiple matches → clickable list in the reveal box ── */
  function renderMatches(list) {
    const box = document.getElementById('clientReveal');
    box.innerHTML = '';
    box.style.gridTemplateColumns = '1fr';   // one row per client
    list.forEach(c => {
      const row = document.createElement('div');
      row.style.cssText = 'cursor:pointer;padding:6px 4px;border-bottom:1px solid rgba(5,163,74,.15);';
      row.innerHTML = `<span class="ci-val"></span><span class="ci-lbl" style="margin:2px 0 0;"></span>`;
      row.querySelector('.ci-val').textContent = c.name ?? '';
      row.querySelector('.ci-lbl').textContent =
        [c.code, c.contact].filter(Boolean).join(' · ');
      row.addEventListener('click', () => pickClient(c));
      box.appendChild(row);
    });
    box.classList.add('show');
    setAlert('primary', `ℹ️ ${list.length} matches — click the customer you want.`);
  }

  /* Click a match → fill the input with the name, then verify it */
  function pickClient(c) {
    document.getElementById('custCode').value = c.name;   // ← name fills the field
    clearTimeout(vTimer);
    lastVerifiedTerm = null;
    document.getElementById('clientReveal').classList.remove('show');
    document.getElementById('lkWrap').classList.add('verifying');
    verify(c.code || c.name);
  }

  /* ── Project → auto-fill first site ── */
  function onProject(val) {
  const projects = window.__projects || [];
  const proj = projects.find(p => String(p.id) === String(val));
  pv('pvProject', proj ? proj.name : '—');
  const sites = proj && proj.sites ? proj.sites : [];
  const firstSite = sites.length ? sites[0] : '';
  document.getElementById('siteField').value = firstSite;   // auto-fill first site (read-only)
  pv('pvSite', firstSite || '—');
}

  function resetSel(id, lbl) {
    const el = document.getElementById(id);
    el.innerHTML = `<option value="">— Select ${lbl} —</option>`;
    el.disabled = true;
  }

  /* ── Priority ── */
  function setPriority(el, val) {
    document.querySelectorAll('#prGroup .pr-pill').forEach(p => p.classList.remove('on'));
    el.classList.add('on');
    document.getElementById('priorityVal').value = val;
    pv('pvPriority', val);
  }

  /* ── Description ── */
  function onDesc(el) {
    const len = el.value.length;
    const cc = document.getElementById('ccCount');
    cc.textContent = len < 20 ? `${len} / 20` : `${len} ✓`;
    cc.className = 'cc ' + (len < 20 ? 'warn' : 'ok');
    pv('pvDesc', len > 0 ? el.value.substring(0, 28) + (len > 28 ? '…' : '') : '—');
  }

  /* ── Files ── */
  function dzOn(e) {
    e.preventDefault();
    document.getElementById('dzBox').classList.add('over');
  }

  function dzOff() {
    document.getElementById('dzBox').classList.remove('over');
  }

  function dzDrop(e) {
    e.preventDefault();
    dzOff();
    addFiles(e.dataTransfer.files);
  }

  function onFiles(f) {
    addFiles(f);
  }

  function addFiles(files) {
    const ok = ['image/jpeg', 'image/png', 'application/pdf'];
    Array.from(files).forEach(f => {
      if (!ok.includes(f.type)) {
        toast('error', 'Wrong Type', `"${f.name}" not allowed.`);
        return;
      }
      if (f.size > 10 * 1024 * 1024) {
        toast('error', 'Too Large', `"${f.name}" exceeds 10 MB.`);
        return;
      }
      if (uploads.find(x => x.name === f.name && x.size === f.size)) return;
      uploads.push(f);
    });
    renderFiles();
  }

 function renderFiles() {
  const list = document.getElementById('fileList');
  list.innerHTML = '';
  uploads.forEach((f, i) => {
    const ext   = f.name.split('.').pop().toUpperCase();
    const isImg = /^image\//.test(f.type) || ['JPG','JPEG','PNG','GIF','WEBP'].includes(ext);
    const url   = URL.createObjectURL(f);

    const d = document.createElement('div');
    d.className = 'fitem';
    d.innerHTML = `
      <a href="${url}" target="_blank" rel="noopener" class="fi-link">
        ${isImg
          ? `<img class="fi-thumb" src="${url}" alt="">`
          : `<span class="fi-pdf"><i class="bi bi-file-earmark-pdf-fill"></i></span>`}
      </a>
      <a href="${url}" target="_blank" rel="noopener" class="fi-name">${f.name}</a>
      <span class="fi-sz">${(f.size/1024).toFixed(1)} KB</span>
      <button type="button" class="fi-rm" onclick="rmFile(${i})"><i class="bi bi-x-lg"></i></button>`;

    list.appendChild(d);
  });
  pv('pvFiles', `${uploads.length} file${uploads.length !== 1 ? 's' : ''}`);
}

  function rmFile(i) {
    uploads.splice(i, 1);
    renderFiles();
  }

  /* ── Submit (POST to controller) ── */
  async function doSubmit() {
    const customerId = document.getElementById('customerId').value;
    const project = document.getElementById('projSel').value;
    const site = document.getElementById('siteField').value.trim();
    const type = document.getElementById('svcType').value;
    const reporter = document.getElementById('reporter').value.trim();
    const priority = document.getElementById('priorityVal').value;
    const desc = document.getElementById('svcDesc').value.trim();
    const verified = document.getElementById('lkWrap').classList.contains('verified');

    if (!verified || !customerId) {
      toast('error', 'Validation', 'Please search and verify a customer first.');
      return;
    }
    if (!project) {
      toast('error', 'Validation', 'Please select a project.');
      return;
    }
    if (!site) {
      toast('error', 'Validation', 'No site found for this project.');
      return;
    }
    if (!type) {
      toast('error', 'Validation', 'Please select a service type.');
      return;
    }
    if (!reporter) {
      toast('error', 'Validation', 'Please enter the reporter name.');
      return;
    }
    if (!priority) {
      toast('error', 'Validation', 'Please select a priority level.');
      return;
    }
    if (desc.length < 20) {
      toast('error', 'Validation', 'Description must be at least 20 characters.');
      return;
    }

    setBtns(true);

    const fd = new FormData();
    fd.append('_token', CSRF_TOKEN);
    fd.append('client_id', window.__clientId);
    fd.append('customer_name', document.getElementById('customerName').value);
    fd.append('project_id', project); // project NAME
    fd.append('project_site', site); // site NAME (auto-filled)
    fd.append('service_type_id', type);
    fd.append('reported_by', reporter);
    fd.append('priority_level', priority);
    fd.append('issue_description', desc);
    fd.append('internal_remark', document.getElementById('remark').value);
    uploads.forEach(f => fd.append('attachments[]', f));

    try {
      const res = await fetch(STORE_URL, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': CSRF_TOKEN,
          'Accept': 'application/json'
        },
        body: fd,
      });

      if (res.status === 422) {
        const err = await res.json();
        const first = Object.values(err.errors || {})[0]?.[0] || 'Validation failed.';
        toast('error', 'Validation', first);
        return;
      }
      if (!res.ok) throw new Error('Server error ' + res.status);

      const data = await res.json(); // expected: { sr_reference: "SR-2025-00001" }
      document.getElementById('srIdOut').textContent = data.sr_reference || '—';
      document.getElementById('srOverlay').classList.add('show');
    } catch (e) {
      toast('error', 'Save Failed', e.message || 'Could not save the request.');
    } finally {
      setBtns(false);
    }
  }

  function setBtns(loading) {
    ['deskBtn', 'mobBtn'].forEach(id => {
      const b = document.getElementById(id);
      if (!b) return;
      b.disabled = loading;
      b.innerHTML = loading ?
        '<span class="spinner-border" style="width:13px;height:13px;border-width:2px;"></span> Submitting…' :
        '<i class="bi bi-send-fill"></i>Submit Ticket';
    });
  }

  /* ── Post-submit ── */
  function goHub() {
    document.getElementById('srOverlay').classList.remove('show');
    clearAll();
    window.location.href = "{{ route('inquiry-approval.index') }}";
  }

  function newTicket() {
    document.getElementById('srOverlay').classList.remove('show');
    clearAll();
    toast('primary', 'Ready', 'Form cleared — register a new SR.');
  }


  /* ── Clear ── */
  function clearAll() {
    document.getElementById('custCode').value = '';
    document.getElementById('customerId').value = '';
    document.getElementById('customerName').value = '';
    document.getElementById('lkWrap').classList.remove('verifying', 'verified');
    document.getElementById('clientReveal').classList.remove('show');
    resetSel('projSel', 'project');
    document.getElementById('siteField').value = '';
    document.getElementById('svcType').value = '';
    document.getElementById('reporter').value = '';
    document.getElementById('svcDesc').value = '';
    document.getElementById('remark').value = '';
    document.getElementById('ccCount').textContent = '0 / 20';
    document.getElementById('ccCount').className = 'cc warn';
    document.getElementById('priorityVal').value = '';
    document.querySelectorAll('#prGroup .pr-pill').forEach(p => p.classList.remove('on'));
    uploads = [];
    renderFiles();
    ['pvCode', 'pvProject', 'pvSite', 'pvType', 'pvReporter', 'pvPriority', 'pvDesc'].forEach(id => pv(id, '—'));
    pv('pvFiles', '0 files');
    setAlert('warning', '⏳ Search a customer to begin.');
  }

  

  /* ── Right panel toggle ── */
  function toggleRP(btn) {
    const w = document.getElementById('rpWrap');
    const icon = document.getElementById('rpChevron');
    const open = w.classList.toggle('open');
    icon.style.transform = open ? 'rotate(180deg)' : 'rotate(0deg)';
  }

  /* ── Helpers ── */
  function pv(id, val) {
    const el = document.getElementById(id);
    if (el) el.textContent = val;
  }

  function toast(type, title, body) {
    const shelf = document.getElementById('toastShelf');
    const icons = {
      success: 'bi-check-circle-fill',
      error: 'bi-x-circle-fill',
      primary: 'bi-info-circle-fill'
    };
    const t = document.createElement('div');
    t.className = `toast-el${type==='error'?' error':type==='success'?' success':''}`;
    t.innerHTML = `<i class="bi ${icons[type]||'bi-info-circle-fill'} ti ${type}"></i>
    <div><p class="tt">${title}</p><p class="tb">${body}</p></div>`;
    shelf.appendChild(t);
    setTimeout(() => {
      t.style.opacity = '0';
      t.style.transition = 'opacity .3s';
      setTimeout(() => t.remove(), 300);
    }, 3400);
  }
</script>
@endpush