@extends('layouts.layout')

@section('title', 'Project & Site Directory')
@section('page_title')
Project &amp; Site<span class="hide-mobile"> Directory</span>
@endsection
@section('page_icon', 'briefcase')


@push('styles')
<style>
  /* ═══ THEME TOKENS ═══ */


  @media (max-width: 576px) {
    .hide-mobile {
      display: none;
    }
  }

  /* ═══ SIDEBAR ═══ */
  .sb-brand {
    display: flex;
    align-items: center;
    gap: 11px;
    padding: 18px 20px 15px;
    border-bottom: 1px solid var(--border-color);
    flex-shrink: 0;
  }

  .sb-brand-icon {
    width: 38px;
    height: 38px;
    flex-shrink: 0;
    background: linear-gradient(135deg, #9A7B4F, #C4A882);
    border-radius: 9px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-size: 1.05rem;
  }

  .sb-brand-name {
    font-size: .9375rem;
    font-weight: 700;
    color: var(--text-heading);
    line-height: 1.2;
  }

  .sb-brand-sub {
    font-size: .6875rem;
    color: var(--text-muted);
  }

  .sb-nav {
    flex: 1;
    overflow-y: auto;
    padding: 10px 0;
  }

  .sb-nav::-webkit-scrollbar {
    width: 3px;
  }

  .sb-nav::-webkit-scrollbar-thumb {
    background: var(--border-color);
    border-radius: 2px;
  }

  .sb-section {
    font-size: .625rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .1em;
    color: var(--text-light);
    padding: 14px 20px 4px;
  }

  .sb-nav a {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 9px 20px;
    font-size: .8125rem;
    color: var(--nav-link);
    border-right: 3px solid transparent;
    transition: background .15s, color .15s;
  }

  .sb-nav a:hover {
    background: var(--surface-2);
    color: #9A7B4F;
  }

  .sb-nav a.active {
    background: rgba(154, 123, 79, .1);
    color: #9A7B4F;
    font-weight: 500;
    border-right-color: #9A7B4F;
  }

  .sb-nav a i {
    font-size: 1rem;
    width: 18px;
    text-align: center;
    flex-shrink: 0;
  }

  .sb-badge {
    font-size: .6rem;
    font-weight: 700;
    padding: 1px 6px;
    border-radius: 9px;
    margin-left: auto;
  }

  .sb-badge.amber {
    background: rgba(217, 119, 6, .15);
    color: #d97706;
  }

  .sb-footer {
    padding: 14px 18px;
    border-top: 1px solid var(--border-color);
    flex-shrink: 0;
  }

  .sb-user {
    display: flex;
    align-items: center;
    gap: 10px;
  }

  .sb-avatar {
    width: 34px;
    height: 34px;
    flex-shrink: 0;
    background: linear-gradient(135deg, #9A7B4F, #C4A882);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-size: .75rem;
    font-weight: 700;
  }

  .sb-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, .45);
    z-index: 299;
    backdrop-filter: blur(2px);
  }

  .sb-overlay.show {
    display: block;
  }

  @media(max-width:991.98px) {
    .sidebar {
      transform: translateX(-100%);
    }

    .sidebar.open {
      transform: translateX(0);
    }
  }

  /* ═══ TOPBAR ═══ */
  .topbar {
    position: fixed;
    top: 0;
    left: var(--sidebar-width);
    right: 0;
    height: 60px;
    background: var(--topbar-bg);
    border-bottom: 1px solid var(--border-color);
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 22px;
    z-index: 200;
    box-shadow: var(--topbar-shadow);
  }

  .topbar-left {
    display: flex;
    align-items: center;
    gap: 12px;
  }

  .hamburger {
    display: none;
    background: none;
    border: none;
    padding: 6px;
    color: var(--text-heading);
    cursor: pointer;
    border-radius: 6px;
    font-size: 1.25rem;
    line-height: 1;
  }

  .hamburger:hover {
    background: var(--surface-2);
  }

  .mobile-brand {
    display: none;
  }

  .pt-main {
    font-size: .9375rem;
    font-weight: 600;
    color: var(--text-heading);
    display: flex;
    align-items: center;
    gap: 7px;
  }

  .topbar .breadcrumb {
    margin: 0;
    font-size: .72rem;
    padding: 0;
  }

  .topbar .breadcrumb-item+.breadcrumb-item::before {
    content: "/";
    color: var(--text-light);
  }

  .topbar .breadcrumb-item.active {
    color: var(--text-muted);
  }

  .topbar .breadcrumb-item a {
    color: #9A7B4F;
  }

  .topbar-right {
    display: flex;
    align-items: center;
    gap: 10px;
  }

  .role-badge {
    font-size: .72rem;
    background: rgba(154, 123, 79, .12);
    color: #9A7B4F;
    padding: 3px 10px;
    border-radius: 20px;
    font-weight: 500;
    white-space: nowrap;
  }

  .clock-d {
    font-size: .72rem;
    color: var(--text-muted);
    white-space: nowrap;
  }

  .t-div {
    width: 1px;
    height: 22px;
    background: var(--border-color);
    flex-shrink: 0;
  }

  .av-btn {
    width: 34px;
    height: 34px;
    background: linear-gradient(135deg, #9A7B4F, #C4A882);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-size: .8rem;
    font-weight: 700;
    cursor: pointer;
  }

  .th-toggle {
    display: flex;
    align-items: center;
    gap: 7px;
    cursor: pointer;
    user-select: none;
  }

  .th-sun {
    color: #fbbc06;
    font-size: .8rem;
  }

  .th-moon {
    color: #C4A882;
    font-size: .8rem;
  }

  .tt-track {
    width: 42px;
    height: 22px;
    background: var(--toggle-track);
    border-radius: 11px;
    position: relative;
    transition: background .3s;
    border: 1px solid var(--border-color);
  }

  .tt-thumb {
    width: 16px;
    height: 16px;
    background: #fff;
    border-radius: 50%;
    position: absolute;
    top: 2px;
    left: 2px;
    transition: transform .3s, background .3s;
    box-shadow: 0 1px 4px rgba(0, 0, 0, .2);
    display: flex;
    align-items: center;
    justify-content: center;
  }

  [data-bs-theme="dark"] .tt-thumb {
    transform: translateX(20px);
    background: #9A7B4F;
  }

  .ts-sun {
    font-size: 8px;
    color: #fbbc06;
  }

  .ts-moon {
    font-size: 8px;
    color: #fff;
    display: none;
  }

  [data-bs-theme="dark"] .ts-sun {
    display: none;
  }

  [data-bs-theme="dark"] .ts-moon {
    display: block;
  }

  @media(max-width:991.98px) {
    .topbar {
      left: 0;
    }

    .hamburger {
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .mobile-brand {
      display: flex;
      align-items: center;
      gap: 9px;
    }

    .pt-wrap {
      display: none;
    }

    .role-badge,
    .clock-d,
    .t-div {
      display: none;
    }
  }

  /* ═══ MAIN ═══ */
  .main-content {
    margin-left: var(--sidebar-width);
    margin-top: 60px;
    padding: 22px 22px 48px;
    min-height: calc(100vh - 60px);
  }

  @media(max-width:991.98px) {
    .main-content {
      margin-left: 0;
    }
  }

  @media(max-width:575.98px) {
    .main-content {
      padding: 14px 12px 48px;
    }
  }

  /* ═══ PAGE HEADER ═══ */
  .pg-header {
    border-radius: 10px;
    padding: 18px 24px;
    margin-bottom: 20px;
    color: #fff;
    position: relative;
    overflow: hidden;
    background: linear-gradient(135deg, #9A7B4F 0%, #7A6140 100%);
  }

  .pg-header::before {
    content: '';
    position: absolute;
    left: -40px;
    bottom: -40px;
    width: 180px;
    height: 180px;
    border-radius: 50%;
    background: rgba(255, 255, 255, .05);
  }

  .pg-header::after {
    content: '';
    position: absolute;
    right: -30px;
    top: -30px;
    width: 160px;
    height: 160px;
    border-radius: 50%;
    background: rgba(255, 255, 255, .07);
  }

  .pg-header h4 {
    font-size: 1rem;
    font-weight: 600;
    margin: 0 0 3px;
    position: relative;
    z-index: 1;
  }

  .pg-header p {
    font-size: .78rem;
    margin: 0;
    opacity: .85;
    position: relative;
    z-index: 1;
  }

  .pg-header .meta-row {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-top: 9px;
    position: relative;
    z-index: 1;
    flex-wrap: wrap;
  }

  .meta-badge {
    background: rgba(255, 255, 255, .18);
    border: 1px solid rgba(255, 255, 255, .3);
    border-radius: 20px;
    font-size: .6875rem;
    padding: 2px 10px;
    font-weight: 500;
  }

  /* ═══ STATS ═══ */
  .stats-strip {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 10px;
    margin-bottom: 18px;
  }

  .stat-card {
    background: var(--card-bg);
    border: 1px solid var(--card-border);
    border-radius: 9px;
    padding: 13px 15px;
    display: flex;
    align-items: center;
    gap: 12px;
    box-shadow: var(--card-shadow);
  }

  .stat-icon {
    width: 38px;
    height: 38px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
    flex-shrink: 0;
  }

  .stat-num {
    font-size: 1.5rem;
    font-weight: 700;
    line-height: 1;
    color: var(--text-heading);
  }

  .stat-lbl {
    font-size: .7rem;
    color: var(--text-muted);
  }

  @media(max-width:767px) {
    .stats-strip {
      grid-template-columns: repeat(2, 1fr);
    }
  }

  /* ═══ FILTER BAR ═══ */
  .filter-bar {
    background: var(--card-bg);
    border: 1px solid var(--card-border);
    border-radius: 10px;
    padding: 14px 16px;
    margin-bottom: 16px;
    box-shadow: var(--card-shadow);
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    align-items: flex-end;
  }

  .filter-group {
    display: flex;
    flex-direction: column;
    gap: 4px;
    min-width: 0;
    flex: 1 1 auto;
  }

  .filter-label {
    font-size: .7rem;
    font-weight: 600;
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: .05em;
  }

  .filter-control {
    height: 34px;
    padding: 0 10px;
    border: 1px solid var(--border-color);
    border-radius: 7px;
    background: var(--input-bg);
    color: var(--text-primary);
    font-size: .8rem;
    min-width: 140px;
    transition: border-color .15s;
  }

  .filter-control:focus {
    outline: none;
    border-color: #9A7B4F;
    box-shadow: var(--input-focus-shadow);
  }

  .filter-actions {
    margin-left: auto;
    display: flex;
    gap: 8px;
    align-items: flex-end;
  }

  /* ═══ TABLE CARD ═══ */
  .tbl-card {
    background: var(--card-bg);
    border: 1px solid var(--card-border);
    border-radius: 10px;
    box-shadow: var(--card-shadow);
    overflow: hidden;
  }

  .tbl-card-hdr {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 13px 18px;
    border-bottom: 1px solid var(--border-color);
    flex-wrap: wrap;
    gap: 10px;
  }

  .tbl-card-hdr-left {
    display: flex;
    align-items: center;
    gap: 10px;
  }

  .tbl-card-title {
    font-size: .875rem;
    font-weight: 600;
    color: var(--text-heading);
     font-family: var(--font-header);
  }

  .result-count {
    font-size: .75rem;
    color: var(--text-muted);
    background: var(--surface-2);
    padding: 2px 9px;
    border-radius: 9px;
  }

  .tbl-wrap {
    overflow-x: auto;
  }

  table.listing {
    width: 100%;
    border-collapse: collapse;
  }

  table.listing thead th {
    padding: 10px 16px;
    font-size: .7rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .07em;
    color: var(--text-muted);
    background: var(--table-header);
    border-bottom: 1px solid var(--border-color);
    white-space: nowrap;
  }

  table.listing thead th.sortable {
    cursor: pointer;
    user-select: none;
  }

  table.listing thead th.sortable:hover {
    color: #9A7B4F;
  }

  table.listing tbody tr {
    border-bottom: 1px solid var(--border-color);
    transition: background .1s;
    cursor: default;
  }

  table.listing tbody tr:last-child {
    border-bottom: none;
  }

  table.listing tbody tr:hover {
    background: var(--table-hover);
  }

  table.listing td {
    padding: 12px 16px;
    font-size: .8125rem;
    color: var(--text-primary);
    vertical-align: middle;
  }

  table.listing td.muted {
    color: var(--text-muted);
    font-size: .78rem;
  }

  table.listing td.mono {
    font-size: .78rem;
    font-weight: 600;
    color: #9A7B4F;
  }

  .row-actions {
    display: flex;
    gap: 5px;
    transition: opacity .12s;
  }

  table.listing tr:hover .row-actions {
    opacity: 1;
  }

  /* ═══ BADGES ═══ */
  .sbadge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 3px 10px;
    border-radius: 20px;
    font-size: .7rem;
    font-weight: 600;
    white-space: nowrap;
  }

  .sb-active {
    background: rgba(16, 185, 129, .12);
    color: #059669;
  }

  .sb-inactive {
    background: rgba(156, 163, 175, .1);
    color: #9ca3af;
  }

  /* ═══ BUTTONS ═══ */
  .btn-gold {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 7px 14px;
    background: linear-gradient(135deg, #9A7B4F, #C4A882);
    color: #fff;
    border: none;
    border-radius: 7px;
    font-size: .8rem;
    font-weight: 500;
    cursor: pointer;
    white-space: nowrap;
    transition: opacity .15s;
  }

  .btn-gold:hover {
    opacity: .87;
  }

  .btn-ghost {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 7px 12px;
    background: var(--surface-2);
    color: var(--text-muted);
    border: 1px solid var(--border-color);
    border-radius: 7px;
    font-size: .8rem;
    cursor: pointer;
    white-space: nowrap;
  }

  .btn-ghost:hover {
    background: var(--surface-3);
  }

  .btn-xs {
    padding: 4px 9px;
    border-radius: 5px;
    font-size: .75rem;
    border: none;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    transition: background .12s;
  }

  .btn-xs-edit {
    background: rgba(154, 123, 79, .1);
    color: #9A7B4F;
  }

  .btn-xs-edit:hover {
    background: rgba(154, 123, 79, .2);
  }

  .btn-xs-view {
    background: rgba(37, 99, 235, .1);
    color: #3b82f6;
  }

  .btn-xs-view:hover {
    background: rgba(37, 99, 235, .2);
  }

  .btn-xs-del {
    background: rgba(239, 68, 68, .08);
    color: #ef4444;
  }

  .btn-xs-del:hover {
    background: rgba(239, 68, 68, .15);
  }

  /* ═══ PAGINATION ═══ */
  .pagination-bar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 12px 18px;
    border-top: 1px solid var(--border-color);
    flex-wrap: wrap;
    gap: 8px;
  }

  .page-info {
    font-size: .78rem;
    color: var(--text-muted);
  }

  .page-btns {
    display: flex;
    gap: 4px;
  }

  .page-btn {
    width: 30px;
    height: 30px;
    border-radius: 6px;
    border: 1px solid var(--border-color);
    background: var(--card-bg);
    color: var(--text-muted);
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: .78rem;
    transition: all .12s;
  }

  .page-btn:hover {
    border-color: #9A7B4F;
    color: #9A7B4F;
  }

  .page-btn.active {
    background: #9A7B4F;
    color: #fff;
    border-color: #9A7B4F;
  }

  /* ═══ MODAL ═══ */
  .modal-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: var(--overlay-bg);
    z-index: 900;
    align-items: flex-start;
    justify-content: center;
    backdrop-filter: blur(3px);
    padding: 40px 16px 24px;
    overflow-y: auto;
  }

  .modal-overlay.show {
    display: flex;
  }

  .modal-box {
    background: var(--modal-bg);
    border-radius: 12px;
    width: 100%;
    max-width: 560px;
    box-shadow: var(--modal-shadow);
    border: 1px solid var(--card-border);
    overflow: hidden;
    animation: modalIn .2s ease;
    margin: auto;
  }

  @keyframes modalIn {
    from {
      opacity: 0;
      transform: translateY(-12px) scale(.98);
    }

    to {
      opacity: 1;
      transform: translateY(0) scale(1);
    }
  }

  .modal-hdr {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 16px 20px;
    border-bottom: 1px solid var(--border-color);
  }

  .modal-hdr-left {
    display: flex;
    align-items: center;
    gap: 11px;
  }

  .modal-hdr-icon {
    width: 36px;
    height: 36px;
    border-radius: 9px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
    background: rgba(124, 58, 237, .12);
    color: #7c3aed;
    flex-shrink: 0;
  }

  .modal-hdr h6 {
    font-size: .9rem;
    font-weight: 600;
    color: var(--text-heading);
    margin: 0;
  }

  .modal-hdr-sub {
    font-size: .72rem;
    color: var(--text-muted);
    margin: 1px 0 0;
  }

  .modal-close {
    background: none;
    border: none;
    color: var(--text-muted);
    cursor: pointer;
    padding: 5px;
    border-radius: 6px;
    font-size: 1rem;
    line-height: 1;
  }

  .modal-close:hover {
    background: var(--surface-2);
    color: var(--text-primary);
  }

  .modal-body {
    padding: 0;
  }

  .modal-foot {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 9px;
    padding: 14px 20px;
    border-top: 1px solid var(--border-color);
    background: var(--surface-2);
  }

  /* Modal section dividers */
  .m-section {
    padding: 18px 20px;
  }

  .m-section+.m-section {
    border-top: 1px solid var(--border-color);
  }

  .m-section-label {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: .72rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .08em;
    color: var(--text-muted);
    margin-bottom: 14px;
  }

  .m-section-label .step-num {
    width: 20px;
    height: 20px;
    border-radius: 50%;
    background: rgba(154, 123, 79, .15);
    color: #9A7B4F;
    font-size: .68rem;
    font-weight: 800;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }

  .m-section-label.done .step-num {
    background: #9A7B4F;
    color: #fff;
  }

  /* Form elements */
  .form-label {
    font-size: .78rem;
    font-weight: 500;
    color: var(--text-heading);
    margin-bottom: 5px;
    display: block;
  }

  .form-label .req {
    color: #ef4444;
    margin-left: 2px;
  }

  .form-label .auto-tag {
    font-size: .65rem;
    background: rgba(154, 123, 79, .12);
    color: #9A7B4F;
    padding: 1px 6px;
    border-radius: 4px;
    font-weight: 600;
    margin-left: 6px;
  }

  .form-control,
  .form-select {
    background: var(--input-bg);
    border: 1px solid var(--border-color);
    color: var(--text-primary);
    border-radius: 7px;
    font-size: .8125rem;
    padding: 7px 11px;
    width: 100%;
    transition: border-color .15s, box-shadow .15s;
  }

  .form-control:focus,
  .form-select:focus {
    outline: none;
    border-color: #9A7B4F;
    box-shadow: var(--input-focus-shadow);
  }

  [data-bs-theme="dark"] .form-control,
  [data-bs-theme="dark"] .form-select {
    background: var(--input-bg);
    color: var(--text-primary);
  }

  textarea.form-control {
    resize: vertical;
    min-height: 70px;
  }

  .form-group {
    margin-bottom: 13px;
  }

  .form-group:last-child {
    margin-bottom: 0;
  }

  .field-hint {
    font-size: .72rem;
    color: var(--text-muted);
    margin-top: 4px;
  }

  /* ── SEARCHABLE CUSTOMER DROPDOWN ── */
  .cust-search-wrap {
    position: relative;
  }

  .cust-search-input-row {
    position: relative;
  }

  .cust-search-icon {
    position: absolute;
    left: 10px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--text-muted);
    font-size: .85rem;
    pointer-events: none;
  }

  .cust-search-input {
    padding-left: 32px !important;
  }

  .cust-clear {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    color: var(--text-muted);
    cursor: pointer;
    padding: 2px;
    line-height: 1;
    font-size: .85rem;
    display: none;
  }

  .cust-clear.visible {
    display: block;
  }

  .cust-dropdown {
    position: absolute;
    top: calc(100% + 4px);
    left: 0;
    right: 0;
    background: var(--modal-bg);
    border: 1px solid var(--border-color);
    border-radius: 8px;
    box-shadow: 0 8px 24px rgba(0, 0, 0, .12);
    z-index: 200;
    max-height: 220px;
    overflow-y: auto;
    display: none;
  }

  .cust-dropdown.open {
    display: block;
  }

  .cust-dropdown::-webkit-scrollbar {
    width: 4px;
  }

  .cust-dropdown::-webkit-scrollbar-thumb {
    background: var(--border-color);
    border-radius: 2px;
  }

  .cust-option {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 13px;
    cursor: pointer;
    transition: background .1s;
    border-bottom: 1px solid var(--border-color);
  }

  .cust-option:last-child {
    border-bottom: none;
  }

  .cust-option:hover {
    background: var(--surface-2);
  }

  .cust-option.selected {
    background: rgba(154, 123, 79, .08);
  }

  .cust-av {
    width: 30px;
    height: 30px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: .7rem;
    font-weight: 700;
    color: #fff;
    flex-shrink: 0;
  }

  .cust-option-name {
    font-size: .8125rem;
    font-weight: 500;
    color: var(--text-heading);
  }

  .cust-option-token {
    font-size: .7rem;
    color: var(--text-muted);
  }

  .cust-no-results {
    padding: 16px;
    text-align: center;
    font-size: .8rem;
    color: var(--text-muted);
  }

  /* Selected customer card */
  .selected-cust-card {
    display: none;
    align-items: center;
    gap: 10px;
    padding: 10px 13px;
    background: rgba(154, 123, 79, .07);
    border: 1px solid rgba(154, 123, 79, .25);
    border-radius: 8px;
    margin-top: 8px;
  }

  .selected-cust-card.show {
    display: flex;
  }

  .selected-cust-card-name {
    font-size: .8125rem;
    font-weight: 600;
    color: var(--text-heading);
  }

  .selected-cust-card-token {
    font-size: .72rem;
    color: var(--text-muted);
  }

  .selected-cust-change {
    margin-left: auto;
    background: none;
    border: none;
    color: #9A7B4F;
    font-size: .75rem;
    cursor: pointer;
    padding: 3px 7px;
    border-radius: 5px;
    transition: background .12s;
    white-space: nowrap;
  }

  .selected-cust-change:hover {
    background: rgba(154, 123, 79, .12);
  }

  /* Toggle */
  .tog-wrap {
    display: flex;
    align-items: center;
    gap: 9px;
    cursor: pointer;
    user-select: none;
  }

  .tog-track {
    width: 38px;
    height: 20px;
    border-radius: 10px;
    background: #dde1ec;
    position: relative;
    transition: background .2s;
    flex-shrink: 0;
  }

  .tog-track.on {
    background: #9A7B4F;
  }

  [data-bs-theme="dark"] .tog-track {
    background: #1e3050;
  }

  [data-bs-theme="dark"] .tog-track.on {
    background: #9A7B4F;
  }

  .tog-thumb {
    width: 14px;
    height: 14px;
    background: #fff;
    border-radius: 50%;
    position: absolute;
    top: 3px;
    left: 3px;
    transition: transform .2s;
    box-shadow: 0 1px 3px rgba(0, 0, 0, .2);
  }

  .tog-track.on .tog-thumb {
    transform: translateX(18px);
  }

  .tog-label {
    font-size: .78rem;
    color: var(--text-muted);
  }

  /* Project form reveal */
  .proj-form-body {
    display: none;
  }

  .proj-form-body.revealed {
    display: block;
    animation: revealSlide .2s ease;
  }

  @keyframes revealSlide {
    from {
      opacity: 0;
      transform: translateY(6px);
    }

    to {
      opacity: 1;
      transform: translateY(0);
    }
  }

  /* Auto code display */
  .auto-code-row {
    display: flex;
    gap: 8px;
    align-items: center;
  }

  .auto-code-field {
    font-size: .82rem;
    font-weight: 600;
    color: #9A7B4F;
    letter-spacing: .04em;
  }

  /* Delete confirm modal */
  .del-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: var(--overlay-bg);
    z-index: 1000;
    align-items: center;
    justify-content: center;
    backdrop-filter: blur(3px);
  }

  .del-overlay.show {
    display: flex;
  }

  .del-box {
    background: var(--modal-bg);
    border-radius: 12px;
    max-width: 360px;
    width: 90%;
    box-shadow: var(--modal-shadow);
    border: 1px solid var(--card-border);
    overflow: hidden;
    animation: modalIn .2s ease;
  }

  .del-body {
    padding: 28px 24px;
    text-align: center;
  }

  .del-icon {
    width: 52px;
    height: 52px;
    border-radius: 14px;
    background: rgba(239, 68, 68, .1);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 14px;
    font-size: 1.4rem;
    color: #ef4444;
  }

  .del-body h6 {
    font-size: .95rem;
    font-weight: 600;
    margin-bottom: 6px;
    color: var(--text-heading);
  }

  .del-body p {
    font-size: .8rem;
    color: var(--text-muted);
    margin: 0;
  }

  .del-foot {
    display: flex;
    gap: 10px;
    padding: 14px 20px;
    border-top: 1px solid var(--border-color);
  }

  .btn-del {
    flex: 1;
    padding: 8px;
    background: #ef4444;
    color: #fff;
    border: none;
    border-radius: 7px;
    font-size: .82rem;
    font-weight: 500;
    cursor: pointer;
  }

  .btn-del:hover {
    background: #dc2626;
  }

  .btn-del-cancel {
    flex: 1;
    padding: 8px;
    background: var(--surface-2);
    color: var(--text-muted);
    border: 1px solid var(--border-color);
    border-radius: 7px;
    font-size: .82rem;
    cursor: pointer;
  }

  /* Toast */
  #toastWrap {
    position: fixed;
    bottom: 22px;
    right: 22px;
    z-index: 9999;
    display: flex;
    flex-direction: column;
    gap: 8px;
    pointer-events: none;
  }

  .toast-item {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    padding: 12px 15px;
    border-radius: 9px;
    background: var(--card-bg);
    border: 1px solid var(--card-border);
    box-shadow: 0 6px 24px rgba(0, 0, 0, .14);
    min-width: 240px;
    max-width: 320px;
    animation: toastIn .2s ease;
    pointer-events: auto;
  }

  @keyframes toastIn {
    from {
      opacity: 0;
      transform: translateY(10px);
    }

    to {
      opacity: 1;
      transform: translateY(0);
    }
  }

  .t-ico {
    font-size: 1rem;
    flex-shrink: 0;
    margin-top: 1px;
  }

  .t-ico.ok {
    color: #10b981;
  }

  .t-ico.err {
    color: #ef4444;
  }

  .t-ico.info {
    color: #9A7B4F;
  }

  .t-title {
    font-size: .8rem;
    font-weight: 600;
    color: var(--text-heading);
    margin: 0 0 2px;
  }

  .t-body {
    font-size: .75rem;
    color: var(--text-muted);
    margin: 0;
  }
