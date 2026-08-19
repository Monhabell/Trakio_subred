<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>ODIN — Automatice Trakio</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700;900&family=Share+Tech+Mono&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css">
    @vite('resources/css/odin/odin.css')
</head>

<body>
    <nav>
        <div class="nav-logo">Odin<span>.</span></div>
        <div class="nav-links">
            <a href="{{ route('app.home') }}">Trakio</a>
            <a href="#" class="active">Inicio</a>
            <a href="#caracteristicas">Características</a>
            <a href="#planes">Planes</a>
            <div class="dropdown">
            <a href="#" class="dropdown-toggle">
                Aplicaciones ▾
            </a>
            <div class="dropdown-menu">
                <a href="{{ route('cronicos.v2') }}">Cronicos</a>
                <a href="{{ route('cronicos.lab') }}">Cronicos (Lab)</a>
                <a href="{{ route('validator') }}">Validador</a>
                <a href="{{ route('reportesGesi') }}">Reportes GESI</a>
                <a href="{{ route('dataLaboral') }}">Indicadores Laborales</a>
                {{-- <a href="{{ route('tamizajeLaboral') }}">Trazabilidad de Tamizajes</a> --}}
            </div>
        </div>
            
        </div>
        
        <form action="{{ route('reset.license') }}" method="POST" style="display: inline;">
            @csrf
            <button class="theme-btn" type="submit" >Descargar</button>
            
        </form>

        <button class="theme-btn" id="themeToggle">
            <span class="theme-icon" id="themeIcon">🌙</span>
            <span id="themeLabel">Modo oscuro</span>
        </button>
    </nav>

    <section class="hero">
        <div class="hero-container">
            
            <div class="hero-left reveal-left">
                <span class="tagline">// SISTEMA DE AUTOMATIZACIÓN DE AVANZADA</span>
                <h1>Domine y Optimice <span>Odin</span></h1>
                <p>La herramienta definitiva de automatización diseñada para maximizar la productividad y acelerar sus flujos de trabajo con precisión absoluta.</p>
            </div>

            <div class="activation-panel reveal-right">
                <div class="panel-title">
                    <i class="ti ti-terminal-2"></i>
                    <span>Consola de Acceso</span>
                </div>

                <div class="action-tabs">
                    <button class="tab-btn active" id="tab-activate" onclick="switchTab('activate')">Codigo de Activación</button>
                    <button class="tab-btn" id="tab-download" onclick="switchTab('download')">Descarga Directa</button>
                </div>

                <div id="content-activate" class="tab-content active">

                

                    @if(!$licenceData)

                        <div id="control-block">
                            <div class="input-group">
                                <i class="ti ti-key"></i>
                                    <input 
                                        type="text" 
                                        id="lic-input" 
                                        class="input-control"
                                        placeholder="Aun no tienes un codigo de activacin, adquiere una licencia para obtenerlo"
                                        spellcheck="false"
                                        readonly
                                    />
                            </div>
                        </div>
                    @else
                        <div id="console-block" class="console-box">
                            <div class="code-title">
                                <i class="ti ti-shield-check" style="color:#00ff66;"></i>
                                <span>LICENCIA DETECTADA CORRECTAMENTE</span>
                            </div>
                            <div class="code-display" id="code-val">{{$licenceData->license_key}}</div>
                            
                            <div class="code-stats">
                                <div>
                                    <div class="code-stat-label">
                                        <span>TIEMPO RESTANTE</span>
                                        <span id="dias-val" class="code-stat-val">
                                            {{ $daysLeft > 0 ? $daysLeft . ' días' : 'Expirada' }}
                                        </span>
                                    </div>
                                    <div class="progress-bar-bg">
                                        <div id="prog-dias" class="progress-bar-fill" style="width: {{ $progress }}%">{{ $progress }}%</div>
                                    </div>
                                </div>
                                <div>
                                    <div class="code-stat-label">
                                        <span>TOKENS ACTIVOS</span>
                                        <span id="tok-val" class="code-stat-val">{{$licenceData->tokens_available}}</span>
                                    </div>
                                    <div class="progress-bar-bg">
                                        <div id="prog-tok" class="progress-bar-fill" style="width: {{ $progress }}%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <div id="content-download" class="tab-content">
                    <div class="download-box">
                        <p>Obtenga el instalador oficial empaquetado y seguro para su sistema.</p>
                        <span class="download-version">VERSIÓN ACTUAL: v4.0.2 (ESTABLE)</span>
                    </div>
                    <form action="{{ route('reset.license') }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" 
                            class="theme-btn">
                            <i class="ti ti-download"></i>
                            <span>DESCARGAR INSTALADOR .EXE</span>
                        </button>
                    </form>
                </div>

                <div class="system-status">
                    <div class="status-item">
                        <div class="status-dot"></div>
                        <span>SISTEMA: OPERATIVO</span>
                    </div>
                    <div class="status-item">
                        <span>SUITE: v4.0</span>
                    </div>
                </div>
            </div>

        </div>
    </section>

    <section id="caracteristicas" class="info-sec reveal">
        <div class="sec-head">
            <span>// RENDIMIENTO COMPROBADO</span>
            <h2>Características Clave</h2>
        </div>
        <div class="grid-3">
            <div class="feat-card">
                <i class="ti ti-bolt feat-icon"></i>
                <h3>Validador de Calidad del Dato</h3>
                <p>Valida errores en bases de datos según las reglas del instructivo 2026.
                            Multi-formato, multi-entorno, verificación paso a paso.</p>
            </div>
            <div class="feat-card">
                <i class="ti ti-shield-lock feat-icon"></i>
                <h3>Análisis crónicos - Cruce Triple</h3>
                <p>Genera análisis de cruce triple UPZ × Edad × Género × Riesgo en segundos.
                            Combina bases de Personas, Tamizajes y Fichas.</p>
                            <ul class="feat-points">
                            <li>Cruce: UPZ × Edad × Género × Riesgo (FINDRISC)</li>
                            <li>Tres bases simultáneas: Personas, Tamizajes, Fichas</li>
                            <li>Columnas configurables por nombre</li>
                            <li>Exportación de resultados a Excel</li>
                        </ul>
            </div>
            <div class="feat-card">
                <i class="ti ti-refresh feat-icon"></i>
                <h3>Sincronía en Tiempo Real</h3>
                <p>Monitoreo constante de variables y métricas directo en su consola táctica con reconexión inteligente automatizada.</p>
            </div>
        </div>
    </section>

    <!-- PLANS -->
    <section class="info-sec reveal visible" id="planes">
        <div class="container">
            <div class="reveal" style="text-align:center;margin-bottom:.5rem">
                <div class="section-label" style="justify-content:center;display:flex">▸ PLANES Y PRECIOS</div>
                <h2 class="section-h2" style="max-width:100%;text-align:center">Escala según tus <em>necesidades</em>
                </h2>
                <p style="color:var(--muted);font-size:14px">Por usuario · mes · pago en COP</p>
            </div>
            <div class="plans-grid reveal">
                <div class="plan-card free">
                    <div class="plan-name">FREE</div>
                    <div class="plan-price">$0</div>
                    <div class="plan-period">15 días de prueba</div>
                    <div class="plan-tokens"><i class="ti ti-bolt"></i> 100 tokens</div>
                    <ul class="plan-feats">
                        <li class="on"><i class="ti ti-circle-check ck"></i>Fichas en HC</li>
                        <li><i class="ti ti-circle-x xx"></i>Validación BBDD</li>
                        <li><i class="ti ti-circle-x xx"></i>Reportes crónicos</li>
                        <li><i class="ti ti-circle-x xx"></i>Comp. derecho</li>
                        <li><i class="ti ti-circle-x xx"></i>Corrección GESIForm</li>
                    </ul>
                    <button class="plan-btn-Active">ACTIVO</button>

                </div>
                <div class="plan-card">
                    <div class="plan-name">STARTER</div>
                    <div class="plan-price"><sup>$</sup>9.900</div>
                    <div class="plan-period">por mes</div>
                    <div class="plan-tokens"><i class="ti ti-bolt"></i> 1.000 tokens</div>
                    <ul class="plan-feats">
                        <li><i class="ti ti-circle-x xx"></i>Fichas en HC</li>
                        <li class="on"><i class="ti ti-circle-check ck"></i>Validación BBDD</li>
                        <li><i class="ti ti-circle-x xx"> </i> Reportes crónicos</li>
                        <li class="on"><i class="ti ti-circle-check ck"> </i>Comp. derecho</li>
                        <li><i class="ti ti-circle-x xx"></i>Corrección GESIForm</li>
                    </ul> 
                    <button class="plan-btn2" onclick="createPreference('starter')">COMPRAR</button>
                </div>
                <div class="plan-card hot">
                    <div class="plan-name">PRO</div>
                    <div class="plan-price"><sup>$</sup>19.900</div>
                    <div class="plan-period">por mes</div>
                    <div class="plan-tokens"><i class="ti ti-bolt"></i> 5.000 tokens</div>
                    <ul class="plan-feats">
                        <li><i class="ti ti-circle-x xx"></i>Fichas en HC</li>
                        <li class="on"><i class="ti ti-circle-check ck"></i>Validación BBDD</li>
                        <li class="on"><i class="ti ti-circle-check ck"></i>Reportes crónicos</li>
                        <li class="on"><i class="ti ti-circle-check ck"></i>Comp. derecho</li>
                        <li><i class="ti ti-circle-x xx"></i>Corrección GESIForm</li>
                    </ul>
                    <button class="plan-btn2" onclick="createPreference('pro')">COMPRAR</button>
                </div>
                <div class="plan-card">
                    <div class="plan-name">BUSINESS</div>
                    <div class="plan-price"><sup>$</sup>39.900</div>
                    <div class="plan-period">por mes · todo activo</div>
                    <div class="plan-tokens"><i class="ti ti-bolt"></i> 20.000 tokens</div>
                    <ul class="plan-feats">
                        <li class="on"><i class="ti ti-circle-check ck"></i>Fichas en HC</li>
                        <li class="on"><i class="ti ti-circle-check ck"></i>Validación BBDD</li>
                        <li class="on"><i class="ti ti-circle-check ck"></i>Reportes crónicos</li>
                        <li class="on"><i class="ti ti-circle-check ck"></i>Comp. derecho</li>
                        <li class="on"><i class="ti ti-circle-check ck"></i>Corrección GESIForm</li>
                    </ul>
                    <button class="plan-btn2" onclick="createPreference('business')">COMPRAR</button>
                </div>
            </div>
        </div>

        
    </section>

    <!-- COMPARE -->
    <section class="info-sec reveal visible" id="comparativa">
        <div class="container reveal">
            <div class="section-label">▸ COMPARATIVA</div>
            <h2 class="section-h2">¿Qué incluye <em>cada plan</em>?</h2>
            <div style="overflow-x:auto">
                <table class="ctable">
                    <thead>
                        <tr>
                            <th>Funcionalidad</th>
                            <th>Free</th>
                            <th>Starter</th>
                            <th class="hl">Pro ★</th>
                            <th>Business</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Validación de BBDD</td>
                            <td class="tv">100 tok</td>
                            <td class="tv">1k tok</td>
                            <td class="hcol tv">5k tok</td>
                            <td class="tv">20k tok</td>
                        </tr>
                        <tr>
                            <td>Reportes de crónicos</td>
                            <td class="tv">100 tok</td>
                            <td class="tv">1k tok</td>
                            <td class="hcol tv">5k tok</td>
                            <td class="tv">20k tok</td>
                        </tr>
                        <tr>
                            <td>Comprobador de derecho</td>
                            <td class="xx2"><i class="ti ti-x"></i></td>
                            <td class="xx2"><i class="ti ti-x"></i></td>
                            <td class="hcol ck2"><i class="ti ti-check"></i></td>
                            <td class="ck2"><i class="ti ti-check"></i></td>
                        </tr>
                        <tr>
                            <td>Creación fichas HC</td>
                            <td class="xx2"><i class="ti ti-x"></i></td>
                            <td class="xx2"><i class="ti ti-x"></i></td>
                            <td class="hcol xx2"><i class="ti ti-x"></i></td>
                            <td class="ck2"><i class="ti ti-check"></i></td>
                        </tr>
                        <tr>
                            <td>Corrección GESIForm</td>
                            <td class="xx2"><i class="ti ti-x"></i></td>
                            <td class="xx2"><i class="ti ti-x"></i></td>
                            <td class="hcol xx2"><i class="ti ti-x"></i></td>
                            <td class="ck2"><i class="ti ti-check"></i></td>
                        </tr>
                        <tr>
                            <td>Activación completa</td>
                            <td style="font-family:var(--mono);font-size:11px;color:var(--muted)">15 días</td>
                            <td class="xx2"><i class="ti ti-x"></i></td>
                            <td class="hcol xx2"><i class="ti ti-x"></i></td>
                            <td class="ck2"><i class="ti ti-check"></i></td>
                        </tr>
                        <tr>
                            <td><strong>Precio / mes</strong></td>
                            <td style="font-family:var(--mono);color:#fff;font-weight:600">$0</td>
                            <td style="font-family:var(--mono);color:#fff">$19.900</td>
                            <td class="hcol" style="font-family:var(--mono);color:var(--red2);font-weight:600">
                                $49.900</td>
                            <td style="font-family:var(--mono);color:#fff">$99.900</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>


    <script>
        function switchTab(type) {
            document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));

            if (type === 'activate') {
                document.getElementById('tab-activate').classList.add('active');
                document.getElementById('content-activate').classList.add('active');
            } else if (type === 'download') {
                document.getElementById('tab-download').classList.add('active');
                document.getElementById('content-download').classList.add('active');
            }
        }

        function validarLicencia() {
            var input = document.getElementById('lic-input').value.trim();
            var em = document.getElementById('error-msg');
            var cb = document.getElementById('console-block');
            var ctrl = document.getElementById('control-block');

            if (input === "") {
                em.classList.add('show');
                cb.classList.remove('show');
                return;
            }

            document.getElementById('code-val').textContent = input.toUpperCase();
            em.classList.remove('show');
            cb.classList.add('show');

            ctrl.style.opacity = '0.5';
            ctrl.style.pointerEvents = 'none';

            setTimeout(function() {
                document.getElementById('dias-val').textContent = '11 días';
                document.getElementById('dias-val').className = 'code-stat-val ok';
                document.getElementById('prog-dias').style.width = '73%';

                document.getElementById('tok-val').textContent = '38 / 100';
                document.getElementById('tok-val').className = 'code-stat-val warn';
                document.getElementById('prog-tok').style.width = '38%';
            }, 200);
        }

        // Animación progresiva por Scroll (IntersectionObserver original)
        var obs = new IntersectionObserver(function(entries) {
            entries.forEach(function(e) {
                if (e.isIntersecting) e.target.classList.add('visible');
            });
        }, {
            threshold: .15
        });
        document.querySelectorAll('.reveal,.reveal-left,.reveal-right').forEach(function(el) {
            obs.observe(el);
        });

        document.querySelectorAll('.plan-card').forEach(function(c, i) {
            c.style.transitionDelay = (i * 0.1) + 's';
        });

        setTimeout(function() {
            document.getElementById('prog-dias').style.transition = 'width 1.5s ease';
            document.getElementById('prog-tok').style.transition = 'width 1.5s ease';
        }, 200);
    </script>
    <script>
function createPreference(pkg){
  var token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
  fetch('{{ route('mp.createPreference') }}', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': token
    },
    body: JSON.stringify({package: pkg})
  })
  .then(function(r){ return r.json(); })
  .then(function(data){ 
    if(data && data.init_point){ 
      window.location = data.init_point; 
    } else { 
      alert('Error creando preferencia'); 
      console.error(data); 
    } 
  })
  .catch(function(e){ 
    console.error(e); 
    alert('Error de conexión'); 
  });
}

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
</script>
</body>

</html>