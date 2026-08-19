<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Builder de Validadores</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
<style>
*{box-sizing:border-box;margin:0;padding:0}
:root{
  --bg:#0f1117;--bg2:#181c24;--bg3:#1e2330;--bg4:#242938;
  --border:#2d3348;--border2:#3a4060;
  --text:#e8eaf0;--text2:#9aa0b8;--text3:#5c6380;
  --purple:#7c6af7;--purple2:#6055d4;--purple3:#3d3580;--purpleLight:#2a2545;
  --green:#3dd68c;--green2:#28a86e;--greenLight:#1a3028;
  --red:#f0505a;--redLight:#2d1a1e;
  --yellow:#f5c842;--yellowLight:#2d2710;
  --blue:#4a9eff;--blueLight:#1a2540;
  --orange:#ff8c42;--orangeLight:#2d1e10;
  --radius:8px;--radius2:12px;--radius3:16px;
}
html,body{min-height:100vh;background:var(--bg);color:var(--text);font-family:system-ui,-apple-system,sans-serif;font-size:14px}
.sr-only{position:absolute;width:1px;height:1px;padding:0;margin:-1px;overflow:hidden;clip:rect(0,0,0,0);white-space:nowrap;border:0}
.layout{display:grid;grid-template-columns:260px 1fr;height:100vh;overflow:hidden}
.sidebar{background:var(--bg2);border-right:1px solid var(--border);display:flex;flex-direction:column;overflow:hidden}
.main{display:flex;flex-direction:column;overflow:hidden}
.sidebar-top{padding:16px;border-bottom:1px solid var(--border)}
.sidebar-logo{display:flex;align-items:center;gap:8px;margin-bottom:14px}
.sidebar-logo i{font-size:20px;color:var(--purple)}
.sidebar-logo span{font-size:15px;font-weight:600;color:var(--text)}
.btn-new{width:100%;padding:9px;background:var(--purpleLight);color:var(--purple);border:1px solid var(--purple3);border-radius:var(--radius);font-size:13px;font-weight:500;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:6px;transition:all .15s}
.btn-new:hover{background:var(--purple3);border-color:var(--purple)}
.sidebar-files{flex:1;overflow-y:auto;padding:12px}
.sidebar-section{font-size:10px;font-weight:600;color:var(--text3);letter-spacing:.08em;text-transform:uppercase;margin-bottom:8px;padding:0 4px}
.file-btn{width:100%;padding:8px 10px;background:transparent;color:var(--text2);border:none;border-radius:var(--radius);font-size:13px;cursor:pointer;display:flex;align-items:center;gap:8px;text-align:left;transition:all .1s;margin-bottom:2px}
.file-btn:hover{background:var(--bg3);color:var(--text)}
.file-btn.active{background:var(--purpleLight);color:var(--purple)}
.file-btn i{font-size:15px;opacity:.6}
.file-btn.active i{opacity:1}
.topbar{padding:12px 20px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;background:var(--bg2)}
.topbar-left{display:flex;align-items:center;gap:12px}
.topbar-title{font-size:15px;font-weight:600;color:var(--text)}
.topbar-badge{font-size:11px;padding:2px 8px;background:var(--bg3);color:var(--text2);border-radius:20px;border:1px solid var(--border)}
.topbar-actions{display:flex;gap:8px}
.btn{padding:7px 14px;border-radius:var(--radius);font-size:13px;font-weight:500;cursor:pointer;display:inline-flex;align-items:center;gap:6px;transition:all .15s;border:1px solid transparent}
.btn-ghost{background:transparent;color:var(--text2);border-color:var(--border)}
.btn-ghost:hover{background:var(--bg3);color:var(--text);border-color:var(--border2)}
.btn-primary{background:var(--purple);color:#fff;border-color:var(--purple)}
.btn-primary:hover{background:var(--purple2)}
.btn-danger{background:var(--redLight);color:var(--red);border-color:var(--red)}
.btn-danger:hover{opacity:.8}
.btn-success{background:var(--greenLight);color:var(--green);border-color:var(--green2)}
.btn-success:hover{opacity:.8}
.btn-sm{padding:5px 10px;font-size:12px}
.btn i{font-size:15px}
.content{flex:1;overflow-y:auto;padding:20px}
.section-card{background:var(--bg2);border:1px solid var(--border);border-radius:var(--radius2);margin-bottom:16px;overflow:hidden}
.section-header{padding:14px 16px;display:flex;align-items:center;justify-content:space-between;cursor:pointer;user-select:none}
.section-header:hover{background:var(--bg3)}
.section-title{display:flex;align-items:center;gap:8px;font-size:13px;font-weight:600;color:var(--text)}
.section-title i{font-size:16px;color:var(--purple);opacity:.8}
.section-body{padding:16px;border-top:1px solid var(--border)}
.chevron{font-size:14px;color:var(--text3);transition:transform .2s}
.chevron.open{transform:rotate(180deg)}
.form-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:12px}
.form-grid-2{display:grid;grid-template-columns:1fr 1fr;gap:12px}
.form-grid-3{display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px}
.form-group{display:flex;flex-direction:column;gap:5px}
.form-label{font-size:11px;font-weight:600;color:var(--text3);text-transform:uppercase;letter-spacing:.06em}
.form-input,.form-select,.form-textarea{background:var(--bg3);color:var(--text);border:1px solid var(--border);border-radius:var(--radius);padding:8px 10px;font-size:13px;font-family:inherit;outline:none;transition:border .15s;width:100%}
.form-input:focus,.form-select:focus,.form-textarea:focus{border-color:var(--purple);box-shadow:0 0 0 3px rgba(124,106,247,.15)}
.form-textarea{min-height:60px;resize:vertical;line-height:1.5}
.form-select option{background:var(--bg3)}
.form-hint{font-size:11px;color:var(--text3);margin-top:2px}
.help-ico{display:inline-flex;align-items:center;justify-content:center;width:14px;height:14px;border-radius:50%;background:var(--bg4);color:var(--text3);font-size:10px;font-weight:700;margin-left:5px;cursor:help;position:relative;vertical-align:middle;line-height:1}
.help-ico:hover{color:var(--purple);background:var(--purpleLight)}
.help-ico[data-tip]:hover::after{content:attr(data-tip);position:absolute;left:50%;transform:translateX(-50%);bottom:135%;background:var(--bg4);color:var(--text);border:1px solid var(--border2);border-radius:var(--radius);padding:8px 10px;font-size:11px;font-weight:400;line-height:1.45;white-space:pre-line;width:260px;box-shadow:0 4px 14px rgba(0,0,0,.4);z-index:1200;text-transform:none;letter-spacing:normal;text-align:left}
.help-ico[data-tip]:hover::before{content:'';position:absolute;left:50%;transform:translateX(-50%);bottom:122%;border:5px solid transparent;border-top-color:var(--border2);z-index:1200}
.env-grid{display:flex;gap:8px;flex-wrap:wrap}
.env-chip{display:flex;align-items:center;gap:6px;padding:6px 12px;background:var(--bg3);border:1px solid var(--border);border-radius:20px;cursor:pointer;font-size:12px;font-weight:500;color:var(--text2);transition:all .15s;user-select:none}
.env-chip:hover{border-color:var(--border2);color:var(--text)}
.env-chip.checked{background:var(--purpleLight);border-color:var(--purple);color:var(--purple)}
.env-chip input{display:none}
.env-chip i{font-size:13px}
.rules-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:14px}
.rules-count{font-size:12px;color:var(--text3);background:var(--bg3);padding:3px 10px;border-radius:20px;border:1px solid var(--border)}
.rule-card{background:var(--bg2);border:1px solid var(--border);border-radius:var(--radius2);margin-bottom:10px;overflow:hidden;transition:border .15s}
.rule-card:hover{border-color:var(--border2)}
.rule-card-header{padding:12px 14px;display:flex;align-items:center;gap:10px;cursor:pointer}
.rule-card-header:hover{background:var(--bg3)}
.rule-num{font-size:10px;font-weight:700;background:var(--bg4);color:var(--text3);padding:3px 7px;border-radius:6px;min-width:28px;text-align:center;border:1px solid var(--border)}
.rule-name{flex:1;font-size:13px;font-weight:500;color:var(--text)}
.rule-desc-small{font-size:12px;color:var(--text3);margin-left:4px;font-weight:400}
.rule-badges{display:flex;gap:4px;align-items:center}
.badge{font-size:10px;padding:2px 7px;border-radius:20px;font-weight:600;letter-spacing:.02em}
.badge-tipo{background:var(--bg4);color:var(--text2);border:1px solid var(--border)}
.badge-ob{background:var(--greenLight);color:var(--green);border:1px solid var(--green2)}
.badge-dep{background:var(--blueLight);color:var(--blue);border:1px solid var(--blue)}
.badge-adv{background:var(--orangeLight);color:var(--orange);border:1px solid var(--orange)}
.rule-actions{display:flex;gap:4px;margin-left:8px}
.rule-card-body{border-top:1px solid var(--border);padding:16px;display:none}
.rule-card-body.open{display:block}
.rule-tabs{display:flex;gap:2px;border-bottom:1px solid var(--border);margin-bottom:16px}
.rule-tab{padding:7px 14px;font-size:12px;font-weight:500;color:var(--text3);cursor:pointer;border-bottom:2px solid transparent;transition:all .15s;background:none;border-top:none;border-left:none;border-right:none}
.rule-tab:hover{color:var(--text)}
.rule-tab.active{color:var(--purple);border-bottom-color:var(--purple)}
.rule-tab-panel{display:none}
.rule-tab-panel.active{display:block}
.dep-block{background:var(--bg3);border:1px solid var(--border);border-radius:var(--radius);padding:12px;margin-bottom:8px}
.dep-block-title{font-size:11px;font-weight:600;color:var(--text3);text-transform:uppercase;letter-spacing:.06em;margin-bottom:10px;display:flex;align-items:center;gap:6px}
.dep-block-title i{font-size:13px;color:var(--blue)}
.vcr-item{background:var(--bg3);border:1px solid var(--border);border-radius:var(--radius);padding:12px;margin-bottom:8px;position:relative}
.vcr-item-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:10px}
.vcr-num{font-size:10px;font-weight:700;background:var(--bg4);color:var(--orange);padding:2px 6px;border-radius:4px;border:1px solid var(--orangeLight)}
.tag-input-wrap{background:var(--bg4);border:1px solid var(--border);border-radius:var(--radius);padding:6px 8px;min-height:36px;display:flex;flex-wrap:wrap;gap:4px;align-items:center;cursor:text}
.tag-input-wrap:focus-within{border-color:var(--purple);box-shadow:0 0 0 3px rgba(124,106,247,.15)}
.tag{display:inline-flex;align-items:center;gap:4px;background:var(--purpleLight);color:var(--purple);border:1px solid var(--purple3);border-radius:20px;padding:2px 8px;font-size:11px;font-weight:500}
.tag-del{cursor:pointer;opacity:.6;font-size:12px;line-height:1}
.tag-del:hover{opacity:1;color:var(--red)}
.tag-input{background:transparent;border:none;outline:none;color:var(--text);font-size:12px;min-width:80px;flex:1}
.spinner-overlay{position:fixed;inset:0;background:rgba(0,0,0,.7);display:flex;align-items:center;justify-content:center;z-index:9999;display:none}
.spinner-box{display:flex;flex-direction:column;align-items:center;gap:14px;background:var(--bg2);border:1px solid var(--border);border-radius:var(--radius2);padding:28px 40px}
.spinner{width:36px;height:36px;border:3px solid rgba(124,106,247,.2);border-top-color:var(--purple);border-radius:50%;animation:spin .7s linear infinite}
.spinner-text{font-size:13px;color:var(--text2)}
@keyframes spin{to{transform:rotate(360deg)}}
.toast{position:fixed;bottom:20px;right:20px;padding:10px 16px;border-radius:var(--radius);font-size:13px;font-weight:500;z-index:10000;display:flex;align-items:center;gap:8px;animation:slideup .25s ease;max-width:360px}
.toast.s{background:var(--greenLight);color:var(--green);border:1px solid var(--green2)}
.toast.e{background:var(--redLight);color:var(--red);border:1px solid var(--red)}
.toast.w{background:var(--yellowLight);color:var(--yellow);border:1px solid var(--yellow)}
@keyframes slideup{from{transform:translateY(12px);opacity:0}to{transform:translateY(0);opacity:1}}
.empty-rules{text-align:center;padding:40px 20px;color:var(--text3)}
.empty-rules i{font-size:40px;opacity:.3;display:block;margin-bottom:10px}
.empty-rules p{font-size:13px}
.modal-bg{position:fixed;inset:0;background:rgba(0,0,0,.7);z-index:1000;display:flex;align-items:center;justify-content:center;display:none}
.modal{background:var(--bg2);border:1px solid var(--border);border-radius:var(--radius2);width:600px;max-width:95vw;max-height:90vh;overflow-y:auto}
.modal-header{padding:16px 20px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;position:sticky;top:0;background:var(--bg2);z-index:2}
.modal-title{font-size:15px;font-weight:600;color:var(--text)}
.modal-body{padding:20px}
.modal-footer{padding:14px 20px;border-top:1px solid var(--border);display:flex;justify-content:flex-end;gap:8px}
.tipo-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:8px;margin-bottom:4px}
.tipo-opt{padding:10px;background:var(--bg3);border:1px solid var(--border);border-radius:var(--radius);cursor:pointer;text-align:center;transition:all .15s}
.tipo-opt:hover{border-color:var(--border2)}
.tipo-opt.sel{border-color:var(--purple);background:var(--purpleLight)}
.tipo-opt i{font-size:18px;display:block;margin-bottom:4px;color:var(--text3)}
.tipo-opt.sel i{color:var(--purple)}
.tipo-opt span{font-size:11px;font-weight:500;color:var(--text2)}
.tipo-opt.sel span{color:var(--purple)}
::-webkit-scrollbar{width:5px}
::-webkit-scrollbar-track{background:var(--bg)}
::-webkit-scrollbar-thumb{background:var(--bg4);border-radius:3px}
::-webkit-scrollbar-thumb:hover{background:var(--border2)}
@media(max-width:700px){.layout{grid-template-columns:1fr}.sidebar{display:none}}
</style>
</head>
<body>

<h1 class="sr-only">Constructor visual de validadores JSON.</h1>

<div id="spinner-overlay" class="spinner-overlay">
  <div class="spinner-box">
    <div class="spinner"></div>
    <span class="spinner-text" id="spinner-text">Cargando...</span>
  </div>
</div>

<div id="modal-bg" class="modal-bg">
  <div class="modal" id="modal">
    <div class="modal-header">
      <span class="modal-title" id="modal-title">Agregar regla</span>
      <button class="btn btn-ghost btn-sm" onclick="closeModal()"><i class="ti ti-x" aria-hidden="true"></i></button>
    </div>
    <div class="modal-body" id="modal-body"></div>
    <div class="modal-footer">
      <button class="btn btn-ghost" onclick="closeModal()">Cancelar</button>
      <button class="btn btn-primary" id="modal-save-btn" onclick="modalSave()"><i class="ti ti-check" aria-hidden="true"></i> Guardar regla</button>
    </div>
  </div>
</div>

<div class="layout">
  <div class="sidebar">
    <div class="sidebar-top">
      <div class="sidebar-logo">
        <i class="ti ti-shield-check" aria-hidden="true"></i>
        <span>Validadores</span>
        <a class="btn btn-ghost" href="{{ route('validator') }}" style="margin-left: auto;">
          <i class="ti ti-arrow-left" aria-hidden="true"></i>
        </a>
      </div>
      <button class="btn-new" onclick="newValidator()">
        <i class="ti ti-plus" aria-hidden="true"></i> Nuevo validador
      </button>
    </div>
    <div class="sidebar-files" id="sidebar-files">
      <div class="sidebar-section">Archivos guardados</div>
      <div id="files-list"></div>
    </div>
  </div>

  <div class="main">
    <div class="topbar">
      <div class="topbar-left">
        <span class="topbar-title" id="topbar-title">Sin validador cargado</span>
        <span class="topbar-badge" id="topbar-badge">—</span>
      </div>
      <div class="topbar-actions">
        <button class="btn btn-ghost" id="btn-export" onclick="exportJson()" style="display:none">
          <i class="ti ti-download" aria-hidden="true"></i> Exportar JSON
        </button>
        <button class="btn btn-success" id="btn-save" onclick="saveValidator()" style="display:none">
          <i class="ti ti-device-floppy" aria-hidden="true"></i> Guardar
        </button>
      </div>
    </div>
    <div class="content" id="content">
      <div style="text-align:center;padding:60px 20px;color:var(--text3)">
        <i class="ti ti-file-code" style="font-size:48px;opacity:.2;display:block;margin-bottom:12px" aria-hidden="true"></i>
        <p style="font-size:14px">Selecciona un validador del panel o crea uno nuevo</p>
      </div>
    </div>
  </div>
</div>

<!-- Modal para gestionar listas -->
<div id="listas-modal-bg" class="modal-bg" style="display:none">
  <div class="modal" id="listas-modal">
    <div class="modal-header">
      <span class="modal-title" id="listas-modal-title">Listas globales</span>
      <button class="btn btn-ghost btn-sm" onclick="closeListasModal()"><i class="ti ti-x" aria-hidden="true"></i></button>
    </div>
    <div class="modal-body" id="listas-modal-body" style="min-height:160px"></div>
    <div class="modal-footer">
      <button class="btn btn-ghost" onclick="closeListasModal()">Cerrar</button>
      <button class="btn btn-primary" onclick="applyListasChanges()">Guardar en validador</button>
      <button class="btn btn-ghost" onclick="saveListasServer()">Guardar en servidor</button>
      <button class="btn btn-success" onclick="downloadListas()">Descargar JSON</button>
    </div>
  </div>
