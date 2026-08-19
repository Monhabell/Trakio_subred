<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Tamizajes por UPZ · GESI</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/xlsx-js-style/dist/xlsx.bundle.js"></script>
<style>
  :root{
  --bg:#F4F7FB;
  --surface:#FFFFFF;

  --ink:#14213D;
  --muted:#64748B;

  --primary:#2563EB;
  --primary-dark:#1D4ED8;
  --accent:#3B82F6;

  --border:#E2E8F0;

  --good-bg:#DCFCE7;
  --good-ink:#15803D;

  --warn-bg:#FEF3C7;
  --warn-ink:#B45309;
}

[data-theme="dark"]{
  --bg:#0F172A;
  --surface:#1E293B;

  --ink:#F1F5F9;
  --muted:#94A3B8;

  --primary:#60A5FA;
  --primary-dark:#3B82F6;
  --accent:#93C5FD;

  --border:#334155;

  --good-bg:#052E16;
  --good-ink:#4ADE80;

  --warn-bg:#451A03;
  --warn-ink:#FBBF24;
}
  *, *::before, *::after{ transition: background-color .25s, color .2s, border-color .2s; }
  *{box-sizing:border-box;}
  body{
    margin:0;
    background:var(--bg);
    color:var(--ink);
    font-family:'Inter',system-ui,sans-serif;
    -webkit-font-smoothing:antialiased;
  }
  .wrap{max-width:1040px;margin:0 auto;padding:36px 24px 64px;}
  header.top{margin-bottom:28px;}
  .eyebrow{
    font-family:'JetBrains Mono',monospace;
    font-size:12px;
    letter-spacing:.08em;
    text-transform:uppercase;
    color:var(--primary);
    margin:0 0 8px;
  }
  h1{
    font-family:'Space Grotesk',sans-serif;
    font-size:30px;
    font-weight:700;
    margin:0 0 10px;
    letter-spacing:-0.01em;
  }
  .sub{color:var(--muted);font-size:15px;line-height:1.55;max-width:720px;margin:0;}

  .card{
    background:var(--surface);
    border:1px solid var(--border);
    border-radius:14px;
    padding:22px;
    margin-bottom:18px;
  }

  .dropzone{
    border:2px dashed var(--border);
    border-radius:12px;
    padding:38px 20px;
    text-align:center;
    cursor:pointer;
    transition:border-color .15s, background .15s;
    position:relative;
  }
  .dropzone.drag{border-color:var(--primary); background:var(--good-bg);}
  .dropzone input[type=file]{
    position:absolute; inset:0; opacity:0; cursor:pointer;
  }
  .dz-icon{font-size:28px; margin-bottom:10px; opacity:.7;}
  .dz-title{font-weight:600; font-size:15px; margin-bottom:4px;}
  .dz-hint{color:var(--muted); font-size:13px;}
  .filename{
    font-family:'JetBrains Mono',monospace;
    font-size:12.5px;
    color:var(--primary-dark);
  }

  .status-row{display:flex; align-items:center; gap:10px; margin-top:14px; font-size:14px;}
  .dot{width:8px;height:8px;border-radius:50%;background:var(--border);flex:none;}
  .dot.busy{background:var(--accent); animation:pulse 1s infinite;}
  .dot.ok{background:var(--primary);}
  .dot.err{background:#C0392B;}
  @keyframes pulse{0%,100%{opacity:1;}50%{opacity:.3;}}

  .tabs{display:flex; gap:6px; margin-bottom:16px; flex-wrap:wrap;}
  .tab{
    font-family:'Inter',sans-serif; font-weight:600; font-size:13.5px;
    padding:10px 16px; border-radius:9px; border:1px solid var(--border);
    background:var(--surface); color:var(--muted); cursor:pointer;
  }
  .tab.active{background:var(--primary); color:#fff; border-color:var(--primary);}
  .panel{display:none;}
  .panel.active{display:block;}
  h3.block-title{font-family:'Space Grotesk',sans-serif;font-size:16px;margin:22px 0 6px;}
  .sub-block{border-top:1px solid var(--border); padding-top:16px; margin-top:16px;}
  .sub-block:first-of-type{border-top:none; padding-top:0; margin-top:0;}

  .stat-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-top:6px;}
  .stat{
    background:var(--bg);
    border:1px solid var(--border);
    border-radius:10px;
    padding:14px 14px;
  }
  .stat .n{font-family:'Space Grotesk',sans-serif;font-size:22px;font-weight:700;color:var(--primary-dark);}
  .stat .l{font-size:12px;color:var(--muted);margin-top:2px;}

  details.diag{margin-top:14px;}
  details.diag summary{cursor:pointer;font-size:13px;color:var(--muted);}
  details.diag .diag-body{
    margin-top:10px;font-size:13px;color:var(--ink);
    background:var(--warn-bg); border-radius:10px; padding:12px 14px; line-height:1.6;
  }
  details.diag .diag-body b{color:var(--warn-ink);}

  .actions{display:flex; gap:10px; margin-top:18px; flex-wrap:wrap;}
  button.primary{
    background:var(--primary); color:#fff; border:none;
    font-family:'Inter',sans-serif; font-weight:600; font-size:14px;
    padding:12px 20px; border-radius:9px; cursor:pointer;
    transition:background .15s;
  }
  button.primary:hover{background:var(--primary-dark);}
  button.primary:disabled{background:#A9BDB9; cursor:not-allowed;}
  button.ghost{
    background:transparent; color:var(--ink); border:1px solid var(--border);
    font-family:'Inter',sans-serif; font-weight:500; font-size:14px;
    padding:12px 18px; border-radius:9px; cursor:pointer;
  }
  button.ghost:hover{border-color:var(--primary);}

  table.preview{
    width:100%; border-collapse:collapse; font-size:13px; margin-top:6px;
  }
  table.preview th, table.preview td{
    border:1px solid var(--border); padding:7px 9px; text-align:right;
    color:var(--ink); background:var(--surface);
  }
  table.preview th{
    background:var(--bg); font-weight:600; text-align:center; color:var(--ink);
    font-size:12px;
  }
  table.preview td:nth-child(1), table.preview td:nth-child(2), table.preview td:nth-child(3){
    text-align:left; font-weight:500;
  }
  table.preview tbody tr:nth-child(even) td{ background:var(--bg); }
  table.preview tbody tr:hover td{ background:var(--good-bg); color:var(--ink); }
  .scrollx{overflow-x:auto; border-radius:10px;}
  .scrollx::-webkit-scrollbar{ height:6px; }
  .scrollx::-webkit-scrollbar-track{ background:var(--bg); }
  .scrollx::-webkit-scrollbar-thumb{ background:var(--border); border-radius:3px; }

  .review-table tbody tr:nth-child(even) td{ background:var(--bg); }
  [data-theme="dark"] .review-table th{ background:var(--bg); color:var(--ink); }

  .assumptions{font-size:12.5px;color:var(--muted); line-height:1.6; margin-top:10px;}
  .assumptions code{
    background:var(--bg); padding:1px 5px; border-radius:5px;
    font-family:'JetBrains Mono',monospace; font-size:11.5px;
  }
  footer.note{margin-top:28px;font-size:12px;color:var(--muted);text-align:center;}

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

  .review-table{width:100%; border-collapse:collapse; font-size:12.5px; margin-top:8px;}
  .review-table th, .review-table td{border:1px solid var(--border); padding:6px 8px; text-align:left; color:var(--ink); background:var(--surface);}
  .review-table th{background:var(--bg); font-weight:600;}
  .review-more{color:var(--muted); font-size:12.5px; margin-top:6px;}

  .title-row{display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap;}
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
    <p class="eyebrow">GESI · Sesiones Colectivas </p>
    <div class="title-row">
      <h1>Reporte Cronicos</h1>
      <div class="token-badge" id="tokenBadge">Tokens disponibles: <b id="tokenCount">0</b></div>
    </div>
    <p class="sub">Sube el archivo exportado de GESI y obtén las tablas de FINDRISC, OMS, Cáncer, Visual, Auditivo y Respiratorias (Asma/EPOC) por UPZ, cruce de edad, sexo y gestación &mdash; en el formato del tablero oficial.</p>
  </header>

  <div class="block-overlay" id="blockOverlay" style="display:none;">
    <div class="block-card">
      <div class="block-icon">&#128274;</div>
      <h2 id="blockTitle">Sin tokens disponibles</h2>
      <p id="blockMessage">No tienes tokens disponibles. Por favor, adquiere más tokens para seguir usando el validador.</p>
    </div>
  </div>

  <div class="card" id="dropzoneCard">
    <div class="dropzone" id="dropzone">
      <input type="file" id="fileInput" accept=".csv,.txt">
      <div class="dz-icon">&#128193;</div>
      <div class="dz-title" id="dzTitle">Arrastra aquí el archivo CSV de GESI, o haz clic para elegirlo</div>
      <div class="dz-hint" id="dzHint">Acepta el export tal como lo descarga GESI (UTF-16, .csv)</div>
    </div>

    <div class="status-row" id="statusRow" style="display:none;">
      <div class="dot" id="statusDot"></div>
      <div id="statusText"></div>
    </div>
  </div>

  <div class="card" id="resultsCard" style="display:none;">
    <div class="tabs" id="tabs"></div>
    <div id="panels"></div>

    <div class="actions">
      <button class="primary" id="downloadBtn">Descargar Excel completo (10 hojas + revisión manual)</button>
      <button class="ghost" id="resetBtn">Cargar otro archivo</button>
    </div>

    <div class="assumptions" id="assumptionsBox"></div>
  </div>

  <footer class="note">El procesamiento de los datos ocurre en tu navegador; solo el conteo de tokens consumidos se envía al servidor.</footer>
</div>

<script>
const UPZ_CATALOG = {
  1:"Paseo de los Libertadores",2:"La Academia",3:"Guaymaral",9:"Verbenal",10:"La Uribe",
  11:"San Cristóbal Norte",12:"Toberín",13:"Los Cedros",14:"Usaquén",15:"Country Club",
  16:"Santa Bárbara",17:"San José de Bavaria",18:"Britalia",19:"El Prado",20:"La Alhambra",
  21:"Los Andes",22:"Doce de Octubre",23:"Casa Blanca Suba",24:"Niza",25:"La Floresta",
  26:"Las Ferias",27:"Suba",28:"El Rincón",29:"Minuto de Dios",30:"Boyacá Real",
  31:"Santa Cecilia",32:"San Blas",33:"Sosiego",34:"20 de Julio",35:"Ciudad Jardín",
  36:"San José",37:"Santa Isabel",38:"Restrepo",39:"Quiroga",40:"Ciudad Montes",
  41:"Muzú",42:"Venecia",43:"San Rafael",44:"Américas",45:"Carvajal",46:"Castilla",
  47:"Kennedy Central",48:"Timiza",49:"Apogeo",50:"La Gloria",51:"Los Libertadores",
  52:"La Flora",53:"Marco Fidel Suárez",54:"Marruecos",55:"Diana Turbay",56:"Danubio",
  57:"Gran Yomasa",58:"Comuneros",59:"Alfonso López",60:"Parque Entrenubes",61:"Ciudad Usme",
  62:"Tunjuelito",63:"El Mochuelo",64:"Monte Blanco",65:"Arborizadora",66:"San Francisco",
  67:"Lucero",68:"El Tesoro",69:"Ismael Perdomo",70:"Jerusalén",71:"Tibabuyes",72:"Bolivia",
  73:"Garcés Navas",74:"Engativá",75:"Fontibón",76:"Fontibón San Pablo",77:"Zona Franca",
  78:"Tintal Norte",79:"Calandaima",80:"Corabastos",81:"Gran Britalia",82:"Patio Bonito",
  83:"Las Margaritas",84:"Bosa Occidental",85:"Bosa Central",86:"El Porvenir",87:"Tintal Sur",
  88:"El Refugio",89:"San Isidro - Patios",90:"Pardo Rubio",91:"Sagrado Corazón",
  92:"La Macarena",93:"Las Nieves",94:"La Candelaria",95:"Las Cruces",96:"Lourdes",
  97:"Chicó Lago",98:"Los Alcázares",99:"Chapinero",100:"Galerías",101:"Teusaquillo",
  102:"La Sabana",103:"Parque Salitre",104:"Parque Simón Bolívar - CAN",105:"Jardín Botánico",
  106:"La Esmeralda",107:"Quinta Paredes",108:"Zona Industrial",109:"Ciudad Salitre Oriental",
  110:"Ciudad Salitre Occidental",111:"Puente Aranda",112:"Granjas de Techo",113:"Bavaria",
  114:"Modelia",115:"Capellanía",116:"Álamos",117:"Aeropuerto El Dorado"
};

const AGES_3 = [
  {key:'J', label:'Juventud (18 a 28 años)', curso:'JUVENTUD'},
  {key:'A', label:'Adultez (29 a 59 años)', curso:'ADULTEZ'},
  {key:'V', label:'Vejez (>=60)', curso:'VEJEZ'},
];
const AGES_6 = [
  {key:'PI', label:'Primera infancia (0 a 5 años)', curso:'PRIMERA INFANCIA'},
  {key:'INF', label:'Infancia (6 a 11 años)', curso:'INFANCIA'},
  {key:'ADO', label:'Adolescencia (12 a 17 años)', curso:'ADOLESCENCIA'},
  {key:'J', label:'Juventud (18 a 28 años)', curso:'JUVENTUD'},
  {key:'A', label:'Adultez (29 a 59 años)', curso:'ADULTEZ'},
  {key:'V', label:'Vejez (>=60)', curso:'VEJEZ'},
];
const AGES_ERC_0_17 = [
  {key:'INF', label:'Infancia (6 a 11 años)', curso:'INFANCIA'},
  {key:'ADO', label:'Adolescencia (12 a 17 años)', curso:'ADOLESCENCIA'},
];
const AGES_ERC_18_39 = [
  {key:'J2', label:'Juventud (18 a 28 años)', edadMin:18, edadMax:28},
  {key:'A2', label:'Adultez (29 a 39 años)', edadMin:29, edadMax:39},
];
const AGES_ERC_40 = [
  {key:'A3', label:'Adultez (40 a 59 años)', edadMin:40, edadMax:59},
  {key:'V3', label:'Vejez (>=60 años)', edadMin:60, edadMax:999},
];
function resolveAgeKey(ageGroups, p){
  for(const g of ageGroups){
    if(g.curso && norm(p.curso)===g.curso) return g.key;
    if(g.edadMin!=null && p.edad!=null && p.edad>=g.edadMin && p.edad<=g.edadMax) return g.key;
  }
  return null;
}

const SEXES = ['M','F','I','T'];
const SEX_LABEL = {M:'Masculino',F:'Femenino',I:'Intersexual',T:'Total'};

function norm(s){ return (s||'').trim().toUpperCase(); }
function upzNumber(code){ const m = (code||'').toUpperCase().match(/(\d+)/); return m ? parseInt(m[1],10) : null; }
function upzNameFor(code){ const n = upzNumber(code); return n!==null ? (UPZ_CATALOG[n]||'') : ''; }

// ---- category mappers for the fields with an already-known official scale ----
function mapFindrisc(raw){
  const s = norm(raw);
  if(s.includes('NO APLICA')) return 'noaplica';
  if(s.includes('MUY ALTO')) return 'muyalto';
  if(s.includes('MODERADO')) return 'moderado';
  if(s.includes('LIGERA')) return 'ligero';
  if(s.includes('BAJO')) return 'bajo';
  if(s.includes('ALTO')) return 'alto';
  return null;
}
function mapOMS(raw){
  const s = norm(raw);
  if(s.includes('NO APLICA')) return 'noaplica';
  if(s.includes('CRITICO') || s.includes('CRÍTICO') || s.includes('EXTREMADAMENTE')) return 'critico';
  if(s.includes('MUY ALTO')) return 'muyalto';
  if(s.includes('MODERADO')) return 'moderado';
  if(s.includes('BAJO')) return 'bajo';
  if(s.includes('ALTO')) return 'alto';
  return null;
}
function mapCancer(raw){
  const s = norm(raw);
  if(s.includes('NO APLICA')) return 'noaplica';
  if(s.includes('SIN RIESGO')) return 'sinriesgo';
  if(s.includes('ALTO')) return 'alto';
  if(s.includes('MODERADO')) return 'moderado';
  if(s.includes('BAJO')) return 'bajo';
  return null;
}
function mapVisual(raw){
  const s = norm(raw);
  if(s.includes('NO APLICA')) return 'noaplica';
  if(s.includes('CEGUERA')) return 'ceguera';
  if(s.includes('GRAVE')) return 'grave';
  if(s.includes('MODERADA')) return 'moderada';
  if(s.includes('LEVE')) return 'leve';
  if(s.includes('NORMAL')) return 'normal';
  return null;
}
function mapAuditivo(raw){
  const s = norm(raw);
  if(s.includes('NO APLICA')) return 'noaplica';
  if(s.includes('NO SAT')) return 'nosatisfactorio';
  if(s.includes('SAT')) return 'satisfactorio';
  return null;
}
function mapSiNo(raw){
  const s = norm(raw);
  if(s.includes('NO APLICA')) return 'noaplica';
  if(s.startsWith('SI') || s.includes('POSITIVO')) return 'si';
  if(s.startsWith('NO') || s.includes('NEGATIVO')) return 'no';
  return null;
}

// Estructura validada contra el tablero oficial
// TABLERO_DE_CONTROL_EDUCATIVO_Surbed_Norte_FEB_26.xlsx (10 hojas).
const TEST_DEFS = [
  {
    id:'findrisc', tabLabel:'FINDRISC', group:null,
    title:'TAMIZAJE FINDRISC', sourceColName:'FINDRISC', mapCategory:mapFindrisc, ageGroups:AGES_3,
    categories:[
      {key:'bajo',label:'Bajo <7 puntos (0-6)'},
      {key:'ligero',label:'Riesgo ligeramente elevado: 7–11 puntos'},
      {key:'moderado',label:'Riesgo moderado: 12–14 puntos'},
      {key:'alto',label:'Riesgo alto: 15–20 puntos'},
      {key:'muyalto',label:'Riesgo muy alto: >20 puntos (no contiene el 20)'},
      {key:'noaplica',label:'No aplica'},
    ],
    gestRow:4, siLabel:'Si', noLabel:'No',
  },
  {
    id:'oms', tabLabel:'OMS', group:null,
    title:'OMS - Cardiovascular', sourceColName:'OMS', mapCategory:mapOMS, ageGroups:AGES_3,
    categories:[
      {key:'bajo',label:'Bajo <5% (1-4)'},
      {key:'moderado',label:'Moderado 5% a <10% (5-9)'},
      {key:'alto',label:'Alto: 10% a <20% (10-19)'},
      {key:'muyalto',label:'Muy alto 20% a <30% (20-29)'},
      {key:'critico',label:'Crítico o extremadamente alto ≥30% (30 en adelante)'},
      {key:'noaplica',label:'No aplica'},
    ],
    gestRow:3, siLabel:'SI', noLabel:'NO',
  },
  {
    id:'cancer', tabLabel:'Cáncer', group:null,
    title:'ESCALA DE CLASIFICACION DEL RIESGO EN CANCER', sourceColName:'CLASIFICACIÓN DE RIESGO EN CÁNCER', mapCategory:mapCancer, ageGroups:AGES_3,
    categories:[
      {key:'alto',label:'RIESGO ALTO'},
      {key:'moderado',label:'RIESGO MODERADO'},
      {key:'bajo',label:'RIESGO BAJO'},
      {key:'sinriesgo',label:'SIN RIESGO'},
      {key:'noaplica',label:'No Aplica'},
    ],
    gestRow:3, siLabel:'SI', noLabel:'NO',
  },
  {
    id:'visual_od', tabLabel:'OD (ojo derecho)', group:'visual', groupLabel:'Visual',
    title:'TAMIZAJE VISUAL\nRESULTADOS OJO DERECHO (OD)', sourceColName:'OD: OJO DERECHO', mapCategory:mapVisual, ageGroups:AGES_6,
    categories:[
      {key:'normal',label:'AGUDEZA VISUAL NORMAL'},
      {key:'leve',label:'DEFICIENCIA VISUAL LEVE'},
      {key:'moderada',label:'DEFICIENCIA VISUAL MODERADA'},
      {key:'grave',label:'DEFICIENCIA VISUAL GRAVE'},
      {key:'ceguera',label:'CEGUERA'},
      {key:'noaplica',label:'No Aplica'},
    ],
    gestRow:3, siLabel:'SI', noLabel:'NO',
  },
  {
    id:'visual_oi', tabLabel:'OI (ojo izquierdo)', group:'visual', groupLabel:'Visual',
    title:'TAMIZAJE VISUAL\nRESULTADOS OJO IZQUIERDO (OI)', sourceColName:'OI: OJO IZQUIERDO', mapCategory:mapVisual, ageGroups:AGES_6,
    categories:[
      {key:'normal',label:'AGUDEZA VISUAL NORMAL'},
      {key:'leve',label:'DEFICIENCIA VISUAL LEVE'},
      {key:'moderada',label:'DEFICIENCIA VISUAL MODERADA'},
      {key:'grave',label:'DEFICIENCIA VISUAL GRAVE'},
      {key:'ceguera',label:'CEGUERA'},
      {key:'noaplica',label:'No Aplica'},
    ],
    gestRow:3, siLabel:'SI', noLabel:'NO',
  },
  {
    id:'auditivo_od', tabLabel:'OD (oído derecho)', group:'auditivo', groupLabel:'Auditivo',
    title:'TAMIZAJE AUDITIVO\nRESULTADOS OIDO DERECHO (OD)', sourceColName:'OD: OÍDO DERECHO', mapCategory:mapAuditivo, ageGroups:AGES_6,
    categories:[
      {key:'satisfactorio',label:'SATISFACTORIO'},
      {key:'nosatisfactorio',label:'NO SATSIFACTORIO'},
      {key:'noaplica',label:'No Aplica'},
    ],
    gestRow:3, siLabel:'SI', noLabel:'NO',
  },
  {
    id:'auditivo_oi', tabLabel:'OI (oído izquierdo)', group:'auditivo', groupLabel:'Auditivo',
    title:'TAMIZAJE AUDITIVO\nRESULTADOS OIDO IZQUIERDO (OI)', sourceColName:'OI: OÍDO IZQUIERDO', mapCategory:mapAuditivo, ageGroups:AGES_6,
    categories:[
      {key:'satisfactorio',label:'SATISFACTORIO'},
      {key:'nosatisfactorio',label:'NO SATSIFACTORIO'},
      {key:'noaplica',label:'No Aplica'},
    ],
    gestRow:3, siLabel:'SI', noLabel:'NO',
  },
  {
    id:'resp_0_17', tabLabel:'< 18 años (6-17, Asma)', group:'resp', groupLabel:'Respiratorias',
    title:'ENFERMEDADES RESPIRATORIAS CRÓNICAS (<18 AÑOS)=ASMA (DE 6 A 17 AÑOS)',
    sourceColName:'0 A 17 AÑOS', mapCategory:mapSiNo, ageGroups:AGES_ERC_0_17,
    categories:[{key:'si',label:'SÍ'},{key:'no',label:'No'},{key:'noaplica',label:'No Aplica'}],
    gestRow:3, siLabel:'SI', noLabel:'NO',
  },
  {
    id:'resp_18_39', tabLabel:'18-39 años (Asma)', group:'resp', groupLabel:'Respiratorias',
    title:'ENFERMEDADES RESPIRATORIAS CRÓNICAS (18-39 AÑOS)=ASMA',
    sourceColName:'18 A 39 AÑOS', mapCategory:mapSiNo, ageGroups:AGES_ERC_18_39,
    categories:[{key:'si',label:'SÍ'},{key:'no',label:'No'},{key:'noaplica',label:'No Aplica'}],
    gestRow:3, siLabel:'SI', noLabel:'NO',
  },
  {
    id:'resp_40', tabLabel:'40+ años (EPOC)', group:'resp', groupLabel:'Respiratorias',
    title:'ENFERMEDADES RESPIRATORIAS CRÓNICAS (40 AÑOS EN ADELANTE)=EPOC',
    sourceColName:'40 AÑOS EN ADELANTE', mapCategory:mapSiNo, ageGroups:AGES_ERC_40,
    categories:[{key:'si',label:'SÍ'},{key:'no',label:'No'},{key:'noaplica',label:'No Aplica'}],
    gestRow:3, siLabel:'SI', noLabel:'NO',
  },
];

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

let lastResults = null;

// ---- Integración de tokens ODIN (Laravel) ----
// INITIAL_TOKENS llega desde el controlador (cronicosV2) vía Blade.
// Ajusta UPDATE_TOKENS_URL a la ruta real que apunta a updateTokens().
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
const dropzoneCard = document.getElementById('dropzoneCard');

function renderTokenBadge(){
  tokenCountEl.textContent = currentTokens;
  tokenBadge.classList.toggle('low', currentTokens <= TOKENS_PER_CHARGE);
}

function showBlock(message){
  blockTitle.textContent = 'Sin tokens disponibles';
  blockMessage.textContent = message || 'No tienes tokens disponibles. Por favor, adquiere más tokens para seguir usando el validador.';
  blockOverlay.style.display = 'flex';
  dropzoneCard.style.pointerEvents = 'none';
  dropzoneCard.style.opacity = '0.5';
}

function checkTokenGate(){
  if(currentTokens <= 0){
    showBlock();
    return false;
  }
  return true;
}

async function chargeTokens(rowsValidated){
  const blocks = Math.ceil(rowsValidated / ROWS_PER_CHARGE);
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
    showBlock('Te quedaste sin tokens tras validar este archivo. Adquiere más tokens para seguir usando el validador.');
  }
}

renderTokenBadge();
if(!checkTokenGate()){ /* el overlay ya quedó visible */ }

const dz = document.getElementById('dropzone');
const fileInput = document.getElementById('fileInput');
const statusRow = document.getElementById('statusRow');
const statusDot = document.getElementById('statusDot');
const statusText = document.getElementById('statusText');
const resultsCard = document.getElementById('resultsCard');
const tabsEl = document.getElementById('tabs');
const panelsEl = document.getElementById('panels');
const dzTitle = document.getElementById('dzTitle');
const dzHint = document.getElementById('dzHint');
const assumptionsBox = document.getElementById('assumptionsBox');

['dragenter','dragover'].forEach(ev=>dz.addEventListener(ev,e=>{e.preventDefault();dz.classList.add('drag');}));
['dragleave','drop'].forEach(ev=>dz.addEventListener(ev,e=>{e.preventDefault();dz.classList.remove('drag');}));
dz.addEventListener('drop', e=>{ const f = e.dataTransfer.files[0]; if(f) handleFile(f); });
fileInput.addEventListener('change', e=>{ const f = e.target.files[0]; if(f) handleFile(f); });
document.getElementById('resetBtn').addEventListener('click', ()=>{
  resultsCard.style.display='none';
  statusRow.style.display='none';
  fileInput.value='';
  dzTitle.textContent='Arrastra aquí el archivo CSV de GESI, o haz clic para elegirlo';
  dzHint.textContent='Acepta el export tal como lo descarga GESI (UTF-16, .csv)';
});

function setStatus(kind, text){
  statusRow.style.display='flex';
  statusDot.className='dot ' + kind;
  statusText.textContent = text;
}

async function handleFile(file){
  if(!checkTokenGate()) return;
  dzTitle.innerHTML = 'Archivo: <span class="filename">'+file.name+'</span>';
  dzHint.textContent = 'Haz clic para elegir un archivo diferente';
  resultsCard.style.display='none';
  setStatus('busy','Leyendo y procesando el archivo…');
  try{
    const buf = await file.arrayBuffer();
    const text = decodeBuffer(buf);
    const lines = text.split(/\r\n|\n|\r/);
    const sections = parseSections(lines);
    const ctx = prepareContext(sections);

    const results = {};
    TEST_DEFS.forEach(def=>{ results[def.id] = runTamizajeField(def, ctx); });

    lastResults = results;
    renderAll(results);
    const totalCounted = TEST_DEFS.map(d=>results[d.id].stats.counted).reduce((a,b)=>a+b,0);
    const rowsValidated = ctx.tamS.rows.length;
    setStatus('ok', 'Listo · procesados los 10 tamizajes (' + totalCounted + ' registros contabilizados sobre ' + rowsValidated + ' filas validadas).');
    await chargeTokens(rowsValidated);
  }catch(err){
    console.error(err);
    setStatus('err', 'No se pudo procesar el archivo: ' + err.message);
  }
}

function decodeBuffer(buf){
  const bytes = new Uint8Array(buf);
  if(bytes[0]===0xFF && bytes[1]===0xFE) return new TextDecoder('utf-16le').decode(buf);
  if(bytes[0]===0xFE && bytes[1]===0xFF) return new TextDecoder('utf-16be').decode(buf);
  return new TextDecoder('utf-8').decode(buf);
}

function parseFields(line){
  let s = line;
  if(s.startsWith('`')) s = s.slice(1);
  if(s.endsWith('`')) s = s.slice(0,-1);
  return s.split('`;`');
}

function parseSections(lines){
  const sections = {};
  let modulo=null, seccion=null, state=null, currentKey=null, currentEntorno=null;
  for(const raw of lines){
    const t = raw.trim();
    if(t.startsWith('Entorno =>')){ currentEntorno = t.split('=>').slice(1).join('=>').trim(); continue; }
    if(t.startsWith('Módulo =>') || t.startsWith('Modulo =>')){ modulo = t.split('=>').slice(1).join('=>').trim(); state=null; continue; }
    if(t.startsWith('Sección =>') || t.startsWith('Seccion =>')){ seccion = t.split('=>').slice(1).join('=>').trim(); state='await_header'; continue; }
    if(state==='await_header'){
      if(t==='') continue;
      const header = parseFields(raw.replace(/\r?\n$/,''));
      currentKey = modulo+'||'+seccion;
      sections[currentKey] = {modulo, seccion, header, rows:[], entornos:[]};
      state='data';
      continue;
    }
    if(state==='data'){
      if(t==='') continue;
      const row = parseFields(raw.replace(/\r?\n$/,''));
      sections[currentKey].rows.push(row);
      sections[currentKey].entornos.push(currentEntorno);
      continue;
    }
  }
  return sections;
}

function findCol(header, pred){
  for(let i=0;i<header.length;i++){ if(pred(norm(header[i]))) return i; }
  return -1;
}
function get(row, idx){ return (idx!==-1 && idx<row.length) ? (row[idx]||'').trim() : ''; }
function mapSexo(s){ s=s.toUpperCase(); if(s.includes('HOMBRE')) return 'M'; if(s.includes('MUJER')) return 'F'; return 'I'; }
function isGestante(s){ s=s.trim().toUpperCase(); if(s==='') return false; if(s.includes('NO APLICA')) return false; return true; }

function findSectionByName(sections, modulo, keyword){
  for(const k in sections){
    if(sections[k].modulo===modulo && sections[k].seccion.toUpperCase().includes(keyword)){
      return sections[k];
    }
  }
  return null;
}

function prepareContext(sections){
  let targetModulo = null;
  for(const k in sections){
    if(sections[k].modulo && sections[k].modulo.toUpperCase().includes('SESIONES COLECTIVAS ENTORNOS')){
      targetModulo = sections[k].modulo; break;
    }
  }
  if(!targetModulo) throw new Error('No se encontró el módulo "Sesiones Colectivas Entornos" en el archivo.');

  const instS = findSectionByName(sections, targetModulo, 'INSTITUC');
  const persS = findSectionByName(sections, targetModulo, 'PERSONAS');
  const tamS  = findSectionByName(sections, targetModulo, 'TAMIZAJE');
  if(!instS) throw new Error('No se encontró la sección de Institución/Establecimiento (con la UPZ).');
  if(!persS) throw new Error('No se encontró la sección de Personas.');
  if(!tamS) throw new Error('No se encontró la sección de Tamizajes.');

  const fichaIdxInst = findCol(instS.header, h=>h==='FICHA_FIC');
  const localidadIdx = findCol(instS.header, h=>h==='LOCALIDAD');
  const upzIdx = findCol(instS.header, h=>h.includes('UPZ'));

  const fichaIdxPers = findCol(persS.header, h=>h==='FICHA_FIC');
  const docIdxPers = findCol(persS.header, h=>h.includes('DOCUMENTO') && !h.includes('TIPO'));
  const sexoIdx = findCol(persS.header, h=>h==='SEXO');
  const cursoIdx = findCol(persS.header, h=>h.includes('CURSO DE VIDA'));
  const gestIdx = findCol(persS.header, h=>h.includes('GESTAC'));
  const nombresIdx = findCol(persS.header, h=>h==='NOMBRES');
  const apellidosIdx = findCol(persS.header, h=>h==='APELLIDOS');
  const edadIdx = findCol(persS.header, h=>h==='EDAD');

  const fichaIdxTam = findCol(tamS.header, h=>h==='FICHA_FIC');
  const docIdxTam = findCol(tamS.header, h=>h.includes('DOCUMENTO') && !h.includes('TIPO'));
  const fechaIdxTam = findCol(tamS.header, h=>h==='FECHA_INTERVENCION');
  const localidadFicIdxTam = findCol(tamS.header, h=>h==='LOCALIDAD_FIC');

  if(upzIdx===-1) throw new Error('No se encontró la columna de UPZ.');

  const instMap = {};
  instS.rows.forEach((row,i)=>{
    const ficha = get(row, fichaIdxInst);
    instMap[ficha] = { entorno: instS.entornos[i] || '', localidad: get(row, localidadIdx), upz: get(row, upzIdx) };
  });

  const persMap = {};
  const persByDocOnly = {};
  persS.rows.forEach(row=>{
    const ficha = get(row, fichaIdxPers);
    const doc = get(row, docIdxPers);
    const edadRaw = get(row, edadIdx);
    const edadNum = edadRaw==='' ? null : parseInt(edadRaw,10);
    const persona = {
      sexo: get(row, sexoIdx), curso: get(row, cursoIdx), gest: get(row, gestIdx),
      nombre: (get(row,nombresIdx)+' '+get(row,apellidosIdx)).trim(),
      edad: (edadNum!=null && !isNaN(edadNum)) ? edadNum : null,
    };
    persMap[ficha+'|'+doc] = persona;
    if(doc!=='' && !persByDocOnly[doc]) persByDocOnly[doc] = {...persona, fichaOrigen: ficha};
  });

  return {instMap, persMap, persByDocOnly, tamS, fichaIdxTam, docIdxTam, fechaIdxTam, localidadFicIdxTam};
}

function runTamizajeField(def, ctx){
  const {instMap, persMap, persByDocOnly, tamS, fichaIdxTam, docIdxTam, fechaIdxTam, localidadFicIdxTam} = ctx;
  const sourceIdx = findCol(tamS.header, h=>h===norm(def.sourceColName));
  const stats = {totalTamRows: tamS.rows.length, sinResultado:0, sinPersonaCoincidente:0, viaOtraFicha:0, sinUpzCoincidente:0, fueraDeRangoEdad:0, counted:0};

  if(sourceIdx===-1){
    return {rows:[], stats, grandTotal:0, def, categories:def.categories||[], review:[], error:'No se encontró la columna "'+def.sourceColName+'" en Tamizajes.'};
  }

  const categories = def.categories;
  const mapCategory = def.mapCategory;

  const agg = {};
  const review = [];
  tamS.rows.forEach(row=>{
    function addReview(motivo, ficha, doc, nombre){
      review.push({
        motivo, nombre: nombre||'',
        documento: doc, fichaFic: ficha,
        fecha: get(row, fechaIdxTam), localidad: get(row, localidadFicIdxTam),
      });
    }
    const ficha = get(row, fichaIdxTam);
    const doc = get(row, docIdxTam);
    const raw = get(row, sourceIdx);
    if(raw===''){ stats.sinResultado++; return; }
    const cat = mapCategory(raw);
    if(!cat){ stats.sinResultado++; return; }
    let p = persMap[ficha+'|'+doc];
    if(!p && persByDocOnly[doc]){
      p = persByDocOnly[doc];
      stats.viaOtraFicha++;
      addReview('Persona encontrada en otra Ficha (Ficha original: '+ p.fichaOrigen +')', ficha, doc, p.nombre);
    }
    if(!p){ stats.sinPersonaCoincidente++; addReview('Sin persona coincidente (Ficha + documento)', ficha, doc, ''); return; }
    const ageKey = resolveAgeKey(def.ageGroups, p);
    if(!ageKey){ stats.fueraDeRangoEdad++; addReview('Fuera de rango de edad (curso de vida: '+(p.curso||'sin dato')+', edad: '+(p.edad!=null?p.edad:'sin dato')+')', ficha, doc, p.nombre); return; }
    const inst = instMap[ficha];
    if(!inst){ stats.sinUpzCoincidente++; addReview('Sin UPZ asociada (Ficha sin Institución)', ficha, doc, p.nombre); return; }
    const sexKey = mapSexo(p.sexo);
    const gkey = inst.localidad+'||'+inst.upz;
    if(!agg[gkey]) agg[gkey] = {entorno:inst.entorno, localidad:inst.localidad, upz:inst.upz, gestSi:0, gestNo:0, cats:{}};
    const a = agg[gkey];
    if(isGestante(p.gest)) a.gestSi++; else a.gestNo++;
    if(!a.cats[cat]) a.cats[cat]={};
    if(!a.cats[cat][ageKey]) a.cats[cat][ageKey]={M:0,F:0,I:0};
    a.cats[cat][ageKey][sexKey]++;
    stats.counted++;
  });

  let rows = Object.values(agg).map(a=>{
    const name = upzNameFor(a.upz);
    let total = 0;
    categories.forEach(c=>def.ageGroups.forEach(g=>{
      const cell = (a.cats[c.key] && a.cats[c.key][g.key]) || {M:0,F:0,I:0};
      total += cell.M+cell.F+cell.I;
    }));
    return {...a, upzName:name, total};
  });
  rows.sort((x,y)=> x.localidad.localeCompare(y.localidad) || (upzNumber(x.upz)-upzNumber(y.upz)));
  const grandTotal = rows.reduce((s,r)=>s+r.total,0);

  return {rows, stats, grandTotal, def, categories, review};
}

// ---------- rendering ----------
function renderAll(results){
  resultsCard.style.display='block';
  tabsEl.innerHTML='';
  panelsEl.innerHTML='';

  const topLevel = [
    {id:'findrisc', label:'FINDRISC'},
    {id:'oms', label:'OMS'},
    {id:'cancer', label:'Cáncer'},
    {id:'visual', label:'Visual'},
    {id:'auditivo', label:'Auditivo'},
    {id:'resp', label:'Respiratorias'},
  ];
  topLevel.forEach((t,i)=>{
    const btn = document.createElement('button');
    btn.className = 'tab' + (i===0?' active':'');
    btn.textContent = t.label;
    btn.dataset.tab = t.id;
    btn.addEventListener('click', ()=>switchTab(t.id));
    tabsEl.appendChild(btn);

    const panel = document.createElement('div');
    panel.className = 'panel' + (i===0?' active':'');
    panel.id = 'panel-'+t.id;
    if(t.id==='visual'){
      ['visual_od','visual_oi'].forEach(cid=>{
        const block = document.createElement('div');
        block.className='sub-block';
        block.innerHTML = renderTestBlock(results[cid], true);
        panel.appendChild(block);
      });
    } else if(t.id==='auditivo'){
      ['auditivo_od','auditivo_oi'].forEach(cid=>{
        const block = document.createElement('div');
        block.className='sub-block';
        block.innerHTML = renderTestBlock(results[cid], true);
        panel.appendChild(block);
      });
    } else if(t.id==='resp'){
      ['resp_0_17','resp_18_39','resp_40'].forEach(cid=>{
        const block = document.createElement('div');
        block.className='sub-block';
        block.innerHTML = renderTestBlock(results[cid], true);
        panel.appendChild(block);
      });
    } else {
      panel.innerHTML = renderTestBlock(results[t.id], false);
    }
    panelsEl.appendChild(panel);
  });

  assumptionsBox.innerHTML = `
    Fuente cruzada para las 10 hojas: módulo <code>Sesiones Colectivas Entornos</code> &rarr; Institución (UPZ) + Personas (sexo, curso de vida, edad, gestación) + Tamizajes (resultado de cada prueba), unidas por <code>Ficha_fic</code> y número de documento.
    Estructura y categorías validadas contra el tablero oficial (FINDRISC, OMS, Visual OD/OI, Auditivo OD/OI, Respiratorias &lt;18/18-39/40+, Cáncer) &mdash; sin columna de %, igual que el tablero.
    Visual y Auditivo usan los 6 grupos de curso de vida (Primera infancia a Vejez); Respiratorias usa los grupos de edad propios de cada prueba (6-11/12-17, 18-28/29-39, 40-59/&gt;=60).
    El nombre de UPZ usa el catálogo oficial de Bogotá.
  `;
}

function switchTab(id){
  document.querySelectorAll('.tab').forEach(b=>b.classList.toggle('active', b.dataset.tab===id));
  document.querySelectorAll('.panel').forEach(p=>p.classList.toggle('active', p.id==='panel-'+id));
}

function renderReviewTable(review){
  if(!review || review.length===0) return '<p style="color:var(--muted);font-size:13px;margin-top:8px;">No hay registros pendientes de revisión.</p>';
  const MAX = 50;
  let html = '<table class="review-table"><thead><tr><th>Motivo</th><th>Nombre</th><th>Documento</th><th>Ficha_fic</th><th>Fecha</th><th>Localidad</th></tr></thead><tbody>';
  review.slice(0,MAX).forEach(r=>{
    html += `<tr><td>${r.motivo}</td><td>${r.nombre}</td><td>${r.documento}</td><td>${r.fichaFic}</td><td>${r.fecha}</td><td>${r.localidad}</td></tr>`;
  });
  html += '</tbody></table>';
  if(review.length>MAX) html += `<p class="review-more">Mostrando ${MAX} de ${review.length}. La lista completa queda en la hoja "Revisión manual" del Excel.</p>`;
  return html;
}

function renderTestBlock(result, isSub){
  const {rows, stats, grandTotal, def, categories, review, error} = result;
  if(error){
    return `<h3 class="block-title">${def.tabLabel}</h3><div class="diag-body" style="background:var(--warn-bg);border-radius:10px;padding:12px;">${error}</div>`;
  }
  const titleHtml = isSub ? `<h3 class="block-title">${def.tabLabel}</h3>` : '';
  const statGrid = `
    <div class="stat-grid">
      <div class="stat"><div class="n">${rows.length}</div><div class="l">UPZ con datos</div></div>
      <div class="stat"><div class="n">${grandTotal}</div><div class="l">Personas tamizadas</div></div>
      <div class="stat"><div class="n">${rows.reduce((s,r)=>s+r.gestSi,0)}</div><div class="l">Gestantes (Sí)</div></div>
      <div class="stat"><div class="n">${stats.totalTamRows}</div><div class="l">Filas leídas en Tamizajes</div></div>
    </div>`;

  let thead = '<tr><th>Localidad</th><th>UPZ</th><th>Nombre UPZ</th><th>Total</th>';
  categories.forEach(c=>{ thead += `<th>${c.label.length>22 ? c.label.slice(0,20)+'…' : c.label}</th>`; });
  thead += '<th>Gest. Sí</th><th>Gest. No</th><th>%</th></tr>';

  let body = '';
  rows.forEach(r=>{
    const catTotal = key => {
      let t=0; def.ageGroups.forEach(g=>{ const c=(r.cats[key]&&r.cats[key][g.key])||{M:0,F:0,I:0}; t+=c.M+c.F+c.I; });
      return t || '';
    };
    const pct = grandTotal ? (r.total/grandTotal*100).toFixed(1)+'%' : '';
    body += `<tr><td>${r.localidad}</td><td>${r.upz}</td><td>${r.upzName}</td><td>${r.total}</td>`;
    categories.forEach(c=>{ body += `<td>${catTotal(c.key)}</td>`; });
    body += `<td>${r.gestSi||''}</td><td>${r.gestNo||''}</td><td>${pct}</td></tr>`;
  });

  const diag = `
    <details class="diag">
      <summary>Ver detalle de lo que se incluyó y excluyó</summary>
      <div class="diag-body">
        Filas leídas: <b>${stats.totalTamRows}</b> ·
        Sin resultado: <b>${stats.sinResultado}</b> ·
        Sin persona coincidente: <b>${stats.sinPersonaCoincidente}</b> ·
        Encontrada en otra Ficha: <b>${stats.viaOtraFicha}</b> ·
        Fuera de rango de edad: <b>${stats.fueraDeRangoEdad}</b> ·
        Sin UPZ asociada: <b>${stats.sinUpzCoincidente}</b> ·
        Contabilizadas: <b>${stats.counted}</b>
      </div>
    </details>
    <details class="diag">
      <summary>Ver nombres, documentos y Ficha_fic pendientes de revisión (${review.length})</summary>
      <div class="diag-body" style="background:var(--bg);">${renderReviewTable(review)}</div>
    </details>`;

  return titleHtml + statGrid + diag + `<div class="scrollx" style="margin-top:14px;"><table class="preview"><thead>${thead}</thead><tbody>${body}</tbody></table></div>`;
}

document.getElementById('downloadBtn').addEventListener('click', ()=>{
  if(!lastResults) return;
  downloadExcel(lastResults);
});

function buildSheet(result){
  const {rows, grandTotal, def, categories} = result;
  const ageGroups = def.ageGroups;
  const catBlockWidth = ageGroups.length*4;
  const totalStart = 6 + categories.length*catBlockWidth;
  const TOTAL_COLS = totalStart + 4;

  const aoa = [];
  for(let i=0;i<5;i++) aoa.push(new Array(TOTAL_COLS).fill(''));
  aoa[0][0] = 'Mes de diligencimiento de tablero de control:';
  aoa[0][6] = def.title;
  aoa[1][0] = 'Mes ejecucion de actividades:';
  aoa[2][0]='Entorno'; aoa[2][1]='Localidad'; aoa[2][2]='Número UPZ'; aoa[2][3]='Nombre UPZ'; aoa[2][4]='Gestantes';
  aoa[2][totalStart]='TOTAL';
  aoa[def.gestRow][4]=def.siLabel; aoa[def.gestRow][5]=def.noLabel;

  categories.forEach((c,ci)=>{
    const start = 6 + ci*catBlockWidth;
    aoa[2][start] = c.label;
    ageGroups.forEach((g,gi)=>{
      const ageStart = start + gi*4;
      aoa[3][ageStart] = g.label;
      SEXES.forEach((sx,si)=>{ aoa[4][ageStart+si] = SEX_LABEL[sx]; });
    });
  });
  SEXES.forEach((sx,si)=>{ aoa[4][totalStart+si] = SEX_LABEL[sx]; });

  rows.forEach((r,ri)=>{
    const rowArr = new Array(TOTAL_COLS).fill('');
    rowArr[0] = ri===0 ? r.entorno : '';
    rowArr[1] = r.localidad; rowArr[2] = r.upz; rowArr[3] = r.upzName;
    rowArr[4] = r.gestSi || ''; rowArr[5] = r.gestNo || '';
    let totM=0, totF=0, totI=0;
    categories.forEach((c,ci)=>{
      const start = 6 + ci*catBlockWidth;
      ageGroups.forEach((g,gi)=>{
        const ageStart = start + gi*4;
        const cell = (r.cats[c.key] && r.cats[c.key][g.key]) || {M:0,F:0,I:0};
        rowArr[ageStart+0]=cell.M||''; rowArr[ageStart+1]=cell.F||''; rowArr[ageStart+2]=cell.I||''; rowArr[ageStart+3]=(cell.M+cell.F+cell.I)||'';
        totM+=cell.M; totF+=cell.F; totI+=cell.I;
      });
    });
    rowArr[totalStart]=totM||''; rowArr[totalStart+1]=totF||''; rowArr[totalStart+2]=totI||''; rowArr[totalStart+3]=(totM+totF+totI)||'';
    aoa.push(rowArr);
  });

  const ws = XLSX.utils.aoa_to_sheet(aoa);
  const merges = [];
  merges.push({s:{r:0,c:6},e:{r:0,c:totalStart-1}});
  [0,1,2,3].forEach(c=>merges.push({s:{r:2,c:c},e:{r:4,c:c}}));
  merges.push({s:{r:2,c:4},e:{r:2,c:5}});
  categories.forEach((c,ci)=>{
    const start = 6+ci*catBlockWidth;
    merges.push({s:{r:2,c:start},e:{r:2,c:start+catBlockWidth-1}});
    ageGroups.forEach((g,gi)=>{
      const ageStart = start+gi*4;
      merges.push({s:{r:3,c:ageStart},e:{r:3,c:ageStart+3}});
    });
  });
  merges.push({s:{r:2,c:totalStart},e:{r:2,c:totalStart+3}});
  ws['!merges'] = merges;
  ws['!cols'] = new Array(TOTAL_COLS).fill({wch:11});
  ws['!cols'][0]={wch:14}; ws['!cols'][1]={wch:16}; ws['!cols'][2]={wch:11}; ws['!cols'][3]={wch:22};
  applySheetStyles(ws, TOTAL_COLS, rows.length);
  return ws;
}

// ---- colores y centrado del Excel descargado ----
const BORDER_THIN = {style:'thin', color:{rgb:'B7CCDD'}};
const CELL_BORDER = {top:BORDER_THIN, bottom:BORDER_THIN, left:BORDER_THIN, right:BORDER_THIN};
function setCellStyle(ws, r, c, style){
  const ref = XLSX.utils.encode_cell({r, c});
  if(ws[ref]) ws[ref].s = style;
}
function applySheetStyles(ws, totalCols, numDataRows){
  // etiquetas manuales (mes de diligenciamiento / mes de ejecución)
  setCellStyle(ws, 0, 0, {font:{bold:true}, alignment:{horizontal:'left', vertical:'center'}});
  setCellStyle(ws, 1, 0, {font:{bold:true}, alignment:{horizontal:'left', vertical:'center'}});
  // título de la prueba (fila 0, desde la col 6)
  for(let c=6;c<totalCols;c++){
    setCellStyle(ws, 0, c, {
      fill:{fgColor:{rgb:'0B3D63'}}, font:{color:{rgb:'FFFFFF'}, bold:true, sz:13},
      alignment:{horizontal:'center', vertical:'center', wrapText:true}, border:CELL_BORDER,
    });
  }
  // categorías (fila 2), grupos de edad (fila 3), sexo/Si-No (fila 4)
  for(let c=0;c<totalCols;c++){
    setCellStyle(ws, 2, c, {
      fill:{fgColor:{rgb:'1B6CA8'}}, font:{color:{rgb:'FFFFFF'}, bold:true},
      alignment:{horizontal:'center', vertical:'center', wrapText:true}, border:CELL_BORDER,
    });
    setCellStyle(ws, 3, c, {
      fill:{fgColor:{rgb:'5B9BD5'}}, font:{color:{rgb:'FFFFFF'}, bold:true},
      alignment:{horizontal:'center', vertical:'center', wrapText:true}, border:CELL_BORDER,
    });
    setCellStyle(ws, 4, c, {
      fill:{fgColor:{rgb:'DEEBF7'}}, font:{color:{rgb:'0B3D63'}, bold:true},
      alignment:{horizontal:'center', vertical:'center', wrapText:true}, border:CELL_BORDER,
    });
  }
  // filas de datos, con bandas alternas y texto centrado (izquierda en Localidad/Nombre UPZ)
  for(let i=0;i<numDataRows;i++){
    const r = 5+i;
    const bg = (i%2===1) ? 'F3F5F4' : 'FFFFFF';
    for(let c=0;c<totalCols;c++){
      const horizontal = (c===1 || c===3) ? 'left' : 'center';
      setCellStyle(ws, r, c, {
        fill:{fgColor:{rgb:bg}}, alignment:{horizontal, vertical:'center'}, border:CELL_BORDER,
      });
    }
  }
}

const SHEET_NAMES = {
  findrisc:'FINDRISC', oms:'OMS',
  visual_od:'VISUAL(OD)', visual_oi:'VISUAL(OI)',
  auditivo_od:'AUDITIVO (OD)', auditivo_oi:'AUDITIVO (OI)',
  resp_0_17:'RESPIRATORIAS <18 AÑOS', resp_18_39:'RESPIRATORIAS 18-39 AÑOS', resp_40:'RESPIRATORIAS 40 AÑOS',
  cancer:'CÁNCER',
};

function buildReviewSheet(results){
  const aoa = [['Prueba','Motivo','Nombre','Documento','Ficha_fic','Fecha','Localidad']];
  TEST_DEFS.forEach(def=>{
    const r = results[def.id];
    if(r.error || !r.review) return;
    r.review.forEach(item=>{
      aoa.push([SHEET_NAMES[def.id], item.motivo, item.nombre, item.documento, item.fichaFic, item.fecha, item.localidad]);
    });
  });
  const ws = XLSX.utils.aoa_to_sheet(aoa);
  ws['!cols'] = [{wch:22},{wch:42},{wch:28},{wch:14},{wch:12},{wch:12},{wch:16}];
  for(let c=0;c<7;c++){
    setCellStyle(ws, 0, c, {
      fill:{fgColor:{rgb:'1B6CA8'}}, font:{color:{rgb:'FFFFFF'}, bold:true},
      alignment:{horizontal:'center', vertical:'center'}, border:CELL_BORDER,
    });
  }
  for(let i=1;i<aoa.length;i++){
    const bg = (i%2===0) ? 'F3F5F4' : 'FFFFFF';
    for(let c=0;c<7;c++){
      const horizontal = (c===1 || c===2) ? 'left' : 'center';
      setCellStyle(ws, i, c, {fill:{fgColor:{rgb:bg}}, alignment:{horizontal, vertical:'center'}, border:CELL_BORDER});
    }
  }
  return ws;
}

function downloadExcel(results){
  const wb = XLSX.utils.book_new();
  TEST_DEFS.forEach(def=>{
    const r = results[def.id];
    if(r.error) return;
    const ws = buildSheet(r);
    XLSX.utils.book_append_sheet(wb, ws, SHEET_NAMES[def.id]);
  });
  XLSX.utils.book_append_sheet(wb, buildReviewSheet(results), 'Revisión manual');
  const stamp = new Date().toISOString().slice(0,10);
  XLSX.writeFile(wb, `tamizajes_sesiones_colectivas_${stamp}.xlsx`);
}
</script>
</body>
</html>