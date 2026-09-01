@extends('layouts.layout')

@section('title', 'QC Review | Matter Mind')
@section('page_title', 'QC Review')
@section('page_icon', 'database')
@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600;700&display=swap" rel="stylesheet"/>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet"/>
<style>
/* ===== QC Review Terminal — scoped page styles ===== */
.qc-wrap{--gold:#9a8053;--gold-2:#b8975e;--queue-width:300px;--proof-bg:#f0f3f9;--locked-bg:#f7f8fb;}
[data-bs-theme="dark"] .qc-wrap{--proof-bg:#2a2928;--locked-bg:#242220;}
.qc-wrap .pg-header h4,.qc-wrap .modal-hdr h6,.qc-wrap .ws-sr-id,.qc-wrap .queue-item-id,
.qc-wrap .stat-num,.qc-wrap .ws-empty h6,.qc-wrap .proof-card-hdr h6,.qc-wrap .expense-hdr h6,
.qc-wrap .action-hdr h6,.qc-wrap .lightbox-hdr h6,.qc-wrap .confirm-hdr h6{letter-spacing:-.01em;}

/* PAGE HEADER */
.qc-wrap .pg-header{background:linear-gradient(135deg,#9A7B4F 0%,#7A6140 100%);border-radius:10px;padding:20px 24px;margin-bottom:20px;color:#fff;position:relative;overflow:hidden;}
.qc-wrap .pg-header::before{content:'';position:absolute;left:-40px;bottom:-40px;width:180px;height:180px;border-radius:50%;background:rgba(255,255,255,.05);}
.qc-wrap .pg-header::after{content:'';position:absolute;right:-30px;top:-30px;width:160px;height:160px;border-radius:50%;background:rgba(255,255,255,.07);}
.qc-wrap .pg-header h4{font-size:1rem;font-weight:600;margin:0 0 3px;position:relative;z-index:1;}
.qc-wrap .pg-header p{font-size:.78rem;margin:0;opacity:.85;position:relative;z-index:1;}
.qc-wrap .pg-header .meta-row{display:flex;align-items:center;gap:8px;margin-top:9px;position:relative;z-index:1;flex-wrap:wrap;}
.qc-wrap .meta-badge{background:rgba(255,255,255,.18);border:1px solid rgba(255,255,255,.3);border-radius:20px;font-size:.6875rem;padding:2px 10px;font-weight:500;}

/* STATS */
.qc-wrap .stats-strip{display:grid;grid-template-columns:repeat(4,1fr);gap:10px;margin-bottom:18px;}
.qc-wrap .stat-card{background:var(--card-bg);border:1px solid var(--card-border);border-radius:9px;padding:13px 15px;display:flex;align-items:center;gap:12px;box-shadow:var(--card-shadow);}
.qc-wrap .stat-icon{width:38px;height:38px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:1rem;flex-shrink:0;}
.qc-wrap .stat-num{font-size:1.5rem;font-weight:700;line-height:1;color:var(--text-heading);}
.qc-wrap .stat-lbl{font-size:.7rem;color:var(--text-muted);}
@media(max-width:767px){.qc-wrap .stats-strip{grid-template-columns:repeat(2,1fr);}}
@media(max-width:420px){.qc-wrap .stats-strip{grid-template-columns:1fr;}}

/* FILTER BAR */
.qc-wrap .filter-bar{background:var(--card-bg);border:1px solid var(--card-border);border-radius:10px;padding:12px 16px;margin-bottom:16px;box-shadow:var(--card-shadow);display:flex;flex-wrap:wrap;gap:10px;align-items:flex-end;}
.qc-wrap .filter-group{display:flex;flex-direction:column;gap:4px;min-width: 0;flex: 1 1 auto;}
.qc-wrap .filter-label{font-size:.7rem;font-weight:600;color:var(--text-muted);text-transform:uppercase;letter-spacing:.05em;}
.qc-wrap .filter-control{height:34px;padding:0 10px;border:1px solid var(--border-color);border-radius:7px;background:var(--input-bg);color:var(--text-primary);font-size:.8rem;min-width:140px;transition:border-color .15s;}
.qc-wrap .filter-control:focus{outline:none;border-color:var(--gold);box-shadow:0 0 0 3px rgba(154,128,83,.12);}
.qc-wrap .filter-actions{margin-left:auto;display:flex;gap:8px;align-items:flex-end;}
.qc-wrap .btn-ghost-sm{display:inline-flex;align-items:center;justify-content:center;width:34px;height:34px;background:var(--surface-2);color:var(--text-muted);border:1px solid var(--border-color);border-radius:7px;font-size:.9rem;cursor:pointer;}
.qc-wrap .btn-ghost-sm:hover{background:var(--surface-3);}
@media(max-width:575.98px){
  .qc-wrap .filter-group{flex:1 1 100%;}
  .qc-wrap .filter-control{width:100%;min-width:0;}
  .qc-wrap .filter-actions{margin-left:0;width:100%;}
}

/* MAIN WORKSPACE LAYOUT */
.qc-wrap .qc-layout{display:grid;grid-template-columns:var(--queue-width) 1fr;gap:14px;align-items:start;}
@media(max-width:899px){.qc-wrap .qc-layout{grid-template-columns:1fr;}}

/* SR QUEUE (LEFT PANEL) */
.qc-wrap .queue-panel{background:var(--card-bg);border:1px solid var(--card-border);border-radius:10px;box-shadow:var(--card-shadow);overflow:hidden;position:sticky;top:82px;}
@media(max-width:899px){.qc-wrap .queue-panel{position:static;}}
.qc-wrap .queue-hdr{padding:13px 16px;border-bottom:1px solid var(--border-color);display:flex;align-items:center;justify-content:space-between;}
.qc-wrap .queue-hdr-title{font-size:.8rem;font-weight:600;color:var(--text-heading);display:flex;align-items:center;gap:7px;}
.qc-wrap .queue-count{font-size:.7rem;background:rgba(239,68,68,.1);color:#ef4444;padding:2px 8px;border-radius:9px;font-weight:700;}
.qc-wrap .queue-list{max-height:calc(100vh - 300px);overflow-y:auto;}
@media(max-width:899px){.qc-wrap .queue-list{max-height:none;}}
.qc-wrap .queue-list::-webkit-scrollbar{width:3px;}
.qc-wrap .queue-list::-webkit-scrollbar-thumb{background:var(--border-color);border-radius:2px;}
.qc-wrap .queue-item{padding:12px 16px;border-bottom:1px solid var(--border-color);cursor:pointer;transition:background .12s;position:relative;}
.qc-wrap .queue-item:last-child{border-bottom:none;}
.qc-wrap .queue-item:hover{background:var(--surface-2);}
.qc-wrap .queue-item.active{background:rgba(154,128,83,.08);border-left:3px solid var(--gold);}
.qc-wrap .queue-item-id{font-size:.78rem;font-weight:700;color:var(--gold);margin-bottom:3px;}
.qc-wrap .queue-item-client{font-size:.8rem;font-weight:500;color:var(--text-heading);margin-bottom:2px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
.qc-wrap .queue-item-site{font-size:.72rem;color:var(--text-muted);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;margin-bottom:6px;}
.qc-wrap .queue-item-foot{display:flex;align-items:center;justify-content:space-between;}
.qc-wrap .scope-badge{font-size:.65rem;font-weight:700;padding:2px 8px;border-radius:10px;}
.qc-wrap .scope-iw{background:rgba(16,185,129,.1);color:#059669;}
.qc-wrap .scope-oow{background:rgba(239,68,68,.1);color:#ef4444;}
.qc-wrap .queue-timer{font-size:.68rem;color:var(--text-muted);display:flex;align-items:center;gap:3px;}
.qc-wrap .timer-warn{color:#d97706;}
.qc-wrap .sla-bar{height:3px;border-radius:2px;margin-top:7px;background:var(--surface-3);overflow:hidden;}
.qc-wrap .sla-bar-fill{height:100%;border-radius:2px;transition:width .3s;}

.qc-wrap .queue-item-id.sr-ref-trigger,
.qc-wrap .ws-sr-id.sr-ref-trigger{ cursor:pointer; }
.qc-wrap .queue-item-id.sr-ref-trigger:hover,

/* QC WORKSPACE (RIGHT PANEL) */
.qc-wrap .ws-panel{display:flex;flex-direction:column;gap:14px;}
.qc-wrap .ws-empty{background:var(--card-bg);border:1px solid var(--card-border);border-radius:10px;box-shadow:var(--card-shadow);padding:60px 24px;text-align:center;}
.qc-wrap .ws-empty-icon{width:60px;height:60px;border-radius:16px;background:var(--surface-2);display:flex;align-items:center;justify-content:center;margin:0 auto 16px;font-size:1.6rem;color:var(--text-light);}
.qc-wrap .ws-empty h6{font-size:.9rem;font-weight:600;color:var(--text-muted);margin-bottom:5px;}
.qc-wrap .ws-empty p{font-size:.78rem;color:var(--text-light);margin:0;}
.qc-wrap .ws-detail{display:none;}
.qc-wrap .ws-detail.show{display:flex;flex-direction:column;gap:14px;}
.qc-wrap .ws-header-card{background:var(--card-bg);border:1px solid var(--card-border);border-radius:10px;box-shadow:var(--card-shadow);padding:16px 20px;}
.qc-wrap .ws-header-top{display:flex;align-items:flex-start;justify-content:space-between;gap:12px;flex-wrap:wrap;margin-bottom:14px;}
.qc-wrap .ws-sr-id{font-size:1rem;font-weight:700;color:var(--gold);margin-bottom:3px;}
.qc-wrap .ws-client{font-size:.9rem;font-weight:600;color:var(--text-heading);margin-bottom:2px;}
.qc-wrap .ws-site{font-size:.8rem;color:var(--text-muted);}
.qc-wrap .ws-scope-pill{display:inline-flex;align-items:center;gap:5px;padding:4px 12px;border-radius:20px;font-size:.75rem;font-weight:700;}
.qc-wrap .ws-meta-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:10px;}
@media(max-width:700px){.qc-wrap .ws-meta-grid{grid-template-columns:repeat(2,1fr);}}
.qc-wrap .ws-meta-item{background:var(--surface-2);border-radius:7px;padding:9px 12px;}
.qc-wrap .ws-meta-label{font-size:.68rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--text-muted);margin-bottom:3px;}
.qc-wrap .ws-meta-value{font-size:.8rem;font-weight:600;color:var(--text-heading);}
.qc-wrap .ws-meta-value.warn{color:#d97706;}
.qc-wrap .ws-meta-value.breach{color:#ef4444;}

/* PROOF THUMBNAILS */
.qc-wrap .proof-card{background:var(--card-bg);border:1px solid var(--card-border);border-radius:10px;box-shadow:var(--card-shadow);overflow:hidden;}
.qc-wrap .proof-card-hdr{padding:13px 18px;border-bottom:1px solid var(--border-color);display:flex;align-items:center;gap:10px;flex-wrap:wrap;}
.qc-wrap .proof-card-icon{width:30px;height:30px;border-radius:7px;background:rgba(16,185,129,.1);display:flex;align-items:center;justify-content:center;font-size:.85rem;color:#15803d;}
.qc-wrap .proof-card-hdr h6{font-size:.85rem;font-weight:600;color:var(--text-heading);margin:0;}
.qc-wrap .proof-card-hdr .csub{font-size:.72rem;color:var(--text-muted);margin-left:auto;}
.qc-wrap .proof-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:14px;padding:16px 18px;}
@media(max-width:700px){.qc-wrap .proof-grid{grid-template-columns:1fr;}}
.qc-wrap .proof-thumb{border-radius:9px;overflow:hidden;border:2px solid var(--border-color);cursor:pointer;transition:border-color .15s,transform .12s;position:relative;}
.qc-wrap .proof-thumb:hover{border-color:var(--gold);transform:translateY(-2px);}
.qc-wrap .proof-img{width:100%;aspect-ratio:4/3;background:var(--proof-bg);display:flex;flex-direction:column;align-items:center;justify-content:center;gap:8px;position:relative;overflow:hidden;}
.qc-wrap .proof-img.before-photo{background:linear-gradient(145deg,#dce8f5 0%,#c8ddf0 100%);}
.qc-wrap .proof-img.after-photo{background:linear-gradient(145deg,#d5f0e2 0%,#b8e8ce 100%);}
.qc-wrap .proof-img.signature-photo{background:linear-gradient(145deg,#f5f0e8 0%,#ede3d0 100%);}
[data-bs-theme="dark"] .qc-wrap .proof-img.before-photo{background:linear-gradient(145deg,#1a2836,#1e3245);}
[data-bs-theme="dark"] .qc-wrap .proof-img.after-photo{background:linear-gradient(145deg,#1a2e24,#1e3829);}
[data-bs-theme="dark"] .qc-wrap .proof-img.signature-photo{background:linear-gradient(145deg,#2a2820,#302e24);}
.qc-wrap .proof-img-icon{font-size:2rem;opacity:.3;}
.qc-wrap .proof-img-label{font-size:.72rem;font-weight:600;opacity:.5;letter-spacing:.04em;text-transform:uppercase;}
.qc-wrap .proof-hover-overlay{position:absolute;inset:0;background:rgba(154,128,83,.8);display:flex;align-items:center;justify-content:center;opacity:0;transition:opacity .15s;}
.qc-wrap .proof-thumb:hover .proof-hover-overlay{opacity:1;}
.qc-wrap .proof-hover-overlay i{color:#fff;font-size:1.5rem;}
.qc-wrap .proof-thumb-foot{padding:8px 10px;display:flex;align-items:center;justify-content:space-between;background:var(--surface-2);}
.qc-wrap .proof-thumb-name{font-size:.72rem;font-weight:600;color:var(--text-heading);}
.qc-wrap .proof-status-ok{color:#15803d;font-size:.7rem;display:flex;align-items:center;gap:3px;font-weight:600;}

.qc-wrap .proof-count-badge{position:absolute;top:8px;right:8px;background:rgba(0,0,0,.65);color:#fff;font-size:.68rem;font-weight:700;padding:2px 8px;border-radius:12px;z-index:2;}
.qc-wrap .proof-strip{display:flex;gap:6px;flex-wrap:wrap;padding:0 18px 14px;}
.qc-wrap .proof-strip:empty{display:none;}
.qc-wrap .proof-strip-item{width:54px;height:54px;border-radius:7px;overflow:hidden;border:1px solid var(--border-color);cursor:pointer;flex-shrink:0;transition:border-color .15s,transform .12s;}
.qc-wrap .proof-strip-item:hover{border-color:var(--gold);transform:translateY(-2px);}
.qc-wrap .proof-strip-item img{width:100%;height:100%;object-fit:cover;display:block;}
.qc-wrap .proof-strip-label{font-size:.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--text-muted);padding:0 18px 6px;}
.qc-wrap .lb-nav{display:flex;align-items:center;gap:8px;}
.qc-wrap .lb-nav button{background:rgba(154,128,83,.1);border:1px solid rgba(154,128,83,.25);color:#9a8053;border-radius:6px;width:28px;height:28px;cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:.8rem;}
.qc-wrap .lb-nav button:disabled{opacity:.35;cursor:not-allowed;}

/* EXPENSE SUMMARY */
.qc-wrap .expense-card{background:var(--card-bg);border:1px solid var(--card-border);border-radius:10px;box-shadow:var(--card-shadow);overflow:hidden;}
.qc-wrap .expense-hdr{padding:13px 18px;border-bottom:1px solid var(--border-color);display:flex;align-items:center;gap:10px;}
.qc-wrap .expense-hdr-icon{width:30px;height:30px;border-radius:7px;background:rgba(245,158,11,.1);display:flex;align-items:center;justify-content:center;font-size:.85rem;color:#d97706;}
.qc-wrap .expense-hdr h6{font-size:.85rem;font-weight:600;color:var(--text-heading);margin:0;}
.qc-wrap .expense-total-badge{margin-left:auto;font-size:.78rem;font-weight:700;background:rgba(245,158,11,.1);color:#d97706;padding:3px 10px;border-radius:20px;}
.qc-wrap .expense-body{padding:14px 18px;}
.qc-wrap .expense-row{display:flex;align-items:center;justify-content:space-between;padding:8px 0;border-bottom:1px solid var(--border-color);font-size:.8rem;gap:10px;flex-wrap:wrap;}
.qc-wrap .expense-row:last-child{border-bottom:none;}
.qc-wrap .expense-cat{display:flex;align-items:center;gap:8px;color:var(--text-primary);}
.qc-wrap .expense-cat i{color:var(--text-muted);font-size:.9rem;}
.qc-wrap .expense-amt{font-weight:600;color:var(--text-heading);}
.qc-wrap .expense-receipt{font-size:.72rem;color:var(--gold);cursor:pointer;display:flex;align-items:center;gap:3px;text-decoration:underline;text-underline-offset:2px;}

/* QC ACTION PANEL */
.qc-wrap .action-card{background:var(--card-bg);border:1px solid var(--card-border);border-radius:10px;box-shadow:var(--card-shadow);overflow:hidden;}
.qc-wrap .action-hdr{padding:13px 18px;border-bottom:1px solid var(--border-color);display:flex;align-items:center;gap:10px;}
.qc-wrap .action-hdr-icon{width:30px;height:30px;border-radius:7px;background:rgba(37,99,235,.1);display:flex;align-items:center;justify-content:center;font-size:.85rem;color:#2563eb;}
.qc-wrap .action-hdr h6{font-size:.85rem;font-weight:600;color:var(--text-heading);margin:0;}
.qc-wrap .action-body{padding:18px;}

/* QC OWNERSHIP LOCK */
.qc-wrap .qc-lock-note{display:none;align-items:center;gap:10px;padding:12px 14px;border-radius:8px;background:var(--surface-2);border:1px solid var(--border-color);font-size:.8rem;color:var(--text-muted);}
.qc-wrap .qc-lock-note.show{display:flex;}
.qc-wrap .qc-lock-note i{font-size:1rem;color:var(--text-light);flex-shrink:0;}
.qc-wrap .qc-lock-note strong{color:var(--text-heading);}
.qc-wrap .action-btns.hidden,.qc-wrap .rework-wrap.hidden{display:none;}
.qc-wrap .queue-item.locked{opacity:.72;}
.qc-wrap .owner-chip{display:inline-flex;align-items:center;gap:3px;font-size:.62rem;font-weight:700;padding:2px 7px;border-radius:9px;background:var(--surface-3);color:var(--text-muted);}
.qc-wrap .owner-chip.se{background:rgba(37,99,235,.1);color:#2563eb;}
.qc-wrap .owner-chip.hop{background:rgba(154,128,83,.12);color:#9a8053;}

/* REWORK TEXTAREA */
.qc-wrap .rework-wrap{margin-bottom:18px;}
.qc-wrap .rework-label-row{display:flex;align-items:center;justify-content:space-between;margin-bottom:6px;gap:8px;flex-wrap:wrap;}
.qc-wrap .rework-label{font-size:.78rem;font-weight:600;color:var(--text-heading);display:flex;align-items:center;gap:6px;}
.qc-wrap .rework-locked-tag{display:inline-flex;align-items:center;gap:4px;font-size:.68rem;font-weight:600;background:var(--surface-2);color:var(--text-muted);padding:2px 8px;border-radius:5px;border:1px solid var(--border-color);}
.qc-wrap .rework-unlocked-tag{display:none;align-items:center;gap:4px;font-size:.68rem;font-weight:600;background:rgba(239,68,68,.1);color:#ef4444;padding:2px 8px;border-radius:5px;border:1px solid rgba(239,68,68,.25);}
.qc-wrap .rework-textarea{width:100%;min-height:90px;padding:10px 12px;border:1px solid var(--border-color);border-radius:8px;background:var(--locked-bg);color:var(--text-muted);font-size:.8125rem;resize:vertical;transition:all .2s;cursor:not-allowed;opacity:.6;}
.qc-wrap .rework-textarea:not([disabled]){background:var(--input-bg);color:var(--text-primary);cursor:text;opacity:1;border-color:#ef4444;}
.qc-wrap .rework-textarea:not([disabled]):focus{outline:none;border-color:#ef4444;box-shadow:0 0 0 3px rgba(239,68,68,.1);}
.qc-wrap .rework-hint{font-size:.72rem;color:var(--text-muted);margin-top:5px;}
.qc-wrap .rework-hint.active{color:#ef4444;}

/* ACTION BUTTONS */
/* .qc-wrap .action-btns{display:flex;gap:10px;flex-wrap:wrap;} */

.qc-wrap .action-btns{display:flex;gap:10px;flex-wrap:nowrap;}
.qc-wrap .action-btns > button{
  flex:1 1 0;
  min-width:0;
  display:inline-flex;
  align-items:center;
  justify-content:center;
  gap:7px;
  white-space:nowrap;
}
.qc-wrap .btn-qc-pass{flex:1;padding:11px 16px;background:linear-gradient(135deg,#9A7B4F,#7A6140);color:#fff;border:none;border-radius:8px;font-size:.85rem;font-weight:600;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px;transition:opacity .15s;min-width:0;}
.qc-wrap .btn-qc-pass:hover{opacity:.88;}
.qc-wrap .btn-qc-pass:disabled{opacity:.4;cursor:not-allowed;}
.qc-wrap .btn-qc-fail{flex:1;padding:11px 16px;background:var(--surface-2);color:var(--text-muted);border:2px solid var(--border-color);border-radius:8px;font-size:.85rem;font-weight:600;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px;transition:all .15s;min-width:0;}
.qc-wrap .btn-qc-fail:hover{border-color:#ef4444;color:#ef4444;background:rgba(239,68,68,.04);}
.qc-wrap .btn-qc-fail.active{border-color:#ef4444;color:#ef4444;background:rgba(239,68,68,.06);}
.qc-wrap .btn-confirm-rework{display:none;width:100%;padding:11px;background:#ef4444;color:#fff;border:none;border-radius:8px;font-size:.85rem;font-weight:600;cursor:pointer;gap:7px;align-items:center;justify-content:center;transition:background .15s;margin-top:10px;}
.qc-wrap .btn-confirm-rework:hover{background:#dc2626;}
.qc-wrap .btn-confirm-rework.show{display:flex;}

/* SCOPE INDICATOR */
.qc-wrap .scope-indicator{display:flex;align-items:center;gap:8px;padding:10px 14px;border-radius:8px;margin-bottom:16px;font-size:.8rem;}
.qc-wrap .scope-indicator.iw{background:rgba(21,128,61,.07);border:1px solid rgba(21,128,61,.2);}
.qc-wrap .scope-indicator.oow{background:rgba(239,68,68,.07);border:1px solid rgba(239,68,68,.2);}

/* SUCCESS STATE */
.qc-wrap .ws-success{display:none;background:var(--card-bg);border:1px solid var(--card-border);border-radius:10px;box-shadow:var(--card-shadow);padding:48px 24px;text-align:center;}
.qc-wrap .ws-success.show{display:block;}
.qc-wrap .success-icon{width:64px;height:64px;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;font-size:1.8rem;}

/* BADGES */
.qc-wrap .sbadge{display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:20px;font-size:.7rem;font-weight:600;white-space:nowrap;}
.qc-wrap .sb-review{background:rgba(245,158,11,.12);color:#d97706;}

/* MODALS */
.qc-modal-overlay{display:none;position:fixed;inset:0;background:var(--overlay-bg,rgba(9,15,35,.62));z-index:900;align-items:center;justify-content:center;backdrop-filter:blur(4px);padding:16px;}
.qc-modal-overlay.show{display:flex;}
.qc-modal-overlay .lightbox-box{background:var(--modal-bg,#fff);border-radius:12px;max-width:700px;width:100%;box-shadow:var(--modal-shadow,0 24px 64px rgba(0,0,0,.16));border:1px solid var(--card-border);overflow:hidden;animation:qcMIn .18s ease;}
@keyframes qcMIn{from{opacity:0;transform:scale(.96);}to{opacity:1;transform:scale(1);}}
.qc-modal-overlay .lightbox-hdr{display:flex;align-items:center;justify-content:space-between;padding:14px 18px;border-bottom:1px solid var(--border-color);}
.qc-modal-overlay .lightbox-hdr h6{font-size:.875rem;font-weight:600;color:var(--text-heading);margin:0;}
.qc-modal-overlay .lb-close{background:none;border:none;color:var(--text-muted);cursor:pointer;padding:4px;border-radius:5px;font-size:1rem;line-height:1;}
.qc-modal-overlay .lb-close:hover{background:var(--surface-2);}
.qc-modal-overlay .lightbox-img{width:100%;aspect-ratio:16/9;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:12px;font-size:.85rem;color:var(--text-muted);}
.qc-modal-overlay .lightbox-foot{padding:12px 18px;border-top:1px solid var(--border-color);display:flex;align-items:center;justify-content:space-between;font-size:.78rem;color:var(--text-muted);background:var(--surface-2);gap:10px;flex-wrap:wrap;}
.qc-modal-overlay .confirm-box{background:var(--modal-bg,#fff);border-radius:12px;max-width:440px;width:100%;box-shadow:var(--modal-shadow,0 24px 64px rgba(0,0,0,.16));border:1px solid var(--card-border);overflow:hidden;animation:qcMIn .18s ease;}
.qc-modal-overlay .confirm-hdr{padding:18px 20px;border-bottom:1px solid var(--border-color);}
.qc-modal-overlay .confirm-hdr h6{font-size:.9rem;font-weight:600;color:var(--text-heading);margin:0;}
.qc-modal-overlay .confirm-body{padding:20px;}
.qc-wrap .branch-route,.qc-modal-overlay .branch-route{display:flex;align-items:center;gap:12px;padding:12px 14px;border-radius:8px;border:1px solid;}
.qc-modal-overlay .branch-route.iw-route{background:rgba(154,128,83,.07);border-color:rgba(154,128,83,.25);}
.qc-modal-overlay .branch-route.oow-route{background:rgba(154,128,83,.07);border-color:rgba(154,128,83,.2);}
.qc-modal-overlay .branch-route.iw-route .branch-route-icon{background:rgba(154,128,83,.12);color:#9a8053;}
.qc-modal-overlay .branch-route.oow-route .branch-route-icon{background:rgba(154,128,83,.12);color:#9a8053;}
.qc-modal-overlay .branch-route-icon{width:36px;height:36px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:1rem;flex-shrink:0;}
.qc-modal-overlay .branch-route-label{font-size:.78rem;color:var(--text-muted);margin-bottom:3px;}
.qc-modal-overlay .branch-route-action{font-size:.85rem;font-weight:600;color:var(--text-heading);}
.qc-modal-overlay .confirm-foot{display:flex;gap:10px;padding:14px 20px;border-top:1px solid var(--border-color);}
.qc-modal-overlay .btn-confirm-pass{flex:1;padding:9px;background:linear-gradient(135deg,#9A7B4F,#7A6140);color:#fff;border:none;border-radius:7px;font-size:.82rem;font-weight:600;cursor:pointer;}
.qc-modal-overlay .btn-cancel-modal{flex:1;padding:9px;background:var(--surface-2);color:var(--text-muted);border:1px solid var(--border-color);border-radius:7px;font-size:.82rem;cursor:pointer;}

/* TOAST */
.qc-toast-wrap{position:fixed;bottom:22px;right:22px;z-index:9999;display:flex;flex-direction:column;gap:8px;pointer-events:none;max-width:calc(100vw - 44px);}
.qc-toast-wrap .toast-item{display:flex;align-items:flex-start;gap:10px;padding:12px 15px;border-radius:9px;background:var(--card-bg);border:1px solid var(--card-border);box-shadow:0 6px 24px rgba(0,0,0,.14);min-width:240px;max-width:320px;animation:qcToastIn .2s ease;pointer-events:auto;}
@keyframes qcToastIn{from{opacity:0;transform:translateY(10px);}to{opacity:1;transform:translateY(0);}}
.qc-toast-wrap .t-ico{font-size:1rem;flex-shrink:0;margin-top:1px;}
.qc-toast-wrap .t-ico.ok{color:#15803d;}.qc-toast-wrap .t-ico.err{color:#ef4444;}
.qc-toast-wrap .t-ico.info{color:var(--gold);}.qc-toast-wrap .t-ico.warn{color:#d97706;}
.qc-toast-wrap .t-title{font-size:.8rem;font-weight:600;color:var(--text-heading);margin:0 0 2px;}
.qc-toast-wrap .t-body{font-size:.75rem;color:var(--text-muted);margin:0;}
@media(max-width:575.98px){.qc-toast-wrap{left:12px;right:12px;bottom:12px;}.qc-toast-wrap .toast-item{max-width:none;}}


.btn-qc-realloc {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  padding: 12px 16px;
  /* border: 1px solid var(--border); */
  border-radius: 8px;
  background: transparent;
  border: 1px solid #d4d0c8;
  color: #6b6862;
  font-size: .85rem;
  font-weight: 500;
  cursor: pointer;
  transition: background .15s, border-color .15s;
}

.btn-qc-realloc:hover {
  /* background: var(--surface-2);
  border-color: var(--border-strong); */

   background: #f5f3ee;
  border-color: #b8b4aa;
  color: #4a4843;
}

.btn-qc-realloc i {
  font-size: 1rem;
}



.realloc-select {
  width: 100%;
  height: 42px;
  padding: 0 12px;
  border: 1px solid #e2e0da;
  border-radius: 8px;
  background: #fff;
  color: #3d3d3a;
  font-size: .85rem;
  font-family: inherit;
  cursor: pointer;
  appearance: none;
  -webkit-appearance: none;
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%236b6862' stroke-width='2'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E");
  background-repeat: no-repeat;
  background-position: right 12px center;
}

.realloc-select:disabled {
  background-color: #f5f3ee;
  color: #9c9a92;
  cursor: not-allowed;
}

.realloc-select:focus {
  outline: none;
  border-color: #b8b4aa;
}



.fail-mode-row{display:flex;gap:8px;margin-bottom:12px;}
.fail-mode-btn{
  flex:1;display:inline-flex;align-items:center;justify-content:center;gap:6px;
  padding:9px 10px;border:1px solid #e5e7eb;border-radius:8px;background:#fff;
  color:var(--text-muted);font-size:.78rem;font-weight:500;cursor:pointer;transition:.15s;
}
.fail-mode-btn:hover{border-color:#9a8053;color:#9a8053;}
.fail-mode-btn.active{background:rgba(154,128,83,.1);border-color:#9a8053;color:#9a8053;font-weight:600;}


.qc-lock-note      { display: none; }
.qc-lock-note.show { display: flex; }
</style>
@endpush

@section('content')
<div class="qc-wrap">

  {{-- PAGE HEADER --}}
  <div class="pg-header">
    <h4><i class="bi bi-patch-check me-2"></i>QC Review Terminal</h4>
    <p>Supervisor quality control workspace. Review field proof submissions side-by-side and authorise closure or return to rework.</p>
    <div class="meta-row">
      <span class="meta-badge"><i class="bi bi-shield-fill-check me-1"></i>Super Admin</span>
      <span class="meta-badge"><i class="bi bi-person-gear me-1"></i>Admin</span>
      <span class="meta-badge"><i class="bi bi-person-workspace me-1"></i>Head of Projects</span>
      <span class="meta-badge"><i class="bi bi-tools me-1"></i>Service Engineer (QC-enabled)</span>
    </div>
  </div>

  {{-- STATS --}}
  <div class="stats-strip">
    <div class="stat-card">
      <div class="stat-icon" style="background:rgba(245,158,11,.1);"><i class="bi bi-hourglass-split" style="color:#d97706;"></i></div>
      <div><div class="stat-num" id="stat-pending">0</div><div class="stat-lbl">Pending QC Review</div></div>
    </div>
    <div class="stat-card">
      <div class="stat-icon" style="background:rgba(21,128,61,.1);"><i class="bi bi-check-circle" style="color:#15803d;"></i></div>
      <div><div class="stat-num">{{ $passedToday ?? 0 }}</div><div class="stat-lbl">Passed Today</div></div>
    </div>
    <div class="stat-card">
      <div class="stat-icon" style="background:rgba(239,68,68,.1);"><i class="bi bi-arrow-clockwise" style="color:#ef4444;"></i></div>
      <div><div class="stat-num">{{ $returnedRework ?? 0 }}</div><div class="stat-lbl">Returned to Rework</div></div>
    </div>
    <div class="stat-card">
      <div class="stat-icon" style="background:rgba(37,99,235,.1);"><i class="bi bi-speedometer2" style="color:#2563eb;"></i></div>
      <div><div class="stat-num">{{ $avgReviewTime ?? 0 }}<span style="font-size:.85rem;">m</span></div><div class="stat-lbl">Avg. Review Time</div></div>
    </div>
  </div>

  {{-- FILTER BAR --}}
  <div class="filter-bar">
    <div class="filter-group">
      <div class="filter-label">Search</div>
      <input class="filter-control" type="text" id="qc-search" placeholder="SR ID, client, technician…" oninput="qcFilterQueue(this.value)"/>
    </div>
    <div class="filter-group">
      <div class="filter-label">Scope</div>
      <select class="filter-control" id="qc-scope" onchange="qcFilterQueue(document.getElementById('qc-search').value)">
        <option value="">All Scopes</option>
        <option>In Warranty</option>
        <option>Out of Warranty</option>
      </select>
    </div>
    <div class="filter-group">
      <div class="filter-label">Technician</div>
      <select class="filter-control" id="qc-tech" onchange="qcFilterQueue(document.getElementById('qc-search').value)">
        <option value="">All Technicians</option>
      </select>
    </div>
    <div class="filter-actions">
      <button class="btn-ghost-sm" onclick="showToast('info','Refreshed','Queue updated.')"><i class="bi bi-arrow-clockwise"></i></button>
    </div>
  </div>

  {{-- QC LAYOUT --}}
  <div class="qc-layout">

    {{-- LEFT: SR QUEUE --}}
    <div class="queue-panel">
      <div class="queue-hdr">
        <div class="queue-hdr-title"><i class="bi bi-list-ul" style="color:#9a8053;"></i>Pending Review</div>
        <span class="queue-count" id="queue-count">0</span>
      </div>
      <div class="queue-list" id="queue-list"></div>
    </div>

    {{-- RIGHT: QC WORKSPACE --}}
    <div class="ws-panel" id="ws-panel">

      {{-- Empty state --}}
      <div class="ws-empty" id="ws-empty">
        <div class="ws-empty-icon"><i class="bi bi-patch-check"></i></div>
        <h6>Select a Ticket to Review</h6>
        <p>Choose a service request from the queue on the left to begin the QC verification process.</p>
      </div>

      {{-- Detail workspace --}}
      <div class="ws-detail" id="ws-detail">

        {{-- SR Header Card --}}
        <div class="ws-header-card">
          <div class="ws-header-top">
            <div>
              <div class="ws-sr-id" id="ws-sr-id">—</div>
              <div class="ws-client" id="ws-client">—</div>
              <div class="ws-site" id="ws-site">—</div>
            </div>
            <div style="display:flex;flex-direction:column;align-items:flex-end;gap:8px;">
              <span class="sbadge sb-review"><i class="bi bi-circle-fill" style="font-size:.4rem;"></i>Pending Review</span>
              <div class="ws-scope-pill" id="ws-scope-pill"></div>
            </div>
          </div>
          <div class="ws-meta-grid">
            <div class="ws-meta-item"><div class="ws-meta-label">Technician</div><div class="ws-meta-value" id="ws-tech">—</div></div>
            <div class="ws-meta-item"><div class="ws-meta-label">Punch In</div><div class="ws-meta-value" id="ws-punchin">—</div></div>
            <div class="ws-meta-item"><div class="ws-meta-label">Punch Out</div><div class="ws-meta-value" id="ws-punchout">—</div></div>
            <div class="ws-meta-item"><div class="ws-meta-label">SLA Elapsed</div><div class="ws-meta-value" id="ws-sla">—</div></div>
          </div>
        </div>

        {{-- Proof Thumbnails --}}
        <div class="proof-card">
          <div class="proof-card-hdr">
            <div class="proof-card-icon"><i class="bi bi-images"></i></div>
            <h6>Deliverable Verification Frame</h6>
            <span class="csub">Click any image to enlarge</span>
          </div>
          <div class="proof-grid">
              <div class="proof-thumb" onclick="openLightbox('before')">
                <div class="proof-img before-photo" id="proof-before">
                  <span class="proof-count-badge hidden" id="proof-before-count"></span>
                  <i class="bi bi-camera proof-img-icon"></i>
                  <span class="proof-img-label">Before Work</span>
                  <div class="proof-hover-overlay"><i class="bi bi-zoom-in"></i></div>
                </div>
                <div class="proof-thumb-foot">
                  <span class="proof-thumb-name">Start Photos</span>
                  <span class="proof-status-ok" id="proof-before-status"><i class="bi bi-check-circle-fill"></i>Uploaded</span>
                </div>
              </div>
              <div class="proof-thumb" onclick="openLightbox('after')">
                <div class="proof-img after-photo" id="proof-after">
                  <i class="bi bi-camera-fill proof-img-icon"></i>
                  <span class="proof-img-label">After Work</span>
                  <div class="proof-hover-overlay"><i class="bi bi-zoom-in"></i></div>
                </div>
                <div class="proof-thumb-foot">
                  <span class="proof-thumb-name">Finish Photo</span>
                  <span class="proof-status-ok" id="proof-after-status"><i class="bi bi-check-circle-fill"></i>Uploaded</span>
                </div>
              </div>
              <div class="proof-thumb" onclick="openLightbox('signature')">
                <div class="proof-img signature-photo" id="proof-signature">
                  <i class="bi bi-pen proof-img-icon"></i>
                  <span class="proof-img-label">Customer Sign-off</span>
                  <div class="proof-hover-overlay"><i class="bi bi-zoom-in"></i></div>
                </div>
                <div class="proof-thumb-foot">
                  <span class="proof-thumb-name">Signature</span>
                  <span class="proof-status-ok" id="proof-signature-status"><i class="bi bi-check-circle-fill"></i>Uploaded</span>
                </div>
              </div>
            </div>
          <div style="margin:0 18px 14px;padding:9px 13px;background:rgba(21,128,61,.07);border:1px solid rgba(21,128,61,.2);border-radius:7px;display:flex;align-items:center;gap:8px;font-size:.78rem;color:#15803d;">
            <i class="bi bi-shield-fill-check"></i>
            All 3 required documents uploaded — Before photo, After photo, and Signed customer acceptance form.
          </div>
          <div class="proof-strip-label" id="before-strip-label" style="display:none;">All Before Photos</div>
          <div class="proof-strip" id="before-strip"></div>

          <div class="proof-strip-label" id="after-strip-label" style="display:none;">All After Photos</div>
          <div class="proof-strip" id="after-strip"></div>
        </div>

        {{-- Expense Summary --}}
        <div class="expense-card">
          <div class="expense-hdr">
            <div class="expense-hdr-icon"><i class="bi bi-receipt"></i></div>
            <h6>Field Expense Summary</h6>
            <span class="expense-total-badge" id="ws-expense-total">AED 0</span>
          </div>
          <div class="expense-body" id="ws-expense-body"></div>
        </div>

        {{-- QC Action Panel --}}
       <div class="action-card">
  <div class="action-hdr">
    <div class="action-hdr-icon"><i class="bi bi-clipboard2-check"></i></div>
    <h6>QC Decision</h6>
  </div>
  <div class="action-body">
    <div class="scope-indicator" id="scope-indicator"></div>

    <!-- <div class="qc-lock-note" id="qc-lock-note">
      <i class="bi bi-lock-fill"></i>
      <span>QC on this ticket is allocated to <strong id="qc-owner-name">—</strong>. You can review the evidence here, but only they can pass or return it.</span>
    </div> -->

    {{-- Two main buttons only --}}
    <!-- <div class="action-btns" id="action-btns">
      <button class="btn-qc-fail" id="btn-fail" onclick="initiateFail()">
        <i class="bi bi-arrow-counterclockwise"></i>QC Fail — Return to Rework
      </button>
      <button class="btn-qc-pass" id="btn-pass" onclick="initiatePass()">
        <i class="bi bi-patch-check-fill"></i>QC Pass — Authorize Closeout
      </button>
    </div> -->



    <div class="qc-lock-note" id="qc-lock-note">
  <i class="bi bi-lock-fill"></i>
  <span>QC on this ticket is allocated to <strong id="qc-owner-name">—</strong>. You can review the evidence here, but only they can pass or return it.</span>
</div>

{{-- Two main buttons only --}}
<div class="action-btns" id="action-btns" style="display:none;">
  <button class="btn-qc-fail" id="btn-fail" onclick="initiateFail()">
    <i class="bi bi-arrow-counterclockwise"></i>QC Fail — Return to Rework
  </button>
  <button class="btn-qc-pass" id="btn-pass" onclick="initiatePass()">
    <i class="bi bi-patch-check-fill"></i>QC Pass — Authorize Closeout
  </button>
</div>

    {{-- Opens ONLY after QC Fail is clicked --}}
    <div id="fail-wrap" style="display:none;margin-top:12px;">

      <div class="fail-mode-row">
        <button type="button" class="fail-mode-btn active" data-mode="rework" onclick="setFailAction('rework')">
          <i class="bi bi-pencil-square"></i>Send back to technician
        </button>
        <button type="button" class="fail-mode-btn" data-mode="realloc" onclick="setFailAction('realloc')">
          <i class="bi bi-arrow-left-right"></i>Reallocate to ML
        </button>
      </div>

      {{-- MODE A: rework --}}
      <div class="rework-wrap" id="rework-wrap">
        <div class="rework-label-row">
          <div class="rework-label"><i class="bi bi-pencil-square"></i>Mandatory Rework Requirements</div>
          <span class="rework-unlocked-tag" id="rework-unlocked-tag" style="display:flex;">
            <i class="bi bi-unlock-fill"></i> Required
          </span>
        </div>
        <textarea class="rework-textarea" id="rework-textarea"
          placeholder="Describe the rework requirements clearly — this will be sent directly to the technician's mobile view…"></textarea>
        <div class="rework-hint active" id="rework-hint">
          Required — SLA timers and historical timestamps will be preserved for the rework cycle.
        </div>
      </div>

      {{-- MODE B: reallocate --}}
      {{-- MODE B: reallocate --}}
<div class="realloc-wrap" id="realloc-wrap" style="display:none;">
  <div class="rework-label">
    <i class="bi bi-person-badge"></i>Assign to ML
    <span id="realloc-cat-name" style="font-weight:500;color:var(--text-muted);margin-left:4px;"></span>
  </div>
  <select id="realloc-ml" class="realloc-select" disabled>
    <option value="">Loading MLs…</option>
  </select>
  <div class="rework-hint" id="realloc-ml-hint">Number in brackets = jobs currently open with that ML (Accepted, In Progress, Rescheduled, Hold).</div>

  <textarea class="rework-textarea" id="realloc-remark" style="margin-top:10px;"
    placeholder="Reason for reallocation…"></textarea>
</div>

      {{-- One shared confirm button --}}
      <button class="btn-confirm-rework show" id="btn-confirm-fail" onclick="confirmFail()">
        <i class="bi bi-send-fill"></i><span id="btn-confirm-fail-text">Confirm — Send Back to Technician</span>
      </button>

      <div id="cancel-fail-wrap" style="margin-top:8px;text-align:center;">
        <button onclick="cancelFail()" style="background:none;border:none;font-size:.78rem;color:var(--text-muted);cursor:pointer;text-decoration:underline;text-underline-offset:2px;">
          Cancel — keep current assessment
        </button>
      </div>
    </div>

  </div>
</div>

      </div>{{-- /ws-detail --}}

      {{-- Success state --}}
      <div class="ws-success" id="ws-success">
        <div class="success-icon" id="success-icon"></div>
        <h5 id="success-title" style="font-size:1rem;font-weight:600;color:var(--text-heading);margin-bottom:6px;"></h5>
        <p id="success-body" style="font-size:.82rem;color:var(--text-muted);margin-bottom:18px;"></p>
        <button onclick="nextTicket()" style="display:inline-flex;align-items:center;gap:7px;padding:9px 20px;background:linear-gradient(135deg,#9a8053,#b8975e);color:#fff;border:none;border-radius:8px;font-size:.82rem;font-weight:500;cursor:pointer;">
          <i class="bi bi-arrow-right"></i>Review Next Ticket
        </button>
      </div>

    </div>{{-- /ws-panel --}}
  </div>{{-- /qc-layout --}}

  {{-- LIGHTBOX MODAL --}}
  <div class="qc-modal-overlay" id="lightbox-modal" onclick="if(event.target===this)closeLightbox()">
    <div class="lightbox-box">
      <div class="lightbox-hdr">
        <h6 id="lb-title">Before Photo</h6>
        <button class="lb-close" onclick="closeLightbox()"><i class="bi bi-x-lg"></i></button>
      </div>
      <div class="lightbox-img" id="lb-img"></div>
      <div class="lightbox-foot">
        <span id="lb-filename">—</span>
        <div style="display:flex;align-items:center;gap:10px;">
          <div class="lb-nav" id="lb-nav" style="display:none;">
            <button id="lb-prev" onclick="lbStep(-1)" title="Previous"><i class="bi bi-chevron-left"></i></button>
            <button id="lb-next" onclick="lbStep(1)" title="Next"><i class="bi bi-chevron-right"></i></button>
          </div>
          <button onclick="downloadProof()" style="display:inline-flex;align-items:center;gap:5px;padding:5px 12px;background:rgba(154,128,83,.1);color:#9a8053;border:1px solid rgba(154,128,83,.25);border-radius:6px;font-size:.75rem;font-weight:500;cursor:pointer;">
            <i class="bi bi-download"></i>Download
          </button>
        </div>
      </div>
    </div>
  </div>

  {{-- QC PASS CONFIRM MODAL --}}
  <div class="qc-modal-overlay" id="pass-modal" onclick="if(event.target===this)closePassModal()">
    <div class="confirm-box">
      <div class="confirm-hdr">
      <h6><i class="bi bi-patch-check-fill me-2" style="color:#9a8053;"></i>Confirm QC Authorisation</h6>
      </div>
      <div class="confirm-body">
        <p style="font-size:.8rem;color:var(--text-muted);margin-bottom:14px;">You are about to authorise QC pass for <strong id="pass-sr-id" style="color:var(--text-heading);"></strong>. Based on the warranty scope, this will trigger the following action:</p>
        <div id="branch-route-display"></div>
        <p style="font-size:.75rem;color:var(--text-muted);margin-top:12px;margin-bottom:0;"><i class="bi bi-whatsapp" style="color:#25d366;"></i> A WhatsApp notification will be dispatched to the client stakeholders upon confirmation.</p>
      </div>
      <div class="confirm-foot">
        <button class="btn-cancel-modal" onclick="closePassModal()">Cancel</button>
        <button class="btn-confirm-pass" onclick="executePass()"><i class="bi bi-check-lg"></i> Confirm &amp; Authorise</button>
      </div>
    </div>
  </div>

  <div class="qc-toast-wrap" id="toastWrap"></div>
</div>
@include('partials.sr_tracking_modal')
@endsection

@push('scripts')
<script>
/* =========================================================
   QC Review Terminal — page scripts
   Row shape:
   { id, dbId, client, site, tech, scope('iw'|'oow'), scopeLabel,
     punchIn, punchOut, sla:{label, cls, fill, color}, slaFill, slaColor,
     expenses:[{cat, icon, amt, receipt}], totalExpense, proof:{before[],after[],signature} }
   ========================================================= */
const QUEUE = @json($queue ?? []);

let QC_FILTERED = QUEUE.slice();   // what's currently rendered
let selectedSR  = null;
let failMode    = false;

/* ---------- RENDER QUEUE ---------- */
function renderQueue(list){
  list = list || QC_FILTERED;
  const ul = document.getElementById('queue-list');
  document.getElementById('queue-count').textContent = list.length;
  const sp = document.getElementById('stat-pending');
  if(sp) sp.textContent = QUEUE.length;          // KPI = total, not filtered

  if(!list.length){
    ul.innerHTML=`<div style="padding:28px 16px;text-align:center;font-size:.8rem;color:var(--text-muted);">
      <i class="bi bi-inbox" style="display:block;font-size:1.6rem;margin-bottom:8px;color:var(--text-light);"></i>
      No tickets match filters</div>`;
    return;
  }
  ul.innerHTML = list.map(sr=>{
    const sla      = sr.sla || {label:'—', cls:'', fill:0, color:'#9ca3af'};
    const active   = selectedSR && selectedSR.id === sr.id ? 'active' : '';
    const timerCls = (sla.cls === 'warn' || sla.cls === 'breach') ? 'timer-warn' : '';
    const scopeCls = sr.scope === 'iw' ? 'scope-iw' : 'scope-oow';
    const lockCls  = sr.canAct ? '' : 'locked';
const ownerCls = sr.qcOwnerType === 'hop' ? 'hop' : 'se';
const ownerTag = sr.canAct ? '' :
  `<span class="owner-chip ${ownerCls}" title="QC owner: ${sr.qcOwner}"><i class="bi bi-lock-fill" style="font-size:.6rem;"></i>${sr.qcOwner}</span>`;

return `<div class="queue-item ${active} ${lockCls}" id="qi-${sr.id}" onclick="selectSR('${sr.id}')">
  <div class="queue-item-id sr-ref-trigger" data-sr-id="${sr.dbId}" onclick="event.stopPropagation(); openSrTracking(${sr.dbId});">${sr.id}</div>
  <div class="queue-item-client">${sr.client}</div>
  <div class="queue-item-site"><i class="bi bi-geo-alt" style="font-size:.7rem;"></i> ${sr.site}</div>
  <div class="queue-item-foot">
    <span class="scope-badge ${scopeCls}">${sr.scopeLabel}</span>
    <span class="queue-timer ${timerCls}"><i class="bi bi-clock" style="font-size:.65rem;"></i>${sla.label}</span>
  </div>
  ${ownerTag ? `<div style="margin-top:6px;">${ownerTag}</div>` : ''}
  <div class="sla-bar"><div class="sla-bar-fill" style="width:${sr.slaFill||0}%;background:${sr.slaColor||'#9ca3af'};"></div></div>
</div>`;
  }).join('');
}

/* ---------- PROOF HELPERS (module scope, not nested) ---------- */
function setProofSet(type, urls){
  urls = Array.isArray(urls) ? urls : (urls ? [urls] : []);

  const el    = document.getElementById(`proof-${type}`);
  const st    = document.getElementById(`proof-${type}-status`);
  const badge = document.getElementById(`proof-${type}-count`);
  const strip = document.getElementById(`${type}-strip`);
  const label = document.getElementById(`${type}-strip-label`);
  if(!el || !st) return;
  const icon  = el.querySelector('.proof-img-icon');

  if(urls.length){
    el.style.backgroundImage    = `url('${urls[0]}')`;
    el.style.backgroundSize     = 'cover';
    el.style.backgroundPosition = 'center';
    if(icon) icon.style.display = 'none';
    st.innerHTML   = `<i class="bi bi-check-circle-fill"></i>${urls.length} uploaded`;
    st.style.color = '#15803d';
  } else {
    el.style.backgroundImage = 'none';
    if(icon) icon.style.display = '';
    st.innerHTML   = '<i class="bi bi-x-circle"></i>Missing';
    st.style.color = '#ef4444';
  }

  if(badge){
    if(urls.length > 1){ badge.textContent = `1 / ${urls.length}`; badge.classList.remove('hidden'); }
    else { badge.classList.add('hidden'); }
  }

  if(strip && label){
    if(urls.length > 1){
      label.style.display = '';
      strip.innerHTML = urls.map((u, i) => `
        <div class="proof-strip-item" onclick="openLightbox('${type}', ${i})">
          <img src="${u}" alt="${type} photo ${i + 1}">
        </div>`).join('');
    } else {
      label.style.display = 'none';
      strip.innerHTML = '';
    }
  }
}

function setProofPdf(elId, statusId, url){
  const el = document.getElementById(elId);
  const st = document.getElementById(statusId);
  if(!el || !st) return;
  const icon = el.querySelector('.proof-img-icon');
  el.style.backgroundImage = 'none';
  if(url){
    if(icon){ icon.className='bi bi-file-earmark-pdf-fill proof-img-icon'; icon.style.display=''; icon.style.opacity='.55'; icon.style.color='#c0392b'; }
    st.innerHTML = '<i class="bi bi-check-circle-fill"></i>Uploaded';
    st.style.color = '#15803d';
  } else {
    if(icon){ icon.className='bi bi-image proof-img-icon'; icon.style.display=''; icon.style.opacity=''; icon.style.color=''; }
    st.innerHTML = '<i class="bi bi-x-circle"></i>Missing';
    st.style.color = '#ef4444';
  }
}

/* ---------- SELECT SR ---------- */
// function applyQcPermission(sr){
//   const canAct = !!sr.canAct;
//   document.getElementById('qc-lock-note').classList.toggle('show', !canAct);
//   document.getElementById('action-btns').style.display = canAct ? '' : 'none';
//   document.getElementById('fail-wrap').style.display   = 'none';
//   if(!canAct){
//     document.getElementById('qc-owner-name').textContent = sr.qcOwner || 'another reviewer';
//   }
// }

function applyQcPermission(sr){
  const canAct = !!sr.canAct;
  document.getElementById('qc-lock-note').classList.toggle('show', !canAct);
  document.getElementById('action-btns').style.display = canAct ? '' : 'none';
  document.getElementById('fail-wrap').style.display   = 'none';
  document.getElementById('qc-owner-name').textContent = sr.qcOwner || 'another reviewer';
}

function selectSR(id){
  selectedSR = QUEUE.find(s=>s.id===id);
  if(!selectedSR) return;
  failMode = false;
  resetFailMode();

  document.querySelectorAll('.qc-wrap .queue-item').forEach(el=>el.classList.remove('active'));
  document.getElementById(`qi-${id}`)?.classList.add('active');

  document.getElementById('ws-empty').style.display='none';
  document.getElementById('ws-success').classList.remove('show');
  document.getElementById('ws-detail').classList.add('show');

  const wsIdEl = document.getElementById('ws-sr-id');
  wsIdEl.textContent = selectedSR.id;
  wsIdEl.classList.add('sr-ref-trigger');
  wsIdEl.onclick = function(){ openSrTracking(selectedSR.dbId); };
  document.getElementById('ws-client').textContent   = selectedSR.client;
  document.getElementById('ws-site').innerHTML       = `<i class="bi bi-geo-alt" style="color:#9a8053;font-size:.8rem;"></i> ${selectedSR.site}`;
  document.getElementById('ws-tech').textContent     = selectedSR.tech;
  document.getElementById('ws-punchin').textContent  = selectedSR.punchIn;
  document.getElementById('ws-punchout').textContent = selectedSR.punchOut;

  const sla   = selectedSR.sla || {label:'—', cls:''};
  const slaEl = document.getElementById('ws-sla');
  slaEl.textContent = sla.label;
  slaEl.className   = 'ws-meta-value ' + (sla.cls || '');

  const iw = selectedSR.scope === 'iw';
  const sp = document.getElementById('ws-scope-pill');
  sp.style.background = iw ? 'rgba(21,128,61,.12)' : 'rgba(239,68,68,.1)';
  sp.style.color      = iw ? '#15803d' : '#ef4444';
  sp.innerHTML        = `<i class="bi bi-shield-${iw ? 'check' : 'exclamation'}"></i> `
                      + (selectedSR.scopeLabel || (iw ? 'In Warranty' : 'Out of Warranty'));

  const si = document.getElementById('scope-indicator');
  if(iw){
    si.className='scope-indicator iw';
    si.innerHTML=`<i class="bi bi-arrow-right-circle-fill" style="color:#15803d;flex-shrink:0;"></i>
      <span style="font-size:.8rem;"><strong>In-Warranty path:</strong> QC Pass will set status to <strong>Completed</strong> and trigger client WhatsApp summary.</span>`;
  } else {
    si.className='scope-indicator oow';
    si.innerHTML=`<i class="bi bi-arrow-right-circle-fill" style="color:#2563eb;flex-shrink:0;"></i>
      <span style="font-size:.8rem;"><strong>Out-of-Warranty path:</strong> QC Pass will forward this SR to <strong>Invoice Panel</strong> for invoice upload before final closure.</span>`;
  }

  /* ---- Proof ---- */
  const proof = selectedSR.proof || {};
  setProofSet('before', proof.before);
  setProofSet('after',  proof.after);
  setProofPdf('proof-signature', 'proof-signature-status', proof.signature);

  /* ---- Expenses ---- */
  const expBody = document.getElementById('ws-expense-body');
  document.getElementById('ws-expense-total').textContent = selectedSR.totalExpense || 'AED 0';

  const raw = selectedSR.expenses;
  const expenses = Array.isArray(raw) ? raw : (raw && typeof raw === 'object' ? Object.values(raw) : []);

  if(!expenses.length){
    expBody.innerHTML=`<div style="padding:10px 0;text-align:center;font-size:.8rem;color:var(--text-muted);">
      <i class="bi bi-receipt" style="display:block;font-size:1.4rem;color:var(--text-light);margin-bottom:6px;"></i>
      No field expenses logged for this SR.</div>`;
  } else {
    expBody.innerHTML = expenses.map(e=>{
      const amt = (typeof e.amt === 'number')
        ? 'AED ' + e.amt.toLocaleString()
        : (String(e.amt || '').trim() || 'AED 0');
      return `<div class="expense-row">
        <div class="expense-cat"><i class="bi ${e.icon || 'bi-receipt'}"></i>${e.cat || '—'}</div>
        <div style="display:flex;align-items:center;gap:12px;">
          <span class="expense-amt">${amt}</span>
          ${e.receipt
            ? `<span class="expense-receipt" onclick="showToast('info','Receipt','Opening receipt image…')"><i class="bi bi-image"></i>View Receipt</span>`
            : `<span style="font-size:.72rem;color:var(--text-light);">No receipt</span>`}
        </div>
      </div>`;
    }).join('');
  }
  applyQcPermission(selectedSR);
}

/* ---------- QC PASS FLOW ---------- */
function initiatePass(){
  if(!selectedSR) return;
  if(!selectedSR.canAct){
    showToast('err','Not Permitted',`QC on ${selectedSR.id} is allocated to ${selectedSR.qcOwner}.`);
    return;
  }
  document.getElementById('pass-sr-id').textContent = selectedSR.id;
  const brd = document.getElementById('branch-route-display');
  if(selectedSR.scope==='iw'){
    brd.innerHTML=`<div class="branch-route iw-route">
      <div class="branch-route-icon"><i class="bi bi-check-circle-fill"></i></div>
      <div><div class="branch-route-label">In-Warranty — Next action</div>
      <div class="branch-route-action">→ Status: Completed + WhatsApp summary to client</div></div></div>`;
  } else {
    brd.innerHTML=`<div class="branch-route oow-route">
      <div class="branch-route-icon"><i class="bi bi-receipt"></i></div>
      <div><div class="branch-route-label">Out-of-Warranty — Next action</div>
      <div class="branch-route-action">→ Forwarded to Invoice Panel for invoice upload</div></div></div>`;
  }
  document.getElementById('pass-modal').classList.add('show');
}
function closePassModal(){document.getElementById('pass-modal').classList.remove('show');}

function executePass(){
  closePassModal();
  const sr = selectedSR;
  if(!sr) return;
  fetch(`/qc-review/${sr.dbId}/pass`, {
    method:'POST',
    headers:{'X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content,'Accept':'application/json'}
  })
  .then(r=>r.json())
  .then(res=>{
    if(!res.success){ showToast('err','Failed', res.message || 'Could not authorise.'); return; }
    dropFromQueue(sr.id);
    document.getElementById('ws-detail').classList.remove('show');
    const icon  = document.getElementById('success-icon');
    const title = document.getElementById('success-title');
    const body  = document.getElementById('success-body');
    if(sr.scope==='iw'){
      icon.style.background='rgba(21,128,61,.12)';
      icon.innerHTML='<i class="bi bi-check-circle-fill" style="color:#15803d;font-size:1.8rem;"></i>';
      title.textContent=`${sr.id} — QC Passed & Completed`;
      body.textContent='Status updated to Completed. WhatsApp summary dispatched to client stakeholders.';
      showToast('ok','QC Authorised',`${sr.id} closed successfully.`);
    } else {
      icon.style.background='rgba(37,99,235,.1)';
      icon.innerHTML='<i class="bi bi-receipt" style="color:#2563eb;font-size:1.8rem;"></i>';
      title.textContent=`${sr.id} — Forwarded to Invoice Panel`;
      body.textContent='Status updated to Pending Invoice. Accounts team notified to upload the invoice.';
      showToast('ok','Forwarded to Accounts',`${sr.id} sent to Invoice Panel.`);
    }
    document.getElementById('ws-success').classList.add('show');
    selectedSR=null;
  })
  .catch(()=>showToast('err','Network Error','Could not reach the server.'));
}

/* ---------- QC FAIL / REWORK FLOW ---------- */

// function initiateFail(){
//   if(!selectedSR) return;
//   if(!selectedSR.canAct){
//     showToast('err','Not Permitted',`QC on ${selectedSR.id} is allocated to ${selectedSR.qcOwner}.`);
//     return;
//   }
//   failMode = true;
//   const ta = document.getElementById('rework-textarea');
//   ta.disabled = false;
//   ta.style.borderColor = '';
//   ta.placeholder = 'Describe the rework requirements clearly — this will be sent directly to the technician\'s mobile view…';
//   ta.focus();
//   document.getElementById('rework-locked-tag').style.display='none';
//   document.getElementById('rework-unlocked-tag').style.display='flex';
//   const hint = document.getElementById('rework-hint');
//   hint.textContent = 'Required — SLA timers and historical timestamps will be preserved for the rework cycle.';
//   hint.classList.add('active');
//   document.getElementById('btn-fail').classList.add('active');
//   document.getElementById('btn-pass').disabled = true;
//   document.getElementById('btn-pass').style.opacity='.3';
//   document.getElementById('btn-confirm-rework').classList.add('show');
//   document.getElementById('cancel-fail-wrap').style.display='block';
// }


// function cancelFail(){failMode=false;resetFailMode();}
// function resetFailMode(){
//   const ta = document.getElementById('rework-textarea');
//   ta.disabled = true;
//   ta.value = '';
//   ta.style.borderColor = '';
//   ta.placeholder = 'This field is locked. Click \'QC Fail — Return to Rework\' to activate…';
//   document.getElementById('rework-locked-tag').style.display='flex';
//   document.getElementById('rework-unlocked-tag').style.display='none';
//   const hint = document.getElementById('rework-hint');
//   hint.textContent = 'This field unlocks only when initiating a rework rejection. SLA timers will be preserved.';
//   hint.classList.remove('active');
//   document.getElementById('btn-fail').classList.remove('active');
//   document.getElementById('btn-pass').disabled = false;
//   document.getElementById('btn-pass').style.opacity='1';
//   document.getElementById('btn-confirm-rework').classList.remove('show');
//   document.getElementById('cancel-fail-wrap').style.display='none';
// }






/* ---------- QC FAIL FLOW (rework OR reallocate) ---------- */
let failAction = 'rework';

function initiateFail(){
  if(!selectedSR) return;
  if(!selectedSR.canAct){
    showToast('err','Not Permitted',`QC on ${selectedSR.id} is allocated to ${selectedSR.qcOwner}.`);
    return;
  }
  failMode = true;
  document.getElementById('action-btns').style.display = 'none';
  document.getElementById('fail-wrap').style.display   = 'block';
  setFailAction('rework');
  document.getElementById('rework-textarea').focus();
}

function setFailAction(mode){
  failAction = mode;
  document.querySelectorAll('.fail-mode-btn').forEach(b=>{
    b.classList.toggle('active', b.dataset.mode === mode);
  });
  const isRework = mode === 'rework';
  document.getElementById('rework-wrap').style.display  = isRework ? 'block' : 'none';
  document.getElementById('realloc-wrap').style.display = isRework ? 'none'  : 'block';
  document.getElementById('btn-confirm-fail-text').textContent =
    isRework ? 'Confirm — Send Back to Technician' : 'Confirm Reallocation to ML';

  if(!isRework && selectedSR){
    document.getElementById('realloc-cat-name').textContent =
      selectedSR.categoryName ? `(${selectedSR.categoryName})` : '';
    loadMls(selectedSR.categoryId);
  }
}

function confirmFail(){
  if(failAction === 'rework') confirmRework();
  else 
  confirmRealloc();
}

function cancelFail(){ resetFailMode(); }

function resetFailMode(){
  failMode = false;
  failAction = 'rework';

  const fw = document.getElementById('fail-wrap');
  if(fw) fw.style.display = 'none';

  const ab = document.getElementById('action-btns');
  if(ab && selectedSR && selectedSR.canAct) ab.style.display = '';

  const ta = document.getElementById('rework-textarea');
  if(ta){ ta.value=''; ta.style.borderColor=''; }
  const rr = document.getElementById('realloc-remark');   if(rr) rr.value='';
  const rc = document.getElementById('realloc-category'); if(rc) rc.value='';
  const rm = document.getElementById('realloc-ml');
  if(rm){ rm.innerHTML='<option value="">Select a category first…</option>'; rm.disabled=true; }

  const bp = document.getElementById('btn-pass');
  if(bp){ bp.disabled=false; bp.style.opacity='1'; }
}

function confirmRework(){
  const ta = document.getElementById('rework-textarea');
  if(!ta.value.trim()){
    ta.style.borderColor='#ef4444'; ta.focus();
    showToast('err','Required Field','Please enter the mandatory rework requirements before proceeding.');
    return;
  }
  const sr = selectedSR;
  if(!sr) return;
  fetch(`/qc-review/${sr.dbId}/fail`, {
    method:'POST',
    headers:{
      'X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content,
      'Accept':'application/json','Content-Type':'application/json'
    },
    body:JSON.stringify({rework_notes:ta.value.trim()})
  })
  .then(r=>r.json())
  .then(res=>{
    if(!res.success){ showToast('err','Failed', res.message || 'Could not return to rework.'); return; }
    dropFromQueue(sr.id);
    document.getElementById('ws-detail').classList.remove('show');
    document.getElementById('success-icon').style.background='rgba(239,68,68,.08)';
    document.getElementById('success-icon').innerHTML='<i class="bi bi-arrow-counterclockwise" style="color:#ef4444;font-size:1.8rem;"></i>';
    document.getElementById('success-title').textContent=`${sr.id} — Returned to Rework`;
    document.getElementById('success-body').textContent='Status reverted to Rework. Technician has been notified on their mobile with your requirements. SLA timestamps preserved.';
    document.getElementById('ws-success').classList.add('show');
    showToast('warn','Rework Initiated',`${sr.id} sent back to ${sr.tech}.`);
    selectedSR=null; failMode=false;
  })
  .catch(()=>showToast('err','Network Error','Could not reach the server.'));
}

/* ---------- QUEUE MUTATION ---------- */
function dropFromQueue(id){
  const i = QUEUE.findIndex(s=>s.id===id);
  if(i>-1) QUEUE.splice(i,1);
  qcFilterQueue();          // re-applies the active filter instead of resetting it
}

/* ---------- NEXT TICKET ---------- */

// function nextTicket(){
//   document.getElementById('ws-success').classList.remove('show');
//   if(QC_FILTERED.length){
//     selectSR(QC_FILTERED[0].id);
//   } else {
//     document.getElementById('ws-empty').style.display='';
//     showToast('ok','Queue Clear','All pending QC tickets have been reviewed.');
//   }
//   const next = QC_FILTERED.find(s => s.canAct) || QC_FILTERED[0];
// if(next) selectSR(next.id);
// }


function nextTicket(){
  document.getElementById('ws-success').classList.remove('show');

  const next = QC_FILTERED.find(s => s.canAct) || QC_FILTERED[0];
  if(next){
    selectSR(next.id);
  } else {
    document.getElementById('ws-empty').style.display = '';
    showToast('ok','Queue Clear','All pending QC tickets have been reviewed.');
  }
}

/* ---------- FILTER QUEUE ---------- */
function qcFilterQueue(q){
  const searchEl = document.getElementById('qc-search');
  if(q === undefined) q = searchEl ? searchEl.value : '';
  q = (q || '').trim().toLowerCase();

  const scopeEl = document.getElementById('qc-scope');
  const techEl  = document.getElementById('qc-tech');
  const scope = scopeEl ? scopeEl.value : '';
  const tech  = techEl  ? techEl.value  : '';

  QC_FILTERED = QUEUE.filter(s=>{
    const hay = [s.id, s.client, s.tech, s.site].filter(Boolean).join(' ').toLowerCase();
    const mq = !q || hay.includes(q);
    const ms = !scope || s.scopeLabel === scope;
    const mt = !tech  || s.tech === tech;
    return mq && ms && mt;
  });
  renderQueue(QC_FILTERED);
}

/* ---------- LIGHTBOX ---------- */
let lbCurrentUrl = null;
let lbList       = [];
let lbIndex      = 0;
let lbTitleBase  = '';
let lbIsPdf      = false;

function openLightbox(type, index){
  const proof = (selectedSR && selectedSR.proof) || {};
  lbIsPdf = false;

  if(type === 'before'){
    lbList = Array.isArray(proof.before) ? proof.before : (proof.before ? [proof.before] : []);
    lbTitleBase = 'Start Photo (Punch In)';
  } else if(type === 'after'){
    lbList = Array.isArray(proof.after) ? proof.after : (proof.after ? [proof.after] : []);
    lbTitleBase = 'Finish Photo (Punch Out)';
  } else {
    lbList = proof.signature ? [proof.signature] : [];
    lbTitleBase = 'Customer Acceptance (Signed PDF)';
    lbIsPdf = true;
  }

  lbIndex = Number.isInteger(index) ? index : 0;
  lbRender();
  document.getElementById('lightbox-modal').classList.add('show');
}

function lbRender(){
  const url = lbList[lbIndex] || null;
  lbCurrentUrl = url;

  const suffix = lbList.length > 1 ? ` — ${lbIndex + 1} of ${lbList.length}` : '';
  document.getElementById('lb-title').textContent    = lbTitleBase + suffix;
  document.getElementById('lb-filename').textContent = url ? url.split('/').pop() : 'No file';

  const box = document.getElementById('lb-img');
  if(url && lbIsPdf){
    box.style.background = '#525659';
    box.innerHTML = `<iframe src="${url}#toolbar=1" style="width:100%;height:70vh;border:none;border-radius:6px;" title="${lbTitleBase}"></iframe>`;
  } else if(url){
    box.style.background = '#fff';
    box.innerHTML = `<img src="${url}" style="max-width:100%;max-height:70vh;object-fit:contain;" alt="${lbTitleBase}">`;
  } else {
    box.style.background = 'var(--surface-2)';
    box.innerHTML = `<i class="bi bi-image" style="font-size:3rem;opacity:.3;"></i><div style="opacity:.5;">No file uploaded</div>`;
  }

  const nav = document.getElementById('lb-nav');
  if(lbList.length > 1){
    nav.style.display = 'flex';
    document.getElementById('lb-prev').disabled = lbIndex === 0;
    document.getElementById('lb-next').disabled = lbIndex === lbList.length - 1;
  } else {
    nav.style.display = 'none';
  }
}

function lbStep(delta){
  const next = lbIndex + delta;
  if(next < 0 || next >= lbList.length) return;
  lbIndex = next;
  lbRender();
}
function closeLightbox(){document.getElementById('lightbox-modal').classList.remove('show');}

function downloadProof(){
  if(!lbCurrentUrl){ showToast('err','Nothing to download','No file is loaded.'); return; }
  const a = document.createElement('a');
  a.href = lbCurrentUrl;
  a.download = lbCurrentUrl.split('/').pop();
  a.target = '_blank';
  document.body.appendChild(a);
  a.click();
  a.remove();
}

/* ---------- TOAST ---------- */
function showToast(type,title,body){
  const w=document.getElementById('toastWrap');
  const icons={ok:'bi-check-circle-fill',err:'bi-x-circle-fill',info:'bi-info-circle-fill',warn:'bi-exclamation-triangle-fill'};
  const t=document.createElement('div');t.className='toast-item';
  t.innerHTML=`<i class="bi ${icons[type]||icons.info} t-ico ${type}"></i><div><p class="t-title">${title}</p><p class="t-body">${body}</p></div>`;
  w.appendChild(t);
  setTimeout(()=>{t.style.transition='opacity .3s';t.style.opacity='0';setTimeout(()=>t.remove(),300);},3800);
}

/* ---------- INIT ---------- */
function qcPopulateTechFilter(){
  const sel = document.getElementById('qc-tech');
  if(!sel) return;
  [...new Set(QUEUE.map(s=>s.tech))].forEach(t=>{
    const o=document.createElement('option');o.value=t;o.textContent=t;sel.appendChild(o);
  });
}

document.addEventListener('DOMContentLoaded', function(){
  qcPopulateTechFilter();
  ['qc-search','qc-scope','qc-tech'].forEach(id=>{
    const el = document.getElementById(id);
    if(!el) return;
    el.addEventListener(el.tagName === 'SELECT' ? 'change' : 'input', ()=>qcFilterQueue());
  });
  renderQueue(QC_FILTERED);
});




// Reallocatopn

// function initiateRealloc() {
//   document.getElementById('realloc-wrap').style.display = 'block';
//   document.getElementById('action-btns').style.display = 'none';
// }


function initiateRealloc(){
  if(!selectedSR) return;
  if(!selectedSR.canAct){
    showToast('err','Not Permitted',`QC on ${selectedSR.id} is allocated to ${selectedSR.qcOwner}.`);
    return;
  }
  document.getElementById('realloc-wrap').style.display = 'block';
  document.getElementById('action-btns').style.display = 'none';
}


/* ---------- REALLOCATION ---------- */

async function loadMls(categoryId){
  const sel = document.getElementById('realloc-ml');
  sel.innerHTML = '<option value="">Loading…</option>';
  sel.disabled  = true;

  if(!categoryId){
    sel.innerHTML = '<option value="">No category set on this SR</option>';
    return;
  }

  try{
    const res   = await fetch(`/mls-by-category/${categoryId}`, {headers:{'Accept':'application/json'}});
    const users = await res.json();

    if(!users.length){
      sel.innerHTML = '<option value="">No ML mapped to this category</option>';
      return;
    }

    sel.innerHTML = '<option value="">Select ML…</option>' + users.map(u => {
      const n = Number(u.active_count) || 0;
      return `<option value="${u.id}">${u.name}${n > 0 ? ` — ${n} open` : ''}</option>`;
    }).join('');
    sel.disabled = false;
  }catch(e){
    sel.innerHTML = '<option value="">Could not load</option>';
  }
}

function confirmRealloc(){
  const sr = selectedSR;
  if(!sr) return;

  const mlId   = document.getElementById('realloc-ml').value;
  const remark = document.getElementById('realloc-remark').value.trim();

  if(!mlId){
    showToast('err','Required Field','Select an ML before confirming.');
    return;
  }

  const btn = document.getElementById('btn-confirm-fail');
  if(btn.disabled) return;
  btn.disabled = true;
  const original = btn.innerHTML;
  btn.innerHTML = '<i class="bi bi-hourglass-split"></i>Saving…';

  fetch('/qc/reallocate', {
    method:'POST',
    headers:{
      'X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content,
      'Accept':'application/json','Content-Type':'application/json'
    },
    body:JSON.stringify({sr_id: sr.dbId, ml_id: mlId, remark: remark})
  })
  .then(r=>r.json())
  .then(res=>{
    if(!res.ok){ showToast('err','Failed', res.message || 'Could not reallocate.'); return; }
    dropFromQueue(sr.id);
    document.getElementById('ws-detail').classList.remove('show');
    document.getElementById('success-icon').style.background='rgba(37,99,235,.1)';
    document.getElementById('success-icon').innerHTML='<i class="bi bi-arrow-left-right" style="color:#2563eb;font-size:1.8rem;"></i>';
    document.getElementById('success-title').textContent=`${sr.id} — Reallocated`;
    document.getElementById('success-body').textContent='The job has been moved to the selected ML.';
    document.getElementById('ws-success').classList.add('show');
    showToast('ok','Reallocated',`${sr.id} moved to a new ML.`);
    selectedSR = null;
    resetFailMode();
  })
  .catch(()=>showToast('err','Network Error','Could not reach the server.'))
  .finally(()=>{ btn.disabled = false; btn.innerHTML = original; });
}



</script>
@endpush