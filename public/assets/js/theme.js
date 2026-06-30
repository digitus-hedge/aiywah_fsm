/**
 * DIGIT-US PORTAL  ·  GLOBAL THEME MANAGER
 * public/assets/js/theme.js
 *
 * Load as the VERY FIRST <script> inside <head> — before any CSS link.
 * This prevents the white flash on dark-mode page loads.
 *
 *   <script src="{{ asset('assets/js/theme.js') }}"></script>
 *
 * The toggle button anywhere in the page just needs id="themeToggleBtn".
 * Clicking it calls window.ThemeManager.toggle() automatically.
 */

(function () {
    'use strict';

    var KEY   = 'du_theme';
    var ROOT  = document.documentElement;

    /* ── 1. Apply saved theme IMMEDIATELY (before paint) ── */
    var saved      = localStorage.getItem(KEY);
    var osDark     = window.matchMedia && window.matchMedia('(prefers-color-scheme:dark)').matches;
    var startDark  = saved ? saved === 'dark' : osDark;

    ROOT.setAttribute('data-theme', startDark ? 'dark' : 'light');

    /* ── 2. Public API ── */
    window.ThemeManager = {

        current: function () {
            return ROOT.getAttribute('data-theme') || 'light';
        },

        isDark: function () {
            return this.current() === 'dark';
        },

        set: function (theme) {
            ROOT.setAttribute('data-theme', theme);
            localStorage.setItem(KEY, theme);
            this._broadcast(theme);
        },

        toggle: function () {
            var next = this.isDark() ? 'light' : 'dark';
            this.set(next);
            return next;
        },

        _broadcast: function (theme) {
            /* 1. Custom event — any page JS can listen */
            try {
                document.dispatchEvent(new CustomEvent('themechange', {
                    detail: { theme: theme, isDark: theme === 'dark' }
                }));
            } catch (e) {}

            /* 2. Re-render Feather icons (they use currentColor / stroke) */
            if (typeof feather !== 'undefined') {
                try { feather.replace(); } catch (e) {}
            }

            /* 3. Update any ApexCharts on the page */
            _syncApexCharts(theme === 'dark');
        }
    };

    /* ── 3. Wire toggle button after DOM is ready ── */
    function wireBtn() {
        var btn = document.getElementById('themeToggleBtn');
        if (!btn || btn.__themeBound) return;
        btn.__themeBound = true;
        btn.addEventListener('click', function () {
            window.ThemeManager.toggle();
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', wireBtn);
    } else {
        wireBtn();          /* script loaded after DOM — wire immediately */
    }

    /* ── 4. Re-wire after every Livewire / Turbo navigation ── */
    document.addEventListener('DOMContentLoaded', wireBtn);

    /* ── 5. Follow OS preference changes (only when no saved pref) ── */
    if (window.matchMedia) {
        window.matchMedia('(prefers-color-scheme:dark)')
              .addEventListener('change', function (e) {
                  if (!localStorage.getItem(KEY)) {
                      window.ThemeManager.set(e.matches ? 'dark' : 'light');
                  }
              });
    }

    /* ── 6. ApexCharts dark/light sync helper ── */
    function _syncApexCharts(dark) {
        if (typeof ApexCharts === 'undefined') return;
        var opts = {
            chart:   { foreColor: dark ? '#c8d4e8' : '#7987a1' },
            grid:    { borderColor: dark ? '#16243d' : '#e4e8f0' },
            tooltip: { theme: dark ? 'dark' : 'light' },
            xaxis: { labels: { style: { colors: dark ? '#6b7fa0' : '#7987a1' } } },
            yaxis: { labels: { style: { colors: dark ? '#6b7fa0' : '#7987a1' } } },
        };
        try {
            document.querySelectorAll('[data-apexcharts-id]').forEach(function (el) {
                var id = el.getAttribute('data-apexcharts-id');
                if (id) ApexCharts.exec(id, 'updateOptions', opts, false, false);
            });
        } catch (e) {}
    }

})();