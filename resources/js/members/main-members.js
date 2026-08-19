function existElement(idElement) {
    return document.getElementById(idElement) !== null;
}

function fadeOutAlerts() {
    const alerts = document.querySelectorAll('.alert-success, .alert-danger, .alert-warning');
    alerts.forEach(alert => {
        setTimeout(() => {
            if (alert) {
                alert.classList.add('fade-out');
                alert.addEventListener('transitionend', () => {alert.remove();});
            }
        }, 3000);
    })
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
       

function initApp() {
    fadeOutAlerts();
    if (existElement('total_fee')) {
        handleReportActivities();   
    }
}

initApp();