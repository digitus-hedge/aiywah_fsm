{{--
  Shared chrome for the worker area. Both worker.dashboard and worker.pipeline
  extend this, so the header, theme toggle, account menu, toast, lightbox,
  drawer plumbing and the responsive shell are defined exactly once.

  Sections a page can fill:
    @section('title')     - browser title
    @section('live')      - optional strip under the header (omit it entirely and
                            the strip is not rendered)
    @section('tabs')      - the .tab-btn row
    @section('content')   - .tab-pane blocks
    @section('drawers')   - bottom sheets; these sit outside .tab-content so an
                            inactive pane can't hide them
    @push('styles')       - page-only CSS
    @push('scripts')      - page-only JS (globals from this file are in scope)

  JS this file exposes to pages:
    $(id) esc(v) showToast(type,title,body) busy(btn,label)
    apiPost(url,payload,isForm) apiGet(url)
    switchTab(id) onTabShow(id,fn) openDrawer(name) closeDrawer(name)
    openLightbox(type,url,name) closeLightbox() animateBars()
    window.beforeSignOut - set it to a function returning false to block sign-out
--}}
<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=no,viewport-fit=cover"/>
  <title>@yield('title', 'Field Console') | Aiywah FSM</title>
  <link rel="icon" type="image/png" href="{{ asset('assets/images/favicon.png') }}">
  <meta name="csrf-token" content="{{ csrf_token() }}"/>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;0,700;1,400&display=swap" rel="stylesheet"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet"/>
  @stack('head')
  
  <style>
/* ══════════════════════════════════════════════════════ SF PRO ═══ */
@font-face{font-family:'SF Pro Display';font-weight:400;font-style:normal;font-display:swap;
  src:url("{{ asset('assets/fonts/SFProDisplay-Regular.otf') }}") format('opentype');}
@font-face{font-family:'SF Pro Display';font-weight:500;font-style:normal;font-display:swap;
  src:url("{{ asset('assets/fonts/SFProDisplay-Medium.otf') }}") format('opentype');}
@font-face{font-family:'SF Pro Display';font-weight:700;font-style:normal;font-display:swap;
  src:url("{{ asset('assets/fonts/SFProDisplay-Bold.otf') }}") format('opentype');}

/* ══════════════════════════════════════════════════════ TOKENS ═══ */
:root,[data-bs-theme="light"]{
  --bg:#f4f2ef; --card:#fff; --card2:#faf9f7; --surface:#f0ece6;
  --border:rgba(0,0,0,.07); --border2:rgba(0,0,0,.12);
  --shadow:0 2px 16px rgba(0,0,0,.07); --shadow-md:0 4px 24px rgba(0,0,0,.1);
  --text:#1a1614; --muted:#8a8480; --light:#c0bcb8;
  --gold:#9a8053; --gold2:#b8975e; --gold-bg:rgba(154,128,83,.1); --gold-border:rgba(154,128,83,.25);
  --tab-bg:#f0ece6; --header-bg:#fff; --overlay:rgba(26,22,20,.6);
  --green:#15803d; --green-bg:rgba(21,128,61,.1);
  --amber:#d97706; --amber-bg:rgba(217,119,6,.1);
  --red:#dc2626;   --red-bg:rgba(220,38,38,.1);
  --blue:#2563eb;  --blue-bg:rgba(37,99,235,.1);
  --purple:#7c3aed;--purple-bg:rgba(124,58,237,.1);
}
[data-bs-theme="dark"]{
  --bg:#141210; --card:#1e1b18; --card2:#252220; --surface:#2a2724;
  --border:rgba(255,255,255,.07); --border2:rgba(255,255,255,.12);
  --shadow:0 2px 20px rgba(0,0,0,.4); --shadow-md:0 4px 28px rgba(0,0,0,.5);
  --text:#e8e0d4; --muted:#7a756e; --light:#4a4540;
  --gold:#9a8053; --gold2:#b8975e; --gold-bg:rgba(154,128,83,.12); --gold-border:rgba(154,128,83,.3);
  --tab-bg:#1a1714; --header-bg:#1a1714; --overlay:rgba(0,0,0,.78);
  --green:#16a34a; --green-bg:rgba(22,163,74,.1);
  --amber:#d97706; --amber-bg:rgba(217,119,6,.1);
  --red:#ef4444;   --red-bg:rgba(239,68,68,.1);
  --blue:#3b82f6;  --blue-bg:rgba(59,130,246,.1);
  --purple:#8b5cf6;--purple-bg:rgba(139,92,246,.1);
}

*,*::before,*::after{box-sizing:border-box;-webkit-tap-highlight-color:transparent;}
html,body{height:100%;overflow:hidden;}
body{font-family:'SF Pro Display','Inter',system-ui,sans-serif;font-size:.9rem;
  background:var(--bg);color:var(--text);margin:0;-webkit-font-smoothing:antialiased;}
.cg{font-family:'Cormorant Garamond',Georgia,serif;letter-spacing:-.01em;}
a{color:inherit;}
.hidden{display:none!important;}

/* ══════════════════════════════════════════════════════ SHELL ═══
   One column on a phone, capped and centred on anything wider. The
   only thing that scrolls is .tab-content, so the header and tabs
   stay put on both pages.
══════════════════════════════════════════════════════════════════ */
.app{display:flex;flex-direction:column;height:100vh;height:100dvh;overflow:hidden;}

