import Queries from "./Queries.js";
import { route } from 'ziggy-js';

export default class Dashboard extends Queries {
    #environment;
    #range;
    #countReceptions;

    constructor(environment, range = {}) {
        super();
        this.#environment = environment;
        this.#range = range;
    }

    set environment(environment) {
        this.#environment = environment;
    }

    get environment() {
        return this.#environment;
    }

    set range(range) {
        this.#range = range;
    }

    get range() {
        return this.#range;
    }

    get countReceptions() {
        return this.#countReceptions;
    }

    set countReceptions(countReceptions) {
        this.#countReceptions = countReceptions;
    }

    async countFormatReceptionsOnGesi() {
        try {
            const url = route('formats.count.gesi');
            const response = await this.axiosGet(url);

            return response.data;
        } catch (error) {
            console.error(error);
        }
    }

    async observationsPercentage(dates = {}) {
        try {
            const url = route('observations.percentage');
            const response = await this.axiosPost(url, dates);

            return response.data;
        } catch (error) {
            console.error(error);
        }
    }

    async receivedCount() {
        try {
            const url = route('receptions.dashboard');
            const response = await this.axiosGet(url);

            return response.data;
        } catch (error) {
            console.error(error);
        }
    }

    async productivity() {
        const url = route('adm.dashboard.productivity');

        const data = {
            environment: this.#environment,
            range: this.#range
        }

        try {
            const response = await this.axiosPost(url, data);
            return response.data;
        } catch (error) {
            console.error('Error:', error);
        }
    }

    productivityHTML(productivity, other_times) {
        if (productivity.length === 0) {
            return `<p class = "text-warnings">
                        No hay información disponible para el periodo seleccionado
                    </p>`;
        }

        let table = '';

        const total_hrs = (minutes) => {
            const hours = minutes / 60;
            return hours.toFixed(1);
        };

        const total_ot = (productivity_dig_id) => {
            const ot = other_times.find(ot => ot.id_user === productivity_dig_id);
            return ot ? ot.total : 0;
        };

        const productivity_total = (productivity, ot) => {
            return (parseFloat(productivity) + parseFloat(ot));
        };

        const sortedProductivity = productivity.map(prod => {
            // Obtener digitadores sin productividad comparando con los IDs

            let hoursProductivity = total_hrs(prod.total);
            let ot = total_ot(prod.dig_id).toFixed(1);

            // extraer el id que esta en oither_times(id_user ) pero no en productivity(dig_id)
            if (other_times.map(ot => ot.id_user).includes(prod.dig_id)) {
                // Eliminar del arreglo other_times
                other_times = other_times.filter(ot => ot.id_user !== prod.dig_id);
            }


            const total = productivity_total(hoursProductivity, ot).toFixed(1);
            return {
                ...prod,
                ot,
                hoursProductivity,
                total,
            };
        });

        const new_other_times = other_times.map(ot => {
            let hoursProductivity = 0;
            let ot_hours = (ot.total / 60).toFixed(1);
            const total = productivity_total(hoursProductivity, ot_hours).toFixed(1);
            return {
                ...ot,
                ot: ot_hours,
                hoursProductivity,
                total,
            };
        });

        // Combinar ambos arreglos
        const combinedProductivity = [...sortedProductivity, ...new_other_times];

        // Ordenar por total de horas en forma descendente
        combinedProductivity.sort((a, b) => b.total - a.total);

        // Generar HTML
        combinedProductivity.forEach((prod, key) => {
            const userData = prod.user || prod.user_id;
            const fullName = this.properNouns(this.nameAndLastName(userData.name, userData.last_name));

            // Ruta dinámica
            const imgPath = `../img/img_perfil/${prod.data_user.id_user}/${prod.data_user.url_img}`;

            // Fallback: Si no hay imagen en la base de datos, usamos una por defecto
            const finalImg = prod.data_user.url_img ? imgPath : '../img/default-avatar.png';

            table += `
    <div class="rank-user-row">
        <div class="rank-position">${key + 1}</div>
        
        <div class="rank-profile-wrapper">
            <img src="${finalImg}" 
                 onerror="this.src='../img/default-avatar.png';">
        </div>

        <div class="rank-user-details">
            <span class="rank-user-name">${fullName}</span>
            <span class="rank-user-role">Digitador</span>
        </div>

        <div class="rank-metrics-box">
            <div class="rank-metric-unit">
                <small class="rank-metric-label">Prod</small>
                <span class="rank-metric-value">${prod.hoursProductivity}h</span>
            </div>
            <div class="rank-metric-unit">
                <small class="rank-metric-label">OT</small>
                <span class="rank-metric-value">${prod.ot}h</span>
            </div>
            <div class="rank-metric-unit rank-metric-total">
                <small class="rank-metric-label">Total</small>
                <strong>${prod.total}h</strong>
            </div>
        </div>
    </div>`;
        });

        return table;
    }

    rangeDatesProductivity() {
        let today = new Date();
        let startDate, endDate;

        startDate = new Date(today.getFullYear(), today.getMonth(), 1);
        endDate = new Date(today.getFullYear(), today.getMonth() + 1, 0);

        // startDate = new Date(2024, 5, 1);
        // endDate = new Date(2024, 5 + 1, 0);

        startDate = moment(startDate).format('YYYY-MM-DD');
        endDate = moment(endDate).format('YYYY-MM-DD');

        return [startDate, endDate];
    }
}