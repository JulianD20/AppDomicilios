<?php
/** @var string $fecha */
/** @var string $domiciliario */
/** @var int    $domiciliarioId */
/** @var array  $pedidos */
/** @var float  $total */
/** @var int    $corridaNumero */
/** @var string $reglaPago */
/** @var float  $factorPago */

$title = 'Factura diaria';
ob_start();

$pendientesCount = is_array($pedidos) ? count($pedidos) : 0;
$corridasPrevias = max(0, (int)$corridaNumero - 1);
$factorPct       = isset($factorPago) ? (float)$factorPago * 100 : 100;
?>
<style>
  :root{ --brand:#FF6B00;--dark:#0F1724;--bg:#F8FAFC;--card-shadow:0 8px 20px rgba(15,23,36,.08) }
  .invoice-card{border-radius:14px}
  .invoice-header{display:flex;align-items:center;justify-content:space-between;gap:1rem}
  .brand{font-weight:700;letter-spacing:.3px;color:var(--bs-secondary-color)}
  .chip{display:inline-flex;align-items:center;gap:.5rem;padding:.35rem .6rem;border:1px solid var(--bs-border-color);
        border-radius:999px;background-color:rgba(var(--bs-primary-rgb),.04)}
  .chip .num{font-weight:700}
  .stats{display:flex;flex-wrap:wrap;gap:.5rem}
  .stat{padding:.5rem .75rem;border-radius:.75rem;background-color:var(--bs-light);
        border:1px dashed var(--bs-border-color);font-size:.9rem}
  .stat b{font-size:1rem}
  .table tfoot th{background:var(--bs-light);font-weight:700}
  .sticky-actions{position:sticky;bottom:0;background:var(--bs-body-bg);border-top:1px solid var(--bs-border-color);
                  padding:.75rem 1rem;display:flex;align-items:center;justify-content:space-between;gap:1rem}
  @media (max-width:576px){.invoice-header{flex-direction:column;align-items:flex-start}}
  @media print{
    .d-print-none{display:none!important}
    .invoice-card{box-shadow:none;border:0}
    .sticky-actions{display:none!important}
  }
</style>

<div class="container">
  <div class="invoice invoice-card card-glass mt-4">
    <div class="p-4">
      <div class="invoice-header">
        <div>
          <h5 class="mb-1">
            Factura - Domiciliario del día
            <span class="badge rounded-pill text-bg-info ms-1">Corrida #<?= (int)$corridaNumero ?> del día</span>
          </h5>
          <div class="text-muted small">
            Domiciliario: <strong><?= esc($domiciliario) ?></strong> &middot;
            Fecha: <strong><?= esc($fecha) ?></strong>
          </div>
        </div>
        <div class="brand"><i class="fa-solid fa-motorcycle" style="color:#FF6B00;"></i>&nbsp;&nbsp;AppDomicilios</div>
      </div>

      <div class="mt-3 stats">
        <div class="stat">Pendientes hoy: <b><?= (int)$pendientesCount ?></b></div>
        <div class="stat">Corridas previas: <b><?= (int)$corridasPrevias ?></b></div>
        <div class="stat">Total a pagar: <b><?= cop($total) ?></b></div>
        <div class="stat">Regla de pago: <b><?= esc($reglaPago ?? '—') ?></b></div>
      </div>

      <div class="alert alert-info py-2 px-3 mt-3 mb-3">
        <i class="fa-solid fa-circle-info"></i>
        Esta factura incluye únicamente los pedidos <u>pendientes</u> del día.
        <?php if (isset($factorPago) && (float)$factorPago < 1): ?>
          <br>Se aplicó un pago del <b><?= number_format($factorPct, 0) ?>%</b> por tener 2 o más pedidos en el día.
        <?php endif; ?>
      </div>

      <div class="table-responsive">
        <table class="table table-sm align-middle table-hover">
          <thead class="table-light">
            <tr>
              <th># Pedido</th>
              <th>Cuadrante</th>
              <th class="text-end">% aplicado</th> <!-- NUEVO -->
              <th class="text-end">Monto a pagar</th>
              <th>Estado</th>
              <th>Hora</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($pedidos as $p):
              $montoBase = (float)($p['monto'] ?? 0);
              $montoPago = (float)($p['monto_calculado'] ?? $montoBase);
              $pct       = (float)($p['porcentaje_aplicado'] ?? (isset($factorPago) ? $factorPct : 100));
            ?>
              <tr>
                <td><?= (int)($p['id'] ?? 0) ?></td>
                <td><?= esc($p['cuadrante'] ?? '-') ?></td>
                <td class="text-end"><?= rtrim(rtrim(number_format($pct, 2, '.', ''), '0'), '.') ?>%</td> <!-- NUEVO -->
                <td class="text-end">
                  <?= cop($montoPago) ?>
                  <?php if ($montoPago < $montoBase): ?>
                    <div class="small text-muted">Base: <?= cop($montoBase) ?></div>
                  <?php endif; ?>
                </td>
                <td><span class="badge text-bg-warning">Pendiente</span></td>
                <td><?= isset($p['created_at']) ? date('H:i', strtotime($p['created_at'])) : '--:--' ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
          <tfoot>
            <tr>
              <!-- Ajuste de colspans por la nueva columna -->
              <th colspan="3" class="text-end">Total a pagar</th>
              <th class="text-end fs-5"><?= cop($total) ?></th>
              <th colspan="2"></th>
            </tr>
            <?php if (!empty($reglaPago)): ?> <!-- NUEVO: solo si hay regla -->
            <tr>
              <td colspan="6" class="text-muted small">
                Regla actual: <strong><?= esc($reglaPago) ?></strong>
              </td>
            </tr>
            <?php endif; ?>
          </tfoot>
        </table>
      </div>
    </div>

    <!-- Barra de acciones fija (no se imprime) -->
    <div class="sticky-actions d-print-none">
      <a class="btn btn-outline-secondary" href="/factor-pago">
        <i class="fa-solid fa-arrow-left-long me-1"></i> Volver
      </a>

      <div class="d-flex gap-2">
        <button onclick="window.print()" class="btn btn-outline-primary">
          <i class="fa-solid fa-print me-1"></i> Imprimir
        </button>

        <form method="post" action="/factor-pago/pagar-dia" class="d-inline m-0" data-loading-submit>
          <?= csrf_field() ?>
          <input type="hidden" name="domiciliario_id" value="<?= (int)$domiciliarioId ?>">
          <input type="hidden" name="fecha" value="<?= esc($fecha) ?>">
          <button type="submit" class="btn btn-success btn-sm" data-loading-text="Pagando ⏳">
            <i class="fa-solid fa-cash-register me-1"></i>
            Pagar total de la corrida #<?= (int)$corridaNumero ?>
          </button>
        </form>
      </div>
    </div>
  </div>
</div>

<?php $content = ob_get_clean(); echo view('layouts/app', compact('content','title')); ?>
