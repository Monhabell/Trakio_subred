import Queries from "./Queries.js";
import { route } from "ziggy-js";

export default class Reception extends Queries {
    #model;
    #type;
    #environment;

    constructor(model = null) {
        super();
        this.#model = model;
        this.formReception = document.getElementById('formReception');
    }

    set type(type) {
        this.#type = type;
    }
    
    get type() {
        return this.#type;
    }

    set model(model) {
        this.#model = model;
    }

    get model() {
        return this.#model;
    }

    get environment() {
        return this.#environment;
    }

    set environment(environment) {
        this.#environment = environment;
    }

    async returnToFix(ids, type, target) {
        const pluralWord = ids.length > 1 ? 'las fichas seleccionadas' : 'la ficha seleccionada';
        const messageConfirmation = `Se devolverá ${pluralWord} para corrección. ¿Desea continuar?`;

        if (confirm(messageConfirmation)) {
            const url = route('receptions.returnToFix');

            const data = {
                ids: [...new Set(ids)], // Eliminar duplicados
                type
            }

            return await this.axiosPost(url, data);
        } else {
            setTimeout(() => {
                target.innerHTML = `<i class="fa-regular fa-square-minus"></i><span class = "fw-bold ms-1 bg-transparent border-0">Devolver</span>`;
                target.classList.remove('disabled');
            }, 500);
            return false;
        }
    }

    /**
     * Esta función se utiliza para entornos y para administradores
     * @param {*} checkBoxes 
     */
    showOptions(checkBoxes) {
        const selectedCheckboxes = Array.from(checkBoxes).filter(item => item.checked);
        const formReception = this.formReception;
        const btnReceiveBox = document.getElementById('btn-receive-box');
        btnReceiveBox.innerHTML = '';

        // Validar que el lote de los elementos seleccionados sea el mismo
        const esLoteUnico = () => {
            const batchNumbersSelected = selectedCheckboxes.map(input => input.getAttribute('data-batch-number'));
            const uniqueBatchNumbers = [...new Set(batchNumbersSelected)];
            return uniqueBatchNumbers.length === 1;
        }

        const createButtons = () => {
            //Botón recibir
            const newButtonReceive = document.createElement('button');
            newButtonReceive.type = 'submit';
            newButtonReceive.classList.add('btn', 'btn-sm', 'btn-success', 'px-4', 'btn-loader');
            newButtonReceive.title = 'Recibir ficha(s) seleccionada(s)';
            newButtonReceive.innerHTML = `<i class="fa-regular fa-square-check"></i>
                                            <span class = "fw-bold ms-1 bg-transparent border-0">Recibir</span>`;

            newButtonReceive.addEventListener('click', async (e) => {
                e.preventDefault();
                if(esLoteUnico() || this.#model == 'environment') {
                    const inputBatchNumber = document.getElementById('batch_number');
                    if (inputBatchNumber) {
                        inputBatchNumber.value = selectedCheckboxes[0].getAttribute('data-batch-number');
                    }
                    formReception.action = this.#type === 'sisco' ? routeReceiveReceptionSisco : routeReceiveReception;
                    formReception.submit();
                }else{
                    const button = e.currentTarget;
                    alert('Las fichas seleccionadas pertenecen a diferentes lotes. Por favor, seleccione fichas del mismo lote para continuar.');
                    setTimeout(() => {
                        button.innerHTML = `<i class="fa-regular fa-square-check"></i>
                                            <span class = "fw-bold ms-1 bg-transparent border-0">Recibir</span>`;
                        button.classList.remove('disabled');
                    }, 500);
                }
            });

            btnReceiveBox.appendChild(newButtonReceive);
            createSpanCount();

            if (this.#model != 'environment' && this.#model != null) {
                //Botón devolver
                const newButtonDelete = document.createElement('button');
                newButtonDelete.type = 'button';
                newButtonDelete.classList.add('btn', 'btn-sm', 'btn-info', 'px-3', 'btn-loader');
                newButtonDelete.title = 'Devolver ficha(s) seleccionada(s)';
                newButtonDelete.innerHTML = `<i class="fa-regular fa-square-minus"></i>
                                                <span class = "fw-bold ms-1 bg-transparent border-0">Devolver</span>`;

                newButtonDelete.addEventListener('click', async (e) => {
                    const ids = selectedCheckboxes.map(input => input.value);
                    const type = selectedCheckboxes[0].getAttribute('data-type') || 'normal';
                    const result = await this.returnToFix(ids, type, e.currentTarget)
                    if (result) location.reload();
                });

                btnReceiveBox.appendChild(newButtonDelete);
            }
        }

        const createSpanCount = () => {
            const pluralWords = selectedCheckboxes.length > 1 ? 'fichas seleccionadas' : 'ficha seleccionada';
            const count = document.createElement('span');
            count.classList.add('icon-box-red', 'py-2', 'mx-4', 'px-3', 'border-0', 'rounded-2');
            count.innerHTML = `${selectedCheckboxes.length} ${pluralWords}`;
            btnReceiveBox.appendChild(count);
        }

        if (selectedCheckboxes.length > 0) {
            createButtons();
            this.buttonsLoader();
        }
    }

