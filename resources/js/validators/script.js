// ============================
// STATE
// ============================
let selectedEntorno = null;
let selectedFormatId = null;
let loadedFiles = [];
let allIssues = [];
let allResults = [];
let lastParsedFiles = [];
let currentFilter = 'all';
let editingFormatId = null;

// Builtin formats loaded from rules.json at startup
let BUILTIN_FORMATS = [];
let LISTAS = {};

// ============================
// CONSTANTS
// ============================
const ENTORNO_CODES = { laboral: '2', educativo: '3', comunitario: '4', institucional: '6' };
const ENTORNO_NAMES = { laboral: 'Laboral', educativo: 'Educativo', comunitario: 'Comunitario', institucional: 'Institucional' };
const ENTORNO_ICONS = { laboral: '🏭', educativo: '🎓', comunitario: '🏘️', institucional: '🏥' };
const ALL_ENTORNOS = ['laboral', 'educativo', 'comunitario', 'institucional'];
// Token alert threshold (clientside)
const TOKEN_ALERT_THRESHOLD = 50;

function checkTokensThreshold(threshold = TOKEN_ALERT_THRESHOLD) {
    try {
        if (typeof userTokens === 'undefined') return;
        const alertEl = document.getElementById('token-alert');
        if (!alertEl) return;
        if (userTokens <= 0) {
            alertEl.innerHTML = `<div class="alert-danger">No tienes tokens disponibles. <a href="/mp/checkout">Comprar tokens</a></div>`;
            alertEl.style.display = 'block';
            return;
        }
        if (userTokens < threshold) {
            alertEl.innerHTML = `<div class="alert-warn">Quedan <strong>${userTokens}</strong> tokens. <a href="/mp/checkout">Recargar ahora</a></div>`;
            alertEl.style.display = 'block';
        } else {
            alertEl.style.display = 'none';
        }
    } catch (e) { console.warn('checkTokensThreshold error', e); }
}

// ============================
// LOAD rules.json
// ============================
async function loadRulesJSON() {
    try {
        const url = '/api/rules-json';
        const res = await fetch(url, {
            method: 'GET',
            cache: 'no-store',
            headers: { 'Pragma': 'no-cache', 'Cache-Control': 'no-cache' }
        });
        if (!res.ok) throw new Error('Error al obtener las reglas desde el servidor');
        const data = await res.json();
        LISTAS = data._listas || {};
        BUILTIN_FORMATS = (data.formats || []).map(fmt => ({
            ...fmt,
            builtin: true,
            rules: resolveRuleValues(fmt.rules)
        }));
        console.log('Reglas cargadas en tiempo real sin caché.');
    } catch (err) {
        console.error(err);
        BUILTIN_FORMATS = [];
    }
}

async function reloadRules() {
    await loadRulesJSON(true);
    renderFmtList();
    alert('Reglas actualizadas');
}

function resolveRuleValues(rules) {
    const out = {};
    for (const [campo, rule] of Object.entries(rules)) {
        const r = { ...rule };
        if (typeof r.valores === 'string' && r.valores.startsWith('$')) {
            const key = r.valores.slice(1);
            r.valores = LISTAS[key] || [];
        }
        if (r.dependencia) {
            r.dependencia = { ...r.dependencia };
            if (r.dependencia.valor && !r.dependencia.valores) {
                r.dependencia.valores = [r.dependencia.valor];
            }
        }
        out[campo] = r;
    }
    return out;
}

// ============================
// FORMAT REGISTRY
// ============================
function loadUserFormats() {
    try { return JSON.parse(localStorage.getItem('mb_user_formats') || '[]'); } catch { return []; }
}
function saveUserFormats(formats) {
    localStorage.setItem('mb_user_formats', JSON.stringify(formats));
}
function loadBuiltinOverrides() {
    try { return JSON.parse(localStorage.getItem('mb_builtin_overrides') || '{}'); } catch { return {}; }
}
function saveBuiltinOverrides(overrides) {
    localStorage.setItem('mb_builtin_overrides', JSON.stringify(overrides));
}

function getAllFormats() {
    const overrides = loadBuiltinOverrides();
    const builtins = BUILTIN_FORMATS.map(f => {
        if (overrides[f.id]) return { ...f, ...overrides[f.id], builtin: true, id: f.id };
        return f;
    });
    return [...builtins, ...loadUserFormats()];
}

function getFormatsForEntorno(entorno) {
    return getAllFormats().filter(f => f.entornos.includes(entorno));
}
function getFormatById(id) {
    return getAllFormats().find(f => f.id === id) || null;
}

// ============================
// UI — STEP 1: ENTORNO
// ============================
function selectEntorno(btn) {
    document.querySelectorAll('.entorno-btn').forEach(b => b.classList.remove('selected'));
    btn.classList.add('selected');
    selectedEntorno = btn.dataset.entorno;
    selectedFormatId = null;
    document.getElementById('step1').classList.add('done');
    document.getElementById('step2').classList.add('active');
    document.getElementById('panel-formato').classList.remove('section-hidden');
    document.getElementById('panel-files').classList.add('section-hidden');
    document.getElementById('results-section').style.display = 'none';
    renderFormatoGrid();
    document.getElementById('formato-panel-title').textContent =
        `Paso 2 — Seleccione el formato para entorno ${ENTORNO_NAMES[selectedEntorno]}`;
}

function renderFormatoGrid() {
    const grid = document.getElementById('formato-grid');
    const formats = getFormatsForEntorno(selectedEntorno);
    if (formats.length === 0) {
        grid.innerHTML = `<div class="empty-state"><span class="empty-icon">📋</span>No hay formatos para este entorno.<br>Cree uno nuevo con el botón de abajo.</div>`;
        return;
    }
    grid.innerHTML = formats.map(f => `
    <button class="formato-btn ${f.id === selectedFormatId ? 'selected' : ''}" data-fmt="${f.id}" onclick="selectFormato(this)">
      <div class="fmt-code">${f.code}</div>
      <div class="fmt-name">${f.nombre}</div>
      <div class="fmt-desc">${f.desc || ''}</div>
      <div class="fmt-entornos">${f.entornos.map(e => ENTORNO_ICONS[e]).join(' ')}</div>
      ${!f.builtin ? '<div class="fmt-custom-badge">Personalizado</div>' : '<div class="fmt-builtin-badge">Builtin</div>'}
    </button>
  `).join('');
}

function selectFormato(btn) {
    document.querySelectorAll('.formato-btn').forEach(b => b.classList.remove('selected'));
    btn.classList.add('selected');
    selectedFormatId = btn.dataset.fmt;
    const fmt = getFormatById(selectedFormatId);
    document.getElementById('step2').classList.add('done');
    document.getElementById('step3').classList.add('active');
    document.getElementById('panel-files').classList.remove('section-hidden');
    document.getElementById('files-panel-title').textContent =
        `Paso 3 — Cargue los archivos del formato ${fmt.nombre}`;
    document.getElementById('selected-badge').textContent =
        `Entorno: ${ENTORNO_NAMES[selectedEntorno]} · Formato: ${fmt.nombre} (${fmt.code})`;
    document.getElementById('btn-validate').disabled = loadedFiles.length === 0;
}

// ============================
// UI — FILE HANDLING
// ============================
function handleFiles(files) {
    for (const f of files) {
        const ext = f.name.split('.').pop().toLowerCase();
        if (!['xlsx', 'csv'].includes(ext)) {
            alert(`El archivo ${f.name} no es válido. Use .xlsx o .csv`);
            continue;
        }
        if (loadedFiles.some(x => x.name === f.name)) continue;
        loadedFiles.push(f);
    }
    renderFileList();
}

function renderFileList() {
    const list = document.getElementById('file-list');
    list.innerHTML = '';
    for (const f of loadedFiles) {
        const item = document.createElement('div');
        item.className = 'file-item';
        item.innerHTML = `
      <span class="file-icon">📄</span>
      <span class="file-name">${f.name}</span>
      <span class="file-size">${(f.size / 1024).toFixed(1)} KB</span>
      <button class="file-remove" onclick="removeFile('${f.name}')">✕</button>`;
        list.appendChild(item);
    }
    document.getElementById('btn-validate').disabled = loadedFiles.length === 0 || !selectedFormatId;
}

function removeFile(name) {
    loadedFiles = loadedFiles.filter(f => f.name !== name);
    renderFileList();
}

// Drag & drop
document.addEventListener('DOMContentLoaded', () => {
    const dropArea = document.getElementById('drop-area');
    if (dropArea) {
        dropArea.addEventListener('dragover', e => { e.preventDefault(); dropArea.classList.add('drag'); });
        dropArea.addEventListener('dragleave', () => dropArea.classList.remove('drag'));
        dropArea.addEventListener('drop', e => {
            e.preventDefault(); dropArea.classList.remove('drag');
            handleFiles(e.dataTransfer.files);
        });
    }
});

// ============================
// FILE READERS — con soporte de columnas duplicadas
// ============================

// Quita únicamente los puntos del inicio y del final del título de columna
// (no toca puntos intermedios, ej: "N° Trabajador." o ".N° Trabajador" -> "N° Trabajador")
function limpiarPuntosHeader(h) {
    return String(h || '')
        .trim()
        .replace(/^\.+/, '')
        .replace(/\.+$/, '')
        .trim();
}

// Normaliza SOLO para efectos de comparación (quita puntos del inicio/final,
// tildes y diferencias de mayúsculas/minúsculas). El resultado de esta función
// nunca se usa como nombre de columna real, solo para decidir si dos títulos
// se refieren al mismo campo.
function normalizarParaComparar(s) {
    return limpiarPuntosHeader(s)
        .normalize('NFD').replace(new RegExp('[̀-ͯ]', 'g'), '')
        .toLowerCase();
}

// Homologa el título leído del archivo contra los nombres de campo definidos
// en las reglas del formato: si coincide exactamente lo deja igual; si solo
// difiere en puntos del inicio/final, tildes o mayúsculas/minúsculas respecto
// a un campo de la regla, usa el nombre EXACTO de la regla (para que el resto
// del motor de validación, que compara por igualdad estricta, siga
// funcionando sin cambios y sin tocar el JSON de reglas).
function resolverHeaderCanonico(headerRaw, ruleFieldNames) {
    const raw = String(headerRaw || '').trim();
    if (!raw) return raw;
    if (!ruleFieldNames || ruleFieldNames.length === 0) return raw;
    if (ruleFieldNames.includes(raw)) return raw;

    const normRaw = normalizarParaComparar(raw);
    for (const campo of ruleFieldNames) {
        if (normalizarParaComparar(campo) === normRaw) return campo;
    }
    return raw;
}

