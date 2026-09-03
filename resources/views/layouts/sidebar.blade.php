<!-- Sidebar Overlay (mobile) -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

@php
/**
 * Every link is gated by its permission KEY (matches permissions.key).
 * A link renders when the user has ANY access ('yes' or 'rls') to that key,
 * or the key is in their fd_grants/ac_grants. Only 'no' hides it.
 *
 * Section headings appear only when at least one child link is visible.
 */

$u        = auth()->user();
$roleCode = $u?->role?->code;

/**
 * Sidebar visibility: show the link if the user has ANY access to the
 * page - including 'rls' (own-record-only) - not just full 'yes' access.
 * Whether they can actually WRITE anything on that page is enforced
 * separately, per-page via isReadonly()/canWrite() and per-record via
 * the Policy - this flag only controls whether the link renders.
 */
$can = fn ($key) => $u && $u->hasAnyAccess($key);

/** Permission granted AND the role isn't on the exclusion list. */
$show = fn ($key, array $except = []) => $can($key) && ! in_array($roleCode, $except, true);

// Live pending count for the Inquiry Approval badge.
$pendingCount = \App\Models\ServiceRequest::where('status', 'Pending')->count();
@endphp

<aside class="sidebar" id="sidebar">

    <a href="{{ route('dashboard') }}" class="sidebar-brand">
        <div class="brand-mark">
            <img src="{{ asset('assets/images/favicon.png') }}"
                 alt="Matter Mind"
                 style="width:35px;height:40px;object-fit:contain;padding:3px">
        </div>
        <div class="brand-text">
            MATTER MIND
            <small>Service That Matters. Always.</small>
        </div>
    </a>

    <ul class="sidebar-nav">

        {{-- ══════════ MAIN ══════════ --}}
        @php
            $showMain = $can('dashboard') || $can('sr_registration')
                     || $can('sr_explorer') || $can('kanban_view');
        @endphp
        @if ($showMain)
            <li class="sidebar-heading">Main</li>

            @if ($can('dashboard'))
                <li>
                    <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i data-feather="grid"></i><span>Dashboard</span>
                    </a>
                </li>
            @endif

                        @if ($can('sr_registration'))
                <li>
                    <a href="{{ route('sr_registration') }}" class="{{ request()->routeIs('sr_registration') ? 'active' : '' }}">
                        <i data-feather="file-plus"></i><span>SR Registration</span>
                    </a>
                </li>
            @endif

            @if ($can('sr_explorer'))
                <li>
                    <a href="{{ route('sr_explorer') }}" class="{{ request()->routeIs('sr_explorer') ? 'active' : '' }}">
                        <i data-feather="search"></i><span>SR Explorer</span>
                    </a>
                </li>
            @endif

            @if ($can('kanban_view'))
                <li>
                    <a href="{{ route('kanban_view') }}" class="{{ request()->routeIs('kanban_view') ? 'active' : '' }}">
                        <i data-feather="trello"></i><span>Ticket Summary</span>
                    </a>
                </li>
            @endif
        @endif

        {{-- ══════════ CUSTOMER ══════════ --}}
        @php
            $showCustomer = $can('client_accounts') || $can('client_directory')
                         || $can('project_site_directory');
        @endphp
        @if ($showCustomer)
            <li class="sidebar-heading">Customer</li>

           {{-- @if ($show('client_accounts', ['SE']))
                <li>
                    <a href="{{ route('clients.create') }}" class="{{ request()->routeIs('clients.create', 'clients.edit') ? 'active' : '' }}">
                        <i data-feather="user-plus"></i><span>Customer Accounts</span>
                    </a>
                </li>
            @endif --}}

            @if ($can('client_directory'))
                <li>
                    <a href="{{ route('clients.directory') }}" class="{{ request()->routeIs('clients.directory') ? 'active' : '' }}">
                        <i data-feather="book-open"></i><span>Customer Directory</span>
                    </a>
                </li>
            @endif

            @if ($can('project_site_directory'))
                <li>
                    <a href="{{ route('project_site_directory') }}" class="{{ request()->routeIs('project_site_directory') ? 'active' : '' }}">
                        <i data-feather="map-pin"></i><span>Projects &amp; Sites</span>
                    </a>
                </li>
            @endif
        @endif

        {{-- ══════════ WORKFLOW ══════════ --}}
        @php
            $showWorkflow = $can('inquiry_approval') || $can('dispatch_engine')
                         || $can('assigned') || $can('qc_review') || $can('completed');
        @endphp
        @if ($showWorkflow)
            <li class="sidebar-heading">Workflow</li>

            @if ($show('inquiry_approval'))
                <li>
                    <a href="{{ route('inquiry-approval.index') }}" class="{{ request()->routeIs('inquiry-approval.index') ? 'active' : '' }}">
                        <i data-feather="inbox"></i><span>Inquiry Approval</span>
                        @if ($pendingCount > 0)
                            <span class="badge-pill">{{ $pendingCount }}</span>
                        @endif
                    </a>
                </li>
            @endif

            @if ($can('dispatch_engine'))
                <li>
                    <a href="{{ route('dispatch_engine') }}" class="{{ request()->routeIs('dispatch_engine') ? 'active' : '' }}">
                        <i data-feather="send"></i><span>Dispatch Engine</span>
                    </a>
                </li>
            @endif

            @if ($can('assigned'))
                <li>
                    <a href="{{ route('assigned') }}" class="{{ request()->routeIs('assigned') ? 'active' : '' }}">
                        <i data-feather="user-check"></i><span>Approved SR</span>
                    </a>
                </li>
            @endif

            @if ($can('qc_review'))
                <li>
                    <a href="{{ route('qc_review') }}" class="{{ request()->routeIs('qc_review') ? 'active' : '' }}">
                        <i data-feather="check-circle"></i><span>QC Review</span>
                    </a>
                </li>
            @endif

