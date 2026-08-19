<!doctype html>
<html lang="es">
<head>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta charset="utf-8">
  <title>Planes y Precios - Escala según tus necesidades</title>
  <style>
    /* Estilos Generales / Modo Oscuro */
    body {
      font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
      padding: 40px 20px;
      background-color: #0b0b0c;
      color: #ffffff;
      margin: 0;
    }

    .container {
      max-width: 1200px;
      margin: 0 auto;
      text-align: center;
    }

    .subtitle {
      color: #9ca3af;
      text-transform: uppercase;
      font-size: 12px;
      letter-spacing: 2px;
      margin-bottom: 8px;
    }

    .title {
      font-size: 32px;
      font-weight: 700;
      margin: 0 0 8px 0;
    }

    .title em {
      font-style: italic;
      font-weight: 400;
      color: #ffffff;
    }

    .meta-info {
      color: #6b7280;
      font-size: 14px;
      margin-bottom: 40px;
    }

    /* Grid de Tarjetas */
    .pricing-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
      gap: 20px;
      align-items: stretch;
    }

    /* Tarjeta Base */
    .card {
      background-color: #111112;
      border: 1px solid #1f2023;
      border-radius: 12px;
      padding: 30px 24px;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      text-align: left;
      position: relative;
      transition: transform 0.2s, border-color 0.2s;
    }

    /* Variación Destacada (PRO) */
    .card.popular {
      border: 1px solid #e13636;
      box-shadow: 0px 0px 15px rgba(225, 54, 54, 0.1);
    }

    /* Etiqueta Más Popular */
    .badge {
      position: absolute;
      top: -12px;
      left: 50%;
      transform: translateX(-50%);
      background-color: #e13636;
      color: #ffffff;
      font-size: 10px;
      font-weight: bold;
      padding: 4px 12px;
      border-radius: 4px;
      letter-spacing: 1px;
      white-space: nowrap;
    }

    /* Encabezado de Tarjeta */
    .plan-name {
      color: #9ca3af;
      font-size: 14px;
      text-transform: uppercase;
      letter-spacing: 1.5px;
      margin-bottom: 16px;
    }

    .plan-price {
      font-size: 36px;
      font-family: 'Courier New', Courier, monospace; /* Fuente estilo digital/futurista */
      font-weight: bold;
      margin-bottom: 2px;
    }

    .plan-period {
      color: #6b7280;
      font-size: 13px;
      margin-bottom: 12px;
    }

    .plan-tokens {
      color: #ff5555;
      font-size: 13px;
      font-weight: 600;
      display: flex;
      align-items: center;
      gap: 4px;
      margin-bottom: 24px;
    }

    /* Línea divisoria */
    .divider {
      border-top: 1px solid #1f2023;
      margin-bottom: 24px;
    }

    /* Lista de Características */
    .features-list {
      list-style: none;
      padding: 0;
      margin: 0 margin-bottom: auto;
      flex-grow: 1;
    }

    .feature-item {
      display: flex;
      align-items: center;
      font-size: 13px;
      margin-bottom: 16px;
      color: #d1d5db;
    }

    /* Iconos Check / Cross */
    .icon {
      width: 16px;
      height: 16px;
      margin-right: 10px;
      flex-shrink: 0;
    }

    .icon-check {
      color: #10b981;
    }

    .icon-cross {
      color: #374151;
    }

    .disabled-text {
      color: #4b5563;
    }

    /* Botones */
    .btn {
      width: 100%;
      padding: 12px;
      background-color: transparent;
      border: 1px solid #2a2b2f;
      color: #9ca3af;
      border-radius: 8px;
      cursor: pointer;
      font-weight: 600;
      font-size: 13px;
      text-transform: uppercase;
      letter-spacing: 1px;
      transition: all 0.2s;
      margin-top: 24px;
    }

    .btn:hover {
      background-color: #1f2023;
      color: #ffffff;
      border-color: #4b5563;
    }

    /* Botón Destacado (PRO) */
    .btn-popular {
      background-color: #e13636;
      border: none;
      color: #ffffff;
    }

    .btn-popular:hover {
      background-color: #ff4747;
      color: #ffffff;
      box-shadow: 0px 4px 12px rgba(225, 54, 54, 0.3);
    }
  </style>
