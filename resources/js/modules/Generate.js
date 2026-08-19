
import Animation from './Animation.js';

export class Generate extends Animation{
    #currentStep = 1;
    #selectedOption;
    #environment;
    #period;
    #signatures;
    #technician;

    constructor(stepsContainers, environment, period, signatures, technician, element) {
        super(element);
        this.stepsContainers = stepsContainers;
        this.#environment = environment;
        this.#period = period;
        this.#signatures = signatures;
        this.#technician = technician;
        this.stepItems = document.querySelectorAll('.step-number');
    }

    get currentStep() {
        return this.#currentStep;
    }

    set currentStep(newStep) {
        if (newStep >= 1 && newStep <= 5) {
            this.#currentStep = newStep;
        } else {
            throw new Error('Invalid step number. Step number must be between 1 and 5.');
        }
    }

    get selectedOption() {
        return this.#selectedOption;
    }

    set selectedOption(newOption) {
        const aceptedOptions = ['environment', 'technician', 'period', 'signature'];
        if (aceptedOptions.includes(newOption)) {
            this.#selectedOption = newOption;
        } else {
            throw new Error('Invalid option. Option must be either "environment", "technician", "period" or "signature".');
        }
    }

    get period(){
        return this.#period;
    }

    set period(newPeriod){
        this.#period = newPeriod;
    }

    get environment(){
        return this.#environment;
    }

    set environment(newEnvironment){
        this.#environment = newEnvironment;
    }

    get signatures(){
        return this.#signatures;
    }

    set signatures(newSignatures){
        this.#signatures = newSignatures;
    }

    get technician(){
        return this.#technician;
    }

    set technician(newTechnician){
        this.#technician = newTechnician;
    }

    /**
     * Queries
    **/

    async getUsersEnvironment(){
        const moduleQueries = await import('./Queries.js');
        const queriesInstance = new moduleQueries.default();
        const url = '/adm/acts/users';

        const data = {
            environment: this.#environment.name
        }

        const response = await queriesInstance.axiosPost(url, data);
        return response.data;
    }

    async getFormatsEnvironment(){
        const querie = await import('./Queries.js');
        const querieInstance = new querie.default();
        const url = '/adm/acts/formats';

        const data = {
            environmentId: this.#environment.id,
            dates: this.#period
        }

        const response = await querieInstance.axiosPost(url, data);
        return response.data;
    }

    /**
     * Animations
    **/

    moveContainer() {
        const currentStepContainer = document.querySelector(`.step-${this.#currentStep}`);
        const nextStepContainer = document.querySelector(`.step-${this.#currentStep + 1}`);
        const isLastStep = this.#currentStep === 4;
    
        if (!currentStepContainer) {
            throw new Error('Invalid step container.');
        }
    
        this.fadeUpElement(currentStepContainer, -90);
    
        if (nextStepContainer) {
            if (isLastStep) {
                const containerRow = document.getElementById('dataReception');
                containerRow.classList.add('shadow');
            }
            
            const autoHeightValue = isLastStep ? currentStepContainer.scrollHeight : 10;
            setTimeout(() => {
                this.autoHeightElement(currentStepContainer.parentElement, autoHeightValue);    
            }, 800);
            
            this.fadeInElement(nextStepContainer);
        }
    
        setTimeout(() => {
            currentStepContainer.classList.add('d-none');
            currentStepContainer.classList.remove('d-flex');
        }, 800);
    }

    setActiveStepItem(){
        this.stepItems.forEach(item => {
            if (this.#currentStep + 1 == item.textContent) {
                setTimeout(() => {
                    item.parentElement.classList.add('active');    
                }, 600);
            }
        });
    }
    
    getBgColorFormat(quantity){
        let colorFormat = quantity > 0 ? 'border-danger' : 'border-dark';

        return colorFormat;
    }

