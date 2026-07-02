<header class="topbar" id="topbar">

    {{-- ── Left: hamburger · page title · breadcrumb ── --}}
    <div class="topbar-left">

        {{-- Hamburger — id="sidebarToggle" is wired in layout.blade.php --}}
        <button class="topbar-toggle" id="sidebarToggle"
                type="button" aria-label="Toggle sidebar">
            <i class="bi bi-list"></i>
        </button>

        <div class="topbar-page-info">
            <div class="topbar-page-title">
                <i class="bi bi-@yield('page_icon', 'grid-1x2') topbar-page-icon"></i>
                <strong>@yield('page_title', 'Dashboard')</strong>
            </div>
            <nav class="topbar-breadcrumb" aria-label="breadcrumb">
                <a href="{{ route('dashboard') }}">Home</a>
                @hasSection('page_title')
                    <span class="bc-sep">/</span>
                    <span class="bc-current">@yield('page_title')</span>
                @endif
            </nav>
        </div>

    </div>

    {{-- ── Right: role badge · clock · theme toggle · avatar ── --}}
    <div class="topbar-right">

        @php
    $user       = Auth::user();
        $userName   = $user->name ?? 'Guest';
        $roleModel  = $user ? $user->role : null;
        $userRole   = optional($roleModel)->name  ?? 'Guest';
        $roleIcon   = optional($roleModel)->icon  ?? 'bi-shield-check';
        $roleColor  = optional($roleModel)->color ?? null;
        $userAvatar = strtoupper(substr(str_replace(' ', '', $userName), 0, 2));
    @endphp

        {{-- Role badge --}}
       <div class="topbar-role-badge" @if($roleColor) style="color:{{ $roleColor }};" @endif>
            <i class="bi {{ $roleIcon }} role-icon"></i>
            <span>{{ $userRole }}</span>
        </div>

        {{-- Live clock — filled by layout.blade.php tickClock() --}}
        <span class="topbar-clock" id="topbarClock"></span>

        {{-- Theme toggle: sun · track · moon
             Click handled by layout.blade.php DOMContentLoaded --}}
        <div class="topbar-theme-wrap" id="themeToggleBtn"
             role="button" tabindex="0"
             title="Toggle light / dark mode"
             aria-label="Toggle theme">
            <i class="bi bi-sun-fill th-icon th-sun"></i>
            <div class="th-track"><div class="th-thumb"></div></div>
            <i class="bi bi-moon-stars-fill th-icon th-moon"></i>
        </div>

        {{-- Avatar --}}
       <div class="topbar-avatar-wrap dropdown">
    <button class="topbar-avatar" type="button"
            id="avatarMenuBtn" title="{{ $userName }}"
            data-bs-toggle="dropdown" aria-expanded="false"
            style="border:none;cursor:pointer;">
        {{ $userAvatar }}
    </button>

    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="avatarMenuBtn">
        <li class="px-3 py-2">
            <div style="font-weight:600;font-size:.85rem;">{{ $userName }}</div>
            <div style="font-size:.72rem;color:var(--text-muted,#888);">{{ $userRole }}</div>
        </li>
        <li><hr class="dropdown-divider"></li>
        <li>
            <a class="dropdown-item" href='#' }}">
                <i class="bi bi-person me-2"></i>Profile
            </a>
        </li>
        <li>
            <form method="POST" action="{{ route('logout') }}" class="m-0">
                @csrf
                <button type="submit" class="dropdown-item text-danger">
                    <i class="bi bi-box-arrow-right me-2"></i>Logout
                </button>
            </form>
        </li>
    </ul>
</div>

    </div>
</header>