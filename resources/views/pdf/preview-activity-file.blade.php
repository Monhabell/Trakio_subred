<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Previsualización informe de actividades</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif !important;
            background-color: #9c9c9c;
            position: relative
        }

        .pdf-container {
            justify-content: center;
            gap: 10px; /* Espacio entre páginas */
            transform: scale(0.85); 
            transform-origin: top center; 
            left: -60px;
        }

        .observations{
            height: 30px;
            padding-left: 10px;
        }

        .pdf-content {
            width: 216mm;
            max-height: 279mm;
            min-height: 279mm;
            padding-top: 10mm;
            padding-left: 10mm;
            padding-right: 10mm;
            padding-bottom: 10mm;
            background-color: white;
            box-shadow: -10px 11px 10px rgb(0 0 0 / 34%);
            overflow: hidden;
            margin: 15px;

        }

        section{
            width: var(--width-document);
        }

        hr{
            color: 1px solid rgba(0, 0, 0, 0.527);
        }

        p{
            padding: 3px;
            text-align: justify;
            font-size: 9.2px;
            margin-bottom: auto !important;
            line-height: 1;
        }

        strong,
        span{
            font-size: 8px;
        }

        strong{
            font-weight: bold;
        }

        h6{
            margin-bottom: 0;
            font-weight: bold;
        }

        .border-right {
            border-right: 1px solid black;
            
        }

        .border-left{
            border-left: 1px solid black;
        }

        
        .border-top {
            border-top: 1px solid rgb(109, 109, 109) !important;
        }

        .border-bottom {
            border-bottom: 1px solid rgb(131, 131, 131) !important;
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
            place-items: center;
        }

        .flex-end{
            justify-content: flex-end;
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

        .w-50 {
            width: 50%;
        }

        .w-100 {
            width: 100% !important;
        }

        .h-18-px{
            height: 18px;
        }

        .h-36-px{
            height: 36px;
        }

        .py-5-px {
            padding-top: 5px;
            padding-bottom: 5px;
        }

        .ps-5{
            padding-left: 5px;
        }

        .ps-10{
            padding-left: 10px;
        }

        .ps-15{
            padding-left: 15px;
        }

        .mt-2-px {
            margin-top: 2px;
        }

        .fw-bold {
            font-weight: bold;
        }

        .container-dates strong,
        .container-dates span{
            display: flex;
            justify-content: center;
            align-items: center;
            width: 35px;
        }

        .content-activity{
            white-space: pre-wrap;
        }

        .options-adjust {
            background: #2a2a2a;
            border: 1px solid #444;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.5);
            padding: 1rem;

            z-index: 1000;
            display: flex;
            flex-direction: column;
            gap: 1rem;
            color: #ccc;
            transition: all 0.3s ease-in-out;
            margin-top: 25%;
        }

        .options-adjust .title {
            font-size: 1.5rem;
            font-weight: bold;
            color: #fff;
            margin: 0;
            text-align: center;
        }

        .options-adjust .subtitle {
            font-size: 1rem;
            color: #ffc107;
            text-align: center;
        }

        .options-adjust .description {
            font-size: 0.9rem;
            text-align: center;
            margin-bottom: 1rem;
        }

       

        .options-adjust .font-size-adjust {
            display: flex;
            justify-content: space-around;
            align-items: center;
            gap: 0.5rem;
        }

        .options-adjust .font-size-badge {
            background: rgba(255, 255, 255, 0.1);
            color: #ffc107;
            padding: 5px 10px;
            border-radius: 6px;
            font-weight: bold;
            font-size: 20px
        }

        .options-adjust .btn,
        .note-section .btn {
            background: linear-gradient(to bottom right, #6d181e, #b71d28);
            color: #fff;
            border: none;
            border-radius: 6px;
            padding: 0.5rem 1rem;
            cursor: pointer;
            font-weight: bold;
            transition: background 0.3s;
        }

        .options-adjust .btn:hover,
        .note-section .btn:hover {
            background: linear-gradient(to bottom right, #b71d28, #8b1e26);
            box-shadow: 0 4px 8px rgba(220, 53, 69, 0.5);
        }

        .note-section {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            align-items: center;
        }

        .note {
            font-size: 0.85rem;
            color: #ffc107;
            text-align: center;
            font-weight: bold;
        }

        .content-value-string{
            line-height: 1;
        }

        .btn-generate-report {
            width: 100%;
            text-align: center;
        }

        /* Adaptación a móvil */
        @media (max-width: 768px) {
            .options-adjust {
                position: static;
                width: 100%;
                top: 0;
                right: 0;
                transform: none;
                border-radius: 0;
                box-shadow: none;
                padding: 1rem 0.5rem;
                font-size: 20px
            }

            .options-adjust .title {
                font-size: 1.25rem;
            }

            .options-adjust .subtitle {
                font-size: 0.9rem;
            }

            .note-section {
                margin-top: 1rem;
                margin: 0 !important;
            }

            .contenedor_hojas {
                margin-top: 12% !important;
                width: 100% !important;
            }

            
        }

        .btn.disabled,
        .btn:disabled {
            background-color: #5f5f5f;
            opacity: 0.65;
        }

        @media (max-width: 876px) {
            .pdf-container{

                flex-direction: column;
                gap: 20px;
                transform: scale(0.4);
            }
            .subtitle, .description {
                display: none;
            }

            .container_lateral_menu{
                background-color: transparent !important;
            }

            .contenedor_hojas {
                margin-top: 50% !important;
            }


            .options-adjust{
                position: fixed;
                top: 0;
                font-size: 5rem;
                background-color: #2a2a2a5e;
                border: none;
            }
        }

        .bg-darrk_red{
            background-color: #6d181e;
            z-index: 200;
        }

        .container_lateral_menu{
            position: fixed; /* Mantener fijo en la pantalla */
            background-color: #818181;
            width: 20%;
            height: 100vh; /* Toda la altura de la pantalla */
            bottom: 0; /* Asegura que esté en la parte inferior */
            right: 0; /* Fijar a la izquierda en lugar de la derecha */
            z-index: 2; /* Asegura que esté por encima de otros elementos */
            padding-top: 5%;
            align-items: center;
            text-align: center;
        }

        .contenedor_hojas{
            width: 80%;
            background-color: #959393;
            align-items: center;
            text-align: center;
            display: flex;
            justify-content: center;
            margin-top: 1%;
        }

       

    </style>
</head>

<body>

    <div class="container_lateral_menu">
     
        <aside class="options-adjust">
            <h6 class="subtitle ">Ajustar tamaño de letra</h6>
            <span class="description d-md-block">Utiliza los botones para ajustar el tamaño del texto en la vista previa.</span>
            <div class="font-size-adjust">
                <button id="decrease-font" class="btn"><i class="fas fa-minus"></i> Reducir</button>
                <span id="current-font-size" class="font-size-badge"></span>
                <button id="increase-font" class="btn"><i class="fas fa-plus"></i> Aumentar</button>
            </div>
    
            <strong class="note">Una vez ajustado el tamaño de letra, presiona el botón "Generar Informe" para aplicar los cambios.</strong>
            
            <form action="{{ route('activities.generate') }}" method="POST">
                @csrf
                <input type="text" class="activities-input activities-input-readonly w-100" value="{{ mb_strtoupper($person_name, 'UTF-8') }}" name="person_name" hidden readonly required>
                <input type="text" class="activities-input activities-input-readonly w-100" value="{{ $contract_number }}" name="contract_number" hidden readonly required>
                <input type="number" class="activities-input activities-input-readonly w-100" value="{{ $document }}" name="document" hidden readonly required>
                <input type="text" class="activities-input activities-input-readonly w-100" value="{{ $user->role->name }}" name="role" hidden readonly required>
                <input type="number" class="activities-input activities-input-readonly w-100" name="total_fee" id="total_fee" value="{{ $total_fee }}" hidden required>
                <input type="date" class="activities-input activities-input-editable w-100" name="init_date" value="{{ $init_date }}" hidden>
                <input type="date" class="activities-input activities-input-editable w-100" name="end_date" value="{{ $end_date }}" hidden>
                <input type="text" value="{{ $total_fee_string }}" name="total_fee_string" hidden>
                <input type="number" id="fontSize" name= "fontSize" step="0.1" hidden>
                @foreach ($text_specific_activities as $key => $specific_activity)
                    <div class="d-flex flex-row" hidden>
                        <textarea class="w-50 border-right content-activity {{ $key != 0 ? 'border-top' : ''}}" name="text_specific_activities[]" hidden>{{ $specific_activity }}</textarea>
                        <textarea class="w-50 content-activity {{ $key != 0 ? 'border-top' : ''}}" name="text_activities_done[]" hidden>{{ $text_activities_done[$key] }}</textarea>
                    </div>
                @endforeach
        
                <div class="note-section" style="margin-top: 20px; margin-bottom: 40px;">
                    <button type="submit" class="btn btn-generate-report btn-loader" style="width: 50%">Generar Informe</button>
                </div>
            </form>
        
        </aside>

        
    </div>

    <nav class="navbar bg-darrk_red border-bottom border-body fixed-top" data-bs-theme="dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <img src="https://www.shutterstock.com/image-vector/pdf-icon-major-file-format-260nw-1903798216.jpg" alt="Logo" width="30" height="24" class="d-inline-block align-text-top">
                Ajuste de Documento - Informe de Actividades
              </a>
          <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarText" aria-controls="navbarText" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
          </button>
          <div class="collapse navbar-collapse" id="navbarText">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
              <li class="nav-item">
                <a class="nav-link active" aria-current="page" href="#">Regresar</a>
              </li>
            </ul>
           
          </div>
        </div>
    </nav>

    <div class=contenedor_hojas>

        <div id="pdf-container" class="pdf-container mt-5">
            <div id="pdf-content" class="pdf-content">
                <div id="page">
                    <header style="font-family: Arial, sans-serif;  display: flex; flex-direction: row; border: 1px solid black; max-width: 750px; box-sizing: border-box;">
                        <div style="height: 60px; text-align: center; width: 150px;">
                            <img src="{{ asset('img/logoNorte.png') }}" style="margin-top: 10px" alt ="Logo norte" width = "120">
                        </div>
    
                        <div style="display: flex; flex-direction: column; border-left: 1px solid black; border-right: 1px solid black; padding: 0; width: 443px;">
                            <div style="display: flex; justify-content: center; align-items: center; width: 100%; padding: 0; height: 30px;">
                                <h1 style="margin: 0; font-size: 11px;">INFORME DE EJECUCIÓN DE CONTRATO DE PRESTACIÓN DE SERVICIOS</h1>
                            </div>
                            <div style="display: flex; justify-content: center; align-items: center; flex-direction: column; width: 100%; height: 30px; padding-bottom: 0; padding-left: 0; padding-right: 0; border-top: 1px solid black;">
                                <h5 style="text-align: center; margin-top: 0; margin-bottom: 3px; font-size: 10px; font-weight: normal;">SUBRED INTEGRADA DE SERVICIOS DE SALUD NORTE E.S.E</h5>
                                <h5 style="margin: 0; font-size: 10px; font-weight: normal;">GESTIÓN CONTRACTUAL</h5>
                            </div>
                        </div>
    
                        <div style="padding: 0; display: flex; flex-direction: column; width: 140px;">
                            <div style="display: flex; justify-content: flex-start; align-items: center; height: 15px;">
                                <h6 style="padding-left: 5px; margin: 0; font-size: 9px; font-weight: normal;">CODIGO: AP-CT-F-50</h6>
                            </div>
                            <div style="display: flex; justify-content: flex-start; align-items: center; height: 15px; border-top: 1px solid black;">
                                <h6 style="padding-left: 5px; margin: 0; font-size: 9px; font-weight: normal;">VERSIÓN: 4</h6>
                            </div>
                            <div style="display: flex; justify-content: flex-start; align-items: center; height: 15px; border-top: 1px solid black;">
                                <h6 style="padding-left: 5px; margin: 0; font-size: 9px; font-weight: normal;">PAGINA <span class="pageNumber"></span> de <span class="totalPages"></span></h6>
                            </div>
                            <div style="display: flex; justify-content: flex-start; align-items: center; height: 15px; border-top: 1px solid black;">
                                <h6 style="padding-left: 5px; margin: 0; font-size: 9px; font-weight: normal;">FECHA: 07/11/2024</h6>
                            </div>
                        </div>
                    </header>
    
                    <section class="d-flex flex-column border-y border-x">
                        <div class="border-bottom h-18-px">
                            <div class="d-flex flex-row" style="width:100%">
                                <div class="border-right d-flex align-items-center" style="width: 120px">
                                    <strong class="ps-1">ÁREA Y/O SERVICIO:</strong>
                                </div>
    
                                <div style="width: 449px" class="d-flex flex-center h-18-px">
                                    <span>
                                        @if (mb_strtoupper($environment, 'UTF-8') == 'GESI')
                                        GESI
                                        @else
                                        DIRECCIÓN DE GESTION DEL RIESGO EN SALUD - ENTORNO {{ mb_strtoupper($environment, 'UTF-8') }}
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
                                        <strong class="fw-bold ps-1">NO DE CONTRATO:</strong>
                                    </div>
                                    <div class="border-right d-flex flex-center" style="width: 310px">
                                        <span>{{ $contract_number }}</span>
                                    </div>
                                </div>
    
                                <div class="h-18-px d-flex flex-row" style="width: 430px">
                                    <div class="border-right d-flex align-items-center" style="width: 160px">
                                        <strong class="ps-1">NOMBRE DEL SUPERVISOR:</strong>
                                    </div>
                                    <div class="border-right d-flex flex-center" style="width: 270px">
                                        <span>SANDRA MIREYA SÁNCHEZ</span>
                                    </div>
                                    {{-- <div class="border-right d-flex flex-center" style="width: 270px">
                                        <span>INGRID PAOLA LOZANO TORRES</span>
                                    </div> --}}
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
                                <strong class="ps-1">NOMBRE DEL CONTRATISTA:</strong>
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
                                <strong class="ps-1">OBJETO DEL CONTRATO:</strong>
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
                                <strong class="ps-1">TOTAL DE EJECUCIÓN (%):</strong>
                            </div>
    
                            <div class="border-right d-flex flex-center" style="width: 270px">
                                <span>100%</span>
                            </div>
                        </div>
                    </section>
    
                    <section id="activities-container"  class="border-1 mt-2-px d-flex flex-column" style="margin-top: 5px">
                        <div class="h-36-px d-flex flex-row border-bottom">
                            <div class="w-50 d-flex flex-center border-right">
                                <h6>OBLIGACIONES ESPECÍFICAS</h6>
                            </div>
                            <div class="w-50 d-flex flex-center">
                                <h6>ACTIVIDADES REALIZADAS</h6>
                            </div>
                        </div>
    
                        @foreach ($text_specific_activities as $key => $specific_activity)
                            <div class="d-flex flex-row border-bottom border-left border-right border-top ">
                                <p class="w-50 border-right content-activity {{ $key != 0 ? 'border-top' : ''}}">{{ $specific_activity }}</p>
                                <p class="w-50 content-activity {{ $key != 0 ? 'border-top' : ''}}">{{ $text_activities_done[$key] }}</p>
                            </div>
                        @endforeach
    
                        <div class="w-100 border-bottom border-x observations d-flex flex-start">
                            <strong>OBSERVACIONES:</strong>
                        </div>
    
                        <div class="d-flex flex-row h-36-px">
                            <section class="border-x d-flex flex-end" style="padding-right: 8px; width: 375px">
                                <strong style="font-size: 11px">TOTAL A PAGAR (Número y letras): M/CTE ($)</strong>
                            </section>
    
                            <section class="d-flex flex-center" style="width: 100px">
                                <span style="font-size: 10px">${{ number_format($total_fee, 0, ',', '.') }}</span>
                            </section>
    
                            <section class="border-right text-center" style="padding-left: 8px; width: 275px">
                                <span class="line-h-1 d-flex flex-center">{{ $total_fee_string }}</span>
                            </section>
                        </div>
    
                        <div class="d-flex flex-row" style="height: 130px;">
                            <section class="border-1 d-flex flex-center flex-column" style="width: 475px">
                                <hr style="width: 180px; margin-top: 50px">
                                <span style="font-size: 8px; margin-top: 5px">NOMBRE COMPLETO, CÉDULA Y FIRMA DEL CONTRATISTA</span>
                                <span class="fw-bold" style="margin-top: 8px; font-size: 9px">{{ mb_strtoupper($person_name) }}</span>
                                <span class="fw-bold" style="margin-top: 5px; font-size: 9px">CC. {{ mb_strtoupper($document) }}</span>
                            </section>
    
                            <section class="border-y border-right d-flex flex-center flex-column" style="width: 275px">
                                <span>{{ getLastDayFromDate($init_date) }}</span>
                                <hr style="width: 180px; margin-top: 50px">
                                <span style="font-size: 8px; margin-top: 5px">Firma de recibido supervisor</span>
                                
                                <span class="fw-bold" style="margin-top: 8px; font-size: 9px">
                                    SANDRA MIREYA SÁNCHEZ<br>
                                </span>

                                <span class="text-center" style="margin-top: 5px">
                                    COORDINADORA PSPIC
                                </span>
                                
                                <!-- <span class="fw-bold" style="margin-top: 8px; font-size: 9px">INGRID PAOLA LOZANO TORRES</span>

                                <span class="text-center" style="margin-top: 5px">
                                    DIRECTORA GESTION DEL RIESGO EN SALUD
                                </span> -->
                            </section>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

    <script>
        function resizeFont() {
            const fontSizeDisplay = document.getElementById('current-font-size');
            const decreaseBtn = document.getElementById('decrease-font');
            const increaseBtn = document.getElementById('increase-font');
            const paragraphs = document.getElementsByTagName('p');
            const fontSizeInput = document.getElementById('fontSize');

            // Verificar si el valor existe y es un número válido
            let fontSize = parseFloat(localStorage.getItem('fontSize'));
            if (isNaN(fontSize)) {
                fontSize = 4; // Valor predeterminado
            }

            // Actualizar la interfaz inicial
            fontSizeDisplay.textContent = `${fontSize.toFixed(1)}px`;
            fontSizeInput.value = fontSize.toFixed(1);
            Array.from(paragraphs).forEach(paragraph => {
                paragraph.style.fontSize = `${fontSize.toFixed(1)}px`;
            });

            // Función para actualizar el tamaño de fuente
            const updateFontSize = (change) => {
                fontSize += change;
                fontSize = Math.max(4, Math.min(24, fontSize.toFixed(1)));
                localStorage.setItem('fontSize', fontSize);

                // Refrescar la página para aplicar los cambios
                location.reload();
            };

            // Asignar eventos a los botones
            decreaseBtn.addEventListener('click', () => updateFontSize(-0.1));
            increaseBtn.addEventListener('click', () => updateFontSize(0.1));
        }

        function paginatePdf () {
            const limitHeight = 23.4;
            const pageContainer = document.getElementById('page');
            const pdfContainer = document.getElementById('pdf-container');
            const activitiesContainer = document.getElementById('activities-container');
            const heightInPixels = pageContainer.offsetHeight;
            const heightInCentimeters =  convertPixelsToCm(heightInPixels)
            const numberPagesToCreate = Math.ceil(heightInCentimeters / limitHeight) - 1 ;

            let currentPage = 0
            while (currentPage < numberPagesToCreate ){
                const newpage = document.createElement('div');
                newpage.classList.add('pdf-content');

                const elementos = activitiesContainer.querySelectorAll('div');
                let alturaAcumulada = 0;
                let contenidoDesbordado  = "";

                for (const element of elementos) {
                    const heightElement = element.offsetHeight;
                    const heightElementCm =  convertPixelsToCm(heightElement);

                    alturaAcumulada += heightElementCm;

                    if (alturaAcumulada > limitHeight) {
                        contenidoDesbordado = element.outerHTML;
                        activitiesContainer.removeChild(element);
                        newpage.innerHTML += contenidoDesbordado;
                    }
                }

                pdfContainer.appendChild(newpage);
                currentPage++;
            }
        }

        function convertPixelsToCm(alturaPx) {
            const dpi = 96;
            const pulgadas = alturaPx / dpi;
            const centimetros = pulgadas * 2.54;
            return centimetros;
        }

        function buttonsLoader() {
            const btns = document.querySelectorAll('.btn-loader');
            btns.forEach(btn => {
                btn.addEventListener('click', () => {
                    const form = btn.closest('form');

                    if (form) {
                        if (form.checkValidity()) {
                            btn.innerHTML = `<i class="fa-solid fa-circle-notch fa-spin" style="color: #ffffff;"></i> Generando PDF`;
                            btn.classList.add('disabled');
                        }else{
                            form.reportValidity();
                        }
                    }
                });
            });
        }

        buttonsLoader();
        resizeFont();
        paginatePdf();
    </script>
</body>
</html>
