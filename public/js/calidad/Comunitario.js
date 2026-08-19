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
            console.error(error);
        }
    }

    if (!ModuleInstance) {
        console.error('No se pudo importar el módulo desde ninguna URL.');
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
const bgTransparent = "rgba(255, 255, 255, 0) ";

//FECHAS
const PATRON_DMY = "[0-9]{2}/[0-9]{2}/[0-9]{4}";
const PATRON_YMD = "[0-9]{4}-[0-9]{2}-[0-9]{2}";

const FECHA_ACTUAL = new Date();
// const ANIO_ACTUAL = FECHA_ACTUAL.getFullYear();
//const MES_ACTUAL = FECHA_ACTUAL.getMonth();

//DATOS FICHA
const FECHA_INTERVENCION = document.querySelector("#FechaIntervencion");
const CONSECUTIVO_FICHA = document.querySelector("#Consecutivo_fic");
const LOCALIDAD_FICHA = document.querySelector("#Localidad_fic");
const BASE_FICHA = document.querySelector("#Id_Base");


try {
    const { sesiones } = await importModule('Comunitario/Sesiones.js', 'sesiones');
} catch (error) {
    console.error("Error cargando modulos", error);
}

/**
 * TÍTULO CODE
 */

function configMenu() {
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
    logo.style.backgroundColor = "#124975";
    logo.style.color = "#fff";
    logo.innerHTML = "<h3>CODE COMUNITARIO</h3>";

    const menu = () => {
        // const ocultarBases = [
        //     3, 4, 6, 8, 9, 14, 17, 18, 19, 20, 21, 22, 23, 25, 26, 27, 28,
        //     29, 30, 31, 32,
        // ];

        // const menuBases = ocultarBases.map((id) =>
        //     document.getElementById(`forOpcionPublicoGesiForm${id}`)
        // );
        // menuBases.forEach((itemMenu) => {
        //     if (itemMenu) {
        //         itemMenu.style.display = "none";
        //     }
        // });

        const optionComunitario = document.querySelector(
            "#sidebar > div > div.sidebar-menu > ul > li:nth-child(4)"
        );
        optionComunitario.style.backgroundColor = "#124975";
    };

    menu();
}

configMenu();

/**
 * FUNCIONES GLOBALES
 */

function existElement(elementId) {
    return document.getElementById(elementId) ? true : false;
}

if (existElement("formularioNuevoFormato")) {
    document
        .getElementById("formularioNuevoFormato")
        .addEventListener("change", (event) => {
            const input = event.target;
            const inputTagName = event.target.tagName;
            const inputType = event.target.type;

            if (inputTagName === "INPUT" && inputType == "text") {
                longitudTextoMenorQue(input, 3);
            }

            if (
                inputTagName === "INPUT" &&
                input.getAttribute("placeholder") == "DD/MM/AAAA"
            ) {
                input.setAttribute("pattern", PATRON_DMY);
            }

            establecerIndicadoresInputs();
        });
}

function establecerIndicadoresInputs() {
    const campos = document.querySelectorAll(
        "select, input:not([type='submit']), textarea"
    );

    campos.forEach((campo) => {
        if (campo.disabled) {
            campo.style.backgroundColor = bgDisabled;
            return;
        }

        const tieneError =
            window.getComputedStyle(campo).backgroundColor === bgError;

        if (tieneError) return;

        const valorTrim = campo.value.trim();
        const esRequerido = campo.required;


        if (esRequerido && valorTrim === "" && !tieneError) {
            campo.style.backgroundColor = bgWarning;
        } else if (!esRequerido && valorTrim === "" && !tieneError) {
            campo.style.backgroundColor = bgWhite;
        } else if (valorTrim !== "" && !tieneError) {
            campo.style.backgroundColor = bgSuccess;
        }
    });
}

function telefonoValido(inputId) {
    const input = document.getElementById(inputId);
    return input.value.length == 7 || input.value.length == 10;
}

/**
 * 
 * @param {string[]} inputsFechaIds 
 */

function aplicarPatronFecha(inputsFechaIds) {
    if (!Array.isArray(inputsFechaIds)) {
        throw new Error("Inputs de fecha deben ser un array");
    }

    const inputsFecha = inputsFechaIds.map((id) =>
        document.getElementById(`valorControl${id}`)
    );

    inputsFecha.forEach((input) => {
        if (input) {
            input.pattern = PATRON_DMY;
            input.addEventListener("change", () => {
                if (fechaMayorQueHoy(input.value)) {
                    input.value = "";
                    alert("La fecha no puede ser mayor a la actual");
                }
            });
        }
    });
}

/**
 *
 * @param {string[]} inputsIds
 */
function establecerCamposRequeridos(inputsIds, requerir = true) {
    const ids = Array.isArray(inputsIds) ? inputsIds : [inputsIds];
    ids.forEach(id => {
        const input = document.getElementById(`valorControl${id}`);
        if (input) {
            input.required = requerir;
        }
    });
}

/**
 *
 * @param {string} tipoDoc
 * @param {HTMLInputElement} inputNacionalidad
 */
function tipoDocVsNacionalidad(tipoDoc, inputNacionalidad) {
    const tiposDocColombianos = [
        "59",
        "60",
        "61",
        "5",
        "7",
        "8",
        "1- CC",
        "2- RC",
        "3- TI",
    ];
    inputNacionalidad.value = tiposDocColombianos.includes(tipoDoc)
        ? "Colombia"
        : "";
}

/**
 * @param {HTMLInputElement} inputSexo
 * @param {HTMLInputElement} inputGenero
 * @param {string} inputActivo
 * @param {HTMLInputElement} inputPdi
 */
function sexoYGenero(inputSexo, inputGenero, inputIdActivo, inputPdi = null) {
    if (inputSexo.value === "3- Intersexual" || inputSexo.value === "69")
        return;

    const validarPDI = () => {
        const generoPdi = {
            67: "117",
            68: "116",
            "1- Hombre": "11- Mujer Transgénero",
            "2- Mujer": "10- Hombre Transgénero",
        };

        if (!inputPdi) return;

        const valueToAdd = generoPdi[inputSexo.value];

        if (valueToAdd) {
            const selectedValues = Array.from(inputPdi.selectedOptions).map(
                (opt) => opt.value
            );

            if (!selectedValues.includes(valueToAdd)) {
                selectedValues.push(valueToAdd);
            }

            Array.from(inputPdi.options).forEach((option) => {
                option.selected = selectedValues.includes(option.value);
            });
        }
    };

    const sexo = () => {
        const sexosGeneros = {
            "1- Hombre": "1- Masculino",
            "2- Mujer": "2- Femenino",
            "3- Intersexual": "3- Transgénero",
            67: "70",
            68: "71",
            69: "72",
        };

        inputGenero.value = sexosGeneros[inputSexo.value];
    };

    const genero = () => {
        const generosSexos = {
            "1- Masculino": "1- Hombre",
            "2- Femenino": "2- Mujer",
            70: "67",
            71: "68",
        };

        inputSexo.value ||= generosSexos[inputGenero.value];
        if (
            (inputGenero.value == "72" ||
                inputGenero.value == "3- Transgénero") &&
            inputPdi
        ) {
            validarPDI();
        }
    };

    if (inputIdActivo == inputSexo.id) sexo();
    if (inputIdActivo == inputGenero.id) genero();
}

function pdiVsDiscapacidad(pdiValue, inputIdCategoria) {
    const inputCategoria = document.getElementById(inputIdCategoria);
    let disabledNoAplica = false;

    const valoresDiscapacidad = ["2- Discapacidad", "108"];
    let valueCategoria = "3822";

    if (valoresDiscapacidad.includes(pdiValue)) {
        valueCategoria = "";
        disabledNoAplica = true;
    }

    if (inputCategoria) {
        inputCategoria.value = valueCategoria;
        inputCategoria.disabled = false;
    }

    const options = inputCategoria.querySelectorAll("option");
    [...options].map((option) => {
        if (option.value == "3822") {
            option.disabled = disabledNoAplica;
        }
    });
}

/**
 * 
 * @param {} camposId 
 */

function validarPoblacional(camposId) {
    const inputTipoDoc = document.getElementById(camposId.tipoDoc);
    const inputPdi = document.getElementById(camposId.pdi);
    const inputNacionalidad = document.getElementById(camposId.nacionalidad);
    const inputDocumento = document.getElementById(camposId.documento);
    const inputSexo = document.getElementById(camposId.sexo);
    const inputGenero = document.getElementById(camposId.genero);

    if (inputTipoDoc) {
        inputTipoDoc.addEventListener("change", () => {
            tipoDocVsNacionalidad(inputTipoDoc.value, inputNacionalidad);
            configurarCampoDocumento(inputDocumento, inputTipoDoc.value);
        });
    }

    if (inputSexo && inputGenero) {
        [inputSexo, inputGenero].forEach((input) => {
            input.addEventListener("change", (e) =>
                sexoYGenero(inputSexo, inputGenero, e.target.id, inputPdi)
            );
        });
    }

    if (inputPdi) {
        inputPdi.addEventListener("change", () =>
            pdiVsDiscapacidad(inputPdi.value, camposId.categoriaDiscapacidad)
        );
    }
};

function longitudTextoIgualQue(inputTexto, longitud) {
    return inputTexto.value.length == longitud;
}

function longitudTextoEntre(inputTexto, minimo, maximo) {
    return (
        inputTexto.value.length >= minimo && inputTexto.value.length <= maximo
    );
}

function longitudTextoMenorQue(input, longitudMinima) {
    input.style.backgroundColor =
        input.value.length < longitudMinima ? bgError : bgTransparent;
}

function configurarCampoDocumento(inputDocumento, tipoDoc) {
    const tiposDocCon10Digitos = ["60", "61", "2- RC", "3- TI"];
    const tiposDocTipoText = [
        "7- Adulto sin ID.",
        "8- Menor sin ID.",
        "65",
        "66",
    ];
    const min = tiposDocCon10Digitos.includes(tipoDoc) ? 1000000000 : null;
    const max = tiposDocCon10Digitos.includes(tipoDoc) ? 1999999999 : null;

    inputDocumento.type = tiposDocTipoText.includes(tipoDoc)
        ? "text"
        : "number";

    inputDocumento.setAttribute("min", min);
    inputDocumento.setAttribute("max", max);
}

function fechaMayorQueHoy(fechaStr) {
    FECHA_ACTUAL.setHours(0, 0, 0, 0);

    const [dia, mes, anio] = fechaStr.split("/").map(Number);
    const fechaIngresada = new Date(anio, mes - 1, dia);

    return fechaIngresada > FECHA_ACTUAL;
}

function setInputsTypeNumber(inputsId) {
    if (Array.isArray(inputsId)) {
        inputsId.forEach((inputId) => {
            const input = document.getElementById(`valorControl${inputId}`);
            input.setAttribute("type", "number");
        });
    }
    if (typeof inputsId === "string") {
        const input = document.getElementById(`valorControl${inputsId}`);
        input.setAttribute("type", "number");
    }
}

function toggleCamposDireccion(inputIdZona, idsRurales = [], idsUrbanos = [], urbanosRequeridos = false) {
    const inputZona = document.getElementById(`valorControl${inputIdZona}`);
    if (!inputZona) return console.warn(`Input con ID valorControl${inputIdZona} no encontrado`);

    const actualizarInputs = (esRural) => {
        const toggleInputs = (ids, activar) => {
            ids.forEach(inputId => {
                const input = document.getElementById(`valorControl${inputId}`);
                if (input) {
                    input.value = activar ? input.value : "";
                    input.disabled = !activar;
                    if (urbanosRequeridos && ids === idsUrbanos) {
                        input.required = activar
                    } else if (ids === idsRurales) {
                        input.required = activar
                    }
                }
            });
        };

        toggleInputs(idsRurales, esRural);
        toggleInputs(idsUrbanos, !esRural);
    };

    const validarZona = () => {
        const esRural = inputZona.value === "58" || inputZona.value === "2- Rural";
        actualizarInputs(esRural);
    };

    validarZona();
    inputZona.addEventListener("change", validarZona);
}

function disabledInputIdFieldIgualTo(
    inputIdToDisabled,
    inputValue,
    igualTo = "NO"
) {
    const input = document.getElementById(inputIdToDisabled);
    input.disabled = inputValue == igualTo;
}

function diferenciaAnios(fecha1, fecha2) {
    const inicio = new Date(fecha1);
    const fin = new Date(fecha2);
    const diffDias = Math.floor(Math.abs(fin - inicio) / (1000 * 60 * 60 * 24));
    return (diffDias / 365.25).toFixed(2); // Convertir a años
}

function insertOptionsList(values = [], ...onInputsIds) {
    const inputs = onInputsIds.map((id) =>
        document.getElementById(`valorControl${id}`)
    );

    inputs.forEach((input) => {
        if (input) {
            const select = document.createElement("select");
            select.id = input.id;
            select.name = input.name;
            select.className = "form-control";
            select.style.width = "100%";

            values.forEach((value) => {
                const option = document.createElement("option");
                option.value = value;
                option.textContent = value;
                select.appendChild(option);
            });

            const inputValue = input.value.trim();
            if (values.includes(inputValue)) {
                select.value = inputValue;
            }

            input.parentNode.replaceChild(select, input);
        }
    });
}

function ejecutarFuncionEventoInput(inputId, evento, funcion) {
    const input = document.getElementById(inputId);
    input.addEventListener(evento, funcion);
    funcion();
}

function configInputsForm() {
    //Autocompletado
    const quitarAutocompletado = () => {
        const inputs = document.querySelectorAll("input");
        for (let i = 0; i < inputs.length; i++) {
            if (inputs[i].autocomplete !== "off") {
                inputs[i].setAttribute("autocomplete", "off");
            }
        }
    };
    quitarAutocompletado();

    //Patron fecha
    const patronFecha = () => {
        const inputsFecha = document.querySelectorAll("input[placeholder='DD/MM/AAAA']");

        inputsFecha.forEach(input => {
            if (input) {
                input.setAttribute("pattern", PATRON_DMY);
                input.addEventListener("change", () => {
                    if (fechaMayorQueHoy(input.value)) {
                        input.value = "";
                        alert("La fecha no puede ser mayor a la actual");
                    }
                });
            }
        });
    }

    patronFecha();
}

configInputsForm();

/**
 * CREACIÓN DE FICHA
 */

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

function disabledBtnActualizar(isDisabled) {
    const btnActualizar = document.getElementById("botonActualizarInformacion");
    if (btnActualizar) {
        btnActualizar.disabled = isDisabled;
    }
}

function validarCamposCreacionFicha() {
    const consecutivoValido = CONSECUTIVO_FICHA.value.length === 6;

    const fechaValida =
        getMonthIntervention() === 8 && getYearIntervention() === 2026;
    CONSECUTIVO_FICHA.style.backgroundColor = consecutivoValido
        ? bgSuccess
        : bgError;
    FECHA_INTERVENCION.style.backgroundColor = fechaValida
        ? bgSuccess
        : bgError;
    disabledBtnActualizar(!(consecutivoValido && fechaValida));
}

function initCreation() {
    if (CONSECUTIVO_FICHA && CONSECUTIVO_FICHA.value == '' && LOCALIDAD_FICHA.value == '') {
        CONSECUTIVO_FICHA.disabled = LOCALIDAD_FICHA.disabled = true;
    }

    const esFechaValida = () => {
        return getMonthIntervention() === 8 && getYearIntervention() === 2026;
    }

    const esDiferenteDeHoy = () => {
        return !fechaMayorQueHoy(FECHA_INTERVENCION.value);
    }

    if (FECHA_INTERVENCION) {
        FECHA_INTERVENCION.addEventListener("change", () => {
            CONSECUTIVO_FICHA.disabled = LOCALIDAD_FICHA.disabled = !esFechaValida() && esDiferenteDeHoy();
        });

        FECHA_INTERVENCION.addEventListener("focus", () => {
            CONSECUTIVO_FICHA.disabled = LOCALIDAD_FICHA.disabled = !esFechaValida();
        });
    }
}

if (CONSECUTIVO_FICHA) CONSECUTIVO_FICHA.addEventListener("keyup", validarCamposCreacionFicha);
if (LOCALIDAD_FICHA) LOCALIDAD_FICHA.addEventListener("change", validarCamposCreacionFicha);

initCreation();
/**
 * SELECCIÓN DE BASE
 */

function ejecutarValidadorBase(idBase) {
    switch (idBase) {
        case "39":
            sesionesColectivas();
            break;
        case "93":
            puntosPID();
            break;
        case "92":
            intervencionesPID();
            break;
        case "31":
            spaciosDeBienestar();
            break;
        case "49":
            maps();
            break;
        case "98":
            fichasDeEvaluacion();
        default:
            console.error("Base no válida");
            break;
    }

    establecerIndicadoresInputs();
}

if (BASE_FICHA) ejecutarValidadorBase(BASE_FICHA.value);

/* --------------------------------------------------------------------------
----------------------------SESIONES COLECTIVAS------------------------------
---------------------------------------------------------------------------*/

async function sesionesColectivas() {
    const { SesionesColectivas } = await importModule('Comunitario/SesionesColectivas.js', 'SesionesColectivas');

    if (!SesionesColectivas) {
        console.error("No se pudo importar el módulo SesionesColectivas.");
        return;
    }

    SesionesColectivas.selectPage();
    establecerIndicadoresInputs();
}

/* --------------------------------------------------------------------------
----------------------------INTERVENCIONES PID-------------------------------
---------------------------------------------------------------------------*/
function intervencionesPID() {
    const newOption = document.createElement("option");
    newOption.value = "16";
    newOption.textContent = "16-Puente Aranda";
    LOCALIDAD_FICHA.appendChild(newOption);
}

/* --------------------------------------------------------------------------
----------------------------SPACIOS DE BIENESTAR-----------------------------
---------------------------------------------------------------------------*/
async function spaciosDeBienestar() {
    const { SpaciosDeBienestar } = await importModule('Comunitario/SpaciosDeBienestar.js', 'SpaciosDeBienestar');

    if (!SpaciosDeBienestar) {
        console.error("No se pudo importar el módulo SpaciosDeBienestar.");
        return;
    }

    SpaciosDeBienestar.initValidate();

    //Dirección
    spaciosDireccion();

    //Edad vs otros campos
    validacionesEdad();

    // Manzana del cuidado
    const valorControl15215 = document.querySelector("#valorControl15215")?.value;
    if (valorControl15215) {
        disabledInputIdFieldIgualTo("valorControl15216", valorControl15215);
    }

    //Assist
    spaciosAssist();

    //Pre test
    const fechaPretest = document.getElementById("valorControl15426");
    fechaPretest.value = FECHA_INTERVENCION.value;
    fechaPretest.readOnly = true;

    // Indicadores
    establecerIndicadoresInputs();
}

function spaciosDireccion() {
    const camposDireccion = {
        rurales: ['15221', '15222', '15223'],
        urbanos: ['15203', '15204', '15209', '15213']
    };
    toggleCamposDireccion('15198', camposDireccion.rurales, camposDireccion.urbanos, true)
}

function validacionesEdad() {
    const campoNacimiento = document.getElementById('valorControl15182');
    const campoEdad = document.getElementById('valorControl15472');

    campoNacimiento.addEventListener('change', () => {
        spaciosCamposSegunEdad(campoEdad.value);
    });
    spaciosCamposSegunEdad(campoEdad.value);
}

function spaciosCamposSegunEdad(edad) {
    const camposAcudiente = () => {
        const containerAcudiente = document.querySelector("#formularioNuevoFormato > div:nth-child(18)");

        if (!containerAcudiente) {
            console.error("No se encontró el contenedor de acudiente.");
            return;
        }

        const esIngresoJovenes = document.getElementById('valorControl15167')?.value === 'Si';
        const requiereAcudiente = edad < 18 || esIngresoJovenes;
        const camposNoRequeridosAcudiente = ['valorControl15227', 'valorControl15229'];

        containerAcudiente.querySelectorAll('input, select').forEach(input => {
            if (!camposNoRequeridosAcudiente.includes(input.id)) {
                input.required = requiereAcudiente;
            }

            input.disabled = !requiereAcudiente;
        });

        const jefeHogar = document.getElementById('valorControl15194');
        if (jefeHogar) {
            jefeHogar.required = requiereAcudiente;
            jefeHogar.disabled = !requiereAcudiente;
        }
    }

    camposAcudiente();

    const carlosCrafft = () => {
        const containerCarlosCrafft = document.querySelector("#formularioNuevoFormato > div:nth-child(40)");

        if (!containerCarlosCrafft) {
            console.error("No se encontró el contenedor de Carlos Crafft.");
            return;
        }

        const requiereCarlosCrafft = edad < 16;

        containerCarlosCrafft.querySelectorAll('select').forEach(input => {
            input.required = requiereCarlosCrafft;
            input.disabled = !requiereCarlosCrafft;
        });
    }

    carlosCrafft();
}

function spaciosAssist() {
    // Contenedor sección Assist
    const container = document.querySelector("#formularioNuevoFormato > div:nth-child(30)");
    if (!container) {
        console.warn("No se encontró el contenedor con el selector especificado.");
        return;
    }

    const validarAssist = () => {
        // Seleccionar todos los select dentro del contenedor
        const selects = Array.from(container.querySelectorAll("select"));

        selects.slice(10, 70).forEach(select => {
            select.disabled = select.value === '';
        });

        selects.slice(0, 10).forEach((select, index) => {
            select.addEventListener("change", () => {
                let salto = 10;
                const estaLLeno = select.value != '';

                do {
                    const selectIndex = selects[index + salto];
                    if (!selectIndex) break;

                    //selectIndex.disabled = !esSi;
                    selectIndex.required = select.value != '';
                    const selectOptions = Array.from(selectIndex.options).map(option => option.value);

                    if (estaLLeno) {
                        selectIndex.disabled = false;
                        selectIndex.value ||= (selectOptions.includes("0 = Nunca") ? "0 = Nunca" : "0 = No, Nunca");
                    } else {
                        selectIndex.value = '';
                        selectIndex.disabled = true;
                    }

                    salto += 10;
                } while (index + salto < (selects.length - 2));
            });
        });
    }

    validarAssist();

    const inhabilitarTotales = () => {
        const inputs = Array.from(container.querySelectorAll("input"));
        inputs.slice(70, 80).forEach(input => {
            input.readOnly = true;
        });
    }

    inhabilitarTotales();
}

/* --------------------------------------------------------------------------
------------------------------------MAPS-------------------------------------
---------------------------------------------------------------------------*/

async function maps() {
    const { Maps } = await importModule('Comunitario/Maps.js', 'Maps');

    if (!Maps) {
        console.error("No se pudo importar el módulo Maps.");
        return;
    }

    Maps.selectPage();

    // Indicadores
    establecerIndicadoresInputs();
}

/* --------------------------------------------------------------------------
--------------------------------PUNTOS PID ----------------------------------
---------------------------------------------------------------------------*/
function puntosPID() {
    const relacionContactoEmergencia = document.getElementById("valorControl13740");
    const telefonoEmergencias = document.getElementById("valorControl14114");
    const sabeDelConsumo = document.getElementById("valorControl13741");
    relacionContactoEmergencia.addEventListener('change', () => {
        if (relacionContactoEmergencia.value == '3375') {
            telefonoEmergencias.required = sabeDelConsumo.required = false;
            telefonoEmergencias.disabled = sabeDelConsumo.disabled = true;
            telefonoEmergencias.value = sabeDelConsumo.value = '';
        } else {
            telefonoEmergencias.required = sabeDelConsumo.required = true;
            telefonoEmergencias.disabled = sabeDelConsumo.disabled = false;
        }

    })
}

function fichasDeEvaluacion() {
    const escucharParaMasBienestar = () => {
        const campoSumatoria = document.getElementById('valorControl15530');
        campoSumatoria.readOnly = true;
    }

    // PASAR DE POSTEST A PRETEST
    // const dispositivos = () => {
    //     const containerPosTest = document.querySelector("#formularioNuevoFormato > div:nth-child(30)");
    //     const containerPreTest = document.querySelector("#formularioNuevoFormato > div:nth-child(20)");

    //     const inputsPosTest = containerPosTest.querySelectorAll('select, input');
    //     const inputsPreTest = containerPreTest.querySelectorAll('select, input');
    //     inputsPosTest.forEach((inputPostest, key) => {
    //         inputsPreTest[key].value ||= inputPostest.value;
    //         inputPostest.value = '';

    //     });
    // }

    const titleSection = document.querySelector("#formularioNuevoFormato > div:nth-child(11) > div > b");
    const sectionMap = {
        "DATOS DEL LUGAR DE LA ACTIVIDAD": escucharParaMasBienestar,
        //"ENCABEZADO GENERAL": dispositivos
    }

    const action = sectionMap[titleSection?.textContent];
    if (action) action();
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
