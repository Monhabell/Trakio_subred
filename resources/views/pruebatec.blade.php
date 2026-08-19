<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Prueba Técnico de Sistemas</title>
<style>
:root{
  --blue:#185FA5;--blue-l:#E6F1FB;--blue-m:#378ADD;
  --green:#0F6E56;--green-l:#E1F5EE;--green-m:#1D9E75;
  --red:#A32D2D;--red-l:#FCEBEB;--red-m:#E24B4A;
  --amber:#854F0B;--amber-l:#FAEEDA;--amber-m:#EF9F27;
  --purple:#4A1D96;--purple-l:#EDE9FE;--purple-m:#7C3AED;
  --teal:#0E5F6E;--teal-l:#CCFBF1;--teal-m:#0D9488;
  --g50:#F8F8F7;--g100:#F1EFE8;--g200:#D3D1C7;--g400:#888780;--g600:#5F5E5A;--g900:#2C2C2A;
  --r:8px;--rl:12px;
}
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',system-ui,sans-serif;background:var(--g50);color:var(--g900);font-size:14px;min-height:100vh}
.screen{display:none}.screen.active{display:block}

.hdr{background:#fff;border-bottom:1px solid var(--g200);padding:0 1.5rem;height:56px;display:flex;align-items:center;justify-content:space-between;position:sticky;top:0;z-index:100}
.logo{display:flex;align-items:center;gap:10px}
.logo-icon{width:32px;height:32px;background:var(--blue);border-radius:8px;display:flex;align-items:center;justify-content:center}
.logo-icon svg{width:18px;height:18px;fill:#fff}
.logo-text{font-size:14px;font-weight:600}.logo-sub{font-size:11px;color:var(--g400)}
.hdr-right{display:flex;align-items:center;gap:12px}
.timer-wrap{display:none;align-items:center;gap:8px;background:var(--g100);border-radius:var(--r);padding:6px 14px}
.timer-wrap.vis{display:flex}
.timer-val{font-size:18px;font-weight:700;font-family:'Courier New',monospace;min-width:56px}
.timer-val.urgent{color:var(--red-m)}
.prog-badge{background:var(--blue-l);color:var(--blue);font-size:12px;font-weight:600;padding:4px 10px;border-radius:20px;display:none}
.prog-badge.vis{display:inline}

.intro-wrap{max-width:640px;margin:0 auto;padding:3rem 1rem}
.eyebrow{font-size:11px;font-weight:600;letter-spacing:.08em;color:var(--blue);text-transform:uppercase;margin-bottom:.75rem}
.intro-title{font-size:28px;font-weight:700;margin-bottom:.5rem;line-height:1.2}
.intro-sub{font-size:15px;color:var(--g600);margin-bottom:2rem}
.modulos-grid{display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:2rem}
.mod-card{background:#fff;border:1px solid var(--g200);border-radius:var(--rl);padding:.9rem 1rem;display:flex;align-items:center;gap:10px}
.mod-icon{width:36px;height:36px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0}
.mod-title{font-size:13px;font-weight:600;margin-bottom:2px}
.mod-qs{font-size:11px;color:var(--g600)}
.name-field{margin-bottom:1.25rem}
.name-field label{display:block;font-size:13px;font-weight:500;margin-bottom:5px}
.name-field input{width:100%;padding:10px 14px;font-size:14px;border:1.5px solid var(--g200);border-radius:var(--r);background:#fff;color:var(--g900)}
.name-field input:focus{outline:none;border-color:var(--blue-m)}
.btn-start{width:100%;padding:12px;font-size:15px;font-weight:600;background:var(--blue);color:#fff;border:none;border-radius:var(--r);cursor:pointer}
.btn-start:hover{background:#0c4480}

.exam-wrap{max-width:780px;margin:0 auto;padding:1.5rem 1rem}
.prog-bar-wrap{margin-bottom:1.25rem}
.prog-steps{display:flex;gap:5px;margin-bottom:4px}
.pstep{flex:1;height:4px;border-radius:2px;background:var(--g200)}
.pstep.done{background:var(--green-m)}.pstep.active{background:var(--blue-m)}
.prog-lbl{font-size:12px;color:var(--g600)}
.mod-header{border-radius:var(--rl);padding:1rem 1.25rem;margin-bottom:1rem;display:flex;align-items:center;gap:12px}
.mh-icon{font-size:24px}.mh-title{font-size:17px;font-weight:700}.mh-sub{font-size:12px;opacity:.85;margin-top:2px}

.q-card{background:#fff;border:1px solid var(--g200);border-radius:var(--rl);padding:1.25rem;margin-bottom:.75rem}
.q-num{font-size:11px;font-weight:600;color:var(--g400);text-transform:uppercase;letter-spacing:.05em;margin-bottom:.4rem}
.q-text{font-size:14px;font-weight:500;line-height:1.6;margin-bottom:.9rem}
.q-hint{font-size:12px;color:var(--g600);background:var(--g100);border-radius:6px;padding:6px 10px;margin-bottom:.75rem;font-style:italic}
.q-code{font-family:'Courier New',monospace;background:var(--g100);border:1px solid var(--g200);border-radius:6px;padding:10px 14px;font-size:13px;margin-bottom:.75rem;color:var(--g900);line-height:1.7;white-space:pre-wrap}
.q-img-desc{font-size:12px;color:var(--g600);background:#FFFBEB;border:1px solid #FDE68A;border-radius:6px;padding:8px 12px;margin-bottom:.75rem}

.opts{display:flex;flex-direction:column;gap:7px}
.opt{display:flex;align-items:flex-start;gap:10px;padding:9px 12px;border:1.5px solid var(--g200);border-radius:var(--r);cursor:pointer;transition:border-color .15s,background .15s;user-select:none}
.opt:hover{border-color:var(--blue-m);background:var(--blue-l)}
.opt.selected{border-color:var(--blue-m);background:var(--blue-l)}
.opt.correct{border-color:var(--green-m)!important;background:var(--green-l)!important}
.opt.wrong{border-color:var(--red-m)!important;background:var(--red-l)!important}
.opt-letter{width:22px;height:22px;border-radius:50%;background:var(--g100);color:var(--g600);font-size:12px;font-weight:700;display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:1px}
.opt.selected .opt-letter{background:var(--blue-m);color:#fff}
.opt.correct .opt-letter{background:var(--green-m);color:#fff}
.opt.wrong .opt-letter{background:var(--red-m);color:#fff}
.opt-text{font-size:13px;line-height:1.45}

.formula-wrap{display:flex;flex-direction:column;gap:6px}
.formula-wrap label{font-size:12px;color:var(--g600);font-weight:500}
.formula-input{width:100%;padding:9px 12px;font-size:14px;font-family:'Courier New',monospace;border:1.5px solid var(--g200);border-radius:var(--r);background:#fff;color:var(--g900)}
.formula-input:focus{outline:none;border-color:var(--blue-m)}

.btn-nav{display:flex;gap:10px;margin-top:1rem}
.btn-prev{flex:1;padding:10px;font-size:13px;font-weight:500;background:#fff;color:var(--g900);border:1.5px solid var(--g200);border-radius:var(--r);cursor:pointer}
.btn-next{flex:2;padding:10px;font-size:14px;font-weight:600;background:var(--blue);color:#fff;border:none;border-radius:var(--r);cursor:pointer}
.btn-next:hover{background:#0c4480}
.btn-finish{flex:2;padding:10px;font-size:14px;font-weight:600;background:var(--green-m);color:#fff;border:none;border-radius:var(--r);cursor:pointer}

.res-wrap{max-width:700px;margin:0 auto;padding:2rem 1rem}
.score-hero{text-align:center;background:#fff;border:1px solid var(--g200);border-radius:var(--rl);padding:2.5rem 1rem;margin-bottom:1.5rem}
.score-big{font-size:72px;font-weight:800;line-height:1;margin-bottom:.25rem}
.score-verdict{display:inline-block;font-size:13px;font-weight:600;padding:4px 14px;border-radius:20px;margin-top:.5rem}
.stats-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:10px;margin-bottom:1.5rem}
.stat-box{background:#fff;border:1px solid var(--g200);border-radius:var(--rl);padding:1rem;text-align:center}
.stat-num{font-size:30px;font-weight:700;display:block}
.stat-lbl{font-size:12px;color:var(--g600)}
.mod-result{background:#fff;border:1px solid var(--g200);border-radius:var(--rl);margin-bottom:.75rem;overflow:hidden}
.mr-head{padding:.9rem 1.25rem;display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid var(--g100)}
.mr-title{font-size:14px;font-weight:600}
.mr-pct{font-size:18px;font-weight:700}
.mr-bar-bg{height:4px;background:var(--g100)}
.mr-bar{height:100%;transition:width .8s}
.mr-meta{padding:.6rem 1.25rem;font-size:12px;color:var(--g600);border-bottom:1px solid var(--g100)}
.err-list{padding:.5rem 1.25rem .75rem}
.err-item{display:flex;gap:8px;padding:5px 0;border-bottom:1px solid var(--g100);font-size:12px}
.err-item:last-child{border-bottom:none}
.err-q{color:var(--g600);flex:1}
.err-ans{text-align:right;flex-shrink:0}
.err-user{color:var(--red-m)}.err-correct{color:var(--green-m)}
.btn-retry{width:100%;padding:11px;font-size:14px;font-weight:500;background:#fff;color:var(--g900);border:1.5px solid var(--g200);border-radius:var(--r);cursor:pointer;margin-top:.5rem}
.btn-retry:hover{background:var(--g100)}
.toast{position:fixed;bottom:24px;left:50%;transform:translateX(-50%);background:var(--g900);color:#fff;font-size:13px;padding:10px 20px;border-radius:var(--r);opacity:0;transition:opacity .3s;z-index:999;pointer-events:none}
.toast.show{opacity:1}
.warn-badge{display:inline-block;background:#FEF3C7;color:#92400E;font-size:11px;font-weight:600;padding:2px 8px;border-radius:4px;margin-bottom:.5rem}

@media(max-width:600px){.modulos-grid{grid-template-columns:1fr}.stats-grid{grid-template-columns:1fr 1fr}}
</style>
</head>
<body>

<header class="hdr">
  <div class="logo">
    <div class="logo-icon"><svg viewBox="0 0 24 24"><path d="M9 3H5a2 2 0 00-2 2v4m6-6h10a2 2 0 012 2v4M9 3v18m0 0h10a2 2 0 002-2V9M9 21H5a2 2 0 01-2-2V9m0 0h18"/></svg></div>
    <div><div class="logo-text">Prueba Técnico de Sistemas</div><div class="logo-sub">Perfil: soporte de datos y ofimática</div></div>
  </div>
  <div class="hdr-right">
    <span class="prog-badge" id="prog-badge"></span>
    <div class="timer-wrap" id="timer-wrap">⏱ <span class="timer-val" id="timer-val">60:00</span></div>
  </div>
</header>

<div id="s-intro" class="screen active">
<div class="intro-wrap">
  <div class="eyebrow">Evaluación técnica · Oficina y datos</div>
  <h1 class="intro-title">Prueba Técnico<br>de Sistemas</h1>
  <p class="intro-sub">50 preguntas en 6 módulos. Enfoque en Excel, manejo de datos, calidad y ofimática. Tiempo: 60 minutos.</p>
  <div class="modulos-grid">
    <div class="mod-card"><div class="mod-icon" style="background:#EFF6FF">📊</div><div><div class="mod-title">Excel Avanzado</div><div class="mod-qs">15 preguntas · peso 30%</div></div></div>
    <div class="mod-card"><div class="mod-icon" style="background:#ECFDF5">🔍</div><div><div class="mod-title">Calidad y validación de datos</div><div class="mod-qs">10 preguntas · peso 20%</div></div></div>
    <div class="mod-card"><div class="mod-icon" style="background:#FAF5FF">🪟</div><div><div class="mod-title">Windows y ofimática</div><div class="mod-qs">10 preguntas · peso 20%</div></div></div>
    <div class="mod-card"><div class="mod-icon" style="background:#FFF1F2">🎧</div><div><div class="mod-title">Soporte a usuarios</div><div class="mod-qs">8 preguntas · peso 16%</div></div></div>
    <div class="mod-card"><div class="mod-icon" style="background:#F0FDF4">🖥️</div><div><div class="mod-title">Hardware básico</div><div class="mod-qs">5 preguntas · peso 10%</div></div></div>
    <div class="mod-card"><div class="mod-icon" style="background:#FFFBEB">🔧</div><div><div class="mod-title">Mantenimiento general</div><div class="mod-qs">2 preguntas · peso 4%</div></div></div>
  </div>
  <div class="name-field">
    <label for="cand-name">Nombre completo del candidato</label>
    <input type="text" id="cand-name" placeholder="Ej. Carlos Andrés Gómez Ruiz" autocomplete="name">
  </div>
  <button class="btn-start" onclick="startExam()">▶ Iniciar prueba</button>
</div>
</div>

<div id="s-exam" class="screen">
<div class="exam-wrap">
  <div class="prog-bar-wrap">
    <div class="prog-steps" id="prog-steps"></div>
    <div class="prog-lbl" id="prog-lbl"></div>
  </div>
  <div class="mod-header" id="mod-header"></div>
  <div id="questions-container"></div>
  <div class="btn-nav">
    <button class="btn-prev" id="btn-prev" onclick="prevMod()">← Anterior</button>
    <button class="btn-next" id="btn-next" onclick="nextMod()">Siguiente módulo →</button>
    <button class="btn-finish" id="btn-finish" style="display:none" onclick="finishExam()">✓ Finalizar prueba</button>
  </div>
</div>
</div>

<div id="s-results" class="screen">
<div class="res-wrap">
  <div class="score-hero">
    <div class="score-big" id="res-big">0</div>
    <div style="font-size:13px;color:var(--g600);margin-bottom:.75rem">puntos sobre 100</div>
    <div style="font-size:16px;font-weight:600" id="res-name"></div>
    <div class="score-verdict" id="res-verdict"></div>
  </div>
  <div class="stats-grid">
    <div class="stat-box"><span class="stat-num" id="res-correct" style="color:var(--green-m)">0</span><span class="stat-lbl">Correctas</span></div>
    <div class="stat-box"><span class="stat-num" id="res-wrong" style="color:var(--red-m)">0</span><span class="stat-lbl">Incorrectas</span></div>
    <div class="stat-box"><span class="stat-num" id="res-time">—</span><span class="stat-lbl">Tiempo usado</span></div>
  </div>
  <div id="res-modulos"></div>
  <button class="btn-retry" onclick="restartExam()">↺ Reintentar</button>
</div>
</div>

<div class="toast" id="toast"></div>

<script>
const MODULOS = [
  // ══════════════════════════════════════════════
  //  1. EXCEL AVANZADO  (15 preguntas)
  // ══════════════════════════════════════════════
  {
    id:"excel", titulo:"Excel Avanzado", icon:"📊",
    color:"#1A5FA5", colorL:"#E6F1FB",
    preguntas:[
      {tipo:"mc",
       texto:"Tienes una lista de 5.000 pacientes en la hoja 'Base' y necesitas traer el nombre de cada uno a otra hoja usando el número de documento como llave. ¿Qué función usarías?",
       opts:["SUMAR.SI","BUSCARV","CONTAR.SI","PROMEDIO.SI"],correcta:1},

      {tipo:"mc",
       texto:"La siguiente fórmula está en la celda C2: =BUSCARV(A2,Base!$D$1:$G$500,3,FALSO). ¿Qué devuelve?",
       opts:["El valor de la columna D del rango Base","El valor de la tercera columna del rango D:G de la hoja Base, donde A2 coincida exactamente","Un promedio de las 3 primeras columnas","Un error porque FALSO no es válido en BUSCARV"],correcta:1},

      {tipo:"mc",
       texto:"Al copiar una fórmula hacia abajo, la referencia $B$2 …",
       opts:["Cambia la fila pero no la columna","Cambia columna y fila automáticamente","Permanece fija sin cambiar ni fila ni columna","Solo fija la columna B"],correcta:2},

      {tipo:"mc",
       texto:"Tienes fechas en la columna A y necesitas extraer solo el año. ¿Qué función usas?",
       opts:["MES(A1)","DIA(A1)","AÑO(A1)","FECHA(A1)"],correcta:2},

      {tipo:"mc",
       texto:"¿Para qué sirve la función CONCATENAR (o el operador &) en Excel?",
       opts:["Sumar valores numéricos de varias celdas","Unir textos de varias celdas en una sola","Contar celdas con texto","Buscar texto dentro de una celda"],correcta:1},

      {tipo:"mc",
       texto:"Quieres resaltar automáticamente en rojo las celdas de la columna C donde el valor sea mayor a 100. ¿Qué herramienta usas?",
       opts:["Ordenar y filtrar","Validación de datos","Formato condicional","Tabla dinámica"],correcta:2},

      {tipo:"mc",
       texto:"¿Cuál es la diferencia entre CONTAR y CONTARA en Excel?",
       opts:["No hay diferencia","CONTAR cuenta celdas con números; CONTARA cuenta celdas no vacías (texto o número)","CONTARA solo cuenta texto","CONTAR también cuenta celdas vacías"],correcta:1},

      {tipo:"mc",
       texto:"Una tabla dinámica permite:",
       opts:["Solo ordenar datos alfabéticamente","Resumir, agrupar y analizar grandes volúmenes de datos de forma interactiva","Crear gráficos automáticamente sin datos","Eliminar duplicados en una columna"],correcta:1},

      {tipo:"mc",
       texto:"¿Cómo eliminas filas duplicadas en Excel de forma nativa (sin fórmulas)?",
       opts:["Filtro avanzado → Copiar a otro lugar","Datos → Quitar duplicados","Inicio → Buscar y seleccionar","Ctrl+H"],correcta:1},

      {tipo:"mc",
       texto:"Necesitas saber cuántos registros de la columna B tienen el texto 'Activo' Y además en la columna C el valor sea mayor a 50. ¿Qué función usas?",
       opts:["CONTAR.SI","SUMAR.SI","CONTAR.SI.CONJUNTO","SUMAR.SI.CONJUNTO"],correcta:2},

      {tipo:"mc",
       texto:"¿Qué hace la función SI.ERROR(BUSCARV(A2,Tabla,2,FALSO),\"No encontrado\")?",
       opts:["Genera un error si no encuentra A2","Devuelve el resultado del BUSCARV; si hay error, muestra 'No encontrado'","Siempre devuelve 'No encontrado'","Busca el texto 'No encontrado' en la tabla"],correcta:1},

      {tipo:"formula",
       texto:"Escribe la fórmula para buscar el valor de A2 en el rango $A$1:$C$500 de la hoja 'Datos', trayendo la columna 3, con coincidencia exacta.",
       hint:"BUSCARV con referencia a otra hoja: NombreHoja!Rango",
       correctas:["=BUSCARV(A2,Datos!$A$1:$C$500,3,FALSO)","=buscarv(a2,datos!$a$1:$c$500,3,falso)","=BUSCARV(A2,Datos!$A$1:$C$500,3,0)"]},

      {tipo:"formula",
       texto:"Tienes números de documento en la columna A (hoja activa) y una base en la hoja 'Usuarios' con documento en columna A y nombre completo en columna B. Escribe la fórmula para traer el nombre a la celda B2.",
       hint:"BUSCARV buscando A2 en Usuarios!$A:$B, columna 2, coincidencia exacta",
       correctas:["=BUSCARV(A2,Usuarios!$A:$B,2,FALSO)","=buscarv(a2,usuarios!$a:$b,2,falso)","=BUSCARV(A2,Usuarios!$A:$B,2,0)"]},

      {tipo:"formula",
       texto:"Escribe la fórmula para contar cuántos registros en el rango D2:D1000 tienen el valor 'Bogotá' en la columna D Y el valor 'Activo' en el rango E2:E1000.",
       hint:"Usa CONTAR.SI.CONJUNTO con dos rangos y dos criterios",
       correctas:['=CONTAR.SI.CONJUNTO(D2:D1000,"Bogotá",E2:E1000,"Activo")','=contar.si.conjunto(D2:D1000,"Bogotá",E2:E1000,"Activo")','=CONTAR.SI.CONJUNTO(D2:D1000,"bogotá",E2:E1000,"activo")']},

      {tipo:"formula",
       texto:"La celda A1 tiene la fecha 15/03/2025. Escribe la fórmula para mostrar solo el año (2025) en otra celda.",
       hint:"Función de fecha que extrae el año",
       correctas:["=AÑO(A1)","=año(a1)","=YEAR(A1)","=year(a1)"]},
    ]
  },

  // ══════════════════════════════════════════════
  //  2. CALIDAD Y VALIDACIÓN DE DATOS (10 preguntas)
  // ══════════════════════════════════════════════
  {
    id:"calidad", titulo:"Calidad y validación de datos", icon:"🔍",
    color:"#0E5F6E", colorL:"#CCFBF1",
    preguntas:[
      {tipo:"mc",
       texto:"Al revisar una base de datos de pacientes encuentras que el campo 'Número de documento' tiene entradas como '8.345.123' (con puntos) y '8345123' (sin puntos). ¿Cuál es el problema?",
       opts:["No hay problema, son el mismo dato","Inconsistencia de formato que impide cruzar la base correctamente","El número está incorrecto","El campo debe ser texto, no número"],correcta:1},

      {tipo:"mc",
       texto:"¿Cuál función de Excel te ayuda a detectar si una celda está vacía?",
       opts:["ESVACIO()","ESBLANCO()","ESNULO()","VACÍO()"],correcta:0},

      {tipo:"mc",
       texto:"Tienes una columna de fechas y notas que algunas celdas tienen el texto '15-03-2025' en vez del formato de fecha real de Excel. ¿Cuál es el riesgo?",
       opts:["Ninguno, Excel los trata igual","No se pueden hacer cálculos de diferencia de días ni ordenar correctamente","El archivo se daña","Excel los convierte automáticamente sin error"],correcta:1},

      {tipo:"mc",
       texto:"¿Qué herramienta de Excel permite restringir que en una celda solo se pueda ingresar un número entre 1 y 150 (para edad)?",
       opts:["Formato condicional","Validación de datos","Proteger hoja","Filtro automático"],correcta:1},

      {tipo:"mc",
       texto:"Al hacer un BUSCARV para cruzar dos bases y obtienes #N/A en muchas filas, la causa más probable es:",
       opts:["La fórmula está mal escrita","El valor buscado no existe en la tabla de referencia o hay diferencias de formato/espacios","La columna de resultado está vacía","El rango es demasiado grande"],correcta:1},

      {tipo:"mc",
       texto:"¿Cómo identificas rápidamente celdas duplicadas en una columna de Excel sin usar fórmulas?",
       opts:["Ctrl+F → buscar duplicados","Inicio → Formato condicional → Reglas para resaltar celdas → Valores duplicados","Datos → Filtro → Duplicados","No es posible sin fórmulas"],correcta:1},

      {tipo:"mc",
       texto:"Un reporte tiene la columna 'Localidad' con valores: 'usaquen', 'Usaquén', 'USAQUEN'. Para estandarizar todo a mayúsculas en Excel, ¿qué función usas?",
       opts:["MINUSC()","NOMPROPIO()","MAYUSC()","TEXTO()"],correcta:2},

      {tipo:"mc",
       texto:"Al recibir un archivo Excel con datos de 10.000 filas para validar calidad, ¿cuál sería el primer paso recomendado?",
       opts:["Borrar las filas que parezcan incorrectas de inmediato","Trabajar sobre una copia del archivo original, sin modificar el original","Enviar el archivo sin revisarlo","Convertirlo a PDF primero"],correcta:1},

      {tipo:"mc",
       texto:"La función ESPACIOS() en Excel sirve para:",
       opts:["Agregar espacio entre columnas","Eliminar espacios al inicio, al final y los dobles espacios internos de un texto","Contar los espacios de una celda","Separar texto en columnas"],correcta:1},

      {tipo:"mc",
       texto:"Cuando un número de documento tiene 9 dígitos en una base y 10 en otra, al cruzarlas con BUSCARV el resultado será:",
       opts:["Coincidencia parcial automática","#N/A porque no coinciden exactamente","#VALOR!","El BUSCARV completará el dígito faltante"],correcta:1},
    ]
  },

  // ══════════════════════════════════════════════
  //  3. WINDOWS Y OFIMÁTICA (10 preguntas)
  // ══════════════════════════════════════════════
  {
    id:"windows", titulo:"Windows y ofimática diaria", icon:"🪟",
    color:"#4A1D96", colorL:"#EDE9FE",
    preguntas:[
      {tipo:"mc",
       texto:"¿Qué atajo de teclado se usa para copiar, pegar y cortar en Windows respectivamente?",
       opts:["Ctrl+C / Ctrl+V / Ctrl+X","Ctrl+C / Ctrl+P / Ctrl+X","Alt+C / Alt+V / Alt+X","Ctrl+C / Ctrl+B / Ctrl+X"],correcta:0},

      {tipo:"mc",
       texto:"Necesitas buscar un archivo del que solo recuerdas parte del nombre. ¿Cómo lo haces en Windows?",
       opts:["Abrir cada carpeta manualmente","Usar el buscador de Windows (Win+S o la barra de búsqueda del explorador) con parte del nombre","Solo es posible si recuerdas el nombre completo","Usar el símbolo del sistema"],correcta:1},

      {tipo:"mc",
       texto:"¿Cuál es la diferencia entre 'Guardar' (Ctrl+S) y 'Guardar como' en un documento?",
       opts:["No hay diferencia","Guardar actualiza el archivo actual; Guardar como permite elegir nombre, ubicación o formato","Guardar como es más lento","Guardar solo funciona en Word"],correcta:1},

      {tipo:"mc",
       texto:"Al trabajar con varios documentos y ventanas abiertas, ¿qué atajo permite cambiar rápidamente entre aplicaciones abiertas?",
       opts:["Ctrl+Tab","Alt+Tab","Win+Tab","Ctrl+Alt+Tab"],correcta:1},

      {tipo:"mc",
       texto:"¿Qué hace el atajo Ctrl+Z en la mayoría de aplicaciones de Office?",
       opts:["Guardar el archivo","Cerrar la aplicación","Deshacer la última acción","Buscar y reemplazar"],correcta:2},

      {tipo:"mc",
       texto:"Necesitas enviar un archivo Excel de 25 MB por correo pero el límite es 10 MB. ¿Cuál es la solución más adecuada en un entorno de oficina?",
       opts:["Imprimir y escanear el archivo","Subir el archivo a OneDrive/Google Drive y compartir el enlace","Renombrar el archivo con extensión .zip","No es posible enviarlo"],correcta:1},

      {tipo:"mc",
       texto:"En Word, ¿cómo reemplazas todas las ocurrencias de la palabra 'usuario' por 'paciente' en un documento de 50 páginas?",
       opts:["Hacerlo manualmente una por una","Ctrl+H → escribir 'usuario' en Buscar y 'paciente' en Reemplazar → Reemplazar todos","Ctrl+F → escribir paciente","Es imposible en Word"],correcta:1},

      {tipo:"mc",
       texto:"¿Qué combinación de teclas permite hacer una captura de pantalla de una región específica en Windows 10/11?",
       opts:["Impr Pant","Win+Impr Pant","Win+Shift+S","Alt+Impr Pant"],correcta:2},

      {tipo:"mc",
       texto:"¿Para qué sirve la tecla F2 cuando tienes seleccionada una celda en Excel o un archivo en el explorador de Windows?",
       opts:["Abre la ayuda","Cierra la ventana","Permite editar/renombrar directamente","Guarda el archivo"],correcta:2},

      {tipo:"formula",
       texto:"Escribe el atajo de teclado para abrir el cuadro 'Ejecutar' de Windows (donde puedes escribir comandos como 'msconfig' o 'regedit').",
       hint:"Combinación de 2 teclas con la tecla Windows",
       correctas:["Win+R","win+r","Windows+R","⊞+R"]},
    ]
  },

  // ══════════════════════════════════════════════
  //  4. SOPORTE A USUARIOS (8 preguntas)
  // ══════════════════════════════════════════════
  {
    id:"soporte", titulo:"Soporte a usuarios", icon:"🎧",
    color:"#A32D2D", colorL:"#FCEBEB",
    preguntas:[
      {tipo:"mc",
       texto:"Un usuario llama diciendo que su Excel 'se puso lento'. ¿Cuál es la primera pregunta que debes hacer?",
       opts:["¿Tiene el Office actualizado?","¿Cuántas filas tiene el archivo y qué fórmulas usa?","¿Formateamos el equipo?","¿Tiene internet?"],correcta:1},

      {tipo:"mc",
       texto:"Un usuario no puede abrir un archivo .xlsx que le enviaron. ¿Cuál podría ser la causa más común?",
       opts:["El archivo está en otro idioma","No tiene instalado Microsoft Excel o una versión compatible","El computador no tiene internet","El archivo es demasiado pequeño"],correcta:1},

      {tipo:"mc",
       texto:"¿Cuál es la mejor práctica cuando vas a hacer cambios en un archivo de datos que te entregó un usuario?",
       opts:["Trabajar directamente sobre el original","Hacer una copia antes de modificar nada","Borrar los datos que parezcan incorrectos primero","Enviarlo a otro compañero"],correcta:1},

      {tipo:"mc",
       texto:"Un usuario dice que 'se le borró todo' en un documento Word. ¿Cuál es el primer paso?",
       opts:["Decirle que lo vuelva a escribir","Verificar si el archivo tiene versiones anteriores guardadas o si está en la Papelera de reciclaje","Formatear el equipo","Reinstalar Office"],correcta:1},

      {tipo:"mc",
       texto:"¿Qué información mínima debes registrar al atender un caso de soporte?",
       opts:["Solo el nombre del usuario","Nombre del usuario, descripción del problema, fecha, acciones tomadas y solución aplicada","Solo el número de ticket","El modelo del computador únicamente"],correcta:1},

      {tipo:"mc",
       texto:"Un usuario necesita imprimir un informe de Excel en una sola página. ¿Qué opción de Excel usas?",
       opts:["Archivo → Imprimir → Ajustar hoja en una página","Ctrl+P sin más configuración","Reducir el zoom al 10%","No es posible en Excel"],correcta:0},

      {tipo:"mc",
       texto:"¿Cuál es la manera correcta de comunicar a un usuario que su solicitud tomará más tiempo de lo esperado?",
       opts:["No decirle nada y esperar","Informarle proactivamente con una estimación de tiempo y actualización del estado","Cerrar el ticket sin resolver","Transferirlo a otro área sin explicar"],correcta:1},

      {tipo:"mc",
       texto:"Un usuario reporta que su equipo no enciende. ¿Cuál es el primer paso de diagnóstico?",
       opts:["Reinstalar Windows","Verificar que el cable de poder esté conectado y que haya energía en el tomacorriente","Cambiar el disco duro","Pedir un equipo nuevo"],correcta:1},
    ]
  },

  // ══════════════════════════════════════════════
  //  5. HARDWARE BÁSICO (5 preguntas)
  // ══════════════════════════════════════════════
  {
    id:"hardware", titulo:"Hardware básico", icon:"🖥️",
    color:"#0F6E56", colorL:"#D6F0E8",
    preguntas:[
      {tipo:"mc",
       texto:"¿Cuál componente es responsable de ejecutar los programas y procesar las instrucciones en un computador?",
       opts:["RAM","Disco duro","CPU (procesador)","Tarjeta de video"],correcta:2},

      {tipo:"mc",
       texto:"Un equipo de oficina con 4 GB de RAM empieza a ir lento cuando abre varios archivos de Excel al tiempo. La solución más efectiva sería:",
       opts:["Cambiar el disco duro","Ampliar la memoria RAM","Reinstalar Windows","Cambiar el monitor"],correcta:1},

      {tipo:"mc",
       texto:"¿Qué diferencia hay entre un disco SSD y un disco HDD?",
       opts:["El HDD es más rápido","El SSD no guarda datos permanentemente","El SSD es más rápido y silencioso; el HDD usa platos magnéticos y es más lento","Son exactamente iguales"],correcta:2},

      {tipo:"mc",
       texto:"¿Cuál es la función de la memoria RAM en un computador?",
       opts:["Almacenar archivos permanentemente","Guardar temporalmente los datos y programas en uso para acceso rápido","Conectarse a internet","Mostrar imágenes en pantalla"],correcta:1},

      {tipo:"mc",
       texto:"Un usuario conecta su USB y Windows no la reconoce. El primer paso de diagnóstico es:",
       opts:["Formatear el computador","Probar la USB en otro puerto y en otro equipo para determinar si el problema es la USB o el puerto","Cambiar el sistema operativo","Comprar una USB nueva"],correcta:1},
    ]
  },

  // ══════════════════════════════════════════════
  //  6. MANTENIMIENTO GENERAL (2 preguntas)
  // ══════════════════════════════════════════════
  {
    id:"mant", titulo:"Mantenimiento general", icon:"🔧",
    color:"#854F0B", colorL:"#FDE9C4",
    preguntas:[
      {tipo:"mc",
       texto:"¿Con qué frecuencia se recomienda hacer limpieza de archivos temporales y desfragmentación (HDD) a un equipo de oficina en uso diario?",
       opts:["Cada día","Cada semana","Cada 1 a 3 meses","Solo cuando el equipo falla"],correcta:2},

      {tipo:"mc",
       texto:"Un equipo de oficina se reinicia solo varias veces al día. Antes de escalar el caso a soporte especializado, ¿qué revisarías primero?",
       opts:["El color del fondo de pantalla","Si hay actualizaciones de Windows pendientes y si el equipo tiene ventilación adecuada","El teclado","Si el monitor tiene brillo alto"],correcta:1},
    ]
  }
];

// ═══════════════ ESTADO ═══════════════
let curMod=0, answers={}, timerInt=null, secsLeft=3600, examStart=0, totalElap=0, submitted=false;

function showScr(id){document.querySelectorAll('.screen').forEach(s=>s.classList.remove('active'));document.getElementById(id).classList.add('active')}
function toast(msg){const t=document.getElementById('toast');t.textContent=msg;t.classList.add('show');setTimeout(()=>t.classList.remove('show'),2400)}

function startExam(){
  const n=document.getElementById('cand-name').value.trim();
  if(!n){document.getElementById('cand-name').focus();toast('Ingresa tu nombre para comenzar');return}
  submitted=false;answers={};curMod=0;secsLeft=3600;examStart=Date.now();
  showScr('s-exam');
  document.getElementById('timer-wrap').classList.add('vis');
  startTimer();renderMod();
}

function startTimer(){
  clearInterval(timerInt);updateTimer();
  timerInt=setInterval(()=>{secsLeft--;updateTimer();if(secsLeft<=0){clearInterval(timerInt);finishExam(true)}},1000);
}
function updateTimer(){
  const m=Math.floor(secsLeft/60),s=secsLeft%60;
  const el=document.getElementById('timer-val');
  el.textContent=`${String(m).padStart(2,'0')}:${String(s).padStart(2,'0')}`;
  el.className='timer-val'+(secsLeft<300?' urgent':'');
}

function renderMod(){
  const mod=MODULOS[curMod];
  const pb=document.getElementById('prog-badge');
  pb.textContent=`Módulo ${curMod+1} / ${MODULOS.length}`;pb.classList.add('vis');
  const ps=document.getElementById('prog-steps');ps.innerHTML='';
  MODULOS.forEach((_,i)=>{const d=document.createElement('div');d.className='pstep'+(i<curMod?' done':i===curMod?' active':'');ps.appendChild(d)});
  document.getElementById('prog-lbl').textContent=`${mod.titulo} — ${mod.preguntas.length} pregunta${mod.preguntas.length>1?'s':''}`;
  document.getElementById('mod-header').innerHTML=`<div class="mh-icon">${mod.icon}</div><div><div class="mh-title" style="color:${mod.color}">${mod.titulo}</div><div class="mh-sub" style="color:${mod.color}">${mod.preguntas.length} preguntas · ${mod.preguntas.filter(p=>p.tipo==='formula').length} de escritura libre</div></div>`;
  document.getElementById('mod-header').style.background=mod.colorL;

  const cont=document.getElementById('questions-container');cont.innerHTML='';
  let gNum=MODULOS.slice(0,curMod).reduce((a,m)=>a+m.preguntas.length,0);

  mod.preguntas.forEach((q,qi)=>{
    gNum++;
    const key=`${curMod}_${qi}`;
    const card=document.createElement('div');card.className='q-card';
    let html=`<div class="q-num">Pregunta ${gNum} de ${MODULOS.reduce((a,m)=>a+m.preguntas.length,0)}</div><div class="q-text">${q.texto}</div>`;
    if(q.hint)html+=`<div class="q-hint">💡 ${q.hint}</div>`;
    if(q.codigo)html+=`<div class="q-code">${q.codigo}</div>`;
    if(q.tipo==='mc'){
      html+=`<div class="opts">`;
      q.opts.forEach((o,oi)=>{
        const sel=answers[key]===oi;
        html+=`<div class="opt${sel?' selected':''}" onclick="selectOpt(${curMod},${qi},${oi})" id="opt_${qi}_${oi}"><div class="opt-letter">${['A','B','C','D'][oi]}</div><div class="opt-text">${o}</div></div>`;
      });
      html+=`</div>`;
    } else {
      const saved=answers[key]||'';
      html+=`<div class="formula-wrap"><label>Escribe tu respuesta:</label><input class="formula-input" type="text" id="finput_${qi}" value="${saved}" oninput="saveFormula(${curMod},${qi},this.value)" placeholder="Escribe aquí…"></div>`;
    }
    card.innerHTML=html;cont.appendChild(card);
  });

  document.getElementById('btn-prev').style.display=curMod===0?'none':'flex';
  document.getElementById('btn-next').style.display=curMod<MODULOS.length-1?'flex':'none';
  document.getElementById('btn-finish').style.display=curMod===MODULOS.length-1?'flex':'none';
  window.scrollTo(0,0);
}

function selectOpt(mi,qi,oi){
  if(submitted)return;
  answers[`${mi}_${qi}`]=oi;
  MODULOS[mi].preguntas[qi].opts.forEach((_,o)=>{const el=document.getElementById(`opt_${qi}_${o}`);el.className='opt'+(o===oi?' selected':'')});
}
function saveFormula(mi,qi,val){answers[`${mi}_${qi}`]=val.trim()}

function saveCurFormulas(){
  MODULOS[curMod].preguntas.forEach((q,qi)=>{if(q.tipo==='formula'){const el=document.getElementById(`finput_${qi}`);if(el)answers[`${curMod}_${qi}`]=el.value.trim()}});
}
function nextMod(){saveCurFormulas();if(curMod<MODULOS.length-1){curMod++;renderMod()}}
function prevMod(){saveCurFormulas();if(curMod>0){curMod--;renderMod()}}

function finishExam(timeout=false){
  clearInterval(timerInt);submitted=true;
  totalElap=Math.round((Date.now()-examStart)/1000);
  saveCurFormulas();
  if(timeout)toast('⏱ Tiempo agotado — calculando resultados...');
  setTimeout(showResults,timeout?1500:0);
}

function showResults(){
  showScr('s-results');
  document.getElementById('timer-wrap').classList.remove('vis');
  document.getElementById('prog-badge').classList.remove('vis');

  let totalC=0,totalW=0;const modRes=[];
  MODULOS.forEach((mod,mi)=>{
    let mc=0,mw=0,errs=[];
    mod.preguntas.forEach((q,qi)=>{
      const key=`${mi}_${qi}`;const resp=answers[key];let ok=false;
      if(q.tipo==='mc'){ok=resp===q.correcta}
      else{const rv=(resp||'').trim().toLowerCase().replace(/\s+/g,' ').replace(/;/g,',');ok=(q.correctas||[]).some(c=>c.toLowerCase().replace(/\s+/g,' ').replace(/;/g,',')===rv)}
      if(ok)mc++;
      else{mw++;errs.push({
        q:`P${MODULOS.slice(0,mi).reduce((a,m)=>a+m.preguntas.length,0)+qi+1}: ${q.texto.substring(0,65)}…`,
        user:resp!==undefined?(q.tipo==='mc'?(q.opts[resp]||'(sin respuesta)'):(resp||'(sin respuesta)')):'(sin respuesta)',
        correct:q.tipo==='mc'?q.opts[q.correcta]:(q.correctas?q.correctas[0]:'—')
      })}
    });
    totalC+=mc;totalW+=mw;
    modRes.push({mod,mc,mw,total:mc+mw,pct:Math.round((mc/(mc+mw))*100)||0,errs});
  });

  const total=totalC+totalW;
  const score=Math.round((totalC/total)*100);
  const name=document.getElementById('cand-name').value;

  const sb=document.getElementById('res-big');
  sb.textContent=score;
  sb.style.color=score>=75?'var(--green-m)':score>=60?'var(--amber-m)':'var(--red-m)';
  document.getElementById('res-name').textContent=name;
  const v=document.getElementById('res-verdict');
  if(score>=90){v.textContent='Excelente — Perfil altamente competente';v.style.cssText='background:var(--green-l);color:var(--green)'}
  else if(score>=75){v.textContent='Bueno — Competencias sólidas para el cargo';v.style.cssText='background:var(--green-l);color:var(--green)'}
  else if(score>=60){v.textContent='Aceptable — Requiere refuerzo en algunos módulos';v.style.cssText='background:var(--amber-l);color:var(--amber)'}
  else{v.textContent='Insuficiente — Se recomienda capacitación antes de vincular';v.style.cssText='background:var(--red-l);color:var(--red)'}

  document.getElementById('res-correct').textContent=totalC;
  document.getElementById('res-wrong').textContent=totalW;
  const mm=Math.floor(totalElap/60),ss=totalElap%60;
  document.getElementById('res-time').textContent=`${mm}m ${ss}s`;

  const rm=document.getElementById('res-modulos');rm.innerHTML='';
  modRes.forEach(r=>{
    const color=r.pct>=75?'var(--green-m)':r.pct>=60?'var(--amber-m)':'var(--red-m)';
    const bl=document.createElement('div');bl.className='mod-result';
    bl.innerHTML=`<div class="mr-head"><div class="mr-title">${r.mod.icon} ${r.mod.titulo}</div><div class="mr-pct" style="color:${color}">${r.pct}%</div></div>
    <div class="mr-bar-bg"><div class="mr-bar" style="width:${r.pct}%;background:${color}"></div></div>
    <div class="mr-meta">${r.mc} correctas · ${r.mw} incorrectas de ${r.total}</div>`;
    if(r.errs.length){
      const el=document.createElement('div');el.className='err-list';
      r.errs.forEach(e=>{
        const it=document.createElement('div');it.className='err-item';
        it.innerHTML=`<span class="err-q">${e.q}</span><div class="err-ans"><div class="err-user">✗ ${e.user}</div><div class="err-correct">✓ ${e.correct}</div></div>`;
        el.appendChild(it);
      });
      bl.appendChild(el);
    }
    rm.appendChild(bl);
  });
  window.scrollTo(0,0);

  enviarResultadosPorCorreo(name, score, totalElap, modRes);
}

function enviarResultadosPorCorreo(name, score, totalSecs, modRes){
  const modulos = modRes.map(r=>({
    titulo:r.mod.titulo,
    pct:r.pct,
    correct:r.mc,
    wrong:r.mw,
    errores:r.errs.map(e=>({pregunta:e.q,user:e.user,correct:e.correct}))
  }));

  fetch('{{ route('tecnicos.enviar_resultados') }}',{
    method:'POST',
    headers:{
      'Content-Type':'application/json',
      'X-CSRF-TOKEN':'{{ csrf_token() }}'
    },
    body:JSON.stringify({candidato:name,score,total_secs:totalSecs,modulos})
  })
  .then(r=>r.ok?toast('Resultados enviados por correo'):toast('No se pudo enviar el correo'))
  .catch(()=>toast('No se pudo enviar el correo'));
}

function restartExam(){showScr('s-intro');document.getElementById('cand-name').value=''}
</script>
</body>
</html>