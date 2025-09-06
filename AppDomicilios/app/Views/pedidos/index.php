<?php $title = 'Historial de Pedidos'; ob_start(); ?>
<div class="container">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h4>📦 Historial de Pedidos</h4>
    <a href="/pedidos/create" class="btn btn-brand">➕ Nuevo Pedido</a>
  </div>

  <div class="card-glass p-3">
    <table class="table table-hover align-middle">
      <thead class="table-light">
        <tr>
          <th>#</th>
          <th>Domiciliario</th>
          <th>Cuadrante</th>
          <th>Precio</th>
          <th>Estado</th>
          <th>Fecha</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($pedidos)): ?>
          <?php foreach ($pedidos as $p): ?>
            <?php
              // Lecturas seguras con fallback a IDs
              $nomDomi = $p['domiciliario'] ?? ('ID ' . ($p['domiciliario_id'] ?? '?'));
              $nomCuad = $p['cuadrante']    ?? ('ID ' . ($p['cuadrante_id']    ?? '?'));

              $precio  = (float)($p['precio_unitario'] ?? 0);
              $estado  = $p['estado'] ?? 'asignado';
              $fecha   = $p['fecha_hora'] ?? ($p['creado_en'] ?? null);

              // Badge de estado
              $badgeClass = 'bg-secondary';
              if ($estado === 'entregado') $badgeClass = 'bg-success';
              if ($estado === 'cancelado') $badgeClass = 'bg-danger';
            ?>
            <tr>
              <td><?= (int) $p['id'] ?></td>
              <td><?= esc($nomDomi) ?></td>
              <td><?= esc($nomCuad) ?></td>
              <td>$<?= number_format($precio, 0, ',', '.') ?></td>
              <td><span class="badge <?= $badgeClass ?>"><?= esc($estado) ?></span></td>
              <td><?= $fecha ? date('d/m/Y H:i', strtotime($fecha)) : '-' ?></td>
              <td>
                <a href="/pedidos/factura/<?= (int)$p['id'] ?>" class="btn btn-sm btn-outline-primary">🧾 Factura</a>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="7" class="text-center text-muted">No hay pedidos registrados.</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?php $content = ob_get_clean(); echo view("layouts/app", compact("content","title")); ?>
