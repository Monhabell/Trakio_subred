import Helper from "../Clases/Helper.js";
import Poblacional from "../Clases/Poblacional.js";
import AttributeInput from "../Clases/AttributeInput.js";

export default class GlobalSesionesColectivas extends Helper {
    #idbase;
    #inputsSelectors; // Contenedor general
    #form;
    #attributeInput;

    constructor(idbase = null) {
        super();
        this.#idbase = idbase;
        this.#attributeInput = new AttributeInput();

        // Organizamos todo en un solo objeto indexado por el ID de la base
        this.#inputsSelectors = {
            "113": { // Comunitario
                page_1: {
                    idPag: "318",
                    required: ["19228", "19231"],
                    type_text: ["19224", "19228"],
                    listeners: [
                        {
                            triggerId: "19233", // El ID del select que cambia
                            action: (value) => {
                                // Ejemplo: Si el valor es '1', el campo 19225 es requerido
                                if (value === 58) {
                                    this.aplicarValidacionObligatoria(["19258"]);
                                }
                            }
                        }
                    ]

                },
                page_2: {
                    idPag: "319",
                    required: ["19235", "19238"],
                    type_text: ["19235"],
                }
            },
            "112": { // Institucional
                page_1: {
                    idPag: "239",
                    required: ["18968", "18974"],
                    type_text: ["18968"],

                }
            },
            "107": { // Educativo
                page_1: {
                    required: ["17353", "17351"],
                    type_text: ["17351"],

                }
            },
            "114": { // Laboral
                page_1: {
                    required: ["19484", "19486"],
                    type_text: ["19480", "19484"],

                }
            }
        };
        this.#form = document.getElementById('formularioNuevoFormato');
    }

    initDynamicListeners(configPagina) {
        if (!configPagina.listeners) return;

        configPagina.listeners.forEach(listen => {
            const element = document.getElementById(`ID_CAMPO_${listen.triggerId}`); // Ajusta según tu naming de IDs
            if (element) {
                // Escuchamos el cambio
                element.addEventListener('change', (e) => {
                    listen.action(e.target.value);
                });
            }
        });
    }


    setBaseDestino(id) {
        this.#idbase = id;
        console.log("Base destino actualizada a:", this.#idbase);
    }
    
    selectPage() {
        const sectionInput = this.#form.querySelector('input[name="Id_seccion"]');
        const sectionId = sectionInput?.value;

        const configBase = this.#inputsSelectors[String(this.#idbase)];

        if (!configBase) {
            console.error(`No hay configuración para la base: ${this.#idbase}`);
            return;
        }

        const paginaConfigurada = Object.values(configBase).find(p => String(p.idPag) === String(sectionId));
        if (!paginaConfigurada) {
            console.warn(`Sección ${sectionId} no mapeada para la base ${this.#idbase}`);
            return;
        }

        // Aplicar tipos para validar inputs
        if (paginaConfigurada.required) {
            this.aplicarValidacionObligatoria(paginaConfigurada.required);
        }

    
    }
    aplicarValidacionObligatoria(ids) {
        console.log("Aplicando validación obligatoria para IDs:", ids); 
        const result = this.#attributeInput.required(ids);
        if (!result) {
            console.warn("Algunos campos requeridos no existen en el DOM.");
        }
          
    }


}