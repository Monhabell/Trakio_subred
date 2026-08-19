@extends('layouts/env/navigation')

@section('main')

<div class="env-page-header">
    <div>
        <span class="env-page-tag">ENTORNO / RECEPCIÓN</span>
        <h1 class="env-page-title">Entregar a GESI</h1>
        <p class="env-page-subtitle">Cargue el archivo CSV de fichas para entregar a digitación.</p>
    </div>
    <a href="{{ route('env.downloadExcel') }}" class="env-btn env-btn-ghost">
        <i class="fas fa-download"></i> Descargar excel para pre-cargue
    </a>
</div>

<div class="env-card mb-4">
    <form method="POST" action="{{ route('env.upload.reception', $user) }}" enctype="multipart/form-data"
        class="env-upload-form d-flex flex-wrap gap-2">
        @csrf

        <button type="button" class="container-btn-file">
            <svg fill="#fff" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 50 50">
                <path d="M28.8125 .03125L.8125 5.34375C.339844
                        5.433594 0 5.863281 0 6.34375L0 43.65625C0
                        44.136719 .339844 44.566406 .8125 44.65625L28.8125
                        49.96875C28.875 49.980469 28.9375 50 29 50C29.230469
                        50 29.445313 49.929688 29.625 49.78125C29.855469 49.589844
                        30 49.296875 30 49L30 1C30 .703125 29.855469 .410156 29.625
                        .21875C29.394531 .0273438 29.105469 -.0234375 28.8125 .03125ZM32
                        6L32 13L34 13L34 15L32 15L32 20L34 20L34 22L32 22L32 27L34 27L34
                        29L32 29L32 35L34 35L34 37L32 37L32 44L47 44C48.101563 44 49
                        43.101563 49 42L49 8C49 6.898438 48.101563 6 47 6ZM36 13L44
                        13L44 15L36 15ZM6.6875 15.6875L11.8125 15.6875L14.5 21.28125C14.710938
                        21.722656 14.898438 22.265625 15.0625 22.875L15.09375 22.875C15.199219
                        22.511719 15.402344 21.941406 15.6875 21.21875L18.65625 15.6875L23.34375
                        15.6875L17.75 24.9375L23.5 34.375L18.53125 34.375L15.28125
                        28.28125C15.160156 28.054688 15.035156 27.636719 14.90625
                        27.03125L14.875 27.03125C14.8125 27.316406 14.664063 27.761719
                        14.4375 28.34375L11.1875 34.375L6.1875 34.375L12.15625 25.03125ZM36
                        20L44 20L44 22L36 22ZM36 27L44 27L44 29L36 29ZM36 35L44 35L44 37L36 37Z"></path>
            </svg>
            Seleccionar archivo
            <input class="file" type="file" name="file" id="fileInput" />
        </button>
        <button type="submit" class="env-btn env-btn-primary" id="btn_upload">
            <i class="fas fa-upload"></i> Subir archivo <span data-file="name"></span>
        </button>
    </form>
    <small class="text-muted fst-italic d-block mt-2">
        <i class="fas fa-info-circle me-1"></i> Por favor cargue el archivo en formato .csv (delimitado por comas)
    </small>
</div>

    @if ($errors->any())
    <!-- Modal -->
    <div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-danger">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="errorModalLabel">
                        <i class="fa-solid fa-circle-xmark me-2"></i> Errores en el archivo
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <p>Se encontraron los siguientes problemas en el CSV:</p>
                    <ul class="list-group">
                        @foreach ($errors->all() as $error)
                        <li class="list-group-item">
                            {{ $error }}
                        </li>
                        @endforeach
                    </ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Script para abrir el modal automáticamente -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
            errorModal.show();
        });
    </script>
    @endif

    @if ($receptions_quantity > 0)
    <div class="env-card mt-3">
        <div class="env-card-head">
            <h2 class="env-card-title">
                <span class="env-badge env-badge-red">{{ $receptions_quantity }}</span>
                Actualizaciones pendientes por entregar
            </h2>
        </div>

        <div class="env-table-wrap">
            <table class="env-table" id="tableReceptions" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>No. ficha</th>
                        <th>Formato</th>
                        <th>Localidad</th>
                        <th>Cantidad</th>
                        <th>Fecha cargue</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($receptions as $reception)
                    <tr>
                        <td>{{ $reception->file_number }}</td>
                        <td>{{ $reception->bases->name }}</td>
                        <td>{{ $reception->localidad->name }}</td>
                        <td>{{ $reception->quantity }}</td>
                        <td>{{ dateFormatN($reception->created_at) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

@endsection