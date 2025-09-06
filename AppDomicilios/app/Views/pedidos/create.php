<?php $title = 'Asignar Pedido'; ob_start(); ?>
<div class="container">
  <div class="card-glass p-4">

    <!-- Encabezado  -->
    <div class="d-flex align-items-center mb-4">
      <a href="/pedidos" class="btn btn-outline-secondary btn-sm me-2 rounded-circle">
        <i class="bi bi-arrow-left"></i>
      </a>
      <h4 class="mb-0">Asignar Pedido</h4>
    </div>

    <!-- Flash mensajes -->
    <?php if (session()->getFlashdata('error')): ?>
      <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('success')): ?>
      <div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
    <?php endif; ?>

    <!-- Formulario -->
    <form id="form-pedido" method="post" action="/pedidos/store" autocomplete="off">
      <?= csrf_field() ?>
      <input type="hidden" name="estado" value="asignado"><!-- opcional: el controller ya lo pone -->

      <div class="row">
        <!-- Domiciliario -->
        <div class="col-md-6 mb-3">
          <label class="form-label">Domiciliario</label>
          <select name="domiciliario_id" class="form-select" id="domiciliarioSelect" required>
            <option value="">-- Selecciona --</option>
            <?php foreach (($domiciliarios ?? []) as $d): ?>
              <option value="<?= (int)$d['id'] ?>"
                <?= (string)old('domiciliario_id') === (string)$d['id'] ? 'selected' : '' ?>>
                <?= esc($d['nombre']) ?><?= isset($d['activo']) && !$d['activo'] ? ' (inactivo)' : '' ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <!-- Cuadrante -->
        <div class="col-md-6 mb-3">
          <label class="form-label">Cuadrante</label>
          <select name="cuadrante_id" class="form-select" id="cuadranteSelect" required>
            <option value="">-- Selecciona --</option>
            <?php foreach (($cuadrantes ?? []) as $c): ?>
              <option value="<?= (int)$c['id'] ?>"
                      data-precio="<?= (float)$c['precio_base'] ?>"
                <?= (string)old('cuadrante_id') === (string)$c['id'] ? 'selected' : '' ?>>
                <?= esc($c['nombre']) ?> (<?= number_format((float)$c['precio_base'], 0, ',', '.') ?>)
              </option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>

      <!-- Dirección -->
      <div class="mb-3">
        <label class="form-label">Dirección</label>
        <input name="direccion" class="form-control" placeholder="Ej: Cra 51B # 90-10"
               value="<?= esc(old('direccion') ?? '') ?>">
      </div>

      <!-- Monto -->
      <div class="mb-3">
        <label class="form-label">Monto a pagar</label>
        <input name="monto" id="montoInput" class="form-control" type="number" step="0.01" min="0"
               value="<?= esc(old('monto') ?? '') ?>" required>
        <div class="form-text">Se autollenará con el precio del cuadrante seleccionado. Puedes modificarlo.</div>
      </div>

      <!-- Notas -->
      <div class="mb-3">
        <label class="form-label">Notas</label>
        <textarea name="notas" class="form-control" rows="2" placeholder="Observaciones opcionales"><?= esc(old('notas') ?? '') ?></textarea>
      </div>

      <div class="d-flex gap-2 justify-content-end">
        <button id="previewBtn" type="button" class="btn btn-outline-primary">
          <i class="bi bi-eye"></i> Previsualizar
        </button>
        <button class="btn btn-brand" type="submit">
          <i class="bi bi-check2-circle"></i> Asignar Pedido
        </button>
      </div>
    </form>
  </div>

  <!-- Modal Previsualizar Factura -->
  <div class="modal fade" id="facturaModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-body">
          <div class="invoice p-0">
            <div class="p-4">
              <div class="d-flex justify-content-between">
                <div>
                  <h5>Factura - Asignación</h5>
                  <div class="muted small">Pedido generado</div>
                </div>
                <div class="text-end">
                  <h5 class="text-muted">AppDomicilios</h5>
                </div>
              </div>

              <hr>
              <div class="row">
                <div class="col-6">
                  <strong>Domiciliario</strong>
                  <div id="f-domiciliario" class="muted small">-</div>
                </div>
                <div class="col-6 text-end">
                  <strong>Cuadrante</strong>
                  <div id="f-cuadrante" class="muted small">-</div>
                </div>
              </div>

              <div class="mt-4 d-flex justify-content-between align-items-center">
                <div class="muted">Método de pago: A convenir</div>
                <div class="fs-5"><strong id="f-monto">$0</strong></div>
              </div>

            </div>
            <div class="p-3 text-end">
              <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cerrar</button>
              <button onclick="window.print()" class="btn btn-brand">Imprimir</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

</div>

<?php $scripts = '
<script>
  // Autollenar monto con el precio_base del cuadrante
  const selC = document.getElementById("cuadranteSelect");
  const montoInput = document.getElementById("montoInput");
  function setMontoFromCuadrante() {
    const opt = selC.options[selC.selectedIndex];
    const precio = parseFloat(opt?.dataset?.precio || "0");
    if (!isNaN(precio) && precio > 0 && (!montoInput.value || parseFloat(montoInput.value) <= 0)) {
      montoInput.value = precio.toFixed(2);
    }
  }
  selC?.addEventListener("change", setMontoFromCuadrante);
  // si hay seleccionado al cargar y no hay monto, autollenar
  window.addEventListener("DOMContentLoaded", setMontoFromCuadrante);

  // Previsualizar
  document.getElementById("previewBtn").addEventListener("click", ()=>{
    const domSel = document.getElementById("domiciliarioSelect");
    const cuaSel = document.getElementById("cuadranteSelect");
    const monto = montoInput.value || "0";

    const domText = domSel.options[domSel.selectedIndex]?.text || "-";
    const cuaText = cuaSel.options[cuaSel.selectedIndex]?.text || "-";

    document.getElementById("f-domiciliario").innerText = domText;
    document.getElementById("f-cuadrante").innerText = cuaText;
    document.getElementById("f-monto").innerText = "$" + Number(monto).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});

    const modal = new bootstrap.Modal(document.getElementById("facturaModal"));
    modal.show();
  });
</script>
'; ?>

<?php $content = ob_get_clean(); echo view("layouts/app", compact("content","title","scripts")); ?>
