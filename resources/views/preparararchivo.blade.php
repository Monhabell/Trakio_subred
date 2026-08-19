@extends('layouts.adm.navigation')

@section('main')

<style>
  /* =========================================================================
     PREPARAR ARCHIVO GESI — estilos con prefijo "pa-" para no chocar con
     Bootstrap / commom.css. Usa las variables de tema de shell.css para
     mantenerse coherente con el resto del panel admin (home, actas, etc).
     ========================================================================= */
  .pa-page-desc{
    font-size:.83rem;color:var(--shell-muted);margin:-1.25rem 0 1.5rem;max-width:760px;line-height:1.5;
  }

  /* ---------- Dropzone ---------- */
  .pa-dropzone{
    border:2px dashed var(--shell-border-strong);
    border-radius:14px;
    background:var(--shell-surface);
    padding:3.5rem 1.5rem;
    text-align:center;
    transition:border-color .15s, background .15s;
    cursor:pointer;
    margin-bottom:2rem;
  }
  .pa-dropzone.pa-drag{border-color:var(--shell-red); background:var(--shell-red-dim);}
  .pa-dropzone-icon{
    width:56px;height:56px;border-radius:16px;margin:0 auto 1rem;
    background:var(--shell-surface-2);border:1px solid var(--shell-border);
    display:flex;align-items:center;justify-content:center;
  }
  .pa-dropzone-icon svg{width:26px;height:26px;color:var(--shell-red);}
  .pa-dropzone h2{font-family:var(--shell-head);font-size:1.05rem;margin:0 0 .4rem;font-weight:700;color:var(--shell-text);}
  .pa-dropzone p{font-size:.82rem;color:var(--shell-muted);margin:0 0 1.2rem;}
  .pa-dropzone .pa-hint{font-size:.7rem;color:var(--shell-muted-2);margin-top:1rem;font-family:var(--shell-mono);}
  #pa-fileInput{display:none;}
  .pa-btn-sm{padding:.4rem .75rem;font-size:.76rem;}

  /* ---------- File bar ---------- */
  .pa-filebar{
    display:flex;align-items:center;justify-content:space-between;gap:1rem;
    padding:1rem 1.25rem;margin-bottom:1.25rem;flex-wrap:wrap;
  }
  .pa-filebar-info{display:flex;align-items:center;gap:.75rem;min-width:0;}
  .pa-file-icon{
    width:36px;height:36px;border-radius:9px;background:var(--shell-surface-2);
    border:1px solid var(--shell-border);display:flex;align-items:center;justify-content:center;flex-shrink:0;
  }
  .pa-file-icon svg{width:17px;height:17px;color:var(--shell-red);}
  .pa-filebar-info .pa-name{font-size:.85rem;font-weight:700;color:var(--shell-text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:420px;}
  .pa-filebar-info .pa-meta{font-size:.72rem;color:var(--shell-muted);font-family:var(--shell-mono);}

  /* ---------- Stat tiles ---------- */
  .pa-stats-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:1rem;margin-bottom:1.5rem;}
  .pa-stats-grid .adm-kpi-value{font-size:1.9rem;}

  /* ---------- Toolbar ---------- */
  .pa-toolbar-card{margin-bottom:1.25rem;}
  .pa-toolbar{display:flex;align-items:center;gap:1.25rem;flex-wrap:wrap;}
  .pa-search-wrap{position:relative;flex:1;min-width:220px;}
  .pa-search-wrap svg{position:absolute;left:11px;top:50%;transform:translateY(-50%);width:15px;height:15px;color:var(--shell-muted-2);}
  .pa-search-wrap input{
    width:100%;padding:.55rem .75rem .55rem 2.1rem;border:1px solid var(--shell-border-strong);border-radius:6px;
    font-family:var(--shell-sans);font-size:.82rem;background:var(--shell-surface-2);color:var(--shell-text);
  }
  .pa-search-wrap input::placeholder{color:var(--shell-muted-2);}
  .pa-search-wrap input:focus{outline:none;border-color:var(--shell-red);}

  .pa-toggle-group{display:flex;align-items:center;gap:1rem;flex-wrap:wrap;}
  .pa-toggle{display:flex;align-items:center;gap:.45rem;cursor:pointer;user-select:none;}
  .pa-toggle input{display:none;}
  .pa-toggle .pa-track{
    width:32px;height:18px;border-radius:100px;background:var(--shell-border-strong);position:relative;transition:background .15s;flex-shrink:0;
  }
  .pa-toggle .pa-track::after{
    content:'';position:absolute;top:2px;left:2px;width:14px;height:14px;border-radius:50%;
    background:#fff;transition:transform .15s;box-shadow:0 1px 2px rgba(0,0,0,.4);
  }
  .pa-toggle input:checked + .pa-track{background:var(--shell-red);}
  .pa-toggle input:checked + .pa-track::after{transform:translateX(14px);}
  .pa-toggle span.pa-lbl{font-size:.76rem;font-weight:600;color:var(--shell-text);white-space:nowrap;}

  .pa-pagesize select{
    border:1px solid var(--shell-border-strong);border-radius:6px;padding:.45rem .6rem;
    font-family:var(--shell-sans);font-size:.76rem;background:var(--shell-surface-2);color:var(--shell-text);font-weight:600;
  }

  .pa-date-range{display:flex;align-items:center;gap:.5rem;flex-wrap:wrap;}
  .pa-date-range .pa-drlabel{font-size:.74rem;font-weight:700;color:var(--shell-muted);white-space:nowrap;}
  .pa-date-range input[type=date]{
    border:1px solid var(--shell-border-strong);border-radius:6px;padding:.4rem .55rem;
    font-family:var(--shell-sans);font-size:.76rem;background:var(--shell-surface-2);color:var(--shell-text);
  }
  .pa-date-range input[type=date]::-webkit-calendar-picker-indicator{filter:invert(.6);}
  .pa-date-range input[type=date]:focus{outline:none;border-color:var(--shell-red);}
  .pa-date-range .pa-dash{color:var(--shell-muted-2);font-size:.74rem;}
  .pa-date-range .pa-clear-dates{
    border:none;background:none;color:var(--shell-red);font-size:.7rem;font-weight:700;
    cursor:pointer;padding:.25rem .1rem;text-decoration:underline;
  }
  .pa-date-range .pa-clear-dates:disabled{color:var(--shell-muted-2);cursor:not-allowed;text-decoration:none;}
  .pa-date-hint{font-size:.65rem;color:var(--shell-muted-2);font-family:var(--shell-mono);width:100%;margin-top:-.25rem;}
  .pa-no-date-col{
    font-size:.7rem;color:#f5a623;background:rgba(245,166,35,.1);
    padding:.3rem .6rem;border-radius:6px;margin-bottom:.6rem;display:inline-flex;align-items:center;gap:.4rem;
    font-weight:600;border:1px solid rgba(245,166,35,.25);
  }

  /* ---------- Module tabs ---------- */
  .pa-modtabs{display:flex;gap:6px;overflow-x:auto;padding-bottom:2px;margin-bottom:0;scrollbar-width:thin;}
  .pa-modtabs::-webkit-scrollbar{height:6px;}
  .pa-modtabs::-webkit-scrollbar-thumb{background:var(--shell-border-strong);border-radius:10px;}
  .pa-modtab{
    flex-shrink:0;cursor:pointer;padding:.6rem .9rem;border-radius:10px 10px 0 0;
    background:var(--shell-surface);border:1px solid var(--shell-border);border-bottom:none;
    font-size:.76rem;font-weight:700;color:var(--shell-muted);
    display:flex;align-items:center;gap:.5rem;white-space:nowrap;
    position:relative;top:1px;transition:color .12s, background .12s;
  }
  .pa-modtab:hover{color:var(--shell-text);}
  .pa-modtab.is-active{background:var(--shell-surface);color:var(--shell-text);border-color:var(--shell-border);}
  .pa-modtab .pa-dot{width:6px;height:6px;border-radius:50%;background:var(--shell-red);flex-shrink:0;}
  .pa-modtab.is-empty .pa-dot{background:var(--shell-muted-2);}
  .pa-modtab .pa-count{
    font-family:var(--shell-mono);font-size:.62rem;font-weight:600;color:var(--shell-muted-2);
    background:var(--shell-surface-2);padding:1px 6px;border-radius:100px;
  }
  .pa-modtab.is-active .pa-count{background:var(--shell-red-dim);color:var(--shell-red);}

  .pa-modpanel{border-radius:0 10px 10px 10px;}

  .pa-sectabs{display:flex;gap:8px;flex-wrap:wrap;margin-bottom:1rem;}
  .pa-sectab{
    cursor:pointer;padding:.5rem .8rem;border-radius:100px;
    background:var(--shell-surface-2);border:1px solid var(--shell-border);
    font-size:.72rem;font-weight:600;color:var(--shell-muted);
    display:flex;align-items:center;gap:.4rem;transition:all .12s;
  }
  .pa-sectab:hover{border-color:var(--shell-border-strong);}
  .pa-sectab.is-active{background:var(--shell-red);border-color:var(--shell-red);color:#fff;}
  .pa-sectab .pa-n{font-family:var(--shell-mono);font-size:.6rem;opacity:.8;}
  .pa-sectab.is-empty::before{content:'';width:5px;height:5px;border-radius:50%;background:var(--shell-muted-2);flex-shrink:0;}
  .pa-sectab.is-active.is-empty::before{background:rgba(255,255,255,.7);}

  /* ---------- Table ---------- */
  .pa-table-scroll{overflow:auto;max-height:560px;border:1px solid var(--shell-border);border-radius:8px;}
  table.pa-data{border-collapse:separate;border-spacing:0;font-size:.74rem;width:100%;}
  table.pa-data thead th{
    position:sticky;top:0;z-index:2;
    background:var(--shell-surface-2);color:var(--shell-text);
    font-weight:700;text-align:left;padding:.5rem .6rem;
    border-right:1px solid var(--shell-border);border-bottom:2px solid var(--shell-red);
    white-space:nowrap;font-size:.68rem;
  }
  table.pa-data thead tr.pa-group-row th{
    top:0;z-index:3;background:var(--shell-bg);
    font-size:.6rem;text-transform:uppercase;letter-spacing:.03em;
    padding:.4rem .6rem;border-right:1px solid rgba(255,255,255,.08);border-bottom:1px solid var(--shell-border);
    text-align:center;
  }
  table.pa-data thead tr.pa-label-row th{top:27px;}
  table.pa-data tbody td{
    padding:.4rem .6rem;border-bottom:1px solid var(--shell-border);border-right:1px solid var(--shell-border);
    white-space:nowrap;max-width:260px;overflow:hidden;text-overflow:ellipsis;color:var(--shell-text);
  }
  table.pa-data tbody tr:nth-child(even){background:rgba(255,255,255,.015);}
  table.pa-data tbody tr:hover{background:var(--shell-surface-2);}
  table.pa-data tbody td.pa-rownum{
    position:sticky;left:0;background:inherit;color:var(--shell-muted-2);font-family:var(--shell-mono);
    font-size:.62rem;text-align:right;padding-right:.5rem;border-right:1px solid var(--shell-border-strong);
  }
  table.pa-data thead th.pa-rownum-h{position:sticky;left:0;z-index:4;background:var(--shell-bg);}

  .pa-empty-state{padding:3.5rem 1rem;text-align:center;color:var(--shell-muted);}
  .pa-empty-state svg{width:38px;height:38px;color:var(--shell-muted-2);margin-bottom:.75rem;}
  .pa-empty-state h3{font-family:var(--shell-head);font-size:.95rem;margin:0 0 .3rem;color:var(--shell-text);}
  .pa-empty-state p{font-size:.78rem;margin:0;color:var(--shell-muted);}

  .pa-table-footer{display:flex;align-items:center;justify-content:space-between;margin-top:.75rem;flex-wrap:wrap;gap:.6rem;}
  .pa-table-footer .pa-info{font-size:.74rem;color:var(--shell-muted);}
  .pa-table-footer .pa-info b{color:var(--shell-text);}
  .pa-pager{display:flex;align-items:center;gap:.4rem;}
  .pa-pager button{
    border:1px solid var(--shell-border-strong);background:var(--shell-surface-2);border-radius:6px;
    width:26px;height:26px;cursor:pointer;display:flex;align-items:center;justify-content:center;
    color:var(--shell-text);font-weight:600;font-size:.7rem;
  }
  .pa-pager button:disabled{opacity:.35;cursor:not-allowed;}
  .pa-pager button:hover:not(:disabled){border-color:var(--shell-red);color:var(--shell-red);}
  .pa-pager .pa-page-ind{font-family:var(--shell-mono);font-size:.7rem;color:var(--shell-muted);padding:0 .35rem;}

  /* ---------- Export panel ---------- */
  .pa-export-bar{margin-bottom:1.25rem;}
  .pa-export-bar-head{display:flex;align-items:center;justify-content:space-between;gap:.75rem;flex-wrap:wrap;}
  .pa-export-bar-head .pa-left{display:flex;align-items:center;gap:.6rem;}
  .pa-export-bar-head .pa-left svg{width:18px;height:18px;color:var(--shell-red);}
  .pa-export-bar-head h3{font-family:var(--shell-head);font-size:.9rem;margin:0;font-weight:700;color:var(--shell-text);}
  .pa-export-bar-head p{font-size:.72rem;color:var(--shell-muted);margin:.15rem 0 0;}
  .pa-export-details{margin-top:.9rem;padding-top:.9rem;border-top:1px solid var(--shell-border);}
  .pa-export-tree{
    display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:.6rem;
    max-height:340px;overflow:auto;padding-right:.25rem;margin-bottom:.75rem;
  }
  .pa-export-mod-card{border:1px solid var(--shell-border);border-radius:8px;padding:.6rem .75rem;background:var(--shell-surface-2);}
  .pa-export-mod-card .pa-mhead{display:flex;align-items:center;gap:.5rem;font-size:.76rem;font-weight:700;margin-bottom:.4rem;color:var(--shell-text);}
  .pa-export-mod-card .pa-mhead label{display:flex;align-items:center;gap:.4rem;cursor:pointer;}
  .pa-export-sec-list{display:flex;flex-direction:column;gap:.25rem;padding-left:1.35rem;}
  .pa-export-sec-list label{
    display:flex;align-items:center;gap:.4rem;font-size:.7rem;color:var(--shell-muted);cursor:pointer;font-weight:500;
  }
  .pa-export-sec-list .pa-n{font-family:var(--shell-mono);color:var(--shell-muted-2);font-size:.6rem;margin-left:auto;}
  .pa-export-tree input[type=checkbox]{width:14px;height:14px;accent-color:var(--shell-red);cursor:pointer;flex-shrink:0;}
  .pa-export-actions{display:flex;align-items:center;gap:.6rem;flex-wrap:wrap;}

  details.pa-export-details-toggle > summary{list-style:none;cursor:pointer;}
  details.pa-export-details-toggle > summary::-webkit-details-marker{display:none;}

  .pa-toast{
    position:fixed;bottom:24px;left:50%;transform:translateX(-50%);
    background:var(--shell-surface-2);color:var(--shell-text);padding:.75rem 1.25rem;border-radius:10px;
    font-size:.8rem;font-weight:600;box-shadow:0 8px 24px rgba(0,0,0,.4);z-index:9999;
    display:flex;align-items:center;gap:.6rem;opacity:0;pointer-events:none;transition:opacity .2s, transform .2s;
    border:1px solid var(--shell-border-strong);
  }
  .pa-toast.show{opacity:1;transform:translateX(-50%) translateY(-4px);}
  .pa-toast.pa-error{background:var(--shell-red);color:#fff;border-color:var(--shell-red);}
  .pa-spinner{width:14px;height:14px;border-radius:50%;border:2px solid rgba(255,255,255,.3);border-top-color:#fff;animation:pa-spin .7s linear infinite;}
  @keyframes pa-spin{to{transform:rotate(360deg);}}

  .pa-footer-note{text-align:center;font-size:.68rem;color:var(--shell-muted-2);margin-top:2rem;font-family:var(--shell-mono);}

  @media (max-width:720px){
    .pa-filebar-info .pa-name{max-width:200px;}
    table.pa-data tbody td{max-width:160px;}
  }
</style>

{{-- ── PAGE HEADER ── --}}
<div class="adm-page-header">
    <div>
        <span class="adm-page-tag">// Herramientas</span>
        <h1 class="adm-page-title">Preparar Archivo GESI</h1>
    </div>
    <div class="adm-page-badge" id="pa-statusTag">Sin archivo cargado</div>
</div>

<p class="pa-page-desc">
    Sube el archivo de exportación GESI (.csv, con bloques Entorno / Módulo / Sección / Sub-Sección) para separarlo
    por pestañas de módulo y sección, filtrar por fecha de intervención y generar el Excel final por hojas.
    El procesamiento ocurre 100% en tu navegador — el archivo no se sube a ningún servidor.
</p>

{{-- ── DROPZONE ── --}}
<div id="pa-dropzone" class="pa-dropzone">
    <div class="pa-dropzone-icon">
        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 16V4M12 4l-4 4M12 4l4 4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><path d="M4 16v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
    </div>
    <h2>Arrastra aquí el archivo de exportación GESI</h2>
    <p>Formato .csv con bloques Entorno / Módulo / Sección / Sub-Sección (UTF-16 o UTF-8)</p>
    <button class="adm-btn adm-btn-primary" id="pa-pickFileBtn" type="button">
        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" width="15" height="15" style="vertical-align:-2px;margin-right:5px;"><path d="M12 4v12M12 4l-3.5 3.5M12 4l3.5 3.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M5 16v2a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
        Seleccionar archivo
    </button>
    <input type="file" id="pa-fileInput" accept=".csv,.txt">
    <div class="pa-hint">El procesamiento ocurre 100% en tu navegador — el archivo no se sube a ningún servidor</div>
</div>

{{-- ── MAIN CONTENT (oculto hasta cargar archivo) ── --}}
<div id="pa-mainContent" style="display:none;">

    <div class="adm-card pa-filebar">
        <div class="pa-filebar-info">
            <div class="pa-file-icon">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M6 3h9l5 5v13a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1Z" stroke="currentColor" stroke-width="1.6"/><path d="M14 3v5h5" stroke="currentColor" stroke-width="1.6"/></svg>
            </div>
            <div>
                <div class="pa-name" id="pa-fileName">—</div>
                <div class="pa-meta" id="pa-fileMeta">—</div>
            </div>
        </div>
        <button class="adm-btn adm-btn-ghost pa-btn-sm" id="pa-changeFileBtn" type="button">Cambiar archivo</button>
    </div>

    <div class="pa-stats-grid" id="pa-statsRow"></div>

    <div class="adm-card pa-toolbar-card">
        <div class="pa-toolbar">
            <div class="pa-search-wrap">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="1.8"/><path d="m20 20-3.5-3.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                <input type="text" id="pa-searchInput" placeholder="Buscar en la sección activa (documento, nombre, id...)">
            </div>
            <div class="pa-toggle-group">
                <label class="pa-toggle"><input type="checkbox" id="pa-toggleEmptyModules" checked><span class="pa-track"></span><span class="pa-lbl">Mostrar módulos vacíos</span></label>
                <label class="pa-toggle"><input type="checkbox" id="pa-toggleEmptySections" checked><span class="pa-track"></span><span class="pa-lbl">Mostrar secciones vacías</span></label>
                <label class="pa-toggle"><input type="checkbox" id="pa-toggleEmptyCols"><span class="pa-track"></span><span class="pa-lbl">Ocultar columnas vacías</span></label>
            </div>
            <div class="pa-pagesize">
                <select id="pa-pageSizeSelect">
                    <option value="25">25 filas/pág.</option>
                    <option value="50" selected>50 filas/pág.</option>
                    <option value="100">100 filas/pág.</option>
                    <option value="250">250 filas/pág.</option>
                    <option value="999999">Todas</option>
                </select>
            </div>
            <div class="pa-date-range">
                <span class="pa-drlabel">Fecha intervención:</span>
                <input type="date" id="pa-dateFrom">
                <span class="pa-dash">–</span>
                <input type="date" id="pa-dateTo">
                <button class="pa-clear-dates" id="pa-clearDatesBtn" disabled type="button">Limpiar</button>
                <div class="pa-date-hint" id="pa-dateHint"></div>
            </div>
        </div>
    </div>

    <div id="pa-exportBarContainer"></div>

    <div class="pa-modtabs" id="pa-modTabs"></div>
    <div class="adm-card pa-modpanel" id="pa-modPanel"></div>

</div>

<div class="pa-footer-note">validador/visor GESI · procesamiento local en el navegador</div>

<div class="pa-toast" id="pa-toast"><span id="pa-toastIcon"></span><span id="pa-toastMsg"></span></div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/exceljs/4.4.0/exceljs.min.js"></script>
<script>
(function(){
"use strict";

/* =========================================================================
   1. ESTADO GLOBAL
   ========================================================================= */
const state = {
  fileName: '',
  fileSizeLabel: '',
  entorno: '',
  modules: [],           // [{name, shortName, sections:[{name, columns, rows, groupSpans, emptyColSet, isEmpty}], isEmpty}]
  activeModuleIndex: 0,
  activeSectionIndex: 0,
  showEmptyModules: true,
  showEmptySections: true,
  hideEmptyColumns: false,
  searchTerm: '',
  pageSize: 50,
  currentPage: 1,
  exportSelected: new Set(), // "modIdx-secIdx" keys included in export
  exportHideEmptyCols: false,
  exportOpen: false,
  dateFrom: '',   // 'YYYY-MM-DD' o ''
  dateTo: '',     // 'YYYY-MM-DD' o ''
  globalDateMin: '',
  globalDateMax: '',
};

const DATE_COLUMN_NAME = 'fecha_intervencion';
function normalizeLabel(str){
  return (str || '')
    .toString()
    .trim()
    .toLowerCase()
    .normalize('NFD').replace(/[̀-ͯ]/g, ''); // quita tildes
}

function q(id){ return document.getElementById(id); }

/* =========================================================================
   2. PARSER GESI
   Formato: bloques "Entorno => X" / "Módulo => X" / "Sección => X" seguidos
   de una línea de encabezado y N líneas de datos, todas delimitadas como
   `campo1`;`campo2`;...;`campoN`; con marcadores "Sub-Sección => Nombre"
   incrustados como pseudo-columnas que agrupan visualmente los campos
   reales que le siguen.
   ========================================================================= */

// Corrige el bug conocido de exportación GESI: a veces falta la backtick
// de apertura antes de un marcador "Sub-Sección =>" embebido a mitad de
// línea, lo que descuadra las columnas. Se normaliza siempre a `;`Sub-Sección
function fixKnownExportBug(content){
  return content.replace(/;`?Sub-Sección =>/g, ';`Sub-Sección =>');
}

function splitFields(line){
  line = line.replace(/\r$/, '');
  if (line.endsWith(';')) line = line.slice(0, -1);
  const parts = line.split(';');
  const out = new Array(parts.length);
  for (let i = 0; i < parts.length; i++){
    let p = parts[i];
    if (p.charAt(0) === '`') p = p.slice(1);
    if (p.charAt(p.length - 1) === '`') p = p.slice(0, -1);
    out[i] = p;
  }
  return out;
}

function decodeFileBuffer(buffer){
  const bytes = new Uint8Array(buffer);
  // BOM detection
  if (bytes.length >= 2 && bytes[0] === 0xFF && bytes[1] === 0xFE){
    return new TextDecoder('utf-16le').decode(buffer);
  }
  if (bytes.length >= 2 && bytes[0] === 0xFE && bytes[1] === 0xFF){
    return new TextDecoder('utf-16be').decode(buffer);
  }
  if (bytes.length >= 3 && bytes[0] === 0xEF && bytes[1] === 0xBB && bytes[2] === 0xBF){
    return new TextDecoder('utf-8').decode(buffer).replace(/^﻿/, '');
  }
  // Heuristic: many interleaved zero bytes => probably UTF-16LE without BOM
  let zeroCount = 0;
  const sampleLen = Math.min(bytes.length, 4000);
  for (let i = 1; i < sampleLen; i += 2) if (bytes[i] === 0) zeroCount++;
  if (zeroCount > sampleLen / 4){
    return new TextDecoder('utf-16le').decode(buffer);
  }
  return new TextDecoder('utf-8').decode(buffer);
}

function parseGesi(content){
  content = fixKnownExportBug(content).replace(/^﻿/, '');
  const lines = content.split('\n');
  const modules = [];
  let currentEntorno = '';
  let currentModule = null;
  let currentSection = null;
  let state_ = 'idle'; // idle | expect_header | data

  for (let i = 0; i < lines.length; i++){
    const line = lines[i].replace(/\r$/, '');
    if (line.trim() === '') continue;

    if (line.startsWith('Entorno => ')){
      currentEntorno = line.slice('Entorno => '.length).trim();
      state_ = 'idle';
      continue;
    }
    if (line.startsWith('Módulo => ')){
      currentModule = {
        name: line.slice('Módulo => '.length).trim(),
        entorno: currentEntorno,
        sections: []
      };
      modules.push(currentModule);
      state_ = 'idle';
      continue;
    }
    if (line.startsWith('Sección => ')){
      currentSection = {
        name: line.slice('Sección => '.length).trim(),
        columns: [],     // [{label, group}]
        keepMask: [],
        rows: []
      };
      if (currentModule) currentModule.sections.push(currentSection);
      state_ = 'expect_header';
      continue;
    }

    if (state_ === 'expect_header' && currentSection){
      const fields = splitFields(line);
      let currentGroup = null;
      const columns = [];
      const keepMask = new Array(fields.length);
      for (let k = 0; k < fields.length; k++){
        const f = fields[k];
        if (f.startsWith('Sub-Sección => ')){
          currentGroup = f.slice('Sub-Sección => '.length).trim();
          keepMask[k] = false;
        } else {
          columns.push({ label: f.trim() || ('Campo ' + (columns.length + 1)), group: currentGroup || 'General' });
          keepMask[k] = true;
        }
      }
      currentSection.columns = columns;
      currentSection.keepMask = keepMask;
      state_ = 'data';
      continue;
    }

    if (state_ === 'data' && currentSection){
      const fields = splitFields(line);
      const mask = currentSection.keepMask;
      const row = [];
      const len = Math.max(fields.length, mask.length);
      for (let k = 0; k < len; k++){
        if (mask[k]) row.push(fields[k] !== undefined ? fields[k] : '');
      }
      currentSection.rows.push(row);
      continue;
    }
  }

  // post-process: strip helper mask, compute derived flags
  let globalMin = '', globalMax = '';
  for (const mod of modules){
    for (const sec of mod.sections){
      delete sec.keepMask;
      sec.isEmpty = sec.rows.length === 0;
      sec.groupSpans = buildGroupSpans(sec.columns);
      sec.emptyColSet = null; // computed lazily on first render

      // localizar columna de fecha de intervención (siempre presente en las
      // exportaciones GESI, pero se busca de forma tolerante por si falta)
      sec.dateColIndex = sec.columns.findIndex(c => normalizeLabel(c.label) === DATE_COLUMN_NAME);
      if (sec.dateColIndex !== -1){
        for (const row of sec.rows){
          const v = (row[sec.dateColIndex] || '').trim().slice(0, 10);
          if (/^\d{4}-\d{2}-\d{2}$/.test(v)){
            if (!globalMin || v < globalMin) globalMin = v;
            if (!globalMax || v > globalMax) globalMax = v;
          }
        }
      }
    }
    mod.isEmpty = mod.sections.every(s => s.isEmpty);
    mod.totalRows = mod.sections.reduce((a, s) => a + s.rows.length, 0);
    mod.shortName = shortModuleName(mod.name);
  }
  parseGesi.lastGlobalDateMin = globalMin;
  parseGesi.lastGlobalDateMax = globalMax;
  return modules;
}

function shortModuleName(name){
  return name.replace(/\s*\([^)]*\)\s*$/, '').trim();
}

function buildGroupSpans(columns){
  const spans = [];
  for (let i = 0; i < columns.length; i++){
    const g = columns[i].group;
    if (spans.length && spans[spans.length - 1].group === g){
      spans[spans.length - 1].span++;
    } else {
      spans.push({ group: g, span: 1 });
    }
  }
  return spans;
}

function computeEmptyColSet(section){
  if (section.emptyColSet) return section.emptyColSet;
  const n = section.columns.length;
  const nonEmpty = new Array(n).fill(false);
  for (const row of section.rows){
    for (let c = 0; c < n; c++){
      if (!nonEmpty[c]){
        const v = row[c];
        if (v !== undefined && v !== null && v.trim() !== '') nonEmpty[c] = true;
      }
    }
  }
  const emptySet = new Set();
  for (let c = 0; c < n; c++) if (!nonEmpty[c]) emptySet.add(c);
  section.emptyColSet = emptySet;
  return emptySet;
}

/* =========================================================================
   3. UTILIDADES DE RENDER
   ========================================================================= */
const GROUP_COLORS = ['#e63946', '#388bfd', '#2ecc71', '#9f7aea', '#f5a623', '#4cc9f0'];
function colorForGroup(groupName, seenMap){
  if (!seenMap.has(groupName)) seenMap.set(groupName, seenMap.size % GROUP_COLORS.length);
  return GROUP_COLORS[seenMap.get(groupName)];
}

function escapeHtml(str){
  if (str === undefined || str === null) return '';
  return String(str)
    .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;');
}

function showToast(msg, isError, persistent){
  const t = q('pa-toast');
  q('pa-toastMsg').textContent = msg;
  q('pa-toastIcon').innerHTML = isError
    ? '<svg width="14" height="14" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="9" stroke="#fff" stroke-width="1.8"/><path d="M12 8v5M12 16h.01" stroke="#fff" stroke-width="1.8" stroke-linecap="round"/></svg>'
    : (persistent ? '<span class="pa-spinner"></span>' : '<svg width="14" height="14" viewBox="0 0 24 24" fill="none"><path d="M5 13l4 4L19 7" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>');
  t.className = 'pa-toast show' + (isError ? ' pa-error' : '');
  if (!persistent){
    clearTimeout(showToast._t);
    showToast._t = setTimeout(() => { t.className = 'pa-toast'; }, 3200);
  }
}
function hideToast(){ q('pa-toast').className = 'pa-toast'; }

/* =========================================================================
   4. CARGA DE ARCHIVO
   ========================================================================= */
function handleFile(file){
  if (!file) return;
  showToast('Leyendo y procesando archivo…', false, true);
  const reader = new FileReader();
  reader.onerror = () => { showToast('No se pudo leer el archivo.', true); };
  reader.onload = (e) => {
    try {
      const text = decodeFileBuffer(e.target.result);
      const modules = parseGesi(text);
      if (!modules.length){
        showToast('No se reconoció la estructura GESI (Entorno / Módulo / Sección) en este archivo.', true);
        return;
      }
      state.fileName = file.name;
      state.fileSizeLabel = formatBytes(file.size);
      state.entorno = modules[0].entorno || '';
      state.modules = modules;
      state.activeModuleIndex = 0;
      state.activeSectionIndex = 0;
      state.searchTerm = '';
      state.currentPage = 1;
      state.exportSelected = new Set();
      state.dateFrom = '';
      state.dateTo = '';
      state.globalDateMin = parseGesi.lastGlobalDateMin || '';
      state.globalDateMax = parseGesi.lastGlobalDateMax || '';
      modules.forEach((m, mi) => m.sections.forEach((s, si) => state.exportSelected.add(mi + '-' + si)));

      q('pa-dropzone').style.display = 'none';
      q('pa-mainContent').style.display = 'block';
      q('pa-statusTag').textContent = modules.length + ' módulos cargados';
      q('pa-dateFrom').value = '';
      q('pa-dateTo').value = '';
      if (state.globalDateMin && state.globalDateMax){
        q('pa-dateFrom').min = state.globalDateMin; q('pa-dateFrom').max = state.globalDateMax;
        q('pa-dateTo').min = state.globalDateMin; q('pa-dateTo').max = state.globalDateMax;
        q('pa-dateHint').textContent = 'Rango disponible en el archivo: ' + state.globalDateMin + ' a ' + state.globalDateMax;
      } else {
        q('pa-dateHint').textContent = '';
      }
      hideToast();
      renderAll();
    } catch (err){
      console.error(err);
      showToast('Error al procesar el archivo: ' + err.message, true);
    }
  };
  reader.readAsArrayBuffer(file);
}

function formatBytes(bytes){
  if (bytes < 1024) return bytes + ' B';
  if (bytes < 1024*1024) return (bytes/1024).toFixed(1) + ' KB';
  return (bytes/(1024*1024)).toFixed(2) + ' MB';
}

/* =========================================================================
   5. RENDER PRINCIPAL
   ========================================================================= */
function renderAll(){
  renderFileBar();
  renderStats();
  renderExportBar();
  renderModuleTabs();
  renderModulePanel();
}

function renderFileBar(){
  q('pa-fileName').textContent = state.fileName;
  const m = state.modules;
  const totalSecciones = m.reduce((a, mod) => a + mod.sections.length, 0);
  const totalFilas = m.reduce((a, mod) => a + mod.totalRows, 0);
  q('pa-fileMeta').textContent = state.fileSizeLabel + '  ·  Entorno: ' + (state.entorno || '—') +
    '  ·  ' + m.length + ' módulos  ·  ' + totalSecciones + ' secciones  ·  ' + totalFilas.toLocaleString('es-CO') + ' registros';
}

function renderStats(){
  const m = state.modules;
  const totalSecciones = m.reduce((a, mod) => a + mod.sections.length, 0);
  const totalFilas = m.reduce((a, mod) => a + mod.totalRows, 0);
  const modulosConDatos = m.filter(mod => !mod.isEmpty).length;
  const html = [
    ['Módulos', m.length],
    ['Con datos', modulosConDatos],
    ['Secciones', totalSecciones],
    ['Registros totales', totalFilas.toLocaleString('es-CO')],
  ].map(([lbl, val]) => `<div class="adm-kpi"><div class="adm-kpi-value">${val}</div><div class="adm-kpi-label">${lbl}</div></div>`).join('');
  q('pa-statsRow').innerHTML = html;
}

function visibleModuleIndices(){
  return state.modules.map((m, i) => i).filter(i => state.showEmptyModules || !state.modules[i].isEmpty);
}
function visibleSectionIndices(mod){
  return mod.sections.map((s, i) => i).filter(i => state.showEmptySections || !mod.sections[i].isEmpty);
}

function renderModuleTabs(){
  const visible = visibleModuleIndices();
  if (!visible.includes(state.activeModuleIndex)){
    state.activeModuleIndex = visible.length ? visible[0] : 0;
    state.activeSectionIndex = 0;
  }
  const html = visible.map(i => {
    const mod = state.modules[i];
    const active = i === state.activeModuleIndex;
    return `<div class="pa-modtab ${active ? 'is-active' : ''} ${mod.isEmpty ? 'is-empty' : ''}" data-modidx="${i}">
      <span class="pa-dot"></span>
      <span>${escapeHtml(mod.shortName)}</span>
      <span class="pa-count">${mod.sections.length} sec · ${mod.totalRows.toLocaleString('es-CO')}</span>
    </div>`;
  }).join('');
  q('pa-modTabs').innerHTML = html || '<div style="padding:14px;color:var(--shell-muted);font-size:12.5px;">No hay módulos visibles con los filtros actuales.</div>';

  q('pa-modTabs').querySelectorAll('.pa-modtab').forEach(el => {
    el.addEventListener('click', () => {
      state.activeModuleIndex = parseInt(el.dataset.modidx, 10);
      state.activeSectionIndex = 0;
      state.searchTerm = '';
      state.currentPage = 1;
      q('pa-searchInput').value = '';
      renderModuleTabs();
      renderModulePanel();
    });
  });
}

function renderModulePanel(){
  const panel = q('pa-modPanel');
  const mod = state.modules[state.activeModuleIndex];
  if (!mod){
    panel.innerHTML = '<div class="pa-empty-state"><h3>Sin módulo seleccionado</h3></div>';
    return;
  }
  const visibleSecs = visibleSectionIndices(mod);
  if (!visibleSecs.includes(state.activeSectionIndex)){
    state.activeSectionIndex = visibleSecs.length ? visibleSecs[0] : 0;
  }

  const sectabsHtml = visibleSecs.map(i => {
    const sec = mod.sections[i];
    const active = i === state.activeSectionIndex;
    return `<div class="pa-sectab ${active ? 'is-active' : ''} ${sec.isEmpty ? 'is-empty' : ''}" data-secidx="${i}">
      <span>${escapeHtml(sec.name)}</span>
      <span class="pa-n">${sec.rows.length.toLocaleString('es-CO')}</span>
    </div>`;
  }).join('');

  panel.innerHTML = `
    <div class="pa-sectabs">${sectabsHtml || '<span style="font-size:12.5px;color:var(--shell-muted);">No hay secciones visibles con los filtros actuales.</span>'}</div>
    <div id="pa-tableArea"></div>
  `;

  panel.querySelectorAll('.pa-sectab').forEach(el => {
    el.addEventListener('click', () => {
      state.activeSectionIndex = parseInt(el.dataset.secidx, 10);
      state.searchTerm = '';
      state.currentPage = 1;
      q('pa-searchInput').value = '';
      renderModulePanel();
    });
  });

  renderTable();
}

function dateInRange(dateStr, from, to){
  const v = (dateStr || '').trim().slice(0, 10);
  if (!/^\d{4}-\d{2}-\d{2}$/.test(v)) return false; // sin fecha válida => no cumple filtro activo
  if (from && v < from) return false;
  if (to && v > to) return false;
  return true;
}

function getFilteredRows(section){
  let rows = section.rows;
  const hasDateFilter = (state.dateFrom || state.dateTo) && section.dateColIndex !== -1;
  if (hasDateFilter){
    rows = rows.filter(row => dateInRange(row[section.dateColIndex], state.dateFrom, state.dateTo));
  }
  if (state.searchTerm){
    const term = state.searchTerm.toLowerCase();
    rows = rows.filter(row => row.some(v => v && v.toLowerCase().includes(term)));
  }
  return rows;
}

function renderTable(){
  const area = q('pa-tableArea');
  const mod = state.modules[state.activeModuleIndex];
  const sec = mod ? mod.sections[state.activeSectionIndex] : null;

  if (!sec){
    area.innerHTML = `<div class="pa-empty-state">
      <svg viewBox="0 0 24 24" fill="none"><path d="M4 6h16M4 12h16M4 18h10" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
      <h3>Sin sección seleccionada</h3><p>Ajusta los filtros de visibilidad para ver contenido.</p>
    </div>`;
    return;
  }

  if (sec.rows.length === 0){
    area.innerHTML = `<div class="pa-empty-state">
      <svg viewBox="0 0 24 24" fill="none"><path d="M12 9v4M12 16.5h.01" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.6"/></svg>
      <h3>Sección vacía</h3><p>"${escapeHtml(sec.name)}" no trae registros en esta exportación (${sec.columns.length} columnas definidas).</p>
    </div>`;
    return;
  }

  const dateFilterActive = !!(state.dateFrom || state.dateTo);
  const noDateColWarning = (dateFilterActive && sec.dateColIndex === -1)
    ? `<div class="pa-no-date-col">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none"><path d="M12 9v4M12 16.5h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.6"/></svg>
        Esta sección no tiene columna "Fecha_intervencion" — se muestran todas las filas sin filtrar por fecha.
      </div>`
    : '';

  const emptyColSet = state.hideEmptyColumns ? computeEmptyColSet(sec) : new Set();
  const visibleColIdx = sec.columns.map((c, i) => i).filter(i => !emptyColSet.has(i));

  // group spans recomputed over visible columns only
  const seenGroups = new Map();
  const groupSpansVisible = [];
  for (const i of visibleColIdx){
    const g = sec.columns[i].group;
    if (groupSpansVisible.length && groupSpansVisible[groupSpansVisible.length - 1].group === g){
      groupSpansVisible[groupSpansVisible.length - 1].span++;
    } else {
      groupSpansVisible.push({ group: g, span: 1 });
    }
  }

  const filteredRows = getFilteredRows(sec);
  const pageSize = state.pageSize;
  const totalPages = Math.max(1, Math.ceil(filteredRows.length / pageSize));
  if (state.currentPage > totalPages) state.currentPage = totalPages;
  if (state.currentPage < 1) state.currentPage = 1;
  const startIdx = (state.currentPage - 1) * pageSize;
  const pageRows = filteredRows.slice(startIdx, startIdx + pageSize);

  const groupRowHtml = groupSpansVisible.map(g =>
    `<th colspan="${g.span}" style="background:${colorForGroup(g.group, seenGroups)};color:#fff;">${escapeHtml(g.group)}</th>`
  ).join('');

  const labelRowHtml = visibleColIdx.map(i => `<th>${escapeHtml(sec.columns[i].label)}</th>`).join('');

  const bodyHtml = pageRows.map((row, ri) => {
    const cells = visibleColIdx.map(ci => `<td title="${escapeHtml(row[ci])}">${escapeHtml(row[ci])}</td>`).join('');
    return `<tr><td class="pa-rownum">${startIdx + ri + 1}</td>${cells}</tr>`;
  }).join('');

  const anyRowFilterActive = state.searchTerm || (dateFilterActive && sec.dateColIndex !== -1);
  area.innerHTML = `
    ${noDateColWarning}
    <div class="pa-table-scroll">
      <table class="pa-data">
        <thead>
          <tr class="pa-group-row"><th class="pa-rownum-h">#</th>${groupRowHtml}</tr>
          <tr class="pa-label-row"><th class="pa-rownum-h">&nbsp;</th>${labelRowHtml}</tr>
        </thead>
        <tbody>${bodyHtml}</tbody>
      </table>
    </div>
    <div class="pa-table-footer">
      <div class="pa-info">
        Mostrando <b>${pageRows.length}</b> de <b>${filteredRows.length.toLocaleString('es-CO')}</b> filas
        ${anyRowFilterActive ? ' (filtradas de ' + sec.rows.length.toLocaleString('es-CO') + ')' : ''}
        &nbsp;·&nbsp; <b>${visibleColIdx.length}</b> de ${sec.columns.length} columnas
        ${emptyColSet.size ? ' (' + emptyColSet.size + ' vacías ocultas)' : ''}
      </div>
      <div class="pa-pager">
        <button id="pa-pgFirst" ${state.currentPage <= 1 ? 'disabled' : ''}>«</button>
        <button id="pa-pgPrev" ${state.currentPage <= 1 ? 'disabled' : ''}>‹</button>
        <span class="pa-page-ind">Pág. ${state.currentPage} / ${totalPages}</span>
        <button id="pa-pgNext" ${state.currentPage >= totalPages ? 'disabled' : ''}>›</button>
        <button id="pa-pgLast" ${state.currentPage >= totalPages ? 'disabled' : ''}>»</button>
      </div>
    </div>
  `;

  q('pa-pgFirst') && q('pa-pgFirst').addEventListener('click', () => { state.currentPage = 1; renderTable(); });
  q('pa-pgPrev') && q('pa-pgPrev').addEventListener('click', () => { state.currentPage--; renderTable(); });
  q('pa-pgNext') && q('pa-pgNext').addEventListener('click', () => { state.currentPage++; renderTable(); });
  q('pa-pgLast') && q('pa-pgLast').addEventListener('click', () => { state.currentPage = totalPages; renderTable(); });
}

/* =========================================================================
   6. PANEL DE EXPORTACIÓN
   ========================================================================= */
function exportSummaryText(){
  const totalSel = state.exportSelected.size;
  const totalAvail = state.modules.reduce((a, m) => a + m.sections.length, 0);
  let txt = `${totalSel} de ${totalAvail} secciones seleccionadas · una hoja por módulo-sección + hoja índice`;
  if (state.dateFrom || state.dateTo){
    txt += ` · fecha_intervencion ${state.dateFrom || '…'} a ${state.dateTo || '…'}`;
  }
  return txt;
}

function renderExportBar(){
  const container = q('pa-exportBarContainer');

  container.innerHTML = `
    <div class="adm-card pa-export-bar">
      <details class="pa-export-details-toggle" id="pa-exportDetails" ${state.exportOpen ? 'open' : ''}>
        <summary>
          <div class="pa-export-bar-head">
            <div class="pa-left">
              <svg viewBox="0 0 24 24" fill="none"><path d="M12 3v12M12 15l-4-4M12 15l4-4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
              <div>
                <h3>Exportar a Excel</h3>
                <p>${exportSummaryText()}</p>
              </div>
            </div>
            <div class="pa-export-actions" onclick="event.preventDefault()">
              <button class="adm-btn adm-btn-ghost pa-btn-sm" id="pa-toggleExportPanelBtn" type="button">Elegir qué exportar</button>
              <button class="adm-btn adm-btn-primary" id="pa-exportBtn" type="button">
                <svg viewBox="0 0 24 24" fill="none" width="15" height="15" style="vertical-align:-2px;margin-right:5px;"><path d="M12 3v12M12 15l-4-4M12 15l4-4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                Descargar Excel
              </button>
            </div>
          </div>
        </summary>
        <div class="pa-export-details">
          <label class="pa-toggle" style="margin-bottom:12px;">
            <input type="checkbox" id="pa-exportHideEmptyColsChk" ${state.exportHideEmptyCols ? 'checked' : ''}>
            <span class="pa-track"></span>
            <span class="pa-lbl">Ocultar columnas vacías en el Excel exportado</span>
          </label>
          <div style="display:flex;gap:10px;margin-bottom:12px;">
            <button class="adm-btn adm-btn-ghost pa-btn-sm" id="pa-selectAllBtn" type="button">Seleccionar todo</button>
            <button class="adm-btn adm-btn-ghost pa-btn-sm" id="pa-selectNoneBtn" type="button">Ninguno</button>
            <button class="adm-btn adm-btn-ghost pa-btn-sm" id="pa-selectWithDataBtn" type="button">Solo con datos</button>
          </div>
          <div class="pa-export-tree" id="pa-exportTree"></div>
        </div>
      </details>
    </div>
  `;

  const detailsEl = q('pa-exportDetails');
  detailsEl.addEventListener('toggle', () => { state.exportOpen = detailsEl.open; });

  q('pa-toggleExportPanelBtn').addEventListener('click', (e) => {
    e.preventDefault();
    detailsEl.open = !detailsEl.open;
    state.exportOpen = detailsEl.open;
  });

  renderExportTree();

  q('pa-exportHideEmptyColsChk').addEventListener('change', (e) => {
    state.exportHideEmptyCols = e.target.checked;
  });
  q('pa-selectAllBtn').addEventListener('click', () => {
    state.modules.forEach((m, mi) => m.sections.forEach((s, si) => state.exportSelected.add(mi + '-' + si)));
    renderExportBar(); state.exportOpen = true; q('pa-exportDetails').open = true;
  });
  q('pa-selectNoneBtn').addEventListener('click', () => {
    state.exportSelected.clear();
    renderExportBar(); state.exportOpen = true; q('pa-exportDetails').open = true;
  });
  q('pa-selectWithDataBtn').addEventListener('click', () => {
    state.exportSelected.clear();
    state.modules.forEach((m, mi) => m.sections.forEach((s, si) => { if (!s.isEmpty) state.exportSelected.add(mi + '-' + si); }));
    renderExportBar(); state.exportOpen = true; q('pa-exportDetails').open = true;
  });
  q('pa-exportBtn').addEventListener('click', onExportClick);
}

function renderExportTree(){
  const tree = q('pa-exportTree');
  tree.innerHTML = state.modules.map((mod, mi) => {
    const modChecked = mod.sections.every((s, si) => state.exportSelected.has(mi + '-' + si));
    const secHtml = mod.sections.map((sec, si) => {
      const key = mi + '-' + si;
      const checked = state.exportSelected.has(key);
      return `<label>
        <input type="checkbox" data-key="${key}" class="pa-secChk" ${checked ? 'checked' : ''}>
        <span>${escapeHtml(sec.name)}</span>
        ${sec.isEmpty ? '<span class="adm-badge adm-badge-red">vacía</span>' : ''}
        <span class="pa-n">${sec.rows.length.toLocaleString('es-CO')}</span>
      </label>`;
    }).join('');
    return `<div class="pa-export-mod-card">
      <div class="pa-mhead">
        <label>
          <input type="checkbox" class="pa-modChk" data-modidx="${mi}" ${modChecked ? 'checked' : ''}>
          <span>${escapeHtml(mod.shortName)}</span>
          ${mod.isEmpty ? '<span class="adm-badge adm-badge-red">vacío</span>' : ''}
        </label>
      </div>
      <div class="pa-export-sec-list">${secHtml}</div>
    </div>`;
  }).join('');

  tree.querySelectorAll('.pa-modChk').forEach(chk => {
    chk.addEventListener('change', () => {
      const mi = parseInt(chk.dataset.modidx, 10);
      const mod = state.modules[mi];
      mod.sections.forEach((s, si) => {
        const key = mi + '-' + si;
        if (chk.checked) state.exportSelected.add(key); else state.exportSelected.delete(key);
      });
      renderExportTree();
      updateExportHeadCount();
    });
  });
  tree.querySelectorAll('.pa-secChk').forEach(chk => {
    chk.addEventListener('change', () => {
      const key = chk.dataset.key;
      if (chk.checked) state.exportSelected.add(key); else state.exportSelected.delete(key);
      renderExportTree();
      updateExportHeadCount();
    });
  });
}

function updateExportHeadCount(){
  const p = document.querySelector('.pa-export-bar-head p');
  if (p) p.textContent = exportSummaryText();
}

/* =========================================================================
   7. EXPORTACIÓN A EXCEL (ExcelJS)
   ========================================================================= */
function safeSheetName(baseName, usedNames){
  let name = baseName.replace(/[:\\\/\?\*\[\]]/g, ' ').replace(/\s+/g, ' ').trim();
  if (name.length > 31) name = name.slice(0, 31).trim();
  if (!name) name = 'Hoja';
  let final = name, i = 2;
  while (usedNames.has(final.toLowerCase())){
    const suffix = ' (' + i + ')';
    final = name.slice(0, Math.max(1, 31 - suffix.length)) + suffix;
    i++;
  }
  usedNames.add(final.toLowerCase());
  return final;
}

async function onExportClick(){
  if (typeof ExcelJS === 'undefined'){
    showToast('No se pudo cargar la librería de Excel (ExcelJS). Verifica tu conexión a internet.', true);
    return;
  }
  if (state.exportSelected.size === 0){
    showToast('Selecciona al menos una sección para exportar.', true);
    return;
  }
  const btn = q('pa-exportBtn');
  btn.disabled = true;
  showToast('Generando Excel…', false, true);

  // yield to the browser so the toast can paint before heavy work starts
  await new Promise(r => setTimeout(r, 30));

  try {
    const workbook = new ExcelJS.Workbook();
    workbook.creator = 'Visor GESI · Trakio';
    workbook.created = new Date();

    const dateFilterActive = !!(state.dateFrom || state.dateTo);

    const usedNames = new Set();
    const indexSheet = workbook.addWorksheet(safeSheetName('Índice', usedNames));
    const indexCols = [
      { header: '#', key: 'n', width: 5 },
      { header: 'Entorno', key: 'entorno', width: 14 },
      { header: 'Módulo', key: 'modulo', width: 42 },
      { header: 'Sección', key: 'seccion', width: 38 },
      { header: 'Filas exportadas', key: 'filas', width: 15 },
    ];
    if (dateFilterActive) indexCols.push({ header: 'Filas totales (sin filtro)', key: 'filasTotales', width: 20 });
    indexCols.push({ header: 'Columnas', key: 'cols', width: 10 }, { header: 'Hoja Excel', key: 'hoja', width: 30 });
    indexSheet.columns = indexCols;
    styleHeaderRow(indexSheet.getRow(1));
    if (dateFilterActive){
      const noteRow = indexSheet.addRow([`Filtro de fecha_intervencion aplicado: ${state.dateFrom || '(sin límite inferior)'} a ${state.dateTo || '(sin límite superior)'}`]);
      indexSheet.mergeCells(noteRow.number, 1, noteRow.number, indexCols.length);
      noteRow.getCell(1).font = { italic: true, color: { argb: 'FF5C6D6A' } };
    }

    let sheetCounter = 1;
    const indexRows = [];
    let modIdx = -1;
    for (const mod of state.modules){
      modIdx++;
      let secIdx = -1;
      for (const sec of mod.sections){
        secIdx++;
        const key = modIdx + '-' + secIdx;
        if (!state.exportSelected.has(key)) continue;

        const emptyColSet = state.exportHideEmptyCols ? computeEmptyColSet(sec) : new Set();
        const visibleColIdx = sec.columns.map((c, i) => i).filter(i => !emptyColSet.has(i));

        let rowsToExport = sec.rows;
        if (dateFilterActive && sec.dateColIndex !== -1){
          rowsToExport = sec.rows.filter(row => dateInRange(row[sec.dateColIndex], state.dateFrom, state.dateTo));
        }

        const sheetName = safeSheetName(mod.shortName + ' - ' + sec.name, usedNames);
        const ws = workbook.addWorksheet(sheetName, { views: [{ state: 'frozen', ySplit: 2 }] });

        // Group header row (row 1) with merges
        const seenGroups = new Map();
        let col = 1;
        let i = 0;
        while (i < visibleColIdx.length){
          const g = sec.columns[visibleColIdx[i]].group;
          let span = 1;
          while (i + span < visibleColIdx.length && sec.columns[visibleColIdx[i + span]].group === g) span++;
          const cell = ws.getRow(1).getCell(col);
          cell.value = g;
          if (span > 1) ws.mergeCells(1, col, 1, col + span - 1);
          cell.alignment = { horizontal: 'center', vertical: 'middle' };
          cell.font = { bold: true, color: { argb: 'FFFFFFFF' }, size: 10 };
          cell.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: groupArgb(g, seenGroups) } };
          col += span;
          i += span;
        }

        // Column label row (row 2)
        const labelRow = ws.getRow(2);
        visibleColIdx.forEach((ci, idx) => {
          const cell = labelRow.getCell(idx + 1);
          cell.value = sec.columns[ci].label;
          cell.font = { bold: true, color: { argb: 'FFFFFFFF' }, size: 10 };
          cell.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FF0F766E' } };
          cell.alignment = { vertical: 'middle', wrapText: true };
        });
        labelRow.height = 26;

        // Data rows (bulk)
        const dataMatrix = rowsToExport.map(row => visibleColIdx.map(ci => row[ci] !== undefined ? row[ci] : ''));
        if (dataMatrix.length){
          ws.addRows(dataMatrix);
        }

        // column widths
        visibleColIdx.forEach((ci, idx) => {
          const label = sec.columns[ci].label || '';
          let maxLen = label.length;
          const sampleCount = Math.min(dataMatrix.length, 150);
          for (let r = 0; r < sampleCount; r++){
            const v = dataMatrix[r][idx];
            if (v && v.length > maxLen) maxLen = v.length;
          }
          ws.getColumn(idx + 1).width = Math.max(10, Math.min(maxLen + 2, 42));
        });

        if (dataMatrix.length){
          ws.autoFilter = { from: { row: 2, column: 1 }, to: { row: 2, column: visibleColIdx.length } };
        }

        const indexEntry = {
          n: sheetCounter++, entorno: mod.entorno || state.entorno, modulo: mod.name, seccion: sec.name,
          filas: rowsToExport.length, cols: visibleColIdx.length, hoja: sheetName
        };
        if (dateFilterActive) indexEntry.filasTotales = sec.rows.length;
        indexRows.push(indexEntry);
      }
    }

    indexRows.forEach(r => indexSheet.addRow(r));
    indexSheet.eachRow((row, rowNum) => { if (rowNum > 1) row.getCell(1).alignment = { horizontal: 'right' }; });

    if (indexRows.length === 0){
      showToast('No se generaron hojas: revisa la selección de secciones.', true);
      btn.disabled = false;
      return;
    }

    const buffer = await workbook.xlsx.writeBuffer();
    const blob = new Blob([buffer], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    const baseName = (state.fileName || 'GESI').replace(/\.[^.]+$/, '');
    const stamp = new Date().toISOString().slice(0, 16).replace(/[-:T]/g, '').replace(/(\d{8})(\d{4})/, '$1_$2');
    a.href = url;
    a.download = baseName + '_por_modulos_' + stamp + '.xlsx';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);

    showToast('Excel generado: ' + indexRows.length + ' hojas listas.');
  } catch (err){
    console.error(err);
    showToast('Error generando el Excel: ' + err.message, true);
  } finally {
    btn.disabled = false;
  }
}

function styleHeaderRow(row){
  row.eachCell(cell => {
    cell.font = { bold: true, color: { argb: 'FFFFFFFF' } };
    cell.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FF0F766E' } };
  });
}

const GROUP_ARGB = ['FF0F766E', 'FF0D6B63', 'FF125E56', 'FF0A3D38', 'FF155E56', 'FF1A6B60'];
function groupArgb(groupName, seenMap){
  if (!seenMap.has(groupName)) seenMap.set(groupName, seenMap.size % GROUP_ARGB.length);
  return GROUP_ARGB[seenMap.get(groupName)];
}

/* =========================================================================
   8. EVENTOS GLOBALES / INIT
   ========================================================================= */
function init(){
  const dropzone = q('pa-dropzone');
  const fileInput = q('pa-fileInput');

  q('pa-pickFileBtn').addEventListener('click', () => fileInput.click());
  dropzone.addEventListener('click', (e) => { if (e.target === dropzone || e.target.closest('.pa-dropzone-icon, h2, p')) fileInput.click(); });
  fileInput.addEventListener('change', (e) => { if (e.target.files[0]) handleFile(e.target.files[0]); });

  ['dragenter', 'dragover'].forEach(evt => dropzone.addEventListener(evt, (e) => { e.preventDefault(); dropzone.classList.add('pa-drag'); }));
  ['dragleave', 'drop'].forEach(evt => dropzone.addEventListener(evt, (e) => { e.preventDefault(); dropzone.classList.remove('pa-drag'); }));
  dropzone.addEventListener('drop', (e) => { if (e.dataTransfer.files[0]) handleFile(e.dataTransfer.files[0]); });

  q('pa-changeFileBtn').addEventListener('click', () => {
    q('pa-mainContent').style.display = 'none';
    q('pa-dropzone').style.display = 'block';
    q('pa-statusTag').textContent = 'Sin archivo cargado';
    fileInput.value = '';
  });

  let searchDebounce;
  q('pa-searchInput').addEventListener('input', (e) => {
    clearTimeout(searchDebounce);
    searchDebounce = setTimeout(() => {
      state.searchTerm = e.target.value.trim();
      state.currentPage = 1;
      renderTable();
    }, 180);
  });

  q('pa-toggleEmptyModules').addEventListener('change', (e) => {
    state.showEmptyModules = e.target.checked;
    renderModuleTabs();
    renderModulePanel();
  });
  q('pa-toggleEmptySections').addEventListener('change', (e) => {
    state.showEmptySections = e.target.checked;
    renderModulePanel();
  });
  q('pa-toggleEmptyCols').addEventListener('change', (e) => {
    state.hideEmptyColumns = e.target.checked;
    renderTable();
  });
  q('pa-pageSizeSelect').addEventListener('change', (e) => {
    state.pageSize = parseInt(e.target.value, 10);
    state.currentPage = 1;
    renderTable();
  });

  q('pa-dateFrom').addEventListener('change', (e) => {
    state.dateFrom = e.target.value || '';
    state.currentPage = 1;
    syncClearDatesBtn();
    renderTable();
    updateExportHeadCount();
  });
  q('pa-dateTo').addEventListener('change', (e) => {
    state.dateTo = e.target.value || '';
    state.currentPage = 1;
    syncClearDatesBtn();
    renderTable();
    updateExportHeadCount();
  });
  q('pa-clearDatesBtn').addEventListener('click', () => {
    state.dateFrom = ''; state.dateTo = '';
    q('pa-dateFrom').value = ''; q('pa-dateTo').value = '';
    state.currentPage = 1;
    syncClearDatesBtn();
    renderTable();
    updateExportHeadCount();
  });
}

function syncClearDatesBtn(){
  const btn = q('pa-clearDatesBtn');
  if (btn) btn.disabled = !(state.dateFrom || state.dateTo);
}

init();
})();
</script>

@endsection
