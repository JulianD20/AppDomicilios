<?php $title = 'Factura Pedido'; ob_start(); ?>
<div class="container">
  <div class="invoice mt-4">
    <div class="p-4">
      <div class="d-flex justify-content-between">
        <div>
          <h5>Factura - Pedido #<?= (int)($pedido['id'] ?? 0) ?></h5>
          <div class="muted small">
            Fecha:
            <?= isset($pedido['fecha_hora'])
                  ? date('Y-m-d H:i', strtotime($pedido['fecha_hora']))
                  : date('Y-m-d') ?>
          </div>
        </div>
        <div class="text-end">
          <h5 class="text-muted">AppDomicilios</h5>
        </div>
      </div>

      <hr>
      <div class="row">
        <div class="col-6">
          <strong>Domiciliario</strong>
          <div class="muted small">
            <?= esc($pedido['domiciliario'] ?? ('ID '.($pedido['domiciliario_id'] ?? '-'))) ?>
          </div>
        </div>
        <div class="col-6 text-end">
          <strong>Cuadrante</strong>
          <div class="muted small">
            <?= esc($pedido['cuadrante'] ?? ('ID '.($pedido['cuadrante_id'] ?? '-'))) ?>
          </div>
        </div>
      </div>

      <div class="mt-3 small">
        <div><strong>Dirección:</strong> <?= esc($pedido['direccion'] ?? '-') ?></div>
        <div><strong>Estado:</strong> <?= esc($pedido['estado'] ?? '-') ?></div>
        <div><strong>Notas:</strong> <?= esc($pedido['notas'] ?? '-') ?></div>
      </div>

      <div class="mt-4 d-flex justify-content-between align-items-center">
        <div class="muted">Método de pago: A convenir</div>
        <div class="fs-4">
          <strong>
            $<?= number_format((float)($pedido['precio_unitario'] ?? $pedido['monto'] ?? 0), 0, ',', '.') ?>
          </strong>
        </div>
      </div>
    </div>

    <div class="p-3 text-end">
      <a class="btn btn-outline-secondary" href="/pedidos">Volver</a>
      <button onclick="window.print()" class="btn btn-brand">Imprimir</button>
    </div>
  </div>
</div>
<?php $content = ob_get_clean(); echo view("layouts/app", compact("content","title")); ?>
