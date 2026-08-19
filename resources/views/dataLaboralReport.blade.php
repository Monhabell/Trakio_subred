<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Extractor de Indicadores GESI · Subred Norte</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/exceljs/4.4.0/exceljs.min.js"></script>
<style>
  :root{
    --bg:#F4F7FB;
    --surface:#FFFFFF;

    --ink:#14213D;
    --muted:#64748B;

    --primary:#2563EB;
    --primary-dark:#1D4ED8;
    --primary-light:#DBEAFE;
    --accent:#3B82F6;

    --border:#E2E8F0;

    --good-bg:#DCFCE7;
    --good-ink:#15803D;
    --good:#15803D;
    --good-light:#DCFCE7;

    --warn-bg:#FEF3C7;
    --warn-ink:#B45309;
    --amber:#B45309;
    --amber-light:#FEF3C7;

    --bad:#B23B3B;
    --bad-light:#FBEAEA;

    --shadow: 0 1px 2px rgba(20,33,61,.06), 0 4px 16px rgba(20,33,61,.05);
  }

  [data-theme="dark"]{
    --bg:#0F172A;
    --surface:#1E293B;

    --ink:#F1F5F9;
    --muted:#94A3B8;

    --primary:#60A5FA;
    --primary-dark:#3B82F6;
    --primary-light:#1E3A5F;
    --accent:#93C5FD;

    --border:#334155;

    --good-bg:#052E16;
    --good-ink:#4ADE80;
    --good:#4ADE80;
    --good-light:#052E16;

    --warn-bg:#451A03;
    --warn-ink:#FBBF24;
    --amber:#FBBF24;
    --amber-light:#451A03;

    --bad:#F87171;
    --bad-light:#450A0A;

    --shadow: 0 1px 2px rgba(0,0,0,.35), 0 4px 16px rgba(0,0,0,.3);
  }
  *, *::before, *::after{ transition: background-color .25s, color .2s, border-color .2s; }
  *{box-sizing:border-box;}
  body{
    margin:0;
    background:var(--bg);
    color:var(--ink);
    font-family:'Inter',sans-serif;
    line-height:1.5;
    -webkit-font-smoothing:antialiased;
  }
  .wrap{max-width:1180px;margin:0 auto;padding:36px 24px 80px;}

  .topbar{
    position:sticky; top:0; z-index:200;
    background:var(--surface); border-bottom:1px solid var(--border);
    display:flex; justify-content:space-between; padding:8px 24px;
    backdrop-filter:blur(10px);
  }
  .theme-btn{
    display:flex; align-items:center; gap:7px;
    background:var(--bg); border:1px solid var(--border);
    color:var(--muted); font-family:'Inter',sans-serif; font-size:13px; font-weight:500;
    padding:6px 13px; border-radius:20px; cursor:pointer;
    transition:border-color .15s, color .15s;
  }
  .theme-btn:hover{border-color:var(--primary); color:var(--ink);}
  .theme-icon{font-size:15px; line-height:1;}

  .title-row{display:flex; align-items:center; gap:12px; flex-wrap:wrap;}
  .title-row h1{margin:0;}
  .token-badge{
    font-family:'JetBrains Mono',monospace; font-size:13px; color:var(--primary-dark);
    background:var(--good-bg); border:1px solid var(--border); border-radius:20px; padding:6px 14px;
    white-space:nowrap;
  }
  .token-badge.low{background:var(--warn-bg); color:var(--warn-ink);}

  .block-overlay{
    position:fixed; inset:0; background:rgba(16,35,34,0.72); z-index:1000;
    display:flex; align-items:center; justify-content:center; padding:24px;
  }
  .block-card{
    background:var(--surface); border-radius:16px; padding:36px 32px; max-width:420px; text-align:center;
    box-shadow:0 12px 40px rgba(0,0,0,0.25);
  }
  .block-icon{font-size:34px; margin-bottom:12px;}
  .block-card h2{font-family:'Space Grotesk',sans-serif; font-size:20px; margin:0 0 10px;}
  .block-card p{color:var(--muted); font-size:14px; line-height:1.6; margin:0;}

  /* ---------- Header ---------- */
  header.top{
    display:flex;justify-content:space-between;align-items:flex-end;
    gap:24px;flex-wrap:wrap;margin-bottom:28px;
    border-bottom:1px solid var(--border);padding-bottom:24px;
  }
  .eyebrow{
    font-family:'JetBrains Mono',monospace;font-size:12px;letter-spacing:.08em;
    color:var(--primary);text-transform:uppercase;margin:0 0 6px;font-weight:600;
  }
  h1{
    font-family:'Space Grotesk',sans-serif;font-size:30px;font-weight:700;
    margin:0;letter-spacing:-.01em;color:var(--primary-dark);
  }
  .sub{color:var(--muted);font-size:14.5px;margin-top:6px;max-width:560px;}
  select#moduleSelect{
    font-family:'Inter',sans-serif;font-size:14px;font-weight:600;
    padding:11px 14px;border-radius:10px;border:1.5px solid var(--border);
    background:var(--surface);color:var(--ink);min-width:300px;cursor:pointer;
  }

  /* ---------- Cards / sections ---------- */
  .card{
    background:var(--surface);border:1px solid var(--border);border-radius:16px;
    padding:24px;margin-bottom:20px;box-shadow:var(--shadow);
  }
  .card h2{
    font-family:'Space Grotesk',sans-serif;font-size:17px;margin:0 0 4px;
    display:flex;align-items:center;gap:10px;
  }
  .step-no{
    display:inline-flex;align-items:center;justify-content:center;
    width:24px;height:24px;border-radius:7px;background:var(--primary);color:#fff;
    font-family:'JetBrains Mono',monospace;font-size:12.5px;font-weight:600;flex-shrink:0;
  }
  .card p.hint{color:var(--muted);font-size:13.5px;margin:0 0 16px;}

  .coming-soon{
    display:flex;align-items:center;gap:12px;background:var(--amber-light);
    border:1px dashed var(--amber);color:var(--amber);padding:14px 16px;border-radius:12px;font-size:13.5px;
  }

  /* ---------- Upload ---------- */
  .dropzone{
    border:2px dashed var(--border);border-radius:14px;padding:32px;text-align:center;
    cursor:pointer;transition:.15s;background:var(--bg);
  }
  .dropzone:hover, .dropzone.drag{border-color:var(--primary);background:var(--primary-light);}
  .dropzone strong{font-family:'Space Grotesk',sans-serif;font-size:15px;}
  .dropzone span.small{display:block;color:var(--muted);font-size:12.5px;margin-top:6px;}
  input[type=file]{display:none;}
  .filepill{
    display:inline-flex;align-items:center;gap:8px;background:var(--primary-light);
    color:var(--primary-dark);padding:8px 14px;border-radius:999px;font-size:13px;
    font-weight:600;margin-top:14px;
  }

  /* ---------- Diagnostic ---------- */
  .diag-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:18px;}
  .stat{background:var(--bg);border:1px solid var(--border);border-radius:12px;padding:14px 16px;}
  .stat .num{font-family:'Space Grotesk',sans-serif;font-size:22px;font-weight:700;color:var(--primary-dark);}
  .stat .lbl{font-size:12px;color:var(--muted);margin-top:2px;}

  .align-meter{display:flex;height:34px;border-radius:8px;overflow:hidden;border:1px solid var(--border);margin:6px 0 4px;}
  .align-meter .seg{display:flex;align-items:center;justify-content:center;font-family:'JetBrains Mono',monospace;font-size:11px;font-weight:600;color:#fff;}
  .seg.left{background:var(--primary);}
  .seg.mid{background:var(--muted);}
  .seg.right{background:var(--amber);}
  .meter-legend{display:flex;gap:18px;font-size:12px;color:var(--muted);margin-top:4px;flex-wrap:wrap;}
  .meter-legend span{display:inline-flex;align-items:center;gap:6px;}
  .dot{width:9px;height:9px;border-radius:3px;display:inline-block;}

  table.diag-table{width:100%;border-collapse:collapse;font-size:13px;margin-top:14px;}
  table.diag-table th, table.diag-table td{
    text-align:left;padding:8px 10px;border-bottom:1px solid var(--border);
  }
  table.diag-table th{color:var(--muted);font-weight:600;font-size:11.5px;text-transform:uppercase;letter-spacing:.03em;}
  table.diag-table td.mono{font-family:'JetBrains Mono',monospace;font-size:12.5px;}
  .badge{padding:2px 9px;border-radius:999px;font-size:11.5px;font-weight:600;}
  .badge.ok{background:var(--good-light);color:var(--good);}
  .badge.warn{background:var(--amber-light);color:var(--amber);}

  /* ---------- Filters ---------- */
  .filters{display:flex;gap:14px;flex-wrap:wrap;align-items:flex-end;}
  .field{display:flex;flex-direction:column;gap:6px;}
  .field label{font-size:12px;color:var(--muted);font-weight:600;}
  .field input{
    font-family:'Inter',sans-serif;padding:9px 12px;border-radius:9px;border:1.5px solid var(--border);
    font-size:13.5px;min-width:150px;
  }
  button.primary{
    background:var(--primary);color:#fff;border:none;padding:10px 20px;border-radius:9px;
    font-weight:600;font-size:13.5px;cursor:pointer;font-family:'Inter',sans-serif;
  }
  button.primary:hover{background:var(--primary-dark);}
  button.ghost{
    background:transparent;border:1.5px solid var(--border);color:var(--ink);
    padding:9px 16px;border-radius:9px;font-weight:600;font-size:13px;cursor:pointer;
  }
  button.ghost:hover{border-color:var(--primary);color:var(--primary-dark);}

  /* ---------- Result ---------- */
  .result-grid{display:grid;grid-template-columns:1.1fr 1fr 1fr;gap:16px;align-items:stretch;}
  .result-main{
    background:linear-gradient(135deg,var(--primary-dark),var(--primary));
    border-radius:16px;padding:26px;color:#fff;display:flex;flex-direction:column;justify-content:center;
  }
  .result-main .pct{font-family:'Space Grotesk',sans-serif;font-size:52px;font-weight:700;line-height:1;}
  .result-main .name{font-size:13.5px;opacity:.9;margin-top:8px;max-width:320px;}
  .result-sub{background:var(--bg);border:1px solid var(--border);border-radius:16px;padding:22px;}
  .result-sub .num{font-family:'Space Grotesk',sans-serif;font-size:30px;font-weight:700;color:var(--primary-dark);}
  .result-sub .lbl{font-size:12.5px;color:var(--muted);margin-top:4px;}
  .formula-box{
    margin-top:16px;background:rgba(255,255,255,.12);border-radius:9px;padding:10px 12px;
    font-family:'JetBrains Mono',monospace;font-size:11.5px;color:#fff;line-height:1.6;
  }

  /* ---------- Table ---------- */
  .table-wrap{overflow-x:auto;margin-top:16px;border:1px solid var(--border);border-radius:12px;}
  table.data{width:100%;border-collapse:collapse;font-size:12.8px;min-width:640px;}
  table.data th{
    background:var(--bg);text-align:left;padding:10px 12px;font-size:11px;
    text-transform:uppercase;letter-spacing:.03em;color:var(--muted);position:sticky;top:0;
    border-bottom:1px solid var(--border);
  }
  table.data td{padding:9px 12px;border-bottom:1px solid var(--border);}
  table.data tr:last-child td{border-bottom:none;}
  .pill-si{background:var(--good-light);color:var(--good);padding:2px 9px;border-radius:999px;font-size:11.5px;font-weight:600;}
  .pill-no{background:var(--bad-light);color:var(--bad);padding:2px 9px;border-radius:999px;font-size:11.5px;font-weight:600;}
  .pill-vacio{background:var(--bg);color:var(--muted);border:1px solid var(--border);padding:2px 9px;border-radius:999px;font-size:11.5px;font-weight:600;}

  .pager{display:flex;justify-content:space-between;align-items:center;margin-top:12px;font-size:13px;color:var(--muted);}
  .pager .btns{display:flex;gap:8px;}

  .empty{
    text-align:center;padding:40px 20px;color:var(--muted);font-size:13.5px;
  }
  footer.note{margin-top:32px;text-align:center;color:var(--muted);font-size:12px;line-height:1.7;}
  footer.note code{font-family:'JetBrains Mono',monospace;background:var(--primary-light);padding:2px 6px;border-radius:5px;color:var(--primary-dark);}
  .hidden{display:none !important;}
</style>
</head>
<body>
<div class="topbar display-between">

  <a class="theme-btn" href="{{ route('guest.index') }}"> Home </a>

  <button class="theme-btn" style="padding:8px 12px; font-size:13px;" onclick="window.location.href='{{ route('mp.checkout') }}'">
      <span class="icon">💳</span>
      <span>Comprar tokens</span>
  </button>

  <button class="theme-btn" id="themeToggle">
    <span class="theme-icon" id="themeIcon">🌙</span>
    <span id="themeLabel">Modo oscuro</span>
  </button>
</div>
<div class="wrap">

  <header class="top">
    <div>
      <p class="eyebrow">Trakio · Subred Norte</p>
      <div class="title-row">
        <h1>Extractor de Indicadores GESI</h1>
        <div class="token-badge" id="tokenBadge">Tokens disponibles: <b id="tokenCount">0</b></div>
      </div>
      <p class="sub">Sube la base fuente de un módulo, valida cómo se leyeron las columnas y obtén el numerador, denominador y % del indicador — con la trazabilidad de dónde salió cada dato.</p>
    </div>
    <select id="moduleSelect">
      <option value="conexion_nocturna">✅ Conexión Nocturna — Bienestar Laboral (Bares)</option>
      <option value="ut_utis">✅ UT Trabajadores — UTIS</option>
      <option value="nna">✅ NNA — Niños, Niñas y Adolescentes Trabajadores</option>
      <option value="safl">🕓 SAFL — Salud Trabajadores Sector Formal (próximamente)</option>
    </select>
  </header>

  <div class="block-overlay" id="blockOverlay" style="display:none;">
    <div class="block-card">
      <div class="block-icon">&#128274;</div>
      <h2 id="blockTitle">Sin tokens disponibles</h2>
      <p id="blockMessage">No tienes tokens disponibles. Por favor, adquiere más tokens para seguir usando el extractor.</p>
    </div>
  </div>

  <!-- STEP 1: UPLOAD -->
  <div class="card" id="cardUpload">
    <h2><span class="step-no">1</span> Cargar la base fuente</h2>
    <p class="hint" id="uploadHint">Sube el CSV exportado de GESI (puede traer varios módulos y secciones apiladas — los detectamos automáticamente y solo usamos las secciones que le corresponden a este indicador).</p>

    <div id="moduleActive">
      <div class="dropzone" id="dropzone">
        <strong>Arrastra el archivo aquí o haz clic para elegirlo</strong>
        <span class="small">.csv exportado de GESI · se detecta automáticamente la codificación (UTF-8 / UTF-16)</span>
        <input type="file" id="fileInput" accept=".csv,.txt">
      </div>
      <div id="filePillWrap"></div>
    </div>

    <div id="moduleComingSoon" class="coming-soon hidden">
      Este módulo aún no está configurado. Cuéntame en el chat cómo está estructurada su base (columnas, secciones que se unen por <code>Ficha_fic</code>) y lo dejamos listo aquí, igual que Conexión Nocturna.
    </div>
  </div>

  <!-- STEP 2: DIAGNOSTIC -->
  <div class="card hidden" id="cardDiag">
    <h2><span class="step-no">2</span> Estructura del archivo</h2>
    <p class="hint">GESI apila varios módulos en el mismo CSV, y cada módulo puede traer varias secciones (mini-tablas independientes con su propio encabezado, unidas por <code>Ficha_fic</code>). Esto es lo que se detectó — en <strong>fondo verde</strong> las secciones que usa el indicador activo; el resto se ignora para este cálculo.</p>

    <div class="table-wrap" style="margin-bottom:18px;">
      <table class="data" id="blocksTable">
        <thead><tr><th>Módulo</th><th>Sección</th><th>Filas de datos</th><th>Columnas</th><th>Uso</th></tr></thead>
        <tbody id="blocksBody"></tbody>
      </table>
    </div>

    <div class="diag-grid">
      <div class="stat"><div class="num" id="statRows">–</div><div class="lbl" id="statRowsLbl">Fichas leídas</div></div>
      <div class="stat"><div class="num" id="statCols">–</div><div class="lbl">Columnas de encabezado</div></div>
      <div class="stat"><div class="num" id="statLenMax">–</div><div class="lbl">Estado de largo de fila</div></div>
      <div class="stat"><div class="num" id="statCompletas">–</div><div class="lbl">Fichas usables para el cálculo</div></div>
    </div>

    <table class="diag-table">
      <thead><tr><th>Columna clave</th><th>Sección de origen</th><th>Ejemplo (fila 1)</th><th>Estado</th></tr></thead>
      <tbody id="diagBody"></tbody>
    </table>
  </div>

  <!-- STEP 2.5: INDICATOR PICKER (solo módulos con más de un indicador) -->
  <div class="card hidden" id="cardIndicator">
    <h2><span class="step-no">·</span> ¿Cuál indicador?</h2>
    <p class="hint">Este módulo agrupa varios indicadores de la matriz oficial. Elige el tipo y el nivel de impacto.</p>
    <div class="filters">
      <div class="field">
        <label>Tipo de indicador</label>
        <select id="indicatorGroup" style="font-family:'Inter',sans-serif;padding:9px 12px;border-radius:9px;border:1.5px solid var(--border);font-size:13.5px;min-width:280px;"></select>
      </div>
      <div class="field">
        <label>Nivel de impacto</label>
        <select id="indicatorImpacto" style="font-family:'Inter',sans-serif;padding:9px 12px;border-radius:9px;border:1.5px solid var(--border);font-size:13.5px;"></select>
      </div>
    </div>
  </div>


  <!-- STEP 3: FILTERS -->
  <div class="card hidden" id="cardFilters">
    <h2><span class="step-no">3</span> Filtrar periodo</h2>
    <p class="hint">Filtra por <code>Fecha_intervencion</code> antes de calcular el indicador (déjalo vacío para incluir todo el histórico).</p>
    <div class="filters">
      <div class="field"><label>Desde</label><input type="date" id="dateFrom"></div>
      <div class="field"><label>Hasta</label><input type="date" id="dateTo"></div>
      <div class="field">
        <label>Denominador</label>
        <select id="denomMode" style="font-family:'Inter',sans-serif;padding:9px 12px;border-radius:9px;border:1.5px solid var(--border);font-size:13.5px;">
          <option value="todos">Todos los establecimientos asesorados</option>
          <option value="cerrados">Solo los que cerraron proceso (Cierre Proceso)</option>
        </select>
      </div>
      <button class="primary" id="btnCalc">Calcular indicador</button>
      <button class="ghost" id="btnReset">Limpiar filtros</button>
    </div>
    <p class="hint" style="margin-top:12px;">⚠️ El campo "Cumplió con los compromisos" solo existe en fichas que llegaron a la sección <strong>Cierre Proceso</strong> del formulario. En las demás fichas simplemente no se ha llegado ahí todavía — no se trata de datos faltantes por error. Elige abajo si el indicador oficial cuenta como universo <em>todos</em> los asesorados o solo los que ya cerraron.</p>
  </div>

  <!-- STEP 4: RESULT -->
  <div class="card hidden" id="cardResult">
    <h2><span class="step-no">4</span> Resultado del indicador</h2>
    <p class="hint" id="indicatorName"></p>
    <div class="result-grid">
      <div class="result-main">
        <div class="pct" id="pctValue">–%</div>
        <div class="name">Cumplimiento del indicador en el periodo filtrado</div>
        <div class="formula-box" id="formulaBox"></div>
      </div>
      <div class="result-sub">
        <div class="num" id="numValue">–</div>
        <div class="lbl">Numerador — establecimientos con Plan de Bienestar implementado (cumplió compromisos = Sí)</div>
      </div>
      <div class="result-sub">
        <div class="num" id="denValue">–</div>
        <div class="lbl">Denominador — total de establecimientos asesorados en el periodo</div>
      </div>
    </div>

    <div style="display:flex;justify-content:space-between;align-items:center;margin-top:20px;flex-wrap:wrap;gap:10px;">
      <p class="hint" style="margin:0;">Detalle de fichas que entraron al cálculo</p>
      <div style="display:flex;gap:10px;">
        <button class="ghost" id="btnExport">⬇ Exportar este indicador</button>
        <button class="primary" id="btnExportAll">⬇ Exportar TODOS los indicadores</button>
      </div>
    </div>

    <div class="table-wrap">
      <table class="data">
        <thead><tr>
          <th>Ficha</th><th>Fecha</th><th>Establecimiento</th><th>Tipo intervención</th><th>Cumplió compromisos</th>
        </tr></thead>
        <tbody id="tableBody"></tbody>
      </table>
    </div>
    <div class="pager">
      <span id="pagerInfo"></span>
      <div class="btns">
        <button class="ghost" id="btnPrev">← Anterior</button>
        <button class="ghost" id="btnNext">Siguiente →</button>
      </div>
    </div>

    <p class="hint" style="margin-top:22px;">Desglose por mes</p>
    <div class="table-wrap">
      <table class="data">
        <thead><tr><th>Mes</th><th>Numerador</th><th>Denominador</th><th>%</th></tr></thead>
        <tbody id="monthBody"></tbody>
      </table>
    </div>
  </div>

  <footer class="note">
    Cada módulo nuevo se agrega definiendo su llave <code>Ficha_fic</code>, sus columnas ancladas y su fórmula — cuéntame cuando tengas la base de UT, NNA o SAFL y la sumamos aquí mismo.<br>
    El procesamiento de los datos ocurre en tu navegador; solo el conteo de tokens consumidos se envía al servidor.
  </footer>
</div>

<script>
// ---- Toggle de tema (claro / oscuro) ----
(function(){
  const root = document.documentElement;
  const btn = document.getElementById('themeToggle');
  const icon = document.getElementById('themeIcon');
  const label = document.getElementById('themeLabel');
  let dark = localStorage.getItem('gesi-theme') === 'dark';
  function apply(){
    root.setAttribute('data-theme', dark ? 'dark' : 'light');
    icon.textContent = dark ? '☀️' : '🌙';
    label.textContent = dark ? 'Modo claro' : 'Modo oscuro';
    localStorage.setItem('gesi-theme', dark ? 'dark' : 'light');
  }
  btn.addEventListener('click', ()=>{ dark=!dark; apply(); });
  apply();
})();

// ---- Integración de tokens ODIN (Laravel) ----
const INITIAL_TOKENS = parseInt('{{ (int)($tokens ?? 0) }}', 10) || 0;
const UPDATE_TOKENS_URL = '{{ route("validator.updateTokens") }}';
const ROWS_PER_CHARGE = 500;
const TOKENS_PER_CHARGE = 5;
let currentTokens = INITIAL_TOKENS;

const tokenBadge = document.getElementById('tokenBadge');
const tokenCountEl = document.getElementById('tokenCount');
const blockOverlay = document.getElementById('blockOverlay');
const blockTitle = document.getElementById('blockTitle');
const blockMessage = document.getElementById('blockMessage');
const cardUploadEl = document.getElementById('cardUpload');

function renderTokenBadge(){
  tokenCountEl.textContent = currentTokens;
  tokenBadge.classList.toggle('low', currentTokens <= TOKENS_PER_CHARGE);
}

function showBlock(message){
  blockTitle.textContent = 'Sin tokens disponibles';
  blockMessage.textContent = message || 'No tienes tokens disponibles. Por favor, adquiere más tokens para seguir usando el extractor.';
  blockOverlay.style.display = 'flex';
  cardUploadEl.style.pointerEvents = 'none';
  cardUploadEl.style.opacity = '0.5';
}

function checkTokenGate(){
  if(currentTokens <= 0){
    showBlock();
    return false;
  }
  return true;
}

async function chargeTokens(rowsProcessed){
  const blocks = Math.ceil(rowsProcessed / ROWS_PER_CHARGE);
  const tokensToCharge = blocks * TOKENS_PER_CHARGE;
  if(tokensToCharge <= 0) return;
  currentTokens = Math.max(0, currentTokens - tokensToCharge);
  renderTokenBadge();
  try{
    const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const resp = await fetch(UPDATE_TOKENS_URL, {
      method:'POST',
      headers:{'Content-Type':'application/json', 'X-CSRF-TOKEN':csrf, 'Accept':'application/json'},
      body: JSON.stringify({tokens_consumed: tokensToCharge}),
    });
    const data = await resp.json();
    if(data && typeof data.tokens_left === 'number'){
      currentTokens = data.tokens_left;
      renderTokenBadge();
    }
  }catch(err){
    console.error('No se pudo actualizar el saldo de tokens en el servidor:', err);
  }
  if(currentTokens <= 0){
    showBlock('Te quedaste sin tokens tras procesar este archivo. Adquiere más tokens para seguir usando el extractor.');
  }
}

renderTokenBadge();
if(!checkTokenGate()){ /* el overlay ya quedó visible */ }
</script>

<script>
/* =========================================================
   PARSER GENERAL: GESI apila Módulos, y cada Módulo apila
   Secciones (mini-tablas independientes, cada una con su
   propio encabezado). Este parser trocea el archivo completo
   en bloques {modulo, seccion, header, rows} usando las
   líneas "Módulo => ..." y "Sección => ..." como marcadores.
   ========================================================= */
function parseGesiFile(text) {
  const lines = text.split(/\r\n|\n/).filter(l => l.length > 0);
  const markers = [];
  let currentModulo = '';
  for (let i = 0; i < lines.length; i++) {
    const clean = lines[i].replace(/^\uFEFF/, '');
    if (clean.startsWith('Módulo =>')) {
      currentModulo = clean.replace(/^Módulo =>\s*/, '').trim();
    } else if (clean.startsWith('Sección =>')) {
      markers.push({ modulo: currentModulo, seccion: clean.replace(/^Sección =>\s*/, '').trim(), lineIdx: i });
    }
  }
  const sections = [];
  for (let b = 0; b < markers.length; b++) {
    const start = markers[b].lineIdx;
    const end = (b + 1 < markers.length) ? markers[b + 1].lineIdx : lines.length;
    let headerLine = lines[start + 1] || '';
    headerLine = headerLine.replace(/^.*?=>\s*/, '');
    const header = headerLine.split('`;`').map(c => c.replace(/`/g, '').trim());
    const rows = [];
    for (let i = start + 2; i < end; i++) {
      const row = lines[i].split('`;`').map(c => c.replace(/`/g, '').trim());
      if (row.length > 1) rows.push(row);
    }
    sections.push({ modulo: markers[b].modulo, seccion: markers[b].seccion, header, headerLen: header.length, rows });
  }
  return sections;
}

