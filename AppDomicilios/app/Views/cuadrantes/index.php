<?php $title = 'Cuadrantes'; ob_start(); ?>
<div class="container py-4">

  <!-- Header -->
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="fw-bold">
      <i class="fa-solid fa-draw-polygon me-2 text-muted"></i> Cuadrantes
    </h3>
    <div class="d-flex gap-2">
      <a href="/cuadrantes/mapa" class="btn btn-brand">
        <i class="fa-solid fa-map-location-dot me-1"></i> Ver Mapa
      </a>
      <a href="/cuadrantes/create" class="btn btn-outline-brand">
        <i class="fa-solid fa-plus me-1"></i> Crear Cuadrante
      </a>
    </div>
  </div>

  <!-- Filtros -->
  <form id="filtersForm" class="card-glass shadow-sm p-3 mb-3" method="get" action="/cuadrantes">
    <div class="row g-2 align-items-center">
      <div class="col-12 col-lg-5">
        <div class="input-group">
          <span class="input-group-text"><i class="fa-solid fa-magnifying-glass"></i></span>
          <input type="text" class="form-control" name="q"
                 placeholder="Buscar por nombre, localidad o barrios…"
                 value="<?= esc($filters['q'] ?? '') ?>" autocomplete="off">
        </div>
      </div>

      <div class="col-6 col-lg-2">
        <select class="form-select" name="estado">
          <option value="">Todos</option>
          <option value="Activo"   <?= (isset($filters['estado']) && $filters['estado']==='Activo')?'selected':'' ?>>Activo</option>
          <option value="Inactivo" <?= (isset($filters['estado']) && $filters['estado']==='Inactivo')?'selected':'' ?>>Inactivo</option>
        </select>
      </div>


      <div class="col-6 col-lg-2">
        <input type="number" class="form-control" name="pmin" min="0" step="1"
               placeholder="Min" value="<?= esc($filters['pmin'] ?? '') ?>">
      </div>
      <div class="col-6 col-lg-2">
        <input type="number" class="form-control" name="pmax" min="0" step="1"
               placeholder="Max" value="<?= esc($filters['pmax'] ?? '') ?>">
      </div>
    <div class="col-12 col-lg-3">
      <div class="d-flex align-items-stretch">
        <select class="form-select" name="per_page">
          <?php foreach([10,20,50] as $pp): ?>
            <option value="<?= $pp ?>" <?= (int)($filters['perPage'] ?? 10) === $pp ? 'selected' : '' ?>>
              <?= $pp ?> por página
            </option>
          <?php endforeach; ?>
        </select>

        <a href="/cuadrantes"
          class="btn btn-light ms-2 d-flex align-items-center justify-content-center flex-shrink-0">
          <i class="fa-solid fa-eraser me-2"></i>
          <span>Limpiar</span>
        </a>
      </div>
    </div>


    </div>


    <!-- Chips -->
    <?php
      $chips = [];
      if (!empty($filters['q']))       $chips[] = ['label'=>'Búsqueda', 'value'=>$filters['q']];
      if (!empty($filters['estado']))  $chips[] = ['label'=>'Estado',   'value'=>$filters['estado']];
      if ($filters['pmin'] !== '' && $filters['pmin'] !== null) $chips[] = ['label'=>'Precio min', 'value'=>$filters['pmin']];
      if ($filters['pmax'] !== '' && $filters['pmax'] !== null) $chips[] = ['label'=>'Precio max', 'value'=>$filters['pmax']];
    ?>
    <?php if (!empty($chips)): ?>
      <div class="mt-2 d-flex flex-wrap gap-2">
        <?php foreach($chips as $c): ?>
          <span class="badge rounded-pill bg-secondary-subtle text-secondary px-3 py-2">
            <i class="fa-solid fa-tag me-1"></i> <?= esc($c['label']) ?>: <strong><?= esc($c['value']) ?></strong>
          </span>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </form>

  <!-- Lista -->
  <div class="row g-4">
    <?php if (empty($cuadrantes)): ?>
      <div class="card-glass p-4 text-center w-100 text-muted">No hay cuadrantes que coincidan.</div>
    <?php else: foreach ($cuadrantes as $c): ?>
      <div class="col-12 col-md-6 col-lg-4">
        <div class="card-glass p-3 h-100 d-flex flex-column list-item" style="border-radius:12px;">
          <div class="d-flex align-items-center mb-3">
            <i class="fa-solid fa-map-location-dot fa-2x text-primary me-3"></i>
            <div>
              <h5 class="mb-0"><?= esc($c['nombre']) ?></h5>
              <small class="text-muted"><?= esc($c['localidad'] ?: 'Sin localidad') ?></small>
            </div>
          </div>

          <div class="small mb-3">
            <div class="d-flex align-items-center mb-1">
              <i class="fa-solid fa-dollar-sign me-2"></i>
              <span class="fw-semibold"><?= number_format((float)($c['precio'] ?? 0), 0, ',', '.') ?></span>
              <span class="ms-3 badge <?= ($c['estado'] ?? 'Activo') === 'Activo' ? 'bg-success' : 'bg-secondary' ?>">
                <?= esc($c['estado'] ?? 'Activo') ?>
              </span>
            </div>
          </div>

          <?php $barriosArr = array_filter(array_map('trim', explode(',', $c['barrios'] ?? ''))); ?>
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
              <div class="text-muted">Sin barrios</div>
            <?php endif; ?>
          </div>

          <div class="mt-auto d-flex justify-content-between align-items-center">
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

              <button type="button" class="btn btn-sm btn-outline-danger"
                data-bs-toggle="modal" data-bs-target="#deleteModal"
                data-id="<?= $c['id'] ?>" data-nombre="<?= esc($c['nombre']) ?>">
                <i class="fa-solid fa-trash"></i>
              </button>
            </div>
          </div>

        </div>
      </div>
    <?php endforeach; endif; ?>
  </div>

  <!-- Paginación -->
  <div class="d-flex justify-content-center mt-4">
    <?= $pager->only(['q','estado','pmin','pmax','per_page'])->links() ?>
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

