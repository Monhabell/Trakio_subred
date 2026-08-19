@extends('layouts.adm.navigation')

@section('main')

<style>
  /* =========================================================================
     CONTROL DE GESTANTES — estilos con prefijo "ge-" para no chocar con
     Bootstrap / commom.css (ej: .row, .progress-bar, .badge, .btn, .card
     ya existen globalmente). Usa las variables de shell.css para mantenerse
     coherente con el resto del panel admin (home, actas, etc).
     ========================================================================= */
  .ge-page-desc{
    font-size:.83rem;color:var(--shell-muted);margin:-1.25rem 0 1.5rem;max-width:760px;line-height:1.5;
  }

  .ge-section{margin-bottom:1.5rem;}
  .ge-card-title{font-family:var(--shell-head);font-size:1rem;margin:0 0 .3rem;font-weight:700;color:var(--shell-text);}
  .ge-card-sub{color:var(--shell-muted);font-size:.78rem;margin-bottom:1rem;}

  /* ---------- Dropzone ---------- */
  .ge-dropzone{
    border:2px dashed var(--shell-border-strong);background:var(--shell-surface-2);
    border-radius:12px;padding:2rem;text-align:center;cursor:pointer;transition:.15s;
  }
  .ge-dropzone:hover, .ge-dropzone.ge-drag{border-color:var(--shell-red);background:var(--shell-red-dim);}
  .ge-dropzone-icon{font-size:26px;color:var(--shell-red);margin-bottom:.5rem;}
  .ge-dropzone strong{color:var(--shell-text);font-family:var(--shell-head);font-size:.95rem;display:block;}
  .ge-dropzone-hint{font-size:.72rem;margin-top:.4rem;color:var(--shell-muted);}
  #ge-fileInput{display:none;}

  .ge-filelist{margin-top:.75rem;font-size:.76rem;color:var(--shell-muted);}
  .ge-filelist .ge-fitem{
    display:flex;justify-content:space-between;align-items:center;gap:10px;
    padding:.5rem .7rem;border-radius:8px;background:var(--shell-surface-2);
    border:1px solid var(--shell-border);margin-top:.4rem;
  }
  .ge-filelist .ge-fitem .ge-fname{color:var(--shell-text);}
  .ge-filelist .ge-fitem .ge-fsize{color:var(--shell-muted-2);margin-left:.3rem;}
  .ge-filelist .ge-fitem .ge-fremove{cursor:pointer;color:var(--shell-red);flex-shrink:0;}

  .ge-row{display:flex;gap:10px;flex-wrap:wrap;align-items:center;}

  /* ---------- Progress / status ---------- */
  .ge-progress-wrap{height:6px;background:var(--shell-border);border-radius:4px;overflow:hidden;margin-top:.75rem;display:none;}
  .ge-progress-bar{height:100%;width:0%;background:var(--shell-red);transition:width .2s;}
  .ge-status{font-size:.76rem;color:var(--shell-muted);margin-top:.6rem;min-height:16px;}
  .ge-integrity-warning{
    margin-top:.9rem;padding:.75rem .9rem;border-radius:10px;
    background:rgba(245,166,35,.1);border:1px solid rgba(245,166,35,.3);color:#f5a623;font-size:.76rem;
  }
  .ge-integrity-warning ul{margin:0;padding-left:1.1rem;}
  .ge-integrity-warning p{margin:.4rem 0;}

  /* ---------- KPIs ---------- */
  .ge-kpis{display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));gap:1rem;}
  .ge-kpis .adm-kpi-value{font-size:1.9rem;}

  /* ---------- Filtros ---------- */
  .ge-filters{display:flex;gap:.9rem;flex-wrap:wrap;align-items:flex-end;margin-top:.4rem;}
  .ge-filters label{
    display:block;font-size:.66rem;color:var(--shell-muted);margin-bottom:.3rem;
    text-transform:uppercase;letter-spacing:.04em;font-family:var(--shell-mono);
  }
  .ge-filters select, .ge-filters input[type=text]{
    padding:.5rem .6rem;border-radius:6px;border:1px solid var(--shell-border-strong);
    background:var(--shell-surface-2);color:var(--shell-text);font-size:.8rem;min-width:170px;font-family:var(--shell-sans);
  }
  .ge-filters select:focus, .ge-filters input[type=text]:focus{outline:none;border-color:var(--shell-red);}

  /* ---------- Tabla ---------- */
  .ge-tablewrap{max-height:560px;overflow:auto;border-radius:8px;border:1px solid var(--shell-border);margin-top:.9rem;}
  table.ge-table{width:100%;border-collapse:collapse;font-size:.76rem;}
  table.ge-table thead th{
    position:sticky;top:0;background:var(--shell-surface-2);color:var(--shell-text);
    text-align:left;padding:.55rem .6rem;font-weight:700;white-space:nowrap;
    border-bottom:2px solid var(--shell-red);font-size:.68rem;
  }
  table.ge-table tbody td{padding:.45rem .6rem;border-bottom:1px solid var(--shell-border);white-space:nowrap;color:var(--shell-text);}
  table.ge-table tbody tr:hover{background:var(--shell-surface-2);}

  /* ---------- Badges de estado ---------- */
  .ge-badge{padding:3px 9px;border-radius:20px;font-size:11px;font-weight:700;letter-spacing:.2px;display:inline-block;margin:2px 3px 2px 0;}
  .ge-badge.ge-gestante{background:rgba(46,204,113,.15);color:#2ecc71;border:1px solid rgba(46,204,113,.3);}
  .ge-badge.ge-puerpera{background:rgba(245,166,35,.15);color:#f5a623;border:1px solid rgba(245,166,35,.3);}
  .ge-badge.ge-lactante{background:rgba(159,122,234,.15);color:#9f7aea;border:1px solid rgba(159,122,234,.3);}

  .ge-empty{text-align:center;padding:2.5rem 1.25rem;color:var(--shell-muted);font-size:.8rem;}

  .ge-footer-note{text-align:center;color:var(--shell-muted-2);font-size:.68rem;padding:1.25rem 0 0;font-family:var(--shell-mono);}
</style>

{{-- ── PAGE HEADER ── --}}
<div class="adm-page-header">
    <div>
        <span class="adm-page-tag">// Herramientas</span>
        <h1 class="adm-page-title">Control de Gestantes</h1>
    </div>
    <div class="adm-page-badge">Subred Norte</div>
</div>

<p class="ge-page-desc">
    Extracción automática de gestantes, puérperas y lactantes desde exportes GESI (.csv). Puedes cargar uno o varios
    archivos a la vez (LAB, COM, EDU, etc). El procesamiento es 100% local en tu navegador — los archivos no salen
    de tu equipo.
</p>

{{-- ── 1. CARGA DE ARCHIVOS ── --}}
<div class="adm-card ge-section">
    <h2 class="ge-card-title">1. Cargar archivos GESI (.csv)</h2>
    <div class="ge-card-sub">Puedes cargar uno o varios exportes a la vez (LAB, COM, EDU, etc). Los archivos no salen de tu navegador: todo el procesamiento es local.</div>

    <div class="ge-dropzone" id="ge-dropzone">
        <div class="ge-dropzone-icon"><i class="fa-solid fa-cloud-arrow-up"></i></div>
        <strong>Haz clic aquí o arrastra tus archivos .csv</strong>
        <div class="ge-dropzone-hint">Formato esperado: exporte GESI en UTF-16 (Entorno / Módulo / Sección)</div>
        <input type="file" id="ge-fileInput" multiple accept=".csv">
    </div>

    <div class="ge-filelist" id="ge-fileList"></div>
    <div class="ge-progress-wrap" id="ge-progressWrap"><div class="ge-progress-bar" id="ge-progressBar"></div></div>
    <div class="ge-status" id="ge-status"></div>

    <div class="ge-row" style="margin-top:14px;">
        <button class="adm-btn adm-btn-primary" id="ge-processBtn" disabled>
            <i class="fa-solid fa-play"></i> Procesar archivos
        </button>
        <button class="adm-btn adm-btn-ghost" id="ge-clearBtn">
            <i class="fa-solid fa-broom"></i> Limpiar
        </button>
    </div>
</div>

{{-- ── 2. RESUMEN ── --}}
<div class="adm-card ge-section" id="ge-resultsCard" style="display:none;">
    <h2 class="ge-card-title">2. Resumen</h2>
    <div class="ge-kpis" id="ge-kpis"></div>
</div>

{{-- ── 3. TABLA ── --}}
<div class="adm-card ge-section" id="ge-tableCard" style="display:none;">
    <h2 class="ge-card-title">3. Registros encontrados</h2>
    <div class="ge-card-sub">Filtra por mes, estado o localidad. Cada fila es un registro de control (una intervención en una fecha).</div>

    <div class="ge-filters">
        <div>
            <label>Mes</label>
            <select id="ge-fMes"><option value="">Todos</option></select>
        </div>
        <div>
            <label>Estado</label>
            <select id="ge-fEstado">
                <option value="">Todos</option>
                <option value="Gestante">Gestante</option>
                <option value="Puérpera">Puérpera</option>
                <option value="Lactante">Lactante</option>
            </select>
        </div>
        <div>
            <label>Localidad</label>
            <select id="ge-fLocalidad"><option value="">Todas</option></select>
        </div>
        <div>
            <label>Buscar nombre / documento</label>
            <input type="text" id="ge-fBusqueda" placeholder="Ej: Karen Peña, 1014217503 o 2341340 (Ficha_fic)">
        </div>
        <div>
            <button class="adm-btn adm-btn-primary" id="ge-exportBtn">
                <i class="fa-solid fa-file-excel"></i> Descargar Excel
            </button>
        </div>
    </div>

    <div class="ge-tablewrap">
        <table class="ge-table" id="ge-dataTable">
            <thead>
                <tr>
                    <th>Mes</th><th>Fecha</th><th>Estado</th><th>Etapa / Trimestre</th>
                    <th>Nombre completo</th><th>Tipo Doc.</th><th>Documento</th><th>Ficha_fic</th><th>Edad</th>
                    <th>Localidad</th><th>Semanas gest.</th><th>FPP</th><th>N° CPN</th>
                    <th>Exámenes lab. al día</th><th>Esquema vacunación</th><th>Condición crónica</th>
                    <th>Módulo / Fuente</th><th>Archivo</th><th>Usuario</th>
                </tr>
            </thead>
            <tbody id="ge-tbody"></tbody>
        </table>
    </div>
    <div class="ge-empty" id="ge-emptyMsg" style="display:none;">No hay registros con los filtros seleccionados.</div>
</div>

<div class="ge-footer-note">Herramienta de uso interno · Subred Norte · Procesamiento 100% local en el navegador</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/exceljs/4.4.0/exceljs.min.js"></script>
<script>
(function(){
"use strict";

/* ============================================================
   FILE HANDLING
   ============================================================ */
const dropzone = document.getElementById('ge-dropzone');
const fileInput = document.getElementById('ge-fileInput');
const fileListEl = document.getElementById('ge-fileList');
const processBtn = document.getElementById('ge-processBtn');
const clearBtn = document.getElementById('ge-clearBtn');
const statusEl = document.getElementById('ge-status');
const progressWrap = document.getElementById('ge-progressWrap');
const progressBar = document.getElementById('ge-progressBar');

let loadedFiles = []; // {name, file}

dropzone.addEventListener('click', () => fileInput.click());
dropzone.addEventListener('dragover', e => { e.preventDefault(); dropzone.classList.add('ge-drag'); });
dropzone.addEventListener('dragleave', () => { dropzone.classList.remove('ge-drag'); });
dropzone.addEventListener('drop', e => {
  e.preventDefault();
  dropzone.classList.remove('ge-drag');
  addFiles(e.dataTransfer.files);
});
fileInput.addEventListener('change', e => addFiles(e.target.files));

function addFiles(fileArr) {
  for (const f of fileArr) {
    if (!/\.csv$/i.test(f.name)) continue;
    if (loadedFiles.some(x => x.name === f.name)) continue;
    loadedFiles.push({ name: f.name, file: f });
  }
  renderFileList();
}

function renderFileList() {
  fileListEl.innerHTML = '';
  loadedFiles.forEach((f, idx) => {
    const div = document.createElement('div');
    div.className = 'ge-fitem';
    div.innerHTML = `<span class="ge-fname"><i class="fa-solid fa-file-csv"></i> ${f.name} <span class="ge-fsize">(${(f.file.size/1024/1024).toFixed(1)} MB)</span></span>
      <span class="ge-fremove" data-idx="${idx}"><i class="fa-solid fa-xmark"></i></span>`;
    fileListEl.appendChild(div);
  });
  fileListEl.querySelectorAll('[data-idx]').forEach(el => {
    el.addEventListener('click', () => {
      loadedFiles.splice(Number(el.dataset.idx), 1);
      renderFileList();
    });
  });
  processBtn.disabled = loadedFiles.length === 0;
}

clearBtn.addEventListener('click', () => {
  loadedFiles = [];
  renderFileList();
  statusEl.textContent = '';
  document.getElementById('ge-resultsCard').style.display = 'none';
  document.getElementById('ge-tableCard').style.display = 'none';
  const box = document.getElementById('ge-integrityWarning');
  if (box) box.remove();
});

/* ============================================================
   GESI PARSER
   Formato: bloques de "Módulo =>" / "Sección =>" seguidos de una
   fila de encabezados "Sub-Sección => DATOS DE LA FICHA ... "
   delimitada por backtick+punto y coma, o por tabulaciones,
   seguida de filas de datos hasta el siguiente encabezado.
   ============================================================ */
function parseRow(line, delim) {
  let s = line.replace(/\r$/, '');
  if (delim === 'BACKTICK') {
    if (s.startsWith('`')) s = s.slice(1);
    if (s.endsWith('`;')) s = s.slice(0, -2);
    else if (s.endsWith('`')) s = s.slice(0, -1);
    return s.split('`;`');
  }
  return s.split('\t');
}

function normHeader(h) {
  return (h || '').replace(/^Sub-Sección => /, '').trim();
}

function expandGluedHeaders(rawCols) {
  // GESI a veces omite la comilla invertida de cierre antes de un nuevo marcador
  // "Sub-Sección => ..." dentro de la fila de encabezados. Cuando eso pasa, esa
  // celda de encabezado queda con DOS columnas lógicas pegadas en un solo texto,
  // pero las filas de datos sí traen esos dos valores por separado. El resultado
  // es que, a partir de ahí, cada columna de datos queda corrida una posición
  // respecto a su encabezado (columnas mostrando el valor de la columna vecina).
  // Aquí separamos esa celda en dos para que encabezados y datos vuelvan a
  // corresponder 1 a 1.
  const expanded = [];
  for (let cell of rawCols) {
    while (true) {
      const idx = cell.indexOf(';Sub-Sección => ');
      if (idx === -1) break;
      let head = cell.slice(0, idx);
      if (head.endsWith('`')) head = head.slice(0, -1); // resto de la comilla invertida faltante
      expanded.push(head);
      cell = cell.slice(idx + 1); // conserva "Sub-Sección => ..." para la siguiente vuelta
    }
    expanded.push(cell);
  }
  return expanded;
}

function stripOuterQuote(line) {
  // Algunos exportes de GESI envuelven cada línea (encabezado y datos) entre
  // comillas dobles "..." además del formato con comillas invertidas. Si no se
  // quita, la línea completa no se reconoce como encabezado/fila de datos.
  if (line.length >= 2 && line[0] === '"' && line[line.length - 1] === '"') {
    return line.slice(1, -1);
  }
  return line;
}

function parseGesiText(text, fileLabel) {
  const lines = text.split(/\n/);
  const sections = [];
  let currentModulo = null, currentSeccion = null;
  let i = 0;
  while (i < lines.length) {
    const line = stripOuterQuote((lines[i] || '').replace(/\r$/, ''));
    if (line.startsWith('Módulo =>')) { currentModulo = line.replace('Módulo =>', '').trim(); i++; continue; }
    if (line.startsWith('Sección =>')) { currentSeccion = line.replace('Sección =>', '').trim(); i++; continue; }
    if (line.startsWith('Sub-Sección => DATOS DE LA FICHA')) {
      const delim = line.includes('`') ? 'BACKTICK' : 'TAB';
      const expandedRaw = expandGluedHeaders(parseRow(line, delim));
      // Antes de limpiar los prefijos "Sub-Sección => " (normHeader), guardamos en
      // qué posiciones arrancaba un bloque nuevo de persona (acudiente, responsable,
      // etc). Si no lo guardamos aquí, esa señal se pierde y no hay forma de saber
      // después dónde termina el bloque de la persona principal.
      const blockBoundaries = [];
      expandedRaw.forEach((cell, idx) => {
        if (idx > 7 && cell.startsWith('Sub-Sección => ')) blockBoundaries.push(idx);
      });
      const headers = expandedRaw.map(normHeader);
      headers._blockBoundaries = blockBoundaries;
      const rows = [];
      i++;
      while (i < lines.length) {
        const l = stripOuterQuote((lines[i] || '').replace(/\r$/, ''));
        if (l.startsWith('Módulo =>') || l.startsWith('Sección =>') || l.startsWith('Sub-Sección => DATOS DE LA FICHA')) break;
        if (l.trim() !== '') rows.push(parseRow(l, delim));
        i++;
      }
      sections.push({ file: fileLabel, modulo: currentModulo, seccion: currentSeccion, headers, rows });
      continue;
    }
    i++;
  }
  return sections;
}

/* ============================================================
   VALIDACIÓN DE INTEGRIDAD
   Si tras el ajuste anterior una sección sigue sin cuadrar (largo de
   encabezados distinto al de sus filas), no se puede confiar en la
   posición de sus columnas: se descarta esa sección para evitar
   mostrar un valor de una columna vecina como si fuera el correcto.
   ============================================================ */
function filterIntegrityIssues(sections) {
  const ok = [];
  const descartadas = [];
  for (const sec of sections) {
    const badRows = sec.rows.filter(r => r.length !== sec.headers.length).length;
    if (badRows > 0) {
      descartadas.push({ file: sec.file, modulo: sec.modulo, seccion: sec.seccion, badRows, total: sec.rows.length });
    } else {
      ok.push(sec);
    }
  }
  return { ok, descartadas };
}

/* ============================================================
   EXTRACCIÓN DE GESTANTES / PUÉRPERAS / LACTANTES
   ============================================================ */
const MONTHS_ES = ['enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre'];

function stripAccents(s) {
  return (s || '').normalize('NFD').replace(/[̀-ͯ]/g, '');
}
function colIndex(headers, exactNames) {
  for (const name of exactNames) {
    const target = stripAccents(name).toUpperCase();
    const idx = headers.findIndex(h => stripAccents(h).toUpperCase() === target);
    if (idx !== -1) return idx;
  }
  return -1;
}
function colIndicesMatching(headers, regex) {
  const out = [];
  headers.forEach((h, idx) => { if (regex.test(stripAccents(h))) out.push(idx); });
  return out;
}

function parseFechaToMes(fecha) {
  if (!fecha) return null;
  fecha = fecha.trim();
  let d = null;
  let m = fecha.match(/^(\d{4})-(\d{2})-(\d{2})/);
  if (m) d = new Date(Number(m[1]), Number(m[2]) - 1, Number(m[3]));
  else {
    m = fecha.match(/^(\d{1,2})\/(\d{1,2})\/(\d{4})/);
    if (m) d = new Date(Number(m[3]), Number(m[2]) - 1, Number(m[1]));
  }
  if (!d || isNaN(d.getTime())) return null;
  return { key: `${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}`, label: `${MONTHS_ES[d.getMonth()]} ${d.getFullYear()}`, date: d };
}

function estadoFromEtapa(v) {
  if (!v) return null;
  const s = v.toLowerCase();
  if (s.includes('trimestre')) return 'Gestante';
  if (s.includes('puerperio')) return 'Puérpera';
  if (s.includes('lactante')) return 'Lactante';
  return null;
}

function findPrimaryBlockEnd(headers) {
  // Los límites de bloque (dónde empieza "Información del Acudiente" u otra
  // persona secundaria) se calculan y se guardan en parseGesiText, antes de
  // limpiar los encabezados (normHeader ya les quitó el texto "Sub-Sección =>").
  // Cortamos ahí para no mezclar nombre/documento de la persona principal con
  // los de un acudiente/responsable u otra persona secundaria de la misma fila.
  const boundaries = headers._blockBoundaries;
  if (boundaries && boundaries.length) return boundaries[0];
  return headers.length;
}

function buildName(headers, row) {
  const blacklist = /ESTABLECIMIENTO|INSTITU|SEDE|EMPRESA|EPS|EAPB|IPS|ASEGURADORA|RESPONSABLE|ACUDIENTE/i;
  const end = findPrimaryBlockEnd(headers);
  const nameIdx = colIndicesMatching(headers, /NOMBRE/i).filter(i => i < end && !blacklist.test(headers[i]));
  const lastIdx = colIndicesMatching(headers, /APELLIDO/i).filter(i => i < end && !blacklist.test(headers[i]));
  const parts = [...nameIdx, ...lastIdx].map(i => (row[i] || '').trim()).filter(Boolean);
  return parts.join(' ').replace(/\s+/g, ' ').trim();
}

/* ------------------------------------------------------------
   Construye índices globales documento -> persona y
   Ficha_fic -> persona, recorriendo TODAS las secciones del/los
   archivo(s) cargado(s) (no solo las de gestantes). Esto permite
   completar datos cuando una sección de seguimiento solo trae
   Ficha_fic (sin nombre/documento), buscando esa misma ficha en
   otra sección del mismo módulo que sí tenga la identificación.
   ------------------------------------------------------------ */
function buildLookups(allSections) {
  const docLookup = {};   // documento -> {nombre, tipoDoc, edad, fnac, localidad}
  const fichaLookup = {}; // "modulo||Ficha_fic" -> {documento, nombre, ...} | 'AMBIGUO'

  for (const sec of allSections) {
    const idxDoc = colIndex(sec.headers, ['NÚMERO DE DOCUMENTO', 'NÚMERO DOCUMENTO']);
    const idxFicha = colIndex(sec.headers, ['Ficha_fic']);
    if (idxDoc === -1) continue;

    const idxTipoDoc = colIndex(sec.headers, ['TIPO DE DOCUMENTO']);
    const idxEdad = colIndex(sec.headers, ['EDAD']);
    const idxFnac = colIndex(sec.headers, ['FECHA DE NACIMIENTO']);
    const idxLoc = colIndex(sec.headers, ['Localidad_fic']);

    for (const row of sec.rows) {
      const documento = (row[idxDoc] || '').trim();
      if (!documento) continue;
      const nombre = buildName(sec.headers, row);
      if (!nombre) continue;

      const persona = {
        documento,
        nombre,
        tipoDoc: idxTipoDoc !== -1 ? (row[idxTipoDoc]||'').trim() : '',
        edad: idxEdad !== -1 ? (row[idxEdad]||'').trim() : '',
        fnac: idxFnac !== -1 ? (row[idxFnac]||'').trim() : '',
        localidad: idxLoc !== -1 ? (row[idxLoc]||'').trim() : ''
      };

      if (!docLookup[documento]) docLookup[documento] = persona;

      const ficha = idxFicha !== -1 ? (row[idxFicha]||'').trim() : '';
      if (!ficha) continue;
      // Ficha_fic se reutiliza como consecutivo independiente en cada Módulo, por lo
      // que solo es una llave confiable DENTRO del mismo módulo. Se combina con el
      // nombre del módulo para evitar cruces falsos entre módulos distintos.
      const fichaKey = `${sec.modulo || ''}||${ficha}`;
      const existing = fichaLookup[fichaKey];
      if (existing === undefined) {
        fichaLookup[fichaKey] = persona;
      } else if (existing !== 'AMBIGUO' && existing.documento !== documento) {
        // La misma Ficha_fic (dentro del mismo módulo) corresponde a varias personas
        // distintas (ej: sesión colectiva con varios asistentes) -> no usar como respaldo.
        fichaLookup[fichaKey] = 'AMBIGUO';
      }
    }
  }
  return { docLookup, fichaLookup };
}

function resolvePersona(docLookup, fichaLookup, documento, ficha, modulo, fallbackNombre) {
  let persona = documento && docLookup[documento] ? docLookup[documento] : null;
  const fichaKey = `${modulo || ''}||${ficha}`;
  if (!persona && ficha && fichaLookup[fichaKey] && fichaLookup[fichaKey] !== 'AMBIGUO') {
    persona = fichaLookup[fichaKey];
  }
  return {
    nombre: (fallbackNombre || (persona && persona.nombre) || ''),
    documento: documento || (persona && persona.documento) || '',
    tipoDoc: (persona && persona.tipoDoc) || '',
    edad: (persona && persona.edad) || '',
    localidad: (persona && persona.localidad) || ''
  };
}

function extractRecords(allSections) {
  const records = [];
  const { docLookup, fichaLookup } = buildLookups(allSections);

  // Pass 1: sections with ETAPA DE GESTACIÓN (persona-level)
  for (const sec of allSections) {
    const idxEtapa = colIndicesMatching(sec.headers, /^ETAPA DE GESTACI[ÓO]N$/i);
    if (idxEtapa.length === 0) continue;
    const idxDoc = colIndex(sec.headers, ['NÚMERO DE DOCUMENTO', 'NÚMERO DOCUMENTO']);
    const idxFicha = colIndex(sec.headers, ['Ficha_fic']);
    const idxEdad = colIndex(sec.headers, ['EDAD']);
    const idxFecha = colIndex(sec.headers, ['Fecha_intervencion']);
    const idxLoc = colIndex(sec.headers, ['Localidad_fic']);
    const idxUser = colIndex(sec.headers, ['Usuario']);
    const idxCronica = colIndex(sec.headers, ['CONDICIÓN CRÓNICA PREVIA AL EMBARAZO', 'CRÓNICAS']);

    for (const row of sec.rows) {
      const etapaVal = (row[idxEtapa[0]] || '').trim();
      const estado = estadoFromEtapa(etapaVal);
      if (!estado) continue;

      const ficha = idxFicha !== -1 ? (row[idxFicha]||'').trim() : '';
      const documentoDirecto = idxDoc !== -1 ? (row[idxDoc] || '').trim() : '';
      const nombreDirecto = buildName(sec.headers, row);
      const persona = resolvePersona(docLookup, fichaLookup, documentoDirecto, ficha, sec.modulo, nombreDirecto);

      const fechaRaw = idxFecha !== -1 ? (row[idxFecha]||'').trim() : '';
      const mesInfo = parseFechaToMes(fechaRaw);
      records.push({
        mesKey: mesInfo ? mesInfo.key : 'sin-fecha',
        mesLabel: mesInfo ? mesInfo.label : 'Sin fecha',
        fecha: fechaRaw,
        estado,
        etapa: etapaVal,
        nombre: persona.nombre,
        tipoDoc: persona.tipoDoc,
        documento: persona.documento,
        ficha,
        edad: (idxEdad !== -1 && row[idxEdad]) ? row[idxEdad].trim() : persona.edad,
        localidad: (idxLoc !== -1 && row[idxLoc]) ? row[idxLoc].trim() : persona.localidad,
        semanas: '',
        fpp: '',
        cpn: '',
        examenes: '',
        vacunacion: '',
        cronica: idxCronica !== -1 ? (row[idxCronica]||'').trim() : '',
        fuente: `${sec.modulo || ''} / ${sec.seccion || ''}`,
        archivo: sec.file,
        usuario: idxUser !== -1 ? (row[idxUser]||'').trim() : ''
      });
    }
  }

  // Pass 2: sections with SEGUIMIENTO de gestantes (SEMANAS DE GESTACIÓN / GESTANTES)
  for (const sec of allSections) {
    const idxSem = colIndex(sec.headers, ['SEMANAS DE GESTACIÓN']);
    const idxGestFlag = colIndex(sec.headers, ['GESTANTES']);
    if (idxSem === -1 && idxGestFlag === -1) continue;
    const idxDoc = colIndex(sec.headers, ['NÚMERO DE DOCUMENTO', 'NÚMERO DOCUMENTO']);
    const idxFicha = colIndex(sec.headers, ['Ficha_fic']);
    const idxFpp = colIndex(sec.headers, ['FPP - FECHA PROBABLE DE PARTO']);
    const idxCpn = colIndex(sec.headers, ['NÚMERO DE CPN - CONTROLES PRENATALES']);
    const idxExam = colIndex(sec.headers, ['EXÁMENES DE LABORATORIO AL DÍA']);
    const idxVac = colIndex(sec.headers, ['ESQUEMA DE VACUNACIÓN']);
    const idxCronica = colIndex(sec.headers, ['CONDICIÓN CRÓNICA PREVIA AL EMBARAZO']);
    const idxFecha = colIndex(sec.headers, ['Fecha_intervencion']);
    const idxLoc = colIndex(sec.headers, ['Localidad_fic']);
    const idxUser = colIndex(sec.headers, ['Usuario']);

    for (const row of sec.rows) {
      const semanas = idxSem !== -1 ? (row[idxSem]||'').trim() : '';
      const gestFlag = idxGestFlag !== -1 ? (row[idxGestFlag]||'').trim() : '';
      const fpp = idxFpp !== -1 ? (row[idxFpp]||'').trim() : '';
      const cpn = idxCpn !== -1 ? (row[idxCpn]||'').trim() : '';
      const hasSignal = semanas || (gestFlag && !/no aplica/i.test(gestFlag) && gestFlag !== '') || fpp || cpn;
      if (!hasSignal) continue;

      const ficha = idxFicha !== -1 ? (row[idxFicha]||'').trim() : '';
      const documentoDirecto = idxDoc !== -1 ? (row[idxDoc]||'').trim() : '';
      const persona = resolvePersona(docLookup, fichaLookup, documentoDirecto, ficha, sec.modulo, '');

      const fechaRaw = idxFecha !== -1 ? (row[idxFecha]||'').trim() : '';
      const mesInfo = parseFechaToMes(fechaRaw);
      records.push({
        mesKey: mesInfo ? mesInfo.key : 'sin-fecha',
        mesLabel: mesInfo ? mesInfo.label : 'Sin fecha',
        fecha: fechaRaw,
        estado: 'Gestante',
        etapa: gestFlag,
        nombre: persona.nombre || '(sin nombre en el exporte)',
        tipoDoc: persona.tipoDoc,
        documento: persona.documento,
        ficha,
        edad: persona.edad,
        localidad: (idxLoc !== -1 && row[idxLoc]) ? row[idxLoc].trim() : persona.localidad,
        semanas,
        fpp,
        cpn,
        examenes: idxExam !== -1 ? (row[idxExam]||'').trim() : '',
        vacunacion: idxVac !== -1 ? (row[idxVac]||'').trim() : '',
        cronica: idxCronica !== -1 ? (row[idxCronica]||'').trim() : '',
        fuente: `${sec.modulo || ''} / ${sec.seccion || ''}`,
        archivo: sec.file,
        usuario: idxUser !== -1 ? (row[idxUser]||'').trim() : ''
      });
    }
  }

  // Pass 3: cualquier OTRA columna en cualquier módulo que identifique directamente
  // a una persona como gestante (ej: "NIÑA O ADOLESCENTE TRABAJADORA GESTANTE",
  // "MUJER GESTANTE"), distinta de ETAPA DE GESTACIÓN (Pass 1) y GESTANTES/SEMANAS
  // DE GESTACIÓN de seguimiento (Pass 2). Se excluyen preguntas de encuesta/evaluación
  // (contienen "?", "¿" o empiezan con numeración tipo "15.") porque esas no
  // identifican al encuestado como gestante, sino que evalúan un conocimiento.
  const yaProcesado = new Set(['GESTANTES', 'ETAPA DE GESTACION']);
  for (const sec of allSections) {
    const candidatos = colIndicesMatching(sec.headers, /GESTANTE/i).filter(i => {
      const h = stripAccents(sec.headers[i]).toUpperCase();
      if (yaProcesado.has(h)) return false;
      if (/SEMANAS DE GESTACION/.test(h)) return false;
      if (/[?¿]/.test(sec.headers[i])) return false;
      if (/^\s*\d/.test(sec.headers[i])) return false;
      if (/ESPACIO|ADECUAD/.test(h)) return false;
      return true;
    });
    if (candidatos.length === 0) continue;

    const idxDoc = colIndex(sec.headers, ['NÚMERO DE DOCUMENTO', 'NÚMERO DOCUMENTO']);
    const idxFicha = colIndex(sec.headers, ['Ficha_fic']);
    const idxFecha = colIndex(sec.headers, ['Fecha_intervencion']);
    const idxLoc = colIndex(sec.headers, ['Localidad_fic']);
    const idxUser = colIndex(sec.headers, ['Usuario']);

    for (const row of sec.rows) {
      for (const idxCol of candidatos) {
        const val = (row[idxCol] || '').trim();
        if (!val) continue;
        if (/^no(\s|$)|no aplica/i.test(val)) continue; // "No", "No aplica"
        if (!/^s[íi]|^1(\s|-)/i.test(val)) continue;    // solo valores afirmativos ("Sí", "1-...")

        const ficha = idxFicha !== -1 ? (row[idxFicha]||'').trim() : '';
        const documentoDirecto = idxDoc !== -1 ? (row[idxDoc]||'').trim() : '';
        const nombreDirecto = buildName(sec.headers, row);
        const persona = resolvePersona(docLookup, fichaLookup, documentoDirecto, ficha, sec.modulo, nombreDirecto);

        const fechaRaw = idxFecha !== -1 ? (row[idxFecha]||'').trim() : '';
        const mesInfo = parseFechaToMes(fechaRaw);
        records.push({
          mesKey: mesInfo ? mesInfo.key : 'sin-fecha',
          mesLabel: mesInfo ? mesInfo.label : 'Sin fecha',
          fecha: fechaRaw,
          estado: 'Gestante',
          etapa: `${sec.headers[idxCol]}: ${val}`,
          nombre: persona.nombre || '(sin nombre en el exporte)',
          tipoDoc: persona.tipoDoc,
          documento: persona.documento,
          ficha,
          edad: persona.edad,
          localidad: (idxLoc !== -1 && row[idxLoc]) ? row[idxLoc].trim() : persona.localidad,
          semanas: '', fpp: '', cpn: '', examenes: '', vacunacion: '', cronica: '',
          fuente: `${sec.modulo || ''} / ${sec.seccion || ''}`,
          archivo: sec.file,
          usuario: idxUser !== -1 ? (row[idxUser]||'').trim() : ''
        });
      }
    }
  }

  // Unificar: si la misma persona (documento o ficha) tiene más de un registro
  // en la misma fecha (por ejemplo: identificada como gestante en un módulo y
  // como puérpera en otro, el mismo día), se deja UNA sola fila que junta
  // todos los estados/hallazgos encontrados en vez de mostrarla repetida.
  const groupMap = new Map();
  for (const r of records) {
    const idParte = r.documento || r.ficha || `${r.nombre}|${r.fuente}`;
    const key = `${idParte}|${r.fecha}`;
    if (!groupMap.has(key)) groupMap.set(key, []);
    groupMap.get(key).push(r);
  }

  const firstNonEmpty = (list, field) => {
    const withValue = list.find(x => x[field] && x[field] !== '(sin nombre en el exporte)');
    if (withValue) return withValue[field];
    const any = list.find(x => x[field]);
    return any ? any[field] : '';
  };
  const uniqueJoined = (list, field, sep) => {
    const vals = [...new Set(list.map(x => x[field]).filter(Boolean))];
    return vals.join(sep);
  };

  const merged = [...groupMap.values()].map(group => {
    if (group.length === 1) return group[0];
    const base = group[0];
    return {
      ...base,
      estado: uniqueJoined(group, 'estado', ' + '),
      etapa: uniqueJoined(group, 'etapa', '  |  '),
      nombre: firstNonEmpty(group, 'nombre'),
      tipoDoc: firstNonEmpty(group, 'tipoDoc'),
      documento: firstNonEmpty(group, 'documento'),
      ficha: firstNonEmpty(group, 'ficha'),
      edad: firstNonEmpty(group, 'edad'),
      localidad: firstNonEmpty(group, 'localidad'),
      semanas: firstNonEmpty(group, 'semanas'),
      fpp: firstNonEmpty(group, 'fpp'),
      cpn: firstNonEmpty(group, 'cpn'),
      examenes: firstNonEmpty(group, 'examenes'),
      vacunacion: firstNonEmpty(group, 'vacunacion'),
      cronica: firstNonEmpty(group, 'cronica'),
      fuente: uniqueJoined(group, 'fuente', '  +  '),
      archivo: uniqueJoined(group, 'archivo', ', '),
      usuario: uniqueJoined(group, 'usuario', ', ')
    };
  });

  merged.sort((a, b) => (a.mesKey < b.mesKey ? -1 : a.mesKey > b.mesKey ? 1 : 0));
  return merged;
}

/* ============================================================
   PIPELINE PRINCIPAL
   ============================================================ */
let allRecords = [];

processBtn.addEventListener('click', async () => {
  processBtn.disabled = true;
  progressWrap.style.display = 'block';
  let allSections = [];
  for (let i = 0; i < loadedFiles.length; i++) {
    const { name, file } = loadedFiles[i];
    statusEl.textContent = `Leyendo ${name}...`;
    progressBar.style.width = `${Math.round((i / loadedFiles.length) * 60)}%`;
    const buf = await file.arrayBuffer();
    let text;
    try {
      text = new TextDecoder('utf-16le').decode(buf).replace(/^﻿/, '');
    } catch (e) {
      text = new TextDecoder('utf-8').decode(buf);
    }
    const sections = parseGesiText(text, name);
    allSections = allSections.concat(sections);
  }
  statusEl.textContent = 'Extrayendo registros de gestantes, puérperas y lactantes...';
  progressBar.style.width = '85%';
  await new Promise(r => setTimeout(r, 30));

  const { ok: seccionesValidas, descartadas } = filterIntegrityIssues(allSections);
  allRecords = extractRecords(seccionesValidas);
  progressBar.style.width = '100%';

  let msg = `Listo: ${allRecords.length} registros encontrados en ${loadedFiles.length} archivo(s).`;
  if (descartadas.length > 0) {
    msg += ` ⚠ Se omitieron ${descartadas.length} sección(es) con desalineación en el exporte (ver detalle abajo) para evitar mostrar datos incorrectos.`;
  }
  statusEl.innerHTML = msg;
  renderIntegrityWarning(descartadas);
  setTimeout(() => { progressWrap.style.display = 'none'; progressBar.style.width='0%'; }, 600);
  processBtn.disabled = false;
  renderResults();
});

function renderIntegrityWarning(descartadas) {
  let box = document.getElementById('ge-integrityWarning');
  if (!descartadas || descartadas.length === 0) {
    if (box) box.remove();
    return;
  }
  if (!box) {
    box = document.createElement('div');
    box.id = 'ge-integrityWarning';
    box.className = 'ge-integrity-warning';
    statusEl.parentNode.insertBefore(box, statusEl.nextSibling);
  }
  const items = descartadas.map(d =>
    `<li><strong>${d.modulo || ''} / ${d.seccion || ''}</strong> (${d.file}) — ${d.badRows} de ${d.total} filas no cuadraban con sus columnas.</li>`
  ).join('');
  box.innerHTML = `<strong>⚠ Secciones omitidas por desalineación en el exporte de GESI:</strong>
    <p>Estas secciones no se incluyeron en el resultado porque el archivo trae más (o menos) valores por fila de los que hay columnas, y no se puede garantizar qué valor corresponde a qué columna. Vuelve a exportar esas fichas desde GESI si necesitas esos datos, o avísanos si se repite seguido.</p>
    <ul>${items}</ul>`;
}

/* ============================================================
   RENDER
   ============================================================ */
function renderResults() {
  document.getElementById('ge-resultsCard').style.display = 'block';
  document.getElementById('ge-tableCard').style.display = 'block';

  const totalGestantes = allRecords.filter(r => r.estado.includes('Gestante')).length;
  const totalPuerperas = allRecords.filter(r => r.estado.includes('Puérpera')).length;
  const totalLactantes = allRecords.filter(r => r.estado.includes('Lactante')).length;
  const meses = new Set(allRecords.map(r => r.mesKey)).size;
  const conSemanas = allRecords.filter(r => r.semanas).length;

  document.getElementById('ge-kpis').innerHTML = `
    <div class="adm-kpi"><div class="adm-kpi-value">${totalGestantes}</div><div class="adm-kpi-label">Registros gestantes</div></div>
    <div class="adm-kpi"><div class="adm-kpi-value">${totalPuerperas}</div><div class="adm-kpi-label">Puérperas</div></div>
    <div class="adm-kpi"><div class="adm-kpi-value">${totalLactantes}</div><div class="adm-kpi-label">Lactantes</div></div>
    <div class="adm-kpi"><div class="adm-kpi-value">${meses}</div><div class="adm-kpi-label">Meses cubiertos</div></div>
    <div class="adm-kpi"><div class="adm-kpi-value">${conSemanas}</div><div class="adm-kpi-label">Con semanas de gestación registradas</div></div>
  `;

  // Filtros dinámicos
  const fMes = document.getElementById('ge-fMes');
  const fLoc = document.getElementById('ge-fLocalidad');
  const mesesUnicos = [...new Map(allRecords.map(r => [r.mesKey, r.mesLabel])).entries()].sort();
  fMes.innerHTML = '<option value="">Todos</option>' + mesesUnicos.map(([k,l]) => `<option value="${k}">${l}</option>`).join('');
  const locsUnicas = [...new Set(allRecords.map(r => r.localidad).filter(Boolean))].sort();
  fLoc.innerHTML = '<option value="">Todas</option>' + locsUnicas.map(l => `<option value="${l}">${l}</option>`).join('');

  applyFilters();
}

function badgeClass(estado) {
  if (estado === 'Gestante') return 'ge-gestante';
  if (estado === 'Puérpera') return 'ge-puerpera';
  return 'ge-lactante';
}
function renderBadges(estadoStr) {
  return (estadoStr || '').split(' + ').filter(Boolean)
    .map(e => `<span class="ge-badge ${badgeClass(e)}">${e}</span>`)
    .join(' ');
}

function applyFilters() {
  const mes = document.getElementById('ge-fMes').value;
  const estado = document.getElementById('ge-fEstado').value;
  const loc = document.getElementById('ge-fLocalidad').value;
  const busqueda = document.getElementById('ge-fBusqueda').value.trim().toLowerCase();

  const filtered = allRecords.filter(r => {
    if (mes && r.mesKey !== mes) return false;
    if (estado && !r.estado.includes(estado)) return false;
    if (loc && r.localidad !== loc) return false;
    if (busqueda && !(r.nombre.toLowerCase().includes(busqueda) || r.documento.includes(busqueda) || (r.ficha||'').includes(busqueda))) return false;
    return true;
  });

  const tbody = document.getElementById('ge-tbody');
  const emptyMsg = document.getElementById('ge-emptyMsg');
  if (filtered.length === 0) {
    tbody.innerHTML = '';
    emptyMsg.style.display = 'block';
  } else {
    emptyMsg.style.display = 'none';
    tbody.innerHTML = filtered.map(r => `
      <tr>
        <td>${r.mesLabel}</td>
        <td>${r.fecha || '—'}</td>
        <td>${renderBadges(r.estado)}</td>
        <td>${r.etapa || '—'}</td>
        <td>${r.nombre || '—'}</td>
        <td>${r.tipoDoc || '—'}</td>
        <td>${r.documento || '—'}</td>
        <td>${r.ficha || '—'}</td>
        <td>${r.edad || '—'}</td>
        <td>${r.localidad || '—'}</td>
        <td>${r.semanas || '—'}</td>
        <td>${r.fpp || '—'}</td>
        <td>${r.cpn || '—'}</td>
        <td>${r.examenes || '—'}</td>
        <td>${r.vacunacion || '—'}</td>
        <td>${r.cronica || '—'}</td>
        <td>${r.fuente || '—'}</td>
        <td>${r.archivo || '—'}</td>
        <td>${r.usuario || '—'}</td>
      </tr>
    `).join('');
  }
  window._filteredRecords = filtered;
}

['ge-fMes','ge-fEstado','ge-fLocalidad','ge-fBusqueda'].forEach(id => {
  document.getElementById(id).addEventListener('input', applyFilters);
  document.getElementById(id).addEventListener('change', applyFilters);
});

/* ============================================================
   EXPORTAR A EXCEL (con diseño) usando ExcelJS
   ============================================================ */
const BRAND = {
  headerFill: 'FF123A6B',      // azul oscuro institucional
  headerFont: 'FFFFFFFF',
  bandFill: 'FFE7F0FB',        // azul muy claro (fila par)
  bandFillAlt: 'FFFFFFFF',     // blanco (fila impar)
  border: 'FFDBE6F2',
  titleFill: 'FF0B2447',
  kpiFill: 'FFF4F8FD',
  gestante: 'FFD7F2E3',
  gestanteText: 'FF14683F',
  puerpera: 'FFFBE8D2',
  puerperaText: 'FF8A5A0C',
  lactante: 'FFE6DEFB',
  lactanteText: 'FF4A2D9C'
};

const COLUMNS = [
  { header: 'Mes', key: 'mesLabel', width: 16 },
  { header: 'Fecha', key: 'fecha', width: 13 },
  { header: 'Estado', key: 'estado', width: 12 },
  { header: 'Etapa / Trimestre', key: 'etapa', width: 32 },
  { header: 'Nombre completo', key: 'nombre', width: 30 },
  { header: 'Tipo Documento', key: 'tipoDoc', width: 14 },
  { header: 'Documento', key: 'documento', width: 16 },
  { header: 'Ficha_fic', key: 'ficha', width: 14 },
  { header: 'Edad', key: 'edad', width: 8 },
  { header: 'Localidad', key: 'localidad', width: 16 },
  { header: 'Semanas de gestación', key: 'semanas', width: 16 },
  { header: 'FPP - Fecha probable de parto', key: 'fpp', width: 18 },
  { header: 'N° CPN', key: 'cpn', width: 10 },
  { header: 'Exámenes de laboratorio al día', key: 'examenes', width: 20 },
  { header: 'Esquema de vacunación', key: 'vacunacion', width: 20 },
  { header: 'Condición crónica previa', key: 'cronica', width: 22 },
  { header: 'Módulo / Sección', key: 'fuente', width: 40 },
  { header: 'Archivo origen', key: 'archivo', width: 26 },
  { header: 'Usuario', key: 'usuario', width: 14 }
];

function styleHeaderRow(row) {
  row.eachCell(cell => {
    cell.font = { bold: true, color: { argb: BRAND.headerFont }, size: 11 };
    cell.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: BRAND.headerFill } };
    cell.alignment = { vertical: 'middle', horizontal: 'left', wrapText: true };
    cell.border = {
      top: { style: 'thin', color: { argb: BRAND.border } },
      bottom: { style: 'thin', color: { argb: BRAND.border } }
    };
  });
  row.height = 24;
}

function estadoColors(estado) {
  const partes = (estado || '').split(' + ').filter(Boolean);
  if (partes.length > 1) return { fill: 'FFEDEDED', text: 'FF444444' }; // varios estados a la vez -> neutro
  if (estado === 'Gestante') return { fill: BRAND.gestante, text: BRAND.gestanteText };
  if (estado === 'Puérpera') return { fill: BRAND.puerpera, text: BRAND.puerperaText };
  return { fill: BRAND.lactante, text: BRAND.lactanteText };
}

function addDataSheet(workbook, name, rows) {
  const sheet = workbook.addWorksheet(name.slice(0, 31), {
    views: [{ state: 'frozen', ySplit: 1 }]
  });
  sheet.columns = COLUMNS;
  styleHeaderRow(sheet.getRow(1));

  rows.forEach((r, i) => {
    const excelRow = sheet.addRow(r);
    const band = i % 2 === 0 ? BRAND.bandFill : BRAND.bandFillAlt;
    excelRow.eachCell({ includeEmpty: true }, cell => {
      cell.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: band } };
      cell.border = {
        top: { style: 'hair', color: { argb: BRAND.border } },
        bottom: { style: 'hair', color: { argb: BRAND.border } }
      };
      cell.alignment = { vertical: 'middle' };
      cell.font = { size: 10.5 };
    });
    const estadoCell = excelRow.getCell('estado');
    const colors = estadoColors(r.estado);
    estadoCell.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: colors.fill } };
    estadoCell.font = { bold: true, size: 10.5, color: { argb: colors.text } };
    estadoCell.alignment = { vertical: 'middle', horizontal: 'center' };
  });

  sheet.autoFilter = { from: 'A1', to: `${sheet.getColumn(COLUMNS.length).letter}1` };
  sheet.getColumn('fecha').alignment = { vertical: 'middle', horizontal: 'center' };
  sheet.getColumn('edad').alignment = { vertical: 'middle', horizontal: 'center' };
  return sheet;
}

