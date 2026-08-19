import Queries from "./Queries.js";

export default class Package extends Queries {
    #id;
    #status;
    #role;

    constructor(id, status = null) {
        super();
        this.#id = id;
        this.#status = status;
        this.#role = 'admin';
    }

    get id() {
        return this.#id;
    }

    set id(id) {
        this.#id = id;
    }

    get status() {
        return this.#status;
    }

    set status(status) {
        this.#status = status;
    }

    returnEnvironment() {
        const form = document.getElementById(`form-return-${this.#id}`);
        const statusNotReturn = ["0", "1", "2"];
        
        if (statusNotReturn.includes(this.#status)) {
            if (confirm("El paquete no está completamente digitado, ¿desea devolverlo?")) {
                form.submit();
            }
        } else if (this.#status == 5) {
            if (confirm("¿Desea cancelar la devolución del paquete?")) {
                form.submit();
            }
        } else {
            form.submit();
        }
    }

    receiveFromDigitizer() {
        const form = document.getElementById(`form-return-dig-${this.#id}`);
        const statusNotReturn = ["0", "1", "2"];
        
        if (statusNotReturn.includes(this.#status)) {
            if (confirm('El paquete no está completamente digitado, ¿desea recibir el paquete?')) {
                form.submit();
            }
        } else if (this.#status == 5) {
            if (confirm('¿Desea devolver en paquete al digitador?')) {
                form.submit();
            }
        } else {
            form.submit();
        }
    }

    changeStatus() {
        const returnButtons = document.querySelectorAll(".option-package");
        if (returnButtons.length == 0) {
            return;
        }

        returnButtons.forEach((button) => {
            button.addEventListener("click", (e) => {
                this.#id = e.currentTarget.dataset.packageId;
                this.#status = e.currentTarget.dataset.packageStatus;

                const functionsChangeStatus = {
                    "environment": this.returnEnvironment,
                    "digitizer": this.receiveFromDigitizer,
                };

                functionsChangeStatus[e.currentTarget.dataset.optionType].bind(this)();
            });
        });
    }

    events(){
        const showOptions = () => {
            const btnsDropdown = document.querySelectorAll('.show-options-package');
            btnsDropdown.forEach(btn => {
                btn.addEventListener('click', (event) => {
                    const packageId = event.currentTarget.dataset.packageId;
                    const containerOptions = document.getElementById(`${packageId}-options`);
                    this.fadeDropdown(containerOptions);
                });
            });
        }

        const showEditFile = () => {
           document.addEventListener('click', (e) => {
                const button = e.target.closest('.edit-file-button');

                if(button){
                    const collapseId = button.dataset.collapseIdEdit;
                    this.collapseEditPackage(collapseId);
                }
            });
        }

        showEditFile();
        showOptions();
        this.changeStatus();
    }

    async showFilesPackages(role) {
        this.#role = role;
        const url = route("packages.show", {package: this.#id});
        try {
            this.innerTablePackages(await this.axiosGet(url));
        } catch (error) {
            console.error("Error al mostrar el paquete:", error);
        }
    }

    collapseEditPackage(collapseId) {
        const collapseElement = document.getElementById(collapseId);

        if (collapseElement.classList.contains('showCollapse')) {
            collapseElement.classList.remove('showCollapse');
            collapseElement.classList.add('hideCollapse');
        } else {
            collapseElement.classList.remove('hideCollapse');
            collapseElement.classList.add('showCollapse');
        }
    }

    async innerTablePackages(files) {
        const containerId = this.role === 'admin' ? 'card-body-packages' : 'package-table';
        const container = document.getElementById(containerId);
    
        if (this.#role === 'admin') {
            const asign_package_id = document.getElementById('asign_package_id');
            asign_package_id.value = files.data[0].id;
    
            if (files.data[0].packages.observations != null) {
                document.getElementById('observations_asign_package').value = files.data[0].packages.observations;
            }    
        } else if (this.#role === 'digitizer') {
            const titleContainer = document.getElementById('tabletitlepaquete');
            if (titleContainer){
                titleContainer.parentElement.hidden = true;
            }
        }
    
        container.classList.remove('d-flex', 'justify-content-center', 'align-items-center');
        container.innerHTML = this.generateHTMLFiles(files.data);
        this.events();
    }    

    generateHTMLFiles(data) {
        let table = '';        
        
        const adminTable = () => { 
            table = 
                `<table class="table table-bordered" id="tablePackages" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No. ficha</th>
                            <th>Formato</th>
                            <th>Cantidad</th>
                            <th>Entregado por</th>
                            <th>Opciones</th>
                        </tr>
                    </thead>
                <tbody>`;

            files.forEach((file) => {
                let iconClass = file.typed_by
                    ? `<i class="fa-solid fa-circle-check fa-beat fa-xs" style="color: #014d00;"></i>`
                    : `<i class="fa-solid fa-circle-xmark fa-beat fa-xs" style="color: #800000;"></i>`;

                table += `
                <tr>
                    <td>${file.file_number} <span class = "ms-1">${iconClass}</span></td>
                    <td>${file.bases.name}</td>
                    <td>${file.quantity}</td>
                    <td>${file.user.name} ${file.user.last_name}</td>
                    <td>
                        <a class="btn btn-sm btn-primary btn-edit-file-package" data-collapse-id-edit = "${file.file_number}${file.id}" data-environment-package = "${file.environment}">
                            <i class="fa-solid fa-pen-to-square fa-xl"></i>
                        </a>
                        <button type="button" class ="btn btn-sm btn-danger">
                            <i class="fa-solid fa-square-xmark fa-xl"></i>
                        </button>    
                    </td>
                </tr>
                <tr class="p-0">
                    <td colspan="5" class="p-0">
                        <div class="hideCollapse" id="${file.file_number}${file.id}">
                            <form class="container-fluid px-0" method="POST" id = "form-${file.file_number}${file.id}">
                                <div class="input-group me-2">
                                    <input type="number" class="form-control" id="file_number" value="${file.file_number}" style="width = 20%">
                                    <select class="form-select w-25" name="format_id" id = "format_id"  name="format_id" style="width = 10%">
                                        ${file.format_id}
                                    </select>
                                    <input type="number" class="form-control" id="quantity" value="${file.quantity}">
                                    <input type="text" class="form-control" value="${file.user.name} ${file.user.last_name}" disabled style="width = 20%">
                                    <input type="button" class="btn btn-sm btn-success" value ="Guardar" onclick = "editFilePackage('form-${file.file_number}${file.id}', '${file.id}', this)">
                                </div>
                            </form>
                        </div>
                    </td>
                </tr>`;
            });

            table += `
                </tbody>
            </table>`;
        }
        
        const digitizerTable = () => {
            const storedTotalMinutes = Number(sessionStorage.getItem('productivityAccumulatedTime') || 0);
            const formatAccumulatedTime = (totalSeconds) => {
                if (!totalSeconds || totalSeconds <= 0) {
                    return '+0s';
                }

                const hours = Math.floor(totalSeconds / 3600);
                const minutes = Math.floor((totalSeconds % 3600) / 60);
                const seconds = totalSeconds % 60;

                if (hours > 0) {
                    return `+${hours}h ${minutes}m`;
                }

                if (minutes > 0) {
                    return `+${minutes}m ${seconds}s`;
                }

                return `+${seconds}s`;
            };

            table += `<div class="d-flex justify-content-end align-items-center mb-3">
                        <div id="productivity-time-summary" class="${storedTotalMinutes > 0 ? '' : 'd-none'} border border-success rounded-pill px-3 py-2 bg-success bg-opacity-10 animate-counter-up" data-total-minutes="${storedTotalMinutes}">
                            <span class="text-success fw-semibold me-2 small">Tiempo:</span>
                            <span id="productivity-time-value" class="text-success fw-bold fs-4">${formatAccumulatedTime(storedTotalMinutes)}</span>
                        </div>
                    </div>
                    <table class="table table-dark border-secondary package-table">
                        <thead>
                            <tr>
                                <th>Ficha</th>
                                <th>Base</th>
                                <th>Usuarios</th>
                                <th>Observaciones</th>
                                <th>Actualización</th>
                                <th class="col-secondary"></th>
                                <th>Acciones</th>
                                <th class="col-secondary">Orden</th>
                                <th>Tiempo de digitación</th>
                            </tr>
                        </thead>

                        <tbody>`

            data.files.forEach((file) => {
                const fileId = file.id;
                const isUpdate = file.productivity?.type === 1 || file.is_update;
                const isEditable = file.productivity?.dig_id === data.current_user;
                const headerTime = Number(file.bases?.header_time ?? 0);
                const bodyTime = Number(file.bases?.body_time ?? 0);
                const initialQuantityUsers = Number(file.productivity?.quantity_users ?? 0);
                const calculationMinutes = headerTime + (bodyTime * initialQuantityUsers);
                let profileImage = '';
                let actionEdit = '';
                let observations = '';
                let rowEditFile = '';

                const quantityUsersRow = file.productivity?.quantity_users 
                                        ?? `<input type="number" class="input-quantity form-control form-control-sm" id="quantity-users-${fileId}" value="0">`;
                const checkBoxIsUpdate = (isDisabled, isEditing = false) => {
                    const checkBoxId = isEditing ? 'is-update-new-' : 'is-update-';
                    const fileIdCheckbox = isEditing ? file.productivity?.id : fileId;
                    return  `
                        <div class="checkbox-wrapper-34">
                            <input type='checkbox' class='tgl tgl-ios Act_New' id='${checkBoxId}${fileIdCheckbox}' value="1" ${isDisabled ? 'disabled' : ''} ${isUpdate ? 'checked' : ''}>
                            <label class='tgl-btn bg-gradient' for='${checkBoxId}${fileIdCheckbox}'></label>
                        </div>`
                }

                if (file.productivity && file.productivity?.observations === null) {
                    observations = '';
                }else if(!file.productivity){
                    observations = `<textarea class="form-control input-observations" id="observations-${fileId}" rows="2" placeholder ="Escriba las novedades que presente la ficha"></textarea>`;
                }else if(file.productivity && file.productivity?.observations !== null){
                    observations = file.productivity?.observations;
                }
                
                if (isEditable) {
                    actionEdit = `
                        <button type="button" id="${file.productivity?.id}" class="btn btn-primary btn-sm ms-3 py-0 px-2 edit-file-button rounded-pill" title= "Editar" data-collapse-id-edit="collapse-edit-${file.productivity?.id}">
                            <i class="fas fa-pencil-alt fa-sm"></i>
                        </button>`

                    rowEditFile = `
                    <tr>
                        <td colspan="9" class="p-0">
                            <div class="package-table__edit hideCollapse d-flex justify-content-between" id="collapse-edit-${file.productivity?.id}">
                                <div>${file.file_number}</div>
                                <div>${file.bases.name}</div>
                                <div><input type="number" class="form-control" id="quantity-users-new-${file.productivity?.id}" value="${quantityUsersRow}"></div>
                                <div><textarea class="form-control input-observations" id="observations-new-${file.productivity?.id}" rows="2" placeholder ="Escriba las novedades que presente la ficha">${observations}</textarea></div>
                                <div>${checkBoxIsUpdate(false, true)}</div>
                                <div><button type="button" data-file-id="${file.productivity?.id}" class="btn btn-sm btn-success btn-update-productivity">Actualizar</button>
                            </div>
                        </td>
                    </tr>`;
                }

                if (file.productivity?.data_user) {
                    let baseNameImage = file.productivity?.data_user.url_img;
                    let imageUrl = `${baseUrl}/${file.productivity?.dig_id}/${baseNameImage}`;
                    let digitizerName = this.nameAndLastName(file.productivity?.user.name, file.productivity?.user.last_name);
                    profileImage += `<img src="${imageUrl}" alt="Foto de perfil" class="box-productivity-photo" title="${digitizerName}">`;
                }

                const statusBadge = file.productivity
                    ? `<span class="badge-status-pill badge-status-pill--done">Reportada</span>`
                    : `<span class="badge-status-pill badge-status-pill--pending">Pendiente</span>`;

                const formattedDate = file.calculation_date
                    ? new Date(file.calculation_date).toLocaleString('es-CO', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' })
                    : '--';

                table += `
                    <tr class="${file.productivity ? '' : 'file-completed-row'}">
                        <td>${file.file_number} ${statusBadge}</td>
                        <td>${file.bases.name}</td>
                        <td>${quantityUsersRow}</td>
                        <td>${observations}</td>
                        <td>${checkBoxIsUpdate(file.productivity)}</td>
                        <td class="col-secondary">${profileImage}</td>
                        <td>
                            <div class="d-flex">
                                <div class="checkbox-wrapper-26">
                                    <input
                                        type="checkbox"
                                        class="productivity-report"
                                        id="_checkbox-${fileId}"
                                        data-package-id = ${file.package_id}
                                        data-file-id = ${fileId}
                                        data-base-id = ${file.format_id}
                                        data-header-time = ${headerTime}
                                        data-body-time = ${bodyTime}
                                        data-calculation-minutes = ${calculationMinutes}
                                        ${file.productivity ? 'checked disabled' : ''}>

                                    <label for="_checkbox-${fileId}" title="${file.productivity ? 'Ficha reportada' : 'Reportar ficha'}">
                                        <div class="tick_mark"></div>
                                    </label>
                                </div>
                               ${actionEdit}
                            </div>
                        </td>
                        <td class="col-secondary">${file.order_file ?? ''}</td>
                        <td>
                            <span class="digitization-time">${formatAccumulatedTime(Number(file.calculation_time ?? 0))}</span>
                            <small class="digitization-date d-block">${formattedDate}</small>
                        </td>
                    </tr>
                    ${rowEditFile}
                   `;
            });

            table += `</tbody>
                    </table>`
        }
        
        const tableRole = {
            'admin': adminTable,
            'digitizer': digitizerTable
        }
        tableRole[this.#role]();
        return table;
    }

    async totalHours(packageId) {
        const url = route('packages.totalHours', { package: packageId });

        try {
            const response = await this.axiosGet(url);
            
            return response.totalHours;
        } catch (error) {
            console.error(`Error fetching total hours for package ${packageId}:`, error);
            return 0;
        }
    }
}