<!-- Overlay de carga -->
<div id="pageLoading" class="loading-overlay d-none" aria-hidden="true">
  <div class="loading-box text-center">
    <div class="spinner-border" role="status" aria-hidden="true"></div>
    <div class="mt-3 fw-semibold">Cargando ⏳</div>
  </div>
</div>

<?php $content = ob_get_clean(); echo view('layouts/app', compact('content','title')); ?>

<!-- Leaflet (solo si usas el botón "ver mapa") -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin=""/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>

<script>
document.addEventListener('DOMContentLoaded', () => {
  // ====== Modal eliminar ======
  const deleteModal = document.getElementById('deleteModal');
  const deleteForm  = document.getElementById('deleteForm');
  const deleteName  = document.getElementById('deleteName');

  if (deleteModal) {
    deleteModal.addEventListener('show.bs.modal', event => {
      const button = event.relatedTarget;
      const id     = button?.getAttribute('data-id');
      const nombre = button?.getAttribute('data-nombre');
      if (deleteForm && id) deleteForm.action = `/cuadrantes/delete/${id}`;
      if (deleteName) deleteName.textContent = nombre || '';
    });
  }

  // ====== Overlay ======
  const overlay = document.getElementById('pageLoading');
  const showLoading = () => {
    if (overlay && overlay.classList.contains('d-none')) {
      overlay.classList.remove('d-none');
      overlay.setAttribute('aria-hidden', 'false');
      document.body.style.cursor = 'progress';
    }
  };
  let delayedTimer = null;
  const startLoadingDelayed = (ms = 250) => {
    clearTimeout(delayedTimer);
    delayedTimer = setTimeout(showLoading, ms);
  };

  // ====== Filtros ======
  const form = document.getElementById('filtersForm');
  if (form) {
    // Submit explícito
    form.addEventListener('submit', () => startLoadingDelayed(250));

    // Debounce de búsqueda
    const qInput = form.querySelector('input[name="q"]');
    let debounceTimer = null;
    if (qInput) {
      qInput.addEventListener('input', () => {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
          startLoadingDelayed(250);
          form.requestSubmit();
        }, 500);
      });
    }

    // Autosubmit en selects/números
    form.querySelectorAll('select, input[type="number"]').forEach(el => {
      el.addEventListener('change', () => {
        startLoadingDelayed(250);
        form.requestSubmit();
      });
    });
  }

  // ====== Paginación y "Limpiar" específicos ======
  document.addEventListener('click', (e) => {
    const a = e.target.closest('a');
    if (!a) return;
    if (a.closest('.pagination') || a.matches('a.btn.btn-light[href="/cuadrantes"]')) {
      startLoadingDelayed(250);
    }
  });

  // ====== Overlay para cualquier navegación interna ======
  document.addEventListener('click', (e) => {
    const a = e.target.closest('a[href]');
    if (!a) return;

    const href = a.getAttribute('href') || '';
    // Ignorar anchors, enlaces que abren componentes BS (modales/offcanvas), o target _blank
    if (href.startsWith('#')) return;
    if (a.hasAttribute('data-bs-toggle')) return;
    if (a.target && a.target.toLowerCase() === '_blank') return;

    // Solo mismas rutas/origen (evita externos)
    const isSameOrigin = href.startsWith('/') || href.startsWith(window.location.origin);
    if (!isSameOrigin) return;

    startLoadingDelayed(250);
  });

  // ====== Fallback universal al abandonar la página ======
  window.addEventListener('beforeunload', () => { showLoading(); });

  // ====== Mapa (Leaflet) ======
  let mapInstance = null, polygonLayer = null;
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

      polygonLayer = L.polygon(puntos, { weight: 2, opacity: 0.9, fillOpacity: 0.2 }).addTo(mapInstance);
      mapInstance.fitBounds(polygonLayer.getBounds(), { padding: [20, 20] });

      document.getElementById("mapTitle").textContent = nombre;
      const modal = new bootstrap.Modal(document.getElementById("mapModal"));
      modal.show();
      setTimeout(() => mapInstance.invalidateSize(), 200);
    });
  });
});
</script>