// Recopila TODOS los nombres de columna que las reglas pueden referenciar:
// no solo el campo principal de cada regla, sino también los campos usados
// en dependencias, "excepto si", comparaciones de fecha, dependencias
// múltiples, validaciones cruzadas y "existe en archivo". Así, aunque una
// columna solo se use como referencia (no tenga su propia regla directa),
// también se homologa igual entre archivos con y sin puntos/tildes/mayúsculas.
function recopilarNombresCampoDeReglas(rules) {
    const nombres = new Set();
    for (const [campo, rule] of Object.entries(rules || {})) {
        nombres.add(campo);
        if (rule.dependencia && rule.dependencia.campo) nombres.add(rule.dependencia.campo);
        if (Array.isArray(rule.dependenciasPorPosicion)) {
            rule.dependenciasPorPosicion.forEach(d => { if (d && d.campo) nombres.add(d.campo); });
        }
        if (rule.exceptoSi && rule.exceptoSi.campo) nombres.add(rule.exceptoSi.campo);
        if (rule.comparar && rule.comparar.campoRef) nombres.add(rule.comparar.campoRef);
        if (Array.isArray(rule.dependenciasMultiples)) {
            rule.dependenciasMultiples.forEach(d => { if (d.campo) nombres.add(d.campo); });
        }
        if (Array.isArray(rule.validacionesCruzadas)) {
            rule.validacionesCruzadas.forEach(vc => {
                if (vc.condicion) Object.keys(vc.condicion).forEach(k => nombres.add(k));
            });
        }
        if (rule.existeEnArchivo && rule.existeEnArchivo.campo) nombres.add(rule.existeEnArchivo.campo);
    }
    return Array.from(nombres);
}

function readExcelFile(file, ruleFieldNames = []) {
    return new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.onload = (e) => {
            try {
                const data = new Uint8Array(e.target.result);
                const workbook = XLSX.read(data, { type: 'array' });
                const firstSheetName = workbook.SheetNames[0];
                const worksheet = workbook.Sheets[firstSheetName];

                // Leer como array de arrays para preservar columnas duplicadas
                const rawData = XLSX.utils.sheet_to_json(worksheet, {
                    range: 1,
                    defval: '',
                    header: 1
                });

                if (rawData.length < 2) return resolve({ headers: [], rows: [] });

                const headers = rawData[0].map(h => resolverHeaderCanonico(h, ruleFieldNames));

                // Detectar duplicados
                const conteoHeaders = {};
                const duplicados = [];
                headers.forEach(h => {
                    if (!h) return;
                    conteoHeaders[h] = (conteoHeaders[h] || 0) + 1;
                    if (conteoHeaders[h] === 2) duplicados.push(h);
                });

                if (duplicados.length > 0) {
                    console.log('Columnas duplicadas detectadas:', duplicados);
                }

                // Convertir a objetos renombrando duplicados con __2, __3, etc.
                const rows = rawData.slice(1).map(rowArr => {
                    const rowObj = {};
                    const conteo = {};
                    headers.forEach((h, idx) => {
                        if (!h) return;
                        conteo[h] = (conteo[h] || 0) + 1;
                        const key = conteo[h] === 1 ? h : h + '__' + conteo[h];
                        rowObj[key] = rowArr[idx] !== undefined ? rowArr[idx] : '';
                    });
                    return rowObj;
                });

                const headersOut = rows.length > 0 ? Object.keys(rows[0]) : [];
                resolve({ headers: headersOut, rows, duplicados });
            } catch (err) { reject(err); }
        };
        reader.onerror = reject;
        reader.readAsArrayBuffer(file);
    });
}

function readCSVFile(file, ruleFieldNames = []) {
    return new Promise((res, rej) => {
        const reader = new FileReader();
        reader.onload = e => {
            const ab = e.target.result;
            const decoder = new TextDecoder('windows-1252');
            const text = decoder.decode(ab).replace(/\r/g, '');
            const rawLines = text.split('\n');
            const lines = rawLines.filter(l => l.trim());
            if (lines.length < 2) return res({ headers: [], rows: [] });

            const possibleDelims = [';', ',', '\t'];
            const firstLineHasDelim = possibleDelims.some(d => lines[0].includes(d));
            const secondLineHasDelim = lines.length > 1 && possibleDelims.some(d => lines[1].includes(d));
            if (!firstLineHasDelim && secondLineHasDelim) {
                lines.shift();
            }

            const delimiter = possibleDelims.find(d => lines[0].includes(d)) || ';';
            const headers = lines[0].split(delimiter).map(h => resolverHeaderCanonico(String(h || '').replace(/^\uFEFF/, ''), ruleFieldNames));

            // Detectar duplicados
            const conteoHeaders = {};
            headers.forEach(h => {
                if (!h) return;
                conteoHeaders[h] = (conteoHeaders[h] || 0) + 1;
            });
            const duplicados = Object.keys(conteoHeaders).filter(h => conteoHeaders[h] > 1);

            // Construir filas renombrando duplicados
            const rows = [];
            for (let i = 1; i < lines.length; i++) {
                const vals = lines[i].split(delimiter);
                const rowObj = {};
                const conteo = {};
                headers.forEach((h, j) => {
                    if (!h) return;
                    conteo[h] = (conteo[h] || 0) + 1;
                    const key = conteo[h] === 1 ? h : h + '__' + conteo[h];
                    rowObj[key] = (vals[j] || '').trim();
                });
                rows.push(rowObj);
            }

            const headersOut = rows.length > 0 ? Object.keys(rows[0]) : [];
            res({ headers: headersOut, rows, duplicados });
        };
        reader.onerror = rej;
        reader.readAsArrayBuffer(file);
    });
}

// ============================
// VALIDATION ENGINE
// ============================
function normalizeCode(val) {
    if (val == null || val === '') return '';
    return String(val)
        .replace(/\|/g, '')
        .replace(/\uFEFF/g, '')
        .replace(/\s*-\s*/g, ' - ')
        .replace(/\s+/g, ' ')
        .trim()
        .toLowerCase();
}

function isBlank(val) {
    return val == null || String(val).trim() === '' || String(val).trim().toLowerCase() === 'nan';
}

function excelDateToJSDate(serial) {
    if (serial instanceof Date) return serial;
    if (serial === '' || serial === null || serial === undefined) return serial;

    let date;
    if (!isNaN(serial)) {
        // Serial numérico de Excel (días desde 1899-12-30)
        date = new Date(Math.round((Number(serial) - 25569) * 86400 * 1000));
    } else {
        // Fecha escrita como texto (ej. "2026-07-21")
        date = new Date(serial);
        if (isNaN(date.getTime())) return serial;
    }

    // Ambas rutas se anclan igual para que la misma fecha calendario
    // produzca siempre el mismo timestamp, venga como número o como texto.
    date.setMinutes(date.getMinutes() + date.getTimezoneOffset());
    return date;
}

// Reduce un timestamp a su día calendario (sin horas), para que dos fechas
// que caen en el mismo día siempre comparen como iguales entre sí.
function truncarADia(ms) {
    const d = new Date(ms);
    return Date.UTC(d.getFullYear(), d.getMonth(), d.getDate());
}

// ============================
// SIMILITUD DE CADENAS (Levenshtein)
// ============================
function levenshtein(a, b) {
    let prev = Array.from({ length: b.length + 1 }, (_, i) => i);
    for (let i = 1; i <= a.length; i++) {
        const curr = [i];
        for (let j = 1; j <= b.length; j++) {
            curr[j] = a[i - 1] === b[j - 1]
                ? prev[j - 1]
                : 1 + Math.min(prev[j], curr[j - 1], prev[j - 1]);
        }
        prev = curr;
    }
    return prev[b.length];
}

function stringSimilarity(a, b) {
    if (a === b) return 1;
    const maxLen = Math.max(a.length, b.length);
    if (maxLen === 0) return 1;
    return 1 - levenshtein(a, b) / maxLen;
}

// Detecta si dos nombres son variantes numeradas de la misma sede.
// Cubre dos casos:
//   1) Uno es prefijo del otro + sufijo numérico: "Mafe" vs "Mafe 2"
//   2) Ambos tienen sufijo numérico con la misma base: "Col. 1" vs "Col. 2"
function sonVariantesNumericas(a, b) {
    const aL = a.toLowerCase().trim();
    const bL = b.toLowerCase().trim();

    // Patrón de sufijo numérico: dígitos, romanos o palabras clave de sede
    const SUFIJO = /^[\s\-]+([\d]+|[ivxlcdmIVXLCDM]+|(sede|campus|nodo|punto)\s*\d*)$/i;

    // Caso 1: el más corto es prefijo exacto del más largo
    const [shorter, longer] = aL.length <= bL.length ? [aL, bL] : [bL, aL];
    if (longer.startsWith(shorter)) {
        const sufijo = longer.slice(shorter.length);
        if (SUFIJO.test(sufijo) || sufijo.trim() === '') return true;
    }

    // Caso 2: ambos terminan en sufijo numérico y comparten la misma base
    // "MIS PEQUEÑOS AMIGUITOS 1" vs "MIS PEQUEÑOS AMIGUITOS 2"
    const reSufijo = /^(.*?)[\s\-]+([\d]+|[ivxlcdmIVXLCDM]+|(sede|campus|nodo|punto)\s*\d*)$/i;
    const mA = aL.match(reSufijo);
    const mB = bL.match(reSufijo);
    if (mA && mB && mA[1].trim() === mB[1].trim()) return true;

    return false;
}

function checkDependencia(dep, row, columnasPresentes, crossFileMaps) {
    if (!dep) return false;

    // Dependencia en otro archivo
    if (dep.archivoIndex !== undefined && crossFileMaps) {
        const crossMap = crossFileMaps[dep.archivoIndex];
        if (!crossMap) return false;
        const joinKey = dep.claveUnion || 'Ficha_fic';
        const rowKeyVal = String(row[joinKey] ?? row['Ficha_fic'] ?? row['FICHA_FIC'] ?? '').trim();
        const crossRow = crossMap.get(rowKeyVal);
        if (!crossRow) return false;
        const refVal = normalizeCode(crossRow[dep.campo]);
        const expectedValues = dep.valores || (dep.valor ? [dep.valor] : []);
        return expectedValues.map(v => normalizeCode(v)).includes(refVal);
    }

    if (!columnasPresentes.includes(dep.campo)) return false;
    const refVal = normalizeCode(row[dep.campo]);
    const expectedValues = dep.valores || (dep.valor ? [dep.valor] : []);
    return expectedValues.map(v => normalizeCode(v)).includes(refVal);
}

// Quita sufijo __2, __3, etc. para mostrar al usuario
function limpiarNombreCampo(campo) {
    return campo.replace(/__\d+$/, '');
}

// Expande reglas para cubrir columnas duplicadas (campo__2, campo__3, etc.)
function expandirReglasDuplicadas(rules, columnasPresentes) {
    const expandidas = {};

    for (const [campo, rule] of Object.entries(rules)) {

        // Posición 1 — campo original
        const rule0 = JSON.parse(JSON.stringify(rule));
        rule0._posicion = 1;

        if (rule.dependenciasPorPosicion && rule.dependenciasPorPosicion.length > 0) {
            const dep0 = rule.dependenciasPorPosicion[0];
            if (dep0) {
                rule0.dependencia = {
                    campo: dep0.campo,
                    valores: dep0.valores || [],
                    mensaje: dep0.mensaje || '',
                    requeridoCuandoCumple: dep0.requeridoCuandoCumple !== false,
                    errorSiNoCumpleTieneValor: dep0.errorSiNoCumpleTieneValor === true
                };
            }
            delete rule0.dependenciasPorPosicion;
        }
        expandidas[campo] = rule0;

        // Posiciones 2, 3, etc. — solo si el campo es repetible o tiene dependenciasPorPosicion
        const esRepetible = rule.repetible || (rule.dependenciasPorPosicion && rule.dependenciasPorPosicion.length > 0);
        const maxOcurrencias = esRepetible ? (rule.repetibleMax > 0 ? rule.repetibleMax : 999) : 1;
        let idx = 2;
        while (idx <= maxOcurrencias) {
            const campoDup = campo + '__' + idx;
            if (columnasPresentes.includes(campoDup)) {
                const ruleDup = JSON.parse(JSON.stringify(rule));
                ruleDup._posicion = idx;

                if (rule.dependenciasPorPosicion && rule.dependenciasPorPosicion.length > 0) {
                    const posIdx = idx - 1;
                    const depPos = rule.dependenciasPorPosicion[posIdx];
                    if (depPos) {
                        ruleDup.dependencia = {
                            campo: depPos.campo,
                            valores: depPos.valores || [],
                            mensaje: depPos.mensaje || '',
                            requeridoCuandoCumple: depPos.requeridoCuandoCumple !== false,
                            errorSiNoCumpleTieneValor: depPos.errorSiNoCumpleTieneValor === true
                        };
                    }
                    delete ruleDup.dependenciasPorPosicion;
                }

                expandidas[campoDup] = ruleDup;
                idx++;
            } else {
                break;
            }
        }
    }

    return expandidas;
}

