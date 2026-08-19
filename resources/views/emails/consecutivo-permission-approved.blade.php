<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        body { margin: 0; padding: 0; width: 100% !important; }
        .container { max-width: 600px; margin: 0 auto; }
    </style>
</head>
<body style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; color: #ffffff; padding: 40px 0; background-color: #0d0d0d;">

    <table class="container" align="center" border="0" cellpadding="0" cellspacing="0" width="600"
        style="background-color: #1a1a1a; border-radius: 12px; overflow: hidden; border: 1px solid #333333;">

        {{-- CABECERA --}}
        <tr>
            <td style="background-color: #1a1a1a; padding: 36px 20px; text-align: center; border-bottom: 4px solid #02a500;">
                <h1 style="margin: 0; color: #ffffff; font-size: 26px; letter-spacing: 2px; text-transform: uppercase;">
                    Trakio<span style="color: #02a500;">.pro</span>
                </h1>
                <p style="color: #888888; font-size: 12px; margin-top: 8px; text-transform: uppercase; letter-spacing: 1px;">
                    Permiso de consecutivos · Mes anterior
                </p>
            </td>
        </tr>

        {{-- CUERPO --}}
        <tr>
            <td style="padding: 36px 30px;">

                <p style="font-size: 17px; color: #ffffff; margin: 0 0 6px;">
                    Hola, <strong>{{ $userName }}</strong>
                </p>

                <p style="font-size: 14px; color: #cccccc; line-height: 1.7; margin: 0 0 24px;">
                    Tu solicitud para registrar consecutivos con fecha de intervención del
                    <strong style="color: #ffffff;">mes anterior</strong> fue aprobada.
                </p>

                <table border="0" cellpadding="0" cellspacing="0" width="100%"
                    style="margin: 0 0 28px; background-color: #0f2b12; border-left: 4px solid #02a500; border-radius: 4px;">
                    <tr>
                        <td style="padding: 18px 20px;">
                            <p style="margin: 0 0 6px; font-size: 11px; text-transform: uppercase; letter-spacing: 1px; color: #888;">
                                Tiempo disponible
                            </p>
                            <p style="margin: 0; font-size: 24px; font-weight: 700; color: #02a500; letter-spacing: 1px;">
                                {{ $durationMinutes }} minutos
                            </p>
                            <p style="margin: 6px 0 0; font-size: 13px; color: #cccccc;">
                                Válido hasta <strong style="color:#fff;">{{ $expiresAt }}</strong>.
                                Después de este momento, el mes anterior se bloqueará nuevamente.
                            </p>
                        </td>
                    </tr>
                </table>

                <p style="font-size: 13px; color: #888; line-height: 1.6; margin: 0;">
                    Ingresa a la plataforma y dirígete a "Solicitar consecutivos" para registrar
                    tus consecutivos pendientes dentro de este tiempo.
                </p>

            </td>
        </tr>

        {{-- PIE --}}
        <tr>
            <td style="background-color: #000000; padding: 24px 30px; text-align: center; border-top: 1px solid #333333;">
                <p style="margin: 0; font-size: 11px; color: #555;">
                    Este es un mensaje automático generado por la plataforma Trakio.pro.<br>
                    © {{ date('Y') }} Todos los derechos reservados.
                </p>
            </td>
        </tr>

    </table>
</body>
</html>
