import Queries from './Queries.js';

export default class Productivity {
    #sds_base_id;
    #numFicha;
    #fechaIntervencion;
    #typingSeconds;
    #sectionId;
    #userSds;
    #inactivitySeconds;

    get sds_base_id() {
        return this.#sds_base_id;
    }

    set sds_base_id(value) {
        this.#sds_base_id = value;
    }

    get numFicha() {
        return this.#numFicha;
    }

    set numFicha(value) {
        this.#numFicha = value;
    }

    get fechaIntervencion() {
        return this.#fechaIntervencion;
    }

    set fechaIntervencion(value) {
        this.#fechaIntervencion = value;
    }

    set typingSeconds(value) {
        this.#typingSeconds = value;
    }

    get typingSeconds() {
        return this.#typingSeconds;
    }

    get sectionId() {
        return this.#sectionId;
    }

    set sectionId(value) {
        this.#sectionId = value;
    }

    get userSds() {
        return this.#userSds;
    }

    set userSds(value) {
        this.#userSds = value;
    }

    get inactivitySeconds() {
        return this.#inactivitySeconds;
    }

    set inactivitySeconds(value) {
        this.#inactivitySeconds = value;
    }

    /**
     * Almacena los datos de productividad en el servidor
     * @return {Promise<Response>} Response del servidor
     */
    async store(){
        const url = `https://www.trakio.pro/api/v1/store-productivity-sds`;
        // const url = `http://127.0.0.1:8000/api/v1/store-productivity-sds`;

        const Query = new Queries();
        const data = {
            sds_id: this.#sds_base_id,
            file_number: this.#numFicha,
            fecha_intervencion: this.#fechaIntervencion,
            calculation_time: this.#typingSeconds,
            inactivity_time: this.#inactivitySeconds,
            section_id: this.#sectionId,
            user_sds: this.#userSds,
            pages: this.getPagesCount().totalPages,
            active_page: this.getPagesCount().activePage
        }

        try {
            return await Query.axiosPost(url, data);
            } catch (error) {
                console.error('Error:', error);
        }
    }

    getPagesCount(){
        const btnsPagesContainer = document.querySelector("#main_body > div.page-wrapper.chiller-theme.toggled > div > main > div > div > div > div.panel-body > div:nth-child(5) > div > center > table > tbody > tr > td:nth-child(3) > table > tbody > tr");
        if(!btnsPagesContainer) return 0;
        const inputsChildren = btnsPagesContainer.querySelectorAll("td > input");
        // La página activa es la que tiene la clase btn-primary
        const activePage = btnsPagesContainer.querySelector("td > input.btn-info");
        return {
            totalPages: inputsChildren.length,
            activePage: activePage ? activePage.value : null
        };
    }
}