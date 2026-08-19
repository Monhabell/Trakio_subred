import SelectSearch from '../Clases/SelectSearch.js';
import AttributeInput from '../Clases/AttributeInput.js';

export default class sqr {

    constructor() {
        if (!this.baseInput || this.baseInput.value !== "37") return;
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
        const tipoDoc = document.getElementById("valorControl22313");
        if (tipoDoc) await this.pag1();
    }

    async pag1() {

        console.log("Iniciando validación Página 1");
        new this.ButtonValidator({
            inputId: "valorControl22356"
        });

        const misCampos = [
            '22313', '22314', '22315', '22316', '22356'
        ].filter(Boolean);

        this.requeridos.configurarValidacionNombres(misCampos);


        new this.ColombianDocumentValidator({
            tipoDocId: "valorControl22317",
            numeroDocId: "valorControl22318",
        });
    }

}