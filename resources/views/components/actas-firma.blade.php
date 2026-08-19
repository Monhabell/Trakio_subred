@if ($pendienteFirma)
    <style>
        .container-alert {
            background: rgba(0, 255, 0, 0.1);
            border: 1px solid var(--trakio-green);
            color: var(--trakio-green);
            padding: 15px;
            border-radius: 8px;
        }
    </style>

    <div class="modal fade" id="modalFirmaObligatoria" data-backdrop="static" data-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="staticBackdropLabel">
                        {{-- <i class="fas fa-exclamation-triangle mr-2"></i>  --}}
                        ACCIÓN REQUERIDA: Tiene {{ $count }} acta(s) pendiente(s) de firma
                    </h5>
                </div>
                <div class="modal-body text-center p-5">
                    <div class="mb-4">
                        <i class="fas fa-file-signature text-danger" style="font-size: 5rem;"></i>
                    </div>
                    <h2 class="font-weight-bold">¡Hola, {{ Auth::user()->name }}!</h2>
                    <p class="lead">
                        Se ha generado un acta semanal que requiere tu firma para poder continuar con el proceso
                        administrativo.
                        <strong>El acceso al resto de la plataforma estará restringido hasta que completes esta firma.</strong>
                    </p>
                    <div class="container-alert mt-4">
                        <strong>Detalles del Documento:</strong><br>
                        Periodo: {{ $pendienteFirma->document->document_mes }} /
                        {{ $pendienteFirma->document->file_year }}
                    </div>
                </div>
                <div class="modal-footer justify-content-center bg-secondary">
                    <a href="{{ route('document.sign', ['document' => $pendienteFirma->document->id]) }}"
                        class="btn btn-danger btn-lg px-5 shadow">
                        Firmar acta <i class="fas fa-chevron-right ml-2"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            $('#modalFirmaObligatoria').modal('show');
        });
    </script>


    <style>
        body.modal-open {
            overflow: hidden !important;
            /* Evita que hagan scroll hacia abajo */
        }

        body.modal-open {
            overflow: hidden !important;
        }

        body.modal-open *:not(.modal):not(.modal *) {
            pointer-events: none !important;
        }
    </style>
@endif
