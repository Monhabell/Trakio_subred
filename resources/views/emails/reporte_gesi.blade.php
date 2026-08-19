<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <style>
        /* Estilos básicos para asegurar visualización en móviles */
        body {
            margin: 0;
            padding: 0;
            width: 100% !important;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
        }
    </style>
</head>

<body style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;  color: #ffffff; padding: 40px 0;">
    <table class="container" align="center" border="0" cellpadding="0" cellspacing="0" width="600"
        style="background-color: #1a1a1a; border-radius: 12px; overflow: hidden; border: 1px solid #333333;">

        <tr>
            <td
                style="background-color: #1a1a1a; padding: 40px 20px; text-align: center; border-bottom: 4px solid #e63946;">
                <h1 style="margin: 0; color: #ffffff; font-size: 28px; letter-spacing: 2px; text-transform: uppercase;">
                    Trakio<span style="color: #e63946;">.pro</span>
                </h1>
                <p
                    style="color: #888888; font-size: 12px; margin-top: 10px; text-transform: uppercase; letter-spacing: 1px;">
                    Reporte de Avance GESI
                </p>
            </td>
        </tr>

        <tr>
            <td style="padding: 40px 30px;">
                <p style="font-size: 18px; color: #ffffff; margin-bottom: 20px;">Hola,</p>
                <p style="font-size: 15px; color: #cccccc; line-height: 1.6;">
                    Se ha generado con éxito el reporte de <strong>Avance de Digitación</strong>. Los datos adjuntos
                    corresponden al procesamiento consolidado de la semana actual.
                </p>

                <table border="0" cellpadding="15" cellspacing="0" width="100%"
                    style="margin: 30px 0; background-color: #262626; border-left: 4px solid #e63946; border-radius: 4px;">
                    <tr>
                        <td style="color: #ffffff; font-size: 14px;">
                            <strong style="color: #e63946;">Fecha:</strong> {{ date('d/m/Y') }}<br>
                            <strong style="color: #e63946;">Hora:</strong> {{ date('H:i A') }}<br>
                            <strong style="color: #e63946;">Sistema:</strong> Trakio.pro
                        </td>
                    </tr>
                </table>

                <p style="font-size: 15px; color: #cccccc; line-height: 1.6;">
                    El archivo Excel adjunto contiene el desglose detallado por <strong>Entorno</strong> y
                    <strong>Formulario</strong>.
                </p>

                <div style="text-align: center; margin-top: 40px;">
                    <span
                        style="display: inline-block; padding: 12px 24px; border: 1px solid #e63946; color: #e63946; border-radius: 30px; font-size: 13px; font-weight: bold; text-transform: uppercase;">
                        Archivo adjunto incluido
                    </span>
                </div>
            </td>
        </tr>

        <tr>
            <td style="background-color: #000000; padding: 30px; text-align: center; border-top: 1px solid #333333;">
                <p style="margin: 0; font-size: 12px; color: #666666;">
                    Este es un mensaje automático generado por la plataforma Trakio.pro.<br>
                    © {{ date('Y') }} Todos los derechos reservados.
                </p>
            </td>
        </tr>
    </table>
</body>

</html>
