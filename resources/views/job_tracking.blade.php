<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0"/>
<title>Track Your Service Request | Matter Mind</title>
<link rel="icon" type="image/png" href="{{ asset('assets/images/favicon.png') }}">

<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600;700&display=swap" rel="stylesheet"/>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet"/>

<style>
/* ── THEME TOKENS ── */
:root{
  --bg:#f5f2ee;--surface:#fff;--surface-2:#f9f6f2;
  --border:#e8e2d8;--border-2:#f0ece6;
  --text:#1a1614;--text-muted:#7a756e;--text-light:#a09890;
  --card-shadow:0 2px 12px rgba(154,128,83,.07);
  --header-bg:#fff;--header-border:#e8e2d8;
}
[data-theme="dark"]{
  --bg:#141210;--surface:#1e1b18;--surface-2:#252220;
  --border:#332e28;--border-2:#2a2520;
  --text:#e8e0d4;--text-muted:#8a8178;--text-light:#5a5248;
  --card-shadow:0 2px 16px rgba(0,0,0,.4);
  --header-bg:#1a1714;--header-border:#332e28;
}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0;-webkit-tap-highlight-color:transparent;}
body{font-size:.875rem;
  background:var(--bg);color:var(--text);min-height:100vh;overflow-x:hidden;}

/* ── HEADER ── */
.pub-header{background:var(--header-bg);border-bottom:1px solid var(--header-border);padding:14px 20px;
  display:flex;align-items:center;justify-content:space-between;position:sticky;
  top:0;z-index:100;box-shadow:0 2px 12px rgba(154,128,83,.08);}
