@extends('layouts.layout')

@section('title', 'User Provisioning — Digit-Us Portal')
@section('page_title', 'User Provisioning')
@section('page_icon', 'building-add')

@php
    $usersData = collect($users ?? [])->map(function ($u) {
        $u = (array) $u;
        return [
            'id'       => $u['id']       ?? null,
            'name'     => $u['name']     ?? '',
            'email'    => strtolower($u['email'] ?? ''),
            'phone'    => $u['phone']    ?? '',
            'role'     => $u['role']     ?? '',
            'roleId'   => $u['roleId']   ?? ($u['role_id'] ?? ''),
            'domains'  => array_values((array) ($u['domains']  ?? [])),
            'fdGrants' => array_values((array) ($u['fdGrants'] ?? ($u['fd_grants'] ?? []))),
            'created'  => $u['created']  ?? '',
            'status'   => $u['status']   ?? 'active',
        ];
    })->values();

    $existingEmailsData = collect($existingEmails ?? [])
        ->map(fn ($e) => strtolower($e))
        ->values();

    $rolesData = collect($roles ?? [])->map(fn ($r) => [
        'code'    => $r->code    ?? '',
        'name'    => $r->name    ?? '',
        'icon'    => $r->icon    ?? 'bi-person',
        'color'   => $r->color   ?? '#9a8053',
        'bg'      => $r->bg      ?? 'rgba(154,128,83,.1)',
        'tagline' => $r->tagline ?? '',
    ])->values();

    // Permission matrix from the DB (grouped sections).
    $permSectionsData = collect($permSections ?? [])->values();
