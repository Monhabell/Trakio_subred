import Queries from './Queries.js';
import { route } from 'ziggy-js';

export default class Notification extends Queries {
    #id;
    #message;
    #type;

    constructor(id = null, message = null) {
        super();
        this.#id = id;
        this.#message = message;
        this.#type = 'info';
    }

    get id() {
        return this.#id;
    }

    set id(id) {
        this.#id = id;
    }

    get message() {
        return this.#message;
    }

    set message(message) {
        this.#message = message;
    }

    get type() {
        return this.#type;
    }

    set type(type) {
        this.#type = type;
    }

    async get() {
        try {
            const url = route('notifications.show');
            const response = await this.axiosGet(url);
            
            return response.data;
        } catch (error) {
            console.error('Error al obtener las notificaciones:', error);
        }
    }

    async create(toUser, ot_id = null) {
        try {
            const url = route('notification.store');
            const data = {
                type: this.#type,
                message: this.#message,
                to_user_id: toUser,
                other_time_id: ot_id
            }

            const response = await this.axiosPost(url, data);
            return response.data.message;
        } catch (error) {
            console.error('Error al crear la notificaciones:', error);
        }
        
    }

    async markAsRead() {
        const url = route('notification.markAsRead', {notification: this.#id});
        const response = await this.axiosPatch(url);
        return response;
    }

    showNotifications(notifications) {
        const container = document.querySelector('div[aria-labelledby="alertsDropdown"]');
        container.innerHTML = '';
        const max_notifications = 10;
        const badge = document.getElementById('counter-notifications')
        const bell = document.getElementById('bell')
        const notificationsCount = notifications.length;

        const badgeCounter = () => {
            if (notificationsCount === 0) {
                badge.hidden = true;

                let notificationData = document.createElement('a');
                notificationData.setAttribute('id', 'read-alerts')
                notificationData.classList.add('dropdown-item', 'text-center', 'small', 'text-gray-500', 'font-weight-bold');
                notificationData.textContent = `¡Todo al día!`;
                container.appendChild(notificationData);
                bell.classList.remove('fa-shake')

                return;
            }

            badge.hidden = false;
            badge.textContent = notificationsCount;
            bell.classList.add('fa-shake')
        }

        const getBgIcon = (alerType) => {
            let bgIcon = 'bg-primary';

            const alertsTypes = {
                'other-times': 'bg-gradient-red',
                'cumpleaños': 'bg-primary',
                'document-signature': 'bg-primary',
                'consecutivo-permission-request': 'bg-gradient-red'
            }

            for (let key in alertsTypes) {
                if (key === alerType.toLowerCase()) {
                    bgIcon = alertsTypes[key]
                    break;
                }
            }

            return bgIcon;
        }

        const builNotificationInformative = (notification, index) => {
            if (index < max_notifications) {
                let notificationDiv = document.createElement('div');
                notificationDiv.classList.add('dropdown-item', 'd-flex', 'align-items-center', 'body-notifications', 'border-0');
                notificationDiv.setAttribute('id', `alert-${notification.id}`);
                const hrefRoute = notification.type === 'document-signature-response' ? `href = "${route('adm.acts')}"` : '';
                
                notificationDiv.innerHTML = `<a ${hrefRoute}>
                                                <div class="mr-3">
                                                    <div class="viewer-notifications icon-circle ">
                                                        <span class = "border border-2 border-primary">
                                                            <img src="/img/LogoTrakioRounded.png" alt="Logo">
                                                        </span>
                                                    </div>
                                                </div>

                                                <div class = "d-flex flex-column">
                                                    <div class="small font-weight-bold">${this.formatDateHourFromISO(notification.created_at)}</div>
                                                    <span class="font-weight-bold text-white">${notification.message}</span>
                                                </div>
                                            </a>`;

                if (index > 0) {
                    container.insertBefore(notificationDiv, container.firstChild)
                } else {
                    container.appendChild(notificationDiv);
                }
            }
        }

        const buildNotificationConsecutivoPermission = (notification, index) => {
            if (index < max_notifications) {
                let notificationDiv = document.createElement('div');
                notificationDiv.classList.add('dropdown-item', 'd-flex', 'align-items-center', 'body-notifications', 'border-0');
                notificationDiv.setAttribute('id', `alert-${notification.id}`);
                notificationDiv.innerHTML = `<div class="mr-3">
                                                <div class="icon-circle ${getBgIcon(notification.type)}">
                                                    <i class="fa-solid fa-unlock fa-2xl text-white"></i>
                                                </div>
                                            </div>

                                            <div class="list-options-white" id="options-notifications-cp">
                                                <a onclick="fadeDropdown('${notification.consecutivo_permission_id}-notify-options')" class="p-1">
                                                    <i class="fa-solid fa-chevron-down" style="color: #ffffff;"></i>
                                                </a>
                                                <ul class="p-0" id="${notification.consecutivo_permission_id}-notify-options" hidden>
                                                    <li class="flex-center">
                                                        <input type="number" class="form-control form-control-sm consecutivo-permission-minutos"
                                                                data-permission-id="${notification.consecutivo_permission_id}"
                                                                value="10" min="1" max="120" style="width: 70px;" title="Minutos a otorgar">
                                                    </li>
                                                    <li class = "flex-center">
                                                        <button type ="button" class ="btn btn-sm fs-6 fw-light p-0 rounded-pill btn-approve-consecutivo-permission"
                                                                data-permission-id = "${notification.consecutivo_permission_id}"
                                                                data-approved = "1"
                                                                data-user-to = "${notification.from_user.id}">
                                                            <i class="fa-solid fa-square-check fa-lg" style="color: #02a500;"></i>
                                                        </button>
                                                    </li>

                                                    <li class = "flex-center">
                                                        <button type ="button" class ="btn btn-sm fs-6 fw-light p-0 rounded-pill btn-approve-consecutivo-permission"
                                                                data-permission-id = "${notification.consecutivo_permission_id}"
                                                                data-approved = "0"
                                                                data-user-to = "${notification.from_user.id}">
                                                            <i class="fa-solid fa-square-xmark fa-lg" style="color: #a50000;"></i>
                                                        </button>
                                                    </li>
                                                </ul>
                                            </div>

                                            <div class = "d-flex flex-column">
                                                <div class="small font-weight-bold">${this.formatDateHourFromISO(notification.created_at)}</div>
                                                <span class="font-weight-bold text-white">${notification.from_user.name} ${notification.message} </span>
                                            </div>`;

                if (index > 0) {
                    container.insertBefore(notificationDiv, container.firstChild)
                } else {
                    container.appendChild(notificationDiv);
                }
            }
        };

        const buildNotificationOt = (notification, index) => {
            if (index < max_notifications) {
                let notificationDiv = document.createElement('div');
                notificationDiv.classList.add('dropdown-item', 'd-flex', 'align-items-center', 'body-notifications', 'border-0');
                notificationDiv.setAttribute('id', `alert-${notification.id}`);
                notificationDiv.innerHTML = `<div class="mr-3">
                                                <div class="icon-circle ${getBgIcon(notification.type)}">
                                                    <i class="fa-solid fa-clock fa-2xl text-white"></i>
                                                </div>
                                            </div>

                                            <div class="list-options-white" id="options-notifications-ot">
                                                <a onclick="fadeDropdown('${notification.other_time_id}-notify-options')" class="p-1">
                                                    <i class="fa-solid fa-chevron-down" style="color: #ffffff;"></i>
                                                </a>
                                                <ul class="p-0" id="${notification.other_time_id}-notify-options" hidden>
                                                    <li class = "flex-center">
                                                        <button type ="button" class ="btn btn-sm fs-6 fw-light p-0 rounded-pill btn-approve-othertimes"
                                                                data-othertime-id = "${notification.other_time_id}"
                                                                data-approved = "1" 
                                                                data-time = "${notification.other_times.time}" 
                                                                data-description = "${notification.other_times.description}" 
                                                                data-user-to = "${notification.from_user.id}">
                                                            <i class="fa-solid fa-square-check fa-lg" style="color: #02a500;"></i>
                                                        </button>
                                                    </li>

                                                    <li class = "flex-center">
                                                        <button type ="button" class ="btn btn-sm fs-6 fw-light p-0 rounded-pill btn-approve-othertimes" 
                                                                data-othertime-id = "${notification.other_time_id}"
                                                                data-approved = "0"
                                                                data-time = "${notification.other_times.time}"
                                                                data-description = "${notification.other_times.description}"
                                                                data-user-to = "${notification.from_user.id}">
                                                            <i class="fa-solid fa-square-xmark fa-lg" style="color: #a50000;"></i>
                                                        </button>
                                                    </li>
                                                </ul>
                                            </div>

                                            <div class = "d-flex flex-column">
                                                <div class="small font-weight-bold">${this.formatDateHourFromISO(notification.created_at)}</div>
                                                <span class="font-weight-bold text-white">${notification.from_user.name} ${notification.message} </span>
                                            </div>`;

                if (index > 0) {
                    container.insertBefore(notificationDiv, container.firstChild)
                } else {
                    container.appendChild(notificationDiv);
                }
            }
        };

        const buildNotificationBirthdays = (notification, index) => {
            if (index < max_notifications) {
                let notificationDiv = document.createElement('div');
                notificationDiv.classList.add('dropdown-item', 'd-flex', 'align-items-center', 'body-notifications', 'border-0');
                notificationDiv.setAttribute('id', `alert-${notification.id}`);

                notificationDiv.innerHTML = `<div class="mr-3">
                                                <div class="viewer-notifications icon-circle ${getBgIcon(notification.type)}">
                                                    <span class = "border border-2 border-primary">
                                                        <img src="../img/img_perfil/${notification.from_user.id}/${notification.data_user.url_img}" alt="Foto de perfil">
                                                    </span>
                                                </div>
                                            </div>

                                            <div class = "d-flex flex-column">
                                                <div class="small font-weight-bold">${this.formatDateHourFromISO(notification.created_at)}</div>
                                                <span class="font-weight-bold text-white">&#127881; ¡${notification.from_user.name} ${notification.message.toLowerCase()}!</span>
                                            </div>`;

                if (index > 0) {
                    container.insertBefore(notificationDiv, container.firstChild)
                } else {
                    container.appendChild(notificationDiv);
                }
            }
        };

        const buildNotificationActs = (notification, index) => {
            if (index < max_notifications) {
                let notificationDiv = document.createElement('div');
                notificationDiv.classList.add('dropdown-item', 'd-flex', 'align-items-center', 'body-notifications', 'border-0');
                notificationDiv.setAttribute('id', `alert-${notification.id}`);

                notificationDiv.innerHTML = `<div class="mr-3">
                                                <div class="viewer-notifications icon-circle ">
                                                    <span class = "border border-2 border-primary">
                                                        <img src="/img/LogoTrakioRounded.png" alt="Logo">
                                                    </span>
                                                </div>
                                            </div>

                                            <div class = "d-flex flex-column">
                                                <div class="small font-weight-bold">${this.formatDateHourFromISO(notification.created_at)}</div>
                                                <span class="font-weight-bold text-white">${notification.message}</span>
                                            </div>`;

                if (index > 0) {
                    container.insertBefore(notificationDiv, container.firstChild)
                } else {
                    container.appendChild(notificationDiv);
                }
            }
        }

        const buildNotificationSignature = (notification, index) => {
            if (index < max_notifications) {
                let notificationDiv = document.createElement('div');
                notificationDiv.classList.add('dropdown-item', 'd-flex', 'align-items-center', 'body-notifications', 'border-0');
                notificationDiv.title = 'Haga click aquí para firmarlo';
                notificationDiv.setAttribute('id', `alert-${notification.id}`);

                notificationDiv.innerHTML = `<a href = "${route('document.sign', {'document': notification.data})}">
                                                <div class="mr-3">
                                                    <div class="viewer-notifications icon-circle ">
                                                        <span class = "border border-2 border-primary">
                                                            <img src="/img/LogoTrakioRounded.png" alt="Logo">
                                                        </span>
                                                    </div>
                                                </div>

                                                <div class = "d-flex flex-column">
                                                    <div class="small font-weight-bold">${this.formatDateHourFromISO(notification.created_at)}</div>
                                                    <span class="font-weight-bold text-white">${notification.message}</span>
                                                </div>
                                            </a>`;

                if (index > 0) {
                    container.insertBefore(notificationDiv, container.firstChild)
                } else {
                    container.appendChild(notificationDiv);
                }
            }
        }

        const appendNotifications = () => {
            if (Array.isArray(notifications) && notificationsCount > 0) {
                const notificationHandlers = {
                    'other-times': buildNotificationOt,
                    'other-times-response': builNotificationInformative,
                    'cumpleaños': buildNotificationBirthdays,
                    'report-weekly': buildNotificationActs,
                    'document-signature': buildNotificationSignature,
                    'document-signature-response': builNotificationInformative,
                    'consecutivo-permission-request': buildNotificationConsecutivoPermission,
                    'consecutivo-permission-response': builNotificationInformative,
                };
        
                // Limitar la cantidad de notificaciones a procesar
                const maxNotifications = Math.min(notificationsCount, max_notifications);
        
                for (let i = 0; i < maxNotifications; i++) {
                    const notification = notifications[i];
                    const handler = notificationHandlers[notification.type.toLowerCase()];
                    if (handler) {
                        handler(notification, i);
                    }
                }

                const remainNotifications = notificationsCount - max_notifications;
                if (remainNotifications > 0) {
                    appendMoreAlerts(remainNotifications);
                }
            }
        };

        const appendMoreAlerts = (remainNotifications) => {
                let notificationData = document.createElement('a');
                notificationData.classList.add('dropdown-item', 'text-center', 'small', 'text-gray-500', 'font-weight-bold');
                notificationData.setAttribute('id', 'more-alerts');
                notificationData.textContent = `Hay ${remainNotifications}  ${remainNotifications > 1 ? 'notificaciones' : 'notificación'}  más`;
                container.appendChild(notificationData);
        }

        const headerAlerts = () => {
            let headerH6 = document.createElement('h6');
            headerH6.classList.add('dropdown-header', 'header-notifications', 'border-0');
            headerH6.textContent = 'Notificaciones';
            headerH6.setAttribute('id', 'dropdown-header');
            container.insertBefore(headerH6, container.firstChild);
        }

        badgeCounter();
        appendNotifications();
        headerAlerts();
    }

    showMailBoxNotifications(notifications) {
        const container = document.getElementById('mail-box-dash');
        const noNotificationsElement = document.querySelector('.no-notifications');

        if (notifications.length === 0) {
            if (!noNotificationsElement) {
                container.innerHTML = `
                    <div class="no-notifications">
                        <i class="fas fa-bell-slash"></i>
                        <span>No hay actividad reciente</span>
                    </div>`;
            }
            return;
        }
    
        if (noNotificationsElement) {
            noNotificationsElement.remove();
        }
    
        const fragment = document.createDocumentFragment();
    
        notifications.forEach(notification => {
            const className = notification.status == 0 ? 'no-leida' : 'leida';
            const newContainer = document.createElement('div');
            newContainer.classList.add('notification-container', className);
    
            newContainer.innerHTML = `
                <div class="${className} position-relative">
                    <span>${notification.message}</span>
                    <div class="notification-footer">
                        <a class="notification-btn"
                            href="${route('receptions.index', {'environment': notification.data, 'notification': notification.id})}">
                            Detalles
                        </a>
                        <span class="notification-date">${this.formatDateHourFromISO(notification.created_at)}</span>
                    </div>
                </div>`;
    
            fragment.appendChild(newContainer);
        });
            
        container.prepend(fragment);
    }    
}