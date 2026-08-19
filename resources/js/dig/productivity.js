import { route } from 'ziggy-js';
import Productivity from "../modules/Productivity.js";
import Package from "../modules/Package.js";

let currentView = 'active'; // 'active' o 'finished'

document.addEventListener('DOMContentLoaded', () => {
    const container = document.getElementById('table-selected-package');
    const gameContainer = document.getElementById('game-container');

    if (gameContainer || container) {
        showListPackagesAssignment();
        buttonScrollPackages();
        initFilters();
    }
});

function initFilters() {
    const btnActive = document.getElementById('btn-filter-active');
    const btnFinished = document.getElementById('btn-filter-finished');

    if (btnActive && btnFinished) {
        btnActive.addEventListener('click', () => {
            if (currentView === 'active') return;
            currentView = 'active';
            updateFilterUI(btnActive, btnFinished);
            showListPackagesAssignment();
        });

        btnFinished.addEventListener('click', () => {
            if (currentView === 'finished') return;
            currentView = 'finished';
            updateFilterUI(btnFinished, btnActive);
            showListPackagesAssignment();
        });
    }
}

function updateFilterUI(activeBtn, inactiveBtn) {
    activeBtn.classList.add('active');
    inactiveBtn.classList.remove('active');
    // Limpiamos la tabla al cambiar de filtro para evitar confusión
    document.getElementById('package-table').innerHTML = '';
}

window.addEventListener('resize', checkOverflowPackages);

function checkOverflowPackages() {
    const listaPaquetes = document.getElementById('packages-list');
    const arrowLeft = document.getElementById('arrowLeft');
    const arrowRight = document.getElementById('arrowRight');

    if (!listaPaquetes || !arrowLeft || !arrowRight) return;

    const isScrollable = listaPaquetes.scrollWidth > listaPaquetes.clientWidth;

    arrowLeft.classList.toggle('d-flex', isScrollable);
    arrowRight.classList.toggle('d-flex', isScrollable);
    arrowLeft.hidden = !isScrollable;
    arrowRight.hidden = !isScrollable;
}

function buttonScrollPackages() {
    const arrows = document.querySelectorAll('.left-arrow, .right-arrow');
    const container = document.getElementById('packages-list');

    arrows.forEach(arrow => {
        arrow.addEventListener('click', () => {
            const scrollBy = arrow.classList.contains('left-arrow') ? -300 : 300;
            container.scrollBy({ left: scrollBy, behavior: 'smooth' });
        });
    });
}

// async function getTotalPackageHours(packageId) {
//     const packageInstance = new Package();
//     const totalHours = await packageInstance.totalHours(packageId);
    
//     return `<span class="total-hours badge bg-info">${totalHours} hrs</span>`;  
// }

function showListPackagesAssignment() {
    const url = route('assignments.show');

    fetch(url)
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('table-selected-package');
            const gameContainer = document.getElementById('game-container');

            if (!data.assignments || data.assignments.length === 0) {
                $(gameContainer).show();
                container.hidden = true;
                return;
            }

            // Filtrado lógico: status 3 es terminado
            const filtered = data.assignments.filter(item => {
                const status = String(item.package.status);
                return currentView === 'active' ? status !== '3' : status === '3';
            });

            buildCards(filtered);
            handleReportAndUpdateProductivity();
        });
}

async function buildCards(assignments) {
    const listContainer = document.getElementById('packages-list');
    let html = '';

    const statusMap = {
        '1': { label: 'Sin Iniciar', color: 'text-secondary' },
        '2': { label: 'En proceso', color: 'text-warning' },
        '3': { label: 'Terminado', color: 'text-success' }
    };

    if (assignments.length === 0) {
        html = `<div class="p-4 text-muted w-100 text-center">No hay paquetes para mostrar en esta sección.</div>`;
    } else {
        assignments.forEach(item => {
            const pkg = item.package;
            const statusInfo = statusMap[pkg.status] || statusMap['1'];
            const isFinishedClass = pkg.status == '3' ? 'finished' : '';
            // Convertir a formato HH:mm, pkg.total_hours son minutos
            const hours = Math.floor(pkg.total_hours / 60);
            const minutes = pkg.total_hours % 60;
            const formattedTime = `${hours}h ${minutes}m`;

            html += `
                <button type="button" id="${pkg.id}" class="p-0 btn-package border-0 bg-transparent">
                    <div class="package-card ${isFinishedClass}">
                        <div class="package-header d-flex justify-content-between align-items-center gap-3">
                            <span class="package-number">${pkg.num_package}</span>
                            <small class="package-status ${statusInfo.color} fw-bold">
                                ${statusInfo.label}
                            </small>
                        </div>
                        <div class="pkg-times">
                            <div class="time-block">
                                <span class="time-icon">INICIO</span>
                                <span class="time-val">${pkg.start_at ?? '--:--'}</span>
                            </div>
                            <span class="time-arrow">→</span>
                            <div class="time-block">
                                <span class="time-icon">FIN</span>
                                <span class="time-val">${pkg.end_at ?? '--:--'}</span>
                            </div>
                        </div>
                        <div class="package-body d-flex flex-column mt-2 text-start gap-1">
                            <span class="package-info"><i class="fa-solid fa-file-lines me-2"></i>${pkg.receptions_count} fichas</span>
                            ${pkg.observations ? `<small class="text-truncate text-muted" style="max-width: 150px;">${pkg.observations}</small>` : ''}
                        </div>
                        <span class="badge bg-primary w-100">${formattedTime}</span>
                    </div>
                </button>`;
        });
    }

    listContainer.innerHTML = html;
    document.getElementById('table-selected-package').hidden = false;

    checkOverflowPackages();
    handleShowFilesPackages();
}

