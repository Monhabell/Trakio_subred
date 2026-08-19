<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prueba de Mecanografía Trakio</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="icon" href="{{ asset('img/logo_temp.png') }}">

</head>

<style>
    * {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
    }

    body {
        font-family: 'Arial', sans-serif;
        background: linear-gradient(45deg, #ff4b1f, #1fddff);
        background-size: 400% 400%;
        animation: gradientBackground 15s ease infinite;
        height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
        overflow: hidden; /* Oculta las barras de desplazamiento */
        position: relative;
    }

    @keyframes gradientBackground {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }

    .container {
        background-color: rgba(255, 255, 255, 0.9);
        padding: 30px;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        text-align: center;
        max-width: 800px;
        width: 100%;
        z-index: 10; /* Asegura que esté por encima del logo */
    }

    h1 {
        font-size: 36px;
        color: #333;
        margin-bottom: 20px;
    }

    #participant-info p {
        font-size: 22px;
        color: #555;
        margin-bottom: 20px;
    }

    #text-container {
        font-size: 24px;
        margin-bottom: 25px;
        line-height: 1.6;
        padding: 15px;
        background-color: #f0f0f0;
        border-radius: 10px;
        min-height: 60px;
    }

    #text-to-type span {
        font-size: 32px;
        font-weight: bold;
        white-space: pre-wrap;
    }

    .correct {
        color: #28A745;
    }

    .incorrect {
        color: #dc3545;
    }

    .current-char {
        background-color: #ffc107;
    }

    #typing-input {
        width: 100%;
        padding: 15px;
        font-size: 18px;
        margin-bottom: 30px;
        border-radius: 10px;
        border: 2px solid #ddd;
        transition: border-color 0.3s;
    }

    #typing-input:focus {
        border-color: #007BFF;
    }

    button {
        padding: 15px 25px;
        font-size: 18px;
        cursor: pointer;
        border: none;
        border-radius: 10px;
        transition: background-color 0.3s, transform 0.3s;
    }

    #start-button {
        background-color: #007BFF;
        color: white;
        margin-right: 10px;
    }

    #start-button:hover {
        background-color: #0056b3;
        transform: scale(1.05);
    }

    #next-player-button {
        background-color: #28A745;
        color: white;
    }

    #next-player-button:hover {
        background-color: #218838;
        transform: scale(1.05);
    }

    #results {
        margin-top: 30px;
    }

    #results p {
        font-size: 24px;
        color: #333;
    }

    #results span {
        font-weight: bold;
    }

    #overall-results {
        margin-top: 40px;
        display: none;
    }

    #overall-results h2 {
        font-size: 28px;
        color: #333;
        margin-bottom: 20px;
    }

    #overall-results p {
        font-size: 24px;
        color: #007BFF;
    }


    #floating-logo {
        position: absolute;
        width: 150px;
        height: 150px;
        top: 0;
        left: 0;
        opacity: 0.2;
        z-index: 1; /* Debajo del contenedor */
        pointer-events: none; /* Evita que interfiera con la interacción del usuario */
    }

</style>
<body>
    <div class="container">
        <h1>Competencia de Mecanografía</h1>
        <div id="participant-info">
            <p>Turno del participante: <span id="current-participant">1</span></p>
        </div>
        <div id="text-container">
            <span id="text-to-type">Texto de ejemplo para la prueba de mecanografía.</span>
        </div>
        <input type="text" id="typing-input" autocomplete="off" placeholder="Escribe aquí..." disabled>
        <button id="start-button">Iniciar prueba</button>
        <button id="next-player-button" style="display:none;">Siguiente participante</button>
        <div id="results">
            <p>Tiempo: <span id="time">0</span> segundos</p>
            <p>Precisión: <span id="accuracy">0</span>%</p>
        </div>
        <div id="overall-results">
            <h2>Resultado final:</h2>
            <p>Ganador: <span id="winner"></span></p>
        </div>
    </div>

    <!-- Logo que rebota -->
    <img src="{{ asset('img/LogoTrakioRounded.png') }}" id="floating-logo" alt="Logo">
    

    <script src="script.js"></script>

    <script>
        let startTime, interval, timer;
