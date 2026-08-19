<section id="documentos_principal" class="container-fluid-planilla">
    <div class="container-fluid mt-2 px-0">
        {{--
        -- Opciones menú
        --}}
        <div class="doc-toolbar btn-group mb-3 d-flex position-relative justify-content-between " role="group">

            <div class="d-block d-lg-none ">
                <button class="btn btn-secondary" id="btn_menu_document">
                    <i class="fa-solid fa-ellipsis">
                </i></button>
            </div>

            <div class="nav nav-pills nav-fill menu-document  ">
                <li class="nav-item">
                    <button class="btn nav-link-files btn-menu" aria-current="page"
                        data-to-container="collapsePlanilla" role="button">
                        <i class="fa-solid fa-file-invoice-dollar"></i> Planilla SGSS
                    </button>
                </li>

                <li class="nav-item">
                    <button class="btn nav-link-files btn-menu ms-lg-2" aria-current="page"
                        data-to-container="collapseProrroga" role="button">
                        <i class="fa-solid fa-calendar-plus"></i> Prórroga
                    </button>
                </li>

                <li class="nav-item">
                    <button class="btn nav-link-files btn-menu ms-lg-2" aria-current="page"
                        data-to-container="collapseSecop" role="button">
                        <i class="fa-solid fa-file-contract"></i> SECOP
                    </button>
                </li>

                @if ($user->environment_id == 0)
                <li class="nav-item">
                    <button class="btn nav-link-files btn-menu ms-lg-2" aria-current="page"
                        data-to-container="collapseInforme" role="button">
                        <i class="fa-solid fa-file-alt"></i> Informe
                    </button>
                </li>
                @endif

                @if ($user->is_admin)
                <li class="nav-item">
                    <button class="btn nav-link-files btn-menu ms-lg-2" aria-current="page"
                        data-to-container="collapseCertifications" role="button">
                        <i class="fa-solid fa-upload"></i> Cargar certificaciones
                    </button>
                </li>

                @if ($user->is_admin)
                <li class="nav-item">
                    <button class="btn btn-menu-secondary dropdown-toggle ms-lg-2" type="button" data-bs-toggle="dropdown">
                        <i class="fa-solid fa-download"></i> Descargar prórrogas
                    </button>
                    <ul class="dropdown-menu doc-dropdown p-0">
                        <form class="doc-dropdown-form" method="post" action="{{ route('prorogation.download') }}">
                            @csrf
                            <label for="year">Año:</label>
                            <input class="form-control" type="number" id="year" name="year" value="{{ getCurrentYear() }}" required>
                            <label class="mt-2">Mes</label>
                            <select class="form-select" id="month" name="month">
                                <option value="1">Enero</option>
                                <option value="2">Febrero</option>
                                <option value="3">Marzo</option>
                                <option value="4">Abril</option>
                                <option value="5">Mayo</option>
                                <option value="6">Junio</option>
                                <option value="7">Julio</option>
                                <option value="8" selected>Agosto</option>
                                <option value="9">Septiembre</option>
                                <option value="10">Octubre</option>
                                <option value="11">Noviembre</option>
                                <option value="12">Diciembre</option>
                            </select>

                            <button type="submit" class="btn btn-primary mt-3">Descargar</button>
                        </form>
                    </ul>
                </li>

                <li class="nav-item">
                    <button class="btn btn-menu-secondary dropdown-toggle ms-lg-2" type="button" data-bs-toggle="dropdown">
                        <i class="fa-solid fa-download"></i> Descargar SECOP
                    </button>
                    <ul class="dropdown-menu doc-dropdown p-0">
                        <form class="doc-dropdown-form" method="post" action="{{ route('secop.download') }}">
                            @csrf
                            <label for="secopDownloadYear">Año:</label>
                            <input class="form-control" type="number" id="secopDownloadYear" name="year" value="{{ getCurrentYear() }}" required>
                            <label class="mt-2">Mes</label>
                            <select class="form-select" id="secopDownloadMonth" name="month">
                                <option value="1">Enero</option>
                                <option value="2">Febrero</option>
                                <option value="3">Marzo</option>
                                <option value="4">Abril</option>
                                <option value="5">Mayo</option>
                                <option value="6">Junio</option>
                                <option value="7">Julio</option>
                                <option value="8" selected>Agosto</option>
                                <option value="9">Septiembre</option>
                                <option value="10">Octubre</option>
                                <option value="11">Noviembre</option>
                                <option value="12">Diciembre</option>
                            </select>

                            <button type="submit" class="btn btn-primary mt-3">Descargar</button>
                        </form>
                    </ul>
                </li>
                @endif

                <li class="nav-item">
                    <button class="btn btn-menu-secondary dropdown-toggle ms-2" type="button" data-bs-toggle="dropdown">
                        <i class="fa-solid fa-download"></i> Descargar planillas
                    </button>
                    <ul class="dropdown-menu doc-dropdown p-0">
                        <form class="doc-dropdown-form" method="post" action="{{ route('insurance.download') }}">
                            @csrf
                            <label for="year">Año:</label>
                            <input class="form-control" type="number" id="year" name="year" value="{{ getCurrentYear() }}" required>
                            <label class="mt-2">Mes</label>
                            <select class="form-select" id="month" name="month">
                                <option value="1">Enero</option>
                                <option value="2">Febrero</option>
                                <option value="3">Marzo</option>
                                <option value="4">Abril</option>
                                <option value="5">Mayo</option>
                                <option value="6">Junio</option>
                                <option value="7">Julio</option>
                                <option value="8" selected>Agosto</option>
                                <option value="9">Septiembre</option>
                                <option value="10">Octubre</option>
                                <option value="11">Noviembre</option>
                                <option value="12">Diciembre</option>
                            </select>

                            <button type="submit" class="btn btn-primary mt-3">Descargar</button>
                        </form>
                    </ul>
                </li>

                @if ($user->environment_id == 0)
                <li class="nav-item">
                    <button class="btn btn-menu-secondary dropdown-toggle ms-lg-2" type="button" data-bs-toggle="dropdown">
                        <i class="fa-solid fa-download"></i> Descargar informes
                    </button>
                    <ul class="dropdown-menu doc-dropdown p-0">
                        <form class="doc-dropdown-form" method="post" action="{{ route('informe.download') }}">
                            @csrf
                            <label for="informeDownloadYear">Año:</label>
                            <input class="form-control" type="number" id="informeDownloadYear" name="year" value="{{ getCurrentYear() }}" required>
                            <label class="mt-2">Mes</label>
                            @php $currentMonth = (int) date('n'); @endphp
                            <select class="form-select" id="informeDownloadMonth" name="month">
                                <option value="1" {{ $currentMonth == 1 ? 'selected' : '' }}>Enero</option>
                                <option value="2" {{ $currentMonth == 2 ? 'selected' : '' }}>Febrero</option>
                                <option value="3" {{ $currentMonth == 3 ? 'selected' : '' }}>Marzo</option>
                                <option value="4" {{ $currentMonth == 4 ? 'selected' : '' }}>Abril</option>
                                <option value="5" {{ $currentMonth == 5 ? 'selected' : '' }}>Mayo</option>
                                <option value="6" {{ $currentMonth == 6 ? 'selected' : '' }}>Junio</option>
                                <option value="7" {{ $currentMonth == 7 ? 'selected' : '' }}>Julio</option>
                                <option value="8" {{ $currentMonth == 8 ? 'selected' : '' }}>Agosto</option>
                                <option value="9" {{ $currentMonth == 9 ? 'selected' : '' }}>Septiembre</option>
                                <option value="10" {{ $currentMonth == 10 ? 'selected' : '' }}>Octubre</option>
                                <option value="11" {{ $currentMonth == 11 ? 'selected' : '' }}>Noviembre</option>
                                <option value="12" {{ $currentMonth == 12 ? 'selected' : '' }}>Diciembre</option>
                            </select>
                            <label class="mt-2">Tipo</label>
                            <select class="form-select" id="informeDownloadType" name="type">
                                <option value="mensual">Mensual</option>
                                <option value="semanal">Semanal</option>
                                <option value="entrega">Entrega</option>
                            </select>
                            <button type="submit" class="btn btn-primary mt-3">Descargar</button>
                        </form>
                    </ul>
                </li>
                @endif

                @endif
            </div>

            <div>
                <button class="Documents-btn nav-link-files active" data-to-container="collapseFiles" role="button">
                    <span class="folderContainer">
                        <svg class="fileBack" width="146" height="113" viewBox="0 0 146 113" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M0 4C0 1.79086 1.79086 0 4 0H50.3802C51.8285 0 53.2056 0.627965 54.1553 1.72142L64.3303 13.4371C65.2799 14.5306 66.657 15.1585 68.1053 15.1585H141.509C143.718 15.1585 145.509 16.9494 145.509 19.1585V109C145.509 111.209 143.718 113 141.509 113H3.99999C1.79085 113 0 111.209 0 109V4Z"
                                fill="url(#paint0_linear_117_4)"></path>
                            <defs>
                                <linearGradient id="paint0_linear_117_4" x1="0" y1="0" x2="72.93" y2="95.4804"
                                    gradientUnits="userSpaceOnUse">
                                    <stop stop-color="#800000"></stop> <!-- Rojo oscuro -->
                                    <stop offset="1" stop-color="#400000"></stop>
                                    <!-- Rojo más oscuro para el degradado -->
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
                                <linearGradient id="paint0_linear_117_5" x1="38.7619" y1="8.71323" x2="66.9106"
                                    y2="82.8317" gradientUnits="userSpaceOnUse">
                                    <stop stop-color="#C30000"></stop> <!-- Rojo claro para la parte frontal -->
                                    <stop offset="1" stop-color="#5E0000"></stop> <!-- Rojo oscuro para el degradado -->
                                </linearGradient>
                            </defs>
                        </svg>
                    </span>
                    <p class="text mb-0">Ver documentos</p>
                </button>
            </div>
        </div>

        <div id="container-documents" >
            {{--
            -- Cargue de planilla
            --}}
            <div class="mb-5" id="collapsePlanilla" hidden>
                <form class="mb-4" method="POST" action="{{ route('insurance.upload') }}" id="insurance-form" enctype="multipart/form-data">
                    @csrf

                    <div role="alert" class="doc-alert">
                        <i class="fa-solid fa-circle-info"></i>
                        <div>
                            <strong>Importante:</strong> Por favor, cargue la planilla de pago a SGSS correspondiente al mes anterior.
                            <br> Asegúrese de que los datos ingresados sean correctos y verídicos.
                        </div>
                    </div>

                    <div class="doc-card">
                        <div class="doc-card-title"><span class="doc-icon"><i class="fa-solid fa-calendar-days"></i></span> Mes y año a certificar</div>
                        <div class="doc-grid-2">
                            <div class="doc-field">
                                <label>Mes</label>
                                <select name="certificationMonth" id="certificationMonth" class="form-select" required>
                                    <option value="">Selecciona un mes</option>
                                    <option value="1">Enero</option>
                                    <option value="2">Febrero</option>
                                    <option value="3">Marzo</option>
                                    <option value="4">Abril</option>
                                    <option value="5">Mayo</option>
                                    <option value="6">Junio</option>
                                    <option value="7">Julio</option>
                                    <option value="8">Agosto</option>
                                    <option value="9">Septiembre</option>
                                    <option value="10">Octubre</option>
                                    <option value="11">Noviembre</option>
                                    <option value="12">Diciembre</option>
                                </select>
                            </div>
                            <div class="doc-field">
                                <label>Año</label>
                                <input type="number" class="form-control" id="certificationYear" name="certificationYear" value="{{ getCurrentYear() }}" readonly required>
                            </div>
                        </div>
                    </div>

                    <div class="doc-card">
                        <div class="doc-card-title"><span class="doc-icon"><i class="fa-solid fa-clipboard-list"></i></span> Datos de la planilla</div>
                        <div class="doc-grid-2">
                            <div class="doc-field">
                                <label>Número de planilla</label>
                                <input type="number" class="form-control" id="formNumber" name="formNumber" placeholder="Ej: 123456" required>
                            </div>
                            <div class="doc-field">
                                <label>Fecha de pago</label>
                                <input type="date" class="form-control" id="datePay" name="datePay" required>
                            </div>
                        </div>
                    </div>

                    <div class="doc-card">
                        <div class="doc-card-title"><span class="doc-icon"><i class="fa-solid fa-coins"></i></span> Ingresar valores</div>
                        <div class="doc-grid-2">
                            <div class="doc-field">
                                <label>EPS {{ $data_user->eps }}</label>
                                <input type="number" class="form-control" id="epsValue" name="epsValue" placeholder="Valor sin puntos" required inputmode="numeric" pattern="[0-9]*">
                            </div>
                            <div class="doc-field">
                                <label>AFP {{ $data_user->afp }}</label>
                                <input type="number" class="form-control" id="afpValue" name="afpValue" placeholder="Valor sin puntos" required inputmode="numeric" pattern="[0-9]*">
                            </div>
                            <div class="doc-field">
                                <label>ARL {{ $data_user->arl }}</label>
                                <input type="number" class="form-control" id="arlValue" name="arlValue" placeholder="Valor sin puntos" required inputmode="numeric" pattern="[0-9]*">
                            </div>
                            <div class="doc-field">
                                <label>Caja de compensación {{ $data_user->caja }}</label>
                                <input type="number" class="form-control" id="compensationValue" name="compensationValue" placeholder="Valor sin puntos" inputmode="numeric" pattern="[0-9]*" @disabled( $data_user->caja == "NINGUNA" )>
                            </div>
                        </div>
                    </div>

                    <div class="doc-card d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div>
                            <div class="doc-card-title mb-1"><span class="doc-icon"><i class="fa-solid fa-receipt"></i></span> Valor total de la planilla</div>
                            <p class="fs-4 fw-bold mb-0" style="color: var(--doc-success);">💲 <span id="totalPlanillaSpan">0</span></p>
                        </div>
                        <input type="hidden" id="totalSgss" name="totalSgss" value="0">
                    </div>

                    <div id="btn_carga" class="file-upload w-100 ">
                        <input type="file" class="file-input" id="sgssDocument" name="sgssDocument" />
                        <label class="file-label" for="sgssDocument">
                            <i class="upload-icon fa-solid fa-cloud-arrow-up"></i>
                            <p class="ms-2">Click aquí para cargar la planilla</p>
                        </label>
                    </div>

                    <div class="text-center" id="btn-up-ins-cont"></div>
                </form>
            </div>
            

            {{--
            -- Cargue de prórroga
            --}}
            <div class="mb-5" id="collapseProrroga" hidden>
                <form class="mb-4" method="POST" action="{{ route('prorogation.upload') }}"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="doc-card">
                        <div class="doc-card-title"><span class="doc-icon"><i class="fa-solid fa-calendar-plus"></i></span> Cargue de prórroga</div>
                        <div class="doc-grid-2">
                            <div class="doc-field">
                                <label for="prorrogationMonth">Mes</label>
                                <select class="form-select" id="prorrogationMonth" name="prorrogationMonth" required>
                                    <option value="" selected disabled>Selecciona un mes</option>
                                    <option value="1">Enero</option>
                                    <option value="2">Febrero</option>
                                    <option value="3">Marzo</option>
                                    <option value="4">Abril</option>
                                    <option value="5">Mayo</option>
                                    <option value="6">Junio</option>
                                    <option value="7">Julio</option>
                                    <option value="8">Agosto</option>
                                    <option value="9">Septiembre</option>
                                    <option value="10">Octubre</option>
                                    <option value="11">Noviembre</option>
                                    <option value="12">Diciembre</option>
                                </select>
                            </div>

                            <div class="doc-field">
                                <label for="prorrogationYear">Año</label>
                                <input type="number" class="form-control" id="prorrogationYear" name="prorrogationYear"
                                value="{{ getCurrentYear() }}" readonly>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center gap-3 flex-wrap">
                            <div class="file-upload-container flex-grow-1">
                                <div class="file-upload px-4">
                                    <input type="file" class="file-input" name="prorogrationFile"
                                        id="prorogrationFile" />
                                    <label class="file-label" for="prorogrationFile">
                                        <i class="upload-icon fa-solid fa-cloud-arrow-up"></i>
                                        <p class="ms-2">Click aquí para cargar el archivo PDF</p>
                                    </label>
                                </div>
                            </div>

                            <div id="btn-up-prog-cont">
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            {{--
            -- Cargue de SECOP
            --}}
            <div class="mb-5" id="collapseSecop" hidden>
                <form class="mb-4" method="POST" action="{{ route('secop.upload') }}"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="doc-card">
                        <div class="doc-card-title"><span class="doc-icon"><i class="fa-solid fa-file-contract"></i></span> Cargue de SECOP</div>
                        <div class="doc-grid-2">
                            <div class="doc-field">
                                <label for="secopMonth">Mes</label>
                                <select class="form-select" id="secopMonth" name="secopMonth" required>
                                    <option value="" selected disabled>Selecciona un mes</option>
                                    <option value="1">Enero</option>
                                    <option value="2">Febrero</option>
                                    <option value="3">Marzo</option>
                                    <option value="4">Abril</option>
                                    <option value="5">Mayo</option>
                                    <option value="6">Junio</option>
                                    <option value="7">Julio</option>
                                    <option value="8">Agosto</option>
                                    <option value="9">Septiembre</option>
                                    <option value="10">Octubre</option>
                                    <option value="11">Noviembre</option>
                                    <option value="12">Diciembre</option>
                                </select>
                            </div>

                            <div class="doc-field">
                                <label for="secopYear">Año</label>
                                <input type="number" class="form-control" id="secopYear" name="secopYear"
                                value="{{ getCurrentYear() }}" readonly>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center gap-3 flex-wrap">
                            <div class="file-upload-container flex-grow-1">
                                <div class="file-upload px-4">
                                    <input type="file" class="file-input" name="secopFile"
                                        id="secopFile" />
                                    <label class="file-label" for="secopFile">
                                        <i class="upload-icon fa-solid fa-cloud-arrow-up"></i>
                                        <p class="ms-2">Click aquí para cargar el archivo PDF</p>
                                    </label>
                                </div>
                            </div>

                            <div id="btn-up-secop-cont">
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            {{--
            -- Informe de actividades
            --}}
            @if ($user->environment_id == 0)
            <div id="collapseInforme" class="mb-3" hidden>
                <form class="mb-3" action="{{ route('activities.actions') }}" method="POST" id="data-form-activities">
                    @csrf
                    <div class="activities-container mb-2">
                        <div class="row">
                            <h4 class="activities-header">Por favor verifique sus datos antes de continuar</h4>
                        </div>

                        <p class="activities-info-text">Tenga en cuenta que antes de generar el informe de actividades
                            debe cargar la
                            planilla de pago a SGSS correspondiente al mes inmediatamente anterior a certificar.</p>
                        <input type="text" name="type_action" id="type_action" hidden>

                        <div class="activities-grid container-fluid px-0">
                            <div class="activities-input-group row">
                                <div class="col-12 col-lg-4">
                                    <span class="activities-input-group-text">Nombre del contratista</span>
                                </div>
                                <div class="col-12 col-lg-8">
                                    <input type="text" class="activities-input activities-input-readonly w-100"
                                        value="{{ $user->name }} {{ $user->last_name }}" name="person_name" readonly
                                        required>
                                </div>
                            </div>

                            <div class="activities-input-group row">
                                <div class="col-12 col-lg-4">
                                    <span class="activities-input-group-text">No. contrato</span>
                                </div>

                                <div class="col-12 col-lg-8">
                                    <input type="text" class="activities-input activities-input-readonly w-100"
                                        value="{{ $data_user->contract }}" name="contract_number" readonly required>
                                </div>
                            </div>

                            <div class="activities-input-group row">
                                <div class="col-12 col-lg-4">
                                    <span class="activities-input-group-text">No. documento</span>
                                </div>
                                <div class="col-12 col-lg-8">
                                    <input type="number" class="activities-input activities-input-readonly w-100"
                                        value="{{ $data_user->document }}" name="document" readonly required>
                                </div>
                            </div>

                            <div class="activities-input-group row">
                                <div class="col-12 col-lg-4">
                                    <span class="activities-input-group-text">Objeto del contrato</span>
                                </div>
                                <div class="col-12 col-lg-8">
                                    <input type="text" class="activities-input activities-input-readonly w-100"
                                        value="{{ $user->role->name }}" name="role" readonly required>
                                </div>
                            </div>

                            <div class="activities-input-group activities-input-wide row">
                                <div class="col-12 col-lg-2">
                                    <span class="activities-input-group-text">Valor certificado</span>
                                </div>

                                <div class="col-12 col-lg-2">
                                    {{-- @php
                                        $valoresPago = [
                                        '1018467532' => 2710400,
                                        '51669685' => 2710400,
                                        '52749225' => 2710400,
                                        '52804740' => 2710400,
                                        '93150605' => 2710400,
                                        '1000254932' => 2637800,
                                        '1013602476' => 2710400,
                                        '1013685943' => 2710400,
                                        '1013690842' => 2637800,
                                        '1032486056' => 2710400,
                                        '1047219947' => 2710400,
                                        '80176736' => 2710400,
                                        '1000506973' => 2710400,
                                        '1014662729' => 2637800,
                                        '1021396476' => 2637800,
                                        '1016111653' => 2710400,
                                        '1026565287' => 2710400,
                                        '1023895640' => 2637800,
                                        '1023883739' => 580800,
                                        '1015995680' => 2710400,
                                        '1000005850' => 2420000,
                                        '1000186067' => 2420000,
                                        '52960559' => 2226400,
                                        '80134170' => 2226400,
                                        '1000383061' => 1113200,
                                        '1000693246' => 3402000,
                                        '1233501357' => 3402000,
                                        '65781935' => 3402000,
                                        ];
                                    @endphp --}}
                                    
                                    <input type="number" class="activities-input activities-input-readonly w-100"
                                        name="total_fee" id="total_fee"
                                        {{-- value="2980800" --}}
                                        required>
                                    <input type="text" name="total_fee_string" id="total_fee_string" value="" readonly
                                        required hidden>
                                </div>

                                <div class="col-12 col-lg-2 mt-md-2">
                                    <span class="activities-input-group-text">Periodo certificado</span>
                                </div>

                                <div class="col-12 col-lg-3">
                                    <input type="date" class="activities-input activities-input-editable w-100"
                                        name="init_date" value="{{ old('init_date') }}" required>
                                </div>

                                <div class="col-12 col-lg-3 mt-1 mt-md-0">
                                    <input type="date" class="activities-input activities-input-editable w-100"
                                        name="end_date" value="{{ old('end_date') }}" required>
                                </div>
                            </div>

                            <button type="submit" class="btn activities-btn-save w-100 btn-loader" value="generate-pdf">
                                <i class="fas fa-file-alt icon-left"></i> Generar informe
                            </button>
                            
                            {{-- <x-modal title="Mi Modal" 
                                name='Alerta' 
                                icons='alert'
                                isBtn=true
                                show={{ !$has_insurance }}
                                btnClass='nav-link-files'
                                :function="['data' => 'data-to-container', 'value' => 'collapsePlanilla']"
                                >
                                    <div class="px-0">
                                        <i class="fa-regular fa-circle-dot fa-beat-fade fa-2xl " style="color: #b30000;"></i>
                                        <span class="ml-3"> No ha realizado el cargue de la planilla, para generar el informe correspondiente al mes de {{getCurrentTextMonth() }}</span>
                            
                                    </div>
                            </x-modal> --}}
                        </div>
                    </div>

                    <div class="accordion" id="accordionActivities">
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingOne">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapseShowActivities" aria-expanded="true"
                                    aria-controls="collapseShowActivities">
                                    Mostrar actividades
                                </button>
                            </h2>

                            {{-- Ver actividades --}}
                            <div class="accordion-collapse collapse container-activities rounded-bottom"
                                id="collapseShowActivities">
                                <div class="row d-none d-lg-flex header-container">
                                    <div class="col-6 text-center header-section">
                                        <h6 class="header-title">OBLIGACIONES ESPECÍFICAS</h6>
                                    </div>
                                    <div class="col-6 text-center header-section">
                                        <h6 class="header-title">ACTIVIDADES REALIZADAS</h6>
                                    </div>
                                </div>

                                @if (count($activities) > 0)
                                @foreach ($activities as $key => $activity)
                                <div class="row text-center activity-row">
                                    <div class="col-12 col-lg-6 px-2 pt-2 pb-0 pt-lg-3 py-lg-3">
                                        <textarea class="textarea-activities resize-textarea" rows="4"
                                            name="text_specific_activities[]" readonly>
                                                {{ $activity->number_activity }}. {{ $activity->description }}
                                            </textarea>
                                    </div>
                                    <div class="col-12 col-lg-6 p-2 pb-lg-0 pt-lg-3 py-lg-3">
                                        <textarea class="form-control editable resize-textarea" rows="4"
                                            name="text_activities_done[]"
                                            required>{{ $done_activities[$key]->description ?? '' }}</textarea>
                                    </div>
                                </div>
                                @endforeach
                                @endif
                            </div>
                        </div>
                </form>

                {{-- Editar actividades --}}
                @if ($user->is_admin)
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingOne">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapseEditActivities" aria-expanded="true"
                            aria-controls="collapseEditActivities">
                            Editar actividades
                        </button>
                    </h2>

                    {{-- Obtener perfiles por cada entorno --}}
                    <div class="collapse container-edit-activities" id="collapseEditActivities">
                        <form class="container-fluid p-0 bg-dark px-2 pt-3 rounded-top-0"
                            action="{{ route('activities.save') }}" id="form-activities" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-12 col-md-4">
                                    <div class="input-group">
                                        <span class="input-group-text">Perfil</span>
                                        <select class="form-select" name="role_id" id="role_id" required>
                                            <option value="">Seleccionar</option>
                                            @foreach ($roles as $role)
                                            <option value="{{ $role->id }}" {{ old('role_id')==$role->id ?
                                                'selected' : ''}}>
                                                {{$role->name}}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-12 col-md-3 d-flex flex-row">
                                    <div class="input-group">
                                        <span class="input-group-text">Valor hora</span>
                                        <span class="input-group-text">$</span>
                                        <input type="number" class="form-control" name="hour_value" id="hour_value"
                                            value="{{ old('hour_value') }}" required>
                                    </div>
                                </div>

                                <div class="col-12 col-md-4 d-flex flex-row">
                                    <div class="input-group" title="Basado en 184 horas mensuales">
                                        <span class="input-group-text">Valor total honorarios</span>
                                        <span class="input-group-text">$</span>
                                        <input type="number" class="form-control" id="total_salary" name="total_salary"
                                            required>
                                    </div>
                                </div>

                                <div class="col-12 col-md-1 d-flex flex-row flex-center">
                                    <button type="submit" title="Guardar" class="btn btn-danger w-100 btn-loader">
                                        <i class="fa-solid fa-floppy-disk"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="row my-2" id="container-add-activities" hidden>
                                <div class="d-flex justify-content-start">
                                    <button type="button" title="Agregar obligaciones específicas"
                                        class="w-25 btn btn-obligations" id="btn-obligations">
                                        <i class="fas fa-plus me-2"></i> Agregar obligaciones específicas
                                    </button>
                                    <div id="container-number-obligations" class="d-flex flex-row">
                                        <div class="position-relative">
                                            <input type="number" class="form-control" id="number-obligations"
                                                placeholder="¿Cuántas?">
                                            <button class="btn btn-sm" id="btn-number-obligations">
                                                <i class="fa-solid fa-paper-plane fa-sm" style="color: #ffffff;"></i>
                                            </button>
                                        </div>
                                        <small class="ms-3 text-warning flex-center">
                                            <i class="fa-solid fa-exclamation-triangle me-2"></i>
                                            La cantidad ingresada se sumará a los campos ya existentes
                                        </small>
                                    </div>
                                </div>
                            </div>

                            <div class="row" id="container-textareas">
                                <div class="d-flex flex-column justify-content-center align-items-center my-5 py-5">
                                    <img src="{{ asset('img/icons/arrow_curve.png') }}" class="img_arrow mb-5"
                                        alt="Arrow">
                                    <span class="fs-6 mt-5 text-warning bg-transparent border-0">Seleccione un
                                        perfil para editarlo</span>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                @endif
            </div>
        </div>
        @endif

        @if ($user->is_admin)
        {{--
        -- Cargue de certificaciones
        --}}
        <div class="mb-5" id="collapseCertifications" hidden>
            <div class="doc-card">
                <div class="doc-card-title"><span class="doc-icon"><i class="fa-solid fa-stamp"></i></span> Cargar certificaciones</div>
                <div class="px-md-2">
                    <form class="d-flex justify-content-between flex-wrap gap-4" action="{{ route('certifications.upload') }}"
                        method="POST" enctype="multipart/form-data" id="form-certifications">
                        @csrf
                        <div class="flex-grow-1" style="min-width: 280px;">
                            <label for="file" class="labelFile">
                                <span class="border-0 bg-transparent"><svg xml:space="preserve"
                                        viewBox="0 0 184.69 184.69" xmlns:xlink="http://www.w3.org/1999/xlink"
                                        xmlns="http://www.w3.org/2000/svg" id="Capa_1" version="1.1" width="60px"
                                        height="60px">
                                        <g>
                                            <g>
                                                <g>
                                                    <path
                                                        d="M149.968,50.186c-8.017-14.308-23.796-22.515-40.717-19.813
                                C102.609,16.43,88.713,7.576,73.087,7.576c-22.117,0-40.112,17.994-40.112,40.115c0,0.913,0.036,1.854,0.118,2.834
                                C14.004,54.875,0,72.11,0,91.959c0,23.456,19.082,42.535,42.538,42.535h33.623v-7.025H42.538
                                c-19.583,0-35.509-15.929-35.509-35.509c0-17.526,13.084-32.621,30.442-35.105c0.931-0.132,1.768-0.633,2.326-1.392
                                c0.555-0.755,0.795-1.704,0.644-2.63c-0.297-1.904-0.447-3.582-0.447-5.139c0-18.249,14.852-33.094,33.094-33.094
                                c13.703,0,25.789,8.26,30.803,21.04c0.63,1.621,2.351,2.534,4.058,2.14c15.425-3.568,29.919,3.883,36.604,17.168
                                c0.508,1.027,1.503,1.736,2.641,1.897c17.368,2.473,30.481,17.569,30.481,35.112c0,19.58-15.937,35.509-35.52,35.509H97.391
                                v7.025h44.761c23.459,0,42.538-19.079,42.538-42.535C184.69,71.545,169.884,53.901,149.968,50.186z"
                                                        style="fill:#ffffff75;"></path>
                                                </g>
                                                <g>
                                                    <path d="M108.586,90.201c1.406-1.403,1.406-3.672,0-5.075L88.541,65.078
                                c-0.701-0.698-1.614-1.045-2.534-1.045l-0.064,0.011c-0.018,0-0.036-0.011-0.054-0.011c-0.931,0-1.85,0.361-2.534,1.045
                                L63.31,85.127c-1.403,1.403-1.403,3.672,0,5.075c1.403,1.406,3.672,1.406,5.075,0L82.296,76.29v97.227
                                c0,1.99,1.603,3.597,3.593,3.597c1.979,0,3.59-1.607,3.59-3.597V76.165l14.033,14.036
                                C104.91,91.608,107.183,91.608,108.586,90.201z" style="fill:#ffffff75;"></path>
                                                </g>
                                            </g>
                                        </g>
                                    </svg></span>
                                <p class="mt-3 text-white">Cargue las certificaciones en formato PDF <b>(Cantidad
                                        máxima seleccionable: 20)</b></p>
                            </label>
                            <input class="certificationsInput" name="certifications[]" id="file" type="file" multiple />
                        </div>

                        <div style="min-width: 220px;">
                            <div class="doc-field">
                                <label for="certificationYear">Año</label>
                                <select class="form-select" name="certificationYear" id="certificationYear">
                                    <option value="{{ getCurrentYear() }}">2026</option>
                                </select>
                            </div>

                            <div class="doc-field">
                                <label for="certificationMonth">Mes</label>
                                <select class="form-select" name="certificationMonth" id="certificationMonth">
                                    {{!! getListMonthsOptions() !!}}
                                </select>
                            </div>

                            <div id="box-btn-submit" class="mt-2"></div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="container-fluid">
                <div class="row">
                    @if (session('infosuccess'))
                    <div class="col-6">
                        <div class="border border-3">
                            <h6 class="text-bg-success p-2 rounded">Certificaciones subidas correctamente:</h6>
                            <ul>
                                @foreach (session('infosuccess') as $certification)
                                <li>{{ $certification }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    @endif

                    @if (session('infoerrors'))
                    <div class="col-6">
                        <div class="border border-3">
                            <h6 class="text-bg-danger p-2 rounded">Certificaciones no cargadas</h6>
                            <ul>
                                @foreach (session('infoerrors') as $certification)
                                <li>{{ $certification }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endif

        {{--
        -- Visualización de documentos
        --}}
        <div class="row" id="collapseFiles">
            <div class="col-12 col-md-6 h-100">
                <div class="doc-card h-100">
                    <div class="doc-card-title"><span class="doc-icon"><i class="fa-solid fa-folder-tree"></i></span> Documentos</div>
                    <div style="max-height: 600px; overflow-y: auto;">
                        <ul id="document-tree" class="link-document"></ul>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6">
                <div class="doc-card h-100">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="doc-card-title mb-0"><span class="doc-icon"><i class="fa-solid fa-eye"></i></span> Vista previa</div>
                        <div>
                            <a id="firmar_pdf" class="btn btn-sm firma_btn " title="Firmar documento">
                                <i class="fa-solid fa-signature fa-xl"></i>
                                <!-- Nueva funcionalidad - Tooltip con flecha -->
                                {{-- <div id="new-feature-tooltip" style="display:none;">
                                    <div class="tooltip-arrow"></div>
                                    <div class="tooltip-content">
                                        <span class="tooltip-close-btn" id="tooltip-close-btn">&times;</span>
                                        ¡Nueva funcionalidad! <br> Ahora puedes firmar el PDF directamente con
                                        un solo clic.
                                    </div>
                                </div> --}}
                            </a>

                            <button id="delete-viewer" class="btn btn-sm btn-danger position-relative" title="Eliminar documento">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </div>
                    </div>

                    <div class="document-preview" id="documento-preview" style="position: relative; height: 700px;">
                        <iframe id="pdf-embed" width="100%" height="100%"></iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{--
    -- Visualización de errores
    --}}
    @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif
    </div>
</section>

<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.10.377/pdf.min.js"></script>
<script src="https://mozilla.github.io/pdf.js/build/pdf.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.6.347/pdf.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf-lib/1.17.1/pdf-lib.min.js"></script>

<script>
    // Obtener el mes actual (0-11, así que sumamos 1)
    const currentMonth = new Date().getMonth() + 1;

    // Seleccionar el elemento <select>
    const selectElement = document.getElementById('certificationMonth');

    // Establecer el valor predeterminado al mes actual
    selectElement.value = currentMonth;
    // funcion para forzar y quitar los puntos
    document.querySelectorAll('#epsValue, #afpValue, #arlValue, #compensationValue')
    .forEach(input => {
        input.addEventListener('input', function () {
            this.value = this.value.replace(/\D/g, ''); // Elimina cualquier caracter que no sea número
        });
    });

</script>
