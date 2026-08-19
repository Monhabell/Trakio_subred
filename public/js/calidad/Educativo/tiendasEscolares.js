import StateInput from '../Clases/StateInput.js';
import AttributeInput from '../Clases/AttributeInput.js';
import Poblacional from '../Clases/Poblacional.js';
import SelectSearch from '../Clases/SelectSearch.js';
import Listas from '../Clases/Listas.js';
import String from '../Clases/String.js';

export default  class tiendas_escolares {
    constructor() {
        this.camposRequeridos = {
            pagina1: ['5963','5927', '5967', '5971', '5975', '5979', '5983', '5987', '6861', 
                '5991', '5995', '5999', '6002', '6005', '6008', '6011', '6014', '6017',
                '6020', '60223', '6026', '6029', '6032', '6035', '6038', '6041', '6044'
            ]
        };

        this.requeridos = new AttributeInput();
        this.estados = new StateInput();
        this.edad = new Poblacional();
        this.barrio = new Listas();
        this.buscar = new SelectSearch();
        this.texto = new String();


        
        if(!this.buscar.insert(['5929', '5928'], false)){
            console.log('Error al crear el buscador.');
            return false; 
        }

        if(!this.validarTiendas()){
            console.log('Error al validar los datos.');
            return false;
        }

        if(!this.texto.pattern(['6048', '6049', '6050', '6051', '6054', '6055', 
            '6056', '6057', '8502','8503','8504', '8505'])){
            console.warn('Error: No se encontró el texto con el código 6048.');
            return false;
        }
    }

    validarTiendas(){
        console.log("Validación de Tiendas iniciado");
        const requeridosCampos = this.camposRequeridos.pagina1
        const esValido = this.requeridos.required(requeridosCampos);

        if (!esValido) {
            console.warn('Validación de página 1 fallida.');
            return false;
        }

        this.eventosTiendas();
        return true;

    }

    eventosTiendas(){
        console.log('Agregando eventos al formulario.');

        const formularioSM =document.getElementById('formularioNuevoFormato');

        if (!formularioSM) {
            console.error('Error: Formulario no encontrado.');
            return 
        }

        formularioSM.addEventListener('change', (event) => {
            const { id, value } = event.target;

            const eventos = {
                "valorControl5937": () => this.barrio.barrios(value,['5939']),
                "valorControl5964": () => this.controlFechas(['5964','5963'])
            }
            if (eventos[id]) {
                eventos[id]();
            }

        });
    }

    controlFechas(campos){
        const [fecha_vicita_2 , fecha_visita_1] = campos.map(id => document.getElementById(`valorControl${id}`));

        if (!fecha_visita_1) {
            console.error('Error: Campo de fecha no encontrado.');
            return;
        }

        if(fecha_vicita_2.value < fecha_visita_1.value){
            alert('La fecha de visita2 no pude ser menor a la fecha vicita 1');
            Estados.actualizarEstadoCampo(fecha_vicita_2, true);
            
            return false;
        }else{
            Estados.actualizarEstadoCampo(fecha_vicita_2, true);
            const requeridos = 
                ['5968','5972','5976','5980','5984',
                '5988','6862','5992','5996','6000',
                '6003','6006','6009','6012','6015',
                '6018','6021','6024','6027','6030',
                '6033','6036','6039', '6042','6045'
            ]

            const esValido = this.requeridos.required(requeridos);

            if (!esValido) {
                console.warn('Validación de página 1 fallida.');
                return false;
            }
            
        }

    }

}