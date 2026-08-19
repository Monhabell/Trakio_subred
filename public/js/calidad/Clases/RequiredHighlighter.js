export default class RequiredHighlighter {
    constructor(config = {}) {
        // Configuración de colores (puedes ajustarlos desde sesiones.js)
        this.colorPending = config.colorPending || "rgba(255, 0, 0, 0.15)"; // Amarillo suave
        this.colorSuccess = config.colorSuccess || "rgba(3, 255, 62, 0.22)";  // Verde suave
        this.colorDefault = config.colorDefault || "#ffffff";                 // Blanco

        this.init();
    }

    init() {
        this.highlightAll();

        // Escuchar cambios en tiempo real
        document.body.addEventListener("input", (e) => {
            if (e.target.hasAttribute('required')) this.applyStyle(e.target);
        });

        document.body.addEventListener("change", (e) => {
            if (e.target.hasAttribute('required')) this.applyStyle(e.target);
        });

        this.observeChanges();
    }

    highlightAll() {
        const inputs = document.querySelectorAll("input[required], select[required], textarea[required]");
        inputs.forEach(input => this.applyStyle(input));
    }

    applyStyle(el) {
        // 1. SI ESTÁ DESHABILITADO: Limpiar estilos (No es responsabilidad del usuario)
        if (el.disabled) {
            el.style.backgroundColor = this.colorDefault;
            el.style.borderLeft = "";
            return;
        }

        // 2. SI ESTÁ HABILITADO Y VACÍO: Resaltar en AMARILLO (Pendiente)
        if (el.value.trim() === "") {
            el.style.backgroundColor = this.colorPending;
            el.style.borderLeft = "4px solid #ff0000ff"; // Borde amarillo fuerte
            el.style.transition = "all 0.3s ease";
        }
        // 3. SI ESTÁ HABILITADO Y LLENO: Resaltar en VERDE (Correcto)
        else {
            el.style.backgroundColor = this.colorSuccess;
            el.style.borderLeft = "4px solid #14ca3eff"; // Borde verde éxito
            el.style.transition = "all 0.3s ease";
        }
    }

    observeChanges() {
        const observer = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                if (mutation.type === "attributes" &&
                    (mutation.attributeName === "disabled" || mutation.attributeName === "required")) {
                    this.applyStyle(mutation.target);
                }
            });
        });

        document.querySelectorAll('input, select, textarea').forEach(input => {
            observer.observe(input, { attributes: true });
        });
    }
}