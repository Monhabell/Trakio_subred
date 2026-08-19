<!DOCTYPE html>

<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Acta de entrega {{ strtolower($environment) }}</title>
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

        .flex-start{
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

        .mt-80-px{
            margin-top: 80px;
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
            padding-top: 6px;
            padding-bottom: 6px;
        }
    </style>
</head>

<body>
    <section class="d-flex flex-column border-y border-x" style="width: 650px">
        <div class="d-grid border-bottom">
            <div class="border-right p-5-px">
                <span class="fw-bold">No. de acta</span>
            </div>
            <div class="p-5-px">
                <span class="fw-bold">Fecha:</span><span> {{ formatDMY($dates[2]) }} </span>
            </div>
        </div>

        <div class="p-5-px">
            <span class="fw-bold">Reunión:</span>
            <span>Entrega y recepción de fichas de captura, entorno {{ strtolower($environment) }}</span>
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
                    @foreach ($usersSignatures as $user)
                    <tr>
                        <td>{{ nameAndLastName($user->name, $user->last_name)}}</td>
                        <td>{{ $user->role->name }}</td>
                        <td>{{ $user->entorno->entorno }}</td>
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
            <ul>
                <li>1. Entrega de fichas y soportes a GESI</li>
                <li>2. Pre crítica de soportes</li>
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
                @for ($i = 0; $i < 3; $i++) <tr>
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
                A continuación, se relaciona la cantidad de fichas de captura entregadas, durante el periodo del {{
                formatDMY($dates[0]) }} al
                {{ formatDMY($dates[1]) }} por parte del entorno {{ strtolower($environment) }} (incluyendo
                actualizaciones y seguimientos). Así mismo se
                realiza la recepción y pre crítica de las fichas por parte de GESI, con las cantidades relacionadas y
                visibles
                desde el siguiente link:
                https://trakio.pro
            </p>

            <div class="w-100 d-flex flex-center mt-20-px">
                <table class="w-60">
                    <thead>
                        <tr>
                            <th>Formato</th>
                            <th>Cantidad</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (count($filesQuantity) > 0)
                            @foreach ($filesQuantity as $format)
                            <tr>
                                <td>{{ $format->bases->name }}</td>
                                <td class="text-center">{{ $format->total }}</td>
                            </tr>
                            @endforeach
                        @else 
                            <tr>
                                <td colspan="2" class="text-center">Sin datos</td>
                            </tr>
                        @endif    
                    </tbody>
                </table>
            </div>

            <p class="mt-20-px">
                De igual manera, se relaciona la cantidad de consecutivos generados en la herramienta de control,
                por cada uno de los formatos.
            </p>

            <div class="w-100 d-flex flex-center mt-20-px mb-20-px">
                <table class="w-60">
                    <thead>
                        <tr>
                            <th>Formato</th>
                            <th>Cantidad</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($formats as $key => $quantity)
                            @if ($quantity > 0)
                                <tr>
                                    <td>{{ $key }}</td>
                                    <td class="text-center">{{ $quantity }}</td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
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

    <section class="d-flex flex-column mt-20-px border-x border-y" style="width: 650px">
        <div class="w-100 p-5-px border-bottom">
            <h4>DECISIONES / CONCLUSIONES</h4>
        </div>

        <div class="w-100 p-5-px">
            <p>
                Se concluye que la cantidad de fichas de captura entregadas durante el periodo del <b>{{
                    formatDMY($dates[0]) }} al
                    {{ formatDMY($dates[1]) }}</b> fue de <b>{{ getTotalFromObject($filesQuantity) }}</b> de parte del
                entorno {{ strtolower($environment) }}. De igual manera, se concluye que la cantidad de
                consecutivos generados en la herramienta de control fue de <b>{{ getTotalFromArray($formats) }}</b> para
                el entorno {{ strtolower($environment) }}.
            </p>
        </div>
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
                @foreach ($usersSignatures as $user)
                <tr>
                    <td>{{ nameAndLastName($user->name, $user->last_name)}}</td>
                    <td class="w-40"></td>
                    <td>{{ $user->email }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </section>
</body>

</html>