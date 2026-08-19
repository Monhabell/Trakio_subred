export default class Calculo {

    #esActualizacion = false; // 

    #scrollCounter = 0;
    #lastScrollTime = 0;
    #continuousScrollStreak = 0;
    #lastActivityType = null; // 'scroll' | 'key' | 'mouse' | 'change'
    #scrollCreditedSeconds = 0; // Tiempo de revisión (solo scroll) ya acreditado
    #maxScrollCreditSeconds = 120; // Tope antifraude de scroll puro
    // --- Propiedades Privadas ---
    #typingSeconds = 0;
    #inactivitySeconds = 0;
    #timerStartedInactivity = 60;
    #pendingSeconds = 0; // Gabela: Tiempo acumulado mientras verifica físico

    #totalCharacters = 0;
    #minKpmThreshold = 120;
    #lastActionTimestamp = Date.now();
    #recentActivity = [];

    // --- Propiedades Anti-Fraude ---
    #lastRegisteredKey = null;
    #keyRepeatCounter = 0;
    #mouseClickCounter = 0;      // NUEVO: Contador de clics rápidos
    #lastClickTimestamp = 0;    // NUEVO: Timestamp del último clic

    #typingInterval = null;
    #inactivityInterval = null;
    #typingDiv = null;
    #inactivityDiv = null;
    #isUIVisible = false;

    #nameLocalStorageTyping = 'tiempo_digitacion';
    #nameLocalStorageInactivity = 'tiempo_inactividad';
    #nameLocalStoragePending = 'tiempo_reserva';
    #nameLocalStorageSessionId = 'tiempo_ficha_id';

    #eventsRegistered = false;

    #apiUrl = 'https://www.trakio.pro/verificar-ficha/';
    #inputSelector = '#Ficha_fic';
    #idbasesInputSelector = '#Id_Base';

    // Centraliza la escritura de #pendingSeconds para que siempre quede en cache
    // (localStorage) y sobreviva a una recarga de página antes de liberarse.
    #setPendingSeconds(value) {
        this.#pendingSeconds = value;
        if (this.#pendingSeconds > 0) {
            localStorage.setItem(this.#nameLocalStoragePending, this.#pendingSeconds);
        } else {
            localStorage.removeItem(this.#nameLocalStoragePending);
        }
    }

    // --- Getters ---
    get totalTypingSeconds() { return this.#typingSeconds; }
    get inactivitySeconds() { return this.#inactivitySeconds; }
    get consinactivitySeconds() {
        return Number(localStorage.getItem(this.#nameLocalStorageInactivity)) || 0;
    }

    /**
     * Libera la reserva (#pendingSeconds) acumulada mientras "verificaba físico"
     * (pausas de teclado/scroll) y la suma al tiempo real, en vez de perderla.
     * Se debe llamar al guardar (botón) y al cerrar/recargar la página, porque
     * #pendingSeconds solo vive en memoria y no se persiste en localStorage.
     */
    liberarReservaPendiente() {
        if (this.#pendingSeconds <= 0) return;

        // La reserva (verificación sin actividad) respeta el mismo tope antifraude
        // que el crédito por scroll, en cualquier modo.
        const remaining = Math.max(0, this.#maxScrollCreditSeconds - this.#scrollCreditedSeconds);
        const credit = Math.min(this.#pendingSeconds, remaining);
        this.#typingSeconds += credit;
        this.#scrollCreditedSeconds += credit;

        this.#setPendingSeconds(0);
        localStorage.setItem(this.#nameLocalStorageTyping, this.#typingSeconds);
    }

    resetTimer() {
        localStorage.removeItem(this.#nameLocalStorageTyping);
        localStorage.removeItem(this.#nameLocalStorageInactivity);
        localStorage.removeItem(this.#nameLocalStoragePending);
        localStorage.removeItem(this.#nameLocalStorageSessionId);

        if (this.#typingInterval) clearInterval(this.#typingInterval);
        if (this.#inactivityInterval) clearInterval(this.#inactivityInterval);
        this.#typingInterval = null;
        this.#inactivityInterval = null;

        this.#typingSeconds = 0;
        this.#inactivitySeconds = 0;
        this.#pendingSeconds = 0;
        this.#totalCharacters = 0;
        this.#recentActivity = [];
        this.#lastActionTimestamp = Date.now();
        this.#lastRegisteredKey = null;
        this.#keyRepeatCounter = 0;
        this.#mouseClickCounter = 0;
        this.#lastActivityType = null;
        this.#scrollCreditedSeconds = 0;

        if (this.#typingDiv) { this.#typingDiv.remove(); this.#typingDiv = null; }
        if (this.#inactivityDiv) { this.#inactivityDiv.remove(); this.#inactivityDiv = null; }
    }

    iniciar(resetTimers = false) {
        // El tiempo guardado en localStorage solo es válido para la MISMA ficha que lo generó.
        // Si el navegador quedó con tiempo de una ficha anterior (cierre sin guardar, navegación
        // directa, etc.) y ahora se abre una ficha distinta, ese tiempo no le pertenece: se descarta.
        const inputFicha = document.querySelector(this.#inputSelector);
        const idbasesInput = document.querySelector(this.#idbasesInputSelector);
        const currentSessionId = (inputFicha && idbasesInput && inputFicha.value)
            ? `${inputFicha.value}_${idbasesInput.value}`
            : null;
        const storedSessionId = localStorage.getItem(this.#nameLocalStorageSessionId);

        if (currentSessionId && storedSessionId && storedSessionId !== currentSessionId) {
            resetTimers = true;
        }

        if (resetTimers) this.resetTimer();

        if (currentSessionId) localStorage.setItem(this.#nameLocalStorageSessionId, currentSessionId);

        this.#typingSeconds = Number(localStorage.getItem(this.#nameLocalStorageTyping)) || 0;
        this.#pendingSeconds = Number(localStorage.getItem(this.#nameLocalStoragePending)) || 0;
        this.#inactivitySeconds = 0;

        this.autoDetectarFicha();

        this.createTypingElement();
        this.startTypingCounter();
        this.startInactivityCounter();
        this.registerActivityListeners();
    }

    async autoDetectarFicha() {
        const inputFicha = document.querySelector(this.#inputSelector);
        const idbasesInput = document.querySelector(this.#idbasesInputSelector);


        console.log("Iniciando detección automática de ficha...");
        if (inputFicha) {
            if (inputFicha.value) await this.consultarApi(inputFicha.value, idbasesInput.value);
            inputFicha.addEventListener('blur', async () => {
                await this.consultarApi(inputFicha.value, idbasesInput.value);
            });
        }

        if (idbasesInput) {
            if (idbasesInput.value) await this.consultarApi(inputFicha.value, idbasesInput.value);
            idbasesInput.addEventListener('blur', async () => {
                await this.consultarApi(inputFicha.value, idbasesInput.value,);
            });
        }
    }

    async consultarApi(valor, idBase) {
        if (!valor) return;
        try {
            const response = await fetch(`${this.#apiUrl}${valor}/${idBase}`);
            const data = await response.json();
            // Cambia el modo internamente sin avisar al usuario por botón
            this.#esActualizacion = !!data.es_actualizacion;

            console.log(`API Response for valor=${valor}, idBase=${idBase}:`, data);


            if (this.#esActualizacion) {
                this.#keyRepeatCounter = 0;
                this.#mouseClickCounter = 0;
            }

            console.log(`Modo ${this.#esActualizacion ? 'ACTUALIZACIÓN' : 'PRODUCCIÓN'} activado para ficha ${valor} (ID Base: ${idBase})`);
        } catch (error) {
            console.error("Error consultando API de ficha:", error);
            this.#esActualizacion = false;
        }
    }

    startTypingCounter() {
        if (this.#typingInterval) clearInterval(this.#typingInterval);

        this.#typingInterval = setInterval(() => {
            const now = Date.now();
            const secondsSinceLastAction = (now - this.#lastActionTimestamp) / 1000;
            const isSpamming = this.#keyRepeatCounter > 6 || this.#mouseClickCounter > 20;

            // --- LÓGICA DE GABELA BLINDADA ---
            // Pasar más de #timerStartedInactivity (60s) sin tocar nada también es tiempo
            // de verificación real (revisar el físico), en cualquier modo. La inactividad
            // por sí sola ya NO borra la reserva — solo el tope antifraude
            // (#maxScrollCreditSeconds) limita cuánto se puede acumular sin actividad.
            if (!isSpamming) {
                if (secondsSinceLastAction <= 2) {
                    /* EL BLINDAJE: Solo entregamos la reserva (#pendingSeconds) si hay ráfaga de TECLADO.
                       Los clics de mouse no alimentan #recentActivity, por lo que no pueden liberar la gabela.
                    */
                    if (this.#recentActivity.length >= 3) {
                        this.#typingSeconds += (1 + this.#pendingSeconds);
                        this.#setPendingSeconds(0);
                        this.#scrollCreditedSeconds = 0; // Hubo edición real: se reinicia el tope de revisión por scroll
                        localStorage.setItem(this.#nameLocalStorageTyping, this.#typingSeconds);
                    } else if (this.#esActualizacion && this.#lastActivityType === 'scroll') {
                        /* REVISIÓN POR SCROLL: igual que con teclado, al reanudar el scroll se
                           libera la reserva (#pendingSeconds) acumulada mientras "verificaba
                           físico" sin moverse, en vez de perderla. Sigue topada por
                           #maxScrollCreditSeconds (tope antifraude de scroll puro). */
                        const remaining = this.#maxScrollCreditSeconds - this.#scrollCreditedSeconds;
                        if (remaining > 0) {
                            const credit = Math.min(1 + this.#pendingSeconds, remaining);
                            this.#typingSeconds += credit;
                            this.#scrollCreditedSeconds += credit;
                            localStorage.setItem(this.#nameLocalStorageTyping, this.#typingSeconds);
                        }
                        this.#setPendingSeconds(0);
                        // Superado el tope: no se acredita más tiempo (posible fraude de solo-scroll)
                    } else {
                        this.#typingSeconds += 1;
                        localStorage.setItem(this.#nameLocalStorageTyping, this.#typingSeconds);
                    }
                } else {
                    // Tiempo de verificación sin actividad: se reserva, topado por el
                    // mismo límite antifraude que el crédito por scroll (en ambos modos).
                    const reservado = this.#scrollCreditedSeconds + this.#pendingSeconds;
                    if (reservado < this.#maxScrollCreditSeconds) this.#setPendingSeconds(this.#pendingSeconds + 1);
                }
            } else {
                this.#setPendingSeconds(0);
            }

            // --- CÁLCULO DE VELOCIDAD (Solo basado en teclas reales) ---
            const windowSizeMS = 2000;
            this.#recentActivity = this.#recentActivity.filter(t => (now - t) < windowSizeMS);
            const instantKpm = (this.#recentActivity.length / (windowSizeMS / 1000)) * 60;
            const isTypingVerySlow = (secondsSinceLastAction < 2 && instantKpm < this.#minKpmThreshold && this.#totalCharacters > 10);

            this.updateTypingUI(instantKpm, isTypingVerySlow, secondsSinceLastAction);
        }, 1000);
    }

    registerActivityListeners() {
        if (this.#eventsRegistered) return;

        let lastScroll = 0;

        window.addEventListener('scroll', () => {
            if (!this.#esActualizacion) return;

            const now = Date.now();
            const delta = now - lastScroll;

            lastScroll = now;

            // 🔴 SCROLL DEMASIADO RÁPIDO (tipo macro o rueda libre)
            if (delta < 50) {
                this.#scrollCounter++;
            } else {
                this.#scrollCounter = 0;
            }

            // 🟠 SCROLL CONTINUO SIN PAUSA
            if (delta < 200) {
                this.#continuousScrollStreak++;
            } else {
                this.#continuousScrollStreak = 0;
            }

            // 🚫 BLOQUEO POR COMPORTAMIENTO SOSPECHOSO
            const isScrollSpam =
                this.#scrollCounter > 15 ||       // ráfaga muy rápida
                this.#continuousScrollStreak > 30; // demasiado continuo

            if (isScrollSpam) return;

            // ✅ Actividad válida
            this.#keyRepeatCounter = 0;
            this.#mouseClickCounter = 0;

            this.reduceInactivity(2, 'scroll');
        }, true);

        // --- LISTENERS DE TECLADO ---
        window.addEventListener('keydown', (e) => {
            if (e.repeat) return;

            if (e.key === this.#lastRegisteredKey) {
                this.#keyRepeatCounter++;
            } else {
                this.#keyRepeatCounter = 0;
                this.#lastRegisteredKey = e.key;
            }

            const isSpamming = this.#keyRepeatCounter > 6;

            // 1. TECLAS DE ESCRITURA REAL: Alimentan la ráfaga para cobrar gabela
            if (e.key.length === 1 && e.key !== ' ') {
                this.#totalCharacters++;
                this.#recentActivity.push(Date.now());
                if (!isSpamming) this.reduceInactivity(2, 'key');
            }
            // 2. TECLAS DE NAVEGACIÓN: Solo reducen inactividad, no cobran gabela
            else if (['Tab', 'Enter', 'ArrowUp', 'ArrowDown', 'ArrowLeft', 'ArrowRight', ' '].includes(e.key)) {
                if (!isSpamming) this.reduceInactivity(15, 'key');
            }
        }, true);

        // --- LISTENERS DE MOUSE (Protección contra clics repetitivos) ---
        ['mousedown', 'touchstart'].forEach(evt => {
            window.addEventListener(evt, () => {
                const now = Date.now();

                // Detectar ráfaga de clics (clics con menos de 300ms de diferencia)
                if (now - this.#lastClickTimestamp < 300) {
                    this.#mouseClickCounter++;
                } else {
                    this.#mouseClickCounter = 0;
                }
                this.#lastClickTimestamp = now;

                // Si hay demasiados clics seguidos, se ignora la acción (Anti-Trampa)
                if (this.#mouseClickCounter > 20) return;

                this.#keyRepeatCounter = 0; // El mouse resetea el spam de teclado
                this.reduceInactivity(5, 'mouse');   // El mouse SOLO reduce inactividad
            }, true);
        });

        window.addEventListener('change', () => {
            // El cambio de un select/input se considera actividad válida
            for (let i = 0; i < 8; i++) this.#recentActivity.push(Date.now());
            this.#inactivitySeconds = 0;
            this.#keyRepeatCounter = 0;
            this.#mouseClickCounter = 0;
            this.#lastActivityType = 'change';
            this.#scrollCreditedSeconds = 0; // Hubo edición real: se reinicia el tope de revisión por scroll
            this.#lastActionTimestamp = Date.now();
            this.clearInactivityUI();
        }, true);

        // --- LIBERAR RESERVA AL CERRAR/RECARGAR ---
        // #pendingSeconds solo vive en memoria; si la página se recarga o cierra
        // sin pasar por el botón de guardar, esa reserva se perdería igual.
        ['beforeunload', 'pagehide'].forEach(evt => {
            window.addEventListener(evt, () => this.liberarReservaPendiente());
        });

        this.#eventsRegistered = true;
    }

    reduceInactivity(seconds, activityType = null) {
        if (this.#inactivitySeconds > this.#timerStartedInactivity) {
            const currentAccumulated = Number(localStorage.getItem(this.#nameLocalStorageInactivity)) || 0;
            const sessionInactivity = this.#inactivitySeconds - this.#timerStartedInactivity;
            localStorage.setItem(this.#nameLocalStorageInactivity, currentAccumulated + sessionInactivity);
        }

        this.#inactivitySeconds = Math.max(0, this.#inactivitySeconds - seconds);
        this.#lastActionTimestamp = Date.now();
        if (activityType) this.#lastActivityType = activityType;

        if (this.#inactivitySeconds <= this.#timerStartedInactivity) {
            this.clearInactivityUI();
        }
    }

    clearInactivityUI() {
        if (this.#inactivityDiv) {
            this.#inactivityDiv.remove();
            this.#inactivityDiv = null;
        }
    }

    startInactivityCounter() {
        if (this.#inactivityInterval) clearInterval(this.#inactivityInterval);
        this.#inactivitySeconds = 0;
        this.#inactivityInterval = setInterval(() => {
            this.#inactivitySeconds++;
            if (this.#inactivitySeconds > this.#timerStartedInactivity) {
                this.updateInactivityUI();
            }
        }, 1000);
    }

    // --- MÉTODOS DE UI ---

    updateTypingUI(kpm = 0, slow = false, secondsSinceLastAction = 0) {
        if (!this.#typingDiv) return;

        let estado = "PRODUCIENDO";
        let colorEstado = "#00ff00";

        // 🔵 MODO ACTUALIZACIÓN (más permisivo)
        if (this.#esActualizacion) {

            if (this.#inactivitySeconds > this.#timerStartedInactivity) {
                estado = "INACTIVO";
                colorEstado = "#ff4444";
            }
            else if (this.#lastActivityType === 'scroll' && this.#scrollCreditedSeconds >= this.#maxScrollCreditSeconds) {
                estado = "LÍMITE DE REVISIÓN (POSIBLE FRAUDE)";
                colorEstado = "#ff8800";
            }
            else if (secondsSinceLastAction > 2) {
                estado = "VERIFICANDO FÍSICO";
                colorEstado = "#00ccff";
            }
            else if (slow) {
                estado = "RITMO BAJO";
                colorEstado = "#ffaa00";
            }

        }
        // 🔴 MODO PRODUCCIÓN (estricto)
        else {

            if (slow || this.#keyRepeatCounter > 6 || this.#mouseClickCounter > 20) {
                estado = "RITMO SOSPECHOSO";
                colorEstado = "#ff8800";
            }
            else if (secondsSinceLastAction > 2 && secondsSinceLastAction <= this.#timerStartedInactivity) {
                estado = "VERIFICANDO FÍSICO";
                colorEstado = "#00ccff";
            }
            else if (this.#inactivitySeconds > this.#timerStartedInactivity) {
                estado = "INACTIVO";
                colorEstado = "#ff4444";
            }
        }

        const content = document.getElementById('timer-stats-area');
        if (content) {
            content.innerHTML = `
                <div style="border-bottom: 1px solid #444; margin-bottom: 8px; padding-bottom: 4px;">
                    <small style="font-size: 10px; color: #aaa;">ESTADO:</small><br>
                    <b style="color: ${colorEstado}; font-size: 13px;">${estado}</b>
                    ${this.#pendingSeconds > 0 && !slow && this.#mouseClickCounter <= 20 ? `<br><small style="color:#00ccff">+${this.#pendingSeconds}s en reserva</small>` : ''}
                </div>
                <div style="margin-bottom: 4px;">
                    <small style="color: #aaa;">Tiempo Prod:</small> 
                    <span style="font-size: 16px; font-weight: bold; float: right;">${this.formatTime(this.#typingSeconds)}</span>
                </div>
                <div style="margin-top: 8px; font-size: 11px; color: #ccc;">
                    Velocidad: <b>${Math.round(kpm)} KPM</b><br>
                    Spam T: <b>${this.#keyRepeatCounter}</b> | Clics: <b>${this.#mouseClickCounter}</b>
                    Nuevo o actualizacion: <b>${this.#esActualizacion ? 'ACTUALIZACIÓN' : 'NUEVO'}</b>
                    ${this.#esActualizacion ? `<br>Revisión scroll: <b>${this.#scrollCreditedSeconds}/${this.#maxScrollCreditSeconds}s</b>` : ''}
                </div>

            `;
        }
    }

    createTypingElement() {
        if (this.#typingDiv) return;
        const div = document.createElement('div');
        div.id = 'tiempo_digitacion_display';
        Object.assign(div.style, {
            position: 'fixed', bottom: '10px', left: '10px', zIndex: 1000,
            fontFamily: 'Segoe UI, Arial, sans-serif'
        });

        div.innerHTML = `
            <button id="btn-toggle-timer" style="
                background: #007bff; color: white; border: none; padding: 6px 14px; 
                border-radius: 20px; cursor: pointer; font-size: 12px; font-weight: bold;
                box-shadow: 0 4px 10px rgba(0,0,0,0.3); margin-bottom: 8px; transition: 0.3s;">
                🕒 Ver Reloj
            </button>
            <div id="timer-main-container" style="
                display: none; background: #1a1a1a; color: white; padding: 15px; 
                width: 200px; border-radius: 12px;
                box-shadow: 0 4px 15px rgba(0,0,0,0.5); border: 1px solid #333;">
                <div id="timer-stats-area"></div>
            </div>

        `;

        document.body.appendChild(div);
        this.#typingDiv = div;
        document.getElementById('btn-toggle-timer').onclick = () => this.toggleVisibility();
    }

    toggleVisibility() {
        this.#isUIVisible = !this.#isUIVisible;
        const container = document.getElementById('timer-main-container');
        const btn = document.getElementById('btn-toggle-timer');
        if (container) {
            container.style.display = this.#isUIVisible ? 'block' : 'none';
            btn.innerText = this.#isUIVisible ? '✕ Ocultar' : '🕒 Ver Reloj';
            btn.style.background = this.#isUIVisible ? '#444' : '#007bff';
        }
    }

    updateInactivityUI() {
        if (!this.#inactivityDiv) this.createInactivityElement();
        const realInactivity = this.#inactivitySeconds - this.#timerStartedInactivity;
        this.#inactivityDiv.innerHTML = `
            <b style="color: #ff4444;">¡ALERTA DE PAUSA!</b><br>
            <span style="font-size:18px;">${this.formatTime(realInactivity)}</span><br> 
        `;
    }

    createInactivityElement() {
        const div = document.createElement('div');
        div.id = 'inactivity_warning_display';
        Object.assign(div.style, {
            position: 'fixed', bottom: '160px', left: '10px', backgroundColor: 'rgba(50, 0, 0, 0.95)',
            color: 'white', padding: '12px', borderRadius: '8px', zIndex: 1001,
            width: '200px', textAlign: 'center', border: '2px solid #ff4444', fontFamily: 'sans-serif'
        });
        document.body.appendChild(div);
        this.#inactivityDiv = div;
    }

    formatTime(totalSeconds) {
        const minutes = Math.floor(totalSeconds / 60);
        const seconds = Math.floor(totalSeconds % 60);
        return `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
    }

    finalizar() {
        this.resetTimer();
    }
}