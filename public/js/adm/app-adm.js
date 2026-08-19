const TOKEN = document.querySelector('meta[name="csrf-token"]').content;
const CARD_BODY_PACKAGES = document.getElementById('card-body-packages');

function setLoader(container) {
    container.classList.add('justify-content-center', 'align-items-center');
    container.innerHTML = `<div class="loader">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 66 66" height="100px" width="100px" class="spinner">
                                <circle stroke="url(#gradient)" r="20" cy="33" cx="33" stroke-width="1" fill="transparent" class="path"></circle>
                                <linearGradient id="gradient">
                                    <stop stop-opacity="1" stop-color="#fe0000" offset="0%"></stop>
                                    <stop stop-opacity="0" stop-color="#af3dff" offset="100%"></stop>
                                </linearGradient>
                            </svg> 
                        </div>`;
}

function properNouns(names, last_name = '') {
    const separate_names = names.split(" ");
    const separate_last_names = last_name.split(" ");
    let full_name = '';

    separate_names.forEach(name => {
        full_name += `${name.charAt(0).toUpperCase()}${name.slice(1).toLowerCase()} `;
    });

    separate_last_names.forEach(last_name => {
        full_name += `${last_name.charAt(0).toUpperCase()}${last_name.slice(1).toLowerCase()} `;
    });

    return full_name;
}

function formatDate(dateString) {
    const date = new Date(dateString);
    const options = { day: '2-digit', month: '2-digit', year: 'numeric' };
    return date.toLocaleDateString('es-ES', options);
}

function existElement(idElement) {
    const byDocumentId = document.getElementById(idElement);
    return byDocumentId;
}

async function queryFetch(package_id, container) {    
    try {
        if (!package_id) {
            location.reload();
        }
        const url = route('packages.show', {package: package_id});
        const response = await axios.get(url, {
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        });
        
        if (response) {
            const html = await innerTablePackages(response.data.data);
            container.innerHTML = html;
        }
        
    }catch(error){
        console.error('Error:', error);
        throw error;
    };
}

function quantitySelectedFiles() {
    if (existElement('tableReceptions')) {
        const checkBoxesTable = document.querySelectorAll('input[name="receptionsFiles[]"]');

        checkBoxesTable.forEach(checkbox => {
            checkbox.addEventListener('change', countSelectedFiles);
        });
    }
}

async function getBasesByEnvironment(environmentId) {
    const url = `/formats/${environmentId}`;

    try {
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': TOKEN
            },
        });

        if (!response.ok) {
            throw new Error('Error al obtener las bases');
        }

        const data = await response.json();
        return data;

    } catch (error) {
        console.error('Error:', error);
        return null;
    }
}

