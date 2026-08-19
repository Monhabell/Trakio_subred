/* Modules */
import Reception from '../modules/Reception.js';
import Notification from '../modules/Notification.js';
import OtherTimes from '../modules/OtherTimes.js';
import ConsecutivoPermission from '../modules/ConsecutivoPermission.js';
import Dashboard from '../modules/Dashboard.js';
import User from '../modules/User.js';
import Correction from '../modules/Correction.js';

/**
/* GENERAL FUNCTIONS
**/
function existElement(idElement) {
    return document.getElementById(idElement) !== null;
}

function fadeOutAlerts() {
    const alerts = document.querySelectorAll('.alert-success, .alert-danger, .alert-warning');
    alerts.forEach(alert => {
        setTimeout(() => {
            if (alert) {
                alert.classList.add('fade-out');
                alert.addEventListener('transitionend', () => { alert.remove(); });
            }
        }, 3000);
    })
}

window.fadeDropdown = fadeDropdown;
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

async function buttonsLoader() {
    const utilityModule = await import('../modules/Utility.js');
    const module = new utilityModule.default();
    module.buttonsLoader();
}

function isEqual(obj1, obj2) {
    return JSON.stringify(obj1) === JSON.stringify(obj2);
}

function menuReceptions(receptionInstance) {
    const menuReceptionButtons = document.querySelectorAll('.menu-reception');

    menuReceptionButtons.forEach(button => {
        button.addEventListener('click', () => {
            const targetContainerId = button.getAttribute('data-to-container');
            const targetContainer = document.getElementById(targetContainerId);

            // Ocultar todas las tablas
            document.querySelectorAll('.table-receptions').forEach(container => {
                container.style.display = 'none';
            });

            // Mostrar la tabla seleccionada
            targetContainer.style.display = 'block';

            // Actualizar el estado activo del botón
            menuReceptionButtons.forEach(btn => btn.classList.remove('active'));
            button.classList.add('active');
            receptionInstance.type = targetContainerId === 'table-receptions-normal' ? 'normal' : 'sisco';
        });
    });
}

async function receptionsList() {
    const receptionInstance = new Reception();
    receptionInstance.environment = environmentId;
    receptionInstance.model = 'admin';

    menuReceptions(receptionInstance);

    try {
        receptionInstance.index();
        receptionInstance.update();
    } catch (error) {
        console.error('Error:', error);
    }
}

/**
/* INITIALIZATION APP
**/

