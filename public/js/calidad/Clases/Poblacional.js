import Helper from './Helper.js';

export default class Poblacional extends Helper{
    #inputsIdsNumber;

    /**
     * 
     * @param {Object} inputsIdsNumber
     * @param {string} inputsIdsNumber.typeDocId
     * @param {string} inputsIdsNumber.nationalityId
     * @param {string} inputsIdsNumber.sexInputId
     * @param {string} inputsIdsNumber.pdiInputId
     * @param {string} inputsIdsNumber.genderInputId
     * @param {string} inputsIdsNumber.categoryDisabilityId
     * @param {string} inputsIdsNumber.dateBirthId
     */
    
    constructor(inputsIdsNumber = {}){
        super();
        this.#inputsIdsNumber = inputsIdsNumber;
    }

    get typeDocInput(){
        return document.getElementById(`valorControl${this.#inputsIdsNumber.typeDocId}`);
    }

    get nationalityInput(){
        return document.getElementById(`valorControl${this.#inputsIdsNumber.nationalityId}`);
    }

    get isColombian(){
        return this.nationalityInput.value === "Colombia" || this.nationalityInput.value === "50";
    }

    get sexInput(){
        return document.getElementById(`valorControl${this.#inputsIdsNumber.sexInputId}`);
    }

    get genderInput(){
        return document.getElementById(`valorControl${this.#inputsIdsNumber.genderInputId}`);
    }

    get pdiInput(){
        return document.getElementById(`valorControl${this.#inputsIdsNumber.pdiInputId}`);
    }

    get categoryDisabilityInput(){
        return document.getElementById(`valorControl${this.#inputsIdsNumber.categoryDisabilityId}`);
    }

    get dateBirthInput(){
        return document.getElementById(`valorControl${this.#inputsIdsNumber.dateBirthId}`);
    }

    get selectedValuesPdi(){
        return Array.from(this.pdiInput.selectedOptions).map(
            (opt) => opt.value
        );
    }

    /**
     * Validar condiciones para mayores de 17 años.
     */
    validarCondicionesMayores(estadoCivil, nacionalidad, tipoDocumento) {
        if(estadoCivil != null ){
            this.actualizarEstadoCampo(estadoCivil, estadoCivil.value == 78);
        }
        
        if (nacionalidad.value == 50) {
            this.actualizarEstadoCampo(tipoDocumento, tipoDocumento.value != 59);
        }
    }

    /**
     * Validar condiciones para menores entre 7 y 17 años.
     */
    validarCondicionesMenores(estadoCivil, nacionalidad, tipoDocumento, edad) {
        if(estadoCivil != null ){
            if (edad > 13) {
                this.actualizarEstadoCampo(estadoCivil, estadoCivil.value == 78);
            }
        }

        if (nacionalidad.value == 50) {
            this.actualizarEstadoCampo(tipoDocumento, tipoDocumento.value != 61);
        }
    }

    /**
     * Validar condiciones para menores de 7 años.
     */
    validarCondicionesInfantiles(estadoCivil, nacionalidad, tipoDocumento) {
        
        if(estadoCivil != null ){
            this.actualizarEstadoCampo(estadoCivil, estadoCivil.value != 78);
        }

        if (nacionalidad.value == 50) {
            this.actualizarEstadoCampo(tipoDocumento, tipoDocumento.value != 60);
        }
    }

