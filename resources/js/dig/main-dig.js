import Notification from "../modules/Notification.js";
import Productivity from "../modules/Productivity.js"
import Format from "../modules/Format.js";
import Environment from "../modules/Environment.js";
import Dashboard from '../modules/Dashboard.js';

function existElement(idElement) {
    return document.getElementById(idElement) !== null;
}

function isEqual(obj1, obj2) {
    return JSON.stringify(obj1) === JSON.stringify(obj2);
}

function fadeOutAlerts() {
    const alerts = document.querySelectorAll(
        ".alert-success, .alert-danger, .alert-warning"
    );
    alerts.forEach((alert) => {
        setTimeout(() => {
            if (alert) {
                alert.style.transition = "opacity 1s";
                alert.style.opacity = 0;
                setTimeout(() => {
                    alert.remove();
                }, 1000);
            }
        }, 3000);
    });
}

async function buttonsLoader() {
    const utilityModule = await import("../modules/Utility.js");
    const module = new utilityModule.default();
    module.buttonsLoader();
}

/**
 * NOTIFICATIONS
 */
let currentNotificationData = null;
async function getNotifications() {
    if (existElement("alertsDropdown")) {
        const notification = new Notification();
        const notificationData = await notification.get();
        if (
            !notification.isEqualJSON(notificationData, currentNotificationData)
        ) {
            notification.showNotifications(notificationData);
            currentNotificationData = notificationData;
        }
    }
}

/**
* DASHBOARD
*/

/**
 * Compara el total de horas del mes con el último valor visto en este navegador
 * y, si aumentó desde la última carga, muestra una animación "+Xh Ym" llamativa.
 */
const TOTAL_HORAS_STORAGE_KEY = 'lastTotalHorasMesDecimal';

function formatHoursDiffLabel(decimalHours) {
    const totalMinutes = Math.round(decimalHours * 60);
    const hours = Math.floor(totalMinutes / 60);
    const minutes = totalMinutes % 60;
    return hours > 0 ? `+${hours}h ${minutes}m` : `+${minutes}m`;
}

function showHoursGainedIfChanged(anchorElem, currentValue) {
    const previousRaw = localStorage.getItem(TOTAL_HORAS_STORAGE_KEY);
    const previousValue = previousRaw !== null ? parseFloat(previousRaw) : null;
    const current = Number(currentValue ?? 0);

    if (previousValue !== null && !isNaN(previousValue) && current > previousValue) {
        const diff = current - previousValue;
        const container = anchorElem.closest('#main-hours-card') || anchorElem.parentElement;

        if (container) {
            container.classList.add('hours-gain-anchor');
            const popup = document.createElement('div');
            popup.className = 'hours-gain-popup';
            popup.textContent = formatHoursDiffLabel(diff);
            container.appendChild(popup);
            popup.addEventListener('animationend', () => popup.remove());

            anchorElem.classList.remove('hours-gain-pulse');
            void anchorElem.offsetWidth;
            anchorElem.classList.add('hours-gain-pulse');
        }
    }

    localStorage.setItem(TOTAL_HORAS_STORAGE_KEY, current);
}

/**
 * Widget global de progreso diario (barra lateral vertical, meta 8h).
 * Se expone en window para que otros módulos (ej. al reportar una ficha en
 * productivity.js) puedan sumarle tiempo en caliente sin recargar la página.
 */
const DAILY_PROGRESS_META_HOURS = 8;

