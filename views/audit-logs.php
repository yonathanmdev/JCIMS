<style>
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

  :root {
    --bg:        #f5f6fa;
    --surface:   #ffffff;
    --border:    #e2e5ec;
    --muted:     #8a93a8;
    --text:      #1a2032;
    --primary:   #3b5bdb;
    --primary-h: #2f4bc7;
    --success:   #2f9e44;
    --danger:    #c92a2a;
    --warning:   #e67700;
    --info:      #1971c2;
    --radius:    8px;
    --shadow:    0 1px 3px rgba(0,0,0,.07), 0 1px 2px rgba(0,0,0,.04);
  }

  body { font-family: 'Inter', system-ui, sans-serif; background: var(--bg); color: var(--text); font-size: 14px; line-height: 1.5; }

  /* ── Layout ── */
  .page { max-width: 1280px; margin: 0 auto; padding: 24px 20px; }
  .page-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px; gap: 12px; flex-wrap: wrap; }
  .page-title { font-size: 20px; font-weight: 600; }
  .page-sub   { font-size: 13px; color: var(--muted); margin-top: 2px; }

  /* ── Stat cards ── */
  .stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 12px; margin-bottom: 20px; }
  .stat  { background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius); padding: 14px 16px; }
  .stat-label { font-size: 11px; text-transform: uppercase; letter-spacing: .06em; color: var(--muted); margin-bottom: 6px; }
  .stat-value { font-size: 26px; font-weight: 600; line-height: 1; }
  .stat-value.danger  { color: var(--danger); }
  .stat-value.success { color: var(--success); }
  .stat-value.info    { color: var(--info); }

  /* ── Toolbar ── */
  .toolbar { display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 12px; align-items: center; }
  .toolbar input, .toolbar select {
    height: 36px; border: 1px solid var(--border); border-radius: var(--radius);
    padding: 0 10px; font-size: 13px; background: var(--surface); color: var(--text);
    outline: none; transition: border-color .15s;
  }
  .toolbar input:focus, .toolbar select:focus { border-color: var(--primary); }
  .toolbar input   { min-width: 200px; flex: 1; }
  .toolbar select  { min-width: 150px; }
  .toolbar-spacer  { flex: 1; }

  /* ── Buttons ── */
  .btn { display: inline-flex; align-items: center; gap: 6px; height: 36px; padding: 0 14px;
         border-radius: var(--radius); border: 1px solid var(--border); font-size: 13px;
         font-weight: 500; cursor: pointer; background: var(--surface); color: var(--text);
         transition: background .12s, border-color .12s; white-space: nowrap; }
  .btn:hover { background: var(--bg); }
  .btn-primary { background: var(--primary); color: #fff; border-color: var(--primary); }
  .btn-primary:hover { background: var(--primary-h); }
  .btn-sm { height: 28px; padding: 0 10px; font-size: 12px; }

  /* ── Table ── */
  .table-wrap { background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius); overflow: hidden; box-shadow: var(--shadow); }
  table { width: 100%; border-collapse: collapse; }
  thead th { padding: 10px 12px; text-align: left; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: .05em; color: var(--muted); background: #f8f9fb; border-bottom: 1px solid var(--border); white-space: nowrap; }
  tbody tr { border-bottom: 1px solid var(--border); transition: background .1s; cursor: pointer; }
  tbody tr:last-child { border-bottom: none; }
  tbody tr:hover, tbody tr.selected { background: #f0f4ff; }
  td { padding: 9px 12px; font-size: 13px; max-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
  td.wrap { white-space: normal; word-break: break-all; }

  /* ── Badges ── */
  .badge { display: inline-block; padding: 2px 8px; border-radius: 20px; font-size: 11px; font-weight: 600; letter-spacing: .02em; }
  .badge-login   { background: #d3f9d8; color: #1a6130; }
  .badge-logout  { background: #e9ecef; color: #495057; }
  .badge-create  { background: #d0ebff; color: #1864ab; }
  .badge-update  { background: #fff3bf; color: #7d5a00; }
  .badge-delete  { background: #ffe3e3; color: #9b2226; }
  .badge-view    { background: #e9ecef; color: #495057; }
  .badge-failed  { background: #ffe3e3; color: #9b2226; }
  .badge-export  { background: #f3d9fa; color: #6a1e87; }
  .badge-other   { background: #e8eeff; color: #3b5bdb; }
  .entity-tag { display: inline-block; padding: 2px 6px; border-radius: 4px; font-size: 11px; background: #f1f3f5; color: #495057; border: 1px solid #dee2e6; }

  /* ── Pagination ── */
  .pagination { display: flex; align-items: center; justify-content: space-between; margin-top: 14px; flex-wrap: wrap; gap: 8px; }
  .page-info  { font-size: 12px; color: var(--muted); }
  .page-btns  { display: flex; gap: 3px; }
  .page-btn   { min-width: 30px; height: 30px; padding: 0 6px; border: 1px solid var(--border); border-radius: var(--radius); background: var(--surface); color: var(--text); font-size: 12px; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: background .1s; }
  .page-btn:hover   { background: var(--bg); }
  .page-btn.active  { background: var(--primary); color: #fff; border-color: var(--primary); }
  .page-btn:disabled { opacity: .4; cursor: not-allowed; pointer-events: none; }
  .per-page { display: flex; align-items: center; gap: 6px; font-size: 12px; color: var(--muted); }
  .per-page select { height: 28px; font-size: 12px; padding: 0 6px; border: 1px solid var(--border); border-radius: var(--radius); background: var(--surface); }

  /* ── Detail panel ── */
  .detail-panel { display: none; background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius); padding: 18px 20px; margin-top: 14px; box-shadow: var(--shadow); }
  .detail-panel.open { display: block; }
  .detail-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px; }
  .detail-title  { font-size: 14px; font-weight: 600; }
  .detail-grid   { display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 12px; }
  .detail-field  { }
  .detail-key    { font-size: 11px; text-transform: uppercase; letter-spacing: .05em; color: var(--muted); margin-bottom: 3px; }
  .detail-val    { font-size: 13px; font-family: 'SF Mono', 'Fira Code', monospace; word-break: break-all; }
  .detail-full   { grid-column: 1 / -1; }
  .diff-row      { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-top: 12px; }
  .diff-box      { border: 1px solid var(--border); border-radius: var(--radius); padding: 10px 12px; font-size: 12px; font-family: 'SF Mono', 'Fira Code', monospace; max-height: 160px; overflow-y: auto; white-space: pre-wrap; word-break: break-all; }
  .diff-label    { font-size: 10px; font-weight: 700; letter-spacing: .06em; text-transform: uppercase; margin-bottom: 6px; }
  .diff-old      { border-color: #ffa8a8; background: #fff5f5; }
  .diff-old .diff-label { color: var(--danger); }
  .diff-new      { border-color: #8ce99a; background: #f4fdf5; }
  .diff-new .diff-label { color: var(--success); }

  /* ── Empty / Loading ── */
  .empty-state { padding: 48px; text-align: center; color: var(--muted); }
  .loading-row td { text-align: center; padding: 32px; color: var(--muted); }
  .spinner { display: inline-block; width: 18px; height: 18px; border: 2px solid var(--border); border-top-color: var(--primary); border-radius: 50%; animation: spin .7s linear infinite; margin-right: 8px; vertical-align: middle; }
  @keyframes spin { to { transform: rotate(360deg); } }

  /* ── Toast ── */
  .toast { position: fixed; bottom: 20px; right: 20px; background: #1a2032; color: #fff; padding: 10px 16px; border-radius: var(--radius); font-size: 13px; opacity: 0; transition: opacity .25s; pointer-events: none; z-index: 999; }
  .toast.show { opacity: 1; }

  @media (max-width: 768px) {
    .diff-row { grid-template-columns: 1fr; }
    .detail-grid { grid-template-columns: 1fr; }
    thead th:nth-child(n+5) { display: none; }
    td:nth-child(n+5) { display: none; }
  }
</style>
<div class="page">

  <!-- Header -->
  <div class="page-header">
    <div>
      <h1 class="page-title">Audit Log</h1>
      <p class="page-sub">Complete activity history for your JCIMS system</p>
    </div>
    <button class="btn" id="btnExport">&#x21E9; Export CSV</button>
  </div>

  <!-- Stat cards -->
  <div class="stats" id="stats">
    <div class="stat"><div class="stat-label">Total events</div><div class="stat-value" id="s-total">—</div></div>
    <div class="stat"><div class="stat-label">Successful logins</div><div class="stat-value info" id="s-logins">—</div></div>
    <div class="stat"><div class="stat-label">Data changes</div><div class="stat-value" id="s-changes">—</div></div>
    <div class="stat"><div class="stat-label">Failures / errors</div><div class="stat-value danger" id="s-failures">—</div></div>
  </div>

  <!-- Toolbar -->
  <div class="toolbar">
    <input type="text" id="search" placeholder="Search action, entity, IP…">
    <select id="filterAction"><option value="">All actions</option></select>
    <select id="filterEntity"><option value="">All entity types</option></select>
    <select id="filterDate">
      <option value="">All time</option>
      <option value="today">Today</option>
      <option value="week">This week</option>
      <option value="month">This month</option>
    </select>
    <button class="btn btn-sm" id="btnReset">Clear filters</button>
  </div>

  <!-- Table -->
  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th style="width:14%">Timestamp</th>
          <th style="width:14%">User</th>
          <th style="width:14%">Action</th>
          <th style="width:11%">Entity type</th>
          <th style="width:20%">Entity ID</th>
          <th style="width:11%">IP address</th>
          <th style="width:10%">Changes</th>
          <th style="width:6%"></th>
        </tr>
      </thead>
      <tbody id="tbody">
        <tr class="loading-row"><td colspan="8"><span class="spinner"></span> Loading…</td></tr>
      </tbody>
    </table>
  </div>

  <!-- Pagination -->
  <div class="pagination">
    <div class="page-info" id="pageInfo"></div>
    <div class="per-page">
      Rows per page
      <select id="perPage">
        <option value="10" selected>10</option>
        <option value="25">25</option>
        <option value="50">50</option>
        <option value="100">100</option>
      </select>
    </div>
    <div class="page-btns" id="pageBtns"></div>
  </div>

  <!-- Detail panel -->
  <div class="detail-panel" id="detailPanel">
    <div class="detail-header">
      <span class="detail-title">Entry detail</span>
      <button class="btn btn-sm" id="btnCloseDetail">&#x2715; Close</button>
    </div>
    <div id="detailContent"></div>
  </div>

</div>

<!-- Toast -->
<div class="toast" id="toast"></div>

<script nonce="<?= htmlspecialchars($GLOBALS['nonce']); ?>">
// ── Config ────────────────────────────────────────────────────────────────────
const CSRF      = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

// ── State ─────────────────────────────────────────────────────────────────────
let state = { page: 1, perPage: 10, search: '', action: '', entity: '', date: '' };
let debounceTimer;

// ── Fetch helpers ─────────────────────────────────────────────────────────────
async function apiFetch(path, opts = {}) {
  const res = await fetch(BASE_URL + path, {
    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF, ...opts.headers },
    ...opts,
  });
  if (!res.ok) throw new Error(`HTTP ${res.status}`);
  return res.json();
}

// ── Stats ─────────────────────────────────────────────────────────────────────
async function loadStats() {
  try {
    const { data } = await apiFetch('/?action=audit-logs-stats');
    document.getElementById('s-total').textContent    = data.total.toLocaleString();
    document.getElementById('s-logins').textContent   = data.logins.toLocaleString();
    document.getElementById('s-changes').textContent  = data.changes.toLocaleString();
    document.getElementById('s-failures').textContent = data.failures.toLocaleString();

    const fa = document.getElementById('filterAction');
    fa.innerHTML = '<option value="">All actions</option>';
    data.actions.forEach(a => {
      const o = document.createElement('option'); o.value = a; o.textContent = a; fa.appendChild(o);
    });

    const fe = document.getElementById('filterEntity');
    fe.innerHTML = '<option value="">All entity types</option>';
    data.entity_types.forEach(e => {
      const o = document.createElement('option'); o.value = e; o.textContent = e; fe.appendChild(o);
    });
  } catch (e) { showToast('Could not load stats'); }
}

// ── Table ─────────────────────────────────────────────────────────────────────
async function loadLogs() {
  const tbody = document.getElementById('tbody');
  tbody.innerHTML = '<tr class="loading-row"><td colspan="8"><span class="spinner"></span> Loading…</td></tr>';

  const params = new URLSearchParams({
    page:        state.page,
    per_page:    state.perPage,
    ...(state.search  && { search:      state.search }),
    ...(state.action  && { audit_action: state.action }),
    ...(state.entity  && { entity_type: state.entity }),
    ...dateRange(state.date),
  });

  try {
    const { data, meta } = await apiFetch(`/?action=audit-logs-data&${params}`);
    renderTable(data);
    renderPagination(meta);
  } catch (e) {
    tbody.innerHTML = '<tr class="loading-row"><td colspan="8">Failed to load entries.</td></tr>';
    showToast('Error loading audit log');
  }
}

function renderTable(rows) {
  const tbody = document.getElementById('tbody');
  if (!rows.length) {
    tbody.innerHTML = '<tr class="loading-row"><td colspan="8">No entries match your filters.</td></tr>';
    return;
  }
  tbody.innerHTML = rows.map(r => `
    <tr data-id="${r.id}">
      <td title="${r.created_at}" style="font-family:monospace;font-size:12px">${fmtDate(r.created_at)}</td>
      <td title="${r.user_email ?? ''}">${esc(r.user_name)}</td>
      <td><span class="badge ${badgeClass(r.action)}">${esc(r.action.replace(/_/g,' '))}</span></td>
      <td><span class="entity-tag">${esc(r.entity_type ?? '—')}</span></td>
      <td style="font-family:monospace;font-size:12px" title="${r.entity_id ?? ''}">${r.entity_id ? r.entity_id.slice(0,20)+'…' : '—'}</td>
      <td style="font-family:monospace;font-size:12px">${esc(r.ip_address ?? '—')}</td>
      <td style="color:${r.has_changes ? 'var(--info)' : 'var(--muted)'}; font-size:12px;">${r.has_changes ? 'Yes' : '—'}</td>
      <td><button class="btn btn-sm" onclick="loadDetail('${r.id}')">View</button></td>
    </tr>
  `).join('');

  tbody.querySelectorAll('tr').forEach(tr => {
    tr.addEventListener('click', e => {
      if (e.target.tagName === 'BUTTON') return;
      loadDetail(tr.dataset.id);
    });
  });
}

function renderPagination(meta) {
  document.getElementById('pageInfo').textContent =
    meta.total === 0 ? 'No results'
    : `Showing ${meta.from}–${meta.to} of ${meta.total.toLocaleString()} entries`;

  const total = meta.last_page;
  const cur   = meta.current_page;
  let pages   = [];

  if (total <= 7) { for (let i=1; i<=total; i++) pages.push(i); }
  else {
    pages = [1];
    if (cur > 3) pages.push('…');
    for (let i=Math.max(2,cur-1); i<=Math.min(total-1,cur+1); i++) pages.push(i);
    if (cur < total-2) pages.push('…');
    pages.push(total);
  }

  document.getElementById('pageBtns').innerHTML =
    `<button class="page-btn" id="prev" ${cur<=1?'disabled':''}>&#8249;</button>` +
    pages.map(p => p==='…'
      ? `<span style="padding:0 4px;line-height:30px;color:var(--muted)">…</span>`
      : `<button class="page-btn ${p===cur?'active':''}" data-p="${p}">${p}</button>`
    ).join('') +
    `<button class="page-btn" id="next" ${cur>=total?'disabled':''}>&#8250;</button>`;

  document.getElementById('prev')?.addEventListener('click', () => { state.page--; loadLogs(); });
  document.getElementById('next')?.addEventListener('click', () => { state.page++; loadLogs(); });
  document.querySelectorAll('[data-p]').forEach(b =>
    b.addEventListener('click', () => { state.page = +b.dataset.p; loadLogs(); })
  );
}

// ── Detail panel ──────────────────────────────────────────────────────────────
async function loadDetail(id) {
  document.querySelectorAll('tbody tr').forEach(r => r.classList.toggle('selected', r.dataset.id === id));
  const panel = document.getElementById('detailPanel');
  panel.className = 'detail-panel open';
  document.getElementById('detailContent').innerHTML = '<span class="spinner"></span> Loading…';
  panel.scrollIntoView({ behavior: 'smooth', block: 'nearest' });

  try {
    const { data: r } = await apiFetch(`/?action=audit-logs-show&id=${encodeURIComponent(id)}`);
    document.getElementById('detailContent').innerHTML = `
      <div class="detail-grid">
        ${field('Log ID',       id)}
        ${field('Timestamp',    fmtDate(r.created_at))}
        ${field('User',         r.user_name + (r.user_email ? ` (${r.user_email})` : ''))}
        ${field('User ID',      r.user_id ?? '—')}
        ${field('Action',       `<span class="badge ${badgeClass(r.action)}">${esc(r.action.replace(/_/g,' '))}</span>`)}
        ${field('Entity type',  r.entity_type ?? '—')}
        ${field('Entity ID',    r.entity_id ?? '—')}
        ${field('IP address',   r.ip_address ?? '—')}
        ${field('User agent',   r.user_agent ?? '—', true)}
        ${field('Metadata',     r.metadata ? JSON.stringify(r.metadata, null, 2) : '—', true)}
      </div>
      ${r.old_values || r.new_values ? `
        <div style="margin-top:12px;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.05em;color:var(--muted);">Changes</div>
        <div class="diff-row">
          <div class="diff-box diff-old"><div class="diff-label">Before</div>${JSON.stringify(r.old_values, null, 2)}</div>
          <div class="diff-box diff-new"><div class="diff-label">After</div>${JSON.stringify(r.new_values, null, 2)}</div>
        </div>` : ''}
    `;
  } catch (e) {
    document.getElementById('detailContent').innerHTML = 'Failed to load entry.';
  }
}

function field(label, value, full = false) {
  return `<div class="detail-field ${full ? 'detail-full' : ''}">
    <div class="detail-key">${label}</div>
    <div class="detail-val">${value}</div>
  </div>`;
}

// ── Export ────────────────────────────────────────────────────────────────────
document.getElementById('btnExport').addEventListener('click', () => {
  const params = new URLSearchParams({
    ...(state.action && { audit_action: state.action }),
    ...(state.entity && { entity_type: state.entity }),
    ...dateRange(state.date),
  });
  window.location.href = `${BASE_URL}/?action=audit-logs-export&${params}`;
});

// ── Filter / search events ─────────────────────────────────────────────────────
document.getElementById('search').addEventListener('input', e => {
  clearTimeout(debounceTimer);
  debounceTimer = setTimeout(() => { state.search = e.target.value; state.page = 1; loadLogs(); }, 350);
});

['filterAction', 'filterEntity', 'filterDate'].forEach(id => {
  document.getElementById(id).addEventListener('change', e => {
    if (id === 'filterAction') state.action = e.target.value;
    if (id === 'filterEntity') state.entity = e.target.value;
    if (id === 'filterDate')   state.date   = e.target.value;
    state.page = 1;
    loadLogs();
  });
});

document.getElementById('perPage').addEventListener('change', e => {
  state.perPage = +e.target.value; state.page = 1; loadLogs();
});

document.getElementById('btnReset').addEventListener('click', () => {
  state = { page:1, perPage: +document.getElementById('perPage').value, search:'', action:'', entity:'', date:'' };
  document.getElementById('search').value = '';
  document.getElementById('filterAction').value = '';
  document.getElementById('filterEntity').value = '';
  document.getElementById('filterDate').value = '';
  loadLogs();
});

document.getElementById('btnCloseDetail').addEventListener('click', () => {
  document.getElementById('detailPanel').className = 'detail-panel';
  document.querySelectorAll('tbody tr').forEach(r => r.classList.remove('selected'));
});

// ── Helpers ───────────────────────────────────────────────────────────────────
function fmtDate(iso) {
  const d = new Date(iso);
  return d.toLocaleDateString('en-GB',{day:'2-digit',month:'short',year:'2-digit'})
       + ' ' + d.toLocaleTimeString('en-GB',{hour:'2-digit',minute:'2-digit',second:'2-digit'});
}

function esc(s) {
  return String(s ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
}

function badgeClass(action) {
  if (action.includes('login_success') || action.includes('logout')) return 'badge-login';
  if (action.includes('login_failed') || action.includes('failed') || action.includes('error')) return 'badge-failed';
  if (action.includes('delete'))   return 'badge-delete';
  if (action.includes('create'))   return 'badge-create';
  if (action.includes('update') || action.includes('change')) return 'badge-update';
  if (action.includes('view') || action.includes('export'))   return 'badge-view';
  return 'badge-other';
}

function dateRange(filter) {
  const now = new Date();
  if (filter === 'today') {
    const d = now.toISOString().slice(0,10);
    return { date_from: d, date_to: d };
  }
  if (filter === 'week') {
    const from = new Date(now); from.setDate(now.getDate()-7);
    return { date_from: from.toISOString().slice(0,10), date_to: now.toISOString().slice(0,10) };
  }
  if (filter === 'month') {
    const from = new Date(now); from.setMonth(now.getMonth()-1);
    return { date_from: from.toISOString().slice(0,10), date_to: now.toISOString().slice(0,10) };
  }
  return {};
}

function showToast(msg) {
  const t = document.getElementById('toast');
  t.textContent = msg; t.className = 'toast show';
  setTimeout(() => t.className = 'toast', 3000);
}

// ── Init ──────────────────────────────────────────────────────────────────────
loadStats();
loadLogs();
</script>
