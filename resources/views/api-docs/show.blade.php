<!DOCTYPE html>
<html lang="bs">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="robots" content="noindex, nofollow">
<title>MojTurnir API — Referenca za mobilnu app</title>
<link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
<style>
  :root {
    --bg: #0b0e14;
    --surface: #12161d;
    --surface-2: #191f28;
    --surface-3: #1f2630;
    --border: #262e39;
    --text: #e7ebef;
    --text-muted: #8fa0ab;
    --text-faint: #5c6b76;
    --accent: #57f1db;
    --accent-ink: #003731;
    --accent-soft: rgba(87, 241, 219, 0.12);
    --get: #5b9cf6;
    --post: #4fd88a;
    --put: #f5b054;
    --delete: #f2607a;
    --mono: ui-monospace, "SF Mono", "Cascadia Code", "JetBrains Mono", Menlo, Consolas, monospace;
    --sans: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
    --radius: 10px;
  }
  @media (prefers-color-scheme: light) {
    :root {
      --bg: #f5f8f8; --surface: #ffffff; --surface-2: #eef2f3; --surface-3: #e4eaec;
      --border: #d8e0e2; --text: #101820; --text-muted: #526069; --text-faint: #8996a0;
      --accent: #0f9c88; --accent-ink: #ffffff; --accent-soft: rgba(15, 156, 136, 0.1);
    }
  }
  :root[data-theme="dark"] {
    --bg: #0b0e14; --surface: #12161d; --surface-2: #191f28; --surface-3: #1f2630;
    --border: #262e39; --text: #e7ebef; --text-muted: #8fa0ab; --text-faint: #5c6b76;
    --accent: #57f1db; --accent-ink: #003731; --accent-soft: rgba(87, 241, 219, 0.12);
  }
  :root[data-theme="light"] {
    --bg: #f5f8f8; --surface: #ffffff; --surface-2: #eef2f3; --surface-3: #e4eaec;
    --border: #d8e0e2; --text: #101820; --text-muted: #526069; --text-faint: #8996a0;
    --accent: #0f9c88; --accent-ink: #ffffff; --accent-soft: rgba(15, 156, 136, 0.1);
  }

  * { box-sizing: border-box; }
  html, body { margin: 0; padding: 0; }
  body {
    background: var(--bg);
    color: var(--text);
    font-family: var(--sans);
    font-size: 15px;
    line-height: 1.55;
    -webkit-font-smoothing: antialiased;
  }
  a { color: inherit; }
  ::selection { background: var(--accent-soft); }

  .app { display: flex; min-height: 100vh; }

  .sidebar {
    width: 272px;
    flex-shrink: 0;
    background: var(--surface);
    border-right: 1px solid var(--border);
    position: sticky;
    top: 0;
    height: 100vh;
    overflow-y: auto;
    display: flex;
    flex-direction: column;
  }
  .sidebar-head { padding: 22px 20px 16px; border-bottom: 1px solid var(--border); }
  .sidebar-head .kicker {
    font-family: var(--mono); font-size: 11px; letter-spacing: 0.08em;
    text-transform: uppercase; color: var(--accent); margin: 0 0 4px;
  }
  .sidebar-head h1 { font-family: var(--mono); font-size: 19px; font-weight: 700; letter-spacing: -0.01em; margin: 0; }
  .search-wrap { padding: 14px 16px; border-bottom: 1px solid var(--border); position: relative; }
  .search-wrap input {
    width: 100%; background: var(--surface-2); border: 1px solid var(--border); color: var(--text);
    border-radius: 8px; padding: 8px 10px 8px 30px; font-size: 13px; font-family: var(--sans); outline: none;
  }
  .search-wrap input:focus { border-color: var(--accent); }
  .search-icon { position: absolute; left: 26px; top: 50%; transform: translateY(-50%); color: var(--text-faint); pointer-events: none; font-size: 13px; }

  nav.taglist { padding: 8px 10px 24px; flex: 1; }
  .tag-group { margin-bottom: 2px; }
  .tag-btn {
    width: 100%; display: flex; align-items: center; justify-content: space-between; gap: 8px;
    background: none; border: none; color: var(--text); font-family: var(--sans); font-size: 13.5px;
    font-weight: 600; padding: 8px 10px; border-radius: 7px; cursor: pointer; text-align: left; text-decoration: none;
  }
  .tag-btn:hover { background: var(--surface-2); }
  .tag-btn .count {
    font-family: var(--mono); font-size: 11px; color: var(--text-faint);
    background: var(--surface-3); border-radius: 20px; padding: 1px 7px;
  }
  .tag-eps { padding-left: 10px; }
  .tag-ep-link {
    display: flex; align-items: baseline; gap: 7px; padding: 5px 10px; border-radius: 6px;
    font-size: 12.5px; color: var(--text-muted); text-decoration: none; cursor: pointer;
  }
  .tag-ep-link:hover { color: var(--text); background: var(--surface-2); }
  .m-tag { font-family: var(--mono); font-size: 9.5px; font-weight: 700; width: 32px; flex-shrink: 0; }
  .m-tag.get { color: var(--get); } .m-tag.post { color: var(--post); }
  .m-tag.put { color: var(--put); } .m-tag.delete { color: var(--delete); }
  .tag-ep-link .path-frag { font-family: var(--mono); overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }

  main { flex: 1; min-width: 0; padding: 40px 48px 120px; max-width: 900px; }
  @media (max-width: 900px) {
    .sidebar { position: fixed; z-index: 40; transform: translateX(-100%); transition: transform 0.2s ease; }
    .sidebar.open { transform: translateX(0); }
    main { padding: 24px 18px 100px; max-width: 100%; }
    .menu-btn { display: flex !important; }
  }

  .menu-btn {
    display: none; align-items: center; gap: 8px; background: var(--surface); border: 1px solid var(--border);
    color: var(--text); border-radius: 8px; padding: 8px 12px; font-family: var(--sans); font-size: 13px;
    font-weight: 600; margin-bottom: 20px; cursor: pointer;
  }

  .quickref { background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius); padding: 22px 24px; margin-bottom: 40px; }
  .quickref h2 { font-family: var(--mono); font-size: 12px; text-transform: uppercase; letter-spacing: 0.08em; color: var(--accent); margin: 0 0 14px; }
  .qr-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(230px, 1fr)); gap: 18px 28px; }
  .qr-item dt { font-size: 12px; color: var(--text-muted); margin-bottom: 4px; font-weight: 600; }
  .qr-item dd { margin: 0; font-family: var(--mono); font-size: 13px; }
  .qr-item dd.wrap { font-family: var(--sans); line-height: 1.5; }
  .base-url-row { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
  .base-url {
    font-family: var(--mono); font-size: 12.5px; background: var(--surface-2); border: 1px solid var(--border);
    border-radius: 6px; padding: 5px 9px; display: inline-flex; align-items: center; gap: 8px;
  }
  .copy-btn {
    background: var(--accent-soft); border: 1px solid transparent; color: var(--accent); border-radius: 5px;
    padding: 3px 8px; font-family: var(--sans); font-size: 11px; font-weight: 700; cursor: pointer;
  }
  .copy-btn:hover { border-color: var(--accent); }

  h1.page-title { font-family: var(--mono); font-size: 26px; margin: 0 0 6px; text-wrap: balance; }
  .page-sub { color: var(--text-muted); margin: 0 0 36px; max-width: 62ch; }

  section.tag-section { margin-bottom: 46px; scroll-margin-top: 20px; }
  section.tag-section > h2 { font-family: var(--mono); font-size: 20px; margin: 0 0 4px; padding-bottom: 10px; border-bottom: 2px solid var(--border); }

  details.endpoint { background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius); margin-bottom: 10px; overflow: hidden; scroll-margin-top: 20px; }
  details.endpoint[open] { border-color: var(--accent); }
  details.endpoint summary { list-style: none; cursor: pointer; padding: 13px 16px; display: flex; align-items: center; gap: 12px; }
  details.endpoint summary::-webkit-details-marker { display: none; }
  details.endpoint summary .chev { color: var(--text-faint); font-size: 11px; transition: transform 0.15s ease; flex-shrink: 0; }
  details.endpoint[open] summary .chev { transform: rotate(90deg); }
  .method-pill { font-family: var(--mono); font-size: 11px; font-weight: 700; padding: 3px 9px; border-radius: 5px; flex-shrink: 0; min-width: 56px; text-align: center; }
  .method-pill.get { background: rgba(91,156,246,0.15); color: var(--get); }
  .method-pill.post { background: rgba(79,216,138,0.15); color: var(--post); }
  .method-pill.put { background: rgba(245,176,84,0.15); color: var(--put); }
  .method-pill.delete { background: rgba(242,96,122,0.15); color: var(--delete); }
  summary .ep-path { font-family: var(--mono); font-size: 13.5px; overflow-wrap: anywhere; }
  summary .ep-summary { color: var(--text-muted); font-size: 12.5px; margin-left: auto; padding-left: 12px; text-align: right; }
  .auth-badge { font-family: var(--mono); font-size: 9.5px; color: var(--text-faint); border: 1px solid var(--border); border-radius: 4px; padding: 1px 5px; flex-shrink: 0; }

  .ep-body { padding: 4px 18px 20px; border-top: 1px solid var(--border); }
  .ep-desc { font-size: 13.5px; color: var(--text-muted); margin: 10px 0 16px; white-space: pre-line; max-width: 68ch; }
  .ep-block h4 { font-family: var(--mono); font-size: 11px; text-transform: uppercase; letter-spacing: 0.06em; color: var(--text-faint); margin: 18px 0 8px; }
  table.params { width: 100%; border-collapse: collapse; font-size: 12.5px; }
  table.params th, table.params td { text-align: left; padding: 6px 10px 6px 0; border-bottom: 1px solid var(--border); vertical-align: top; }
  table.params th { color: var(--text-faint); font-weight: 600; font-size: 11px; text-transform: uppercase; letter-spacing: 0.04em; }
  table.params td.pname { font-family: var(--mono); color: var(--accent); white-space: nowrap; }
  table.params td.ptype { font-family: var(--mono); color: var(--text-muted); white-space: nowrap; }
  .req-star { color: var(--delete); margin-left: 3px; }

  .status-row { display: flex; align-items: baseline; gap: 8px; margin: 10px 0 4px; font-size: 12.5px; }
  .status-code { font-family: var(--mono); font-weight: 700; padding: 1px 7px; border-radius: 4px; font-size: 11px; }
  .status-2xx { background: rgba(79,216,138,0.15); color: var(--post); }
  .status-4xx { background: rgba(242,96,122,0.15); color: var(--delete); }
  .status-desc { color: var(--text-muted); }

  .empty-state { color: var(--text-faint); text-align: center; padding: 60px 20px; font-size: 14px; }
  footer.page-footer { margin-top: 60px; padding-top: 20px; border-top: 1px solid var(--border); color: var(--text-faint); font-size: 12px; }

  @media (prefers-reduced-motion: reduce) { * { transition: none !important; } }
