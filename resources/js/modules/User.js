import Queries from './Queries.js';
import { route } from 'ziggy-js';

export default class User extends Queries {
    #id;
    #name;

    constructor(id, name) {
        super();
        this.#id = id;
        this.#name = name;
    }

    get id() {
        return this.#id;
    }

    set id(id) {
        this.#id = id;
    }

    get name() {
        return this.#name;
    }

    set name(name) {
        this.#name = name;
    }

    async getTypedUser(name) {
        const url = '/adm/users/getDigitizer';
        const data = { user: name }
        const user = await this.axiosPost(url, data);
        return user;
    }

    async filter(environmentId){
        try {
            const url = route('users.filter', { environment_id: environmentId });
            const user = await this.axiosGet(url);
            return user.data;
        } catch (error) {
            console.error(error);
            throw new Error('Ha ocurrido un error');
        }
    }

    async gesiProfiles (){
        try {
            const url = route('profile.gesi');
            const users = await this.axiosGet(url);
            return users.data;
        } catch (error) {
            console.error(error);
            throw new Error('Ha ocurrido un error');
        }
    }
}