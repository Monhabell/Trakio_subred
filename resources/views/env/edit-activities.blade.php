@extends('layouts.env.navigation')

@section('main')
    <style>
        @media (max-width: 576px) {
            #editProfiles .input-group {
                flex-wrap: wrap;
            }
        }
    </style>

    <div class="env-page-header">
        <div>
            <span class="env-page-tag">ENTORNO / PERFILES</span>
            <h1 class="env-page-title">Editar perfiles</h1>
            <p class="env-page-subtitle">En este apartado podrá modificar las obligaciones específicas y honorarios para cada uno de los perfiles asociados a su entorno.</p>
        </div>
    </div>

    <!-- Editar perfiles -->
    <div id="editProfiles">
        @if ($errors->any())
            <div class="alert alert-danger mb-3">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Obtener perfiles por cada entorno --}}
        <form class="env-card" action="{{ route('activities.save') }}" id="form-activities" method="POST">
            @csrf
            <div class="row g-3">
                <div class="col-12 col-md-4">
                    <div class="input-group">
                        <span class="input-group-text">Perfil</span>
                        <select class="form-select" name="role_id" id="role_id" required>
                            <option value="">Seleccionar</option>
                            @foreach ($roles as $role)
                                <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                    {{ $role->name }}
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
                        <input type="number" class="form-control" id="total_salary" name="total_salary" required>
                    </div>
                </div>

                <div class="col-12 col-md-1 d-flex flex-row flex-center">
                    <button type="submit" title="Guardar" class="btn btn-danger w-100 btn-loader">
                        <i class="fa-solid fa-floppy-disk"></i>
                    </button>
                </div>
            </div>

            <div class="row my-3" id="container-add-activities" hidden>
                <div class="d-flex flex-wrap align-items-center">
                    <button type="button" title="Agregar obligaciones específicas" class="btn btn-obligations"
                        id="btn-obligations">
                        <i class="fas fa-plus me-2"></i> Agregar obligaciones específicas
                    </button>
                    <div id="container-number-obligations" class="d-flex flex-row">
                        <div class="position-relative">
                            <input type="number" class="form-control" id="number-obligations" placeholder="¿Cuántas?">
                            <button class="btn btn-sm" id="btn-number-obligations">
                                <i class="fa-solid fa-paper-plane" style="color: #640000;"></i>
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
                    <img src="{{ asset('img/icons/arrow_curve.png') }}" class="img_arrow mb-5 d-none d-md-block" alt="Arrow">
                    <span class="fs-6 mt-5 text-warning bg-transparent border-0">Seleccione un perfil para
                        editarlo</span>
                </div>
            </div>
        </form>
    </div>
@endsection