    selectAllReceptions() {
        const formReception = this.formReception;

        formReception.addEventListener('change', (e) => {
            const selectAllBtn = e.target;
            
            const idName = selectAllBtn.id;
            if(idName !== 'selectAll' && idName !== 'selectAllSisco') return;
            
            const idNameTable = this.#type === 'sisco' ? 'table-receptions-sisco' : 'table-receptions-normal';
            selectCheckboxes(idNameTable, selectAllBtn);
        });

        const selectCheckboxes = (idNameTable, selectAllBtn) => {
            const table = document.getElementById(idNameTable);
            const name = this.#type === 'sisco' ? 'receptionsFilesSisco[]' : 'receptionsFiles[]';
            const checkBoxes = table.querySelectorAll(`input[name="${name}"]`);
            checkBoxes.forEach(checkBox => {
                checkBox.checked = selectAllBtn.checked;
            });
            this.showOptions(checkBoxes);
        }

        selectCheckboxes('table-receptions-normal', document.getElementById('selectAll'));
    }

    selectRangeReception() {
        const formReception = this.formReception;

        formReception.addEventListener('change', (e) => {
            const button = e.target;
            //const type = button.dataset.type;
            if(button.name !== 'receptionsFiles[]' && button.name !== 'receptionsFilesSisco[]') return;
            const idNameTable = this.#type === 'sisco' ? 'table-receptions-sisco' : 'table-receptions-normal';
            selectCheckboxes(idNameTable);
        });

        const selectCheckboxes = (idNameTable) => {
            const table = document.getElementById(idNameTable);
            const name = this.#type === 'sisco' ? 'receptionsFilesSisco[]' : 'receptionsFiles[]';
            const checkBoxes = table.querySelectorAll(`input[name="${name}"]`);
            let lastChecked = null;
            const receptionInstance = this;

            checkBoxes.forEach(checkbox => {
                checkbox.addEventListener('click', function(e) {
                    if (e.shiftKey && lastChecked) {
                        let inBetween = false;
                        checkBoxes.forEach(box => {
                            if (box === this || box === lastChecked) {
                                inBetween = !inBetween;
                            }
                            if (inBetween) {
                                box.checked = inBetween;
                            }
                        })
                    }
                    lastChecked = this;

                    receptionInstance.showOptions(checkBoxes);
                    //updateCallback(selectedCounted);
                }, { once: true });
            });
        }

        selectCheckboxes(this.#type === 'sisco' ? 'table-receptions-sisco' : 'table-receptions-normal');
    }

    async getReceptions(){
        try {
            const response = await this.axiosGet(route('receptions.list', { environment: this.#environment }));
            
            if(!response.success){
                throw new Error('Error al obtener las recepciones');
            }

            return response.data;
        } catch (error) {
            console.log('Error al obtener las recepciones: ', error);
        }
    }

    async index(){
        this.showSkeleton();

        try {
            const response = await this.getReceptions();
    
            if(response.receptions.length > 0){
                this.showNormal(response.receptions, response.environment.formats);
            }
    
            if (response.receptions_sisco.length > 0) {
                this.showSisco(response.receptions_sisco, response.environment.formats);
            }
    
            this.selectRangeReception();
            this.selectAllReceptions();
        } finally  {
            this.hideSkeleton();
        }
    }

    showNormal(receptions, formats){
        const content = document.getElementById('reception-content');
        if (content) content.style.display = 'block';

        let html = '';
        const tbody = document.getElementById('tbody-receptions-normal');

        receptions.forEach((reception, key) => {
            const prevBatch = key > 0 ? receptions[key - 1].batch_number : 0;
            const bases = formats.map(format => {
                return `<option value="${format.id}" ${format.id == reception.format_id ? 'selected' : ''}>${format.name}</option>`;
            }).join('');

            html += `<tr class="${reception.order_file == 1 ? 'border-top' : ''} ${prevBatch != reception.batch_number ? 'border-top border-danger' : ''}">
                        <td>
                            <div class="form-check ps-0">
                                <input type="checkbox" class="form-check-input shadow border-2"
                                    name="receptionsFiles[]" data-type="normal"
                                    value="${reception.id}"
                                    data-batch-number="${reception.batch_number}">
                            </div>
                        </td>
                        <td>
                            <input type="number" class="form-control form-control-sm" value="${reception.order_file}" id="order_file_${reception.id}" style="width: 80px;" />
                            <small class="text-muted">
                                Lote ${reception.batch_number}
                            </small>
                            <input type="hidden" id="batch_number_${reception.id}" name="batch_number" value="${reception.batch_number}">
                        </td>
                        <td>
                            <div class="d-flex flex-column">
                                <span class="bg-transparent fw-bold">${reception.file_number}</span>
                                ${reception.localidad ? `<small class="text-muted">${reception.localidad.name}</small>` : ''}
                            </div>
                        </td>
                        <td>
                            <div class="d-flex flex-column">
                                <select class="form-select form-select-sm" id="format_${reception.id}" style="width: 200px;">
                                    ${bases}
                                </select>
                                ${reception.users && reception.users.length > 0 ? `<small class="text-muted">${reception.users.map(professional => this.properNouns(this.nameAndLastName(professional.name, professional.last_name))).join(' - ')}</small>` : ''}
                            </div>
                        </td>
                        <td>
                            <input type="number" class="form-control form-control-sm" id="quantity_users_${reception.id}" value="${reception.quantity_users ?? ''}" style="width: 80px;" />
                        </td>
                        <td>
                            <span class="bg-transparent fw-bold">
                                ${reception.user ? this.properNouns(this.nameAndLastName(reception.user.name, reception.user.last_name)) : ''}
                            </span></br>
                            <small>
                                ${this.formatDateHourFromISO(reception.created_at)}
                            </small>
                        </td>
                        <td>${reception.fecha_intervencion ? this.formatDateDMY(reception.fecha_intervencion) : ''}</br></td>
                        <td>
                            <button type="button" class="btn btn-primary btn-sm update-reception" data-id="${reception.id}">
                                <i class="fa-solid fa-save"></i>
                            </button>
                        </td>
                    </tr>`
        });
        
        tbody.innerHTML = html;
    }

    showSisco(receptions, formats){
        const content = document.getElementById('reception-content');
        if (content) content.style.display = 'block';
        
        let html = '';
        const tbody = document.getElementById('tbody-receptions-sisco');

        receptions.forEach((reception, key) => {
            const prevBatch = key > 0 ? receptions[key - 1].batch_number : 0;
            const bases = formats.map(format => {
                if(format.name.toLowerCase().includes('sisco')) {
                    return `<option value="${format.id}" ${format.id == reception.format_id ? 'selected' : ''}>${format.name}</option>`;
                }
            }).join('');

            html += `
                <tr class="${reception.order_file == 1 ? 'border-top' : ''} ${prevBatch != reception.batch_number ? 'border-top border-primary' : ''} ">
                    <td>
                        <div class="form-check ps-0">
                            <input type="checkbox" class="form-check-input shadow border-2"
                                name="receptionsFilesSisco[]" data-type="sisco" value="${reception.id}"
                                data-batch-number="${reception.batch_number}">
                        </div>
                    </td>
                    <td>
                        <input type="number" class="form-control form-control-sm" value="${reception.order_file}" id="order_file_${reception.id}_sisco" style="width: 80px;" />
                        <small class="text-muted">
                            Lote ${reception.batch_number}
                        </small>
                        <input type="hidden" id="batch_number_${reception.id}_sisco" name="batch_number" value="${reception.batch_number}">
                    </td>
                    <td>
                        <div class="d-flex flex-column">
                            ${reception.consecutivo_sisco}
                            ${reception.localidad ? `<small class="text-muted">${reception.localidad.name}</small>` : ''}
                        </div>
                    </td>
                    <td>
                        <div class="d-flex flex-column">
                            <select class="form-select form-select-sm" id="format_${reception.id}_sisco" style="width: 200px;">
                                ${bases}
                            </select>

                            ${reception.users && reception.users.length > 0 ? `<small class="text-muted">${reception.users.map(user => this.properNouns(this.nameAndLastName(user.name, user.last_name))).join(' - ')}</small>` : ''}
                        </div>
                    </td>
                    <td>
                        <span class="bg-transparent fw-bold">
                                ${reception.delivered_by ? this.properNouns(this.nameAndLastName(reception.delivered_by.name, reception.delivered_by.last_name)) : ''}
                            </span></br>
                            <small>
                                ${this.formatDateHourFromISO(reception.created_at)}
                        </small>
                    </td>
                    <td>${reception.intervention_date ? this.formatDateDMY(reception.intervention_date) : ''}</br></td>
                    <td>
                        <button type="button" class="btn btn-primary btn-sm update-reception" data-id="${reception.id}" data-type="sisco">
                            <i class="fa-solid fa-save"></i>
                        </button>
                    </td>
                </tr>
            `;
        });
        
        tbody.innerHTML = html;
    }

    update() {
        const formReception = this.formReception;

        formReception.addEventListener('click', async (e) => {
            let button;

            try {
                button = e.target.closest('.update-reception');
                if (!button) return;

                this.buttonLoader(button);

                const receptionId = button.dataset.id;
                const type = button.dataset.type || 'normal';

                const suffix = type === 'sisco' ? '_sisco' : '';

                const orderFile = document.getElementById(`order_file_${receptionId}${suffix}`)?.value;
                const formatId = document.getElementById(`format_${receptionId}${suffix}`)?.value;
                const batchNumber = document.getElementById(`batch_number_${receptionId}${suffix}`)?.value;
                const quantityUsers = document.getElementById(`quantity_users_${receptionId}${suffix}`)?.value;

                const routeName = type === 'sisco'
                    ? 'receptions.sisco.update'
                    : 'receptions.update';

                const url = route(routeName, { reception: receptionId });

                const data = {
                    order_file: orderFile,
                    format_id: formatId,
                    batch_number: batchNumber,
                    quantity_users: quantityUsers
                };

                const result = await this.axiosPost(url, data);

                if (result.data.success) {
                    this.index();
                    this.rowAnimation(button.closest('tr'));
                }

                if (result.data.errors) {
                    alert('Error al actualizar la ficha: ' + Object.values(result.data.errors).flat().join(' '));
                }

            } catch (error) {
                if (error.response?.data?.errors) {
                    alert('Error al actualizar la ficha: ' + Object.values(error.response.data.errors).flat().join(' '));
                } else {
                    alert('Error al actualizar la ficha. Por favor, inténtelo de nuevo.');
                }

                console.error('Error:', error);
                this.normalizeButton(button, '<i class="fa-solid fa-save"></i>');
            }
        });
    }
    
    rowAnimation(row){
        if(!row) return;

        row.classList.add('row-animated');
        
        setTimeout(() => {
            row.classList.remove('row-animated');
        }, 1500);
    }

    showSkeleton() {
        const skeleton = document.getElementById('skeleton-reception');
        if (skeleton) skeleton.style.display = 'block';
    }

    hideSkeleton() {
        const skeleton = document.getElementById('skeleton-reception');
        if (skeleton) skeleton.style.display = 'none';
    }
}