async function initApp() {
    //Dashboard
    if (existElement('productivity-container-dash')) {
        getDashboardData();
        setGoalProductivity();
    }

    //Recepciones
    if (existElement('tableReceptions')) {
        receptionsList();
    }

    //Otros tiempos
    if (existElement('form-filter-ot')) {
        document.getElementById('ot_status').addEventListener('change', function () {
            document.getElementById('form-filter-ot').submit();
        });
    }

    if (existElement('alertsDropdown') || existElement('ot_table')) {
        listenersBtnOtherTime();
    }

    //Asignaciones
    if (existElement('asign_digitizer_name')) {
        const DOMElements = {
            inputDigitizerName: document.getElementById('asign_digitizer_name'),
            ulElement: document.getElementById('list_digitizer'),
            selectId: document.getElementById('asign_digitizer_id'),
            liClassElements: 'list_digitizers',
        }
        getListDigitizers(DOMElements);
    }

    if (existElement('form_filter_packages')) handlePackages();

    //Filtrar por digitador
    if (existElement('search_digitizer')) {
        const DOMElements = {
            inputDigitizerName: document.getElementById('search_digitizer'),
            ulElement: document.getElementById('list_digitizer_search'),
            selectId: document.getElementById('search_digitizer_id'),
            liClassElements: 'list_digitizers_search',
        }
        getListDigitizers(DOMElements);
    }

    //Correcciones
    if (existElement('filter-corrections-box')) {
        const filter = sessionStorage.getItem('filter-corrections') ? sessionStorage.getItem('filter-corrections') : '';

        //Menú correcciones
        const setActiveNav = (buttons) => {
            buttons.forEach(button => {
                button.classList.remove('active');
            });

            const filter = sessionStorage.getItem('filter-corrections') ? sessionStorage.getItem('filter-corrections') : '';
            document.querySelector(`[data-filter-status="${filter}"]`).classList.add('active');
        }

        //Filtrar correcciones
        const listenerNavCorrections = () => {
            const correction_buttons_nav = document.querySelectorAll('.nav-link-corrections');
            setActiveNav(correction_buttons_nav);

            correction_buttons_nav.forEach(button => {
                button.addEventListener('click', (event) => {
                    const filter = event.currentTarget.dataset.filterStatus;
                    sessionStorage.setItem('filter-corrections', filter);

                    //Cargar correcciones
                    loadCorrections(filter).then(() => {
                        showOptionsCorrections();
                        deleteCorrections();
                        listenerSwitch();
                    });

                    //Actualizar botones de navegación
                    setActiveNav(correction_buttons_nav);
                })
            });
        }

        listenerNavCorrections();

        //Buscar corrección
        const listenerSearchCorrection = () => {
            const form = document.getElementById('form-search-corrections');
            form.addEventListener('submit', (e) => {
                e.preventDefault();
                const input_file_number = form.search_file.value;

                const filter = sessionStorage.getItem('filter-corrections') ? sessionStorage.getItem('filter-corrections') : '';
                loadCorrections(filter, input_file_number)
                    .then(() => {
                        showOptionsCorrections();
                        deleteCorrections();
                        listenerSwitch();
                    });
            });
        }

        listenerSearchCorrection();

        //Cargar correcciones inicialmente
        loadCorrections(filter).then(() => {
            showOptionsCorrections();
            deleteCorrections();
            listenerSwitch();
        });
    }

    //Usuarios
    if (existElement('users-container')) {
        //Mostrar info usuarios
        const animation_info_users = () => {
            const links_users = document.querySelectorAll('.user-details');
            links_users.forEach(link => {
                link.addEventListener('click', (event) => {
                    const userId = event.currentTarget.dataset.userId;
                    const card_inner = document.getElementById(`card-inner-${userId}`);
                    if (card_inner.classList.contains('user-card-rotate')) {
                        card_inner.classList.remove('user-card-rotate');
                    } else {
                        card_inner.classList.add('user-card-rotate');
                    }
                });
            })
        }

        animation_info_users();

        //Actualizar entorno asignado
        const updateEnvironments = () => {
            const selects = document.querySelectorAll('.env-select-user');
            selects.forEach(select => {
                select.addEventListener('change', (event) => {
                    const userId = event.currentTarget.dataset.userId;
                    const form = document.getElementById(`form-update-user-${userId}`);
                    if (form) {
                        form.submit();
                    } else {
                        console.error("El elemento no existe");
                    }
                });
            });
        }

        updateEnvironments();

        //Guardar username asignado
        const updateUsernames = () => {
            const buttons = document.querySelectorAll('.save-username-user');
            buttons.forEach(button => {
                button.addEventListener('click', (event) => {
                    const userId = event.currentTarget.dataset.userId;
                    const form = document.getElementById(`form-update-user-${userId}`);
                    if (form) {
                        form.submit();
                    } else {
                        console.error("El elemento no existe");
                    }
                });
            });
        }

        updateUsernames();
    }

    //Actas
    if (existElement('form-acts')) configureActModule();

    //Certificaciones
    if (existElement('form-certifications')) {
        const certificationsForm = document.getElementById('form-certifications');
        const boxBtnSubmit = document.getElementById('box-btn-submit');
        const certificationsFiles = certificationsForm.file;

        certificationsFiles.addEventListener('change', () => {
            if (certificationsFiles.value) {
                boxBtnSubmit.innerHTML = "";

                const newBtn = document.createElement('button');
                newBtn.classList.add('btn', 'btn-primary', 'btn-block');
                newBtn.type = 'submit';
                newBtn.innerHTML = "Subir certificaciones";

                boxBtnSubmit.appendChild(newBtn);
            }
        });
    }
}

