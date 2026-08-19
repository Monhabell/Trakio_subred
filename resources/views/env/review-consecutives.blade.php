@extends('layouts/env/navigation')

@section('main')

    <div class="env-page-header">
        <div>
            <span class="env-page-tag">Gestión documental</span>
            <h1 class="env-page-title">Solicitudes</h1>
        </div>

        <div>
             @include('components.reception-delay-alerts', ['delayAlerts' => $delayAlerts ?? collect() , 'alertsByUser' => $alertsByUser ?? collect()])
        </div>
    </div>

    <div class="w-100 d-flex gap-2 mb-3">
        <button type="button" class="env-btn env-btn-primary btn-section-toggle active w-50 justify-content-center" data-target="section-consecutivos">
            <i class="fas fa-list-ol me-2"></i>Consecutivos
        </button>
        <button type="button" class="env-btn env-btn-ghost btn-section-toggle w-50 justify-content-center" data-target="section-sisco">
            <i class="fas fa-exchange-alt me-2"></i>SISCO
        </button>
    </div>

    <section id="section-consecutivos" class="content-section mt-2">

        {{-- Botón visible solo en mobile para abrir el panel de filtros --}}
        <button type="button" class="env-btn env-btn-ghost open-filters d-lg-none w-100 justify-content-center mb-3">
            <i class="fas fa-sliders-h"></i> Filtros de búsqueda
        </button>

        <div class="env-split-layout">
            {{-- COLUMNA DE FILTROS --}}
            <div class="col-md-3 p-0 sidebar-wrapper" id="filter-panel">
                <div class="env-card sidebar-card">
                    <div class="env-card-head d-flex flex-column align-items-stretch">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="env-card-title mb-0">Búsqueda avanzada</h5>
                            <button type="button" class="env-btn-ghost btn btn-sm d-lg-none" id="close-filters" aria-label="Cerrar filtros">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <button type="button" class="env-btn env-btn-ghost mt-2 justify-content-center"
                            onclick="window.location='{{ route('reviewconsecutives.index') }}'" title="Limpiar filtros">
                            Limpiar filtros
                        </button>
                    </div>

                    <form id="filter-form" action="{{ route('reviewconsecutives.index') }}"
                        class="d-flex flex-column gap-3">
                        {{-- Entorno --}}
                        <div class="env-filter-group">
                            <label for="filter-environment" class="env-filter-label d-flex align-items-center gap-2">
                                <i class="fas fa-globe"></i> Entorno
                            </label>
                            <select id="filter-environment" name="environment" class="form-select form-select-sm">
                                <option value="">Todos</option>
                                @foreach ($environments as $environment)
                                    @if ($environment->id == 0)
                                        @continue
                                    @endif
                                    <option value="{{ $environment->id }}"
                                        {{ request('environment') == $environment->id ? 'selected' : '' }}>
                                        {{ $environment->entorno }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Profesional --}}
                        <div class="env-filter-group">
                            <label for="filter-profesional" class="env-filter-label d-flex align-items-center gap-2">
                                <i class="fas fa-user-tie"></i> Profesional
                            </label>
                            <select id="filter-profesional" name="profesional" class="form-select select-search">
                                <option value="">Buscar profesionales</option>
                                @foreach ($professionals as $prof)
                                    <option value="{{ $prof->id }}"
                                        {{ request('profesional') == $prof->id ? 'selected' : '' }}>
                                        {{ properNouns($prof->name, $prof->last_name) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Número de ficha --}}
                        <div class="env-filter-group">
                            <label for="filter-ficha" class="env-filter-label d-flex align-items-center gap-2">
                                <i class="fas fa-hashtag"></i> Número de Ficha
                            </label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text"><i class="fas fa-search text-muted"></i></span>
                                <input type="text" id="filter-ficha" name="ficha" value="{{ request('ficha') ?? '' }}"
                                    class="form-control" placeholder="Ej. 1010341102">
                            </div>
                        </div>

                        {{-- Estado --}}
                        <div class="env-filter-group">
                            <label for="filter-status" class="env-filter-label d-flex align-items-center gap-2">
                                <i class="far fa-circle"></i> Estado
                            </label>
                            <select id="filter-status" name="status" class="form-select form-select-sm">
                                <option value="">Todos</option>
                                <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Pendientes</option>
                                <option value="2" {{ request('status') == '2' ? 'selected' : '' }}>Sellados</option>
                                <option value="10" {{ request('status') == '10' ? 'selected' : '' }}>Rechazados</option>
                                <option value="3" {{ request('status') == '3' ? 'selected' : '' }}>Entregados a GESI</option>
                                <option value="14" {{ request('status') == '14' ? 'selected' : '' }}>Recibidos por entorno</option>
                                <option value="6" {{ request('status') == '6' ? 'selected' : '' }}>Entregado al profesional</option>
                            </select>
                        </div>

                        {{-- Mes y año intervención --}}
                        <div class="env-filter-group">
                            <label for="filter-month" class="env-filter-label d-flex align-items-center gap-2">
                                <i class="fas fa-calendar-alt"></i> Mes y año de intervención
                            </label>
                            <div class="d-flex gap-2">
                                <select id="filter-month" name="month_intervention" class="form-select form-select-sm">
                                    {!! getListMonthsOptions('month_intervention') !!}
                                </select>
                                <select name="year_intervention" class="form-select form-select-sm">
                                    {!! getYearOptions('year_intervention') !!}
                                </select>
                            </div>
                        </div>

                        {{-- Localidad --}}
                        <div class="env-filter-group">
                            <label for="filter-localidad" class="env-filter-label d-flex align-items-center gap-2">
                                <i class="fas fa-map-marker-alt"></i> Localidad
                            </label>
                            <select id="filter-localidad" name="localidad[]" multiple class="form-select form-select-sm">
                                @foreach ($localidades as $localidad)
                                    <option value="{{ $localidad->id }}"
                                        {{ is_array(request('localidad')) && in_array($localidad->id, request('localidad')) ? 'selected' : (request('localidad') == $localidad->id ? 'selected' : '') }}>
                                        {{ $localidad->name }} - {{ $localidad->identificador }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted small">Puedes seleccionar varias localidades con CTRL + click</small>
                        </div>

                        {{-- Botón de Aplicar Filtros (Necesario para el campo multiple) --}}
                        <button type="submit" class="env-btn env-btn-primary justify-content-center mt-3" id="apply-filters-button"
                            onclick="submitAndShowLoading(() => document.getElementById('filter-form').submit()); return false;">
                            Aplicar Filtros
                        </button>
                    </form>
                </div>
            </div>

            {{-- COLUMNA DE CONTENIDO (Fichas) --}}
            <div class="col-md-9 position-relative">

                {{-- HEADER DEL CONTENIDO: Título, contador y botones de acción masiva --}}
                <div class="sticky-top-custom env-card py-3 px-3 mb-3" style="z-index: 10;">
                    {{-- BARRA DE SELECCIÓN --}}
                    <div id="selected-bar"
                        class="d-none pb-3 rounded-3 bg-dark-selection shadow d-flex flex-wrap justify-content-between align-items-center gap-3">
                        <div class="d-flex align-items-center gap-3 flex-wrap">
                            <form id="selected-form" method="POST"
                                action="{{ route('reviewconsecutives.update-status') }}"
                                class="d-flex align-items-center gap-2">
                                @csrf
                                <input type="hidden" name="selected_ids" id="selected-ids">
                                <input type="hidden" name="status" id="input-action">

                                <div class="dropdown">
                                    <button class="env-btn env-btn-primary dropdown-toggle" type="button"
                                        data-bs-toggle="dropdown" disabled>
                                        Acciones masivas
                                    </button>

                                    <ul class="dropdown-menu dropdown-menu-dark" style="z-index: 100">
                                        @if (request('status') == 1 || request('status') == 10)
                                            <li>
                                                <a class="dropdown-item text-success" href="#" data-action="">
                                                    <i class="fas fa-check-circle me-2"></i>Aprobar
                                                </a>
                                            </li>
                                        @endif

                                        @if (request('status') == 1 || request('status') == 2)
                                            <li>
                                                <a class="dropdown-item text-danger" href="#" data-action="10">
                                                    <i class="fas fa-times-circle me-2"></i>Rechazar
                                                </a>
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                            </form>

                            <span id="selected-count" class="fw-bold text-accent d-none"></span>
                        </div>

                        {{-- ACCIONES --}}
                        <div class="d-flex align-items-center gap-2 flex-wrap">

                            {{-- EDICIÓN MASIVA --}}
                            @if (request('environment') && request('environment') != '' && request('status') != 3)
                                <button class="env-btn env-btn-ghost"
                                    id="edit-button" title="Selecciona hasta 20 fichas" disabled>
                                    <i class="fas fa-edit"></i>
                                    <span class="d-none d-md-inline">Edición masiva</span>
                                </button>
                            @endif

                            <button type="button" class="env-btn env-btn-ghost" onclick="clearSelection()">
                                <i class="fas fa-times me-1"></i> Limpiar selección
                            </button>

                            {{-- RECARGAR --}}
                            <button type="button" title="Actualizar"
                                onclick="submitAndShowLoading(() => {
                                    const activeSection = localStorage.getItem('activeSection') || 'section-consecutivos';
                                    const url = new URL(window.location.href);
                                    url.searchParams.set('section', activeSection);
                                    window.location.href = url.toString();
                                })"
                                class="env-btn env-btn-ghost d-flex align-items-center justify-content-center">
                                <i class="fas fa-sync"></i>
                            </button>
                        </div>
                    </div>

                    {{-- HEADER SUPERIOR --}}
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">

                        {{-- CONTADOR --}}
                        <div class="d-flex flex-column">
                            <span class="env-badge env-badge-red">Mostrando {{ count($files) ?? 0 }} resultados</span>
                        </div>
                    </div>
                </div>


               

                {{-- SPINNER DE CARGA --}}
                <div id="loading-overlay" class="loading-overlay show">
                    <div class="spinner-border text-accent" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                </div>

                {{-- CONTENEDOR DE FICHAS (RESULTADOS) --}}
                <div id="fichas-container" class="d-flex flex-column gap-2 cursor-pointer" style="max-height: 600px; overflow-y: auto;">
                    @if (count($files) > 0)
                        @php
                            $showWatermark = request('environment') != '' && request('status') == 2;
                        @endphp
                        @foreach ($files as $file)
                            <div class="ficha-item selectable env-card cursor-pointer p-3"
                                data-id="{{ $file->id }}" data-file-number="{{ $file->file_number }}">

                                @if ($showWatermark)
                                    <div class="ficha-watermark" id="watermark-{{ $file->id }}"></div>
                                @endif

                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="d-flex flex-column">
                                        <div class="d-flex align-items-center gap-3 mb-1 flex-wrap">
                                            {{-- ID Principal --}}
                                            <h5 class="mb-0 fw-bold">#{{ $file->file_number }}</h5>
                                            {{-- Estado como Badge (Tag) --}}
                                            <span class="badge {{ getStatusFileClass($file->status) }} fw-bold">
                                                {{ getStatusNameFile($file->status) }}
                                            </span>

                                            <div class="d-flex text-md-end w-100 w-md-auto mt-2 mt-md-0">
                                                @foreach ($file->users as $userProfessional)
                                                    <div class="small text-warning fw-medium mr-2">
                                                        <i class="fas fa-user-circle me-1"></i>
                                                        {{ propernouns(nameAndLastName($userProfessional->name, $userProfessional->last_name)) }}
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>

                                        {{-- Tipo de Intervención (Metadato Principal) --}}
                                        <p class="mb-0 text-muted small">
                                            {{ $file->environment_file->entorno }} -
                                            {{ properNouns($file->bases->name) ?? '' }}
                                            @if ($file->interventionType)
                                                <span class="text-muted fst-italic">-
                                                    {{ $file->interventionType->name }}</span>
                                            @endif
                                        </p>
                                    </div>

                                </div>

                                {{-- Fechas y Ubicación (Metadatos Secundarios) --}}
                                <div
                                    class="d-flex flex-wrap justify-content-between align-items-center mt-1 pt-2 border-top">
                                    <div class="d-flex gap-2 small text-muted flex-wrap">
                                        @if ($file->fecha_intervencion)
                                            <div>
                                                <i class="far fa-calendar-alt me-1 text-info"></i>
                                                Intervención:
                                                {{ \Carbon\Carbon::parse($file->fecha_intervencion)->translatedFormat('j \\d\\e F \\d\\e Y') }}
                                            </div>
                                        @endif
                                        @if ($file->required_date)
                                            <div>
                                                <i class="far fa-calendar-alt me-1 text-info"></i>
                                                Solicitado:
                                                {{ \Carbon\Carbon::parse($file->required_date)->translatedFormat('j \\d\\e F \\d\\e Y') }}
                                            </div>
                                        @endif
                                    </div>

                                    <div class="text-end small text-muted">
                                        <i class="fas fa-map-marker-alt me-1 text-danger"></i>
                                        {{ $file->localidad->name ?? 'N/A' }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="env-empty-state">
                            <i class="fas fa-info-circle"></i>
                            <span class="env-empty-title">Sin resultados</span>
                            <p class="env-empty-text">Asegúrate de tener filtros activos o intenta con otros criterios.</p>
                        </div>
                    @endif

                    {{-- PAGINACIÓN --}}
                    @if (count($files) > 0)
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mt-4">
                            <div class="small text-muted">
                                Página {{ $files->currentPage() }} de {{ $files->lastPage() }}
                            </div>

                            <div class="d-flex align-items-center gap-2">
                                <label for="per-page" class="small mb-0 text-muted">Mostrar</label>
                                <select id="per-page" class="form-select form-select-sm" style="width: auto;">
                                    <option value="10" {{ $files->perPage() == 10 ? 'selected' : '' }}>10</option>
                                    <option value="25" {{ $files->perPage() == 25 ? 'selected' : '' }}>25</option>
                                    <option value="50" {{ $files->perPage() == 50 ? 'selected' : '' }}>50</option>
                                    <option value="100" {{ $files->perPage() == 100 ? 'selected' : '' }}>100</option>
                                    <option value="500" {{ $files->perPage() == 500 ? 'selected' : '' }}>500</option>
                                    <option value="1000" {{ $files->perPage() == 1000 ? 'selected' : '' }}>1000</option>
                                </select>
                            </div>

                            <div>
                                {{ $files->appends(request()->query())->onEachSide(1)->links('pagination::bootstrap-5') }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    {{-- OVERLAY para el panel de filtros en mobile --}}
    <div id="overlay" class="env-filter-overlay"></div>

    <section id="section-sisco" class="content-section d-none mt-2">
        <div class="env-split-layout">
            {{-- Contenido de Sisco --}}
            <div class="col-md-3 env-card">
                <div class="env-card-head">
                    <h5 class="env-card-title mb-0">Crear consecutivos SISCO</h5>
                </div>

                <form id="create-consecutive-form" action="{{ route('consecutivesSisco.store') }}" method="POST"
                    class="d-flex flex-column gap-3">
                    @csrf

                    <div class="env-filter-group">
                        <label for="consecutive-fecha" class="env-filter-label d-flex align-items-center gap-2">
                            <i class="fas fa-calendar-alt"></i> Fecha de recepción
                        </label>
                        <input type="date" id="consecutive-fecha" name="consecutive_fecha" class="form-control" required>
                    </div>

                    <div class="env-filter-group">
                        <label for="consecutive-environment" class="env-filter-label d-flex align-items-center gap-2">
                            <i class="fas fa-globe"></i> Entorno
                        </label>
                        <select id="consecutive-environment" name="consecutive_environment" class="form-select form-select-sm" required>
                            <option value="">Seleccione un entorno</option>
                            @foreach ($environments as $environment)
                                @if ($environment->id == 0)
                                    @continue
                                @endif
                                <option value="{{ $environment->id }}">{{ $environment->entorno }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="env-filter-group">
                        <label for="consecutive-format" class="env-filter-label d-flex align-items-center gap-2">
                            <i class="fas fa-file-alt"></i> Formato
                        </label>
                        <select id="consecutive-format" name="consecutive_format" class="form-select form-select-sm" required>
                            <option value="">Seleccione un formato</option>
                        </select>
                    </div>

                    <div class="env-filter-group">
                        <label for="consecutive-profesional" class="env-filter-label d-flex align-items-center gap-2">
                            <i class="fas fa-user-tie"></i> Profesional
                        </label>
                        <select id="consecutive-profesional" name="consecutive_profesional" class="form-select form-select-sm" required>
                            <option value="">Seleccione un profesional</option>
                            @foreach ($professionals as $prof)
                                <option value="{{ $prof->id }}">{{ properNouns($prof->name, $prof->last_name) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="env-filter-group">
                        <label for="consecutive-localidad" class="env-filter-label d-flex align-items-center gap-2">
                            <i class="fas fa-map-marker-alt"></i> Localidad
                        </label>
                        <select id="consecutive-localidad" name="consecutive_localidad" class="form-select form-select-sm">
                            <option value="">Seleccione una localidad</option>
                            @foreach ($localidades as $localidad)
                                <option value="{{ $localidad->id }}">{{ $localidad->name }}</option>
                            @endforeach
                        </select>
                    </div>


                    <div class="env-filter-group">
                        <label for="consecutive-quantity" class="env-filter-label d-flex align-items-center gap-2">
                            <i class="fas fa-list-ol"></i> Cantidad de consecutivos
                        </label>
                        <input type="number" id="consecutive-quantity" name="consecutive_quantity" value="1"
                            class="form-control" min="1" max="100">
                    </div>

                    <button type="submit" class="env-btn env-btn-primary justify-content-center mt-3">
                        Crear consecutivos
                    </button>

                </form>
            </div>

            <div class="col-md-9">
                {{-- Contenedor con Scroll --}}
                <div class="env-card p-0">
                    <div class="env-table-wrap" style="max-height: 600px; overflow-y: auto;">
                        <table class="env-table m-0">
                            <thead class="sticky-top">
                                <tr>
                                    <th>Consecutivo</th>
                                    <th>Formato</th>
                                    <th>Fecha creación</th>
                                    <th>Profesional</th>
                                    <th>Localidad</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($consecutivos as $c)
                                    <tr>
                                        <td>{{ $c->consecutivo_sisco }}</td>
                                        <td>{{ $c->format->name ?? 'N/A' }}</td>
                                        <td>{{ $c->created_at->format('d/m/Y H:i') }}</td>
                                        <td>{{ $c->users->map(fn($u) => $u->name)->join(', ') }}</td>
                                        <td>{{ $c->localidad->name ?? 'N/A' }}</td>
                                        <td>{!! $c->status_badge !!}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">No hay registros</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Paginación FUERA del scroll --}}
                    <div class="env-card-foot">
                        <div class="d-flex justify-content-between align-items-center flex-wrap">
                            <small class="text-muted">
                                Página {{ $consecutivos->currentPage() }} de {{ $consecutivos->lastPage() }}
                            </small>
                            <div class="custom-pagination">
                                {{ $consecutivos->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
        document.getElementById('consecutive-environment').addEventListener('change', function() {
            let environmentId = this.value;

            let formatSelect = document.getElementById('consecutive-format');
            formatSelect.innerHTML = '<option value="">Cargando...</option>';

            if (environmentId) {
                fetch(`/formats/by-environment/${environmentId}`)
                    .then(response => response.json())
                    .then(data => {
                        formatSelect.innerHTML = '<option value="">Seleccione un formato</option>';

                        data.forEach(format => {
                            let option = document.createElement('option');
                            option.value = format.id;
                            option.text = format.name; // asegúrate que este campo exista
                            formatSelect.appendChild(option);
                        });
                    })
                    .catch(error => {
                        console.error(error);
                        formatSelect.innerHTML = '<option value="">Error al cargar</option>';
                    });
            } else {
                formatSelect.innerHTML = '<option value="">Seleccione un formato</option>';
            }
        });
    </script>

    {{-- ************************************************************ --}}
    {{-- SCRIPT PARA MANEJO DE ESTADOS Y FILTROS (AJUSTADO) --}}
    {{-- ************************************************************ --}}
    <script>
        // Variables globales para los elementos clave (Asegúrate de tener un ID para selected-bar)
        const filterForm = document.getElementById('filter-form');
        const loadingOverlay = document.getElementById('loading-overlay');
        const fichasContainer = document.getElementById('fichas-container');
        const perPageSelect = document.getElementById('per-page');
        const selectedBar = document.getElementById('selected-bar'); // Nuevo elemento

        // Función para mostrar el spinner y preparar el envío/navegación
        const submitAndShowLoading = (callback) => {
            if (loadingOverlay && fichasContainer) {
                fichasContainer.style.opacity = '0';
                loadingOverlay.classList.add('show');
                loadingOverlay.style.display = 'flex';
            }

            setTimeout(() => {
                if (callback) {
                    callback();
                }
            }, 50);
        };

        // Función para limpiar la selección masiva (ya existente en el código original, pero se asegura la visibilidad)
        function clearSelection() {
            document.querySelectorAll('.ficha-item.selected').forEach(item => {
                item.classList.remove('selected');
            });
            updateSelectedCount(); // Llama a la función que actualiza el contador y visibilidad de la barra
        }

        // Función para actualizar el contador de selección y la visibilidad de la barra de acciones
        function updateSelectedCount() {
            const selectedItems = document.querySelectorAll('.ficha-item.selected');
            const count = selectedItems.length;
            const selectedIds = Array.from(selectedItems).map(item => item.dataset.id);

            // Actualizar el número y la visibilidad de la barra
            if (selectedBar) {
                document.getElementById('selected-count').textContent = `${count} seleccionada(s)`;
                selectedBar.classList.toggle('d-none', count === 0);
            }

            // Actualizar el input oculto para el formulario de acción
            const selectedIdsInput = document.getElementById('selected-ids');
            if (selectedIdsInput) {
                selectedIdsInput.value = selectedIds.join(',');
            }

            // Habilitar/Deshabilitar botones de acción masiva y edición masiva
            const isEnabled = count > 0;
            document.querySelectorAll('#selected-bar button').forEach(btn => btn.disabled = !isEnabled);

            const editButton = document.getElementById('edit-button');
            if (editButton) {
                editButton.disabled = count === 0 || count > 20; // Lógica del límite de 20
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Inicialización: Ocultar el loader y mostrar resultados después de un pequeño tiempo.
            if (loadingOverlay && fichasContainer) {
                const MIN_DISPLAY_TIME = 300;
                setTimeout(() => {
                    loadingOverlay.classList.remove('show');
                    // Ocultar físicamente el loader después de la transición (ajustar si es necesario)
                    setTimeout(() => {
                        loadingOverlay.style.display = 'none';
                    }, 300);

                    fichasContainer.style.opacity = '1';
                    fichasContainer.style.display = 'flex';
                }, MIN_DISPLAY_TIME);
            }

            // Lógica de selección de fichas (EXISTENTE, pero adaptada al nuevo updateSelectedCount)
            document.querySelectorAll('.ficha-item').forEach(item => {
                item.addEventListener('click', function(e) {
                    // Lógica de Shift + Click (asumiendo que ya tienes una variable para el último seleccionado)
                    const isShift = e.shiftKey;

                    // Toggle de la clase selected
                    this.classList.toggle('selected');

                    // Actualizar el estado de la barra de selección
                    updateSelectedCount();
                });
            });

            // Eventos de Filtro: Envío automático para selects y manual para el botón/inputs.
            if (filterForm) {
                const filterElements = filterForm.querySelectorAll('select, input[type="text"]');

                filterElements.forEach(element => {
                    if (element.tagName === 'SELECT' && !element.multiple) {
                        element.addEventListener('change', function() {
                            const activeSection = localStorage.getItem('activeSection') ||
                                'section-consecutivos';
                            const url = new URL(window.location.href);
                            url.searchParams.set('section', activeSection);
                            window.history.replaceState({}, '', url);
                            submitAndShowLoading(() => filterForm.submit());
                        });
                    }
                });

                // Para el input de ficha
                document.getElementById('filter-ficha').addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        const activeSection = localStorage.getItem('activeSection') ||
                            'section-consecutivos';
                        const url = new URL(window.location.href);
                        url.searchParams.set('section', activeSection);
                        window.history.replaceState({}, '', url);
                        submitAndShowLoading(() => filterForm.submit());
                    }
                });
            }

            // **Lógica para el cambio de paginación por página**
            if (perPageSelect) {
                perPageSelect.addEventListener('change', function() {
                    const selectElement = this;
                    submitAndShowLoading(() => {
                        const url = new URL(window.location.href);
                        url.searchParams.set('per_page', selectElement.value);
                        url.searchParams.set('page', 1);
                        window.location.href = url.toString();
                    });
                });
            }

            // Lógica para los botones de acción masiva
            document.querySelectorAll('#selected-bar a[data-action]').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const action = this.dataset.action;
                    document.getElementById('input-action').value = action;

                    // Confirma y envía
                    if (confirm(
                            `¿Estás seguro de que deseas realizar esta acción (${this.textContent.trim()}) en ${document.querySelectorAll('.ficha-item.selected').length} fichas?`
                        )) {
                        submitAndShowLoading(() => document.getElementById('selected-form')
                            .submit());
                    }
                });
            });
        });
    </script>

    <script>
        document.getElementById('create-consecutive-form').addEventListener('submit', function(e) {
            const button = this.querySelector('button[type="submit"]');

            // Evitar múltiples envíos
            if (button.disabled) {
                e.preventDefault();
                return;
            }

            button.disabled = true;
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creando...';
        });
        document.addEventListener('DOMContentLoaded', function() {
            const sectionButtons = document.querySelectorAll('.btn-section-toggle');
            const sections = document.querySelectorAll('.content-section');

            // Función para activar una sección específica
            function activateSection(targetId) {
                // 1. Manejar visualización de botones
                sectionButtons.forEach(b => {
                    b.classList.remove('active', 'env-btn-primary');
                    b.classList.add('env-btn-ghost');
                });
                const activeButton = document.querySelector(`[data-target="${targetId}"]`);
                if (activeButton) {
                    activeButton.classList.add('active', 'env-btn-primary');
                    activeButton.classList.remove('env-btn-ghost');
                }

                // 2. Manejar visibilidad de secciones
                sections.forEach(section => {
                    if (section.id === targetId) {
                        section.classList.remove('d-none');
                        section.style.opacity = '0';
                        setTimeout(() => section.style.opacity = '1', 50);
                    } else {
                        section.classList.add('d-none');
                    }
                });

                // 3. Guardar en localStorage
                localStorage.setItem('activeSection', targetId);

                // 4. Opcional: Actualizar URL (sin recargar)
                const url = new URL(window.location);
                url.searchParams.set('section', targetId);
                window.history.replaceState({}, '', url);
            }

            // Event listeners para los botones
            sectionButtons.forEach(btn => {
                btn.addEventListener('click', function() {
                    const targetId = this.getAttribute('data-target');
                    activateSection(targetId);
                });
            });

            // **RESTAURAR SECCIÓN ACTIVA AL CARGAR LA PÁGINA**
            const savedSection = localStorage.getItem('activeSection');
            const urlSection = new URLSearchParams(window.location.search).get('section');
            const sectionToActivate = urlSection || savedSection ||
                'section-consecutivos'; // Por defecto consecutivos

            // Activar la sección guardada
            activateSection(sectionToActivate);
        });
    </script>

    <script>
        new TomSelect("#consecutive-profesional", {
            create: false,
            sortField: {
                field: "text",
                direction: "asc"
            }
        });
    </script>
@endsection