function addSummarySheet(workbook, data) {
  const sheet = workbook.addWorksheet('Resumen', { views: [{ showGridLines: false }] });
  sheet.getColumn(1).width = 3;
  sheet.getColumn(2).width = 34;
  sheet.getColumn(3).width = 18;
  sheet.getColumn(4).width = 3;
  sheet.getColumn(5).width = 34;
  sheet.getColumn(6).width = 18;

  sheet.mergeCells('B2:F2');
  const title = sheet.getCell('B2');
  title.value = 'Control de Gestantes · Subred Norte';
  title.font = { bold: true, size: 16, color: { argb: 'FFFFFFFF' } };
  title.alignment = { vertical: 'middle', horizontal: 'left' };
  sheet.getRow(2).height = 30;
  for (let c = 2; c <= 6; c++) {
    sheet.getRow(2).getCell(c).fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: BRAND.titleFill } };
  }

  const now = new Date();
  const fechaTxt = now.toLocaleDateString('es-CO', { day: '2-digit', month: 'long', year: 'numeric' });
  sheet.mergeCells('B3:F3');
  const sub = sheet.getCell('B3');
  sub.value = `Reporte generado el ${fechaTxt} · ${data.length} registros`;
  sub.font = { italic: true, size: 10.5, color: { argb: 'FF5A6B7D' } };
  sheet.getRow(3).height = 18;

  const totalGestantes = data.filter(r => r.estado.includes('Gestante')).length;
  const totalPuerperas = data.filter(r => r.estado.includes('Puérpera')).length;
  const totalLactantes = data.filter(r => r.estado.includes('Lactante')).length;
  const conSemanas = data.filter(r => r.semanas).length;
  const meses = [...new Set(data.map(r => r.mesLabel))].length;

  const kpis = [
    ['Registros Gestante', totalGestantes, BRAND.gestante, BRAND.gestanteText],
    ['Registros Puérpera', totalPuerperas, BRAND.puerpera, BRAND.puerperaText],
    ['Registros Lactante', totalLactantes, BRAND.lactante, BRAND.lactanteText],
    ['Meses cubiertos', meses, BRAND.kpiFill, BRAND.headerFill],
    ['Con semanas de gestación registradas', conSemanas, BRAND.kpiFill, BRAND.headerFill]
  ];
  let r0 = 5;
  kpis.forEach(([label, value, fill, textColor]) => {
    const lbl = sheet.getCell(`B${r0}`);
    lbl.value = label;
    lbl.font = { size: 10.5, color: { argb: 'FF132436' } };
    lbl.alignment = { vertical: 'middle' };
    lbl.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: fill } };
    const val = sheet.getCell(`C${r0}`);
    val.value = value;
    val.font = { bold: true, size: 13, color: { argb: textColor } };
    val.alignment = { vertical: 'middle', horizontal: 'right' };
    val.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: fill } };
    sheet.getRow(r0).height = 22;
    r0++;
  });

  // Distribución por localidad
  const porLocalidad = {};
  data.forEach(r => { const l = r.localidad || 'Sin localidad'; porLocalidad[l] = (porLocalidad[l]||0)+1; });
  const locEntries = Object.entries(porLocalidad).sort((a,b) => b[1]-a[1]);

  const headLoc = sheet.getCell('E5');
  headLoc.value = 'Registros por localidad';
  headLoc.font = { bold: true, size: 11.5, color: { argb: BRAND.headerFill } };
  sheet.mergeCells('E5:F5');
  let rLoc = 6;
  locEntries.forEach(([loc, count]) => {
    sheet.getCell(`E${rLoc}`).value = loc;
    sheet.getCell(`E${rLoc}`).font = { size: 10.5 };
    sheet.getCell(`F${rLoc}`).value = count;
    sheet.getCell(`F${rLoc}`).font = { bold: true, size: 10.5 };
    sheet.getCell(`F${rLoc}`).alignment = { horizontal: 'right' };
    rLoc++;
  });

  // Distribución por mes
  const startMesRow = Math.max(r0 + 1, rLoc + 1);
  const headMes = sheet.getCell(`B${startMesRow}`);
  headMes.value = 'Registros por mes';
  headMes.font = { bold: true, size: 11.5, color: { argb: BRAND.headerFill } };
  sheet.mergeCells(`B${startMesRow}:C${startMesRow}`);
  const porMes = {};
  data.forEach(r => { porMes[r.mesLabel] = (porMes[r.mesLabel]||0)+1; });
  let rMes = startMesRow + 1;
  Object.entries(porMes).sort((a,b) => a[0]<b[0]?-1:1).forEach(([mes, count]) => {
    sheet.getCell(`B${rMes}`).value = mes;
    sheet.getCell(`B${rMes}`).font = { size: 10.5 };
    sheet.getCell(`C${rMes}`).value = count;
    sheet.getCell(`C${rMes}`).font = { bold: true, size: 10.5 };
    sheet.getCell(`C${rMes}`).alignment = { horizontal: 'right' };
    rMes++;
  });

  return sheet;
}

