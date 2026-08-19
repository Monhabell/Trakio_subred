@extends('layouts.dig.navigation')

@section('main')

<style>
    .ci-page {
        max-width: 980px;
        margin: 0 auto;
    }

    .ci-image {
        text-align: center;
        margin: 0 0 1.25rem;
    }

    .ci-image img {
        max-width: 100%;
        height: auto;
        border-radius: 10px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.25);
    }

    .ci-text {
        font-size: 1rem;
        line-height: 1.6;
        text-align: center;
        color: var(--shell-muted);
        margin: 0 0 1.25rem;
    }

    .ci-nav {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 0.75rem;
        background: var(--shell-surface);
        border: 1px solid var(--shell-border);
        border-radius: 10px;
        padding: 0.85rem 1.25rem;
        margin-bottom: 2rem;
    }

    .ci-nav-label {
        font-family: var(--shell-mono);
        font-size: 11px;
        letter-spacing: 1.5px;
        text-transform: uppercase;
        color: var(--shell-muted-2);
    }

    .ci-nav-links {
        display: flex;
        flex-wrap: wrap;
        gap: 0.4rem;
        list-style: none;
        margin: 0;
        padding: 0;
    }

    .ci-nav-links a {
        display: inline-block;
        padding: 0.4rem 0.85rem;
        font-size: 0.82rem;
        color: var(--shell-muted);
        border-radius: 6px;
        text-decoration: none;
        transition: color .15s, background-color .15s;
    }

    .ci-nav-links a:hover {
        color: var(--shell-text);
        background: var(--shell-surface-2);
    }

    .ci-links {
        display: flex;
        flex-direction: column;
        gap: 0.85rem;
        margin-top: 1rem;
    }

    .ci-link-row {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 0.6rem;
    }

    .ci-link-row input[type="text"] {
        flex: 1 1 220px;
        min-width: 0;
        background: var(--shell-surface-2);
        color: var(--shell-text);
        border: 1px solid var(--shell-border-strong);
        border-radius: 8px;
        padding: 0.6rem 0.85rem;
        font-family: var(--shell-mono);
        font-size: 0.78rem;
    }

    .ci-copy-btn {
        background: var(--shell-red);
        color: #fff;
        border: none;
        padding: 0.55rem 1.1rem;
        border-radius: 50px;
        font-size: 0.8rem;
        font-weight: 600;
        cursor: pointer;
        transition: box-shadow 0.2s ease;
        flex: 0 0 auto;
    }

    .ci-copy-btn:hover {
        box-shadow: 0 0 14px var(--shell-red-glow);
    }

    .ci-install-link {
        display: inline-block;
    }

    @media (max-width: 576px) {
        .ci-link-row {
            flex-direction: column;
            align-items: stretch;
        }

        .ci-link-row input[type="text"] {
            width: 100%;
        }

        .ci-copy-btn {
            width: 100%;
            text-align: center;
        }
    }
</style>

