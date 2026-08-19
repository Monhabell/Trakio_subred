// Cache-busting por versión manual: sube CALIDAD_JS_VERSION cada vez que publiques
// un cambio en los módulos de /js/calidad/ (incluye http/*.js) para que el navegador
// y la extensión Code Injector descarguen la versión nueva de una, en vez de servir
// una copia cacheada. El .htaccess deja el Cache-Control listo para que, entre
// versiones, el navegador siga aprovechando la caché normalmente.
const isLocalEnv = location.hostname === "127.0.0.1" || location.hostname === "localhost";
const CALIDAD_JS_VERSION = '2';

async function importModule(path, moduleName = 'Module') {
    const cacheBuster = `?v=${CALIDAD_JS_VERSION}`;
    const urls = isLocalEnv
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
const ANIO_ACTUAL = FECHA_ACTUAL.getFullYear();
const MES_ACTUAL = FECHA_ACTUAL.getMonth();

//DATOS FICHA
const FECHA_INTERVENCION = document.querySelector("#FechaIntervencion");
const CONSECUTIVO_FICHA = document.querySelector("#Consecutivo_fic");
const LOCALIDAD_FICHA = document.querySelector("#Localidad_fic");
const BASE_FICHA = document.querySelector("#Id_Base");

try {
    const { DateValidatorfer } = await importModule('Clases/DateValidator.js', 'DateValidator');
    const { sesiones } = await importModule('Educativo/sesiones2.js', 'sesiones');

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


async function cargarModulos() {
    const modulos = {
        PrevEmbarazo: 'Prev_embarazo.js',
        SaludMental: 'Salud_mental.js',
        Sesiones: 'Sesiones.js',
        TiendasEscolares: 'tiendasEscolares.js'
    };

    const instancias = {};

    // Cargamos los 4 módulos en paralelo (en vez de uno por uno) y respetando
    // el orden de URLs según el entorno (en producción nunca debe intentarse 127.0.0.1 primero).
    await Promise.all(Object.entries(modulos).map(async ([nombre, archivo]) => {
        const { [nombre]: instancia } = await importModule(`Educativo/${archivo}`, nombre);
        if (instancia) instancias[nombre] = instancia;
        else console.error(`Error crítico: No se pudo cargar ${nombre}`);
    }));

    return {
        instanciaEmbarazo: instancias.PrevEmbarazo,
        instanciaSaludMental: instancias.SaludMental,
        instanciaSesiones: instancias.Sesiones,
        instanciaTiendasEscolares: instancias.TiendasEscolares
    };
}

// Uso con manejo completo de errores
cargarModulos()
    .then(({ instanciaEmbarazo, instanciaSaludMental, instanciaSesiones, instanciaTiendasEscolares }) => {
        console.log('Todos los módulos cargados correctamente');
        // Aquí puedes usar las instancias:
        // instanciaEmbarazo.metodo();
        // instanciaSesiones.metodo();
    })
    .catch(error => {
        console.error('Error al cargar módulos:', error);
        // Mostrar mensaje al usuario o implementar plan B
    });
try {
    //TÍTULO CODE
    let heading = document.querySelector("#main_body > div > div > main > div > div > div > div.panel-heading");
    heading.removeAttribute("style");
    heading.style.backgroundColor = "#522200";
    heading.style.color = "#fff";

    let logo = document.querySelector("#sidebar > div > div.sidebar-brand.logo_blanco");
    logo.style.backgroundColor = "#00ff00ff";
    logo.innerHTML = "<h3>CODE EDUCATIVO</h3>";


    // //Colores validadores
    let bgError = "rgba(255, 50, 50, 0.4)";
    let bgSuccess = "rgba(50, 255, 50, 0.7)";

    // //Obtener el elemento de entrada de fecha
    const fechaInput = document.querySelector("#FechaIntervencion");

    // // Agregar el evento de escucha 'input' al campo de fecha
    fechaInput.addEventListener('change', validarFecha);



    function getMesActual() {
        let fecha = new Date();
        let dia = fecha.getDate();  // Día actual
        let mes = fecha.getMonth() + 1;  // Mes actual (getMonth devuelve de 0 a 11, por lo que sumamos 1)
        let diaHabil = getDiaHabil(mes);  // Obtenemos el 5º día hábil del mes

        if (dia > diaHabil) {
            return mes; // Si el día actual es mayor al 5º día hábil, devolvemos el mes actual
        } else {
            if (mes === 1) {
                return 12; // Si estamos en enero, devolvemos diciembre del año anterior
            } else {
                return mes - 1; // De lo contrario, devolvemos el mes anterior
            }
        }
    }

    function getDiaHabil(mes) {
        let fecha = new Date(new Date().getFullYear(), mes - 1, 1);  // Primer día del mes
        let diaHabil = 0;
        let contador = 0;

        // Recorremos cada día del mes hasta encontrar el 5º día hábil (evitando sábados y domingos)
        while (contador < 5) {
            if (fecha.getDay() !== 0 && fecha.getDay() !== 6) {  // Evitamos fines de semana
                contador++;  // Solo contamos los días hábiles
            }
            if (contador < 5) {
                fecha.setDate(fecha.getDate() + 1);  // Avanzamos un día
            }
        }
        return fecha.getDate();  // Devolvemos el día correspondiente al 5º día hábil
    }


    // // Función para validar la fecha ingresada
    function validarFecha() {
        var verificar = document.querySelector("#FechaIntervencion").value;
        var fechaComponentes1 = verificar.split("/");
        var mes1 = fechaComponentes1[1];
        var año1 = fechaComponentes1[2];

        var mes_deseado = getMesActual()

        // Verifica si el mes seleccionado no es el mes deseado (por ejemplo, febrero = 2)
        if (mes1 != mes_deseado || año1 != 2025) {
            // Restablece el valor del campo de fecha a una fecha válida dentro del mes deseado
            alert('La fecha no corresponde');
            document.querySelector("#FechaIntervencion").value = "";
        }
    }

    //Validar consecutivo
    let consecutivo = document.querySelector("#Consecutivo_fic");
    consecutivo.addEventListener('keyup', function () {
        this.value.length != 5 ?
            (toggleBtnActualizacion(0), this.style.backgroundColor = bgError) :
            (toggleBtnActualizacion(1), this.style.backgroundColor = bgSuccess);
    })

    function toggleBtnActualizacion(estado) {
        let btnActualizar = document.querySelector("#botonActualizarInformacion")
        estado == 1 ? btnActualizar.disabled = false : false
        estado == 0 ? btnActualizar.disabled = true : false
    }

} catch (error) { }

try { //id ficha visible

    for (let i = 1; i < 10; i++) {
        let idFichaVisible = document.querySelector(`#main_body > div > div > main > div > div > div > div.panel-body > table > tbody > tr:nth-child(${i}) > td:nth-child(1) > form > input[type=hidden]:nth-child(4)`);
        idFichaVisible.type = "";
        idFichaVisible.style.borderRadius = "20px";
    }

} catch (error3) { }

try {
    let bgError = "rgba(255, 46, 0, 0.8)";
    let letraBlanca = "#fff";
    let letraNegra = "#000";
    let bgSuccess = "rgba(50, 255, 50, 0.4)";
    let bgNone = "#fff";

    function formatoYMD(fecha) {
        let fechaArray = fecha.split("/");
        let dia = fechaArray[0];
        let mes = fechaArray[1];
        let anio = fechaArray[2];

        return anio + "-" + mes + "-" + dia;
    }

    function formatoCoordenadas(coordenadaX, coordenadaY) {
        coordenadaX.pattern = "-[4-7]{2}.[0-9]{7,8,9}";
        coordenadaY.pattern = "[4-4]{1}.[0-9]{7,8,9}";
    }

    function calcularEdad(fechaMayor, fechaMenor) {
        let fecha1 = new Date(fechaMenor);
        let fecha2 = new Date(fechaMayor);

        // Calcular la diferencia en milisegundos entre las fechas
        let diferenciaMs = fecha2 - fecha1;

        return diferenciaMs / (1000 * 60 * 60 * 24 * 365.25);
    }

    function establecerFormatoDMY(fecha) {
        fecha.pattern = "[0-9]{2}/[0-9]{2}/[0-9]{4}";
    }

    function disabledBtnUpd(btn, accion) {
        if (accion == 1) {
            btn.disabled = false;
        } else if (accion == 0) { btn.disabled = true; }
    }
    ////
    function minLongCampos(campo, longitudMin) {
        campo.value.length < longitudMin ? campo.style.backgroundColor = "rgba(196, 35, 0, 0.5)" : campo.style.backgroundColor = '#fff';
    }

    /*-------------------------------------------------------
    ---------------------AUTOCUIDADO-------------------------
    -------------------------------------------------------*/
    try {


        let primerNombre_AC = document.querySelector("#valorControl10348");
        let segundoNombre_AC = document.querySelector("#valorControl10349");
        let primerApellido_AC = document.querySelector("#valorControl10350");
        let segundoApellido_AC = document.querySelector("#valorControl10351");

        primerNombre_AC.pattern = "[A-Za-zñÑ\\s]+";
        segundoNombre_AC.pattern = "[A-Za-zñÑ\\s]+";
        primerApellido_AC.pattern = "[A-Za-zñÑ\\s]+";
        segundoApellido_AC.pattern = "[A-Za-zñÑ\\s]+";



        primerNombre_AC.required = true;
        primerApellido_AC.required = true;

        primerNombre_AC.addEventListener('keyup', function () {
            this.value.length < 3 ?
                (this.style.backgroundColor = bgError, toggleBtnActualizacion(0)) :
                (this.style.backgroundColor = bgSuccess, toggleBtnActualizacion(1));
        })

        segundoNombre_AC.addEventListener('keyup', function () {
            this.value.length < 3 ?
                (this.style.backgroundColor = bgError, toggleBtnActualizacion(1)) :
                (this.style.backgroundColor = bgSuccess, toggleBtnActualizacion(1));
        })

        primerApellido_AC.addEventListener('keyup', function () {
            this.value.length < 3 ?
                (this.style.backgroundColor = bgError, toggleBtnActualizacion(0)) :
                (this.style.backgroundColor = bgSuccess, toggleBtnActualizacion(1));
        })

        segundoApellido_AC.addEventListener('keyup', function () {
            this.value.length < 3 ?
                (this.style.backgroundColor = bgError, toggleBtnActualizacion(1)) :
                (this.style.backgroundColor = bgSuccess, toggleBtnActualizacion(1));
        })



    } catch (error) {


    }
    // auto cuidado pagina 2

    try {

        document.getElementById('valorControl10370').addEventListener('change', function () {
            var fechaIntervencion = document.getElementById('FechaIntervencion')
            var fechaValoracion = document.getElementById('valorControl10370').value

            if (fechaIntervencion.value != fechaValoracion) {
                var formulario = document.querySelector("#formularioNuevoFormato");
                var camposDeEntrada = formulario.querySelectorAll('select');
                camposDeEntrada.forEach(function (campo) {
                    campo.disabled = true;
                });
                alert("la fecha de valoracion inical debe ser igual a la fecha de intervencion")
                document.getElementById('valorControl10370').innerHTML = "";
                document.getElementById('valorControl10370').style.backgroundColor = '#FF0000';
            } else {
                var formulario = document.querySelector("#formularioNuevoFormato");
                var camposDeEntrada = formulario.querySelectorAll('select');
                camposDeEntrada.forEach(function (campo) {
                    campo.required = true;
                    campo.disabled = false;
                });
                document.getElementById('valorControl10370').style.backgroundColor = '#74FF33';
            }
        });

        document.getElementById('valorControl10355').multiple = false
        document.getElementById('valorControl10354').multiple = false


    } catch (error) {

    }

    try {
        //Fecha valoración inicial
        let fechaValoracionInicial = document.querySelector("#valorControl10370");
        fechaValoracionInicial.pattern = "[0-9]{2}/[0-9]{2}/[0-9]{4}";

        //Preguntas valoración inicial obligatorias
        fechaValoracionInicial.addEventListener('change', function () {
            let inputsVI = document.querySelectorAll("select");
            if (this.value != '' || this.value != null) {
                for (let i = 0; i < inputsVI.length; i++) {
                    inputsVI[i].required = true;
                }
            } else {
                for (let i = 0; i < inputsVI.length; i++) {
                    inputsVI[i].required = false;
                }
            }
        });

    } catch (error) { }

    try {
        //Fecha valoración final
        let fechaValoracionFinal = document.querySelector("#valorControl10374");
        fechaValoracionFinal.pattern = "[0-9]{2}/[0-9]{2}/[0-9]{4}";

        //Preguntas valoración final obligatorias
        fechaValoracionFinal.addEventListener('change', function () {
            let inputsVF = document.querySelectorAll("select");
            if (this.value != '' || this.value != null) {
                for (let i = 0; i < inputsVF.length; i++) {
                    inputsVF[i].required = true;
                }
            } else {
                for (let i = 0; i < inputsVF.length; i++) {
                    inputsVF[i].required = false;
                }
            }
        });
    } catch (error) { }

    /*----------------------------------------------------------
    ----------------------CONTEO JORNADAS-----------------------
    -----------------------------------------------------------*/
    try {
        let ejPrincipal_num = document.querySelector("#valorControl7417");
        ejPrincipal_num.setAttribute("type", "number");

        let manzana_cuidado_cj = document.querySelector("#valorControl10395");
        manzana_cuidado_cj.required = true;

        let telefono_cj = document.querySelector("#valorControl7436");
        telefono_cj.addEventListener('keyup', function () {
            if (this.value.length != 7 && this.value.length != 10) {
                this.style.backgroundColor = bgError;
                toggleBtnActualizacion(0);
            } else {
                this.style.backgroundColor = bgSuccess;
                toggleBtnActualizacion(1);
            }
        })

        let num_manzana_cj = document.querySelector("#valorControl10397");
        num_manzana_cj.disabled = true;

        manzana_cuidado_cj.addEventListener('change', function () {
            if (manzana_cuidado_cj.value == 'SI') {
                num_manzana_cj.required = true;
                num_manzana_cj.disabled = false;
            } else {
                num_manzana_cj.required = false;
                num_manzana_cj.value = '';
                num_manzana_cj.disabled = true;
            }
        })

    } catch (error) { }

    try {
        //Fecha valoración inicial
        let fechaSesion = document.querySelector("#valorControl7438");
        fechaSesion.pattern = "[0-9]{2}/[0-9]{2}/[0-9]{4}";


        fechaSesion.addEventListener('change', function () {
            var fechaIntervencionStr = document.getElementById("FechaIntervencion").value;
            var fechaIntervencion = new Date(fechaIntervencionStr.split("/").reverse().join("-"));
            var fechaSesion1 = document.querySelector("#valorControl7438").value;
            var fechaSesionDate = new Date(fechaSesion1.split("/").reverse().join("-"));
            if (fechaSesionDate < fechaIntervencion) {
                alert("La fecha de sesión es menor a la fecha de intervención");
                fechaSesion.style.backgroundColor = '#FF0000'
            } else {
                fechaSesion.style.backgroundColor = '#74FF33'
            }

        })

        var fechaIntervencionStr = document.getElementById("FechaIntervencion").value;
        var fechaIntervencion = new Date(fechaIntervencionStr.split("/").reverse().join("-"));
        var fechaSesion1 = document.querySelector("#valorControl7438").value;
        var fechaSesionDate = new Date(fechaSesion1.split("/").reverse().join("-"));
        if (fechaSesionDate < fechaIntervencion) {
            alert("La fecha de sesión es menor a la fecha de intervención");
            fechaSesion.style.backgroundColor = '#FF0000'
        } else {
            fechaSesion.style.backgroundColor = '#74FF33'
        }

        let nombreDig = document.querySelector("#valorControl8104");
        nombreDig.required = true;
    } catch (error) {

    }

    /*-------------------HIGIENE DE MANOS----------------------*/
    try {
        //Nombre del docente obligatorio para colegios
        let docente = document.querySelector("#valorControl1265");
        let tipoPoblacion = document.querySelector("#valorControl1262");
        tipoPoblacion.addEventListener('change', function () {
            this.value === "1634" ?
                (docente.required = true) :
                (docente.required = false);
        })

        let fechaEvaluacion = document.querySelector("#valorControl4028");
        establecerFormatoDMY(fechaEvaluacion);

        //Manzana del cuidado
        let manzana_priorizada = document.querySelector("#valorControl10409");
        let num_mazana = document.querySelector("#valorControl10410");
        num_mazana.disabled = true;
        manzana_priorizada.addEventListener('change', function () {
            this.value === '958' ?
                (num_mazana.required = true, num_mazana.disabled = false) :
                (num_mazana.required = false, num_mazana.disabled = true, num_mazana.value = '')
        })

        // Coordenadas
        let zona_hm = document.querySelector("#valorControl1268");
        let coorX_hm = document.querySelector("#valorControl4034");
        let coorY_hm = document.querySelector("#valorControl4035");
        coorX_hm.pattern = "-[4-7]{2}.[0-9]{6}";
        coorY_hm.pattern = "[4]{1}.[0-9]{6}";

        zona_hm.addEventListener('change', function () {
            this.value === '58' ?
                (coorX_hm.required = true, coorY_hm.required = true) :
                (coorX_hm.required = false, coorY_hm.required = false)
        })

        //Longitud telefono
        let tel1_hm = document.querySelector("#valorControl1294");
        tel1_hm.addEventListener('keyup', function () {
            this.value.length != 7 && this.value.length != 10 ?
                (this.style.backgroundColor = bgError, toggleBtnActualizacion(0)) :
                (this.style.backgroundColor = bgSuccess, toggleBtnActualizacion(1))
        })

        let tel2_hm = document.querySelector("#valorControl1289");
        tel2_hm.addEventListener('keyup', function () {
            this.value.length != 7 && this.value.length != 10 ?
                (this.style.backgroundColor = bgError, toggleBtnActualizacion(0)) :
                (this.style.backgroundColor = bgSuccess, toggleBtnActualizacion(1))
        })

        //Tipo de documento y nacionalidad
        let documentoHM = document.querySelector("#valorControl1300");
        let nacionalidadHM = document.querySelector("#valorControl1302");

        documentoHM.addEventListener('change', function () {

            this.value == '59' ?
                (nacionalidadHM.value = '50', nacionalidadHM.style.backgroundColor = bgSuccess) :
                nacionalidadHM.style.backgroundColor = '#fff';
        })






        //let nacimiento_hm = document.querySelector("#valorControl1304");
        //nacimiento_hm.pattern = "[0-9]{2}/[0-9]{2}/[0-9]{4}";
    } catch (error) { }

    try {
        document.getElementById('valorControl10411').required = true
    } catch (error) {

    }

    try {
        var inputvalueparticipantes = document.getElementById("valorControl7441");
        inputvalueparticipantes.type = "number";
        inputvalueparticipantes.min = 20;

        var nombreApellido = document.getElementById('valorControl7443');
        nombreApellido.autocomplete = "off";
        nombreApellido.addEventListener('change', function () {
            var value = nombreApellido.value;
            partesNombre = value.split(' ');
            if (partesNombre.length >= 2) {

                var longitudMinima = 3; // Puedes ajustar este valor según tus necesidades
                var nombreValido = partesNombre[0].length >= longitudMinima;
                var apellidoValido = partesNombre[1].length >= longitudMinima;

                if (nombreValido && apellidoValido) {
                    nombreApellido.style.backgroundColor = '#33FF3C';
                    nombreApellido.style.color = '#000';
                    document.getElementById("botonActualizarInformacion").disabled = false;

                } else {
                    alert('Por favor, ingrese el nombre y el apellido completos.');
                    nombreApellido.style.backgroundColor = '#520A00';
                    nombreApellido.style.color = '#fff';
                    document.getElementById("botonActualizarInformacion").disabled = true;

                }

            }

        })

        var value = nombreApellido.value;
        partesNombre = value.split(' ');
        if (partesNombre.length >= 2) {
            var longitudMinima = 3; // Puedes ajustar este valor según tus necesidades
            var nombreValido = partesNombre[0].length >= longitudMinima;
            var apellidoValido = partesNombre[1].length >= longitudMinima;

            if (nombreValido && apellidoValido) {
                nombreApellido.style.backgroundColor = '#33FF3C';
                nombreApellido.style.color = '#000';
                document.getElementById("botonActualizarInformacion").disabled = false;

            } else {
                alert('Por favor, ingrese el nombre y el apellido completos.');
                nombreApellido.style.backgroundColor = '#520A00';
                nombreApellido.style.color = '#fff';
                document.getElementById("botonActualizarInformacion").disabled = true;

            }

        }


        function validarCampo(campoId) {

            var campo = document.getElementById(campoId);
            campo.autocomplete = "off"

            campo.addEventListener('change', function () {
                validarTodosLosCampos();
            });

            function validarCampoIndividual(valor) {
                // Verificar si el campo no está vacío
                if (valor.trim() === '') {
                    campo.style.backgroundColor = '';
                    campo.style.color = '';
                    return true;
                } else {
                    var partesCampo = valor.split(' ');
                    if (partesCampo.length >= 2) {
                        var longitudMinima = 3;
                        var nombreValido = partesCampo[0].length >= longitudMinima;
                        var apellidoValido = partesCampo[1].length >= longitudMinima;
                        return nombreValido && apellidoValido;
                    }
                    return false;
                }


            }

            return {
                validar: function () {
                    var value = campo.value;
                    if (!validarCampoIndividual(value)) {
                        campo.style.backgroundColor = '#520A00';
                        campo.style.color = '#fff';
                        return false;
                    } else {
                        campo.style.backgroundColor = '#33FF3C';
                        campo.style.color = '#000';
                        return true;
                    }
                }
            };
        }

        function validarTodosLosCampos() {
            var camposValidos = 0;

            // Itera sobre los IDs de los campos
            ['valorControl7450', 'valorControl7444', 'valorControl7446', 'valorControl7445', 'valorControl7447', 'valorControl7448', 'valorControl7449', 'valorControl8101', 'valorControl8102', 'valorControl8103'].forEach(function (campoId) {
                var campoObj = validarCampo(campoId);
                if (campoObj.validar()) {
                    camposValidos++;
                }
            });

            // Desactiva o activa el botón según la cantidad de campos válidos
            document.getElementById("botonActualizarInformacion").disabled = camposValidos !== 10;
        }

        // Llama a la función para cada campo que necesitas validar
        ['valorControl7450', 'valorControl7444', 'valorControl7446', 'valorControl7445', 'valorControl7447', 'valorControl7448', 'valorControl7449', 'valorControl8101', 'valorControl8102', 'valorControl8103'].forEach(function (campoId) {
            validarCampo(campoId);
        });

        // Llama a la función inicial para establecer el estado inicial del botón
        validarTodosLosCampos();


    } catch (error) {

    }

    /*------------------------------------HIGIENE BUCAL-------------------------------------*/
    try {
        let nacimiento_HB = document.querySelector("#valorPoblacionalFechaNacimiento");
        nacimiento_HB.pattern = "[0-9]{4}-[0-9]{2}-[0-9]{2}"; //Patrón fecha nacimiento
        let tipoDoc_HB = document.querySelector("#valorPoblacionalTipoDocumento");
        let PDI_HB = document.querySelector("#valorPoblacionalPoblacionDiferencialInclusion"); //Población diferencial
        let nacionalidad_HB = document.querySelector("#valorPoblacionalNacionalidad"); //Nacionalidad
        nacionalidad_HB.required = true;
        PDI_HB.required = true;


        //Nacionalidad vs documento
        nacionalidad_HB.addEventListener('change', function () {
            if (this.value != '50') {
                PDI_HB.value = '2618';
                PDI_HB.style.backgroundColor = bgSuccess;
                tipoDoc_HB.value == '59' || tipoDoc_HB.value == '60' || tipoDoc_HB.value == '61' ?
                    (tipoDoc_HB.style.backgroundColor = bgError, tipoDoc_HB.value = '', toggleBtnActualizacion(0)) :
                    (tipoDoc_HB.style.backgroundColor = bgSuccess, toggleBtnActualizacion(1))
            } else if (this.value == '50') {
                PDI_HB.value = '2620';
                PDI_HB.style.backgroundColor = bgSuccess;
                tipoDoc_HB.value == '59' || tipoDoc_HB.value == '60' || tipoDoc_HB.value == '61' ?
                    (tipoDoc_HB.style.backgroundColor = bgSuccess, toggleBtnActualizacion(1)) :
                    (tipoDoc_HB.style.backgroundColor = bgError, toggleBtnActualizacion(0))
            }
        })

        //Sexo vs género
        let sexo_HB = document.querySelector("#valorPoblacionalSexo");
        let genero_HB = document.querySelector("#valorPoblacionalGenero");
        sexo_HB.required = true;
        genero_HB.required = true;

        sexo_HB.addEventListener('change', function () {
            switch (this.value) {
                case '67':
                    genero_HB.value = '70'
                    genero_HB.style.backgroundColor = bgSuccess
                    break;

                case '68':
                    genero_HB.value = '71'
                    genero_HB.style.backgroundColor = bgSuccess
                    break;
            }
        })

        genero_HB.addEventListener('change', function () {
            switch (this.value) {
                case '70':
                    sexo_HB.value = '67'
                    sexo_HB.style.backgroundColor = bgSuccess
                    break;

                case '71':
                    sexo_HB.value = '68'
                    sexo_HB.style.backgroundColor = bgSuccess
                    break;
            }
        })

        //Etnia vs pueblo
        let etnia_HB = document.querySelector("#valorPoblacionalEtnia");
        let pueblo_HB = document.querySelector("#valorPoblacionalPueblo");
        let espaniol = document.querySelector("#valorPoblacionalHablaEspanol");
        etnia_HB.required = true;

        etnia_HB.addEventListener('change', function () {
            this.value != '84' ?
                (pueblo_HB.required = true, espaniol.required = true, pueblo_HB.disabled = false) :
                (pueblo_HB.required = false, pueblo_HB.disabled = true, espaniol.required = false)
        })

        //Afiliación vs nombre EAPB
        let afiliacion_HB = document.querySelector("#valorPoblacionalAfiliacionSGSSS");
        let EAP_HB = document.querySelector("#valorPoblacionalNombreEAPB");
        afiliacion_HB.addEventListener('change', function () {
            if (this.value != '135' && this.value != '133') {
                EAP_HB.required = true;
                EAP_HB.disabled = false;
            } else {
                EAP_HB.disabled = true;
                EAP_HB.value = '';
                EAP_HB.required = false;
            }
        })

        //LONGITUD NOMBRES
        let primerNombre_HB = document.querySelector("#valorPoblacionalPrimerNombre");
        let segundoNombre_HB = document.querySelector("#valorPoblacionalSegundoNombre");
        let primerApellido_HB = document.querySelector("#valorPoblacionalPrimerApellido");
        let segundoApellido_HB = document.querySelector("#valorPoblacionalSegundoApellido");

        primerNombre_HB.required = true;
        primerApellido_HB.required = true;

        primerNombre_HB.addEventListener('keyup', function () {
            this.value.length < 3 ?
                (this.style.backgroundColor = bgError, toggleBtnActualizacion(0)) :
                (this.style.backgroundColor = bgSuccess, toggleBtnActualizacion(1));
        })

        segundoNombre_HB.addEventListener('keyup', function () {
            this.value.length < 3 ?
                (this.style.backgroundColor = bgError, toggleBtnActualizacion(1)) :
                (this.style.backgroundColor = bgSuccess, toggleBtnActualizacion(1));
        })

        primerApellido_HB.addEventListener('keyup', function () {
            this.value.length < 3 ?
                (this.style.backgroundColor = bgError, toggleBtnActualizacion(0)) :
                (this.style.backgroundColor = bgSuccess, toggleBtnActualizacion(1));
        })

        segundoApellido_HB.addEventListener('keyup', function () {
            this.value.length < 3 ?
                (this.style.backgroundColor = bgError, toggleBtnActualizacion(1)) :
                (this.style.backgroundColor = bgSuccess, toggleBtnActualizacion(1));
        })

        let priorizado = document.querySelector("#valorControl8048"); //Campo Priorizado para acompañamiento familiar
        priorizado.required = true;

        //Edad vs Documento
        let edad_hb = document.querySelector("#valorPoblacionalEdad");

        nacimiento_HB.addEventListener('change', function () {

            if (edad_hb.value < 7 && nacionalidad_HB.value === '50') { //Menor de 7 años colombiano con RC
                tipoDoc_HB.value = '60';
                tipoDoc_HB.style.backgroundColor = bgSuccess;
            } else if (edad_hb.value >= 18 && nacionalidad_HB.value === '50') { //Mayor de edad colombiano con CC
                tipoDoc_HB.value = '59';
                tipoDoc_HB.style.backgroundColor = bgSuccess;
            } else if (edad_hb.value > 7 && edad_hb.value < 18 && nacionalidad_HB.value === '50') { //Rango edad para TI
                tipoDoc_HB.value = '61';
                tipoDoc_HB.style.backgroundColor = bgSuccess;
            }
        })

        //Tipo documento VS nacionalidad
        tipoDoc_HB.addEventListener('change', function () {
            if (this.value == '60' || this.value == '61' || this.value == '59') {
                nacionalidad_HB.value = '50';
                nacionalidad_HB.style.backgroundColor = bgSuccess
                this.style.backgroundColor = bgSuccess
            }
        })

        //Longitud documento VS tipo documento
        let numDoc_hb = document.querySelector("#valorPoblacionalDocumento");
        //numDoc_hb.setAttribute("type", "number");

        numDoc_hb.addEventListener('keyup', function () {
            if (tipoDoc_HB.value == '60' || tipoDoc_HB.value == '61') {
                numDoc_hb.setAttribute('maxlenght', "10");
            }
        })

    } catch (error) { }

    /*-------------------SESIONES COLECTIVAS--------------------*/
    try {


        //Datos institucion
        var nombreInstitucion = document.getElementById('valorControl9');
        let jornada = document.querySelector("#valorControl11");
        let tipoInstitucion = document.querySelector("#valorControl38");

        nombreInstitucion.required = true;
        jornada.required = true;
        tipoInstitucion.required = true;
        jornada.addEventListener("change", function () {
            jornada.value == "350" && tipoInstitucion.value == "1634" ?
                ((tipoInstitucion.style.backgroundColor = "rgba(255, 50, 50, 0.4)"),
                    tipoInstitucion.focus(),
                    tipoInstitucion.clear()) :
                (tipoInstitucion.style.backgroundColor = "#fff");
        });

        let tipo_inst_publ = document.querySelector("#valorControl3658");
        let tipo_inst_priv = document.querySelector("#valorControl3659");

        validarCheckTipoINst();

        function validarCheckTipoINst() {
            if (tipo_inst_publ.checked) {
                tipo_inst_priv.checked = false
                tipo_inst_priv.disabled = true

            } else if (tipo_inst_priv.checked) {
                tipo_inst_priv.checked = true
                tipo_inst_publ.checked = false;
                tipo_inst_publ.disabled = true

            } else {
                tipo_inst_publ.disabled = false
                tipo_inst_priv.disabled = false
                tipo_inst_priv.required = true;
                tipo_inst_publ.required = true;
            }
        }

        tipo_inst_priv.addEventListener('change', validarCheckTipoINst)
        tipo_inst_publ.addEventListener('change', validarCheckTipoINst)

        //Curso
        let grado = document.querySelector("#valorControl39");
        grado.required = true;

        //Coordenadas y dirección
        let coordenadaX = document.querySelector("#valorControl34");
        coordenadaX.pattern = "-[4-7]{2}.[0-9]{7,8,9}";

        let coordenadaY = document.querySelector("#valorControl35");
        coordenadaY.pattern = "[4-4]{1}.[0-9]{7,8,9}";

        let eje_principalSC = document.querySelector("#valorControl17");
        let upz_SC = document.querySelector("#valorControl15");
        upz_SC.required = true;

        let zonaSC = document.querySelector("#valorControl12");
        zonaSC.addEventListener("change", function () {
            switch (this.value) {
                case '57':
                    coordenadaY.required = false;
                    coordenadaX.required = false;
                    eje_principalSC.required = true;
                    break;

                case '58':
                    coordenadaY.required = true;
                    coordenadaX.required = true;
                    eje_principalSC.required = false;
                    break;
            }
        });

        //Telefono
        let telefono_SC = document.querySelector("#valorControl36");
        telefono_SC.required = true;

        telefono_SC.addEventListener("blur", function () {
            // Expresión regular para validar números de teléfono (fijo o celular)
            let patron = /^(?:\d{7}|\d{10})$/;
            let btnUpdSM = document.querySelector("#botonActualizarInformacion");
            // Obtener el valor del campo de teléfono
            let numeroTelefono = telefono_SC.value.trim();

            // Verificar si el número coincide con el patrón
            if (patron.test(numeroTelefono)) {
                telefono_SC.style.backgroundColor = '#33FF3C'
                disabledBtnUpd(btnUpdSM, 1)
            } else {
                alert("Número de teléfono no válido. Por favor, ingrese un número de 7 u 10 dígitos.")
                telefono_SC.style.backgroundColor = bgError
                disabledBtnUpd(btnUpdSM, 0)
            }
        });


        //DIRECCION NUMERO

        var numerodireccion1 = document.querySelector("#valorControl18");
        numerodireccion1.type = 'number';
        numerodireccion1.max = '250';

        var numerodireccion2 = document.querySelector("#valorControl23");
        numerodireccion2.type = 'number';
        numerodireccion2.max = '250';

        var numerodireccion3 = document.querySelector("#valorControl28");
        numerodireccion3.type = 'number';
        numerodireccion3.max = '250';
    } catch (error) { }

    try {
        //Sesiones y procesos
        let fechaSesion = document.querySelector("#valorControl44");
        fechaSesion.pattern = "[0-9]{2}/[0-9]{2}/[0-9]{4]";

        let proceso = document.querySelector("#valorControl41");
        proceso.required = true;
        dicument.getElementById('valorControl6059').disabled = true
    } catch (error) { }

    try {
        //Fecha de nacimiento
        let fechaNacimiento = document.querySelector("#valorControl56");
        fechaNacimiento.pattern = "[0-9]{2}/[0-9]{2}/[0-9]{4}";
        let fIntervencion = document.querySelector("#FechaIntervencion");
        let estadoCivil = document.querySelector("#valorControl55");
        let tipoDocumento = document.querySelector("#valorControl51");
        let nacionalidad = document.querySelector("#valorControl58");

        fechaNacimiento.addEventListener("change", function () {
            let edad = calcularEdad(
                formatoYMD(fIntervencion.value),
                formatoYMD(fechaNacimiento.value)
            );

            //Validar estado civil
            switch (true) {
                case edad < 14:
                    estadoCivil.value = "78";
                    estadoCivil.style.backgroundColor = bgSuccess
                    break;

                case edad >= 14:
                    estadoCivil.value == "78" ?
                        (estadoCivil.value = '73', estadoCivil.style.backgroundColor = bgSuccess) :
                        estadoCivil.style.backgroundColor = bgNone;
                    break;

                default:
                    break;
            }

            edad < 7 && nacionalidad.value == "50" ?
                (tipoDocumento.value = "60", tipoDocumento.style.backgroundColor = bgSuccess) :
                (tipoDocumento.style.backgroundColor = bgNone);

            edad > 7 && edad < 18 && nacionalidad.value == '50' ?
                (tipoDocumento.value = '61', tipoDocumento.style.backgroundColor = bgSuccess) :
                (tipoDocumento.style.backgroundColor = bgNone)

            edad >= 18 && nacionalidad.value == "50" ?
                ((tipoDocumento.value = "59"),
                    (tipoDocumento.style.backgroundColor = bgSuccess)) :
                (tipoDocumento.style.backgroundColor = bgNone);

            if (nacionalidad.value != '50') {
                switch (tipoDocumento.value) {
                    case '59':
                    case '60':
                    case '61':
                        tipoDocumento.value == '';
                        tipoDocumento.style.backgroundColor = bgError
                        toggleBtnActualizacion(0)
                        break;

                    default:
                        tipoDocumento.style.backgroundColor = bgNone
                        toggleBtnActualizacion(1)
                        break;
                }
            }
        });

        //Nacionalidad y tipo de documento
        tipoDocumento.addEventListener("change", function () {
            switch (this.value) {
                case '59':
                case '60':
                case '61':
                    nacionalidad.value = "50"
                    nacionalidad.style.backgroundColor = bgSuccess
                    this.style.backgroundColor = bgSuccess
                    toggleBtnActualizacion(1)
                    break;

                case '62':
                case '64':
                case '1637':
                case '1638':
                case '1639':
                case '1640':
                case '2482':
                    if (nacionalidad.value == '50') {
                        nacionalidad.style.backgroundColor = bgError;
                        nacionalidad.value = '';
                        toggleBtnActualizacion(0)
                    } else if (nacionalidad.value != '50') {
                        toggleBtnActualizacion(1)
                        nacionalidad.style.backgroundColor = bgSuccess;
                        this.style.backgroundColor = bgSuccess;
                    }

                default:
                    nacionalidad.style.backgroundColor = bgNone
                    break;
            }
        });

        nacionalidad.addEventListener("change", function () {
            let edad = calcularEdad(
                formatoYMD(fIntervencion.value),
                formatoYMD(fechaNacimiento.value)
            );
            edad < 7 && this.value == "50" ?
                ((tipoDocumento.value = "60"),
                    (tipoDocumento.style.backgroundColor = bgSuccess)) :
                (tipoDocumento.style.backgroundColor = bgNone);

            edad > 7 && edad < 18 && this.value == '50' ?
                (tipoDocumento.value = '61', tipoDocumento.style.backgroundColor = bgSuccess) :
                (tipoDocumento.style.backgroundColor = bgNone)

            edad >= 18 && this.value == "50" ?
                ((tipoDocumento.value = "59"),
                    (tipoDocumento.style.backgroundColor = bgSuccess)) :
                (tipoDocumento.style.backgroundColor = bgNone);

            if (this.value != '50') {
                switch (tipoDocumento.value) {
                    case '59':
                    case '60':
                    case '61':
                        tipoDocumento.value = '';
                        tipoDocumento.style.backgroundColor = bgError
                        toggleBtnActualizacion(0)
                        break;

                    default:
                        break;
                }
            }




            //edad > 7 || edad < 18 ? ((tipoDocumento.value = "61"),
            //(tipoDocumento.style.backgroundColor = "rgba(50, 255, 50, 0.4)"))
            //: (tipoDocumento.style.backgroundColor = "#fff");

            //this.value != '50' && tipoDocumento.value == '59' ? (tipoDocumento.style.backgroundColor = 'rgba(255, 50, 50, 0.4)', btnActualizar.disabled = true) : (tipoDocumento.style.backgroundColor = '#fff', btnActualizar.disabled = false)
        });

        //PDI
        let poblacion = document.querySelector("#valorControl59");
        poblacion.required = true;

        //Sexo y género
        let sexo = document.querySelector("#valorControl53");
        let genero = document.querySelector("#valorControl54");
        sexo.addEventListener("change", function () {
            this.value == "67" ?
                ((genero.value = "70"),
                    (genero.style.backgroundColor = "rgba(50, 255, 50, 0.4)")) :
                false;
            this.value == "68" ?
                ((genero.value = "71"),
                    (genero.style.backgroundColor = "rgba(50, 255, 50, 0.4)"),
                    (genero.style.borderColor = "rgba(50, 255, 50, 1)")) :
                false;
        });

        genero.addEventListener("change", function () {
            this.value == "70" ?
                ((sexo.value = "67"),
                    (sexo.style.backgroundColor = "rgba(50, 255, 50, 0.4)")) :
                false;
            this.value == "71" ?
                ((sexo.value = "68"),
                    (sexo.style.backgroundColor = "rgba(50, 255, 50, 0.4)"),
                    (sexo.style.borderColor = "rgba(50, 255, 50, 1)")) :
                false;
        });

        let poblacionSC = document.querySelector("#valorControl61");
        tipoDocumento.addEventListener('change', function () {
            this.value == '60' || this.value == '61' ? (poblacionSC.value = '351', poblacionSC.style.backgroundColor = bgSuccess) : (poblacionSC.style.backgroundColor = bgNone)
        })

        document.getElementById('valorControl59').required = true;
    } catch (error) { }

    /*------------------------PREVENCIÓN DEL EMBARAZO----------------------------*/
    try {
        let componente = document.querySelector("#valorControl8403");
        let procesoGeneral = document.querySelector("#valorControl8404");
        let poblacion_PE = document.querySelector("#valorControl8402");
        let esNueva = document.querySelector("#valorControl377");
        let colegio_sintonizarte = document.querySelector("#valorControl378");
        let apoyo_pareja = document.querySelector("#valorControl379");
        let convive_pareja = document.querySelector("#valorControl380");
        let edadPareja = document.querySelector("#valorControl381");
        let gestante = document.querySelector("#valorControl383");
        let embarazos_previos = document.querySelector("#valorControl402");
        let aislamiento_familiar = document.querySelector("#valorControl407");
        let perfil_profesional = document.querySelector("#valorControl8389");

        //Campos de gestantes
        let fechaProb_parto = document.querySelector("#valorControl384");
        let edad_gestacional = document.querySelector("#valorControl385");

        //Si es gestante debe tener fecha probable de parto
        gestante.addEventListener('change', function () {
            this.value == '1309' ?
                (fechaProb_parto.required = true, edad_gestacional.required = true) :
                (fechaProb_parto.required = false, edad_gestacional.required = true);
        })

        componente.value = '9';
        procesoGeneral.value = '303';

        if (componente.value == '9' && procesoGeneral.value == '303') {
            componente.style.backgroundColor = bgSuccess;
            procesoGeneral.style.backgroundColor = bgSuccess;
        }

        //Visualización campos dirección
        let zona_pe = document.querySelector("#valorControl321");
        let localidad_pe = document.querySelector("#valorControl323");
        let upz_pe = document.querySelector("#valorControl356");
        let upzPriorizada_pe = document.querySelector("#valorControl8405");
        let barrio_pe = document.querySelector("#valorControl357");
        let barrioPriorizado_pe = document.querySelector("#valorControl8406");
        let manzana_pe = document.querySelector("#valorControl8407");

        let camposVisibles = [zona_pe, localidad_pe, upz_pe, upzPriorizada_pe, barrio_pe, barrioPriorizado_pe, manzana_pe];

        for (let i = 0; i < camposVisibles.length; i++) {
            camposVisibles[i].style.borderColor = "#3581B8";
            camposVisibles[i].style.borderWidth = "2px";
        }

        //Longitud telefono
        let telefono_PE = document.querySelector("#valorControl375");
        telefono_PE.addEventListener('keyup', function () {
            this.value.length != 7 && this.value.length != 10 ?
                (this.style.backgroundColor = bgError, toggleBtnActualizacion(0)) :
                (this.style.backgroundColor = bgSuccess, toggleBtnActualizacion(1))
        })

        edadPareja.setAttribute("type", "number");

        //Campos obligatorios
        esNueva.required = true;
        colegio_sintonizarte.required = true;
        componente.required = true;
        poblacion_PE.required = true;
        procesoGeneral.required = true;
        apoyo_pareja.required = true;
        convive_pareja.required = true;
        //edadPareja.required = true; // OBCIONAL
        embarazos_previos.required = true;
        aislamiento_familiar.required = true;
        perfil_profesional.required = true;
        zona_pe.addEventListener('change', function () {
            this.value == '57' ?
                (barrio_pe.required = true,
                    barrio_pe.style.borderColor = "#3581B8",
                    barrio_pe.style.borderWidth = "2px",
                    barrio_pe.disabled = false) :
                (barrio_pe.required = false,
                    barrio_pe.style.borderColor = "#fff");
        })

        barrio_pe.required = true;

        fecha_parto.pattern = "[0-9]{2}-[0-9]{2}-[0-9]{4}";



    } catch (error) {

    }

    /*------------------------SALUD MENTAL----------------------------*/
    try {
        //Tipo de institución y curso
        let tipoInstitucion = document.querySelector("#valorControl10574");
        let curso = document.querySelector("#valorControl10579");
        tipoInstitucion.addEventListener('change', function () {
            this.value == '1634' || this.value == '1633' ? curso.required = true : curso.required = false;
            this.value == '1636' ? (curso.value = '401', curso.style.backgroundColor = "rgba(50, 255, 50, 0.4)") : curso.style.backgroundColor = "#fff";
        })

        //Longitud nombres y apellidos
        let primerNombre = document.querySelector("#valorControl10580");
        let segundoNombre = document.querySelector("#valorControl10581");
        let primerApellido = document.querySelector("#valorControl10582");
        let segundoApellido = document.querySelector("#valorControl10583");

        primerNombre.addEventListener('keyup', function () {
            this.value.length < 3 ? (this.style.backgroundColor = bgError, this.style.color = letraBlanca) : (this.style.backgroundColor = '#fff', this.style.color = letraNegra);
        })

        segundoNombre.addEventListener('keyup', function () {
            this.value.length < 3 ? (this.style.backgroundColor = bgError, this.style.color = letraBlanca) : (this.style.backgroundColor = '#fff', this.style.color = letraNegra);
        })

        primerApellido.addEventListener('keyup', function () {
            this.value.length < 3 ? (this.style.backgroundColor = bgError, this.style.color = letraBlanca) : (this.style.backgroundColor = '#fff', this.style.color = letraNegra);
        })

        segundoApellido.addEventListener('keyup', function () {
            this.value.length < 3 ? (this.style.backgroundColor = bgError, this.style.color = letraBlanca) : (this.style.backgroundColor = '#fff', this.style.color = letraNegra);
        })

        //Tipo documento vs nacionalidad
        let tipoDocSM = document.querySelector("#valorControl10584");
        let nacionalidadSM = document.querySelector("#valorControl10586");

        tipoDocSM.addEventListener('change', function () {
            switch (this.value) {
                case '59':
                case '60':
                case '61':
                    nacionalidadSM.value = "50";
                    nacionalidadSM.style.backgroundColor = bgSuccess;
                    tipoDocSM.style.backgroundColor = bgSuccess;
                    break;

                case '65':
                case '66':
                case '1639':
                case '1640':
                case '2482':
                case '62':
                    nacionalidadSM.value == "50" ? (nacionalidadSM.style.backgroundColor = bgError) : (nacionalidadSM.style.backgroundColor = bgSuccess, this.style.backgroundColor = bgSuccess);

                default:
                    nacionalidadSM.style.backgroundColor = bgNone
                    break;
            }
        })

        //Nacionalidad vs Tipo documento
        nacionalidadSM.addEventListener('change', function () {
            switch (tipoDocSM.value) {
                case '59':
                case '60':
                case '61':
                    this.value != '50' || this.value == '' ? (tipoDocSM.style.backgroundColor = bgError, tipoDocSM.value = '') : tipoDocSM.style.backgroundColor = bgSuccess
                    break;

                default:
                    tipoDocSM.style.backgroundColor = bgNone;
                    break;
            }
        })

        //Sexo y género
        let sexoSM = document.querySelector("#valorControl10587");
        let generoSM = document.querySelector("#valorControl10588");
        let etapa_gest = document.querySelector("#valorControl10596");

        sexoSM.addEventListener('change', function () {
            switch (this.value) {
                case '68':
                    generoSM.value = '71';
                    generoSM.style.backgroundColor = bgSuccess;
                    etapa_gest.disabled = false;
                    break;

                case '67':
                    generoSM.value = '70';
                    generoSM.style.backgroundColor = bgSuccess;
                    etapa_gest.disabled = true;
                    break;

                default:
                    generoSM.style.backgroundColor = bgNone;
                    etapa_gest.disabled = true;
                    break;
            }
        })

        generoSM.addEventListener('change', function () {
            switch (this.value) {
                case '71':
                    sexoSM.value = '68';
                    sexoSM.style.backgroundColor = bgSuccess;
                    break;

                case '70':
                    sexoSM.value = '67';
                    sexoSM.style.backgroundColor = bgSuccess;
                    break;

                default:
                    sexoSM.style.backgroundColor = bgNone;
                    break;
            }
        })
        let btnUpdSM = document.querySelector("#botonActualizarInformacion");

        //Formato fecha nacimiento
        let fechaNacSM = document.querySelector("#valorControl10592");
        fechaNacSM.pattern = '[0-9]{2}/[0-9]{2}/[0-9]{4}';

        document.getElementById('valorControl10592').addEventListener('change', function () {
            // Obtener la fecha de nacimiento del campo
            var fechaNacimiento = this.value;

            // Calcular la edad
            var fechaNacArray = fechaNacimiento.split('/');
            var fechaNac = new Date(fechaNacArray[2], fechaNacArray[1] - 1, fechaNacArray[0]);
            var hoy = new Date();
            var edad = hoy.getFullYear() - fechaNac.getFullYear();

            // Ajustar la edad si aún no ha llegado el cumpleaños en este año
            if (hoy < new Date(hoy.getFullYear(), fechaNac.getMonth(), fechaNac.getDate())) {
                edad--;
            }

            // Crear el span si no existe
            var edadSpan = document.getElementById('edadSpan');
            if (!edadSpan) {
                edadSpan = document.createElement('span');
                edadSpan.id = 'edadSpan';
                document.getElementById('datepicker-pastdisabled10592').appendChild(edadSpan);
            }

            // Mostrar la edad en el span
            edadSpan.textContent = 'Edad: ' + edad + ' años';

            var cursoinput = document.getElementById("valorControl10579")

            var curso = cursoinput.value

            // Mostrar una alerta si la edad es menor de 5 años
            if (edad < 5 && curso > 390) {
                alert('la persona es menor de 5 años no puede estar en el curso seleccionado ');
                cursoinput.style.backgroundColor = '#FF3333'
                cursoinput.style.color = '#FFf'
                document.getElementById("botonActualizarInformacion").disabled = true;
            } else {
                document.getElementById("botonActualizarInformacion").disabled = false;
                cursoinput.style.backgroundColor = '#74FF33'

            }
        });

        //Fecha nacimiento vs tipo documento


        let fechaIntervencionSM = document.querySelector("#FechaIntervencion");

        fechaNacSM.addEventListener('change', function () {
            let edad = calcularEdad(fechaIntervencionSM.value, fechaNacSM.value);
            if (edad >= 18) {
                nacionalidadSM.value == '50' ? (tipoDocSM.value = '59', tipoDocSM.style.backgroundColor = bgSuccess) : tipoDocSM.style.backgroundColor = bgNone; //CC
            } else if (edad < 7) {
                nacionalidadSM.value == '50' ? (tipoDocSM.value = '60', tipoDocSM.style.backgroundColor = bgSuccess) : tipoDocSM.style.backgroundColor = bgNone; //RC
            } else if (edad >= 7 && edad < 18) {
                nacionalidadSM.value == '50' ? (tipoDocSM.value = '61', tipoDocSM.style.backgroundColor = bgSuccess) : tipoDocSM.style.backgroundColor = bgNone; //TI
            }

            if (edad < 15) {

            } else {

            }
        })

        //Afiliación al SGSSS
        let afiliado = document.querySelector("#valorControl10594");
        let nombreEAPB = document.querySelector("#valorControl10595");
        nombreEAPB.required = true;
        afiliado.addEventListener('change', function () {
            this.value == '135' ? (nombreEAPB.value = '', nombreEAPB.disabled = false, nombreEAPB.value = "NO ASEGURADO", nombreEAPB.style.backgroundColor = '#33FF3C') : nombreEAPB.disabled = false;
        })

        //Coordenadas Salud mental
        let coordenadaX_SM = document.querySelector("#valorControl10625");
        let coordenadaY_SM = document.querySelector("#valorControl10626");
        formatoCoordenadas(coordenadaX_SM, coordenadaY_SM);

        //Teléfono
        let telefonoSM_1 = document.querySelector("#valorControl10627");
        telefonoSM_1.addEventListener('change', function () {
            this.value.length < 7 || this.value.length > 10 || this.value.length == 8 || this.value.length == 9 ?
                (this.style.backgroundColor = bgError, this.style.color = letraBlanca, disabledBtnUpd(btnUpdSM, 0)) :
                (this.style.backgroundColor = bgSuccess, this.style.color = letraNegra, disabledBtnUpd(btnUpdSM, 1));
        })

        let telefonoSM_2 = document.querySelector("#valorControl10628");
        telefonoSM_2.addEventListener('change', function () {
            this.value.length < 7 || this.value.length > 10 || this.value.length == 8 || this.value.length == 9 ?
                (this.style.backgroundColor = bgError, this.style.color = letraBlanca, disabledBtnUpd(btnUpdSM, 0)) :
                (this.style.backgroundColor = bgSuccess, this.style.color = letraNegra, disabledBtnUpd(btnUpdSM, 1));
        })

    } catch (error) { }

    //Entorno escolar jardines
    try {
        let fech_int_eej = document.querySelector("#FechaIntervencion");
        let fechaSeguimiento_1 = document.querySelector("#valorControl4074");
        let fechaSeguimiento_2 = document.querySelector("#valorControl4075");
        fechaSeguimiento_1.pattern = '[0-9]{2}/[0-9]{2}/[0-9]{4}';
        fechaSeguimiento_2.pattern = '[0-9]{2}/[0-9]{2}/[0-9]{4}';

        if (fechaSeguimiento_1.value = fech_int_eej.value) {
            fechaSeguimiento_1.style.backgroundColor = bgSuccess;
        }

        let tipo_inst = document.querySelector("#valorControl4079");
        tipo_inst.required = true;

        let num_estudiantes = document.querySelector("#valorControl4081");
        num_estudiantes.setAttribute("type", "number");

        let num_docentes = document.querySelector("#valorControl4081");
        num_docentes.setAttribute("type", "number");

        let localidad_cab = document.querySelector("#Localidad_fic");
        let localidad_fich = document.querySelector("#valorControl4085");

        //Misma localidad ficha
        if (localidad_fich.value = localidad_cab.value) {
            localidad_fich.style.backgroundColor = bgSuccess;
        }

        //Longitud No. telefono
        let telefono_eej = document.querySelector("#valorControl4112");
        telefono_eej.addEventListener('keyup', function () {
            switch (this.value.length) {
                case 7:
                case 10:
                    this.style.backgroundColor = bgSuccess;
                    toggleBtnActualizacion(1)
                    break;

                default:
                    this.style.backgroundColor = bgError;
                    toggleBtnActualizacion(0)
                    break;
            }
        })

    } catch (error) {

    }

} catch (error) { }

try {
    /* manzana del Cuidado Higiene Bucal pagina 1*/

    document.getElementById('valorControl7998').addEventListener('change', function () {

        let manzanaDelCuidado = document.getElementById('valorControl7998').value;


        if (manzanaDelCuidado == 959) {
            document.querySelector("#valorControl10413").disabled = true;
            document.querySelector("#valorControl10413").required = false;
        } else {
            document.querySelector("#valorControl10413").disabled = false;
            document.querySelector("#valorControl10413").required = true;
        }
    })


} catch (error) {

}

try {
    /* manzana del Cuidado Higiene Bucal pagina 2*/
    document.getElementById('valorControl8037').required = true;
} catch (error) {

}

document.addEventListener('DOMContentLoaded', function () {
    // Detectar si hay un SweetAlert modal abierto y aplicar la configuración
    Swal.update({
        allowOutsideClick: false
    });
});

// ocultar bases antiguas

// document.getElementById("forOpcionEducativoGesiForm13").hidden = true;
// document.getElementById("forOpcionEducativoGesiForm16").hidden = true;
// document.getElementById("forOpcionEducativoGesiForm8").hidden = true;

document.querySelector('#forOpcionEducativoGesiForm12 a').style.color = '#36ff00';
document.querySelector('#forOpcionEducativoGesiForm10 a').style.color = '#36ff00';
document.querySelector('#forOpcionEducativoGesiForm14 a').style.color = '#36ff00';

// paternidad temprana

document.getElementById('formularioNuevoFormato').addEventListener("change", (event) => {
    const campo = event.target;
    console.log(campo)
    // if (campo.type != 'submit') {
    //     establecerIndicadoresInputs(campo);
    // }
});

function quitarRequeridoTemporal() {
    const nivelapt = document.querySelector("#valorControl20487");

    if (nivelapt) {
        console.log("El campo existe en este formulario");
        nivelapt.required = false;
    } else {
        console.log("El campo no existe en este formulario");
    }
}

const sds_base_id = document.querySelector("#Id_Base").value;

if (sds_base_id == '52') {
    quitarRequeridoTemporal();
}