/**
/* ACTAS
**/
function listenerRequestSign() {
    const containerActs = document.getElementById('container-acts');
    containerActs.addEventListener('click', (event) => {
        const btnOpenModal = event.target.closest('button');
        const isRequestButton = btnOpenModal.classList.contains('btn-request-sign');
        if (isRequestButton) {
            const inputModalUrl = document.getElementById('url-document-tosign');
            inputModalUrl.value = btnOpenModal.value;
        }
    });

    const initMenuSigns = async () => {
        const utilityModule = await import('../modules/Utility.js');
        const utilInstance = new utilityModule.default();
        utilInstance.simpleMenuNav("nav-acts-signs");

        const userModule = await import('../modules/User.js');
        const userInstance = new userModule.default();

        const buildLabelsUSers = (users) => {
            let html = '';

            users.forEach(user => {
                html += `<label for = "user-${user.id}" class="user-item d-flex align-items-center justify-content-between">
                            <input type="checkbox" class="form-check-input hidden" id="user-${user.id}" name = "usersToRequestSign[]" value="${user.id}" >
                            <span class="user-name-sign">${utilInstance.nameAndLastName(user.name, user.last_name)}</span>
                            <span class="user-role">${user.role.name}</span>
                        </label>`;
            });

            return html;
        }

        document.getElementById('box-nav-acts-signs').addEventListener("click", async (e) => {
            const usersListContainer = document.getElementById('user-list-sign');
            utilInstance.containerLoader(usersListContainer);

            try {
                if (e.target.classList.contains("nav-acts-signs")) {
                    const environmentId = e.target.dataset.environmentId;
                    const users = await userInstance.filter(environmentId);

                    if (usersListContainer && users.length > 0) {
                        usersListContainer.classList.remove('justify-content-center', 'align-items-center', 'message-container');
                        usersListContainer.innerHTML = buildLabelsUSers(users);
                    } else {
                        usersListContainer.classList.add('message-container');
                        usersListContainer.innerHTML = `<p class="no-users-message">No hay usuarios registrados en este entorno.</p>`;
                    }
                }
            }
            catch (error) {
                console.error('Error:', error);
            }
        });
    }

    initMenuSigns();
}

async function configureActModule() {
    const { Generate } = await import('../modules/Generate.js');
    const receptionActInstance = new Generate();

    document.querySelectorAll('[data-act-attribute]').forEach(option => {
        const trigger = option.type === "radio" ? "change" : "click";
        option.addEventListener(trigger, (event) => handleOptionSelection(event, receptionActInstance));
    });

    //Menu
    const initMenu = async () => {
        const utilityModule = await import('../modules/Utility.js');
        const utilInstance = new utilityModule.default();

        const itemsMenu = {
            containersIds: ['containerShowActs', 'containerGenerateAct', 'containerGenerateWeekAct'],
            buttonsClass: "nav-link-acts"
        }

        utilInstance.interactiveMenu(itemsMenu);
    }

    const listenerDeleteFilters = () => {
        const buttonDelete = document.getElementById('btn-delete-filter-acts');
        buttonDelete.addEventListener('click', () => {
            const form = buttonDelete.closest('form');
            Array.from(form.querySelectorAll('select')).map(input => input.value = 'all');;
            buttonDelete.classList.add('btn-loader');
        });
    }

    const createButtonDeleteFilter = () => {
        if (!existElement('btn-delete-filter-acts')) {
            const form = document.getElementById('form-filter-acts');
            const newButton = document.createElement('button');
            newButton.id = 'btn-delete-filter-acts';
            newButton.classList.add('btn', 'btn-sm', 'p-0');
            newButton.innerHTML = '<i class="fa-solid fa-x"></i>';
            form.insertAdjacentElement('beforebegin', newButton);
            listenerDeleteFilters();
        }
    }

    const configFilters = () => {
        const form = document.getElementById('form-filter-acts');
        form.addEventListener('change', (event) => {
            const selectValue = event.target.value;
            if (selectValue !== 'all') {
                createButtonDeleteFilter();
            }
        });
    }

    initMenu();
    listenerRequestSign();
    // deleteFilterActs();
    configFilters();
}

async function handleOptionSelection(event, receptionActInstance) {
    const selected = event.currentTarget.dataset.actAttribute;
    if (selected === "period") {
        receptionActInstance.period = getPeriodData();
    } else if (selected === "signature") {
        receptionActInstance.signatures = getSignatureData();
    } else if (selected === "environment") {
        receptionActInstance.environment = getEnvironmentData();
    } else if (selected === "technician") {
        receptionActInstance.technician = getTechnicianData();
    }

    receptionActInstance.selectedOption = selected;
    receptionActInstance.evaluateStep();
}