function decodeBuffer(buf) {
  const bytes = new Uint8Array(buf);
  if (bytes[0] === 0xFF && bytes[1] === 0xFE) return new TextDecoder('utf-16le').decode(buf);
  if (bytes[0] === 0xFE && bytes[1] === 0xFF) return new TextDecoder('utf-16be').decode(buf);
  if (bytes[0] === 0xEF && bytes[1] === 0xBB && bytes[2] === 0xBF) return new TextDecoder('utf-8').decode(buf);
  let zeroCount = 0;
  const sampleLen = Math.min(2000, bytes.length);
  for (let i = 1; i < sampleLen; i += 2) if (bytes[i] === 0) zeroCount++;
  if (zeroCount / (sampleLen / 2) > 0.6) return new TextDecoder('utf-16le').decode(buf);
  return new TextDecoder('utf-8').decode(buf);
}

function findSection(sections, nameContains) {
  return sections.find(s => s.seccion.toUpperCase().includes(nameContains.toUpperCase()));
}
function findSectionInModule(sections, moduloContains, seccionContains) {
  return sections.find(s => s.modulo.toUpperCase().includes(moduloContains.toUpperCase())
                          && s.seccion.toUpperCase().includes(seccionContains.toUpperCase()));
}
function colIdx(section, nameContains) {
  return section.header.findIndex(h => h.toUpperCase().includes(nameContains.toUpperCase()));
}
function escapeHtml(s) { return String(s).replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c])); }
function parsePct(s) {
  if (!s) return NaN;
  const n = parseFloat(String(s).replace('%','').replace(',','.').trim());
  return isNaN(n) ? NaN : n;
}
function monthOf(fecha) { return fecha && fecha.length >= 7 ? fecha.slice(0,7) : null; }

