<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel · Museo Pokémon</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;600;700&family=Inter:wght@300;400;500;600;700&family=Press+Start+2P&display=swap" rel="stylesheet">
    <style>
        /* ══════════════════════════════════════════════
           RESET & VARIABLES
        ══════════════════════════════════════════════ */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --gold:        #d4af37;
            --gold-light:  #f9e8a2;
            --gold-dark:   #997a15;
            --gold-glow:   rgba(212,175,55,.35);
            --bg:          #0a0c11;
            --bg-card:     #0f1420;
            --bg-input:    #141926;
            --bg-sidebar:  #0b0e18;
            --border:      rgba(212,175,55,.2);
            --border-hover:rgba(212,175,55,.55);
            --text:        #e8ecf2;
            --text-muted:  #7a8394;
            --text-dim:    #4a5568;
            --danger:      #c0392b;
            --danger-dark: #922b21;
            --success:     #27ae60;
            --sala1:       #c8860a;
            --sala2:       #6c3fc0;
            --sala3:       #0b7dda;
            --radius:      10px;
            --transition:  all .25s cubic-bezier(.16,1,.3,1);
        }

        html, body {
            height: 100%;
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            color: var(--text);
            overflow-x: hidden;
        }

        /* ══════════════════════════════════════════════
           LAYOUT
        ══════════════════════════════════════════════ */
        .layout {
            display: grid;
            grid-template-columns: 260px 1fr;
            grid-template-rows: 60px 1fr;
            min-height: 100vh;
        }

        /* ── HEADER ── */
        .header {
            grid-column: 1 / -1;
            background: var(--bg-sidebar);
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 0 28px;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .header-logo {
            font-family: 'Press Start 2P', monospace;
            font-size: .65rem;
            color: var(--gold);
            letter-spacing: 1px;
            text-shadow: 0 0 12px var(--gold-glow);
        }

        .header-sep { width: 1px; height: 28px; background: var(--border); margin: 0 4px; }

        .header-title {
            font-family: 'Cinzel', serif;
            font-size: 1rem;
            color: var(--text-muted);
            letter-spacing: 2px;
        }

        .header-badge {
            margin-left: auto;
            background: rgba(212,175,55,.12);
            border: 1px solid var(--gold-dark);
            color: var(--gold);
            padding: 4px 14px;
            border-radius: 20px;
            font-size: .7rem;
            font-family: 'Cinzel', serif;
            letter-spacing: 1.5px;
        }

        .header-back-link {
            margin-left: 12px;
            color: var(--text-muted);
            text-decoration: none;
            font-size: .75rem;
            letter-spacing: 1px;
            border: 1px solid var(--border);
            padding: 5px 14px;
            border-radius: 20px;
            transition: var(--transition);
        }

        .header-back-link:hover { color: var(--gold); border-color: var(--gold-dark); }

        /* ── SIDEBAR ── */
        .sidebar {
            background: var(--bg-sidebar);
            border-right: 1px solid var(--border);
            padding: 24px 16px;
            display: flex;
            flex-direction: column;
            gap: 6px;
            overflow-y: auto;
        }

        .sidebar-section-label {
            font-size: .6rem;
            letter-spacing: 2.5px;
            text-transform: uppercase;
            color: var(--text-dim);
            padding: 12px 10px 6px;
            font-weight: 600;
        }

        .sidebar-btn {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 14px;
            border-radius: var(--radius);
            border: 1px solid transparent;
            background: none;
            color: var(--text-muted);
            font-size: .82rem;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            transition: var(--transition);
            text-align: left;
            width: 100%;
        }

        .sidebar-btn:hover { background: rgba(255,255,255,.04); color: var(--text); }

        .sidebar-btn.active {
            background: rgba(212,175,55,.1);
            border-color: var(--border-hover);
            color: var(--gold-light);
        }

        .sidebar-btn .icon {
            width: 28px;
            height: 28px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            flex-shrink: 0;
        }

        .sala1-dot { background: rgba(200,134,10,.18); }
        .sala2-dot { background: rgba(108, 63,192,.18); }
        .sala3-dot { background: rgba(11,125,218,.18); }
        .all-dot   { background: rgba(212,175,55,.12); }

        .sidebar-count {
            margin-left: auto;
            background: rgba(255,255,255,.06);
            color: var(--text-dim);
            font-size: .65rem;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 10px;
            min-width: 26px;
            text-align: center;
        }

        .sidebar-count.active-count { background: var(--gold-dark); color: #000; }

        /* ── MAIN ── */
        .main {
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        /* ══════════════════════════════════════════════
           TOOLBAR
        ══════════════════════════════════════════════ */
        .toolbar {
            padding: 20px 28px 0;
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .toolbar-title {
            font-family: 'Cinzel', serif;
            font-size: 1.2rem;
            color: var(--text);
            letter-spacing: 1.5px;
        }

        .btn-primary {
            margin-left: auto;
            background: linear-gradient(135deg, var(--gold-dark), var(--gold));
            color: #07080c;
            border: none;
            padding: 10px 22px;
            border-radius: var(--radius);
            font-size: .8rem;
            font-weight: 700;
            font-family: 'Cinzel', serif;
            letter-spacing: 1.5px;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(212,175,55,.35); }

        .search-box {
            background: var(--bg-input);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 9px 16px;
            color: var(--text);
            font-size: .82rem;
            font-family: 'Inter', sans-serif;
            width: 220px;
            transition: var(--transition);
        }

        .search-box:focus { outline: none; border-color: var(--gold-dark); box-shadow: 0 0 0 3px rgba(212,175,55,.1); }
        .search-box::placeholder { color: var(--text-dim); }

        /* ══════════════════════════════════════════════
           TABLA DE CARTAS
        ══════════════════════════════════════════════ */
        .table-wrapper {
            flex: 1;
            overflow-y: auto;
            padding: 16px 28px 28px;
            margin-top: 16px;
        }

        /* Scroll personalizado */
        .table-wrapper::-webkit-scrollbar { width: 6px; }
        .table-wrapper::-webkit-scrollbar-track { background: transparent; }
        .table-wrapper::-webkit-scrollbar-thumb { background: var(--border); border-radius: 3px; }

        .cards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 16px;
        }

        /* Tarjeta de carta */
        .card-item {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 12px;
            overflow: hidden;
            transition: var(--transition);
            position: relative;
        }

        .card-item:hover { border-color: var(--border-hover); transform: translateY(-3px); box-shadow: 0 12px 30px rgba(0,0,0,.5); }

        .card-item-header {
            position: relative;
            height: 160px;
            background: #0c0f1a;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .card-item-img {
            height: 100%;
            width: 100%;
            object-fit: contain;
            padding: 8px;
            transition: transform .4s ease;
        }

        .card-item:hover .card-item-img { transform: scale(1.05); }

        .card-item-no-img {
            color: var(--text-dim);
            font-size: .75rem;
            letter-spacing: 1px;
            text-align: center;
        }

        .card-item-sala-badge {
            position: absolute;
            top: 10px;
            left: 10px;
            font-size: .55rem;
            font-weight: 700;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            padding: 3px 10px;
            border-radius: 10px;
            font-family: 'Cinzel', serif;
        }

        .badge-sala-1 { background: rgba(200,134,10,.25); color: #f0b429; border: 1px solid rgba(200,134,10,.4); }
        .badge-sala-2 { background: rgba(108, 63,192,.25); color: #a78bfa; border: 1px solid rgba(108,63,192,.4); }
        .badge-sala-3 { background: rgba(11,125,218,.25);  color: #60a5fa; border: 1px solid rgba(11,125,218,.4);  }
        .badge-sala-default { background: rgba(212,175,55,.12); color: var(--gold); border: 1px solid var(--border); }

        .card-item-reves-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            background: rgba(0,0,0,.6);
            border: 1px solid var(--border);
            color: var(--text-muted);
            font-size: .55rem;
            padding: 3px 8px;
            border-radius: 8px;
            cursor: pointer;
            letter-spacing: 1px;
            transition: var(--transition);
        }

        .card-item-reves-btn:hover { color: var(--gold); border-color: var(--gold-dark); }

        .card-item-body {
            padding: 14px 16px;
        }

        .card-item-title {
            font-family: 'Cinzel', serif;
            font-size: .9rem;
            font-weight: 700;
            color: var(--text);
            margin-bottom: 4px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .card-item-meta {
            font-size: .72rem;
            color: var(--text-muted);
            margin-bottom: 12px;
            line-height: 1.5;
        }

        .card-item-meta strong { color: var(--gold); }

        .card-item-actions {
            display: flex;
            gap: 8px;
        }

        .btn-edit, .btn-del {
            flex: 1;
            padding: 8px;
            border-radius: 8px;
            font-size: .7rem;
            font-weight: 600;
            letter-spacing: .5px;
            cursor: pointer;
            transition: var(--transition);
            font-family: 'Inter', sans-serif;
            border: 1px solid transparent;
        }

        .btn-edit {
            background: rgba(212,175,55,.1);
            border-color: rgba(212,175,55,.2);
            color: var(--gold-light);
        }

        .btn-edit:hover { background: rgba(212,175,55,.2); border-color: var(--gold); }

        .btn-del {
            background: rgba(192,57,43,.1);
            border-color: rgba(192,57,43,.2);
            color: #e57373;
        }

        .btn-del:hover { background: rgba(192,57,43,.25); border-color: var(--danger); }

        /* Empty state */
        .empty-state {
            grid-column: 1 / -1;
            text-align: center;
            padding: 60px 20px;
            color: var(--text-dim);
        }

        .empty-state-icon { font-size: 3rem; margin-bottom: 16px; opacity: .5; }
        .empty-state-title { font-size: 1rem; margin-bottom: 8px; color: var(--text-muted); }
        .empty-state-sub { font-size: .8rem; }

        /* ══════════════════════════════════════════════
           MODAL
        ══════════════════════════════════════════════ */
        .modal-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,.75);
            backdrop-filter: blur(6px);
            z-index: 500;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .modal-backdrop.open { display: flex; }

        .modal {
            background: var(--bg-card);
            border: 1px solid var(--border-hover);
            border-radius: 16px;
            width: 100%;
            max-width: 700px;
            max-height: 90vh;
            display: flex;
            flex-direction: column;
            box-shadow: 0 30px 60px rgba(0,0,0,.8), 0 0 0 1px rgba(212,175,55,.08);
            animation: modalIn .3s cubic-bezier(.16,1,.3,1);
        }

        @keyframes modalIn {
            from { opacity: 0; transform: scale(.96) translateY(12px); }
            to   { opacity: 1; transform: scale(1) translateY(0); }
        }

        .modal-header {
            padding: 22px 26px;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .modal-title {
            font-family: 'Cinzel', serif;
            font-size: 1.05rem;
            letter-spacing: 2px;
            background: linear-gradient(90deg, #fff, var(--gold-light));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .modal-close {
            margin-left: auto;
            background: none;
            border: none;
            color: var(--text-muted);
            font-size: 1.4rem;
            cursor: pointer;
            padding: 4px 8px;
            border-radius: 6px;
            transition: var(--transition);
            line-height: 1;
        }

        .modal-close:hover { background: rgba(255,255,255,.06); color: var(--text); }

        .modal-body {
            padding: 24px 26px;
            overflow-y: auto;
            flex: 1;
        }

        .modal-body::-webkit-scrollbar { width: 5px; }
        .modal-body::-webkit-scrollbar-thumb { background: var(--border); border-radius: 3px; }

        .modal-footer {
            padding: 18px 26px;
            border-top: 1px solid var(--border);
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }

        /* ── Formulario ── */
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .form-group { display: flex; flex-direction: column; gap: 6px; }
        .form-group.span-2 { grid-column: 1 / -1; }

        .form-label {
            font-size: .72rem;
            font-weight: 600;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: var(--text-muted);
        }

        .form-label .req { color: var(--gold); margin-left: 2px; }

        .form-input, .form-select, .form-textarea {
            background: var(--bg-input);
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 10px 14px;
            color: var(--text);
            font-size: .85rem;
            font-family: 'Inter', sans-serif;
            transition: var(--transition);
            outline: none;
            width: 100%;
        }

        .form-input:focus, .form-select:focus, .form-textarea:focus {
            border-color: var(--gold-dark);
            box-shadow: 0 0 0 3px rgba(212,175,55,.1);
        }

        .form-select { cursor: pointer; appearance: none; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath d='M1 1l5 5 5-5' stroke='%23d4af37' stroke-width='2' fill='none' stroke-linecap='round'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 14px center; padding-right: 36px; }

        .form-select option { background: #1a2030; }

        .form-textarea { resize: vertical; min-height: 80px; line-height: 1.5; }

        /* Upload de imagen */
        .upload-zone {
            border: 2px dashed var(--border);
            border-radius: 10px;
            padding: 20px 14px;
            text-align: center;
            cursor: pointer;
            transition: var(--transition);
            position: relative;
            overflow: hidden;
        }

        .upload-zone:hover, .upload-zone.drag-over { border-color: var(--gold-dark); background: rgba(212,175,55,.05); }

        .upload-zone input[type="file"] {
            position: absolute;
            inset: 0;
            opacity: 0;
            cursor: pointer;
            width: 100%;
            height: 100%;
        }

        .upload-icon { font-size: 1.6rem; margin-bottom: 6px; }
        .upload-label { font-size: .78rem; color: var(--text-muted); }
        .upload-label strong { color: var(--gold); }
        .upload-sub { font-size: .65rem; color: var(--text-dim); margin-top: 3px; }

        .upload-preview {
            width: 100%;
            max-height: 120px;
            object-fit: contain;
            border-radius: 6px;
            margin-top: 8px;
            display: none;
        }

        /* Botones del modal */
        .btn-cancel {
            background: rgba(255,255,255,.04);
            border: 1px solid var(--border);
            color: var(--text-muted);
            padding: 10px 22px;
            border-radius: var(--radius);
            font-size: .82rem;
            cursor: pointer;
            transition: var(--transition);
            font-family: 'Inter', sans-serif;
        }

        .btn-cancel:hover { background: rgba(255,255,255,.08); color: var(--text); }

        .btn-save {
            background: linear-gradient(135deg, var(--gold-dark), var(--gold));
            color: #07080c;
            border: none;
            padding: 10px 28px;
            border-radius: var(--radius);
            font-size: .82rem;
            font-weight: 700;
            cursor: pointer;
            transition: var(--transition);
            font-family: 'Inter', sans-serif;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-save:hover { transform: translateY(-1px); box-shadow: 0 6px 16px rgba(212,175,55,.35); }
        .btn-save:disabled { opacity: .5; cursor: not-allowed; transform: none; }

        /* ══════════════════════════════════════════════
           TOAST NOTIFICACIONES
        ══════════════════════════════════════════════ */
        .toast-stack {
            position: fixed;
            bottom: 24px;
            right: 24px;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 8px;
            pointer-events: none;
        }

        .toast {
            background: var(--bg-card);
            border-left: 4px solid var(--gold);
            border-radius: 8px;
            padding: 12px 18px;
            font-size: .82rem;
            box-shadow: 0 8px 24px rgba(0,0,0,.6);
            animation: toastIn .3s ease;
            pointer-events: all;
            max-width: 320px;
        }

        .toast.toast-error { border-color: var(--danger); }
        .toast.toast-success { border-color: var(--success); }

        @keyframes toastIn {
            from { opacity: 0; transform: translateX(20px); }
            to   { opacity: 1; transform: translateX(0); }
        }

        /* ══════════════════════════════════════════════
           CONFIRM DIALOG
        ══════════════════════════════════════════════ */
        .confirm-modal {
            background: var(--bg-card);
            border: 1px solid rgba(192,57,43,.4);
            border-radius: 14px;
            padding: 30px 28px;
            max-width: 380px;
            width: 100%;
            text-align: center;
            animation: modalIn .25s ease;
        }

        .confirm-icon { font-size: 2.5rem; margin-bottom: 14px; }
        .confirm-title { font-family: 'Cinzel', serif; font-size: 1rem; margin-bottom: 8px; color: var(--text); }
        .confirm-text { font-size: .83rem; color: var(--text-muted); margin-bottom: 22px; line-height: 1.5; }

        .confirm-actions { display: flex; gap: 10px; justify-content: center; }

        .btn-danger {
            background: var(--danger);
            color: #fff;
            border: none;
            padding: 10px 24px;
            border-radius: var(--radius);
            font-size: .82rem;
            font-weight: 700;
            cursor: pointer;
            transition: var(--transition);
            font-family: 'Inter', sans-serif;
        }

        .btn-danger:hover { background: var(--danger-dark); }

        /* ══════════════════════════════════════════════
           STATS BAR
        ══════════════════════════════════════════════ */
        .stats-bar {
            display: flex;
            gap: 16px;
            padding: 16px 28px 0;
            flex-wrap: wrap;
        }

        .stat-pill {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 8px 16px;
            font-size: .72rem;
            color: var(--text-muted);
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .stat-pill strong { color: var(--gold-light); font-size: .85rem; }

        /* ══════════════════════════════════════════════
           LOADER
        ══════════════════════════════════════════════ */
        .spinner {
            display: inline-block;
            width: 14px;
            height: 14px;
            border: 2px solid rgba(7,8,12,.4);
            border-top-color: #07080c;
            border-radius: 50%;
            animation: spin .7s linear infinite;
        }

        @keyframes spin { to { transform: rotate(360deg); } }
    </style>
</head>
<body>

<div class="layout">

    <!-- ══ HEADER ══════════════════════════════════════════════════════════ -->
    <header class="header">
        <span class="header-logo">PK</span>
        <div class="header-sep"></div>
        <span class="header-title">PANEL DE ADMINISTRACIÓN</span>
        <div class="header-badge">MUSEO POKÉMON</div>
        <a href="index.html" class="header-back-link">← Ver Museo</a>
    </header>

    <!-- ══ SIDEBAR ═════════════════════════════════════════════════════════ -->
    <aside class="sidebar">
        <span class="sidebar-section-label">Filtrar por Sala</span>

        <button class="sidebar-btn active" onclick="filtrarSala('todas')" id="btn-todas">
            <span class="icon all-dot">🏛️</span>
            Todas las Salas
            <span class="sidebar-count" id="count-todas">—</span>
        </button>
        <button class="sidebar-btn" onclick="filtrarSala('sala1')" id="btn-sala1">
            <span class="icon sala1-dot">🔥</span>
            Sala 1 · Clásicas
            <span class="sidebar-count" id="count-sala1">0</span>
        </button>
        <button class="sidebar-btn" onclick="filtrarSala('sala2')" id="btn-sala2">
            <span class="icon sala2-dot">✨</span>
            Sala 2 · LEGEND
            <span class="sidebar-count" id="count-sala2">0</span>
        </button>
        <button class="sidebar-btn" onclick="filtrarSala('sala3')" id="btn-sala3">
            <span class="icon sala3-dot">⚡</span>
            Sala 3 · Modernas
            <span class="sidebar-count" id="count-sala3">0</span>
        </button>

        <span class="sidebar-section-label" style="margin-top:16px;">Acciones</span>
        <button class="sidebar-btn" onclick="abrirModal()">
            <span class="icon" style="background:rgba(212,175,55,.12)">➕</span>
            Nueva Carta
        </button>
        <button class="sidebar-btn" onclick="recargar()">
            <span class="icon" style="background:rgba(255,255,255,.05)">🔄</span>
            Actualizar Lista
        </button>
        <button class="sidebar-btn" onclick="sincronizarAJSONBin()" id="btn-sync">
            <span class="icon" style="background:rgba(39,174,96,.12)">☁️</span>
            Subir local → JSONBin
        </button>

        <div style="margin-top:auto;padding-top:20px;border-top:1px solid var(--border);">
            <div style="font-size:.6rem;letter-spacing:1.5px;color:var(--text-dim);text-transform:uppercase;padding:0 10px 6px;">JSONBin</div>
            <div style="font-size:.68rem;color:var(--text-dim);padding:4px 10px;line-height:1.6;">
                <div style="color:var(--text-muted);">Bin ID:</div>
                <div style="font-family:monospace;color:var(--gold);font-size:.6rem;word-break:break-all;">6aa26828ffd5d16053f558da</div>
                <div id="jb-status" style="margin-top:6px;display:flex;align-items:center;gap:5px;">
                    <span id="jb-dot" style="width:7px;height:7px;background:#4a5568;border-radius:50%;display:inline-block;"></span>
                    <span id="jb-status-text" style="font-size:.62rem;">Verificando…</span>
                </div>
            </div>
        </div>
    </aside>

    <!-- ══ MAIN ═════════════════════════════════════════════════════════════ -->
    <main class="main">
        <div class="toolbar">
            <h1 class="toolbar-title" id="toolbar-titulo">Todas las Salas</h1>
            <input class="search-box" type="search" id="buscador" placeholder="🔍  Buscar por título, set, artista…" oninput="filtrarBusqueda()">
            <button class="btn-primary" onclick="abrirModal()">
                ＋ ALTA DE CARTA
            </button>
        </div>

        <div class="stats-bar" id="stats-bar">
            <div class="stat-pill">Total <strong id="stat-total">—</strong></div>
            <div class="stat-pill">Sala 1 <strong id="stat-s1">—</strong></div>
            <div class="stat-pill">Sala 2 <strong id="stat-s2">—</strong></div>
            <div class="stat-pill">Sala 3 <strong id="stat-s3">—</strong></div>
        </div>

        <div class="table-wrapper">
            <div class="cards-grid" id="cards-grid">
                <div class="empty-state">
                    <div class="empty-state-icon">⌛</div>
                    <div class="empty-state-title">Cargando catálogo…</div>
                </div>
            </div>
        </div>
    </main>

</div>

<!-- ══ MODAL ALTA / EDICIÓN ═══════════════════════════════════════════════ -->
<div class="modal-backdrop" id="modal-backdrop">
    <div class="modal" id="modal">
        <div class="modal-header">
            <div class="modal-title" id="modal-titulo">ALTA DE CARTA</div>
            <button class="modal-close" onclick="cerrarModal()">✕</button>
        </div>

        <div class="modal-body">
            <form id="form-carta" enctype="multipart/form-data">
                <input type="hidden" id="f-modo"     value="crear">
                <input type="hidden" id="f-id-orig"  value="">

                <div class="form-grid">

                    <!-- Sala -->
                    <div class="form-group">
                        <label class="form-label">Sala <span class="req">*</span></label>
                        <select class="form-select" id="f-sala" required>
                            <option value="">— Seleccionar sala —</option>
                            <option value="sala1">Sala 1 · Clásicas</option>
                            <option value="sala2">Sala 2 · LEGEND</option>
                            <option value="sala3">Sala 3 · Modernas</option>
                        </select>
                    </div>

                    <!-- Rareza -->
                    <div class="form-group">
                        <label class="form-label">Rareza</label>
                        <input class="form-input" type="text" id="f-rarity" placeholder="Ej: Holo Rare, Secret Rare…">
                    </div>

                    <!-- Título -->
                    <div class="form-group span-2">
                        <label class="form-label">Título <span class="req">*</span></label>
                        <input class="form-input" type="text" id="f-titulo" placeholder="Ej: Sabrina's Gengar" required>
                    </div>

                    <!-- Set y Año -->
                    <div class="form-group">
                        <label class="form-label">Set / Expansión</label>
                        <input class="form-input" type="text" id="f-set" placeholder="Ej: Gym Challenge">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Año</label>
                        <input class="form-input" type="number" id="f-year" placeholder="Ej: 2000" min="1996" max="2099">
                    </div>

                    <!-- Artista -->
                    <div class="form-group">
                        <label class="form-label">Ilustrador</label>
                        <input class="form-input" type="text" id="f-artist" placeholder="Ej: Ken Sugimori">
                    </div>

                    <!-- ID personalizado -->
                    <div class="form-group">
                        <label class="form-label">ID Personalizado <span style="color:var(--text-dim);font-size:.6rem;text-transform:none;">(opcional)</span></label>
                        <input class="form-input" type="text" id="f-id" placeholder="Ej: gengar-gym-1998" pattern="[a-z0-9\-]+" title="Solo letras, números y guiones">
                    </div>

                    <!-- Descripción -->
                    <div class="form-group span-2">
                        <label class="form-label">Descripción del Museo</label>
                        <textarea class="form-textarea" id="f-desc" placeholder="Texto de la plaqueta informativa…" rows="3"></textarea>
                    </div>

                    <!-- Fallback URL -->
                    <div class="form-group span-2">
                        <label class="form-label">URL de Imagen Fallback <span style="color:var(--text-dim);font-size:.6rem;text-transform:none;">(si no hay archivo local)</span></label>
                        <input class="form-input" type="url" id="f-fallback" placeholder="https://images.pokemontcg.io/...">
                    </div>

                    <!-- Upload Frente -->
                    <div class="form-group">
                        <label class="form-label">Portada (Frente) <span style="color:var(--text-dim);font-size:.6rem;text-transform:none;">JPG / PNG / WebP</span></label>
                        <div class="upload-zone" id="zone-frente">
                            <input type="file" id="f-img-frente" accept="image/*" onchange="previewImg(this, 'prev-frente', 'zone-frente')">
                            <div class="upload-icon">🃏</div>
                            <div class="upload-label">Arrastra o <strong>elige archivo</strong></div>
                            <div class="upload-sub">Frente de la carta · máx 8 MB</div>
                            <img class="upload-preview" id="prev-frente" alt="Preview frente">
                        </div>
                    </div>

                    <!-- Upload Reverso -->
                    <div class="form-group">
                        <label class="form-label">Reverso (Parte de atrás) <span style="color:var(--text-dim);font-size:.6rem;text-transform:none;">opcional</span></label>
                        <div class="upload-zone" id="zone-reves">
                            <input type="file" id="f-img-reves" accept="image/*" onchange="previewImg(this, 'prev-reves', 'zone-reves')">
                            <div class="upload-icon">↩️</div>
                            <div class="upload-label">Arrastra o <strong>elige archivo</strong></div>
                            <div class="upload-sub">Reverso de la carta · máx 8 MB</div>
                            <img class="upload-preview" id="prev-reves" alt="Preview reverso">
                        </div>
                    </div>

                </div>
            </form>
        </div>

        <div class="modal-footer">
            <button class="btn-cancel" onclick="cerrarModal()">Cancelar</button>
            <button class="btn-save" id="btn-guardar" onclick="guardarCarta()">
                <span id="btn-save-text">💾 GUARDAR</span>
            </button>
        </div>
    </div>
</div>

<!-- ══ MODAL CONFIRMAR BAJA ════════════════════════════════════════════════ -->
<div class="modal-backdrop" id="confirm-backdrop">
    <div class="confirm-modal">
        <div class="confirm-icon">🗑️</div>
        <div class="confirm-title">¿Dar de baja esta carta?</div>
        <div class="confirm-text" id="confirm-text">Esta acción eliminará la carta del catálogo JSON permanentemente.</div>
        <div class="confirm-actions">
            <button class="btn-cancel" onclick="cerrarConfirm()">Cancelar</button>
            <button class="btn-danger" id="btn-confirm-del">Dar de baja</button>
        </div>
    </div>
</div>

<!-- ══ TOAST ════════════════════════════════════════════════════════════════ -->
<div class="toast-stack" id="toast-stack"></div>

<!-- ══════════════════════════════════════════════════════════════════════════
     JAVASCRIPT
════════════════════════════════════════════════════════════════════════════ -->
<script>
const API = 'php/api.php';

// Mapas de salas
const SALAS = {
    sala1: { label: 'Sala 1 · Clásicas',  badge: 'badge-sala-1' },
    sala2: { label: 'Sala 2 · LEGEND',    badge: 'badge-sala-2' },
    sala3: { label: 'Sala 3 · Modernas',  badge: 'badge-sala-3' },
};

let catalogo     = [];
let filtroSala   = 'todas';
let filtroBusq   = '';

// ─── CARGA INICIAL ─────────────────────────────────────────────────────────
async function cargar() {
    try {
        const res  = await fetch(`${API}?action=listar&_=${Date.now()}`);
        const json = await res.json();
        if (!json.ok) throw new Error(json.error);
        catalogo = json.data || [];
        actualizarContadores();
        renderGrid();
    } catch (e) {
        showToast('Error al cargar catálogo: ' + e.message, 'error');
    }
}

function recargar() { cargar(); showToast('Catálogo actualizado', 'success'); }

// ─── CONTADORES SIDEBAR Y STATS ────────────────────────────────────────────
function actualizarContadores() {
    const total = catalogo.length;
    const s1 = catalogo.filter(c => c.sala === 'sala1').length;
    const s2 = catalogo.filter(c => c.sala === 'sala2').length;
    const s3 = catalogo.filter(c => c.sala === 'sala3').length;

    document.getElementById('count-todas').textContent = total;
    document.getElementById('count-sala1').textContent = s1;
    document.getElementById('count-sala2').textContent = s2;
    document.getElementById('count-sala3').textContent = s3;

    document.getElementById('stat-total').textContent = total;
    document.getElementById('stat-s1').textContent    = s1;
    document.getElementById('stat-s2').textContent    = s2;
    document.getElementById('stat-s3').textContent    = s3;
}

// ─── RENDERIZAR GRID ───────────────────────────────────────────────────────
function renderGrid() {
    const grid = document.getElementById('cards-grid');

    let data = catalogo;

    // Filtro sala
    if (filtroSala !== 'todas') {
        data = data.filter(c => c.sala === filtroSala);
    }

    // Filtro búsqueda
    if (filtroBusq) {
        const q = filtroBusq.toLowerCase();
        data = data.filter(c =>
            (c.titulo   || '').toLowerCase().includes(q) ||
            (c.set      || '').toLowerCase().includes(q) ||
            (c.artist   || '').toLowerCase().includes(q) ||
            (c.rarity   || '').toLowerCase().includes(q) ||
            (c.id       || '').toLowerCase().includes(q)
        );
    }

    if (data.length === 0) {
        grid.innerHTML = `
            <div class="empty-state">
                <div class="empty-state-icon">📭</div>
                <div class="empty-state-title">No hay cartas en esta sala</div>
                <div class="empty-state-sub">Usa "Alta de Carta" para añadir la primera.</div>
            </div>`;
        return;
    }

    grid.innerHTML = data.map(c => tarjetaHTML(c)).join('');
}

function tarjetaHTML(c) {
    const sala    = SALAS[c.sala] || { label: c.sala || 'Sin sala', badge: 'badge-sala-default' };
    const imgSrc  = c.imagen || c.imagen_fallback || '';
    const imgRev  = c.imagen_reves || '';

    const imgEl = imgSrc
        ? `<img class="card-item-img" src="${imgSrc}" alt="${c.titulo}" onerror="this.src='${c.imagen_fallback || ''}'; this.onerror=null;">`
        : `<div class="card-item-no-img">📷<br>Sin imagen</div>`;

    const revBtn = imgRev
        ? `<button class="card-item-reves-btn" onclick="toggleReves(this, '${imgSrc}', '${imgRev}')">↩ Reverso</button>`
        : '';

    return `
    <div class="card-item" data-id="${c.id}">
        <div class="card-item-header">
            ${imgEl}
            <span class="card-item-sala-badge ${sala.badge}">${sala.label}</span>
            ${revBtn}
        </div>
        <div class="card-item-body">
            <div class="card-item-title" title="${c.titulo}">${c.titulo || '—'}</div>
            <div class="card-item-meta">
                <strong>${c.rarity || 'Sin rareza'}</strong><br>
                ${c.set || '—'} · ${c.year || '—'}<br>
                🎨 ${c.artist || 'Desconocido'}
            </div>
            <div class="card-item-actions">
                <button class="btn-edit" onclick="abrirEdicion('${c.id}')">✏️ Editar</button>
                <button class="btn-del"  onclick="confirmarBaja('${c.id}', '${escapar(c.titulo)}')">🗑️ Baja</button>
            </div>
        </div>
    </div>`;
}

function escapar(s) { return (s || '').replace(/'/g, "\\'").replace(/"/g, '&quot;'); }

function toggleReves(btn, frente, reves) {
    const img = btn.closest('.card-item-header').querySelector('.card-item-img');
    if (!img) return;
    const isReves = btn.dataset.showing === 'reves';
    img.src = isReves ? frente : reves;
    btn.dataset.showing = isReves ? 'frente' : 'reves';
    btn.textContent = isReves ? '↩ Reverso' : '↩ Frente';
}

// ─── FILTROS ───────────────────────────────────────────────────────────────
function filtrarSala(sala) {
    filtroSala = sala;

    document.querySelectorAll('.sidebar-btn').forEach(b => b.classList.remove('active'));
    document.getElementById('btn-' + (sala === 'todas' ? 'todas' : sala)).classList.add('active');

    const titulos = { todas: 'Todas las Salas', sala1: 'Sala 1 · Clásicas', sala2: 'Sala 2 · LEGEND', sala3: 'Sala 3 · Modernas' };
    document.getElementById('toolbar-titulo').textContent = titulos[sala] || sala;

    renderGrid();
}

function filtrarBusqueda() {
    filtroBusq = document.getElementById('buscador').value.trim();
    renderGrid();
}

// ─── MODAL ALTA / EDICIÓN ──────────────────────────────────────────────────
function abrirModal() {
    document.getElementById('modal-titulo').textContent = 'ALTA DE CARTA';
    document.getElementById('f-modo').value     = 'crear';
    document.getElementById('f-id-orig').value  = '';
    document.getElementById('form-carta').reset();
    limpiarPreviews();
    document.getElementById('modal-backdrop').classList.add('open');
}

function abrirEdicion(id) {
    const carta = catalogo.find(c => c.id === id);
    if (!carta) return showToast('Carta no encontrada', 'error');

    document.getElementById('modal-titulo').textContent = 'EDITAR CARTA';
    document.getElementById('f-modo').value     = 'editar';
    document.getElementById('f-id-orig').value  = carta.id;

    // Rellenar campos
    document.getElementById('f-sala').value     = carta.sala     || '';
    document.getElementById('f-titulo').value   = carta.titulo   || '';
    document.getElementById('f-set').value      = carta.set      || '';
    document.getElementById('f-year').value     = carta.year     || '';
    document.getElementById('f-artist').value   = carta.artist   || '';
    document.getElementById('f-rarity').value   = carta.rarity   || '';
    document.getElementById('f-desc').value     = carta.descripcion || '';
    document.getElementById('f-fallback').value = carta.imagen_fallback || '';
    document.getElementById('f-id').value       = '';

    // Mostrar preview actual si existe
    limpiarPreviews();
    if (carta.imagen) {
        const prev = document.getElementById('prev-frente');
        prev.src = carta.imagen;
        prev.style.display = 'block';
    }
    if (carta.imagen_reves) {
        const prev = document.getElementById('prev-reves');
        prev.src = carta.imagen_reves;
        prev.style.display = 'block';
    }

    document.getElementById('modal-backdrop').classList.add('open');
}

function cerrarModal() {
    document.getElementById('modal-backdrop').classList.remove('open');
}

function limpiarPreviews() {
    ['prev-frente','prev-reves'].forEach(id => {
        const el = document.getElementById(id);
        el.src = '';
        el.style.display = 'none';
    });
}

function previewImg(input, previewId, zoneId) {
    const file = input.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = e => {
        const prev = document.getElementById(previewId);
        prev.src = e.target.result;
        prev.style.display = 'block';
    };
    reader.readAsDataURL(file);
}

// ─── GUARDAR (CREAR / EDITAR) ──────────────────────────────────────────────
async function guardarCarta() {
    const modo    = document.getElementById('f-modo').value;
    const idOrig  = document.getElementById('f-id-orig').value;
    const titulo  = document.getElementById('f-titulo').value.trim();
    const sala    = document.getElementById('f-sala').value;

    if (!titulo || !sala) {
        showToast('Título y sala son obligatorios', 'error');
        return;
    }

    const btn = document.getElementById('btn-guardar');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner"></span> Guardando…';

    const fd = new FormData();
    fd.append('action',      modo === 'editar' ? 'editar' : 'crear');
    fd.append('id',          modo === 'editar' ? idOrig : (document.getElementById('f-id').value.trim()));
    fd.append('titulo',      titulo);
    fd.append('sala',        sala);
    fd.append('set',         document.getElementById('f-set').value.trim());
    fd.append('year',        document.getElementById('f-year').value.trim());
    fd.append('artist',      document.getElementById('f-artist').value.trim());
    fd.append('rarity',      document.getElementById('f-rarity').value.trim());
    fd.append('descripcion', document.getElementById('f-desc').value.trim());
    fd.append('fallback',    document.getElementById('f-fallback').value.trim());

    const frente = document.getElementById('f-img-frente').files[0];
    const reves  = document.getElementById('f-img-reves').files[0];
    if (frente) fd.append('img_frente', frente);
    if (reves)  fd.append('img_reves',  reves);

    try {
        const res  = await fetch(API, { method: 'POST', body: fd });
        const json = await res.json();

        if (!json.ok) throw new Error(json.error);

        showToast(modo === 'editar' ? '✅ Carta actualizada' : '✅ Carta dada de alta', 'success');
        cerrarModal();
        await cargar();
    } catch (e) {
        showToast('Error: ' + e.message, 'error');
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<span id="btn-save-text">💾 GUARDAR</span>';
    }
}

// ─── BAJA ──────────────────────────────────────────────────────────────────
let idParaBajar = null;

function confirmarBaja(id, titulo) {
    idParaBajar = id;
    document.getElementById('confirm-text').textContent = `Se eliminará "${titulo}" del catálogo JSON. Esta acción no se puede deshacer.`;
    document.getElementById('confirm-backdrop').classList.add('open');

    document.getElementById('btn-confirm-del').onclick = async () => {
        cerrarConfirm();
        const fd = new FormData();
        fd.append('action', 'eliminar');
        fd.append('id', idParaBajar);
        try {
            const res  = await fetch(API, { method: 'POST', body: fd });
            const json = await res.json();
            if (!json.ok) throw new Error(json.error);
            showToast('🗑️ Carta dada de baja', 'success');
            await cargar();
        } catch (e) {
            showToast('Error al dar de baja: ' + e.message, 'error');
        }
    };
}

function cerrarConfirm() {
    document.getElementById('confirm-backdrop').classList.remove('open');
    idParaBajar = null;
}

// ─── TOASTS ────────────────────────────────────────────────────────────────
function showToast(msg, tipo = 'info') {
    const stack = document.getElementById('toast-stack');
    const t = document.createElement('div');
    t.className = `toast toast-${tipo}`;
    t.textContent = msg;
    stack.appendChild(t);
    setTimeout(() => t.remove(), 3500);
}

// ─── SINCRONIZAR local → JSONBin ───────────────────────────────────────────
async function sincronizarAJSONBin() {
    const btn = document.getElementById('btn-sync');
    btn.disabled = true;
    btn.querySelector('.icon').textContent = '⏳';

    try {
        const fd = new FormData();
        fd.append('action', 'sincronizar');
        const res  = await fetch(API, { method: 'POST', body: fd });
        const json = await res.json();
        if (!json.ok) throw new Error(json.error);
        showToast(`☁️ ${json.mensaje} (${json.subidas} cartas)`, 'success');
        setJBStatus(true);
        await cargar();
    } catch (e) {
        showToast('Error al sincronizar: ' + e.message, 'error');
        setJBStatus(false);
    } finally {
        btn.disabled = false;
        btn.querySelector('.icon').textContent = '☁️';
    }
}

// ─── ESTADO JSONBIN ────────────────────────────────────────────────────────
function setJBStatus(ok) {
    const dot  = document.getElementById('jb-dot');
    const text = document.getElementById('jb-status-text');
    if (!dot || !text) return;
    dot.style.background  = ok ? '#27ae60' : '#c0392b';
    text.textContent      = ok ? 'Conectado ✓' : 'Sin conexión';
    text.style.color      = ok ? '#27ae60' : '#e57373';
}

async function checkJBStatus() {
    try {
        const res = await fetch(API + '?action=listar&_=' + Date.now());
        const json = await res.json();
        setJBStatus(json.ok);
    } catch {
        setJBStatus(false);
    }
}

// ─── CERRAR MODALES CON BACKDROP ──────────────────────────────────────────
document.getElementById('modal-backdrop').addEventListener('click', e => {
    if (e.target === document.getElementById('modal-backdrop')) cerrarModal();
});
document.getElementById('confirm-backdrop').addEventListener('click', e => {
    if (e.target === document.getElementById('confirm-backdrop')) cerrarConfirm();
});

// ─── INICIO ────────────────────────────────────────────────────────────────
cargar();
checkJBStatus();
</script>
</body>
</html>
