export default class AttributeInput {
    constructor() {
        this.bgError = "rgba(255, 50, 50, 0.4)";
        this.bgSuccess = "rgba(50, 255, 50, 0.7)";
        this.bgWarning = "rgba(255, 255, 50, 0.7)";
        this.bgPrimary = "rgba(90, 100, 255, 0.5)";
        this.bgErrorbgWhite = "#fff";
        this.bgDisabled = "rgba(128, 128, 128, 0.2)";
        this.bgTransparent = "rgba(255, 255, 255, 0) ";
    }

    /**
     * Validar si los campos requeridos están presentes y marcarlos como requeridos.
     * @param {Array|string} inputsId - IDs de los campos requeridos.
     * @returns {boolean} - `true` si todos los campos existen y son válidos, de lo contrario `false`.
     */
    required(inputsId) {
        let todosValidos = true;

        if (typeof inputsId === 'object' && !Array.isArray(inputsId)) {
            inputsId = Object.values(inputsId).flat();
        }

        inputsId.forEach(id => {
            const input = document.getElementById(`valorControl${id}`);
            if (!input) {
                todosValidos = false;
            } else {
                input.required = true;
            }
        });

        return todosValidos;
    }

    type(inputsId, type) {
        if (Array.isArray(inputsId)) {
            inputsId.forEach((inputId) => {
                const input = document.getElementById(`valorControl${inputId}`);
                input.setAttribute("type", type);
            });
        }

        if (typeof inputsId === "string") {
            const input = document.getElementById(`valorControl${inputsId}`);
            input.setAttribute("type", type);
        }
    }

    backgroundError(inputId) {
        const input = document.getElementById(inputId);
        if (input) {
            input.style.backgroundColor = this.bgError;
            input.style.color = "#000";
        } else {
            console.warn(`El campo con ID valorControl${inputId} no existe en el DOM.`);
        }
    }

    inputsIndicators() {
        const campos = document.querySelectorAll(
            "select, input:not([type='submit']), textarea"
        );

        campos.forEach((campo) => {
            if (campo.disabled) {
                campo.style.backgroundColor = this.bgDisabled;
                return;
            }

            const valorTrim = campo.value.trim();
            const esRequerido = campo.required;
            const tieneError =
                window.getComputedStyle(campo).backgroundColor === this.bgError;

            if (esRequerido && valorTrim === "") {
                campo.style.backgroundColor = this.bgWarning;
            } else if (!esRequerido && valorTrim === "" && !tieneError) {
                campo.style.backgroundColor = this.bgWhite;
            } else if (valorTrim !== "" && !tieneError) {
                campo.style.backgroundColor = this.bgSuccess;
            }
        });
    }

    disabledOptionsFromSelect(idSelect, optionsValueToDisabled, booleanDisabled) {
        const select = document.getElementById(idSelect);
        const optionsSelect = select.querySelectorAll("option");

        // Deshabilitar todas las opciones al principio
        optionsSelect.forEach((option) => {
            option.disabled = !booleanDisabled;
        });

        if (Array.isArray(optionsValueToDisabled)) {
            // Si optionsValueToDisabled es un array
            optionsSelect.forEach((option) => {
                if (optionsValueToDisabled.includes(option.value)) {
                    option.disabled = booleanDisabled;
                }
            });
        } else {
            // Si optionsValueToDisabled es un solo valor
            optionsSelect.forEach((option) => {
                if (option.value == optionsValueToDisabled) {
                    option.disabled = booleanDisabled;
                    option.selected = !booleanDisabled;
                }
            });
        }
    }

    /**
     * Actualizar el estado visual de un campo en base a si hay un error.
     * @param {HTMLElement} campo - El campo HTML a modificar.
     * @param {boolean} esError - Indica si el campo tiene un error.
     */
    actualizarEstadoCampo(campo, esError, mensaje = "") {
        if (!campo) {
            console.error("Campo no encontrado para actualizar estado.");
            return;
        }

        const showMessage = (message) => {
            const existingMessage = campo.parentElement.querySelector(".campo-error-message");
            if (existingMessage) existingMessage.remove();

            const newSpan = document.createElement("span");
            newSpan.classList.add("campo-error-message");
            newSpan.innerHTML = `<p style="color: red; margin: 0;">${message}</p>`;
            campo.parentElement.appendChild(newSpan);
        };

        if (esError) {
            if (mensaje) showMessage(mensaje);
            campo.style.backgroundColor = this.bgError;
            campo.value = "";
        } else {
            const existingMessage = campo.parentElement.querySelector(".campo-error-message");
            if (existingMessage) existingMessage.remove()
            campo.style.backgroundColor = this.bgSuccess;
        }

        this.toggleBotonGuardar(esError);
    }

    /**
     * Activar o desactivar el botón de guardar.
     * @param {boolean} deshabilitar - Indica si el botón debe estar deshabilitado.
     */
    toggleBotonGuardar(deshabilitar) {
        const boton = document.getElementById('botonActualizarInformacion');
        if (!boton) {
            console.error("Botón 'Actualizar Información' no encontrado.");
            return;
        }
        boton.disabled = deshabilitar;
    }

    /**
     * Habilitar o deshabilitar un conjunto de campos.
    */
    toggleCampos(camposIds, deshabilitar) {
        camposIds.forEach(id => {
            const campo = document.getElementById(`valorControl${id}`);
            if (campo) {
                campo.disabled = deshabilitar;
            } else {
                console.warn(`Campo con ID valorControl${id} no encontrado.`);
            }
        });
    }




