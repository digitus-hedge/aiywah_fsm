<!DOCTYPE html>
<html lang="en" data-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">    
    <title>@yield('title', 'MATTER MIND')</title>

    <link rel="icon" type="image/png" href="{{ asset('assets/images/favicon.png') }}">
    
    {{-- ① theme.js FIRST - sets data-theme before any paint, prevents flash --}}
    <script src="{{ asset('assets/js/theme.js') }}"></script>

    {{-- Fonts (self-hosted) --}}
    <link rel="stylesheet" href="{{ asset('assets/fonts/fonts.css') }}">

    {{-- Google Fonts Roboto - can now remove, or keep as a fallback --}}
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:opsz,wght@9..40,400;9..40,500;9..40,700&display=swap" rel="stylesheet">
    
    {{-- Bootstrap 5 CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- Bootstrap Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    {{-- Feather Icons (JS - render after DOM) --}}
    <script src="https://cdn.jsdelivr.net/npm/feather-icons@4.29.1/dist/feather.min.js"></script>

    {{-- ApexCharts --}}
    <script src="https://cdn.jsdelivr.net/npm/apexcharts@3.45.1/dist/apexcharts.min.js"></script>

    {{-- ② Global theme CSS --}}
    <link rel="stylesheet" href="{{ asset('assets/css/theme.css') }}">

    {{-- ③ Navbar CSS --}}
    <link rel="stylesheet" href="{{ asset('assets/css/navbar.css') }}">

    {{-- ④ App CSS --}}
    <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}">

    {{-- ⑤ Per-page styles --}}
    @stack('styles')
    
</head>

<body>

    {{-- Sidebar overlay --}}
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    {{-- Sidebar --}}
    @include('layouts.sidebar')

    {{-- Navbar --}}
    @include('layouts.navbar')

    {{-- Main content --}}
    <main class="main">
        <div class="page-content">
            @yield('content')
        </div>
        @include('layouts.footer')
    </main>

    {{-- Bootstrap JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
    document.addEventListener('DOMContentLoaded', function () {

        /* ── 1. Feather icons ── */
        if (typeof feather !== 'undefined') feather.replace();

        /* ── 2. Re-render feather on theme change ── */
        document.addEventListener('themechange', function () {
            if (typeof feather !== 'undefined') feather.replace();
        });

        /* ── 3. Sidebar toggle (mobile hamburger) ──────────────────
           Looks for:
             #sidebarToggle  - the hamburger button in navbar.blade.php
             #sidebar        - the <aside> in sidebar.blade.php
             #sidebarOverlay - the backdrop div above
           Adds/removes .show class on sidebar and overlay.
        ─────────────────────────────────────────────────────────── */
        var sidebar   = document.getElementById('sidebar');
        var overlay   = document.getElementById('sidebarOverlay');
        var hamburger = document.getElementById('sidebarToggle');

        function openSidebar() {
            if (sidebar)  sidebar.classList.add('show');
            if (overlay)  overlay.classList.add('show');
            document.body.classList.add('sidebar-open');
            document.body.style.overflow = 'hidden';
        }

        function closeSidebar() {
            if (sidebar)  sidebar.classList.remove('show');
            if (overlay)  overlay.classList.remove('show');
            document.body.classList.remove('sidebar-open');
            document.body.style.overflow = '';
        }

        if (hamburger) {
            hamburger.addEventListener('click', function (e) {
                e.stopPropagation();
                var isOpen = sidebar && sidebar.classList.contains('show');
                isOpen ? closeSidebar() : openSidebar();
            });
        }

        /* Close when overlay is clicked */
        if (overlay) overlay.addEventListener('click', closeSidebar);

        /* Close when a sidebar nav link is clicked (mobile) */
        document.querySelectorAll('#sidebar .sidebar-nav a').forEach(function (link) {
            link.addEventListener('click', function () {
                if (window.innerWidth < 992) closeSidebar();
            });
        });

        /* ── 4. Live clock ──────────────────────────────────────────
           getElementById runs after DOM is ready, so topbarClock
           is guaranteed to exist at this point.
        ─────────────────────────────────────────────────────────── */
        function tickClock() {
            var el = document.getElementById('topbarClock');
            if (!el) return;
            el.textContent = new Date().toLocaleTimeString('en-IN', {
                hour:   '2-digit',
                minute: '2-digit',
                second: '2-digit'
            });
            setTimeout(tickClock, 1000);
        }
        tickClock();

       

    }); /* end DOMContentLoaded */
    </script>

    {{-- Per-page scripts --}}
    @stack('scripts')

</body>
</html>