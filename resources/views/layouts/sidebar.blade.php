<!-- Sidebar Overlay (mobile) -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<aside class="sidebar" id="sidebar">
    <a href="{{ route('dashboard') }}" class="sidebar-brand">
        <div class="brand-mark">
    <img src="{{ asset('assets/images/logo-mattermind-icon-only.png') }}"
         alt="MatterMind Logo"
         style="width:40px; height:40px; object-fit:contain;">
</div>
        <div class="brand-text">
            MATTER MIND
            <small>Perfection Is A State Of Mind</small>
        </div>
    </a>

    <ul class="sidebar-nav">
        <li class="sidebar-heading">Main</li>
        <li>
            <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i data-feather="layout"></i>Dashboard
            </a>
        </li>
        <li>
            <a href="{{ route('kanban_view') }}" class="{{ request()->routeIs('kanban_view') ? 'active' : '' }}">
                <i data-feather="clipboard"></i>Ticket Summary(Kanban)
            </a>
        </li>
        <li>
            <a href="{{ route('sr_registration') }}" class="{{ request()->routeIs('sr_registration') ? 'active' : '' }}">
                <i data-feather="file-plus"></i>SR Registration
            </a>
        </li>

        <li class="sidebar-heading">Workflow</li>
        <li>
            <a href="{{ route('inquiry-approval.index') }}"
            class="{{ request()->routeIs('inquiry-approval.index') ? 'active' : '' }}">
                <i data-feather="clipboard"></i> Inquiry Approval
                <span class="badge-pill">12</span>
            </a>
        </li>
        <li>
            <a href="{{ route('clients.create') }}" class="{{ request()->routeIs('clients.*') ? 'active' : '' }}">
                <i data-feather="users"></i>Client Accounts
            </a>
        </li>
        <li>
            <a href="{{ route('dispatch_engine') }}" class="{{ request()->routeIs('dispatch_engine') ? 'active' : '' }}">
                <i data-feather="user-check"></i>Dispatch Engine
            </a>
        </li>
        <li>
            <a href="#" class="{{ request()->routeIs('quality_check') ? 'active' : '' }}">
                <i data-feather="check-circle"></i>QC Review
            </a>
        </li>

        <li class="sidebar-heading">Finance</li>
        <li>
            <a href="#" class="{{ request()->routeIs('quotation_desk') ? 'active' : '' }}">
                <i data-feather="file-text"></i>Quotation Desk
            </a>
        </li>
        <li>
            <a href="#" class="{{ request()->routeIs('invoice_panel') ? 'active' : '' }}">
                <i data-feather="file"></i>Invoice Panel
            </a>
        </li>
        <li>
            <a href="#" class="{{ request()->routeIs('expense_ledger') ? 'active' : '' }}">
                <i data-feather="check-square"></i>Expense Ledger
            </a>
        </li>

        <li class="sidebar-heading">System</li>
        <li>
            <a href="#" class="{{ request()->routeIs('analytics') ? 'active' : '' }}">
                <i data-feather="bar-chart-2"></i>Analytics
            </a>
        </li>
        <li>
            <a href="#" class="{{ request()->routeIs('user_provisioning') ? 'active' : '' }}">
                <i data-feather="shield"></i>User Provisioning
            </a>
        </li>
        <li>
            <a href="#" class="{{ request()->routeIs('system_config') ? 'active' : '' }}">
                <i data-feather="settings"></i>System Config
            </a>
        </li>
    </ul>
</aside>