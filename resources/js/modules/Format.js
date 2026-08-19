import Query from './Queries';
import { route } from 'ziggy-js';

export default class Format extends Query {
    #id;

    constructor(id) {
        super();
        this.#id = id;
    }

    async getById() {

        try {
            const url = route('format.show', {format: this.#id});
            const format = await this.axiosGet(url);

            return format;
        } catch (error) {
            console.error(error);
        }
    }

    /**
     * Retrieves a list of bases associated with a specific environment.
     *
     * @param {number} environmentId - The unique identifier of the environment.
     * @returns {Promise<Array|Error>} - A promise that resolves with an array of bases or rejects with an error.
     */
    async getByEnvironment(environmentId){
        const url = route('environment.bases', {environment: environmentId});
    
        try {
            const response = await this.axiosPost(url);
    
            return response.data.bases;
        } catch (error) {
            console.error("Error:", error);
            return error;
        }
    }
}