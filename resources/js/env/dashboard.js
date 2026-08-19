import Dashboard from '../modules/Dashboard.js';
const dashboard = new Dashboard();

async function getDashboardData (){
    setSkeletonCells();
    renderGrafica();
    const init_dateInput = document.getElementById('init_date');
    const end_dateInput = document.getElementById('end_date');

    if (init_dateInput.value === '') {
        const firstDayOfMonth = new Date();
        firstDayOfMonth.setDate(1);
        init_dateInput.value = firstDayOfMonth.toISOString().split('T')[0];
    }

    if (end_dateInput.value === '') {
        const today = new Date();
        end_dateInput.value = today.toISOString().split('T')[0];
    }

    const dates = {
        init_date: init_dateInput.value,
        end_date: end_dateInput.value
    };
    
    let response = await dashboard.observationsPercentage(dates);
    const productivity = response.data;

    const observationsPercentageChart = async () => { 
        const ctx = document.getElementById('observationsPercentage').getContext('2d');
        const porcentaje = productivity.percentageObservations;
        
        const gaugeData = {
            datasets: [{
                data: [porcentaje, 100 - porcentaje],
                backgroundColor: [
                    getGaugeColor(porcentaje),
                    'rgba(0, 0, 0, 0)'
                ],
                borderWidth: 0,
                circumference: 180, // Media vuelta (180 grados)
                rotation: 270, // Comienza desde la parte superior
                needleValue: porcentaje // Valor para la aguja
            }, {
                data: [25, 25, 25, 25],
                backgroundColor: [
                    'rgba(0, 190, 44, 0.47)',   // Verde claro (suave)
                    'rgba(255, 193, 7, 0.5)',   // Amarillo (medio)
                    'rgba(253, 126, 20, 0.7)',  // Naranja (más opaco)
                    'rgba(220, 53, 69, 0.9)'    // Rojo (más intenso)
                ],
                circumference: 180,
                rotation: 270,
                borderWidth: 0,
                weight: 0.3
            }]
        };

        new Chart(ctx, {
            type: 'doughnut',
            data: gaugeData,
            options: {
                responsive: true,
                cutout: '80%',
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        enabled: false
                    }
                },
                events: []
            },
            plugins: [{
                id: 'gaugeNeedle',
                afterDraw: (chart) => {
                    const {ctx, chartArea: {width, height}, data} = chart;
                    const needleValue = data.datasets[0].needleValue;
                    
                    // Texto del porcentaje
                    ctx.font = '24px Arial';
                    ctx.fillStyle = '#fff';
                    ctx.textAlign = 'center';
                    ctx.fillText(`${needleValue.toFixed(1)}%`, width / 2, height * 0.7);
                }
            }]
        });

        insertObservationsByFormat();
    };

    const insertObservationsByFormat = () => {
        const container = document.querySelector('.observations-by-format-container');
        const htmlList = `
            <ul class="list-group list-group-flush">
                ${productivity.observationsCountByFormat.map(format => `
                    <li class="list-group-item d-flex justify-content-between align-items-center gap-4">
                        <span class="format-name">${format.name}</span>
                        <span class="badge bg-primary rounded-pill">${format.total}</span>
                    </li>
                `).join('')}
            </ul>
        `;
        container.appendChild(document.createRange().createContextualFragment(htmlList));
    }

    const getGaugeColor = (value) => {
        if (value < 25) return 'rgba(0, 190, 44, 0.47)';
        if (value < 50) return 'rgba(255, 193, 7, 0.5)';
        if (value < 75) return 'rgba(253, 126, 20, 0.7)';
        if (value < 100) return 'rgba(220, 53, 69, 0.9)';

        return '#36A2EB';
    }

    const lastObservations = () => {
        const container = document.getElementById('observations-container-dash');
        container.innerHTML = '';

        const formatDate = (dateStr) => {
            return new Date(dateStr).toLocaleDateString('es-ES', {
                day: 'numeric',
                month: 'long',
                year: 'numeric',
            });
        };

        productivity.observations.forEach(observation => {
            const formattedDate = formatDate(observation.created_at);
            const observationBtn = document.createElement('a');
            observationBtn.href =  route('env.traceability', {file_number_search: observation.file_number, format_search: observation.bases.id});
            observationBtn.title = 'Ver ficha';
            observationBtn.className = 'observation-button';
            observationBtn.type = 'button';

            observationBtn.innerHTML = `
                <div class="observation-header">
                    <span class="observation-format">${observation.bases.name}</span>
                    <span class="observation-ficha">Ficha: ${observation.file_number}</span>
                </div>
                <div class="observation-content">${observation.observations}</div>
                <div class="observation-footer">${formattedDate}</div>
            `;

            container.appendChild(observationBtn);
        });
    };
    
   
    if (productivity.percentageObservations) {
        observationsPercentageChart();
        removeMessageNoData('message-no-info-2');
    }else messageNoData('message-no-info-2');

    removeSkeletonCells();
    messageNoData('message-no-info-3');
}

