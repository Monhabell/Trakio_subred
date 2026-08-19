@extends('layouts.adm.navigation')

@section('main')

<!-- Page Heading -->
<div class="btn-group mb-1 d-flex justify-content-between" role="group">
    <!-- Menu -->
    <div class="nav nav-pills nav-fill">
        <li class="nav-item">
            <button class="btn btn-dark" type="button" data-bs-toggle="collapse" data-bs-target="#collapseWidthExample"
                aria-expanded="false" aria-controls="collapseWidthExample">
                Generar actas
            </button>
        </li>

        <div class="collapse collapse-horizontal" id="collapseWidthExample" style="max-heigth: 100px">
            <div class="d-flex flex-row radio-inputs ms-2">
                <li class="nav-item">
                    <label class="radio">
                        <input class="nav-link-acts" type="radio" name="radio" data-to-container="containerGenerateAct">
                        <span class="name px-3">Entrega</span>
                    </label>
                </li>

                <li class="nav-item">
                    <label class="radio">
                        <input class="nav-link-acts" type="radio" name="radio"
                            data-to-container="containerGenerateWeekAct">
                        <span class="name px-3">Semanales</span>
                    </label>
                </li>

                <li class="nav-item">
                    <label class="radio">
                        <input class="nav-link-acts" type="radio" name="radio"
                            data-to-container="containerGenerateBreachAct">
                        <span class="name px-3">Incumplimiento</span>
                    </label>
                </li>
            </div>
        </div>
    </div>

    <div class="nav nav-pills nav-fill">
        <li class="nav-item">
            <label class="Documents-btn nav-link-files nav-link-acts" data-to-container="containerShowActs"
                role="button">
                <span class="folderContainer">
                    <svg class="fileBack" width="146" height="113" viewBox="0 0 146 113" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M0 4C0 1.79086 1.79086 0 4 0H50.3802C51.8285 0 53.2056 0.627965 54.1553 1.72142L64.3303 13.4371C65.2799 14.5306 66.657 15.1585 68.1053 15.1585H141.509C143.718 15.1585 145.509 16.9494 145.509 19.1585V109C145.509 111.209 143.718 113 141.509 113H3.99999C1.79085 113 0 111.209 0 109V4Z"
                            fill="url(#paint0_linear_117_4)"></path>
                        <defs>
                            <linearGradient id="paint0_linear_117_4" x1="0" y1="0" x2="72.93" y2="95.4804"
                                gradientUnits="userSpaceOnUse">
                                <stop stop-color="#800000"></stop>
                                <stop offset="1" stop-color="#400000"></stop>
                            </linearGradient>
                        </defs>
                    </svg>

                    <svg class="filePage" width="88" height="99" viewBox="0 0 88 99" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <rect width="88" height="99" fill="url(#paint0_linear_117_6)"></rect>
                        <defs>
                            <linearGradient id="paint0_linear_117_6" x1="0" y1="0" x2="81" y2="160.5"
                                gradientUnits="userSpaceOnUse">
                                <stop stop-color="white"></stop>
                                <stop offset="1" stop-color="#686868"></stop>
                            </linearGradient>
                        </defs>
                    </svg>

                    <svg class="fileFront" width="160" height="79" viewBox="0 0 160 79" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M0.29306 12.2478C0.133905 9.38186 2.41499 6.97059 5.28537 6.97059H30.419H58.1902C59.5751 6.97059 60.9288 6.55982 62.0802 5.79025L68.977 1.18034C70.1283 0.410771 71.482 0 72.8669 0H77H155.462C157.87 0 159.733 2.1129 159.43 4.50232L150.443 75.5023C150.19 77.5013 148.489 79 146.474 79H7.78403C5.66106 79 3.9079 77.3415 3.79019 75.2218L0.29306 12.2478Z"
                            fill="url(#paint0_linear_117_5)"></path>
                        <defs>
                            <linearGradient id="paint0_linear_117_5" x1="38.7619" y1="8.71323" x2="66.9106" y2="82.8317"
                                gradientUnits="userSpaceOnUse">
                                <stop stop-color="#C30000"></stop>
                                <stop offset="1" stop-color="#5E0000"></stop>
                            </linearGradient>
                        </defs>
                    </svg>
                </span>
                <input class="nav-link-acts" type="radio" name="radio" data-to-container="containerGenerateBreachAct"
                    style="display: none">
                <span class="name px-3 text-white">Ver actas</span>
            </label>
        </li>
    </div>
    <!-- End menu -->
</div>

