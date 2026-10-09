@extends('layouts.layout')

@section('title', 'SR Registration - Digit-Us Portal')
@section('page_title', 'SR Registration')
@section('page_icon', 'building-add')

@push('styles')
<style>
  /* ── Page tokens (change the blue here) ───── */
  :root {
    --sr-primary: #0d8ed6;
    --sr-primary-dark: #0b76b3;
    --sr-primary-light: #45b3e7;
    --sr-primary-soft: rgba(13, 142, 214, .13);
  }

  /* ── Page title row ──────────────────────── */
  .sr-top {
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    gap: 12px 20px;
    flex-wrap: wrap;
    margin-bottom: 16px;
  }

  .sr-title {
    font-size: 1.375rem;
    font-weight: 700;
    color: var(--text-heading);
    margin: 0 0 4px;
    letter-spacing: -.01em;
  }

  .sr-sub {
    font-size: .875rem;
    color: var(--text-muted);
    margin: 0;
  }

  /* Status line (filled by setAlert) */
  .sr-alert {
    display: flex;                 /* was inline-flex: now lines up with the input edges */
    align-items: center;
    gap: 8px;
    margin-top: 10px;
    padding: 8px 12px;             /* was 7px 12px */
    border: 1px solid transparent;
    border-radius: 6px;            /* same as the inputs */
    font-size: .75rem;             /* was .78rem */
    font-weight: 500;
    line-height: 1.4;
    color: var(--text-primary);
    transition: background .2s, border-color .2s;
  }

  .sr-alert:empty {
    display: none;
  }

  .sr-alert strong {
    font-weight: 600;
    color: var(--text-heading);
  }

  /* ── Cards (left column) ─────────────────── */
  .fc,
  .rp-card {
    background: var(--card-bg);
    border: 1px solid var(--card-border);
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(16, 24, 40, .06);
    margin-bottom: 16px;
    transition: background .3s, border-color .3s;
  }

  .fc-head,
  .rp-head {
    display: flex;
    align-items: center;
    gap: 12px;
    margin: 0 18px;
    padding: 14px 0 12px;
    border-bottom: 1px solid var(--card-border);
  }

  .fc-step {
    width: 24px;
    height: 24px;
    border-radius: 50%;
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: .78rem;
    font-weight: 700;
    color: var(--text-heading);
    background: var(--sr-primary-soft);
  }

  .fc-head h6,
  .rp-head h6 {
    font-size: .95rem;
    font-weight: 700;
    color: var(--text-heading);
    margin: 0;
  }

  .fc-body,
  .rp-body {
    padding: 16px 18px 18px;
  }

  @media(max-width:575.98px) {

    .fc-head,
    .rp-head {
      margin: 0 14px;
    }

    .fc-body,
    .rp-body {
      padding: 14px;
    }
  }

  /* ── Form controls ───────────────────────── */
  :is(.sr-page, .ct-box) .form-label {
    font-size: .78rem;
    font-weight: 600;
    color: var(--text-heading);
    margin-bottom: 6px;
    display: block;
  }

  .req {
    color: #e5484d;
    margin-left: 2px;
  }

  :is(.sr-page, .ct-box) .form-control,
  :is(.sr-page, .ct-box) .form-select {
    font-size: .8125rem;
    border: 1px solid var(--border-color);
    border-radius: 6px;
    padding: .45rem .75rem;
    min-height: 36px;
    color: var(--text-primary);
    background-color: var(--input-bg);
    width: 100%;
    line-height: 1.5;
    transition: border-color .18s, box-shadow .18s, background-color .3s;
  }

  :is(.sr-page, .ct-box) select.form-select {
    appearance: none;
    -webkit-appearance: none;
    padding-right: 34px;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3E%3Cpath fill='none' stroke='%2364748b' stroke-width='1.6' stroke-linecap='round' stroke-linejoin='round' d='M3.5 6l4.5 4.5L12.5 6'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 12px center;
    background-size: 12px 12px;
  }

  :is(.sr-page, .ct-box) .form-control:focus,
  :is(.sr-page, .ct-box) .form-select:focus {
    border-color: var(--sr-primary);
    box-shadow: 0 0 0 3px var(--sr-primary-soft);
    outline: none;
  }

  :is(.sr-page, .ct-box) .form-control::placeholder {
    color: var(--text-light);
  }

  :is(.sr-page, .ct-box) .form-control:disabled,
  :is(.sr-page, .ct-box) .form-select:disabled {
    background-color: var(--surface-2);
    opacity: .6;
    cursor: not-allowed;
  }

  :is(.sr-page, .ct-box) .form-control[readonly] {
    background-color: var(--surface-2);
    cursor: default;
  }

  .sr-page textarea.form-control {
    resize: vertical;
    min-height: 96px;
  }

  .form-hint {
    font-size: .7rem;
    color: var(--text-muted);
    margin-top: 6px;
    display: block;
  }

  /* Customer lookup */
  .lk-wrap {
    position: relative;
  }

  .lk-wrap .lk-ic {
    position: absolute;
    left: 11px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--text-muted);
    font-size: .85rem;
    pointer-events: none;
  }

  .sr-page .lk-wrap .form-control {
    padding-left: 34px;
    padding-right: 34px;
  }

  .lk-spin,
  .lk-ok {
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
    color: var(--sr-primary);
  }

  .lk-ok {
    color: #05a34a;
    font-size: .95rem;
  }

  .lk-wrap.verifying .lk-spin,
  .lk-wrap.verified .lk-ok {
    display: block;
  }

  .sr-page .lk-wrap.verified .form-control {
    border-color: #05a34a;
    background-color: rgba(5, 163, 74, .03);
  }

  /* Verified customer / match list (full width under the three fields) */
  .client-reveal {
    display: none;
    margin-top: 14px;
    background: rgba(5, 163, 74, .06);
    border: 1px solid rgba(5, 163, 74, .22);
    border-radius: 6px;
    padding: 12px 14px;
    animation: fadeUp .22s ease;
  }

  .client-reveal.show {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
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

  /* Contact person select + add button */
  .ct-pick {
    display: flex;
    gap: 10px;
  }

  .ct-pick .form-select {
    flex: 1;
    min-width: 0;
  }

  .btn-add-ct {
    width: 36px;
    height: 36px;
    flex-shrink: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border: 1px solid var(--sr-primary);
    background: var(--card-bg);
    color: var(--sr-primary);
    border-radius: 6px;
    cursor: pointer;
    transition: background .15s, color .15s;
  }

  .btn-add-ct:hover:not(:disabled) {
    background: var(--sr-primary);
    color: #fff;
  }

  .btn-add-ct:disabled {
    opacity: .4;
    cursor: not-allowed;
  }

  /* Priority (radio look) */
  .priority-row {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 10px 28px;
    min-height: 36px;
  }

  .pr-pill {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
    font-size: .8125rem;
    font-weight: 500;
    color: var(--text-primary);
    user-select: none;
  }

  .pr-radio {
    position: relative;
    width: 16px;
    height: 16px;
    flex-shrink: 0;
    border-radius: 50%;
    border: 1.5px solid var(--text-light);
    background: var(--input-bg);
    transition: border-color .15s;
  }

  .pr-pill:hover .pr-radio,
  .pr-pill.on .pr-radio {
    border-color: var(--sr-primary);
  }

  .pr-pill.on .pr-radio::after {
    content: '';
    position: absolute;
    inset: 3px;
    border-radius: 50%;
    background: var(--sr-primary);
  }

  .pr-pill.on {
    color: var(--text-heading);
    font-weight: 600;
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
    color: #e5484d;
  }

  /* Dropzone */
  .dz {
    position: relative;
    border: 1.5px dashed var(--dz-border);
    border-radius: 8px;
    padding: 18px 14px;
    text-align: center;
    cursor: pointer;
    background: var(--dz-bg);
    transition: border-color .2s, background .2s;
  }

  .dz:hover,
  .dz.over {
    border-color: var(--sr-primary);
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

  .dz-txt {
    display: flex;
    align-items: center;
    justify-content: center;
    flex-wrap: wrap;
    gap: 6px;
    font-size: .8125rem;
    color: var(--text-primary);
    margin: 0;
  }

  .dz-txt strong {
    color: var(--sr-primary);
    font-weight: 600;
  }

  .dz-ic {
    font-size: 1.5rem;
    line-height: 1;
    color: var(--sr-primary);
    margin-right: 4px;
  }

  .dz-hint {
    font-size: .69rem;
    color: var(--text-muted);
    margin: 8px 0 0;
  }

  .dz-hint span {
    margin: 0 8px;
    opacity: .6;
  }

  /* File list */
  .file-list {
    margin-top: 10px;
  }

  .fitem {
    display: flex;
    align-items: center;
    gap: 10px;
    background: var(--file-bg);
    border: 1px solid var(--file-border);
    border-radius: 6px;
    padding: 7px 10px;
    margin-bottom: 6px;
    font-size: .79rem;
    animation: fadeUp .2s ease;
  }

  .fitem:hover {
    border-color: var(--text-muted);
  }

  .fitem .fi-thumb {
    width: 44px;
    height: 44px;
    object-fit: cover;
    border-radius: 6px;
    border: 1px solid var(--file-border);
    flex: 0 0 auto;
    display: block;
  }

  .fi-pdf {
    width: 44px;
    height: 44px;
    border-radius: 6px;
    flex: 0 0 auto;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(255, 51, 102, .1);
    color: #ff3366;
    font-size: 1.2rem;
  }

  .fi-name {
    flex: 1;
    min-width: 0;
    color: var(--text-heading);
    font-weight: 500;
    text-decoration: none;
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
    color: #e5484d;
  }

  /* ── Buttons ─────────────────────────────── */
  .btn-main,
  .btn-ghost {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 7px;
    border-radius: 6px;
    padding: .55rem 1.25rem;
    font-size: .8125rem;
    font-weight: 600;
    line-height: 1.4;
    cursor: pointer;
    transition: background .15s, border-color .15s, color .15s, transform .1s;
  }

  .btn-main {
    background: var(--sr-primary);
    border: 1px solid var(--sr-primary);
    color: #fff;
  }

  .btn-main:hover {
    background: var(--sr-primary-dark);
    border-color: var(--sr-primary-dark);
  }

  .btn-main:active {
    transform: scale(.98);
  }

  .btn-main:disabled {
    opacity: .55;
    cursor: not-allowed;
  }

  .btn-ghost {
    background: var(--card-bg);
    border: 1px solid var(--border-color);
    color: var(--text-heading);
  }

  .btn-ghost:hover {
    border-color: var(--text-muted);
    background: var(--surface-2);
  }

  .desk-actions {
    display: flex;
    gap: 12px;
    margin-top: 18px;
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
      display: none;
    }

    .main-content {
      padding-bottom: 86px !important;
    }
  }

  /* ── Right panel ─────────────────────────── */
  .rp-ic {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: .9rem;
    color: var(--sr-primary);
    background: var(--sr-primary-soft);
  }

  .rp-bulb {
    font-size: 1.15rem;
    color: var(--sr-primary);
  }

  .rp-badge {
    margin-left: auto;
    border-radius: 6px;
    font-size: .68rem;
    font-weight: 600;
    padding: 3px 9px;
    background: rgba(251, 188, 6, .14);
    color: #b88b00;
  }

  .pv-row {
    display: grid;
    grid-template-columns: 42% 1fr;
    gap: 10px;
    align-items: baseline;
    padding: 7px 0;
    font-size: .8rem;
  }

  .pv-k {
    color: var(--text-primary);
  }

  .pv-v {
    min-width: 0;
    font-weight: 600;
    color: var(--text-heading);
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
  }

  #pvPriority {
    color: var(--sr-primary);
  }

  /* Tips */
  .tip {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    margin-bottom: 16px;
  }

  .tip:last-child {
    margin-bottom: 0;
  }

  .tip-n {
    width: 22px;
    height: 22px;
    border-radius: 50%;
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: .7rem;
    font-weight: 700;
    color: #fff;
    background: var(--sr-primary-light);
  }

  .tip-t {
    font-size: .8rem;
    font-weight: 600;
    color: var(--text-heading);
    margin: 0 0 3px;
  }

  .tip-d {
    font-size: .72rem;
    color: var(--text-muted);
    line-height: 1.55;
    margin: 0;
  }

  /* Collapsible right panel (mobile) */
  .rp-toggle {
    display: none;
    width: 100%;
    background: var(--card-bg);
    border: 1px solid var(--card-border);
    border-radius: 8px;
    padding: 12px 15px;
    cursor: pointer;
    font-size: .8125rem;
    font-weight: 600;
    color: var(--text-heading);
    align-items: center;
    justify-content: space-between;
    margin-bottom: 13px;
    box-shadow: 0 1px 3px rgba(16, 24, 40, .06);
  }

  .rp-toggle span {
    display: flex;
    align-items: center;
    gap: 8px;
  }

  .rp-toggle span i {
    color: var(--sr-primary);
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
    border-left: 4px solid var(--sr-primary);
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
    border-color: #e5484d;
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
    color: var(--sr-primary);
  }

  .ti.success {
    color: #05a34a;
  }

  .ti.error {
    color: #e5484d;
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
    border-radius: 10px;
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
    background: var(--sr-primary-soft);
    border: 1px solid rgba(13, 142, 214, .3);
    border-radius: 6px;
    padding: 10px 18px;
    font-size: .9375rem;
    font-weight: 700;
    color: var(--sr-primary);
    display: inline-block;
    letter-spacing: .06em;
    margin-bottom: 16px;
  }

  /* ── Add contact modal ───────────────────── */
  .ct-overlay {
    display: none;
    position: fixed;
    inset: 0;
    z-index: 1200;
    align-items: center;
    justify-content: center;
    padding: 16px;
    background: rgba(15, 23, 42, .45);
    backdrop-filter: blur(3px);
  }

  .ct-overlay.show {
    display: flex;
  }

  .ct-box {
    background: var(--card-bg, #fff);
    border: 1px solid var(--card-border);
    border-radius: 10px;
    width: 100%;
    max-width: 420px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, .3);
  }

  .ct-hdr {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 14px 18px;
    border-bottom: 1px solid var(--card-border);
  }

  .ct-hdr h6 {
    margin: 0;
    font-size: .95rem;
    font-weight: 700;
    color: var(--text-heading);
    display: flex;
    align-items: center;
    gap: 8px;
  }

  .ct-hdr h6 i {
    color: var(--sr-primary);
  }

  .ct-x {
    background: none;
    border: 0;
    cursor: pointer;
    color: var(--text-muted);
  }

  .ct-body {
    padding: 16px 18px;
  }

  .ct-body .form-label+.form-control {
    margin-bottom: 12px;
  }

  .ct-phone {
    display: flex;
    gap: 8px;
  }

  .ct-phone .form-select {
    width: 96px;
    flex-shrink: 0;
  }

  .ct-phone .form-control {
    flex: 1;
    min-width: 0;
  }

  .ct-chk-lbl {
    display: flex;
    align-items: center;
    gap: 8px;
    margin: 18px 0 0;
    font-size: .8125rem;
    color: var(--text-primary);
    cursor: pointer;
  }

  .ct-chk-lbl input {
    accent-color: var(--sr-primary);
  }

  .ct-foot {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    padding: 12px 18px;
    border-top: 1px solid var(--card-border);
  }

  /* Needed for the contact modal stacking */
  .sidebar {
    z-index: 1000;
  }

  body.modal-open {
    overflow: hidden;
  }

  body.modal-open .sidebar,
  body.modal-open .topbar {
    pointer-events: none;
  }