const maxTime = 60; // Duración máxima de la prueba (en segundos)
const textsArray = [
    "A lo largo de su vida, el ser humano experimenta innumerables emociones, desde la alegría hasta la tristeza, pasando por la nostalgia, la ira y el amor. Cada una de ellas nos enseña algo valioso sobre nosotros mismos y sobre el mundo que nos rodea.",
    "La curiosidad del ser humano ha sido el motor que ha impulsado los grandes avances de la historia. Gracias a nuestra capacidad de cuestionar, hemos sido capaces de descubrir, inventar y crear todo aquello que hoy en día nos facilita la vida.",
    "La ciencia nos ha permitido comprender muchos de los fenómenos que ocurren a nuestro alrededor. Sin embargo, aún quedan muchas preguntas por responder, misterios que desafían nuestra comprensión y que quizás nunca lleguemos a resolver del todo.",
    "A medida que avanzamos en la vida, nos damos cuenta de que no se trata solo de alcanzar metas, sino de disfrutar del proceso. Cada paso, cada obstáculo, cada victoria, es una oportunidad para aprender y crecer, tanto a nivel personal como profesional.",
    "La tecnología ha transformado radicalmente la forma en que vivimos, trabajamos y nos comunicamos. Nos ha conectado de maneras inimaginables hace unas décadas, pero también ha traído consigo nuevos retos y responsabilidades que debemos enfrentar con sabiduría.",
    "El medio ambiente es un recurso invaluable que debemos proteger. Cada pequeña acción cuenta, desde reciclar hasta reducir el consumo de energía. Si todos hacemos nuestra parte, podemos garantizar un futuro más sostenible para las generaciones que vienen.",
    "La lectura es una puerta a mundos desconocidos, a nuevas ideas y a diferentes perspectivas. A través de los libros, podemos viajar en el tiempo, conocer culturas lejanas y sumergirnos en la mente de otros seres humanos, lo cual enriquece nuestra visión del mundo.",
    "El esfuerzo y la dedicación son las claves para lograr cualquier objetivo que nos propongamos. A lo largo del camino, es probable que encontremos dificultades, pero si mantenemos la perseverancia y la confianza en nosotros mismos, podremos superar cualquier obstáculo.",
    "El arte de la comunicación efectiva es una habilidad que, si bien se aprende con la práctica, es esencial en todos los ámbitos de la vida. Saber expresar nuestras ideas de manera clara y asertiva nos permite establecer relaciones más sanas y alcanzar nuestros objetivos.",
    "La creatividad es uno de los mayores dones del ser humano. A través de la imaginación, somos capaces de resolver problemas, innovar en diferentes áreas y aportar soluciones que no existían antes. La clave está en alimentar esa creatividad cada día.",
    "A lo largo de los siglos, las civilizaciones han dejado su huella en la historia a través de monumentos, obras de arte, avances científicos y literarios. Estas contribuciones son un testimonio de la grandeza del ser humano y su capacidad de trascender en el tiempo.",
    "La educación es el pilar fundamental para el desarrollo de una sociedad. A través del conocimiento, no solo se fomenta el crecimiento personal, sino que también se promueve una ciudadanía más crítica y comprometida con los desafíos del mundo contemporáneo.",
    "El deporte no solo es una actividad física, sino también una disciplina que fomenta valores como la constancia, la superación personal y el trabajo en equipo. A través del deporte, aprendemos a lidiar con la derrota, a celebrar la victoria y a seguir mejorando cada día.",
    "La historia de la humanidad está marcada por grandes avances y descubrimientos, pero también por momentos de oscuridad. Es fundamental conocer y aprender del pasado para no repetir los mismos errores y construir un futuro más justo y equitativo para todos.",
    "La tecnología de la información ha revolucionado la forma en que almacenamos y procesamos los datos. Hoy en día, grandes volúmenes de información se generan cada segundo, y el reto es saber cómo gestionar y utilizar esos datos de manera efectiva y ética.",
    "El clima es un factor determinante en la vida en la Tierra. Las variaciones climáticas afectan no solo a los ecosistemas, sino también a la agricultura, la economía y la salud humana. Por eso es crucial entender su funcionamiento y actuar para mitigar su impacto.",
    "La naturaleza nos ofrece paisajes asombrosos y una biodiversidad extraordinaria. Desde las vastas selvas tropicales hasta los desiertos más áridos, cada rincón del planeta tiene algo único que ofrecer. Es nuestra responsabilidad cuidar y preservar estos tesoros naturales.",
    "La música es un lenguaje universal que trasciende fronteras y culturas. A través de sus melodías y ritmos, somos capaces de experimentar una amplia gama de emociones, desde la euforia hasta la melancolía, lo que la convierte en una parte esencial de la vida humana.",
    "El universo es vasto y misterioso, lleno de galaxias, estrellas y planetas que están más allá de nuestra comprensión. A pesar de nuestros avances científicos, apenas hemos comenzado a rascar la superficie de lo que realmente hay allá afuera en el espacio profundo.",
    "El trabajo en equipo es esencial para alcanzar grandes logros. A través de la colaboración, podemos combinar nuestras fortalezas individuales y superar juntos los desafíos que nos encontramos en el camino. La unión de talentos siempre genera mejores resultados.",
    "La salud mental es tan importante como la física, y sin embargo, muchas veces se pasa por alto. Cuidar de nuestro bienestar emocional nos permite tener una vida más plena y enfrentar las adversidades con mayor resiliencia. La clave está en buscar ayuda cuando la necesitamos.",
    "El cine es una forma de arte que combina imagen, sonido y narrativa para contar historias que nos conmueven y nos hacen reflexionar. A lo largo de su historia, ha sido una ventana al alma humana, permitiéndonos ver el mundo a través de los ojos de diferentes personajes.",
    "El agua es un recurso vital para la vida en la Tierra, y sin embargo, en muchas partes del mundo es escasa. Proteger este recurso es fundamental para garantizar que las futuras generaciones tengan acceso a agua potable y que los ecosistemas puedan seguir prosperando.",
    "La arquitectura es el arte de diseñar y construir espacios que no solo sean funcionales, sino también estéticamente agradables. A través de los siglos, hemos visto cómo las ciudades y sus edificaciones reflejan la cultura y la identidad de las civilizaciones.",
    "La astronomía nos ha permitido mirar más allá de nuestro planeta y entender mejor nuestro lugar en el universo. Cada descubrimiento nos muestra lo pequeño que somos en comparación con la inmensidad del cosmos, pero también nos inspira a seguir explorando y aprendiendo.",
    "La ciencia ficción nos invita a imaginar futuros posibles, a explorar las consecuencias de los avances tecnológicos y a reflexionar sobre lo que significa ser humano en un mundo en constante cambio. Es un género que nos desafía a pensar más allá de los límites conocidos.",
    "El cine documental es una herramienta poderosa para contar historias reales y generar conciencia sobre temas sociales, políticos y ambientales. A través de la cámara, los cineastas nos ofrecen una visión íntima de la vida de personas y lugares que de otro modo no conoceríamos.",
    "Las redes sociales han transformado la manera en que nos comunicamos, permitiéndonos compartir ideas, opiniones y momentos con personas de todo el mundo. Sin embargo, también han traído consigo retos como la desinformación y la falta de privacidad.",
    "El cambio climático es uno de los mayores desafíos que enfrenta la humanidad en la actualidad. Sus efectos ya son visibles en todo el mundo, y es nuestra responsabilidad tomar medidas para reducir nuestra huella de carbono y proteger el planeta para las futuras generaciones.",
    "El ser humano siempre ha sentido una fascinación por lo desconocido, lo que ha llevado a la exploración de los océanos, las selvas y el espacio exterior. Esa curiosidad innata es lo que nos impulsa a seguir descubriendo los misterios que nos rodean."
];

 // Lista de 30 textos aleatorios
 let textToType = "";
