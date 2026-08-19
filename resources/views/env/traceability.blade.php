@extends(Auth::user()->is_admin ? 'layouts.env.navigation' : 'layouts.members.nav')



@section('main')

    <div class="env-page-header">
        <div>
            <span class="env-page-tag">Gestión documental</span>
            <h1 class="env-page-title">Trazabilidad</h1>
        </div>
    </div>

    <div class="env-card mb-4">
        <div class="env-card-head">
            <h2 class="env-card-title"><i class="fas fa-comment-dots"></i> Mis observaciones de {{ \Carbon\Carbon::now()->translatedFormat('F Y') }}</h2>
            @if ($myObservationsCount > 0)
                <span class="env-badge env-badge-red">{{ $myObservationsCount }} en total</span>
            @endif
        </div>

        @if ($myObservationsCount > 0)
            <p class="env-card-note">Observaciones que te han registrado este mes en tus fichas, agrupadas por formato.</p>

            <div class="obs-format-list mb-3" id="obsFormatChips">
                <button type="button" class="env-chip env-chip-high obs-format-chip is-active" data-format-filter="all">
                    Todos <b>{{ $myObservationsCount }}</b>
                </button>
                @foreach ($myObservationsByFormat as $formatRow)
                    <button type="button" class="env-chip env-chip-high obs-format-chip" data-format-filter="{{ $formatRow['id'] }}">
                        {{ $formatRow['name'] }} <b>{{ $formatRow['total'] }}</b>
                    </button>
                @endforeach
            </div>

            <div class="obs-list" id="obsList">
                @foreach ($myObservations as $index => $observation)
                    @php $obsFile = $observation->receptions; @endphp
                    <a class="obs-item {{ $index >= 10 ? 'is-extra' : '' }}"
                        data-format-id="{{ $obsFile->format_id ?? '' }}"
                        href="{{ route('env.traceability', ['file_number_search' => $obsFile->file_number ?? '', 'format_search' => $obsFile->format_id ?? '']) }}"
                        title="Ver ficha">
                        <div class="obs-item-head">
                            <span class="obs-item-format">{{ $obsFile->bases->name ?? 'Formato' }}</span>
                            <span class="obs-item-ficha">Ficha {{ $obsFile->file_number ?? '—' }}</span>
                        </div>
                        <p class="obs-item-text">{{ $observation->observations }}</p>
                        <div class="d-flex justify-content-between align-items-baseline gap-2">
                            <span class="obs-item-date">
                                @if ($observation->user)
                                    Revisado por {{ properNouns(nameAndLastName($observation->user->name, $observation->user->last_name)) }}
                                @endif
                            </span>
                            <span class="obs-item-date">{{ \Carbon\Carbon::parse($observation->created_at)->translatedFormat('j \d\e F \d\e Y') }}</span>
                        </div>
                    </a>
                @endforeach
            </div>

            <script>
                (function () {
                    const chips = document.querySelectorAll('#obsFormatChips [data-format-filter]');
                    const items = document.querySelectorAll('#obsList .obs-item');

                    chips.forEach(chip => chip.addEventListener('click', () => {
                        chips.forEach(c => c.classList.remove('is-active'));
                        chip.classList.add('is-active');

                        const formatId = chip.dataset.formatFilter;

                        items.forEach(item => {
                            if (formatId === 'all') {
                                item.style.display = item.classList.contains('is-extra') ? 'none' : '';
                            } else {
                                item.style.display = item.dataset.formatId === formatId ? '' : 'none';
                            }
                        });
                    }));
                })();
            </script>
        @else
            <div class="env-empty-state py-3">
                <i class="fas fa-circle-check"></i>
                <span class="env-empty-title">Sin observaciones</span>
                <p class="env-empty-text">No tienes observaciones registradas en tus fichas este mes.</p>
            </div>
        @endif
    </div>

    <div class="env-filter-bar">
        <form action="{{ route('env.traceability') }}" method="GET" class="d-flex flex-wrap align-items-end gap-3 flex-grow-1">
            <div class="env-filter-group">
                <label for="file_number_search" class="env-filter-label">No. ficha</label>
                <div class="input-group input-group-sm">
                    <input type="number" class="form-control" id="file_number_search" name="file_number_search"
                        value="{{ request('file_number_search') }}">
                    <button type="submit" class="env-btn env-btn-primary btn-loader">
                        <i class="fa-solid fa-magnifying-glass fa-sm"></i>
                    </button>
                </div>
            </div>

            @if (request('file_number_search') !== null)
                <span class="env-badge env-badge-red">{{ count($files) }} resultados</span>
            @endif
        </form>

        <div class="d-flex flex-row gap-2 flex-wrap">
            <div class="dropdown">
                <a href="#" class="env-btn env-btn-primary" data-bs-toggle="dropdown"
                    aria-expanded="false">
                    <i class="fas fa-download fa-sm"></i> Generar reporte
                </a>

                {{-- Contenido del popover --}}
                <ul class="dropdown-menu p-2">
                    <form action="{{ route('traceability.export') }}" method="POST">
                        @csrf
                        @method('POST')
                        <div class="env-filter-group mb-2">
                            <span class="env-filter-label">Desde</span>
                            <input type="date" class="form-control form-control-sm" name="startDate">
                        </div>
                        <div class="env-filter-group mb-2">
                            <span class="env-filter-label">Hasta</span>
                            <input type="date" class="form-control form-control-sm" name="endDate">
                        </div>
                        <div class="env-filter-group mb-2">
                            <span class="env-filter-label">Entorno</span>
                            <select class="form-select form-select-sm" name="environment_id">
                                <option value="">Todos</option>
                                @foreach ($environments as $environment)
                                    @if ($environment->id != 0)
                                        <option value="{{ $environment->id }}">{{ $environment->entorno }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="env-btn env-btn-primary w-100 justify-content-center">Descargar</button>
                    </form>
                </ul>
            </div>

            <div class="dropdown">
                <a href="#" class="env-btn env-btn-ghost" id="export-button" title="Exportar herramienta de control" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-file-excel"></i> Exportar HC
                </a>

                <ul class="dropdown-menu p-2">
                    <form action="{{ route('reviewconsecutives.export') }}" method="POST">
                        @csrf
                        @method('POST')
                        <small class="text-muted fst-italic">
                            <i class="fas fa-info-circle me-1"></i>Filtro aplicable para fecha de intervención
                        </small>
                        <div class="env-filter-group mb-2 mt-2">
                            <span class="env-filter-label">Desde</span>
                            <input type="date" class="form-control form-control-sm" name="startDate">
                        </div>
                        <div class="env-filter-group mb-2">
                            <span class="env-filter-label">Hasta</span>
                            <input type="date" class="form-control form-control-sm" name="endDate">
                        </div>
                        <div class="env-filter-group mb-2">
                            <span class="env-filter-label">Entorno</span>
                            <select class="form-select form-select-sm" name="environment">
                                <option value="">Todos</option>
                                @foreach ($environments as $environment)
                                    @if ($environment->id != 0)
                                        <option value="{{ $environment->id }}">{{ $environment->entorno }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="env-btn env-btn-primary w-100 justify-content-center">Descargar</button>
                    </form>
                </ul>
            </div>
        </div>
    </div>

    <div class="env-card p-0">
        <div class="env-table-wrap scrollable-container container-traceability" style="max-height: 75vh;">
            @if (count($files) > 0)
                @foreach ($files as $file)
                    @php $hasProductivity = isset($file->productivity); @endphp

                    <div class="env-card trace-card mb-2" data-id="{{ $file->id }}">
                        <div class="trace-card-top">
                            {{-- Numero ficha --}}
                            <div class="trace-card-info">
                                <div class="ficha-number">
                                    <i class="fas fa-hashtag me-1 text-danger"></i>
                                    {{ $file->file_number }}
                                    <span class="badge {{ getStatusFileClass($file->status) }} ficha-status">
                                        {{ getStatusNameFile($file->status) }}
                                    </span>
                                </div>

                                <div class="text-muted small">
                                    {{ $file->environment_file->entorno }} -
                                    {{ properNouns($file->bases->name) ?? '' }}
                                    <span class="text-muted small">{{ $file->interventionType ? '- ' . $file->interventionType->name : '' }}</span>
                                </div>

                                @if ($file->delivered_by)
                                    <div>
                                        <span>
                                            <i class="fas fa-user-tie me-1 text-danger"></i>
                                            {{ $file->delivered_by ? properNouns(nameAndLastName($file->user->name, $file->user->last_name)) : '' }}
                                        </span>
                                    </div>
                                @endif

                                <span class="text-muted small">
                                    @if ($file->localidad)
                                        <i class="fas fa-map-marker-alt me-1 text-danger"></i>
                                        {{ $file->localidad->name ?? 'N/A' }}
                                    @endif
                                </span>
                            </div>

                            {{-- Número de paquete --}}
                            <div class="trace-card-actions">
                                @if (!empty($file->packages->num_package))
                                    <a class="env-btn env-btn-primary py-1 px-2"
                                        href="{{ route('packages.index', ['environment' => $file->environment_file->entorno, 'search_package' => $file->packages->num_package]) }}">
                                        <span class="fs-6 mb-0">{{ separateNumberPackage($file->packages->num_package) }}</span>
                                    </a>
                                @endif

                                @if ($hasProductivity)
                                    <div class="d-flex flex-row rounded-2 gap-1">
                                        <div>
                                            <a href="{{ route('productivity.edit', $file->productivity->id) }}"
                                                class="env-btn env-btn-ghost py-1" title="Editar ficha"><i
                                                    class="fa-regular fa-pen-to-square"></i></a>
                                        </div>
                                        @if ($file->productivity->deleted_at)
                                            <form
                                                action="{{ route('productivity.restore', $file->productivity->id) }}"
                                                method="POST"
                                                onsubmit="return confirm('Desea reaturar la productividad a {{ nameAndLastName($file->productivity->user->name, $file->productivity->user->last_name) }}')">
                                                @csrf
                                                <button type="submit" class="env-btn env-btn-ghost py-1"
                                                    title="Restaurar productividad"><i
                                                        class="fa-solid fa-arrow-right-arrow-left"></i></button>
                                            </form>
                                        @else
                                            <form
                                                action="{{ route('productivity.delete', $file->productivity->id) }}"
                                                method="POST"
                                                onsubmit="return confirm('Desea eliminar la productividad a {{ nameAndLastName($file->productivity->user->name, $file->productivity->user->last_name) }}')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="env-btn env-btn-ghost py-1"
                                                    title="Eliminar productividad"><i
                                                        class="fa-solid fa-trash"></i></button>
                                            </form>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- Fechas --}}
                        <div class="trace-card-dates">
                            @if ($file->fecha_intervencion)
                                <div class="text-muted small">
                                    <i class="far fa-calendar-alt me-1 text-danger"></i>
                                    <strong>Fecha intervención</strong>
                                    {{ \Carbon\Carbon::parse($file->fecha_intervencion)->translatedFormat('j \\d\\e F \\d\\e Y') }}
                                </div>
                            @endif
                            @if ($file->received_by)
                                <div class="text-muted small">
                                    <i class="far fa-calendar-alt me-1 text-danger"></i>
                                    <strong>Entregada por
                                        {{ properNouns(nameAndLastName($file->user->name, $file->user->last_name)) }}</strong>
                                        el
                                    {{ \Carbon\Carbon::parse($file->created_at)->translatedFormat('j \d\e F \d\e\l Y\, H:i') }}
                                </div>
                            @endif
                            @if ($hasProductivity)
                                <div class="text-muted small">
                                    <i class="far fa-calendar-alt me-1 text-danger"></i>
                                    <strong>Digitada por {{ properNouns(nameAndLastName($file->productivity->user->name, $file->productivity->user->last_name)) }}</strong>
                                    el {{ \Carbon\Carbon::parse($file->productivity->created_at)->translatedFormat('j \d\e F \d\e\l Y\, H:i') }}
                                </div>
                            @endif
                            @if ($file->received_by_environment)
                                <div class="text-muted small">
                                    <i class="far fa-calendar-alt me-1 text-danger"></i>
                                    <b>Devuelta por {{ properNouns(nameAndLastName($file->user_return->name, $file->user_return->last_name)) }} </b> el
                                    {{ \Carbon\Carbon::parse($file->return_date)->translatedFormat('j \d\e F \d\e\l Y\, H:i') }}
                                </div>
                            @endif
                        </div>

                        @if ($hasProductivity && $file->productivity->observations)
                            <hr class="my-2">
                            <div class="text-muted px-2 py-1 small mb-0" role="alert">
                                {{ $file->productivity->observations }}
                            </div>
                        @endif

                        @if (count($file->status_histories) > 0)
                            <button class="env-btn env-btn-ghost mt-2" type="button"
                                data-bs-toggle="collapse"
                                data-bs-target="#collapseDetails{{ $file->id }}"
                                aria-expanded="false"
                                aria-controls="collapseDetails{{ $file->id }}">
                                Ver historial de estados <i class="fas fa-chevron-down ms-1"></i>
                            </button>

                            <div id="collapseDetails{{ $file->id }}" class="collapse">
                                <div class="row g-3 mt-2">
                                    @foreach ($file->status_histories as $history)
                                        <div class="col-12 col-md-6 col-lg-3">
                                            <div class="env-card history-card p-2 h-100">
                                                <div class="mb-2">
                                                    <span class="badge bg-transparent {{ getStatusFileClass($history->new_status) }} me-1">
                                                        {{ getStatusNameFile($history->new_status) }}
                                                    </span>
                                                </div>

                                                <p class="mb-1 small">
                                                    <span class="text-muted">Cambiado de</span>
                                                    <span class="fw-semibold fst-italic">
                                                        {{ getStatusNameFile($history->previous_status) }}
                                                    </span>
                                                    <span class="text-muted">a</span>
                                                    <span class="fw-semibold fst-italic">
                                                        {{ getStatusNameFile($history->new_status) }}
                                                    </span>
                                                    <span class="text-muted">por</span>
                                                    <span class="text-muted">
                                                        {{ properNouns(nameAndLastName($history->user->name, $history->user->last_name)) }}
                                                    </span>
                                                </p>

                                                <small class="text-muted d-block mt-2">
                                                    <i class="far fa-clock me-1"></i>
                                                    {{ \Carbon\Carbon::parse($history->created_at)->format('d/m/Y H:i') }}
                                                </small>

                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach
            @else
                <div class="env-empty-state">
                    <i class="fas fa-info-circle"></i>
                    <span class="env-empty-title">Sin resultados</span>
                    <p class="env-empty-text">Intenta con otro número de ficha.</p>
                </div>
            @endif
        </div>
    </div>
@endsection
