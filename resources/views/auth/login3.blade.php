@extends('index')

@section('main')

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,400;0,9..144,500;1,9..144,500&family=Space+Grotesk:wght@400;500;600;700&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet">

<div class="trakio-login">
    <div class="trakio-login__grid">

        {{-- Panel izquierdo: narrativa de marca --}}
        <div class="trakio-panel trakio-panel--ink">
            <div class="trakio-panel__inner">

                <div class="trakio-brand">
                    <svg class="trakio-seal" viewBox="0 0 120 120" aria-hidden="true">
                        <defs>
                            <path id="sealPath" d="M 60,60 m -46,0 a 46,46 0 1,1 92,0 a 46,46 0 1,1 -92,0" />
                        </defs>
                        <circle cx="60" cy="60" r="46" class="trakio-seal__ring" />
                        <circle cx="60" cy="60" r="35" class="trakio-seal__ring trakio-seal__ring--inner" />
                        <text class="trakio-seal__text">
                            <textPath href="#sealPath" startOffset="0%">
                                TRAKIO&#160;&#160;&#160;&#8226;&#160;&#160;&#160;MUY&#160;PRONTO&#160;&#160;&#160;&#8226;&#160;&#160;&#160;
                            </textPath>
                        </text>
                        <text x="60" y="65" text-anchor="middle" class="trakio-seal__mark">T</text>
                    </svg>

                    <span class="trakio-eyebrow">Muy pronto</span>
                    <h1 class="trakio-headline">Una nueva forma de<br><em>control y trazabilidad.</em></h1>
                    <p class="trakio-sub">
                        Digitaliza, valida y sigue cada expediente de salud pública en un mismo lugar, de principio a fin.
                    </p>
                </div>

                <div class="trakio-chain" aria-hidden="true">
                    <div class="trakio-chain__line"></div>

                    <div class="trakio-node">
                        <span class="trakio-node__dot trakio-node__dot--active"></span>
                        <div class="trakio-node__body">
                            <span class="trakio-node__code">TRK-2026-04127</span>
                            <span class="trakio-node__status">Archivado</span>
                        </div>
                    </div>
                    <div class="trakio-node">
                        <span class="trakio-node__dot"></span>
                        <div class="trakio-node__body">
                            <span class="trakio-node__code">TRK-2026-04126</span>
                            <span class="trakio-node__status">Validado</span>
                        </div>
                    </div>
                    <div class="trakio-node">
                        <span class="trakio-node__dot"></span>
                        <div class="trakio-node__body">
                            <span class="trakio-node__code">TRK-2026-04125</span>
                            <span class="trakio-node__status">Digitalizado</span>
                        </div>
                    </div>
                    <div class="trakio-node trakio-node--last">
                        <span class="trakio-node__dot"></span>
                        <div class="trakio-node__body">
                            <span class="trakio-node__code">TRK-2026-04124</span>
                            <span class="trakio-node__status">Recibido</span>
                        </div>
                    </div>
                </div>

                <p class="trakio-footnote">Cada documento, con su historia completa.</p>
            </div>
        </div>

        {{-- Panel derecho: formulario --}}
        <div class="trakio-panel trakio-panel--paper">
            <div class="trakio-panel__inner trakio-panel__inner--form">

                <span class="trakio-kicker">Acceso interno</span>
                <h2 class="trakio-form-title">Iniciar sesión</h2>

                <form class="trakio-form" method="POST" action="{{ route('login') }}">
                    @csrf

                    {{-- Campo de correo electrónico --}}
                    <div class="trakio-field">
                        <label for="email" class="trakio-label">Correo electrónico</label>
                        <div class="trakio-input">
                            <span class="icon fa fa-user" aria-hidden="true"></span>
                            <input
                                type="email"
                                name="email"
                                id="email"
                                placeholder="usuario@correo.com"
                                required
                                autofocus
                            >
                        </div>
                        <small class="trakio-hint">Ingresa el mismo correo que usaste al registrarte.</small>
                    </div>

                    {{-- Campo de contraseña --}}
                    <div class="trakio-field">
                        <label for="inputPass" class="trakio-label">Contraseña</label>
                        <div class="trakio-input">
                            <span class="icon fa fa-key" aria-hidden="true"></span>
                            <input
                                id="inputPass"
                                type="password"
                                name="password"
                                placeholder="Escribe tu contraseña"
                                autocomplete="off"
                                required
                            >
                            <span id="viewPassIcon" class="fa fa-solid fa-eye trakio-eye" role="button" tabindex="0" aria-label="Mostrar contraseña"></span>
                        </div>
                    </div>

                    {{-- reCAPTCHA --}}
                    <div class="trakio-captcha">
                        {!! NoCaptcha::renderJs() !!}
                        {!! NoCaptcha::display() !!}
                    </div>

                    {{-- Mensajes de error --}}
                    @error('message')
                        <div class="trakio-alert" role="alert">
                            {{ $message }}
                        </div>
                    @enderror

                    {{-- Botón de envío --}}
                    <button type="submit" name="ingresar" id="ingresar" class="trakio-submit btn-loader">
                        Iniciar sesión
                    </button>
                </form>

                <div class="trakio-links">
                    <a href="{{ route('password.request') }}">¿Olvidaste tu contraseña?</a>
                </div>

                <div class="trakio-register">
                    <span>¿No tienes una cuenta?</span>
                    <a href="{{ route('register') }}">Registrarse</a>
                </div>

            </div>
        </div>

    </div>
