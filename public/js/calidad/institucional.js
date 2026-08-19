

// Cache-busting por versión manual: sube CALIDAD_JS_VERSION cada vez que publiques
// un cambio en los módulos de /js/calidad/ (incluye http/*.js) para que el navegador
// y la extensión Code Injector descarguen la versión nueva de una, en vez de servir
// una copia cacheada. El .htaccess deja el Cache-Control listo para que, entre
// versiones, el navegador siga aprovechando la caché normalmente.
const CALIDAD_JS_VERSION = '2';

async function importModule(path, moduleName = 'Module') {

    const isLocal = location.hostname === "127.0.0.1" || location.hostname === "localhost";
    const cacheBuster = `?v=${CALIDAD_JS_VERSION}`;

    const urls = isLocal
        ? [
            `http://127.0.0.1:8000/js/calidad/${path}${cacheBuster}`,
            `https://www.trakio.pro/js/calidad/${path}${cacheBuster}`
        ]
        : [
            `https://www.trakio.pro/js/calidad/${path}${cacheBuster}`,
            `http://127.0.0.1:8000/js/calidad/${path}${cacheBuster}`
        ];

    let ModuleInstance = null;

    for (const url of urls) {
        try {
            const { default: Module } = await import(url);
            ModuleInstance = new Module();
            break;
        } catch (error) {
            console.warn(`Falló la importación desde: ${url}`);
        }
    }

    if (!ModuleInstance) {
        console.error(`No se pudo importar el módulo: ${path}`);
    }

    return {
        [moduleName]: ModuleInstance,
    };
}


/**
 * VARIABLES GLOBALES
 */

//COLORES

const bgError = "rgba(255, 50, 50, 0.4)";
const bgSuccess = "rgba(50, 255, 50, 0.7)";
const bgWarning = "rgba(255, 255, 50, 0.7)";
const bgPrimary = "rgba(90, 100, 255, 0.5)";
const bgWhite = "#fff";
const bgDisabled = "rgba(128, 128, 128, 0.2)";




const FECHA_ACTUAL = new Date();
const ANIO_ACTUAL = FECHA_ACTUAL.getFullYear();
const MES_ACTUAL = FECHA_ACTUAL.getMonth();

//DATOS FICHA
const FECHA_INTERVENCION = document.querySelector("#FechaIntervencion");
const CONSECUTIVO_FICHA = document.querySelector("#Consecutivo_fic");
const LOCALIDAD_FICHA = document.querySelector("#Localidad_fic");
const BASE_FICHA = document.querySelector("#Id_Base");


const bgTransparent = "rgba(255, 255, 255, 0) ";

//FECHAS
const PATRON_DMY = "[0-9]{2}/[0-9]{2}/[0-9]{4}";
const PATRON_YMD = "[0-9]{4}-[0-9]{2}-[0-9]{2}";



try {
    const { sesiones } = await importModule('institucional/sesiones.js', 'sesiones');
} catch (error) {
    console.error("Error cargando modulos", error);
}


try {
    const { Calculo } = await importModule('http/Calculo.js', 'Calculo');

    /**
     * Calcula el tiempo de digitación de la ficha
     * @returns null
     */
    async function calcularTiempoDigitacion() {
        const btnNewFicha = document.querySelector("#main_body > div > div > main > div > div > div > div.panel-heading > div > form:nth-child(2) > input.form-control.btn.btn-success");
        const btnReturn = document.querySelector("#main_body > div > div > main > div > div > div > div.panel-heading > div > div.col-md-2 > table > tbody > tr > td > form > button");
        const btnsEditFicha = document.querySelectorAll("button[name='btnEditReg']");

        btnsEditFicha.forEach(btnEditFicha => {
            btnEditFicha.addEventListener("click", (e) => {
                e.preventDefault();
                Calculo.iniciar(true);
                const formEdit = btnEditFicha.closest("form");
                setTimeout(() => {
                    formEdit.submit(true);
                }, 1000);
            });
        });

        if (!Calculo) {
            console.error("No se pudo importar el módulo de tiempos");
            return;
        }

        if (btnNewFicha) {
            btnNewFicha.addEventListener("click", () => {
                Calculo.iniciar(true);
            });
        }

        if (btnReturn) {
            btnReturn.addEventListener("click", () => {
                Calculo.finalizar();
            });
        }

        const menuItems = document.querySelectorAll('.sidebar-submenu a');

        menuItems.forEach(item => {
            item.addEventListener("click", () => {
                // Verificamos que el objeto Calculo exista para evitar errores en consola
                if (typeof Calculo !== 'undefined' && typeof Calculo.finalizar === 'function') {
                    console.log("Navegación detectada: Finalizando cálculo de GESI...");
                    Calculo.finalizar();
                }
            });
        });

        if (FECHA_INTERVENCION && CONSECUTIVO_FICHA && LOCALIDAD_FICHA && BASE_FICHA) {
            Calculo.iniciar();
        }
    }

    async function saveProductivity() {
        //Buttons
        const { Productivity } = await importModule('http/Productivity.js', 'Productivity');
        const btnGuardar = document.querySelector("#botonActualizarInformacion");
        const btnOk = document.querySelector("#main_body > div.swal2-container.swal2-center.swal2-shown > div > div.swal2-actions > button.swal2-confirm.swal2-styled");

        //Values
        const numFicha = document.querySelector("#Ficha_fic");
        const fechaIntervencion = document.querySelector("#FechaIntervencion");
        const sds_base_id = document.querySelector("#Id_Base");
        const page_id = document.querySelector("input[name='Id_seccion']");
        const user_sds = document.querySelector("input[name='paginaUsuario']");

        const save = async () => {
            Productivity.sds_base_id = sds_base_id.value;
            if (typeof Calculo.liberarReservaPendiente === 'function') Calculo.liberarReservaPendiente();
            Productivity.typingSeconds = Calculo.totalTypingSeconds;
            Productivity.inactivitySeconds = Calculo.consinactivitySeconds;
            Productivity.numFicha = numFicha.value;
            Productivity.fechaIntervencion = fechaIntervencion.value;
            Productivity.sectionId = page_id.value;
            Productivity.userSds = user_sds.value;

            btnGuardar.disabled = true;
            const res = await Productivity.store();
            return res;
        }

        if (btnGuardar) {
            btnGuardar.addEventListener("click", async (e) => {
                e.preventDefault();
                const res = await save();

                if (res && res.status === 201) {
                    btnGuardar.disabled = true;
                    const form = btnGuardar.closest("form");
                    // Validar los datos antes de enviar el formulario
                    if (form.checkValidity()) {
                        form.submit();
                    } else {
                        btnGuardar.disabled = false;
                        alert("Por favor, complete todos los campos requeridos.");
                    }
                } else {
                    btnGuardar.disabled = false;
                    alert(res.data.message);
                }
            });
        }

        // if (btnOk) {
        //     btnOk.addEventListener("click", async (e) => {
        //         e.preventDefault();
        //         await save();
        //     });
        // }
    }

    saveProductivity();
    calcularTiempoDigitacion();
} catch (error) {
    console.error("No se pudo importar el módulo de tiempos", error);
}


// -------------------------END--------------------------------


/* ------------------------------------------------------------------------
---------------------GENERAL FUNCTIONS AND VARIABLES ----------------------
--------------------------------------------------------------------------*/

function validarLongitudMinCampo(inputId, longitud) {
    return document.getElementById(inputId).value.trim().length < longitud;
}

function longitudMinCampoDOM(campoId, longitud) {
    let colorinput, activar;
    if (validarLongitudMinCampo(campoId, longitud)) {
        colorinput = bgError;
        activar = 0;
    } else {
        colorinput = bgSuccess;
        activar = 1;
    }
    document.getElementById(campoId).style.backgroundColor = colorinput;
    toggleBtnActualizacion(activar);
}

function validarLongitudTel(inputId) {
    const telefono = document.getElementById(inputId);
    let bgInput;
    let estadoBtn;
    if (
        telefono.value.trim().length === 7 ||
        telefono.value.trim().length === 10
    ) {
        bgInput = bgSuccess;
        estadoBtn = 1;
    } else {
        bgInput = bgError;
        estadoBtn = 0;
    }

    telefono.style.backgroundColor = bgInput;
    toggleBtnActualizacion(estadoBtn);
}

function date1LessDate2(date1, date2) {
    return new Date(date1) < new Date(date2);
}

function disabledBtnActualizar(estado) {
    try {
        const btnActualizar = document.querySelector("#botonActualizarInformacion");
        btnActualizar.disabled = !estado;
    } catch (error) { }
}

function setAttrInputsFromContainer(pathContainers, attribute, valueAttribute) {
    let container, inputsContainer;
    if (Array.isArray(pathContainers)) {
        pathContainers.forEach((pathContainer) => {
            container = document.querySelector(pathContainer);
            inputsContainer = container.querySelectorAll("input, select");
            inputsContainer.forEach((input) => {
                if (input.getAttribute("type") != "hidden") {
                    input.setAttribute(attribute, valueAttribute);
                }

                if (attribute === "value" && valueAttribute === "") {
                    clearValueInputs(input.id);
                }
            });
        });
    } else {
        container = document.querySelector(pathContainers);
        inputsContainer = container.querySelectorAll("input, select");
        inputsContainer.forEach((input) => {
            if (input.getAttribute("type") != "hidden") {
                input.setAttribute(attribute, valueAttribute);
            }

            if (attribute === "value" && valueAttribute === "") {
                clearValueInputs(input.id);
            }
        });
    }
}

function clearValueInputs(inputId) {
    const input = document.getElementById(inputId);
    let labelInput = input.tagName.toLowerCase();
    if (labelInput === "select") {
        input.selectedIndex = -1;
    } else {
        input.value = "";
    }
}

function resizeMultiplesSelects() {
    const selectsMultiple = document.querySelectorAll("select[multiple]");
    selectsMultiple.forEach((item) => {
        item.style.height = "200px";
    });
}

function removeAttrInputsFromContainer(pathContainers, attribute) {
    let container, inputsContainer;
    if (Array.isArray(pathContainers)) {
        pathContainers.forEach((pathContainer) => {
            container = document.querySelector(pathContainer);
            inputsContainer = container.querySelectorAll("input, select");
            inputsContainer.forEach((input) => {
                if (input.getAttribute("type") != "hidden") {
                    input.removeAttribute(attribute);
                }
            });
        });
    } else {
        container = document.querySelector(pathContainers);
        inputsContainer = container.querySelectorAll("input, select");
        inputsContainer.forEach((input) => {
            if (input.getAttribute("type") != "hidden") {
                input.removeAttribute(attribute);
            }
        });
    }
}

function setAttributeInput(inputId, attribute, attrValue) {
    if (Array.isArray(inputId)) {
        for (const input of inputId) {
            document.getElementById(input).setAttribute(attribute, attrValue);
        }
    } else {
        document.getElementById(inputId).setAttribute(attribute, attrValue);
    }
}

function removeAttributeInput(inputId, attribute) {
    if (Array.isArray(inputId)) {
        for (const input of inputId) {
            document.getElementById(input).removeAttribute(attribute);
        }
    } else {
        document.getElementById(inputId).removeAttribute(attribute);
    }
}

function disabledOptionsFromSelect(
    idSelect,
    optionsValueToDisabled,
    booleanDisabled
) {
    const select = document.getElementById(idSelect);
    const optionsSelect = select.querySelectorAll("option");

    // Deshabilitar todas las opciones al principio
    optionsSelect.forEach((option) => {
        option.disabled = !booleanDisabled;
    });

    if (Array.isArray(optionsValueToDisabled)) {
        // Si optionsValueToDisabled es un array
        optionsSelect.forEach((option) => {
            if (optionsValueToDisabled.includes(option.value)) {
                option.disabled = booleanDisabled;
            }
        });
    } else {
        // Si optionsValueToDisabled es un solo valor
        optionsSelect.forEach((option) => {
            if (option.value == optionsValueToDisabled) {
                option.disabled = booleanDisabled;
                option.selected = !booleanDisabled;
            }
        });
    }
}

