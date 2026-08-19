@extends('layouts/env/navigation')

@section('main')

<div class="env-page-header">
    <div>
        <span class="env-page-tag">Consecutivos</span>
        <h1 class="env-page-title">Editar fichas</h1>
    </div>
</div>

<form action={{route('reviewconsecutives.update')}} method="POST" class="d-flex flex-column gap-3">
    @csrf

    <div class="d-flex justify-content-end gap-2 mb-2">
        <a href="{{ url()->previous() }}" class="env-btn env-btn-ghost btn-sm">
            <i class="fas fa-arrow-left me-1"></i>Volver
        </a>
        <button type="submit" class="env-btn env-btn-primary btn-sm btn-loader">
            <i class="fas fa-save me-1"></i>Guardar
        </button>
    </div>

    @foreach($files as $index => $file)
    <div class="env-card">
        <div class="ficha-item">
            <div class="ficha-header">
                <div class="d-flex flex-column">
                    <div class="ficha-number">
                        <i class="fas fa-hashtag me-1 text-danger"></i>
                        <input type="text" name="file_ids[]" value="{{ $file->id }}" hidden>
                        <input type="text" id="file-number-input-{{ $file->id }}" name="file_numbers[]" value="{{ $file->file_number }}" hidden>
                        <span id="file-number-{{ $file->id }}">{{ $file->file_number }}</span>
                        <span class="status-badge {{
                                $file->status == 10 ? 'status-rejected' :
                                ($file->status == 1 ? 'status-pending' :
                                ($file->status == 2 ? 'status-sealed' :
                                ($file->status == 3 ? 'status-delivered' :
                                ($file->status == 4 ? 'status-typed' :
                                ($file->status == 5 ? 'status-returned' :
                                ($file->status == 6 ? 'status-professional' :
                                ($file->status == 7 ? 'status-facilitator' :
                                'status-unknown')))))))
                            }} ficha-status">
                            {{ getStatusNameFile($file->status) }}
                        </span>
                    </div>

                    <span>
                        {{$file->environment_file->entorno}} - {{ properNouns($file->bases->name) ?? '' }}
                        <span class="text-muted small">{{ $file->interventionType ? '- '.$file->interventionType->name :
                            ''
                            }}</span>
                    </span>
                </div>

                <div class="d-flex flex-column align-items-end">
                    @foreach($file->users as $prof)
                    <span class="text-light">
                        {{ properNouns(nameAndLastName($prof->name, $prof->last_name)) ?? 'Sin asignar' }}
                        <i class="fas fa-user-tie me-1 text-danger"></i>
                    </span>
                    @endforeach
                    <div>
                        <span class="text-muted">
                            {{ $file->localidad->name ?? 'N/A' }}
                        </span>
                        <i class="fas fa-map-marker-alt me-1 text-danger"></i>
                    </div>
                </div>
            </div>

            <div class="row g-3 mt-1">
                <div class="col-12 col-md-4 mt-0">
                    <label for="localidad_id" class="form-label text-muted fst-italic">Localidad</label>
                    <select id="localidad_id" name="localidad_id[]"
                        class="form-select form-select-sm subtle-focus input-edit-localidad"
                        data-file-id="{{ $file->id }}">
                        @foreach($localidades as $localidad)
                        <option
                            value="{{ $localidad->id}}" @selected(intval($localidad->id) == $file->localidad_id)
                        >
                            {{ $localidad->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 col-md-4 mt-0">
                    <label for="fecha_intervencion" class="form-label text-muted fst-italic">Fecha de
                        intervención</label>
                    <input type="date" id="fecha_intervencion" name="fecha_intervencion[]"
                        class="form-control form-control-sm subtle-focus input-edit"
                        value="{{ $file->fecha_intervencion }}">
                </div>

                <div class="col-12 col-md-4 mt-0">
                    <label for="format" class="form-label text-muted fst-italic">Formato</label>
                    <select id="format_id" name="format[]"
                        class="form-select form-select-sm subtle-focus input-edit">
                        @foreach($formats as $format)
                        <option value="{{ $format->id }}" @selected($format->id == $file->format_id)>
                            {{ $format->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Fechas --}}
            <div class="ficha-dates mt-2 text-muted small">
                @if($file->required_date)
                <div><i class="far fa-calendar-alt me-1 text-danger"></i><strong>Solicitado el </strong>
                    {{ \Carbon\Carbon::parse($file->required_date)->translatedFormat('j \\d\\e F \\d\\e Y') }}
                </div>
                @endif

                @if($file->fecha_intervencion)
                <div><i class="far fa-calendar-alt me-1 text-danger"></i><strong>Fecha intervención:</strong>
                    {{ \Carbon\Carbon::parse($file->fecha_intervencion)->translatedFormat('j \\d\\e F \\d\\e Y') }}
                </div>
                @endif
            </div>
        </div>
    </div>
    @endforeach
</form>

@vite([
'resources/js/env/review-files-edit.js',
])
@endsection