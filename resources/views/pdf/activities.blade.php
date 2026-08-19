<!DOCTYPE html>

<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Informe de actividades</title>
    <style>
        * {
            padding: 0;
            margin: 0;
            box-sizing: border-box;
            --width-document: 750px !important;
        }

        body {
            font-family: Arial, sans-serif;
            margin-left: 2%;
        }

        section {
            width: var(--width-document);
        }

        hr {
            color: 1px solid rgba(0, 0, 0, 0.527);
        }

        p {
            padding: 3px;
            text-align: justify;
        }

        strong,
        span {
            font-size: 8px;
        }

        strong {
            font-weight: bold;
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

        .border-1 {
            border: 1px solid black;
            border-collapse: collapse;
        }

        .text-center {
            text-align: center;
        }

        .row {
            display: inline-block;
            vertical-align: middle;
        }

        .d-flex {
            display: flex;
        }

        .align-items-center {
            align-items: center;
        }

        .flex-center {
            justify-content: center;
            align-items: center;
        }

        .flex-end {
            justify-content: flex-end;
            align-items: center;
        }

        .flex-row {
            flex-direction: row;
        }

        .flex-column {
            flex-direction: column;
        }

        .w-50 {
            width: 50%;
        }

        .w-100 {
            width: 100% !important;
        }

        .h-18-px {
            height: 18px;
        }

        .h-30-px {
            height: 25px;
        }

        .h-36-px {
            height: 36px;
        }

        .py-5-px {
            padding-top: 5px;
            padding-bottom: 5px;
        }

        .ps-5 {
            padding-left: 5px;
        }

        .ps-10 {
            padding-left: 10px;
        }

        .ps-15 {
            padding-left: 15px;
        }

        .mt-2-px {
            margin-top: 2px;
        }

        .fw-bold {
            font-weight: bold;
        }

        .container-dates strong,
        .container-dates span {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 35px;
        }

        .content-activity {
            /* white-space: pre-wrap; */
            padding: 5px;
        }

        .table-activities {
            width: 100%;
            border: 1px solid black;
            border-collapse: collapse;
            page-break-inside: auto;
        }

        .table-activities th {
            vertical-align: top;
            text-align: center;
            padding: 4px 6px;
            font-size: 9px;
        }

        p {
            padding: 3px;
        }

        .table-activities th,
        .table-activities td {
            padding: 6px 6px;
            border-top: 1px solid black;
            vertical-align: top;
        }

        .table-activities td {
            text-align: left;
        }

        .table-activities tr {
            page-break-inside: avoid;
        }

        .table-activities th {
            vertical-align: middle;
            height: 30px;
            font-size: 9px;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <section class="d-flex flex-column border-y border-x">
        <div class="border-bottom h-18-px">
            <div class="d-flex flex-row" style="width:100%">
                <div class="border-right d-flex align-items-center" style="width: 120px">
                    <strong class="ps-5">ÁREA Y/O SERVICIO:</strong>
                </div>

                <div style="width: 449px" class="d-flex flex-center h-18-px">
                    <span>
                        @if (mb_strtoupper($environment, 'UTF-8') == 'GESI')
                            GESI
                        @else
                            DIRECCIÓN DE GESTION DEL RIESGO EN SALUD - ENTORNO
                            {{ $contract_number === '9110-2024' ? 'CANALIZACIONES' : mb_strtoupper($environment, 'UTF-8') }}
                        @endif
                    </span>
                </div>

                <div class="border-x ps-15 d-flex align-items-center" style="padding-right: 13px">
                    <strong>UNIDAD:</strong>
                </div>

                <div class="ps-10 d-flex align-items-center">
                    <span>Salud pública - PIC</span>
                </div>
            </div>
        </div>

        <div class="h-36-px w-100 d-flex flex-row border-bottom">
            <div class="d-flex flex-column">
                <div class="h-18-px d-flex flex-row border-bottom" style="width: 430px">
                    <div class="border-right d-flex align-items-center" style="width: 120px">
                        <strong class="fw-bold ps-5">NO DE CONTRATO:</strong>
                    </div>
                    <div class="border-right d-flex flex-center" style="width: 310px">
                        <span>{{ $contract_number }}</span>
                    </div>
                </div>

                <div class="h-18-px d-flex flex-row" style="width: 430px">
                    <div class="border-right d-flex align-items-center" style="width: 160px">
                        <strong class="ps-5">NOMBRE DEL SUPERVISOR: </strong>
                    </div>

                    <div class="border-right d-flex flex-center" style="width: 270px">
                        @if ($contract_number === '7625-2024')
                            <span>INGRID PAOLA LOZANO TORRES</span>
                        @else
                            <span>SANDRA MIREYA SÁNCHEZ</span>
                        @endif


                    </div>

                </div>
            </div>

            <div class="h-36-px d-flex flex-center border-right" style="width: 105px">
                <strong class="text-center">PERIODO CERTIFICADO</strong>
            </div>

            <div class="d-flex flex-column container-dates" style="width: 210px">
                <div class="h-18-px d-flex flex-row border-bottom">
                    <strong class="border-right">Día</strong>
                    <strong class="border-right">Mes</strong>
                    <strong class="border-right" style="width: 36px">Año</strong>
                    <strong class="border-right">Día</strong>
                    <strong class="border-right">Mes</strong>
                    <strong style="width: 36px">Año</strong>
                </div>

                <div class="h-18-px d-flex flex-row">
                    <span class="border-right">{{ getDay($init_date) }}</span>
                    <span class="border-right">{{ getMonth($init_date) }}</span>
                    <span class="border-right" style="width: 36px">{{ getYear($init_date) }}</span>
                    <span class="border-right">{{ getDay($end_date) }}</span>
                    <span class="border-right">{{ getMonth($end_date) }}</span>
                    <span style="width: 36px">{{ getYear($end_date) }}</span>
                </div>

                {{-- <div class="h-36-px d-flex flex-center text-center">
                    <span class="w-100">{{ getTextMonthFromNumber(getMonth($init_date))." ".getYear($end_date) }}</span>
                </div> --}}
            </div>
        </div>

        <div class="h-18-px d-flex flex-row border-bottom">
            <div class="border-right d-flex align-items-center" style="width: 160px">
                <strong class="ps-5">NOMBRE DEL CONTRATISTA:</strong>
            </div>

            <div class="border-right text-center d-flex flex-center" style="width: 270px">
                <span>{{ mb_strtoupper($person_name, 'UTF-8') }}</span>
            </div>

            <div class="border-right d-flex flex-center" style="width: 105px">
                <strong>DOCUMENTO:</strong>
            </div>

            <div class="d-flex flex-center" style="width: 210px">
                <span>{{ $document }}</span>
            </div>
        </div>

        <div class="h-18-px d-flex flex-row border-bottom">
            <div class="border-right d-flex align-items-center" style="width: 160px">
                <strong class="ps-5">OBJETO DEL CONTRATO:</strong>
            </div>

            <div class="d-flex flex-center" style="width: 585px">
                <span>
                    @if (mb_strtoupper($environment, 'UTF-8') == 'GESI')
                        {{ mb_strtoupper($role, 'UTF-8') == 'TÉCNICO DE SISTEMAS' ? 'TECNICO II - TÉCNICO EN SISTEMAS' : 'TECNICO III - DIGITADOR(A)' }}
                    @else
                        {{ mb_strtoupper($role, 'UTF-8') }}
                    @endif
                </span>
            </div>
        </div>

        <div class="h-18-px d-flex flex-row">
            <div class="border-right d-flex align-items-center" style="width: 160px">
                <strong class="ps-5">TOTAL DE EJECUCIÓN (%):</strong>
            </div>

            <div class="border-right d-flex flex-center" style="width: 270px">
                <span>100%</span>
            </div>
        </div>
    </section>

    <section class="mt-2-px" style="margin-top: 5px; width: 750px;">
        <table class="table-activities">
            <tbody>
                <tr>
                    <th class="w-50 border-right text-center">
                        OBLIGACIONES ESPECÍFICAS
                    </th>
                    <th class="w-50 text-center">
                        ACTIVIDADES REALIZADAS
                    </th>
                </tr>

                @foreach ($text_specific_activities as $key => $specific_activity)
                    <tr>
                        <td class="border-right content-activity" style="font-size: {{ $fontSize ?? 10 }}px">
                            {{ trim($specific_activity) }}
                        </td>

                        <td class="content-activity" style="font-size: {{ $fontSize ?? 10 }}px">
                            {{ trim($text_activities_done[$key]) }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="w-100 border-bottom border-x py-5-px ps-5">
            <strong>OBSERVACIONES: </strong>

            @php
                $adicionales = [
                    '1000693246' => 61,
                    '1233501357' => 61,
                    '65781935' => 61,
                    '1018467532' => 19,
                    '51669685' => 17,
                    '52749225' => 24,
                    '52804740' => 24,
                    '93150605' => 17,
                    '1000254932' => 16,
                    '1013602476' => 24,
                    '1013685943' => 24,
                    '1032486056' => 20,
                    '1047219947' => 24,
                    '80176736' => 17,
                    '1000506973' => 24,
                    '1014662729' => 18,
                    '1021396476' => 20,
                    '1016111653' => 16,
                    '1026565287' => 16,
                    '1023895640' => 21,
                    '1015995680' => 21,
                    '1000005850' => 20,
                    '1000186067' => 20,
                    '52960559' => 20,
                    '80134170' => 20,
                    '1000383061' => 20,
                ];
            @endphp

            @if (@isset($adicionales[$document]))
                <span>Se certifican ({{ $adicionales[$document] }}) horas adicionales por ausencia de perfil</span>
            @endif
        </div>

    </section>


    <section>
        <div class="d-flex flex-row h-36-px">
            <div class="border-x d-flex flex-end" style="padding-right: 8px; width: 375px">
                <strong style="font-size: 11px">TOTAL A PAGAR (Número y letras): M/CTE ($)</strong>
            </div>

            <div class="d-flex flex-center border-right" style="width: 100px">
                <span style="font-size: 10px">${{ number_format($total_fee, 0, ',', '.') }}</span>
            </div>

            <div class="border-right text-center" style="padding-left: 8px; width: 275px">
                <span>{{ $total_fee_string }}</span>
            </div>
        </div>

        <div class="d-flex flex-row" style="height: 130px;">
            <div class="border-1 d-flex flex-center flex-column" style="width: 475px; padding-top: 40px">
                <hr style="width: 275px">
                <span style="font-size: 8px; margin-top: 5px">NOMBRE COMPLETO, CÉDULA Y FIRMA DEL CONTRATISTA</span>
                <span class="fw-bold"
                    style="margin-top: 8px; font-size: 9px">{{ mb_strtoupper($person_name) }}</span>
                <span class="fw-bold" style="margin-top: 5px; font-size: 9px">CC.
                    {{ mb_strtoupper($document) }}</span>
            </div>

            <div class="border-y border-right d-flex flex-center flex-column" style="width: 275px">
                <span>{{ getLastDayFromDate($init_date) }}</span>
                <hr style="width: 180px; margin-top: 50px">
                <span style="font-size: 8px; margin-top: 5px">Firma de recibido supervisor</span>

                <span class="fw-bold" style="margin-top: 8px; font-size: 9px">
                    @if ($contract_number === '7625-2024')
                        INGRID PAOLA LOZANO TORRES <br>
                    @else
                        SANDRA MIREYA SÁNCHEZ <br>
                    @endif


                </span>

                <span class="text-center" style="margin-top: 5px">

                    @if ($contract_number === '7625-2024')
                        DIRECTORA GESTION DEL RIESGO EN SALUD
                    @else
                        COORDINADORA PSPIC
                    @endif


                </span>

            </div>
        </div>

        <div class="border-bottom border-x w-100 text-center">
            <span style="font-size: 9px;">Nota: Este informe de obligaciones para aprobación estará sujeto a la
                certificación que expida el supervisor.</span>
        </div>
    </section>
</body>

</html>
