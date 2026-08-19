import Queries from "./Queries.js";
import { route } from 'ziggy-js';

export default class ConsecutivoPermission extends Queries {
    #id;
    #durationMinutes;

    constructor(id = null, durationMinutes = null) {
        super();
        this.#id = id;
        this.#durationMinutes = durationMinutes;
    }

    get id() {
        return this.#id;
    }

    set id(id) {
        this.#id = id;
    }

    get durationMinutes() {
        return this.#durationMinutes;
    }

    set durationMinutes(durationMinutes) {
        this.#durationMinutes = durationMinutes;
    }

    async approve() {
        const url = route('consecutivo.permission.approve', { consecutivoPermission: this.#id });
        const response = await this.axiosPatch(url, { duration_minutes: this.#durationMinutes });
        return response.message;
    }

    async reject() {
        const url = route('consecutivo.permission.reject', { consecutivoPermission: this.#id });
        const response = await this.axiosPatch(url);
        return response.message;
    }

    async estado() {
        const url = route('consecutivo.permission.status');
        const response = await this.axiosGet(url);
        return response;
    }
}
