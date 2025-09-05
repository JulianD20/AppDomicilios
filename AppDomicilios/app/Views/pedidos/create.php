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

    <!-- Formulario -->
    <form id="form-pedido" method="post" action="/pedidos/store">
      <div class="row">
        <div class="col-md-6 mb-3">
          <label class="form-label">Domiciliario</label>
          <select name="domiciliario_id" class="form-select" id="domiciliarioSelect" required>
            <option value="">-- Selecciona --</option>
          </select>
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label">Cuadrante</label>
          <select name="cuadrante_id" class="form-select" id="cuadranteSelect" required>
            <option value="">-- Selecciona --</option>
          </select>
        </div>
      </div>

      <div class="mb-3">
        <label class="form-label">Monto a pagar</label>
        <input name="monto" class="form-control" type="number" step="0.01" min="0" required>
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
  document.getElementById("previewBtn").addEventListener("click", ()=>{
    const domSel = document.getElementById("domiciliarioSelect");
    const cuaSel = document.getElementById("cuadranteSelect");
    const monto = document.querySelector("input[name=\'monto\']").value || "0";

    const domText = domSel.options[domSel.selectedIndex]?.text || "-";
    const cuaText = cuaSel.options[cuaSel.selectedIndex]?.text || "-";

    document.getElementById("f-domiciliario").innerText = domText;
    document.getElementById("f-cuadrante").innerText = cuaText;
    document.getElementById("f-monto").innerText = "$" + parseFloat(monto).toFixed(2);

    const modal = new bootstrap.Modal(document.getElementById("facturaModal"));
    modal.show();
  });
</script>
'; ?>

<?php $content = ob_get_clean(); echo view("layouts/app", compact("content","title","scripts")); ?>


