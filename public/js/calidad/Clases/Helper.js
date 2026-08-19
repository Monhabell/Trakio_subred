import Listas from './Listas.js';

export default class Helper extends Listas{
    constructor(){
        super();
        this.currentMonth =  new Date().getMonth();
        this.interventionDate = document.querySelector("#FechaIntervencion").value;
        this.currentUser = document.querySelector('input[name="paginaUsuario"').value;
    }

    toggleDirectionInputs(zoneInputId, ruralInputsId = [], urbanInputsId = [], requiredUrbanInputsId = false) {
        const inputZona = document.getElementById(`valorControl${zoneInputId}`);
        if (!inputZona) return console.warn(`Input con ID valorControl${zoneInputId} no encontrado`);
    
        const actualizarInputs = (esRural) => {
            const toggleInputs = (ids, activar) => {
                ids.forEach(inputId => {
                    const input = document.getElementById(`valorControl${inputId}`);
                    if (input) {
                        input.value = activar ? input.value : "";
                        input.disabled = !activar;
                        if (requiredUrbanInputsId && ids === urbanInputsId) {
                            input.required = activar
                        } else if (ids === idsRurales) {
                            input.required = activar
                        }
                    }
                });
            };
    
            toggleInputs(ruralInputsId, esRural);
            toggleInputs(urbanInputsId, !esRural);
        };
    
        const validarZona = () => {
            const esRural = inputZona.value === "58" || inputZona.value === "2- Rural";
            actualizarInputs(esRural);
        };
    
        validarZona();
        inputZona.addEventListener("change", validarZona);
    }

    disabledInputIfFieldIgualTo(
        inputIdToDisabled,
        inputValue,
        igualTo = "NO"
    ) {
        const input = document.getElementById(inputIdToDisabled);
        input.disabled = inputValue == igualTo;
    }

    formatDateToYMD(date) {
        const [d, m, y] = date.split('/');
        return new Date(`${y}-${m}-${d}`);
    }

    /**
     * 
     * @param {date} birthDate 
     * @returns {number}
     * @description Calcula la edad en años a partir de una fecha de nacimiento y la fecha de intervención.
     */

    ageCalculate(birthDate) {
        const initDate = new Date(this.formatDateToYMD(this.interventionDate));
        const endDate = new Date(this.formatDateToYMD(birthDate));

        if (isNaN(initDate) || isNaN(endDate)) return null;
    
        const diferenciaMs = initDate - endDate;
        const edadEnAnios = diferenciaMs / (1000 * 60 * 60 * 24 * 365.25);
        
        return Math.round(edadEnAnios * 10) / 10;
    }
    
}