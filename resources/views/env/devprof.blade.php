@extends('layouts/env/navigation')

@section('main')
<style>
    .return-container {
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
        background: linear-gradient(to bottom, var(--shell-red-dim) 0%, transparent 100%);
    }

    /* Buscador y Filtros */
    .search-bar {
        background: var(--shell-surface-2);
        border: 1px solid var(--shell-border-strong);
        border-radius: 12px;
        padding: 0.5rem 1rem;
        color: var(--shell-text);
    }

    .search-bar::placeholder {
        color: var(--shell-muted-2);
    }

    .scrollable-content {
        flex-grow: 1;
        overflow-y: auto;
        padding: 0 clamp(1rem, 4vw, 2rem) 140px clamp(1rem, 4vw, 2rem);
    }

    /* Tarjetas de Devolución */
    .return-card {
        background: var(--shell-surface);
        border: 1px solid var(--shell-border);
        border-radius: 16px;
        transition: all 0.3s;
        height: 100%;
    }

    .return-card:hover {
        border-color: var(--shell-red);
        box-shadow: 0 0 20px var(--shell-red-glow);
    }

    .card-header-return {
        padding: 1.25rem;
        border-bottom: 1px solid var(--shell-border);
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 0.75rem;
    }

    .file-item-return {
        background: var(--shell-surface-2);
        margin-bottom: 0.5rem;
        padding: 0.8rem;
        border-radius: 8px;
        border-right: 3px solid transparent;
        transition: 0.2s;
    }

    .file-item-return.selected {
        border-right-color: var(--shell-red);
        background: var(--shell-red-dim);
    }

    /* Footer Fijo */
    .sticky-footer {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        padding: 1.5rem clamp(1rem, 4vw, 2rem);
        background: color-mix(in srgb, var(--shell-bg) 95%, transparent);
        backdrop-filter: blur(10px);
        border-top: 1px solid var(--shell-border);
        z-index: 1000;
        flex-wrap: wrap;
        gap: 0.75rem;
    }

    .btn-return-action {
        background: var(--shell-red);
        color: #ffffff;
        font-weight: 800;
        padding: 0.8rem 2.5rem;
        border-radius: 50px;
        border: none;
        transition: 0.3s;
    }

    .btn-return-action:hover {
        transform: scale(1.05);
        box-shadow: 0 0 25px var(--shell-red-glow);
    }

    .input-receiver {
        background: var(--shell-surface-2);
        border: 1px solid var(--shell-border-strong);
        color: var(--shell-text);
        border-radius: 8px;
        padding: 0.5rem 1rem;
        width: 300px;
    }

    .input-receiver:focus {
        border-color: var(--shell-red);
        outline: none;
        box-shadow: 0 0 10px var(--shell-red-glow);
    }

    .custom-checkbox {
        width: 20px;
        height: 20px;
        cursor: pointer;
        accent-color: var(--shell-red);
        flex-shrink: 0;
    }

    .stats-badge {
        background: var(--shell-surface);
        border: 1px solid var(--shell-border);
    }

    @media (max-width: 768px) {
        .search-bar.w-50 {
            width: 100% !important;
        }

        .header-section .d-flex.gap-3 {
            flex-direction: column;
            align-items: stretch;
            width: 100%;
        }
    }

    @media (max-width: 576px) {
        .sticky-footer {
            flex-direction: column;
            align-items: stretch;
            text-align: center;
        }

        .sticky-footer .text-end {
            text-align: center !important;
        }

        .btn-return-action {
            width: 100%;
            justify-content: center;
        }
    }
</style>

