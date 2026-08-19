@extends('layouts.adm.navigation')

@section('main')

    <div class="adm-page-header">
        <div>
            <span class="adm-page-tag">// Prueba de importación</span>
            <h1 class="adm-page-title">Importar errores de digitación (Excel)</h1>
        </div>
        <a href="{{ route('adm.error-imports.index') }}" class="adm-btn adm-btn-ghost">
            <i class="fas fa-list"></i> Ver cargas realizadas
        </a>
    </div>

    @if (session('success'))
        <div style="margin-bottom:1rem; padding:.65rem 1rem; background:rgba(46,204,113,.1); border:1px solid rgba(46,204,113,.3); border-radius:6px; font-size:.82rem; color:#2ecc71;">
            <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div style="margin-bottom:1rem; padding:.65rem 1rem; background:rgba(230,57,70,.1); border:1px solid rgba(230,57,70,.3); border-radius:6px; font-size:.82rem; color:#e63946;">
            <i class="fas fa-exclamation-circle me-1"></i> {{ session('error') }}
        </div>
    @endif

    <div class="adm-card" style="margin-bottom: 1.5rem;">
        <div class="adm-card-head">
            <span class="adm-card-title"><i class="fas fa-file-excel"></i> Subir archivo de prueba</span>
        </div>
        <p class="adm-card-note">
            Esta pantalla solo previsualiza lo que el sistema detecta en el Excel. No guarda nada en la base de datos.
        </p>

        <form action="{{ route('adm.error-imports.preview') }}" method="POST" enctype="multipart/form-data"
              style="display:flex; gap:1rem; align-items:flex-end; flex-wrap:wrap; padding: .5rem 0;">
            @csrf
            <div class="adm-filter-group">
                <span class="adm-filter-label">Entorno</span>
                <select name="environment_id" class="adm-filter-input" required>
                    <option value="">Seleccionar...</option>
                    @foreach ($environments as $environment)
                        <option value="{{ $environment->id }}" {{ ($environment_id ?? null) == $environment->id ? 'selected' : '' }}>
                            {{ $environment->entorno }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="adm-filter-group">
                <span class="adm-filter-label">Base</span>
                <select name="format_id" class="adm-filter-input" required>
                    <option value="">Seleccionar...</option>
                    @foreach ($formats as $format)
                        <option value="{{ $format->id }}" {{ ($format_id ?? null) == $format->id ? 'selected' : '' }}>
                            {{ $format->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="adm-filter-group">
                <span class="adm-filter-label">Sección</span>
                <input type="text" name="section" class="adm-filter-input" placeholder="Ej: Datos Generales"
                       value="{{ $section ?? old('section') }}" required>
            </div>
            <div class="adm-filter-group">
                <span class="adm-filter-label">Archivo (.xlsx)</span>
                <input type="file" name="file" accept=".xlsx,.xls" class="adm-filter-input" required>
            </div>
            <button type="submit" class="adm-btn adm-btn-primary">Previsualizar</button>
        </form>

        @error('environment_id')
            <div style="color:var(--shell-red); font-size:.82rem;">{{ $message }}</div>
        @enderror
        @error('format_id')
            <div style="color:var(--shell-red); font-size:.82rem;">{{ $message }}</div>
        @enderror
        @error('section')
            <div style="color:var(--shell-red); font-size:.82rem;">{{ $message }}</div>
        @enderror
        @error('file')
            <div style="color:var(--shell-red); font-size:.82rem;">{{ $message }}</div>
        @enderror
    </div>

    @isset($preview)
        <div class="adm-card">
            <div class="adm-card-head">
                <span class="adm-card-title"><i class="fas fa-magnifying-glass"></i> Resultado de la previsualización</span>
                <span class="adm-badge">{{ $original_filename }}</span>
            </div>
            <p class="adm-card-note">
                Base: <strong>{{ $formats->firstWhere('id', $format_id)?->name }}</strong>
                &middot; Sección: <strong>{{ $section }}</strong>
            </p>

            @if (!$preview['username_column_found'])
                <div style="margin-bottom:1rem; padding:.65rem 1rem; background:rgba(230,57,70,.1); border:1px solid rgba(230,57,70,.3); border-radius:6px; font-size:.82rem; color:#e63946;">
                    <i class="fas fa-exclamation-triangle me-1"></i>
                    No se encontró una columna llamada "Usuario" entre las primeras 6 columnas del archivo — ningún error se podrá asignar a un digitador.
                </div>
            @endif

            @if (!$preview['location_column_found'] || !$preview['observations_column_found'])
                <div style="margin-bottom:1rem; padding:.65rem 1rem; background:rgba(241,196,15,.1); border:1px solid rgba(241,196,15,.3); border-radius:6px; font-size:.82rem; color:#f1c40f;">
                    <i class="fas fa-circle-info me-1"></i>
                    @if (!$preview['location_column_found'])
                        No se encontró una columna de "Número de usuario" o "Página" (opcional) — el digitador no verá en qué parte del documento está el error.
                    @endif
                    @if (!$preview['observations_column_found'])
                        No se encontró una columna de "Observaciones" (opcional).
                    @endif
                </div>
            @endif

            <div class="adm-kpi-grid" style="grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); margin-bottom: 1rem;">
                <div class="adm-kpi">
                    <div class="adm-kpi-label">Filas totales</div>
                    <div class="adm-kpi-value">{{ $preview['stats']['total_rows'] }}</div>
                </div>
                <div class="adm-kpi is-accent">
                    <div class="adm-kpi-label">Fichas con errores</div>
                    <div class="adm-kpi-value">{{ $preview['stats']['flagged_rows'] }}</div>
                </div>
                <div class="adm-kpi">
                    <div class="adm-kpi-label">Usuarios sin identificar</div>
                    <div class="adm-kpi-value">{{ $preview['unresolved_users'] }}</div>
                </div>
                <div class="adm-kpi">
                    <div class="adm-kpi-label">Fichas no encontradas</div>
                    <div class="adm-kpi-value">{{ $preview['unresolved_fichas'] }}</div>
                </div>
            </div>

            @if ($preview['rows']->isEmpty())
                <p style="color: var(--shell-muted); text-align:center; padding: 1.5rem 0;">
                    No se detectó ninguna celda en rojo en el archivo — nada para cargar.
                </p>
            @else
                <div style="overflow-x:auto;">
                    <table class="adm-table">
                        <thead>
                            <tr>
                                <th>Ficha</th>
                                <th>Usuario (Excel)</th>
                                <th>Usuario resuelto</th>
                                <th>Ficha en receptions</th>
                                <th>Nº usuario / Pág.</th>
                                <th>Observaciones</th>
                                <th>Campos en error</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($preview['rows'] as $row)
                                <tr>
                                    <td>{{ $row['file_number'] }}</td>
                                    <td>{{ $row['username_raw'] ?: 'Sin dato' }}</td>
                                    <td>
                                        @if ($row['user_name'])
                                            {{ $row['user_name'] }}
                                        @else
                                            <span class="adm-badge adm-badge-red">No identificado</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($row['reception_found'])
                                            <i class="fas fa-check" style="color:#2ecc71;"></i>
                                        @else
                                            <span class="adm-badge adm-badge-red">No encontrada</span>
                                        @endif
                                    </td>
                                    <td>{{ $row['location_reference'] ?: '—' }}</td>
                                    <td>{{ $row['observations'] ?: '—' }}</td>
                                    <td>
                                        @foreach ($row['flags'] as $flag)
                                            <span class="adm-badge adm-badge-red" style="margin: 2px;" title="{{ trim($flag['value']) === '' ? 'Campo vacío' : 'Campo con dato incorrecto' }}">
                                                {{ $flag['field_label'] }} ({{ trim($flag['value']) === '' ? 'vacío' : 'dato incorrecto' }})
                                            </span>
                                        @endforeach
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            @if (!$preview['rows']->isEmpty())
                <form action="{{ route('adm.error-imports.commit') }}" method="POST" style="margin-top:1.5rem;"
                      onsubmit="return confirm('¿Cargar estos {{ $preview['stats']['flagged_rows'] }} errores a la base de datos?');">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">
                    <input type="hidden" name="environment_id" value="{{ $environment_id }}">
                    <input type="hidden" name="format_id" value="{{ $format_id }}">
                    <input type="hidden" name="section" value="{{ $section }}">
                    <input type="hidden" name="original_filename" value="{{ $original_filename }}">
                    <button type="submit" class="adm-btn adm-btn-primary">Efectivamente, cargar</button>
                </form>
            @endif
        </div>
    @endisset

@endsection
