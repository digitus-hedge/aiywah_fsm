<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0"/>
  <title>Service Feedback | Matter Mind</title>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600;700&display=swap" rel="stylesheet"/>
  
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet"/>

    <link rel="icon" type="image/png" href="{{ asset('assets/images/favicon.png') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
  <style>
:root,[data-bs-theme="light"]{
  --app-bg:#f4f6fb;--surface:#fff;--surface-2:#f0f3f9;--surface-3:#e8edf7;
  --card-bg:#fff;--card-border:#eaeef6;--input-bg:#fff;
  --text-primary:#1a2236;--text-heading:#0d1626;--text-muted:#7987a1;--text-light:#b0bac9;
  --border-color:#e4e8f0;--card-shadow:0 2px 12px rgba(100,120,160,.09);
  --topbar-shadow:0 2px 10px rgba(100,120,160,.08);--toggle-track:#dde1ec;
  --input-focus-shadow:0 0 0 3px rgba(154,128,83,.12);--star-empty:#d7dbe4;
}
[data-bs-theme="dark"]{
  --app-bg:#1c1b1a;--surface:#1e1d1c;--surface-2:#2a2928;--surface-3:#302f2e;
  --card-bg:#242220;--card-border:#3a3836;--input-bg:#2a2928;
  --text-primary:#d4cfc8;--text-heading:#e8e3dc;--text-muted:#7a756e;--text-light:#4a4642;
  --border-color:#3a3836;--card-shadow:0 2px 16px rgba(0,0,0,.4);
  --topbar-shadow:0 2px 12px rgba(0,0,0,.4);--toggle-track:rgba(154,128,83,.25);
  --input-focus-shadow:0 0 0 3px rgba(154,128,83,.2);--star-empty:#3a3836;
}
*,*::before,*::after{box-sizing:border-box;-webkit-tap-highlight-color:transparent;}
body{font-size:.875rem;background:var(--app-bg);color:var(--text-primary);margin:0;padding:0;overflow-x:hidden;transition:background .3s,color .3s;min-height:100vh;}
h1,h2,h3,h4,h5,h6,.pg-hdr-title,.brand-name,.thanks-title{letter-spacing:-.01em;}