let textToTypeArray = [];
const textContainer = document.getElementById("text-to-type");
const typingInput = document.getElementById("typing-input");
const startButton = document.getElementById("start-button");
const nextPlayerButton = document.getElementById("next-player-button");
const timeDisplay = document.getElementById("time");
const accuracyDisplay = document.getElementById("accuracy");
const currentParticipantDisplay = document.getElementById("current-participant");
const overallResults = document.getElementById("overall-results");
const winnerDisplay = document.getElementById("winner");

let typedChars = [];
let correctChars = 0;
let currentPlayer = 1;
let player1Results = {};
let player2Results = {};

startButton.addEventListener("click", startTest);
nextPlayerButton.addEventListener("click", startNextPlayer);

function startTest() {
    resetTest(); // Reiniciar variables y interfaz
    textToType = getRandomText(); // Obtener un texto aleatorio
    textToTypeArray = textToType.split(""); // Convertir el texto en un array de caracteres
    displayTextToType(); // Mostrar el texto que se va a escribir
    
    typingInput.disabled = false;
    typingInput.focus();
    startButton.disabled = true; // Deshabilitar el botón una vez iniciado
    nextPlayerButton.style.display = "none"; // Asegurar que el botón del siguiente jugador no esté visible
    startTime = new Date().getTime();
    
    interval = setInterval(updateTime, 1000); // Actualizar tiempo
    timer = setTimeout(endTest, maxTime * 1000); // Terminar después de 60 segundos
    typingInput.addEventListener("input", handleTyping); // Manejar entrada de texto
}

