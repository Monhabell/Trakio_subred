@extends('layouts.adm.navigation')

@section('main')
    <!-- Page heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1>Recepción entorno {{ $environment->entorno }}
            @if ($receptions_quantity > 0)
                <span class="badge text-bg-danger">{{ $receptions_quantity }}</span>
            @endif
        </h1>
    </div>

    <div class="w-100 d-flex gap-2">
        <button class="menu-reception w-50 pt-1 active" aria-current="page" data-to-container="table-receptions-normal"
            role="button">
            Fichas
        </button>

        <button class="menu-reception w-50 pt-1" aria-current="page" data-to-container="table-receptions-sisco" role="button">
            Sisco
        </button>
    </div>

    <div class="card shadow mb-4 bg-dark pt-0">
        <div class="card-body">
            <div class="table-responsive">
                @if ($receptions_quantity > 0)
                    <form method="POST" id="formReception">
                        @csrf
                        @method('POST')

                        <div class="d-flex w-100 justify-content-between py-3">
                            <div class="input-group input-group w-25 icon-box-red">
                                <span class="input-group-text" id="inputGroup-sizing">{{ $environment_id }}-</span>
                                <input type="number" class="form-control form-reception_package" name="num_package"
                                    value="{{ empty(substr($last_number_package, 1)) ? 1 : (int) substr($last_number_package, 1) + 1 }}"
                                    aria-label="Sizing example input" aria-describedby="inputGroup-sizing">
                                <input type="number" id="batch_number" name="batch_number" hidden>
                                <input type="hidden" value="{{ $environment_id }}" name="environment_id" readonly>
                            </div>

                            <div id="btn-receive-box"></div>
                        </div>

                        <div class="col-12 px-0 table-receptions" id="table-receptions-normal">
                            <table class="table table-dark position-relative table-hover table-borderless"
                                id="tableReceptions" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th class="text-center text-danger">
                                            <label for="selectAll" class="m-0">Todo</label>
                                            <input type="checkbox" class="checkbox-select-all" name="selectAll"
                                                id="selectAll" hidden>
                                        </th>
                                        <th>Orden</th>
                                        <th>Lote</th>
                                        <th>No. ficha</th>
                                        <th>Formato</th>
                                        <th>Cantidad</th>
                                        <th>Entregado por</th>
                                        <th>Fecha cargue</th>
                                        <th>Fecha intervención</th>
                                        <th>Opciones</th>
                                    </tr>
                                </thead>
                                <tbody class="scrollable-container tbody-receptions">
                                    @foreach ($receptions as $key => $reception)
                                        @php
                                            $prevBatch = $receptions[$key > 0 ? $key - 1 : 0]->batch_number;
                                        @endphp
                                        <tr
                                            class="{{ $reception->order_file == 1 ? 'border-top' : '' }} {{ $prevBatch != $reception->batch_number ? 'border-top border-primary' : '' }} ">
                                            <td>
                                                <div class="form-check ps-0">
                                                    <input type="checkbox" class="form-check-input shadow border-2"
                                                        name="receptionsFiles[]" data-type="normal"
                                                        value="{{ $reception->id }}"
                                                        data-batch-number="{{ $reception->batch_number }}">
                                                </div>
                                            </td>
                                            <td>{{ $reception->order_file }}</td>
                                            <td>{{ $reception->batch_number }} </td>
                                            <td>
                                                <div class="d-flex flex-column">
                                                    {{ $reception->file_number }}
                                                    @if ($reception->localidad)
                                                        <small class="text-muted">
                                                            {{ $reception->localidad->name }}
                                                        </small>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex flex-column">
                                                    {{ $reception->bases->name }}
                                                    @if (isset($reception->users) && count($reception->users) > 0)
                                                        <small class="text-muted">
                                                            @foreach ($reception->users as $professional)
                                                                {{ properNouns(nameAndLastName($professional->name, $professional->last_name)) }}
                                                                @if (!$loop->last)
                                                                    -
                                                                @endif
                                                            @endforeach
                                                        </small>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>{{ $reception->quantity }}</td>
                                            <td>{{ $reception->delivered_by ? properNouns(nameAndLastName($reception->user->name, $reception->user->last_name)) : '' }}
                                            </td>
                                            <td>{{ dateFormatN($reception->created_at) }}</br></td>
                                            <td>{{ formatDMY($reception->fecha_intervencion) }}</br></td>
                                            <td>
                                                <a class="btn btn-info btn-sm" data-bs-toggle="collapse"
                                                    href="#{{ $reception->file_number }}{{ $reception->id }}"
                                                    role="button" aria-expanded="false" aria-controls="collapseExample">
                                                    <i class="fa-solid fa-pen-to-square"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        <tr class="p-0">
                                            <td colspan="10" class="p-0">
                                                <div class="collapse input-group"
                                                    id="{{ $reception->file_number }}{{ $reception->id }}">
                                                    <form action="{{ route('receptions.update', $reception) }}"
                                                        class="input-group me-2" method="POST">
                                                        @csrf
                                                        <input type="number" name="order_file" class="form-control"
                                                            value="{{ $reception->order_file }}">
                                                        <input type="text" name="batch_number" class="form-control"
                                                            value="{{ $reception->batch_number }}" disabled>
                                                        <input type="number" name="file_number" class="form-control"
                                                            value="{{ $reception->file_number }}">
                                                        <select name="format_id" class="form-select w-25" name="format_id">
                                                            @foreach ($bases as $base)
                                                                <option value="{{ $base->id }}"
                                                                    @selected($reception->format_id === $base->id)>{{ $base->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        <input type="number" class="form-control" name="quantity"
                                                            value="{{ $reception->quantity }}">
                                                        <input type="text" class="form-control"
                                                            value="{{ $reception->user->name }} {{ $reception->user->last_name }}"
                                                            disabled>
                                                        <input type="date" class="form-control" name="fecha_intervencion"
                                                            value="{{ formatDateInput($reception->created_at) }}"
                                                            disabled>
                                                        <input type="date" class="form-control"
                                                            name="fecha_intervencion"
                                                            value="{{ formatDateInput($reception->fecha_intervencion) }}">
                                                        <button type="submit"
                                                            class="btn btn-sm btn-info border-0 btn-loader">
                                                            Guardar
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="col-12 px-0 table-receptions" id="table-receptions-sisco" style="display: none;">
                            <table class="table table-dark position-relative table-hover table-borderless"
                                id="tableReceptionsSisco" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th class="text-center text-danger">
                                            <label for="selectAllSisco" class="m-0">Todo</label>
                                            <input type="checkbox" class="checkbox-select-all" name="selectAllSisco"
                                                id="selectAllSisco" hidden>
                                        </th>
                                        <th>Orden</th>
                                        <th>Lote</th>
                                        <th>No. ficha</th>
                                        <th>Formato</th>
                                        <th>Entregado por</th>
                                        <th>Fecha cargue</th>
                                        <th>Fecha intervención</th>
                                        <th>Opciones</th>
                                    </tr>
                                </thead>
                                <tbody class="scrollable-container tbody-receptions">
                                    @foreach ($receptions_sisco as $key => $reception)
                                        @php
                                            $prevBatch = $receptions_sisco[$key > 0 ? $key - 1 : 0]->batch_number;
                                        @endphp
                                        <tr
                                            class="{{ $reception->order_file == 1 ? 'border-top' : '' }} {{ $prevBatch != $reception->batch_number ? 'border-top border-primary' : '' }} ">
                                            <td>
                                                <div class="form-check ps-0">
                                                    <input type="checkbox" class="form-check-input shadow border-2"
                                                        name="receptionsFilesSisco[]" data-type="sisco"
                                                        value="{{ $reception->id }}"
                                                        data-batch-number="{{ $reception->batch_number }}">
                                                </div>
                                            </td>
                                            <td>{{ $reception->order_file }}</td>
                                            <td>{{ $reception->batch_number }} </td>
                                            <td>
                                                <div class="d-flex flex-column">
                                                    {{ $reception->consecutivo_sisco }}
                                                    
                                                    @if ($reception->localidad)
                                                        <small class="text-muted">
                                                            {{ $reception->localidad->name }}
                                                        </small>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex flex-column">
                                                    {{ $reception->format->name }}
                                                    @if (isset($reception->users) && count($reception->users) > 0)
                                                        <small class="text-muted">
                                                            @foreach ($reception->users as $professional)
                                                                {{ properNouns(nameAndLastName($professional->name, $professional->last_name)) }}
                                                                @if (!$loop->last)
                                                                    -
                                                                @endif
                                                            @endforeach
                                                        </small>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>{{ $reception->deliveredBy ? properNouns(nameAndLastName($reception->deliveredBy->name, $reception->deliveredBy->last_name)) : '' }}
                                            </td>
                                            <td>{{ dateFormatN($reception->created_at) }}</br></td>
                                            <td>{{ formatDMY($reception->intervention_date) }}</br></td>
                                            <td>
                                                {{-- <a class="btn btn-info btn-sm" data-bs-toggle="collapse"
                                            href="#{{$reception->file_number}}{{$reception->id}}" role="button"
                                            aria-expanded="false" aria-controls="collapseExample">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a> --}}
                                                {{-- <button type="button" class="btn btn-danger btn-sm delete-reception-button"
                                            data-reception-id="{{ $reception->id }}">
                                            <i class="fas fa-trash"></i>
                                        </button> --}}
                                            </td>
                                        </tr>
                                        {{-- <tr class="p-0">
                                    <td colspan="10" class="p-0">
                                        <div class="collapse input-group" id="{{$reception->file_number}}{{$reception->id}}">
                                            <form action="{{ route('receptions.update', $reception) }}" class="input-group me-2" method="POST">
                                                @csrf
                                                <input type="number" name="order_file" class="form-control" value="{{ $reception->order_file }}">
                                                <input type="text" name="batch_number" class="form-control" value="{{ $reception->batch_number }}" disabled>
                                                <input type="number" name="file_number" class="form-control" value="{{ $reception->file_number }}">
                                                <select name="format_id" class="form-select w-25" name="format_id">
                                                    @foreach ($bases as $base)
                                                    <option value="{{ $base->id }}" 
                                                        @selected($reception->format_id === $base->id)>{{ $base->name }}
                                                    </option>
                                                    @endforeach
                                                </select>
                                                <input type="number" class="form-control" name="quantity" value="{{ $reception->quantity }}">
                                                <input type="text" class="form-control" value="{{ $reception->user->name }} {{ $reception->user->last_name }}" disabled>
                                                <input type="date" class="form-control" name="fecha_intervencion" value="{{ formatDateInput($reception->created_at) }}" disabled>
                                                <input type="date" class="form-control" name="fecha_intervencion" value="{{ formatDateInput($reception->fecha_intervencion) }}">
                                                <button type="submit" class="btn btn-sm btn-info border-0 btn-loader">
                                                    Guardar
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr> --}}
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </form>
                @else
                    <div role="alert">
                        Recepciones de {{ strtolower($environment->entorno) }} al día</div>
                @endif
            </div>
        </div>
    </div>

@endsection
