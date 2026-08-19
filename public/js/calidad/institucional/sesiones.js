import SelectSearch from '../Clases/SelectSearch.js';
import AttributeInput from '../Clases/AttributeInput.js';

export default class sesiones {
    constructor() {
        this.buscar = new SelectSearch();
        this.requeridos = new AttributeInput();
        this.baseInput = document.getElementById('Id_Base');

        // Validación de base rápida
        if (!this.baseInput || this.baseInput.value !== "112") return;

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

            const Manzana = document.getElementById("valorControl18999");
            const canalizacion = document.getElementById("valorControl19148");

            if (Manzana) this.pag1();
            if (canalizacion) this.pag2();

        } catch (error) {
            console.error("❌ Error inicializando sesiones:", error);
        }
    }

    pag1() {
        console.log("Iniciando validación Página 1");
        new this.ButtonValidator({
            inputId: "valorControl18999"
        });

        const camposFechas = ["valorControl19007", "valorControl19028", "valorControl19048", "valorControl19068"];
        const campoReferencia = "FechaIntervencion"; // Fecha de intervención

        this.requeridos.fechasInput(camposFechas, campoReferencia);
    }

    pag2() {
        console.log("Iniciando validación Página 2");
        new this.ButtonValidator({
            inputId: "valorControl19148"
        });

        new this.ColombianDocumentValidator({
            tipoDocId: "valorControl19131",
            numeroDocId: "valorControl19132",
            nacionalidadId: "valorControl19138"
        });

        new this.CondicionalValidate({
            dateId: "valorControl19137",    // Disparador: Fecha Nacimiento
            triggerId: "valorControl19954", // Disparador: Edad
            targetId: ["valorControl19135", "valorControl19136", "valorControl19134"],  // Campo afectado
            valueCamp: ["4028", "4020", "4513"],
            camptipoDocument: "valorControl19131",
            nacionalidadId: "valorControl19138", // value 50 colombiano
            isEdad: true
        });

        new this.CondicionalValidate({
            triggerId: "valorControl19140",
            activeValue: "100",
            groupA: ["valorControl22673"]
        });


        new this.CondicionalValidate({
            triggerId: "valorControl19133",
            activeValue: "79",
            groupA: ["valorControl19140", "valorControl22673"],

        });


        new this.CondicionalValidate({
            triggerId: "valorControl19133",
            activeValue: "67",
            input: ["valorControl19144"],
            valueinputDefect: ["3785"],
            condicionalsimple: true
        });


        const misCampos = [
            '19129', '19130'
        ].filter(Boolean);

        this.requeridos.configurarValidacionNombres(misCampos);


    }
}