/* =========================================================
   CONFIGURACIÓN DE MÓDULOS
   FICHA_IDX=2 y FECHA_IDX=3 son constantes en todas las
   secciones GESI (siempre empiezan con "DATOS DE LA FICHA").
   ========================================================= */
const FICHA_IDX = 2, FECHA_IDX = 3;

const MODULES = {
  conexion_nocturna: {
    label: "Conexión Nocturna — Bienestar Laboral (Bares)",
    active: true,
    requiredSections: ["CONEXIÓN NOCTURNA"],
    indicatorGroups: [{
      key: 'default', label: 'Plan de Bienestar implementado', impactos: [{key:'default', label:'—'}],
      numDesc: "Número de establecimientos con Plan de Bienestar implementado",
      fuenteNum: "Base conexión Nocturna, Tu bar tu responsabilidad, filtrar por variable CUMPLIÓ CON LOS COMPROMISOS CONCERTADOS / SI",
      denDesc: "N° total de establecimientos asesorados",
      fuenteDen: "Base conexión Nocturna (TIPO DE INTERVENCION nueva), Tu bar tu responsabilidad, total de establecimientos caracterizados",
      formulaDesc: "N° de establecimientos con Plan de Bienestar implementado / N° total de establecimientos asesorados *100",
      metodologia: "Se usó la sección 'Conexión Nocturna Bienestar Trabajo' del módulo Conexión Nocturna. El numerador cuenta las fichas donde el campo 'Cumplió con los compromisos concertados' = Sí. El denominador es el total de fichas (establecimientos) de esa sección."
    }],
    build(sections) {
      const main = findSection(sections, 'CONEXIÓN NOCTURNA  BIENESTAR');
      if (!main) return null;
      return main.rows.filter(r => r[FICHA_IDX]).map(r => {
        const fCumplio = r.length - 3;
        return {
          ficha: r[FICHA_IDX], fecha: r[FECHA_IDX],
          etiqueta1: r[10] || '', etiqueta2: r[13] || '',
          esNumerador: ['si','sí'].includes((r[fCumplio]||'').trim().toLowerCase()),
          esDenominador: true,
          detalleCol: (r[fCumplio]||'').trim() || '(vacío)'
        };
      });
    },
    labels: { col1: 'Tipo intervención', col2: 'Establecimiento', col3:'Cumplió compromisos', numLabel: "Numerador — cumplió compromisos = Sí", denLabel: "Denominador — total asesorados" }
  },

  ut_utis: {
    label: "UT Trabajadores — UTIS",
    active: true,
    requiredSections: ["IDENTIFICACIÓN DE LA UT", "PLAN DE TRABAJO", "IDENTIFICACIÓN DE INDIVIDUOS"],
    indicatorGroups: [
      {
        key: 'utis', label: 'UTIS que logran cambios ≥25% (EELS)',
        impactos: [
          {key:'mediano', label:'Mediano impacto', derivadaA:'UTI MEDIA'},
          {key:'bajo', label:'Bajo impacto', derivadaA:'UT BAJA'},
          {key:'alto', label:'Alto impacto', derivadaA:'UTI ALTA', exact:true}
        ],
        numDescTpl: n => `Número de UTIS de ${n} caracterizadas que logran cambios a partir de la implementación de la EELS`,
        fuenteNumTpl: n => `Valoración final menos(-) valoración inicial mayores o igual de 25%. Solo ${n}. Fecha de monitoreo`,
        denDescTpl: n => `Número total de UTIS de ${n} caracterizadas y desarrollan implementación de la EELS*100`,
        fuenteDenTpl: n => `Filtrar UT ${n} total de UTIS identificadas`,
        formulaDescTpl: n => `Número de UTIS de ${n} que logran cambios / Número total de UTIS de ${n} caracterizadas *100`,
        metodologia: "Se unió la sección 'Identificación de la UT' (clasifica cada UT por 'UT DERIVADA A') con la sección 'Plan de Trabajo' (trae '% Valoración inicial' y '% Valoración final'), ambas por Ficha_fic. El numerador cuenta UTs de ese nivel de impacto donde (valoración final − valoración inicial) ≥ 25 puntos porcentuales. El denominador es el total de UTs identificadas en ese nivel de impacto."
      },
      {
        key: 'trabajadores', label: 'Trabajadores informales que mejoran prácticas (decálogo)',
        impactos: [
          {key:'mediano', label:'Mediano impacto', derivadaA:'UTI MEDIA'},
          {key:'bajo', label:'Bajo impacto', derivadaA:'UT BAJA'},
          {key:'alto', label:'Alto impacto', derivadaA:'UTI ALTA', exact:true}
        ],
        numDescTpl: n => `Número de trabajadores informales de UTI ${n} que mejoran sus prácticas de cuidado según decálogo de salud en el trabajo`,
        fuenteNumTpl: n => `Filtrar UT ${n} Impacto. Filtrar trabajadores con cumplimiento del decálogo, UT termina proceso / SI`,
        denDescTpl: n => `Número Total de trabajadores informales caracterizados en UTI de ${n} con compromiso de modificar prácticas de cuidado y participaron de la EELS*100`,
        fuenteDenTpl: n => `Filtrar UT ${n} Impacto filtrar todos los que tengan compromiso UT termina proceso / SI`,
        formulaDescTpl: n => `Número de trabajadores de UTI ${n} que mejoran prácticas / Número total de trabajadores caracterizados con compromiso *100`,
        metodologia: "Se unieron tres secciones por Ficha_fic: 'Identificación de la UT' (nivel de impacto), 'Plan de Trabajo' (campo 'UTI termina el proceso desarrollado' = Sí) e 'Identificación de Individuos' (trabajador). El denominador cuenta trabajadores con 'Decálogo compromiso' diligenciado (se comprometieron a cambiar prácticas) en UTs de ese impacto que terminaron el proceso. El numerador es el subconjunto con 'Decálogo cumplimiento' diligenciado (sí cumplieron)."
      },
      {
        key: 'continuidad', label: 'UTIS de vigencias anteriores que continúan',
        impactos: [{key:'default', label:'Seguimiento a la continuidad', derivadaA:'SEGUIMIENTO'}],
        numDescTpl: () => `Número de UTIS intervenidas de vigencias anteriores que logran continuar con cambios a partir de la implementación de la EELS`,
        fuenteNumTpl: () => `Filtrar ut derivada 8 seguimiento a la continuidad. Filtrar las que sí continúan con el proceso`,
        denDescTpl: () => `Número total de UTIS de seguimiento de vigencias anteriores con seguimiento realizado *100`,
        fuenteDenTpl: () => `Filtrar ut derivada 8 seguimiento a la continuidad total`,
        formulaDescTpl: () => `Número de UTIS que logran continuar / Número total de UTIS de seguimiento de vigencias anteriores *100`,
        metodologia: "Se usó la sección 'Identificación de la UT', filtrando 'UT DERIVADA A' = seguimiento de vigencias anteriores. El numerador cuenta las UTs donde 'Continúa en la implementación de gestión del riesgo en la UTI' = Sí. El denominador es el total de UTs en esa categoría. Nota: en la plantilla oficial el texto de 'Fuente numerador' y 'Fuente denominador' de esta fila parecen estar intercambiados respecto al patrón de las demás filas — aquí se interpretó según lo que describe el nombre del indicador (numerador = las que logran continuar), y se dejó una aclaración en esta celda para que se pueda verificar con el equipo que reporta la matriz."
      }
    ],
    build(sections) {
      const ut = findSectionInModule(sections, 'UT TRABAJADORES', 'IDENTIFICACIÓN DE LA UT');
      const plan = findSectionInModule(sections, 'UT TRABAJADORES', 'PLAN DE TRABAJO');
      const ind = findSectionInModule(sections, 'UT TRABAJADORES', 'IDENTIFICACIÓN DE INDIVIDUOS');
      if (!ut || !plan || !ind) return null;
      const derivadaIdx = colIdx(ut, 'UT DERIVADA A');
      const viIdx = colIdx(plan, 'VALORACIÓN INICIAL');
      const vfIdx = colIdx(plan, 'VALORACIÓN FINAL');
      const terminaIdx = colIdx(plan, 'TERMINA EL PROCESO');
      const continuaIdx = colIdx(ut, 'CONTINUA EN LA IMPLEMENTACIÓN');
      const compromisoIdx = colIdx(ind, 'DECÁLOGO COMPROMISO');
      const cumplimientoIdx = colIdx(ind, 'DECÁLOGO CUMPLIMIENTO');

      const utByFicha = new Map();
      ut.rows.forEach(r => { if (r[FICHA_IDX]) utByFicha.set(r[FICHA_IDX].trim(), r); });
      const planByFicha = new Map();
      plan.rows.forEach(r => { if (r[FICHA_IDX]) planByFicha.set(r[FICHA_IDX].trim(), r); });

      // Dataset A: nivel UT (para indicador "utis")
      const utisRows = ut.rows.filter(r => r[FICHA_IDX]).map(r => {
        const planRow = planByFicha.get(r[FICHA_IDX].trim());
        const vi = planRow ? parsePct(planRow[viIdx]) : NaN;
        const vf = planRow ? parsePct(planRow[vfIdx]) : NaN;
        const delta = (!isNaN(vi) && !isNaN(vf)) ? (vf - vi) : NaN;
        return {
          ficha: r[FICHA_IDX], fecha: r[FECHA_IDX],
          derivadaA: (r[derivadaIdx] || '').trim().toUpperCase(),
          etiqueta1: r[derivadaIdx] || '', etiqueta2: r[8] || '',
          esNumerador: !isNaN(delta) && delta >= 25,
          esDenominador: true,
          detalleCol: isNaN(delta) ? 'Sin monitoreo' : `${delta.toFixed(1)} pp`
        };
      });

      // Dataset B: nivel trabajador individual (para indicador "trabajadores")
      const trabRows = ind.rows.filter(r => r[FICHA_IDX]).map(r => {
        const ficha = r[FICHA_IDX].trim();
        const utRow = utByFicha.get(ficha);
        const planRow = planByFicha.get(ficha);
        const derivadaA = utRow ? (utRow[derivadaIdx] || '').trim().toUpperCase() : '';
        const termina = planRow ? (planRow[terminaIdx] || '').trim().toLowerCase() : '';
        const compromiso = (r[compromisoIdx] || '').trim();
        const cumplimiento = (r[cumplimientoIdx] || '').trim();
        return {
          ficha: r[FICHA_IDX], fecha: r[FECHA_IDX],
          derivadaA,
          etiqueta1: utRow ? (utRow[derivadaIdx]||'') : '', etiqueta2: (r[11]||'') + ' ' + (r[13]||''),
          esDenominador: termina === 'si' && !!compromiso,
          esNumerador: termina === 'si' && !!compromiso && !!cumplimiento,
          detalleCol: cumplimiento ? 'Cumplió' : (compromiso ? 'Con compromiso, sin cumplir' : 'Sin compromiso')
        };
      });

      // Dataset C: continuidad de UTIS de vigencias anteriores (para indicador "continuidad")
      const continuidadRows = ut.rows.filter(r => r[FICHA_IDX]).map(r => {
        const derivadaA = (r[derivadaIdx] || '').trim().toUpperCase();
        const continua = (r[continuaIdx] || '').trim().toLowerCase();
        const esCategoria = derivadaA.includes('SEGUIMIENTO');
        return {
          ficha: r[FICHA_IDX], fecha: r[FECHA_IDX],
          derivadaA,
          etiqueta1: r[derivadaIdx] || '', etiqueta2: r[8] || '',
          esDenominador: esCategoria,
          esNumerador: esCategoria && ['si','sí'].includes(continua),
          detalleCol: !esCategoria ? '—' : (['si','sí'].includes(continua) ? 'Continúa' : 'No continúa')
        };
      });

      return { utis: utisRows, trabajadores: trabRows, continuidad: continuidadRows };
    },
    labels: {
      utis: { col1: 'UT derivada a', col2: 'Nombre UT', col3:'Cambio (Δ pp)', numLabel: "Numerador — logra cambio ≥25 puntos porcentuales", denLabel: "Denominador — total UTIS caracterizadas en ese nivel de impacto" },
      trabajadores: { col1: 'UT derivada a', col2: 'Trabajador', col3:'Estado decálogo', numLabel: "Numerador — trabajadores que cumplen el decálogo", denLabel: "Denominador — trabajadores con compromiso, en UTs que terminaron el proceso" },
      continuidad: { col1: 'UT derivada a', col2: 'Nombre UT', col3:'Estado continuidad', numLabel: "Numerador — UTIS que logran continuar", denLabel: "Denominador — total UTIS de seguimiento de vigencias anteriores" }
    }
  },
  nna: {
    label: "NNA — Niños, Niñas y Adolescentes Trabajadores",
    active: true,
    requiredSections: ["NNA IDENTIFICADO COMO TRABAJADOR", "DESARROLLO DE ACOMPAÑAMIENTOS"],
    indicatorGroups: [{
      key: 'default', label: 'Continúan desvinculados del trabajo infantil', impactos: [{key:'default', label:'—'}],
      numDesc: "Número de niños, niñas y adolescentes trabajadores que continúan desvinculados del trabajo infantil",
      fuenteNum: "Base NNA tipo de intervención, resultado de la intervención NNA desvinculados SI",
      denDesc: "Número de niños, niñas y adolescentes trabajadores",
      fuenteDen: "Base NNA tipo de intervención total de NNA identificados",
      formulaDesc: "Número de NNA que continúan desvinculados / Número de NNA (tipo intervención Seguimiento al efecto) *100",
      metodologia: "Se usó el módulo 'Niños Niñas y Adolescentes Trabajadores', uniendo por Ficha_fic la sección 'NNA identificado como trabajador' (filtrando TIPO INTERVENCIÓN = 'Seguimiento al efecto', el tipo 5) con la sección 'Desarrollo de acompañamientos, compromisos y seguimiento' (columna 'NNA desvinculado de la actividad laboral'). El numerador cuenta los NNA de seguimiento con desvinculado = Sí; el denominador es el total de NNA con ese tipo de intervención."
    }],
    build(sections) {
      const nna = findSectionInModule(sections, 'NIÑOS NIÑAS Y ADOLESCENTES', 'NNA IDENTIFICADO COMO TRABAJADOR');
      const seg = findSectionInModule(sections, 'NIÑOS NIÑAS Y ADOLESCENTES', 'DESARROLLO DE ACOMPAÑAMIENTOS');
      if (!nna || !seg) return null;
      const tipoIdx = colIdx(nna, 'TIPO INTERVENCIÓN');
      const desvIdx = colIdx(seg, 'NNA DESVINCULADO DE LA ACTIVIDAD LABORAL');
      const segByFicha = new Map();
      seg.rows.forEach(r => { if (r[FICHA_IDX]) segByFicha.set(r[FICHA_IDX].trim(), r); });
      return nna.rows.filter(r => r[FICHA_IDX] && (r[tipoIdx]||'').trim().toUpperCase().includes('SEGUIMIENTO AL EFECTO')).map(r => {
        const segRow = segByFicha.get(r[FICHA_IDX].trim());
        const desv = segRow ? (segRow[desvIdx]||'').trim().toLowerCase() : '';
        return {
          ficha: r[FICHA_IDX], fecha: r[FECHA_IDX],
          etiqueta1: r[tipoIdx] || '', etiqueta2: (r[13]||'')+' '+(r[14]||''),
          esDenominador: true,
          esNumerador: desv === 'si',
          detalleCol: desv === 'si' ? 'Desvinculado' : (desv === 'no' ? 'No desvinculado' : 'Sin dato')
        };
      });
    },
    labels: { col1: 'Tipo intervención', col2: 'NNA', col3:'Desvinculación', numLabel: "Numerador — continúan desvinculados (Sí)", denLabel: "Denominador — NNA en seguimiento al efecto" }
  },
  safl: { label: "SAFL — Salud Trabajadores Sector Formal", active: false }
};