function validateRecord(row, rowIdx, fileName, entorno, rules, crossFileMaps, crossFileSets) {
    const issues = [];
    const columnasPresentes = Object.keys(row);
    const ahora = new Date();
    const ficha = row['Ficha_fic'] ?? row['FICHA_FIC'] ?? '';
    const usuarioExcel = row['Usuario'] ?? row['USUARIO'] ?? row['usuario'] ?? '';
    const HOY_LIMITE = new Date(ahora.getFullYear(), ahora.getMonth(), ahora.getDate(), 23, 59, 59, 999).getTime();

    // Expandir reglas para columnas duplicadas
    rules = expandirReglasDuplicadas(rules, columnasPresentes);

    for (const [campo, rule] of Object.entries(rules)) {
        if (!columnasPresentes.includes(campo)) continue;

        const val = row[campo];
        const normVal = normalizeCode(val);
        const campoMostrar = limpiarNombreCampo(campo);
        const desc = rule.desc || campoMostrar;
        const posicion = rule._posicion && rule._posicion > 1 ? rule._posicion : null;

        let isObligatorio = rule.obligatorio && rule.obligatorio.includes(entorno);

        // ===================================
        // DEPENDENCIA
        // ===================================
        if (rule.dependencia) {
            const dep = rule.dependencia;
            const depAplicaEnEntorno = !dep.entornos || dep.entornos.includes(entorno);

            if (depAplicaEnEntorno) {
                const condMet = checkDependencia(dep, row, columnasPresentes, crossFileMaps);
                const requerido = dep.requeridoCuandoCumple !== false;
                const errorSiNoCumple = dep.errorSiNoCumpleTieneValor === true;

                if (condMet && requerido && isBlank(val)) {
                    // Verificar exceptoSi antes de reportar el error
                    let eximir = false;
                    if (rule.exceptoSi) {
                        const { campo: refCampo, valor: refValor } = rule.exceptoSi;
                        if (columnasPresentes.includes(refCampo)) {
                            const refActual = row[refCampo];
                            eximir = refValor === '__nonempty__'
                                ? !isBlank(refActual)
                                : normalizeCode(refActual) === normalizeCode(refValor);
                        }
                    }
                    if (!eximir) {
                        issues.push({
                            severity: 'error',
                            campo: campoMostrar,
                            posicion,
                            valor: val,
                            fila: rowIdx,
                            archivo: fileName,
                            Ficha_fic: ficha,
                            usuarioExcel: usuarioExcel,
                            mensaje: dep.mensaje || `${desc} es obligatorio según la dependencia configurada.`
                        });
                    }
                }

                if (!condMet && errorSiNoCumple && !isBlank(val)) {
                    issues.push({
                        severity: 'error',
                        campo: campoMostrar,
                        posicion,
                        valor: val,
                        fila: rowIdx,
                        archivo: fileName,
                        Ficha_fic: ficha,
                        usuarioExcel: usuarioExcel,
                        mensaje: `${desc} no debe registrarse para este componente.`
                    });
                }

                if (!condMet || !requerido) {
                    isObligatorio = false;
                }
            }
        }

        // Excepción condicional (exceptoSi)
        if (isObligatorio && rule.exceptoSi) {
            const { campo: refCampo, valor: refValor } = rule.exceptoSi;
            if (columnasPresentes.includes(refCampo)) {
                const refActual = row[refCampo];
                const eximir = refValor === '__nonempty__'
                    ? !isBlank(refActual)
                    : normalizeCode(refActual) === normalizeCode(refValor);
                if (eximir) isObligatorio = false;
            }
        }

        // ============================
        // DEPENDENCIAS MÚLTIPLES
        // ============================
        if (rule.dependenciasMultiples && rule.dependenciasMultiples.length > 0) {
            let allMatch = true;
            for (const dep of rule.dependenciasMultiples) {
                if (!columnasPresentes.includes(dep.campo)) { allMatch = false; break; }
                const rowVal = normalizeCode(row[dep.campo]);
                const expected = (dep.valores || []).map(v => normalizeCode(v));
                if (!expected.includes(rowVal)) { allMatch = false; break; }
            }

            const whenTrue = rule.multiWhenTrue || 'required';
            const whenFalse = rule.multiWhenFalse || 'ignore';

            if (allMatch) {
                if (whenTrue === 'required' && isBlank(val)) {
                    issues.push({
                        severity: 'error',
                        campo: campoMostrar,
                        posicion,
                        valor: val,
                        fila: rowIdx,
                        archivo: fileName,
                        Ficha_fic: ficha,
                        usuarioExcel: usuarioExcel,
                        mensaje: rule.multiMensaje || `${desc} es obligatorio por dependencias múltiples.`
                    });
                }
            } else {
                if (whenFalse === 'error' && !isBlank(val)) {
                    issues.push({
                        severity: 'error',
                        campo: campoMostrar,
                        posicion,
                        valor: val,
                        fila: rowIdx,
                        archivo: fileName,
                        Ficha_fic: ficha,
                        usuarioExcel: usuarioExcel,
                        mensaje: `${desc} no debe registrarse porque no cumple las dependencias múltiples.`
                    });
                }
            }
        }

        // ============================
        // FORZAR VALOR FIJO POR ENTORNO
        // ============================
        if (rule.reglasPorEntorno && rule.reglasPorEntorno[entorno] && rule.reglasPorEntorno[entorno].valorFijo) {
            const esperado = String(rule.reglasPorEntorno[entorno].valorFijo).trim().toLowerCase();
            const actual = String(val || '').trim().toLowerCase();

            if (isBlank(val)) {
                issues.push({
                    severity: 'error',
                    campo: campoMostrar,
                    posicion,
                    valor: val,
                    fila: rowIdx,
                    archivo: fileName,
                    Ficha_fic: ficha,
                    usuarioExcel: usuarioExcel,
                    mensaje: rule.reglasPorEntorno[entorno].mensaje || `${desc} debe ser "${rule.reglasPorEntorno[entorno].valorFijo}".`
                });
            } else if (actual !== esperado) {
                issues.push({
                    severity: 'warning',
                    campo: campoMostrar,
                    posicion,
                    valor: val,
                    fila: rowIdx,
                    archivo: fileName,
                    Ficha_fic: ficha,
                    usuarioExcel: usuarioExcel,
                    mensaje: rule.reglasPorEntorno[entorno].mensaje || `${desc} debe ser "${rule.reglasPorEntorno[entorno].valorFijo}".`
                });
            }
            continue;
        }

        // Obligatoriedad
        if (isObligatorio && isBlank(val)) {
            let mensaje = `${desc} es obligatorio para este registro.`;
            if (rule.reglasPorEntorno && rule.reglasPorEntorno[entorno] && rule.reglasPorEntorno[entorno].valorFijo) {
                mensaje = `${desc} debe ser "${rule.reglasPorEntorno[entorno].valorFijo}".`;
            }
            issues.push({
                severity: 'error',
                campo: campoMostrar,
                posicion,
                valor: val,
                fila: rowIdx,
                archivo: fileName,
                Ficha_fic: ficha,
                usuarioExcel: usuarioExcel,
                mensaje
            });
            continue;
        }

        // ============================
        // REGLAS POR ENTORNO
        // ============================
        if (rule.reglasPorEntorno && rule.reglasPorEntorno[entorno]) {
            const reglaEntorno = rule.reglasPorEntorno[entorno];
            if (reglaEntorno.valorFijo) {
                const esperado = String(reglaEntorno.valorFijo).trim().toLowerCase();
                const actual = String(val || '').trim().toLowerCase();
                if (actual !== esperado) {
                    issues.push({
                        severity: 'warning',
                        campo: campoMostrar,
                        posicion,
                        valor: val,
                        fila: rowIdx,
                        archivo: fileName,
                        Ficha_fic: ficha,
                        usuarioExcel: usuarioExcel,
                        mensaje: reglaEntorno.mensaje || `${desc} debe ser "${reglaEntorno.valorFijo}".`
                    });
                }
            }
        }

        // Validaciones de contenido
        if (!isBlank(val)) {

            if (rule.regex) {
                const rx = new RegExp(rule.regex);
                if (!rx.test(String(val).trim())) {
                    issues.push({
                        severity: 'warning',
                        campo: campoMostrar,
                        posicion,
                        valor: val,
                        fila: rowIdx,
                        archivo: fileName,
                        Ficha_fic: ficha,
                        usuarioExcel: usuarioExcel,
                        mensaje: rule.dependencia?.mensaje || `${desc} no tiene un formato válido para Colombia.`
                    });
                }
            }

            // Longitud de caracteres (mínimo / máximo)
            if (rule.longitudMin != null || rule.longitudMax != null) {
                const len = String(val).trim().length;
                if (rule.longitudMin != null && len < rule.longitudMin) {
                    issues.push({ severity: 'warning', campo: campoMostrar, posicion, valor: val, fila: rowIdx, archivo: fileName, Ficha_fic: ficha, mensaje: `${desc} debe tener al menos ${rule.longitudMin} caracteres.`, usuarioExcel });
                } else if (rule.longitudMax != null && len > rule.longitudMax) {
                    issues.push({ severity: 'warning', campo: campoMostrar, posicion, valor: val, fila: rowIdx, archivo: fileName, Ficha_fic: ficha, mensaje: `${desc} debe tener máximo ${rule.longitudMax} caracteres.`, usuarioExcel });
                }
            }

            // Fechas
            if (rule.tipo && rule.tipo.toString().includes('fecha')) {
                const fechaObjeto = excelDateToJSDate(val);
                const fechaMs = fechaObjeto instanceof Date ? fechaObjeto.getTime() : new Date(fechaObjeto).getTime();
                if (isNaN(fechaMs)) {
                    issues.push({ severity: 'warning', campo: campoMostrar, posicion, valor: val, fila: rowIdx, archivo: fileName, Ficha_fic: ficha, mensaje: `${desc} no es una fecha válida.` });
                } else {
                    if (rule.maxHoy && fechaMs > HOY_LIMITE) {
                        const fechaLegible = fechaObjeto instanceof Date ? fechaObjeto.toLocaleDateString('es-CO') : val;
                        issues.push({ severity: 'warning', campo: campoMostrar, posicion, valor: val, fila: rowIdx, archivo: fileName, Ficha_fic: ficha, mensaje: `${desc} (${fechaLegible}) no puede ser una fecha futura.` });
                    }
                    if (rule.comparar) {
                        const { campoRef, operacion, mensaje } = rule.comparar;
                        const valRef = row[campoRef];
                        if (!isBlank(valRef)) {
                            const fechaRefObj = excelDateToJSDate(valRef);
                            const fechaRefMs = fechaRefObj instanceof Date ? fechaRefObj.getTime() : new Date(fechaRefObj).getTime();
                            if (!isNaN(fechaRefMs)) {
                                // Comparar solo por día calendario (ignorando hora) para que
                                // la misma fecha nunca se marque como inconsistente entre sí.
                                const diaVal = truncarADia(fechaMs);
                                const diaRef = truncarADia(fechaRefMs);
                                const inconsistente =
                                    (operacion === '>=' && diaVal < diaRef) ||
                                    (operacion === '<=' && diaVal > diaRef) ||
                                    (operacion === '>' && diaVal <= diaRef) ||
                                    (operacion === '<' && diaVal >= diaRef);
                                if (inconsistente) issues.push({ severity: 'warning', campo: campoMostrar, posicion, valor: val, fila: rowIdx, archivo: fileName, Ficha_fic: ficha, mensaje, usuarioExcel });
                            }
                        }
                    }
                }
            }

            // Excepciones por entorno
            if (rule.excepcionPorEntorno && rule.excepcionPorEntorno[entorno]) {
                const exc = rule.excepcionPorEntorno[entorno];
                if (exc.prohibido && normVal === normalizeCode(exc.prohibido)) {
                    issues.push({ severity: 'warning', campo: campoMostrar, posicion, valor: val, fila: rowIdx, archivo: fileName, Ficha_fic: ficha, mensaje: exc.mensaje || `El valor "${val}" no es válido aquí.`, usuarioExcel });
                }
            }

            // Lista de valores
            if (rule.valores && !rule.valores.map(v => normalizeCode(v)).includes(normVal)) {
                issues.push({ severity: 'warning', campo: campoMostrar, posicion, valor: val, fila: rowIdx, archivo: fileName, Ficha_fic: ficha, mensaje: `${desc} incorrecto. Se esperaba: ${rule.valores.join(', ')}`, usuarioExcel });
            }

            // Numérico y rango
            if (rule.tipo === 'numero' || rule.tipo === 'rango') {
                const num = Number(val);
                if (isNaN(num)) {
                    issues.push({ severity: 'warning', campo: campoMostrar, posicion, valor: val, fila: rowIdx, archivo: fileName, Ficha_fic: ficha, mensaje: `${desc} debe ser un número`, usuarioExcel });
                } else if (rule.tipo === 'rango' && (num < rule.min || num > rule.max)) {
                    issues.push({ severity: 'warning', campo: campoMostrar, posicion, valor: val, fila: rowIdx, archivo: fileName, Ficha_fic: ficha, mensaje: `${desc} debe estar entre ${rule.min} y ${rule.max}`, usuarioExcel });
                }
            }

            // Texto no vacío
            if (rule.tipo === 'texto_nonempty' && String(val).trim().length === 0) {
                issues.push({ severity: 'error', campo: campoMostrar, posicion, valor: val, fila: rowIdx, archivo: fileName, Ficha_fic: ficha, mensaje: `${desc} no puede estar vacío.`, usuarioExcel });
            }

            // Alfanumérico en mayúsculas (solo A-Z y 0-9, sin espacios ni minúsculas)
            if (rule.tipo === 'alfanumerico_mayus' && !/^[A-Z0-9]+$/.test(String(val).trim())) {
                issues.push({ severity: 'warning', campo: campoMostrar, posicion, valor: val, fila: rowIdx, archivo: fileName, Ficha_fic: ficha, mensaje: `${desc} debe contener solo letras y números en mayúscula, sin espacios.`, usuarioExcel });
            }
        }

        // ============================
        // VALIDACIONES CRUZADAS
        // ============================
        if (rule.validacionesCruzadas && rule.validacionesCruzadas.length > 0) {
            for (const vCruzada of rule.validacionesCruzadas) {
                let cumpleCondicion = true;

                for (const [fld, vals] of Object.entries(vCruzada.condicion)) {
                    let valorEnFila;
                    const xfileRef = vCruzada.condicionArchivos && vCruzada.condicionArchivos[fld];
                    if (xfileRef && xfileRef.archivoIndex !== undefined && crossFileMaps) {
                        const crossMap = crossFileMaps[xfileRef.archivoIndex];
                        if (!crossMap) { cumpleCondicion = false; break; }
                        const joinKey = xfileRef.claveUnion || 'Ficha_fic';
                        const rowKeyVal = String(row[joinKey] ?? row['Ficha_fic'] ?? row['FICHA_FIC'] ?? '').trim();
                        const crossRow = crossMap.get(rowKeyVal);
                        if (!crossRow) { cumpleCondicion = false; break; }
                        valorEnFila = normalizeCode(crossRow[fld] || '');
                    } else {
                        valorEnFila = normalizeCode(row[fld] || "");
                    }
                    let cumpleCriterioFld = false;

                    for (const v of (vals || [])) {
                        const vStr = String(v);

                        if (vStr.startsWith('!')) {
                            const valorANegar = normalizeCode(vStr.substring(1));
                            if (valorEnFila !== valorANegar) { cumpleCriterioFld = true; break; }
                        }

                        if (vStr.startsWith('*') && vStr.endsWith('*')) {
                            const textoBuscar = vStr.slice(1, -1).toLowerCase().trim();
                            if (String(valorEnFila).toLowerCase().trim().includes(textoBuscar)) { cumpleCriterioFld = true; break; }
                        }

                        if (vStr.includes('-')) {
                            const [min, max] = vStr.split('-').map(Number);
                            const numFila = Number(valorEnFila);
                            if (!isNaN(numFila) && numFila >= min && numFila <= max) { cumpleCriterioFld = true; break; }
                        } else if (normalizeCode(vStr) === valorEnFila) {
                            cumpleCriterioFld = true; break;
                        }
                    }

                    if (!cumpleCriterioFld) { cumpleCondicion = false; break; }
                }

                if (cumpleCondicion) {
                    const permitidos = (vCruzada.valoresPermitidos || []).map(v => normalizeCode(v));
                    const contiene = (vCruzada.contiene || []).map(v => String(v).toLowerCase());
                    const valorTexto = String(val || '').toLowerCase().trim();

                    let tieneValorNoPermitido = false;
                    if (vCruzada.noPermitidos) {
                        tieneValorNoPermitido = vCruzada.noPermitidos.some(v =>
                            valorTexto === String(v).toLowerCase().trim()
                        );
                    }

                    const contieneTexto = contiene.some(txt => valorTexto.includes(String(txt).toLowerCase().trim()));

                    const noContiene = (vCruzada.noContiene || []).map(v => String(v).toLowerCase().trim());
                    const contieneTextoProhibido = noContiene.length > 0 && noContiene.some(txt => valorTexto.includes(txt));

                    const tieneValorValido = permitidos.some(p => normVal.includes(p));
                    const hayRestricciones = permitidos.length > 0 || contiene.length > 0;
                    // Solo evaluar restricciones positivas cuando el campo tiene valor;
                    // si está vacío, `obligatorio` ya maneja la presencia por entorno.
                    const noCumplePositivos = hayRestricciones && !isBlank(val) && !tieneValorValido && !contieneTexto;

                    if (noCumplePositivos || tieneValorNoPermitido || contieneTextoProhibido) {
                        issues.push({
                            severity: 'warning',
                            campo: campoMostrar,
                            posicion,
                            valor: val,
                            fila: rowIdx,
                            archivo: fileName,
                            Ficha_fic: ficha,
                            usuarioExcel,
                            mensaje: vCruzada.mensaje || `${desc} no es válido para las condiciones dadas.`
                        });
                    }
                }
            }
        }

        // ============================
        // EXISTENCIA EN OTRO ARCHIVO
        // ============================
        if (rule.existeEnArchivo && !isBlank(val) && crossFileSets) {
            const ref = rule.existeEnArchivo;
            const targetSets = crossFileSets[ref.archivoIndex];
            if (targetSets) {
                // Si no se especifica campo destino, usa el mismo nombre del campo actual
                const targetField = ref.campo || campo;
                const targetSet = targetSets[targetField];
                if (targetSet && !targetSet.has(normVal)) {
                    issues.push({
                        severity: 'error',
                        campo: campoMostrar,
                        posicion,
                        valor: val,
                        fila: rowIdx,
                        archivo: fileName,
                        Ficha_fic: ficha,
                        usuarioExcel,
                        mensaje: ref.mensaje || `${desc}: el valor "${val}" no existe en el archivo ${ref.archivoIndex + 1}.`
                    });
                }
            }
        }
    }

    return issues;
}

