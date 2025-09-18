<?php $title = 'Domiciliarios'; ob_start(); ?>
<div class="container py-4">

  <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-3">
    <h3 class="fw-bold text-brand mb-0">
      <i class="fa-solid fa-motorcycle me-2 text-orange"></i> Domiciliarios
    </h3>

    <div class="d-flex gap-2">
      <a href="/domiciliarios/create" class="btn btn-orange shadow-sm">
        <i class="fa-solid fa-plus me-1"></i> Nuevo Domiciliario
      </a>
    </div>
  </div>

  <!-- Barra de búsqueda / filtros -->
  <form id="filtersForm" class="card-glass shadow-sm p-3 mb-3" method="get" action="/domiciliarios">
    <div class="row g-2 align-items-center">
      <div class="col-12 col-md-6">
        <div class="input-group">
          <span class="input-group-text"><i class="fa-solid fa-magnifying-glass"></i></span>
          <input
            type="text"
            class="form-control"
            name="q"
            placeholder="Buscar por nombre, teléfono o cédula…"
            value="<?= esc($filters['q'] ?? '') ?>"
            autocomplete="off"
          >
        </div>
      </div>

      <div class="col-6 col-md-3">
        <select class="form-select" name="estado">
          <option value="">Todos los estados</option>
          <option value="Activo"   <?= (isset($filters['estado']) && $filters['estado']==='Activo')?'selected':'' ?>>Activo</option>
          <option value="Inactivo" <?= (isset($filters['estado']) && $filters['estado']==='Inactivo')?'selected':'' ?>>Inactivo</option>
        </select>
      </div>

      <div class="col-6 col-md-3">
        <select class="form-select" name="per_page">
          <?php foreach([10,20,50] as $pp): ?>
            <option value="<?= $pp ?>" <?= (int)($filters['perPage'] ?? 10) === $pp ? 'selected' : '' ?>>
              <?= $pp ?> por página
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <!-- Rango de fechas (visible en md+) -->
      <div class="col-6 col-md-3 d-none d-md-block">
        <input type="date" class="form-control" name="desde" value="<?= esc($filters['desde'] ?? '') ?>" placeholder="Desde">
      </div>
      <div class="col-6 col-md-3 d-none d-md-block">
        <input type="date" class="form-control" name="hasta" value="<?= esc($filters['hasta'] ?? '') ?>" placeholder="Hasta">
      </div>

      <div class="col-12 col-md-3 text-end">

          <a href="/domiciliarios" class="btn btn-light ms-2 d-flex align-items-center justify-content-center flex-shrink-0">
            <i class="fa-solid fa-eraser me-1"></i> Limpiar
          </a>

      </div>
    </div>

    <!-- Chips de filtros activos -->
    <?php
      $chips = [];
      if (!empty($filters['q']))      $chips[] = ['label'=>'Búsqueda', 'value'=>$filters['q']];
      if (!empty($filters['estado'])) $chips[] = ['label'=>'Estado',   'value'=>$filters['estado']];
      if (!empty($filters['desde']))  $chips[] = ['label'=>'Desde',    'value'=>$filters['desde']];
      if (!empty($filters['hasta']))  $chips[] = ['label'=>'Hasta',    'value'=>$filters['hasta']];
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

  <?php if(empty($domiciliarios)): ?>
    <div class="card-glass p-4 text-center text-muted shadow-sm">
      <i class="fa-solid fa-circle-info mb-2 fs-4"></i>
      <p class="mb-0">No hay domiciliarios que coincidan con tus filtros.</p>
    </div>
  <?php else: ?>
    <div class="card-glass shadow-sm p-0">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th>Nombre</th>
              <th>Teléfono</th>
              <th>Cédula</th>
              <th>Estado</th>
              <th>Fecha de Ingreso</th>
              <th class="text-end">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach($domiciliarios as $d): ?>
              <tr>
                <td class="fw-semibold"><?= esc($d['nombre']) ?></td>
                <td><?= esc($d['telefono']) ?></td>
                <td><?= esc($d['cedula']) ?></td>
                <td>
                  <?php if($d['estado'] === 'Activo'): ?>
                    <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill">
                      <i class="fa-solid fa-check-circle me-1"></i> Activo
                    </span>
                  <?php else: ?>
                    <span class="badge bg-secondary-subtle text-secondary px-3 py-2 rounded-pill">
                      <i class="fa-solid fa-circle-xmark me-1"></i> Inactivo
                    </span>
                  <?php endif; ?>
                </td>
                <td><?= esc($d['fecha_ingreso']) ?></td>
                <td class="text-end">
                  <div class="btn-group">
                    <a href="/domiciliarios/show/<?= $d['id'] ?>" class="btn btn-sm btn-outline-secondary" title="Ver">
                      <i class="fa-solid fa-eye"></i>
                    </a>
                    <a href="/domiciliarios/edit/<?= $d['id'] ?>" class="btn btn-sm btn-outline-primary" title="Editar">
                      <i class="fa-solid fa-pen"></i>
                    </a>
                    <button type="button" class="btn btn-sm btn-outline-danger"
                      data-bs-toggle="modal" data-bs-target="#deleteModal"
                      data-id="<?= $d['id'] ?>" data-nombre="<?= esc($d['nombre']) ?>"
                      title="Eliminar">
                      <i class="fa-solid fa-trash"></i>
                    </button>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Paginación (conserva filtros) -->
    <div class="d-flex justify-content-center mt-4">
      <?= $pager->only(['q','estado','desde','hasta','per_page'])->links() ?>
    </div>
  <?php endif; ?>