function getTechnicianData() {
    const options = document.getElementsByName('technician');
    let technicianName = '';

    options.forEach(option => {
        if (option.checked) {
            technicianName = option.id;
        }
    });

    return technicianName;
}

function getEnvironmentData() {
    const options = document.getElementsByName('environment');
    let environmentData = {};

    options.forEach(option => {
        if (option.checked) {
            environmentData.id = option.value;
            environmentData.name = option.id;
        }
    });

    return environmentData;
}

function getPeriodData() {
    return {
        initDate: document.getElementById('init-date-act').value,
        endDate: document.getElementById('end-date-act').value,
        dateSignature: document.getElementById('act-date').value
    };
}

function getSignatureData() {
    const signatures = Array.from(document.getElementsByName('signatures[]'))
        .filter(signature => signature.checked)
        .map(signature => signature.dataset.valueSignatures);

    return signatures;
}

/**
/* DASHBOARD
**/
let listProductivity = null;

async function getDashboardData() {
    const container = document.getElementById('productivity-container-dash');
    const dashboard = new Dashboard();

    const activeEnvironmentFilter = (activeButton = null) => {
        const buttons = document.querySelectorAll('[data-environment-id]');
        if (activeButton) {
            buttons.forEach(button => {
                button.classList.remove('dashboardActiveFilter');
                button.classList.add('dashboardNotActiveFilter');

                if (activeButton === button.dataset.environmentId) {
                    button.classList.add('dashboardActiveFilter');
                    button.classList.remove('dashboardNotActiveFilter');
                }
            });
        } else {
            buttons.forEach(button => {
                button.classList.remove('dashboardNotActiveFilter');
                button.classList.remove('dashboardActiveFilter');
            });
        }
    }

    const setLoader = () => {
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

    const dashboardData = async (filterType = 'month', loader = true) => {
        let initDate, endDate;
        switch (filterType) {
            case 'day':
                const date = new Date();
                initDate = endDate = `${date.getFullYear()}-${(date.getMonth() + 1).toString().padStart(2, '0')}-${date.getDate().toString().padStart(2, '0')}`;
                localStorage.setItem('rangeDashboard', [initDate, endDate]);
                break;

            case 'week':
                const currentDate = new Date();

                const firstDayOfWeekDate = new Date(currentDate);
                firstDayOfWeekDate.setDate(currentDate.getDate() - currentDate.getDay() + 1);

                const lastDayOfWeekDate = new Date(firstDayOfWeekDate);
                lastDayOfWeekDate.setDate(firstDayOfWeekDate.getDate() + 6);

                initDate = moment(firstDayOfWeekDate).format('YYYY-MM-DD');
                endDate = moment(lastDayOfWeekDate).format('YYYY-MM-DD');

                localStorage.setItem('rangeDashboard', JSON.stringify([initDate, endDate]));
                break;

            case 'month':
                [initDate, endDate] = dashboard.rangeDatesProductivity();
                localStorage.setItem('rangeDashboard', [initDate, endDate]);
                break;

            case 'custom':
                const initDateInput = document.getElementById('init_date').value
                const endDateInput = document.getElementById('end_date').value
                if (initDateInput && endDateInput) {
                    initDate = initDateInput;
                    endDate = endDateInput;
                    localStorage.setItem('rangeDashboard', [initDate, endDate]);
                }

                break;

            default:
                if (localStorage.getItem('rangeDashboard')) {
                    const dates = localStorage.getItem('rangeDashboard');
                    const separatedDates = dates.split(",");

                    initDate = separatedDates[0];
                    endDate = separatedDates[1];
                }
                break;
        }

        const range = {
            init_date: initDate,
            end_date: endDate
        };

        dashboard.range = range;
        dashboard.environment =
            sessionStorage.getItem('environmentDashboard')
                ? sessionStorage.getItem('environmentDashboard')
                : null;

        if (loader) setLoader();

        const response = await dashboard.productivity();
        innerProductivity({ response, 'filterType': filterType });
    }

    const activeRangeFilter = (button) => {
        const buttons = document.querySelectorAll('.dashboardFilter');
        buttons.forEach(button => {
            button.classList.remove('active');
            button.disabled = false;
        });

        if (button.value !== 'custom') {
            button.classList.add('active');
            button.disabled = true;
        }
    }

    const specificFilter = () => {
        const filtersButton = document.querySelectorAll('.dashboardFilter');
        filtersButton.forEach(button => {
            button.addEventListener('click', (e) => {
                dashboardData(button.value);
                activeRangeFilter(button);
            });
        });
    }

    const productivityByEnvironment = () => {
        const buttons = document.querySelectorAll('[data-environment-id]');
        buttons.forEach(button => {
            button.addEventListener('click', async (event) => {
                const environmentId = event.currentTarget.dataset.environmentId;
                const environmentIdSession = sessionStorage.getItem('environmentDashboard')
                if (environmentId != environmentIdSession) {
                    dashboard.environment = environmentId;
                    sessionStorage.setItem('environmentDashboard', environmentId)
                    dashboardData();
                    activeEnvironmentFilter(environmentId);
                } else {
                    sessionStorage.removeItem('environmentDashboard', environmentId)
                    dashboardData()
                    activeEnvironmentFilter();
                }
            });
        });
    }

    const innerProductivity = (data) => {
        container.classList.remove('justify-content-center', 'align-items-center');
        const productivity = data.response.productivity;
        const other_times = data.response.other_times;

        try {
            if (!isEqual(listProductivity, data)) {
                if (container && data.response.success) {
                    container.innerHTML = dashboard.productivityHTML(productivity, other_times);
                } else {
                    container.innerHTML = `<p class = "text-danger">No se pudo obtener la información de productividad.</p>`;
                }

                listProductivity = data;
            }
        } catch (e) {
            console.error('Error al actualizar la productividad:', e);
            container.innerHTML = `<p class = "text-danger">Ocurrió un error al actualizar la productividad.</p>`;
        }
    }

    const getDataChar = async () => {
        const ctx = document.getElementById('receptionVsDoneChart').getContext('2d');
        const dataReceptions = await dashboard.receivedCount();
        const data_info = [];

        dataReceptions.environments.forEach(env => {
            data_info.push({
                label: env.entorno,
                data: dataReceptions.receptions.find(environment => environment.environment == env.id),
                backgroundColor: "red"
            });
        });

        const data = {
            labels: data_info.map(info => info.label),
            datasets: [{
                label: "Entregadas",
                backgroundColor: "blue",
                data: data_info.map(info => {
                    return info.data ? info.data.total_count : 0;
                }),
                borderWidth: 2,
                backgroundColor: "rgba(121, 2, 2, 0.3)",
                borderColor: "rgb(247, 0, 0)",
                borderRadius: 8,
            },
            {
                label: "Digitadas",
                backgroundColor: "green",
                data: data_info.map(info => {
                    return info.data ? info.data.digitadas : 0;
                }),
                borderWidth: 2,
                borderColor: "rgb(0, 75, 187)",
                backgroundColor: "rgba(0, 11, 163, 0.1)",
                borderRadius: 8,
            }]
        };

        const config = {
            type: 'bar',
            data: data,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'top' }
                },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        }

        new Chart(ctx, config);
    }

    productivityByEnvironment();
    specificFilter();

    dashboardData();
    setInterval(() => {
        dashboardData('default', false);
    }, 10000);

    activeEnvironmentFilter(dashboard.environment);
    getDataChar();
}

