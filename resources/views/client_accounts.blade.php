@extends('layouts.layout')

@section('title', 'Client Account Creation — Digit-Us Portal')
@section('page_title', 'Client Accounts')
@section('page_icon', 'building-add')

@php
  $isEdit = isset($client) && $client;
@endphp

@push('styles')
<style>
:root,[data-theme="light"]{
  --app-bg:#f4f6fb;--surface:#fff;--surface-2:#f0f3f9;--surface-3:#e8edf7;
  --card-bg:#fff;--card-border:#eaeef6;--modal-bg:#fff;--input-bg:#fff;
  --text-primary:#1a2236;--text-heading:#0d1626;--text-muted:#7987a1;
  --text-light:#b0bac9;--nav-link:#4a5568;--border-color:#e4e8f0;
  --card-shadow:0 2px 12px rgba(70, 80, 99, 0.09);
  --overlay-bg:rgba(9,15,35,.6);--modal-shadow:0 24px 64px rgba(0,0,0,.16);
  --table-row-hover:rgba(154,123,79,.04);--table-header:#f7f9fd;
  --input-focus-shadow:0 0 0 3px rgba(154,123,79,.12);
}
[data-theme="dark"]{
  --app-bg:#060d1c;--surface:#0c1427;--surface-2:#101e33;--surface-3:#141f34;
  --card-bg:#0d1829;--card-border:#16243d;--modal-bg:#0d1829;--input-bg:#101e33;
  --text-primary:#c8d4e8;--text-heading:#e4ecf8;--text-muted:#6b7fa0;
  --text-light:#3a4d66;--nav-link:#8aa0be;--border-color:#16243d;
  --card-shadow:0 2px 16px rgba(0,0,0,.4);
  --overlay-bg:rgba(0,0,0,.75);--modal-shadow:0 24px 64px rgba(0,0,0,.55);
  --table-row-hover:rgba(154,123,79,.07);--table-header:#101e33;
  --input-focus-shadow:0 0 0 3px rgba(154,123,79,.18);
}