function formatClockWithSeconds(decimalHours) {
    if (!decimalHours || decimalHours <= 0) return '00:00:00';
    const totalSeconds = Math.round(decimalHours * 3600);
    const hours = Math.floor(totalSeconds / 3600);
    const minutes = Math.floor((totalSeconds % 3600) / 60);
    const seconds = totalSeconds % 60;
    return `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
}

function renderDailyProgress(horasHoyDecimal) {
    const fillElem = document.getElementById('daily_progress_fill');
    const pctElem = document.getElementById('daily_progress_pct');
    const hoursElem = document.getElementById('daily_progress_hours');
    if (!fillElem || !pctElem || !hoursElem) return;

    const pct = Math.min(Math.round((horasHoyDecimal / DAILY_PROGRESS_META_HOURS) * 100), 100);
    fillElem.style.height = pct + '%';
    pctElem.innerText = pct + '%';
    hoursElem.innerText = `${formatClockWithSeconds(horasHoyDecimal)} / 08:00:00`;
}

window.DailyProgressWidget = {
    horasHoy: 0,

    setInitial(horasHoyDecimal) {
        this.horasHoy = Number(horasHoyDecimal || 0);
        renderDailyProgress(this.horasHoy);
    },

    addSeconds(seconds) {
        const addedHours = Number(seconds || 0) / 3600;
        if (addedHours <= 0) return;

        this.horasHoy += addedHours;
        renderDailyProgress(this.horasHoy);
        this.celebrate(addedHours);
    },

    celebrate(addedHours) {
        const widget = document.getElementById('daily-progress-widget');
        if (!widget) return;

        const addedMinutes = Math.max(1, Math.round(addedHours * 60));
        const popup = document.createElement('div');
        popup.className = 'daily-progress-gain-popup';
        popup.textContent = `+${addedMinutes}m`;
        widget.appendChild(popup);
        popup.addEventListener('animationend', () => popup.remove());

        widget.classList.remove('daily-progress-widget--pulse');
        void widget.offsetWidth;
        widget.classList.add('daily-progress-widget--pulse');
    }
};

function dahboardDig() {
    const url = "/dig/productivity-month";

    fetch(url)
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                const metrics = data.metrics;

                // 1. Actualizar Números Principales
                const totalHorasMesElem = document.getElementById('total_horas_mes_num');
                if (totalHorasMesElem) {
                    totalHorasMesElem.innerText = `${metrics.total_horas} min`;
                    showHoursGainedIfChanged(totalHorasMesElem, metrics.total_horas_decimal);
                }

                const canDiaElem = document.getElementById('CanDia');
                if (canDiaElem) canDiaElem.innerText = metrics.fichas_hoy;

                const fichasMesElem = document.getElementById('fichas_digitadas_mes');
                if (fichasMesElem) fichasMesElem.innerText = metrics.fichas_mes;

                const digitacionHoursElem = document.getElementById('digitacion_hours');
                if (digitacionHoursElem) digitacionHoursElem.innerText = metrics.horas_digitacion + 'h';

                const approvedHoursElem = document.getElementById('approved_hours_summary');
                if (approvedHoursElem) approvedHoursElem.innerText = metrics.horas_otros + 'h';

                // 1b. Tiempo acumulado hasta ayer y tiempo de hoy
                const horasHastaAyerElem = document.getElementById('horas_hasta_ayer_val');
                if (horasHastaAyerElem) horasHastaAyerElem.innerText = metrics.horas_hasta_ayer + 'h';

                const horasHoyElem = document.getElementById('horas_hoy_val');
                if (horasHoyElem) horasHoyElem.innerText = metrics.horas_hoy + 'h';

                // 2. Métricas Comparativas (Promedio y Ranking)
                const promedioEquipoElem = document.getElementById('promedio_equipo_val');
                if (promedioEquipoElem) {
                    promedioEquipoElem.innerText = metrics.promedio_equipo + 'h';
                }

                const rankingElem = document.getElementById('ranking_pos_val');
                if (rankingElem) {
                    rankingElem.innerText = `#${metrics.posicion_ranking} de ${metrics.total_colaboradores}`;
                }

                const sigNivelElem = document.getElementById('meta_siguiente_val');
                if (sigNivelElem) {
                    sigNivelElem.innerText = metrics.falta_para_superar_siguiente > 0
                        ? `Te faltan ${metrics.falta_para_superar_siguiente}h para subir de puesto`
                        : '¡Eres el líder de productividad!';
                }

                // 3. LÓGICA MEJORADA DE ALERTA DE RETRASO
                const atrasoElem = document.getElementById('atraso_status_val');
                // Buscamos el card de horas (el col-lg-4 que contiene el total de horas)
                const hoursCard = totalHorasMesElem ? totalHorasMesElem.closest('.modern-card') : null;

                if (atrasoElem && hoursCard) {
                    const dif = metrics.atraso_vs_promedio;

                    if (dif < 0) {
                        // ESTADO: RETRASADO (CRÍTICO)
                        hoursCard.classList.add('card-delayed'); // Aplica el borde rojo y fondo oscuro
                        atrasoElem.innerHTML = `
                            <span class="status-badge-critical mb-1">
                                <i class='bx bxs-error-circle'></i> Retraso Detectado
                            </span>
                            <span class="text-danger fw-bold" style="font-size: 0.9rem;"> 
                                Estás colgado ${Math.abs(dif)}h
                            </span>
                        `;
                    } else {
                        // ESTADO: LIDERANDO (POSITIVO)
                        hoursCard.classList.remove('card-delayed');
                        atrasoElem.innerHTML = `
                            <div class="ranking-badge" style="background: rgba(0, 255, 136, 0.1); color: #00ff88; border-color: rgba(0, 255, 136, 0.2);">
                                <i class='bx bxs-zap'></i> Vas liderando por ${dif}h
                            </div>
                        `;
                    }
                }

                // 4. Barra de Progreso (Meta Mensual)
                const progressFill = document.querySelector('.progress-bar-fill');
                if (progressFill) {
                    progressFill.style.width = metrics.porcentaje_meta + '%';
                    document.getElementById('txt_progreso_horas').innerText = metrics.porcentaje_meta + '%';
                }

                // 4b. Barra de Progreso Vertical (Meta del Día: 8h)
                if (document.getElementById('daily_progress_fill')) {
                    window.DailyProgressWidget.setInitial(metrics.horas_hoy_decimal);
                }

                // 5. Renderizar Lista de Actividades
                const listContainer = document.getElementById('other-times-list');
                if (listContainer && data.actividades) {
                    listContainer.innerHTML = data.actividades.map(act => `
                        <div class="d-flex align-items-center mb-3 p-2 rounded-3" style="background: #181818; border: 1px solid #222;">
                            <div class="me-3 p-2 bg-dark rounded">📋</div>
                            <div class="flex-grow-1">
                                <h6 class="mb-0 small fw-bold text-white text-truncate" style="max-width: 150px;">${act.descripcion}</h6>
                                <small class="text-muted">Día ${act.fecha} — ${act.horas}h</small>
                            </div>
                            <span class="badge ${act.status ? 'text-success' : 'text-warning'} small">
                                ${act.status ? '<i class="bx bxs-check-circle"></i>' : '<i class="bx bx-time"></i>'}
                            </span>
                        </div>
                    `).join('');
                }

                // 6. Actualizar Gráfica (Chart.js)
                if (typeof myChart !== 'undefined' && data.chart) {
                    myChart.data.labels = data.chart.labels;
                    myChart.data.datasets[0].data = data.chart.values;
                    myChart.update();
                }
            }
        })
        .catch(error => console.error('Error al cargar el dashboard:', error));
}

