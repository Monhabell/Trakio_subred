import Query from './Queries';
import { route } from 'ziggy-js';

export default class Format extends Query {

    constructor() {
        super();
    }

    /**
     * Retrieves a list of all environments.
     * @returns {Promise<Array|Error>} - A promise that resolves with an array of environment or rejects with an error.
     */
    async show(){
        const url = route('environment.index');
    
        try {
            const response = await this.axiosGet(url);
    
            return response.data;
        } catch (error) {
            console.error("Error:", error);
            return error;
        }
    }
}
