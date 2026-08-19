import Utility from '../modules/Utility.js';
import Notification from '../modules/Notification.js';

function existElement(idElement) {
    return document.getElementById(idElement) !== null;
}

function btnLoaderUtility(){
    const utility = new Utility();
    utility.buttonsLoader();
}

function fadeOutAlert() {
    const alerts = document.querySelectorAll(
        ".alert-success, .alert-danger, .alert-warning"
    );
    alerts.forEach((alert) => {
        setTimeout(() => {
            if (alert) {
                alert.classList.add("fade-out");
                alert.addEventListener("transitionend", () => {
                    alert.remove();
                });
            }
        }, 3000);
    });
}

//Recepciones
async function initReceptions() {
    const receptionModule = await import("../modules/Reception.js");
    const receptionInstance = new receptionModule.default();
    receptionInstance.model = "environment";
    receptionInstance.selectRangeReception();
    receptionInstance.selectAllReceptions();

    document.querySelectorAll(".delete-reception-button").forEach((button) => {
        button.addEventListener("click", async (event) => {
            const receptionId = event.currentTarget.dataset.receptionId;
            try {
                const resultDelete = await receptionInstance.delete(
                    receptionId
                );
                if (resultDelete) {
                    location.reload();
                }
            } catch (error) {
                console.error("No se pudo eliminar la ficha", error);
            }
        });
    });
}

//Entregas
function showButtonUpload() {
    const inputFile = document.getElementById("fileInput");

    inputFile.addEventListener("change", (e) => {
        const file = e.target.files;
        const btnUpload = document.getElementById("btn_upload");
        const spanFileName = document.querySelector('[data-file="name"]');

        if (file.length > 0) {
            spanFileName.innerHTML = `"${file[0].name}"`;
            btnUpload.classList.add("btn-upload-visible");
        } else {
            btnUpload.classList.remove("btn-upload-visible");
            spanFileName.innerHTML = "";
        }
    });
}

//Informe de actividades
async function handleManagerReportActivities() {
    const moduleActivity = await import("../modules/ReportActivity.js");
    const reportActivity = new moduleActivity.default();
    reportActivity.handleActivitiesReportManager();
}

async function handleReportActivities() {
    const total_fee = document.getElementById('total_fee');
    const utilityModule = await import('../modules/Utility.js');
    const utilityInstance = new utilityModule.default();
    const total_fee_string = document.getElementById('total_fee_string');
    
    const numberToWord = () => {
        total_fee_string.value = utilityInstance.numberToWords(total_fee.value);
    }

    total_fee.addEventListener('keyup', numberToWord);
    numberToWord();

    const actions = async () => {
        const utilityReport = await import('../modules/ReportActivity.js');
        const reportInstance = new utilityReport.default();
        reportInstance.handleActionsReportActivities();
    }

    actions();
}

/**
/* NOTIFICACIONES
**/
let currentNotificationData = null;
async function getNotifications() {
    if (existElement('alertsDropdown')) {
        const notification = new Notification();
        const notificationData = await notification.get();
        if (!notification.isEqualJSON(notificationData, currentNotificationData)) {
            notification.showNotifications(notificationData);
            currentNotificationData = notificationData;
        }
    }
}

function initApp() {
    //Cargue de fichas
    if (existElement("fileInput")) {
        showButtonUpload();
    }

    //Edición de perfiles
    if (existElement("editProfiles")) {
        handleManagerReportActivities();
    }

    //Recepción de fichas
    if (existElement("tableDevolution")) {
        initReceptions();
    }

    if(existElement("data-form-activities")){
        handleReportActivities();
    }

    fadeOutAlert();
    btnLoaderUtility();
}

document.addEventListener("DOMContentLoaded", initApp);
setInterval(async() => {
    await getNotifications()
}, 5000);

/**
 * Guardar posición del scroll
 */
window.addEventListener('beforeunload', () => {
    localStorage.setItem('scrollPosition', window.scrollY);
});

window.addEventListener('scroll', () => {
    localStorage.setItem('scrollPosition', window.scrollY);
});

// Restaurar la posición del scroll al cargar
window.addEventListener('load', () => {
    const scrollY = localStorage.getItem('scrollPosition');
    
    if (scrollY !== null) {
        window.scrollTo(0, parseInt(scrollY));
        localStorage.removeItem('scrollPosition');
    }
});

getNotifications();