<div id="skeleton-reception" class="skeleton-wrapper">
  <style>
    @keyframes shimmer {
      0%   { background-position: -600px 0; }
      100% { background-position:  600px 0; }
    }
    .sk {
      background: linear-gradient(90deg, #5a5c69 25%, #333350 50%, #5a5c69 75%);
      background-size: 600px 100%;
      animation: shimmer 1.4s infinite linear;
      border-radius: 4px;
      display: inline-block;
    }
    .skeleton-wrapper {
      background: #0D1117;
      border-radius: 12px;
      padding: 24px;
      width: 100%;
      box-sizing: border-box;
    }
    .sk-title    { height: 28px; width: 260px; display: block; margin-bottom: 24px; }
    .sk-tabs     { display: flex; margin-bottom: 20px; }
    .sk-tab-a    { height: 38px; flex: 1; background: #c0392b; border-radius: 6px 0 0 6px; }
    .sk-tab-b    { height: 38px; flex: 1; background: #5a5c69; border-radius: 0 6px 6px 0; }
    .sk-search   { display: flex; gap: 8px; margin-bottom: 20px; }
    .sk-prefix   { height: 34px; width: 36px; border-radius: 6px; }
    .sk-input    { height: 34px; width: 280px; border-radius: 6px; }
    .sk-table    { width: 100%; border-collapse: collapse; }
    .sk-table thead th { padding: 10px 12px; border-bottom: 1px solid rgba(255,255,255,0.08); }
    .sk-table tbody td { padding: 13px 12px; border-bottom: 1px solid rgba(255,255,255,0.05); vertical-align: middle; }
    .sk-th       { height: 13px; border-radius: 3px; background: rgba(255,255,255,0.15); display: block; }
    .sk-cb       { width: 16px; height: 16px; border-radius: 3px; }
    .sk-sm       { height: 14px; }
    .sk-sub      { height: 11px; margin-top: 5px; opacity: 0.7; }
    .sk-btn      { width: 30px; height: 28px; border-radius: 6px; }
  </style>

  {{-- Título --}}
  <div class="sk sk-title"></div>

  {{-- Tabs --}}
  <div class="sk-tabs">
    <div class="sk-tab-a"></div>
    <div class="sk-tab-b"></div>
  </div>

  {{-- Barra de búsqueda --}}
  <div class="sk-search">
    <div class="sk sk-prefix"></div>
    <div class="sk sk-input"></div>
  </div>

  {{-- Tabla --}}
  <table class="sk-table">
    <thead>
      <tr>
        @foreach([16, 38, 30, 70, 60, 50, 90, 100, 110, 50] as $w)
          <th><div class="sk-th" style="width: {{ $w }}px;"></div></th>
        @endforeach
      </tr>
    </thead>
    <tbody>
      @for($i = 0; $i < 8; $i++)
        <tr>
          <td><div class="sk sk-cb"></div></td>
          <td><div class="sk sk-sm" style="width:14px;"></div></td>
          <td><div class="sk sk-sm" style="width:36px;"></div></td>
          <td>
            <div class="sk sk-sm" style="width: {{ [88,90,86,90,92,88,86,90][$i] }}px;"></div>
            <div class="sk sk-sub" style="width: {{ [75,60,70,68,72,65,70,60][$i] }}px;"></div>
          </td>
          <td><div class="sk sk-sm" style="width:110px;"></div></td>
          <td><div class="sk sk-sm" style="width:10px;"></div></td>
          <td><div class="sk sk-sm" style="width:{{ [120,110,120,115,120,110,120,115][$i] }}px;"></div></td>
          <td><div class="sk sk-sm" style="width:{{ [150,155,150,160,150,155,145,150][$i] }}px;"></div></td>
          <td><div class="sk sk-sm" style="width:80px;"></div></td>
          <td><div class="sk sk-btn"></div></td>
        </tr>
      @endfor
    </tbody>
  </table>
</div>