@php
$entornosMap = [2 => 'Laboral', 3 => 'Educativo', 4 => 'Comunitario', 6 => 'Institucional'];
@endphp
@forelse (collect($calidadEntorno)->sortByDesc('porcentaje_error') as $cal)
    <div style="margin-bottom:14px;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:5px;">
            <span style="font-size:0.75rem; color:var(--shell-muted);">
                {{ $entornosMap[$cal['entorno']] ?? 'Otro' }}
            </span>
            <span style="font-size:0.75rem; font-weight:600; font-family:var(--shell-mono); color:var(--shell-text);">
                {{ $cal['porcentaje_error'] }}%
            </span>
        </div>
        <div class="adm-q-bar-track">
            <div class="adm-q-bar-fill {{ $cal['semaforo'] == 'danger' ? 'bg-danger' : '' }}"
                style="width:{{ $cal['porcentaje_error'] }}%;
                    background: {{ $cal['semaforo'] == 'danger' ? 'var(--shell-red)' : '#f59e0b' }};">
            </div>
        </div>
    </div>
@empty
    <p style="color:var(--shell-muted);text-align:center;font-size:0.8rem;padding:1rem 0;">Sin datos</p>
@endforelse