@if ($can('rework_sr'))
            <li>
    <a href="{{ route('rework_sr') }}" class="{{ request()->routeIs('rework_sr') ? 'active' : '' }}">
        <i data-feather="rotate-ccw"></i><span>Rework SR</span>
    </a>
</li>
     @endif
            @if ($can('completed'))
                <li>
                    <a href="{{ route('completed') }}" class="{{ request()->routeIs('completed') ? 'active' : '' }}">
                        <i data-feather="award"></i><span>Completed SR</span>
                    </a>
                </li>
            @endif
        @endif

        {{-- ══════════ FINANCE ══════════ --}}
        @php
            // $showFinance = $can('quotation_desk') || $can('invoice_panel') || $can('expense_ledger');

             $showFinance = ($can('quotation_desk') || $can('invoice_panel') || $can('expense_ledger'))
        && auth()->user()?->role?->code !== 'SE';
        @endphp
        @if ($showFinance)
            <li class="sidebar-heading">Finance</li>

            @if ($can('quotation_desk'))
                <li>
                    <a href="{{ route('quotation_desk') }}" class="{{ request()->routeIs('quotation_desk') ? 'active' : '' }}">
                        <i data-feather="file-text"></i><span>Quotation Desk</span>
                    </a>
                </li>
            @endif

            @if ($can('invoice_panel'))
                <li>
                    <a href="{{ route('invoice_panel') }}" class="{{ request()->routeIs('invoice_panel') ? 'active' : '' }}">
                        <i data-feather="credit-card"></i><span>Invoice Panel</span>
                    </a>
                </li>
            @endif

            @if ($can('expense_ledger'))
                <li>
                    <a href="{{ route('expense_ledger') }}" class="{{ request()->routeIs('expense_ledger') ? 'active' : '' }}">
                        <i data-feather="dollar-sign"></i><span>Expense Ledger</span>
                    </a>
                </li>
            @endif
        @endif

       {{-- ══════════ SYSTEM ══════════ --}}
        @php
            // A user with only user_provisioning (no direct user_directory access)
            // must still see this link - provisioning lives inside the directory
            // page, so provisioning rights imply reaching the directory.
            $showSystem = $u?->canAccessUserDirectory()
                    || $can('master_data') || $can('wa_notification_log')
                    || $can('activity-log');
        @endphp
        @if ($showSystem)
            <li class="sidebar-heading">System</li>

            @if ($u?->canAccessUserDirectory())
                <li>
                    <a href="{{ route('user_directory') }}" class="{{ request()->routeIs('user_directory') ? 'active' : '' }}">
                        <i data-feather="users"></i><span>User Directory</span>
                    </a>
                </li>
            @endif

            @if ($show('master_data', ['SE']))
                <li>
                    <a href="{{ route('masters.index') }}" class="{{ request()->routeIs('masters.*') ? 'active' : '' }}">
                        <i data-feather="database"></i><span>Master Data</span>
                    </a>
                </li>
            @endif

            @if ($can('wa_notification_log'))
                <li>
                    <a href="{{ route('wa_notification_log') }}" class="{{ request()->routeIs('wa_notification_log') ? 'active' : '' }}">
                        <i data-feather="message-circle"></i><span>WhatsApp Notifications</span>
                    </a>
                </li>
            @endif

            @if ($can('activity-log'))
                <li>
                    <a href="{{ route('activity-log') }}" class="{{ request()->routeIs('activity-log') ? 'active' : '' }}">
                        <i data-feather="activity"></i><span>Activity Log</span>
                    </a>
                </li>
            @endif
        @endif

    </ul>