</style>
@endpush

@section('content')
@php
    $canViewTriage = $canViewTriage ?? auth()->user()->hasAnyAccess('inquiry_approval');
@endphp

{{-- Toasts --}}
<div class="toast-shelf" id="toastShelf"></div>

{{-- Success overlay --}}
<div class="sr-overlay" id="srOverlay">
  <div class="sr-modal">
    <div class="ok-ring"><i class="bi bi-check-lg"></i></div>
    <h5>Ticket Submitted!</h5>
    <p>Your service request is logged and set to <strong>Pending</strong>.</p>
    <div class="sr-id" id="srIdOut">SR-0000-00000</div>
    <p style="font-size:.76rem;margin-bottom:18px;">
      <i class="bi bi-check-circle-fill me-1" style="color:#05a34a;"></i>Timestamped and saved in the system.
    </p>
    <button class="btn-main w-100 mb-2" onclick="goHub()">
      <i class="bi bi-grid-1x2"></i>{{ $canViewTriage ? 'Inquiry Approvals' : 'View My Tickets' }}
    </button>
    <button class="btn-ghost w-100" onclick="newTicket()">
      <i class="bi bi-plus-circle"></i>Register Another SR
    </button>
  </div>
</div>

{{-- Add Contact Modal --}}
<div class="ct-overlay" id="ctModal" onclick="if(event.target===this)closeCtModal()">
  <div class="ct-box" role="dialog" aria-modal="true">
    <div class="ct-hdr">
      <h6><i class="bi bi-person-plus"></i>Add Contact Person</h6>
      <button type="button" class="ct-x" onclick="closeCtModal()"><i class="bi bi-x-lg"></i></button>
    </div>
    <div class="ct-body">
      <label class="form-label" for="ctName">Name <span class="req">*</span></label>
      <input type="text" class="form-control" id="ctName" placeholder="e.g. John Facilities">

      <label class="form-label" for="ctMobile">WhatsApp Number <span class="req">*</span></label>
      <div class="ct-phone">
        <select class="form-select" id="ctCountry">
          <option value="+971">+971</option>
          <option value="+91" selected>+91</option>
          <option value="+1">+1</option>
          <option value="+44">+44</option>
          <option value="+966">+966</option>
          <option value="+974">+974</option>
        </select>
        <input type="tel" class="form-control" id="ctMobile" placeholder="50 123 4567"
               inputmode="numeric" maxlength="15"
               oninput="this.value=this.value.replace(/[^0-9]/g,'')">
      </div>

      <label class="ct-chk-lbl">
        <input type="checkbox" id="ctNotify" value="1">
        <span>Send WhatsApp updates</span>
      </label>
    </div>
    <div class="ct-foot">
      <button type="button" class="btn-ghost" onclick="closeCtModal()">Cancel</button>
      <button type="button" class="btn-main" id="ctSaveBtn" onclick="saveContact()">
        <i class="bi bi-check-lg"></i>Save Contact
      </button>
    </div>
  </div>
