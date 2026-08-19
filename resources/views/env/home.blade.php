@extends('layouts.env.navigation')

@section('main')

{{-- 🎨 ESTILOS — usa el sistema de diseño env (tokens --shell-*, claro/oscuro) --}}
<style>
    /* Filtros de entorno (botones tipo chip) */
    .env-entorno-filters {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .filter-button {
        background: var(--shell-surface-2);
        border: 1px solid var(--shell-border-strong);
        color: var(--shell-muted);
        font-family: var(--shell-sans);
        padding: 0.45rem 1rem;
        border-radius: 6px;
        font-size: 0.82rem;
        font-weight: 500;
        cursor: pointer;
        transition: 0.2s;
    }

    .filter-button:hover {
        color: var(--shell-text);
        border-color: var(--shell-text);
    }

    .filter-button.active {
        background: var(--shell-red);
        border-color: var(--shell-red);
        color: #fff;
    }

    .env-dashboard-selects {
        display: flex;
        gap: 0.75rem;
        flex-wrap: wrap;
    }

    /* Layout de gráficos */
    .grid-layout-charts {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 1.25rem;
    }

    @media (max-width: 1100px) {
        .grid-layout-charts {
            grid-template-columns: 1fr;
        }
    }

    .kpi-value-warning {
        color: var(--shell-warning);
    }

    /* Tabla top usuarios */
    .scrollable {
        max-height: 380px;
        overflow-y: auto;
        scrollbar-width: thin;
    }

    .table-custom {
        width: 100%;
        font-size: 0.85rem;
        color: var(--shell-text);
    }

    .table-custom thead th {
        color: var(--shell-red);
        font-family: var(--shell-mono);
        font-size: 11px;
        letter-spacing: 1px;
        text-transform: uppercase;
        text-align: left;
        padding-bottom: 10px;
        border-bottom: 1px solid var(--shell-border);
    }

    .table-custom td {
        padding: 10px 5px;
        border-bottom: 1px solid var(--shell-border);
    }

    .tasa-error-value {
        position: absolute;
        bottom: 15%;
        font-family: var(--shell-head);
        font-weight: 800;
        font-size: 1.4rem;
        color: var(--shell-text);
    }

    @media (max-width: 576px) {
        .env-dashboard-selects {
            flex-direction: column;
            width: 100%;
        }

        .env-dashboard-selects .form-select-sm {
            width: 100%;
        }
    }
</style>

<div class="env-page-header">
    <div>
        <span class="env-page-tag">ENTORNO / DASHBOARD</span>
        <h1 class="env-page-title">Panel de control</h1>
        <p class="env-page-subtitle">Resumen de productividad, observaciones y flujo de consecutivos.</p>
    </div>
</div>

{{-- 🧩 SECCIÓN FILTROS --}}
<div class="env-filter-bar">
    <div class="env-entorno-filters">
        <button class="filter-button active" data-filter-type="entorno" data-value="TODOS">Todos</button>
        @foreach ($availableEntornos as $entorno)
            @if ($entorno->id != 1)
                <button class="filter-button" data-filter-type="entorno" data-value="{{ $entorno->id }}">{{ $entorno->entorno }}</button>
            @endif
        @endforeach
    </div>

    <div class="env-dashboard-selects ms-auto">
        <select id="localidad-filter" class="form-select form-select-sm">
            <option value="TODOS">Todas las Localidades</option>
            @foreach ($availableLocalidades as $localidad)
                <option value="{{ $localidad->id }}">{{ $localidad->name }}</option>
            @endforeach
        </select>
        <select id="month-filter" class="form-select form-select-sm">
            <option value="TODOS">Últimos 4 Meses</option>
            @foreach ($availableMonths as $month)
                <option value="{{ $month['value'] }}" @if ($month['value'] === $currentMonth) selected @endif>{{ $month['label'] }}</option>
            @endforeach
        </select>
    </div>
</div>

{{-- 🎯 KPIs --}}
<div class="env-kpi-grid">
    <div class="env-kpi">
        <div class="env-kpi-label">Asignados</div>
        <div class="env-kpi-value" id="kpi-total-asignados">0</div>
    </div>
    <div class="env-kpi is-accent">
        <div class="env-kpi-label">Pendientes Ingreso</div>
        <div class="env-kpi-value" id="kpi-pendientes-ingreso">0</div>
    </div>
    <div class="env-kpi">
        <div class="env-kpi-label">En Digitación</div>
        <div class="env-kpi-value kpi-value-warning" id="kpi-pendientes-gesi">0</div>
    </div>
    <div class="env-kpi">
        <div class="env-kpi-label">Ciclo Promedio</div>
        <div class="env-kpi-value" id="kpi-tiempo-ciclo">0</div>
    </div>
</div>

{{-- 📊 GRÁFICOS --}}
<div class="grid-layout-charts">
    <div class="d-flex flex-column gap-4">
        {{-- Gráfico Flujo --}}
        <div class="env-card">
            <div class="env-card-head">
                <h2 class="env-card-title">Flujo de Consecutivos</h2>
            </div>
            <div class="chart-wrap-md">
                <canvas id="chartFlujo"></canvas>
            </div>
        </div>

        {{-- Gráfico Observaciones --}}
        <div class="env-card">
            <div class="env-card-head">
                <h2 class="env-card-title">Distribución por Tipo</h2>
            </div>
            <div class="row align-items-center g-3">
                <div class="col-12 col-md-5">
                    <div class="chart-wrap-sm">
                        <canvas id="chartObservacionesTipo"></canvas>
                    </div>
                </div>
                <div class="col-12 col-md-7" id="observaciones-leyenda"></div>
            </div>
        </div>
    </div>

    <div class="d-flex flex-column gap-4">
        {{-- Tasa de Error --}}
        <div class="env-card">
            <div class="env-card-head">
                <h2 class="env-card-title">Tasa de Error</h2>
            </div>
            <div class="chart-wrap-sm d-flex justify-content-center align-items-center position-relative">
                <canvas id="chartTasaError"></canvas>
                <div class="tasa-error-value" id="tasaError">0%</div>
            </div>
        </div>

        {{-- Tabla Top Usuarios --}}
        <div class="env-card scrollable">
            <div class="env-card-head">
                <h2 class="env-card-title" id="top-chart-title">Ranking Usuarios</h2>
            </div>
            <div id="top-5-table-container">
                <p class="text-muted">Cargando...</p>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    let flowChart, obsChart, errorChart;

    // Configuración Global de Chart.js para que no crezcan
    Chart.defaults.maintainAspectRatio = false;
    Chart.defaults.responsive = true;

    function getShellColor(varName) {
        return getComputedStyle(document.documentElement).getPropertyValue(varName).trim();
    }

    Chart.defaults.color = getShellColor('--shell-muted');

    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.filter-button').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.filter-button').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                fetchMetrics();
            });
        });
        document.getElementById('localidad-filter').addEventListener('change', fetchMetrics);
        document.getElementById('month-filter').addEventListener('change', fetchMetrics);
        fetchMetrics();
    });

    function fetchMetrics() {
        const ent = document.querySelector('.filter-button.active').dataset.value;
        const loc = document.getElementById('localidad-filter').value;
        const mes = document.getElementById('month-filter').value;

        const params = new URLSearchParams({ entorno: ent, localidad: loc, mes: mes === 'TODOS' ? '' : mes });

        fetch(`/api/dashboard/metrics?${params}`)
            .then(res => res.json())
            .then(data => {
                updateKPIs(data);
                drawCharts(data);
                drawTable(data.topUsuarios);
            });
    }

    function updateKPIs(data) {
        document.getElementById('kpi-total-asignados').innerText = data.totalAsignados.toLocaleString();
        document.getElementById('kpi-pendientes-ingreso').innerText = data.pendientesIngresoGesi.toLocaleString();
        document.getElementById('kpi-pendientes-gesi').innerText = data.pendientesGesi.toLocaleString();
        document.getElementById('kpi-tiempo-ciclo').innerText = `${data.tiempoPromedioCiclo.toFixed(1)}d`;
    }

    function drawCharts(data) {
        const gridColor = getShellColor('--shell-border');
        const redColor = getShellColor('--shell-red');
        const surface2 = getShellColor('--shell-surface-2');
        const successColor = getShellColor('--shell-success');

        // Flujo
        if (flowChart) flowChart.destroy();
        flowChart = new Chart(document.getElementById('chartFlujo'), {
            type: 'bar',
            data: {
                labels: data.flujoMensual.meses,
                datasets: [
                    { label: 'Solicitados', data: data.flujoMensual.solicitados, backgroundColor: redColor, borderRadius: 4 },
                    { label: 'Gesi', data: data.flujoMensual.Gesi, backgroundColor: getShellColor('--shell-muted-2'), borderRadius: 4 },
                    { label: 'Retornados', data: data.flujoMensual.retornados, backgroundColor: '#980303', borderRadius: 4 }
                ]
            },
            options: { scales: { y: { grid: { color: gridColor } }, x: { grid: { display: false } } } }
        });

        // Observaciones
        if (obsChart) obsChart.destroy();
        obsChart = new Chart(document.getElementById('chartObservacionesTipo'), {
            type: 'doughnut',
            data: {
                labels: data.distribucionObservaciones.tipos,
                datasets: [{ data: data.distribucionObservaciones.conteo, backgroundColor: [redColor, '#980303', surface2], borderWidth: 0 }]
            },
            options: { cutout: '75%', plugins: { legend: { display: false } } }
        });

        // Leyenda
        let leyHtml = '';
        data.distribucionObservaciones.tipos.forEach((t, i) => {
            leyHtml += `<div class="d-flex justify-content-between mb-1"><small>${t}</small><b>${data.distribucionObservaciones.conteo[i]}</b></div>`;
        });
        document.getElementById('observaciones-leyenda').innerHTML = leyHtml;

        // Tasa Error
        if (errorChart) errorChart.destroy();
        document.getElementById('tasaError').innerText = `${data.tasaError.toFixed(1)}%`;
        errorChart = new Chart(document.getElementById('chartTasaError'), {
            type: 'doughnut',
            data: {
                datasets: [{ data: [data.tasaError, 100 - data.tasaError], backgroundColor: [data.tasaError > 20 ? redColor : successColor, surface2], borderWidth: 0 }]
            },
            options: { rotation: 270, circumference: 180, cutout: '80%', plugins: { tooltip: { enabled: false } } }
        });
    }

    function drawTable(top) {
        const container = document.getElementById('top-5-table-container');
        document.getElementById('top-chart-title').innerText = top.title;
        let html = `<table class="table-custom"><thead><tr><th>Usuario</th><th>Asig.</th><th>Err%</th></tr></thead><tbody>`;
        top.data.forEach(u => {
            html += `<tr><td>${u.nombre}</td><td>${u.asignados}</td><td class="${u.tasa_error > 15 ? 'text-danger' : 'text-success'}">${u.tasa_error}%</td></tr>`;
        });
        html += `</tbody></table>`;
        container.innerHTML = html;
    }
</script>
@endsection
