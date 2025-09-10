<?php $title = 'Historial de Pedidos'; ob_start(); ?>
<div class="container">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h4>📦 Historial de Pedidos</h4>
    <a href="/pedidos/create" class="btn btn-brand">➕ Nuevo Pedido</a>
  </div>

  <div class="card-glass p-3">
    <table class="table table-hover align-middle">
      <thead class="table-light">
        <tr>
          <th>#</th>
          <th>Domiciliario</th>
          <th>Cuadrante</th>
          <th>Monto</th>
          <th>Fecha</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($pedidos)): ?>
          <?php foreach($pedidos as $p): ?>
            <tr>
              <td><?= $p['id'] ?></td>
              <td><?= esc($p['domiciliario']) ?></td>
              <td><?= esc($p['cuadrante']) ?></td>
              <td>$<?= number_format($p['monto'], 2) ?></td>
              <td><?= date('d/m/Y H:i', strtotime($p['created_at'])) ?></td>
              <td>
                <a href="/pedidos/factura/<?= $p['id'] ?>" class="btn btn-sm btn-outline-primary">🧾 Factura</a>

                <!-- Botón Editar -->
                <a href="/pedidos/edit/<?= $p['id'] ?>" class="btn btn-sm btn-outline-primary">
                  <i class="fa-solid fa-pen"></i>
                </a>

              <!-- Botón Eliminar -->
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
            <td colspan="6" class="text-center text-muted">No hay pedidos registrados.</td>
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

<?php $content = ob_get_clean(); echo view('layouts/app', compact('content','title')); ?>

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