</div>

<!-- Modal Eliminar (fuera de la tabla) -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg rounded-3">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title">
          <i class="fa-solid fa-triangle-exclamation me-2"></i> Confirmar Eliminación
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <p>¿Estás seguro que deseas eliminar al domiciliario <strong id="deleteName"></strong>?</p>
      </div>
      <div class="modal-footer">
        <form id="deleteForm" method="post">
          <?= csrf_field() ?>
          <input type="hidden" name="_method" value="DELETE">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-danger"><i class="fa-solid fa-trash me-1"></i>Eliminar</button>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Overlay de carga (fuera de la tabla) -->
<div id="pageLoading" class="loading-overlay d-none" aria-hidden="true">
  <div class="loading-box text-center">
    <div class="spinner-border" role="status" aria-hidden="true"></div>
    <div class="mt-3 fw-semibold">Cargando ⏳</div>
  </div>
</div>

<?php $content = ob_get_clean(); echo view('layouts/app', compact('content','title')); ?>

<!-- JS: modal eliminar + overlay con delay -->
<script>
document.addEventListener('DOMContentLoaded', () => {
  // ===== Modal eliminar =====
  const deleteModal = document.getElementById('deleteModal');
  const deleteForm  = document.getElementById('deleteForm');
  const deleteName  = document.getElementById('deleteName');

  if (deleteModal) {
    deleteModal.addEventListener('show.bs.modal', event => {
      const button = event.relatedTarget;
      const id     = button?.getAttribute('data-id');
      const nombre = button?.getAttribute('data-nombre');
      if (deleteForm && id) deleteForm.action = `/domiciliarios/delete/${id}`;
      if (deleteName) deleteName.textContent = nombre || '';
    });
  }

  // ===== Overlay =====
  const overlay = document.getElementById('pageLoading');
  const showLoading = () => {
    if (overlay && overlay.classList.contains('d-none')) {
      overlay.classList.remove('d-none');
      overlay.setAttribute('aria-hidden', 'false');
      document.body.style.cursor = 'progress';
    }
  };
  const hideLoading = () => {
    if (overlay) {
      overlay.classList.add('d-none');
      overlay.setAttribute('aria-hidden', 'true');
      document.body.style.cursor = '';
    }
  };

  // Delay para evitar parpadeo
  let delayedTimer = null;
  const startLoadingDelayed = (ms = 250) => {
    clearTimeout(delayedTimer);
    delayedTimer = setTimeout(showLoading, ms);
  };

  // ===== Filtros / búsqueda =====
  const form = document.getElementById('filtersForm');
  if (form) {
    // Submit explícito (botón Aplicar)
    form.addEventListener('submit', () => startLoadingDelayed(250));

    // Debounce input búsqueda
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

    // Auto-submit en selects/fechas
    form.querySelectorAll('select, input[type="date"]').forEach(el => {
      el.addEventListener('change', () => {
        startLoadingDelayed(250);
        form.requestSubmit();
      });
    });
  }

  // Paginación y botón "Limpiar"
  document.addEventListener('click', (e) => {
    const a = e.target.closest('a');
    if (!a) return;
    if (a.closest('.pagination') || a.matches('a.btn.btn-light[href="/domiciliarios"]')) {
      startLoadingDelayed(250);
    }
  });

  // En navegación real, muestra overlay inmediato
  window.addEventListener('beforeunload', showLoading);
});
</script>

<!-- Estilos -->
<style>
  /* Estilos para filtros */
  .card-glass {
    background: rgba(255,255,255,0.85);
    backdrop-filter: blur(10px);
    border-radius: 16px;
  }
  .text-orange { color: #FF6B00 !important; }
  .btn-orange { background-color:#FF6B00; color:#fff; border:none; }
  .btn-orange:hover { background-color:#e55f00; color:#fff; }

  .input-group .form-control:focus,
  .form-select:focus,
  input[type="date"]:focus {
    box-shadow: 0 0 0 .25rem rgba(255,107,0,.25);
    border-color: #FF6B00;
  }

  .badge.bg-secondary-subtle {
    background: #f2f2f3 !important;
    border: 1px solid #e6e6e7;
  }

  .pagination { gap: 10px; margin-top: 1rem; }
  .pagination .page-link { border-radius: 8px; padding: 8px 14px; }

  /* Overlay de carga */
  .loading-overlay {
    position: fixed;
    inset: 0;
    background: rgba(255,255,255,0.7);
    backdrop-filter: blur(2px);
    z-index: 2000;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: opacity .15s ease-in-out;
  }
  .loading-overlay.d-none { display: none; }
  .loading-box {
    background: rgba(255,255,255,0.95);
    border-radius: 16px;
    padding: 24px 28px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.15);
    min-width: 240px;
  }
</style>