async function formatsOnGesiChar (){
    const formatsOnGesi = await dashboard.countFormatReceptionsOnGesi();

    if (!formatsOnGesi && !Array.isArray(formatsOnGesi) && !formatsOnGesi.length > 0) {
        messageNoData('message-no-info-4');
        return;
    }

    removeMessageNoData('message-no-info-4');

    const ctx = document.getElementById('receptionByFormat').getContext('2d');
    const variedColors = [
        "rgba(121, 2, 2, 0.8)",    // rojo
        "rgba(0, 123, 255, 0.8)",  // azul
        "rgba(40, 167, 69, 0.8)",  // verde
        "rgba(255, 193, 7, 0.8)",  // amarillo
        "rgba(220, 53, 69, 0.8)",  // rojo claro
        "rgba(23, 162, 184, 0.8)", // cian
        "rgba(108, 117, 125, 0.8)",// gris
        "rgba(255, 87, 34, 0.8)",  // naranja
        "rgba(156, 39, 176, 0.8)", // púrpura
        "rgba(0, 188, 212, 0.8)",  // turquesa
        "rgba(76, 175, 80, 0.8)",  // verde brillante
        "rgba(255, 152, 0, 0.8)",  // naranja fuerte
        "rgba(233, 30, 99, 0.8)",  // rosado
        "rgba(63, 81, 181, 0.8)",  // azul fuerte
        "rgba(205, 220, 57, 0.8)"  // lima
    ];

    const labels = formatsOnGesi.map(item => {
        const nombre = item.format.slice(0, 30) + '...';
        return `${nombre} (${item.total_count})`;
    });
    const totalCounts = formatsOnGesi.map(item => item.total_count);
    
    const data = {
        labels: labels,
        datasets: [
            {
                label: "Fichas en GESI",
                data: totalCounts,
                backgroundColor: variedColors.slice(0, totalCounts.length),
                borderColor: "rgba(255, 255, 255, 0)",
                borderWidth: 2,
                borderRadius: 8,
            }
        ]
    };

    const config = {
        type: 'doughnut',
        data: data,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { 
                    position: 'right',
                    labels: {
                        color: 'rgba(255, 255, 255, 0.8)',
                    }
                }
            },
        }
    };

    new Chart(ctx, config);
};

function messageNoData(containerId) {
    const container = document.getElementById(containerId);
    container.hidden = false;
    container.innerHTML = `
        <div class="no-data-message">
            <i class="fas fa-info-circle no-data-icon"></i>
            <p class="no-data-text">No hay datos disponibles para la fecha seleccionada</p>
        </div>
    `;
}

function removeMessageNoData(containerId, ) {
    const container = document.getElementById(containerId);
    if (container.hidden) return;
    container.hidden = true;
    container.innerHTML = '';
}

function removeSkeletonCells() {
    const skeletonCells = document.querySelectorAll('.skeleton-cell');
    skeletonCells.forEach(cell => {
        cell.classList.remove('skeleton-cell');
    });
}

function setSkeletonCells() {
    const skeletonCells = document.querySelectorAll('.content-box-dashboard');
    skeletonCells.forEach(cell => {
        cell.classList.add('skeleton-cell');
    });
}

// setInterval(() => {
//     getDashboardData();
// }, 300000);

if (existElement('init_date') && existElement('end_date')) {
    document.getElementById('init_date')?.addEventListener('change', getDashboardData);
    document.getElementById('end_date')?.addEventListener('change', getDashboardData);
    
    getDashboardData();
    formatsOnGesiChar();
}

async function renderGrafica() {
    const receprions = window.recepcionesData || [];

    if (!receprions.length) {
        console.warn("No hay datos para mostrar el gráfico de entornos.");
        const msg = document.getElementById("message-no-info-1");
        if (msg) {
            msg.hidden = false;
            msg.textContent = "No hay información disponible para este periodo.";
        }
        return;
    }

    // Agrupamos por entorno y por mes (YYYY-MM)
    const entornos = [...new Set(receprions.map(r => r.entorno))];
    const meses = [...new Set(receprions.map(r => `${r.año}-${String(r.mes).padStart(2, "0")}`))].sort();

    // Creamos datasets por entorno
    const datasets = entornos.map(entorno => ({
        label: entorno == 4?'Comunitario':entorno == 3?'Educativo':entorno == 2?'Laboral':entorno == 6?'Institucional':'Desconocido',
        data: meses.map(m => {
            const registro = receprions.find(r =>
                r.entorno === entorno &&
                `${r.año}-${String(r.mes).padStart(2, "0")}` === m
            );
            return registro ? registro.total : 0;
        }),
        borderWidth: 1,
        borderRadius: 3,
        borderColor: getColor(entorno),   // ← color de la línea
        backgroundColor: getColor(entorno),
        pointBackgroundColor: getColor(entorno),
        pointBorderColor: '#fff',
        pointRadius: 6,
        pointHoverRadius: 8,
        tension: 0.3, // suaviza las líneas
    }));

    // Generador de colores
    function getColor() {
        const colors = [
            "#00bcd4", "#ff9800", "#4caf50", "#e91e63", "#9c27b0",
            "#3f51b5", "#8bc34a", "#ffc107", "#ff5722", "#607d8b"
        ];
        return colors[Math.floor(Math.random() * colors.length)];
    }

    // Nombres de meses legibles (Ej: "Noviembre 2025")
    const etiquetas = meses.map(m => {
        const [año, mes] = m.split("-");
        const nombreMes = new Date(año, mes - 1).toLocaleString("es-ES", { month: "long" });
        return `${nombreMes.charAt(0).toUpperCase() + nombreMes.slice(1)} ${año}`;
    });

    // Creamos el gráfico
    const ctx = document.getElementById("chartEntornos");
    if (!ctx) {
        console.error("No se encontró el canvas #chartEntornos");
        return;
    }

    new Chart(ctx, {
        type: "line",
        data: {
            labels: etiquetas,
            datasets: datasets
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: "top" },
                title: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: { display: true, text: "Cantidad" }
                },
                x: {
                    title: { display: true, text: "Mes" }
                }
            }
        }
    });
}