document.getElementById('ge-exportBtn').addEventListener('click', async () => {
  const data = window._filteredRecords && window._filteredRecords.length ? window._filteredRecords : allRecords;
  if (!data.length) { alert('No hay registros para exportar.'); return; }

  const exportBtn = document.getElementById('ge-exportBtn');
  const originalLabel = exportBtn.innerHTML;
  exportBtn.disabled = true;
  exportBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Generando Excel...';

  try {
    const workbook = new ExcelJS.Workbook();
    workbook.creator = 'Control de Gestantes · Subred Norte';
    workbook.created = new Date();

    addSummarySheet(workbook, data);
    addDataSheet(workbook, 'Todos los registros', data);

    const porMes = {};
    data.forEach(r => { (porMes[r.mesLabel] = porMes[r.mesLabel] || []).push(r); });
    Object.keys(porMes).sort().forEach(mesLabel => {
      const safeName = mesLabel.replace(/[\\\/\?\*\[\]:]/g, '');
      addDataSheet(workbook, safeName || 'Mes', porMes[mesLabel]);
    });

    const buffer = await workbook.xlsx.writeBuffer();
    const blob = new Blob([buffer], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
    const now = new Date();
    const stamp = `${now.getFullYear()}${String(now.getMonth()+1).padStart(2,'0')}${String(now.getDate()).padStart(2,'0')}`;
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `Control_Gestantes_SubredNorte_${stamp}.xlsx`;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
  } catch (err) {
    console.error(err);
    alert('Ocurrió un error generando el Excel. Revisa la consola para más detalle.');
  } finally {
    exportBtn.disabled = false;
    exportBtn.innerHTML = originalLabel;
  }
});

})();
</script>

@endsection
