import SelectSearch from '../Clases/SelectSearch.js';
import AttributeInput from '../Clases/AttributeInput.js';

export default class sesiones {
    constructor() {
        this.buscar = new SelectSearch();
        this.requeridos = new AttributeInput();
        this.baseInput = document.getElementById('Id_Base');

        // Validación de base rápida
        if (!this.baseInput || this.baseInput.value !== "107") return;

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

            const Manzana = document.getElementById("valorControl17378");
            const canalizacion = document.getElementById("valorControl17527");

            if (Manzana) this.pag1();
            if (canalizacion) this.pag2();

        } catch (error) {
            console.error("❌ Error inicializando sesiones:", error);
        }
    }

    pag1() {
        console.log("Iniciando validación Página 1");
        new this.ButtonValidator({
            inputId: "valorControl17378"
        });

        const camposFechas = ["valorControl17386", "valorControl17407", "valorControl17427", "valorControl17447" ];
        const campoReferencia = "FechaIntervencion"; // Fecha de intervención

        this.requeridos.fechasInput(camposFechas, campoReferencia);

        this.requeridos.required(['17355']);

    }

    pag2() {
        console.log("Iniciando validación Página 2");
        new this.ButtonValidator({
            inputId: "valorControl17527"
        });

        new this.ColombianDocumentValidator({
            tipoDocId: "valorControl17510",
            numeroDocId: "valorControl17511",
            nacionalidadId: "valorControl17517"
        });

        new this.CondicionalValidate({
            dateId: "valorControl17516",    // Disparador: Fecha Nacimiento
            triggerId: "valorControl19845", // Disparador: Edad
            targetId: ["valorControl17514", "valorControl17515"],  // Campo afectado
            valueCamp: ["4028", "4020"],
            camptipoDocument: "valorControl17510",
            nacionalidadId: "valorControl17517", // value 50 colombiano
            isEdad: true
        });

        new this.CondicionalValidate({
            triggerId: "valorControl17512",
            activeValue: "67",
            input: ["valorControl17523"],
            valueinputDefect: ["3785"],
            condicionalsimple: true
        });

        const misCampos = [
            '17508', '17509'
        ].filter(Boolean);

        this.requeridos.configurarValidacionNombres(misCampos);
    }
}