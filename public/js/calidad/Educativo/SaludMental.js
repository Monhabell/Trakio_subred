import Listas from '../Clases/Listas.js';
import StateInput from '../Clases/StateInput.js';
import AttributeInput from '../Clases/AttributeInput.js';
import Poblacional from '../Clases/Poblacional.js';


export default class SaludMental {
    constructor() {

        this.camposRequeridos = {
            pagina1: ['12719', '12717', '12734'],
        };

        this.camposDisabled = {
            pagina1: ['12734'],
        }

        this.requeridos = new AttributeInput();
        this.estados = new StateInput();
        this.edad = new Poblacional();
        this.barrio = new Listas();

        if(!this.camposDisabledEtapaGestacion()){
            console.warn('No se desabilito el campo de etapa de gestacion');
        }
        // Validación inicial
        if (!this.validarPaginaSaludmental()) {
            console.log('Esperando validación de la siguiente página.');
            // return false;
        }

    }


    validarPaginaSaludmental() {

        const requeridosCampos = this.camposRequeridos.pagina1
        const esValido = this.requeridos.required(requeridosCampos);

        if (!esValido) {
            console.warn('Validación de página 1 fallida.');
            return false;
        }

        this.agregarEventosFormularioSM();
        return true;
    }

    agregarEventosFormularioSM() {


        const formularioSM =document.getElementById('formularioNuevoFormato');

        if (!formularioSM) {
            console.error('Error: Formulario no encontrado.');
            return 
        }

        formularioSM.addEventListener('change', (event) => {
            const { id, value } = event.target;

            const eventos = {
                "valorControl12727": () => this.validarEdadSM(value, ['null', '12719', '12717', '12728']),
                "valorControl12717": () => this.validarTipoDocumentoSL(value,['12719','12718']),
                "valorControl12712": () => this.validarCmpoTexto(event.target),
                "valorControl12713": () => this.validarCmpoTexto(event.target),
                "valorControl12714": () => this.validarCmpoTexto(event.target),
                "valorControl12715": () => this.validarCmpoTexto(event.target),
                "valorControl12720": () => this.sexo(value, ['12721']),
                "valorControl12738": () => this.barrio.barrios(value,['12743']),
             
            }
            if (eventos[id]) {
                eventos[id]();
            }
        });
    }

    camposDisabledEtapaGestacion (){
        
        const requeridosCampos = this.camposDisabled.pagina1
        const [etapaGestacion] = requeridosCampos.map(id => document.getElementById(`valorControl${id}`));

        const sexo = document.getElementById('valorControl12720')

        if (!sexo) {
            console.error('Error: Campo de etapa de gestación no encontrado.');
            return false;
        }

        if(sexo.value == 67){
            etapaGestacion.disabled = true;
            return true
        }

        return false
    }


    validarEdadSM(campoValue, camposDependientes) {
        
        const [estadoCivil, nacionalidad, tipoDocumento, edad] = camposDependientes.map(id => document.getElementById(`valorControl${id}`));

       if (camposDependientes[0] == null){
            estadoCivil = null
       }

        // Lógica condicional basada en la edad
        if (edad.value > 17) {
            this.edad.validarCondicionesMayores(estadoCivil, nacionalidad, tipoDocumento);
        } else if (edad.value > 6) {
            this.edad.validarCondicionesMenores(estadoCivil, nacionalidad, tipoDocumento, campoValue);
        } else {
            this.edad.validarCondicionesInfantiles(estadoCivil, nacionalidad, tipoDocumento);
        }
    }

    validarTipoDocumentoSL(campoValue, camposDependientes) {
        const [nacionalidadId, noDocumentoId] = camposDependientes.map(id => document.getElementById(`valorControl${id}`));
        
        if (!nacionalidadId || !noDocumentoId) {
            console.error('Campos para validación de documento no encontrados.');
            return;
        }
        if (campoValue == 59 || campoValue == 60 || campoValue == 61) {
            nacionalidadId.value = 50;
            Estados.actualizarEstadoCampo(nacionalidadId, false);
            nacionalidadId.style.pointerEvents = 'none';

            if (noDocumentoId.value.length !== 10) {
                Estados.actualizarEstadoCampo(noDocumentoId, true);
            } else {
                Estados.actualizarEstadoCampo(noDocumentoId, false);
            }
        } else {
            nacionalidadId.style.pointerEvents = '';
        }
    }

    validarCmpoTexto(campo) {
        // validar que el campo solo contierne letras no se aceptan espacios ni caracteres especiales
        if (!/^[a-zA-ZáéíóúñÁÉÍÓ����]+$/.test(campo.value)) {
            Estados.actualizarEstadoCampo(campo, true);
        } else {
            Estados.actualizarEstadoCampo(campo, false);
        }
    }

    sexo(value, campos) {

        const [generoId] = campos.map(id => document.getElementById(`valorControl${id}`));
        if (!generoId) return;

        const mapaGeneros = {
            67: 70,
            68: 71,
            69: 72
        };
    
        if (mapaGeneros[value] !== undefined) {
            generoId.value = mapaGeneros[value];
            Estados.actualizarEstadoCampo(generoId, false);
        } else {
            Estados.actualizarEstadoCampo(generoId, true);
        }
    }




    

    

    

}

