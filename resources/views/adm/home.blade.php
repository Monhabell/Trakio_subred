@extends('layouts.adm.navigation')

@section('main')

<style>
    /* ── GESI PAGE LOADER ── */
    #gesi-loader {
        position: fixed;
        inset: 0;
        z-index: 9999;
        background: rgba(8, 10, 18, 0.87);
        display: flex;
        align-items: center;
        justify-content: center;
        backdrop-filter: blur(4px);
        -webkit-backdrop-filter: blur(4px);
        transition: opacity 0.3s ease;
    }
    #gesi-loader.gesi-loader--hidden {
        opacity: 0;
        pointer-events: none;
    }
    .gesi-loader-box {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 16px;
    }
    .gesi-spinner {
        width: 42px;
        height: 42px;
        border: 3px solid rgba(255,255,255,0.08);
        border-top-color: #dc3545;
        border-radius: 50%;
        animation: gesi-spin 0.75s linear infinite;
    }
    @keyframes gesi-spin { to { transform: rotate(360deg); } }
    .gesi-loader-text {
        color: rgba(255,255,255,0.5);
        font-size: 0.8rem;
        letter-spacing: 0.05em;
        margin: 0;
    }
    .gesi-btn-spinner {
        display: inline-block;
        width: 12px;
        height: 12px;
        border: 2px solid rgba(255,255,255,0.25);
        border-top-color: #fff;
        border-radius: 50%;
        animation: gesi-spin 0.6s linear infinite;
        vertical-align: middle;
        margin-right: 5px;
    }
    /* Skeleton animado para el panel de productividad */
    .gesi-skeleton { padding: 1.25rem; display: flex; flex-direction: column; gap: 10px; }
    .gesi-skeleton-line {
        height: 13px;
        background: linear-gradient(90deg, rgba(255,255,255,0.04) 25%, rgba(255,255,255,0.09) 50%, rgba(255,255,255,0.04) 75%);
        background-size: 200% 100%;
        border-radius: 4px;
        animation: gesi-shimmer 1.5s infinite;
    }
    @keyframes gesi-shimmer { 0% { background-position: 200% 0; } 100% { background-position: -200% 0; } }
</style>

{{-- ── LOADING OVERLAY (visible por defecto, se oculta al cargar el DOM) ── --}}
<div id="gesi-loader" role="status" aria-live="polite">
    <div class="gesi-loader-box">
        <div class="gesi-spinner"></div>
        <p class="gesi-loader-text" id="gesi-loader-text">Cargando dashboard...</p>
    </div>
