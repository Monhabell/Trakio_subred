export default class Utility {
    isEqualJSON(object1, object2) {
        return JSON.stringify(object1) === JSON.stringify(object2);
    }

    formatDateDMY(dateString) {
        const [year, month, day] = dateString.split('-');
        const date = new Date(year, month - 1, day);
        const options = { day: '2-digit', month: '2-digit', year: 'numeric' };
        return date.toLocaleDateString('es-ES', options);
    }    

    dateFormatN(date) {
        return moment(date).format('MMM DD, YYYY');
    }

    formatDateHourFromISO(dateString) {
        const date = new Date(dateString);
    
        const dateOptions = {
            day: 'numeric',
            month: 'long',
            year: 'numeric'
        };
    
        const timeOptions = {
            hour: '2-digit',
            minute: '2-digit',
            hour12: false
        };
    
        const formattedDate = date.toLocaleDateString('es-ES', dateOptions);
        const formattedTime = date.toLocaleTimeString('es-ES', timeOptions);
            
        return `${formattedDate.replace(',', '')}, ${formattedTime}`;
    }

    formatHumanDate(date) {
        const fecha = date.includes('T') ? new Date(date) : new Date(`${date}T00:00:00`);
        const diasSemana = ["domingo", "lunes", "martes", "miércoles", "jueves", "viernes", "sábado"];
        const meses = [
            "enero", "febrero", "marzo", "abril", "mayo", "junio",
            "julio", "agosto", "septiembre", "octubre", "noviembre", "diciembre"
        ];
    
        const diaSemana = diasSemana[fecha.getDay()];
        const dia = fecha.getDate();
        const mes = meses[fecha.getMonth()];
    
        return `${diaSemana} ${dia} de ${mes}, ${fecha.getFullYear()}`;
    }    
    
    fullField(field) {
        return (field !== undefined && field !== null && field !== '');
    }

    properNouns(names, last_name = '') {

        if(names === undefined || names === null || names === ''){
            return '';
        }
        
        const separate_names = names.split(" ");
        const separate_last_names = last_name.split(" ");
        let full_name = '';
    
        separate_names.forEach(name => {
            full_name += `${name.charAt(0).toUpperCase()}${name.slice(1).toLowerCase()} `;
        });
    
        separate_last_names.forEach(last_name => {
            full_name += `${last_name.charAt(0).toUpperCase()}${last_name.slice(1).toLowerCase()} `;
        });
    
        return full_name;
    }

    nameAndLastName(name, last_name) {
        const proper_name = this.properNouns(name);
        const proper_last_name = this.properNouns(last_name);

        const first_name = proper_name.split(' ');
        const first_last_name = proper_last_name.split(' ');
        return `${first_name[0]} ${first_last_name[0]}`;
    }

    currentElementWidth(element) {
        const currentWidth = element.getBoundingClientRect().width; //Ancho en pixeles
        const parentContainer = element.parentElement;
        const parentWidth = parentContainer.getBoundingClientRect().width; //Ancho en pixeles contenedor            
        const widthPercentage = (currentWidth / parentWidth) * 100;            

        return parseInt(widthPercentage);
    }

    canvasShowDocument(url, targetDiv) {
        const pdfContainer = document.getElementById(targetDiv);

        event.preventDefault();
    
        pdfjsLib.getDocument(url).promise.then(pdf => {
            // Mostrar la primera página del PDF
            pdf.getPage(1).then(page => {
                const viewport = page.getViewport({
                    scale: 1.0
                });
                const canvas = document.createElement('canvas');
                const context = canvas.getContext('2d');
                canvas.height = viewport.height;
                canvas.width = viewport.width;
    
                // Renderizar la página en el canvas
                const renderContext = {
                    canvasContext: context,
                    viewport: viewport
                };
                page.render(renderContext).promise.then(() => {
                    pdfContainer.innerHTML = '';
                    pdfContainer.appendChild(canvas);
                });
            });
        }).catch(error => {
            console.error('Error al cargar el PDF:', error);
            pdfContainer.innerHTML = 'No se pudo cargar el PDF.';
        });
    
        // Evitar que se abra la nueva pestaña
        return false;
    }

    interactiveMenu(itemsMenu){
        const openContainer = (selectContainerId) => {
            itemsMenu.containersIds.forEach(containerId => {
                try {
                    const container = document.getElementById(containerId);
                    container.hidden = !(containerId === selectContainerId);
                } catch (error) {
                    console.error(error);
                }
                
            });
        }
        
        const buttonsControl = () => {            
            const buttons = document.querySelectorAll(`.${itemsMenu.buttonsClass}`);
    
            buttons.forEach(button => {
                if(itemsMenu.sessionStorage){
                    const activeOption = sessionStorage.getItem(itemsMenu.localStorageName);
                    if(activeOption){
                        if (activeOption === button.dataset.toContainer) {
                            buttons.forEach(button => button.classList.remove('active'));
                            button.classList.add('active');
                            openContainer(activeOption);
                        }
                    }
                }
                
                button.addEventListener('click', (event) => {
                    const containerOpen = event.currentTarget.dataset.toContainer;
                    sessionStorage.setItem(itemsMenu.localStorageName, containerOpen);
    
                    openContainer(containerOpen)
                    buttons.forEach(button => button.classList.remove('active'));
                    button.classList.add('active');
                });
            });
        }
        
        buttonsControl();
    }

    simpleMenuNav(buttonsClass = null, classForActive = 'active'){
        if(!buttonsClass){
            console.error('La clase para los botones es requerida');
            return;
        }

        const links = Array.from(document.getElementsByClassName(`${buttonsClass}`));
        
        if(!links){
            console.error('No se encontraron los enlaces con la clase', buttonsClass);
            return;
        }

        const removeActive = () => {
            links.forEach(link => link.classList.remove(classForActive));
        }

        links.forEach(link => {
            link.addEventListener('click', (event) => {
                event.preventDefault();
                removeActive();
                event.target.closest('button').classList.add(classForActive);
            });
        });
    }

    buttonsLoader(message = '') {
        const btns = document.querySelectorAll('.btn-loader');
        btns.forEach(btn => {
            btn.addEventListener('click', () => {
                const form = btn.closest('form');

                if (form) {
                    if (form.checkValidity()) {
                        btn.innerHTML = `<i class="fa-solid fa-circle-notch fa-spin" style="color: #ffffff;"></i> ${message}`;
                        btn.classList.add('disabled');
                    }else{
                        form.reportValidity();
                    }
                }
            });
        });
    }

    containerLoader(container){
        container.classList.add('d-flex', 'justify-content-center', 'align-items-center');
        container.innerHTML = `<div class="loader">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 66 66" height="100px" width="100px" class="spinner">
                                <circle stroke="url(#gradient)" r="20" cy="33" cx="33" stroke-width="1" fill="transparent" class="path"></circle>
                                <linearGradient id="gradient">
                                    <stop stop-opacity="1" stop-color="#fe0000" offset="0%"></stop>
                                    <stop stop-opacity="0" stop-color="#af3dff" offset="100%"></stop>
                                </linearGradient>
                            </svg> 
                        </div>`;
    }

    numberToWords(number) {
        const unidades = (num) => {
            switch (num) {
                case 1:
                    return "UN";
                case 2:
                    return "DOS";
                case 3:
                    return "TRES";
                case 4:
                    return "CUATRO";
                case 5:
                    return "CINCO";
                case 6:
                    return "SEIS";
                case 7:
                    return "SIETE";
                case 8:
                    return "OCHO";
                case 9:
                    return "NUEVE";
            }
    
            return "";
        }
    
        const decenasY = (strSin, numUnidades) => {
            if (numUnidades > 0)
                return strSin + " Y " + unidades(numUnidades)
    
            return strSin;
        } //DecenasY()

        const decenas = (num) => {
            const decena = Math.floor(num / 10);
            const unidad = num - (decena * 10);
    
            switch (decena) {
                case 1:
                    switch (unidad) {
                        case 0:
                            return "DIEZ";
                        case 1:
                            return "ONCE";
                        case 2:
                            return "DOCE";
                        case 3:
                            return "TRECE";
                        case 4:
                            return "CATORCE";
                        case 5:
                            return "QUINCE";
                        default:
                            return "DIECI" + unidades(unidad);
                    }
                case 2:
                    switch (unidad) {
                        case 0:
                            return "VEINTE";
                        default:
                            return "VEINTI" + unidades(unidad);
                    }
                case 3:
                    return decenasY("TREINTA", unidad);
                case 4:
                    return decenasY("CUARENTA", unidad);
                case 5:
                    return decenasY("CINCUENTA", unidad);
                case 6:
                    return decenasY("SESENTA", unidad);
                case 7:
                    return decenasY("SETENTA", unidad);
                case 8:
                    return decenasY("OCHENTA", unidad);
                case 9:
                    return decenasY("NOVENTA", unidad);
                case 0:
                    return unidades(unidad);
            }
        } //Unidades()

        const centenas = (num) => {
            const centenas = Math.floor(num / 100);
            const decenasNum = num - (centenas * 100);
    
            switch (centenas) {
                case 1:
                    if (decenasNum > 0)
                        return "CIENTO " + decenas(decenasNum);
                    return "CIEN";
                case 2:
                    return "DOSCIENTOS " + decenas(decenasNum);
                case 3:
                    return "TRESCIENTOS " + decenas(decenasNum);
                case 4:
                    return "CUATROCIENTOS " + decenas(decenasNum);
                case 5:
                    return "QUINIENTOS " + decenas(decenasNum);
                case 6:
                    return "SEISCIENTOS " + decenas(decenasNum);
                case 7:
                    return "SETECIENTOS " + decenas(decenasNum);
                case 8:
                    return "OCHOCIENTOS " + decenas(decenasNum);
                case 9:
                    return "NOVECIENTOS " + decenas(decenasNum);
            }
    
            return decenas(decenasNum);
        } //Centenas()

        const seccion = (num, divisor, strSingular, strPlural) => {
            const cientos = Math.floor(num / divisor)
            const resto = num - (cientos * divisor)
    
            let letras = "";
    
            if (cientos > 0)
                if (cientos > 1)
                    letras = centenas(cientos) + " " + strPlural;
                else
                    letras = strSingular;
    
            if (resto > 0)
                letras += "";
    
            return letras;
        } //Seccion()

        const miles = (num) => {
            const divisor = 1000;
            const cientos = Math.floor(num / divisor)
            const resto = num - (cientos * divisor)
    
            const strMiles = seccion(num, divisor, "UN MIL", "MIL");
            const strCentenas = centenas(resto);
    
            if (strMiles == "")
                return strCentenas;
    
            return strMiles + " " + strCentenas;
    
            //return Seccion(num, divisor, "UN MIL", "MIL") + " " + Centenas(resto);
        } //Miles()
    
        const millones = (num) => {
            const divisor = 1000000;
            const cientos = Math.floor(num / divisor);
            const resto = num - (cientos * divisor);
    
            let strMillones = seccion(num, divisor, "UN MILLÓN", "MILLONES");
            const strMiles = miles(resto);
    
            if (strMillones == "")
                return strMiles;
    
            let millones = strMillones + " " + strMiles;
            return millones;
            //document.getElementById('inputNumLetras').value = millones;
            //return strMillones + " " + strMiles;
    
            //return Seccion(num, divisor, "UN MILLON", "MILLONES") + " " + Miles(resto);
        } //Millones()

        let data = {
            numero: number,
            enteros: Math.floor(number),
            centavos: (((Math.round(number * 100)) - (Math.floor(number) * 100))),
            letrasCentavos: "",
            letrasMonedaPlural: "PESOS",
            letrasMonedaSingular: "PESO"
        };

        if (data.centavos > 0)
            data.letrasCentavos = "CON " + data.centavos + "/100";

        if (data.enteros == 0) {
            return "CERO " + data.letrasMonedaPlural + " " + data.letrasCentavos;
        }
        if (data.enteros == 1) {
            return millones(data.enteros) + " " + data.letrasMonedaSingular + " " + data.letrasCentavos;
        } else
            return millones(data.enteros) + " " + data.letrasMonedaPlural + " " + data.letrasCentavos;
    }

    validateImage(url, callback) {
        const img = new Image();
        img.onload = () => callback(true);
        img.onerror = () => callback(false);
        img.src = url;
    }
}