{{-- Ver actas --}}
<div class="card col-12 p-0 border-0 scrollable-container position-relative" id="containerShowActs">
    <div class="card-header bg-dark px-2 d-flex gap-2">
        {{-- Filtros --}}
        <div class="dropdown">
            <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary" data-bs-toggle="dropdown"
                aria-expanded="false">
                <i class="fa-solid fa-filter text-white"></i> Filtros
            </a>

            <ul class="dropdown-menu p-2 bg-dark">
                <form action="{{ route('adm.acts') }}" id="form-filter-acts">
                    <div class="mt-1">
                        <div class="input-group input-group-sm mb-3">
                            <label class="input-group-text bg-dark" for="monthFilter">Mes</label>
                            <select class="form-select form-select-sm" id="monthFilter" name="monthFilter">
                                <option value="all">Todos</option>
                                {{!! getListMonthsOptions('monthFilter') !!}}
                            </select>
                        </div>
                        <div class="input-group input-group-sm mb-3">
                            <label class="input-group-text bg-dark">Entorno</label>
                            <select class="form-select form-select-sm" id="environmentFilter" name="environmentFilter">
                                <option value="all">Todos</option>
                                @foreach($environments as $environment)
                                @if ($environment->id != 0)
                                <option
                                    value="{{ $environment->entorno }}"
                                    {{ request('environmentFilter') ==  $environment->entorno ? 'selected' : ''}}
                                >{{ $environment->entorno }}</option>
                                @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="input-group input-group-sm mb-3">
                            <label class="input-group-text bg-dark">Tipo de acta</label>
                            <select class="form-select form-select-sm" id="typeFilter" name="typeFilter">
                                <option value="all">Todos</option>
                                <option value="report-weekly" {{ request('typeFilter') ==  'report-weekly' ? 'selected' : ''}}>Semanales</option>
                                <option value="reception_acts" {{ request('typeFilter') ==  'reception_acts' ? 'selected' : ''}}>Entrega</option>
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-sm btn-success w-100 btn-loader">Aplicar</button>
                </form>
            </ul>
        </div>

        {{-- Descargar actas --}}
        <div class="dropdown">
            <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-secondary" data-bs-toggle="dropdown"
                aria-expanded="false">
                <i class="fa-solid fa-download text-white"></i> Descargar
            </a>

            <ul class="dropdown-menu p-2 bg-dark">
                <form action="{{ route('acts.download') }}" method="POST">
                    @csrf
                    <div class="mt-1">
                        <div class="input-group input-group-sm mb-3">
                            <label class="input-group-text bg-dark" for="dlYear">Año</label>
                            <input type="number" class="form-control form-control-sm" id="dlYear" name="yearFilter" value="{{ getCurrentYear() }}" required>
                        </div>
                        <div class="input-group input-group-sm mb-3">
                            <label class="input-group-text bg-dark" for="dlMonth">Mes</label>
                            <select class="form-select form-select-sm" id="dlMonth" name="monthFilter">
                                <option value="all">Todos</option>
                                {{!! getListMonthsOptions() !!}}
                            </select>
                        </div>
                        <div class="input-group input-group-sm mb-3">
                            <label class="input-group-text bg-dark">Tipo</label>
                            <select class="form-select form-select-sm" id="dlType" name="typeFilter">
                                <option value="all">Todos</option>
                                <option value="report-weekly">Semanales</option>
                                <option value="reception_acts">Entrega</option>
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-sm btn-secondary w-100 btn-loader">Descargar ZIP</button>
                </form>
            </ul>
        </div>
    </div>

    <div class="card-body bg-secondary pt-0 px-0 rounded-0 container-acts px-2" id="container-acts">
        @if (count($documents) > 0)
        <div class="row row-cols-md-2 row-cols-lg-3">
            @foreach ($documents as $document)
            <div class="p-2">
                <div class="card bg-dark px-3 py-2">
                    <div class="d-flex justify-content-between">
                        <div class="d-flex flex-center flex-row">
                            <i
                                class="{{ getIconEnvironmentAct($document->path, $document->name) }} text-gray-700 mx-2"></i>
                            <span class="fw-bold bg-transparent border-0">{{ getFileEnvironmentAct($document->path,
                                $document->name)}}</span>

                            @if (calculateElapsedMinutes($document->created_at) < 20)
                                <small class="ms-3 badge text-bg-info">Nuevo</small>
                            @endif
                        </div>

                        <form action="{{ route('document.destroy', ['id' => $document->id]) }}" method="POST"
                            onsubmit="return confirm('¿Está seguro que desea eliminar este documento?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn bg-transparent pe-0">
                                <i class="fa-solid fa-trash" style="color: #bd0000;"></i>
                            </button>
                        </form>
                    </div>

                    <div class="card-body p-0">
                        <iframe src="{{ asset('storage/' . $document->path) }}#toolbar=0" type="application/pdf"
                            width="100%" height="100%" style="border: none;"></iframe>
                    </div>

                    <div class="d-flex justify-content-between">
                        <div class="card-footer d-flex flex-row flex-center gap-1 bg-transparent border-0 p-0">
                            <a href="{{ asset('storage/' . $document->path) }}" class="btn btn-sm p-0" target="_blank"
                                title="Ver documento">
                                <i class="fa-solid fa-eye" style="color: #ffffff;"></i>
                            </a>

                            <a href="{{ asset('storage/' . $document->path) }}" class="btn btn-sm p-0" download
                                title="Descargar documento">
                                <i class="fa-solid fa-download" style="color: #ffffff;"></i>
                            </a>

                            <a href="#" onclick="printPDF('{{ asset('storage/' . $document->path) }}')" class="btn btn-sm p-0" 
                                title="Imprimir documento">
                                <i class="fa-solid fa-print" style="color: #ffffff;"></i>
                            </a>

                            <div class="ms-2 mb-0 flex-column">
                                @if ($document->name == 'reception_acts')
                                <h6 class="flex-center file-name-act mb-0">
                                    {{ getFileNameAct($document->name, $document->document_mes) }}
                                </h6>
                                @else
                                <h6 class="flex-center file-name-act mb-0">
                                    Acta semanal
                                    @if ($document->status == 2 && $document->name == 'report-weekly')
                                        {{-- icono de email --}}
                                        <i class="fa-solid fa-envelope text-warning ms-2 mt-1" title="Acta enviada"></i>
                                    @endif
                                </h6>
                                
                                @endif
                                
                                @if (calculateElapsedMinutes($document->created_at) < 20080)
                                <small class="file-date-act">{{ $document->created_at->diffForHumans() }}</small>
                                @else
                                <small class="file-date-act">{{ dateFormatN($document->created_at) }}</small>
                                @endif
                                
                            </div>
                        </div>

                        <div class="flex-center">
                            <a class="btn btn-sm btn-request-sign px-1" href = "{{ route('document.sign', ['document' => $document->id]) }}" title="Firmar documento">
                                <i class="fa-solid fa-pen-nib fa-lg" style="color:  #0d6efd"></i>
                            </a>

                            <button class="btn btn-sm btn-request-sign px-1 ms-2" value= "{{ $document->id }}" title="Solicitar firmas" data-bs-toggle="modal" data-bs-target="#modalUserSigns">
                                <i class="fa-solid fa-signature fa-lg" style="color: #e3342f"></i>
                            </button>

                            @if ($document->status == 0 && $document->name == 'report-weekly')
                                {{-- Botón para aprobar--}}
                                <form action="{{ route('document.approve', ['document' => $document->id]) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-sm px-2 btn-success" title="Aprobar documento">
                                        <i class="fa-solid fa-check"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="bg-dark w-100 d-flex flex-center flex-column py-3">
            <img src="{{ asset('img/icons/not_found.png') }}" width="200">
            <h5>Ningún archivo para mostrar</h5>
        </div>
        @endif
    </div>
