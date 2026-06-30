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
            $userRole   = Auth::check() ? (Auth::user()->role ?? 'Front Desk') : 'Guest';
            $userName   = Auth::check() ? (Auth::user()->name ?? 'User')       : 'Guest';
            $userAvatar = strtoupper(substr(str_replace(' ', '', $userName), 0, 2));
        @endphp

        {{-- Role badge --}}
        <div class="topbar-role-badge">
            <i class="bi bi-shield-check role-icon"></i>
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
        <div class="topbar-avatar" title="{{ $userName }}">{{ $userAvatar }}</div>

    </div>
</header>