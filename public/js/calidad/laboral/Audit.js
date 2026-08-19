import SelectSearch from '../Clases/SelectSearch.js';
import AttributeInput from '../Clases/AttributeInput.js';

export default class audit {
    constructor() {
        if (!this.baseInput || this.baseInput.value !== "123") return;
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

            const digitador = document.getElementById("valorControl22287");


            if (digitador) this.pag1();


        } catch (error) {
            console.error("❌ Error inicializando sesiones:", error);
        }
    }

    async pag1() {
        console.log("Iniciando validación Página 1");

        new this.ButtonValidator({
            inputId: "valorControl22287"
        });

        new this.ColombianDocumentValidator({
            tipoDocId: "valorControl22266",
            numeroDocId: "valorControl22286",
        });

        const misCampos = [
            '22265'
        ].filter(Boolean);

        this.requeridos.configurarValidacionNombres(misCampos);

    }



} 