</style>
@endpush



<!-- ══ SIDEBAR ══ -->

<!-- ══ MAIN ══ -->
@section('content')

<div class="pg-header">
  <h4><i class="bi bi-diagram-3 me-2"></i>Project & Site Directory</h4>
  <p>Each project is linked to a single customer and has one designated site location. Manage all registered projects across all customer accounts from here.</p>
  <div class="meta-row">
    <span class="meta-badge"><i class="bi bi-shield-fill-check me-1"></i>Super Admin</span>
    <span class="meta-badge"><i class="bi bi-person-gear me-1"></i>Admin</span>
    <span class="meta-badge"><i class="bi bi-person-badge me-1"></i>Front Desk</span>
  </div>
</div>

<!-- Stats -->
<div class="stats-strip">
  <div class="stat-card">
    <div class="stat-icon" style="background:rgba(124,58,237,.1);"><i class="bi bi-diagram-3" style="color:#7c3aed;"></i></div>
    <div>
      <div class="stat-num" id="stat-total">{{ $stats['total'] }}</div>
      <div class="stat-lbl">Total Projects</div>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-icon" style="background:rgba(16,185,129,.1);"><i class="bi bi-check-circle" style="color:#10b981;"></i></div>
    <div>
      <div class="stat-num" id="stat-active">{{ $stats['active'] }}</div>
      <div class="stat-lbl">Active</div>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-icon" style="background:rgba(156,163,175,.1);"><i class="bi bi-slash-circle" style="color:#9ca3af;"></i></div>
    <div>
      <div class="stat-num" id="stat-inactive">{{ $stats['inactive'] }}</div>
      <div class="stat-lbl">Inactive</div>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-icon" style="background:rgba(37,99,235,.1);"><i class="bi bi-buildings" style="color:#3b82f6;"></i></div>
    <div>
      <div class="stat-num" id="stat-clients">{{ $stats['clients'] }}</div>
      <div class="stat-lbl">Customer Covered</div>
    </div>
  </div>
