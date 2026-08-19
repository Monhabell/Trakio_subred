@extends('layouts.members.nav')
@section('main')

@vite('resources/css/members/home.css')

<div class="mem-home">
    {{-- HEADER --}}
    <div class="mem-home-header">
        <div><h5 class="mem-home-title">Panel <span>Operativo</span></h5></div>
        <form action="{{ route('dashboard.productivity.data') }}" method="GET" class="mem-home-filters">
            <select name="month" class="form-select mem-home-select">
                @foreach(['01'=>'Enero','02'=>'Febrero','03'=>'Marzo','04'=>'Abril','05'=>'Mayo','06'=>'Junio','07'=>'Julio','08'=>'Agosto','09'=>'Septiembre','10'=>'Octubre','11'=>'Noviembre','12'=>'Diciembre'] as $num => $nombre)
                    <option value="{{ $num }}" {{ $dashboard['filtros']['mes'] == $num ? 'selected' : '' }}>{{ $nombre }}</option>
                @endforeach
            </select>
            <select name="year" class="form-select mem-home-select">
                @for($i = date('Y'); $i >= 2023; $i--)
                    <option value="{{ $i }}" {{ $dashboard['filtros']['anio'] == $i ? 'selected' : '' }}>{{ $i }}</option>
                @endfor
            </select>
            <button type="submit" class="mem-home-btn"><i class="fas fa-sync-alt me-1"></i> ACTUALIZAR</button>
        </form>
    </div>

    {{-- LO MÁS IMPORTANTE --}}
    <div class="mem-shortcuts-grid">
        <a href="{{ route('toolscontrol.index') }}" class="mem-shortcut mem-shortcut-cyan">
            <div class="mem-shortcut-icon"><i class="fa-solid fa-folder-open"></i></div>
            <div class="mem-shortcut-body">
                <span class="mem-shortcut-title">Consecutivos del mes</span>
                <span class="mem-shortcut-value">{{ $dashboard['total_consecutivos'] }}</span>
                <span class="mem-shortcut-split">
                    <span class="mem-shortcut-tag mem-shortcut-tag-new">{{ $dashboard['nuevas_consecutivos'] }} nuevas</span>
                    <span class="mem-shortcut-tag mem-shortcut-tag-update">{{ $dashboard['actualizaciones_consecutivos'] }} actualizaciones</span>
                </span>
                <span class="mem-shortcut-note">
                    @if ($dashboard['ultimo_consecutivo'])
                        Último: ficha {{ $dashboard['ultimo_consecutivo']->file_number }}
                    @else
                        Aún no has solicitado consecutivos este mes
                    @endif
                </span>
            </div>
            <i class="fa-solid fa-chevron-right mem-shortcut-arrow"></i>
        </a>

        <a href="{{ route('env.traceability') }}" class="mem-shortcut mem-shortcut-red">
            <div class="mem-shortcut-icon"><i class="fa-solid fa-magnifying-glass"></i></div>
            <div class="mem-shortcut-body">
                <span class="mem-shortcut-title">Observaciones de trazabilidad</span>
                <span class="mem-shortcut-value">{{ $dashboard['total_observaciones'] }}</span>
                <span class="mem-shortcut-note">
                    @if ($dashboard['base_mas_observaciones'])
                        Más frecuentes en {{ $dashboard['base_mas_observaciones']->base }}
                    @else
                        Sin observaciones registradas este mes
                    @endif
                </span>
            </div>
            <i class="fa-solid fa-chevron-right mem-shortcut-arrow"></i>
        </a>
    </div>

    {{-- KPIs --}}
    <div class="mem-kpi-grid">
        <div class="mem-kpi">
            <span class="mem-kpi-title">Fichas Intervenidas</span>
            <div class="d-flex align-items-baseline gap-2 flex-wrap">
                <span class="mem-kpi-value">{{ $dashboard['total_recepciones'] }}</span>
                <span class="badge mem-kpi-badge">{{ $dashboard['total_fichas'] }} únicas</span>
            </div>
        </div>

        <div class="mem-kpi">
            <span class="mem-kpi-title">Digitación (Total)</span>
            <div class="mem-kpi-value text-success">{{ $dashboard['digitadas'] }}</div>
            @php $porcDigitado = ($dashboard['total_recepciones'] + $dashboard['total_actualizaciones'] > 0) ? ($dashboard['digitadas']/($dashboard['total_recepciones'] + $dashboard['total_actualizaciones']))*100 : 0; @endphp
            <div class="mem-progress"><div class="bar" style="width: {{ $porcDigitado }}%; background: #28a745;"></div></div>
        </div>

        <div class="mem-kpi">
            <span class="mem-kpi-title">Pendientes de Sello</span>
            <div class="mem-kpi-value text-warning">{{ $dashboard['pendientes_sellar'] }}</div>
        </div>

        <div class="mem-kpi">
            <span class="mem-kpi-title">Calidad (Obs)</span>
            <div class="mem-kpi-value text-danger">{{ $dashboard['total_observaciones'] }}</div>
        </div>
    </div>

    {{-- BLOQUE DE ACTUALIZACIONES (TRANSVERSAL) --}}
    <div class="row g-3 mb-3">
        <div class="col-12">
            <div class="mem-panel mem-panel-updates">
                <div class="mem-panel-title">CONTROL DE ACTUALIZACIONES GESI (Mes Actual)</div>
                <div class="row align-items-center">
                    <div class="col-md-3 mem-updates-count">
                        <span class="mem-kpi-title d-block">Actualizaciones detectadas</span>
                        <span class="h2 fw-bold text-info">{{ $dashboard['total_actualizaciones'] }}</span>
                    </div>
                    <div class="col-md-9 px-md-4">
                        <small class="text-muted">Progreso de digitación en actualizaciones de fichas (incluso de meses previos):</small>
                        <div class="d-flex justify-content-between mt-2">
                            <span class="small fw-bold">{{ $dashboard['actualizaciones_digitadas'] }} / {{ $dashboard['total_actualizaciones'] }} Completadas</span>
                            @php $porcGesi = $dashboard['total_actualizaciones'] > 0 ? ($dashboard['actualizaciones_digitadas'] / $dashboard['total_actualizaciones']) * 100 : 0; @endphp
                            <span class="small text-info">{{ round($porcGesi, 1) }}%</span>
                        </div>
                        <div class="mem-progress mem-updates-progress mt-1"><div class="bar" style="width: {{ $porcGesi }}%"></div></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- GRÁFICA Y CALIDAD --}}
    <div class="row g-3 mb-3">
        <div class="col-lg-7">
            <div class="mem-panel">
                <div class="mem-panel-title">TENDENCIA OPERATIVA (Originales)</div>
                <div class="mem-chart-wrap">
                    <canvas id="pipelineChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="mem-panel">
                <div class="mem-panel-title">ALERTA DE INCIDENCIAS</div>
                <div class="text-center py-2">
                    <h2 class="mem-incidence-value">{{ $dashboard['total_observaciones'] }}</h2>
                    <p class="text-muted small text-uppercase">Observaciones en {{ $dashboard['recepciones_con_obs'] }} recepciones</p>
                </div>
                @if($dashboard['base_mas_observaciones'])
                <div class="mt-2 mem-incidence-box">
                    <small class="text-muted d-block">BASE CON MÁS ERRORES:</small>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-bold small">{{ $dashboard['base_mas_observaciones']->base }}</span>
                        <span class="badge bg-danger">{{ $dashboard['base_mas_observaciones']->total }}</span>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- TABLA DETALLADA --}}
    <div class="row g-3">
        <div class="col-12">
            <div class="mem-panel">
                @php $totalParaTabla = $dashboard['total_recepciones'] + $dashboard['total_actualizaciones']; @endphp
                <div class="mem-panel-title">ESTADO DE GESTIÓN TOTAL ({{ $totalParaTabla }} registros)</div>
                <div class="table-responsive">
                    <table class="table mem-table">
                        <thead><tr><th>Etapa</th><th class="text-center">Cant.</th><th>% Participación</th></tr></thead>
                        <tbody>
                            @foreach($dashboard['por_estado'] as $nombre => $cantidad)
                            <tr>
                                <td class="fw-semibold">{{ $nombre }}</td>
                                <td class="text-center"><span class="badge">{{ $cantidad }}</span></td>
                                <td style="min-width: 160px">
                                    @php $porc = ($totalParaTabla > 0) ? ($cantidad / $totalParaTabla) * 100 : 0; @endphp
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="mem-progress flex-grow-1 mt-0"><div class="bar" style="width: {{ $porc }}%"></div></div>
                                        <span class="small fw-bold">{{ round($porc, 1) }}%</span>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    if (window.myChart) { window.myChart.destroy(); }
    const ctx = document.getElementById('pipelineChart').getContext('2d');
    window.myChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: @json($dashboard['grafica']['labels']),
            datasets: [
                { label: 'Recepciones', data: @json($dashboard['grafica']['recepciones']), borderColor: '#ff2e2e', backgroundColor: 'rgba(255, 46, 46, 0.1)', fill: true, tension: 0.4, borderWidth: 3, pointRadius: 4 }
            ]
        },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, grid: { color: 'rgba(127,127,127,0.15)' }, ticks: { color: '#888', font: { size: 10 } } }, x: { grid: { display: false }, ticks: { color: '#888', font: { size: 10 } } } } }
    });
</script>
@endsection
