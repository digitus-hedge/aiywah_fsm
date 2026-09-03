<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0"/>
<title>Maintenance Lead · Previous Day Summary · Matter Mind SR Portal</title>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<link rel="icon" type="image/png" href="{{ asset('assets/images/favicon.png') }}">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
html{overflow-x:hidden;}
body{font-family:'DM Sans',system-ui,sans-serif;background:#f2f4f2;color:#1a1614;
  -webkit-font-smoothing:antialiased;min-height:100vh;overflow-x:hidden;
  font-size:clamp(14px,.9vw + 11px,16px);}

:root{
  --mm-gold:#9A7B4F;
  --mm-gold-light:#C4A882;
  --mm-gold-dark:#7A6140;
  --mm-ink:#1a1614;
}

.hdr{background:linear-gradient(135deg,var(--mm-gold) 0%,var(--mm-gold-dark) 100%);
  padding:clamp(18px,4vw,28px) clamp(16px,4vw,28px) clamp(20px,4vw,30px);position:relative;overflow:hidden;}
.hdr::before{content:'';position:absolute;right:-60px;top:-60px;width:220px;height:220px;
  border-radius:50%;background:rgba(255,255,255,.08);}
.hdr::after{content:'';position:absolute;left:-40px;bottom:-50px;width:180px;height:180px;
  border-radius:50%;background:rgba(255,255,255,.05);}
.hdr-inner{position:relative;z-index:1;display:flex;align-items:flex-start;
  justify-content:space-between;gap:12px;flex-wrap:wrap;}
.hdr-left{min-width:0;flex:1 1 220px;}
.hdr-eyebrow{font-size:.62rem;font-weight:800;text-transform:uppercase;
  letter-spacing:.13em;color:rgba(255,255,255,.65);margin-bottom:5px;}
