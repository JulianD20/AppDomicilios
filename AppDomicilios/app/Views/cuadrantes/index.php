<?php $title = 'Cuadrantes'; ob_start(); ?>
<div class="container">

  <!-- Header -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h3><i class="fa-solid fa-draw-polygon me-2 text-muted"></i>Cuadrantes</h3>
    <a href="/cuadrantes/create" class="btn btn-brand"><i class="fa-solid fa-plus me-1"></i>Crear Cuadrante</a>
  </div>

  <!-- Lista de cuadrantes -->
  <div class="row g-4">
    <?php if(empty($cuadrantes)): ?>
      <div class="card-glass p-4 muted text-center w-100">No hay cuadrantes.</div>
    <?php else: foreach($cuadrantes as $c): ?>
      <div class="col-12 col-md-6 col-lg-4 fade-in-up">
        <div class="card-glass p-3 list-item d-flex flex-column justify-content-between h-100"
             style="transition: transform .2s, box-shadow .2s; border-radius:12px;">
          
          <div class="d-flex align-items-center mb-2">
            <i class="fa-solid fa-map-location-dot fa-2x text-primary me-3"></i>
            <div>
              <h5 class="mb-0"><?= esc($c['nombre']) ?></h5>
              <small class="muted"><?= esc($c['descripcion']) ?></small>
            </div>
          </div>

          <div class="d-flex justify-content-between align-items-center mt-auto">
            <div class="text-muted small">
              <i class="fa-solid fa-dollar-sign me-1"></i><?= isset($c['precio']) ? number_format($c['precio'], 0) : 'N/A' ?>
            </div>
            <div class="d-flex gap-2">
              <a href="#" class="btn btn-sm btn-outline-secondary" title="Ver en mapa">
                <i class="fa-solid fa-eye"></i>
              </a>
              <a href="/cuadrantes/edit/<?= $c['id'] ?>" class="btn btn-sm btn-outline-primary" title="Editar">
                <i class="fa-solid fa-pen-to-square"></i>
              </a>
              <a href="/cuadrantes/delete/<?= $c['id'] ?>" class="btn btn-sm btn-outline-danger" title="Eliminar">
                <i class="fa-solid fa-trash"></i>
              </a>
            </div>
          </div>

        </div>
      </div>
    <?php endforeach; endif; ?>
  </div>
</div>

<?php $scripts = '
<script>
  // Animación hover para las tarjetas
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
'; ?>

<?php $content = ob_get_clean(); echo view('layouts/app', compact('content','title','scripts')); ?>