@endphp

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet"/>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet"/>
<style>
:root,[data-bs-theme="light"]{
  --up-overlay-bg:rgba(9,15,35,.62);
  --up-modal-shadow:0 24px 64px rgba(0,0,0,.18);
  --up-tbl-hover:rgba(154,128,83,.04);
  --up-tbl-hdr:#f7f9fd;
  --up-row-stripe:#fafbff;
}
[data-theme="dark"],[data-bs-theme="dark"]{
  --up-overlay-bg:rgba(0,0,0,.75);
  --up-modal-shadow:0 24px 64px rgba(0,0,0,.55);
  --up-tbl-hover:rgba(154,128,83,.07);
  --up-tbl-hdr:#101e33;
  --up-row-stripe:#0c1527;
}
.up-page{padding:0;}
@media(max-width:575.98px){.up-page{padding:14px 12px 48px;}}
.pg-hdr{background:linear-gradient(135deg,#9A7B4F 0%,#7A6140 100%);border-radius:10px;padding:20px 24px;margin-bottom:20px;color:#fff;position:relative;overflow:hidden;}
.pg-hdr::before{content:'';position:absolute;left:-40px;bottom:-40px;width:180px;height:180px;border-radius:50%;background:rgba(255,255,255,.05);}
.pg-hdr::after{content:'';position:absolute;right:-30px;top:-30px;width:160px;height:160px;border-radius:50%;background:rgba(255,255,255,.07);}
.pg-hdr h4{font-size:1rem;font-weight:600;margin:0 0 3px;position:relative;z-index:1;}
.pg-hdr p{font-size:.78rem;margin:0;opacity:.85;position:relative;z-index:1;}
.pg-hdr .mrow{display:flex;gap:8px;margin-top:10px;position:relative;z-index:1;flex-wrap:wrap;}
.mbadge{background:rgba(255,255,255,.18);border:1px solid rgba(255,255,255,.3);border-radius:20px;font-size:.6875rem;padding:2px 10px;font-weight:500;}
@media(max-width:575.98px){.pg-hdr{padding:14px 16px;}}
.workspace{display:grid;grid-template-columns:1fr 300px;gap:16px;align-items:start;}
@media(max-width:1199.98px){.workspace{grid-template-columns:1fr 290px;}}
@media(max-width:991.98px){.workspace{grid-template-columns:1fr;}}
.card{background:var(--card-bg);border:1px solid var(--card-border);border-radius:10px;box-shadow:var(--card-shadow);overflow:hidden;margin-bottom:16px;transition:background .3s,border-color .3s;}
.card:last-child{margin-bottom:0;}
.chdr{padding:14px 20px;border-bottom:1px solid var(--card-border);display:flex;align-items:center;gap:10px;}
.chdr-ico{width:32px;height:32px;border-radius:7px;display:flex;align-items:center;justify-content:center;font-size:.9rem;flex-shrink:0;}
.chdr h6{margin:0;font-size:.875rem;font-weight:600;color:var(--text-heading);}
.chdr .csub{font-size:.7rem;color:var(--text-muted);display:block;margin-top:1px;}
.cbody{padding:20px;}
@media(max-width:575.98px){.chdr{padding:12px 14px;}.cbody{padding:14px;}}
.fg{margin-bottom:16px;}
.fg:last-child{margin-bottom:0;}
.fl{font-size:.8rem;font-weight:500;color:var(--nav-link);margin-bottom:5px;display:flex;align-items:center;gap:5px;}
.fl .req{color:#ff3366;font-size:.75rem;}
.iiwrap{position:relative;}
.iiwrap .ii{position:absolute;left:10px;top:50%;transform:translateY(-50%);color:var(--text-muted);font-size:.85rem;pointer-events:none;}
.iiwrap .form-control{padding-left:34px;}
.form-control,.form-select{width:100%;font-size:.8125rem;border:1.5px solid var(--border-color);border-radius:7px;padding:.469rem .8rem;color:var(--text-primary);background:var(--input-bg);transition:border-color .15s,box-shadow .15s,background .3s;}
.form-control:focus,.form-select:focus{border-color:#9a8053;box-shadow:0 0 0 3px rgba(154,128,83,.12);outline:none;}
.form-control::placeholder{color:var(--text-light);}
.form-control.is-valid{border-color:#05a34a;}.form-control.is-invalid{border-color:#ff3366;}
[data-theme="dark"] .form-select option,[data-bs-theme="dark"] .form-select option{background:#101e33;color:#c8d4e8;}
.fhint{font-size:.7rem;color:var(--text-muted);margin-top:4px;}
.ferr{font-size:.7rem;color:#ff3366;margin-top:4px;display:none;}
.fok{font-size:.7rem;color:#05a34a;margin-top:4px;display:none;}
.ewrap{position:relative;}
.ewrap .form-control{padding-right:36px;}
.espinner{position:absolute;right:10px;top:50%;transform:translateY(-50%);display:none;}
.espinner .spinner-border{width:14px;height:14px;border-width:2px;color:#fbbc06;}
.eok{position:absolute;right:10px;top:50%;transform:translateY(-50%);color:#05a34a;font-size:.85rem;display:none;}
.eerr{position:absolute;right:10px;top:50%;transform:translateY(-50%);color:#ff3366;font-size:.85rem;display:none;}
.role-select-row{display:flex;gap:10px;align-items:flex-start;flex-wrap:wrap;}
.role-select-wrap{flex:1;min-width:200px;}
.role-pill-preview{display:flex;align-items:center;gap:7px;padding:6px 12px;border-radius:7px;font-size:.78rem;font-weight:600;min-width:160px;flex-shrink:0;}
.role-pill-preview i{font-size:.9rem;}
.role-desc-box{margin-top:10px;background:var(--surface-2);border:1px solid var(--card-border);border-radius:8px;padding:12px 14px;display:none;}
.role-desc-box.show{display:block;}
.rdb-top{display:flex;align-items:center;gap:9px;margin-bottom:8px;}
.rdb-icon{width:30px;height:30px;border-radius:6px;display:flex;align-items:center;justify-content:center;font-size:.85rem;flex-shrink:0;}
.rdb-name{font-size:.8125rem;font-weight:600;color:var(--text-heading);}
.rdb-tagline{font-size:.7rem;color:var(--text-muted);}
.perm-toggle-row{display:flex;align-items:center;justify-content:space-between;gap:10px;margin-top:12px;padding:9px 12px;background:var(--surface-2);border:1px solid var(--card-border);border-radius:8px;}
.perm-toggle-label{display:flex;align-items:center;gap:7px;font-size:.76rem;font-weight:600;color:var(--text-heading);}
.perm-toggle-label i{color:#9a8053;font-size:.9rem;}
.perm-view-btn{display:inline-flex;align-items:center;gap:6px;background:var(--card-bg);border:1.5px solid var(--border-color);border-radius:7px;padding:5px 13px;font-size:.74rem;font-weight:600;color:var(--text-muted);cursor:pointer;transition:all .15s;flex-shrink:0;}
.perm-view-btn:hover{border-color:#9a8053;color:#9a8053;background:rgba(154,128,83,.06);}
.perm-view-btn.active{border-color:#9a8053;color:#9a8053;background:rgba(154,128,83,.1);}
.perm-view-btn i{font-size:.9rem;}
.perm-collapse{display:none;}
.perm-collapse.open{display:block;}
.perm-table{width:100%;border-collapse:collapse;margin-top:12px;}
.perm-table thead th{font-size:.65rem;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--text-muted);padding:7px 10px;border-bottom:1px solid var(--card-border);background:var(--up-tbl-hdr);}
.perm-table thead th:first-child{text-align:left;}
.perm-table thead th:not(:first-child){text-align:center;width:56px;}
.perm-table tbody tr{border-bottom:1px solid var(--card-border);}
.perm-table tbody tr:last-child{border-bottom:none;}
.perm-table tbody tr:nth-child(odd){background:var(--up-row-stripe);}
.perm-table tbody td{padding:8px 10px;font-size:.77rem;vertical-align:middle;}
.perm-table tbody td:first-child{color:var(--text-primary);display:flex;align-items:center;gap:7px;}
.perm-table tbody td:first-child i{font-size:.85rem;color:var(--text-muted);width:16px;flex-shrink:0;}
.perm-table tbody td:not(:first-child){text-align:center;}
.perm-cell-yes{color:#05a34a;font-size:1rem;}
.perm-cell-no{color:#aeb7c5;font-size:.85rem;}
.perm-cell-partial{color:#fbbc06;font-size:.85rem;}
.fd-cell input[type="checkbox"]{accent-color:#9a8053;width:14px;height:14px;cursor:pointer;}
.fd-cell{text-align:center!important;}
.fd-label{font-size:.6rem;color:var(--text-muted);display:block;margin-top:1px;}
.perm-section-hdr td{background:var(--surface-3)!important;font-size:.65rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--text-muted);padding:5px 10px!important;border-bottom:1px solid var(--card-border);}
.perm-note{font-size:.7rem;color:var(--text-muted);margin-top:8px;padding:7px 10px;background:rgba(154,128,83,.06);border-radius:6px;border-left:3px solid #9a8053;display:flex;align-items:flex-start;gap:6px;line-height:1.5;}
.perm-note i{color:#9a8053;flex-shrink:0;margin-top:1px;}
.fd-upgrade-note{background:rgba(249,115,22,.06);border-left-color:#f97316;}
.fd-upgrade-note i{color:#f97316;}
.domain-card-wrap{display:none;}
.domain-card-wrap.show{display:block;}
.ml-domain-note{background:rgba(16,185,129,.07);border:1px solid rgba(16,185,129,.2);border-radius:7px;padding:9px 12px;font-size:.72rem;color:var(--text-muted);display:flex;align-items:flex-start;gap:7px;margin-bottom:14px;line-height:1.5;}
.ml-domain-note i{color:#10b981;flex-shrink:0;margin-top:1px;}
.dom-cat{border:1px solid var(--card-border);border-radius:8px;overflow:hidden;margin-bottom:8px;}
.dom-cat:last-child{margin-bottom:0;}
.dom-cat-hdr{display:flex;align-items:center;gap:9px;padding:10px 13px;cursor:pointer;background:var(--surface-2);transition:background .15s;user-select:none;}
.dom-cat-hdr:hover{background:var(--surface-3);}
.dom-cat-hdr.has-picks{background:rgba(154,128,83,.06);border-left:3px solid #9a8053;}
.dom-cat-icon{width:28px;height:28px;border-radius:6px;display:flex;align-items:center;justify-content:center;font-size:.85rem;flex-shrink:0;}
.dom-cat-title{font-size:.8rem;font-weight:600;color:var(--text-heading);flex:1;}
.dom-cat-count{font-size:.65rem;font-weight:700;padding:2px 8px;border-radius:10px;background:rgba(154,128,83,.12);color:#9a8053;display:none;}
.dom-cat-hdr.has-picks .dom-cat-count{display:inline;}
.dom-cat-chevron{color:var(--text-muted);font-size:.85rem;transition:transform .22s;flex-shrink:0;}
.dom-cat-hdr.open .dom-cat-chevron{transform:rotate(180deg);}
.dom-cat-body{display:none;padding:11px 13px 13px;border-top:1px solid var(--card-border);}
.dom-cat-body.open{display:block;}
.skill-tags{display:flex;flex-wrap:wrap;gap:7px;}
.d-tag{display:flex;align-items:center;gap:6px;border:1.5px solid var(--border-color);border-radius:6px;padding:5px 11px;cursor:pointer;font-size:.76rem;color:var(--text-muted);background:var(--card-bg);transition:all .16s;user-select:none;}
.d-tag:hover{border-color:rgba(154,128,83,.4);color:#9a8053;background:rgba(154,128,83,.04);}
.d-tag.picked{border-color:#9a8053;background:rgba(154,128,83,.1);color:#9a8053;font-weight:500;}
.d-tag .chk{width:13px;height:13px;border-radius:3px;border:1.5px solid var(--border-color);background:var(--card-bg);flex-shrink:0;transition:all .15s;display:flex;align-items:center;justify-content:center;}
.d-tag.picked .chk{background:#9a8053;border-color:#9a8053;}
.d-tag.picked .chk::after{content:'✓';font-size:7px;color:#fff;}
.d-selected-wrap{display:flex;flex-wrap:wrap;gap:5px;margin-top:10px;min-height:20px;}
.dschip{background:rgba(154,128,83,.1);border:1px solid rgba(154,128,83,.2);border-radius:20px;font-size:.65rem;font-weight:600;color:#9a8053;padding:2px 9px;display:inline-flex;align-items:center;gap:4px;}
.dschip .rm{cursor:pointer;opacity:.7;font-size:.7rem;line-height:1;}
.dschip .rm:hover{opacity:1;color:#ff3366;}
.dom-toolbar{display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;}
.dom-toolbar-btns{display:flex;gap:6px;}
.dom-act-btn{background:none;border:1px solid var(--border-color);border-radius:5px;padding:3px 10px;font-size:.7rem;color:var(--text-muted);cursor:pointer;transition:all .15s;}
.dom-act-btn:hover{border-color:#9a8053;color:#9a8053;}
.dom-sel-count{font-size:.72rem;color:var(--text-muted);}
.dom-sel-count span{font-weight:700;color:#9a8053;}
.action-bar{display:flex;gap:10px;margin-top:20px;flex-wrap:wrap;}
.btn-save{background:linear-gradient(135deg,#9A7B4F,#7A6140);color:#fff;border:none;border-radius:8px;padding:.58rem 1.5rem;font-size:.875rem;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:7px;transition:all .2s;}
.btn-save:hover{box-shadow:0 4px 16px rgba(154,128,83,.4);}
.btn-save:active{transform:scale(.98);}
.btn-save:disabled{opacity:.55;cursor:not-allowed;box-shadow:none;}
.btn-rst{background:transparent;color:var(--text-muted);border:1.5px solid var(--border-color);border-radius:8px;padding:.58rem 1.2rem;font-size:.875rem;font-weight:500;cursor:pointer;display:inline-flex;align-items:center;gap:7px;transition:all .2s;}
.btn-rst:hover{border-color:var(--text-muted);color:var(--text-heading);}
.rp{position:sticky;top:20px;}
@media(max-width:991.98px){.rp{position:static;}}
.rp-card{background:var(--card-bg);border:1px solid var(--card-border);border-radius:10px;box-shadow:var(--card-shadow);overflow:hidden;}
.rp-section{border-bottom:1px solid var(--card-border);}
.rp-section:last-child{border-bottom:none;}
.rp-sec-hdr{padding:10px 14px;display:flex;align-items:center;gap:8px;background:var(--surface-2);border-bottom:1px solid var(--card-border);}
.rp-sec-hdr i{font-size:.85rem;flex-shrink:0;}
.rp-sec-hdr span{font-size:.75rem;font-weight:700;color:var(--text-heading);text-transform:uppercase;letter-spacing:.06em;}
.rp-sec-body{padding:12px 14px;}
.pp-row{display:flex;align-items:center;gap:10px;padding-bottom:10px;border-bottom:1px solid var(--card-border);margin-bottom:10px;}
.pp-av{width:40px;height:40px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:.82rem;font-weight:700;color:#fff;flex-shrink:0;background:linear-gradient(135deg,#82693f,#9a8053);transition:background .3s;}
.pp-name{font-size:.82rem;font-weight:600;color:var(--text-heading);}
.pp-email{font-size:.68rem;color:var(--text-muted);}
.pp-rp{display:inline-flex;align-items:center;gap:4px;font-size:.65rem;font-weight:700;padding:2px 8px;border-radius:20px;margin-top:2px;}
.srow{display:flex;justify-content:space-between;align-items:baseline;margin-bottom:6px;font-size:.76rem;}
.srow:last-child{margin-bottom:0;}
.srow .sk{color:var(--text-muted);}
.srow .sv{font-weight:500;color:var(--text-heading);text-align:right;max-width:58%;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;}
.srow .sv.ph{color:var(--text-light);font-style:italic;font-weight:400;}
hr.shr{border-color:var(--card-border);margin:8px 0;}
.email-badge{background:rgba(154,128,83,.07);border:1px solid rgba(154,128,83,.18);border-radius:6px;padding:7px 10px;font-size:.7rem;color:var(--text-muted);display:flex;align-items:flex-start;gap:6px;margin-top:8px;}
.email-badge i{color:#9a8053;flex-shrink:0;margin-top:1px;}
.cklist{display:flex;flex-direction:column;gap:6px;}
.cli{display:flex;align-items:center;gap:7px;font-size:.74rem;}
.clico{width:17px;height:17px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:.58rem;flex-shrink:0;transition:all .2s;}
.cl-done{background:rgba(5,163,74,.12);color:#05a34a;}
.cl-pend{background:var(--surface-2);color:var(--text-light);border:1px solid var(--border-color);}
.clt{color:var(--text-muted);transition:color .2s;}
.clt.done{color:var(--text-heading);}
.utbl-wrap{overflow-x:auto;-webkit-overflow-scrolling:touch;}
.utbl{width:100%;border-collapse:collapse;min-width:500px;}
.utbl thead th{font-size:.67rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--text-muted);padding:9px 11px;border-bottom:1px solid var(--card-border);background:var(--up-tbl-hdr);white-space:nowrap;}
.utbl tbody tr{border-bottom:1px solid var(--card-border);cursor:pointer;transition:background .15s;}
.utbl tbody tr:last-child{border-bottom:none;}
.utbl tbody tr:hover{background:var(--up-tbl-hover);}
.utbl tbody td{padding:9px 11px;font-size:.78rem;vertical-align:middle;}
.u-cell{display:flex;align-items:center;gap:9px;}
.uav{width:28px;height:28px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:.6rem;font-weight:700;color:#fff;flex-shrink:0;}
.uname{font-weight:500;color:var(--text-heading);}
.uemail{font-size:.67rem;color:var(--text-muted);}
.rpill{font-size:.62rem;font-weight:700;padding:2px 8px;border-radius:10px;white-space:nowrap;}
.dchips{display:flex;gap:3px;flex-wrap:wrap;}
.dch{font-size:.6rem;padding:1px 6px;border-radius:8px;background:rgba(154,128,83,.1);color:#9a8053;}
.sdot{width:7px;height:7px;border-radius:50%;display:inline-block;margin-right:5px;}
.dot-a{background:#05a34a;}.dot-p{background:#fbbc06;}.dot-i{background:#aeb7c5;}
.tblfoot{padding:9px 13px;border-top:1px solid var(--card-border);font-size:.72rem;color:var(--text-muted);display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:6px;}
.pgbtns{display:flex;gap:4px;}
.pgb{background:var(--surface-2);border:1px solid var(--border-color);border-radius:5px;width:26px;height:26px;display:flex;align-items:center;justify-content:center;cursor:pointer;font-size:.72rem;color:var(--text-muted);transition:all .15s;}
.pgb:hover,.pgb.act{background:rgba(154,128,83,.1);border-color:#9a8053;color:#9a8053;}
.tblsearch{display:flex;gap:8px;margin-bottom:12px;flex-wrap:wrap;}
.tsrch{position:relative;flex:1;min-width:140px;}
.tsrch i{position:absolute;left:9px;top:50%;transform:translateY(-50%);color:var(--text-muted);font-size:.8rem;}
.tsrch input{padding-left:28px;width:100%;font-size:.77rem;border:1px solid var(--border-color);border-radius:6px;height:32px;color:var(--text-primary);background:var(--input-bg);}
.tsrch input:focus{border-color:#9a8053;box-shadow:0 0 0 2px rgba(154,128,83,.1);outline:none;}
.tsrch input::placeholder{color:var(--text-light);}
.fsel{font-size:.77rem;border:1px solid var(--border-color);border-radius:6px;padding:.25rem .55rem;height:32px;color:var(--text-primary);background:var(--input-bg);}
.fsel:focus{border-color:#9a8053;outline:none;}
[data-theme="dark"] .fsel option,[data-bs-theme="dark"] .fsel option{background:#101e33;}
.mover{display:none;position:fixed;inset:0;background:var(--up-overlay-bg);z-index:9000;align-items:center;justify-content:center;padding:16px;}
.mover.show{display:flex;}
.mbox{background:var(--modal-bg);border-radius:12px;border:1px solid var(--card-border);max-width:420px;width:100%;box-shadow:var(--up-modal-shadow);animation:popIn .25s ease;overflow:hidden;}
@keyframes popIn{from{transform:scale(.88);opacity:0;}to{transform:none;opacity:1;}}
.mhdr{padding:15px 18px;border-bottom:1px solid var(--card-border);display:flex;align-items:center;justify-content:space-between;}
.mhdr h6{margin:0;font-size:.9rem;font-weight:600;color:var(--text-heading);}
.mclose{background:none;border:none;color:var(--text-muted);cursor:pointer;font-size:1.1rem;padding:4px;border-radius:4px;line-height:1;}
.mclose:hover{color:var(--text-heading);background:var(--surface-2);}
.mbody{padding:18px 20px;}
.micoRing{width:56px;height:56px;border-radius:50%;background:rgba(130,105,63,.1);display:flex;align-items:center;justify-content:center;font-size:1.5rem;color:#82693f;margin:0 auto 14px;}
.mtitle{font-size:.9375rem;font-weight:600;color:var(--text-heading);text-align:center;margin-bottom:5px;}
.msub{font-size:.78rem;color:var(--text-muted);text-align:center;line-height:1.5;}
.msum{background:var(--surface-2);border:1px solid var(--border-color);border-radius:7px;padding:12px 14px;margin:14px 0;display:grid;grid-template-columns:1fr 1fr;gap:8px;}
.msr .ml{color:var(--text-muted);font-size:.67rem;text-transform:uppercase;letter-spacing:.05em;}
.msr .mv{font-weight:500;color:var(--text-heading);margin-top:1px;font-size:.76rem;}
.msr.full{grid-column:1/-1;}
.mftr{padding:13px 18px;border-top:1px solid var(--card-border);display:flex;gap:10px;justify-content:flex-end;}
.btn-mc{background:var(--surface-2);border:1px solid var(--border-color);border-radius:6px;padding:.42rem 1rem;font-size:.8rem;cursor:pointer;color:var(--text-muted);transition:all .15s;}
.btn-mc:hover{border-color:var(--text-muted);color:var(--text-heading);}
.btn-mok{border:none;border-radius:6px;padding:.42rem 1.2rem;font-size:.8rem;font-weight:600;cursor:pointer;color:#fff;background:linear-gradient(135deg,#9A7B4F,#7A6140);display:flex;align-items:center;gap:6px;transition:all .15s;}
.btn-mok:hover{box-shadow:0 4px 14px rgba(154,128,83,.4);}
.twrap{position:fixed;top:70px;right:16px;z-index:9999;display:flex;flex-direction:column;gap:8px;max-width:310px;}
.titem{background:var(--card-bg);border-left:4px solid #9a8053;border-radius:7px;padding:12px 14px;box-shadow:0 6px 24px rgba(0,0,0,.18);display:flex;align-items:flex-start;gap:10px;animation:toastIn .3s ease;}
.titem.success{border-color:#05a34a;}.titem.error{border-color:#ff3366;}.titem.warning{border-color:#fbbc06;}
@keyframes toastIn{from{transform:translateX(40px);opacity:0;}to{transform:none;opacity:1;}}
.tii{font-size:1.1rem;flex-shrink:0;margin-top:1px;}
.tii.primary{color:#9a8053;}.tii.success{color:#05a34a;}.tii.error{color:#ff3366;}.tii.warning{color:#fbbc06;}
.tit{font-size:.8125rem;font-weight:600;margin:0 0 2px;color:var(--text-heading);}
.tib{font-size:.72rem;margin:0;color:var(--text-muted);}
@media(max-width:575.98px){.twrap{left:12px;right:12px;max-width:none;}}

/* ════════════════════════════════
   RESPONSIVE — ALL DEVICES
════════════════════════════════ */

/* Small/standard desktop — slightly narrower side panel */
@media(max-width:1299.98px){
  .workspace{grid-template-columns:1fr 280px;}
}

/* Tablet landscape — side panel becomes full-width row below the form */
@media(max-width:991.98px){
  .workspace{grid-template-columns:1fr;}
  .rp{position:static;}
  .modal-open-guard{}
}

/* Tablet portrait / large phone */
@media(max-width:767.98px){
  .role-select-row{flex-direction:column;}
  .role-select-wrap{min-width:0;width:100%;}
  .role-pill-preview{width:100%;justify-content:center;min-width:0;}
  .action-bar{flex-direction:column;}
  .action-bar .btn-save,.action-bar .btn-rst{width:100%;justify-content:center;}
  .dom-toolbar{flex-direction:column;align-items:flex-start;gap:8px;}
  .dom-toolbar-btns{flex-wrap:wrap;}
  .msum{grid-template-columns:1fr;}
  .tblsearch{flex-direction:column;align-items:stretch;}
  .tblsearch .tsrch{max-width:none!important;}
  .tblsearch .fsel{width:100%!important;}
  .pg-hdr p{font-size:.74rem;}
}

/* Phones */
@media(max-width:575.98px){
  .row.g-3>[class*="col-"]{width:100%;}
  .cbody{padding:14px 12px;}
  .chdr{padding:12px 12px;}
  .chdr h6{font-size:.82rem;}
  .chdr .csub{font-size:.66rem;}
  .perm-table thead th:not(:first-child){width:42px;}
  .perm-table tbody td,.perm-table thead th{padding:6px 5px;font-size:.7rem;}
  .perm-table tbody td:first-child{gap:5px;}
  .d-tag{font-size:.72rem;padding:5px 9px;}
  .rp-sec-body{padding:12px;}
  .mbox{max-width:100%;}
  .mftr{flex-direction:column-reverse;}
  .mftr .btn-mc,.mftr .btn-mok{width:100%;justify-content:center;}
  .fullTbody td,.utbl tbody td{padding:8px 9px;}
  .pg-hdr h4{font-size:.9rem;line-height:1.35;}
}

/* Very small phones */
@media(max-width:400px){
  .pg-hdr{padding:13px 14px;}
  .pg-hdr h4{font-size:.86rem;}
  .pg-hdr h4 i{display:none;}
  .mbadge{font-size:.6rem;padding:2px 8px;}
  .chdr-ico{width:28px;height:28px;}
  .form-control,.form-select{font-size:.8rem;}
  .btn-save,.btn-rst{font-size:.82rem;}
}
</style>
@endpush

@section('content')

<!-- SUCCESS MODAL -->
<div class="mover" id="successModal">
  <div class="mbox">
    <div class="mhdr"><h6>User Profile Created</h6><button class="mclose" onclick="closeModal()"><i class="bi bi-x-lg"></i></button></div>
    <div class="mbody">
      <div class="micoRing"><i class="bi bi-person-check-fill"></i></div>
      <div class="mtitle">Profile Saved &amp; Invite Sent</div>
      <div class="msub">User account provisioned. System initialisation email with login credentials dispatched.</div>
      <div class="msum" id="msum"></div>
      <div style="background:rgba(37,211,102,.07);border:1px solid rgba(37,211,102,.2);border-radius:7px;padding:9px 12px;font-size:.72rem;color:var(--text-muted);display:flex;align-items:center;gap:7px;">
        <i class="bi bi-envelope-check-fill" style="color:#05a34a;flex-shrink:0;"></i>
        Initialisation email dispatched to <strong id="mEmail">—</strong>
      </div>
    </div>
    <div class="mftr">
      <button class="btn-mc" onclick="closeModal()">Close</button>
      <button class="btn-mok" onclick="closeModal()"><i class="bi bi-plus-lg"></i>Add Another</button>
    </div>
  </div>
</div>

<!-- TOAST -->
<div class="twrap" id="twrap"></div>

<div class="up-page">

  <div class="pg-hdr">
    <h4><i class="bi bi-shield-lock me-2"></i>User Provisioning &amp; Dynamic Permission Studio</h4>
    <p>Create user accounts, assign roles, and configure technical domain expertise. Role assignments automatically govern data visibility and access across all portal endpoints.</p>
    <div class="mrow">
      <span class="mbadge"><i class="bi bi-shield-fill-check me-1"></i>Super Admin</span>
      <span class="mbadge"><i class="bi bi-person-gear me-1"></i>Admin</span>
    </div>
  </div>

  <div class="workspace">

    <!-- ══ LEFT: FORM ══ -->
    <div>
      <!-- SECTION 1: Employee Identity -->
      <div class="card">
        <div class="chdr">
          <div class="chdr-ico" style="background:rgba(130,105,63,.1);"><i class="bi bi-person-vcard-fill" style="color:#82693f;"></i></div>
          <div><h6>Employee Identity</h6><span class="csub">Full name and corporate email address</span></div>
        </div>
        <div class="cbody">
          <div class="row g-3">
            <div class="col-sm-6">
              <div class="fg">
                <label class="fl">Employee Full Name <span class="req">*</span></label>
                <div class="iiwrap">
                  <i class="bi bi-person ii"></i>
                  <input type="text" class="form-control" id="empName" placeholder="e.g. Rahul Mehta" autocomplete="off" oninput="syncAll()">
                </div>
              </div>
            </div>
            <div class="col-sm-6">
              <div class="fg">
                <label class="fl">Corporate Email <span class="req">*</span></label>
                <div class="ewrap">
                  <div class="iiwrap">
                    <i class="bi bi-envelope ii"></i>
                    <input type="email" class="form-control" id="empEmail" placeholder="name@mattermind.com" autocomplete="off" oninput="onEmailInput(this)" style="padding-left:34px;padding-right:36px;">
                  </div>
                  <div class="espinner" id="espinner"><div class="spinner-border"></div></div>
                  <i class="bi bi-check-circle-fill eok" id="eok"></i>
                  <i class="bi bi-x-circle-fill eerr" id="eerr"></i>
                </div>
                <div class="ferr" id="emailErrMsg"></div>
                <div class="fok" id="emailOkMsg">✓ Email available.</div>
              </div>
            </div>
            <div class="col-sm-6">
              <div class="fg">
                <label class="fl">Password <span class="req">*</span></label>
                <div class="iiwrap">
                  <i class="bi bi-lock ii"></i>
                  <input type="password" class="form-control" id="empPassword" placeholder="Min. 8 characters" autocomplete="new-password" oninput="syncAll()">
                </div>
                <div class="fhint">Sent securely to the employee on save.</div>
              </div>
            </div>
            <div class="col-sm-6">
            <div class="fg">
              <label class="fl">Phone Number <span class="req">*</span></label>
              <div style="display:flex;gap:6px;">
                <select class="form-select" id="empPhoneCountry" style="width:92px;flex-shrink:0;" onchange="syncAll()">
                  <option value="+971">🇦🇪 +971</option>
                  <option value="+91" selected>🇮🇳 +91</option>
                  <option value="+1">🇺🇸 +1</option>
                  <option value="+44">🇬🇧 +44</option>
                  <option value="+966">🇸🇦 +966</option>
                  <option value="+974">🇶🇦 +974</option>
                </select>
                <div class="iiwrap" style="flex:1;">
                  <i class="bi bi-telephone ii"></i>
                  <input type="tel" class="form-control" id="empPhone"
                        placeholder="98765 43210" autocomplete="off" inputmode="numeric"
                        maxlength="15"
                        oninput="this.value=this.value.replace(/[^0-9]/g,'');validatePhone();syncAll()"
                </div>
              </div>
              <div class="ferr" id="phoneErrMsg"></div>
            </div>
          </div>
          </div>
        </div>
      </div>

      <!-- SECTION 2: Role Assignment -->
      <div class="card">
        <div class="chdr">
          <div class="chdr-ico" style="background:rgba(154,128,83,.1);"><i class="bi bi-person-gear" style="color:#9a8053;"></i></div>
          <div><h6>Operational Security Role</h6><span class="csub">Select role — permission matrix auto-populates below</span></div>
        </div>
        <div class="cbody">
          <div class="fg">
            <label class="fl">Role Assignment <span class="req">*</span></label>
            <div class="role-select-row">
              <div class="role-select-wrap">
               <select class="form-select" id="roleSelect" onchange="onRoleChange(this.value)">
                <option value="">— Select operational role —</option>
                @foreach ($rolesData as $r)
                    <option value="{{ $r['code'] }}">{{ $r['name'] }}</option>
                @endforeach
                </select>
              </div>
              <div class="role-pill-preview" id="rolePillPreview" style="display:none;"></div>
            </div>
          </div>
          <div class="role-desc-box" id="roleDescBox">
            <div class="rdb-top">
              <div class="rdb-icon" id="rdbIcon"></div>
              <div>
                <div class="rdb-name" id="rdbName"></div>
                <div class="rdb-tagline" id="rdbTagline"></div>
              </div>
            </div>
            <div id="permTableWrap"></div>
          </div>
        </div>
      </div>

      <!-- SECTION 3: Domain Expertise — ML only -->
      <div class="domain-card-wrap" id="domainCardWrap">
        <div class="card">
          <div class="chdr">
            <div class="chdr-ico" style="background:rgba(16,185,129,.1);"><i class="bi bi-tags-fill" style="color:#10b981;"></i></div>
            <div><h6>Technical Domain Expertise</h6><span class="csub">Maintenance Lead — select skill domains across any category</span></div>
          </div>
          <div class="cbody">
            <div class="ml-domain-note">
              <i class="bi bi-info-circle-fill"></i>
              Skills are grouped under categories for clarity. A Maintenance Lead can hold skills across multiple categories — select all that apply regardless of category. These determine which tickets get routed to this technician.
            </div>
            <div class="dom-toolbar">
              <div class="dom-toolbar-btns">
                <button class="dom-act-btn" onclick="expandAllCats()">Expand All</button>
                <button class="dom-act-btn" onclick="collapseAllCats()">Collapse All</button>
                <button class="dom-act-btn" onclick="clearAllDomains()">Clear All</button>
              </div>
              <span class="dom-sel-count"><span id="domSelCount">0</span> skills selected</span>
            </div>
            <div id="domainGrid"></div>
            <div class="d-selected-wrap" id="dsWrap"></div>
          </div>
        </div>
      </div>

      <!-- Actions -->
      <div class="action-bar">
        <button class="btn-save" id="saveBtn" onclick="saveUser()"><i class="bi bi-floppy-fill"></i>Save User Profile</button>
        <button class="btn-rst" onclick="resetForm()"><i class="bi bi-arrow-counterclockwise"></i>Reset</button>
      </div>
    </div>

    <!-- ══ RIGHT PANEL ══ -->
    <div class="rp">
      <div class="rp-card">
        <div class="rp-section">
          <div class="rp-sec-hdr">
            <i class="bi bi-person-circle" style="color:#82693f;"></i>
            <span>Profile Preview</span>
          </div>
          <div class="rp-sec-body">
            <div class="pp-row">
              <div class="pp-av" id="ppAv">?</div>
              <div style="flex:1;min-width:0;">
                <div class="pp-name" id="ppName" style="color:var(--text-light);font-style:italic;">Not entered</div>
                <div class="pp-email" id="ppEmail">—</div>
                <div id="ppRp"></div>
              </div>
            </div>
            <div class="srow"><span class="sk">Role</span><span class="sv ph" id="sumRole">Not selected</span></div>
            <div class="srow" style="margin:0;"><span class="sk">Domains</span><span class="sv ph" id="sumDom">None</span></div>
            <div class="email-badge"><i class="bi bi-envelope-fill"></i>On save, an initialisation email with credentials is sent to the employee.</div>
          </div>
        </div>
        <div class="rp-section">
          <div class="rp-sec-hdr">
            <i class="bi bi-shield-check" style="color:#9a8053;"></i>
            <span>Validation Checklist</span>
          </div>
          <div class="rp-sec-body">
            <div class="cklist">
              <div class="cli"><div class="clico cl-pend" id="chk-name">1</div><span class="clt" id="chkt-name">Employee full name</span></div>
              <div class="cli"><div class="clico cl-pend" id="chk-email">2</div><span class="clt" id="chkt-email">Valid corporate email</span></div>
              <div class="cli"><div class="clico cl-pend" id="chk-role">3</div><span class="clt" id="chkt-role">Operational role selected</span></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- FULL TABLE -->
  <div class="card" style="margin-top:20px;">
    <div class="chdr">
      <div class="chdr-ico" style="background:rgba(154,128,83,.1);"><i class="bi bi-table" style="color:#9a8053;"></i></div>
      <div><h6>All Provisioned Users</h6><span class="csub">Full directory — click any row to load profile into form</span></div>
    </div>
    <div class="cbody" style="padding-bottom:0;">
      <div class="tblsearch">
        <div class="tsrch" style="max-width:240px;"><i class="bi bi-search"></i><input type="text" id="fullSearch" placeholder="Search name, email…" oninput="filterFull()"/></div>
        <select class="fsel" id="fullRoleF" onchange="filterFull()">
          <option value="">All Roles</option>
          <option>Super Admin</option><option>Admin</option><option>Head of Projects</option>
          <option>Maintenance Lead</option><option>Front Desk Executive</option><option>Accounts / AR</option>
        </select>
        <span style="font-size:.72rem;color:var(--text-muted);align-self:center;" id="fullCount"></span>
      </div>
    </div>
    <div class="utbl-wrap">
      <table class="utbl" style="min-width:640px;">
        <thead><tr><th>Employee</th><th>Role</th><th>Domains</th><th>Created</th><th>Status</th></tr></thead>
        <tbody id="fullTbody"></tbody>
      </table>
    </div>
    <div class="tblfoot"><span id="fullLbl"></span><div class="pgbtns" id="fullPg"></div></div>
  </div>

</div><!-- /.up-page -->

@endsection

@push('scripts')
<script>
/* ════════════════════════════════
   SERVER-PROVIDED DATA
════════════════════════════════ */
const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
const SAVE_URL   = @json($saveUrl ?? null);
const UPDATE_URL_BASE = @json($updateUrlBase ?? null);
let editingUserId     = null;

/* Permission matrix from the DB (permissions + permission_role).
   Each section: { label, items:[{ key, name, icon, access:{code:'yes|no|rls'}, grant:{code:true} }] } */
const PERM_SECTIONS = @json($permSectionsData);

/* Role metadata from the roles table. */
const ROLE_META = (@json($rolesData)).reduce((acc, r) => {
  acc[r.code] = {name:r.name, icon:r.icon, color:r.color, bg:r.bg, tagline:r.tagline};
  return acc;
}, {});

/* Row-pill colors derived from roles (keyed by code). */
const RC = (@json($rolesData)).reduce((acc, r) => { acc[r.code] = r.color; return acc; }, {});

/* FD extra grants state (stores permission KEYS). */
let fdGrants = new Set();

/* ════════════════════════════════
   DOMAIN MASTER — fully dynamic, sourced from service_categories / service_domains
════════════════════════════════ */
const DOMAIN_CATS = @json($domainCats);

function hexToRgba(hex, alpha = 0.1) {
  if (!hex) return `rgba(148,163,184,${alpha})`;
  const h = hex.replace('#', '');
  const bigint = parseInt(h.length === 3 ? h.split('').map(c => c + c).join('') : h, 16);
  const r = (bigint >> 16) & 255, g = (bigint >> 8) & 255, b = bigint & 255;
  return `rgba(${r},${g},${b},${alpha})`;
}

// Normalize every category/skill id to a real Number up front, once,
// so nothing downstream ever has to guess about string vs number ids.
DOMAIN_CATS.forEach(c => {
  c.id = Number(c.id);
  c.bg = hexToRgba(c.color, 0.1);
  c.skills = (c.skills || [])
    .map(s => ({ ...s, id: Number(s.id), catId: c.id, catLabel: c.label }))
    .filter(s => Number.isInteger(s.id) && s.id > 0); // guards against bad/zero/NaN ids from the server
});

const DOMAINS_FLAT = DOMAIN_CATS.flatMap(c => c.skills);
const VALID_DOMAIN_IDS = new Set(DOMAINS_FLAT.map(d => d.id));

let selectedRole=null;
let selectedDomains=new Set();
let emailValid=false;
let phoneValid=false;
let emailTimer=null;

const EXISTING_EMAILS = @json($existingEmailsData);
let USERS = @json($usersData);
let fFull=[...USERS],pgF=1;
const PFI=6;

/* ════════════════════════════════
   ROLE CHANGE
════════════════════════════════ */
function onRoleChange(val){
  selectedRole=val||null;
  fdGrants=new Set();
  const box=document.getElementById('roleDescBox');
  const pill=document.getElementById('rolePillPreview');
  if(!val){box.className='role-desc-box';pill.style.display='none';const _d=document.getElementById('domainCardWrap');if(_d)_d.classList.remove('show');syncAll();return;}
  const m=ROLE_META[val]||{name:val,icon:'bi-person',color:'#9a8053',bg:'rgba(154,128,83,.1)',tagline:''};
  document.getElementById('rdbIcon').style.cssText=`background:${m.bg};`;
  document.getElementById('rdbIcon').innerHTML=`<i class="bi ${m.icon}" style="color:${m.color};"></i>`;
  document.getElementById('rdbName').textContent=m.name;
  document.getElementById('rdbTagline').textContent=m.tagline;
  pill.style.display='flex';
  pill.style.cssText=`display:flex;align-items:center;gap:7px;padding:6px 12px;border-radius:7px;font-size:.78rem;font-weight:600;flex-shrink:0;background:${m.bg};color:${m.color};border:1px solid ${m.color}30;`;
  pill.innerHTML=`<i class="bi ${m.icon}"></i>${m.name}`;
  renderPermTable(val);
  box.className='role-desc-box show';
  renderDomains();
  syncAll();
}

function renderPermTable(roleId){
  const isFD = roleId === 'FD';
  let html=`<div class="perm-toggle-row">
      <span class="perm-toggle-label"><i class="bi bi-list-check"></i>Permission Matrix</span>
      <button type="button" class="perm-view-btn" id="permViewBtn" onclick="togglePermTable()">
        <i class="bi bi-eye" id="permViewIcon"></i><span id="permViewTxt">View</span>
      </button>
    </div>
    <div class="perm-collapse" id="permCollapse">
    <table class="perm-table">
    <thead>
      <tr>
        <th>Module / Feature</th>
        <th>Access</th>
        ${isFD?'<th style="width:80px;">Extend<br/><span style="font-weight:400;font-size:.6rem;text-transform:none;letter-spacing:0;">(Admin grant)</span></th>':''}
      </tr>
    </thead>
    <tbody>`;

  (PERM_SECTIONS||[]).forEach(sec=>{
    html+=`<tr class="perm-section-hdr"><td colspan="${isFD?3:2}">${sec.label}</td></tr>`;
    (sec.items||[]).forEach(item=>{
      const label = item.name;
      const icon  = item.icon || 'bi-dot';
      const val   = (item.access && item.access[roleId]) || 'no';
      const isGrantable = isFD && item.grant && item.grant['FD'];
      const isGranted   = fdGrants.has(item.key);

      let cell='';
      if(val==='yes'){
        cell=`<td><i class="bi bi-check-circle-fill perm-cell-yes"></i></td>`;
      } else if(val==='rls'){
        cell=`<td><i class="bi bi-slash-circle perm-cell-partial" title="Filtered view only"></i></td>`;
      } else {
        cell=`<td><i class="bi bi-dash perm-cell-no"></i></td>`;
      }
      const grantCell = isFD
        ? `<td class="fd-cell">${isGrantable
            ? `<input type="checkbox" ${isGranted?'checked':''} onchange="toggleFdGrant('${item.key}',this.checked)" title="Grant this permission to FD user"/>`
            : '<span style="color:var(--text-light);font-size:.75rem;">—</span>'}</td>`
        : '';

      html+=`<tr>
        <td><i class="bi ${icon}"></i>${label}</td>
        ${cell}
        ${grantCell}
      </tr>`;
    });
  });

  html+=`</tbody></table></div>`;

  if(roleId==='SA'){
    html+=`<div class="perm-note"><i class="bi bi-shield-fill-check"></i>Super Admin has unrestricted access to all features including system configuration and master data. This role cannot be further restricted.</div>`;
  } else if(roleId==='AD'){
    html+=`<div class="perm-note"><i class="bi bi-info-circle-fill"></i>Full operational control — includes all Head of Projects access plus user and system management.</div>`;
  } else if(roleId==='HP'){
    html+=`<div class="perm-note"><i class="bi bi-info-circle-fill"></i>Head of Projects has full operational scope — approvals, dispatch, QC, and a filtered analytics view. Financial flows (quotation/invoice) route through Accounts.</div>`;
  } else if(roleId==='FD'){
    html+=`<div class="perm-note fd-upgrade-note"><i class="bi bi-sliders2"></i>Front Desk Executive has a defined base permission set. The checkboxes above allow an Admin or Super Admin to selectively extend specific higher-level access when needed.</div>`;
  } else if(roleId==='ML'){
    html+=`<div class="perm-note"><i class="bi bi-info-circle-fill"></i>Maintenance Lead sees only their own assigned pipeline. Domain expertise tags below determine which tickets are routed to this user.</div>`;
  } else if(roleId==='AC'){
    html+=`<div class="perm-note"><i class="bi bi-info-circle-fill"></i>Accounts / AR is scoped to out-of-warranty financial flows only — quotations, invoices, and expense reconciliation.</div>`;
  }

  document.getElementById('permTableWrap').innerHTML=html;
}

function togglePermTable(){
  const box=document.getElementById('permCollapse');
  const btn=document.getElementById('permViewBtn');
  const txt=document.getElementById('permViewTxt');
  const ico=document.getElementById('permViewIcon');
  if(!box)return;
  const open=box.classList.toggle('open');
  if(btn)btn.classList.toggle('active',open);
  if(txt)txt.textContent=open?'Hide':'View';
  if(ico)ico.className=open?'bi bi-eye-slash':'bi bi-eye';
}

function toggleFdGrant(key,checked){
  if(checked)fdGrants.add(key);
  else fdGrants.delete(key);
}

/* ════════════════════════════════
   DOMAIN EXPERTISE — nested, fully dynamic
════════════════════════════════ */
function renderDomains(){
  const wrap=document.getElementById('domainCardWrap');
  if(!wrap)return;
  if(selectedRole==='ML'){wrap.classList.add('show');}
  else{wrap.classList.remove('show');selectedDomains=new Set();}

  const grid=document.getElementById('domainGrid');
  if(!grid)return;
  grid.innerHTML=DOMAIN_CATS.map(cat=>{
    const picked=cat.skills.filter(s=>selectedDomains.has(s.id));
    const isOpen=cat.skills.some(s=>selectedDomains.has(s.id));
    return `
    <div class="dom-cat" id="domcat-${cat.id}">
      <div class="dom-cat-hdr${picked.length?' has-picks':''}${isOpen?' open':''}"
           onclick="toggleCat(${cat.id})">
        <div class="dom-cat-icon" style="background:${cat.bg};">
          <i class="bi ${cat.icon}" style="color:${cat.color};"></i>
        </div>
        <span class="dom-cat-title">${cat.label}</span>
        <span class="dom-cat-count">${picked.length} selected</span>
        <i class="bi bi-chevron-down dom-cat-chevron"></i>
      </div>
      <div class="dom-cat-body${isOpen?' open':''}" id="domcatbody-${cat.id}">
        <div class="skill-tags">
          ${cat.skills.map(s=>`
            <div class="d-tag${selectedDomains.has(s.id)?' picked':''}"
                 onclick="toggleDomain(${s.id})">
              <div class="chk"></div>${s.label}
            </div>`).join('')}
        </div>
      </div>
    </div>`;
  }).join('');

  const cnt=document.getElementById('domSelCount');
  if(cnt)cnt.textContent=selectedDomains.size;

  const chips=document.getElementById('dsWrap');
  if(!chips)return;
  if(!selectedDomains.size){chips.innerHTML='';return;}
  chips.innerHTML=Array.from(selectedDomains).map(id=>{
    const s=DOMAINS_FLAT.find(x=>x.id===id);
    return s?`<span class="dschip">${s.label}<i class="bi bi-x rm" onclick="removeDomain(${id})"></i></span>`:'';
  }).join('');
  syncAll();
}

function toggleCat(catId){
  catId = Number(catId);
  const hdr=document.querySelector(`#domcat-${catId} .dom-cat-hdr`);
  const body=document.getElementById(`domcatbody-${catId}`);
  if(!hdr||!body)return;
  hdr.classList.toggle('open');
  body.classList.toggle('open');
}

function expandAllCats(){
  DOMAIN_CATS.forEach(c=>{
    document.querySelector(`#domcat-${c.id} .dom-cat-hdr`)?.classList.add('open');
    document.getElementById(`domcatbody-${c.id}`)?.classList.add('open');
  });
}

function collapseAllCats(){
  DOMAIN_CATS.forEach(c=>{
    document.querySelector(`#domcat-${c.id} .dom-cat-hdr`)?.classList.remove('open');
    document.getElementById(`domcatbody-${c.id}`)?.classList.remove('open');
  });
}

function clearAllDomains(){
  selectedDomains=new Set();
  renderDomains();syncAll();
}

// Only real, known domain ids (as validated against VALID_DOMAIN_IDS) can ever
// enter selectedDomains — this is what prevents a bad "0" or stale id from
// ever reaching the save payload.
function toggleDomain(id){
  id = Number(id);
  if(!VALID_DOMAIN_IDS.has(id)) return;
  if(selectedDomains.has(id))selectedDomains.delete(id);
  else selectedDomains.add(id);
  renderDomains();syncAll();
}
function removeDomain(id){
  id = Number(id);
  selectedDomains.delete(id);
  renderDomains();syncAll();
}

/* ════════════════════════════════
   EMAIL VALIDATION
════════════════════════════════ */
function onEmailInput(el){
  clearTimeout(emailTimer);emailValid=false;
  hideEmailFB();syncAll();
  const v=el.value.trim();
  if(!v)return;
  if(!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v)){el.className='form-control is-invalid';showErrMsg('Invalid email format.');return;}
  document.getElementById('espinner').style.display='block';
  emailTimer=setTimeout(()=>{
    document.getElementById('espinner').style.display='none';
    if(EXISTING_EMAILS.includes(v.toLowerCase())){
      emailValid=false;el.className='form-control is-invalid';
      document.getElementById('eerr').style.display='block';
      showErrMsg('This email is already registered.');
    } else {
      emailValid=true;el.className='form-control is-valid';
      document.getElementById('eok').style.display='block';
      document.getElementById('emailOkMsg').style.display='block';
    }
    syncAll();
  },800);
}
function validatePhone(){
  const num = document.getElementById('empPhone').value.trim();
  const err = document.getElementById('phoneErrMsg');
  const inp = document.getElementById('empPhone');
  // 7–15 digits is the practical range for national numbers
  if(!num){ phoneValid=false; err.style.display='none'; inp.className='form-control'; return; }
  if(!/^\d{7,15}$/.test(num)){
    phoneValid=false;
    inp.className='form-control is-invalid';
    err.textContent='Enter a valid phone number (7–15 digits).';
    err.style.display='block';
  } else {
    phoneValid=true;
    inp.className='form-control is-valid';
    err.style.display='none';
  }
}
function showErrMsg(m){const e=document.getElementById('emailErrMsg');e.textContent=m;e.style.display='block';}
function hideEmailFB(){
  document.getElementById('espinner').style.display='none';
  document.getElementById('eok').style.display='none';
  document.getElementById('eerr').style.display='none';
  document.getElementById('emailOkMsg').style.display='none';
  document.getElementById('emailErrMsg').style.display='none';
  document.getElementById('empEmail').className='form-control';
}

/* ════════════════════════════════
   LIVE SYNC
════════════════════════════════ */
function syncAll(){
  const name=document.getElementById('empName').value.trim();
  const email=document.getElementById('empEmail').value.trim();
  const m=ROLE_META[selectedRole];

  const av=document.getElementById('ppAv');
  av.textContent=name?name.split(' ').map(w=>w[0]).join('').toUpperCase().substring(0,2):'?';
  av.style.background=m?`linear-gradient(135deg,${m.color},${m.color}bb)`:'linear-gradient(135deg,#82693f,#9a8053)';

  const pn=document.getElementById('ppName');
  pn.textContent=name||'Not entered';pn.style.color=name?'var(--text-heading)':'var(--text-light)';pn.style.fontStyle=name?'':'italic';
  document.getElementById('ppEmail').textContent=email||'—';

  const rp=document.getElementById('ppRp');
  rp.innerHTML=m?`<span class="pp-rp" style="background:${m.bg};color:${m.color};"><i class="bi ${m.icon} me-1"></i>${m.name}</span>`:'';

  const sr=document.getElementById('sumRole');sr.textContent=m?m.name:'Not selected';sr.className=m?'sv':'sv ph';
  const sd=document.getElementById('sumDom');
  if(selectedDomains.size){sd.textContent=Array.from(selectedDomains).map(id=>DOMAINS_FLAT.find(d=>d.id===id)?.label||id).join(', ');sd.className='sv';}
  else{sd.textContent='None';sd.className='sv ph';}

  setChk('name',name.length>0);
  setChk('email',emailValid);
  setChk('role',!!selectedRole);
}

function setChk(id,done){
  const ico=document.getElementById(`chk-${id}`);
  const txt=document.getElementById(`chkt-${id}`);
  if(!ico||!txt)return;
  if(done){ico.className='clico cl-done';ico.innerHTML='<i class="bi bi-check2" style="font-size:9px;"></i>';txt.className='clt done';}
  else{ico.className='clico cl-pend';ico.textContent={name:'1',email:'2',role:'3'}[id];txt.className='clt';}
}

/* ════════════════════════════════
   SAVE
════════════════════════════════ */
function saveUser(){
  const name=document.getElementById('empName').value.trim();
  const email=document.getElementById('empEmail').value.trim();
  const phone = document.getElementById('empPhone').value.trim();
  const phoneCountry = document.getElementById('empPhoneCountry').value;
  const password=document.getElementById('empPassword').value;
  const isEditing = editingUserId !== null;

  if(!name){showToast('error','Missing','Employee full name is required.');document.getElementById('empName').focus();return;}
  if(!emailValid){showToast('error','Email Issue','Enter a valid, unique corporate email.');document.getElementById('empEmail').focus();return;}
  if(!phone || !/^\d{7,15}$/.test(phone)){
  showToast('error','Phone Issue','Enter a valid phone number (7–15 digits).');
  document.getElementById('empPhone').focus();return;
  }
  if(!isEditing && (!password || password.length<8)){showToast('error','Password Required','Password must be at least 8 characters.');document.getElementById('empPassword').focus();return;}
  if(isEditing && password && password.length<8){showToast('error','Password Too Short','New password must be at least 8 characters.');document.getElementById('empPassword').focus();return;}
  if(!selectedRole){showToast('error','Role Required','Please select an operational role.');return;}
  const m=ROLE_META[selectedRole];
  const btn=document.getElementById('saveBtn');
  btn.disabled=true;
  btn.innerHTML='<span class="spinner-border" style="width:13px;height:13px;border-width:2px;"></span>Saving…';

  // Only real, currently-valid domain ids ever leave the browser.
  const domainIds = Array.from(selectedDomains).filter(id => VALID_DOMAIN_IDS.has(id));
  const grants=Array.from(fdGrants);

  const payload={
    name,
    email:email.toLowerCase(),
    phone,
    role:m.name,
    roleId:selectedRole,
    domains:domainIds,
    fdGrants:grants,
  };
  if(password) payload.password = password;

  const url    = isEditing ? `${UPDATE_URL_BASE}/${editingUserId}` : SAVE_URL;
  const method =  'POST';  // was: isEditing ? 'PUT' : 'POST'

  if(!url){
    showToast('error','Not Configured','Save endpoint is missing.');
    btn.disabled=false;btn.innerHTML='<i class="bi bi-floppy-fill"></i>Save User Profile';
    return;
  }

  fetch(url,{
    method,
    headers:{
      'Content-Type':'application/json',
      'Accept':'application/json',
      'X-CSRF-TOKEN':CSRF_TOKEN,
      'X-Requested-With':'XMLHttpRequest',
    },
    body:JSON.stringify(payload),
  })
  .then(async r=>{
    const data=await r.json().catch(()=>({}));
    if(!r.ok)throw new Error(data.message||'Save failed');
    if(!data.user)throw new Error('Server did not return the saved user.');
    commitSavedUser(data.user,m,email);
  })
  .catch(err=>{
    showToast('error','Save Failed',err.message||'Could not provision user.');
    btn.disabled=false;btn.innerHTML='<i class="bi bi-floppy-fill"></i>Save User Profile';
  });
}

function commitSavedUser(saved,m,email){
  const domainIds = (saved.domains || []).map(id => Number(id));
  const domainLabels = domainIds.map(id => DOMAINS_FLAT.find(d=>d.id===id)?.label || id);

  USERS.unshift({
    name:saved.name,
    email:(saved.email||email).toLowerCase(),
    role:saved.role||m.name,
    roleId:saved.roleId||selectedRole,
    domains:domainIds,
    fdGrants:saved.fdGrants||[],
    created:saved.created||new Date().toLocaleDateString('en-GB',{day:'2-digit',month:'short',year:'numeric'}),
    status:saved.status||'pending',
  });
  if(!EXISTING_EMAILS.includes((saved.email||email).toLowerCase())){
    EXISTING_EMAILS.push((saved.email||email).toLowerCase());
  }
  document.getElementById('mEmail').textContent=saved.email||email;
  document.getElementById('msum').innerHTML=`
    <div class="msr"><div class="ml">Full Name</div><div class="mv">${saved.name}</div></div>
    <div class="msr"><div class="ml">Role</div><div class="mv" style="color:${m.color};font-weight:700;">${saved.role||m.name}</div></div>
    <div class="msr full"><div class="ml">Email</div><div class="mv">${saved.email||email}</div></div>
    <div class="msr full"><div class="ml">Domains</div><div class="mv">${domainLabels.join(', ')||'—'}</div></div>
    ${(saved.fdGrants&&saved.fdGrants.length)?`<div class="msr full"><div class="ml">Extended FD Permissions</div><div class="mv" style="color:#f97316;">${saved.fdGrants.join(', ')}</div></div>`:''}`;
  document.getElementById('successModal').classList.add('show');
}

function closeModal(){document.getElementById('successModal').classList.remove('show');window.location.reload();}

/* ════════════════════════════════
   RESET
════════════════════════════════ */
function resetForm(){
  editingUserId=null;
  document.getElementById('empName').value='';
  document.getElementById('empEmail').value='';
  document.getElementById('empPhone').value = '';
  document.getElementById('empPassword').value='';
  document.getElementById('roleSelect').value='';
  emailValid=false;hideEmailFB();
  selectedRole=null;selectedDomains=new Set();fdGrants=new Set();
  document.getElementById('roleDescBox').className='role-desc-box';
  document.getElementById('rolePillPreview').style.display='none';
  const dcw=document.getElementById('domainCardWrap');if(dcw)dcw.classList.remove('show');
  const btn=document.getElementById('saveBtn');
  btn.innerHTML='<i class="bi bi-floppy-fill"></i>Save User Profile';
  renderDomains();syncAll();
  showToast('primary','Reset','Form cleared.');
}

/* ════════════════════════════════
   TABLES
════════════════════════════════ */
function filterFull(){
  const q=document.getElementById('fullSearch').value.toLowerCase();
  const rf=document.getElementById('fullRoleF').value;
  fFull=USERS.filter(u=>(!q||(u.name.toLowerCase().includes(q)||u.email.includes(q)))&&(!rf||u.role===rf));
  pgF=1;renderFull();
}
function renderFull(){
  const s=(pgF-1)*PFI,page=fFull.slice(s,s+PFI);
  document.getElementById('fullCount').textContent=`${fFull.length} / ${USERS.length}`;
  document.getElementById('fullLbl').textContent=`Showing ${Math.min(s+1,fFull.length)}–${Math.min(s+PFI,fFull.length)} of ${fFull.length}`;
  const tot=Math.ceil(fFull.length/PFI);
  document.getElementById('fullPg').innerHTML=tot<=1?'':Array.from({length:tot},(_,i)=>`<div class="pgb ${i+1===pgF?'act':''}" onclick="pgF=${i+1};renderFull()">${i+1}</div>`).join('');
  document.getElementById('fullTbody').innerHTML=page.map(u=>`
    <tr onclick="loadUser(${JSON.stringify(u).replace(/"/g,'&quot;')})">
      <td><div class="u-cell"><div class="uav" style="background:linear-gradient(135deg,${RC[u.roleId]},${RC[u.roleId]}99);">${u.name.split(' ').map(w=>w[0]).join('').substring(0,2).toUpperCase()}</div><div><div class="uname">${u.name}</div><div class="uemail">${u.email}</div></div></div></td>
      <td><span class="rpill" style="background:${RC[u.roleId]}1a;color:${RC[u.roleId]};">${u.role}</span></td>
      <td><div class="dchips">${u.domains.length?u.domains.map(id=>`<span class="dch">${DOMAINS_FLAT.find(d=>d.id===Number(id))?.label||id}</span>`).join(''):'<span style="font-size:.7rem;color:var(--text-muted);">—</span>'}</div></td>
      <td style="font-size:.72rem;color:var(--text-muted);">${u.created}</td>
      <td><span class="sdot ${u.status==='active'?'dot-a':u.status==='pending'?'dot-p':'dot-i'}"></span><span style="font-size:.72rem;color:var(--text-muted);">${u.status==='pending'?'Pending invite':'Active'}</span></td>
    </tr>`).join('');
}

function loadUser(u){
  editingUserId = u.id ?? null;
  document.getElementById('empName').value=u.name;
  document.getElementById('empEmail').value=u.email;
  document.getElementById('empPhone').value = u.phone || '';
  emailValid=true;
  document.getElementById('empEmail').className='form-control is-valid';
  document.getElementById('eok').style.display='block';
  document.getElementById('emailOkMsg').style.display='block';
  document.getElementById('roleSelect').value=u.roleId;
  selectedRole=u.roleId;
  onRoleChange(u.roleId);
  fdGrants=new Set(u.fdGrants||[]);

  // Only accept ids that are real, currently-known domains — guards against
  // stale/soft-deleted/legacy references stored against this user.
  selectedDomains = new Set(
    (u.domains||[])
      .map(id => Number(id))
      .filter(id => VALID_DOMAIN_IDS.has(id))
  );

  renderPermTable(u.roleId);
  renderDomains();syncAll();
  const btn=document.getElementById('saveBtn');
  btn.innerHTML='<i class="bi bi-pencil-fill"></i>Update User Profile';
  window.scrollTo({top:0,behavior:'smooth'});
  showToast('primary','Editing',`Editing ${u.name}. Change details and click Update.`);
}

/* ════════════════════════════════
   TOAST
════════════════════════════════ */
function showToast(type,title,body){
  const w=document.getElementById('twrap');
  const ico={success:'bi-check-circle-fill',error:'bi-x-circle-fill',primary:'bi-info-circle-fill',warning:'bi-exclamation-circle-fill'};
  const t=document.createElement('div');
  t.className=`titem ${type==='error'?'error':type==='success'?'success':type==='warning'?'warning':''}`;
  t.innerHTML=`<i class="bi ${ico[type]||'bi-info-circle-fill'} tii ${type}"></i><div><p class="tit">${title}</p><p class="tib">${body}</p></div>`;
  w.appendChild(t);
  setTimeout(()=>{t.style.opacity='0';t.style.transition='opacity .3s';setTimeout(()=>t.remove(),300);},4000);
}

/* ════════════════════════════════
   INIT
════════════════════════════════ */
renderDomains();syncAll();
fFull=[...USERS];
renderFull();

/* Ensure the create form always starts empty — clears any browser-restored
   values (e.g. admin@demo.com / password) after a refresh or bfcache restore. */
function clearCreateForm(){
  if(editingUserId!==null) return;
  document.getElementById('empName').value='';
  document.getElementById('empEmail').value='';
  document.getElementById('empPhone').value = '';
  document.getElementById('empPassword').value='';
  emailValid=false;hideEmailFB();syncAll();
}
window.addEventListener('load',clearCreateForm);
window.addEventListener('pageshow',function(e){ if(e.persisted) clearCreateForm(); });
</script>
@endpush