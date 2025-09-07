<?php $title = 'Domiciliarios'; ob_start(); ?>
<div class="container">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="mb-0">Domiciliarios</h3>
    <a href="/domiciliarios/create" class="btn btn-brand">➕ Nuevo Domiciliario</a>
  </div>

  <?php if(empty($domiciliarios)): ?>
    <div class="card-glass p-4 text-center muted">No hay domiciliarios aún.</div>
  <?php else: ?>
    <div class="table-responsive card-glass p-3">
      <table class="table table-hover align-middle mb-0">
        <thead>
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
              <td><?= esc($d['nombre']) ?></td>
              <td><?= esc($d['telefono']) ?></td>
              <td><?= esc($d['cedula']) ?></td>
              <td>
                <?php if($d['estado'] === 'Activo'): ?>
                  <span class="badge bg-success">Activo</span>
                <?php else: ?>
                  <span class="badge bg-secondary">Inactivo</span>
                <?php endif; ?>
              </td>
              <td><?= esc($d['fecha_ingreso']) ?></td>
              <td class="text-end">
                <!-- Botón Ver -->
                <a href="/domiciliarios/show/<?= $d['id'] ?>" class="btn btn-sm btn-outline-secondary">
                  <i class="fa-solid fa-eye"></i>
                </a>
                <!-- Botón Editar -->
                <a href="/domiciliarios/edit/<?= $d['id'] ?>" class="btn btn-sm btn-outline-primary">
                  <i class="fa-solid fa-pen"></i>
                </a>
                <!-- Botón Eliminar -->
                <form action="/domiciliarios/delete/<?= $d['id'] ?>" method="post" class="d-inline">
                  <?= csrf_field() ?>
                  <button type="button" class="btn btn-sm btn-outline-danger"
                  data-bs-toggle="modal" data-bs-target="#deleteModal"
                  data-id="<?= $d['id'] ?>" data-nombre="<?= esc($d['nombre']) ?>">
                  <i class="fa-solid fa-trash"></i>
                  </button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <!-- Paginación -->
    <div class="d-flex justify-content-center mt-4">
      <?= $pager->links() ?>
    </div>
  <?php endif; ?>
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
        <p>¿Estás seguro que deseas eliminar al domiciliario <strong id="deleteName"></strong>?</p>
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

  deleteModal.addEventListener('show.bs.modal', event => {
    const button = event.relatedTarget;
    const id = button.getAttribute('data-id');
    const nombre = button.getAttribute('data-nombre');

    deleteForm.action = `/domiciliarios/delete/${id}`;
    deleteName.textContent = nombre;
  });
});
</script>



