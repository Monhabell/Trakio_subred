import SelectSearch from '../Clases/SelectSearch.js';
import AttributeInput from '../Clases/AttributeInput.js';

export default class rqc {

    constructor() {
        if (!this.baseInput || this.baseInput.value !== "34") return;
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
        const tipoDoc = document.getElementById("valorControl22294");
        if (tipoDoc) await this.pag1();
    }

    async pag1() {

        console.log("Iniciando validación Página 1");
        new this.ButtonValidator({
            inputId: "valorControl22312"
        });

        const misCampos = [
            '22294', '22295', '22296', '22297'
        ].filter(Boolean);

        this.requeridos.configurarValidacionNombres(misCampos);


        new this.ColombianDocumentValidator({
            tipoDocId: "valorControl22298",
            numeroDocId: "valorControl22299",
        });
    }

}