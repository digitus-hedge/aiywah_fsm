<!-- Sidebar Overlay (mobile) -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

@php
/**
* Each sidebar link is gated by its permission KEY (matches permissions.key).
* A link renders only if $u->hasAccess($key) is true — role access 'yes'
* OR the key is in the user's fd_grants. 'rls' and 'no' hide the link.
* Section headings show only when at least one child link is visible.
*/
$u = auth()->user();
$can = fn ($key) => $u && $u->hasAccess($key);

// Live pending count for the Inquiry Approval badge.
$pendingCount = \App\Models\ServiceRequest::where('status', 'Pending')->count();
$srExplorer = \App\Models\ServiceRequest::count();
@endphp

<aside class="sidebar" id="sidebar">
    <a href="#" class="sidebar-brand">
        <div class="brand-mark">
            <img src="{{ asset('assets/images/logo-icon.webp') }}"
                alt="MatterMind Logo"
                style="width:35px; height:40px; object-fit:contain; padding:3px">
        </div>
        <div class="brand-text">
            MATTER MIND
            <small>Service That Matters. Always.</small>
        </div>
    </a>

    <ul class="sidebar-nav">

        {{-- ══ MAIN ══ --}}
        @php $showMain = $can('dashboard') || $can('kanban_view') || $can('sr_registration'); @endphp
        @if ($showMain)
        <li class="sidebar-heading">Main</li>
        @if ($can('dashboard'))
        <li>
            <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i data-feather="file-plus"></i>Dashboard
            </a>
        </li>
        @endif
       @if ($can('sr_registration') && auth()->user()->role?->code !== 'SE')

        <li>
            <a href="{{ route('sr_registration') }}" class="{{ request()->routeIs('sr_registration') ? 'active' : '' }}">
                <i data-feather="file-plus"></i>SR Registration
            </a>
        </li>
        @endif
         @if ($can('sr_explorer'))
        <li>
            <a href="{{ route('sr_explorer') }}" class="{{ request()->routeIs('sr_explorer') ? 'active' : '' }}" class="active"> <i data-feather="tag"></i>SR Explorer
                <!-- @if ($srExplorer > 0)
                <span class="badge-pill">{{ $srExplorer }}</span>
                @endif -->
            </a>
        </li>
        @endif
        @if ($can('kanban_view'))
        <li>
            <a href="{{ route('kanban_view') }}" class="{{ request()->routeIs('kanban_view') ? 'active' : '' }}">
                <i data-feather="clipboard"></i>Ticket Summary
            </a>
        </li>
        @endif
        @endif

        {{-- ══ Client management ══ --}}
        @php $showClientManagement = $can('client_accounts') || $can('client_directory'); @endphp

        

        @if ($showClientManagement)
        <li class="sidebar-heading">Customer </li>
        @if ($can('client_accounts') && auth()->user()?->role?->code !== 'SE')

        <li>
            <a href="{{ route('clients.create') }}" class="{{ request()->routeIs('clients.create', 'clients.edit') ? 'active' : '' }}">
                <i data-feather="users"></i>Customer Accounts
            </a>
        </li>
        @endif

        @if ($can('client_directory'))
        <li>
            <a href="{{ route('clients.directory') }}" class="{{ request()->routeIs('clients.directory') ? 'active' : '' }}">
                <i data-feather="book-open"></i>Customer Directory
            </a>
        </li>
        @endif
        
        <li>
            <a href="{{ route('project_site_directory') }}" class="{{ request()->routeIs('project_site_directory') ? 'active' : '' }}" class="active">
                 <i data-feather="briefcase"></i>Projects and Site
                
            </a>
        </li>
        

        

        @endif

        {{-- ══ WORKFLOW ══ --}}
        @php $showWorkflow = $can('inquiry_approval') || $can('dispatch_engine') || $can('assigned') ||$can('qc_review') || $can('completed'); @endphp
        @if ($showWorkflow)
        <li class="sidebar-heading">Workflow</li>
        @if ($can('inquiry_approval') && !in_array(auth()->user()?->role?->code, ['SE', 'HP']))
        <li>
            <a href="{{ route('inquiry-approval.index') }}"
                class="{{ request()->routeIs('inquiry-approval.index') ? 'active' : '' }}">
                <i data-feather="clipboard"></i> Inquiry Approval
                @if ($pendingCount > 0)
                <span class="badge-pill">{{ $pendingCount }}</span>
                @endif
            </a>
        </li>
        @endif

        @if ($can('dispatch_engine'))
        <li>
            <a href="{{ route('dispatch_engine') }}" class="{{ request()->routeIs('dispatch_engine') ? 'active' : '' }}">
                <i data-feather="user-check"></i>Dispatch Engine
            </a>
        </li>
        @endif

        @if ($can('assigned'))
        <li>
            <a href="{{ route('assigned') }}" class="{{ request()->routeIs('assigned') ? 'active' : '' }}">
                <i data-feather="user-check"></i>Approved SR
            </a>
        </li>
        @endif
        @if ($can('qc_review'))
        <li>
            <a href="{{ route('qc_review') }}" class="{{ request()->routeIs('qc_review') ? 'active' : '' }}">
                <i data-feather="check-circle"></i>QC Review
            </a>
        </li>
        @endif
         @if ($can('completed'))
        <li>
            <a href="{{ route('completed') }}" class="{{ request()->routeIs('completed') ? 'active' : '' }}">
                <i data-feather="check-circle"></i>Completed SR
            </a>
        </li>
        @endif
        @endif

        {{-- ══ FINANCE ══ --}}
        @php $showFinance = $can('quotation_desk') || $can('invoice_panel') || $can('expense_ledger'); @endphp
        @if ($showFinance)
        <li class="sidebar-heading">Finance</li>
        @if ($can('quotation_desk'))
        <li>
            <a href="{{ route('quotation_desk') }}" class="{{ request()->routeIs('quotation_desk') ? 'active' : '' }}">
                <i data-feather="file-text"></i>Quotation Desk
            </a>
        </li>
        @endif
        @if ($can('invoice_panel'))
        <li>
            <a href="{{ route('invoice_panel') }}" class="{{ request()->routeIs('invoice_panel') ? 'active' : '' }}">
                <i data-feather="file"></i>Invoice Panel
            </a>
        </li>
        @endif
        @if ($can('expense_ledger'))
        <li>
            <a href="{{ route('expense_ledger') }}" class="{{ request()->routeIs('expense_ledger') ? 'active' : '' }}">
                <i data-feather="check-square"></i>Expense Ledger
            </a>
        </li>
        @endif
        @endif

        {{-- ══ SYSTEM ══ --}}
        @php $showSystem = $can('analytics') || $can('user_provisioning') || $can('master_data') || $can('activity_logs'); @endphp
        @if ($showSystem)
        <li class="sidebar-heading">System</li>
        @if ($can('analytics'))
        <!-- <li>
            <a href="{{ route('analytics') }}" class="{{ request()->routeIs('analytics') ? 'active' : '' }}">
                <i data-feather="bar-chart-2"></i>Analytics
            </a>
        </li> -->
        @endif
        @if ($can('user_directory'))
        <li>
            <a href="{{ route('user_directory') }}" class="{{ request()->routeIs('user_directory') ? 'active' : '' }}">
                <i data-feather="user"></i>User Directory
            </a>
        </li>
        @endif
@if ($can('client_accounts') && !in_array(auth()->user()?->role?->code, ['SE', 'HP']))
        <li>
            <a href="{{ route('user_provisioning') }}" class="{{ request()->routeIs('user_provisioning') ? 'active' : '' }}">
                <i data-feather="shield"></i>User Provisioning
            </a>
        </li>
        @endif
        @if ($can('master_data') && !in_array(auth()->user()?->role?->code, ['SE', 'HOP']))
        <li>
            <a href="{{ route('masters.index') }}" class="{{ request()->routeIs('masters.*') ? 'active' : '' }}">
                <i data-feather="database"></i>Master Data
            </a>
        </li>
        @endif
         @if ($can('wa_notification_log'))
        <li>
            <a href="{{ route('wa_notification_log') }}" class="{{ request()->routeIs('wa_notification_log') ? 'active' : '' }}">
                <i data-feather="settings"></i>WhatsApp Notifications
            </a>
        </li>
        @endif
       @if ($can('activity_log'))
        <li>
            <a href="{{ route('activity-log') }}" class="{{ request()->routeIs('activity-log') ? 'active' : '' }}">
                <i data-feather="activity"></i>Activity Log
            </a>
        </li>
        @endif
        @endif

    </ul>
</aside>