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
                <a href="">Home</a>
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
            @php
            $isHopSe = optional($roleModel)->code === 'HP' && (bool) ($user->is_se_enabled ?? false);
        @endphp

                @if ($isHopSe)
        <div class="dash-switch">
            <a href="{{ route('dashboard') }}"
               class="dash-switch-btn {{ request()->routeIs('dashboard') ? 'active' : '' }}"
               title="Head of Projects view">
                <i class="bi bi-briefcase-fill"></i>
                <span>HoP</span>
            </a>
            <a href="{{ route('sedashboard') }}"
               class="dash-switch-btn {{ request()->routeIs('sedashboard') ? 'active' : '' }}"
               title="Service Engineer view">
                <i class="bi bi-wrench-adjustable"></i>
                <span>SE</span>
            </a>
        </div>
        @endif

        {{-- Role badge --}}
       <div class="topbar-role-badge" @if($roleColor) style="color:{{ $roleColor }};" @endif>
            <i class="bi {{ $roleIcon }} role-icon"></i>
            <span>{{ $userRole }}</span>
        </div>


        <!-- Notifcations List -->
        <div class="notif-wrap" style="position:relative;">
  <button id="notifBell" onclick="toggleNotif()" style="background:none;border:none;position:relative;cursor:pointer;padding:8px;">
    <i class="bi bi-bell-fill" style="font-size:1.15rem;color:var(--text-heading);"></i>
    <span id="notifBadge" style="display:none;position:absolute;top:2px;right:2px;min-width:16px;height:16px;padding:0 4px;background:#ff3366;color:#fff;font-size:.6rem;font-weight:700;border-radius:8px;align-items:center;justify-content:center;"></span>
  </button>

  <div id="notifPanel" style="display:none;position:absolute;right:0;top:100%;width:340px;max-height:420px;overflow-y:auto;background:#fff;border:1px solid #eee;border-radius:12px;box-shadow:0 8px 28px rgba(0,0,0,.12);z-index:1000;">
    <div style="display:flex;justify-content:space-between;align-items:center;padding:12px 14px;border-bottom:1px solid #f0f0f0;position:sticky;top:0;background:#fff;">
      <strong style="font-size:.85rem;">Notifications</strong>
      <a href="#" onclick="markAllRead();return false;" style="font-size:.72rem;color:#6571ff;text-decoration:none;">Mark all read</a>
    </div>
    <div id="notifList"></div>
    <a href="/notifications/all" style="display:block;text-align:center;padding:11px;font-size:.76rem;font-weight:600;color:#6571ff;text-decoration:none;border-top:1px solid #f0f0f0;position:sticky;bottom:0;background:#fff;">
    View all notifications
  </a>

  </div>
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


<style>
/* ---------- desktop: dropdown anchored to bell ---------- */
#notifPanel {
  position: absolute !important;
  right: 0 !important;
  left: auto !important;
  top: calc(100% + 6px) !important;
  width: 340px !important;
  max-width: calc(100vw - 24px) !important;
  max-height: 420px !important;
  overflow-y: auto !important;
  background: #fff !important;
  border: 1px solid #eee !important;
  border-radius: 12px !important;
  box-shadow: 0 8px 28px rgba(0,0,0,.12) !important;
  z-index: 1050 !important;
  overscroll-behavior: contain;
  -webkit-overflow-scrolling: touch;
}

/* ---------- mobile: centred modal ---------- */
#notifPanel.centered {
  position: fixed !important;
  top: 50% !important;
  left: 50% !important;
  right: auto !important;
  bottom: auto !important;
  transform: translate(-50%, -50%) !important;
  width: calc(100vw - 32px) !important;
  max-width: 420px !important;
  max-height: 80vh !important;
  max-height: 80dvh !important;
  border-radius: 16px !important;
  box-shadow: 0 12px 40px rgba(0,0,0,.22) !important;
}

@media (max-width: 576px) {
  #notifPanel {
    position: fixed !important;
    top: 50% !important;
    left: 50% !important;
    right: auto !important;
    bottom: auto !important;
    transform: translate(-50%, -50%) !important;
    width: calc(100vw - 32px) !important;
    max-width: 420px !important;
    max-height: 80dvh !important;
    border-radius: 16px !important;
  }
}

/* backdrop behind the centred modal */
#notifBackdrop {
  display: none;
  position: fixed; inset: 0;
  background: rgba(0,0,0,.4);
  z-index: 1040;
}
#notifBackdrop.show { display: block; }

body.notif-locked { overflow: hidden !important; }

/* sticky head/foot keep working inside the modal */
#notifPanel .notif-head,
#notifPanel > div:first-child {
  position: sticky !important; top: 0 !important;
  background: #fff !important; z-index: 2;
  border-radius: 16px 16px 0 0;
}
#notifPanel > a[href="/notifications/all"] {
  position: sticky !important; bottom: 0 !important;
  background: #fff !important;
  border-radius: 0 0 16px 16px;
}

