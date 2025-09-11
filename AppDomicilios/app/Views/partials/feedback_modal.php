<?php $fb = session('feedback'); if ($fb && ($fb['view'] ?? '') === 'modal'): ?>
<div class="modal fade" id="feedbackModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow">
      <div class="modal-body text-center p-4">
        <div class="display-6 mb-2"><?= esc($fb['icono']) ?></div>
        <h5 class="mb-1"><?= esc($fb['titulo']) ?></h5>
        <?php if (!empty($fb['mensaje'])): ?>
          <p class="text-body-secondary mb-3"><?= esc($fb['mensaje']) ?></p>
        <?php endif; ?>

        <?php
          $btnClass = in_array($fb['bs'], ['danger','warning']) ? 'btn-danger' : 'btn-primary';
        ?>
        <button type="button" class="btn <?= $btnClass ?>" data-bs-dismiss="modal">Aceptar</button>
      </div>
    </div>
  </div>
</div>
<script>
document.addEventListener('DOMContentLoaded',function(){
  const el = document.getElementById('feedbackModal');
  if (!el) return;
  const m = new bootstrap.Modal(el, { backdrop: 'static', keyboard: true });
  m.show();
});
</script>
<?php endif; ?>