// Función que maneja la prueba del siguiente jugador
function startNextPlayer() {
    // Reiniciar todo antes de comenzar con el nuevo jugador
    clearInterval(interval); // Limpiar intervalo del tiempo del jugador anterior
    clearTimeout(timer); // Limpiar timeout si aún estaba corriendo
    typingInput.removeEventListener("input", handleTyping); // Eliminar el evento del jugador anterior
    
    // Cambiar al siguiente jugador
    currentPlayer = 2; // Cambiar al segundo jugador
    currentParticipantDisplay.textContent = "2"; // Actualizar el indicador de jugador actual

    // Ocultar el botón para el siguiente jugador y reiniciar todo
    nextPlayerButton.style.display = "none"; 
    startButton.style.display = "inline"; // Mostrar el botón de iniciar para el segundo jugador
    resetTest(); // Reiniciar la prueba para el segundo jugador

    // Iniciar nuevamente para el segundo jugador
    textToType = getRandomText(); // Obtener un nuevo texto aleatorio
    textToTypeArray = textToType.split(""); // Convertir el nuevo texto en un array de caracteres
    displayTextToType(); // Mostrar el nuevo texto
    
    typingInput.disabled = false; // Habilitar el input nuevamente
    typingInput.focus();
    startButton.disabled = true; // Deshabilitar el botón una vez iniciado el segundo jugador
    
    startTime = new Date().getTime();
    interval = setInterval(updateTime, 1000); // Actualizar tiempo
    timer = setTimeout(endTest, maxTime * 1000); // Terminar después de 60 segundos

    // Volver a agregar el evento de input para manejar la escritura
    typingInput.addEventListener("input", handleTyping);
}

function getRandomText() {
    const randomIndex = Math.floor(Math.random() * textsArray.length);
    return textsArray[randomIndex];
}

function updateTime() {
    const currentTime = new Date().getTime();
    const elapsedTime = Math.floor((currentTime - startTime) / 1000);
    timeDisplay.textContent = elapsedTime;

    if (elapsedTime >= maxTime) {
        endTest();
    }
}

