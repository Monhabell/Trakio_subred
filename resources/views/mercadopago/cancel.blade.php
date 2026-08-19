<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>ODIN — Pago Cancelado</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700;900&family=Share+Tech+Mono&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --red:       #e53030;
            --red2:      #ff4444;
            --red3:      #ff7777;
            --red-glow:  rgba(229,48,48,0.35);
            --amber:     #ffcc00;
            --amber-dim: rgba(255,204,0,0.15);
            --bg:        #030303;
            --bg2:       #0a0a0a;
            --bg3:       #111;
            --text:      #f0f0f0;
            --muted:     #888;
            --border:    rgba(255,255,255,0.07);
            --mono:      'Share Tech Mono', monospace;
            --head:      'Orbitron', sans-serif;
        }

        html, body {
            background: var(--bg);
            color: var(--text);
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* Grilla de fondo */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image:
                linear-gradient(rgba(255,255,255,0.012) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.012) 1px, transparent 1px);
            background-size: 44px 44px;
            pointer-events: none;
            z-index: 0;
        }

        /* Radial ambiental — ahora más rojo/naranja, señal de error */
        body::after {
            content: '';
            position: fixed;
            inset: 0;
            background: radial-gradient(circle at 50% 45%, rgba(229,48,48,0.12) 0%, transparent 60%);
            pointer-events: none;
            z-index: 0;
        }

        /* ── NAV ── */
        nav {
            position: fixed;
            top: 0; left: 0; right: 0;
            z-index: 100;
            padding: 1.25rem 3rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: rgba(3,3,3,0.88);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--border);
        }

        .nav-logo {
            font-family: var(--head);
            font-size: 20px;
            font-weight: 900;
            color: #fff;
            letter-spacing: 5px;
            text-decoration: none;
        }
        .nav-logo span { color: var(--red2); }

        .nav-back {
            background: transparent;
            border: 1px solid var(--border);
            color: var(--muted);
            font-family: var(--head);
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
            padding: 0.55rem 1.25rem;
            cursor: pointer;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: 0.2s;
        }
        .nav-back:hover { border-color: var(--red); color: #fff; }

        /* ── STAGE ── */
        .stage {
            position: relative;
            z-index: 10;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 100px 1.5rem 4rem;
        }

        /* ── CARD ── */
        .error-card {
            background: var(--bg2);
            border: 1px solid rgba(229,48,48,0.25);
            width: 100%;
            max-width: 560px;
            position: relative;
            padding: 3.5rem 3rem;
            animation: cardIn 0.8s cubic-bezier(0.16,1,0.3,1) both;
        }

        @keyframes cardIn {
            from { opacity: 0; transform: translateY(40px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* Esquinas decorativas en rojo intenso */
        .error-card::before,
        .error-card::after {
            content: '';
            position: absolute;
            width: 14px; height: 14px;
            border-color: var(--red2);
            border-style: solid;
        }
        .error-card::before { top: -1px;    left: -1px;  border-width: 2px 0 0 2px; }
        .error-card::after  { bottom: -1px; right: -1px; border-width: 0 2px 2px 0; }

        /* Línea de alerta que parpadea en el borde superior */
        .alert-line {
            position: absolute;
            top: -1px; left: 0; right: 0;
            height: 2px;
            background: linear-gradient(90deg, transparent, var(--red2), var(--amber), var(--red2), transparent);
            animation: alertPulse 2s ease-in-out infinite;
        }
        @keyframes alertPulse {
            0%, 100% { opacity: 1; }
            50%       { opacity: 0.3; }
        }

        /* ── ÍCONO DE ERROR ── */
        .icon-ring {
            width: 80px; height: 80px;
            border: 1px solid rgba(229,48,48,0.3);
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 2rem;
            position: relative;
            animation: ringIn 0.6s cubic-bezier(0.16,1,0.3,1) 0.3s both;
        }
        @keyframes ringIn {
            from { opacity: 0; transform: scale(0.5) rotate(-20deg); }
            to   { opacity: 1; transform: scale(1) rotate(0deg); }
        }
        /* Anillo exterior más tenue */
        .icon-ring::before {
            content: '';
            position: absolute;
            inset: -7px;
            border: 1px solid rgba(229,48,48,0.1);
        }
        .icon-ring i {
            font-size: 36px;
            color: var(--red2);
            filter: drop-shadow(0 0 10px var(--red-glow));
            animation: iconShake 0.5s ease 0.9s both;
        }
        @keyframes iconShake {
            0%,100% { transform: translateX(0); }
            20%     { transform: translateX(-5px); }
            40%     { transform: translateX(5px); }
            60%     { transform: translateX(-3px); }
            80%     { transform: translateX(3px); }
        }

        /* ── TEXTOS ── */
        .status-label {
            font-family: var(--mono);
            font-size: 11px;
            letter-spacing: 3px;
            color: var(--red3);
            text-transform: uppercase;
            text-align: center;
            margin-bottom: 0.75rem;
            animation: fadeUp 0.6s ease 0.5s both;
        }

        .error-title {
            font-family: var(--head);
            font-size: 1.85rem;
            font-weight: 900;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: -0.5px;
            color: #fff;
            margin-bottom: 0.75rem;
            animation: fadeUp 0.6s ease 0.6s both;
        }

        .error-sub {
            text-align: center;
            font-size: 14px;
            color: var(--muted);
            line-height: 1.65;
            margin-bottom: 2.5rem;
            animation: fadeUp 0.6s ease 0.7s both;
        }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(12px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* ── DIVIDER ── */
        .divider {
            border: none;
            border-top: 1px solid var(--border);
            margin-bottom: 2rem;
            animation: fadeUp 0.6s ease 0.75s both;
        }

        /* ── MOTIVOS COMUNES ── */
        .reasons-block {
            background: rgba(229,48,48,0.04);
            border: 1px solid rgba(229,48,48,0.12);
            padding: 1.25rem 1.5rem;
            margin-bottom: 2rem;
            animation: fadeUp 0.6s ease 0.8s both;
        }

        .reasons-title {
            font-family: var(--mono);
            font-size: 10px;
            letter-spacing: 2px;
            color: var(--red3);
            text-transform: uppercase;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .reasons-list {
            display: flex;
            flex-direction: column;
            gap: 0.6rem;
        }

        .reason-item {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 13px;
            color: var(--muted);
            font-family: var(--mono);
            letter-spacing: 0.5px;
        }
        .reason-item i {
            font-size: 14px;
            color: var(--red);
            flex-shrink: 0;
        }

        /* ── BADGE DE ESTADO ── */
        .state-badge {
            background: rgba(229,48,48,0.06);
            border: 1px solid rgba(229,48,48,0.2);
            padding: 0.5rem 1rem;
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 2.5rem;
            animation: fadeUp 0.6s ease 0.9s both;
        }
        .badge-dot {
            width: 7px; height: 7px;
            background: var(--red2);
            border-radius: 50%;
            box-shadow: 0 0 8px var(--red-glow);
            flex-shrink: 0;
            animation: pulse 1.5s ease-in-out infinite;
        }
        @keyframes pulse {
            0%,100% { opacity: 1; }
            50%      { opacity: 0.3; }
        }
        .badge-text {
            font-family: var(--mono);
            font-size: 11px;
            letter-spacing: 1.5px;
            color: var(--red3);
            text-transform: uppercase;
        }

        /* ── BOTONES ── */
        .btn-group {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.75rem;
            animation: fadeUp 0.6s ease 1s both;
        }

        .btn-primary {
            background: var(--red);
            color: #fff;
            border: none;
            padding: 1rem;
            font-family: var(--head);
            font-weight: 900;
            font-size: 11px;
            letter-spacing: 2px;
            text-transform: uppercase;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            background: var(--red2);
            box-shadow: 0 0 20px var(--red-glow);
        }

        .btn-secondary {
            background: transparent;
            color: var(--muted);
            border: 1px solid var(--border);
            padding: 1rem;
            font-family: var(--head);
            font-weight: 700;
            font-size: 11px;
            letter-spacing: 2px;
            text-transform: uppercase;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            text-decoration: none;
            transition: all 0.2s ease;
        }
        .btn-secondary:hover { border-color: rgba(255,255,255,0.2); color: #fff; }

        /* ── PIE ── */
        .card-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 1.75rem;
            padding-top: 1.25rem;
            border-top: 1px solid var(--border);
            font-family: var(--mono);
            font-size: 10px;
            color: var(--muted);
            letter-spacing: 1px;
            animation: fadeUp 0.6s ease 1.1s both;
        }
        .footer-dot {
            width: 5px; height: 5px;
            background: var(--amber);
            border-radius: 50%;
            display: inline-block;
            margin-right: 6px;
            box-shadow: 0 0 6px var(--amber);
        }

        /* ── PARTÍCULAS (caen en lugar de subir — sensación de fallo) ── */
        .particles {
            position: fixed;
            inset: 0;
            z-index: 1;
            pointer-events: none;
            overflow: hidden;
        }
        .particle {
            position: absolute;
            width: 2px; height: 2px;
            border-radius: 50%;
            animation: fallDown linear infinite;
            opacity: 0;
        }
        @keyframes fallDown {
            0%   { transform: translateY(-10vh) scale(0); opacity: 0; }
            10%  { opacity: 0.5; }
            90%  { opacity: 0.2; }
            100% { transform: translateY(110vh) scale(1); opacity: 0; }
        }
    </style>
</head>
<body>

    <!-- NAV -->
    <nav>
        <a href="/" class="nav-logo">Odin<span>.</span></a>
        <a href="/validator" class="nav-back">
            <i class="ti ti-arrow-left"></i>
            <span>Volver al Sistema</span>
        </a>
    </nav>

    <!-- PARTÍCULAS AMBIENTALES -->
    <div class="particles" id="particles"></div>

    <!-- CONTENIDO -->
    <div class="stage">
        <div class="error-card">
            <div class="alert-line"></div>

            <!-- Ícono -->
            <div class="icon-ring">
                <i class="ti ti-shield-x"></i>
            </div>

            <!-- Estado -->
            <div class="status-label">// TRANSACCIÓN INTERRUMPIDA</div>
            <h1 class="error-title">Pago No Completado</h1>
            <p class="error-sub">
                El proceso de pago fue cancelado o no pudo ser procesado.<br>
                No se realizó ningún cargo a tu cuenta.
            </p>

            <hr class="divider">

            <!-- Motivos comunes -->
            <div class="reasons-block">
                <div class="reasons-title">
                    <i class="ti ti-alert-triangle"></i>
                    Posibles causas
                </div>
                <div class="reasons-list">
                    <div class="reason-item">
                        <i class="ti ti-point-filled"></i>
                        <span>El proceso fue cancelado manualmente</span>
                    </div>
                    <div class="reason-item">
                        <i class="ti ti-point-filled"></i>
                        <span>Fondos insuficientes o tarjeta rechazada</span>
                    </div>
                    <div class="reason-item">
                        <i class="ti ti-point-filled"></i>
                        <span>Tiempo de sesión de pago agotado</span>
                    </div>
                    <div class="reason-item">
                        <i class="ti ti-point-filled"></i>
                        <span>Problema de conexión durante la transacción</span>
                    </div>
                </div>
            </div>

            <!-- Badge de estado -->
            <div class="state-badge">
                <div class="badge-dot"></div>
                <div class="badge-text">Sin cargo — ningún token fue descontado</div>
            </div>

            <!-- CTAs -->
            <div class="btn-group">
                <a href="{{ route('mp.checkout') }}" class="btn-primary">
                    <i class="ti ti-refresh"></i>
                    <span>Reintentar</span>
                </a>
                <a href="/validator" class="btn-secondary">
                    <i class="ti ti-home"></i>
                    <span>Ir al Sistema</span>
                </a>
            </div>

            <!-- Footer interno -->
            <div class="card-footer">
                <span><span class="footer-dot"></span>ESTADO: PAGO PENDIENTE</span>
                <span>SUITE v4.0</span>
            </div>
        </div>
    </div>

    <script>
        /* Partículas que caen (efecto fallo/descenso vs. éxito/ascenso) */
        (function () {
            var container = document.getElementById('particles');
            var count = 22;
            for (var i = 0; i < count; i++) {
                var p = document.createElement('div');
                p.className = 'particle';
                p.style.left = Math.random() * 100 + 'vw';
                var size = (Math.random() * 2 + 1) + 'px';
                p.style.width  = size;
                p.style.height = size;
                p.style.animationDuration = (Math.random() * 10 + 8) + 's';
                p.style.animationDelay    = (Math.random() * 8) + 's';
                /* Mezcla rojo con ámbar para diferenciar del estado de éxito */
                p.style.background = Math.random() > 0.6 ? '#ffcc00' : '#ff4444';
                container.appendChild(p);
            }
        })();
    </script>
</body>
</html>