<?php $title = 'Factor de Pago';
ob_start(); ?>

<div class="container">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold">🧮 Factor de Pago</h4>
    <div class="d-flex gap-2">
      <a href="/pedidos" class="btn btn-outline-secondary">Ver Pedidos</a>
      <button class="btn btn-brand" data-bs-toggle="modal" data-bs-target="#facturaDiaModal">
        🧾 Generar factura por día
      </button>
    </div>
  </div>

  <?php if (session('success')): ?>
    <div class="alert alert-success"><?= esc(session('success')) ?></div>
  <?php endif; ?>
  <?php if (session('error')): ?>
    <div class="alert alert-danger"><?= esc(session('error')) ?></div>
  <?php endif; ?>

  <div class="card-glass p-4 mb-4">
    <h5 class="mb-3">⚙️ Regla escalonada</h5>

    <form method="post" action="/factor-pago/config" id="form-reglas" data-loading-submit>
      <?= csrf_field() ?>
      <input type="hidden" name="reglas_json" id="reglas_json">

      <div class="table-responsive">
        <table class="table table-sm align-middle">
          <thead class="table-light">
            <tr>
              <th style="width: 120px">Desde #</th>
              <th style="width: 120px">Hasta #</th>
              <th style="width: 160px">Porcentaje</th>
              <th></th>
            </tr>
          </thead>
          <tbody id="rulesBody"></tbody>
        </table>
      </div>

      <div class="d-flex gap-2">
        <button type="button" id="addRow" class="btn btn-outline-secondary btn-sm">
          <i class="fa-solid fa-plus"></i> Agregar tramo
        </button>
        <div class="ms-auto text-muted small">
          Actual: <strong><?= esc($rulesText ?? '') ?></strong>
        </div>
      </div>

      <div class="d-flex justify-content-end mt-3">
        <button class="btn btn-outline-primary btn-sm" type="submit" data-loading-text="Guardando ⏳">
          <i class="fa-solid fa-floppy-disk me-1"></i> Guardar reglas
        </button>
      </div>
    </form>
  </div>

  <script>
    (function() {
      const initial = <?= json_encode($rules ?? [], JSON_UNESCAPED_UNICODE) ?>;
      const body = document.getElementById('rulesBody');
      const hidden = document.getElementById('reglas_json');

      function rowTpl(r = {}, idx = 0) {
        const from = r.from ?? '';
        const to = (r.to === null || r.to === undefined) ? '' : r.to;
        const pct = r.percent ?? 100;
        return `
      <tr>
        <td><input type="number" class="form-control form-control-sm from" min="1" step="1" value="${from}"></td>
        <td><input type="number" class="form-control form-control-sm to" min="1" step="1" value="${to}" placeholder="∞"></td>
        <td>
          <div class="input-group input-group-sm">
            <input type="number" class="form-control percent" min="0" max="100" step="0.01" value="${pct}">
            <span class="input-group-text">%</span>
          </div>
        </td>
        <td class="text-end">
          <button type="button" class="btn btn-outline-danger btn-sm del">Eliminar</button>
        </td>
      </tr>`;
      }

      function render(rows) {
        body.innerHTML = rows.map(rowTpl).join('');
      }

      function read() {
        const rows = [];
        body.querySelectorAll('tr').forEach(tr => {
          const from = parseInt(tr.querySelector('.from').value || '0', 10);
          const toRaw = tr.querySelector('.to').value;
          const to = toRaw === '' ? null : parseInt(toRaw, 10);
          const pct = parseFloat(tr.querySelector('.percent').value || '0');
          if (from >= 1 && pct >= 0 && pct <= 100) {
            if (to !== null && to < from) return; // salta inválidos
            rows.push({
              from,
              to,
              percent: pct
            });
          }
        });
        // ordenar por 'from'
        rows.sort((a, b) => a.from - b.from);
        return rows;
      }

      function addRow(v = {
        from: '',
        to: '',
        percent: 100
      }) {
        body.insertAdjacentHTML('beforeend', rowTpl(v));
      }

      document.getElementById('addRow').addEventListener('click', () => addRow());

      body.addEventListener('click', (e) => {
        if (e.target.classList.contains('del')) {
          e.target.closest('tr').remove();
        }
      });

      document.getElementById('form-reglas').addEventListener('submit', (e) => {
        const rows = read();
        if (rows.length === 0) {
          e.preventDefault();
          alert('Debes definir al menos un tramo.');
          return;
        }
        // Nota: aceptamos solapes; en el cálculo se usa la PRIMERA regla que coincida.
        hidden.value = JSON.stringify(rows);
      });

      // Inicial
      if (initial && initial.length) render(initial);
      else render([{
          from: 1,
          to: 1,
          percent: 100
        },
        {
          from: 2,
          to: null,
          percent: 50
        },
      ]);
    })();
  </script>


  <div class="card-glass p-4">
    <p class="mb-0 text-muted">
      Usa el botón <b>“Generar factura por día”</b> para calcular el pago del domiciliario
      siguiendo la regla: <em>primer pedido 100%, siguientes al 50% (Por defecto).</em>
    </p>
  </div>
</div>

<!-- Modal Factura por día (EXCLUSIVO de este módulo) -->
<div class="modal fade" id="facturaDiaModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content card-glass">
      <div class="modal-header">
        <h5 class="modal-title">Generar factura por día</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>

      <form method="get" action="/factor-pago/factura-dia" data-loading-submit>
        <div class="modal-body">

          <?php if (session('fd_error')): ?>
            <div class="alert alert-warning">
              <?= esc(session('fd_error')) ?>
            </div>
          <?php endif; ?>

          <div class="mb-3">
            <label class="form-label">Domiciliario</label>
            <?php $selDom = (int)(session('fd_domiciliario_id') ?? 0); ?>
            <select name="domiciliario_id" class="form-select" required>
              <option value="">-- Selecciona --</option>
              <?php if (!empty($domiciliarios)): ?>
                <?php foreach ($domiciliarios as $d): ?>
                  <option value="<?= (int)$d['id'] ?>" <?= $selDom === (int)$d['id'] ? 'selected' : '' ?>>
                    <?= esc($d['nombre']) ?>
                  </option>
                <?php endforeach; ?>
              <?php endif; ?>
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label">Fecha</label>
            <input
              type="date"
              name="fecha"
              class="form-control"
              value="<?= esc(session('fd_fecha') ?? date('Y-m-d')) ?>"
              required>
          </div>
        </div>

        <div class="modal-footer">
          <?= csrf_field() ?>
          <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cerrar</button>
          <button type="submit" class="btn btn-brand btn-sm" data-loading-text="Generando ⏳">Generar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php $content = ob_get_clean();
echo view('layouts/app', compact('content', 'title')); ?>

<?php if (!empty($showFacturaDiaModal)): ?>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const el = document.getElementById('facturaDiaModal');
      const modal = bootstrap.Modal.getOrCreateInstance(el);
      modal.show();
    });
  </script>
<?php endif; ?>

<style>
  .card-glass {
    background: rgba(255, 255, 255, 0.85);
    border-radius: 12px;
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
  }

  .btn-brand {
    background: #FF6B00;
    color: #fff;
    border-radius: 8px;
    transition: all .2s ease-in-out;
  }

  .btn-brand:hover {
    background: #e65f00;
    color: #fff;
  }
</style>