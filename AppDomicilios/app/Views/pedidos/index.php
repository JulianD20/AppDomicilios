<?php $title = 'Historial de Pedidos'; ob_start(); ?>
<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold">📦 Historial de Pedidos</h4>
    <div class="d-flex gap-2">
      <a href="/pedidos/create" class="btn btn-brand fa-plus">Nuevo Pedido</a>
    </div>
  </div>

  <!-- Filtros -->
  <form id="filtersForm" class="card-glass p-3 shadow-sm mb-3" method="get" action="/pedidos">
    <div class="row g-2 align-items-center">
      <div class="col-12 col-lg-4">
        <div class="input-group">
          <span class="input-group-text"><i class="fa-solid fa-magnifying-glass"></i></span>
          <input type="text" class="form-control" name="q"
                 placeholder="Buscar por domiciliario, cuadrante o dirección…"
                 value="<?= esc($filters['q'] ?? '') ?>" autocomplete="off">
        </div>
      </div>

      <div class="col-6 col-lg-2">
        <select class="form-select" name="estado">
          <option value="">Estado</option>
          <option value="pendiente" <?= (isset($filters['estado']) && $filters['estado']==='pendiente')?'selected':'' ?>>Pendiente</option>
          <option value="pagado"    <?= (isset($filters['estado']) && $filters['estado']==='pagado')?'selected':'' ?>>Pagado</option>
        </select>
      </div>

      <div class="col-6 col-lg-2">
        <select class="form-select" name="domiciliario_id">
          <option value="0">Todos los domiciliarios</option>
          <?php foreach($domiciliarios as $d): ?>
            <option value="<?= $d['id'] ?>" <?= (int)($filters['domId'] ?? 0) === (int)$d['id'] ? 'selected' : '' ?>>
              <?= esc($d['nombre']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="col-6 col-lg-2">
        <select class="form-select" name="cuadrante_id">
          <option value="0">Todos los cuadrantes</option>
          <?php foreach($cuadrantes as $c): ?>
            <option value="<?= $c['id'] ?>" <?= (int)($filters['cuaId'] ?? 0) === (int)$c['id'] ? 'selected' : '' ?>>
              <?= esc($c['nombre']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <!-- per_page + Limpiar juntitos -->
      <div class="col-12 col-lg-2">
        <div class="d-flex align-items-stretch">
          <select class="form-select" name="per_page">
            <?php foreach([10,20,50] as $pp): ?>
              <option value="<?= $pp ?>" <?= (int)($filters['perPage'] ?? 10) === $pp ? 'selected' : '' ?>>
                <?= $pp ?> por página
              </option>
            <?php endforeach; ?>
          </select>

        </div>
      </div>

      <!-- Fechas -->
      <div class="col-6 col-lg-2">
        <input type="date" class="form-control" name="desde" value="<?= esc($filters['desde'] ?? '') ?>" placeholder="Desde">
      </div>
      <div class="col-6 col-lg-2">
        <input type="date" class="form-control" name="hasta" value="<?= esc($filters['hasta'] ?? '') ?>" placeholder="Hasta">
      </div>

      <!-- Monto min/max -->
      <div class="col-6 col-lg-2">
        <input type="number" class="form-control" name="mmin" min="0" step="1"
               placeholder="Monto min" value="<?= esc($filters['mmin'] ?? '') ?>">
      </div>
      <div class="col-6 col-lg-2">
        <input type="number" class="form-control" name="mmax" min="0" step="1"
               placeholder="Monto max" value="<?= esc($filters['mmax'] ?? '') ?>">
      </div>

      <div class="col-12 col-lg-2 text-end">
        <a href="/pedidos" class="btn btn-light ms-2 d-flex align-items-center justify-content-center flex-shrink-0">
          <i class="fa-solid fa-eraser me-1"></i> Limpiar
        </a>
      </div>
    </div>

    <!-- Chips -->
    <?php
      $chips = [];
      if (!empty($filters['q']))       $chips[] = ['label'=>'Búsqueda',  'value'=>$filters['q']];
      if (!empty($filters['estado']))  $chips[] = ['label'=>'Estado',    'value'=>ucfirst($filters['estado'])];
      if (!empty($filters['domId']))   $chips[] = ['label'=>'Domiciliario','value'=>$filters['domId']];
      if (!empty($filters['cuaId']))   $chips[] = ['label'=>'Cuadrante', 'value'=>$filters['cuaId']];
      if (!empty($filters['desde']))   $chips[] = ['label'=>'Desde',     'value'=>$filters['desde']];
      if (!empty($filters['hasta']))   $chips[] = ['label'=>'Hasta',     'value'=>$filters['hasta']];
      if ($filters['mmin'] !== '' && $filters['mmin'] !== null) $chips[] = ['label'=>'Monto min','value'=>$filters['mmin']];
      if ($filters['mmax'] !== '' && $filters['mmax'] !== null) $chips[] = ['label'=>'Monto max','value'=>$filters['mmax']];
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

  <!-- Tabla -->
  <div class="card-glass p-3 shadow-sm">
    <table class="table table-hover table-striped align-middle">
      <thead class="table-light">
        <tr>
          <th>#</th>
          <th>Domiciliario</th>
          <th>Cuadrante</th>
          <th>Dirección</th>
          <th>Monto</th>
          <th>Estado</th>
          <th>Fecha</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($pedidos)): ?>
          <?php foreach($pedidos as $p): ?>
            <tr>
              <td class="fw-semibold"><?= $p['id'] ?></td>
              <td><?= esc($p['domiciliario']) ?></td>
              <td><?= esc($p['cuadrante']) ?></td>
              <td><?= esc($p['direccion']) ?></td>
              <td class="text-success fw-bold"><?= cop($p['monto']) ?></td>
              <td>
                <?php if (!empty($p['pagado'])): ?>
                  <span class="badge bg-success">✔ Pagado</span>
                <?php else: ?>
                  <span class="badge bg-warning text-dark">⏳ Pendiente</span>
                <?php endif; ?>
              </td>
              <td><?= date('d/m/Y H:i', strtotime($p['created_at'])) ?></td>
              <td>
                <a href="/pedidos/factura/<?= $p['id'] ?>" class="btn btn-sm btn-outline-primary">🧾</a>
                <a href="/pedidos/edit/<?= $p['id'] ?>" class="btn btn-sm btn-outline-secondary"><i class="fa-solid fa-pen"></i></a>
                <button type="button" class="btn btn-sm btn-outline-danger"
                  data-bs-toggle="modal" data-bs-target="#deleteModal"
                  data-id="<?= $p['id'] ?>" data-domiciliario="<?= esc($p['domiciliario']) ?>">
                  <i class="fa-solid fa-trash"></i>
                </button>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="8" class="text-center text-muted">No hay pedidos que coincidan.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <!-- Paginación (conserva filtros) -->
  <div class="d-flex justify-content-center mt-3">
    <?= $pager->only(['q','estado','domiciliario_id','cuadrante_id','desde','hasta','mmin','mmax','per_page'])->links() ?>
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
        <p>¿Seguro que deseas eliminar el pedido #<strong id="deleteId"></strong> de <strong id="deleteName"></strong>?</p>
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

<!-- Overlay -->
<div id="pageLoading" class="loading-overlay d-none" aria-hidden="true">
  <div class="loading-box text-center">
    <div class="spinner-border" role="status" aria-hidden="true"></div>
    <div class="mt-3 fw-semibold">Cargando ⏳</div>
  </div>
</div>

<?php $content = ob_get_clean(); echo view('layouts/app', compact('content','title')); ?>

<script>
document.addEventListener('DOMContentLoaded', () => {
  // Modal eliminar
  const deleteModal = document.getElementById('deleteModal');
  const deleteForm  = document.getElementById('deleteForm');
  const deleteName  = document.getElementById('deleteName');
  const deleteId    = document.getElementById('deleteId');

  deleteModal.addEventListener('show.bs.modal', e => {
    const btn  = e.relatedTarget;
    const id   = btn.getAttribute('data-id');
    const name = btn.getAttribute('data-domiciliario');
    deleteForm.action = `/pedidos/delete/${id}`;
    deleteName.textContent = name || '';
    deleteId.textContent   = id || '';
  });

  // Overlay
  const overlay = document.getElementById('pageLoading');
  const showLoading = () => {
    if (overlay.classList.contains('d-none')) {
      overlay.classList.remove('d-none');
      overlay.setAttribute('aria-hidden', 'false');
      document.body.style.cursor = 'progress';
    }
  };
  let t=null; const startLoadingDelayed=(ms=250)=>{ clearTimeout(t); t=setTimeout(showLoading, ms); };

  const form = document.getElementById('filtersForm');
  if (form) {
    form.addEventListener('submit', () => startLoadingDelayed(250));

    // debounce búsqueda
    const q = form.querySelector('input[name="q"]');
    let db=null;
    if (q) {
      q.addEventListener('input', () => {
        clearTimeout(db);
        db = setTimeout(() => { startLoadingDelayed(250); form.requestSubmit(); }, 500);
      });
    }
    // autosubmit en selects/fechas/números
    form.querySelectorAll('select, input[type="date"], input[type="number"]').forEach(el => {
      el.addEventListener('change', () => { startLoadingDelayed(250); form.requestSubmit(); });
    });
  }

  // paginación y limpiar
  document.addEventListener('click', e => {
    const a = e.target.closest('a');
    if (!a) return;
    if (a.closest('.pagination') || a.matches('a.btn.btn-light[href="/pedidos"]')) {
      startLoadingDelayed(250);
    }
  });

  window.addEventListener('beforeunload', () => showLoading());
});
</script>

<style>
  .card-glass{background:rgba(255,255,255,0.85);border-radius:12px;backdrop-filter:blur(10px);box-shadow:0 4px 12px rgba(0,0,0,0.08);}
  .btn-brand{background:#FF6B00;color:#fff;border-radius:8px}
  .btn-brand:hover{background:#e65f00;color:#fff}
  .table thead th{background:#f8f9fa;color:#333;font-weight:600}
  .table tbody tr:hover{background:rgba(13,110,253,.05)}
  .loading-overlay{position:fixed;inset:0;background:rgba(255,255,255,.7);backdrop-filter:blur(2px);z-index:2000;display:flex;align-items:center;justify-content:center}
  .loading-overlay.d-none{display:none}
  .loading-box{background:rgba(255,255,255,.95);border-radius:16px;padding:24px 28px;box-shadow:0 10px 30px rgba(0,0,0,.15);min-width:240px}
</style>
