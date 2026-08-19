export default class ButtonNewRegister {
    constructor(config) {
        this.inputToWatch = document.getElementById(config.inputId);
        // Selector por valor exacto
        this.submitBtn = document.querySelector('input[value="+ Nuevo registro"]');

        if (!this.inputToWatch || !this.submitBtn) return;

        this.init();
    }

    init() {
        // Escuchamos ambos para cubrir escritura y cambios por script/selects
        const events = ["input", "change"];
        events.forEach(event => {
            this.inputToWatch.addEventListener(event, () => this.validate());
        });

        this.validate(); // Ejecución inicial
    }

    validate() {
        const isEmpty = this.inputToWatch.value.trim() === "";
        this.submitBtn.disabled = isEmpty;
        this.submitBtn.style.opacity = isEmpty ? "0.5" : "1";
        this.submitBtn.style.cursor = isEmpty ? "not-allowed" : "pointer";
        this.submitBtn.style.pointerEvents = isEmpty ? "none" : "auto";
    }
}