function formatAccumulatedTime(totalSeconds) {
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
}

function setProductivityTimeCounter(totalMinutes) {
    const summary = document.getElementById('productivity-time-summary');
    const summaryValue = document.getElementById('productivity-time-value');

    if (!summary || !summaryValue) return;

    summary.dataset.totalMinutes = totalMinutes;
    summaryValue.textContent = formatAccumulatedTime(totalMinutes);
    summary.classList.toggle('d-none', totalMinutes <= 0);
    summary.classList.remove('animate-counter-up');
    void summary.offsetWidth;
    summary.classList.add('animate-counter-up');
    sessionStorage.setItem('productivityAccumulatedTime', totalMinutes);
}

function updateProductivityTimeCounterFromValue(calculationTime) {
    const seconds = Number(calculationTime || 0);
    setProductivityTimeCounter(seconds);
}

let tableEventAdded = false;
function handleReportAndUpdateProductivity() {
    const tablePackage = document.getElementById('package-table');
    if (!tablePackage || tableEventAdded) return;

    tablePackage.addEventListener('click', (e) => {
        const target = e.target;
        if (target.tagName === 'INPUT' && target.classList.contains('productivity-report')) {
            if (target.checked) {
                saveProductivity(target.dataset)
                    .then((response) => {
                        const reportedTime = response?.data?.calculation_time;
                        if (reportedTime !== undefined && reportedTime !== null) {
                            updateProductivityTimeCounterFromValue(reportedTime);
                            window.DailyProgressWidget?.addSeconds(reportedTime);
                        }
                        target.disabled = true;
                    })
                    .catch(() => {
                        target.checked = false;
                        target.disabled = false;
                    });
            }
        }
        if (target.tagName === 'BUTTON' && target.classList.contains('btn-update-productivity')) {
            updateProductivity(target.dataset, target);
        }
    });
    tableEventAdded = true;
}

async function handleShowFilesPackages() {
    const UtilityModule = await import('../modules/Utility.js');
    const utility = new UtilityModule.default();
    utility.simpleMenuNav('btn-package');

    document.querySelectorAll('.btn-package').forEach(button => {
        button.addEventListener('click', async () => {
            const container = document.getElementById('package-table');
            utility.containerLoader(container);
            await new Promise(resolve => setTimeout(resolve, 0));
            showPackageFiles(button.id);
        });
    });
}

async function showPackageFiles(package_id = null) {
    const packageModule = await import("../modules/Package.js");
    const packageInstance = new packageModule.default();
    sessionStorage.setItem('currentPackageId', package_id);
    packageInstance.id = package_id;
    packageInstance.showFilesPackages('digitizer');
}

async function saveProductivity(dataset) {
    const fileId = dataset.fileId;
    const data = {
        file_id: fileId,
        base_id: dataset.baseId,
        package_id: dataset.packageId,
        quantity_users: document.getElementById(`quantity-users-${fileId}`).value,
        observations: document.getElementById(`observations-${fileId}`).value,
        type: document.getElementById(`is-update-${fileId}`).checked ? '1' : '0'
    };
    const productivity = new Productivity();
    const response = await productivity.store(data);
    showListPackagesAssignment(); // Recarga para actualizar si el paquete se completó
    return response;
}

async function updateProductivity(dataset, button) {
    const fileId = dataset.fileId;
    const data = {
        quantity_users: document.getElementById(`quantity-users-new-${fileId}`).value,
        observations: document.getElementById(`observations-new-${fileId}`).value,
        type: document.getElementById(`is-update-new-${fileId}`).checked ? '1' : '0'
    };
    const productivity = new Productivity();
    productivity.id = fileId;
    productivity.buttonLoader(button);
    await productivity.update(data);
}