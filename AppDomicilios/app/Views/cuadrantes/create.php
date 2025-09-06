<?php $title = 'Crear Cuadrante'; ob_start(); ?>
<div class="container">
  <div class="card-glass p-4">
    <div class="d-flex align-items-center mb-4">
      <i class="fa-solid fa-draw-polygon fa-lg me-2 text-muted"></i>
      <h4 class="mb-0">Crear Cuadrante</h4>
    </div>

    <?php if (session('error')): ?>
      <div class="alert alert-danger"><?= esc(session('error')) ?></div>
    <?php endif; ?>
    <?php if (session('success')): ?>
      <div class="alert alert-success"><?= esc(session('success')) ?></div>
    <?php endif; ?>

    <form method="post" action="/cuadrantes/store" class="mt-3" id="form-cuadrante">
      <?= csrf_field() ?>

      <div class="row g-3 mb-3">
        <!-- Nombre -->
        <div class="col-md-6">
          <label class="form-label">Nombre</label>
          <div class="input-group">
            <span class="input-group-text"><i class="fa-solid fa-tag"></i></span>
            <input class="form-control" name="nombre" placeholder="Nombre del cuadrante" required
                   value="<?= esc(old('nombre')) ?>">
          </div>
        </div>

        <!-- Precio (se mapea a precio_base en el controller) -->
        <div class="col-md-6">
          <label class="form-label">Precio del cuadrante</label>
          <div class="input-group">
            <span class="input-group-text"><i class="fa-solid fa-dollar-sign"></i></span>
            <input class="form-control" name="precio" placeholder="Ej: 7000" type="number" min="0" step="0.01" required
                   value="<?= esc(old('precio')) ?>">
          </div>
        </div>
      </div>

      <!-- Localidad (opcional) -->
      <div class="row g-3 mb-3">
        <div class="col-md-6">
          <label class="form-label">Localidad (opcional)</label>
          <input class="form-control" name="localidad" placeholder="Ej: Norte–Centro" value="<?= esc(old('localidad')) ?>">
        </div>
        <div class="col-md-6">
          <!-- Espacio para futuro: estado activo, etc. -->
        </div>
      </div>

      <!-- Coordenadas -->
      <div class="mb-3 position-relative">
        <label class="form-label">Coordenadas (formato [[lat,lon],[lat,lon],...])</label>
        <div class="input-group">
          <span class="input-group-text"><i class="fa-solid fa-map-pin"></i></span>
          <textarea class="form-control" id="coordsDisplay" rows="3" name="coordenadas"
                    placeholder='Ej: [[10.97,-74.79],[10.98,-74.78],[10.96,-74.78]]'><?= esc(old('coordenadas')) ?></textarea>
        </div>
        <small class="text-muted">Puedes editar manualmente o hacer click en el mapa para ir agregando puntos.</small>
      </div>

      <!-- Barrios visual + oculto para enviar -->
      <div class="mb-3 position-relative">
        <label class="form-label">Barrios</label>
        <div class="input-group">
          <span class="input-group-text"><i class="fa-solid fa-city"></i></span>
          <textarea class="form-control" id="barriosDisplay" rows="2" readonly
                    placeholder="Se llenará automáticamente"><?= esc(old('barrios')) ?></textarea>
        </div>
        <input type="hidden" name="barrios" id="barrios" value="<?= esc(old('barrios')) ?>">
      </div>

      <!-- Mapa -->
      <label class="form-label">Mapa</label>
      <div id="map" class="mb-3" style="height:400px;border-radius:12px;"></div>

      <!-- Botones -->
      <div class="d-flex gap-2 mt-3">
        <button class="btn btn-brand" type="submit"><i class="fa-solid fa-save me-1"></i>Guardar</button>
        <a href="/cuadrantes" class="btn btn-outline-secondary"><i class="fa-solid fa-arrow-left me-1"></i>Cancelar</a>
        <button type="button" id="resetMap" class="btn btn-outline-danger ms-auto"><i class="fa-solid fa-trash me-1"></i>Limpiar</button>
      </div>

    </form>
  </div>
</div>

<?php $scripts = '
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin=""/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
<script src="https://cdn.jsdelivr.net/npm/@turf/turf@6.5.0/turf.min.js"></script>

