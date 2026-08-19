<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Validador MB — Más Bienestar</title>

</head>


@vite(['resources/css/validators/style.css', 'resources/js/validators/script.js'])


<body>

    @if(!empty($message))
    <div style="
        position: fixed;
        inset: 0;
        z-index: 9999;
        background: rgba(10, 12, 18, 0.96);
        display: flex;
        align-items: center;
        justify-content: center;
        backdrop-filter: blur(6px);
    ">
        <div style="
            background: #13151e;
            border: 1px solid #f03e3e;
            border-radius: 16px;
            padding: 48px 40px;
            max-width: 480px;
            width: 90%;
            text-align: center;
            box-shadow: 0 0 60px rgba(240,62,62,0.18);
        ">
            <div style="font-size: 52px; margin-bottom: 20px;">🔒</div>
            <div style="
                font-family: 'Space Mono', monospace;
                font-size: 11px;
                letter-spacing: 3px;
                text-transform: uppercase;
                color: #f03e3e;
                margin-bottom: 16px;
            ">Acceso restringido</div>
            <p style="
                color: #e0e4f0;
                font-size: 16px;
                line-height: 1.6;
                margin: 0 0 32px;
            ">{{ $message }}</p>
            <a href="mailto:soporte@masbienestar.com" style="
                display: inline-block;
                background: #f03e3e;
                color: #fff;
                text-decoration: none;
                padding: 12px 28px;
                border-radius: 8px;
                font-size: 14px;
                font-weight: 600;
            ">Contactar soporte</a>
        </div>
    </div>
    @endif

    <div class="app">
        <header>
            <div class="logo-badge">MB</div>
            <div class="header-text">
                <h1>Validador de Calidad del Dato</h1>
                <p>Entornos Más Bienestar — Multi-formato · Verificación según instructivo 2026</p>
            </div>
            <!-- <button class="btn-manage" onclick="openFormatManager()">⚙ Gestionar formatos</button> -->

            <button onclick="window.location.href='{{ route('validator.builder.index') }}'"
                    class="btn-manage">

                <span class="icon">⚙</span>
                <span>Gestor de formatos</span>

            </button>
            <div class="tokens-badge">
               
                <div class="token-info">
                    <small class="text-uppercase fw-semibold opacity-75">
                        Tokens Disponibles
                    </small>

                    <span id="tokens-counter" class="fw-bold fs-5">
                        {{ isset($tokens) ? $tokens : 'Cargando...' }}
                    </span>
                </div>
                <div style="margin-left:12px;">
                    <button class="btn-manage" style="padding:8px 12px; font-size:13px;" onclick="window.location.href='{{ route('mp.checkout') }}'">
                        <span class="icon">💳</span>
                        <span>Comprar tokens</span>
                    </button>
                </div>
            </div>
            <div id="token-alert" style="display:none;margin-top:8px;grid-column:1/-1"></div>
        </header>

        <!-- STEPS -->
        <div class="steps">
            <div class="step active" id="step1">
                <div class="step-num">1</div>
                <div class="step-label">Entorno</div>
            </div>
            <div class="step-sep"></div>
            <div class="step" id="step2">
                <div class="step-num">2</div>
                <div class="step-label">Formato</div>
            </div>
            <div class="step-sep"></div>
            <div class="step" id="step3">
                <div class="step-num">3</div>
                <div class="step-label">Archivos</div>
            </div>
            <div class="step-sep"></div>
            <div class="step" id="step4">
                <div class="step-num">4</div>
                <div class="step-label">Resultados</div>
            </div>
        </div>

        <!-- STEP 1: ENTORNO -->
        <div class="panel" id="panel-entorno">
            <div class="panel-title">Paso 1 — Seleccione el entorno</div>

            <div class="entorno-grid">

                <button class="entorno-btn" data-entorno="laboral" onclick="selectEntorno(this)">
                    <span class="icon">🏭</span>
                    <div class="label">Laboral</div>
                    <div class="code">COD: 2</div>
                </button>

                <button class="entorno-btn" data-entorno="educativo" onclick="selectEntorno(this)">
                    <span class="icon">🎓</span>
                    <div class="label">Educativo</div>
                    <div class="code">COD: 3</div>
                </button>

                <button class="entorno-btn" data-entorno="comunitario" onclick="selectEntorno(this)">
                    <span class="icon">🏘️</span>
                    <div class="label">Comunitario</div>
                    <div class="code">COD: 4</div>
                </button>

                <button class="entorno-btn" data-entorno="institucional" onclick="selectEntorno(this)">
                    <span class="icon">🏥</span>
                    <div class="label">Institucional</div>
                    <div class="code">COD: 6</div>
                </button>

            </div>
        </div>

        <!-- STEP 2: FORMATO -->
        <div class="panel section-hidden" id="panel-formato">
            <div class="panel-title" id="formato-panel-title">Paso 2 — Seleccione el formato a validar</div>
            <div class="notice" id="formato-notice">
                ℹ️ Solo se muestran los formatos disponibles para el entorno seleccionado.
            </div>
            <div id="formato-grid" class="formato-grid"></div>
            <div style="margin-top:16px;">
                <button class="btn-outline" onclick="openFormatManager()">+ Crear nuevo formato</button>
            </div>
        </div>

        <!-- STEP 3: FILES -->
        <div class="panel section-hidden" id="panel-files">
            <div class="panel-title" id="files-panel-title">Paso 3 — Cargue los archivos CSV o Excel</div>
            <div class="notice">
                ℹ️ Los archivos deben ser exportados del sistema en formato <strong>.xlsx</strong> o <strong>.csv</strong>. Puede cargar uno o
                varios archivos a la vez.
            </div>
            <div class="upload-area" id="drop-area">
                <input type="file" id="file-input" multiple accept=".xlsx,.csv" onchange="handleFiles(this.files)">
                <div class="upload-icon">📂</div>
                <div class="upload-text">Arrastre los archivos aquí o <strong>haga clic para seleccionar</strong></div>
                <div style="font-size:12px; color:var(--text3); margin-top:6px;">Formato: .xlsx o .csv</div>
            </div>
            <div class="file-list" id="file-list"></div>
            <div style="margin-top:20px; display:flex; gap:12px; align-items:center; flex-wrap:wrap;">
                <button class="btn-validate" id="btn-validate" disabled onclick="runValidation()">
                    <span id="btn-icon">▶</span>
                    <span id="btn-text">VALIDAR ARCHIVOS</span>
                </button>
                <div id="selected-badge" style="font-size:13px; color:var(--text2);"></div>
            </div>
        </div>

        <!-- STEP 4: RESULTS -->
        <div id="results-section" style="display:none;">
            <div class="panel">
                <div class="results-header">
                    <div>
                        <div class="panel-title">Resultados de calidad del dato</div>
                        <div style="font-size:13px; color:var(--text2);" id="results-subtitle"></div>
                    </div>
                    <div style="display:flex; gap:12px; align-items:center; flex-wrap:wrap;">
                        <div class="score-badge">
                            <div class="score-num" id="score-num">--</div>
                            <div class="score-label">% Calidad global</div>
                        </div>
                        <button class="btn-export" onclick="exportCSV()">⬇ Exportar errores CSV</button>
                        <button class="btn-export" onclick="exportExcelConErrores()">⬇ Exportar Excel con datos y errores</button>
                        <button class="btn-export" onclick="enviarErroresADigitadores()">📤 Enviar a digitadores</button>
                        <button class="btn-export" onclick="resetApp()">↺ Nueva validación</button>
                    </div>
                </div>

                <div class="stats-grid">
                    <div class="stat-card stat-ok">
                        <div class="stat-num" id="stat-records">0</div>
                        <div class="stat-lbl">Registros analizados</div>
                    </div>
                    <div class="stat-card stat-warn">
                        <div class="stat-num" id="stat-warnings">0</div>
                        <div class="stat-lbl">Error de digitacion</div>
                    </div>
                    <div class="stat-card stat-err">
                        <div class="stat-num" id="stat-errors">0</div>
                        <div class="stat-lbl">Errores críticos</div>
                    </div>
                    <div class="stat-card stat-sim">
                        <div class="stat-num" id="stat-similitud">0</div>
                        <div class="stat-lbl">Nombres inconsistentes</div>
                    </div>
                </div>

                <div style="margin-bottom:24px;">
                    <div
                        style="display:flex; justify-content:space-between; font-size:12px; color:var(--text2); margin-bottom:6px;">
                        <span>Índice de calidad del dato</span>
                        <span id="score-pct">0%</span>
                    </div>
                    <div class="progress-bar-wrap" style="height:10px;">
                        <div class="progress-bar-fill" id="quality-bar" style="width:0%; background:var(--err);">
                        </div>
                    </div>
                </div>

                <div class="filter-bar">
                    <button class="filter-btn active" onclick="filterIssues('all', this)">Todos</button>
                    <button class="filter-btn" onclick="filterIssues('error', this)">❌ Errores críticos</button>
                    <button class="filter-btn" onclick="filterIssues('warning', this)">⚠️ Error de digitacion</button>
                    <button class="filter-btn" onclick="filterIssues('similitud', this)">🔀 Nombres inconsistentes</button>
                    <button class="filter-btn" onclick="filterIssues('info', this)">ℹ️ Informativos</button>
                </div>

                <div id="issues-body"></div>
            </div>
        </div>

    </div>

    <!-- ===================== FORMAT MANAGER MODAL ===================== -->
    <div id="modal-overlay" class="modal-overlay hidden" onclick="closeFormatManagerIfBg(event)">
        <div class="modal-box">
            <div class="modal-header">
                <div style="display:flex; align-items:center; gap:12px;">
                    <div class="logo-badge" style="font-size:9px;">FMT</div>
                    <div>
                        <div
                            style="font-family:'Space Mono',monospace; font-size:11px; color:var(--accent); letter-spacing:2px; text-transform:uppercase;">
                            Gestor de Formatos</div>
                        <div style="font-size:12px; color:var(--text2); margin-top:2px;">Cree y configure formatos de
                            validación con
                            sus reglas de campo</div>
                    </div>
                </div>
                <button class="modal-close" onclick="closeFormatManager()">✕</button>
            </div>

            <div class="modal-body">
                <div class="modal-columns">

                    <!-- LEFT: format list -->
                    <div class="modal-col-left">
                        <div class="col-header">
                            <span>Formatos disponibles</span>
                            <button class="btn-sm-accent" onclick="startNewFormat()">+ Nuevo</button>
                        </div>
                        <div id="fmt-list"></div>
                    </div>

                    <!-- RIGHT: format editor -->
                    <div class="modal-col-right" id="fmt-editor-panel">
                        <div class="editor-empty" id="fmt-editor-empty">
                            <span style="font-size:40px; display:block; margin-bottom:12px;">📋</span>
                            Seleccione un formato para editar<br>o cree uno nuevo
                        </div>

                        <div id="fmt-editor-form" style="display:none;">
                            <!-- Builtin notice -->
                            <div id="fmt-builtin-notice" class="builtin-notice" style="display:none;">
                                <span>⚙</span>
                                <span>Este es un formato base. Los cambios se guardan como personalización local y no
                                    afectan el archivo
                                    <code>rules.json</code>.</span>
                            </div>

                            // en colomnas

                            <div class="">
                                <div class="editor-section">
                                    <label class="field-label">Nombre del formato *</label>
                                    <input type="text" id="fmt-name"
                                        placeholder="Ej: Sesiones Colectivas, UT, HXB..." class="field-input">
                                </div>
                                <div class="editor-section">
                                    <label class="field-label">Código / Abreviatura</label>
                                    <input type="text" id="fmt-code" placeholder="Ej: SSC, UT, HXB"
                                        class="field-input" maxlength="10">
                                </div>
                                <div class="editor-section">
                                    <label class="field-label">Descripción</label>
                                    <input type="text" id="fmt-desc" placeholder="Descripción corta del formato"
                                        class="field-input">
                                </div>
                            </div>

                            <div class="editor-section">
                                <label class="field-label">Entornos donde aplica *</label>
                                <div class="entorno-checks" id="fmt-entornos">
                                    <label class="check-label"><input type="checkbox" value="laboral"> 🏭
                                        Laboral</label>
                                    <label class="check-label"><input type="checkbox" value="educativo"> 🎓
                                        Educativo</label>
                                    <label class="check-label"><input type="checkbox" value="comunitario"> 🏘️
                                        Comunitario</label>
                                    <label class="check-label"><input type="checkbox" value="institucional"> 🏥
                                        Institucional</label>
                                </div>
                            </div>

                            <div class="editor-actions">
                                <button class="btn-validate" style="padding:10px 24px; font-size:12px;"
                                    onclick="saveFormat()">💾
                                    Guardar formato</button>
                                <button class="btn-export" id="btn-delete-fmt" onclick="deleteFormat()"
                                    style="display:none; border-color:var(--err); color:var(--err);">🗑
                                    Eliminar</button>
                                <button class="btn-export" id="btn-reset-builtin" onclick="resetBuiltinFormat()"
                                    style="display:none; border-color:var(--warn); color:var(--warn);">↺ Restaurar
                                    original</button>

                                <button class="btn-validate" onclick="reloadRules()" class="btn-refresh">
                                    🔄 Refrescar reglas
                                </button>
                            </div>

                            <div class="editor-section">
                                <div
                                    style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
                                    <label class="field-label" style="margin:0;">Reglas de campo</label>
                                    <button class="btn-sm-accent" onclick="addRuleRow()">+ Agregar campo</button>
                                </div>
                                <div id="rules-container"></div>
                            </div>


                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- RULE ROW TEMPLATE (hidden) -->
    <template id="rule-row-tpl">

        <div class="rule-row" data-rule-id="">

            <!-- HEADER -->
            <div class="rule-row-header">

                <input type="text" class="rule-campo field-input"
                    placeholder="Nombre exacto del campo (columna Excel)" style="flex:2;">

                <select class="rule-tipo field-input" style="flex:1;" onchange="onRuleTipoChange(this)">
                    <option value="texto">Texto libre</option>
                    <option value="texto_nonempty">Texto no vacío</option>
                    <option value="numero">Número</option>
                    <option value="rango">Rango numérico</option>
                    <option value="fecha">Fecha</option>
                    <option value="codigo">Lista de valores</option>
                    <option value="alfanumerico_mayus">Alfanumérico (MAYÚSCULAS)</option>
                </select>

                <button class="rule-dep-toggle btn-sm-muted" onclick="toggleDepSection(this)" title="Dependencia">
                    ⛓ Dep
                </button>

                <button class="btn-sm-muted" onclick="toggleMultiDepSection(this)" title="Dependencias múltiples">
                    🔗 Multi
                </button>

                <button class="btn-sm-muted" onclick="toggleEntornoRules(this)" title="Reglas por entorno">
                    🌍 Entorno
                </button>

                <button class="btn-sm-muted" onclick="toggleExceptoSection(this)" title="Excepto si">
                    ⚠ Excepto
                </button>

                <button class="btn-sm-muted" onclick="toggleFechaSection(this)" title="Opciones de fecha">
                    📅 Fecha
                </button>

                <button class="btn-icon-danger" onclick="removeRuleRow(this)" title="Eliminar campo">
                    ✕
                </button>

            </div>

            <!-- BODY -->
            <div class="rule-row-body">

                <!-- OBLIGATORIO -->
                <div class="rule-row-line">

                    <label class="mini-label">
                        Obligatorio en:
                    </label>

                    <div class="mini-checks rule-obligatorio">

                        <label>
                            <input type="checkbox" value="laboral">
                            Lab
                        </label>

                        <label>
                            <input type="checkbox" value="educativo">
                            Edu
                        </label>

                        <label>
                            <input type="checkbox" value="comunitario">
                            Com
                        </label>

                        <label>
                            <input type="checkbox" value="institucional">
                            Ins
                        </label>

                    </div>

                </div>

                <!-- RANGO -->
                <div class="rule-row-line rule-extra-rango" style="display:none;">

                    <label class="mini-label">
                        Min:
                    </label>

                    <input type="number" class="rule-min field-input" style="width:80px;" placeholder="0">

                    <label class="mini-label" style="margin-left:8px;">
                        Max:
                    </label>

                    <input type="number" class="rule-max field-input" style="width:80px;" placeholder="100">

                </div>

                <!-- VALORES -->
                <div class="rule-row-line rule-extra-valores" style="display:none;">

                    <label class="mini-label">
                        Valores válidos:
                    </label>

                    <input type="text" class="rule-valores field-input" placeholder="Ej: Si|No|No aplica"
                        style="flex:1;">

                </div>

                <!-- DEPENDENCIA -->
                <div class="rule-extra-dep rule-dep-block"
                    style="display:none; flex-direction:column; gap:8px; margin-top:8px;">

                    <div style="display:flex; align-items:center; gap:4px;">

                        <span
                            style="
                            font-size:10px;
                            font-family:'Space Mono', monospace;
                            color:var(--accent);
                            letter-spacing:1px;
                            text-transform:uppercase;
                        ">
                            ⛓ Dependencia condicional
                        </span>

                    </div>

                    <!-- CAMPO -->
                    <div class="rule-row-line">

                        <label class="mini-label">
                            Campo ref:
                        </label>

                        <input type="text" class="rule-dep-campo field-input" placeholder="Campo del que depende"
                            style="flex:2;">

                    </div>

                    <!-- VALORES -->
                    <div class="rule-row-line">

                        <label class="mini-label">
                            Valores activadores:
                        </label>

                        <input type="text" class="rule-dep-valores field-input" placeholder="Ej: 272|273|274"
                            style="flex:2;">

                    </div>

                    <!-- ENTORNOS -->
                    <div class="rule-row-line">

                        <label class="mini-label">
                            Aplicar en:
                        </label>

                        <div class="mini-checks rule-dep-entornos">

                            <label>
                                <input type="checkbox" value="laboral">
                                Lab
                            </label>

                            <label>
                                <input type="checkbox" value="educativo">
                                Edu
                            </label>

                            <label>
                                <input type="checkbox" value="comunitario">
                                Com
                            </label>

                            <label>
                                <input type="checkbox" value="institucional">
                                Ins
                            </label>

                        </div>

                    </div>

                    <!-- CUANDO CUMPLE -->
                    <div class="rule-row-line">

                        <label class="mini-label">
                            Cuando cumple:
                        </label>

                        <select class="rule-dep-when-true field-input">

                            <option value="required">
                                Obligatorio
                            </option>

                            <option value="optional">
                                Opcional
                            </option>

                        </select>

                    </div>

                    <!-- CUANDO NO CUMPLE -->
                    <div class="rule-row-line">

                        <label class="mini-label">
                            Cuando NO cumple:
                        </label>

                        <select class="rule-dep-when-false field-input">

                            <option value="ignore">
                                Ignorar
                            </option>

                            <option value="error">
                                Error si tiene valor
                            </option>

                        </select>

                    </div>

                    <!-- MENSAJE -->
                    <div class="rule-row-line">

                        <label class="mini-label">
                            Mensaje:
                        </label>

                        <input type="text" class="rule-dep-msg field-input" placeholder="Mensaje de error"
                            style="flex:2;">

                    </div>

                </div>

                <!-- DEPENDENCIAS MULTIPLES -->
                <div class="rule-extra-multidep"
                    style="display:none; flex-direction:column; gap:8px; margin-top:8px;">

                    <div style="display:flex; align-items:center; justify-content:space-between;">

                        <span
                            style="
                            font-size:10px;
                            font-family:'Space Mono', monospace;
                            color:var(--accent4);
                            letter-spacing:1px;
                            text-transform:uppercase;
                        ">
                            🔗 Dependencias múltiples
                        </span>

                        <button class="btn-sm-accent" onclick="addMultiDepRow(this)">
                            + Condición
                        </button>

                    </div>

                    <div class="multi-dep-container"></div>

                    <div class="rule-row-line">

                        <label class="mini-label">
                            Cuando cumple:
                        </label>

                        <select class="rule-multi-true field-input">

                            <option value="required">
                                Obligatorio
                            </option>

                            <option value="optional">
                                Opcional
                            </option>

                        </select>

                    </div>

                    <div class="rule-row-line">

                        <label class="mini-label">
                            Cuando NO cumple:
                        </label>

                        <select class="rule-multi-false field-input">

                            <option value="ignore">
                                Ignorar
                            </option>

                            <option value="error">
                                Error si tiene valor
                            </option>

                        </select>

                    </div>

                    <div class="rule-row-line">

                        <label class="mini-label">
                            Mensaje:
                        </label>

                        <input type="text" class="rule-multi-msg field-input" placeholder="Mensaje de error"
                            style="flex:2;">

                    </div>

                </div>

                <!-- REGLAS POR ENTORNO -->
                <div class="rule-extra-entorno" style="display:none; flex-direction:column; gap:8px; margin-top:8px;">

                    <div style="display:flex; align-items:center; gap:4px;">

                        <span
                            style="
                            font-size:10px;
                            font-family:'Space Mono', monospace;
                            color:var(--accent2);
                            letter-spacing:1px;
                            text-transform:uppercase;
                        ">
                            🌍 Reglas por entorno
                        </span>

                    </div>

                    <div class="rule-row-line">

                        <label class="mini-label">
                            Laboral:
                        </label>

                        <input type="text" class="rule-env-laboral field-input" placeholder="Ej: No aplica"
                            style="flex:1;">

                    </div>

                    <div class="rule-row-line">

                        <label class="mini-label">
                            Educativo:
                        </label>

                        <input type="text" class="rule-env-educativo field-input" placeholder="Ej: No aplica"
                            style="flex:1;">

                    </div>

                    <div class="rule-row-line">

                        <label class="mini-label">
                            Comunitario:
                        </label>

                        <input type="text" class="rule-env-comunitario field-input" placeholder="Ej: No aplica"
                            style="flex:1;">

                    </div>

                    <div class="rule-row-line">

                        <label class="mini-label">
                            Institucional:
                        </label>

                        <input type="text" class="rule-env-institucional field-input" placeholder="Ej: No aplica"
                            style="flex:1;">

                    </div>

                </div>

                <!-- EXCEPTO SI -->
                <div class="rule-extra-excepto" style="display:none; flex-direction:column; gap:8px; margin-top:8px;">

                    <div style="display:flex; align-items:center; gap:4px;">

                        <span
                            style="
                            font-size:10px;
                            font-family:'Space Mono', monospace;
                            color:var(--accent3);
                            letter-spacing:1px;
                            text-transform:uppercase;
                        ">
                            ⚠ Excepto si
                        </span>

                    </div>

                    <div class="rule-row-line">

                        <label class="mini-label">
                            Campo:
                        </label>

                        <input type="text" class="rule-exc-campo field-input" placeholder="Campo referencia"
                            style="flex:2;">

                    </div>

                    <div class="rule-row-line">

                        <label class="mini-label">
                            Valor:
                        </label>

                        <input type="text" class="rule-exc-valor field-input"
                            placeholder="Valor que desactiva obligatoriedad" style="flex:2;">

                    </div>

                </div>

                <!-- FECHAS -->
                <div class="rule-extra-fecha" style="display:none; flex-direction:column; gap:8px; margin-top:8px;">

                    <div style="display:flex; align-items:center; gap:4px;">

                        <span
                            style="
                            font-size:10px;
                            font-family:'Space Mono', monospace;
                            color:var(--accent4);
                            letter-spacing:1px;
                            text-transform:uppercase;
                        ">
                            📅 Validaciones fecha
                        </span>

                    </div>

                    <div class="rule-row-line">

                        <label>
                            <input type="checkbox" class="rule-max-hoy">
                            No permitir fechas futuras
                        </label>

                    </div>

                    <div class="rule-row-line">

                        <label class="mini-label">
                            Comparar con:
                        </label>

                        <input type="text" class="rule-comp-campo field-input"
                            placeholder="Campo fecha referencia" style="flex:1;">

                        <select class="rule-comp-op field-input" style="width:90px;">
                            <option value=">=">>=</option>
                            <option value="<=">
                                <=< /option>
                            <option value=">">></option>
                            <option value="<">
                                << /option>
                        </select>

                    </div>

                    <div class="rule-row-line">

                        <label class="mini-label">
                            Mensaje:
                        </label>

                        <input type="text" class="rule-comp-msg field-input" placeholder="Mensaje de error"
                            style="flex:2;">

                    </div>

                </div>

                <!-- DESCRIPCION -->
                <div class="rule-row-line" style="margin-top:8px;">

                    <label class="mini-label">
                        Descripción:
                    </label>

                    <input type="text" class="rule-desc field-input" placeholder="Ej: Tipo de documento"
                        style="flex:1;">

                </div>

            </div>

        </div>

    </template>

    <template id="multi-dep-row-tpl">

        <div class="multi-dep-row" style="display:flex; gap:8px; align-items:center;">

            <input type="text" class="multi-dep-campo field-input" placeholder="Campo referencia"
                style="flex:2;">

            <input type="text" class="multi-dep-valores field-input" placeholder="Valores: 1|2|3"
                style="flex:2;">

            <button class="btn-icon-danger" onclick="removeMultiDepRow(this)">
                ✕
            </button>

        </div>

    </template>

    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/exceljs@4.4.0/dist/exceljs.min.js"></script>

    <script>

        let userTokens = @json($tokens ?? 0);
        
        // Opcional: Si quieres mostrar los tokens en la interfaz al cargar la página
        document.addEventListener("DOMContentLoaded", function() {
            const tokensDisplay = document.getElementById('tokens-counter');
            if (tokensDisplay) {
                tokensDisplay.textContent = userTokens;
            }
            if (typeof checkTokensThreshold === 'function') checkTokensThreshold();
        });
    </script>

</body>

</html>
