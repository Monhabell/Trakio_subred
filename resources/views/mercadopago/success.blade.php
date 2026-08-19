<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>ODIN — Pago Aprobado</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700;900&family=Share+Tech+Mono&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --red:      #e53030;
            --red2:     #ff4444;
            --red3:     #ff7777;
            --red-glow: rgba(229,48,48,0.35);
            --green:    #00ff66;
            --bg:       #030303;
            --bg2:      #0a0a0a;
            --bg3:      #111;
            --text:     #f0f0f0;
            --muted:    #888;
            --border:   rgba(255,255,255,0.07);
            --mono:     'Share Tech Mono', monospace;
            --head:     'Orbitron', sans-serif;
        }

        html, body {
            background: var(--bg);
            color: var(--text);
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* ── GRID DE FONDO ── */
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

        /* ── RADIAL AMBIENTAL ── */
        body::after {
            content: '';
            position: fixed;
            inset: 0;
            background: radial-gradient(circle at 50% 45%, rgba(229,48,48,0.07) 0%, transparent 65%);
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

        /* ── CARD PRINCIPAL ── */
        .success-card {
            background: var(--bg2);
            border: 1px solid var(--border);
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

        /* Esquinas decorativas */
        .success-card::before,
        .success-card::after {
            content: '';
            position: absolute;
            width: 14px; height: 14px;
            border-color: var(--red);
            border-style: solid;
        }
        .success-card::before { top: -1px; left: -1px; border-width: 2px 0 0 2px; }
        .success-card::after  { bottom: -1px; right: -1px; border-width: 0 2px 2px 0; }

        /* Línea de escáner animada */
        .scan-line {
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 2px;
            background: linear-gradient(90deg, transparent, var(--red), transparent);
            animation: scan 3s ease-in-out 0.5s both;
        }
        @keyframes scan {
            0%   { top: 0;    opacity: 1; }
            80%  { top: 100%; opacity: 1; }
            100% { top: 100%; opacity: 0; }
        }

        /* ── ÍCONO DE ÉXITO ── */
        .icon-ring {
            width: 80px; height: 80px;
            border: 1px solid rgba(0,255,102,0.25);
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 2rem;
            position: relative;
            animation: ringIn 0.6s cubic-bezier(0.16,1,0.3,1) 0.3s both;
        }
        @keyframes ringIn {
            from { opacity: 0; transform: scale(0.5); }
            to   { opacity: 1; transform: scale(1); }
        }
        .icon-ring::before {
            content: '';
            position: absolute;
            inset: -6px;
            border: 1px solid rgba(0,255,102,0.1);
        }
        .icon-ring i {
            font-size: 36px;
            color: var(--green);
            filter: drop-shadow(0 0 8px rgba(0,255,102,0.5));
        }

        /* ── ETIQUETA ── */
        .status-label {
            font-family: var(--mono);
            font-size: 11px;
            letter-spacing: 3px;
            color: var(--green);
            text-transform: uppercase;
            text-align: center;
            margin-bottom: 0.75rem;
            animation: fadeUp 0.6s ease 0.5s both;
        }

        /* ── TÍTULO ── */
        .success-title {
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

        /* ── SUBTÍTULO ── */
        .success-sub {
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

        /* ── SEPARADOR ── */
        .divider {
            border: none;
            border-top: 1px solid var(--border);
            margin-bottom: 2rem;
            animation: fadeUp 0.6s ease 0.75s both;
        }

        /* ── DETALLES DE TRANSACCIÓN ── */
        .tx-rows {
            display: flex;
            flex-direction: column;
            gap: 0.85rem;
            margin-bottom: 2.5rem;
            animation: fadeUp 0.6s ease 0.8s both;
        }

        .tx-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 13px;
        }

        .tx-label {
            color: var(--muted);
            font-family: var(--mono);
            letter-spacing: 1px;
            font-size: 11px;
            text-transform: uppercase;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .tx-label i { font-size: 15px; color: var(--red3); }

        .tx-value {
            font-family: var(--mono);
            font-size: 13px;
            color: #fff;
            font-weight: bold;
            letter-spacing: 1px;
        }
        .tx-value.green { color: var(--green); }
        .tx-value.red   { color: var(--red2); }

        /* Barra de tokens */
        .tokens-bar-bg {
            background: rgba(255,255,255,0.05);
            height: 4px;
            width: 100%;
            margin-top: 6px;
            border-radius: 2px;
            overflow: hidden;
        }
        .tokens-bar-fill {
            height: 100%;
            background: var(--green);
            box-shadow: 0 0 8px rgba(0,255,102,0.5);
            border-radius: 2px;
            animation: barIn 1.2s cubic-bezier(0.16,1,0.3,1) 1.1s both;
        }
        @keyframes barIn {
            from { width: 0%; }
            to   { width: 100%; }
        }

        /* ── BADGE DE ESTADO ── */
        .state-badge {
            background: rgba(0,255,102,0.07);
            border: 1px solid rgba(0,255,102,0.2);
            padding: 0.5rem 1rem;
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 2.5rem;
            animation: fadeUp 0.6s ease 0.9s both;
        }
        .badge-dot {
            width: 7px; height: 7px;
            background: var(--green);
            border-radius: 50%;
            box-shadow: 0 0 8px var(--green);
            flex-shrink: 0;
            animation: pulse 2s ease-in-out 1.5s infinite;
        }
        @keyframes pulse {
            0%,100% { opacity: 1; }
            50%      { opacity: 0.4; }
        }
        .badge-text {
            font-family: var(--mono);
            font-size: 11px;
            letter-spacing: 1.5px;
            color: var(--green);
            text-transform: uppercase;
        }

        /* ── BOTÓN CTA ── */
        .btn-cta {
            width: 100%;
            background: var(--red);
            color: #fff;
            border: none;
            padding: 1.1rem;
            font-family: var(--head);
            font-weight: 900;
            font-size: 12px;
            letter-spacing: 3px;
            text-transform: uppercase;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            text-decoration: none;
            transition: all 0.3s ease;
            animation: fadeUp 0.6s ease 1s both;
        }
        .btn-cta:hover {
            background: var(--red2);
            box-shadow: 0 0 20px var(--red-glow);
        }

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
            background: var(--green);
            border-radius: 50%;
            display: inline-block;
            margin-right: 6px;
            box-shadow: 0 0 6px var(--green);
        }

        /* ── PARTÍCULAS ── */
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
            background: var(--red2);
            border-radius: 50%;
            animation: floatUp linear infinite;
            opacity: 0;
        }
        @keyframes floatUp {
            0%   { transform: translateY(100vh) scale(0); opacity: 0; }
            10%  { opacity: 0.6; }
            90%  { opacity: 0.3; }
            100% { transform: translateY(-20vh) scale(1.5); opacity: 0; }
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
        <div class="success-card">
            <div class="scan-line"></div>

            @php
                /*
                 * MercadoPago envía los datos como query string en la URL de retorno.
                 * Ejemplo de external_reference: "user_1|tokens_500"
                 * Lo parseamos para extraer la cantidad de tokens comprados.
                 */
                $extRef    = request('external_reference', '');
                $paymentId = request('payment_id', '—');
                $status    = strtoupper(request('status', 'approved'));
                $orderRef  = request('merchant_order_id', '—');

                // Extraer tokens del formato "user_X|tokens_Y"
                $tokenCount = '—';
                foreach (explode('|', $extRef) as $part) {
                    if (str_starts_with(trim($part), 'tokens_')) {
                        $tokenCount = ltrim(trim($part), 'tokens_');
                        // Formato legible: "500 tokens"
                        $tokenCount = number_format((int) $tokenCount) . ' tokens';
                    }
                }

                // Método de pago legible
                $rawType = request('payment_type', '');
                $paymentTypeLabel = match($rawType) {
                    'account_money'  => 'Dinero en cuenta MP',
                    'credit_card'    => 'Tarjeta de crédito',
                    'debit_card'     => 'Tarjeta débito',
                    'ticket'         => 'Efectivo / ticket',
                    'bank_transfer'  => 'Transferencia bancaria',
                    default          => $rawType ?: '—',
                };
            @endphp

            <!-- Ícono -->
            <div class="icon-ring">
                <i class="ti ti-shield-check"></i>
            </div>

            <!-- Estado -->
            <div class="status-label">// TRANSACCIÓN VERIFICADA</div>
            <h1 class="success-title">Pago Aprobado</h1>
            <p class="success-sub">
                Tu compra ha sido procesada con éxito. Los tokens serán<br>
                acreditados en tu cuenta en los próximos instantes.
            </p>

            <hr class="divider">

            <!-- Filas de detalle con datos reales de MercadoPago -->
            <div class="tx-rows">

                {{-- Estado --}}
                <div class="tx-row">
                    <div class="tx-label">
                        <i class="ti ti-credit-card"></i>
                        Estado del pago
                    </div>
                    <div class="tx-value green">{{ $status }}</div>
                </div>

                {{-- Tokens --}}
                <div class="tx-row">
                    <div class="tx-label">
                        <i class="ti ti-coin"></i>
                        Tokens adquiridos
                    </div>
                    <div class="tx-value">{{ $tokenCount }}</div>
                </div>
                <div style="margin-top:-4px;">
                    <div class="tokens-bar-bg">
                        <div class="tokens-bar-fill"></div>
                    </div>
                </div>

                {{-- Método de pago --}}
                <div class="tx-row">
                    <div class="tx-label">
                        <i class="ti ti-wallet"></i>
                        Método de pago
                    </div>
                    <div class="tx-value" style="font-size:12px;">{{ $paymentTypeLabel }}</div>
                </div>

                {{-- ID de pago --}}
                <div class="tx-row">
                    <div class="tx-label">
                        <i class="ti ti-receipt"></i>
                        ID de pago
                    </div>
                    <div class="tx-value" style="font-size:11px; color:var(--muted);">
                        #{{ $paymentId }}
                    </div>
                </div>

                {{-- Orden --}}
                <div class="tx-row">
                    <div class="tx-label">
                        <i class="ti ti-hash"></i>
                        Orden MP
                    </div>
                    <div class="tx-value" style="font-size:11px; color:var(--muted);">
                        #{{ $orderRef }}
                    </div>
                </div>

                {{-- Fecha --}}
                <div class="tx-row">
                    <div class="tx-label">
                        <i class="ti ti-calendar"></i>
                        Fecha
                    </div>
                    <div class="tx-value" style="font-size:11px;">
                        {{ now()->format('d M Y · H:i') }}
                    </div>
                </div>

            </div>

            <!-- Badge de estado del sistema -->
            <div class="state-badge">
                <div class="badge-dot"></div>
                <div class="badge-text">Cuenta sincronizada — tokens disponibles para uso inmediato</div>
            </div>

            <!-- CTA -->
            <a href="{{ route('guest.index') }}" class="btn-cta">
                <i class="ti ti-terminal-2"></i>
                <span>Odin</span>
            </a>

            <a href="{{ route('validator') }}" class="btn-cta" style="margin-top: 1rem; background: transparent; border: 1px solid var(--border); color: var(--muted);">
                <i class="ti ti-arrow-right"></i>
                <span>Validadores</span>
            </a>

            <!-- Footer interno -->
            <div class="card-footer">
                <span><span class="footer-dot"></span>SISTEMA: OPERATIVO</span>
                <span>SUITE v4.0</span>
            </div>
        </div>
    </div>

    <script>
        /* Partículas flotantes ambientales */
        (function () {
            var container = document.getElementById('particles');
            var count = 28;
            for (var i = 0; i < count; i++) {
                var p = document.createElement('div');
                p.className = 'particle';
                p.style.left = Math.random() * 100 + 'vw';
                p.style.width  = (Math.random() * 2 + 1) + 'px';
                p.style.height = p.style.width;
                p.style.animationDuration  = (Math.random() * 12 + 8) + 's';
                p.style.animationDelay     = (Math.random() * 10) + 's';
                /* Algunas partículas en verde, la mayoría en rojo */
                if (Math.random() > 0.75) {
                    p.style.background = '#00ff66';
                }
                container.appendChild(p);
            }
        })();
    </script>
</body>
</html>