/**
/* CORRECCIONES
**/
function listenerSwitch() {
    const switch_buttons = document.querySelectorAll('.switch-button-corrections');
    switch_buttons.forEach(button => {
        button.addEventListener('change', async (event) => {
            event.preventDefault();
            const correctionId = event.currentTarget.dataset.correctionId;
            const isCorrected = button.checked ? '1' : '0';
            updateCorrectionStatus(correctionId, isCorrected);
        });
    });
}

async function updateCorrectionStatus(correction_id, new_status) {
    const correction = new Correction(correction_id);
    correction.status = new_status;
    try {
        const result = await correction.updateStatus();

        const filter = sessionStorage.getItem('filter-corrections') ? sessionStorage.getItem('filter-corrections') : '';

        loadCorrections(filter).then(() => {
            showOptionsCorrections();
            deleteCorrections();
            listenerSwitch();
        });
    } catch (error) {
        console.error('Error al actualizar el estado', error);
    }
}

function showOptionsCorrections() {
    const options_buttons = document.querySelectorAll('.show-corrections-options');
    options_buttons.forEach(button => {
        button.addEventListener('click', (event) => {
            const options_id = event.currentTarget.dataset.correctionId;
            fadeDropdown(`${options_id}-corrections`);
        })
    });
}

function deleteCorrections() {
    const correction_buttons_delete = document.querySelectorAll('.delete-correction');
    correction_buttons_delete.forEach(button => {
        button.addEventListener('click', async (event) => {
            const correctionId = event.currentTarget.dataset.correctionId;
            const resultDelete = await new Correction(correctionId).delete();

            if (resultDelete) {
                alert(resultDelete);
                const filter = sessionStorage.getItem('filter-corrections');
                loadCorrections(filter);
            }
        });
    });
}

