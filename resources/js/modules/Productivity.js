import Package from "./Package";
import Queries from "./Queries";
import Swal from 'sweetalert2';

export default class Productivity extends Queries {

    #id;

    constructor(id) {
        super();
        this.#id = id;

        this.isSubmitting = false;
    }

    get id() {
        return this.#id;
    }

    set id(id) {
        this.#id = id;
    }

    async actualizarBarraDeProgreso(totalTime, e) {
        try {
            const response = await fetch('/obtener-entorno');
            const data = await response.json();
            const entornoId = e; // ID del entorno recibido

            const valorEntorno = data.entornos[entornoId] || 'No encontrado';
            const fecha = new Date(); // Obtener la fecha actual
            const dia = String(fecha.getDate()).padStart(2, '0'); // Formatear día con dos dígitos
            const mes = String(fecha.getMonth() + 1).padStart(2, '0'); // Obtener el mes (+1 porque en JS los meses van de 0 a 11)

            const claveFecha = `${dia}/${mes}`; // Crear la clave con el formato "DD/MM"

            const valordia = (valorEntorno[claveFecha]?.meta !== undefined)
                ? valorEntorno[claveFecha].meta
                : 8;

            const progressLabelsContainer = document.getElementById("progress-labels");

            const maxHours = valordia;
            progressLabelsContainer.innerHTML = "";
            for (let i = valordia; i >= 0; i--) {
                let label = document.createElement("span");
                label.textContent = i + " hrs";
                progressLabelsContainer.appendChild(label);
            }

            const porcentaje = Math.min((totalTime / maxHours) * 100, 100); // Limita a 100%
            const progressBar = document.querySelector(".bar");
            progressBar.style.height = `${porcentaje}%`;

            if (porcentaje < 50) {
                progressBar.style.background = "linear-gradient(0deg, #750000, #ff5100)";
            } else if (porcentaje < 80) {
                progressBar.style.background = "linear-gradient(0deg, #750000, #ff5100,rgb(255, 145, 0))"; // Naranja-Amarillo
            } else {
                progressBar.style.background = "linear-gradient(0deg, #750000, #ff5100, #00ff00)"; // Verde
            }

            const HorasDay = maxHours;
            const totalProcentajeDay = (totalTime / HorasDay) * 100;

            // Mostrar el porcentaje en el HTML
            $("#PorcentajeDay").html("+" + totalProcentajeDay.toFixed(2) + "%");

            // Remover clases anteriores
            $("#PorcentajeDay").removeClass(
                "text-danger text-warning text-success"
            );

            // Cambiar el color según el valor del porcentaje
            if (totalProcentajeDay < 33) {
                $("#PorcentajeDay").addClass("text-danger");
            } else if (totalProcentajeDay < 50) {
                $("#PorcentajeDay").addClass("text-warning"); // Naranja
            } else {
                $("#PorcentajeDay").addClass("text-success");
            }
        } catch (error) {
            console.error('Error al obtener el JSON:', error);
        }
    }

    progressbarUpdate() {
        const progressLabelsContainer = document.getElementById("bar-progres");

        fetch('productivity-day')
            .then(response => response.json())
            .then(data => {

                if (data.day.horas == 0) {
                    progressLabelsContainer.hidden = true
                } else {
                    this.actualizarBarraDeProgreso(data.day.horas, data.entorno);
                }
            })
            .catch(error => console.error("Error fetching data:", error));
    }

    async store(data) {

        if (this.isSubmitting) {
            console.warn("Ya se está enviando, espera...");
            return;
        }

        this.isSubmitting = true;

        if (typeof data !== 'object' || data === null || Array.isArray(data)) {
            console.error("El parámetro data no es válido");
            return;
        }

        try {
            this.isSubmitting = true; // Bloqueamos el botón al empezar
            const url = route('productivity.store');
            const response = await this.axiosPost(url, data);

            // ÉXITO: El controlador devolvió 200 OK
            await Swal.fire({
                icon: 'success',
                title: '¡Guardado!',
                text: response.data.message || 'Datos guardados correctamente',
                timer: 2000,
                showConfirmButton: false,
                timerProgressBar: true,
            });

            this.progressbarUpdate();
            return response;

        } catch (error) {
            // ERROR: Capturamos fallos del servidor (500, 404) o de red
            let mensajeError = 'Ocurrió un error inesperado';

            if (error.response && error.response.data) {
                // Si el controlador envió un mensaje específico de error
                mensajeError = error.response.data.error || error.response.data.message;
            } else if (error.message.includes('SDS')) {
                // Por si tienes una validación manual previa
                mensajeError = 'Ficha no encontrada en SDS. Verifique la funcionalidad de Trakio Injector';
            }

            Swal.fire({
                icon: 'error',
                title: 'Atención',
                text: mensajeError,
                confirmButtonColor: '#d33',
                confirmButtonText: 'Entendido'
            });

            throw error; // Re-lanzamos por si algún componente padre necesita saber que falló

        } finally {
            // Esto se ejecuta SIEMPRE (haya éxito o error)
            const packageId = sessionStorage.getItem('currentPackageId');
            if (packageId) {
                const packageInstance = new Package();
                packageInstance.id = packageId;
                packageInstance.showFilesPackages('digitizer');
            }

            this.isSubmitting = false; // Liberamos la bandera
        }
    }

    async update(data) {
        const url = route('productivity.update', { productivity: this.#id });
        if (typeof data !== 'object' || data === null || Array.isArray(data)) {
            console.error("El parámetro data no es válido");
            return;
        }

        const headers = {
            data: data
        }

        try {
            const response = await this.axiosPut(url, headers);
            this.progressbarUpdate();
            return response;
        } catch (error) {
            alert('No fue posible actualizar la ficha');
            throw error;
        } finally {
            const packageInstance = new Package();
            packageInstance.id = sessionStorage.getItem('currentPackageId');
            packageInstance.showFilesPackages('digitizer');
        }
    }
}
