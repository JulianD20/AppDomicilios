<?php $title = 'Domiciliarios'; ob_start(); ?>
<div class="container">

  <!-- Header -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="mb-0"><i class="fa-solid fa-motorcycle me-2 text-muted"></i>Domiciliarios</h3>
    <a href="/domiciliarios/create" class="btn btn-brand">➕ Nuevo Domiciliario</a>
  </div>

  <!-- Lista -->
  <div class="row g-4">
    <?php if (empty($domiciliarios)): ?>
      <div class="card-glass p-4 text-center muted w-100">No hay domiciliarios aún.</div>
    <?php else: foreach ($domiciliarios as $d): ?>
      <div class="col-12 col-md-6 col-lg-4 fade-in-up">
        <div class="card-glass p-3 list-item d-flex justify-content-between align-items-center"
             style="transition: transform .2s, box-shadow .2s; border-radius:12px;">
          
          <div class="d-flex align-items-center">
            <i class="fa-solid fa-person-biking fa-2x text-primary me-3"></i>
            <div>
              <h5 class="mb-1"><?= esc($d['nombre']) ?></h5>
              <div class="small text-muted">
                <div>C.C. <?= esc($d['cedula'] ?? '-') ?></div>
                <div>Tel: <?= esc($d['telefono'] ?? 'Sin teléfono') ?></div>
                <div>Ingreso: <?= esc($d['fecha_ingreso'] ?? '-') ?></div>
              </div>
            </div>
          </div>

          <div class="text-end">
            <span class="badge <?= (int)($d['activo'] ?? 1) ? 'bg-success' : 'bg-secondary' ?>">
              <?= (int)($d['activo'] ?? 1) ? 'Activo' : 'Inactivo' ?>
            </span>
            <div class="mt-2 d-flex gap-2 justify-content-end">
              <!-- Ver pedidos de este domiciliario (si tienes esa ruta/listado) -->
              <a href="/pedidos?domiciliario_id=<?= (int)$d['id'] ?>" class="btn btn-sm btn-outline-secondary" title="Ver pedidos">
                <i class="fa-solid fa-list"></i>
              </a>
              <a href="/domiciliarios/edit/<?= (int)$d['id'] ?>" class="btn btn-sm btn-outline-primary" title="Editar">
                <i class="fa-solid fa-pen-to-square"></i>
              </a>
              <a href="/domiciliarios/delete/<?= (int)$d['id'] ?>" class="btn btn-sm btn-outline-danger" title="Eliminar">
                <i class="fa-solid fa-trash"></i>
              </a>
            </div>
          </div>

        </div>
      </div>
    <?php endforeach; endif; ?>
  </div>
</div>

<?php
$scripts = '
<script>
  // Hover anim
  document.querySelectorAll(".list-item").forEach(card => {
    card.addEventListener("mouseenter", ()=> {
      card.style.transform = "translateY(-5px)";
      card.style.boxShadow = "0 15px 35px rgba(0,0,0,0.15)";
    });
    card.addEventListener("mouseleave", ()=> {
      card.style.transform = "none";
      card.style.boxShadow = "0 8px 20px rgba(15,23,36,0.08)";
    });
  });
</script>
';
?>

<?php $content = ob_get_clean(); echo view('layouts/app', compact('content','title','scripts')); ?>