/* TOPBAR */
.topbar{height:60px;background:var(--surface);border-bottom:1px solid var(--border-color);display:flex;align-items:center;justify-content:space-between;padding:0 22px;box-shadow:var(--topbar-shadow);position:sticky;top:0;z-index:100;transition:background .3s,border-color .3s;}
.brand{display:flex;align-items:center;gap:11px;}
.brand-icon{width:38px;height:38px;flex-shrink:0;background:linear-gradient(135deg,#9a8053,#b8975e);border-radius:9px;display:flex;align-items:center;justify-content:center;color:#fff;font-size:.85rem;font-weight:700;letter-spacing:-.5px;}
.brand-name{font-size:.9375rem;font-weight:700;color:var(--text-heading);line-height:1.2;}
.brand-sub{font-size:.6875rem;color:var(--text-muted);}
.th-toggle{display:flex;align-items:center;gap:7px;cursor:pointer;user-select:none;}
.th-sun{color:#fbbc06;font-size:.8rem;}.th-moon{color:#b8975e;font-size:.8rem;}
.tt-track{width:42px;height:22px;background:var(--toggle-track);border-radius:11px;position:relative;transition:background .3s;border:1px solid var(--border-color);}
.tt-thumb{width:16px;height:16px;background:#fff;border-radius:50%;position:absolute;top:2px;left:2px;transition:transform .3s,background .3s;box-shadow:0 1px 4px rgba(0,0,0,.2);display:flex;align-items:center;justify-content:center;}
[data-bs-theme="dark"] .tt-thumb{transform:translateX(20px);background:#9a8053;}
.ts-sun{font-size:8px;color:#fbbc06;}.ts-moon{font-size:8px;color:#fff;display:none;}
[data-bs-theme="dark"] .ts-sun{display:none;}[data-bs-theme="dark"] .ts-moon{display:block;}

/* WRAP */
.wrap{max-width:560px;margin:0 auto;padding:26px 16px 60px;}

/* PAGE HEADER */
.pg-header{border-radius:12px;padding:22px 26px;margin-bottom:22px;color:#fff;position:relative;overflow:hidden;background:linear-gradient(135deg,#9a8053,#b8975e);}
.pg-header::before{content:'';position:absolute;left:-40px;bottom:-40px;width:180px;height:180px;border-radius:50%;background:rgba(255,255,255,.06);}
.pg-header::after{content:'';position:absolute;right:-30px;top:-30px;width:160px;height:160px;border-radius:50%;background:rgba(255,255,255,.08);}
.pg-hdr-title{font-size:1.15rem;font-weight:600;margin:0 0 4px;position:relative;z-index:1;}
.pg-hdr-desc{font-size:.8rem;margin:0;opacity:.9;position:relative;z-index:1;line-height:1.5;}
.pg-hdr-meta{display:flex;align-items:center;gap:8px;margin-top:12px;position:relative;z-index:1;flex-wrap:wrap;}
.meta-badge{background:rgba(255,255,255,.18);border:1px solid rgba(255,255,255,.3);border-radius:20px;font-size:.7rem;padding:3px 11px;font-weight:500;display:inline-flex;align-items:center;gap:5px;}

/* CARD */
.card{background:var(--card-bg);border:1px solid var(--card-border);border-radius:12px;box-shadow:var(--card-shadow);overflow:hidden;transition:background .3s,border-color .3s;}
.card-body{padding:26px 24px;}
@media(max-width:575.98px){.card-body{padding:20px 16px;}}

.section-label{font-size:.72rem;font-weight:600;color:var(--text-muted);text-transform:uppercase;letter-spacing:.06em;margin-bottom:10px;display:flex;align-items:center;gap:6px;}
.req{color:#ef4444;}

/* STARS */
.stars-block{text-align:center;padding:8px 0 4px;margin-bottom:26px;}
.stars{display:inline-flex;gap:10px;margin-bottom:10px;}
.star{font-size:2.4rem;color:var(--star-empty);cursor:pointer;transition:transform .12s,color .12s;line-height:1;}
.star:hover{transform:scale(1.12);}
.star.filled{color:#fbbc06;}
.star.filled i::before{content:"\f586";}
.star i::before{content:"\f588";}
.rating-caption{font-size:.85rem;font-weight:600;color:var(--text-heading);min-height:20px;transition:color .15s;}
.rating-caption.empty{color:var(--text-light);font-weight:400;font-style:italic;}
@media(max-width:575.98px){.star{font-size:2rem;gap:6px;}}

/* TICKET INFO */
.ticket-info{background:var(--surface-2);border:1px solid var(--border-color);border-radius:9px;padding:13px 16px;margin-bottom:24px;display:flex;align-items:center;gap:12px;}
.ti-icon{width:40px;height:40px;border-radius:8px;background:rgba(154,128,83,.12);display:flex;align-items:center;justify-content:center;color:#9a8053;font-size:1.1rem;flex-shrink:0;}
.ti-id{font-size:.9rem;font-weight:700;color:#9a8053;}
.ti-sub{font-size:.72rem;color:var(--text-muted);margin-top:1px;}

/* TEXTAREA */
.fld-textarea{width:100%;min-height:110px;padding:12px 14px;border:1px solid var(--border-color);border-radius:9px;background:var(--input-bg);color:var(--text-primary);font-size:.85rem;resize:vertical;transition:border-color .15s,box-shadow .15s;}
.fld-textarea:focus{outline:none;border-color:#9a8053;box-shadow:var(--input-focus-shadow);}
.fld-textarea::placeholder{color:var(--text-light);}
.char-count{font-size:.7rem;color:var(--text-muted);text-align:right;margin-top:5px;}

/* SUBMIT */
.btn-submit{width:100%;margin-top:24px;padding:13px;background:linear-gradient(135deg,#9a8053,#b8975e);color:#fff;border:none;border-radius:9px;font-size:.9rem;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;gap:8px;transition:opacity .15s,transform .1s;}
.btn-submit:hover{opacity:.94;}
.btn-submit:active{transform:scale(.99);}
.btn-submit:disabled{opacity:.5;cursor:not-allowed;}

.form-footnote{font-size:.72rem;color:var(--text-muted);text-align:center;margin-top:16px;line-height:1.5;}

/* THANK YOU SPLASH */
.thanks{display:none;text-align:center;padding:40px 24px 44px;}
.thanks.show{display:block;animation:fadeUp .4s ease;}
@keyframes fadeUp{from{opacity:0;transform:translateY(14px);}to{opacity:1;transform:none;}}
.thanks-ring{width:82px;height:82px;border-radius:50%;background:rgba(22,163,74,.12);display:flex;align-items:center;justify-content:center;margin:0 auto 20px;color:#16a34a;font-size:2.4rem;animation:pop .45s cubic-bezier(.3,1.4,.5,1);}
@keyframes pop{0%{transform:scale(.5);opacity:0;}100%{transform:scale(1);opacity:1;}}
.thanks-title{font-size:1.4rem;font-weight:700;color:var(--text-heading);margin-bottom:8px;}
.thanks-msg{font-size:.88rem;color:var(--text-muted);line-height:1.6;max-width:360px;margin:0 auto 20px;}
.thanks-stars{display:inline-flex;gap:6px;margin-bottom:6px;}
.thanks-stars i{color:#fbbc06;font-size:1.3rem;}
.thanks-stars i.dim{color:var(--star-empty);}









.btn-preview{
  width:100%;padding:12px;margin-top:15px;border-radius:10px;
  border:1px dashed rgba(128,128,128,.45);background:transparent;
  color:inherit;font-size:14px;cursor:pointer;
}
.btn-preview:hover{background:rgba(128,128,128,.06)}

.pv-overlay{
  position:fixed;inset:0;z-index:99999;background:rgba(0,0,0,.55);
  display:flex;align-items:center;justify-content:center;padding:16px;
}
.pv-box{
  width:100%;max-width:630px;max-height:88vh;border-radius:16px;
  background:#fff;display:flex;flex-direction:column;overflow:hidden;
  box-shadow:0 18px 50px rgba(0,0,0,.35);
}
.pv-head{
  display:flex;align-items:center;justify-content:space-between;
  padding:16px 20px;border-bottom:1px solid rgba(128,128,128,.18);
}
.pv-title{font-weight:600;font-size:15px}
.pv-close{background:none;border:none;font-size:16px;cursor:pointer;color:inherit;opacity:.6}
.pv-close:hover{opacity:1}
.pv-body{padding:20px;overflow-y:auto}

.pv-ref{font-size:18px;font-weight:700;margin-bottom:14px}
.pv-sec{
  font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;
  color:#b08d57;margin:20px 0 10px;padding-bottom:6px;
  border-bottom:1px solid rgba(176,141,87,.25);
}
.pv-sub{font-size:12px;font-weight:600;opacity:.65;margin:14px 0 6px}
.pv-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:10px 20px}
.pv-row{display:flex;flex-direction:column;gap:2px}
.pv-lbl{font-size:11px;text-transform:uppercase;letter-spacing:.05em;opacity:.55}
.pv-val{font-size:14px}
.pv-text{font-size:14px;line-height:1.55;white-space:pre-wrap}

.pv-photos{display:grid;grid-template-columns:repeat(3,1fr);gap:10px}
.pv-photo{
  display:flex;flex-direction:column;gap:6px;text-decoration:none;color:inherit;
}
.pv-photo img{
  width:100%;aspect-ratio:4/3;object-fit:cover;border-radius:10px;
  border:1px solid rgba(128,128,128,.2);background:#f4f4f4;
}
.pv-photo span{font-size:11px;text-align:center;opacity:.65}
.pv-photo-empty{
  display:flex;align-items:center;justify-content:center;flex-direction:column;
  aspect-ratio:4/3;border-radius:10px;gap:6px;
  border:1px dashed rgba(128,128,128,.3);opacity:.45;
}

.pv-table{width:100%;border-collapse:collapse;font-size:13px}
.pv-table th{
  text-align:left;font-size:11px;text-transform:uppercase;letter-spacing:.05em;
  opacity:.55;padding:8px 6px;border-bottom:1px solid rgba(128,128,128,.2);
}
.pv-table td{padding:8px 6px;border-bottom:1px solid rgba(128,128,128,.1)}
.pv-table td.num{text-align:right;font-variant-numeric:tabular-nums}

.pv-money{
  display:flex;gap:18px;flex-wrap:wrap;margin-top:12px;
  padding:10px 12px;border-radius:9px;background:rgba(128,128,128,.06);font-size:13px;
}
.pv-punch{
  margin-top:18px;padding-top:4px;border-top:1px solid rgba(128,128,128,.12);
}
.pv-total{
  display:flex;justify-content:space-between;align-items:center;
  margin-top:22px;padding:14px 16px;border-radius:11px;
  background:rgba(176,141,87,.09);font-size:15px;
}
.pv-empty,.pv-loading,.pv-err{
  font-size:13px;opacity:.6;padding:12px;text-align:center;
}
.pv-err{color:#ff3366;opacity:1}

@media(max-width:560px){
  .pv-grid{grid-template-columns:1fr}
  .pv-photos{grid-template-columns:1fr}
}

.pv-chip{
  display:inline-block;padding:2px 8px;border-radius:20px;font-size:11px;
  text-transform:capitalize;background:rgba(128,128,128,.12);
}

.pv-table td.num
{
  text-align: unset;
}


.pv-photo-ph{
  display:flex;
  flex-direction:column;
  align-items:center;
  justify-content:center;
  gap:4px;
  width:100%;
  aspect-ratio:4/3;
  border-radius:10px;
  border:1px dashed rgba(128,128,128,.32);
  font-size:22px;
  opacity:.35;
}

.pv-photo-pdf{
  border-style:solid;
  border-color:rgba(220,53,69,.28);
  background:rgba(220,53,69,.05);
  color:#dc3545;
  opacity:1;
}
.pv-photo-pdf i{font-size:34px;line-height:1}
.pv-photo-pdf em{
  font-style:normal;
  font-size:10px;
  font-weight:600;
  letter-spacing:.08em;
  text-transform:uppercase;
}
.pv-photo-pdf:hover{background:rgba(220,53,69,.1)}

.pv-photo-pdfwrap object{
  width:100%;
  aspect-ratio:4/3;
  border-radius:10px;
  border:1px solid rgba(128,128,128,.22);
  background:#f4f4f4;
  pointer-events:none;
  display:block;
  overflow:hidden;
  scrollbar-width:none;
  -ms-overflow-style:none;
}
.pv-photo-pdfwrap object::-webkit-scrollbar{display:none}


.pv-pdf-clip{
  position:relative;
  width:100%;
  aspect-ratio:4/3;
  border-radius:10px;
  border:1px solid rgba(128,128,128,.22);
  background:#f4f4f4;
  overflow:hidden;
}
.pv-pdf-clip object{
  position:absolute;
  top:0;
  left:0;
  width:calc(100% + 20px);   /* push the scrollbar past the clip edge */
  height:calc(100% + 20px);
  border:0;
  pointer-events:none;
  display:block;
}
  </style>
</head>
<body>

<header class="topbar">
  <div class="brand">
    <div class="brand-icon">MM</div>
    <div>
      <div class="brand-name">Matter Mind</div>
      <div class="brand-sub">Service Feedback</div>
    </div>
  </div>
  <div class="th-toggle" onclick="toggleTheme()">
    <i class="bi bi-sun-fill th-sun"></i>
    <div class="tt-track"><div class="tt-thumb"><i class="bi bi-sun-fill ts-sun"></i><i class="bi bi-moon-stars-fill ts-moon"></i></div></div>
    <i class="bi bi-moon-stars-fill th-moon"></i>
  </div>
</header>

<div class="wrap">

  <div class="pg-header">
    <div class="pg-hdr-title">How did we do?</div>
    <p class="pg-hdr-desc">Your service request has been completed. Please take a moment to rate the technician and share any comments - it helps us serve you better.</p>
    <div class="pg-hdr-meta">
      <span class="meta-badge"><i class="bi bi-check-circle-fill"></i> Job Completed</span>
      <span class="meta-badge"><i class="bi bi-shield-lock-fill"></i> Secure Survey</span>
    </div>
  </div>

  <!-- FORM CARD -->
  <div class="card" id="formCard">
    <div class="card-body">


      <!-- ▼ ALERT GOES HERE ▼ -->
      <div id="fbAlert" class="fb-alert" role="alert" style="display:none;"></div>

      @if($serviceRequest->feedback_submitted_at)
        <div class="fb-alert warn" style="display:flex;">
          <i class="bi bi-info-circle-fill"></i>
          <span>
            Feedback for this service request was submitted on
            {{ \Carbon\Carbon::parse($serviceRequest->feedback_submitted_at)->format('d M Y \a\t h:i A') }}.
            It can only be submitted once.
          </span>
        </div>
      @endif
      <!-- ▲ END ▲ -->


      <div class="ticket-info">
        <div class="ti-icon"><i class="bi bi-receipt"></i></div>
        <div>
          <div class="ti-id" id="ticketId">
            SR-{{ \Carbon\Carbon::parse($serviceRequest->created_at)->format('Y') }}-{{ str_pad($serviceRequest->id, 5, '0', STR_PAD_LEFT) }}
          </div>
          <div class="ti-sub" id="ticketSub">
            {{ $serviceRequest->serviceCategory->category_name ?? 'Service Request' }} · Completed {{ \Carbon\Carbon::parse($serviceRequest->updated_at)->format('d M Y') }}
          </div>

           <button class="btn-preview" id="previewBtn" type="button" onclick="openPreview()">
        <i class="bi bi-eye-fill"></i> Preview Job Record
      </button>
        </div>
      </div>

      <div class="section-label"><i class="bi bi-star-fill"></i> Technician Performance Score <span class="req">*</span></div>
      <div class="stars-block">
        <div class="stars" id="stars">
          <span class="star" data-v="1" onclick="setRating(1)" onmouseover="hoverRating(1)" onmouseout="hoverRating(0)"><i class="bi"></i></span>
          <span class="star" data-v="2" onclick="setRating(2)" onmouseover="hoverRating(2)" onmouseout="hoverRating(0)"><i class="bi"></i></span>
          <span class="star" data-v="3" onclick="setRating(3)" onmouseover="hoverRating(3)" onmouseout="hoverRating(0)"><i class="bi"></i></span>
          <span class="star" data-v="4" onclick="setRating(4)" onmouseover="hoverRating(4)" onmouseout="hoverRating(0)"><i class="bi"></i></span>
          <span class="star" data-v="5" onclick="setRating(5)" onmouseover="hoverRating(5)" onmouseout="hoverRating(0)"><i class="bi"></i></span>
        </div>
        <div class="rating-caption empty" id="ratingCaption">Tap a star to rate</div>
      </div>

     

      <div class="section-label"><i class="bi bi-chat-left-text"></i> Open Evaluation Comments</div>
      <textarea class="fld-textarea" id="comments" maxlength="500" placeholder="Tell us about your experience - punctuality, quality of work, professionalism, or anything else you'd like us to know…" oninput="updateCount()"></textarea>
      <div class="char-count"><span id="charCount">0</span> / 500</div>

      <!-- <button class="btn-submit" id="submitBtn" onclick="submitFeedback()">
        <i class="bi bi-send-fill"></i> Submit Feedback Assessment
      </button> -->

      <button class="btn-submit" id="submitBtn" onclick="submitFeedback()"
        @disabled($serviceRequest->feedback_submitted_at)>
  <i class="bi bi-send-fill"></i>
  {{ $serviceRequest->feedback_submitted_at ? 'Feedback Already Submitted' : 'Submit Feedback Assessment' }}
</button>


      <div class="form-footnote"><i class="bi bi-info-circle"></i> Your feedback is linked to this service request and can only be submitted once.</div>
    </div>
  </div>

  <!-- THANK YOU SPLASH -->
  <div class="card thanks" id="thanksSplash">
    <div class="thanks-ring"><i class="bi bi-check-lg"></i></div>
    <div class="thanks-stars" id="thanksStars"></div>
    <div class="thanks-title">Thank You!</div>
    <div class="thanks-msg">Your feedback has been recorded successfully. We truly appreciate you taking the time to help us improve our service.</div>
  </div>

</div>

<script>

document.addEventListener('DOMContentLoaded', function () {
  @if($serviceRequest->feedback_submitted_at)
    document.getElementById('comments').disabled = true;
    var s = document.getElementById('stars');
    s.style.pointerEvents = 'none';
    s.style.opacity = '.55';
    document.getElementById('ratingCaption').textContent = 'Rating submitted';
  @endif
});

const PREVIEW_URL = @json(route('feedback.preview', $serviceRequest->id));


function openPreview() {
  if (document.getElementById('pvOverlay')) return;

  const m = document.createElement('div');
  m.className = 'pv-overlay';
  m.id = 'pvOverlay';
  m.innerHTML = `
    <div class="pv-box" role="dialog" aria-modal="true">
      <div class="pv-head">
        <div class="pv-title"><i class="bi bi-clipboard-data"></i> Job Record</div>
        <button class="pv-close" id="pvClose" type="button"><i class="bi bi-x-lg"></i></button>
      </div>
      <div class="pv-body" id="pvBody">
        <div class="pv-loading"><i class="bi bi-arrow-repeat"></i> Loading…</div>
      </div>
    </div>`;

  document.body.appendChild(m);
  document.body.style.overflow = 'hidden';

  const close = () => {
    m.remove();
    document.body.style.overflow = '';
    document.removeEventListener('keydown', onKey);
  };
  const onKey = e => { if (e.key === 'Escape') close(); };

  document.addEventListener('keydown', onKey);
  document.getElementById('pvClose').onclick = close;
  m.onclick = e => { if (e.target === m) close(); };

  loadPreview();
}

async function loadPreview() {
  const body = document.getElementById('pvBody');

  try {
    const res = await fetch(PREVIEW_URL, { headers: { 'Accept': 'application/json' } });
    if (!res.ok) throw new Error('Request failed (' + res.status + ')');
    const d = await res.json();

    const hasExpenses = d.punches.some(p => p.items && p.items.length);

    body.innerHTML = `
      <div class="pv-ref">${esc(d.ref)}</div>

      <div class="pv-sec">Client</div>
      <div class="pv-grid">
        ${row('Company', d.client_company)}
        ${row('Client Code', d.client_code)}
        ${row('Contact Person', d.client_contact)}
        ${row('Mobile', d.client_phone)}
        ${row('Designation', d.client_desig)}
      </div>

      <div class="pv-sec">Project &amp; Site</div>
      <div class="pv-grid">
        ${row('Project', d.proj_name)}
        ${row('Project Code', d.proj_code)}
        ${row('Site', d.site_name)}
        ${row('Warranty Start', d.proj_completion)}
        ${row('Warranty End', d.warranty_end)}
          ${row('Warranty Scope', d.warranty)}
      </div>
      <div class="pv-sub">Site Address</div>
      <div class="pv-text">${esc(d.site_address)}</div>

      <div class="pv-sec">Service Request</div>
      <div class="pv-grid">
        ${row('Category', d.category)}
        ${row('Priority', d.priority)}
        ${row('Contact Person', d.reported)}
        ${row('Technician', d.tech)}
      </div>
      <div class="pv-sub">Issue Description</div>
      <div class="pv-text">${esc(d.issue)}</div>

      ${d.punches.length
        ? d.punches.map(punchBlock).join('')
        : '<div class="pv-sec">Punch Logs</div><div class="pv-empty">No punch records.</div>'}

      ${hasExpenses
        ? `<div class="pv-total">
             <span>Grand Total</span><strong>₹ ${esc(d.total)}</strong>
           </div>`
        : ''}`;

  } catch (e) {
    body.innerHTML = `<div class="pv-err"><i class="bi bi-exclamation-triangle"></i> ${esc(e.message)}</div>`;
  }
}

function row(label, val) {
  return `<div class="pv-row">
            <div class="pv-lbl">${esc(label)}</div>
            <div class="pv-val">${esc(val ?? '-')}</div>
          </div>`;
}

function punchBlock(p) {
  const isPdf = src => /\.pdf(\?|$)/i.test(String(src || ''));

  const photo = (src, label) => {
    if (!src) {
      return `<div class="pv-photo pv-photo-empty">
                <div class="pv-photo-ph"><i class="bi bi-image"></i></div>
                <span>${esc(label)}</span>
              </div>`;
    }

    if (isPdf(src)) {
      return `<a class="pv-photo pv-photo-pdfwrap" href="${esc(src)}" target="_blank" rel="noopener" title="Open PDF">
                <div class="pv-pdf-clip">
                  <object data="${esc(src)}#page=1&view=FitH&toolbar=0&navpanes=0&scrollbar=0&statusbar=0&messages=0"
                          type="application/pdf">
                    <div class="pv-photo-ph pv-photo-pdf">
                      <i class="bi bi-file-earmark-pdf-fill"></i><em>PDF</em>
                    </div>
                  </object>
                </div>
                <span>${esc(label)}</span>
              </a>`;
    }

    return `<a class="pv-photo" href="${esc(src)}" target="_blank" rel="noopener">
              <img src="${esc(src)}" alt="${esc(label)}" loading="lazy"
                   onerror="this.closest('.pv-photo').classList.add('pv-photo-broken')">
              <span>${esc(label)}</span>
            </a>`;
  };

  const items = p.items.length
    ? `<div class="pv-table-wrap">
         <table class="pv-table">
           <thead>
             <tr>
               <th>Item</th>
               <th>Category</th>
               <th class="num">Qty</th>
               <th class="num">Rate</th>
               <th class="num">Total</th>
               <th class="ctr">Receipt</th>
             </tr>
           </thead>
           <tbody>
             ${p.items.map(i => `
               <tr>
                 <td>${esc(i.name)}</td>
                 <td>${esc(i.category)}</td>
                 <td class="num">${esc(i.qty)}</td>
                 <td class="num">${esc(i.rate)}</td>
                 <td class="num strong">${esc(i.total)}</td>
                 <td class="ctr">${i.receipt
                       ? `<a href="${esc(i.receipt)}" target="_blank" rel="noopener" title="View receipt">
                            <i class="bi ${isPdf(i.receipt) ? 'bi-file-earmark-pdf' : 'bi-paperclip'}"></i>
                          </a>`
                       : '<span class="muted">-</span>'}</td>
               </tr>`).join('')}
           </tbody>
         </table>
       </div>`
    : `<div class="pv-empty">No expense items recorded.</div>`;

  return `
    <div class="pv-punch">
      <div class="pv-sec">Punch #${esc(p.id)} · ${esc(p.technician)}</div>

      <div class="pv-grid">
        ${row('Punch In', p.punch_in)}
        ${row('Punch Out', p.punch_out)}
      </div>

      <div class="pv-sub">Site Evidence</div>
      <div class="pv-photos">
        ${photo(p.photos.start,     'Before')}
        ${photo(p.photos.finish,    'After')}
        ${photo(p.photos.signature, 'Customer Signature')}
      </div>

      <div class="pv-sub">Expense Log</div>
      ${items}
    </div>`;
}

function esc(v) {
  return String(v ?? '')
    .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;').replace(/'/g, '&#39;');
}

/* THEME - same mechanism as portal pages, with persistence */
function applyTheme(dark){document.documentElement.setAttribute('data-bs-theme',dark?'dark':'light');try{localStorage.setItem('mm_theme',dark?'dark':'light');}catch(e){}}
function toggleTheme(){var isDark=document.documentElement.getAttribute('data-bs-theme')==='dark';applyTheme(!isDark);}
(function(){try{var s=localStorage.getItem('mm_theme');applyTheme(s?s==='dark':window.matchMedia('(prefers-color-scheme:dark)').matches);}catch(e){}})();

/* RATING */
var rating=0;
var CAPTIONS={1:'Poor',2:'Fair',3:'Good',4:'Very Good',5:'Excellent'};
function paint(n){document.querySelectorAll('#stars .star').forEach(function(s){s.classList.toggle('filled',parseInt(s.dataset.v)<=n);});}
function hoverRating(n){paint(n||rating);}
function setRating(n){
  rating=n;paint(n);
  var cap=document.getElementById('ratingCaption');
  cap.textContent=CAPTIONS[n];cap.classList.remove('empty');
  cap.style.color='';cap.style.fontStyle='';cap.style.fontWeight='';
}

/* COMMENTS */
function updateCount(){document.getElementById('charCount').textContent=document.getElementById('comments').value.length;}

/* SUBMIT */
function submitFeedback(){
  if(rating===0){
    var cap=document.getElementById('ratingCaption');
    cap.textContent='Please select a rating first';
    cap.style.color='#ef4444';cap.style.fontStyle='normal';cap.style.fontWeight='600';
    return;
  }

  var btn=document.getElementById('submitBtn');
  var original=btn.innerHTML;
  btn.disabled=true;btn.innerHTML='<i class="bi bi-arrow-repeat"></i> Submitting…';

  fetch('{{ route("clients.feedback.store", $serviceRequest->id) }}', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
      'Accept': 'application/json'
    },
    body: JSON.stringify({
      performance_score: rating,
      evaluation_comment: document.getElementById('comments').value
    })
  })
  // read the body no matter what the status is
  .then(function(res){
    return res.json()
      .catch(function(){ return {}; })          // HTML error page / empty body
      .then(function(data){ return { status: res.status, ok: res.ok, data: data }; });
  })
  .then(function(r){

    /* ---- already submitted ---- */
    if(r.status===409){
      showFbAlert('warn', r.data.message || 'Feedback has already been submitted for this request.');
      lockFbForm();
      return;
    }

    /* ---- validation failed ---- */
    if(r.status===422){
      var first='';
      if(r.data.errors){
        var k=Object.keys(r.data.errors)[0];
        first=r.data.errors[k][0];
      }
      showFbAlert('err', first || r.data.message || 'Please check the form and try again.');
      btn.disabled=false;btn.innerHTML=original;
      return;
    }

    /* ---- session expired / not authorised ---- */
    if(r.status===401 || r.status===419){
      showFbAlert('err','Your session expired. Please refresh the page and try again.');
      btn.disabled=false;btn.innerHTML=original;
      return;
    }

    /* ---- any other failure ---- */
    if(!r.ok){
      showFbAlert('err', r.data.message || 'Something went wrong ('+r.status+'). Please try again.');
      btn.disabled=false;btn.innerHTML=original;
      return;
    }

    /* ---- success ---- */
    var ts=document.getElementById('thanksStars');
    var h='';for(var i=1;i<=5;i++){h+='<i class="bi bi-star-fill'+(i<=rating?'':' dim')+'"></i>';}
    ts.innerHTML=h;
    document.getElementById('formCard').style.display='none';
    document.getElementById('thanksSplash').classList.add('show');
    window.scrollTo({top:0,behavior:'smooth'});
  })
  .catch(function(){
    // only genuine network failures reach here now
    showFbAlert('err','Network error - please check your connection and try again.');
    btn.disabled=false;btn.innerHTML=original;
  });
}

function showFbAlert(type,msg){
  var a=document.getElementById('fbAlert');
  var icons={err:'exclamation-triangle-fill',ok:'check-circle-fill',warn:'info-circle-fill'};
  a.className='fb-alert '+type;
  a.innerHTML='<i class="bi bi-'+icons[type]+'"></i><span>'+msg+'</span>';
  a.style.display='flex';
  a.scrollIntoView({behavior:'smooth',block:'center'});
}

function lockFbForm(){
  var btn=document.getElementById('submitBtn');
  btn.disabled=true;
  btn.innerHTML='<i class="bi bi-check-circle-fill"></i> Feedback Already Submitted';
  document.getElementById('comments').disabled=true;
  var s=document.getElementById('stars');
  s.style.pointerEvents='none';
  s.style.opacity='.55';
}
</script>
</body>
</html>