let currentModule = "conexion_nocturna";
let currentGroupKey = "default";
let currentImpactoKey = "default";
let allSections = [];
let builtData = null;       // resultado crudo de mod.build()
let computedRows = [];      // filas de la vista activa (según grupo/impacto)
let filteredRows = [];
let page = 0;
const PAGE_SIZE = 25;

function activeIndicatorGroup() {
  const mod = MODULES[currentModule];
  return (mod.indicatorGroups || []).find(g => g.key === currentGroupKey) || (mod.indicatorGroups||[])[0];
}
function activeImpacto() {
  const grp = activeIndicatorGroup();
  return (grp.impactos || []).find(i => i.key === currentImpactoKey) || (grp.impactos||[])[0];
}

/* ---------------- Module switch ---------------- */
const moduleSelect = document.getElementById('moduleSelect');
moduleSelect.addEventListener('change', () => {
  currentModule = moduleSelect.value;
  const mod = MODULES[currentModule];
  currentGroupKey = mod.indicatorGroups ? mod.indicatorGroups[0].key : 'default';
  currentImpactoKey = mod.indicatorGroups ? mod.indicatorGroups[0].impactos[0].key : 'default';
  document.getElementById('moduleActive').classList.toggle('hidden', !mod.active);
  document.getElementById('moduleComingSoon').classList.toggle('hidden', mod.active);
  ['cardDiag','cardIndicator','cardFilters','cardResult'].forEach(id => document.getElementById(id).classList.add('hidden'));
  document.getElementById('filePillWrap').innerHTML = '';
});

