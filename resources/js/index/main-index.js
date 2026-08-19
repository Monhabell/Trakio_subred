import Bola from './Bola.js';
import Utility from '../modules/Utility.js';

function viewPassword() {
    const inputPass = document.getElementById('inputPass');
    const viewPassIcon = document.getElementById('viewPassIcon');

    viewPassIcon.addEventListener('click', () => {
        viewPassIcon.classList.toggle('fa-eye-slash');
        viewPassIcon.classList.toggle('fa-eye');
        inputPass.type = inputPass.type === 'text'? 'password' : 'text';
    });
}

function fadeOutAlerts() {
    const alerts = document.querySelectorAll('.alert-success, .alert-danger, .alert-warning, .alert-capcha');
    alerts.forEach(alert => {
        setTimeout(() => {
            if (alert) {
                alert.classList.add('fade-out');
                alert.addEventListener('transitionend', () => {alert.remove()});
            }
        }, 3000);
    });
}

function handleAnimationBackground(){
    const canvas = document.getElementById('canvas-bg');
    const ctx = canvas.getContext('2d');

    // Dimensiones lógicas (CSS px). Todo el dibujo y la física de las
    // partículas usan estas unidades; el escalado por devicePixelRatio
    // se aplica aparte vía ctx.setTransform para que se vea nítido en
    // pantallas retina/alta densidad sin afectar la lógica de movimiento.
    let dims = { width: window.innerWidth, height: window.innerHeight };

    const bolas = [];
    const screenWidth = window.innerWidth;
    const numBolas = screenWidth <= 768 ? 20 : 100;
    const velocidad = screenWidth <= 768 ? 1 : 2;

    for (let i = 0; i < numBolas; i++) {
        const init = Math.random() * dims.width;
        bolas.push(new Bola(init, dims.height / 2, velocidad));
    }

    function resizeCanvas() {
        const dpr = window.devicePixelRatio || 1;
        const prevWidth = dims.width;
        const prevHeight = dims.height;

        dims = { width: window.innerWidth, height: window.innerHeight };

        canvas.style.width = dims.width + 'px';
        canvas.style.height = dims.height + 'px';
        canvas.width = Math.round(dims.width * dpr);
        canvas.height = Math.round(dims.height * dpr);
        ctx.setTransform(dpr, 0, 0, dpr, 0, 0);

        // Reubica las partículas proporcionalmente para que no queden
        // "atrapadas" fuera del área visible si la ventana cambió de tamaño
        if (prevWidth && prevHeight) {
            const scaleX = dims.width / prevWidth;
            const scaleY = dims.height / prevHeight;
            bolas.forEach(bola => {
                bola.x *= scaleX;
                bola.y *= scaleY;
            });
        }
    }

    resizeCanvas();
    window.addEventListener('resize', resizeCanvas);

    let mouse = { x: dims.width / 2, y: dims.height / 2 };

    canvas.addEventListener('mousemove', (event) => {
        mouse.x = event.clientX;
        mouse.y = event.clientY;
    });

    const animar = () => {
        ctx.clearRect(0, 0, dims.width, dims.height);

        // 1) Efecto "planetas": las partículas cercanas entre sí se atraen,
        // curvando su trayectoria y cambiando su velocidad.
        bolas.forEach(bola => {
            bolas.forEach(bola2 => {
                if (bola !== bola2) {
                    bola.gravitar(bola2);
                }
            });
        });

        // 2) Mover y dibujar cada partícula con la velocidad ya actualizada
        bolas.forEach(bola => {
            bola.mover(dims, mouse);
            bola.dibujar(ctx);
        });

        // 3) Líneas de conexión entre partículas cercanas
        bolas.forEach(bola=> {
            bolas.forEach(bola2=> {
                let dx = bola2.x - bola.x;
                let dy = bola2.y - bola.y;
                let dt = Math.sqrt(dx**2 + dy**2)

                if(dt <130){
                    ctx.beginPath();
                    ctx.moveTo(bola.x, bola.y);
                    ctx.lineTo(bola2.x, bola2.y);
                    ctx.lineWidth = 0.5;
                    ctx.strokeStyle = `rgba(255, 90, 95, ${1 - dt / 130})`;
                    ctx.stroke();
                    ctx.closePath();
                }
            });
        })
        requestAnimationFrame(animar);
    }

    animar();
}

function handleButtonLoader(){
    const utility = new Utility();
    utility.buttonsLoader("Validando datos...");
}

function initApp(){
    fadeOutAlerts();
    if(document.getElementById("viewPassIcon")){
        viewPassword();
    }
    
    handleAnimationBackground();
    handleButtonLoader();
}

initApp();