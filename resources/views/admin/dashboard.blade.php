<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>ARVA — Admin panelis</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;0,9..40,800;0,9..40,900&display=swap');

        :root {
            --ink:        #072536;
            --ink-soft:   rgba(7,37,54,.65);
            --ink-muted:  rgba(7,37,54,.4);
            --teal:       #13c4ab;
            --teal-dim:   rgba(19,196,171,.12);
            --teal-dark:  #06616d;
            --pink:       #de7388;
            --purple:     #97276b;
            --purple-dim: rgba(151,39,107,.08);
            --bg:         #ffffff;
            --bg-soft:    #f7fbfc;
            --bg-card:    #ffffff;
            --border:     rgba(7,37,54,.1);
            --shadow:     0 8px 32px rgba(7,37,54,.1);
            --shadow-lg:  0 20px 50px rgba(7,37,54,.14);
            --radius:     16px;
            --radius-sm:  10px;
            --sidebar-w:  240px;
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--bg-soft);
            color: var(--ink);
            min-height: 100vh;
            display: flex;
        }

        /* ── SIDEBAR ─────────────────────────────────────── */
        .sidebar {
            width: var(--sidebar-w);
            background: var(--bg-card);
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0; left: 0; bottom: 0;
            z-index: 100;
            box-shadow: 4px 0 24px rgba(7,37,54,.06);
        }

        .sidebar-brand {
            padding: 24px 20px 20px;
            border-bottom: 1px solid var(--border);
        }

        .sidebar-brand img {
            height: 44px;
            width: auto;
        }

        .sidebar-label {
            display: block;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: .12em;
            text-transform: uppercase;
            color: var(--ink-muted);
            margin: 20px 20px 8px;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 20px;
            font-size: 14px;
            font-weight: 500;
            color: var(--ink-soft);
            cursor: pointer;
            border-left: 3px solid transparent;
            border-radius: 0;
            transition: background .15s, color .15s, border-color .15s;
            text-decoration: none;
        }

        .nav-item svg { width: 17px; height: 17px; flex-shrink: 0; }

        .nav-item:hover {
            color: var(--ink);
            background: var(--bg-soft);
        }

        .nav-item.active {
            color: var(--teal-dark);
            border-left-color: var(--teal);
            background: var(--teal-dim);
            font-weight: 600;
        }

        .sidebar-footer {
            margin-top: auto;
            padding: 16px 20px;
            border-top: 1px solid var(--border);
        }

        .sidebar-footer a {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            color: var(--ink-soft);
            text-decoration: none;
            font-weight: 500;
            transition: color .15s;
        }

        .sidebar-footer a:hover { color: var(--ink); }

        /* ── MAIN ────────────────────────────────────────── */
        .main {
            margin-left: var(--sidebar-w);
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
            font-weight: 800;
            color: var(--ink);
        }

        .page-title span { color: var(--teal-dark); }

        /* ── STATS ───────────────────────────────────────── */
        .stats {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 14px;
            margin-bottom: 28px;
        }

        .stat-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 20px;
            box-shadow: var(--shadow);
            position: relative;
            overflow: hidden;
        }

        .stat-card::after {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 3px;
        }

        .stat-card:nth-child(1)::after { background: linear-gradient(90deg, var(--teal), var(--teal-dark)); }
        .stat-card:nth-child(2)::after { background: linear-gradient(90deg, var(--purple), var(--pink)); }
        .stat-card:nth-child(3)::after { background: linear-gradient(90deg, var(--pink), var(--purple)); }
        .stat-card:nth-child(4)::after { background: linear-gradient(90deg, var(--ink), var(--teal-dark)); }

        .stat-label {
            font-size: 11px;
            font-weight: 700;
            color: var(--ink-muted);
            text-transform: uppercase;
            letter-spacing: .08em;
            margin-bottom: 10px;
        }

        .stat-value {
            font-size: 32px;
            font-weight: 800;
            color: var(--ink);
            line-height: 1;
        }

        /* ── PANEL ───────────────────────────────────────── */
        .panel {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            overflow: hidden;
        }

        .panel-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 18px 22px;
            border-bottom: 1px solid var(--border);
            background: var(--bg-soft);
        }

        .panel-title {
            font-size: 15px;
            font-weight: 700;
            color: var(--ink);
        }

        /* ── BUTTONS ─────────────────────────────────────── */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 9px 18px;
            border-radius: var(--radius-sm);
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            border: none;
            transition: all .15s;
            font-family: 'DM Sans', sans-serif;
            white-space: nowrap;
        }

        .btn-primary {
            background: linear-gradient(120deg, var(--ink), var(--purple));
            color: #fff;
        }
        .btn-primary:hover { filter: brightness(1.1); transform: translateY(-1px); }

        .btn-teal {
            background: linear-gradient(120deg, var(--teal-dark), var(--teal));
            color: #fff;
        }
        .btn-teal:hover { filter: brightness(1.08); transform: translateY(-1px); }

        .btn-ghost {
            background: transparent;
            color: var(--ink-soft);
            border: 1px solid var(--border);
        }
        .btn-ghost:hover { border-color: var(--ink-soft); color: var(--ink); background: var(--bg-soft); }

        .btn-danger {
            background: transparent;
            color: #c0392b;
            border: 1px solid rgba(192,57,43,.3);
        }
        .btn-danger:hover { background: #c0392b; color: #fff; border-color: #c0392b; }

        .btn-sm { padding: 6px 12px; font-size: 12px; }

        /* ── TABLE ───────────────────────────────────────── */
        table { width: 100%; border-collapse: collapse; }

        thead tr { border-bottom: 1px solid var(--border); }

        th {
            text-align: left;
            padding: 12px 20px;
            font-size: 11px;
            font-weight: 700;
            color: var(--ink-muted);
            text-transform: uppercase;
            letter-spacing: .08em;
        }

        td {
            padding: 14px 20px;
            font-size: 14px;
            border-bottom: 1px solid var(--border);
            color: var(--ink);
        }

        tr:last-child td { border-bottom: none; }
        tbody tr:hover td { background: var(--bg-soft); }

        .badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 700;
        }

        .badge-teal  { background: var(--teal-dim);    color: var(--teal-dark); }
        .badge-purple{ background: var(--purple-dim);  color: var(--purple); }
        .badge-green { background: rgba(39,174,96,.1); color: #1e8449; }
        .badge-red   { background: rgba(192,57,43,.1); color: #c0392b; }

        .actions { display: flex; gap: 6px; }

        /* ── SEARCH BAR ──────────────────────────────────── */
        .search-wrap { position: relative; }

        .search-wrap svg {
            position: absolute;
            left: 11px;
            top: 50%;
            transform: translateY(-50%);
            width: 16px;
            height: 16px;
            color: var(--ink-muted);
            pointer-events: none;
        }

        .search-wrap input {
            padding-left: 34px !important;
        }

        /* ── FORMS ───────────────────────────────────────── */
        .form-group { margin-bottom: 16px; }

        .form-label {
            display: block;
            font-size: 11px;
            font-weight: 700;
            color: var(--ink-muted);
            text-transform: uppercase;
            letter-spacing: .08em;
            margin-bottom: 6px;
        }

        input[type=text], input[type=number], input[type=email], select, textarea {
            width: 100%;
            background: var(--bg-soft);
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            padding: 10px 14px;
            color: var(--ink);
            font-size: 14px;
            font-family: 'DM Sans', sans-serif;
            transition: border-color .15s, box-shadow .15s;
        }

        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: var(--teal);
            box-shadow: 0 0 0 3px rgba(19,196,171,.15);
        }

        select option { background: #fff; }

        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
        .form-row-4 { display: grid; grid-template-columns: 1fr 1fr 100px 90px 36px; gap: 8px; align-items: end; margin-bottom: 8px; }

        /* ── MODAL ───────────────────────────────────────── */
        .modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(7,37,54,.4);
            backdrop-filter: blur(4px);
            z-index: 200;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .modal-overlay.open { display: flex; }

        .modal {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 0;
            width: 100%;
            max-width: 560px;
            max-height: 90vh;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            box-shadow: var(--shadow-lg);
        }

        .modal-head {
            padding: 22px 24px 18px;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: var(--bg-soft);
        }

        .modal-head h3 {
            font-size: 17px;
            font-weight: 800;
        }

        .modal-close {
            background: none;
            border: none;
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: var(--ink-muted);
            font-size: 18px;
            transition: background .15s, color .15s;
        }

        .modal-close:hover { background: var(--border); color: var(--ink); }

        .modal-body {
            overflow-y: auto;
            padding: 22px 24px;
            flex: 1;
        }

        .modal-footer {
            padding: 16px 24px;
            border-top: 1px solid var(--border);
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            background: var(--bg-soft);
        }

        /* ── TABS ────────────────────────────────────────── */
        .tab-bar {
            display: flex;
            gap: 0;
            border-bottom: 1px solid var(--border);
            margin: 0 -24px 20px;
            padding: 0 24px;
        }

        .tab {
            padding: 10px 16px;
            font-size: 13px;
            font-weight: 600;
            color: var(--ink-muted);
            cursor: pointer;
            border-bottom: 2px solid transparent;
            margin-bottom: -1px;
            transition: color .15s, border-color .15s;
            user-select: none;
        }

        .tab:hover { color: var(--ink); }
        .tab.active { color: var(--teal-dark); border-bottom-color: var(--teal); }

        .tab-pane { display: none; }
        .tab-pane.active { display: block; }

        /* ── VARIANTS ────────────────────────────────────── */
        .variant-header {
            display: grid;
            grid-template-columns: 1fr 1fr 100px 90px 36px;
            gap: 8px;
            margin-bottom: 6px;
        }

        .variant-header span {
            font-size: 11px;
            font-weight: 700;
            color: var(--ink-muted);
            text-transform: uppercase;
            letter-spacing: .06em;
        }

        .remove-variant {
            background: none;
            border: 1px solid var(--border);
            border-radius: 8px;
            color: #c0392b;
            width: 36px;
            height: 38px;
            cursor: pointer;
            font-size: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background .15s;
            flex-shrink: 0;
        }

        .remove-variant:hover { background: #c0392b; color: #fff; border-color: #c0392b; }

        /* ── SECTION VISIBILITY ──────────────────────────── */
        .section { display: none; }
        .section.active { display: block; }

        /* ── IMG ─────────────────────────────────────────── */
        .img-thumb {
            width: 46px;
            height: 46px;
            border-radius: 10px;
            object-fit: cover;
            border: 1px solid var(--border);
        }

        .img-placeholder {
            width: 46px;
            height: 46px;
            border-radius: 10px;
            background: var(--bg-soft);
            border: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }

        /* ── TOAST ───────────────────────────────────────── */
        .toast {
            position: fixed;
            bottom: 24px;
            right: 24px;
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 14px 20px;
            font-size: 14px;
            font-weight: 600;
            z-index: 9999;
            transform: translateY(80px);
            opacity: 0;
            transition: all .3s cubic-bezier(.175,.885,.32,1.275);
            display: flex;
            align-items: center;
            gap: 10px;
            box-shadow: var(--shadow-lg);
            max-width: 340px;
        }

        .toast.show { transform: translateY(0); opacity: 1; }
        .toast.success { border-left: 4px solid var(--teal); color: var(--teal-dark); }
        .toast.error   { border-left: 4px solid #c0392b;    color: #c0392b; }

        /* ── EMPTY STATE ─────────────────────────────────── */
        .empty-cell {
            text-align: center;
            color: var(--ink-muted);
            padding: 40px 20px !important;
            font-size: 14px;
        }

        @media (max-width: 1100px) { .stats { grid-template-columns: repeat(2,1fr); } }
    </style>
</head>
<body>

<!-- ─── SIDEBAR ────────────────────────────────────────── -->
<aside class="sidebar">
    <div class="sidebar-brand">
        <img src="/bildites/Logo_Arva.png" alt="ARVA" />
    </div>

    <span class="sidebar-label">Pārvaldība</span>

    <nav>
        <a class="nav-item active" data-section="products" onclick="showSection('products',this)" href="#">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M20 7H4a2 2 0 00-2 2v10a2 2 0 002 2h16a2 2 0 002-2V9a2 2 0 00-2-2z"/>
                <path d="M16 7V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v2"/>
            </svg>
            Produkti
        </a>
        <a class="nav-item" data-section="orders" onclick="showSection('orders',this)" href="#">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/>
                <rect x="9" y="3" width="6" height="4" rx="1"/>
            </svg>
            Pasūtījumi
        </a>
        <a class="nav-item" data-section="users" onclick="showSection('users',this)" href="#">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/>
                <circle cx="9" cy="7" r="4"/>
                <path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/>
            </svg>
            Lietotāji
        </a>
    </nav>

    <div class="sidebar-footer">
        <a href="/">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                <polyline points="9,22 9,12 15,12 15,22"/>
            </svg>
            Uz mājas lapu
        </a>
    </div>
</aside>

<!-- ─── MAIN ────────────────────────────────────────────── -->
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

    <!-- PRODUCTS -->
    <div class="section active" id="section-products">
        <div class="panel">
            <div class="panel-header">
                <div class="panel-title">Visi produkti</div>
                <div style="display:flex;gap:10px;align-items:center;">
                    <div class="search-wrap">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/>
                        </svg>
                        <input type="text" id="productSearch" placeholder="Meklēt produktu..." style="max-width:210px;" oninput="filterProducts()" />
                    </div>
                    <button class="btn btn-teal" onclick="openProductModal()">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                        </svg>
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
                        <th>Dzimums</th>
                        <th>Cena</th>
                        <th>Varianti</th>
                        <th>Darbības</th>
                    </tr>
                </thead>
                <tbody id="productsTable">
                    <tr><td colspan="7" class="empty-cell">Ielādē...</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- ORDERS -->
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
                        <th>Darbības</th>
                    </tr>
                </thead>
                <tbody id="ordersTable">
                    <tr><td colspan="6" class="empty-cell">Ielādē...</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- USERS -->
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
                        <th>Loma</th>
                        <th>Reģistrācija</th>
                        <th>Pasūtījumi</th>
                        <th>Darbības</th>
                    </tr>
                </thead>
                <tbody id="usersTable">
                    <tr><td colspan="7" class="empty-cell">Ielādē...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</main>

<!-- ─── PRODUCT MODAL ───────────────────────────────────── -->
<div class="modal-overlay" id="productModal" onclick="handleOverlayClick(event)">
    <div class="modal">
        <div class="modal-head">
            <h3 id="modalTitle">Jauns produkts</h3>
            <button class="modal-close" onclick="closeProductModal()">✕</button>
        </div>

        <div class="modal-body">
            <div class="tab-bar">
                <div class="tab active" onclick="switchTab('info', this)">Informācija</div>
                <div class="tab" onclick="switchTab('variants', this)">Varianti</div>
            </div>

            <!-- INFO -->
            <div class="tab-pane active" id="tab-info">
                <div class="form-group">
                    <label class="form-label">Nosaukums</label>
                    <input type="text" id="prodName" placeholder="Piem. Men Linen Shirt" />
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Cena (€)</label>
                        <input type="number" id="prodPrice" placeholder="39.99" step="0.01" min="0" />
                    </div>
                    <div class="form-group">
                        <label class="form-label">Kategorija</label>
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
                <div class="form-group">
                    <label class="form-label">Dzimums</label>
                    <select id="prodGender">
                        <option value="men">Vīrieši</option>
                        <option value="women">Sievietes</option>
                    </select>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Attēls — vīrieši</label>
                        <input type="file" id="prodImageMen" accept="image/*" />
                        <img id="prodImageMenPreview" class="img-thumb" style="display:none;margin-top:8px;" />
                    </div>
                    <div class="form-group">
                        <label class="form-label">Attēls — sievietes</label>
                        <input type="file" id="prodImageWomen" accept="image/*" />
                        <img id="prodImageWomenPreview" class="img-thumb" style="display:none;margin-top:8px;" />
                    </div>
                </div>
            </div>

            <!-- VARIANTS -->
            <div class="tab-pane" id="tab-variants">
                <div class="variant-header">
                    <span>Krāsa</span>
                    <span>Izmērs</span>
                    <span>Cena (€)</span>
                    <span>Stock</span>
                    <span></span>
                </div>
                <div id="variantsList"></div>
                <button class="btn btn-ghost" style="width:100%;justify-content:center;margin-top:10px;" onclick="addVariantRow()">
                    + Pievienot variantu
                </button>
            </div>
        </div>

        <div class="modal-footer">
            <button class="btn btn-ghost" onclick="closeProductModal()">Atcelt</button>
            <button class="btn btn-primary" onclick="saveProduct()">Saglabāt</button>
        </div>
    </div>
</div>

<!-- ─── CONFIRM MODAL ───────────────────────────────────── -->
<div class="modal-overlay" id="confirmModal">
    <div class="modal" style="max-width:420px;">
        <div class="modal-head">
            <h3 id="confirmTitle">Apstiprini darbību</h3>
            <button class="modal-close" onclick="closeConfirmModal(false)">✕</button>
        </div>
        <div class="modal-body">
            <p id="confirmMessage" style="color:var(--ink-soft);font-size:14px;line-height:1.6;"></p>
        </div>
        <div class="modal-footer">
            <button class="btn btn-ghost" onclick="closeConfirmModal(false)">Atcelt</button>
            <button class="btn btn-primary" id="confirmAcceptBtn" onclick="closeConfirmModal(true)">Apstiprināt</button>
        </div>
    </div>
</div>

<!-- ─── TOAST ───────────────────────────────────────────── -->
<div class="toast" id="toast"></div>

<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').content;
let editingProductId = null;
let allProducts = [];

// ─── NAVIGATION ────────────────────────────────────────
function showSection(name, el) {
    event.preventDefault();
    document.querySelectorAll('.section').forEach(s => s.classList.remove('active'));
    document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('active'));
    document.getElementById('section-' + name).classList.add('active');
    el.classList.add('active');

    const titles = { products: 'Produkti', orders: 'Pasūtījumi', users: 'Lietotāji' };
    document.getElementById('pageTitle').textContent = titles[name];

    if (name === 'orders') loadOrders();
    if (name === 'users')  loadUsers();
}

// ─── TABS ───────────────────────────────────────────────
function switchTab(name, el) {
    const modal = el.closest('.modal-body, .modal');
    modal.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
    modal.querySelectorAll('.tab-pane').forEach(t => t.classList.remove('active'));
    modal.querySelector('#tab-' + name).classList.add('active');
    el.classList.add('active');
}

// ─── TOAST ──────────────────────────────────────────────
function showToast(msg, type = 'success') {
    const t = document.getElementById('toast');
    t.textContent = (type === 'success' ? '✓  ' : '✕  ') + msg;
    t.className = 'toast show ' + type;
    setTimeout(() => t.classList.remove('show'), 3200);
}

// ─── CONFIRM MODAL ──────────────────────────────────────
let confirmResolve = null;

function confirmAction(message, { title = 'Apstiprini darbību', acceptLabel = 'Apstiprināt', danger = false } = {}) {
    document.getElementById('confirmTitle').textContent = title;
    document.getElementById('confirmMessage').textContent = message;
    const acceptBtn = document.getElementById('confirmAcceptBtn');
    acceptBtn.textContent = acceptLabel;
    acceptBtn.className = 'btn ' + (danger ? 'btn-danger' : 'btn-primary');
    document.getElementById('confirmModal').classList.add('open');
    return new Promise((resolve) => { confirmResolve = resolve; });
}

function closeConfirmModal(result) {
    document.getElementById('confirmModal').classList.remove('open');
    if (confirmResolve) {
        confirmResolve(result);
        confirmResolve = null;
    }
}

// ─── STATS ──────────────────────────────────────────────
async function loadStats() {
    try {
        const r = await fetch('/admin/api/stats');
        if (!r.ok) return;
        const d = await r.json();
        document.getElementById('statProducts').textContent = d.products ?? '—';
        document.getElementById('statVariants').textContent = d.variants ?? '—';
        document.getElementById('statOrders').textContent   = d.orders   ?? '—';
        document.getElementById('statUsers').textContent    = d.users    ?? '—';
    } catch(e) {}
}

// ─── PRODUCTS ───────────────────────────────────────────
async function loadProducts() {
    try {
        const r = await fetch('/admin/api/products');
        if (!r.ok) throw new Error();
        allProducts = await r.json();
        renderProducts(allProducts);
    } catch(e) {
        document.getElementById('productsTable').innerHTML =
            '<tr><td colspan="7" class="empty-cell">Neizdevās ielādēt produktus.</td></tr>';
    }
}

function renderProducts(products) {
    const tbody = document.getElementById('productsTable');
    if (!products.length) {
        tbody.innerHTML = '<tr><td colspan="7" class="empty-cell">Nav produktu</td></tr>';
        return;
    }
    tbody.innerHTML = products.map(p => `
        <tr>
            <td>
                ${p.image_men
                    ? `<img src="${p.image_men}" class="img-thumb" />`
                    : `<div class="img-placeholder">👕</div>`}
            </td>
            <td><strong>${escHtml(p.name)}</strong></td>
            <td><span class="badge badge-purple">${escHtml(p.category)}</span></td>
            <td>${{men:'<span class="badge badge-teal">Vīrieši</span>',women:'<span class="badge" style="background:rgba(222,115,136,.12);color:#97276b">Sievietes</span>',unisex:'<span class="badge" style="background:var(--bg-soft);color:var(--ink-soft)">Unisex</span>'}[p.gender] ?? escHtml(p.gender ?? '')}</td>
            <td>€${Number(p.price).toFixed(2)}</td>
            <td><span class="badge badge-teal">${p.variants_count ?? 0} varianti</span></td>
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

// ─── PRODUCT MODAL ──────────────────────────────────────
function resetModal() {
    document.querySelectorAll('#productModal .tab').forEach((t,i) => {
        t.classList.toggle('active', i === 0);
    });
    document.querySelectorAll('#productModal .tab-pane').forEach((p,i) => {
        p.classList.toggle('active', i === 0);
    });
}

function openProductModal() {
    editingProductId = null;
    document.getElementById('modalTitle').textContent = 'Jauns produkts';
    document.getElementById('prodName').value       = '';
    document.getElementById('prodPrice').value      = '';
    document.getElementById('prodCategory').value   = 'tshirt';
    document.getElementById('prodGender').value     = 'men';
    document.getElementById('prodImageMen').value   = '';
    document.getElementById('prodImageWomen').value = '';
    document.getElementById('prodImageMenPreview').style.display   = 'none';
    document.getElementById('prodImageWomenPreview').style.display = 'none';
    document.getElementById('variantsList').innerHTML = '';
    addVariantRow();
    resetModal();
    document.getElementById('productModal').classList.add('open');
}

async function openEditModal(id) {
    editingProductId = id;
    document.getElementById('modalTitle').textContent = 'Rediģēt produktu';

    try {
        const r = await fetch(`/admin/api/products/${id}`);
        if (!r.ok) throw new Error();
        const p = await r.json();

        document.getElementById('prodName').value       = p.name;
        document.getElementById('prodPrice').value      = p.price;
        document.getElementById('prodCategory').value   = p.category;
        document.getElementById('prodGender').value     = p.gender ?? 'men';
        document.getElementById('prodImageMen').value   = '';
        document.getElementById('prodImageWomen').value = '';

        const menPreview   = document.getElementById('prodImageMenPreview');
        const womenPreview = document.getElementById('prodImageWomenPreview');
        if (p.image_men)   { menPreview.src   = p.image_men;   menPreview.style.display   = 'block'; } else { menPreview.style.display   = 'none'; }
        if (p.image_women) { womenPreview.src = p.image_women; womenPreview.style.display = 'block'; } else { womenPreview.style.display = 'none'; }

        const vl = document.getElementById('variantsList');
        vl.innerHTML = '';
        if (p.variants && p.variants.length) {
            p.variants.forEach(v => addVariantRow(v));
        } else {
            addVariantRow();
        }

        resetModal();
        document.getElementById('productModal').classList.add('open');
    } catch(e) {
        showToast('Neizdevās ielādēt produktu!', 'error');
    }
}

function closeProductModal() {
    document.getElementById('productModal').classList.remove('open');
}

function handleOverlayClick(e) {
    if (e.target === document.getElementById('productModal')) closeProductModal();
}

async function saveProduct() {
    const name      = document.getElementById('prodName').value.trim();
    const price     = document.getElementById('prodPrice').value;
    const category  = document.getElementById('prodCategory').value;
    const gender    = document.getElementById('prodGender').value;
    const imageMenFile   = document.getElementById('prodImageMen').files[0];
    const imageWomenFile = document.getElementById('prodImageWomen').files[0];

    if (!name)  { showToast('Ievadi nosaukumu!', 'error'); return; }
    if (!price) { showToast('Ievadi cenu!', 'error'); return; }

    const variants = [];
    document.querySelectorAll('#variantsList .variant-entry').forEach(row => {
        const color = row.querySelector('.v-color').value.trim();
        const size  = row.querySelector('.v-size').value.trim();
        const vprice = row.querySelector('.v-price').value;
        const stock  = row.querySelector('.v-stock').value;
        const vid    = row.dataset.vid;
        if (color && size) {
            variants.push({ id: vid || null, color, size, price: vprice || null, stock: stock || 0 });
        }
    });

    const formData = new FormData();
    formData.append('name', name);
    formData.append('price', price);
    formData.append('category', category);
    formData.append('gender', gender);
    if (imageMenFile)   formData.append('image_men', imageMenFile);
    if (imageWomenFile) formData.append('image_women', imageWomenFile);
    variants.forEach((v, i) => {
        if (v.id) formData.append(`variants[${i}][id]`, v.id);
        formData.append(`variants[${i}][color]`, v.color);
        formData.append(`variants[${i}][size]`, v.size);
        if (v.price !== null) formData.append(`variants[${i}][price]`, v.price);
        formData.append(`variants[${i}][stock]`, v.stock);
    });
    if (editingProductId) formData.append('_method', 'PUT');

    const url = editingProductId ? `/admin/api/products/${editingProductId}` : '/admin/api/products';

    try {
        const r = await fetch(url, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': CSRF },
            body: formData,
        });
        if (r.ok) {
            showToast(editingProductId ? 'Produkts atjaunināts!' : 'Produkts izveidots!');
            closeProductModal();
            loadProducts();
            loadStats();
        } else {
            const err = await r.json().catch(() => ({}));
            showToast(err.message ?? 'Saglabāšanas kļūda!', 'error');
        }
    } catch(e) {
        showToast('Savienojuma kļūda!', 'error');
    }
}

async function deleteProduct(id) {
    const ok = await confirmAction('Dzēst šo produktu un visus tā variantus?', {
        title: 'Dzēst produktu',
        acceptLabel: 'Dzēst',
        danger: true,
    });
    if (!ok) return;
    try {
        const r = await fetch(`/admin/api/products/${id}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': CSRF },
        });
        if (r.ok) {
            showToast('Produkts dzēsts!');
            loadProducts();
            loadStats();
        } else {
            showToast('Kļūda dzēšot!', 'error');
        }
    } catch(e) {
        showToast('Savienojuma kļūda!', 'error');
    }
}

// ─── VARIANTS ───────────────────────────────────────────
function addVariantRow(v = null) {
    const div = document.createElement('div');
    div.className = 'variant-entry';
    div.dataset.vid = v?.id ?? '';
    div.innerHTML = `
        <div class="form-row-4">
            <input class="v-color" type="text" placeholder="Black" value="${escHtml(v?.color ?? '')}" />
            <input class="v-size"  type="text" placeholder="M"     value="${escHtml(v?.size  ?? '')}" />
            <input class="v-price" type="number" placeholder="—" step="0.01" min="0" value="${v?.price ?? ''}" />
            <input class="v-stock" type="number" placeholder="0"  min="0"           value="${v?.stock ?? 0}" />
            <button class="remove-variant" type="button" onclick="this.closest('.variant-entry').remove()" title="Dzēst">✕</button>
        </div>
    `;
    document.getElementById('variantsList').appendChild(div);
}

// ─── ORDERS ─────────────────────────────────────────────
const ORDER_STATUSES = {
    processing: { label: 'Apstrādē',  cls: 'badge-teal' },
    shipped:    { label: 'Nosūtīts',  cls: 'badge-purple' },
    delivered:  { label: 'Piegādāts', cls: 'badge-green' },
    cancelled:  { label: 'Atcelts',   cls: 'badge-red' },
};

async function loadOrders() {
    const tbody = document.getElementById('ordersTable');
    tbody.innerHTML = '<tr><td colspan="6" class="empty-cell">Ielādē...</td></tr>';
    try {
        const r = await fetch('/admin/api/orders');
        if (!r.ok) throw new Error();
        const orders = await r.json();
        if (!orders.length) {
            tbody.innerHTML = '<tr><td colspan="6" class="empty-cell">Nav pasūtījumu</td></tr>';
            return;
        }
        tbody.innerHTML = orders.map(o => {
            const st = ORDER_STATUSES[o.status] ?? { label: o.status, cls: 'badge-green' };
            const opts = Object.entries(ORDER_STATUSES).map(([val, {label}]) =>
                `<option value="${val}" ${o.status === val ? 'selected' : ''}>${label}</option>`
            ).join('');
            return `
            <tr>
                <td><strong style="color:var(--teal-dark)">#${o.id}</strong></td>
                <td>${escHtml(o.user?.name ?? '—')}</td>
                <td><strong>€${Number(o.total ?? 0).toFixed(2)}</strong></td>
                <td><span class="badge ${st.cls}">${st.label}</span></td>
                <td style="color:var(--ink-soft);font-size:13px;">${new Date(o.created_at).toLocaleDateString('lv-LV')}</td>
                <td>
                    <div style="display:flex;gap:6px;align-items:center;">
                        <select class="status-select" style="padding:5px 8px;font-size:12px;border-radius:8px;border:1px solid var(--border);" onchange="updateOrderStatus(${o.id}, this.value, this)">
                            ${opts}
                        </select>
                    </div>
                </td>
            </tr>`;
        }).join('');
    } catch(e) {
        tbody.innerHTML = '<tr><td colspan="6" class="empty-cell">Neizdevās ielādēt pasūtījumus.</td></tr>';
    }
}

async function updateOrderStatus(id, status, selectEl) {
    try {
        const r = await fetch(`/admin/api/orders/${id}`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body: JSON.stringify({ status }),
        });
        if (r.ok) {
            showToast('Statuss atjaunināts!');
            loadOrders();
        } else {
            showToast('Kļūda!', 'error');
            loadOrders();
        }
    } catch(e) {
        showToast('Savienojuma kļūda!', 'error');
    }
}