</head>
<body>

  <div class="container">
    <div class="subtitle">▸ Planes y Precios</div>
    <h2 class="title">Escala según tus <em>necesidades</em></h2>
    <div class="meta-info">Por usuario · mes · pago en COP</div>

    <div class="pricing-grid">
      
      <div class="card">
        <div>
          <div class="plan-name">Free</div>
          <div class="plan-price">$0</div>
          <div class="plan-period">15 días de prueba</div>
          <div class="plan-tokens">⚡ 100 tokens</div>
          <div class="divider"></div>
          
          <ul class="features-list">
            <li class="feature-item"><span class="icon icon-check">✓</span> Validación BBDD</li>
            <li class="feature-item"><span class="icon icon-check">✓</span> Reportes crónicos</li>
            <li class="feature-item disabled-text"><span class="icon icon-cross">✕</span> Comp. derecho</li>
            <li class="feature-item disabled-text"><span class="icon icon-cross">✕</span> Fichas en HC</li>
            <li class="feature-item disabled-text"><span class="icon icon-cross">✕</span> Corrección GESIForm</li>
          </ul>
        </div>
        <button class="btn" onclick="createPreference('free')">Activar Free</button>
      </div>

      <div class="card">
        <div>
          <div class="plan-name">Starter</div>
          <div class="plan-price">$19.900</div>
          <div class="plan-period">por mes</div>
          <div class="plan-tokens">⚡ 1.000 tokens</div>
          <div class="divider"></div>
          
          <ul class="features-list">
            <li class="feature-item"><span class="icon icon-check">✓</span> Validación BBDD</li>
            <li class="feature-item"><span class="icon icon-check">✓</span> Reportes crónicos</li>
            <li class="feature-item disabled-text"><span class="icon icon-cross">✕</span> Comp. derecho</li>
            <li class="feature-item disabled-text"><span class="icon icon-cross">✕</span> Fichas en HC</li>
            <li class="feature-item disabled-text"><span class="icon icon-cross">✕</span> Corrección GESIForm</li>
          </ul>
        </div>
        <button class="btn" onclick="createPreference('starter')">Comprar</button>
      </div>

      <div class="card popular">
        <div class="badge">★ MÁS POPULAR</div>
        <div>
          <div class="plan-name">Pro</div>
          <div class="plan-price">$49.900</div>
          <div class="plan-period">por mes</div>
          <div class="plan-tokens">⚡ 5.000 tokens</div>
          <div class="divider"></div>
          
          <ul class="features-list">
            <li class="feature-item"><span class="icon icon-check">✓</span> Validación BBDD</li>
            <li class="feature-item"><span class="icon icon-check">✓</span> Reportes crónicos</li>
            <li class="feature-item"><span class="icon icon-check">✓</span> Comp. derecho</li>
            <li class="feature-item disabled-text"><span class="icon icon-cross">✕</span> Fichas en HC</li>
            <li class="feature-item disabled-text"><span class="icon icon-cross">✕</span> Corrección GESIForm</li>
          </ul>
        </div>
        <button class="btn btn-popular" onclick="createPreference('pro')">Comprar</button>
      </div>

      <div class="card">
        <div>
          <div class="plan-name">Business</div>
          <div class="plan-price">$99.900</div>
          <div class="plan-period">por mes · todo activo</div>
          <div class="plan-tokens">⚡ 20.000 tokens</div>
          <div class="divider"></div>
          
          <ul class="features-list">
            <li class="feature-item"><span class="icon icon-check">✓</span> Validación BBDD</li>
            <li class="feature-item"><span class="icon icon-check">✓</span> Reportes crónicos</li>
            <li class="feature-item"><span class="icon icon-check">✓</span> Comp. derecho</li>
            <li class="feature-item"><span class="icon icon-check">✓</span> Fichas en HC</li>
            <li class="feature-item"><span class="icon icon-check">✓</span> Corrección GESIForm</li>
          </ul>
        </div>
        <button class="btn" onclick="createPreference('business')">Comprar</button>
      </div>

    </div>
  </div>

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
</script>
</body>
</html>