    // crear funcion que recibe id de los campos que son solo texto para validar primer nombre segundo nombre aplellido y segundo apellido ademas campos que tienen nomnbre completo en si mismos 


    configurarValidacionNombres(ids) {
        // Esta expresión busca cualquier cosa que NO sea:
        // A-Z (Mayúsculas), ÁÉÍÓÚ (Tildes), Ñ, o \s (Espacios)
        const regexProhibidos = /[^A-ZÁÉÍÓÚÑ\s]/;
        const regexLimpieza = /[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g;

        ids.forEach(id => {
            if (!id) return;
            const fullId = `valorControl${id}`;
            const el = document.getElementById(fullId);

            if (!el) {
                console.error(`❌ Error: El elemento ${fullId} NO existe.`);
                return;
            }

            el.addEventListener('input', (e) => {
                let valorOriginal = e.target.value;

                if (regexProhibidos.test(valorOriginal)) {

                    // Disparamos tu aviso de bloqueo
                    this.bloquear("Solo se permiten MAYÚSCULAS. No se aceptan números ni caracteres especiales (*, -, ., @, etc.)");

                    let valorLimpio = valorOriginal.replace(regexLimpieza, '').toUpperCase();

                    e.target.value = valorLimpio;

                    this.backgroundError(id);
                } else {
                    // Si el campo está perfecto, fondo verde
                    el.style.backgroundColor = this.bgSuccess;
                }
            });

            // Limpieza final al perder el foco (quita espacios extras)
            el.addEventListener('blur', (e) => {
                e.target.value = e.target.value.trim().replace(/\s+/g, ' ').toUpperCase();
            });
        });
    }



    bloquearCamposPermanente(ids) {
        ids.forEach(id => {
            if (!id) return;
            const fullId = `valorControl${id}`;
            const el = document.getElementById(fullId);

            if (el) {
                // 1. Bloqueo nativo del HTML
                el.disabled = true;
                el.style.backgroundColor = this.bgDisabled;
                el.style.cursor = "not-allowed";

                // 2. Bloqueo para Select2 (Forma correcta)
                if (typeof $ !== 'undefined' && $(el).data('select2')) {
                    // Select2 se deshabilita pasando true al atributo disabled
                    $(el).prop('disabled', true).trigger('change.select2');
                }
            } else {
                console.warn(`⚠️ No se pudo bloquear: ${fullId} no existe.`);
            }
        });
    }


    fechasInput(ids, inputIntervencionId) {
        // Agrega esto en tu función fechasInput
        ids.forEach(id => {
            const el = $(`#${id}`);
            if (el.data('datepicker')) {
                el.datepicker("destroy"); // Elimina el calendario de la librería
            }
            // También desactiva el autocompletado del navegador
            el.attr('autocomplete', 'off');
        });
        const inputIntervencion = document.getElementById(inputIntervencionId);
        if (!inputIntervencion) return;

        // Función para convertir DD/MM/AAAA a un objeto Date válido
        const parseFecha = (str) => {
            if (!str || !str.includes('/')) return new Date(str); // Por si ya viene en AAAA-MM-DD
            const [dia, mes, anio] = str.split('/');
            return new Date(`${anio}-${mes}-${dia}T00:00:00`);
        };

        const validarFec = (el) => {
            if (!el.value || !inputIntervencion.value) return;

            // Usamos la nueva función de conversión
            const fechaSeleccionada = parseFecha(el.value);
            const fechaIntervencion = parseFecha(inputIntervencion.value);
            const hoy = new Date();
            hoy.setHours(0, 0, 0, 0);

            let mensajeError = "";

            // Validaciones
            if (isNaN(fechaSeleccionada.getTime()) || isNaN(fechaIntervencion.getTime())) {
                console.error("Formato de fecha no reconocido", el.value, inputIntervencion.value);
                return;
            }

            if (fechaSeleccionada < fechaIntervencion) {
                mensajeError = `La fecha (${el.value}) no puede ser anterior a la intervención (${inputIntervencion.value}).`;
            } else if (fechaSeleccionada > hoy) {
                mensajeError = `La fecha (${el.value}) no puede ser superior al día de hoy.`;
            }

            if (mensajeError) {
                el.value = "";
                el.style.backgroundColor = "rgba(255, 0, 0, 0.1)";
                el.style.borderLeft = "4px solid red";

                if (typeof Swal !== "undefined") {
                    Swal.fire({ icon: 'error', title: 'Fecha Inválida', text: mensajeError });
                } else {
                    alert(mensajeError);
                }
            } else {
                el.style.backgroundColor = "rgba(40, 167, 69, 0.15)";
                el.style.borderLeft = "4px solid #28a745";
            }
        };

        // Ejecución inicial y eventos
        setTimeout(() => {
            ids.forEach(id => {
                const el = document.getElementById(id);
                if (el && el.value) validarFec(el);
            });
        }, 500);

        ids.forEach(id => {
            const el = document.getElementById(id);
            if (el) el.addEventListener("change", () => validarFec(el));
        });

        inputIntervencion.addEventListener("change", () => {
            ids.forEach(id => {
                const el = document.getElementById(id);
                if (el && el.value) validarFec(el);
            });
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

