<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="refresh" content="1800">
    <title>Trakio TV - Actividad en tiempo real</title>
    <link rel="icon" href="{{ asset('img/logo_temp.png') }}">
    @vite(['resources/css/global.css'])
    <style>
        :root {
            color-scheme: light;
            --tv-plane: #d9e4f5;
            --tv-surface: #ffffff;
            --tv-ink: #0b1220;
            --tv-ink-2: #1f2b40;
            --tv-ink-muted: #4b5a75;
            --tv-border: rgba(11, 18, 32, 0.24);
            --tv-blue: #0b57d0;
            --tv-aqua: #0b7d8a;
            --tv-good: #15803d;
            --tv-warning: #b45309;
            --tv-critical: #dc2626;
            --tv-flash: rgba(11, 87, 208, 0.5);
        }

        * {
            box-sizing: border-box;
        }

        html {
            /* Escala pensada para proyectores/TVs de baja resoluci&oacute;n. */
            font-size: clamp(13px, 0.4vw + 7px, 16px);
        }

        html,
        body {
            margin: 0;
            height: 100%;
            background: var(--tv-plane);
            color: var(--tv-ink);
            font-family: system-ui, -apple-system, "Segoe UI", sans-serif;
            -webkit-font-smoothing: antialiased;
            overflow: hidden;
        }

        .tv-shell {
            display: flex;
            flex-direction: column;
            height: 100vh;
            padding: 0.7rem 1rem;
            gap: 0.6rem;
        }

        .tv-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-shrink: 0;
        }

        .tv-title {
            display: flex;
            align-items: baseline;
            gap: 14px;
        }

        .tv-title h1 {
            font-size: 1.3rem;
            margin: 0;
            font-weight: 800;
            letter-spacing: 0.2px;
        }

        .tv-title span {
            color: var(--tv-ink-muted);
            font-size: 0.85rem;
            font-weight: 500;
        }

        .tv-clock {
            display: flex;
            align-items: center;
            gap: 1.2rem;
        }

        .tv-stat {
            background: var(--tv-surface);
            border: 1px solid var(--tv-border);
            border-radius: 10px;
            padding: 0.25rem 0.8rem;
            text-align: center;
            min-width: 6.5rem;
            box-shadow: 0 1px 3px rgba(11, 18, 32, 0.06);
        }

        .tv-stat .tv-stat-value {
            font-size: 1.4rem;
            font-weight: 800;
            font-variant-numeric: tabular-nums;
            line-height: 1;
        }

        .tv-stat .tv-stat-label {
            font-size: 0.6rem;
            color: var(--tv-ink-muted);
            text-transform: uppercase;
            letter-spacing: 0.06em;
            margin-top: 3px;
            font-weight: 600;
        }

        #tv-clock-time {
            font-size: 1.5rem;
            font-weight: 800;
            font-variant-numeric: tabular-nums;
            line-height: 1;
        }

        #tv-clock-date {
            color: var(--tv-ink-muted);
            font-size: 0.72rem;
            text-transform: capitalize;
            font-weight: 500;
        }

        .tv-grid {
            flex: 1;
            display: grid;
            grid-template-columns: 1fr 1fr;
            grid-template-rows: 1fr 1fr 0.85fr;
            gap: 0.6rem;
            min-height: 0;
        }

        .tv-panel-wide {
            grid-column: 1 / -1;
        }

        .tv-chart-wrap {
            flex: 1;
            position: relative;
            padding: 10px 18px 4px;
            min-height: 0;
        }

        .tv-chart-wrap svg {
            width: 100%;
            height: 100%;
            overflow: visible;
        }

        .tv-chart-live {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 0.68rem;
            font-weight: 600;
            color: var(--tv-ink-muted);
        }

        .tv-chart-total {
            font-weight: 800;
            color: var(--tv-blue);
        }

        .tv-chart-live .tv-pulse-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--tv-blue);
            animation: tvpulse 1.6s ease-in-out infinite;
        }

        @keyframes tvpulse {
            0% { box-shadow: 0 0 0 0 rgba(11, 87, 208, 0.5); }
            70% { box-shadow: 0 0 0 8px rgba(11, 87, 208, 0); }
            100% { box-shadow: 0 0 0 0 rgba(11, 87, 208, 0); }
        }

        .tv-chart-tooltip {
            position: absolute;
            pointer-events: none;
            background: var(--tv-ink);
            color: var(--tv-surface);
            font-size: 0.72rem;
            font-weight: 600;
            padding: 4px 8px;
            border-radius: 6px;
            transform: translate(-50%, -110%);
            white-space: nowrap;
            opacity: 0;
            transition: opacity 0.1s ease;
            z-index: 5;
        }

        .tv-panel {
            background: var(--tv-surface);
            border: 1.5px solid var(--tv-border);
            border-radius: 12px;
            display: flex;
            flex-direction: column;
            min-height: 0;
            overflow: hidden;
            box-shadow: 0 1px 4px rgba(11, 18, 32, 0.05);
        }

        .tv-chips {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
            padding: 6px 12px 0;
            flex-shrink: 0;
        }

        .tv-chip {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 2px 9px;
            border-radius: 20px;
            font-size: 0.68rem;
            font-weight: 700;
            background: var(--tv-blue);
            color: #ffffff;
        }

        .tv-chip b {
            font-variant-numeric: tabular-nums;
        }

        .tv-panel-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.4rem 0.7rem;
            border-bottom: 1px solid var(--tv-border);
            flex-shrink: 0;
        }

        .tv-panel-head h2 {
            margin: 0;
            font-size: 0.92rem;
            font-weight: 800;
        }

        .tv-dot {
            width: 9px;
            height: 9px;
            border-radius: 50%;
            flex-shrink: 0;
        }

        .tv-panel-body {
            flex: 1;
            overflow-y: auto;
            padding: 2px 0.65rem;
        }

        .tv-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            padding: 0.4rem 4px;
            border-bottom: 1px solid var(--tv-border);
            font-size: 0.83rem;
        }

        .tv-row:last-child {
            border-bottom: none;
        }

        .tv-row.tv-flash {
            animation: tvflash 3.2s ease-out;
        }

        @keyframes tvflash {
            0% {
                background: var(--tv-flash);
                box-shadow: inset 5px 0 0 0 var(--tv-blue);
            }

            65% {
                background: var(--tv-flash);
                box-shadow: inset 5px 0 0 0 var(--tv-blue);
            }

            100% {
                background: transparent;
                box-shadow: inset 5px 0 0 0 transparent;
            }
        }

        .tv-row-main {
            display: flex;
            flex-direction: column;
            min-width: 0;
        }

        .tv-row-main strong {
            color: var(--tv-ink);
            font-weight: 600;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .tv-row-main small {
            color: var(--tv-ink-muted);
            font-size: 0.72rem;
        }

        .tv-row-side {
            flex-shrink: 0;
            text-align: right;
            color: var(--tv-ink-2);
            font-size: 0.74rem;
            font-variant-numeric: tabular-nums;
            line-height: 1.3;
        }

        .tv-badge {
            display: inline-block;
            padding: 1px 8px;
            border-radius: 20px;
            font-size: 0.68rem;
            font-weight: 700;
            white-space: nowrap;
        }

        /* Los estados de ficha vienen de global.css con fondos translucidos pensados
           para tema oscuro: aqu&iacute; se refuerzan a colores s&oacute;lidos para que se
           lean bien en un proyector con luz de d&iacute;a. */
        .tv-badge.status-pending { background: #b45309; color: #fff; border-color: #b45309; }
        .tv-badge.status-rejected { background: #dc2626; color: #fff; border-color: #dc2626; }
        .tv-badge.status-sealed { background: #15803d; color: #fff; border-color: #15803d; }
        .tv-badge.status-fix-punteo { background: #be185d; color: #fff; border-color: #be185d; }
        .tv-badge.status-delivered { background: #0b57d0; color: #fff; border-color: #0b57d0; }
        .tv-badge.status-received-gesi { background: #0b7d8a; color: #fff; border-color: #0b7d8a; }
        .tv-badge.status-delivered-digitizer { background: #6d28d9; color: #fff; border-color: #6d28d9; }
        .tv-badge.status-typed { background: #4338ca; color: #fff; border-color: #4338ca; }
        .tv-badge.status-returned { background: #c2410c; color: #fff; border-color: #c2410c; }
        .tv-badge.status-returned-process { background: #a16207; color: #fff; border-color: #a16207; }
        .tv-badge.status-professional { background: #991b1b; color: #fff; border-color: #991b1b; }
        .tv-badge.status-facilitator { background: #a21caf; color: #fff; border-color: #a21caf; }
        .tv-badge.status-received-environment { background: #0e7490; color: #fff; border-color: #0e7490; }
        .tv-badge.status-unknown { background: #52525b; color: #fff; border-color: #52525b; }

        .tv-wait {
            display: inline-block;
            padding: 1px 8px;
            border-radius: 20px;
            font-weight: 700;
            font-size: 0.76rem;
        }

        .tv-wait-good {
            color: #ffffff;
            background: var(--tv-good);
        }

        .tv-wait-warning {
            color: #ffffff;
            background: var(--tv-warning);
        }

        .tv-wait-critical {
            color: #ffffff;
            background: var(--tv-critical);
        }

        .tv-empty {
            color: var(--tv-ink-muted);
            font-size: 0.85rem;
            padding: 1rem 4px;
            text-align: center;
        }

        .tv-arrow {
            color: var(--tv-ink-muted);
            margin: 0 4px;
        }

        .tv-offline {
            position: fixed;
            top: 12px;
            right: 24px;
            background: var(--tv-critical);
            color: #ffffff;
            font-weight: 700;
            padding: 4px 14px;
            border-radius: 20px;
            font-size: 0.8rem;
            display: none;
            z-index: 10;
        }
    </style>
</head>

<body>
    <div class="tv-offline" id="tv-offline">Sin conexión con el servidor</div>

    <div class="tv-shell">
        <div class="tv-header">
            <div class="tv-title">
                <h1>Actividad en tiempo real</h1>
                <span>Trakio &middot; Oficina GESI</span>
            </div>
            <div class="tv-clock">
                <div class="tv-stat">
                    <div class="tv-stat-value" id="tv-count-cola">&mdash;</div>
                    <div class="tv-stat-label">Fichas por digitar</div>
                </div>
                <div>
                    <div id="tv-clock-time">--:--:--</div>
                    <div id="tv-clock-date"></div>
                </div>
            </div>
        </div>

        <div class="tv-grid">
            <div class="tv-panel">
                <div class="tv-panel-head">
                    <h2>Formatos solicitados</h2>
                    <span class="tv-dot" style="background: var(--tv-blue)"></span>
                </div>
                <div class="tv-panel-body" id="panel-fichas"></div>
            </div>

            <div class="tv-panel">
                <div class="tv-panel-head">
                    <h2>Cambios de estado de fichas</h2>
                    <span class="tv-dot" style="background: var(--tv-good)"></span>
                </div>
                <div class="tv-panel-body" id="panel-estados"></div>
            </div>

            <div class="tv-panel">
                <div class="tv-panel-head">
                    <h2>Horas cargadas por digitadores</h2>
                    <span class="tv-dot" style="background: var(--tv-aqua)"></span>
                </div>
                <div class="tv-panel-body" id="panel-horas"></div>
            </div>

            <div class="tv-panel">
                <div class="tv-panel-head">
                    <h2>Fichas digitadas &middot; &uacute;ltimas 8 horas</h2>
                    <div class="tv-chart-live">
                        <span class="tv-chart-total" id="chart-total"></span>
                        <span class="tv-pulse-dot"></span> en vivo
                    </div>
                </div>
                <div class="tv-chart-wrap">
                    <svg id="chart-svg" viewBox="0 0 1000 180" preserveAspectRatio="none"></svg>
                    <div class="tv-chart-tooltip" id="chart-tooltip"></div>
                </div>
            </div>

            <div class="tv-panel tv-panel-wide">
                <div class="tv-panel-head">
                    <h2>Fichas recibidas por GESI &middot; pendientes por digitar</h2>
                    <span class="tv-dot" style="background: var(--tv-warning)"></span>
                </div>
                <div class="tv-chips" id="panel-cola-summary"></div>
                <div class="tv-panel-body" id="panel-cola"></div>
            </div>
        </div>
    </div>

    <script>
        const DATA_URL = "{{ route('tv.dashboard.data') }}";
        const POLL_MS = 4000;
        const seenIds = { fichas: new Set(), horas: new Set(), estados: new Set() };
        let firstLoad = true;

        function tickClock() {
            const now = new Date();
            document.getElementById('tv-clock-time').textContent = now.toLocaleTimeString('es-CO', { hour12: false });
            document.getElementById('tv-clock-date').textContent = now.toLocaleDateString('es-CO', {
                weekday: 'long', day: 'numeric', month: 'long'
            });
        }
        tickClock();
        setInterval(tickClock, 1000);

        function timeAgo(iso) {
            if (!iso) return '';
            const diffMs = Date.now() - new Date(iso).getTime();
            const mins = Math.max(0, Math.floor(diffMs / 60000));
            if (mins < 1) return 'hace instantes';
            if (mins < 60) return `hace ${mins} min`;
            const hours = Math.floor(mins / 60);
            if (hours < 24) return `hace ${hours} h ${mins % 60}m`;
            return `hace ${Math.floor(hours / 24)} d`;
        }

        function formatWait(mins) {
            if (mins === null || mins === undefined) return '0 min';
            if (mins < 60) return `${mins} min`;
            const hours = Math.floor(mins / 60);
            const restMin = mins % 60;
            if (hours < 24) return restMin > 0 ? `${hours} h ${restMin} min` : `${hours} h`;
            const days = Math.floor(hours / 24);
            const restHours = hours % 24;
            return restHours > 0 ? `${days} d ${restHours} h` : `${days} d`;
        }

        function formatDuration(seconds) {
            if (seconds === null || seconds === undefined || seconds <= 0) return '0 min';
            if (seconds < 60) return `${Math.round(seconds)} seg`;
            const minutes = seconds / 60;
            if (minutes < 60) return `${Math.round(minutes)} min`;
            const hours = seconds / 3600;
            return `${hours.toFixed(hours < 10 ? 1 : 0)} h`;
        }

        function waitClass(mins) {
            if (mins === null || mins === undefined) return '';
            if (mins >= 60) return 'tv-wait-critical';
            if (mins >= 30) return 'tv-wait-warning';
            return 'tv-wait-good';
        }

        function renderEmpty(container) {
            container.innerHTML = '<div class="tv-empty">Sin novedades</div>';
        }

        function renderFichas(items) {
            const el = document.getElementById('panel-fichas');
            if (!items.length) return renderEmpty(el);
            el.innerHTML = items.map(it => `
                <div class="tv-row ${!seenIds.fichas.has(it.id) && !firstLoad ? 'tv-flash' : ''}">
                    <div class="tv-row-main">
                        <strong>${it.profesional ?? 'Profesional sin asignar'}</strong>
                        <small>${it.formato ?? 'Formato'}${it.localidad ? ' &middot; ' + it.localidad : ''}${it.entorno ? ' &middot; ' + it.entorno : ''}${it.cantidad ? ' &middot; ' + it.cantidad + ' unid.' : ''}</small>
                    </div>
                    <div class="tv-row-side">Ficha ${it.ficha ?? '#' + it.id}<br>${timeAgo(it.fecha)}</div>
                </div>
            `).join('');
            items.forEach(it => seenIds.fichas.add(it.id));
        }

        function renderHoras(items) {
            const el = document.getElementById('panel-horas');
            if (!items.length) return renderEmpty(el);
            el.innerHTML = items.map(it => `
                <div class="tv-row ${!seenIds.horas.has(it.id) && !firstLoad ? 'tv-flash' : ''}">
                    <div class="tv-row-main">
                        <strong>${it.digitador}</strong>
                        <small>${it.formato ?? ''}${it.ficha ? ' &middot; Ficha ' + it.ficha : ''}</small>
                    </div>
                    <div class="tv-row-side">${formatDuration(it.segundos)}<br>${timeAgo(it.fecha)}</div>
                </div>
            `).join('');
            items.forEach(it => seenIds.horas.add(it.id));
        }

        function renderCola(items) {
            const el = document.getElementById('panel-cola');
            if (!items.length) return renderEmpty(el);
            el.innerHTML = items.map(it => `
                <div class="tv-row">
                    <div class="tv-row-main">
                        <strong>Ficha ${it.ficha ?? '#' + it.id}</strong>
                        <small>${it.entorno ?? 'Sin entorno'}${it.formato ? ' &middot; ' + it.formato : ''}</small>
                    </div>
                    <div class="tv-row-side">
                        ${it.horas ?? 0} h estimadas<br>
                        <span class="tv-wait ${waitClass(it.espera_minutos)}">esperando ${formatWait(it.espera_minutos)}</span>
                    </div>
                </div>
            `).join('');
        }

        function renderColaResumen(items) {
            const el = document.getElementById('panel-cola-summary');
            if (!items.length) {
                el.innerHTML = '';
                return;
            }
            el.innerHTML = items.map(it => `
                <span class="tv-chip">${it.entorno} <b>${it.cantidad}</b></span>
            `).join('');
        }

        function renderEstados(items) {
            const el = document.getElementById('panel-estados');
            if (!items.length) return renderEmpty(el);
            el.innerHTML = items.map(it => `
                <div class="tv-row ${!seenIds.estados.has(it.id) && !firstLoad ? 'tv-flash' : ''}">
                    <div class="tv-row-main">
                        <strong>Ficha ${it.ficha ?? 'N/D'}</strong>
                        <small>${it.estado_anterior}<span class="tv-arrow">&rarr;</span></small>
                    </div>
                    <div class="tv-row-side">
                        <span class="tv-badge ${it.clase_nuevo}">${it.estado_nuevo}</span><br>
                        ${it.usuario} &middot; ${timeAgo(it.fecha)}
                    </div>
                </div>
            `).join('');
            items.forEach(it => seenIds.estados.add(it.id));
        }

        const CHART_W = 1000, CHART_H = 180;
        const CHART_PAD = { top: 40, right: 16, bottom: 26, left: 30 };
        let chartPoints = [];
        const chartSvg = document.getElementById('chart-svg');
        const chartTooltip = document.getElementById('chart-tooltip');
        const chartTotalEl = document.getElementById('chart-total');

        function buildChartPoints(items, max) {
            const innerW = CHART_W - CHART_PAD.left - CHART_PAD.right;
            const innerH = CHART_H - CHART_PAD.top - CHART_PAD.bottom;
            const n = items.length;
            return items.map((it, i) => ({
                x: n <= 1 ? CHART_PAD.left : CHART_PAD.left + (innerW * i) / (n - 1),
                y: CHART_PAD.top + innerH - (innerH * it.cantidad) / max,
                hora: it.hora,
                cantidad: it.cantidad,
                digitadores: it.digitadores || 0,
            }));
        }

        function renderChart(items) {
            if (!items.length) {
                chartSvg.innerHTML = '';
                chartPoints = [];
                chartTotalEl.textContent = '';
                return;
            }

            const max = Math.max(1, ...items.map(it => it.cantidad));
            const total = items.reduce((sum, it) => sum + it.cantidad, 0);
            const maxDigitadores = Math.max(0, ...items.map(it => it.digitadores || 0));
            chartTotalEl.textContent = `${total} ficha(s) · hasta ${maxDigitadores} digitador(es) a la vez`;

            chartPoints = buildChartPoints(items, max);
            const innerH = CHART_H - CHART_PAD.top - CHART_PAD.bottom;
            const baseline = CHART_PAD.top + innerH;

            const linePath = chartPoints.map((p, i) => `${i === 0 ? 'M' : 'L'} ${p.x.toFixed(1)} ${p.y.toFixed(1)}`).join(' ');
            const last = chartPoints[chartPoints.length - 1];
            const areaPath = `${linePath} L ${last.x.toFixed(1)} ${baseline} L ${chartPoints[0].x.toFixed(1)} ${baseline} Z`;

            const gridLines = [0, 0.5, 1].map(f => {
                const y = (CHART_PAD.top + innerH * (1 - f)).toFixed(1);
                const value = Math.round(max * f);
                return `
                    <line x1="${CHART_PAD.left}" y1="${y}" x2="${CHART_W - CHART_PAD.right}" y2="${y}" stroke="var(--tv-border)" stroke-width="1" />
                    <text x="${CHART_PAD.left - 8}" y="${y}" font-size="11" fill="var(--tv-ink-muted)" text-anchor="end" dominant-baseline="middle">${value}</text>
                `;
            }).join('');

            const labelStep = Math.max(1, Math.ceil(chartPoints.length / 8));
            const labels = chartPoints.map((p, i) => {
                const isLast = i === chartPoints.length - 1;
                if (i % labelStep !== 0 && !isLast) return '';
                return isLast
                    ? `<text x="${p.x.toFixed(1)}" y="${CHART_H - 6}" font-size="11" font-weight="700" fill="var(--tv-blue)" text-anchor="end">Ahora</text>`
                    : `<text x="${p.x.toFixed(1)}" y="${CHART_H - 6}" font-size="11" fill="var(--tv-ink-muted)" text-anchor="middle">${p.hora}</text>`;
            }).join('');

            const valueLabels = chartPoints.map(p => {
                if (!p.cantidad) return '';
                const digitadoresLabel = p.digitadores
                    ? `<text x="${p.x.toFixed(1)}" y="${(p.y - 23).toFixed(1)}" font-size="10" font-weight="700" fill="var(--tv-aqua)" text-anchor="middle">${p.digitadores} dig.</text>`
                    : '';
                return `
                    <text x="${p.x.toFixed(1)}" y="${(p.y - 10).toFixed(1)}" font-size="12" font-weight="700" fill="var(--tv-ink-2)" text-anchor="middle">${p.cantidad}</text>
                    ${digitadoresLabel}
                `;
            }).join('');

            chartSvg.innerHTML = `
                ${gridLines}
                <line x1="${last.x.toFixed(1)}" y1="${CHART_PAD.top}" x2="${last.x.toFixed(1)}" y2="${baseline}" stroke="var(--tv-blue)" stroke-width="1" stroke-dasharray="3 3" opacity="0.35" />
                <path d="${areaPath}" fill="var(--tv-blue)" opacity="0.10" stroke="none" />
                <path d="${linePath}" fill="none" stroke="var(--tv-blue)" stroke-width="2.5" stroke-linejoin="round" stroke-linecap="round" />
                ${valueLabels}
                ${labels}
                <circle cx="${last.x.toFixed(1)}" cy="${last.y.toFixed(1)}" r="8" fill="var(--tv-blue)" opacity="0.28">
                    <animate attributeName="r" values="8;16;8" dur="1.8s" repeatCount="indefinite" />
                    <animate attributeName="opacity" values="0.28;0;0.28" dur="1.8s" repeatCount="indefinite" />
                </circle>
                <circle cx="${last.x.toFixed(1)}" cy="${last.y.toFixed(1)}" r="5" fill="var(--tv-blue)" stroke="var(--tv-surface)" stroke-width="2" />
            `;
        }

        chartSvg.addEventListener('mousemove', (e) => {
            if (!chartPoints.length) return;
            const rect = chartSvg.getBoundingClientRect();
            const relX = (e.clientX - rect.left) / rect.width * CHART_W;
            let nearest = 0;
            let nearestDist = Infinity;
            chartPoints.forEach((p, i) => {
                const d = Math.abs(p.x - relX);
                if (d < nearestDist) { nearestDist = d; nearest = i; }
            });
            const p = chartPoints[nearest];
            chartTooltip.textContent = `${p.hora} — ${p.cantidad} ficha(s) · ${p.digitadores} digitador(es)`;
            chartTooltip.style.left = (p.x / CHART_W * 100) + '%';
            chartTooltip.style.top = (p.y / CHART_H * 100) + '%';
            chartTooltip.style.opacity = '1';
        });

        chartSvg.addEventListener('mouseleave', () => {
            chartTooltip.style.opacity = '0';
        });

        async function poll() {
            try {
                const res = await fetch(DATA_URL, { headers: { 'Accept': 'application/json' } });
                if (!res.ok) throw new Error('bad status');
                const data = await res.json();

                document.getElementById('tv-offline').style.display = 'none';
                document.getElementById('tv-count-cola').textContent = data.contadores?.cola_digitacion ?? 0;

                renderFichas(data.fichas_solicitadas || []);
                renderHoras(data.horas_cargadas || []);
                renderCola(data.cola_digitacion || []);
                renderColaResumen(data.cola_por_entorno || []);
                renderEstados(data.cambios_estado || []);
                renderChart(data.produccion_hoy || []);

                firstLoad = false;
            } catch (e) {
                document.getElementById('tv-offline').style.display = 'block';
            }
        }

        poll();
        setInterval(poll, POLL_MS);
    </script>
</body>

</html>
