@php
    $alerts = $delayAlerts ?? collect();

    $alertsByUser = $alertsByUser ?? collect();

    if ($alertsByUser->isEmpty()) {
        $alertsByUser = $alerts->flatMap(function ($alert) {
            return $alert->users->map(function ($user) {
                return [
                    'name' => trim(($user->name ?? '') . ' ' . ($user->last_name ?? '')),
                    'id' => $user->id,
                ];
            });
        })
        ->filter(fn($item) => !empty($item['name']))
        ->groupBy('name')
        ->map(function ($items) {
            return [
                'count' => $items->count(),
                'name' => $items->first()['name'],
            ];
        })
        ->sortByDesc('count')
        ->values();
    }
@endphp



@if ($alerts->isNotEmpty())
    <div class="dropdown delay-alerts-dropdown">
        <button type="button"
                id="delayAlertsToggle"
                class="delay-alerts-btn dropdown-toggle"
                data-bs-toggle="dropdown"
                data-bs-auto-close="outside"
                aria-expanded="false">
            <span class="delay-alerts-icon">
                <i class="fas fa-exclamation-triangle"></i>
            </span>
            <span class="delay-alerts-label">
                <strong>{{ $alerts->count() }}</strong> retraso{{ $alerts->count() === 1 ? '' : 's' }}
            </span>
        </button>

        <div class="dropdown-menu dropdown-menu-end delay-alerts-menu p-0" aria-labelledby="delayAlertsToggle">
            <div class="delay-alerts-menu-head d-flex align-items-start justify-content-between gap-2">
                <div>
                    <div class="fw-bold">¡Urgente!</div>
                    <div class="small">Fichas con fecha de intervención superior a 5 días sin registrar ni entregar a gesi.</div>
                </div>
                <a href="{{ route('reviewconsecutives.delay-alerts.export', request()->query()) }}"
                   class="delay-alerts-export-btn flex-shrink-0"
                   title="Descargar Excel">
                    <i class="fas fa-file-excel"></i>
                </a>
            </div>

            <div class="accordion accordion-flush" id="accordionDelayAlerts">

                {{-- Resumen por profesional --}}
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed delay-alerts-accordion-btn"
                                type="button"
                                data-bs-toggle="collapse"
                                data-bs-target="#collapseUsers">
                            <i class="fas fa-users me-2"></i>
                            Alertas por profesional
                        </button>
                    </h2>

                    <div id="collapseUsers"
                        class="accordion-collapse collapse"
                        data-bs-parent="#accordionDelayAlerts">

                        <div class="accordion-body p-2">
                            <div class="d-flex flex-column gap-2 delay-alerts-scroll" style="max-height: 220px;">
                                @foreach($alertsByUser as $userData)
                                    <div class="d-flex justify-content-between align-items-center delay-alerts-row py-2 px-1">
                                        <span class="small fw-semibold">{{ $userData['name'] }}</span>
                                        <span class="badge bg-secondary">
                                            {{ $userData['count'] }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Detalle de registros --}}
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed delay-alerts-accordion-btn"
                                type="button"
                                data-bs-toggle="collapse"
                                data-bs-target="#collapseAlerts">
                            <i class="fas fa-file-alt me-2"></i>
                            Ver registros ({{ $alerts->count() }})
                        </button>
                    </h2>

                    <div id="collapseAlerts"
                         class="accordion-collapse collapse"
                         data-bs-parent="#accordionDelayAlerts">

                        <div class="accordion-body p-2">

                            <div class="d-flex flex-column gap-2 delay-alerts-scroll" style="max-height: 320px;">

                                @foreach ($alerts as $alert)

                                    <div class="rounded-3 delay-alerts-item p-2">

                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <div class="fw-bold">
                                                    #{{ $alert->file_number }}
                                                </div>

                                                <div class="small delay-alerts-muted">
                                                    {{ $alert->environment_file->entorno ?? 'Sin entorno' }}
                                                    ·
                                                    {{ $alert->bases->name ?? 'Sin base' }}
                                                </div>

                                                <div class="small mt-1">

                                                    <strong>Profesional:</strong>

                                                    @foreach($alert->users as $user)
                                                        <span class="badge bg-secondary">
                                                            {{ $user->name }}
                                                        </span>
                                                    @endforeach

                                                </div>

                                            </div>

                                            <span class="badge bg-warning text-dark">
                                                {{ $alert->days_delay }} días
                                            </span>
                                        </div>

                                        <div class="small mt-1 delay-alerts-muted">
                                            Intervención:
                                            {{ \Carbon\Carbon::parse($alert->fecha_intervencion)->format('d/m/Y') }}
                                        </div>

                                        <div class="small delay-alerts-muted">
                                            Estado:
                                            {{ getStatusNameFile($alert->status) }}
                                        </div>

                                    </div>

                                @endforeach

                            </div>

                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <style>
        .delay-alerts-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.7rem;
            background: var(--shell-surface, #2b0f0f);
            border: 2px solid var(--shell-danger, #b30000);
            color: var(--shell-text, #fff);
            border-radius: 999px;
            padding: 0.55rem 1.25rem 0.55rem 0.55rem;
            font-size: 0.95rem;
            font-weight: 600;
            line-height: 1;
            box-shadow: 0 0 0 0 rgba(255, 77, 77, 0.45);
            animation: delay-alerts-pulse 2s infinite;
        }

        .delay-alerts-btn:hover {
            border-color: var(--shell-red, #ff4d4d);
        }

        .delay-alerts-btn::after {
            display: none;
        }

        @keyframes delay-alerts-pulse {
            0% { box-shadow: 0 0 0 0 rgba(255, 77, 77, 0.45); }
            70% { box-shadow: 0 0 0 9px rgba(255, 77, 77, 0); }
            100% { box-shadow: 0 0 0 0 rgba(255, 77, 77, 0); }
        }

        .delay-alerts-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: linear-gradient(135deg, #ff4d4d 0%, #b30000 100%);
            color: #fff;
            font-size: 0.95rem;
            flex-shrink: 0;
        }

        .delay-alerts-label strong {
            color: var(--shell-red, #ff4d4d);
        }

        .delay-alerts-export-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 6px;
            background: rgba(46, 204, 113, .12);
            border: 1px solid rgba(46, 204, 113, .35);
            color: #2ecc71;
            font-size: 0.95rem;
            text-decoration: none;
        }

        .delay-alerts-export-btn:hover {
            background: rgba(46, 204, 113, .22);
            color: #2ecc71;
        }

        .delay-alerts-menu {
            width: 360px;
            max-width: calc(100vw - 2rem);
            background: var(--shell-surface, #2b0f0f);
            border: 1px solid var(--shell-border-strong, rgba(255,255,255,.16));
            border-radius: 10px;
            box-shadow: 0 16px 32px rgba(0, 0, 0, 0.55);
            color: var(--shell-text, #fff);
            overflow: hidden;
        }

        .delay-alerts-menu-head {
            padding: 0.85rem 1rem 0.65rem;
            border-bottom: 1px solid var(--shell-border, rgba(255,255,255,.08));
        }

        .delay-alerts-menu-head .small {
            color: var(--shell-muted, rgba(255,255,255,.6));
        }

        .delay-alerts-accordion-btn {
            background: transparent;
            color: var(--shell-text, #fff);
        }

        .delay-alerts-accordion-btn:not(.collapsed) {
            background: var(--shell-surface-2, rgba(255,255,255,.06));
            color: var(--shell-text, #fff);
        }

        .delay-alerts-accordion-btn::after {
            filter: invert(1) grayscale(100%) brightness(200%);
        }

        .delay-alerts-row,
        .delay-alerts-item {
            border-bottom: 1px solid var(--shell-border, rgba(255,255,255,.08));
        }

        .delay-alerts-item {
            background: var(--shell-surface-2, rgba(255,255,255,.06));
            border: 1px solid var(--shell-border, rgba(255,255,255,.08));
        }

        .delay-alerts-muted {
            color: var(--shell-muted, rgba(255,255,255,.6));
        }

        .delay-alerts-scroll {
            overflow-y: auto;
            scrollbar-width: thin;
        }

        @media (max-width: 576px) {
            .delay-alerts-menu {
                width: calc(100vw - 2rem);
            }

            .delay-alerts-label {
                display: none;
            }

            .delay-alerts-btn {
                padding: 0.4rem;
            }
        }
    </style>
@endif
