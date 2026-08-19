@extends('layouts.dig.navigation')
@section('main')
    <div class="dig-page-header">
        <div>
            <span class="dig-page-tag">Digitalización · Consecutivos</span>
            <h2 class="dig-page-title">Consecutivos</h2>
        </div>
    </div>

    <div class="row row-cols-1 row-cols-md-2 g-4">
        <div class="col">
        <form class="dig-card undocumented-container" action="{{ route('consecutivos.store') }}" method="POST">
            @csrf
            <h4 class="mb-4">Crear consecutivo</h4>

            <div class="row">
                <!-- Localidad -->
                <div class="col-12 col-xl-6">
                    <select class="form-select" id="localidad" name="localidad" required onchange="cargarUPZ()">
                        <option value="" disabled selected>Seleccione una localidad</option>
                        <!-- Opciones de localidad -->
                        @foreach ($localidades as $localidad)
                            <option value="{{ $localidad->identificador }}"
                                {{ old('localidad') == $localidad->id ? 'selected' : '' }}>
                                {{ $localidad->identificador }} - {{ $localidad->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- UPZ -->
                <div class="col-12 col-xl-6">
                    <select class="form-select" id="upz" name="upz">
                        <option value="" disabled selected>Seleccione una UPZ</option>
                    </select>
                </div>
            </div>

            <div class="row text-center g-3">
                <div class="col-12 input-group">
                    <span class="input-group-text" id="inputGroup-sizing-default">No. ficha</span>
                    <input type="number" class="form-control" id="ficha" name="ficha" value="{{ old('ficha') }}"
                        required>
                </div>

                <div class="input-group col-12 col-xxl-6">
                    <span class="input-group-text" id="inputGroup-sizing-default">Primer nombre</span>
                    <input type="text" class="form-control" id="primer_nombre" name="primer_nombre"
                        value="{{ old('primer_nombre') }}" pattern="[A-Za-zÁÉÍÓÚáéíóúñÑ]+" required>
                </div>

                <div class="input-group col-12 col-xxl-6">
                    <span class="input-group-text" id="inputGroup-sizing-default">Segundo nombre</span>
                    <input type="text" class="form-control" id="segundo_nombre" name="segundo_nombre"
                        value="{{ old('segundo_nombre') }}" pattern="[A-Za-zÁÉÍÓÚáéíóúñÑ\s]+">
                </div>

                <div class="input-group col-12 col-xxl-6">
                    <span class="input-group-text" id="inputGroup-sizing-default">Primer apellido</span>
                    <input type="text" class="form-control" id="primer_apellido" name="primer_apellido"
                        value="{{ old('primer_apellido') }}" pattern="[A-Za-zÁÉÍÓÚáéíóúñÑ]+" required>
                </div>

                <div class="input-group col-12 col-xxl-6">
                    <span class="input-group-text" id="inputGroup-sizing-default">Segundo apellido</span>
                    <input type="text" class="form-control" id="segundo_apellido" name="segundo_apellido"
                        value="{{ old('segundo_apellido') }}" pattern="[A-Za-zÁÉÍÓÚáéíóúñÑ]+">
                </div>
            </div>

            <div class="row px-2">
                <button type="submit" class="dig-btn dig-btn-primary mt-3 w-100 justify-content-center">Crear</button>
            </div>
        </form>
        </div>

        <!-- Sección Historial -->
        <div class="col">
        <div class="dig-card">
            <h4 class="mb-4">Historial</h4>
            <div class="d-flex align-items-center mb-4">
                <form action="{{ route('consecutivos.show') }}" class="flex-grow-1 me-3">
                    <div class="input-group">
                        <input type="text" name="query" value = "{{ request('query') }}" class="form-control"
                            placeholder="Buscar">
                        <button type="submit" class="dig-btn dig-btn-primary btn-loader">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </button>
                    </div>
                </form>

                <form action="{{ route('consecutivos.filterByUser') }}" method="GET">
                    <input type="hidden" name="user_id" value="{{ $user->id }}">
                    @if (request()->has('user_id'))
                        <input type="hidden" name="remove_filter" value="1">
                        <button type="submit" class="dig-btn dig-btn-primary">
                            <i class="fa-solid fa-filter-circle-xmark"></i>
                        </button>
                    @else
                        <button type="submit" class="dig-btn dig-btn-ghost">
                            <i class="fa fa-filter"></i>
                        </button>
                    @endif
                </form>
            </div>

            <!-- Lista de consecutivos -->
            <ul class="list-group scrollable-content list-consecutivos">
                @foreach ($consecutivos as $consecutivo)
                    <li class="list-group-item d-flex justify-content-between align-items-center mt-2"
                        style="background: var(--shell-surface-2); border-color: var(--shell-border); color: var(--shell-text);">
                        <div>
                            <h5 class="mb-1">{{ $consecutivo->consecutivo }}</h5>
                            <input type="text" id = "{{ $consecutivo->consecutivo }}"
                                value={{ $consecutivo->consecutivo }} hidden>
                            <small style="color: var(--shell-muted);">
                                Para {{ $consecutivo->primer_nombre }} {{ $consecutivo->primer_apellido }},
                                ficha No. {{ $consecutivo->receptions?->file_number ?? 'N/A' }} - Creado el
                                {{ $consecutivo->created_at->format('d/m/Y') }}
                                por {{ $consecutivo->usuario?->name ?? 'N/A' }}
                            </small>
                        </div>
                        <button class="dig-btn dig-btn-primary btn-sm"
                            onclick="copyToClipboard('{{ $consecutivo->consecutivo }}')">
                            <i class="fa-solid fa-copy"></i>
                        </button>
                    </li>
                @endforeach
            </ul>
        </div>
        </div>
    </div>

    <script>
        // Definir un objeto para almacenar las UPZ por localidad
        const upzPorLocalidad = {};

        // Función para cargar las UPZ desde la API
        async function cargarDatosUPZ() {
            //const url = 'https://bogota-laburbano.opendatasoft.com/api/explore/v2.1/catalog/datasets/poblacion-upz-bogota/records?select=cod_loc%2C%20nomb_loc%2C%20cod_upz%2C%20nom_upz&limit=100';
            const url = 'https://www.trakio.pro/js/json_localidades/poblacion-upz-bogota.json';

            try {
                const response = await fetch(url);
                if (!response.ok) {
                    throw new Error('Error en la red: ' + response.status);
                }

                const data = await response.json();

                // Iterar directamente sobre el array de objetos `data`
                data.forEach(record => {
                    const localidad = record.cod_loc;
                    const upz = record.cod_upz;
                    const nombreUPZ = record.nom_upz;
                    const localidadKey = localidad.toString();

                    // Verificar si la clave de localidad ya existe
                    if (!upzPorLocalidad[localidadKey]) {
                        upzPorLocalidad[localidadKey] = [];
                    }
                    // Agregar la UPZ y su nombre al arreglo correspondiente
                    upzPorLocalidad[localidadKey].push({
                        upz,
                        nombreUPZ
                    });
                });

            } catch (error) {
                console.error('Hubo un problema con la solicitud Fetch:', error);
            }
        }

        // Función para cargar las UPZ según la localidad seleccionada
        function cargarUPZ() {
            const localidad = document.getElementById("localidad").value;
            const upzSelect = document.getElementById("upz");

            // Limpiar las opciones de UPZ
            upzSelect.innerHTML = '<option value="" selected disabled>Seleccione una UPZ</option>';

            if (localidad && upzPorLocalidad[localidad]) {
                // Añadir las UPZ correspondientes a la localidad seleccionada
                upzPorLocalidad[localidad].forEach(function(item) {
                    const {
                        upz,
                        nombreUPZ
                    } = item;

                    // Crear la opción para el select
                    const option = document.createElement("option");
                    option.value = upz; // Valor solo el cod_upz
                    option.text = `${upz} - ${nombreUPZ}`; // Texto con el cod_upz y nom_upz
                    upzSelect.appendChild(option);
                });
            } else {
                console.error('No se encontraron UPZ para la localidad seleccionada:', localidad);
            }
        }

        // Llamar a la función para cargar los datos de la API al cargar la página
        window.addEventListener('load', cargarDatosUPZ);

        async function copyToClipboard(elementId) {
            try {
                const inputData = document.getElementById(elementId);
                if (!inputData) {
                    console.error("Elemento con ID '" + elementId + "' no encontrado.");
                    return;
                }

                const text = inputData.value;

                await navigator.clipboard.writeText(text);
                showNotification(`Consecutivo copiado: ${text}`);

            } catch (err) {
                console.error('Error al copiar al portapapeles:', err);
                showNotification("Error al copiar", true);
            }
        }

        function showNotification(mensaje, esError = false) {
            const notificacion = document.createElement('div');
            notificacion.textContent = mensaje;
            notificacion.style.position = 'fixed';
            notificacion.style.top = '15px';
            notificacion.style.right = '20px';
            notificacion.style.padding = '10px';
            notificacion.style.backgroundColor = esError ? '#FF5733' : '#dc3545';
            notificacion.style.color = 'white';
            notificacion.style.borderRadius = '5px';
            notificacion.style.zIndex = '9999';
            document.body.appendChild(notificacion);

            setTimeout(() => {
                notificacion.remove();
            }, 3000);
        }
    </script>
@endsection
