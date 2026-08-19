let colorrojo = "#943126"
let colorazul = "#34495E"
let colorblancotxt = "#ffff"

try {

    const inputs = document.querySelectorAll('input');

    inputs.forEach(input => {
        input.setAttribute('autocomplete', 'off')
    });

    let logo = document.querySelector("#sidebar > div > div.sidebar-brand.logo_blanco");
    logo.style.backgroundColor = "#5B2C6F";
    logo.style.color = "#fff";
    logo.innerHTML = "<h3>CODE LABORAL</h3>";
    document.querySelector("#main_body > div > div > main > div > div > div > div.panel-heading").style.backgroundColor = "#31353d"

    let bgError = "#943126";
    let bgSuccess = "#1E8449";
    let letraBlanca = "#fff"

    function error(campo) {
        campo.style.backgroundColor = bgError;
        campo.style.color = letraBlanca;
    }

    function success(campo) {
        campo.style.backgroundColor = bgSuccess;
        campo.style.color = letraBlanca;
    }
    // Obtén el elemento de entrada de fecha
    const fechaInput = document.querySelector("#FechaIntervencion");

    // Agrega el evento de escucha 'input' al campo de fecha
    fechaInput.addEventListener('change', validarFecha);

    // Función para validar la fecha ingresada
    function validarFecha() {
        var verificar = document.querySelector("#FechaIntervencion").value;
        var fechaComponentes1 = verificar.split("/");
        var mes1 = fechaComponentes1[1];
        var año1 = fechaComponentes1[2];

        // Verifica si el mes seleccionado no es el mes deseado (por ejemplo, febrero = 2)
        if (mes1 != 10 || año1 != 2024) {
            // Restablece el valor del campo de fecha a una fecha válida dentro del mes deseado
            alert('Fecha de intervención incorrecta')
            document.querySelector("#FechaIntervencion").value = ""

        }
    }
    //Validar consecutivo y entrega
    let consecutivo = document.querySelector("#Consecutivo_fic");
    consecutivo.addEventListener('change', function() {
        this.value.length != 5 ?
            (toggleBtnActualizacion(0), error(consecutivo), alert("Consecutivo incorrecto!!")) :
            (toggleBtnActualizacion(1), success(consecutivo));
    })

    function validarEntrega() {
        let consecutivo = document.querySelector("#Consecutivo_fic").value;
        let localidad = document.querySelector("#Ficha_fic").value;
        let entorno = document.querySelector("#Espacio_fic").value;
        let fichaCompleta = `1${localidad}${entorno}${consecutivo}`;
        const digitadas = new XMLHttpRequest();
        digitadas.onreadystatechange = function() {
            if (this.readyState === 4 && this.status === 200) {
                if (this.responseText == '1') return true;
                else return false;
            }
        };
        digitadas.open("GET", `https://gesiapp.com.co/configuraciones/ReporteDigitado/consultaEntrega.php?fichaCompleta=${fichaCompleta}`, true);
        digitadas.send();
    }

    function toggleBtnActualizacion(estado) {
        let btnActualizar = document.querySelector("#botonActualizarInformacion")
        estado == 1 ? btnActualizar.disabled = false : false
        estado == 0 ? btnActualizar.disabled = true : false
    }
} catch (error) {}