async function innerTablePackages(data) {
    if(!data || !data.files || data.files.length === 0) {
        return `<div class="d-flex flex-column align-items-center">
                    <i class="fa-solid fa-box-open fa-4x mb-3" style="color: #6c757d;"></i>
                    <p class="text-muted">No se encontraron fichas en este paquete</p>
                </div>`;
    }
    
    let table = '';
    const packageId = data.files[0].package_id;
    const packageStatus = data.files[0].packages.status;
    const packageType = data.files[0].packages.type;
    const nonEditPackage = packageStatus == 5 || packageStatus == 6;
    const cardHeader = document.getElementById('card-header-packages');
    const formats = await getBasesByEnvironment(data.files[0].environment);
    
    const initMessage = () => {
        const container_init_package = document.getElementById('container_init_package');
        container_init_package.classList.remove('d-flex');
        container_init_package.classList.add('d-none');
    }

    const timePackage = () => {
        let timeEnc = 0;
        let timeUser = 0;

        data.files.forEach(format => {
            timeEnc += parseInt(format.bases ? format.bases.header_time : format.format.header_time);
            if (!format.quantity_users) {
                timeUser += format.quantity * (format.bases ? format.bases.body_time : format.format.body_time);
            } else if(format.quantity_users && format.quantity_users > 0){
                timeUser += format.quantity_users * (format.bases ? format.bases.body_time : format.format.body_time);
            }
        });

        return (timeEnc + timeUser) / 60;
    }

    const setPackageInfo = () => {
        document.getElementById('package-info').innerHTML = `
                    <div class="rounded">
                        <span class="badge d-flex flex-column p-2">
                            <p class = "fs-6 mb-0">${timePackage().toFixed(1)} hrs</p>
                            <small class = "fw-bold text-dark">Tiempo aproximado</small>
                        </span>
                    </div>

                    <div style="display: none;">
                        <button class="btn btn-primary" type="button" title="Añadir ficha a paquete" 
                        ${packageStatus > 4 ? 'disabled' : 'data-bs-toggle="collapse" data-bs-target="#formAddFilePackage" aria-expanded="false"'}>
                            <i class="fa-solid fa-circle-plus fa-xl"></i>
                        </button>
                    </div>

                    <div class="rounded">
                        <span class="badge d-flex flex-column p-2">
                            <p class = "fs-6 mb-0">${data.files[0].packages.num_package}</p>
                            <small class = "fw-bold text-dark">Paquete</small>
                        </span>
                    </div>
        `;
    }

    const setOptionsBases = (format_id = null) => {
        let options = "";
        for (const format of formats.bases) {
            const selected = format_id == format.id ? "selected" : "";
            options += `<option value = "${format.id}" ${selected}>${format.name}</option>`;
        }
        
        return options;
    }

    if (existElement('form_assigment')) {
        document.getElementById('asign_package_id').value = packageId;
        if (data.files[0].packages !== null) {
            document.getElementById('observations_asign_package').value =
                data.files[0].packages.observations
        }
    }

    if (existElement('formAddFilePackage') && packageType === 'basic') {   
        // Obtener al menos un id de profesional asociado a una ficha del paquete
        let professionalId = '';
        for (const file of data.files) {
            if (file.users && Array.isArray(file.users) && file.users.length > 0) {
                professionalId = file.users[0].id;
                break;
            }
        }

        // Obtener el orden máximo actual en el paquete
        let maxOrder = 0;
        data.files.forEach(file => {
            if (file.order_file && file.order_file > maxOrder) {
                maxOrder = file.order_file;
            }
        });

        document.getElementById('newOrder').value = maxOrder + 1;
        document.getElementById('newPackageId').value = packageId;
        document.getElementById('newFormat').innerHTML = setOptionsBases();
        document.getElementById('newEnvironmentId').value = data.files[0].environment;
        document.getElementById('newDeliveredBy').value = data.files[0].delivered_by;
        document.getElementById('newQuantity').value = data.files[0].quantity;
        document.getElementById('newProfessional').value = professionalId;
        document.getElementById('newBatchNumber').value = data.files[0].batch_number;
    }

    const tableWithoutControls = () => {
        cardHeader.hidden = true;
        table += `
                <table class="table table-dark table-hover" id="tablePackages" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No. ficha</th>
                            <th>Formato</th>
                            <th>Cantidad usuarios</th>
                            <th>Entregado por</th>
                        </tr>
                    </thead>
                <tbody>`;

        data.files.forEach(file => {
            let iconClass = '<i class="fa-solid fa-circle-xmark fa-beat fa-xs" style="color: #ff6666;"></i>';
            let observation_icon = '';
            const baseName = file.bases ? file.bases.name : file.format.name;
            const userName = file.user ? `${file.user.name} ${file.user.last_name}` : `${file.deliveredBy.name} ${file.deliveredBy.last_name}`;

            if (file.productivity) {
                iconClass = `<i class="fa-solid fa-circle-check fa-beat fa-xs" style="color: #66ff66;"></i>`;

                if (file.productivity?.observations != null){
                    observation_icon = `<i class="fa-solid fa-xs fa-circle-exclamation fa-beat" style="color: #66b3ff;" title= "${file.productivity?.observations}"></i>`;
                }
            }

            const traceabilityUrl = `${routeBaseTraceability}?file_number_search=${encodeURIComponent(file.file_number)}`;
            table += `
                <tr>
                    <td><a href= "${traceabilityUrl}" class = "text-white">${file.file_number}</a> 
                        <span class = "ms-1 border-0 bg-transparent">${iconClass}</span>
                        <span class = "ms-1 border-0 bg-transparent">${observation_icon}</span>
                    </td>
                    <td>(${file.quantity}) ${baseName}</td>
                    <td>${file.quantity_users ?? '--'}</td>
                    <td>${userName}</td>
                </tr>
            `;
        })

        table += `
                </tbody>
            </table>
        `;
    }

    const tableWithControls = () => {
        cardHeader.hidden = false;
        table += `
            <table class="table table-dark table-hover" id="tablePackages" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th></th>
                        <th>No. ficha</th>
                        <th>Formato</th>
                        <th>Cantidad usuarios</th>
                        <th>Fecha intervención</th>
                        <th>De</th>
                        <th>Editar</th>
                    </tr>
                </thead>
            <tbody>`;

        data.files.forEach(file => {
            let iconClass = file.productivity ?
                `<i class="fa-solid fa-circle-check fa-beat fa-xs" style="color: #66ff66;"></i>` :
                `<i class="fa-solid fa-circle-xmark fa-beat fa-xs" style="color: #ff6666;"></i>`;
            const traceabilityUrl = `${routeBaseTraceability}?file_number_search=${encodeURIComponent(file.file_number)}`;
            const productivity_object = Object.entries(data.productivity);
            const productivity_file = productivity_object.map(([key, productivity]) => productivity).find(productivity => productivity.file_id == file.id);
            const baseName = file.bases ? file.bases.name : file.format.name;
            const userName = file.user ? `${file.user.name} ${file.user.last_name}` : `${file.deliveredBy.name} ${file.deliveredBy.last_name}`;
            
            let observation_icon = '';
            if (productivity_file && productivity_file.observations) {
                observation_icon = `<i class="fa-solid fa-xs fa-circle-exclamation fa-beat" style="color: #66b3ff;" title= "${productivity_file.observations}"></i>`;
            } else {
                observation_icon = '';
            }

            table += `
                <tr>
                    <td>${file.order_file ?? ''}</td>
                    <td><a href= "${traceabilityUrl}" class = "text-white">${file.file_number}</a> 
                        <span class = "ms-1 border-0 bg-transparent">${iconClass}</span>
                        <span class = "ms-1 border-0 bg-transparent">${observation_icon}</span>
                    </td>
                    <td>(${file.quantity}) ${baseName}</td>
                    <td>${file.quantity_users ?? '--'}</td>
                    <td>${file.fecha_intervencion ? file.fecha_intervencion : ''}</td>
                    <td>${userName}</td>
                    <td>
                        <a class="btn p-0" onclick="collapseEditPackage('${file.file_number}${file.id}')">
                            <i class="fa-solid fa-square-pen fa-2xl" style = "color: #0d6efd"></i>
                        </a>    
                    </td>
                </tr>
                <tr class="p-0">
                    <td colspan="7" class="p-0">
                        <div class="hideCollapse" id="${file.file_number}${file.id}">
                            <form class="container-fluid px-0 input-group d-flex justify-content-between" method="POST" id="form-${file.file_number}${file.id}">
                                <input type="hidden" class="form-control" id="package_id" value="${file.package_id}" style="width: 130px">
                                <input type="number" class="form-control" id="order_file" value="${file.order_file}" style="width: 20px">
                                <input type="number" class="form-control" id="file_number" value="${file.file_number}" style="width: 100px">
                                <select class="form-select" name="format_id" id = "format_id" name="format_id" style="width: 300px">
                                    ${setOptionsBases(file.format_id)}
                                </select>
                                <input type="number" class="form-control" id="quantity_users" value="${file.quantity_users ?? 0}" style="width: 40px">
                                <input type="date" class="form-control" id="fecha_intervencion" value="${file.fecha_intervencion ? file.fecha_intervencion : ''}" style="width: 12%">
                                
                                <button type="button" class="btn btn-sm btn-info" onclick = "editFilePackage('form-${file.file_number}${file.id}', '${file.id}', this)" style="width: 5%">
                                    <i class="fa-solid fa-floppy-disk"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            `;
        })

        table += `
                </tbody>
            </table>
        `;
    }

    const tableSisco = () => {
        cardHeader.hidden = false;
        table += `
            <table class="table table-dark table-hover" id="tablePackages" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>No. ficha</th>
                        <th>Formato</th>
                        <th>Entregado por</th>
                        <th>Editar</th>
                    </tr>
                </thead>
            <tbody>`;

        data.files.forEach(file => {
            const baseName = file.bases ? file.bases.name : file.format.name;
            let iconClass = file.productivity ?
                `<i class="fa-solid fa-circle-check fa-beat fa-xs" style="color: #66ff66;"></i>` :
                `<i class="fa-solid fa-circle-xmark fa-beat fa-xs" style="color: #ff6666;"></i>`;
            const traceabilityUrl = `${routeBaseTraceability}?file_number_search=${encodeURIComponent(file.consecutivo_sisco)}`;
            const productivity_object = Object.entries(data.productivity);
            const productivity_file = productivity_object.map(([key, productivity]) => productivity).find(productivity => productivity.file_id == file.id);
            const userName = file.delivered_by ? `${file.delivered_by.name} ${file.delivered_by.last_name}` : 'N/A';
            
            let observation_icon = '';
            if (productivity_file && productivity_file.observations) {
                observation_icon = `<i class="fa-solid fa-xs fa-circle-exclamation fa-beat" style="color: #66b3ff;" title= "${productivity_file.observations}"></i>`;
            } else {
                observation_icon = '';
            }

            table += `
                <tr>
                    <td><a href= "${traceabilityUrl}" class = "text-white">${file.consecutivo_sisco}</a> 
                        <span class = "ms-1 border-0 bg-transparent">${iconClass}</span>
                        <span class = "ms-1 border-0 bg-transparent">${observation_icon}</span>
                    </td>
                    <td>${baseName}</td>
                    <td>${userName}</td>
                    <td>
                        <a class="btn p-0" onclick="collapseEditPackage('${file.consecutivo_sisco}${file.id}')">
                            <i class="fa-solid fa-square-pen fa-2xl" style = "color: #0d6efd"></i>
                        </a>
                    </td>
                </tr>
                <tr class="p-0">
                    <td colspan="5" class="p-0">
                        <div class="collapsePackageEdit hideCollapse" id="${file.consecutivo_sisco}${file.id}">
                            <form class="container-fluid px-0" method="POST" id = "form-${file.consecutivo_sisco}${file.id}">
                                <div class="input-group me-2">
                                    <input type="number" class="form-control" id="file_number" value="${file.consecutivo_sisco}" style="width = 20%">
                                    <select class="form-select w-25" name="format_id" id = "format_id"  name="format_id" style="width = 10%">
                                        ${setOptionsBases(file.format_id)}
                                    </select>
                                    <input type="text" class="form-control" value="${userName}" disabled style="width = 20%">
                                    <button type="button" class="btn btn-sm btn-info"  onclick = "editFilePackage('form-${file.consecutivo_sisco}${file.id}', '${file.id}', this)">Guardar</button>
                                </div>
                            </form>
                        </div>
                    </td>
                </tr>
            `;
        })

        table += `
                </tbody>
            </table>
        `;
    }
    
    if (packageType === 'basic') {
        if (nonEditPackage) {
            tableWithoutControls();    
            
        }else{
            tableWithControls();
        }
    }else{
        tableSisco();
    }

    setPackageInfo();
    initMessage();

    return table;
}

