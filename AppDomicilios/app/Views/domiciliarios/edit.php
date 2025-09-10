<?php $title = 'Editar Domiciliario'; ob_start(); ?>
<div class="container">
  <div class="card-glass p-4">
    <div class="d-flex align-items-center mb-4">
      <i class="fa-solid fa-motorcycle fa-lg me-2 text-muted"></i>
      <h4 class="mb-0">Editar Domiciliario</h4>
    </div>

    <form method="post" action="/domiciliarios/update/<?= $domiciliario['id'] ?>" class="mt-3">
      <?= csrf_field() ?>


      <div class="row g-3 mb-3">
        <!-- Nombre -->
        <div class="col-md-6">
          <label class="form-label">Nombre</label>
          <div class="input-group">
            <span class="input-group-text"><i class="fa-solid fa-user"></i></span>
            <input class="form-control" name="nombre" value="<?= esc($domiciliario['nombre']) ?>" required>
          </div>
        </div>

        <!-- Teléfono -->
        <div class="col-md-6">
          <label class="form-label">Teléfono</label>
          <div class="input-group">
            <span class="input-group-text"><i class="fa-solid fa-phone"></i></span>
            <input class="form-control" name="telefono" value="<?= esc($domiciliario['telefono']) ?>" required>
          </div>
        </div>
      </div>

      <div class="row g-3 mb-3">
        <!-- Cédula -->
        <div class="col-md-6">
          <label class="form-label">Cédula</label>
          <div class="input-group">
            <span class="input-group-text"><i class="fa-solid fa-id-card"></i></span>
            <input class="form-control" name="cedula" value="<?= esc($domiciliario['cedula']) ?>" required>
          </div>
        </div>

        <!-- Estado -->
        <div class="col-md-6">
          <label class="form-label">Estado</label>
          <div class="input-group">
            <span class="input-group-text"><i class="fa-solid fa-toggle-on"></i></span>
            <select class="form-select" name="estado" required>
              <option value="Activo" <?= $domiciliario['estado'] == 'Activo' ? 'selected' : '' ?>>Activo</option>
              <option value="Inactivo" <?= $domiciliario['estado'] == 'Inactivo' ? 'selected' : '' ?>>Inactivo</option>
            </select>
          </div>
        </div>
      </div>

      <div class="row g-3 mb-3">
        <!-- Fecha de ingreso -->
        <div class="col-md-6">
          <label class="form-label">Fecha de ingreso</label>
          <div class="input-group">
            <span class="input-group-text"><i class="fa-solid fa-calendar-days"></i></span>
            <input type="date" class="form-control" name="fecha_ingreso" value="<?= esc($domiciliario['fecha_ingreso']) ?>" required>
          </div>
        </div>
      </div>

      <!-- Botones -->
      <div class="d-flex gap-2 mt-4">
        <button class="btn btn-brand"><i class="fa-solid fa-save me-1"></i>Actualizar</button>
        <a href="/domiciliarios" class="btn btn-outline-secondary"><i class="fa-solid fa-arrow-left me-1"></i>Cancelar</a>
      </div>
    </form>
  </div>
</div>
<?php $content = ob_get_clean(); echo view('layouts/app', compact('content','title')); ?>