function onlyEnabledOptionFromSelect(idSelect, optionsValueToEnabled) {
    const select = document.getElementById(idSelect);
    const optionsSelect = select.querySelectorAll("option");

    if (Array.isArray(optionsValueToEnabled)) {
        for (let i = 0; i < optionsValueToEnabled.length; i++) {
            optionsSelect.forEach((option) => {
                if (option.value != optionsValueToEnabled[i]) {
                    option.disabled = true;
                } else {
                    option.selected = "true";
                    option.disabled = false;
                }
            });
        }
    } else {
        optionsSelect.forEach((option) => {
            if (option.value != optionsValueToEnabled) {
                option.disabled = true;
            } else {
                option.selected = "true";
                option.disabled = false;
            }
        });
        select.style.backgroundColor = bgSuccess;
    }
}

function dispararEvento(inputId, evento) {
    let campo = document.getElementById(inputId);
    let newEvento = new Event(evento);
    campo.dispatchEvent(newEvento);
}

function formatoYMD(fecha) {
    let fechaArray = fecha.split("/");
    let dia = fechaArray[0];
    let mes = fechaArray[1];
    let anio = fechaArray[2];

    return anio + "-" + mes + "-" + dia;
}

function campo1DependienteDel2(inputId1, inputId2, validador) {
    const campo2 = document.getElementById(inputId2);
    const campo1 = document.getElementById(inputId1);
    if (campo2.value === validador) {
        campo1.required = true;
        campo1.disabled = false;
    } else {
        campo1.required = false;
        campo1.disabled = true;
        clearValueInputs(inputId1);
    }
    bgCamposLlenos();
}

function toggleBtnActualizacion(estado) {
    let btnActualizar = document.querySelector("#botonActualizarInformacion");
    estado == 1 ? (btnActualizar.disabled = false) : false;
    estado == 0 ? (btnActualizar.disabled = true) : false;
}

function readonlyInputs(element) {
    if (
        element.tagName.toLowerCase() === "input" ||
        element.tagName.toLowerCase() === "select"
    ) {
        element.setAttribute("readonly", "true");
    } else {
        console.error("No es un input");
    }
}

function eventListeners(inputId, evento, funcion) {
    let input = document.getElementById(inputId);
    input.addEventListener(evento, funcion);
}

function bgCamposLlenos() {
    let campos = document.querySelectorAll(
        "select, input:not([type='submit']), textarea"
    );
    campos.forEach((campo) => {
        const computedStyle = window.getComputedStyle(campo);
        const backgroundColorInput = computedStyle.backgroundColor;

        if (
            (campo.value === "" || campo.value.trim() === "") &&
            !campo.disabled &&
            campo.required
        ) {
            campo.style.backgroundColor = bgWarning;
        } else if (
            (campo.value !== "" || campo.value.trim() !== "") &&
            !campo.disabled &&
            backgroundColorInput != bgError
        ) {
            campo.style.backgroundColor = bgSuccess;
        } else if (campo.disabled) {
            campo.style.backgroundColor = bgDisabled;
        } else if (!campo.required &&
            !campo.disabled &&
            backgroundColorInput != bgError
        ) {
            campo.style.backgroundColor = bgWhite;
        }
    });
}

function eventListenersCamposLlenos() {
    let campos = document.querySelectorAll("select, input, textarea");
    campos.forEach((campo) => {
        campo.addEventListener("change", bgCamposLlenos);
    });
}

function validarManzanaCuidado(idManzana, idNroManzana) {
    const manzana = document.getElementById(idManzana);
    const nroManzana = document.getElementById(idNroManzana);
    if (manzana.value == "Si") {
        nroManzana.disabled = false;
        nroManzana.required = true;
    } else {
        nroManzana.disabled = true;
        nroManzana.required = false;
        nroManzana.value = "";
    }
    bgCamposLlenos();
}

function getValueInput(inputId) {
    return document.getElementById(inputId).value;
}

const patronCoordenadaX = "^-?74.d+$";
const patronCoordenadaY = "^4.d+$";

function setPatternCoordinate(idInputX, idInputY) {
    document.getElementById(idInputX).pattern = patronCoordenadaX;
    document.getElementById(idInputY).pattern = patronCoordenadaY;
}

function validarTelefono(inputId) {
    const telefono = document.getElementById(inputId);
    let bgInput, stateBtn;
    if (telefono.value.length != 7 && telefono.value.length != 10) {
        bgInput = bgError;
        stateBtn = false;
    } else {
        bgInput = bgSuccess;
        stateBtn = true;
    }
    telefono.style.backgroundColor = bgInput;
    disabledBtnActualizar(stateBtn);
}


const letraBlanca = "#fff";
const letraNegra = "#000";

const patronDMY = "[0-9]{2}/[0-9]{2}/[0-9]{4}";
const patronYMD = "[0-9]{4}-[0-9]{2}-[0-9]{2}";



/* ------------------------------------------------------------------------
--------------------------------TÍTULO CODE--------------------------------
--------------------------------------------------------------------------*/
conditionsForAll();

function conditionsForAll() {
    //Setup heading
    const heading = document.querySelector(
        "#main_body > div > div > main > div > div > div > div.panel-heading"
    );
    heading.removeAttribute("style");
    heading.style.backgroundColor = "#522200";
    heading.style.color = "#fff";

    const logo = document.querySelector(
        "#sidebar > div > div.sidebar-brand.logo_blanco"
    );
    logo.style.backgroundColor = "#00B6FF";
    logo.style.color = "#fff";
    logo.innerHTML = "<h3>CODE INSTITUCIONAL</h3>";

    //Deshabilitar autocompletar
    const inputs = document.querySelectorAll("input");
    inputs.forEach((input) => {
        input.setAttribute("autocomplete", "off");
    });
}

/* ------------------------------------------------------------------------
----------------------------CREACIÓN DE FICHA------------------------------
--------------------------------------------------------------------------*/

const BTN_REGRESAR = document.querySelector(".BtnGrid.Bnts");

function getMonthIntervention() {
    const arrayFechaIntervencion = FECHA_INTERVENCION.value.split("/");
    const mesIntervencion = parseInt(arrayFechaIntervencion[1]);

    return mesIntervencion;
}

function getYearIntervention() {
    const arrayFechaIntervencion = FECHA_INTERVENCION.value.split("/");
    const anioIntervencion = parseInt(arrayFechaIntervencion[2]);

    return anioIntervencion;
}

CONSECUTIVO_FICHA.addEventListener("keyup", validarCamposCreacionFicha);
FECHA_INTERVENCION.addEventListener("change", validarCamposCreacionFicha);
LOCALIDAD_FICHA.addEventListener("change", validarCamposCreacionFicha);

function validarCamposCreacionFicha() {
    const consecutivoValido = CONSECUTIVO_FICHA.value.length === 5;
    const fechaValida =
        getMonthIntervention() === 8 && getYearIntervention() === 2026;
    CONSECUTIVO_FICHA.style.backgroundColor = consecutivoValido ?
        bgSuccess :
        bgError;
    FECHA_INTERVENCION.style.backgroundColor = fechaValida ? bgSuccess : bgError;
    disabledBtnActualizar(consecutivoValido && fechaValida);
}

try {
    //id ficha visible
    for (let i = 1; i < 10; i++) {
        let idFichaVisible = document.querySelector(
            `#main_body > div > div > main > div > div > div > div.panel-body > table > tbody > tr:nth-child(${i}) > td:nth-child(1) > form > input[type=hidden]:nth-child(4)`
        );
        idFichaVisible.type = "text";
        idFichaVisible.style.borderRadius = "20px";
    }
} catch (error) { }

