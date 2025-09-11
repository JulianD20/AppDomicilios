<?php $title = 'Domiciliarios'; ob_start(); ?>
<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold text-brand mb-0">
      <i class="fa-solid fa-motorcycle me-2 text-orange"></i> Domiciliarios
    </h3>
    <a href="/domiciliarios/create" class="btn btn-orange shadow-sm">
      <i class="fa-solid fa-plus me-1"></i> Nuevo Domiciliario
    </a>
  </div>

  <?php if(empty($domiciliarios)): ?>
    <div class="card-glass p-4 text-center text-muted shadow-sm">
      <i class="fa-solid fa-circle-info mb-2 fs-4"></i>
      <p class="mb-0">No hay domiciliarios aún.</p>
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
                    <!-- Botón Ver -->
                    <a href="/domiciliarios/show/<?= $d['id'] ?>" class="btn btn-sm btn-outline-secondary" title="Ver">
                      <i class="fa-solid fa-eye"></i>
                    </a>
                    <!-- Botón Editar -->
                    <a href="/domiciliarios/edit/<?= $d['id'] ?>" class="btn btn-sm btn-outline-primary" title="Editar">
                      <i class="fa-solid fa-pen"></i>
                    </a>
                    <!-- Botón Eliminar -->
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

    <!-- Paginación -->
    <div class="d-flex justify-content-center mt-4">
      <?= $pager->links() ?>
    </div>
  <?php endif; ?>
</div>

<!-- Modal Eliminar -->
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

<style>
  /* Color personalizado */
  .text-orange {
    color: #FF6B00 !important;
  }

  .btn-orange {
    background-color: #FF6B00;
    color: #fff;
    border: none;
  }

  .btn-orange:hover {
    background-color: #e55f00;
    color: #fff;
  }

  /* Estilo paginación */
  .pagination {
    gap: 10px;
  }

  .pagination .page-link {
    border-radius: 8px;
    padding: 8px 14px;
  }

  .pagination {
    margin-top: 1rem;
  }
</style>

