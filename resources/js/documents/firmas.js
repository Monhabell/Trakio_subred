import { route } from 'ziggy-js';

const HANDLE_SIZE = 14;
const MIN_SIGNATURE_SIZE = 30;

let isDragging = false;
let activeHandle = null; // 'tl' | 'tr' | 'br' | 'bl' | null
let dragOffsetX = 0, dragOffsetY = 0;
let resizeAnchor = { x: 0, y: 0 }; // esquina opuesta a la que se está arrastrando
let img = null; // Variable para la imagen de la firma
let pdfDoc = null; // Documento PDF completo
let pdfPage = null; // Página actual del PDF
let currentPage = 1; // Página actual
let totalPages = 0; // Total de páginas en el PDF
let signature = { x: 0, y: 0, width: 120, height: 60 };

cargarPdfParaFirma();

const canvas = document.getElementById('pdf-canvas_firma');
const context = canvas.getContext('2d');
const hint = document.getElementById('signature-hint');
const sizeControl = document.getElementById('signature-size');
const sizeValue = document.getElementById('signature-size-value');
const centerButton = document.getElementById('center-signature');
const removeButton = document.getElementById('remove-signature');
const signatureInput = document.getElementById('signature-image');

function accentColor() {
    return getComputedStyle(document.documentElement).getPropertyValue('--shell-red')?.trim() || '#c0392b';
}

function setControlsEnabled(enabled) {
    sizeControl.disabled = !enabled;
    centerButton.disabled = !enabled;
    removeButton.disabled = !enabled;
    hint.classList.toggle('d-none', enabled);
}

// Actualizar el tamaño de la firma con el valor del control (mantiene proporción 2:1)
sizeControl.addEventListener('input', () => {
    const newSize = Number(sizeControl.value);
    signature.width = newSize;
    signature.height = newSize / 2;
    sizeValue.textContent = `${newSize} px`;
    drawSignature();
});

centerButton.addEventListener('click', () => {
    if (!img) return;
    signature.x = canvas.width / 2 - signature.width / 2;
    signature.y = canvas.height / 2 - signature.height / 2;
    drawSignature();
});

removeButton.addEventListener('click', () => {
    img = null;
    signatureInput.value = '';
    setControlsEnabled(false);
    drawSignature();
});

// Botones de navegación entre páginas
document.getElementById('prev-page_firma').addEventListener('click', async function () {
    if (currentPage > 1) {
        currentPage--;
        renderPage();
    }
});

document.getElementById('next-page_firma').addEventListener('click', async function () {
    if (currentPage < totalPages) {
        currentPage++;
        renderPage();
    }
});

async function cargarPdfParaFirma() {
    const pdfjs = window['pdfjs-dist/build/pdf'];
    pdfjs.GlobalWorkerOptions.workerSrc = "https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.6.347/pdf.worker.min.js";

    const loadingTask = pdfjs.getDocument(DOCUMENT_URL);
    pdfDoc = await loadingTask.promise;
    totalPages = pdfDoc.numPages;
    renderPage();
}

let offscreenCanvas = null; // Canvas auxiliar para el PDF
let offscreenContext = null;

// Renderizar una página específica del PDF
async function renderPage() {
    pdfDoc.getPage(currentPage).then(page => {
        pdfPage = page;
        const viewport = page.getViewport({ scale: 1.5 });

        if (!offscreenCanvas) {
            offscreenCanvas = document.createElement('canvas');
            offscreenContext = offscreenCanvas.getContext('2d');
        }

        offscreenCanvas.width = viewport.width;
        offscreenCanvas.height = viewport.height;

        const renderContext = {
            canvasContext: offscreenContext,
            viewport: viewport,
        };

        page.render(renderContext).promise.then(() => {
            canvas.width = offscreenCanvas.width;
            canvas.height = offscreenCanvas.height;
            context.drawImage(offscreenCanvas, 0, 0);
            if (img) drawSignature();
        });

        document.getElementById('page-info').innerText = `Página ${currentPage} de ${totalPages}`;
    });
}

// Cargar la firma
signatureInput.addEventListener('change', function (event) {
    const file = event.target.files[0];
    if (file && file.type === 'image/png') {
        img = new Image();
        img.onload = () => {
            signature.x = canvas.width / 2 - signature.width / 2;
            signature.y = canvas.height / 2 - signature.height / 2;
            sizeControl.value = signature.width;
            sizeValue.textContent = `${signature.width} px`;
            setControlsEnabled(true);
            drawSignature();
        };
        img.src = URL.createObjectURL(file);
    } else {
        alert("¡Por favor, cargue una imagen PNG!");
    }
});

// Dibujar la firma en el canvas
function drawSignature() {
    context.clearRect(0, 0, canvas.width, canvas.height);
    context.drawImage(offscreenCanvas, 0, 0);

    if (img) {
        context.drawImage(img, signature.x, signature.y, signature.width, signature.height);
        drawResizeHandles();
    }
}

function getHandles() {
    return {
        tl: { x: signature.x, y: signature.y },
        tr: { x: signature.x + signature.width, y: signature.y },
        br: { x: signature.x + signature.width, y: signature.y + signature.height },
        bl: { x: signature.x, y: signature.y + signature.height },
    };
}

// Dibujar el marco punteado y las esquinas de ajuste de la firma
function drawResizeHandles() {
    const handles = getHandles();
    const color = accentColor();

    context.save();
    context.strokeStyle = color;
    context.lineWidth = 1.5;
    context.setLineDash([6, 4]);
    context.strokeRect(signature.x, signature.y, signature.width, signature.height);
    context.setLineDash([]);

    Object.values(handles).forEach((point) => {
        context.beginPath();
        context.arc(point.x, point.y, HANDLE_SIZE / 2, 0, Math.PI * 2);
        context.fillStyle = '#fff';
        context.fill();
        context.lineWidth = 2;
        context.strokeStyle = color;
        context.stroke();
    });

    context.restore();
}