<div class="ci-page">
    <div class="dig-page-header">
        <div>
            <span class="dig-page-tag">Digitador / Herramientas</span>
            <h1 class="dig-page-title">Guía de Instalación de Code Injector</h1>
        </div>
    </div>

    <nav class="ci-nav" id="navbar-example2">
        <span class="ci-nav-label">Entornos</span>
        <ul class="ci-nav-links">
            <li><a href="#Hogar">Hogar</a></li>
            <li><a href="#Laboral">Laboral</a></li>
            <li><a href="#Comunitario">Comunitario</a></li>
            <li><a href="#Educativo">Educativo</a></li>
            <li><a href="#Institucional">Institucional</a></li>
        </ul>
    </nav>

    <div class="dig-card mb-4">
        <div class="dig-card-head">
            <h2 class="dig-card-title">Instalación general</h2>
        </div>
        <div class="ci-image">
            <img src="{{ asset('img/hogarCode.png') }}" alt="Instalación en Hogar">
        </div>
        <p class="ci-text">Instrucciones: Haz clic en el enlace de abajo para ir a la página de la extensión Code Injector en la Chrome Web Store.
            Una vez en la página, busca el botón que dice "Agregar a Chrome" y haz clic en él.
            Confirma la instalación cuando aparezca la ventana emergente. ¡Listo! Ahora la extensión estará instalada en tu navegador Chrome.
        </p>
        <a href="https://link1.com" class="ci-copy-btn ci-install-link" target="_blank">Instalar</a>
    </div>

    <span id="Hogar"></span>

    {{-- Sección Hogar --}}
    <div class="dig-card mb-4">
        <div class="dig-card-head">
            <h2 class="dig-card-title">Entorno Hogar</h2>
        </div>
        <p class="ci-text">Links para el entorno</p>
        <div class="ci-links">
            <div class="ci-link-row">
                <input id="hogar-link1" type="text" value="https://gesiapps.saludcapital.gov.co/GESI_sistemas/GESI_Form" readonly>
                <button class="ci-copy-btn" onclick="copyToClipboard('hogar-link1')">Copiar</button>
            </div>
            <div class="ci-link-row">
                <input id="hogar-link2" type="text" value="https://trakio.pro/js/calidad/hogar.js" readonly>
                <button class="ci-copy-btn" onclick="copyToClipboard('hogar-link2')">Copiar</button>
            </div>
        </div>
    </div>

    <span id="Laboral"></span>

    {{-- Sección Laboral --}}
    <div class="dig-card mb-4">
        <div class="dig-card-head">
            <h2 class="dig-card-title">Entorno Laboral</h2>
        </div>
        <p class="ci-text">Links para el entorno</p>
        <div class="ci-links">
            <div class="ci-link-row">
                <input id="laboral-link1" type="text" value="https://gesiapps.saludcapital.gov.co/GESI_sistemas/GESI_Form" readonly>
                <button class="ci-copy-btn" onclick="copyToClipboard('laboral-link1')">Copiar</button>
            </div>
            <div class="ci-link-row">
                <input id="laboral-link2" type="text" value="https://trakio.pro/js/calidad/laboral.js" readonly>
                <button class="ci-copy-btn" onclick="copyToClipboard('laboral-link2')">Copiar</button>
            </div>
        </div>
    </div>

    <span id="Comunitario"></span>

    {{-- Sección Comunitario --}}
    <div class="dig-card mb-4">
        <div class="dig-card-head">
            <h2 class="dig-card-title">Entorno Comunitario</h2>
        </div>
        <p class="ci-text">Links para el entorno</p>
        <div class="ci-links">
            <div class="ci-link-row">
                <input id="laboral-link1" type="text" value="https://gesiapps.saludcapital.gov.co/GESI_sistemas/GESI_Form" readonly>
                <button class="ci-copy-btn" onclick="copyToClipboard('laboral-link1')">Copiar</button>
            </div>
            <div class="ci-link-row">
                <input id="Comunitario-link2" type="text" value="https://trakio.pro/js/calidad/Comunitario.js" readonly>
                <button class="ci-copy-btn" onclick="copyToClipboard('Comunitario-link2')">Copiar</button>
            </div>
        </div>
    </div>

    <span id="Educativo"></span>

    {{-- Sección Educativo --}}
    <div class="dig-card mb-4">
        <div class="dig-card-head">
            <h2 class="dig-card-title">Entorno Educativo</h2>
        </div>
        <p class="ci-text">Links para el entorno</p>
        <div class="ci-links">
            <div class="ci-link-row">
                <input id="laboral-link1" type="text" value="https://gesiapps.saludcapital.gov.co/GESI_sistemas/GESI_Form" readonly>
                <button class="ci-copy-btn" onclick="copyToClipboard('laboral-link1')">Copiar</button>
            </div>
            <div class="ci-link-row">
                <input id="educativo-link2" type="text" value="https://trakio.pro/js/calidad/educativo.js" readonly>
                <button class="ci-copy-btn" onclick="copyToClipboard('educativo-link2')">Copiar</button>
            </div>
        </div>
    </div>

    <span id="Institucional"></span>

    {{-- Sección Institucional --}}
    <div class="dig-card mb-4">
        <div class="dig-card-head">
            <h2 class="dig-card-title">Entorno Institucional</h2>
        </div>
        <p class="ci-text">Links para el entorno</p>
        <div class="ci-links">
            <div class="ci-link-row">
                <input id="laboral-link1" type="text" value="https://gesiapps.saludcapital.gov.co/GESI_sistemas/GESI_Form" readonly>
                <button class="ci-copy-btn" onclick="copyToClipboard('laboral-link1')">Copiar</button>
            </div>
            <div class="ci-link-row">
                <input id="institucional-link2" type="text" value="https://trakio.pro/js/calidad/institucional.js" readonly>
                <button class="ci-copy-btn" onclick="copyToClipboard('institucional-link2')">Copiar</button>
            </div>
        </div>
    </div>

    {{-- Repite el diseño para los demás entornos --}}
    {{-- Sección Comunitario, Educativo, Institucional, etc... --}}

</div>

<script>
    function copyToClipboard(inputId) {
        const input = document.getElementById(inputId);
        input.select();
        input.setSelectionRange(0, 99999); // For mobile devices
        document.execCommand('copy');
        alert('Link copiado al portapapeles: ' + input.value);
    }
</script>

@endsection
