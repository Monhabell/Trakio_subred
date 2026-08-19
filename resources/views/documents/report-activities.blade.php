@extends(Auth::user()->is_admin ? 'layouts.env.navigation' : 'layouts.members.nav')

@section('main')
<style>
    /* Estilos propios de "Generar informe de actividades". No se tocan
       resources/css/shared/* (form-report.css ya define .select/.selected/.options/.option,
       .activities-* y #no-specific-activities-message para TODOS los layouts, por lo que
       se conservan tal cual). Aquí solo se ajusta el envoltorio del formulario y el
       responsive de columnas, usando tokens --shell-* con fallback. */
    .report-act-card {
        background: var(--shell-surface, #1f1f1f);
        border: 1px solid var(--shell-border, rgba(255, 255, 255, 0.08));
        border-radius: 10px;
        padding: 0;
        color: var(--shell-text, #e9ecef);
    }

    @media (min-width: 768px) {
        .report-act-card {
            padding: 1rem;
        }
    }

    @media (max-width: 575.98px) {
        .report-act-card {
            border-radius: 8px;
        }
    }

    /* Evitar overflow horizontal en mobile con textos largos dentro de los grupos. */
    .activities-input-group-text {
        word-break: break-word;
    }
</style>

<div class="d-sm-flex align-items-center justify-content-between mb-1 mb-md-2">
    <div>
        <h1 class="mb-0">{{ $edit_template ? 'Editar '.$loaded_template[0]->template->name : 'Generar informe de
            actividades'}}</h1>
    </div>

    @if ($errors->any())
    <div class="alert alert-danger mb-0 py-auto">
        <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif
</div>

@if ($templates)
<div class="template-list pb-2">
    @foreach ($templates as $template)
    <div class="select">
        <div class="selected {{ request('template') == $template->id ? 'active' : '' }}">
            <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 512 512" class="arrow">
                <path
                    d="M233.4 406.6c12.5 12.5 32.8 12.5 45.3 0l192-192c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L256 338.7 86.6 169.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3l192 192z">
                </path>
            </svg>
            <span>{{ $template->name }}</span>
        </div>
        <div class="options w-100">
            <form action="{{ route('report.activities.show', ['template' => $template->id]) }}" method="POST">
                @csrf
                <input class="option text-start" type="submit" value="Usar" />
            </form>
            <form action="{{ route('report.activities.edit', ['template' => $template->id]) }}" method="POST">
                @csrf
                <input class="option text-start" type="submit" value="Editar" />
            </form>
            <form action="{{ route('report.activities.delete', ['template' => $template->id]) }}" method="POST" id="form-template-delete">
                @csrf
                @method('delete')
                <input class="option drop text-start" type="submit" value="Eliminar"/>
            </form>
        </div>
    </div>
    @endforeach
</div>
@endif

<form class="report-act-card p-0 p-md-3" action="{{ route('activities.actions') }}" id="data-form-activities"
    method="POST">
    @csrf
    <div class="activities-container {{ $edit_template ? 'hidden' : ''}}">
        <div class="row mb-2 mb-md-0">
            <h4 class="activities-header px-0">Por favor verifique sus datos antes de continuar</h4>
           
        </div>
        <p class="activities-info-text">Tenga en cuenta que antes de generar el informe de actividades debe cargar la
            planilla de pago a SGSS correspondiente al mes inmediatamente anterior a certificar.</p>
        <input type="text" name="type_action" id="type_action" hidden>
        <input type="number" name="template_id" value="{{ $loaded_template[0]->template->id ?? '' }}" hidden>

        <div class="activities-grid container-fluid">
            <div class="activities-input-group row">
                <div class="col-12 col-lg-4">
                    <span class="activities-input-group-text">Nombre del contratista</span>
                </div>
                <div class="col-12 col-lg-8">
                    <input type="text" class="activities-input activities-input-readonly w-100"
                        value="{{ $user->name }} {{ $user->last_name }}" name="person_name" readonly required>
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

                <div class="col-12 col-lg-4">
                    <input type="number" class="activities-input activities-input-readonly w-100" name="total_fee"
                        id="total_fee"
                        value="{{ $user->role->total_fee + getAdicionalHours($total_overtime, $user->role->hour_value) }}"
                        required>
                </div>

                <div class="col-12 col-lg-6">
                    <input type="text" name="total_fee_string" id="total_fee_string" value="" readonly required hidden>
                    <span class="activities-input-group-text ms-2">correspondiente a
                        {{ getHoursMonth($total_overtime) }} horas
                        @if (count($total_overtime) > 0)
                        @if ($total_overtime[0]->total_over_times > 0)
                        <span class="text-danger">, más <b class="text-white">{{ $total_overtime[0]->total_over_times
                                }}</b> hora(s)
                            extra(s) para un total de <b class="text-white">{{ getHoursMonth($total_overtime) +
                                $total_overtime[0]->total_over_times }}</b>
                            hora(s)
                            @endif
                            @endif
                        </span>
                </div>
            </div>

            <div class="activities-input-group activities-input-wide row">
                <div class="col-12 col-lg-2">
                    <span class="activities-input-group-text">Periodo certificado</span>
                </div>

                <div class="col-12 col-lg-4">
                    <input type="date" class="activities-input activities-input-editable w-100" name="init_date"
                        value="{{ old('init_date') }}">
                </div>
                <div class="col-12 col-lg-4 mt-1 mt-md-0">
                    <input type="date" class="activities-input activities-input-editable w-100" name="end_date"
                        value="{{ old('end_date') }}" >
                </div>
            </div>
        </div>
    </div>

    @if (count($activities) > 0)
    <div class="container-activities">
        <div class="row d-none d-lg-flex header-container">
            <div class="col-6 text-center header-section">
                <h6 class="header-title">OBLIGACIONES ESPECÍFICAS</h6>
            </div>
            <div class="col-6 text-center header-section">
                <h6 class="header-title">ACTIVIDADES REALIZADAS</h6>
            </div>
        </div>

        @if (count($loaded_template) > count($activities))
        <span class="ms-3 text-warning flex-center bg-transparent border-0">
            <i class="fa-solid fa-exclamation-triangle me-2"></i>
            La cantidad de actividades guardadas en la plantilla "{{ $loaded_template[0]->template->name }}"
            ({{ count($loaded_template) }}) es mayor al número de actividades cargadas por el entorno
            ({{ count($activities) }})
        </span>
        @endif


        <div class="row d-block d-lg-none header-container">
            <div class="col-12 text-center header-section">
                <h6 class="header-title">OBLIGACIONES ESPECÍFICAS Y ACTIVIDADES REALIZADAS</h6>
            </div>
        </div>

        @if ($edit_template)
        @foreach ($loaded_template as $key => $activityTemplate)
        <div class="row text-center activity-row">
            <div class="col-12 col-lg-6 px-2 pt-2 pb-0 pt-lg-3 py-lg-3">
                <textarea class="textarea-activities resize-textarea" rows="4" name="text_specific_activities[]"
                    readonly>@if ($activities){{ $key+1 }}. {{ old('text_specific_activities.' . $key, $activities[$key]->description ?? '') }}@endif</textarea>
            </div>
            <div class="col-12 col-lg-6 p-2 pb-lg-0 pt-lg-3 py-lg-3">
                <textarea class="form-control editable resize-textarea" rows="4" name="text_activities_done[]" required>{{ old('text_activities_done.' . $key, $activityTemplate->description ?? '') }}</textarea>
            </div>
        </div>
        @endforeach
        @else
        @foreach ($activities as $key => $activity)
        <div class="row text-center activity-row">
            <div class="col-12 col-lg-6 px-2 pt-2 pb-0 pt-lg-3 py-lg-3">
                <textarea class="textarea-activities resize-textarea" rows="4" name="text_specific_activities[]"
                    readonly>
                            {{ $activity->number_activity }}. {{ $activity->description }}
                        </textarea>
            </div>
            <div class="col-12 col-lg-6 p-2 pb-lg-0 pt-lg-3 py-lg-3">
                <textarea class="form-control editable resize-textarea" rows="4" name="text_activities_done[]"
                    required>{{ old('text_activities_done.' . $key, $loaded_template[$key]->description ?? '') }}</textarea>
            </div>
        </div>
        @endforeach
        @endif
        
        <div class="row d-flex flex-center">
            <div class="col-12 col-lg-3 pt-3">
                @if (!$edit_template)
                <button type="button" class="btn activities-btn-generate w-100" data-bs-toggle="modal"
                    data-bs-target="#modalNameTemplate">
                    <i class="fas fa-save icon-left"></i> Guardar nueva plantilla
                </button>
                @endif

                @if ($edit_template)
                <button type="submit" class="btn activities-btn-save w-100" value="update-template">
                    <i class="fas fa-save icon-left"></i> Guardar cambios
                </button>
                @endif
            </div>

            <div class="col-12 col-lg-3 pt-3">
                @if (!$edit_template)
                <button type="submit" class="btn activities-btn-save w-100" value="generate-pdf">
                    <i class="fas fa-file-alt icon-left"></i> Generar informe
                </button>
                @endif

                @if ($edit_template)
                <a type="button" class="btn activities-btn-generate w-100" href="{{ route('report.activities.show') }}">
                    <i class="fa-solid fa-circle-xmark me-2"></i> Cancelar
                </a>
                @endif
            </div>

            <div class="modal fade" id="modalNameTemplate" aria-hidden="true" aria-labelledby="modalNameTemplate"
                tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content shadow-modal">
                        <div class="modal-body bg-dark text-light p-4 rounded-3">
                            <h5 class="modal-title text-highlight mb-3">Guardar plantilla</h5>
                            <div class="input-group input-group-sm mb-3">
                                <input type="text" class="form-control shadow-input" name="template_name"
                                    placeholder="Ingrese el nombre de la plantilla">
                            </div>
                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-sm btn-save px-4"
                                    value="save-template">Guardar</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @else
    <div class="col-12 text-center py-4" id="no-specific-activities-message">
        <div class="alert alert-dark d-flex flex-column align-items-center justify-content-center border border-danger"
            role="alert">
            <i class="fas fa-exclamation-triangle mb-3 text-danger" style="font-size: 2rem;"></i>
            <span class="fs-5 fw-bold text-light mb-2 border-0 bg-transparent">No hay actividades específicas
                cargadas.</span>
            <p class="text-light">
                Por favor comuníquese con el administrador de su entorno
                para que cargue sus actividades específicas.
            </p>
        </div>
    </div>
    @endif
</form>

{{-- <x-modal title="Mi Modal" 
    name='Alerta' 
    icons='alert'
    {{-- show={{ !$has_insurance }} 
    btnClass='nav-link-files'
    :href="route('documents.index')"
    :function="['data' => 'data-to-container', 'value' => 'collapsePlanilla']"
    >
        <div class="px-0">
            <i class="fa-regular fa-circle-dot fa-beat-fade fa-2xl " style="color: #b30000;"></i>
            <span class="ml-3"> No ha realizado el cargue de la planilla, para generar el informe correspondiente al mes de {{getCurrentTextMonth() }}</span>
    </div>
</x-modal> --}}
@endsection