/* ------------------------------------------------------------------------
------------------------VALIDACIÓN POBLACIONAL-----------------------------
--------------------------------------------------------------------------*/
try {
    const tipoDocPob = document.querySelector("#valorPoblacionalTipoDocumento");
    const numDocPob = document.querySelector("#valorPoblacionalDocumento");
    const nacionalidadPob = document.querySelector(
        "#valorPoblacionalNacionalidad"
    );
    const nacionalidadPobOptions = nacionalidadPob.querySelectorAll("option");
    const sexoPob = document.querySelector("#valorPoblacionalSexo");
    const generoPob = document.querySelector("#valorPoblacionalGenero");
    const estadoCivilPob = document.querySelector("#valorPoblacionalEstadoCivil");
    const fechaNacPob = document.querySelector(
        "#valorPoblacionalFechaNacimiento"
    );
    const edadPob = document.querySelector("#valorPoblacionalEdad");
    const pdiPob = document.querySelector(
        "#valorPoblacionalPoblacionDiferencialInclusion"
    );
    const catDiscapacidad = document.querySelector(
        "#valorPoblacionalCategoriaDiscapacidad"
    );
    const etniaPob = document.querySelector("#valorPoblacionalEtnia");
    const afiliacionSGSSPob = document.querySelector(
        "#valorPoblacionalAfiliacionSGSSS"
    );
    const nombreEAPBPob = document.querySelector("#valorPoblacionalNombreEAPB");
    const tipoDocsCol = "59, 60, 61, 63";
    const tipoDocsSN = "65, 66";

    document
        .getElementById("valorPoblacionalDocumento")
        .addEventListener("keyup", function () {
            setTimeout(validarPoblacional, 2500);
        });
    eventListeners("valorPoblacionalTipoDocumento", "change", validarPoblacional);
    eventListeners("valorPoblacionalNacionalidad", "change", validarPoblacional);
    eventListeners("valorPoblacionalSexo", "change", sexGen);
    eventListeners("valorPoblacionalGenero", "change", genSex);
    eventListeners(
        "valorPoblacionalFechaNacimiento",
        "change",
        validarPoblacional
    );
    eventListeners("valorPoblacionalEstadoCivil", "change", validarPoblacional);
    eventListeners(
        "valorPoblacionalPoblacionDiferencialInclusion",
        "change",
        validarPoblacional
    );
    eventListeners(
        "valorPoblacionalCategoriaDiscapacidad",
        "change",
        validarPoblacional
    );

    const basesConPoblacional = "64, 67, 55, 56";
    if (basesConPoblacional.includes(BASE_FICHA.value)) {
        setTimeout(validarPoblacional(), 1300);
    }

    function validarPoblacional() {
        requiredFillPoblacional();
        //------//Documento vs nacionalidad vs PDI//------//
        if (tipoDocsCol.includes(tipoDocPob.value)) {
            disabledOptionsFromSelect(
                "valorPoblacionalPoblacionDiferencialInclusion",
                "2618",
                true
            );
            nacionalidadPobOptions.forEach((option) => {
                if (option.value !== "50") {
                    onlyEnabledOptionFromSelect("valorPoblacionalNacionalidad", "50");
                    option.selected = false;
                } else {
                    option.disabled = false;
                    option.selected = true;
                }
            });
        } else {
            disabledOptionsFromSelect(
                "valorPoblacionalPoblacionDiferencialInclusion",
                "2620",
                true
            );
            nacionalidadPobOptions.forEach((option) => {
                if (option.value === "50") {
                    option.disabled = true;
                    option.selected = false;
                } else {
                    option.disabled = false;
                }
            });
            pdiPob.style.backgroundColor = bgSuccess;
        }
        nacionalidadPob.style.backgroundColor =
            nacionalidadPob.value == "" ? bgWarning : bgSuccess;

        //------//Tipo documento vs número documento//------//
        const typeNumDocPob = tipoDocsSN.includes(tipoDocPob.value) ?
            "text" :
            "number";
        setAttributeInput("valorPoblacionalDocumento", "type", typeNumDocPob);
        if (tipoDocPob.value == "60" || tipoDocPob.value == "61") {
            numDocPob.setAttribute("min", "1000000000");
            numDocPob.setAttribute("max", "1999999999");
        } else {
            numDocPob.removeAttribute("min");
            numDocPob.removeAttribute("max");
        }

        //Validar edad vs documento
        validarEdadDocumento();

        //------//Patrón campo de fecha//------//
        setAttributeInput("valorPoblacionalFechaNacimiento", "pattern", patronYMD);

        //------//Longitud nombres//------//
        setTimeout(() => {
            const nombresObligatorios = [
                "valorPoblacionalPrimerNombre",
                "valorPoblacionalPrimerApellido",
            ];
            for (const nombreCampo of nombresObligatorios) {
                longitudMinCampoDOM(nombreCampo, 3);
            }
        }, 1500);

        setAttrInputsFromContainer(
            "#formularioNuevoFormato > div:nth-child(12) > div > div:nth-child(7)",
            "onchange",
            "longitudMinCampoDOM(this.id, 3)"
        );

        //Sexo y género
        sexGen();
        genSex();

        validarEstadoCivil();
        categoriasDiscapacidad();

        //Afiliación a SGSSS
        validarAfiliacionSGSS();
        afiliacionSGSSPob.addEventListener("change", () => {
            validarAfiliacionSGSS();
            nombreEAPBPob.removeAttribute("disabled");
        });

        //Etnia
        validarEtniaPob();
        eventListeners("valorPoblacionalEtnia", "change", validarEtniaPob);

        //Quitar requerido ocupación
        removeAttrInputsFromContainer(
            "#formularioNuevoFormato > div:nth-child(12) > div > div:nth-child(10) > div:nth-child(3)",
            "required"
        );
        bgCamposLlenos();
    }

    function requiredFillPoblacional() {
        for (let i = 8; i < 10; i++) {
            setAttrInputsFromContainer(
                `#formularioNuevoFormato > div:nth-child(12) > div > div:nth-child(${i})`,
                "required",
                "true"
            );
        }
    }

    //Estado civil y fecha de nacimiento
    function validarEstadoCivil() {
        estadoCivilPob.required = true;
        let bgFechNac, bgEstadoCivil;
        if (fechaNacPob.value == "" || fechaNacPob.value == "0000-00-00") {
            estadoCivilPob.readOnly = "1";
            estadoCivilPob.value = "";
            bgEstadoCivil = bgWarning;
            bgFechNac = bgError;
        } else {
            if (
                date1LessDate2(fechaNacPob.value, formatoYMD(FECHA_INTERVENCION.value))
            ) {
                estadoCivilPob.disabled = false;
                bgFechNac = bgSuccess;
                if (edadPob.value < 14) {
                    onlyEnabledOptionFromSelect("valorPoblacionalEstadoCivil", "78");
                    estadoCivilPob.value = "78";
                    bgEstadoCivil = bgSuccess;
                } else if (edadPob.value >= 14) {
                    const optionsEstadoCivilPob =
                        estadoCivilPob.querySelectorAll("option");
                    if (estadoCivilPob.value === "" || estadoCivilPob.value === null) {
                        bgEstadoCivil = bgWarning;
                        estadoCivilPob.disabled = false;
                    } else {
                        estadoCivilPob.disabled = false;
                        bgEstadoCivil = bgSuccess;
                    }
                    optionsEstadoCivilPob.forEach((option) => {
                        if (option.value == "78") {
                            option.disabled = true;
                            option.selected = false;
                        } else {
                            option.disabled = false;
                        }
                    });
                }
            } else {
                fechaNacPob.value = "";
                bgFechNac = bgError;
                alert("Por favor revise la fecha de nacimiento");
            }
        }
        fechaNacPob.style.backgroundColor = bgFechNac;
        estadoCivilPob.style.backgroundColor = bgEstadoCivil;
    }

    function validarEdadDocumento() {
        const edad = getValueInput("valorPoblacionalEdad");
        const comunDocs = ["64", "1639", "1640", "2482"];
        const docMenoresSiete = comunDocs.concat("60", "66");
        const docSiete = docMenoresSiete.concat("61");
        const docOchoADiesiciete = comunDocs.concat("61", "66", "1637", "1638");
        const docMayoredEdad = comunDocs.concat("59", "62", "65", "1638", "1637");

        if (edad < 7 && !docMenoresSiete.includes(tipoDocPob.value)) {
            console.error("Error");
        } else if (edad == 7 && !docSiete.includes(tipoDocPob.value)) {
            console.error("Error");
        } else if (
            edad >= 7 &&
            edad < 18 &&
            !docOchoADiesiciete.includes(tipoDocPob.value)
        ) {
            console.error("Error");
        } else if (edad >= 18 && !docMayoredEdad.includes(tipoDocPob.value)) {
            console.error("Error");
        } else {
            console.log("%cTipo documento OK", "color: green; font-weight: bold;");
        }
    }

    function sexGen() {
        let generoValue;
        if (sexoPob.value == "67") {
            generoValue = "70";
        } else if (sexoPob.value == "68") {
            generoValue = "71";
        }
        generoPob.value = generoValue;
        generoPob.style.backgroundColor =
            generoPob.value == "" ? bgError : bgSuccess;
    }

    function genSex() {
        let sexoValue;
        if (generoPob.value == "70") {
            sexoValue = "67";
        } else if (generoPob.value == "71") {
            sexoValue = "68";
        }
        sexoPob.value = sexoValue;
        sexoPob.style.backgroundColor = generoPob.value == "" ? bgError : bgSuccess;
    }

    function categoriasDiscapacidad() {
        if (pdiPob.value != "108") {
            catDiscapacidad.value = "";
            catDiscapacidad.setAttribute("disabled", "true");
            catDiscapacidad.style.backgroundColor = bgWhite;
        } else {
            catDiscapacidad.disabled = false;
            catDiscapacidad.style.backgroundColor =
                catDiscapacidad.value == "" ? bgWarning : bgSuccess;
            catDiscapacidad.setAttribute("required", "true");
        }
    }

    function validarEtniaPob() {
        if (etniaPob.value === "84" || etniaPob.value === "") {
            onlyEnabledOptionFromSelect("valorPoblacionalHablaEspanol", "-1");
            removeAttributeInput("valorPoblacionalPueblo", "required");
            onlyEnabledOptionFromSelect("valorPoblacionalPueblo", "");
            setAttributeInput("valorPoblacionalPueblo", "value", "");
        } else {
            disabledOptionsFromSelect("valorPoblacionalHablaEspanol", "-1", true);
            setAttributeInput("valorPoblacionalPueblo", "required", "true");
            disabledOptionsFromSelect("valorPoblacionalPueblo", "", true);
        }
        bgCamposLlenos();
    }

    //Afiliación a SGSSS
    function validarAfiliacionSGSS() {
        const afiliacionSGSS = getValueInput("valorPoblacionalAfiliacionSGSSS");
        if (afiliacionSGSS === "" || afiliacionSGSS === "0") {
            setAttributeInput("valorPoblacionalNombreEAPB", "required", true);
        }
        if (afiliacionSGSS === "135") {
            setAttributeInput("valorPoblacionalNombreEAPB", "readonly", "1");
            nombreEAPBPob.value = "NO ASEGURADO";
        } else if (afiliacionSGSS === "") {
            removeAttributeInput("valorPoblacionalNombreEAPB", "required");
            setAttributeInput("valorPoblacionalNombreEAPB", "readonly", "1");
            nombreEAPBPob.value = "";
        } else if (afiliacionSGSS !== "135" || afiliacionSGSS !== "") {
            if (nombreEAPBPob.value == "NO ASEGURADO") {
                nombreEAPBPob.value = "";
            }
            removeAttributeInput("valorPoblacionalNombreEAPB", "disabled");
            removeAttributeInput("valorPoblacionalNombreEAPB", "readonly");
        }
        bgCamposLlenos();
    }
} catch (error) { }

/* ------------------------------------------------------------------------
---------------------------SESIONES COLECTIVAS-----------------------------
--------------------------------------------------------------------------*/

try {
    try {
        /*-------------------DATOS INSTITUCIÓN--------------------*/
        if (BASE_FICHA.value == "56") {
            initialConditionsSCPage1();
            bgCamposLlenos();
        }

        function initialConditionsSCPage1() {
            //Teléfono
            document
                .getElementById("valorControl4938")
                .addEventListener("keyup", function () {
                    validarTelefono(this.id);
                });

            //Patrón coordenadas
            setPatternCoordinate("valorControl4936", "valorControl4937");

            //Tipo institución
            campo1DependienteDel2(
                "valorControl10338",
                "valorControl10337",
                "12 Otra Institución"
            );
            document
                .getElementById("valorControl10337")
                .addEventListener("change", () => {
                    campo1DependienteDel2(
                        "valorControl10338",
                        "valorControl10337",
                        "12 Otra Institución"
                    );
                });

            //Dirección
            const FillsTpNumber = [
                "valorControl4920",
                "valorControl4925",
                "valorControl4929",
                "valorControl4932",
                "valorControl4934",
            ];
            setAttributeInput(FillsTpNumber, "type", "number");

            const numEje_principal = document.querySelector("#valorControl4920");
            numEje_principal.setAttribute("type", "number");
        }
    } catch (error) { }

    /*-------------------SESIONES COLECTIVAS DATOS SESIONES--------------------*/
    try {
        if (BASE_FICHA.value == "56") {
            eventListenersCamposLlenos();
            initialConditionsSCPage2();
        }

        function initialConditionsSCPage2() {
            //Patrón fecha de sesión
            setAttributeInput("valorControl4944", "pattern", patronDMY);
            setAttributeInput("valorControl4943", "type", "number");

            requiredFieldsSCPg2();
            eventListeners("valorControl4944", "change", validarFechaSesionSC);
            resizeMultiplesSelects();
        }

        function requiredFieldsSCPg2() {
            setAttrInputsFromContainer(
                "#formularioNuevoFormato > div:nth-child(12)",
                "required",
                true
            );
            removeAttributeInput("valorControl4942", "required");
            removeAttributeInput("valorControl5802", "required");
            removeAttributeInput("valorControl4946", "required");

            setAttributeInput(
                ["valorControl4942", "valorControl5802"],
                "disabled",
                true
            );
            bgCamposLlenos();
        }

        function validarFechaSesionSC() {
            const fechaSesionSC = document.getElementById("valorControl4944");
            let bgFecha, activeBtn;
            if (
                date1LessDate2(
                    formatoYMD(fechaSesionSC.value),
                    formatoYMD(FECHA_INTERVENCION.value)
                )
            ) {
                alert("La fecha de la sesión no puede ser menor a la de intervención");
                bgFecha = bgError;
                fechaSesionSC.value = "";
                activeBtn = 0;
            } else {
                bgFecha = bgSuccess;
                activeBtn = 1;
            }
            toggleBtnActualizacion(activeBtn);
            fechaSesionSC.style.backgroundColor = bgFecha;
        }
    } catch (error) { }

    /*----------------------SESIONES COLECTIVAS INDIVIDUOS----------------------*/
    try {
        //Nro sesión requerido
        document.querySelector("#valorControl4958").required = true;

        removeAttrInputsFromContainer(
            "#formularioNuevoFormato > b > b > div:nth-child(2) > div.col-md-12 > div > table > tbody > tr:nth-child(2)",
            "required"
        );
    } catch (error) { }
} catch (error) { }

