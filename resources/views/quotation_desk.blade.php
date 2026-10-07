@extends('layouts.layout')

@section('title', 'Quotation Desk | Matter Mind')
@section('page_title', 'Quotation Desk')
@section('page_icon', 'database')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600;700&display=swap" rel="stylesheet"/>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet"/>
<style>
/* ===== Quotation Desk - scoped page styles ===== */
.qd-wrap{--gold:#9a8053;--gold-2:#b8975e;--queue-width:290px;}
.qd-wrap h4,.qd-wrap h5,.qd-wrap h6,.qd-wrap .pg-hdr-title,.qd-wrap .card-title,
.qd-wrap .ws-sr-id,.qd-wrap .qi-id,.qd-wrap .stat-num,.qd-wrap .pa-card-title{letter-spacing:-.01em;}

.qd-wrap .qi-id.sr-ref-trigger,
.qd-wrap .ws-sr-id.sr-ref-trigger,
.qd-wrap td.mono .sr-ref-trigger{ cursor:pointer; }

/* PAGE HEADER */
.qd-wrap .pg-header{background:linear-gradient(135deg,#9A7B4F 0%,#7A6140 100%);border-radius:10px;padding:20px 24px;margin-bottom:20px;color:#fff;position:relative;overflow:hidden;}
.qd-wrap .pg-header::before{content:'';position:absolute;left:-40px;bottom:-40px;width:180px;height:180px;border-radius:50%;background:rgba(255,255,255,.05);}
.qd-wrap .pg-header::after{content:'';position:absolute;right:-30px;top:-30px;width:160px;height:160px;border-radius:50%;background:rgba(255,255,255,.07);}
.qd-wrap .pg-hdr-title{font-size:1rem;font-weight:600;margin:0 0 3px;position:relative;z-index:1;}
.qd-wrap .pg-hdr-desc{font-size:.78rem;margin:0;opacity:.85;position:relative;z-index:1;}
.qd-wrap .pg-hdr-meta{display:flex;align-items:center;gap:8px;margin-top:9px;position:relative;z-index:1;flex-wrap:wrap;}
.qd-wrap .meta-badge{background:rgba(255,255,255,.18);border:1px solid rgba(255,255,255,.3);border-radius:20px;font-size:.6875rem;padding:2px 10px;font-weight:500;}

/* STATS */
.qd-wrap .stats-strip{display:grid;grid-template-columns:repeat(4,1fr);gap:10px;margin-bottom:18px;}
.qd-wrap .stat-card{background:var(--card-bg);border:1px solid var(--card-border);border-radius:9px;padding:13px 15px;display:flex;align-items:center;gap:12px;box-shadow:var(--card-shadow);}
.qd-wrap .stat-icon{width:38px;height:38px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:1rem;flex-shrink:0;}
.qd-wrap .stat-num{font-size:1.4rem;font-weight:700;line-height:1;color:var(--text-heading);}
.qd-wrap .stat-lbl{font-size:.7rem;color:var(--text-muted);}
@media(max-width:767px){.qd-wrap .stats-strip{grid-template-columns:repeat(2,1fr);}}
@media(max-width:420px){.qd-wrap .stats-strip{grid-template-columns:1fr;}}

/* FILTER BAR */
.qd-wrap .filter-bar{background:var(--card-bg);border:1px solid var(--card-border);border-radius:10px;padding:12px 16px;margin-bottom:16px;box-shadow:var(--card-shadow);display:flex;flex-wrap:wrap;gap:10px;align-items:flex-end;}
.qd-wrap .filter-group{display:flex;flex-direction:column;gap:4px;min-width: 0;flex: 1 1 auto;}
.qd-wrap .filter-label{font-size:.7rem;font-weight:600;color:var(--text-muted);text-transform:uppercase;letter-spacing:.05em;}
.qd-wrap .filter-control{height:34px;padding:0 10px;border:1px solid var(--border-color);border-radius:7px;background:var(--input-bg);color:var(--text-primary);font-size:.8rem;min-width:140px;transition:border-color .15s;}
.qd-wrap .filter-control:focus{outline:none;border-color:var(--gold);box-shadow:0 0 0 3px rgba(154,128,83,.12);}
.qd-wrap .filter-actions{margin-left:auto;display:flex;gap:8px;align-items:flex-end;}
@media(max-width:575.98px){
  .qd-wrap .filter-group{flex:1 1 100%;}
  .qd-wrap .filter-control{width:100%;min-width:0;}
  .qd-wrap .filter-actions{margin-left:0;width:100%;}
  .qd-wrap .filter-actions .btn-ghost{flex:1;justify-content:center;}
}

/* TWO-PANEL */
.qd-wrap .two-panel{display:grid;grid-template-columns:var(--queue-width) 1fr;gap:14px;align-items:start;}
@media(max-width:899px){.qd-wrap .two-panel{grid-template-columns:1fr;}}

/* QUEUE */
.qd-wrap .queue-panel{background:var(--card-bg);border:1px solid var(--card-border);border-radius:10px;box-shadow:var(--card-shadow);overflow:hidden;position:sticky;top:80px;}
@media(max-width:899px){.qd-wrap .queue-panel{position:static;}}
.qd-wrap .queue-hdr{padding:12px 16px;border-bottom:1px solid var(--border-color);display:flex;align-items:center;justify-content:space-between;}
.qd-wrap .queue-hdr-title{font-size:.8rem;font-weight:600;color:var(--text-heading);display:flex;align-items:center;gap:7px;}
.qd-wrap .q-count{font-size:.7rem;background:rgba(154,128,83,.12);color:var(--gold);padding:2px 8px;border-radius:9px;font-weight:700;}
.qd-wrap .queue-list{max-height:calc(100vh - 320px);overflow-y:auto;}
@media(max-width:899px){.qd-wrap .queue-list{max-height:none;}}
.qd-wrap .queue-list::-webkit-scrollbar{width:3px;}
.qd-wrap .queue-list::-webkit-scrollbar-thumb{background:var(--border-color);border-radius:2px;}
.qd-wrap .queue-item{padding:11px 15px;border-bottom:1px solid var(--border-color);cursor:pointer;transition:background .12s;}
.qd-wrap .queue-item:last-child{border-bottom:none;}
.qd-wrap .queue-item:hover{background:var(--surface-2);}
.qd-wrap .queue-item.active{background:rgba(154,128,83,.07);border-left:3px solid var(--gold);}
.qd-wrap .qi-id{font-size:.78rem;font-weight:700;color:var(--gold);margin-bottom:2px;}
.qd-wrap .qi-client{font-size:.79rem;font-weight:500;color:var(--text-heading);margin-bottom:1px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
.qd-wrap .qi-sub{font-size:.71rem;color:var(--text-muted);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;margin-bottom:5px;}
.qd-wrap .qi-foot{display:flex;align-items:center;justify-content:space-between;}
.qd-wrap .qi-time{font-size:.68rem;color:var(--text-muted);display:flex;align-items:center;gap:3px;}

/* WORKSPACE */
.qd-wrap .ws-panel{display:flex;flex-direction:column;gap:14px;min-width:0;}
.qd-wrap .ws-empty{background:var(--card-bg);border:1px solid var(--card-border);border-radius:10px;box-shadow:var(--card-shadow);padding:60px 24px;text-align:center;}
.qd-wrap .ws-empty-icon{width:56px;height:56px;border-radius:14px;background:var(--surface-2);display:flex;align-items:center;justify-content:center;margin:0 auto 14px;font-size:1.5rem;color:var(--text-light);}
.qd-wrap .ws-empty h6{font-size:.9rem;font-weight:600;color:var(--text-muted);margin-bottom:4px;}
.qd-wrap .ws-empty p{font-size:.78rem;color:var(--text-light);margin:0;}
.qd-wrap .ws-card{background:var(--card-bg);border:1px solid var(--card-border);border-radius:10px;box-shadow:var(--card-shadow);overflow:hidden;}
.qd-wrap .ws-card-hdr{padding:13px 18px;border-bottom:1px solid var(--border-color);display:flex;align-items:center;gap:10px;}
.qd-wrap .ws-card-icon{width:30px;height:30px;border-radius:7px;display:flex;align-items:center;justify-content:center;font-size:.85rem;flex-shrink:0;}
.qd-wrap .ws-card-hdr h6{font-size:.85rem;font-weight:600;color:var(--text-heading);margin:0;}
.qd-wrap .ws-card-body{padding:18px;}

/* SR header in workspace */
.qd-wrap .sr-hdr-card{background:var(--card-bg);border:1px solid var(--card-border);border-radius:10px;box-shadow:var(--card-shadow);padding:16px 20px;}
.qd-wrap .ws-sr-id{font-size:1rem;font-weight:700;color:var(--gold);margin-bottom:2px;}
.qd-wrap .ws-sr-client{font-size:.9rem;font-weight:600;color:var(--text-heading);margin-bottom:2px;}
.qd-wrap .ws-sr-site{font-size:.8rem;color:var(--text-muted);}
.qd-wrap .meta-chips{display:flex;gap:8px;flex-wrap:wrap;margin-top:12px;}
.qd-wrap .meta-chip{background:var(--surface-2);border-radius:7px;padding:7px 12px;}
.qd-wrap .meta-chip-label{font-size:.67rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--text-muted);margin-bottom:2px;}
.qd-wrap .meta-chip-value{font-size:.79rem;font-weight:600;color:var(--text-heading);}

/* FORM */
.qd-wrap .form-label-sm{font-size:.78rem;font-weight:600;color:var(--text-heading);margin-bottom:5px;display:block;}
.qd-wrap .form-label-sm .req{color:#ef4444;margin-left:2px;}
.qd-wrap .form-label-sm .opt{color:var(--text-muted);font-weight:400;margin-left:4px;}
.qd-wrap .form-group{margin-bottom:14px;}
.qd-wrap .form-group:last-child{margin-bottom:0;}
.qd-wrap .fc{width:100%;padding:8px 11px;border:1px solid var(--border-color);border-radius:7px;background:var(--input-bg);color:var(--text-primary);font-size:.8125rem;transition:border-color .15s,box-shadow .15s;}
.qd-wrap .fc:focus{outline:none;border-color:var(--gold);box-shadow:0 0 0 3px rgba(154,128,83,.12);}
.qd-wrap textarea.fc{resize:vertical;min-height:38px;font-family:inherit;line-height:1.45;}
.qd-wrap .field-hint{font-size:.72rem;color:var(--text-muted);margin-top:4px;}

/* QUOTE BUILDER */
.qd-wrap .qf-grid{display:grid;grid-template-columns:3fr 1fr;gap:14px;margin-bottom:16px;align-items:start;}
.qd-wrap .qf-grid .form-group{margin-bottom:0;min-width:0;}
@media(max-width:1099px){.qd-wrap .qf-grid{grid-template-columns:2fr 1fr;}}
@media(max-width:575.98px){.qd-wrap .qf-grid{grid-template-columns:1fr;}}
.qd-wrap .ql-num{text-align:right;font-variant-numeric:tabular-nums;}
.qd-wrap .q-totals{margin:16px 0 16px auto;max-width:380px;display:flex;flex-direction:column;gap:8px;}
.qd-wrap .q-tot-row{display:grid;grid-template-columns:104px 1fr;align-items:center;gap:12px;}
.qd-wrap .q-tot-lbl{font-size:.8rem;color:var(--text-heading);text-align:right;margin:0;}
.qd-wrap .q-tot-lbl .req{color:#ef4444;margin-left:2px;}
.qd-wrap .q-tot-val{padding:8px 11px;border:1px solid var(--border-color);border-radius:7px;background:var(--surface-2);text-align:right;font-size:.8125rem;font-weight:600;color:var(--text-heading);font-variant-numeric:tabular-nums;}
.qd-wrap .q-tot-grand .q-tot-lbl{font-weight:700;}
.qd-wrap .q-tot-grand .q-tot-val{font-size:.95rem;font-weight:700;color:var(--gold);background:rgba(154,128,83,.08);border-color:rgba(154,128,83,.3);}
.qd-wrap .q-disc-wrap{display:flex;gap:6px;}
.qd-wrap .q-disc-wrap select{width:70px;flex-shrink:0;padding-left:8px;padding-right:4px;}
.qd-wrap .q-disc-amt{font-size:.72rem;color:var(--text-muted);text-align:right;margin-top:3px;min-height:1em;font-variant-numeric:tabular-nums;}
.qd-wrap .q-need{font-size:.75rem;color:var(--text-muted);margin-bottom:10px;display:flex;align-items:center;gap:6px;}
.qd-wrap .q-need:empty{display:none;}
.qd-wrap .q-actions{display:flex;gap:10px;align-items:stretch;flex-wrap:wrap;}
.qd-wrap .q-actions .btn-submit{margin-top:0;flex:1 1 260px;width:auto;}
.qd-wrap .q-actions .btn-ghost{white-space:nowrap;justify-content:center;}
@media(max-width:575.98px){.qd-wrap .q-totals{max-width:none;}.qd-wrap .q-actions .btn-ghost{flex:1 1 100%;}}

/* TABS */
.qd-wrap .qd-tabs{display:flex;gap:6px;margin:22px 0 12px;border-bottom:1px solid var(--border-color);flex-wrap:wrap;}
.qd-wrap .qd-tab{display:inline-flex;align-items:center;gap:7px;padding:9px 16px;background:none;border:none;border-bottom:2px solid transparent;font-size:.8rem;font-weight:600;color:var(--text-muted);cursor:pointer;margin-bottom:-1px;transition:color .15s,border-color .15s;}
.qd-wrap .qd-tab:hover{color:var(--text-heading);}
.qd-wrap .qd-tab.active{color:var(--gold);border-bottom-color:var(--gold);}
.qd-wrap .qd-tab-count{font-size:.65rem;padding:1px 7px;border-radius:9px;font-weight:700;background:var(--surface-2);color:var(--text-muted);}
.qd-wrap .qd-tab.active .qd-tab-count{background:rgba(154,128,83,.12);color:var(--gold);}
.qd-wrap .qd-pane{display:none;}
.qd-wrap .qd-pane.active{display:block;}
.qd-wrap .sb-rej{background:rgba(239,68,68,.1);color:#ef4444;}
.qd-modal-overlay .btn-reject{flex:1;padding:9px;border:1px solid rgba(239,68,68,.3);background:rgba(239,68,68,.08);color:#ef4444;border-radius:7px;font-size:.82rem;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;gap:6px;}
.qd-modal-overlay .btn-reject:hover{background:rgba(239,68,68,.15);}

/* BUTTONS */
.qd-wrap .btn-gold{display:inline-flex;align-items:center;gap:6px;padding:7px 14px;background:linear-gradient(135deg,var(--gold),var(--gold-2));color:#fff;border:none;border-radius:7px;font-size:.8rem;font-weight:500;cursor:pointer;white-space:nowrap;}
.qd-wrap .btn-gold:hover{opacity:.87;}
.qd-wrap .btn-ghost{display:inline-flex;align-items:center;gap:6px;padding:7px 12px;background:var(--surface-2);color:var(--text-muted);border:1px solid var(--border-color);border-radius:7px;font-size:.8rem;cursor:pointer;}
.qd-wrap .btn-ghost:hover{background:var(--surface-3);}
.qd-wrap .btn-ghost:disabled{opacity:.45;cursor:not-allowed;}
.qd-wrap .btn-submit{width:100%;padding:12px;border:none;border-radius:8px;font-size:.875rem;font-weight:600;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px;transition:opacity .15s;margin-top:4px;}
.qd-wrap .btn-submit:hover{opacity:.88;}
.qd-wrap .btn-submit:disabled{opacity:.38;cursor:not-allowed;}
.qd-wrap .btn-amber{background:linear-gradient(135deg,#b45309,#d97706);color:#fff;}

/* PENDING APPROVAL SECTION */
.qd-wrap .section-divider{display:flex;align-items:center;gap:12px;margin:22px 0 14px;}
.qd-wrap .section-divider-line{flex:1;height:1px;background:var(--border-color);}
.qd-wrap .section-label{display:flex;align-items:center;gap:8px;font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--text-muted);white-space:nowrap;}
.qd-wrap .section-count{font-size:.65rem;padding:1px 7px;border-radius:9px;font-weight:700;}

/* APPROVAL TABLE */
.qd-wrap .pa-card{background:var(--card-bg);border:1px solid var(--card-border);border-radius:10px;box-shadow:var(--card-shadow);overflow:hidden;}
.qd-wrap .pa-card-hdr{display:flex;align-items:center;justify-content:space-between;padding:13px 18px;border-bottom:1px solid var(--border-color);flex-wrap:wrap;gap:8px;}
.qd-wrap .pa-card-hdr-left{display:flex;align-items:center;gap:10px;}
.qd-wrap .pa-card-icon{width:30px;height:30px;border-radius:7px;display:flex;align-items:center;justify-content:center;font-size:.85rem;flex-shrink:0;}
.qd-wrap .pa-card-title{font-size:.85rem;font-weight:600;color:var(--text-heading);}
.qd-wrap .pa-card-sub{font-size:.72rem;color:var(--text-muted);margin-top:1px;}
.qd-wrap table.pa-tbl{width:100%;border-collapse:collapse;min-width:740px;}
.qd-wrap table.pa-tbl thead th{padding:9px 14px;font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--text-muted);background:var(--surface-2);border-bottom:1px solid var(--border-color);white-space:nowrap;text-align:left;}
.qd-wrap table.pa-tbl tbody tr{border-bottom:1px solid var(--border-color);transition:background .1s;}
.qd-wrap table.pa-tbl tbody tr:last-child{border-bottom:none;}
.qd-wrap table.pa-tbl tbody tr:hover{background:var(--table-hover);}
.qd-wrap table.pa-tbl td{padding:11px 14px;font-size:.8rem;color:var(--text-primary);vertical-align:middle;}
.qd-wrap table.pa-tbl td.mono{font-size:.77rem;font-weight:600;color:var(--gold);}
.qd-wrap table.pa-tbl td.muted{color:var(--text-muted);font-size:.78rem;}
.qd-wrap .pa-empty{padding:30px;text-align:center;font-size:.8rem;color:var(--text-muted);}
.qd-wrap .pa-empty i{display:block;font-size:1.4rem;color:var(--text-light);margin-bottom:7px;}
.qd-wrap .pa-scroll{overflow-x:auto;-webkit-overflow-scrolling:touch;}

/* APPROVE BUTTON */
.qd-wrap .btn-mark{display:inline-flex;align-items:center;gap:5px;padding:5px 11px;border-radius:6px;font-size:.76rem;font-weight:600;cursor:pointer;border:1px solid;transition:opacity .15s;white-space:nowrap;}
.qd-wrap .btn-mark:hover{opacity:.82;}
.qd-wrap .btn-mark-green{background:rgba(154,128,83,.1);color:var(--gold);border-color:rgba(154,128,83,.28);}

/* BADGES */
.qd-wrap .sbadge{display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:20px;font-size:.7rem;font-weight:600;}
.qd-wrap .sb-oow{background:rgba(239,68,68,.1);color:#ef4444;}
.qd-wrap .sb-pq{background:rgba(139,92,246,.1);color:#7c3aed;}

/* SUCCESS */
.qd-wrap .ws-success{background:var(--card-bg);border:1px solid var(--card-border);border-radius:10px;box-shadow:var(--card-shadow);padding:48px 24px;text-align:center;display:none;}
.qd-wrap .ws-success.show{display:block;}
.qd-wrap .s-icon{width:60px;height:60px;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 14px;font-size:1.6rem;}

/* MODAL */
.qd-modal-overlay{display:none;position:fixed;inset:0;background:var(--overlay-bg,rgba(9,15,35,.62));z-index:900;align-items:center;justify-content:center;backdrop-filter:blur(4px);padding:20px;}
.qd-modal-overlay.show{display:flex;}
.qd-modal-overlay .modal-box{background:var(--modal-bg,#fff);border-radius:12px;width:100%;max-width:440px;box-shadow:var(--modal-shadow,0 24px 64px rgba(0,0,0,.16));border:1px solid var(--card-border);overflow:hidden;animation:qdMIn .18s ease;}
@keyframes qdMIn{from{opacity:0;transform:scale(.96);}to{opacity:1;transform:scale(1);}}
.qd-modal-overlay .modal-hdr{display:flex;align-items:center;justify-content:space-between;padding:15px 20px;border-bottom:1px solid var(--border-color);}
.qd-modal-overlay .modal-hdr-left{display:flex;align-items:center;gap:10px;}
.qd-modal-overlay .modal-hdr-icon{width:32px;height:32px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:.9rem;flex-shrink:0;}
.qd-modal-overlay .modal-hdr h6{font-size:.9rem;font-weight:600;color:var(--text-heading);margin:0;}
.qd-modal-overlay .modal-close{background:none;border:none;color:var(--text-muted);cursor:pointer;padding:4px;border-radius:5px;font-size:1rem;line-height:1;}
.qd-modal-overlay .modal-close:hover{background:var(--surface-2);}
.qd-modal-overlay .modal-body{padding:20px;}
.qd-modal-overlay .modal-foot{display:flex;gap:10px;padding:14px 20px;border-top:1px solid var(--border-color);background:var(--surface-2);}
.qd-modal-overlay .btn-confirm{flex:1;padding:9px;border:none;border-radius:7px;font-size:.82rem;font-weight:600;cursor:pointer;color:#fff;}
.qd-modal-overlay .btn-cancel{flex:1;padding:9px;background:var(--surface-2);color:var(--text-muted);border:1px solid var(--border-color);border-radius:7px;font-size:.82rem;cursor:pointer;}
.qd-modal-overlay .btn-confirm:disabled{opacity:.45;cursor:not-allowed;}

/* SEND QUOTATION DIALOG */
.qd-modal-overlay .modal-box.modal-wide{max-width:640px;display:flex;flex-direction:column;max-height:calc(100vh - 40px);}
.qd-modal-overlay .modal-wide .modal-body{overflow-y:auto;}
.qd-wrap .sq-row{display:grid;grid-template-columns:64px 1fr;gap:10px;align-items:center;margin-bottom:10px;}
.qd-wrap .sq-lbl{font-size:.8rem;color:var(--text-muted);margin:0;}
.qd-wrap .sq-lbl .req{color:#ef4444;margin-left:2px;}
.qd-wrap .sq-from{font-size:.8rem;color:var(--text-muted);padding:8px 11px;border:1px solid var(--border-color);border-radius:7px;background:var(--surface-2);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;}
.qd-wrap .sq-to{display:flex;gap:6px;align-items:center;min-width:0;}
.qd-wrap .sq-to .fc{min-width:0;}
.qd-wrap .sq-link{background:none;border:none;color:var(--gold);font-size:.75rem;font-weight:700;cursor:pointer;padding:4px 5px;border-radius:5px;}
.qd-wrap .sq-link:hover{background:var(--surface-2);}
.qd-wrap .sq-hint{font-size:.72rem;color:var(--text-muted);margin:-4px 0 10px 74px;}
.qd-wrap .sq-card{border:1px solid var(--border-color);border-radius:9px;overflow:hidden;margin:14px 0;}
.qd-wrap .sq-card-top{background:rgba(154,128,83,.08);text-align:center;padding:14px;}
.qd-wrap .sq-card-lbl{font-size:.78rem;font-weight:600;color:var(--text-heading);}
.qd-wrap .sq-card-amt{font-size:1.25rem;font-weight:700;color:var(--gold);font-variant-numeric:tabular-nums;}
.qd-wrap .sq-card-row{display:flex;justify-content:space-between;gap:12px;padding:8px 14px;border-top:1px solid var(--border-color);font-size:.78rem;color:var(--text-muted);}
.qd-wrap .sq-card-row strong{color:var(--text-heading);font-weight:600;text-align:right;}
.qd-wrap .sq-attach{display:flex;align-items:center;justify-content:space-between;gap:10px;flex-wrap:wrap;}
.qd-wrap .sq-check{display:inline-flex;align-items:center;gap:7px;font-size:.8rem;color:var(--text-heading);margin:0;cursor:pointer;}
.qd-wrap .sq-chip{display:inline-flex;align-items:center;gap:7px;padding:6px 10px;border:1px solid var(--border-color);border-radius:7px;background:var(--surface-2);font-size:.78rem;color:var(--text-heading);cursor:pointer;}
.qd-wrap .sq-chip:hover{border-color:var(--gold);}
.qd-wrap .sq-need{font-size:.75rem;color:var(--text-muted);margin-top:12px;min-height:1.1em;}
@media(max-width:575.98px){.qd-wrap .sq-row{grid-template-columns:1fr;gap:4px;}.qd-wrap .sq-hint{margin-left:0;}}

/* TOAST */
.qd-toast-wrap{position:fixed;bottom:22px;right:22px;z-index:9999;display:flex;flex-direction:column;gap:8px;pointer-events:none;max-width:calc(100vw - 44px);}
.qd-toast-wrap .toast-item{display:flex;align-items:flex-start;gap:10px;padding:12px 15px;border-radius:9px;background:var(--card-bg);border:1px solid var(--card-border);box-shadow:0 6px 24px rgba(0,0,0,.14);min-width:240px;max-width:320px;animation:qdToastIn .2s ease;pointer-events:auto;}
@keyframes qdToastIn{from{opacity:0;transform:translateY(10px);}to{opacity:1;transform:translateY(0);}}
.qd-toast-wrap .t-ico{font-size:1rem;flex-shrink:0;margin-top:1px;}
.qd-toast-wrap .t-ico.ok{color:#15803d;}.qd-toast-wrap .t-ico.err{color:#ef4444;}
.qd-toast-wrap .t-ico.info{color:var(--gold);}.qd-toast-wrap .t-ico.warn{color:#d97706;}
.qd-toast-wrap .t-title{font-size:.8rem;font-weight:600;color:var(--text-heading);margin:0 0 2px;}
.qd-toast-wrap .t-body{font-size:.75rem;color:var(--text-muted);margin:0;}
@media(max-width:575.98px){.qd-toast-wrap{left:12px;right:12px;bottom:12px;}.qd-toast-wrap .toast-item{max-width:none;}}

</style>
@endpush

@section('content')
<div class="qd-wrap">

  {{-- PAGE HEADER --}}
  <div class="pg-header">
    <h4 class="pg-hdr-title"><i class="bi bi-file-earmark-text me-2"></i>Out-of-Warranty Quotation Desk</h4>
    <p class="pg-hdr-desc">Prepare quotations for out-of-warranty SRs and generate the PDF here. Track customer approval status before routing to operations.</p>
    <div class="pg-hdr-meta">
      <span class="meta-badge"><i class="bi bi-shield-fill-check me-1"></i>Super Admin</span>
      <span class="meta-badge"><i class="bi bi-person-gear me-1"></i>Admin</span>
      <span class="meta-badge"><i class="bi bi-calculator me-1"></i>Accounts / AR</span>
    </div>
  </div>

  {{-- STATS --}}
  <div class="stats-strip">
    <div class="stat-card"><div class="stat-icon" style="background:rgba(139,92,246,.1);"><i class="bi bi-hourglass-split" style="color:#7c3aed;"></i></div><div><div class="stat-num" id="stat-pq">0</div><div class="stat-lbl">Pending Quotation</div></div></div>
    <div class="stat-card"><div class="stat-icon" style="background:rgba(245,158,11,.1);"><i class="bi bi-clock-history" style="color:#d97706;"></i></div><div><div class="stat-num" id="stat-pa">0</div><div class="stat-lbl">Pending Client Approval</div></div></div>
    <div class="stat-card"><div class="stat-icon" style="background:rgba(21,128,61,.1);"><i class="bi bi-check-circle" style="color:#15803d;"></i></div><div><div class="stat-num">{{ $clientApproved ?? 0 }}</div><div class="stat-lbl">Client Approved</div></div></div>
    <div class="stat-card"><div class="stat-icon" style="background:rgba(239,68,68,.1);"><i class="bi bi-x-circle" style="color:#ef4444;"></i></div><div><div class="stat-num">{{ $quoteRejected ?? 0 }}</div><div class="stat-lbl">Quote Rejected</div></div></div>
  </div>

  {{-- FILTER BAR --}}
  <div class="filter-bar">
    <div class="filter-group"><div class="filter-label">Search</div><input class="filter-control" type="text" id="q-search" placeholder="SR ID, customer name…"/></div>
    <div class="filter-group"><div class="filter-label">Date From</div><input class="filter-control" type="date" id="q-date-from"/></div>
    <div class="filter-group"><div class="filter-label">Date To</div><input class="filter-control" type="date" id="q-date-to"/></div>
    <div class="filter-actions"><button class="btn-ghost" onclick="qdResetFilters()"><i class="bi bi-x-circle"></i>Reset</button></div>
  </div>

  {{-- TWO-PANEL --}}
  <div class="two-panel">
    <div class="queue-panel">
      <div class="queue-hdr">
        <div class="queue-hdr-title"><i class="bi bi-list-ul" style="color:#9a8053;"></i>OoW Queue</div>
        <span class="q-count" id="q-count">0</span>
      </div>
      <div class="queue-list" id="q-list"></div>
    </div>
    <div class="ws-panel">
      <div class="ws-empty" id="q-empty">
        <div class="ws-empty-icon"><i class="bi bi-file-earmark-text"></i></div>
        <h6 style="font-family:unset;">Select a Ticket</h6>
        <p>Choose a pending OoW SR from the queue to prepare its quotation.</p>
      </div>
      <div class="ws-success" id="q-success">
        <div class="s-icon" style="background:rgba(154,128,83,.1);"><i class="bi bi-file-earmark-check" style="color:#9a8053;font-size:1.6rem;"></i></div>
        <h5 style="font-size:1rem;color:var(--text-heading);margin-bottom:6px;" id="q-success-title"></h5>
        <p style="font-size:.82rem;color:var(--text-muted);margin-bottom:18px;" id="q-success-body"></p>
        <button onclick="qdNext()" class="btn-gold"><i class="bi bi-arrow-right"></i>Next Ticket</button>
      </div>
      <div id="q-detail" style="display:none;flex-direction:column;gap:14px;">
        <div class="sr-hdr-card">
          <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:12px;margin-bottom:12px;flex-wrap:wrap;">
            <div><div class="ws-sr-id" id="q-sr-id"></div><div class="ws-sr-client" id="q-sr-client"></div><div class="ws-sr-site" id="q-sr-site"></div></div>
            <div style="display:flex;flex-direction:column;align-items:flex-end;gap:6px;">
              <span class="sbadge sb-pq"><i class="bi bi-circle-fill" style="font-size:.4rem;"></i>Pending Quote</span>
              <span class="sbadge sb-oow"><i class="bi bi-shield-exclamation"></i>Out of Warranty</span>
            </div>
          </div>
          <div class="meta-chips" id="q-chips"></div>
        </div>

        @if (auth()->user()?->role?->code !== 'HP')

        {{-- CREATE QUOTATION (replaces the old PDF upload card) --}}
        <div class="ws-card">
          <div class="ws-card-hdr">
            <div class="ws-card-icon" style="background:rgba(154,128,83,.1);"><i class="bi bi-receipt" style="color:#9a8053;"></i></div>
            <h6>Create Quotation</h6>
          </div>
          <div class="ws-card-body">

            {{-- Quotation details --}}
            <div class="qf-grid">
              <div class="form-group qf-wide">
                <label class="form-label-sm" for="q-summary">Summary <span class="req">*</span></label>
                <textarea class="fc" id="q-summary" rows="2" maxlength="500" placeholder="e.g. Compressor replacement and gas refill for split AC" oninput="q_validate()"></textarea>
              </div>
              <div class="form-group">
                <label class="form-label-sm" for="q-expiry">Expiry Date <span class="opt">(optional)</span></label>
                <input type="date" class="fc" id="q-expiry" onchange="q_validate()"/>
                <div class="field-hint">Quote is valid until this date.</div>
              </div>
            </div>

            {{-- Amount and totals --}}
            <div class="q-totals">
              <div class="q-tot-row">
                <label class="q-tot-lbl" for="q-amount">Amount (₹) <span class="req">*</span></label>
                <input type="number" class="fc ql-num" id="q-amount" min="0" step="0.01" placeholder="0.00" oninput="qlRefresh()"/>
              </div>
              <div class="q-tot-row">
                <label class="q-tot-lbl" for="q-disc">Discount</label>
                <div>
                  <div class="q-disc-wrap">
                    <input type="number" class="fc ql-num" id="q-disc" min="0" step="0.01" placeholder="0" oninput="qlRefresh()"/>
                    <select class="fc" id="q-disc-type" aria-label="Discount type" onchange="qlRefresh()">
                      <option value="percent">%</option>
                      <option value="flat">₹</option>
                    </select>
                  </div>
                  <div class="q-disc-amt" id="q-disc-amt"></div>
                </div>
              </div>
              <div class="q-tot-row">
                <label class="q-tot-lbl" for="q-adj">Adjustment</label>
                <div>
                  <input type="number" class="fc ql-num" id="q-adj" step="0.01" placeholder="0" oninput="qlRefresh()"/>
                  <div class="q-disc-amt">Use a minus sign to deduct.</div>
                </div>
              </div>
              <div class="q-tot-row q-tot-grand">
                <div class="q-tot-lbl">Grand Total</div>
                <div class="q-tot-val" id="q-grand">₹ 0.00</div>
              </div>
            </div>

            <div class="form-group" style="margin-bottom:16px;">
              <label class="form-label-sm" for="q-notes">Notes / Terms <span class="opt">(optional)</span></label>
              <textarea class="fc" id="q-notes" rows="2" maxlength="2000" placeholder="Payment terms, warranty on the work, exclusions…"></textarea>
              <div class="field-hint">Printed at the bottom of the quotation PDF.</div>
            </div>

            <div class="q-need" id="q-need"></div>
            <div id="q-val-msg" style="display:none;padding:8px 12px;border-radius:7px;background:rgba(239,68,68,.07);border:1px solid rgba(239,68,68,.2);color:#ef4444;font-size:.78rem;margin-bottom:10px;"></div>

            <div class="q-actions">
              <button type="button" class="btn-ghost" id="q-preview-btn" onclick="previewQuote()" disabled>
                <i class="bi bi-eye"></i>Preview PDF
              </button>
              <button type="button" class="btn-submit btn-amber" id="q-btn" onclick="openSendModal()" disabled>
                <i class="bi bi-send-check-fill"></i>Generate Quote PDF &amp; Send to Customer
              </button>
            </div>
          </div>
        </div>
        @endif
      </div>
    </div>
  </div>

  {{-- TABS: PENDING / REJECTED --}}
  <div class="qd-tabs">
    <button class="qd-tab active" id="tab-pending" onclick="qdTab('pending')">
      <i class="bi bi-clock-history"></i>Pending Customer Approval
      <span class="qd-tab-count" id="pa-count">0</span>
    </button>
    <button class="qd-tab" id="tab-rejected" onclick="qdTab('rejected')">
      <i class="bi bi-x-circle"></i>Quotation Rejected
      <span class="qd-tab-count" id="rj-count">0</span>
    </button>
  </div>

  {{-- PANE: PENDING --}}
  <div class="qd-pane active" id="pane-pending">
    <div class="pa-card">
      <div class="pa-card-hdr">
        <div class="pa-card-hdr-left">
          <div class="pa-card-icon" style="background:rgba(245,158,11,.1);"><i class="bi bi-hourglass-split" style="color:#d97706;"></i></div>
          <div><div class="pa-card-title">Awaiting Customer Response</div><div class="pa-card-sub">Quote sent to customer - mark approved or rejected when customer responds</div></div>
        </div>
        <div style="display:flex;align-items:center;gap:6px;font-size:.75rem;color:var(--text-muted);">
          <i class="bi bi-envelope-check" style="color:#9a8053;"></i>Quotation emailed to the customer on submission
        </div>
      </div>
      <div class="pa-scroll">
        <table class="pa-tbl">
          <thead>
            <tr><th>SR ID</th><th>Customer</th><th>Site</th><th>Quote Ref</th><th>Quote Submitted</th><th>Waiting</th><th style="text-align:center;width:160px;">Action</th></tr>
          </thead>
          <tbody id="pa-tbody"></tbody>
        </table>
      </div>
    </div>
  </div>

  {{-- PANE: REJECTED --}}
  <div class="qd-pane" id="pane-rejected">
    <div class="pa-card">
      <div class="pa-card-hdr">
        <div class="pa-card-hdr-left">
          <div class="pa-card-icon" style="background:rgba(239,68,68,.1);"><i class="bi bi-x-circle" style="color:#ef4444;"></i></div>
          <div><div class="pa-card-title">Quotations Rejected by Customer</div><div class="pa-card-sub">Quotes the customer declined - revise and re-submit if required</div></div>
        </div>
      </div>
      <div class="pa-scroll">
        <table class="pa-tbl">
          <thead>
            <tr><th>SR ID</th><th>Customer</th><th>Site</th><th>Quote Ref</th><th>Rejected On</th><th>Since</th><th style="text-align:center;width:120px;">Status</th></tr>
          </thead>
          <tbody id="rj-tbody"></tbody>
        </table>
      </div>
    </div>
  </div>
  {{-- APPROVE MODAL --}}
  <div class="qd-modal-overlay" id="qa-modal" onclick="if(event.target===this)this.classList.remove('show')">
    <div class="modal-box">
      <div class="modal-hdr">
        <div class="modal-hdr-left">
          <div class="modal-hdr-icon" style="background:rgba(154,128,83,.1);"><i class="bi bi-check-circle-fill" style="color:#9a8053;"></i></div>
          <h6>Mark Quote as customer Approved</h6>
        </div>
        <button class="modal-close" onclick="document.getElementById('qa-modal').classList.remove('show')"><i class="bi bi-x-lg"></i></button>
      </div>
      <div class="modal-body">
        <p style="font-size:.8rem;color:var(--text-muted);margin-bottom:14px;">Confirm customer has approved the quote for <strong id="qa-sr" style="color:var(--text-heading);"></strong>.</p>
       <div style="padding:12px 14px;border-radius:8px;background:rgba(154,128,83,.07);border:1px solid rgba(154,128,83,.2);display:flex;align-items:flex-start;gap:10px;margin-bottom:12px;">
          <i class="bi bi-arrow-right-circle-fill" style="color:#9a8053;flex-shrink:0;margin-top:2px;"></i>
          <div style="font-size:.8rem;color:#9a8053;">
            <strong>Status: Quoted → Approved</strong><br/>
            <span style="opacity:.8;font-size:.76rem;">SR becomes available in Head of Projects dispatch queue for technician assignment.</span>
          </div>
        </div>
        <div style="padding:9px 12px;background:rgba(154,128,83,.07);border:1px solid rgba(154,128,83,.2);border-radius:7px;font-size:.78rem;color:#9a8053;display:flex;align-items:center;gap:8px;">
          <i class="bi bi-whatsapp" style="color:#9a8053;"></i>WhatsApp confirmation dispatched to client stakeholders.
        </div>
      </div>
     <div class="modal-foot">
        <button class="btn-reject" onclick="execQRejection()"><i class="bi bi-x-circle"></i> Quotation Rejected</button>
        <button class="btn-confirm" style="background:linear-gradient(135deg,#9A7B4F,#7A6140);" onclick="execQApproval()"><i class="bi bi-check-lg"></i> Confirm Approval</button>
      </div>
    </div>
  </div>

  {{-- SEND QUOTATION DIALOG --}}
  @if (auth()->user()?->role?->code !== 'HP')
  <div class="qd-modal-overlay" id="sq-modal" onclick="if(event.target===this)closeSendModal()">
    <div class="modal-box modal-wide" role="dialog" aria-modal="true" aria-labelledby="sq-title">
      <div class="modal-hdr">
        <div class="modal-hdr-left">
          <div class="modal-hdr-icon" style="background:rgba(154,128,83,.1);"><i class="bi bi-envelope-paper" style="color:#9a8053;"></i></div>
          <h6 id="sq-title">Send Quotation</h6>
        </div>
        <button type="button" class="modal-close" onclick="closeSendModal()" aria-label="Close"><i class="bi bi-x-lg"></i></button>
      </div>
      <div class="modal-body">
        <div class="sq-row">
          <div class="sq-lbl">From</div>
          <div class="sq-from">{{ config('mail.from.address') }}</div>
        </div>
        <div class="sq-row">
          <label class="sq-lbl" for="sq-to">To <span class="req">*</span></label>
          <div class="sq-to">
            <input type="text" class="fc" id="sq-to" placeholder="customer@gmail.com" autocomplete="off" oninput="sqValidate()"/>
            <button type="button" class="sq-link" id="sq-cc-btn" onclick="sqShow('cc')">CC</button>
            <button type="button" class="sq-link" id="sq-bcc-btn" onclick="sqShow('bcc')">BCC</button>
          </div>
        </div>
        <div class="sq-hint">Separate several addresses with commas.</div>
        <div class="sq-row" id="sq-cc-row" style="display:none;">
          <label class="sq-lbl" for="sq-cc">CC</label>
          <input type="text" class="fc" id="sq-cc" autocomplete="off" oninput="sqValidate()"/>
        </div>
        <div class="sq-row" id="sq-bcc-row" style="display:none;">
          <label class="sq-lbl" for="sq-bcc">BCC</label>
          <input type="text" class="fc" id="sq-bcc" autocomplete="off" oninput="sqValidate()"/>
        </div>
        <div class="sq-row">
          <label class="sq-lbl" for="sq-subject">Subject <span class="req">*</span></label>
          <input type="text" class="fc" id="sq-subject" maxlength="200" oninput="sqValidate()"/>
        </div>

        <label class="form-label-sm" for="sq-message" style="margin-top:14px;">Message</label>
        <textarea class="fc" id="sq-message" rows="4" maxlength="5000"></textarea>

        <div class="sq-card">
          <div class="sq-card-top">
            <div class="sq-card-lbl">Quotation Amount</div>
            <div class="sq-card-amt" id="sq-amt">₹ 0.00</div>
          </div>
          <div class="sq-card-row"><span>Service Request</span><strong id="sq-sr"></strong></div>
          <div class="sq-card-row"><span>Customer</span><strong id="sq-client"></strong></div>
          <div class="sq-card-row"><span>Valid Until</span><strong id="sq-valid"></strong></div>
        </div>

        <div class="sq-attach">
          <label class="sq-check"><input type="checkbox" id="sq-attach" checked/>Attach quotation PDF</label>
          <button type="button" class="sq-chip" onclick="previewQuote()" title="Open the PDF">
            <i class="bi bi-file-earmark-pdf-fill" style="color:#ef4444;"></i>Quotation.pdf<i class="bi bi-eye"></i>
          </button>
        </div>
        <div class="sq-need" id="sq-need"></div>
      </div>
      <div class="modal-foot">
        <button type="button" class="btn-cancel" onclick="closeSendModal()">Cancel</button>
        <button type="button" class="btn-confirm" id="sq-send" style="background:linear-gradient(135deg,#9A7B4F,#7A6140);" onclick="submitQuote()"><i class="bi bi-send-fill"></i> Send</button>
      </div>
    </div>
  </div>
  @endif

  <div class="qd-toast-wrap" id="qdToastWrap"></div>
</div>
@include('partials.sr_tracking_modal')
@endsection

@push('scripts')
<script>
/* =========================================================
   Quotation Desk - page scripts
   Q_QUEUE row shape:
   { id, dbId, client, site, logged, createdAt, issue, email }
   PENDING_APPROVAL row shape:
   { id, sr, dbId, client, site, ref, submitted, waiting, createdAt }
   ========================================================= */
var Q_QUEUE          = @json($qQueue ?? []);
var PENDING_APPROVAL = @json($pendingApproval ?? []);
var CSRF             = '{{ csrf_token() }}';
var USER_ROLE        = '{{ auth()->user()?->role?->code }}';
var APP_NAME         = @json(config('app.name'));
/* filtered views - what actually gets rendered */
var Q_FILTERED  = Q_QUEUE.slice();
var PA_FILTERED = PENDING_APPROVAL.slice();

var selQ = null;

var REJECTED    = @json($rejectedQuotes ?? []);
var RJ_FILTERED = REJECTED.slice();
var QD_TAB      = 'pending';

/* ---------- TOAST ---------- */
function showToast(type,title,body){
  var w = document.getElementById('qdToastWrap');
  var icons = {ok:'bi-check-circle-fill',err:'bi-x-circle-fill',info:'bi-info-circle-fill',warn:'bi-exclamation-triangle-fill'};
  var t = document.createElement('div');
  t.className = 'toast-item';
  t.innerHTML = '<i class="bi '+(icons[type]||icons.info)+' t-ico '+type+'"></i><div><p class="t-title">'+title+'</p><p class="t-body">'+body+'</p></div>';
  w.appendChild(t);
  setTimeout(function(){t.style.transition='opacity .3s';t.style.opacity='0';setTimeout(function(){t.remove();},300);},3800);
}

/* ---------- SMALL HELPERS ---------- */
function qdNum(v){ var n = parseFloat(v); return isFinite(n) ? n : 0; }
function qdR2(n){ return Math.round((n + Number.EPSILON) * 100) / 100; }
function qdMoney(n){
  return '₹ ' + qdR2(n).toLocaleString('en-IN',{minimumFractionDigits:2,maximumFractionDigits:2});
}
function qdToday(){
  var d = new Date();
  function p(x){ return (x < 10 ? '0' : '') + x; }
  return d.getFullYear()+'-'+p(d.getMonth()+1)+'-'+p(d.getDate());
}
/* parse a JSON reply; turn non-2xx (and non-JSON error pages) into a readable Error */
function qdJson(r){
  return r.json().catch(function(){ return {}; }).then(function(d){
    if(!r.ok) throw new Error(d.message || ('Server error ('+r.status+')'));
    return d;
  });
}

/* ---------- QUOTATION TOTALS ---------- */
function qlCalc(){
  var sub   = qdR2(Math.max(0, qdNum(document.getElementById('q-amount').value)));
  var dVal  = Math.max(0, qdNum(document.getElementById('q-disc').value));
  var dType = document.getElementById('q-disc-type').value;
  var disc  = dType === 'percent' ? qdR2(sub * Math.min(dVal,100) / 100) : Math.min(qdR2(dVal), sub);
  var adj   = qdR2(qdNum(document.getElementById('q-adj').value));
  return {sub:sub, disc:disc, adj:adj, grand:qdR2(sub - disc + adj)};
}

function qlRefresh(){
  if(!document.getElementById('q-amount')) return;   // form is not rendered for the HP role
  var t = qlCalc();
  document.getElementById('q-disc-amt').textContent = t.disc > 0 ? '− ' + qdMoney(t.disc) : '';
  document.getElementById('q-grand').textContent    = qdMoney(t.grand);
  q_validate();
}

/* ---------- VALIDATION ---------- */
/* returns the first problem with the form, or '' when it is ready to send */
function qfError(){
  var sum = document.getElementById('q-summary');
  if(!sum) return 'Quotation form is not available.';
  if(!selQ) return 'Select a ticket from the queue.';
  if(sum.value.trim().length < 3) return 'Enter a summary.';
  if(qdNum(document.getElementById('q-amount').value) <= 0) return 'Enter the quotation amount.';
  var ex = document.getElementById('q-expiry').value;
  if(ex && ex < qdToday()) return 'Expiry date cannot be in the past.';
  if(qlCalc().grand < 0) return 'Grand total cannot be negative.';
  return '';
}

function q_validate(){
  var btn = document.getElementById('q-btn');
  if(!btn) return;
  var err = qfError();
  btn.disabled = !!err;
  var pv = document.getElementById('q-preview-btn');
  if(pv) pv.disabled = !!err;
  var need = document.getElementById('q-need');
  if(need) need.textContent = err ? 'To continue: ' + err : '';
}
window.q_validate = q_validate;

function qfReset(){
  if(!document.getElementById('q-amount')) return;   // form is not rendered for the HP role
  ['q-summary','q-expiry','q-notes','q-amount','q-disc','q-adj'].forEach(function(id){
    document.getElementById(id).value = '';
  });
  document.getElementById('q-disc-type').value = 'percent';
  document.getElementById('q-expiry').min = qdToday();
  document.getElementById('q-val-msg').style.display = 'none';
  qlRefresh();
}

function qfPayload(){
  return {
    summary:        document.getElementById('q-summary').value.trim(),
    expiry_date:    document.getElementById('q-expiry').value || null,
    notes:          document.getElementById('q-notes').value.trim() || null,
    discount_type:  document.getElementById('q-disc-type').value,
    discount_value: Math.max(0, qdNum(document.getElementById('q-disc').value)),
    adjustment:     qdNum(document.getElementById('q-adj').value),
    amount:         qdR2(qdNum(document.getElementById('q-amount').value))
  };
}

/* ---------- QUEUE ---------- */
function renderQQueue(list){
  list = list || Q_FILTERED;
  var ul = document.getElementById('q-list');
  document.getElementById('q-count').textContent = list.length;
  document.getElementById('stat-pq').textContent = Q_QUEUE.length;   // KPI = total, not filtered
  if(!list.length){
    ul.innerHTML = '<div style="padding:28px;text-align:center;font-size:.8rem;color:var(--text-muted);"><i class="bi bi-inbox" style="display:block;font-size:1.6rem;margin-bottom:8px;color:var(--text-light);"></i>No matching tickets</div>';
    return;
  }
  ul.innerHTML = list.map(function(sr){
    var ac = selQ && selQ.id===sr.id ? ' active':'';
    return '<div class="queue-item'+ac+'" data-id="'+sr.id+'" onclick="selectQ(this.dataset.id)">'+
      '<div class="qi-id sr-ref-trigger" data-sr-id="'+sr.dbId+'" onclick="event.stopPropagation(); openSrTracking('+sr.dbId+');">'+sr.id+'</div>'+
      '<div class="qi-client">'+sr.client+'</div>'+
      '<div class="qi-sub"><i class="bi bi-geo-alt" style="font-size:.7rem;"></i> '+sr.site+'</div>'+
      '<div class="qi-foot">'+
        '<span class="sbadge sb-oow" style="font-size:.65rem;">OoW</span>'+
        '<span class="qi-time"><i class="bi bi-clock" style="font-size:.65rem;"></i>'+(sr.logged||'')+'</span>'+
      '</div></div>';
  }).join('');
}

function selectQ(id){
  selQ = Q_QUEUE.find(function(s){return s.id===id;});
  if(!selQ) return;

  /* start a fresh quotation, with the SR's issue as the starting summary */
  qfReset();
  var sumEl = document.getElementById('q-summary');
  if(sumEl && selQ.issue) sumEl.value = String(selQ.issue).slice(0,500);

  q_validate();
  renderQQueue();
  document.getElementById('q-empty').style.display   = 'none';
  document.getElementById('q-success').classList.remove('show');
  document.getElementById('q-detail').style.display = 'flex';
  var qSrIdEl = document.getElementById('q-sr-id');
  qSrIdEl.textContent = selQ.id;
  qSrIdEl.classList.add('sr-ref-trigger');
  qSrIdEl.onclick = function(){ openSrTracking(selQ.dbId); };
  document.getElementById('q-sr-client').textContent = selQ.client;
  document.getElementById('q-sr-site').innerHTML     = '<i class="bi bi-geo-alt" style="color:#9a8053;font-size:.8rem;"></i> '+selQ.site;
  document.getElementById('q-chips').innerHTML =
    '<div class="meta-chip"><div class="meta-chip-label">Issue</div><div class="meta-chip-value" style="font-size:.78rem;font-weight:400;">'+(selQ.issue||'-')+'</div></div>'+
    '<div class="meta-chip"><div class="meta-chip-label">Logged</div><div class="meta-chip-value">'+(selQ.logged||'-')+'</div></div>'+
    '<div class="meta-chip"><div class="meta-chip-label">Scope</div><div class="meta-chip-value" style="color:#ef4444;">Out of Warranty</div></div>';
}

/* ---------- PENDING APPROVAL ---------- */
function renderPA(list){
  list = list || PA_FILTERED;
  var tbody = document.getElementById('pa-tbody');
  document.getElementById('pa-count').textContent = list.length;
  document.getElementById('stat-pa').textContent  = PENDING_APPROVAL.length;   // KPI = total
  if(!list.length){
    tbody.innerHTML = '<tr><td colspan="7"><div class="pa-empty"><i class="bi bi-inbox"></i><p>No quotes awaiting client approval</p></div></td></tr>';
    return;
  }
  var canDecide = USER_ROLE !== 'HP';
  tbody.innerHTML = list.map(function(item){
    var actionCell = canDecide
      ? '<button class="btn-mark btn-mark-green" data-id="'+item.id+'" data-sr="'+item.sr+'" onclick="openQAModal(this.dataset.id,this.dataset.sr)"><i class="bi bi-check-circle-fill"></i>Record Decision</button>'
      : '<span class="muted" style="font-size:.75rem;">-</span>';
    return '<tr>'+
      '<td class="mono"><span class="sr-ref-trigger" data-sr-id="'+item.dbId+'" onclick="openSrTracking('+item.dbId+');">'+item.sr+'</span></td>'+
      '<td style="font-weight:500;">'+item.client+'</td>'+
      '<td class="muted">'+item.site+'</td>'+
      '<td><span style="font-size:.77rem;font-weight:600;color:#9a8053;background:rgba(154,128,83,.08);padding:2px 7px;border-radius:4px;">'+item.ref+'</span></td>'+
      '<td class="muted">'+item.submitted+'</td>'+
      '<td><span style="font-size:.75rem;color:#d97706;display:inline-flex;align-items:center;gap:4px;"><i class="bi bi-clock"></i>'+item.waiting+'</span></td>'+
      '<td style="text-align:center;">'+actionCell+'</td>'+
    '</tr>';
  }).join('');
}

/* ---------- FILTERS ---------- */
function qdFilter(){
  var q    = document.getElementById('q-search').value.trim().toLowerCase();
  var from = document.getElementById('q-date-from').value;   // '' or 'YYYY-MM-DD'
  var to   = document.getElementById('q-date-to').value;

  function match(row){
    if (from && (!row.createdAt || row.createdAt < from)) return false;
    if (to   && (!row.createdAt || row.createdAt > to))   return false;
    if (q) {
      var hay = [row.id, row.sr, row.client, row.site, row.ref, row.issue]
        .filter(Boolean).join(' ').toLowerCase();
      if (hay.indexOf(q) === -1) return false;
    }
    return true;
  }

  Q_FILTERED  = Q_QUEUE.filter(match);
  PA_FILTERED = PENDING_APPROVAL.filter(match);
  RJ_FILTERED = REJECTED.filter(match);
  renderRJ(RJ_FILTERED);
  renderQQueue(Q_FILTERED);
  renderPA(PA_FILTERED);
}

function qdResetFilters(){
  document.getElementById('q-search').value = '';
  document.getElementById('q-date-from').value = '';
  document.getElementById('q-date-to').value = '';
  Q_FILTERED  = Q_QUEUE.slice();
  PA_FILTERED = PENDING_APPROVAL.slice();
  RJ_FILTERED = REJECTED.slice();
  renderRJ(RJ_FILTERED);
  renderQQueue(Q_FILTERED);
  renderPA(PA_FILTERED);
}

/* ---------- PREVIEW ---------- */
/* asks the server to render the PDF without saving or sending anything */
function previewQuote(){
  var err = qfError();
  if(err){ showValMsg('q',err); return; }
  var sr  = selQ;
  var btn = document.getElementById('q-preview-btn');
  btn.disabled = true;

  /* open the tab now, while we still have the click, so the browser doesn't block it */
  var win = window.open('', '_blank');

  fetch('/quotation_desk/'+sr.dbId+'/quote/preview', {
    method:'POST',
    headers:{'X-CSRF-TOKEN':CSRF,'Accept':'application/json','Content-Type':'application/json'},
    body: JSON.stringify(qfPayload())
  })
  .then(function(r){
    if(!r.ok) return qdJson(r);          // throws with the server's message
    return r.blob();
  })
  .then(function(blob){
    var url = URL.createObjectURL(blob);
    if(win){ win.location.href = url; }
    else {
      /* pop-up was blocked - download it instead */
      var a = document.createElement('a');
      a.href = url; a.download = 'quotation-preview.pdf';
      document.body.appendChild(a); a.click(); a.remove();
    }
    setTimeout(function(){ URL.revokeObjectURL(url); }, 60000);
  })
  .catch(function(e){
    if(win) win.close();
    showToast('err','Preview Failed', e.message);
  })
  .then(function(){ q_validate(); });
}

/* ---------- SEND DIALOG ---------- */
var SQ_EMAIL_RE = /^[^\s@,;]+@[^\s@,;]+\.[^\s@,;]+$/;
var sqFor = null;      // SR the dialog was last filled in for
var sqBusy = false;

function sqEl(id){ return document.getElementById(id); }
function sqList(v){ return String(v || '').split(/[,;\s]+/).filter(Boolean); }
function sqBad(v){ return sqList(v).filter(function(e){ return !SQ_EMAIL_RE.test(e); }); }
function sqDate(iso){
  if(!iso) return '-';
  var p = iso.split('-');
  return new Date(+p[0], +p[1]-1, +p[2]).toLocaleDateString('en-GB',{day:'2-digit',month:'short',year:'numeric'});
}

/* first problem with the email fields, or '' when it can be sent */
function sqError(){
  if(!sqEl('sq-to')) return 'Send dialog is not available.';
  if(!sqList(sqEl('sq-to').value).length) return 'Enter the customer\'s email address.';
  var bad = sqBad(sqEl('sq-to').value).concat(sqBad(sqEl('sq-cc').value), sqBad(sqEl('sq-bcc').value));
  if(bad.length) return 'This email address is not complete: ' + bad[0];
  if(!sqEl('sq-subject').value.trim()) return 'Enter a subject.';
  return '';
}

function sqValidate(){
  if(!sqEl('sq-send')) return;
  var err = sqError();
  sqEl('sq-send').disabled = !!err || sqBusy;
  sqEl('sq-need').textContent = err ? 'To send: ' + err : '';
}

function sqShow(which){
  sqEl('sq-'+which+'-row').style.display = '';
  sqEl('sq-'+which+'-btn').style.display = 'none';
  sqEl('sq-'+which).focus();
}

function openSendModal(){
  var err = qfError();
  if(err){ showValMsg('q',err); return; }
  if(!sqEl('sq-modal')) return;

  /* fill the email fields once per ticket, so reopening keeps what was typed */
  if(sqFor !== selQ.id){
    sqFor = selQ.id;
    var who = (selQ.client && selQ.client !== '-') ? selQ.client : 'Team';
    sqEl('sq-to').value      = selQ.email || '';
    sqEl('sq-cc').value      = '';
    sqEl('sq-bcc').value     = '';
    sqEl('sq-subject').value = 'Quotation for ' + selQ.id + ' from ' + APP_NAME;
    sqEl('sq-message').value = 'Hello ' + who + ',\n\nThank you for contacting us. Please find the quotation for your service request below. Kindly review it and let us know your approval.';
    sqEl('sq-attach').checked = true;
    ['cc','bcc'].forEach(function(k){
      sqEl('sq-'+k+'-row').style.display = 'none';
      sqEl('sq-'+k+'-btn').style.display = '';
    });
  }

  /* the summary card always shows the current figures */
  sqEl('sq-amt').textContent    = qdMoney(qlCalc().grand);
  sqEl('sq-sr').textContent     = selQ.id;
  sqEl('sq-client').textContent = selQ.client || '-';
  sqEl('sq-valid').textContent  = sqDate(sqEl('q-expiry').value);

  sqValidate();
  sqEl('sq-modal').classList.add('show');
  (sqEl('sq-to').value ? sqEl('sq-subject') : sqEl('sq-to')).focus();
}

function closeSendModal(){
  if(sqBusy) return;                       // don't close while the email is going out
  var m = sqEl('sq-modal');
  if(m) m.classList.remove('show');
}

/* ---------- SUBMIT ---------- */
/* Send button in the dialog: builds the PDF, emails it, then moves the ticket on */
function submitQuote(){
  var err = qfError();
  if(err){ closeSendModal(); showValMsg('q',err); return; }
  err = sqError();
  if(err){ sqValidate(); return; }
  if(sqBusy) return;

  var sr      = selQ;
  var to      = sqList(sqEl('sq-to').value);
  var payload = qfPayload();
  payload.email_to      = to.join(', ');
  payload.email_cc      = sqList(sqEl('sq-cc').value).join(', ')  || null;
  payload.email_bcc     = sqList(sqEl('sq-bcc').value).join(', ') || null;
  payload.email_subject = sqEl('sq-subject').value.trim();
  payload.email_message = sqEl('sq-message').value.trim() || null;
  payload.attach_pdf    = sqEl('sq-attach').checked;

  var send = sqEl('sq-send');
  var sendHtml = send.innerHTML;
  sqBusy = true;
  send.disabled = true;
  send.innerHTML = 'Sending…';
  document.getElementById('q-btn').disabled = true;

  fetch('/quotation_desk/'+sr.dbId+'/quote', {
    method:'POST',
    headers:{'X-CSRF-TOKEN':CSRF,'Accept':'application/json','Content-Type':'application/json'},
    body: JSON.stringify(payload)
  })
  .then(qdJson)
  .then(function(d){
    var ref = (d && d.ref) || '-';   // quotation number generated by the server

    sqBusy = false; sqFor = null;
    send.innerHTML = sendHtml;
    closeSendModal();

    var i = Q_QUEUE.findIndex(function(s){return s.id===sr.id;});
    if(i > -1) Q_QUEUE.splice(i,1);

    PENDING_APPROVAL.push({
      id:'PA-'+sr.dbId, dbId:sr.dbId, sr:sr.id, client:sr.client, site:sr.site,
      ref:ref, submitted:'Just now', waiting:'0m',
      createdAt: sr.createdAt || new Date().toISOString().slice(0,10)
    });

    document.getElementById('q-detail').style.display = 'none';
    document.getElementById('q-success-title').textContent = sr.id+' - Quotation Sent';
    document.getElementById('q-success-body').textContent  = 'Quotation '+ref+' was emailed to '+to.join(', ')+'.';
    document.getElementById('q-success').classList.add('show');
    selQ = null;
    showToast('ok','Quotation Sent',sr.id+' moved to Pending Client Approval.');
    qdFilter();
  })
  .catch(function(e){
    /* nothing was changed on the server - leave the dialog open so it can be fixed and resent */
    sqBusy = false;
    send.innerHTML = sendHtml;
    q_validate(); sqValidate();
    showToast('err','Quotation Not Sent', e.message);
  });
}

/* ---------- APPROVAL ---------- */
var pendingQAId = null;
function openQAModal(id,sr){pendingQAId=id;document.getElementById('qa-sr').textContent=sr;document.getElementById('qa-modal').classList.add('show');}

function execQApproval(){
  var idx  = PENDING_APPROVAL.findIndex(function(i){return i.id===pendingQAId;});
  var item = PENDING_APPROVAL[idx];
  document.getElementById('qa-modal').classList.remove('show');
  if(!item) return;

  fetch('/quotation_desk/'+item.dbId+'/approve', {
    method:'POST',
    headers:{'X-CSRF-TOKEN':CSRF,'Accept':'application/json'}
  })
  .then(function(r){ return r.json().then(function(d){ if(!r.ok) throw new Error(d.message||'Server error'); return d; }); })
  .then(function(){
    PENDING_APPROVAL.splice(idx,1);
    qdFilter();
    showToast('ok','Client Approved',item.sr+' - status set to Approved.');
  })
  .catch(function(e){ showToast('err','Approval Failed', e.message); });
}

function execQRejection(){
  var idx  = PENDING_APPROVAL.findIndex(function(i){return i.id===pendingQAId;});
  var item = PENDING_APPROVAL[idx];
  document.getElementById('qa-modal').classList.remove('show');
  if(!item) return;

  fetch('/quotation_desk/'+item.dbId+'/reject', {
    method:'POST',
    headers:{'X-CSRF-TOKEN':CSRF,'Accept':'application/json'}
  })
  .then(function(r){ return r.json().then(function(d){ if(!r.ok) throw new Error(d.message||'Server error'); return d; }); })
  .then(function(){
    PENDING_APPROVAL.splice(idx,1);
    REJECTED.unshift({
      id:'QR-'+item.dbId, dbId:item.dbId, sr:item.sr, client:item.client, site:item.site,
      ref:item.ref, rejected:'Just now', ago:'0m',
      createdAt:item.createdAt || new Date().toISOString().slice(0,10)
    });
    qdFilter();
    qdTab('rejected');
    showToast('warn','Quotation Rejected',item.sr+' - status set to Quote Rejected.');
  })
  .catch(function(e){ showToast('err','Rejection Failed', e.message); });
}
function qdNext(){document.getElementById('q-success').classList.remove('show');document.getElementById('q-empty').style.display='';}

function showValMsg(prefix,msg){
  var el = document.getElementById(prefix+'-val-msg');
  if(!el) return;
  el.textContent = msg; el.style.display = 'block';
  setTimeout(function(){el.style.display='none';},3500);
}

/* ---------- INIT ---------- */
document.addEventListener('DOMContentLoaded', function(){
  ['q-search','q-date-from','q-date-to'].forEach(function(id){
    var el = document.getElementById(id);
    if(!el) return;
    el.addEventListener(el.type === 'date' ? 'change' : 'input', qdFilter);
  });
  renderQQueue();
  renderPA();
  renderRJ();
  document.addEventListener('keydown', function(e){ if(e.key === 'Escape') closeSendModal(); });
});

function renderRJ(list){
  list = list || RJ_FILTERED;
  var tbody = document.getElementById('rj-tbody');
  document.getElementById('rj-count').textContent = list.length;
  if(!list.length){
    tbody.innerHTML = '<tr><td colspan="7"><div class="pa-empty"><i class="bi bi-inbox"></i><p>No rejected quotations</p></div></td></tr>';
    return;
  }
  tbody.innerHTML = list.map(function(item){
    return '<tr>'+
      '<td class="mono"><span class="sr-ref-trigger" data-sr-id="'+item.dbId+'" onclick="openSrTracking('+item.dbId+');">'+item.sr+'</span></td>'+
      '<td style="font-weight:500;">'+item.client+'</td>'+
      '<td class="muted">'+item.site+'</td>'+
      '<td><span style="font-size:.77rem;font-weight:600;color:#9a8053;background:rgba(154,128,83,.08);padding:2px 7px;border-radius:4px;">'+(item.ref||'-')+'</span></td>'+
      '<td class="muted">'+(item.rejected||'-')+'</td>'+
      '<td class="muted">'+(item.ago||'-')+'</td>'+
      '<td style="text-align:center;"><span class="sbadge sb-rej"><i class="bi bi-x-circle-fill" style="font-size:.65rem;"></i>Rejected</span></td>'+
    '</tr>';
  }).join('');
}

function qdTab(which){
  QD_TAB = which;
  document.getElementById('tab-pending').classList.toggle('active', which==='pending');
  document.getElementById('tab-rejected').classList.toggle('active', which==='rejected');
  document.getElementById('pane-pending').classList.toggle('active', which==='pending');
  document.getElementById('pane-rejected').classList.toggle('active', which==='rejected');
}
</script>
@endpush