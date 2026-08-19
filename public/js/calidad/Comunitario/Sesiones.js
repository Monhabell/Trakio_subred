import SelectSearch from '../Clases/SelectSearch.js';
import AttributeInput from '../Clases/AttributeInput.js';

export default class sesiones {
    constructor() {
        this.buscar = new SelectSearch();
        this.requeridos = new AttributeInput();
        this.baseInput = document.getElementById('Id_Base');

        // Validación de base rápida
        if (!this.baseInput || this.baseInput.value !== "113") return;

        this.init();
    }

    async init() {
        try {
            const modButton = await import('../Clases/ButtonNewRegister.js');
            this.ButtonValidator = modButton.default;

            const modDoc = await import('../Clases/ColombianDocumentValidator.js');
            this.ColombianDocumentValidator = modDoc.default;

            const modCond = await import('../Clases/FormDependencyManager.js');
            this.CondicionalValidate = modCond.default;

            const modHigh = await import('../Clases/RequiredHighlighter.js');
            this.Highlighter = modHigh.default;

            new this.Highlighter({
                colorRequired: "rgba(255, 255, 0, 0.1)", // Amarillo muy sutil
                colorValid: "#ffffff"
            })

            const Manzana = document.getElementById("valorControl19255");
            const canalizacion = document.getElementById("valorControl19404");

            if (Manzana) this.pag1();
            if (canalizacion) this.pag2();

        } catch (error) {
            console.error("❌ Error inicializando sesiones:", error);
        }
    }

    pag1() {

        console.log("Iniciando validación Página 1");
        new this.ButtonValidator({
            inputId: "valorControl19255"
        });


        // new this.CondicionalValidate({
        //     triggerId: "valorControl19266",
        //     activeValue: ["35", "36", "37"],
        //     groupA: ["valorControl19232"]
        // });


        const requeridosCampos = ['19255', '19232','19255'];
        this.requeridos.required(requeridosCampos);

        const camposParaBloquear = ['19224', '19225', '19226', '19227', '19229', '19230']; // IDs de los controles
        this.requeridos.bloquearCamposPermanente(camposParaBloquear);

        const camposFechas = ["valorControl19263", "valorControl19284", "valorControl19304", "valorControl19324"];
        const campoReferencia = "FechaIntervencion"; // Fecha de intervención

        const campoConactenadodirect = "valorControl19261"; // Campo que se llenará con la fecha concatenada
        const urbana = document.getElementById("valorControl19233");

        urbana.addEventListener("change", () => {
            const valorUrbana = urbana.value;
            if (valorUrbana === "58") {

                document.getElementById(campoConactenadodirect).disabled = true;
                document.getElementById(campoConactenadodirect).value = ""; // Limpiar el valor si se deshabilita
            } else {
                document.getElementById(campoConactenadodirect).disabled = false;
            }
        });


        this.requeridos.fechasInput(camposFechas, campoReferencia);


    }

    pag2() {
        console.log("Iniciando validación Página 2");
        new this.ButtonValidator({
            inputId: "valorControl19404"
        });


        new this.ColombianDocumentValidator({
            tipoDocId: "valorControl19387",
            numeroDocId: "valorControl19388",
            nacionalidadId: "valorControl19394"
        });


        new this.CondicionalValidate({
            dateId: "valorControl19393",
            triggerId: "valorControl19956",
            targetId: ["valorControl19391", "valorControl19392", "valorControl19390"],  // Campo afectado
            valueCamp: ["4028", "4020", '4513'],
            camptipoDocument: "valorControl19387",
            nacionalidadId: "valorControl19394", // value 50 colombiano
            isEdad: true
        });

        new this.CondicionalValidate({
            triggerId: "valorControl19389",
            activeValue: "67",
            input: ["valorControl19400"],
            valueinputDefect: ["3785"],
            condicionalsimple: true
        });

        const misCampos = [
            '19385', '19386'
        ].filter(Boolean);

        this.requeridos.configurarValidacionNombres(misCampos);
    }
}