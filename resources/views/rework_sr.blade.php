@extends('layouts.layout')

@section('title', 'Rework SRs — Digit-Us Portal')
@section('page_title', 'Rework SRs')
@section('page_icon', 'person-check')


@push('styles')
<style>
    /* PAGE HEADER */
    .pg-header {
        border-radius: 10px;
        padding: 18px 24px;
        margin-bottom: 20px;
        color: #fff;
        position: relative;
        overflow: hidden;
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

    /* STATS STRIP */
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

    /* FILTER BAR */
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
        min-width: 130px;
        transition: border-color .15s, box-shadow .15s;
    }

    .filter-control:focus {
        outline: none;
        border-color: #9A7B4F;
        box-shadow: var(--input-focus-shadow);
    }

    .filter-search {
        min-width: 220px;
    }

    .filter-actions {
        margin-left: auto;
        display: flex;
        gap: 8px;
        align-items: flex-end;
    }

    /* TABLE CARD */
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

    table.listing tbody tr {
        border-bottom: 1px solid var(--border-color);
        transition: background .1s;
        cursor: pointer;
    }

    table.listing tbody tr:last-child {
        border-bottom: none;
    }

    table.listing tbody tr:hover {
        background: var(--table-hover);
    }

    table.listing td {
        padding: 11px 16px;
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
    .mono .sr-ref-trigger{ cursor:pointer; }
    /* BUTTONS */
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

    .btn-xs-view {
        background: rgba(37, 99, 235, .1);
        color: #3b82f6;
    }

    .btn-xs-view:hover {
        background: rgba(37, 99, 235, .2);
    }

    /* BADGES */
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

    .sb-assigned {
        background: rgba(37, 99, 235, .12);
        color: #2563eb;
    }

    .sb-progress {
        background: rgba(245, 158, 11, .14);
        color: #d97706;
    }

    .sb-accepted {
        background: rgba(16, 185, 129, .15);
        color: #059669;
    }

    .sb-hold {
        background: rgba(107, 114, 128, .15);
        color: #6b7280;
    }

    .sb-warranty {
        background: rgba(16, 185, 129, .1);
        color: #059669;
    }

    .sb-oow {
        background: rgba(239, 68, 68, .1);
        color: #ef4444;
    }

    /* PRIORITY PILLS */
    .pri {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 3px 9px;
        border-radius: 6px;
        font-size: .7rem;
        font-weight: 600;
        white-space: nowrap;
    }

    .pri-high {
        background: rgba(239, 68, 68, .12);
        color: #ef4444;
    }

    .pri-medium {
        background: rgba(245, 158, 11, .14);
        color: #d97706;
    }

    .pri-low {
        background: rgba(16, 185, 129, .12);
        color: #059669;
    }

    .pri-none {
        background: var(--surface-2);
        color: var(--text-muted);
    }

    /* SLA / DATE CELLS */
    .cell-sla {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-size: .78rem;
        color: var(--text-muted);
    }

    .cell-sla.overdue {
        color: #ef4444;
        font-weight: 600;
    }

    .worker-cell {
        display: flex;
        align-items: center;
        gap: 9px;
    }

    .w-av {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        background: linear-gradient(135deg, #9A7B4F, #C4A882);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: .68rem;
        font-weight: 700;
        color: #fff;
        flex-shrink: 0;
    }

    .w-av.unassigned {
        background: var(--surface-3);
        color: var(--text-muted);
    }

    /* PAGINATION */
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

    /* TOAST */
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

    /* EMPTY STATE */
    .empty-row td {
        text-align: center;
        padding: 40px 20px;
        color: var(--text-muted);
        font-size: .82rem;
    }

    .empty-row td i {
        display: block;
        font-size: 2rem;
        color: var(--text-light);
        margin-bottom: 8px;
    }

    /* ══════════════════════════════════════════
   SR DETAIL MODAL (assignment view)
══════════════════════════════════════════ */
    .sr-modal-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: var(--overlay-bg, rgba(9, 15, 35, .6));
        z-index: 1000;
        align-items: center;
        justify-content: center;
        backdrop-filter: blur(4px);
        padding: 20px;
    }

    .sr-modal-overlay.show {
        display: flex;
    }

    .sr-modal-box {
        background: var(--modal-bg, var(--card-bg));
        border-radius: 12px;
        width: 100%;
        max-width: 680px;
        max-height: 90vh;
        display: flex;
        flex-direction: column;
        box-shadow: var(--modal-shadow, 0 24px 64px rgba(0, 0, 0, .16));
        border: 1px solid var(--card-border);
        overflow: hidden;
        animation: srModalIn .2s ease;
    }

    @keyframes srModalIn {
        from {
            opacity: 0;
            transform: scale(.96) translateY(6px);
        }

        to {
            opacity: 1;
            transform: scale(1) translateY(0);
        }
    }

    .sr-modal-hdr {
        border-bottom: 1px solid var(--border-color);
        flex-shrink: 0;
    }

    .sr-modal-hdr-banner {
        background: var(--app-bg);
        padding: 16px 22px;
        position: relative;
        overflow: hidden;
    }

    .sr-modal-hdr-banner::after {
        content: '';
        position: absolute;
        right: -30px;
        top: -30px;
        width: 130px;
        height: 130px;
        border-radius: 50%;
        background: rgba(255, 255, 255, .08);
    }

    .sr-modal-close {
        position: absolute;
        top: 14px;
        right: 16px;
        z-index: 2;
        background: rgb(165 165 165);
        border: none;
        color: #fff;
        width: 30px;
        height: 30px;
        border-radius: 7px;
        cursor: pointer;
        font-size: 1rem;
        line-height: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background .15s;
    }

    .sr-modal-close:hover {
        background: rgb(165 165 165);
    }

    .sr-modal-id {
        font-size: 1.05rem;
        font-weight: 700;
        margin: 0 0 4px;
        position: relative;
        z-index: 1;
    }

    .sr-modal-client {
        font-size: .82rem;
        opacity: .9;
        position: relative;
        z-index: 1;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .sr-modal-body {
        padding: 20px 22px;
        overflow-y: auto;
    }

    .sr-detail-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 14px 20px;
    }

    @media(max-width:560px) {
        .sr-detail-grid {
            grid-template-columns: 1fr;
        }
    }

    .sr-detail-item {
        display: flex;
        flex-direction: column;
        gap: 4px;
        min-width: 0;
    }

    .sr-detail-item.full {
        grid-column: 1 / -1;
    }

    .sr-detail-label {
        font-size: .66rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .06em;
        color: var(--text-muted);
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .sr-detail-label i {
        font-size: .8rem;
        color: #9A7B4F;
    }

    .sr-detail-value {
        font-size: .85rem;
        color: var(--text-heading);
        font-weight: 500;
        word-break: break-word;
    }

    .sr-detail-value.muted {
        color: var(--text-muted);
        font-weight: 400;
    }

    .sr-detail-divider {
        grid-column: 1 / -1;
        height: 1px;
        background: var(--border-color);
        margin: 2px 0;
    }

    .sr-modal-foot {
        padding: 14px 22px;
        border-top: 1px solid var(--border-color);
        display: flex;
        gap: 10px;
        justify-content: flex-end;
        flex-shrink: 0;
        flex-wrap: wrap;
    }

    @media(max-width:480px) {
        .sr-modal-foot {
            justify-content: stretch;
        }

        .sr-modal-foot>* {
            flex: 1;
            justify-content: center;
        }
    }

    .rw-tab {
        padding: 7px 16px;
        border: 1px solid var(--border, #e5e7eb);
        background: #fff;
        border-radius: 8px;
        font-size: .8rem;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .rw-tab.active {
        background: linear-gradient(135deg, #9A7B4F, #7A6140);
        color: #fff;
        border-color: transparent;
    }

    span.sbadge.sb-realloc {
        background: rgba(37, 99, 235, .12);
    }
</style>
@endpush

@section('content')

<div class="pg-header" style="background:linear-gradient(135deg,#9A7B4F 0%,#7A6140 100%);">
    <h4><i class="bi bi-person-check me-2"></i>Rework Service Requests</h4>

    <div class="meta-row">
        <span class="meta-badge"><i class="bi bi-person-workspace me-1"></i>Worker Assignment</span>
        <span class="meta-badge"><i class="bi bi-calendar-event me-1"></i>Scheduled Visits</span>
        <span class="meta-badge"><i class="bi bi-hourglass-split me-1"></i>SLA Tracking</span>
    </div>
</div>

<div class="stats-strip">
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(37,99,235,.1);"><i class="bi bi-list-ul" style="color:#2563eb;"></i></div>
        <div>
            <div class="stat-num">{{ $stats['rework'] ?? 0 }}</div>
            <div class="stat-lbl">Rework</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(139,70,212,.12);"><i class="bi bi-arrow-left-right" style="color:#8b46d4;"></i></div>
        <div>
            <div class="stat-num">{{ $stats['reallocated'] ?? 0 }}</div>
            <div class="stat-lbl">Reallocated</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(245,158,11,.1);"><i class="bi bi-calendar-day" style="color:#d97706;"></i></div>
        <div>
            <div class="stat-num">{{ $stats['today'] ?? 0 }}</div>
            <div class="stat-lbl">Today</div>
        </div>
    </div>

</div>


<div class="rw-tabs" style="display:flex;gap:8px;margin-bottom:14px;">
    <button type="button" class="rw-tab active" data-tab="all" onclick="setReworkTab('all')">
        <i class="bi bi-list-ul"></i> All
        <!-- <span class="rw-tab-count">{{ $stats['rework'] ?? 0 }}</span> -->
    </button>
    <button type="button" class="rw-tab" data-tab="realloc" onclick="setReworkTab('realloc')">
        <i class="bi bi-arrow-left-right"></i> Reallocated
        <!-- <span class="rw-tab-count">{{ $stats['reallocated'] ?? 0 }}</span> -->
    </button>
</div>


<form id="filterForm" onsubmit="return false">
    <input type="hidden" name="tab" id="rwTab" value="all">

    <div class="filter-bar">
        <div class="filter-group">
            <div class="filter-label">Search</div>
            <input class="filter-control filter-search" type="text" name="search"
                placeholder="SR ID, customer, worker…" oninput="debounceFilter()" />
        </div>

        <div class="filter-group">

            <div class="filter-label">Priority</div>
            <select class="filter-control" name="priority" onchange="applyFilters()">
                <option value="">All</option>
                @foreach(($priorities ?? []) as $p)
                <option value="{{ $p->name }}"
                    data-color="{{ $p->color }}"
                    @selected(request('priority')===$p->name)>
                    {{ $p->name }}
                </option>
                @endforeach
            </select>
        </div>

        <div class="filter-actions">
            <button type="button" class="btn-ghost" onclick="resetFilters()"><i class="bi bi-x-circle"></i>Reset</button>

        </div>
    </div>
</form>

<div class="tbl-card">
    <div class="tbl-card-hdr">
        @fragment('hdr')
        <div class="tbl-card-hdr-left">
            <span class="tbl-card-title">
                {{ request('tab') === 'realloc' ? 'Reallocated Jobs' : 'Rework Jobs' }}
            </span>
            <span class="result-count" id="result-count">
                Showing {{ $assigned->count() }} of {{ $assigned->total() }}
            </span>
        </div>
        @endfragment
    </div>


    <div class="tbl-wrap">
        <table class="listing" id="sr-table">
            <thead>
                <tr>
                    <th>SR ID</th>
                    <th>Customer</th>
                    <th>Site / Location</th>
                    <th>Worker</th>
                    <th class="owner-column"
                        style="{{ request('tab') === 'realloc' ? '' : 'display:none;' }}">
                        Owner
                    </th>
                    <th>Priority</th>
                    <th>Status</th>
                    <th>Updated On</th>
                    <th style="width:70px;">Action</th>
                </tr>
            </thead>
            <tbody id="sr-tbody">

                @fragment('rows')

                @php
                $isReallocated = request('tab') === 'realloc';
                @endphp

                @forelse($assigned as $sr)
                @php
                $srCode = 'SR-'.\Carbon\Carbon::parse($sr->created_at)->format('Y').'-'.str_pad($sr->id,5,'0',STR_PAD_LEFT);

                $assignedAt = $sr->updated_at;
                // $isReallocated = request('tab') === 'realloc';


                $worker = $isReallocated
                ? (optional($sr->reallocateUser)->name ?? 'Unassigned')
                : (optional($sr->assignedUser)->name ?? 'Unassigned');

                $owner = optional($sr->assignedUser)->name ?? 'Unassigned';


                $eta = $sr->eta_at ? \Carbon\Carbon::parse($sr->eta_at) : null;
                $isOverdue = $eta && $eta->isPast();

                $isOow = ($sr->warranty_scope ?? '') === 'Out of Warranty';

                $status = $sr->status ?? '—';
                $statusLbl = \Illuminate\Support\Str::headline($status);
                $priority = $sr->priority_level ?? '—';
                $priKey = strtolower($priority);

                $statusClass = match($status){
                'in_progress' => 'sb-progress',
                'accepted' => 'sb-accepted',
                'on_hold' => 'sb-hold',
                default => 'sb-assigned',
                };

                if ($isReallocated) {
                $statusLbl = 'Reallocated';
                $statusClass = 'sb-realloc';
                }

                $priClass = match($priKey){
                'high' => 'pri-high',
                'medium' => 'pri-medium',
                'low' => 'pri-low',
                default => 'pri-none',
                };

                $srPayload = [
                'code' => $srCode,
                'dbId' => $sr->id,
                'client' => optional($sr->client)->company_name ?? '—',
                'site' => optional($sr->project)->site_name ?? '—',
                'worker' => $worker,
                'issue' => $sr->issue_description ?? '—',
                'status' => $statusLbl,
                'priority' => $priority,
                'realloc_remarks' => $sr->relocation_remarks ?? null,
                // 'warranty' => $isOow ? 'Out of Warranty' : 'In Warranty',
                'warranty' => ($sr->project
                && $sr->project->warranty_end_date
                && \Carbon\Carbon::parse($sr->project->warranty_end_date)->endOfDay()->isFuture())
                ? 'In Warranty'
                : 'Out of Warranty',
                // 'contact' => optional($sr->client)->primary_mobile ?? optional($sr->client)->contact_number ?? '—',

                'contact' => (function () use ($sr) {
                $c = $sr->client;
                if (! $c) return '—';
                $num = $c->primary_mobile ?? $c->contact_number ?? null;
                if (! $num) return '—';
                return trim(($c->primary_country ?? '') . ' ' . $num);
                })(),

                'scheduled' => $assignedAt->format('d M Y · h:i A'),
                'assigned' => $assignedAt->format('d M Y · h:i A'),
                'assigned_h' => $assignedAt->diffForHumans(),
                'sla_due' => $eta ? $eta->format('d M Y · h:i A') : '—',
                'sla_due_h' => $eta ? $eta->diffForHumans() : '—',
                'is_overdue' => $isOverdue,
                ];
                @endphp
                <tr data-sr='@json($srPayload)' onclick="openSrModal(this)">
                    <td class="mono">
                        <span class="sr-ref-trigger" data-sr-id="{{ $sr->id }}" onclick="event.stopPropagation(); openSrTracking({{ $sr->id }});">
                            {{ $srCode }}
                        </span>
                    </td>
                    <td><strong style="font-size:.82rem">{{ optional($sr->client)->company_name ?? '—' }}</strong></td>
                    <td class="muted">{{ optional($sr->project)->site_name ?? '—' }}</td>
                    <td>
                        <div class="worker-cell">
                            <span class="w-av {{ $worker === 'Unassigned' ? 'unassigned' : '' }}">
                                {{ strtoupper(\Illuminate\Support\Str::substr($worker, 0, 2)) }}
                            </span>

                            <span style="font-size:.8rem;">
                                {{ $worker }}
                            </span>
                        </div>
                    </td>

                    {{-- Owner --}}
                    <td class="owner-column"
                        style="{{ $isReallocated ? '' : 'display:none;' }}">
                        <div class="worker-cell">
                            <span class="w-av {{ $owner === 'Unassigned' ? 'unassigned' : '' }}">
                                {{ strtoupper(\Illuminate\Support\Str::substr($owner, 0, 2)) }}
                            </span>

                            <span style="font-size:.8rem;">
                                {{ $owner }}
                            </span>
                        </div>
                    </td>

                    <td><span class="pri {{ $priClass }}"><i class="bi bi-flag-fill" style="font-size:.6rem;"></i>{{ $priority }}</span></td>
                    <td><span class="sbadge {{ $statusClass }}"><i class="bi bi-circle-fill" style="font-size:.4rem;"></i>{{ $statusLbl }}</span></td>
                    <!-- <td>
                        <span class="cell-sla {{ $isOverdue ? 'overdue' : '' }}">
                            <i class="bi {{ $isOverdue ? 'bi-exclamation-triangle-fill' : 'bi-hourglass-split' }}"></i>{{ $eta ? $eta->diffForHumans() : '—' }}
                        </span>
                    </td> -->

                    {{-- Updated On --}}
                    <td class="muted" style="font-size:.8rem;" title="{{ $assignedAt->format('d M Y · h:i A') }}">
                        {{ $assignedAt->format('d M Y') }}
                        <div style="font-size:.72rem;opacity:.7;">{{ $assignedAt->format('h:i A') }}</div>
                    </td>

                    <td onclick="event.stopPropagation()">
                        <div style="display:flex;gap:5px;">
                            <button class="btn-xs btn-xs-view" onclick="openSrModal(this.closest('tr'))">
                                <i class="bi bi-eye"></i> View
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr class="empty-row">
                    <td colspan="{{ $isReallocated ? 9 : 8 }}"><i class="bi bi-inbox"></i>No assigned service requests found.</td>
                </tr>
                @endforelse
                @endfragment
            </tbody>
        </table>
    </div>

    <div class="pagination-bar">
        @fragment('pager')
        <div class="page-info">
            Page {{ $assigned->currentPage() }} of {{ $assigned->lastPage() }} · {{ $assigned->total() }} total records
        </div>
        <div class="page-btns">
            <button class="page-btn" {{ $assigned->onFirstPage() ? 'disabled' : '' }}
                onclick="goToPage({{ $assigned->currentPage() - 1 }})"><i class="bi bi-chevron-left"></i></button>
            @foreach($assigned->getUrlRange(1, $assigned->lastPage()) as $page => $url)
            <button class="page-btn {{ $page == $assigned->currentPage() ? 'active' : '' }}"
                onclick="goToPage({{ $page }})">{{ $page }}</button>
            @endforeach
            <button class="page-btn" {{ $assigned->hasMorePages() ? '' : 'disabled' }}
                onclick="goToPage({{ $assigned->currentPage() + 1 }})"><i class="bi bi-chevron-right"></i></button>
        </div>
        @endfragment
    </div>
</div>

{{-- ══════════════ SR ASSIGNMENT MODAL ══════════════ --}}
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
                    <span class="sr-detail-value"><span class="sbadge sb-assigned" id="sr-m-status-badge"><i class="bi bi-circle-fill" style="font-size:.4rem;"></i> <span id="sr-m-status">—</span></span></span>
                </div>
                <div class="sr-detail-item">
                    <span class="sr-detail-label"><i class="bi bi-flag-fill"></i>Priority</span>
                    <span class="sr-detail-value" id="sr-m-priority">—</span>
                </div>

                <div class="sr-detail-divider"></div>

                <div class="sr-detail-item">
                    <span class="sr-detail-label"><i class="bi bi-geo-alt"></i>Site / Location</span>
                    <span class="sr-detail-value" id="sr-m-site">—</span>
                </div>
                <div class="sr-detail-item">
                    <span class="sr-detail-label"><i class="bi bi-person-workspace"></i>Assigned Worker</span>
                    <span class="sr-detail-value" id="sr-m-worker">—</span>
                </div>
                <div class="sr-detail-item">
                    <span class="sr-detail-label"><i class="bi bi-telephone"></i>Customer Contact</span>
                    <span class="sr-detail-value" id="sr-m-contact">—</span>
                </div>
                <div class="sr-detail-item">
                    <span class="sr-detail-label"><i class="bi bi-shield-check"></i>Warranty Scope</span>
                    <span class="sr-detail-value" id="sr-m-warranty">—</span>
                </div>

                <div class="sr-detail-item full">
                    <span class="sr-detail-label"><i class="bi bi-card-text"></i>Reported Issue</span>
                    <span class="sr-detail-value muted" id="sr-m-issue">—</span>
                </div>



                <div class="sr-detail-item full" id="sr-m-remarks-wrap" style="display:none;">
                    <span class="sr-detail-label"><i class="bi bi-arrow-left-right"></i>Reallocation Remarks</span>
                    <span class="sr-detail-value muted" id="sr-m-remarks">—</span>
                </div>


                <div class="sr-detail-divider"></div>

                <div class="sr-detail-item">
                    <span class="sr-detail-label"><i class="bi bi-calendar-event"></i>Updated On</span>
                    <span class="sr-detail-value muted" id="sr-m-scheduled">—</span>
                </div>
                <!-- <div class="sr-detail-item">
                    <span class="sr-detail-label"><i class="bi bi-person-check"></i>Assigned On</span>
                    <span class="sr-detail-value muted" id="sr-m-assigned">—</span>
                </div> -->


                <!-- <div class="sr-detail-item full">
                    <span class="sr-detail-label"><i class="bi bi-hourglass-split"></i>SLA Due</span>
                    <span class="sr-detail-value" id="sr-m-sla" style="font-size:1.05rem;font-weight:700;">—</span>
                </div> -->


            </div>
        </div>
        <div class="sr-modal-foot">
            <button class="btn-ghost" onclick="closeSrModal()"><i class="bi bi-x-circle"></i>Close</button>
        </div>
    </div>
</div>

<div id="toastWrap"></div>
@include('partials.sr_tracking_modal')
@endsection


@push('scripts')
<script>
    var currentPage = 1;
    var filterTimer = null;
    var currentSr = {}; // payload of the SR whose modal is open

    // ---- Toast ----
    function showToast(type, title, body) {
        var w = document.getElementById('toastWrap');
        if (!w) return;
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

    function _set(id, val) {
        var el = document.getElementById(id);
        if (el) el.textContent = (val === null || val === undefined || val === '') ? '—' : val;
    }

    // ══════════════ ASSIGNMENT MODAL ══════════════
    function openSrModal(row) {
        if (!row) return;
        var data;
        try {
            data = JSON.parse(row.getAttribute('data-sr') || '{}');
        } catch (e) {
            data = {};
        }
        currentSr = data;

        var idEl = document.getElementById('sr-m-id');
        if (idEl) {
            idEl.textContent = data.code || '—';
            if (data.dbId) {
                idEl.classList.add('sr-ref-trigger');
                idEl.style.cursor = 'pointer';
                idEl.onclick = function(){ openSrTracking(data.dbId); };
            }
        }
        _set('sr-m-client', data.client);
        _set('sr-m-status', data.status);
        _set('sr-m-priority', data.priority);
        _set('sr-m-site', data.site);
        _set('sr-m-worker', data.worker);
        _set('sr-m-contact', data.contact);
        _set('sr-m-warranty', data.warranty);
        _set('sr-m-issue', data.issue);
        _set('sr-m-scheduled', data.scheduled);
        _set('sr-m-assigned', data.assigned);

        // SLA due — colour red when overdue.
        var sla = document.getElementById('sr-m-sla');
        if (sla) {
            sla.textContent = (data.sla_due || '—') + (data.sla_due_h && data.sla_due_h !== '—' ? '  ·  ' + data.sla_due_h : '');
            sla.style.color = data.is_overdue ? '#ef4444' : 'var(--text-heading)';
        }


        // Reallocation remarks — only render when the SR actually has them
        var rmWrap = document.getElementById('sr-m-remarks-wrap');
        if (rmWrap) {
            var rm = (data.realloc_remarks || '').trim();
            if (rm !== '') {
                _set('sr-m-remarks', rm);
                rmWrap.style.display = '';
            } else {
                rmWrap.style.display = 'none';
            }
        }

        document.getElementById('srModal').classList.add('show');
        document.body.style.overflow = 'hidden';
    }

    function closeSrModal() {
        document.getElementById('srModal').classList.remove('show');
        document.body.style.overflow = '';
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeSrModal();
    });

    // ══════════════ FILTERING / PAGINATION ══════════════
    function debounceFilter() {
        clearTimeout(filterTimer);
        filterTimer = setTimeout(function() {
            currentPage = 1;
            applyFilters();
        }, 400);
    }

    function goToPage(p) {
        currentPage = p;
        applyFilters();
    }

    function applyFilters() {
        var form = document.getElementById('filterForm');
        var params = new URLSearchParams(new FormData(form));
        params.set('page', currentPage);

        updateExportHref(); // ← replaces the inline exportBtn.href line

        fetch(window.location.pathname + "?" + params.toString() + "&frag=rows", {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(function(r) {
                return r.text();
            })
            .then(function(html) {
                document.getElementById('sr-tbody').innerHTML = html;
            });


        fetch(window.location.pathname + "?" + params.toString() + "&frag=hdr", {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(function(r) {
                return r.text();
            })
            .then(function(html) {
                document.querySelector('.tbl-card-hdr').innerHTML = html;
            });

        fetch(window.location.pathname + "?" + params.toString() + "&frag=pager", {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(function(r) {
                return r.text();
            })
            .then(function(html) {
                document.querySelector('.pagination-bar').innerHTML = html;
            });
    }

    function resetFilters() {
        document.getElementById('filterForm').reset();
        currentPage = 1;
        var tab = document.getElementById('rwTab').value;
        document.querySelectorAll('.rw-tab').forEach(function(b) {
            b.classList.toggle('active', b.dataset.tab === tab);
        });
        applyFilters();
    }


    // Set export href on initial load (before any filter change)
    document.addEventListener('DOMContentLoaded', function() {
        updateExportHref();
    });

    function updateExportHref() {
        var form = document.getElementById('filterForm');
        if (!form) return;
        var params = new URLSearchParams(new FormData(form));
        params.set('page', currentPage);
        var exportBtn = document.getElementById('exportBtn');
        if (exportBtn) {
            exportBtn.href = window.location.pathname + '?' + params.toString() + '&export=csv';
        }
    }

    function setReworkTab(tab) {

        document.getElementById('rwTab').value = tab;

        document.querySelectorAll('.rw-tab').forEach(b => {
            b.classList.toggle('active', b.dataset.tab === tab);
        });

        // Show/hide Owner column
        document.querySelectorAll('.owner-column').forEach(el => {
            el.style.display = tab === 'realloc' ? '' : 'none';
        });

        currentPage = 1;

        applyFilters();
    }
</script>
@endpush