

export default class ColombianDocumentValidator {
    constructor(config = {}) {
        if (!config.tipoDocId || !config.numeroDocId) return;


        this.tipoDocumento = document.getElementById(config.tipoDocId);
        this.numeroDocumento = document.getElementById(config.numeroDocId);
        this.nacionalidad = config.nacionalidadId
            ? document.getElementById(config.nacionalidadId)
            : null;



        if (!this.tipoDocumento || !this.numeroDocumento) return;

        this.init();
    }

    init() {
        // ... (puntos 1 y 2 se mantienen igual)

        // 3. Eventos
        this.tipoDocumento.addEventListener("change", () => {
            this.setNacionalidadState();
            this.numeroDocumento.value = "";
        });

        this.numeroDocumento.addEventListener("change", () => {
            this.validarDocumento();
        });

        // MODIFICACIÓN AQUÍ:
        this.numeroDocumento.addEventListener("input", () => {
            const tipo = this.tipoDocumento.value;

            // Si es RC (60), TI (61) o CC (59), bloqueamos letras en tiempo real
            if (["59", "60", "61"].includes(tipo)) {
                this.numeroDocumento.value = this.numeroDocumento.value.replace(/\D/g, '');
            }
            // Si es 65 o 66, permitimos letras pero las convertimos a MAYÚSCULAS automáticamente
            else if (["65", "66"].includes(tipo)) {
                this.numeroDocumento.value = this.numeroDocumento.value.toUpperCase();
            }
        });
    }

    // Separamos la lógica de la nacionalidad para que no limpie el número
    setNacionalidadState() {
        if (!this.nacionalidad) return; // 🔥 Protección clave

        const tipo = this.tipoDocumento.value;
        const $nacionalidad = $(this.nacionalidad);

        if (["59", "60", "61"].includes(tipo)) {

            $nacionalidad.val("50").trigger('change');
            $nacionalidad.addClass('readonly-select');

            const container = this.nacionalidad.nextElementSibling;
            if (container) {
                container.style.backgroundColor = "rgba(128,128,128,0.2)";
                container.style.pointerEvents = "none";
            }

        } else {

            $nacionalidad.removeClass('readonly-select');

            const container = this.nacionalidad.nextElementSibling;
            if (container) {
                container.style.backgroundColor = "";
                container.style.pointerEvents = "auto";
            }
        }
    }

    validarDocumento() {
        const tipo = this.tipoDocumento.value;
        const numero = this.numeroDocumento.value.trim();

        if (!numero) return true;

        let mensajeError = "";

        switch (tipo) {
            case "60": // RC
                if (!/^\d+$/.test(numero)) mensajeError = "El Registro Civil solo debe contener números.";
                else if (numero.length < 8 || numero.length > 11)
                    mensajeError = "El Registro Civil debe tener entre 8 y 11 dígitos.";
                break;

            case "61": // TI
                if (!/^\d+$/.test(numero)) mensajeError = "La Tarjeta de Identidad solo debe contener números.";
                else if (numero.length < 10 || numero.length > 11)
                    mensajeError = "La Tarjeta de Identidad debe tener entre 10 y 11 dígitos.";
                break;

            case "59": // CC
                if (!/^\d+$/.test(numero)) mensajeError = "La Cédula solo debe contener números.";
                else if (numero.length < 6 || numero.length > 10)
                    mensajeError = "La Cédula debe tener entre 6 y 10 dígitos.";
                break;

            case "65": // Ejemplo: Cédula Extranjería
            case "66": // Ejemplo: Pasaporte
                // Permite letras y números. Validamos que no tenga más de 2 letras.
                const letras = numero.match(/[a-zA-Z]/g) || [];
                if (letras.length > 2) {
                    mensajeError = "Este documento solo permite hasta 2 letras.";
                } else if (numero.length < 3 || numero.length > 15) {
                    mensajeError = "El documento debe tener entre 3 y 15 caracteres.";
                }
                break;

            default:
                // Por si hay otros tipos, validamos al menos longitud básica
                if (numero.length < 3) mensajeError = "Documento demasiado corto.";
                break;
        }

        // Si se encontró un error
        if (mensajeError) {
            this.numeroDocumento.value = ""; // Blanquear campo
            if (typeof $ !== 'undefined') $(this.numeroDocumento).trigger('change');
            return this.bloquear(mensajeError);
        }

        return true;
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