<div class="return-container">
    <div class="header-section">
        <div class="env-page-header" style="margin-bottom: 0; border-bottom: none; padding-bottom: 0;">
            <div>
                <span class="env-page-tag">Gestión de paquetes</span>
                <h2 class="env-page-title">ENTREGA A <span style="color: var(--shell-red)">PROFESIONAL</span></h2>
                <p class="env-page-subtitle mb-0">Entrega de paquetes procesados al equipo profesional.</p>
            </div>
            <div class="d-flex gap-3 flex-wrap justify-content-md-end">
                <input type="text" id="packageSearch" class="search-bar w-50" placeholder="🔍 Buscar por Profesional o Ficha...">
                <div class="stats-badge p-2 px-3 rounded-3 d-flex align-items-center">
                    <span class="small me-2" style="color: var(--shell-muted);">LISTOS:</span>
                    <span class="h5 mb-0 fw-bold" style="color: var(--shell-red);">{{ $packages->count() }} Paquetes</span>
                </div>
            </div>
        </div>
    </div>

    <div class="scrollable-content">
        <form method="POST" action="{{ route('env.return.process') }}" id="formReturn">
            @csrf
            <div class="row g-4 mt-2">
                @foreach ($packages as $packageId => $items)
                    @php 
                        $first = $items->first();
                        // Obtenemos los nombres únicos de los profesionales vinculados a este paquete
                        $profesionales = $items->flatMap(function($item) {
                            return $item->users->map(function($u) {
                                return $u->name . ' ' . $u->last_name;
                            });
                        })->unique()->implode(', ');
                    @endphp

                    <div class="col-xl-4 col-md-6 package-grid-item">
                        <div class="return-card">
                            <div class="card-header-return">
                                <div>
                                    <div class="d-flex align-items-center gap-2 flex-wrap">
                                        <input type="checkbox" class="custom-checkbox select-all-pkg" data-package="{{ $packageId }}">
                                        <h5 class="mb-0 fw-bold" style="color: var(--shell-text);">Paquete #{{ $first->packages->num_package ?? $packageId }}</h5>
                                    </div>
                                    <span class="d-block small mt-1" style="color: var(--shell-muted);">fecha de recepcion: {{ \Carbon\Carbon::parse($first->return_date ?? 'Sin fecha')->format('d/m/Y') }}</span>

                                    <small style="color: var(--shell-red);">
                                        <i class="bi bi-person-badge me-1"></i>
                                        {{ Str::limit($profesionales, 40) }}
                                    </small>
                                </div>
                                <span class="env-badge env-badge-red">
                                    {{ $items->count() }} fichas
                                </span>
                            </div>

                            <div class="p-3" style="max-height: 250px; overflow-y: auto;">
                                @foreach ($items as $item)
                                    <div class="file-item-return d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center gap-2">
                                            <input hidden type="checkbox" name="selected_files[]" value="{{ $item->id }}"
                                                class="item-checkbox" data-parent="{{ $packageId }}">
                                            <div>
                                                <div class="fw-bold small" style="color: var(--shell-text);">{{ $item->file_number }}</div>
                                                <div class="fw-bold small" style="color: var(--shell-text);">{{ $item->bases->name ?? 'Sin base' }}</div>

                                                <div style="font-size: 0.65rem; color: var(--shell-muted);">
                                                    {{ $item->users->pluck('last_name')->implode(', ') }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="text-end">
                                            <span class="env-badge" style="font-size: 0.6rem;">ORDEN {{ $item->order_file }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="sticky-footer d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-3">
                    <span style="color: var(--shell-muted);">Preparando para devolver</span>
                </div>

                <div class="text-end">
                    <span class="me-4 small" style="color: var(--shell-muted);">Items seleccionados: <b id="selectedCount" style="color: var(--shell-info);">0</b></span>
                    <button type="submit" class="btn btn-return-action">
                        <i class="bi bi-arrow-left-right me-2"></i> REGISTRAR ENTREGA
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const selectedCountLabel = document.getElementById('selectedCount');
        
        // Actualizar contador
        const updateCounter = () => {
            const count = document.querySelectorAll('.item-checkbox:checked').length;
            selectedCountLabel.innerText = count;
        };

        // Seleccionar todo el paquete
        document.querySelectorAll('.select-all-pkg').forEach(headerCheck => {
            headerCheck.addEventListener('change', function() {
                const pkgId = this.dataset.package;
                const children = document.querySelectorAll(`.item-checkbox[data-parent="${pkgId}"]`);
                children.forEach(c => {
                    c.checked = this.checked;
                    c.closest('.file-item-return').classList.toggle('selected', this.checked);
                });
                updateCounter();
            });
        });

        // Selección individual
        document.querySelectorAll('.item-checkbox').forEach(check => {
            check.addEventListener('change', function() {
                this.closest('.file-item-return').classList.toggle('selected', this.checked);
                updateCounter();
            });
        });

        // Buscador básico en vivo
        document.getElementById('packageSearch').addEventListener('input', function(e) {
            const term = e.target.value.toLowerCase();
            document.querySelectorAll('.package-grid-item').forEach(card => {
                const text = card.innerText.toLowerCase();
                card.style.display = text.includes(term) ? 'block' : 'none';
            });
        });
    });
</script>
@endsection