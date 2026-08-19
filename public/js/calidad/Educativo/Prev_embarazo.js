import StateInput from '../Clases/StateInput.js';
import AttributeInput from '../Clases/AttributeInput.js';
import Poblacional from '../Clases/Poblacional.js';

export default class PrevEmbarazo {
    constructor() {
        this.camposRequeridos = {
            pagina1: ['14214', '14215', '14220'],
        };

        this.requeridos = new AttributeInput();
        this.estados = new StateInput();
        this.edad = new Poblacional();

        // Validación inicial
        if (!this.validarPagina1()) {
            console.log('Esperando validación de la siguiente página.');
            // return false
        }
    }

    validarPagina1() {
        const requeridosCampos = this.camposRequeridos.pagina1
        const esValido = this.requeridos.required(requeridosCampos);

        if (!esValido) {
            console.warn('Validación de página 1 fallida.');
            return false;
        }

        this.agregarEventosFormulario();
        return true;
    }


    agregarEventosFormulario() {
        const formulario = document.getElementById('formularioNuevoFormato');
        if (!formulario) {
            console.error('Formulario no encontrado. No se pueden agregar eventos.');
            return;
        }

        formulario.addEventListener('change', (event) => {
            const { id, value } = event.target;

            // Manejar eventos específicos según el campo cambiado.
            const eventosEspecificos = {
                "valorControl14215": () => this.validarCampo(value, ['14216', '14217', '14218', '14219']),
                "valorControl14214": () => this.validarCampo(value, ['14205', '14206', '14207', '14208', '14209', '14210', '14211', '14212', '14213']),
                "valorControl14220": () => this.validarCampo(value, ['14221', '14222', '14223']),
                "valorControl14129": () => this.validarTipoDocumento(value, ['14135', '14128']),
                "valorControl14133": () => this.validarEdad(value, ['14132', '14135', '14129', '14590']),
                
            };

            if (eventosEspecificos[id]) {
                eventosEspecificos[id]();
            }
        });
    }


    validarCampo(campoValue, camposDependientes) {
        const habilitar = campoValue == 959;
        this.estados.toggleCampos(camposDependientes, habilitar); // abilitar o desabilitar campos 
    }


    validarTipoDocumento(campoValue, camposDependientes) {
        const [nacionalidadId, noDocumentoId] = camposDependientes.map(id => document.getElementById(`valorControl${id}`));
        
        if (!nacionalidadId || !noDocumentoId) {
            console.error('Campos para validación de documento no encontrados.');
            return;
        }
        if (campoValue == 59 || campoValue == 60 || campoValue == 61) {
            nacionalidadId.value = 50;
            estados.actualizarEstadoCampo(nacionalidadId, false);
            nacionalidadId.style.pointerEvents = 'none';

            if (noDocumentoId.value.length !== 10) {
                this.estados.actualizarEstadoCampo(noDocumentoId, true);
            } else {
                this.estados.actualizarEstadoCampo(noDocumentoId, false);
            }
        } else {
            nacionalidadId.style.pointerEvents = '';
        }
    }

    validarEdad(campoValue, camposDependientes) {
        const [estadoCivil, nacionalidad, tipoDocumento, edad] = camposDependientes.map(id => document.getElementById(`valorControl${id}`));

        if (!estadoCivil || !nacionalidad || !tipoDocumento || !edad) {
            console.error('Campos para validación de edad no encontrados.');
            return;
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

}
