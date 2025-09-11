<?php $title = 'Factura Pedido';
ob_start(); ?>
<div class="container">
  <div class="invoice mt-4">
    <div class="p-4">
      <div class="d-flex justify-content-between">
        <div>
          <h5>Factura - Pedido #<?= $pedido['id'] ?? '' ?></h5>
          <div class="muted small">Fecha: <?= date('Y-m-d') ?></div>
        </div>
        <div class="text-end">
          <h5 class="text-muted">AppDomicilios</h5>
        </div>
      </div>

      <hr>
      <div class="row">
        <div class="col-6">
          <strong>Domiciliario</strong>
          <div class="muted small"><?= esc($pedido['domiciliario'] ?? '-') ?></div>
        </div>
        <div class="col-6 text-end">
          <strong>Cuadrante</strong>
          <div class="muted small"><?= esc($pedido['cuadrante'] ?? '-') ?></div>
        </div>
        <div class="col-6 ">
          <strong>Dirección</strong>
          <div class="muted small"><?= esc($pedido['direccion'] ?? '-') ?></div>
        </div>
      </div>

      <div class="mt-4 d-flex justify-content-between align-items-center">
        <div class="muted">Método de pago: A convenir</div>
        <div class="fs-4"><strong>$<?= number_format($pedido['monto'] ?? 0, 2) ?></strong></div>
      </div>

    </div>

    <div class="p-3 text-end">
      <a class="btn btn-outline-secondary" href="/pedidos">Volver</a>
      <button onclick="window.print()" class="btn btn-brand">Imprimir</button>
    </div>
  </div>
</div>
<?php $content = ob_get_clean();
echo view("layouts/app", compact("content", "title")); ?>