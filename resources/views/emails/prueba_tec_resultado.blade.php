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
                <h1 style="margin: 0; color: #ffffff; font-size: 22px;">Prueba Técnico de Sistemas</h1>
                <p style="color: #E6F1FB; font-size: 12px; margin-top: 6px; text-transform: uppercase; letter-spacing: 1px;">
                    Subred Norte · Entornos Más Bienestar
                </p>
            </td>
        </tr>

        <tr>
            <td style="padding: 30px 30px 10px;">
                <p style="font-size: 15px; color: #2C2C2A;">
                    <strong>Candidato:</strong> {{ $candidato }}<br>
                    <strong>Calificación final:</strong> {{ $score }} / 100<br>
                    <strong>Tiempo total:</strong> {{ intdiv($totalSecs, 60) }}m {{ $totalSecs % 60 }}s<br>
                    <strong>Fecha:</strong> {{ date('d/m/Y H:i') }}
                </p>
            </td>
        </tr>

        @foreach ($modulos as $m)
        <tr>
            <td style="padding: 10px 30px;">
                <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #D3D1C7;border-radius:10px;overflow:hidden;">
                    <tr>
                        <td style="background-color:#F1EFE8;padding:10px 14px;font-size:14px;font-weight:bold;color:#2C2C2A;">
                            {{ $m['titulo'] }} — {{ $m['pct'] }}%
                            <span style="float:right;font-weight:normal;font-size:12px;color:#5F5E5A;">
                                {{ $m['correct'] }} correctas · {{ $m['wrong'] }} incorrectas
                            </span>
                        </td>
                    </tr>
                    @if (count($m['errores']))
                    <tr>
                        <td style="padding:10px 14px;">
                            @foreach ($m['errores'] as $e)
                            <div style="font-size:12px;padding:5px 0;border-bottom:1px solid #F1EFE8;">
                                <strong>{{ $e['pregunta'] }}</strong><br>
                                <span style="color:#A32D2D;">✗ "{{ $e['user'] ?: '(sin respuesta)' }}"</span>
                                &nbsp;→&nbsp;
                                <span style="color:#0F6E56;">✓ "{{ $e['correct'] }}"</span>
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
                    Mensaje automático generado por el sistema de evaluación técnica.
                </p>
            </td>
        </tr>
    </table>
</body>

</html>
