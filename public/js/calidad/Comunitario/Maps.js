import Helper from "../Clases/Helper.js";
import Poblacional from "../Clases/Poblacional.js";

export default class Maps extends Helper{
    #inputsSelectors;
    #form;

    constructor(){
        super();
        this.#inputsSelectors = {
            page_1 : {
                required: [
                    "9912", //Motivo ingreso
                    "9978", // Resultado VIH
                    "9981", // Resultado sífilis
                    "9983", // Resultado hepatitis B
                    "9984", // Sintomático respiratorio
                    "9993"  // Tipo de familia
                ],
                type_number: [
                    "9924",
                    "9930",
                    "9934",
                    "9945",
                    "9946"
                ]
            },
            page_2 : {
                required: [
                    "9994", "9995", "9996", "9997",
                    "9998", "10000", "10001", "10002",
                    "10003", "10004", "10005", "10006",
                    "10007", "10008", "10009", "10010",
                    "10011", "10012", "10013", "10014", 
                    "10015", "10016", "10017", "10018",
                    "10019", "10020", "10021", "10022",
                    "10023", "10024", "10025", "10026",
                    "10027", "10028", "10029"
                ]
            },
            page_3 : {                
                required: [
                    "10186",
                    "10187",
                    "10188"
                    
                ]
            },
        };

        this.#form = document.getElementById('formularioNuevoFormato');
    }

    selectPage(){
        const sectionId = this.#form.querySelectorAll('input[name="Id_seccion"]')[0]?.value;
    
        // EJECUCIÓN DE PÁGINA CORRESPONDIENTE
        const sectionMap = {
            "178": () => this.firstPage(),
            "179": () => this.secondPage(),
            "180": () => this.thirdPage(),
        };
    
        const action = sectionMap[sectionId];
        if (action) action();
    }

    firstPage(){
        this.validatePoblational();
        this.eps("9910", "9909");
        this.type(this.#inputsSelectors.page_1.type_number, "number");
        this.barrios('9919', '9921');
        this.required(this.#inputsSelectors.page_1.required);
        this.defaultValuesAlerts();
    }

    defaultValuesAlerts(){
        const container = document.querySelector("#formularioNuevoFormato > div:nth-child(18)");
        Array.from(container.querySelectorAll('select')).forEach((select, index) => {
            if (index === 7) {
                select.disabled = true;
            }else if(select.value === ''){
                const options = Array.from(select.options);
                options[options.length - 1].selected = true;    
            }
        });
    }

    secondPage(){
        this.secondPageDeafultValues();
        this.required(this.#inputsSelectors.page_2?.required);
    }

    secondPageDeafultValues(){
        const initValuesSqr = () => {
            const container = document.querySelector("#formularioNuevoFormato > div:nth-child(14)");
            const containerSelects = container.querySelectorAll('select');
            Array.from(containerSelects).slice(0, 30).map(select => select.value ||= "NO");
        }

        const initValuesApgar = () => {
            const container = document.querySelector("#formularioNuevoFormato > div:nth-child(12)");
            const containerSelects = container.querySelectorAll('select');
        
            Array.from(containerSelects).slice(0, 5).forEach(select => {
                const options = Array.from(select.options).map(opt => opt.value);
                if (options.includes("0 - Nunca")) {
                    select.value ||= "0 - Nunca";
                } else if (options.includes("0 -Nunca")) {
                    select.value ||= "0 -Nunca";
                }
            });
        };

        initValuesSqr();
        initValuesApgar();
    }


    thirdPage(){
        this.required(this.#inputsSelectors.page_3.required);
    }

    validatePoblational(){
        const poblacional = new Poblacional({
            typeDocId: '9898',
            nationalityId: '9900',
            sexInputId: '9903',
            genderInputId: '9904',
            pdiInputId: '9906',
            categoryDisabilityId: '11502',
            dateBirthId: '9902'
        });

        poblacional.validateNationality();
        poblacional.sexGender(true);
        poblacional.validatePdi();
        poblacional.initialValues({
            '9906': "2620",
            '9905': "84",
            '9913': "57",
            '9947': "NO",
            '9949': "NO",
        });
        poblacional.validateAge();
    }
}