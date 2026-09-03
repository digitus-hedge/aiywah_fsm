<style>
.srtm-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:2000;backdrop-filter:blur(3px);align-items:center;justify-content:center;padding:16px;}
.srtm-overlay.show{display:flex;}
.srtm-box{background:#fff;width:100%;max-width:560px;max-height:88vh;border-radius:14px;overflow:hidden;display:flex;flex-direction:column;box-shadow:0 20px 60px rgba(0,0,0,.3);animation:srtmIn .18s ease;}
@keyframes srtmIn{from{opacity:0;transform:translateY(12px) scale(.98)}to{opacity:1;transform:none}}
.srtm-hdr{padding:16px 18px;border-bottom:1px solid #e8e2d8;display:flex;align-items:flex-start;justify-content:space-between;gap:10px;flex-shrink:0;}
.srtm-ref{font-size:1.05rem;font-weight:700;color:#9a8053;}
.srtm-sub{font-size:.78rem;color:#7a756e;margin-top:2px;}
.srtm-close{width:30px;height:30px;border-radius:7px;border:1px solid #e8e2d8;background:#f9f6f2;color:#7a756e;cursor:pointer;flex-shrink:0;}
.srtm-chip{display:inline-flex;align-items:center;gap:6px;padding:5px 12px;border-radius:20px;font-size:.75rem;font-weight:700;margin-top:6px;}
.chip-ok{background:rgba(21,128,61,.1);color:#15803d;}
.chip-warn{background:rgba(180,83,9,.1);color:#b45309;}
.chip-bad{background:rgba(220,38,38,.1);color:#dc2626;}
.chip-info{background:rgba(37,99,235,.1);color:#2563eb;}
.chip-inprog{background:rgba(8,145,178,.1);color:#0891b2;}
.chip-pending{background:rgba(124,58,237,.1);color:#7c3aed;}
.srtm-body{overflow-y:auto;padding:16px 18px 24px;}
.srtm-meta{display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:18px;}
.srtm-cell{background:#f9f6f2;border-radius:8px;padding:8px 10px;}
.srtm-cell-label{font-size:.63rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#7a756e;margin-bottom:2px;}
.srtm-cell-value{font-size:.8rem;font-weight:600;color:#1a1614;}
.srtm-section-title{font-size:.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#7a756e;margin:16px 0 10px;}
.srtm-tl-item{display:flex;gap:12px;}
.srtm-tl-left{display:flex;flex-direction:column;align-items:center;width:26px;flex-shrink:0;}
.srtm-tl-node{width:26px;height:26px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:.7rem;border:2px solid #e8e2d8;background:#fff;flex-shrink:0;}
.srtm-tl-node.done{background:#9a8053;border-color:#9a8053;color:#fff;}
.srtm-tl-node.active{background:#fff;border-color:#9a8053;color:#9a8053;box-shadow:0 0 0 3px rgba(154,128,83,.15);}
.srtm-tl-node.pending{background:#f9f6f2;color:#c5bdb0;}
.srtm-tl-line{width:2px;flex:1;background:#e8e2d8;min-height:14px;margin-top:2px;}
.srtm-tl-line.done{background:#9a8053;}
.srtm-tl-item:last-child .srtm-tl-line{display:none;}
.srtm-tl-right{flex:1;padding-top:2px;padding-bottom:16px;}
.srtm-tl-label{font-size:.82rem;font-weight:600;color:#1a1614;}
.srtm-tl-label.pending{color:#a09890;font-weight:500;}
.srtm-tl-time{font-size:.72rem;color:#9a8053;font-weight:600;}
.srtm-tl-desc{font-size:.72rem;color:#a09890;margin-top:2px;}
.srtm-tl-item.srtm-tl-last .srtm-tl-line{display:none;}
.srtm-hist-item{display:flex;gap:10px;padding:9px 0;border-bottom:1px solid #f0ece6;}
.srtm-hist-item:last-child{border-bottom:none;}
.srtm-hist-dot{width:7px;height:7px;border-radius:50%;margin-top:5px;flex-shrink:0;}
.srtm-hist-event{font-size:.78rem;font-weight:500;color:#1a1614;}
.srtm-hist-meta{font-size:.7rem;color:#a09890;}
.srtm-hist-time{font-size:.68rem;color:#c5bdb0;white-space:nowrap;margin-left:auto;text-align:right;}
.srtm-loading{padding:40px 0;text-align:center;color:#a09890;font-size:.85rem;}
.sr-ref-trigger{cursor:pointer;color:#9a8053;font-weight:600;text-decoration:none;border-bottom:1px dashed rgba(154,128,83,.4);}
.sr-ref-trigger:hover{border-bottom-style:solid;}
.srtm-tl-by{font-size:.7rem;color:#9a8053;font-weight:600;margin-top:3px;display:flex;align-items:center;gap:4px;}
.srtm-tl-by i{font-size:.68rem;opacity:.75;}

.srtm-owner-banner{display:flex;align-items:center;gap:8px;background:#f9f6f2;border:1px solid #e8e2d8;border-radius:8px;padding:9px 12px;margin-bottom:14px;font-size:.78rem;}
.srtm-owner-banner i{color:#9a8053;font-size:.85rem;flex-shrink:0;}
.srtm-owner-banner strong{color:#1a1614;font-weight:700;}
</style>

<div class="srtm-overlay" id="srtm-overlay" onclick="if(event.target===this) closeSrTracking()">
  <div class="srtm-box">
    <div class="srtm-hdr">
      <div>
        <div class="srtm-ref" id="srtm-ref">-</div>
        <div class="srtm-sub" id="srtm-sub">-</div>
        <div class="srtm-chip" id="srtm-chip"><i class="bi bi-circle"></i><span id="srtm-chip-text">-</span></div>
      </div>
      <button class="srtm-close" onclick="closeSrTracking()"><i class="bi bi-x-lg"></i></button>
    </div>
    <div class="srtm-body">
      <div id="srtm-content">
        <div class="srtm-loading"><i class="bi bi-arrow-repeat" style="font-size:1.4rem;"></i><br>Loading tracking…</div>
      </div>
    </div>
  </div>
</div>

<script>
(function(){
  const BASE = @json($srTrackingBase ?? url('/sr-tracking'));

  function esc(v){ return String(v == null ? '' : v)
    .replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }

  window.openSrTracking = function(srId){
    const overlay = document.getElementById('srtm-overlay');
    overlay.classList.add('show');
    document.body.style.overflow = 'hidden';
    document.getElementById('srtm-content').innerHTML =
      '<div class="srtm-loading"><i class="bi bi-arrow-repeat" style="font-size:1.4rem;"></i><br>Loading tracking…</div>';

    fetch(`${BASE}/${srId}/popup`, { headers: { 'Accept': 'application/json' } })
      .then(r => { if(!r.ok) throw new Error('HTTP '+r.status); return r.json(); })
      .then(renderSrTracking)
      .catch(() => {
        document.getElementById('srtm-content').innerHTML =
          '<div class="srtm-loading">Could not load tracking for this ticket.</div>';
      });
  };

  window.closeSrTracking = function(){
    document.getElementById('srtm-overlay').classList.remove('show');
    document.body.style.overflow = '';
  };

  function renderSrTracking(d){
    document.getElementById('srtm-ref').textContent = d.sr.ref;
    document.getElementById('srtm-sub').textContent = `${d.sr.client} · ${d.sr.site}`;
    document.getElementById('srtm-chip').className = 'srtm-chip ' + d.status.chip;
    document.getElementById('srtm-chip').querySelector('i').className = 'bi ' + d.status.icon;
    document.getElementById('srtm-chip-text').textContent = d.status.label;

        const owner = (d.ownership && d.ownership.owner)
      ? `<div class="srtm-owner-banner"><i class="bi bi-signpost-split-fill"></i><span>Currently with <strong>${esc(d.ownership.owner)}</strong></span></div>`
      : '';

    const meta = `
      ${owner}
      <div class="srtm-meta">
        <div class="srtm-cell"><div class="srtm-cell-label">Category</div><div class="srtm-cell-value">${esc(d.sr.category)}</div></div>
        <div class="srtm-cell"><div class="srtm-cell-label">Priority</div><div class="srtm-cell-value">${esc(d.sr.priority)}</div></div>
        <div class="srtm-cell"><div class="srtm-cell-label">Assigned To</div><div class="srtm-cell-value">${esc((d.ownership && d.ownership.assigned_to) || 'Unassigned')}</div></div>
        <div class="srtm-cell"><div class="srtm-cell-label">Site</div><div class="srtm-cell-value">${esc(d.sr.site)}</div></div>
        <div class="srtm-cell" style="grid-column:1/-1;"><div class="srtm-cell-label">Issue</div><div class="srtm-cell-value" style="font-weight:400;">${esc(d.sr.issue)}</div></div>
      </div>`;

    const timeline = `
  <div class="srtm-section-title"><i class="bi bi-signpost-2"></i> Progress</div>
  ${d.milestones
    .filter(m => m.state === 'done' || m.state === 'active')
    .map((m,i,arr) => {
      const label = (i === 0 && m.label === 'Pending') ? 'SR Created' : m.label;
      return `
      <div class="srtm-tl-item ${i === arr.length - 1 ? 'srtm-tl-last' : ''}">
        <div class="srtm-tl-left">
          <div class="srtm-tl-node ${m.state}"><i class="bi ${m.state==='done'?'bi-check':m.icon}"></i></div>
          <div class="srtm-tl-line ${m.state==='done'?'done':''}"></div>
        </div>
        <div class="srtm-tl-right">
          <div class="srtm-tl-label">${esc(label)}</div>
          <div class="srtm-tl-time">${m.time ? esc(m.time) : (m.state==='active' ? 'In progress' : 'Pending')}</div>
          <div class="srtm-tl-desc">${esc(m.desc)}</div>
          ${m.by ? `<div class="srtm-tl-by"><i class="bi bi-person-fill"></i>${esc(m.by)}</div>` : ''}
        </div>
      </div>`;
    }).join('') || '<div class="srtm-loading" style="padding:16px 0;">No milestones completed yet.</div>'}`;

    const history = d.history.length ? `
      <div class="srtm-section-title"><i class="bi bi-clock-history"></i> Activity Log</div>
      ${d.history.map(h => `
        <div class="srtm-hist-item">
          <div class="srtm-hist-dot" style="background:${esc(h.color)};"></div>
          <div>
            <div class="srtm-hist-event">${esc(h.event)}</div>
            <div class="srtm-hist-meta">${esc(h.meta)}</div>
          </div>
          <div class="srtm-hist-time">${esc(h.day)}<br>${esc(h.time)}</div>
        </div>`).join('')}` : '';

    document.getElementById('srtm-content').innerHTML = meta + timeline + history;
  }

  // Global delegated click - works for ANY page, any element with this class
  document.addEventListener('click', function(e){
    const el = e.target.closest('.sr-ref-trigger');
    if (el && el.dataset.srId) openSrTracking(el.dataset.srId);
  });

  document.addEventListener('keydown', function(e){
    if (e.key === 'Escape') closeSrTracking();
  });
})();
</script>