<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Cruce FINDRISC — Más Bienestar</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Mono:wght@400;500;600&family=IBM+Plex+Sans:ital,wght@0,300;0,400;0,500;0,600;1,300&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0 }

        :root {
            --bg:       #070a12;
            --surface:  #0c1220;
            --surface2: #111827;
            --surface3: #172035;
            --border:   #1e2d42;
            --border2:  #253650;
            --accent:   #e63946;
            --accent2:  #ff6b6b;
            --cyan:     #00d4ff;
            --text:     #dde4f0;
            --text2:    #8fa3bd;
            --muted:    #4d627a;
            --success:  #10d48a;
            --warn:     #f59e0b;
            --danger:   #ef4444;
            --mono:     'IBM Plex Mono', monospace;
            --sans:     'IBM Plex Sans', sans-serif;
        }

        body {
            background: var(--bg);
            font-family: var(--sans);
            color: var(--text);
            min-height: 100vh;
            padding: 2rem 1rem 4rem;
        }

        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image:
                linear-gradient(rgba(0, 212, 255, .025) 1px, transparent 1px),
                linear-gradient(90deg, rgba(0, 212, 255, .025) 1px, transparent 1px);
            background-size: 48px 48px;
            pointer-events: none;
            z-index: 0;
        }

        .wrap {
            width: 100%;
            max-width: 1300px;
            margin: 0 auto;
            position: relative;
            z-index: 1;
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
        }

        /* ── HEADER ── */
        header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1.5rem;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 1.5rem 2rem;
            position: relative;
            overflow: hidden;
        }

        header::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 2px;
            background: linear-gradient(90deg, var(--accent) 0%, var(--accent2) 40%, transparent 100%);
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 1.25rem;
        }

        .logo-badge {
            width: 54px;
            height: 54px;
            border-radius: 14px;
            background: linear-gradient(135deg, var(--accent) 0%, #9b1d2a 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: var(--mono);
            font-size: 12px;
            font-weight: 600;
            color: #fff;
            flex-shrink: 0;
            box-shadow: 0 6px 24px rgba(230,57,70,.35);
            letter-spacing: 1px;
        }

        .eyebrow {
            font-family: var(--mono);
            font-size: 9px;
            color: var(--accent2);
            letter-spacing: 3px;
            text-transform: uppercase;
            margin-bottom: 6px;
        }

        header h1 {
            font-size: 18px;
            font-weight: 600;
            color: #fff;
            line-height: 1.2;
        }

        header p {
            font-size: 12px;
            color: var(--text2);
            margin-top: 5px;
        }

        .tokens-widget {
            display: flex;
            flex-direction: column;
            align-items: center;
            flex-shrink: 0;
            background: var(--surface2);
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 14px 24px;
            min-width: 140px;
            position: relative;
            overflow: hidden;
        }

        .tokens-widget::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 2px;
            background: linear-gradient(90deg, var(--cyan), transparent);
        }

        .tokens-label {
            font-family: var(--mono);
            font-size: 8px;
            text-transform: uppercase;
            letter-spacing: 2.5px;
            color: var(--muted);
            margin-bottom: 6px;
        }

        #tokens-counter {
            font-family: var(--mono);
            font-size: 26px;
            font-weight: 600;
            color: var(--cyan);
            line-height: 1;
        }

        /* ── SECTION CARD ── */
        .section-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 16px;
            overflow: hidden;
        }

        .section-header {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: .875rem 1.5rem;
            border-bottom: 1px solid var(--border);
            background: var(--surface2);
        }

        .section-tag {
            font-family: var(--mono);
            font-size: 8px;
            text-transform: uppercase;
            letter-spacing: 2.5px;
            color: var(--accent2);
            background: rgba(230,57,70,.12);
            border: 1px solid rgba(230,57,70,.22);
            border-radius: 6px;
            padding: 3px 10px;
        }

        .section-title {
            font-size: 13px;
            font-weight: 400;
            color: var(--text2);
        }

        /* ── UPLOAD GRID ── */
        .upload-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
        }

        .upload-box {
            background: var(--surface);
            padding: 1.75rem 1.5rem;
            cursor: pointer;
            transition: background .2s;
            position: relative;
            overflow: hidden;
            display: block;
            border-right: 1px solid var(--border);
        }

        .upload-box:last-child { border-right: none }

        .upload-box::after {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 3px;
            background: var(--border2);
            transition: background .3s;
        }

        .upload-box:hover { background: var(--surface2) }
        .upload-box:hover::after { background: var(--accent) }
        .upload-box.loaded::after { background: var(--success) }

        .upload-box input[type="file"] { display: none }

        .ub-step {
            font-family: var(--mono);
            font-size: 9px;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: var(--muted);
            margin-bottom: 14px;
        }

        .ub-icon {
            font-size: 34px;
            display: block;
            margin-bottom: 12px;
        }

        .ub-title {
            font-size: 14px;
            font-weight: 600;
            color: var(--text);
            margin-bottom: 8px;
        }

        .ub-name {
            font-family: var(--mono);
            font-size: 10px;
            color: var(--text2);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            margin-bottom: 5px;
            transition: color .2s;
        }

        .upload-box.loaded .ub-name { color: var(--success) }

        .ub-hint {
            font-size: 10px;
            color: var(--muted);
        }

        /* ── COLUMNS CONFIG ── */
        .cols-sections {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
        }

        .cols-section {
            background: var(--surface);
            padding: 1.5rem;
            border-right: 1px solid var(--border);
        }

        .cols-section:last-child { border-right: none }

        .cols-section-title {
            font-family: var(--mono);
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: var(--text2);
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .cols-section-title::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--border);
        }

        .col-field { margin-bottom: .75rem }
        .col-field:last-child { margin-bottom: 0 }

        .col-field label {
            display: block;
            font-size: 10px;
            color: var(--muted);
            margin-bottom: 5px;
            font-family: var(--mono);
        }

        .col-field input {
            width: 100%;
            background: var(--surface2);
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 8px 12px;
            font-family: var(--mono);
            font-size: 11px;
            color: var(--text);
            outline: none;
            transition: border-color .2s, box-shadow .2s;
        }

        .col-field input:focus {
            border-color: var(--accent2);
            box-shadow: 0 0 0 3px rgba(230,57,70,.1);
        }

        /* ── ACTION BAR ── */
        .action-bar {
            display: flex;
            gap: 1rem;
            align-items: stretch;
        }

        .btn-run {
            height: 52px;
            padding: 0 36px;
            background: linear-gradient(135deg, var(--accent) 0%, #c1121f 100%);
            color: #fff;
            border: none;
            border-radius: 12px;
            font-family: var(--mono);
            font-size: 13px;
            font-weight: 600;
            letter-spacing: 1.5px;
            cursor: pointer;
            transition: opacity .2s, transform .1s, box-shadow .2s;
            text-transform: uppercase;
            box-shadow: 0 4px 20px rgba(230,57,70,.3);
            white-space: nowrap;
            flex-shrink: 0;
        }

        .btn-run:hover {
            opacity: .9;
            box-shadow: 0 6px 28px rgba(230,57,70,.45);
        }

        .btn-run:active { transform: scale(.98) }

        .btn-run:disabled {
            background: var(--surface2);
            color: var(--muted);
            cursor: not-allowed;
            box-shadow: none;
        }

        .btn-dl {
            height: 52px;
            padding: 0 28px;
            background: transparent;
            color: var(--success);
            border: 1px solid var(--success);
            border-radius: 12px;
            font-family: var(--mono);
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 1px;
            cursor: pointer;
            transition: all .2s;
            text-transform: uppercase;
            white-space: nowrap;
            flex-shrink: 0;
            display: none;
        }

        .btn-dl:hover { background: var(--success); color: #000 }
        .btn-dl.visible { display: block }

        .status {
            flex: 1;
            padding: 0 18px;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 12px;
            font-family: var(--mono);
            font-size: 11px;
            color: var(--muted);
            display: flex;
            align-items: center;
            gap: 10px;
            min-height: 52px;
            transition: border-color .3s, color .3s, background .3s;
        }

        .status.ok {
            border-color: var(--success);
            color: var(--success);
            background: rgba(16,212,138,.05);
        }

        .status.err {
            border-color: var(--danger);
            color: var(--danger);
            background: rgba(239,68,68,.05);
        }

        /* ── RESULTS ── */
        .results { display: none }
        .results.show {
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
        }

        .results-header {
            font-family: var(--mono);
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 3px;
            color: var(--text2);
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .results-header::before { content: '▎'; color: var(--accent2) }

        .results-header::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--border);
        }

        /* Stat cards (clases generadas por JS) */
        .stats-summary {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
        }

        .stat-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-left: 4px solid var(--accent);
            border-radius: 14px;
            padding: 1.25rem 1.5rem;
            position: relative;
            overflow: hidden;
        }

        .stat-card::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(255,255,255,.02) 0%, transparent 60%);
            pointer-events: none;
        }

        .stat-card .val {
            font-family: var(--mono);
            font-size: 34px;
            font-weight: 600;
            color: #fff;
            line-height: 1;
            margin-bottom: 8px;
        }

        .stat-card .lbl {
            font-size: 12px;
            color: var(--text2);
        }

        /* ── TABLA ── */
        .table-wrap {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 16px;
            overflow: hidden;
            overflow-x: auto;
        }

        .main-table {
            border-collapse: collapse;
            font-size: 11px;
            white-space: nowrap;
            width: 100%;
        }

        .main-table th, .main-table td {
            border: 1px solid var(--border);
            padding: 5px 7px;
            text-align: center;
            vertical-align: middle;
        }

        .main-table th {
            font-family: var(--mono);
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--text2);
            background: var(--surface2);
            line-height: 1.3;
        }

        .main-table td { background: var(--surface); color: var(--text) }
        .main-table tr:hover td { background: var(--surface2) }

        .td-upz {
            font-family: var(--mono);
            font-size: 12px;
            font-weight: 600;
            text-align: left !important;
            background: #080f1e !important;
            color: #00d4ff !important;
            border-left: 3px solid var(--accent) !important;
            min-width: 130px;
            position: sticky;
            left: 0;
            z-index: 2;
        }

        .td-total-row {
            font-family: var(--mono);
            font-size: 12px;
            font-weight: 600;
            text-align: left !important;
            color: #fff !important;
            background: #0a1428 !important;
            position: sticky;
            left: 0;
            z-index: 2;
        }

        .th-upz {
            text-align: left !important;
            background: #080f1e !important;
            color: #fff !important;
            font-size: 11px !important;
            min-width: 130px;
            position: sticky;
            left: 0;
            z-index: 3;
        }

        .num { font-family: var(--mono); font-size: 11px }
        .num-m { color: #7ec8ff }
        .num-f { color: #f07bff }
        .num-i { color: #555 }
        .num-st { font-weight: 600; background: rgba(255,255,255,.03) !important }
        .num-cat { background: rgba(0,212,255,.1) !important; font-weight: 700; color: #00d4ff !important }
        .num-total { background: #00d4ff !important; color: #000 !important; font-weight: 700 }

        .th-cat { border-bottom: 2px solid #00d4ff !important; background: rgba(0,212,255,.06) !important; color: #00d4ff !important; font-size: 10px !important }
        .th-grupo { font-size: 8px !important; background: rgba(255,255,255,.03) !important; border-right: 1px solid #333 !important }
        .th-total-col { background: #0a1428 !important; color: #fff !important }
        .th-totalcat { background: #1e293b !important; font-size: 10px !important; color: var(--accent2) !important }

        .console-note {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 10px 16px;
            font-family: var(--mono);
            font-size: 10px;
            color: var(--muted);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .console-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: var(--accent2);
            animation: pulse 1.5s ease-in-out infinite;
            flex-shrink: 0;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1 }
            50% { opacity: .3 }
        }

        .spinner {
            width: 14px;
            height: 14px;
            border: 2px solid rgba(255,255,255,.2);
            border-top-color: #fff;
            border-radius: 50%;
            animation: spin .7s linear infinite;
            flex-shrink: 0;
            display: inline-block;
            vertical-align: middle;
        }

        @keyframes spin { to { transform: rotate(360deg) } }

        @media (max-width: 900px) {
            .upload-grid, .cols-sections { grid-template-columns: 1fr }
            .upload-box { border-right: none; border-bottom: 1px solid var(--border) }
            .cols-section { border-right: none; border-bottom: 1px solid var(--border) }
            .stats-summary { grid-template-columns: 1fr }
            header { flex-direction: column; align-items: flex-start }
            .action-bar { flex-wrap: wrap }
        }
    </style>
</head>

<body>

    @if(!empty($message))
    <div style="
        position: fixed;
        inset: 0;
        z-index: 9999;
        background: rgba(10, 12, 18, 0.96);
        display: flex;
        align-items: center;
        justify-content: center;
        backdrop-filter: blur(6px);
    ">
        <div style="
            background: #13151e;
            border: 1px solid #f03e3e;
            border-radius: 16px;
            padding: 48px 40px;
            max-width: 480px;
            width: 90%;
            text-align: center;
            box-shadow: 0 0 60px rgba(240,62,62,0.18);
        ">
            <div style="font-size: 52px; margin-bottom: 20px;">🔒</div>
            <div style="
                font-family: 'IBM Plex Mono', monospace;
                font-size: 11px;
                letter-spacing: 3px;
                text-transform: uppercase;
                color: #f03e3e;
                margin-bottom: 16px;
            ">Acceso restringido</div>
            <p style="
                color: #e0e4f0;
                font-size: 16px;
                line-height: 1.6;
                margin: 0 0 32px;
            ">{{ $message }}</p>
            <a href="mailto:soporte@masbienestar.com" style="
                display: inline-block;
                background: #f03e3e;
                color: #fff;
                text-decoration: none;
                padding: 12px 28px;
                border-radius: 8px;
                font-size: 14px;
                font-weight: 600;
            ">Contactar soporte</a>
        </div>
    </div>
    @endif

    <div class="wrap">

        <!-- HEADER -->
        <header>
            <div class="header-left">
                <div class="logo-badge">FDR</div>
                <div>
                    <div class="eyebrow">Análisis para crónicos — Más Bienestar</div>
                    <h1>Cruce Triple: UPZ × Edad × Género × Riesgo</h1>
                    <p>Personas · Tamizajes · Fichas — filas por UPZ, columnas por nivel de riesgo</p>
                </div>
            </div>
            <div class="tokens-widget">
                <div class="tokens-label">Tokens disponibles</div>
                <div id="tokens-counter">Cargando...</div>
            </div>
        </header>

        <!-- PASO 1: ARCHIVOS -->
        <div class="section-card">
            <div class="section-header">
                <span class="section-tag">PASO 1</span>
                <span class="section-title">Cargar archivos de datos</span>
            </div>
            <div class="upload-grid">

                <label class="upload-box" id="box_per" for="f_per">
                    <div class="ub-step">01 — Base Personas</div>
                    <span class="ub-icon">👤</span>
                    <div class="ub-title">Datos personales</div>
                    <div class="ub-name" id="n_per">Seleccionar archivo .xlsx</div>
                    <div class="ub-hint">Documento · Edad · Sexo</div>
                    <input type="file" id="f_per" accept=".xlsx,.xls">
                </label>

                <label class="upload-box" id="box_tam" for="f_tam">
                    <div class="ub-step">02 — Base Tamizajes</div>
                    <span class="ub-icon">📋</span>
                    <div class="ub-title">Resultados FINDRISC</div>
                    <div class="ub-name" id="n_tam">Seleccionar archivo .xlsx</div>
                    <div class="ub-hint">Documento · Ficha_fic · FINDRISC</div>
                    <input type="file" id="f_tam" accept=".xlsx,.xls">
                </label>

                <label class="upload-box" id="box_fic" for="f_fic">
                    <div class="ub-step">03 — Base Fichas</div>
                    <span class="ub-icon">🗺️</span>
                    <div class="ub-title">Fichas geográficas</div>
                    <div class="ub-name" id="n_fic">Seleccionar archivo .xlsx</div>
                    <div class="ub-hint">Ficha_fic · UPZ/UPR</div>
                    <input type="file" id="f_fic" accept=".xlsx,.xls">
                </label>

            </div>
        </div>

        <!-- PASO 2: CONFIGURACIÓN -->
        <div class="section-card">
            <div class="section-header">
                <span class="section-tag">PASO 2</span>
                <span class="section-title">Nombres de columnas en los archivos</span>
            </div>
            <div class="cols-sections">

                <div class="cols-section">
                    <div class="cols-section-title">📄 Base Personas</div>
                    <div class="col-field">
                        <label>Columna Documento</label>
                        <input type="text" id="col_doc_per" value=".Número de Documento.">
                    </div>
                    <div class="col-field">
                        <label>Columna Edad</label>
                        <input type="text" id="col_edad" value=".Edad.">
                    </div>
                    <div class="col-field">
                        <label>Columna Sexo</label>
                        <input type="text" id="col_sexo" value=".Sexo.">
                    </div>
                </div>

                <div class="cols-section">
                    <div class="cols-section-title">📋 Base Tamizajes</div>
                    <div class="col-field">
                        <label>Columna Documento</label>
                        <input type="text" id="col_doc_tam" value=".Número de Documento.">
                    </div>
                    <div class="col-field">
                        <label>Columna Ficha_fic</label>
                        <input type="text" id="col_ficha_tam" value="Ficha_fic">
                    </div>
                    <div class="col-field">
                        <label>Columna a elegir</label>
                        <input type="text" id="col_puntaje" value=".FINDRISC.">
                    </div>
                </div>

                <div class="cols-section">
                    <div class="cols-section-title">🗺️ Base Fichas</div>
                    <div class="col-field">
                        <label>Columna Ficha_fic</label>
                        <input type="text" id="col_ficha_fic" value="Ficha_fic">
                    </div>
                    <div class="col-field">
                        <label>Columna UPZ/UPR</label>
                        <input type="text" id="col_upz" value=".UPZ/UPR.">
                    </div>
                </div>

            </div>
        </div>

        <!-- ACCIÓN -->
        <div class="action-bar">
            <button class="btn-run" id="btnRun">▶ Ejecutar Cruce</button>
            <button class="btn-dl" id="btnDl">⬇ Descargar Excel</button>
            <div class="status" id="status">
                <span>⬤</span>
                <span id="stext">Esperando archivos…</span>
            </div>
        </div>

        <!-- RESULTADOS -->
        <div class="results" id="results">
            <div class="results-header">Resumen General</div>
            <div class="stats-summary" id="stats-summary"></div>
            <div class="results-header">Tabla UPZ × Nivel de Riesgo × Grupo de Edad × Género</div>
            <div class="table-wrap">
                <table class="main-table" id="main-table"></table>
            </div>
            <div class="console-note">
                <div class="console-dot"></div>
                Datos completos también disponibles en la consola del navegador (F12 → Console)
            </div>
        </div>

    </div>

    <script src="https://cdn.sheetjs.com/xlsx-latest/package/dist/xlsx.full.min.js"></script>
    <script>
        window._XLSXRead = window.XLSX;
    </script>
    <script src="https://cdn.jsdelivr.net/npm/xlsx-js-style@1.2.0/dist/xlsx.bundle.js"></script>

    <script>

        let userTokens = @json($tokens ?? 0);

        document.addEventListener("DOMContentLoaded", function() {
            const tokensDisplay = document.getElementById('tokens-counter');
            if (tokensDisplay) {
                tokensDisplay.textContent = userTokens;
            }
        });
    </script>

    <script>
        const $ = id => document.getElementById(id);

        const GRUPOS = ["P", "I", "D", "J", "A", "V"];
        const GRUPO_LABEL = {
            P: "Prim.Inf\n(0-5)",
            I: "Infancia\n(6-11)",
            D: "Adolesc.\n(12-17)",
            J: "Juventud\n(18-28)",
            A: "Adultez\n(29-59)",
            V: "Vejez\n(≥60)"
        };

        // Estado global para la descarga
        let _lastCategorias = [],
            _lastDataMap = null,
            _lastTotal = null;

        ['per', 'tam', 'fic'].forEach(id => {
            $(`f_${id}`).onchange = e => {
                $(`n_${id}`).textContent = e.target.files[0].name;
                $(`box_${id}`).classList.add('loaded');
            };
        });

        function normalizarValor(v) {
            if (v === undefined || v === null || String(v).trim() === "") return "Sin Dato";
            return String(v).trim();
        }

        function createEmptyRow(categorias) {
            const row = {};
            categorias.forEach(cat => {
                row[cat] = {};
                GRUPOS.forEach(g => {
                    row[cat][g] = {
                        M: 0,
                        F: 0,
                        I: 0
                    };
                });
            });
            return row;
        }

        $('btnRun').onclick = async () => {
            const files = {
                per: $('f_per').files[0],
                tam: $('f_tam').files[0],
                fic: $('f_fic').files[0]
            };
            if (!files.per || !files.tam || !files.fic) {
                alert("Por favor, sube los tres archivos (.xlsx) antes de continuar.");
                return;
            }

            // 1. CONTROL PREVIO GLOBAL: Si ya está en 0 localmente, ni ejecutamos
            if (typeof userTokens !== 'undefined' && userTokens <= 0) {
                alert('No tienes tokens disponibles para realizar esta acción. Por favor, adquiere más tokens para continuar.');
                return;
            }

            const cols = {
                docPer: $('col_doc_per').value.trim(),
                edad: $('col_edad').value.trim(),
                sexo: $('col_sexo').value.trim(),
                docTam: $('col_doc_tam').value.trim(),
                fichaTam: $('col_ficha_tam').value.trim(),
                target: $('col_puntaje').value.trim(),
                fichaFic: $('col_ficha_fic').value.trim(),
                upz: $('col_upz').value.trim()
            };

            $('btnRun').disabled = true;
            $('btnRun').innerHTML = '<span class="spinner"></span> Procesando...';
            $('btnDl').classList.remove('visible');

            try {
                const readXls = async f => {
                    const buf = await f.arrayBuffer();
                    const wb = _XLSXRead.read(buf);
                    return _XLSXRead.utils.sheet_to_json(wb.Sheets[wb.SheetNames[0]], {
                        range: 1
                    });
                };

                const [dPer, dTam, dFic] = await Promise.all([readXls(files.per), readXls(files.tam), readXls(files
                    .fic)]);

                const tokensNecesarios = Math.ceil(dTam.length / 500) * 5;

                if (typeof userTokens !== 'undefined' && userTokens < tokensNecesarios) {
                    alert(`No tienes suficientes tokens para esta operación.\nRequeridos: ${tokensNecesarios} tokens (para ${dTam.length} filas).\nDisponibles: ${userTokens}.`);
                    // Restablecemos el botón antes de salir
                    $('btnRun').disabled = false;
                    $('btnRun').innerHTML = '▶ Ejecutar Cruce';
                    return;
                }

                    // Mapeo Fichas -> UPZ
                const mapFic = new Map();
                dFic.forEach(f => {
                    const id = String(f[cols.fichaFic] || "").trim();
                    const upz = String(f[cols.upz] || "Sin UPZ").trim();
                    if (id) mapFic.set(id, upz);
                });

                // Mapeo Documento -> datos personales
                const mapPer = new Map();
                dPer.forEach(p => {
                    const doc = String(p[cols.docPer] || "").trim();
                    if (doc) mapPer.set(doc, {
                        edad: p[cols.edad],
                        sexo: p[cols.sexo]
                    });
                });

                // Categorías dinámicas
                const categoriasSet = new Set();
                dTam.forEach(t => categoriasSet.add(normalizarValor(t[cols.target])));
                const categorias = Array.from(categoriasSet).sort();

                // Cruce
                const upzData = new Map();
                const totalGeneral = createEmptyRow(categorias);
                let contadores = {
                    cruzados: 0,
                    sinPer: 0,
                    sinUpz: 0
                };

                dTam.forEach(tam => {
                    const doc = String(tam[cols.docTam] || "").trim();
                    const ficha = String(tam[cols.fichaTam] || "").trim();
                    const cat = normalizarValor(tam[cols.target]);

                    const persona = mapPer.get(doc);
                    if (!persona) {
                        contadores.sinPer++;
                        return;
                    }

                    const upz = mapFic.get(ficha) || "Sin UPZ";
                    if (upz === "Sin UPZ") contadores.sinUpz++;

                    const sRaw = String(persona.sexo || "").toLowerCase();
                    let s = "I";
                    if (sRaw.includes("h") || (sRaw.includes("m") && !sRaw.includes("muj"))) s = "M";
                    else if (sRaw.includes("f") || sRaw.includes("muj")) s = "F";

                    const edadN = parseInt(persona.edad);
                    let g = null;
                    if (edadN >= 0 && edadN <= 5) g = "P";
                    else if (edadN >= 6 && edadN <= 11) g = "I";
                    else if (edadN >= 12 && edadN <= 17) g = "D";
                    else if (edadN >= 18 && edadN <= 28) g = "J";
                    else if (edadN >= 29 && edadN <= 59) g = "A";
                    else if (edadN >= 60) g = "V";
                    if (!g) return;

                    if (!upzData.has(upz)) upzData.set(upz, createEmptyRow(categorias));
                    upzData.get(upz)[cat][g][s]++;
                    totalGeneral[cat][g][s]++;
                    contadores.cruzados++;
                });

                if (tokensNecesarios > 0) {
                    try {
                        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                                        || document.querySelector('input[name="_token"]')?.value;

                        const response = await fetch('/validator/update-tokens', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken
                            },
                            body: JSON.stringify({
                                tokens_consumed: tokensNecesarios
                            })
                        });

                        const data = await response.json();

                        if (data.success) {
                            userTokens = data.tokens_left;
                            console.log('Tokens actualizados tras el cruce:', userTokens);

                            // Si tienes un contador visual en el HTML de esta vista, lo actualizamos
                            const tokensDisplay = document.getElementById('tokens-counter');
                            if (tokensDisplay) tokensDisplay.textContent = userTokens;
                        } else {
                            alert('Problema al registrar tus tokens: ' + data.message);
                        }
                    } catch (error) {
                        console.error('Error al comunicar el cobro de tokens:', error);
                    }
                }

                // Guardar para descarga
                _lastCategorias = categorias;
                _lastDataMap = upzData;
                _lastTotal = totalGeneral;

                renderTable(categorias, upzData, totalGeneral);

                $('stats-summary').innerHTML =
                    `
            <div class="stat-card"><div class="val">${contadores.cruzados.toLocaleString()}</div><div class="lbl">Registros Cruzados</div></div>
            <div class="stat-card" style="border-left:4px solid #eab308"><div class="val">${categorias.length}</div><div class="lbl">Categorías Detectadas</div></div>
            <div class="stat-card" style="border-left:4px solid #22c55e"><div class="val">${upzData.size}</div><div class="lbl">Zonas (UPZ)</div></div>`;

                $('results').classList.add('show');
                $('status').className = 'status ok';
                $('stext').textContent =
                    `Éxito: ${contadores.cruzados} procesados. Sin persona: ${contadores.sinPer}.`;
                $('btnDl').classList.add('visible');

            } catch (err) {
                console.error(err);
                $('status').className = 'status err';
                $('stext').textContent = 'Error: ' + err.message;
            } finally {
                $('btnRun').disabled = false;
                $('btnRun').innerHTML = '▶ Ejecutar Cruce';
            }
        };

        /* ─────────────────────────────────────────────
           RENDER TABLA — encabezados sin rowspan/colspan anidados problemáticos
           Estructura:
             Fila 1: UPZ (rowspan=3) | CAT colspan=NGrupos*4+1 (x nCats) | TOTAL FILA (rowspan=3)
             Fila 2: dentro de cada CAT → GRUPO colspan=4 (x NGrupos) | Total Cat (rowspan=2)
             Fila 3: dentro de cada GRUPO → M | F | I | Σ
        ───────────────────────────────────────────── */
        function renderTable(categorias, dataMap, totalGeneral) {
            const nG = GRUPOS.length; // 6
            const colsPerCat = nG * 4 + 1; // 6 grupos × 4 sexos + 1 totalCat = 25

            let html = '<thead>';

            /* ── FILA 1: UPZ | [CAT...] | TOTAL ── */
            html += '<tr>';
            html += `<th rowspan="3" class="th-upz">UPZ / UPR</th>`;
            categorias.forEach(cat => {
                html += `<th colspan="${colsPerCat}" class="th-cat">${cat}</th>`;
            });
            html += `<th rowspan="3" class="th-total-col">TOTAL<br>FILA</th>`;
            html += '</tr>';

            /* ── FILA 2: dentro de cada CAT → [GRUPO...] | TotalCat ── */
            html += '<tr>';
            categorias.forEach(() => {
                GRUPOS.forEach(g => {
                    html += `<th colspan="4" class="th-grupo">${GRUPO_LABEL[g]}</th>`;
                });
                html += `<th rowspan="2" class="th-totalcat">Total<br>Cat.</th>`;
            });
            html += '</tr>';

            /* ── FILA 3: dentro de cada GRUPO → M F I Σ ── */
            html += '<tr>';
            categorias.forEach(() => {
                GRUPOS.forEach(() => {
                    html += '<th style="color:#7ec8ff">M</th>';
                    html += '<th style="color:#f07bff">F</th>';
                    html += '<th style="color:#888">I</th>';
                    html += '<th style="color:#ccc">Σ</th>';
                });
                // La celda TotalCat ya tiene rowspan=2 arriba, no se repite aquí
            });
            html += '</tr></thead><tbody>';

            /* ── FILAS DE DATOS ── */
            const sortedUPZs = Array.from(dataMap.keys()).sort((a, b) => a.localeCompare(b, undefined, {
                numeric: true
            }));

            const buildRow = (label, rowData, isTotal) => {
                let tr = `<tr><td class="${isTotal?'td-total-row':'td-upz'}">${label}</td>`;
                let sumaFila = 0;

                categorias.forEach(cat => {
                    let sumaCat = 0;
                    GRUPOS.forEach(g => {
                        const d = rowData[cat][g];
                        const st = d.M + d.F + d.I;
                        sumaCat += st;
                        tr += `<td class="num num-m">${d.M||''}</td>`;
                        tr += `<td class="num num-f">${d.F||''}</td>`;
                        tr += `<td class="num num-i">${d.I||''}</td>`;
                        tr += `<td class="num num-st">${st||''}</td>`;
                    });
                    tr += `<td class="num num-cat">${sumaCat||''}</td>`;
                    sumaFila += sumaCat;
                });

                tr += `<td class="num num-total">${sumaFila}</td></tr>`;
                return tr;
            };

            sortedUPZs.forEach(upz => {
                html += buildRow(upz, dataMap.get(upz), false);
            });
            html += buildRow('TOTAL GENERAL', totalGeneral, true);

            $('main-table').innerHTML = html + '</tbody>';
        }

        /* ─────────────────────────────────────────────
           DESCARGA EXCEL
        ───────────────────────────────────────────── */
        $('btnDl').onclick = () => {
            if (!_lastDataMap) return;
            exportToExcel(_lastCategorias, _lastDataMap, _lastTotal);
        };

        function exportToExcel(categorias, dataMap, totalGeneral) {
            // Usamos XLSXStyle (xlsx-js-style) que carga como window.XLSXStyle
            const XS = window.XLSXStyle || window.XLSX;
            const nG = GRUPOS.length; // 6
            const nC = categorias.length;

            // ── Paleta de colores por nivel de riesgo ────────────────────────────
            const CAT_COLORS = [{
                    bg: '1A4731',
                    fg: '22C55E'
                }, // Bajo     — verde oscuro
                {
                    bg: '1A2E00',
                    fg: '84CC16'
                }, // Lig      — verde lima
                {
                    bg: '2D2000',
                    fg: 'EAB308'
                }, // Moderado — amarillo
                {
                    bg: '2D1200',
                    fg: 'F97316'
                }, // Alto     — naranja
                {
                    bg: '2D0000',
                    fg: 'EF4444'
                }, // Muy Alto — rojo
            ];
            const getCatColor = i => CAT_COLORS[i % CAT_COLORS.length];

            // ── Estilos base ─────────────────────────────────────────────────────
            const border = {
                top: {
                    style: 'thin',
                    color: {
                        rgb: '2A3A5A'
                    }
                },
                bottom: {
                    style: 'thin',
                    color: {
                        rgb: '2A3A5A'
                    }
                },
                left: {
                    style: 'thin',
                    color: {
                        rgb: '2A3A5A'
                    }
                },
                right: {
                    style: 'thin',
                    color: {
                        rgb: '2A3A5A'
                    }
                }
            };
            const borderMed = side => ({
                style: 'medium',
                color: {
                    rgb: '4A7FD4'
                }
            });

            const st = (bg, fg, bold = false, sz = 9, wrap = false, ha = 'center', va = 'center') => ({
                fill: {
                    fgColor: {
                        rgb: bg
                    }
                },
                font: {
                    name: 'Arial',
                    sz,
                    bold,
                    color: {
                        rgb: fg
                    }
                },
                alignment: {
                    horizontal: ha,
                    vertical: va,
                    wrapText: wrap
                },
                border
            });

            // ── Construir la hoja celda a celda ──────────────────────────────────
            // Mapeamos col index → col letter helper
            const colLetter = n => {
                let s = '';
                n++;
                while (n > 0) {
                    s = String.fromCharCode(65 + (n - 1) % 26) + s;
                    n = Math.floor((n - 1) / 26);
                }
                return s;
            };
            const addr = (r, c) => `${colLetter(c)}${r+1}`;

            // Hoja vacía
            const ws = {};
            const merges = [];
            const colWidths = [];

            let colIdx = 0; // cursor de columna actual

            // ── Columna UPZ (col 0) ──────────────────────────────────────────────
            // Tres filas de encabezado → merge vertical rows 0-2
            ws[addr(0, 0)] = {
                v: 'UPZ / UPR',
                s: st('0A1428', '00D4FF', true, 11, false, 'left')
            };
            ws[addr(1, 0)] = {
                v: '',
                s: st('0A1428', '00D4FF', true, 11)
            };
            ws[addr(2, 0)] = {
                v: '',
                s: st('0A1428', '00D4FF', true, 11)
            };
            merges.push({
                s: {
                    r: 0,
                    c: 0
                },
                e: {
                    r: 2,
                    c: 0
                }
            });
            colWidths.push({
                wch: 22
            });
            colIdx = 1;

            // ── Columnas por categoría ───────────────────────────────────────────
            categorias.forEach((cat, ci) => {
                const cc = getCatColor(ci);
                const catStart = colIdx;
                const catEnd = colIdx + nG * 4; // +1 para totalCat, pero lo ponemos después

                // Fila 1: nombre categoría (merge todo el bloque)
                ws[addr(0, catStart)] = {
                    v: cat,
                    s: st(cc.bg, cc.fg, true, 10, false, 'center')
                };
                for (let k = 1; k < nG * 4 + 1; k++) {
                    ws[addr(0, catStart + k)] = {
                        v: '',
                        s: st(cc.bg, cc.fg, true, 10)
                    };
                }
                merges.push({
                    s: {
                        r: 0,
                        c: catStart
                    },
                    e: {
                        r: 0,
                        c: catStart + nG * 4
                    }
                });

                // Fila 2 + 3: grupos de edad (cada uno = 4 cols: M F I Σ)
                let gCol = catStart;
                GRUPOS.forEach((g, gi) => {
                    const lbl = GRUPO_LABEL[g].replace('\n', ' ');
                    const gbg = gi % 2 === 0 ? '0D1929' : '111C30';

                    // Fila 2: etiqueta grupo (merge 4 cols)
                    ws[addr(1, gCol)] = {
                        v: lbl,
                        s: st(gbg, 'C8D8F0', true, 8, true, 'center')
                    };
                    for (let k = 1; k < 4; k++) ws[addr(1, gCol + k)] = {
                        v: '',
                        s: st(gbg, 'C8D8F0')
                    };
                    merges.push({
                        s: {
                            r: 1,
                            c: gCol
                        },
                        e: {
                            r: 1,
                            c: gCol + 3
                        }
                    });

                    // Fila 3: M F I Σ
                    ws[addr(2, gCol)] = {
                        v: 'M',
                        s: st('0A1428', '7EC8FF', true, 9)
                    };
                    ws[addr(2, gCol + 1)] = {
                        v: 'F',
                        s: st('0A1428', 'F07BFF', true, 9)
                    };
                    ws[addr(2, gCol + 2)] = {
                        v: 'I',
                        s: st('0A1428', '888888', true, 9)
                    };
                    ws[addr(2, gCol + 3)] = {
                        v: 'Σ',
                        s: st('0A1428', 'FFFFFF', true, 9)
                    };

                    colWidths.push({
                        wch: 4
                    }, {
                        wch: 4
                    }, {
                        wch: 4
                    }, {
                        wch: 5
                    });
                    gCol += 4;
                });

                // Total Cat (col = catStart + nG*4)
                ws[addr(1, gCol)] = {
                    v: 'Total\nCat.',
                    s: st('1A2840', cc.fg, true, 9, true, 'center')
                };
                ws[addr(2, gCol)] = {
                    v: '',
                    s: st('1A2840', cc.fg)
                };
                merges.push({
                    s: {
                        r: 1,
                        c: gCol
                    },
                    e: {
                        r: 2,
                        c: gCol
                    }
                });
                colWidths.push({
                    wch: 8
                });

                colIdx = gCol + 1;
            });

            // ── Columna TOTAL FILA (última) ──────────────────────────────────────
            ws[addr(0, colIdx)] = {
                v: 'TOTAL\nFILA',
                s: st('00D4FF', '000000', true, 10, true, 'center')
            };
            ws[addr(1, colIdx)] = {
                v: '',
                s: st('00D4FF', '000000')
            };
            ws[addr(2, colIdx)] = {
                v: '',
                s: st('00D4FF', '000000')
            };
            merges.push({
                s: {
                    r: 0,
                    c: colIdx
                },
                e: {
                    r: 2,
                    c: colIdx
                }
            });
            colWidths.push({
                wch: 10
            });
            const totalFilaCol = colIdx;

            // ── Filas de datos ───────────────────────────────────────────────────
            const sortedUPZs = Array.from(dataMap.keys()).sort((a, b) => a.localeCompare(b, undefined, {
                numeric: true
            }));

            const writeDataRow = (rowIdx, label, rowData, isTotal) => {
                const upzStyle = isTotal ?
                    st('0A1428', 'FFFFFF', true, 11, false, 'left') :
                    st('080F1E', '00D4FF', false, 11, false, 'left');
                ws[addr(rowIdx, 0)] = {
                    v: label,
                    s: upzStyle
                };

                let c = 1;
                let sumaFila = 0;

                categorias.forEach((cat, ci) => {
                    const cc = getCatColor(ci);
                    let sumaCat = 0;

                    GRUPOS.forEach((g, gi) => {
                        const d = rowData[cat][g];
                        const st_ = d.M + d.F + d.I;
                        sumaCat += st_;
                        const bg = isTotal ? '111828' : (gi % 2 === 0 ? '080F1E' : '0C1420');

                        const numSt = (fg, bold = false) => ({
                            fill: {
                                fgColor: {
                                    rgb: isTotal ? '131D2E' : bg
                                }
                            },
                            font: {
                                name: 'Arial',
                                sz: 10,
                                bold,
                                color: {
                                    rgb: fg
                                }
                            },
                            alignment: {
                                horizontal: 'center',
                                vertical: 'center'
                            },
                            border
                        });

                        ws[addr(rowIdx, c)] = {
                            v: d.M || '',
                            s: numSt(isTotal ? '7EC8FF' : '7EC8FF')
                        };
                        ws[addr(rowIdx, c + 1)] = {
                            v: d.F || '',
                            s: numSt(isTotal ? 'F07BFF' : 'F07BFF')
                        };
                        ws[addr(rowIdx, c + 2)] = {
                            v: d.I || '',
                            s: numSt('666666')
                        };
                        ws[addr(rowIdx, c + 3)] = {
                            v: st_ || '',
                            s: numSt('FFFFFF', true)
                        };
                        c += 4;
                    });

                    // Total Cat
                    ws[addr(rowIdx, c)] = {
                        v: sumaCat,
                        s: {
                            fill: {
                                fgColor: {
                                    rgb: isTotal ? '0D2035' : '0A1E30'
                                }
                            },
                            font: {
                                name: 'Arial',
                                sz: 10,
                                bold: true,
                                color: {
                                    rgb: cc.fg
                                }
                            },
                            alignment: {
                                horizontal: 'center',
                                vertical: 'center'
                            },
                            border
                        }
                    };
                    sumaFila += sumaCat;
                    c++;
                });

                // Total Fila
                ws[addr(rowIdx, totalFilaCol)] = {
                    v: sumaFila,
                    s: {
                        fill: {
                            fgColor: {
                                rgb: isTotal ? '00A0C0' : '007A94'
                            }
                        },
                        font: {
                            name: 'Arial',
                            sz: 11,
                            bold: true,
                            color: {
                                rgb: '000000'
                            }
                        },
                        alignment: {
                            horizontal: 'center',
                            vertical: 'center'
                        },
                        border
                    }
                };
            };

            let rowIdx = 3;
            sortedUPZs.forEach(upz => {
                writeDataRow(rowIdx++, upz, dataMap.get(upz), false);
            });
            writeDataRow(rowIdx, 'TOTAL GENERAL', totalGeneral, true);

            // ── Rango y metadatos ─────────────────────────────────────────────────
            const totalRows = rowIdx + 1;
            const totalCols = totalFilaCol + 1;
            ws['!ref'] = `A1:${colLetter(totalCols-1)}${totalRows}`;
            ws['!merges'] = merges;
            ws['!cols'] = colWidths;

            // Alturas de fila para encabezados
            ws['!rows'] = [{
                    hpt: 22
                }, // fila 1: nombres categoría
                {
                    hpt: 30
                }, // fila 2: grupos edad
                {
                    hpt: 16
                }, // fila 3: M F I Σ
            ];

            // ── Libro y descarga ──────────────────────────────────────────────────
            const wb = XS.utils.book_new();
            XS.utils.book_append_sheet(wb, ws, 'FINDRISC');
            XS.writeFile(wb, `FINDRISC_cruce_${new Date().toISOString().slice(0,10)}.xlsx`);
        }
    </script>
</body>

</html>