dahboardDig();

// Variable global para la instancia
let myChart;

function initChart() {
    const ctx = document.getElementById('productivity-chart').getContext('2d');

    // Crear degradado para el área debajo de la línea
    const gradient = ctx.createLinearGradient(0, 0, 0, 300);
    gradient.addColorStop(0, 'rgba(255, 60, 65, 0.4)'); // Rojo neón arriba
    gradient.addColorStop(1, 'rgba(255, 60, 65, 0)');   // Transparente abajo

    myChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: [], // Se llena con el fetch
            datasets: [{
                label: 'Fichas Digitadas',
                data: [], // Se llena con el fetch
                borderColor: '#ff3c41',
                borderWidth: 3,
                backgroundColor: gradient,
                fill: true,
                tension: 0.4, // Curva la línea para que sea "moderna"
                pointRadius: 4,
                pointBackgroundColor: '#ff3c41',
                pointHoverRadius: 6,
                hitRadius: 30
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false } // Ocultamos leyenda para más limpieza
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(255, 255, 255, 0.05)' }, // Rejilla muy tenue
                    ticks: { color: '#888', font: { size: 11 } }
                },
                x: {
                    grid: { display: false },
                    ticks: { color: '#888', font: { size: 11 } }
                }
            }
        }
    });
}