/* ------------------------------------------------------------------------
-----------------------------ESCALA ABREVIADA -----------------------------
--------------------------------------------------------------------------*/
try {
    const fechCarac = document.querySelector("#valorControl11299");
    const fechEval = document.querySelector("#valorControl8684");
    //Campos fecha caracterización anio, mes y dia
    const anioCarac = document.querySelector("#valorControl8689");
    const mesCarac = document.querySelector("#valorControl8690");
    const diaCarac = document.querySelector("#valorControl8691");
    //Edad en años y meses caracterización
    const edadAnioCarac = document.querySelector("#valorControl8697");
    const edadMesCarac = document.querySelector("#valorControl8698");
    const edadDiaCarac = document.querySelector("#valorControl8699");
    //Campos edad en meses caracterización
    const edadMesesCarac_1 = document.querySelector("#valorControl8732");
    const edadMesesCarac_2 = document.querySelector("#valorControl8752");
    const edadMesesCarac_3 = document.querySelector("#valorControl8771");
    const edadMesesCarac_4 = document.querySelector("#valorControl8812");
    //Edad en años y meses evaluación
    const edadAnioEval = document.querySelector("#valorControl8716");
    const edadMesEval = document.querySelector("#valorControl8717");
    //Campos edad en meses evaluación
    const edadMesesEval_1 = document.querySelector("#valorControl8733");
    const edadMesesEval_2 = document.querySelector("#valorControl8753");
    const edadMesesEval_3 = document.querySelector("#valorControl8772");
    const edadMesesEval_4 = document.querySelector("#valorControl8813");
    //Campos fecha evaluación anio, mes y dia
    const anioEval = document.querySelector("#valorControl8708");
    const mesEval = document.querySelector("#valorControl8709");
    const diaEval = document.querySelector("#valorControl8710");
    //Campos fecha nacimiento caracterización
    const nacimiento_anio_carac = document.querySelector("#valorControl8693");
    const nacimiento_mes_carac = document.querySelector("#valorControl8694");
    const nacimiento_dia_carac = document.querySelector("#valorControl8695");
    //Nombres usuario
    const segundoNombreEAD = document.querySelector("#valorControl8726");
    const segundoApellidoEAD = document.querySelector("#valorControl8728");
    let entornoEAD = document.querySelector("#valorControl8683");
    //Datos usuario
    const pdiEAD = document.querySelector("#valorControl10318");
    const nacionalidadEAD = document.querySelector("#valorControl10317");
    const optionsnacionalidadEAD = nacionalidadEAD.querySelectorAll("option");
    const tipoDocEAD = document.querySelector("#valorControl8729");
    const optionsTipoDocsEAD = tipoDocEAD.querySelectorAll("option");
    const documentsNoCol = "59, 61, 62, 65";

    if (BASE_FICHA.value == "65") {
        setInitialConditionsEAD();
    }

    function setInitialConditionsEAD() {
        //Patrón de fechas
        fechCarac.pattern = patronDMY;
        fechEval.pattern = patronDMY;
        fechCarac.required = true;

        //Deshabilitar campos de evaluación
        //desactiveFillsEvaluation();

        //Eliminar opción entorno educativo y seleccionar institucional
        let optionEducativo = entornoEAD.querySelector('option[value="421"]');
        if (optionEducativo) {
            optionEducativo.remove();
            entornoEAD.value = "2602";
            entornoEAD.style.backgroundColor = bgSuccess;
        } else {
            console.log("No existe la opción");
        }

        //Establecer inputs a tipo number
        let arrayContainersEAD = [
            "#formularioNuevoFormato > div:nth-child(16)",
            "#formularioNuevoFormato > div:nth-child(14)",
            "#formularioNuevoFormato > div:nth-child(20)",
            "#formularioNuevoFormato > div:nth-child(22)",
            "#formularioNuevoFormato > div:nth-child(24)",
            "#formularioNuevoFormato > div:nth-child(26)",
        ];
        setAttrInputsFromContainer(arrayContainersEAD, "type", "number");

        //Campos requeridos
        setAttrInputsFromContainer(
            "#formularioNuevoFormato > div:nth-child(18)",
            "required",
            "true"
        );
        segundoNombreEAD.required = false;
        segundoApellidoEAD.required = false;

        //Establecer localidad
        let localidadSelectEAD = document.querySelector("#valorControl8686");
        let optionsLocalidades = localidadSelectEAD.querySelectorAll("option");
        optionsLocalidades.forEach((option) => {
            if (parseInt(LOCALIDAD_FICHA.value) != parseInt(option.value)) {
                option.remove();
            }
            localidadSelectEAD.style.backgroundColor = bgSuccess;
        });
        setMaxValueInputsDate();
        validarDatosUsuario();
        validarNacionalidad();
        validarPDI();

        eventListenersCamposLlenos();
        bgCamposLlenos();
    }

    function desactiveFillsEvaluation() {
        //Deshabilitar campos de evaluación
        const containerEvaluacion = "#formularioNuevoFormato > div:nth-child(16)"; //Container cálculo edad evaluación
        let attr = MES_ACTUAL + 1 === 7 ? "required" : "disabled";

        const containersInputEvaluacion = [];
        for (let i = 0; i < 24; i++) {
            if (i <= 5) {
                containersInputEvaluacion.push(
                    `#formularioNuevoFormato > div:nth-child(20) > div:nth-child(${i + 1
                    }) > div > table > tbody > tr:nth-child(2) > td > table > tbody > tr > td:nth-child(3)`
                );
            } else if (i >= 6 && i <= 11) {
                containersInputEvaluacion.push(
                    `#formularioNuevoFormato > div:nth-child(22) > div:nth-child(${i - 5
                    }) > div > table > tbody > tr:nth-child(2) > td > table > tbody > tr > td:nth-child(3)`
                );
            } else if (i >= 12 && i <= 17) {
                containersInputEvaluacion.push(
                    `#formularioNuevoFormato > div:nth-child(24) > div:nth-child(${i - 11
                    }) > div > table > tbody > tr:nth-child(2) > td > table > tbody > tr > td:nth-child(3)`
                );
            } else if (i >= 18 && i <= 23) {
                containersInputEvaluacion.push(
                    `#formularioNuevoFormato > div:nth-child(26) > div:nth-child(${i - 17
                    }) > div > table > tbody > tr:nth-child(2) > td > table > tbody > tr > td:nth-child(3)`
                );
            }
        }

        const semanasGestacion = ['valorControl8719', 'valorControl8721', 'valorControl8722', 'valorControl8723'];

        for (let i = 0; i < semanasGestacion.length; i++) {
            document.getElementById(semanasGestacion[i]).required = false;
        }

        setAttributeInput(["valorControl8832", "valorControl8684"], attr, "true");
        setAttrInputsFromContainer(containersInputEvaluacion, attr, "true");
        setAttrInputsFromContainer(containerEvaluacion, attr, "true");
    }

    //-----------------------------Validación datos usuario---------------------------------------
    //--------------------------------------------------------------------------------------------

    function validarDatosUsuario() {
        //Eliminar documentos que no aplican
        optionsTipoDocsEAD.forEach((option) => {
            if (documentsNoCol.includes(option.value)) {
                option.remove();
            }
        });

        //Evento para tipo documento
        tipoDocEAD.addEventListener("change", validarNacionalidad);
        tipoDocEAD.addEventListener("change", validarPDI);
        nacionalidadEAD.addEventListener("change", validarNacionalidad);
    }

    function validarNacionalidad() {
        if (tipoDocEAD.value === "60") {
            optionsnacionalidadEAD.forEach((option) => {
                if (option.value !== "50") {
                    option.disabled = true;
                    option.selected = false;
                } else {
                    option.disabled = false;
                    option.selected = true;
                    nacionalidadEAD.style.backgroundColor = bgSuccess;
                }
            });
        } else {
            optionsnacionalidadEAD.forEach((option) => {
                if (option.value === "50") {
                    option.disabled = true;
                    option.selected = false;
                } else {
                    option.disabled = false;
                }
            });

            if (nacionalidadEAD.value == "50" || nacionalidadEAD.value == "") {
                nacionalidadEAD.value = "";
                nacionalidadEAD.style.backgroundColor = bgWarning;
            } else {
                nacionalidadEAD.style.backgroundColor = bgSuccess;
            }
        }
    }

    function validarPDI() {
        const optionsPDI = pdiEAD.querySelectorAll("option");
        if (tipoDocEAD.value !== "60") {
            optionsPDI.forEach((option) => {
                if (option.value === "2618") {
                    option.selected = true;
                    option.disabled = false;
                } else {
                    option.disabled = true;
                    option.selected = false;
                }
            });
        } else {
            optionsPDI.forEach((option) => {
                if (option.value === "2620") {
                    option.selected = true;
                    option.disabled = false;
                } else {
                    option.disabled = true;
                    option.selected = false;
                }
            });
        }
    }

    function setMaxValueInputsDate() {
        //Establecer max value
        const fechasArreglos = {
            camposAnios: [anioCarac, edadAnioCarac, nacimiento_anio_carac, anioEval],
            camposMeses: [mesCarac, edadMesCarac, nacimiento_mes_carac, mesEval],
            camposDias: [diaCarac, edadDiaCarac, diaEval, nacimiento_dia_carac],
        };

        const edadMaxima = 6;
        const minValueEdadAnio = ANIO_ACTUAL - edadMaxima;

        for (let i = 0; i < fechasArreglos.camposAnios.length; i++) {
            if (i != 1) {
                fechasArreglos.camposMeses[i].setAttribute("min", "1");
                fechasArreglos.camposDias[i].setAttribute("min", "1");
                fechasArreglos.camposAnios[i].setAttribute("min", minValueEdadAnio);
            } else {
                fechasArreglos.camposAnios[i].setAttribute("max", edadMaxima);
            }

            fechasArreglos.camposMeses[i].setAttribute("max", "12");
            fechasArreglos.camposDias[i].setAttribute("max", "31");
        }
        evaluacionRealizada();
    }

    //-------------------------Calcular edad en meses-----------------------------
    //----------------------------------------------------------------------------
    function calculateAgeMonths(arrayInputsAgeMonths, anios, meses) {
        let edadMeses = anios * 12 + parseInt(meses);

        for (let i = 0; i < arrayInputsAgeMonths.length; i++) {
            arrayInputsAgeMonths[i].value = edadMeses;
            readonlyInputs(arrayInputsAgeMonths[i]);
            arrayInputsAgeMonths[i].style.backgroundColor = bgSuccess;
        }
    }

    let camposCaracEdadArr = [
        edadMesesCarac_1,
        edadMesesCarac_2,
        edadMesesCarac_3,
        edadMesesCarac_4,
    ];

    let camposEdadEvalArr = [
        edadMesesEval_1,
        edadMesesEval_2,
        edadMesesEval_3,
        edadMesesEval_4,
    ];

    edadMesCarac.addEventListener("change", function () {
        calculateAgeMonths(camposCaracEdadArr, edadAnioCarac.value, this.value);
    });

    edadAnioCarac.addEventListener("change", function () {
        calculateAgeMonths(camposCaracEdadArr, this.value, edadMesCarac.value);
    });

    edadMesEval.addEventListener("change", function () {
        calculateAgeMonths(camposEdadEvalArr, edadAnioEval.value, this.value);
    });

    edadAnioEval.addEventListener("change", function () {
        calculateAgeMonths(camposEdadEvalArr, this.value, edadMesEval.value);
    });

    //-----------Colocar fecha dividida caracterización y/o evaluación------------
    //----------------------------------------------------------------------------

    validarFechaCaracEval(1);
    fechCarac.addEventListener("change", function () {
        validarFechaCaracEval(1);
    });

    fechEval.addEventListener("change", function () {
        validarFechaCaracEval(2);
    });

    function validarFechaCaracEval(typeFecha) {
        let caracterizacion = formatoYMD(fechCarac.value);
        let arrayCamposFech = [];

        switch (typeFecha) {
            case 1:
                let intervencion = formatoYMD(FECHA_INTERVENCION.value);
                const dateCarac = new Date(caracterizacion);

                if (
                    date1LessDate2(caracterizacion, intervencion) ||
                    dateCarac.getFullYear() !== 2024 ||
                    dateCarac.getMonth() + 1 > MES_ACTUAL + 1
                ) {
                    fechCarac.value = "";
                    fechCarac.style.backgroundColor = bgError;
                    alert(
                        "La fecha de caracterización no cumple con los parámetros establecidos"
                    );
                } else {
                    fechCarac.style.backgroundColor = bgSuccess;
                    arrayCamposFech = [anioCarac, mesCarac, diaCarac];
                    setDateCaracSplit(caracterizacion, arrayCamposFech);
                }
                break;

            case 2:
                let evaluacion = formatoYMD(fechEval.value);
                if (date1LessDate2(evaluacion, caracterizacion)) {
                    fechEval.value = "";
                    fechEval.style.backgroundColor = bgError;
                    alert(
                        "La fecha de evaluación no puede ser menor a la de caracterización"
                    );
                } else {
                    fechEval.style.backgroundColor = bgSuccess;
                    arrayCamposFech = [anioEval, mesEval, diaEval];
                    setDateCaracSplit(evaluacion, arrayCamposFech);
                }
                break;

            default:
                break;
        }
        evaluacionRealizada();
    }

    function setDateCaracSplit(date, arrayAnioMesDia) {
        let arrayFecha = date.split("-");

        for (let i = 0; i < arrayFecha.length; i++) {
            if ((arrayAnioMesDia[i].value = arrayFecha[i])) {
                arrayAnioMesDia[i].style.backgroundColor = bgSuccess;
                readonlyInputs(arrayAnioMesDia[i]);
            } else {
                arrayAnioMesDia[i].style.backgroundColor = bgWhite;
            }
        }
    }

    //------------------------Calcular PD-----------------------
    //----------------------------------------------------------

    //***MG-MOTRICIDAD GRUESA***//
    //***MF-MOTRICIDAD FINO ADAPTATIVA***//
    //***AL-AUDICIÓN LENGUAJE***//
    //***PS-PERSONAL SOCIAL***//
    const camposCalculoPD = {
        mg: ["valorControl8736", "valorControl8739", "valorControl8742"],
        mf: ["valorControl8755", "valorControl8758", "valorControl8761"],
        al: ["valorControl8774", "valorControl8777", "valorControl8781"],
        ps: ["valorControl8815", "valorControl8818", "valorControl8821"],
    };

    for (let sec in camposCalculoPD) {
        if (camposCalculoPD.hasOwnProperty(sec)) {
            setEventcalculoPD(camposCalculoPD[sec]);
        }
    }

    function setEventcalculoPD(arrayCampos) {
        arrayCampos.forEach((campo) => {
            document.getElementById(campo).addEventListener("keyup", () => {
                calculopdEAD(arrayCampos);
            });
        });
    }

    function calculopdEAD(arrayValues) {
        const num1 = document.getElementById(arrayValues[0]);
        const num2 = document.getElementById(arrayValues[1]);
        let resultado = document.getElementById(arrayValues[2]);
        if ((resultado.value = parseInt(num1.value) + parseInt(num2.value))) {
            resultado.readOnly = "1";
            resultado.style.backgroundColor = bgSuccess;
        }
    }

    //--------------Validar fecha de nacimiento-----------------
    //----------------------------------------------------------
    nacimiento_dia_carac.addEventListener("change", validateDateBorn);
    nacimiento_mes_carac.addEventListener("change", validateDateBorn);
    nacimiento_anio_carac.addEventListener("change", validateDateBorn);

    function validateDateBorn() {
        const arrayNacimiento = [
            nacimiento_dia_carac,
            nacimiento_mes_carac,
            nacimiento_anio_carac,
        ];
        const fechaNacimiento = arrayNacimiento
            .map((input) => input.value)
            .join("/");
        if (
            date1LessDate2(formatoYMD(fechCarac.value), formatoYMD(fechaNacimiento))
        ) {
            alert(
                "La fecha de nacimiento no puede ser mayor a la de caracterización"
            );
            arrayNacimiento.forEach((input) => {
                input.style.backgroundColor = bgError;
            });
        } else {
            arrayNacimiento.forEach((input) => {
                input.style.backgroundColor = bgSuccess;
            });
        }
    }

    //------------------Visualización color---------------------
    //----------------------------------------------------------
    function setColourPTEAD(params) {
        const arrayContainersPT = [];
        for (let i = 20; i < 26; i++) {
            if (i % 2 == 0) {
                arrayContainersPT.push(
                    `#formularioNuevoFormato > div:nth-child(${i}) > div:nth-child(6) > div > table > tbody > tr:nth-child(2) > td > table > tbody > tr`
                );
            }
        }
        console.log(arrayContainersPT);
    }
    //----------------Longitud inputs text-------------------
    //-------------------------------------------------------
    const inputsToValidateLenght = [
        "#valorControl8685", //nombre HCB
        "#valorControl8834", //nombre digitador
        "#valorControl8725", //Primer nombre
        "#valorControl8726", //Segundo nombre
        "#valorControl8727", //Primer apellido
        "#valorControl8728", //Segundo apellido
    ];
    let longitudMin;
    inputsToValidateLenght.forEach((idInput) => {
        longitudMin =
            idInput == "#valorControl8685" || idInput == "#valorControl8834" ? 6 : 3;
        document.querySelector(idInput).addEventListener("keyup", function () {
            longitudMinCampoDOM(this.id, longitudMin);
        });
    });

    //¿Se realizó evaluación?
    function evaluacionRealizada() {
        const realizacionEval = document.querySelector("#valorControl8830");
        realizacionEval.querySelectorAll("option");
        let evaluacionSN;
        if (fechEval.value == "") {
            disabledOptionsFromSelect("valorControl8830", "SI", "true");
            evaluacionSN = "NO";
        } else {
            disabledOptionsFromSelect("valorControl8830", "NO", "true");
            evaluacionSN = "SI";
        }
        realizacionEval.value = evaluacionSN;
        realizacionEval.style.backgroundColor = bgSuccess;
    }
} catch (error) { }

