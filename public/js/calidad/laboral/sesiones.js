import SelectSearch from '../Clases/SelectSearch.js';
import AttributeInput from '../Clases/AttributeInput.js';

export default class sesiones {
    constructor() {
        if (!this.baseInput || this.baseInput.value !== "114") return;
        this.buscar = new SelectSearch();
        this.requeridos = new AttributeInput();
        this.baseInput = document.getElementById('Id_Base');

        // Validación de base rápida

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

            const Manzana = document.getElementById("valorControl19511");
            const canalizacion = document.getElementById("valorControl19660");

            if (Manzana) this.pag1();
            if (canalizacion) this.pag2();

        } catch (error) {
            console.error("❌ Error inicializando sesiones:", error);
        }
    }

    pag1() {
        console.log("Iniciando validación Página 1");
        new this.ButtonValidator({
            inputId: "valorControl19484"
        });

        this.buscar.insert(['19492']);

        const camposFechas = ["valorControl19519", "valorControl19540", "valorControl19560", "valorControl19580"];
        const campoReferencia = "FechaIntervencion"; // Fecha de intervención

        this.requeridos.fechasInput(camposFechas, campoReferencia);

    }

    pag2() {

        //this.buscar.insert(['19650']);

        console.log("Iniciando validación Página 2");
        new this.ButtonValidator({
            inputId: "valorControl19660"
        });

        new this.ColombianDocumentValidator({
            tipoDocId: "valorControl19643",
            numeroDocId: "valorControl19644",
            nacionalidadId: "valorControl19650"
        });

        new this.CondicionalValidate({
            dateId: "valorControl19649",    // Disparador: Fecha Nacimiento
            triggerId: "valorControl19958", // Disparador: Edad
            targetId: ["valorControl19647", "valorControl19648"],  // Campo afectado
            valueCamp: ["4028", "4020"],
            camptipoDocument: "valorControl19643",
            nacionalidadId: "valorControl19650", // value 50 colombiano
            isEdad: true
        });



        new this.CondicionalValidate({
            triggerId: "valorControl19652",
            activeValue: "100",
            groupA: ["valorControl22674"]
        });

        new this.CondicionalValidate({
            triggerId: "valorControlHidden19651",
            activeValue: "79",
            groupA: ["valorControl19652", "valorControl22674"],

        });

        const misCampos = [
            '19641', '19642'
        ].filter(Boolean);

        this.requeridos.configurarValidacionNombres(misCampos);

    }
}