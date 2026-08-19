import SelectSearch from '../Clases/SelectSearch.js';
import Modal from '../Clases/Modal.js';
import Helper from '../Clases/Helper.js';

export default class Sesiones_colectivas extends Helper {
    constructor() {
        super();
        this.camposRequeridosSC = {
            pagina1: ['12', '14'],
            pagina3: ['52', '50'],
            pagina2: ['46', '44'],
        };

        this.buscar = new SelectSearch();
        this.modal = new Modal();

        // Validación inicial
        if (!this.validarpaginaunoSC()) {
            return false
        }

        if (!this.buscar.insert(['9', '10'])) {
            console.log('Error al crear el buscador.');
            return false;
        }
    }

    validarpaginaunoSC() {
        const requeridosCamposSC = this.camposRequeridosSC.pagina1
        const esValidoSC = this.required(requeridosCamposSC);
        if (!esValidoSC) {
            const requeridosCamposSC3 = this.camposRequeridosSC.pagina3
            const esValidoSC3 = this.required(requeridosCamposSC3);
            if (!esValidoSC3) {
                const requeridosCamposSC2 = this.camposRequeridosSC.pagina2
                const esValidoSC2 = this.required(requeridosCamposSC2);
                if (!esValidoSC2) {
                    return false
                }else{
                    this.detectarNodeProcesos();      
                }
            }else{
                this.cargarsesionesModulos();
            }
        }
        this.agregarEventosFormularioSC();
        return true;
    }

    agregarEventosFormularioSC() {
        const formularioSM = document.getElementById('formularioNuevoFormato');
        if (!formularioSM) {
            console.error('Error: Formulario no encontrado.');
            return
        }

        formularioSM.addEventListener('change', (event) => {
            const { id, value } = event.target;
            const eventosSC = {
                "valorControl14": () => this.barrios("14", ['16']),
                "valorControl38": () => this.SaveLocalStorage(value),
                "valorControl61": () => this.calcularEdad(value,['56']),
                "valorControl53": () => this.Gestacion(value,['60']),
            }
            if (eventosSC[id]) {
                eventosSC[id]();
            }
        })
    }

    SaveLocalStorage(v) {
        // guardar v en local storage
        localStorage.setItem('institucion', JSON.stringify(v));
    }

    Gestacion(v,g){
        console.log(v,g)
        const gestante = document.getElementById('valorControl'+ g);
        if (v == 68) {
            gestante.required = true;
        }else{
            gestante.value = "";
            gestante.readOnly = false;
        }
        

    }
    calcularEdad(p,e) {
        const perssonEdad = document.getElementById('valorControl'+ e).value

        // Convertir fecha de nacimiento (dd/mm/yyyy) a yyyy-mm-dd
        const [diaNa, mesNa, anioNa] = perssonEdad.split('/');
        const fechaNa = new Date(`${anioNa}-${mesNa}-${diaNa}`);
        const fechaIntStr = document.getElementById("FechaIntervencion").value;
        // Convertir fecha de intervención (dd/mm/yyyy) a yyyy-mm-dd
        const [diaInt, mesInt, anioInt] = fechaIntStr.split('/');
        const fechaInt = new Date(`${anioInt}-${mesInt}-${diaInt}`);
        if (isNaN(fechaNa.getTime()) || isNaN(fechaInt.getTime())) {
            console.log("Una o ambas fechas no son válidas");
            return;
        }
        // Calcular la diferencia en milisegundos
        const diffMs = fechaInt - fechaNa;
        // Convertir milisegundos a años
        const edad = diffMs / (1000 * 60 * 60 * 24 * 365.25);

        const institucionGuardada = localStorage.getItem('institucion'); // Recupera el string JSON
        if (institucionGuardada) {
            const institucion = JSON.parse(institucionGuardada); // Convierte a objeto/array
            const campo = document.getElementById("valorControl56")

            if (edad >= 24 && institucion == 1634 && p == 351 ) {
                this.modal.mostrarModal('Advertencia', 'La persona es meyor de 24 años no puede estar en Colegio');
                this.actualizarEstadoCampo(campo, true)

            }else{
                this.actualizarEstadoCampo(campo, false)
            }
            if (edad <= 14 && institucion == 1635) {
                this.actualizarEstadoCampo(campo, true)
                this.modal.mostrarModal('Advertencia', 'La persona es menor de 15 años no puede estar en Universidad');

            }else{
                this.actualizarEstadoCampo(campo, false)
            }
            if (edad >= 6 && institucion == 1633 && p == 351) {
                this.actualizarEstadoCampo(campo, true);
                this.modal.mostrarModal('Advertencia', 'La persona es mayor de 6 años no puede estar en jardín');
            }else{
                this.actualizarEstadoCampo(campo, false)
            }

        } else {
            console.log('No hay datos guardados en "institucion"');
        }

        if (edad <= 14) {
            const campoPoblacion = document.getElementById("valorControl61")
            campoPoblacion.value = 351
            this.actualizarEstadoCampo(campo, false)
        }
    }    
    detectarNodeProcesos(){
        const fila = document.querySelector("#main_body > div > div > main > div > div > div > div.panel-body > div:nth-child(5) > div > center > table > tbody > tr > td:nth-child(3) > table > tbody > tr");
        const celdas = fila.querySelectorAll("td");
        if(celdas < 1){
            return false
        }
        const candSesiones = celdas.length / 2
        console.log(candSesiones)
        localStorage.setItem('CantProcesosSesiones', JSON.stringify(candSesiones));
        retur
    }

    cargarsesionesModulos(){
        const CantProcesosStr = localStorage.getItem('CantProcesosSesiones'); 
        const CantProcesos = parseInt(CantProcesosStr, 10);
        const procesos = CantProcesos //4
        console.log(procesos)
        let camposModulos = ["3620","3621", "3622" , "3623", "3624","3625", "3626" , "3627", "3628", "3629", "7068", "7069", "7070", "7071", "7072", "7073", "7074", "7075" ];
        
        for (let index = 1; index <= procesos ; index++) {
            const campo = document.getElementById('valorControl'+camposModulos[index - 1]);
            if (campo) {
                if(campo.value == ""){
                    campo.value = 1;
                    this.actualizarEstadoCampo(campo, false)
                }else{
                    this.actualizarEstadoCampo(campo, false)
                }
            }
        }
    }
}