/* -------------------------------------------------------------------------
---------------------PERCEPCIÓN CAPACIDADES BÁSICAS ------------------------
--------------------------------------------------------------------------*/
try {
    let PDI_pch = document.querySelector(
        "#valorPoblacionalPoblacionDiferencialInclusion"
    );
    let nacion_pch = document.querySelector("#valorPoblacionalNacionalidad");
    nacion_pch.addEventListener("change", function () {
        this.value != "50" ?
            ((PDI_pch.value = "2618"), (PDI_pch.style.backgroundColor = bgSuccess)) :
            (PDI_pch.value = "2620"),
            (PDI_pch.style.backgroundColor = bgSuccess);
    });

    let puntajePCB = document.querySelector("#valorControl8150");
    puntajePCB.setAttribute("type", "number");
    puntajePCB.required = true;

    let fechaInicial = document.querySelector("#valorControl11877");
    let fechaFinal = document.querySelector("#valorControl11881");

    fechaFinal.pattern = patronDMY;
    fechaInicial.pattern = patronDMY;
    fechaInicial.required = true;

    let pregunta1_cph = document.querySelector("#valorControl8108");
    let pregunta2_cph = document.querySelector("#valorControl8112");
    let pregunta3_cph = document.querySelector("#valorControl8116");
    let pregunta4_cph = document.querySelector("#valorControl8120");
    let pregunta5_cph = document.querySelector("#valorControl8124");
    let pregunta6_cph = document.querySelector("#valorControl8128");
    let pregunta7_cph = document.querySelector("#valorControl8132");
    let pregunta8_cph = document.querySelector("#valorControl8136");
    let pregunta9_cph = document.querySelector("#valorControl8140");
    let pregunta10_cph = document.querySelector("#valorControl8144");
    let pregunta11_cph = document.querySelector("#valorControl8148");

    let despues_cbh = [
        pregunta1_cph,
        pregunta2_cph,
        pregunta3_cph,
        pregunta4_cph,
        pregunta5_cph,
        pregunta6_cph,
        pregunta7_cph,
        pregunta8_cph,
        pregunta9_cph,
        pregunta10_cph,
        pregunta11_cph,
    ];

    for (let i = 0; i < despues_cbh.length; i++) {
        despues_cbh[i].disabled = true;
    }

    //Comparar fecha de aplicación inicial y final
    fechaFinal.addEventListener("change", function () {
        let diasFinInicio = date1LessDate2(
            formatoYMD(fechaInicial.value),
            formatoYMD(fechaFinal.value)
        );

        if (!diasFinInicio) {
            this.style.backgroundColor = bgError;
            toggleBtnActualizacion(0);
            for (let i = 0; i < despues_cbh.length; i++) {
                despues_cbh[i].disabled = true;
                despues_cbh[i].required = false;
            }
        } else {
            this.style.backgroundColor = bgSuccess;
            toggleBtnActualizacion(1);
            for (let i = 0; i < despues_cbh.length; i++) {
                despues_cbh[i].disabled = false;
                despues_cbh[i].required = true;
            }
        }
    });
} catch (error) { }

/*---------------------------------------------------------------------------
-----------------------------------HCB---------------------------------------
----------------------------------------------------------------------------*/
//----------Page 1---------------