.hdr-title{font-size:clamp(1.05rem,3.6vw,1.5rem);font-weight:900;color:#fff;line-height:1.2;margin-bottom:6px;}
.hdr-date{font-size:.78rem;color:rgba(255,255,255,.85);font-weight:500;}
.hdr-right{display:flex;flex-direction:column;align-items:flex-end;gap:6px;flex-shrink:0;}
.role-pill{font-size:.68rem;font-weight:800;padding:4px 12px;border-radius:20px;
  background:rgba(255,255,255,.18);color:#fff;border:1px solid rgba(255,255,255,.32);white-space:nowrap;}
.hdr-time{font-size:.64rem;color:rgba(255,255,255,.7);white-space:nowrap;}

.quick-bar{border-top:1px solid rgba(255,255,255,.15);
  padding:10px clamp(16px,4vw,28px);display:flex;gap:8px;flex-wrap:wrap;position:relative;z-index:1;}
.qb-pill{font-size:.67rem;font-weight:700;padding:4px 10px;border-radius:20px;
  display:flex;align-items:center;gap:4px;white-space:nowrap;}
.qp-green{background:#edf8f2;color:#1a6b47;}
.qp-gold {background:#fdf5e8;color:var(--mm-gold-dark);}
.qp-red  {background:#fff0f0; color:#8a2020;}
.qp-amb  {background:#fff8e8; color:#8a6020;}
.qp-blue {background:#eff6ff; color:#1d4ed8;}
.qp-purp {background:#f5f3ff; color:#6d28d9;}

.body{padding:14px;max-width:1240px;margin:0 auto;}
@media(min-width:600px){.body{padding:18px;}}
@media(min-width:900px){.body{padding:22px 18px;}}
@media(min-width:1400px){.body{max-width:1800px;padding:28px 22px;}}

.kpi-strip{display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:12px;}
@media(min-width:340px) and (max-width:499px){.kpi-strip{grid-template-columns:1fr 1fr;}}
@media(min-width:500px){.kpi-strip{grid-template-columns:repeat(3,1fr);}}
@media(min-width:800px){.kpi-strip{grid-template-columns:repeat(6,1fr);}}

.kpi{background:#fff;border-radius:12px;padding:14px 14px 12px;
  box-shadow:0 1px 6px rgba(0,0,0,.07);border:1px solid #e8e8e4;
  cursor:pointer;transition:all .18s;position:relative;overflow:hidden;}
.kpi:hover{box-shadow:0 4px 16px rgba(0,0,0,.1);transform:translateY(-2px);}
.kpi::before{content:'';position:absolute;top:0;left:0;right:0;height:3px;border-radius:12px 12px 0 0;}
.kpi.k-gold::before{background:linear-gradient(90deg,var(--mm-gold),var(--mm-gold-light));}
.kpi.k-green::before{background:linear-gradient(90deg,#2d6b4a,#4a8a6a);}
.kpi.k-blue::before{background:linear-gradient(90deg,#1d4ed8,#3b82f6);}
.kpi.k-red::before{background:linear-gradient(90deg,#b91c1c,#ef4444);}
.kpi.k-purp::before{background:linear-gradient(90deg,#6d28d9,#8b5cf6);}
.kpi.k-amb::before{background:linear-gradient(90deg,#b45309,#f59e0b);}
.kpi-ico{width:30px;height:30px;border-radius:8px;display:flex;align-items:center;
  justify-content:center;font-size:.85rem;margin-bottom:8px;}
.ki-gold{background:#fdf8f0;} .ki-green{background:#edf8f2;}
.ki-blue{background:#eff6ff;} .ki-red{background:#fff0f0;} .ki-purp{background:#f5f3ff;} .ki-amb{background:#fff8e8;}
.kpi-val{font-size:clamp(1.5rem,4.5vw,1.9rem);font-weight:900;line-height:1;margin-bottom:2px;}
.kv-gold{color:var(--mm-gold);} .kv-green{color:#2d6b4a;}
.kv-blue{color:#1d4ed8;} .kv-red{color:#b91c1c;} .kv-purp{color:#6d28d9;} .kv-amb{color:#b45309;}
.kpi-lbl{font-size:.66rem;font-weight:700;color:#8a9890;line-height:1.3;}
.kpi-sub{font-size:.6rem;color:#b0bab8;margin-top:2px;}
.kpi-more{font-size:.6rem;font-weight:700;color:var(--mm-gold);margin-top:6px;
  display:flex;align-items:center;gap:3px;opacity:.75;}

.grid-2{display:grid;grid-template-columns:1fr;gap:12px;margin-bottom:12px;}
@media(min-width:640px){.grid-2{grid-template-columns:1fr 1fr;}}

.card{background:#fff;border-radius:14px;padding:clamp(14px,3vw,18px);
  box-shadow:0 1px 8px rgba(0,0,0,.06);border:1px solid #e8e8e4;
  cursor:pointer;transition:all .18s;min-width:0;}
.card:hover{box-shadow:0 6px 22px rgba(0,0,0,.1);transform:translateY(-1px);}
.card-top{display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:14px;gap:8px;}
.card-title{font-size:.72rem;font-weight:800;color:#6a7a70;text-transform:uppercase;letter-spacing:.08em;}
.card-more-btn{font-size:.65rem;font-weight:700;color:var(--mm-gold);display:flex;align-items:center;gap:3px;opacity:.85;white-space:nowrap;}
.card-big{font-size:clamp(1.9rem,5.5vw,2.6rem);font-weight:900;line-height:1;}

.ch{position:relative;}
.ch canvas{position:absolute;inset:0;width:100%!important;height:100%!important;}
.ch-140{height:140px;}

.item-list{display:flex;flex-direction:column;gap:6px;margin-top:6px;}
.list-item{display:flex;align-items:flex-start;gap:7px;padding:7px 9px;
  background:#fafaf8;border-radius:8px;border:1px solid #eee;}
.li-dot{width:6px;height:6px;border-radius:50%;flex-shrink:0;margin-top:5px;}
.li-body{flex:1;min-width:0;}
.li-sr{font-size:.7rem;font-weight:700;color:#1a1614;word-break:break-word;}
.li-reason{font-size:.63rem;color:#8a9890;line-height:1.4;}

.exp-row{display:flex;align-items:baseline;gap:8px;margin:6px 0;}
.exp-big{font-size:1.8rem;font-weight:900;color:#6d28d9;}
.exp-sub{font-size:.72rem;color:#8a9890;font-weight:600;}
.exp-track{height:8px;background:#f0eefa;border-radius:4px;overflow:hidden;margin:8px 0;}
.exp-fill{height:100%;border-radius:4px;background:linear-gradient(90deg,#6d28d9,#8b5cf6);}

.rating-stars{font-size:1.6rem;color:var(--mm-gold);letter-spacing:2px;}

.sheet-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);
  z-index:800;backdrop-filter:blur(3px);}
.sheet-overlay.open{display:block;}
.bottom-sheet{position:fixed;left:0;right:0;bottom:-100%;background:#fff;
  z-index:900;border-radius:20px 20px 0 0;
  max-height:80vh;display:flex;flex-direction:column;
  box-shadow:0 -4px 30px rgba(0,0,0,.15);transition:bottom .35s cubic-bezier(.4,0,.2,1);}
.bottom-sheet.open{bottom:0;}
.bs-handle{width:40px;height:4px;background:#e0e0dc;border-radius:2px;margin:10px auto 0;}
.bs-hdr{padding:14px 20px 12px;border-bottom:1px solid #f0f0ec;
  display:flex;align-items:center;justify-content:space-between;flex-shrink:0;gap:10px;}
.bs-title{font-size:.9rem;font-weight:800;color:#1a1614;}
.bs-close{width:28px;height:28px;border-radius:7px;background:#f5f5f0;flex-shrink:0;
  border:none;cursor:pointer;font-size:.8rem;display:flex;align-items:center;justify-content:center;}
.bs-body{flex:1;overflow-y:auto;overflow-x:auto;padding:14px 20px 24px;}
.bs-body::-webkit-scrollbar{width:3px;}
.bs-body::-webkit-scrollbar-thumb{background:#e0e0dc;border-radius:2px;}

.detail-table{width:100%;min-width:480px;border-collapse:collapse;font-size:.78rem;}
.detail-table th{text-align:left;padding:7px 10px;font-size:.65rem;font-weight:800;
  text-transform:uppercase;letter-spacing:.07em;color:#9a9890;
  border-bottom:1px solid #f0f0ec;white-space:nowrap;}
.detail-table td{padding:8px 10px;border-bottom:1px solid #f8f8f5;color:#1a1614;vertical-align:middle;}
.detail-table tr:last-child td{border-bottom:none;}
.detail-table tr:hover td{background:#fafaf8;}

.cta-wrap{padding:14px 14px 28px;max-width:1240px;margin:0 auto;}
@media(min-width:1400px){.cta-wrap{max-width:1320px;}}
.cta-btn{display:flex;align-items:center;justify-content:center;gap:8px;
  background:linear-gradient(135deg,var(--mm-gold),var(--mm-gold-light));color:#fff;
  padding:14px 24px;border-radius:12px;font-size:.88rem;font-weight:800;
  text-decoration:none;border:none;cursor:pointer;width:100%;max-width:520px;margin:0 auto;
  box-shadow:0 4px 16px rgba(154,123,79,.3);transition:opacity .15s;}
.cta-btn:hover{opacity:.88;}

.footer{text-align:center;padding:10px 16px 20px;font-size:.62rem;color:#9a9890;}

@media(max-width:359px){
  .kpi-strip{grid-template-columns:1fr 1fr;}
  .hdr{padding:16px 14px 20px;}
  .body{padding:10px;}
}
</style>
</head>
<body>

<div class="hdr">
  <div class="hdr-inner">
    <div class="hdr-left">
      <div class="hdr-eyebrow">Matter Mind SR Portal</div>
      <div class="hdr-title">Previous Day Summary</div>
      <div class="hdr-date">{{ $summaryDate->format('l, F j, Y') }}</div>
    </div>
    <div class="hdr-right">
      <div class="role-pill">⬡ {{ $user->role->name ?? 'Maintenance Lead' }}</div>
      <div class="hdr-time">Generated at {{ now()->format('h:i A') }}</div>
    </div>
  </div>
    <div class="quick-bar">
    @if($data['enabled']['ml_completed'] ?? true)
      <div class="qb-pill qp-green">✅ {{ $data['quick']['completed'] }} Completed</div>
    @endif
    @if($data['enabled']['ml_punch'] ?? true)
      <div class="qb-pill qp-gold">⏱ {{ $data['quick']['punches'] }} Punched</div>
    @endif
    @if($data['enabled']['ml_rework'] ?? true)
      <div class="qb-pill qp-purp">🔄 {{ $data['quick']['rework'] }} Rework</div>
    @endif
    @if($data['enabled']['ml_pending'] ?? true)
      <div class="qb-pill qp-amb">⏳ {{ $data['quick']['pending'] }} Pending</div>
    @endif
  </div>
</div>

<div class="body">

  <div class="kpi-strip">
  @if($data['enabled']['ml_completed'] ?? true)
    <div class="kpi k-green" onclick="openSheet('completed')">
      <div class="kpi-ico ki-green">✅</div>
      <div class="kpi-val kv-green">{{ $data['kpis']['completed']['value'] }}</div>
      <div class="kpi-lbl">Completed</div>
      <div class="kpi-sub">{{ $data['kpis']['completed']['sub'] }}</div>
      <div class="kpi-more">View list ›</div>
    </div>
  @endif
  @if($data['enabled']['ml_punch'] ?? true)
    <div class="kpi k-gold" onclick="openSheet('punches')">
      <div class="kpi-ico ki-gold">⏱</div>
      <div class="kpi-val kv-gold">{{ $data['kpis']['punches']['value'] }}</div>
      <div class="kpi-lbl">Punched In/Out</div>
      <div class="kpi-sub">{{ $data['kpis']['punches']['sub'] }}</div>
      <div class="kpi-more">View list ›</div>
    </div>
  @endif
  @if($data['enabled']['ml_rework'] ?? true)
    <div class="kpi k-purp" onclick="openSheet('rework')">
      <div class="kpi-ico ki-purp">🔄</div>
      <div class="kpi-val kv-purp">{{ $data['kpis']['rework']['value'] }}</div>
      <div class="kpi-lbl">Rework</div>
      <div class="kpi-sub">{{ $data['kpis']['rework']['sub'] }}</div>
      <div class="kpi-more">View list ›</div>
    </div>
  @endif
  @if($data['enabled']['ml_pending'] ?? true)
    <div class="kpi k-amb" onclick="openSheet('pending')">
      <div class="kpi-ico ki-amb">⏳</div>
      <div class="kpi-val kv-amb">{{ $data['kpis']['pending']['value'] }}</div>
      <div class="kpi-lbl">Pending Today</div>
      <div class="kpi-sub">{{ $data['kpis']['pending']['sub'] }}</div>
      <div class="kpi-more">View list ›</div>
    </div>
  @endif
  @if($data['enabled']['ml_expenses'] ?? true)
    <div class="kpi k-blue" onclick="openSheet('expenses')">
      <div class="kpi-ico ki-blue">💳</div>
      <div class="kpi-val kv-blue">{{ $data['kpis']['expenses']['value'] }}</div>
      <div class="kpi-lbl">Expenses</div>
      <div class="kpi-sub">{{ $data['kpis']['expenses']['sub'] }}</div>
      <div class="kpi-more">View list ›</div>
    </div>
  @endif
  @if($data['enabled']['ml_ratings'] ?? true)
    <div class="kpi k-red">
      <div class="kpi-ico ki-red">⭐</div>
      <div class="kpi-val kv-red">{{ $data['kpis']['rating']['value'] }}</div>
      <div class="kpi-lbl">Avg Rating</div>
      <div class="kpi-sub">{{ $data['kpis']['rating']['sub'] }}</div>
    </div>
  @endif
  </div>

  <div class="grid-2">

  @if($data['enabled']['ml_expenses'] ?? true)
    <div class="card" onclick="openSheet('expenses')">
      <div class="card-top">
        <div class="card-title">Expense Submissions</div>
        <div class="card-more-btn">View all ›</div>
      </div>
      <div class="exp-row">
        <div class="exp-big">{{ $data['expenses']['submissions'] }}</div>
        <div class="exp-sub">submissions logged</div>
      </div>
      <div class="exp-track"><div class="exp-fill" style="width:{{ $data['expenses']['submission_rate'] }}%;"></div></div>
      <div style="font-size:.65rem;color:#8a9890;margin-bottom:14px;">{{ $data['expenses']['submission_rate'] }}% receipts in · {{ $data['expenses']['pending'] }} pending</div>
      <div style="border-top:1px solid #f0f0ec;padding-top:12px;">
        <div style="font-size:.65rem;color:#8a9890;font-weight:700;text-transform:uppercase;
          letter-spacing:.07em;margin-bottom:6px;">Total Value Logged</div>
        <div style="font-size:1.8rem;font-weight:900;color:#6d28d9;line-height:1;">AED {{ number_format($data['expenses']['total_value']) }}</div>
      </div>
    </div>
  @endif

  @if($data['enabled']['ml_ratings'] ?? true)
    <div class="card">
      <div class="card-top">
        <div class="card-title">Client Ratings — Jobs Closed</div>
      </div>
      @if($data['ratings']['rated_count'] > 0)
        <div class="rating-stars">{{ $data['ratings']['avg_stars'] }}</div>
        <div style="font-size:1.6rem;font-weight:900;margin-top:6px;">{{ $data['ratings']['avg'] }} <span style="font-size:.8rem;color:#8a9890;font-weight:600;">/ 5</span></div>
        <div style="font-size:.7rem;color:#8a9890;margin-top:4px;">from {{ $data['ratings']['rated_count'] }} rated job(s)</div>
      @else
        <div style="padding:20px 4px;color:#9a9890;font-size:.8rem;">No ratings received yet for yesterday's completed jobs.</div>
      @endif
    </div>
  @endif

  </div>

  <div class="grid-2">

  @if($data['enabled']['ml_rework'] ?? true)
    <div class="card" onclick="openSheet('rework')">
      <div class="card-top">
        <div class="card-title">Rework — Previous Submissions</div>
        <div class="card-more-btn">View ›</div>
      </div>
      <div class="card-big" style="color:#6d28d9;margin-bottom:10px;">{{ $data['kpis']['rework']['value'] }}</div>
      <div class="item-list">
        @foreach(array_slice($data['sheets']['rework']['rows'], 0, 5) as $row)
        <div class="list-item">
          <div class="li-dot" style="background:#6d28d9;"></div>
          <div class="li-body">
            <div class="li-sr">{{ $row[0] }} · {{ $row[1] }}</div>
            <div class="li-reason">{{ $row[2] }}</div>
          </div>
        </div>
        @endforeach
        @if(!count($data['sheets']['rework']['rows']))
        <div style="padding:14px 4px;color:#9a9890;font-size:.75rem;text-align:center;">No rework from your previous submissions.</div>
        @endif
      </div>
    </div>
  @endif

  @if($data['enabled']['ml_pending'] ?? true)
    <div class="card" onclick="openSheet('pending')">
      <div class="card-top">
        <div class="card-title">Pending Jobs — Waiting Today</div>
        <div class="card-more-btn">View ›</div>
      </div>
      <div class="card-big" style="color:#b45309;margin-bottom:10px;">{{ $data['kpis']['pending']['value'] }}</div>
      <div class="item-list">
        @foreach(array_slice($data['sheets']['pending']['rows'], 0, 5) as $row)
        <div class="list-item">
          <div class="li-dot" style="background:#f59e0b;"></div>
          <div class="li-body">
            <div class="li-sr">{{ $row[0] }} · {{ $row[1] }}</div>
            <div class="li-reason">{{ $row[3] }} · {{ $row[4] }}</div>
          </div>
        </div>
        @endforeach
        @if(!count($data['sheets']['pending']['rows']))
        <div style="padding:14px 4px;color:#9a9890;font-size:.75rem;text-align:center;">Nothing waiting — all clear.</div>
        @endif
      </div>
    </div>
  @endif

  </div>

</div>

<div class="cta-wrap">
  <a class="cta-btn" href="{{ $loginUrl }}">Open Portal Dashboard →</a>
</div>
<div class="footer">Matter Mind SR Portal · Maintenance Lead Summary · {{ $summaryDate->format('d M Y') }} · Link valid 24 hours</div>

<div class="sheet-overlay" id="overlay" onclick="closeSheet()"></div>

<div class="bottom-sheet" id="bottomSheet">
  <div class="bs-handle"></div>
  <div class="bs-hdr">
    <div class="bs-title" id="bsTitle">Details</div>
    <button class="bs-close" onclick="closeSheet()">✕</button>
  </div>
  <div class="bs-body" id="bsBody"></div>
</div>

<script>
var PAGE_DATA = @json($data);

var SHEET_COLUMNS = {
  completed: ['SR ID','Client','Category','Rating','Completed At'],
  punches:   ['SR ID','Client','Punch In','Punch Out'],
  rework:    ['SR ID','Client','Reason','Rejected At'],
  pending:   ['SR ID','Client','Category','Status','ETA'],
  expenses:  ['SR ID','Category','Amount','Receipt'],
};

function renderGenericTable(key){
  var sheet = PAGE_DATA.sheets[key];
  if(!sheet || !sheet.rows || !sheet.rows.length){
    return '<div style="padding:24px 4px;color:#9a9890;font-size:.8rem;text-align:center;">No detail rows for this section yet.</div>';
  }
  var cols = SHEET_COLUMNS[key] || ['SR ID','Client','Category','Status','Date'];
  var head = '<tr>'+cols.map(function(c){return '<th>'+c+'</th>';}).join('')+'</tr>';
  var body = sheet.rows.map(function(r){
    return '<tr>'+r.map(function(v){return '<td>'+v+'</td>';}).join('')+'</tr>';
  }).join('');
  return '<table class="detail-table"><thead>'+head+'</thead><tbody>'+body+'</tbody></table>';
}

function openSheet(key){
  var sheet = PAGE_DATA.sheets[key];
  if(!sheet) return;
  document.getElementById('bsTitle').textContent = sheet.title;
  document.getElementById('bsBody').innerHTML = renderGenericTable(key);
  document.getElementById('overlay').classList.add('open');
  document.getElementById('bottomSheet').classList.add('open');
  document.body.style.overflow = 'hidden';
}

function closeSheet(){
  document.getElementById('overlay').classList.remove('open');
  document.getElementById('bottomSheet').classList.remove('open');
  document.body.style.overflow = '';
}

document.addEventListener('keydown', function(e){ if(e.key === 'Escape') closeSheet(); });
</script>
</body>
</html>