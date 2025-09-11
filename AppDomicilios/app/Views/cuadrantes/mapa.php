<?php
$title = "Mapa de Cuadrantes";
ob_start();
?>

<div class="container-fluid">

  <!-- Bienvenida -->
  <div class="card card-glass mb-4">
    <div class="card-body d-flex align-items-center">
      <i class="fa-solid fa-hand-sparkles fs-3 text-brand me-3"></i>
      <div>
        <h5 class="mb-1 fw-bold">Bienvenido a <span class="text-brand">AppDomicilios</span></h5>
        <p class="mb-0 text-muted">Visualiza en este mapa los cuadrantes disponibles para domicilios.</p>
      </div>
    </div>
  </div>

  <!-- Card con el mapa -->
  <div class="card card-glass">
    <div class="card-header bg-white border-0 d-flex align-items-center justify-content-between">
      <div class="d-flex align-items-center">
        <i class="fa-solid fa-map-location-dot text-brand me-2"></i>
        <h5 class="mb-0">Mapa de Cuadrantes</h5>
      </div>
      <!-- Botón volver -->
      <a href="/cuadrantes" class="btn btn-outline-brand btn-sm">
        <i class="fa-solid fa-arrow-left me-1"></i> Volver
      </a>
    </div>
    <div class="card-body p-0">
      <div id="map" style="height: 600px; width: 100%;"></div>
    </div>
    <div class="card-footer bg-white text-muted small text-center">
      <i class="fa-solid fa-info-circle me-1 text-brand"></i>
      Cada polígono representa un cuadrante con cobertura activa.
    </div>
  </div>

</div>

<?php
$scripts = '
<script>
  var map = L.map("map");

  L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
      attribution: "&copy; OpenStreetMap contributors"
  }).addTo(map);

  var colors = [
      "#e6194b","#3cb44b","#ffe119","#4363d8","#f58231",
      "#911eb4","#46f0f0","#f032e6","#bcf60c","#fabebe",
      "#008080","#e6beff","#9a6324","#fffac8","#800000",
      "#aaffc3","#808000","#ffd8b1","#000075","#808080"
  ];

  var cuadrantes = ' . json_encode($cuadrantes) . ';
  var allLayers = [];

  cuadrantes.forEach((c, index) => {
      try {
          var coords = JSON.parse(c.coords_json);
          var color = colors[index % colors.length];

          var polygon = L.polygon(coords, {
              color: color,
              weight: 2,
              fillColor: color,
              fillOpacity: 0.45
          }).addTo(map);

          allLayers.push(polygon);

          polygon.bindPopup("<i class=\\"fa-solid fa-draw-polygon text-brand me-1\\"></i> " +
                            "<b style=\\"color:" + color + "; font-size:14px;\\">" + c.nombre + "</b>");

          polygon.bindTooltip("<i class=\\"fa-solid fa-map-pin me-1 text-brand\\"></i> " + c.nombre, {
              permanent: true,
              direction: "center",
              className: "cuadrante-label"
          });
      } catch (e) {
          console.error("Error con cuadrante ID " + c.id, e);
      }
  });

  if (allLayers.length > 0) {
      var group = L.featureGroup(allLayers);
      map.fitBounds(group.getBounds().pad(0.2));
  }
</script>

<style>
  .cuadrante-label {
      background: rgba(255, 255, 255, 0.9);
      border: 1px solid rgba(0,0,0,0.2);
      border-radius: 6px;
      padding: 3px 8px;
      font-weight: 600;
      font-size: 13px;
      color: #0f1724;
      text-align: center;
      box-shadow: 0 2px 4px rgba(0,0,0,0.2);
  }
  .leaflet-popup-content-wrapper {
      border-radius: 6px;
      padding: 4px;
      font-family: Inter, system-ui, Arial;
  }
  .text-brand {
      color: var(--brand);
  }
  .btn-outline-brand {
      border: 1px solid var(--brand);
      color: var(--brand);
      font-weight: 500;
      border-radius: 6px;
      background: #fff;
  }
  .btn-outline-brand:hover {
      background: var(--brand);
      color: #fff;
  }
</style>
';
$content = ob_get_clean();
echo view("layouts/app", compact("content","title","scripts"));
?>

