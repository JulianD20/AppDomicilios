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
<script src="https://cdn.jsdelivr.net/npm/@turf/turf@6.5.0/turf.min.js"></script>
<script>
  // --------- Mapa base ----------
  var map = L.map("map");
  L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
    attribution: "&copy; OpenStreetMap contributors"
  }).addTo(map);

  // Paleta de colores fija
  var colors = [
    "#e6194b","#3cb44b","#ffe119","#4363d8","#f58231",
    "#911eb4","#46f0f0","#f032e6","#bcf60c","#fabebe",
    "#008080","#e6beff","#9a6324","#fffac8","#800000",
    "#aaffc3","#808000","#ffd8b1","#000075","#808080"
  ];

  // Datos desde PHP (coords_json en formato [[lat,lon],...])
  var cuadrantes = ' . json_encode($cuadrantes) . ';

  // --------- Utils geo ----------
  function latlngsToClosedRing(latlngs){
    var ring = latlngs.map(function(p){ return [p[1], p[0]]; }); // [lng,lat]
    if (ring.length && (ring[0][0] !== ring[ring.length-1][0] || ring[0][1] !== ring[ring.length-1][1])) {
      ring.push(ring[0]);
    }
    return ring;
  }
  function parseCoordsJson(c){
    try {
      var arr = JSON.parse(c.coords_json || "[]");
      if (Array.isArray(arr) && arr.length >= 3) return arr;
    } catch(e){}
    return null;
  }
  function safeDifference(a, b){
    // turf.difference puede devolver null o lanzar en geometrías borde;
    // intentamos normalizar con truncate; si falla, devolvemos null.
    try {
      var diff = turf.difference(a, b);
      if (!diff) return null;
      return turf.truncate(diff, {precision: 6, coordinates: 2});
    } catch(e){
      try {
        var aa = turf.truncate(a, {precision: 6, coordinates: 2});
        var bb = turf.truncate(b, {precision: 6, coordinates: 2});
        return turf.difference(aa, bb) || null;
      } catch(e2){
        return null;
      }
    }
  }

  // --------- Lógica de “no superponer” ----------
  var allLayers = [];
  var mask = null; // unión de todo lo ya dibujado (Polygon/MultiPolygon en [lng,lat])

  // Si quieres que “gane el más antiguo” asegúrate que el arreglo venga ordenado del más viejo al más nuevo
  // cuadrantes.sort((a,b)=> (a.created_at > b.created_at) ? 1 : -1);

  cuadrantes.forEach(function(c, idx){
    var coords = parseCoordsJson(c);
    if (!coords) { console.warn("coords_json inválidas para ID", c.id); return; }

    // Nuevo polígono en formato Turf
    var ring = latlngsToClosedRing(coords);
    var nuevo = turf.polygon([ring], { id: c.id, nombre: c.nombre, localidad: c.localidad });

    // Recortar contra la máscara ya ocupada
    var aDibujar = mask ? safeDifference(nuevo, mask) : nuevo;

    // Si no queda nada (totalmente cubierto) => mostrar contorno punteado opcional
    if (!aDibujar) {
      // Contorno punteado del original para informar que está 100% cubierto
      var dashed = L.geoJSON(nuevo, {
        style: { color: "#444", weight: 2, fillOpacity: 0, dashArray: "6,6" }
      }).addTo(map);
      dashed.bindPopup("<b>" + (c.nombre || "Cuadrante") + "</b><br><small>Oculto por recorte visual (solapado totalmente).</small>");
      allLayers.push(dashed);
    } else {
      var color = colors[idx % colors.length];

      // Pintar solo la parte NO solapada
      var polyLayer = L.geoJSON(aDibujar, {
        style: { color: color, weight: 2, fillColor: color, fillOpacity: 0.45 }
      }).addTo(map);

      // Etiqueta centrada (centroide de la geometría recortada)
      try{
        var centro = turf.center(aDibujar).geometry.coordinates; // [lng,lat]
        var label = L.tooltip({
          permanent: true, direction: "center", className: "cuadrante-label"
        })
        .setContent("<i class=\\"fa-solid fa-map-pin me-1 text-brand\\"></i> " + (c.nombre || "Cuadrante"))
        .setLatLng([centro[1], centro[0]]);
        polyLayer.addTo(map).bindTooltip(label);
      } catch(_){}

      polyLayer.eachLayer(function(layer){
        layer.bindPopup("<i class=\\"fa-solid fa-draw-polygon text-brand me-1\\"></i> " +
                        "<b style=\\"color:"+color+"; font-size:14px;\\">" + (c.nombre || "Cuadrante") + "</b>" +
                        (c.localidad ? "<br><small class=\\"text-muted\\">" + c.localidad + "</small>" : "") +
                        "<br><small class=\\"text-muted\\">*Área visual recortada para evitar solapes.*</small>");
      });

      allLayers.push(polyLayer);
    }

    // Actualizar máscara = unión (lo original sin recortar; prioridad la da el orden de iteración)
    try {
      mask = mask ? turf.union(mask, nuevo) : nuevo;
    } catch(e) {
      // Si union falla por geometrías degeneradas, intentamos truncar
      try {
        var mm = mask ? turf.truncate(mask, {precision: 6, coordinates: 2}) : null;
        var nn = turf.truncate(nuevo, {precision: 6, coordinates: 2});
        mask = mm ? turf.union(mm, nn) : nn;
      } catch(e2){
        // En última instancia, mantenemos la máscara previa para no romper el flujo
      }
    }
  });

  // Ajustar cámara
  if (allLayers.length > 0) {
    var group = L.featureGroup(allLayers);
    try { map.fitBounds(group.getBounds().pad(0.2)); } catch(_) { map.setView([10.96854,-74.78132], 12); }
  } else {
    map.setView([10.96854,-74.78132], 12);
  }
</script>

<style>
  .cuadrante-label {
    background: rgba(255,255,255,0.9);
    border: 1px solid rgba(0,0,0,0.2);
    border-radius: 6px;
    padding: 3px 8px;
    font-weight: 600;
    font-size: 13px;
    color: #0f1724;
    text-align: center;
    box-shadow: 0 2px 4px rgba(0,0,0,0.2);
  }
  .leaflet-popup-content-wrapper { border-radius: 6px; padding: 4px; font-family: Inter, system-ui, Arial; }
  .text-brand { color: var(--brand); }
</style>
';
$content = ob_get_clean();
echo view("layouts/app", compact("content","title","scripts"));
?>


