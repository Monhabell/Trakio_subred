import Queries from "./Queries.js";
import { route } from 'ziggy-js';

export default class OtherTimes extends Queries {
    #id;
    #isApproved;

    constructor(id, isApproved) {
        super();
        this.#id = id;
        this.#isApproved = isApproved;
    }

    get id() {
        return this.#id;
    }

    set id(id) {
        this.#id = id;
    }

    get isApproved() {
        return this.#isApproved;
    }

    set isApproved(isApproved) {
        this.#isApproved = isApproved;
    }

    async update() {
        const url = route('othertimes.update', {otherTime: this.#id});

        const headers = {
            'isApproved': this.#isApproved
        }

        const response = await this.axiosPatch(url, headers);
        return response.message;
    }
}