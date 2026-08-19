import Helper from "../Clases/Helper.js";
import Modal from "../Clases/Modal.js";
import Poblacional from "../Clases/Poblacional.js";

export default class SpaciosDeBienestar extends Helper {
    #inputsSelectors;

    constructor(){
        super();
        this.#inputsSelectors = {
            page_1 : {
                required: ['15426']
            },
        }
    }

    get interventionTypeInputsId(){
        return {
            conInstitucion: [
                "valorControl15167", //Adolescentes
                "valorControl15168", //SRPA
                "valorControl15171", //PPL
                "valorControl15172", //IE
                "valorControl15173", //UN
            ],
            sinInstitcion: [
                "valorControl15169", //Alcohol
                "valorControl15170" //Jornadas
            ],
        };
    }

    get environmentInput(){
        return document.getElementById("valorControl15165");
    }

    get allInterventionTypeinputsId(){
        return [
            ...this.interventionTypeInputsId.conInstitucion,
            ...this.interventionTypeInputsId.sinInstitcion,
        ]
    }

    initValidate(){
        this.required(this.#inputsSelectors.page_1.required);
        this.interventionType();
        this.barrios('15200','15202');
        this.eps("15190", "15189");
        this.validatePoblational();
    }

    defaultValues(){
        const interventionType = () => {
            //Establecer comunitario por defecto
            if (this.environmentInput) this.environmentInput.value ||= "Comunitario";
                
            this.allInterventionTypeinputsId.forEach((inputId) => {
                const input = document.getElementById(inputId);
                input.value ||= "N/A";
            });
        }

        const alerts = () => {
            const container = document.querySelector(
                "#formularioNuevoFormato > div:nth-child(20)"
            );
            if (!container) {
                console.error("No se encontró el contenedor de alertas.");
                return;
            }
        
            const selects = container.querySelectorAll("select");
        
            selects.forEach((select) => {
                select.required = true;
                if (!select.value) {
                    const options = select.options;
                    options[options.length - 1].selected ||= true;
                }
            });
        }
        
        interventionType();
        alerts();
    }

    validateIntitution(){
        const container = document.querySelector(
            "#formularioNuevoFormato > div:nth-child(12)"
        );
        const institutionInput = document.getElementById("valorControl15166");        

        const validate = (activeInput = null) => {
            const containerSelects = [...container.querySelectorAll("select")];
            const camposRespuestaSi = new Set(
                containerSelects
                    .filter((input) => input.value == "Si")
                    .map((input) => input.id)
            );
            
            if (activeInput) {
                const respuestasSi = [...camposRespuestaSi];
                if (respuestasSi.length > 1) {
                    respuestasSi.map(id => document.querySelector(`#${id}`)).forEach(input => input.value = '');
                    new Modal().mostrarModal("Tipo de interveción incorrecto", "No puede haber más de un tipo de intervención");
                }
    
                const responsesNa = containerSelects.filter(select => select.value === 'N/A');
    
                if (responsesNa.length === this.allInterventionTypeinputsId.length) {                    
                    new Modal().mostrarModal("Tipo de interveción", "Debe seleccionar al menos un tipo de intervención");
                }
            }
           
            const requiereInstitucion =
                this.interventionTypeInputsId.conInstitucion.some((input) =>
                    camposRespuestaSi.has(input)
                );

            if (!requiereInstitucion) institutionInput.value = '';
            institutionInput.required = requiereInstitucion;
            institutionInput.readOnly = !requiereInstitucion;
        }

        container.addEventListener("change", (e) => {
            const activeInput = e.target;
            if (activeInput.matches("select")) {
                validate(activeInput);
            }
        });

        validate();
    }

    interventionType(){
        this.defaultValues();
        this.validateIntitution();
    }

    validatePoblational(){
        const poblacional = new Poblacional({
            typeDocId: '15178',
            nationalityId: '15180',
            sexInputId: '15183',
            genderInputId: '15184',
            pdiInputId: '15186',
            categoryDisabilityId: '15191',
            dateBirthId: '15182'
        });

        poblacional.validateNationality();
        poblacional.sexGender(true);
        poblacional.validatePdi();
        poblacional.initialValues({
            '15191': "3822"
        });
        poblacional.validateAge();
    }

}