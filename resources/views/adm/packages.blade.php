@extends('layouts.adm.navigation')
@section('main')

<!-- Page body -->
<div class="container-fluid m-0 p-0 w-100 px-3">
  <form class="container-fluid px-0" id="form_filter_packages"
    action="{{ route('packages.index', ['environment' => request('environment')])}}">
    
    <div class="row px-2 py-2 bg-dark border-0">
      <div class="d-none col-md-6 d-sm-block navbar-search ps-0">
        <div class="input-group">
          <input type="number" class="form-control form-control-sm small" name="search_package"
            placeholder="Buscar paquete" aria-label="Search" aria-describedby="basic-addon2"
            value="{{ request('search_package') }}">
          <div class="input-group-append">
            <button class="btn btn-primary btn-sm" type="submit">
              <i class="fas fa-search fa-sm"></i>
            </button>
          </div>
        </div>
      </div>

      <div class="d-none col-md-6 d-sm-block navbar-search pe-0">
        <div class="input-group position-relative">
          <input type="search" class="form-control form-control-sm small" id="search_digitizer" name="search_digitizer"
            placeholder="Filtrar por digitador" aria-label="Search" aria-describedby="basic-addon2"
            value="{{ request('search_digitizer') }}" autocomplete="off">
          <input type="number" id="search_digitizer_id" name="search_digitizer_id"
            value="{{ request('search_digitizer_id') }}" hidden>
          <div class="input-group-append">
            <button class="btn btn-primary btn-sm" type="submit">
              <i class="fas fa-search fa-sm"></i>
            </button>
          </div>
        </div>
        <div hidden>
          <ul class="px-2 py-0 m-0 rounded-2 position-absolute z-3 bg-dark d-flex flex-column"
            id="list_digitizer_search" hidden></ul>
        </div>
      </div>
    </div>

    <div class="row px-2 rounded my-3 bg-dark border-0">
      <div class="row d-flex my-2 px-0 justify-content-start">
        <div class="ps-0 w-auto d-flex align-items-center">
          <button class="border-0 btn-primary rounded-3" type="button" data-bs-toggle="collapse"
            data-bs-target="#collapseFiltersPackage" aria-expanded="false" aria-controls="collapseFiltersPackage"
            title="Filtrar por estado del paquete">
            <img src="{{ asset('img/icons/filtrar.png') }}" width="25">
          </button>
        </div>

        <div class="col-md-auto p-0 d-flex align-items-center">
          <div class="collapse collapse-horizontal" id="collapseFiltersPackage" style="max-height: 28px;">
            <div class="d-flex flex-row p-0 bg-transparent border-0 gap-2">
              <div class="col-md-auto px-0">
                <input type="checkbox" name="filer_by_state[]" id="noasignado" value="0" hidden>
                <label for="noasignado" class="rounded border-left-light bg-gradient fs-6 px-2 mb-0">No asignado <small
                    class="fw-bold">({{ countPackageStatus($statusCounts, 0) }})</small></label>
              </div>
              <div class="col-md-auto px-0">
                <input type="checkbox" name="filer_by_state[]" id="asignado" value="1" hidden>
                <label for="asignado" class="rounded border-left-danger bg-gradient fs-6 px-2 mb-0">Pendiente <small
                    class="fw-bold">({{ countPackageStatus($statusCounts, 1) }})</small></label>
              </div>
              <div class="col-md-auto px-0">
                <input type="checkbox" name="filer_by_state[]" id="enproceso" value="2" hidden>
                <label for="enproceso" class="rounded border-left-warning bg-gradient fs-6 px-2 mb-0">Proceso <small
                    class="fw-bold">({{ countPackageStatus($statusCounts, 2) }})</small></label>
              </div>
              <div class="col-md-auto px-0">
                <input type="checkbox" name="filer_by_state[]" id="digitado" value="3" hidden>
                <label for="digitado" class="rounded border-left-success bg-gradient fs-6 px-2 mb-0">Digitado <small
                    class="fw-bold">({{ countPackageStatus($statusCounts, 3) }})</small></label>
              </div>
              <div class="col-md-auto px-0">
                <input type="checkbox" name="filer_by_state[]" id="devueltodig" value="4" hidden>
                <label for="devueltodig" class="rounded border-left-info bg-gradient fs-6 px-2 mb-0">Devuelto digitador
                  <small class="fw-bold">({{ countPackageStatus($statusCounts, 4) }})</small></label>
              </div>
              <div class="col-md-auto px-0">
                <input type="checkbox" name="filer_by_state[]" id="devueltoent" value="5" hidden>
                <label for="devueltoent" class="rounded border-left-primary bg-gradient fs-6 px-2 mb-0">Revisión entorno
                  <small class="fw-bold">({{ countPackageStatus($statusCounts, 5) }})</small></label>
              </div>
              <div class="col-md-auto px-0">
                <input type="checkbox" name="filer_by_state[]" id="recibidoent" value="6" hidden>
                <label for="recibidoent" class="rounded border-left-dark bg-gradient fs-6 px-2 mb-0">Recibido
                  entorno</label>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </form>
  @if (count($packages) > 0)
  <div class="row my-0" id="container-list-packages">
    <div class="col-12 col-md-3 px-0 scrollable-container box-packages">
      @foreach ($packages as $package)
      <div class="card_package w-100 mb-2 p-0 pt-1" id="{{ $package->id }}">
        <button class="ps-3 pe-0 w-75 pt-2 mb-3" data-bs-toggle="collapse" data-bs-target="#collapseFilesPackage"
          aria-expanded="false" aria-controls="collapseWidthExample" value="{{ $package->id }}" >
          @php
          $quantityCount = 
            $quantitiesDig->firstWhere('package_id', $package->id) ?? 
            (object) ['productivity_count' => 0, 'count' => 0, 'productivity_with_observations' => 0];
          @endphp

          <div class="w-100">
            <span class="d-flex flex-row justify-content-between">
              @if ($package->type == 'sisco')
                <h6 class="number_digit {{ getClassStatusPackages($package) }}">
                  Sisco
                </h6>
              @else
                <h6 class="number_digit text-start {{ getClassStatusPackages($package) }}">
                  {{ $quantityCount->productivity_count }} de {{ $quantityCount->count }}
                </h6>

                @if ($quantityCount->productivity_with_observations > 0)
                  <h6>
                    <i class="fa-solid fa-xs fa-circle-exclamation fa-beat" style="color: #66b3ff;"></i>
                    <span style="color: #66b3ff;">{{ $quantityCount->productivity_with_observations }}</span>
                  </h6>
                @endif

                <h6 class="{{ getClassStatusPackages($package) }}">
                  {{ percentageCompletePackage($quantityCount->productivity_count, $quantityCount->count) }}%
                </h6>
              @endif
            </span>

            @if ($package->status != '6')
            <div class="list-options-white" id="options-packages">
              <a data-package-id="{{ $package->id }}"
                class="p-1 show-options-package">
                <i class="fa-solid fa-ellipsis fa-sm" style="color: #ffffff;"></i>
              </a>
              <ul class="p-0 mb-1" id="{{ $package->id }}-options" hidden>
                <li class="option-rounded-blue option-package d-flex flex-center"
                  data-package-id="{{ $package->id }}" data-package-status="{{$package->status}}" data-option-type="environment">
                  <form action="{{ route('package.return', [$package, $user]) }}" id="form-return-{{ $package->id }}"
                    method="POST">
                    @csrf
                    @method('PUT')
                    @if ($package->status != 5)
                    <i class="fa-solid fa-arrow-rotate-left fa-sm" style="color: #ffffff;"
                      title="Devolver a entorno"></i>
                    @else
                    <i class="fa-solid fa-xmark fa-sm" style="color: #ff0000;"
                      title="Cancelar devolución a entorno"></i>
                    @endif
                  </form>
                </li>

                {{-- Devolver al digitador --}}
                @if ($package->status != 0 && $package->status != 5 && $package->status != 6)
                <li class="option-rounded-blue p-1 option-package"
                  data-package-id="{{ $package->id }}" data-package-status="{{$package->status}}" data-option-type="digitizer">
                  <form action="{{ route('package.returnDigitizer', $package) }}" id="form-return-dig-{{ $package->id }}"
                    method="POST">
                    @csrf
                    @method('PUT')
                      @if ($package->status < 4) 
                      <i class="fa-solid fa-check-to-slot fa-sm" style="color: #ffffff;" title="Recibir a digitador"></i>
                      @else
                      <i class="fa-solid fa-person-walking-arrow-loop-left fa-sm" style="color: #ffffff;" title="Devolver a digitador"></i>
                      @endif
                  </form>
                </li>
                @endif
              </ul>
            </div>
            @endif
          </div>
          <span class="icon my-2">
            <h2 class="text-white mb-0">{{ substr($package->num_package, 0, 1) }}-{{ substr($package->num_package, 1) }}
            </h2>
          </span>

          <div class="d-flex justify-content-start flex-column pb-0">
            <h4 class="text-start mb-0 mt-2">
              @if ($package->status != '0')
              <div class="viewer d-flex justify-content-start">
                @foreach ($assignments as $assignment)
                @if ($assignment->package_id == $package->id)
                <span title="{{ nameAndLastName($assignment->user->name, $assignment->user->last_name) }}" data-assignment-id = "{{ $assignment->id }}" data-package-id = "{{  $package->id }}">
                  <img src="{{ asset(getUrlImgProfile($assignment->user->id, $data_users)) }}" alt="Foto de perfil">
                </span>
                @endif
                @endforeach
              </div>
              @else
              <span class="bg-danger rounded-pill px-2">No asignado</span>
              @endif
            </h4>
            <p class="text-start d-flex flex-column">
              <span>Recibido: {{ $package->created_at->format('d/m/Y') }}</span>
              @if ($package->status == 6)
              <span>Devuelto: {{ $package->updated_at->format('d/m/Y') }}</span>
              @endif
            </p>
          </div>

          <div class="shine"></div>
          <div class="background">
            <div class="tiles">
              <div class="tile tile-1"></div>
              <div class="tile tile-2"></div>
              <div class="tile tile-3"></div>
              <div class="tile tile-4"></div>
              <div class="tile tile-5"></div>
              <div class="tile tile-6"></div>
              <div class="tile tile-7"></div>
              <div class="tile tile-8"></div>
              <div class="tile tile-9"></div>
              <div class="tile tile-10"></div>
            </div>

            <div class="line line-1"></div>
            <div class="line line-2"></div>
            <div class="line line-3"></div>
          </div>
        </button>
      </div>
      @endforeach
    </div>

    <div class="col-12 col-md-9 mb-2 scrollable-container box-files pe-0">
      <div class="h-100">
        <div class="collapse collapse-horizontal" id="collapseFilesPackage">
          <form class="w-100 py-2 px-4 d-flex justify-content-between bg-dark rounded-0" id="package-info">
            <input type="checkbox" name="unassign" value="4" hidden>
          </form>

          <form class="collapse border-0 bg-dark" id="formAddFilePackage" action="{{ route('receptions.store') }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="row g-2">
              <!-- No. ficha -->
              <div class="col-2">
                <div class="mb-2">
                  <label for="newFile" class="form-label mb-0">No. ficha</label>
                  <input type="number" class="form-control form-control-sm" id="newFile" name="newFile" required>
                </div>
              </div>

              <!-- Formato -->
              <div class="col-4">
                <div class="mb-2">
                  <label for="newFormat" class="form-label mb-0">Formato</label>
                  <select class="form-select form-select-sm" id="newFormat" name="newFormat" required>
                    <!-- opciones -->
                  </select>
                </div>
              </div>

              <!-- Cantidad -->
              <div class="col-1">
                <div class="mb-2">
                  <label for="newQuantity" class="form-label mb-0">Cantidad</label>
                  <input type="number" class="form-control form-control-sm" id="newQuantity" name="newQuantity" value="1" required>
                </div>
              </div>

              <!-- Fecha de intervención -->
              <div class="col-3">
                <div class="mb-2">
                  <label for="newFechaIntervencion" class="form-label mb-0">Fecha intervención</label>
                  <input type="date" class="form-control form-control-sm" id="newFechaIntervencion" name="newFechaIntervencion" required>
                </div>
              </div>

              <!-- Orden -->
              <div class="col-1">
                <div class="mb-2">
                  <label for="newOrder" class="form-label mb-0">Orden</label>
                  <input type="number" class="form-control form-control-sm" id="newOrder" name="newOrder" required>
                </div>
              </div>

              <!-- Botón -->
              <div class="col-1 d-flex align-items-center">
                <button type="submit" class="btn btn-success btn-sm w-100 btn-loader">
                  <i class="fa-solid fa-floppy-disk fa-xs"></i>
                </button>
              </div>

              <input type="number" class="form-control" id="newPackageId" name="newPackageId" hidden>
              <input type="number" class="form-control" id="newDeliveredBy" name="newDeliveredBy" hidden>
              <input type="number" class="form-control" id="newReceivedBy" name="newReceivedBy" hidden>
              <input type="number" class="form-control" id="newEnvironmentId" name="newEnvironmentId" hidden>
              <input type="number" class="form-control" id="newProfessional" name="newProfessional" hidden>
              <input type="number" class="form-control" id="newBatchNumber" name="newBatchNumber" hidden>
            </div>
          </form>

          <div class="card-header bg-dark rounded-0" id="card-header-packages" hidden>
            <form class="input-group input-group-sm" id="form_assigment"
              action="{{ route('assignments.store', ['environment' => request('environment')]) }}" method="POST">
              @csrf
              <div class="input-group">
                <div class="position-relative w-50">
                  <button type="submit" class="btn btn-sm btn-info py-0 rounded btn-loader position-absolute z-3 btn-assign">Guardar</button>
                  <input type="search" class="form-control form-control-sm" placeholder="Asignar a" autocomplete="off"
                    id="asign_digitizer_name">
                  
                </div>
                <input type="text" class="form-control form-control-sm" placeholder="Observaciones" name="observations_asign_package"
                  id="observations_asign_package" aria-label="Example text with two button addons">

                <input type="number" id="asign_digitizer_id" name="asign_digitizer_id" hidden>
                <input type="number" id="asign_package_id" name="asign_package_id" hidden>
              </div>
              <div class="ms-5 ps-3 position-relative w-100" hidden>
                <ul class="px-4 py-0 m-0 rounded-2 position-absolute z-3 bg-dark d-flex flex-column"
                  id="list_digitizer"></ul>
              </div>
            </form>
          </div>

          <div class="card card-body py-3 bg-dark rounded-0" id="card-body-packages">
          </div>
        </div>

        <div class="d-flex flex-column justify-content-center align-items-center h-100" id="container_init_package">
          <img src="{{ asset('img/icons/arrow_curve.png') }}" class="img_arrow mb-5" alt="Arrow">
          <span class="fs-5 mt-5">Seleccione un paquete para continuar</span>
        </div>
      </div>
    </div>
  </div>
  @else
  <div class="row mt-3">
    <div class="col-12 d-flex flex-column justify-content-center align-items-center">
      <img src="{{ asset('img/icons/not_found.png') }}" class="icon_not_found">
      <span class="fs-5">Ningún paquete para mostrar</span>
    </div>
  </div>
  @endif
</div>

@endsection