// Llamar al inicio
document.addEventListener('DOMContentLoaded', () => {
    initChart();
    //loadDashboardData();
});


let previousDataProductivity = null;


async function showOtherTimes(otherTimes) {
    const utilityModule = await import('../modules/Utility.js');
    const utility = new utilityModule.default();

    let sumaOtherTime = 0;
    $("#approved_hours").html("");

    $("#other-times-list").html("");
    $.each(otherTimes, function (key, otherTime) {
        const dateOtherTime = utility.formatHumanDate(otherTime.date_time);
        let contentData = $(`
            <div class="card-ot-item border-bottom py-2 px-3 rounded d-flex flex-column gap-2 bg-dark text-light">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-circle fa-2xs text-primary me-2"></i>
                        <span class="fw-bold bg-transparent border-0">Solicitud de ${otherTime.time} horas</span>
                    </div>
                    <div>
                        ${otherTime.approved === null
                ? `<span class="badge bg-warning text-dark">Pendiente</span>`
                : otherTime.approved == 1
                    ? `<span class="badge bg-success">Aprobada</span>`
                    : `<span class="badge bg-danger">Rechazada</span>`
            }
                    </div>
                </div>
                <div class="text-muted fst-italic">
                    ${otherTime.description}, para el día ${dateOtherTime}
                </div>
            </div>
        `);

        $("#other-times-list").append(contentData);

        if (otherTime.approved == 1) {
            sumaOtherTime += otherTime.time;
        }
    });

    $("#approved_hours").html(
        "Total aprobadas: <b>" + sumaOtherTime + "<b>"
    );
}

/**
 * OTHER TIMES
 */
