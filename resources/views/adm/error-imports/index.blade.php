@extends('layouts.adm.navigation')

@section('main')

    <div class="adm-page-header">
        <div>
            <span class="adm-page-tag">// Errores de digitación</span>
            <h1 class="adm-page-title">Cargas de Excel</h1>
        </div>
        <a href="{{ route('adm.error-imports.test') }}" class="adm-btn adm-btn-primary">
            <i class="fas fa-upload"></i> Subir nuevo Excel
        </a>
    </div>

    @if (session('success'))
        <div style="margin-bottom:1rem; padding:.65rem 1rem; background:rgba(46,204,113,.1); border:1px solid rgba(46,204,113,.3); border-radius:6px; font-size:.82rem; color:#2ecc71;">
            <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
        </div>
    @endif

    <div class="adm-kpi-grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); margin-bottom: 1.5rem;">
        <div class="adm-kpi is-accent">
            <div class="adm-kpi-label">Campos pendientes por corregir (todas las cargas)</div>
            <div class="adm-kpi-value">{{ $totalPending }}</div>
        </div>
    </div>

    <div class="adm-card">
        <div class="adm-card-head">
            <span class="adm-card-title"><i class="fas fa-list"></i> Historial de cargas</span>
            <span class="adm-badge">{{ $imports->count() }} cargas</span>
        </div>

        @if ($imports->isEmpty())
            <p style="color: var(--shell-muted); text-align:center; padding: 1.5rem 0;">
                Todavía no se ha cargado ningún Excel de errores.
            </p>
        @else
            <div style="overflow-x:auto;">
                <table class="adm-table">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Entorno</th>
                            <th>Base</th>
                            <th>Sección</th>
                            <th>Archivo</th>
                            <th>Cargado por</th>
                            <th style="text-align:center;">Fichas</th>
                            <th style="text-align:center;">Campos totales</th>
                            <th style="text-align:center;">Pendientes</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($imports as $import)
                            @php $digitadores = $digitadoresByImport->get($import->id, collect()); @endphp
                            <tr>
                                <td>{{ $import->created_at->format('d/m/Y H:i') }}</td>
                                <td>{{ $import->environment->entorno ?? 'N/A' }}</td>
                                <td colspan="2">
                                    <form action="{{ route('adm.error-imports.update', $import) }}" method="POST"
                                          style="display:flex; gap:.5rem; align-items:center;">
                                        @csrf
                                        @method('PATCH')
                                        <select name="format_id" class="adm-filter-input" style="min-width:140px;" required>
                                            <option value="">Sin base</option>
                                            @foreach ($formats as $format)
                                                <option value="{{ $format->id }}" {{ $import->format_id == $format->id ? 'selected' : '' }}>
                                                    {{ $format->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <input type="text" name="section" class="adm-filter-input" style="min-width:140px;"
                                               placeholder="Sección" value="{{ $import->section }}" required>
                                        <button type="submit" class="adm-btn adm-btn-ghost" title="Guardar">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </form>
                                </td>
                                <td>{{ $import->original_filename }}</td>
                                <td>{{ $import->uploadedBy ? properNouns($import->uploadedBy->name, $import->uploadedBy->last_name) : 'N/A' }}</td>
                                <td style="text-align:center;">{{ $import->records()->count() }}</td>
                                <td style="text-align:center;">{{ $import->total_fields_count }}</td>
                                <td style="text-align:center;">
                                    @if ($import->pending_fields_count > 0)
                                        <span class="adm-badge adm-badge-red">{{ $import->pending_fields_count }}</span>
                                    @else
                                        <i class="fas fa-check" style="color:#2ecc71;"></i>
                                    @endif
                                </td>
                                <td style="display:flex; gap:.4rem;">
                                    @if ($digitadores->isNotEmpty())
                                        <button type="button" class="adm-btn adm-btn-ghost digitadores-toggle"
                                                data-target="digitadores-{{ $import->id }}" title="Ver digitadores">
                                            <i class="fas fa-users"></i>
                                        </button>
                                    @endif
                                    <form action="{{ route('adm.error-imports.destroy', $import) }}" method="POST"
                                          onsubmit="return confirm('¿Eliminar esta carga y todos sus registros? Podrás volver a subir el Excel después.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="adm-btn adm-btn-ghost"
                                                style="color:var(--shell-red); border-color:rgba(230,57,70,.3);"
                                                title="Eliminar carga">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @if ($digitadores->isNotEmpty())
                                <tr class="digitadores-row" id="digitadores-{{ $import->id }}" style="display:none;">
                                    <td colspan="10" style="background:var(--shell-surface-2); padding:1rem 1.25rem;">
                                        <div class="digitadores-grid">
                                            @foreach ($digitadores as $digitador)
                                                @php
                                                    $statusMeta = [
                                                        'completado' => ['label' => 'Completado', 'class' => 'is-done', 'icon' => 'fa-circle-check'],
                                                        'en_proceso' => ['label' => 'En proceso', 'class' => 'is-progress', 'icon' => 'fa-spinner'],
                                                        'sin_iniciar' => ['label' => 'Sin iniciar', 'class' => 'is-pending', 'icon' => 'fa-circle-exclamation'],
                                                    ][$digitador['status']];
                                                @endphp
                                                <div class="digitador-chip {{ $statusMeta['class'] }}">
                                                    <div class="digitador-chip-head">
                                                        <span class="digitador-name">{{ $digitador['name'] }}</span>
                                                        <span class="digitador-status"><i class="fas {{ $statusMeta['icon'] }}"></i> {{ $statusMeta['label'] }}</span>
                                                    </div>
                                                    <div class="digitador-progress-bar">
                                                        <div class="digitador-progress-fill" style="width: {{ $digitador['total_fields'] > 0 ? round(($digitador['resolved_fields'] / $digitador['total_fields']) * 100) : 0 }}%"></div>
                                                    </div>
                                                    <span class="digitador-count">{{ $digitador['resolved_fields'] }}/{{ $digitador['total_fields'] }} campos corregidos</span>

                                                    @if (($digitador['pending_errors'] ?? collect())->isNotEmpty())
                                                        <details class="digitador-errors">
                                                            <summary>Ver errores pendientes ({{ $digitador['pending_fields'] }})</summary>
                                                            <ul class="digitador-error-list">
                                                                @foreach ($digitador['pending_errors'] as $ficha)
                                                                    <li class="digitador-error-ficha-item">
                                                                        <span class="digitador-error-ficha">
                                                                            <i class="fas fa-file-lines"></i> Ficha {{ $ficha['file_number'] }}
                                                                        </span>
                                                                        <ul class="digitador-error-fields">
                                                                            @foreach ($ficha['fields'] as $field)
                                                                                <li>
                                                                                    <span class="digitador-error-label">{{ $field['label'] }}</span>
                                                                                    <span class="digitador-error-issue">{{ $field['issue'] }}</span>
                                                                                </li>
                                                                            @endforeach
                                                                        </ul>
                                                                    </li>
                                                                @endforeach
                                                            </ul>
                                                        </details>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <style>
        .digitadores-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: .75rem;
        }
        .digitador-chip {
            background: var(--shell-surface);
            border: 1px solid var(--shell-border-strong);
            border-radius: 10px;
            padding: .65rem .8rem;
        }
        .digitador-chip-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: .5rem;
            margin-bottom: .5rem;
        }
        .digitador-name {
            font-weight: 600;
            font-size: .85rem;
            color: var(--shell-text);
        }
        .digitador-status {
            font-size: .7rem;
            padding: .15rem .5rem;
            border-radius: 999px;
            white-space: nowrap;
        }
        .digitador-chip.is-done .digitador-status { color: #2ecc71; background: rgba(46,204,113,.12); }
        .digitador-chip.is-progress .digitador-status { color: #f1c40f; background: rgba(241,196,15,.12); }
        .digitador-chip.is-pending .digitador-status { color: var(--shell-red); background: rgba(230,57,70,.12); }
        .digitador-progress-bar {
            height: 5px;
            border-radius: 999px;
            background: var(--shell-border);
            overflow: hidden;
            margin-bottom: .35rem;
        }
        .digitador-progress-fill {
            height: 100%;
            border-radius: 999px;
            background: #2ecc71;
        }
        .digitador-count {
            font-size: .72rem;
            color: var(--shell-muted);
        }
        .digitador-errors {
            margin-top: .55rem;
            border-top: 1px dashed var(--shell-border);
            padding-top: .45rem;
        }
        .digitador-errors summary {
            cursor: pointer;
            font-size: .72rem;
            font-weight: 600;
            color: var(--shell-red);
            list-style: none;
        }
        .digitador-errors summary::-webkit-details-marker {
            display: none;
        }
        .digitador-errors summary::before {
            content: "\f105";
            font-family: "Font Awesome 6 Free";
            font-weight: 900;
            display: inline-block;
            margin-right: .35rem;
            transition: transform .15s ease;
        }
        .digitador-errors[open] summary::before {
            transform: rotate(90deg);
        }
        .digitador-error-list {
            list-style: none;
            margin: .5rem 0 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            gap: .5rem;
            max-height: 220px;
            overflow-y: auto;
        }
        .digitador-error-ficha-item {
            background: var(--shell-surface-2);
            border: 1px solid var(--shell-border);
            border-radius: 8px;
            padding: .45rem .6rem;
        }
        .digitador-error-ficha {
            font-size: .74rem;
            font-weight: 600;
            color: var(--shell-text);
        }
        .digitador-error-fields {
            list-style: none;
            margin: .35rem 0 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            gap: .25rem;
        }
        .digitador-error-fields li {
            display: flex;
            justify-content: space-between;
            gap: .5rem;
            font-size: .72rem;
        }
        .digitador-error-label {
            color: var(--shell-muted);
            white-space: nowrap;
        }
        .digitador-error-issue {
            color: var(--shell-red);
            text-align: right;
        }
    </style>

    <script>
        document.querySelectorAll('.digitadores-toggle').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var row = document.getElementById(btn.dataset.target);
                if (row) row.style.display = row.style.display === 'none' ? '' : 'none';
            });
        });
    </script>

@endsection
