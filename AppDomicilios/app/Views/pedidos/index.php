<?php $title = 'Historial de Pedidos'; ob_start(); ?>

<div class="container">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold text">📦 Historial de Pedidos</h4>

    <div class="d-flex gap-2">
    <button class="btn btn-brand fa-plus" data-bs-toggle="modal" data-bs-target="#facturaDiaModal">
      🧾 Factura por día
    </button>

    <a href="/pedidos/create" class="btn btn-brand fa-plus"> Nuevo Pedido</a>
    </div>
  </div>



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
              <td class="text-success fw-bold">$<?= number_format($p['monto'], 2) ?></td>
              <td>
                <?php if (!empty($p['pagado'])): ?>
                  <?php
                    $tz = 'America/Bogota';
                    $pagadoAt = $p['pagado_at'] ? \CodeIgniter\I18n\Time::parse($p['pagado_at'])->setTimezone($tz) : null;
                    $fechaCorta = $pagadoAt ? $pagadoAt->toLocalizedString('EEE d MMM, HH:mm') : '';
                    $fechaLarga = $pagadoAt ? $pagadoAt->toLocalizedString("EEEE d 'de' MMMM 'de' y, HH:mm:ss") : '';
                    $rel = $pagadoAt ? $pagadoAt->humanize() : '';
                  ?>
                  <span class="badge bg-success" data-bs-toggle="tooltip" title="<?= esc($fechaLarga) ?>">
                    ✔ Pagado
                  </span>
                  <?php if ($pagadoAt): ?>
                    <div class="small text-muted">
                      <?= esc($fechaCorta) ?> <span>(<?= esc($rel) ?>)</span>
                    </div>
                  <?php endif; ?>
                <?php else: ?>
                  <span class="badge bg-warning text-dark">⏳ Pendiente</span>
                <?php endif; ?>
              </td>
              <td><?= date('d/m/Y H:i', strtotime($p['created_at'])) ?></td>
              <td>
                <a href="/pedidos/factura/<?= $p['id'] ?>" class="btn btn-sm btn-outline-primary">
                  🧾 Factura
                </a>

                <a href="/pedidos/edit/<?= $p['id'] ?>" class="btn btn-sm btn-outline-secondary">
                  <i class="fa-solid fa-pen"></i>
                </a>

                <form action="/pedidos/delete/<?= $p['id'] ?>" method="post" class="d-inline">
                  <?= csrf_field() ?>
                  <button type="button" class="btn btn-sm btn-outline-danger"
                    data-bs-toggle="modal" data-bs-target="#deleteModal"
                    data-id="<?= $p['id'] ?>" data-domiciliario="<?= esc($p['domiciliario']) ?>">
                    <i class="fa-solid fa-trash"></i>
                  </button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="7" class="text-center text-muted">No hay pedidos registrados.</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
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
        <p>¿Estás seguro que deseas eliminar el pedido # <strong id="deleteId"></strong> de <strong id="deleteName"></strong>?</p>
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

<!-- Modal Factura por día -->
<div class="modal fade" id="facturaDiaModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content card-glass">
      <div class="modal-header">
        <h5 class="modal-title">Generar factura por día</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>

      <form method="get" action="/pedidos/factura-dia">
        <div class="modal-body">

          <?php if (session('fd_error')): ?>
            <div class="alert alert-warning">
              <?= esc(session('fd_error')) ?>
            </div>
          <?php endif; ?>

          <div class="mb-3">
            <label class="form-label">Domiciliario</label>
            <?php $selDom = (int)(session('fd_domiciliario_id') ?? 0); ?>
            <select name="domiciliario_id" class="form-select" required>
              <option value="">-- Selecciona --</option>
              <?php if (!empty($domiciliarios)): ?>
                <?php foreach ($domiciliarios as $d): ?>
                  <option value="<?= (int)$d['id'] ?>" <?= $selDom === (int)$d['id'] ? 'selected' : '' ?>>
                    <?= esc($d['nombre']) ?>
                  </option>
                <?php endforeach; ?>
              <?php endif; ?>
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label">Fecha</label>
            <input
              type="date"
              name="fecha"
              class="form-control"
              value="<?= esc(session('fd_fecha') ?? date('Y-m-d')) ?>"
              required
            >
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cerrar</button>
          <button type="submit" class="btn btn-brand">Generar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php $content = ob_get_clean(); echo view('layouts/app', compact('content','title')); ?>

<?php
$mustOpenFacturaModal = session('showFacturaDiaModal')
    || session('fd_error')
    || session('fd_domiciliario_id')
    || session('fd_fecha');
?>
<?php if ($mustOpenFacturaModal): ?>
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const el = document.getElementById('facturaDiaModal');
    const modal = bootstrap.Modal.getOrCreateInstance(el);
    modal.show();
  });
</script>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const deleteModal = document.getElementById('deleteModal');
  const deleteForm = document.getElementById('deleteForm');
  const deleteName = document.getElementById('deleteName');
  const deleteId = document.getElementById('deleteId');

  deleteModal.addEventListener('show.bs.modal', event => {
    const button = event.relatedTarget;
    const id = button.getAttribute('data-id');
    const domiciliario = button.getAttribute('data-domiciliario');

    deleteForm.action = `/pedidos/delete/${id}`;
    deleteName.textContent = domiciliario;
    deleteId.textContent = id;
  });
});
</script>

<style>
  .card-glass {
    background: rgba(255, 255, 255, 0.85);
    border-radius: 12px;
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
  }

  /* Tabla */
  .table thead th {
    background: #f8f9fa;
    color: #333;
    font-weight: 600;
  }

  .table tbody tr:hover {
    background: rgba(13, 110, 253, 0.05);
  }

  /* Badges */
  .badge {
    font-size: 0.85rem;
    padding: 6px 10px;
    border-radius: 6px;
  }

  /* Botones custom */
  .btn-brand {
    background: #FF6B00;
    color: white;
    border-radius: 8px;
    transition: all 0.2s ease-in-out;
  }
  
  .btn-brand:hover {
    background: #e65f00;
    color: #fff;
  }
</style>
