import Log from 'laravel-mix/src/Log.js';
import Query from '../modules/Queries.js';
import { route } from 'ziggy-js';

if (existElement('disclaimer_modal')) {
    setTimeout(function() {
        $('#disclaimer_modal').modal('show');
    }, 400);
}

document.addEventListener('DOMContentLoaded', (event) => {
    const folders = document.querySelectorAll('.folder');

    folders.forEach(folder => {
        folder.addEventListener('click', function(event) {
            event.stopPropagation(); // Evita que el evento se propague a los elementos padre
            const sublist = this.querySelector('ul');
            const icon = this.querySelector('.icon i');
            if (sublist) {
                sublist.classList.toggle('hidden');
                if (sublist.classList.contains('hidden')) {
                    icon.classList.remove('fa-folder-open');
                    icon.classList.add('fa-folder');
                } else {
                    icon.classList.remove('fa-folder');
                    icon.classList.add('fa-folder-open');
                }
            }
        });
    });
});

function existElement(idElement) {
    const byDocumentId = document.getElementById(idElement);
    return byDocumentId;
}

function sumaPlanilla() {
    const form = document.getElementById('insurance-form');
    const spanTotal = document.getElementById('totalPlanillaSpan');
    const inputTotal = document.getElementById('totalSgss');

    const add = ()=> {
        const total = (parseInt(form.epsValue.value || 0) + parseInt(form.afpValue.value || 0) + parseInt(form.arlValue.value || 0) + parseInt(form.compensationValue.value || 0));
        spanTotal.textContent = inputTotal.value = parseInt(total).toLocaleString('es-ES');;
    }

    form.querySelectorAll('input[type="number"]').forEach(input => {
        input.addEventListener('keyup', add);
    });
}

function handleViewDocuments(){
    document.addEventListener('click', function(event) {
        if (event.target.classList.contains('link-document')) {
            const url = event.target.dataset.urlDocument; 
            previewDocumento(url);   
        }
    });
}

function previewDocumento(url) {
    const embed = document.getElementById('pdf-embed');   
    embed.src = url; 
}

function showButtonUpload(buttonId, containerId) {
    if (!existElement(buttonId) && !existElement(containerId)) {
       
        return;
    }

    const buttonUploadContainer = document.getElementById(containerId);
    const sgssDocument = document.getElementById(buttonId);

    sgssDocument.addEventListener('change', async () => {
        buttonUploadContainer.innerHTML = '';
        const getOriginalName = () => {
            let fileName = sgssDocument.value.split('\\').pop();
            if (fileName.length > 20) {
                fileName = `${fileName.slice(0, 20)}...`;
            }
            return fileName;
        }
        
        if (sgssDocument.value) {  
            const btn_carga = document.getElementById('btn_carga');
            btn_carga.hidden = true;          
            const newButton = document.createElement('button');
            newButton.type = 'submit';
            newButton.classList.add('btn', 'btn-success', 'h-100', 'btn-lg', 'fw-bold', 'btn-loader');
            newButton.style.maxWidth = "100%";
            newButton.innerHTML = `<small><b>✅Enviar PLanilla</b> ${getOriginalName()}</small>`;
            buttonUploadContainer.appendChild(newButton);

            const utilityModule = await import('../modules/Utility.js');
            const utility = new utilityModule.default();
            utility.buttonsLoader("Cargando archivo...");
        }
    });
}
 
function handleViewDocument () {
    const tree = document.getElementById('document-tree');

    function loadFolderContent(path, parentElement) {
        const spinner = document.createElement('div');
        spinner.className = 'document-spinner';
        parentElement.appendChild(spinner);

        fetch(`/documents/folder-content?path=${encodeURIComponent(path)}`)
            .then(response => response.json())
            .then(data => {
                parentElement.innerHTML = '';
                data.forEach(item => {
                    if (item.tipo === 'directorio') {
                        parentElement.innerHTML += `
                            <li   class="folder" data-path="${item.ruta}">
                                <span class="icon"><i class="fa fa-folder"></i></span> ${item.nombre}
                                 <div class="document-spinner-2 hidden"></div> <!-- Spinner dentro de cada carpeta -->
                                <ul class="hidden"></ul>
                            </li>`;
                    } else {
                        parentElement.innerHTML += `
                            <li class="file">
                                <span class="icon"><i class="fa fa-file-pdf"></i></span>
                                <a class = "link-document" data-url-document = "/storage/documents/${item.ruta}"> ${item.nombre}</a>
                            </li>`;
                    }
                });
                parentElement.removeChild(spinner);
            })
            .catch(error => {
                console.error('Error al cargar contenido:', error);
                spinner.classList.add('hidden'); // Quitar spinner incluso si hay error
            });
    }

    loadFolderContent('/', tree);

    tree.addEventListener('click', function (event) {
        const folder = event.target.closest('.folder');
        if (folder) {
            const ul = folder.querySelector('ul');
            if (ul.classList.contains('hidden')) {
                const path = folder.getAttribute('data-path');
                ul.classList.remove('hidden');
                loadFolderContent(path, ul); // Mostrar spinner

            } else {
                ul.classList.add('hidden');
            }
        }
    });
}

