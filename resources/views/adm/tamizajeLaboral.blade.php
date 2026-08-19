@extends('layouts.adm.navigation')

@section('main')

<style>
  /* =========================================================================
     TAMIZAJES LABORAL — estilos con prefijo "tl-" para no chocar con
     Bootstrap / commom.css (ej: .modal, .badge, .btn, .row ya existen
     globalmente). Usa las variables de shell.css para mantenerse coherente
     con el resto del panel admin (home, actas, etc).
     ========================================================================= */
  .tl-page-desc{
    font-size:.83rem;color:var(--shell-muted);margin:-1.25rem 0 1.5rem;max-width:760px;line-height:1.5;
  }

  .tl-section{margin-bottom:1.5rem;}
  .tl-panel-head{display:flex;align-items:flex-start;justify-content:space-between;gap:12px;flex-wrap:wrap;margin-bottom:1rem;}
  .tl-panel-actions{display:flex;gap:8px;flex-shrink:0;flex-wrap:wrap;}
  .tl-card-title{font-family:var(--shell-head);font-size:1rem;margin:0 0 .3rem;font-weight:700;color:var(--shell-text);}
  .tl-card-sub{color:var(--shell-muted);font-size:.78rem;margin:0;max-width:640px;}
  .tl-btn-sm{padding:.4rem .75rem;font-size:.76rem;}

  /* ---------- Chain diagram ---------- */
  .tl-chain-card{margin-bottom:1.5rem;}
  .tl-chain{display:flex;align-items:center;gap:0;flex-wrap:wrap;margin-top:.75rem;}
  .tl-chain-node{display:flex;flex-direction:column;align-items:center;gap:8px;width:120px;}
  .tl-chain-circle{
    width:40px;height:40px;border-radius:50%;background:var(--shell-surface-2);
    border:2px solid var(--shell-border-strong);display:flex;align-items:center;justify-content:center;
    font-family:var(--shell-mono);font-size:15px;font-weight:700;color:var(--shell-text);
  }
  .tl-chain-node.tl-origin .tl-chain-circle{background:var(--shell-red);border-color:var(--shell-red);color:#fff;}
  .tl-chain-label{font-size:10.5px;text-align:center;color:var(--shell-muted);line-height:1.25;font-family:var(--shell-mono);letter-spacing:.02em;}
  .tl-chain-link{width:34px;height:2px;background:repeating-linear-gradient(90deg,var(--shell-border-strong) 0 5px, transparent 5px 9px);margin:0 2px 26px;}

  /* ---------- Dropzone ---------- */
  .tl-dropzone{
    border:1.5px dashed var(--shell-border-strong);border-radius:10px;padding:2rem;text-align:center;
    background:var(--shell-surface-2);cursor:pointer;transition:.15s;
  }
  .tl-dropzone:hover, .tl-dropzone.tl-drag{border-color:var(--shell-red);background:var(--shell-red-dim);}
  .tl-dropzone-icon{font-size:24px;color:var(--shell-red);margin-bottom:.5rem;}
  .tl-dropzone h3{margin:0 0 4px;font-family:var(--shell-head);font-size:.95rem;font-weight:700;color:var(--shell-text);}
  .tl-dropzone p{margin:0;color:var(--shell-muted);font-size:.78rem;}
  #tl-file-input{display:none;}

  .tl-filelist{margin-top:.9rem;display:flex;flex-direction:column;gap:.5rem;}
  .tl-filechip{
    display:flex;align-items:center;justify-content:space-between;gap:10px;
    background:var(--shell-surface-2);border:1px solid var(--shell-border);border-radius:7px;
    padding:.55rem .75rem;font-size:.78rem;
  }
  .tl-filechip .tl-fname{font-family:var(--shell-mono);font-size:.74rem;color:var(--shell-text);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;}
  .tl-filechip .tl-fmeta{color:var(--shell-muted-2);flex-shrink:0;font-size:.7rem;}
  .tl-filechip button{border:none;background:none;color:var(--shell-red);cursor:pointer;font-size:.74rem;font-family:var(--shell-sans);padding:2px 6px;}

  /* ---------- KPIs ---------- */
  .tl-kpis{display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));gap:1rem;}
  .tl-kpis .adm-kpi-value{font-size:1.9rem;}
  .tl-kpi-ok .adm-kpi-value{color:#2ecc71;}
  .tl-kpi-sub{font-size:.68rem;color:var(--shell-muted-2);margin-top:.35rem;}

  /* ---------- Tabla resumen por módulo ---------- */
  .tl-modtable{width:100%;border-collapse:collapse;font-size:.78rem;}
  .tl-modtable th{
    text-align:left;font-family:var(--shell-mono);font-size:.62rem;letter-spacing:.06em;text-transform:uppercase;
    color:var(--shell-muted);padding:.5rem .6rem;border-bottom:1px solid var(--shell-border);
  }
  .tl-modtable td{padding:.55rem .6rem;border-bottom:1px solid var(--shell-border);color:var(--shell-text);}
  .tl-modtable tr:last-child td{border-bottom:none;}
  .tl-bar-wrap{background:var(--shell-red-dim);border-radius:5px;overflow:hidden;height:7px;width:100%;min-width:80px;}
  .tl-bar-fill{background:#2ecc71;height:100%;}

  /* ---------- Filtros ---------- */
  .tl-filters{display:flex;flex-wrap:wrap;gap:1rem 1.4rem;align-items:flex-end;}
  .tl-field{display:flex;flex-direction:column;gap:.4rem;}
  .tl-field label{font-family:var(--shell-mono);font-size:.62rem;text-transform:uppercase;letter-spacing:.06em;color:var(--shell-muted);}
  .tl-field input[type=date], .tl-field input[type=text], .tl-field select{
    font-family:var(--shell-sans);border:1px solid var(--shell-border-strong);border-radius:6px;padding:.5rem .6rem;
    font-size:.8rem;background:var(--shell-surface-2);color:var(--shell-text);min-width:150px;
  }
  .tl-field input[type=text]{min-width:220px;}
  .tl-field input:focus, .tl-field select:focus{outline:none;border-color:var(--shell-red);}
  .tl-modchecks{display:flex;flex-wrap:wrap;gap:.5rem 1rem;}
  .tl-modchecks label{display:flex;align-items:center;gap:.4rem;font-size:.78rem;color:var(--shell-muted);cursor:pointer;font-family:var(--shell-sans);}
  .tl-modchecks input{accent-color:var(--shell-red);}

  /* ---------- Tabla de detalle ---------- */
  .tl-tablewrap{overflow-x:auto;}
  table.tl-table{width:100%;border-collapse:collapse;font-size:.76rem;min-width:1080px;}
  table.tl-table thead th{
    position:sticky;top:0;background:var(--shell-surface-2);color:var(--shell-text);
    text-align:left;font-family:var(--shell-mono);font-size:.62rem;letter-spacing:.06em;text-transform:uppercase;
    padding:.6rem .7rem;white-space:nowrap;z-index:2;border-bottom:2px solid var(--shell-red);
  }
  table.tl-table tbody td{padding:.55rem .7rem;border-bottom:1px solid var(--shell-border);vertical-align:middle;color:var(--shell-text);}
  table.tl-table tbody tr:hover{background:var(--shell-surface-2);}
  table.tl-table tbody tr.tl-notfound{background:rgba(230,57,70,.06);}
  .tl-mono{font-family:var(--shell-mono);}

  .tl-badge{display:inline-flex;align-items:center;gap:5px;padding:3px 9px;border-radius:20px;font-size:11px;font-weight:600;font-family:var(--shell-sans);}
  .tl-badge.tl-ok{background:rgba(46,204,113,.15);color:#2ecc71;}
  .tl-badge.tl-bad{background:var(--shell-red-dim);color:var(--shell-red);}
  .tl-badge .tl-dot{width:6px;height:6px;border-radius:50%;}
  .tl-badge.tl-ok .tl-dot{background:#2ecc71;}
  .tl-badge.tl-bad .tl-dot{background:var(--shell-red);}

  .tl-chainmini{display:flex;gap:4px;}
  .tl-chainmini .tl-node{
    width:20px;height:20px;border-radius:50%;border:1.5px solid var(--shell-border-strong);
    background:var(--shell-surface);display:flex;align-items:center;justify-content:center;
    font-family:var(--shell-mono);font-size:9.5px;color:var(--shell-muted-2);
  }
  .tl-chainmini .tl-node.tl-hit{background:#2ecc71;border-color:#2ecc71;color:#0a1f14;}

  .tl-rowbtn{border:none;background:none;color:var(--shell-red);cursor:pointer;font-size:.72rem;font-weight:600;padding:4px 6px;font-family:var(--shell-sans);}
  .tl-rowbtn:hover{text-decoration:underline;}

  .tl-empty{padding:2.75rem 1.25rem;text-align:center;color:var(--shell-muted);}
  .tl-empty-big{font-family:var(--shell-head);font-size:.95rem;color:var(--shell-text);margin-bottom:.4rem;}

  .tl-toolbar-bottom{display:flex;align-items:center;justify-content:space-between;padding:.75rem 0 0;border-top:1px solid var(--shell-border);font-size:.76rem;color:var(--shell-muted);margin-top:.75rem;}
  .tl-pager{display:flex;align-items:center;gap:.6rem;}
  .tl-pager button{border:1px solid var(--shell-border-strong);background:var(--shell-surface-2);color:var(--shell-text);border-radius:6px;padding:.35rem .65rem;cursor:pointer;font-size:.72rem;}
  .tl-pager button:disabled{opacity:.4;cursor:not-allowed;}
  .tl-pager button:hover:not(:disabled){border-color:var(--shell-red);color:var(--shell-red);}

  /* ---------- Modal ---------- */
  .tl-modal-bg{position:fixed;inset:0;background:rgba(0,0,0,.55);display:none;align-items:center;justify-content:center;z-index:2000;padding:24px;}
  .tl-modal-bg.tl-show{display:flex;}
  .tl-modal{background:var(--shell-surface);border:1px solid var(--shell-border-strong);border-radius:12px;max-width:640px;width:100%;max-height:82vh;overflow:auto;box-shadow:0 20px 60px rgba(0,0,0,.5);}
  .tl-modal-head{padding:1.1rem 1.35rem;border-bottom:1px solid var(--shell-border);display:flex;justify-content:space-between;align-items:flex-start;gap:14px;}
  .tl-modal-head h3{margin:0 0 4px;font-family:var(--shell-head);font-size:1rem;color:var(--shell-text);}
  .tl-modal-head p{margin:0;color:var(--shell-muted);font-size:.78rem;}
  .tl-modal-close{border:none;background:var(--shell-surface-2);width:30px;height:30px;border-radius:50%;cursor:pointer;font-size:16px;color:var(--shell-muted);flex-shrink:0;}
  .tl-modal-body{padding:1.1rem 1.35rem;}
  .tl-match-card{border:1px solid var(--shell-border);border-radius:8px;padding:.85rem 1rem;margin-bottom:.75rem;background:var(--shell-surface-2);}
  .tl-match-card:last-child{margin-bottom:0;}
  .tl-match-card .tl-src{font-family:var(--shell-mono);font-size:.68rem;text-transform:uppercase;letter-spacing:.05em;color:var(--shell-red);margin-bottom:.5rem;font-weight:700;}
  .tl-kv{display:grid;grid-template-columns:120px 1fr;gap:5px 10px;font-size:.78rem;}
  .tl-kv dt{color:var(--shell-muted-2);}
  .tl-kv dd{margin:0;color:var(--shell-text);}
  .tl-none-found{text-align:center;color:var(--shell-red);padding:1.25rem;font-size:.82rem;background:var(--shell-red-dim);border-radius:8px;}

  .tl-footer-note{margin-top:1.4rem;font-size:.68rem;color:var(--shell-muted-2);text-align:center;font-family:var(--shell-mono);}

  @media (max-width:900px){
    .tl-kpis{grid-template-columns:repeat(2,1fr);}
  }
</style>

{{-- ── PAGE HEADER ── --}}
<div class="adm-page-header">
    <div>
        <span class="adm-page-tag">// Herramientas</span>
        <h1 class="adm-page-title">Tamizajes laboral</h1>
    </div>
</div>

<p class="tl-page-desc">
    Cruza los documentos de los 6 módulos de tamizaje contra Sesiones Colectivas, NNA Trabajadores y UT Trabajadores,
    para confirmar que cada persona tamizada quede registrada en la base correspondiente. Sube el export GESI (.csv)
    y el cruce se calcula 100% en tu navegador.
</p>

<div class="adm-card tl-chain-card">
    <div class="tl-card-title">Flujo de trazabilidad validado</div>
    <div class="tl-chain" aria-hidden="true">
        <div class="tl-chain-node tl-origin"><div class="tl-chain-circle">T</div><span class="tl-chain-label">Tamizaje</span></div>
        <div class="tl-chain-link"></div>
        <div class="tl-chain-node"><div class="tl-chain-circle">S</div><span class="tl-chain-label">Sesiones<br>Colectivas</span></div>
        <div class="tl-chain-link"></div>
        <div class="tl-chain-node"><div class="tl-chain-circle">N</div><span class="tl-chain-label">NNA<br>Trabajadores</span></div>
        <div class="tl-chain-link"></div>
        <div class="tl-chain-node"><div class="tl-chain-circle">U</div><span class="tl-chain-label">UT<br>Trabajadores</span></div>
    </div>
</div>

{{-- ── 1. CARGA DE ARCHIVOS ── --}}
<div class="adm-card tl-section" id="tl-panel-upload">
    <div class="tl-panel-head">
        <div>
            <div class="tl-card-title">1 · Cargar export GESI</div>
            <p class="tl-card-sub">Archivo(s) CSV exportados desde GESI (UTF-16, separados por acento grave). Puedes cargar más de un entorno a la vez.</p>
        </div>
        <div class="tl-panel-actions">
            <button class="adm-btn adm-btn-primary tl-btn-sm" id="tl-btn-process" disabled>
                <i class="fa-solid fa-shuffle"></i> Procesar y cruzar
            </button>
        </div>
    </div>

    <div class="tl-dropzone" id="tl-dropzone">
        <div class="tl-dropzone-icon"><i class="fa-solid fa-cloud-arrow-up"></i></div>
        <h3>Arrastra el archivo aquí o haz clic para elegirlo</h3>
        <p>.csv exportado de GESI — puedes seleccionar varios a la vez</p>
        <input type="file" id="tl-file-input" accept=".csv" multiple>
    </div>
    <div class="tl-filelist" id="tl-filelist"></div>
</div>

{{-- ── RESULTADOS (ocultos hasta procesar) ── --}}
<div id="tl-results-area" style="display:none;">

    <div class="adm-card tl-section">
        <div class="tl-panel-head">
            <div>
                <div class="tl-card-title">2 · Resumen de cobertura</div>
                <p class="tl-card-sub" id="tl-summary-sub"></p>
            </div>
        </div>
        <div class="tl-kpis" id="tl-kpis"></div>
        <div style="height:18px"></div>
        <table class="tl-modtable" id="tl-modtable"></table>
    </div>

    <div class="adm-card tl-section">
        <div class="tl-panel-head">
            <div>
                <div class="tl-card-title">3 · Filtros</div>
                <p class="tl-card-sub">El filtro de fecha aplica sobre la fecha de intervención del tamizaje — los documentos pueden estar registrados en meses anteriores en las bases de destino.</p>
            </div>
            <div class="tl-panel-actions">
                <button class="adm-btn adm-btn-ghost tl-btn-sm" id="tl-btn-clear-filters">Limpiar filtros</button>
                <button class="adm-btn adm-btn-primary tl-btn-sm" id="tl-btn-export">
                    <i class="fa-solid fa-file-excel"></i> Exportar Excel
                </button>
            </div>
        </div>

        <div class="tl-filters">
            <div class="tl-field">
                <label>Fecha desde</label>
                <input type="date" id="tl-f-desde">
            </div>
            <div class="tl-field">
                <label>Fecha hasta</label>
                <input type="date" id="tl-f-hasta">
            </div>
            <div class="tl-field">
                <label>Estado</label>
                <select id="tl-f-estado">
                    <option value="todos">Todos</option>
                    <option value="encontrados">Encontrados</option>
                    <option value="no-encontrados">No encontrados</option>
                </select>
            </div>
            <div class="tl-field" style="flex:1;">
                <label>Buscar (documento, ficha, usuario, localidad)</label>
                <input type="text" id="tl-f-texto" placeholder="Ej: 1023862084">
            </div>
        </div>
        <div style="height:14px"></div>
        <div class="tl-field">
            <label>Módulos de tamizaje</label>
            <div class="tl-modchecks" id="tl-modchecks"></div>
        </div>
    </div>

    <div class="adm-card tl-section">
        <div class="tl-panel-head">
            <div>
                <div class="tl-card-title">4 · Detalle de registros</div>
                <p class="tl-card-sub" id="tl-table-sub"></p>
            </div>
        </div>
        <div class="tl-tablewrap">
            <table class="tl-table" id="tl-results-table">
                <thead>
                    <tr>
                        <th>Módulo tamizaje</th>
                        <th>Documento</th>
                        <th>Tipo doc.</th>
                        <th>Fecha interv.</th>
                        <th>Localidad</th>
                        <th>Usuario</th>
                        <th>Ficha</th>
                        <th>Estado</th>
                        <th>Bases</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody id="tl-results-tbody"></tbody>
            </table>
            <div class="tl-empty" id="tl-empty-state" style="display:none;">
                <div class="tl-empty-big">Sin resultados con estos filtros</div>
                <div>Prueba a ajustar el rango de fechas o limpiar el texto de búsqueda.</div>
            </div>
        </div>
        <div class="tl-toolbar-bottom">
            <span id="tl-page-info"></span>
            <div class="tl-pager">
                <button id="tl-btn-prev">‹ Anterior</button>
                <button id="tl-btn-next">Siguiente ›</button>
            </div>
        </div>
    </div>

    <div class="tl-footer-note">Cruce basado en número de documento normalizado (solo letras y dígitos). Los documentos sin ese campo diligenciado no se pueden validar.</div>
</div>

{{-- ── MODAL DETALLE ── --}}
<div class="tl-modal-bg" id="tl-modal-bg">
    <div class="tl-modal">
        <div class="tl-modal-head">
            <div>
                <h3 id="tl-modal-title">Detalle</h3>
                <p id="tl-modal-sub"></p>
            </div>
            <button class="tl-modal-close" id="tl-modal-close">✕</button>
        </div>
        <div class="tl-modal-body" id="tl-modal-body"></div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/exceljs/4.4.0/exceljs.min.js"></script>
<script>
// gesi-core.js
// Núcleo de parsing y cruce para exports GESI (formato UTF-16LE, campos entre backticks separados por `;`).
// Se mantiene como módulo autocontenido (IIFE) para poder reutilizarlo sin depender de la UI de esta vista.

const GesiCore = (function () {

  // ---------- Utilidades de texto ----------

  function stripAccents(s) {
    return (s || '').normalize('NFD').replace(/[̀-ͯ]/g, '');
  }

  function normKey(s) {
    return stripAccents((s || '').toString()).toUpperCase().trim().replace(/\s+/g, ' ');
  }

  function normDoc(s) {
    if (s == null) return '';
    // Deja solo dígitos y letras (algunos documentos de extranjería traen letras); quita espacios/puntos/guiones.
    return s.toString().trim().toUpperCase().replace(/[^A-Z0-9]/g, '');
  }

  // ---------- Corrección del bug conocido de backtick faltante antes de "Sub-Sección =>" ----------
  function fixLine(line) {
    // Caso normal correcto: `;`Sub-Sección => X`;`
    // Bug variante A: X;Sub-Sección => ...   (falta backtick de cierre del campo anterior Y apertura del nuevo)
    let s = line.replace(/([^`]);Sub-Secci[oó]n => /g, '$1`;`Sub-Sección => ');
    // Bug variante B: X`;Sub-Sección => ...  (falta solo el backtick de apertura del nuevo campo)
    s = s.replace(/`;Sub-Secci[oó]n => /g, '`;`Sub-Sección => ');
    return s;
  }

  function splitRow(line) {
    let s = line.replace(/\r$/, '');
    // quita backtick inicial si existe
    if (s.startsWith('`')) s = s.slice(1);
    // quita backtick / punto y coma final residual
    s = s.replace(/`+;?\s*$/, '');
    if (s.length === 0) return [];
    return s.split('`;`');
  }

  // ---------- Parser principal ----------
  // Devuelve un arreglo de "secciones": { entorno, modulo, seccion, headers:[...], rows:[[...]] }
  function parseGesiText(text) {
    // normaliza EOL y separa líneas
    const lines = text.split(/\r\n|\n|\r/);

    const sections = [];
    let currentEntorno = '';
    let currentModulo = '';
    let currentSection = null; // referencia al objeto de sección actual

    for (let raw of lines) {
      if (!raw || !raw.trim()) continue;
      const line = fixLine(raw);
      const trimmed = line.trim();

      if (trimmed.startsWith('Entorno => ')) {
        currentEntorno = trimmed.slice('Entorno => '.length).trim();
        continue;
      }
      if (trimmed.startsWith('Módulo => ') || trimmed.startsWith('Modulo => ')) {
        currentModulo = trimmed.replace(/^M[oó]dulo => /, '').trim();
        currentSection = null;
        continue;
      }
      if (trimmed.startsWith('Sección => ') || trimmed.startsWith('Seccion => ')) {
        const seccionName = trimmed.replace(/^Secci[oó]n => /, '').trim();
        currentSection = {
          entorno: currentEntorno,
          modulo: currentModulo,
          seccion: seccionName,
          headers: null,
          rows: []
        };
        sections.push(currentSection);
        continue;
      }
      if (trimmed.startsWith('Sub-Sección => ') || trimmed.startsWith('Sub-Seccion => ')) {
        // Línea de encabezado de columnas para la sección actual
        if (!currentSection) continue;
        let body = trimmed.replace(/^Sub-Secci[oó]n => /, '');
        const tokens = splitRow('`' + body + '`'); // reutiliza el mismo splitter agregando backticks ficticios
        const headers = tokens.map(t => t.replace(/^Sub-Secci[oó]n => /, '').trim());
        currentSection.headers = headers;
        continue;
      }

      // Si llegamos aquí, es una fila de datos
      if (!currentSection || !currentSection.headers) continue;
      const values = splitRow(line);
      currentSection.rows.push(values);
    }

    return sections;
  }

  // ---------- Detección genérica de columnas de "documento" ----------
  function isDocumentoHeader(h) {
    const k = normKey(h);
    if (!k.includes('DOCUMENTO')) return false;
    if (k.includes('TIPO DE DOCUMENTO') || k === 'TIPO DOCUMENTO') return false;
    return true;
  }
  function isTipoDocumentoHeader(h) {
    const k = normKey(h);
    return k.includes('TIPO DE DOCUMENTO') || k === 'TIPO DOCUMENTO';
  }

  function headerIndex(headers, exactNames) {
    const norm = headers.map(normKey);
    for (const name of exactNames) {
      const idx = norm.indexOf(normKey(name));
      if (idx !== -1) return idx;
    }
    return -1;
  }

  function rowCommonFields(headers, values) {
    const idxFecha = headerIndex(headers, ['Fecha_intervencion']);
    const idxFicha = headerIndex(headers, ['Ficha_fic']);
    const idxLocalidad = headerIndex(headers, ['Localidad_fic']);
    const idxUsuario = headerIndex(headers, ['Usuario']);
    const idxIdFic = headerIndex(headers, ['Id_fic']);
    return {
      idFicha: idxIdFic !== -1 ? (values[idxIdFic] || '').trim() : '',
      ficha: idxFicha !== -1 ? (values[idxFicha] || '').trim() : '',
      fecha: idxFecha !== -1 ? (values[idxFecha] || '').trim() : '',
      localidad: idxLocalidad !== -1 ? (values[idxLocalidad] || '').trim() : '',
      usuario: idxUsuario !== -1 ? (values[idxUsuario] || '').trim() : ''
    };
  }

  function tryBuildNombre(headers, values) {
    const norm = headers.map(normKey);
    // Caso "NOMBRES" + "APELLIDOS"
    let iN = norm.indexOf('NOMBRES');
    let iA = norm.indexOf('APELLIDOS');
    if (iN !== -1 || iA !== -1) {
      return [values[iN] || '', values[iA] || ''].join(' ').trim();
    }
    // Caso 1ER NOMBRE / 2DO NOMBRE / 1ER APELLIDO / 2DO APELLIDO (con variantes de espacio)
    const parts = [];
    ['1ER NOMBRE', '1 ER NOMBRE', '2DO NOMBRE', '2 DO NOMBRE', '1ER APELLIDO', '1 ER APELLIDO', '2DO APELLIDO', '2 DO APELLIDO']
      .forEach(key => {
        const idx = norm.indexOf(normKey(key));
        if (idx !== -1 && values[idx] && values[idx].trim()) parts.push(values[idx].trim());
      });
    return parts.join(' ').trim();
  }

  // ---------- Configuración de módulos fuente (tamizajes) y bases objetivo ----------
  const SOURCE_MODULES = [
    { key: 'TEST AUDIT', match: 'TAMIZAJE  TEST AUDIT', label: 'Tamizaje Test AUDIT' },
    { key: 'CANCER', match: 'CLASIFICACIÓN DEL RIESGO DE CÁNCER', label: 'Clasificación Riesgo de Cáncer' },
    { key: 'ERC_ASMA_EPOC', match: 'TAMIZAJE ERC ASMA EPOC', label: 'Tamizaje ERC / Asma / EPOC' },
    { key: 'FINDRISC', match: 'TAMIZAJE FINDRISC', label: 'Tamizaje FINDRISC' },
    { key: 'OMS', match: 'TAMIZAJES FAMILIARES  OMS', label: 'Tamizaje Familiar OMS' },
    { key: 'SRQ', match: 'TAMIZAJES FAMILIARES  SRQ', label: 'Tamizaje Familiar SRQ' }
  ];

  const TARGET_BASES = [
    { key: 'SESIONES', moduloMatch: 'SESIONES COLECTIVAS ENTORNOS', seccionMatch: 'PERSONAS', label: 'Sesiones Colectivas (Personas)' },
    { key: 'NNA', moduloMatch: 'NIÑOS NIÑAS Y ADOLESCENTES TRABAJADORES', seccionMatch: 'NNA IDENTIFICADO COMO TRABAJADOR', label: 'NNA Trabajadores' },
    { key: 'UT', moduloMatch: 'UT TRABAJADORES', seccionMatch: 'IDENTIFICACIÓN DE INDIVIDUOS', label: 'UT Trabajadores (Individuos)' }
  ];

  function matches(haystack, needle) {
    return normKey(haystack).includes(normKey(needle));
  }

  // ---------- Construcción del índice de bases objetivo ----------
  function buildTargetIndex(sections) {
    const index = new Map(); // documentoNormalizado -> [ {base, ficha, fecha, localidad, usuario, nombre, tipoDocumento, campo} ]

    for (const base of TARGET_BASES) {
      const secs = sections.filter(s => matches(s.modulo, base.moduloMatch) && matches(s.seccion, base.seccionMatch));
      for (const sec of secs) {
        if (!sec.headers) continue;
        const headers = sec.headers;
        const docCols = [];
        headers.forEach((h, i) => { if (isDocumentoHeader(h)) docCols.push(i); });
        if (docCols.length === 0) continue;

        for (const values of sec.rows) {
          const common = rowCommonFields(headers, values);
          const nombre = tryBuildNombre(headers, values);
          for (const ci of docCols) {
            const raw = (values[ci] || '').trim();
            const dn = normDoc(raw);
            if (!dn) continue;
            let tipoDoc = '';
            if (ci > 0 && isTipoDocumentoHeader(headers[ci - 1])) tipoDoc = (values[ci - 1] || '').trim();
            const entry = {
              baseKey: base.key,
              baseLabel: base.label,
              campo: headers[ci],
              documento: raw,
              tipoDocumento: tipoDoc,
              nombre,
              ...common
            };
            if (!index.has(dn)) index.set(dn, []);
            index.get(dn).push(entry);
          }
        }
      }
    }
    return index;
  }

  // ---------- Construcción de registros fuente (tamizajes) y cruce ----------
  function buildRegistros(sections, targetIndex) {
    const registros = [];

    for (const mod of SOURCE_MODULES) {
      const secs = sections.filter(s => matches(s.modulo, mod.match));
      for (const sec of secs) {
        if (!sec.headers) continue;
        const headers = sec.headers;
        const docCols = [];
        headers.forEach((h, i) => { if (isDocumentoHeader(h)) docCols.push(i); });
        if (docCols.length === 0) continue;

        for (const values of sec.rows) {
          const common = rowCommonFields(headers, values);
          for (const ci of docCols) {
            const raw = (values[ci] || '').trim();
            const dn = normDoc(raw);
            if (!dn) continue;
            let tipoDoc = '';
            if (ci > 0 && isTipoDocumentoHeader(headers[ci - 1])) tipoDoc = (values[ci - 1] || '').trim();

            const found = targetIndex.get(dn) || [];
            registros.push({
              modulo: mod.label,
              moduloKey: mod.key,
              seccion: sec.seccion,
              campoDocumento: headers[ci],
              documento: raw,
              documentoNorm: dn,
              tipoDocumento: tipoDoc,
              ...common,
              encontrado: found.length > 0,
              coincidencias: found
            });
          }
        }
      }
    }
    return registros;
  }

  function processFiles(fileTexts) {
    let sections = [];
    for (const text of fileTexts) {
      sections = sections.concat(parseGesiText(text));
    }
    const targetIndex = buildTargetIndex(sections);
    const registros = buildRegistros(sections, targetIndex);
    return { sections, targetIndex, registros };
  }

  return {
    parseGesiText, buildTargetIndex, buildRegistros, processFiles,
    normKey, normDoc, isDocumentoHeader, isTipoDocumentoHeader,
    SOURCE_MODULES, TARGET_BASES
  };
})();
</script>
<script>
(function () {
  'use strict';

  const state = {
    files: [],        // [{name, size, text}]
    registros: [],     // resultado de GesiCore.processFiles
    filtered: [],
    page: 1,
    pageSize: 60,
    modActive: new Set(GesiCore.SOURCE_MODULES.map(m => m.key))
  };

  // ---------- Elementos ----------
  const dropzone = document.getElementById('tl-dropzone');
  const fileInput = document.getElementById('tl-file-input');
  const filelist = document.getElementById('tl-filelist');
  const btnProcess = document.getElementById('tl-btn-process');
  const resultsArea = document.getElementById('tl-results-area');
  const summarySub = document.getElementById('tl-summary-sub');
  const kpisEl = document.getElementById('tl-kpis');
  const modtableEl = document.getElementById('tl-modtable');
  const modchecksEl = document.getElementById('tl-modchecks');
  const fDesde = document.getElementById('tl-f-desde');
  const fHasta = document.getElementById('tl-f-hasta');
  const fEstado = document.getElementById('tl-f-estado');
  const fTexto = document.getElementById('tl-f-texto');
  const btnClearFilters = document.getElementById('tl-btn-clear-filters');
  const btnExport = document.getElementById('tl-btn-export');
  const tbody = document.getElementById('tl-results-tbody');
  const emptyState = document.getElementById('tl-empty-state');
  const tableSub = document.getElementById('tl-table-sub');
  const pageInfo = document.getElementById('tl-page-info');
  const btnPrev = document.getElementById('tl-btn-prev');
  const btnNext = document.getElementById('tl-btn-next');
  const modalBg = document.getElementById('tl-modal-bg');
  const modalTitle = document.getElementById('tl-modal-title');
  const modalSub = document.getElementById('tl-modal-sub');
  const modalBody = document.getElementById('tl-modal-body');
  const modalClose = document.getElementById('tl-modal-close');

  // ---------- Carga de archivos ----------
  function readFileAsUtf16(file) {
    return new Promise((resolve, reject) => {
      const reader = new FileReader();
      reader.onload = () => {
        try {
          const buf = reader.result;
          const decoder = new TextDecoder('utf-16le');
          let text = decoder.decode(buf);
          text = text.replace(/^﻿/, '');
          resolve(text);
        } catch (e) { reject(e); }
      };
      reader.onerror = reject;
      reader.readAsArrayBuffer(file);
    });
  }

  function renderFilelist() {
    filelist.innerHTML = '';
    state.files.forEach((f, i) => {
      const div = document.createElement('div');
      div.className = 'tl-filechip';
      div.innerHTML = `
        <span class="tl-fname">${escapeHtml(f.name)}</span>
        <span class="tl-fmeta">${(f.size / 1024).toFixed(0)} KB</span>
        <button data-i="${i}">Quitar</button>`;
      div.querySelector('button').addEventListener('click', () => {
        state.files.splice(i, 1);
        renderFilelist();
        btnProcess.disabled = state.files.length === 0;
      });
      filelist.appendChild(div);
    });
  }

  async function handleFiles(fileListObj) {
    const arr = Array.from(fileListObj).filter(f => /\.csv$/i.test(f.name));
    for (const f of arr) {
      const text = await readFileAsUtf16(f);
      state.files.push({ name: f.name, size: f.size, text });
    }
    renderFilelist();
    btnProcess.disabled = state.files.length === 0;
  }

  dropzone.addEventListener('click', () => fileInput.click());
  fileInput.addEventListener('change', e => handleFiles(e.target.files));
  ['dragenter', 'dragover'].forEach(evt => dropzone.addEventListener(evt, e => {
    e.preventDefault(); dropzone.classList.add('tl-drag');
  }));
  ['dragleave', 'drop'].forEach(evt => dropzone.addEventListener(evt, e => {
    e.preventDefault(); dropzone.classList.remove('tl-drag');
  }));
  dropzone.addEventListener('drop', e => handleFiles(e.dataTransfer.files));

  // ---------- Procesamiento ----------
  btnProcess.addEventListener('click', () => {
    btnProcess.disabled = true;
    const prevHtml = btnProcess.innerHTML;
    btnProcess.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Procesando…';
    setTimeout(() => {
      try {
        const texts = state.files.map(f => f.text);
        const { registros } = GesiCore.processFiles(texts);
        state.registros = registros;
        buildModuleCheckboxes();
        applyFilters();
        resultsArea.style.display = 'block';
        resultsArea.scrollIntoView({ behavior: 'smooth', block: 'start' });
      } catch (err) {
        alert('Ocurrió un error al procesar el archivo. Verifica que sea un export GESI válido.\n\n' + err.message);
        console.error(err);
      } finally {
        btnProcess.disabled = false;
        btnProcess.innerHTML = prevHtml;
      }
    }, 30);
  });

  function buildModuleCheckboxes() {
    modchecksEl.innerHTML = '';
    GesiCore.SOURCE_MODULES.forEach(m => {
      const id = 'tl-mc-' + m.key;
      const label = document.createElement('label');
      label.innerHTML = `<input type="checkbox" id="${id}" checked> ${escapeHtml(m.label)}`;
      label.querySelector('input').addEventListener('change', e => {
        if (e.target.checked) state.modActive.add(m.key); else state.modActive.delete(m.key);
        applyFilters();
      });
      modchecksEl.appendChild(label);
    });
  }

  // ---------- Filtros ----------
  [fDesde, fHasta, fEstado].forEach(el => el.addEventListener('change', () => applyFilters()));
  fTexto.addEventListener('input', debounce(() => applyFilters(), 180));
  btnClearFilters.addEventListener('click', () => {
    fDesde.value = ''; fHasta.value = ''; fEstado.value = 'todos'; fTexto.value = '';
    state.modActive = new Set(GesiCore.SOURCE_MODULES.map(m => m.key));
    modchecksEl.querySelectorAll('input').forEach(cb => cb.checked = true);
    applyFilters();
  });

  function applyFilters() {
    const desde = fDesde.value;
    const hasta = fHasta.value;
    const estado = fEstado.value;
    const texto = normPlain(fTexto.value);

    state.filtered = state.registros.filter(r => {
      if (!state.modActive.has(r.moduloKey)) return false;
      if (desde && r.fecha && r.fecha < desde) return false;
      if (hasta && r.fecha && r.fecha > hasta) return false;
      if (estado === 'encontrados' && !r.encontrado) return false;
      if (estado === 'no-encontrados' && r.encontrado) return false;
      if (texto) {
        const hay = normPlain([r.documento, r.ficha, r.usuario, r.localidad, r.modulo].join(' '));
        if (!hay.includes(texto)) return false;
      }
      return true;
    });

    state.page = 1;
    renderKpis();
    renderModTable();
    renderTable();
  }

  function normPlain(s) {
    return (s || '').toString().toLowerCase();
  }

  // ---------- KPIs ----------
  function renderKpis() {
    const total = state.filtered.length;
    const ok = state.filtered.filter(r => r.encontrado).length;
    const bad = total - ok;
    const pct = total ? ((ok / total) * 100).toFixed(1) : '0.0';

    summarySub.textContent = `${state.registros.length.toLocaleString('es-CO')} registros de tamizaje cargados en total · mostrando resultados según filtros activos`;

    kpisEl.innerHTML = `
      <div class="adm-kpi">
        <div class="adm-kpi-value">${total.toLocaleString('es-CO')}</div>
        <div class="adm-kpi-label">Registros filtrados</div>
        <div class="tl-kpi-sub">documentos de tamizaje evaluados</div>
      </div>
      <div class="adm-kpi tl-kpi-ok">
        <div class="adm-kpi-value">${ok.toLocaleString('es-CO')}</div>
        <div class="adm-kpi-label">Encontrados</div>
        <div class="tl-kpi-sub">ya registrados en al menos una base</div>
      </div>
      <div class="adm-kpi is-accent">
        <div class="adm-kpi-value">${bad.toLocaleString('es-CO')}</div>
        <div class="adm-kpi-label">No encontrados</div>
        <div class="tl-kpi-sub">pendientes de verificar / registrar</div>
      </div>
      <div class="adm-kpi">
        <div class="adm-kpi-value">${pct}%</div>
        <div class="adm-kpi-label">Cobertura</div>
        <div class="tl-kpi-sub">% de trazabilidad confirmada</div>
      </div>
    `;
  }

  function renderModTable() {
    const byMod = {};
    GesiCore.SOURCE_MODULES.forEach(m => { byMod[m.key] = { label: m.label, total: 0, ok: 0 }; });
    state.filtered.forEach(r => {
      if (!byMod[r.moduloKey]) return;
      byMod[r.moduloKey].total++;
      if (r.encontrado) byMod[r.moduloKey].ok++;
    });
    let rows = '<thead><tr><th>Módulo</th><th>Total</th><th>Encontrados</th><th>No encontrados</th><th>Cobertura</th></tr></thead><tbody>';
    Object.values(byMod).forEach(m => {
      const pct = m.total ? Math.round((m.ok / m.total) * 100) : 0;
      rows += `<tr>
        <td>${escapeHtml(m.label)}</td>
        <td class="tl-mono">${m.total}</td>
        <td class="tl-mono" style="color:#2ecc71">${m.ok}</td>
        <td class="tl-mono" style="color:var(--shell-red)">${m.total - m.ok}</td>
        <td style="display:flex; align-items:center; gap:8px;">
          <div class="tl-bar-wrap"><div class="tl-bar-fill" style="width:${pct}%"></div></div>
          <span class="tl-mono" style="font-size:11.5px; color:var(--shell-muted-2); min-width:34px;">${pct}%</span>
        </td>
      </tr>`;
    });
    rows += '</tbody>';
    modtableEl.innerHTML = rows;
  }

  // ---------- Tabla principal ----------
  const BASE_ORDER = GesiCore.TARGET_BASES; // [{key,label}]

  function renderTable() {
    const total = state.filtered.length;
    const pages = Math.max(1, Math.ceil(total / state.pageSize));
    if (state.page > pages) state.page = pages;
    const startIdx = (state.page - 1) * state.pageSize;
    const pageItems = state.filtered.slice(startIdx, startIdx + state.pageSize);

    tableSub.textContent = `${total.toLocaleString('es-CO')} registros coinciden con los filtros actuales.`;

    if (total === 0) {
      tbody.innerHTML = '';
      emptyState.style.display = 'block';
    } else {
      emptyState.style.display = 'none';
      tbody.innerHTML = pageItems.map((r, i) => rowHtml(r, startIdx + i)).join('');
      tbody.querySelectorAll('button.tl-rowbtn').forEach(btn => {
        btn.addEventListener('click', () => openModal(state.filtered[parseInt(btn.dataset.idx, 10)]));
      });
    }

    pageInfo.textContent = total ? `Página ${state.page} de ${pages} · ${total.toLocaleString('es-CO')} registros` : 'Sin registros';
    btnPrev.disabled = state.page <= 1;
    btnNext.disabled = state.page >= pages;
  }

  function rowHtml(r, idx) {
    const chain = BASE_ORDER.map(b => {
      const hit = r.coincidencias.some(c => c.baseKey === b.key);
      return `<span class="tl-node ${hit ? 'tl-hit' : ''}" title="${escapeHtml(b.label)}">${b.key[0]}</span>`;
    }).join('');
    return `<tr class="${r.encontrado ? '' : 'tl-notfound'}">
      <td>${escapeHtml(r.modulo)}</td>
      <td class="tl-mono">${escapeHtml(r.documento || '—')}</td>
      <td>${escapeHtml(r.tipoDocumento || '—')}</td>
      <td class="tl-mono">${escapeHtml(r.fecha || '—')}</td>
      <td>${escapeHtml(r.localidad || '—')}</td>
      <td>${escapeHtml(r.usuario || '—')}</td>
      <td class="tl-mono">${escapeHtml(r.ficha || '—')}</td>
      <td>${r.encontrado
        ? '<span class="tl-badge tl-ok"><span class="tl-dot"></span>Encontrado</span>'
        : '<span class="tl-badge tl-bad"><span class="tl-dot"></span>No encontrado</span>'}</td>
      <td><div class="tl-chainmini">${chain}</div></td>
      <td><button class="tl-rowbtn" data-idx="${idx}">Ver</button></td>
    </tr>`;
  }

  btnPrev.addEventListener('click', () => { if (state.page > 1) { state.page--; renderTable(); } });
  btnNext.addEventListener('click', () => { state.page++; renderTable(); });

  // ---------- Modal detalle ----------
  function openModal(r) {
    modalTitle.textContent = `Documento ${r.documento}`;
    modalSub.textContent = `${r.modulo} · Fecha intervención ${r.fecha || 's/d'} · Ficha ${r.ficha || 's/d'}`;
    if (!r.coincidencias.length) {
      modalBody.innerHTML = `<div class="tl-none-found">Este documento no aparece en Sesiones Colectivas, NNA Trabajadores ni UT Trabajadores.</div>`;
    } else {
      modalBody.innerHTML = r.coincidencias.map(c => `
        <div class="tl-match-card">
          <div class="tl-src">${escapeHtml(c.baseLabel)}</div>
          <dl class="tl-kv">
            <dt>Nombre</dt><dd>${escapeHtml(c.nombre || '—')}</dd>
            <dt>Tipo doc.</dt><dd>${escapeHtml(c.tipoDocumento || '—')}</dd>
            <dt>Fecha interv.</dt><dd class="tl-mono">${escapeHtml(c.fecha || '—')}</dd>
            <dt>Localidad</dt><dd>${escapeHtml(c.localidad || '—')}</dd>
            <dt>Usuario</dt><dd>${escapeHtml(c.usuario || '—')}</dd>
            <dt>Ficha</dt><dd class="tl-mono">${escapeHtml(c.ficha || '—')}</dd>
          </dl>
        </div>`).join('');
    }
    modalBg.classList.add('tl-show');
  }
  modalClose.addEventListener('click', () => modalBg.classList.remove('tl-show'));
  modalBg.addEventListener('click', e => { if (e.target === modalBg) modalBg.classList.remove('tl-show'); });

  // ---------- Exportar Excel ----------
  btnExport.addEventListener('click', async () => {
    if (!state.filtered.length) { alert('No hay registros para exportar con los filtros actuales.'); return; }
    btnExport.disabled = true;
    const prevHtml = btnExport.innerHTML;
    btnExport.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Generando…';
    try {
      const wb = new ExcelJS.Workbook();
      wb.creator = 'Validador de Trazabilidad de Tamizajes';
      wb.created = new Date();

      const ws = wb.addWorksheet('Detalle');
      ws.columns = [
        { header: 'Módulo tamizaje', key: 'modulo', width: 30 },
        { header: 'Documento', key: 'documento', width: 16 },
        { header: 'Tipo documento', key: 'tipoDocumento', width: 20 },
        { header: 'Fecha intervención', key: 'fecha', width: 16 },
        { header: 'Localidad', key: 'localidad', width: 16 },
        { header: 'Usuario', key: 'usuario', width: 14 },
        { header: 'Ficha', key: 'ficha', width: 14 },
        { header: 'Estado', key: 'estado', width: 16 },
        { header: 'Bases donde se encontró', key: 'bases', width: 34 },
        { header: 'Detalle coincidencias', key: 'detalle', width: 60 }
      ];
      ws.getRow(1).font = { bold: true };
      ws.getRow(1).fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FF0F4642' } };
      ws.getRow(1).font = { bold: true, color: { argb: 'FFFFFFFF' } };

      state.filtered.forEach(r => {
        const bases = r.coincidencias.map(c => c.baseLabel).filter((v, i, a) => a.indexOf(v) === i).join(' | ');
        const detalle = r.coincidencias.map(c =>
          `${c.baseLabel}: ${c.nombre || 's/d'} (fecha ${c.fecha || 's/d'}, ficha ${c.ficha || 's/d'})`
        ).join('  //  ');
        const row = ws.addRow({
          modulo: r.modulo,
          documento: r.documento,
          tipoDocumento: r.tipoDocumento,
          fecha: r.fecha,
          localidad: r.localidad,
          usuario: r.usuario,
          ficha: r.ficha,
          estado: r.encontrado ? 'Encontrado' : 'No encontrado',
          bases: bases || '—',
          detalle: detalle || '—'
        });
        if (!r.encontrado) {
          row.eachCell(cell => { cell.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FFF4DED6' } }; });
        }
      });
      ws.autoFilter = { from: 'A1', to: 'J1' };
      ws.views = [{ state: 'frozen', ySplit: 1 }];

      // Hoja resumen
      const ws2 = wb.addWorksheet('Resumen');
      ws2.columns = [
        { header: 'Módulo', key: 'modulo', width: 34 },
        { header: 'Total', key: 'total', width: 10 },
        { header: 'Encontrados', key: 'ok', width: 14 },
        { header: 'No encontrados', key: 'bad', width: 16 },
        { header: 'Cobertura %', key: 'pct', width: 12 }
      ];
      ws2.getRow(1).font = { bold: true, color: { argb: 'FFFFFFFF' } };
      ws2.getRow(1).fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FF0F4642' } };
      const byMod = {};
      GesiCore.SOURCE_MODULES.forEach(m => { byMod[m.key] = { label: m.label, total: 0, ok: 0 }; });
      state.filtered.forEach(r => {
        if (!byMod[r.moduloKey]) return;
        byMod[r.moduloKey].total++;
        if (r.encontrado) byMod[r.moduloKey].ok++;
      });
      Object.values(byMod).forEach(m => {
        ws2.addRow({ modulo: m.label, total: m.total, ok: m.ok, bad: m.total - m.ok, pct: m.total ? Math.round((m.ok / m.total) * 100) : 0 });
      });

      const buf = await wb.xlsx.writeBuffer();
      const blob = new Blob([buf], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
      const url = URL.createObjectURL(blob);
      const a = document.createElement('a');
      const stamp = new Date().toISOString().slice(0, 10);
      a.href = url;
      a.download = `validacion_tamizajes_${stamp}.xlsx`;
      document.body.appendChild(a);
      a.click();
      a.remove();
      URL.revokeObjectURL(url);
    } catch (err) {
      alert('No se pudo generar el Excel: ' + err.message);
      console.error(err);
    } finally {
      btnExport.disabled = false;
      btnExport.innerHTML = prevHtml;
    }
  });

  // ---------- Utilidades ----------
  function escapeHtml(s) {
    return (s == null ? '' : s.toString())
      .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;');
  }
  function debounce(fn, ms) {
    let t;
    return (...args) => { clearTimeout(t); t = setTimeout(() => fn(...args), ms); };
  }
})();
</script>

@endsection
