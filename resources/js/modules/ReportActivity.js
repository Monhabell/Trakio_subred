import Queries from "./Queries.js";
import { route } from 'ziggy-js';
export default class ReportActivity extends Queries {
    #role;
    #id;

    constructor(role, id = null) {
        super();
        this.#role = role;
        this.#id = id;
    }

    get id() { return this.#id; }
    set id(value) { this.#id = value; }

    handleActivitiesReportManager() {
        const roleIdInput = document.getElementById("role_id");
        const valuePerHourInput = document.getElementById("hour_value");
        const totalFee = document.getElementById("total_salary");
        const containerTextAreas = document.getElementById(
            "container-textareas"
        );

        const showBtnAddActivities = (hide) => {
            const container = document.getElementById(
                "container-add-activities"
            );
            container.hidden = !hide;
        };

        const updateRoleDetails = (role) => {
            valuePerHourInput.value = role.hour_value;
            totalFee.value = role.total_fee;
            if (role.hour_value > 0 && role.total_salary > 0) {
                this.calculateSalary(role.hour_value);
            }
        };

        const getActivities = async () => {
            if (roleIdInput.value) {
                this.containerLoader(containerTextAreas);
                this.#role = roleIdInput.value;
                const role = await this.getRole();

                if (role) updateRoleDetails(role);
                
                this.buildTextAreas(role.activities.length, role.activities);
                showBtnAddActivities(true);
            } else {
                showBtnAddActivities(false);
            }
        };

        roleIdInput.addEventListener("change", getActivities);
        getActivities();

        valuePerHourInput.addEventListener("keyup", () => {
            this.calculateSalary(valuePerHourInput.value);
        });

        this.showInputObligations();
        this.dragElement(containerTextAreas);
        this.deleteTextarea();
    }

    handleActionsReportActivities(){
        const formData = document.getElementById('data-form-activities');
        formData.addEventListener('submit', (e) => {
            e.preventDefault();
            if(formData.checkValidity()){
                const button = e.submitter;
                const input_type_action = document.getElementById('type_action');
                
                button.classList.add('disabled');
                button.innerHTML = `
                    <div class="dot-spinner">
                        <div class="dot-spinner__dot"></div>
                        <div class="dot-spinner__dot"></div>
                        <div class="dot-spinner__dot"></div>
                        <div class="dot-spinner__dot"></div>
                        <div class="dot-spinner__dot"></div>
                        <div class="dot-spinner__dot"></div>
                        <div class="dot-spinner__dot"></div>
                        <div class="dot-spinner__dot"></div>
                    </div>`;
                input_type_action.value = button.value;
                formData.submit();
            }
        });
    }

    buildTextAreas(numberTextareas, content = "") {
        const mainContainer = document.getElementById("container-textareas");

        if (Array.isArray(content)) {
            mainContainer.replaceChildren();
        }

        const previousElement = mainContainer.previousElementSibling;
        const isNoActivityContainer = previousElement.classList.contains("no-activities-message");

        if (numberTextareas == 0) {
            if (!isNoActivityContainer) {
                const messageNoActivitiesHTML = `   
                            <div class="alert alert-dark d-flex flex-column align-items-center justify-content-center border border-danger mb-0" role="alert">
                                <i class="fas fa-info-circle mb-3 text-danger" style="font-size: 2rem;"></i>
                                <span class="fs-5 fw-bold text-light mb-2 border-0 bg-transparent">Este perfil aún no cuenta con actividades cargadas.</span>
                                <p class="text-light mb-3">
                                    Haga clic en el botón <span class="text-danger fw-bold border-0 bg-transparent">"Agregar obligaciones específicas"</span> para comenzar a asignar actividades a los profesionales.
                                </p>
                            </div>`;

                const newContainerMessage = document.createElement('div');
                newContainerMessage.classList.add("text-center", "mb-2", "no-activities-message", "p-1");
                newContainerMessage.innerHTML = messageNoActivitiesHTML;
                mainContainer.insertAdjacentElement('beforebegin', newContainerMessage);
            }

            return;
        }
        
        if(isNoActivityContainer){
            previousElement.remove();
        }

        const countNodes = mainContainer.children.length;

        for (let i = 0; i < numberTextareas; i++) {
            const newContainerTextarea = document.createElement("div");
            newContainerTextarea.classList.add("col-12", "draggable", "pb-2");
            newContainerTextarea.setAttribute("draggable", true);
            const activityId = content != '' ? content[i].id : "";

            let textArea = `
                    <div class="input-group input-group-lg border border-danger rounded position-relative">
                        <span class="input-group-text border-0" id="inputGroup-sizing-lg">${
                            countNodes + i + 1
                        }</span>
                        <div class="form-floating flex-grow-1">
                            <textarea class="form-control border-0 resize-textarea" id="floatingTextareaActivities${activityId}" rows="4" name="specific_activity[]" placeholder="Actividad" required>${
                                content != "" ? content[i].description : ""
                            }</textarea>
                            <input value="${activityId}" name="activityId[]" hidden>
                            <label class="bg-transparent" for="floatingTextareaActivities">Escriba la obligación</label>
                        </div>
                        <button type="button" class="btn btn-sm btn-danger btn-floating delete-textarea-btn position-absolute top-0 end-0 me-2 mt-2" value = "${activityId}" data-position-activity = "${content != "" ? content[i].number_activity : ""}" title="Eliminar obligación">
                            <i class="fa-solid fa-trash fa-xs"></i>
                        </button>
                    </div>`;
            newContainerTextarea.innerHTML = textArea;
            mainContainer.appendChild(newContainerTextarea);
        }

        setTimeout(() => {
            this.dragElement(document.getElementById("container-textareas"));
        }, 0);

        this.resizeTextarea();
    }

    showInputObligations() {
        const containerInput = document.getElementById(
            "container-number-obligations"
        );
        const btn = document.getElementById("btn-obligations");

        const showInput = async () => {
            try {
                this.fadeInUpElement(containerInput, 0, 0.2);
            } catch (error) {
                console.error("Error al importar el módulo", error);
            }
        };

        const hideInput = async () => {
            try {
                this.fadeOutLeftElement(containerInput, -115, 0.2);
            } catch (error) {
                console.error("Error al importar el módulo", error);
            }
        };

        const listenerBuildTextarea = () => {
            const inputNumber = document.getElementById("number-obligations");
            const btnSendNumber = document.getElementById(
                "btn-number-obligations"
            );
            const inputRole = document.getElementById("role_id");

            btnSendNumber.addEventListener("click", (e) => {
                e.preventDefault();
                const numberTextareas = parseInt(inputNumber.value);
                if (numberTextareas > 0 && inputRole.value != "") {
                    this.buildTextAreas(numberTextareas);
                    hideInput();
                } else {
                    alert("Por favor ingrese un número mayor a 0");
                }
            });
        };

        btn.addEventListener("click", showInput);

        listenerBuildTextarea();
    }

    calculateSalary(value) {
        const salaryInput = document.getElementById("total_salary");
        salaryInput.value = value > 0 ? value * 184 : 0;
    }

    async getRole() {
        const url = route('role.show');
        const data = {
            role: this.#role,
        };

        const response = await this.axiosPost(url, data);
        return response.data.role;
    }

    resizeTextarea() {
        const textareas = document.querySelectorAll(".resize-textarea");
        textareas.forEach((textarea) => {
            const adjustHeight = (element) => {
                element.style.height = "auto";
                element.style.height = `${element.scrollHeight}px`;
            };

            adjustHeight(textarea);
            textarea.addEventListener("input", () => adjustHeight(textarea));
        });
    }

    deleteTextarea() {
        const container = document.getElementById("container-textareas");
        
        // Eliminar el contenedor del textarea
        const removeTextarea = (button) => {
            const containerTextarea = button.closest("div[draggable]");
            if (containerTextarea) {
                containerTextarea.remove();
            }
        };
    
        // Eliminar actividad en BD
        const deleteActivity = async () => {
            try {
                const url = `/activity/delete/${this.#id}`;
                return await this.axiosDelete(url);
            } catch (error) {
                console.error("Error al eliminar la actividad:", error);
                return false;
            }
        };

        const submitFormactivities = () => {
            const form = document.getElementById('form-activities');
            if (!form) return;
            form.submit();
        }
    
        container.addEventListener("click", async (e) => {
            e.preventDefault();
    
            const button = e.target.closest("button");
            if (!button) return;
    
            if (button.value) {
                this.#id = button.value;
                const positionActivity = button.getAttribute("data-position-activity");
    
                if (confirm(`¿Seguro(a) que desea eliminar la actividad no. ${positionActivity}?`)) {
                    const result = await deleteActivity();
                    if (result) {
                        removeTextarea(button);
                        submitFormactivities();                    
                    } else {
                        alert("Hubo un problema al eliminar la actividad.");
                    }
                }
            }else{
                removeTextarea(button);
            }
        });
    }
}