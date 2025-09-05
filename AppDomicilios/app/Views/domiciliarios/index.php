<?php $title = 'Domiciliarios'; ob_start(); ?>
<div class="container">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="mb-0">Domiciliarios</h3>
    <a href="/domiciliarios/create" class="btn btn-brand"> ➕ Nuevo Domiciliario</a>
  </div>

  <div class="row g-3">
    <?php if(empty($domiciliarios)): ?>
      <div class="card-glass p-4 text-center muted">No hay domiciliarios aún.</div>
    <?php else: foreach($domiciliarios as $d): ?>
      <div class="col-12 col-md-6 col-lg-4 fade-in-up">
        <div class="card-glass p-3 list-item d-flex justify-content-between align-items-center">
          <div>
            <h5 class="mb-1"><?= esc($d['nombre']) ?></h5>
            <div class="muted small"><?= esc($d['telefono']) ?> · C.C. <?= esc($d['cedula']) ?></div>
          </div>
          <div class="text-end">
            <a href="#" class="btn btn-sm btn-outline-secondary"><i class="fa-solid fa-eye"></i></a>
          </div>
        </div>
      </div>
    <?php endforeach; endif; ?>
  </div>
</div>
<?php $content = ob_get_clean(); echo view('layouts/app', compact('content','title')); ?>
