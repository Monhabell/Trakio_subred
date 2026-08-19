export default class FormDependencyManager {
    constructor(config) {
        this.trigger = document.getElementById(config.triggerId);
        this.dateInput = document.getElementById(config.dateId);
        this.config = config;

        if (!this.trigger) return;
        this.init();
    }

    init() {
        this.trigger.addEventListener("change", () => this.applyLogic());

        if (this.dateInput) {
            this.dateInput.addEventListener("change", () => {
                setTimeout(() => this.applyLogic(), 250);
            });
        }

        // Validación inicial (Carga y Refresco)
        this.applyLogic();
        window.addEventListener('load', () => this.applyLogic());
    }

    applyLogic() {
        // Caso 1: Lógica de Edad
        if (this.config.isEdad) {
            this.logicEdad();
            return;
        }

        // Caso 2: Condicional Simple (Asignación de valor por defecto)
        if (this.config.condicionalsimple) {
            this.logicSimple();
            return;
        }

        // Caso 3: Lógica de Grupos (Habilitar/Deshabilitar)
        const currentValue = this.trigger.value;
        let isMatch = Array.isArray(this.config.activeValue)
            ? this.config.activeValue.includes(currentValue)
            : currentValue == this.config.activeValue;

        this.updateInputs(this.config.groupA || [], isMatch);
        this.updateInputs(this.config.groupB || [], !isMatch);
    }

    logicSimple() {
        const currentValue = this.trigger.value;
        const activeValue = this.config.activeValue;
        const targets = Array.isArray(this.config.input) ? this.config.input : [this.config.input];
        const defaultValues = Array.isArray(this.config.valueinputDefect) ? this.config.valueinputDefect : [this.config.valueinputDefect];

        const isMatch = Array.isArray(activeValue)
            ? activeValue.includes(currentValue)
            : currentValue == activeValue;

        targets.forEach((id, index) => {
            const el = document.getElementById(id);
            if (!el) return;

            if (isMatch) {
                const valDefecto = defaultValues[index];
                if (el.value !== valDefecto) {
                    el.value = valDefecto;
                    if (typeof $ !== 'undefined') $(el).trigger('change');
                    el.style.backgroundColor = "rgba(50, 255, 50, 0.3)"; // Éxito
                }
            }
        });
    }

    logicEdad() {
        const edad = parseInt(this.trigger.value);
        if (isNaN(edad)) return;

        // 1. Lógica para el campo de Tipo de Documento (camptipoDocument)
        if (this.config.camptipoDocument) {
            const elDoc = document.getElementById(this.config.camptipoDocument);
            const elNacionalidad = document.getElementById(this.config.nacionalidadId);

            if (elDoc && elNacionalidad) {
                const esColombiano = elNacionalidad.value == "50";
                const valorActual = elDoc.value;

                // Definimos códigos de excepción que NO debemos sobrescribir
                // 7: Mayor sin ID, 8: Menor sin ID, 65: CE, 66: PA
                const excepcionesPermitidas = ["7", "8", "65", "66"];

                let tipoCorrecto = "";
                let nombreTipo = "";

                // Si el valor actual es una excepción permitida, no forzamos el cambio
                if (excepcionesPermitidas.includes(valorActual)) {
                    console.log(`ℹ️ Se respeta el documento especial: ${valorActual}`);
                }
                else if (esColombiano) {
                    // Definimos el tipo correcto según tabla legal colombiana
                    if (edad >= 0 && edad <= 6) {
                        tipoCorrecto = "60"; // RC
                        nombreTipo = "Registro Civil (RC)";
                    } else if (edad >= 7 && edad <= 17) {
                        tipoCorrecto = "61"; // TI
                        nombreTipo = "Tarjeta de Identidad (TI)";
                    } else if (edad >= 18) {
                        tipoCorrecto = "59"; // CC
                        nombreTipo = "Cédula de Ciudadanía (CC)";
                    }

                    // SOLO si hay inconsistencia y no es excepción
                    if (tipoCorrecto && valorActual !== tipoCorrecto) {

                        // Notificamos al usuario
                        this.bloquear(`El tipo de documento es erroneo el tipó correcto es ${nombreTipo} por la edad (${edad} años). Si es un caso especial (Sin ID), selecciónelo manualmente. Por favor reporte en Trakio.`);


                        elDoc.value = "";
                        elDoc.style.backgroundColor = "rgba(255, 50, 50, 0.2)"; // Verde suave (ajustado)

                        // Disparamos cambio para Select2 y validadores de longitud
                        if (typeof $ !== 'undefined') $(elDoc).trigger('change');

                        console.warn(`⚠️ Ajuste automático: ${valorActual} -> ${tipoCorrecto}`);
                    }
                }
            }
        }

        // 2. Lógica para targets generales (Regla de los 14 años para otros campos)
        const targets = Array.isArray(this.config.targetId) ? this.config.targetId : [this.config.targetId];
        const values = Array.isArray(this.config.valueCamp) ? this.config.valueCamp : [this.config.valueCamp];

        targets.forEach((id, index) => {
            const el = document.getElementById(id);
            if (!el) return;

            const valorRestringido = values[index];

            if (edad < 14) {
                // Si es menor de 14, VALIDAMOS que el valor sea el permitido
                if (el.value !== valorRestringido) {
                    // 1. Si el dato es diferente (está mal o vacío), lo vaciamos
                    el.value = "";

                    // 2. Disparamos el cambio para que otros componentes se enteren
                    if (typeof $ !== 'undefined') $(el).trigger('change');

                    // 3. Marcamos en ROJO para alertar al usuario que debe corregir
                    el.style.backgroundColor = "rgba(255, 50, 50, 0.3)";
                    el.style.borderLeft = "4px solid red";
                } else {
                    // Si el usuario selecciona el valor correcto (restringido), se pone en VERDE
                    el.style.backgroundColor = "rgba(50, 255, 50, 0.3)";
                    el.style.borderLeft = "4px solid green";
                }
            } else {
                // Si es MAYOR de 14, NO puede tener el valor restringido de menor
                if (el.value === valorRestringido) {
                    // 1. Vaciamos porque el valor de "menor" es inválido para un adulto
                    el.value = "";
                    if (typeof $ !== 'undefined') $(el).trigger('change');

                    // 2. Marcamos en ROJO
                    el.style.backgroundColor = "rgba(255, 50, 50, 0.3)";
                    el.style.borderLeft = "4px solid red";
                } else if (el.value !== "") {
                    // Si tiene cualquier otro valor (que no sea el de menor), está BIEN
                    el.style.backgroundColor = "rgba(50, 255, 50, 0.3)";
                    el.style.borderLeft = "4px solid green";
                }
            }
        });
    }

    updateInputs(idList, shouldEnable) {
        idList.forEach(id => {
            const el = document.getElementById(id);
            if (!el) return;

            // Habilitar / Deshabilitar nativo
            el.disabled = !shouldEnable;
            el.required = shouldEnable;

            if (!shouldEnable) {
                el.value = "";
                el.style.backgroundColor = "rgba(128, 128, 128, 0.1)";

                // Corrección para Select2
                if (typeof $ !== 'undefined' && $(el).data('select2')) {
                    $(el).val(null).prop('disabled', true).trigger('change');
                }
            } else {
                // Habilitar Select2
                if (typeof $ !== 'undefined' && $(el).data('select2')) {
                    $(el).prop('disabled', false).trigger('change');
                }
                if (el.value.trim() !== "") {
                    el.style.backgroundColor = "rgba(50, 255, 50, 0.3)";
                }
            }
        });
    }

    bloquear(mensaje) {
        if (typeof Swal !== "undefined") {
            Swal.fire({
                icon: 'error',
                title: 'Dato inconsistente',
                text: mensaje,
                confirmButtonText: 'Corregir'
            }).then(() => {
                // Mantenemos el valor para que el usuario vea qué está mal, 
                // pero le damos el foco para que lo corrija.
                this.numeroDocumento.focus();
                this.numeroDocumento.style.border = "2px solid red";
            });
        } else {
            alert(mensaje);
            this.numeroDocumento.focus();
        }
        return false;
    }
}