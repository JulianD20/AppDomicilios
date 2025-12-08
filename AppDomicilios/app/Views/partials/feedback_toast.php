<?php $fb = session('feedback'); if ($fb && ($fb['view'] ?? '') === 'toast'): ?>
<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index:1080;">
  <div id="toastFeedback" class="toast text-bg-<?= esc($fb['bs']) ?> shadow" role="status"
       data-bs-delay="<?= (int)($fb['timeout'] ?: 5000) ?>"
       <?= !empty($fb['persistente']) ? 'data-bs-autohide="false"' : 'data-bs-autohide="true"' ?>>
    <div class="toast-header">
      <span class="me-2"><?= esc($fb['icono']) ?></span>
      <strong class="me-auto"><?= esc($fb['titulo']) ?></strong>
      <small class="text-muted">ahora</small>
      <button type="button" class="btn-close ms-2 mb-1" data-bs-dismiss="toast" aria-label="Cerrar"></button>
    </div>
    <?php if (!empty($fb['mensaje'])): ?>
      <div class="toast-body"><?= esc($fb['mensaje']) ?></div>
    <?php endif; ?>
  </div>
</div>
<script>
document.addEventListener('DOMContentLoaded',function(){
  const el = document.getElementById('toastFeedback');
  if (!el) return;
  const t = new bootstrap.Toast(el);
  t.show();
});
</script>
<?php endif; ?>