    validateValuesSelected() {
        const spanSelectedValue = document.getElementById(`value-${this.#selectedOption}`);
        if (!spanSelectedValue) {
            throw new Error('Invalid selected value span.');
        }
    
        let isValidData = false;
        let displayText = '';
    
        switch (this.#selectedOption) {
            case 'environment':
                isValidData = this.fullField(this.#environment.name);
                if (isValidData) {
                    displayText = this.#environment.name;
                }
                break;
    
            case 'technician':
                isValidData = this.fullField(this.#technician);
                if (isValidData) {
                    displayText = this.#technician;
                }
                break;
    
            case 'period':
                isValidData = this.fullField(this.#period.initDate) && this.fullField(this.#period.endDate);
                if (isValidData) {
                    displayText = `${this.formatDateDMY(this.#period.initDate)} - ${this.formatDateDMY(this.#period.endDate)}`;
                }
                break;
    
            case 'signature':
                isValidData = this.#signatures.length > 0;
                if (isValidData) {
                    setTimeout(() => {
                        spanSelectedValue.innerHTML = this.#signatures.map(signature => `${signature}<br>`).join('');
                    }, 800);
                }
                break;
    
            default:
                break;
        }
    
        if (isValidData) {
            setTimeout(() => {
                spanSelectedValue.style.transition = 'all ease-in 0.6s';
                spanSelectedValue.style.opacity = '1';
            }, 150);
            spanSelectedValue.textContent = displayText;
        }
    
        return isValidData;
    }

    /**
     * HTML inner
    **/

    async innerEnvironmentUsers(){
        const users = await this.getUsersEnvironment();
        const container = document.querySelector('.step-4');

        users.environmentUsers.forEach(user => {
            const fullName = this.properNouns(this.nameAndLastName(user.name, user.last_name));
            const divContainer = document.createElement('div');
            divContainer.classList.add('form-group', 'mb-0', 'flex-center');

            const html = `<input type="checkbox" class="btn-check" name="signatures[]" id="user-act-${user.id}" autocomplete="off"
                         value="${user.id}" data-value-signatures="${fullName}">
                    <label class="btn btn-outline-light w-75" for="user-act-${user.id}">${fullName}</label><br>`;

            divContainer.innerHTML = html;
            container.insertBefore(divContainer, container.firstChild);
        });        
    }

    

    setListenerQuantity(){
        const containerInputs = document.getElementById('dataFormats');
        const inputs = document.getElementsByName('formatsQuantity[]');
        const totalInput = document.getElementById('totalQuantity');

        const getTotalFormats = () => {
            let total = 0;
            inputs.forEach(input => {
                if (!isNaN(parseInt(input.value))) {
                    total += parseInt(input.value);
                }
            });
            return total;
        }

        containerInputs.addEventListener('keyup', (event) => {
            const input = event.target.closest('input');
            const span = input.previousElementSibling;

            span.classList.remove('border-danger', 'border-dark');
            span.classList.add(this.getBgColorFormat(input.value));

            totalInput.value = getTotalFormats();
        });

        totalInput.value = getTotalFormats();
    }

    async innerFormatsEnvironment() {
        const data = await this.getFormatsEnvironment();
        const container = document.getElementById('dataFormats');
    
        const createFormatDiv = (format, isRemain = false) => {
            const newDiv = document.createElement('div');
            newDiv.classList.add('input-group');
            const nombreBase = isRemain ? format.name : format.bases.name;
            const numberFormat = isRemain ? 0 : format.total;
            const bgColor = isRemain ? 'dark' : 'danger';
    
            newDiv.innerHTML = `
                <span class="input-group-text bg-dark border-${bgColor} bg-gradient text-white w-75">${nombreBase}</span>
                <input type="number" name="formatsQuantity[]" aria-label="Quantity" class="form-control border border-1 border-primary" value="${numberFormat}">
                <input type="text" name="formatsNames[]" class="form-control" value="${nombreBase}" hidden>
            `;
    
            return newDiv;
        };

        const createTotalContainer = () => {
            const newDiv = document.createElement('div');
            newDiv.classList.add('input-group');
    
            newDiv.innerHTML = `
                <span class="input-group-text bg-dark border-warning bg-gradient text-white w-75">TOTAL</span>
                <input type="number" aria-label="TotalQuantity" id = "totalQuantity" class="form-control border border-1 border-primary" readonly>
            `;
    
            return newDiv;
        };
    
        if (data.formats.length > 0) {
            data.formats.forEach(format => {
                container.append(createFormatDiv(format));
            });
        }
    
        if (data.formatsRemain.length > 0) {
            data.formatsRemain.forEach(format => {
                container.append(createFormatDiv(format, true));
            });
        }

        container.append(createTotalContainer());
        this.setListenerQuantity();
    }

    evaluateStep() {
        const progressBar = document.querySelector('.steps-progress');
        const animation = new Animation(progressBar);
        const stepSize = 23;
        const steps = {
            environment: 1,
            technician: 2,
            period: 3,
            signature: 4
        };
        
        const targetStep = steps[this.#selectedOption];
        
        if (this.#currentStep === targetStep) {
            if (!this.validateValuesSelected()) {
                alert('Debe seleccionar todos los datos requeridos.');
                return;
            }
    
            switch (this.#currentStep) {
                case 1:
                    this.innerEnvironmentUsers();
                    break;
                case 3:
                    this.innerFormatsEnvironment();
                    break;
            }
    
            animation.moveProgressBar(stepSize);
            this.setActiveStepItem();
            this.moveContainer();
            this.#currentStep++;
        } else {
            animation.moveProgressBarTo((targetStep - 1) * stepSize);
            this.#currentStep = targetStep;
        }
    }    
}