</aside>

<style>
/* ═══════════════════════════════════════════════
   SIDEBAR - scoped so nothing leaks into the page
═══════════════════════════════════════════════ */
.sidebar{
  --sb-brand:#9a8053;
  --sb-brand-soft:rgba(154,128,83,.12);
  --sb-brand-hover:rgba(154,128,83,.07);
  scrollbar-width:thin;
  scrollbar-color:none;
}
.sidebar::-webkit-scrollbar{width:5px;}
.sidebar::-webkit-scrollbar-track{background:transparent;}
.sidebar::-webkit-scrollbar-thumb{background:var(--border-color,#e4e8f0);border-radius:3px;}
.sidebar:hover::-webkit-scrollbar-thumb{background:rgba(154,128,83,.35);}

/* ── Brand ── */
.sidebar-brand{
  display:flex;align-items:center;gap:11px;
  text-decoration:none;
  border-bottom:1px solid var(--border-color,#e4e8f0);
}
.sidebar-brand .brand-mark{
  display:flex;align-items:center;justify-content:center;flex-shrink:0;
  border-radius:9px;
  transition:transform .25s cubic-bezier(.34,1.56,.64,1);
}
.sidebar-brand:hover .brand-mark{transform:scale(1.06) rotate(-3deg);}
.sidebar-brand .brand-text{
  font-size:.9375rem;font-weight:700;letter-spacing:.04em;
  color:var(--text-heading,#0d1626);line-height:1.2;
}
.sidebar-brand .brand-text small{
  display:block;
  font-size:.625rem;font-weight:400;letter-spacing:.01em;
  color:var(--text-muted,#7987a1);margin-top:2px;
}

/* ── Nav list ── */
.sidebar-nav{list-style:none;margin:0;padding:8px 0 24px;}

/* Section headings - fixed height so nothing reflows on load */
.sidebar-heading{
  font-size:.625rem;font-weight:700;
  text-transform:uppercase;letter-spacing:.13em;
  color:var(--text-light,#b0bac9);
  padding:16px 22px 5px;
  min-height:28px;line-height:28px;
  user-select:none;
}
.sidebar-nav li:first-child .sidebar-heading,
.sidebar-heading:first-child{padding-top:10px;}

/* ── Links ── */
.sidebar-nav a{
  position:relative;
  display:flex;align-items:center;gap:11px;
  margin:2px 10px;padding:9px 12px;
  border-radius:8px;
  font-size:.8125rem;font-weight:450;
  color:var(--nav-link,#4a5568);
  text-decoration:none;
  transition:background .16s ease,color .16s ease,transform .16s ease;
}

/* Reserve icon space BEFORE feather swaps <i> for <svg> - stops the shift */
.sidebar-nav a > i[data-feather],
.sidebar-nav a > svg{
  flex:0 0 18px;width:18px;height:18px;
  stroke-width:1.9;
  color:var(--text-muted,#7987a1);
  transition:color .16s ease;
}
.sidebar-nav a > span{
  flex:1;min-width:0;
  overflow:hidden;text-overflow:ellipsis;white-space:nowrap;
}

.sidebar-nav a:hover{
  background:var(--sb-brand-hover);
  color:var(--sb-brand);
  transform:translateX(2px);
}
.sidebar-nav a:hover > svg,
.sidebar-nav a:hover > i[data-feather]{color:var(--sb-brand);}

/* Active - tinted pill plus a flush accent bar on the rail */
.sidebar-nav a.active{
  background:var(--sb-brand-soft);
  color:var(--sb-brand);
  font-weight:600;
}
.sidebar-nav a.active > svg,
.sidebar-nav a.active > i[data-feather]{
  color:var(--sb-brand);
  stroke-width:2.2;
}
.sidebar-nav a.active::before{
  content:'';
  position:absolute;left:-10px;top:50%;
  transform:translateY(-50%);
  width:3px;height:20px;
  border-radius:0 3px 3px 0;
  background:var(--sb-brand);
}

/* Keyboard focus - visible without being loud */
.sidebar-nav a:focus-visible{
  outline:none;
  box-shadow:0 0 0 2px rgba(154,128,83,.35);
}

/* ── Count badge ── */
/* ── Inquiry Approval Pending Count ── */
.sidebar-nav a .badge-pill{
    margin-left:auto;
    flex:0 0 auto;

    min-width:24px;
    height:22px;
    padding:0 7px;

    display:inline-flex;
    align-items:center;
    justify-content:center;

    border-radius:999px;

    background:#fff1f0;
    color:#d64545;

    border:1px solid #ffd6d3;

    font-size:.65rem;
    font-weight:700;
    line-height:1;

    box-shadow:0 1px 3px rgba(0,0,0,.06);

    transition:
        background .16s ease,
        color .16s ease,
        border-color .16s ease,
        transform .16s ease;
}

/* Hover */
.sidebar-nav a:hover .badge-pill{
    background:#ffe5e2;
    color:#c93636;
    border-color:#ffc2bd;
}

/* Active Inquiry Approval */
.sidebar-nav a.active .badge-pill{
    background:#d64545;
    color:#fff;
    border-color:#d64545;
    box-shadow:0 2px 5px rgba(214,69,69,.25);
}

/* Dark mode */
[data-theme="dark"] .sidebar-nav a .badge-pill,
[data-bs-theme="dark"] .sidebar-nav a .badge-pill{
    background:rgba(214,69,69,.16);
    color:#ff8b87;
    border-color:rgba(214,69,69,.30);
}

[data-theme="dark"] .sidebar-nav a.active .badge-pill,
[data-bs-theme="dark"] .sidebar-nav a.active .badge-pill{
    background:#d64545;
    color:#fff;
    border-color:#d64545;
}

/* ── Motion preferences ── */
@media (prefers-reduced-motion:reduce){
  .sidebar-nav a,
  .sidebar-brand .brand-mark{transition:none;}
  .sidebar-nav a:hover{transform:none;}
}

/* Kill transitions until the page has settled */
.sidebar.preload,
.sidebar.preload *{transition:none !important;animation:none !important;}
</style>

<script>
(function () {
    var sidebar = document.getElementById('sidebar');
    var overlay = document.getElementById('sidebarOverlay');
    if (!sidebar) return;

    sidebar.classList.add('preload');

    // 1. Draw icons immediately so nothing shifts after paint
    if (window.feather) feather.replace();

    // 2. Restore scroll position (survives reload)
    var KEY = 'mm_sidebar_scroll';
    var saved = sessionStorage.getItem(KEY);
    if (saved) sidebar.scrollTop = parseInt(saved, 10) || 0;

    sidebar.addEventListener('scroll', function () {
        sessionStorage.setItem(KEY, sidebar.scrollTop);
    }, { passive: true });

    // 3. Instant active highlight + remember position on click
    sidebar.querySelectorAll('.sidebar-nav a').forEach(function (link) {
        link.addEventListener('click', function () {
            sidebar.querySelectorAll('.sidebar-nav a.active')
                   .forEach(function (a) { a.classList.remove('active'); });
            this.classList.add('active');
            sessionStorage.setItem(KEY, sidebar.scrollTop);
        });
    });

    // 4. Scroll the active link into view without animating
    var active = sidebar.querySelector('.sidebar-nav a.active');
    if (active && !saved) active.scrollIntoView({ block: 'nearest' });

    // 5. Mobile drawer close
    if (overlay) {
        overlay.addEventListener('click', function () {
            sidebar.classList.remove('open');
            overlay.classList.remove('show');
        });
    }

    // 6. Re-enable transitions once everything is settled
    window.addEventListener('load', function () {
        requestAnimationFrame(function () {
            sidebar.classList.remove('preload');
        });
    });
})();
</script>