    validateNationality(){
        const typeDocInput = this.typeDocInput;
        const nationalityInput =  this.nationalityInput;
        const pdiInput = this.pdiInput;
        const colombianTypesDoc = [
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

        [typeDocInput, nationalityInput].forEach(input => {
            input.addEventListener("change", (e) => {
                const target = e.target;
                const typeDoc = typeDocInput.value;

                if(target === typeDocInput){
                    nationalityInput.value = colombianTypesDoc.includes(typeDoc) ? "Colombia" : "";
                    this.actualizarEstadoCampo(typeDocInput, false);
                };
                
                if (target === nationalityInput) {
                    if (!this.isColombian && colombianTypesDoc.includes(typeDoc)) {
                        this.actualizarEstadoCampo(typeDocInput, true, "Tipo de documento no coincide con la nacionalidad");
                        typeDocInput.value = "";
                    }
                    else if(this.isColombian && !colombianTypesDoc.includes(typeDoc)) {
                        typeDocInput.value = ""
                    }else{
                        this.actualizarEstadoCampo(typeDocInput, false);
                    };
                }

                this.validatePdi();
            });
        });
    }

    /**
     * @param {string} sexInputId 
     * @param {string} genderInputId 
     * @param {boolean} includePdi
     * @param {string} pdiInputId
     * @returns {void}
     */
    sexGender(validatePdi = false) {
        const sexInput = this.sexInput;
        const genderInput = this.genderInput;
        let esTransgenero = false;

        if (sexInput.value === "3- Intersexual" || sexInput.value === "69") return;
    
        const validarPDI = () => {
            const pdiInput = this.pdiInput;
            pdiInput.value = "";
            esTransgenero = genderInput.value == "72" || genderInput.value == "3- Transgénero";

            if (esTransgenero && pdiInput) {
                const generoPdi = {
                    67: "117",
                    68: "116",
                    "1- Hombre": "11- Mujer Transgénero",
                    "2- Mujer": "10- Hombre Transgénero",
                };
        
                if (!pdiInput) return;
        
                const valueToAdd = generoPdi[sexInput.value];
        
                if (valueToAdd) {
                    const selectedValues = Array.from(pdiInput.selectedOptions).map(
                        (opt) => opt.value
                    );
        
                    if (!selectedValues.includes(valueToAdd)) {
                        selectedValues.push(valueToAdd);
                    }
        
                    Array.from(pdiInput.options).forEach((option) => {
                        option.selected = selectedValues.includes(option.value);
                    });
                }
            }
        };
    
        const sex = () => {
            const sexosGeneros = {
                "1- Hombre": "1- Masculino",
                "2- Mujer": "2- Femenino",
                "3- Intersexual": "3- Transgénero",
                67: "70",
                68: "71",
                69: "72",
            };
    
            genderInput.value = sexosGeneros[sexInput.value];
        };
    
        const gender = () => {
            const generosSexos = {
                "1- Masculino": "1- Hombre",
                "2- Femenino": "2- Mujer",
                70: "67",
                71: "68",
            };
            esTransgenero = genderInput.value === "72" || genderInput.value === "3- Transgénero";
            if (!esTransgenero) sexInput.value = generosSexos[genderInput.value];
            if (validatePdi) validarPDI();
        };
    
        sexInput.addEventListener("change", sex);
        genderInput.addEventListener("change", gender)
    }

    validateAge(){
        const dateBirthInput = this.dateBirthInput;
        dateBirthInput.addEventListener("change", () => {
            const age = parseFloat(this.ageCalculate(dateBirthInput.value));            
            const typeDoc = this.typeDocInput.value;

            let isError = false;
            let message = "";

            const typeDocbyAge = {
                colombianAdults: [
                    "59", 
                    "65", 
                    "1- CC",
                    "7- Adulto sin ID."
                ],
                colombianChildrens: [
                    "60", "66",
                    "8- Menor sin ID.", "2- RC"
                ],
                colombianMinors: [
                    "61", "66", 
                    "3- TI", "8- Menor sin ID."
                ],
                foreignAdults: [
                    "62", "64",
                    "65", "1637", 
                    "1638", "1639", 
                    "1640", "2482", 
                    "4- CE", "6- Pasaporte", 
                    "7- Adulto sin ID.", "9- Carnet Diplomático", 
                    "10- Salvoconducto", "11- Permiso Especial de permanencia",
                    "12- Documento Nacional de Identidad (del país de origen)", "13- PPT Permiso por Protección Temporal"
                ],
                foreignMinors: [
                    "65", "66", 
                    "1637", "1639", 
                    "1640", "2482"
                ],
            }
            
            switch (true) {
                case age <= 7:
                    if (this.isColombian && !typeDocbyAge.colombianChildrens.includes(typeDoc)) {
                        isError = true;
                        message = "Edad no válida para el tipo de documento";
                    }

                    if (!this.isColombian && !typeDocbyAge.foreignMinors.includes(typeDoc)) {
                        isError = true;
                        message = "Edad no válida para el tipo de documento";
                    }
                    
                    break;
                
                case age < 18:
                    if (this.isColombian && !typeDocbyAge.colombianMinors.includes(typeDoc)) {
                        isError = true;
                        message = "Edad no válida para el tipo de documento";
                    }

                    if (!this.isColombian && !typeDocbyAge.foreignMinors.includes(typeDoc)) {
                        isError = true;
                        message = "Edad no válida para el tipo de documento";
                    }
            
                    break;
                
                case age >= 18:
                    if (this.isColombian && !typeDocbyAge.colombianAdults.includes(typeDoc)) {
                        isError = true;
                        message = "Edad no válida para el tipo de documento";
                    }

                    if (!this.isColombian && !typeDocbyAge.foreignAdults.includes(typeDoc)) {
                        isError = true;
                        message = "Edad no válida para el tipo de documento";
                    }
                    break;

                default:
                    break;
            }
            
            this.actualizarEstadoCampo(dateBirthInput, isError, message);
        });
    }

    /**
     * 
     * @description Valida PDI contra nacionalidad y categoría discapacidad
     */
    validatePdi(){        
        const nationalityInput =  this.nationalityInput;
        const isPdiMultiple = this.pdiInput.multiple;
        const pdiInput = this.pdiInput;
        const foreignValuePdi = "2618";
        const valueNoAplicaPdi = "2620";

        if (nationalityInput.value === "") return;

        const withNationality = () => {
            
            if (isPdiMultiple) {
                const noAplicaIndex = this.selectedValuesPdi.indexOf(valueNoAplicaPdi);
                
                if (noAplicaIndex !== -1) {
                    this.selectedValuesPdi.splice(noAplicaIndex, 1);
                }
        
                if (!this.selectedValuesPdi.includes(foreignValuePdi)) {
                    this.selectedValuesPdi.push(foreignValuePdi);
                }
        
                pdiInput.value = [...this.selectedValuesPdi];
            } else {
                pdiInput.value ||= (!this.isColombian && pdiInput.value === valueNoAplicaPdi)
                    ? foreignValuePdi
                    : valueNoAplicaPdi;
            }
        };        

        const withCategoryDisability = () => {
            const categoryInput = this.categoryDisabilityInput;
            let disabledNoAplica = false;

            const valoresDiscapacidadPdi = ["2- Discapacidad", "108"];
            let valueCategoriaNoAplica = "3822";
            
            if (isPdiMultiple) {
                
                if(this.selectedValuesPdi.some(value => valoresDiscapacidadPdi.includes(value))){
                    valueCategoriaNoAplica = "";
                    disabledNoAplica = true;
                }
            }else{                                
                if (valoresDiscapacidadPdi.includes(pdiInput.value)) {
                    valueCategoriaNoAplica = "";
                    disabledNoAplica = true;
                }
            }
            
            if (categoryInput) {
                categoryInput.value = valueCategoriaNoAplica;
                categoryInput.disabled = false;
            }

            const options = categoryInput.querySelectorAll("option");
            [...options].map((option) => {
                if (option.value == "3822") {
                    option.disabled = disabledNoAplica;
                }
            });
        }

        withNationality();
        withCategoryDisability();
        // this.pdiInput.addEventListener("change", withCategoryDisability);
    }

    /**
     * 
     * @param {Object} inputsValues 
     */
    initialValues(inputsValues = {}) {
        Object.entries(inputsValues).forEach(([inputId, value]) => {
            const input = document.getElementById(`valorControl${inputId}`);
            if (input) input.value ||= value;
            else console.warn(`El campo con ID valorControl${inputId} no existe en el DOM.`);
        });
    }    
}