/* stop long strings widening the panel */
#notifList, #notifList * {
  min-width: 0 !important;
  overflow-wrap: anywhere !important;
  word-break: break-word !important;
  max-width: 100%;
}
</style>

<script>
const NOTIF_ICON = { sr_created: 'bi-plus-circle-fill', status_updated: 'bi-arrow-repeat' };
const NOTIF_CLR  = { sr_created: '#05a34a', status_updated: '#6571ff' };

const STATUS_MAP = {
  'Pending':['#fff4e0','#b7791f'], 'Approved':['#e6f6ec','#05a34a'], 'Forwarded':['#e7f0fb','#2563c9'],
  'Rejected':['#fdeaea','#d83a3a'], 'Assigned':['#e9ebff','#6571ff'], 'Quoted':['#eef3e6','#5c8a1a'],
  'In Progress':['#e0f2f1','#0d8f7e'], 'Quote Rejected':['#fdeaea','#d83a3a'], 'Qc Review':['#f3ebfb','#8b46d4'],
  'Rework':['#fdeee0','#c76a12'], 'Reschedule':['#fef6e0','#b7791f'], 'Accepted':['#e6f6ec','#05a34a'],
  'Pending Invoice':['#fff4e0','#b7791f'], 'Invoice Submitted':['#e7f0fb','#2563c9'],
  'Completed':['#e6f6ec','#05a34a'], 'On Hold':['#fdeaea','#d83a3a'],
};

function statusChip(s) {
  const [bg, clr] = STATUS_MAP[s] || ['#f1f1f1', '#666'];
  return `<span style="font-size:.6rem;padding:2px 7px;border-radius:9px;background:${bg};color:${clr};white-space:nowrap;">${s}</span>`;
}

function loadNotif() {
  fetch('/notifications', {
    headers: {
      'Accept': 'application/json',
    },
  })
    .then(r => r.json())
    .then(d => {
      const badge = document.getElementById('notifBadge');
      if (d.unread > 0) { badge.style.display = 'flex'; badge.textContent = d.unread > 99 ? '99+' : d.unread; }
      else badge.style.display = 'none';

      const list = document.getElementById('notifList');
      list.innerHTML = d.logs.length ? d.logs.map(n => {
        let chips = '';
        if (n.to) {   // show a chip whenever a to_status exists (creation OR status change)
          chips = `<div style="display:flex;align-items:center;gap:5px;margin-top:5px;flex-wrap:wrap;">
            ${n.from ? statusChip(n.from) + '<i class="bi bi-arrow-right" style="font-size:.62rem;color:#bbb;"></i>' : ''}
            ${statusChip(n.to)}
          </div>`;
        }
        return `
        <div style="display:flex;gap:10px;padding:11px 14px;border-bottom:1px solid #d9d7d3;background:${n.read ? '#fff' : '#f7f8ff'};">
          <i class="bi ${NOTIF_ICON[n.event] || 'bi-bell'}" style="color:${NOTIF_CLR[n.event] || '#888'};font-size:1rem;flex:0 0 auto;margin-top:2px;"></i>
          <div style="min-width:0;flex:1;">
            <div style="font-size:.78rem;font-weight:600;color:#6e7177;">${n.title}</div>
            <div style="font-size:.72rem;color:var(--text-muted);">${n.message}</div>
            ${chips}
            <div style="font-size:.62rem;color:#aaa;margin-top:3px;">${n.by ? n.by + ' · ' : ''}${n.ago}</div>
          </div>
        </div>`;
      }).join('')
        : '<div style="padding:24px;text-align:center;color:#aaa;font-size:.78rem;">No notifications</div>';
    });
}


function toggleNotif(force) {
  const p = document.getElementById('notifPanel');
  const b = document.getElementById('notifBackdrop');
  const open = (force !== undefined) ? force : !p.classList.contains('show');
  const small = Math.min(window.innerWidth, document.documentElement.clientWidth) <= 576;

  p.removeAttribute('style');
  p.classList.toggle('centered', small);
  p.style.display = open ? 'block' : 'none';

  if (b) b.classList.toggle('show', open && small);
  document.body.classList.toggle('notif-locked', open && small);
  if (open) loadNotif();
}

function markAllRead() {
  fetch('/notifications/read', {
    method: 'POST',
    headers: {
      'Accept': 'application/json',
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
      'Content-Type': 'application/json',
    },
  }).then(() => loadNotif());
}

document.addEventListener('click', e => {
  if (!e.target.closest('.notif-wrap')) {
    const p = document.getElementById('notifPanel');
    if (p) p.style.display = 'none';
  }
});

loadNotif();
setInterval(loadNotif, 30000);
</script>