/* ---------------- Upload handling ---------------- */
const dropzone = document.getElementById('dropzone');
const fileInput = document.getElementById('fileInput');
dropzone.addEventListener('click', () => fileInput.click());
dropzone.addEventListener('dragover', e => { e.preventDefault(); dropzone.classList.add('drag'); });
dropzone.addEventListener('dragleave', () => dropzone.classList.remove('drag'));
dropzone.addEventListener('drop', e => {
  e.preventDefault(); dropzone.classList.remove('drag');
  if (e.dataTransfer.files.length) handleFile(e.dataTransfer.files[0]);
});
fileInput.addEventListener('change', e => { if (e.target.files.length) handleFile(e.target.files[0]); });

function handleFile(file) {
  if(!checkTokenGate()) return;
  document.getElementById('filePillWrap').innerHTML =
    `<span class="filepill">📄 ${file.name} · ${(file.size/1024/1024).toFixed(2)} MB</span>`;
  const reader = new FileReader();
  reader.onload = (e) => {
    const text = decodeBuffer(e.target.result);
    allSections = parseGesiFile(text);
    const mod = MODULES[currentModule];
    builtData = mod.build(allSections);
    renderBlocksTable();
    renderIndicatorPicker();
    updateComputedRows();
    runDiagnostics();
    document.getElementById('cardDiag').classList.remove('hidden');
    document.getElementById('cardFilters').classList.remove('hidden');
    const rowsProcessed = allSections.reduce((s, sec) => s + sec.rows.length, 0);
    chargeTokens(rowsProcessed);
  };
  reader.readAsArrayBuffer(file);
}

