@extends('layouts.dig.navigation')

@section('main')
    <style>
        #home-container {
            color: var(--shell-text);
            min-height: 100vh;
            font-family: var(--shell-sans);
        }

        /* CARDS (reusa la superficie del shell, look "modern-card" del diseño anterior) */
        .modern-card {
            background: var(--shell-surface);
            border: 1px solid var(--shell-border);
            border-radius: 1.25rem;
            position: relative;
            overflow: hidden;
            transition: transform 0.3s ease, border-color 0.3s ease;
            color: var(--shell-text);
        }

        [data-theme="dark"] .modern-card {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.45), 0 1px 2px rgba(0, 0, 0, 0.5);
        }

        [data-theme="light"] .modern-card {
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.12), 0 8px 24px rgba(15, 23, 42, 0.1);
        }

        .modern-card:hover {
            border-color: var(--shell-border-strong);
            transform: translateY(-2px);
        }

        /* PROGRESS BAR */
        .progress-custom {
            height: 8px;
            background: var(--shell-surface-2);
            border: 1px solid var(--shell-border);
            border-radius: 10px;
            overflow: hidden;
        }

        .progress-bar-fill {
            background: linear-gradient(135deg, var(--shell-red) 0%, var(--shell-red-glow) 100%);
            height: 100%;
            border-radius: 10px;
            transition: width 1s ease-in-out;
        }

        /* AVATAR GLOW */
        .avatar-wrapper {
            position: relative;
            padding: 10px;
        }

        .avatar-glow {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 120%;
            height: 120%;
            background: var(--shell-red);
            filter: blur(35px);
            opacity: 0.15;
            z-index: 0;
        }

        .avatar-frame {
            border: 3px solid var(--shell-surface-2);
            object-fit: cover;
            position: relative;
            z-index: 1;
        }

        .avatar-placeholder {
            width: 130px;
            height: 130px;
            border: 1px solid var(--shell-border-strong);
            background: var(--shell-surface-2);
        }

        /* BUTTONS */
        .btn-action {
            background: var(--shell-red);
            border: none;
            color: #fff;
            padding: 12px 28px;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-action:hover {
            box-shadow: 0 0 20px var(--shell-red-glow);
            transform: scale(1.02);
            color: #fff;
        }

        .btn-outline-shell {
            background: transparent;
            border: 1px solid var(--shell-border-strong);
            color: var(--shell-text);
            border-radius: 12px;
            padding: 12px 28px;
            transition: all 0.2s;
        }

        .btn-outline-shell:hover {
            border-color: var(--shell-red);
            color: var(--shell-red);
        }

        .scrollable-content::-webkit-scrollbar {
            width: 5px;
        }

        .scrollable-content::-webkit-scrollbar-track {
            background: transparent;
        }

        .scrollable-content::-webkit-scrollbar-thumb {
            background: var(--shell-border-strong);
            border-radius: 10px;
        }

        /* Animación: ganancia de horas detectada al recargar la página */
        .hours-gain-anchor {
            position: relative;
            overflow: visible;
        }

        .hours-gain-popup {
            position: absolute;
            top: -6px;
            left: 50%;
            transform: translate(-50%, 0);
            color: #2ecc71;
            font-weight: 800;
            font-size: 1.5rem;
            white-space: nowrap;
            text-shadow: 0 0 14px rgba(46, 204, 113, 0.75);
            pointer-events: none;
            z-index: 20;
            animation: hours-gain-float 2.4s ease-out forwards;
        }

        @keyframes hours-gain-float {
            0% {
                opacity: 0;
                transform: translate(-50%, 15px) scale(0.7);
            }

            15% {
                opacity: 1;
                transform: translate(-50%, -10px) scale(1.2);
            }

            30% {
                transform: translate(-50%, -16px) scale(1);
            }

            75% {
                opacity: 1;
                transform: translate(-50%, -45px) scale(1);
            }

            100% {
                opacity: 0;
                transform: translate(-50%, -70px) scale(0.9);
            }
        }

        @keyframes hours-gain-pulse {
            0% {
                text-shadow: 0 0 0 rgba(46, 204, 113, 0);
            }

            30% {
                text-shadow: 0 0 18px rgba(46, 204, 113, 0.85);
                color: #2ecc71;
            }

            100% {
                text-shadow: 0 0 0 rgba(46, 204, 113, 0);
            }
        }

        .hours-gain-pulse {
            animation: hours-gain-pulse 1.6s ease-out;
        }

        /* Badge Ranking */
        .ranking-badge {
            background: var(--shell-red-dim);
            color: var(--shell-red);
            border: 1px solid rgba(230, 57, 70, 0.25);
            padding: 5px 12px;
            border-radius: 8px;
            font-size: 0.75rem;
            font-weight: bold;
        }

        /* Animación sutil de pulso para cuando hay retraso */
        @keyframes pulse-red {
            0% {
                box-shadow: 0 0 0 0 var(--shell-red-glow);
            }

            70% {
                box-shadow: 0 0 0 15px rgba(230, 57, 70, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(230, 57, 70, 0);
            }
        }

        /* Clase dinámica para el card cuando hay retraso */
        .card-delayed {
            border-color: rgba(230, 57, 70, 0.5) !important;
            background: linear-gradient(145deg, var(--shell-surface) 0%, var(--shell-red-dim) 100%) !important;
        }

        .status-badge-critical {
            background: var(--shell-red);
            color: #fff;
            padding: 4px 10px;
            border-radius: 6px;
            text-transform: uppercase;
            font-size: 0.65rem;
            letter-spacing: 1px;
            animation: pulse-red 2s infinite;
        }

        .hr-shell {
            border-color: var(--shell-border);
        }

        .icon-box {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .text-shell-muted {
            color: var(--shell-muted) !important;
        }

        .dig-modal-close {
            filter: none;
        }

        [data-theme="dark"] .dig-modal-close {
            filter: invert(1) grayscale(100%) brightness(200%);
        }

        @media (max-width: 575.98px) {
            .btn-action,
            .btn-outline-shell {
                width: 100%;
                justify-content: center;
                text-align: center;
            }
        }
    </style>

    @include('dig.partials.daily-progress-bar')

    <div id="home-container">
        <div class="dig-page-header">
            <div>
                <span class="dig-page-tag">Trakio · Gesi</span>
                <h1 class="dig-page-title">Buen día, <span style="color: var(--shell-red);">{{ explode(' ', $user->name)[0] }}</span></h1>
                <p class="dig-page-subtitle">Panel de productividad del digitador</p>
            </div>
            <span class="dig-page-badge"><span class="pulse"></span> En vivo</span>
        </div>

        <div class="container-fluid px-0">

            {{-- FILA 1: PERFIL Y MÉTRICA PRINCIPAL --}}
            <div class="row g-4 mb-4">
                <div class="col-lg-8">
                    <div class="modern-card p-4 h-100 d-flex align-items-center">
                        <div class="row w-100">
                            <div class="col-md-8">
                                <div class="mb-4">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-shell-muted small">Progreso Mensual (Meta 184h)</span>
                                        <span class="fw-bold" id="txt_progreso_horas">0%</span>
                                    </div>
                                    <div class="progress-custom mb-2">
                                        <div class="progress-bar-fill" style="width: 0%;"></div>
                                    </div>
                                    <small class="text-shell-muted" id="meta_siguiente_val">Calculando posición en tiempo
                                        real...</small>
                                </div>

                                <div class="d-flex flex-wrap gap-3 mt-4">
                                    <a href="{{ route('productivity.index') }}" class="btn btn-action">
                                        <i class='bx bx-plus-circle me-2'></i>Reportar productividad
                                    </a>
                                    <button class="btn btn-outline-shell" data-bs-toggle="modal"
                                        data-bs-target="#otherTimeModal">
                                        Solicitar tiempos
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-4 d-none d-md-flex align-items-center justify-content-center">
                                <div class="avatar-wrapper">
                                    <div class="avatar-glow"></div>
                                    @if (!empty($data_user))
                                        <img class="rounded-circle shadow-lg avatar-frame" width="130"
                                            height="130"
                                            src="{{ asset('img/img_perfil/' . $data_user->id_user . '/' . $data_user->url_img) }}">
                                    @else
                                        <div class="rounded-circle d-flex align-items-center justify-content-center avatar-placeholder">
                                            <i class='bx bx-user fs-1 text-shell-muted'></i>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="modern-card p-4 h-100">
                        <div class="d-flex justify-content-between align-items-start mb-4">
                            <h6 class="text-shell-muted small fw-bold text-uppercase mb-0">Horas del mes</h6>
                            <span class="ranking-badge" id="ranking_pos_val">Cargando...</span>
                        </div>
                        <div class="text-center">
                            <div class="text-center" id="main-hours-card">
                                <h1 class="display-3 fw-bold mb-0" id="total_horas_mes_num">0</h1>

                                <div id="atraso_status_val"
                                    class="mt-2 d-flex flex-column align-items-center justify-content-center"
                                    style="min-height: 50px;">
                                </div>

                                <hr class="hr-shell">
                            </div>

                            <hr class="hr-shell">
                            <div class="row text-center">
                                <div class="col-6 border-end hr-shell">
                                    <small class="text-shell-muted d-block">Digitación</small>
                                    <span class="fw-bold" id="digitacion_hours">0h</span>
                                </div>
                                <div class="col-6">
                                    <small class="text-shell-muted d-block">Otras Tareas</small>
                                    <span class="fw-bold" id="approved_hours_summary">0h</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- FILA 2: COMPARATIVA DE EQUIPO --}}
            <div class="dig-kpi-grid mb-4">
                <div class="dig-kpi d-flex align-items-center">
                    <div class="icon-box me-3 p-3 rounded-4"
                        style="background: var(--shell-surface-2); color: var(--shell-text);">
                        <i class='bx bx-calendar-check fs-3'></i>
                    </div>
                    <div>
                        <h4 class="mb-0 fw-bold" id="horas_hasta_ayer_val">0h</h4>
                        <small class="text-shell-muted text-uppercase small">Total hasta Ayer</small>
                    </div>
                </div>
                <div class="dig-kpi d-flex align-items-center">
                    <div class="icon-box me-3 p-3 rounded-4"
                        style="background: rgba(0, 204, 255, 0.12); color: #00ccff;">
                        <i class='bx bx-time-five fs-3'></i>
                    </div>
                    <div>
                        <h4 class="mb-0 fw-bold" id="horas_hoy_val">0h</h4>
                        <small class="text-shell-muted text-uppercase small">Tiempo de Hoy</small>
                    </div>
                </div>
                <div class="dig-kpi d-flex align-items-center">
                    <div class="icon-box me-3 p-3 rounded-4"
                        style="background: var(--shell-red-dim); color: var(--shell-red);">
                        <i class='bx bx-line-chart fs-3'></i>
                    </div>
                    <div>
                        <h4 class="mb-0 fw-bold" id="promedio_equipo_val">0h</h4>
                        <small class="text-shell-muted text-uppercase small">Promedio Horas Mes</small>
                    </div>
                </div>
                <div class="dig-kpi d-flex align-items-center">
                    <div class="icon-box me-3 p-3 rounded-4"
                        style="background: var(--shell-surface-2); color: var(--shell-text);">
                        <i class='bx bx-file fs-3'></i>
                    </div>
                    <div>
                        <h4 class="mb-0 fw-bold" id="CanDia">0</h4>
                        <small class="text-shell-muted text-uppercase small">Fichas Hoy</small>
                    </div>
                </div>
               
                <div class="dig-kpi d-flex align-items-center">
                    <div class="icon-box me-3 p-3 rounded-4"
                        style="background: rgba(46, 204, 113, 0.12); color: #2ecc71;">
                        <i class='bx bx-check-double fs-3'></i>
                    </div>
                    <div>
                        <h4 class="mb-0 fw-bold" id="fichas_digitadas_mes">0</h4>
                        <small class="text-shell-muted text-uppercase small">Acumulado Mes</small>
                    </div>
                </div>
            </div>

            {{-- FILA 2.5: FICHAS PENDIENTES POR DIGITAR (status 11, recibidas por GESI) --}}
            <div class="row g-4 mb-4">
                <div class="col-12">
                    <div class="modern-card p-4 {{ $totalVencidasDigitar > 0 ? 'card-delayed' : '' }}">
                        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
                            <div>
                                <h6 class="text-shell-muted small fw-bold text-uppercase mb-1">
                                    <i class='bx bx-file-blank me-1'></i> Fichas pendientes por digitar
                                </h6>
                                <small class="text-shell-muted">Recibidas por GESI y sin digitar (estado 11). Plazo de
                                    digitación: 2 días desde el recibido.</small>
                            </div>
                            <div class="text-end">
                                <h2 class="mb-0 fw-bold">{{ $totalPendientesDigitar }}</h2>
                                @if ($totalVencidasDigitar > 0)
                                    <span class="status-badge-critical">{{ $totalVencidasDigitar }} vencida{{ $totalVencidasDigitar > 1 ? 's' : '' }}</span>
                                @else
                                    <span class="ranking-badge">Al día</span>
                                @endif
                            </div>
                        </div>

                        @if ($pendientesPorEntorno->isEmpty())
                            <p class="text-shell-muted small mb-0">No hay fichas pendientes por digitar. ¡Vas al día!</p>
                        @else
                            <div class="row g-3">
                                @foreach ($pendientesPorEntorno as $item)
                                    <div class="col-md-6 col-lg-4">
                                        <div class="dig-kpi {{ $item->vencidas > 0 ? 'card-delayed' : '' }}">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <div>
                                                    <small class="text-shell-muted text-uppercase d-block">{{ $item->environment_name ?? 'Sin entorno' }}</small>
                                                    <h4 class="mb-0 fw-bold">{{ $item->total }}</h4>
                                                </div>
                                                @if ($item->vencidas > 0)
                                                    <span class="status-badge-critical">{{ $item->vencidas }} vencida{{ $item->vencidas > 1 ? 's' : '' }}</span>
                                                @endif
                                            </div>
                                            <div class="d-flex gap-3 mt-2 pt-2 border-top hr-shell">
                                                <small class="text-shell-muted">
                                                    <i class='bx bx-refresh' style="color:#f5a623;"></i>
                                                    Actualización: <strong>{{ $item->actualizacion }}</strong>
                                                </small>
                                                <small class="text-shell-muted">
                                                    <i class='bx bx-file-blank' style="color:#2ecc71;"></i>
                                                    Nuevas: <strong>{{ $item->nuevas }}</strong>
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- FILA 3: GRÁFICA Y ACTIVIDADES --}}
            <div class="row g-4">
                <div class="col-xl-8">
                    <div class="dig-card">
                        <div class="dig-card-head">
                            <h5 class="dig-card-title">Rendimiento 7 días</h5>
                            <i class='bx bx-dots-horizontal-rounded text-shell-muted'></i>
                        </div>
                        <div class="chart-wrap-md">
                            <canvas id="productivity-chart"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4">
                    <div class="dig-card">
                        <div class="dig-card-head">
                            <h5 class="dig-card-title">Actividades extra</h5>
                        </div>
                        <div class="scrollable-content" id="other-times-list" style="height:320px; overflow-y: auto;">
                            <div class="text-center py-5">
                                <div class="spinner-border text-danger spinner-border-sm" role="status"></div>
                                <p class="text-shell-muted small mt-2">Cargando actividades...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL SOLICITUD TIEMPOS (Estilizado) --}}
    <div class="modal fade" id="otherTimeModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; background: var(--shell-surface); color: var(--shell-text);">
                <div class="modal-header border-0 p-4 pb-0">
                    <h5 class="modal-title fw-bold">Solicitar Tiempos Extra</h5>
                    <button type="button" class="btn-close dig-modal-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="otherTimeForm" class="p-4" action="{{ route('dig.request.othertimes') }}" method="post">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="text-shell-muted small mb-1">Horas Solicitadas</label>
                            <input class="form-control p-2" type="number"
                                name="time_requested" max="8" step="0.1" placeholder="Ej: 2.5" required>
                        </div>
                        <div class="col-md-6">
                            <label class="text-shell-muted small mb-1">Fecha de Actividad</label>
                            <input class="form-control" type="date"
                                name="date_activity" required>
                        </div>
                        <div class="col-12">
                            <label class="text-shell-muted small mb-1">Técnico Responsable</label>
                            <select class="form-select" name="technician_id"
                                required>
                                <option value="">Seleccionar técnico...</option>
                                @foreach ($technicians as $technician)
                                    <option value="{{ $technician->id }}">
                                        {{ properNouns($technician->name, $technician->last_name) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="text-shell-muted small mb-1">Actividad Realizada</label>
                            <select name="activity_id" class="form-select"
                                data-request-ot="activity" required>
                                <option value="">Seleccionar actividad...</option>
                                <option value="1">Alistamiento de auditoria</option>
                                <option value="2">Apoyo digitación</option>
                                <option value="3">Corrección de hallazgos</option>
                                <option value="4">Digitación formatos cuídate, sé feliz</option>
                                <option value="5">Digitación formatos sisco</option>
                                <option value="6">Organización de archivo</option>
                                <option value="7">Radicación de documentos</option>
                                <option value="8">Recolección de carnets</option>
                                <option value="9">Revisión formatos</option>
                                <option value="10">Devolución de formatos</option>
                                <option value="11">Reunión</option>
                            </select>
                        </div>

                        {{-- Selects Dinámicos (JS) --}}
                        <div class="col-12">
                            <select class="form-select mb-2"
                                data-request-ot="step-2-ot" hidden required></select>
                            <select class="form-select" data-request-ot="step-3-ot"
                                style="height: auto" hidden required></select>
                        </div>

                        <div class="col-12">
                            <label class="text-shell-muted small mb-1">Resumen generado</label>
                            <textarea class="form-control" name="description" rows="2"
                                data-request-ot="description-ot" readonly required></textarea>
                        </div>
                    </div>
                    <div class="mt-4 d-flex gap-2">
                        <button type="submit" class="btn btn-action w-100">Enviar solicitud</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