function handleAt(x, y) {
    const handles = getHandles();
    return Object.entries(handles).find(([, point]) => {
        const dist = Math.hypot(x - point.x, y - point.y);
        return dist <= HANDLE_SIZE;
    })?.[0] ?? null;
}

function cursorForHandle(handle) {
    if (handle === 'tl' || handle === 'br') return 'nwse-resize';
    if (handle === 'tr' || handle === 'bl') return 'nesw-resize';
    return 'default';
}

function pointerPosition(e) {
    const rect = canvas.getBoundingClientRect();
    const scaleX = canvas.width / rect.width;
    const scaleY = canvas.height / rect.height;
    return {
        x: (e.clientX - rect.left) * scaleX,
        y: (e.clientY - rect.top) * scaleY,
    };
}

function isInsideSignature(x, y) {
    return x >= signature.x && x <= signature.x + signature.width &&
        y >= signature.y && y <= signature.y + signature.height;
}

canvas.addEventListener('pointermove', (e) => {
    if (!img || isDragging || activeHandle) return;
    const { x, y } = pointerPosition(e);
    const handle = handleAt(x, y);

    if (handle) {
        canvas.style.cursor = cursorForHandle(handle);
    } else if (isInsideSignature(x, y)) {
        canvas.style.cursor = 'move';
    } else {
        canvas.style.cursor = 'default';
    }
});

canvas.addEventListener('pointerdown', (e) => {
    if (!img) return;
    const { x, y } = pointerPosition(e);
    const handle = handleAt(x, y);

    if (handle) {
        activeHandle = handle;
        const handles = getHandles();
        const opposite = { tl: 'br', tr: 'bl', br: 'tl', bl: 'tr' }[handle];
        resizeAnchor = handles[opposite];
        canvas.setPointerCapture(e.pointerId);
        return;
    }

    if (isInsideSignature(x, y)) {
        isDragging = true;
        dragOffsetX = x - signature.x;
        dragOffsetY = y - signature.y;
        canvas.style.cursor = 'move';
        canvas.setPointerCapture(e.pointerId);
    }
});

canvas.addEventListener('pointermove', (e) => {
    if (!isDragging && !activeHandle) return;
    const { x, y } = pointerPosition(e);

    if (isDragging) {
        signature.x = x - dragOffsetX;
        signature.y = y - dragOffsetY;
        drawSignature();
        return;
    }

    if (activeHandle) {
        const anchor = resizeAnchor;
        let newX = Math.min(anchor.x, x);
        let newY = Math.min(anchor.y, y);
        let newWidth = Math.abs(x - anchor.x);
        let newHeight = Math.abs(y - anchor.y);

        newWidth = Math.max(MIN_SIGNATURE_SIZE, newWidth);
        newHeight = Math.max(MIN_SIGNATURE_SIZE, newHeight);

        signature.width = newWidth;
        signature.height = newHeight;
        signature.x = newX;
        signature.y = newY;

        sizeControl.value = Math.round(newWidth);
        sizeValue.textContent = `${Math.round(newWidth)} px`;
        drawSignature();
    }
});

function stopInteraction(e) {
    isDragging = false;
    activeHandle = null;
    if (e?.pointerId !== undefined && canvas.hasPointerCapture?.(e.pointerId)) {
        canvas.releasePointerCapture(e.pointerId);
    }
    canvas.style.cursor = 'default';
}

canvas.addEventListener('pointerup', stopInteraction);
canvas.addEventListener('pointercancel', stopInteraction);
canvas.addEventListener('pointerleave', () => {
    if (!isDragging && !activeHandle) canvas.style.cursor = 'default';
});

// Estado inicial: sin firma cargada, controles deshabilitados y aviso visible
setControlsEnabled(false);

// Guardar PDF firmado
document.getElementById('save-signed-pdf').addEventListener('click', async function () {
    if (!img || !img.src) {
        alert("¡Por favor, cargue una firma válida!");
        return;
    }

    const pdfBytes = await fetch(DOCUMENT_URL).then(res => res.arrayBuffer());
    const pdfDoc = await PDFLib.PDFDocument.load(pdfBytes);
    const imgBytes = await fetch(img.src).then(res => res.arrayBuffer());
    const signatureImage = await pdfDoc.embedPng(imgBytes);
    const page = pdfDoc.getPage(currentPage - 1);
    const scale = 1.5;
    const signatureX = signature.x / scale;
    const signatureY = (canvas.height - signature.y - signature.height) / scale;
    const signatureWidth = signature.width / scale;
    const signatureHeight = signature.height / scale;

    page.drawImage(signatureImage, {
        x: signatureX,
        y: signatureY,
        width: signatureWidth,
        height: signatureHeight,
    });

    const pdfBytesWithSignature = await pdfDoc.save();

    const formData = new FormData();
    formData.append('file', new Blob([pdfBytesWithSignature], { type: 'application/pdf' }), 'documento_firmado.pdf');
    formData.append('originalPath', DOCUMENT_URL);
    formData.append('id_document', DOCUMENT_ID);

    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const url = route('document.update.sign');
    try {
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
            },
            body: formData,
        });

        if (response.ok) {
            alert('El documento ha sido reemplazado con éxito.');
            window.location.href = route('documents.index');
        } else {
            alert('Error al reemplazar el documento.');
        }
    } catch (error) {
        console.error('Error al enviar el archivo:', error);
        alert('Ocurrió un error al intentar enviar el archivo.');
    }
});
