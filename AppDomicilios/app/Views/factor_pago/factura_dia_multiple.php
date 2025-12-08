<?php
/**
 * @var string $fecha
 * @var array  $facturas  // lista de bloques: [domiciliario, domiciliarioId, fecha, pedidos[], total, corridaNumero, reglaPago, sin_pedidos?, mensaje?]
 * @var float  $granTotal
 * @var array  $ids
 */

$title = 'Factura diaria (múltiple)';
ob_start();

function pct_fmt($n) {
  $s = rtrim(rtrim(number_format((float)$n, 2, '.', ''), '0'), '.');
  return $s === '' ? '0' : $s;
}
?>

<style>
  .grid {display:grid; gap:1rem}
  @media (min-width: 992px){ .grid { grid-template-columns: 1fr 1fr } }
  .invoice-card{border-radius:14px}
  .summary-sticky{position:sticky; top:0; z-index: 5}
</style>

<div class="container">
  <div class="d-flex justify-content-between align-items-center mt-4 mb-2">
    <h4 class="mb-0">🧾 Factura del día — Múltiples domiciliarios</h4>
    <div class="text-muted">Fecha: <b><?= esc($fecha) ?></b></div>
  </div>

  <div class="alert alert-info d-flex align-items-center gap-2">
    <i class="fa-solid fa-circle-info"></i>
    Cada bloque aplica la misma regla escalonada, pero contando los pedidos <u>dentro del día por domiciliario</u>.
  </div>

  <div class="grid">
    <?php foreach ($facturas as $f): ?>
      <div class="invoice invoice-card card-glass">
        <div class="p-3">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <h6 class="mb-1">Domiciliario: <b><?= esc($f['domiciliario']) ?></b></h6>
              <div class="small text-muted">
                Corrida #<?= (int)$f['corridaNumero'] ?> &middot; Regla: <b><?= esc($f['reglaPago'] ?: '—') ?></b>
              </div>
            </div>
            <span class="badge text-bg-secondary">ID <?= (int)$f['domiciliarioId'] ?></span>
          </div>

          <?php if (!empty($f['sin_pedidos'])): ?>
            <div class="alert alert-light border mt-3 mb-0">
              <?= esc($f['mensaje'] ?? 'Sin pedidos') ?>
            </div>
          <?php else: ?>
            <div class="table-responsive mt-3">
              <table class="table table-sm align-middle table-hover">
                <thead class="table-light">
                  <tr>
                    <th># Pedido</th>
                    <th>Cuadrante</th>
                    <th class="text-end">% aplicado</th>
                    <th class="text-end">Monto a pagar</th>
                    <th>Hora</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($f['pedidos'] as $p): ?>
                    <?php
                      $base = (float)($p['monto'] ?? 0);
                      $calc = (float)($p['monto_calculado'] ?? $base);
                      $pct  = (float)($p['porcentaje_aplicado'] ?? 100);
                    ?>
                    <tr>
                      <td><?= (int)($p['id'] ?? 0) ?></td>
                      <td><?= esc($p['cuadrante'] ?? '-') ?></td>
                      <td class="text-end"><?= pct_fmt($pct) ?>%</td>
                      <td class="text-end">
                        <?= cop($calc) ?>
                        <?php if ($calc < $base): ?>
                          <div class="small text-muted">Base: <?= cop($base) ?></div>
                        <?php endif; ?>
                      </td>
                      <td><?= isset($p['created_at']) ? date('H:i', strtotime($p['created_at'])) : '--:--' ?></td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
                <tfoot>
                  <tr>
                    <th colspan="3" class="text-end">Subtotal</th>
                    <th class="text-end fs-6"><?= cop((float)$f['total']) ?></th>
                    <th></th>
                  </tr>
                </tfoot>
              </table>
            </div>
          <?php endif; ?>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

  <div class="card-glass mt-3 p-3 summary-sticky">
    <div class="d-flex align-items-center justify-content-between">
      <div class="fs-5">Gran total a pagar: <b><?= cop((float)$granTotal) ?></b></div>
      <div class="d-flex gap-2">
        <a class="btn btn-outline-secondary" href="/factor-pago"><i class="fa-solid fa-arrow-left-long me-1"></i> Volver</a>

        <?php if ($granTotal > 0): ?>
          <form method="post" action="/factor-pago/pagar-dia-multiple" class="d-inline m-0" data-loading-submit>
            <?= csrf_field() ?>
            <?php foreach ($ids as $id): ?>
              <input type="hidden" name="domiciliario_ids[]" value="<?= (int)$id ?>">
            <?php endforeach; ?>
            <input type="hidden" name="fecha" value="<?= esc($fecha) ?>">
            <button type="submit" class="btn btn-success btn-sm" data-loading-text="Pagando ⏳">
              <i class="fa-solid fa-cash-register me-1"></i> Pagar todo
            </button>
          </form>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<?php $content = ob_get_clean(); echo view('layouts/app', compact('content','title')); ?>
