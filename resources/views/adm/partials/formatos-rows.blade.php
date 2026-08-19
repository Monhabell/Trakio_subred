@forelse ($reporteFormatos as $item)
    @php
        $desviacion = $item->tiempo_real_individual - $item->tiempo_esperado_individual;
        $porcentaje = $item->tiempo_esperado_individual > 0
            ? ($desviacion / $item->tiempo_esperado_individual) * 100
            : 0;
    @endphp
    <tr>
        <td>{{ $item->nombre_formato }}</td>
        <td>
            <span style="font-family:var(--shell-mono); font-size:0.78rem; color:var(--shell-muted);">
                {{ number_format($item->total_procesado) }}
            </span>
        </td>
        <td style="font-family:var(--shell-mono);">{{ number_format($item->paginas_promedio, 2) }}</td>
        <td style="font-family:var(--shell-mono);">{{ number_format($item->tiempo_real_individual, 2) }}</td>
        <td style="font-family:var(--shell-mono);">{{ number_format($item->tiempo_esperado_individual, 2) }}</td>
        <td style="font-family:var(--shell-mono);">{{ number_format($item->usuarios_promedio_receptions, 2) }}</td>
        <td data-value="{{ $desviacion }}">
            <span style="font-family:var(--shell-mono); font-size:0.8rem; color:{{ $desviacion > 0 ? 'var(--shell-red)' : '#2ecc71' }};">
                {{ $desviacion > 0 ? '+' : '' }}{{ number_format($desviacion, 2) }}m
            </span>
            <br>
            <span style="font-size:0.68rem; color:var(--shell-muted-2);">
                ({{ number_format($porcentaje, 1) }}%)
            </span>
        </td>
        <td>
            @if (abs($porcentaje) <= 15)
                <span class="adm-chip adm-chip-ok">Calibrado</span>
            @elseif($porcentaje > 15)
                <span class="adm-chip adm-chip-high">Aumentar tiempo</span>
            @else
                <span class="adm-chip adm-chip-low">Optimizar tiempo</span>
            @endif
        </td>
    </tr>
@empty
    <tr>
        <td colspan="8" style="text-align:center; padding:2rem; color:var(--shell-muted);">Sin datos de formatos</td>
    </tr>
@endforelse
