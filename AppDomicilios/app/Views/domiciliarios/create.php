<?php $title = 'Crear Domiciliario'; ob_start(); ?>
<div class="container">
  <div class="card-glass p-4">
    <div class="d-flex align-items-center mb-4">
      <i class="fa-solid fa-motorcycle fa-lg" style="color: #FF6B00;"></i>
      <h4 class="mb-0">&nbsp;&nbsp;Crear Domiciliario</h4>
    </div>

    <form method="post" action="/domiciliarios/store" class="mt-3" data-loading-submit>
      <?= csrf_field() ?> <!-- Seguridad contra CSRF -->

      <div class="row g-3 mb-3">
        <!-- Nombre -->
        <div class="col-md-6">
          <label class="form-label">Nombre</label>
          <div class="input-group">
            <span class="input-group-text"><i class="fa-solid fa-user"></i></span>
            <input class="form-control" 
                   name="nombre" 
                   placeholder="Nombre completo" 
                   required
                   minlength="3"
                   maxlength="60"
                   pattern="^[a-zA-ZÁÉÍÓÚáéíóúÑñ\s]+$"
                   title="Solo letras y espacios. Mínimo 3 caracteres.">
          </div>
        </div>

        <!-- Teléfono -->
        <div class="col-md-6">
          <label class="form-label">Teléfono</label>
          <div class="input-group">
            <span class="input-group-text"><i class="fa-solid fa-phone"></i></span>
            <input type="number" class="form-control" 
                   name="telefono" 
                   placeholder="Ej: 3001234567" 
                   required
                   minlength="10"
                   maxlength="10"
                   pattern="^[0-9]{10}$"
                   title="Debe tener exactamente 10 dígitos numéricos.">
          </div>
        </div>
      </div>

      <div class="row g-3 mb-3">
        <!-- Cédula -->
        <div class="col-md-6">
          <label class="form-label">Cédula</label>
          <div class="input-group">
            <span class="input-group-text"><i class="fa-solid fa-id-card"></i></span>
            <input type="number" 
                   class="form-control" 
                   name="cedula" 
                   placeholder="Número de cédula" 
                   required
                   minlength="6"
                   maxlength="12"
                   oninput="if(this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
                   title="Solo números. Entre 6 y 12 dígitos.">
          </div>
        </div>

        <!-- Estado -->
        <div class="col-md-6">
          <label class="form-label">Estado</label>
          <div class="input-group">
            <span class="input-group-text"><i class="fa-solid fa-toggle-on"></i></span>
            <select class="form-select" name="estado" required>
              <option value="Activo" selected>Activo</option>
              <option value="Inactivo">Inactivo</option>
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
            <input type="date" class="form-control" name="fecha_ingreso" required>
          </div>
        </div>
      </div>

      <!-- Botones -->
      <div class="d-flex gap-2 mt-4">
        <button class="btn btn-outline-secondary btn-sm" type="submit" data-loading-text="Guardando ⏳">
          <i class="fa-solid fa-save me-1"></i>Guardar
        </button>
        <a href="/domiciliarios" class="btn btn-outline-secondary">
          <i class="fa-solid fa-arrow-left me-1"></i>Cancelar
        </a>
      </div>
    </form>
  </div>
</div>
<?php $content = ob_get_clean(); echo view('layouts/app', compact('content','title')); ?>