// ============================
// VALIDACIÓN CRUZADA ENTRE FILAS — UNIFICAR NOMBRES
// ============================
function validateUnificarNombres(rows, fileName, rules, entorno) {
    const issues = [];

    for (const [campo, rule] of Object.entries(rules)) {
        if (!rule.unificarNombres) continue;

        // Si tiene entornos configurados, solo aplica en esos entornos
        if (rule.unificarNombresEntornos && rule.unificarNombresEntornos.length > 0
            && !rule.unificarNombresEntornos.includes(entorno)) continue;

        const umbral = (rule.similaridadUmbral ?? 90) / 100;
        const desc = rule.desc || campo;
        const campoMostrar = limpiarNombreCampo(campo);

        // Recolectar todas las entradas no vacías con su fila y row original
        const entradas = rows
            .map((row, i) => ({ valor: String(row[campo] ?? '').trim(), fila: i + 3, row }))
            .filter(e => !isBlank(e.valor));

        if (entradas.length === 0) continue;

        // Agrupar por valor normalizado → { valor original, lista de entradas }
        const unicosMap = new Map();
        for (const entrada of entradas) {
            const key = normalizeCode(entrada.valor);
            if (!unicosMap.has(key)) unicosMap.set(key, { valor: entrada.valor, entradas: [] });
            unicosMap.get(key).entradas.push(entrada);
        }

        const unicos = [...unicosMap.values()];
        if (unicos.length <= 1) continue; // Todos idénticos → sin problema

        // Comparar cada par de valores únicos
        // filasConError: fila → { entrada, otroValor, pct } — guardamos solo la coincidencia más alta
        const filasConError = new Map();

        for (let i = 0; i < unicos.length; i++) {
            for (let j = i + 1; j < unicos.length; j++) {
                const sim = stringSimilarity(
                    unicos[i].valor.toLowerCase(),
                    unicos[j].valor.toLowerCase()
                );
                if (sim < umbral) continue;
                // Si la diferencia es solo un sufijo numérico (sede 2, sede 3…) → no es typo
                if (sonVariantesNumericas(unicos[i].valor, unicos[j].valor)) continue;

                const pct = Math.round(sim * 100);

                for (const e of unicos[i].entradas) {
                    const prev = filasConError.get(e.fila);
                    if (!prev || prev.pct < pct)
                        filasConError.set(e.fila, { entrada: e, otroValor: unicos[j].valor, pct });
                }
                for (const e of unicos[j].entradas) {
                    const prev = filasConError.get(e.fila);
                    if (!prev || prev.pct < pct)
                        filasConError.set(e.fila, { entrada: e, otroValor: unicos[i].valor, pct });
                }
            }
        }

        for (const { entrada, otroValor, pct } of filasConError.values()) {
            const ficha = entrada.row['Ficha_fic'] ?? entrada.row['FICHA_FIC'] ?? '';
            const usuario = entrada.row['Usuario'] ?? entrada.row['USUARIO'] ?? entrada.row['usuario'] ?? '';
            issues.push({
                severity: 'similitud',
                campo: campoMostrar,
                posicion: null,
                valor: entrada.valor,
                fila: entrada.fila,
                archivo: fileName,
                Ficha_fic: ficha,
                usuarioExcel: usuario,
                mensaje: `${desc}: "${entrada.valor}" es ${pct}% similar a "${otroValor}". Unifique el nombre de la institución.`
            });
        }
    }

    return issues;
}

