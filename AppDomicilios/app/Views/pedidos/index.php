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
          <th>Monto</th>
          <th>Fecha</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($pedidos)): ?>
          <?php foreach($pedidos as $p): ?>
            <tr>
              <td><?= $p['id'] ?></td>
              <td><?= esc($p['domiciliario']) ?></td>
              <td><?= esc($p['cuadrante']) ?></td>
              <td>$<?= number_format($p['monto'], 2) ?></td>
              <td><?= date('d/m/Y H:i', strtotime($p['created_at'])) ?></td>
              <td>
                <a href="/pedidos/factura/<?= $p['id'] ?>" class="btn btn-sm btn-outline-primary">🧾 Factura</a>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="6" class="text-center text-muted">No hay pedidos registrados.</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?php $content = ob_get_clean(); echo view("layouts/app", compact("content","title")); ?>