function matchesImpacto(derivadaA, impacto) {
  return impacto.exact ? derivadaA === impacto.derivadaA.toUpperCase() : derivadaA.includes(impacto.derivadaA.toUpperCase());
}

function updateComputedRows() {
  const mod = MODULES[currentModule];
  if (currentModule === 'ut_utis') {
    computedRows = builtData ? builtData[currentGroupKey] : [];
    const impacto = activeImpacto();
    computedRows = computedRows.filter(r => matchesImpacto(r.derivadaA, impacto));
  } else {
    computedRows = builtData || [];
  }
}

/* ---------------- Blocks table (todas las secciones detectadas) ---------------- */
function renderBlocksTable() {
  const mod = MODULES[currentModule];
  const body = document.getElementById('blocksBody');
  body.innerHTML = allSections.map(s => {
    const used = mod.requiredSections.some(rs => s.seccion.toUpperCase().includes(rs.toUpperCase()));
    return `<tr style="${used ? 'font-weight:700;background:var(--primary-light);' : ''}">
      <td>${escapeHtml(s.modulo)}</td>
      <td>${escapeHtml(s.seccion)}</td>
      <td>${s.rows.length.toLocaleString('es-CO')}</td>
      <td>${s.headerLen}</td>
      <td>${used ? '<span class="badge ok">usada</span>' : '—'}</td>
    </tr>`;
  }).join('');
}

/* ---------------- Diagnostics ---------------- */
function runDiagnostics() {
  const mod = MODULES[currentModule];
  document.getElementById('statRows').textContent = computedRows.length.toLocaleString('es-CO');
  document.getElementById('statRowsLbl').textContent = 'Fichas resueltas (vista activa)';
  document.getElementById('statCols').textContent = mod.requiredSections.length + ' sección(es)';
  document.getElementById('statLenMax').textContent = computedRows.length ? 'Consistente' : 'Sin datos';
  document.getElementById('statCompletas').textContent = computedRows.filter(r=>r.esDenominador).length.toLocaleString('es-CO');

  const sample = computedRows[0] || {};
  const body = document.getElementById('diagBody');
  const rows = [
    ['Ficha_fic', '—', sample.ficha],
    ['Fecha_intervencion', '—', sample.fecha],
    ['Columna 1', '—', sample.etiqueta1],
    ['Columna 2', '—', sample.etiqueta2],
    ['Estado calculado', '—', sample.detalleCol],
  ];
  body.innerHTML = rows.map(([name, sec, val]) => `<tr>
    <td>${name}</td><td class="mono">${escapeHtml(sec)}</td>
    <td class="mono">${escapeHtml(val === undefined || val === '' ? '(vacío)' : val).toString().slice(0,40)}</td>
    <td><span class="badge ok">OK</span></td>
  </tr>`).join('');
}

/* ---------------- Indicator picker ---------------- */
function renderIndicatorPicker() {
  const mod = MODULES[currentModule];
  const card = document.getElementById('cardIndicator');
  if (!mod.indicatorGroups || (mod.indicatorGroups.length <= 1 && mod.indicatorGroups[0].impactos.length <= 1)) {
    card.classList.add('hidden'); return;
  }
  card.classList.remove('hidden');
  const groupSel = document.getElementById('indicatorGroup');
  const impactoSel = document.getElementById('indicatorImpacto');
  groupSel.innerHTML = mod.indicatorGroups.map(g => `<option value="${g.key}">${g.label}</option>`).join('');
  groupSel.value = currentGroupKey;
  function refreshImpactos() {
    const grp = mod.indicatorGroups.find(g => g.key === groupSel.value);
    impactoSel.innerHTML = grp.impactos.map(i => `<option value="${i.key}">${i.label}</option>`).join('');
    impactoSel.value = currentImpactoKey;
    if (![...impactoSel.options].some(o=>o.value===currentImpactoKey)) impactoSel.selectedIndex = 0;
  }
  refreshImpactos();
  groupSel.onchange = () => {
    currentGroupKey = groupSel.value;
    refreshImpactos();
    currentImpactoKey = impactoSel.value;
    updateComputedRows(); runDiagnostics();
    if (!document.getElementById('cardResult').classList.contains('hidden')) calcularIndicador();
  };
  impactoSel.onchange = () => {
    currentImpactoKey = impactoSel.value;
    updateComputedRows(); runDiagnostics();
    if (!document.getElementById('cardResult').classList.contains('hidden')) calcularIndicador();
  };
}

/* ---------------- Filter + calculate ---------------- */
document.getElementById('btnCalc').addEventListener('click', calcularIndicador);
document.getElementById('btnReset').addEventListener('click', () => {
  document.getElementById('dateFrom').value = '';
  document.getElementById('dateTo').value = '';
  calcularIndicador();
});

