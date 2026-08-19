<!DOCTYPE html>

<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">


    <title>Acta de entrega {{ $entorno }}</title>
    <style>
        * {
            padding: 0;
            margin: 0;
            box-sizing: border-box;
            --width-document: 650px !important;
        }

        body {
            font-family: Arial, sans-serif;
        }

        table,
        td,
        th {
            border: 1px solid black;
            border-collapse: collapse;
            padding: 3px;
            align-items: center;
            text-align: center;
        }

        .ObservacionesTable {
            max-width: 380px;
            min-width: 350px;

            border-collapse: collapse;
            padding: 3px;
        }

        .firma {
            min-width: 180px;
            border-collapse: collapse;
            padding: 3px;
        }

        .actividades {
            min-width: 200px;
            border-collapse: collapse;
            padding: 3px;

        }

        .nombre {
            min-width: 200px;
            border-collapse: collapse;
            padding: 3px;
        }

        th,
        td {
            text-align: left;
        }

        th {
            text-align: center;
            font-size: 0.9em;
        }

        td {
            font-size: 0.8em;
            padding: 3px 5px;
        }

        h1 {
            font-size: 20px;
        }

        h4 {
            font-weight: bold;
            font-size: 0.9em;
        }

        h5 {
            font-size: 0.6em;
            font-weight: normal;
        }

        h6 {
            font-weight: normal;
            font-size: 0.6em;
        }

        br {
            margin: 0 0;
        }

        ul {
            list-style-type: none;
        }

        span,
        li,
        p {
            font-size: 0.8em;
        }

        .border-left {
            border-left: 1px solid black;
        }

        .border-right {
            border-right: 1px solid black;
        }

        .border-top {
            border-top: 1px solid black;
        }

        .border-bottom {
            border-bottom: 1px solid black;
        }

        .border-x {
            border-left: 1px solid black;
            border-right: 1px solid black;
        }

        .border-y {
            border-top: 1px solid black;
            border-bottom: 1px solid black;
        }

        .text-center {
            text-align: center;
        }

        .row {
            display: inline-block;
            vertical-align: middle;
        }

        .text-left {
            text-align: left;
            line-height: normal;
            display: inline-block;
            vertical-align: middle;
        }

        .center-absolute {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        .left-absolute {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
        }

        .d-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            grid-gap: 3px;
        }

        .d-flex {
            display: flex;
        }

        .flex-center {
            justify-content: center;
            align-items: center;
        }

        .flex-start {
            justify-content: flex-start;
            align-items: center;
        }

        .flex-row {
            flex-direction: row;
        }

        .flex-column {
            flex-direction: column;
        }

        .flex-between {
            justify-content: space-between;
        }

        .w-20 {
            width: 20%;
        }

        .w-40 {
            width: 30%;
        }

        .w-50 {
            width: 50%;
        }

        .w-60 {
            width: 60%;
        }

        .w-100 {
            width: 100% !important;
        }

        .h-25 {
            height: 25%;
        }

        .h-50 {
            height: 50%;
        }

        .h-100 {
            height: 100%;
        }

        .h-100-px {
            height: 100px;
        }

        .h-55 {
            height: 55px;
        }

        .border-1 {
            border: 1px solid black;
            border-collapse: collapse;
        }

        .p-0 {
            padding: 0;
        }

        .px-0 {
            padding-left: 0;
            padding-right: 0;
        }

        .py-0 {
            padding-top: 0;
            padding-bottom: 0;
        }

        .pb-0 {
            padding-bottom: 0;
        }

        .py-2 {
            padding-top: 2px;
            padding-bottom: 2px;
        }

        .py-5-px {
            padding-top: 5px;
            padding-bottom: 5px;
        }

        .p-5-px {
            padding: 5px;
        }

        .py-10-px {
            padding-top: 10px;
            padding-bottom: 10px;
        }

        .pt-2 {
            padding-top: 2px;
        }

        .ps-2 {
            padding-left: 2px;
        }

        .mt-20-px {
            margin-top: 20px;
        }

        .mb-20-px {
            margin-bottom: 20px;
        }

        .position-relative {
            position: relative;
        }

        .fw-bold {
            font-weight: bold;
        }

        .table-compromises td {
            width: 162px;
        }

        .table-signatures td {
            padding-top: 10px;
            padding-bottom: 10px;
        }

        .table-title {
            font-size: 0.9 rem;
            font-weight: bold;
            margin-bottom: 10 px;
            text-align: center;
            color: #7f8c8d;
        }

    </style>

</head>

