@extends('worker.layouts.console')

@section('title', 'Field Pipeline')
@section('heading', 'Field Pipeline')

@push('head')
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
@endpush

@push('styles')
<style>
/* ══════════════════════════════════════════════════════ FILTERS ═══ */
.tab-filter{display:flex;gap:6px;margin-bottom:14px;overflow-x:auto;padding-bottom:3px;}
.tab-filter::-webkit-scrollbar{height:0;}
.tf-btn{padding:6px 13px;font-size:.73rem;font-weight:600;border:1px solid var(--border);
  background:var(--card);border-radius:20px;cursor:pointer;color:var(--muted);transition:all .18s;
  flex:0 0 auto;white-space:nowrap;}
.tf-btn:hover{border-color:var(--gold-border);color:var(--gold);}
.tf-btn.active{background:var(--gold);border-color:var(--gold);color:#fff;}

/* ══════════════════════════════════════════════════════ JOB CARDS ═══ */
.pl-card{background:var(--card);border:1px solid var(--border);border-radius:14px;margin-bottom:10px;
  box-shadow:var(--shadow);overflow:hidden;transition:border-color .15s;}
.pl-card.is-active{border-color:rgba(21,128,61,.45);}
.pl-card.is-accepted{border-left:3px solid var(--blue);}
.jc-main{padding:14px 15px;cursor:pointer;}
.jc-top{display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:8px;gap:8px;}
.jc-sr{font-size:1.02rem;font-weight:700;color:var(--gold);}
.jc-client{font-size:.86rem;font-weight:600;color:var(--text);margin-bottom:2px;}
.jc-contract{font-size:.7rem;color:var(--muted);margin-bottom:9px;}
.jc-meta{display:flex;gap:10px;flex-wrap:wrap;align-items:center;}
.jc-meta-item{display:flex;align-items:center;gap:4px;font-size:.72rem;color:var(--muted);min-width:0;}
.jc-meta-item i{color:var(--gold);font-size:.72rem;flex-shrink:0;}
.jc-meta-item span{overflow:hidden;text-overflow:ellipsis;white-space:nowrap;}
.jc-sla{font-size:.68rem;font-weight:700;padding:2px 8px;border-radius:20px;margin-left:auto;flex-shrink:0;}
.sla-ok{background:var(--green-bg);color:var(--green);}
.sla-w{background:var(--amber-bg);color:var(--amber);}
.sla-c{background:var(--red-bg);color:var(--red);}
.jc-chevron{color:var(--light);font-size:.85rem;transition:transform .25s;flex-shrink:0;}
.jc-chevron.open{transform:rotate(180deg);}

.jc-expand{display:none;border-top:1px solid var(--border);background:var(--card2);}
.jc-expand.open{display:block;}
.jc-exp-section{padding:13px 15px;border-bottom:1px solid var(--border);}
.jc-exp-section:last-child{border-bottom:none;}
.exp-label{font-size:.62rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;
  color:var(--light);margin-bottom:7px;display:flex;align-items:center;gap:5px;}
.exp-text{font-size:.77rem;color:var(--text);line-height:1.5;overflow-wrap:anywhere;
  word-break:break-word;white-space:pre-wrap;max-width:100%;min-width:0;}
.rework-note{font-size:.75rem;color:var(--red);background:var(--red-bg);border-radius:9px;
  padding:9px 11px;margin-top:6px;line-height:1.5;border:1px solid rgba(220,38,38,.15);overflow-wrap:anywhere;}
.hist-line{font-size:.72rem;color:var(--muted);padding:3px 0 3px 9px;
  border-left:2px solid var(--border2);margin:0 0 4px 2px;overflow-wrap:anywhere;}
.photo-row{display:flex;gap:8px;flex-wrap:wrap;margin-top:4px;}
.photo-thumb{width:64px;height:64px;border-radius:9px;background:var(--surface);
  border:1px solid var(--border);display:flex;align-items:center;justify-content:center;
  font-size:1.3rem;cursor:pointer;overflow:hidden;flex-shrink:0;text-decoration:none;}
.photo-thumb img{width:100%;height:100%;object-fit:cover;}
.photo-pdf iframe{pointer-events:none;width:100%;height:100%;border:0;}

.eta-form{padding:13px 15px;background:var(--surface);border-top:1px solid var(--border);}
.eta-title{font-size:.74rem;font-weight:700;color:var(--text);margin-bottom:11px;
  display:flex;align-items:center;gap:6px;}
.eta-title i{color:var(--gold);}
.eta-grid{display:grid;grid-template-columns:1fr 1fr;gap:9px;margin-bottom:14px;}
@media(max-width:340px){.eta-grid{grid-template-columns:1fr;}}
.eta-field label{font-size:.66rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em;
  color:var(--muted);display:block;margin-bottom:4px;}
.eta-input{width:100%;font-size:.82rem;border:1px solid var(--border2);border-radius:9px;
  padding:.5rem .7rem;color:var(--text);background:var(--card);-webkit-appearance:none;appearance:none;
  transition:border-color .15s,box-shadow .15s;}
.eta-input:focus{border-color:var(--gold);box-shadow:0 0 0 3px var(--gold-bg);outline:none;}
.eta-input.err{border-color:var(--red);}
.accept-btn{width:100%;border:none;border-radius:11px;padding:.7rem 1rem;font-size:.85rem;font-weight:700;
  cursor:pointer;display:flex;align-items:center;justify-content:center;gap:7px;transition:all .18s;
  background:linear-gradient(135deg,#9a8053,#b8975e);color:#fff;box-shadow:0 4px 14px rgba(154,128,83,.3);}
.accept-btn:hover:not(:disabled){transform:translateY(-1px);}
.accept-btn:disabled{opacity:.45;cursor:not-allowed;background:var(--surface);color:var(--muted);box-shadow:none;}
.accept-btn.go{background:linear-gradient(135deg,#15803d,#16a34a);box-shadow:0 4px 14px rgba(21,128,61,.3);}
.accept-btn.blue{background:linear-gradient(135deg,#2563eb,#3b82f6);box-shadow:0 4px 14px rgba(37,99,235,.3);}
.accept-btn.ghost{background:none;box-shadow:none;border:1px dashed var(--gold-border);
  color:var(--gold);margin-top:9px;font-weight:600;}
.accept-btn.ghost:hover{background:var(--gold-bg);transform:none;}
.wa-hint{font-size:.68rem;color:var(--muted);text-align:center;margin-top:8px;
  display:flex;align-items:center;justify-content:center;gap:4px;}
.wa-hint i{color:#25d366;}
.err-msg{font-size:.68rem;color:var(--red);margin-top:4px;display:none;}
.err-msg.show{display:block;}
.jc-active-note{padding:13px 15px;text-align:center;font-size:.78rem;color:var(--green);
  font-weight:600;background:var(--green-bg);border-top:1px solid var(--border);}

/* ══════════════════════════════════════════════════════ TERMINAL ═══ */
.ajb{background:linear-gradient(135deg,#8a6e47,#9a8053);border-radius:14px;padding:16px 18px;
  margin-bottom:12px;color:#fff;position:relative;overflow:hidden;}
.ajb::before{content:'';position:absolute;right:-24px;top:-24px;width:120px;height:120px;
  border-radius:50%;background:rgba(255,255,255,.08);}
.ajb-sr{font-size:.72rem;opacity:.8;margin-bottom:2px;position:relative;}
.ajb-client{font-size:1.35rem;font-weight:700;
  margin-bottom:2px;position:relative;}
.ajb-site{font-size:.78rem;opacity:.85;display:flex;align-items:center;gap:5px;position:relative;}
.ajb-meta{display:flex;gap:8px;margin-top:11px;flex-wrap:wrap;position:relative;}
.ajb-chip{background:rgba(255,255,255,.18);border:1px solid rgba(255,255,255,.3);border-radius:20px;
  font-size:.65rem;padding:3px 10px;font-weight:500;display:flex;align-items:center;gap:4px;}

.timer-widget{background:var(--card);border:1px solid var(--border);border-radius:14px;padding:14px 16px;
  margin-bottom:12px;display:flex;align-items:center;gap:14px;box-shadow:var(--shadow);}
.tw-icon{width:44px;height:44px;border-radius:11px;display:flex;align-items:center;
  justify-content:center;font-size:1.2rem;flex-shrink:0;background:var(--surface);}
.tw-time{font-size:1.7rem;font-weight:700;
  font-variant-numeric:tabular-nums;color:var(--text);line-height:1;}
.tw-lbl{font-size:.68rem;color:var(--muted);margin-top:3px;}
.tw-status{margin-left:auto;font-size:.68rem;font-weight:700;padding:3px 10px;border-radius:20px;}
.status-idle{background:var(--surface);color:var(--muted);}
.status-live{background:var(--green-bg);color:var(--green);animation:pulse 2s ease-in-out infinite;}
.status-done{background:var(--gold-bg);color:var(--gold);}

.punch-row{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:10px;
    margin-bottom:12px;
}
.punch-btn{border:none;border-radius:11px;padding:.75rem 1rem;font-size:.84rem;font-weight:700;
  cursor:pointer;display:flex;align-items:center;justify-content:center;gap:6px;transition:all .18s;color:#fff;}
.punch-btn:active:not(:disabled){transform:scale(.97);}
.punch-btn:disabled{opacity:.4;cursor:not-allowed;}
.btn-punchin{background:linear-gradient(135deg,#15803d,#16a34a);box-shadow:0 4px 14px rgba(21,128,61,.3);}
.btn-punchout{background:linear-gradient(135deg,#dc2626,#ef4444);box-shadow:0 4px 14px rgba(220,38,38,.3);}

.compliance-card{background:var(--card);border:1px solid var(--border);border-radius:14px;
  padding:15px;margin-bottom:12px;box-shadow:var(--shadow);}
.comp-title{font-size:.78rem;font-weight:700;color:var(--text);margin-bottom:12px;
  display:flex;align-items:center;gap:6px;}
.comp-title i{color:var(--gold);}
.comp-item{display:flex;align-items:center;gap:10px;padding:10px 0;border-bottom:1px solid var(--border);
  flex-wrap:wrap;}
.comp-item:last-of-type{border-bottom:none;}
.comp-icon-wrap{width:40px;height:40px;border-radius:11px;display:flex;align-items:center;
  justify-content:center;font-size:1.1rem;flex-shrink:0;background:var(--surface);}
.comp-info{flex:1;min-width:110px;}
.comp-lbl{font-size:.78rem;font-weight:600;color:var(--text);}
.comp-hint{font-size:.68rem;color:var(--muted);margin-top:1px;}
.comp-status{font-size:.66rem;font-weight:700;padding:3px 9px;border-radius:20px;flex-shrink:0;white-space:nowrap;}
.cs-done{background:var(--green-bg);color:var(--green);}
.cs-pending{background:var(--surface);color:var(--muted);}
.comp-upload-btn{background:none;border:1px solid var(--border2);border-radius:8px;color:var(--muted);
  font-size:.72rem;padding:5px 11px;cursor:pointer;display:flex;align-items:center;gap:4px;
  flex-shrink:0;transition:all .15s;}
.comp-upload-btn:hover:not(:disabled){border-color:var(--gold);color:var(--gold);}
.comp-upload-btn:disabled{opacity:.4;cursor:not-allowed;}
.upload-input{position:absolute;width:1px;height:1px;opacity:0;overflow:hidden;z-index:-1;pointer-events:none;}

.photo-strip{display:flex;gap:8px;flex-wrap:wrap;padding:4px 0 10px;}
.photo-strip:empty{display:none;}
.ps-item{position:relative;width:64px;height:64px;border-radius:9px;overflow:hidden;
  border:1px solid var(--border);flex-shrink:0;background:var(--surface);}
.ps-item img{width:100%;height:100%;object-fit:cover;cursor:pointer;display:block;}
.ps-del{position:absolute;top:2px;right:2px;width:18px;height:18px;border-radius:50%;
  background:rgba(0,0,0,.6);color:#fff;border:none;cursor:pointer;font-size:.6rem;
  display:flex;align-items:center;justify-content:center;padding:0;line-height:1;}
.ps-del:hover{background:var(--red);}

.lock-info{background:var(--red-bg);border:1px solid rgba(220,38,38,.2);border-radius:10px;
  padding:10px 12px;font-size:.73rem;color:var(--muted);display:flex;align-items:flex-start;
  gap:7px;margin-bottom:12px;line-height:1.5;}
.lock-info i{color:var(--red);flex-shrink:0;margin-top:1px;}
.lock-info.amber{background:var(--amber-bg);border-color:rgba(217,119,6,.2);}
.lock-info.amber i{color:var(--amber);}

.location-badge{background:var(--card2);border:1px solid var(--border);border-radius:10px;
  padding:9px 12px;font-size:.72rem;color:var(--muted);display:flex;align-items:center;
  gap:7px;margin-bottom:12px;flex-wrap:wrap;}
.location-badge i{color:var(--gold);}
.location-badge .coords{font-size:.7rem;font-weight:600;color:var(--text);font-variant-numeric:tabular-nums;}

.tx-item{display:flex;align-items:center;gap:9px;padding:8px 0;border-bottom:1px solid var(--border);font-size:.76rem;}
.tx-item:last-child{border-bottom:none;}
.tx-item .body{min-width:0;flex:1;}
.tx-item .en{font-weight:600;color:var(--text);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;}
.tx-item .ec{font-size:.68rem;color:var(--muted);}
.tx-item .ea{font-weight:700;color:var(--text);white-space:nowrap;}
.tx-item .er{font-size:.68rem;color:var(--light);}
.tx-receipt{width:30px;height:30px;border-radius:7px;object-fit:cover;border:1px solid var(--border);
  cursor:pointer;flex-shrink:0;}
.tx-total{text-align:right;font-size:.78rem;font-weight:700;color:var(--text);
  padding-top:8px;margin-top:2px;border-top:1px solid var(--border);}
.tx-empty{text-align:center;padding:14px;font-size:.78rem;color:var(--muted);}

/* ══════════════════════════════════════════════════════ DRAWER EXTRAS ═══ */
.rs-tabs{display:flex;background:var(--surface);border-radius:10px;padding:3px;margin-bottom:14px;}
.rs-tab{flex:1;padding:8px;font-size:.78rem;font-weight:600;border:none;background:none;
  border-radius:8px;cursor:pointer;color:var(--muted);transition:all .18s;}
.rs-tab.active{background:var(--card);color:var(--gold);box-shadow:var(--shadow);}
.rs-panel{display:none;}
.rs-panel.show{display:block;}
.receipt-zone{border:2px dashed var(--border2);border-radius:11px;padding:18px;text-align:center;
  cursor:pointer;background:var(--card2);transition:all .2s;position:relative;margin-top:4px;}
.receipt-zone:hover{border-color:var(--gold);background:var(--gold-bg);}
.receipt-zone input{position:absolute;inset:0;opacity:0;cursor:pointer;}
.receipt-zone i{font-size:1.6rem;color:var(--muted);display:block;margin-bottom:5px;}
.receipt-zone p{font-size:.75rem;color:var(--muted);margin:0;}
.receipt-preview{display:none;background:var(--green-bg);border:1px solid rgba(21,128,61,.2);
  border-radius:9px;padding:8px 11px;font-size:.75rem;color:var(--green);
  align-items:center;gap:6px;margin-top:7px;}
.receipt-preview.show{display:flex;}
.terms-box{max-height:200px;overflow-y:auto;border:1px solid var(--border2);border-radius:10px;
  padding:13px;font-size:.76rem;line-height:1.55;color:var(--text);background:var(--card2);}
.terms-box p{margin:0 0 9px;}
.terms-box p:last-child{margin-bottom:0;}
.policy-check{display:flex;align-items:flex-start;gap:9px;margin-top:12px;font-size:.78rem;
  cursor:pointer;color:var(--text);line-height:1.45;}
.policy-check input{margin-top:2px;width:16px;height:16px;flex-shrink:0;accent-color:#9a8053;}
.sig-pad{border:1px solid var(--border2);border-radius:10px;background:#fff;position:relative;}
.sig-pad canvas{width:100%;height:170px;display:block;touch-action:none;border-radius:10px;}

/* ══════════════════════════════════════════════════════ HISTORY / PROFILE ═══ */
.hist-card{background:var(--card);border:1px solid var(--border);border-radius:13px;
  padding:13px 15px;margin-bottom:10px;box-shadow:var(--shadow);}
.hist-top{display:flex;align-items:center;justify-content:space-between;margin-bottom:6px;gap:8px;}
.hist-ref{font-size:.98rem;font-weight:700;color:var(--gold);}
.hist-date{font-size:.68rem;color:var(--muted);}
.hist-client{font-size:.82rem;font-weight:600;color:var(--text);margin-bottom:2px;}
.hist-site{font-size:.7rem;color:var(--muted);margin-bottom:9px;}
.hist-grid{display:flex;gap:8px;flex-wrap:wrap;align-items:center;}
.hist-chip{font-size:.68rem;color:var(--muted);display:flex;align-items:center;gap:4px;
  background:var(--card2);border:1px solid var(--border);border-radius:8px;padding:3px 9px;}
.hist-total{margin-left:auto;
  font-size:1rem;font-weight:700;color:var(--text);}

.prof-hero{background:linear-gradient(135deg,#8a6e47,#9a8053);border-radius:16px;padding:24px 16px;
  text-align:center;color:#fff;margin-bottom:14px;position:relative;overflow:hidden;}
.prof-hero::before{content:'';position:absolute;right:-30px;top:-30px;width:130px;height:130px;
  border-radius:50%;background:rgba(255,255,255,.08);}
.prof-avatar{width:72px;height:72px;border-radius:50%;margin:0 auto 10px;background:rgba(255,255,255,.22);
  border:3px solid rgba(255,255,255,.45);display:flex;align-items:center;justify-content:center;
  font-size:1.4rem;font-weight:700;position:relative;}
.prof-name{font-size:1.5rem;font-weight:700;position:relative;}
.prof-role{font-size:.72rem;opacity:.85;margin-top:2px;position:relative;}
.info-row{display:flex;align-items:center;gap:11px;padding:11px 0;border-bottom:1px solid var(--border);}
.info-row:last-child{border-bottom:none;padding-bottom:0;}
.info-ico{width:36px;height:36px;border-radius:10px;background:var(--gold-bg);display:flex;
  align-items:center;justify-content:center;color:var(--gold);flex-shrink:0;}
.info-lbl{font-size:.66rem;text-transform:uppercase;letter-spacing:.06em;color:var(--muted);}
.info-val{font-size:.82rem;font-weight:500;color:var(--text);word-break:break-word;}

/* Two columns once there is room — job list beside nothing else, so cards
   simply get wider rather than stretching text lines. */
  /* Keep the four terminal action buttons on one visual rhythm */
#expenseBtn,
#rsBtn{
  width:100%;
  border-radius:11px;
  padding:.75rem 1rem;
  font-size:.84rem;
  font-weight:700;
  display:flex;
  align-items:center;
  justify-content:center;
  gap:6px;
  margin:0 0 10px;
  box-sizing:border-box;
}

.punch-row{margin-bottom:10px;}

.cg {
    font-family: UI-MONOSPACE;
    letter-spacing: -.01em;
}

#expAmount
{
  margin-bottom: 12px;
}

#expCategory
{
  margin-bottom: 12px;
}

.jc-from {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  padding: 2px 8px;
  border-radius: 10px;
  background: #FAEEDA;
  color: #854F0B;
  font-size: .68rem;
  font-weight: 500;
  white-space: nowrap;
}



.tx-del{
  background:none;border:none;cursor:pointer;padding:4px 6px;margin-left:6px;
  color:var(--muted);border-radius:6px;font-size:.9rem;line-height:1;
}
.tx-del:hover{color:var(--red,#ef4444);background:rgba(239,68,68,.08);}
.tx-del:disabled{opacity:.4;cursor:not-allowed;}

</style>
@endpush

{{-- ══════════════════════════════════════════════════════ TABS ═══ --}}
@section('tabs')
  <a class="tab-btn jump" href="{{ $routes['dashboard'] ?? route('worker.dashboard') }}">
    <i class="bi bi-speedometer2"></i><span>Dashboard</span>
  </a>
  <button class="tab-btn active" data-tab="pipeline">
    <i class="bi bi-list-task"></i><span>Pipeline</span>
  </button>
  <button class="tab-btn" data-tab="terminal">
    <i class="bi bi-broadcast"></i><span>My Jobs</span>
    <span class="tab-live hidden" id="activeDot"></span>
  </button>


 <button class="tab-btn" data-tab="realloc">
  <i class="bi bi-arrow-left-right"></i><span>Reallocated</span>
  <span class="pane-count" id="reallocDot"></span>
</button>

  <button class="tab-btn" data-tab="history">
    <i class="bi bi-clock-history"></i><span>History</span>
  </button>
  <button class="tab-btn" data-tab="profile">
    <i class="bi bi-person-circle"></i><span>Profile</span>
  </button>
@endsection

{{-- ══════════════════════════════════════════════════════ CONTENT ═══ --}}
@section('content')

<!-- ─────────── PIPELINE ─────────── -->
<div class="tab-pane active" id="tab-pipeline">
  <div class="pane-head">
    <div>
      <div class="pane-title cg"><i class="bi bi-list-task"></i>My pipeline</div>
      <div class="pane-sub" id="pipelineSubtitle">Loading jobs&hellip;</div>
    </div>
    <span class="pane-count" id="pipelineCount">0 jobs</span>
  </div>

  <div class="tab-filter" id="tabFilter">
    <button class="tf-btn active" data-filter="Pending">Pending</button>
    <button class="tf-btn" data-filter="Accepted">Accepted</button>
    <button class="tf-btn" data-filter="Rework">Rework</button>
    <button class="tf-btn" data-filter="Rescheduled">Rescheduled</button>
    <button class="tf-btn" data-filter="On Hold">On hold</button>
    <button class="tf-btn" data-filter="Review">Review</button>
    <button class="tf-btn" data-filter="Completed">Completed</button>
  </div>


  <div class="tab-filter" id="sourceFilter" style="display:none;margin-top:8px;">
  <button class="tf-btn active" data-source="own">Own</button>
  <button class="tf-btn" data-source="reallocated">Reallocated</button>
</div>

  <div id="jobList"></div>
</div>

<!-- ─────────── TERMINAL ─────────── -->
<div class="tab-pane" id="tab-terminal">
  <div id="terminalEmpty" class="card">
    <div class="empty-state">
      <i class="bi bi-broadcast"></i>
      <h6>No active job</h6>
      <p>Make a job active from the pipeline and the terminal opens here.</p>
      <a href="#" data-goto="pipeline">Back to my pipeline</a>
    </div>
  </div>

  <div id="terminalBody" class="hidden">
    <div class="pane-head">
      <div>
        <div class="pane-title cg" id="termTitle"><i class="bi bi-broadcast"></i>Job terminal</div>
        <div class="pane-sub" id="termSub">Active job</div>
      </div>
    </div>

    <div class="ajb" id="jobBanner"></div>

    <div class="timer-widget">
      <div class="tw-icon" id="timerIcon"><i class="bi bi-clock" style="color:var(--muted);"></i></div>
      <div>
        <div class="tw-time" id="timerDisplay">00:00:00</div>
        <div class="tw-lbl" id="timerLabel">Not started</div>
      </div>
      <div class="tw-status status-idle" id="timerStatus">Idle</div>
    </div>

    <div class="location-badge" id="geoBadge">
      <i class="bi bi-geo-alt"></i><span>Location not captured yet</span>
    </div>

    <div class="section-card">
      <div class="section-card-hdr"><i class="bi bi-card-text" style="color:var(--gold);"></i>Work description</div>
      <div class="section-card-body">
        <textarea class="d-remark" id="workDesc" rows="3" placeholder="What work is being carried out&hellip;"></textarea>
      </div>
    </div>

    <div class="lock-info amber hidden" id="etaNotice">
      <i class="bi bi-clock-history"></i><span id="etaNoticeText"></span>
    </div>

    <div class="punch-row">
      <button class="punch-btn btn-punchin" id="punchInBtn"><i class="bi bi-play-fill"></i>Start Job</button>
      <button class="punch-btn btn-punchout" id="punchOutBtn" disabled><i class="bi bi-check2-square"></i>Finish job</button>
    </div>

    <button class="btn-outline" id="expenseBtn" disabled><i class="bi bi-receipt"></i>Log Material Expense</button>
    <button class="btn-outline brand hidden" id="rsBtn"><i class="bi bi-calendar2-event"></i>Reschedule / Hold Job</button>

    <div class="lock-info hidden" id="lockInfo">
      <i class="bi bi-lock-fill"></i>
      <span>Finish job is locked. Upload at least one <strong>before</strong> and one <strong>after</strong> photo to unlock.</span>
    </div>

    <div class="compliance-card">
      <div class="comp-title"><i class="bi bi-shield-check"></i>Compliance Uploads</div>

      <div class="comp-item">
        <div class="comp-icon-wrap" id="beforeIcon"><i class="bi bi-camera-fill" style="color:var(--amber);"></i></div>
        <div class="comp-info">
          <div class="comp-lbl">Before photos</div>
          <div class="comp-hint">Site condition before work</div>
        </div>
        <span class="comp-status cs-pending" id="beforeStatus">Pending</span>
        <button class="comp-upload-btn" data-upload="before" disabled><i class="bi bi-camera"></i>Add</button>
        <input type="file" id="beforeInput" class="upload-input" accept="image/*" capture="environment" multiple>
      </div>
      <div class="photo-strip" id="beforeStrip"></div>

      <div class="comp-item">
        <div class="comp-icon-wrap" id="afterIcon"><i class="bi bi-camera-fill" style="color:var(--gold);"></i></div>
        <div class="comp-info">
          <div class="comp-lbl">After photos</div>
          <div class="comp-hint">Site condition after work</div>
        </div>
        <span class="comp-status cs-pending" id="afterStatus">Pending</span>
        <button class="comp-upload-btn" data-upload="after" disabled><i class="bi bi-camera"></i>Add</button>
        <input type="file" id="afterInput" class="upload-input" accept="image/*" capture="environment" multiple/>
      </div>
      <div class="photo-strip" id="afterStrip"></div>
    </div>

    <div class="section-card">
      <div class="section-card-hdr"><i class="bi bi-receipt" style="color:var(--amber);"></i>Material expenses</div>
      <div class="section-card-body" id="expenseListWrap">
        <div class="tx-empty">No expenses logged yet.</div>
      </div>
    </div>
  </div>
</div>



<!-- <div class="tab-pane" id="tab-realloc">
  <div class="pane-head">
    <div>
      <div class="pane-title cg"><i class="bi bi-arrow-left-right"></i>Reallocated to me</div>
      <div class="pane-sub" id="reallocSubtitle">Jobs moved over from another ML</div>
    </div>
    <span class="pane-count" id="reallocCount">0 jobs</span>
  </div>

  <div id="reallocList"></div>
</div> -->


<div class="tab-pane" id="tab-realloc">
  <div class="pane-head">
    <div>
      <div class="pane-title cg"><i class="bi bi-arrow-left-right"></i>Reallocated Jobs</div>
      <div class="pane-sub">Jobs moved over from another ML</div>
    </div>
    <span class="pane-count">{{ count($reallocated ?? []) }} jobs</span>
  </div>

  @forelse ($reallocated ?? [] as $r)
    <article class="pl-card">
      <div class="jc-main">
        <div class="jc-top">
          <span class="jc-sr">{{ $r['ref'] }}</span>
          <!-- <span class="pill pill-red">Reallocated</span> -->
        </div>

        <div class="jc-client">{{ $r['client'] }}</div>
        <div class="jc-contract"><i class="bi bi-file-earmark-text"></i> {{ $r['project'] }}</div>

        <div class="jc-meta">
          <span class="jc-meta-item"><i class="bi bi-geo-alt"></i><span>{{ $r['site'] }}</span></span>
          <span class="jc-meta-item"><i class="bi bi-person-fill"></i><span>Reallocated To: {{ $r['reallocateUser'] }}</span></span>
          <span class="jc-meta-item"><i class="bi bi-calendar3"></i><span>{{ $r['eta'] }}</span></span>
          <!-- <span class="pill pill-blue">Reallocated: {{ $r['reallocate'] }}</span> -->
        </div>
      </div>
    </article>
  @empty
    <div class="card">
      <div class="empty-state">
        <i class="bi bi-inbox"></i>
        <h6>Nothing reallocated</h6>
        <p>Jobs moved to you from another ML appear here.</p>
      </div>
    </div>
  @endforelse
</div>

<!-- ─────────── HISTORY ─────────── -->
<div class="tab-pane" id="tab-history">
  <div class="pane-head">
    <div>
      <div class="pane-title cg"><i class="bi bi-clock-history"></i>Work history</div>
      <div class="pane-sub" id="histSub">Loading&hellip;</div>
    </div>
    <span class="pane-count" id="histCount">0 jobs</span>
  </div>

  <div class="mini-row">
    <div class="mini-box"><div class="mini-val" id="statJobs">&mdash;</div><div class="mini-lbl">Jobs</div></div>
    <div class="mini-box"><div class="mini-val" id="statHours">&mdash;</div><div class="mini-lbl">Hours</div></div>
    <div class="mini-box"><div class="mini-val" id="statExp">&mdash;</div><div class="mini-lbl">AED mat.</div></div>
  </div>

  <div id="histList">
    <div class="skel"></div><div class="skel"></div><div class="skel"></div>
  </div>
</div>

<!-- ─────────── PROFILE ─────────── -->
<div class="tab-pane" id="tab-profile">
  <div class="pane-head">
    <div>
      <div class="pane-title cg"><i class="bi bi-person-circle"></i>My profile</div>
      <div class="pane-sub">Account &amp; performance</div>
    </div>
    <button class="action-btn outline" id="profRefresh"><i class="bi bi-arrow-clockwise"></i>Refresh</button>
  </div>

  <div id="profBody">
    <div class="skel" style="height:170px;"></div>
    <div class="skel" style="height:120px;"></div>
  </div>
</div>

@endsection

{{-- ══════════════════════════════════════════════════════ DRAWERS ═══ --}}
@section('drawers')

<div class="overlay" data-overlay="rs"></div>
<div class="drawer" data-drawer="rs">
  <div class="drawer-handle"></div>
  <div class="drawer-hdr">
    <h6><i class="bi bi-calendar2-event" style="color:var(--gold);"></i>Reschedule or Hold</h6>
    <button class="drawer-close" data-close="rs"><i class="bi bi-x-lg"></i></button>
  </div>
  <div class="drawer-body">
    <div class="drawer-sr"><i class="bi bi-link-45deg"></i>Job: <strong id="rsSrRef">&mdash;</strong></div>

    <div class="rs-tabs">
      <button class="rs-tab active" data-rstab="reschedule"><i class="bi bi-calendar-check"></i> Reschedule</button>
      <button class="rs-tab" data-rstab="hold"><i class="bi bi-pause-circle"></i> Keep on Hold</button>
    </div>

    <div class="rs-panel show" id="rsPanel-reschedule">
      <label class="d-label" for="rsDate">New date <span class="req">*</span></label>
      <input type="date" class="d-input" id="rsDate"/>
      <label class="d-label" for="rsTime">New time <span class="req">*</span></label>
      <input type="time" class="d-input" id="rsTime"/>
      <label class="d-label" for="rsRemark">Reason <span class="req">*</span></label>
      <textarea class="d-remark" id="rsRemark" rows="3" placeholder="Why is this moving&hellip;"></textarea>
      <div class="drawer-actions">
        <button class="btn-cancel" data-close="rs">Cancel</button>
        <button class="btn-save" id="rsConfirmBtn"><i class="bi bi-calendar-check"></i> Reschedule</button>
      </div>
    </div>

    <div class="rs-panel" id="rsPanel-hold">
      <div class="hold-note">
        <i class="bi bi-info-circle-fill"></i>
        The job goes on hold and the client is notified. Resume it from the pipeline when you can attend.
      </div>
      <label class="d-label" for="holdRemark">Reason for hold <span class="req">*</span></label>
      <textarea class="d-remark" id="holdRemark" rows="3" placeholder="Why is this on hold&hellip;"></textarea>
      <div class="drawer-actions">
        <button class="btn-cancel" data-close="rs">Cancel</button>
        <button class="btn-save warn" id="holdConfirmBtn"><i class="bi bi-pause-circle"></i> Confirm hold</button>
      </div>
    </div>
  </div>
</div>

<div class="overlay" data-overlay="exp"></div>
<div class="drawer" data-drawer="exp">
  <div class="drawer-handle"></div>
  <div class="drawer-hdr">
    <h6><i class="bi bi-receipt" style="color:var(--amber);"></i>Log Material Expense</h6>
    <button class="drawer-close" data-close="exp"><i class="bi bi-x-lg"></i></button>
  </div>
  <div class="drawer-body">
    <div class="drawer-sr"><i class="bi bi-link-45deg"></i>Linked to: <strong id="expSrRef">&mdash;</strong></div>

    <label class="d-label" for="expCategory">Category <span class="req">*</span></label>
    <select class="d-select" id="expCategory">
      <option value="">&mdash; Select category &mdash;</option>
      @foreach (($expenseCategories ?? []) as $cat)
        <option value="{{ $cat }}">{{ $cat }}</option>
      @endforeach
    </select>

    <div id="expNameWrap" class="hidden">
      <label class="d-label" for="expName">Item name <span class="req">*</span></label>
      <input type="text" class="d-input" id="expName" placeholder="e.g. 20mm PVC elbow"/>
    </div>

    <label class="d-label" for="expAmount">Amount (AED) <span class="req">*</span></label>
    <input type="number" class="d-input" id="expAmount" placeholder="0.00" min="0" step="0.01" inputmode="decimal"/>

    <span class="d-label">Receipt photo</span>
    <div class="receipt-zone">
      <input type="file" id="expReceipt" accept="image/*" capture="environment"/>
      <i class="bi bi-camera-fill"></i>
      <p>Tap to capture receipt</p>
    </div>
    <div class="receipt-preview" id="receiptPreview">
      <i class="bi bi-check-circle-fill"></i><span id="receiptName"></span>
    </div>

    <div class="drawer-actions">
      <button class="btn-cancel" data-close="exp">Cancel</button>
      <button class="btn-save" id="expSaveBtn">Save entry</button>
    </div>
  </div>
</div>

<div class="overlay" data-overlay="fin"></div>
<div class="drawer" data-drawer="fin">
  <div class="drawer-handle"></div>
  <div class="drawer-hdr">
    <h6><i class="bi bi-check2-square" style="color:var(--green);"></i>Finish job</h6>
    <button class="drawer-close" data-close="fin"><i class="bi bi-x-lg"></i></button>
  </div>
  <div class="drawer-body">
    <div class="drawer-sr"><i class="bi bi-link-45deg"></i>Job: <strong id="finSrRef">&mdash;</strong></div>
    <label class="d-label" for="finSummary">Completion summary</label>
    <textarea class="d-remark" id="finSummary" rows="3" placeholder="What was done, parts replaced, outcome&hellip;"></textarea>
    <div class="drawer-actions">
      <button class="btn-cancel" data-close="fin">Cancel</button>
      <button class="btn-save" id="finConfirmBtn"><i class="bi bi-check2-square"></i> Finish job</button>
    </div>
  </div>
</div>

<div class="overlay" data-overlay="sign"></div>
<div class="drawer" data-drawer="sign">
  <div class="drawer-handle"></div>
  <div class="drawer-hdr">
    <h6><i class="bi bi-pen" style="color:var(--gold);"></i>Client acceptance</h6>
    <button class="drawer-close" data-close="sign"><i class="bi bi-x-lg"></i></button>
  </div>
  <div class="drawer-body">
    <div class="drawer-sr"><i class="bi bi-link-45deg"></i>Job: <strong id="signSrRef">&mdash;</strong></div>

    <div id="termsBlock">
      <span class="d-label">Terms &amp; policy</span>
      <div id="termsScroll" class="terms-box">
        <p><strong>Service completion acceptance</strong></p>
        <p>By signing below, the client confirms the work described has been carried out to a satisfactory standard and the site has been left in acceptable condition.</p>
        <p>The client acknowledges the materials logged against this job and agrees these were used in the course of the work.</p>
        <p>Signing does not waive any manufacturer or workmanship warranty applicable to the service.</p>
        <p>Any dispute regarding the completed work must be raised within the warranty window stated in the service agreement.</p>
        <p>This acceptance is recorded electronically with a timestamp and forms part of the service record.</p>
      </div>

      <label class="policy-check">
        <input type="checkbox" id="policyCheck"/>
        <span>I have read and accept the terms and policy above on behalf of the client.</span>
      </label>
    </div>

    <div id="signBlock" class="hidden" style="margin-top:15px;">
      <label class="d-label" for="clientNameInput">Client name <span class="req">*</span></label>
      <input type="text" class="d-input" id="clientNameInput"  style="margin-bottom:15px;" placeholder="Name of person signing"/>
      <span class="d-label">Signature <span class="req">*</span></span>
      <div class="sig-pad"><canvas id="sigCanvas"></canvas></div>
      <button type="button" class="action-btn outline" id="sigClearBtn" style="margin-top:7px;">
        <i class="bi bi-eraser"></i> Clear
      </button>
    </div>

    <div class="drawer-actions">
      <button class="btn-cancel" data-close="sign">Cancel</button>
      <button class="btn-save" id="signSubmitBtn" disabled><i class="bi bi-check2-square"></i> Accept &amp; sign</button>
    </div>
  </div>
</div>

@endsection

{{-- ══════════════════════════════════════════════════════ SCRIPT ═══ --}}
@push('scripts')

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
'use strict';

/* ══════════════════════════════════════════════════════
   BACKEND DATA
══════════════════════════════════════════════════════ */
const JOBS   = @json($jobs ?? []);
const ACTIVE = @json($activeJob ?? null);
const ROUTES = @json($routes ?? []);
const LETTERHEAD = @json($letterhead ?? ['header' => null, 'footer' => null, 'watermark' => null]);

  const SLA_MATRIX = @json($slaMatrix);

/* ══════════════════════════════════════════════════════
   STATE
   activeRef  = display ref ("SR-2026-0123") -> DOM ids
   activeSrId = numeric primary key          -> API calls
══════════════════════════════════════════════════════ */
let activeRef      = null;
let activeSrId     = null;
let punchInTime    = null;
let timerInterval  = null;
let expandedRef    = null;
let currentFilter  = 'Pending';
let uploads        = { before: [], after: [] };
let expenses       = [];
let historyLoaded  = false;
let profileLoaded  = false;
let signatureUploaded = false;
let activeEtaAt    = null;
let etaGateTimer   = null;
const EARLY_START_GRACE_MS = 15 * 60 * 1000;

/* Block sign-out while a punch is open — the layout checks this. */
window.beforeSignOut = () => {
  if (punchInTime) {
    showToast('warning', 'Job in progress', 'Finish or hold the current job before signing out.');
    return false;
  }
};

/* ══════════════════════════════════════════════════════
   GEOLOCATION
══════════════════════════════════════════════════════ */
let lastFix = null;                       // { lat, lng, accuracy, address }
const MAX_ACCEPTABLE_ACCURACY = 100;      // metres

/**
 * Ask the browser for a position. Resolves to null rather than rejecting —
 * a denied permission must not block the punch.
 */
function getPosition({ timeout = 12000, highAccuracy = true } = {}) {
  return new Promise((resolve) => {
    if (!navigator.geolocation) { resolve(null); return; }

    navigator.geolocation.getCurrentPosition(
      (pos) => resolve({
        lat: +pos.coords.latitude.toFixed(7),
        lng: +pos.coords.longitude.toFixed(7),
        accuracy: pos.coords.accuracy != null ? +pos.coords.accuracy.toFixed(2) : null,
      }),
      () => resolve(null),
      { enableHighAccuracy: highAccuracy, timeout, maximumAge: 30000 }
    );
  });
}

/** Reverse-geocode via OSM. Failure is non-fatal — coords alone are enough. */
async function reverseGeocode(lat, lng) {
  try {
    const url = `https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lng}`;
    const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
    if (!res.ok) return null;
    const body = await res.json();
    return body.display_name ?? null;
  } catch { return null; }
}

async function captureLocation() {
  const fix = await getPosition();
  if (!fix) { lastFix = null; renderLocationBadge(null); return null; }

  // A coarse fix on desktop is usually IP-based and can be hundreds of km off.
  fix.coarse = fix.accuracy != null && fix.accuracy > MAX_ACCEPTABLE_ACCURACY;
  fix.address = await reverseGeocode(fix.lat, fix.lng);
  lastFix = fix;
  renderLocationBadge(fix);
  return fix;
}

/** Append lat/lng/accuracy/address to a payload object or FormData. */
function withGeo(payload, fix) {
  if (!fix) return payload;

  if (payload instanceof FormData) {
    payload.append('lat', fix.lat);
    payload.append('lng', fix.lng);
    if (fix.accuracy != null) payload.append('accuracy', fix.accuracy);
    if (fix.address) payload.append('address', fix.address);
    payload.append('coarse', fix.coarse ? 1 : 0);
    return payload;
  }
  return { ...payload, lat: fix.lat, lng: fix.lng, accuracy: fix.accuracy,
           address: fix.address, coarse: fix.coarse };
}

function renderLocationBadge(fix) {
  const el = $('geoBadge');
  if (!el) return;

  if (!fix) {
    el.innerHTML = '<i class="bi bi-geo-alt-slash"></i>' +
      '<span>Location unavailable &mdash; enable GPS for site verification</span>';
    return;
  }

  el.innerHTML =
    `<i class="bi bi-geo-alt-fill" style="color:${fix.coarse ? 'var(--amber)' : 'var(--gold)'};"></i>` +
    `<span class="coords">${fix.lat.toFixed(5)}, ${fix.lng.toFixed(5)}</span>` +
    (fix.accuracy != null ? `<span style="margin-left:auto;">&plusmn;${Math.round(fix.accuracy)}m</span>` : '') +
    (fix.coarse ? '<div style="flex-basis:100%;margin-top:4px;color:var(--amber);">Approximate — network fix, not GPS. Use the phone on site.</div>' : '') +
    (fix.address ? `<div style="flex-basis:100%;margin-top:4px;">${esc(fix.address)}</div>` : '');
}

/* ══════════════════════════════════════════════════════
   SMALL HELPERS
══════════════════════════════════════════════════════ */

/** Today as YYYY-MM-DD in the *local* timezone (toISOString would shift it). */
function todayLocal() {
  const d = new Date();
  const pad = (n) => String(n).padStart(2, '0');
  return `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}`;
}

function hhmm(date) {
  return date.toLocaleTimeString('en-IN', { hour: '2-digit', minute: '2-digit' });
}

function fileUrl(a) {
  let p = typeof a === 'string' ? a : (a.url || a.path || '');
  if (!p) return '';
  if (/^https?:\/\//i.test(p)) return p;
  p = String(p).replace(/^\/+/, '');
  if (!/^storage\//i.test(p)) p = 'storage/' + p;
  return '/' + p.replace(/\/{2,}/g, '/');
}

/** Show either the terminal or its empty state. */
function setTerminalVisible(has) {
  $('terminalBody').classList.toggle('hidden', !has);
  $('terminalEmpty').classList.toggle('hidden', has);
  $('activeDot').classList.toggle('hidden', !has);
}

/* ══════════════════════════════════════════════════════
   PIPELINE RENDER
══════════════════════════════════════════════════════ */
const BADGE_MAP = {
  'Pending': 'pill-amber', 'Accepted': 'pill-blue', 'Rework': 'pill-red',
  'Rescheduled': 'pill-blue', 'On Hold': 'pill-amber',
  'Review': 'pill-purple', 'Completed': 'pill-green',
};


// function renderPipeline() {
//   const list = $('jobList');
//   const visible = JOBS.filter((j) => j.status === currentFilter);
//   const reworkCount = JOBS.filter((j) => j.status === 'Rework').length;

//   $('pipelineCount').textContent = `${visible.length} job${visible.length === 1 ? '' : 's'}`;
//   $('pipelineSubtitle').textContent = `${JOBS.length} total \u00b7 ${reworkCount} need rework`;

//   if (!visible.length) {
//     list.innerHTML =
//       '<div class="card"><div class="empty-state"><i class="bi bi-check2-all"></i>' +
//       '<h6>All clear</h6><p>No jobs in this category.</p></div></div>';
//     return;
//   }
//   list.innerHTML = visible.map(buildJobCard).join('');
// }


function renderPipeline() {
  const list = $('jobList');
  let visible = JOBS.filter((j) => j.status === currentFilter);

  if (currentFilter === 'Rework') {
    visible = visible.filter((j) => j.source === currentSource);
  }

  const reworkCount = JOBS.filter((j) => j.status === 'Rework').length;
  $('pipelineCount').textContent = `${visible.length} job${visible.length === 1 ? '' : 's'}`;
  $('pipelineSubtitle').textContent = `${JOBS.length} total · ${reworkCount} need rework`;

  if (!visible.length) {
    list.innerHTML =
      '<div class="card"><div class="empty-state"><i class="bi bi-check2-all"></i>' +
      '<h6>All clear</h6><p>No jobs in this category.</p></div></div>';
    return;
  }
  list.innerHTML = visible.map(buildJobCard).join('');
}

function buildJobCard(job) {
  // const slaClass   = job.hrsAgo > 24 ? 'sla-c' : job.hrsAgo > 8 ? 'sla-w' : 'sla-ok';

  const sla = slaStatus(job, 'dispatch');
  const isResched  = job.status === 'Rescheduled';
  const isAccepted = job.status === 'Accepted';
  const onHold     = job.status === 'On Hold';
  const badgeClass = BADGE_MAP[job.status] ?? 'pill-gold';
  const isExpanded = expandedRef === job.id;
  const isActive   = activeRef === job.id;
  const ref        = esc(job.id);
  const shortSite  = String(job.site ?? '').split(',')[0];

const isRealloc = job.source === 'reallocated';
const statusLbl = isRealloc ? 'Reallocated' : job.status;
const statusCls = isRealloc ? 'pill-purple' : badgeClass;

  const reworkBlock = job.reworkNote
    ? `<div class="jc-exp-section">
         <div class="exp-label">
           <i class="bi bi-exclamation-triangle-fill" style="color:var(--red);"></i>Rework instructions
         </div>
         <div class="rework-note">${esc(job.reworkNote)}</div>
       </div>`
    : '';

  const historyBlock = (job.history ?? []).length
    ? `<div class="jc-exp-section">
         <div class="exp-label"><i class="bi bi-clock-history" style="color:var(--gold2);"></i>History log</div>
         ${job.history.map((h) => `<div class="hist-line">${esc(h)}</div>`).join('')}
       </div>`
    : '';

  const rescheduleBlock = job.rescheduleReason
    ? `<div class="jc-exp-section">
         <div class="exp-label">
           <i class="bi bi-calendar2-event" style="color:var(--blue);"></i>Reschedule details
           ${job.rescheduleCount > 1 ? `<span style="margin-left:auto;font-weight:700;">&times;${Number(job.rescheduleCount)}</span>` : ''}
         </div>
         <div class="exp-text" style="margin-bottom:6px;">
           ${job.previousEta ? `<span style="text-decoration:line-through;opacity:.6;">${esc(job.previousEta)}</span> &rarr; ` : ''}
           <strong>${esc(job.eta ?? '—')}</strong>
         </div>
         <div class="rework-note" style="background:var(--blue-bg);border-color:rgba(37,99,235,.2);color:var(--blue);">
           ${esc(job.rescheduleReason)}
         </div>
         <div style="font-size:.66rem;color:var(--muted);margin-top:5px;">
           Logged ${esc(job.rescheduledAt ?? '—')}
         </div>
       </div>`
    : '';

  const photoBlock = (job.attachments ?? []).length
    ? `<div class="jc-exp-section">
         <div class="exp-label"><i class="bi bi-images" style="color:var(--amber);"></i>Attached files</div>
         <div class="photo-row">
           ${job.attachments.map((a) => {
             const url  = fileUrl(a);
             const name = url.split('/').pop().split('?')[0];

             if (/\.(png|jpe?g|gif|webp|bmp|svg)(\?|$)/i.test(url)) {
               return `<div class="photo-thumb" title="${esc(name)}"
                            onclick="openLightbox('image','${esc(url)}','${esc(name)}')">
                         <img src="${esc(url)}" alt="" loading="lazy">
                       </div>`;
             }
             if (/\.pdf(\?|$)/i.test(url)) {
               return `<div class="photo-thumb photo-pdf" title="${esc(name)}"
                            onclick="openLightbox('pdf','${esc(url)}','${esc(name)}')">
                         <iframe src="${esc(url)}#toolbar=0&navpanes=0&view=FitH" scrolling="no"></iframe>
                       </div>`;
             }
             return `<a class="photo-thumb" href="${esc(url)}" target="_blank" title="${esc(name)}">
                       <i class="bi bi-file-earmark-arrow-down" style="color:var(--gold);"></i>
                     </a>`;
           }).join('')}
         </div>
       </div>`
    : '';

  const footer = isActive
    ? `<div class="jc-active-note">
         <i class="bi bi-check2-circle"></i> This job is live in the terminal
       </div>`
    : onHold
    ? `<div class="eta-form">
         <div class="eta-title"><i class="bi bi-pause-circle"></i>Job on hold</div>
         <button class="accept-btn" data-resume="${ref}" data-srid="${Number(job.sr_id)}">
           <i class="bi bi-play-circle"></i>Resume job
         </button>
       </div>`
    : isAccepted
    ? `<div class="eta-form">
         <div class="eta-title"><i class="bi bi-check2-circle" style="color:var(--green);"></i>Accepted &middot; ETA ${esc(job.eta ?? '—')}</div>
         <button class="accept-btn go" data-activate="${ref}" data-srid="${Number(job.sr_id)}">
           <i class="bi bi-broadcast"></i>Make active
         </button>
         <button class="accept-btn ghost" data-rsagain="${ref}" data-srid="${Number(job.sr_id)}">
           <i class="bi bi-calendar2-event"></i>Reschedule
         </button>
       </div>`
    : isResched
    ? `<div class="eta-form">
         <div class="eta-title"><i class="bi bi-calendar2-event" style="color:var(--blue);"></i>Rescheduled &middot; ETA ${esc(job.eta ?? '—')}</div>
         <button class="accept-btn blue" data-activate="${ref}" data-srid="${Number(job.sr_id)}">
           <i class="bi bi-broadcast"></i>Make active
         </button>
         <button class="accept-btn ghost" data-rsagain="${ref}" data-srid="${Number(job.sr_id)}">
           <i class="bi bi-calendar2-event"></i>Reschedule again
         </button>
       </div>`
    : job.status === 'Completed'
    ? `<div class="jc-active-note" style="color:var(--muted);background:var(--surface);">
         <i class="bi bi-check2-all"></i> Submitted &mdash; nothing left to do here
       </div>`
    : `<div class="eta-form">
         <div class="eta-title"><i class="bi bi-calendar-check"></i>Set expected attendance (ETA)</div>
         <div class="eta-grid">
           <div class="eta-field">
             <label for="etaDate-${ref}">Date <span class="req">*</span></label>
             <input type="date" class="eta-input" id="etaDate-${ref}" min="${todayLocal()}" data-eta="${ref}"/>
             <div class="err-msg" id="errDate-${ref}">Date required</div>
           </div>
           <div class="eta-field">
  <label for="etaTime-${ref}">Time <span class="req">*</span></label>
 <input type="time" class="eta-input" id="etaTime-${ref}" data-eta="${ref}"
       oninput="validateEtaTime('${ref}'); refreshAcceptBtn('${ref}')"/>
  <div class="err-msg" id="errTime-${ref}">Time required</div>
</div>
         </div>
         <button class="accept-btn" id="acceptBtn-${ref}" data-accept="${ref}" data-srid="${Number(job.sr_id)}" disabled>
           <i class="bi bi-check2-circle"></i>Accept job
         </button>
         <div class="wa-hint">
           <i class="bi bi-whatsapp"></i>Client gets a live tracking link on acceptance
         </div>
       </div>`;

  return `
    <article class="pl-card${isActive ? ' is-active' : ''}${isAccepted && !isActive ? ' is-accepted' : ''}" id="jcard-${ref}">
      <div class="jc-main" data-toggle="${ref}">
        <div class="jc-top">
          <span class="jc-sr">${ref}</span>
          <div style="display:flex;gap:6px;align-items:center;">
            <span class="pill ${statusCls}">${esc(statusLbl)}</span>
            ${isActive ? '<span class="pill pill-green">&#9679; Live</span>' : ''}
            <i class="bi bi-chevron-down jc-chevron${isExpanded ? ' open' : ''}"></i>
          </div>
        </div>
        <div class="jc-client" style="display:flex;align-items:center;gap:8px;">
  <span>${esc(job.client)}</span>
  ${job.source === 'reallocated'
    ? `<span class="jc-from">Own:<i class="bi bi-person-fill"></i>${esc(job.ownerName)}</span>`
    : ''}
</div>
        <div class="jc-contract"><i class="bi bi-file-earmark-text"></i> ${esc(job.contract)}</div>
        <div class="jc-meta">
          <span class="jc-meta-item"><i class="bi bi-tools"></i><span>${esc(job.domain)}</span></span>
          <span class="jc-meta-item"><i class="bi bi-geo-alt"></i><span>${esc(shortSite)}</span></span>
          
          <span class="jc-sla" style="color:${sla.color};background:${sla.color}1a;border:1px solid ${sla.color}55;font-weight:600;display:inline-flex;align-items:center;gap:4px;"
      title="${job.clockRunning ? 'Awaiting acceptance' : 'Dispatch → Accept'}${sla.next ? ` · ${sla.next.at - sla.hrs}h to ${esc(sla.next.name)}` : ''}">
  <i class="bi ${job.clockRunning ? 'bi-hourglass-split' : 'bi-clock'}"></i>
  ${job.hasClock ? `${sla.hrs}h · ${esc(sla.name)}` : '—'}
</span>
        </div>
      </div>

      <div class="jc-expand${isExpanded ? ' open' : ''}" id="jexp-${ref}">
        <div class="jc-exp-section">
          <div class="exp-label"><i class="bi bi-geo-alt-fill" style="color:var(--gold);"></i>Site address</div>
          <div class="exp-text">${esc(job.site)}</div>
        </div>
        <div class="jc-exp-section">
          <div class="exp-label"><i class="bi bi-card-text" style="color:var(--gold);"></i>Issue description</div>
          <div class="exp-text">${esc(job.description)}</div>
        </div>
        ${reworkBlock}
        ${rescheduleBlock}
        ${historyBlock}
        ${photoBlock}
        ${footer}
      </div>
    </article>`;
}

/**
 * Toggle in place rather than re-rendering the list — a full re-render would
 * discard any ETA the user has typed into a sibling card.
 */
function toggleExpand(ref) {
  const opening = expandedRef !== ref;

  if (expandedRef) {
    $(`jexp-${expandedRef}`)?.classList.remove('open');
    document.querySelector(`#jcard-${CSS.escape(expandedRef)} .jc-chevron`)?.classList.remove('open');
  }
  expandedRef = opening ? ref : null;

  if (opening) {
    $(`jexp-${ref}`)?.classList.add('open');
    document.querySelector(`#jcard-${CSS.escape(ref)} .jc-chevron`)?.classList.add('open');
  }
}

/* ══════════════════════════════════════════════════════
   DELEGATED EVENTS — PIPELINE
══════════════════════════════════════════════════════ */
$('jobList').addEventListener('click', (e) => {
  const resume = e.target.closest('[data-resume]');
  if (resume) { resumeJob(resume.dataset.resume, Number(resume.dataset.srid), resume); return; }

  const rsAgain = e.target.closest('[data-rsagain]');
  if (rsAgain) {
    activeRef  = rsAgain.dataset.rsagain;
    activeSrId = Number(rsAgain.dataset.srid);
    openRescheduleDrawer();
    return;
  }

  const activate = e.target.closest('[data-activate]');
  if (activate) { activateJob(activate.dataset.activate, Number(activate.dataset.srid)); return; }

  const accept = e.target.closest('[data-accept]');
  if (accept) { acceptJob(accept.dataset.accept, Number(accept.dataset.srid), accept); return; }

  const toggle = e.target.closest('[data-toggle]');
  if (toggle) { toggleExpand(toggle.dataset.toggle); }
});

// $('jobList').addEventListener('change', (e) => {
//   const input = e.target.closest('[data-eta]');
//   if (!input) return;

//   const ref  = input.dataset.eta;
//   const date = $(`etaDate-${ref}`)?.value;
//   const time = $(`etaTime-${ref}`)?.value;
//   const btn  = $(`acceptBtn-${ref}`);
//   if (btn) btn.disabled = !(date && time);

//   input.classList.remove('err');
//   $(`errDate-${ref}`)?.classList.remove('show');
//   $(`errTime-${ref}`)?.classList.remove('show');
// });



$('jobList').addEventListener('change', (e) => {
  const input = e.target.closest('[data-eta]');
  if (!input) return;

  const ref = input.dataset.eta;

  input.classList.remove('err');
  $(`errDate-${ref}`)?.classList.remove('show');

  syncEtaTimeMin(ref);
  refreshAcceptBtn(ref);
});


// $('tabFilter').addEventListener('click', (e) => {
//   const btn = e.target.closest('[data-filter]');
//   if (!btn) return;
//   currentFilter = btn.dataset.filter;
//   document.querySelectorAll('.tf-btn').forEach((b) => b.classList.remove('active'));
//   btn.classList.add('active');
//   renderPipeline();
// });


let currentSource = 'own';

$('tabFilter').addEventListener('click', (e) => {
  const btn = e.target.closest('[data-filter]');
  if (!btn) return;
  currentFilter = btn.dataset.filter;
  document.querySelectorAll('#tabFilter .tf-btn').forEach((b) => b.classList.remove('active'));
  btn.classList.add('active');

  const sf = $('sourceFilter');
  sf.style.display = currentFilter === 'Rework' ? 'flex' : 'none';
  if (currentFilter === 'Rework') {
    currentSource = 'own';
    document.querySelectorAll('#sourceFilter .tf-btn').forEach((b, i) => b.classList.toggle('active', i === 0));
  }

  renderPipeline();
});



$('sourceFilter').addEventListener('click', (e) => {
  const btn = e.target.closest('[data-source]');
  if (!btn) return;
  currentSource = btn.dataset.source;
  document.querySelectorAll('#sourceFilter .tf-btn').forEach((b) => b.classList.remove('active'));
  btn.classList.add('active');
  renderPipeline();
});

/* ══════════════════════════════════════════════════════
   ACCEPT / RESUME / ACTIVATE
══════════════════════════════════════════════════════ */
async function acceptJob(ref, srId, btn) {
  const date = $(`etaDate-${ref}`).value;
  const time = $(`etaTime-${ref}`).value;

  if (!date) { $(`errDate-${ref}`).classList.add('show'); $(`etaDate-${ref}`).classList.add('err'); return; }
  if (!time) { $(`errTime-${ref}`).classList.add('show'); $(`etaTime-${ref}`).classList.add('err'); return; }

  const restore = busy(btn, 'Accepting\u2026');

  try {
    await apiPost(ROUTES.accept, { sr_id: srId, eta_date: date, eta_time: time });

    // Reset per-job terminal state so a second job never inherits the first's.
    resetJobState(ref, srId);

    const job = JOBS.find((j) => j.id === ref);
    job.status   = 'Accepted';
    job.accepted = true;
    job.eta      = `${date} ${time}`;
    buildBanner(job, date, time);

    renderPipeline();
    openTerminal();
  } catch (err) {
    restore();
    showToast('error', 'Could not accept job', err.message);
  }
}

async function resumeJob(ref, srId, btn) {
  const restore = busy(btn, 'Resuming\u2026');

  try {
    await apiPost(ROUTES.resume, { sr_id: srId });
    resetJobState(ref, srId);

    const job = JOBS.find((j) => j.id === ref);
    job.status = 'Accepted';

    const [etaDate, etaTime] = String(job.eta ?? '').split(' ');
    buildBanner(job, etaDate || '\u2014', etaTime || '\u2014');

    showToast('success', 'Job resumed', 'Back in the terminal.');
    renderPipeline();
    openTerminal();
  } catch (err) {
    restore();
    showToast('error', 'Could not resume', err.message);
  }
}

function activateJob(ref, srId) {
  if (activeRef === ref) { switchTab('terminal'); return; }

  if (punchInTime) {
    showToast('warning', 'Job in progress', 'Finish or hold the current job before switching.');
    return;
  }

  resetJobState(ref, srId);

  const job = JOBS.find((j) => j.id === ref);
  const [etaDate, etaTime] = String(job.eta ?? '').split(' ');
  buildBanner(job, etaDate || '\u2014', etaTime || '\u2014');

  renderPipeline();
  openTerminal();
  renderExpenses();
}

function resetJobState(ref, srId) {
  activeRef   = ref;
  activeSrId  = srId;
  punchInTime = null;
  uploads     = { before: [], after: [] };
  expenses    = [];
  signatureUploaded = false;
  clearInterval(timerInterval);
}

function buildBanner(job, etaDate, etaTime) {
  // const slaColor  = job.hrsAgo > 24 ? '#fca5a5' : job.hrsAgo > 8 ? '#fcd34d' : '#a7f3d0';
  const sla       = slaStatus(job, 'dispatch');

  const shortSite = String(job.site ?? '').split(',')[0];

  $('jobBanner').innerHTML = `
    <div class="ajb-sr">${esc(job.id)} \u00b7 ${esc(job.domain)}</div>
    <div class="ajb-client">${esc(job.client)}</div>
    <div class="ajb-site"><i class="bi bi-geo-alt-fill"></i>${esc(shortSite)}</div>
    <div class="ajb-meta">
      <span class="ajb-chip"><i class="bi bi-calendar3"></i>ETA ${esc(etaDate)} ${esc(etaTime)}</span>
      <span class="ajb-chip"><i class="bi bi-exclamation-circle"></i>${esc(job.priority)} priority</span>
    <span class="ajb-chip" style="color:${sla.color};"><i class="bi bi-clock"></i>${sla.hrs}h · ${esc(sla.name)}</span>
    </div>`;

  $('termTitle').innerHTML = `<i class="bi bi-broadcast"></i>${esc(job.id)}`;
  $('termSub').textContent  = job.client;
  $('expSrRef').textContent = job.id;
  $('rsSrRef').textContent  = job.id;
  $('finSrRef').textContent = job.id;
  activeEtaAt = parseEta(etaDate, etaTime);
}

/* ══════════════════════════════════════════════════════
   TERMINAL
══════════════════════════════════════════════════════ */
function openTerminal() {
  setTerminalVisible(true);
  switchTab('terminal');

  // Fresh terminal: nothing punched, nothing uploaded.
  $('punchInBtn').disabled  = false;
  $('punchInBtn').innerHTML = '<i class="bi bi-play-fill"></i>Start job';
  $('punchOutBtn').disabled  = true;
  $('punchOutBtn').innerHTML = '<i class="bi bi-check2-square"></i>Finish job';
  $('expenseBtn').disabled = true;
  $('rsBtn').classList.remove('hidden');
  $('lockInfo').classList.add('hidden');

  $('timerDisplay').textContent = '00:00:00';
  $('timerLabel').textContent   = 'Not started';
  $('timerStatus').textContent  = 'Idle';
  $('timerStatus').className    = 'tw-status status-idle';
  $('timerIcon').innerHTML = '<i class="bi bi-clock" style="color:var(--muted);"></i>';

  uploads = { before: [], after: [] };
  ['before', 'after'].forEach((type) => {
    renderPhotoStrip(type);
    document.querySelector(`[data-upload="${type}"]`).disabled = true;
  });

  $('workDesc').value = '';
  $('finSummary').value = '';
  renderExpenses();
  applyEtaGate();
}

/* ══════════════════════════════════════════════════════
   PUNCH IN
══════════════════════════════════════════════════════ */
$('punchInBtn').addEventListener('click', () => {
  const btn = $('punchInBtn');
  btn.disabled = true;
  btn.innerHTML = '<span class="spin"></span> Starting\u2026';
  punchIn();
});






// TIme Previous Checking

function pad(n){ return n < 10 ? '0' + n : '' + n; }

function todayStr(){
  var d = new Date();
  return d.getFullYear() + '-' + pad(d.getMonth()+1) + '-' + pad(d.getDate());
}

function nowTimeStr(buffer){
  var d = new Date();
  d.setMinutes(d.getMinutes() + (buffer || 0));   // e.g. +15 min lead time
  return pad(d.getHours()) + ':' + pad(d.getMinutes());
}

/* Call whenever the date input changes, and once on render */
function syncEtaTimeMin(ref){
  var dateEl = document.getElementById('etaDate-' + ref);
  var timeEl = document.getElementById('etaTime-' + ref);
  if(!timeEl) return;

  var isToday = !dateEl || dateEl.value === todayStr();

  if(isToday){
    timeEl.min = nowTimeStr(0);          // pass 15 to force a 15-min lead
    // clear a now-invalid earlier selection
    if(timeEl.value && timeEl.value < timeEl.min) timeEl.value = '';
  } else {
    timeEl.removeAttribute('min');       // future date → any time allowed
  }

  validateEtaTime(ref);
}

function refreshAcceptBtn(ref){
  var btn    = document.getElementById('acceptBtn-' + ref);
  var dateEl = document.getElementById('etaDate-' + ref);
  if(!btn) return;

  var dateOk = !dateEl || (dateEl.value && dateEl.value >= todayStr());
  btn.disabled = !(dateOk && validateEtaTime(ref));
}


function validateEtaTime(ref){
  var dateEl = document.getElementById('etaDate-' + ref);
  var timeEl = document.getElementById('etaTime-' + ref);
  var errEl  = document.getElementById('errTime-' + ref);
  if(!timeEl || !errEl) return true;

  if(!timeEl.value){
    errEl.textContent = 'Time required';
    errEl.classList.add('show');
    timeEl.classList.add('err');
    return false;
  }

  var isToday = !dateEl || !dateEl.value || dateEl.value === todayStr();
  if(isToday && timeEl.value < nowTimeStr(0)){
    errEl.textContent = 'Time must be later than now';
    errEl.classList.add('show');
    timeEl.classList.add('err');
    return false;
  }

  errEl.classList.remove('show');
  timeEl.classList.remove('err');
  return true;
}

function isEtaValid(ref){
  var dateEl = document.getElementById('etaDate-' + ref);
  var timeEl = document.getElementById('etaTime-' + ref);
  if(!timeEl) return false;

  var dv = dateEl ? dateEl.value : todayStr();
  var tv = timeEl.value;

  if(!dv || !tv) return false;

  var picked = new Date(dv + 'T' + tv);
  if(isNaN(picked.getTime())) return false;

  return picked.getTime() > Date.now();
}




async function punchIn() {
  const btn = $('punchInBtn');

  if (!activeSrId) {
    btn.disabled = false;
    btn.innerHTML = '<i class="bi bi-play-fill"></i>Start job';
    showToast('error', 'No active job', 'Re-activate the job first.');
    return;
  }

  btn.innerHTML = '<span class="spin"></span> Locating\u2026';
  const fix = await captureLocation();
  btn.innerHTML = '<span class="spin"></span> Starting\u2026';

  if (!fix) showToast('warning', 'No GPS fix', 'Starting without location. Enable GPS if possible.');

  try {
    const res = await apiPost(ROUTES.punchIn, withGeo({
      sr_id: activeSrId,
      work_description: $('workDesc').value.trim() || null,
    }, fix));

    punchInTime = new Date(res.punch_in_at);

    clearInterval(timerInterval);
    timerInterval = setInterval(updateTimer, 1000);
    updateTimer();

    $('timerIcon').innerHTML = '<i class="bi bi-stopwatch-fill" style="color:var(--green);"></i>';
    $('timerStatus').textContent = 'Live';
    $('timerStatus').className   = 'tw-status status-live';
    $('timerLabel').textContent  = 'Time on site';

    document.querySelectorAll('[data-upload]').forEach((b) => { b.disabled = false; });
    $('expenseBtn').disabled = false;
    btn.innerHTML = '<i class="bi bi-check2"></i>Job started';

    showToast('success', 'Punched in', 'Job started.');
    applyEtaGate();     // punch is open now — hides the notice, stops the ticker
    refreshLock();
  } catch (err) {
    btn.disabled = false;
    btn.innerHTML = '<i class="bi bi-play-fill"></i>Start job';

    if (/too early/i.test(err.message)) {
      showToast('warning', 'Too early to start', err.message);
      openRescheduleDrawer();
      $('rsRemark').value = 'Attending earlier than the scheduled ETA.';
    } else {
      showToast('error', 'Punch-in failed', err.message);
    }
  }
}

function updateTimer() {
  if (!punchInTime) return;
  const secs = Math.max(0, Math.floor((Date.now() - punchInTime.getTime()) / 1000));
  const pad = (n) => String(n).padStart(2, '0');
  $('timerDisplay').textContent =
    `${pad(Math.floor(secs / 3600))}:${pad(Math.floor((secs % 3600) / 60))}:${pad(secs % 60)}`;
}

/* ══════════════════════════════════════════════════════
   COMPLIANCE UPLOADS
══════════════════════════════════════════════════════ */
document.querySelectorAll('[data-upload]').forEach((btn) => {
  btn.addEventListener('click', () => $(`${btn.dataset.upload}Input`).click());
});

['before', 'after'].forEach((type) => {
  $(`${type}Input`).addEventListener('change', (e) => uploadFile(type, e.target));
});

/** Downscale a camera photo so the tab doesn't run out of memory. */
function compressImage(file, maxEdge = 1600, quality = 0.8) {
  return new Promise((resolve) => {
    if (!file.type.startsWith('image/')) { resolve(file); return; }

    const url = URL.createObjectURL(file);
    const img = new Image();

    img.onload = () => {
      let { width, height } = img;
      const scale = Math.min(1, maxEdge / Math.max(width, height));
      width  = Math.round(width * scale);
      height = Math.round(height * scale);

      const canvas = document.createElement('canvas');
      canvas.width = width;
      canvas.height = height;
      canvas.getContext('2d').drawImage(img, 0, 0, width, height);

      canvas.toBlob((blob) => {
        URL.revokeObjectURL(url);
        if (!blob) { resolve(file); return; }
        resolve(new File([blob], file.name.replace(/\.\w+$/, '') + '.jpg', { type: 'image/jpeg' }));
      }, 'image/jpeg', quality);
    };

    img.onerror = () => { URL.revokeObjectURL(url); resolve(file); };
    img.src = url;
  });
}

async function uploadFile(type, input) {
  if (!input.files.length) return;

  const btn = document.querySelector(`[data-upload="${type}"]`);
  const restore = busy(btn, '');
  const labels = { before: 'Before photos', after: 'After photos' };

  try {
    const form = new FormData();
    form.append('sr_id', activeSrId);
    form.append('type', type);

    // Sequential, not Promise.all — parallel decodes are what crash the tab.
    for (const f of input.files) {
      form.append('files[]', await compressImage(f));
    }

    const res = await apiPost(ROUTES.upload, form, true);
    uploads[type].push(...res.photos);
    restore();
    renderPhotoStrip(type);
    showToast('success', `${labels[type]} uploaded`,
      `${res.photos.length} photo${res.photos.length === 1 ? '' : 's'} added.`);
    refreshLock();
  } catch (err) {
    restore();
    showToast('error', `${labels[type]} failed`, err.message);
  } finally {
    input.value = '';
  }
}

function renderPhotoStrip(type) {
  const strip = $(`${type}Strip`);
  const list  = uploads[type];

  strip.innerHTML = list.map((p) => `
    <div class="ps-item">
      <img src="${esc(p.url)}" alt="${type} photo" data-lb="${esc(p.url)}"/>
      <button class="ps-del" data-del="${Number(p.id)}" data-type="${type}" title="Remove">
        <i class="bi bi-x-lg"></i>
      </button>
    </div>`).join('');

  const done = list.length > 0;
  $(`${type}Status`).textContent = done ? `${list.length} \u2713` : 'Pending';
  $(`${type}Status`).className   = `comp-status ${done ? 'cs-done' : 'cs-pending'}`;
}

['before', 'after'].forEach((type) => {
  $(`${type}Strip`).addEventListener('click', async (e) => {
    const del = e.target.closest('[data-del]');
    if (del) {
      const id = Number(del.dataset.del);
      const t  = del.dataset.type;
      try {
        await apiPost(ROUTES.photoDelete, { sr_id: activeSrId, photo_id: id });
        uploads[t] = uploads[t].filter((p) => p.id !== id);
        renderPhotoStrip(t);
        refreshLock();
      } catch (err) {
        showToast('error', 'Could not remove photo', err.message);
      }
      return;
    }

    const img = e.target.closest('[data-lb]');
    if (img) openLightbox('image', img.dataset.lb, 'Photo');
  });
});

function refreshLock() {
  const allUploaded = uploads.before.length > 0 && uploads.after.length > 0;
  const punched = Boolean(punchInTime);

  $('punchOutBtn').disabled = !(allUploaded && punched);
  $('lockInfo').classList.toggle('hidden', !punched || allUploaded);
}

/* ══════════════════════════════════════════════════════
   PUNCH OUT
══════════════════════════════════════════════════════ */
$('punchOutBtn').addEventListener('click', () => {
  if (!(uploads.before.length && uploads.after.length)) {
    showToast('error', 'Locked', 'Upload all compliance files first.');
    return;
  }
  if (!signatureUploaded) { openSignDrawer(); return; }
  $('finSrRef').textContent = activeRef ?? '\u2014';
  openDrawer('fin');
});

// Cient Input Forms
document.getElementById('clientNameInput').addEventListener('input', function(e){
  e.target.value = e.target.value.replace(/[^a-zA-Z\s]/g, '');   // ← kills 0-9
});



$('finConfirmBtn').addEventListener('click', async () => {
  const btn = $('finConfirmBtn');
  const restore = busy(btn, 'Locating\u2026');

  const fix = await captureLocation();
  btn.innerHTML = '<span class="spin"></span> Processing\u2026';

  const payload = withGeo({
    sr_id:   activeSrId,
    summary: $('finSummary').value.trim() || null,
  }, fix);

  try {
    const res = await apiPost(ROUTES.punchOut, payload);

    closeDrawer('fin');
    clearInterval(timerInterval);

    $('timerStatus').textContent = 'Completed';
    $('timerStatus').className   = 'tw-status status-done';
    $('timerLabel').textContent  = `Total: ${res.duration ?? '\u2014'}`;
    $('punchOutBtn').innerHTML = '<i class="bi bi-check2"></i>Job finished';
    $('punchOutBtn').disabled  = true;

    showToast('success', 'Job finished',
      `Duration ${res.duration ?? '\u2014'} \u00b7 AED ${res.grand_total ?? '0.00'} \u00b7 Sent for review.`);

    // Drop the finished job from the local list, then reset.
    const index = JOBS.findIndex((j) => j.id === activeRef);
    if (index > -1) JOBS.splice(index, 1);

    clearJobState();
    historyLoaded = false;
    profileLoaded = false;

    setTimeout(() => { switchTab('pipeline'); renderPipeline(); }, 1600);
  } catch (err) {
    restore();
    showToast('error', 'Could not finish job', err.message);
  }
});

/** Wipe everything tied to the job that just left the terminal. */
function clearJobState() {
  activeRef = activeSrId = punchInTime = null;
  activeEtaAt = null;
  signatureUploaded = false;
  clearInterval(etaGateTimer);
  clearInterval(timerInterval);
  $('etaNotice').classList.add('hidden');
  uploads  = { before: [], after: [] };
  expenses = [];
  setTerminalVisible(false);
}

/* ══════════════════════════════════════════════════════
   EXPENSES
══════════════════════════════════════════════════════ */
$('expenseBtn').addEventListener('click', () => openDrawer('exp'));

$('expCategory').addEventListener('change', (e) => {
  $('expNameWrap').classList.toggle('hidden', !e.target.value);
  if (e.target.value) $('expName').focus();
});

$('expReceipt').addEventListener('change', (e) => {
  if (!e.target.files.length) return;
  $('receiptName').textContent = e.target.files[0].name;
  $('receiptPreview').classList.add('show');
});

$('expSaveBtn').addEventListener('click', async () => {
  const category = $('expCategory').value;
  const name     = $('expName').value.trim();
  const amount   = $('expAmount').value;

  if (!category) { showToast('warning', 'Missing', 'Select an expense category.'); return; }
  if (!name)     { showToast('warning', 'Missing', 'Enter an item name.'); return; }
  if (!amount || parseFloat(amount) <= 0) { showToast('warning', 'Missing', 'Enter a valid amount.'); return; }

  const btn = $('expSaveBtn');
  const restore = busy(btn, 'Saving\u2026');

  const form = new FormData();
  form.append('sr_id', activeSrId);
  form.append('category', category);
  form.append('name', name);
  form.append('amount', amount);
  if ($('expReceipt').files.length) form.append('receipt', $('expReceipt').files[0]);

  try {
    const res = await apiPost(ROUTES.expense, form, true);

    expenses.push({
        id: res.item_id,                    // ← add
      category, name,
      amount: parseFloat(amount).toFixed(2),
      time: hhmm(new Date()),
      receiptUrl: res.receipt_url ?? null,
    });

    restore();
    resetExpenseForm();
    closeDrawer('exp');
    renderExpenses();
    showToast('success', 'Expense saved', `AED ${parseFloat(amount).toFixed(2)} \u2014 ${name}`);
  } catch (err) {
    restore();
    showToast('error', 'Could not save expense', err.message);
  }
});

function resetExpenseForm() {
  $('expCategory').value = '';
  $('expName').value = '';
  $('expNameWrap').classList.add('hidden');
  $('expAmount').value = '';
  $('expReceipt').value = '';
  $('receiptPreview').classList.remove('show');
}

function renderExpenses() {
  const wrap = $('expenseListWrap');

  if (!expenses.length) {
    wrap.innerHTML = '<div class="tx-empty">No expenses logged yet.</div>';
    return;
  }

  const total = expenses.reduce((sum, e) => sum + parseFloat(e.amount), 0).toFixed(2);

  wrap.innerHTML =
    expenses.map((e) => `
      <div class="tx-item">
        ${e.receiptUrl
          ? `<img src="${esc(e.receiptUrl)}" class="tx-receipt" alt="Receipt" data-receipt="${esc(e.receiptUrl)}"/>`
          : '<i class="bi bi-receipt" style="color:var(--amber);font-size:1.1rem;"></i>'}
        <div class="body">
          <div class="en">${esc(e.name ?? e.category)}</div>
          <div class="ec">${esc(e.category)}</div>
        </div>
        <span class="ea">AED ${esc(e.amount)}</span>
         <span class="er">${esc(e.time)}</span>
        ${e.id ? `<button class="tx-del" data-delexp="${Number(e.id)}" title="Remove">
                    <i class="bi bi-trash"></i>
                  </button>` : ''}
      </div>`).join('') +
    `<div class="tx-total">Total: AED ${total}</div>`;
}

// $('expenseListWrap').addEventListener('click', (e) => {
//   const img = e.target.closest('[data-receipt]');
//   if (img) openLightbox('image', img.dataset.receipt, 'Receipt');
// });

$('expenseListWrap').addEventListener('click', async (e) => {
  const del = e.target.closest('[data-delexp]');
  if (del) {
    const id  = Number(del.dataset.delexp);
    const row = del.closest('.tx-item');
    const name = row?.querySelector('.en')?.textContent ?? 'this entry';
    const amt  = row?.querySelector('.ea')?.textContent ?? '';

    const result = await Swal.fire({
      title: 'Remove this expense?',
      html: `<strong>${name}</strong><br><span style="color:#6b6862;">${amt}</span>`,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Yes, remove it',
      cancelButtonText: 'Cancel',
      confirmButtonColor: '#ef4444',
      cancelButtonColor: '#9c9a92',
      reverseButtons: true,
      focusCancel: true,
    });

    if (!result.isConfirmed) return;

    del.disabled = true;
    try {
      await apiPost(ROUTES.expenseDelete, { sr_id: activeSrId, item_id: id });
      expenses = expenses.filter((x) => Number(x.id) !== id);
      renderExpenses();
      showToast('success', 'Expense removed', 'Entry deleted and totals updated.');
    } catch (err) {
      del.disabled = false;
      showToast('error', 'Could not remove', err.message);
    }
    return;
  }

  const img = e.target.closest('[data-receipt]');
  if (img) openLightbox('image', img.dataset.receipt, 'Receipt');
});

/* ══════════════════════════════════════════════════════
   RESCHEDULE / HOLD
══════════════════════════════════════════════════════ */
function openRescheduleDrawer() {
  $('rsSrRef').textContent = activeRef ?? '\u2014';
  $('rsDate').min = todayLocal();
  $('rsDate').value = '';
  $('rsTime').value = '';
  $('rsRemark').value = '';
  $('holdRemark').value = '';
  switchRsTab('reschedule');
  openDrawer('rs');
}

$('rsBtn').addEventListener('click', openRescheduleDrawer);

document.querySelectorAll('[data-rstab]').forEach((tab) => {
  tab.addEventListener('click', () => switchRsTab(tab.dataset.rstab));
});

function switchRsTab(name) {
  document.querySelectorAll('.rs-tab').forEach((t) => {
    t.classList.toggle('active', t.dataset.rstab === name);
  });
  document.querySelectorAll('.rs-panel').forEach((p) => p.classList.remove('show'));
  $(`rsPanel-${name}`).classList.add('show');
}

// $('rsConfirmBtn').addEventListener('click', async () => {
//   const date   = $('rsDate').value;
//   const time   = $('rsTime').value;
//   const remark = $('rsRemark').value.trim();

//   if (!date || !time) { showToast('warning', 'Required', 'Set a new date and time.'); return; }
//   if (!remark)        { showToast('warning', 'Required', 'Enter a rescheduling reason.'); return; }

//   const btn = $('rsConfirmBtn');
//   const restore = busy(btn, 'Saving55\u2026');

//   try {
//     await apiPost(ROUTES.reschedule, { sr_id: activeSrId, eta_date: date, eta_time: time, remark });
//     closeDrawer('rs');

//     const index = JOBS.findIndex((j) => j.id === activeRef);
//     if (index > -1) {
//       JOBS[index].status           = 'Rescheduled';
//       JOBS[index].previousEta      = JOBS[index].eta;
//       JOBS[index].eta              = `${date} ${time}`;
//       JOBS[index].rescheduleReason = remark;
//       JOBS[index].rescheduleCount  = (JOBS[index].rescheduleCount ?? 0) + 1;
//       JOBS[index].rescheduledAt    = new Date().toLocaleString('en-GB', {
//         day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit',
//       });
//     }

//     clearJobState();
//     showToast('success', 'Job rescheduled', `New ETA: ${date} at ${time}.`);
//     setTimeout(() => { switchTab('pipeline'); renderPipeline(); }, 1200);
//   } catch (err) {
//     restore();
//     showToast('error', 'Could not reschedule', err.message);
//   }
// });




$('rsConfirmBtn').addEventListener('click', async () => {
  const date   = $('rsDate').value;
  const time   = $('rsTime').value;
  const remark = $('rsRemark').value.trim();

  if (!date || !time) { showToast('warning', 'Required', 'Set a new date and time.'); return; }
  if (!remark)        { showToast('warning', 'Required', 'Enter a rescheduling reason.'); return; }

  const btn = $('rsConfirmBtn');
  if (btn.disabled) return;              // guard against double-submit
  const restore = busy(btn, 'Saving…');

  try {
    await apiPost(ROUTES.reschedule, { sr_id: activeSrId, eta_date: date, eta_time: time, remark });
    closeDrawer('rs');

    const index = JOBS.findIndex((j) => j.id === activeRef);
    if (index > -1) {
      JOBS[index].status           = 'Rescheduled';
      JOBS[index].previousEta      = JOBS[index].eta;
      JOBS[index].eta              = `${date} ${time}`;
      JOBS[index].rescheduleReason = remark;
      JOBS[index].rescheduleCount  = (JOBS[index].rescheduleCount ?? 0) + 1;
      JOBS[index].rescheduledAt    = new Date().toLocaleString('en-GB', {
        day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit',
      });
    }

    clearJobState();
    showToast('success', 'Job rescheduled', `New ETA: ${date} at ${time}.`);
    setTimeout(() => { switchTab('pipeline'); renderPipeline(); }, 1200);
  } catch (err) {
    showToast('error', 'Could not reschedule', err.message);
  } finally {
    restore();
  }
});


// $('holdConfirmBtn').addEventListener('click', async () => {
//   const remark = $('holdRemark').value.trim();
//   if (!remark) { showToast('warning', 'Required', 'Enter a reason for the hold.'); return; }

//   const btn = $('holdConfirmBtn');
//   const restore = busy(btn, 'Saving\u2026');

//   try {
//     await apiPost(ROUTES.hold, { sr_id: activeSrId, remark });

//     closeDrawer('rs');
//     showToast('warning', 'Job on hold', 'Placed on hold. Reason logged.');

//     const index = JOBS.findIndex((j) => j.id === activeRef);
//     if (index > -1) {
//       JOBS[index].status = 'On Hold';
//       JOBS[index].accepted = false;
//     }

//     clearJobState();
//     setTimeout(() => { switchTab('pipeline'); renderPipeline(); }, 1200);
//   } catch (err) {
//     restore();
//     showToast('error', 'Could not hold job', err.message);
//   }
// });


$('holdConfirmBtn').addEventListener('click', async () => {
  const remark = $('holdRemark').value.trim();
  if (!remark) { showToast('warning', 'Required', 'Enter a reason for the hold.'); return; }

  const btn = $('holdConfirmBtn');
  if (btn.disabled) return;
  const restore = busy(btn, 'Saving…');

  try {
    await apiPost(ROUTES.hold, { sr_id: activeSrId, remark });

    closeDrawer('rs');   // ← was 'hold', drawer's actual id is 'rs'
    showToast('warning', 'Job on hold', 'Placed on hold. Reason logged.');

    const index = JOBS.findIndex((j) => j.id === activeRef);
    if (index > -1) {
      JOBS[index].status = 'On Hold';
      JOBS[index].accepted = false;
    }

    clearJobState();
    setTimeout(() => { switchTab('pipeline'); renderPipeline(); }, 1200);
  } catch (err) {
    showToast('error', 'Could not hold job', err.message);
  } finally {
    restore();
  }
});


/* ══════════════════════════════════════════════════════
   CLIENT ACCEPTANCE + SIGNATURE
══════════════════════════════════════════════════════ */
let sigCtx = null, sigDrawing = false, sigHasInk = false;

function openSignDrawer() {
  $('signSrRef').textContent = activeRef ?? '\u2014';
  $('policyCheck').checked = false;
  $('clientNameInput').value = '';
  $('signBlock').classList.add('hidden');
  $('signSubmitBtn').disabled = true;
  openDrawer('sign');
  // Canvas must be sized after the drawer is visible (needs layout width).
  requestAnimationFrame(initSigCanvas);
}

function initSigCanvas() {
  const canvas = $('sigCanvas');
  const ratio = window.devicePixelRatio || 1;
  const rect = canvas.getBoundingClientRect();
  canvas.width  = rect.width * ratio;
  canvas.height = rect.height * ratio;
  sigCtx = canvas.getContext('2d');
  sigCtx.scale(ratio, ratio);
  sigCtx.lineWidth = 2;
  sigCtx.lineCap = 'round';
  sigCtx.lineJoin = 'round';
  sigCtx.strokeStyle = '#1a1614';
  sigHasInk = false;
}

function sigPos(e) {
  const rect = $('sigCanvas').getBoundingClientRect();
  const t = e.touches ? e.touches[0] : e;
  return { x: t.clientX - rect.left, y: t.clientY - rect.top };
}

function sigStart(e) { e.preventDefault(); sigDrawing = true; const p = sigPos(e); sigCtx.beginPath(); sigCtx.moveTo(p.x, p.y); }
function sigMove(e)  { if (!sigDrawing) return; e.preventDefault(); const p = sigPos(e); sigCtx.lineTo(p.x, p.y); sigCtx.stroke(); sigHasInk = true; refreshSignSubmit(); }
function sigEnd()    { sigDrawing = false; }

(function bindSigCanvas() {
  const canvas = $('sigCanvas');
  canvas.addEventListener('mousedown', sigStart);
  canvas.addEventListener('mousemove', sigMove);
  window.addEventListener('mouseup', sigEnd);
  canvas.addEventListener('touchstart', sigStart, { passive: false });
  canvas.addEventListener('touchmove', sigMove, { passive: false });
  canvas.addEventListener('touchend', sigEnd);
})();

$('sigClearBtn').addEventListener('click', () => {
  const canvas = $('sigCanvas');
  sigCtx.clearRect(0, 0, canvas.width, canvas.height);
  sigHasInk = false;
  refreshSignSubmit();
});

$('policyCheck').addEventListener('change', (e) => {
  $('signBlock').classList.toggle('hidden', !e.target.checked);
  if (e.target.checked) requestAnimationFrame(initSigCanvas);
  refreshSignSubmit();
});

$('clientNameInput').addEventListener('input', refreshSignSubmit);

function refreshSignSubmit() {
  $('signSubmitBtn').disabled = !(
    $('policyCheck').checked &&
    $('clientNameInput').value.trim().length > 1 &&
    sigHasInk
  );
}

$('signSubmitBtn').addEventListener('click', async () => {
  const clientName = $('clientNameInput').value.trim();
  if (!$('policyCheck').checked || !sigHasInk || !clientName) {
    showToast('warning', 'Incomplete', 'Accept the policy and sign first.');
    return;
  }

  const btn = $('signSubmitBtn');
  const restore = busy(btn, 'Locating\u2026');

  const fix = await captureLocation();
  btn.innerHTML = '<span class="spin"></span> Generating\u2026';

  try {
    const pdfBlob = new Blob([buildAcceptancePdf(clientName, fix)], { type: 'application/pdf' });

    const form = new FormData();
    form.append('sr_id', activeSrId);
    form.append('client_name', clientName);
    form.append('signature', pdfBlob, `acceptance-${activeRef}.pdf`);
    withGeo(form, fix);

    await apiPost(ROUTES.signature, form, true);

    signatureUploaded = true;
    restore();
    closeDrawer('sign');
    showToast('success', 'Acceptance recorded', 'Signature saved. You can finish the job.');

    $('finSrRef').textContent = activeRef ?? '\u2014';
    openDrawer('fin');
  } catch (err) {
    restore();
    showToast('error', 'Could not save signature', err.message);
  }
});

/** Compose the terms + signature into a one-page PDF. Returns a Blob. */
function buildAcceptancePdf(clientName, fix) {
  const { jsPDF } = window.jspdf;
  const doc = new jsPDF({ unit: 'mm', format: 'a4' });
  const pageW = doc.internal.pageSize.getWidth();
  const pageH = doc.internal.pageSize.getHeight();
  const margin = 18;

  // Match the source image aspect ratios so nothing stretches.
  const HEADER_H = pageW * (280 / 1108);
  const FOOTER_H = pageW * (150 / 1600);

  function paintChrome() {
    try {
      if (LETTERHEAD && LETTERHEAD.watermark) {
        const wmW = 110, wmH = 110;
        if (doc.setGState) doc.setGState(new doc.GState({ opacity: 0.08 }));
        doc.addImage(LETTERHEAD.watermark, 'PNG',
          (pageW - wmW) / 2, pageH - FOOTER_H - wmH - 6, wmW, wmH);
        if (doc.setGState) doc.setGState(new doc.GState({ opacity: 1 }));
      }
    } catch (e) { console.warn('watermark skipped:', e); }

    try {
      if (LETTERHEAD && LETTERHEAD.header) {
        doc.addImage(LETTERHEAD.header, 'PNG', 0, 0, pageW, HEADER_H);
      }
    } catch (e) { console.warn('header skipped:', e); }

    try {
      if (LETTERHEAD && LETTERHEAD.footer) {
        doc.addImage(LETTERHEAD.footer, 'PNG', 0, pageH - FOOTER_H, pageW, FOOTER_H);
      }
    } catch (e) { console.warn('footer skipped:', e); }
  }

  paintChrome();
  let y = HEADER_H + 10;

  doc.setFont('helvetica', 'bold');
  doc.setFontSize(15);
  doc.setTextColor(30);
  doc.text('Service Completion Acceptance', margin, y);
  y += 8;

  doc.setFont('helvetica', 'normal');
  doc.setFontSize(9);
  doc.setTextColor(90);
  doc.text(`Job Reference: ${activeRef ?? '-'}`, margin, y); y += 5;
  doc.text(`Date: ${new Date().toLocaleString('en-GB')}`, margin, y); y += 8;

  doc.setTextColor(30);
  doc.setFontSize(10);
  // Pull the terms straight from the DOM so the PDF matches what was shown.
  const termsText = $('termsScroll').innerText.replace(/\n{2,}/g, '\n\n').trim();
  const lines = doc.splitTextToSize(termsText, pageW - margin * 2);
  doc.text(lines, margin, y);
  y += lines.length * 4.6 + 6;

  doc.setDrawColor(200);
  doc.line(margin, y, pageW - margin, y);
  y += 8;

  doc.setFont('helvetica', 'bold');
  doc.setFontSize(10);
  doc.text('Accepted and signed by:', margin, y);
  y += 6;
  doc.setFont('helvetica', 'normal');
  doc.text(clientName, margin, y);
  y += 6;

  const sigData = $('sigCanvas').toDataURL('image/png');
  doc.setFontSize(9);
  doc.setTextColor(90);
  doc.text('Signature:', margin, y);
  y += 2;
  const sigW = 70, sigH = 30;
  doc.addImage(sigData, 'PNG', margin, y, sigW, sigH);
  y += sigH + 2;
  doc.setDrawColor(150);
  doc.line(margin, y, margin + sigW, y);
  y += 6;

  if (fix) {
    doc.setFontSize(8);
    doc.setTextColor(120);
    doc.text(`Signed at: ${fix.lat.toFixed(6)}, ${fix.lng.toFixed(6)}`
      + (fix.accuracy != null ? ` (\u00b1${Math.round(fix.accuracy)}m)` : ''), margin, y);
    y += 4;
    if (fix.address) doc.text(doc.splitTextToSize(fix.address, pageW - margin * 2), margin, y);
  }

  return doc.output('blob');
}

/* ══════════════════════════════════════════════════════
   HISTORY
══════════════════════════════════════════════════════ */
async function loadHistory(force = false) {
  if (historyLoaded && !force) return;

  try {
    const res = await apiGet(ROUTES.history);
    historyLoaded = true;

    $('statJobs').textContent  = res.stats.jobs;
    $('statHours').textContent = res.stats.hours;
    $('statExp').textContent   = res.stats.expenses;

    $('histCount').textContent = `${res.items.length} job${res.items.length === 1 ? '' : 's'}`;
    $('histSub').textContent   = `${res.stats.hours}h logged \u00b7 last 50 jobs`;

    const list = $('histList');
    if (!res.items.length) {
      list.innerHTML =
        '<div class="card"><div class="empty-state"><i class="bi bi-inbox"></i>' +
        '<h6>Nothing yet</h6><p>Finished jobs will appear here.</p></div></div>';
      return;
    }

    list.innerHTML = res.items.map((h) => `
      <article class="hist-card">
        <div class="hist-top">
          <span class="hist-ref">${esc(h.ref)}</span>
          <div style="display:flex;gap:6px;align-items:center;">
            <span class="pill pill-${esc(h.status)}">${esc(h.status)}</span>
            <span class="hist-date">${esc(h.date)}</span>
          </div>
        </div>
        <div class="hist-client">${esc(h.client)}</div>
        <div class="hist-site"><i class="bi bi-geo-alt"></i> ${esc(String(h.site).split(',')[0])}</div>
        <div class="hist-grid">
          <span class="hist-chip"><i class="bi bi-stopwatch"></i>${esc(h.duration)}</span>
          <span class="hist-chip"><i class="bi bi-clock"></i>${esc(h.in)}\u2013${esc(h.out)}</span>
          <span class="hist-chip"><i class="bi bi-receipt"></i>${Number(h.itemCount)}</span>
          <span class="hist-total">AED ${esc(h.total)}</span>
        </div>
      </article>`).join('');
  } catch (err) {
    $('histList').innerHTML =
      '<div class="card"><div class="empty-state"><i class="bi bi-wifi-off"></i>' +
      `<h6>Could not load</h6><p>${esc(err.message)}</p></div></div>`;
  }
}

/* ══════════════════════════════════════════════════════
   PROFILE
══════════════════════════════════════════════════════ */
async function loadProfile(force = false) {
  if (profileLoaded && !force) return;

  try {
    const res = await apiGet(ROUTES.profile);
    profileLoaded = true;
    const u = res.user, s = res.stats;

    $('profBody').innerHTML = `
      <div class="prof-hero">
        <div class="prof-avatar">${esc(u.initials)}</div>
        <div class="prof-name cg">${esc(u.name)}</div>
        <div class="prof-role">${esc(u.role)}${u.code ? ' \u00b7 ' + esc(u.code) : ''}</div>
      </div>

      <div class="mini-row">
        <div class="mini-box"><div class="mini-val">${Number(s.open)}</div><div class="mini-lbl">Open</div></div>
        <div class="mini-box"><div class="mini-val">${Number(s.completed)}</div><div class="mini-lbl">Done</div></div>
        <div class="mini-box"><div class="mini-val">${Number(s.hours)}</div><div class="mini-lbl">Hours</div></div>
      </div>

      <div class="compliance-card">
        <div class="comp-title"><i class="bi bi-person-vcard"></i>Account details</div>
        <div class="info-row">
          <div class="info-ico"><i class="bi bi-envelope-fill"></i></div>
          <div><div class="info-lbl">Email</div><div class="info-val">${esc(u.email)}</div></div>
        </div>
        <div class="info-row">
          <div class="info-ico"><i class="bi bi-telephone-fill"></i></div>
          <div><div class="info-lbl">Phone</div><div class="info-val">${esc(u.phone)}</div></div>
        </div>
        <div class="info-row">
          <div class="info-ico"><i class="bi bi-calendar-event-fill"></i></div>
          <div><div class="info-lbl">Member since</div><div class="info-val">${esc(u.joined)}</div></div>
        </div>
      </div>

      <button class="btn-outline brand" id="profPwdBtn"><i class="bi bi-key-fill"></i>Change password</button>
      <button class="btn-outline danger" id="profLogoutBtn"><i class="bi bi-box-arrow-right"></i>Sign out</button>`;

    $('profPwdBtn').addEventListener('click', openPwdModal);
    $('profLogoutBtn').addEventListener('click', () => $('logoutBtn').click());
  } catch (err) {
    profileLoaded = false;
    $('profBody').innerHTML =
      '<div class="card"><div class="empty-state"><i class="bi bi-wifi-off"></i>' +
      `<h6>Could not load</h6><p>${esc(err.message)}</p></div></div>`;
  }
}

$('profRefresh').addEventListener('click', () => loadProfile(true));

/* ---------- Change password ---------- */
function openPwdModal() {
  if ($('pwdModal')) return;

  const m = document.createElement('div');
  m.className = 'modal-backdrop';
  m.id = 'pwdModal';
  m.innerHTML = `
    <div class="modal-card" role="dialog" aria-modal="true" aria-label="Change password">
      <div class="comp-title"><i class="bi bi-key-fill"></i>Change password</div>
      <div id="pwdMsg" class="pwd-msg" style="display:none"></div>
      <input type="password" id="pwdCur"  class="inp" placeholder="Current password"     autocomplete="current-password">
      <input type="password" id="pwdNew"  class="inp" placeholder="New password"         autocomplete="new-password">
      <input type="password" id="pwdConf" class="inp" placeholder="Confirm new password" autocomplete="new-password">
      <div class="modal-actions">
        <button class="btn-outline" id="pwdCancel" type="button">Cancel</button>
        <button class="btn-outline brand" id="pwdSave" type="button">Update</button>
      </div>
    </div>`;

  document.body.appendChild(m);
  document.body.style.overflow = 'hidden';
  $('pwdCur').focus();

  const msg = $('pwdMsg');

  function close() {
    document.removeEventListener('keydown', onKey);
    document.body.style.overflow = '';
    m.remove();
  }

  function onKey(e) {
    if (e.key === 'Escape') { e.stopPropagation(); close(); }
    if (e.key === 'Enter' && m.contains(document.activeElement)) submit();
  }

  function show(text, ok = false) {
    msg.textContent = text;
    msg.className = ok ? 'pwd-msg pwd-ok' : 'pwd-msg pwd-err';
    msg.style.display = 'block';
  }

  async function submit() {
    const btn = $('pwdSave');
    if (btn.disabled) return;

    const cur  = $('pwdCur').value;
    const nw   = $('pwdNew').value;
    const conf = $('pwdConf').value;

    msg.style.display = 'none';

    if (!cur || !nw || !conf) return show('All fields are required.');
    if (nw.length < 8)        return show('New password must be at least 8 characters.');
    if (nw !== conf)          return show('New passwords do not match.');
    if (nw === cur)           return show('New password must differ from the current one.');

    btn.disabled = true;
    btn.textContent = 'Saving\u2026';

    try {
      const r = await apiPost(ROUTES.changePassword, {
        current_password: cur, password: nw, password_confirmation: conf,
      });
      show(r.message || 'Password updated.', true);
      setTimeout(close, 1200);
    } catch (e) {
      show(e.message || 'Could not update password.');
      btn.disabled = false;
      btn.textContent = 'Update';
    }
  }

  document.addEventListener('keydown', onKey);
  m.addEventListener('click', (e) => { if (e.target === m) close(); });
  $('pwdCancel').addEventListener('click', close);
  $('pwdSave').addEventListener('click', submit);
}

/* ══════════════════════════════════════════════════════
   ETA GATE
══════════════════════════════════════════════════════ */
function parseEta(dateStr, timeStr) {
  if (!dateStr || !timeStr || dateStr === '\u2014' || timeStr === '\u2014') return null;
  const d = new Date(`${dateStr}T${String(timeStr).slice(0, 5)}:00`);
  return isNaN(d.getTime()) ? null : d;
}

function applyEtaGate() {
  clearInterval(etaGateTimer);
  const notice = $('etaNotice');
  const btn    = $('punchInBtn');

  if (punchInTime || !activeEtaAt) { notice.classList.add('hidden'); return; }

  const tick = () => {
    const waitMs = activeEtaAt.getTime() - Date.now() - EARLY_START_GRACE_MS;

    if (waitMs <= 0) {
      notice.classList.add('hidden');
      btn.disabled = false;
      clearInterval(etaGateTimer);
      return;
    }

    const mins  = Math.ceil(waitMs / 60000);
    const label = mins >= 60 ? `${Math.floor(mins / 60)}h ${mins % 60}m` : `${mins}m`;

    $('etaNoticeText').innerHTML =
      `Scheduled for <strong>${esc(activeEtaAt.toLocaleString('en-GB', {
        day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit',
      }))}</strong> — can start in ${esc(label)}. ` +
      `Tap <strong>Reschedule / hold job</strong> if you need to start now.`;

    notice.classList.remove('hidden');
    btn.disabled = true;
  };

  tick();
  etaGateTimer = setInterval(tick, 30000);
}






/* ══════════════════════════════════════════════════════
   SLA BANDS — thresholds come from the matrix, not code
══════════════════════════════════════════════════════ */
function slaBands(stage) {
  const key = stage || 'approve';                 // 'approve' | 'dispatch' | 'qc'
  return (SLA_MATRIX || [])
    .filter((r) => Number(r[key]) > 0)
    .map((r) => ({ name: r.name, color: r.color, at: Number(r[key]) }))
    .sort((a, b) => a.at - b.at);
}

function slaStatus(item, stage) {
  const bands = slaBands(stage);
  const hrs   = Number(item.hrsAgo) || 0;

  if (!bands.length) return { color: '#8a8a8a', name: '—', hrs, at: null, next: null, pct: 0 };

  let hit = null;
  for (const b of bands) { if (hrs >= b.at) hit = b; else break; }
  const next = bands.find((b) => b.at > hrs) || null;

  if (!hit) {
    return { color: '#8a8a8a', name: '', hrs, at: null, next,
             pct: next ? Math.round((hrs / next.at) * 100) : 0 };
  }

  const span = next ? (next.at - hit.at) : 1;
  return { color: hit.color, name: hit.name, hrs, at: hit.at, next,
           pct: next ? Math.round(((hrs - hit.at) / span) * 100) : 100 };
}


/* ══════════════════════════════════════════════════════
   RESTORE AN OPEN PUNCH
══════════════════════════════════════════════════════ */
function restoreTerminal(state) {
  const job = state.job;

  activeRef  = job.id;
  activeSrId = Number(job.sr_id);
  uploads = {
    before: Array.isArray(state.uploads?.before) ? state.uploads.before : [],
    after:  Array.isArray(state.uploads?.after)  ? state.uploads.after  : [],
  };
  expenses = (state.expenses ?? []).map((e) => ({ ...e }));

  buildBanner(job, state.etaDate ?? '\u2014', state.etaTime ?? '\u2014');
  setTerminalVisible(true);
  switchTab('terminal');

  $('workDesc').value = state.workDesc ?? '';
  $('rsBtn').classList.remove('hidden');

  // Punch already open: the timer is running, uploads are unlocked.
  if (state.punchInAt) {
    punchInTime = new Date(state.punchInAt);

    clearInterval(timerInterval);
    timerInterval = setInterval(updateTimer, 1000);
    updateTimer();

    $('timerIcon').innerHTML = '<i class="bi bi-stopwatch-fill" style="color:var(--green);"></i>';
    $('timerStatus').textContent = 'Live';
    $('timerStatus').className   = 'tw-status status-live';
    $('timerLabel').textContent  = 'Time on site';

    $('punchInBtn').disabled  = true;
    $('punchInBtn').innerHTML = '<i class="bi bi-check2"></i>Job started';

    if (state.punchInLocation) renderLocationBadge(state.punchInLocation);

    document.querySelectorAll('[data-upload]').forEach((b) => { b.disabled = false; });
    $('expenseBtn').disabled = false;
  }

  ['before', 'after'].forEach(renderPhotoStrip);
  renderExpenses();
  refreshLock();
  applyEtaGate();
}

/* ══════════════════════════════════════════════════════
   DEEP LINKS FROM THE DASHBOARD
   ?filter=<status>  ?job=<ref>  #terminal|#history|#profile
══════════════════════════════════════════════════════ */

/** Select a status tab by name. Returns false if that tab doesn't exist. */
function applyFilter(name) {
  if (!name) return false;

  const buttons = Array.from(document.querySelectorAll('.tf-btn'));
  const target  = buttons.find((b) => b.dataset.filter.toLowerCase() === String(name).toLowerCase());
  if (!target) return false;

  currentFilter = target.dataset.filter;
  buttons.forEach((b) => b.classList.remove('active'));
  target.classList.add('active');
  return true;
}

/** Open one SR's card: right filter, expanded, scrolled into view. */
function focusJob(ref) {
  if (!ref) return;

  const job = JOBS.find((j) => j.id === ref);
  if (!job) {
    showToast('warning', 'Not in your pipeline', `${ref} is either closed or assigned to someone else.`);
    return;
  }

  if (job.status !== currentFilter) applyFilter(job.status);
  renderPipeline();
  if (expandedRef !== ref) toggleExpand(ref);

  const card = $(`jcard-${ref}`);
  if (card) requestAnimationFrame(() => card.scrollIntoView({ block: 'center', behavior: 'smooth' }));
}

/* Lazy-load the two tabs that fetch. */
onTabShow('history', () => loadHistory());
onTabShow('profile', () => loadProfile());

/* ══════════════════════════════════════════════════════
   INIT
══════════════════════════════════════════════════════ */
document.addEventListener('DOMContentLoaded', () => {
  try {
    const params = new URLSearchParams(location.search);
    const wanted = {
      filter: params.get('filter'),
      job:    params.get('job'),
      page:   (location.hash || '').replace('#', '').toLowerCase(),
    };

    applyFilter(wanted.filter);
    renderPipeline();

    // Rehydrate an open punch first: it sets activeRef, uploads, expenses and
    // the banner, and lands on the terminal.
    if (ACTIVE) restoreTerminal(ACTIVE);
    else setTerminalVisible(false);

    // An explicit deep link wins over that default landing.
    if (['history', 'profile'].includes(wanted.page)) {
      switchTab(wanted.page);
    } else if (wanted.page === 'terminal' && activeSrId) {
      switchTab('terminal');
    } else if (wanted.job) {
      switchTab('pipeline');
      focusJob(wanted.job);
    } else if (!ACTIVE) {
      switchTab('pipeline');
    }
  } catch (err) {
    console.error('Init failed:', err);
    showToast('error', 'Load error', err.message);
  }
});
</script>
@endpush