</div>

<script>
// ─── STATE ───────────────────────────────────────────────────────────────────
var STATE = {
  files: [],
  current: null,
  currentFileName: null,
  editingRuleKey: null,
};

var TIPOS = [
  {v:'texto', label:'Texto', icon:'ti-alphabet'},
  {v:'texto_nonempty', label:'Texto requerido', icon:'ti-forms'},
  {v:'numero', label:'Número', icon:'ti-123'},
  {v:'fecha', label:'Fecha', icon:'ti-calendar'},
  {v:'fecha_sesion', label:'Fecha sesión', icon:'ti-calendar-event'},
  {v:'codigo', label:'Código', icon:'ti-qrcode'},
  {v:'rango', label:'Rango', icon:'ti-arrows-left-right'},
];

var ENTORNOS = ['laboral','educativo','comunitario','institucional'];
var _listasGlobales = {};
var _modalValCr = [];
var _depPosiciones = [];
var _depMultiples = [];
var _modalData = {};
var _tagData = {};

// ─── HELPERS ─────────────────────────────────────────────────────────────────
function showSpinner(msg){ document.getElementById('spinner-text').textContent=msg||'Cargando...'; document.getElementById('spinner-overlay').style.display='flex'; }
function hideSpinner(){ document.getElementById('spinner-overlay').style.display='none'; }
function toast(msg,type){ var t=document.createElement('div'); t.className='toast '+(type||'s'); var ic={s:'circle-check',e:'alert-circle',w:'alert-triangle'}; t.innerHTML='<i class="ti ti-'+ic[type||'s']+'" aria-hidden="true"></i>'+msg; document.body.appendChild(t); setTimeout(function(){t.remove();},3500); }
function esc(s){ return String(s||'').replace(/&/g,'&amp;').replace(/"/g,'&quot;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }
function help(text){ return '<span class="help-ico" data-tip="'+esc(text)+'" tabindex="0">?</span>'; }
function typoLabel(v){ var t=TIPOS.find(function(x){return x.v===v;}); return t?t.label:v; }
function capitalize(s){ return s.charAt(0).toUpperCase()+s.slice(1); }

// ─── SIDEBAR ─────────────────────────────────────────────────────────────────
function renderSidebar(){
  var list = document.getElementById('files-list');
  if(!STATE.files.length){ list.innerHTML='<div style="font-size:12px;color:var(--text3);padding:4px">Sin archivos</div>'; return; }
  var currentKey = STATE.currentFileName || (STATE.current && STATE.current.id);
  list.innerHTML = STATE.files.map(function(f){
    var active = currentKey && currentKey === f.id ? 'active' : '';
    return '<button class="file-btn '+active+'" onclick="loadFile(\''+f.id+'\')" data-id="'+f.id+'">'
      +'<i class="ti ti-file-code" aria-hidden="true"></i>'+esc(f.name||f.id)+'</button>';
  }).join('');
}

function newValidator(){
  STATE.current = {id:'',nombre:'',code:'',desc:'',entornos:[],rules:{},_listas:{}};
  STATE.currentFileName = null;
  document.getElementById('btn-save').style.display='';
  document.getElementById('btn-export').style.display='';
  renderEditor();
}

function loadFile(fileId){
  showSpinner('Cargando validador...');
  fetch('/validator-builder/load/'+fileId)
    .then(function(r){
      if(!r.ok) return r.json().then(function(e){ throw new Error(e.error||'HTTP '+r.status); });
      return r.json();
    })
    .then(function(data){
      hideSpinner();
      if(data && data.error){ toast('Error: '+data.error,'e'); return; }
      // Sanitizar rules: si llegó como array vacío [], convertir a objeto {}
      if(Array.isArray(data.rules)){
        var rulesObj = {};
        data.rules.forEach(function(r){
          if(r && r.campo) rulesObj[r.campo] = r;
        });
        data.rules = rulesObj;
      }
      if(!data.rules || typeof data.rules !== 'object') data.rules = {};
      STATE.current = data;
      STATE.currentFileName = fileId.toLowerCase().replace('.json','');
      document.getElementById('btn-save').style.display='';
      document.getElementById('btn-export').style.display='';
      renderSidebar();
      renderEditor();
      toast('Cargado: '+(data.nombre||fileId),'s');
    })
    .catch(function(err){ hideSpinner(); toast('Error al cargar: '+(err.message||err),'e'); });
}

// ─── RENDER EDITOR ───────────────────────────────────────────────────────────
function renderEditor(){
  if(!STATE.current) return;
  var c = STATE.current;
  var ruleKeys = Object.keys(c.rules||{});
  document.getElementById('topbar-title').textContent = c.nombre || 'Sin nombre';
  document.getElementById('topbar-badge').textContent = c.code || '—';

  var html = '';
  html += sectionCard('Datos generales','ti-info-circle',true,
    '<div class="form-grid">'
    +'<div class="form-group"><label class="form-label">ID</label><input class="form-input" id="f-id" value="'+esc(c.id||'')+'" placeholder="ej. ssc"></div>'
    +'<div class="form-group"><label class="form-label">Nombre</label><input class="form-input" id="f-nombre" value="'+esc(c.nombre||'')+'" placeholder="Sesiones Colectivas"></div>'
    +'<div class="form-group"><label class="form-label">Código</label><input class="form-input" id="f-code" value="'+esc(c.code||'')+'" placeholder="SSC"></div>'
    +'<div class="form-group"><label class="form-label">Descripción</label><input class="form-input" id="f-desc" value="'+esc(c.desc||'')+'" placeholder="Descripción del validador"></div>'
    +'</div>'
  );
  html += sectionCard('Entornos aplicables','ti-building',true,
    '<div class="env-grid" id="entornos-global">'
    + ENTORNOS.map(function(e){
        var checked = (c.entornos||[]).indexOf(e)!==-1;
        return '<div class="env-chip'+(checked?' checked':'')+'" onclick="toggleEnvGlobal(event,\''+e+'\')" style="cursor:pointer">'
          +'<input type="checkbox" '+(checked?'checked':'')+' data-env="'+e+'" style="display:none">'
          +'<i class="ti '+(checked?'ti-check':'ti-x')+'" aria-hidden="true"></i>'+capitalize(e)+'</div>';
      }).join('')
    +'</div>'
  );

    // Sección: Listas globales (visualizar/editar listas.json)
    html += sectionCard('Listas globales','ti-list',false,
      '<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px">'
      +'<div style="font-size:13px;color:var(--text2)">Gestiona las listas definidas en listas.json</div>'
      +'<div style="display:flex;gap:8px">'
        +'<button class="btn btn-ghost btn-sm" onclick="openListasManager()"><i class="ti ti-edit" aria-hidden="true"></i> Gestionar listas</button>'
        +'<button class="btn btn-ghost btn-sm" onclick="downloadListas()"><i class="ti ti-download" aria-hidden="true"></i> Descargar JSON</button>'
      +'</div>'
      +'</div>'
    );

  html += '<div class="rules-header">'
    +'<div style="display:flex;align-items:center;gap:10px">'
    +'<span style="font-size:14px;font-weight:600;color:var(--text)"><i class="ti ti-list-check" style="margin-right:6px;color:var(--purple)" aria-hidden="true"></i>Reglas de validación</span>'
    +'<span class="rules-count">'+ruleKeys.length+' reglas</span>'
    +'</div>'
    +'<button class="btn btn-primary btn-sm" onclick="openAddModal()"><i class="ti ti-plus" aria-hidden="true"></i> Agregar regla</button>'
    +'</div>';

  if(!ruleKeys.length){
    html += '<div class="empty-rules"><i class="ti ti-checklist" aria-hidden="true"></i><p>No hay reglas. Haz clic en "Agregar regla" para comenzar.</p></div>';
  } else {
    ruleKeys.forEach(function(key,idx){ html += renderRuleCard(key, c.rules[key], idx); });
  }
  document.getElementById('content').innerHTML = html;
}

function sectionCard(title,icon,openInit,body){
  var id='sc-'+Math.random().toString(36).slice(2,7);
  return '<div class="section-card">'
    +'<div class="section-header" onclick="toggleSection(\''+id+'\')">'
    +'<span class="section-title"><i class="ti '+icon+'" aria-hidden="true"></i>'+title+'</span>'
    +'<i class="ti ti-chevron-down chevron'+(openInit?' open':'')+'" id="chev-'+id+'" aria-hidden="true"></i>'
    +'</div>'
    +'<div class="section-body" id="'+id+'" style="'+(openInit?'':'display:none')+'">'+body+'</div>'
    +'</div>';
}

function toggleSection(id){
  var el=document.getElementById(id); var ch=document.getElementById('chev-'+id);
  var open=el.style.display!=='none'; el.style.display=open?'none':''; ch.classList.toggle('open',!open);
}

// ─── RENDER RULE CARD ─────────────────────────────────────────────────────────
function renderRuleCard(key,rule,idx){
  var badges='';
  badges += '<span class="badge badge-tipo">'+typoLabel(rule.tipo||'texto')+'</span>';
  if(rule.obligatorio && rule.obligatorio.length) badges += '<span class="badge badge-ob"><i class="ti ti-asterisk" style="font-size:9px" aria-hidden="true"></i> Obligatorio</span>';
  if(rule.dependencia) badges += '<span class="badge badge-dep"><i class="ti ti-link" style="font-size:9px" aria-hidden="true"></i> Dependencia</span>';
  if(rule.longitudMin!=null||rule.longitudMax!=null){
    var lonLabel = (rule.longitudMin!=null?rule.longitudMin:'0')+'-'+(rule.longitudMax!=null?rule.longitudMax:'∞')+' car.';
    badges += '<span class="badge badge-tipo"><i class="ti ti-ruler-2" style="font-size:9px" aria-hidden="true"></i> '+lonLabel+'</span>';
  }
  if(rule.repetible){
    var maxLabel = rule.repetibleMax > 0 ? ' (máx. '+rule.repetibleMax+')' : '';
    badges += '<span class="badge badge-dep"><i class="ti ti-copy" style="font-size:9px" aria-hidden="true"></i> Repetible'+maxLabel+'</span>';
  }
  if(rule.unificarNombres)
    badges += '<span class="badge" style="background:rgba(139,92,246,.12);color:#a78bfa;border:1px solid rgba(139,92,246,.25)"><i class="ti ti-arrows-diff" style="font-size:9px" aria-hidden="true"></i> Unificar nombres</span>';
  if(rule.consistentePorDocumento)
    badges += '<span class="badge" style="background:rgba(59,130,246,.1);color:#60a5fa;border:1px solid rgba(59,130,246,.3)"><i class="ti ti-id" style="font-size:9px" aria-hidden="true"></i> Consistente por doc.</span>';
  if(rule.existeEnArchivo)
    badges += '<span class="badge" style="background:rgba(245,200,66,.1);color:var(--yellow);border:1px solid rgba(245,200,66,.3)"><i class="ti ti-file-search" style="font-size:9px" aria-hidden="true"></i> Existe en archivo</span>';
  if(rule.validacionesCruzadas || rule.dependenciasMultiples || rule.reglasPorEntorno || rule.exceptoSi || rule.comparar)
    badges += '<span class="badge badge-adv"><i class="ti ti-star" style="font-size:9px" aria-hidden="true"></i> Avanzado</span>';

  var cardId='rc-'+idx;
  return '<div class="rule-card" id="'+cardId+'">'
    +'<div class="rule-card-header" onclick="toggleRule(\''+cardId+'\')">'
    +'<span class="rule-num">#'+(idx+1)+'</span>'
    +'<span class="rule-name">'+esc(key)+'<span class="rule-desc-small">'+esc(rule.desc||'')+'</span></span>'
    +'<div class="rule-badges">'+badges+'</div>'
    +'<div class="rule-actions" onclick="event.stopPropagation()">'
    +'<button class="btn btn-ghost btn-sm" onclick="openEditModal(\''+esc(key)+'\')" aria-label="Editar"><i class="ti ti-edit" aria-hidden="true"></i></button>'
    +'<button class="btn btn-danger btn-sm" onclick="deleteRule(\''+esc(key)+'\')" aria-label="Eliminar"><i class="ti ti-trash" aria-hidden="true"></i></button>'
    +'</div></div>'
    +'<div class="rule-card-body" id="'+cardId+'-body">'+renderRuleDetail(key,rule)+'</div>'
    +'</div>';
}

function renderRuleDetail(key,rule){
  var html='<div class="rule-tabs" role="tablist">';
  var tabs=[{id:'gen',label:'General',icon:'ti-info-circle'}];
  if(rule.dependencia) tabs.push({id:'dep',label:'Dependencia',icon:'ti-link'});
  if(rule.dependenciasPorPosicion && rule.dependenciasPorPosicion.length) tabs.push({id:'deppos',label:'Deps. posición',icon:'ti-list-numbers'});
  if(rule.dependenciasMultiples) tabs.push({id:'depm',label:'Deps. múlt.',icon:'ti-link'});
  if(rule.validacionesCruzadas) tabs.push({id:'vc',label:'Val. cruzadas',icon:'ti-arrows-split-2'});
  if(rule.reglasPorEntorno) tabs.push({id:'rpe',label:'Por entorno',icon:'ti-adjustments'});
  if(rule.exceptoSi || rule.comparar || rule.existeEnArchivo) tabs.push({id:'adv',label:'Avanzado',icon:'ti-settings'});

  var panelId='tab-'+encodeURIComponent(key);
  tabs.forEach(function(t,i){
    html += '<button class="rule-tab'+(i===0?' active':'')+'" onclick="switchTab(\''+panelId+'\',\''+t.id+'\')" role="tab">'
      +'<i class="ti '+t.icon+'" style="font-size:13px;margin-right:4px" aria-hidden="true"></i>'+t.label+'</button>';
  });
  html += '</div>';

  // Panel general
  html += '<div class="rule-tab-panel active" id="'+panelId+'-gen">';
  html += '<div class="form-grid-3" style="margin-bottom:12px">';
  html += '<div class="form-group"><label class="form-label">Campo</label><div class="form-input" style="background:var(--bg4);cursor:default">'+esc(key)+'</div></div>';
  html += '<div class="form-group"><label class="form-label">Tipo</label><div class="form-input" style="background:var(--bg4);cursor:default">'+esc(typoLabel(rule.tipo))+'</div></div>';
  html += '<div class="form-group"><label class="form-label">Descripción</label><div class="form-input" style="background:var(--bg4);cursor:default">'+esc(rule.desc||'—')+'</div></div>';
  html += '</div>';
  if(rule.obligatorio && rule.obligatorio.length){
    html += '<div style="margin-bottom:10px"><label class="form-label" style="display:block;margin-bottom:6px">Obligatorio en</label><div class="env-grid">'
      +rule.obligatorio.map(function(e){return '<span class="env-chip checked"><i class="ti ti-check" aria-hidden="true"></i>'+capitalize(e)+'</span>';}).join('')+'</div></div>';
  }
  if(rule.opcional && rule.opcional.length){
    html += '<div style="margin-bottom:10px"><label class="form-label" style="display:block;margin-bottom:6px">Opcional en</label><div class="env-grid">'
      +rule.opcional.map(function(e){return '<span class="env-chip" style="border-color:var(--yellow);color:var(--yellow);background:var(--yellowLight)"><i class="ti ti-minus" aria-hidden="true"></i>'+capitalize(e)+'</span>';}).join('')+'</div></div>';
  }
  if(rule.regex) html += '<div style="margin-bottom:8px"><label class="form-label">Expresión regular</label><div class="form-input" style="background:var(--bg4);font-family:monospace;font-size:12px;cursor:default">'+esc(rule.regex)+'</div></div>';
  if(rule.min!=null||rule.max!=null){
    html += '<div class="form-grid-2" style="margin-bottom:8px">';
    if(rule.min!=null) html += '<div class="form-group"><label class="form-label">Mínimo</label><div class="form-input" style="background:var(--bg4);cursor:default">'+rule.min+'</div></div>';
    if(rule.max!=null) html += '<div class="form-group"><label class="form-label">Máximo</label><div class="form-input" style="background:var(--bg4);cursor:default">'+rule.max+'</div></div>';
    html += '</div>';
  }
  if(rule.longitudMin!=null||rule.longitudMax!=null){
    html += '<div class="form-grid-2" style="margin-bottom:8px">';
    if(rule.longitudMin!=null) html += '<div class="form-group"><label class="form-label">Mín. caracteres</label><div class="form-input" style="background:var(--bg4);cursor:default">'+rule.longitudMin+'</div></div>';
    if(rule.longitudMax!=null) html += '<div class="form-group"><label class="form-label">Máx. caracteres</label><div class="form-input" style="background:var(--bg4);cursor:default">'+rule.longitudMax+'</div></div>';
    html += '</div>';
  }
  if(rule.valores){
    var v=Array.isArray(rule.valores)?rule.valores.join(', '):rule.valores;
    html += '<div style="margin-bottom:8px"><label class="form-label">Valores permitidos</label><div class="form-input" style="background:var(--bg4);cursor:default;font-size:12px">'+esc(v)+'</div></div>';
  }
  if(rule.maxHoy) html += '<div style="margin-bottom:8px"><span class="badge badge-adv"><i class="ti ti-calendar-x" style="font-size:10px" aria-hidden="true"></i> No puede ser fecha futura</span></div>';
  if(rule.unificarNombres){
    var unificarLabel='Unificar nombres — umbral: '+(rule.similaridadUmbral||90)+'%';
    if(rule.unificarNombresEntornos && rule.unificarNombresEntornos.length)
      unificarLabel+=' — solo en: '+rule.unificarNombresEntornos.map(capitalize).join(', ');
    else
      unificarLabel+=' — todos los entornos';
    html += '<div style="margin-bottom:8px"><span class="badge" style="background:rgba(139,92,246,.12);color:#a78bfa;border:1px solid rgba(139,92,246,.25)"><i class="ti ti-arrows-diff" style="font-size:10px" aria-hidden="true"></i> '+unificarLabel+'</span></div>';
  }
  if(rule.consistentePorDocumento){
    var consistenteLabel='Consistente por documento — debe ser idéntico en todos los registros de la misma persona';
    if(rule.consistentePorDocumentoEntornos && rule.consistentePorDocumentoEntornos.length)
      consistenteLabel+=' — solo en: '+rule.consistentePorDocumentoEntornos.map(capitalize).join(', ');
    else
      consistenteLabel+=' — todos los entornos';
    html += '<div style="margin-bottom:8px"><span class="badge" style="background:rgba(59,130,246,.1);color:#60a5fa;border:1px solid rgba(59,130,246,.3)"><i class="ti ti-id" style="font-size:10px" aria-hidden="true"></i> '+consistenteLabel+'</span></div>';
  }
  if(rule.repetible){
    var rl=rule.repetibleMax>0?' — máx. '+rule.repetibleMax+' ocurrencias':' — todas las ocurrencias';
    html += '<div style="margin-bottom:8px"><span class="badge badge-dep"><i class="ti ti-copy" style="font-size:10px" aria-hidden="true"></i> Repetible'+rl+'</span></div>';
  }
  html += '</div>';

  // Panel dependencia
  if(rule.dependencia){
    var dep=rule.dependencia;
    html += '<div class="rule-tab-panel" id="'+panelId+'-dep">';
    html += '<div class="dep-block"><div class="dep-block-title"><i class="ti ti-link" aria-hidden="true"></i>Condición de activación</div>';
    if(dep.archivoIndex!=null){
      html += '<div style="display:inline-flex;align-items:center;gap:6px;margin-bottom:8px;padding:4px 10px;background:var(--blueLight);border:1px solid var(--blue);border-radius:20px;font-size:11px;color:var(--blue)">'
        +'<i class="ti ti-files" style="font-size:13px" aria-hidden="true"></i>'
        +'Archivo #'+dep.archivoIndex+' · Clave: <b>'+esc(dep.claveUnion||'Ficha_fic')+'</b></div>';
    }
    html += '<div class="form-grid-2" style="margin-bottom:10px">';
    html += '<div class="form-group"><label class="form-label">Cuando el campo</label><div class="form-input" style="background:var(--bg4);font-size:12px;cursor:default">'+esc(dep.campo||'')+'</div></div>';
    html += '<div class="form-group"><label class="form-label">Tenga alguno de estos valores</label><div class="form-input" style="background:var(--bg4);font-size:12px;cursor:default">'+esc((dep.valores||[]).join(' / '))+'</div></div>';
    html += '</div>';
    if(dep.mensaje) html += '<div class="form-group" style="margin-bottom:10px"><label class="form-label">Mensaje</label><div class="form-input" style="background:var(--bg4);font-size:12px;cursor:default">'+esc(dep.mensaje)+'</div></div>';
    if(dep.entornos && dep.entornos.length){
      html += '<div style="margin-bottom:8px"><label class="form-label" style="display:block;margin-bottom:6px">Aplica en entornos</label><div class="env-grid">'+dep.entornos.map(function(e){return '<span class="env-chip checked"><i class="ti ti-check" aria-hidden="true"></i>'+capitalize(e)+'</span>';}).join('')+'</div></div>';
    }
    var flags=[];
    if(dep.requeridoCuandoCumple) flags.push('<span class="badge badge-dep">Requerido cuando cumple</span>');
    if(dep.errorSiNoCumpleTieneValor) flags.push('<span class="badge badge-dep">Error si no cumple pero tiene valor</span>');
    if(flags.length) html += '<div style="display:flex;gap:6px;flex-wrap:wrap">'+flags.join('')+'</div>';
    html += '</div></div>';
  }

  // Panel dependencias por posición
  if(rule.dependenciasPorPosicion && rule.dependenciasPorPosicion.length){
    html += '<div class="rule-tab-panel" id="'+panelId+'-deppos">';
    rule.dependenciasPorPosicion.forEach(function(dp,pi){
      html += '<div class="dep-block" style="margin-bottom:8px">';
      html += '<div class="dep-block-title"><i class="ti ti-link" aria-hidden="true"></i>';
      html += 'Posición '+(pi+1)+(pi===0?' — primera ocurrencia':pi===1?' — segunda ocurrencia':' — ocurrencia '+(pi+1));
      html += '</div>';
      html += '<div class="form-grid-2" style="margin-bottom:8px">';
      html += '<div class="form-group"><label class="form-label">Cuando el campo</label><div class="form-input" style="background:var(--bg4);font-size:12px;cursor:default">'+esc(dp.campo||'')+'</div></div>';
      html += '<div class="form-group"><label class="form-label">Tenga estos valores</label><div class="form-input" style="background:var(--bg4);font-size:12px;cursor:default">'+esc((dp.valores||[]).join(' / '))+'</div></div>';
      html += '</div>';
      if(dp.mensaje) html += '<div class="form-group" style="margin-bottom:8px"><label class="form-label">Mensaje</label><div class="form-input" style="background:var(--bg4);font-size:12px;cursor:default">'+esc(dp.mensaje)+'</div></div>';
      var flags2=[];
      if(dp.requeridoCuandoCumple!==false) flags2.push('<span class="badge badge-dep">Requerido cuando cumple</span>');
      if(dp.errorSiNoCumpleTieneValor) flags2.push('<span class="badge" style="background:var(--redLight);color:var(--red);border:1px solid var(--red)">Error si no cumple pero tiene valor</span>');
      if(flags2.length) html += '<div style="display:flex;gap:6px;flex-wrap:wrap">'+flags2.join('')+'</div>';
      html += '</div>';
    });
    html += '</div>';
  }

  // Panel deps múltiples
  if(rule.dependenciasMultiples){
    html += '<div class="rule-tab-panel" id="'+panelId+'-depm">';
    rule.dependenciasMultiples.forEach(function(dm,i){
      html += '<div class="dep-block" style="margin-bottom:8px"><div class="dep-block-title"><i class="ti ti-link" aria-hidden="true"></i>Dependencia adicional '+(i+1)+'</div>';
      if(dm.archivoIndex!=null){
        html += '<div style="display:inline-flex;align-items:center;gap:6px;margin-bottom:8px;padding:4px 10px;background:var(--blueLight);border:1px solid var(--blue);border-radius:20px;font-size:11px;color:var(--blue)">'
          +'<i class="ti ti-files" style="font-size:13px" aria-hidden="true"></i>'
          +'Archivo #'+dm.archivoIndex+' · Clave: <b>'+esc(dm.claveUnion||'Ficha_fic')+'</b></div>';
      }
      html += '<div class="form-grid-2" style="margin-bottom:8px">';
      html += '<div class="form-group"><label class="form-label">Cuando el campo</label><div class="form-input" style="background:var(--bg4);font-size:12px;cursor:default">'+esc(dm.campo||'')+'</div></div>';
      html += '<div class="form-group"><label class="form-label">Tenga alguno de estos valores</label><div class="form-input" style="background:var(--bg4);font-size:12px;cursor:default">'+esc((dm.valores||[]).join(' / '))+'</div></div>';
      html += '</div>';
      if(dm.mensaje) html += '<div class="form-group" style="margin-bottom:8px"><label class="form-label">Mensaje</label><div class="form-input" style="background:var(--bg4);font-size:12px;cursor:default">'+esc(dm.mensaje)+'</div></div>';
      if(dm.entornos && dm.entornos.length){
        html += '<div style="margin-bottom:8px"><label class="form-label" style="display:block;margin-bottom:6px">Aplica en entornos</label><div class="env-grid">'+dm.entornos.map(function(e){return '<span class="env-chip checked"><i class="ti ti-check" aria-hidden="true"></i>'+capitalize(e)+'</span>';}).join('')+'</div></div>';
      }
      var mflags=[];
      if(dm.requeridoCuandoCumple) mflags.push('<span class="badge badge-dep">Requerido cuando cumple</span>');
      if(dm.errorSiNoCumpleTieneValor) mflags.push('<span class="badge badge-dep">Error si no cumple pero tiene valor</span>');
      if(mflags.length) html += '<div style="display:flex;gap:6px;flex-wrap:wrap">'+mflags.join('')+'</div>';
      html += '</div>';
    });
    html += '</div>';
  }

  // Panel val cruzadas
  if(rule.validacionesCruzadas){
    html += '<div class="rule-tab-panel" id="'+panelId+'-vc">';
    rule.validacionesCruzadas.forEach(function(vc,i){
      html += '<div class="vcr-item"><div class="vcr-item-header"><span class="vcr-num">Regla '+(i+1)+'</span></div>';
      if(vc.condicion){
        var conds=Object.entries(vc.condicion).map(function(e){return '<b style="color:var(--text)">'+esc(e[0])+'</b> = '+esc((e[1]||[]).join(', '));});
        html += '<div class="form-group" style="margin-bottom:8px"><label class="form-label">Cuando</label><div class="form-input" style="background:var(--bg4);font-size:12px;cursor:default">'+conds.join(' <b style="color:var(--purple)">Y</b> ')+'</div></div>';
      }
      if(vc.valoresPermitidos && vc.valoresPermitidos.length){
        html += '<div class="form-group" style="margin-bottom:8px"><label class="form-label">Valores permitidos</label><div style="display:flex;flex-wrap:wrap;gap:4px">'+vc.valoresPermitidos.map(function(v){return '<span class="badge badge-ob">'+esc(v)+'</span>';}).join('')+'</div></div>';
      }
      if(vc.noPermitidos && vc.noPermitidos.length){
        html += '<div class="form-group" style="margin-bottom:8px"><label class="form-label">Valores NO permitidos</label><div style="display:flex;flex-wrap:wrap;gap:4px">'+vc.noPermitidos.map(function(v){return '<span class="badge" style="background:var(--redLight);color:var(--red);border:1px solid var(--red)">'+esc(v)+'</span>';}).join('')+'</div></div>';
      }
      if(vc.contiene && vc.contiene.length){
        html += '<div class="form-group" style="margin-bottom:8px"><label class="form-label">Debe contener</label><div style="display:flex;flex-wrap:wrap;gap:4px">'+vc.contiene.map(function(v){return '<span class="badge badge-dep">'+esc(v)+'</span>';}).join('')+'</div></div>';
      }
      if(vc.noContiene && vc.noContiene.length){
        html += '<div class="form-group" style="margin-bottom:8px"><label class="form-label">No debe contener</label><div style="display:flex;flex-wrap:wrap;gap:4px">'+vc.noContiene.map(function(v){return '<span class="badge" style="background:var(--redLight);color:var(--red);border:1px solid var(--red)">'+esc(v)+'</span>';}).join('')+'</div></div>';
      }
      if(vc.mensaje) html += '<div class="form-group"><label class="form-label">Mensaje</label><div class="form-input" style="background:var(--bg4);font-size:12px;cursor:default">'+esc(vc.mensaje)+'</div></div>';
      html += '</div>';
    });
    html += '</div>';
  }

  // Panel por entorno
  if(rule.reglasPorEntorno){
    html += '<div class="rule-tab-panel" id="'+panelId+'-rpe">';
    Object.entries(rule.reglasPorEntorno).forEach(function(entry){
      var env=entry[0], cfg=entry[1];
      html += '<div class="dep-block"><div class="dep-block-title"><i class="ti ti-adjustments" aria-hidden="true"></i>'+capitalize(env)+'</div>';
      if(cfg.valorFijo!==undefined) html += '<div class="form-group"><label class="form-label">Valor fijo</label><div class="form-input" style="background:var(--bg4);font-size:12px;cursor:default">'+esc(cfg.valorFijo||'(vacío)')+'</div></div>';
      else html += '<div style="font-size:12px;color:var(--text3)">Sin configuración específica</div>';
      html += '</div>';
    });
    html += '</div>';
  }

  // Panel avanzado
  if(rule.exceptoSi || rule.comparar || rule.existeEnArchivo){
    html += '<div class="rule-tab-panel" id="'+panelId+'-adv">';
    if(rule.existeEnArchivo){
      var eea=rule.existeEnArchivo;
      html += '<div class="dep-block" style="margin-bottom:12px"><div class="dep-block-title"><i class="ti ti-file-search" aria-hidden="true"></i>Existencia en otro archivo</div>';
      html += '<div class="form-grid-3">';
      html += '<div class="form-group"><label class="form-label">Archivo</label><div class="form-input" style="background:var(--bg4);font-size:12px;cursor:default">Archivo #'+eea.archivoIndex+'</div></div>';
      html += '<div class="form-group"><label class="form-label">Buscar en campo</label><div class="form-input" style="background:var(--bg4);font-size:12px;cursor:default">'+esc(eea.campo||'(mismo nombre)')+'</div></div>';
      html += '<div class="form-group"><label class="form-label">Mensaje</label><div class="form-input" style="background:var(--bg4);font-size:12px;cursor:default">'+esc(eea.mensaje||'—')+'</div></div>';
      html += '</div></div>';
    }
    if(rule.exceptoSi){
      html += '<div class="dep-block"><div class="dep-block-title"><i class="ti ti-ban" aria-hidden="true"></i>Excepto si</div>';
      html += '<div class="form-grid-2"><div class="form-group"><label class="form-label">Campo</label><div class="form-input" style="background:var(--bg4);font-size:12px;cursor:default">'+esc(rule.exceptoSi.campo||'')+'</div></div>';
      html += '<div class="form-group"><label class="form-label">Valor</label><div class="form-input" style="background:var(--bg4);font-size:12px;cursor:default">'+esc(rule.exceptoSi.valor||'')+'</div></div></div></div>';
    }
    if(rule.comparar){
      html += '<div class="dep-block"><div class="dep-block-title"><i class="ti ti-arrows-compare" aria-hidden="true"></i>Comparar con otro campo</div>';
      html += '<div class="form-grid-3"><div class="form-group"><label class="form-label">Campo referencia</label><div class="form-input" style="background:var(--bg4);font-size:12px;cursor:default">'+esc(rule.comparar.campoRef||'')+'</div></div>';
      html += '<div class="form-group"><label class="form-label">Operación</label><div class="form-input" style="background:var(--bg4);font-size:12px;cursor:default">'+esc(rule.comparar.operacion||'')+'</div></div>';
      html += '<div class="form-group"><label class="form-label">Mensaje</label><div class="form-input" style="background:var(--bg4);font-size:12px;cursor:default">'+esc(rule.comparar.mensaje||'')+'</div></div></div></div>';
    }
    html += '</div>';
  }

  return html;
}

function toggleRule(id){ document.getElementById(id+'-body').classList.toggle('open'); }

function switchTab(panelId,tabId){
  var card=document.getElementById(panelId+'-gen') && document.getElementById(panelId+'-gen').closest('.rule-card-body');
  if(!card) return;
  card.querySelectorAll('.rule-tab').forEach(function(t){t.classList.remove('active'); t.removeAttribute('aria-selected');});
  card.querySelectorAll('.rule-tab-panel').forEach(function(p){p.classList.remove('active');});
  var activeTab=card.querySelector('[onclick*="\''+tabId+'\'"]');
  if(activeTab){activeTab.classList.add('active'); activeTab.setAttribute('aria-selected','true');}
  var panel=document.getElementById(panelId+'-'+tabId);
  if(panel) panel.classList.add('active');
}

function toggleEnvGlobal(event,env){
  event.stopPropagation();
  if(!STATE.current) return;
  var arr=STATE.current.entornos||[];
  var idx=arr.indexOf(env);
  if(idx===-1) arr.push(env); else arr.splice(idx,1);
  STATE.current.entornos=arr;
  var wrap=document.getElementById('entornos-global');
  if(!wrap) return;
  wrap.querySelectorAll('.env-chip').forEach(function(chip){
    var e=chip.querySelector('input[data-env]'); if(!e) return;
    var isChecked=arr.indexOf(e.dataset.env)!==-1;
    chip.classList.toggle('checked',isChecked);
    chip.querySelector('i').className='ti '+(isChecked?'ti-check':'ti-x');
    e.checked=isChecked;
  });
}

function deleteRule(key){
  if(!confirm('¿Eliminar la regla "'+key+'"?')) return;
  // Asegurar que rules sea objeto antes de eliminar
  if(Array.isArray(STATE.current.rules)){
    var rulesObj = {};
    STATE.current.rules.forEach(function(r){
      if(r && r.campo) rulesObj[r.campo] = r;
    });
    STATE.current.rules = rulesObj;
  }
  delete STATE.current.rules[key];
  renderEditor();
  toast('Regla eliminada','w');
}

// ─── TAG INPUTS ──────────────────────────────────────────────────────────────
function initTagInput(id,values){
  _tagData[id]=values?JSON.parse(JSON.stringify(values)):[];
  renderTags(id);
}
function renderTags(id){
  var wrap=document.getElementById(id+'-tags');
  var hidden=document.getElementById(id+'-hidden');
  if(!wrap) return;
  wrap.innerHTML=_tagData[id].map(function(v,i){
    return '<span class="tag">'+esc(v)+'<span class="tag-del" onclick="removeTag(\''+id+'\','+i+')" aria-label="Eliminar">×</span></span>';
  }).join('');
  if(hidden) hidden.value=JSON.stringify(_tagData[id]);
}
function removeTag(id,i){ _tagData[id].splice(i,1); renderTags(id); }
function tagKeydown(e,id){
  if(e.key==='Enter'||e.key===','||e.key===';'){
    e.preventDefault();
    var val=e.target.value.trim().replace(/,|;/g,'');
    if(val){ _tagData[id].push(val); renderTags(id); e.target.value=''; }
  }
  if(e.key==='Backspace'&&!e.target.value&&_tagData[id].length){ _tagData[id].pop(); renderTags(id); }
}

// ─── MODAL ───────────────────────────────────────────────────────────────────
function openAddModal(){
  _modalData={mode:'add', rule:{campo:'',tipo:'texto',desc:'',obligatorio:[],opcional:[]}};
  _modalValCr=[];
  _depPosiciones=[];
  _depMultiples=[];
  renderModal('Agregar regla');
  document.getElementById('modal-bg').style.display='flex';
}

function openEditModal(key){
  var rule=JSON.parse(JSON.stringify(STATE.current.rules[key]||{}));
  rule.campo=key;
  _modalData={mode:'edit', origKey:key, rule:rule};
  _modalValCr=JSON.parse(JSON.stringify(rule.validacionesCruzadas||[]));
  _depPosiciones=JSON.parse(JSON.stringify(rule.dependenciasPorPosicion||[]));
  _depMultiples=JSON.parse(JSON.stringify(rule.dependenciasMultiples||[]));
  renderModal('Editar regla: '+key);
  document.getElementById('modal-bg').style.display='flex';
}

function closeModal(){ document.getElementById('modal-bg').style.display='none'; }

function renderModal(title){
  document.getElementById('modal-title').textContent=title;
  var r=_modalData.rule;
  var dep=r.dependencia||{};
  var exc=r.exceptoSi||{};
  var cmp=r.comparar||{};

  var html='';
  html += '<div class="rule-tabs" id="modal-tabs">'
    +'<button class="rule-tab active" onclick="switchModalTab(\'gen\')"><i class="ti ti-info-circle" style="font-size:13px;margin-right:4px" aria-hidden="true"></i>General</button>'
    +'<button class="rule-tab" onclick="switchModalTab(\'dep\')"><i class="ti ti-link" style="font-size:13px;margin-right:4px" aria-hidden="true"></i>Dependencia</button>'
    +'<button class="rule-tab" onclick="switchModalTab(\'valcr\')"><i class="ti ti-arrows-split-2" style="font-size:13px;margin-right:4px" aria-hidden="true"></i>Val. cruzadas</button>'
    +'<button class="rule-tab" onclick="switchModalTab(\'adv\')"><i class="ti ti-settings" style="font-size:13px;margin-right:4px" aria-hidden="true"></i>Avanzado</button>'
    +'</div>';

  // ── TAB GENERAL ──
  html += '<div class="rule-tab-panel active" id="modal-tab-gen">';
  html += '<div class="form-grid-2" style="margin-bottom:12px">';
  html += '<div class="form-group"><label class="form-label">Nombre del campo *'+help('Debe coincidir EXACTAMENTE con el nombre de la columna en el archivo Excel/CSV que se va a validar (mayúsculas, espacios y puntos incluidos).')+'</label><input class="form-input" id="m-campo" value="'+esc(r.campo||'')+'" placeholder=".Nombre del campo."></div>';
  html += '<div class="form-group"><label class="form-label">Descripción'+help('Texto explicativo que se muestra en los mensajes de error para que el usuario entienda qué campo falló. No afecta la validación.')+'</label><input class="form-input" id="m-desc" value="'+esc(r.desc||'')+'" placeholder="Para qué sirve"></div>';
  html += '</div>';

  html += '<div style="margin-bottom:14px"><label class="form-label" style="display:block;margin-bottom:8px">Tipo de dato'+help('Define cómo se valida el contenido: Texto (cualquier valor), Texto requerido (no puede quedar vacío), Número (debe ser numérico), Fecha / Fecha sesión (debe ser una fecha válida), Código (debe estar en la lista de "Valores permitidos"), Rango (número que debe estar entre un mínimo y un máximo).')+'</label>';
  html += '<div class="tipo-grid" id="tipo-grid">';
  TIPOS.forEach(function(t){
    var sel=(r.tipo||'texto')===t.v;
    html += '<div class="tipo-opt'+(sel?' sel':'')+'" onclick="selectTipo(\''+t.v+'\')" data-tipo="'+t.v+'"><i class="ti '+t.icon+'" aria-hidden="true"></i><span>'+t.label+'</span></div>';
  });
  html += '</div><input type="hidden" id="m-tipo" value="'+(r.tipo||'texto')+'"></div>';
  html += '<div id="tipo-extra-fields"></div>';

  html += '<div style="margin-bottom:12px"><label class="form-label" style="display:block;margin-bottom:8px">Obligatorio en entornos'+help('En los entornos marcados aquí, este campo NO puede quedar vacío. Si una fila no aplica a ninguno de estos entornos, el campo puede estar vacío sin error.')+'</label><div class="env-grid" id="m-obligatorio">';
  ENTORNOS.forEach(function(e){
    var ch=(r.obligatorio||[]).indexOf(e)!==-1;
    html += '<label class="env-chip'+(ch?' checked':'')+'" onclick="toggleModalEnv(\'obligatorio\',\''+e+'\')">'
      +'<input type="checkbox" '+(ch?'checked':'')+' data-env="'+e+'" style="display:none">'
      +'<i class="ti '+(ch?'ti-check':'ti-x')+'" aria-hidden="true"></i>'+capitalize(e)+'</label>';
  });
  html += '</div></div>';

  html += '<div style="margin-bottom:12px"><label class="form-label" style="display:block;margin-bottom:8px">Opcional en entornos'+help('Marca esto solo como referencia informativa: indica en qué entornos el campo puede aparecer pero no es obligatorio. No genera errores por sí solo.')+'</label><div class="env-grid" id="m-opcional">';
  ENTORNOS.forEach(function(e){
    var ch=(r.opcional||[]).indexOf(e)!==-1;
    html += '<label class="env-chip'+(ch?' checked':'')+'" onclick="toggleModalEnv(\'opcional\',\''+e+'\')" style="'+(ch?'border-color:var(--yellow);color:var(--yellow);background:var(--yellowLight)':'')+'">'
      +'<input type="checkbox" '+(ch?'checked':'')+' data-env="'+e+'" style="display:none">'
      +'<i class="ti '+(ch?'ti-check':'ti-x')+'" aria-hidden="true"></i>'+capitalize(e)+'</label>';
  });
  html += '</div></div>';

  html += '<div style="margin-bottom:12px"><label class="form-label">Validación (opcional)'+help('Elige un patrón (expresión regular) que el valor debe cumplir para ser válido, por ejemplo "Solo números" o "Correo electrónico". Si no cumple, se marca como advertencia.')+'</label>'
    +'<select class="form-input" id="m-regex">'
    +'<option value="">Sin validación</option>'
    +'<option value="^[A-ZÑÁÉÍÓÚ]+( [A-ZÑÁÉÍÓÚ]+)*$">Nombres completos en MAYÚSCULA</option>'
    +'<option value="^[0-9]+$">Solo números</option>'
    +'<option value="^[a-zA-Z]+$">Solo letras</option>'
    +'<option value="^[a-zA-Z0-9]+$">Letras y números</option>'
    +'<option value="^[A-Z0-9 ]+$">Letras y números en mayúscula</option>'
    +'<option value="^[A-Z]+$">Solo mayúsculas</option>'
    +'<option value="^[a-z]+$">Solo minúsculas</option>'
    +'<option value="^[0-9]{10}$">Exactamente 10 números</option>'
    +'<option value="^[^@\\s]+@[^@\\s]+\\.[^@\\s]+$">Correo electrónico</option>'
    +'<option value="^\\d+%$">Porcentaje</option>'
    +'<option value="^\\d{4}-\\d{2}-\\d{2}$">Fecha YYYY-MM-DD</option>'
    +'<option value="^(https?:\\/\\/)?([\\w.-]+)+(:\\d+)?(\\/([\\w/_-.]*)?)?$">URL</option>'
    +'</select></div>';

  html += '<div style="margin-bottom:12px"><label class="form-label">Valores permitidos'+help('Lista cerrada de valores exactos que acepta este campo (ej. "Sí", "No"). Escribe un valor y presiona Enter para agregarlo. Si el valor de la fila no está en esta lista, se marca como advertencia.')+'</label>'
    +'<div class="tag-input-wrap" id="m-valores-wrap" onclick="document.getElementById(\'m-valores-input\').focus()">'
    +'<span id="m-valores-tags"></span>'
    +'<input type="text" class="tag-input" id="m-valores-input" placeholder="Escribe y presiona Enter..." onkeydown="tagKeydown(event,\'m-valores\')">'
    +'</div><input type="hidden" id="m-valores-hidden"></div>';

  html += '<div class="form-group" style="margin-bottom:12px"><label class="form-label" style="display:block;margin-bottom:8px">Longitud de caracteres (opcional)'+help('Cuenta los caracteres del valor (no su valor numérico). Ideal para teléfonos o documentos: ej. mínimo 7 y máximo 10 caracteres.')+'</label>'
    +'<div class="form-grid-2">'
    +'<div class="form-group"><label class="form-label">Mínimo de caracteres</label><input class="form-input" id="m-lonmin" type="number" min="0" value="'+(r.longitudMin!=null?r.longitudMin:'')+'" placeholder="ej. 7"></div>'
    +'<div class="form-group"><label class="form-label">Máximo de caracteres</label><input class="form-input" id="m-lonmax" type="number" min="0" value="'+(r.longitudMax!=null?r.longitudMax:'')+'" placeholder="ej. 10"></div>'
    +'</div>'
    +'<div class="form-hint">Útil para teléfonos o documentos: valida cuántos caracteres tiene el valor (no su valor numérico).</div>'
    +'</div>';

  html += '<div style="margin-bottom:4px"><label class="env-chip" id="m-maxhoy-chip" onclick="toggleMaxHoy()" style="display:inline-flex;width:auto;'+(r.maxHoy?'border-color:var(--purple);color:var(--purple);background:var(--purpleLight)':'')+'">'
    +'<input type="checkbox" id="m-maxhoy" '+(r.maxHoy?'checked':'')+' style="display:none">'
    +'<i class="ti '+(r.maxHoy?'ti-check':'ti-x')+'" aria-hidden="true"></i> No puede ser fecha futura (maxHoy)</label>'+help('Solo aplica a campos de tipo Fecha. Si la fecha del archivo es posterior al día de hoy, se marca como error.')+'</div>';

  // Toggle repetible
  html += '<div style="margin-bottom:4px;margin-top:8px">'
    +'<label class="env-chip" id="m-repetible-chip" onclick="toggleRepetible()" style="display:inline-flex;width:auto;'+(r.repetible?'border-color:var(--blue);color:var(--blue);background:var(--blueLight)':'')+'">'
    +'<input type="checkbox" id="m-repetible" '+(r.repetible?'checked':'')+' style="display:none">'
    +'<i class="ti '+(r.repetible?'ti-check':'ti-x')+'" aria-hidden="true"></i> Este campo puede aparecer varias veces en el archivo'
    +'</label>'+help('Actívalo cuando el archivo tiene varias columnas con el mismo nombre (ej. "Teléfono 1", "Teléfono 2" agrupadas bajo un mismo campo). La regla se aplica a todas por igual.')
    +'<div style="font-size:11px;color:var(--text3);margin-top:4px;margin-left:4px">Si está activo, la misma validación aplica a todas las columnas con este nombre</div>'
    +'</div>'
    +'<div id="m-repetible-max-wrap" style="'+(r.repetible?'':'display:none')+';margin-top:8px;display:'+(r.repetible?'flex':'none')+';align-items:center;gap:10px;background:var(--bg3);border:1px solid var(--border);border-radius:var(--radius);padding:10px">'
    +'<i class="ti ti-list-numbers" style="color:var(--blue);font-size:16px" aria-hidden="true"></i>'
    +'<div style="flex:1"><div style="font-size:12px;font-weight:500;color:var(--text);margin-bottom:2px">Máximo de ocurrencias a validar</div>'
    +'<div style="font-size:11px;color:var(--text3)">Dejar en 0 para validar todas</div></div>'
    +'<input type="number" class="form-input" id="m-repetible-max" min="0" max="99" value="'+(r.repetibleMax||0)+'" style="width:70px;text-align:center">'
    +'</div>';

  // Toggle unificarNombres
  html += '<div style="margin-bottom:4px;margin-top:8px">'
    +'<label class="env-chip" id="m-unificar-chip" onclick="toggleUnificarNombres()" style="display:inline-flex;width:auto;'+(r.unificarNombres?'border-color:#a78bfa;color:#a78bfa;background:rgba(139,92,246,.12)':'')+'">'
    +'<input type="checkbox" id="m-unificar" '+(r.unificarNombres?'checked':'')+' style="display:none">'
    +'<i class="ti '+(r.unificarNombres?'ti-check':'ti-x')+'" aria-hidden="true"></i> Verificar uniformidad de nombres entre filas'
    +'</label>'+help('Compara todos los valores de este campo en el archivo y avisa si hay variaciones pequeñas que probablemente sean el mismo nombre mal escrito (ej. "Maria" vs "María").')
    +'<div style="font-size:11px;color:var(--text3);margin-top:4px;margin-left:4px">Detecta variaciones tipográficas comparando todos los valores del archivo (ej: letra de más, acento faltante)</div>'
    +'</div>'
    +'<div id="m-unificar-umbral-wrap" style="'+(r.unificarNombres?'':'display:none')+';margin-top:8px;display:'+(r.unificarNombres?'flex':'none')+';flex-direction:column;gap:10px;background:var(--bg3);border:1px solid var(--border);border-radius:var(--radius);padding:10px">'
    +'<div style="display:flex;align-items:center;gap:10px">'
    +'<i class="ti ti-arrows-diff" style="color:#a78bfa;font-size:16px" aria-hidden="true"></i>'
    +'<div style="flex:1"><div style="font-size:12px;font-weight:500;color:var(--text);margin-bottom:2px">Umbral de similitud (%)</div>'
    +'<div style="font-size:11px;color:var(--text3)">Si dos nombres son ≥ este % similares pero distintos → error. Recomendado: 90</div></div>'
    +'<input type="number" class="form-input" id="m-unificar-umbral" min="50" max="99" value="'+(r.similaridadUmbral||90)+'" style="width:70px;text-align:center">'
    +'</div>'
    +'<div>'
    +'<div style="font-size:11px;font-weight:600;color:var(--text3);text-transform:uppercase;letter-spacing:.06em;margin-bottom:6px">Aplica en entornos <span style="font-weight:400;color:var(--text3)">(vacío = todos)</span></div>'
    +'<div class="env-grid" id="m-unificar-entornos">'
    + ENTORNOS.map(function(e){
        var ch=(r.unificarNombresEntornos||[]).indexOf(e)!==-1;
        return '<label class="env-chip'+(ch?' checked':'')+'" onclick="toggleModalEnv(\'unificar-entornos\',\''+e+'\')">'
          +'<input type="checkbox" '+(ch?'checked':'')+' data-env="'+e+'" style="display:none">'
          +'<i class="ti '+(ch?'ti-check':'ti-x')+'" aria-hidden="true"></i>'+capitalize(e)+'</label>';
      }).join('')
    +'</div>'
    +'</div>'
    +'</div>';

  // Toggle consistentePorDocumento
  html += '<div style="margin-bottom:4px;margin-top:8px">'
    +'<label class="env-chip" id="m-consistente-chip" onclick="toggleConsistente()" style="display:inline-flex;width:auto;'+(r.consistentePorDocumento?'border-color:#60a5fa;color:#60a5fa;background:rgba(59,130,246,.1)':'')+'">'
    +'<input type="checkbox" id="m-consistente" '+(r.consistentePorDocumento?'checked':'')+' style="display:none">'
    +'<i class="ti '+(r.consistentePorDocumento?'ti-check':'ti-x')+'" aria-hidden="true"></i> Verificar consistencia por número de documento'
    +'</label>'+help('Si la misma persona (identificada por su documento) aparece en varias filas, este campo debe tener siempre el mismo valor en todas ellas. Útil para nombres, fecha de nacimiento, etc.')
    +'<div style="font-size:11px;color:var(--text3);margin-top:4px;margin-left:4px">Si la misma persona aparece en varias filas, el valor debe ser idéntico en todas</div>'
    +'</div>'
    +'<div id="m-consistente-entornos-wrap" style="'+(r.consistentePorDocumento?'':'display:none')+';margin-top:8px;background:var(--bg3);border:1px solid var(--border);border-radius:var(--radius);padding:10px">'
    +'<div style="font-size:11px;font-weight:600;color:var(--text3);text-transform:uppercase;letter-spacing:.06em;margin-bottom:6px">Aplica en entornos <span style="font-weight:400;color:var(--text3)">(vacío = todos)</span></div>'
    +'<div class="env-grid" id="m-consistente-entornos">'
    + ENTORNOS.map(function(e){
        var ch=(r.consistentePorDocumentoEntornos||[]).indexOf(e)!==-1;
        return '<label class="env-chip'+(ch?' checked':'')+'" onclick="toggleModalEnv(\'consistente-entornos\',\''+e+'\')">'
          +'<input type="checkbox" '+(ch?'checked':'')+' data-env="'+e+'" style="display:none">'
          +'<i class="ti '+(ch?'ti-check':'ti-x')+'" aria-hidden="true"></i>'+capitalize(e)+'</label>';
      }).join('')
    +'</div>'
    +'</div>';

  html += '</div>'; // /tab gen

  // ── TAB DEPENDENCIA ──
  html += '<div class="rule-tab-panel" id="modal-tab-dep">';
  html += '<p style="font-size:12px;color:var(--text3);margin-bottom:12px">Este campo será obligatorio solo cuando otro campo tenga cierto valor.</p>';
  html += '<div class="form-group" style="margin-bottom:12px"><label class="form-label">Cuando el campo'+help('Nombre EXACTO de otro campo (columna) de este mismo archivo cuyo valor activa la condición.')+'</label><input class="form-input" id="m-dep-campo" value="'+esc(dep.campo||'')+'" placeholder=".Nombre del campo dependiente."></div>';
  html += '<div class="form-group" style="margin-bottom:12px"><label class="form-label">Tenga alguno de estos valores'+help('Si el campo de arriba tiene cualquiera de estos valores en la fila, se activa la dependencia. Escribe un valor y Enter para agregarlo.')+'</label>'
    +'<div class="tag-input-wrap" id="m-dep-valores-wrap" onclick="document.getElementById(\'m-dep-valores-input\').focus()">'
    +'<span id="m-dep-valores-tags"></span>'
    +'<input type="text" class="tag-input" id="m-dep-valores-input" placeholder="Escribe y presiona Enter..." onkeydown="tagKeydown(event,\'m-dep-valores\')">'
    +'</div><input type="hidden" id="m-dep-valores-hidden"></div>';
  html += '<div class="form-group" style="margin-bottom:12px"><label class="form-label">Mensaje de error'+help('Texto que se muestra cuando la dependencia se cumple pero este campo no tiene el valor esperado.')+'</label><input class="form-input" id="m-dep-mensaje" value="'+esc(dep.mensaje||'')+'" placeholder="Este campo es obligatorio cuando..."></div>';
  html += '<div style="margin-bottom:12px"><label class="form-label" style="display:block;margin-bottom:8px">Aplica en entornos'+help('La dependencia solo se evalúa en los entornos marcados aquí. Vacío = se evalúa en todos los entornos.')+'</label><div class="env-grid" id="m-dep-entornos">';
  ENTORNOS.forEach(function(e){
    var ch=(dep.entornos||[]).indexOf(e)!==-1;
    html += '<label class="env-chip'+(ch?' checked':'')+'" onclick="toggleModalEnv(\'dep-entornos\',\''+e+'\')">'
      +'<input type="checkbox" '+(ch?'checked':'')+' data-env="'+e+'" style="display:none">'
      +'<i class="ti '+(ch?'ti-check':'ti-x')+'" aria-hidden="true"></i>'+capitalize(e)+'</label>';
  });
  html += '</div></div>';
  html += '<div style="display:flex;gap:16px;flex-wrap:wrap;margin-bottom:16px">';
  html += '<label class="env-chip" id="m-dep-req-chip" onclick="toggleDepFlag(\'req\')" style="width:auto;display:inline-flex;'+(dep.requeridoCuandoCumple?'border-color:var(--blue);color:var(--blue);background:var(--blueLight)':'')+'">'
    +'<input type="checkbox" id="m-dep-req" '+(dep.requeridoCuandoCumple?'checked':'')+' style="display:none">'
    +'<i class="ti '+(dep.requeridoCuandoCumple?'ti-check':'ti-x')+'" aria-hidden="true"></i> Requerido cuando cumple condición</label>'+help('Si se activa la condición de dependencia, este campo pasa a ser obligatorio (no puede quedar vacío).');
  html += '<label class="env-chip" id="m-dep-err-chip" onclick="toggleDepFlag(\'err\')" style="width:auto;display:inline-flex;'+(dep.errorSiNoCumpleTieneValor?'border-color:var(--red);color:var(--red);background:var(--redLight)':'')+'">'
    +'<input type="checkbox" id="m-dep-err" '+(dep.errorSiNoCumpleTieneValor?'checked':'')+' style="display:none">'
    +'<i class="ti '+(dep.errorSiNoCumpleTieneValor?'ti-check':'ti-x')+'" aria-hidden="true"></i> Error si no cumple pero tiene valor</label>'+help('Si la condición de dependencia NO se cumple pero este campo igual tiene un valor cargado, se marca como error (caso contrario al normal).');
  html += '</div>';

  // ── Dependencia entre archivos ──
  html += '<div class="dep-block" style="margin-top:4px">';
  html += '<div class="dep-block-title"><i class="ti ti-files" aria-hidden="true"></i>Dependencia entre archivos <span style="font-weight:400;color:var(--text3)">(opcional)</span></div>';
  html += '<div style="font-size:11px;color:var(--text3);margin-bottom:10px">Usa esto cuando el campo de referencia está en <b>otro archivo cargado</b>. Deja vacío si está en el mismo archivo.</div>';
  html += '<div class="form-grid-2">';
  html += '<div class="form-group"><label class="form-label">Índice del archivo'+help('Posición del archivo (según el orden en que el usuario los sube) donde está el campo de referencia: 0 = primero, 1 = segundo, etc.')+'</label>'
    +'<input class="form-input" id="m-dep-archivo-index" type="number" min="0" max="9" value="'+(dep.archivoIndex!=null?dep.archivoIndex:'')+'" placeholder="0 = primero, 1 = segundo...">'
    +'<div class="form-hint">0 = primer archivo, 1 = segundo archivo</div></div>';
  html += '<div class="form-group"><label class="form-label">Campo de unión (clave común)'+help('Columna presente en ambos archivos que permite emparejar la fila de este archivo con la fila correspondiente del otro archivo (ej. número de ficha o documento).')+'</label>'
    +'<input class="form-input" id="m-dep-clave-union" value="'+esc(dep.claveUnion||'')+'" placeholder="Ficha_fic">'
    +'<div class="form-hint">Campo que tienen en común ambos archivos para unir las filas</div></div>';
  html += '</div></div>';

  // ── Dependencias múltiples (varias condiciones OR, cada una puede ser de otro archivo) ──
  html += '<div style="border-top:1px solid var(--border);padding-top:16px;margin-top:16px">';
  html += '<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px">'
    +'<div><label class="form-label" style="display:block">Dependencias adicionales'+help('Agrega más condiciones de dependencia además de la principal. Útil cuando el campo depende de valores en varios archivos distintos (cada una puede tener su propio índice de archivo y clave de unión), o cuando necesitas varias condiciones independientes.')+'</label>'
    +'<div style="font-size:11px;color:var(--text3);margin-top:2px">Cada una puede apuntar a un archivo distinto con su propia clave de unión</div></div>'
    +'<button class="btn btn-ghost btn-sm" onclick="addDepMultiple()"><i class="ti ti-plus" aria-hidden="true"></i> Agregar dependencia</button>'
    +'</div>';
  html += '<div id="m-depm-list">';
  if(!_depMultiples.length){
    html += '<div style="font-size:12px;color:var(--text3);padding:8px;text-align:center">Sin dependencias adicionales. Haz clic en "Agregar dependencia".</div>';
  } else {
    _depMultiples.forEach(function(dm,mi){ html += renderDepMultipleRow(dm,mi); });
  }
  html += '</div></div>';

  // Dependencias por posición
  html += '<div id="m-dep-posiciones-wrap" style="'+(r.repetible?'':'display:none')+';border-top:1px solid var(--border);padding-top:16px">';
  html += '<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px">'
    +'<div><label class="form-label" style="display:block">Dependencias por posición'+help('Solo aparece si el campo es "repetible". Permite definir una condición distinta para cada columna repetida (ej. la primera ocurrencia depende de un campo, la segunda de otro).')+'</label>'
    +'<div style="font-size:11px;color:var(--text3);margin-top:2px">Define una dependencia diferente para cada ocurrencia del campo</div></div>'
    +'<button class="btn btn-ghost btn-sm" onclick="addDepPosicion()"><i class="ti ti-plus" aria-hidden="true"></i> Agregar posición</button>'
    +'</div>';
  html += '<div id="m-dep-posiciones-list">';
  if(!_depPosiciones.length){
    html += '<div style="font-size:12px;color:var(--text3);padding:8px;text-align:center">Sin dependencias por posición. Haz clic en "Agregar posición".</div>';
  } else {
    _depPosiciones.forEach(function(dp,pi){ html += renderDepPosicionRow(dp,pi); });
  }
  html += '</div></div>';
  html += '</div>'; // /tab dep

  // ── TAB VAL CRUZADAS ──
  html += '<div class="rule-tab-panel" id="modal-tab-valcr">';
  html += '<p style="font-size:12px;color:var(--text3);margin-bottom:12px">Define qué valores son válidos dependiendo de condiciones en otros campos.'+help('Ej: si "Tipo de intervención" = "Laboral", entonces este campo solo puede tener ciertos valores permitidos, o debe/no debe contener ciertas palabras. Se pueden agregar varias reglas con "Agregar validación cruzada".')+'</p>';
  html += '<div id="m-valcr-list"></div>';
  html += '<button class="btn btn-ghost btn-sm" onclick="addValCrModal()" style="margin-top:4px"><i class="ti ti-plus" aria-hidden="true"></i> Agregar validación cruzada</button>';
  html += '</div>';

  // ── TAB AVANZADO ──
  html += '<div class="rule-tab-panel" id="modal-tab-adv">';
  html += '<div class="dep-block" style="margin-bottom:12px"><div class="dep-block-title"><i class="ti ti-ban" aria-hidden="true"></i>Excepto si (excluir obligatorio)</div>';
  html += '<div class="form-grid-2">';
  html += '<div class="form-group"><label class="form-label">Campo'+help('Nombre EXACTO de otro campo del mismo archivo. Si este campo tiene ese valor exacto en la fila, la regla de "Obligatorio" de esta regla se ignora.')+'</label><input class="form-input" id="m-exc-campo" value="'+esc(exc.campo||'')+'" placeholder="nombre_campo"></div>';
  html += '<div class="form-group"><label class="form-label">Valor'+help('Valor exacto que debe tener el "Campo" de arriba para que este campo deje de ser obligatorio.')+'</label><input class="form-input" id="m-exc-valor" value="'+esc(exc.valor||'')+'" placeholder="valor_excepcion"></div>';
  html += '</div></div>';
  html += '<div class="dep-block" style="margin-bottom:12px"><div class="dep-block-title"><i class="ti ti-arrows-compare" aria-hidden="true"></i>Comparar con otro campo (fechas)'+help('Solo para campos de tipo Fecha. Compara la fecha de este campo contra la fecha de otro campo de la misma fila (mismo día calendario, sin importar la hora).')+'</div>';
  html += '<div class="form-grid-3">';
  html += '<div class="form-group"><label class="form-label">Campo referencia'+help('Nombre EXACTO del otro campo de fecha contra el que se compara (ej. Fecha_intervencion).')+'</label><input class="form-input" id="m-cmp-campo" value="'+esc(cmp.campoRef||'')+'" placeholder="Fecha_intervencion"></div>';
  html += '<div class="form-group"><label class="form-label">Operación'+help('Condición que DEBE cumplirse para que NO haya error (fecha de este campo vs. campo de referencia):\n>= mayor o igual: válido si es igual o posterior. Error solo si es ANTERIOR.\n<= menor o igual: válido si es igual o anterior. Error solo si es POSTERIOR.\n> mayor (estricto): error si es igual o anterior (fechas iguales SÍ dan error).\n< menor (estricto): error si es igual o posterior (fechas iguales SÍ dan error).\n== igual: error si son fechas distintas.\nSi solo quieres bloquear fechas anteriores permitiendo el mismo día, usa ">=".')+'</label><select class="form-select" id="m-cmp-op"><option value="">— ninguna —</option><option value=">=">&gt;= mayor o igual</option><option value="<=">&lt;= menor o igual</option><option value=">">&gt; mayor</option><option value="<">&lt; menor</option><option value="==">== igual</option></select></div>';
  html += '<div class="form-group"><label class="form-label">Mensaje'+help('Texto de error que se muestra cuando la comparación de fechas falla.')+'</label><input class="form-input" id="m-cmp-msg" value="'+esc(cmp.mensaje||'')+'" placeholder="Mensaje de error"></div>';
  html += '</div></div>';
  var eea2=r.existeEnArchivo||{};
  html += '<div class="dep-block"><div class="dep-block-title"><i class="ti ti-file-search" style="color:var(--yellow)" aria-hidden="true"></i>Existencia en otro archivo</div>';
  html += '<p style="font-size:11px;color:var(--text3);margin-bottom:10px">El valor de este campo debe existir en una columna de otro archivo. Si no aparece, se genera un error.</p>';
  html += '<div class="form-grid-3">';
  html += '<div class="form-group"><label class="form-label">Índice del archivo'+help('Posición del archivo donde se debe buscar el valor, según el orden en que el usuario los sube (0 = primer archivo, 1 = segundo, etc.).')+'</label>'
    +'<input class="form-input" id="m-eea-archivo" type="number" min="0" max="9" value="'+(eea2.archivoIndex!=null?eea2.archivoIndex:'')+'" placeholder="0=primero, 1=segundo...">'
    +'<div class="form-hint">0 = primer archivo cargado</div></div>';
  html += '<div class="form-group"><label class="form-label">Campo donde buscar <span style="color:var(--text3);font-weight:400">(opcional)</span>'+help('Nombre de la columna en el OTRO archivo donde se busca el valor. Déjalo vacío si la columna se llama igual en ambos archivos.')+'</label>'
    +'<input class="form-input" id="m-eea-campo" value="'+esc(eea2.campo||'')+'" placeholder="Dejar vacío = mismo nombre">'
    +'<div class="form-hint">Vacío = usa el mismo nombre de este campo</div></div>';
  html += '<div class="form-group"><label class="form-label">Mensaje de error'+help('Texto que se muestra cuando el valor de este campo no se encuentra en el otro archivo.')+'</label>'
    +'<input class="form-input" id="m-eea-mensaje" value="'+esc(eea2.mensaje||'')+'" placeholder="El documento no existe en el archivo 1"></div>';
  html += '</div></div></div>';

  document.getElementById('modal-body').innerHTML = html;

  initTagInput('m-valores', Array.isArray(r.valores) ? r.valores : (r.valores ? [r.valores] : []));
  initTagInput('m-dep-valores', dep.valores || []);

  // Inicializar deps por posición
  if(_depPosiciones.length) renderDepPosicionesList();

  // Inicializar deps múltiples
  if(_depMultiples.length) renderDepMultiplesList();

  _modalValCr = JSON.parse(JSON.stringify(r.validacionesCruzadas || []));
  renderValCrModal();

  if(cmp.operacion) document.getElementById('m-cmp-op').value = cmp.operacion;
  if(r.regex){
    var sel = document.getElementById('m-regex');
    if(sel) sel.value = r.regex;
  }
  updateTipoExtras(r.tipo||'texto');
}

function renderDepPosicionRow(dp,pi){
  dp=dp||{};
  var html='<div class="dep-block" id="m-dep-pos-row-'+pi+'" style="margin-bottom:8px">';
  html += '<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px">';
  html += '<div class="dep-block-title" style="margin-bottom:0"><i class="ti ti-link" aria-hidden="true"></i>Posición '+(pi+1)+(pi===0?' (primera ocurrencia)':pi===1?' (segunda ocurrencia)':' (ocurrencia '+(pi+1)+')')+'</div>';
  html += '<button class="btn btn-danger btn-sm" onclick="removeDepPosicion('+pi+')"><i class="ti ti-trash" aria-hidden="true"></i></button>';
  html += '</div>';
  html += '<div class="form-grid-2" style="margin-bottom:8px">';
  html += '<div class="form-group"><label class="form-label">Cuando el campo</label>'
    +'<input class="form-input" id="m-dep-pos-campo-'+pi+'" value="'+esc(dp.campo||'')+'" placeholder=".Campo de referencia."></div>';
  html += '<div class="form-group"><label class="form-label">Tenga alguno de estos valores</label>'
    +'<div class="tag-input-wrap" id="m-dep-pos-vals-wrap-'+pi+'" onclick="document.getElementById(\'m-dep-pos-vals-input-'+pi+'\').focus()">'
    +'<span id="m-dep-pos-vals-'+pi+'-tags"></span>'
    +'<input type="text" class="tag-input" id="m-dep-pos-vals-input-'+pi+'" onkeydown="tagKeydown(event,\'m-dep-pos-vals-'+pi+'\')" placeholder="valor...">'
    +'</div><input type="hidden" id="m-dep-pos-vals-'+pi+'-hidden"></div>';
  html += '</div>';
  html += '<div class="form-group" style="margin-bottom:8px"><label class="form-label">Mensaje de error</label>'
    +'<input class="form-input" id="m-dep-pos-msg-'+pi+'" value="'+esc(dp.mensaje||'')+'" placeholder="Este campo es obligatorio cuando..."></div>';
  html += '<div style="display:flex;gap:12px;flex-wrap:wrap">';
  html += '<label style="display:flex;align-items:center;gap:6px;cursor:pointer;font-size:12px">'
    +'<input type="checkbox" id="m-dep-pos-req-'+pi+'" '+(dp.requeridoCuandoCumple!==false?'checked':'')+' style="cursor:pointer;accent-color:var(--purple)"> Requerido cuando cumple</label>';
  html += '<label style="display:flex;align-items:center;gap:6px;cursor:pointer;font-size:12px">'
    +'<input type="checkbox" id="m-dep-pos-err-'+pi+'" '+(dp.errorSiNoCumpleTieneValor?'checked':'')+' style="cursor:pointer;accent-color:var(--red)"> Error si no cumple pero tiene valor</label>';
  html += '</div></div>';
  return html;
}

function renderDepMultipleRow(dm,mi){
  dm=dm||{};
  var html='<div class="dep-block" id="m-depm-row-'+mi+'" style="margin-bottom:8px">';
  html += '<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px">';
  html += '<div class="dep-block-title" style="margin-bottom:0"><i class="ti ti-link" aria-hidden="true"></i>Dependencia adicional '+(mi+1)+'</div>';
  html += '<button class="btn btn-danger btn-sm" onclick="removeDepMultiple('+mi+')"><i class="ti ti-trash" aria-hidden="true"></i></button>';
  html += '</div>';
  html += '<div class="form-grid-2" style="margin-bottom:8px">';
  html += '<div class="form-group"><label class="form-label">Cuando el campo</label>'
    +'<input class="form-input" id="m-depm-campo-'+mi+'" value="'+esc(dm.campo||'')+'" placeholder="Nombre del campo dependiente"></div>';
  html += '<div class="form-group"><label class="form-label">Tenga alguno de estos valores</label>'
    +'<div class="tag-input-wrap" id="m-depm-vals-'+mi+'-wrap" onclick="document.getElementById(\'m-depm-vals-'+mi+'-input\').focus()">'
    +'<span id="m-depm-vals-'+mi+'-tags"></span>'
    +'<input type="text" class="tag-input" id="m-depm-vals-'+mi+'-input" onkeydown="tagKeydown(event,\'m-depm-vals-'+mi+'\')" placeholder="valor...">'
    +'</div><input type="hidden" id="m-depm-vals-'+mi+'-hidden"></div>';
  html += '</div>';
  html += '<div class="form-group" style="margin-bottom:8px"><label class="form-label">Mensaje de error</label>'
    +'<input class="form-input" id="m-depm-msg-'+mi+'" value="'+esc(dm.mensaje||'')+'" placeholder="Este campo es obligatorio cuando..."></div>';
  html += '<div style="margin-bottom:8px"><label class="form-label" style="display:block;margin-bottom:6px">Aplica en entornos</label><div class="env-grid" id="m-depm-entornos-'+mi+'">';
  ENTORNOS.forEach(function(e){
    var ch=(dm.entornos||[]).indexOf(e)!==-1;
    html += '<label class="env-chip'+(ch?' checked':'')+'" onclick="toggleModalEnv(\'depm-entornos-'+mi+'\',\''+e+'\')">'
      +'<input type="checkbox" '+(ch?'checked':'')+' data-env="'+e+'" style="display:none">'
      +'<i class="ti '+(ch?'ti-check':'ti-x')+'" aria-hidden="true"></i>'+capitalize(e)+'</label>';
  });
  html += '</div></div>';
  html += '<div style="display:flex;gap:12px;flex-wrap:wrap;margin-bottom:12px">';
  html += '<label class="env-chip" id="m-depm-req-chip-'+mi+'" onclick="toggleDepMultipleFlag('+mi+',\'req\')" style="width:auto;display:inline-flex;'+(dm.requeridoCuandoCumple?'border-color:var(--blue);color:var(--blue);background:var(--blueLight)':'')+'">'
    +'<input type="checkbox" id="m-depm-req-'+mi+'" '+(dm.requeridoCuandoCumple?'checked':'')+' style="display:none">'
    +'<i class="ti '+(dm.requeridoCuandoCumple?'ti-check':'ti-x')+'" aria-hidden="true"></i> Requerido cuando cumple</label>';
  html += '<label class="env-chip" id="m-depm-err-chip-'+mi+'" onclick="toggleDepMultipleFlag('+mi+',\'err\')" style="width:auto;display:inline-flex;'+(dm.errorSiNoCumpleTieneValor?'border-color:var(--red);color:var(--red);background:var(--redLight)':'')+'">'
    +'<input type="checkbox" id="m-depm-err-'+mi+'" '+(dm.errorSiNoCumpleTieneValor?'checked':'')+' style="display:none">'
    +'<i class="ti '+(dm.errorSiNoCumpleTieneValor?'ti-check':'ti-x')+'" aria-hidden="true"></i> Error si no cumple pero tiene valor</label>';
  html += '</div>';
  html += '<div style="font-size:11px;color:var(--text3);margin-bottom:8px"><i class="ti ti-files" aria-hidden="true"></i> Dependencia entre archivos (opcional, dejar vacío si el campo de referencia está en este mismo archivo)</div>';
  html += '<div class="form-grid-2">';
  html += '<div class="form-group"><label class="form-label">Índice del archivo'+help('Posición del archivo (0 = primero, 1 = segundo, etc.) donde está el campo de referencia.')+'</label>'
    +'<input class="form-input" id="m-depm-archivo-'+mi+'" type="number" min="0" max="9" value="'+(dm.archivoIndex!=null?dm.archivoIndex:'')+'" placeholder="0 = primero, 1 = segundo..."></div>';
  html += '<div class="form-group"><label class="form-label">Campo de unión (clave común)'+help('Columna presente en ambos archivos que permite emparejar la fila de este archivo con la fila correspondiente del otro archivo.')+'</label>'
    +'<input class="form-input" id="m-depm-clave-'+mi+'" value="'+esc(dm.claveUnion||'')+'" placeholder="Ficha_fic"></div>';
  html += '</div></div>';
  return html;
}

function toggleDepMultipleFlag(mi,which){
  var cbId = which==='req' ? 'm-depm-req-'+mi : 'm-depm-err-'+mi;
  var chipId = which==='req' ? 'm-depm-req-chip-'+mi : 'm-depm-err-chip-'+mi;
  var color = which==='req' ? 'var(--blue)' : 'var(--red)';
  var bg = which==='req' ? 'var(--blueLight)' : 'var(--redLight)';
  var cb=document.getElementById(cbId); cb.checked=!cb.checked;
  var chip=document.getElementById(chipId); chip.querySelector('i').className='ti '+(cb.checked?'ti-check':'ti-x');
  if(cb.checked){chip.style.borderColor=color;chip.style.color=color;chip.style.background=bg;}
  else{chip.style.borderColor='';chip.style.color='';chip.style.background='';}
}

function addDepMultiple(){
  _depMultiples.push({campo:'',valores:[],mensaje:'',entornos:[],requeridoCuandoCumple:true,errorSiNoCumpleTieneValor:false});
  renderDepMultiplesList();
}
function removeDepMultiple(mi){ _depMultiples.splice(mi,1); renderDepMultiplesList(); }
function renderDepMultiplesList(){
  var list=document.getElementById('m-depm-list');
  if(!list) return;
  if(!_depMultiples.length){
    list.innerHTML='<div style="font-size:12px;color:var(--text3);padding:8px;text-align:center">Sin dependencias adicionales. Haz clic en "Agregar dependencia".</div>';
    return;
  }
  list.innerHTML=_depMultiples.map(function(dm,mi){ return renderDepMultipleRow(dm,mi); }).join('');
  _depMultiples.forEach(function(dm,mi){ initTagInput('m-depm-vals-'+mi, dm.valores||[]); });
}

function addDepPosicion(){
  _depPosiciones.push({campo:'',valores:[],mensaje:'',requeridoCuandoCumple:true,errorSiNoCumpleTieneValor:false});
  renderDepPosicionesList();
}
function removeDepPosicion(pi){ _depPosiciones.splice(pi,1); renderDepPosicionesList(); }
function renderDepPosicionesList(){
  var list=document.getElementById('m-dep-posiciones-list');
  if(!list) return;
  if(!_depPosiciones.length){
    list.innerHTML='<div style="font-size:12px;color:var(--text3);padding:8px;text-align:center">Sin dependencias por posición. Haz clic en "Agregar posición".</div>';
    return;
  }
  list.innerHTML=_depPosiciones.map(function(dp,pi){ return renderDepPosicionRow(dp,pi); }).join('');
  _depPosiciones.forEach(function(dp,pi){ initTagInput('m-dep-pos-vals-'+pi, dp.valores||[]); });
}

function toggleRepetible(){
  var cb=document.getElementById('m-repetible');
  cb.checked=!cb.checked;
  var chip=document.getElementById('m-repetible-chip');
  chip.querySelector('i').className='ti '+(cb.checked?'ti-check':'ti-x');
  if(cb.checked){chip.style.borderColor='var(--blue)';chip.style.color='var(--blue)';chip.style.background='var(--blueLight)';}
  else{chip.style.borderColor='';chip.style.color='';chip.style.background='';}
  var wrap=document.getElementById('m-dep-posiciones-wrap');
  if(wrap) wrap.style.display=cb.checked?'':'none';
  var maxWrap=document.getElementById('m-repetible-max-wrap');
  if(maxWrap) maxWrap.style.display=cb.checked?'flex':'none';
}

function switchModalTab(tabId){
  document.getElementById('modal-body').querySelectorAll('.rule-tab').forEach(function(t){t.classList.remove('active');});
  document.getElementById('modal-body').querySelectorAll('.rule-tab-panel').forEach(function(p){p.classList.remove('active');});
  var tabs=document.getElementById('modal-tabs').querySelectorAll('.rule-tab');
  var order=['gen','dep','valcr','adv'];
  var idx=order.indexOf(tabId);
  if(tabs[idx]) tabs[idx].classList.add('active');
  var panel=document.getElementById('modal-tab-'+tabId);
  if(panel) panel.classList.add('active');
}

function selectTipo(v){
  document.getElementById('m-tipo').value=v;
  document.getElementById('tipo-grid').querySelectorAll('.tipo-opt').forEach(function(o){o.classList.toggle('sel',o.dataset.tipo===v);});
  updateTipoExtras(v);
}

function updateTipoExtras(tipo){
  var el=document.getElementById('tipo-extra-fields'); if(!el) return;
  var html='';
  if(tipo==='rango'||tipo==='numero'){
    var cur=_modalData.rule;
    var label1=tipo==='rango'?'Valor mínimo':'Mínimo (opcional)';
    var label2=tipo==='rango'?'Valor máximo':'Máximo (opcional)';
    html='<div class="form-grid-2" style="margin-bottom:12px">'
      +'<div class="form-group"><label class="form-label">'+label1+'</label><input class="form-input" id="m-min" type="number" value="'+(cur.min!=null?cur.min:'')+'" placeholder="0"></div>'
      +'<div class="form-group"><label class="form-label">'+label2+'</label><input class="form-input" id="m-max" type="number" value="'+(cur.max!=null?cur.max:'')+'" placeholder="999"></div>'
      +'</div>';
  }
  el.innerHTML=html;
}

function toggleModalEnv(group,env){
  var wrap=document.getElementById('m-'+group); if(!wrap) return;
  var chip=wrap.querySelector('[data-env="'+env+'"]') && wrap.querySelector('[data-env="'+env+'"]').closest('.env-chip');
  if(!chip) return;
  var cb=chip.querySelector('input'); cb.checked=!cb.checked; var on=cb.checked;
  if(group==='opcional'){chip.style.borderColor=on?'var(--yellow)':'';chip.style.color=on?'var(--yellow)':'';chip.style.background=on?'var(--yellowLight)':'';chip.classList.toggle('checked',false);}
  else{chip.classList.toggle('checked',on);chip.style.borderColor='';chip.style.color='';chip.style.background='';}
  chip.querySelector('i').className='ti '+(on?'ti-check':'ti-x');
}

function toggleMaxHoy(){
  var cb=document.getElementById('m-maxhoy'); cb.checked=!cb.checked;
  var chip=document.getElementById('m-maxhoy-chip'); chip.querySelector('i').className='ti '+(cb.checked?'ti-check':'ti-x');
  if(cb.checked){chip.style.borderColor='var(--purple)';chip.style.color='var(--purple)';chip.style.background='var(--purpleLight)';}
  else{chip.style.borderColor='';chip.style.color='';chip.style.background='';}
}

function toggleUnificarNombres(){
  var cb=document.getElementById('m-unificar'); cb.checked=!cb.checked;
  var chip=document.getElementById('m-unificar-chip'); chip.querySelector('i').className='ti '+(cb.checked?'ti-check':'ti-x');
  if(cb.checked){chip.style.borderColor='#a78bfa';chip.style.color='#a78bfa';chip.style.background='rgba(139,92,246,.12)';}
  else{chip.style.borderColor='';chip.style.color='';chip.style.background='';}
  var wrap=document.getElementById('m-unificar-umbral-wrap');
  if(wrap) wrap.style.display=cb.checked?'flex':'none';
}

function toggleConsistente(){
  var cb=document.getElementById('m-consistente'); cb.checked=!cb.checked;
  var chip=document.getElementById('m-consistente-chip'); chip.querySelector('i').className='ti '+(cb.checked?'ti-check':'ti-x');
  if(cb.checked){chip.style.borderColor='#60a5fa';chip.style.color='#60a5fa';chip.style.background='rgba(59,130,246,.1)';}
  else{chip.style.borderColor='';chip.style.color='';chip.style.background='';}
  var wrap=document.getElementById('m-consistente-entornos-wrap');
  if(wrap) wrap.style.display=cb.checked?'block':'none';
}

function toggleDepFlag(which){
  if(which==='req'){
    var cb=document.getElementById('m-dep-req'); cb.checked=!cb.checked;
    var chip=document.getElementById('m-dep-req-chip'); chip.querySelector('i').className='ti '+(cb.checked?'ti-check':'ti-x');
    if(cb.checked){chip.style.borderColor='var(--blue)';chip.style.color='var(--blue)';chip.style.background='var(--blueLight)';}
    else{chip.style.borderColor='';chip.style.color='';chip.style.background='';}
  } else {
    var cb2=document.getElementById('m-dep-err'); cb2.checked=!cb2.checked;
    var chip2=document.getElementById('m-dep-err-chip'); chip2.querySelector('i').className='ti '+(cb2.checked?'ti-check':'ti-x');
    if(cb2.checked){chip2.style.borderColor='var(--red)';chip2.style.color='var(--red)';chip2.style.background='var(--redLight)';}
    else{chip2.style.borderColor='';chip2.style.color='';chip2.style.background='';}
  }
}

// ─── VAL CRUZADAS ────────────────────────────────────────────────────────────
function renderValCrModal(){
  var list=document.getElementById('m-valcr-list'); if(!list) return;
  if(!_modalValCr.length){ list.innerHTML='<div style="font-size:12px;color:var(--text3);margin-bottom:8px">Sin validaciones cruzadas.</div>'; return; }
  list.innerHTML=_modalValCr.map(function(vc,i){
    var condRows='';
    if(vc.condicion && Object.keys(vc.condicion).length){
      Object.keys(vc.condicion).forEach(function(campo,ci){
        var valores=(vc.condicion[campo]||[]).join(', ');
        var xf=(vc.condicionArchivos&&vc.condicionArchivos[campo])||{};
        var xfOpen=xf.archivoIndex!=null;
        condRows += '<div id="m-vc-cond-row-'+i+'-'+ci+'" style="margin-bottom:8px">'
          +'<div style="display:flex;gap:6px;align-items:center">'
          +'<input class="form-input" style="flex:1" placeholder="Nombre del campo" id="m-vc-cond-campo-'+i+'-'+ci+'" value="'+esc(campo)+'">'
          +'<span style="color:var(--text3);font-size:12px;white-space:nowrap">=</span>'
          +'<input class="form-input" style="flex:2" placeholder="valor1, valor2..." id="m-vc-cond-vals-'+i+'-'+ci+'" value="'+esc(valores)+'">'
          +'<button class="btn btn-ghost btn-sm" onclick="toggleCondXFile('+i+','+ci+')" title="Referencia a otro archivo" style="'+(xfOpen?'color:var(--blue);background:var(--blueLight);border-color:var(--blue)':'')+'">'
          +'<i class="ti ti-files" aria-hidden="true"></i></button>'
          +'<button class="btn btn-danger btn-sm" onclick="removeCondRow('+i+','+ci+')" title="Eliminar"><i class="ti ti-x" aria-hidden="true"></i></button></div>'
          +'<div id="m-vc-cond-xfile-'+i+'-'+ci+'" style="display:'+(xfOpen?'flex':'none')+';gap:6px;margin-top:4px;padding:6px;background:var(--bg4);border:1px solid var(--border);border-radius:var(--radius)">'
          +'<div class="form-group" style="flex:1;margin:0"><label class="form-label" style="margin-bottom:3px">Índice archivo</label>'
          +'<input class="form-input" id="m-vc-cond-xarch-'+i+'-'+ci+'" type="number" min="0" max="9" value="'+(xfOpen?xf.archivoIndex:'')+'" placeholder="0=primero"></div>'
          +'<div class="form-group" style="flex:2;margin:0"><label class="form-label" style="margin-bottom:3px">Campo de unión</label>'
          +'<input class="form-input" id="m-vc-cond-xkey-'+i+'-'+ci+'" value="'+esc(xf.claveUnion||'')+'" placeholder="Ficha_fic"></div>'
          +'</div></div>';
      });
    } else {
      condRows = '<div id="m-vc-cond-row-'+i+'-0" style="margin-bottom:8px">'
        +'<div style="display:flex;gap:6px;align-items:center">'
        +'<input class="form-input" style="flex:1" placeholder="Nombre del campo" id="m-vc-cond-campo-'+i+'-0" value="">'
        +'<span style="color:var(--text3);font-size:12px;white-space:nowrap">=</span>'
        +'<input class="form-input" style="flex:2" placeholder="valor1, valor2..." id="m-vc-cond-vals-'+i+'-0" value="">'
        +'<button class="btn btn-ghost btn-sm" onclick="toggleCondXFile('+i+',0)" title="Referencia a otro archivo"><i class="ti ti-files" aria-hidden="true"></i></button>'
        +'<button class="btn btn-danger btn-sm" onclick="removeCondRow('+i+',0)"><i class="ti ti-x" aria-hidden="true"></i></button></div>'
        +'<div id="m-vc-cond-xfile-'+i+'-0" style="display:none;gap:6px;margin-top:4px;padding:6px;background:var(--bg4);border:1px solid var(--border);border-radius:var(--radius)">'
        +'<div class="form-group" style="flex:1;margin:0"><label class="form-label" style="margin-bottom:3px">Índice archivo</label>'
        +'<input class="form-input" id="m-vc-cond-xarch-'+i+'-0" type="number" min="0" max="9" placeholder="0=primero"></div>'
        +'<div class="form-group" style="flex:2;margin:0"><label class="form-label" style="margin-bottom:3px">Campo de unión</label>'
        +'<input class="form-input" id="m-vc-cond-xkey-'+i+'-0" placeholder="Ficha_fic"></div>'
        +'</div></div>';
    }
    return '<div class="vcr-item">'
      +'<div class="vcr-item-header"><span class="vcr-num">Regla '+(i+1)+'</span>'
      +'<button class="btn btn-danger btn-sm" onclick="removeValCr('+i+')"><i class="ti ti-trash" aria-hidden="true"></i> Eliminar</button></div>'
      +'<div style="margin-bottom:10px">'
      +'<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px">'
      +'<label class="form-label">Cuando estos campos tengan estos valores</label>'
      +'<button class="btn btn-ghost btn-sm" onclick="addCondRow('+i+')"><i class="ti ti-plus" aria-hidden="true"></i> Agregar campo</button></div>'
      +'<div style="background:var(--bg4);border:1px solid var(--border);border-radius:var(--radius);padding:10px" id="m-vc-cond-wrap-'+i+'">'+condRows+'</div>'
      +'<div style="font-size:11px;color:var(--text3);margin-top:4px">Lógica <b>Y</b> entre campos</div></div>'
      +'<div class="form-group" style="margin-bottom:8px">'
      +'<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px">'
      +'<label class="form-label" style="margin-bottom:0">Entonces solo puede tener estos valores</label>'
      +'<div style="position:relative" id="m-vc-lista-wrap-'+i+'">'
      +'<button class="btn btn-ghost btn-sm" type="button" onclick="toggleListaDropdown(event,'+i+')" style="font-size:11px"><i class="ti ti-database-import" aria-hidden="true"></i> Importar lista</button>'
      +'<div id="m-vc-lista-dropdown-'+i+'" style="display:none;position:absolute;right:0;top:100%;margin-top:4px;background:var(--bg3);border:1px solid var(--border2);border-radius:var(--radius);z-index:100;min-width:220px;box-shadow:0 4px 12px rgba(0,0,0,.3)"></div>'
      +'</div></div>'
      +'<div class="tag-input-wrap" id="m-vc-perm-wrap-'+i+'" onclick="document.getElementById(\'m-vc-perm-input-'+i+'\').focus()">'
      +'<span id="m-vc-perm-'+i+'-tags"></span>'
      +'<input type="text" class="tag-input" id="m-vc-perm-input-'+i+'" onkeydown="tagKeydown(event,\'m-vc-perm-'+i+'\')" placeholder="Escribe y presiona Enter...">'
      +'</div><input type="hidden" id="m-vc-perm-'+i+'-hidden"></div>'
      +'<div class="form-group" style="margin-bottom:8px"><label class="form-label" style="margin-bottom:6px;display:block">Debe contener</label>'
      +'<div class="tag-input-wrap" id="m-vc-cont-wrap-'+i+'" onclick="document.getElementById(\'m-vc-cont-input-'+i+'\').focus()">'
      +'<span id="m-vc-cont-'+i+'-tags"></span>'
      +'<input type="text" class="tag-input" id="m-vc-cont-input-'+i+'" onkeydown="tagKeydown(event,\'m-vc-cont-'+i+'\')" placeholder="Escribe y presiona Enter...">'
      +'</div><input type="hidden" id="m-vc-cont-'+i+'-hidden"></div>'
      +'<div class="form-group" style="margin-bottom:8px"><label class="form-label" style="margin-bottom:6px;display:block">No debe contener</label>'
      +'<div class="tag-input-wrap" id="m-vc-nocont-wrap-'+i+'" onclick="document.getElementById(\'m-vc-nocont-input-'+i+'\').focus()">'
      +'<span id="m-vc-nocont-'+i+'-tags"></span>'
      +'<input type="text" class="tag-input" id="m-vc-nocont-input-'+i+'" onkeydown="tagKeydown(event,\'m-vc-nocont-'+i+'\')" placeholder="Escribe y presiona Enter...">'
      +'</div><input type="hidden" id="m-vc-nocont-'+i+'-hidden"></div>'
      +'<div class="form-group"><label class="form-label">Mensaje de error</label>'
      +'<input class="form-input" id="m-vc-msg-'+i+'" value="'+esc(vc.mensaje||'')+'" placeholder="Ej: Este valor no es válido para el tipo seleccionado">'
      +'</div></div>';
  }).join('');

  _modalValCr.forEach(function(vc,i){
    initTagInput('m-vc-perm-'+i, vc.valoresPermitidos||[]);
    initTagInput('m-vc-cont-'+i, vc.contiene||[]);
    initTagInput('m-vc-nocont-'+i, vc.noContiene||[]);
  });
}

function toggleListaDropdown(event,vcIndex){
  event.stopPropagation();
  document.querySelectorAll('[id^="m-vc-lista-dropdown-"]').forEach(function(d){if(d.id!=='m-vc-lista-dropdown-'+vcIndex) d.style.display='none';});
  var dd=document.getElementById('m-vc-lista-dropdown-'+vcIndex); if(!dd) return;
  var todasListas={};
  if(_listasGlobales) Object.assign(todasListas,_listasGlobales);
  if(STATE.current && STATE.current._listas) Object.assign(todasListas,STATE.current._listas);
  var nombres=Object.keys(todasListas);
  if(!nombres.length){ dd.innerHTML='<div style="padding:10px 12px;font-size:12px;color:var(--text3)">No hay listas disponibles</div>'; }
  else {
    dd.innerHTML='<div style="padding:8px;min-width:220px">'
      +'<div style="font-size:10px;font-weight:600;color:var(--text3);text-transform:uppercase;letter-spacing:.06em;margin-bottom:6px;padding:0 4px">Selecciona una lista</div>'
      + nombres.map(function(nombre){
          var count=(todasListas[nombre]||[]).length;
          return '<button class="btn btn-ghost" type="button" onclick="abrirItemsLista(event,\''+esc(nombre)+'\','+vcIndex+')" style="width:100%;text-align:left;border-radius:var(--radius);border:none;padding:7px 10px;font-size:12px;display:flex;align-items:center;justify-content:space-between;gap:8px;margin-bottom:2px">'
            +'<span><i class="ti ti-list" style="margin-right:6px;opacity:.5" aria-hidden="true"></i>'+esc(nombre)+'</span>'
            +'<span style="font-size:10px;color:var(--text3);background:var(--bg4);padding:2px 6px;border-radius:10px;flex-shrink:0">'+count+'</span></button>';
        }).join('')+'</div>';
  }
  dd.style.display=dd.style.display==='none'?'block':'none';
}

function abrirItemsLista(event,nombreLista,vcIndex){
  event.stopPropagation();
  var todasListas={};
  if(_listasGlobales) Object.assign(todasListas,_listasGlobales);
  if(STATE.current && STATE.current._listas) Object.assign(todasListas,STATE.current._listas);
  var items=todasListas[nombreLista]||[];
  var dd=document.getElementById('m-vc-lista-dropdown-'+vcIndex); if(!dd) return;
  var tagId='m-vc-perm-'+vcIndex; var yaSeleccionados=_tagData[tagId]||[];
  dd.innerHTML='<div style="padding:8px;min-width:260px">'
    +'<div style="display:flex;align-items:center;gap:6px;margin-bottom:8px">'
    +'<button class="btn btn-ghost btn-sm" type="button" onclick="toggleListaDropdown(event,'+vcIndex+')" style="padding:4px 8px"><i class="ti ti-arrow-left" aria-hidden="true"></i></button>'
    +'<span style="font-size:12px;font-weight:600;color:var(--text);flex:1">'+esc(nombreLista)+'</span>'
    +'<button class="btn btn-primary btn-sm" type="button" onclick="confirmarItemsLista(event,\''+esc(nombreLista)+'\','+vcIndex+')" style="font-size:11px"><i class="ti ti-check" aria-hidden="true"></i> Agregar</button></div>'
    +'<div style="display:flex;gap:4px;margin-bottom:6px">'
    +'<button class="btn btn-ghost btn-sm" type="button" onclick="selectAllItems(event,'+vcIndex+')" style="font-size:11px;flex:1">Todos</button>'
    +'<button class="btn btn-ghost btn-sm" type="button" onclick="deselectAllItems(event,'+vcIndex+')" style="font-size:11px;flex:1">Ninguno</button></div>'
    +'<div style="max-height:220px;overflow-y:auto;display:flex;flex-direction:column;gap:2px" id="m-vc-items-list-'+vcIndex+'">'
    + items.map(function(item){
        var checked=yaSeleccionados.indexOf(item)!==-1;
        return '<label onclick="event.stopPropagation()" style="display:flex;align-items:center;gap:8px;padding:5px 8px;border-radius:var(--radius);cursor:pointer;font-size:12px;color:'+(checked?'var(--purple)':'var(--text2)')+';background:'+(checked?'var(--purpleLight)':'')+'">'
          +'<input type="checkbox" data-item="'+esc(item)+'" '+(checked?'checked':'')+' style="cursor:pointer;accent-color:var(--purple)">'
          +'<span>'+esc(item)+'</span></label>';
      }).join('')+'</div></div>';
  dd.style.display='block';
}

function selectAllItems(event,vcIndex){
  event.stopPropagation();
  var dd=document.getElementById('m-vc-lista-dropdown-'+vcIndex); if(!dd) return;
  dd.querySelectorAll('input[data-item]').forEach(function(cb){cb.checked=true;cb.closest('label').style.background='var(--purpleLight)';cb.closest('label').style.color='var(--purple)';});
}

function deselectAllItems(event,vcIndex){
  event.stopPropagation();
  var dd=document.getElementById('m-vc-lista-dropdown-'+vcIndex); if(!dd) return;
  dd.querySelectorAll('input[data-item]').forEach(function(cb){cb.checked=false;cb.closest('label').style.background='';cb.closest('label').style.color='var(--text2)';});
}

function confirmarItemsLista(event,nombreLista,vcIndex){
  event.stopPropagation();
  var dd=document.getElementById('m-vc-lista-dropdown-'+vcIndex); if(!dd) return;
  var tagId='m-vc-perm-'+vcIndex; if(!_tagData[tagId]) _tagData[tagId]=[];
  var agregados=0;
  dd.querySelectorAll('input[data-item]:checked').forEach(function(cb){
    var item=cb.dataset.item;
    if(_tagData[tagId].indexOf(item)===-1){_tagData[tagId].push(item);agregados++;}
  });
  renderTags(tagId); dd.style.display='none';
  toast(agregados>0?agregados+' valor'+(agregados>1?'es':'')+' agregado'+(agregados>1?'s':''):'No se agregaron valores nuevos', agregados>0?'s':'w');
}

function addCondRow(vcIndex){
  var wrap=document.getElementById('m-vc-cond-wrap-'+vcIndex); if(!wrap) return;
  var rows=wrap.querySelectorAll('[id^="m-vc-cond-row-'+vcIndex+'-"]');
  var newIndex=rows.length;
  var div=document.createElement('div');
  div.id='m-vc-cond-row-'+vcIndex+'-'+newIndex;
  div.style.cssText='margin-bottom:8px';
  div.innerHTML='<div style="display:flex;gap:6px;align-items:center">'
    +'<input class="form-input" style="flex:1" placeholder="Nombre del campo" id="m-vc-cond-campo-'+vcIndex+'-'+newIndex+'">'
    +'<span style="color:var(--text3);font-size:12px;white-space:nowrap">=</span>'
    +'<input class="form-input" style="flex:2" placeholder="valor1, valor2..." id="m-vc-cond-vals-'+vcIndex+'-'+newIndex+'">'
    +'<button class="btn btn-ghost btn-sm" onclick="toggleCondXFile('+vcIndex+','+newIndex+')" title="Referencia a otro archivo"><i class="ti ti-files" aria-hidden="true"></i></button>'
    +'<button class="btn btn-danger btn-sm" onclick="removeCondRow('+vcIndex+','+newIndex+')" title="Eliminar"><i class="ti ti-x" aria-hidden="true"></i></button></div>'
    +'<div id="m-vc-cond-xfile-'+vcIndex+'-'+newIndex+'" style="display:none;gap:6px;margin-top:4px;padding:6px;background:var(--bg4);border:1px solid var(--border);border-radius:var(--radius)">'
    +'<div class="form-group" style="flex:1;margin:0"><label class="form-label" style="margin-bottom:3px">Índice archivo</label>'
    +'<input class="form-input" id="m-vc-cond-xarch-'+vcIndex+'-'+newIndex+'" type="number" min="0" max="9" placeholder="0=primero"></div>'
    +'<div class="form-group" style="flex:2;margin:0"><label class="form-label" style="margin-bottom:3px">Campo de unión</label>'
    +'<input class="form-input" id="m-vc-cond-xkey-'+vcIndex+'-'+newIndex+'" placeholder="Ficha_fic"></div>'
    +'</div>';
  wrap.appendChild(div);
}

function toggleCondXFile(vcIndex,rowIndex){
  var xfile=document.getElementById('m-vc-cond-xfile-'+vcIndex+'-'+rowIndex); if(!xfile) return;
  var isOpen=xfile.style.display!=='none';
  xfile.style.display=isOpen?'none':'flex';
  var row=document.getElementById('m-vc-cond-row-'+vcIndex+'-'+rowIndex); if(!row) return;
  var btn=row.querySelector('button[onclick*="toggleCondXFile"]'); if(!btn) return;
  if(!isOpen){btn.style.color='var(--blue)';btn.style.background='var(--blueLight)';btn.style.borderColor='var(--blue)';}
  else{btn.style.color='';btn.style.background='';btn.style.borderColor='';}
}

function removeCondRow(vcIndex,rowIndex){ var row=document.getElementById('m-vc-cond-row-'+vcIndex+'-'+rowIndex); if(row) row.remove(); }
function addValCrModal(){ _modalValCr.push({condicion:{},valoresPermitidos:[],noPermitidos:[],mensaje:''}); renderValCrModal(); }
function removeValCr(i){ _modalValCr.splice(i,1); renderValCrModal(); }

// ─── MODAL SAVE ──────────────────────────────────────────────────────────────
function modalSave(){
  var campo=(document.getElementById('m-campo').value||'').trim();
  if(!campo){ toast('El nombre del campo es obligatorio','e'); return; }
  if(_modalData.mode==='add' && STATE.current.rules[campo]){ toast('Ya existe una regla con ese campo','e'); return; }

  var tipo=document.getElementById('m-tipo').value;
  var rule={tipo:tipo, desc:document.getElementById('m-desc').value.trim()};

  var ob=[];
  document.getElementById('m-obligatorio').querySelectorAll('input:checked').forEach(function(cb){ob.push(cb.dataset.env);});
  if(ob.length) rule.obligatorio=ob;

  var op=[];
  document.getElementById('m-opcional').querySelectorAll('input:checked').forEach(function(cb){op.push(cb.dataset.env);});
  if(op.length) rule.opcional=op;

  var regex=(document.getElementById('m-regex').value||'').trim();
  if(regex) rule.regex=regex;

  var minEl=document.getElementById('m-min'); var maxEl=document.getElementById('m-max');
  if(minEl && minEl.value!=='') rule.min=parseInt(minEl.value);
  if(maxEl && maxEl.value!=='') rule.max=parseInt(maxEl.value);

  var lonMinEl=document.getElementById('m-lonmin'); var lonMaxEl=document.getElementById('m-lonmax');
  if(lonMinEl && lonMinEl.value!=='') rule.longitudMin=parseInt(lonMinEl.value);
  if(lonMaxEl && lonMaxEl.value!=='') rule.longitudMax=parseInt(lonMaxEl.value);

  if(document.getElementById('m-maxhoy').checked) rule.maxHoy=true;

  // unificarNombres
  var unificarEl=document.getElementById('m-unificar');
  if(unificarEl && unificarEl.checked){
    rule.unificarNombres=true;
    var umbralEl2=document.getElementById('m-unificar-umbral');
    var umbralVal=umbralEl2?parseInt(umbralEl2.value):90;
    rule.similaridadUmbral=(!isNaN(umbralVal)&&umbralVal>=50&&umbralVal<=99)?umbralVal:90;
    var unificarEnvs=[];
    var unificarEnvWrap=document.getElementById('m-unificar-entornos');
    if(unificarEnvWrap) unificarEnvWrap.querySelectorAll('input:checked').forEach(function(cb){unificarEnvs.push(cb.dataset.env);});
    if(unificarEnvs.length) rule.unificarNombresEntornos=unificarEnvs;
  }

  // consistentePorDocumento
  var consistenteEl=document.getElementById('m-consistente');
  if(consistenteEl && consistenteEl.checked){
    rule.consistentePorDocumento=true;
    var consistenteEnvs=[];
    var consistenteEnvWrap=document.getElementById('m-consistente-entornos');
    if(consistenteEnvWrap) consistenteEnvWrap.querySelectorAll('input:checked').forEach(function(cb){consistenteEnvs.push(cb.dataset.env);});
    if(consistenteEnvs.length) rule.consistentePorDocumentoEntornos=consistenteEnvs;
  }

  // repetible — usar nombre diferente para evitar colisión con maxEl
  if(document.getElementById('m-repetible').checked){
    rule.repetible=true;
    var repetibleMaxEl=document.getElementById('m-repetible-max');
    var maxVal=repetibleMaxEl?parseInt(repetibleMaxEl.value):0;
    if(maxVal>0) rule.repetibleMax=maxVal;
  }

  var vArr=_tagData['m-valores']||[];
  if(vArr.length===1 && vArr[0].startsWith('$')) rule.valores=vArr[0];
  else if(vArr.length) rule.valores=vArr;

  var depCampo=(document.getElementById('m-dep-campo').value||'').trim();
  if(depCampo){
    var depVals=_tagData['m-dep-valores']||[];
    var depEnvs=[];
    document.getElementById('m-dep-entornos').querySelectorAll('input:checked').forEach(function(cb){depEnvs.push(cb.dataset.env);});
    rule.dependencia={campo:depCampo, valores:depVals};
    var depMsg=(document.getElementById('m-dep-mensaje').value||'').trim();
    if(depMsg) rule.dependencia.mensaje=depMsg;
    if(depEnvs.length) rule.dependencia.entornos=depEnvs;
    if(document.getElementById('m-dep-req').checked) rule.dependencia.requeridoCuandoCumple=true;
    if(document.getElementById('m-dep-err').checked) rule.dependencia.errorSiNoCumpleTieneValor=true;
    var depArchivoIndexVal=(document.getElementById('m-dep-archivo-index').value||'').trim();
    if(depArchivoIndexVal!=='') rule.dependencia.archivoIndex=parseInt(depArchivoIndexVal);
    var depClaveUnionVal=(document.getElementById('m-dep-clave-union').value||'').trim();
    if(depClaveUnionVal) rule.dependencia.claveUnion=depClaveUnionVal;
  }

  // dependencias por posición
  var depPos=[];
  _depPosiciones.forEach(function(dp,pi){
    var campoEl=document.getElementById('m-dep-pos-campo-'+pi);
    var msgEl=document.getElementById('m-dep-pos-msg-'+pi);
    var reqEl=document.getElementById('m-dep-pos-req-'+pi);
    var errEl=document.getElementById('m-dep-pos-err-'+pi);
    if(!campoEl || !campoEl.value.trim()) return;
    depPos.push({
      campo:campoEl.value.trim(),
      valores:_tagData['m-dep-pos-vals-'+pi]||[],
      mensaje:msgEl?msgEl.value.trim():'',
      requeridoCuandoCumple:reqEl?reqEl.checked:true,
      errorSiNoCumpleTieneValor:errEl?errEl.checked:false
    });
  });
  if(depPos.length) rule.dependenciasPorPosicion=depPos;

  // dependencias adicionales (múltiples, cada una puede ser de otro archivo)
  var depMult=[];
  _depMultiples.forEach(function(dm,mi){
    var campoEl=document.getElementById('m-depm-campo-'+mi);
    if(!campoEl || !campoEl.value.trim()) return;
    var item={campo:campoEl.value.trim(), valores:_tagData['m-depm-vals-'+mi]||[]};
    var msgEl=document.getElementById('m-depm-msg-'+mi);
    if(msgEl && msgEl.value.trim()) item.mensaje=msgEl.value.trim();
    var envs=[];
    var envWrap=document.getElementById('m-depm-entornos-'+mi);
    if(envWrap) envWrap.querySelectorAll('input:checked').forEach(function(cb){envs.push(cb.dataset.env);});
    if(envs.length) item.entornos=envs;
    var reqEl=document.getElementById('m-depm-req-'+mi);
    if(reqEl && reqEl.checked) item.requeridoCuandoCumple=true;
    var errEl=document.getElementById('m-depm-err-'+mi);
    if(errEl && errEl.checked) item.errorSiNoCumpleTieneValor=true;
    var archivoEl=document.getElementById('m-depm-archivo-'+mi);
    var archivoVal=archivoEl?(archivoEl.value||'').trim():'';
    if(archivoVal!=='') item.archivoIndex=parseInt(archivoVal);
    var claveEl=document.getElementById('m-depm-clave-'+mi);
    if(claveEl && claveEl.value.trim()) item.claveUnion=claveEl.value.trim();
    depMult.push(item);
  });
  if(depMult.length) rule.dependenciasMultiples=depMult;

  // validaciones cruzadas
  var vcList=[];
  _modalValCr.forEach(function(vc,i){
    var msgEl=document.getElementById('m-vc-msg-'+i);
    var condObj={};
    var condArch={};
    var wrap=document.getElementById('m-vc-cond-wrap-'+i);
    if(wrap){
      wrap.querySelectorAll('[id^="m-vc-cond-row-'+i+'-"]').forEach(function(row){
        var rowId=row.id.replace('m-vc-cond-row-'+i+'-','');
        var campoEl=document.getElementById('m-vc-cond-campo-'+i+'-'+rowId);
        var valsEl=document.getElementById('m-vc-cond-vals-'+i+'-'+rowId);
        if(campoEl && valsEl && campoEl.value.trim()){
          var vals=valsEl.value.split(',').map(function(v){return v.trim();}).filter(Boolean);
          if(vals.length) condObj[campoEl.value.trim()]=vals;
          var xArchEl=document.getElementById('m-vc-cond-xarch-'+i+'-'+rowId);
          var xKeyEl=document.getElementById('m-vc-cond-xkey-'+i+'-'+rowId);
          if(xArchEl && xArchEl.value.trim()!==''){
            condArch[campoEl.value.trim()]={
              archivoIndex:parseInt(xArchEl.value),
              claveUnion:(xKeyEl&&xKeyEl.value.trim())||'Ficha_fic'
            };
          }
        }
      });
    }
    var perm=_tagData['m-vc-perm-'+i]||[];
    var cont=_tagData['m-vc-cont-'+i]||[];
    var nocont=_tagData['m-vc-nocont-'+i]||[];
    var entry={};
    if(Object.keys(condObj).length) entry.condicion=condObj;
    if(Object.keys(condArch).length) entry.condicionArchivos=condArch;
    if(perm.length) entry.valoresPermitidos=perm;
    if(cont.length) entry.contiene=cont;
    if(nocont.length) entry.noContiene=nocont;
    if(msgEl) entry.mensaje=msgEl.value.trim();
    if(Object.keys(entry).length) vcList.push(entry);
  });
  if(vcList.length) rule.validacionesCruzadas=vcList;

  // avanzado
  var excCampo=(document.getElementById('m-exc-campo').value||'').trim();
  if(excCampo) rule.exceptoSi={campo:excCampo, valor:(document.getElementById('m-exc-valor').value||'').trim()};
  var cmpCampo=(document.getElementById('m-cmp-campo').value||'').trim();
  var cmpOp=document.getElementById('m-cmp-op').value;
  if(cmpCampo && cmpOp) rule.comparar={campoRef:cmpCampo, operacion:cmpOp, mensaje:(document.getElementById('m-cmp-msg').value||'').trim()};
  var eeaArchivo=(document.getElementById('m-eea-archivo').value||'').trim();
  var eeaCampo=(document.getElementById('m-eea-campo').value||'').trim();
  if(eeaArchivo!==''&&eeaCampo) rule.existeEnArchivo={
    archivoIndex:parseInt(eeaArchivo),
    campo:eeaCampo,
    mensaje:(document.getElementById('m-eea-mensaje').value||'').trim()
  };

  // Siempre borrar clave anterior al editar para evitar duplicados
  if(_modalData.mode==='edit') delete STATE.current.rules[_modalData.origKey];
  STATE.current.rules[campo]=rule;

  closeModal();
  renderEditor();
  toast(_modalData.mode==='add'?'Regla agregada':'Regla actualizada','s');
}

// ─── GESTIÓN DE LISTAS ─────────────────────────────────────────────────────
var _listasWorking = {};
function openListasManager(){
  // Crear copia de trabajo combinando globales y las del validador actual
  _listasWorking = {};
  if(_listasGlobales) Object.assign(_listasWorking, JSON.parse(JSON.stringify(_listasGlobales)));
  if(STATE.current && STATE.current._listas) Object.assign(_listasWorking, JSON.parse(JSON.stringify(STATE.current._listas)));
  renderListasManager();
  document.getElementById('listas-modal-bg').style.display='flex';
}
function closeListasModal(){ document.getElementById('listas-modal-bg').style.display='none'; }
function renderListasManager(){
  var body=document.getElementById('listas-modal-body'); if(!body) return;
  var nombres=Object.keys(_listasWorking||{});
  if(!nombres.length){ body.innerHTML = '<div style="padding:12px;color:var(--text3)">No hay listas definidas.</div>'; return; }
  var html = '<div style="display:flex;flex-direction:column;gap:8px">';
  nombres.forEach(function(nombre){
    var count = (_listasWorking[nombre]||[]).length;
    html += '<div style="display:flex;align-items:center;justify-content:space-between;gap:8px;padding:8px;border-radius:8px;background:var(--bg3);border:1px solid var(--border)">'
      +'<div style="font-size:13px;font-weight:600">'+esc(nombre)+'</div>'
      +'<div style="display:flex;gap:8px;align-items:center">'
        +'<div style="font-size:12px;color:var(--text3);background:var(--bg4);padding:4px 8px;border-radius:10px">'+count+'</div>'
        +'<button class="btn btn-ghost btn-sm" onclick="openEditLista(\''+esc(nombre)+'\')">Editar</button>'
        +'<button class="btn btn-danger btn-sm" onclick="deleteLista(\''+esc(nombre)+'\')">Eliminar</button>'
      +'</div></div>';
  });
  html += '<div style="display:flex;gap:8px;margin-top:8px"><input class="form-input" id="new-list-name" placeholder="Nombre nueva lista"><button class="btn btn-primary" onclick="createNewLista()">Crear lista</button></div>';
  html += '</div>';
  body.innerHTML = html;
}
function openEditLista(nombre){
  var items = (_listasWorking[nombre]||[]).slice();
  var body=document.getElementById('listas-modal-body'); if(!body) return;
  var html = '<div style="display:flex;flex-direction:column;gap:10px">';
  html += '<div style="display:flex;align-items:center;justify-content:space-between">'
    +'<div style="font-size:14px;font-weight:700">Editar lista: '+esc(nombre)+'</div>'
    +'<div></div>'
  +'</div>';
  html += '<div class="tag-input-wrap" onclick="document.getElementById(\'listas-edit-input\').focus()">'
    +'<span id="listas-edit-tags"></span>'
    +'<input id="listas-edit-input" class="tag-input" placeholder="Agregar y presionar Enter" onkeydown="listasEditKeydown(event,\''+esc(nombre)+'\')">'
  +'</div>';
  html += '<div style="font-size:12px;color:var(--text3)">Arrastra o pega valores separados por comas.</div>';
  html += '<div style="display:flex;gap:8px;justify-content:flex-end">'
    +'<button class="btn btn-ghost" onclick="renderListasManager()">Volver</button>'
    +'<button class="btn btn-primary" onclick="saveListaEdits(\''+esc(nombre)+'\')">Guardar</button>'
  +'</div>';
  html += '</div>';
  body.innerHTML = html;
  _tagData['listas-edit-'+nombre]=items;
  renderListaEditTags(nombre);
}
function renderListaEditTags(nombre){
  var id='listas-edit-'+nombre; var wrap=document.getElementById('listas-edit-tags'); if(!wrap) return;
  var arr=_tagData[id]||[]; wrap.innerHTML = arr.map(function(v,i){return '<span class="tag">'+esc(v)+'<span class="tag-del" onclick="listasRemoveTag(\''+nombre+'\','+i+')">×</span></span>';}).join('');
  var hidden=document.getElementById('listas-edit-hidden'); if(hidden) hidden.value=JSON.stringify(arr);
}
function listasEditKeydown(e,nombre){ if(e.key==='Enter'||e.key===','||e.key===';'){ e.preventDefault(); var v=e.target.value.trim().replace(/,|;/g,''); if(!v) return; var id='listas-edit-'+nombre; if(!_tagData[id]) _tagData[id]=[]; _tagData[id].push(v); e.target.value=''; renderListaEditTags(nombre);} }
function listasRemoveTag(nombre,i){ var id='listas-edit-'+nombre; if(!_tagData[id]) return; _tagData[id].splice(i,1); renderListaEditTags(nombre); }
function saveListaEdits(nombre){ var id='listas-edit-'+nombre; var items=_tagData[id]||[]; _listasWorking[nombre]=items; toast('Lista guardada en memoria','s'); renderListasManager(); }
function createNewLista(){ var name=document.getElementById('new-list-name').value.trim(); if(!name){ toast('Nombre vacío','w'); return; } if(_listasWorking[name]){ toast('Ya existe','w'); return; } _listasWorking[name]=[]; document.getElementById('new-list-name').value=''; renderListasManager(); openEditLista(name); }
function deleteLista(nombre){ if(!confirm('Eliminar lista "'+nombre+'"?')) return; delete _listasWorking[nombre]; toast('Lista eliminada (en memoria)','w'); renderListasManager(); }
function applyListasChanges(){ if(!STATE.current) STATE.current={}; STATE.current._listas = JSON.parse(JSON.stringify(_listasWorking)); closeListasModal(); toast('Listas aplicadas al validador (en memoria). Guarda para persistir.','s'); }
function downloadListas(){ var payload = { _listas: Object.assign({}, _listasGlobales||{}, (STATE.current&&STATE.current._listas)||{}) }; var blob=new Blob([JSON.stringify(payload,null,2)],{type:'application/json'}); var a=document.createElement('a'); a.href=URL.createObjectURL(blob); a.download='listas.json'; a.click(); }

function saveListasServer(){
  var payload = { _listas: Object.assign({}, _listasGlobales||{}, _listasWorking||{}, (STATE.current&&STATE.current._listas)||{}) };
  var token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
  showSpinner('Guardando listas en servidor...');
  fetch('/validator-builder/save-listas',{
    method:'POST',
    headers: {'Content-Type':'application/json','X-CSRF-TOKEN': token},
    body: JSON.stringify(payload)
  }).then(function(r){ return r.json().then(function(d){ return {status:r.status,data:d}; }); })
  .then(function(res){ hideSpinner(); if(res.data && res.data.success){ toast('Listas guardadas en servidor','s'); _listasGlobales = payload._listas; } else { toast(res.data.message||'Error guardando listas','e'); } })
  .catch(function(err){ hideSpinner(); console.error(err); toast('Error de conexión','e'); });
}


// ─── SAVE VALIDATOR ──────────────────────────────────────────────────────────
function saveValidator(){
  if(!STATE.current){ toast('Nada que guardar','w'); return; }
  // Sanitizar rules siempre al inicio
  if(Array.isArray(STATE.current.rules)){
    var rulesObj = {};
    STATE.current.rules.forEach(function(r){
      if(r && r.campo) rulesObj[r.campo] = r;
    });
    STATE.current.rules = rulesObj;
  }
  if(!STATE.current.rules || typeof STATE.current.rules !== 'object'){
    STATE.current.rules = {};
  }
  STATE.current.id=document.getElementById('f-id').value.trim();
  STATE.current.nombre=document.getElementById('f-nombre').value.trim();
  STATE.current.code=document.getElementById('f-code').value.trim();
  STATE.current.desc=document.getElementById('f-desc').value.trim();
  var ob=[];
  document.querySelectorAll('#entornos-global input[data-env]:checked').forEach(function(cb){ob.push(cb.dataset.env);});
  STATE.current.entornos=ob;

  var csrfToken=document.querySelector('meta[name="csrf-token"]');
  var token=csrfToken?csrfToken.getAttribute('content'):'';
  // Si ya existe archivo cargado, usar ese nombre siempre
  // Si es nuevo, usar el id que escribió el usuario
  var filenameToUse = STATE.currentFileName || STATE.current.id;
  // Asegurar que rules sea objeto y no array antes de enviar
  var rulesParaEnviar = STATE.current.rules;
  if(Array.isArray(rulesParaEnviar)){
      // Convertir array a objeto usando el campo como key
      var rulesObj = {};
      rulesParaEnviar.forEach(function(r){
          if(r && r.campo) rulesObj[r.campo] = r;
      });
      rulesParaEnviar = rulesObj;
  }
  var payload = Object.assign({}, STATE.current, {
      filename: filenameToUse,
      rules: rulesParaEnviar
  });

  console.log('=== DEBUG SAVE ===');
  console.log('STATE.currentFileName:', STATE.currentFileName);
  console.log('STATE.current.id:', STATE.current.id);
  console.log('filenameToUse:', filenameToUse);
  console.log('payload.filename:', payload.filename);

  showSpinner('Guardando...');
  console.log('rules antes de enviar:', JSON.stringify(STATE.current.rules));
console.log('cantidad de reglas:', Object.keys(STATE.current.rules).length);
  fetch('/validator-builder/save',{
    method:'POST',
    headers:{'Content-Type':'application/json','X-CSRF-TOKEN':token},
    body:JSON.stringify(payload)
  })
  .then(function(r){return r.json().then(function(data){return {status:r.status,data:data};});})
  .then(function(result){
    hideSpinner();
    if(result.data.success){
      toast('Guardado correctamente','s');
      var savedFile = (result.data.filename || filenameToUse).toLowerCase().replace('.json','');
      STATE.currentFileName = savedFile;
      var existing=STATE.files.find(function(f){return f.id===savedFile;});
      if(existing){ existing.name=STATE.current.nombre||savedFile; }
      else if(STATE.current.id){ STATE.files.push({id:savedFile,name:STATE.current.nombre||savedFile}); }
      renderSidebar();
    } else { toast(result.data.message||'Error al guardar','e'); }
  })
  .catch(function(err){ hideSpinner(); console.error(err); toast('Sin conexión','e'); });
}

function exportJson(){
  if(!STATE.current){ toast('Nada que exportar','w'); return; }
  STATE.current.id=document.getElementById('f-id').value.trim();
  STATE.current.nombre=document.getElementById('f-nombre').value.trim();
  STATE.current.code=document.getElementById('f-code').value.trim();
  STATE.current.desc=document.getElementById('f-desc').value.trim();
  var blob=new Blob([JSON.stringify(STATE.current,null,2)],{type:'application/json'});
  var a=document.createElement('a'); a.href=URL.createObjectURL(blob);
  a.download=(STATE.current.id||'validador')+'.json'; a.click();
}

// ─── INIT ─────────────────────────────────────────────────────────────────
(function init(){
  var serverFiles={!! json_encode($files) !!};
  STATE.files=serverFiles;
  renderSidebar();
  // Intentar cargar listas desde el archivo público
  fetch('/js/validators/listas.json')
    .then(function(r){ if(!r.ok) throw new Error('No encontrado'); return r.json(); })
    .then(function(data){ _listasGlobales = data._listas || data._listas || {}; })
    .catch(function(err){ console.warn('No se pudo cargar /js/validators/listas.json:', err); });

  if(STATE.files.length>0) loadFile(STATE.files[0].id);
})();
</script>
</body>
</html>