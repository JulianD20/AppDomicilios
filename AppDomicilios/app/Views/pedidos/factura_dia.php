<?php
/** @var string $fecha */
/** @var string $domiciliario */
/** @var int    $domiciliarioId */
/** @var array  $pedidos */
/** @var float  $total */
/** @var int    $corridaNumero */

$title = 'Factura diaria';
ob_start();

$pendientesCount = is_array($pedidos) ? count($pedidos) : 0;
$corridasPrevias = max(0, (int)$corridaNumero - 1);
?>
<style>
    :root {
    --brand: #FF6B00;
    --dark: #0F1724;
    --bg: #F8FAFC;
    --card-shadow: 0 8px 20px rgba(15, 23, 36, 0.08);
  }
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
        <div class="brand"><i class="fa-solid fa-motorcycle" style="color: #FF6B00;"></i>&nbsp;&nbsp;AppDomicilios</div>
      </div>

      <div class="mt-3 stats">
        <div class="stat">Pendientes hoy: <b><?= (int)$pendientesCount ?></b></div>
        <div class="stat">Corridas previas: <b><?= (int)$corridasPrevias ?></b></div>
        <div class="stat">Total a pagar: <b>$<?= number_format($total, 2) ?></b></div>
      </div>

      <div class="alert alert-info py-2 px-3 mt-3 mb-3">
        <i class="fa-solid fa-circle-info"></i>
        Esta factura incluye únicamente los pedidos <u>pendientes</u> del día.
      </div>

      <div class="table-responsive">
        <table class="table table-sm align-middle table-hover">
          <thead class="table-light">
            <tr>
              <th># Pedido</th>
              <th>Cuadrante</th>
              <th class="text-end">Monto</th>
              <th>Estado</th>
              <th>Hora</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($pedidos as $p): ?>
              <tr>
                <td><?= (int)$p['id'] ?></td>
                <td><?= esc($p['cuadrante'] ?? '-') ?></td>
                <td class="text-end">$<?= number_format((float)$p['monto'], 2) ?></td>
                <td><span class="badge text-bg-warning">Pendiente</span></td>
                <td><?= date('H:i', strtotime($p['created_at'])) ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
          <tfoot>
            <tr>
              <th colspan="2" class="text-end">Total a pagar</th>
              <th class="text-end fs-5">$<?= number_format($total, 2) ?></th>
              <th colspan="2"></th>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>

    <!-- Barra de acciones fija (no se imprime) -->
    <div class="sticky-actions d-print-none">
      <a class="btn btn-outline-secondary" href="/pedidos">
        <i class="fa-solid fa-arrow-left-long me-1"></i> Volver
      </a>

      <div class="d-flex gap-2">
        <button onclick="window.print()" class="btn btn-outline-primary">
          <i class="fa-solid fa-print me-1"></i> Imprimir
        </button>

        <form method="post" action="/pedidos/pagar-dia" class="d-inline m-0" data-loading-submit>
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