function displayTextToType() {
    textContainer.innerHTML = ""; // Limpiar contenedor

    textToTypeArray.forEach((char, index) => {
        const span = document.createElement("span");
        span.textContent = char;

        if (typedChars[index] === char) {
            span.classList.add("correct");
        } else if (typedChars[index] !== undefined) {
            span.classList.add("incorrect");
        }

        if (index === typedChars.length) {
            span.classList.add("current-char"); // Caracter actual
        }

        textContainer.appendChild(span);
    });
}

function handleTyping() {
    const typedText = typingInput.value;
    typedChars = typedText.split("");

    correctChars = countCorrectCharacters();

    displayTextToType(); // Actualizar el texto con los caracteres correctos/incorrectos

    if (typedText === textToType) {
        endTest(); // Si el usuario termina antes de tiempo
    }
}

function calculateAccuracy() {
    const accuracy = (correctChars / textToType.length) * 100;
    accuracyDisplay.textContent = accuracy.toFixed(2);
    return accuracy;
}

function endTest() {
    clearInterval(interval); // Detener la actualización del tiempo
    clearTimeout(timer); // Limpiar timeout de tiempo límite
    typingInput.disabled = true;

    const timeTaken = parseInt(timeDisplay.textContent, 10);
    const accuracy = calculateAccuracy();

    // Guardar los resultados del jugador actual
    if (currentPlayer === 1) {
        player1Results = { time: timeTaken, accuracy: accuracy };
        nextPlayerButton.style.display = "inline"; // Mostrar el botón para el siguiente jugador
        startButton.style.display = "none"; // Ocultar el botón de iniciar
    } else {
        player2Results = { time: timeTaken, accuracy: accuracy };
        determineWinner();
    }
}

function determineWinner() {
    overallResults.style.display = "block"; // Mostrar resultados generales

    let winner;
    if (player1Results.accuracy > player2Results.accuracy) {
        winner = "Participante 1";
    } else if (player1Results.accuracy < player2Results.accuracy) {
        winner = "Participante 2";
    } else {
        // Si hay empate en precisión, determinar por el tiempo
        if (player1Results.time < player2Results.time) {
            winner = "Participante 1";
        } else {
            winner = "Participante 2";
        }
    }

    winnerDisplay.textContent = winner; // Mostrar quién ganó
}

function countCorrectCharacters() {
    let correct = 0;
    for (let i = 0; i < typedChars.length; i++) {
        if (typedChars[i] === textToTypeArray[i]) {
            correct++;
        }
    }
    return correct;
}

function resetTest() {
    typedChars = [];
    correctChars = 0;
    typingInput.value = "";
    timeDisplay.textContent = "0";
    accuracyDisplay.textContent = "0";
    textContainer.innerHTML = ""; // Limpiar el texto para el nuevo participante
}

    </script>

<script>
        const logo = document.getElementById('floating-logo');
        let xPos = 100, yPos = 100;
        let xSpeed = 2, ySpeed = 2;
        const logoWidth = 150, logoHeight = 150; // Tamaño del logo

        function moveLogo() {
            const windowWidth = window.innerWidth;
            const windowHeight = window.innerHeight;

            // Actualiza la posición
            xPos += xSpeed;
            yPos += ySpeed;

            // Rebota cuando toca los límites de la pantalla
            if (xPos + logoWidth > windowWidth || xPos < 0) {
                xSpeed = -xSpeed;
            }
            if (yPos + logoHeight > windowHeight || yPos < 0) {
                ySpeed = -ySpeed;
            }

            // Aplica la nueva posición al logo
            logo.style.transform = `translate(${xPos}px, ${yPos}px)`;

            requestAnimationFrame(moveLogo);
        }

        // Inicia la animación
        requestAnimationFrame(moveLogo);
    </script>
</body>
</html>
