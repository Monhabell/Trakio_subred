import SelectSearch from '../Clases/SelectSearch.js';
import AttributeInput from '../Clases/AttributeInput.js';

export default class ut {

    constructor() {
        if (!this.baseInput || this.baseInput.value !== "15") return;
        this.buscar = new SelectSearch();
        this.requeridos = new AttributeInput();
        this.baseInput = document.getElementById('Id_Base');
        this.init();
    }

    async init() {
        const modButton = await import('../Clases/ButtonNewRegister.js');
        this.ButtonValidator = modButton.default;


        const modDoc = await import('../Clases/ColombianDocumentValidator.js');
        this.ColombianDocumentValidator = modDoc.default;
        const tipoDoc = document.getElementById("valorControl20550");
        if (tipoDoc) await this.pag1();
    }

    async pag1() {

        console.log("Iniciando validación Página 1");
        new this.ButtonValidator({
            inputId: "valorControl20531"
        });

        const misCampos = [
            '20550'
        ].filter(Boolean);

        this.requeridos.configurarValidacionNombres(misCampos);

    }



}