</div>

{{-- Main --}}
<main class="main-content sr-page">

  {{-- ── Page title + status line ── --}}
  {{-- <div class="sr-top">
    <div>
      <h4 class="sr-title">SR Registration</h4>
      <p class="sr-sub">Create a service request</p>
    </div>
    <div class="sr-alert" id="srAlert"></div>
  </div> --}}

  <form id="srForm" enctype="multipart/form-data">
    @csrf
    <div class="row g-3 g-lg-4 align-items-start">

      {{-- ════════════════ LEFT COLUMN ════════════════ --}}
      <div class="col-lg-8">

        {{-- ── CARD 1 · Customer & Project ── --}}
        <div class="fc">
          <div class="fc-head">
            <span class="fc-step">1</span>
            <h6>Customer &amp; Project</h6>
          </div>
          <div class="fc-body">
            <div class="row g-3">

              {{-- Lookup --}}
              <div class="col-12 col-xl-4">
                <label class="form-label" for="custCode">Customer Lookup <span class="req">*</span></label>
                <div class="lk-wrap" id="lkWrap">
                  <i class="bi bi-search lk-ic"></i>
                  <input type="text" class="form-control" id="custCode" name="customer_code"
                    placeholder="Enter customer name, code or mobile number"
                    maxlength="60" autocomplete="off"
                    oninput="onCode(this.value)" />
                  <div class="lk-spin">
                    <div class="spinner-border"></div>
                  </div>
                  <i class="bi bi-check-circle-fill lk-ok"></i>
                </div>
                <span class="form-hint">Search by customer name, code or mobile number</span>
                {{-- hidden fields posted to controller --}}
                <input type="hidden" id="customerId" name="customer_id" />
                <input type="hidden" id="customerName" name="customer_name" />
              </div>

              {{-- Project --}}
              <div class="col-sm-6 col-xl-4">
                <label class="form-label" for="projSel">Project <span class="req">*</span></label>
                <select class="form-select" id="projSel" name="project_id" disabled onchange="onProject(this.value)">
                  <option value="">- Select project -</option>
                </select>
                <span class="form-hint">All projects for selected customer</span>
              </div>

              {{-- Site (auto-filled, read-only) --}}
              <div class="col-sm-6 col-xl-4">
                <label class="form-label" for="siteField">Site Location <span class="req">*</span></label>
                <input type="text" class="form-control" id="siteField" name="project_site"
                  placeholder="Auto-filled from project" readonly />
                <span class="form-hint">Auto-filled from selected project</span>
              </div>

            </div>

            {{-- Verified customer details / match list --}}
            <div class="client-reveal" id="clientReveal">
              <div><span class="ci-lbl">Customer Name</span><span class="ci-val" id="cName">-</span></div>
              <div><span class="ci-lbl">Status</span><span class="ci-val" id="cStatus" style="color:#05a34a;">-</span></div>
              <div><span class="ci-lbl">Contact Person</span><span class="ci-val" id="cFlag">-</span></div>
              <div><span class="ci-lbl">Contact</span><span class="ci-val" id="cContact">-</span></div>
            </div>
            <div class="sr-alert" id="srAlert"></div>
          </div>
          
        </div>
        
        {{-- ── CARD 2 · Service Information ── --}}
        <div class="fc">
          <div class="fc-head">
            <span class="fc-step">2</span>
            <h6>Service Information</h6>
          </div>
          <div class="fc-body">
            <div class="row g-3">

              {{-- Service Category --}}
              <div class="col-md-6">
                <label class="form-label" for="svcType">Service Category <span class="req">*</span></label>
                <select class="form-select" id="svcType" name="service_type_id"
                  onchange="pv('pvType', this.options[this.selectedIndex].text)">
                  <option value="">- Select Category -</option>
                  @foreach($categories as $category)
                  <option value="{{ $category->id }}">
                    {{ $category->category_name }}
                  </option>
                  @endforeach
                </select>
              </div>

              {{-- Contact Person --}}
              <div class="col-md-6">
                <label class="form-label" for="reporter">Contact Person <span class="req">*</span></label>
                <div class="ct-pick">
                  <select class="form-select" id="reporter" name="reported_by" onchange="onReporterChange(this)">
                    <option value="">Select customer first…</option>
                  </select>
                  <button type="button" class="btn-add-ct" id="addCtBtn" onclick="openCtModal()" title="Add contact person" disabled>
                    <i class="bi bi-plus-lg"></i>
                  </button>
                </div>
                <input type="hidden" name="reported_by_mobile" id="reporterMobile">
              </div>

              {{-- Priority --}}
              <div class="col-12">
                <label class="form-label">Priority <span class="req">*</span></label>
                <div class="priority-row" id="prGroup">
                  @foreach($priorities as $priority)
                    <div class="pr-pill" onclick="setPriority(this, '{{ $priority->name }}')">
                      <span class="pr-radio"></span>{{ $priority->name }}
                    </div>
                  @endforeach
                </div>
                <input type="hidden" id="priorityVal" name="priority_level" />
              </div>

            </div>
          </div>
        </div>

        {{-- ── CARD 3 · Request Details ── --}}
        <div class="fc">
          <div class="fc-head">
            <span class="fc-step">3</span>
            <h6>Request Details</h6>
          </div>
          <div class="fc-body">
            <label class="form-label" for="svcDesc">Description <span class="req">*</span></label>
            <textarea class="form-control" id="svcDesc" name="issue_description" rows="4"
              placeholder="Describe the issue in detail (minimum 20 characters)"
              oninput="onDesc(this)"></textarea>
            <div class="d-flex justify-content-between align-items-center">
              <span class="form-hint">Minimum 20 characters</span>
              <span class="cc warn" id="ccCount">0 / 20</span>
            </div>

            {{-- Internal remark is still switched off, same as before
            <div class="mt-3">
              <label class="form-label">Internal Remark (Optional)</label>
              <textarea class="form-control" id="remark" name="internal_remark" rows="3"
                placeholder="Add any internal notes (optional)"></textarea>
            </div>
            --}}
          </div>
        </div>

        {{-- ── CARD 4 · Attachments + actions ── --}}
        <div class="fc">
          <div class="fc-head">
            <span class="fc-step">4</span>
            <h6>Attachments</h6>
          </div>
          <div class="fc-body">
            <div class="dz" id="dzBox" ondragover="dzOn(event)" ondragleave="dzOff()" ondrop="dzDrop(event)">
              <input type="file" id="fileInput" multiple accept=".jpg,.jpeg,.png,.pdf" onchange="onFiles(this.files)" />
              <p class="dz-txt">
                <i class="bi bi-cloud-arrow-up dz-ic"></i><strong>Choose files</strong> or drag them here
              </p>
              <p class="dz-hint">Supported formats: JPG, PNG, PDF <span>|</span> Max 10 MB each <span>|</span> Optional</p>
            </div>
            <div class="file-list" id="fileList"></div>

            {{-- Desktop actions --}}
            <div class="desk-actions">
              <button type="button" class="btn-main" onclick="doSubmit()" id="deskBtn">Submit Service Request</button>
              <button type="button" class="btn-ghost" onclick="clearAll()">Reset</button>
            </div>
          </div>
        </div>

      </div>{{-- /col-lg-8 --}}

      {{-- ════════════════ RIGHT COLUMN ════════════════ --}}
      <div class="col-lg-4">

        {{-- Mobile toggle --}}
        <button type="button" class="rp-toggle" onclick="toggleRP(this)">
          <span><i class="bi bi-file-earmark-text"></i>Request Summary</span>
          <i class="bi bi-chevron-down" id="rpChevron" style="transition:transform .25s;color:var(--text-muted);"></i>
        </button>

        <div class="rp-body-wrap" id="rpWrap">

          {{-- Request Summary --}}
          <div class="rp-card">
            <div class="rp-head">
              <span class="rp-ic"><i class="bi bi-file-earmark-text"></i></span>
              <h6>Request Summary</h6>
              <span class="rp-badge">• Draft</span>
            </div>
            <div class="rp-body">
              <div class="pv-row"><span class="pv-k">Customer</span><span class="pv-v" id="pvCode">-</span></div>
              <div class="pv-row"><span class="pv-k">Project</span><span class="pv-v" id="pvProject">-</span></div>
              <div class="pv-row"><span class="pv-k">Site</span><span class="pv-v" id="pvSite">-</span></div>
              <div class="pv-row"><span class="pv-k">Category</span><span class="pv-v" id="pvType">-</span></div>
              <div class="pv-row"><span class="pv-k">Contact Person</span><span class="pv-v" id="pvReporter">-</span></div>
              <div class="pv-row"><span class="pv-k">Priority</span><span class="pv-v" id="pvPriority">-</span></div>
              <div class="pv-row"><span class="pv-k">Attachments</span><span class="pv-v" id="pvFiles">0 files</span></div>
              <div class="pv-row"><span class="pv-k">Description</span><span class="pv-v" id="pvDesc">-</span></div>
            </div>
          </div>

          {{-- Before submitting --}}
          <div class="rp-card">
            <div class="rp-head">
              <i class="bi bi-lightbulb rp-bulb"></i>
              <h6>Before submitting</h6>
            </div>
            <div class="rp-body">
              <div class="tip">
                <span class="tip-n">1</span>
                <div>
                  <p class="tip-t">Verify customer and project</p>
                  <p class="tip-d">Make sure the customer and project are correct before submitting.</p>
                </div>
              </div>
              <div class="tip">
                <span class="tip-n">2</span>
                <div>
                  <p class="tip-t">Describe the issue clearly</p>
                  <p class="tip-d">Provide detailed information to help the team understand and resolve faster.</p>
                </div>
              </div>
              <div class="tip">
                <span class="tip-n">3</span>
                <div>
                  <p class="tip-t">Attach photos or PDFs if helpful</p>
                  <p class="tip-d">Add relevant files to support your request (if available).</p>
                </div>
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
  <button type="button" class="btn-ghost" style="flex:1;" onclick="clearAll()">Reset</button>
  <button type="button" class="btn-main" style="flex:2;" onclick="doSubmit()" id="mobBtn">Submit Service Request</button>
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
    setAlert('warning', 'Search a customer to begin.');
  });

  /* ── Alert bar ── */
   function setAlert(type, msg) {
    const el = document.getElementById('srAlert');
    const m = {
      warning: {                          // the "search a customer" prompts: now blue, not yellow
        bg: 'rgba(13,142,214,.07)',
        bd: 'rgba(13,142,214,.22)',
        c: '#0d8ed6',
        i: 'bi-info-circle-fill'          // was bi-exclamation-circle-fill
      },
      success: {
        bg: 'rgba(5,163,74,.07)',
        bd: 'rgba(5,163,74,.22)',
        c: '#05a34a',
        i: 'bi-check-circle-fill'
      },
      danger: {
        bg: 'rgba(229,72,77,.07)',        // was rgba(255,51,102,.07)
        bd: 'rgba(229,72,77,.25)',
        c: '#e5484d',                     // was #ff3366
        i: 'bi-x-circle-fill'
      },
      primary: {
        bg: 'rgba(13,142,214,.07)',       // was rgba(18,165,220,.07)
        bd: 'rgba(13,142,214,.22)',
        c: '#0d8ed6',                     // was #12A5DC
        i: 'bi-info-circle-fill'
      },
    } [type] || {};
    el.style.cssText = `background:${m.bg};border-color:${m.bd};`;
    el.innerHTML = `<i class="bi ${m.i}" style="color:${m.c};font-size:.85rem;flex-shrink:0;"></i><span>${msg}</span>`;
  }

  /* ── Contact person modal ── */

  const CONTACT_STORE_URL = "{{ url('/service-requests/contacts') }}"; // POST

  function openCtModal() {
    if (!window.__clientId) { toast('error','Validation','Verify a customer first.'); return; }

    const m = document.getElementById('ctModal');

    // Escape any stacking context created by .main / .page-content
    if (m.parentElement !== document.body) document.body.appendChild(m);

    document.getElementById('ctName').value      = '';
    document.getElementById('ctMobile').value    = '';
    document.getElementById('ctNotify').checked  = false;

    m.classList.add('show');
    document.body.classList.add('modal-open');

    setTimeout(() => document.getElementById('ctName').focus(), 50);
  }

  function closeCtModal() {
    document.getElementById('ctModal').classList.remove('show');
    document.body.classList.remove('modal-open');
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
    pv('pvReporter', '-');
  }

  function onReporterChange(sel) {
    pv('pvReporter', sel.value || '-');
  }

  /* ── Lookup (debounced) ── */

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
    pv('pvCode', '-');
    pv('pvProject', '-');
    pv('pvSite', '-');
    setAlert('warning', 'Search by customer name, code or mobile.');

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

    // Already verified this exact value - don't re-run or re-toast
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
          <div><span class="ci-lbl">Customer Name</span><span class="ci-val" id="cName">-</span></div>
          <div><span class="ci-lbl">Status</span><span class="ci-val" id="cStatus" style="color:#05a34a;">-</span></div>
          <div><span class="ci-lbl">Contact Person</span><span class="ci-val" id="cFlag">-</span></div>
          <div><span class="ci-lbl">Contact</span><span class="ci-val" id="cContact">-</span></div>`;
        document.getElementById('cName').textContent = c.name ?? '-';
        document.getElementById('cStatus').textContent = c.status ?? '-';
        document.getElementById('cFlag').textContent = c.flag ?? '-';
        document.getElementById('cContact').textContent = c.contact ?? '-';
        document.getElementById('customerId').value = c.id ?? '';
        document.getElementById('customerName').value = c.name ?? '';
        window.__clientId = c.id;
        document.getElementById('clientReveal').classList.add('show');

        fillReporters(c.contacts);

        const ps = document.getElementById('projSel');
        ps.innerHTML = '<option value="">- Select project -</option>';

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
        pv('pvSite', '-');

        pv('pvCode', c.name ?? term);
        setAlert('success', `Verified: <strong>${c.name}</strong> - select a project to continue.`);
        toast('success', 'Customer Verified', c.name);
      } else {
        lastVerifiedTerm = null;
        setAlert('danger', 'No customer found. Check the name, code or mobile.');
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
    setAlert('primary', `${list.length} matches - click the customer you want.`);
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
    pv('pvProject', proj ? proj.name : '-');
    const sites = proj && proj.sites ? proj.sites : [];
    const firstSite = sites.length ? sites[0] : '';
    document.getElementById('siteField').value = firstSite;   // auto-fill first site (read-only)
    pv('pvSite', firstSite || '-');
  }

  function resetSel(id, lbl) {
    const el = document.getElementById(id);
    el.innerHTML = `<option value="">- Select ${lbl} -</option>`;
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
    pv('pvDesc', len > 0 ? el.value.substring(0, 28) + (len > 28 ? '…' : '') : '-');
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
    // fd.append('internal_remark', document.getElementById('remark').value);
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
      document.getElementById('srIdOut').textContent = data.sr_reference || '-';
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
        'Submit Service Request';   // label only: was "Submit Ticket"
    });
  }

  /* ── Post-submit ── */
  const CAN_VIEW_TRIAGE = @json($canViewTriage);

  function goHub() {
    document.getElementById('srOverlay').classList.remove('show');
    clearAll();
    window.location.href = CAN_VIEW_TRIAGE
        ? "{{ route('inquiry-approval.index') }}"
        : "{{ route('sr_explorer') }}";   // or dashboard, or wherever SE/AC should land
  }

  function newTicket() {
    document.getElementById('srOverlay').classList.remove('show');
    clearAll();
    toast('primary', 'Ready', 'Form cleared - register a new SR.');
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

    document.getElementById('ccCount').textContent = '0 / 20';
    document.getElementById('ccCount').className = 'cc warn';
    document.getElementById('priorityVal').value = '';
    document.querySelectorAll('#prGroup .pr-pill').forEach(p => p.classList.remove('on'));
    uploads = [];
    renderFiles();
    ['pvCode', 'pvProject', 'pvSite', 'pvType', 'pvReporter', 'pvPriority', 'pvDesc'].forEach(id => pv(id, '-'));
    pv('pvFiles', '0 files');
    setAlert('warning', 'Search a customer to begin.');
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