try {
    let ej_principal_num = document.querySelector("#valorControl6217");
    ej_principal_num.setAttribute("type", "number");

    let ej_generador_num = document.querySelector("#valorControl6226");
    ej_generador_num.setAttribute("type", "number");

    const inputsPerrosNoVacEster = [
        "valorControl6773", //perrosNoVacunados
        "valorControl6771", //perrosNoEsterilizados
    ];

    const inputsGatosNoVacEster = [
        "valorControl6774", //gatosNoVacunados
        "valorControl6772", //gatosNoEsterilizados
    ];

    const perrosHCB = document.querySelector("#valorControl6775");
    const gatosHCB = document.querySelector("#valorControl6776");

    if (BASE_FICHA.value == "64") {
        setInitialConditionsHCBPg1();
        eventListenersCamposLlenos();
        bgCamposLlenos();
        const fechaEvaluacion = document.getElementById('valorControl16911');
        fechaEvaluacion.required = false;
    }

    //----Campos evaluación------
    function attrCamposEvaluacionHCB() {
        let attributeEvaluacionHCB = "";
        //if (MES_ACTUAL + 1 !== 6) {
        if (MES_ACTUAL + 1 !== 7) {
            attributeEvaluacionHCB = "disabled";
        } else {
            attributeEvaluacionHCB = "required";
        }

        setAttributeInput("valorControl6210", attributeEvaluacionHCB, "true"); //Fecha evaluación
        document.getElementById('valorControl6210').required = false;
        for (let i = 2; i < 73; i++) {
            if (i != 16 && i != 21 && i != 25 && i != 33 && i < 38) {
                setAttrInputsFromContainer(
                    `#formularioNuevoFormato > div:nth-child(18) > div:nth-child(${i}) > div > table > tbody > tr:nth-child(2) > td > table > tbody > tr > td:nth-child(3)`,
                    attributeEvaluacionHCB,
                    "true"
                );
            } else if (i >= 38 && i - 36 != 8 && i - 36 != 21 && i - 36 != 28) {
                setAttrInputsFromContainer(
                    `#formularioNuevoFormato > div:nth-child(20) > div:nth-child(${i - 36
                    }) > div > table > tbody > tr:nth-child(2) > td > table > tbody > tr > td:nth-child(3)`,
                    attributeEvaluacionHCB,
                    "true"
                );
            }
        }
    }

    //----Campos requeridos------
    function requiredCamposHCBPg1() {
        //Identificación HCB
        const camposObligatorios = [
            "valorControl6211", //Zona
            "valorControl6213", //Localidad
            "valorControl6214", //UPZ
            "valorControl6215", //Barrio
            "valorControl6234", //Asociación
            "valorControl6233", //Estrato
            "valorControl6240", //AIEPI
            "valorControl6241", //Estrategia MV
            "valorControl6242", //Huerta casera
            "valorControl6243", //Espacios padres de familia
            "valorControl6248", //Servicios
            "valorControl6246", //Lugar
        ];
        setAttributeInput(camposObligatorios, "required", "true");

        //Características del HCB
        const contenedoresObligatorios = [
            "#formularioNuevoFormato > div:nth-child(16) > div:nth-child(4)",
            "#formularioNuevoFormato > div:nth-child(16) > div:nth-child(5)",
            "#formularioNuevoFormato > div:nth-child(16) > div:nth-child(6)",
        ];
        setAttrInputsFromContainer(contenedoresObligatorios, "required", "true");

        //Riesgos ambientales
        for (let i = 2; i < 73; i++) {
            if (i != 16 && i != 21 && i != 25 && i != 33 && i < 38) {
                setAttrInputsFromContainer(
                    `#formularioNuevoFormato > div:nth-child(18) > div:nth-child(${i}) > div > table > tbody > tr:nth-child(2) > td > table > tbody > tr > td:nth-child(1)`,
                    "required",
                    "true"
                );
            } else if (i >= 38 && i - 36 != 8 && i - 36 != 21 && i - 36 != 28) {
                setAttrInputsFromContainer(
                    `#formularioNuevoFormato > div:nth-child(20) > div:nth-child(${i - 36
                    }) > div > table > tbody > tr:nth-child(2) > td > table > tbody > tr > td:nth-child(1)`,
                    "required",
                    "true"
                );
            }
        }
    }

    //----Campos tipo número
    function numberInputsHCBPg1() {
        for (let i = 4; i < 7; i++) {
            setAttrInputsFromContainer(
                `#formularioNuevoFormato > div:nth-child(16) > div:nth-child(${i})`,
                "type",
                "number"
            );
        }

        for (let i = 29; i < 37; i++) {
            setAttrInputsFromContainer(
                `#formularioNuevoFormato > div:nth-child(20) > div:nth-child(${i})`,
                "type",
                "number"
            );
        }

        for (let i = 4; i < 7; i++) {
            setAttrInputsFromContainer(
                `#formularioNuevoFormato > div:nth-child(16) > div:nth-child(${i})`,
                "type",
                "number"
            );
        }

        setAttributeInput("valorControl6237", "type", "number");
        setAttributeInput("valorControl6236", "type", "number");
    }

    function validarDireccionHCB(zona) {
        if (zona === "58" || zona.trim() === "" || zona === "") {
            for (let i = 7; i < 15; i++) {
                setAttrInputsFromContainer(
                    `#formularioNuevoFormato > div:nth-child(12) > div:nth-child(${i})`,
                    "disabled",
                    "true"
                );
                setAttrInputsFromContainer(
                    `#formularioNuevoFormato > div:nth-child(12) > div:nth-child(${i})`,
                    "value",
                    ""
                );
                removeAttrInputsFromContainer(
                    `#formularioNuevoFormato > div:nth-child(12) > div:nth-child(${i})`,
                    "required"
                );
            }

            setAttrInputsFromContainer(
                [
                    "#formularioNuevoFormato > div:nth-child(12) > div:nth-child(15)",
                    "#formularioNuevoFormato > div:nth-child(12) > div:nth-child(19)",
                ],
                "required",
                "true"
            );

            removeAttrInputsFromContainer(
                [
                    "#formularioNuevoFormato > div:nth-child(12) > div:nth-child(15)",
                    "#formularioNuevoFormato > div:nth-child(12) > div:nth-child(19)",
                ],
                "disabled"
            );
        } else {
            for (let i = 7; i < 15; i++) {
                removeAttrInputsFromContainer(
                    `#formularioNuevoFormato > div:nth-child(12) > div:nth-child(${i})`,
                    "disabled"
                );

                setAttrInputsFromContainer(
                    `#formularioNuevoFormato > div:nth-child(12) > div:nth-child(${i})`,
                    "required",
                    "true"
                );
            }

            removeAttrInputsFromContainer(
                [
                    "#formularioNuevoFormato > div:nth-child(12) > div:nth-child(15)",
                    "#formularioNuevoFormato > div:nth-child(12) > div:nth-child(19)",
                ],
                "required"
            );

            setAttrInputsFromContainer(
                [
                    "#formularioNuevoFormato > div:nth-child(12) > div:nth-child(15)",
                    "#formularioNuevoFormato > div:nth-child(12) > div:nth-child(19)",
                ],
                "disabled",
                "true"
            );

            setAttrInputsFromContainer(
                [
                    "#formularioNuevoFormato > div:nth-child(12) > div:nth-child(15)",
                    "#formularioNuevoFormato > div:nth-child(12) > div:nth-child(19)",
                ],
                "value",
                ""
            );
        }

        const noRequiredDir = [
            "valorControl6218",
            "valorControl6219",
            "valorControl6223",
            "valorControl6225",
            "valorControl6224",
            "valorControl6220",
            "valorControl6782",
            "valorControl6784",
            "valorControl6228",
            "valorControl6785",
            "valorControl6229",
            "valorControl6786",
            "valorControl6778",
            "valorControl6779",
        ];
        noRequiredDir.forEach((campo) => {
            document.getElementById(campo).removeAttribute("required");
        });

        bgCamposLlenos();
    }

    function setInitialConditionsHCBPg1() {
        //Campos requeridos HCB
        requiredCamposHCBPg1();

        //Evento para validar campos obligatorios
        eventListenersCamposLlenos();

        //Apariencia fila fecha evaluación
        let box_campo_evaluacion = document.querySelector(
            "#formularioNuevoFormato > div:nth-child(12) > div:nth-child(1)"
        );
        box_campo_evaluacion.classList.remove("col-md-3");
        box_campo_evaluacion.classList.add("col-md-12", "mb-4");

        //Establecer atributos campos evaluación
        attrCamposEvaluacionHCB();

        //Colocar localidad
        onlyEnabledOptionFromSelect(
            "valorControl6213",
            parseInt(LOCALIDAD_FICHA.value)
        );
        setAttributeInput(
            "valorControl6213",
            "value",
            parseInt(LOCALIDAD_FICHA.value)
        );

        //Cargar UPZ
        setTimeout(() => {
            dispararEvento("valorControl6213", "change");
        }, 500);

        //Validar dirección
        const zonaHCB = document.querySelector("#valorControl6211");
        zonaHCB.addEventListener("change", () => {
            validarDireccionHCB(zonaHCB.value);
        });
        validarDireccionHCB(zonaHCB.value);

        //Manzana del cuidado
        eventListeners(
            "valorControl8360",
            "change",
            validarManzanaCuidado("valorControl8360", "valorControl9641")
        );

        //Teléfonos
        const inputsTelHCB = ["valorControl6237", "valorControl6236"];

        inputsTelHCB.forEach((input) => {
            document.getElementById(input).addEventListener("keyup", () => {
                validarLongitudTel(input);
            });
        });

        //Correos
        let correosHCB = ["valorControl6238", "valorControl6239"];
        setAttributeInput(correosHCB, "type", "email");

        //Campos numéricos
        numberInputsHCBPg1();

        //Certificación AIEPI
        campo1DependienteDel2("valorControl6245", "valorControl6240", "Si");
        document
            .getElementById("valorControl6240")
            .addEventListener("change", () => {
                campo1DependienteDel2("valorControl6245", "valorControl6240", "Si");
            });

        //Reunión periódica con los padres
        campo1DependienteDel2("valorControl6244", "valorControl6243", "Si");
        document
            .getElementById("valorControl6243")
            .addEventListener("change", () => {
                campo1DependienteDel2("valorControl6244", "valorControl6243", "Si");
            });

        //Lugar de funcionamiento
        campo1DependienteDel2("valorControl6247", "valorControl6246", "Otro");
        document
            .getElementById("valorControl6246")
            .addEventListener("change", () => {
                campo1DependienteDel2("valorControl6247", "valorControl6246", "Otro");
            });

        //Convivencia con animales
        eventListeners("valorControl6775", "change", setMaxAnimalesVacunados);
        eventListeners("valorControl6776", "change", setMaxAnimalesVacunados);

        inputsPerrosNoVacEster.forEach((input) => {
            document.getElementById(input).addEventListener("keyup", () => {
                validarAnimalesDomHCB(input, perrosHCB.value);
            });
        });

        inputsGatosNoVacEster.forEach((input) => {
            document.getElementById(input).addEventListener("keyup", () => {
                validarAnimalesDomHCB(input, gatosHCB.value);
            });
        });

        //Color campos llenos o vacíos
        bgCamposLlenos();
    }

    function setMaxAnimalesVacunados() {
        inputsPerrosNoVacEster.forEach((inputId) => {
            document.getElementById(inputId).max = perrosHCB.value;
        });

        inputsGatosNoVacEster.forEach((inputId) => {
            document.getElementById(inputId).max = gatosHCB.value;
        });
    }

    function validarAnimalesDomHCB(inputId, cantidadAnimal) {
        document.getElementById(inputId).style.backgroundColor =
            getValueInput(inputId) > cantidadAnimal || getValueInput(inputId) === "" ?
                bgError :
                bgSuccess;
    }

    //Lugar de funcionamiento
    let lugar_hcb = document.querySelector("#valorControl6246");
    lugar_hcb.required = true;

    let otro_lugar_hcb = document.querySelector("#valorControl6247");
    lugar_hcb.addEventListener("change", function () {
        this.value == "Otro" ?
            (otro_lugar_hcb.required = true) :
            (otro_lugar_hcb.required = false);
    });
} catch (error) { }

//-----------Page 2------------
try {
    if (BASE_FICHA.value === "64") {
        setInitialConditionsHCBPg2();
    }

    function setInitialConditionsHCBPg2() {
        //Deshabilitar poblacional no vigente
        setAttrInputsFromContainer(
            "#formularioNuevoFormato > b > b > div:nth-child(2)",
            "disabled",
            "true"
        );

        //Condición crónica
        //Tamizajes obligatorios
        // setTimeout(() => {
        //   setAttributeInput(["valorControl9613", "valorControl9614", "valorControl9615"], "required", "true");
        // }, 2000);

        //setAttributeInput("valorControl6290", "disabled", "true");

        //Deshabilitar campos evaluación
        //disabledCamposEvaluacionHCB();

        //Campos clasificación
        const camposClasificacionHCB = ["valorControl12000", "valorControl12001"];
        camposClasificacionHCB.forEach((idCampo) => {
            document.getElementById(idCampo).addEventListener("change", () => {
                setPercentageClasificacion(idCampo);
            });
        });

        setSelectPriorizadoHCB();
        eventListenersCamposLlenos();
        bgCamposLlenos();
    }

    function setPercentageClasificacion(idInput) {
        const input = document.getElementById(idInput);
        let colorbg, stateBtn;

        if (input.value.trim() !== "") {
            if (!/%$/.test(input.value.trim())) {
                colorbg = bgError;
                stateBtn = 0;
            } else {
                colorbg = bgSuccess;
                stateBtn = 1;
            }
        }
        input.style.backgroundColor = colorbg;
        toggleBtnActualizacion(stateBtn);
    }

    function formatoAlertasHCB(inputId) {
        const campoAlerta = document.getElementById(inputId);
        const campoValidado = campoAlerta.value.replace(/[^\d,]/g, "");

        return campoValidado;
    }

    function setSelectPriorizadoHCB() {
        const campoPriorizado = document.getElementById("valorControl12002");

        let selectPriorizadoHCB = document.createElement("select");
        selectPriorizadoHCB.id = "valorControl12002";
        selectPriorizadoHCB.name = "valorControl12002";
        selectPriorizadoHCB.classList.add("form-control");
        selectPriorizadoHCB.style.width = "200px";

        const opcionesPriorizado = ["", "SI", "NO"];
        opcionesPriorizado.forEach((optionTexto) => {
            let opcion = document.createElement("option");
            opcion.text = optionTexto;
            opcion.value = optionTexto;
            selectPriorizadoHCB.appendChild(opcion);
        });
        campoPriorizado.parentNode.replaceChild(
            selectPriorizadoHCB,
            campoPriorizado
        );
        if (campoPriorizado.value != "") {
            for (let i = 0; i < selectPriorizadoHCB.options.length; i++) {
                if (selectPriorizadoHCB.options[i].value === campoPriorizado.value) {
                    selectPriorizadoHCB.options[i].selected = true;
                    break;
                }
            }
        }
    }

    function disabledCamposEvaluacionHCB() {
        let attributeEvaluacionHCB2 = "";
        if (MES_ACTUAL + 1 !== 6) {
            attributeEvaluacionHCB2 = "disabled";
        }

        const camposEvaluacionPg2 = [
            "valorControl6293",
            "valorControl6293",
            "valorControl6303",
            "valorControl12001",
            "valorControl6306",
            "valorControl6309",
            "valorControl6312",
            "valorControl6315",
            "valorControl6769",
            "valorControl6298",
            "valorControl6299",
            "valorControl6300",
        ];
        setAttributeInput(camposEvaluacionPg2, attributeEvaluacionHCB2, "true");
    }

    //Tamizajes obligatorios
    let oms_hcb = document.querySelector("#valorControl9613");
    let findrisc_hcb = document.querySelector("#valorControl9614");
    let epoc_hcb = document.querySelector("#valorControl9615");
    let tamizajes_array = [oms_hcb, findrisc_hcb, epoc_hcb];
    for (let i = 0; i < tamizajes_array.length; i++) {
        tamizajes_array[i].required = false;
    }
} catch (error) { }