// ============================
// VALIDACIÓN DE CONSISTENCIA DE CAMPOS POR DOCUMENTO
// ============================
function validateConsistenciaPorDocumento(rows, fileName, rules, entorno) {
    const issues = [];
    const CAMPO_CLAVE = '.Número de Documento.';

    for (const [campo, rule] of Object.entries(rules)) {
        if (!rule.consistentePorDocumento) continue;

        // Si tiene entornos configurados, solo aplica en esos entornos
        if (rule.consistentePorDocumentoEntornos && rule.consistentePorDocumentoEntornos.length > 0
            && !rule.consistentePorDocumentoEntornos.includes(entorno)) continue;

        const desc = rule.desc || campo;
        const campoMostrar = limpiarNombreCampo(campo);

        // Primera pasada: registrar el primer valor encontrado por documento
        const primerValorPorDoc = new Map();
        rows.forEach((row, i) => {
            const doc = String(row[CAMPO_CLAVE] || '').trim();
            if (!doc) return;
            const val = String(row[campo] || '').trim();
            if (!val) return;
            if (!primerValorPorDoc.has(doc)) {
                primerValorPorDoc.set(doc, { val, fila: i + 3 });
            }
        });

        // Segunda pasada: detectar filas donde el valor difiere del primero
        rows.forEach((row, i) => {
            const doc = String(row[CAMPO_CLAVE] || '').trim();
            if (!doc) return;
            const val = String(row[campo] || '').trim();
            if (!val) return;
            const referencia = primerValorPorDoc.get(doc);
            if (!referencia || val === referencia.val) return;

            const ficha = row['Ficha_fic'] ?? row['FICHA_FIC'] ?? '';
            const usuario = row['Usuario'] ?? row['USUARIO'] ?? row['usuario'] ?? '';
            issues.push({
                severity: 'warning',
                campo: campoMostrar,
                posicion: null,
                valor: val,
                fila: i + 3,
                archivo: fileName,
                Ficha_fic: ficha,
                usuarioExcel: usuario,
                mensaje: `${desc}: La persona con documento "${doc}" tiene aquí "${val}" pero en la fila ${referencia.fila} aparece como "${referencia.val}". Deben ser idénticos en todos los registros.`
            });
        });
    }

    return issues;
}

console.log(userTokens)

// ============================
// MAIN VALIDATION
// ============================
async function runValidation() {
    if (!selectedEntorno || !selectedFormatId || loadedFiles.length === 0) return;

    if (userTokens <= 0) {
        alert('No tienes tokens disponibles para realizar esta acción. Por favor, adquiere más tokens para continuar.');
        return;
    }

    const fmt = getFormatById(selectedFormatId);
    if (!fmt) return alert('Formato no encontrado');

    const btn = document.getElementById('btn-validate');
    btn.disabled = true;
    btn.classList.add('loading');
    document.getElementById('btn-icon').innerHTML = '<div class="spinner"></div>';
    document.getElementById('btn-text').textContent = 'PROCESANDO...';

    allIssues = [];
    allResults = [];
    let totalTokensConsumidos = 0;

    // Leer todos los archivos primero para permitir dependencias entre archivos
    const ruleFieldNames = recopilarNombresCampoDeReglas(fmt.rules);
    const parsedFiles = [];
    for (const file of loadedFiles) {
        try {
            const ext = file.name.split('.').pop().toLowerCase();
            const parsed = ext === 'csv' ? await readCSVFile(file, ruleFieldNames) : await readExcelFile(file, ruleFieldNames);
            parsedFiles.push({ file, ...parsed });
        } catch (err) {
            allIssues.push({ severity: 'error', campo: 'Archivo', posicion: null, valor: file.name, mensaje: `Error al leer: ${err.message}`, fila: 0, archivo: file.name });
            parsedFiles.push({ file, headers: [], rows: [] });
        }
    }

    lastParsedFiles = parsedFiles;

    // Construir índices por clave de unión para dependencias entre archivos
    // crossFileMaps[i] = Map(claveUnion → row) del archivo i
    const JOIN_KEYS = ['Ficha_fic', 'FICHA_FIC', 'ficha_fic'];
    const crossFileMaps = parsedFiles.map(pf => {
        const map = new Map();
        for (const row of pf.rows) {
            const keyVal = String(JOIN_KEYS.reduce((v, k) => v !== '' ? v : (row[k] ?? ''), '')).trim();
            if (keyVal) map.set(keyVal, row);
        }
        return map;
    });

    // crossFileSets[i][campo] = Set de valores normalizados — para existeEnArchivo
    const crossFileSets = parsedFiles.map(pf => {
        const sets = {};
        for (const row of pf.rows) {
            for (const [col, val] of Object.entries(row)) {
                if (!sets[col]) sets[col] = new Set();
                const norm = normalizeCode(val);
                if (norm) sets[col].add(norm);
            }
        }
        return sets;
    });

    for (let fileIdx = 0; fileIdx < parsedFiles.length; fileIdx++) {
        const { file, rows, headers } = parsedFiles[fileIdx];
        try {
            let fileIssues = 0;

            if (rows.length === 0) {
                allIssues.push({ severity: 'error', campo: 'Archivo', posicion: null, valor: file.name, mensaje: 'Archivo vacío o sin datos desde la fila 2', fila: 0, archivo: file.name });
                continue;
            }

            const tokensNecesarios = Math.ceil(rows.length / 500) * 5;

            if (userTokens < tokensNecesarios) {
                allIssues.push({
                    severity: 'error',
                    campo: 'Tokens',
                    posicion: null,
                    valor: file.name,
                    mensaje: `Tokens insuficientes. Requeridos: ${tokensNecesarios}, Disponibles: ${userTokens}`,
                    fila: 0,
                    archivo: file.name
                });
                alert(`No tienes suficientes tokens para el archivo "${file.name}". Necesitas ${tokensNecesarios} y te quedan ${userTokens}.`);
                continue;
            }

            for (let i = 0; i < rows.length; i++) {
                const realRowIdx = i + 3;
                const rowIssues = validateRecord(rows[i], realRowIdx, file.name, selectedEntorno, fmt.rules, crossFileMaps, crossFileSets);
                allIssues.push(...rowIssues);
                fileIssues += rowIssues.length;
            }

            // Validación cruzada entre filas: nombres de institución inconsistentes
            const similitudIssues = validateUnificarNombres(rows, file.name, fmt.rules, selectedEntorno);
            allIssues.push(...similitudIssues);
            fileIssues += similitudIssues.length;

            // Validación de consistencia de nombres/apellidos por número de documento
            const consistenciaIssues = validateConsistenciaPorDocumento(rows, file.name, fmt.rules, selectedEntorno);
            allIssues.push(...consistenciaIssues);
            fileIssues += consistenciaIssues.length;

            totalTokensConsumidos += tokensNecesarios;
            allResults.push({ name: file.name, records: rows.length, issues: fileIssues, headers });
        } catch (err) {
            allIssues.push({ severity: 'error', campo: 'Archivo', posicion: null, valor: file.name, mensaje: `Error al procesar: ${err.message}`, fila: 0, archivo: file.name });
        }
    }

    if (totalTokensConsumidos > 0) {
        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                || document.querySelector('input[name="_token"]')?.value;
            const response = await fetch('/validator/update-tokens', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({ tokens_consumed: totalTokensConsumidos })
            });
            const data = await response.json();
            if (data.success) {
                userTokens = data.tokens_left;
            } else {
                alert('Hubo un problema al actualizar tus tokens en el servidor: ' + data.message);
            }
        } catch (error) {
            console.error('Error en la comunicación con el servidor:', error);
            alert('No se pudo guardar el cobro de tokens. Verifica tu conexión.');
        }
    }

    const tokensDisplay = document.getElementById('tokens-counter');
    if (tokensDisplay) tokensDisplay.textContent = userTokens;

    // Check low-tokens alert
    if (typeof checkTokensThreshold === 'function') checkTokensThreshold();

    renderResults();

    btn.disabled = false;
    btn.classList.remove('loading');
    document.getElementById('btn-icon').textContent = '▶';
    document.getElementById('btn-text').textContent = 'VALIDAR ARCHIVOS';
}

// ============================
// RESULTS RENDERING
// ============================
function renderResults() {
    const fmt = getFormatById(selectedFormatId);
    const totalRecords = allResults.reduce((s, r) => s + r.records, 0);
    const errors = allIssues.filter(i => i.severity === 'error').length;
    const warnings = allIssues.filter(i => i.severity === 'warning').length;
    const infos = allIssues.filter(i => i.severity === 'info').length;
    const similitudes = allIssues.filter(i => i.severity === 'similitud').length;

    const maxDeduct = totalRecords * 10;
    const deductions = errors * 2 + warnings * 0.5 + similitudes * 1;
    const score = totalRecords > 0 ? Math.max(0, Math.round(100 - (deductions / Math.max(maxDeduct, deductions + 1)) * 100)) : 0;

    document.getElementById('stat-records').textContent = totalRecords.toLocaleString('es');
    document.getElementById('stat-errors').textContent = errors.toLocaleString('es');
    document.getElementById('stat-warnings').textContent = warnings.toLocaleString('es');
    const statSim = document.getElementById('stat-similitud');
    if (statSim) statSim.textContent = similitudes.toLocaleString('es');

    const sn = document.getElementById('score-num');
    sn.textContent = score + '%';
    sn.className = 'score-num ' + (score >= 80 ? 'score-good' : score >= 60 ? 'score-med' : 'score-bad');
    document.getElementById('score-pct').textContent = score + '%';

    const bar = document.getElementById('quality-bar');
    bar.style.width = score + '%';
    bar.style.background = score >= 80 ? 'var(--ok)' : score >= 60 ? 'var(--warn)' : 'var(--err)';

    document.getElementById('results-subtitle').textContent =
        `Entorno ${ENTORNO_NAMES[selectedEntorno]} · Formato ${fmt.nombre} — ${allResults.length} archivo(s) — ${totalRecords} registros — ${errors + warnings + infos + similitudes} incidencias`;

    document.getElementById('results-section').style.display = 'block';
    document.getElementById('step3').classList.add('done');
    document.getElementById('step4').classList.add('active');

    renderIssues('all');
}