</div>

{{-- Acta entregas --}}
<div class="col-12 p-0 scrollable-container position-relative" id="containerGenerateAct" hidden>
    <div class="steps-progress">
    </div>
    <div class="bg-dark rounded py-5 px-3 container-steps">
        <div class="step-item active">
            <span class="step-number">1</span>
            <label class="step-text">Entorno</label>
        </div>

        <div class="step-item">
            <span class="step-number">2</span>
            <label class="step-text">Técnico</label>
        </div>

        <div class="step-item">
            <span class="step-number">3</span>
            <label class="step-text">Periodo</label>
        </div>

        <div class="step-item">
            <span class="step-number">4</span>
            <label class="step-text">Firmas</label>
        </div>

        <div class="step-item">
            <span class="step-number">5</span>
            <label class="step-text">Datos entrega</label>
        </div>
    </div>

    <form action="{{ route('pdf.receptions') }}" class="container-fluid px-0 mt-4 mt-2 bg-dark" id="form-acts"
        method="POST">
        @csrf
        @method('POST')
        <div class="row shadow py-4">
            {{-- Elección de entorno --}}
            <div class="col-3">
                <div class="d-flex flex-center">
                    <h3 class="font-weight-bold text-white">Entorno</h3>
                </div>

                <div class="d-flex align-items-center px-4">
                    <span
                        class="bg-gradient rounded d-flex align-items-center justify-content-center text-white fs-6 w-100 py-1 shadow"
                        id="value-environment" style="opacity: 0">Entorno</span>
                </div>

                <div class="d-flex flex-column w-100 justify-content-center step-1">
                    @foreach ($environments as $environment)
                    @if ($environment->id != 0)
                    <div class="form-group mb-0 d-flex align-items-center justify-content-center w-100">
                        <input type="radio" class="btn-check" name="environment" id="{{ $environment->entorno }}"
                            value="{{ $environment->id }},{{ $environment->entorno }}"
                            data-value-environment="{{ $environment->entorno }}" data-act-attribute="environment">
                        <label class="btn btn-danger w-75" for="{{ $environment->entorno }}">{{
                            $environment->entorno }}</label>
                    </div>
                    @endif
                    @endforeach
                </div>
            </div>

            {{-- Elección de técnico --}}
            <div class="col-3">
                <div class="d-flex flex-center">
                    <h3 class="font-weight-bold text-white">Técnico</h3>
                </div>

                <div class="px-0 px-4">
                    <span
                        class="bg-gradient rounded d-flex align-items-center justify-content-center text-white fs-6 px-4 w-100 py-1 shadow"
                        id="value-technician" style="opacity: 0">Técnico</span>
                </div>

                <div class="viewer-acts d-flex flex-column step-2 flex-center w-100" style="opacity: 0">
                    @foreach ($technicians as $technician)
                    <div class="flex-center flex-column w-100">
                        <input type="radio" class="btn-check" name="technician"
                            id="{{ properNouns(nameAndLastName($technician->user->name, $technician->user->last_name)) }}"
                            value="{{ $technician->user->id }}" data-act-attribute="technician">
                        <label class="btn btn btn-danger d-flex flex-center flex-column w-75"
                            for="{{ properNouns(nameAndLastName($technician->user->name, $technician->user->last_name)) }}">
                            <span
                                title="{{ properNouns(nameAndLastName($technician->user->name, $technician->user->last_name)) }}"
                                class="w-25" class="flex-center">
                                <img src="{{ asset('img/img_perfil/' . $technician->id_user . '/' . $technician->url_img) }}"
                                    alt="{{ $technician->user->name }}" class="img-fluid rounded-circle">
                            </span>
                            <p class="font-weight-bold mb-0">
                                {{ properNouns(nameAndLastName($technician->user->name,
                                $technician->user->last_name)) }}
                            </p>
                        </label>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Elección de periodo --}}
            <div class="col-3">
                <div class="d-flex flex-center">
                    <h3 class="font-weight-bold text-white">Período</h3>
                </div>

                <div class="px-1">
                    <span
                        class="bg-gradient rounded d-flex align-items-center justify-content-center text-white fs-6 px-4 w-100 py-1 shadow"
                        id="value-period" style="opacity: 0">Periodo</span>
                </div>

                <div class="flex-column flex-center step-3" style="opacity: 0">
                    <div class="w-75">
                        <label class="text-bg-primary w-100 text-center mb-0">Rango de fechas</label>
                        <input type="date" class="form-control" name="initDate" id="init-date-act">
                        <input type="date" class="form-control" name="endDate" id="end-date-act">
                    </div>

                    <div class="mt-2 w-75">
                        <label class="text-bg-primary w-100 text-center mb-0">Fecha del acta</label>
                        <input type="date" class="form-control" name="actDate" id="act-date">
                    </div>

                    <div class="flex-center mt-3 w-75">
                        <input type="button" class="btn btn-danger w-100" value="Confirmar" data-act-attribute="period">
                    </div>
                </div>
            </div>

            {{-- Firmas --}}
            <div class="col-3 pe-5">
                <div class="d-flex flex-center">
                    <h3 class="font-weight-bold text-white">Firmas</h3>
                </div>

                <div class="d-flex justify-content-center w-100 d-flex align-items-center px-0">
                    <span class="bg-gradient rounded text-center text-white fs-6 py-1 px-2 w-100 shadow"
                        id="value-signature" style="opacity: 0">Firmas</span>
                </div>

                <div class="d-flex flex-column step-4" style="opacity: 0">
                    <div class="form-group mb-0 flex-center">
                        <a type="button" class="btn btn-danger w-75" data-act-attribute="signature">Confirmar</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4 p-4" id="dataReception">
            {{-- Datos entrega --}}
            <div class="col-12">
                <div class="d-flex justify-content-between step-5 ps-0" style="opacity: 0">
                    <div class="flex-center">
                        <h3 class="font-weight-bold text-white">Datos de entrega</h3>
                    </div>

                    <div class="form-group w-75 mb-0" id="dataFormats">
                        <div class="d-flex justify-content-between mb-2">
                            <div class="flex-center">
                                <label for="bases" class="mb-0">Datos sugeridos</label>
                            </div>

                            <div class="d-flex">
                                <button type="submit" class="btn btn-primary me-1 btn-loader">Generar PDF</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

