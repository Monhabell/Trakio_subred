import SelectSearch from '../Clases/SelectSearch.js';
import AttributeInput from '../Clases/AttributeInput.js';

export default class formnal {

    constructor() {
        if (!this.baseInput || this.baseInput.value !== "120") return;
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

        const modCond = await import('../Clases/FormDependencyManager.js');
        this.CondicionalValidate = modCond.default;

        const placa = document.getElementById('valorControl22021')
        const sexo = document.getElementById('valorControl22036')


        if (placa) await this.pag1();
        if (sexo) await this.pag2();
    }

    async pag1() {
        try {
            console.log("Iniciando validación Página 1 formal");
            new this.ButtonValidator({
                inputId: "valorControl22021"
            });

        } catch (error) {

        }
    }

    async pag2() {
        try {
            console.log("Iniciando validación Página 2 formal");
            new this.ButtonValidator({
                inputId: "valorControl22036"
            });

            this.buscar.insert(['22032']);

            new this.ColombianDocumentValidator({
                tipoDocId: "valorControl22030",
                numeroDocId: "valorControl22031",
                nacionalidadId: "valorControl22032"

            });

            new this.CondicionalValidate({
                triggerId: "valorControl22039",
                activeValue: "959",
                groupA: ["valorControl22040"],
                groupB: ["valorControl22041", "valorControl22042"]
            });





        } catch (error) {

        }
    }

}