</style>
</head>
<body>

<div class="app">
  <aside class="sidebar" id="sidebar">
    <div class="sidebar-head">
      <p class="kicker">API Reference</p>
      <h1>MojTurnir</h1>
    </div>
    <div class="search-wrap">
      <span class="search-icon">&#128269;</span>
      <input type="text" id="search" placeholder="Pretraži endpoint-e...">
    </div>
    <nav class="taglist" id="taglist"></nav>
  </aside>

  <main>
    <button class="menu-btn" id="menuBtn">&#9776; Endpoint-i</button>

    <h1 class="page-title">MojTurnir API</h1>
    <p class="page-sub">REST API referenca za mobilnu aplikaciju. Token autentikacija (Sanctum), JSON odgovori, jedna envelopa za sve.</p>

    <div class="quickref">
      <h2>Brzi pregled</h2>
      <dl class="qr-grid">
        <div class="qr-item">
          <dt>Base URL</dt>
          <dd class="wrap base-url-row" id="serverUrls"></dd>
        </div>
        <div class="qr-item">
          <dt>Autentikacija</dt>
          <dd class="wrap">Bearer token header:<br><span class="base-url">Authorization: Bearer &lbrace;token&rbrace;</span></dd>
        </div>
        <div class="qr-item">
          <dt>Format odgovora</dt>
          <dd class="wrap">&lbrace; success, data, message &rbrace; &mdash; osim 422 grešaka (Laravel standard: message + errors)</dd>
        </div>
        <div class="qr-item">
          <dt>Paginacija</dt>
          <dd class="wrap">Listing endpoint-i vraćaju <code>meta</code> uz <code>data</code>. <code>?per_page=</code> (max 100).</dd>
        </div>
        <div class="qr-item">
          <dt>Rate limit</dt>
          <dd class="wrap">6 pokušaja/min na login/register/google/forgot-password/reset-password</dd>
        </div>
        <div class="qr-item">
          <dt>Token expiry</dt>
          <dd class="wrap">90 dana &mdash; nema refresh flow, samo re-login na 401</dd>
        </div>
      </dl>
    </div>

    <div id="content"></div>

    <footer class="page-footer">Generisano live iz <code>openapi.yaml</code> na serveru.</footer>
  </main>
</div>

<script id="spec-data" type="application/json">{!! $specJson !!}</script>
<script>
(function () {
  const spec = JSON.parse(document.getElementById('spec-data').textContent);
  const paths = spec.paths || {};
  const tags = (spec.tags || []).map(t => t.name);

  const byTag = {};
  tags.forEach(t => byTag[t] = []);
  Object.keys(paths).forEach(path => {
    const methods = paths[path];
    Object.keys(methods).forEach(method => {
      if (!['get', 'post', 'put', 'patch', 'delete'].includes(method)) return;
      const op = methods[method];
      const tag = (op.tags && op.tags[0]) || 'Other';
      if (!byTag[tag]) byTag[tag] = [];
      byTag[tag].push({ path, method, op });
    });
  });

  function slug(s) { return s.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)/g, ''); }
  function escapeHtml(s) {
    return String(s).replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
  }

  function resolveSchema(schema) {
    if (!schema) return null;
    if (schema.$ref) {
      const parts = schema.$ref.replace('#/', '').split('/');
      let cur = spec;
      parts.forEach(p => cur = cur ? cur[p] : null);
      return cur;
    }
    return schema;
  }

  function flattenProps(schema, prefix, out) {
    schema = resolveSchema(schema);
    if (!schema) return;
    if (schema.allOf) {
      schema.allOf.forEach(s => flattenProps(s, prefix, out));
      return;
    }
    if (schema.properties) {
      const req = new Set(schema.required || []);
      Object.keys(schema.properties).forEach(key => {
        const propSchema = resolveSchema(schema.properties[key]);
        const type = propSchema && (propSchema.type || (propSchema.enum ? 'enum' : 'object'));
        out.push({
          name: prefix ? prefix + '.' + key : key,
          type: type + (propSchema && propSchema.enum ? ' (' + propSchema.enum.join('|') + ')' : '') + (propSchema && propSchema.format ? ', ' + propSchema.format : ''),
          required: req.has(key),
          description: propSchema && propSchema.description || ''
        });
      });
    }
  }

  function renderRequestBody(op) {
    const rb = op.requestBody;
    if (!rb) return '';
    const content = rb.content || {};
    const ct = Object.keys(content)[0];
    if (!ct) return '';
    const schema = content[ct].schema;
    const rows = [];
    flattenProps(schema, '', rows);
    if (!rows.length) return '';
    let html = '<div class="ep-block"><h4>Request body' + (ct !== 'application/json' ? ' &middot; ' + escapeHtml(ct) : '') + '</h4>';
    html += '<table class="params"><thead><tr><th>Polje</th><th>Tip</th><th></th></tr></thead><tbody>';
    rows.forEach(r => {
      html += '<tr><td class="pname">' + escapeHtml(r.name) + (r.required ? '<span class="req-star">*</span>' : '') + '</td>';
      html += '<td class="ptype">' + escapeHtml(r.type || '') + '</td>';
      html += '<td>' + escapeHtml(r.description || '') + '</td></tr>';
    });
    html += '</tbody></table></div>';
    return html;
  }

  function renderParams(op) {
    const params = (op.parameters || []).map(p => p.$ref ? resolveSchema(p) : p);
    if (!params.length) return '';
    let html = '<div class="ep-block"><h4>Parametri</h4><table class="params"><thead><tr><th>Naziv</th><th>U</th><th>Tip</th></tr></thead><tbody>';
    params.forEach(p => {
      const schema = resolveSchema(p.schema) || {};
      html += '<tr><td class="pname">' + escapeHtml(p.name) + (p.required ? '<span class="req-star">*</span>' : '') + '</td>';
      html += '<td class="ptype">' + escapeHtml(p.in || '') + '</td>';
      html += '<td class="ptype">' + escapeHtml(schema.type || '') + '</td></tr>';
    });
    html += '</tbody></table></div>';
    return html;
  }

  function renderResponses(op) {
    const responses = op.responses || {};
    let html = '<div class="ep-block"><h4>Odgovori</h4>';
    Object.keys(responses).forEach(code => {
      const r = responses[code];
      const cls = code.startsWith('2') ? 'status-2xx' : 'status-4xx';
      html += '<div class="status-row"><span class="status-code ' + cls + '">' + escapeHtml(code) + '</span><span class="status-desc">' + escapeHtml(r.description || '') + '</span></div>';
    });
    html += '</div>';
    return html;
  }

  function endpointId(path, method) { return 'ep-' + slug(method + '-' + path); }

  function renderEndpoint(path, method, op) {
    const id = endpointId(path, method);
    const isPublic = Array.isArray(op.security) && op.security.length === 0;
    let html = '<details class="endpoint" id="' + id + '" data-search="' + escapeHtml((method + ' ' + path + ' ' + (op.summary || '')).toLowerCase()) + '">';
    html += '<summary>';
    html += '<span class="chev">&#9656;</span>';
    html += '<span class="method-pill ' + method + '">' + method.toUpperCase() + '</span>';
    html += '<span class="ep-path">' + escapeHtml(path) + '</span>';
    if (!isPublic) html += '<span class="auth-badge">AUTH</span>';
    html += '<span class="ep-summary">' + escapeHtml(op.summary || '') + '</span>';
    html += '</summary>';
    html += '<div class="ep-body">';
    if (op.description) html += '<p class="ep-desc">' + escapeHtml(op.description).replace(/\n/g, '<br>') + '</p>';
    html += renderParams(op);
    html += renderRequestBody(op);
    html += renderResponses(op);
    html += '</div></details>';
    return html;
  }

  const taglistEl = document.getElementById('taglist');
  let sidebarHtml = '';
  tags.forEach(tag => {
    const eps = byTag[tag] || [];
    if (!eps.length) return;
    sidebarHtml += '<div class="tag-group">';
    sidebarHtml += '<a href="#tag-' + slug(tag) + '" class="tag-btn"><span>' + escapeHtml(tag) + '</span><span class="count">' + eps.length + '</span></a>';
    sidebarHtml += '<div class="tag-eps">';
    eps.forEach(e => {
      sidebarHtml += '<a class="tag-ep-link" href="#' + endpointId(e.path, e.method) + '" data-open="' + endpointId(e.path, e.method) + '">';
      sidebarHtml += '<span class="m-tag ' + e.method + '">' + e.method.toUpperCase() + '</span>';
      sidebarHtml += '<span class="path-frag">' + escapeHtml(e.path) + '</span>';
      sidebarHtml += '</a>';
    });
    sidebarHtml += '</div></div>';
  });
  taglistEl.innerHTML = sidebarHtml;

  const contentEl = document.getElementById('content');
  let contentHtml = '';
  tags.forEach(tag => {
    const eps = byTag[tag] || [];
    if (!eps.length) return;
    contentHtml += '<section class="tag-section" id="tag-' + slug(tag) + '">';
    contentHtml += '<h2>' + escapeHtml(tag) + '</h2>';
    eps.forEach(e => { contentHtml += renderEndpoint(e.path, e.method, e.op); });
    contentHtml += '</section>';
  });
  contentEl.innerHTML = contentHtml || '<p class="empty-state">Nema endpoint-a.</p>';

  document.querySelectorAll('[data-open]').forEach(link => {
    link.addEventListener('click', function () {
      const targetId = this.getAttribute('data-open');
      const el = document.getElementById(targetId);
      if (el) { el.open = true; }
      if (window.innerWidth <= 900) document.getElementById('sidebar').classList.remove('open');
    });
  });

  const serverEl = document.getElementById('serverUrls');
  (spec.servers || []).forEach(s => {
    const span = document.createElement('span');
    span.className = 'base-url';
    span.innerHTML = escapeHtml(s.url) + ' <button class="copy-btn" type="button">Copy</button>';
    span.querySelector('button').addEventListener('click', function () {
      navigator.clipboard.writeText(s.url).then(() => {
        this.textContent = 'Copied!';
        setTimeout(() => { this.textContent = 'Copy'; }, 1200);
      });
    });
    serverEl.appendChild(span);
  });

  const searchInput = document.getElementById('search');
  searchInput.addEventListener('input', function () {
    const q = this.value.trim().toLowerCase();
    document.querySelectorAll('.tag-ep-link').forEach(link => {
      const id = link.getAttribute('data-open');
      const epEl = document.getElementById(id);
      const matches = !q || (epEl && epEl.getAttribute('data-search').includes(q));
      link.style.display = matches ? '' : 'none';
    });
    document.querySelectorAll('.tag-group').forEach(group => {
      const anyVisible = Array.from(group.querySelectorAll('.tag-ep-link')).some(l => l.style.display !== 'none');
      group.style.display = anyVisible ? '' : 'none';
    });
    document.querySelectorAll('.endpoint').forEach(epEl => {
      const matches = !q || epEl.getAttribute('data-search').includes(q);
      epEl.style.display = matches ? '' : 'none';
    });
  });

  document.getElementById('menuBtn').addEventListener('click', function () {
    document.getElementById('sidebar').classList.toggle('open');
  });
})();
</script>
</body>
</html>
