<?php $title = 'Detalle del Domiciliario'; ob_start(); ?>
<div class="container">
  <div class="card-glass p-4 shadow-sm">
    <div class="row g-4 align-items-center">

      <!-- Icono -->
      <div class="col-md-4 text-center">
        <img src="<?= base_url('images/domiciliario.png') ?>" 
             alt="Domiciliario" 
             class="img-fluid rounded shadow-sm" 
             style="max-height: 220px;">
      </div>

      <!-- Datos  -->
      <div class="col-md-8">
        <div class="d-flex align-items-center mb-4">
          <h4 class="fw-bold text-brand mb-0">Detalle del Domiciliario</h4>
        </div>

        <div class="row g-3">
          <!-- Nombre -->
          <div class="col-md-6">
            <label class="form-label fw-bold">Nombre</label>
            <input class="form-control" value="<?= esc($domiciliario['nombre']) ?>" readonly>
          </div>

          <!-- Teléfono -->
          <div class="col-md-6">
            <label class="form-label fw-bold">Teléfono</label>
            <input class="form-control" value="<?= esc($domiciliario['telefono']) ?>" readonly>
          </div>

          <!-- Cédula -->
          <div class="col-md-6">
            <label class="form-label fw-bold">Cédula</label>
            <input class="form-control" value="<?= esc($domiciliario['cedula']) ?>" readonly>
          </div>

          <!-- Estado -->
          <div class="col-md-6">
            <label class="form-label fw-bold">Estado</label>
            <input class="form-control" value="<?= esc($domiciliario['estado']) ?>" readonly>
          </div>

          <!-- Fecha de ingreso -->
          <div class="col-md-6">
            <label class="form-label fw-bold">Fecha de Ingreso</label>
            <input class="form-control" value="<?= esc($domiciliario['fecha_ingreso']) ?>" readonly>
          </div>
        </div>

        <!-- Botón volver -->
        <div class="mt-4">
          <a href="/domiciliarios" class="btn btn-outline-secondary">
            <i class="fa-solid fa-arrow-left me-1"></i> Volver
          </a>
        </div>
      </div>

    </div>
  </div>
</div>
<?php $content = ob_get_clean(); echo view('layouts/app', compact('content','title')); ?>

