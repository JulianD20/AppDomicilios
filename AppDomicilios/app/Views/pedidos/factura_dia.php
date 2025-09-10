<?php
/** @var string $fecha */
/** @var string $domiciliario */
/** @var int    $domiciliarioId */
/** @var array  $pedidos */
/** @var float  $total */

$title = 'Factura diaria';
ob_start();
?>
<div class="container">
  <div class="invoice mt-4">
    <div class="p-4">
      <div class="d-flex justify-content-between align-items-start">
        <div>
          <h5>Factura - Domiciliario del día</h5>
          <div class="muted small">
            Domiciliario: <strong><?= esc($domiciliario) ?></strong><br>
            Fecha: <strong><?= esc($fecha) ?></strong>
          </div>
        </div>
        <div class="text-end">
          <h5 class="text-muted">AppDomicilios</h5>
        </div>
      </div>

      <hr>

      <div class="table-responsive">
        <table class="table table-sm align-middle">
          <thead class="table-light">
            <tr>
                <th># Pedido</th>
                <th>Cuadrante</th>
                <th>Monto</th>
                <th>Estado</th> 
                <th>Hora</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($pedidos as $p): ?>
              <tr>
                <td><?= (int)$p['id'] ?></td>
                <td><?= esc($p['cuadrante'] ?? '-') ?></td>
                <td>$<?= number_format((float)$p['monto'], 2) ?></td>
                <td><?= !empty($p['pagado']) ? 'Pagado' : 'Pendiente' ?></td> 
                <td><?= date('H:i', strtotime($p['created_at'])) ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
          <tfoot>
            <tr>
              <th colspan="2" class="text-end">Total</th>
              <th colspan="2" class="fs-5">$<?= number_format($total, 2) ?></th>
            </tr>
          </tfoot>
        </table>
      </div>

    <div class="d-flex justify-content-between mt-3 d-print-none">
        <a class="btn btn-outline-secondary" href="/pedidos">Volver</a>

        <div class="d-flex gap-2">
            <button onclick="window.print()" class="btn btn-brand">Imprimir</button>

            <form method="post" action="/pedidos/pagar-dia" class="d-inline m-0">
            <?= csrf_field() ?>
            <input type="hidden" name="domiciliario_id" value="<?= (int)$domiciliarioId ?>">
            <input type="hidden" name="fecha" value="<?= esc($fecha) ?>">
            <?php $hayPendientes = array_reduce($pedidos, fn($c,$x)=> $c || empty($x['pagado']), false); ?>
            <button type="submit" class="btn btn-success" <?= $hayPendientes ? '' : 'disabled' ?>>
            Pagar total
            </button>
            </form>
        </div>
    </div>

    </div>
  </div>
</div>
<?php $content = ob_get_clean(); echo view('layouts/app', compact('content','title')); ?>