/* ----------------------------------------------------------------------
------------------------MASCOTA VERDE ----------------------------------- 
-----------------------------------------------------------------------*/
// try {
//     async function mascotaVerde(){
//         const { MascotaVerde } = await importModule('institucional/MascotaVerde.js', 'MascotaVerde');

//         if (!MascotaVerde) {
//             console.error("No se pudo importar el módulo SesionesColectivas.");
//             return;
//         }

//         MascotaVerde.selectPage();

//         initialConditionsMVPage1();
//         eventListenersCamposLlenos();
//         bgCamposLlenos();
//     }

//     //-----------//Datos de la institución//-----------//
//     if (BASE_FICHA.value == "67") {
//         mascotaVerde();
//     }

//     function initialConditionsMVPage1() {
//         //Zona rural-urbana
//         validarZona();
//         eventListeners("valorControl6957", "change", validarZona);
//     }

//     function disabledCamposMVPg1() {
//         for (let i = 22; i < 25; i++) {
//             //Establecer inicio del index de acuerdo al nro sesión
//             setAttrInputsFromContainer(
//                 `#formularioNuevoFormato > div:nth-child(${i})`,
//                 "disabled",
//                 true
//             );
//         }
//         setAttributeInput("valorControl10320", "disabled", true);
//     }

//     //Zona rural-urbana
//     function validarZona() {
//         const tipoZona = document.querySelector("#valorControl6957");
//         const rural = tipoZona.value == "58";
//         const containerRural = [
//             "#formularioNuevoFormato > div:nth-child(12) > div:nth-child(22)",
//             "#formularioNuevoFormato > div:nth-child(12) > div:nth-child(18)",
//         ];
//         const requiredFillsAddres = [
//             "valorControl6963",
//             "valorControl6964",
//             "valorControl6972",
//             "valorControl6977",
//         ];
//         const containerDireccion = [];
//         for (let i = 10; i < 18; i++) {
//             containerDireccion.push(
//                 `#formularioNuevoFormato > div:nth-child(12) > div:nth-child(${i})`
//             );
//         }

//         if (rural) {
//             setAttrInputsFromContainer(containerDireccion, "value", "");
//             setAttrInputsFromContainer(containerDireccion, "disabled", "true");
//             removeAttrInputsFromContainer(containerDireccion, "required");
//             removeAttrInputsFromContainer(containerRural, "disabled");
//             setAttrInputsFromContainer(containerRural, "required", "true");
//         } else {
//             removeAttrInputsFromContainer(containerDireccion, "disabled");
//             setAttributeInput(requiredFillsAddres, "required", "true");
//             removeAttrInputsFromContainer(containerRural, "required");
//             setAttrInputsFromContainer(containerRural, "value", "");
//             setAttrInputsFromContainer(containerRural, "disabled", "true");
//         }
//     }
// } catch (error) {}

//-----------------PÁGINA 2 MASCOTA VERDE ------------------------
// try {
//     let inputsProfesionalesS1P2MV = ["valorControl7008", "valorControl7009"];
//     const dateS1MV = document.querySelector("#valorControl7002");
//     const dateS2MV = document.querySelector("#valorControl7011");

//     if (BASE_FICHA.value == "67") {
//         setInitialConditionsMVPg2();
//         validateCompleteSesionMV();
//         //getProfesionalsNamesMVS1();
//         eventListenersCamposLlenos();
//     }

//     function setInitialConditionsMVPg2() {
//         dateS1MV.pattern = patronDMY;
//         dateS2MV.pattern = patronDMY;

//         if (MES_ACTUAL + 1 === 4 || MES_ACTUAL + 1 === 5) {
//             setAttributeInput("valorControl7002", "required", "true");
//             if ((dateS1MV.value = FECHA_INTERVENCION.value)) {
//                 dateS1MV.style.backgroundColor = bgSuccess;
//                 setAttributeInput("valorControl7002", "readonly", "1");
//             } else {
//                 dateS1MV.style.backgroundColor = bgWarning;
//                 setAttributeInput("valorControl7002", "readonly", "0");
//             }

//             setAttributeInput("valorControl7001", "required", "true");
//             setAttributeInput("valorControl7001", "value", 1);
//             setAttributeInput("valorControl7001", "readonly", "1");
//             setAttributeInput(inputsProfesionalesS1P2MV, "required", "true");
//         } else {
//             if (
//                 getValueInput("valorControl7001") != "" &&
//                 getValueInput("valorControl7002") != ""
//             ) {
//                 setAttributeInput("valorControl7001", "readonly", "1");
//                 setAttributeInput("valorControl7002", "readonly", "1");
//             }
//         }

//         setAttributeInput("valorControl7001", "type", "number");
//         eventListeners("valorControl7011", "change", validarFechasSesiones);
//         validarSimbolosAlertas();

//         const triggerValidateSesions = async () => {
//             const { MascotaVerde } = await importModule('institucional/MascotaVerde.js', 'MascotaVerde');

//             if (!MascotaVerde) {
//                 console.error("No se pudo importar el módulo SesionesColectivas.");
//                 return;
//             }
//             const dataSesions = [{
//                 numberSesion: 2,
//                 idInputSesion: "valorControl7010",
//                 pathContainer: "#formularioNuevoFormato > b > b > div:nth-child(6)",
//                 idInputAlert: "valorControl7013",
//                 idInputTematica: "valorControl12009"
//             },
//             {
//                 numberSesion: 3,
//                 idInputSesion: "valorControl7019",
//                 pathContainer: "#formularioNuevoFormato > b > b > div:nth-child(8)",
//                 idInputAlert: "valorControl7022",
//                 idInputTematica: "valorControl12010"
//             },
//             {
//                 numberSesion: 4,
//                 idInputSesion: "valorControl7028",
//                 pathContainer: "#formularioNuevoFormato > b > b > div:nth-child(10)",
//                 idInputAlert: "valorControl7031",
//                 idInputTematica: "valorControl12011"
//             },
//             {
//                 numberSesion: 5,
//                 idInputSesion: "valorControl7037",
//                 pathContainer: "#formularioNuevoFormato > b > b > div:nth-child(12)",
//                 idInputAlert: "valorControl7040",
//                 idInputTematica: "valorControl12012"
//             },
//             {
//                 numberSesion: 6,
//                 idInputSesion: "valorControl7046",
//                 pathContainer: "#formularioNuevoFormato > b > b > div:nth-child(14)",
//                 idInputAlert: "valorControl7049",
//                 idInputTematica: "valorControl12013"
//             }
//             ];

//             dataSesions.forEach((sesion) => {
//                 validateCompleteSesionMV(
//                     sesion.numberSesion,
//                     sesion.idInputSesion,
//                     sesion.pathContainer,
//                     sesion.idInputAlert,
//                     sesion.idInputTematica
//                 );
//                 document
//                     .getElementById(sesion.idInputSesion)
//                     .addEventListener("keyup", () => {
//                         validateCompleteSesionMV(
//                             sesion.numberSesion,
//                             sesion.idInputSesion,
//                             sesion.pathContainer,
//                             sesion.idInputAlert,
//                             sesion.idInputTematica
//                         );

//                         MascotaVerde.getProfesionalsSessions();
//                     });
//             });
//         };

//         triggerValidateSesions();
//     }

//     function validateCompleteSesionMV(numberSesion, idInputSesion, pathContainer, idInputAlert, idInputTematica) {
//         if (getValueInput(idInputSesion) == "" || getValueInput(idInputSesion) != numberSesion) {
//             setAttrInputsFromContainer(pathContainer, "value", "");
//             setAttrInputsFromContainer(pathContainer, "disabled", true);
//             removeAttrInputsFromContainer(pathContainer, "required");
//             removeAttributeInput(idInputSesion, "disabled");
//             removeAttributeInput(idInputTematica, "required"); //Quitar requerido a alertas psicosociales
//         } else {
//             removeAttrInputsFromContainer(pathContainer, "disabled");
//             setAttrInputsFromContainer(pathContainer, "required", true); //Establecer requerido a los datos de la sesión
//             removeAttributeInput(idInputAlert, "required"); //Quitar requerido a alertas psicosociales
//             removeAttributeInput(idInputTematica, "required"); //Quitar requerido a alertas psicosociales
//         }
//         bgCamposLlenos();
//     }

//     function getProfesionalsNamesMVS1() {
//         //Sesión 1
//         for (let i = 0; i < inputsProfesionalesS1P2MV.length; i++) {
//             let profesionalName = window.localStorage.getItem(
//                 `profesional${i + 1}S1MV`
//             );
//             if (profesionalName != null) {
//                 if (
//                     (document.getElementById(inputsProfesionalesS1P2MV[i]).value =
//                         profesionalName)
//                 ) {
//                     document.getElementById(
//                         inputsProfesionalesS1P2MV[i]
//                     ).style.backgroundColor = bgSuccess;
//                     //setAttributeInput(inputsProfesionalesS1P2MV[i], "readonly", "1");
//                 }
//             }
//         }
//     }

//     function validarSimbolosAlertas() {
//         const idCamposAlertas = [
//             "valorControl7004",
//             "valorControl7013",
//             "valorControl7022",
//             "valorControl7031",
//             "valorControl7040",
//             "valorControl7049",
//         ];
//         idCamposAlertas.forEach((idCampo) => {
//             document
//                 .getElementById(idCampo)
//                 .addEventListener("keypress", function (evt) {
//                     var tecla = event.key;
//                     if (
//                         [
//                             ".",
//                             "/",
//                             "-",
//                             "*",
//                             "+",
//                             "`",
//                             "'",
//                             "°",
//                             "|",
//                             "#",
//                             "$",
//                             "%",
//                             "&",
//                             "!",
//                             '"',
//                             "(",
//                             ")",
//                             "=",
//                             "¡",
//                         ].includes(tecla)
//                     )
//                         event.preventDefault();
//                 });
//         });
//     }

//     function removeProfesionalsNamesMV() {
//         const keyProfesionalsMV = [
//             "profesional1S1MV",
//             "profesional2S1MV",
//             "profesional1S2MV",
//             "profesional2S2MV",
//         ];
//         for (let i = 0; i < keyProfesionalsMV.length; i++) {
//             window.localStorage.removeItem(keyProfesionalsMV[i]);
//         }
//     }

//     //Validar coherencia de fechas
//     function validarFechasSesiones() {
//         if (
//             date1LessDate2(formatoYMD(dateS2MV.value), formatoYMD(dateS1MV.value)) ||
//             date1LessDate2(FECHA_ACTUAL, formatoYMD(dateS2MV.value))
//         ) {
//             alert("Por favor verifique la fecha de la sesión 2");
//             dateS2MV.value = "";
//             dateS2MV.style.backgroundColor = bgError;
//         } else {
//             dateS2MV.style.backgroundColor = bgSuccess;
//         }
//     }
// } catch (error) { }

/* ----------------------------------------------------------------------
-------------------PLAN DE CUIDADO INSTITUCIONAL ------------------------ 
-----------------------------------------------------------------------*/
try {
    //Teléfono
    let tel1_pci = document.querySelector("#valorControl5628");
    tel1_pci.addEventListener("keyup", function () {
        this.value.length != 10 && this.value.length != 7 ?
            ((this.style.backgroundColor = bgError), toggleBtnActualizacion(0)) :
            ((this.style.backgroundColor = bgSuccess), toggleBtnActualizacion(1));
    });

    let tel2_pci = document.querySelector("#valorControl5629");
    tel2_pci.addEventListener("keyup", function () {
        this.value.length != 10 && this.value.length != 7 ?
            ((this.style.backgroundColor = bgError), toggleBtnActualizacion(0)) :
            ((this.style.backgroundColor = bgSuccess), toggleBtnActualizacion(1));
    });

    //Correo
    let correo1_pci = document.querySelector("#valorControl5630");
    correo1_pci.setAttribute("type", "email");

    //Tipo institución
    let tipoInstOtra_pci = document.querySelector("#valorControl5831");
    let tipoInst_pci = document.querySelector("#valorControl5632");
    tipoInst_pci.addEventListener("change", function () {
        this.value == "2239" ?
            (tipoInstOtra_pci.required = true) :
            (tipoInstOtra_pci.required = false);
    });
} catch (error) { }

//Fecha implementación
try {
    if (BASE_FICHA.value == "59") {
        //Apariencia temática
        let boxSesion = document.querySelector(
            "#formularioNuevoFormato > div:nth-child(12) > div:nth-child(1)"
        );
        let boxLinea = document.querySelector(
            "#formularioNuevoFormato > div:nth-child(12) > div:nth-child(2)"
        );
        let boxDimension = document.querySelector(
            "#formularioNuevoFormato > div:nth-child(12) > div:nth-child(3)"
        );
        let boxesPCI = [boxSesion, boxLinea, boxDimension];

        for (let i = 0; i < boxesPCI.length; i++) {
            boxesPCI[i].classList.remove("col-md-3");
            boxesPCI[i].classList.add("col-md-4");
        }

        let fechaPlan = document.querySelector("#valorControl5649");
        fechaPlan.pattern = patronDMY;

        resizeMultiplesSelects();
    }
} catch (error) { }
//Fecha relatoría
try {
    let fechaRelatoria = document.querySelector("#valorControl5602");
    fechaRelatoria.pattern = patronDMY;

    let box_lineaoperativa = document.querySelector(
        "#formularioNuevoFormato > div:nth-child(12) > div:nth-child(2)"
    );
    box_lineaoperativa.removeAttribute("class");
    box_lineaoperativa.setAttribute("class", "col-md-4");

    let box_numSesion = document.querySelector(
        "#formularioNuevoFormato > div:nth-child(12) > div:nth-child(1)"
    );
    box_numSesion.removeAttribute("class");
    box_numSesion.setAttribute("class", "col-md-4");

    let box_fechaRelatoria = document.querySelector(
        "#formularioNuevoFormato > div:nth-child(12) > div:nth-child(3)"
    );
    box_fechaRelatoria.removeAttribute("class");
    box_fechaRelatoria.setAttribute("class", "col-md-4");

    let box_texto = document.querySelector(
        "#formularioNuevoFormato > div:nth-child(12) > div.col-md-3"
    );
    box_texto.removeAttribute("class");
    box_texto.setAttribute("class", "col-md-12");

    let textarea_relatoria = document.querySelector("#valorControl5603");
    textarea_relatoria.setAttribute("cols", "12");
    textarea_relatoria.setAttribute("rows", "6");

    let table_textArea = document.querySelector(
        "#formularioNuevoFormato > div:nth-child(12) > div.col-md-12 > table"
    );
    table_textArea.removeAttribute("style");
    table_textArea.style.width = "100%";

    let table_lineaoperativa = document.querySelector(
        "#formularioNuevoFormato > div:nth-child(12) > div.col-md-1 > table"
    );
    table_lineaoperativa.removeAttribute("style");
    table_lineaoperativa.style.width = "100%";
} catch (error) { }

/*------------------------------------------------------------------------------
  ----------------------Instituciones protección y atención-----------------------
  ------------------------------------------------------------------------------*/

try {
    //Temporal habilitar campos de usuario
    let containerDataUserIPA = document.querySelector(
        "#formularioNuevoFormato > b > b > div:nth-child(2)"
    );
    let inputsContainersUserIPA =
        containerDataUserIPA.querySelectorAll("input, select");

    inputsContainersUserIPA.forEach((input) => {
        input.removeAttribute("readonly");
    });

    let fechaIntSeg = document.querySelector("#valorControl3693");
    fechaIntSeg.pattern = patronDMY;

    let fechaNacIns = document.querySelector("#valorControl1734");
    fechaNacIns.pattern = patronDMY;
    let nombreInstitucion = document.querySelector("#valorControl1709");

    //Longitud nombre institución
    nombreInstitucion.addEventListener("change", function () {
        this.value.length < 5 ?
            ((this.style.backgroundColor = bgError), toggleBtnActualizacion(0)) :
            ((this.style.backgroundColor = bgSuccess), toggleBtnActualizacion(1));
    });

    let institucionOtra = document.querySelector("#valorControl1724");
    let tipoInstitucion = document.querySelector("#valorControl3015");

    tipoInstitucion.addEventListener("change", function () {
        this.value == "2239" ?
            (institucionOtra.required = true) :
            (institucionOtra.required = false);
    });

    //Longitud institución - otra
    institucionOtra.addEventListener("change", function () {
        this.value.length < 5 ?
            ((this.style.backgroundColor = bgError), toggleBtnActualizacion(0)) :
            ((this.style.backgroundColor = bgSuccess), toggleBtnActualizacion(1));
    });

    //Datos usuarios tamizaje salud mental
    let tipoDocIPA_3 = document.querySelector("#valorControl1731"); //Tipo documento
    let nacionIPA_3 = document.querySelector("#valorControl1741"); //Nacionalidad
    tipoDocIPA_3.addEventListener("change", function () {
        switch (this.value) {
            case "59":
            case "60":
            case "61":
                nacionIPA_3.value = "50";
                nacionIPA_3.style.backgroundColor = bgSuccess;
                toggleBtnActualizacion(1);
                break;

            case "62":
            case "1639":
            case "1640":
            case "2482":
                nacionIPA_3.value == "50" ?
                    ((nacionIPA_3.style.backgroundColor = bgError),
                        toggleBtnActualizacion(0)) :
                    ((nacionIPA_3.style.backgroundColor = bgSuccess),
                        toggleBtnActualizacion(1));

            default:
                break;
        }
    });
} catch (error) { }

/* -----------------------------------------------------------------------
-----------------------------MEDICIÓN TIEMPOS-----------------------------
------------------------------------------------------------------------ */

try {
    var mAux, sAux;
    let tiempo;
    let h = window.localStorage.getItem("h");
    let m = window.localStorage.getItem("m");
    let s = window.localStorage.getItem("s");
    let urlActual = window.location;

    function formatoYMD(fecha) {
        let fechaArray = fecha.split("/");
        let dia = fechaArray[0];
        let mes = fechaArray[1];
        let anio = fechaArray[2];

        return anio + "-" + mes + "-" + dia;
    }

    try {
        if (
            urlActual.href ==
            "http://gesiaplicaciones.saludcapital.gov.co/GESI_sistemas/GESI_Form"
        ) {
            ResetCrono();
        } else if (
            urlActual.href ==
            "http://gesiaplicaciones.saludcapital.gov.co/GESI_sistemas/GESI_Form/formulario"
        ) {
            init();
        }
    } catch (error) { }

    try {
        btnRegresar = document.querySelector(
            "#main_body > div.page-wrapper.chiller-theme.toggled > div > main > div > div > div > div.panel-heading > div > div > table > tbody > tr > td:nth-child(7) > input"
        );
        btnRegresar.addEventListener("click", DetenerCrono);
    } catch (error) { }

    try {
        btnBuscar = document.querySelector(
            "#main_body > div.page-wrapper.chiller-theme.toggled > div > main > div > div > div > div.panel-heading > div > div > table > tbody > tr > td:nth-child(19) > input"
        );
        btnBuscar.addEventListener("click", init);

        let btnNuevaFicha = document.querySelector(
            "#main_body > div.page-wrapper.chiller-theme.toggled > div > main > div > div > div > div.panel-heading > div > div > table > tbody > tr > td:nth-child(26) > input"
        );
        btnNuevaFicha.addEventListener("click", init);
    } catch (error) { }

    function getNombreBase() {
        let base = document.querySelector(
            "#main_body > div.page-wrapper.chiller-theme.toggled > div > main > div > div > div > div.panel-heading > div > div > table > tbody > tr > td:nth-child(1) > b"
        );
        return base.textContent;
    }

    function getNumFicha() {
        let numFicha = document.querySelector("#Ficha_fic");
        return numFicha.value;
    }

    function getUserNewFicha() {
        let user = document.querySelector(
            "#formularioNuevoFormato > input[type=hidden]:nth-child(1)"
        );
        return user.value;
    }

    function DetenerCrono() {
        let tiempoTotal = h + ":" + mAux + ":" + sAux;
        let fechaIntervencion = document.querySelector("#FechaIntervencion").value;
        guardarInfo(tiempoTotal, formatoYMD(fechaIntervencion));
        h = 0;
        m = 0;
        s = 0;
        window.localStorage.setItem("s", s);
        window.localStorage.setItem("m", m);
        window.localStorage.setItem("h", h);
        clearInterval(tiempo);
    }

    function init() {
        //Guardar nombre usuario
        try {
            let user = document.querySelector("paginaUsuario");
            localStorage.setItem("user", user.value);
        } catch (error) { }

        tiempo = setInterval(cronometrar, 1000);
    }

    function ResetCrono() {
        h = 0;
        m = 0;
        s = 0;
        window.localStorage.setItem("s", s);
        window.localStorage.setItem("m", m);
        window.localStorage.setItem("h", h);
    }

    function cronometrar() {
        s++;

        if (s > 59) {
            m++;
            s = 0;
        }
        if (m > 59) {
            h++;
            m = 0;
        }

        if (s < 10) {
            sAux = "0" + s;
        } else {
            sAux = s;
        }
        if (m.length < 2) {
            mAux = "0" + m;
        } else {
            mAux = m;
        }

        window.localStorage.setItem("s", sAux);
        window.localStorage.setItem("m", mAux);
        window.localStorage.setItem("h", h);
        mostrar();
    }

    function mostrar() {
        let seg = window.localStorage.getItem("s");
        let min = window.localStorage.getItem("m");
        let hrs = window.localStorage.getItem("h");
        console.log(hrs + ":" + min + ":" + seg);
    }

    function guardarInfo(tiempoTotal, fechaInt) {
        let base = getNombreBase();
        let ficha = getNumFicha();
        console.log(fechaInt);
        let usuarioFinal;
        let user = localStorage.getItem("user");
        if (user == undefined) {
            usuarioFinal = getUserNewFicha();
        } else {
            usuarioFinal = user;
        }

        $.ajax({
            type: "POST",
            url: "https://gesiapp.com.co/js/tiempoFichas/cronoFichas.php",
            data: {
                base: base,
                user: usuarioFinal,
                tiempo: tiempoTotal,
                ficha: ficha,
                fechaInt: fechaInt,
            },
            success: function (r) {
                console.log(r);
            },
        });
    }
} catch (error) { }

try {
    let TermFicha = document.querySelector(
        "#main_body > div.swal2-container.swal2-center.swal2-shown > div > div.swal2-actions > button.swal2-confirm.swal2-styled"
    );
    TermFicha.addEventListener("click", function () {
        console.log("Guardando");
        let Noficha = document.querySelector("#Ficha_fic").value;
        console.log(Noficha);
        var Nombre = document.querySelector(
            "#main_body > div > div > main > div > div > div > div.panel-heading > div > div.col-md-10 > table > tbody > tr > td:nth-child(2) > b"
        ).textContent;
        console.log(Nombre);
        const digitadas = new XMLHttpRequest();
        digitadas.onreadystatechange = function () {
            if (this.readyState === 4 && this.status === 200) {
                console.log(this.responseText);
            }
        };
        digitadas.open(
            "GET",
            `https://gesiapp.com.co/configuraciones/ReporteDigitado/inmgresarDigitado.php?ficha=${Noficha}&name=${Nombre}`,
            true
        );
        digitadas.send();
    });
} catch (error) { }