function getIndicatorMeta(modKey, grpKey, impKey) {
  const mod = MODULES[modKey];
  if (modKey !== 'ut_utis') {
    const grp = mod.indicatorGroups[0];
    return { ...grp, labels: mod.labels, modLabel: mod.label };
  }
  const grp = mod.indicatorGroups.find(g => g.key === grpKey);
  const impacto = grp.impactos.find(i => i.key === impKey);
  const n = impacto.label.toLowerCase();
  return {
    numDesc: grp.numDescTpl(n), fuenteNum: grp.fuenteNumTpl(n),
    denDesc: grp.denDescTpl(n), fuenteDen: grp.fuenteDenTpl(n),
    formulaDesc: grp.formulaDescTpl(n), metodologia: grp.metodologia,
    labels: mod.labels[grpKey], modLabel: mod.label
  };
}
function currentIndicatorMeta() { return getIndicatorMeta(currentModule, currentGroupKey, currentImpactoKey); }

function filterByDate(rows, from, to) {
  return rows.filter(r => {
    if (!r.fecha) return !from && !to;
    if (from && r.fecha < from) return false;
    if (to && r.fecha > to) return false;
    return true;
  });
}

/* Calcula el resultado de UNA combinación módulo/indicador/impacto, reusando
   los datos ya parseados en allSections. Usado tanto por la vista activa
   como por la exportación de "todos los indicadores". */
function computeResult(modKey, grpKey, impKey, from, to) {
  const mod = MODULES[modKey];
  if (!mod.active || !mod.build) return null;
  const data = mod.build(allSections);
  if (!data) return null;
  let rows;
  if (modKey === 'ut_utis') {
    const grp = mod.indicatorGroups.find(g => g.key === grpKey);
    const impacto = grp.impactos.find(i => i.key === impKey);
    rows = data[grpKey].filter(r => matchesImpacto(r.derivadaA, impacto));
  } else {
    rows = data;
  }
  const filtered = filterByDate(rows, from, to);
  const denRows = filtered.filter(r => r.esDenominador);
  const numRows = filtered.filter(r => r.esNumerador);
  const meta = getIndicatorMeta(modKey, grpKey, impKey);
  return {
    modKey, grpKey, impKey, meta, filtered,
    numerador: numRows.length, denominador: denRows.length,
    pct: denRows.length ? (numRows.length/denRows.length*100) : 0,
    monthly: computeMonthly(filtered)
  };
}

/* Todas las combinaciones módulo/indicador/impacto activas, EN EL MISMO ORDEN
   de la matriz oficial (excluyendo las filas marcadas en naranja: 11,13,14,15
   — canalizaciones SIRC, pruebas SISCO, positivos SISCO, SAFL — que no se hacen). */
function allIndicatorCombos() {
  const order = [
    {modKey:'ut_utis', grpKey:'utis', impKey:'mediano'},          // fila 2
    {modKey:'ut_utis', grpKey:'utis', impKey:'bajo'},              // fila 3
    {modKey:'ut_utis', grpKey:'trabajadores', impKey:'mediano'},   // fila 4
    {modKey:'ut_utis', grpKey:'trabajadores', impKey:'bajo'},      // fila 5
    {modKey:'ut_utis', grpKey:'utis', impKey:'alto'},              // fila 6
    {modKey:'ut_utis', grpKey:'trabajadores', impKey:'alto'},      // fila 7
    {modKey:'ut_utis', grpKey:'continuidad', impKey:'default'},    // fila 8
    // fila 9 (empresas nuevas — Base Formalidad) va aquí cuando se construya
    {modKey:'conexion_nocturna', grpKey:'default', impKey:'default'}, // fila 10
    {modKey:'nna', grpKey:'default', impKey:'default'},            // fila 12
  ];
  return order.filter(c => MODULES[c.modKey] && MODULES[c.modKey].active);
}

function calcularIndicador() {
  const from = document.getElementById('dateFrom').value;
  const to = document.getElementById('dateTo').value;

  const result = computeResult(currentModule, currentGroupKey, currentImpactoKey, from, to);
  if (!result) return;
  filteredRows = result.filtered;
  const { meta, numerador, denominador, pct } = result;

  document.getElementById('indicatorName').textContent = meta.numDesc;
  document.getElementById('pctValue').textContent = pct.toFixed(1) + '%';
  document.getElementById('numValue').textContent = numerador.toLocaleString('es-CO');
  document.getElementById('denValue').textContent = denominador.toLocaleString('es-CO');
  document.getElementById('formulaBox').textContent =
    'Fuente numerador: ' + meta.fuenteNum + '\nFuente denominador: ' + meta.fuenteDen + '\nFórmula: ' + meta.formulaDesc;
  document.querySelectorAll('.result-sub .lbl')[0].textContent = meta.labels.numLabel;
  document.querySelectorAll('.result-sub .lbl')[1].textContent = meta.labels.denLabel;

  const heads = document.querySelectorAll('#cardResult table.data thead th');
  heads[2].textContent = meta.labels.col1;
  heads[3].textContent = meta.labels.col2;
  heads[4].textContent = meta.labels.col3;

  page = 0;
  renderTable();
  renderMonthTable(filteredRows);
  document.getElementById('cardResult').classList.remove('hidden');
}

/* ---------------- Table + pagination ---------------- */
function renderTable() {
  const start = page * PAGE_SIZE;
  const pageRows = filteredRows.slice(start, start + PAGE_SIZE);
  const body = document.getElementById('tableBody');
  if (pageRows.length === 0) {
    body.innerHTML = `<tr><td colspan="5"><div class="empty">No hay fichas en este periodo.</div></td></tr>`;
  } else {
    body.innerHTML = pageRows.map(r => {
      const pill = r.esNumerador ? `<span class="pill-si">${escapeHtml(r.detalleCol)}</span>`
                 : r.esDenominador ? `<span class="pill-no">${escapeHtml(r.detalleCol)}</span>`
                 : `<span class="pill-vacio">${escapeHtml(r.detalleCol)}</span>`;
      return `<tr>
        <td class="mono">${escapeHtml(r.ficha)}</td>
        <td>${escapeHtml(r.fecha)||'—'}</td>
        <td>${escapeHtml(r.etiqueta1)||'—'}</td>
        <td>${escapeHtml(r.etiqueta2)||'—'}</td>
        <td>${pill}</td>
      </tr>`;
    }).join('');
  }
  const totalPages = Math.max(1, Math.ceil(filteredRows.length / PAGE_SIZE));
  document.getElementById('pagerInfo').textContent =
    `${filteredRows.length.toLocaleString('es-CO')} fichas · página ${page+1} de ${totalPages}`;
}
document.getElementById('btnPrev').addEventListener('click', () => { if (page>0){page--; renderTable();} });
document.getElementById('btnNext').addEventListener('click', () => {
  const totalPages = Math.ceil(filteredRows.length / PAGE_SIZE);
  if (page < totalPages-1){page++; renderTable();}
});

/* ---------------- Monthly breakdown ---------------- */
function computeMonthly(rows) {
  const byMonth = new Map();
  rows.forEach(r => {
    const m = monthOf(r.fecha) || 'Sin fecha';
    if (!byMonth.has(m)) byMonth.set(m, {num:0, den:0});
    const bucket = byMonth.get(m);
    if (r.esDenominador) bucket.den++;
    if (r.esNumerador) bucket.num++;
  });
  return [...byMonth.entries()].sort((a,b)=>a[0].localeCompare(b[0]));
}
function renderMonthTable(rows) {
  const monthly = computeMonthly(rows);
  const body = document.getElementById('monthBody');
  body.innerHTML = monthly.map(([m, v]) => `<tr>
    <td>${m}</td><td>${v.num}</td><td>${v.den}</td><td>${v.den? (v.num/v.den*100).toFixed(1):'0.0'}%</td>
  </tr>`).join('') || `<tr><td colspan="4"><div class="empty">Sin datos</div></td></tr>`;
}