</div>

<style>
.trakio-login {
    --ink: #10192E;
    --ink-soft: #1C2A45;
    --paper: #F7F4EC;
    --paper-line: #E4DFD0;
    --seal: #9A3324;
    --trace: #3E7C6A;
    --gold: #C9A227;

    min-height: 100vh;
    width: 100%;
    font-family: 'Space Grotesk', sans-serif;
    color: var(--ink);
}

.trakio-login__grid {
    display: grid;
    grid-template-columns: 1.15fr 1fr;
    min-height: 100vh;
}

.trakio-panel {
    display: flex;
    align-items: center;
    padding: clamp(2rem, 4vw, 4.5rem);
}

.trakio-panel--ink {
    background:
        radial-gradient(circle at 15% 10%, rgba(201,162,39,0.10), transparent 45%),
        linear-gradient(160deg, var(--ink) 0%, var(--ink-soft) 100%);
    color: #EDE9DE;
}

.trakio-panel--paper {
    background: var(--paper);
}

.trakio-panel__inner {
    max-width: 480px;
    width: 100%;
    margin: 0 auto;
}

/* --- Marca / sello --- */

.trakio-seal {
    width: 92px;
    height: 92px;
    margin-bottom: 1.75rem;
}
.trakio-seal__ring {
    fill: none;
    stroke: rgba(201,162,39,0.55);
    stroke-width: 1;
}
.trakio-seal__ring--inner {
    stroke: rgba(237,233,222,0.35);
    stroke-dasharray: 2 4;
}
.trakio-seal__text {
    font-family: 'IBM Plex Mono', monospace;
    font-size: 6.4px;
    letter-spacing: 1px;
    fill: var(--gold);
}
.trakio-seal__mark {
    font-family: 'Fraunces', serif;
    font-style: italic;
    font-size: 30px;
    fill: #EDE9DE;
}

.trakio-eyebrow {
    display: inline-block;
    font-family: 'IBM Plex Mono', monospace;
    font-size: 0.72rem;
    letter-spacing: 0.22em;
    text-transform: uppercase;
    color: var(--gold);
    margin-bottom: 0.85rem;
}

.trakio-headline {
    font-family: 'Fraunces', serif;
    font-weight: 500;
    font-size: clamp(1.9rem, 3vw, 2.6rem);
    line-height: 1.15;
    margin: 0 0 1rem;
    color: #F7F4EC;
}
.trakio-headline em {
    font-style: italic;
    color: var(--gold);
}

.trakio-sub {
    font-size: 0.98rem;
    line-height: 1.55;
    color: rgba(237,233,222,0.72);
    max-width: 38ch;
    margin: 0;
}

/* --- Cadena de trazabilidad --- */

.trakio-chain {
    position: relative;
    margin-top: 3rem;
    padding-left: 1.4rem;
}
.trakio-chain__line {
    position: absolute;
    left: 5px;
    top: 6px;
    bottom: 6px;
    width: 1px;
    background: linear-gradient(180deg, var(--trace), rgba(62,124,106,0.05));
}
.trakio-node {
    position: relative;
    display: flex;
    align-items: baseline;
    gap: 0.85rem;
    padding-bottom: 1.35rem;
    opacity: 0;
    animation: trakio-rise 0.6s ease forwards;
}
.trakio-node:nth-child(2) { animation-delay: 0.05s; }
.trakio-node:nth-child(3) { animation-delay: 0.15s; }
.trakio-node:nth-child(4) { animation-delay: 0.25s; }
.trakio-node:nth-child(5) { animation-delay: 0.35s; }
.trakio-node--last { padding-bottom: 0; }

.trakio-node__dot {
    position: absolute;
    left: -1.4rem;
    top: 5px;
    width: 9px;
    height: 9px;
    border-radius: 50%;
    background: var(--ink-soft);
    border: 1px solid var(--trace);
}
.trakio-node__dot--active {
    background: var(--trace);
    box-shadow: 0 0 0 4px rgba(62,124,106,0.22);
}

.trakio-node__body {
    display: flex;
    flex-direction: column;
    gap: 0.15rem;
}
.trakio-node__code {
    font-family: 'IBM Plex Mono', monospace;
    font-size: 0.82rem;
    color: #EDE9DE;
    letter-spacing: 0.02em;
}
.trakio-node__status {
    font-size: 0.78rem;
    color: rgba(237,233,222,0.55);
}

