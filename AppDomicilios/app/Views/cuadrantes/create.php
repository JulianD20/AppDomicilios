<?php $title = 'Crear Cuadrante'; ob_start(); ?>
<div class="container">
  <div class="card-glass p-4">
    <div class="d-flex align-items-center mb-4">
      <i class="fa-solid fa-draw-polygon fa-lg me-2 text-muted"></i>
      <h4 class="mb-0">Crear Cuadrante</h4>
    </div>

    <form method="post" action="/cuadrantes/store" class="mt-3" id="form-cuadrante">

      
      <div class="row g-3 mb-3">
        <!-- Nombre -->
        <div class="col-md-6">
          <label class="form-label">Nombre</label>
          <div class="input-group">
            <span class="input-group-text"><i class="fa-solid fa-tag"></i></span>
            <input class="form-control" name="nombre" placeholder="Nombre del cuadrante" required>
          </div>
        </div>

        <!-- Precio -->
        <div class="col-md-6">
          <label class="form-label">Precio del cuadrante</label>
          <div class="input-group">
            <span class="input-group-text"><i class="fa-solid fa-dollar-sign"></i></span>
            <input class="form-control" name="precio" placeholder="Ej: 5000" type="number" min="0" step="0.01" required>
          </div>
        </div>
      </div>

      <!-- Coordenadas -->
      <div class="mb-3 position-relative">
        <label class="form-label">Coordenadas</label>
        <div class="input-group">
          <span class="input-group-text"><i class="fa-solid fa-map-pin"></i></span>
          <textarea class="form-control" id="coordsDisplay" rows="3" name="coordenadas" placeholder="Ej: [[lat,lng],[lat,lng],...]"></textarea>
        </div>
        <small class="text-muted">Puedes editar manualmente o dibujar en el mapa</small>
      </div>

      <!-- Barrios (solo lectura) -->
      <div class="mb-3 position-relative">
        <label class="form-label">Barrios</label>
        <div class="input-group">
          <span class="input-group-text"><i class="fa-solid fa-city"></i></span>
          <textarea class="form-control" id="barriosDisplay" rows="2" readonly placeholder="Los barrios se llenarán automáticamente"></textarea>
        </div>
      </div>

      <!-- Mapa -->
      <label class="form-label">Mapa</label>
      <div id="map" class="mb-3" style="height:400px;border-radius:12px;"></div>

      <!-- Botones -->
      <div class="d-flex gap-2 mt-3">
        <button class="btn btn-brand"><i class="fa-solid fa-save me-1"></i>Guardar</button>
        <a href="/cuadrantes" class="btn btn-outline-secondary"><i class="fa-solid fa-arrow-left me-1"></i>Cancelar</a>
        <button type="button" id="resetMap" class="btn btn-outline-danger ms-auto"><i class="fa-solid fa-trash me-1"></i>Limpiar</button>
      </div>

    </form>
  </div>
</div>

<?php $scripts = '
<script src="https://cdn.jsdelivr.net/npm/@turf/turf@6.5.0/turf.min.js"></script>
<script>
  const lat = 10.968540; const lng = -74.781320;
  const map = L.map("map").setView([lat,lng], 12);
  L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png",{ maxZoom: 19 }).addTo(map);

  let poly = null;
  const coordsDisplay = document.getElementById("coordsDisplay");
  const barriosDisplay = document.getElementById("barriosDisplay");

  let barriosGeoJSON = null;
  fetch("/geojson/Barrios_de_Barranquilla_según_POT_20250905.geojson")
    .then(res => res.json())
    .then(data => barriosGeoJSON = data)
    .catch(err => console.error("No se pudo cargar el GeoJSON:", err));

  function obtenerBarrios(points){
    if(!barriosGeoJSON || points.length < 3) return "";
    if(points[0][0] !== points[points.length-1][0] || points[0][1] !== points[points.length-1][1]){
      points.push(points[0]);
    }
    const polyCoords = points.map(p => [p[1], p[0]]);
    const cuadrantePolygon = turf.polygon([polyCoords]);
    let barriosDentro = [];
    barriosGeoJSON.features.forEach(layer => {
      if(layer.geometry.type === "Polygon"){
        const barrioPolygon = turf.polygon(layer.geometry.coordinates);
        if(turf.booleanIntersects(cuadrantePolygon, barrioPolygon)){
          barriosDentro.push(layer.properties.nombre);
        }
      }
      if(layer.geometry.type === "MultiPolygon"){
        const barrioPolygon = turf.multiPolygon(layer.geometry.coordinates);
        if(turf.booleanIntersects(cuadrantePolygon, barrioPolygon)){
          barriosDentro.push(layer.properties.nombre);
        }
      }
    });
    return barriosDentro.join(", ");
  }

  coordsDisplay.addEventListener("input", function(){
    try {
      const points = JSON.parse(coordsDisplay.value);
      if(Array.isArray(points) && points.length >= 3){
        if(poly) map.removeLayer(poly);
        poly = L.polygon(points, { color: "#FF6B00", fillOpacity: 0.2 }).addTo(map);
        map.fitBounds(poly.getBounds());
        barriosDisplay.value = obtenerBarrios(points);
      }
    } catch (e) { console.warn("Formato inválido de coordenadas"); }
  });

  map.on("click", function(e){
    const latlng = [e.latlng.lat, e.latlng.lng];
    let points = [];
    if(coordsDisplay.value){
      try { points = JSON.parse(coordsDisplay.value); } catch(e){}
    }
    points.push(latlng);
    if(poly) map.removeLayer(poly);
    poly = L.polygon(points, { color: "#FF6B00", fillOpacity: 0.2 }).addTo(map);
    coordsDisplay.value = JSON.stringify(points);
    barriosDisplay.value = obtenerBarrios(points);
  });

  document.getElementById("resetMap").addEventListener("click", ()=> {
    coordsDisplay.value = "";
    barriosDisplay.value = "";
    if(poly) map.removeLayer(poly);
  });
</script>
'; ?>

<?php $content = ob_get_clean(); echo view("layouts/app", compact("content","title","scripts")); ?>