function collapseEditPackage(collapseId) {
    if (existElement(collapseId)) {
        let collapseElement = document.getElementById(collapseId);
        collapseElement.classList.toggle('showCollapse');
        collapseElement.classList.toggle('hideCollapse');
    }
}

function editFilePackage(form_id, reception_id, button) {
    button.innerHTML = `<i class="fa-solid fa-spinner fa-spin-pulse"></i>`;
    button.disabled = true;

    const form = document.getElementById(form_id);
    const package_id = form.package_id.value;
    if (!form) {
        console.error('Formulario no encontrado');
        button.disabled = false;
        button.innerHTML = "<i class='fa-solid fa-floppy-disk'></i>";
        return;
    }

    const url = `/receptions/update/${reception_id}`;

    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': TOKEN,
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            'format_id': form.format_id.value,
            'file_number': form.file_number.value,
            'quantity_users': form.quantity_users.value,
            'order_file': form.order_file.value,
            'fecha_intervencion': form.fecha_intervencion.value,
            'package_id': form.package_id.value
        })
    })
        .then(response => response.json())
        .then(data => {            
            queryFetch(package_id, CARD_BODY_PACKAGES);
            setTimeout(() => {
                button.disabled = false;
                button.innerHTML = "<i class='fa-solid fa-floppy-disk'></i>";
            }, 600);
        })
        .catch(error => console.error('Error', error))
}