if (existElement("otherTimeModal")) {
    function getActivities() {
        return {
            1: "Alistamiento de auditoria",
            2: "Apoyo digitación",
            3: "Corrección de hallazgos",
            4: "Digitación formatos cuídate, sé feliz",
            5: "Digitación formatos sisco",
            6: "Organización de archivo",
            7: "Radicación de documentos",
            8: "Recolección de carnets",
            9: "Revision formatos",
            10: "Devolución de formatos",
            11: "Reunión"
        };
    }

    function getSites() {
        return {
            7: "Caps Garcés Navas",
            8: "Caps Lorencita Villegas",
            9: "Casa PIC Suba",
            10: "Caps Suba",
            11: "Caps Verbenal",
            12: "Ferias",
            13: "Hospital de Chapinero",
            14: "Hospital Engativá - Calle 80",
            15: "Hospital Simón Bolivar",
            16: "Rionegro",
            17: "Secretaría de salud",
        };
    }

    function getDepartments() {
        return {
            1: "GESI",
            2: "Salud pública",
            3: "Coordinación",
        };
    }

    /**
     * Options
     * @returns HTML
     */
    function generateOptionsSites() {
        const sites = getSites();
        let options = '<option value = "">Sede...</option>';

        for (let key in sites) {
            options += `<option value="${key}">${sites[key]}</option>`;
        }

        return options;
    }

    async function getEnvironments() {
        let environments = '';
        if (sessionStorage.getItem('environments')) {
            environments = JSON.parse(sessionStorage.getItem('environments'));
        } else {
            const environment = new Environment();
            environments = await environment.show();
            sessionStorage.setItem('environments', JSON.stringify(environments))
        }

        return environments;
    }

    async function generateOptionsEnvironments() {
        const environments = await getEnvironments();
        let options = '<option value = "">Seleccionar entorno...</option>';
        environments.forEach((environment) => {
            if (environment.id != 0) {
                options += `<option value="${environment.id}">${environment.entorno}</option>`;
            }
        });
        return options;
    }

    async function generateOptionsBases(environmentId) {
        const format = new Format();
        const formats = await format.getByEnvironment(environmentId);

        let options = '<option value = "">Seleccionar formato...</option>';
        formats.forEach((format) => {
            options += `<option value="${format.id}">${format.name}</option>`;
        });
        return options;
    }

    function generateOptionsDepartments(onlyGesi = false) {
        const departments = getDepartments();
        let options = '<option value="">Seleccionar proceso...</option>';

        for (let key in departments) {
            const isGesi = departments[key] === "GESI";

            if ((onlyGesi && isGesi) || (!onlyGesi && !isGesi)) {
                options += `<option value="${key}">${departments[key]}</option>`;
            }
        }

        return options;
    }

    function eventListenersOt() {
        const requestOtInputs = document.querySelectorAll("[data-request-ot]");
        const activity = document.querySelector('[data-request-ot="activity"]');
        const step2 = document.querySelector('[data-request-ot="step-2-ot"]');
        const step3 = document.querySelector('[data-request-ot="step-3-ot"]');
        const activitiesToEnvironment = ["1", "3", "4", "5", "9", "10"];
        const activitiesToDepartments = ["2", "6", "7", "8"];

        const settingStep2 = async (input) => {
            let options = "";

            if (activitiesToEnvironment.includes(input.value)) {
                options = await generateOptionsEnvironments();
            } else {
                options = generateOptionsSites();
            }

            step2.innerHTML = options;
            step2.hidden = false;

            if (activity.value == 4 || activity.value == 5) {
                step3.hidden = true;
            }
        };

        const settingStep3 = async (input) => {
            const activitiesToOnlyGesi = ["1", "4", "5", "10", "11"];

            let options = "";
            let multiple = false;

            if (activity.value == 3 || activity.value == 9) {
                options = await generateOptionsBases(input.value);
                multiple = true;
            } else if (activitiesToDepartments.includes(activity.value)) {
                options = generateOptionsDepartments();
            } else if (activitiesToOnlyGesi.includes(activity.value)) {
                options = generateOptionsDepartments(true);
            }

            if (options) {
                step3.innerHTML = options;
                step3.hidden = false;
                step3.toggleAttribute("multiple", multiple);
            } else {
                step3.hidden = true;
            }
        };

        requestOtInputs.forEach((input) => {
            input.addEventListener("change", async (e) => {
                e.preventDefault();
                const field = e.currentTarget.dataset.requestOt;
                const descriptionTextarea = document.querySelector(
                    '[data-request-ot="description-ot"]'
                );
                switch (field) {
                    case "activity":
                        settingStep2(input);
                        break;

                    case "step-2-ot":
                        settingStep3(input);
                        break;
                }

                const generateOtDescription = async () => {
                    let textActivity =
                        activity.value != null
                            ? getActivities()[activity.value]
                            : "";
                    let textStep2 = "";
                    let textStep3 = "";

                    if (step2.value != "") {
                        if (activitiesToEnvironment.includes(activity.value)) {
                            const environments = await getEnvironments();
                            textStep2 = "entorno ";
                            environments.forEach((environment) => {
                                if (environment.id == step2.value) {
                                    textStep2 += environment.entorno;
                                }
                            });
                        } else {
                            const site = getSites()[step2.value];
                            textStep2 = `en ${site}`;
                        }
                    }

                    if (step3.value != "") {
                        const selectedFormats = Array.from(
                            step3.selectedOptions
                        ).map((format) => format.text.toLowerCase());
                        textStep3 = `(${selectedFormats.join(", ")})`;
                    }

                    descriptionTextarea.value = `${textActivity} ${textStep2} ${textStep3}`;
                };
                generateOtDescription();
            });
        });
    }
    eventListenersOt();
}


function initApp() {
    buttonsLoader();
    fadeOutAlerts();
    getNotifications();
    //saveLoginInfo();

    setInterval(async () => {
        await getNotifications();
    }, 10000);
}

initApp();