try {

    //NNA

    document.querySelector("#main_body > div > div").style.backgroundColor = "#31353d";
    document.querySelector("#main_body > div > div > main").style.backgroundColor = "#31353d";
    document.querySelector("#main_body > div > div > main > div > div > div > div.panel-heading > div > div").style.color = "#FDFEFE";

    var ocultarcc = document.querySelector("#valorControl10528");
    var renuevacc = ocultarcc.options[1];
    renuevacc.style.display = "none";
    var renuevace = ocultarcc.options[4];
    renuevace.style.display = "none";
    var renuevaasi = ocultarcc.options[7];
    renuevaasi.style.display = "none";
    var renuevacd = ocultarcc.options[9];
    renuevacd.style.display = "none";
    var renuevasc = ocultarcc.options[10];
    renuevasc.style.display = "none";

    //Validar longitud documento
    let longidocnna = document.querySelector("#valorControl10529");
    let tdoc = document.querySelector("#valorControl10528");

    longidocnna.addEventListener("keyup", function() {
        switch (tdoc.value) {
            case "60":
            case "61":

                if (longidocnna.value.length != 10) {
                    toggleBtnActualizacion(0);
                    longidocnna.style.backgroundColor = "#943126";
                    longidocnna.style.color = "#fff";
                } else {
                    toggleBtnActualizacion(1);
                    longidocnna.style.backgroundColor = "#34495E";
                    longidocnna.style.color = "#fff";
                }

                break;

            default:

                if (longidocnna.value.length < 3) {
                    toggleBtnActualizacion(0);
                    longidocnna.style.backgroundColor = "#943126";
                    longidocnna.style.color = "#fff";
                } else {
                    toggleBtnActualizacion(1);
                    longidocnna.style.backgroundColor = "#34495E";
                    longidocnna.style.color = "#fff";
                }
                break;
        }

    })

    // validar nacionalidad con documento
    var pobdi = document.querySelector("#valorControl10542");
    document
        .querySelector("#valorControl10528")
        .addEventListener("change", function tipodocumento() {
            var tipodoc = document.querySelector("#valorControl10528");
            if (tipodoc.value == "60" || tipodoc.value == "61") {

                var nacionalidad = document.querySelector("#valorControl10534");
                nacionalidad.value = "50";
                nacionalidad.style.backgroundColor = "#34495E";
                nacionalidad.style.color = "#FDFEFE";
                document
                    .querySelector("#valorControl10529")
                    .setAttribute("max", 999999999);
                document
                    .querySelector("#valorControl10529")
                    .setAttribute("min", 99999999);

                pobdi.value = "2620";
                pobdi.style.backgroundColor = "#34495E";
                pobdi.style.color = "#fff";

                if (longidocnna.value.length != 10) {
                    toggleBtnActualizacion(0);
                    longidocnna.style.backgroundColor = "#943126";
                    longidocnna.style.color = "#fff";
                } else {
                    toggleBtnActualizacion(1);
                    longidocnna.style.backgroundColor = "#34495E";
                    longidocnna.style.color = "#fff";
                }

            } else {

                pobdi.value = "2618";
                pobdi.style.backgroundColor = "#34495E";
                pobdi.style.color = "#fff";

                if (longidocnna.value.length < 3) {
                    toggleBtnActualizacion(0);
                    longidocnna.style.backgroundColor = "#943126";
                    longidocnna.style.color = "#fff";
                } else {
                    toggleBtnActualizacion(1);
                    longidocnna.style.backgroundColor = "#34495E";
                    longidocnna.style.color = "#fff";
                }

            }





        });

    //sexo y genero

    document.querySelector("#valorControl10535").addEventListener("change", function sexoygenero() {

        var sexomenor = document.querySelector("#valorControl10535")
        var generomenor = document.querySelector("#valorControl10536")
        switch (sexomenor.value) {
            case "67":

                generomenor.value = "70"
                generomenor.style.backgroundColor = "#34495E";
                generomenor.style.color = "#FDFEFE";
                sexomenor.style.backgroundColor = "#34495E";
                sexomenor.style.color = "#FDFEFE";

                break;
            case "68":

                generomenor.value = "71"
                generomenor.style.backgroundColor = "#34495E";
                generomenor.style.color = "#FDFEFE";
                sexomenor.style.backgroundColor = "#34495E";
                sexomenor.style.color = "#FDFEFE";

                break;

            case "69":

                generomenor.value = "72"
                generomenor.style.backgroundColor = "#34495E";
                generomenor.style.color = "#FDFEFE";
                sexomenor.style.backgroundColor = "#34495E";
                sexomenor.style.color = "#FDFEFE";

                break;

            default:
                break;
        }

    })


    // fecha de nacimiento
    let fechnac = document.querySelector("#valorControl10538")

    fechnac.pattern = "[0-9]{2}/[0-9]{2}/[0-9]{4}";

    fechnac.addEventListener('change', function() {

        let edad2 = document.querySelector("#valorControl10539")

        var resultadoedad = edad2.value;

        if (resultadoedad <= 17) {

            toggleBtnActualizacion(1);
            fechnac.style.backgroundColor = colorazul;
            fechnac.style.color = colorblancotxt;

        } else {

            toggleBtnActualizacion(0);
            fechnac.style.backgroundColor = colorrojo;
            fechnac.style.color = colorblancotxt;
        }

    })



    document.querySelector("#valorControl10538").addEventListener("change", function validadordependientefecha() {
        var edad = document.querySelector("#valorControl10539").value;
        if (edad >= 0 && edad <= 13) {
            var estadocivil = document.querySelector("#valorControl10537");
            estadocivil.value = "78";
            estadocivil.style.backgroundColor = "#34495E";
            estadocivil.style.color = "#FDFEFE";
        } else if (edad >= 14) {
            var estadocivil = document.querySelector("#valorControl10537");
            estadocivil.value = "73";
            estadocivil.style.backgroundColor = "#34495E";
            estadocivil.style.color = "#FDFEFE";
        }
    });

    // etnia
    document.querySelector("#valorControl10541").addEventListener("change", function etnia() {
        var valoretnia = document.querySelector("#valorControl10541").value;

        if (valoretnia != 84) {
            document.querySelector("#valorControl10546").required = true;
            document.querySelector("#valorControl10546").disabled = false;
        } else {
            document.querySelector("#valorControl10546").disabled = true;
            document.querySelector("#valorControl10546").required = false;
        }
    });

    //eapb

    document
        .querySelector("#valorControl10548")
        .addEventListener("change", function afiliacion() {
            var vinculacion = document.querySelector("#valorControl10548").value;
            if (vinculacion == 135) {
                var eapb = document.querySelector("#valorControl10549");
                eapb.value = "NO ASEGURADO";
                eapb.style.backgroundColor = "#34495E";
                eapb.style.color = "#FDFEFE";
            }
        });



    document
        .querySelector("#valorControl10550")
        .addEventListener("change", function zona() {
            var zona = document.querySelector("#valorControl10550").value;
            if (zona == 57) {
                document.querySelector("#valorControl10662").required = true;
                document.querySelector("#valorControl10662").disabled = false;
            } else {
                document.querySelector("#valorControl10662").disabled = true;
                document.querySelector("#valorControl10662").required = false;
            }
        });

    document
        .querySelector("#valorControl10663")
        .addEventListener("change", function nivelsisben() {
            var sisben = document.querySelector("#valorControl10663").value;
            if (sisben == "") {
                document.querySelector("#valorControl10664").disabled = true;
                document.querySelector("#valorControl10665").disabled = true;
                document.querySelector("#valorControl10664").required = false;
                document.querySelector("#valorControl10665").required = false;
            } else {
                document.querySelector("#valorControl10664").disabled = false;
                document.querySelector("#valorControl10665").disabled = false;
                document.querySelector("#valorControl10664").required = true;
                document.querySelector("#valorControl10665").required = true;
            }
        });

    document
        .querySelector("#valorControl10675")
        .addEventListener("change", function tipodocumentoacu() {
            var tipodoc = document.querySelector("#valorControl10675").value;
            switch (tipodoc) {
                case "59":
                    var nacionalidad = document.querySelector("#valorControl10679");
                    nacionalidad.value = "50";
                    nacionalidad.style.backgroundColor = "#34495E";
                    nacionalidad.style.color = "#FDFEFE";

                    break;
                case "60":
                    var nacionalidad = document.querySelector("#valorControl10679");
                    nacionalidad.value = "50";
                    nacionalidad.style.backgroundColor = "#34495E";
                    nacionalidad.style.color = "#FDFEFE";

                    break;
                case "61":
                    var nacionalidad = document.querySelector("#valorControl10679");
                    nacionalidad.value = "50";
                    nacionalidad.style.backgroundColor = "#34495E";
                    nacionalidad.style.color = "#FDFEFE";

                    break;

                default:
                    break;
            }
        });

    document
        .querySelector("#valorControl10685")
        .addEventListener("change", function afiliacion() {
            var vinculacion = document.querySelector("#valorControl10685").value;
            if (vinculacion == 135) {
                var eapb = document.querySelector("#valorControl10686");
                eapb.value = "NO ASEGURADO";
                eapb.style.backgroundColor = "#34495E";
                eapb.style.color = "#FDFEFE";
            }
        });

    document.querySelector("#valorControl10687").required = true;
    document.querySelector("#valorControl10688").required = true;

    document
        .querySelector("#valorControl10465")
        .addEventListener("change", function condicionesdesalud() {
            var tipointervencion = document.querySelector("#valorControl10465").value;
            if (tipointervencion == 105) {
                var lesionlaboral = document.querySelector("#valorControl10747");
                lesionlaboral.value = "NO APLICA";
                lesionlaboral.style.backgroundColor = "#34495E";
                lesionlaboral.style.color = "#FDFEFE";
            } else {
                var lesionlaboral = document.querySelector("#valorControl10747");
                var renuevall = lesionlaboral.options[3];
                renuevall.style.display = "none";
            }
        });

    document.querySelector("#valorControl10764\\[\\]").required = true;
    document.querySelector("#valorControl10765\\[\\]").required = true;
    document.querySelector("#valorControl10767").required = true;
    document.querySelector("#valorControl10768").required = true;
    document.querySelector("#valorControl10769").required = true;
    document.querySelector("#valorControl10770").required = true;
    document.querySelector("#valorControl10771").required = true;
    document.querySelector("#valorControl10772").required = true;
    document.querySelector("#valorControl10776").required = true;
    document.querySelector("#valorControl10777").required = true;
    document.querySelector("#valorControl10778").required = true;
    document.querySelector("#valorControl10779").required = true;
    document.querySelector("#valorControl10780").required = true;
    document.querySelector("#valorControl10781").required = true;
    document.querySelector("#valorControl10782").required = true;

    document
        .querySelector("#valorControl10741")
        .addEventListener("change", function estudio() {
            var primaria = document.querySelector("#valorControl10741").value;
            if (primaria != "") {
                document.querySelector("#valorControl10742").disabled = true;
            } else {
                document.querySelector("#valorControl10742").disabled = false;
            }
        });
} catch (error) {
    try {
        document.querySelector("#valorControl10786").disabled = true;
        document.querySelector("#valorControl10787").disabled = true;
        document.querySelector("#valorControl10788").disabled = true;
        document.querySelector("#valorControl10789").disabled = true;
        document.querySelector("#valorControl10791").disabled = true;
        document.querySelector("#valorControl10792").disabled = true;
        document.querySelector("#valorControl10793").disabled = true;
        document.querySelector("#valorControl10795").disabled = true;
        document.querySelector("#valorControl10796").disabled = true;
        document.querySelector("#valorControl10797").disabled = true;
        document.querySelector("#valorControl10798").disabled = true;
        document.querySelector("#valorControl10799").disabled = true;
        document.querySelector("#valorControl10801").disabled = true;
        document.querySelector("#valorControl10802").disabled = true;
        document.querySelector("#valorControl10803").disabled = true;
        document.querySelector("#valorControl10804").disabled = true;
        document.querySelector("#valorControl10805").disabled = true;
        document.querySelector("#valorControl10806").disabled = true;
        document.querySelector("#valorControl10807").disabled = true;
        document.querySelector("#valorControl10809").required = true;
        //document.querySelector("#valorControl10810").required = true;
        document.querySelector("#valorControl10812").required = true;
        //document.querySelector("#valorControl10813").required = true;
        document.querySelector("#valorControl10815").required = true;
        //document.querySelector("#valorControl10816").required = true;
        document.querySelector("#valorControl10818").required = true;
        //document.querySelector("#valorControl10819").required = true;
        document.querySelector("#valorControl10821").required = true;
        //document.querySelector("#valorControl10822").required = true;
        document.querySelector("#valorControl10824").required = true;
        //document.querySelector("#valorControl10825").required = true;
        document.querySelector("#valorControl10827").required = true;
        //document.querySelector("#valorControl10828").required = true;
        document.querySelector("#valorControl10830").required = true;
        //document.querySelector("#valorControl10831").required = true;
        document.querySelector("#valorControl10833").required = true;
        //document.querySelector("#valorControl10834").required = true;
        document.querySelector("#valorControl10836").required = true;
        //document.querySelector("#valorControl10837").required = true;

        document
            .querySelector("#valorControl10783")
            .addEventListener("change", function fechaplan() {
                var fecha = document.querySelector("#valorControl10783").value;
                if (fecha != "") {
                    document.querySelector("#valorControl10786").disabled = false;
                    document.querySelector("#valorControl10787").disabled = false;
                    document.querySelector("#valorControl10788").disabled = false;
                    document.querySelector("#valorControl10789").disabled = false;
                    document.querySelector("#valorControl10791").disabled = false;
                    document.querySelector("#valorControl10792").disabled = false;
                    document.querySelector("#valorControl10793").disabled = false;
                    document.querySelector("#valorControl10795").disabled = false;
                    document.querySelector("#valorControl10796").disabled = false;
                    document.querySelector("#valorControl10797").disabled = false;
                    document.querySelector("#valorControl10798").disabled = false;
                    document.querySelector("#valorControl10799").disabled = false;
                    document.querySelector("#valorControl10801").disabled = false;
                    document.querySelector("#valorControl10802").disabled = false;
                    document.querySelector("#valorControl10803").disabled = false;
                    document.querySelector("#valorControl10804").disabled = false;
                    document.querySelector("#valorControl10805").disabled = false;
                    document.querySelector("#valorControl10806").disabled = false;
                    document.querySelector("#valorControl10807").disabled = false;
                }
            });
    } catch (error) {
        //UTIS
        try {
            var tipoUT = document.querySelector("#valorControl11226").value;
            localStorage.setItem("ut", tipoUT);

            var ocultarrc = document.querySelector("#valorControl11264");
            var renuevarc = ocultarrc.options[2];
            renuevarc.style.display = "none";
            var ocultarti = document.querySelector("#valorControl11264");
            var renuevati = ocultarti.options[3];
            renuevati.style.display = "none";
            var ocultarmsi = document.querySelector("#valorControl11264");
            var renuevamsi = ocultarmsi.options[8];
            renuevamsi.style.display = "none";


            document.querySelector("#valorControl11264").addEventListener("change", function docpropietario() {
                var doc = document.querySelector("#valorControl11264").value;

                if (doc == "59") {

                    var nacional = document.querySelector("#valorControl11266");
                    nacional.value = "50";
                    nacional.style.backgroundColor = "#34495E";
                    nacional.style.color = "#FDFEFE"
                }
            });
        } catch (error) {
            try {
                //pagina 2
                var utis = localStorage.getItem("ut");
                /*
                        if (utis == '1970') {
                            
                            document.querySelector("#valorControl11396").disabled = true
                        }
                        else if (utis == '1969'){

                            
                            document.querySelector("#valorControl11396").disabled = true

                        }
                        else{

                            document.querySelector("#valorControl11396").disabled = false
                        //}
                         */

                document.querySelector("#valorControl11409").required = true;
                document.querySelector("#valorControl11410").required = true;
                document.querySelector("#valorControl11412").required = true;
                document.querySelector("#valorControl11413").required = true;
                document.querySelector("#valorControl11415").required = true;
                document.querySelector("#valorControl11416").required = true;
                document.querySelector("#valorControl11418").required = true;
                document.querySelector("#valorControl11419").required = true;
                document.querySelector("#valorControl11421").required = true;
                document.querySelector("#valorControl11422").required = true;
                document.querySelector("#valorControl11424").required = true;
                document.querySelector("#valorControl11425").required = true;
                document.querySelector("#valorControl11427").required = true;
                document.querySelector("#valorControl11428").required = true;
                document.querySelector("#valorControl11430").required = true;
                document.querySelector("#valorControl11431").required = true;
                document.querySelector("#valorControl11433").required = true;
                document.querySelector("#valorControl11434").required = true;
                document.querySelector("#valorControl11437").required = true;
                document.querySelector("#valorControl11438").required = true;
                document.querySelector("#valorControl11439").required = true;
                document.querySelector("#valorControl11442").required = true;
                document.querySelector("#valorControl11443").required = true;
                document.querySelector("#valorControl11444").required = true;
            } catch (error) {
                try {
                    //pagina 3

                    var ocultardoc = document.querySelector("#valorControl11451");
                    var renuevarcut = ocultardoc.options[2];
                    renuevarcut.style.display = "none";
                    var renuevatiut = ocultardoc.options[3];
                    renuevatiut.style.display = "none";
                    var renuevamsiut = ocultardoc.options[8];
                    renuevamsiut.style.display = "none";

                    document.querySelector("#valorControl11457").pattern =
                        "[0-9]{2}/[0-9]{2}/[0-9]{4}";
                    document.querySelector("#valorControl11474").pattern =
                        "[0-9]{2}_[0-9]{2}";

                    document.querySelector("#valorControl11464").required = true;
                    document.querySelector("#valorControl11513").required = true;
                    document.querySelector("#valorControl11514").required = true;

                    document
                        .querySelector("#valorControl11451")
                        .addEventListener("change", function documento() {
                            var docs = document.querySelector("#valorControl11451").value;
                            switch (docs) {
                                case "59":
                                    var nac = document.querySelector("#valorControl11453");
                                    nac.value = "50";
                                    nac.style.backgroundColor = "#34495E";
                                    nac.style.color = "#FDFEFE"
                                    var pb = document.querySelector("#valorControl11464");
                                    pb.value = "2620";
                                    pb.style.backgroundColor = "#34495E";
                                    pb.style.color = "#FDFEFE"

                                    break;

                                default:
                                    break;
                            }
                        });

                    document
                        .querySelector("#valorControl11461")
                        .addEventListener("change", function etni() {
                            var etn = document.querySelector("#valorControl11461").value;
                            switch (etn) {
                                case "84":
                                    document.querySelector("#valorControl11469").disabled = true;

                                    break;
                            }
                        });

                    document
                        .querySelector("#valorControl11453")
                        .addEventListener("change", function pobdif() {
                            var naci = document.querySelector("#valorControl11453").value;
                            if (naci != "50") {
                                var pdydi = document.querySelector("#valorControl11464");
                                pdydi.value = "2618";
                                pdydi.style.backgroundColor = "#34495E";
                                pdydi.style.color = "#FDFEFE"
                            }
                        });

                    document
                        .querySelector("#valorControl11454")
                        .addEventListener("change", function sex() {
                            var sexo = document.querySelector("#valorControl11454").value;

                            switch (sexo) {
                                case "67":
                                    var gen = document.querySelector("#valorControl11455");
                                    gen.value = "70";
                                    gen.style.backgroundColor = "#34495E";
                                    gen.style.color = "#FDFEFE";
                                    break;
                                case "68":
                                    var gen = document.querySelector("#valorControl11455");
                                    gen.value = "71";
                                    gen.style.backgroundColor = "#34495E";
                                    gen.style.color = "#FDFEFE";
                                    break;
                            }
                        });

                    document
                        .querySelector("#valorControl11471")
                        .addEventListener("change", function afi() {
                            var afili = document.querySelector("#valorControl11471").value;

                            if (afili == "135")
                                var noase = document.querySelector("#valorControl11472");
                            noase.value = "NO ASEGURADO";
                            noase.style.backgroundColor = "#34495E";
                            noase.style.color = "#FDFEFE";
                        });
                } catch (error) {}
            }
        }
    }
}

