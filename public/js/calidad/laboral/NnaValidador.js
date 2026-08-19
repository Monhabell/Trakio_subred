import SelectSearch from '../Clases/SelectSearch.js';
import AttributeInput from '../Clases/AttributeInput.js';

export default class NnaValidator {
    constructor() {

        this.buscar = new SelectSearch();
        this.requeridos = new AttributeInput();
        this.baseInput = document.getElementById('Id_Base');

        if (!this.baseInput || this.baseInput.value !== "16") return;

        this.init();
    }

    async init() {
        const tipoDoc = document.getElementById("valorControl21240");
        if (tipoDoc) await this.pag1();
    }

    async pag1() {
        try {
            // Buscadores de selects
            this.buscar.insert(['21246', '21267', '21257', '21268', '21307', '21302', '21818']);

            // Imports dinámicos
            const modDoc = await import('../Clases/ColombianDocumentValidator.js');
            const ColombianDocumentValidator = modDoc.default;

            const modCond = await import('../Clases/FormDependencyManager.js');
            const CondicionalValidate = modCond.default;

            const modHigh = await import('../Clases/RequiredHighlighter.js');
            this.Highlighter = modHigh.default;

            new this.Highlighter({
                colorRequired: "rgba(255, 255, 0, 0.1)", // Amarillo muy sutil
                colorValid: "#ffffff"
            })

            // --- INSTANCIA 1: Validadores de Documentos ---
            new ColombianDocumentValidator({
                tipoDocId: "valorControl21240",
                numeroDocId: "valorControl21241",
                nacionalidadId: "valorControl21246"
            });


            new CondicionalValidate({
                dateId: "valorControl21251",    // Disparador: Fecha Nacimiento
                triggerId: "valorControl21252", // Disparador: Edad
                targetId: ["valorControl21249", "valorControl21256"],  // Campo afectado
                valueCamp: ["4020", "4028"],
                isEdad: true
            });


            new CondicionalValidate({
                triggerId: "valorControl21265",
                activeValue: "58",
                groupA: ["valorControl21288", "valorControl21289", "valorControl21294"],

            });

            new CondicionalValidate({
                triggerId: "valorControl21270",
                activeValue: "958",
                groupA: ["valorControl21271"],

            });

            new CondicionalValidate({
                triggerId: "valorControl21822",
                activeValue: "958",
                groupA: ["valorControl21823"]
            });

            new CondicionalValidate({
                triggerId: "valorControl21824",
                activeValue: "958",
                groupA: ["valorControl21825"]
            });

            new CondicionalValidate({
                triggerId: "valorControl21826",
                activeValue: ["121", "122", "123", "124", "125", "126", "127", "128"],
                groupA: ["valorControl21827"]
            });

            const misCamposName = [
                '21242', '21243', '21244', '21245'
            ].filter(Boolean);

            this.requeridos.configurarValidacionNombres(misCamposName);
            // no requeridos
            const segundoNombreAcudiente = document.getElementById("valorControl21297");
            const segundoApellidoAcudiente = document.getElementById("valorControl21299");

            [segundoNombreAcudiente, segundoApellidoAcudiente].forEach(campo => {
                if (campo) {
                    campo.required = false;
                }
            });

            // 6. Formateo de Servicio de Salud (CORREGIDO const)
            const serviSalud = document.getElementById("valorControl21828");

            if (serviSalud) {
                // Cambiamos "input" por "blur" para que actúe al salir del campo
                serviSalud.addEventListener("blur", (e) => {
                    let val = e.target.value.trim();

                    // Verificamos si es un número de un solo dígito (1-9)
                    if (/^[1-9]$/.test(val)) {
                        e.target.value = "0" + val;
                    }
                });
            }


        } catch (error) {
            console.error("❌ Error en la carga de módulos:", error);
        }
    }
}

