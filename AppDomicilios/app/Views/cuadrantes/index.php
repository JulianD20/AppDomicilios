<?php $title = 'Cuadrantes'; ob_start(); ?>
<div class="container">

  <!-- Header -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h3><i class="fa-solid fa-draw-polygon me-2 text-muted"></i>Cuadrantes</h3>
    <a href="/cuadrantes/create" class="btn btn-brand"><i class="fa-solid fa-plus me-1"></i>Crear Cuadrante</a>
  </div>

  <!-- Lista de cuadrantes -->
  <div class="row g-4">
    <?php if (empty($cuadrantes)): ?>
      <div class="card-glass p-4 muted text-center w-100">No hay cuadrantes.</div>
    <?php else: foreach ($cuadrantes as $c): ?>
      <div class="col-12 col-md-6 col-lg-4 fade-in-up">
        <div class="card-glass p-3 list-item d-flex flex-column justify-content-between h-100"
             style="transition: transform .2s, box-shadow .2s; border-radius:12px;">

          <!-- Título + subtítulo -->
          <div class="d-flex align-items-center mb-2">
            <i class="fa-solid fa-map-location-dot fa-2x text-primary me-3"></i>
            <div>
              <h5 class="mb-0"><?= esc($c['nombre']) ?></h5>
              <small class="muted">
                <?= esc($c['localidad'] ?? 'Sin localidad') ?>
                <?php if (!empty($c['barrios'])): ?>
                  · <span title="<?= esc($c['barrios']) ?>">barrios</span>
                <?php endif; ?>
              </small>
            </div>
          </div>

          <!-- Precio + estado + acciones -->
          <div class="d-flex justify-content-between align-items-center mt-auto">
            <div class="small">
              <span class="text-muted me-2">
                <i class="fa-solid fa-dollar-sign me-1"></i>
                <?= number_format((float)($c['precio_base'] ?? 0), 0, ',', '.') ?>
              </span>
              <span class="badge <?= (int)($c['activo'] ?? 1) ? 'bg-success' : 'bg-secondary' ?>">
                <?= (int)($c['activo'] ?? 1) ? 'Activo' : 'Inactivo' ?>
              </span>
            </div>
            <div class="d-flex gap-2">
              <a href="#"
                 class="btn btn-sm btn-outline-secondary btn-ver-mapa"
                 title="Ver en mapa"
                 data-nombre="<?= esc($c['nombre'], 'attr') ?>"
                 data-coords='<?= esc($c['coords_json'], 'attr') ?>'>
                <i class="fa-solid fa-eye"></i>
              </a>
              <a href="/cuadrantes/edit/<?= (int)$c['id'] ?>" class="btn btn-sm btn-outline-primary" title="Editar">
                <i class="fa-solid fa-pen-to-square"></i>
              </a>
              <a href="/cuadrantes/delete/<?= (int)$c['id'] ?>" class="btn btn-sm btn-outline-danger" title="Eliminar">
                <i class="fa-solid fa-trash"></i>
              </a>
            </div>
          </div>

        </div>
      </div>
    <?php endforeach; endif; ?>
  </div>
</div>

<!-- Modal Mapa -->
<div class="modal fade" id="mapModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fa-solid fa-map me-2"></i><span id="mapTitle">Mapa</span></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body p-0">
        <div id="leafletMap" style="height: 480px;"></div>
      </div>
    </div>
  </div>
</div>

<?php
$scripts = '
<!-- Leaflet CSS/JS (solo si no lo cargas en el layout) -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin=""/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>

<script>
  // Animación hover para las tarjetas
  document.querySelectorAll(".list-item").forEach(card => {
    card.addEventListener("mouseenter", ()=> {
      card.style.transform = "translateY(-5px)";
      card.style.boxShadow = "0 15px 35px rgba(0,0,0,0.15)";
    });
    card.addEventListener("mouseleave", ()=> {
      card.style.transform = "none";
      card.style.boxShadow = "0 8px 20px rgba(15,23,36,0.08)";
    });
  });

  // Modal & mapa
  let mapInstance = null;
  let polygonLayer = null;

  function ensureMap() {
    if (!mapInstance) {
      mapInstance = L.map("leafletMap");
      L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
        maxZoom: 19,
        attribution: "&copy; OpenStreetMap"
      }).addTo(mapInstance);
    }
  }

  document.querySelectorAll(".btn-ver-mapa").forEach(btn => {
    btn.addEventListener("click", (e) => {
      e.preventDefault();
      const nombre = btn.dataset.nombre || "Cuadrante";
      const coordsStr = btn.dataset.coords || "[]";
      let puntos = [];
      try { puntos = JSON.parse(coordsStr); } catch (err) {}

      // Leaflet usa [lat, lon]; nuestro JSON ya viene así
      ensureMap();
      if (polygonLayer) { polygonLayer.remove(); }

      // Validación mínima
      if (!Array.isArray(puntos) || puntos.length < 3) {
        alert("El polígono no tiene puntos suficientes.");
        return;
      }

      polygonLayer = L.polygon(puntos, { weight: 2, opacity: 0.9, fillOpacity: 0.2 });
      polygonLayer.addTo(mapInstance);

      // Ajustar vista
      mapInstance.fitBounds(polygonLayer.getBounds(), { padding: [20, 20] });

      // Título del modal
      document.getElementById("mapTitle").textContent = nombre;

      // Abrir modal (Bootstrap 5)
      const modal = new bootstrap.Modal(document.getElementById("mapModal"));
      modal.show();

      // Fix de tamaño cuando el modal termina de abrir
      setTimeout(() => { mapInstance.invalidateSize(); }, 200);
    });
  });
</script>
';
?>

<?php $content = ob_get_clean(); echo view('layouts/app', compact('content','title','scripts')); ?>
