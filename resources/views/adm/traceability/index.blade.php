@extends('layouts.adm.navigation')

@section('consecutivos_style')
    <link href="{{ asset('css/adm/traceability.css') }}" rel="stylesheet">
@endsection

@section('main')

    <div class="card shadow h-100 pt-0 bg-transparent border-0">
        <div class="card-header bg-dark d-flex justify-content-between">
            <form action="{{ route('adm.traceability') }}" method="GET">
                <div class="d-flex justify-content-start">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text" id="inputGroup-sizing-sm">No. ficha</span>
                        <input type="number" class="form-control" id="file_number_search" name="file_number_search"
                            value="{{ request('file_number_search') }}">
                    </div>
                    <div class="d-flex align-content-center">
                        <button type="submit" class="btn btn-primary btn-sm btn-loader">
                            <i class="fa-solid fa-magnifying-glass fa-sm" style="color: #ffffff;"></i>
                        </button>
                    </div>
                    @if (request('file_number_search') !== null)
                        <span class="badge text-bg-dark ms-3 d-flex flex-center">{{ count($files) }} resultados</span>
                    @endif
                </div>
            </form>

            <div class="d-flex flex-row gap-2">
                <div class="dropdown">
                    <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        <i class="fas fa-download fa-sm text-white-50"></i> Generar reporte
                    </a>

                    <!-- Contenido del popover -->
                    <ul class="dropdown-menu p-2 bg-dark">
                        <form action="{{ route('traceability.export') }}" method="POST">
                            @csrf
                            @method('POST')
                            <div class="mb-3">
                                <span class="border-0 bg-transparent">Desde</span>
                                <input type="date" class="form-control form-control-sm" name="startDate">
                            </div>
                            <div class="mb-3">
                                <span class="border-0 bg-transparent">Hasta</span>
                                <input type="date" class="form-control form-control-sm" name="endDate">
                            </div>
                            <div class="mb-3">
                                <span class="border-0 bg-transparent">Entorno</span>
                                <select class="form-select form-select-sm" name="environment_id">
                                    <option value="">Todos</option>
                                    @foreach ($environments as $environment)
                                        @if ($environment->id != 0)
                                            <option value="{{ $environment->id }}">{{ $environment->entorno }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="btn btn-sm btn-success w-100">Descargar</button>
                        </form>
                    </ul>
                </div>

                <div class="dropdown">
                    <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-outline-success" id="export-button" title="Exportar herramienta de control" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-file-excel"></i> Exportar HC
                    </a>
                    
                    <ul class="dropdown-menu p-2 bg-dark">
                        <form action="{{ route('reviewconsecutives.export') }}" method="POST">
                            @csrf
                            @method('POST')
                            <small class="text-muted fst-italic">
                                <i class="fas fa-info-circle me-1"></i>Filtro aplicable para fecha de intervención
                            </small>
                            <div class="mb-3">
                                <span class="border-0 bg-transparent">Desde</span>
                                <input type="date" class="form-control form-control-sm" name="startDate">
                            </div>
                            <div class="mb-3">
                                <span class="border-0 bg-transparent">Hasta</span>
                                <input type="date" class="form-control form-control-sm" name="endDate">
                            </div>
                            <div class="mb-3">
                                <span class="border-0 bg-transparent">Entorno</span>
                                <select class="form-select form-select-sm" name="environment">
                                    <option value="">Todos</option>
                                    @foreach ($environments as $environment)
                                        @if ($environment->id != 0)
                                            <option value="{{ $environment->id }}">{{ $environment->entorno }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="btn btn-sm btn-success w-100">Descargar</button>
                        </form>
                    </ul>
                </div>
            </div>
        </div>

        <div class="card-body d-grid row-gap-2 px-0 scrollable-container container-traceability">
            @if (count($files) > 0)
                @foreach ($files as $file)
                    @php $hasProductivity = isset($file->productivity); @endphp

                    <div class="ficha-item px-2 py-4" data-id="{{ $file->id }}">
                        <div class="d-flex align-items-center mx-0">
                            {{-- Numero ficha --}}
                            <div class="d-flex col-md-6 flex-row justify-content-between ps-3">
                                <div class="flex-column ">
                                    <div class="ficha-number">
                                        <i class="fas fa-hashtag me-1 text-danger"></i>
                                        {{ $file->file_number }}
                                        <span class="badge {{ getStatusFileClass($file->status) }} ficha-status">
                                            {{ getStatusNameFile($file->status) }}
                                        </span>
                                    </div>

                                    <div class="text-muted">
                                        {{ $file->environment_file->entorno }} -
                                        {{ properNouns($file->bases->name) ?? '' }}
                                        <span
                                            class="text-muted small">{{ $file->interventionType ? '- ' . $file->interventionType->name : '' }}</span>
                                    </div>

                                    @if ($file->delivered_by)
                                        <div>
                                            <span class="text-light">
                                                <i class="fas fa-user-tie me-1 text-danger"></i>
                                                {{ $file->delivered_by ? properNouns(nameAndLastName($file->user->name, $file->user->last_name)) : '' }}
                                            </span>
                                        </div>
                                    @endif

                                    <span class="text-muted">
                                        @if ($file->localidad)
                                            <i class="fas fa-map-marker-alt me-1 text-danger"></i>
                                            {{ $file->localidad->name ?? 'N/A' }}
                                        @endif
                                    </span>
                                </div>

                                <div>
                                    {{-- Número de paquete --}}
                                    <div class="pe-0 flex-column flex-center">
                                        @if (!empty($file->packages->num_package))
                                            <a class="btn btn-primary py-0 px-2"
                                                href="{{ route('packages.index', ['environment' => $file->environment_file->entorno, 'search_package' => $file->packages->num_package]) }}">
                                                <div class="text-center w-100 pt-0">
                                                    <span
                                                        class="fs-3 mb-0">{{ separateNumberPackage($file->packages->num_package) }}</span>
                                                </div>
                                            </a>
                                        @endif

                                        @if ($hasProductivity)
                                            <div class="d-flex flex-rowmy-2 rounded-2">
                                                <div>
                                                    <a href="{{ route('productivity.edit', $file->productivity->id) }}"
                                                        class="btn text-primary py-1" title="Editar ficha"><i
                                                            class="fa-regular fa-pen-to-square fa-lg"></i></a>
                                                </div>
                                                @if ($file->productivity->deleted_at)
                                                    <form
                                                        action="{{ route('productivity.restore', $file->productivity->id) }}"
                                                        method="POST"
                                                        onsubmit="return confirm('Desea reaturar la productividad a {{ nameAndLastName($file->productivity->user->name, $file->productivity->user->last_name) }}')">
                                                        @csrf
                                                        <button type="submit" class="btn text-danger py-1"
                                                            title="Restaurar productividad"><i
                                                                class="fa-solid fa-arrow-right-arrow-left fa-lg"></i></button>
                                                    </form>
                                                @else
                                                    <form
                                                        action="{{ route('productivity.delete', $file->productivity->id) }}"
                                                        method="POST"
                                                        onsubmit="return confirm('Desea eliminar la productividad a {{ nameAndLastName($file->productivity->user->name, $file->productivity->user->last_name) }}')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn text-danger py-1"
                                                            title="Eliminar productividad"><i
                                                                class="fa-solid fa-trash fa-lg"></i></button>
                                                    </form>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex" style="height: 100px; background-color: #3c3b3b;">
                                <div class="vr"></div>
                            </div>

                            {{-- Fechas --}}
                            <div class="d-flex flex-column col-md-6 align-items-end">
                                @if ($file->fecha_intervencion)
                                    <div class="text-muted small w-75 text-align-start">
                                        <i class="far fa-calendar-alt me-1 text-danger"></i>
                                        <strong>Fecha intervención</strong>
                                        {{ \Carbon\Carbon::parse($file->fecha_intervencion)->translatedFormat('j \\d\\e F \\d\\e Y') }}
                                    </div>
                                @endif
                                @if ($file->received_by)
                                    <div class="text-muted small w-75 text-align-start">
                                        <i class="far fa-calendar-alt me-1 text-danger"></i>
                                        <strong>Entregada por
                                            {{ properNouns(nameAndLastName($file->user->name, $file->user->last_name)) }}</strong>
                                            el
                                        {{ \Carbon\Carbon::parse($file->created_at)->translatedFormat('j \d\e F \d\e\l Y\, H:i') }}
                                    </div>
                                @endif
                                @if ($hasProductivity)
                                    <div class="text-muted small w-75 text-align-start">
                                        <i class="far fa-calendar-alt me-1 text-danger"></i>
                                        <strong>Digitada por {{ properNouns(nameAndLastName($file->productivity->user->name, $file->productivity->user->last_name)) }}</strong>
                                        el {{ \Carbon\Carbon::parse($file->productivity->created_at)->translatedFormat('j \d\e F \d\e\l Y\, H:i') }}
                                    </div>
                                @endif
                                @if ($file->received_by_environment)
                                    <div class="text-muted small w-75 text-align-start">
                                        <i class="far fa-calendar-alt me-1 text-danger"></i>
                                        <b>Devuelta por {{ properNouns(nameAndLastName($file->user_return->name, $file->user_return->last_name)) }} </b> el
                                        {{ \Carbon\Carbon::parse($file->return_date)->translatedFormat('j \d\e F \d\e\l Y\, H:i') }}
                                    </div>
                                @endif
                            </div>
                        </div>

                        @if ($hasProductivity && $file->productivity->observations)
                            <div class="d-flex" style="width: 100%;background-color: #3c3b3b;height: 1px;">
                                <div class="hr"></div>
                            </div>
                            <div class="text-muted px-4 py-1 small rounded-0 mb-0" role="alert">
                                {{ $file->productivity->observations }}
                            </div>
                        @endif

                        @if (count($file->status_histories) > 0)
                            <button class="btn btn-outline-dark btn-sm text-light" type="button"
                                data-bs-toggle="collapse"
                                data-bs-target="#collapseDetails{{ $file->id }}"
                                aria-expanded="false"
                                aria-controls="collapseDetails{{ $file->id }}">
                                Ver historial de estados <i class="fas fa-chevron-down ms-1"></i>
                            </button>

                            <div id="collapseDetails{{ $file->id }}" class="collapse">
                                <div class="row g-3">
                                    @foreach ($file->status_histories as $history)
                                        <div class="col-12 col-md-6 col-lg-3 border border-2 border-dark rounded mt-0">
                                            <div class="history-card border-secondary p-2 h-100 rounded shadow-sm">
                                                <div class="mb-2">
                                                    <span class="badge bg-transparent {{ getStatusFileClass($history->new_status) }} me-1">
                                                        {{ getStatusNameFile($history->new_status) }}
                                                    </span>
                                                </div>

                                                <p class="text-light mb-1 small">
                                                    <span class="text-muted">Cambiado de</span>
                                                    <span class="fw-semibold text-light textitalic">
                                                        {{ getStatusNameFile($history->previous_status) }}
                                                    </span>
                                                    <span class="text-muted">a</span>
                                                    <span class="fw-semibold text-light text-italic">
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
                <div class="alert alert-dark" role="alert">
                    Sin resultados
                </div>
            @endif
        </div>

    </div>
@endsection
