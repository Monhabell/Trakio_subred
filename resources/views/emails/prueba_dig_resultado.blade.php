<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <style>
        body { margin: 0; padding: 0; width: 100% !important; }
        .container { max-width: 640px; margin: 0 auto; }
    </style>
</head>

<body style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color:#F8F8F7; padding: 40px 0;">
    <table class="container" align="center" border="0" cellpadding="0" cellspacing="0" width="640"
        style="background-color: #ffffff; border-radius: 12px; overflow: hidden; border: 1px solid #D3D1C7;">

        <tr>
            <td style="background-color: #185FA5; padding: 30px 20px; text-align: center;">
                <h1 style="margin: 0; color: #ffffff; font-size: 22px;">Prueba de Digitación · Ficha SSC</h1>
                <p style="color: #E6F1FB; font-size: 12px; margin-top: 6px; text-transform: uppercase; letter-spacing: 1px;">
                    Subred Norte · Entornos Más Bienestar
                </p>
            </td>
        </tr>

        <tr>
            <td style="padding: 30px 30px 10px;">
                <p style="font-size: 15px; color: #2C2C2A;">
                    <strong>Digitador:</strong> {{ $digitador }}<br>
                    <strong>Calificación final:</strong> {{ $score }} / 100<br>
                    <strong>Tiempo total:</strong> {{ intdiv($totalSecs, 60) }}m {{ $totalSecs % 60 }}s<br>
                    <strong>Fecha:</strong> {{ date('d/m/Y H:i') }}
                </p>
            </td>
        </tr>

        @if ($mecanografia)
        <tr>
            <td style="padding: 10px 30px;">
                <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #D3D1C7;border-radius:10px;overflow:hidden;">
                    <tr>
                        <td style="background-color:#E6F1FB;padding:10px 14px;font-size:14px;font-weight:bold;color:#185FA5;">
                            Mecanografía — {{ $mecanografia['pct'] }}%
                            <span style="float:right;font-weight:normal;font-size:12px;color:#5F5E5A;">
                                {{ $mecanografia['correct'] }}/{{ $mecanografia['total'] }} caracteres correctos · {{ intdiv($mecanografia['elapsed'], 60) }}m {{ $mecanografia['elapsed'] % 60 }}s
                                @if($mecanografia['timeout']) · Tiempo agotado @endif
                            </span>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        @endif

        @foreach ($fichas as $f)
        <tr>
            <td style="padding: 10px 30px;">
                <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #D3D1C7;border-radius:10px;overflow:hidden;">
                    <tr>
                        <td style="background-color:#F1EFE8;padding:10px 14px;font-size:14px;font-weight:bold;color:#2C2C2A;">
                            {{ $f['titulo'] }} — {{ $f['pct'] }}%
                            <span style="float:right;font-weight:normal;font-size:12px;color:#5F5E5A;">
                                {{ $f['correct'] }} correctos · {{ $f['wrong'] }} errores · {{ intdiv($f['elapsed'], 60) }}m {{ $f['elapsed'] % 60 }}s
                                @if($f['timeout']) · Tiempo agotado @endif
                            </span>
                        </td>
                    </tr>
                    @if (count($f['errores']))
                    <tr>
                        <td style="padding:10px 14px;">
                            @foreach ($f['errores'] as $e)
                            <div style="font-size:12px;padding:5px 0;border-bottom:1px solid #F1EFE8;">
                                <strong>{{ $e['field'] }}:</strong>
                                <span style="color:#A32D2D;">✗ "{{ $e['user'] ?: '(vacío)' }}"</span>
                                &nbsp;→&nbsp;
                                <span style="color:#0F6E56;">✓ "{{ $e['expected'] }}"</span>
                            </div>
                            @endforeach
                        </td>
                    </tr>
                    @endif
                </table>
            </td>
        </tr>
        @endforeach

        <tr>
            <td style="background-color: #F8F8F7; padding: 20px 30px; text-align: center; border-top: 1px solid #D3D1C7;">
                <p style="margin: 0; font-size: 11px; color: #888780;">
                    Mensaje automático generado por el sistema de evaluación de digitación.
                </p>
            </td>
        </tr>
    </table>
</body>

</html>