</div>

<div class="filter-bar">
  <div class="filter-group">
    <div class="filter-label">Search</div>
    <input class="filter-control" type="text" id="search-input" placeholder="Project code, name, site..." oninput="filterProjects()" />
  </div>
  <div class="filter-group">
    <div class="filter-label">Customer</div>
    <select class="filter-control" id="filter-client" onchange="filterProjects()">
      <option value="">All Customers</option>
      @foreach($clients as $c)
      <option value="{{ $c->company_name }}">{{ $c->company_name }}</option>
      @endforeach
    </select>
  </div>
  <div class="filter-group">
    <div class="filter-label">Status</div>
    <select class="filter-control" id="filter-status" onchange="filterProjects()">
      <option value="">All</option>
      <option>Active</option>
      <option>Inactive</option>
    </select>
  </div>
  <div class="filter-actions">
    <button class="btn-ghost" onclick="resetFilters()"><i class="bi bi-x-circle"></i>Reset</button>

        @if (auth()->user()?->role?->code !== 'SE')

    <button class="btn-gold" onclick="openAddModal()"><i class="bi bi-plus-lg"></i>Add Project</button>
    @endif
  </div>
</div>

<div class="tbl-card">
  <div class="tbl-card-hdr">
    <div class="tbl-card-hdr-left">
      <span class="tbl-card-title">Projects & Sites</span>
      <span class="result-count" id="result-count">{{ $projects->count() }} projects</span>
    </div>
  </div>
  <div class="tbl-wrap">
    <table class="listing" id="proj-table">
      <thead>
        <tr>
          <th style="width:36px;">#</th>
          <th>Project Code</th>
          <th>Project Name</th>
          <th>Customer</th>
          <th>Site Name</th>
          <th>Site Address</th>
          <th style="width:90px;">Status</th>
          <th style="width:140px;">Actions</th>
        </tr>
      </thead>
      <tbody id="proj-tbody">
        @forelse($projects as $i => $p)
        @php
        $cl = $p->client;
        $rowData = [
        'id' => $p->id,
        'project_name' => $p->project_name,
        'project_code' => $p->project_code,
        'completion_date' => $p->completion_date, // <--- ADD THIS CRITICAL LINE HERE 'site_name'=> $p->site_name,
        'site_address' => $p->site_address,
          'site_name' => $p->site_name,
          'status' => $p->status,
          'client_id' => $p->client_id,
          'client_name' => optional($cl)->company_name,
          'client_token' => optional($cl)->unique_code,
          'warranty_id' => $p->warranty_id,
          'warranty_name' => optional($p->warranty)->name,
          'project_engineer' => $p->project_engineer,
          'engineer_contact' => $p->engineer_contact,
          ];
          @endphp
          <tr data-client="{{ optional($cl)->company_name }}" data-status="{{ $p->status }}" data-json='@json($rowData)'>
            <td class="muted">{{ $i + 1 }}</td>
            <td class="mono">{{ $p->project_code }}</td>
            <td><strong style="font-size:.8125rem;">{{ $p->project_name }}</strong></td>
            <td>
              <div style="display:flex;align-items:center;gap:7px;">
                <div style="width:22px;height:22px;border-radius:5px;background:#9A7B4F;display:flex;align-items:center;justify-content:center;font-size:.6rem;font-weight:700;color:#fff;">{{ strtoupper(substr(optional($cl)->company_name ?? '?',0,1)) }}</div>
                <div>
                  <div style="font-size:.8rem;font-weight:500;">{{ optional($cl)->company_name ?? '—' }}</div>
                  <div style="font-size:.7rem;color:var(--text-muted);">{{ optional($cl)->unique_code }}</div>
                </div>
              </div>
            </td>
            <td>
              <div style="display:flex;align-items:center;gap:6px;">
                <i class="bi bi-geo-alt" style="color:#9A7B4F;font-size:.85rem;"></i>
                <span style="font-size:.8rem;">{{ $p->site_name }}</span>
              </div>
            </td>
            <td class="muted" style="font-size:.78rem;max-width:220px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;" title="{{ $p->site_address }}">{{ $p->site_address }}</td>
            <td><span class="sbadge {{ $p->status==='Active'?'sb-active':'sb-inactive' }}"><i class="bi bi-circle-fill" style="font-size:.4rem;"></i>{{ $p->status }}</span></td>
            <td>
              <div class="row-actions">
                    <button class="btn-xs btn-xs-view" onclick="window.location='{{ url('projects') }}/{{ $p->id }}'"><i class="bi bi-eye"></i>View</button>

                <button class="btn-xs btn-xs-edit" onclick="editProject(this)"><i class="bi bi-pencil"></i>Edit</button>
                <button class="btn-xs btn-xs-del" onclick='openDelModal({{ $p->id }}, @json($p->project_name))'><i class="bi bi-trash3"></i></button>
              </div>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="8" style="text-align:center;padding:40px;color:var(--text-muted);">No projects yet.</td>
          </tr>
          @endforelse
      </tbody>
    </table>
  </div>