.trakio-footnote {
    margin-top: 1.75rem;
    font-family: 'Fraunces', serif;
    font-style: italic;
    font-size: 0.95rem;
    color: rgba(237,233,222,0.55);
}

@keyframes trakio-rise {
    from { opacity: 0; transform: translateY(6px); }
    to { opacity: 1; transform: translateY(0); }
}
@media (prefers-reduced-motion: reduce) {
    .trakio-node { animation: none; opacity: 1; }
}

/* --- Formulario --- */

.trakio-panel__inner--form { max-width: 400px; }

.trakio-kicker {
    display: block;
    font-family: 'IBM Plex Mono', monospace;
    font-size: 0.72rem;
    letter-spacing: 0.2em;
    text-transform: uppercase;
    color: var(--seal);
    margin-bottom: 0.6rem;
}

.trakio-form-title {
    font-family: 'Fraunces', serif;
    font-weight: 500;
    font-size: 2rem;
    margin: 0 0 2rem;
    color: var(--ink);
}

.trakio-field { margin-bottom: 1.4rem; }

.trakio-label {
    display: block;
    font-size: 0.8rem;
    font-weight: 500;
    color: var(--ink);
    margin-bottom: 0.45rem;
}

.trakio-input {
    display: flex;
    align-items: center;
    gap: 0.65rem;
    border-bottom: 1.5px solid var(--paper-line);
    padding: 0.6rem 0.1rem;
    transition: border-color 0.2s ease;
    position: relative;
}
.trakio-input:focus-within { border-color: var(--trace); }
.trakio-input .icon {
    color: var(--seal);
    font-size: 0.9rem;
    opacity: 0.75;
}
.trakio-input input {
    flex: 1;
    border: none;
    background: transparent;
    outline: none;
    font-family: 'Space Grotesk', sans-serif;
    font-size: 0.95rem;
    color: var(--ink);
}
.trakio-input input::placeholder { color: #9C9587; }
.trakio-input input:focus-visible {
    outline: 2px solid var(--trace);
    outline-offset: 3px;
    border-radius: 3px;
}
.trakio-eye {
    cursor: pointer;
    color: #9C9587;
    font-size: 0.9rem;
}

.trakio-hint {
    display: block;
    margin-top: 0.5rem;
    font-size: 0.78rem;
    color: #9C9587;
}

.trakio-captcha { margin: 0.5rem 0 1.25rem; }

.trakio-alert {
    background: rgba(154,51,36,0.08);
    border-left: 3px solid var(--seal);
    color: var(--seal);
    font-size: 0.88rem;
    padding: 0.75rem 1rem;
    margin-bottom: 1.25rem;
    border-radius: 2px;
}

.trakio-submit {
    width: 100%;
    padding: 0.9rem 1rem;
    border: none;
    border-radius: 999px;
    background: var(--ink);
    color: var(--paper);
    font-family: 'Space Grotesk', sans-serif;
    font-size: 0.95rem;
    font-weight: 600;
    letter-spacing: 0.01em;
    cursor: pointer;
    transition: background 0.2s ease, transform 0.15s ease;
}
.trakio-submit:hover { background: var(--ink-soft); }
.trakio-submit:active { transform: translateY(1px); }
.trakio-submit:focus-visible {
    outline: 2px solid var(--gold);
    outline-offset: 3px;
}

.trakio-links {
    text-align: center;
    margin-top: 1.5rem;
}
.trakio-links a {
    font-size: 0.85rem;
    color: var(--seal);
    text-decoration: none;
}
.trakio-links a:hover { text-decoration: underline; }

.trakio-register {
    margin-top: 2.25rem;
    padding: 0.9rem 1.1rem;
    background: rgba(16,25,46,0.03);
    border: 1px solid var(--paper-line);
    border-radius: 10px;
    text-align: center;
    font-size: 0.85rem;
    color: #6B6459;
}
.trakio-register a {
    margin-left: 0.5rem;
    color: var(--ink);
    font-weight: 600;
    text-decoration: none;
}
.trakio-register a:hover { text-decoration: underline; }

/* --- Responsive --- */

@media (max-width: 960px) {
    .trakio-login__grid {
        grid-template-columns: 1fr;
    }
    .trakio-panel--ink {
        padding-bottom: 2.5rem;
    }
    .trakio-chain { margin-top: 2rem; }
    .trakio-node:nth-child(4),
    .trakio-node:nth-child(5) { display: none; }
    .trakio-footnote { display: none; }
}

@media (max-width: 560px) {
    .trakio-panel { padding: 2rem 1.4rem; }
    .trakio-seal { width: 72px; height: 72px; }
    .trakio-headline { font-size: 1.6rem; }
}
</style>

@endsection