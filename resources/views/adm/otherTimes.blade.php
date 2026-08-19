@extends('layouts.adm.navigation')

@section('main')

{{-- <div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0">Otros tiempos</h1>
</div> --}}

<div class="card shadow mb-4 pt-0 border-0">
    <!-- Card Header - Dropdown -->
    <div class="card-header py-2 d-flex flex-row align-items-center justify-content-between bg-dark">
        <form action="{{ route('adm.othertimes.show') }}" class="d-flex flex-row justify-content-between" method="POST"
            id="form-filter-ot">
            @csrf
            @method('POST')
            {{-- <div class="input-group input-group-sm me-3">
                <span class="input-group-text" id="inputGroup-sizing-sm">Rango</span>
                <input type="date" name="init_date" class="form-control">
                <input type="date" name="end_date" class="form-control">
            </div> --}}
            <div class="input-group input-group-sm">
                <span class="input-group-text" id="inputGroup-sizing-sm">Estado</span>
                <select name="ot_status" id="ot_status" class="form-select" value="{{ session('status') }}">
                    <option value="2" selected>Seleccionar</option>
                    <option value="" {{ session('status')=="1" ? 'selected' : '' }}>Pendiente</option>
                    <option value="1" {{ session('status')=="1" ? 'selected' : '' }}>Aprobadas</option>
                    <option value="0" {{ session('status')=="0" ? 'selected' : '' }}>Rechazadas</option>
                </select>
            </div>
        </form>
    </div>
    <!-- Card Body -->
    <div class="card-body p-0">
        <div class="table-responsive">
            <div class="col-12 px-0 scrollable-container table-othertimes bg-dark rounded-0">
                <table class="table table-dark table-hover" id="ot_table" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Fecha actividad</th>
                            <th>Digitador</th>
                            <th>Solicitado a</th>
                            <th>Descripción</th>
                            <th>Horas</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($otherTimes as $otherTime)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($otherTime->date_time)->format('d/m/Y') }}</td>
                            <td>{{ properNouns(nameAndLastName($otherTime->userId->name, $otherTime->userId->last_name)) }}</td>
                            <td>{{ properNouns(nameAndLastName($otherTime->requestTo->name, $otherTime->requestTo->last_name)) }}</td>
                            <td>{{ $otherTime->description }}</td>
                            <td>{{ $otherTime->time}}</td>
                            <td>
                                @if ($otherTime->approved === null)
                                <span class="badge text-bg-warning">Pendiente</span>
                                @elseif($otherTime->approved === 1)
                                <span class="badge text-bg-success">Aprobada</span>
                                @elseif($otherTime->approved === 0)
                                <span class="badge text-bg-danger">Rechazada</span>
                                @endif
                            </td>
                            <td class="">
                                @if ($otherTime->requestTo->id === $user->id)
                                    @if (!$otherTime->approved)
                                    <button type = "button" class ="btn btn-sm fs-5 fw-light p-0 rounded-pill btn-approve-othertimes-table" title="Aprobar"
                                            data-othertime-id = "{{ $otherTime->id }}"
                                            data-approved = "1"
                                            data-time = "{{ $otherTime->time }}"
                                            data-description = "{{ $otherTime->description }}"
                                            data-user-to = "{{ $otherTime->userId->id }}">
                                        <i class="fa-solid fa-square-check fa-lg"  style="color: #02a500;"></i>
                                    </button>    
                                    @endif

                                    @if ($otherTime->approved || $otherTime->approved === null)
                                    <button type = "button" class ="btn btn-sm fs-5 fw-light p-0 rounded-pill btn-approve-othertimes-table" title="Rechazar"
                                        data-othertime-id = "{{ $otherTime->id }}"
                                        data-approved = "0" 
                                        data-time = "{{ $otherTime->time }}" 
                                        data-description = "{{ $otherTime->description }}" 
                                        data-user-to = "{{ $otherTime->userId->id }}">
                                        <i class="fa-solid fa-square-xmark fa-lg" style="color: #da0202;"></i>
                                    </button>
                                    @endif
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection