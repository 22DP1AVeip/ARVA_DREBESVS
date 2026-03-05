<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>ARVA Admin</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&family=DM+Mono:wght@400;500&display=swap');

        :root {
            --bg: #0f0f0f;
            --surface: #1a1a1a;
            --surface2: #222222;
            --border: #2e2e2e;
            --accent: #e8ff47;
            --accent-dim: rgba(232,255,71,0.12);
            --text: #f0f0f0;
            --text-muted: #777;
            --danger: #ff4f4f;
            --success: #4fff9a;
            --radius: 10px;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            display: flex;
        }

        /* SIDEBAR */
        .sidebar {
            width: 220px;
            background: var(--surface);
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            padding: 24px 0;
            position: fixed;
            top: 0; left: 0; bottom: 0;
            z-index: 100;
        }

        .logo {
            padding: 0 20px 24px;
            border-bottom: 1px solid var(--border);
            margin-bottom: 16px;
        }

        .logo span {
            font-size: 20px;
            font-weight: 700;
            letter-spacing: 0.06em;
            color: var(--accent);
        }

        .logo small {
            display: block;
            font-size: 11px;
            color: var(--text-muted);
            font-family: 'DM Mono', monospace;
            margin-top: 2px;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 11px 20px;
            font-size: 14px;
            font-weight: 500;
            color: var(--text-muted);
            cursor: pointer;
            border-radius: 0;
            transition: all 0.15s;
            border-left: 3px solid transparent;
            text-decoration: none;
        }

        .nav-item:hover { color: var(--text); background: var(--surface2); }
        .nav-item.active { color: var(--accent); border-left-color: var(--accent); background: var(--accent-dim); }
        .nav-item svg { width: 17px; height: 17px; flex-shrink: 0; }

        .sidebar-footer {
            margin-top: auto;
            padding: 16px 20px;
            border-top: 1px solid var(--border);
        }

        .sidebar-footer a {
            font-size: 13px;
            color: var(--text-muted);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* MAIN */
        .main {
            margin-left: 220px;
            flex: 1;
            padding: 32px;
            min-height: 100vh;
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 28px;
        }

        .page-title {
            font-size: 22px;
            font-weight: 700;
        }

        .page-title span {
            color: var(--accent);
        }

        /* STATS */
        .stats {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 14px;
            margin-bottom: 28px;
        }

        .stat-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 18px;
        }

        .stat-label {
            font-size: 12px;
            color: var(--text-muted);
            font-family: 'DM Mono', monospace;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 8px;
        }

        .stat-value {
            font-size: 28px;
            font-weight: 700;
        }

        /* PANEL */
        .panel {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            overflow: hidden;
        }

        .panel-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 16px 20px;
            border-bottom: 1px solid var(--border);
        }

        .panel-title {
            font-size: 15px;
            font-weight: 600;
        }

        /* BUTTONS */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 9px 16px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            border: none;
            transition: all 0.15s;
            font-family: 'DM Sans', sans-serif;
        }

        .btn-primary { background: var(--accent); color: #0f0f0f; }
        .btn-primary:hover { filter: brightness(0.9); }
        .btn-ghost { background: transparent; color: var(--text-muted); border: 1px solid var(--border); }
        .btn-ghost:hover { color: var(--text); border-color: #555; }
        .btn-danger { background: transparent; color: var(--danger); border: 1px solid var(--danger); }
        .btn-danger:hover { background: var(--danger); color: #fff; }
        .btn-sm { padding: 6px 11px; font-size: 12px; }

        /* TABLE */
        table { width: 100%; border-collapse: collapse; }

        thead tr { border-bottom: 1px solid var(--border); }

        th {
            text-align: left;
            padding: 12px 16px;
            font-size: 11px;
            font-weight: 600;
            color: var(--text-muted);
            font-family: 'DM Mono', monospace;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        td {
            padding: 13px 16px;
            font-size: 14px;
            border-bottom: 1px solid var(--border);
        }

        tr:last-child td { border-bottom: none; }
        tr:hover td { background: var(--surface2); }

        .badge {
            display: inline-block;
            padding: 3px 9px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 600;
            font-family: 'DM Mono', monospace;
        }

        .badge-active { background: rgba(79,255,154,0.15); color: var(--success); }
        .badge-inactive { background: rgba(255,79,79,0.12); color: var(--danger); }

        /* MODAL */
        .modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.7);
            z-index: 200;
            align-items: center;
            justify-content: center;
        }

        .modal-overlay.open { display: flex; }

        .modal {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 28px;
            width: 100%;
            max-width: 540px;
            max-height: 90vh;
            overflow-y: auto;
        }

        .modal-title {
            font-size: 17px;
            font-weight: 700;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-close {
            background: none;
            border: none;
            color: var(--text-muted);
            font-size: 20px;
            cursor: pointer;
            line-height: 1;
        }

        /* FORMS */
        .form-group { margin-bottom: 16px; }

        label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 6px;
            font-family: 'DM Mono', monospace;
        }

        input, select, textarea {
            width: 100%;
            background: var(--surface2);
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 10px 12px;
            color: var(--text);
            font-size: 14px;
            font-family: 'DM Sans', sans-serif;
            transition: border-color 0.15s;
        }

        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: var(--accent);
        }

        select option { background: var(--surface2); }

        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }

        /* TABS */
        .tab-bar {
            display: flex;
            gap: 0;
            border-bottom: 1px solid var(--border);
            padding: 0 20px;
        }

        .tab {
            padding: 12px 16px;
            font-size: 13px;
            font-weight: 600;
            color: var(--text-muted);
            cursor: pointer;
            border-bottom: 2px solid transparent;
            margin-bottom: -1px;
            transition: all 0.15s;
        }

        .tab:hover { color: var(--text); }
        .tab.active { color: var(--accent); border-bottom-color: var(--accent); }

        .tab-content { display: none; padding: 20px; }
        .tab-content.active { display: block; }

        /* VARIANTS SECTION */
        .variant-row {
            display: grid;
            grid-template-columns: 1fr 1fr 90px 90px auto;
            gap: 8px;
            align-items: end;
            margin-bottom: 10px;
        }

        .variant-row input, .variant-row select {
            padding: 8px 10px;
            font-size: 13px;
        }

        .remove-variant {
            background: none;
            border: 1px solid var(--border);
            border-radius: 8px;
            color: var(--danger);
            width: 36px;
            height: 36px;
            cursor: pointer;
            font-size: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .remove-variant:hover { background: var(--danger); color: #fff; }

        /* TOAST */
        .toast {
            position: fixed;
            bottom: 24px;
            right: 24px;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 14px 20px;
            font-size: 14px;
            font-weight: 500;
            z-index: 999;
            transform: translateY(80px);
            opacity: 0;
            transition: all 0.3s cubic-bezier(.175,.885,.32,1.275);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .toast.show { transform: translateY(0); opacity: 1; }
        .toast.success { border-color: var(--success); color: var(--success); }
        .toast.error { border-color: var(--danger); color: var(--danger); }

        /* SECTION VIEWS */
        .section { display: none; }
        .section.active { display: block; }

        /* SEARCH */
        .search-bar {
            display: flex;
            gap: 10px;
            margin-bottom: 0;
        }

        .search-bar input {
            max-width: 260px;
        }

        /* IMG PREVIEW */
        .img-preview {
            width: 48px;
            height: 48px;
            border-radius: 8px;
            object-fit: cover;
            background: var(--surface2);
            border: 1px solid var(--border);
        }

        .img-placeholder {
            width: 48px;
            height: 48px;
            border-radius: 8px;
            background: var(--surface2);
            border: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-muted);
            font-size: 20px;
        }

        .actions { display: flex; gap: 6px; }

        @media (max-width: 1100px) { .stats { grid-template-columns: repeat(2,1fr); } }
    </style>
</head>
<body>

<!-- SIDEBAR -->
<aside class="sidebar">
    <div class="logo">
        <span>ARVA</span>
        <small>admin panel</small>
    </div>

    <nav>
        <a class="nav-item active" onclick="showSection('products')" href="#">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 7H4a2 2 0 00-2 2v10a2 2 0 002 2h16a2 2 0 002-2V9a2 2 0 00-2-2z"/><path d="M16 7V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v2"/></svg>
            Produkti
        </a>
        <a class="nav-item" onclick="showSection('orders')" href="#">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="1"/></svg>
            Pasūtījumi
        </a>
        <a class="nav-item" onclick="showSection('users')" href="#">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
            Lietotāji
        </a>
    </nav>

    <div class="sidebar-footer">
        <a href="/">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9,22 9,12 15,12 15,22"/></svg>
            Uz mājas lapu
        </a>
    </div>
</aside>

<!-- MAIN CONTENT -->
<main class="main">
    <div class="topbar">
        <div class="page-title" id="pageTitle">Produkti</div>
    </div>

    <!-- STATS -->
    <div class="stats">
        <div class="stat-card">
            <div class="stat-label">Produkti</div>
            <div class="stat-value" id="statProducts">—</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Varianti</div>
            <div class="stat-value" id="statVariants">—</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Pasūtījumi</div>
            <div class="stat-value" id="statOrders">—</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Lietotāji</div>
            <div class="stat-value" id="statUsers">—</div>
        </div>
    </div>

    <!-- PRODUCTS SECTION -->
    <div class="section active" id="section-products">
        <div class="panel">
            <div class="panel-header">
                <div class="panel-title">Visi produkti</div>
                <div style="display:flex;gap:10px;align-items:center;">
                    <input type="text" placeholder="Meklēt..." id="productSearch" style="max-width:200px;" oninput="filterProducts()" />
                    <button class="btn btn-primary" onclick="openProductModal()">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                        Jauns produkts
                    </button>
                </div>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Attēls</th>
                        <th>Nosaukums</th>
                        <th>Kategorija</th>
                        <th>Cena</th>
                        <th>Varianti</th>
                        <th>Darbības</th>
                    </tr>
                </thead>
                <tbody id="productsTable">
                    <tr><td colspan="6" style="text-align:center;color:var(--text-muted);padding:32px;">Ielādē...</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- ORDERS SECTION -->
    <div class="section" id="section-orders">
        <div class="panel">
            <div class="panel-header">
                <div class="panel-title">Pasūtījumi</div>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Lietotājs</th>
                        <th>Summa</th>
                        <th>Statuss</th>
                        <th>Datums</th>
                    </tr>
                </thead>
                <tbody id="ordersTable">
                    <tr><td colspan="5" style="text-align:center;color:var(--text-muted);padding:32px;">Ielādē...</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- USERS SECTION -->
    <div class="section" id="section-users">
        <div class="panel">
            <div class="panel-header">
                <div class="panel-title">Lietotāji</div>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Vārds</th>
                        <th>E-pasts</th>
                        <th>Reģistrācija</th>
                        <th>Pasūtījumi</th>
                    </tr>
                </thead>
                <tbody id="usersTable">
                    <tr><td colspan="5" style="text-align:center;color:var(--text-muted);padding:32px;">Ielādē...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</main>

<!-- PRODUCT MODAL -->
<div class="modal-overlay" id="productModal">
    <div class="modal">
        <div class="modal-title">
            <span id="modalTitle">Jauns produkts</span>
            <button class="modal-close" onclick="closeProductModal()">✕</button>
        </div>

        <div class="tab-bar">
            <div class="tab active" onclick="switchTab('info')">Informācija</div>
            <div class="tab" onclick="switchTab('variants')" id="variantsTabBtn">Varianti</div>
        </div>

        <!-- INFO TAB -->
        <div class="tab-content active" id="tab-info">
            <div class="form-group">
                <label>Nosaukums</label>
                <input type="text" id="prodName" placeholder="Piem. Men Jeans 1" />
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Cena (€)</label>
                    <input type="number" id="prodPrice" placeholder="39.99" step="0.01" min="0" />
                </div>
                <div class="form-group">
                    <label>Kategorija</label>
                    <select id="prodCategory">
                        <option value="tshirt">T-krekls</option>
                        <option value="jeans">Džinsi</option>
                        <option value="jacket">Jaka</option>
                        <option value="hat">Cepure</option>
                        <option value="shoes">Apavi</option>
                        <option value="other">Cits</option>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Attēls (vīrieši) — URL</label>
                    <input type="text" id="prodImageMen" placeholder="https://..." />
                </div>
                <div class="form-group">
                    <label>Attēls (sievietes) — URL</label>
                    <input type="text" id="prodImageWomen" placeholder="https://..." />
                </div>
            </div>
        </div>

        <!-- VARIANTS TAB -->
        <div class="tab-content" id="tab-variants">
            <div id="variantsList"></div>
            <button class="btn btn-ghost" style="width:100%;justify-content:center;margin-top:8px;" onclick="addVariantRow()">
                + Pievienot variantu
            </button>
        </div>

        <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:20px;padding-top:16px;border-top:1px solid var(--border);">
            <button class="btn btn-ghost" onclick="closeProductModal()">Atcelt</button>
            <button class="btn btn-primary" onclick="saveProduct()">Saglabāt</button>
        </div>
    </div>
</div>

<!-- TOAST -->
<div class="toast" id="toast"></div>

<script>
    const CSRF = document.querySelector('meta[name="csrf-token"]').content;
    let editingProductId = null;
    let allProducts = [];

    // ─── NAVIGATION ───────────────────────────────────────
    function showSection(name) {
        document.querySelectorAll('.section').forEach(s => s.classList.remove('active'));
        document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('active'));
        document.getElementById('section-' + name).classList.add('active');
        document.querySelectorAll('.nav-item')[['products','orders','users'].indexOf(name)].classList.add('active');

        const titles = { products: 'Produkti', orders: 'Pasūtījumi', users: 'Lietotāji' };
        document.getElementById('pageTitle').textContent = titles[name];

        if (name === 'orders') loadOrders();
        if (name === 'users') loadUsers();
    }

    // ─── TABS ─────────────────────────────────────────────
    function switchTab(name) {
        document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
        document.querySelectorAll('.tab-content').forEach(t => t.classList.remove('active'));
        document.getElementById('tab-' + name).classList.add('active');
        event.target.classList.add('active');
    }

    // ─── TOAST ────────────────────────────────────────────
    function showToast(msg, type = 'success') {
        const t = document.getElementById('toast');
        t.textContent = (type === 'success' ? '✓ ' : '✕ ') + msg;
        t.className = 'toast show ' + type;
        setTimeout(() => t.classList.remove('show'), 3000);
    }

    // ─── LOAD STATS ───────────────────────────────────────
    async function loadStats() {
        try {
            const r = await fetch('/admin/api/stats');
            const d = await r.json();
            document.getElementById('statProducts').textContent = d.products ?? '—';
            document.getElementById('statVariants').textContent = d.variants ?? '—';
            document.getElementById('statOrders').textContent = d.orders ?? '—';
            document.getElementById('statUsers').textContent = d.users ?? '—';
        } catch(e) {}
    }

    // ─── PRODUCTS ─────────────────────────────────────────
    async function loadProducts() {
        const r = await fetch('/admin/api/products');
        allProducts = await r.json();
        renderProducts(allProducts);
    }

    function renderProducts(products) {
        const tbody = document.getElementById('productsTable');
        if (!products.length) {
            tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;color:var(--text-muted);padding:32px;">Nav produktu</td></tr>';
            return;
        }
        tbody.innerHTML = products.map(p => `
            <tr>
                <td>
                    ${p.image_men
                        ? `<img src="${p.image_men}" class="img-preview" />`
                        : `<div class="img-placeholder">👕</div>`}
                </td>
                <td><strong>${p.name}</strong></td>
                <td>${p.category}</td>
                <td>€${Number(p.price).toFixed(2)}</td>
                <td><span class="badge badge-active">${p.variants_count ?? 0} varianti</span></td>
                <td>
                    <div class="actions">
                        <button class="btn btn-ghost btn-sm" onclick="openEditModal(${p.id})">Rediģēt</button>
                        <button class="btn btn-danger btn-sm" onclick="deleteProduct(${p.id})">Dzēst</button>
                    </div>
                </td>
            </tr>
        `).join('');
    }

    function filterProducts() {
        const q = document.getElementById('productSearch').value.toLowerCase();
        renderProducts(allProducts.filter(p =>
            p.name.toLowerCase().includes(q) || p.category.toLowerCase().includes(q)
        ));
    }

    // ─── PRODUCT MODAL ────────────────────────────────────
    function openProductModal() {
        editingProductId = null;
        document.getElementById('modalTitle').textContent = 'Jauns produkts';
        document.getElementById('prodName').value = '';
        document.getElementById('prodPrice').value = '';
        document.getElementById('prodCategory').value = 'tshirt';
        document.getElementById('prodImageMen').value = '';
        document.getElementById('prodImageWomen').value = '';
        document.getElementById('variantsList').innerHTML = '';
        addVariantRow();
        document.getElementById('productModal').classList.add('open');

        // reset tabs
        document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
        document.querySelectorAll('.tab-content').forEach(t => t.classList.remove('active'));
        document.querySelectorAll('.tab')[0].classList.add('active');
        document.getElementById('tab-info').classList.add('active');
    }

    async function openEditModal(id) {
        editingProductId = id;
        document.getElementById('modalTitle').textContent = 'Rediģēt produktu';

        const r = await fetch(`/admin/api/products/${id}`);
        const p = await r.json();

        document.getElementById('prodName').value = p.name;
        document.getElementById('prodPrice').value = p.price;
        document.getElementById('prodCategory').value = p.category;
        document.getElementById('prodImageMen').value = p.image_men ?? '';
        document.getElementById('prodImageWomen').value = p.image_women ?? '';

        // load variants
        const vl = document.getElementById('variantsList');
        vl.innerHTML = '';
        if (p.variants && p.variants.length) {
            p.variants.forEach(v => addVariantRow(v));
        } else {
            addVariantRow();
        }

        document.getElementById('productModal').classList.add('open');

        document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
        document.querySelectorAll('.tab-content').forEach(t => t.classList.remove('active'));
        document.querySelectorAll('.tab')[0].classList.add('active');
        document.getElementById('tab-info').classList.add('active');
    }

    function closeProductModal() {
        document.getElementById('productModal').classList.remove('open');
    }

    async function saveProduct() {
        const name = document.getElementById('prodName').value.trim();
        const price = document.getElementById('prodPrice').value;
        const category = document.getElementById('prodCategory').value;
        const image_men = document.getElementById('prodImageMen').value.trim() || null;
        const image_women = document.getElementById('prodImageWomen').value.trim() || null;

        if (!name || !price) { showToast('Aizpildi nosaukumu un cenu!', 'error'); return; }

        // collect variants
        const variants = [];
        document.querySelectorAll('.variant-entry').forEach(row => {
            const color = row.querySelector('.v-color').value.trim();
            const size = row.querySelector('.v-size').value.trim();
            const vprice = row.querySelector('.v-price').value;
            const stock = row.querySelector('.v-stock').value;
            const vid = row.dataset.vid;
            if (color && size) {
                variants.push({ id: vid || null, color, size, price: vprice || null, stock: stock || 0 });
            }
        });

        const payload = { name, price, category, image_men, image_women, variants };
        const url = editingProductId ? `/admin/api/products/${editingProductId}` : '/admin/api/products';
        const method = editingProductId ? 'PUT' : 'POST';

        try {
            const r = await fetch(url, {
                method,
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
                body: JSON.stringify(payload)
            });
            if (r.ok) {
                showToast(editingProductId ? 'Produkts atjaunināts!' : 'Produkts izveidots!');
                closeProductModal();
                loadProducts();
                loadStats();
            } else {
                const err = await r.json();
                showToast(err.message ?? 'Kļūda!', 'error');
            }
        } catch(e) {
            showToast('Savienojuma kļūda!', 'error');
        }
    }

    async function deleteProduct(id) {
        if (!confirm('Dzēst šo produktu?')) return;
        const r = await fetch(`/admin/api/products/${id}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': CSRF }
        });
        if (r.ok) {
            showToast('Produkts dzēsts!');
            loadProducts();
            loadStats();
        } else {
            showToast('Kļūda dzēšot!', 'error');
        }
    }

    // ─── VARIANTS ─────────────────────────────────────────
    let variantCounter = 0;
    function addVariantRow(v = null) {
        variantCounter++;
        const id = variantCounter;
        const div = document.createElement('div');
        div.className = 'variant-entry';
        div.dataset.vid = v?.id ?? '';
        div.innerHTML = `
            <div style="display:grid;grid-template-columns:1fr 1fr 90px 90px 36px;gap:8px;align-items:end;margin-bottom:10px;">
                <div>
                    ${id === 1 ? '<label>Krāsa</label>' : ''}
                    <input class="v-color" type="text" placeholder="Black" value="${v?.color ?? ''}" />
                </div>
                <div>
                    ${id === 1 ? '<label>Izmērs</label>' : ''}
                    <input class="v-size" type="text" placeholder="M" value="${v?.size ?? ''}" />
                </div>
                <div>
                    ${id === 1 ? '<label>Cena (€)</label>' : ''}
                    <input class="v-price" type="number" placeholder="—" step="0.01" min="0" value="${v?.price ?? ''}" />
                </div>
                <div>
                    ${id === 1 ? '<label>Stock</label>' : ''}
                    <input class="v-stock" type="number" placeholder="0" min="0" value="${v?.stock ?? 0}" />
                </div>
                <div style="padding-top:${id === 1 ? '22' : '0'}px">
                    <button class="remove-variant" onclick="this.closest('.variant-entry').remove()">✕</button>
                </div>
            </div>
        `;
        document.getElementById('variantsList').appendChild(div);
    }

    // ─── ORDERS ───────────────────────────────────────────
    async function loadOrders() {
        try {
            const r = await fetch('/admin/api/orders');
            const orders = await r.json();
            const tbody = document.getElementById('ordersTable');
            if (!orders.length) {
                tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;color:var(--text-muted);padding:32px;">Nav pasūtījumu</td></tr>';
                return;
            }
            tbody.innerHTML = orders.map(o => `
                <tr>
                    <td><code style="font-family:DM Mono,monospace;color:var(--accent)">#${o.id}</code></td>
                    <td>${o.user?.name ?? '—'}</td>
                    <td>€${Number(o.total ?? 0).toFixed(2)}</td>
                    <td><span class="badge badge-active">${o.status ?? 'jauns'}</span></td>
                    <td style="color:var(--text-muted);font-size:13px;">${new Date(o.created_at).toLocaleDateString('lv')}</td>
                </tr>
            `).join('');
        } catch(e) {}
    }

    // ─── USERS ────────────────────────────────────────────
    async function loadUsers() {
        try {
            const r = await fetch('/admin/api/users');
            const users = await r.json();
            const tbody = document.getElementById('usersTable');
            if (!users.length) {
                tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;color:var(--text-muted);padding:32px;">Nav lietotāju</td></tr>';
                return;
            }
            tbody.innerHTML = users.map((u, i) => `
                <tr>
                    <td style="color:var(--text-muted)">${i+1}</td>
                    <td>${u.name}</td>
                    <td style="color:var(--text-muted)">${u.email}</td>
                    <td style="color:var(--text-muted);font-size:13px;">${new Date(u.created_at).toLocaleDateString('lv')}</td>
                    <td>${u.orders_count ?? 0}</td>
                </tr>
            `).join('');
        } catch(e) {}
    }

    // ─── INIT ─────────────────────────────────────────────
    loadStats();
    loadProducts();
</script>

</body>
</html>