</div>



<!-- ══ ADD / EDIT PROJECT MODAL ══ -->
<div class="modal-overlay" id="proj-modal" onclick="handleOverlayClick(event)">
  <div class="modal-box">
    <div class="modal-hdr">
      <div class="modal-hdr-left">
        <div class="modal-hdr-icon"><i class="bi bi-folder-plus"></i></div>
        <div>
          <h6 id="modal-title">Add New Project</h6>
          <div class="modal-hdr-sub" id="modal-sub">Select a customer, then fill in project and site details</div>
        </div>
      </div>
      <button class="modal-close" onclick="closeModal()"><i class="bi bi-x-lg"></i></button>
    </div>

    <div class="modal-body">

      <!-- STEP 1: CUSTOMER SELECTION -->
      <div class="m-section">
        <div class="m-section-label" id="step1-label">
          <span class="step-num">1</span>Select Customer Account
        </div>
        <div class="form-group" style="margin-bottom:0;">
          <label class="form-label">Customer <span class="req">*</span></label>

          <!-- Search input -->
          <div class="cust-search-wrap" id="cust-search-wrap">
            <div class="cust-search-input-row">
              <i class="bi bi-search cust-search-icon"></i>
              <input type="text" class="form-control cust-search-input" id="cust-search"
                placeholder="Type Customer name or token to search…"
                oninput="filterCustomers(this.value)" onfocus="openCustDropdown()" autocomplete="off" />
              <button class="cust-clear" id="cust-clear" onclick="clearCustomer()"><i class="bi bi-x"></i></button>
            </div>
            <div class="cust-dropdown" id="cust-dropdown">
              <!-- rendered by JS -->
            </div>
          </div>

          <!-- Selected customer confirmation card -->
          <div class="selected-cust-card" id="selected-cust-card">
            <div class="cust-av" id="sel-cust-av" style="background:#9A7B4F;"></div>
            <div>
              <div class="selected-cust-card-name" id="sel-cust-name"></div>
              <div class="selected-cust-card-token" id="sel-cust-token"></div>
            </div>
            <button class="selected-cust-change" onclick="changeCustomer()"><i class="bi bi-pencil"></i> Change</button>
          </div>
        </div>
      </div>

      <!-- STEP 2: PROJECT DETAILS (revealed after customer selection) -->
      <div class="proj-form-body" id="proj-form-body">

        <div class="m-section">
          <div class="m-section-label" id="step2-label">
            <span class="step-num">2</span>Project Details
          </div>
          <div class="form-group">
            <label class="form-label">Project Name <span class="req">*</span></label>
            <input type="text" class="form-control" id="proj-name" placeholder="e.g. Downtown Retail Portfolio" />
          </div>

          <div style="display:flex;gap:16px;flex-wrap:wrap;">

            <div class="form-group" style="margin-bottom:0;flex:1;min-width:300px;">
              <label class="form-label">Project Code <span class="req">*</span>
                <!-- <span class="auto-tag">AUTO</span> -->
              </label>

              <div class="auto-code-row">
                <input type="text" class="form-control auto-code-field" id="proj-code" placeholder="PRJ-XXXXXX"  oninput="this.value=this.value.toUpperCase()">
                <!-- <button class="btn-ghost" style="padding:6px 11px;font-size:.78rem;" onclick="regenCode()">
                  <i class="bi bi-arrow-repeat"></i>Regenerate
                </button> -->
              </div>
              
              <div class="field-hint">Auto-generated from customer token. You can edit it.</div>
            </div>

            <div class="form-group" style="margin-bottom:0;flex:1;">
              <label class="form-label">Completion Date <span class="req">*</span></label>
              <div class="auto-code-row">
                <input type="date" class="form-control" id="proj-completion">
              </div>
              <div class="field-hint">Expected or actual completion date.</div>
            </div>
          </div>


          <div class="form-group" style="margin-top:14px;flex:1;">
            <label class="form-label">Warranty Name <span class="req">*</span></label>
            <div class="auto-code-row">
              <select class="form-control" id="proj-warranty">
                <option value="">Select Warranty</option>
                @foreach($warranties as $warranty)
                <option value="{{ $warranty->id }}">{{ $warranty->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="field-hint">Select a warranty.</div>
          </div>


         <div style="display:flex;gap:16px;flex-wrap:wrap;margin-top:14px;">
  <div class="form-group" style="margin-bottom:0;flex:1;min-width:260px;">
    <label class="form-label">Project Engineer</label>
    <input type="text" class="form-control" id="proj-engineer" placeholder="e.g. Rahul Menon">
    <div class="field-hint">Internal person associated with the project. Optional.</div>
  </div>

  <div class="form-group" style="margin-bottom:0;flex:1;min-width:260px;">
    <label class="form-label">Engineer Contact</label>
    <div style="display:grid;grid-template-columns:112px 1fr;gap:6px;align-items:center;">
      <select class="form-control" id="proj-engineer-country" style="min-width:0;padding-left:8px;padding-right:6px;">
        <option value="">— Code —</option>
        <option value="+971" selected>UAE +971</option>
        <option value="+91">India +91</option>
        <option value="+1">USA +1</option>
        <option value="+44">UK +44</option>
        <option value="+966">KSA +966</option>
        <option value="+974">Qatar +974</option>
        <option value="+965">Kuwait +965</option>
        <option value="+973">Bahrain +973</option>
        <option value="+968">Oman +968</option>
      </select>
      <input type="text" class="form-control" id="proj-engineer-contact" placeholder="Phone"
             style="min-width:0;"
             inputmode="numeric" maxlength="15"
             oninput="this.value=this.value.replace(/[^0-9]/g,'')">
    </div>
    <!-- <div class="field-hint">Digits only.</div> -->
  </div>
</div>

        </div>

        <div class="m-section">
          <div class="m-section-label" id="step3-label">
            <span class="step-num">3</span>Site Details
          </div>
          <div class="form-group">
            <label class="form-label">Site Name <span class="req">*</span></label>
            <input type="text" class="form-control" id="site-name" placeholder="e.g. Dubai Mall – Ground Floor G12" />
            <div class="field-hint">The physical location name where service requests will be raised.</div>
          </div>
          <div class="form-group">
            <label class="form-label">Full Site Address <span class="req">*</span></label>
            <textarea class="form-control" id="site-address" rows="2"
              placeholder="Building name, floor/unit, street, area, city…"></textarea>
          </div>
          <div class="form-group" style="margin-bottom:0;">
            <div class="tog-wrap" onclick="toggleTog('status-tog',this)">
              <div class="tog-track on" id="status-tog">
                <div class="tog-thumb"></div>
              </div>
              <span class="tog-label">Active — project visible across SR Registration and Dispatch</span>
            </div>
          </div>
        </div>

      </div><!-- /proj-form-body -->
    </div><!-- /modal-body -->

    <div class="modal-foot">
      <button class="btn-ghost" onclick="closeModal()">Cancel</button>
      <button class="btn-gold" id="save-btn" onclick="saveProject()" disabled style="opacity:.5;cursor:not-allowed;">
        <i class="bi bi-floppy"></i>Save Project
      </button>
    </div>
  </div>
</div>

<!-- ══ DELETE CONFIRM ══ -->
<div class="del-overlay" id="del-overlay" onclick="if(event.target===this)closeDelModal()">
  <div class="del-box">
    <div class="del-body">
      <div class="del-icon"><i class="bi bi-trash3"></i></div>
      <h6>Delete Project</h6>
      <p id="del-msg">Are you sure? This will remove the project and its site record. Existing SRs linked to this project will retain their data.</p>
    </div>
    <div class="del-foot">
      <button class="btn-del-cancel" onclick="closeDelModal()">Cancel</button>
      <button class="btn-del" onclick="execDelete()">Yes, Delete</button>
    </div>
  </div>
</div>

<div id="toastWrap"></div>
@endsection

@push('scripts')
@php
$clientsJs = $clients->map(fn($c) => [
'id' => $c->id,
'name' => $c->company_name,
'token' => $c->unique_code,
'color' => '#9A7B4F',
])->values();
@endphp
<script>
  const CLIENTS = @json($clientsJs);

  const CSRF = '{{ csrf_token() }}';
  const ROUTES = {
    store: '{{ route("projects.store") }}',
    update: id => `{{ url("projects") }}/${id}`,
    destroy: id => `{{ url("projects") }}/${id}`,
  };
  let selectedClientId = null;
  let editingProjectId = null;
  let deletingProjectId = null;

  function getClient(id) {
    return CLIENTS.find(c => c.id === id) || {};
  }

  /* ---- FILTER (over rendered rows) ---- */
  function filterProjects() {
    const q = document.getElementById('search-input').value.toLowerCase();
    const cl = document.getElementById('filter-client').value.toLowerCase();
    const st = document.getElementById('filter-status').value;
    let visible = 0;
    document.querySelectorAll('#proj-tbody tr[data-client]').forEach(r => {
      const txt = r.textContent.toLowerCase();
      const matchQ = !q || txt.includes(q);
      const matchC = !cl || (r.dataset.client || '').toLowerCase() === cl;
      const matchS = !st || r.dataset.status === st;
      const show = matchQ && matchC && matchS;
      r.style.display = show ? '' : 'none';
      if (show) visible++;
    });
    document.getElementById('result-count').textContent = visible + ' project' + (visible !== 1 ? 's' : '');
  }

  function resetFilters() {
    document.getElementById('search-input').value = '';
    document.getElementById('filter-client').value = '';
    document.getElementById('filter-status').value = '';
    filterProjects();
  }

  /* ---- EDIT (reads data-json from the row) ---- */
  function editProject(btn) {
    const p = JSON.parse(btn.closest('tr').dataset.json);
    editingProjectId = p.id;
    openModal();
    document.getElementById('modal-title').textContent = 'Edit Project';
    document.getElementById('modal-sub').textContent = 'Update project or site details';
    selectCustomer(getClient(p.client_id));
    setTimeout(() => {
      document.getElementById('proj-name').value = p.project_name;
      document.getElementById('proj-code').value = p.project_code;
      document.getElementById('proj-engineer').value =  p.project_engineer ?? '';
      document.getElementById('proj-engineer-contact').value = p.engineer_contact ?? '';
         document.getElementById('proj-engineer-country').value = p.engineer_country ?? '';
      document.getElementById('site-name').value = p.site_name;
      document.getElementById('site-address').value = p.site_address;
      // ── warranty (handles inactive/filtered warranties) ──
      var wSel = document.getElementById('proj-warranty');
      var wVal = (p.warranty_id != null && p.warranty_id !== '') ? String(p.warranty_id) : '';
      if (wVal && !wSel.querySelector('option[value="' + wVal + '"]')) {
        var opt = document.createElement('option');
        opt.value = wVal;
        opt.textContent = (p.warranty_name || ('Warranty #' + wVal));
        wSel.appendChild(opt);
      }
      wSel.value = wVal;


      if (p.completion_date) {
        // Splits 'YYYY-MM-DD HH:MM:SS' or ISO strings at the space/T to get just 'YYYY-MM-DD'
        document.getElementById('proj-completion').value = p.completion_date.split(/[ T]/)[0];
      } else {
        document.getElementById('proj-completion').value = '';
      }

      const tog = document.getElementById('status-tog');
      p.status === 'Active' ? tog.classList.add('on') : tog.classList.remove('on');
      enableSaveBtn();
    }, 50);
  }

  /* ---- MODAL ---- */
  function openAddModal() {
    editingProjectId = null;
    openModal();
    document.getElementById('modal-title').textContent = 'Add New Project';
    document.getElementById('modal-sub').textContent = 'Select a client, then fill in project and site details';
  }

  function openModal() {
    resetModalForm();
    document.getElementById('proj-modal').classList.add('show');
    document.body.style.overflow = 'hidden';
    setTimeout(() => document.getElementById('cust-search').focus(), 120);
  }

  function closeModal() {
    document.getElementById('proj-modal').classList.remove('show');
    document.body.style.overflow = '';
    resetModalForm();
  }

  function handleOverlayClick(e) {
    if (e.target === document.getElementById('proj-modal')) closeModal();
  }

  function resetModalForm() {
    selectedClientId = null;
    document.getElementById('cust-search').value = '';
    document.getElementById('cust-search-wrap').style.display = 'block';
    document.getElementById('selected-cust-card').classList.remove('show');
    document.getElementById('cust-dropdown').classList.remove('open');
    document.getElementById('cust-clear').classList.remove('visible');
    document.getElementById('proj-form-body').classList.remove('revealed');
    ['proj-name', 'proj-code', 'proj-completion', 'site-name', 'site-address', 'proj-engineer','proj-engineer-country', 'proj-engineer-contact'].forEach(id => {
      const el = document.getElementById(id);
      if (el) el.value = '';
    });
    document.getElementById('status-tog').classList.add('on');
    const btn = document.getElementById('save-btn');
    btn.disabled = true;
    btn.style.opacity = '.5';
    btn.style.cursor = 'not-allowed';
    document.getElementById('step1-label').classList.remove('done');
  }

  /* ---- CUSTOMER SEARCH (unchanged logic) ---- */
  function renderCustOptions(list) {
    const dd = document.getElementById('cust-dropdown');
    if (!list.length) {
      dd.innerHTML = '<div class="cust-no-results"><i class="bi bi-search" style="display:block;margin-bottom:6px;font-size:1.2rem;color:var(--text-light);"></i>No customers found</div>';
      return;
    }
    dd.innerHTML = list.map(c => {
      const init = c.name.split(' ').map(w => w[0]).join('').substring(0, 2).toUpperCase();
      return `<div class="cust-option${c.id===selectedClientId?' selected':''}" onclick="selectCustomer(CLIENTS.find(x=>x.id==${c.id}))">
      <div class="cust-av" style="background:${c.color};">${init}</div>
      <div><div class="cust-option-name">${c.name}</div><div class="cust-option-token">${c.token}</div></div>
      ${c.id===selectedClientId?'<i class="bi bi-check-circle-fill" style="margin-left:auto;color:#9A7B4F;font-size:.9rem;"></i>':''}
    </div>`;
    }).join('');
  }

  function filterCustomers(q) {
    document.getElementById('cust-dropdown').classList.add('open');
    document.getElementById('cust-clear').classList.toggle('visible', q.length > 0);
    renderCustOptions(CLIENTS.filter(c => c.name.toLowerCase().includes(q.toLowerCase()) || c.token.toLowerCase().includes(q.toLowerCase())));
  }

  function openCustDropdown() {
    renderCustOptions(CLIENTS);
    document.getElementById('cust-dropdown').classList.add('open');
  }

  function selectCustomer(client) {
    if (!client) return;
    selectedClientId = client.id;
    document.getElementById('cust-search-wrap').style.display = 'none';
    document.getElementById('cust-dropdown').classList.remove('open');
    document.getElementById('selected-cust-card').classList.add('show');
    const init = client.name.split(' ').map(w => w[0]).join('').substring(0, 2).toUpperCase();
    document.getElementById('sel-cust-av').style.background = client.color;
    document.getElementById('sel-cust-av').textContent = init;
    document.getElementById('sel-cust-name').textContent = client.name;
    document.getElementById('sel-cust-token').textContent = client.token;
    document.getElementById('step1-label').classList.add('done');
    document.getElementById('proj-form-body').classList.add('revealed');
   
    enableSaveBtn();
  }

  function changeCustomer() {
    selectedClientId = null;
    document.getElementById('cust-search-wrap').style.display = 'block';
    document.getElementById('selected-cust-card').classList.remove('show');
    document.getElementById('proj-form-body').classList.remove('revealed');
    document.getElementById('step1-label').classList.remove('done');
    document.getElementById('cust-search').value = '';
    document.getElementById('cust-clear').classList.remove('visible');
    const btn = document.getElementById('save-btn');
    btn.disabled = true;
    btn.style.opacity = '.5';
    btn.style.cursor = 'not-allowed';
    setTimeout(() => document.getElementById('cust-search').focus(), 80);
  }

  function clearCustomer() {
    document.getElementById('cust-search').value = '';
    document.getElementById('cust-clear').classList.remove('visible');
    renderCustOptions(CLIENTS);
  }
  document.addEventListener('click', e => {
    const dd = document.getElementById('cust-dropdown');
    if (dd && !dd.contains(e.target) && !document.getElementById('cust-search')?.contains(e.target)) dd.classList.remove('open');
  });

  // function regenCode() {
  //   if (!selectedClientId) return;
  //   const cl = getClient(selectedClientId);
  //   const prefix = cl.token ? cl.token.replace('CUST-', '').substring(0, 3) : 'PRJ';
  //   document.getElementById('proj-code').value = `PRJ-${prefix}${String(Math.floor(Math.random()*900)+100)}`;
  // }

  function regenCode() {
  if (!selectedClientId) return;
  const year  = new Date().getFullYear();
  const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
  let rand = '';
  for (let i = 0; i < 4; i++) rand += chars.charAt(Math.floor(Math.random() * chars.length));
  document.getElementById('proj-code').value = `PRJ-${year}-${rand}`;
}

  function toggleTog(trackId, wrap) {
    const t = document.getElementById(trackId);
    t.classList.toggle('on');
    const lbl = wrap.querySelector('.tog-label');
    if (lbl) lbl.textContent = t.classList.contains('on') ? 'Active — project visible across SR Registration and Dispatch' : 'Inactive — hidden from SR Registration';
  }

  function enableSaveBtn() {
    const b = document.getElementById('save-btn');
    b.disabled = false;
    b.style.opacity = '1';
    b.style.cursor = 'pointer';
  }

  /* ---- SAVE (POST/PUT to DB) ---- */
  function saveProject() {
  const payload = {
    client_id: selectedClientId,
    project_name: document.getElementById('proj-name').value.trim(),
    project_code: document.getElementById('proj-code').value.trim(),
    site_name: document.getElementById('site-name').value.trim(),
    site_address: document.getElementById('site-address').value.trim(),
    completion_date: document.getElementById('proj-completion').value || null,
    warranty_id: document.getElementById('proj-warranty').value || null,
    project_engineer: document.getElementById('proj-engineer').value.trim() || null,
    engineer_contact: document.getElementById('proj-engineer-contact').value.trim() || null,
    engineer_country: document.getElementById('proj-engineer-country').value.trim() || null,
    status: document.getElementById('status-tog').classList.contains('on') ? 'Active' : 'Inactive',
  };

  if (!payload.client_id) {
    showToast('err', 'Missing', 'Please select a client.');
    return;
  }
  if (!payload.project_name) {
    showToast('err', 'Missing', 'Please enter a project name.');
    return;
  }
  if (!payload.completion_date) {
    showToast('err', 'Missing', 'Please select a completion date.');
    return;
  }
  if (!payload.warranty_id) {
    showToast('err', 'Missing', 'Please select a warranty.');
    return;
  }
  if (!payload.site_name) {
    showToast('err', 'Missing', 'Please enter the site name.');
    return;
  }
  if (!payload.site_address) {
    showToast('err', 'Missing', 'Please enter the site address.');
    return;
  }

   if (!payload.project_code) {
    showToast('err', 'Missing', 'Please enter the Project code');
    return;
  }
  

  // optional field — warn, but allow proceeding
  if (!payload.project_engineer) {
    if (typeof Swal === 'undefined') {
      if (confirm('Project engineer field is empty.\n\nDo you want to proceed?')) doSaveProject(payload);
      return;
    }

    Swal.fire({
      icon: 'warning',
      title: 'Project Engineer Missing',
      html: `Project engineer field is empty.<br>
             <span style="font-size:.85rem;color:#888;">Do you want to proceed?</span>`,
      showCancelButton: true,
      confirmButtonText: 'Yes, proceed',
      cancelButtonText: 'Go back',
      confirmButtonColor: '#9A7B4F',
      cancelButtonColor: '#6c757d',
      reverseButtons: true,
      focusCancel: true
    }).then(res => {
      if (res.isConfirmed) {
        doSaveProject(payload);
      } else {
        document.getElementById('proj-engineer').focus();
      }
    });
    return;
  }

  doSaveProject(payload);
}

function doSaveProject(payload) {
  const url    = editingProjectId ? ROUTES.update(editingProjectId) : ROUTES.store;
  const method = editingProjectId ? 'PUT' : 'POST';
  const isEdit = !!editingProjectId;

  fetch(url, {
      method,
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': CSRF,
        'Accept': 'application/json'
      },
      body: JSON.stringify(payload)
    })
    .then(async r => {
      if (!r.ok) {
        throw await r.json();
      }
      return r.json();
    })
    .then(data => {
      closeModal();

      Swal.fire({
        icon: 'success',
        title: isEdit ? 'Project Updated' : 'Project Added',
        html: `${data.message || 'Saved successfully.'}<br>
               <span style="font-size:.8rem;color:#888;">${payload.project_code || ''}</span>`,
        confirmButtonText: 'Done',
        confirmButtonColor: '#9A7B4F',
        allowOutsideClick: false
      }).then(() => location.reload());
    })
    .catch(err => {
      const msg = err?.errors ? Object.values(err.errors)[0][0] : 'Could not save project.';
      Swal.fire({
        icon: 'error',
        title: 'Save Failed',
        text: msg,
        confirmButtonText: 'OK',
        confirmButtonColor: '#9A7B4F'
      });
    });
}
  /* ---- DELETE (DELETE to DB) ---- */
  function openDelModal(id, name) {
    deletingProjectId = id;
    document.getElementById('del-msg').textContent = `Are you sure you want to delete "${name}"? Existing SRs linked to this project will retain their data but the project will be removed.`;
    document.getElementById('del-overlay').classList.add('show');
  }

  function closeDelModal() {
    document.getElementById('del-overlay').classList.remove('show');
  }

  function execDelete() {
    fetch(ROUTES.destroy(deletingProjectId), {
        method: 'DELETE',
        headers: {
          'X-CSRF-TOKEN': CSRF,
          'Accept': 'application/json'
        }
      })
      .then(r => r.json())
      .then(data => {
        closeDelModal();
        showToast('ok', 'Deleted', data.message);
        setTimeout(() => location.reload(), 600);
      })
      .catch(() => showToast('err', 'Error', 'Could not delete project.'));
  }

  /* ---- TOAST ---- */
  function showToast(type, title, body) {
    const w = document.getElementById('toastWrap');
    if (!w) return;
    const icons = {
      ok: 'bi-check-circle-fill',
      err: 'bi-x-circle-fill',
      info: 'bi-info-circle-fill'
    };
    const t = document.createElement('div');
    t.className = 'toast-item';
    t.innerHTML = `<i class="bi ${icons[type]||icons.info} t-ico ${type}"></i><div><p class="t-title">${title}</p><p class="t-body">${body}</p></div>`;
    w.appendChild(t);
    setTimeout(() => {
      t.style.transition = 'opacity .3s';
      t.style.opacity = '0';
      setTimeout(() => t.remove(), 300);
    }, 3500);
  }
</script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush