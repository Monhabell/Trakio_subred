/**
 * VARIABLES GLOBALES
 */

//COLORES
const bgError = "rgba(255, 50, 50, 0.4)";
const bgSuccess = "rgba(50, 255, 50, 0.7)";
const bgWarning = "rgba(253, 122, 122, 0.7)";
const bgPrimary = "rgba(90, 100, 255, 0.5)";
const bgWhite = "#fff";
const bgDisabled = "rgba(128, 128, 128, 0.2)";
const bgTransparent = "rgba(255, 255, 255, 0) ";
const COLOR_BLANCO = "#ffffff";
const COLOR_ERROR = "rgba(255, 50, 50, 0.4)";

//FECHAS
const PATRON_DMY = "[0-9]{2}/[0-9]{2}/[0-9]{4}";
const PATRON_YMD = "[0-9]{4}-[0-9]{2}-[0-9]{2}";

const FECHA_ACTUAL = new Date();
const ANIO_ACTUAL = FECHA_ACTUAL.getFullYear();
const MES_ACTUAL = FECHA_ACTUAL.getMonth();

//DATOS FICHA
const FECHA_INTERVENCION = document.querySelector("#FechaIntervencion");
const CONSECUTIVO_FICHA = document.querySelector("#Consecutivo_fic");
const LOCALIDAD_FICHA = document.querySelector("#Localidad_fic");
const BASE_FICHA = document.querySelector("#Id_Base");


const mainContainer = document.querySelector('main');
let ID_BASE = ifExists('Id_Base') ? document.getElementById('Id_Base').value : '';


let EDAD;

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

try {
    const modules = [
        ['laboral/NnaValidador.js', 'NnaValidator'],
        ['laboral/sesiones.js', 'sesiones'],
        ['laboral/Menstrual.js', 'Menstrual'],
        ['laboral/Formal.js', 'formnal'],
        ['laboral/Audit.js', 'audit'],
        ['laboral/Cancer.js', 'cancer'],
        ['laboral/Rqc.js', 'rqc'],
        ['laboral/Findrisc.js', 'findrisc'],
        ['laboral/Oms.js', 'oms'],
        ['laboral/Sqr.js', 'sqr'],
        ['laboral/Ut.js', 'ut']
    ];



    const results = await Promise.allSettled(
        modules.map(([path, name]) => importModule(path, name))
    );

    results.forEach((result, i) => {
        if (result.status === "rejected") {
            console.error(`Error cargando ${modules[i][1]}`, result.reason);
        }
    });




} catch (error) {
    console.error("Error cargando NnaValidator", error);
}

try {
    const { DateValidator } = await importModule('Clases/Datevalidator.js', 'DateValidator');

} catch (error) {
    console.error(error);
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

/**
 * FUNCIONES GENERALES
 */
function ifExists(campoId) {
    return document.getElementById(campoId);

}
function styleTitulo() {
    const sidebarBrand = document.querySelector('.sidebar-brand');
    sidebarBrand.style.backgroundColor = "#f10000ff";
    sidebarBrand.textContent = "CODE LABORAL";
    sidebarBrand.style.fontSize = "28px";
    sidebarBrand.style.color = COLOR_BLANCO;
    mainContainer.style.backgroundColor = "#31353d";
}

function styleBackground() {
    const stylesBackground = {
        backgroundColor: "rgba(42, 43, 44, 0.21)",
        color: "rgb(255, 255, 255)"
    };

    const formContainer = document.querySelector(".panel");
    formContainer.style.borderColor = "rgba(65, 65, 65, 0.16)";

    const stylesHeaders = {
        backgroundColor: COLOR_NEGRO,
    }

    document.querySelectorAll('#main_body div, #main_body form, #main_body input, #main_body select, #main_body option, #main_body td')
        .forEach(element => Object.assign(element.style, stylesBackground));

    document.querySelectorAll('#main_body b')
        .forEach(element => {
            const padre = element.parentNode;

            if (padre.parentNode.classList.contains('align-items-center') && padre.parentNode.classList.contains('row')) {
                Object.assign(padre.parentNode.style, stylesHeaders);
                Object.assign(padre.style, stylesHeaders);
            }

        });

    document.querySelectorAll('#main_body table')
        .forEach(element => element.style.borderColor = "transparent");
}

function colorearInputs() {
    const allInputs = document.querySelectorAll('input,select');
    allInputs.forEach(input => {
        if (getComputedStyle(input).backgroundColor == COLOR_ERROR) return;
        if (input.type != 'submit' && input.value != "") input.style.backgroundColor = COLOR_LLENO;
        if (input.type != 'submit' && input.disabled) input.style.backgroundColor = COLOR_DISABLED;
        if (input.type != 'submit' && input.required && input.value == "" && !input.disabled) input.style.backgroundColor = COLOR_REQUERIDO;
        if (input.type != 'submit' && !input.required && input.value == "" && !input.disabled) input.style.backgroundColor = "transparent";
        // if (input.type != 'submit' && input.value == "" && !input.required && input.readOnly != '1') input.style.backgroundColor = COLOR_NEGRO;
    })
}

function eventoColorearInputs() {
    document.addEventListener('change', (e) => {
        const input = e.target;
        const typeInputs = ['SELECT', 'INPUT'];
        if (typeInputs.includes(input.tagName)) {
            colorearInputs();
        }
    })
    colorearInputs();
}





function disabledOptionsFromSelect(
    idSelect,
    optionsValueToDisabled,
    booleanDisabled
) {
    const select = document.getElementById(idSelect);
    //const optionsSelect = select.querySelectorAll("option");

    // Deshabilitar todas las opciones al principio
    // optionsSelect.forEach((option) => {
    //     option.disabled = !booleanDisabled;
    // });

    // if (Array.isArray(optionsValueToDisabled)) {
    //     // Si optionsValueToDisabled es un array
    //     optionsSelect.forEach((option) => {
    //         if (optionsValueToDisabled.includes(option.value)) {
    //             option.remove();
    //         }
    //     });
    // } else {
    //     // Si optionsValueToDisabled es un solo valor
    //     optionsSelect.forEach((option) => {
    //         if (option.value == optionsValueToDisabled) {
    //             option.remove();

    //         }
    //     });
    // }
}

function modificarBotonGuardarParaGentePendeja(isDisabled) {
    const BtnGuardar = document.getElementById('botonActualizarInformacion');
    BtnGuardar.hidden = isDisabled;
    BtnGuardar.disabled = isDisabled;
}

function date1LessDate2(date1, date2) {
    return new Date(date1) < new Date(date2);
}

function formatoYMD(fecha) {
    let fechaArray = fecha.split("/");
    let dia = fechaArray[0];
    let mes = fechaArray[1];
    let anio = fechaArray[2];

    return anio + "-" + mes + "-" + dia;
}

function tipoDocVsNacionalidad(campoDocId, campoNacId) {
    const tipoDoc = document.getElementById(campoDocId);
    const nacionalidad = document.getElementById(campoNacId);
    const docsColombianos = ['60', '61', '59', '63'];

    tipoDoc.addEventListener('change', () => {
        nacionalidad.value = docsColombianos.includes(tipoDoc.value) ? '50' : '';
    })
}

function digitosDoc(tipoDocId, docId) {
    const noDoc = document.getElementById(docId);
    const tipoDoc = document.getElementById(tipoDocId);
    const docsColombianos = ['60', '61', '63'];
    let deshabilitarBoton = false;
    let color;

    noDoc.addEventListener('input', () => {

        if (docsColombianos.includes(tipoDoc.value) && noDoc.value.length != 10) {
            color = COLOR_ERROR;
            deshabilitarBoton = true;
        }
        else if (tipoDoc.value == '59' && noDoc.value.length > 10) {
            color = COLOR_ERROR;
            deshabilitarBoton = true;
        } else {
            color = COLOR_LLENO;
            deshabilitarBoton = false;
        }

        noDoc.style.backgroundColor = color;
        modificarBotonGuardarParaGentePendeja(deshabilitarBoton);
    })
}

function sexoVsGenero(campoSexo, CampoGenero) {
    const sexo = document.getElementById(campoSexo);
    const genero = document.getElementById(CampoGenero);
    sexo.addEventListener('change', () => {
        genero.value = sexo.value == 67 ? 70 : 71;
        sexoVsLactante(sexo.value);
    });
}


function afiliacion(afiliacionId, eapbId) {
    const afiliacion = document.getElementById(afiliacionId);
    const epsList = [
        "",
        "ALIANSALUD EPS",
        "ANAS WAYUU EPS-I",
        "ASMET SALUD",
        "ASOCIACION INDIGENA DEL CAUCA EPSI",
        "CAJACOPI ATLANTICO",
        "CAPITAL SALUD EPS",
        "CAPRESOCA",
        "COMFACHOCO",
        "COMFAORIENTE",
        "COOSALUD EPS",
        "COOMEVA EPS",
        "COMPENSAR EPS",
        "DUSAKAWI EPS-I",
        "EMSSANAR E.S.S.",
        "EPS FAMILIAR DE COLOMBIA",
        "FAMISANAR",
        "JERSALUD",
        "MALLAMAS EPS-I",
        "MEDICOS ASOCIADOS",
        "MUTUAL SER",
        "NUEVA EPS",
        "PIJAOS SALUD EPS-I",
        "SALUD TOTAL EPS S.A.",
        "SANITAS EPS",
        "SAVIA SALUD EPS",
        "SURA EPS",
        "UNISALUD",
        "ECOPETROL",
        "FIDUPREVISORA",
        "FUERZAS MILITARES",
        "MAGISTERIO (DOCENTES)",
        "POLICÍA NACIONAL",
        "SANIDAD MILITAR",
        "SERVISALUD",
        "UNIVERSIDADES PÚBLICAS",
        "NO ASEGURADO"
    ];

    insertOptionsList(epsList, eapbId)

    const eapb = document.getElementById(eapbId);
    eapb.required = true;

    afiliacion.addEventListener('change', () => {
        const eapb = document.getElementById(eapbId)
        eapb.value = afiliacion.value == '135' ? "NO ASEGURADO" : '';

    })
}

function insertOptionsList(values = [], ...onInputsIds) {
    const inputs = onInputsIds.map(id => document.getElementById(id));

    inputs.forEach(input => {
        if (input) {
            const select = document.createElement("select");
            select.id = input.id;
            select.name = input.name;
            select.className = "form-control";
            select.style.width = "100%";

            values.forEach(value => {
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

function barrios(campolocalidad, campobarrio) {
    const localidad = document.getElementById(campolocalidad);

    localidad.addEventListener('change', () => {

        if (!localidad) {
            console.error("Elemento de localidad no encontrado");
            return;
        }

        try {

            import('https://www.trakio.pro/js/calidad/clases/barrios_Api.js?v=1.1.1')
                .then(module => {
                    const barrios = new module.default();
                    barrios.barriosBogota(localidad.value, campobarrio);
                })
                .catch(error => console.error("Error al cargar el módulo:", error));

        } catch (error) {

            import('https://www.trakio.pro/js/calidad/clases/barrios_Api.js?v=1.1.1')
                .then(module => {
                    const barrios = new module.default();
                    barrios.barriosBogota(localidad.value, campobarrio);
                })
                .catch(error => console.error("Error al cargar el módulo:", error));

        }


    })
}

function longitudTelefono(...camposTelefonoId) {
    const validarTelefono = (campoTelefonoId) => {
        const telefono = document.getElementById(campoTelefonoId);

        telefono.addEventListener('input', () => {
            telefono.style.backgroundColor = telefono.value.length === 10 ? COLOR_LLENO : COLOR_ERROR;
        });
    }

    if (Array.isArray(camposTelefonoId)) {
        camposTelefonoId.forEach(campoTelefonoId => {
            validarTelefono(campoTelefonoId);
        });
        return;
    }

    validarTelefono(camposTelefonoId);
}

function datosRequeridos(idCampos) {
    idCampos.forEach(idCampo => {
        document.getElementById(idCampo).required = true;
    });
}

function elegirBase() {
    switch (ID_BASE) {
        case '16':
            fichaNNA();
            break;

        case '15':
            fichaUtis();
            break;



        default:
            break;
    }
}

function initApp() {
    //styleBackground();
    styleTitulo();
    elegirBase();
    eventoColorearInputs();
}

initApp();

/**
 * FUNCION NNA
 */

function datosPredefinidos() {
    //Correo
    const correo = document.getElementById('valorControl16242');
    correo.value ||= "ENTORNOLABORAL@SUBREDNORTE.GOV.CO";

    const etnia = document.getElementById('valorControl16200');
    etnia.value ||= "84";

    const hablaespañol = document.getElementById('valorControl16204');
    hablaespañol.value ||= "958";

    const categoriadisc = document.getElementById('valorControl16203');
    categoriadisc.value ||= "3822";
}

function fichaNNA() {
    const camposRequeridos = [
        'valorControl16237',
    ];

    //CAMPOS REQUERIDOS
    //datosRequeridos(camposRequeridos);
    // LO QUE DICE LA FUNCION
    //datosPredefinidos();
    //BORRAR TIPOS DE DOCUMENTOS
    deshabilitarDocsNNA();
    // # DOCUMENTO NNA
    digitosDoc('valorControl16187', 'valorControl16188');
    // TIPO DOC VS NACIONALIDAD 
    tipoDocVsNacionalidad('valorControl16187', 'valorControl16193');
    // TIPO DOC VS PDI 
    tipoDocVsPdi('valorControl16187', 'valorControl16201');
    // SEXO VS GENERO
    sexoVsGenero('valorControl16194', 'valorControl16195');
    //FECHA DE NACIMIENTO
    validarFechaNacimiento();
    //EDAD
    calcularEdad();
    //NO ASEGURADO NIÑOS
    afiliacion("valorControl16207", "valorControl16208");
    //BARRIOS VIVIENDA
    barrios("valorControl16212", ["16214"]);
    // BARRIOS TRABAJO
    barrios("valorControl16265", ["16267"]);
    //LONGITUD TEL 1
    longitudTelefono("valorControl16239");
    //LONGITUD DOCUMENTO ACUDIENTE
    digitosDoc('valorControl16251', 'valorControl16252');
    //ESCONDER OPCIONES TIPO DOCUMENTO ACUDIENTE
    deshabilitarDocsNNAAcu();
    //TIPO DE DOCUMENTO ACUDIENTE VS NACIONALIDAD
    tipoDocAcuVsNacionalidad();
    //NO ASEGURADO ACUDIENTE 
    afiliacion("valorControl16257", "valorControl16258");
    //LONGITUD TELEFONO ACUDIENTE
    longitudTelefono("valorControl16260");
    //HORAS Y DIAS DE TRABAJO A LA SEMANA
    horasYdias();
    //ACTUALMENTE ESTUDIA VUELVE REQUERIDO COLEGIO Y FALTO AL COLEGIO
    infoEscolar();
    //ACTIVIDADES SEMANALES VUELVE REQUERIDO A CUALES REALIZA
    actividadesSemanales();
    //LESIONES RELACIONADAS CON EL TRABAJO
    lesion();
    //CONDICIONES DE SALUD TODAS EN NO
    condicionesDeSalud();
    //COLOCA LOS SELECTS EN N/A EN LA COLUMNA DE COMPROMISO DEL DECALOGO
    decalogoCompromiso();
}

function deshabilitarDocsNNA() {
    const docNoNNA = ["59", "62", "65", "1637", "1638"];
    disabledOptionsFromSelect(
        "valorControl16187",
        docNoNNA,
        true
    );
}

function tipoDocVsPdi(campoTipoDoc, campoPdi) {
    const tipoDoc = document.getElementById(campoTipoDoc);
    const Pdi = document.getElementById(campoPdi);
    const docsColombianos = ['60', '61', '59', '63'];
    tipoDoc.addEventListener('change', () => {

        Pdi.value = docsColombianos.includes(tipoDoc.value) ? '2620' : '2618';
    })
}

function sexoVsLactante(sexo) {
    const inputGestante = document.getElementById('valorControl16346');
    const inputLactante = document.getElementById('valorControl16347');

    inputGestante.value = inputLactante.value = sexo == '68' ? '959' : '';
    inputGestante.required = inputLactante.required = sexo == '68';

    inputGestante.disabled = inputLactante.disabled = sexo == '67';
}

function validarFechaNacimiento() {
    const fechaNac = document.getElementById('valorControl16197');
    fechaNac.addEventListener('change', () => {
        const fechaA_M_D = formatoYMD(fechaNac.value);
        const fechaMenorQueHoy = date1LessDate2(fechaA_M_D, new Date());
        fechaNac.style.backgroundColor = !fechaMenorQueHoy ? COLOR_ERROR : '';
    })
}

function calcularEdad() {
    const fechaNac = document.getElementById('valorControl16197');
    const intervencion = document.getElementById('FechaIntervencion');
    const edadInput = document.getElementById('valorControl16198');
    edadInput.disabled = true;

    fechaNac.addEventListener('change', () => {
        const fechaNacValue = new Date(fechaNac.value.split("/").reverse().join("/"));
        const fechaIntervencionValue = new Date(intervencion.value.split("/").reverse().join("/"));

        const diffAnios = (fechaIntervencionValue - fechaNacValue) / (1000 * 60 * 60 * 24);
        EDAD = parseFloat((diffAnios / 365.25).toFixed(2));
        edadInput.value = EDAD;
        edadVsTipoDoc();
        edadVsEstadoCivil();
    })
}

function edadVsTipoDoc() {
    const tipoDoc = document.getElementById('valorControl16187');
    const nacionalidad = document.getElementById('valorControl16193');

    if (nacionalidad.value == "50") {
        tipoDoc.value = EDAD > 7 && tipoDoc.value == "60" ? "" : "";
        tipoDoc.value = EDAD < 7 && tipoDoc.value != "60" ? "" : "";
    }
}

function edadVsEstadoCivil() {
    const tipoDoc = document.getElementById('valorControl16196');

    if (EDAD < 14) {
        tipoDoc.value = "78";
    }
    else if (EDAD > 14) {
        tipoDoc.value = "73";
    }
}

function deshabilitarDocsNNAAcu() {
    const docNoNNA = ["60", "61", "66", "63"];
    disabledOptionsFromSelect(
        "valorControl16251",
        docNoNNA,
        true
    );
}

function tipoDocAcuVsNacionalidad() {
    const tipoDocAcu = document.getElementById('valorControl16251');
    const nacionalidadAcu = document.getElementById('valorControl16253');
    tipoDocAcu.addEventListener('change', () => {
        nacionalidadAcu.value = tipoDocAcu.value == 59 ? 50 : '';
    });
}

function horasYdias() {
    const horas = document.getElementById('valorControl16302');
    const dias = document.getElementById('valorControl16303');

    horas.addEventListener('keyup', () => {
        if (horas.value > 24) {
            horas.style.backgroundColor = COLOR_ERROR;
        }
        else if (horas.value < 24) {
            horas.style.backgroundColor = COLOR_LLENO;
        }
    });

    dias.addEventListener('keyup', () => {
        if (dias.value > 7) {
            dias.style.backgroundColor = COLOR_ERROR;
        }
        else if (dias.value < 7) {
            dias.style.backgroundColor = COLOR_LLENO;
        }
    });
}

function infoEscolar() {
    const actualmenteEstudia = document.getElementById('valorControl16308');
    const colegio = document.getElementById('valorControl16310');
    const faltoColegio = document.getElementById('valorControl16315');

    actualmenteEstudia.addEventListener('change', () => {
        if (actualmenteEstudia.value == "958") {
            colegio.required = true;
            faltoColegio.required = true;
            colegio.style.backgroundColor = COLOR_REQUERIDO;
            faltoColegio.style.backgroundColor = COLOR_REQUERIDO;
        }
        if (actualmenteEstudia.value == "959") {
            colegio.required = false;
            faltoColegio.required = false;
            colegio.style.backgroundColor = COLOR_NEGRO;
            faltoColegio.style.backgroundColor = COLOR_NEGRO;
        }
    });
}

function actividadesSemanales() {

    const realizaActs = document.getElementById('valorControl16316');
    const Acts = document.getElementById('valorControl16317');

    realizaActs.addEventListener('change', () => {
        if (realizaActs.value == "958") {
            Acts.required = true;
            Acts.style.backgroundColor = COLOR_REQUERIDO;
        }
        if (realizaActs.value == "959") {
            Acts.required = false;
            Acts.style.backgroundColor = COLOR_NEGRO;
            Acts.value = "";
        }
    });
}
function lesion() {

    const lesion = document.getElementById('valorControl16318');
    const accidente = document.getElementById('valorControl16319');
    const consecuencia = document.getElementById('valorControl16320');

    lesion.addEventListener('change', () => {

        accidente.required = lesion.value == "958";
        consecuencia.required = lesion.value == "958";

    })
}

function condicionesDeSalud() {
    const contenedor = document.querySelector("#formularioNuevoFormato > div:nth-child(22)");
    const selectsContenedor = contenedor.querySelectorAll("select");

    for (let i = 3; i < 13; i++) {
        const selectValue = selectsContenedor[i].value;
        selectsContenedor[i].value = selectValue == "" ? "959" : selectValue;
    }
}

function decalogoCompromiso() {

    const contenedor = document.querySelector("#formularioNuevoFormato > div:nth-child(26)");
    const selectsContenedor = contenedor.querySelectorAll("select");
    const tipoIntervencion = document.getElementById('valorControl16186');

    tipoIntervencion.addEventListener('change', () => {
        const esSeguimientoAlEfecto = tipoIntervencion.value === '105';

        if (esSeguimientoAlEfecto) {
            selectsContenedor.forEach(select => {
                select.value = '960';
            })
        } else {
            selectsContenedor.forEach(select => {
                select.value = '';
            })
            for (let i = 0; i < 20; i += 2) {
                selectsContenedor[i].value = esSeguimientoAlEfecto ? "960" : "959";
            }
        }

    });
}

/**
 * FUNCION UTIS
 */

function fichaUtis() {

    // const titulo = document.querySelector("#formularioNuevoFormato > div:nth-child(11) > div > b").textContent;
    const form = document.getElementById('formularioNuevoFormato');
    const sectionId = form.querySelectorAll('input[name="Id_seccion"]')[0]?.value;
    switch (sectionId) {

        case '199':

            longitudesDireccion('valorControl11236', 'valorControl11241', 'valorControl11245');
            //TELEFONO 
            longitudTelefono('valorControl11256', 'valorControl11257');
            //ACTIVIDAD ECONOMICA 
            actividadEconomica();
            //DIGITOS DOC PAG 1
            digitosDoc('valorControl11264', 'valorControl11265')
            tipoDocVsNacionalidad('valorControl11264', 'valorControl11266');
            //DESHABILITAR DOCUMENTOS PAG 1
            deshabilitarDocsUtisPag1();
            //DETERMINAR SI ES UTI DE ALTO
            guardaTipoUti();
            //BARRIOS UTIS1
            barrios("valorControl11229", ["11231"]);
            complementosDireccion();
            break;

        case '200':
            sustanciasQuimicas();
            elementosDeProteccionTemporal();
            break;

        case '203':
            datosPredefinidosUtis3();
            digitosDoc('valorControl11451', 'valorControl11452');
            deshabilitarDocsUtisPag3();
            tipoDocVsNacionalidad('valorControl11451', 'valorControl11453');
            tiempoDeTrabajo();
            sexoVsGenero('valorControl11454', 'valorControl11455');
            tipoDocVsPdi('valorControl11451', 'valorControl11464');
            afiliacion('valorControl11471', 'valorControl11472');
            accidenteRelacionado();
            perimetroCintura();
            preniadaSinPerimetro();
            break;

        case '204':
            estilosDeVida();
            //medidas 1
            medidas('valorControl11547', 'valorControl11548', 'valorControl11549', 'valorControl11550', 'valorControl11551', 'valorControl11556');
            semaforizacion('valorControl11550', 'valorControl11564');

            //medidas 2
            medidas('valorControl11584', 'valorControl11585', 'valorControl11586', 'valorControl11587', 'valorControl11588', 'valorControl11593');
            semaforizacion('valorControl11587', 'valorControl11601');

            //medidas 3
            medidas('valorControl11605', 'valorControl11606', 'valorControl11607', 'valorControl11608', 'valorControl11609', 'valorControl11614');
            semaforizacion('valorControl11608', 'valorControl11622');

            //medidas 4
            medidas('valorControl11626', 'valorControl11627', 'valorControl11628', 'valorControl11629', 'valorControl11630', 'valorControl11635');
            semaforizacion('valorControl11629', 'valorControl11643');

            //medidas 5
            medidas('valorControl11647', 'valorControl11648', 'valorControl11649', 'valorControl11650', 'valorControl11651', 'valorControl11656');
            semaforizacion('valorControl11650', 'valorControl11664');

            planDeEmergencias();
            semaforizacionfinal('valorControl11578', 'valorControl11580', 'valorControl11581')
            nombredigitador('valorControl11583');
            break;

        default:
            break;
    }
}

function longitudesDireccion(ejePrincipal, ejeGenerador, placa) {
    const valoresControl = {
        [ejePrincipal]: 246,
        [ejeGenerador]: 246,
        [placa]: 99
    }
    const campos = document.querySelectorAll(`#${ejePrincipal},#${ejeGenerador},#${placa}`)
    campos.forEach(campo => {
        campo.addEventListener('change', () => {
            if (valoresControl[campo.id]) {
                campo.style.backgroundColor = campo.value > valoresControl[campo.id] ? COLOR_ERROR : COLOR_LLENO;
            }
        })
    });
}

function complementosDireccion() {
    const complemento1 = document.getElementById('valorControl11247');
    const complemento1Num = document.getElementById('valorControl11248');
    const complemento2 = document.getElementById('valorControl11252');
    const complemento2Num = document.getElementById('valorControl11253');

    complemento1.addEventListener('change', () => {
        complemento1Num.required = complemento1.value != "";
    })

    complemento2.addEventListener('change', () => {
        complemento2Num.required = complemento2.value != "";
    })
}

function actividadEconomica() {

    const actividad = document.getElementById('valorControl11258');
    const codigoActividad = document.getElementById('valorControl11259');

    actividad.addEventListener('change', () => {

        codigoActividad.value = actividad.value;
    });

}

function deshabilitarDocsUtisPag1() {
    const docNoUtis = ["60", "61", "66"];
    disabledOptionsFromSelect(
        "valorControl11264",
        docNoUtis,
        true
    );
}

function guardaTipoUti() {
    const tipoUti = document.getElementById('valorControl11226');

    tipoUti.addEventListener('change', () => {

        localStorage.setItem('tipoDeUti', tipoUti.value);
    });
}

function tiempoDeTrabajo() {
    const tiempo = document.getElementById('valorControl11474');

    const hola = [1, 2]

    tiempo.addEventListener('change', () => {
        const tiempovalue = tiempo.value;
        const primeraParte = Array.from(tiempovalue).slice(0, 2).join("");
        const segundaParte = Array.from(tiempovalue).slice(2, 4).join("");
        tiempo.value = [primeraParte, segundaParte].join("_");
    })
}

function sustanciasQuimicas() {

    const tipoUti = localStorage.getItem('tipoDeUti');

    if (tipoUti) {

        if (tipoUti != '1968') return;
        ['11396', '11397', '11398'].forEach(id => document.getElementById(`valorControl${id}`).required = true)
    }
}
function elementosDeProteccionTemporal() {

    const contenedor = document.querySelector("#formularioNuevoFormato > div:nth-child(16)")
    const selectsContenedor = contenedor.querySelectorAll("select");

    for (let i = 0; i < 26; i++) {
        selectsContenedor[i].value ||= i >= 18 ? "NO" : "NO APLICA";
    }
}

function datosPredefinidosUtis3() {
    const etnia = document.getElementById("valorControl11461");
    const hablaEspañol = document.getElementById("valorControl11468");
    const ocupacion = document.getElementById("valorControl11465");

    etnia.value = "84";
    hablaEspañol.value = "SI";
    ocupacion.value = "901";
}

function deshabilitarDocsUtisPag3() {
    const docNoUtis = ["60", "61", "66"];
    // disabledOptionsFromSelect(
    //     "valorControl11451",
    //     docNoUtis,
    //     true
    // );
}

function accidenteRelacionado() {
    const accidente = document.getElementById("valorControl11481");
    const elementosAccidente = document.querySelectorAll("#valorControl11483,#valorControl11484,#valorControl11487")

    accidente.addEventListener('change', () => {
        const tuvoAccidente = accidente.value == "SI";
        elementosAccidente.forEach(elementoAccidente => {
            elementoAccidente.required = tuvoAccidente;
            elementoAccidente.disabled = !tuvoAccidente;
        });
    })
}

function perimetroCintura() {
    const perimetro = document.getElementById("valorControl11512");
    perimetro.addEventListener('input', () => {
        if (perimetro.value < 65 || perimetro.value > 170) {
            perimetro.style.backgroundColor = COLOR_ERROR;
        }
        else {
            perimetro.style.backgroundColor = COLOR_LLENO;
        }
    })
}

function preniadaSinPerimetro() {
    const perimetro = document.getElementById("valorControl11512");
    const etapaGestacion = document.getElementById("valorControl11521");

    etapaGestacion.addEventListener('change', () => {
        const estaPreniada = etapaGestacion.value != "";
        perimetro.value = estaPreniada ? "" : perimetro.value;
        perimetro.required = !estaPreniada;
        perimetro.disabled = estaPreniada;
    })
}

function estilosDeVida() {
    const estilos1 = document.getElementById("valorControl11538");
    const estilos2 = document.getElementById("valorControl11540");
    const estilos3 = document.getElementById("valorControl11542");
    const fecha1 = document.getElementById("valorControl11539");
    const fecha2 = document.getElementById("valorControl11541");
    const fecha3 = document.getElementById("valorControl11543");
    fecha1.disabled = true;
    fecha2.disabled = true;
    fecha3.disabled = true;

    estilos1.addEventListener('change', () => {
        fecha1.required = estilos1.value != "";
        fecha1.disabled = estilos1.value == "";
    })

    estilos2.addEventListener('change', () => {

        fecha2.required = estilos2.value != "";
        fecha2.disabled = estilos2.value == "";
    })

    estilos3.addEventListener('change', () => {

        fecha3.required = estilos3.value != "";
        fecha3.disabled = estilos3.value == "";
    })
}

function medidas(campriesgos, campexposicion, campdaño, campsemaforizacion, campinformacion, campvaloracionInicial) {
    const riesgos = document.getElementById(campriesgos);
    const exposicion = document.getElementById(campexposicion);
    const daño = document.getElementById(campdaño);
    const semaforizacion = document.getElementById(campsemaforizacion);
    const informacion = document.getElementById(campinformacion);
    const valoracionInicial = document.getElementById(campvaloracionInicial);
    const fechaIntervencion = document.getElementById("FechaIntervencion");

    riesgos.addEventListener('change', () => {

        exposicion.required = riesgos.value != "" ? "true" : "";
        daño.required = riesgos.value != "" ? "true" : "";
        semaforizacion.required = riesgos.value != "" ? "true" : "";
        informacion.required = riesgos.value != "" ? "true" : "";
        informacion.value = riesgos.value != "" ? "1457" : "";
        valoracionInicial.required = riesgos.value != "" ? "true" : "";
        valoracionInicial.value = riesgos.value != "" ? fechaIntervencion.value : "";
    })
}

function semaforizacion(campsemaforizacion1, campsemaforizacion2) {
    const semaforizacion = document.getElementById(campsemaforizacion1);
    const semaforizacion2 = document.getElementById(campsemaforizacion2);

    semaforizacion.addEventListener('input', () => {
        if (semaforizacion.value.trim() === "") {
            semaforizacion.value = "";
        } else {
            semaforizacion.value = semaforizacion.value.replace(/%/g, '') + "%";
        }
    })

    semaforizacion2.addEventListener('input', () => {
        if (semaforizacion2.value.trim() === "") {
            semaforizacion2.value = "";
        } else {
            semaforizacion2.value = semaforizacion2.value.replace(/%/g, '') + "%";
        }
    })
}

function planDeEmergencias() {

    const contenedor = document.querySelector("#formularioNuevoFormato > div:nth-child(28)");
    const selectsContenedor = contenedor.querySelectorAll("select");

    for (let i = 0; i < 5; i++) {
        const selectValue = selectsContenedor[i].value;
        selectsContenedor[i].value = selectValue == "" ? "NO" : selectValue;
    }
}

function semaforizacionfinal(campsemaforizacion1, campsemaforizacion2, campsemaforizacion3) {
    const semaforizacion = document.getElementById(campsemaforizacion1);
    const semaforizacion2 = document.getElementById(campsemaforizacion2);
    const semaforizacion3 = document.getElementById(campsemaforizacion3);

    semaforizacion.addEventListener('input', () => {
        if (semaforizacion.value.trim() === "") {
            semaforizacion.value = "";
        } else {
            semaforizacion.value = semaforizacion.value.replace(/%/g, '') + "%";
        }
    })

    semaforizacion2.addEventListener('input', () => {
        if (semaforizacion2.value.trim() === "") {
            semaforizacion2.value = "";
        } else {
            semaforizacion2.value = semaforizacion2.value.replace(/%/g, '') + "%";
        }
    })

    semaforizacion3.addEventListener('input', () => {
        if (semaforizacion3.value.trim() === "") {
            semaforizacion3.value = "";
        } else {
            semaforizacion3.value = semaforizacion3.value.replace(/%/g, '') + "%";
        }
    })
}

function nombredigitador(campoDig) {

    const digList = [
        "",
        "ALBA SABOGAL",
        "ANGIE JIMENEZ",
        "CAMILA YOPASA",
        "CAROLINA GARAY",
        "CRISTHIAN PARRA",
        "DANIEL MANCHEGO",
        "FELIPE ARIAS",
        "GINA GOMEZ",
        "JENNY ROJAS",
        "LAURA VELASCO",
        "LUIS ESGUERRA",
        "MARCELA CAPERA",
        "MARILYN ARANGO",
        "MARTHA RIAÑO",
        "PAOLA GAMBOA",
        "MARIANA OCHOA",
        "DAVID ALVAREZ",
        "NICOLAS ABRIL",
        "JUAN CASTELLANOS",
        "JESISSON ANDREY",
        "SANTIAGO RODRIGUEZ",
        "JUAN GUTIERREZ",
        "ANDRES OROZCO",
        "MIGUEL GALINDO",
        "SANTIAGO ARANZAZU",
        "JUAN CASTELLANOS",
        "NICOLÁS ABRIL",
    ];

    insertOptionsListDig(digList, campoDig)
}

function insertOptionsListDig(values = [], ...onInputsIds) {
    const inputs = onInputsIds.map(id => document.getElementById(id));

    inputs.forEach(input => {
        if (input) {
            const select = document.createElement("select");
            select.id = input.id;
            select.name = input.name;
            select.className = "form-control";
            select.style.width = "100%";

            values.forEach(value => {
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



