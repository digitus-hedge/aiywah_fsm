
<style>
.sa-role-rail{display:flex;gap:6px;padding:12px 14px;border-bottom:1px solid var(--border-color);flex-wrap:wrap;}
.sa-role-btn{flex:1;min-width:110px;display:flex;flex-direction:column;align-items:center;gap:2px;padding:8px 6px;border-radius:8px;border:1px solid var(--border-color);background:var(--surface-2);color:var(--text-muted);cursor:pointer;font-size:.72rem;font-weight:600;transition:all .15s;}
.sa-role-btn i{font-size:1rem;}
.sa-role-btn:hover{border-color:rgba(154,123,79,.35);}
.sa-role-btn.active{background:linear-gradient(135deg,rgba(154,123,79,.15),rgba(196,168,130,.1));color:#9A7B4F;border-color:#9A7B4F;}

.sa-user-row{display:flex;align-items:center;gap:9px;padding:9px 12px;border-radius:8px;cursor:pointer;margin-bottom:2px;transition:background .12s;}
.sa-user-row:hover{background:var(--surface-2);}
.sa-user-row.selected{background:var(--app-bg);border:1px solid var(--card-border);box-shadow:var(--card-shadow);}
.sa-user-av{width:30px;height:30px;border-radius:50%;background:linear-gradient(135deg,#9A7B4F,#C4A882);display:flex;align-items:center;justify-content:center;color:#fff;font-size:.68rem;font-weight:700;flex-shrink:0;}
.sa-user-name{font-size:.8rem;font-weight:500;color:var(--text-heading);}
.sa-user-sub{font-size:.68rem;color:var(--text-muted);}

.sa-item{display:flex;align-items:flex-start;gap:12px;padding:12px 4px;border-bottom:1px solid var(--border-color);}
.sa-item:last-child{border-bottom:none;}
.sa-item-body{flex:1;min-width:0;}
.sa-item-title{font-size:.82rem;font-weight:600;color:var(--text-heading);margin-bottom:2px;}
.sa-item-desc{font-size:.73rem;color:var(--text-muted);line-height:1.4;}
.sa-checkbox{width:18px;height:18px;flex-shrink:0;margin-top:2px;accent-color:#9A7B4F;cursor:pointer;}

.sa-days-card{border:1px solid var(--border-color);border-radius:9px;padding:12px 14px;margin-bottom:14px;}
.sa-days-hdr{display:flex;align-items:center;justify-content:space-between;margin-bottom:10px;}
.sa-days-title{font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--text-muted);}
.sa-days-row{display:flex;gap:8px;flex-wrap:wrap;}
.sa-day-pill{display:flex;align-items:center;gap:6px;padding:6px 11px;border-radius:20px;border:1px solid var(--border-color);background:var(--surface-2);cursor:pointer;font-size:.75rem;font-weight:600;color:var(--text-muted);transition:all .12s;user-select:none;}
.sa-day-pill.on{background:rgba(154,123,79,.14);border-color:#9A7B4F;color:#9A7B4F;}
.sa-day-pill input{accent-color:#9A7B4F;width:14px;height:14px;cursor:pointer;}

.sa-link-btn{background:none;border:none;color:#9A7B4F;font-size:.72rem;font-weight:600;cursor:pointer;padding:2px 4px;}
.sa-link-btn:hover{text-decoration:underline;}

.sa-status-dot{width:8px;height:8px;border-radius:50%;flex-shrink:0;margin-left:6px;}
.sa-status-dot.on{background:#10b981;}
.sa-status-dot.off{background:#9ca3af;}
</style>

<div class="master-panel" id="panel-summary-alert">
  <div class="md-layout">

    <!-- LEFT: role selector + user list -->
    <div class="md-left">
      <div class="sa-role-rail" id="sa-role-rail"><!-- rendered by JS --></div>
      <div class="md-left-hdr">
        <div class="md-left-hdr-title">
          <i class="bi bi-people"></i>
          <span id="sa-user-list-title">Select a role</span>
        </div>
      </div>
      <div class="md-list" id="sa-user-list">
        <div class="no-sel" style="padding:32px 16px;">
          <div class="no-sel-icon"><i class="bi bi-person-badge"></i></div>
          <h6>Pick a role above</h6>
          <p>Its users will show up here.</p>
        </div>
      </div>
    </div>

    <!-- RIGHT: checklist for the selected user -->
    <div class="md-right" id="sa-right">
      <div class="no-sel" id="sa-no-sel-state">
        <div class="no-sel-icon"><i class="bi bi-arrow-left"></i></div>
        <h6>Select a User</h6>
        <p>Pick a role, then a user, to manage their Daily Summary alert checklist.</p>
      </div>

      <div id="sa-checklist-panel" style="display:none;flex-direction:column;flex:1;">
        <div class="md-right-hdr">
          <div class="md-right-hdr-title">
            <span style="font-size:.8rem;color:var(--text-muted);">Summary alerts for</span>
            <div id="sa-selected-user-pill" class="cat-label-pill" style="background:rgba(154,123,79,.12);color:#9A7B4F;"></div>
          </div>
          <div style="display:flex;gap:14px;align-items:center;">
            <button class="sa-link-btn" onclick="saToggleAll(true)">Check all</button>
            <button class="sa-link-btn" onclick="saToggleAll(false)">Uncheck all</button>
            <button class="btn-primary-gold" style="padding:5px 13px;font-size:.75rem;" onclick="saveSummaryAlert()">
              <i class="bi bi-floppy"></i>Save
            </button>
          </div>
        </div>
        <div class="md-right-body">

  <div class="sa-days-card" id="sa-enable-card" style="border-color:#9A7B4F;margin-bottom:14px;">
    <div class="sa-days-hdr">
      <span class="sa-days-title"><i class="bi bi-power"></i>&nbsp; Summary Alerts</span>
    </div>
    <div class="tog-wrap" onclick="saToggleEnabled()">
      <div class="tog-track" id="sa-enable-track"><div class="tog-thumb"></div></div>
      <span class="tog-label" id="sa-enable-label">Disabled - no summary will be sent to this user</span>
    </div>
  </div>

  <div class="info-banner green" style="margin-bottom:6px;">
    <i class="bi bi-whatsapp"></i>
    <span>These are the sections included in this user's daily-morning WhatsApp summary. Everything starts checked - untick anything they don't need.</span>
  </div>

  <div id="sa-configured-block" style="display:none;">
    <div class="sa-days-card">
      <div class="sa-days-hdr">
        <span class="sa-days-title"><i class="bi bi-calendar-week"></i>&nbsp; Days to Send</span>
        <span>
          <button class="sa-link-btn" onclick="saDaysToggleAll(true)">All days</button>
          <button class="sa-link-btn" onclick="saDaysToggleAll(false)">None</button>
        </span>
      </div>
      <div class="sa-days-row" id="sa-days-row"><!-- rendered by JS --></div>
    </div>

    <div id="sa-item-list"><!-- rendered by JS --></div>
  </div>

  <div class="no-sel" id="sa-disabled-hint" style="padding:32px 16px;">
    <div class="no-sel-icon"><i class="bi bi-toggle-off"></i></div>
    <h6>Summary Alerts are off for this user</h6>
    <p>Turn on the switch above to configure which days and sections they receive.</p>
  </div>

</div>
      </div>
    </div>

  </div>
</div>

@push('scripts')
<script>
/* ─── SUMMARY ALERT: roles config (must mirror MasterController::SUMMARY_ALERT_ROLES) ─── */
const SA_ROLES = [
  { slug: 'admin', label: 'Admin',              icon: 'bi-shield-fill-check' },
  { slug: 'hop',   label: 'HoP',                icon: 'bi-person-badge' },
  { slug: 'se',    label: 'Service Engineer',   icon: 'bi-person-gear' },
  { slug: 'ml',    label: 'Maintenance Lead',   icon: 'bi-tools' },
  { slug: 'ac',    label: 'Accounts',           icon: 'bi-cash-coin' },
];

window.SA_ROUTES = {
  usersByRole:      (slug)  => `{{ url('masters/summary-alert/users') }}/${slug}`,
  permissionsByUser:(id)    => `{{ url('masters/summary-alert/permissions') }}/${id}`,
  save:             "{{ route('masters.summary-alert.save') }}",
};

let saSelectedRole = null;
let saSelectedUser = null;
let saItems = []; // [{id,key,title,description,is_enabled}]
let saDays = {};  // {monday:true, tuesday:true, ..., sunday:true}

const SA_DAY_DEFS = [
  { key: 'monday',    label: 'Mon' },
  { key: 'tuesday',   label: 'Tue' },
  { key: 'wednesday', label: 'Wed' },
  { key: 'thursday',  label: 'Thu' },
  { key: 'friday',    label: 'Fri' },
  { key: 'saturday',  label: 'Sat' },
  { key: 'sunday',    label: 'Sun' },
];

function renderSaDays(){
  const el = document.getElementById('sa-days-row');
  el.innerHTML = SA_DAY_DEFS.map(d => `
    <label class="sa-day-pill${saDays[d.key] ? ' on' : ''}" id="sa-day-pill-${d.key}">
      <input type="checkbox" ${saDays[d.key] ? 'checked' : ''} onchange="saToggleDay('${d.key}')">
      ${d.label}
    </label>
  `).join('');
}

function saToggleDay(dayKey){
  saDays[dayKey] = !saDays[dayKey];
  document.getElementById(`sa-day-pill-${dayKey}`).classList.toggle('on', saDays[dayKey]);
}

function saDaysToggleAll(state){
  SA_DAY_DEFS.forEach(d => saDays[d.key] = state);
  renderSaDays();
}

function saInitials(name){
  return (name || '?').trim().split(/\s+/).slice(0,2).map(w=>w[0]?.toUpperCase()||'').join('');
}

function renderSaRoleRail(){
  const el = document.getElementById('sa-role-rail');
  el.innerHTML = SA_ROLES.map(r => `
    <div class="sa-role-btn${saSelectedRole===r.slug?' active':''}" onclick="saSelectRole('${r.slug}')">
      <i class="bi ${r.icon}"></i><span>${r.label}</span>
    </div>
  `).join('');
}

async function saSelectRole(slug){
  saSelectedRole = slug;
  saSelectedUser = null;
  renderSaRoleRail();
  document.getElementById('sa-checklist-panel').style.display = 'none';
  document.getElementById('sa-no-sel-state').style.display = 'flex';

  const roleLabel = SA_ROLES.find(r=>r.slug===slug)?.label || slug;
  document.getElementById('sa-user-list-title').textContent = roleLabel + ' Users';
  const listEl = document.getElementById('sa-user-list');
  listEl.innerHTML = `<div style="padding:24px;text-align:center;color:var(--text-muted);font-size:.78rem;">Loading users…</div>`;

  try {
    const r = await api(window.SA_ROUTES.usersByRole(slug), 'GET');
    const users = r.data || [];
    if(!users.length){
      listEl.innerHTML = `<div class="no-sel" style="padding:32px 16px;"><div class="no-sel-icon"><i class="bi bi-person-x"></i></div><h6>No users in this role</h6><p>Add users under ${roleLabel} first.</p></div>`;
      return;
    }
    listEl.innerHTML = users.map(u => `
  <div class="sa-user-row" id="sa-user-row-${u.id}" onclick="saSelectUser(${u.id}, '${(u.name||'').replace(/'/g,"\\'")}')">
    <div class="sa-user-av">${saInitials(u.name)}</div>
    <div style="flex:1;min-width:0;">
      <div class="sa-user-name">${u.name || 'Unnamed user'}</div>
      <div class="sa-user-sub">${u.phone || u.email || ''}</div>
    </div>
    <span class="sa-status-dot ${u.summary_enabled ? 'on' : 'off'}" id="sa-status-dot-${u.id}" title="${u.summary_enabled ? 'Enabled' : 'Disabled'}"></span>
  </div>
`).join('');
  } catch(e) {
    listEl.innerHTML = `<div style="padding:20px;color:#ef4444;font-size:.78rem;">${e.message}</div>`;
  }
}

async function saSelectUser(userId, name){
  saSelectedUser = userId;
  document.querySelectorAll('.sa-user-row').forEach(el=>el.classList.remove('selected'));
  document.getElementById(`sa-user-row-${userId}`)?.classList.add('selected');

  document.getElementById('sa-no-sel-state').style.display = 'none';
  const panel = document.getElementById('sa-checklist-panel');
  panel.style.display = 'flex';
  document.getElementById('sa-selected-user-pill').innerHTML = `<i class="bi bi-person-fill"></i>${name}`;

  const listEl = document.getElementById('sa-item-list');
  listEl.innerHTML = `<div style="padding:24px;text-align:center;color:var(--text-muted);font-size:.78rem;">Loading checklist…</div>`;

  try {
    const r = await api(window.SA_ROUTES.permissionsByUser(userId), 'GET');
    saItems   = (r.data && r.data.items) || [];
    saDays    = (r.data && r.data.days) || {};
    saEnabled = !!(r.data && r.data.enabled);

    updateEnabledUI();   // ← replaces the inline toggle code
    renderSaDays();
    renderSaItems();
  } catch(e) {
    listEl.innerHTML = `<div style="padding:20px;color:#ef4444;font-size:.78rem;">${e.message}</div>`;
  }
}

function renderSaItems(){
  const listEl = document.getElementById('sa-item-list');
  if(!saItems.length){
    listEl.innerHTML = `<div class="no-sel" style="padding:24px;"><div class="no-sel-icon"><i class="bi bi-inbox"></i></div><h6>No alert items defined for this role yet</h6></div>`;
    return;
  }
  listEl.innerHTML = saItems.map(it => `
    <div class="sa-item">
      <div class="sa-item-body">
        <div class="sa-item-title">${it.title}</div>
        <div class="sa-item-desc">${it.description || ''}</div>
      </div>
      <input type="checkbox" class="sa-checkbox" id="sa-cb-${it.id}"
        ${it.is_enabled ? 'checked' : ''} onchange="saToggleItem(${it.id})">
    </div>
  `).join('');
}

function saToggleItem(alertTypeId){
  const item = saItems.find(i=>i.id===alertTypeId);
  if(!item) return;
  item.is_enabled = document.getElementById(`sa-cb-${alertTypeId}`).checked;
}

function saToggleAll(state){
  saItems.forEach(i => i.is_enabled = state);
  renderSaItems();
}
let saEnabled = false; // NEW: gates whether this user actually receives the WhatsApp send

function saToggleEnabled(){
  saEnabled = !saEnabled;
  updateEnabledUI();
}

function updateEnabledUI(){
  const track = document.getElementById('sa-enable-track');
  const label = document.getElementById('sa-enable-label');
  const configuredBlock = document.getElementById('sa-configured-block');
  const disabledHint = document.getElementById('sa-disabled-hint');

  track.classList.toggle('on', saEnabled);
  label.textContent = saEnabled
    ? 'Enabled - this user will receive their daily summary'
    : 'Disabled - no summary will be sent to this user';

  configuredBlock.style.display = saEnabled ? 'block' : 'none';
  disabledHint.style.display    = saEnabled ? 'none'  : 'flex';
}
async function saveSummaryAlert(){
  if(!saSelectedUser){ showToast('err','No User Selected','Pick a user first.'); return; }
  const payload = {
    user_id: saSelectedUser,
    items: saItems.map(i => ({ alert_type_id: i.id, is_enabled: i.is_enabled })),
    days: saDays,
    enabled: saEnabled,
  };
  try {
    await api(window.SA_ROUTES.save, 'POST', payload);
    showToast('ok','Saved','Summary alert preferences updated.');

    // Reflect the saved state immediately - both in the checklist panel
    // and in the user-list dot - without waiting for a re-fetch.
    updateEnabledUI();

    const dot = document.getElementById(`sa-status-dot-${saSelectedUser}`);
    if (dot) {
      dot.classList.toggle('on', saEnabled);
      dot.classList.toggle('off', !saEnabled);
      dot.title = saEnabled ? 'Enabled' : 'Disabled';
    }
  } catch(e) {
    showToast('err','Error', e.message);
  }
}

renderSaRoleRail();
</script>
@endpush