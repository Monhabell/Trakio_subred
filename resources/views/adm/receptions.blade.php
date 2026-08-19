@extends('layouts.adm.navigation')

@section('main')

<x-reception-skeleton />

<div id="reception-content" style="display: none">
    <div class="w-100 d-flex gap-2 mb-3">
        <button class="rcp-btn-tab w-50 pt-1 active" aria-current="page" data-to-container="table-receptions-normal"
            role="button">
            Fichas {{ strtolower($environment_name) }}
        </button>

        <button class="rcp-btn-tab w-50 pt-1" aria-current="page" data-to-container="table-receptions-sisco"
            role="button">
            Sisco {{ strtolower($environment_name) }}
        </button>
    </div>

    <div class="card shadow mb-4 rcp-custom-card pt-0">
        <div class="card-body">
            <div class="table-responsive">
                <form method="POST" id="formReception">
                    @csrf
                    @method('POST')

                    <div class="d-flex w-100 justify-content-between py-3">
                        <div class="input-group w-25 icon-box-red">
                            <span class="input-group-text" id="inputGroup-sizing">{{ $environment_id }}-</span>
                            <input type="number" class="form-control form-reception_package" name="num_package"
                                value="{{ empty(substr($lastNumberPackage, 1)) ? 1 : (int) substr($lastNumberPackage, 1) + 1 }}"
                                aria-label="Sizing example input" aria-describedby="inputGroup-sizing">
                            <input type="number" id="batch_number" name="batch_number" hidden>
                            <input type="hidden" value="{{ $environment_id }}" name="environment_id" readonly>
                        </div>

                        <div id="btn-receive-box"></div>
                    </div>

                    <div class="col-12 px-0 table-receptions" id="table-receptions-normal">
                        <table class="table rcp-theme-table position-relative table-hover table-borderless" id="tableReceptions"
                            width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th class="text-center text-danger">
                                        <input type="checkbox" class="checkbox-select-all form-check-input shadow border-2"
                                            name="selectAll" id="selectAll">
                                    </th>
                                    <th>Orden</th>
                                    <th>No. ficha</th>
                                    <th>Formato</th>
                                    <th>Cantidad usuarios</th>
                                    <th>Cargue</th>
                                    <th>Fecha intervención</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody class="scrollable-container tbody-receptions" id="tbody-receptions-normal">
                            </tbody>
                        </table>
                    </div>

                    <div class="col-12 px-0 table-receptions" id="table-receptions-sisco" style="display: none;">
                        <table class="table rcp-theme-table position-relative table-hover table-borderless"
                            id="tableReceptionsSisco" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th class="text-center text-danger">
                                        <input type="checkbox" class="checkbox-select-all form-check-input shadow border-2" name="selectAllSisco" id="selectAllSisco">
                                    </th>
                                    <th>Orden</th>
                                    <th>No. ficha</th>
                                    <th>Formato</th>
                                    <th>Cargue</th>
                                    <th>Fecha intervención</th>
                                    <th>Opciones</th>
                                </tr>
                            </thead>
                            <tbody class="scrollable-container tbody-receptions" id="tbody-receptions-sisco">
                            </tbody>
                        </table>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection