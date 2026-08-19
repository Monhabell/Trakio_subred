import Queries from './Queries.js';
import User from './User.js';

export default class Correction extends Queries {
    #id;
    #status;
    #file_number;

    constructor(id = null, status = null, file_number = null) {
        super();
        this.#id = id;
        this.#status = status;
        this.#file_number = file_number;
    }

    set id(id) {
        this.#id = id;
    }

    get id() {
        return this.#id;
    }

    set status(status) {
        this.#status = status;
    }

    get status() {
        return this.#status;
    }

    set file_number(file_number) {
        this.#file_number = file_number;
    }

    get file_number() {
        return this.#file_number;
    }

    async delete(){
        if(confirm('¿Desea eliminar la corrección?')){
            const url = `/adm/corrections/delete/${this.#id}`;
            return await this.axiosDelete(url);
        }
    }

    async load(){
        const url = `/adm/corrections/`;
        const data = {
            status: this.#status,
            file_number: this.#file_number
        }
        const response = await this.axiosPost(url, data);
        return response.data;
    }

    async updateStatus(){
        const url = `/adm/corrections/${this.#id}`;
        
        const headers = {
            'data' : 
            {
                'status': this.#status
            }
        }

        const response = await this.axiosPut(url, headers);
        return response;
    }

    getCorrectionsType(type){
        const types = {
            1: 'Eliminar ficha',
            2: 'Eliminar página',
            3: 'Cambiar número',
            4: 'Cambiar fecha',
        };

        return types[type];
    }

    async generateHTML(){
        const data = await this.load();
        const user_instance = new User();
        const profileImages = await user_instance.gesiProfiles();

        const getStateSwitch = (isCorrected) => {
            return isCorrected ? 'checked' : '';
        }

        if(data.corrections.length === 0){
            return '<div class="correction" draggable="true">No hay correcciones para mostrar.</div>';
        }

        return data.corrections.map(correction => {
            const pageNumber = correction.tipo_cambio == 2
                ? `<span class = "fw-bold bg-transparent border-0">No. ${correction.no_pag}</span>`
                : '';

            const newNumber = correction.tipo_cambio == 3 
                ? `<span class = "fw-bold bg-transparent border-0">a ${correction.nuevo_numero}</span>`
                : '';
                
            const newDate = correction.tipo_cambio == 4
                ? `<span class = "fw-bold bg-transparent border-0">a ${this.formatDateDMY(correction.nueva_fecha)}</span>`
                : '';

            const profile = profileImages.find(user => () => {
                correction.user.id === user.id
            });
            
            let profileImage = `../img/img_perfil/${correction.user.id}/${profile.url_img}`;

            this.validateImage(profileImage, (exists) => {
                if (!exists) {
                    profileImage = '../img/undraw_profile_1.svg';
                }
            });

            return `
            <div class = "p-2 mt-0">
                <div class="correction bg-dark">
                    <div class="tags w-75">
                        <span class="tag">
                            ${this.getCorrectionsType(correction.tipo_cambio)}
                            ${newNumber} ${newDate} ${pageNumber}
                        </span>

                        <label class="switch">
                            <input type="checkbox" class = "switch-button-corrections" data-correction-id = "${correction.id}" ${getStateSwitch(correction.corrected)}>
                            <div class="slider">
                                <div class="circle">
                                    <svg class="cross" xml:space="preserve" style="enable-background:new 0 0 512 512" viewBox="0 0 365.696 365.696" y="0" x="0" height="6" width="6" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" xmlns="http://www.w3.org/2000/svg">
                                        <g>
                                            <path data-original="#000000" fill="currentColor" d="M243.188 182.86 356.32 69.726c12.5-12.5 12.5-32.766 0-45.247L341.238 9.398c-12.504-12.503-32.77-12.503-45.25 0L182.86 122.528 69.727 9.374c-12.5-12.5-32.766-12.5-45.247 0L9.375 24.457c-12.5 12.504-12.5 32.77 0 45.25l113.152 113.152L9.398 295.99c-12.503 12.503-12.503 32.769 0 45.25L24.48 356.32c12.5 12.5 32.766 12.5 45.247 0l113.132-113.132L295.99 356.32c12.503 12.5 32.769 12.5 45.25 0l15.081-15.082c12.5-12.504 12.5-32.77 0-45.25zm0 0"></path>
                                        </g>
                                    </svg>
                                    <svg class="checkmark" xml:space="preserve" style="enable-background:new 0 0 512 512" viewBox="0 0 24 24" y="0" x="0" height="10" width="10" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" xmlns="http://www.w3.org/2000/svg">
                                        <g>
                                            <path class="" data-original="#000000" fill="currentColor" d="M9.707 19.121a.997.997 0 0 1-1.414 0l-5.646-5.647a1.5 1.5 0 0 1 0-2.121l.707-.707a1.5 1.5 0 0 1 2.121 0L9 14.171l9.525-9.525a1.5 1.5 0 0 1 2.121 0l.707.707a1.5 1.5 0 0 1 0 2.121z"></path>
                                        </g>
                                    </svg>
                                </div>
                            </div>
                        </label>

                        <div class="list-options-dark">
                            <a class="options show-corrections-options" data-correction-id = "${ correction.id }">
                                <svg xml:space="preserve" viewBox="0 0 41.915 41.916"
                                    xmlns:xlink="http://www.w3.org/1999/xlink" xmlns="http://www.w3.org/2000/svg"
                                    id="Capa_1" version="1.1" fill="#000000">
                                    <g stroke-width="0" id="SVGRepo_bgCarrier"></g>
                                    <g stroke-linejoin="round" stroke-linecap="round" id="SVGRepo_tracerCarrier">
                                    </g>
                                    <g id="SVGRepo_iconCarrier">
                                        <g>
                                            <g>
                                                <path
                                                    d="M11.214,20.956c0,3.091-2.509,5.589-5.607,5.589C2.51,26.544,0,24.046,0,20.956c0-3.082,2.511-5.585,5.607-5.585 C8.705,15.371,11.214,17.874,11.214,20.956z">
                                                </path>
                                                <path
                                                    d="M26.564,20.956c0,3.091-2.509,5.589-5.606,5.589c-3.097,0-5.607-2.498-5.607-5.589c0-3.082,2.511-5.585,5.607-5.585 C24.056,15.371,26.564,17.874,26.564,20.956z">
                                                </path>
                                                <path
                                                    d="M41.915,20.956c0,3.091-2.509,5.589-5.607,5.589c-3.097,0-5.606-2.498-5.606-5.589c0-3.082,2.511-5.585,5.606-5.585 C39.406,15.371,41.915,17.874,41.915,20.956z">
                                                </path>
                                            </g>
                                        </g>
                                    </g>
                                </svg>
                            </a>

                            <ul class="p-0 mb-1 text-center w-100 position-absolute" id="${correction.id}-corrections" hidden>
                                <li class="option-squared-danger delete-correction" data-correction-id="${correction.id}">
                                    <form action="" id="form-correct-${correction.id }" method="POST">
                                        <i class="fa-solid fa-xmark fa-sm" title="Eliminar"></i>
                                    </form>
                                </li>
                                <li class="option-squared-primary" title="Editar">
                                    <i class="fa-solid fa-pen-to-square fa-sm"></i>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="correction-content my-2">
                        <p>${correction.receptions.file_number}</p>
                        <p>${correction.receptions.bases.name}</p>
                        <p>${correction.receptions.environment_file.entorno }</p>
                    </div>

                    <div class="stats">
                        <div>
                            <div><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <g stroke-width="0" id="SVGRepo_bgCarrier"></g>
                                    <g stroke-linejoin="round" stroke-linecap="round"
                                        id="SVGRepo_tracerCarrier"></g>
                                    <g id="SVGRepo_iconCarrier">
                                        <path stroke-linecap="round" stroke-width="2" d="M12 8V12L15 15"></path>
                                        <circle stroke-width="2" r="9" cy="12" cx="12"></circle>
                                    </g>
                                </svg>
                                ${this.dateFormatN(correction.created_at) }
                            </div>
                        </div>
                        <div class="viewer">
                            <span title="${this.nameAndLastName(correction.user.name, correction.user.last_name) }">
                                <img src="${profileImage}" alt="Foto de perfil">
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            `;
        }).join('');
    }
}