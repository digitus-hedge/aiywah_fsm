@extends('layouts.layout')

@section('title', 'Invoice Panel | Matter Mind')
@section('page_title', 'Invoice Panel')
@section('page_icon', 'database')
@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600;700&display=swap" rel="stylesheet"/>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet"/>
<style>
/* ===== Invoice Panel - scoped page styles ===== */
.inv-wrap{--gold:#9a8053;--gold-2:#b8975e;--queue-width:290px;}
.inv-wrap h4,.inv-wrap h5,.inv-wrap h6,.inv-wrap .pg-hdr-title,.inv-wrap .card-title,
.inv-wrap .ws-sr-id,.inv-wrap .qi-id,.inv-wrap .stat-num,.inv-wrap .pa-card-title{letter-spacing:-.01em;}

/* PAGE HEADER */
.inv-wrap .pg-header{background:linear-gradient(135deg,#9A7B4F 0%,#7A6140 100%);border-radius:10px;padding:20px 24px;margin-bottom:20px;color:#fff;position:relative;overflow:hidden;}
.inv-wrap .pg-header::before{content:'';position:absolute;left:-40px;bottom:-40px;width:180px;height:180px;border-radius:50%;background:rgba(255,255,255,.05);}
.inv-wrap .pg-header::after{content:'';position:absolute;right:-30px;top:-30px;width:160px;height:160px;border-radius:50%;background:rgba(255,255,255,.07);}
.inv-wrap .pg-hdr-title{font-size:1rem;font-weight:600;margin:0 0 3px;position:relative;z-index:1;}
.inv-wrap .pg-hdr-desc{font-size:.78rem;margin:0;opacity:.85;position:relative;z-index:1;}
.inv-wrap .pg-hdr-meta{display:flex;align-items:center;gap:8px;margin-top:9px;position:relative;z-index:1;flex-wrap:wrap;}
.inv-wrap .meta-badge{background:rgba(255,255,255,.18);border:1px solid rgba(255,255,255,.3);border-radius:20px;font-size:.6875rem;padding:2px 10px;font-weight:500;}

.inv-wrap .qi-id.sr-ref-trigger,
.inv-wrap .ws-sr-id.sr-ref-trigger,
.inv-wrap td.mono .sr-ref-trigger{ cursor:pointer; }
/* STATS */
.inv-wrap .stats-strip{display:grid;grid-template-columns:repeat(4,1fr);gap:10px;margin-bottom:18px;}
.inv-wrap .stat-card{background:var(--card-bg);border:1px solid var(--card-border);border-radius:9px;padding:13px 15px;display:flex;align-items:center;gap:12px;box-shadow:var(--card-shadow);}
.inv-wrap .stat-icon{width:38px;height:38px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:1rem;flex-shrink:0;}
.inv-wrap .stat-num{font-size:1.4rem;font-weight:700;line-height:1;color:var(--text-heading);}
.inv-wrap .stat-lbl{font-size:.7rem;color:var(--text-muted);}
@media(max-width:767px){.inv-wrap .stats-strip{grid-template-columns:repeat(2,1fr);}}
@media(max-width:420px){.inv-wrap .stats-strip{grid-template-columns:1fr;}}

/* FILTER BAR */
.inv-wrap .filter-bar{background:var(--card-bg);border:1px solid var(--card-border);border-radius:10px;padding:12px 16px;margin-bottom:16px;box-shadow:var(--card-shadow);display:flex;flex-wrap:wrap;gap:10px;align-items:flex-end;}
.inv-wrap .filter-group{display:flex;flex-direction:column;gap:4px;min-width: 0;
    flex: 1 1 auto;}
.inv-wrap .filter-label{font-size:.7rem;font-weight:600;color:var(--text-muted);text-transform:uppercase;letter-spacing:.05em;}
.inv-wrap .filter-control{height:34px;padding:0 10px;border:1px solid var(--border-color);border-radius:7px;background:var(--input-bg);color:var(--text-primary);font-size:.8rem;min-width:140px;transition:border-color .15s;}
.inv-wrap .filter-control:focus{outline:none;border-color:var(--gold);box-shadow:0 0 0 3px rgba(154,128,83,.12);}
.inv-wrap .filter-actions{margin-left:auto;display:flex;gap:8px;align-items:flex-end;}
@media(max-width:575.98px){
  .inv-wrap .filter-group{flex:1 1 100%;}
  .inv-wrap .filter-control{width:100%;min-width:0;}
  .inv-wrap .filter-actions{margin-left:0;width:100%;}
  .inv-wrap .filter-actions .btn-ghost{flex:1;justify-content:center;}
}

/* TWO-PANEL */
.inv-wrap .two-panel{display:grid;grid-template-columns:var(--queue-width) 1fr;gap:14px;align-items:start;}
@media(max-width:899px){.inv-wrap .two-panel{grid-template-columns:1fr;}}

/* QUEUE */
.inv-wrap .queue-panel{background:var(--card-bg);border:1px solid var(--card-border);border-radius:10px;box-shadow:var(--card-shadow);overflow:hidden;position:sticky;top:80px;}
@media(max-width:899px){.inv-wrap .queue-panel{position:static;}}
.inv-wrap .queue-hdr{padding:12px 16px;border-bottom:1px solid var(--border-color);display:flex;align-items:center;justify-content:space-between;}
.inv-wrap .queue-hdr-title{font-size:.8rem;font-weight:600;color:var(--text-heading);display:flex;align-items:center;gap:7px;}
.inv-wrap .q-count{font-size:.7rem;background:rgba(154,128,83,.12);color:var(--gold);padding:2px 8px;border-radius:9px;font-weight:700;}
.inv-wrap .queue-list{max-height:calc(100vh - 320px);overflow-y:auto;}
@media(max-width:899px){.inv-wrap .queue-list{max-height:none;}}
.inv-wrap .queue-list::-webkit-scrollbar{width:3px;}
.inv-wrap .queue-list::-webkit-scrollbar-thumb{background:var(--border-color);border-radius:2px;}
.inv-wrap .queue-item{padding:11px 15px;border-bottom:1px solid var(--border-color);cursor:pointer;transition:background .12s;}
.inv-wrap .queue-item:last-child{border-bottom:none;}
.inv-wrap .queue-item:hover{background:var(--surface-2);}
.inv-wrap .queue-item.active{background:rgba(154,128,83,.07);border-left:3px solid var(--gold);}
.inv-wrap .qi-id{font-size:.78rem;font-weight:700;color:var(--gold);margin-bottom:2px;}
.inv-wrap .qi-client{font-size:.79rem;font-weight:500;color:var(--text-heading);margin-bottom:1px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
.inv-wrap .qi-sub{font-size:.71rem;color:var(--text-muted);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;margin-bottom:5px;}
.inv-wrap .qi-foot{display:flex;align-items:center;justify-content:space-between;}
.inv-wrap .qi-time{font-size:.68rem;color:var(--text-muted);display:flex;align-items:center;gap:3px;}

/* WORKSPACE */
.inv-wrap .ws-panel{display:flex;flex-direction:column;gap:14px;}
.inv-wrap .ws-empty{background:var(--card-bg);border:1px solid var(--card-border);border-radius:10px;box-shadow:var(--card-shadow);padding:60px 24px;text-align:center;}
.inv-wrap .ws-empty-icon{width:56px;height:56px;border-radius:14px;background:var(--surface-2);display:flex;align-items:center;justify-content:center;margin:0 auto 14px;font-size:1.5rem;color:var(--text-light);}
.inv-wrap .ws-empty h6{font-size:.9rem;font-weight:600;color:var(--text-muted);margin-bottom:4px;}
.inv-wrap .ws-empty p{font-size:.78rem;color:var(--text-light);margin:0;}
.inv-wrap .ws-card{background:var(--card-bg);border:1px solid var(--card-border);border-radius:10px;box-shadow:var(--card-shadow);overflow:hidden;}
.inv-wrap .ws-card-hdr{padding:13px 18px;border-bottom:1px solid var(--border-color);display:flex;align-items:center;gap:10px;}
.inv-wrap .ws-card-icon{width:30px;height:30px;border-radius:7px;display:flex;align-items:center;justify-content:center;font-size:.85rem;flex-shrink:0;}
.inv-wrap .ws-card-hdr h6{font-size:.85rem;font-weight:600;color:var(--text-heading);margin:0;}
.inv-wrap .ws-card-body{padding:18px;}

/* SR header in workspace */
.inv-wrap .sr-hdr-card{background:var(--card-bg);border:1px solid var(--card-border);border-radius:10px;box-shadow:var(--card-shadow);padding:16px 20px;}
.inv-wrap .ws-sr-id{font-size:1rem;font-weight:700;color:var(--gold);margin-bottom:2px;}
.inv-wrap .ws-sr-client{font-size:.9rem;font-weight:600;color:var(--text-heading);margin-bottom:2px;}
.inv-wrap .ws-sr-site{font-size:.8rem;color:var(--text-muted);}
.inv-wrap .meta-chips{display:flex;gap:8px;flex-wrap:wrap;margin-top:12px;}
.inv-wrap .meta-chip{background:var(--surface-2);border-radius:7px;padding:7px 12px;}
.inv-wrap .meta-chip-label{font-size:.67rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--text-muted);margin-bottom:2px;}
.inv-wrap .meta-chip-value{font-size:.79rem;font-weight:600;color:var(--text-heading);}

/* FORM */
.inv-wrap .form-label-sm{font-size:.78rem;font-weight:600;color:var(--text-heading);margin-bottom:5px;display:block;}
.inv-wrap .form-label-sm .req{color:#ef4444;margin-left:2px;}
.inv-wrap .form-label-sm .hint{font-size:.7rem;color:var(--text-muted);font-weight:400;margin-left:6px;}
.inv-wrap .form-group{margin-bottom:14px;}
.inv-wrap .form-group:last-child{margin-bottom:0;}
.inv-wrap .fc{width:100%;padding:8px 11px;border:1px solid var(--border-color);border-radius:7px;background:var(--input-bg);color:var(--text-primary);font-size:.8125rem;transition:border-color .15s,box-shadow .15s;}
.inv-wrap .fc:focus{outline:none;border-color:var(--gold);box-shadow:0 0 0 3px rgba(154,128,83,.12);}
.inv-wrap .field-hint{font-size:.72rem;color:var(--text-muted);margin-top:4px;}

/* CREATE INVOICE FORM */
.inv-wrap .if-section{font-size:.85rem;font-weight:700;color:var(--text-heading);margin:0 0 12px;}
.inv-wrap .if-summary{display:flex;flex-direction:column;gap:10px;max-width:640px;margin-bottom:24px;}
.inv-wrap .if-row{display:grid;grid-template-columns:140px minmax(0,1fr);align-items:center;gap:14px;}
.inv-wrap .if-row.top{align-items:start;}
.inv-wrap .if-row.top .if-lbl{padding-top:9px;}
.inv-wrap .if-lbl{font-size:.8rem;color:var(--text-heading);text-align:right;margin:0;}
.inv-wrap .if-lbl .req{color:#ef4444;margin-left:2px;}
.inv-wrap select.fc{height:35px;}
.inv-wrap textarea.fc{resize:vertical;min-height:38px;font-family:inherit;line-height:1.45;}
.inv-wrap .fc[readonly]{background:var(--surface-2);color:var(--text-heading);cursor:default;}
.inv-wrap .if-money{position:relative;}
.inv-wrap .if-money span{position:absolute;left:11px;top:50%;transform:translateY(-50%);font-size:.8rem;color:var(--text-muted);pointer-events:none;}
.inv-wrap .if-money .fc{padding-left:26px;font-variant-numeric:tabular-nums;}
.inv-wrap .if-add-note{margin-top:6px;}
.inv-wrap .if-hint-btn{background:none;border:none;color:var(--gold);font-weight:700;font-size:.72rem;cursor:pointer;padding:0 2px;text-decoration:underline;}
.inv-wrap .if-total{padding:9px 12px;border:1px solid rgba(154,128,83,.3);border-radius:7px;background:rgba(154,128,83,.08);font-size:1rem;font-weight:700;color:var(--gold);font-variant-numeric:tabular-nums;}
@media(max-width:575.98px){
  .inv-wrap .if-row{grid-template-columns:1fr;gap:4px;}
  .inv-wrap .if-lbl{text-align:left;}
  .inv-wrap .if-row.top .if-lbl{padding-top:0;}
}

.inv-wrap .if-need{font-size:.75rem;color:var(--text-muted);text-align:right;}
.inv-wrap .if-need:empty{display:none;}
.inv-wrap .if-actions{display:flex;justify-content:flex-end;gap:10px;flex-wrap:wrap;margin:18px -18px -18px;padding:14px 18px;border-top:1px solid var(--border-color);background:var(--surface-2);}
.inv-wrap .if-actions .btn-ghost{background:var(--card-bg);color:var(--text-heading);padding:9px 16px;font-weight:500;}
.inv-wrap .if-actions .btn-ghost:hover{background:var(--surface-3);}
.inv-wrap .if-actions .btn-ghost:disabled{opacity:.45;cursor:not-allowed;}
.inv-wrap .btn-generate{display:inline-flex;align-items:center;gap:7px;padding:9px 18px;border:none;border-radius:7px;font-size:.82rem;font-weight:600;cursor:pointer;color:#fff;background:linear-gradient(135deg,#9A7B4F,#7A6140);}
.inv-wrap .btn-generate:hover{opacity:.9;}
.inv-wrap .btn-generate:disabled{opacity:.38;cursor:not-allowed;}
@media(max-width:575.98px){.inv-wrap .if-actions > button{flex:1 1 100%;justify-content:center;}}
.inv-wrap .pdf-link{display:inline-flex;align-items:center;gap:5px;font-size:.77rem;font-weight:600;color:#9a8053;background:rgba(154,128,83,.08);padding:2px 7px;border-radius:4px;text-decoration:none;}
.inv-wrap a.pdf-link:hover{background:rgba(154,128,83,.16);}

/* HOP APPROVAL: EMAIL THE INVOICE TO THE CUSTOMER */
.inv-modal-overlay .modal-box.modal-wide{max-width:600px;display:flex;flex-direction:column;max-height:calc(100vh - 110px);margin-top:70px;}
.inv-modal-overlay .modal-wide .modal-body{overflow-y:auto;}
.inv-modal-overlay .btn-confirm:disabled{opacity:.45;cursor:not-allowed;}
.inv-wrap .hm-check{display:flex;align-items:center;gap:8px;font-size:.82rem;font-weight:600;color:var(--text-heading);margin:16px 0 12px;cursor:pointer;}
.inv-wrap .hm-row{display:grid;grid-template-columns:64px minmax(0,1fr);gap:10px;align-items:center;margin-bottom:10px;}
.inv-wrap .hm-lbl{font-size:.8rem;color:var(--text-muted);margin:0;}
.inv-wrap .hm-lbl .req{color:#ef4444;margin-left:2px;}
.inv-wrap .hm-hint{font-size:.72rem;color:var(--text-muted);margin:-4px 0 10px 74px;}
.inv-wrap .hm-attach{display:flex;align-items:center;gap:8px;flex-wrap:wrap;font-size:.78rem;color:var(--text-muted);margin-top:12px;}
.inv-wrap .hm-need{font-size:.75rem;color:var(--text-muted);margin-top:12px;min-height:1.1em;}
@media(max-width:575.98px){.inv-wrap .hm-row{grid-template-columns:1fr;gap:4px;}.inv-wrap .hm-hint{margin-left:0;}}

/* BUTTONS */
.inv-wrap .btn-gold{display:inline-flex;align-items:center;gap:6px;padding:7px 14px;background:linear-gradient(135deg,var(--gold),var(--gold-2));color:#fff;border:none;border-radius:7px;font-size:.8rem;font-weight:500;cursor:pointer;white-space:nowrap;}
.inv-wrap .btn-gold:hover{opacity:.87;}
.inv-wrap .btn-ghost{display:inline-flex;align-items:center;gap:6px;padding:7px 12px;background:var(--surface-2);color:var(--text-muted);border:1px solid var(--border-color);border-radius:7px;font-size:.8rem;cursor:pointer;}
.inv-wrap .btn-ghost:hover{background:var(--surface-3);}
.inv-wrap .btn-submit{width:100%;padding:12px;border:none;border-radius:8px;font-size:.875rem;font-weight:600;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px;transition:opacity .15s;margin-top:4px;}
.inv-wrap .btn-submit:hover{opacity:.88;}
.inv-wrap .btn-submit:disabled{opacity:.38;cursor:not-allowed;}
.inv-wrap .btn-green{background:linear-gradient(135deg,#9A7B4F,#7A6140);color:#fff;}

/* CLOSEOUT LOCK */
.inv-wrap .lock-banner{display:flex;align-items:center;gap:8px;padding:10px 13px;border-radius:7px;font-size:.78rem;margin-bottom:12px;background:rgba(245,158,11,.07);border:1px solid rgba(245,158,11,.2);color:#d97706;}

/* RESOURCE BLOCKS */
.inv-wrap .resource-grid{display:grid;grid-template-columns:1fr 1fr;gap:12px;}
@media(max-width:640px){.inv-wrap .resource-grid{grid-template-columns:1fr;}}
.inv-wrap .resource-block{background:var(--surface-2);border:1px solid var(--border-color);border-radius:8px;padding:13px 14px;}
.inv-wrap .rb-label{font-size:.67rem;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--text-muted);margin-bottom:10px;display:flex;align-items:center;gap:6px;}
.inv-wrap .rb-row{display:flex;align-items:center;justify-content:space-between;padding:5px 0;border-bottom:1px solid var(--border-color);font-size:.79rem;}
.inv-wrap .rb-row:last-child{border-bottom:none;padding-bottom:0;}
.inv-wrap .rb-key{color:var(--text-muted);}
.inv-wrap .rb-val{font-weight:500;color:var(--text-heading);}
.inv-wrap .rb-total{font-weight:700;color:var(--gold);font-size:.85rem;}
.inv-wrap .readonly-tag{font-size:.65rem;background:var(--surface-3);color:var(--text-muted);padding:1px 6px;border-radius:4px;font-weight:600;margin-left:auto;}

/* SECTION DIVIDER */
.inv-wrap .section-divider{display:flex;align-items:center;gap:12px;margin:22px 0 14px;}
.inv-wrap .section-divider-line{flex:1;height:1px;background:var(--border-color);}
.inv-wrap .section-label{display:flex;align-items:center;gap:8px;font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--text-muted);white-space:nowrap;}
.inv-wrap .section-count{font-size:.65rem;padding:1px 7px;border-radius:9px;font-weight:700;}

/* APPROVAL TABLE */
.inv-wrap .pa-card{background:var(--card-bg);border:1px solid var(--card-border);border-radius:10px;box-shadow:var(--card-shadow);overflow:hidden;}
.inv-wrap .pa-card-hdr{display:flex;align-items:center;justify-content:space-between;padding:13px 18px;border-bottom:1px solid var(--border-color);flex-wrap:wrap;gap:8px;}
.inv-wrap .pa-card-hdr-left{display:flex;align-items:center;gap:10px;}
.inv-wrap .pa-card-icon{width:30px;height:30px;border-radius:7px;display:flex;align-items:center;justify-content:center;font-size:.85rem;flex-shrink:0;}
.inv-wrap .pa-card-title{font-size:.85rem;font-weight:600;color:var(--text-heading);}
.inv-wrap .pa-card-sub{font-size:.72rem;color:var(--text-muted);margin-top:1px;}
.inv-wrap table.pa-tbl{width:100%;border-collapse:collapse;min-width:760px;}
.inv-wrap table.pa-tbl thead th{padding:9px 14px;font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--text-muted);background:var(--surface-2);border-bottom:1px solid var(--border-color);white-space:nowrap;text-align:left;}
.inv-wrap table.pa-tbl tbody tr{border-bottom:1px solid var(--border-color);transition:background .1s;}
.inv-wrap table.pa-tbl tbody tr:last-child{border-bottom:none;}
.inv-wrap table.pa-tbl tbody tr:hover{background:var(--table-hover);}
.inv-wrap table.pa-tbl td{padding:11px 14px;font-size:.8rem;color:var(--text-primary);vertical-align:middle;}
.inv-wrap table.pa-tbl td.mono{font-size:.77rem;font-weight:600;color:var(--gold);}
.inv-wrap table.pa-tbl td.muted{color:var(--text-muted);font-size:.78rem;}
.inv-wrap .pa-empty{padding:30px;text-align:center;font-size:.8rem;color:var(--text-muted);}
.inv-wrap .pa-empty i{display:block;font-size:1.4rem;color:var(--text-light);margin-bottom:7px;}
.inv-wrap .pa-scroll{overflow-x:auto;-webkit-overflow-scrolling:touch;}

/* APPROVE BUTTON */
.inv-wrap .btn-mark{display:inline-flex;align-items:center;gap:5px;padding:5px 11px;border-radius:6px;font-size:.76rem;font-weight:600;cursor:pointer;border:1px solid;transition:opacity .15s;white-space:nowrap;}
.inv-wrap .btn-mark:hover{opacity:.82;}
.inv-wrap .btn-mark-blue{background:rgba(154,128,83,.1);color:var(--gold);border-color:rgba(154,128,83,.28);}

/* BADGES */
.inv-wrap .sbadge{display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:20px;font-size:.7rem;font-weight:600;}
.inv-wrap .sb-oow{background:rgba(239,68,68,.1);color:#ef4444;}
.inv-wrap .sb-pi{background:rgba(154,128,83,.12);color:var(--gold);}

/* SUCCESS */
.inv-wrap .ws-success{background:var(--card-bg);border:1px solid var(--card-border);border-radius:10px;box-shadow:var(--card-shadow);padding:48px 24px;text-align:center;display:none;}
.inv-wrap .ws-success.show{display:block;}
.inv-wrap .s-icon{width:60px;height:60px;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 14px;font-size:1.6rem;}

/* MODALS */
.inv-modal-overlay{display:none;position:fixed;inset:0;background:var(--overlay-bg,rgba(9,15,35,.62));z-index:900;align-items:center;justify-content:center;backdrop-filter:blur(4px);padding:20px;}
.inv-modal-overlay.show{display:flex;}
.inv-modal-overlay .modal-box{background:var(--modal-bg,#fff);border-radius:12px;width:100%;max-width:440px;box-shadow:var(--modal-shadow,0 24px 64px rgba(0,0,0,.16));border:1px solid var(--card-border);overflow:hidden;animation:invMIn .18s ease;}
@keyframes invMIn{from{opacity:0;transform:scale(.96);}to{opacity:1;transform:scale(1);}}
.inv-modal-overlay .modal-hdr{display:flex;align-items:center;justify-content:space-between;padding:15px 20px;border-bottom:1px solid var(--border-color);}
.inv-modal-overlay .modal-hdr-left{display:flex;align-items:center;gap:10px;}
.inv-modal-overlay .modal-hdr-icon{width:32px;height:32px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:.9rem;flex-shrink:0;}
.inv-modal-overlay .modal-hdr h6{font-size:.9rem;font-weight:600;color:var(--text-heading);margin:0;}
.inv-modal-overlay .modal-close{background:none;border:none;color:var(--text-muted);cursor:pointer;padding:4px;border-radius:5px;font-size:1rem;line-height:1;}
.inv-modal-overlay .modal-close:hover{background:var(--surface-2);}
.inv-modal-overlay .modal-body{padding:20px;}
.inv-modal-overlay .modal-foot{display:flex;gap:10px;padding:14px 20px;border-top:1px solid var(--border-color);background:var(--surface-2);}
.inv-modal-overlay .btn-confirm{flex:1;padding:9px;border:none;border-radius:7px;font-size:.82rem;font-weight:600;cursor:pointer;color:#fff;}
.inv-modal-overlay .btn-cancel{flex:1;padding:9px;background:var(--surface-2);color:var(--text-muted);border:1px solid var(--border-color);border-radius:7px;font-size:.82rem;cursor:pointer;}

/* TOAST */
.inv-toast-wrap{position:fixed;bottom:22px;right:22px;z-index:9999;display:flex;flex-direction:column;gap:8px;pointer-events:none;max-width:calc(100vw - 44px);}
.inv-toast-wrap .toast-item{display:flex;align-items:flex-start;gap:10px;padding:12px 15px;border-radius:9px;background:var(--card-bg);border:1px solid var(--card-border);box-shadow:0 6px 24px rgba(0,0,0,.14);min-width:240px;max-width:320px;animation:invToastIn .2s ease;pointer-events:auto;}
@keyframes invToastIn{from{opacity:0;transform:translateY(10px);}to{opacity:1;transform:translateY(0);}}
.inv-toast-wrap .t-ico{font-size:1rem;flex-shrink:0;margin-top:1px;}
.inv-toast-wrap .t-ico.ok{color:#15803d;}.inv-toast-wrap .t-ico.err{color:#ef4444;}
.inv-toast-wrap .t-ico.info{color:var(--gold);}.inv-toast-wrap .t-ico.warn{color:#d97706;}
.inv-toast-wrap .t-title{font-size:.8rem;font-weight:600;color:var(--text-heading);margin:0 0 2px;}
.inv-toast-wrap .t-body{font-size:.75rem;color:var(--text-muted);margin:0;}
@media(max-width:575.98px){.inv-toast-wrap{left:12px;right:12px;bottom:12px;}.inv-toast-wrap .toast-item{max-width:none;}}
</style>
@endpush

@section('content')
<div class="inv-wrap">

  {{-- PAGE HEADER --}}
  <div class="pg-header">
    <h4 class="pg-hdr-title"><i class="bi bi-receipt me-2"></i>Create Invoice &amp; Mark Approve</h4>
    <p class="pg-hdr-desc">Create invoices for QC-passed OoW SRs and generate the PDF here. SR closure is locked until the invoice is generated. Mark HoP approval to trigger final completion.</p>
    <div class="pg-hdr-meta">
      <span class="meta-badge"><i class="bi bi-shield-fill-check me-1"></i>Super Admin</span>
      <span class="meta-badge"><i class="bi bi-person-gear me-1"></i>Admin</span>
      <span class="meta-badge"><i class="bi bi-calculator me-1"></i>Accounts / AR</span>
    </div>
  </div>

  {{-- STATS --}}
  <div class="stats-strip">
    <div class="stat-card"><div class="stat-icon" style="background:rgba(37,99,235,.1);"><i class="bi bi-hourglass-split" style="color:#2563eb;"></i></div><div><div class="stat-num" id="stat-pi">0</div><div class="stat-lbl">Pending Invoice</div></div></div>
   <div class="stat-card"><div class="stat-icon" style="background:rgba(154,128,83,.1);"><i class="bi bi-person-workspace" style="color:#9a8053;"></i></div><div><div class="stat-num" id="stat-ph">0</div><div class="stat-lbl">Pending HoP Approval</div></div></div>
    <div class="stat-card"><div class="stat-icon" style="background:rgba(21,128,61,.1);"><i class="bi bi-check-circle" style="color:#15803d;"></i></div><div><div class="stat-num">{{ $completedThisMonth ?? 0 }}</div><div class="stat-lbl">Completed This Month</div></div></div>
    <div class="stat-card"><div class="stat-icon" style="background:rgba(154,128,83,.1);"><i class="bi bi-currency-exchange" style="color:#9a8053;"></i></div><div><div class="stat-num">{{ $invCurrency['symbol'] ?? '₹' }} {{ $invoicedThisMonth ?? '0' }}</div><div class="stat-lbl">Invoiced This Month</div></div></div>
  </div>

  {{-- FILTER BAR --}}
  <div class="filter-bar">
    <div class="filter-group"><div class="filter-label">Search</div><input class="filter-control" type="text" id="inv-search" placeholder="SR ID, customer name…"/></div>
    <div class="filter-group"><div class="filter-label">Date From</div><input class="filter-control" type="date" id="inv-date"/></div>
    <div class="filter-actions"><button class="btn-ghost" onclick="invResetFilters()"><i class="bi bi-x-circle"></i>Reset</button></div>
  </div>

  {{-- TWO-PANEL --}}
  <div class="two-panel">
    <div class="queue-panel">
      <div class="queue-hdr">
        <div class="queue-hdr-title"><i class="bi bi-list-ul" style="color:#9a8053;"></i>Pending Invoice</div>
        <span class="q-count" id="inv-count">0</span>
      </div>
      <div class="queue-list" id="inv-list"></div>
    </div>
    <div class="ws-panel">
      <div class="ws-empty" id="inv-empty">
        <div class="ws-empty-icon"><i class="bi bi-receipt"></i></div>
        <h6>Select a Ticket</h6>
        <p>Choose a QC-passed OoW SR to create its invoice and initiate the finalisation process.</p>
      </div>
      <div class="ws-success" id="inv-success">
        <div class="s-icon" style="background:rgba(154,128,83,.1);"><i class="bi bi-receipt" style="color:#9a8053;font-size:1.6rem;"></i></div>
        <h5 style="font-size:1rem;color:var(--text-heading);margin-bottom:6px;font-family:unset;" id="inv-success-title"></h5>
        <p style="font-size:.82rem;color:var(--text-muted);margin-bottom:18px;" id="inv-success-body"></p>
        <div style="display:flex;gap:10px;justify-content:center;flex-wrap:wrap;">
          <a id="inv-success-pdf" class="btn-ghost" href="#" target="_blank" rel="noopener" style="text-decoration:none;"><i class="bi bi-file-earmark-pdf-fill" style="color:#ef4444;"></i>View Invoice PDF</a>
          <button onclick="invNext()" class="btn-gold"><i class="bi bi-arrow-right"></i>Next Ticket</button>
        </div>
      </div>
      <div id="inv-detail" style="display:none;flex-direction:column;gap:14px;">
        <div class="sr-hdr-card">
          <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:12px;margin-bottom:12px;flex-wrap:wrap;">
            <div><div class="ws-sr-id" id="inv-sr-id"></div><div class="ws-sr-client" id="inv-sr-client"></div><div class="ws-sr-site" id="inv-sr-site"></div></div>
            <div style="display:flex;flex-direction:column;align-items:flex-end;gap:6px;">
              <span class="sbadge sb-pi"><i class="bi bi-circle-fill" style="font-size:.4rem;"></i>Pending Invoice</span>
              <span class="sbadge sb-oow"><i class="bi bi-shield-exclamation"></i>Out of Warranty</span>
            </div>
          </div>
          <div class="meta-chips" id="inv-chips"></div>
        </div>
        <div class="ws-card">
          <div class="ws-card-hdr">
            <div class="ws-card-icon" style="background:rgba(37,99,235,.1);"><i class="bi bi-person-badge" style="color:#2563eb;"></i></div>
            <h6>Technician Resource Summary</h6>
            <span class="readonly-tag" style="margin-left:auto;"><i class="bi bi-lock-fill" style="font-size:.6rem;"></i> Read Only</span>
          </div>
          <div class="ws-card-body"><div class="resource-grid" id="inv-resources"></div></div>
        </div>
        {{-- CREATE INVOICE (replaces the old PDF upload card) --}}
        <div class="ws-card">
          <div class="ws-card-hdr">
            <div class="ws-card-icon" style="background:rgba(154,128,83,.1);"><i class="bi bi-receipt" style="color:#9a8053;"></i></div>
            <h6>Create Invoice</h6>
          </div>
          <div class="ws-card-body">
            <div class="lock-banner" id="inv-lock"><i class="bi bi-lock-fill"></i>Final closure is locked until the invoice is generated for this record.</div>

            {{-- Invoice summary --}}
            <div class="if-section">Invoice Summary</div>
            <div class="if-summary">
              <div class="if-row">
                <label class="if-lbl" for="inv-invoice-date">Invoice Date <span class="req">*</span></label>
                <input type="date" class="fc" id="inv-invoice-date" onchange="invDateChanged()"/>
              </div>
              <div class="if-row">
                <label class="if-lbl" for="inv-terms">Payment Terms</label>
                {{-- same keys as invoicePaymentTerms() in BuildsInvoicePdf --}}
                <select class="fc" id="inv-terms" onchange="invTermsChanged()">
                  <option value="net_30">Pay within 30 days</option>
                  <option value="custom">Custom</option>
                </select>
              </div>
              <div class="if-row">
                <label class="if-lbl" for="inv-due">Due Date <span class="req">*</span></label>
                <input type="date" class="fc" id="inv-due" onchange="invDueChanged()"/>
              </div>
              <div class="if-row">
                <label class="if-lbl" for="inv-created-by">Created By</label>
                <input type="text" class="fc" id="inv-created-by" value="{{ auth()->user()?->name }}" readonly tabindex="-1"/>
              </div>
            </div>

            {{-- Amount: the quotation total plus anything extra --}}
            <div class="if-section">Invoice Amount</div>
            <div class="if-summary">
              <div class="if-row top">
                <label class="if-lbl" for="inv-amount" id="inv-amount-lbl">Quotation Amount</label>
                <div>
                  <div class="if-money">
                    <span>{{ $invCurrency['symbol'] ?? '₹' }}</span>
                    <input type="number" class="fc" id="inv-amount" min="0" step="0.01" inputmode="decimal" placeholder="0.00" oninput="invRefresh()"/>
                  </div>
                  <div class="field-hint" id="inv-amount-hint"></div>
                </div>
              </div>
              <div class="if-row top">
                <label class="if-lbl" for="inv-additional">Additional Amount</label>
                <div>
                  <div class="if-money">
                    <span>{{ $invCurrency['symbol'] ?? '₹' }}</span>
                    <input type="number" class="fc" id="inv-additional" min="0" step="0.01" inputmode="decimal" placeholder="0.00" oninput="invRefresh()"/>
                  </div>
                  <input type="text" class="fc if-add-note" id="inv-additional-note" maxlength="255" placeholder="What is it for? e.g. Extra materials on site" aria-label="Additional amount description"/>
                  <div class="field-hint" id="inv-exp-hint"></div>
                </div>
              </div>
              <div class="if-row">
                <div class="if-lbl" style="font-weight:700;">Total</div>
                <div class="if-total" id="inv-grand"></div>
              </div>
            </div>

            <div class="form-group">
              <label class="form-label-sm" for="inv-notes">Notes / Terms <span class="hint">(optional)</span></label>
              <textarea class="fc" id="inv-notes" rows="2" maxlength="2000" placeholder="Bank details, payment instructions, warranty on the work…"></textarea>
              <div class="field-hint">Printed at the bottom of the invoice PDF.</div>
            </div>

            <div id="inv-val-msg" style="display:none;padding:8px 12px;border-radius:7px;background:rgba(239,68,68,.07);border:1px solid rgba(239,68,68,.2);color:#ef4444;font-size:.78rem;margin:12px 0 0;"></div>

            <div class="if-actions">
              <div class="if-need" id="inv-need" style="flex:1 1 100%;"></div>
              <button type="button" class="btn-ghost" onclick="invCancel()">Cancel</button>
              <button type="button" class="btn-ghost" id="inv-preview-btn" onclick="previewInvoice()" disabled><i class="bi bi-eye"></i>Preview PDF</button>
              <button type="button" class="btn-generate" id="inv-btn" onclick="openInvConfirm()" disabled><i class="bi bi-file-earmark-pdf-fill"></i>Generate Invoice</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- PENDING HOP APPROVAL SECTION --}}
  <div class="section-divider">
    <div class="section-divider-line"></div>
    <div class="section-label">
      <i class="bi bi-person-workspace" style="color:#9a8053;"></i>Pending HoP Approval
      <span class="section-count" style="background:rgba(154,128,83,.12);color:#9a8053;" id="ph-count">0</span>
    </div>
    <div class="section-divider-line"></div>
  </div>

  <div class="pa-card">
    <div class="pa-card-hdr">
      <div class="pa-card-hdr-left">
        <div class="pa-card-icon" style="background:rgba(154,128,83,.1);"><i class="bi bi-person-check" style="color:#9a8053;"></i></div>
        <div><div class="pa-card-title">Submitted - Awaiting Head of Projects</div><div class="pa-card-sub">Invoice generated - mark when HoP confirms final closure</div></div>
      </div>
      <div style="font-size:.75rem;color:var(--text-muted);display:flex;align-items:center;gap:5px;"><i class="bi bi-bell" style="color:#9a8053;"></i>HoP notified on submission</div>
    </div>
    <div class="pa-scroll">
      <table class="pa-tbl">
        <thead>
          <tr><th>SR ID</th><th>Customer</th><th>Site</th><th>Invoice No</th><th>Submitted</th><th>Pending</th><th style="text-align:center;width:180px;">Action</th></tr>
        </thead>
        <tbody id="ph-tbody"></tbody>
      </table>
    </div>
  </div>

  {{-- INVOICE SUBMIT CONFIRM MODAL --}}
  <div class="inv-modal-overlay" id="inv-conf-modal" onclick="if(event.target===this)this.classList.remove('show')">
    <div class="modal-box">
      <div class="modal-hdr">
        <div class="modal-hdr-left">
          <div class="modal-hdr-icon" style="background:rgba(154,128,83,.1);"><i class="bi bi-check-circle-fill" style="color:#9a8053;"></i></div>
          <h6>Confirm Invoice Generation</h6>
        </div>
        <button class="modal-close" onclick="document.getElementById('inv-conf-modal').classList.remove('show')"><i class="bi bi-x-lg"></i></button>
      </div>
      <div class="modal-body">
        <p style="font-size:.8rem;color:var(--text-muted);margin-bottom:14px;">Generating an invoice of <strong id="inv-conf-amt" style="color:var(--text-heading);"></strong> for <strong id="inv-conf-sr" style="color:var(--text-heading);"></strong>. This will:</p>
        <div style="display:flex;flex-direction:column;gap:8px;margin-bottom:12px;">
          <div style="display:flex;align-items:center;gap:8px;font-size:.8rem;"><i class="bi bi-1-circle-fill" style="color:#9a8053;flex-shrink:0;"></i>Save the invoice and generate its PDF on the SR record.</div>
          <div style="display:flex;align-items:center;gap:8px;font-size:.8rem;"><i class="bi bi-2-circle-fill" style="color:#9a8053;flex-shrink:0;"></i>Forward to <strong>Head of Projects</strong> for final approval.</div>
          <div style="display:flex;align-items:center;gap:8px;font-size:.8rem;"><i class="bi bi-3-circle-fill" style="color:#9a8053;flex-shrink:0;"></i>On HoP approval → Status:<strong>Completed</strong> + WhatsApp summary to customer.</div>
        </div>
        <div style="padding:9px 12px;background:rgba(154,128,83,.07);border:1px solid rgba(154,128,83,.2);border-radius:7px;font-size:.78rem;color:#9a8053;display:flex;align-items:center;gap:8px;">
          <i class="bi bi-info-circle"></i>Cannot be undone once the invoice is generated.
        </div>
      </div>
      <div class="modal-foot">
        <button class="btn-cancel" onclick="document.getElementById('inv-conf-modal').classList.remove('show')">Cancel</button>
        <button class="btn-confirm" style="background:linear-gradient(135deg,#9A7B4F,#7A6140);" onclick="execInvSubmit()"><i class="bi bi-check-lg"></i> Confirm &amp; Generate</button>
      </div>
    </div>
  </div>

  {{-- HOP APPROVAL MODAL (also emails the invoice PDF to the customer) --}}
  <div class="inv-modal-overlay" id="hop-modal" onclick="if(event.target===this)closeHopModal()">
    <div class="modal-box modal-wide" role="dialog" aria-modal="true" aria-labelledby="hop-title">
      <div class="modal-hdr">
        <div class="modal-hdr-left">
          <div class="modal-hdr-icon" style="background:rgba(154,128,83,.1);"><i class="bi bi-patch-check-fill" style="color:#9a8053;"></i></div>
          <h6 id="hop-title" style="font-family:unset;">Mark Invoice as HoP Approved</h6>
        </div>
        <button type="button" class="modal-close" onclick="closeHopModal()" aria-label="Close"><i class="bi bi-x-lg"></i></button>
      </div>
      <div class="modal-body">
        <p style="font-size:.8rem;color:var(--text-muted);margin-bottom:14px;">Confirm Head of Projects has approved the invoice for <strong id="hop-sr" style="color:var(--text-heading);"></strong>.</p>
        <div style="padding:12px 14px;border-radius:8px;background:rgba(154,128,83,.07);border:1px solid rgba(154,128,83,.2);display:flex;align-items:flex-start;gap:10px;">
          <i class="bi bi-arrow-right-circle-fill" style="color:#9a8053;flex-shrink:0;margin-top:2px;"></i>
          <div style="font-size:.8rem;color:#9a8053;"><strong>Status: Pending Invoice → Completed</strong><br/><span style="opacity:.8;font-size:.76rem;">SR fully closed. Digital summary and feedback link dispatched to customer via WhatsApp.</span></div>
        </div>

        <label class="hm-check"><input type="checkbox" id="hop-send" checked onchange="hopToggleEmail()"/>Email the invoice to the customer</label>

        <div id="hop-email">
          <div class="hm-row">
            <label class="hm-lbl" for="hop-from">From <span class="req">*</span></label>
            <input type="email" class="fc" id="hop-from" autocomplete="off" oninput="hopValidate()"/>
          </div>
          <div class="hm-hint">Use an address your mail server is allowed to send from.</div>
          <div class="hm-row">
            <label class="hm-lbl" for="hop-to">To <span class="req">*</span></label>
            <input type="text" class="fc" id="hop-to" placeholder="customer@example.com" autocomplete="off" oninput="hopValidate()"/>
          </div>
          <div class="hm-hint">Separate several addresses with commas.</div>
          <div class="hm-row">
            <label class="hm-lbl" for="hop-subject">Subject <span class="req">*</span></label>
            <input type="text" class="fc" id="hop-subject" maxlength="200" oninput="hopValidate()"/>
          </div>
          <label class="form-label-sm" for="hop-message" style="margin-top:14px;">Message</label>
          <textarea class="fc" id="hop-message" rows="4" maxlength="5000"></textarea>
          <div class="hm-attach">
            <i class="bi bi-paperclip"></i>Attached:
            <a class="pdf-link" id="hop-pdf" href="#" target="_blank" rel="noopener"><i class="bi bi-file-earmark-pdf-fill" style="color:#ef4444;"></i><span id="hop-pdf-name"></span></a>
          </div>
        </div>
        <div class="hm-need" id="hop-need"></div>
      </div>
      <div class="modal-foot">
        <button type="button" class="btn-cancel" onclick="closeHopModal()">Cancel</button>
        <button type="button" class="btn-confirm" id="hop-confirm" style="background:linear-gradient(135deg,#9A7B4F,#7A6140);" onclick="execHopApproval()"></button>
      </div>
    </div>
  </div>

  <div class="inv-toast-wrap" id="invToastWrap"></div>
</div>
@include('partials.sr_tracking_modal')
@endsection

@push('scripts')
<script>
/* =========================================================
   Invoice Panel - page scripts
   INV_QUEUE row shape:
   { id, dbId, client, site, technician, logged, createdAt,
     punchIn, punchOut, duration, expenses:[{cat, amt}], totalExp,
     quote:{ref, summary, amount}|null }
   PENDING_HOP row shape:
   { id, sr, dbId, client, site, code, pdfUrl, email, total, submitted, waiting, createdAt }
   ========================================================= */
var INV_QUEUE   = @json($invQueue ?? []);
var PENDING_HOP = @json($pendingHop ?? []);
var CSRF        = '{{ csrf_token() }}';
var CAN_HOP_APPROVE = @json($canHopApprove ?? false);
/* days until due; null = the user picks the date. Same keys as invoicePaymentTerms() in BuildsInvoicePdf. */
var INV_TERMS   = { net_30:{days:30}, custom:{days:null} };
var CUR         = @json($invCurrency['symbol'] ?? '₹');
var HOP_FROM    = @json(config('mail.from.address'));   // default From address for the invoice email
var APP_NAME    = @json(config('app.name'));
/* filtered views - what actually gets rendered */
var INV_FILTERED = INV_QUEUE.slice();
var PH_FILTERED  = PENDING_HOP.slice();

var selInv  = null;
var invBusy = false;

/* ---------- TOAST ---------- */
function showToast(type,title,body){
  var w = document.getElementById('invToastWrap');
  var icons = {ok:'bi-check-circle-fill',err:'bi-x-circle-fill',info:'bi-info-circle-fill',warn:'bi-exclamation-triangle-fill'};
  var t = document.createElement('div');
  t.className = 'toast-item';
  t.innerHTML = '<i class="bi '+(icons[type]||icons.info)+' t-ico '+type+'"></i><div><p class="t-title"></p><p class="t-body"></p></div>';
  t.querySelector('.t-title').textContent = title;
  t.querySelector('.t-body').textContent  = body;
  w.appendChild(t);
  setTimeout(function(){t.style.transition='opacity .3s';t.style.opacity='0';setTimeout(function(){t.remove();},300);},3800);
}

/* ---------- SMALL HELPERS ---------- */
function invEl(id){ return document.getElementById(id); }
function invEsc(s){
  return String(s == null ? '' : s).replace(/[&<>"']/g, function(c){
    return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c];
  });
}
function invNum(v){ var n = parseFloat(v); return isFinite(n) ? n : 0; }
function invR2(n){ return Math.round((n + Number.EPSILON) * 100) / 100; }
function invFmt(n){ return invR2(n).toLocaleString('en-IN',{minimumFractionDigits:2,maximumFractionDigits:2}); }
function invMoney(n){ return CUR + ' ' + invFmt(n); }
function invIso(d){
  function p(x){ return (x < 10 ? '0' : '') + x; }
  return d.getFullYear()+'-'+p(d.getMonth()+1)+'-'+p(d.getDate());
}
function invToday(){ return invIso(new Date()); }
function invAddDays(iso, days){
  var p = iso.split('-');
  return invIso(new Date(+p[0], +p[1]-1, +p[2] + days));
}
/* parse a JSON reply; turn non-2xx (and non-JSON error pages) into a readable Error */
function invJson(r){
  return r.json().catch(function(){ return {}; }).then(function(d){
    if(!r.ok) throw new Error(d.message || ('Server error ('+r.status+')'));
    return d;
  });
}

/* ---------- INVOICE SUMMARY: DATES + TERMS ---------- */
function invTermDays(){
  var t = INV_TERMS[invEl('inv-terms').value];
  return t && t.days !== null && t.days !== undefined ? +t.days : null;
}
/* fixed terms decide the due date; "Custom" leaves it to the user */
function invApplyTerms(){
  var date = invEl('inv-invoice-date').value;
  var days = invTermDays();
  if(date && days !== null) invEl('inv-due').value = invAddDays(date, days);
  invEl('inv-due').min = date || '';
}
function invDateChanged(){ invApplyTerms(); inv_validate(); }
function invTermsChanged(){ invApplyTerms(); inv_validate(); }
/* typing a due date that doesn't match the chosen terms switches the terms to Custom */
function invDueChanged(){
  var date = invEl('inv-invoice-date').value;
  var days = invTermDays();
  if(date && days !== null && invEl('inv-due').value !== invAddDays(date, days) && INV_TERMS.custom){
    invEl('inv-terms').value = 'custom';
  }
  inv_validate();
}

/* ---------- AMOUNT ---------- */
/* the quotation total for the selected SR, or 0 when it has none saved */
function invQuoteAmount(){
  var q = selInv && selInv.quote;
  var a = q ? invNum(q.amount) : 0;
  return a > 0 ? invR2(a) : 0;
}

function invCalc(){
  var amount = invR2(Math.max(0, invNum(invEl('inv-amount').value)));
  var add    = invR2(Math.max(0, invNum(invEl('inv-additional').value)));
  return {amount:amount, add:add, grand:invR2(amount + add)};
}

function invRefresh(){
  invEl('inv-grand').textContent = invMoney(invCalc().grand);
  inv_validate();
}

/* one click copies what the technician logged on site into Additional Amount */
function invUseExpenses(){
  if(!selInv) return;
  invEl('inv-additional').value = invR2(invNum(selInv.totalExp)).toFixed(2);
  if(!invEl('inv-additional-note').value.trim()) invEl('inv-additional-note').value = 'Site expenses';
  invRefresh();
}

/* ---------- VALIDATION ---------- */
/* returns the first problem with the form, or '' when it is ready to generate */
function invError(){
  if(!selInv) return 'Select a ticket from the queue.';
  var date = invEl('inv-invoice-date').value;
  var due  = invEl('inv-due').value;
  if(!date) return 'Choose the invoice date.';
  if(!due)  return 'Choose the due date.';
  if(due < date) return 'Due date cannot be before the invoice date.';
  if(invNum(invEl('inv-amount').value) <= 0) return 'Enter the invoice amount.';
  if(invNum(invEl('inv-additional').value) < 0) return 'Additional amount cannot be negative.';
  return '';
}

function inv_validate(){
  var btn = invEl('inv-btn');
  if(!btn) return;
  var err = invError();
  btn.disabled = !!err || invBusy;
  invEl('inv-preview-btn').disabled = !!err || invBusy;
  invEl('inv-need').textContent = err && selInv ? 'To continue: ' + err : '';
}
window.inv_validate = inv_validate;

function invShowValMsg(msg){
  var el = invEl('inv-val-msg');
  el.textContent = msg; el.style.display = 'block';
  setTimeout(function(){ el.style.display = 'none'; }, 4500);
}

function invPayload(){
  var t = invCalc();
  return {
    invoice_date:      invEl('inv-invoice-date').value,
    payment_terms:     invEl('inv-terms').value,
    due_date:          invEl('inv-due').value,
    amount:            t.amount,     // ignored by the server when the SR has a quotation
    additional_amount: t.add,
    additional_note:   invEl('inv-additional-note').value.trim() || null,
    notes:             invEl('inv-notes').value.trim() || null
  };
}

/* ---------- QUEUE ---------- */
function renderInvQueue(list){
  list = list || INV_FILTERED;
  var ul = document.getElementById('inv-list');
  document.getElementById('inv-count').textContent = list.length;
  var pi = document.getElementById('stat-pi');
  if(pi) pi.textContent = INV_QUEUE.length;   // KPI = total, not filtered
  if(!list.length){
    ul.innerHTML = '<div style="padding:28px;text-align:center;font-size:.8rem;color:var(--text-muted);"><i class="bi bi-inbox" style="display:block;font-size:1.6rem;margin-bottom:8px;color:var(--text-light);"></i>No matching tickets</div>';
    return;
  }
  ul.innerHTML = list.map(function(sr){
    var ac = selInv && selInv.id===sr.id ? ' active':'';
    return '<div class="queue-item'+ac+'" data-id="'+sr.id+'" onclick="selectInv(this.dataset.id)">'+
      '<div class="qi-id sr-ref-trigger" data-sr-id="'+sr.dbId+'" onclick="event.stopPropagation(); openSrTracking('+sr.dbId+');">'+sr.id+'</div>'+
      '<div class="qi-client">'+sr.client+'</div>'+
      '<div class="qi-sub"><i class="bi bi-geo-alt" style="font-size:.7rem;"></i> '+sr.site+'</div>'+
      '<div class="qi-foot">'+
        '<span class="sbadge sb-pi" style="font-size:.65rem;">Pending Invoice</span>'+
        '<span class="qi-time"><i class="bi bi-clock" style="font-size:.65rem;"></i>'+(sr.logged||'')+'</span>'+
      '</div></div>';
  }).join('');
}

/* start a fresh invoice for the selected SR */
function invResetForm(){
  var q  = selInv.quote;
  var qa = invQuoteAmount();

  invEl('inv-invoice-date').value = invToday();
  invEl('inv-terms').selectedIndex = 0;
  invApplyTerms();

  /* the quotation total is the invoice amount; it is only typed in when the SR has no quotation saved */
  var amountEl = invEl('inv-amount');
  amountEl.value    = qa ? qa.toFixed(2) : '';
  amountEl.readOnly = !!qa;
  invEl('inv-amount-lbl').innerHTML = qa ? 'Quotation Amount' : 'Amount <span class="req">*</span>';
  invEl('inv-amount-hint').textContent = qa
    ? 'From quotation ' + ((q && q.ref) || '-') + '. Add anything extra below.'
    : 'No quotation amount is saved for this SR. Enter the amount to invoice.';

  invEl('inv-additional').value = '';
  invEl('inv-additional-note').value = '';
  invEl('inv-notes').value = '';
  invEl('inv-val-msg').style.display = 'none';

  var exp = invNum(selInv.totalExp);
  invEl('inv-exp-hint').innerHTML = exp > 0
    ? 'Technician logged '+invEsc(invMoney(exp))+' on site. <button type="button" class="if-hint-btn" onclick="invUseExpenses()">Use this</button>'
    : '';

  invRefresh();
}

function selectInv(id){
  selInv = INV_QUEUE.find(function(s){return s.id===id;});
  if(!selInv) return;
  invResetForm();
  renderInvQueue();
  document.getElementById('inv-empty').style.display = 'none';
  document.getElementById('inv-success').classList.remove('show');
  document.getElementById('inv-detail').style.display = 'flex';
  var invSrIdEl = document.getElementById('inv-sr-id');
  invSrIdEl.textContent = selInv.id;
  invSrIdEl.classList.add('sr-ref-trigger');
  invSrIdEl.onclick = function(){ openSrTracking(selInv.dbId); };
  document.getElementById('inv-sr-client').textContent = selInv.client;
  document.getElementById('inv-sr-site').innerHTML     = '<i class="bi bi-geo-alt" style="color:#9a8053;font-size:.8rem;"></i> '+selInv.site;
  document.getElementById('inv-chips').innerHTML =
    '<div class="meta-chip"><div class="meta-chip-label">Technician</div><div class="meta-chip-value">'+(selInv.technician||'-')+'</div></div>'+
    '<div class="meta-chip"><div class="meta-chip-label">Punch In</div><div class="meta-chip-value">'+(selInv.punchIn||'-')+'</div></div>'+
    '<div class="meta-chip"><div class="meta-chip-label">Punch Out</div><div class="meta-chip-value">'+(selInv.punchOut||'-')+'</div></div>'+
    '<div class="meta-chip"><div class="meta-chip-label">Duration</div><div class="meta-chip-value">'+(selInv.duration||'-')+'</div></div>';

  var expenses = selInv.expenses || [];
  var expRows = expenses.length
    ? expenses.map(function(e){return '<div class="rb-row"><span class="rb-key">'+e.cat+'</span><span class="rb-val">'+invMoney(e.amt||0)+'</span></div>';}).join('')
    : '<div class="rb-row"><span class="rb-key">No expenses logged</span><span class="rb-val">-</span></div>';

  document.getElementById('inv-resources').innerHTML =
    '<div class="resource-block"><div class="rb-label"><i class="bi bi-clock"></i>Time on Site</div>'+
    '<div class="rb-row"><span class="rb-key">Punch In</span><span class="rb-val">'+(selInv.punchIn||'-')+'</span></div>'+
    '<div class="rb-row"><span class="rb-key">Punch Out</span><span class="rb-val">'+(selInv.punchOut||'-')+'</span></div>'+
    '<div class="rb-row"><span class="rb-key">Duration</span><span class="rb-total">'+(selInv.duration||'-')+'</span></div></div>'+
    '<div class="resource-block"><div class="rb-label"><i class="bi bi-receipt"></i>Material Expenses</div>'+expRows+
    '<div class="rb-row"><span class="rb-key">Total</span><span class="rb-total">'+invMoney(selInv.totalExp||0)+'</span></div></div>';
}

/* Cancel: drop the draft and go back to the queue */
function invCancel(){
  if(invBusy) return;
  selInv = null;
  document.getElementById('inv-detail').style.display = 'none';
  document.getElementById('inv-empty').style.display = '';
  renderInvQueue();
}

/* ---------- PENDING HOP ---------- */
function renderPH(list){
  list = list || PH_FILTERED;
  var tbody = document.getElementById('ph-tbody');
  document.getElementById('ph-count').textContent = list.length;
  var ph = document.getElementById('stat-ph');
  if(ph) ph.textContent = PENDING_HOP.length;   // KPI = total
  if(!list.length){
    tbody.innerHTML = '<tr><td colspan="7"><div class="pa-empty"><i class="bi bi-inbox"></i><p>No invoices pending HoP approval</p></div></td></tr>';
    return;
  }
  tbody.innerHTML = list.map(function(item){
  var actionCell = CAN_HOP_APPROVE
    ? '<button class="btn-mark btn-mark-blue" data-id="'+item.id+'" data-sr="'+item.sr+'" onclick="openHopModal(this.dataset.id,this.dataset.sr)"><i class="bi bi-patch-check-fill"></i>Mark HoP Approved &amp; Close</button>'
    : '<span style="font-size:.72rem;color:var(--text-muted);"><i class="bi bi-lock" style="margin-right:4px;"></i>No access</span>';

  /* the invoice number opens the stored PDF */
  var codeCell = item.pdfUrl
    ? '<a class="pdf-link" href="'+invEsc(item.pdfUrl)+'" target="_blank" rel="noopener" title="Open the invoice PDF"><i class="bi bi-file-earmark-pdf-fill" style="color:#ef4444;"></i>'+invEsc(item.code)+'</a>'
    : '<span class="pdf-link">'+invEsc(item.code)+'</span>';

  return '<tr>'+
    '<td class="mono"><span class="sr-ref-trigger" data-sr-id="'+item.dbId+'" onclick="openSrTracking('+item.dbId+');">'+item.sr+'</span></td>'+
    '<td style="font-weight:500;">'+item.client+'</td>'+
    '<td class="muted">'+item.site+'</td>'+
    '<td>'+codeCell+'</td>'+
    '<td class="muted">'+item.submitted+'</td>'+
    '<td><span style="font-size:.75rem;color:#9a8053;display:inline-flex;align-items:center;gap:4px;"><i class="bi bi-clock"></i>'+item.waiting+'</span></td>'+
    '<td style="text-align:center;">'+actionCell+'</td>'+
  '</tr>';
}).join('');
}

/* ---------- FILTERS ---------- */
function invFilter(){
  var q     = document.getElementById('inv-search').value.trim().toLowerCase();
  var from  = document.getElementById('inv-date').value;          // '' or 'YYYY-MM-DD'
  var toEl  = document.getElementById('inv-date-to');             // optional second input
  var to    = toEl ? toEl.value : '';

  function match(row){
    if (from && (!row.createdAt || row.createdAt < from)) return false;
    if (to   && (!row.createdAt || row.createdAt > to))   return false;
    if (q) {
      var hay = [row.id, row.sr, row.client, row.site, row.code, row.technician]
        .filter(Boolean).join(' ').toLowerCase();
      if (hay.indexOf(q) === -1) return false;
    }
    return true;
  }

  INV_FILTERED = INV_QUEUE.filter(match);
  PH_FILTERED  = PENDING_HOP.filter(match);
  renderInvQueue(INV_FILTERED);
  renderPH(PH_FILTERED);
}

function invResetFilters(){
  document.getElementById('inv-search').value = '';
  document.getElementById('inv-date').value = '';
  var toEl = document.getElementById('inv-date-to');
  if(toEl) toEl.value = '';
  INV_FILTERED = INV_QUEUE.slice();
  PH_FILTERED  = PENDING_HOP.slice();
  renderInvQueue(INV_FILTERED);
  renderPH(PH_FILTERED);
}

/* ---------- PREVIEW ---------- */
/* asks the server to render the PDF without saving anything */
function previewInvoice(){
  var err = invError();
  if(err){ invShowValMsg(err); return; }
  var sr = selInv;
  invEl('inv-preview-btn').disabled = true;

  /* open the tab now, while we still have the click, so the browser doesn't block it */
  var win = window.open('', '_blank');

  fetch('/invoice_panel/'+sr.dbId+'/invoice/preview', {
    method:'POST',
    headers:{'X-CSRF-TOKEN':CSRF,'Accept':'application/json','Content-Type':'application/json'},
    body: JSON.stringify(invPayload())
  })
  .then(function(r){
    if(!r.ok) return invJson(r);          // throws with the server's message
    return r.blob();
  })
  .then(function(blob){
    var url = URL.createObjectURL(blob);
    if(win){ win.location.href = url; }
    else {
      /* pop-up was blocked - download it instead */
      var a = document.createElement('a');
      a.href = url; a.download = 'invoice-preview.pdf';
      document.body.appendChild(a); a.click(); a.remove();
    }
    setTimeout(function(){ URL.revokeObjectURL(url); }, 60000);
  })
  .catch(function(e){
    if(win) win.close();
    showToast('err','Preview Failed', e.message);
  })
  .then(function(){ inv_validate(); });
}

/* ---------- GENERATE ---------- */
function openInvConfirm(){
  var err = invError();
  if(err){ invShowValMsg(err); return; }
  document.getElementById('inv-conf-sr').textContent  = selInv ? selInv.id : '';
  document.getElementById('inv-conf-amt').textContent = invMoney(invCalc().grand);
  document.getElementById('inv-conf-modal').classList.add('show');
}

function execInvSubmit(){
  document.getElementById('inv-conf-modal').classList.remove('show');
  var sr = selInv;
  if(!sr || invBusy) return;
  var err = invError();
  if(err){ invShowValMsg(err); return; }

  var btn = invEl('inv-btn');
  var btnHtml = btn.innerHTML;
  invBusy = true;
  btn.innerHTML = 'Generating…';
  inv_validate();

  fetch('/invoice_panel/'+sr.dbId+'/invoice', {
    method:'POST',
    headers:{'X-CSRF-TOKEN':CSRF,'Accept':'application/json','Content-Type':'application/json'},
    body: JSON.stringify(invPayload())
  })
  .then(invJson)
  .then(function(d){
    var no = (d && d.invoice_no) || '-';      // invoice number generated by the server
    invBusy = false;
    btn.innerHTML = btnHtml;

    var i = INV_QUEUE.findIndex(function(s){return s.id===sr.id;});
    if(i > -1) INV_QUEUE.splice(i,1);

    PENDING_HOP.push({
      id:'IA-'+sr.dbId, dbId:sr.dbId, sr:sr.id, client:sr.client, site:sr.site,
      code:no, pdfUrl:(d && d.pdf_url) || null, email:(d && d.customer_email) || '',
      total:(d && d.grand_total) || 0, submitted:'Just now', waiting:'0m',
      createdAt: sr.createdAt || new Date().toISOString().slice(0,10)
    });

    document.getElementById('inv-detail').style.display = 'none';
    document.getElementById('inv-success-title').textContent = sr.id+' - Invoice '+no+' Generated';
    document.getElementById('inv-success-body').textContent  = 'Invoice total '+invMoney(d && d.grand_total)+'. Head of Projects notified. Ticket now appears in Pending HoP Approval below.';
    var pdf = document.getElementById('inv-success-pdf');
    if(d && d.pdf_url){ pdf.href = d.pdf_url; pdf.style.display = ''; }
    else { pdf.style.display = 'none'; }
    document.getElementById('inv-success').classList.add('show');
    selInv = null;
    invFilter();
    showToast('ok','Invoice Generated',sr.id+' moved to Pending HoP Approval.');
  })
  .catch(function(e){
    /* nothing was changed on the server - the form is left as it was */
    invBusy = false;
    btn.innerHTML = btnHtml;
    inv_validate();
    showToast('err','Invoice Not Generated', e.message);
  });
}

function invNext(){document.getElementById('inv-success').classList.remove('show');document.getElementById('inv-empty').style.display='';}

/* ---------- HOP APPROVAL (+ email the invoice to the customer) ---------- */
var HOP_EMAIL_RE = /^[^\s@,;]+@[^\s@,;]+\.[^\s@,;]+$/;
var pendingHopId = null;
var hopBusy = false;

function hopItem(){ return PENDING_HOP.find(function(i){ return i.id === pendingHopId; }); }
function hopList(v){ return String(v || '').split(/[,;\s]+/).filter(Boolean); }

/* first problem with the email fields, or '' when the SR can be closed */
function hopError(){
  if(!invEl('hop-send').checked) return '';
  var from = invEl('hop-from').value.trim();
  if(!from) return 'Enter the From address.';
  if(!HOP_EMAIL_RE.test(from)) return 'The From address is not complete.';
  var to = hopList(invEl('hop-to').value);
  if(!to.length) return 'Enter the customer\'s email address.';
  var bad = to.filter(function(e){ return !HOP_EMAIL_RE.test(e); });
  if(bad.length) return 'This email address is not complete: ' + bad[0];
  if(!invEl('hop-subject').value.trim()) return 'Enter a subject.';
  var item = hopItem();
  if(item && !item.pdfUrl) return 'This SR has no invoice PDF to attach. Untick the email option to close it without sending.';
  return '';
}

function hopValidate(){
  var err  = hopError();
  var send = invEl('hop-send').checked;
  var btn  = invEl('hop-confirm');
  btn.disabled = !!err || hopBusy;
  if(!hopBusy){
    btn.innerHTML = send
      ? '<i class="bi bi-send-check-fill"></i> Approve, Close SR &amp; Send Invoice'
      : '<i class="bi bi-check-lg"></i> Confirm HoP Approval &amp; Close SR';
  }
  invEl('hop-need').textContent = err ? 'To continue: ' + err : '';
}

function hopToggleEmail(){
  invEl('hop-email').style.display = invEl('hop-send').checked ? '' : 'none';
  hopValidate();
}

function openHopModal(id, sr){
  pendingHopId = id;
  var item = hopItem() || {};
  var who  = (item.client && item.client !== '-') ? item.client : 'Team';
  var no   = (item.code && item.code !== '-') ? item.code : 'Invoice';

  invEl('hop-sr').textContent = sr;
  invEl('hop-send').checked   = true;
  invEl('hop-from').value     = HOP_FROM || '';
  invEl('hop-to').value       = item.email || '';
  invEl('hop-subject').value  = 'Invoice ' + no + ' for ' + sr + ' from ' + APP_NAME;
  invEl('hop-message').value  = 'Hello ' + who + ',\n\nThe work on your service request ' + sr + ' is complete. Please find the invoice attached'
    + (item.total ? ' for ' + invMoney(item.total) : '') + '.\n\nThank you for your business.';
  invEl('hop-pdf-name').textContent = no + '.pdf';
  invEl('hop-pdf').href = item.pdfUrl || '#';

  hopToggleEmail();
  invEl('hop-modal').classList.add('show');
  (invEl('hop-to').value ? invEl('hop-subject') : invEl('hop-to')).focus();
}

function closeHopModal(){
  if(hopBusy) return;                       // don't close while the email is going out
  invEl('hop-modal').classList.remove('show');
}

function execHopApproval(){
  var item = hopItem();
  if(!item || hopBusy) return;
  if(hopError()){ hopValidate(); return; }

  var send = invEl('hop-send').checked;
  var to   = hopList(invEl('hop-to').value);
  var payload = { send_email: send };
  if(send){
    payload.email_from    = invEl('hop-from').value.trim();
    payload.email_to      = to.join(', ');
    payload.email_subject = invEl('hop-subject').value.trim();
    payload.email_message = invEl('hop-message').value.trim() || null;
  }

  var btn = invEl('hop-confirm');
  hopBusy = true;
  btn.disabled = true;
  btn.innerHTML = send ? 'Sending…' : 'Closing…';

  fetch('/invoice_panel/'+item.dbId+'/hop-approve', {
    method:'POST',
    headers:{'X-CSRF-TOKEN': CSRF, 'Accept':'application/json', 'Content-Type':'application/json'},
    body: JSON.stringify(payload)
  })
  .then(invJson)
  .then(function(){
    hopBusy = false;
    closeHopModal();
    var idx = PENDING_HOP.findIndex(function(i){ return i.id === item.id; });
    if(idx > -1) PENDING_HOP.splice(idx,1);
    invFilter();
    showToast('ok','SR Closed', item.sr+' - Completed.' + (send ? ' Invoice emailed to '+to.join(', ')+'.' : ''));
  })
  .catch(function(e){
    /* nothing was changed on the server - the dialog stays open so it can be fixed and resent */
    hopBusy = false;
    hopValidate();
    showToast('err','Approval Failed', e.message);
  });
}

/* ---------- INIT ---------- */
document.addEventListener('DOMContentLoaded', function(){
  ['inv-search','inv-date','inv-date-to'].forEach(function(id){
    var el = document.getElementById(id);
    if(!el) return;
    el.addEventListener(el.type === 'date' ? 'change' : 'input', invFilter);
  });
  renderInvQueue();
  renderPH();
  document.addEventListener('keydown', function(e){ if(e.key === 'Escape') closeHopModal(); });
});
</script>
@endpush