function eliminarDocumentos(ruta) {
    const url = route('document.delete');
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    if (confirm('¿Estás seguro de que quieres eliminar este documento?')) {
        fetch(url, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    ruta: ruta
                })
            })
            .then(response => {
                if (response.ok) {
                    alert('Documento eliminado exitosamente.');
                    location.reload();
                } else {
                    alert('Error al eliminar el documento.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Se produjo un error al intentar eliminar el documento.');
            });
    }
}

document.addEventListener('DOMContentLoaded', function () {
    const pdfViewer = document.getElementById('pdf-viewer');
    const totalPagesEl = document.getElementById('total-pages');
    const deleteBtn = document.getElementById('delete-viewer');
    const signBtn = document.getElementById('firmar_pdf');

    let pdfDoc = null;
    let currentPage = 1;

    // Función para cargar un PDF
    function loadPDF(url) {
        pdfjsLib.getDocument(url).promise.then(function (doc) {
            pdfDoc = doc;
            totalPagesEl.textContent = pdfDoc.numPages;
            renderPage(1); // Renderizar la primera página
            pdfViewer.classList.remove('hidden');
        });
    
        deleteBtn.onclick = function() {
            eliminarDocumentos(url);
        };

        signBtn.onclick = function () {
            // Redirigir usando un formulario oculto para realizar el POST
            handlesign(url)
        };
    }

    async function handlesign(document_url) {
        const query = new Query();
        const url = route('document.idFromPath');
        const data = { document_url: document_url }
        const response = await query.axiosPost(url, data);
        if (response.data.success) {
            window.location.href = route('document.sign', {'document': response.data.document_id})    
        }else{
            alert("Error al firmar el documento");
        }
    }

    // Ejemplo: Abrir un PDF al hacer clic (puedes cambiar esta lógica)
    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('link-document')) {
            e.preventDefault();
            const pdfUrl = e.target.dataset.urlDocument;
            loadPDF(pdfUrl);
        }
    });
});

// // Mostrar el tooltip cuando la página cargue
// document.addEventListener('DOMContentLoaded', function () {
//     const button = document.getElementById('firmar_pdf'); // Cambiar al id del botón que deseas resaltar
//     const tooltip = document.getElementById('new-feature-tooltip');
//     const closeButton = document.getElementById('tooltip-close-btn');

//     tooltip.style.display = 'block';
//     const rect = button.getBoundingClientRect();
   
//     closeButton.addEventListener('click', function () {
//         tooltip.style.display = 'none';
//     });
// });

//Informe de actividades
async function handleReportActivities() {
    const total_fee = document.getElementById('total_fee');

    const utilityModule = await import('../modules/Utility.js');
    const utilityInstance = new utilityModule.default();
    utilityInstance.buttonsLoader();
    const total_fee_string = document.getElementById('total_fee_string');
    
    const numberToWord = () => {
        total_fee_string.value = utilityInstance.numberToWords(parseInt(total_fee.value) );
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

//Informe de actividades
async function handleManagerReportActivities() {
    const moduleActivity = await import("../modules/ReportActivity.js");
    const reportActivity = new moduleActivity.default();
    reportActivity.handleActivitiesReportManager();
}

function handleDeleteTemplate(){
    const form = document.getElementById('form-template-delete');
    form.addEventListener('submit', (e) => {
        e.preventDefault();
        const confirmation = confirm('¿Está seguro de que desea eliminar esta plantilla?');
        if (confirmation) {
            form.submit();
        }
    });
}

async function initApp(){
    if (existElement('container-documents')) {
        showButtonUpload('sgssDocument', 'btn-up-ins-cont');
        showButtonUpload('prorogrationFile', 'btn-up-prog-cont');
        showButtonUpload('secopFile', 'btn-up-secop-cont');
        handleViewDocuments();
        sumaPlanilla();

        const itemsMenu = {
            containersIds: ['collapsePlanilla', 'collapseProrroga', 'collapseSecop', 'collapseInforme', 'collapseFiles', 'collapseCertifications'],
            buttonsClass: "nav-link-files",
            localStorageName: "containerFilesOpen"
        }
        const moduleUtility = await import('../modules/Utility.js');
        const utility = new moduleUtility.default();
        utility.interactiveMenu(itemsMenu);
        handleViewDocument();
        menuDocument ()
        
    }

    if (existElement("collapseEditActivities")) {
        handleManagerReportActivities();
    }

    if (existElement('total_fee')) {
        handleReportActivities();   
    }

    if (existElement('form-template-delete')) {
        handleDeleteTemplate();
    }
}

document.addEventListener("DOMContentLoaded", () => {
    initApp();
});

function menuDocument (){
    let btn_menu = document.getElementById("btn_menu_document")
    btn_menu.addEventListener('click', () => {
        const container = document.querySelector(".menu-document")
        container.classList.toggle("active")

    } )
}