{{-- Acta semanal --}}
<div class="container-fluid px-0">
    <!-- Contenedor de configuración -->
    <div class="row mb-5" id="containerGenerateWeekAct" hidden>
        <form class="col-12 col-lg-6 p-4 shadow rounded-0 rounded-start-2 bg-dark text-light" action="{{ route('configurar.tarea') }}" method="POST">
            @csrf
            <h4 class="mb-4">Configurar envío de correos</h4>
            <div class="mb-3">
                <label for="email" class="form-label">Para:</label>
                <input type="email" id="email" name="email" class="form-control" placeholder="info@trakio.pro" required>

                <label for="asunto" class="form-label mt-3">Asunto del correo:</label>
                <input type="text" id="subject" name="subject" class="form-control" placeholder="Escriba el asunto del correo" required>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="dias" class="form-label">Días de envío:</label>
                    <select id="days" name="days[]" class="form-select" multiple aria-label="Multiple select example" required>
                        <option value="monday">Lunes</option>
                        <option value="tuesday">Martes</option>
                        <option value="wednesday">Miércoles</option>
                        <option value="thursday">Jueves</option>
                        <option value="friday">Viernes</option>
                        <option value="saturday">Sábado</option>
                        <option value="sunday">Domingo</option>
                    </select>
                    <small class="form-text text-muted">Puede seleccionar múltiples días.</small>
                </div>
                <div class="col-md-6">
                    <label for="environment_id" class="form-label">Entorno:</label>
                    <select id="environment_id" name="environment_id" class="form-select" multiple required>
                        @foreach ($environments as $environment)
                        @if ($environment->id != 0)
                        <option value="{{$environment->id }}">{{ $environment->entorno }}</option>
                        @endif
                        @endforeach
                    </select>
                    <small class="form-text text-muted">Seleccione los entornos aplicables.</small>
                </div>
            </div>

            <div class="d-grid">
                <button type="submit" class="btn btn-primary btn-loader">Guardar configuración</button>
            </div>
        </form>
        
        {{-- Contenedor de lista de tareas --}}
        <div class="col-12 col-lg-6 p-4 shadow rounded-0 rounded-end-2 bg-dark text-light">
            <h4 class="mb-4">Tareas programadas</h4>
            <ul class="list-group">
                @foreach ($emailTasks as $task)
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <strong>{{ $task->subject }}</strong>
                        <small class="text-muted d-block">{{ $task->environment->entorno }}</small>
                        <small class="text-muted d-block">
                            @foreach ($task->days as $days)
                            {{ getTextDay($days) }}
                            @endforeach
                        </small>
                    </div>
                    <button class="btn btn-danger btn-sm">Eliminar</button>
                </li>
                @endforeach
            </ul>
        </div>
    </div>
</div>

{{-- Modal solicitud firmas --}}
<div class="modal fade" id="modalUserSigns" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
        <form class="modal-content" action = "{{ route('document.sign.request') }}" method="POST">
            @csrf
            <div class="modal-header pb-0 pt-3">
                <h1 class="modal-title fs-5" id="staticBackdropLabel">Solicitar firmas</h1>
                <ul class="nav nav-tabs border-0" id="box-nav-acts-signs">
                    @foreach ($environments as $environment)
                    @if ($environment->id != 1)
                    <li class="nav-item">
                        <a class="nav-link nav-acts-signs" aria-current="page" data-environment-id = "{{ $environment->id }}">{{ $environment->entorno }}</a>
                    </li>
                    @endif
                    @endforeach
                </ul>
            </div>
            <div class="modal-body">
                <div class="user-list message-container" id="user-list-sign">    
                    <div class="message-item">
                        <i class="fa-solid fa-circle-info"></i>
                        <p>Seleccione un entorno para continuar.</p>
                    </div>
                </div>
                <input type="number" id="url-document-tosign" name="document_id" hidden>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary btn-loader">Enviar solicitud</button>
            </div>
        </form>  
    </div>
</div>

@endsection