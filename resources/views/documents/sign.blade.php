@extends(Auth::user()->environment_id != 0 && !Auth::user()->is_admin ? 'layouts.members.nav' :
(Auth::user()->environment_id != 0 && Auth::user()->is_admin ? 'layouts.env.navigation' : (Auth::user()->environment_id
== 0 && Auth::user()->is_admin ? 'layouts.adm.navigation' : 'layouts.dig.navigation')))

@section('main')
    <style>
        /* Estilos propios de la vista "Firmar documento". No tocan resources/css/shared/*
           para no afectar otras vistas; usan tokens --shell-* con fallback cuando existen
           (layout env) y colores neutros razonables en el resto de layouts. */
        .doc-sign-section {
            color: var(--shell-text, inherit);
        }

        .doc-sign-title {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: var(--shell-text, inherit);
        }

        .doc-sign-row {
            background: var(--shell-surface, #fff);
            border: 1px solid var(--shell-border, #dee2e6);
            border-radius: 10px;
            padding: 1rem;
            margin: 0;
        }

        .doc-sign-pdf-col {
            min-width: 0;
        }

        .doc-sign-pdf-name {
            color: var(--shell-text, inherit);
            min-width: 0;
        }

        /* El contenedor donde firmas.js inyecta el <canvas> dinámicamente.
           No se cambia su clase/id, solo su apariencia visual. */
        .doc-sign-canvas-wrap {
            background: var(--shell-surface-2, #f8f9fa);
            border: 1px solid var(--shell-border, #dee2e6);
            border-radius: 8px;
            width: 100%;
            overflow-x: auto;
            position: relative;
        }

        .doc-sign-canvas-wrap canvas {
            max-width: 100%;
            display: block;
            touch-action: none;
        }

        .doc-sign-hint {
            position: absolute;
            top: 12px;
            left: 12px;
            right: 12px;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            background: var(--shell-surface, rgba(255, 255, 255, .95));
            border: 1px solid var(--shell-border-strong, #ced4da);
            color: var(--shell-text, #212529);
            border-radius: 8px;
            padding: 0.5rem 0.85rem;
            font-size: 0.85rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, .15);
            pointer-events: none;
            transition: opacity .2s ease;
        }

        .doc-sign-hint i {
            color: var(--shell-red, #c0392b);
        }

        .doc-sign-hint.d-none {
            display: none;
        }

        .doc-sign-size-row {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .doc-sign-size-row input[type="range"] {
            flex: 1;
        }

        .doc-sign-size-value {
            font-family: var(--shell-mono, monospace);
            font-size: 0.78rem;
            color: var(--shell-muted, #6b7280);
            background: var(--shell-surface-2, #f1f3f5);
            border: 1px solid var(--shell-border, #dee2e6);
            border-radius: 6px;
            padding: 0.2rem 0.5rem;
            min-width: 56px;
            text-align: center;
            flex-shrink: 0;
        }

        .doc-sign-actions {
            display: flex;
            gap: 0.5rem;
            margin-top: 0.75rem;
        }

        .doc-sign-actions .btn {
            flex: 1;
            font-size: 0.82rem;
        }

        .doc-sign-options {
            background: var(--shell-surface, #fff);
            border: 1px solid var(--shell-border, #dee2e6);
            border-radius: 8px;
            padding: 1.25rem;
            margin-top: 0;
        }

        .doc-sign-options h5 {
            color: var(--shell-red, #c0392b);
            font-weight: 700;
            text-align: center;
            margin-bottom: 0.75rem;
        }

        .doc-sign-options hr {
            border-top: 1px solid var(--shell-border-strong, #ced4da);
            margin: 0.75rem 0;
        }

        .doc-sign-options .form-label {
            color: var(--shell-text, inherit);
            font-weight: 600;
            margin-bottom: 0.35rem;
        }

        /* La columna de opciones SOLO debe quedar fija en pantallas >=lg;
           en mobile debe fluir normalmente debajo del PDF. */
        @media (min-width: 992px) {
            .doc-sign-options-col {
                position: sticky;
                top: 20px;
                align-self: flex-start;
                max-height: calc(100vh - 40px);
                overflow-y: auto;
            }
        }

        @media (max-width: 991.98px) {
            .doc-sign-options-col {
                position: static;
                margin-top: 1rem;
            }
        }

        @media (max-width: 575.98px) {
            .doc-sign-row {
                padding: 0.5rem;
            }

            .doc-sign-options {
                padding: 1rem;
            }
        }
    </style>

    <section class="signature-main-container doc-sign-section">
        <div>
            <h1 class="doc-sign-title">Firmar documento</h1>
        </div>

        <!-- Contenedor del PDF -->
        <div class="row doc-sign-row">

            <div class="col-12 col-lg-8 pdf_print doc-sign-pdf-col">
                <!-- Controles de navegación -->
                <div class="pdf-navigation mb-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div class="d-flex align-items-center">
                        <button type="button" id="prev-page_firma" class="btn btn-secondary btn-sm me-2">
                            <i class="fa-solid fa-caret-left"></i>
                        </button>
                        <button type="button" id="next-page_firma" class="btn btn-secondary btn-sm">
                            <i class="fa-solid fa-caret-right"></i>
                        </button>
                        <span class="badge bg-primary text-white ms-2" id="page-info"></span>
                    </div>
                    <div class="fw-bold text-truncate doc-sign-pdf-name">
                        {{ getFileNameAct($document->name, $document->document_mes) }}
                    </div>
                </div>

                <div class="canvas-container scrollable-content doc-sign-canvas-wrap">
                    <canvas id="pdf-canvas_firma" style="border: 1px solid #ddd;"></canvas>
                    <div class="doc-sign-hint" id="signature-hint">
                        <i class="fa-solid fa-circle-info"></i>
                        <span>Cargue su firma y luego arrástrela o tome una de las esquinas para cambiar su tamaño.</span>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-4 fixed-column signature-options doc-sign-options doc-sign-options-col mt-3 mt-lg-0">
                <h5>Opciones de firma</h5>
                <hr>
                <div class="row">
                    <div class="col-12 mt-3 px-0">
                        <label for="signature-image" class="form-label">Cargar Firma (PNG)</label>
                        <input type="file" id="signature-image" class="form-control" accept="image/png">
                    </div>
                    <div class="col-12 px-0 mt-4">
                        <label for="signature-size" class="form-label">Tamaño de la firma</label>
                        <div class="doc-sign-size-row">
                            <input type="range" id="signature-size" class="form-range" min="20" max="1000" value="60" disabled>
                            <span class="doc-sign-size-value" id="signature-size-value">60 px</span>
                        </div>
                    </div>
                </div>

                <div class="doc-sign-actions">
                    <button type="button" class="btn btn-outline-secondary" id="center-signature" disabled>
                        <i class="fa-solid fa-arrows-to-dot me-1"></i> Centrar
                    </button>
                    <button type="button" class="btn btn-outline-danger" id="remove-signature" disabled>
                        <i class="fa-solid fa-trash me-1"></i> Quitar
                    </button>
                </div>

                <button type="button" class="btn btn-primary mt-3 w-100" id="save-signed-pdf">Guardar</button>
            </div>
        </div>
    </section>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.10.377/pdf.min.js"></script>
    <script src="https://mozilla.github.io/pdf.js/build/pdf.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.6.347/pdf.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf-lib/1.17.1/pdf-lib.min.js"></script>

    <script>
        const URL_TEMP = @json($url);
        const DOCUMENT_URL = `/storage/${URL_TEMP}`;
        const DOCUMENT_ID = {{$document->id}}
       
    </script>

    @vite([
        'resources/js/documents/firmas.js'
    ])
@endsection