async function loadCorrections(filter = '', file_number = null) {
    const corrections = new Correction();
    corrections.status = filter;
    corrections.file_number = file_number;

    const filter_container = document.getElementById('filter-corrections-box');
    filter_container.innerHTML = await corrections.generateHTML();
}

/**
/* ASIGNACIONES
**/
async function handlePackages() {
    const packageModule = await import('../modules/Package.js');
    const packageApp = new packageModule.default();
    packageApp.events();
}

function debounce(func, delay) {
    let timeout;
    return function (...args) {
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(this, args), delay);
    };
}

function getListDigitizers(elements = {}) {
    const user = new User();

    const updateList = async () => {
        const query = elements.inputDigitizerName.value.trim();

        if (query.length > 0) {
            const response = await user.getTypedUser(query);
            const users = response.data.data;

            elements.ulElement.innerHTML = listDigitizerHTML(users, elements.liClassElements);
            elements.ulElement.parentElement.hidden = false;

            if (users.length > 0) {
                const listElements = elements.ulElement.querySelectorAll(`.${elements.liClassElements}`);

                listElements.forEach(li => li.addEventListener('click', (event) => {
                    user.name = event.currentTarget.dataset.nameDigitizer;
                    user.id = event.currentTarget.dataset.idDigitizer;
                    elements.inputDigitizerName.value = user.name;
                    elements.selectId.value = user.id;
                    elements.ulElement.innerHTML = '';
                    elements.ulElement.parentElement.hidden = true;
                }));
            }
        } else {
            clearList();
        }
    };

    const clearList = () => {
        elements.ulElement.innerHTML = '';
        elements.ulElement.parentElement.hidden = true;
        elements.selectId.value = '';
    };

    elements.inputDigitizerName.addEventListener('keyup', debounce(updateList, 300));
}


function listDigitizerHTML(digitizers, liClass) {
    let list = '';
    if (digitizers.length > 0) {
        digitizers.forEach(digitizer => {
            const user = new User();
            list += `
                <a class = "${liClass} text-white icon-link icon-link-hover" style="--bs-link-hover-color-rgb: 25, 135, 84;" data-name-digitizer = "${digitizer.name} ${digitizer.last_name}" data-id-digitizer = "${digitizer.id}" href= "#">
                    ${user.properNouns(digitizer.name, digitizer.last_name)}
                </a>
            `;
        });
    } else {
        list += "<li>Ninguna coincidencia</li>";
    }

    return list;
}

/**
/* NOTIFICACIONES
**/
let currentNotifications = null;
async function getNotifications() {
    if (existElement('alertsDropdown') || existElement('ot_table')) {
        const notification = new Notification();
        const notificationData = await notification.get();

        if (!notification.isEqualJSON(notificationData, currentNotifications)) {
            const topBarNotifications = notificationData.filter(notification => notification.type != 'reception');
            notification.showNotifications(topBarNotifications);
            if (topBarNotifications.length > 0) {
                listenersBtnOtherTime();
                listenersBtnConsecutivoPermission();
            }

            if (existElement('mail-box-dash')) {
                const mailBoxNotifications = notificationData.filter(notification => notification.type == 'reception');
                notification.showMailBoxNotifications(mailBoxNotifications);
            }

            currentNotifications = notificationData;
        }
    }
}