</div>

    {{-- ── PAGE HEADER ── --}}
    <div class="adm-page-header">
        <div>
            <span class="adm-page-tag">// Panel de control</span>
            <h1 class="adm-page-title">Dashboard Operativo Gesi</h1>
        </div>
        <div class="adm-page-badge"><span class="pulse"></span> En tiempo real</div>
    </div>

    {{-- ── FILTER BAR ── --}}
    <form action="{{ route('app.home') }}" method="GET" class="adm-filter-bar">
        <div class="adm-filter-group">
            <span class="adm-filter-label">Desde</span>
            <input type="date" name="fecha_inicio" value="{{ $inicioMes }}" class="adm-filter-input">
        </div>
        <div class="adm-filter-group">
            <span class="adm-filter-label">Hasta</span>
            <input type="date" name="fecha_fin" value="{{ $finMes }}" class="adm-filter-input">
        </div>
        <div style="display:flex; gap:8px; align-items:flex-end;">
            <button type="submit" class="adm-btn adm-btn-primary">Filtrar</button>
            <a href="{{ route('app.home') }}" class="adm-btn adm-btn-ghost">Reiniciar</a>
        </div>
    </form>

    {{-- ── RECORDATORIO ENTREGA GESI ── --}}
    @php
        $reminderSent = $emailNotification ?? null;
        $deadlineReminder = \App\Console\Commands\GesiDeliveryReminder::getSecondBusinessDayNextMonth();
    @endphp
    <div class="adm-card" style="margin-bottom: 1.5rem; border-color: rgba(230,57,70,.35);">
        <div class="adm-card-head" style="flex-wrap: wrap; gap: .75rem;">
            <span class="adm-card-title">
                <i class="fas fa-envelope-open-text" style="color:#e63946;"></i>
                Recordatorio de Entrega de Bases GESI
            </span>
            <span style="font-size: .8rem; color: var(--shell-muted);">
                Próxima fecha límite:
                <strong style="color:#fff;">{{ $deadlineReminder->translatedFormat('j \d\e F \d\e Y') }}</strong>
                <span style="color: var(--shell-muted); font-size:.72rem;">
                    &middot; {{ $deadlineReminder->diffForHumans() }}
                </span>
            </span>
        </div>

        <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:1rem; padding: .25rem 0 .5rem;">

            {{-- Estado del envío --}}
            @if ($reminderSent)
                <div style="display:flex; align-items:center; gap:.6rem;">
                    <span style="display:inline-flex; align-items:center; justify-content:center; width:32px; height:32px; border-radius:50%; background:rgba(46,204,113,.15); border:1.5px solid #2ecc71;">
                        <i class="fas fa-check" style="color:#2ecc71; font-size:.8rem;"></i>
                    </span>
                    <div>
                        <div style="font-size:.85rem; font-weight:600; color:#2ecc71;">
                            Notificación enviada este mes
                        </div>
                        <div style="font-size:.75rem; color:var(--shell-muted);">
                            {{ $reminderSent['sent_at'] }} &middot; {{ $reminderSent['count'] }} usuario(s) notificado(s)
                        </div>
                    </div>
                </div>
            @else
                <div style="display:flex; align-items:center; gap:.6rem;">
                    <span style="display:inline-flex; align-items:center; justify-content:center; width:32px; height:32px; border-radius:50%; background:rgba(230,57,70,.12); border:1.5px solid rgba(230,57,70,.4);">
                        <i class="fas fa-bell" style="color:#e63946; font-size:.8rem;"></i>
                    </span>
                    <div>
                        <div style="font-size:.85rem; font-weight:600; color:var(--shell-text);">
                            Aún no se ha enviado el recordatorio de este mes
                        </div>
                        <div style="font-size:.75rem; color:var(--shell-muted);">
                            Se notificará a todos los usuarios (excepto digitadores) con la fecha límite de entrega
                            y sus fichas en retraso.
                        </div>
                    </div>
                </div>
            @endif

            {{-- Botón de envío --}}
            <form action="{{ route('gesi.delivery-reminder.send') }}" method="POST"
                  onsubmit="
                      if (!confirm('¿Enviar el recordatorio de entrega GESI a todos los usuarios?')) return false;
                      var b = this.querySelector('button[type=submit]');
                      b.disabled = true;
                      b.innerHTML = '<span class=\'gesi-btn-spinner\'></span> Enviando...';
                  ">
                @csrf
                <button type="submit" class="adm-btn adm-btn-primary" style="display:inline-flex; align-items:center; gap:.5rem;">
                    <i class="fas fa-paper-plane"></i>
                    {{ $reminderSent ? 'Reenviar recordatorio' : 'Enviar recordatorio ahora' }}
                </button>
            </form>
        </div>

        @if (session('gesi_reminder_success'))
            <div style="margin-top:.75rem; padding:.65rem 1rem; background:rgba(46,204,113,.1); border:1px solid rgba(46,204,113,.3); border-radius:6px; font-size:.82rem; color:#2ecc71;">
                <i class="fas fa-check-circle me-1"></i> {{ session('gesi_reminder_success') }}
            </div>
        @endif
    </div>

    {{-- ── IMPORTAR ERRORES DE DIGITACIÓN (EXCEL) ── --}}
    <div class="adm-card" style="margin-bottom: 1.5rem;">
        <div class="adm-card-head">
            <span class="adm-card-title">
                <i class="fas fa-file-excel" style="color:#2ecc71;"></i>
                Errores de Digitación
            </span>
            <span class="adm-badge">Carga desde Excel</span>
        </div>
        <p class="adm-card-note">
            Sube el Excel donde marcaste en rojo las celdas con error. El sistema solo carga las fichas que tengan
            al menos una columna marcada y luego las muestra a cada digitador en su vista de "mis errores".
        </p>
        <div class="adm-card-foot" style="display:flex; gap:.75rem;">
            <a href="{{ route('adm.error-imports.index') }}" class="adm-btn adm-btn-primary" style="display:inline-flex; align-items:center; gap:.5rem;">
                <i class="fas fa-list"></i> Ver cargas y pendientes
            </a>
            <a href="{{ route('adm.error-imports.test') }}" class="adm-btn adm-btn-ghost" style="display:inline-flex; align-items:center; gap:.5rem;">
                <i class="fas fa-upload"></i> Subir nuevo Excel
            </a>
        </div>
    </div>

    {{-- ── KPIs ── --}}
    <div class="adm-kpi-grid">
        <div class="adm-kpi">
            <div class="adm-kpi-label">Solicitudes Totales</div>
            <div class="adm-kpi-value">{{ number_format($consecutivosTotal) }}</div>
        </div>
        <div class="adm-kpi is-accent">
            <div class="adm-kpi-label">En Trámite</div>
            <div class="adm-kpi-value">{{ number_format($enTramite) }}</div>
        </div>
        <div class="adm-kpi">
            <div class="adm-kpi-label">Devueltos / Finalizados</div>
            <div class="adm-kpi-value">{{ number_format($devueltosFinalizados) }}</div>
        </div>
    </div>

    {{-- ── ROW 1: Pipeline + Alertas Críticas ── --}}
    <div class="row g-4 adm-section">

        <div class="col-lg-6">
            <div class="adm-card h-100">
                <div class="adm-card-head">
                    <span class="adm-card-title">
                        <i class="fa-solid fa-chart-column"></i> Operativo de Consecutivos
                    </span>
                    <span class="adm-badge">Historial Logístico</span>
                </div>
                <div style="position: relative; flex: 1; min-height: 320px;">
                    <canvas id="pipelineChart"></canvas>
                </div>
                <p class="adm-card-note">
                    Del total de fichas pendientes por digitar (estado 11):
                    <span style="display:inline-block;width:10px;height:10px;border-radius:2px;background:#f5a623;"></span>
                    "Actualización" = reingresos (mismo file_number + base, otro mes o duplicado) &middot;
                    <span style="display:inline-block;width:10px;height:10px;border-radius:2px;background:#2ecc71;"></span>
                    "Nuevas" = fichas únicas de este mes
                </p>
                <div class="adm-card-foot">
                    <i class="fas fa-sync-alt fa-xs me-1"></i> Actualizado en tiempo real
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="adm-card h-100">
                <div class="adm-card-head">
                    <span class="adm-card-title">
                        <i class="fas fa-exclamation-triangle"></i> Alertas Críticas por Entorno
                    </span>
                    <span class="adm-badge adm-badge-red">{{ $dataCriticaEntorno->sum() }} críticos</span>
                </div>
                <p class="adm-card-note">Registros con &gt; 5 días de retraso &middot; Mes Actual</p>
                <div style="position: relative; flex: 1; min-height: 320px;">
                    <canvas id="chartCriticoEntorno"></canvas>
                </div>
                <div class="adm-card-foot">
                    <i class="fas fa-info-circle me-1"></i> Criterio: No entregado a Gesi
                </div>
            </div>
        </div>

    </div>

    {{-- ── ROW 1.5: Pendientes por Digitar ── --}}
    <div class="row g-4 adm-section">
        <div class="col-12">
            <div class="adm-card">
                <div class="adm-card-head">
                    <span class="adm-card-title">
                        <i class="fa-solid fa-file-circle-exclamation"></i> Fichas Pendientes por Digitar
                    </span>
                    @if ($totalVencidasDigitar > 0)
                        <span class="adm-badge adm-badge-red">{{ $totalVencidasDigitar }} vencida{{ $totalVencidasDigitar > 1 ? 's' : '' }}</span>
                    @else
                        <span class="adm-badge">Al día</span>
                    @endif
                </div>
                <p class="adm-card-note">
                    Recibidas por GESI en el periodo filtrado (estado 11) y aún sin digitar. Plazo de digitación:
                    2 días desde el recibido. En rojo, las que ya superaron el plazo.
                </p>

                @if ($pendientesPorEntorno->isEmpty())
                    <p style="color: var(--shell-muted); text-align:center; padding: 1.5rem 0; margin:0;">
                        No hay fichas pendientes por digitar en el periodo filtrado.
                    </p>
                @else
                    <div class="adm-kpi-grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); margin-bottom: 0;">
                        <div class="adm-kpi is-accent">
                            <div class="adm-kpi-label">Total Pendientes</div>
                            <div class="adm-kpi-value">{{ $totalPendientesDigitar }}</div>
                            <div style="margin-top: .75rem; font-size: 0.72rem; color: var(--shell-muted);">
                                <span style="color:#f5a623;">●</span> Actualización: <strong>{{ $totalActualizacionDigitar }}</strong>
                                &nbsp;&middot;&nbsp;
                                <span style="color:#2ecc71;">●</span> Nuevas: <strong>{{ $totalNuevasDigitar }}</strong>
                            </div>
                        </div>
                        @foreach ($pendientesPorEntorno as $item)
                            <div class="adm-kpi"
                                @if ($item->vencidas > 0)
                                    style="border-color: rgba(230, 57, 70, 0.4); background: linear-gradient(180deg, var(--shell-red-dim), transparent);"
                                @endif
                            >
                                <div class="adm-kpi-label">{{ $item->environment_name ?? 'Sin entorno' }}</div>
                                <div class="adm-kpi-value" style="font-size: 1.8rem;">{{ $item->total }}</div>
                                @if ($item->vencidas > 0)
                                    <span class="adm-badge adm-badge-red" style="margin-top: .5rem; display:inline-block;">
                                        {{ $item->vencidas }} vencida{{ $item->vencidas > 1 ? 's' : '' }}
                                    </span>
                                @endif
                                <div style="margin-top: .6rem; font-size: 0.7rem; color: var(--shell-muted);">
                                    <span style="color:#f5a623;">●</span> Actualización: <strong>{{ $item->actualizacion }}</strong>
                                    &nbsp;&middot;&nbsp;
                                    <span style="color:#2ecc71;">●</span> Nuevas: <strong>{{ $item->nuevas }}</strong>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ── ROW 2: Productivity + Alerts ── --}}
    <div class="row g-4 adm-section">
        <div class="col-lg-7">
            <div class="adm-card">
                <div class="adm-card-head">
                    <span class="adm-card-title">
                        <i class="fa-solid fa-user-gear"></i> Monitor de Productividad
                    </span>
                    <div style="display:flex; gap:8px; align-items:center;">
                        <div class="adm-period-group">
                            <button class="adm-period-btn dashboardFilter" value="day">Día</button>
                            <button class="adm-period-btn dashboardFilter" value="week">Semana</button>
                            <button class="adm-period-btn dashboardFilter" value="month">Mes</button>
                        </div>
                        <div class="dropdown">
                            <button class="adm-period-btn dropdown-toggle" data-bs-toggle="dropdown" type="button">
                                Personalizado
                            </button>
                            <div class="dropdown-menu dropdown-menu-dark p-0"
                                style="background:transparent; border:none;">
                                <div class="adm-card" style="min-width:200px;">
                                    <div style="margin-bottom:10px;">
                                        <div class="adm-filter-label" style="margin-bottom:4px;">Desde</div>
                                        <input type="date" id="init_date" class="adm-filter-input" style="width:100%;">
                                    </div>
                                    <div style="margin-bottom:12px;">
                                        <div class="adm-filter-label" style="margin-bottom:4px;">Hasta</div>
                                        <input type="date" id="end_date" class="adm-filter-input" style="width:100%;">
                                    </div>
                                    <button type="button" class="adm-btn adm-btn-primary w-100 dashboardFilter"
                                        value="custom">Aplicar</button>
                                </div>
                            </div>
                        </div>
                        <button id="btnExportExcel" class="adm-excel-btn" type="button">
                            <i class="fas fa-file-excel"></i>
                        </button>
                    </div>
                </div>

                <div class="row g-0" style="flex:1;">
                    <div class="col-12" style="padding-left:16px;">
                        <div id="productivity-container-dash" class="adm-scroll" style="height:100%;">
                            <div class="productivity-placeholder">
                                Seleccione un entorno para visualizar datos
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="adm-card">
                <div class="adm-card-head">
                    <span class="adm-card-title">
                        <i class="fa-solid fa-triangle-exclamation"></i> Alertas de Retraso
                    </span>
                </div>
                <div class="adm-scroll">
                    @php $entornosMap = [2 => 'Laboral', 3 => 'Educativo', 4 => 'Comunitario', 6 => 'Institucional']; @endphp
                    <table class="adm-table">
                        <thead>
                            <tr>
                                <th>Entorno</th>
                                <th style="text-align:center;">Total</th>
                                <th style="text-align:center;">Digitados</th>
                                <th style="text-align:center; color:var(--shell-red);">Pendientes</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($alertasLocalidad as $alerta)
                                <tr>
                                    <td style="font-weight:500;">{{ $entornosMap[$alerta->environment] ?? 'Otro' }}
                                    </td>
                                    <td style="text-align:center; color:var(--shell-muted);">{{ $alerta->total }}</td>
                                    <td style="text-align:center;">{{ $alerta->digitados }}</td>
                                    <td style="text-align:center;">
                                        <span class="adm-badge adm-badge-red">{{ $alerta->pendientes }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4"
                                        style="text-align:center; padding:2.5rem; color:var(--shell-muted-2);">
                                        Sin alertas activas
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- ── ROW 3: Mini charts ── --}}
    <div class="row g-4 adm-section">
        <div class="col-md-3" hidden>
            <div class="adm-card">
                <div class="adm-card-title">Histórico Mensual</div>
                <div class="chart-wrap-sm"><canvas id="historyChart"></canvas></div>
            </div>
        </div>
        <div class="col-md-3" hidden>
            <div class="adm-card">
                <div class="adm-card-title">Cumplimiento Meta</div>
                <div class="chart-wrap-sm"><canvas id="metaDigitador"></canvas></div>
            </div>
        </div>

        <div class="col-md-6">

            <div class="adm-card">
                <div class="adm-card-head">
                    <span class="adm-card-title">
                        <i class="fa-solid fa-bullseye"></i> Demanda Entorno
                    </span>
                </div>
                <div class="chart-wrap-md">
                    <canvas id="entornoChartPolar"></canvas>
                </div>
            </div>

        </div>


        <div class="col-md-3">
            <div class="adm-card">
                <div class="adm-card-title" style="margin-bottom:1rem;">Distribución Carga</div>
                <div class="chart-wrap-sm"><canvas id="entornoChartDoughnut"></canvas></div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="adm-card">
                <div class="adm-card-title" style="margin-bottom:1rem;">Calidad por Entorno</div>
                <div id="calidad-body" class="adm-scroll" style="max-height:180px; margin-top:4px;">
                    <div class="gesi-skeleton">
                        <div class="gesi-skeleton-line" style="height:28px;width:100%;"></div>
                        <div class="gesi-skeleton-line" style="height:28px;width:100%;"></div>
                        <div class="gesi-skeleton-line" style="height:28px;width:80%;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── AUDIT TABLE ── --}}
    <div class="adm-card adm-section">
        <div class="adm-card-head" style="margin-bottom:0; padding-bottom:1rem; border-bottom:1px solid var(--shell-border);">
            <span class="adm-card-title">
                <i class="fas fa-stopwatch"></i> Auditoría de Tiempos Individuales por Formato
            </span>
        </div>
        <div style="overflow-x:auto; margin-top:1rem;">
            <table class="adm-table" id="tiemposTable">
                <thead>
                    <tr>
                        <th>Formato</th>
                        <th>Muestra</th>
                        <th>Prom. Usuarios</th>
                        <th>T. Real (min)</th>
                        <th>T. Ideal (min)</th>
                        <th>Prom. Recepciones</th>
                        <th id="sortDesviacion" style="cursor:pointer;">Desviación ⬍</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody id="formatos-tbody">
                    <tr>
                        <td colspan="8" style="padding:2rem;text-align:center;">
                            <div class="gesi-spinner" style="margin:0 auto;"></div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        Chart.defaults.color = '#6b7280';
        Chart.defaults.borderColor = 'rgba(255,255,255,0.05)';
        Chart.defaults.maintainAspectRatio = false;
        Chart.defaults.font.family = "'DM Sans', sans-serif";

        // Definimos los colores para el tema oscuro