// ─── USERS ──────────────────────────────────────────────
async function loadUsers() {
    const tbody = document.getElementById('usersTable');
    tbody.innerHTML = '<tr><td colspan="7" class="empty-cell">Ielādē...</td></tr>';
    try {
        const r = await fetch('/admin/api/users');
        if (!r.ok) throw new Error();
        const users = await r.json();
        if (!users.length) {
            tbody.innerHTML = '<tr><td colspan="7" class="empty-cell">Nav lietotāju</td></tr>';
            return;
        }
        tbody.innerHTML = users.map((u, i) => `
            <tr>
                <td style="color:var(--ink-muted)">${i + 1}</td>
                <td><strong>${escHtml(u.name)}</strong></td>
                <td style="color:var(--ink-soft)">${escHtml(u.email)}</td>
                <td>${u.is_admin
                    ? '<span class="badge badge-purple">Admins</span>'
                    : '<span class="badge" style="background:var(--bg-soft);color:var(--ink-muted)">Lietotājs</span>'}</td>
                <td style="color:var(--ink-soft);font-size:13px;">${new Date(u.created_at).toLocaleDateString('lv-LV')}</td>
                <td><span class="badge badge-teal">${u.orders_count ?? 0}</span></td>
                <td>
                    <button class="btn btn-ghost btn-sm" onclick="toggleUserRole(${u.id}, ${u.is_admin ? 1 : 0})">
                        ${u.is_admin ? 'Noņemt admin' : 'Padarīt admin'}
                    </button>
                </td>
            </tr>
        `).join('');
    } catch(e) {
        tbody.innerHTML = '<tr><td colspan="7" class="empty-cell">Neizdevās ielādēt lietotājus.</td></tr>';
    }
}