const containerPackages = Array.from(document.querySelectorAll('.card_package'));

function showFilesPackages() {
    if (containerPackages.length > 0) {
        const btnsPackages = document.querySelectorAll('button[data-bs-target="#collapseFilesPackage"]');
        btnsPackages.forEach(btn => {
            btn.addEventListener('click', () => {
                setBorderButtonPackages(containerPackages, btn.value);
                localStorage.setItem('package_active_id', btn.value);
                setLoader(CARD_BODY_PACKAGES);
                queryFetch(btn.value, CARD_BODY_PACKAGES);
            });
        })
    }
}

function setBorderButtonPackages(buttons, activeButton) {
    buttons.forEach(button => {
        button.style.transition = 'border 0.1s ease';
        button.style.border = 'none';
        if (activeButton == button.id) {
            button.style.border = '5px solid rgba(255, 105, 105, 0.3)';
            button.classList.remove('border-0');
        }
    })
}

const form_search_package = existElement('form_filter_packages') ? document.getElementById('form_filter_packages') : "";
function preventCloseCollapse() {
    if (document.getElementById('collapseFilesPackage')) {
        const collapsePackages = document.getElementById('collapseFilesPackage');
        collapsePackages.addEventListener('hide.bs.collapse', function (e) {
            e.preventDefault();
        })
    }
}

