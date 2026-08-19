<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Generador Dinámico de Reportes GESI</title>
<script src="https://cdnjs.cloudflare.com/ajax/libs/exceljs/4.4.0/exceljs.min.js"></script>
<style>
  :root{
    --azul:#1e4d8b; --azul-oscuro:#0f2c52; --azul-claro:#eaf1fb; --gris:#f5f6f8; --gris-100:#f7f8fa;
    --borde:#dde2e8; --texto:#232a33; --texto-sec:#667080; --verde:#1f8a4c; --verde-claro:#e9f7ee;
    --radio:10px; --sombra-sm:0 1px 3px rgba(16,24,40,.06); --sombra-md:0 6px 20px rgba(16,24,40,.10);
  }
  *{box-sizing:border-box}
  body{font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;background:var(--gris);margin:0;color:var(--texto);line-height:1.5}

  /* Barra superior */
  .topbar{background:linear-gradient(120deg,var(--azul-oscuro),var(--azul));color:#fff;padding:22px 32px;box-shadow:var(--sombra-md)}
  .topbar-inner{max-width:1320px;margin:0 auto;display:flex;justify-content:space-between;align-items:center;gap:20px;flex-wrap:wrap}
  .topbar h1{margin:0;font-size:1.3rem;font-weight:700;color:#fff;letter-spacing:.2px}
  .topbar .subtitulo{margin:4px 0 0;color:rgba(255,255,255,.82);font-size:.85rem;max-width:640px}
  .topbar .estado-archivo{font-size:.78rem;background:rgba(255,255,255,.14);padding:7px 16px;border-radius:20px;white-space:nowrap;font-weight:600}

  /* Stepper de progreso */
  .stepper{max-width:1320px;margin:0 auto;padding:18px 32px 0;display:flex}
  .stepper .paso-nav{flex:1;display:flex;align-items:center;gap:10px;position:relative;padding-bottom:16px}
  .stepper .paso-nav:not(:last-child)::after{content:'';position:absolute;top:16px;left:calc(50% + 22px);right:calc(-50% + 22px);height:2px;background:var(--borde)}
  .stepper .paso-nav .num{width:32px;height:32px;border-radius:50%;background:#fff;border:2px solid var(--borde);color:var(--texto-sec);display:flex;align-items:center;justify-content:center;font-size:.85rem;font-weight:700;flex:0 0 auto;z-index:1;transition:all .2s ease}
  .stepper .paso-nav .txt{font-size:.8rem;color:var(--texto-sec);font-weight:600;line-height:1.2}
  .stepper .paso-nav.activo .num{background:var(--azul);border-color:var(--azul);color:#fff;box-shadow:0 0 0 4px var(--azul-claro)}
  .stepper .paso-nav.activo .txt{color:var(--azul)}
  .stepper .paso-nav.completado .num{background:var(--verde);border-color:var(--verde);color:#fff}
  .stepper .paso-nav.completado .txt{color:var(--verde)}
  .stepper .paso-nav.disponible .num{border-color:var(--verde);color:var(--verde)}

  .wrap{max-width:1320px;margin:0 auto;padding:22px 32px 70px}
  .guia{margin-bottom:20px;background:#fff;border:1px solid var(--borde);border-radius:var(--radio);padding:14px 20px;box-shadow:var(--sombra-sm)}
  .guia summary{cursor:pointer;font-weight:600;color:var(--azul);font-size:.9rem}

  .card{background:#fff;border:1px solid var(--borde);border-radius:var(--radio);padding:26px 30px;margin-bottom:20px;box-shadow:var(--sombra-sm)}
  .card h2{font-size:1.08rem;margin:0 0 18px;color:var(--azul-oscuro);font-weight:700}
  label{display:block;font-size:.85rem;font-weight:600;margin-bottom:6px;color:#455065}
  select,input[type=file],input[type=text]{width:100%;padding:10px 12px;border:1px solid var(--borde);border-radius:8px;font-size:.9rem;margin-bottom:14px;background:#fff;color:var(--texto);transition:border-color .15s ease}
  select:focus,input:focus{outline:none;border-color:var(--azul)}

  .btn{background:var(--azul);color:#fff;border:none;padding:11px 24px;border-radius:8px;cursor:pointer;font-size:.9rem;font-weight:600;box-shadow:var(--sombra-sm);transition:transform .12s ease,box-shadow .12s ease,background .12s ease}
  .btn:hover{background:var(--azul-oscuro);box-shadow:var(--sombra-md);transform:translateY(-1px)}
  .btn:active{transform:translateY(0)}
  .btn:disabled{background:#b7c0cc;cursor:not-allowed;box-shadow:none;transform:none}
  .btn-verde{background:var(--verde)} .btn-verde:hover{background:#166b3b}
  .btn-link{background:none;color:var(--azul);padding:0;text-decoration:underline;font-weight:600;box-shadow:none}
  .btn-link:hover{background:none;transform:none;box-shadow:none;color:var(--azul-oscuro)}
  .oculto{display:none}

  .grid-2{display:grid;grid-template-columns:1fr 1fr;gap:28px}
  .grid-3{display:grid;grid-template-columns:repeat(3,1fr);gap:20px}
  @media (max-width:900px){.grid-2,.grid-3{grid-template-columns:1fr}}

  .panel-seccion{background:var(--gris-100);border:1px solid var(--borde);border-radius:var(--radio);padding:18px 20px}
  .panel-seccion .etiqueta-panel{display:inline-block;background:var(--azul);color:#fff;font-size:.72rem;font-weight:700;letter-spacing:.4px;padding:3px 10px;border-radius:5px;margin-bottom:12px;text-transform:uppercase}
  .panel-seccion.panel-b .etiqueta-panel{background:var(--verde)}

  .grid-check{display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:6px 18px;max-height:240px;overflow-y:auto;border:1px solid var(--borde);border-radius:8px;padding:12px 14px;margin-bottom:16px;background:var(--gris-100)}
  .grid-check.col-unica{grid-template-columns:1fr}
  .grid-check label{display:flex;align-items:center;gap:6px;font-weight:400;font-size:.85rem;margin:0;color:var(--texto)}

  .fila-filtro{display:flex;gap:10px;margin-bottom:10px;align-items:center;background:var(--gris-100);padding:9px 12px;border-radius:8px;border:1px solid var(--borde);flex-wrap:wrap}
  .fila-filtro select,.fila-filtro input{margin-bottom:0}

  .badge{display:inline-block;background:var(--azul-claro);color:var(--azul);padding:2px 10px;border-radius:12px;font-size:.75rem;font-weight:600;margin-left:6px}
  .error{color:#b3261e;font-size:.85rem;margin-top:8px}
  .ok{color:var(--verde);font-size:.85rem;margin-top:8px}
  .volver{font-size:.85rem;margin-bottom:14px;display:inline-block}
  .resumen{background:var(--azul-claro);border-radius:8px;padding:12px 16px;font-size:.85rem;margin-bottom:16px;font-weight:600;color:var(--azul-oscuro)}

  #dropzone{border:2px dashed var(--borde);border-radius:var(--radio);padding:40px;text-align:center;color:var(--texto-sec);cursor:pointer;margin-bottom:14px;transition:all .15s ease;background:var(--gris-100)}
  #dropzone:hover{border-color:var(--azul);background:var(--azul-claro)}
  #dropzone.arrastrando{border-color:var(--azul);background:var(--azul-claro)}
  .spinner{display:inline-block;width:14px;height:14px;border:2px solid #fff;border-top-color:transparent;border-radius:50%;animation:girar .7s linear infinite;vertical-align:middle;margin-right:6px}
  @keyframes girar{to{transform:rotate(360deg)}}

  .caja-destacada{background:#fff8e6;border:1px solid #f0d98c;border-radius:var(--radio);padding:16px 20px;margin-bottom:20px}
  .caja-diferencia{background:#fff8e6;border:1px solid #f0d98c;border-radius:var(--radio);padding:16px 20px;margin-top:16px}

  .barra-preview{position:fixed;left:0;right:0;bottom:0;background:#fff;border-top:3px solid var(--verde);box-shadow:0 -8px 24px rgba(16,24,40,.16);z-index:1000;display:flex;flex-direction:column;max-height:50vh}
  .barra-preview.oculto{display:none}
  .barra-preview-header{display:flex;justify-content:space-between;align-items:center;padding:10px 32px;cursor:pointer;background:var(--verde-claro);border-bottom:1px solid #cfe9d2;flex:0 0 auto;user-select:none}
  .barra-preview-header strong{font-size:.85rem;color:var(--verde)}
  .barra-preview-header span{font-size:.8rem;color:var(--texto-sec)}
  .barra-preview-body{overflow-y:auto;padding:14px 32px 20px;display:grid;grid-template-columns:1fr;gap:0;max-width:1320px;margin:0 auto;width:100%}
  .barra-preview.colapsada .barra-preview-body{display:none}
  .preview-live:empty{display:none}
  .preview-live table{border-collapse:collapse;font-size:.78rem;width:100%;margin-top:6px}
  .preview-live th,.preview-live td{border:1px solid var(--borde);padding:5px 8px;white-space:nowrap}
  .preview-live th{background:var(--gris-100);text-align:left}
  .preview-live + .preview-live:not(:empty){margin-top:16px;padding-top:16px;border-top:1px dashed var(--borde)}
  @media (min-width:1100px){
    .barra-preview-body:has(.preview-live:not(:empty)) { grid-template-columns:1fr; }
    .barra-preview-body:has(#previewPaso3:not(:empty)):has(#previewCruce:not(:empty)){grid-template-columns:1fr 1fr;gap:32px}
    .barra-preview-body:has(#previewPaso3:not(:empty)):has(#previewCruce:not(:empty)) .preview-live + .preview-live:not(:empty){margin-top:0;padding-top:0;border-top:none;border-left:1px dashed var(--borde);padding-left:32px}
  }
  .auto-stats-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(170px,1fr));gap:10px;margin-top:8px}
  .auto-stats-col{background:var(--gris-100);border:1px solid var(--borde);border-radius:8px;padding:8px 10px}
  .auto-stats-col h4{margin:0 0 4px;font-size:.78rem;color:var(--azul)}
  .auto-stats-col table{width:100%;font-size:.72rem}
  .auto-stats-col td{border:none;padding:1px 0}
</style>
</head>
<body>
<div class="topbar">
  <div class="topbar-inner">
    <div>
      <h1>Generador Dinámico de Reportes GESI</h1>
      <p class="subtitulo">Sube cualquier export GESI, elige qué datos necesitas y descarga un Excel con detalle y estadísticas. Todo se procesa en tu navegador — el archivo nunca sale de tu equipo.</p>
    </div>
    <span class="estado-archivo" id="estadoArchivo">Sin archivo cargado</span>
  </div>
</div>

<div class="stepper">
  <div class="paso-nav activo" data-paso="1"><span class="num">1</span><span class="txt">Subir archivo</span></div>
  <div class="paso-nav" data-paso="2"><span class="num">2</span><span class="txt">Módulo y sección</span></div>
  <div class="paso-nav" data-paso="3"><span class="num">3</span><span class="txt">Configurar reporte</span></div>
  <div class="paso-nav" data-paso="4"><span class="num">4</span><span class="txt">Cruce (opcional)</span></div>
</div>

<div class="wrap">
  <details class="guia">
    <summary>📖 ¿Cómo saco el reporte que necesito? (guía rápida)</summary>
    <ol style="font-size:.85rem;color:#444;margin:12px 0 4px;padding-left:20px;line-height:1.6">
      <li><b>Sube el archivo</b> exportado de GESI (.csv). Puede tener cualquier estructura, se detecta sola.</li>
      <li><b>Elige el módulo y la sección</b> donde vive el dato que buscas. Si no sabes cuál es, prueba con la que tenga el nombre más parecido a lo que te pidieron (ej. "sesiones colectivas", "seguimiento", "identificación").</li>
      <li>Si aparece la caja amarilla de <b>"Resumen ejecutivo"</b>, úsala primero — trae ya calculadas las métricas típicas (sesiones, tamizajes, temas, perfiles) sin configurar nada.</li>
      <li>Si necesitas algo distinto, abre <b>"Arma un reporte personalizado"</b>:
        <ul style="margin:6px 0;padding-left:18px">
          <li><b>Columnas del detalle</b>: cuáles quieres ver en la hoja de datos (deja todas sin marcar para verlas todas).</li>
          <li><b>Agrupar por</b>: si quieres contar/sumar por categoría (ej. por Localidad, por UPZ) marca esa columna aquí.</li>
          <li><b>Filtros</b>: para quedarte solo con ciertos registros (ej. "UT DERIVADA A = UTI Alta"). <b>Haz clic en el campo "valor" para ver el desplegable</b> con los valores que realmente existen — así no escribes algo que no coincide con el dato real.</li>
        </ul>
      </li>
      <li>Dale <b>"Generar y descargar Excel"</b>. Si el resultado da 0 filas, la herramienta te muestra qué valores sí existen en esa columna para que corrijas el filtro.</li>
    </ol>
    <p style="font-size:.8rem;color:#777;margin-top:8px">¿Necesitas cruzar datos de dos secciones distintas (ej. sesiones + tamizajes por número de documento)? Baja hasta la sección <b>"🔀 Cruce entre dos secciones"</b>, más abajo.</p>
  </details>

  <!-- PASO 1 -->
  <div class="card" id="paso1">
    <h2>1. Sube el archivo</h2>
    <div id="dropzone">Arrastra el archivo aquí o haz clic para seleccionarlo<br><small>.csv / .txt</small></div>
    <input type="file" id="inputArchivo" accept=".csv,.txt" style="display:none">
    <button class="btn" id="btnAnalizar" disabled>Analizar archivo</button>
    <div id="msgPaso1"></div>
  </div>

  <!-- PASO 2 -->
  <div class="card oculto" id="paso2">
    <h2>2. Elige módulo y sección</h2>
    <label>Módulo</label>
    <select id="selModulo"></select>
    <label>Sección</label>
    <select id="selSeccion"></select>
    <button class="btn" id="btnContinuar">Continuar</button>
  </div>

  <!-- PASO 3 -->
  <div class="card oculto" id="paso3">
    <a class="volver btn-link" id="btnVolver" href="javascript:void(0)">&larr; Cambiar módulo/sección</a>
    <h2>3. Configura el reporte</h2>
    <div class="resumen" id="resumenSeccion"></div>

    <div id="cajaResumenColectivo" class="oculto caja-destacada" style="margin-bottom:18px">
      <strong style="font-size:.9rem">📊 Resumen ejecutivo — Sesiones Colectivas</strong>
      <p style="font-size:.82rem;color:#665;margin:6px 0 10px">Detecté columnas típicas de esta sección (participantes, tamizajes, temas, perfiles). Genera el resumen con las métricas ya calculadas, sin configurar nada más.</p>
      <button class="btn btn-verde" id="btnResumenColectivo">Generar resumen ejecutivo</button>
      <div id="msgResumenColectivo"></div>
    </div>

    <details style="margin-bottom:14px">
    <summary style="cursor:pointer;font-size:.85rem;color:#555;font-weight:600">O arma un reporte personalizado (avanzado)</summary>
    <div style="margin-top:14px">
    <label>Columnas a incluir en el detalle (ninguna marcada = todas)</label>
    <div class="grid-check" id="checkColumnas"></div>

    <label>Agrupar por (opcional, puedes elegir varias)</label>
    <div class="grid-check" id="checkGroupBy"></div>

    <div class="grid-3">
      <div>
        <label>Tipo de estadística</label>
        <select id="selAggregate">
          <option value="count">Contar registros</option>
          <option value="sum">Sumar columna</option>
          <option value="avg">Promediar columna</option>
        </select>
      </div>
      <div id="wrapAggCol">
        <label>Columna numérica</label>
        <select id="selAggCol"></select>
      </div>
    </div>

    <label>Filtros (opcional)</label>
    <p style="font-size:.78rem;color:#777;margin:-10px 0 10px">
      Elige la columna, luego haz clic en el campo "valor" para ver los valores reales que existen ahí (evita errores de escritura).
      <b>=</b> y <b>!=</b> no distinguen mayúsculas/minúsculas ni espacios. <b>Contiene</b> busca el texto en cualquier parte del valor.
    </p>
    <div id="listaFiltros"></div>
    <button class="btn-link" id="btnAgregarFiltro" style="margin-bottom:14px">+ Agregar filtro</button>
    <br>

    <button class="btn btn-verde" id="btnGenerar">Generar y descargar Excel</button>
    <div id="msgPaso3"></div>
    </div>
    </details>
  </div>

  <!-- PASO 4: Cruce entre secciones -->
  <div class="card oculto" id="paso4">
    <h2>🔀 Cruce entre dos secciones</h2>
    <p style="font-size:.82rem;color:#666;margin-top:-8px;margin-bottom:18px">Une datos de dos secciones distintas usando una llave común (ej. <code>Ficha_fic</code> o número de documento). Ten en cuenta que Ficha_fic puede repetirse entre módulos — revisa los resultados si esperas cruces 1 a 1.</p>

    <div class="grid-2">
      <div class="panel-seccion">
        <span class="etiqueta-panel">Sección A</span>
        <label>Módulo</label>
        <select id="selModuloA"></select>
        <label>Sección</label>
        <select id="selSeccionA"></select>
        <label>Columna llave</label>
        <select id="selKeyA"></select>
      </div>
      <div class="panel-seccion panel-b">
        <span class="etiqueta-panel">Sección B</span>
        <label>Módulo</label>
        <select id="selModuloB"></select>
        <label>Sección</label>
        <select id="selSeccionB"></select>
        <label>Columna llave</label>
        <select id="selKeyB"></select>
      </div>
    </div>

    <label style="margin-top:18px">Tipo de cruce</label>
    <select id="selTipoCruce">
      <option value="inner">Solo coincidencias (inner join)</option>
      <option value="leftA">Todas las filas de A, aunque no crucen con B</option>
    </select>

    <button class="btn" id="btnCargarColumnasCruce">Cargar columnas de ambas secciones</button>

    <div id="cajaColumnasCruce" class="oculto" style="margin-top:20px">
      <div class="grid-2">
        <div>
          <label>Columnas de A a incluir (ninguna = todas)</label>
          <div class="grid-check col-unica" id="checkColsA"></div>
        </div>
        <div>
          <label>Columnas de B a incluir (ninguna = todas)</label>
          <div class="grid-check col-unica" id="checkColsB"></div>
        </div>
      </div>

      <div class="grid-2">
        <div>
          <label>Filtros en Sección A (opcional)</label>
          <p style="font-size:.78rem;color:#777;margin:-8px 0 8px">Ej. quedarte solo con los de "alta" antes de cruzar.</p>
          <div id="filtrosCruceA"></div>
          <button class="btn-link" id="btnAgregarFiltroA" style="margin-bottom:10px">+ Agregar filtro</button>
        </div>
        <div>
          <label>Filtros en Sección B (opcional)</label>
          <p style="font-size:.78rem;color:#777;margin:-8px 0 8px">Ej. un campo de Plan de Trabajo mayor/menor/entre un valor.</p>
          <div id="filtrosCruceB"></div>
          <button class="btn-link" id="btnAgregarFiltroB" style="margin-bottom:10px">+ Agregar filtro</button>
        </div>
      </div>

      <div class="caja-diferencia">
        <label style="margin-top:0">Filtro por diferencia entre columnas (opcional)</label>
        <p style="font-size:.78rem;color:#665;margin:-6px 0 10px">Para reglas tipo "Valoración final menos Valoración inicial mayor o igual a 25%". Los valores con "%" se limpian automáticamente.</p>
        <div style="display:flex;gap:12px;align-items:end;flex-wrap:wrap">
          <div style="flex:1;min-width:180px">
            <label style="font-weight:400">Columna 1 (minuendo, ej. final)</label>
            <select id="selDiffCol1"><option value="">-- No usar este filtro --</option></select>
          </div>
          <div style="flex:0 0 20px;text-align:center;padding-bottom:14px">−</div>
          <div style="flex:1;min-width:180px">
            <label style="font-weight:400">Columna 2 (sustraendo, ej. inicial)</label>
            <select id="selDiffCol2"></select>
          </div>
          <div style="flex:0 0 90px">
            <label style="font-weight:400">Operador</label>
            <select id="selDiffOp">
              <option value=">=">&ge;</option>
              <option value="<=">&le;</option>
              <option value=">">&gt;</option>
              <option value="<">&lt;</option>
              <option value="=">=</option>
            </select>
          </div>
          <div style="flex:0 0 90px">
            <label style="font-weight:400">Valor (%)</label>
            <input type="text" id="inputDiffValor" placeholder="25">
          </div>
        </div>
      </div>

      <label style="margin-top:16px">Estadística sobre el resultado cruzado (opcional)</label>
      <div class="grid-3">
        <div>
          <label style="font-weight:400">Agrupar por</label>
          <select id="selGroupByCruce"><option value="">-- Sin agrupar --</option></select>
        </div>
        <div>
          <label style="font-weight:400">Tipo</label>
          <select id="selAggregateCruce">
            <option value="count">Contar</option>
            <option value="sum">Sumar columna</option>
            <option value="avg">Promediar columna</option>
          </select>
        </div>
        <div id="wrapAggColCruce">
          <label style="font-weight:400">Columna numérica a sumar/promediar</label>
          <select id="selAggColCruce"></select>
        </div>
      </div>

      <button class="btn btn-verde" id="btnGenerarCruce" style="margin-top:8px">Generar y descargar cruce</button>
      <div id="msgCruce"></div>
    </div>

  </div>
</div>

<div class="barra-preview oculto" id="barraVistaPrevia">
  <div class="barra-preview-header" id="barraVistaPreviaHeader">
    <strong>👁 Vista previa en tiempo real</strong>
    <span id="barraVistaPreviaToggle">▾ minimizar</span>
  </div>
  <div class="barra-preview-body">
    <div class="preview-live" id="previewPaso3"></div>
    <div class="preview-live" id="previewCruce"></div>
  </div>
</div>

<script>
// ---------------------------------------------------------------
// Estado global
// ---------------------------------------------------------------
let arbol = {};          // { modulo: { seccion: { headers:[], rows:[[...]] } } }
let moduloActual = null;
let seccionActual = null;
let columnasDisponibles = [];

// ---------------------------------------------------------------
// Stepper superior de progreso
// ---------------------------------------------------------------
function actualizarStepperNav(activo) {
  document.querySelectorAll('.stepper .paso-nav[data-paso]').forEach(el => {
    const n = parseInt(el.dataset.paso, 10);
    el.classList.remove('activo', 'completado');
    if (n < activo) el.classList.add('completado');
    else if (n === activo) el.classList.add('activo');
  });
}

// ---------------------------------------------------------------
// Barra de vista previa flotante (compartida entre paso 3 y el cruce)
// ---------------------------------------------------------------
function ajustarEspacioBarraPreview() {
  const barra = document.getElementById('barraVistaPrevia');
  if (!barra || barra.classList.contains('oculto')) {
    document.body.style.paddingBottom = '';
    return;
  }
  document.body.style.paddingBottom = (barra.offsetHeight + 20) + 'px';
}

// Muestra/oculta la barra según si alguno de los dos paneles (reporte simple
// o cruce) tiene contenido, y reserva espacio en la página para que nunca
// tape el contenido ni obligue a hacer scroll para verla.
function actualizarBarraPreview() {
  const barra = document.getElementById('barraVistaPrevia');
  const p3 = document.getElementById('previewPaso3');
  const pc = document.getElementById('previewCruce');
  const visible = !!((p3 && p3.innerHTML.trim()) || (pc && pc.innerHTML.trim()));
  barra.classList.toggle('oculto', !visible);
  ajustarEspacioBarraPreview();
}

document.getElementById('barraVistaPreviaHeader').addEventListener('click', () => {
  const barra = document.getElementById('barraVistaPrevia');
  const colapsada = barra.classList.toggle('colapsada');
  document.getElementById('barraVistaPreviaToggle').textContent = colapsada ? '▸ expandir' : '▾ minimizar';
  ajustarEspacioBarraPreview();
});
window.addEventListener('resize', ajustarEspacioBarraPreview);

// Cuenta valores automáticamente por columna, sin que el usuario tenga que
// configurar "agrupar por": ignora columnas vacías o con demasiados valores
// distintos (probablemente texto libre o documentos/IDs, no categorías).
function calcularAutoStats(records, columnas, maxDistintos = 40, maxColumnas = 8) {
  const califican = [];
  for (const col of columnas) {
    const frec = {};
    for (const r of records) {
      const v = String(r[col] ?? '').trim();
      if (!v) continue;
      frec[v] = (frec[v] || 0) + 1;
    }
    const entradas = Object.entries(frec).sort((a, b) => b[1] - a[1]);
    if (entradas.length >= 2 && entradas.length <= maxDistintos) {
      califican.push({ columna: col, entradas });
    }
  }
  return { mostrar: califican.slice(0, maxColumnas), totalCalifican: califican.length };
}

function renderAutoStatsHtml(resultado) {
  if (!resultado.mostrar.length) return '';
  let html = '<div style="margin-top:12px"><strong>📊 Conteos automáticos por columna:</strong>';
  html += '<div class="auto-stats-grid">';
  resultado.mostrar.forEach(({ columna, entradas }) => {
    const top = entradas.slice(0, 10);
    html += `<div class="auto-stats-col"><h4>${escHtml(columna)}</h4><table>`
      + top.map(([valor, cant]) => `<tr><td>${escHtml(valor)}</td><td style="text-align:right;font-weight:600">${cant}</td></tr>`).join('')
      + '</table>';
    if (entradas.length > top.length) html += `<div style="font-size:.68rem;color:#888;margin-top:2px">+${entradas.length - top.length} valor(es) más</div>`;
    html += '</div>';
  });
  html += '</div>';
  if (resultado.totalCalifican > resultado.mostrar.length) {
    html += `<div style="font-size:.72rem;color:#888;margin-top:6px">Mostrando ${resultado.mostrar.length} de ${resultado.totalCalifican} columna(s) con conteos automáticos posibles.</div>`;
  }
  html += '</div>';
  return html;
}

// ---------------------------------------------------------------
// PASO 1: carga y parseo
// ---------------------------------------------------------------
const dropzone = document.getElementById('dropzone');
const inputArchivo = document.getElementById('inputArchivo');
const btnAnalizar = document.getElementById('btnAnalizar');
let archivoSeleccionado = null;

dropzone.addEventListener('click', () => inputArchivo.click());
dropzone.addEventListener('dragover', e => { e.preventDefault(); dropzone.classList.add('arrastrando'); });
dropzone.addEventListener('dragleave', () => dropzone.classList.remove('arrastrando'));
dropzone.addEventListener('drop', e => {
  e.preventDefault();
  dropzone.classList.remove('arrastrando');
  if (e.dataTransfer.files.length) {
    archivoSeleccionado = e.dataTransfer.files[0];
    dropzone.textContent = archivoSeleccionado.name;
    btnAnalizar.disabled = false;
  }
});
inputArchivo.addEventListener('change', e => {
  if (e.target.files.length) {
    archivoSeleccionado = e.target.files[0];
    dropzone.textContent = archivoSeleccionado.name;
    btnAnalizar.disabled = false;
  }
});

btnAnalizar.addEventListener('click', async () => {
  const msg = document.getElementById('msgPaso1');
  msg.innerHTML = '<span class="spinner" style="border-color:#1e4d8b;border-top-color:transparent"></span>Procesando...';
  btnAnalizar.disabled = true;
  try {
    const texto = await leerConEncoding(archivoSeleccionado);
    arbol = parsearGesi(texto);
    if (Object.keys(arbol).length === 0) {
      throw new Error('No se detectaron módulos en el archivo. Verifica el formato.');
    }
    moduloActual = null;
    seccionActual = null;
    poblarModulos();
    poblarModulosCruce();
    document.getElementById('paso1').classList.add('oculto');
    document.getElementById('paso2').classList.remove('oculto');
    document.getElementById('paso4').classList.remove('oculto');
    document.getElementById('previewPaso3').innerHTML = '';
    document.getElementById('previewCruce').innerHTML = '';
    actualizarBarraPreview();
    actualizarStepperNav(2);
    document.querySelector('.stepper .paso-nav[data-paso="4"]').classList.add('disponible');
    document.getElementById('estadoArchivo').textContent = '📄 ' + archivoSeleccionado.name;
  } catch (err) {
    msg.innerHTML = '<div class="error">' + err.message + '</div>';
    btnAnalizar.disabled = false;
  }
});

// Detecta encoding por BOM (UTF-16LE/BE, UTF-8) con fallback a UTF-8
async function leerConEncoding(file) {
  const buffer = await file.arrayBuffer();
  const bytes = new Uint8Array(buffer);

  if (bytes[0] === 0xFF && bytes[1] === 0xFE) {
    return new TextDecoder('utf-16le').decode(bytes.slice(2));
  }
  if (bytes[0] === 0xFE && bytes[1] === 0xFF) {
    return new TextDecoder('utf-16be').decode(bytes.slice(2));
  }
  if (bytes[0] === 0xEF && bytes[1] === 0xBB && bytes[2] === 0xBF) {
    return new TextDecoder('utf-8').decode(bytes.slice(3));
  }
  // Heurística: muchos bytes nulos en los primeros 200 bytes = probable UTF-16 sin BOM
  let nulos = 0;
  for (let i = 0; i < Math.min(200, bytes.length); i++) if (bytes[i] === 0) nulos++;
  if (nulos > 10) {
    return new TextDecoder('utf-16le').decode(bytes);
  }
  return new TextDecoder('utf-8').decode(bytes);
}

// Parser dinámico: Módulo => / Sección => / Sub-Sección => (encabezado) / filas de datos
function parsearGesi(texto) {
  // Corrige un defecto común en exports GESI: un marcador "Sub-Sección => ..."
  // incrustado a mitad de la línea de encabezado sin la comilla invertida de
  // apertura (ej. "...CAMPO`;Sub-Sección => Texto`;`SIGUIENTE..." en vez de
  // "...CAMPO`;`Sub-Sección => Texto`;`SIGUIENTE..."). Sin esta corrección,
  // dos columnas se fusionan en una sola y todo lo que viene después queda
  // desplazado un puesto respecto a las filas de datos reales.
  texto = texto.replace(/`;(Sub-Secci[óo]n\s*=>)/gi, '`;`$1');

  const lineas = texto.split(/\r\n|\r|\n/);
  const resultado = {};
  let modulo = null, seccion = null, headerActivo = null;

  const reModulo = /^M[óo]dulo\s*=>\s*(.+)$/i;
  const reSeccion = /^Secci[óo]n\s*=>\s*(.+)$/i;
  const reSubSeccion = /^Sub-Secci[óo]n\s*=>\s*(.+)$/i;

  const splitFields = (linea) => {
    linea = linea.replace(/^`+|`+$/g, '');
    return linea.split('`;`').map(v => v.replace(/^`+|`+$/g, '').trim());
  };

  for (let raw of lineas) {
    const linea = raw.replace(/\s+$/, '');
    if (!linea) continue;

    let m;
    if ((m = linea.match(reModulo))) {
      modulo = m[1].trim();
      seccion = null; headerActivo = null;
      resultado[modulo] = resultado[modulo] || {};
      continue;
    }
    if ((m = linea.match(reSeccion))) {
      seccion = m[1].trim();
      headerActivo = null;
      if (modulo) {
        resultado[modulo][seccion] = resultado[modulo][seccion] || { headers: [], rows: [] };
      }
      continue;
    }
    if ((m = linea.match(reSubSeccion))) {
      if (!modulo) continue;
      seccion = seccion || 'General';
      const headers = splitFields(m[1]);
      resultado[modulo][seccion] = resultado[modulo][seccion] || { headers: [], rows: [] };
      resultado[modulo][seccion].headers = headers;
      headerActivo = headers;
      continue;
    }
    // fila de datos
    if (modulo && seccion && headerActivo) {
      resultado[modulo][seccion].rows.push(splitFields(linea));
    }
  }

  // limpia secciones vacías
  for (const mod in resultado) {
    for (const sec in resultado[mod]) {
      const d = resultado[mod][sec];
      if (!d.headers.length || !d.rows.length) delete resultado[mod][sec];
    }
    if (Object.keys(resultado[mod]).length === 0) delete resultado[mod];
  }
  return resultado;
}

// ---------------------------------------------------------------
// PASO 2: elegir módulo / sección
// ---------------------------------------------------------------
function poblarModulos() {
  const selModulo = document.getElementById('selModulo');
  selModulo.innerHTML = '<option value="">-- Selecciona --</option>' +
    Object.keys(arbol).map(m => `<option value="${escHtml(m)}">${escHtml(m)}</option>`).join('');
  document.getElementById('selSeccion').innerHTML = '<option value="">-- Selecciona un módulo primero --</option>';
}

document.getElementById('selModulo').addEventListener('change', e => {
  const mod = e.target.value;
  const selSeccion = document.getElementById('selSeccion');
  if (!mod) { selSeccion.innerHTML = '<option value="">-- Selecciona un módulo primero --</option>'; return; }
  const secciones = Object.keys(arbol[mod]);
  selSeccion.innerHTML = '<option value="">-- Selecciona --</option>' +
    secciones.map(s => `<option value="${escHtml(s)}">${escHtml(s)} (${arbol[mod][s].rows.length} filas)</option>`).join('');
});

document.getElementById('btnContinuar').addEventListener('click', () => {
  const mod = document.getElementById('selModulo').value;
  const sec = document.getElementById('selSeccion').value;
  if (!mod || !sec) return;
  moduloActual = mod; seccionActual = sec;
  columnasDisponibles = arbol[mod][sec].headers;

  document.getElementById('resumenSeccion').textContent =
    `Módulo: ${mod}  |  Sección: ${sec}  |  ${arbol[mod][sec].rows.length} filas  |  ${columnasDisponibles.length} columnas`;

  pintarChecklist('checkColumnas', columnasDisponibles, 'col');
  pintarChecklist('checkGroupBy', columnasDisponibles, 'grp');
  const selAggCol = document.getElementById('selAggCol');
  selAggCol.innerHTML = columnasDisponibles.map(c => `<option value="${escHtml(c)}">${escHtml(c)}</option>`).join('');

  document.getElementById('listaFiltros').innerHTML = '';

  // ¿Esta sección tiene cara de "sesiones colectivas"? (participantes + al menos un tema o tamizaje)
  const tieneParticipantes = detectarColumna(columnasDisponibles, ['PARTICIPANTES']);
  const tieneTemaOTamizaje = detectarColumna(columnasDisponibles, ['TEMA']) || detectarColumna(columnasDisponibles, ['TAMIZAJE', 'PRUEBAS']);
  document.getElementById('cajaResumenColectivo').classList.toggle('oculto', !(tieneParticipantes && tieneTemaOTamizaje));
  document.getElementById('msgResumenColectivo').innerHTML = '';
  document.getElementById('paso2').classList.add('oculto');
  document.getElementById('paso3').classList.remove('oculto');
  document.getElementById('msgPaso3').innerHTML = '';
  renderPreviewPaso3();
  actualizarStepperNav(3);
});

document.getElementById('btnVolver').addEventListener('click', () => {
  document.getElementById('paso3').classList.add('oculto');
  document.getElementById('paso2').classList.remove('oculto');
  document.getElementById('previewPaso3').innerHTML = '';
  actualizarBarraPreview();
  actualizarStepperNav(2);
});

function pintarChecklist(contenedorId, columnas, prefijo) {
  const cont = document.getElementById(contenedorId);
  cont.innerHTML = columnas.map((c, i) =>
    `<label><input type="checkbox" data-col="${escHtml(c)}" class="chk-${prefijo}"> ${escHtml(c)}</label>`
  ).join('');
}

document.getElementById('selAggregate').addEventListener('change', e => {
  document.getElementById('wrapAggCol').style.display = e.target.value === 'count' ? 'none' : 'block';
});

// ---------------------------------------------------------------
// Filtros dinámicos (genérico: se usa en el reporte de una sección
// y en ambos lados del cruce entre secciones)
// ---------------------------------------------------------------
let contadorFiltros = 0;

// Valores únicos no vacíos de una columna, dado un headers/rows específico.
function valoresUnicosDe(headers, rows, col, limite = 500) {
  const idx = headers.indexOf(col);
  if (idx === -1) return [];
  const set = new Set();
  for (const row of rows) {
    const v = String(row[idx] ?? '').trim();
    if (v) set.add(v);
  }
  return [...set].sort((a, b) => a.localeCompare(b, 'es')).slice(0, limite);
}

// Wrapper para la sección activa del reporte simple (paso 3).
function obtenerValoresUnicos(col, limite = 500) {
  if (!moduloActual || !seccionActual) return [];
  return valoresUnicosDe(columnasDisponibles, arbol[moduloActual][seccionActual].rows, col, limite);
}

// Crea una fila de filtro (columna / operador / valor[es]) dentro del contenedor dado.
// obtenerValoresFn(col) debe devolver el array de valores únicos para esa columna.
// alCambiar (opcional) se invoca cuando se quita la fila, ya que ese botón no
// dispara eventos "change"/"input" que la vista previa en tiempo real pueda escuchar.
function crearFilaFiltro(idContenedor, columnas, obtenerValoresFn, alCambiar) {
  contadorFiltros++;
  const dlId = 'dl-filtro-' + contadorFiltros;
  const div = document.createElement('div');
  div.className = 'fila-filtro';
  div.innerHTML = `
    <select class="filtro-col">${columnas.map(c => `<option value="${escHtml(c)}">${escHtml(c)}</option>`).join('')}</select>
    <select class="filtro-op" style="max-width:120px">
      <option value="=">=</option>
      <option value="!=">!=</option>
      <option value="contains">contiene</option>
      <option value=">">&gt; mayor</option>
      <option value="<">&lt; menor</option>
      <option value="between">entre</option>
      <option value="empty">vacío</option>
      <option value="not_empty">no vacío</option>
    </select>
    <input type="text" class="filtro-val" placeholder="valor" list="${dlId}" style="max-width:130px">
    <input type="text" class="filtro-val2 oculto" placeholder="y este" style="max-width:90px">
    <datalist id="${dlId}"></datalist>
    <button type="button" class="btn-link btn-quitar-filtro" style="color:#b3261e">✕</button>
  `;
  document.getElementById(idContenedor).appendChild(div);

  const refrescar = () => {
    const col = div.querySelector('.filtro-col').value;
    const datalist = div.querySelector('datalist');
    const valores = obtenerValoresFn(col);
    datalist.innerHTML = valores.map(v => `<option value="${escHtml(v)}"></option>`).join('');
    div.querySelector('.filtro-val').placeholder = valores.length ? `${valores.length} distintos` : 'valor';
  };
  refrescar();
  div.querySelector('.filtro-col').addEventListener('change', refrescar);
  div.querySelector('.filtro-op').addEventListener('change', e => {
    const esEntre = e.target.value === 'between';
    div.querySelector('.filtro-val2').classList.toggle('oculto', !esEntre);
    div.querySelector('.filtro-val').placeholder = esEntre ? 'desde' : div.querySelector('.filtro-val').placeholder;
  });
  div.querySelector('.btn-quitar-filtro').addEventListener('click', () => {
    div.remove();
    if (alCambiar) alCambiar();
  });
  return div;
}

// Lee las filas de filtro de un contenedor y devuelve [{columna, operador, valor, valor2}]
function leerFiltros(idContenedor) {
  return [...document.querySelectorAll('#' + idContenedor + ' .fila-filtro')].map(div => ({
    columna: div.querySelector('.filtro-col').value,
    operador: div.querySelector('.filtro-op').value,
    valor: div.querySelector('.filtro-val').value,
    valor2: div.querySelector('.filtro-val2').value,
  }));
}

// Aplica una lista de filtros a un array de objetos {columna: valor}. Devuelve
// {resultado, avisos} — avisos indica si algún filtro dejó la lista en 0.
function aplicarFiltros(records, filtros) {
  const normComparar = s => String(s).trim().toLowerCase();
  const avisos = [];
  let out = records;
  for (const f of filtros) {
    if (!f.columna) continue;
    const antes = out.length;
    out = out.filter(r => {
      const actual = r[f.columna] ?? '';
      switch (f.operador) {
        case '=': return normComparar(actual) === normComparar(f.valor);
        case '!=': return normComparar(actual) !== normComparar(f.valor);
        case 'contains': return normComparar(actual).includes(normComparar(f.valor));
        case '>': return !isNaN(parseFloat(actual)) && parseFloat(actual) > parseFloat(f.valor);
        case '<': return !isNaN(parseFloat(actual)) && parseFloat(actual) < parseFloat(f.valor);
        case 'between': {
          const n = parseFloat(actual), a = parseFloat(f.valor), b = parseFloat(f.valor2);
          return !isNaN(n) && !isNaN(a) && !isNaN(b) && n >= Math.min(a, b) && n <= Math.max(a, b);
        }
        case 'empty': return String(actual).trim() === '';
        case 'not_empty': return String(actual).trim() !== '';
        default: return true;
      }
    });
    if (out.length === 0 && antes > 0) {
      const detalleValor = f.operador === 'between' ? `${f.valor} y ${f.valor2}` : f.valor;
      avisos.push(`El filtro "${f.columna} ${f.operador} ${detalleValor}" dejó la lista en 0 filas (tenía ${antes}).`);
    }
  }
  return { resultado: out, avisos };
}

document.getElementById('btnAgregarFiltro').addEventListener('click', () => {
  crearFilaFiltro('listaFiltros', columnasDisponibles, obtenerValoresUnicos, renderPreviewPaso3);
  renderPreviewPaso3();
});

// ---------------------------------------------------------------
// PASO 3: calcular (compartido entre vista previa y generación de Excel)
// ---------------------------------------------------------------
function calcularReportePaso3() {
  const headers = arbol[moduloActual][seccionActual].headers;
  const rows = arbol[moduloActual][seccionActual].rows;

  // filas -> objetos {columna: valor}
  let records = rows.map(r => {
    const obj = {};
    headers.forEach((h, i) => obj[h] = r[i] !== undefined ? r[i] : '');
    return obj;
  });

  // aplicar filtros
  const filtros = leerFiltros('listaFiltros');
  const { resultado: recordsFiltrados, avisos: avisosFiltro } = aplicarFiltros(records, filtros);
  records = recordsFiltrados;

  // columnas elegidas para detalle
  const colsElegidas = [...document.querySelectorAll('.chk-col:checked')].map(c => c.dataset.col);
  const colsDetalle = colsElegidas.length ? colsElegidas : headers;

  // group by
  const groupBy = [...document.querySelectorAll('.chk-grp:checked')].map(c => c.dataset.col);
  const aggregate = document.getElementById('selAggregate').value;
  const aggCol = document.getElementById('selAggCol').value;

  let stats = [];
  if (groupBy.length) {
    const grupos = {};
    for (const r of records) {
      const key = groupBy.map(g => r[g] || '(vacío)').join(' | ');
      (grupos[key] = grupos[key] || []).push(r);
    }
    for (const key in grupos) {
      const filas = grupos[key];
      let valor;
      if (aggregate === 'count') valor = filas.length;
      else if (aggregate === 'sum') valor = filas.reduce((s, r) => s + (parseFloat(r[aggCol]) || 0), 0);
      else if (aggregate === 'avg') valor = filas.length ? filas.reduce((s, r) => s + (parseFloat(r[aggCol]) || 0), 0) / filas.length : 0;
      const partes = key.split(' | ');
      const fila = {};
      groupBy.forEach((g, i) => fila[g] = partes[i]);
      fila['valor'] = Math.round(valor * 100) / 100;
      stats.push(fila);
    }
    stats.sort((a, b) => b.valor - a.valor);
  }

  return { records, colsDetalle, stats, groupBy, aggregate, aggCol, filtros, avisosFiltro };
}

// Vista previa en tiempo real: se recalcula cada vez que el usuario cambia
// columnas, agrupación o filtros, para que vea ANTES de descargar qué va a salir.
function renderPreviewPaso3() {
  const cont = document.getElementById('previewPaso3');
  if (!cont) return;
  if (!moduloActual || !seccionActual) { cont.innerHTML = ''; actualizarBarraPreview(); return; }
  try {
    const { records, colsDetalle, stats } = calcularReportePaso3();
    let html = `<strong>Reporte simple — ${escHtml(seccionActual)}:</strong> se descargarían <b>${records.length}</b> fila(s) en el detalle, con <b>${colsDetalle.length}</b> columna(s)`;
    html += stats.length ? ` y <b>${stats.length}</b> grupo(s) en la hoja de estadísticas.` : '.';

    if (records.length) {
      const muestra = records.slice(0, 8);
      html += '<div style="overflow-x:auto;margin-top:8px"><table><tr>'
        + colsDetalle.map(c => `<th>${escHtml(c)}</th>`).join('') + '</tr>'
        + muestra.map(r => '<tr>' + colsDetalle.map(c => `<td>${escHtml(r[c] ?? '')}</td>`).join('') + '</tr>').join('')
        + '</table></div>';
      if (records.length > muestra.length) {
        html += `<div style="font-size:.75rem;color:#777;margin-top:4px">... y ${records.length - muestra.length} fila(s) más (se incluyen todas al descargar).</div>`;
      }
      html += renderAutoStatsHtml(calcularAutoStats(records, colsDetalle));
    } else {
      html += '<div class="error" style="margin-top:6px">⚠ Con esta configuración no quedaría ninguna fila.</div>';
    }
    cont.innerHTML = html;
  } catch (err) {
    cont.innerHTML = '';
  }
  actualizarBarraPreview();
}

function debounce(fn, ms) {
  let t;
  return (...args) => { clearTimeout(t); t = setTimeout(() => fn(...args), ms); };
}
const renderPreviewPaso3Deb = debounce(renderPreviewPaso3, 250);
document.getElementById('paso3').addEventListener('input', renderPreviewPaso3Deb);
document.getElementById('paso3').addEventListener('change', renderPreviewPaso3Deb);

// ---------------------------------------------------------------
// PASO 3: generar Excel
// ---------------------------------------------------------------
document.getElementById('btnGenerar').addEventListener('click', async () => {
  const msg = document.getElementById('msgPaso3');
  msg.innerHTML = '';
  const btn = document.getElementById('btnGenerar');
  btn.disabled = true;
  btn.innerHTML = '<span class="spinner"></span>Generando...';

  try {
    const { records, colsDetalle, stats, groupBy, aggregate, filtros, avisosFiltro } = calcularReportePaso3();

    if (records.length === 0 && filtros.length) {
      const ultimoFiltro = filtros[filtros.length - 1];
      const valoresDisponibles = obtenerValoresUnicos(ultimoFiltro.columna, 15);
      msg.innerHTML = '<div class="error">'
        + 'No quedó ningún registro después de aplicar los filtros.<br>'
        + avisosFiltro.map(a => '• ' + a).join('<br>')
        + (valoresDisponibles.length
            ? `<br><br>Valores que sí existen en "<b>${escHtml(ultimoFiltro.columna)}</b>": ${valoresDisponibles.map(escHtml).join(', ')}`
            : `<br><br>La columna "${escHtml(ultimoFiltro.columna)}" no tiene valores diligenciados en esta sección.`)
        + '<br><br>Tip: haz clic en el campo "valor" del filtro para ver el desplegable con los valores reales y evitar errores de escritura.'
        + '</div>';
      return;
    }

    await generarExcel(records, colsDetalle, stats, groupBy, aggregate);
    msg.innerHTML = '<div class="ok">✓ Excel generado y descargado (' + records.length + ' filas en detalle' + (stats.length ? ', ' + stats.length + ' grupos en estadísticas' : '') + ')</div>';
  } catch (err) {
    msg.innerHTML = '<div class="error">' + err.message + '</div>';
  } finally {
    btn.disabled = false;
    btn.textContent = 'Generar y descargar Excel';
  }
});

async function generarExcel(records, colsDetalle, stats, groupBy, aggregate) {
  const wb = new ExcelJS.Workbook();

  if (stats.length) {
    const wsStats = wb.addWorksheet('Estadisticas');
    const etiquetaValor = { count: 'Cantidad', sum: 'Suma', avg: 'Promedio' }[aggregate] || 'Valor';
    wsStats.columns = [...groupBy.map(g => ({ header: g, key: g, width: 22 })), { header: etiquetaValor, key: 'valor', width: 14 }];
    wsStats.getRow(1).font = { bold: true };
    wsStats.getRow(1).fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FFD9E1F2' } };
    stats.forEach(s => wsStats.addRow(s));
  }

  const wsDetalle = wb.addWorksheet('Detalle');
  wsDetalle.columns = colsDetalle.map(c => ({ header: c, key: c, width: Math.min(Math.max(c.length, 12), 35) }));
  wsDetalle.getRow(1).font = { bold: true };
  wsDetalle.getRow(1).fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FFD9E1F2' } };
  records.forEach(r => {
    const fila = {};
    colsDetalle.forEach(c => fila[c] = r[c] ?? '');
    wsDetalle.addRow(fila);
  });

  const buffer = await wb.xlsx.writeBuffer();
  const blob = new Blob([buffer], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
  const url = URL.createObjectURL(blob);
  const a = document.createElement('a');
  a.href = url;
  a.download = `reporte_${moduloActual.replace(/[^a-z0-9]+/gi, '_')}_${new Date().toISOString().slice(0,10)}.xlsx`;
  a.click();
  URL.revokeObjectURL(url);
}

// ---------------------------------------------------------------
// RESUMEN EJECUTIVO — Sesiones Colectivas
// ---------------------------------------------------------------

const normTxt = s => String(s).normalize('NFD').replace(/[\u0300-\u036f]/g, '').toUpperCase();

// Busca la primera columna cuyo nombre contenga alguno de los patrones.
// Devuelve el nombre real de la columna o null. (Para columnas ÚNICAS en la sección.)
function detectarColumna(headers, patrones) {
  for (const h of headers) {
    if (patrones.some(p => normTxt(h).includes(normTxt(p)))) return h;
  }
  return null;
}

// Devuelve TODOS los ÍNDICES de columnas que matchean. Necesario cuando el
// mismo nombre de columna se repite varias veces en la fila (ej. TEMA aparece
// 4 veces porque el formulario permite hasta 4 sesiones/contenidos por ficha).
function indicesColumna(headers, patrones) {
  const out = [];
  headers.forEach((h, i) => { if (patrones.some(p => normTxt(h).includes(normTxt(p)))) out.push(i); });
  return out;
}

function sumarColumna(records, col) {
  if (!col) return 0;
  return records.reduce((s, r) => s + (parseFloat(r[col]) || 0), 0);
}

// Busca, dentro del mismo módulo, una sección hermana cuyo nombre matchee
// alguno de los patrones (ej. "PERSONAS" o "INDIVIDUOS" para sacar canalizaciones).
function buscarSeccionHermana(modulo, patrones, excluir) {
  const secciones = arbol[modulo] || {};
  for (const nombreSec in secciones) {
    if (nombreSec === excluir) continue;
    if (patrones.some(p => normTxt(nombreSec).includes(normTxt(p)))) return nombreSec;
  }
  return null;
}

document.getElementById('btnResumenColectivo').addEventListener('click', async () => {
  const msg = document.getElementById('msgResumenColectivo');
  const btn = document.getElementById('btnResumenColectivo');
  msg.innerHTML = '';
  btn.disabled = true;
  const textoOriginal = btn.textContent;
  btn.innerHTML = '<span class="spinner"></span>Calculando...';

  try {
    const headers = arbol[moduloActual][seccionActual].headers;
    const rows = arbol[moduloActual][seccionActual].rows; // arrays crudos, preservan columnas duplicadas
    const records = rows.map(r => {
      const obj = {};
      headers.forEach((h, i) => obj[h] = r[i] !== undefined ? r[i] : '');
      return obj;
    });

    // --- 1. Número de sesiones ---
    // Si existe una columna "NÚMERO DE SESIÓN" (puede repetirse hasta 4 veces
    // por ficha), cuenta cuántas veces viene diligenciada. Si no existe ese
    // patrón (estructura de una sesión por fila), usa el conteo de filas.
    const idxNumSesion = indicesColumna(headers, ['NUMERO DE SESION']);
    let numeroSesiones;
    if (idxNumSesion.length) {
      numeroSesiones = 0;
      for (const row of rows) {
        for (const idx of idxNumSesion) {
          if (String(row[idx] ?? '').trim()) numeroSesiones++;
        }
      }
    } else {
      numeroSesiones = records.length;
    }

    // --- 2. Personas abordadas (participaciones) ---
    // Prioriza el campo agregado "...PARTICIPAN EN JORNADAS" sobre otros
    // campos de participantes (ej. "participantes en la última sesión", que es otra métrica).
    const colParticipantes = detectarColumna(headers, ['PERSONAS QUE PARTICIPAN EN JORNADAS', 'PARTICIPAN EN JORNADAS'])
      || detectarColumna(headers, ['NO. DE PARTICIPANTES', 'NUMERO DE PARTICIPANTES', 'PARTICIPANTES']);
    const numeroParticipantes = sumarColumna(records, colParticipantes);

    // --- 3. Tamizajes por tipo (VIH, Sífilis, Hepatitis B) ---
    const tiposTamizaje = [
      { nombre: 'VIH', colReal: detectarColumna(headers, ['PRUEBAS VIH']), colReactivas: detectarColumna(headers, ['PRUEBAS VIH REACTIVAS', 'VIH REACTIVAS']) },
      { nombre: 'Sífilis', colReal: detectarColumna(headers, ['PRUEBAS SIFILIS']), colReactivas: detectarColumna(headers, ['SIFILIS REACTIVAS']) },
      { nombre: 'Hepatitis B', colReal: detectarColumna(headers, ['PRUEBAS HEPATITIS B']), colReactivas: detectarColumna(headers, ['HEPATITIS B REACTIVAS']) },
    ].filter(t => t.colReal);
    // Si "colReal" y "colReactivas" resultaron ser la misma columna (porque el
    // patrón "PRUEBAS VIH" matcheó también "...VIH REACTIVAS"), busca la
    // columna de reactivas explícitamente después de esa posición.
    for (const t of tiposTamizaje) {
      if (t.colReal && t.colReal === t.colReactivas) {
        const idxReal = headers.indexOf(t.colReal);
        const idxReactivas = headers.findIndex((h, i) => i > idxReal && normTxt(h).includes('REACTIVA'));
        t.colReactivas = idxReactivas > -1 ? headers[idxReactivas] : null;
      }
    }

    const tamizajes = tiposTamizaje.map(t => ({
      tipo: t.nombre,
      realizadas: sumarColumna(records, t.colReal),
      reactivas: t.colReactivas ? sumarColumna(records, t.colReactivas) : 0,
    }));
    const totalTamizajes = tamizajes.reduce((s, t) => s + t.realizadas, 0);

    // --- 4. Preservativos ---
    const colPreservativos = detectarColumna(headers, ['PRESERVATIVOS ENTREGADOS', 'PRESERVATIVOS']);
    const numeroPreservativos = colPreservativos ? sumarColumna(records, colPreservativos) : null;

    // --- 5. Canalizaciones ---
    // En este tipo de módulo casi nunca vive en la misma sección: es un dato
    // por persona en la sección "Personas"/"Individuos" del mismo módulo.
    // Primero intenta en la sección actual (por si sí viene agregado ahí);
    // si no, busca automáticamente en la sección hermana.
    let numeroCanalizaciones = null;
    let fuenteCanalizaciones = null;
    const colCanalizacionesLocal = detectarColumna(headers, ['CANALIZACIONES REALIZADAS']);
    if (colCanalizacionesLocal) {
      numeroCanalizaciones = sumarColumna(records, colCanalizacionesLocal);
      fuenteCanalizaciones = seccionActual;
    } else {
      const secPersonas = buscarSeccionHermana(moduloActual, ['PERSONAS', 'INDIVIDUOS'], seccionActual);
      if (secPersonas) {
        const headersP = arbol[moduloActual][secPersonas].headers;
        const rowsP = arbol[moduloActual][secPersonas].rows;
        const colCanaliza = detectarColumna(headersP, ['CANALIZACION']);
        if (colCanaliza) {
          const idx = headersP.indexOf(colCanaliza);
          numeroCanalizaciones = rowsP.filter(r => {
            const v = normTxt(r[idx] ?? '').trim();
            return v && v !== 'NO' && v !== 'N/A' && v !== 'NINGUNA' && v !== '0';
          }).length;
          fuenteCanalizaciones = secPersonas + ' (columna "' + colCanaliza + '")';
        }
      }
    }

    // --- 6. Temas abordados (soporta columnas "TEMA" repetidas por índice) ---
    const idxTemas = indicesColumna(headers, ['TEMA']);
    const frecTemas = {};
    for (const row of rows) {
      for (const idx of idxTemas) {
        const v = String(row[idx] ?? '').trim();
        if (v) frecTemas[v] = (frecTemas[v] || 0) + 1;
      }
    }
    const temas = Object.entries(frecTemas).map(([tema, cantidad]) => ({ tema, cantidad })).sort((a, b) => b.cantidad - a.cantidad);

    // --- 7. Perfiles que intervinieron ---
    // Soporta dos formatos: (a) columnas "NOMBRE PROFESIONAL"/"NOMBRE TÉCNICO"/"NOMBRE DIGITADOR"
    // con nombres propios, o (b) columnas "PERFIL 1".."PERFIL 9" repetidas (rol/perfil, no nombre propio).
    let perfiles = [];
    const idxNombresPerfil = indicesColumna(headers, ['NOMBRE PROFESIONAL', 'NOMBRE TECNICO', 'NOMBRE DIGITADOR']);
    const idxPerfilNumerado = indicesColumna(headers, ['PERFIL 1', 'PERFIL 2', 'PERFIL 3', 'PERFIL 4', 'PERFIL 5', 'PERFIL 6', 'PERFIL 7', 'PERFIL 8', 'PERFIL 9']);

    if (idxNombresPerfil.length) {
      const frec = {};
      for (const row of rows) {
        for (const idx of idxNombresPerfil) {
          const nombre = String(row[idx] ?? '').trim();
          if (!nombre) continue;
          const h = normTxt(headers[idx]);
          const rol = h.includes('DIGITADOR') ? 'Digitador' : (h.includes('TECNIC') ? 'Técnico' : 'Profesional');
          const key = nombre + '||' + rol;
          frec[key] = (frec[key] || 0) + 1;
        }
      }
      perfiles = Object.entries(frec).map(([key, sesiones]) => { const [nombre, rol] = key.split('||'); return { nombre, rol, sesiones }; });
    } else if (idxPerfilNumerado.length) {
      const frec = {};
      for (const row of rows) {
        for (const idx of idxPerfilNumerado) {
          const v = String(row[idx] ?? '').trim();
          if (v) frec[v] = (frec[v] || 0) + 1;
        }
      }
      perfiles = Object.entries(frec).map(([nombre, sesiones]) => ({ nombre, rol: '', sesiones }));
    }
    perfiles.sort((a, b) => b.sesiones - a.sesiones);

    if (!idxTemas.length && !perfiles.length && !totalTamizajes) {
      msg.innerHTML = '<div class="error">No se detectaron suficientes columnas conocidas en esta sección para armar el resumen. Usa el modo personalizado abajo.</div>';
      return;
    }

    await generarExcelResumen({
      numeroSesiones, numeroParticipantes, tamizajes, totalTamizajes,
      numeroCanalizaciones, fuenteCanalizaciones, numeroPreservativos, temas, perfiles,
    });

    msg.innerHTML = '<div class="ok">✓ Resumen ejecutivo generado y descargado.'
      + (fuenteCanalizaciones ? ` Canalizaciones tomadas de: ${fuenteCanalizaciones}.` : (numeroCanalizaciones === null ? ' (No se encontró columna de canalizaciones en este módulo — revísala manualmente.)' : ''))
      + '</div>';
  } catch (err) {
    msg.innerHTML = '<div class="error">' + err.message + '</div>';
  } finally {
    btn.disabled = false;
    btn.textContent = textoOriginal;
  }
});

async function generarExcelResumen(d) {
  const wb = new ExcelJS.Workbook();
  const azul = 'FFD9E1F2';
  const estiloHeader = (row) => { row.font = { bold: true }; row.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: azul } }; };

  // Hoja 1: KPIs generales
  const wsKpi = wb.addWorksheet('Resumen KPIs');
  wsKpi.columns = [{ header: 'Indicador', key: 'ind', width: 40 }, { header: 'Valor', key: 'val', width: 16 }];
  estiloHeader(wsKpi.getRow(1));
  wsKpi.addRow({ ind: 'Número de sesiones', val: d.numeroSesiones });
  wsKpi.addRow({ ind: 'Número de personas abordadas (participaciones)', val: d.numeroParticipantes });
  wsKpi.addRow({ ind: 'Número total de tamizajes realizados', val: d.totalTamizajes });
  d.tamizajes.forEach(t => wsKpi.addRow({ ind: `   · Tamizaje ${t.tipo} (realizadas / reactivas)`, val: `${t.realizadas} / ${t.reactivas}` }));
  wsKpi.addRow({ ind: 'Número de canalizaciones' + (d.fuenteCanalizaciones ? ` (fuente: ${d.fuenteCanalizaciones})` : ''), val: d.numeroCanalizaciones === null ? 'No encontrado — revisar manualmente' : d.numeroCanalizaciones });
  if (d.numeroPreservativos !== null) wsKpi.addRow({ ind: 'Preservativos entregados', val: d.numeroPreservativos });
  wsKpi.addRow({ ind: 'Temas distintos abordados', val: d.temas.length });
  wsKpi.addRow({ ind: 'Perfiles distintos que intervinieron', val: d.perfiles.length });

  // Hoja 2: Temas abordados
  if (d.temas.length) {
    const wsTemas = wb.addWorksheet('Temas Abordados');
    wsTemas.columns = [{ header: 'Tema', key: 'tema', width: 60 }, { header: 'N° de sesiones', key: 'cantidad', width: 16 }];
    estiloHeader(wsTemas.getRow(1));
    d.temas.forEach(t => wsTemas.addRow(t));
  }

  // Hoja 3: Perfiles que intervinieron
  if (d.perfiles.length) {
    const wsPerfiles = wb.addWorksheet('Perfiles');
    wsPerfiles.columns = [{ header: 'Nombre', key: 'nombre', width: 32 }, { header: 'Rol', key: 'rol', width: 16 }, { header: 'N° de sesiones', key: 'sesiones', width: 16 }];
    estiloHeader(wsPerfiles.getRow(1));
    d.perfiles.forEach(p => wsPerfiles.addRow(p));
  }

  const buffer = await wb.xlsx.writeBuffer();
  const blob = new Blob([buffer], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
  const url = URL.createObjectURL(blob);
  const a = document.createElement('a');
  a.href = url;
  a.download = `resumen_sesiones_colectivas_${new Date().toISOString().slice(0,10)}.xlsx`;
  a.click();
  URL.revokeObjectURL(url);
}

// ---------------------------------------------------------------
// PASO 4: Cruce entre secciones
// ---------------------------------------------------------------
function poblarModulosCruce() {
  const opciones = '<option value="">-- Selecciona --</option>' +
    Object.keys(arbol).map(m => `<option value="${escHtml(m)}">${escHtml(m)}</option>`).join('');
  document.getElementById('selModuloA').innerHTML = opciones;
  document.getElementById('selModuloB').innerHTML = opciones;
  document.getElementById('selSeccionA').innerHTML = '<option value="">-- Selecciona un módulo primero --</option>';
  document.getElementById('selSeccionB').innerHTML = '<option value="">-- Selecciona un módulo primero --</option>';
  document.getElementById('cajaColumnasCruce').classList.add('oculto');
}

function ligarModuloSeccion(idModulo, idSeccion, idKey) {
  document.getElementById(idModulo).addEventListener('change', e => {
    const mod = e.target.value;
    const selSeccion = document.getElementById(idSeccion);
    document.getElementById('cajaColumnasCruce').classList.add('oculto');
    if (!mod) { selSeccion.innerHTML = '<option value="">-- Selecciona un módulo primero --</option>'; return; }
    const secciones = Object.keys(arbol[mod]);
    selSeccion.innerHTML = '<option value="">-- Selecciona --</option>' +
      secciones.map(s => `<option value="${escHtml(s)}">${escHtml(s)} (${arbol[mod][s].rows.length} filas)</option>`).join('');
  });
  document.getElementById(idSeccion).addEventListener('change', () => {
    document.getElementById('cajaColumnasCruce').classList.add('oculto');
    poblarSelectKey(idModulo, idSeccion, idKey);
  });
}
ligarModuloSeccion('selModuloA', 'selSeccionA', 'selKeyA');
ligarModuloSeccion('selModuloB', 'selSeccionB', 'selKeyB');

function poblarSelectKey(idModulo, idSeccion, idKey) {
  const mod = document.getElementById(idModulo).value;
  const sec = document.getElementById(idSeccion).value;
  const selKey = document.getElementById(idKey);
  if (!mod || !sec) { selKey.innerHTML = ''; return; }
  const headers = arbol[mod][sec].headers;
  selKey.innerHTML = headers.map(h => `<option value="${escHtml(h)}">${escHtml(h)}</option>`).join('');
  // Auto-preseleccionar si hay una columna que parezca Ficha_fic o documento
  const auto = detectarColumna(headers, ['FICHA_FIC', 'FICHA FIC']) || detectarColumna(headers, ['NUMERO DE DOCUMENTO', 'N°MERO DE DOCUMENTO', 'NUMERO DOCUMENTO', 'N DOCUMENTO']);
  if (auto) selKey.value = auto;
}

let contextoCruceA = null; // { headers, rows }
let contextoCruceB = null;

document.getElementById('btnCargarColumnasCruce').addEventListener('click', () => {
  const modA = document.getElementById('selModuloA').value, secA = document.getElementById('selSeccionA').value;
  const modB = document.getElementById('selModuloB').value, secB = document.getElementById('selSeccionB').value;
  if (!modA || !secA || !modB || !secB) { alert('Elige módulo y sección para ambos lados (A y B).'); return; }
  if (!document.getElementById('selKeyA').value || !document.getElementById('selKeyB').value) { alert('Elige la columna llave de ambos lados.'); return; }

  const headersA = arbol[modA][secA].headers;
  const headersB = arbol[modB][secB].headers;
  contextoCruceA = { headers: headersA, rows: arbol[modA][secA].rows };
  contextoCruceB = { headers: headersB, rows: arbol[modB][secB].rows };

  pintarChecklist('checkColsA', headersA, 'colA');
  pintarChecklist('checkColsB', headersB, 'colB');

  // reiniciar paneles de filtro de A y B con las columnas de la selección actual
  document.getElementById('filtrosCruceA').innerHTML = '';
  document.getElementById('filtrosCruceB').innerHTML = '';

  const opcionesCombinadas = ['<option value="">-- Sin agrupar --</option>']
    .concat(headersA.map(h => `<option value="A||${escHtml(h)}">A: ${escHtml(h)}</option>`))
    .concat(headersB.map(h => `<option value="B||${escHtml(h)}">B: ${escHtml(h)}</option>`))
    .join('');
  document.getElementById('selGroupByCruce').innerHTML = opcionesCombinadas;

  const opcionesNum = headersA.map(h => `<option value="A||${escHtml(h)}">A: ${escHtml(h)}</option>`)
    .concat(headersB.map(h => `<option value="B||${escHtml(h)}">B: ${escHtml(h)}</option>`)).join('');
  document.getElementById('selAggColCruce').innerHTML = opcionesNum;

  const opcionesDiff = ['<option value="">-- No usar este filtro --</option>'].concat(
    headersA.map(h => `<option value="A||${escHtml(h)}">A: ${escHtml(h)}</option>`)
      .concat(headersB.map(h => `<option value="B||${escHtml(h)}">B: ${escHtml(h)}</option>`))
  ).join('');
  document.getElementById('selDiffCol1').innerHTML = opcionesDiff;
  document.getElementById('selDiffCol2').innerHTML = opcionesDiff.replace('-- No usar este filtro --', '-- Selecciona --');

  document.getElementById('msgCruce').innerHTML = '';
  document.getElementById('cajaColumnasCruce').classList.remove('oculto');
  renderPreviewCruce();
});

document.getElementById('btnAgregarFiltroA').addEventListener('click', () => {
  if (!contextoCruceA) return;
  crearFilaFiltro('filtrosCruceA', contextoCruceA.headers, col => valoresUnicosDe(contextoCruceA.headers, contextoCruceA.rows, col), renderPreviewCruce);
  renderPreviewCruce();
});
document.getElementById('btnAgregarFiltroB').addEventListener('click', () => {
  if (!contextoCruceB) return;
  crearFilaFiltro('filtrosCruceB', contextoCruceB.headers, col => valoresUnicosDe(contextoCruceB.headers, contextoCruceB.rows, col), renderPreviewCruce);
  renderPreviewCruce();
});

document.getElementById('selAggregateCruce').addEventListener('change', e => {
  document.getElementById('wrapAggColCruce').style.display = e.target.value === 'count' ? 'none' : 'block';
});

// ---------------------------------------------------------------
// Cruce: calcular (compartido entre vista previa y generación de Excel)
// ---------------------------------------------------------------
function calcularCruce() {
  const modA = document.getElementById('selModuloA').value, secA = document.getElementById('selSeccionA').value;
  const modB = document.getElementById('selModuloB').value, secB = document.getElementById('selSeccionB').value;
  const keyA = document.getElementById('selKeyA').value, keyB = document.getElementById('selKeyB').value;
  const tipoCruce = document.getElementById('selTipoCruce').value;

  if (!modA || !secA || !modB || !secB || !keyA || !keyB) return null;

  const headersA = arbol[modA][secA].headers, rowsA = arbol[modA][secA].rows;
  const headersB = arbol[modB][secB].headers, rowsB = arbol[modB][secB].rows;

  const recordsA0 = rowsA.map(r => { const o = {}; headersA.forEach((h, i) => o[h] = r[i] ?? ''); return o; });
  const recordsB0 = rowsB.map(r => { const o = {}; headersB.forEach((h, i) => o[h] = r[i] ?? ''); return o; });

  // filtros de cada lado, ANTES de cruzar (ej. A: "UT DERIVADA A contiene alta",
  // B: "algún campo de Plan de Trabajo entre X y Y")
  const filtrosA = leerFiltros('filtrosCruceA');
  const filtrosB = leerFiltros('filtrosCruceB');
  const { resultado: recordsA, avisos: avisosA } = aplicarFiltros(recordsA0, filtrosA);
  const { resultado: recordsB, avisos: avisosB } = aplicarFiltros(recordsB0, filtrosB);

  if (recordsA.length === 0 || recordsB.length === 0) {
    return { error: true, recordsA, recordsB, avisosA, avisosB, recordsA0, recordsB0 };
  }

  const normKey = v => String(v).trim().toUpperCase();

  // índice de B (ya filtrado) por llave normalizada
  const indiceB = {};
  for (const rb of recordsB) {
    const k = normKey(rb[keyB]);
    if (!k) continue;
    (indiceB[k] = indiceB[k] || []).push(rb);
  }

  const colsA = [...document.querySelectorAll('.chk-colA:checked')].map(c => c.dataset.col);
  const colsB = [...document.querySelectorAll('.chk-colB:checked')].map(c => c.dataset.col);
  const colsAFinal = colsA.length ? colsA : headersA;
  const colsBFinal = colsB.length ? colsB : headersB;

  // Filtro por diferencia entre columnas (ej. Valoración final - Valoración
  // inicial >= 25%). Se evalúa con TODAS las columnas de ra/rb, sin importar
  // cuáles se marcaron para mostrar en el detalle.
  const parsePorcentaje = v => parseFloat(String(v ?? '').replace('%', '').replace(',', '.').trim());
  const diffCol1Sel = document.getElementById('selDiffCol1').value;
  const diffCol2Sel = document.getElementById('selDiffCol2').value;
  const diffOp = document.getElementById('selDiffOp').value;
  const diffValorTxt = document.getElementById('inputDiffValor').value;
  let evaluarDiff = null;
  if (diffCol1Sel && diffCol2Sel && diffValorTxt !== '') {
    const [lado1, col1] = diffCol1Sel.split('||');
    const [lado2, col2] = diffCol2Sel.split('||');
    const umbral = parsePorcentaje(diffValorTxt);
    evaluarDiff = (ra, rb) => {
      const v1 = parsePorcentaje(lado1 === 'A' ? ra[col1] : (rb ? rb[col1] : ''));
      const v2 = parsePorcentaje(lado2 === 'A' ? ra[col2] : (rb ? rb[col2] : ''));
      if (isNaN(v1) || isNaN(v2)) return false;
      const diferencia = v1 - v2;
      switch (diffOp) {
        case '>=': return diferencia >= umbral;
        case '<=': return diferencia <= umbral;
        case '>': return diferencia > umbral;
        case '<': return diferencia < umbral;
        case '=': return diferencia === umbral;
        default: return true;
      }
    };
  }
  let excluidasPorDiff = 0;

  let sinCoincidencia = 0;
  const merged = [];
  for (const ra of recordsA) {
    const k = normKey(ra[keyA]);
    const matches = indiceB[k] || [];
    if (matches.length === 0) {
      sinCoincidencia++;
      if (tipoCruce === 'inner') continue;
      if (evaluarDiff && !evaluarDiff(ra, null)) { excluidasPorDiff++; continue; }
      const fila = { 'Llave': ra[keyA] };
      colsAFinal.forEach(c => fila['A: ' + c] = ra[c]);
      colsBFinal.forEach(c => fila['B: ' + c] = '');
      merged.push(fila);
      continue;
    }
    for (const rb of matches) {
      if (evaluarDiff && !evaluarDiff(ra, rb)) { excluidasPorDiff++; continue; }
      const fila = { 'Llave': ra[keyA] };
      colsAFinal.forEach(c => fila['A: ' + c] = ra[c]);
      colsBFinal.forEach(c => fila['B: ' + c] = rb[c]);
      merged.push(fila);
    }
  }

  // estadística opcional
  const groupSel = document.getElementById('selGroupByCruce').value;
  const aggregate = document.getElementById('selAggregateCruce').value;
  const aggColSel = document.getElementById('selAggColCruce').value;
  let stats = [];
  if (groupSel) {
    const [lado, col] = groupSel.split('||');
    const colOut = (lado === 'A' ? 'A: ' : 'B: ') + col;
    let aggColOut = null;
    if (aggregate !== 'count' && aggColSel) {
      const [ladoAgg, colAgg] = aggColSel.split('||');
      aggColOut = (ladoAgg === 'A' ? 'A: ' : 'B: ') + colAgg;
    }
    const grupos = {};
    for (const m of merged) {
      const key = m[colOut] || '(vacío)';
      (grupos[key] = grupos[key] || []).push(m);
    }
    for (const key in grupos) {
      const filas = grupos[key];
      let valor;
      if (aggregate === 'count') valor = filas.length;
      else if (aggregate === 'sum') valor = filas.reduce((s, r) => s + (parseFloat(r[aggColOut]) || 0), 0);
      else valor = filas.length ? filas.reduce((s, r) => s + (parseFloat(r[aggColOut]) || 0), 0) / filas.length : 0;
      stats.push({ [colOut]: key, valor: Math.round(valor * 100) / 100 });
    }
    stats.sort((a, b) => b.valor - a.valor);
  }

  return { merged, stats, sinCoincidencia, tipoCruce, excluidasPorDiff, evaluarDiff, recordsA, recordsB, recordsA0, recordsB0, avisosA, avisosB };
}

// Vista previa en tiempo real del cruce: se recalcula con cada cambio de
// columnas, filtros o el filtro de diferencia, antes de generar el Excel.
function renderPreviewCruce() {
  const cont = document.getElementById('previewCruce');
  if (!cont) return;
  if (document.getElementById('cajaColumnasCruce').classList.contains('oculto')) {
    cont.innerHTML = '';
    actualizarBarraPreview();
    return;
  }
  try {
    const r = calcularCruce();
    if (!r) {
      cont.innerHTML = '<span style="color:#777">Completa módulo, sección y columna llave en ambos lados para ver la vista previa.</span>';
      return;
    }
    if (r.error) {
      cont.innerHTML = '<div class="error">' + (r.recordsA.length === 0
        ? 'Los filtros de Sección A dejarían la lista en 0 filas antes de cruzar.'
        : 'Los filtros de Sección B dejarían la lista en 0 filas antes de cruzar.') + '</div>';
      return;
    }

    let html = `<strong>Cruce entre secciones:</strong> resultaría en <b>${r.merged.length}</b> fila(s)`;
    html += ` · A: ${r.recordsA.length}/${r.recordsA0.length} filas tras filtrar · B: ${r.recordsB.length}/${r.recordsB0.length} filas tras filtrar`;
    html += ` · ${r.sinCoincidencia} sin coincidencia${r.tipoCruce === 'inner' ? ' (excluidas)' : ' (incluidas)'}`;
    if (r.evaluarDiff) html += ` · ${r.excluidasPorDiff} excluidas por filtro de diferencia`;
    if (r.stats.length) html += ` · ${r.stats.length} grupo(s) en estadísticas`;
    html += '.';

    if (r.merged.length) {
      const cols = Object.keys(r.merged[0]);
      const muestra = r.merged.slice(0, 8);
      html += '<div style="overflow-x:auto;margin-top:8px"><table><tr>'
        + cols.map(c => `<th>${escHtml(c)}</th>`).join('') + '</tr>'
        + muestra.map(m => '<tr>' + cols.map(c => `<td>${escHtml(m[c] ?? '')}</td>`).join('') + '</tr>').join('')
        + '</table></div>';
      if (r.merged.length > muestra.length) {
        html += `<div style="font-size:.75rem;color:#777;margin-top:4px">... y ${r.merged.length - muestra.length} fila(s) más (se incluyen todas al descargar).</div>`;
      }
      html += renderAutoStatsHtml(calcularAutoStats(r.merged, Object.keys(r.merged[0])));
    } else {
      html += '<div class="error" style="margin-top:6px">⚠ Con esta configuración no quedaría ninguna fila.</div>';
    }
    cont.innerHTML = html;
  } catch (err) {
    cont.innerHTML = '';
  } finally {
    actualizarBarraPreview();
  }
}

const renderPreviewCruceDeb = debounce(renderPreviewCruce, 250);
document.getElementById('paso4').addEventListener('input', renderPreviewCruceDeb);
document.getElementById('paso4').addEventListener('change', renderPreviewCruceDeb);

document.getElementById('btnGenerarCruce').addEventListener('click', async () => {
  const msg = document.getElementById('msgCruce');
  const btn = document.getElementById('btnGenerarCruce');
  msg.innerHTML = '';
  btn.disabled = true;
  const textoOriginal = btn.textContent;
  btn.innerHTML = '<span class="spinner"></span>Cruzando datos...';

  try {
    const r = calcularCruce();
    if (!r) {
      msg.innerHTML = '<div class="error">Completa módulo, sección y columna llave en ambos lados (A y B).</div>';
      return;
    }
    if (r.error) {
      if (r.recordsA.length === 0) {
        msg.innerHTML = '<div class="error">Los filtros de Sección A dejaron la lista en 0 filas antes de cruzar.<br>' + r.avisosA.join('<br>') + '</div>';
      } else {
        msg.innerHTML = '<div class="error">Los filtros de Sección B dejaron la lista en 0 filas antes de cruzar.<br>' + r.avisosB.join('<br>') + '</div>';
      }
      return;
    }

    await generarExcelCruce(r.merged, r.stats, r.sinCoincidencia, r.tipoCruce);
    msg.innerHTML = `<div class="ok">✓ Cruce generado: <b>${r.merged.length} filas resultantes</b>.`
      + ` Sección A: ${r.recordsA.length} filas tras filtrar (de ${r.recordsA0.length} originales).`
      + ` Sección B: ${r.recordsB.length} filas tras filtrar (de ${r.recordsB0.length} originales).`
      + (r.evaluarDiff ? ` ${r.excluidasPorDiff} filas excluidas por el filtro de diferencia.` : '')
      + ` ${r.sinCoincidencia} filas de A sin coincidencia en B${r.tipoCruce === 'inner' ? ' (excluidas)' : ' (incluidas con B vacío)'}.</div>`;
  } catch (err) {
    msg.innerHTML = '<div class="error">' + err.message + '</div>';
  } finally {
    btn.disabled = false;
    btn.textContent = textoOriginal;
  }
});

async function generarExcelCruce(merged, stats, sinCoincidencia, tipoCruce) {
  const wb = new ExcelJS.Workbook();
  const azul = 'FFD9E1F2';
  const estiloHeader = (row) => { row.font = { bold: true }; row.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: azul } }; };

  if (stats.length) {
    const wsStats = wb.addWorksheet('Estadisticas');
    const cols = Object.keys(stats[0]);
    wsStats.columns = cols.map(c => ({ header: c, key: c, width: c === 'valor' ? 14 : 28 }));
    estiloHeader(wsStats.getRow(1));
    stats.forEach(s => wsStats.addRow(s));
  }

  const wsDetalle = wb.addWorksheet('Cruce');
  if (merged.length) {
    const cols = Object.keys(merged[0]);
    wsDetalle.columns = cols.map(c => ({ header: c, key: c, width: Math.min(Math.max(c.length, 12), 32) }));
    estiloHeader(wsDetalle.getRow(1));
    merged.forEach(m => wsDetalle.addRow(m));
  }

  const wsNota = wb.addWorksheet('Notas');
  wsNota.getCell('A1').value = 'Tipo de cruce';
  wsNota.getCell('B1').value = tipoCruce === 'inner' ? 'Solo coincidencias (inner join)' : 'Todas las filas de A (left join)';
  wsNota.getCell('A2').value = 'Filas de A sin coincidencia en B';
  wsNota.getCell('B2').value = sinCoincidencia;
  wsNota.getColumn('A').width = 32;
  wsNota.getColumn('B').width = 40;

  const buffer = await wb.xlsx.writeBuffer();
  const blob = new Blob([buffer], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
  const url = URL.createObjectURL(blob);
  const a = document.createElement('a');
  a.href = url;
  a.download = `cruce_secciones_${new Date().toISOString().slice(0,10)}.xlsx`;
  a.click();
  URL.revokeObjectURL(url);
}

function escHtml(str) {
  return String(str).replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
}
</script>
</body>
</html>