@extends('layouts.layout')
@section('title', 'Client Directory— Digit-Us Portal')
@section('page_title', 'Client Directory')
@section('page_icon', 'bi bi-buildings')

@push('styles')
<style>
  /* ── SIDEBAR ── */
  .sidebar {
    width: var(--sidebar-width);
    min-height: 100vh;
    background: var(--sidebar-bg);
    border-right: 1px solid var(--border-color);
    display: flex;
    flex-direction: column;
    position: fixed;
    top: 0;
    left: 0;
    z-index: 300;
    box-shadow: var(--sidebar-shadow);
    transition: transform .28s cubic-bezier(.4, 0, .2, 1);
  }

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
    background: linear-gradient(135deg, #9a8053, #b8975e);
    border-radius: 9px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-size: .85rem;
    font-weight: 700;
    font-family: 'Cormorant Garamond', Georgia, serif;
    letter-spacing: -.5px;
    line-height: 1;
  }

  .sb-brand-name {
    font-size: .9375rem;
    font-weight: 700;
    color: var(--text-heading);
    line-height: 1.2;
    font-family: 'Cormorant Garamond', Georgia, serif;
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
    color: #9a8053;
  }

  .sb-nav a.active {
    background: rgba(154, 128, 83, .1);
    color: #9a8053;
    font-weight: 500;
    border-right-color: #9a8053;
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
    background: linear-gradient(135deg, #9a8053, #b8975e);
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

  /* ── TOPBAR ── */
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

  .pg-title {
    font-size: .9375rem;
    font-weight: 600;
    color: var(--text-heading);
    display: flex;
    align-items: center;
    gap: 7px;
    font-family: 'Cormorant Garamond', Georgia, serif;
  }

  .breadcrumb {
    margin: 0;
    font-size: .72rem;
    padding: 0;
  }

  .breadcrumb-item+.breadcrumb-item::before {
    content: "/";
    color: var(--text-light);
  }

  .breadcrumb-item.active {
    color: var(--text-muted);
  }

  .breadcrumb-item a {
    color: #9a8053;
  }

  .topbar-right {
    display: flex;
    align-items: center;
    gap: 10px;
  }

  .role-badge {
    font-size: .72rem;
    background: rgba(154, 128, 83, .12);
    color: #9a8053;
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
    background: linear-gradient(135deg, #9a8053, #b8975e);
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
    color: #b8975e;
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
    background: #9a8053;
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

  /* ── MAIN ── */
  .main-content {
    margin-left: var(--sidebar-width);
    margin-top: 60px;
    padding: 22px 22px 56px;
    min-height: calc(100vh - 60px);
  }

  @media(max-width:991.98px) {
    .main-content {
      margin-left: 0;
    }
  }

  @media(max-width:575.98px) {
    .main-content {
      padding: 14px 12px 56px;
    }
  }

  /* ── CLIENT HERO ── */
  .client-hero {
    border-radius: 12px;
    padding: 24px 28px;
    margin-bottom: 20px;
    color: #fff;
    position: relative;
    overflow: hidden;
    background: linear-gradient(135deg, #9A7B4F 0%, #7A6140 100%);
  }

  .client-hero::before {
    content: '';
    position: absolute;
    left: -50px;
    bottom: -50px;
    width: 220px;
    height: 220px;
    border-radius: 50%;
    background: rgba(255, 255, 255, .05);
  }

  .client-hero::after {
    content: '';
    position: absolute;
    right: -40px;
    top: -40px;
    width: 200px;
    height: 200px;
    border-radius: 50%;
    background: rgba(255, 255, 255, .07);
  }

  .hero-top {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 16px;
    position: relative;
    z-index: 1;
    flex-wrap: wrap;
  }

  .hero-token {
    font-size: .75rem;
    font-weight: 700;
    background: rgba(255, 255, 255, .2);
    border: 1px solid rgba(255, 255, 255, .3);
    border-radius: 20px;
    padding: 3px 12px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    margin-bottom: 8px;
    font-family: monospace;
    letter-spacing: .03em;
  }

  .hero-name {
    font-family: 'Cormorant Garamond', Georgia, serif;
    font-size: 1.55rem;
    font-weight: 700;
    line-height: 1.15;
    margin: 0 0 6px;
    letter-spacing: -.02em;
  }

  .hero-tag {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: .82rem;
    opacity: .9;
  }

  .hero-meta {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-top: 14px;
    flex-wrap: wrap;
    position: relative;
    z-index: 1;
  }

  .hero-badge {
    background: rgba(255, 255, 255, .18);
    border: 1px solid rgba(255, 255, 255, .28);
    border-radius: 20px;
    font-size: .7rem;
    padding: 3px 11px;
    font-weight: 500;
    display: inline-flex;
    align-items: center;
    gap: 5px;
  }

  .hero-badge.green {
    background: rgba(16, 185, 129, .3);
    border-color: rgba(16, 185, 129, .4);
  }

  .hero-actions {
    display: flex;
    gap: 8px;
    position: relative;
    z-index: 1;
    flex-shrink: 0;
    flex-wrap: wrap;
  }

  .btn-hero-out {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 15px;
    border: 1px solid rgba(255, 255, 255, .4);
    border-radius: 8px;
    color: #fff;
    background: rgba(255, 255, 255, .12);
    font-size: .8rem;
    font-weight: 500;
    cursor: pointer;
    transition: background .15s;
    white-space: nowrap;
    font-family: 'SF Pro Display', -apple-system, sans-serif;
  }

  .btn-hero-out:hover {
    background: rgba(255, 255, 255, .22);
  }

  .btn-hero-solid {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 15px;
    border: none;
    border-radius: 8px;
    color: #0891b2;
    background: #fff;
    font-size: .8rem;
    font-weight: 600;
    cursor: pointer;
    transition: opacity .15s;
    white-space: nowrap;
    font-family: 'SF Pro Display', -apple-system, sans-serif;
  }

  .btn-hero-solid:hover {
    opacity: .9;
  }

  /* ── STATS ── */
  .stats-strip {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 10px;
    margin-bottom: 20px;
  }

  .stat-card {
    background: var(--card-bg);
    border: 1px solid var(--card-border);
    border-radius: 10px;
    padding: 14px 15px;
    display: flex;
    align-items: center;
    gap: 12px;
    box-shadow: var(--card-shadow);
  }

  .stat-icon {
    width: 38px;
    height: 38px;
    border-radius: 9px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
    flex-shrink: 0;
  }

  .stat-num {
    font-size: 1.45rem;
    font-weight: 700;
    line-height: 1;
    color: var(--text-heading);
    font-family: 'Cormorant Garamond', Georgia, serif;
  }

  .stat-lbl {
    font-size: .7rem;
    color: var(--text-muted);
    margin-top: 2px;
  }

  @media(max-width:900px) {
    .stats-strip {
      grid-template-columns: repeat(3, 1fr);
    }
  }

  @media(max-width:575px) {
    .stats-strip {
      grid-template-columns: repeat(2, 1fr);
    }
  }

  /* ── LAYOUT ── */
  .view-layout {
    display: grid;
    grid-template-columns: 1fr 300px;
    gap: 16px;
    align-items: start;
  }

  .view-layout>* {
    min-width: 0;
  }

  @media(max-width:1200px) {
    .view-layout {
      grid-template-columns: 1fr 280px;
    }
  }

  @media(max-width:1100px) {
    .view-layout {
      grid-template-columns: 1fr;
    }
  }

  /* ── CARD ── */
  .card {
    background: var(--card-bg);
    border: 1px solid var(--card-border);
    border-radius: 10px;
    box-shadow: var(--card-shadow);
    overflow: hidden;
    margin-bottom: 0;
  }

  .card-hdr {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 13px 18px;
    border-bottom: 1px solid var(--border-color);
    flex-wrap: wrap;
    gap: 10px;
  }

  .card-hdr-left {
    display: flex;
    align-items: center;
    gap: 10px;
  }

  .card-hdr-title {
    font-size: .9rem;
    font-weight: 600;
    color: var(--text-heading);
    font-family: 'Cormorant Garamond', Georgia, serif;
  }

  .result-count {
    font-size: .72rem;
    color: var(--text-muted);
    background: var(--surface-2);
    padding: 2px 9px;
    border-radius: 9px;
  }

  /* ── FILTER BAR ── */
  .pf-filter-bar {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    align-items: flex-end;
    padding: 12px 18px;
    border-bottom: 1px solid var(--border-color);
    background: var(--surface-2);
  }

  .filter-group {
    display: flex;
    flex-direction: column;
    gap: 3px;
  }

  .filter-label {
    font-size: .65rem;
    font-weight: 700;
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: .06em;
  }

  .filter-control {
    height: 32px;
    padding: 0 9px;
    border: 1px solid var(--border-color);
    border-radius: 7px;
    background: var(--input-bg);
    color: var(--text-primary);
    font-size: .78rem;
    font-family: 'SF Pro Display', -apple-system, sans-serif;
    min-width: 120px;
    transition: border-color .15s;
  }

  .filter-control:focus {
    outline: none;
    border-color: #9a8053;
    box-shadow: var(--input-focus-shadow);
  }

  .filter-actions {
    margin-left: auto;
    display: flex;
    gap: 6px;
    align-items: flex-end;
  }

  /* ── TABLE ── */
  .tbl-wrap {
    overflow-x: auto;
  }

  table.listing {
    width: 100%;
    border-collapse: collapse;
  }

  table.listing thead th {
    padding: 9px 14px;
    font-size: .67rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .07em;
    color: var(--text-muted);
    background: var(--table-header);
    border-bottom: 1px solid var(--border-color);
    white-space: nowrap;
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
    padding: 11px 14px;
    font-size: .8rem;
    color: var(--text-primary);
    vertical-align: middle;
  }

  table.listing td.muted {
    color: var(--text-muted);
    font-size: .75rem;
  }

  table.listing td.mono {
    font-family: monospace;
    font-size: .78rem;
    font-weight: 600;
    color: #9a8053;
  }

  .row-actions {
    display: flex;
    gap: 5px;
    /* opacity: 0; */
    transition: opacity .12s;
  }

  table.listing tr:hover .row-actions {
    /* opacity: 1; */
  }

  .site-line {
    display: flex;
    align-items: center;
    gap: 6px;
  }

  .site-line i {
    color: #9a8053;
    font-size: .8rem;
    flex-shrink: 0;
  }

  /* ── STATUS BADGES ── */
  .sbadge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 3px 10px;
    border-radius: 20px;
    font-size: .69rem;
    font-weight: 600;
    white-space: nowrap;
  }

  .sb-active-g {
    background: rgba(16, 185, 129, .12);
    color: #059669;
  }

  .sb-inactive {
    background: rgba(156, 163, 175, .1);
    color: #9ca3af;
  }

  /* ── PAGINATION ── */
  .pagination-bar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 11px 18px;
    border-top: 1px solid var(--border-color);
    flex-wrap: wrap;
    gap: 8px;
  }

  .page-info {
    font-size: .75rem;
    color: var(--text-muted);
  }

  .page-btns {
    display: flex;
    gap: 4px;
  }

  .page-btn {
    width: 28px;
    height: 28px;
    border-radius: 6px;
    border: 1px solid var(--border-color);
    background: var(--card-bg);
    color: var(--text-muted);
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: .75rem;
    transition: all .12s;
  }

  .page-btn:hover {
    border-color: #9a8053;
    color: #9a8053;
  }

  .page-btn.active {
    background: #9a8053;
    color: #fff;
    border-color: #9a8053;
  }

  /* ── BUTTONS ── */
  .btn-gold {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 7px 14px;
    background: linear-gradient(135deg, #9a8053, #b8975e);
    color: #fff;
    border: none;
    border-radius: 7px;
    font-size: .8rem;
    font-weight: 500;
    cursor: pointer;
    white-space: nowrap;
    transition: opacity .15s;
    font-family: 'SF Pro Display', -apple-system, sans-serif;
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
    font-family: 'SF Pro Display', -apple-system, sans-serif;
  }

  .btn-ghost:hover {
    background: var(--surface-3);
  }

  .btn-xs {
    padding: 4px 9px;
    border-radius: 5px;
    font-size: .73rem;
    border: none;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    font-family: 'SF Pro Display', -apple-system, sans-serif;
    transition: background .12s;
  }

  .btn-xs-view {
    background: rgba(37, 99, 235, .1);
    color: #3b82f6;
  }

  .btn-xs-view:hover {
    background: rgba(37, 99, 235, .2);
  }

  .btn-xs-edit {
    background: rgba(154, 128, 83, .1);
    color: #9a8053;
  }

  .btn-xs-edit:hover {
    background: rgba(154, 128, 83, .2);
  }

  .btn-xs-danger {
    background: rgba(239, 68, 68, .08);
    color: #ef4444;
  }

  .btn-xs-danger:hover {
    background: rgba(239, 68, 68, .15);
  }

  /* ── RIGHT PANEL CARDS ── */
  .info-card {
    background: var(--card-bg);
    border: 1px solid var(--card-border);
    border-radius: 10px;
    box-shadow: var(--card-shadow);
    overflow: hidden;
    margin-bottom: 14px;
    min-width: 0;
    max-width: 100%;
  }

  .info-card-hdr {
    padding: 12px 16px;
    border-bottom: 1px solid var(--border-color);
    display: flex;
    align-items: center;
    gap: 8px;
  }

  .info-card-hdr i {
    color: #9a8053;
    font-size: .95rem;
  }

  .info-card-title {
    font-size: .82rem;
    font-weight: 600;
    color: var(--text-heading);
    font-family: 'Cormorant Garamond', Georgia, serif;
  }

  .info-row {
    display: flex;
    align-items: flex-start;
    gap: 8px;
    padding: 9px 16px;
    border-bottom: 1px solid var(--border-color);
  }

  .info-row:last-child {
    border-bottom: none;
  }

  .info-key {
    font-size: .71rem;
    color: var(--text-muted);
    font-weight: 500;
    white-space: nowrap;
    padding-top: 1px;
    flex-shrink: 0;
    min-width: 100px;
  }

  .info-val {
    font-size: .78rem;
    color: var(--text-heading);
    font-weight: 500;
    text-align: right;
    flex: 1;
    min-width: 0;
    word-break: break-word;
    overflow-wrap: anywhere;
  }

  .info-val.mono {
    font-family: monospace;
    color: #9a8053;
    word-break: break-all;
  }

  /* ── STAKEHOLDER LIST ── */
  .stk-list {
    padding: 6px 16px 12px;
  }

  .stk-row {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 8px 0;
    border-bottom: 1px solid var(--border-color);
  }

  .stk-row:last-child {
    border-bottom: none;
  }

  .stk-av {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    background: linear-gradient(135deg, #9a8053, #b8975e);
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: .72rem;
    font-weight: 700;
    flex-shrink: 0;
  }

  .stk-name {
    font-size: .79rem;
    font-weight: 600;
    color: var(--text-heading);
    line-height: 1.2;
  }

  .stk-role {
    font-size: .68rem;
    color: var(--text-muted);
  }

  .stk-phone {
    font-size: .72rem;
    color: #9a8053;
    font-family: monospace;
    margin-left: auto;
    flex-shrink: 0;
  }

  /* ── TIMELINE ── */
  .timeline {
    padding: 14px 16px;
  }

  .tl-item {
    display: flex;
    gap: 12px;
    padding-bottom: 18px;
    position: relative;
  }

  .tl-item:last-child {
    padding-bottom: 0;
  }

  .tl-item::before {
    content: '';
    position: absolute;
    left: 13px;
    top: 28px;
    bottom: 0;
    width: 1px;
    background: var(--timeline-line);
  }

  .tl-item:last-child::before {
    display: none;
  }

  .tl-dot {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: .7rem;
    flex-shrink: 0;
    border: 2px solid var(--border-color);
  }

  .tl-content {
    flex: 1;
    min-width: 0;
    overflow: hidden;
  }

  .tl-action {
    font-size: .78rem;
    font-weight: 600;
    color: var(--text-heading);
    line-height: 1.35;
    word-break: break-word;
  }

  .tl-by {
    font-size: .7rem;
    color: var(--text-muted);
    margin-top: 2px;
  }

  .tl-time {
    font-size: .65rem;
    color: var(--text-light);
    margin-top: 3px;
  }

  /* ── MODAL (CREATE PROJECT) ── */
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
    max-width: 580px;
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
    width: 38px;
    height: 38px;
    border-radius: 9px;
    background: linear-gradient(135deg, #9a8053, #b8975e);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-size: 1rem;
    flex-shrink: 0;
  }

  .modal-hdr h6 {
    font-size: 1rem;
    font-weight: 600;
    color: var(--text-heading);
    margin: 0;
    font-family: 'Cormorant Garamond', Georgia, serif;
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
    max-height: 70vh;
    overflow-y: auto;
  }

  .modal-body::-webkit-scrollbar {
    width: 5px;
  }

  .modal-body::-webkit-scrollbar-thumb {
    background: var(--border-color);
    border-radius: 3px;
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
    background: rgba(154, 128, 83, .15);
    color: #9a8053;
    font-size: .68rem;
    font-weight: 800;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }

  .locked-banner {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 13px;
    background: rgba(154, 128, 83, .09);
    border: 1px solid rgba(154, 128, 83, .22);
    border-radius: 8px;
    margin-bottom: 13px;
  }

  .locked-banner i {
    color: #9a8053;
    font-size: 1rem;
    flex-shrink: 0;
  }

  .locked-val {
    font-size: .8rem;
    font-weight: 600;
    color: var(--text-heading);
  }

  .locked-sub {
    font-size: .7rem;
    color: var(--text-muted);
  }

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

  .auto-tag {
    font-size: .63rem;
    background: rgba(154, 128, 83, .12);
    color: #9a8053;
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
    padding: 8px 11px;
    width: 100%;
    transition: border-color .15s, box-shadow .15s;
    font-family: 'SF Pro Display', -apple-system, sans-serif;
  }

  .form-control:focus,
  .form-select:focus {
    outline: none;
    border-color: #9a8053;
    box-shadow: var(--input-focus-shadow);
  }

  [data-bs-theme="dark"] .form-control,
  [data-bs-theme="dark"] .form-select {
    background: var(--input-bg);
    color: var(--text-primary);
  }

  textarea.form-control {
    resize: vertical;
    min-height: 72px;
  }

  .form-group {
    margin-bottom: 13px;
  }

  .form-group:last-child {
    margin-bottom: 0;
  }

  .field-hint {
    font-size: .71rem;
    color: var(--text-muted);
    margin-top: 4px;
  }

  .auto-code-row {
    display: flex;
    gap: 8px;
    align-items: center;
  }

  .auto-code-field {
    font-family: monospace;
    font-size: .82rem;
    font-weight: 600;
    color: #9a8053;
    letter-spacing: .04em;
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
    background: #9a8053;
  }

  [data-bs-theme="dark"] .tog-track {
    background: #393837;
  }

  [data-bs-theme="dark"] .tog-track.on {
    background: #9a8053;
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

  /* Split row */
  .split-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 10px;
  }

  @media(max-width:520px) {
    .split-row {
      grid-template-columns: 1fr;
    }
  }

  /* ── EMPTY STATE ── */
  .empty-st {
    padding: 44px 24px;
    text-align: center;
  }

  .empty-st i {
    font-size: 2rem;
    color: var(--text-light);
    display: block;
    margin-bottom: 10px;
  }

  .empty-st h6 {
    font-family: 'Cormorant Garamond', Georgia, serif;
    font-size: 1.05rem;
    color: var(--text-heading);
    margin-bottom: 5px;
  }

  .empty-st p {
    font-size: .79rem;
    color: var(--text-muted);
    max-width: 300px;
    margin: 0 auto 14px;
  }

  /* ── TOAST ── */
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
    color: #9a8053;
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


@section('content')
<!-- HERO -->
<div class="client-hero">
  <div class="hero-top">
    <div style="position:relative;z-index:1;min-width:0;">
      <div class="hero-token"><i class="bi bi-building-fill"></i>{{ $client->unique_code }}</div>
      <div class="hero-name">{{ $client->company_name }}</div>
      <div class="hero-tag"><i class="bi bi-person-fill"></i>{{ $client->contact_name ?? '—' }} · Primary Contact</div>
    </div>
    <div class="hero-actions">
      <button class="btn-hero-out" onclick="window.location='{{ url('admin/clients') }}'">
        <i class="bi bi-arrow-left"></i>Back to Directory
      </button>
      <a class="btn-hero-out" href="{{ url('admin/clients/'.$client->id.'/edit') }}">
        <i class="bi bi-pencil"></i>Edit Client
      </a>
      <button class="btn-hero-solid" onclick="openProjectModal()">
        <i class="bi bi-plus-lg"></i>Create Project
      </button>
    </div>
  </div>
  <div class="hero-meta">
    @if($client->status === 'Active' || $client->status === 'active')
    <span class="hero-badge green"><i class="bi bi-check-circle-fill"></i>{{ ucfirst($client->status) }}</span>
    @else
    <span class="hero-badge"><i class="bi bi-slash-circle"></i>{{ ucfirst($client->status ?? 'Inactive') }}</span>
    @endif
    <span class="hero-badge"><i class="bi bi-telephone-fill"></i>{{ $client->primary_country }} {{ $client->primary_mobile }}</span>
    @if($client->email)
    <span class="hero-badge"><i class="bi bi-envelope-fill"></i>{{ $client->email }}</span>
    @endif
    @if($client->designation)
    <span class="hero-badge"><i class="bi bi-file-earmark-check"></i>{{ $client->designation }}</span>
    @endif
    <span class="hero-badge"><i class="bi bi-calendar3"></i>Since {{ optional($client->created_at)->format('M Y') }}</span>
  </div>
</div>

<!-- STATS -->
<div class="stats-strip">
  <div class="stat-card">
    <div class="stat-icon" style="background:rgba(6,182,212,.1);"><i class="bi bi-diagram-3" style="color:#0891b2;"></i></div>
    <div>
      <div class="stat-num">{{ $projects->count() }}</div>
      <div class="stat-lbl">Total Projects</div>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-icon" style="background:rgba(16,185,129,.1);"><i class="bi bi-check-circle" style="color:#10b981;"></i></div>
    <div>
      <div class="stat-num">{{ $projects->where('status','Active')->count() }}</div>
      <div class="stat-lbl">Active Projects</div>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-icon" style="background:rgba(124,58,237,.1);"><i class="bi bi-ticket-detailed" style="color:#7c3aed;"></i></div>
    <div>
      <div class="stat-num">{{ $lifetimeSrs ?? 0 }}</div>
      <div class="stat-lbl">Lifetime SRs</div>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-icon" style="background:rgba(6,182,212,.1);"><i class="bi bi-activity" style="color:#0891b2;"></i></div>
    <div>
      <div class="stat-num">{{ $activeSrs ?? 0 }}</div>
      <div class="stat-lbl">Active SRs</div>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-icon" style="background:rgba(154,128,83,.12);"><i class="bi bi-star-fill" style="color:#9a8053;"></i></div>
    <div>
      <div class="stat-num">{{ $avgRating ?? '—' }}</div>
      <div class="stat-lbl">Avg Rating</div>
    </div>
  </div>
</div>

<!-- LAYOUT -->
<div class="view-layout">

  <!-- LEFT: PROJECT LISTING -->
  <div style="min-width:0;">
    <div class="card">
      <div class="card-hdr">
        <div class="card-hdr-left">
          <span class="card-hdr-title">Client Projects</span>
          <span class="result-count" id="pf-count">4 projects</span>
        </div>
        <div style="display:flex;gap:7px;">
          <button class="btn-ghost" onclick="showToast('ok','Export','Generating projects CSV…')"><i class="bi bi-download"></i>Export</button>
          <button class="btn-gold" onclick="openProjectModal()"><i class="bi bi-plus-lg"></i>Create Project</button>
        </div>
      </div>

      <div class="pf-filter-bar">
        <div class="filter-group">
          <div class="filter-label">Search</div>
          <input class="filter-control" id="pf-search" type="text" placeholder="Project name, code, site…" oninput="filterProjects()" />
        </div>
        <div class="filter-group">
          <div class="filter-label">Status</div>
          <select class="filter-control" id="pf-status" onchange="filterProjects()">
            <option value="">All</option>
            <option>Active</option>
            <option>Inactive</option>
          </select>
        </div>
        <div class="filter-actions">
          <button class="btn-ghost" onclick="resetProjectFilters()"><i class="bi bi-x-circle"></i>Reset</button>
        </div>
      </div>

      <div class="tbl-wrap">
        <table class="listing">
          <thead>
            <tr>
              <th style="width:36px;">#</th>
              <th>Project Code</th>
              <th>Project Name</th>
              <th>Site Name</th>
              <th>Site Address</th>
              <th style="text-align:center;">SRs</th>
              <th style="width:88px;">Status</th>
              <th style="width:150px;">Actions</th>
            </tr>
          </thead>
          <tbody id="pf-tbody"></tbody>
        </table>
      </div>
      <div class="pagination-bar">
        <div class="page-info" id="pf-page-info">Page 1 of 1 · 4 projects</div>
        <div class="page-btns">
          <button class="page-btn" onclick="showToast('info','Pagination','Previous page')"><i class="bi bi-chevron-left"></i></button>
          <button class="page-btn active">1</button>
          <button class="page-btn" onclick="showToast('info','Pagination','Next page')"><i class="bi bi-chevron-right"></i></button>
        </div>
      </div>
    </div>
  </div>

  <!-- RIGHT PANEL -->
  <div style="min-width:0;overflow:hidden;">

    <!-- Client Details -->
    <div class="info-card">
      <div class="info-card-hdr"><i class="bi bi-info-circle-fill"></i><span class="info-card-title">Client Details</span></div>
      <div class="info-row"><span class="info-key">Client Token</span><span class="info-val mono">{{ $client->unique_code }}</span></div>
      <div class="info-row"><span class="info-key">Trade Name</span><span class="info-val">{{ $client->company_name }}</span></div>
      <div class="info-row"><span class="info-key">Primary Contact</span><span class="info-val">{{ $client->contact_name ?? '—' }}</span></div>
      <div class="info-row"><span class="info-key">Phone</span><span class="info-val mono">{{ $client->primary_country }} {{ $client->primary_mobile }}</span></div>
      <div class="info-row"><span class="info-key">Email</span><span class="info-val" style="font-size:.72rem;">{{ $client->email ?? '—' }}</span></div>
      <div class="info-row"><span class="info-key">HQ Address</span><span class="info-val" style="font-size:.72rem;line-height:1.4;">{{ $client->address ?? '—' }}</span></div>
      <div class="info-row"><span class="info-key">Status</span><span class="info-val">
          @if($client->status === 'Active' || $client->status === 'active')
          <span class="sbadge sb-active-g" style="font-size:.68rem;"><i class="bi bi-circle-fill" style="font-size:.32rem;"></i>Active</span>
          @else
          <span class="sbadge sb-inactive" style="font-size:.68rem;"><i class="bi bi-circle-fill" style="font-size:.32rem;"></i>{{ ucfirst($client->status ?? 'Inactive') }}</span>
          @endif
        </span></div>
      <div class="info-row"><span class="info-key">Designation</span><span class="info-val">{{ $client->designation ?? '—' }}</span></div>
      <div class="info-row"><span class="info-key">Onboarded</span><span class="info-val">{{ optional($client->created_at)->format('d M Y') }}</span></div>
    </div>

    <!-- Stakeholders -->
    <div class="info-card">
      <div class="info-card-hdr">
        <i class="bi bi-people-fill"></i>
        <span class="info-card-title">Notification Contacts</span>
        <button class="btn-xs btn-xs-edit" style="margin-left:auto;" onclick="showToast('info','Contacts','Manage contacts modal…')"><i class="bi bi-plus-lg"></i>Add</button>
      </div>
      <div class="stk-list">
        <div class="stk-row">
          <div class="stk-av">JH</div>
          <div style="min-width:0;flex:1;">
            <div class="stk-name">James Harrington</div>
            <div class="stk-role">Facilities Manager</div>
          </div>
          <div class="stk-phone">+971 50 123 4567</div>
        </div>
        <div class="stk-row">
          <div class="stk-av" style="background:linear-gradient(135deg,#0891b2,#0e7490);">RK</div>
          <div style="min-width:0;flex:1;">
            <div class="stk-name">Rakesh Kumar</div>
            <div class="stk-role">Site Supervisor</div>
          </div>
          <div class="stk-phone">+971 55 987 6543</div>
        </div>
        <div class="stk-row">
          <div class="stk-av" style="background:linear-gradient(135deg,#7c3aed,#8b5cf6);">SA</div>
          <div style="min-width:0;flex:1;">
            <div class="stk-name">Sarah Al Mansoori</div>
            <div class="stk-role">Ops Coordinator</div>
          </div>
          <div class="stk-phone">+971 56 445 2211</div>
        </div>
      </div>
    </div>

    <!-- Recent Activity -->
    <div class="info-card">
      <div class="info-card-hdr"><i class="bi bi-clock-history"></i><span class="info-card-title">Recent Activity</span></div>
      <div class="timeline">
        <div class="tl-item">
          <div class="tl-dot" style="background:rgba(154,128,83,.12);border-color:rgba(154,128,83,.3);"><i class="bi bi-plus" style="color:#9a8053;font-size:.72rem;"></i></div>
          <div class="tl-content">
            <div class="tl-action">Project PRJ-AF004 — Created</div>
            <div class="tl-by">by Priya Nair (FD)</div>
            <div class="tl-time">Today, 10:34 AM</div>
          </div>
        </div>
        <div class="tl-item">
          <div class="tl-dot" style="background:rgba(16,185,129,.12);border-color:rgba(16,185,129,.3);"><i class="bi bi-check-lg" style="color:#10b981;font-size:.66rem;"></i></div>
          <div class="tl-content">
            <div class="tl-action">SR-2024-0198 — QC Passed &amp; Closed</div>
            <div class="tl-by">Downtown Retail Portfolio</div>
            <div class="tl-time">Yesterday, 09:14 AM</div>
          </div>
        </div>
        <div class="tl-item">
          <div class="tl-dot" style="background:rgba(6,182,212,.1);border-color:rgba(6,182,212,.3);"><i class="bi bi-person-fill" style="color:#0891b2;font-size:.62rem;"></i></div>
          <div class="tl-content">
            <div class="tl-action">SR-2024-0201 — Technician Punched In</div>
            <div class="tl-by">Festival City Operations</div>
            <div class="tl-time">2 days ago</div>
          </div>
        </div>
        <div class="tl-item">
          <div class="tl-dot" style="background:rgba(37,99,235,.1);border-color:rgba(37,99,235,.3);"><i class="bi bi-pencil" style="color:#2563eb;font-size:.62rem;"></i></div>
          <div class="tl-content">
            <div class="tl-action">Contact updated — James Harrington</div>
            <div class="tl-by">by Ahmed Al Rashid (Admin)</div>
            <div class="tl-time">5 days ago</div>
          </div>
        </div>
      </div>
    </div>

  </div><!-- /right panel -->
</div><!-- /view-layout -->


<!-- ═════ CREATE PROJECT MODAL ═════ -->
<div class="modal-overlay" id="projectModal" onclick="if(event.target===this)closeProjectModal()">
  <div class="modal-box">
    <div class="modal-hdr">
      <div class="modal-hdr-left">
        <div class="modal-hdr-icon"><i class="bi bi-folder-plus"></i></div>
        <div>
          <h6>Create New Project</h6>
          <div class="modal-hdr-sub">Register a new project under this client</div>
        </div>
      </div>
      <button class="modal-close" onclick="closeProjectModal()"><i class="bi bi-x-lg"></i></button>
    </div>

    <div class="modal-body">

      <!-- Locked client context -->
      <div class="m-section">
        <div class="m-section-label"><span class="step-num">1</span>Client Context</div>
        <div class="locked-banner">
          <i class="bi bi-building-fill"></i>
          <div style="flex:1;min-width:0;">
            <div class="locked-val">{{ $client->company_name }}</div>
            <div class="locked-sub">{{ $client->unique_code }} · Auto-linked to this project</div>
          </div>
          <i class="bi bi-lock-fill" style="font-size:.9rem;"></i>
        </div>
      </div>

      <!-- Project details -->
      <div class="m-section">
        <div class="m-section-label"><span class="step-num">2</span>Project Details</div>
        <div class="form-group">
          <label class="form-label">Project Name <span class="req">*</span></label>
          <input type="text" class="form-control" id="pn-name" placeholder="e.g. Downtown Retail Portfolio" />
          <div class="field-hint">A clear, human-readable identifier for this contract scope.</div>
        </div>
        <div class="form-group" style="margin-bottom:0;">
          <label class="form-label">Project Code <span class="auto-tag">AUTO</span></label>
          <div class="auto-code-row">
            <input type="text" class="form-control auto-code-field" id="pn-code" value="PRJ-A1F004" style="max-width:220px;" oninput="this.value=this.value.toUpperCase()" />
            <button class="btn-ghost" style="padding:6px 11px;font-size:.78rem;" onclick="regenCode()">
              <i class="bi bi-arrow-repeat"></i>Regenerate
            </button>
          </div>
          <div class="field-hint">Auto-generated from client token. Editable if you need a specific format.</div>
        </div>


        <!-- <div class="form-group" style="margin-bottom:0;flex:1;min-width:200px;">
          <label class="form-label">Completion Date</label>
          <div class="auto-code-row">
            <input type="date" class="form-control" id="proj-completion" style="max-width:200px;">
          </div>
          <div class="field-hint">Expected or actual completion date.</div>
        </div> -->








      </div>

      <!-- Site details -->
      <div class="m-section">
        <div class="m-section-label"><span class="step-num">3</span>Site Details</div>
        <div class="form-group">
          <label class="form-label">Site Name <span class="req">*</span></label>
          <input type="text" class="form-control" id="pn-site" placeholder="e.g. Dubai Mall – Ground Floor G12" />
          <div class="field-hint">The physical location where service requests will be raised.</div>
        </div>
        <div class="form-group">
          <label class="form-label">Full Site Address <span class="req">*</span></label>
          <textarea class="form-control" id="pn-addr" rows="2" placeholder="Building name, floor/unit, street, area, city…"></textarea>
        </div>
        <div class="split-row">
          <div class="form-group">
            <label class="form-label">Contract Type</label>
            <select class="form-select" id="pn-contract">
              <option>AMC — Annual Maintenance</option>
              <option>PPM — Periodic</option>
              <option>Reactive Only</option>
              <option>One-Time Project</option>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label">Contract Start</label>
            <input type="date" class="form-control" id="pn-start" />
          </div>
        </div>
        <div class="form-group" style="margin-bottom:0;">
          <div class="tog-wrap" onclick="toggleTog('statusTog',this)">
            <div class="tog-track on" id="statusTog">
              <div class="tog-thumb"></div>
            </div>
            <span class="tog-label">Active — project visible across SR Registration and Dispatch</span>
          </div>
        </div>
      </div>

    </div>
    <div class="modal-foot">
      <button class="btn-ghost" onclick="closeProjectModal()">Cancel</button>
      <button class="btn-gold" id="pn-save-btn" onclick="saveProject()"><i class="bi bi-floppy"></i>Save Project</button>
    </div>
  </div>
</div>

<div id="toastWrap"></div>

@endsection

@push('scripts')
<script>
  /* ─────────────────────────────────────────────
   THEME — declared BEFORE the IIFE that calls it
───────────────────────────────────────────── */


  /* ─────────────────────────────────────────────
     CLIENT + PROJECT DATA
  ───────────────────────────────────────────── */
  var CLIENT = {
    id: @json($client - > id),
    token: @json($client - > unique_code),
    name: @json($client - > company_name),
    contact: @json($client - > contact_name),
    phone: @json(trim(($client - > primary_country ?? '').
      ' '.($client - > primary_mobile ?? '')))
  };

  // Built from the projects table (shaped in the controller)
  var PROJECTS = @json($projectsJs);

  var filteredProjects = PROJECTS.slice();

  /* ─────────────────────────────────────────────
     RENDER PROJECTS
  ───────────────────────────────────────────── */
  function renderProjects(list) {
    var tbody = document.getElementById('pf-tbody');
    if (!list.length) {
      tbody.innerHTML = '<tr><td colspan="8"><div class="empty-st"><i class="bi bi-inbox"></i><h6>No Projects Yet</h6><p>No projects match your filters, or this client has none registered.</p><button class="btn-gold" onclick="openProjectModal()"><i class="bi bi-plus-lg"></i>Create First Project</button></div></td></tr>';
      document.getElementById('pf-count').textContent = '0 projects';
      document.getElementById('pf-page-info').textContent = 'No results';
      return;
    }
    var rows = '';
    for (var i = 0; i < list.length; i++) {
      var p = list[i];
      var badge = p.active ?
        '<span class="sbadge sb-active-g"><i class="bi bi-circle-fill" style="font-size:.32rem;"></i>Active</span>' :
        '<span class="sbadge sb-inactive"><i class="bi bi-circle-fill" style="font-size:.32rem;"></i>Inactive</span>';
      rows += '<tr>' +
        '<td class="muted">' + (i + 1) + '</td>' +
        '<td class="mono">' + p.code + '</td>' +
        '<td><strong style="font-size:.81rem;">' + p.name + '</strong><div style="font-size:.68rem;color:var(--text-muted);margin-top:1px;">' + p.contract + '</div></td>' +
        '<td><div class="site-line"><i class="bi bi-geo-alt-fill"></i><span style="font-size:.79rem;">' + p.siteName + '</span></div></td>' +
        '<td class="muted" style="font-size:.75rem;max-width:200px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;" title="' + p.siteAddress + '">' + p.siteAddress + '</td>' +
        '<td style="text-align:center;"><strong>' + p.srCount + '</strong></td>' +
        '<td>' + badge + '</td>' +
        '<td>' +
        '<div class="row-actions">' +
        '<button class="btn-xs btn-xs-view" onclick="window.location=\'{{ url('
      admin / projects ') }}/\' + p.id"><i class="bi bi-eye"></i>View</button>' +
        '<button class="btn-xs btn-xs-edit" onclick="editProject(' + p.id + ')"><i class="bi bi-pencil"></i></button>' +
        '<button class="btn-xs btn-xs-danger" onclick="confirmDelete(' + p.id + ')"><i class="bi bi-trash3"></i></button>' +
        '</div>' +
        '</td>' +
        '</tr>';
    }
    tbody.innerHTML = rows;
    document.getElementById('pf-count').textContent = list.length + ' project' + (list.length !== 1 ? 's' : '');
    document.getElementById('pf-page-info').textContent = 'Page 1 of 1 · ' + list.length + ' project' + (list.length !== 1 ? 's' : '');
  }

  function filterProjects() {
    var q = document.getElementById('pf-search').value.toLowerCase();
    var st = document.getElementById('pf-status').value;
    filteredProjects = PROJECTS.filter(function(p) {
      if (q && p.name.toLowerCase().indexOf(q) === -1 && p.code.toLowerCase().indexOf(q) === -1 && p.siteName.toLowerCase().indexOf(q) === -1) return false;
      if (st === 'Active' && !p.active) return false;
      if (st === 'Inactive' && p.active) return false;
      return true;
    });
    renderProjects(filteredProjects);
  }

  function resetProjectFilters() {
    document.getElementById('pf-search').value = '';
    document.getElementById('pf-status').value = '';
    filteredProjects = PROJECTS.slice();
    renderProjects(filteredProjects);
  }

  function editProject(id) {
    var p = PROJECTS.find(function(x) {
      return x.id === id;
    });
    showToast('info', 'Edit Project', 'Opening editor for ' + (p ? p.name : 'project'));
  }

  function confirmDelete(id) {
    var p = PROJECTS.find(function(x) {
      return x.id === id;
    });
    if (!p) return;
    if (confirm('Delete "' + p.name + '"? Existing SRs will retain their data but the project will be removed.')) {
      PROJECTS = PROJECTS.filter(function(x) {
        return x.id !== id;
      });
      filterProjects();
      showToast('ok', 'Deleted', p.name + ' has been removed.');
    }
  }

  /* ─────────────────────────────────────────────
     CREATE PROJECT MODAL
  ───────────────────────────────────────────── */
  function openProjectModal() {
    /* reset form */
    document.getElementById('pn-name').value = '';
    document.getElementById('pn-site').value = '';
    document.getElementById('pn-addr').value = '';
    document.getElementById('pn-contract').selectedIndex = 0;
    document.getElementById('pn-start').value = '';
    regenCode();
    var tog = document.getElementById('statusTog');
    if (!tog.classList.contains('on')) tog.classList.add('on');
    var togLbl = tog.parentElement.querySelector('.tog-label');
    if (togLbl) togLbl.textContent = 'Active — project visible across SR Registration and Dispatch';

    document.getElementById('projectModal').classList.add('show');
    document.body.style.overflow = 'hidden';
    setTimeout(function() {
      document.getElementById('pn-name').focus();
    }, 100);
  }

  function closeProjectModal() {
    document.getElementById('projectModal').classList.remove('show');
    document.body.style.overflow = '';
  }

  function regenCode() {
    var year = new Date().getFullYear();
    var chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    var rand = '';
    for (var i = 0; i < 4; i++) {
      rand += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    document.getElementById('pn-code').value = 'PRJ-' + year + '-' + rand;
  }





  function toggleTog(id, wrap) {
    var t = document.getElementById(id);
    t.classList.toggle('on');
    var lbl = wrap.querySelector('.tog-label');
    if (lbl) lbl.textContent = t.classList.contains('on') ?
      'Active — project visible across SR Registration and Dispatch' :
      'Inactive — hidden from SR Registration';
  }

  function saveProject() {
    var name = document.getElementById('pn-name').value.trim();
    var code = document.getElementById('pn-code').value.trim();
    var site = document.getElementById('pn-site').value.trim();
    var addr = document.getElementById('pn-addr').value.trim();
    var contract = document.getElementById('pn-contract').value;
    var startDate = document.getElementById('pn-start').value;
    var active = document.getElementById('statusTog').classList.contains('on');

    if (!name) {
      showToast('err', 'Missing', 'Please enter a project name.');
      document.getElementById('pn-name').focus();
      return;
    }
    if (!site) {
      showToast('err', 'Missing', 'Please enter the site name.');
      document.getElementById('pn-site').focus();
      return;
    }
    if (!addr) {
      showToast('err', 'Missing', 'Please enter the site address.');
      document.getElementById('pn-addr').focus();
      return;
    }

    var btn = document.getElementById('pn-save-btn');
    btn.disabled = true;
    btn.innerHTML = '<i class="bi bi-arrow-repeat"></i> Saving…';

    setTimeout(function() {
      var newId = PROJECTS.reduce(function(a, p) {
        return Math.max(a, p.id);
      }, 0) + 1;
      PROJECTS.push({
        id: newId,
        code: code || ('PRJ-A1F' + String(newId).padStart(3, '0')),
        name: name,
        siteName: site,
        siteAddress: addr,
        contract: contract,
        startDate: startDate || '—',
        srCount: 0,
        active: active
      });
      closeProjectModal();
      filterProjects();
      showToast('ok', 'Project Created', name + ' has been added to this client.');
      btn.disabled = false;
      btn.innerHTML = '<i class="bi bi-floppy"></i>Save Project';
    }, 700);
  }

  /* ─────────────────────────────────────────────
     SIDEBAR / CLOCK
  ───────────────────────────────────────────── */
  function toggleSidebar() {
    document.getElementById('sidebar').classList.toggle('open');
    document.getElementById('sbOverlay').classList.toggle('show');
  }

  function closeSidebar() {
    document.getElementById('sidebar').classList.remove('open');
    document.getElementById('sbOverlay').classList.remove('show');
  }

  function updateClock() {
    var el = document.getElementById('clock');
    if (el) el.textContent = new Date().toLocaleTimeString([], {
      hour: '2-digit',
      minute: '2-digit',
      second: '2-digit'
    });
  }
  setInterval(updateClock, 1000);
  updateClock();

  /* ─────────────────────────────────────────────
     TOAST
  ───────────────────────────────────────────── */
  function showToast(type, title, body) {
    var w = document.getElementById('toastWrap');
    var icons = {
      ok: 'bi-check-circle-fill',
      err: 'bi-x-circle-fill',
      info: 'bi-info-circle-fill'
    };
    var t = document.createElement('div');
    t.className = 'toast-item';
    t.innerHTML = '<i class="bi ' + (icons[type] || icons.info) + ' t-ico ' + type + '"></i><div><p class="t-title">' + title + '</p><p class="t-body">' + body + '</p></div>';
    w.appendChild(t);
    setTimeout(function() {
      t.style.transition = 'opacity .3s';
      t.style.opacity = '0';
      setTimeout(function() {
        t.remove();
      }, 300);
    }, 3500);
  }

  /* ─────────────────────────────────────────────
     INIT
  ───────────────────────────────────────────── */
  renderProjects(PROJECTS);
</script>
@endpush