function renderIssues(filter) {
    currentFilter = filter;
    const body = document.getElementById('issues-body');
    const filtered = filter === 'all' ? allIssues : allIssues.filter(i => i.severity === filter);

    if (filtered.length === 0) {
        body.innerHTML = `<div class="empty-state"><span class="empty-icon">${filter === 'error' ? '✅' : '🎉'}</span>${filter === 'all' ? 'Sin incidencias encontradas. ¡Excelente calidad del dato!' : 'Sin incidencias de este tipo.'}</div>`;
        return;
    }

    const byFile = {};
    for (const issue of filtered) {
        if (!byFile[issue.archivo]) byFile[issue.archivo] = [];
        byFile[issue.archivo].push(issue);
    }

    let html = '';
    for (const [fname, issues] of Object.entries(byFile)) {
        const errC = issues.filter(i => i.severity === 'error').length;
        const warnC = issues.filter(i => i.severity === 'warning').length;
        html += `<div class="file-section">
            <div class="file-section-header" onclick="toggleSection(this)">
                <span style="font-size:16px;">📄</span>
                <span class="fsec-name">${fname}</span>
                <span class="tag ${errC > 0 ? 'tag-err' : warnC > 0 ? 'tag-warn' : 'tag-ok'}">${issues.length} incidencias</span>
                <span style="font-size:12px; color:var(--text3);">${errC} err / ${warnC} adv</span>
                <span class="chevron open">▾</span>
            </div>
            <div class="file-section-body">
                <div class="issues-container">
                <table class="issues-table">
                <thead>
                <tr>
                <th>TIPO</th><th>FILA</th><th>NOFicha</th><th>CAMPO</th><th>VALOR</th><th>DESCRIPCIÓN</th><th>USUARIO EXCEL</th>
                <tbody>`;
        for (const issue of issues) {
            const tagClass = issue.severity === 'error' ? 'tag-err'
                : issue.severity === 'warning' ? 'tag-warn'
                : issue.severity === 'similitud' ? 'tag-sim'
                : 'tag-info';
            const tagLabel = issue.severity === 'error' ? '❌ ERROR'
                : issue.severity === 'warning' ? '⚠️ ADVERTENCIA'
                : issue.severity === 'similitud' ? '🔀 NOMBRE INCONSISTENTE'
                : 'ℹ️ INFO';
            html += `<tr>
                <td><span class="tag ${tagClass}">${tagLabel}</span></td>
                <td><span class="row-ref">#${issue.fila}</span></td>
                <td style="font-family:'Space Mono',monospace;font-size:11px; max-width:180px; word-break:break-word;">${escHtml(issue.Ficha_fic)}</td>
                <td style="font-family:'Space Mono',monospace;font-size:11px; max-width:180px; word-break:break-word;">${escHtml(issue.campo)}${issue.posicion ? '<span style="font-size:10px;color:var(--warn);margin-left:4px">(pos. ' + issue.posicion + ')</span>' : ''}</td>
                <td style="font-family:'Space Mono',monospace;font-size:11px; color:var(--text3);">${escHtml(issue.valor)}</td>
                <td style="font-size:12px;">${escHtml(issue.mensaje)}</td>
                <td style="font-family:'Space Mono',monospace;font-size:11px; color:var(--text3);">${escHtml(issue.usuarioExcel)}</td>
            </tr>`;
        }
        html += `</tbody></table></div></div></div>`;
    }
    body.innerHTML = html;
}

function escHtml(s) {
    return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
}

function toggleSection(header) {
    const body = header.nextElementSibling;
    const chevron = header.querySelector('.chevron');
    if (body.style.display === 'none') { body.style.display = ''; chevron.classList.add('open'); }
    else { body.style.display = 'none'; chevron.classList.remove('open'); }
}

function filterIssues(type, btn) {
    document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    renderIssues(type);
}

