import Helper from "../Clases/Helper.js";

export default class MascotaVerde extends Helper{
    #inputsSelectors;
    #form;

    constructor (){
        super();
        this.#inputsSelectors = {
            page_1 : {
                type_number: ["6964", "6972", "6977", "6982", "6984", "10319", "10320"],
                required: ["6956", "6955", "6957", "6959", "6960", "7055"]
            },
        }

        this.#form = document.getElementById('formularioNuevoFormato');
    }

    selectPage(){
        const sectionId = this.#form.querySelectorAll('input[name="Id_seccion"]')[0]?.value;
        
        // EJECUCIÓN DE PÁGINA CORRESPONDIENTE
        const sectionMap = {
            "127": () => this.firstPage(),
            "128": () => this.secondPage(),
        };
    
        const action = sectionMap[sectionId];
        
        if (action) action();
        this.inputsIndicators();
    }

    firstPage(){
        this.type(this.#inputsSelectors.page_1?.type_number, "number");
        this.required(this.#inputsSelectors.page_1?.required);
        this.saveProfessionals();
    }

    saveProfessionals(){
        const inputsIdProfesionals = [
            "valorControl7055",
            "valorControl7056",
            "valorControl7057",
            "valorControl7058",
            "valorControl7059",
            "valorControl7060",
            "valorControl7061",
            "valorControl7062",
            "valorControl7063",
            "valorControl7064",
            "valorControl7065",
            "valorControl7066",
        ];

        this.#form.addEventListener('change', (e) => {
            const input = e.target;
            if (inputsIdProfesionals.includes(input.id)) {
                const index = inputsIdProfesionals.findIndex((id) => id === input.id) + 1;
                const sessionNumber = index%2 !== 0 ? (index+1)/2 : index/2;                
                localStorage.setItem(`p${index % 2 === 0 ? 2 : 1}s${sessionNumber}`, input.value);
            }
        })
    }

    secondPage(){
        this.initialPoblacionalData();
        this.getProfesionalsSessions();
    }

    initialPoblacionalData(){
        const docType = document.getElementById("valorPoblacionalTipoDocumento");
        docType.value ||= '60';

        const pdi = document.getElementById("valorPoblacionalPoblacionDiferencialInclusion");
        pdi.value ||= '2620';

        const etnia = document.getElementById("valorPoblacionalEtnia");
        etnia.value ||= '84';

        const estadoCivil = document.getElementById("valorPoblacionalEstadoCivil");
        estadoCivil.value ||= '78';

        const disabledDocsMV = () => {
            const docNoMascota = ["59", "61", "62", "65"];
            this.disabledOptionsFromSelect(
                "valorPoblacionalTipoDocumento",
                docNoMascota,
                true
            );
        }

        disabledDocsMV();
    }

    getProfesionalsSessions(){
        const inputsIdProfesionals = [
            "valorControl7008",
            "valorControl7009",
            "valorControl7017",
            "valorControl7018",
            "valorControl7026",
            "valorControl7027",
            "valorControl7035",
            "valorControl7036",
            "valorControl7044",
            "valorControl7045",
            "valorControl7053",
            "valorControl7054",
        ];

        inputsIdProfesionals.forEach((id, index) => {
            const input = document.getElementById(id);
            if (input) {
                if (!input.disabled ) {
                    const sessionNumber = (index + 1) % 2 !== 0 ? Math.ceil((index + 1) / 2) : Math.ceil(index / 2);
                    const profesionalNumber =  (index + 1) % 2 !== 0 ? 1 : 2;
                    
                    input.value ||= localStorage.getItem(`p${profesionalNumber}s${sessionNumber}`) || '';
                }
            }
        });

        this.inputsIndicators();
    }
    
}