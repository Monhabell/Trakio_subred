function openChat() {
    const chatbotWindow = document.getElementById('chatbotWindow');
    chatbotWindow.style.display = chatbotWindow.style.display == 'flex' ? 'none' : 'flex';
}

function closeChat() {
    document.getElementById('chatbotWindow').style.display = 'none';
}

document.addEventListener("keydown", function (event) {
    if (event.key === "Enter") {
        sendMessage();
    }
});

function sendMessage() {
    const userMessage = document.getElementById('userMessage').value;
    const chatMessages = document.getElementById('chatMessages');

    if (userMessage.trim() === "") {
        return;
    }

    // Añadir el mensaje del usuario a la ventana de chat
    chatMessages.innerHTML += `<div class="chatBubble user">${escapeHTML(userMessage)}</div>`;
    document.getElementById('userMessage').value = ''; // Limpiar el input

    // Desplazar scroll hacia abajo
    scrollToBottom();

    // Añadir indicador de "escribiendo..."
    const typingIndicator = document.createElement('div');
    typingIndicator.className = 'chatBubble bot typing';
    typingIndicator.innerHTML = '<span class="dots"><span>.</span><span>.</span><span>.</span></span>';
    chatMessages.appendChild(typingIndicator);
    scrollToBottom();

    // Hacer la solicitud AJAX al servidor
    fetch('/chatbot', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            message: userMessage
        })
    })
    .then(response => response.json())
    .then(data => {
        typingIndicator.remove();

        // Formatear el mensaje antes de añadirlo al chat
        const formattedResponse = formatMessage(data.response);

        // Añadir la respuesta del chatbot a la ventana de chat
        const responseBubble = document.createElement('div');
        responseBubble.className = 'chatBubble bot';
        responseBubble.innerHTML = formattedResponse;
        chatMessages.appendChild(responseBubble);

        // Desplazar scroll hacia abajo
        scrollToBottom();
    })
    .catch(error => {
        console.error('Error:', error);
        typingIndicator.remove();
        chatMessages.innerHTML += `<div class="chatBubble bot">Error: no se pudo enviar el mensaje</div>`;

        // Desplazar scroll hacia abajo
        scrollToBottom();
    });
}

function scrollToBottom() {
    const chatMessages = document.getElementById('chatMessages');
    chatMessages.scrollTop = chatMessages.scrollHeight; // Desplazar el scroll hasta el final
}

// window.onload = function () {
//     // Verificar si el usuario ya ha visto la nueva funcionalidad
//     if (!localStorage.getItem('newFeatureSeen')) {
//         // Mostrar la ventana de nueva funcionalidad
//         document.getElementById('newFeatureWindow').style.display = 'block';
//     }
// };

function escapeHTML(text) {
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;',
    };
    return text.replace(/[&<>"']/g, function(m) { return map[m]; });
}


function formatMessage(message) {
    // Si ya contiene <br>, no lo escapamos
    if (!message.includes("<br>")) {
        message = escapeHTML(message);
    }

    // Manejar bloques de código multilínea ``` ```
    message = message.replace(/```([\s\S]*?)```/g, function (match, codeBlock) {
        return `<pre><code>${codeBlock}</code></pre>`;
    });

    // Manejar fragmentos de código en línea `...`
    message = message.replace(/`([^`]+)`/g, '<code>$1</code>');

    // Convertir **texto** en negritas
    message = message.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');

    return message;
}


const chatbotWindow = document.getElementById('chatbotWindow');
const chatbotHeader = document.getElementById('chatbotHeader');

let isDragging = false;
let offsetX, offsetY;

chatbotHeader.addEventListener('mousedown', (e) => {
    isDragging = true;
    offsetX = e.clientX - chatbotWindow.offsetLeft;
    offsetY = e.clientY - chatbotWindow.offsetTop;
    document.addEventListener('mousemove', onMouseMove);
});

document.addEventListener('mouseup', () => {
    isDragging = false;
    document.removeEventListener('mousemove', onMouseMove);
});

function onMouseMove(e) {
    if (isDragging) {
        const messagesBox = chatbotWindow.querySelector('#chatMessages');
        chatbotWindow.style.left = `${e.clientX - offsetX}px`;
        chatbotWindow.style.top = `${e.clientY - offsetY}px`;
        chatbotWindow.style.bottom = `auto`;
        messagesBox.style.height = `100%`;
    }
}

function initChatbot() {
    const chatbotOpen = document.getElementById('chatbotButton');
    const chatbotSendMessage = document.getElementById('btn-send-message');
    const chatbotClose = document.getElementById('btn-close-chat');

    chatbotOpen.addEventListener('click', openChat);
    chatbotClose.addEventListener('click', closeChat);
    chatbotSendMessage.addEventListener('click', sendMessage);
}

initChatbot();


try {
    let botonclsechat = document.getElementById('btnCloseNewFeature')
    if (botonclsechat){
        botonclsechat.addEventListener('click', closeNewFeature);
    }
    
// Función para cerrar la ventana de nueva funcionalidad
    function closeNewFeature() {
        document.getElementById('newFeatureWindow').style.display = 'none';
        // Marcar que el usuario ha visto la nueva funcionalidad
        localStorage.setItem('newFeatureSeen', 'true');
    }
} catch (error) {
    console.error(error);
    
}

