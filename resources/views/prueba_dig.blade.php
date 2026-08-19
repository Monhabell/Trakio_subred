<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Prueba de Digitación — Ficha SSC</title>
<style>
  :root {
    --blue:#185FA5;--blue-l:#E6F1FB;--blue-m:#378ADD;
    --green:#0F6E56;--green-l:#E1F5EE;--green-m:#1D9E75;
    --red:#A32D2D;--red-l:#FCEBEB;--red-m:#E24B4A;
    --amber:#854F0B;--amber-l:#FAEEDA;--amber-m:#EF9F27;
    --g50:#F8F8F7;--g100:#F1EFE8;--g200:#D3D1C7;--g400:#888780;--g600:#5F5E5A;--g900:#2C2C2A;
    --r:8px;--rl:12px;
  }
  *{box-sizing:border-box;margin:0;padding:0}
  body{font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',system-ui,sans-serif;background:var(--g50);color:var(--g900);font-size:14px;line-height:1.5;min-height:100vh}
  .screen{display:none}.screen.active{display:block}
  .app-header{background:#fff;border-bottom:1px solid var(--g200);padding:0 2rem;display:flex;align-items:center;justify-content:space-between;height:56px;position:sticky;top:0;z-index:100}
  .logo{display:flex;align-items:center;gap:10px}
  .logo-icon{width:32px;height:32px;background:var(--blue);border-radius:8px;display:flex;align-items:center;justify-content:center}
  .logo-icon svg{width:18px;height:18px;fill:#fff}
  .logo-text{font-size:14px;font-weight:600}
  .logo-sub{font-size:11px;color:var(--g400);margin-top:1px}
  .header-timer{display:none;align-items:center;gap:8px;background:var(--g100);border-radius:var(--r);padding:6px 14px}
  .header-timer.visible{display:flex}
  .timer-val{font-size:18px;font-weight:700;font-family:'Courier New',monospace;min-width:48px}
  .timer-val.urgent{color:var(--red-m)}
  .badge-ficha{background:var(--blue-l);color:var(--blue);font-size:12px;font-weight:600;padding:4px 10px;border-radius:20px;display:none}
  .badge-ficha.visible{display:inline}
  /* intro */
  .intro-wrap{max-width:560px;margin:0 auto;padding:3rem 1rem}
  .eyebrow{font-size:11px;font-weight:600;letter-spacing:.08em;color:var(--blue);text-transform:uppercase;margin-bottom:.75rem}
  .intro-title{font-size:28px;font-weight:700;margin-bottom:.5rem;line-height:1.2}
  .intro-sub{font-size:15px;color:var(--g600);margin-bottom:2rem}
  .info-cards{display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:2rem}
  .info-card{background:#fff;border:1px solid var(--g200);border-radius:var(--rl);padding:1rem;display:flex;align-items:flex-start;gap:10px}
  .ic-icon{width:32px;height:32px;border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:16px}
  .ic-b{background:var(--blue-l)}.ic-g{background:var(--green-l)}.ic-a{background:var(--amber-l)}.ic-gr{background:var(--g100)}
  .ic-title{font-size:13px;font-weight:600;margin-bottom:2px}
  .ic-desc{font-size:12px;color:var(--g600)}
  .name-field{margin-bottom:1.5rem}
  .name-field label{display:block;font-size:13px;font-weight:500;margin-bottom:6px}
  .name-field input{width:100%;padding:10px 14px;font-size:14px;border:1.5px solid var(--g200);border-radius:var(--r);background:#fff;color:var(--g900);transition:border-color .15s}
  .name-field input:focus{outline:none;border-color:var(--blue-m)}
  .btn-start{width:100%;padding:12px;font-size:15px;font-weight:600;background:var(--blue);color:#fff;border:none;border-radius:var(--r);cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px}
  .btn-start:hover{background:#0c4480}
  /* exam */
  .exam-wrap{max-width:820px;margin:0 auto;padding:2rem 1rem}
  .progress-steps{display:flex;gap:8px;margin-bottom:.5rem}
  .step{flex:1;height:4px;border-radius:2px;background:var(--g200);transition:background .3s}
  .step.done{background:var(--green-m)}.step.active{background:var(--blue-m)}
  .progress-label{font-size:12px;color:var(--g600);margin-bottom:1.25rem}
  .section-card{background:#fff;border:1px solid var(--g200);border-radius:var(--rl);padding:1.25rem;margin-bottom:1rem}
  .section-head{display:flex;align-items:center;gap:8px;margin-bottom:1rem;padding-bottom:.75rem;border-bottom:1px solid var(--g100)}
  .section-num{width:24px;height:24px;border-radius:50%;background:var(--g100);color:var(--g600);font-size:12px;font-weight:700;display:flex;align-items:center;justify-content:center;flex-shrink:0}
  .section-title{font-size:14px;font-weight:600}
  .fg{display:grid;gap:10px;margin-bottom:10px}
  .fg2{grid-template-columns:1fr 1fr}
  .fg3{grid-template-columns:1fr 1fr 1fr}
  .fg4{grid-template-columns:1fr 1fr 1fr 1fr}
  .fg5{grid-template-columns:1fr 1fr 1fr 1fr 1fr}
  .fg1{grid-template-columns:1fr}
  .field-group{display:flex;flex-direction:column;gap:4px}
  .field-group label{font-size:11px;font-weight:600;color:var(--g600);text-transform:uppercase;letter-spacing:.04em}
  .field-group input,.field-group select{padding:7px 10px;font-size:13px;border:1.5px solid var(--g200);border-radius:var(--r);background:#fff;color:var(--g900);height:34px;width:100%;transition:border-color .15s,background .15s}
  .field-group input:focus,.field-group select:focus{outline:none;border-color:var(--blue-m)}
  .ok{border-color:var(--green-m)!important;background:var(--green-l)!important}
  .err{border-color:var(--red-m)!important;background:var(--red-l)!important}
  /* tabla participantes */
  .ptable{width:100%;border-collapse:collapse;font-size:12px}
  .ptable th{background:var(--g100);color:var(--g600);font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.04em;padding:6px 8px;border:1px solid var(--g200);text-align:center;white-space:nowrap}
  .ptable td{padding:4px 5px;border:1px solid var(--g200)}
  .ptable td input,.ptable td select{width:100%;font-size:12px;padding:4px 6px;border:1.5px solid var(--g200);border-radius:6px;background:#fff;color:var(--g900);height:30px}
  .ptable td input:focus,.ptable td select:focus{outline:none;border-color:var(--blue-m)}
  .ptable td.rn{text-align:center;color:var(--g400);font-weight:600;width:28px}
  .ptable td input.ok,.ptable td select.ok{border-color:var(--green-m)!important;background:var(--green-l)!important}
  .ptable td input.err,.ptable td select.err{border-color:var(--red-m)!important;background:var(--red-l)!important}
  .table-wrap{overflow-x:auto}
  .btn-submit{width:100%;padding:12px;font-size:14px;font-weight:600;background:var(--blue);color:#fff;border:none;border-radius:var(--r);cursor:pointer;margin-top:.5rem;display:flex;align-items:center;justify-content:center;gap:8px}
  .btn-submit:hover{background:#0c4480}
  .btn-submit:disabled{background:var(--g200);color:var(--g400);cursor:not-allowed}
  /* results */
  .results-wrap{max-width:640px;margin:0 auto;padding:2rem 1rem}
  .score-hero{text-align:center;padding:2.5rem 1rem;margin-bottom:1.5rem;background:#fff;border:1px solid var(--g200);border-radius:var(--rl)}
  .score-big{font-size:72px;font-weight:800;line-height:1;margin-bottom:.25rem}
  .score-verdict{display:inline-block;font-size:13px;font-weight:600;padding:4px 14px;border-radius:20px;margin-top:.5rem}
  .stats-row{display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px;margin-bottom:1.5rem}
  .stat-box{background:#fff;border:1px solid var(--g200);border-radius:var(--rl);padding:1rem;text-align:center}
  .stat-num{font-size:32px;font-weight:700;display:block}
  .stat-lbl{font-size:12px;color:var(--g600)}
  .ficha-result{background:#fff;border:1px solid var(--g200);border-radius:var(--rl);margin-bottom:1rem;overflow:hidden}
  .ficha-head{padding:1rem 1.25rem;display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid var(--g100)}
  .ficha-title{font-size:14px;font-weight:600}
  .ficha-pct{font-size:18px;font-weight:700}
  .ficha-bar-wrap{height:4px;background:var(--g100)}
  .ficha-bar{height:100%;transition:width .8s ease}
  .ficha-meta{padding:.75rem 1.25rem;font-size:12px;color:var(--g600)}
  .error-list{padding:.5rem 1.25rem}
  .error-item{display:flex;align-items:flex-start;justify-content:space-between;padding:5px 0;border-bottom:1px solid var(--g100);font-size:12px}
  .error-item:last-child{border-bottom:none}
  .err-field{color:var(--g600);flex:1;padding-right:12px}
  .err-vals{text-align:right;flex-shrink:0}
  .err-user{color:var(--red-m)}
  .err-correct{color:var(--green-m)}
  .btn-retry{width:100%;padding:11px;font-size:14px;font-weight:500;background:#fff;color:var(--g900);border:1.5px solid var(--g200);border-radius:var(--r);cursor:pointer;margin-top:.5rem}
  .btn-retry:hover{background:var(--g100)}
  .toast{position:fixed;bottom:24px;left:50%;transform:translateX(-50%);background:var(--g900);color:#fff;font-size:13px;padding:10px 20px;border-radius:var(--r);opacity:0;transition:opacity .3s;z-index:999;pointer-events:none;white-space:nowrap}
  .toast.show{opacity:1}
  @media(max-width:640px){
    .info-cards{grid-template-columns:1fr}
    .fg2,.fg3,.fg4,.fg5{grid-template-columns:1fr 1fr}
    .app-header{padding:0 1rem}
    .score-big{font-size:52px}
  }
</style>
</head>
<body>

<header class="app-header">
  <div class="logo">
    <div class="logo-icon">
      <svg viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
    </div>
    <div>
      <div class="logo-text">Prueba SSC</div>
      <div class="logo-sub">Subred Norte · Entornos Más Bienestar</div>
    </div>
  </div>
  <div style="display:flex;align-items:center;gap:12px">
    <span class="badge-ficha" id="badge-ficha"></span>
    <div class="header-timer" id="header-timer">
      <span>⏱</span>
      <span class="timer-val" id="timer-display">05:00</span>
    </div>
  </div>
</header>

<!-- INTRO -->
<div id="s-intro" class="screen active">
  <div class="intro-wrap">
    <div class="eyebrow">Sistema de evaluación · Digitación</div>
    <h1 class="intro-title">Prueba de digitación<br>Ficha SSC</h1>
    <p class="intro-sub">Digita con precisión los datos de las fichas de Sesiones Colectivas.</p>
    <div class="info-cards">
      <div class="info-card"><div class="ic-icon ic-b">📋</div><div><div class="ic-title">3 fichas</div><div class="ic-desc">Institución, sesión y participantes completos</div></div></div>
      <div class="info-card"><div class="ic-icon ic-a">⏱</div><div><div class="ic-title">5 min por ficha</div><div class="ic-desc">Temporizador visible en la barra superior</div></div></div>
      <div class="info-card"><div class="ic-icon ic-g">✓</div><div><div class="ic-title">Evaluación automática</div><div class="ic-desc">Compara tus datos con la clave correcta</div></div></div>
      <div class="info-card"><div class="ic-icon ic-gr">🎯</div><div><div class="ic-title">Calificación /100</div><div class="ic-desc">Con detalle de errores por campo</div></div></div>
    </div>
    <div class="name-field">
      <label for="digitador-name">Nombre completo del digitador</label>
      <input type="text" id="digitador-name" placeholder="Ej. María Fernanda Gómez" autocomplete="name">
    </div>
    <button class="btn-start" onclick="startExam()">▶ Iniciar prueba</button>
  </div>
</div>

<!-- TYPING -->
<div id="s-typing" class="screen">
  <div class="intro-wrap">
    <div class="eyebrow">Paso 1 de 2 · Mecanografía</div>
    <h1 class="intro-title">Velocidad y precisión</h1>
    <p class="intro-sub">Transcribe el siguiente texto tal como aparece. Tienes 60 segundos.</p>
    <div class="section-card">
      <div id="typing-text" style="font-size:15px;line-height:1.8;color:var(--g600);margin-bottom:1rem"></div>
      <textarea id="typing-input" rows="4" placeholder="Comienza a escribir aquí..." disabled
        style="width:100%;padding:12px;font-size:14px;border:1.5px solid var(--g200);border-radius:var(--r);resize:none;font-family:inherit"></textarea>
      <div style="display:flex;justify-content:space-between;margin-top:.75rem;font-size:12px;color:var(--g600)">
        <span id="typing-accuracy">Precisión: 100%</span>
      </div>
    </div>
    <button class="btn-start" id="btn-typing-start" onclick="startTypingTest()">▶ Comenzar mecanografía</button>
  </div>
</div>

<!-- EXAM -->
<div id="s-exam" class="screen">
  <div class="exam-wrap">
    <div class="progress-steps" id="progress-steps"></div>
    <div class="progress-label" id="progress-label"></div>
    <div id="exam-form"></div>
    <button class="btn-submit" id="btn-submit" onclick="submitCurrent()">Guardar y continuar →</button>
  </div>
</div>

<!-- RESULTS -->
<div id="s-results" class="screen">
  <div class="results-wrap">
    <div class="score-hero">
      <div class="score-big" id="res-score-big">0</div>
      <div style="font-size:13px;color:var(--g600);margin-bottom:1rem">puntos sobre 100</div>
      <div style="font-size:16px;font-weight:600" id="res-name"></div>
      <div class="score-verdict" id="res-verdict"></div>
    </div>
    <div class="stats-row">
      <div class="stat-box"><span class="stat-num" id="res-correct" style="color:var(--green-m)">0</span><span class="stat-lbl">Correctos</span></div>
      <div class="stat-box"><span class="stat-num" id="res-wrong" style="color:var(--red-m)">0</span><span class="stat-lbl">Errores</span></div>
      <div class="stat-box"><span class="stat-num" id="res-time">0s</span><span class="stat-lbl">Tiempo total</span></div>
    </div>
    <div id="res-fichas"></div>
    <button class="btn-retry" onclick="restartExam()">↺ Reintentar prueba</button>
  </div>
</div>

<div class="toast" id="toast"></div>

<script>
const POPS = ["Ninguno","Adulto mayor","Discapacidad","Migrante","Victima conflicto armado","Habitante de calle","LGBTI","Gestante","Indígena","Afrodescendiente"];
const TIPOS = ["","CC","TI","RC","CE","PA","AS","MS","PE"];
const NAC = ["Colombiana","Venezolana","Ecuatoriana","Peruana","Haitiana","Otra"];
const MOD = ["","Presencial","Virtual","Mixta"];
const MC_OPTS = ["","Sí","No"];

const TYPING_TIME = 60;
const TYPING_TEXTS = [
  "A lo largo de su vida, el ser humano experimenta innumerables emociones, desde la alegría hasta la tristeza, pasando por la nostalgia, la ira y el amor. Cada una de ellas nos enseña algo valioso sobre nosotros mismos y sobre el mundo que nos rodea.",
  "La curiosidad del ser humano ha sido el motor que ha impulsado los grandes avances de la historia. Gracias a nuestra capacidad de cuestionar, hemos sido capaces de descubrir, inventar y crear todo aquello que hoy en día nos facilita la vida.",
  "El esfuerzo y la dedicación son las claves para lograr cualquier objetivo que nos propongamos. A lo largo del camino, es probable que encontremos dificultades, pero si mantenemos la perseverancia y la confianza en nosotros mismos, podremos superar cualquier obstáculo.",
  "La educación es el pilar fundamental para el desarrollo de una sociedad. A través del conocimiento, no solo se fomenta el crecimiento personal, sino que también se promueve una ciudadanía más crítica y comprometida con los desafíos del mundo contemporáneo.",
  "El trabajo en equipo es esencial para alcanzar grandes logros. A través de la colaboración, podemos combinar nuestras fortalezas individuales y superar juntos los desafíos que nos encontramos en el camino. La unión de talentos siempre genera mejores resultados."
];
let typingText="", typingTimerInt=null, typingSecsLeft=TYPING_TIME, typingStart=0, typingRes=null;

const EXAMS = [
  {
    titulo:"Ficha #001", subtitulo:"Jardín Infantil Los Robles · Usaquén",
    enc:{
      institucion:{l:"Nombre institución / establecimiento",v:"Jardín Infantil Los Robles"},
      lugar:{l:"Lugar de la actividad",v:"Salón comunal barrio Los Robles"},
      zona:{l:"Zona",v:"Urbana"},localidad:{l:"Localidad",v:"Usaquén"},
      upz:{l:"UPZ / UPR",v:"UPZ 9 - Verbenal"},
      manzana:{l:"Manzana de Cuidado",v:"No",sel:MC_OPTS},
      numero_mc:{l:"Número Manzana (si aplica)",v:""}
    },
    con:{
      modalidad:{l:"Modalidad de la sesión",v:"Presencial",sel:MOD},
      fecha_dd:{l:"Día (DD)",v:"14",mx:2},fecha_mm:{l:"Mes (MM)",v:"03",mx:2},
      fecha_aaaa:{l:"Año (AAAA)",v:"2025",mx:4},
      num_sesion:{l:"# Sesión",v:"1",mx:3},
      linea:{l:"Línea operativa",v:"Entorno Cuidador"},
      componente:{l:"Componente",v:"Promoción"},
      proceso:{l:"Proceso",v:"Educación para la salud"},
      tema:{l:"Tema",v:"Alimentación saludable"},
      docente:{l:"Docente / encargado",v:"Carmen Ríos Fuentes"}
    },
    part:[
      {nom:"Laura Patricia",ape:"Torres Medina",tip:"CC",doc:"52887634",
       fn_dd:"15",fn_mm:"08",fn_aaaa:"1985",edad:"39",nac:"Colombiana",pob:"Ninguno"},
      {nom:"Jorge Andrés",ape:"Gómez Pinilla",tip:"CC",doc:"80145223",
       fn_dd:"03",fn_mm:"11",fn_aaaa:"1979",edad:"45",nac:"Colombiana",pob:"Ninguno"},
      {nom:"María Fernanda",ape:"Ospina Castro",tip:"CC",doc:"43521890",
       fn_dd:"22",fn_mm:"04",fn_aaaa:"1991",edad:"33",nac:"Colombiana",pob:"Victima conflicto armado"},
      {nom:"Ricardo Alberto",ape:"Peña Morales",tip:"CC",doc:"79456123",
       fn_dd:"07",fn_mm:"01",fn_aaaa:"1968",edad:"57",nac:"Colombiana",pob:"Adulto mayor"},
    ]
  },
  {
    titulo:"Ficha #002", subtitulo:"Centro Comunitario La Esperanza · Suba",
    enc:{
      institucion:{l:"Nombre institución / establecimiento",v:"Centro Comunitario La Esperanza"},
      lugar:{l:"Lugar de la actividad",v:"Aula principal sede norte"},
      zona:{l:"Zona",v:"Urbana"},localidad:{l:"Localidad",v:"Suba"},
      upz:{l:"UPZ / UPR",v:"UPZ 27 - Suba"},
      manzana:{l:"Manzana de Cuidado",v:"Sí",sel:MC_OPTS},
      numero_mc:{l:"Número Manzana (si aplica)",v:"3"}
    },
    con:{
      modalidad:{l:"Modalidad de la sesión",v:"Virtual",sel:MOD},
      fecha_dd:{l:"Día (DD)",v:"22",mx:2},fecha_mm:{l:"Mes (MM)",v:"04",mx:2},
      fecha_aaaa:{l:"Año (AAAA)",v:"2025",mx:4},
      num_sesion:{l:"# Sesión",v:"2",mx:3},
      linea:{l:"Línea operativa",v:"Entorno Cuidador"},
      componente:{l:"Componente",v:"Prevención"},
      proceso:{l:"Proceso",v:"Detección temprana"},
      tema:{l:"Tema",v:"Salud mental"},
      docente:{l:"Docente / encargado",v:"Pablo Sánchez Vargas"}
    },
    part:[
      {nom:"Ana Milena",ape:"Bejarano López",tip:"CC",doc:"39754102",
       fn_dd:"29",fn_mm:"06",fn_aaaa:"1993",edad:"31",nac:"Colombiana",pob:"Ninguno"},
      {nom:"Carlos Eduardo",ape:"Mora Suárez",tip:"CC",doc:"79231456",
       fn_dd:"11",fn_mm:"12",fn_aaaa:"1972",edad:"52",nac:"Colombiana",pob:"Discapacidad"},
      {nom:"Diana Carolina",ape:"Herrera Niño",tip:"TI",doc:"1020345678",
       fn_dd:"05",fn_mm:"09",fn_aaaa:"2008",edad:"16",nac:"Colombiana",pob:"Ninguno"},
      {nom:"Luisa Alejandra",ape:"Quintero Rueda",tip:"CC",doc:"52901237",
       fn_dd:"18",fn_mm:"03",fn_aaaa:"1987",edad:"38",nac:"Venezolana",pob:"Migrante"},
    ]
  },
  {
    titulo:"Ficha #003", subtitulo:"IED Colegio Nuevo Horizonte · Engativá",
    enc:{
      institucion:{l:"Nombre institución / establecimiento",v:"IED Colegio Nuevo Horizonte"},
      lugar:{l:"Lugar de la actividad",v:"Biblioteca escolar"},
      zona:{l:"Zona",v:"Urbana"},localidad:{l:"Localidad",v:"Engativá"},
      upz:{l:"UPZ / UPR",v:"UPZ 74 - Engativá"},
      manzana:{l:"Manzana de Cuidado",v:"No",sel:MC_OPTS},
      numero_mc:{l:"Número Manzana (si aplica)",v:""}
    },
    con:{
      modalidad:{l:"Modalidad de la sesión",v:"Presencial",sel:MOD},
      fecha_dd:{l:"Día (DD)",v:"05",mx:2},fecha_mm:{l:"Mes (MM)",v:"06",mx:2},
      fecha_aaaa:{l:"Año (AAAA)",v:"2025",mx:4},
      num_sesion:{l:"# Sesión",v:"3",mx:3},
      linea:{l:"Línea operativa",v:"Entorno Cuidador"},
      componente:{l:"Componente",v:"Protección"},
      proceso:{l:"Proceso",v:"Gestión del riesgo"},
      tema:{l:"Tema",v:"Actividad física"},
      docente:{l:"Docente / encargado",v:"Sandra Milena Quiroga"}
    },
    part:[
      {nom:"Felipe Andrés",ape:"Ramírez Vela",tip:"RC",doc:"1140876523",
       fn_dd:"20",fn_mm:"02",fn_aaaa:"2018",edad:"7",nac:"Colombiana",pob:"Ninguno"},
      {nom:"Lucía Esperanza",ape:"Vargas Díaz",tip:"CC",doc:"52334901",
       fn_dd:"14",fn_mm:"10",fn_aaaa:"1965",edad:"59",nac:"Colombiana",pob:"Adulto mayor"},
      {nom:"Jhon Fredy",ape:"Castro Molina",tip:"CC",doc:"1032456789",
       fn_dd:"30",fn_mm:"07",fn_aaaa:"1990",edad:"34",nac:"Colombiana",pob:"Ninguno"},
      {nom:"Patricia Elena",ape:"Niño Bermúdez",tip:"CC",doc:"39812045",
       fn_dd:"09",fn_mm:"05",fn_aaaa:"1978",edad:"47",nac:"Colombiana",pob:"Victima conflicto armado"},
    ]
  }
];

// variable global  de tiempo 
const timer = 420; // 7 minutos en segundos 
let curEx=0, timerInt=null, secsLeft=timer, examRes=[], totalElap=0, examStart=0;

function showScr(id){document.querySelectorAll('.screen').forEach(s=>s.classList.remove('active'));document.getElementById(id).classList.add('active')}
function toast(msg){const t=document.getElementById('toast');t.textContent=msg;t.classList.add('show');setTimeout(()=>t.classList.remove('show'),2200)}

function startExam(){
  const n=document.getElementById('digitador-name').value.trim();
  if(!n){document.getElementById('digitador-name').focus();toast('Ingresa tu nombre para comenzar');return}
  typingRes=null;
  showScr('s-typing');
  prepareTypingTest();
}

function prepareTypingTest(){
  typingText=TYPING_TEXTS[Math.floor(Math.random()*TYPING_TEXTS.length)];
  document.getElementById('typing-text').textContent=typingText;
  const inp=document.getElementById('typing-input');
  inp.value='';inp.disabled=true;
  document.getElementById('typing-accuracy').textContent='Precisión: 100%';
  document.getElementById('btn-typing-start').disabled=false;
  document.getElementById('badge-ficha').textContent='Mecanografía';
  document.getElementById('badge-ficha').classList.add('visible');
}

function startTypingTest(){
  document.getElementById('btn-typing-start').disabled=true;
  const inp=document.getElementById('typing-input');
  inp.disabled=false;inp.focus();
  typingSecsLeft=TYPING_TIME;typingStart=Date.now();
  document.getElementById('header-timer').classList.add('visible');
  updateTypingTimer();
  clearInterval(typingTimerInt);
  typingTimerInt=setInterval(()=>{typingSecsLeft--;updateTypingTimer();if(typingSecsLeft<=0){clearInterval(typingTimerInt);finishTypingTest(true)}},1000);
  inp.addEventListener('input',onTypingInput);
}

function updateTypingTimer(){
  const m=Math.floor(typingSecsLeft/60),s=typingSecsLeft%60;
  const el=document.getElementById('timer-display');
  el.textContent=`${String(m).padStart(2,'0')}:${String(s).padStart(2,'0')}`;
  el.className='timer-val'+(typingSecsLeft<10?' urgent':'');
}

function onTypingInput(){
  const inp=document.getElementById('typing-input');
  const typed=inp.value;
  let correct=0;
  for(let i=0;i<typed.length;i++){if(typed[i]===typingText[i])correct++}
  const pct=typingText.length>0?Math.round((correct/typingText.length)*100):0;
  document.getElementById('typing-accuracy').textContent=`Precisión: ${pct}%`;
  if(typed.length>=typingText.length)finishTypingTest(false);
}

function finishTypingTest(timeout){
  clearInterval(typingTimerInt);
  const inp=document.getElementById('typing-input');
  inp.removeEventListener('input',onTypingInput);
  inp.disabled=true;
  const elapsed=Math.round((Date.now()-typingStart)/1000);
  const typed=inp.value;
  let correct=0;
  for(let i=0;i<typed.length;i++){if(typed[i]===typingText[i])correct++}
  const pct=typingText.length>0?Math.round((correct/typingText.length)*100):0;
  typingRes={pct,correct,total:typingText.length,elapsed,timeout};
  toast(`Mecanografía guardada — ${pct}%`);
  setTimeout(startDigitExam,1400);
}

function startDigitExam(){
  curEx=0;examRes=[];totalElap=0;
  showScr('s-exam');
  document.getElementById('header-timer').classList.add('visible');
  renderExam();
}

function selEl(id,opts,val){
  let s=`<select id="${id}">`;
  opts.forEach(o=>s+=`<option value="${o}"${o===val?' selected':''}>${o||'Seleccionar'}</option>`);
  return s+'</select>';
}

function renderExam(){
  const ex=EXAMS[curEx];
  document.getElementById('badge-ficha').textContent=`Ficha ${curEx+1} / ${EXAMS.length}`;
  document.getElementById('badge-ficha').classList.add('visible');
  const steps=document.getElementById('progress-steps');
  steps.innerHTML='';
  EXAMS.forEach((_,i)=>{const d=document.createElement('div');d.className='step'+(i<curEx?' done':i===curEx?' active':'');steps.appendChild(d)});
  document.getElementById('progress-label').textContent=`${ex.titulo} — ${ex.subtitulo}`;

  const f=document.getElementById('exam-form');
  f.innerHTML='';

  // --- Sección 1: Institución ---
  let h1=`<div class="section-card"><div class="section-head"><div class="section-num">1</div><div class="section-title">Datos de la institución y/o establecimiento</div></div>`;
  const enc=ex.enc;
  h1+=`<div class="fg fg2">
    <div class="field-group"><label>${enc.institucion.l}</label><input type="text" id="f_inst" value=""></div>
    <div class="field-group"><label>${enc.lugar.l}</label><input type="text" id="f_lugar" value=""></div>
  </div>
  <div class="fg fg3">
    <div class="field-group"><label>${enc.zona.l}</label><input type="text" id="f_zona" value=""></div>
    <div class="field-group"><label>${enc.localidad.l}</label><input type="text" id="f_localidad" value=""></div>
    <div class="field-group"><label>${enc.upz.l}</label><input type="text" id="f_upz" value=""></div>
  </div>
  <div class="fg fg2">
    <div class="field-group"><label>${enc.manzana.l}</label>${selEl('f_manzana',MC_OPTS,'')}</div>
    <div class="field-group"><label>${enc.numero_mc.l}</label><input type="text" id="f_numero_mc" value=""></div>
  </div></div>`;
  f.insertAdjacentHTML('beforeend',h1);

  // --- Sección 2: Contenidos ---
  const con=ex.con;
  let h2=`<div class="section-card"><div class="section-head"><div class="section-num">2</div><div class="section-title">Contenidos desarrollados</div></div>
  <div class="fg fg2">
    <div class="field-group"><label>${con.modalidad.l}</label>${selEl('f_modalidad',MOD,'')}</div>
    <div class="field-group"><label>${con.linea.l}</label><input type="text" id="f_linea" value=""></div>
  </div>
  <div class="fg fg4">
    <div class="field-group"><label>${con.fecha_dd.l}</label><input type="text" id="f_fecha_dd" maxlength="2" placeholder="DD"></div>
    <div class="field-group"><label>${con.fecha_mm.l}</label><input type="text" id="f_fecha_mm" maxlength="2" placeholder="MM"></div>
    <div class="field-group"><label>${con.fecha_aaaa.l}</label><input type="text" id="f_fecha_aaaa" maxlength="4" placeholder="AAAA"></div>
    <div class="field-group"><label>${con.num_sesion.l}</label><input type="text" id="f_num_sesion" maxlength="3" placeholder="#"></div>
  </div>
  <div class="fg fg2">
    <div class="field-group"><label>${con.componente.l}</label><input type="text" id="f_componente" value=""></div>
    <div class="field-group"><label>${con.proceso.l}</label><input type="text" id="f_proceso" value=""></div>
  </div>
  <div class="fg fg1">
    <div class="field-group"><label>${con.tema.l}</label><input type="text" id="f_tema" value=""></div>
  </div>
  <div class="fg fg1">
    <div class="field-group"><label>${con.docente.l}</label><input type="text" id="f_docente" value=""></div>
  </div></div>`;
  f.insertAdjacentHTML('beforeend',h2);

  // --- Sección 3: Participantes (tabla) ---
  let h3=`<div class="section-card"><div class="section-head"><div class="section-num">3</div><div class="section-title">Identificación de las personas</div></div>
  <div class="table-wrap"><table class="ptable">
  <thead><tr>
    <th>#</th><th>Nombres</th><th>Apellidos</th><th>Tipo doc.</th><th>Número documento</th>
    <th>F. Nac. DD</th><th>F. Nac. MM</th><th>F. Nac. AAAA</th>
    <th>Edad</th><th>Nacionalidad</th><th>Población prioritaria</th>
  </tr></thead><tbody>`;

  ex.part.forEach((p,i)=>{
    const tipSel=TIPOS.map(t=>`<option value="${t}"${t===''?'':''} >${t||'Tipo'}</option>`).join('');
    const nacSel=NAC.map(n=>`<option value="${n}">${n}</option>`).join('');
    const pobSel=POPS.map(po=>`<option value="${po}">${po}</option>`).join('');
    h3+=`<tr>
      <td class="rn">${i+1}</td>
      <td><input type="text" id="p_nom_${i}" placeholder="Nombres"></td>
      <td><input type="text" id="p_ape_${i}" placeholder="Apellidos"></td>
      <td><select id="p_tip_${i}"><option value="">Tipo</option>${tipSel}</select></td>
      <td><input type="text" id="p_doc_${i}" placeholder="Número"></td>
      <td><input type="text" id="p_fdd_${i}" maxlength="2" placeholder="DD"></td>
      <td><input type="text" id="p_fmm_${i}" maxlength="2" placeholder="MM"></td>
      <td><input type="text" id="p_faa_${i}" maxlength="4" placeholder="AAAA"></td>
      <td><input type="text" id="p_edad_${i}" maxlength="3" placeholder="##"></td>
      <td><select id="p_nac_${i}"><option value="">Selec.</option>${nacSel}</select></td>
      <td><select id="p_pob_${i}"><option value="">Selec.</option>${pobSel}</select></td>
    </tr>`;
  });
  h3+=`</tbody></table></div></div>`;
  f.insertAdjacentHTML('beforeend',h3);

  startTimer();
  document.getElementById('btn-submit').disabled=false;
  window.scrollTo(0,0);
}


function startTimer(){
  clearInterval(timerInt);secsLeft=timer;examStart=Date.now();updateTimer();
  
  timerInt=setInterval(()=>{secsLeft--;updateTimer();if(secsLeft<=0){clearInterval(timerInt);submitCurrent(true)}},1000);
}
function updateTimer(){
  const m=Math.floor(secsLeft/60),s=secsLeft%60;
  const el=document.getElementById('timer-display');
  el.textContent=`${String(m).padStart(2,'0')}:${String(s).padStart(2,'0')}`;
  el.className='timer-val'+(secsLeft<60?' urgent':'');
}

function submitCurrent(timeout=false){
  clearInterval(timerInt);
  document.getElementById('btn-submit').disabled=true;
  const elapsed=Math.round((Date.now()-examStart)/1000);
  totalElap+=elapsed;
  const ex=EXAMS[curEx];
  let correct=0,wrong=0,fieldResults=[];

  // campos encabezado
  const encMap=[
    ['f_inst',ex.enc.institucion],['f_lugar',ex.enc.lugar],['f_zona',ex.enc.zona],
    ['f_localidad',ex.enc.localidad],['f_upz',ex.enc.upz],
    ['f_manzana',ex.enc.manzana],['f_numero_mc',ex.enc.numero_mc]
  ];
  const conMap=[
    ['f_modalidad',ex.con.modalidad],['f_linea',ex.con.linea],
    ['f_fecha_dd',ex.con.fecha_dd],['f_fecha_mm',ex.con.fecha_mm],
    ['f_fecha_aaaa',ex.con.fecha_aaaa],['f_num_sesion',ex.con.num_sesion],
    ['f_componente',ex.con.componente],['f_proceso',ex.con.proceso],
    ['f_tema',ex.con.tema],['f_docente',ex.con.docente]
  ];

  [...encMap,...conMap].forEach(([id,fd])=>{
    const el=document.getElementById(id);if(!el)return;
    const uv=(el.value||'').trim(),cv=String(fd.v||'').trim();
    const ok=uv.toLowerCase()===cv.toLowerCase();
    if(ok)correct++;else wrong++;
    fieldResults.push({field:fd.l,ok,user:uv,expected:cv});
    el.classList.add(ok?'ok':'err');
  });

  // campos participantes
  ex.part.forEach((p,i)=>{
    const partFields=[
      [`p_nom_${i}`,'Nombres P.'+(i+1),p.nom],
      [`p_ape_${i}`,'Apellidos P.'+(i+1),p.ape],
      [`p_tip_${i}`,'Tipo doc. P.'+(i+1),p.tip],
      [`p_doc_${i}`,'Número doc. P.'+(i+1),p.doc],
      [`p_fdd_${i}`,'Fecha nac. DD P.'+(i+1),p.fn_dd],
      [`p_fmm_${i}`,'Fecha nac. MM P.'+(i+1),p.fn_mm],
      [`p_faa_${i}`,'Fecha nac. AAAA P.'+(i+1),p.fn_aaaa],
      [`p_edad_${i}`,'Edad P.'+(i+1),p.edad],
      [`p_nac_${i}`,'Nacionalidad P.'+(i+1),p.nac],
      [`p_pob_${i}`,'Población P.'+(i+1),p.pob],
    ];
    partFields.forEach(([id,lbl,val])=>{
      const el=document.getElementById(id);if(!el)return;
      const uv=(el.value||'').trim(),cv=String(val||'').trim();
      const ok=uv.toLowerCase()===cv.toLowerCase();
      if(ok)correct++;else wrong++;
      fieldResults.push({field:lbl,ok,user:uv,expected:cv});
      el.classList.add(ok?'ok':'err');
    });
  });

  const total=correct+wrong;
  const pct=total>0?Math.round((correct/total)*100):0;
  examRes.push({titulo:ex.titulo,correct,wrong,total,pct,fieldResults,timeout,elapsed});

  if(curEx<EXAMS.length-1){
    toast(`Ficha ${curEx+1} guardada — ${pct}%`);
    setTimeout(()=>{curEx++;renderExam()},1400);
  } else {
    setTimeout(showResults,1400);
  }
}

function showResults(){
  showScr('s-results');
  document.getElementById('header-timer').classList.remove('visible');
  document.getElementById('badge-ficha').classList.remove('visible');
  let tc=0,tw=0;
  examRes.forEach(r=>{tc+=r.correct;tw+=r.wrong});
  const total=tc+tw,score=total>0?Math.round((tc/total)*100):0;
  const name=document.getElementById('digitador-name').value;

  const se=document.getElementById('res-score-big');
  se.textContent=score;
  se.style.color=score>=75?'var(--green-m)':score>=60?'var(--amber-m)':'var(--red-m)';
  document.getElementById('res-name').textContent=name;
  const v=document.getElementById('res-verdict');
  if(score>=90){v.textContent='Excelente desempeño';v.style.cssText='background:var(--green-l);color:var(--green)'}
  else if(score>=75){v.textContent='Buen desempeño';v.style.cssText='background:var(--green-l);color:var(--green)'}
  else if(score>=60){v.textContent='Desempeño aceptable';v.style.cssText='background:var(--amber-l);color:var(--amber)'}
  else{v.textContent='Necesita refuerzo';v.style.cssText='background:var(--red-l);color:var(--red)'}

  document.getElementById('res-correct').textContent=tc;
  document.getElementById('res-wrong').textContent=tw;
  const mins=Math.floor(totalElap/60),secs=totalElap%60;
  document.getElementById('res-time').textContent=`${mins}m ${secs}s`;

  const det=document.getElementById('res-fichas');det.innerHTML='';
  if(typingRes){
    const tcolor=typingRes.pct>=75?'var(--green-m)':typingRes.pct>=60?'var(--amber-m)':'var(--red-m)';
    const tbl=document.createElement('div');tbl.className='ficha-result';
    tbl.innerHTML=`<div class="ficha-head"><div class="ficha-title">Mecanografía</div><div class="ficha-pct" style="color:${tcolor}">${typingRes.pct}%</div></div>
    <div class="ficha-bar-wrap"><div class="ficha-bar" style="width:${typingRes.pct}%;background:${tcolor}"></div></div>
    <div class="ficha-meta">${typingRes.correct}/${typingRes.total} caracteres correctos · ${Math.floor(typingRes.elapsed/60)}m ${typingRes.elapsed%60}s${typingRes.timeout?' · Tiempo agotado':''}</div>`;
    det.appendChild(tbl);
  }
  examRes.forEach(r=>{
    const color=r.pct>=75?'var(--green-m)':r.pct>=60?'var(--amber-m)':'var(--red-m)';
    const bl=document.createElement('div');bl.className='ficha-result';
    bl.innerHTML=`<div class="ficha-head"><div class="ficha-title">${r.titulo}</div><div class="ficha-pct" style="color:${color}">${r.pct}%</div></div>
    <div class="ficha-bar-wrap"><div class="ficha-bar" style="width:${r.pct}%;background:${color}"></div></div>
    <div class="ficha-meta">${r.correct} correctos · ${r.wrong} errores · ${Math.floor(r.elapsed/60)}m ${r.elapsed%60}s${r.timeout?' · Tiempo agotado':''}</div>`;
    const errs=r.fieldResults.filter(x=>!x.ok);
    if(errs.length){
      const el2=document.createElement('div');el2.className='error-list';
      errs.forEach(e=>{
        const it=document.createElement('div');it.className='error-item';
        it.innerHTML=`<span class="err-field">${e.field}</span><div class="err-vals"><div class="err-user">✗ "${e.user||'(vacío)'}"</div><div class="err-correct">✓ "${e.expected||'(vacío)'}"</div></div>`;
        el2.appendChild(it);
      });
      bl.appendChild(el2);
    }
    det.appendChild(bl);
  });
  window.scrollTo(0,0);

  enviarResultadosPorCorreo(name, score, totalElap);
}

function enviarResultadosPorCorreo(name, score, totalSecs){
  const fichas = examRes.map(r=>({
    titulo:r.titulo,
    pct:r.pct,
    correct:r.correct,
    wrong:r.wrong,
    timeout:r.timeout,
    elapsed:r.elapsed,
    errores:r.fieldResults.filter(x=>!x.ok).map(e=>({field:e.field,user:e.user,expected:e.expected}))
  }));

  fetch('{{ route('prueba.enviar_resultados') }}',{
    method:'POST',
    headers:{
      'Content-Type':'application/json',
      'X-CSRF-TOKEN':'{{ csrf_token() }}'
    },
    body:JSON.stringify({digitador:name,score,total_secs:totalSecs,fichas,mecanografia:typingRes})
  })
  .then(r=>r.ok?toast('Resultados enviados por correo'):toast('No se pudo enviar el correo'))
  .catch(()=>toast('No se pudo enviar el correo'));
}

function restartExam(){showScr('s-intro');document.getElementById('digitador-name').value='';typingRes=null}
</script>
</body>
</html>