function exportCSV() {
    if (allIssues.length === 0) return;
    const header = 'Archivo;Fila;Tipo;Ficha;Campo;Valor;Descripción;Usuario Excel\n';
    const rows = allIssues.map(i =>
        `"${i.archivo}";"${i.fila}";"${i.severity}";"${i.Ficha_fic}";"${i.campo}${i.posicion ? ' (pos.' + i.posicion + ')' : ''}";"${String(i.valor).replace(/"/g, '""')}";"${i.mensaje.replace(/"/g, '""')}";"${i.usuarioExcel}"`
    ).join('\n');
    const blob = new Blob(['\uFEFF' + header + rows], { type: 'text/csv;charset=utf-8;' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `errores_${selectedFormatId}_${selectedEntorno}_${new Date().toISOString().slice(0, 10)}.csv`;
    a.click();
}

// Exporta un .xlsx con los datos originales cargados y resalta en rojo
// las celdas donde se detectó un error/advertencia, además de la celda
// de Ficha en cada fila con incidencias (para poder filtrar por color).
async function exportExcelConErrores() {
    if (!lastParsedFiles || lastParsedFiles.length === 0) {
        alert('No hay datos cargados para exportar. Valide los archivos primero.');
        return;
    }
    if (typeof ExcelJS === 'undefined') {
        alert('No se pudo cargar el motor de generación de Excel. Recargue la página e intente de nuevo.');
        return;
    }

    const workbook = new ExcelJS.Workbook();
    // Rojo puro (no rosado claro): es el color exacto que ErrorSheetParser::isRed()
    // busca al leer un Excel subido manualmente en /adm/error-imports, así este
    // archivo exportado sirve directo para ese cargue si se necesita.
    const FILL_ERROR = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FFFF0000' } };
    const FONT_ERROR = { color: { argb: 'FFFFFFFF' } };
    const FICHA_KEYS = ['Ficha_fic', 'FICHA_FIC', 'ficha_fic'];

    const usedSheetNames = new Set();

    for (const pf of lastParsedFiles) {
        const { file, headers, rows } = pf;
        if (!headers || headers.length === 0) continue;

        const fileIssues = allIssues.filter(i => i.archivo === file.name);

        // rowIdx (0-based en `rows`) -> Map(columnaKey -> [mensajes])
        const errorCellsByRow = new Map();
        // rowIdx -> true si la fila tiene al menos una incidencia
        const rowsWithIssues = new Set();

        for (const issue of fileIssues) {
            if (issue.fila == null) continue;
            const rowIdx = issue.fila - 3; // fila real de excel -> índice 0-based en rows
            if (rowIdx < 0 || rowIdx >= rows.length) continue;
            rowsWithIssues.add(rowIdx);
            const colKey = issue.posicion ? `${issue.campo}__${issue.posicion}` : issue.campo;
            if (!errorCellsByRow.has(rowIdx)) errorCellsByRow.set(rowIdx, new Map());
            const msgs = errorCellsByRow.get(rowIdx);
            if (!msgs.has(colKey)) msgs.set(colKey, []);
            msgs.get(colKey).push(issue.mensaje);
        }

        // Nombre de hoja válido y único (máx 31 caracteres, sin caracteres prohibidos)
        let sheetName = file.name.replace(/\.[^.]+$/, '').replace(/[\\\/\*\?\[\]:]/g, ' ').trim().slice(0, 31) || 'Datos';
        let finalName = sheetName, n = 2;
        while (usedSheetNames.has(finalName)) {
            finalName = (sheetName.slice(0, 28) + '_' + n);
            n++;
        }
        usedSheetNames.add(finalName);

        const ws = workbook.addWorksheet(finalName);
        const fichaKey = headers.find(h => FICHA_KEYS.includes(h));

        // El cargue manual de /adm/error-imports asume que la columna A es la
        // ficha, sin importar cómo venga ordenado el archivo original. Se
        // reordena solo para este export (no afecta la lectura/validación).
        const headersExport = fichaKey ? [fichaKey, ...headers.filter(h => h !== fichaKey)] : headers;
        // Columna extra, no viene del archivo original: junta los mensajes de
        // todos los campos en error de la fila para que el digitador vea el
        // detalle sin depender de la nota flotante de cada celda roja. Es la
        // misma columna "Observaciones" que ErrorSheetParser reconoce al
        // volver a subir este Excel en /adm/error-imports.
        const headersWithObs = [...headersExport, 'Observaciones'];

        ws.addRow(headersWithObs);
        ws.getRow(1).eachCell(cell => {
            cell.font = { bold: true, color: { argb: 'FFFFFFFF' } };
            cell.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FF2F4157' } };
        });

        rows.forEach((row, rowIdx) => {
            const values = headersExport.map(h => row[h] ?? '');
            const errCols = errorCellsByRow.get(rowIdx);
            const observaciones = errCols
                ? Array.from(errCols.entries()).map(([col, msgs]) => `${col}: ${msgs.join(' / ')}`).join(' | ')
                : '';
            const excelRow = ws.addRow([...values, observaciones]);

            if (errCols) {
                headersExport.forEach((h, colIdx) => {
                    if (errCols.has(h)) {
                        const cell = excelRow.getCell(colIdx + 1);
                        cell.fill = FILL_ERROR;
                        cell.font = FONT_ERROR;
                        cell.note = errCols.get(h).join('\n');
                    }
                });
            }

            if (rowsWithIssues.has(rowIdx) && fichaKey) {
                const fichaCell = excelRow.getCell(headersExport.indexOf(fichaKey) + 1);
                fichaCell.fill = FILL_ERROR;
                fichaCell.font = FONT_ERROR;
            }
        });

        ws.autoFilter = { from: { row: 1, column: 1 }, to: { row: 1, column: headersWithObs.length } };
        ws.views = [{ state: 'frozen', ySplit: 1 }];
        headersWithObs.forEach((h, idx) => {
            ws.getColumn(idx + 1).width = h === 'Observaciones' ? 50 : Math.min(40, Math.max(12, String(h).length + 2));
        });
    }

    const buffer = await workbook.xlsx.writeBuffer();
    const blob = new Blob([buffer], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `validacion_${selectedFormatId}_${selectedEntorno}_${new Date().toISOString().slice(0, 10)}.xlsx`;
    a.click();
    URL.revokeObjectURL(url);
}

// Agrupa los issues de un archivo por ficha (fila), con todos los campos
// marcados y su observación (mensaje del error). Usa el mismo conjunto de
// issues que se resalta en rojo en exportExcelConErrores, así lo que se envía
// a los digitadores siempre coincide con lo que se ve marcado en el Excel.
function agruparIssuesPorFicha(pf, fileIssues) {
    const { rows } = pf;
    const porFila = new Map(); // rowIdx (0-based en rows) -> Map(label -> {valor, mensajes:[]})

    for (const issue of fileIssues) {
        if (issue.fila == null) continue;
        const rowIdx = issue.fila - 3; // misma conversión usada en exportExcelConErrores
        if (rowIdx < 0 || rowIdx >= rows.length) continue;
        if (!porFila.has(rowIdx)) porFila.set(rowIdx, new Map());
        const campos = porFila.get(rowIdx);
        if (!campos.has(issue.campo)) campos.set(issue.campo, { valor: issue.valor, mensajes: [] });
        if (issue.mensaje) campos.get(issue.campo).mensajes.push(issue.mensaje);
    }

    const filas = [];
    for (const [rowIdx, campos] of porFila.entries()) {
        const row = rows[rowIdx];
        const ficha = String(row['Ficha_fic'] ?? row['FICHA_FIC'] ?? row['ficha_fic'] ?? '').trim();
        if (!ficha) continue; // sin ficha no se puede asociar la tarea a una recepción
        const usuario = String(row['Usuario'] ?? row['USUARIO'] ?? row['usuario'] ?? '').trim();

        filas.push({
            row_number: rowIdx + 3,
            file_number: ficha,
            username_raw: usuario,
            fields: Array.from(campos.entries()).map(([label, info]) => ({
                label,
                value: info.valor == null ? '' : String(info.valor),
                observacion: info.mensajes.join(' | ')
            }))
        });
    }
    return filas;
}

// Envía los errores detectados a /adm/error-imports para que queden asignados
// a los digitadores (por su Usuario), igual que el flujo manual de subir un
// Excel con celdas en rojo, pero sin generar ni re-leer ningún archivo: los
// datos ya agrupados por ficha/campo se mandan directo como JSON. No afecta
// en nada el cargue y la validación normal del Excel por parte del usuario.
async function enviarErroresADigitadores() {
    if (!lastParsedFiles || lastParsedFiles.length === 0) {
        alert('No hay datos validados para enviar. Valide los archivos primero.');
        return;
    }
    if (allIssues.length === 0) {
        alert('No hay incidencias para enviar a los digitadores.');
        return;
    }

    const fmt = getFormatById(selectedFormatId);
    const environmentId = ENTORNO_CODES[selectedEntorno];
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    let totalFichas = 0;
    let archivosEnviados = 0;
    const fallidos = [];

    for (const pf of lastParsedFiles) {
        const { file, rows } = pf;
        if (!rows || rows.length === 0) continue;

        const fileIssues = allIssues.filter(i => i.archivo === file.name);
        if (fileIssues.length === 0) continue;

        const filas = agruparIssuesPorFicha(pf, fileIssues);
        if (filas.length === 0) continue;

        try {
            const response = await fetch('/validator/send-to-digitizers', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({
                    environment_id: environmentId,
                    original_filename: file.name,
                    format_nombre: fmt ? fmt.nombre : '',
                    rows: filas
                })
            });
            const data = await response.json();
            if (response.ok && data.success) {
                archivosEnviados++;
                totalFichas += data.fichas || filas.length;
            } else {
                fallidos.push(`${file.name}: ${data.message || 'error desconocido'}`);
            }
        } catch (err) {
            fallidos.push(`${file.name}: ${err.message}`);
        }
    }

    if (archivosEnviados > 0) {
        alert(`Se enviaron ${totalFichas} ficha(s) con errores a los digitadores (${archivosEnviados} archivo(s)).`
            + (fallidos.length ? `\n\nAlgunos archivos fallaron:\n${fallidos.join('\n')}` : ''));
    } else {
        alert('No se pudo enviar ningún archivo a los digitadores.' + (fallidos.length ? `\n\n${fallidos.join('\n')}` : ''));
    }
}

function resetApp() {
    selectedEntorno = null;
    selectedFormatId = null;
    loadedFiles = [];
    allIssues = [];
    allResults = [];
    lastParsedFiles = [];
    document.querySelectorAll('.entorno-btn').forEach(b => b.classList.remove('selected'));
    document.getElementById('panel-formato').classList.add('section-hidden');
    document.getElementById('panel-files').classList.add('section-hidden');
    document.getElementById('results-section').style.display = 'none';
    document.getElementById('file-list').innerHTML = '';
    document.getElementById('btn-validate').disabled = true;
    ['step1', 'step2', 'step3', 'step4'].forEach(id => {
        document.getElementById(id).classList.remove('active', 'done');
    });
    document.getElementById('step1').classList.add('active');
}

// ============================
// FORMAT MANAGER MODAL
// ============================
function openFormatManager() {
    document.getElementById('modal-overlay').classList.remove('hidden');
    editingFormatId = null;
    renderFmtList();
    showEditorEmpty();
}

function closeFormatManager() {
    document.getElementById('modal-overlay').classList.add('hidden');
    if (selectedEntorno) renderFormatoGrid();
}

function closeFormatManagerIfBg(e) {
    if (e.target === document.getElementById('modal-overlay')) closeFormatManager();
}

function renderFmtList() {
    const container = document.getElementById('fmt-list');
    const all = getAllFormats();
    container.innerHTML = all.map(f => `
    <div class="fmt-list-item ${f.id === editingFormatId ? 'active' : ''}" onclick="editFormat('${f.id}')">
      <div class="fli-code">${f.code}</div>
      <div class="fli-info">
        <div class="fli-name">${f.nombre}</div>
        <div class="fli-entornos">${f.entornos.map(e => ENTORNO_ICONS[e]).join('')} · ${Object.keys(f.rules).length} campos</div>
      </div>
      ${f.builtin ? '<div class="fli-builtin" title="Formato base (editable)">⚙</div>' : '<div class="fli-custom">✦</div>'}
    </div>
  `).join('');
}

function showEditorEmpty() {
    document.getElementById('fmt-editor-empty').style.display = '';
    document.getElementById('fmt-editor-form').style.display = 'none';
}

function startNewFormat() {
    editingFormatId = null;
    document.getElementById('fmt-editor-empty').style.display = 'none';
    document.getElementById('fmt-editor-form').style.display = '';
    document.getElementById('btn-delete-fmt').style.display = 'none';
    document.getElementById('btn-reset-builtin').style.display = 'none';
    document.getElementById('fmt-builtin-notice').style.display = 'none';
    document.getElementById('fmt-name').value = '';
    document.getElementById('fmt-code').value = '';
    document.getElementById('fmt-desc').value = '';
    document.querySelectorAll('#fmt-entornos input[type=checkbox]').forEach(cb => cb.checked = false);
    document.getElementById('rules-container').innerHTML = '';
}

function editFormat(id) {
    const fmt = getFormatById(id);
    if (!fmt) return;
    editingFormatId = id;

    document.getElementById('fmt-editor-empty').style.display = 'none';
    document.getElementById('fmt-editor-form').style.display = '';

    const isBuiltin = fmt.builtin;
    const overrides = loadBuiltinOverrides();
    const hasOverride = isBuiltin && !!overrides[id];

    document.getElementById('btn-delete-fmt').style.display = isBuiltin ? 'none' : '';
    const resetBtn = document.getElementById('btn-reset-builtin');
    resetBtn.style.display = (isBuiltin && hasOverride) ? '' : 'none';
    const notice = document.getElementById('fmt-builtin-notice');
    notice.style.display = isBuiltin ? 'flex' : 'none';

    document.getElementById('fmt-name').value = fmt.nombre;
    document.getElementById('fmt-code').value = fmt.code;
    document.getElementById('fmt-desc').value = fmt.desc || '';

    document.querySelectorAll('#fmt-entornos input[type=checkbox]').forEach(cb => {
        cb.checked = fmt.entornos.includes(cb.value);
        cb.disabled = false;
    });

    const container = document.getElementById('rules-container');
    container.innerHTML = '';
    for (const [campo, rule] of Object.entries(fmt.rules)) {
        addRuleRow(campo, rule, false);
    }

    renderFmtList();
}

// ============================
// RULE ROWS
// ============================
function addRuleRow(campoDefault = '', ruleDefault = null, readonly = false) {
    const tpl = document.getElementById('rule-row-tpl');
    const clone = tpl.content.cloneNode(true);
    const row = clone.querySelector('.rule-row');
    row.dataset.ruleId = Date.now() + Math.random();

    const campoInput = row.querySelector('.rule-campo');
    const tipoSelect = row.querySelector('.rule-tipo');
    const descInput = row.querySelector('.rule-desc');
    const minInput = row.querySelector('.rule-min');
    const maxInput = row.querySelector('.rule-max');
    const valoresInput = row.querySelector('.rule-valores');
    const depCampoInput = row.querySelector('.rule-dep-campo');
    const depValoresInput = row.querySelector('.rule-dep-valores');
    const depMsgInput = row.querySelector('.rule-dep-msg');

    campoInput.value = campoDefault;
    descInput.value = ruleDefault?.desc || '';

    if (ruleDefault) {
        const tipo = ruleDefault.tipo || 'texto';
        tipoSelect.value = tipo;

        if (tipo === 'rango') {
            row.querySelector('.rule-extra-rango').style.display = 'flex';
            minInput.value = ruleDefault.min ?? '';
            maxInput.value = ruleDefault.max ?? '';
        }
        if (tipo === 'codigo') {
            row.querySelector('.rule-extra-valores').style.display = 'flex';
            valoresInput.value = (ruleDefault.valores || []).join('|');
        }

        row.querySelectorAll('.rule-obligatorio input').forEach(cb => {
            cb.checked = ruleDefault.obligatorio && ruleDefault.obligatorio.includes(cb.value);
        });

        if (ruleDefault.dependencia) {
            const depEntornos = ruleDefault.dependencia.entornos || [];
            row.querySelectorAll('.rule-dep-entornos input').forEach(cb => {
                cb.checked = depEntornos.includes(cb.value);
            });
            row.querySelector('.rule-extra-dep').style.display = 'flex';
            row.querySelector('.rule-dep-toggle').classList.add('active');
            depCampoInput.value = ruleDefault.dependencia.campo || '';
            const depVals = ruleDefault.dependencia.valores || (ruleDefault.dependencia.valor ? [ruleDefault.dependencia.valor] : []);
            depValoresInput.value = depVals.join('|');
            depMsgInput.value = ruleDefault.dependencia.mensaje || '';

            const whenTrue = row.querySelector('.rule-dep-when-true');
            const whenFalse = row.querySelector('.rule-dep-when-false');
            if (whenTrue) whenTrue.value = ruleDefault.dependencia.requeridoCuandoCumple === false ? 'optional' : 'required';
            if (whenFalse) whenFalse.value = ruleDefault.dependencia.errorSiNoCumpleTieneValor ? 'error' : 'ignore';
        }

        if (ruleDefault.exceptoSi) {
            const exc = row.querySelector('.rule-extra-excepto');
            if (exc) {
                exc.style.display = 'flex';
                row.querySelector('.rule-excepto-toggle')?.classList.add('active');
                row.querySelector('.rule-exc-campo').value = ruleDefault.exceptoSi.campo || '';
                row.querySelector('.rule-exc-valor').value = ruleDefault.exceptoSi.valor || '';
            }
        }

        if (ruleDefault.dependenciasMultiples) {
            const section = row.querySelector('.rule-extra-multidep');
            section.style.display = 'flex';
            row.querySelector('.rule-multidep-toggle')?.classList.add('active');
            const container = row.querySelector('.multi-dep-container');
            ruleDefault.dependenciasMultiples.forEach(dep => {
                const tpl = document.getElementById('multi-dep-row-tpl');
                const clone = tpl.content.cloneNode(true);
                const temp = document.createElement('div');
                temp.appendChild(clone);
                const depRow = temp.querySelector('.multi-dep-row');
                depRow.querySelector('.multi-dep-campo').value = dep.campo || '';
                depRow.querySelector('.multi-dep-valores').value = (dep.valores || []).join('|');
                container.appendChild(depRow);
            });
            row.querySelector('.rule-multi-true').value = ruleDefault.multiWhenTrue || 'required';
            row.querySelector('.rule-multi-false').value = ruleDefault.multiWhenFalse || 'ignore';
            row.querySelector('.rule-multi-msg').value = ruleDefault.multiMensaje || '';
        }

        if (ruleDefault.reglasPorEntorno) {
            const env = row.querySelector('.rule-extra-entorno');
            if (env) {
                env.style.display = 'flex';
                row.querySelector('.rule-entorno-toggle')?.classList.add('active');
                Object.entries(ruleDefault.reglasPorEntorno).forEach(([entorno, conf]) => {
                    const input = row.querySelector(`.rule-env-${entorno}`);
                    if (input && conf.valorFijo) input.value = conf.valorFijo;
                });
            }
        }

        if (ruleDefault.maxHoy || ruleDefault.comparar) {
            const fecha = row.querySelector('.rule-extra-fecha');
            if (fecha) {
                fecha.style.display = 'flex';
                row.querySelector('.rule-fecha-toggle')?.classList.add('active');
                if (ruleDefault.maxHoy) row.querySelector('.rule-max-hoy').checked = true;
                if (ruleDefault.comparar) {
                    row.querySelector('.rule-comp-campo').value = ruleDefault.comparar.campoRef || '';
                    row.querySelector('.rule-comp-op').value = ruleDefault.comparar.operacion || '>=';
                    row.querySelector('.rule-comp-msg').value = ruleDefault.comparar.mensaje || '';
                }
            }
        }
    }

    if (readonly) {
        row.querySelectorAll('input, select, button.btn-icon-danger').forEach(el => {
            if (el.tagName === 'BUTTON') el.style.display = 'none';
            else el.disabled = true;
        });
        row.style.opacity = '0.7';
    }

    document.getElementById('rules-container').appendChild(clone);
}

function onRuleTipoChange(select) {
    const row = select.closest('.rule-row');
    const tipo = select.value;
    const rango = row.querySelector('.rule-extra-rango');
    const valores = row.querySelector('.rule-extra-valores');
    const fecha = row.querySelector('.rule-extra-fecha');
    rango.style.display = 'none';
    valores.style.display = 'none';
    if (tipo === 'rango') rango.style.display = 'flex';
    if (tipo === 'codigo') valores.style.display = 'flex';
    if (tipo.includes('fecha')) fecha.style.display = 'flex';
}

function toggleDepSection(btn) {
    const row = btn.closest('.rule-row');
    const depSection = row.querySelector('.rule-extra-dep');
    const isOpen = depSection.style.display === 'flex';
    depSection.style.display = isOpen ? 'none' : 'flex';
    btn.classList.toggle('active', !isOpen);
}

function toggleMultiDepSection(btn) {
    const row = btn.closest('.rule-row');
    const section = row.querySelector('.rule-extra-multidep');
    if (!section) return;
    const isOpen = section.style.display !== 'none';
    section.style.display = isOpen ? 'none' : 'flex';
    btn.classList.toggle('active', !isOpen);
}

function addMultiDepRow(btn) {
    const row = btn.closest('.rule-row');
    const container = row.querySelector('.multi-dep-container');
    const tpl = document.getElementById('multi-dep-row-tpl');
    const clone = tpl.content.cloneNode(true);
    container.appendChild(clone);
}

function removeMultiDepRow(btn) { btn.closest('.multi-dep-row').remove(); }
function removeRuleRow(btn) { btn.closest('.rule-row').remove(); }

// ============================
// SAVE / DELETE FORMAT
// ============================
function saveFormat() {
    const nombre = document.getElementById('fmt-name').value.trim();
    const code = document.getElementById('fmt-code').value.trim().toUpperCase();
    const desc = document.getElementById('fmt-desc').value.trim();
    const entornos = [...document.querySelectorAll('#fmt-entornos input:checked')].map(cb => cb.value);

    if (!nombre) return alert('El nombre del formato es obligatorio.');
    if (entornos.length === 0) return alert('Seleccione al menos un entorno.');

    const rules = {};
    document.querySelectorAll('#rules-container .rule-row').forEach(row => {
        const campo = row.querySelector('.rule-campo').value.trim();
        if (!campo) return;
        const tipo = row.querySelector('.rule-tipo').value;
        const desc = row.querySelector('.rule-desc').value.trim();
        const obligatorio = [...row.querySelectorAll('.rule-obligatorio input:checked')].map(cb => cb.value);
        const rule = { tipo, desc };
        const multiRows = row.querySelectorAll('.multi-dep-row');

        if (multiRows.length > 0) {
            rule.dependenciasMultiples = [];
            multiRows.forEach(r => {
                const campo = r.querySelector('.multi-dep-campo').value.trim();
                const valores = r.querySelector('.multi-dep-valores').value.split('|').map(v => v.trim()).filter(Boolean);
                if (!campo) return;
                rule.dependenciasMultiples.push({ campo, valores });
            });
            rule.multiWhenTrue = row.querySelector('.rule-multi-true').value;
            rule.multiWhenFalse = row.querySelector('.rule-multi-false').value;
            rule.multiMensaje = row.querySelector('.rule-multi-msg').value.trim();
        }

        if (obligatorio.length > 0) rule.obligatorio = obligatorio;

        if (tipo === 'rango') {
            rule.min = Number(row.querySelector('.rule-min').value) || 0;
            rule.max = Number(row.querySelector('.rule-max').value) || 999;
        }
        if (tipo === 'codigo') {
            const valStr = row.querySelector('.rule-valores').value.trim();
            rule.valores = valStr ? valStr.split('|').map(v => v.trim()).filter(Boolean) : [];
        }

        const depSection = row.querySelector('.rule-extra-dep');
        if (depSection && depSection.style.display === 'flex') {
            const depCampo = row.querySelector('.rule-dep-campo').value.trim();
            const depValores = row.querySelector('.rule-dep-valores').value.trim();
            const depMsg = row.querySelector('.rule-dep-msg').value.trim();
            if (depCampo && depValores) {
                const depEntornos = [...row.querySelectorAll('.rule-dep-entornos input:checked')].map(cb => cb.value);
                rule.dependencia = {
                    campo: depCampo,
                    valores: depValores.split('|').map(v => v.trim()).filter(Boolean),
                    mensaje: depMsg,
                    entornos: depEntornos
                };
            }
        }

        const depWhenTrue = row.querySelector('.rule-dep-when-true');
        const depWhenFalse = row.querySelector('.rule-dep-when-false');
        if (rule.dependencia) {
            if (depWhenTrue) rule.dependencia.requeridoCuandoCumple = depWhenTrue.value === 'required';
            if (depWhenFalse) rule.dependencia.errorSiNoCumpleTieneValor = depWhenFalse.value === 'error';
        }

        const excCampo = row.querySelector('.rule-exc-campo')?.value.trim();
        const excValor = row.querySelector('.rule-exc-valor')?.value.trim();
        if (excCampo && excValor) rule.exceptoSi = { campo: excCampo, valor: excValor };

        const reglasEntorno = {};
        ['laboral', 'educativo', 'institucional', 'comunitario'].forEach(ent => {
            const input = row.querySelector(`.rule-env-${ent}`);
            if (input && input.value.trim()) reglasEntorno[ent] = { valorFijo: input.value.trim() };
        });
        if (Object.keys(reglasEntorno).length > 0) rule.reglasPorEntorno = reglasEntorno;

        const maxHoy = row.querySelector('.rule-max-hoy');
        if (maxHoy?.checked) rule.maxHoy = true;

        const compCampo = row.querySelector('.rule-comp-campo')?.value.trim();
        if (compCampo) {
            rule.comparar = {
                campoRef: compCampo,
                operacion: row.querySelector('.rule-comp-op').value,
                mensaje: row.querySelector('.rule-comp-msg').value.trim()
            };
        }

        rules[campo] = rule;
    });

    const isBuiltin = BUILTIN_FORMATS.some(f => f.id === editingFormatId);

    if (isBuiltin) {
        const overrides = loadBuiltinOverrides();
        overrides[editingFormatId] = { nombre, code: code || editingFormatId.toUpperCase(), desc, entornos, rules };
        saveBuiltinOverrides(overrides);
    } else {
        const userFormats = loadUserFormats();
        if (editingFormatId) {
            const idx = userFormats.findIndex(f => f.id === editingFormatId);
            if (idx >= 0) userFormats[idx] = { ...userFormats[idx], nombre, code: code || userFormats[idx].code, desc, entornos, rules };
        } else {
            const id = 'uf_' + Date.now();
            userFormats.push({ id, nombre, code: code || nombre.substring(0, 4).toUpperCase(), desc, builtin: false, entornos, rules });
            editingFormatId = id;
        }
        saveUserFormats(userFormats);
    }

    renderFmtList();
    editFormat(editingFormatId);
    alert(`✅ Formato "${nombre}" guardado correctamente.`);
}

function deleteFormat() {
    if (!editingFormatId) return;
    if (!confirm('¿Eliminar este formato? Esta acción no se puede deshacer.')) return;
    const userFormats = loadUserFormats().filter(f => f.id !== editingFormatId);
    saveUserFormats(userFormats);
    editingFormatId = null;
    renderFmtList();
    showEditorEmpty();
}

function resetBuiltinFormat() {
    if (!editingFormatId) return;
    if (!confirm('¿Restaurar este formato a sus valores originales? Se perderán los cambios guardados.')) return;
    const overrides = loadBuiltinOverrides();
    delete overrides[editingFormatId];
    saveBuiltinOverrides(overrides);
    editFormat(editingFormatId);
    alert('✅ Formato restaurado a los valores originales.');
}

// ============================
// INIT
// ============================
(async () => {
    await loadRulesJSON();
})();

function toggleEntornoRules(btn) {
    const row = btn.closest('.rule-row');
    const section = row.querySelector('.rule-extra-entorno');
    if (!section) return;
    const isOpen = section.style.display === 'flex';
    section.style.display = isOpen ? 'none' : 'flex';
    btn.classList.toggle('active', !isOpen);
}

function toggleExceptoSection(btn) {
    const row = btn.closest('.rule-row');
    const section = row.querySelector('.rule-extra-excepto');
    if (!section) return;
    const isOpen = section.style.display === 'flex';
    section.style.display = isOpen ? 'none' : 'flex';
    btn.classList.toggle('active', !isOpen);
}

function toggleFechaSection(btn) {
    const row = btn.closest('.rule-row');
    const section = row.querySelector('.rule-extra-fecha');
    if (!section) return;
    const isOpen = section.style.display === 'flex';
    section.style.display = isOpen ? 'none' : 'flex';
    btn.classList.toggle('active', !isOpen);
}

window.selectFormato = selectFormato;
window.selectEntorno = selectEntorno;
window.removeFile = removeFile;
window.toggleSection = toggleSection;
window.runValidation = runValidation;
window.exportCSV = exportCSV;
window.exportExcelConErrores = exportExcelConErrores;
window.enviarErroresADigitadores = enviarErroresADigitadores;
window.resetApp = resetApp;
window.openFormatManager = openFormatManager;
window.closeFormatManager = closeFormatManager;
window.filterIssues = filterIssues;
window.handleFiles = handleFiles;
window.closeFormatManagerIfBg = closeFormatManagerIfBg;
window.startNewFormat = startNewFormat;
window.addRuleRow = addRuleRow;
window.saveFormat = saveFormat;
window.deleteFormat = deleteFormat;
window.resetBuiltinFormat = resetBuiltinFormat;
window.editFormat = editFormat;
window.toggleDepSection = toggleDepSection;
window.onRuleTipoChange = onRuleTipoChange;
window.removeRuleRow = removeRuleRow;
window.toggleEntornoRules = toggleEntornoRules;
window.toggleExceptoSection = toggleExceptoSection;
window.toggleFechaSection = toggleFechaSection;
window.addMultiDepRow = addMultiDepRow;
window.toggleMultiDepSection = toggleMultiDepSection;
window.removeMultiDepRow = removeMultiDepRow;