<script>
  // --- Mapa base ---
  const lat = 10.968540, lng = -74.781320;
  const map = L.map("map").setView([lat, lng], 12);
  L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", { maxZoom: 19 }).addTo(map);

  // Capas
  let polygonLayer = null;
  let lineLayer    = null;
  let markerGroup  = L.layerGroup().addTo(map);

  // Estado actual de puntos [[lat,lon], ...]
  let currentPoints = [];

  // --- Refs del formulario ---
  const coordsDisplay  = document.getElementById("coordsDisplay"); // textarea visible [[lat,lon],...]
  const barriosDisplay = document.getElementById("barriosDisplay"); // textarea solo lectura
  const barriosHidden  = document.getElementById("barrios");        // input hidden que se envía al backend
  const resetBtn       = document.getElementById("resetMap");

  // --- Carga del GeoJSON de barrios ---
  let barriosGeoJSON = null;
  fetch("/geojson/barrios_barranquilla.geojson")
    .then(res => res.json())
    .then(data => barriosGeoJSON = data)
    .catch(err => console.error("No se pudo cargar el GeoJSON:", err));

  // --- Utilidades ---
  function setBarriosText(value) {
    const v = value || "";
    barriosDisplay.value = v;
    barriosHidden.value  = v;
  }

  function ringClosed(points) {
    if (!Array.isArray(points) || points.length === 0) return [];
    const first = points[0];
    const last  = points[points.length - 1];
    const same  = first && last && first[0] === last[0] && first[1] === last[1];
    return same ? points.slice() : [...points, first];
  }

  function obtenerBarrios(points) {
    if (!barriosGeoJSON || !Array.isArray(points) || points.length < 3) return "";
    // Turf usa [lon, lat]
    const closed   = ringClosed(points).map(p => [p[1], p[0]]);
    const cuadrantePolygon = turf.polygon([closed]);
    const nombres = new Set();

    (barriosGeoJSON.features || []).forEach(f => {
      try {
        const g = f.geometry;
        if (!g) return;
        let polyF = null;
        if (g.type === "Polygon")      polyF = turf.polygon(g.coordinates);
        else if (g.type === "MultiPolygon") polyF = turf.multiPolygon(g.coordinates);
        if (polyF && turf.booleanIntersects(cuadrantePolygon, polyF)) {
          const props = f.properties || {};
          const name = props.nombre || props.Name || props.BARRIO || "Barrio s/n";
          nombres.add(name);
        }
      } catch(e) {}
    });

    return Array.from(nombres).join(", ");
  }

  function redraw() {
    // Limpia capas
    if (polygonLayer) { map.removeLayer(polygonLayer); polygonLayer = null; }
    if (lineLayer)    { map.removeLayer(lineLayer);    lineLayer    = null; }
    markerGroup.clearLayers();

    // Siempre sincroniza el textarea con el estado actual
    coordsDisplay.value = JSON.stringify(currentPoints);

    if (currentPoints.length === 0) {
      setBarriosText("");
      return;
    }

    // Marca los puntos
    currentPoints.forEach(p => L.circleMarker(p, { radius: 5 }).addTo(markerGroup));

    if (currentPoints.length < 3) {
      // Dibuja polilínea provisional
      lineLayer = L.polyline(currentPoints, { weight: 2, dashArray: "4 4" }).addTo(map);
      map.fitBounds(lineLayer.getBounds(), { padding: [10,10] });
      setBarriosText(""); // aún no hay barrios
      return;
    }

    // Con 3+ puntos dibuja polígono y calcula barrios
    polygonLayer = L.polygon(currentPoints, { color: "#FF6B00", weight: 2, fillOpacity: 0.2 }).addTo(map);
    map.fitBounds(polygonLayer.getBounds(), { padding: [10,10] });

    const barrios = obtenerBarrios(currentPoints);
    setBarriosText(barrios);
  }

  // --- Eventos ---
  // Clic en el mapa -> agrega punto y redibuja
  map.on("click", (e) => {
    currentPoints.push([e.latlng.lat, e.latlng.lng]);
    redraw();
  });

  // Edición manual del textarea -> reinterpreta puntos y redibuja
  coordsDisplay.addEventListener("input", () => {
    try {
      const pts = JSON.parse(coordsDisplay.value);
      currentPoints = Array.isArray(pts) ? pts : [];
    } catch { currentPoints = []; }
    redraw();
  });

  // Botón limpiar
  resetBtn.addEventListener("click", () => {
    currentPoints = [];
    redraw();
  });

  // Si viene algo precargado (old()), lo dibujamos al cargar
  (function initFromOld() {
    if (!coordsDisplay.value) return;
    try {
      const pts = JSON.parse(coordsDisplay.value);
      currentPoints = Array.isArray(pts) ? pts : [];
      redraw();
    } catch {}
  })();
</script>
'; ?>

<?php $content = ob_get_clean(); echo view("layouts/app", compact("content","title","scripts")); ?>