<style>
  .card-glass { background: rgba(255,255,255,0.85); backdrop-filter: blur(8px); border-radius: 12px; }
  .btn-brand { background: var(--brand, #FF6B00); color:#fff; border:none; }
  .btn-brand:hover { background:#e55e00; color:#fff; }
  .btn-outline-brand { border:1px solid var(--brand, #FF6B00); color:var(--brand, #FF6B00); background:#fff; }
  .btn-outline-brand:hover { background:var(--brand, #FF6B00); color:#fff; }
  .barrios-wrap { display:flex; flex-wrap:wrap; gap:.25rem; max-height:120px; overflow:auto; padding-right:4px; }
  .list-item { transition: transform .2s, box-shadow .2s; }
  .list-item:hover { transform: translateY(-5px); box-shadow: 0 15px 35px rgba(0,0,0,0.15); }

  .pagination { gap:10px; }
  .pagination .page-link { border-radius:8px; padding:8px 14px; }

  /* Overlay */
  .loading-overlay { position:fixed; inset:0; background:rgba(255,255,255,.7); backdrop-filter:blur(2px); z-index:2000; display:flex; align-items:center; justify-content:center; }
  .loading-overlay.d-none { display:none; }
  .loading-box { background:rgba(255,255,255,.95); border-radius:16px; padding:24px 28px; box-shadow:0 10px 30px rgba(0,0,0,0.15); min-width:240px; }
</style>