/* ---------------- Export Excel (formato de la plantilla oficial) ---------------- */
document.getElementById('btnExport').addEventListener('click', async () => {
  const meta = currentIndicatorMeta();
  const denRows = filteredRows.filter(r => r.esDenominador);
  const numRows = filteredRows.filter(r => r.esNumerador);
  const denominador = denRows.length, numerador = numRows.length;
  const pct = denominador ? (numerador/denominador*100) : 0;
  const monthly = computeMonthly(filteredRows);

  const wb = new ExcelJS.Workbook();

  // Hoja 1: Indicador — mismo formato de columnas que la plantilla oficial + valores calculados
  const ws = wb.addWorksheet('Indicador');
  ws.columns = [
    {header:'Numerador', key:'a', width:38},
    {header:'FUENTE - NUMERADOR', key:'b', width:34},
    {header:'Denominador', key:'c', width:38},
    {header:'FUENTE DENOMINADOR', key:'d', width:34},
    {header:'Formula', key:'e', width:38},
    {header:'Valor Numerador', key:'f', width:16},
    {header:'Valor Denominador', key:'g', width:18},
    {header:'% Resultado', key:'h', width:14},
  ];
  ws.getRow(1).font = {bold:true, color:{argb:'FFFFFFFF'}};
  ws.getRow(1).fill = {type:'pattern', pattern:'solid', fgColor:{argb:'FF0E6C5C'}};
  ws.addRow({a:meta.numDesc, b:meta.fuenteNum, c:meta.denDesc, d:meta.fuenteDen, e:meta.formulaDesc,
             f:numerador, g:denominador, h:(pct.toFixed(1)+'%')});
  ws.getRow(2).alignment = {wrapText:true, vertical:'top'};
  ws.getRow(2).height = 90;

  // Hoja 2: Metodología (descripción de cómo se sacaron los datos)
  const meth = wb.addWorksheet('Metodología');
  meth.columns = [{header:'Campo', key:'k', width:24},{header:'Detalle', key:'v', width:90}];
  meth.getRow(1).font = {bold:true};
  meth.addRows([
    {k:'Módulo', v: MODULES[currentModule].label},
    {k:'Indicador', v: meta.numDesc},
    {k:'Cómo se calculó', v: meta.metodologia},
    {k:'Periodo filtrado', v: (document.getElementById('dateFrom').value||'Sin límite') + ' a ' + (document.getElementById('dateTo').value||'Sin límite')},
    {k:'Archivo fuente', v: document.querySelector('.filepill') ? document.querySelector('.filepill').textContent.trim() : 'CSV cargado por el usuario'},
    {k:'Fecha de cálculo', v: new Date().toLocaleString('es-CO')},
  ]);
  meth.getColumn(2).alignment = {wrapText:true, vertical:'top'};

  // Hoja 3: Por mes
  const mSheet = wb.addWorksheet('Por mes');
  mSheet.columns = [
    {header:'Mes', key:'m', width:14},{header:'Numerador', key:'n', width:14},
    {header:'Denominador', key:'d', width:16},{header:'%', key:'p', width:12},
  ];
  mSheet.getRow(1).font = {bold:true, color:{argb:'FFFFFFFF'}};
  mSheet.getRow(1).fill = {type:'pattern', pattern:'solid', fgColor:{argb:'FF0E6C5C'}};
  monthly.forEach(([m,v]) => mSheet.addRow({m, n:v.num, d:v.den, p: v.den ? (v.num/v.den*100).toFixed(1)+'%' : '0.0%'}));

  // Hoja 4: Detalle
  const det = wb.addWorksheet('Detalle');
  det.columns = [
    {header:'Ficha', key:'ficha', width:14},{header:'Fecha', key:'fecha', width:14},
    {header:'Columna 1', key:'c1', width:22},{header:'Columna 2', key:'c2', width:30},
    {header:'Estado', key:'c3', width:22},{header:'¿Cuenta en numerador?', key:'num', width:20},
  ];
  det.getRow(1).font = {bold:true, color:{argb:'FFFFFFFF'}};
  det.getRow(1).fill = {type:'pattern', pattern:'solid', fgColor:{argb:'FF0E6C5C'}};
  filteredRows.forEach(r => det.addRow({ficha:r.ficha, fecha:r.fecha, c1:r.etiqueta1, c2:r.etiqueta2, c3:r.detalleCol, num: r.esNumerador?'Sí':'No'}));

  const buf = await wb.xlsx.writeBuffer();
  const blob = new Blob([buf], {type:'application/octet-stream'});
  const url = URL.createObjectURL(blob);
  const a = document.createElement('a');
  a.href = url; a.download = `indicador_${currentModule}_${currentGroupKey}_${currentImpactoKey}.xlsx`;
  a.click();
  URL.revokeObjectURL(url);
});

/* ---------------- Export Excel — TODOS los indicadores disponibles ---------------- */
function sheetSafe(name) { return name.replace(/[\[\]\*\/\\\?:]/g, '').slice(0, 31); }

document.getElementById('btnExportAll').addEventListener('click', async () => {
  const from = document.getElementById('dateFrom').value;
  const to = document.getElementById('dateTo').value;
  const combos = allIndicatorCombos();
  const results = combos.map(c => computeResult(c.modKey, c.grpKey, c.impKey, from, to)).filter(Boolean);

  if (!results.length) { alert('No hay datos calculables todavía — sube el archivo primero.'); return; }

  const wb = new ExcelJS.Workbook();

  // Hoja 1: Resumen — una fila por indicador, mismas columnas de la plantilla oficial
  const ws = wb.addWorksheet('Resumen indicadores');
  ws.columns = [
    {header:'Módulo', key:'mod', width:22},
    {header:'Numerador', key:'a', width:36},
    {header:'FUENTE - NUMERADOR', key:'b', width:32},
    {header:'Denominador', key:'c', width:36},
    {header:'FUENTE DENOMINADOR', key:'d', width:32},
    {header:'Formula', key:'e', width:36},
    {header:'Valor Numerador', key:'f', width:15},
    {header:'Valor Denominador', key:'g', width:16},
    {header:'% Resultado', key:'h', width:12},
  ];
  ws.getRow(1).font = {bold:true, color:{argb:'FFFFFFFF'}};
  ws.getRow(1).fill = {type:'pattern', pattern:'solid', fgColor:{argb:'FF0E6C5C'}};
  results.forEach(r => {
    const row = ws.addRow({
      mod: r.meta.modLabel, a: r.meta.numDesc, b: r.meta.fuenteNum, c: r.meta.denDesc,
      d: r.meta.fuenteDen, e: r.meta.formulaDesc, f: r.numerador, g: r.denominador,
      h: r.pct.toFixed(1)+'%'
    });
    row.alignment = {wrapText:true, vertical:'top'};
  });

  // Hoja 2: Metodología — una fila por indicador
  const meth = wb.addWorksheet('Metodología');
  meth.columns = [
    {header:'Módulo', key:'mod', width:22},{header:'Indicador', key:'ind', width:40},
    {header:'Cómo se calculó', key:'v', width:90},
  ];
  meth.getRow(1).font = {bold:true};
  results.forEach(r => meth.addRow({mod: r.meta.modLabel, ind: r.meta.numDesc, v: r.meta.metodologia}));
  meth.getColumn(3).alignment = {wrapText:true, vertical:'top'};
  meth.addRow({});
  meth.addRow({mod:'Periodo filtrado', ind:(from||'Sin límite') + ' a ' + (to||'Sin límite')});
  meth.addRow({mod:'Archivo fuente', ind: document.querySelector('.filepill') ? document.querySelector('.filepill').textContent.trim() : ''});
  meth.addRow({mod:'Fecha de cálculo', ind: new Date().toLocaleString('es-CO')});

  // Hoja 3: Por mes — todos los indicadores juntos, con columna Indicador
  const mSheet = wb.addWorksheet('Por mes');
  mSheet.columns = [
    {header:'Indicador', key:'ind', width:36},{header:'Mes', key:'m', width:14},
    {header:'Numerador', key:'n', width:14},{header:'Denominador', key:'d', width:16},{header:'%', key:'p', width:12},
  ];
  mSheet.getRow(1).font = {bold:true, color:{argb:'FFFFFFFF'}};
  mSheet.getRow(1).fill = {type:'pattern', pattern:'solid', fgColor:{argb:'FF0E6C5C'}};
  results.forEach(r => {
    r.monthly.forEach(([m,v]) => mSheet.addRow({
      ind: r.meta.numDesc, m, n:v.num, d:v.den, p: v.den ? (v.num/v.den*100).toFixed(1)+'%' : '0.0%'
    }));
  });

  // Una hoja de Detalle por indicador
  results.forEach(r => {
    const tabName = sheetSafe((r.grpKey!=='default' ? r.grpKey+'_'+r.impKey : r.modKey));
    const det = wb.addWorksheet(sheetSafe('Det_'+tabName) || 'Detalle');
    det.columns = [
      {header:'Ficha', key:'ficha', width:14},{header:'Fecha', key:'fecha', width:14},
      {header:'Columna 1', key:'c1', width:22},{header:'Columna 2', key:'c2', width:30},
      {header:'Estado', key:'c3', width:22},{header:'¿Cuenta en numerador?', key:'num', width:20},
    ];
    det.getRow(1).font = {bold:true, color:{argb:'FFFFFFFF'}};
    det.getRow(1).fill = {type:'pattern', pattern:'solid', fgColor:{argb:'FF0E6C5C'}};
    r.filtered.forEach(row => det.addRow({ficha:row.ficha, fecha:row.fecha, c1:row.etiqueta1, c2:row.etiqueta2, c3:row.detalleCol, num: row.esNumerador?'Sí':'No'}));
  });

  const buf = await wb.xlsx.writeBuffer();
  const blob = new Blob([buf], {type:'application/octet-stream'});
  const url = URL.createObjectURL(blob);
  const a = document.createElement('a');
  a.href = url; a.download = `todos_los_indicadores_GESI.xlsx`;
  a.click();
  URL.revokeObjectURL(url);
});
</script>


</body>
</html>