.brand{display:flex;align-items:center;gap:10px;}
.brand-icon{width:36px;height:36px;background:linear-gradient(135deg,#9a8053,#b8975e);
  border-radius:8px;display:flex;align-items:center;justify-content:center;
  color:#fff;font-size:.8rem;font-weight:700;
  letter-spacing:-.5px;flex-shrink:0;}
.brand-name{font-size:.9375rem;
  font-weight:700;color:var(--text);line-height:1.2;}
.brand-sub{font-size:.65rem;color:var(--text-muted);}
.header-right{display:flex;align-items:center;gap:10px;}
.live-badge{display:inline-flex;align-items:center;gap:5px;padding:4px 10px;
  border-radius:20px;font-size:.72rem;font-weight:600;background:rgba(21,128,61,.1);
  color:#15803d;border:1px solid rgba(21,128,61,.2);}
.live-dot{width:7px;height:7px;border-radius:50%;background:#15803d;animation:pulse 1.8s infinite;}
@keyframes pulse{0%,100%{opacity:1;transform:scale(1);}50%{opacity:.5;transform:scale(.8);}}

/* ── PAGE WRAP ── */
.page-wrap{margin:0 auto;padding:24px 16px 60px;}


/* ── SR CARD ── */
.sr-card{background:var(--surface);border:1px solid var(--border);border-radius:12px;
  padding:20px;margin-bottom:20px;box-shadow:0 2px 12px rgba(154,128,83,.07);}
.sr-card-top{display:flex;align-items:flex-start;justify-content:space-between;
  gap:12px;margin-bottom:14px;flex-wrap:wrap;}
.sr-id{font-size:1.1rem;font-weight:700;
  color:#9a8053;margin-bottom:3px;}
.sr-client{font-size:.9rem;font-weight:600;color:var(--text);margin-bottom:2px;}
.sr-site{font-size:.8rem;color:#7a756e;display:flex;align-items:center;gap:5px;}
.status-chip{display:inline-flex;align-items:center;gap:6px;padding:6px 14px;
  border-radius:20px;font-size:.78rem;font-weight:700;}
.chip-inprog{background:rgba(6,182,212,.1);color:#0891b2;border:1px solid rgba(6,182,212,.2);}
.chip-assigned{background:rgba(37,99,235,.1);color:#2563eb;border:1px solid rgba(37,99,235,.2);}
.chip-completed{background:rgba(21,128,61,.1);color:#15803d;border:1px solid rgba(21,128,61,.2);}
.chip-pending{background:rgba(107,114,128,.1);color:#6b7280;border:1px solid rgba(107,114,128,.2);}
.sr-meta-row{display:grid;grid-template-columns:repeat(3,1fr);gap:10px;}
@media(max-width:480px){.sr-meta-row{grid-template-columns:repeat(2,1fr);}}
.sr-meta-item{background:var(--surface-2);border-radius:8px;padding:9px 12px;}
.sr-meta-label{font-size:.65rem;font-weight:700;text-transform:uppercase;
  letter-spacing:.07em;color:#7a756e;margin-bottom:3px;}
.sr-meta-value{font-size:.82rem;font-weight:600;color:var(--text);}
.sr-meta-value.gold{color:#9a8053;}

/* ── TECHNICIAN CARD ── */
.tech-card{background:linear-gradient(135deg,#9a8053,#b8975e);border-radius:12px;
  padding:18px 20px;margin-bottom:20px;color:#fff;display:flex;align-items:center;
  gap:16px;box-shadow:0 4px 16px rgba(154,128,83,.25);}
.tech-avatar{width:52px;height:52px;border-radius:50%;background:rgba(255,255,255,.25);
  display:flex;align-items:center;justify-content:center;font-size:1.2rem;
  font-weight:700;flex-shrink:0;
  border:2px solid rgba(255,255,255,.4);}
.tech-info{flex:1;}
.tech-label{font-size:.68rem;opacity:.8;text-transform:uppercase;letter-spacing:.08em;margin-bottom:2px;}
.tech-name{font-size:1rem;font-weight:700;margin-bottom:4px;}
.tech-eta{font-size:.8rem;opacity:.9;display:flex;align-items:center;gap:5px;}
.tech-actions{display:flex;flex-direction:column;gap:7px;align-items:flex-end;}
.btn-maps{display:inline-flex;align-items:center;gap:5px;padding:6px 12px;
background:rgba(255,255,255,.2);color:#fff;border:1px solid rgba(255,255,255,.35);
border-radius:7px;font-size:.75rem;font-weight:500;cursor:pointer;
text-decoration:none;white-space:nowrap;transition:background .15s;}
.btn-maps:hover{background:rgba(255,255,255,.3);}
@media(max-width:480px){.tech-card{flex-wrap:wrap;}.tech-actions{align-items:flex-start;flex-direction:row;}}

/* ── MILESTONE GRAPH ── */
.milestone-section{background:var(--surface);border:1px solid var(--border);border-radius:12px;
  padding:22px 20px;margin-bottom:20px;box-shadow:0 2px 12px rgba(154,128,83,.07);}
.ms-section-title{font-size:.95rem;
  font-weight:700;color:var(--text);margin-bottom:6px;display:flex;align-items:center;gap:8px;}
.ms-section-sub{font-size:.76rem;color:#7a756e;margin-bottom:24px;color:var(--text-muted);}

/* DESKTOP: horizontal timeline */
.timeline-h{display:flex;align-items:flex-start;position:relative;overflow-x:auto;
  padding-bottom:8px;}
.timeline-h::-webkit-scrollbar{height:4px;}
.timeline-h::-webkit-scrollbar-thumb{background:#e8e2d8;border-radius:2px;}
.tl-item{display:flex;flex-direction:column;align-items:center;flex:1;
  min-width:90px;position:relative;z-index:1;}
/* connector lines */
.tl-item::before{content:'';position:absolute;top:19px;right:50%;left:-50%;
  height:3px;background:var(--border);z-index:0;}
.tl-item:first-child::before{display:none;}
.tl-item.done::before{background:linear-gradient(to right,#9a8053,#9a8053);}
.tl-item.active::before{background:linear-gradient(to right,#9a8053,#e8e2d8);}

.tl-node{width:40px;height:40px;border-radius:50%;display:flex;align-items:center;
  justify-content:center;font-size:.95rem;position:relative;z-index:2;
  flex-shrink:0;transition:all .3s;border:3px solid var(--border);background:var(--surface);}
.tl-node.done{background:#9a8053;border-color:#9a8053;color:#fff;}
.tl-node.active{background:#fff;border-color:#9a8053;color:#9a8053;
  box-shadow:0 0 0 5px rgba(154,128,83,.15),0 0 0 10px rgba(154,128,83,.07);}
.tl-node.active .node-icon{animation:nodeFloat 2.5s ease-in-out infinite;}
@keyframes nodeFloat{0%,100%{transform:scale(1);}50%{transform:scale(1.15);}}
.tl-node.pending{background:var(--surface-2);border-color:var(--border);color:var(--text-light);}

.tl-label{font-size:.7rem;font-weight:600;color:var(--text);margin-top:9px;
  text-align:center;line-height:1.3;max-width:90px;}
.tl-label.pending{color:var(--text-light);}
.tl-time{font-size:.65rem;color:var(--text-muted);margin-top:3px;text-align:center;}
.tl-time.active-time{color:#9a8053;font-weight:600;}

/* MOBILE: vertical timeline */
.timeline-v{display:none;padding-left:8px;}
.tlv-item{display:flex;gap:14px;position:relative;padding-bottom:24px;}
.tlv-item:last-child{padding-bottom:0;}
.tlv-left{display:flex;flex-direction:column;align-items:center;width:36px;flex-shrink:0;}
.tlv-node{width:36px;height:36px;border-radius:50%;display:flex;align-items:center;
  justify-content:center;font-size:.9rem;flex-shrink:0;border:3px solid var(--border);background:var(--surface);position:relative;z-index:1;}
.tlv-node.done{background:#9a8053;border-color:#9a8053;color:#fff;}
.tlv-node.active{background:#fff;border-color:#9a8053;color:#9a8053;
  box-shadow:0 0 0 4px rgba(154,128,83,.15),0 0 0 8px rgba(154,128,83,.07);}
.tlv-node.active .node-icon{animation:nodeFloat 2.5s ease-in-out infinite;}
.tlv-node.pending{background:var(--surface-2);border-color:var(--border);color:var(--text-light);}
.tlv-line{flex:1;width:3px;background:var(--border);margin-top:4px;min-height:20px;border-radius:2px;}
.tlv-line.done{background:#9a8053;}
.tlv-line.active{background:linear-gradient(to bottom,#9a8053,#e8e2d8);}
.tlv-item:last-child .tlv-line{display:none;}
.tlv-right{flex:1;padding-top:6px;}
.tlv-label{font-size:.82rem;font-weight:600;color:var(--text);margin-bottom:2px;}
.tlv-label.pending{color:var(--text-light);}
.tlv-time{font-size:.72rem;color:var(--text-muted);}
.tlv-time.active-time{color:#9a8053;font-weight:600;}
.tlv-desc{font-size:.72rem;color:var(--text-light);margin-top:3px;line-height:1.4;}

@media(max-width:600px){.timeline-h{display:none;}.timeline-v{display:block;}}

/* ── ACTIVE STATUS BANNER ── */
.active-banner{background:rgba(6,182,212,.07);border:1px solid rgba(6,182,212,.2);
  border-radius:10px;padding:14px 16px;margin-bottom:20px;
  display:flex;align-items:center;gap:12px;}
.ab-icon{width:40px;height:40px;border-radius:10px;background:rgba(6,182,212,.12);
  display:flex;align-items:center;justify-content:center;font-size:1.1rem;color:#0891b2;flex-shrink:0;}
.ab-title{font-size:.875rem;font-weight:600;color:var(--text);margin-bottom:2px;}
.ab-sub{font-size:.76rem;color:var(--text-muted);}


/* ── JOB DETAIL DRAWER ── */
.drawer-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);
  z-index:500;backdrop-filter:blur(3px);}
.drawer-overlay.show{display:block;}
.detail-drawer{position:fixed;bottom:0;left:0;right:0;max-height:88vh;
  background:var(--surface);border-top:1px solid var(--border);
  border-radius:18px 18px 0 0;z-index:501;
  transform:translateY(100%);transition:transform .32s cubic-bezier(.4,0,.2,1);
  overflow:hidden;display:flex;flex-direction:column;}
.detail-drawer.open{transform:translateY(0);}
.drawer-handle{width:36px;height:4px;background:var(--border);border-radius:2px;
  margin:12px auto 0;flex-shrink:0;}
.drawer-hdr{display:flex;align-items:center;justify-content:space-between;
  padding:14px 20px 12px;border-bottom:1px solid var(--border);flex-shrink:0;}
.drawer-hdr-left{display:flex;align-items:center;gap:10px;}
.drawer-hdr-icon{width:34px;height:34px;border-radius:8px;
  background:linear-gradient(135deg,#9a8053,#b8975e);
  display:flex;align-items:center;justify-content:center;color:#fff;font-size:.9rem;}
.drawer-title{font-size:.95rem;
  font-weight:700;color:var(--text);}
.drawer-sub{font-size:.72rem;color:var(--text-muted);margin-top:1px;}
.drawer-close{width:30px;height:30px;border-radius:7px;border:1px solid var(--border);
  background:var(--surface-2);color:var(--text-muted);cursor:pointer;
  display:flex;align-items:center;justify-content:center;font-size:.85rem;}
.drawer-close:hover{color:var(--text);}
.drawer-body{overflow-y:auto;padding:18px 20px 32px;flex:1;}
.drawer-body::-webkit-scrollbar{width:4px;}
.drawer-body::-webkit-scrollbar-thumb{background:var(--border);border-radius:2px;}

/* Detail sections */
.detail-section{margin-bottom:20px;}
.detail-section:last-child{margin-bottom:0;}
.detail-section-title{font-size:.68rem;font-weight:700;text-transform:uppercase;
  letter-spacing:.09em;color:var(--text-muted);margin-bottom:10px;
  display:flex;align-items:center;gap:6px;}
.detail-section-title i{color:#9a8053;}
.detail-grid{display:grid;grid-template-columns:1fr 1fr;gap:8px;}
@media(min-width:480px){.detail-grid{grid-template-columns:repeat(3,1fr);}}
.detail-cell{background:var(--surface-2);border-radius:8px;padding:9px 12px;}
.detail-cell.full{grid-column:1/-1;}
.dc-label{font-size:.67rem;font-weight:600;text-transform:uppercase;
  letter-spacing:.06em;color:var(--text-muted);margin-bottom:3px;}
.dc-value{font-size:.82rem;font-weight:600;color:var(--text);}
.dc-value.gold{color:#9a8053;}
.dc-value.green{color:#15803d;}

/* Technician block in drawer */
.tech-detail-block{display:flex;align-items:center;gap:14px;
  background:var(--surface-2);border-radius:10px;padding:14px 16px;}
.tech-detail-av{width:48px;height:48px;border-radius:50%;
  background:linear-gradient(135deg,#9a8053,#b8975e);
  display:flex;align-items:center;justify-content:center;color:#fff;
  font-size:1rem;font-weight:700;flex-shrink:0;}
.tech-detail-name{font-size:.9rem;font-weight:700;color:var(--text);margin-bottom:2px;}
.tech-detail-domain{font-size:.76rem;color:var(--text-muted);}
.tech-detail-status{margin-left:auto;text-align:right;}

/* Status timeline in drawer (compact vertical) */
.compact-timeline{display:flex;flex-direction:column;gap:0;}
.ct-item{display:flex;align-items:flex-start;gap:12px;padding:9px 0;}
.ct-left{display:flex;flex-direction:column;align-items:center;width:28px;flex-shrink:0;}
.ct-node{width:28px;height:28px;border-radius:50%;display:flex;align-items:center;
  justify-content:center;font-size:.75rem;flex-shrink:0;border:2px solid var(--border);
  background:var(--surface);}
.ct-node.done{background:#9a8053;border-color:#9a8053;color:#fff;}
.ct-node.active{background:var(--surface);border-color:#9a8053;color:#9a8053;
  box-shadow:0 0 0 3px rgba(154,128,83,.15);}
.ct-node.pending{background:var(--surface-2);color:var(--text-light);}
.ct-line{width:2px;flex:1;background:var(--border);min-height:12px;margin-top:2px;border-radius:1px;}
.ct-line.done{background:#9a8053;}
.ct-line.active{background:linear-gradient(to bottom,#9a8053,var(--border));}
.ct-item:last-child .ct-line{display:none;}
.ct-right{flex:1;padding-top:3px;}
.ct-label{font-size:.8rem;font-weight:600;color:var(--text);margin-bottom:1px;}
.ct-label.pending{color:var(--text-muted);font-weight:400;}
.ct-time{font-size:.71rem;color:var(--text-muted);}
.ct-time.gold{color:#9a8053;font-weight:500;}

/* Clickable SR card */
.sr-card{cursor:pointer;transition:box-shadow .15s,transform .1s;}
.sr-card:hover{box-shadow:0 4px 20px rgba(154,128,83,.15);transform:translateY(-1px);}
.sr-card-hint{font-size:.72rem;color:#9a8053;display:flex;align-items:center;
  gap:4px;margin-top:10px;justify-content:flex-end;opacity:.8;}

/* Clickable milestone nodes */
.tl-node{cursor:pointer;}
.tl-node:hover{opacity:.85;transform:scale(1.08);}
.tlv-node{cursor:pointer;}
.tlv-node:hover{opacity:.85;transform:scale(1.06);}

/* Milestone tooltip */
.ms-tooltip{position:fixed;background:var(--text);color:var(--surface);
  padding:8px 12px;border-radius:8px;font-size:.75rem;max-width:220px;
  z-index:600;pointer-events:none;opacity:0;transition:opacity .15s;
  box-shadow:0 4px 16px rgba(0,0,0,.2);line-height:1.4;}
.ms-tooltip.visible{opacity:1;}
.ms-tooltip strong{font-weight:600;}
.ms-tooltip::after{content:'';position:absolute;top:100%;left:50%;
  transform:translateX(-50%);border:5px solid transparent;
  border-top-color:var(--text);}

/* ── HEADER RIGHT ── */
.header-right{display:flex;align-items:center;gap:10px;}
.update-row{display:flex;align-items:center;gap:8px;}
.update-text{font-size:.74rem;color:var(--text-muted);white-space:nowrap;}
.update-text strong{color:var(--text);font-weight:600;}
.btn-refresh{display:inline-flex;align-items:center;gap:5px;padding:6px 11px;
  background:rgba(154,128,83,.12);color:#9a8053;
  border:1px solid rgba(154,128,83,.3);border-radius:7px;font-size:.75rem;
  font-weight:600;cursor:pointer;
  transition:background .15s;white-space:nowrap;line-height:1.2;}
.btn-refresh:hover{background:rgba(154,128,83,.22);}
.btn-refresh:disabled{opacity:.5;cursor:not-allowed;}
.btn-refresh.spinning i{animation:spin .6s linear infinite;}
.hdr-div{width:1px;height:22px;background:var(--border);flex-shrink:0;}
.theme-toggle{width:32px;height:32px;border-radius:7px;
  border:1px solid var(--border);background:var(--surface-2);
  color:var(--text-muted);cursor:pointer;
  display:flex;align-items:center;justify-content:center;font-size:.88rem;
  transition:background .15s,color .15s;flex-shrink:0;}
.theme-toggle:hover{background:var(--border);color:var(--text);}
[data-theme="dark"] .theme-toggle{color:#fbbc06;}
@media(max-width:520px){.update-text{display:none;}}
@keyframes spin{to{transform:rotate(360deg);}}

/* ── HISTORY CARD ── */
.history-card{background:var(--surface);border:1px solid var(--border);border-radius:12px;
  overflow:hidden;box-shadow:0 2px 12px rgba(154,128,83,.07);}
.history-hdr{padding:14px 18px;border-bottom:1px solid var(--border);
  display:flex;align-items:center;gap:8px;}
.history-hdr-title{font-size:.875rem;
  font-weight:700;color:var(--text);}
.history-hdr-sub{font-size:.72rem;color:#7a756e;margin-left:auto;color:var(--text-muted);}
.history-item{display:flex;align-items:flex-start;gap:12px;padding:13px 18px;
  border-bottom:1px solid var(--border-2);}
.history-item:last-child{border-bottom:none;}
.h-dot{width:8px;height:8px;border-radius:50%;flex-shrink:0;margin-top:5px;}
.h-text{flex:1;}
.h-event{font-size:.8rem;font-weight:500;color:var(--text);margin-bottom:2px;}
.h-meta{font-size:.72rem;color:var(--text-muted);}
.h-time{font-size:.72rem;color:var(--text-light);white-space:nowrap;margin-left:auto;text-align:right;}

/* ── FOOTER ── */
.pub-footer{text-align:center;padding:20px;font-size:.72rem;color:var(--text-light);}
.pub-footer a{color:#9a8053;}
.pub-footer .footer-brand{
font-size:.78rem;color:var(--text-muted);margin-bottom:4px;}

/* ── COMPLETED OVERLAY ── */
.completed-card{background:linear-gradient(135deg,#14532d,#15803d);
  border-radius:12px;padding:24px;text-align:center;color:#fff;margin-bottom:20px;
  box-shadow:0 4px 20px rgba(21,128,61,.25);}
.completed-icon{width:64px;height:64px;border-radius:50%;background:rgba(255,255,255,.2);
  display:flex;align-items:center;justify-content:center;margin:0 auto 14px;
  font-size:1.8rem;}
.completed-card h3{font-size:1.1rem;
  font-weight:700;margin-bottom:6px;}
.completed-card p{font-size:.8rem;opacity:.9;margin:0;}
.feedback-btn{display:inline-flex;align-items:center;gap:6px;margin-top:14px;
  padding:9px 20px;background:rgba(255,255,255,.2);color:#fff;
  border:1px solid rgba(255,255,255,.35);border-radius:8px;font-size:.82rem;
  font-weight:500;cursor:pointer;text-decoration:none;transition:background .15s;}
.feedback-btn:hover{background:rgba(255,255,255,.3);}
</style>


</head>
<body>

<!-- HEADER -->



@php
  $punchIn    = optional($sr->punch)->punch_in_at;
  $isActive   = $sr->status === 'In Progress' && $punchIn;
  $onSite     = $isActive ? $punchIn->diffForHumans(null, true, false, 2) : null;
  $srRef      = 'SR-'.optional($sr->created_at)->format('Y').'-'.str_pad($sr->id, 5, '0', STR_PAD_LEFT);
  $techName   = optional($sr->assignedUser)->name;
  $domainName = optional($sr->domain)->domain_name;
  $catName    = optional($sr->category)->category_name;
  $techDomain = collect([$catName, $domainName])->filter()->implode(' · ') ?: '-';


  $initials = $techName
      ? Str::of($techName)->explode(' ')->take(2)->map(fn($w) => Str::upper(Str::substr($w, 0, 1)))->implode('')
      : '--';
  
  $siteName = optional($sr->project)->site_name ?: null;
  $siteAddr = optional($sr->project)->site_address ?: null;
@endphp

<header class="pub-header">
  <div class="brand">
    <div class="brand-icon">MM</div>
    <div>
      <div class="brand-name">Matter Mind</div>
      <div class="brand-sub">Service That Matters. Always.</div>
    </div>
  </div>
  <div class="header-right">
    <div class="update-row">
      <span class="update-text">Updated: <strong id="last-updated">just now</strong></span>
      <button class="btn-refresh" id="refresh-btn" onclick="doRefresh()">
        <i class="bi bi-arrow-clockwise"></i>Refresh
      </button>
    </div>
    <div class="hdr-div"></div>
    <button class="theme-toggle" onclick="toggleTheme()" title="Toggle dark mode">
      <i class="bi bi-moon-stars-fill" id="theme-icon"></i>
    </button>
    <div class="live-badge"><div class="live-dot"></div>Live Tracking</div>
  </div>
</header>

<div class="page-wrap">

  <!-- SR CARD -->
  <div class="sr-card" onclick="openDrawer()">
    <div class="sr-card-top">
      <div>
        <div class="sr-id">{{ $srRef }}</div>
        <div class="sr-client">{{ optional($sr->client)->company_name ?? '-' }}</div>
        <div class="sr-site"><i class="bi bi-geo-alt" style="color:#9a8053;font-size:.85rem;"></i>
    {{ $siteName ?? 'Site not specified' }}
      </div>
      </div>
      <div class="status-chip {{ $statusMeta['chip'] }}" id="sr-status-chip">
        <i class="bi {{ $statusMeta['icon'] }}"></i>{{ $statusMeta['label'] }}
      </div>
    </div>
    <div class="sr-meta-row">
      <div class="sr-meta-item">
        <div class="sr-meta-label">SR Reference</div>
        <div class="sr-meta-value gold">{{ $srRef }}</div>
      </div>
      <div class="sr-meta-item">
        <div class="sr-meta-label">Service Type</div>
        <div class="sr-meta-value">
          
          {{ optional($sr->category)->category_name ?? '-' }}
        </div>
      </div>
      <div class="sr-meta-item">
        <div class="sr-meta-label">Logged On</div>
        <div class="sr-meta-value">{{ optional($sr->created_at)->format('d M Y') ?? '-' }}</div>
      </div>
    </div>
    <div class="sr-card-hint"><i class="bi bi-info-circle"></i>Tap for full job details</div>
  </div>

  <!-- ACTIVE STATUS BANNER -->
  @if($isActive)
  <div class="active-banner" id="active-banner">
    <div class="ab-icon"><i class="bi bi-person-walking"></i></div>
    <div>
      <div class="ab-title">Technician is currently on-site and working</div>
      <div class="ab-sub">Work commenced at {{ $punchIn->format('h:i A') }} · Duration on-site:
        <strong id="duration-live" style="color:{{ $statusMeta['color'] }};">{{ $onSite }}</strong>
      </div>
    </div>
  </div>
  @endif

  <!-- TECHNICIAN CARD -->
  @if($sr->assignedUser)
  <div class="tech-card" id="tech-card">
    <div class="tech-avatar">{{ $initials }}</div>
    <div class="tech-info">
      <div class="tech-label">Assigned Technician</div>
      <div class="tech-name">{{ $techName }}</div>
      <div class="tech-eta">
        <i class="bi bi-clock"></i>
        <span id="tech-eta-text">
          @if($punchIn)
            On-site since {{ $punchIn->format('h:i A') }}
          @elseif($sr->eta_at)
            ETA {{ $sr->eta_at->format('d M · h:i A') }}
          @else
            Awaiting ETA
          @endif
        </span>
      </div>
    </div>
    <!-- <div class="tech-actions">
      <a class="btn-maps" href="javascript:void(0)" onclick="showToast('maps')"><i class="bi bi-geo-alt-fill"></i>View Location</a>
    </div> -->
  </div>
  @endif

  <!-- MILESTONES -->
  <div class="milestone-section">
    <div class="ms-section-title"><i class="bi bi-signpost-2" style="color:#9a8053;"></i>Service Request Progress</div>
    <div class="ms-section-sub">Your request moves through each stage as our team works on it. Current stage is highlighted.</div>
    <div class="timeline-h" id="timeline-h"></div>
    <div class="timeline-v" id="timeline-v"></div>
  </div>

  <!-- ACTIVITY HISTORY -->
  <div class="history-card">
    <div class="history-hdr">
      <i class="bi bi-clock-history" style="color:#9a8053;"></i>
      <span class="history-hdr-title">Activity Log</span>
      <span class="history-hdr-sub">All times are UAE (GST)</span>
    </div>
    <div id="history-list"></div>
  </div>

</div>

<!-- DRAWER -->
<div class="drawer-overlay" id="drawer-overlay" onclick="closeDrawer()"></div>
<div class="detail-drawer" id="detail-drawer">
  <div class="drawer-handle"></div>
  <div class="drawer-hdr">
    <div class="drawer-hdr-left">
      <div class="drawer-hdr-icon"><i class="bi bi-file-earmark-text"></i></div>
      <div>
        <div class="drawer-title">Job Details - {{ $srRef }}</div>
        <div class="drawer-sub">{{ optional($sr->client)->company_name ?? '-' }} · {{ $siteName ?? '-' }}</div>
      </div>
    </div>
    <button class="drawer-close" onclick="closeDrawer()"><i class="bi bi-x-lg"></i></button>
  </div>
  <div class="drawer-body">

    <div class="detail-section">
      <div class="detail-section-title"><i class="bi bi-ticket-detailed"></i>Service Request</div>
      <div class="detail-grid">
        <div class="detail-cell"><div class="dc-label">SR Reference</div><div class="dc-value gold">{{ $srRef }}</div></div>
        <div class="detail-cell"><div class="dc-label">Status</div><div class="dc-value" style="color:{{ $statusMeta['color'] }}">{{ $statusMeta['label'] }}</div></div>
        <div class="detail-cell"><div class="dc-label">Scope</div><div class="dc-value">{{ $sr->warranty_scope === 'iw' ? 'In Warranty' : ($sr->warranty_scope === 'oow' ? 'Out of Warranty' : '-') }}</div></div>
        <div class="detail-cell"><div class="dc-label">Service Domain</div><div class="dc-value">{{ optional($sr->category)->category_name ?? '-' }}</div></div>
        <div class="detail-cell"><div class="dc-label">Priority</div><div class="dc-value">{{ $sr->priority_level ?? '-' }}</div></div>
        <div class="detail-cell"><div class="dc-label">Logged On</div><div class="dc-value">{{ optional($sr->created_at)->format('d M Y') ?? '-' }}</div></div>
        <div class="detail-cell full">
          <div class="dc-label">Issue Description</div>
          <div class="dc-value" style="font-weight:400;font-size:.8rem;line-height:1.5;">{{ $sr->issue_description ?: '-' }}</div>
        </div>
      </div>
    </div>

    <div class="detail-section">
      <div class="detail-section-title"><i class="bi bi-buildings"></i>Customer &amp; Site</div>
      <div class="detail-grid">
        <div class="detail-cell"><div class="dc-label">Customer</div><div class="dc-value">{{ optional($sr->client)->company_name ?? '-' }}</div></div>
        <div class="detail-cell"><div class="dc-label">Customer Token</div><div class="dc-value gold">{{ optional($sr->client)->unique_code ?? '-' }}</div></div>
        <div class="detail-cell"><div class="dc-label">Site</div><div class="dc-value">{{ $siteName ?? '-' }}</div></div>
        <div class="detail-cell full">
          <div class="dc-label">Full Address</div>
          <div class="dc-value" style="font-weight:400;font-size:.8rem;">{{ $siteAddr ?? '-' }}</div>
        </div>
      </div>
    </div>

    @if($sr->assignedUser)
    <div class="detail-section">
      <div class="detail-section-title"><i class="bi bi-person-badge"></i>Assigned Technician</div>
      <div class="tech-detail-block">
        <div class="tech-detail-av">{{ $initials }}</div>
        <div>
          <div class="tech-detail-name">{{ $techName }}</div>
          <div class="tech-detail-domain">{{ $techDomain }}</div>
        </div>
        <div class="tech-detail-status">
          <div style="font-size:.72rem;color:var(--text-muted);margin-bottom:3px;">{{ $punchIn ? 'On-site since' : 'ETA' }}</div>
          <div style="font-size:.85rem;font-weight:700;color:#9a8053;">{{ optional($punchIn ?? $sr->eta_at)->format('h:i A') ?? '-' }}</div>
          <div style="font-size:.72rem;color:var(--text-muted);margin-top:2px;" id="drawer-duration">-</div>
        </div>
      </div>
    </div>
    @endif

    <div class="detail-section">
      <div class="detail-section-title"><i class="bi bi-clock-history"></i>Key Timestamps</div>
      <div class="detail-grid">
        @foreach([
          'Logged'          => $sr->created_at,
          'Approved'        => $sr->accepted_at,
          'Assigned'        => $sr->dispatched_at,
          'ETA Confirmed'   => $sr->eta_at,
          'Punch In'        => $punchIn,
          'QC Reviewed'     => $sr->qc_reviewed_at,
          'Est. Completion' => optional($sr->punch)->punch_out_at,
        ] as $lbl => $ts)
        <div class="detail-cell">
          <div class="dc-label">{{ $lbl }}</div>
          <div class="dc-value" @if(!$ts) style="color:var(--text-muted);" @endif>
            {{ $ts ? $ts->format('d M · h:i A') : 'Pending' }}
          </div>
        </div>
        @endforeach
      </div>
    </div>

    <div class="detail-section">
      <div class="detail-section-title"><i class="bi bi-signpost-2"></i>Progress Timeline</div>
      <div class="compact-timeline">
        @foreach($milestones as $m)
        <div class="ct-item">
          <div class="ct-left">
            <div class="ct-node {{ $m['state'] }}">
              <i class="bi {{ $m['state'] === 'done' ? 'bi-check' : $m['icon'] }}"></i>
            </div>
            @if(!$loop->last)
              <div class="ct-line {{ $m['state'] === 'done' ? 'done' : ($m['state'] === 'active' ? 'active' : '') }}"></div>
            @endif
          </div>
          <div class="ct-right">
            <div class="ct-label {{ $m['state'] === 'pending' ? 'pending' : '' }}">{{ $m['label'] }}</div>
            <div class="ct-time {{ $m['state'] === 'active' ? 'gold' : '' }}">
              {{ $m['time'] ?: 'Pending' }}{{ $m['desc'] ? ' - '.$m['desc'] : '' }}
            </div>
          </div>
        </div>
        @endforeach
      </div>
    </div>

  </div>
</div>

<div class="ms-tooltip" id="ms-tooltip"></div>

<footer class="pub-footer">
  <div class="footer-brand">Matter Mind · Service That Matters. Always.</div>
  <div>This page updates automatically. For urgent assistance contact <a href="mailto:support@mattermind.com">support@mattermind.com</a></div>
</footer>

<div id="toast-wrap" style="position:fixed;bottom:22px;left:50%;transform:translateX(-50%);z-index:9999;pointer-events:none;"></div>

<script>
/* ═══ DATA (server-driven) ═══ */
var MILESTONES = @json($milestones);
var HISTORY    = @json($history);
var PUNCH_IN   = @json(optional($punchIn)->toIso8601String());
var SR_ID      = @json($sr->id);
var DATA_URL   = @json(route('clients.job_tracking.data', ['id' => $sr->id]));

/* ═══ HELPERS ═══ */
function esc(v){
  return String(v == null ? '' : v)
    .replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

/* ═══ RENDER HORIZONTAL ═══ */
function renderH(){
  var el = document.getElementById('timeline-h');
  if(!el) return;
  el.innerHTML = MILESTONES.map(function(m, idx){
    var label = esc(m.label).replace(/ /, '<br>');
    var time  = m.time ? esc(m.time) : (m.state === 'pending' ? '' : '-');
    return '<div class="tl-item '+m.state+'">'+
      '<div class="tl-node '+m.state+'" data-idx="'+idx+'" onclick="showMsTip(event, parseInt(this.dataset.idx))">'+
        '<i class="bi '+esc(m.icon)+' node-icon"></i></div>'+
      '<div class="tl-label'+(m.state === 'pending' ? ' pending' : '')+'">'+label+'</div>'+
      '<div class="tl-time'+(m.state === 'active' ? ' active-time' : '')+'">'+time+'</div>'+
    '</div>';
  }).join('');
}

/* ═══ RENDER VERTICAL ═══ */
function renderV(){
  var el = document.getElementById('timeline-v');
  if(!el) return;
  el.innerHTML = MILESTONES.map(function(m, idx){
    var isLast    = idx === MILESTONES.length - 1;
    var lineClass = m.state === 'done' ? 'done' : (m.state === 'active' ? 'active' : '');
    var time      = m.time ? esc(m.time) : (m.state === 'pending' ? 'Pending' : '-');
    return '<div class="tlv-item">'+
      '<div class="tlv-left">'+
        '<div class="tlv-node '+m.state+'" data-idx="'+idx+'" onclick="showMsTip(event, parseInt(this.dataset.idx))">'+
          '<i class="bi '+esc(m.icon)+' node-icon"></i></div>'+
        (!isLast ? '<div class="tlv-line '+lineClass+'"></div>' : '')+
      '</div>'+
      '<div class="tlv-right">'+
        '<div class="tlv-label'+(m.state === 'pending' ? ' pending' : '')+'">'+esc(m.label)+'</div>'+
        '<div class="tlv-time'+(m.state === 'active' ? ' active-time' : '')+'">'+time+'</div>'+
        '<div class="tlv-desc">'+esc(m.desc)+'</div>'+
      '</div>'+
    '</div>';
  }).join('');
}

/* ═══ RENDER HISTORY ═══ */
function renderHistory(){
  var wrap = document.getElementById('history-list');
  if(!wrap) return;
  if(!HISTORY.length){
    wrap.innerHTML = '<div class="history-item"><div class="h-text"><div class="h-meta">No activity recorded yet.</div></div></div>';
    return;
  }
  wrap.innerHTML = HISTORY.map(function(h){
    return '<div class="history-item">'+
      '<div class="h-dot" style="background:'+esc(h.color)+';"></div>'+
      '<div class="h-text"><div class="h-event">'+esc(h.event)+'</div><div class="h-meta">'+esc(h.meta)+'</div></div>'+
      '<div class="h-time">'+esc(h.day)+'<br>'+esc(h.time)+'</div>'+
    '</div>';
  }).join('');
}

/* ═══ LIVE DURATION ═══ */
function pad(n){ return n < 10 ? '0'+n : String(n); }
function fmtDur(fromIso){
  var diff = Math.floor((new Date() - new Date(fromIso)) / 1000);
  if(diff < 0) diff = 0;
  var h = Math.floor(diff/3600), m = Math.floor((diff%3600)/60), s = diff%60;
  return h > 0 ? h+'h '+m+'m' : m+'m '+pad(s)+'s';
}
function updateDuration(){
  if(!PUNCH_IN) return;
  var el = document.getElementById('duration-live');
  if(el) el.textContent = fmtDur(PUNCH_IN);
}
setInterval(function(){ updateDuration(); }, 1000);
updateDuration();

/* ═══ DARK MODE ═══ */
function toggleTheme(){
  var el   = document.documentElement;
  var dark = el.getAttribute('data-theme') === 'dark';
  el.setAttribute('data-theme', dark ? '' : 'dark');
  var icon = document.getElementById('theme-icon');
  if(icon) icon.className = dark ? 'bi bi-moon-stars-fill' : 'bi bi-sun-fill';
}

/* ═══ LAST UPDATED ═══ */
var lastUpdSec = 0;
setInterval(function(){
  lastUpdSec++;
  var lu = document.getElementById('last-updated');
  if(!lu) return;
  lu.textContent = lastUpdSec < 60   ? lastUpdSec+'s ago'
                 : lastUpdSec < 3600 ? Math.floor(lastUpdSec/60)+'m ago'
                 : Math.floor(lastUpdSec/3600)+'h ago';
}, 1000);

/* ═══ REFRESH (live re-fetch) ═══ */
function doRefresh(){
  var btn = document.getElementById('refresh-btn');
  btn.classList.add('spinning');
  btn.disabled = true;

  fetch(DATA_URL, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } })
    .then(function(r){
      if(!r.ok) throw new Error('HTTP '+r.status);
      return r.json();
    })
    .then(function(d){
      MILESTONES = d.milestones;
      HISTORY    = d.history;
      PUNCH_IN   = d.punch_in;
      renderH(); renderV(); renderHistory();

      var chip = document.getElementById('sr-status-chip');
      if(chip){
        chip.className = 'status-chip ' + d.status.chip;
        chip.innerHTML = '<i class="bi '+esc(d.status.icon)+'"></i>'+esc(d.status.label);
      }
      updateDuration();
      lastUpdSec = 0;
      document.getElementById('last-updated').textContent = 'just now';
      showToast('refresh');
    })
    .catch(function(){ showToast('error'); })
    .finally(function(){
      btn.classList.remove('spinning');
      btn.disabled = false;
    });
}

/* ═══ TOAST ═══ */
function showToast(type){
  var msgs = {
    refresh: 'Status refreshed.',
    maps:    'Opening technician location in Maps…',
    error:   'Could not refresh. Please try again.'
  };
  var t = document.createElement('div');
  t.style.cssText = 'background:#1a1614;color:#fff;padding:10px 18px;border-radius:24px;font-size:.8rem;white-space:nowrap;box-shadow:0 4px 16px rgba(0,0,0,.2);animation:toastIn .2s ease;';
  t.textContent = msgs[type] || 'Updated.';
  document.getElementById('toast-wrap').appendChild(t);
  setTimeout(function(){
    t.style.opacity = '0';
    t.style.transition = 'opacity .3s';
    setTimeout(function(){ t.remove(); }, 300);
  }, 2800);
}

/* ═══ DRAWER ═══ */
function openDrawer(){
  document.getElementById('detail-drawer').classList.add('open');
  document.getElementById('drawer-overlay').classList.add('show');
  document.body.style.overflow = 'hidden';
  updateDrawerDuration();
}
function closeDrawer(){
  document.getElementById('detail-drawer').classList.remove('open');
  document.getElementById('drawer-overlay').classList.remove('show');
  document.body.style.overflow = '';
}
function updateDrawerDuration(){
  var el = document.getElementById('drawer-duration');
  if(!el) return;
  el.textContent = PUNCH_IN ? fmtDur(PUNCH_IN)+' on-site' : '-';
}

/* ═══ TOOLTIP ═══ */
var tooltipTimer = null;
function showMsTip(e, idx){
  e.stopPropagation();
  var m = MILESTONES[idx];
  if(!m) return;
  var tip  = document.getElementById('ms-tooltip');
  var time = m.time || (m.state === 'pending' ? 'Not reached yet' : '-');
  tip.innerHTML = '<strong>'+esc(m.label)+'</strong><br>'+esc(m.desc)+
    '<br><span style="opacity:.7;font-size:.68rem;">'+esc(time)+'</span>';
  var rect = e.currentTarget.getBoundingClientRect();
  tip.style.left = Math.min(Math.max(rect.left + rect.width/2, 110), window.innerWidth - 110)+'px';
  tip.style.top  = (rect.top - 8)+'px';
  tip.style.transform = 'translate(-50%,-100%)';
  tip.classList.add('visible');
  clearTimeout(tooltipTimer);
  tooltipTimer = setTimeout(function(){ tip.classList.remove('visible'); }, 2800);
}
document.addEventListener('click', function(){
  var tip = document.getElementById('ms-tooltip');
  if(tip) tip.classList.remove('visible');
});

/* ═══ SWIPE DOWN TO CLOSE ═══ */
(function(){
  var startY = 0;
  document.addEventListener('touchstart', function(e){
    var drawer = document.getElementById('detail-drawer');
    if(drawer && drawer.classList.contains('open')) startY = e.touches[0].clientY;
  });
  document.addEventListener('touchend', function(e){
    var drawer = document.getElementById('detail-drawer');
    if(!drawer || !drawer.classList.contains('open')) return;
    if(e.changedTouches[0].clientY - startY > 80) closeDrawer();
  });
})();

/* ═══ TOAST ANIMATION ═══ */
var s = document.createElement('style');
s.textContent = '@keyframes toastIn{from{opacity:0;transform:translateX(-50%) translateY(10px);}to{opacity:1;transform:translateX(-50%) translateY(0);}}';
document.head.appendChild(s);

/* ═══ INIT ═══ */
renderH();
renderV();
renderHistory();
</script>

</body>
</html>