const RED = '#dc3545';
const RED_A = 'rgba(220, 53, 69, 0.2)';
const TEXT_COLOR = '#a0a0a0';
const GRID_COLOR = 'rgba(255, 255, 255, 0.05)';

const AMBER = '#f5a623';
const AMBER_A = 'rgba(245, 166, 35, 0.2)';
const GREEN = '#2ecc71';
const GREEN_A = 'rgba(46, 204, 113, 0.2)';

// Datos ya organizados en el orden del flujo real desde el backend:
// Solicitados → Rechazados → Sellados → En GESI → Pendientes: Actualización → Pendientes: Nuevas → Digitados → Devueltos
const pipelineLabels = @json($pipelineLabels);
const pipelineData = @json($pipelineData);
// 'Pendientes: Actualización' en ámbar, 'Pendientes: Nuevas' en verde (mismos colores que la tarjeta
// de arriba) y 'Digitados' en rojo sólido para distinguir el tramo ya finalizado del pipeline.
const pipelineColors = [RED_A, RED_A, RED_A, RED_A, AMBER, GREEN, RED, RED_A];
const pipelineBorders = [RED, RED, RED, RED, AMBER, GREEN, RED, RED];

new Chart(document.getElementById('pipelineChart'), {
    type: 'bar',
    data: {
        labels: pipelineLabels,
        datasets: [{
            data: pipelineData,
            backgroundColor: pipelineColors,
            borderColor: pipelineBorders,
            borderWidth: 1.5,
            borderRadius: 6,
            barThickness: 24,
            hoverBackgroundColor: pipelineBorders
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            },
            tooltip: {
                backgroundColor: '#212529',
                titleColor: '#fff',
                bodyColor: '#fff',
                borderColor: '#2d3238',
                borderWidth: 1
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: {
                    color: GRID_COLOR,
                    drawBorder: false
                },
                ticks: {
                    color: TEXT_COLOR,
                    font: { size: 11 }
                }
            },
            x: {
                grid: {
                    display: false // Limpiamos el eje X
                },
                ticks: {
                    color: '#ffffff', // Etiquetas en blanco para resaltar
                    autoSkip: false,
                    maxRotation: 0,
                    minRotation: 0,
                    font: {
                        size: 9,
                        weight: '500'
                    },
                    // Las etiquetas largas (con paréntesis o ":") se muestran en dos líneas para que no se encimen
                    callback: function (value) {
                        const label = pipelineLabels[value] ?? '';
                        if (label.includes('(')) {
                            return label.replace(' (', '\n(').replace(')', '').split('\n');
                        }
                        if (label.includes(': ')) {
                            return label.split(': ');
                        }
                        return label;
                    }
                }
            }
        }
    }
});

        // Polar
        new Chart(document.getElementById('entornoChartPolar'), {
            type: 'polarArea',
            data: {
                labels: ['Laboral', 'Educativo', 'Comunitario', 'Institucional'],
                datasets: [{
                    data: {!! json_encode(array_values($demandaEntorno->toArray())) !!},
                    backgroundColor: ['rgba(230,57,70,0.7)', 'rgba(56,139,253,0.7)', 'rgba(35,134,54,0.7)',
                        'rgba(210,153,34,0.7)'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                scales: {
                    r: {
                        grid: {
                            color: 'rgba(255,255,255,0.06)'
                        },
                        ticks: {
                            display: false
                        }
                    }
                },
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            boxWidth: 8,
                            font: {
                                size: 11
                            },
                            padding: 16
                        }
                    }
                }
            }
        });

        // History
        new Chart(document.getElementById('historyChart'), {
            type: 'line',
            data: {
                labels: @json($historico['labels']),
                datasets: [{
                    data: @json($historico['data']),
                    borderColor: RED,
                    backgroundColor: RED_A,
                    fill: true,
                    tension: 0.45,
                    pointRadius: 0,
                    borderWidth: 1.5
                }]
            },
            options: {
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    x: {
                        display: false
                    },
                    y: {
                        display: false
                    }
                }
            }
        });

        // Meta – se actualiza vía AJAX en adminHomeHeavy
        var metaDigitadorChart = new Chart(document.getElementById('metaDigitador'), {
            type: 'bar',
            data: {
                labels: [],
                datasets: [{
                    data: [],
                    backgroundColor: 'rgba(46,204,113,0.55)',
                    borderColor: '#2ecc71',
                    borderWidth: 1,
                    borderRadius: 4
                }]
            },
            options: {
                plugins: { legend: { display: false } },
                scales: { y: { display: false }, x: { display: false } }
            }
        });

        // Doughnut – se actualiza vía AJAX
        var doughnutChart = new Chart(document.getElementById('entornoChartDoughnut'), {
            type: 'doughnut',
            data: {
                labels: [],
                datasets: [{
                    data: [],
                    backgroundColor: ['rgba(230,57,70,0.8)', 'rgba(56,139,253,0.8)', 'rgba(35,134,54,0.8)',
                        'rgba(100,100,110,0.6)'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                cutout: '72%',
                plugins: { legend: { display: false } }
            }
        });

        // Excel export
        document.getElementById('btnExportExcel').addEventListener('click', function() {
            let start = document.querySelector('input[name="fecha_inicio"]').value;
            let end = document.querySelector('input[name="fecha_fin"]').value;
            window.location.href = `{{ route('digitadores.export') }}?init_date=${start}&end_date=${end}`;
        });
    </script>


<script>
    const ctxCritico = document.getElementById('chartCriticoEntorno').getContext('2d');
    new Chart(ctxCritico, {
        type: 'bar',
        data: {
            labels: @json($labelsEntornosCriticos),
            datasets: [{
                label: 'Registros Críticos',
                data: @json($dataCriticaEntorno),
                backgroundColor: 'rgba(220, 53, 69, 0.8)', // Rojo más vibrante para modo oscuro
                borderColor: '#dc3545',
                borderWidth: 1,
                borderRadius: 5 // Bordes redondeados en las barras
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                title: { display: false } // Ya tenemos el título en el HTML
            },
            scales: {
                x: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(255, 255, 255, 0.1)' // Líneas de cuadrícula sutiles
                    },
                    ticks: {
                        color: '#a0a0a0' // Color de números en el eje X
                    }
                },
                y: {
                    grid: {
                        display: false // Quitamos líneas horizontales para limpiar el diseño
                    },
                    ticks: {
                        color: '#ffffff', // Nombres de entornos en blanco para que resalten
                        font: {
                            weight: 'bold'
                        }
                    }
                }
            }
        }
    });
</script>

<script>
// ── CARGA AJAX DE SECCIONES PESADAS ──────────────────────────────────────────
(function () {
    var params = new URLSearchParams({
        fecha_inicio: '{{ $inicioMes }}',
        fecha_fin:    '{{ $finMes }}'
    });

    fetch('{{ route("app.home.heavy") }}?' + params, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(function (r) { return r.json(); })
    .then(function (data) {
        // Calidad por entorno
        var calidadEl = document.getElementById('calidad-body');
        if (calidadEl) calidadEl.innerHTML = data.calidad_html;

        // Tabla de formatos
        var formatosEl = document.getElementById('formatos-tbody');
        if (formatosEl) formatosEl.innerHTML = data.formatos_html;

        // Chart: metaDigitador
        if (metaDigitadorChart && data.labelsDigitadores) {
            metaDigitadorChart.data.labels              = data.labelsDigitadores;
            metaDigitadorChart.data.datasets[0].data    = data.dataPorcentajes;
            metaDigitadorChart.update();
        }

        // Chart: doughnut distribución carga
        if (doughnutChart && data.dataEntornos_keys) {
            doughnutChart.data.labels              = data.dataEntornos_keys;
            doughnutChart.data.datasets[0].data    = data.dataEntornos_vals;
            doughnutChart.update();
        }
    })
    .catch(function () {
        // Si falla el AJAX, mostrar mensaje de error discreto en cada sección
        var calidadEl = document.getElementById('calidad-body');
        if (calidadEl) calidadEl.innerHTML = '<p style="color:var(--shell-muted);text-align:center;font-size:0.78rem;padding:.5rem;">Sin datos</p>';
        var formatosEl = document.getElementById('formatos-tbody');
        if (formatosEl) formatosEl.innerHTML = '<tr><td colspan="8" style="text-align:center;padding:2rem;color:var(--shell-muted);">Sin datos</td></tr>';
    });
})();

// ── OVERLAY / NAVEGACIÓN ─────────────────────────────────────────────────────
(function () {
    var loader     = document.getElementById('gesi-loader');
    var loaderText = document.getElementById('gesi-loader-text');

    function showLoader(msg) {
        loaderText.textContent = msg || 'Cargando...';
        loader.classList.remove('gesi-loader--hidden');
    }

    function hideLoader() {
        loader.classList.add('gesi-loader--hidden');
        // Quitar del DOM después de la transición para no bloquear clicks
        setTimeout(function () { loader.style.display = 'none'; }, 350);
    }

    // Ocultar cuando el DOM y los recursos estén listos
    if (document.readyState === 'complete') {
        hideLoader();
    } else {
        window.addEventListener('load', hideLoader);
        // Fallback: si tarda más de 8 s forzar ocultamiento
        setTimeout(hideLoader, 8000);
    }

    // Mostrar al enviar el formulario de filtros de fecha
    var filterForm = document.querySelector('.adm-filter-bar');
    if (filterForm) {
        filterForm.addEventListener('submit', function () {
            var btn = this.querySelector('button[type="submit"]');
            showLoader('Aplicando filtro...');
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<span class="gesi-btn-spinner"></span> Filtrando...';
            }
        });
    }

    // Mostrar al hacer clic en "Reiniciar" (navegación)
    var resetLink = document.querySelector('a.adm-btn-ghost');
    if (resetLink) {
        resetLink.addEventListener('click', function () {
            showLoader('Reiniciando...');
        });
    }

    // Skeleton animado en el contenedor de productividad mientras carga el AJAX
    var skeletonHTML = [
        '<div class="gesi-skeleton">',
        '  <div class="gesi-skeleton-line" style="width:55%"></div>',
        '  <div class="gesi-skeleton-line" style="width:100%"></div>',
        '  <div class="gesi-skeleton-line" style="width:80%"></div>',
        '  <div class="gesi-skeleton-line" style="width:100%"></div>',
        '  <div class="gesi-skeleton-line" style="width:65%"></div>',
        '  <div class="gesi-skeleton-line" style="width:100%"></div>',
        '  <div class="gesi-skeleton-line" style="width:90%"></div>',
        '</div>'
    ].join('');

    document.querySelectorAll('.dashboardFilter').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var container = document.getElementById('productivity-container-dash');
            if (container) {
                container.innerHTML = skeletonHTML;
            }
        });
    });
})();
</script>


@endsection