function keepStateCollapsePackage() {
    if (document.getElementById('collapseFilesPackage')) {
        const collapseFilesPackage = document.getElementById('collapseFilesPackage');

        form_search_package.addEventListener('submit', () => {
            localStorage.removeItem('package_active_id');
        })

        window.addEventListener('load', () => {
            if (localStorage.getItem('package_active_id')) {
                setLoader(CARD_BODY_PACKAGES);
                queryFetch(localStorage.getItem('package_active_id'), CARD_BODY_PACKAGES);
                setBorderButtonPackages(containerPackages, localStorage.getItem('package_active_id'));
                collapseFilesPackage.classList.add('show');
                localStorage.removeItem('package_active_id');
            }
        })
    }
}

function keepStatusFilterPackage() {
    if (existElement('form_filter_packages')) {
        const filters = form_search_package.querySelectorAll('input[type="checkbox"]');
        filters.forEach(filter => {
            filter.addEventListener('change', function () {
                const checboxes_filters = Array.from(filters);
                const active_filter_id = checboxes_filters.filter(item => item.checked).map(item => item.id);
                localStorage.setItem('filter_by_status', JSON.stringify(active_filter_id));
                form_search_package.submit();
            });
        });
    }

    if (existElement('collapseFiltersPackage')) {
        const collapseFilters = document.getElementById('collapseFiltersPackage');

        if (localStorage.getItem('statusCollapseFilters')) {
            collapseFilters.classList.add('show');
        }

        collapseFilters.addEventListener('shown.bs.collapse', function () {
            localStorage.setItem('statusCollapseFilters', 'show');
        });

        collapseFilters.addEventListener('hidden.bs.collapse', function () {
            localStorage.removeItem('statusCollapseFilters');
        });
    }
}

function setPreviousStatusFilterPackages() {
    const active_filters = localStorage.getItem('filter_by_status');
    if (existElement('form_filter_packages')) {
        if (active_filters) {
            const labels_filters = form_search_package.querySelectorAll('label');
            labels_filters.forEach(label => {
                label.classList.remove('filter_active_packages');
            })
            const active_filters_ids = JSON.parse(active_filters);
            if (Array.isArray(active_filters_ids)) {
                active_filters_ids.forEach(id => {
                    const checbox = document.getElementById(id);
                    checbox.checked = true;
                    form_search_package.querySelector(`[for="${id}"]`).classList.add('filter_active_packages');
                });
            }
        }
    }
}

function removeStatusFiltersPackages() {
    if (localStorage.getItem('filter_by_status')) {
        if (existElement('accordionSidebar')) {
            const menuNav = document.getElementById('accordionSidebar');
            const linksNav = menuNav.querySelectorAll('a');
            linksNav.forEach(link => {
                link.addEventListener('click', () => {
                    localStorage.removeItem('filter_by_status')
                })
            })
        }
    }
}

function fadeDropdown(item_id) {
    const box_options = document.getElementById(`${item_id}`);

    box_options.style.transition = 'opacity 0.3s, max-height 0.3s';
    box_options.style.overflow = 'hidden';
    box_options.style.opacity = 0;
    box_options.style.maxHeight = '0px';

    if (box_options.hidden) {
        box_options.hidden = false;
        requestAnimationFrame(() => {
            box_options.style.opacity = 1;
            box_options.style.maxHeight = box_options.scrollHeight + 'px';
        });
    } else {
        box_options.style.opacity = 0;
        box_options.style.maxHeight = '0px';
        setTimeout(() => {
            box_options.hidden = true;
        }, 300);
    }
}

if (existElement('form_filter_packages')) {
    showFilesPackages();
    preventCloseCollapse();
    keepStateCollapsePackage();
    keepStatusFilterPackage();
    setPreviousStatusFilterPackages();
    removeStatusFiltersPackages();
}

/**
 * Imprimir actas
 */

function printPDF(pdfUrl) {
    var iframe = document.createElement('iframe');
    iframe.style.display = 'none';
    iframe.src = pdfUrl;
    document.body.appendChild(iframe);
    iframe.onload = function () {
        iframe.contentWindow.print();
    };
}

/**
 * Evitar que se cierre el contenerdor de la clase dropdown-menu
 */

$('.dropdown-menu').on('click', function (e) {
    e.stopPropagation();
});  