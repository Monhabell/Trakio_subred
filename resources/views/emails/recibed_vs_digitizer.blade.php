<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cantidades Digitadas - Cantidades recibidas</title>
    <style>
        /* Aquí puedes agregar tu CSS personalizado */
        body {
            font-family: Arial, sans-serif;
            color: #333;
        }
        .header {
            background-color: #f2f2f2;
            padding: 20px;
            text-align: center;
        }
        .content {
            padding: 20px;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 12px;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Reporte de Cantidad VS Digitizer</h1>
    </div>
    <div class="content">
        <p>Estimado,</p>
        <p>Se ha generado el siguiente reporte:</p>

        <table border="1" cellpadding="10" cellspacing="0" style="width: 100%;">
            <tr>
                <th>Entorno</th>
                <th>Cantidad Entregada</th>
                <th>Cantidad Digitada</th>

            </tr>
            <tr>
                <td>Hogar</td>
                <td>{{ $variable1 }}</td>
                <td>{{ $cantDigEd }}</td>
            </tr>
            <tr>
                <td>Laboral</td>
                <td>{{ $variable2 }}</td>
                <td>{{ $cantDigEd }}</td>
            </tr>
            <tr>
                <td>Comunitario</td>
                <td>{{ $variable3 }}</td>
                <td>{{ $cantDigEd }}</td>
            </tr>
            <tr>
                <td>Educativo</td>
                <td>{{ $variable4 }}</td>
                <td>{{ $cantDigEd }}</td>
            </tr>
            <tr>
                <td>Institucional</td>
                <td>{{ $variable6 }}</td>
                <td>{{ $cantDigEd }}</td>
            </tr>
        </table>

        <p>Gracias por su atención.</p>
    </div>
    <div class="footer">
        <p>Este correo es generado automáticamente. No responda a este mensaje.</p>
    </div>
</body>
</html>
