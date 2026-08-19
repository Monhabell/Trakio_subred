@extends('layouts.adm.navigation')

@section('main')
<div class="align-items-center mb-4">
    <h1>Editar ficha en productividad</h1>
</div>

<form action="{{ route('productivity.update', $productivity->id) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="container-fluid px-0">
        <div class="row">
            <div class="col-12 col-md-3 form-item-productivity-edit">
                <h5>Ficha</h5>
                <span class="rounded-pill px-3">{{ $productivity->receptions->file_number }} </span>
            </div>

            <div class="col-12 col-md-3 form-item-productivity-edit">
                <h5>Formato</h5>
                <span class="rounded-pill px-3">{{ $productivity->base->name }}</span>
            </div>

            <div class="col-12 col-md-3 form-item-productivity-edit">
                <h5>Digitado por</h5>
                <span class="rounded-pill px-3">{{ properNouns(nameAndLastname($productivity->user->name, $productivity->user->last_name)) }}</span>
            </div>

            <div class="col-12 col-md-3 form-item-productivity-edit">
                <h5>Usuarios / seguimientos</h5>
                <span class="rounded-pill px-3">{{ $productivity->quantity_users}}</span>
            </div>
        </div>

        <div class="row">
            <div class="form-floating col-12 my-3 px-0">
                <textarea class="form-control" name="observations" id="observations" placeholder="Observaciones" id="floatingTextarea2" style="height: 100px">{{ $productivity->observations }}</textarea>
                <label for="observations" class="fw-bold">Observaciones</label>
            </div>
        </div>

        <div class="row">
            <div class="col-12 d-flex justify-content-between px-0">
                <a href="{{ route('adm.traceability') }}" class="btn"><i class="fa-solid fa-arrow-left me-2"></i> Volver a trazabilidad</a>
                <button type="submit" class="btn btn-primary btn-loader">Guardar</button>
            </div>
        </div>
    </div>
</form>
@endsection