function tipouti(valor) {
    let uti = valor;
    localStorage.setItem("ut", JSON.stringify(uti));
    var utis = localStorage.getItem("ut");

    return utis;
}

/*----------------------------- MEDICIÓN DE TIEMPOS ---------------------------------*/

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
    } catch (error) {}

    try {
        btnRegresar = document.querySelector(
            "#main_body > div.page-wrapper.chiller-theme.toggled > div > main > div > div > div > div.panel-heading > div > div > table > tbody > tr > td:nth-child(7) > input"
        );
        btnRegresar.addEventListener("click", DetenerCrono);
    } catch (error) {}

    try {
        btnBuscar = document.querySelector(
            "#main_body > div.page-wrapper.chiller-theme.toggled > div > main > div > div > div > div.panel-heading > div > div > table > tbody > tr > td:nth-child(19) > input"
        );
        btnBuscar.addEventListener("click", init);

        let btnNuevaFicha = document.querySelector(
            "#main_body > div.page-wrapper.chiller-theme.toggled > div > main > div > div > div > div.panel-heading > div > div > table > tbody > tr > td:nth-child(26) > input"
        );
        btnNuevaFicha.addEventListener("click", init);
    } catch (error) {}

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
        } catch (error) {}

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
    }

    function guardarInfo(tiempoTotal, fechaInt) {
        let base = getNombreBase();
        let ficha = getNumFicha();
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
                fechaInt: fechaInt
            },
            success: function(r) {
                console.log(r);
            },
        });
    }
} catch (error) {}

