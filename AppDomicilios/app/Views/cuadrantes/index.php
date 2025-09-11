<?php $title = 'Cuadrantes'; ob_start(); ?>
<div class="container">

  <!-- Header -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h3><i class="fa-solid fa-draw-polygon me-2 text-muted"></i> Cuadrantes</h3>
    <div class="d-flex gap-2">
      <!-- Botón para ver mapa general -->
      <a href="/cuadrantes/mapa" class="btn btn-brand">
        <i class="fa-solid fa-map-location-dot me-1"></i> Ver Mapa
      </a>
      <!-- Botón para crear cuadrante -->
      <a href="/cuadrantes/create" class="btn btn-outline-brand">
        <i class="fa-solid fa-plus me-1"></i> Crear Cuadrante
      </a>
    </div>
  </div>

  <!-- Lista de cuadrantes -->
  <div class="row g-4">
    <?php if (empty($cuadrantes)): ?>
      <div class="card-glass p-4 muted text-center w-100">No hay cuadrantes.</div>
    <?php else: foreach ($cuadrantes as $c): ?>
      <div class="col-12 col-md-6 col-lg-4 fade-in-up">
        <div class="card-glass p-3 h-100 d-flex flex-column list-item" style="border-radius:12px;">

          <!-- Encabezado -->
          <div class="d-flex align-items-center mb-3">
            <i class="fa-solid fa-map-location-dot fa-2x text-primary me-3"></i>
            <div>
              <h5 class="mb-0"><?= esc($c['nombre']) ?></h5>
              <small class="text-muted">
                <?= esc($c['localidad'] ?: 'Sin localidad') ?>
              </small>
            </div>
          </div>

          <!-- Datos -->
          <div class="small mb-3">
            <div class="d-flex align-items-center mb-1">
              <i class="fa-solid fa-dollar-sign me-2"></i>
              <span class="fw-semibold">
                <?= number_format((float)($c['precio'] ?? 0), 0, ',', '.') ?>
              </span>
              <span class="ms-3 badge <?= ($c['estado'] ?? 'Activo') === 'Activo' ? 'bg-success' : 'bg-secondary' ?>">
                <?= esc($c['estado'] ?? 'Activo') ?>
              </span>
            </div>
          </div>

          <!-- Barrios -->
          <?php
            $barriosArr = array_filter(array_map('trim', explode(',', $c['barrios'] ?? '')));
          ?>
          <div class="mb-3">
            <div class="d-flex align-items-center mb-2">
              <i class="fa-solid fa-city me-2 text-muted"></i>
              <span class="text-muted">Barrios</span>
            </div>

            <?php if ($barriosArr): ?>
              <div class="barrios-wrap">
                <?php foreach ($barriosArr as $b): ?>
                  <span class="badge bg-light text-dark me-1 mb-1 border" title="<?= esc($b, 'attr') ?>">
                    <?= esc($b) ?>
                  </span>
                <?php endforeach; ?>
              </div>
            <?php else: ?>
              <div class="muted">Sin barrios</div>
            <?php endif; ?>
          </div>

          <!-- Acciones -->
          <div class="mt-auto d-flex justify-content-between align-items-center">
            <div class="d-flex gap-2">
              
              <!-- Boton de ver  -->
              <a href="#"
                  class="btn btn-sm btn-outline-secondary btn-ver-mapa"
                  title="Ver en mapa"
                  data-nombre="<?= esc($c['nombre'], 'attr') ?>"
                  data-coords='<?= esc($c['coords_json'], 'attr') ?>'>
                  <i class="fa-solid fa-eye"></i>
              </a>

              <!-- boton de editar -->
              <a href="/cuadrantes/edit/<?= (int)$c['id'] ?>" class="btn btn-sm btn-outline-primary" title="Editar">
                  <i class="fa-solid fa-pen-to-square"></i>
              </a>

              <!-- Botón Eliminar -->
              <form action="/cuadrantes/delete/<?= $c['id'] ?>" method="post" class="d-inline">
                <?= csrf_field() ?>
                <button type="button" class="btn btn-sm btn-outline-danger"
                data-bs-toggle="modal" data-bs-target="#deleteModal"
                data-id="<?= $c['id'] ?>" data-nombre="<?= esc($c['nombre']) ?>">
                <i class="fa-solid fa-trash"></i>
                </button>
              </form>
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

<!-- Modal Eliminar -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content card-glass">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fa-solid fa-triangle-exclamation text-danger me-2"></i> Confirmar Eliminación</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <p>¿Estás seguro que deseas eliminar el cuadrante <strong id="deleteName"></strong>?</p>
      </div>
      <div class="modal-footer">
        <form id="deleteForm" method="post">
          <?= csrf_field() ?>
          <input type="hidden" name="_method" value="DELETE">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-danger"><i class="fa-solid fa-trash me-1"></i>Eliminar</button>
        </form>
      </div>
    </div>
  </div>
</div>

<?php
$scripts = '
<style>
  .barrios-wrap { display:flex; flex-wrap:wrap; gap:.25rem; max-height:120px; overflow:auto; padding-right:4px; }
  .list-item { transition: transform .2s, box-shadow .2s; }
  .list-item:hover { transform: translateY(-5px); box-shadow: 0 15px 35px rgba(0,0,0,0.15); }

  .btn-brand {
    background: var(--brand);
    color: #fff;
    font-weight: 500;
    border-radius: 6px;
    border: none;
  }
  .btn-brand:hover {
    background: #e55e00;
    color: #fff;
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

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin=""/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>

<script>
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

      ensureMap();
      if (polygonLayer) polygonLayer.remove();

      if (!Array.isArray(puntos) || puntos.length < 3) {
        alert("El polígono no tiene puntos suficientes.");
        return;
      }

      polygonLayer = L.polygon(puntos, { weight: 2, opacity: 0.9, fillOpacity: 0.2 });
      polygonLayer.addTo(mapInstance);
      mapInstance.fitBounds(polygonLayer.getBounds(), { padding: [20, 20] });

      document.getElementById("mapTitle").textContent = nombre;

      const modal = new bootstrap.Modal(document.getElementById("mapModal"));
      modal.show();

      setTimeout(() => { mapInstance.invalidateSize(); }, 200);
    });
  });
</script>
';
?>

<?php $content = ob_get_clean(); echo view('layouts/app', compact('content','title','scripts')); ?>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const deleteModal = document.getElementById('deleteModal');
  const deleteForm = document.getElementById('deleteForm');
  const deleteName = document.getElementById('deleteName');

  deleteModal.addEventListener('show.bs.modal', event => {
    const button = event.relatedTarget;
    const id = button.getAttribute('data-id');
    const nombre = button.getAttribute('data-nombre');

    deleteForm.action = `/cuadrantes/delete/${id}`;
    deleteName.textContent = nombre;
  });
});
</script>