async function toggleUserRole(id, currentlyAdmin) {
    const action = currentlyAdmin ? 'noņemt admin tiesības' : 'piešķirt admin tiesības';
    const ok = await confirmAction(`Vai tiešām vēlies ${action} šim lietotājam?`, {
        title: currentlyAdmin ? 'Noņemt admin tiesības' : 'Piešķirt admin tiesības',
        acceptLabel: currentlyAdmin ? 'Noņemt' : 'Piešķirt',
        danger: currentlyAdmin,
    });
    if (!ok) return;
    try {
        const r = await fetch(`/admin/api/users/${id}/role`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
        });
        if (r.ok) {
            showToast(currentlyAdmin ? 'Admin tiesības noņemtas!' : 'Lietotājs kļuvis par adminu!');
            loadUsers();
        } else {
            const d = await r.json().catch(() => ({}));
            showToast(d.error ?? 'Kļūda!', 'error');
        }
    } catch(e) {
        showToast('Savienojuma kļūda!', 'error');
    }
}

// ─── HELPERS ────────────────────────────────────────────
function escHtml(str) {
    return String(str ?? '')
        .replace(/&/g,'&amp;')
        .replace(/</g,'&lt;')
        .replace(/>/g,'&gt;')
        .replace(/"/g,'&quot;');
}

// ─── INIT ───────────────────────────────────────────────
loadStats();
loadProducts();
</script>

</body>
</html>