try {
    let TermFicha = document.querySelector("#main_body > div.swal2-container.swal2-center.swal2-shown > div > div.swal2-actions > button.swal2-confirm.swal2-styled");
    TermFicha.addEventListener('click', function() {
        console.log("Guardando")
        let Noficha = document.querySelector("#Ficha_fic").value;
        console.log(Noficha);
        var Nombre = document.querySelector("#main_body > div > div > main > div > div > div > div.panel-heading > div > div.col-md-10 > table > tbody > tr > td:nth-child(2) > b").textContent
        console.log(Nombre);
        const digitadas = new XMLHttpRequest();
        digitadas.onreadystatechange = function() {
            if (this.readyState === 4 && this.status === 200) {
                console.log(this.responseText)
            }
        };
        digitadas.open("GET", `https://gesiapp.com.co/configuraciones/ReporteDigitado/inmgresarDigitado.php?ficha=${Noficha}&name=${Nombre}`, true);
        digitadas.send();
    })
} catch (error) {}

document.addEventListener('DOMContentLoaded', function () {
    // Detectar si hay un SweetAlert modal abierto y aplicar la configuración
    Swal.update({
      allowOutsideClick: false
    });
  });

// =============================GUARDAR REGISTRO SDS ===================================

try {
    //Buttons
    const btnGuardar = document.querySelector("#botonActualizarInformacion");
    const btnEditReg = document.querySelector("#btnEditReg");
    const btnBack = document.querySelector("#main_body > div > div > main > div > div > div > div.panel-heading > div > div.col-md-2 > table > tbody > tr > td > form > button");
    const btnOk = document.querySelector("#main_body > div.swal2-container.swal2-center.swal2-shown > div > div.swal2-actions > button.swal2-confirm.swal2-styled");
    const btnTest = document.querySelector("#controlBotonSeccion184");
    //Values
    const consecutivo = document.querySelector("#Consecutivo_fic");
    const fechaIntervencion = document.querySelector("#FechaIntervencion").value;
    const sds_base_id = document.querySelector("#Id_Base").value;

    function saveProductivity() {
        let numFicha = document.querySelector("#Ficha_fic").value;
        const url = `https://www.trakio.pro/save_productivity_sds?id_sds=${sds_base_id}&no_ficha=${numFicha}&fecha_intervencion=${fechaIntervencion}`;
        //const url = `http://127.0.0.1:8000/save_productivity_sds?id_sds=${sds_base_id}&no_ficha=${numFicha}&fecha_intervencion=${fechaIntervencion}`;

        // Use the Fetch API to make the GET request
        fetch(url)
            .then(response => {
                if (!response.ok) {
                    throw new Error('La ficha no está en recepciones');
                }
                return response.json();
            })
            .then(data => {
                btnGuardar.disabled = false;
            })
            .catch(error => {
                btnGuardar.disabled = true;
                //alert(error.message);
            });
    }

    consecutivo.addEventListener("change", saveProductivity);
    btnOk.addEventListener("click", saveProductivity);
    btnGuardar.addEventListener("click", saveProductivity);

    function tiempoTotalFicha() {
        const start = localStorage.getItem('initHour');
        const end = new Date().getTime();
        let diff = (end - start) / 3600000;

        if(localStorage.removeItem('initHour')){
            localStorage.removeItem('initHour')
        }

        console.log("Total tiempo ficha", diff);
        return diff.toFixed(2);
    }

    if (btnEditReg) {
        btnEditReg.addEventListener("click", function () {
            const currentDateTime = new Date().toLocaleString();
            localStorage.setItem("initHour", currentDateTime);
        });    
    }else{
        localStorage.setItem("initHour", "No hay botón para editar registro");
    }

    btnBack.addEventListener("click", tiempoTotalFicha);
    btnTest.addEventListener("click", tiempoTotalFicha);

    const currentDateTime = new Date().toLocaleString();
    console.log(currentDateTime);

} catch (error) { }
