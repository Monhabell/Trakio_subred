@extends('layouts.members.nav')

@section('main')
    <div class="parent">
        <div class="table-consecutivos">
            <div class="section-header">
                <h2 class="section-title">
                    <i class="fas fa-list"></i>
                    Consecutivos
                </h2>
            </div>

            <div class="search-container">
                <div class="search-box">
                    <div class="search-input-wrapper">
                        <i class="fas fa-search"></i>
                        <input type="text" class="form-input search-input" id="searchInput"
                            placeholder="Buscar por localidad, formato, estado...">
                    </div>
                    <button type="button" class="btn btn-primary mem-search-btn" id="btnSearch">
                        Buscar
                    </button>
                </div>
            </div>

            <div class="consecutivos-container" id="consecutivos-container">
                <!-- Los consecutivos se cargarán aquí dinámicamente -->
            </div>
        </div>

        <div class="form-consecutivos">
            <div class="form-container">
                <div class="form-header">
                    <h3 class="form-title">Solicitar consecutivos</h3>
                    <p class="form-subtitle">Complete los datos para generar nuevos consecutivos</p>
                </div>

                <form action="{{ route('consecutivos') }}" method="POST" class="inline-form" id="formConsecutivos">
                    @csrf

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="localidad">
                                <i class="fas fa-map-marker-alt"></i> Localidad
                            </label>
                            <select name="localidad" id="localidad" class="form-select-consecutivos" required>
                                <option value="">Seleccione una localidad</option>
                                @foreach ($localidades as $localidad)
                                    <option value="{{ $localidad->identificador }}">{{ $localidad->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="Entorno">
                                <i class="fa-solid fa-puzzle-piece"></i> Entorno
                            </label>
                            <select name="id_entorno" id="entorno" class="form-select-consecutivos" required>
                                <option value="">Seleccione un entorno</option>
                                @foreach ($entornos as $entorno)
                                    <option value="{{ $entorno->id }}">{{ $entorno->entorno }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="formato">
                                <i class="fas fa-file-alt"></i> Formato
                            </label>
                            <select name="id_formato" id="formato" class="form-select-consecutivos" required>
                                <option value="">Seleccione un formato</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="fechaIntervencion">
                                <i class="fas fa-calendar-alt"></i> Fecha de intervención
                            </label>
                            <input type="date" name="fecha_intervencion" id="fechaIntervencion" class="form-input"
                                required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="cantidad_consecutivos">
                                <i class="fas fa-hashtag"></i> Cantidad
                            </label>
                            <input type="number" name="cantidad_consecutivos" id="cantidad_consecutivos"
                                placeholder="Cantidad de consecutivos" min="1" max="10" class="form-input"
                                required>
                        </div>

                        <div class="form-group" id="intervencion-container" style="display:none;">
                            <label class="form-label" for="intervencion">
                                <i class="fas fa-tasks"></i> Intervención
                            </label>
                            <select name="id_intervencion" id="intervencion" class="form-select-consecutivos">
                                <option value="">Seleccione intervención</option>
                            </select>
                        </div>
                    </div>

                    <button type="submit" class="submit-btn" id="btnSubmit">
                        <i class="fas fa-check"></i> Solicitar consecutivos
                    </button>



                </form>

                <div id="permisoMesAnteriorInfo" class="form-row" style="display:none; margin-top: 12px;">
                    <p class="form-subtitle" id="permisoMesAnteriorTexto"></p>
                </div>

                <div id="permisoMesAnteriorAccion" style="display:none; margin-top: 12px;">
                    <button type="button" class="btn btn-secondary" id="btnMostrarSolicitudPermiso">
                        <i class="fas fa-unlock"></i> ¿Olvidó pedir consecutivos del mes anterior? Solicite permiso
                    </button>

                    <form id="formSolicitarPermiso" method="POST" action="{{ route('consecutivo.permission.store') }}"
                        style="display:none; margin-top: 12px;">
                        @csrf
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label" for="requested_quantity">
                                    <i class="fas fa-hashtag"></i> Cantidad que necesita
                                </label>
                                <input type="number" name="requested_quantity" id="requested_quantity" min="1"
                                    max="20" class="form-input" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="reason">
                                    <i class="fas fa-comment"></i> Motivo (opcional)
                                </label>
                                <input type="text" name="reason" id="reason" maxlength="500" class="form-input">
                            </div>
                        </div>
                        <button type="submit" class="submit-btn">
                            <i class="fas fa-paper-plane"></i> Enviar solicitud
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="pending-consecutivos" hidden>
            <div class="section-header">
                <h2 class="section-title">
                    <i class="fas fa-clock"></i>
                    Solicitudes Pendientes
                </h2>
                <div class="section-actions">
                    <button class="btn btn-secondary" id="refreshBtn">
                        <i class="fas fa-sync-alt"></i> Actualizar
                    </button>
                </div>
            </div>
            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Formato</th>
                            <th>Cantidad</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>#00345</td>
                            <td>Formato X</td>
                            <td>5</td>
                            <td><span class="status-badge status-pending">Pendiente</span></td>
                        </tr>
                        <tr>
                            <td>#00346</td>
                            <td>Formato Y</td>
                            <td>10</td>
                            <td><span class="status-badge status-pending">Pendiente</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @if (session('success') || session('error'))
        <div class="custom-alert-modal show {{ session('success') ? 'success' : 'error' }}">
            <div class="custom-alert-content">
                <div class="custom-alert-icon">
                    <i class="fas fa-{{ session('success') ? 'check-circle' : 'times-circle' }}"></i>
                </div>
                <div class="custom-alert-message">
                    {{ session('success') ?? session('error') }}
                </div>
            </div>
        </div>

        <script>
            // Desaparece suavemente en 3 segundos
            setTimeout(() => {
                const modal = document.querySelector('.custom-alert-modal');
                if (modal) {
                    modal.classList.remove('show');
                    modal.classList.add('hide');
                    setTimeout(() => modal.remove(), 500); // eliminar del DOM después de la animación
                }
            }, 3000);
        </script>
    @endif


    <div class="modal-overlay" id="modalEditarFormato">
        <div class="modal-container">
            <div class="modal-header">
                <h3 class="modal-title">
                    <i class="fa-solid fa-circle-info"></i>
                    Cambiar cantidad de formatos
                </h3>
                <button class="modal-close" id="closeInfoDetailModal">&times;</button>
            </div>

            <form id="formFormato_cant" method="POST" action="{{ route('update_lformat') }}">
                @csrf
                <div class="modal-body">

                    <div class="modal-form-group">
                        <input type="text" class="id_number" id="id_number_format" name="id_number_format" hidden>
                    </div>

                    <div class="modal-form-group">
                        <label for="name_format" class="modal-form-label">Formato</label>
                        <input type="text" class="modal-form-input" id="name_format" name="name_format" readonly>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="cantidad_consecutivos_format">
                            <i class="fas fa-hashtag"></i> Cantidad
                        </label>
                        <input type="number" name="cantidad_consecutivos_format" id="cantidad_consecutivos_format"
                            placeholder="Cantidad de consecutivos" min="1" max="50" class="form-input"
                            required>
                    </div>


                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" id="closeedit">
                        <i class="fa-solid fa-floppy-disk"></i> Actualizar
                    </button>
                    <button type="button" class="btn btn-secondary" id="closeEditFormat">Cerrar</button>
                </div>

            </form>
        </div>
    </div>

    <div class="modal-overlay" id="modalEditarConsecutivo">
        <div class="modal-container">
            <div class="modal-header">
                <h3 class="modal-title">
                    <i class="fa-solid fa-circle-info"></i>
                    Cambiar Localidad
                </h3>
                <button class="modal-close" id="closeInfoDetailModal">&times;</button>
            </div>

            <form id="formFormato_localidad" method="POST" action="{{ route('update_localidad') }}">
                @csrf
                <div class="modal-body">

                    <div class="modal-form-group">
                        <input type="text" class="id_number" id="id_number" name="id_edit" hidden>
                    </div>

                    <div class="modal-form-group">
                        <label class="modal-form-label">
                            <i class="fa-solid fa-hashtag"></i> Ficha
                        </label>
                        <input type="text" class="modal-form-input" id="edit_number" name="edit_number" readonly>
                    </div>

                    <div class="modal-form-group">
                        <label class="form-label" for="localidad_edit">
                            <i class="fas fa-map-marker-alt"></i> Localidad
                        </label>
                        <select name="localidad" id="localidad_edit" class="form-select-consecutivos" required>
                            <option value="">Seleccione una localidad</option>
                            @foreach ($localidades as $localidad)
                                <option value="{{ $localidad->id }}">{{ $localidad->name }}</option>
                            @endforeach
                        </select>
                    </div>


                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" id="closeedit">
                        <i class="fa-solid fa-floppy-disk"></i> Actualizar
                    </button>
                    <button type="button" class="btn btn-secondary" id="closeEdit">Cerrar</button>
                </div>

            </form>
        </div>
    </div>

    <!-- Modal de Información del Consecutivo -->
    <div class="modal-overlay" id="infoDetailModal">
        <div class="modal-container">
            <div class="modal-header">
                <h3 class="modal-title">
                    <i class="fa-solid fa-circle-info"></i>
                    Información del Consecutivo
                </h3>
                <button class="modal-close" id="closeInfoDetailModal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="modal-form-group">
                    <label class="modal-form-label">
                        <i class="fa-solid fa-hashtag"></i> Número de Ficha
                    </label>
                    <div class="modal-form-input" id="info_file_number">-</div>
                </div>

                <div class="modal-form-group">
                    <label class="modal-form-label">
                        <i class="fa-solid fa-file-zipper"></i> Formato Base
                    </label>
                    <div class="modal-form-input" id="info_base_name">-</div>
                </div>

                <div class="modal-form-group">
                    <label class="modal-form-label">
                        <i class="fa-solid fa-screwdriver-wrench"></i> Tipo de Intervención
                    </label>
                    <div class="modal-form-input" id="info_intervencion">-</div>
                </div>

                <div class="modal-form-group">
                    <label class="modal-form-label">
                        <i class="fa-solid fa-layer-group"></i> Formatos Adicionales
                    </label>
                    <div id="info_formatos_adicionales" class="modal-form-input">
                        <span class="text-muted">No hay formatos adicionales</span>
                    </div>
                </div>

                <div class="modal-form-group">
                    <label class="modal-form-label">
                        <i class="fa-solid fa-calendar"></i> Fecha de Creación
                    </label>
                    <div class="modal-form-input" id="info_created_at">-</div>
                </div>

                <div class="modal-form-group">
                    <label class="modal-form-label">
                        <i class="fa-solid fa-flag"></i> Estado
                    </label>
                    <div id="info_status">-</div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="closeInfoDetailModalBtn">Cerrar</button>

            </div>
        </div>
    </div>

    <!-- Modal de envío de formato -->
    <div class="modal-overlay" id="formatModal">
        <div class="modal-container">
            <div class="modal-header">
                <h3 class="modal-title">
                    <i class="fa-solid fa-paper-plane"></i>
                    Enviar Formato
                </h3>
                <button class="modal-close" id="closeFormatModal">&times;</button>
            </div>
            <form id="formFormato" method="POST" action="{{ route('save_format_add') }}">
                @csrf
                <div class="modal-body">
                    <input type="number" name="environment" id="environment" hidden>
                    <input type="number" name="localidad_id" id="localidad_id" hidden>
                    <input type="number" name="user_id_profesional" id="user_id_profesional" hidden>
                    <input type="number" name="consecutivo" id="consecutivo" hidden>

                    <div class="modal-form-group">
                        <label class="modal-form-label">
                            <i class="fa-solid fa-hashtag"></i> Número de Ficha
                        </label>
                        <input type="text" class="modal-form-input" id="file_number" name="file_number" readonly>
                    </div>

                    <div class="modal-form-group" hidden>
                        <label class="modal-form-label">
                            <i class="fa-solid fa-file-zipper"></i> Base
                        </label>
                        <input type="text" class="modal-form-input" id="base_name" name="base_name" readonly>
                    </div>

                    <div class="modal-form-group">
                        <label class="modal-form-label">
                            <i class="fa-solid fa-file-zipper"></i> Formato enlazado
                        </label>
                        <select class="form-select-consecutivos" name="formato_nuevo" id="format_select_modal" required>
                            <option value="">Seleccione formato</option>
                        </select>
                    </div>

                    <div class="modal-form-group">
                        <label class="modal-form-label">Fecha intervención</label>
                        <input class="form-input" type="date" name="fecha_intervencion" id="fecha_intervencion"
                            required>
                    </div>

                    <div class="modal-form-group">
                        <label class="modal-form-label">
                            <i class="fa-solid fa-screwdriver-wrench"></i> Cantidad
                        </label>
                        <input type="text" class="modal-form-input" id="cantidad_formato" name="cant_formato_nuevo"
                            min="1" max="30" required>
                    </div>

                    <!-- Campo oculto con el ID real -->
                    <input type="hidden" id="users_format" name="user_ids">


                    <input type="hidden" name="id" id="registro_id">
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="cancelFormatModalBtn">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-floppy-disk"></i> Guardar Formato
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal-overlay" id="formatModalNewUser">
        <div class="modal-container">
            <div class="modal-header p-2">
                <h3 class="modal-title fs-6 fs-md-5">
                    <i class="fa-solid fa-paper-plane"></i>
                    Agregar nuevo profesional
                </h3>
                <button class="modal-close" id="closeFormatModal">&times;</button>
            </div>
            <form id="formFormato_User" method="POST" action="{{ route('save_user_add') }}">
                @csrf
                <div class="modal-body p-2">

                    <div class="modal-form-group fs-4 fs-md-3 mb-0">
                        <i class="fas fa-hashtag me-1 text-danger"></i>
                        <span id="file_number_user" class="fs-4 fs-md-3"></span>
                    </div>

                    <div class="modal-form-group fs-5 fs-md-4">
                        <i class="fa-solid fa-file-zipper me-1 text-danger"></i>
                        <span id="base_name_user" class="fs-5 fs-md-4"></span>
                    </div>

                    <div id="user_list_asigned" class="user-list-asigned mb-2"></div>

                    <div class="user-input-wrapper">
                        <i class="fa-solid fa-user-plus"></i>
                        <input list="users" id="User_new" placeholder="Agregar un usuario..."
                            class="form-control-user">
                        <datalist id="users">
                            @foreach ($user_format as $userTool)
                                <option value="{{ $userTool->name }} {{ $userTool->last_name }}"
                                    data-id="{{ $userTool->id }}"></option>
                            @endforeach
                        </datalist>
                    </div>

                    <!-- Campo oculto donde guardaremos el ID del usuario -->
                    <input type="hidden" name="id_User_new" id="id_User_new">

                    <!-- Campo oculto con el ID real -->
                    <input type="hidden" name="id" id="registro_id_user">
                </div>

                <div class="modal-footer d-flex flex-row flex-md-column p-1">
                    <button type="button" class="btn btn-sm btn-secondary"
                        id="cancelFormatModalBtnProfesional">Cancelar</button>
                    <button type="submit" class="btn btn-sm btn-primary">
                        <i class="fa-solid fa-floppy-disk"></i> Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Funcionalidad de los Modales
        function openModal(modalId) {
            const modal = document.getElementById(modalId);
            const modalContainer = modal.querySelector('.modal-container');

            modal.style.display = 'flex';
            setTimeout(() => {
                modalContainer.classList.add('active');
            }, 10);

            document.addEventListener('keydown', closeOnEscape);
        }

        function closeModal(modalId) {
            const modal = document.getElementById(modalId);
            const modalContainer = modal.querySelector('.modal-container');

            modalContainer.classList.remove('active');
            setTimeout(() => {
                modal.style.display = 'none';
            }, 300);

            document.removeEventListener('keydown', closeOnEscape);
        }

        function closeOnEscape(event) {
            if (event.key === 'Escape') {
                const openModals = document.querySelectorAll('.modal-overlay[style*="display: flex"]');
                openModals.forEach(modal => {
                    closeModal(modal.id);
                });
            }
        }

        // Funcionalidad específica del Modal de Información
        function openInfoDetailModal(consecutivo) {
            document.getElementById('info_file_number').textContent = consecutivo.file_number || 'N/A';
            document.getElementById('info_base_name').textContent = consecutivo.bases?.name || 'Sin nombre';
            document.getElementById('info_intervencion').textContent = consecutivo.intervention_type?.name ||
                'Sin intervención';
            document.getElementById('info_created_at').textContent = consecutivo.created_at ?
                new Date(consecutivo.created_at).toLocaleDateString('es-ES') : 'N/A';

            document.getElementById('info_status').innerHTML = statusFicha(consecutivo.status);

            const formatosContainer = document.getElementById('info_formatos_adicionales');
            if (consecutivo.children && consecutivo.children.length > 0) {
                formatosContainer.innerHTML = formatosHijos(consecutivo.children);
            } else {
                formatosContainer.innerHTML = '<span class="text-muted">No hay formatos adicionales</span>';
            }

            openModal('infoDetailModal');
        }

        document.addEventListener("DOMContentLoaded", function() {
            const fechaInput = document.getElementById("fecha_intervencion");
            const hoy = new Date();

            const año = hoy.getFullYear();
            const mes = String(hoy.getMonth() + 1).padStart(2, '0'); // Mes actual (0-11)

            // Primer día y último día del mes actual
            const primerDia = `${año}-${mes}-01`;
            const ultimoDia = new Date(año, hoy.getMonth() + 1, 0).getDate();
            const ultimoDiaMes = `${año}-${mes}-${ultimoDia}`;

            // Asignar límites al input
            fechaInput.setAttribute("min", primerDia);
            fechaInput.setAttribute("max", ultimoDiaMes);
        });

        // Event listeners para modales
        document.getElementById('closeInfoDetailModal').addEventListener('click', () => closeModal('infoDetailModal'));
        document.getElementById('closeInfoDetailModalBtn').addEventListener('click', () => closeModal('infoDetailModal'));
        document.getElementById('closeFormatModal').addEventListener('click', () => closeModal('formatModal'));
        document.getElementById('cancelFormatModalBtn').addEventListener('click', () => closeModal(
            'modalEditarConsecutivo'));
        document.getElementById('cancelFormatModalBtnProfesional').addEventListener('click', () => closeModal(
            'formatModalNewUser'));
        document.getElementById('closeEdit').addEventListener('click', () => closeModal('modalEditarConsecutivo'));
        document.getElementById('closeEditFormat').addEventListener('click', () => closeModal('modalEditarFormato'));

        // Cerrar modales al hacer click fuera del contenido
        document.getElementById('infoDetailModal').addEventListener('click', (e) => {
            if (e.target === document.getElementById('infoDetailModal')) {
                closeModal('infoDetailModal');
            }
        });

        document.getElementById('formatModal').addEventListener('click', (e) => {
            if (e.target === document.getElementById('formatModal')) {
                closeModal('formatModal');
            }
        });

        document.getElementById('modalEditarConsecutivo').addEventListener('click', (e) => {
            if (e.target === document.getElementById('modalEditarConsecutivo')) {
                closeModal('modalEditarConsecutivo');
            }
        });

        document.getElementById('modalEditarFormato').addEventListener('click', (e) => {
            if (e.target === document.getElementById('modalEditarFormato')) {
                closeModal('modalEditarFormato');
            }
        });

        // Resto del código JavaScript existente...
        let formatos = @json($format);
        let intervenciones = @json($intervenciones);

        document.getElementById('formato').addEventListener('change', function() {
            let formatoId = this.value;
            let intervencionSelect = document.getElementById('intervencion');
            let container = document.getElementById('intervencion-container');

            intervencionSelect.innerHTML = '<option value="">Seleccione intervención</option>';
            let filtradas = intervenciones.filter(i => i.format_id == formatoId);

            if (filtradas.length > 0) {
                container.style.display = 'block';
                filtradas.forEach(i => {
                    let option = document.createElement('option');
                    option.value = i.id;
                    option.textContent = i.name;
                    intervencionSelect.required = true;
                    intervencionSelect.appendChild(option);
                });
            } else {
                container.style.display = 'none';
                intervencionSelect.required = false;
            }
        });

        document.addEventListener("DOMContentLoaded", function() {
            document.getElementById('entorno').addEventListener('change', function() {
                let entornoId = this.value;
                let formatoSelect = document.getElementById('formato');

                formatoSelect.innerHTML = '<option value="">Seleccione un formato</option>';

                if (entornoId) {
                    fetch(`/get-formatos/${entornoId}`)
                        .then(response => response.json())
                        .then(data => {

                            data.forEach(formato => {

                                let option = document.createElement('option');
                                option.value = formato.id;
                                option.textContent = formato.name;
                                formatoSelect.appendChild(option);

                            });
                        })
                        .catch(error => console.error('Error:', error));
                }
            });
        });
        
        document.addEventListener('DOMContentLoaded', () => {
            const inputFecha = document.getElementById('fechaIntervencion');
            if (!inputFecha) return;

            const formatoFecha = fecha => fecha.toISOString().split('T')[0];
            const hoy = new Date();
            const primerDiaMesActual = new Date(hoy.getFullYear(), hoy.getMonth(), 1);
            const ultimoDiaMesActual = new Date(hoy.getFullYear(), hoy.getMonth() + 1, 0);

            inputFecha.min = formatoFecha(primerDiaMesActual);
            inputFecha.max = formatoFecha(ultimoDiaMesActual);

            const infoDiv = document.getElementById('permisoMesAnteriorInfo');
            const textoInfo = document.getElementById('permisoMesAnteriorTexto');
            const accionDiv = document.getElementById('permisoMesAnteriorAccion');

            fetch('{{ route('consecutivo.permission.status') }}')
                .then(response => response.json())
                .then(estado => {
                    if (estado.active) {
                        const primerDiaMesAnterior = new Date(hoy.getFullYear(), hoy.getMonth() - 1, 1);
                        inputFecha.min = formatoFecha(primerDiaMesAnterior);

                        if (infoDiv && textoInfo) {
                            textoInfo.textContent = `Tienes un permiso activo: quedan ${estado.minutes_left} minuto(s) para registrar consecutivos del mes anterior.`;
                            infoDiv.style.display = 'block';
                        }
                    } else if (accionDiv) {
                        accionDiv.style.display = 'block';
                    }
                })
                .catch(error => console.error('Error consultando el estado del permiso:', error));
        });

        document.getElementById('btnMostrarSolicitudPermiso')?.addEventListener('click', () => {
            const form = document.getElementById('formSolicitarPermiso');
            if (form) form.style.display = form.style.display === 'none' ? 'block' : 'none';
        });

        // Deshabilitar botón al enviar formulario
        document.addEventListener("DOMContentLoaded", () => {
            const form = document.getElementById("formConsecutivos");
            const btn = document.getElementById("btnSubmit");

            if (form && btn) {
                form.addEventListener("submit", function() {
                    btn.disabled = true;
                    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enviando...';
                });
            }

            // Botón de actualizar
            const refreshBtn = document.getElementById("refreshBtn");
            if (refreshBtn) {
                refreshBtn.addEventListener("click", function() {
                    this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Actualizando...';
                    setTimeout(() => {
                        this.innerHTML = '<i class="fas fa-sync-alt"></i> Actualizar';
                        cargarReceptions();
                    }, 1000);
                });
            }
        });

        function formatosHijos(formatos) {
            if (!Array.isArray(formatos) || formatos.length === 0) return '';

            let html = '';
            formatos.forEach(formato => {
                // recortar nombre si es muy largo
                let nombre = formato.bases.name;
                if (nombre.length > 35) {
                    nombre = nombre.substring(0, 35) + '...';
                }

                // icono según estado
                let icono = '';
                if (formato.status === "1") {
                    icono = `<i class="fa-solid fa-2xs fa-clock text-warning" title="Pendiente"></i>`;
                } else if (formato.status === "2") {
                    icono = `<i class="fa-solid fa-2xs fa-check text-success" title="Completado"></i>`;
                } else if (formato.status === "3") {
                    icono = `<i class="fa-solid fa-2xs fa-check-double text-success" title="Entregado a Gesi"></i>`;
                } else if (formato.status === "4") {
                    icono = `<i class="fa-solid fa-2xs fa-desktop text-success" title="Digitado"></i>`;
                } else if (formato.status === "5") {
                    icono =
                        `<i class="fa-solid fa-2xs fa-person-walking-arrow-loop-left text-success" title="Devolución"></i>`;
                }

                // generar botón de editar
                let botonEditar = `
                    <button
                        class="btn m-0 p-0 mb-2 editar-formato"
                        data-info='${JSON.stringify(formato)}'
                        title="Editar formato">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </button>
                `;


                // construir HTML final
                html += `
                    <div class="d-flex align-items-center mb-1">
                        <span class="badge text-bg-primary-add">
                            ${nombre} <small>Cant: ${formato.quantity}</small>
                        </span>
                        ${icono}
                        ${botonEditar}
                    </div>
                `;
            });
            return html;
        }

        function statusFicha(status) {
            const statusNames = {
                10: {
                    label: 'Rechazado',
                    class: 'status-badge status-rejected'
                },
                1: {
                    label: 'Pendiente',
                    class: 'status-badge status-pending'
                },
                2: {
                    label: 'Sellado',
                    class: 'status-badge status-sealed'
                },
                3: {
                    label: 'Entregado a GESI',
                    class: 'status-badge status-delivered'
                },
                4: {
                    label: 'Digitado',
                    class: 'status-badge status-digitado'
                },
                5: {
                    label: 'Devuelto entorno',
                    class: 'status-badge status-returned'
                },
                6: {
                    label: 'Entregado profesional',
                    class: 'status-badge status-professional'
                },
                7: {
                    label: 'Entregado facilitador',
                    class: 'status-badge status-facilitator'
                },
                14: {
                    label: 'Recibido por Entorno',
                    class: 'status-badge status-professional'
                }

            };

            return statusNames[status] ?
                `<span class="${statusNames[status].class}">${statusNames[status].label}</span>` :
                `<span class="status-badge status-pending">Desconocido</span>`;
        }

        function cargarReceptions(busqueda = '') {

            let url = "{{ route('consecutivos_search') }}";

            if (busqueda.trim() !== '') {
                url += "?search=" + encodeURIComponent(busqueda);
            }

            fetch(url)
                .then(response => response.json())
                .then(data => {

                    let container = document.getElementById("consecutivos-container");
                    container.innerHTML = "";

                    if (data.length === 0) {
                        container.innerHTML = `
                            <div class="text-center p-4">
                                <i class="fas fa-search fa-2x text-muted mb-3"></i>
                                <p class="text-muted">No se encontraron resultados</p>
                            </div>`;
                        return;
                    }

                    data.forEach(r => {
                        container.innerHTML += `
                        <div class="consecutivo-item">
                            <div class="consecutivo-top">
                                <span class="consecutivo-id">
                                    <i class="fa-solid fa-hashtag"></i> ${r.file_number || 'N/A'}
                                </span>
                                ${statusFicha(r.status)}
                            </div>

                            <div class="consecutivo-main">
                                <h2 class="seccion-data m-0">
                                    <i class="fa-solid fa-file-zipper"></i>
                                    ${r.bases?.name || 'Sin nombre'}
                                </h2>

                                <div class="consecutivo-meta">
                                    ${r.intervention_type
                                        ? `<span class="intervencion">${r.intervention_type.name}</span>`
                                        : ''
                                    }
                                    <span class="intervencion">
                                        Fecha de intervención: ${r.fecha_intervencion}
                                    </span>
                                </div>

                                <div class="consecutivo-children">
                                    ${formatosHijos(r.children)}
                                </div>
                            </div>

                            <div class="consecutivo-actions">

                                ${ r.adicional_format.length > 0
                                    ? `
                                            <button class="btn-sm btn-secondary btn-open-format-modal"
                                                title="Asociar formato"
                                                data-info='${JSON.stringify(r)}'>
                                                <i class="fa-solid fa-file-circle-plus"></i>
                                                <span class="action-label">Asociar</span>
                                            </button>`
                                    : ''
                                }

                                <button class="btn-sm btn-secondary btn-open-format-User position-relative"
                                    title="Agregar profesional"
                                    data-info='${JSON.stringify(r)}'>
                                    <i class="fa-solid fa-person-circle-plus"></i>
                                    <span class="action-label">Profesional</span>

                                    ${r.users.length > 1
                                        ? `
                                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                                    ${r.users.length - 1}
                                                </span>`
                                        : ''
                                    }
                                </button>

                                <button class="btn-sm btn-secondary btn-edit-info"
                                    title="Editar información"
                                    data-info='${JSON.stringify(r)}'>
                                    <i class="fa-solid fa-pen-to-square"></i>
                                    <span class="action-label">Editar</span>
                                </button>

                            </div>
                        </div>`;
                    });

                    asignarEventosConsecutivos();
                })
                .catch(error => {
                    console.error('Error al cargar consecutivos:', error);

                    document.getElementById("consecutivos-container").innerHTML = `
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle"></i>
                            Error al cargar los consecutivos
                        </div>`;
                });
        }

        // primera carga
        cargarReceptions();

        document.addEventListener('DOMContentLoaded', function() {

            const searchInput = document.getElementById('searchInput');
            const btnSearch = document.getElementById('btnSearch');

            if (btnSearch && searchInput) {

                btnSearch.addEventListener('click', function() {
                    cargarReceptions(searchInput.value);
                });

                searchInput.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        cargarReceptions(this.value);
                    }
                });
            }
        });


        function asignarEventosConsecutivos() {
            // Asignar eventos a los botones de información (🔍)
            document.querySelectorAll('.btn-info-detail-modal').forEach(btn => {
                btn.addEventListener('click', function() {
                    const info = JSON.parse(this.getAttribute('data-info'));
                    openInfoDetailModal(info);
                });
            });

            document.querySelectorAll('.btn-open-format-User').forEach(btn => {
                btn.addEventListener('click', function() {
                    const info = JSON.parse(this.getAttribute('data-info'));

                    // Asignar valores
                    document.getElementById('file_number_user').textContent = info.file_number;
                    document.getElementById('base_name_user').textContent = info.bases?.name || '';
                    document.getElementById('registro_id_user').value = info.id;

                    const lis_user = document.getElementById('user_list_asigned');
                    lis_user.innerHTML = ''; // limpiar antes de agregar

                    // Crear usuarios
                    info.users.forEach(user => {
                        const p = document.createElement('p');
                        p.classList.add('p-2');
                        p.innerHTML = `
                            <i class="fa-solid fa-user"></i> ${user.name} ${user.last_name}
                            <button type="button" class="btn-remove-user"
                                    data-registro-id="${info.id}"
                                    data-user-id="${user.id}"
                                    title="Eliminar usuario">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        `;
                        lis_user.appendChild(p);
                    });

                    // 🔥 Escuchamos los botones eliminar
                    lis_user.querySelectorAll('.btn-remove-user').forEach(btnDelete => {
                        btnDelete.addEventListener('click', async function() {
                            const registroId = this.getAttribute('data-registro-id');
                            const userId = this.getAttribute('data-user-id');

                            const confirmar = confirm(
                                '¿Estás seguro de que deseas eliminar este usuario asignado?'
                            );
                            if (!confirmar) return;

                            // Esperar respuesta del servidor
                            const exito = await eliminarUsuarioAsignado(registroId,
                                userId);

                            if (exito) {
                                alert('✅ Usuario eliminado correctamente');
                                this.parentElement.remove();
                            } else {
                                alert('❌ Error al eliminar el usuario');
                            }
                        });
                    });

                    openModal('formatModalNewUser');
                });
            });

            // Función asíncrona
            async function eliminarUsuarioAsignado(registroId, userId) {
                try {
                    const response = await fetch(`/eliminar-usuario-asignado/${registroId}/${userId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });

                    const data = await response.json();
                    return data.success; // true o false según el backend
                } catch (error) {
                    console.error('Error en la solicitud:', error);
                    return false;
                }
            }



            // Asignar eventos a los botones "formato"
            document.querySelectorAll('.btn-open-format-modal').forEach(btn => {
                btn.addEventListener('click', function() {
                    const info = JSON.parse(this.getAttribute('data-info'));

                    document.getElementById('environment').value = info.environment;
                    document.getElementById('localidad_id').value = info.localidad_id;
                    document.getElementById('user_id_profesional').value = info.user_id_profesional;
                    document.getElementById('consecutivo').value = info.consecutivo;

                    // Llenar los campos del formulario
                    document.getElementById('file_number').value = info.file_number || 'N/A';
                    document.getElementById('base_name').value = info.bases?.name || 'Sin nombre';
                    document.getElementById('intervencion').value = info.intervention_type?.name ||
                        'Sin intervención';
                    document.getElementById('registro_id').value = info.id;
                    document.getElementById('users_format').value = info.users.map(u => u.id).join(',');
                    const ids = info.adicional_format.map(item => item.id_format_adicional);

                    // Hacer la solicitud al backend
                    fetch('{{ route('get.format.names') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                ids
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            const select = document.getElementById('format_select_modal');
                            select.innerHTML = ''; // Limpiar select

                            if (data.length > 0) {
                                data.forEach(format => {
                                    const option = document.createElement('option');
                                    option.value = format.id;
                                    option.textContent = format.name;
                                    select.appendChild(option);
                                });
                            } else {
                                const option = document.createElement('option');
                                option.textContent = 'No hay formatos adicionales';
                                select.appendChild(option);
                            }
                        })
                        .catch(error => console.error('Error al cargar formatos:', error));

                    // Mostrar modal de formato
                    openModal('formatModal');
                });
            });

            document.querySelectorAll('.btn-edit-info').forEach(btn => {
                btn.addEventListener('click', e => {
                    const info = JSON.parse(e.currentTarget.getAttribute('data-info'));
                    abrirModalEdicion(info); // función que tú definirás
                });
            });

            document.querySelectorAll('.editar-formato').forEach(btn => {
                btn.addEventListener('click', e => {
                    const info = JSON.parse(e.currentTarget.getAttribute('data-info'));
                    abrirModalFormato(info); // función que tú definirás
                });
            });
        }

        function abrirModalFormato(info) {
            // Aquí puedes llenar los campos del modal con los datos de "info"
            document.getElementById('id_number_format').value = info.id || 'N/A';
            document.getElementById('name_format').value = info.bases?.name || 'Sin nombre';

            openModal('modalEditarFormato');
        }

        function abrirModalEdicion(info) {
            // Aquí puedes llenar los campos del modal con los datos de "info"
            document.getElementById('id_number').value = info.id || 'N/A';
            document.getElementById('edit_number').value = info.file_number || 'N/A';
            openModal('modalEditarConsecutivo');
        }
    </script>

    <script>
        document.getElementById('User_new').addEventListener('input', function() {
            const input = this;
            const list = document.getElementById('users');
            const hiddenInput = document.getElementById('id_User_new');
            const selectedOption = Array.from(list.options).find(
                option => option.value === input.value
            );
            hiddenInput.value = selectedOption ? selectedOption.dataset.id : '';
        });
    </script>



    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('formFormato');
            const submitButton = form.querySelector('button[type="submit"]');

            form.addEventListener('submit', function() {
                // Deshabilita el botón
                submitButton.disabled = true;
                // Cambia el texto mientras se envía
                submitButton.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Guardando...';
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('formFormato_localidad');
            const submitButton = form.querySelector('button[type="submit"]');

            form.addEventListener('submit', function() {
                // Deshabilita el botón
                submitButton.disabled = true;
                // Cambia el texto mientras se envía
                submitButton.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Guardando...';
            });
        });
    </script>


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form2 = document.getElementById('formFormato_User');
            const submitButton2 = form2.querySelector('button[type="submit"]');

            form2.addEventListener('submit', function() {
                // Deshabilita el botón
                submitButton2.disabled = true;
                // Cambia el texto mientras se envía
                submitButton2.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Guardando...';
            });
        });
    </script>
@endsection
