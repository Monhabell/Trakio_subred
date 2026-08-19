@extends('layouts/env/navigation')

@section('main')
    <style>
        /* Estructura para el scroll interno */
        .reception-container {
            background-color: var(--shell-bg);
            min-height: calc(100dvh - var(--shell-nav-h));
            display: flex;
            flex-direction: column;
            color: var(--shell-text);
            font-family: var(--shell-sans);
        }

        .header-section {
            padding: 1.5rem clamp(1rem, 4vw, 2rem);
            flex-shrink: 0;
        }

        .scrollable-content {
            flex-grow: 1;
            overflow-y: auto;
            padding: 0 clamp(1rem, 4vw, 2rem);
            padding-bottom: 120px;
            /* Espacio para el botón flotante */
        }

        /* Footer con Botón Siempre Visible */
        .sticky-submit-container {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 2rem 1rem;
            background: linear-gradient(to top, var(--shell-bg) 60%, transparent);
            text-align: center;
            z-index: 1000;
            pointer-events: none;
            /* Deja pasar clicks al fondo excepto al botón */
        }

        .btn-receive-all {
            pointer-events: auto;
            /* Reactiva clicks para el botón */
            background: var(--shell-red);
            color: #fff;
            padding: 1rem 3.5rem;
            border-radius: 50px;
            font-weight: 700;
            border: none;
            box-shadow: 0 8px 25px var(--shell-red-glow);
            transition: all 0.3s;
            text-transform: uppercase;
            letter-spacing: 1px;
            max-width: 100%;
        }

        .btn-receive-all:hover {
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 12px 30px var(--shell-red-glow);
            background: var(--shell-red);
            filter: brightness(1.1);
        }

        /* Card de paquete, sobre .env-card */
        .package-card {
            border-radius: 16px;
        }

        .package-header {
            padding: 1.25rem;
            border-bottom: 1px solid var(--shell-border);
            background: var(--shell-surface-2);
            border-radius: 16px 16px 0 0;
        }

        .file-scroll-area {
            max-height: 220px;
            overflow-y: auto;
            padding: 1rem;
        }

        .file-item {
            background: var(--shell-surface-2);
            border-radius: 10px;
            padding: 0.8rem 1rem;
            margin-bottom: 0.6rem;
            border-left: 4px solid transparent;
            transition: 0.2s;
            cursor: pointer;
        }

        .file-item:hover {
            border-color: var(--shell-border-strong);
            background: color-mix(in srgb, var(--shell-surface-2) 80%, var(--shell-text) 6%);
        }

        .file-item.is-checked {
            border-left-color: var(--shell-red);
            background: var(--shell-red-dim);
        }

        .stats-badge {
            background: var(--shell-surface);
            border: 1px solid var(--shell-border);
            padding: 0.5rem 1.2rem;
            border-radius: 10px;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .custom-checkbox {
            width: 22px;
            height: 22px;
            cursor: pointer;
            accent-color: var(--shell-red);
            flex-shrink: 0;
        }

        .btn-verify {
            background: transparent;
            border: 1px solid var(--shell-border-strong);
            color: var(--shell-text);
            transition: all 0.3s;
        }

        .btn-verify:hover {
            background: var(--shell-red);
            border-color: var(--shell-red);
            color: #fff;
            transform: translateY(-2px);
        }

        /* Scrollbar Personalizada */
        .scrollable-content::-webkit-scrollbar,
        .file-scroll-area::-webkit-scrollbar {
            width: 6px;
        }

        .scrollable-content::-webkit-scrollbar-thumb,
        .file-scroll-area::-webkit-scrollbar-thumb {
            background: var(--shell-border-strong);
            border-radius: 10px;
        }

        .env-modal-close {
            filter: none;
        }

        [data-theme="dark"] .env-modal-close {
            filter: invert(1) grayscale(100%);
        }

        @media (max-width: 576px) {
            .header-section .row > div {
                margin-bottom: 0.75rem;
            }

            .btn-receive-all {
                padding: 0.9rem 2rem;
                width: 100%;
            }

            .sticky-submit-container {
                padding: 1.25rem 1rem;
            }
        }
    </style>

    <div class="reception-container">
        <div class="header-section">
            <div class="env-page-header" style="margin-bottom: 0; border-bottom: none; padding-bottom: 0;">
                <div>
                    <span class="env-page-tag">Gestión de paquetes</span>
                    <h2 class="env-page-title">RECEPCIÓN DE <span style="color: var(--shell-red)">FICHAS</span></h2>
                    <p class="env-page-subtitle mb-0">Seleccione los paquetes y confirme la recepción masiva.</p>
                </div>
                <div class="stats-badge">
                    <div>
                        <small class="d-block text-uppercase" style="font-size: 0.6rem; color: var(--shell-muted);">Total
                            Pendientes</small>
                        <span class="h4 mb-0 fw-bold" style="color: var(--shell-red)">{{ $receptions_quantity }}</span>
                    </div>
                    <i class="bi bi-box-seam h4 mb-0" style="color: var(--shell-muted);"></i>
                </div>
            </div>
        </div>

        <div class="scrollable-content">
            @if ($packages->count() > 0)
                <form method="POST" id="formReception" action="{{ route('env.receive') }}">
                    @csrf
                    <div class="row g-4">
                        @foreach ($packages as $packageId => $items)
                            @php
                                $firstItem = $items->first();
                                $packageName = $firstItem->packages->num_package ?? 'N/A';
                            @endphp
                            <div class="col-xl-4 col-md-6">
                                <div class="env-card package-card p-0">
                                    <div class="package-header">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <div class="d-flex align-items-center gap-3">
                                                <input type="checkbox" class="custom-checkbox select-package"
                                                    data-package="{{ $packageId }}" id="check-pkg-{{ $packageId }}">
                                                <label class="h5 mb-0 fw-bold" for="check-pkg-{{ $packageId }}">Paquete
                                                    #{{ $packageName }}</label>
                                            </div>
                                            <span class="env-badge env-badge-red">
                                                {{ $items->count() }} fichas
                                            </span>
                                        </div>
                                        <div class="small" style="color: var(--shell-muted);">
                                            <i class="bi bi-clock-history me-1"></i> Retorno:
                                            {{ \Carbon\Carbon::parse($firstItem->return_date)->format('d/m/Y') }}
                                        </div>
                                    </div>

                                    <div class="file-scroll-area">
                                        @foreach ($items as $reception)
                                            <div class="file-item" id="item-{{ $reception->id }}">
                                                <input type="checkbox" name="receptionsFiles[]" value="{{ $reception->id }}"
                                                    class="check-item d-none" data-parent-package="{{ $packageId }}">

                                                <div class="d-flex justify-content-between align-items-start mb-1">
                                                    <span class="fw-bold"
                                                        style="color: var(--shell-text);">{{ $reception->file_number }}</span>
                                                    <small style="color: var(--shell-muted);">ORDEN {{ $reception->order_file }}</small>
                                                </div>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span style="font-size: 0.75rem; color: var(--shell-muted);">
                                                        <i
                                                            class="bi bi-geo-alt-fill me-1"></i>{{ $reception->bases->name }}
                                                    </span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>

                                    <div class="p-3 mt-auto">
                                        <button type="button" class="env-btn env-btn-ghost btn-verify w-100 justify-content-center" data-bs-toggle="modal"
                                            data-bs-target="#modal-{{ $packageId }}">
                                            <i class="bi bi-eye me-2"></i>Verificar detalles
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="modal fade" id="modal-{{ $packageId }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-lg modal-dialog-centered">
                                    <div class="modal-content" style="background: var(--shell-surface); border: 1px solid var(--shell-border-strong); color: var(--shell-text);">
                                        <div class="modal-header" style="border-bottom: 1px solid var(--shell-border);">
                                            <h5 class="modal-title" style="color: var(--shell-text);">Detalles Paquete #{{ $packageName }}</h5>
                                            <button type="button" class="btn-close env-modal-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body p-0">
                                            <div class="env-table-wrap">
                                                <table class="env-table mb-0">
                                                    <thead>
                                                        <tr>
                                                            <th class="ps-4">ORDEN</th>
                                                            <th>FICHA</th>
                                                            <th>BASE</th>
                                                            <th>OBSERVACIONES</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($items as $reception)
                                                            <tr>
                                                                <td class="ps-4 fw-bold" style="color: var(--shell-red);">
                                                                    {{ $reception->order_file }}</td>
                                                                <td>{{ $reception->file_number }}</td>
                                                                <td>{{ $reception->bases->name }}</td>
                                                                <td class="small" style="color: var(--shell-muted);">
                                                                    {{ $reception->productivity->observations ?? '--' }}
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
                        @endforeach
                    </div>

                    <div class="sticky-submit-container">
                        <button type="submit" class="btn btn-receive-all">
                            <i class="bi bi-check2-circle me-2"></i> Recepcionar Seleccionados
                        </button>
                    </div>
                </form>
            @else
                <div class="env-empty-state">
                    <i class="bi bi-inbox"></i>
                    <span class="env-empty-title">No hay paquetes pendientes</span>
                </div>
            @endif
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Seleccionar todo el paquete
            document.querySelectorAll('.select-package').forEach(mainCheckbox => {
                mainCheckbox.addEventListener('change', function() {
                    const pkgId = this.dataset.package;
                    const itemCheckboxes = document.querySelectorAll(
                        `.check-item[data-parent-package="${pkgId}"]`);
                    itemCheckboxes.forEach(cb => {
                        cb.checked = this.checked;
                        const itemDiv = cb.closest('.file-item');
                        itemDiv.classList.toggle('is-checked', this.checked);
                    });
                });
            });

            // Click en el item para seleccionar individualmente
            document.querySelectorAll('.file-item').forEach(item => {
                item.style.cursor = 'pointer';
                item.addEventListener('click', function(e) {
                    if (e.target.tagName !== 'INPUT') {
                        const cb = this.querySelector('.check-item');
                        cb.checked = !cb.checked;
                        cb.dispatchEvent(new Event('change', {
                            bubbles: true
                        }));
                    }
                });
            });

            document.querySelectorAll('.check-item').forEach(cb => {
                cb.addEventListener('change', function() {
                    const itemDiv = this.closest('.file-item');
                    const pkgId = this.getAttribute('data-parent-package');
                    const mainCheckbox = document.getElementById(`check-pkg-${pkgId}`);

                    itemDiv.classList.toggle('is-checked', this.checked);
                    if (!this.checked) {
                        mainCheckbox.checked = false;
                    }
                });
            });
        });
    </script>
@endsection
