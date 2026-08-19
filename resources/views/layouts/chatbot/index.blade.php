<!-- Botón flotante del Chatbot -->
<button id="chatbotButton"></button>

<!-- Ventana de Chatbot -->
<div id="chatbotWindow">
    <div id="chatbotHeader">
        <img src="{{ asset('videos/Tara-unscreen-2.gif') }}" alt="Tara">
        <span class="typing-effect">Hola, soy Tara. ¿En qué puedo ayudarte?</span>
        <button id="btn-close-chat">
            <i class="fa-solid fa-x fa-xs"></i>
        </button>
    </div>
    <div id="chatMessages"></div>
    <div class="d-flex">
        <input type="text" id="userMessage" placeholder="Escribe un mensaje...">
        <button id="btn-send-message"><i class="fa-regular fa-paper-plane"></i></button>
    </div>
    <div id="resizeHandle"></div>
</div>