<body>
    <header class="h-100-px d-flex flex-row border-y border-x" style="width: 650px">
        <div class="text-center" style="width: 170px">
            {!! $htmlImage !!}
        </div>
        <div class="d-flex flex-column border-x px-0" style="width: 315px">
            <div class="d-flex flex-center w-100 p-0 h-50">
                <h1>ACTA DE REUNIÓN</h1>
            </div>

            <div class="d-flex flex-center flex-column w-100 h-50 pb-0 px-0 border-top py-2">
                <h5 class="text-center mb-2">SUBRED INTEGRADA DE SERVICIOS DE SALUD NORTE E.S.E</h5>
                <h5>GESTION DE CALIDAD</h5>
            </div>
        </div>
        <div class="p-0 flex-column" style="width: 165px">
            <div class="d-flex flex-start" style="height: 25px">
                <h6 class="ps-2">CODIGO: ES-GC-F-104</h6>
            </div>

            <div class="d-flex flex-start border-top" style="height: 25px">
                <h6 class="ps-2">VERSIÓN: 8</h6>
            </div>

            <div class="d-flex flex-start border-top" style="height: 25px">
                <h6 class="ps-2">PAGINA 1</h6>
            </div>

            <div class="d-flex flex-start border-top" style="height: 25px">
                <h6 class="ps-2">FECHA 31/10/2024</h6>
            </div>
        </div>
    </header>

    <section class="d-flex flex-column mt-20-px border-y border-x" style="width: 650px">
        <div class="d-grid border-bottom">
            <div class="border-right p-5-px">
                <span class="fw-bold">No. de acta</span>
                <span>{{ $year }}{{ $mes }}{{ $fechaFinal }}</span>
            </div>
            <div class="p-5-px">
                <span class="fw-bold">Fecha:</span><span> {{ $fecha }} </span>
            </div>
        </div>

        <div class="p-5-px">
            <span class="fw-bold">Reunión:</span>
            <span>Productividad – Gesi {{ $entorno_name }}</span>
        </div>
    </section>

    <section class="d-flex flex-column mt-20-px" style="width: 650px">
        <div class="w-100 p-5-px border-top border-x">
            <h4>ASISTENTES</h4>
        </div>

        <div>
            <table class="w-100">
                <thead>
                    <tr>
                        <th>NOMBRE</th>
                        <th>CARGO</th>
                        <th>ÁREA/ENTIDAD</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($digitadores as $digitador)
                        <tr>
                            <td>{{ ucwords(strtolower($digitador->user->name)) . ' ' . ucwords(strtolower($digitador->user->last_name)) }}</td>
                            <td class="firma"> {{ $digitador->user->entorno->entorno }} </td>
                            <td>{{ $digitador->user->role->name }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>

    <section class="d-flex flex-column mt-20-px" style="width: 650px">
        <div class="w-100 p-5-px border-x border-y">
            <h4>ORDEN DEL DÍA / AGENDA</h4>
        </div>

        <div class="w-100 border-x border-bottom p-5-px">
            <p>En cumplimiento de las disposiciones legales y contractuales, se lleva a cabo seguimiento al
                proceso de digitación y productividad correspondiente a la semana del {{ $fechaInicial }} al
                {{ $fechaFinal }}, los siguientes serán los puntos a tratar:
            </p>

            <br>

            <ul>
                <li>1. Evaluación de la productividad semanal.</li>
                <li>2. Cumplimiento de la meta en cuanto a calidad del dato</li>
            </ul>
        </div>
    </section>

    <section class="d-flex flex-column mt-20-px" style="width: 650px">
        <div class="w-100 p-5-px border-x border-top">
            <h4>REVISIÓN COMPROMISOS ANTERIORES</h4>
        </div>

        <table class="table-compromises">
            <thead>
                <tr>
                    <th>Responsable</th>
                    <th>Fecha</th>
                    <th colspan="2">Cumplimiento</th>
                    <th>Causa</th>
                </tr>
            </thead>

            <tbody>
                <tr>
                    <td></td>
                    <td></td>
                    <td class="text-center">Si_______</td>
                    <td class="text-center">No_______</td>
                    <td></td>
                </tr>
            </tbody>
        </table>
    </section>

    <section class="d-flex flex-column mt-20-px border-x border-y" style="width: 650px">
        <div class="w-100 p-5-px border-bottom">
            <h4>DESARROLLO DE LA REUNIÓN</h4>
        </div>

        <div class="p-5-px">
            <p>
                Se lleva a cabo seguimiento semanal a los colaboradores en mención del listado adjunto
                para comprobar la productividad respecto a cumplimiento en horas y calidad del dato
                digitado.
                Teniendo en cuenta la productividad reportada por cada uno de los digitadores y
                evaluada por el técnico responsable, se especifica de la siguiente manera
            </p>

            <div class="w-100 flex-center mt-20-px" style="display: flex; flex-direction: column; align-items: center;">
                <p class="table-title">Tabla 1: Relación de horas reportadas por cada digitador</p> 
                
                <table class="w-60">
                    <thead class="table-primary">
                        <tr>
                            <th>Nombre</th>
                            <th>Productividad</th>
                            <th>Actividades</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach ($Horas_digitador as $nombreDigitador => $totalHoras)
                        <tr>
                            <td class="nombre">{{ ucwords(strtolower($nombreDigitador)) }}</td> <!-- Nombre del digitador -->
                            <td>{{ number_format($totalHoras['horas'], 1) }} horas</td> <!-- Total de horas acumuladas -->
                            <td class="actividades">Digitación {{ $totalHoras['acciones'] }}</td> <!-- Lista de acciones -->
                        </tr>
                    @endforeach

                    </tbody>
                </table>
            </div>

            <p class="mt-20-px">
                2. De acuerdo a la validación de calidad realizada semanalmente, los hallazgos encontrados se cargan en
                el siguiente link, donde cada digitador realiza la respectiva corrección:
            </p>

            <div class="w-100 mt-20-px mb-20-px" style="display: flex; justify-content: center; align-items: center;">
                <table class="w-60">
                    <thead>
                        <tr>
                            <th>https://docs.google.com/spreadsheets/d/18IK7D2cLOEJkQ0w277mSrx_8FuD-M7DmLVG3Yc-K-Vg/edit?usp=sharing
                            </th>
                        </tr>
                    </thead>

                </table>
            </div>

            <p>
                Al corroborar que cada uno de los digitadores haya finalizado las correcciones, se procede a realizar la
                descarga de las bases para verificar que los hallazgos hayan sido subsanados. Basándose en la
                comparativa se puede determinar si el digitador cumplió exitosamente con el producto a entregar.
            </p>

            <br>

            <p>
                2.1. Al realizar la validación de bases de datos el día {{ $fechaFinal - 2 }}, se identifican los
                siguientes hallazgos de calidad, que en la mayoría de los casos corresponde a incorrecto
                diligenciamiento de las fichas de captura por parte del entorno. De acuerdo a esto se enlistan a
                continuación la cantidad de observaciones referidas por cada digitador
            </p>

            <div class="w-100 flex-center mt-20-px" style="display: flex; flex-direction: column; align-items: center;">
                <p class="table-title">Tabla 2: Cantidad de hallazgos encontrados por cada digitador</p> 
                <table class="w-60">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Cantidad</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($cantidadesAgrupadas as $nombre => $cantidad)
                            <tr>
                                <td>{{ucwords(strtolower( $nombre)) }}</td>
                                <td>{{ $cantidad }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <br>

            <div class="w-100 flex-center mt-20-px" style="display: flex; flex-direction: column; align-items: center;">
                <p class="table-title">Tabla 3: Relación de hallazgos encontrados</p>
                <table class="w-60">
                    <thead>
                        <tr>
                            <th>No Ficha</th>
                            <th>Formato</th>
                            <th>Digitado por</th>
                            <th>Observaciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($observaciones as $dato)
                            <tr>
                                <td>{{ $dato['receptions'] }}</td>
                                <td>{{ $dato['formato'] }}</td>
                                <td>{{ ucwords(strtolower($dato['nombreDig'])) }}</td>
                                <td class="ObservacionesTable">{{ $dato['observaciones'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>


            <br>
            <p>
                3. El día {{ $fechaFinal }} se realiza la corrección del dato con la información suministrada por el
                entorno en el aplicativo de digitación partiendo de los hallazgos identificados anteriormente.
            </p>
        </div>

    </section>

    <section class="mt-20-px" style="width: 650px">
        <table class="w-100">
            <thead>
                <tr>
                    <th class="w-50">COMPROMISOS DE ESTA REUNIÓN</th>
                    <th>RESPONSABLE</th>
                    <th>FECHA</th>
                </tr>
            </thead>

            <tbody>
                <tr>
                    <td>No aplica</td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td>No aplica</td>
                    <td></td>
                    <td></td>
                </tr>
            </tbody>
        </table>
    </section>

    <section class="d-flex flex-column mt-20-px" style="width: 650px">
        <div class="w-100 p-5-px border-x border-top">
            <h4>FIRMAS DE LOS ASISTENTES</h4>
        </div>

        <table class="table-signatures">
            <thead>
                <tr>
                    <th>NOMBRE</th>
                    <th>FIRMA</th>
                    <th>CORREO ELECTRÓNICO</th>
                </tr>
            </thead>

            <tbody>

                @foreach ($digitadores as $digitador)
                    <tr>
                        <td>{{ ucwords(strtolower($digitador->user->name)) . ' ' . ucwords(strtolower($digitador->user->last_name)) }}</td>
                        <td class="firma"></td>
                        <td>{{ $digitador->user->email }}</td>
                    </tr>
                @endforeach

            </tbody>
        </table>
</body>

</html>
