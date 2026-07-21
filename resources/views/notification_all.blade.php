@extends('layouts.layout')

@section('content')
<div class="notif-page">

    <div class="notif-banner">
        <div class="notif-banner-top">
            <div class="notif-banner-ico"><i class="bi bi-bell-fill"></i></div>
            <div>
                <h4 class="notif-title">All Notifications</h4>
                <span class="notif-sub">Service Request Activity Logs</span>
            </div>
        </div>
        <div class="notif-filters">
            @php $ev = request('event'); $st = request('state'); @endphp
            <a href="{{ route('notifications.all') }}" class="nf-btn {{ !$ev && !$st ? 'act' : '' }}">All</a>
            <a href="{{ route('notifications.all', ['event' => 'sr_created']) }}" class="nf-btn {{ $ev === 'sr_created' ? 'act' : '' }}">Created</a>
            <a href="{{ route('notifications.all', ['event' => 'status_updated']) }}" class="nf-btn {{ $ev === 'status_updated' ? 'act' : '' }}">Status</a>
            <span class="nf-divider"></span>
            <a href="{{ route('notifications.all', ['state' => 'unread']) }}" class="nf-btn {{ $st === 'unread' ? 'act' : '' }}">Unread</a>
        </div>
    </div>

    @php
    $statusMap = [
    'Pending' => ['#fff4e0', '#b7791f'],
    'Approved' => ['#e6f6ec', '#05a34a'],
    'Forwarded' => ['#e7f0fb', '#2563c9'],
    'Rejected' => ['#fdeaea', '#d83a3a'],
    'Assigned' => ['#e9ebff', '#6571ff'],
    'Quoted' => ['#eef3e6', '#5c8a1a'],
    'In Progress' => ['#e0f2f1', '#0d8f7e'],
    'Quote Rejected' => ['#fdeaea', '#d83a3a'],
    'Qc Review' => ['#f3ebfb', '#8b46d4'],
    'Rework' => ['#fdeee0', '#c76a12'],
    'Reschedule' => ['#fef6e0', '#b7791f'],
    'Accepted' => ['#e6f6ec', '#05a34a'],
    'Pending Invoice' => ['#fff4e0', '#b7791f'],
    'Invoice Submitted' => ['#e7f0fb', '#2563c9'],
    'Completed' => ['#e6f6ec', '#05a34a'],
    'On Hold' => ['#fdeaea', '#d83a3a'],
    ];
    @endphp

    <div class="notif-card">
        @forelse($logs as $n)
        @php
        $isCreate = $n->event === 'sr_created';
        $icon = $isCreate ? 'bi-plus-circle-fill' : 'bi-arrow-repeat';
        $iconClr = $isCreate ? '#05a34a' : '#6571ff';
        [$toBg, $toClr] = $statusMap[$n->to_status] ?? ['#f1f1f1', '#666'];
        [$fromBg, $fromClr] = $statusMap[$n->from_status] ?? ['#f1f1f1', '#666'];
        @endphp
        <div class="notif-row {{ $n->read_at ? '' : 'unread' }}">
            <div class="notif-ico" style="background:{{ $isCreate ? 'rgba(5,163,74,.1)' : 'rgba(101,113,255,.1)' }};">
                <i class="bi {{ $icon }}" style="color:{{ $iconClr }};"></i>
            </div>
            <div class="notif-body">
                <div class="notif-row-top">
                    <span class="notif-row-title">{{ $n->title }}</span>
                    @if($n->read_at)
                    <span class="notif-flag read">Read</span>
                    @else
                    <span class="notif-flag unread">Unread</span>
                    @endif
                </div>
                <div class="notif-msg">{{ $n->message }}</div>

                @if($n->to_status)
                <div class="notif-chips">
                    @if($n->from_status)
                    <span class="st-chip" style="background:{{ $fromBg }};color:{{ $fromClr }};">{{ $n->from_status }}</span>
                    <i class="bi bi-arrow-right" style="font-size:.72rem;color:#bbb;"></i>
                    @endif
                    <span class="st-chip" style="background:{{ $toBg }};color:{{ $toClr }};">{{ $n->to_status }}</span>
                </div>
                @endif



                <div class="notif-meta">
                    {{ optional($n->causer)->name ? $n->causer->name . ' · ' : '' }}{{ $n->created_at->diffForHumans() }}
                    <span class="notif-date">· {{ $n->created_at->format('d M Y, h:i A') }}</span>
                </div>

            </div>
        </div>
        @empty
        <div class="notif-empty">No notifications found</div>
        @endforelse
    </div>

    <div class="notif-pager">
        {{ $logs->onEachSide(1)->links() }}
    </div>
</div>