function createResponseNotificationOtherTime(notification) {
    const notificationInstance = new Notification();
    const time_ot = notification.time;

    notificationInstance.type = 'other-times-response';
    notificationInstance.message = notification.otherTime.isApproved == 1 ?
        `Te han aprobado la solicitud de ${time_ot} horas, correspondientes a ${notification.description}` :
        `Te han rechazado la solicitud de ${time_ot} horas, correspondientes a ${notification.description}`;

    notificationInstance.create(notification.userTo, notification.otherTime.id);
    notificationInstance.get();
}

async function listenersBtnOtherTime() {
    const showConfirmation = (isApproved) => {
        const message = isApproved == 1
            ? "¿Confirmar la asignación de las horas?"
            : "¿Rechazar horas?";
        return confirm(message);
    };

    const buildNotificationData = (button) => {
        const { notificationId, description, time, userTo, othertimeId, approved } = button.dataset;
        return {
            id: notificationId || null,
            description,
            time,
            userTo,
            otherTime: {
                isApproved: approved,
                id: othertimeId
            }
        };
    };

    const buttons = document.querySelectorAll('.btn-approve-othertimes, .btn-approve-othertimes-table');
    buttons.forEach(button => {
        button.addEventListener('click', async (event) => {
            event.preventDefault();

            const isApproved = button.dataset.approved;
            if (!showConfirmation(isApproved)) return;

            const notificationData = buildNotificationData(button);
            createResponseNotificationOtherTime(notificationData);

            const otherTime = new OtherTimes();
            otherTime.id = button.dataset.othertimeId;
            otherTime.isApproved = isApproved;

            try {
                await otherTime.update();

                if (button.classList.contains('btn-approve-othertimes-table')) {
                    location.reload();
                } else {
                    await getNotifications();
                }

            } catch (error) {
                console.error("Error al actualizar u obtener notificaciones:", error);
            }
        });
    });
}

async function listenersBtnConsecutivoPermission() {
    const showConfirmation = (isApproved) => {
        const message = isApproved == 1
            ? "¿Aprobar el permiso para consecutivos del mes anterior?"
            : "¿Rechazar la solicitud?";
        return confirm(message);
    };

    const buttons = document.querySelectorAll('.btn-approve-consecutivo-permission');
    buttons.forEach(button => {
        button.addEventListener('click', async (event) => {
            event.preventDefault();

            const isApproved = button.dataset.approved;
            if (!showConfirmation(isApproved)) return;

            const permission = new ConsecutivoPermission();
            permission.id = button.dataset.permissionId;

            try {
                if (isApproved == 1) {
                    const minutosInput = document.querySelector(
                        `.consecutivo-permission-minutos[data-permission-id="${button.dataset.permissionId}"]`
                    );
                    permission.durationMinutes = minutosInput ? minutosInput.value : 10;
                    await permission.approve();
                } else {
                    await permission.reject();
                }

                await getNotifications();
            } catch (error) {
                console.error("Error al actualizar u obtener notificaciones:", error);
            }
        });
    });
}

setInterval(async () => {
    await getNotifications()
}, 10000);

fadeOutAlerts();
buttonsLoader();
initApp();
getNotifications();

function setGoalProductivity() {
    document.getElementById('formHoras').addEventListener('submit', async function (event) {
        event.preventDefault();
        await updateGoalPerDay();
    });
}


async function updateGoalPerDay() {
    const entornoId = document.getElementById('entornoId').value;
    const entornoValor = document.getElementById('entornoValor').value;
    const date = document.getElementById("date_time").value;
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    try {
        const response = await fetch('/editar-hora-dia', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({ id: entornoId, valor: entornoValor, fecha: date })
        });

        const result = await response.json();
        alert(result.message);
    } catch (error) {
        console.error("Error al guardar los datos:", error);
        alert("Ocurrió un error al guardar los datos.");
    }
}

if (existElement('tiemposTable')) setTimerProductivity();

function setTimerProductivity() {
    document.getElementById('sortDesviacion').addEventListener('click', function () {
        const tbody = document.querySelector('#tiemposTable tbody');
        const rows = Array.from(tbody.querySelectorAll('tr'));

        const asc = this.classList.toggle('asc');

        rows.sort((a, b) => {
            const valA = parseFloat(a.children[6].dataset.value);
            const valB = parseFloat(b.children[6].dataset.value);

            return asc ? valA - valB : valB - valA;
        });

        rows.forEach(row => tbody.appendChild(row));
    });
}
