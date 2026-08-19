import Helper from "../Clases/Helper.js";
import Poblacional from "../Clases/Poblacional.js";

export default class SesionesColectivas extends Helper{

    #inputsSelectors;
    #form;
    #usersByTypist;

    constructor(){
        super();
        this.#inputsSelectors = {
            page_1 : {
                type_number: ["14155", "14161", "14165", "14168", "14170"]
            },
            page_2 : {
                type_number: ["14494", "14495", "14496"]
            },
            page_3 : {
                date_sesions: [
                    "14514",
                    "14518",
                    "14522",
                    "14526",
                    "14530",
                    "14534",
                    "14538",
                    "14542",
                    "14546",
                    "14550",
                    "14554",
                    "14558",
                    "14562",
                    "14566",
                    "14570",
                    "14574",
                ],
                session_attendance: [
                    "14513",
                    "14517",
                    "14521",
                    "14525",
                    "14529",
                    "14533",
                    "14537",
                    "14541",
                    "14545",
                    "14549",
                    "14553",
                    "14557",
                    "14561",
                    "14565",
                    "14569",
                    "14573",
                ],
                professional_profile: [
                    "14515",
                    "14519",
                    "14523",
                    "14527",
                    "14531",
                    "14535",
                    "14539",
                    "14543",
                    "14547",
                    "14551",
                    "14555",
                    "14559",
                    "14563",
                    "14567",
                    "14571",
                    "14575",
                ],
                typist_name: [
                    "14516",
                    "14520",
                    "14524",
                    "14528",
                    "14532",
                    "14536",
                    "14540",
                    "14544",
                    "14548",
                    "14552",
                    "14556",
                    "14560",
                    "14564",
                    "14568",
                    "14572",
                    "14576",
                ],
                required: [
                    "14497",
                    "14498",
                    "14499",
                    "14500",
                    "14502",
                    "14503",
                    "14504",
                    "14507",
                    "14508",
                    "14509",
                    "14511",
                ]
            },
        };

        this.#usersByTypist = {
            'argarcia': 'FELIPE ARIAS',
            'kasolano': 'KEVIN SOLANO',
            'jcrojas': 'JENNY ROJAS',
            'adpinzon': 'ANDRES PINZON',
            'aljimenez': 'ANGIE JIMENEZ',
            'jaospina': 'CAMILA YOPASA',
            'gagomez': 'GINA GOMEZ',
            'dcgaray': 'CAROLINA GARAY'
        }
        this.#form = document.getElementById('formularioNuevoFormato');
    }

    selectPage(){
        const sectionId = this.#form.querySelectorAll('input[name="Id_seccion"]')[0]?.value;
    
        // EJECUCIÓN DE PÁGINA CORRESPONDIENTE
        const sectionMap = {
            "238": () => this.firstPage(),
            "241": () => this.secondPage(),
            "243": () => this.thirdPage(),
        };
    
        const action = sectionMap[sectionId];
        if (action) action();
    }

    firstPage(){
        this.barrios('14149','14151');
        this.type(this.#inputsSelectors.page_1?.type_number, "number");
        //Temporal
        const tipoSesion = document.getElementById("valorControl17208");
        tipoSesion.required = false;
    }

    secondPage(){
        this.type(this.#inputsSelectors.page_2?.type_number, "number");
        this.personsNumber();
    }

    personsNumber (){
        const container = document.querySelector("#formularioNuevoFormato > div:nth-child(12)");
        const componentToPersons = "351";
        const processToPersons = new Set(["519", "520", "521"]);

        const toggledInputs = (disabled) => {
            
            this.#inputsSelectors.page_2?.type_number.forEach((id) => {
                const input = document.getElementById(`valorControl${id}`);
                if (input) {
                    console.log(input.value);
                    
                    if (disabled) input.value = null;
                    input.readOnly = disabled;
                    input.required = !disabled;
                };
            });
        }

        const checkValues = () => {
            const component = document.getElementById('valorControl14491')?.value;
            const process = document.getElementById('valorControl14492')?.value;
            
            const disabled = !(componentToPersons === component && processToPersons.has(process));
            toggledInputs(disabled);
        }

        checkValues();
        container.addEventListener('change', checkValues);
    }

    thirdPage(){
        this.type(this.#inputsSelectors.page_3?.type_number, "number");
        this.required(this.#inputsSelectors.page_3?.required);
        this.digitadores(this.#inputsSelectors.page_3?.typist_name);
        this.controlInputsSesions();
        this.validatePoblational();
    }

    controlInputsSesions() {
        const inputsIdAttendance = this.#inputsSelectors.page_3?.session_attendance;
        
        inputsIdAttendance.forEach((id, key) => {
            const input = document.getElementById(`valorControl${id}`);
            if (input) {
                input.value ||= 0;
                input.addEventListener("change", () => this.disabledSesionsInputs());
            }
        });

        this.disabledSesionsInputs();
    }

    disabledSesionsInputs() {
        const allInputsIdSesions = this.#inputsSelectors.page_3?.date_sesions.concat(
            this.#inputsSelectors.page_3?.professional_profile,
            this.#inputsSelectors.page_3?.typist_name
        );

        const inputsIdAttendance = this.#inputsSelectors.page_3?.session_attendance;

        if (!Array.isArray(allInputsIdSesions) && !Array.isArray(inputsIdAttendance)) {
            console.warn("No se encontraron los inputs de sesiones o asistencia");
            return;
        }

        const inputsIds = allInputsIdSesions.map(
            (inputId) => `#valorControl${inputId}`
        );
        const inputs = document.querySelectorAll(inputsIds);

        const inputsIdsCondition = inputsIdAttendance.map(
            (inputId) => `#valorControl${inputId}`
        );
        const inputsCondition = [
            ...document.querySelectorAll(inputsIdsCondition),
        ];

        const camposPorSesion = Math.ceil(
            inputs.length / inputsCondition.length
        );

        [...inputs].forEach((input, key) => {
            if (input) {
                const conditionIndex = Math.floor(key / camposPorSesion);

                if (inputsCondition[conditionIndex]) {
                    input.disabled =
                        inputsCondition[conditionIndex].value == "0";
                    
                    if((key+1) % 3 === 0) {
                        input.value ||= input.disabled ? "" : this.#usersByTypist[this.currentUser]
                    }
                }
            }
        });
        
        const handleBlur = () => {
            inputs[0].value = this.interventionDate;
        };
        
        if (!inputs[0].disabled) {
            inputs[0].addEventListener("blur", handleBlur);
            handleBlur();
        }
    }

    validatePoblational(){
        const poblacional = new Poblacional({
            typeDocId: '14499',
            nationalityId: '14507',
            sexInputId: '14502',
            genderInputId: '14503',
            pdiInputId: '14509',
            categoryDisabilityId: '14511',
            dateBirthId: '14504'
        });

        poblacional.validateNationality();
        poblacional.sexGender(true);
        poblacional.validatePdi();
        poblacional.initialValues({
            '14511': "3822",
            '14508': "84"
        });
        poblacional.validateAge();
    }
}