.header{background:var(--header-bg);border-bottom:1px solid var(--border);
  height:60px;display:flex;align-items:center;justify-content:space-between;
  gap:12px;flex-shrink:0;box-shadow:0 1px 0 var(--border);
  padding:0 max(14px,env(safe-area-inset-left)) 0 max(14px,env(safe-area-inset-right));}
.hdr-inner{width:100%;max-width:920px;margin:0 auto;display:flex;
  align-items:center;justify-content:space-between;gap:12px;}
.hdr-left{display:flex;align-items:center;gap:10px;min-width:0;}
.hdr-icon{width:34px;height:34px;background:linear-gradient(135deg,#9a8053,#b8975e);
  border-radius:9px;display:flex;align-items:center;justify-content:center;color:#fff;
  font-size:.78rem;font-weight:700;font-family:'Cormorant Garamond',Georgia,serif;flex-shrink:0;}
.hdr-text{min-width:0;}
.hdr-title{font-family:'Cormorant Garamond',Georgia,serif;font-size:1rem;font-weight:700;
  color:var(--text);line-height:1.1;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
.hdr-sub{font-size:.65rem;color:var(--muted);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
.hdr-right{display:flex;align-items:center;gap:8px;flex-shrink:0;}
.clk{font-size:.7rem;color:var(--muted);font-variant-numeric:tabular-nums;display:none;}
@media(min-width:430px){.clk{display:block;}}

.th-toggle{display:flex;align-items:center;gap:6px;cursor:pointer;user-select:none;
  background:none;border:none;padding:0;}
.th-sun{color:#fbbc06;font-size:.75rem;}
.th-moon{color:var(--gold2);font-size:.75rem;}
.tt-track{width:38px;height:20px;background:var(--tab-bg);border-radius:10px;
  position:relative;border:1px solid var(--border);}
.tt-thumb{width:14px;height:14px;background:#fff;border-radius:50%;position:absolute;top:2px;left:2px;
  transition:transform .3s,background .3s;box-shadow:0 1px 4px rgba(0,0,0,.15);}
[data-bs-theme="dark"] .tt-thumb{transform:translateX(18px);background:var(--gold);}
/* the toggle is the first thing to go on a very narrow phone */
@media(max-width:359px){.th-toggle{display:none;}}

.av-wrap{position:relative;}
.av-chip{display:flex;align-items:center;gap:7px;background:var(--gold-bg);
  border:1px solid var(--gold-border);border-radius:20px;padding:4px 10px 4px 5px;cursor:pointer;}
.av-circle{width:26px;height:26px;background:linear-gradient(135deg,#9a8053,#b8975e);border-radius:50%;
  display:flex;align-items:center;justify-content:center;color:#fff;font-size:.6rem;font-weight:700;flex-shrink:0;}
.av-name{font-size:.72rem;font-weight:600;color:var(--gold);display:none;}
@media(min-width:520px){.av-name{display:block;}}
.av-menu{position:absolute;top:calc(100% + 8px);right:0;min-width:200px;background:var(--card);
  border:1px solid var(--border);border-radius:12px;box-shadow:var(--shadow-md);
  padding:6px;z-index:200;display:none;}
.av-menu.open{display:block;}
.av-menu-head{padding:8px 10px 9px;border-bottom:1px solid var(--border);margin-bottom:5px;}
.av-menu-name{font-size:.8rem;font-weight:600;color:var(--text);}
.av-menu-role{font-size:.68rem;color:var(--muted);margin-top:1px;}
.av-menu-item{display:flex;align-items:center;gap:9px;width:100%;padding:9px 10px;border-radius:8px;
  background:none;border:none;cursor:pointer;font-family:'SF Pro Display','Inter',sans-serif;
  font-size:.78rem;color:var(--text);text-align:left;text-decoration:none;transition:background .15s;}
.av-menu-item:hover{background:var(--surface);}
.av-menu-item i{font-size:.85rem;color:var(--muted);}
.av-menu-item.danger,.av-menu-item.danger i{color:var(--red);}

/* LIVE STRIP */
.live-row{background:linear-gradient(90deg,rgba(154,128,83,.08),rgba(154,128,83,.03));
  border-bottom:1px solid var(--gold-border);flex-shrink:0;padding:8px 14px;}
.live-inner{max-width:920px;margin:0 auto;display:flex;align-items:center;
  justify-content:space-between;gap:10px;flex-wrap:wrap;}
.live-chip{display:inline-flex;align-items:center;gap:6px;font-size:.75rem;font-weight:500;color:var(--gold);}
.live-dot{width:7px;height:7px;border-radius:50%;background:#22c55e;animation:ldot 1.8s infinite;flex-shrink:0;}
@keyframes ldot{0%,100%{opacity:1;transform:scale(1);}50%{opacity:.4;transform:scale(.7);}}
.sla-warn{display:inline-flex;align-items:center;gap:5px;font-size:.73rem;font-weight:500;
  text-decoration:none;color:var(--amber);background:var(--amber-bg);padding:3px 9px;
  border-radius:20px;border:1px solid rgba(217,119,6,.2);}
.sla-warn.crit{color:var(--red);background:var(--red-bg);border-color:rgba(220,38,38,.2);}
.sla-warn.ok{color:var(--green);background:var(--green-bg);border-color:rgba(21,128,61,.2);}

/* TAB BAR - scrolls sideways on a phone, centres on desktop */
.tab-bar{background:var(--tab-bg);border-bottom:1px solid var(--border);flex-shrink:0;
  overflow-x:auto;-webkit-overflow-scrolling:touch;}
.tab-bar::-webkit-scrollbar{display:none;}
.tab-bar-inner{display:flex;max-width:920px;margin:0 auto;min-width:min-content;}
.tab-btn{display:flex;flex-direction:column;align-items:center;gap:2px;padding:10px 15px;
  cursor:pointer;white-space:nowrap;border-bottom:2px solid transparent;transition:all .15s;
  flex-shrink:0;background:none;border-top:none;border-left:none;border-right:none;
  position:relative;text-decoration:none;font-family:'SF Pro Display','Inter',sans-serif;}
.tab-btn i{font-size:.95rem;color:var(--light);transition:color .15s;}
.tab-btn span{font-size:.63rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em;
  color:var(--muted);transition:color .15s;}
.tab-btn .tab-badge{font-size:.58rem;font-weight:700;padding:1px 5px;border-radius:8px;margin-top:1px;}
.tab-btn.active{border-bottom-color:var(--gold);}
.tab-btn.active i,.tab-btn.active span{color:var(--gold);}
/* a tab that navigates to the other page rather than switching a pane */
.tab-btn.jump i,.tab-btn.jump span{color:var(--gold);}
.tab-btn.jump::after{content:'';position:absolute;left:14px;right:14px;bottom:0;
  height:2px;background:var(--gold-border);}
.tab-live{position:absolute;top:7px;right:10px;width:8px;height:8px;border-radius:50%;
  background:#22c55e;animation:ldot 1.8s infinite;}
@media(min-width:600px){.tab-btn{padding:10px 19px;}.tab-btn span{font-size:.68rem;}}
@media(min-width:960px){.tab-bar-inner{justify-content:center;}}

/* CONTENT */
.tab-content{flex:1;overflow-y:auto;-webkit-overflow-scrolling:touch;}
.tab-content::-webkit-scrollbar{width:4px;}
.tab-content::-webkit-scrollbar-thumb{background:var(--border2);border-radius:2px;}
.tab-pane{display:none;max-width:920px;margin:0 auto;
  padding:16px max(14px,env(safe-area-inset-left)) calc(80px + env(safe-area-inset-bottom));}
.tab-pane.active{display:block;}
@media(min-width:600px){.tab-pane{padding:20px 20px 80px;}}

/* ══════════════════════════════════════════════════════ COMMON ═══ */
.sec-label{font-size:.67rem;font-weight:700;text-transform:uppercase;letter-spacing:.1em;
  color:var(--light);margin:18px 0 10px;display:flex;align-items:center;gap:6px;}
.sec-label:first-child{margin-top:0;}
.sec-label i{color:var(--gold);font-size:.75rem;}

.pane-head{display:flex;align-items:flex-start;justify-content:space-between;gap:10px;margin-bottom:14px;}
.pane-title{font-family:'Cormorant Garamond',Georgia,serif;font-size:1.2rem;font-weight:700;
  color:var(--text);line-height:1.1;display:flex;align-items:center;gap:7px;}
.pane-title i{font-size:.9rem;color:var(--gold);}
.pane-sub{font-size:.7rem;color:var(--muted);margin-top:3px;}
.pane-count{font-size:.68rem;font-weight:700;padding:4px 11px;border-radius:20px;
  background:var(--gold-bg);color:var(--gold);white-space:nowrap;flex-shrink:0;}

.card{background:var(--card);border:1px solid var(--border);border-radius:14px;
  box-shadow:var(--shadow);overflow:hidden;margin-bottom:12px;}
.card:last-child{margin-bottom:0;}
.card-pad{padding:16px;}
@media(min-width:600px){.card-pad{padding:18px 20px;}}

.section-card{background:var(--card);border:1px solid var(--border);border-radius:14px;
  margin-bottom:12px;overflow:hidden;box-shadow:var(--shadow);}
.section-card-hdr{padding:11px 15px;border-bottom:1px solid var(--border);font-size:.78rem;
  font-weight:700;color:var(--text);display:flex;align-items:center;gap:7px;}
.section-card-body{padding:14px 15px;}

.stat-grid{display:grid;gap:10px;margin-bottom:12px;}
.stat-grid.g2{grid-template-columns:repeat(2,1fr);}
.stat-grid.g3{grid-template-columns:repeat(3,1fr);}
@media(max-width:400px){.stat-grid.g3{grid-template-columns:repeat(2,1fr);}}
.stat-box{background:var(--card);border:1px solid var(--border);border-radius:12px;padding:13px 14px;}
.stat-box.filled{background:var(--card2);}
.stat-label{font-size:.67rem;font-weight:600;text-transform:uppercase;letter-spacing:.07em;
  color:var(--muted);margin-bottom:5px;}
.stat-val{font-family:'Cormorant Garamond',Georgia,serif;font-size:1.8rem;font-weight:700;
  color:var(--text);line-height:1;}
.stat-sub{font-size:.69rem;color:var(--muted);margin-top:3px;}
.stat-delta{font-size:.7rem;font-weight:600;display:inline-flex;align-items:center;gap:2px;margin-top:4px;}
.d-up{color:var(--green);}.d-dn{color:var(--red);}

.mini-row{display:grid;grid-template-columns:repeat(3,1fr);gap:9px;margin-bottom:14px;}
.mini-box{background:var(--card);border:1px solid var(--border);border-radius:12px;
  padding:12px 8px;text-align:center;box-shadow:var(--shadow);}
.mini-val{font-family:'Cormorant Garamond',Georgia,serif;font-size:1.5rem;font-weight:700;
  color:var(--text);line-height:1.1;}
.mini-lbl{font-size:.62rem;text-transform:uppercase;letter-spacing:.07em;color:var(--muted);margin-top:3px;}

.pill{font-size:.67rem;font-weight:700;padding:3px 8px;border-radius:20px;
  display:inline-flex;align-items:center;gap:4px;white-space:nowrap;}
.pill-green{background:var(--green-bg);color:var(--green);}
.pill-amber{background:var(--amber-bg);color:var(--amber);}
.pill-red{background:var(--red-bg);color:var(--red);}
.pill-blue{background:var(--blue-bg);color:var(--blue);}
.pill-gold{background:var(--gold-bg);color:var(--gold);}
.pill-purple{background:var(--purple-bg);color:var(--purple);}
.pill-submitted{background:var(--gold-bg);color:var(--gold);}
.pill-approved{background:var(--green-bg);color:var(--green);}
.pill-rejected{background:var(--red-bg);color:var(--red);}

.as-link{display:block;text-decoration:none;color:inherit;transition:border-color .15s,transform .15s;}
.as-link:hover{border-color:var(--gold-border);}
.as-link:active{transform:scale(.995);}

.empty-state{display:flex;flex-direction:column;align-items:center;justify-content:center;
  padding:32px 20px;text-align:center;}
.empty-state i{font-size:2.2rem;color:var(--light);margin-bottom:10px;}
.empty-state h6{font-size:.86rem;font-weight:600;color:var(--text);margin:0 0 4px;}
.empty-state p{font-size:.8rem;color:var(--muted);margin:0;}
.empty-state a{color:var(--gold);font-size:.78rem;margin-top:8px;text-decoration:none;font-weight:600;}
.empty-state a:hover{text-decoration:underline;}

.skel{background:var(--surface);border-radius:12px;height:74px;margin-bottom:10px;
  animation:pulse 1.4s ease-in-out infinite;}
@keyframes pulse{0%,100%{opacity:1;}50%{opacity:.55;}}
.spin{display:inline-block;width:14px;height:14px;border:2px solid rgba(255,255,255,.4);
  border-top-color:#fff;border-radius:50%;animation:spin .7s linear infinite;}
@keyframes spin{to{transform:rotate(360deg);}}

/* BUTTONS */
.action-btn{padding:6px 13px;border-radius:20px;font-size:.72rem;font-weight:600;cursor:pointer;
  border:none;transition:all .15s;font-family:'SF Pro Display','Inter',sans-serif;
  text-decoration:none;display:inline-flex;align-items:center;gap:5px;}
.action-btn.primary{background:var(--gold);color:#fff;}
.action-btn.primary:hover{opacity:.88;}
.action-btn.outline{background:transparent;border:1px solid var(--border2);color:var(--muted);}
.action-btn.outline:hover{border-color:var(--gold);color:var(--gold);}
.btn-outline{width:100%;border:1px dashed rgba(217,119,6,.45);border-radius:11px;padding:.65rem 1rem;
  font-size:.8rem;font-weight:600;cursor:pointer;display:flex;align-items:center;justify-content:center;
  gap:6px;background:var(--amber-bg);color:var(--amber);transition:all .18s;margin-bottom:12px;
  font-family:'SF Pro Display','Inter',sans-serif;}
.btn-outline:hover:not(:disabled){border-color:var(--amber);}
.btn-outline:disabled{opacity:.4;cursor:not-allowed;}
.btn-outline.brand{border-color:var(--gold-border);color:var(--gold);background:var(--gold-bg);}
.btn-outline.brand:hover:not(:disabled){border-color:var(--gold);}
.btn-outline.danger{border-color:rgba(220,38,38,.4);color:var(--red);background:var(--red-bg);}

/* ══════════════════════════════════════════════════════ DRAWERS ═══ */
.overlay{display:none;position:fixed;inset:0;background:var(--overlay);z-index:800;}
.overlay.show{display:block;}
.drawer{position:fixed;bottom:0;left:50%;transform:translateX(-50%) translateY(100%);
  width:100%;max-width:540px;background:var(--card);border-radius:20px 20px 0 0;z-index:900;
  padding:0 0 max(20px,env(safe-area-inset-bottom));
  transition:transform .32s cubic-bezier(.4,0,.2,1);max-height:92vh;max-height:92dvh;overflow-y:auto;}
.drawer.open{transform:translateX(-50%) translateY(0);}
.drawer-handle{width:36px;height:4px;background:var(--border2);border-radius:2px;margin:10px auto 0;}
.drawer-hdr{padding:14px 18px 11px;border-bottom:1px solid var(--border);
  display:flex;align-items:center;justify-content:space-between;}
.drawer-hdr h6{margin:0;font-family:'Cormorant Garamond',Georgia,serif;font-size:1.1rem;
  font-weight:700;color:var(--text);display:flex;align-items:center;gap:8px;}
.drawer-close{background:none;border:none;color:var(--muted);cursor:pointer;font-size:1.05rem;
  padding:4px;border-radius:6px;line-height:1;}
.drawer-close:hover{color:var(--text);background:var(--surface);}
.drawer-body{padding:15px 18px;}
.drawer-sr{font-size:.72rem;color:var(--muted);background:var(--card2);border:1px solid var(--border);
  border-radius:9px;padding:8px 11px;margin-bottom:13px;display:flex;align-items:center;gap:6px;}
.drawer-sr i,.drawer-sr strong{color:var(--gold);}
.drawer-actions{display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-top:18px;}

.d-label{font-size:.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.07em;
  color:var(--muted);display:block;margin:13px 0 6px;}
.d-label:first-of-type{margin-top:0;}
.req{color:var(--red);}
.d-input,.d-select,.d-remark{width:100%;font-size:.85rem;border:1px solid var(--border2);
  border-radius:10px;padding:.55rem .8rem;color:var(--text);background:var(--card);
  -webkit-appearance:none;transition:border-color .15s,box-shadow .15s;
  font-family:'SF Pro Display','Inter',sans-serif;}
.d-remark{resize:none;min-height:76px;line-height:1.5;}
.d-input:focus,.d-select:focus,.d-remark:focus{border-color:var(--gold);
  box-shadow:0 0 0 3px var(--gold-bg);outline:none;}
.d-input::placeholder,.d-remark::placeholder{color:var(--light);}
[data-bs-theme="dark"] .d-select option{background:#252220;color:#e8e0d4;}

.btn-save{background:linear-gradient(135deg,#9a8053,#b8975e);color:#fff;border:none;border-radius:11px;
  padding:.65rem 1rem;font-size:.84rem;font-weight:700;cursor:pointer;transition:all .18s;
  font-family:'SF Pro Display','Inter',sans-serif;display:flex;align-items:center;justify-content:center;gap:6px;}
.btn-save:hover:not(:disabled){box-shadow:0 4px 14px rgba(154,128,83,.4);}
.btn-save:disabled{opacity:.55;cursor:not-allowed;}
.btn-save.warn{background:linear-gradient(135deg,#d97706,#f59e0b);}
.btn-save.danger{background:linear-gradient(135deg,#dc2626,#ef4444);}
.btn-cancel{background:var(--surface);border:1px solid var(--border2);border-radius:11px;
  padding:.65rem 1rem;font-size:.84rem;font-weight:600;color:var(--muted);cursor:pointer;
  transition:all .18s;font-family:'SF Pro Display','Inter',sans-serif;}
.btn-cancel:hover{border-color:var(--muted);color:var(--text);}

.hold-note{background:var(--amber-bg);border:1px solid rgba(217,119,6,.2);border-radius:9px;
  padding:10px 12px;font-size:.73rem;color:var(--muted);display:flex;align-items:flex-start;
  gap:7px;margin-bottom:11px;line-height:1.5;}
.hold-note i{color:var(--amber);flex-shrink:0;margin-top:1px;}
.hold-note.danger{background:var(--red-bg);border-color:rgba(220,38,38,.2);}
.hold-note.danger i{color:var(--red);}

/* ══════════════════════════════════════════════════════ TOAST ═══ */
#tw{position:fixed;bottom:20px;right:16px;z-index:9999;display:flex;flex-direction:column;
  gap:8px;pointer-events:none;max-width:calc(100% - 32px);}
.ti{display:flex;align-items:flex-start;gap:9px;padding:11px 14px;border-radius:11px;
  background:var(--card);border:1px solid var(--border);border-left:3px solid var(--gold);
  box-shadow:var(--shadow-md);max-width:300px;animation:tin .2s ease;pointer-events:auto;}
@keyframes tin{from{opacity:0;transform:translateY(8px);}to{opacity:1;transform:none;}}
.ti.success{border-left-color:var(--green);}
.ti.error{border-left-color:var(--red);}
.ti.warning{border-left-color:var(--amber);}
.ti-ico{font-size:.95rem;flex-shrink:0;margin-top:1px;color:var(--gold);}
.ti.success .ti-ico{color:var(--green);}
.ti.error .ti-ico{color:var(--red);}
.ti.warning .ti-ico{color:var(--amber);}
.ti-t{font-size:.79rem;font-weight:600;color:var(--text);margin:0 0 2px;}
.ti-b{font-size:.73rem;color:var(--muted);margin:0;}

/* ══════════════════════════════════════════════════════ LIGHTBOX ═══ */
.lb-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.8);z-index:3000;
  padding:48px 24px 24px;overflow:auto;}
.lb-overlay.show{display:flex;align-items:center;justify-content:center;}
.lb-box{position:relative;display:flex;flex-direction:column;max-width:min(900px,92vw);
  max-height:88vh;margin:auto;}
.lb-close{position:absolute;top:-34px;right:0;background:none;border:0;color:#fff;
  font-size:1.05rem;line-height:1;padding:4px 8px;cursor:pointer;z-index:2;}
.lb-close:hover{color:#fbbc06;}
.lb-media{display:flex;flex-direction:column;min-height:0;max-width:100%;}
.lb-media img{max-width:100%;max-height:78vh;width:auto;height:auto;object-fit:contain;
  display:block;margin:0 auto;border-radius:8px;background:#fff;}
.lb-pdf iframe{width:min(900px,92vw);height:78vh;border:0;border-radius:8px;background:#fff;display:block;}
.lb-cap{display:flex;align-items:center;justify-content:space-between;gap:16px;margin-top:10px;
  color:#fff;font-size:.78rem;}
.lb-cap > span{overflow:hidden;text-overflow:ellipsis;white-space:nowrap;min-width:0;opacity:.85;}
.lb-open{color:#fbbc06!important;text-decoration:none!important;white-space:nowrap;flex-shrink:0;
  display:inline-flex;align-items:center;gap:5px;}
.lb-open:hover{text-decoration:underline!important;}

/* ══════════════════════════════════════════════════════ MODAL ═══ */
.modal-backdrop{position:fixed;inset:0;z-index:99999;background:var(--overlay);display:flex;
  align-items:center;justify-content:center;padding:16px;}
.modal-card{width:100%;max-width:380px;padding:20px;border-radius:16px;background:var(--card);
  color:var(--text);display:flex;flex-direction:column;gap:10px;box-shadow:0 18px 50px rgba(0,0,0,.35);}
.modal-card .inp{width:100%;padding:11px 13px;border-radius:10px;font-size:14px;
  border:1px solid var(--border2);background:var(--card2);color:inherit;
  font-family:'SF Pro Display','Inter',sans-serif;}
.modal-card .inp:focus{outline:none;border-color:var(--gold);}
.modal-actions{display:flex;gap:8px;margin-top:6px;}
.modal-actions button{flex:1;margin-bottom:0;}
.pwd-msg{font-size:13px;padding:8px 10px;border-radius:8px;}
.pwd-err{color:var(--red);background:var(--red-bg);}
.pwd-ok{color:var(--green);background:var(--green-bg);}

a:focus-visible,button:focus-visible{outline:2px solid var(--gold);outline-offset:2px;border-radius:8px;}
@media(prefers-reduced-motion:reduce){
  *,*::before,*::after{animation-duration:.01ms!important;transition-duration:.01ms!important;}
}
  </style>
  @stack('styles')
</head>
<body>
@include('partials.sr_tracking_modal', ['srTrackingBase' => url('/worker/sr-tracking-mobile')])
<div id="tw"></div>

<div class="lb-overlay" id="lbOverlay">
  <div class="lb-box">
    <button class="lb-close" id="lbClose"><i class="bi bi-x-lg"></i></button>
    <div id="lbContent"></div>
  </div>
</div>

{{-- Sign out - every page has it, so it lives here --}}
<div class="overlay" data-overlay="out"></div>
<div class="drawer" data-drawer="out">
  <div class="drawer-handle"></div>
  <div class="drawer-hdr">
    <h6><i class="bi bi-box-arrow-right" style="color:var(--red);"></i>Sign out</h6>
    <button class="drawer-close" data-close="out"><i class="bi bi-x-lg"></i></button>
  </div>
  <div class="drawer-body">
    <div class="hold-note danger">
      <i class="bi bi-question-circle-fill"></i>
      You'll need to sign in again to reach your jobs. Anything unsaved in the terminal will be lost.
    </div>
    <div class="drawer-actions">
      <button class="btn-cancel" data-close="out">Stay signed in</button>
      <button class="btn-save danger" id="outConfirmBtn"><i class="bi bi-box-arrow-right"></i> Sign out</button>
    </div>
  </div>
</div>

@yield('drawers')

<div class="app">

  <header class="header">
    <div class="hdr-inner">
      <div class="hdr-left">
        <div class="hdr-icon">MM</div>
        <div class="hdr-text">
          <div class="hdr-title cg">@yield('heading', 'Field Console')</div>
          <div class="hdr-sub">{{ $userRole ?? 'Maintenance Lead' }}@if(!empty($userCode)) &middot; {{ $userCode }}@endif</div>
        </div>
      </div>
      <div class="hdr-right">
        <span class="clk" id="clk"></span>
        <button class="th-toggle" id="themeBtn" aria-label="Toggle theme">
          <i class="bi bi-sun-fill th-sun"></i>
          <div class="tt-track"><div class="tt-thumb"></div></div>
          <i class="bi bi-moon-stars-fill th-moon"></i>
        </button>
        <div class="av-wrap">
          <button class="av-chip" id="avBtn" aria-haspopup="true" aria-expanded="false">
            <div class="av-circle">{{ $userInitials ?? '' }}</div>
            <span class="av-name">{{ $userShort ?? ($userName ?? '') }}</span>
          </button>
          <div class="av-menu" id="avMenu">
            <div class="av-menu-head">
              <div class="av-menu-name">{{ $userName ?? '' }}</div>
              <div class="av-menu-role">{{ $userRole ?? 'Maintenance Lead' }}</div>
            </div>
            <a class="av-menu-item" href="{{ $routes['dashboard'] ?? route('worker.dashboard') }}">
              <i class="bi bi-speedometer2"></i>Dashboard
            </a>
            <a class="av-menu-item" href="{{ $routes['pipeline'] ?? route('worker.pipeline') }}">
              <i class="bi bi-list-task"></i>My pipeline
            </a>
            <a class="av-menu-item" href="{{ ($routes['pipeline'] ?? route('worker.pipeline')) }}#history">
              <i class="bi bi-clock-history"></i>Work history
            </a>
            <a class="av-menu-item" href="{{ ($routes['pipeline'] ?? route('worker.pipeline')) }}#profile">
              <i class="bi bi-person-circle"></i>Profile
            </a>
            <button class="av-menu-item danger" id="logoutBtn"><i class="bi bi-box-arrow-right"></i>Sign out</button>
          </div>
        </div>
      </div>
    </div>
  </header>

  @hasSection('live')
    <div class="live-row"><div class="live-inner">@yield('live')</div></div>
  @endif

  <div class="tab-bar"><div class="tab-bar-inner">@yield('tabs')</div></div>

  <div class="tab-content" id="tabContent">
    @yield('content')
  </div>
</div>

<form method="POST" action="{{ $routes['logout'] ?? route('worker.logout') }}" id="logoutForm" style="display:none;">@csrf</form>

<script>
'use strict';

/* ══════════════════════════════════════════════════════
   HELPERS
══════════════════════════════════════════════════════ */
const $ = (id) => document.getElementById(id);
const CSRF = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

/** Escape for HTML text and quoted attribute contexts. */
function esc(value) {
  return String(value ?? '')
    .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;').replace(/'/g, '&#39;');
}

function showToast(type, title, body) {
  const icons = {
    success: 'bi-check-circle-fill', error: 'bi-x-circle-fill',
    warning: 'bi-exclamation-triangle-fill', primary: 'bi-info-circle-fill',
  };
  const el = document.createElement('div');
  el.className = `ti ${type}`;
  el.innerHTML =
    `<i class="bi ${icons[type] ?? icons.primary} ti-ico"></i>` +
    `<div><p class="ti-t">${esc(title)}</p><p class="ti-b">${esc(body)}</p></div>`;
  $('tw').appendChild(el);
  setTimeout(() => {
    el.style.transition = 'opacity .3s';
    el.style.opacity = '0';
    setTimeout(() => el.remove(), 300);
  }, 4000);
}

/** Swap a button into a busy state; returns a restore() closure. */
function busy(btn, label) {
  const original = btn.innerHTML;
  const wasDisabled = btn.disabled;
  btn.disabled = true;
  btn.innerHTML = `<span class="spin"></span> ${esc(label)}`;
  return () => { btn.innerHTML = original; btn.disabled = wasDisabled; };
}

/** POST helper. Surfaces the server's validation message rather than a bare status. */
async function apiPost(url, payload, isForm = false) {
  if (!url) throw new Error('Endpoint not configured.');

  const opts = {
    method: 'POST',
    headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
    body: isForm ? payload : JSON.stringify(payload),
  };
  if (!isForm) opts.headers['Content-Type'] = 'application/json';

  const res = await fetch(url, opts);

  if (!res.ok) {
    let msg = `Request failed (${res.status})`;
    try {
      const body = await res.json();
      // Laravel 422 puts field errors under `errors`, the summary under `message`.
      if (body.errors) msg = Object.values(body.errors).flat()[0] ?? msg;
      else if (body.message) msg = body.message;
    } catch { /* non-JSON error body; keep the status message */ }
    throw new Error(msg);
  }
  return res.json();
}

async function apiGet(url) {
  if (!url) throw new Error('Endpoint not configured.');
  const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
  if (!res.ok) throw new Error(`Request failed (${res.status})`);
  return res.json();
}

/* ══════════════════════════════════════════════════════
   THEME + CLOCK
══════════════════════════════════════════════════════ */
function applyTheme(dark) {
  document.documentElement.setAttribute('data-bs-theme', dark ? 'dark' : 'light');
  try { localStorage.setItem('ml_theme', dark ? 'dark' : 'light'); } catch (e) {}
  if (typeof window.onThemeChange === 'function') window.onThemeChange(dark);
}

(function initTheme() {
  let saved = null;
  try { saved = localStorage.getItem('ml_theme'); } catch (e) {}
  applyTheme(saved ? saved === 'dark' : window.matchMedia('(prefers-color-scheme:dark)').matches);
})();

$('themeBtn')?.addEventListener('click', () => {
  applyTheme(document.documentElement.getAttribute('data-bs-theme') !== 'dark');
});

(function initClock() {
  const tick = () => {
    const el = $('clk');
    if (el) el.textContent = new Date().toLocaleTimeString('en-IN', { hour: '2-digit', minute: '2-digit' });
  };
  tick();
  setInterval(tick, 1000);
})();

/* ══════════════════════════════════════════════════════
   ACCOUNT MENU + SIGN OUT
══════════════════════════════════════════════════════ */
const avBtn = $('avBtn');
const avMenu = $('avMenu');

avBtn.addEventListener('click', (e) => {
  e.stopPropagation();
  const open = avMenu.classList.toggle('open');
  avBtn.setAttribute('aria-expanded', open ? 'true' : 'false');
});
document.addEventListener('click', () => {
  avMenu.classList.remove('open');
  avBtn.setAttribute('aria-expanded', 'false');
});

$('logoutBtn').addEventListener('click', () => {
  // A page can block this - the pipeline does while a punch is open.
  if (typeof window.beforeSignOut === 'function' && window.beforeSignOut() === false) return;
  openDrawer('out');
});

$('outConfirmBtn').addEventListener('click', () => {
  const btn = $('outConfirmBtn');
  btn.disabled = true;
  btn.innerHTML = '<span class="spin"></span> Signing out\u2026';
  $('logoutForm').submit();
});

/* ══════════════════════════════════════════════════════
   DRAWERS - discovered from the DOM, so a page just adds
   [data-drawer="x"] + [data-overlay="x"] and it works.
══════════════════════════════════════════════════════ */
const DRAWERS = {};

document.querySelectorAll('[data-drawer]').forEach((el) => {
  const name = el.dataset.drawer;
  DRAWERS[name] = { drawer: el, overlay: document.querySelector(`[data-overlay="${name}"]`) };
});

function openDrawer(name) {
  const d = DRAWERS[name];
  if (!d) return;
  d.drawer.classList.add('open');
  d.overlay?.classList.add('show');
  document.body.style.overflow = 'hidden';
}

function closeDrawer(name) {
  const d = DRAWERS[name];
  if (!d) return;
  d.drawer.classList.remove('open');
  d.overlay?.classList.remove('show');
  document.body.style.overflow = '';
}

function closeAllDrawers() { Object.keys(DRAWERS).forEach(closeDrawer); }

Object.entries(DRAWERS).forEach(([name, { overlay }]) => {
  overlay?.addEventListener('click', () => closeDrawer(name));
});
document.querySelectorAll('[data-close]').forEach((btn) => {
  btn.addEventListener('click', () => closeDrawer(btn.dataset.close));
});

/* ══════════════════════════════════════════════════════
   LIGHTBOX
══════════════════════════════════════════════════════ */
function openLightbox(type, url, name) {
  const overlay = $('lbOverlay');
  const content = $('lbContent');
  if (!overlay || !content) return;

  const openBtn = `<a href="${esc(url)}" target="_blank" rel="noopener" class="lb-open">
                     <i class="bi bi-box-arrow-up-right"></i> Open in new tab
                   </a>`;

  content.innerHTML = type === 'pdf'
    ? `<div class="lb-media lb-pdf">
         <iframe src="${esc(url)}#view=FitH" title="${esc(name || '')}"></iframe>
         <div class="lb-cap"><span>${esc(name || '')}</span>${openBtn}</div>
       </div>`
    : `<div class="lb-media">
         <img src="${esc(url)}" alt="${esc(name || '')}">
         <div class="lb-cap"><span>${esc(name || '')}</span>${openBtn}</div>
       </div>`;

  overlay.classList.add('show');
  document.body.style.overflow = 'hidden';
}

function closeLightbox() {
  $('lbOverlay')?.classList.remove('show');
  const c = $('lbContent');
  if (c) c.innerHTML = '';
  document.body.style.overflow = '';
}

window.openLightbox = openLightbox;    // inline onclick handlers need these
window.closeLightbox = closeLightbox;

$('lbClose')?.addEventListener('click', closeLightbox);
$('lbOverlay')?.addEventListener('click', (e) => { if (e.target === $('lbOverlay')) closeLightbox(); });

/* Escape closes whatever is on top. */
document.addEventListener('keydown', (e) => {
  if (e.key !== 'Escape') return;
  if ($('lbOverlay')?.classList.contains('show')) { closeLightbox(); return; }
  const open = Object.keys(DRAWERS).find((n) => DRAWERS[n].drawer.classList.contains('open'));
  if (open) { closeDrawer(open); return; }
  avMenu.classList.remove('open');
});

/* ══════════════════════════════════════════════════════
   TABS
   Panes are #tab-<id>; buttons carry data-tab="<id>".
   onTabShow('history', fn) runs fn the first time that tab opens.
══════════════════════════════════════════════════════ */
const TAB_HOOKS = {};

function onTabShow(id, fn) {
  (TAB_HOOKS[id] = TAB_HOOKS[id] || []).push(fn);
}

function switchTab(id) {
  const pane = $(`tab-${id}`);
  if (!pane) return;

  document.querySelectorAll('.tab-pane').forEach((p) => p.classList.remove('active'));
  document.querySelectorAll('.tab-btn[data-tab]').forEach((b) => {
    b.classList.toggle('active', b.dataset.tab === id);
  });
  pane.classList.add('active');
  $('tabContent').scrollTop = 0;

  (TAB_HOOKS[id] || []).forEach((fn) => fn());
  animateBars();
}

document.addEventListener('click', (e) => {
  const btn = e.target.closest('.tab-btn[data-tab], [data-goto]');
  if (!btn) return;
  const id = btn.dataset.tab || btn.dataset.goto;
  if (!id || !$(`tab-${id}`)) return;
  e.preventDefault();
  switchTab(id);
});

/** Run the width transition on any bar rendered with data-w. */
function animateBars() {
  requestAnimationFrame(() => {
    document.querySelectorAll('[data-w]').forEach((el) => { el.style.width = el.dataset.w; });
  });
}
</script>

@stack('scripts')
</body>
</html>