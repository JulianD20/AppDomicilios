<?php $fb = session('feedback'); if ($fb && ($fb['view'] ?? 'alert') === 'alert'): ?>
<div
  class="alert alert-<?= esc($fb['bs']) ?> <?= empty($fb['persistente']) ? 'alert-dismissible' : '' ?> fade show shadow position-fixed top-0 start-50 translate-middle-x mt-3"
  role="alert"
  style="z-index:1080; max-width:min(92vw, 560px);"
  data-timeout="<?= esc($fb['timeout']) ?>"
  <?= !empty($fb['persistente']) ? 'data-sticky="1"' : '' ?>
>
  <div class="d-flex align-items-start gap-2">
    <div class="fs-5"><?= esc($fb['icono']) ?></div>
    <div>
      <div class="fw-bold"><?= esc($fb['titulo']) ?></div>
      <?php if (!empty($fb['mensaje'])): ?>
        <div class="small text-body-secondary"><?= esc($fb['mensaje']) ?></div>
      <?php endif; ?>
    </div>
  </div>

  <?php if (empty($fb['persistente'])): ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
  <?php endif; ?>
</div>
<?php endif; ?>
