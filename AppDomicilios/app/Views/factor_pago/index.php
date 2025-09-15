<?php $title = 'Factor de Pago'; ob_start(); ?>

<div class="container">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold">🧮 Factor de Pago</h4>
    <div class="d-flex gap-2">
      <a href="/pedidos" class="btn btn-outline-secondary">Ver Pedidos</a>
      <button class="btn btn-brand" data-bs-toggle="modal" data-bs-target="#facturaDiaModal">
        🧾 Generar factura por día
      </button>
    </div>
  </div>

  <?php if (session('success')): ?>
    <div class="alert alert-success"><?= esc(session('success')) ?></div>
  <?php endif; ?>
  <?php if (session('error')): ?>
    <div class="alert alert-danger"><?= esc(session('error')) ?></div>
  <?php endif; ?>

  <div class="card-glass p-4">
    <p class="mb-0 text-muted">
      Usa el botón <b>“Generar factura por día”</b> para calcular el pago del domiciliario
      siguiendo la regla: <em>primer pedido 100%, siguientes al 50%.</em>
    </p>
  </div>
</div>

<!-- Modal Factura por día (EXCLUSIVO de este módulo) -->
<div class="modal fade" id="facturaDiaModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content card-glass">
      <div class="modal-header">
        <h5 class="modal-title">Generar factura por día</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>

      <form method="get" action="/factor-pago/factura-dia" data-loading-submit>
        <div class="modal-body">

          <?php if (session('fd_error')): ?>
            <div class="alert alert-warning">
              <?= esc(session('fd_error')) ?>
            </div>
          <?php endif; ?>

          <div class="mb-3">
            <label class="form-label">Domiciliario</label>
            <?php $selDom = (int)(session('fd_domiciliario_id') ?? 0); ?>
            <select name="domiciliario_id" class="form-select" required>
              <option value="">-- Selecciona --</option>
              <?php if (!empty($domiciliarios)): ?>
                <?php foreach ($domiciliarios as $d): ?>
                  <option value="<?= (int)$d['id'] ?>" <?= $selDom === (int)$d['id'] ? 'selected' : '' ?>>
                    <?= esc($d['nombre']) ?>
                  </option>
                <?php endforeach; ?>
              <?php endif; ?>
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label">Fecha</label>
            <input
              type="date"
              name="fecha"
              class="form-control"
              value="<?= esc(session('fd_fecha') ?? date('Y-m-d')) ?>"
              required
            >
          </div>
        </div>

        <div class="modal-footer">
          <?= csrf_field() ?>
          <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cerrar</button>
          <button type="submit" class="btn btn-brand btn-sm" data-loading-text="Generando ⏳">Generar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php $content = ob_get_clean(); echo view('layouts/app', compact('content','title')); ?>

<?php if (!empty($showFacturaDiaModal)): ?>
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const el = document.getElementById('facturaDiaModal');
    const modal = bootstrap.Modal.getOrCreateInstance(el);
    modal.show();
  });
</script>
<?php endif; ?>

<style>
  .card-glass {
    background: rgba(255, 255, 255, 0.85);
    border-radius: 12px;
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
  }
  .btn-brand {
    background: #FF6B00; color: #fff; border-radius: 8px;
    transition: all .2s ease-in-out;
  }
  .btn-brand:hover { background: #e65f00; color: #fff; }
</style>
