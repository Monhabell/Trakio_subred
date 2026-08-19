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
            <span>Actividades técnico {{ $entorno_name }}</span>
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
                    @foreach ($firmas_final as $firma)
                    <tr>
                        <td>
                            {{ ucwords(strtolower($firma->user->name ?? '')) . ' ' . ucwords(strtolower($firma->user->last_name ?? '')) }}
                        </td>
                        <td class="firma">{{$firma->user->role->name}}</td>
                        <td>{{ $firma->user->entorno->entorno ?? 'Sin correo' }}</td>
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
                @for ($i = 0; $i < 3; $i++)
                    <tr>
                        <td></td>
                        <td></td>
                        <td class="text-center">Si_______</td>
                        <td class="text-center">No_______</td>
                        <td></td>
                    </tr>
                @endfor
            </tbody>
        </table>
    </section>

    <section class="d-flex flex-column mt-20-px border-x border-y" style="width: 650px">
        <div class="w-100 p-5-px border-bottom">
            <h4>DESARROLLO DE LA REUNIÓN</h4>
        </div>

        <div class="p-5-px">
            <p>
                De acuerdo al lineamiento vigente para el entorno transversal GESI, a continuación, se
                especifican las actividades ejecutadas durante la semana del {{ $fechaInicial }} al {{ $fechaFinal }}.
            </p>

            <br>

            <ol>
                <li class="mb-20-px">
                    Recepción de formatos.
                    Se recibieron un total de {{ $cantidad_num }} formatos de captura por parte del entorno, realizando
                    el
                    respectivo proceso de precrítica.

                    <div class="w-100 flex-center mt-20-px"
                        style="display: flex; flex-direction: column; align-items: center;">

                        <p class="table-title">Tabla 1: Cantidad de formatos recibidos</p>

                        <table class="w-60 mb-3">
                            <thead class="table-primary">
                                <tr>
                                    <th>FORMATO</th>
                                    <th>CANTIDAD</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($cantidad_recibed as $nombreFormato => $data)
                                    <tr>
                                        <td class="nombre">{{ $nombreFormato }}</td> <!-- Nombre del digitador -->
                                        <td>{{ $data['cantidad'] }}</td> <!-- Total de horas acumuladas -->
                                    </tr>
                                @endforeach

                            </tbody>
                        </table>
                    </div>
                </li>

                <li class="mb-20-px">
                    <h3>2. Recepción de fichas de capturas digitadas por parte del digitador.</h3>
                    Se reciben fichas los días miércoles y viernes validando de igual forma la integridad de
                    las mismas e identificando hallazgos en cuanto al diligenciamiento de los formatos por
                    parte del entorno.

                    <div class="w-100 flex-center mt-20-px"
                        style="display: flex; flex-direction: column; align-items: center;">

                        <p class="table-title">Tabla 2: Revisión de observaciones encontradas por los digitadores</p>

                        <table class="w-60 mb-3">
                            <thead>
                                <tr>
                                    <th>No Ficha</th>
                                    <th>Formato</th>
                                    <th>Digitado Por</th>
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
                        <br>
                    </div>

                </li>

                <li class="mb-20-px">
                    <h3>3. Validación de bases de datos.</h3>
                    Para el día {{ $fechaFinal }} se validaron los datos ingresados en el aplicativo durante la
                    semana, para lo cual se realiza la retroalimentación a los digitadores y al entorno, con el
                    fin de realizar la respectiva corrección y verificación en el aplicativo.
                    El resultado de la validación, se carga en un drive compartido con los digitadores, donde
                    cada uno revisa las fichas digitadas y realiza la respectiva corrección.
                </li>

                <li class="mb-20-px">
                    <h3>4. Creación de consecutivos en la herramienta de control en aplicativo dispuesto por SDS.</h3> 
                    Posterior a la recepción de formatos y una vez hayan sido digitados de forma correcta,
                    se procede a ejecutar la creación de consecutivos en la herramienta de control en el
                    aplicativo dispuesto por SDS, dando como resultado la creación de los siguientes
                    formatos

                    <div class="w-100 flex-center mt-20-px"
                        style="display: flex; flex-direction: column; align-items: center;">

                        <p class="table-title">Tabla 3: Cantidad de fichas cargadas a la herramienta de control.</p>

                        <table class="w-60 mb-3">
                            <thead>
                                <tr>
                                    <th>FORMATO</th>
                                    <th>CANTIDAD CREADA</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($cantidad_creada_H as $nombreFormato2 => $data)
                                    <tr>
                                        <td class="nombre">{{ $nombreFormato2 }}</td> <!-- Nombre del digitador -->
                                        <td>{{ $data['cantidad_cread'] }}</td> <!-- Total de horas acumuladas -->
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <br>
                </li>

                <li class="mb-20-px">
                    <h3>4. Validacion de datos</h3> 
                    Los días 30 de octubre y 01 de noviembre se validaron los datos ingresados durante la semana en el aplicativo proporcionado por la Secretaría de Salud, brindando la retroalimentación correspondiente a los digitadores y al entorno sobre los hallazgos observados, con el fin de que sean corregidos. Esto permite realizar las correcciones necesarias y verificar la información en el aplicativo. El resultado de dicha validación puede consultarse en el siguiente enlace:
                    <div class="w-100 mt-20-px mb-20-px" style="display: flex; justify-content: center; align-items: center;">
                        <table class="w-60 mb-3">
                            <thead>
                                <tr>
                                    <th>https://docs.google.com/spreadsheets/d/13-z29iwfjw4XeJxRBuQL15gmac4-v3frQ_s6Y8Jdt1k/edit?gid=1341954942#gid=1341954942
                                    </th>
                                </tr>
                            </thead>
        
                        </table>
                    </div>

                    Los errores más recurrentes están relacionados con datos incoherentes del usuario, números de teléfono incorrectos y problemas en la redacción de textos. Sin embargo, es importante destacar que estos errores son inducidos por el diligenciamiento de los formatos por parte del profesional del entorno. Por esta razón, las observaciones ingresadas en el aplicativo de productividad cobran gran relevancia, ya que permiten realizar la retroalimentación correspondiente al entorno.
                </li>

                <li class="mb-20-px">
                    <h3>5. Devolución de formatos al entorno.</h3> 
                    Durante la semana en cuestión, se realiza devolución de un total de {{ $cantidad_devuelta_num }}
                    fichas de captura
                    debidamente digitadas y validadas, es decir, que estas hayan sido correctamente cargadas
                    al aplicativo dispuesto por SDS:


                    <div class="w-100 flex-center mt-20-px"
                        style="display: flex; flex-direction: column; align-items: center;">

                        <p class="table-title">Tabla 4: : Cantidad de fichas devueltas al entorno</p>

                        <table class="w-60 mb-3">
                            <thead>
                                <tr>
                                    <th>FORMATO</th>
                                    <th>CANTIDAD </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($cantidad_devuelta as $nombreFormato1 => $data)
                                    <tr>
                                        <td class="nombre">{{ $nombreFormato1 }}</td> <!-- Nombre del digitador -->
                                        <td>{{ $data['cantidad_dev'] }}</td> <!-- Total de horas acumuladas -->
                                    </tr>
                                @endforeach

                            </tbody>
                        </table>
                    </div>
                    <br>
                </li>
            </ol>
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
                @foreach ($firmas_final as $firma)
                    <tr>
                        <td>
                            {{ ucwords(strtolower($firma->user->name ?? '')) . ' ' . ucwords(strtolower($firma->user->last_name ?? '')) }}
                        </td>
                        <td class="firma"></td>
                        <td>{{ $firma->user->email ?? 'Sin correo' }}</td>
                    </tr>
                @endforeach

            </tbody>
        </table>
</body>

</html>