<style>
    .notif-banner {
        background: linear-gradient(135deg, #9A7B4F 0%, #7A6140 100%);
        border-radius: 14px;
        padding: 22px 24px;
        margin-bottom: 22px;
        box-shadow: 0 2px 12px rgba(122, 97, 64, .18);
    }

    .notif-banner-top {
        display: flex;
        align-items: center;
        gap: 14px;
        margin-bottom: 16px;
    }

    .notif-banner-ico {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        background: rgba(255, 255, 255, .15);
        display: flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 auto;
    }

    .notif-banner-ico i {
        color: #fff;
        font-size: 1.15rem;
    }

    .notif-page {
        margin: 0 auto;
        padding: 28px 20px;
    }

    .notif-date {
        color: #c0c0c0;
    }

    .notif-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
        margin-bottom: 22px;
    }

    .notif-title {
        margin: 0;
        font-weight: 600;
        color: #f4eded;
        font-size: 1.35rem;
    }


    .notif-banner::before {
        width: 200px;
        height: 200px;
        background: rgba(255, 255, 255, .06);
        left: -50px;
        bottom: -50px;
    }

    .notif-banner::after {
        width: 160px;
        height: 160px;
        background: rgba(255, 255, 255, .08);
        right: -30px;
        top: -30px;
    }


    .notif-sub {
        font-size: .8rem;
        /* color: var(--text-muted); */
        color: #dde5f3;
    }

    .notif-filters {
        display: flex;
        gap: 6px;
        flex-wrap: wrap;
        align-items: center;

    }

    .nf-btn {
        font-size: .75rem;
        padding: 6px 14px;
        border: 1px solid #e2e2e2;
        border-radius: 20px;
        color: #555;
        text-decoration: none;
        background: #fff;
        line-height: 1;
        white-space: nowrap;
        transition: all .15s;
    }

    .nf-btn:hover {
        border-color: #c9ccff;
        color: #6571ff;
    }

    .nf-btn.act {
        background: #6571ff;
        border-color: #6571ff;
        color: #fff;
    }

    .notif-card {
        background: #fff;
        border: 1px solid #eee;
        border-radius: 12px;
        overflow: hidden;
    }

    .notif-row {
        display: flex;
        gap: 12px;
        padding: 16px;
        align-items: flex-start;
        border-bottom: 1px solid #d9d7d3;
        background: #fff;
    }

    .notif-row:last-child {
        border-bottom: none;
    }

    .notif-row.unread {
        background: #f7f8ff;
    }

    .notif-ico {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 auto;
        margin-top: 1px;
    }

    .notif-ico i {
        font-size: 1rem;
    }

    .notif-body {
        flex: 1;
        min-width: 0;
    }

    .notif-row-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
    }

    .notif-row-title {
        font-size: .9rem;
        font-weight: 600;
        color: #464c55;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .notif-flag {
        font-size: .64rem;
        flex: 0 0 auto;
    }

    .notif-flag.read {
        color: #a8a8a8;
    }

    .notif-flag.unread {
        font-weight: 700;
        color: #6571ff;
        background: #e9ebff;
        padding: 2px 9px;
        border-radius: 10px;
    }

    .notif-msg {
        font-size: .8rem;
        color: var(--text-muted);
        margin-top: 3px;
        line-height: 1.45;
    }

    .notif-chips {
        display: flex;
        align-items: center;
        gap: 6px;
        margin-top: 8px;
        flex-wrap: wrap;
    }

    .st-chip {
        font-size: .64rem;
        padding: 3px 9px;
        border-radius: 10px;
        white-space: nowrap;
        line-height: 1.3;
    }

    .notif-meta {
        font-size: .68rem;
        color: #a8a8a8;
        margin-top: 6px;
    }

    .notif-empty {
        padding: 44px;
        text-align: center;
        color: #aaa;
        font-size: .85rem;
    }

    /* ---------- PAGINATION ---------- */
    .notif-pager {
        margin-top: 18px;
        display: flex;
        justify-content: center;
    }

    /* hide the default "Showing x to y of z results" text block */
    .notif-pager nav>p,
    .notif-pager nav>div:first-child:not(.pagination) {
        display: none;
    }

    /* center the numbered list */
    .notif-pager nav {
        display: flex;
        justify-content: center;
        width: 100%;
    }

    .notif-pager ul,
    .notif-pager .pagination {
        display: flex;
        align-items: center;
        gap: 4px;
        list-style: none;
        padding: 0;
        margin: 0;
        flex-wrap: wrap;
    }

    /* clamp any SVG chevrons so they don't blow up */
    .notif-pager svg {
        width: 16px;
        height: 16px;
        display: inline-block;
    }

    /* box ONLY the links/spans inside pagination list items */
    .notif-pager li a,
    .notif-pager li span,
    .notif-pager .pagination a,
    .notif-pager .pagination span {
        display: flex;
        align-items: center;
        justify-content: center;
        min-width: 34px;
        height: 34px;
        padding: 0 10px;
        font-size: .78rem;
        border: 1px solid #e2e2e2;
        border-radius: 8px;
        color: #555;
        text-decoration: none;
        background: #fff;
        line-height: 1;
        transition: all .15s;
    }

    .notif-pager li a:hover {
        border-color: #c9ccff;
        color: #6571ff;
    }

    .notif-pager li.active span,
    .notif-pager .pagination [aria-current] span {
        background: #6571ff;
        border-color: #6571ff;
        color: #fff;
    }

    .notif-pager li.disabled span {
        color: #ccc;
        background: #fafafa;
        cursor: not-allowed;
    }

    /* ---------- END PAGINATION ---------- */

    @media (max-width: 560px) {
        .notif-head {
            flex-direction: column;
            align-items: flex-start;
        }

        .notif-filters {
            width: 100%;
        }

        .notif-page {
            padding: 20px 14px;
        }
    }

    .nf-divider {
        width: 1px;
        height: 20px;
        background: #e2e2e2;
        align-self: center;
        margin: 0 4px;
    }
</style>
@endsection