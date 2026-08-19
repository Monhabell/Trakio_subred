class DateValidator {

    constructor() {

        const selector = '#FechaIntervencion';
        const options = {
            min: "2026-08-01",
            max: "2026-08-31",
            blockWeekends: true
        };
        this.input = document.querySelector(selector);
        if (!this.input) {
            console.warn(`No se encontró el input: ${selector}`);
            return;
        }
        this.min = options.min;
        this.max = options.max;
        this.blockWeekends = options.blockWeekends;
        this.messageContainer = null;
        this.init();
    }

    init() {
        this.createMessageContainer();
        this.addEvents();
    }

    createMessageContainer() {
        this.messageContainer = document.createElement("small");
        this.messageContainer.style.color = "red";
        this.input.after(this.messageContainer);
    }

    addEvents() {

        // Cuando escriben manual
        this.input.addEventListener("blur", () => {
            this.validate();
        });

        this.input.addEventListener("change", () => {
            this.validate();
        });

        // 🔥 Evento REAL del datepicker (Bootstrap)
        if (window.$) {
            $(this.input).on('changeDate', () => {
                this.validate();
            });
        }

        // Autoformato
        this.input.addEventListener("input", (e) => {
            this.formatInput(e);
        });
    }

    formatInput(e) {

        let value = e.target.value.replace(/\D/g, '');

        if (value.length >= 3 && value.length <= 4)
            value = value.replace(/(\d{2})(\d+)/, '$1/$2');

        if (value.length >= 5)
            value = value.replace(/(\d{2})(\d{2})(\d+)/, '$1/$2/$3');

        e.target.value = value;
    }

    validate() {

        const value = this.input.value.trim();

        if (!value) return;

        const regex = /^\d{2}\/\d{2}\/\d{4}$/;

        if (!regex.test(value)) {
            return this.forceCorrection("Formato inválido. Use DD/MM/AAAA");
        }

        const [day, month, year] = value.split("/").map(Number);

        // 🔥 Validar que la fecha exista realmente
        const dateObj = new Date(year, month - 1, day);

        if (
            dateObj.getFullYear() !== year ||
            dateObj.getMonth() !== month - 1 ||
            dateObj.getDate() !== day
        ) {
            return this.forceCorrection("Fecha inválida.");
        }

        // Convertir min y max a Date reales
        const minDate = this.min ? new Date(this.min + "T00:00:00") : null;
        const maxDate = this.max ? new Date(this.max + "T00:00:00") : null;

        if (minDate && dateObj < minDate) {
            return this.forceCorrection("Fecha menor al rango permitido.");
        }

        if (maxDate && dateObj > maxDate) {
            return this.forceCorrection("Fecha mayor al rango permitido.");
        }

        // if (this.blockWeekends) {
        //     const dayWeek = dateObj.getDay();
        //     if (dayWeek === 0 || dayWeek === 6) {
        //         return this.forceCorrection("No se permiten fines de semana.");
        //     }
        // }

        this.clear();
    }

    forceCorrection(message) {
        alert(message);
        this.input.value = "";
        this.input.classList.add("is-invalid");
        setTimeout(() => {
            this.input.focus();
        }, 10);
    }

    invalidate(message) {
        this.input.classList.add("is-invalid");
        this.showMessage(message);
    }
    clear() {
        this.input.classList.remove("is-invalid");
        this.showMessage("");
    }

    showMessage(message) {
        if (!this.messageContainer) return;
        this.messageContainer.textContent = message;
    }
}

export default DateValidator;