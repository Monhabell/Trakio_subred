import SelectSearch from '../Clases/SelectSearch.js';
import AttributeInput from '../Clases/AttributeInput.js';

export default class Menstrual {
    constructor() {
        if (!this.baseInput || this.baseInput.value !== "117") return;
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

            const Menstrual = document.getElementById("valorControl20364");

            if (Menstrual) this.pag1();


        } catch (error) {
            console.error("❌ Error inicializando sesiones:", error);
        }
    }

    pag1() {
        console.log("Iniciando validación Página 1 menstrual");
        new this.ButtonValidator({
            inputId: "valorControl20364"
        });

        new this.ColombianDocumentValidator({
            tipoDocId: "valorControl20392",
            numeroDocId: "valorControl20391",

        });

        const misCampos = [
            '20393'
        ].filter(Boolean);

        this.requeridos.configurarValidacionNombres(misCampos);


    }


}