.pg-header{background:linear-gradient(135deg,#9A7B4F 0%,#7A6140 100%);border-radius:10px;padding:20px 24px;margin-bottom:20px;color:#fff;position:relative;overflow:hidden;}
.pg-header::before{content:'';position:absolute;left:-40px;bottom:-40px;width:180px;height:180px;border-radius:50%;background:rgba(255,255,255,.05);}
.pg-header::after {content:'';position:absolute;right:-30px;top:-30px;width:160px;height:160px;border-radius:50%;background:rgba(255,255,255,.07);}
.pg-header h4{font-size:1rem;font-weight:600;margin:0 0 3px;position:relative;z-index:1;color:#fff;}
.pg-header p  {font-size:.78rem;margin:0;opacity:.85;position:relative;z-index:1;color:#fff;}
.pg-header .meta-row{display:flex;align-items:center;gap:8px;margin-top:10px;position:relative;z-index:1;flex-wrap:wrap;}
.meta-badge{background:rgba(255,255,255,.18);border:1px solid rgba(255,255,255,.3);border-radius:20px;font-size:.6875rem;padding:2px 10px;font-weight:500;}
@media(max-width:575.98px){.pg-header{padding:14px 16px;}.pg-header h4{font-size:.9rem;}}

.workspace{display:grid;grid-template-columns:1fr 320px;gap:18px;align-items:start;}
@media(max-width:1199.98px){.workspace{grid-template-columns:1fr 290px;}}
@media(max-width:991.98px){.workspace{grid-template-columns:1fr;}}

.card{background:var(--card-bg);border:1px solid var(--card-border);border-radius:10px;box-shadow:var(--card-shadow);overflow:hidden;margin-bottom:16px;transition:background .3s,border-color .3s;}
.card:last-child{margin-bottom:0;}
.card-hdr{padding:14px 20px;border-bottom:1px solid var(--card-border);display:flex;align-items:center;gap:10px;}
.card-hdr-icon{width:32px;height:32px;border-radius:7px;display:flex;align-items:center;justify-content:center;font-size:.9rem;flex-shrink:0;}
.card-hdr h6{margin:0;font-size:.875rem;font-weight:600;color:var(--text-heading);}
.card-hdr .csub{font-size:.7rem;color:var(--text-muted);display:block;margin-top:1px;}
.card-body{padding:20px;}
@media(max-width:575.98px){.card-hdr{padding:12px 14px;}.card-body{padding:14px;}}

.form-group{margin-bottom:16px;}
.form-group:last-child{margin-bottom:0;}
.form-label{font-size:.8rem;font-weight:500;color:var(--nav-link);margin-bottom:5px;display:flex;align-items:center;gap:5px;}
.form-label .req{color:#ff3366;font-size:.75rem;}
.form-label .auto-tag{font-size:.62rem;background:rgba(154,123,79,.1);color:#9A7B4F;padding:1px 6px;border-radius:4px;font-weight:600;margin-left:4px;}
.form-label .lock-tag{font-size:.62rem;background:rgba(120,135,161,.12);color:var(--text-muted);padding:1px 6px;border-radius:4px;font-weight:600;margin-left:4px;}
.form-label .hint-icon{color:var(--text-light);font-size:.75rem;cursor:help;}
.form-control,.form-select,.form-textarea{
  width:100%;font-size:.8125rem;border:1.5px solid var(--border-color);border-radius:7px;
  padding:.469rem .8rem;color:var(--text-primary);background:var(--input-bg);
  transition:border-color .15s,box-shadow .15s,background .3s;
}
.form-control:focus,.form-select:focus,.form-textarea:focus{border-color:#9A7B4F;box-shadow:var(--input-focus-shadow);outline:none;}
.form-control::placeholder,.form-textarea::placeholder{color:var(--text-light);}
.form-control.is-valid{border-color:#05a34a;}
.form-control.is-invalid{border-color:#ff3366;}
.form-control.checking{border-color:#fbbc06;}
.form-control:disabled,.form-control[readonly]{background:var(--surface-2);color:var(--text-muted);cursor:not-allowed;}
.form-textarea{resize:vertical;min-height:80px;}
[data-theme="dark"] .form-select option{background:#101e33;color:#c8d4e8;}

/* Full-width notify bar under the number */
.sh-notify-bar{
  grid-column:1/-1;
  display:flex;align-items:center;justify-content:space-between;
  gap:10px;margin-top:2px;padding:8px 12px;
  background:rgba(37,211,102,.06);
  border:1px solid rgba(37,211,102,.18);
  border-radius:8px;
}
.sh-notify-label{
  display:flex;align-items:center;gap:7px;
  font-size:.74rem;font-weight:500;color:var(--text-primary);
}
.sh-notify-label i{color:#25d366;font-size:.95rem;}

/* Premium toggle switch */
.sh-toggle{position:relative;display:inline-flex;flex-shrink:0;cursor:pointer;}
.sh-toggle input{position:absolute;opacity:0;width:0;height:0;}
.sh-toggle-track{
  width:38px;height:22px;border-radius:20px;
  background:var(--surface-3);border:1px solid var(--border-color);
  transition:background .2s,border-color .2s;position:relative;display:block;
}
.sh-toggle-thumb{
  position:absolute;top:50%;left:2px;transform:translateY(-50%);
  width:16px;height:16px;border-radius:50%;background:#fff;
  box-shadow:0 1px 3px rgba(0,0,0,.25);
  transition:left .2s;
}
.sh-toggle input:checked + .sh-toggle-track{
  background:linear-gradient(135deg,#25d366,#1eb355);
  border-color:#1eb355;
}
.sh-toggle input:checked + .sh-toggle-track .sh-toggle-thumb{left:18px;}
.sh-toggle input:focus-visible + .sh-toggle-track{box-shadow:0 0 0 3px rgba(37,211,102,.2);}
[data-theme="dark"] .sh-toggle-thumb{background:#e4ecf8;}

.input-icon-wrap{position:relative;}
.input-icon-wrap .form-control{padding-left:34px;}
.input-icon-wrap .ii{position:absolute;left:10px;top:50%;transform:translateY(-50%);color:var(--text-muted);font-size:.85rem;pointer-events:none;}
.input-right-icon{position:absolute;right:10px;top:50%;transform:translateY(-50%);font-size:.85rem;}
.check-spinner{display:none;}
.check-ok{color:#05a34a;display:none;}
.check-err{color:#ff3366;display:none;}
.field-msg{font-size:.7rem;margin-top:4px;display:none;}
.field-msg.ok{color:#05a34a;display:block;}
.field-msg.err{color:#ff3366;display:block;}
.field-msg.info{color:var(--text-muted);display:block;}

.auto-row{display:flex;gap:8px;align-items:center;}
.auto-row .form-control{flex:1;}
.btn-regen{background:var(--surface-2);border:1.5px solid var(--border-color);border-radius:7px;color:var(--text-muted);cursor:pointer;padding:.469rem .75rem;font-size:.78rem;display:inline-flex;align-items:center;gap:5px;white-space:nowrap;transition:all .15s;flex-shrink:0;}
.btn-regen:hover{border-color:#9A7B4F;color:#9A7B4F;background:rgba(154,123,79,.07);}
.btn-regen:disabled{opacity:.5;cursor:not-allowed;}

.phone-row{display:flex;gap:8px;}
.phone-country{width:90px;flex-shrink:0;}
.phone-number{flex:1;}

.sec-div{display:flex;align-items:center;gap:10px;margin:18px 0 14px;}
.sec-div hr{flex:1;border-color:var(--card-border);}
.sec-div span{font-size:.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--text-muted);white-space:nowrap;}

.stakeholder-list{display:flex;flex-direction:column;gap:8px;margin-bottom:10px;}
.sh-row{display:flex;gap:8px;align-items:flex-start;padding:10px 12px;background:var(--surface-2);border:1px solid var(--card-border);border-radius:8px;transition:border-color .15s;}
.sh-row:hover{border-color:rgba(154,123,79,.3);}
.sh-row.primary-sh{border-color:rgba(154,123,79,.35);background:rgba(154,123,79,.05);}
.sh-inputs{flex:1;display:grid;grid-template-columns:1fr 1fr;gap:8px;}
@media(max-width:575.98px){.sh-inputs{grid-template-columns:1fr;}}
.sh-label{font-size:.62rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--text-muted);display:block;margin-bottom:3px;}
.sh-input{width:100%;font-size:.78rem;border:1px solid var(--border-color);border-radius:6px;padding:.38rem .65rem;color:var(--text-primary);background:var(--input-bg);transition:border-color .15s,box-shadow .15s;}
.sh-input:focus{border-color:#9A7B4F;box-shadow:0 0 0 2px rgba(154,123,79,.1);outline:none;}
.sh-input::placeholder{color:var(--text-light);}
.sh-primary-badge{font-size:.6rem;font-weight:700;background:rgba(154,123,79,.15);color:#9A7B4F;padding:2px 7px;border-radius:10px;white-space:nowrap;align-self:flex-start;margin-top:2px;}
.sh-remove{margin-top: 0px;background:none;border:none;color:var(--text-light);cursor:pointer;font-size:.9rem;padding:4px;border-radius:5px;flex-shrink:0;transition:all .15s;align-self:center;}
.sh-remove:hover{color:#ff3366;background:rgba(255,51,102,.08);}
.btn-add-sh{background:none;border:1.5px dashed var(--border-color);border-radius:8px;width:100%;padding:.5rem;font-size:.78rem;color:var(--text-muted);cursor:pointer;display:flex;align-items:center;justify-content:center;gap:6px;transition:all .15s;}
.btn-add-sh:hover{border-color:#9A7B4F;color:#9A7B4F;background:rgba(154,123,79,.04);}

.ps-grid{margin-bottom:10px;}
.ps-row{background:var(--surface-2);border:1px solid var(--card-border);border-radius:9px;padding:14px;margin-bottom:10px;transition:border-color .2s,box-shadow .2s;position:relative;}
.ps-row:hover{border-color:rgba(154,123,79,.3);box-shadow:0 2px 10px rgba(154,123,79,.08);}
.ps-row.existing-row{border-color:rgba(5,163,74,.3);}
.ps-row.new-row{border-color:rgba(154,123,79,.4);animation:rowFadeIn .3s ease;}
@keyframes rowFadeIn{from{opacity:0;transform:translateY(-6px);}to{opacity:1;transform:none;}}
.ps-row-hdr{display:flex;align-items:center;justify-content:space-between;margin-bottom:10px;}
.ps-row-num{font-size:.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--text-muted);display:flex;align-items:center;gap:6px;}
.ps-row-num span{background:rgba(154,123,79,.1);color:#9A7B4F;border-radius:4px;padding:1px 7px;font-size:.65rem;}
.ps-row-num .saved-pill{background:rgba(5,163,74,.12);color:#05a34a;}
.ps-remove{background:none;border:none;color:var(--text-light);cursor:pointer;font-size:.9rem;padding:4px 6px;border-radius:5px;transition:all .15s;}
.ps-remove:hover{color:#ff3366;background:rgba(255,51,102,.08);}
.ps-fields{display:grid;grid-template-columns:1fr 1fr;gap:10px;}
@media(max-width:575.98px){.ps-fields{grid-template-columns:1fr;}}
.ps-label{font-size:.68rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--text-muted);display:block;margin-bottom:4px;}
.ps-input,.ps-textarea{width:100%;font-size:.8rem;border:1px solid var(--border-color);border-radius:6px;padding:.4rem .7rem;color:var(--text-primary);background:var(--input-bg);transition:border-color .15s,box-shadow .15s;}
.ps-input:focus,.ps-textarea:focus{border-color:#9A7B4F;box-shadow:0 0 0 2px rgba(154,123,79,.1);outline:none;}
.ps-input::placeholder,.ps-textarea::placeholder{color:var(--text-light);}
.ps-input[readonly]{background:var(--surface-3);cursor:not-allowed;color:var(--text-muted);}
.ps-textarea{resize:vertical;min-height:54px;grid-column:1/-1;}
.ps-full{grid-column:1/-1;}
.ps-code-row{display:flex;gap:6px;}
.ps-code-row .ps-input{flex:1;}
.ps-code-btn{background:var(--surface-3);border:1px solid var(--border-color);border-radius:6px;color:var(--text-muted);cursor:pointer;padding:.38rem .6rem;font-size:.75rem;display:flex;align-items:center;gap:4px;flex-shrink:0;transition:all .15s;white-space:nowrap;}
.ps-code-btn:hover{border-color:#9A7B4F;color:#9A7B4F;}
.btn-add-ps{background:none;border:1.5px dashed var(--border-color);border-radius:9px;width:100%;padding:.6rem;font-size:.8125rem;color:var(--text-muted);cursor:pointer;display:flex;align-items:center;justify-content:center;gap:7px;transition:all .15s;}
.btn-add-ps:hover{border-color:#9A7B4F;color:#9A7B4F;background:rgba(154,123,79,.04);}

.action-bar{display:flex;gap:10px;margin-top:20px;flex-wrap:wrap;}
.btn-save{background:linear-gradient(135deg,#9A7B4F,#7A6140);color:#fff;border:none;border-radius:8px;padding:.58rem 1.5rem;font-size:.875rem;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:7px;transition:all .2s;}
.btn-save:hover{box-shadow:0 4px 16px rgba(154,123,79,.4);}
.btn-save:active{transform:scale(.98);}
.btn-save:disabled{opacity:.55;cursor:not-allowed;box-shadow:none;}
.btn-reset{background:transparent;color:var(--text-muted);border:1.5px solid var(--border-color);border-radius:8px;padding:.58rem 1.2rem;font-size:.875rem;font-weight:500;cursor:pointer;display:inline-flex;align-items:center;gap:7px;transition:all .2s;text-decoration:none;}
.btn-reset:hover{border-color:var(--text-muted);color:var(--text-heading);}

.right-panel{position:sticky;top:80px;display:flex;flex-direction:column;gap:14px;}
@media(max-width:991.98px){.right-panel{position:static;}}
.summary-card{background:var(--card-bg);border:1px solid var(--card-border);border-radius:10px;box-shadow:var(--card-shadow);overflow:hidden;}
.sum-hdr{padding:13px 16px;border-bottom:1px solid var(--card-border);display:flex;align-items:center;gap:9px;}
.sum-hdr-ico{width:30px;height:30px;border-radius:6px;display:flex;align-items:center;justify-content:center;font-size:.85rem;flex-shrink:0;}
.sum-hdr h6{margin:0;font-size:.8375rem;font-weight:600;color:var(--text-heading);}
.sum-hdr .ssub{font-size:.7rem;color:var(--text-muted);display:block;}
.sum-body{padding:14px 16px;}
.sum-row{display:flex;justify-content:space-between;align-items:baseline;margin-bottom:8px;font-size:.78rem;}
.sum-row:last-child{margin-bottom:0;}
.sum-row .sk{color:var(--text-muted);}
.sum-row .sv{font-weight:500;color:var(--text-heading);text-align:right;max-width:58%;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;}
.sum-row .sv.placeholder{color:var(--text-light);font-weight:400;font-style:italic;}
hr.sum-hr{border-color:var(--card-border);margin:10px 0;}
.project-count-pill{background:rgba(154,123,79,.1);color:#9A7B4F;font-size:.7rem;font-weight:700;padding:3px 10px;border-radius:20px;}
.stakeholder-pill-list{display:flex;flex-direction:column;gap:5px;margin-top:4px;}
.sh-pill{display:flex;align-items:center;gap:6px;font-size:.72rem;color:var(--text-muted);}
.sh-pill i{color:#25d366;font-size:.85rem;}

.checklist{display:flex;flex-direction:column;gap:6px;}
.cl-item{display:flex;align-items:center;gap:8px;font-size:.75rem;}
.cl-icon{width:18px;height:18px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:.62rem;flex-shrink:0;}
.cl-done{background:rgba(5,163,74,.12);color:#05a34a;}
.cl-pending{background:var(--surface-2);color:var(--text-light);border:1px solid var(--border-color);}
.cl-text{color:var(--text-muted);}
.cl-text.done{color:var(--text-heading);}

.clients-tbl{width:100%;border-collapse:collapse;}
.clients-tbl thead th{font-size:.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--text-muted);padding:9px 12px;border-bottom:1px solid var(--card-border);background:var(--table-header);white-space:nowrap;}
.clients-tbl tbody tr{border-bottom:1px solid var(--card-border);transition:background .15s;cursor:pointer;}
.clients-tbl tbody tr:last-child{border-bottom:none;}
.clients-tbl tbody tr:hover{background:var(--table-row-hover);}
.clients-tbl tbody td{padding:10px 12px;font-size:.78rem;vertical-align:middle;}
.tbl-client-name{font-weight:500;color:var(--text-heading);}
.tbl-token{font-size:.72rem;color:#9A7B4F;font-weight:600;}
.tbl-wrap{overflow-x:auto;-webkit-overflow-scrolling:touch;}
.status-dot{width:7px;height:7px;border-radius:50%;display:inline-block;margin-right:5px;}
.dot-active{background:#05a34a;}
.dot-inactive{background:#aeb7c5;}
.tbl-empty{text-align:center;padding:18px;color:var(--text-light);font-size:.75rem;font-style:italic;}

.modal-overlay{display:none;position:fixed;inset:0;background:var(--overlay-bg);z-index:9000;align-items:center;justify-content:center;padding:16px;}
.modal-overlay.show{display:flex;}
.modal-box{background:var(--modal-bg);border-radius:12px;border:1px solid var(--card-border);max-width:420px;width:100%;box-shadow:var(--modal-shadow);animation:popIn .25s ease;overflow:hidden;}
@keyframes popIn{from{transform:scale(.88);opacity:0;}to{transform:none;opacity:1;}}
.modal-hdr{padding:15px 18px;border-bottom:1px solid var(--card-border);display:flex;align-items:center;justify-content:space-between;}
.modal-hdr h6{margin:0;font-size:.9rem;font-weight:600;color:var(--text-heading);}
.modal-close{background:none;border:none;color:var(--text-muted);cursor:pointer;font-size:1.1rem;padding:4px;border-radius:4px;line-height:1;text-decoration:none;}
.modal-close:hover{color:var(--text-heading);background:var(--surface-2);}
.modal-body-c{padding:20px;text-align:center;}
.modal-icon-ring{width:56px;height:56px;border-radius:50%;background:rgba(154,123,79,.1);display:flex;align-items:center;justify-content:center;font-size:1.6rem;color:#9A7B4F;margin:0 auto 14px;}
.modal-title-t{font-size:.9375rem;font-weight:600;color:var(--text-heading);margin-bottom:6px;}
.modal-sub-t{font-size:.78rem;color:var(--text-muted);line-height:1.5;}
.modal-token-box{background:var(--surface-2);border:1px solid var(--card-border);border-radius:7px;padding:10px 14px;margin:14px 0;font-size:.875rem;font-weight:700;color:#9A7B4F;letter-spacing:.06em;}
.modal-ftr{padding:13px 18px;border-top:1px solid var(--card-border);display:flex;gap:10px;justify-content:flex-end;}
.btn-modal-close{background:var(--surface-2);border:1px solid var(--border-color);border-radius:6px;padding:.42rem 1rem;font-size:.8rem;cursor:pointer;color:var(--text-muted);transition:all .15s;text-decoration:none;}
.btn-modal-close:hover{border-color:var(--text-muted);color:var(--text-heading);}
.btn-modal-ok{background:linear-gradient(135deg,#9A7B4F,#C4A882);border:none;border-radius:6px;padding:.42rem 1.2rem;font-size:.8rem;font-weight:600;cursor:pointer;color:#fff;display:flex;align-items:center;gap:6px;transition:all .15s;text-decoration:none;}
.btn-modal-ok:hover{box-shadow:0 4px 14px rgba(154,123,79,.35);}

.toast-wrap{position:fixed;top:70px;right:16px;z-index:9999;display:flex;flex-direction:column;gap:8px;max-width:310px;}
.toast-item{background:var(--card-bg);border-left:4px solid #9A7B4F;border-radius:7px;padding:12px 14px;box-shadow:0 6px 24px rgba(0,0,0,.18);display:flex;align-items:flex-start;gap:10px;animation:toastIn .3s ease;}
.toast-item.success{border-color:#05a34a;}.toast-item.error{border-color:#ff3366;}.toast-item.warning{border-color:#fbbc06;}
@keyframes toastIn{from{transform:translateX(40px);opacity:0;}to{transform:none;opacity:1;}}
.ti-icon{font-size:1.1rem;flex-shrink:0;margin-top:1px;}
.ti-icon.primary{color:#9A7B4F;}.ti-icon.success{color:#05a34a;}.ti-icon.error{color:#ff3366;}.ti-icon.warning{color:#fbbc06;}
.ti-title{font-size:.8125rem;font-weight:600;margin:0 0 2px;color:var(--text-heading);}
.ti-body{font-size:.72rem;margin:0;color:var(--text-muted);}
@media(max-width:575.98px){.toast-wrap{left:12px;right:12px;max-width:none;}}
</style>
@endpush

@section('content')

<div class="toast-wrap" id="toastWrap"></div>

{{-- Success Modal --}}
<div class="modal-overlay {{ session('success') ? 'show' : '' }}" id="successModal">
  <div class="modal-box">
    <div class="modal-hdr">
      <h6>Client Account Saved</h6>
      <a href="{{ route('clients.create') }}" class="modal-close"><i class="bi bi-x-lg"></i></a>
    </div>
    <div class="modal-body-c">
      <div class="modal-icon-ring"><i class="bi bi-person-check-fill"></i></div>
      <div class="modal-title-t">Saved Successfully</div>
      <div class="modal-sub-t">The client account has been saved and the lookup index refreshed.</div>
      <div class="modal-token-box">{{ session('saved_token', 'CUST-——') }}</div>
    </div>
    <div class="modal-ftr">
      <a href="{{ route('clients.directory') }}" class="btn-modal-close">Close</a>
      <a href="{{ route('clients.create') }}" class="btn-modal-ok"><i class="bi bi-plus-lg"></i>Add Another</a>
    </div>
  </div>
</div>

<div class="pg-header">
  <h4><i class="bi bi-building-add me-2"></i>{{ $isEdit ? 'Edit Client Account' : 'Client Account Creation' }}</h4>
  <p>{{ $isEdit ? 'Update firm name, contact details, and projects. Only the unique code and project code are locked.' : 'Register enterprise clients, assign identification tokens, link projects, and configure stakeholder contacts.' }}</p>
  <div class="meta-row">
    <span class="meta-badge"><i class="bi bi-person-badge me-1"></i>Front Desk</span>
    <span class="meta-badge"><i class="bi bi-person-gear me-1"></i>Admin</span>
    <span class="meta-badge"><i class="bi bi-shield-fill-check me-1"></i>Super Admin</span>
  </div>
</div>
<form method="POST"
      action="{{ $isEdit ? route('clients.update', $client) : route('clients.store') }}"
      id="clientForm"
      data-store-url="{{ route('clients.store') }}"
      data-update-url-base="{{ url('clients') }}">
  @csrf
  @if($isEdit) @method('PUT') @endif
  <div class="workspace">

    {{-- ══ LEFT: FORM ══ --}}
    <div>

      {{-- SECTION 1: Client Identity --}}
      <div class="card">
        <div class="card-hdr">
          <div class="card-hdr-icon" style="background:rgba(154,123,79,.1);"><i class="bi bi-building" style="color:#9A7B4F;"></i></div>
          <div><h6>Client Identity</h6><span class="csub">{{ $isEdit ? 'Firm name editable — unique code locked' : 'Firm name and unique identification token' }}</span></div>
        </div>
        <div class="card-body">

          <div class="form-group">
            <label class="form-label">Client Firm Name <span class="req">*</span></label>
            <div class="input-icon-wrap">
              <i class="bi bi-building ii"></i>
              <input type="text" class="form-control" id="firmName" name="company_name"
                value="{{ old('company_name', $isEdit ? $client->company_name : '') }}"
                placeholder="e.g. Skyline Technologies Pvt Ltd" oninput="onFirmNameInput()" />
            </div>
            <div class="field-msg info" id="firmNameMsg">Enter the registered legal name of the enterprise.</div>
          </div>

          <div class="form-group">
            <label class="form-label">
              Unique Client Identification Token <span class="req">*</span>
              <span class="auto-tag">AUTO</span>@if($isEdit)<span class="lock-tag">LOCKED</span>@endif
            </label>
            <div class="auto-row">
              <div style="flex:1;position:relative;">
                <input type="text" class="form-control" id="clientToken" name="unique_code"
                       value="{{ old('unique_code', $suggestedCode ?? '') }}" placeholder="CUST-XXXX-000" maxlength="20"
                       style="font-weight:600;letter-spacing:.04em;text-transform:uppercase;font-size:.82rem;padding-right:40px;"
                       {{ $isEdit ? 'readonly' : '' }}/>
                <span class="input-right-icon">
                <div class="check-spinner" id="tokenSpinner"><div class="spinner-border" style="width:14px;height:14px;border-width:2px;color:#fbbc06;" role="status"></div></div>
                  <i class="bi bi-check-circle-fill check-ok" id="tokenOk"></i>
                  <i class="bi bi-x-circle-fill check-err" id="tokenErr"></i>
                </span>
              </div>
              <button type="button" class="btn-regen" onclick="regenToken()" title="Regenerate token" {{ $isEdit ? 'disabled' : '' }}>
                <i class="bi bi-arrow-repeat"></i>Regenerate
              </button>
            </div>
            <div class="field-msg" id="tokenMsg"></div>
          </div>

        </div>
      </div>

      {{-- SECTION 2: Primary Contact --}}
      <div class="card">
        <div class="card-hdr">
          <div class="card-hdr-icon" style="background:rgba(154,123,79,.1);"><i class="bi bi-person-lines-fill" style="color:#9A7B4F;"></i></div>
          <div><h6>Primary Contact</h6><span class="csub">Main point of contact for this client</span></div>
        </div>
        <div class="card-body">

          @php
            $primaryCountry = $isEdit ? $client->primary_country : '';
            $primaryMobile  = $isEdit ? $client->primary_mobile  : '';
          @endphp

          <div class="row g-3">
            <div class="col-sm-6">
              <div class="form-group" style="margin-bottom:0;">
                <label class="form-label">Contact Name <span class="req">*</span></label>
                <div class="input-icon-wrap">
                  <i class="bi bi-person ii"></i>
                  <input type="text" class="form-control" id="contactName" name="contact_name"
                         value="{{ old('contact_name', $isEdit ? $client->contact_name : '') }}" placeholder="Full name" oninput="syncSummary()"/>
                </div>
              </div>
            </div>
            <div class="col-sm-6">
              <div class="form-group" style="margin-bottom:0;">
                <label class="form-label">Designation</label>
                <input type="text" class="form-control" id="designation" name="designation"
                       value="{{ old('designation', $isEdit ? $client->designation : '') }}" placeholder="e.g. Facilities Manager"/>
              </div>
            </div>
          </div>

         <br />

          <div class="form-group" style="margin-bottom:0;">
            <label class="form-label">Primary Contact Mobile <span class="req">*</span></label>
            <div class="phone-row">
              <select class="form-select phone-country" id="primaryCountry" name="primary_country">
                <option value="+971" @selected($primaryCountry==='+971')>🇦🇪 +971</option>
                <option value="+91"  @selected($primaryCountry==='+91')>🇮🇳 +91</option>
                <option value="+1"   @selected($primaryCountry==='+1')>🇺🇸 +1</option>
                <option value="+44"  @selected($primaryCountry==='+44')>🇬🇧 +44</option>
                <option value="+966" @selected($primaryCountry==='+966')>🇸🇦 +966</option>
                <option value="+974" @selected($primaryCountry==='+974')>🇶🇦 +974</option>
                <option value="+965" @selected($primaryCountry==='+965')>🇰🇼 +965</option>
                <option value="+973" @selected($primaryCountry==='+973')>🇧🇭 +973</option>
                <option value="+968" @selected($primaryCountry==='+968')>🇴🇲 +968</option>
              </select>
              <div class="input-icon-wrap phone-number">
                <i class="bi bi-phone ii"></i>
                <input type="tel" class="form-control" id="primaryMobile" name="primary_mobile"
                       value="{{ old('primary_mobile', $primaryMobile) }}" placeholder="50 123 4567"
                       oninput="validatePhone(this,'primaryPhoneMsg')" maxlength="15"/>
              </div>
            </div>
            <div class="field-msg" id="primaryPhoneMsg"></div>
            <div style="font-size:.68rem;color:var(--text-muted);margin-top:4px;display:flex;align-items:center;gap:4px;">
              <i class="bi bi-whatsapp" style="color:#25d366;"></i>This number receives all WhatsApp ticket updates.
            </div>
          </div>

        </div>
      </div>

      {{-- SECTION 3: WhatsApp Stakeholders --}}
      <div class="card">
        <div class="card-hdr">
          <div class="card-hdr-icon" style="background:rgba(37,211,102,.1);"><i class="bi bi-whatsapp" style="color:#25d366;"></i></div>
          <div><h6>WhatsApp Contacts</h6><span class="csub">Additional contacts for ticket notifications</span></div>
        </div>
        <div class="card-body">
          <div style="font-size:.72rem;color:var(--text-muted);background:rgba(37,211,102,.06);border:1px solid rgba(37,211,102,.18);border-radius:7px;padding:8px 11px;display:flex;align-items:flex-start;gap:7px;margin-bottom:14px;">
            <i class="bi bi-info-circle" style="color:#25d366;flex-shrink:0;margin-top:1px;"></i>
            Check numbers below will receive WhatsApp notifications for ticket events.
          </div>
          <div class="stakeholder-list" id="stakeholderList"></div>
          <button type="button" class="btn-add-sh" onclick="addStakeholder()">
            <i class="bi bi-plus-lg"></i>Add WhatsApp Contact
          </button>
        </div>
      </div>

      {{-- SECTION 4: Projects --}}
      <div class="card">
        <div class="card-hdr">
          <div class="card-hdr-icon" style="background:rgba(154,123,79,.1);"><i class="bi bi-diagram-3-fill" style="color:#9A7B4F;"></i></div>
          <div><h6>Projects &amp; Sites</h6><span class="csub">Each project has one site location</span></div>
        </div>
        <div class="card-body">
          <div style="font-size:.72rem;color:var(--text-muted);margin-bottom:14px;">
            {{ $isEdit ? 'Existing projects are pre-filled and editable. Add more below.' : 'Click Add Project to append an entry.' }}
          </div>
          <div class="ps-grid" id="psGrid"></div>
          <button type="button" class="btn-add-ps" onclick="addProject()">
            <i class="bi bi-plus-lg"></i>Add Project
          </button>
        </div>
      </div>

      <div class="action-bar">
        <button type="submit" class="btn-save" id="saveBtn">
          <i class="bi bi-floppy-fill"></i>{{ $isEdit ? 'Update Client Account' : 'Save Client Account' }}
        </button>
        <a href="{{ route('clients.create') }}" class="btn-reset">
          <i class="bi bi-arrow-counterclockwise"></i>{{ $isEdit ? 'Cancel / New' : 'Reset Form' }}
        </a>
      </div>

    </div>{{-- /left --}}

    {{-- ══ RIGHT: SUMMARY + LOOKUP ══ --}}
    <div class="right-panel">

      <div class="summary-card">
        <div class="sum-hdr">
          <div class="sum-hdr-ico" style="background:rgba(154,123,79,.1);"><i class="bi bi-eye" style="color:#9A7B4F;"></i></div>
          <div><h6>Account Preview</h6><span class="ssub">Live form summary</span></div>
        </div>
        <div class="sum-body">
          <div class="sum-row"><span class="sk">Firm Name</span><span class="sv placeholder" id="sumFirm">Not entered</span></div>
          <div class="sum-row"><span class="sk">Token</span><span class="sv placeholder" id="sumToken">—</span></div>
          <div class="sum-row"><span class="sk">Contact</span><span class="sv placeholder" id="sumContact">Not entered</span></div>
          <hr class="sum-hr"/>
          <div class="sum-row"><span class="sk">Projects</span><span class="sv" id="sumProjects"><span class="project-count-pill">0</span></span></div>
          <div class="sum-row" style="align-items:flex-start;margin-bottom:0;">
            <span class="sk" style="margin-top:3px;">WhatsApp Contact</span>
            <div class="stakeholder-pill-list" id="sumStakeholders">
              <span style="font-size:.72rem;color:var(--text-light);font-style:italic;">None added</span>
            </div>
          </div>
        </div>
      </div>

      <div class="summary-card">
        <div class="sum-hdr">
          <div class="sum-hdr-ico" style="background:rgba(154,123,79,.1);"><i class="bi bi-shield-check" style="color:#9A7B4F;"></i></div>
          <div><h6>Validation Checklist</h6><span class="ssub">Required before saving</span></div>
        </div>
        <div class="sum-body">
          <div class="checklist">
            <div class="cl-item"><div class="cl-icon cl-pending" id="chk-firm">1</div><span class="cl-text" id="chktxt-firm">Client firm name</span></div>
            <div class="cl-item"><div class="cl-icon cl-pending" id="chk-token">2</div><span class="cl-text" id="chktxt-token">Token present</span></div>
            <div class="cl-item"><div class="cl-icon cl-pending" id="chk-contact">3</div><span class="cl-text" id="chktxt-contact">Primary contact name</span></div>
            <div class="cl-item"><div class="cl-icon cl-pending" id="chk-phone">4</div><span class="cl-text" id="chktxt-phone">Primary mobile number</span></div>
            <div class="cl-item"><div class="cl-icon cl-pending" id="chk-site">5</div><span class="cl-text" id="chktxt-site">At least one project</span></div>
          </div>
        </div>
      </div>

      <div class="summary-card">
        <div class="sum-hdr">
          <div class="sum-hdr-ico" style="background:rgba(154,123,79,.1);"><i class="bi bi-buildings" style="color:#9A7B4F;"></i></div>
          <div><h6>Recent Clients</h6><span class="ssub">Click to edit — last 5</span></div>
        </div>
        <div class="tbl-wrap">
          <table class="clients-tbl">
            <thead>
              <tr><th>Client</th><th>Token</th><th>Status</th></tr>
            </thead>
            <tbody>
              @forelse($recentClients ?? [] as $c)
                <tr onclick="window.location='{{ route('clients.edit', $c) }}'">
                  <td><div class="tbl-client-name" style="max-width:120px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $c->company_name }}</div></td>
                  <td><span class="tbl-token">{{ $c->unique_code }}</span></td>
                  <td><span class="status-dot dot-active"></span><span style="font-size:.7rem;color:var(--text-muted);">Active</span></td>
                </tr>
              @empty
                <tr><td colspan="3" class="tbl-empty">No clients yet.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>

    </div>{{-- /right-panel --}}

  </div>{{-- /workspace --}}
</form>

@endsection

@push('scripts')


<script>
@php
  $clientData = null;
  if ($isEdit) {
     $clientData = $isEdit ? [
    'primary_country' => $client->primary_country,
    'primary_mobile'  => $client->primary_mobile,
    'mobiles'  => $client->mobiles->map(fn ($m) => [
        'name'    => $m->name,
        'country' => $m->country,
        'mobile'  => $m->mobile,
        'notify'  => (bool) $m->notify,
    ])->values(),
    
    'projects' => $client->projects->map(fn($p) => [
        'id'              => $p->id,
        'project_name'    => $p->project_name,
        'project_code'    => $p->project_code,
        'site_name'       => $p->site_name,
        'site_address'    => $p->site_address,
        'completion_date' => optional($p->completion_date)->format('Y-m-d'),
          'warranty_id'     => $p->warranty_id,

    ])->values(),
] : null;
  }
@endphp

const IS_EDIT = @json($isEdit);
const CLIENT_DATA = @json($clientData);
const EXISTING_TOKENS = @json($existingTokens ?? []);

let matchedClientId = null;
let companyLookupTimer = null;
window.warrantiesData = @json(($warranties ?? collect())->map(fn($w) => ['id' => $w->id, 'name' => $w->name])->values());
function onFirmNameInput() {
  syncSummary();
  updateChecklist();

  if (IS_EDIT) return; // already editing a specific record — don't auto-switch

  clearTimeout(companyLookupTimer);
  companyLookupTimer = setTimeout(lookupByCompanyName, 450);
}

async function lookupByCompanyName() {
  const name = document.getElementById('firmName').value.trim();
  if (!name) { resetToCreateMode(); return; }

  try {
    const res  = await fetch(`{{ route('clients.lookupByName') }}?name=` + encodeURIComponent(name));
    const data = await res.json();
    data.found ? applyMatchedClient(data) : resetToCreateMode();
  } catch (err) {
    console.error('Client lookup failed:', err);
  }
}

function applyMatchedClient(data) {
  matchedClientId = data.client_id;

  // Lock token (auto-filled, not editable)
  const tokenEl = document.getElementById('clientToken');
  tokenEl.value    = data.unique_code;
  tokenEl.readOnly = true;
  document.querySelector('.btn-regen').disabled = true;

  // Re-target the form at this client's update endpoint
  const form = document.getElementById('clientForm');
  form.action = form.dataset.updateUrlBase + '/' + matchedClientId;
  ensureMethodField('PUT');

  // Everything else stays editable and is just pre-filled
  document.getElementById('contactName').value = data.contact_name || '';
  document.getElementById('designation').value  = data.designation  || '';

  document.getElementById('primaryCountry').value = data.primary_country || '+971';
  document.getElementById('primaryMobile').value  = data.primary_mobile  || '';
  validatePhone(document.getElementById('primaryMobile'), 'primaryPhoneMsg');

  document.getElementById('stakeholderList').innerHTML = '';
  shCount = 0;
  (data.mobiles || []).forEach(m => addStakeholder(m)); // no slice(1) — these are all stakeholders

  document.getElementById('psGrid').innerHTML = '';
  psCount = 0;
  (data.projects || []).forEach(p => addProject(p, true)); // project_code stays readonly
  if (!data.projects || !data.projects.length) addProject();

  showToast('primary', 'Existing Client Found', 'Saved details loaded — fields are editable except the token and project codes.');
  syncSummary();
  updateChecklist();
}

function resetToCreateMode() {
  if (!matchedClientId) return;
  matchedClientId = null;

  const tokenEl = document.getElementById('clientToken');
  tokenEl.readOnly = false;
  document.querySelector('.btn-regen').disabled = false;

  const form = document.getElementById('clientForm');
  form.action = form.dataset.storeUrl;
  removeMethodField();
}

function ensureMethodField(method) {
  let f = document.getElementById('methodField');
  if (!f) {
    f = document.createElement('input');
    f.type = 'hidden';
    f.name = '_method';
    f.id   = 'methodField';
    document.getElementById('clientForm').appendChild(f);
  }
  f.value = method;
}

function removeMethodField() {
  const f = document.getElementById('methodField');
  if (f) f.remove();
}
/* ── Token (display only; real generation is server-side) ── */
let tokenVerified = true;

let tokenTimer=null;

function genToken(firm=''){
  const yr=new Date().getFullYear();
  const suffix=firm.replace(/[^A-Z0-9]/gi,'').toUpperCase().substring(0,4)||Math.random().toString(36).substring(2,6).toUpperCase();
  const num=String(Math.floor(Math.random()*9000)+1000);
  return `CUST-${yr}${suffix.padEnd(4,'X').substring(0,4)}`;
}

function regenToken() {
  // Double safety check: Stop if the app is globally in edit mode
  if (IS_EDIT) return;

  // 1. Calculate the next number based on existing database tokens
  // If EXISTING_TOKENS has 0 items, next is 1. If it has 5 items, next is 6.
  const nextCount = (typeof EXISTING_TOKENS !== 'undefined') ? (EXISTING_TOKENS.length + 1) : 1;
  
  // 2. Format the components
  const yr = new Date().getFullYear(); // 2026
  const paddedNum = String(nextCount).padStart(3, '0'); // Pads 1 to '001', 12 to '012'
  
  // 3. Build the final token string: CUST-2026001
  const token = `CUST-${yr}${paddedNum}`;
  
  // 4. Inject that fresh token back into your visible token input box
  document.getElementById('clientToken').value = token;
  
  // 5. Trigger your custom formatting or sync listener 
  onTokenInput(document.getElementById('clientToken'));
  
  // 6. Sync the UI layout summaries
  syncSummary();
}


function checkTokenUnique(val) {
  // Hide the checking spinner asset
  document.getElementById('tokenSpinner').style.display = 'none';
  
  // Cross-reference against your dynamic database array
  const isDup = EXISTING_TOKENS.includes(val);
  const msg = document.getElementById('tokenMsg');
  const tokenInput = document.getElementById('clientToken');

  if (isDup) {
    // IF EXISTING: Turn red, show error symbol, and block submission
    tokenVerified = false;
    document.getElementById('tokenErr').style.display = 'block';
    document.getElementById('tokenOk').style.display = 'none';
    tokenInput.className = 'form-control is-invalid';
    
    msg.className = 'field-msg err';
    msg.textContent = `⚠ This token (${val}) already exists in the database. Please regenerate.`;
  } else {
    // IF NOT EXISTING: Turn green, show success checkmark, and allow save
    tokenVerified = true;
    document.getElementById('tokenOk').style.display = 'block';
    document.getElementById('tokenErr').style.display = 'none';
    tokenInput.className = 'form-control is-valid';
    
    msg.className = 'field-msg ok';
    msg.textContent = `✓ Token ${val} is unique and available.`;
  }
  
  updateChecklist();
  syncSummary();
}

function onTokenInput(el){
  el.value=el.value.toUpperCase().replace(/[^A-Z0-9-]/g,'');
  tokenVerified=false;
  clearTimeout(tokenTimer);
  const val=el.value.trim();
  hideTokenFeedback();
  syncSummary();
  if(val.length>=6){
    showTokenChecking();
    tokenTimer=setTimeout(()=>checkTokenUnique(val),900);
  }
  updateChecklist();
}

function showTokenChecking(){
  document.getElementById('tokenSpinner').style.display='block';
  document.getElementById('tokenOk').style.display='none';
  document.getElementById('tokenErr').style.display='none';
  document.getElementById('clientToken').className='form-control checking';
  const msg=document.getElementById('tokenMsg');
  msg.className='field-msg info';
  msg.textContent='Checking uniqueness…';
}
function hideTokenFeedback(){
  document.getElementById('tokenSpinner').style.display='none';
  document.getElementById('tokenOk').style.display='none';
  document.getElementById('tokenErr').style.display='none';
  document.getElementById('clientToken').className='form-control';
  document.getElementById('tokenMsg').className='field-msg';
  document.getElementById('tokenMsg').textContent='';
}



/* ── Phone validation ── */
function validatePhone(el, msgId) {
  const val = el.value.replace(/\D/g,'');
  el.value  = el.value.replace(/[^0-9\s\-\+]/g,'');
  const msg = document.getElementById(msgId);
  if (!val.length)    { msg.className = 'field-msg'; msg.textContent = ''; el.classList.remove('is-valid','is-invalid'); }
  else if (val.length < 7)  { msg.className = 'field-msg err'; msg.textContent = 'Number too short.';  el.classList.add('is-invalid');el.classList.remove('is-valid'); }
  else if (val.length > 15) { msg.className = 'field-msg err'; msg.textContent = 'Number too long.';   el.classList.add('is-invalid');el.classList.remove('is-valid'); }
  else                       { msg.className = 'field-msg ok';  msg.textContent = '✓ Valid phone number.'; el.classList.add('is-valid');el.classList.remove('is-invalid'); }
  updateChecklist();
  syncSummary();
}

/* ── Stakeholders ── */
let shCount = 0;

function addStakeholder(prefill) {
  shCount++;
  const idx = shCount;
  const div = document.createElement('div');
  div.className = 'sh-row';
  div.id        = `sh-${idx}`;
  const name = prefill && prefill.name ? prefill.name.replace(/"/g,'&quot;') : '';
  const num  = prefill && prefill.mobile ? prefill.mobile.replace(/"/g,'&quot;') : '';
  const on   = !!(prefill && prefill.notify);
 div.innerHTML = `
    <div class="sh-inputs">
      <div>
        <span class="sh-label">Name / Label</span>
        <input type="text" class="sh-input" name="stakeholders[${idx}][name]" value="${name}" placeholder="e.g. John Facilities" oninput="syncSummary()"/>
      </div>
      <div>
        <span class="sh-label">WhatsApp Number</span>
        <div style="display:flex;gap:5px;">
          <select class="sh-input" name="stakeholders[${idx}][country]" style="width:80px;flex-shrink:0;">
            ${['+971','+91','+1','+44','+966','+974'].map(c =>
              `<option value="${c}" ${prefill && prefill.country === c ? 'selected' : ''}>${c}</option>`
            ).join('')}
          </select>
          <input type="tel" class="sh-input" name="stakeholders[${idx}][mobile]" value="${num}" placeholder="50 123 4567" inputmode="numeric" pattern="[0-9]*" maxlength="15" oninput="this.value=this.value.replace(/[^0-9]/g,'');syncSummary()" style="flex:1;"/>
        </div>
      </div>
      <div class="sh-notify-bar">
        <div class="sh-notify-label">
          <i class="bi bi-whatsapp"></i>
          <span>Send WhatsApp updates</span>
        </div>
        <input type="hidden" name="stakeholders[${idx}][notify]" value="0">
        <label class="sh-toggle">
          <input type="checkbox" name="stakeholders[${idx}][notify]" value="1" ${on ? 'checked' : ''} onchange="syncSummary()">
          <span class="sh-toggle-track"><span class="sh-toggle-thumb"></span></span>
        </label>
      </div>
    </div>
    <button type="button" class="sh-remove" onclick="removeStakeholder('sh-${idx}')" title="Remove"><i class="bi bi-trash3"></i></button>`;
  document.getElementById('stakeholderList').appendChild(div);
  syncSummary();
}

function removeStakeholder(id) {
  const el = document.getElementById(id);
  if (!el) return;
  el.remove(); syncSummary();
}

/* ── Project rows ── */
let psCount = 0;

function addProject(prefill, existing) {
  psCount++;
  const idx  = psCount;
  const grid = document.getElementById('psGrid');
  const div  = document.createElement('div');
  div.className = `ps-row ${existing ? 'existing-row' : 'new-row'}`;
  div.id        = `ps-${idx}`;
  const pn = prefill && prefill.project_name ? prefill.project_name.replace(/"/g,'&quot;') : '';
  const pc = prefill && prefill.project_code ? prefill.project_code.replace(/"/g,'&quot;') : '';
  const sn = prefill && prefill.site_name ? prefill.site_name.replace(/"/g,'&quot;') : '';
  const sa = prefill && prefill.site_address ? prefill.site_address : '';
  const cd = prefill && prefill.completion_date ? prefill.completion_date : '';
  const wid = prefill && prefill.warranty_id ? String(prefill.warranty_id) : '';
  const pid = prefill && prefill.id ? String(prefill.id) : '';

  const warrantyOptions = (window.warrantiesData || [])
    .map(w => `<option value="${w.id}" ${String(w.id) === wid ? 'selected' : ''}>${w.name}</option>`)
    .join('');

  div.innerHTML = `
    <div class="ps-row-hdr">
      <div class="ps-row-num"><i class="bi bi-diagram-3" style="color:#9A7B4F;"></i>Project Entry
        <span class="${existing ? 'saved-pill' : ''}">${existing ? 'SAVED' : '#'+idx}</span>
      </div>
      ${idx > 1 || existing ? `<button type="button" class="ps-remove" onclick="removePS('ps-${idx}')" title="Remove entry"><i class="bi bi-trash3"></i></button>` : ''}
    </div>
    <div class="ps-fields">
        <input type="hidden" name="projects[${idx}][id]" value="${pid}"/>
      <div>
        <span class="ps-label">Project Name <span style="color:#ff3366;">*</span></span>
        <input type="text" class="ps-input" name="projects[${idx}][project_name]" value="${pn}" placeholder="e.g. HQ Maintenance Contract" oninput="syncSummary()"/>
      </div>
      <div>
        <span class="ps-label">Project Code <span style="color:#ff3366;">*</span> <span style="font-size:.6rem;background:rgba(154,123,79,.1);color:#9A7B4F;padding:1px 5px;border-radius:3px;font-weight:700;">AUTO</span></span>
        <div class="ps-code-row">
          <input type="text" class="ps-input" name="projects[${idx}][project_code]" value="${pc}" placeholder="PRJ-XXXXXX" id="pscode-${idx}"
                 style="font-size:.78rem;text-transform:uppercase;"
                 readonly/>
        </div>
      </div>
      <div>
        <span class="ps-label">Completion Date <span style="color:#ff3366;">*</span></span>
        <input type="date" class="ps-input" name="projects[${idx}][completion_date]" value="${cd}" oninput="syncSummary()"/>
      </div>
       <div>
        <span class="ps-label">Warranty <span style="color:#ff3366;">*</span></span>
        <select class="ps-input" name="projects[${idx}][warranty_id]" onchange="syncSummary()">
          <option value="">Select Warranty</option>
          ${warrantyOptions}
        </select>
      </div>
      <div class="ps-full">
        <span class="ps-label">Site Name / Header <span style="color:#ff3366;">*</span></span>
        <input type="text" class="ps-input" name="projects[${idx}][site_name]" value="${sn}" placeholder="e.g. Main Building, Warehouse Block A" oninput="syncSummary()"/>
      </div>
      <div class="ps-full">
        <span class="ps-label">Physical Site Address</span>
        <textarea class="ps-textarea" name="projects[${idx}][site_address]" rows="2" placeholder="Building name, floor, area, city…" oninput="syncSummary()">${sa}</textarea>
      </div>
    </div>`;
  grid.appendChild(div);
  if (!pc) genProjectCode(idx);
  syncSummary();
  updateChecklist();
  setTimeout(() => div.classList.remove('new-row'), 200);
}

function genProjectCode(idx) {
  const el = document.getElementById(`pscode-${idx}`);
  if (el) el.value = `PRJ-${new Date().getFullYear()}-${Math.random().toString(36).substring(2,6).toUpperCase()}`;
}

function removePS(id) {
  const el = document.getElementById(id);
  if (!el) return;
  el.remove(); syncSummary(); updateChecklist();
}

/* ── Live summary sync ── */
function syncSummary() {
  const firm    = document.getElementById('firmName').value.trim();
  const token   = document.getElementById('clientToken').value.trim();
  const contact = document.getElementById('contactName').value.trim();

  const sumFirm = document.getElementById('sumFirm');
  sumFirm.textContent = firm || 'Not entered';
  sumFirm.className   = firm ? 'sv' : 'sv placeholder';

  const sumToken = document.getElementById('sumToken');
  sumToken.textContent = token || '—';
  sumToken.className   = token ? 'sv' : 'sv placeholder';

  const sumContact = document.getElementById('sumContact');
  sumContact.textContent = contact || 'Not entered';
  sumContact.className   = contact ? 'sv' : 'sv placeholder';

  const psRows = document.getElementById('psGrid').children.length;
  document.getElementById('sumProjects').innerHTML =
    `<span class="project-count-pill">${psRows} project${psRows !== 1 ? 's' : ''}</span>`;

  const shList = document.getElementById('stakeholderList').querySelectorAll('.sh-row');
  const sumSh  = document.getElementById('sumStakeholders');
  if (!shList.length) {
    sumSh.innerHTML = '<span style="font-size:.72rem;color:var(--text-light);font-style:italic;">None added</span>';
    return;
  }
  sumSh.innerHTML = Array.from(shList).map(row => {
    const nameEl = row.querySelector('input:not([type="tel"])');
    const telEl  = row.querySelector('input[type="tel"]');
    const name   = nameEl ? nameEl.value || '—' : '—';
    const num    = telEl  ? telEl.value  || '—' : '—';
    return `<div class="sh-pill"><i class="bi bi-whatsapp"></i><span>${name}: <strong>${num}</strong></span></div>`;
  }).join('');
}

/* ── Validation checklist ── */
function setCheck(id, done) {
  const ico = document.getElementById(`chk-${id}`);
  const txt = document.getElementById(`chktxt-${id}`);
  if (!ico || !txt) return;
  if (done) {
    ico.className = 'cl-icon cl-done';
    ico.innerHTML = '<i class="bi bi-check2" style="font-size:9px;"></i>';
    txt.className = 'cl-text done';
  } else {
    ico.className = 'cl-icon cl-pending';
    txt.className = 'cl-text';
  }
}

function updateChecklist() {
  const firm     = document.getElementById('firmName').value.trim().length > 0;
  const token    = document.getElementById('clientToken').value.trim().length > 0;
  const contact  = document.getElementById('contactName').value.trim().length > 0;
  const phoneVal = document.getElementById('primaryMobile').value.replace(/\D/g,'');
  const phone    = phoneVal.length >= 7 && phoneVal.length <= 15;
  const sites    = document.getElementById('psGrid').children.length > 0;
  setCheck('firm', firm); setCheck('token', token); setCheck('contact', contact);
  setCheck('phone', phone); setCheck('site', sites);
  ['firm','token','contact','phone','site'].forEach((k, i) => {
    const ico = document.getElementById(`chk-${k}`);
    if (ico && ico.classList.contains('cl-pending')) ico.textContent = String(i + 1);
  });
}

/* ── Submit guard ── */
document.getElementById('clientForm').addEventListener('submit', function (e) {
  const firm    = document.getElementById('firmName').value.trim();
  const token   = document.getElementById('clientToken').value.trim();
  const contact = document.getElementById('contactName').value.trim();
  const phone   = document.getElementById('primaryMobile').value.replace(/\D/g,'');
  const sites   = document.getElementById('psGrid').children.length;

  if (!firm)    { e.preventDefault(); showToast('error','Missing Field','Please enter the client firm name.'); return; }
  if (!token)   { e.preventDefault(); showToast('error','Token Issue','Token is missing.'); return; }
  if (!contact) { e.preventDefault(); showToast('error','Missing Field','Please enter the primary contact name.'); document.getElementById('contactName').focus(); return; }
  if (phone.length < 7) { e.preventDefault(); showToast('error','Invalid Number','Please enter a valid primary mobile number.'); document.getElementById('primaryMobile').focus(); return; }
  if (!sites)   { e.preventDefault(); showToast('error','No Projects','Please add at least one project.'); return; }

  const btn = document.getElementById('saveBtn');
  btn.disabled  = true;
  btn.innerHTML = '<span class="spinner-border" style="width:14px;height:14px;border-width:2px;"></span>Saving…';
});

/* ── Toast ── */
function showToast(type, title, body) {
  const w   = document.getElementById('toastWrap');
  const ico = {success:'bi-check-circle-fill',error:'bi-x-circle-fill',primary:'bi-info-circle-fill',warning:'bi-exclamation-circle-fill'};
  const t   = document.createElement('div');
  t.className = `toast-item${type==='error'?' error':type==='success'?' success':type==='warning'?' warning':''}`;
  t.innerHTML = `<i class="bi ${ico[type]||'bi-info-circle-fill'} ti-icon ${type}"></i>
    <div><p class="ti-title">${title}</p><p class="ti-body">${body}</p></div>`;
  w.appendChild(t);
  setTimeout(() => { t.style.opacity='0'; t.style.transition='opacity .3s'; setTimeout(()=>t.remove(),300); }, 4000);
}

@if($errors->any())
  showToast('error','Please fix the following','{{ $errors->first() }}');
@endif

/* ── Init ── */
document.getElementById('contactName').addEventListener('input', () => { syncSummary(); updateChecklist(); });

if (IS_EDIT && CLIENT_DATA) {
  // Primary mobile now comes from the clients table columns directly
  document.getElementById('primaryCountry').value = CLIENT_DATA.primary_country || '+971';
  document.getElementById('primaryMobile').value  = CLIENT_DATA.primary_mobile  || '';
  validatePhone(document.getElementById('primaryMobile'), 'primaryPhoneMsg');

  CLIENT_DATA.mobiles.forEach(m => addStakeholder(m)); // all entries here are stakeholders now
  (CLIENT_DATA.projects || []).forEach(p => addProject(p, true));
  if (!CLIENT_DATA.projects || !CLIENT_DATA.projects.length) addProject();
} else {
  addProject();
}

syncSummary();
updateChecklist();
</script>
@endpush