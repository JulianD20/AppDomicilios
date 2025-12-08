<?php $title = 'Asignar Pedido';
ob_start(); ?>
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
    <form id="form-pedido" method="post" action="/pedidos/store" data-loading-submit>
      <?= csrf_field() ?> <!-- Seguridad contra CSRF -->

      <div class="row">
        <div class="col-md-6 mb-3">
          <label class="form-label">Domiciliario</label>
          <select name="domiciliario_id" class="form-select" id="domiciliarioSelect" required>
            <option value="">-- Selecciona --</option>
            <?php foreach ($domiciliarios as $d): ?>
              <option value="<?= (int)$d['id'] ?>"><?= esc($d['nombre']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="col-md-6 mb-3">
          <label class="form-label">Cuadrante</label>
          <select name="cuadrante_id" class="form-select" id="cuadranteSelect" required>
            <option value="">-- Selecciona --</option>
            <?php // Fallback inicial por si falla el fetch (se sobrescribe en JS si carga bien)
            foreach ($cuadrantes as $c): ?>
              <option
                value="<?= (int)$c['id'] ?>"
                data-precio="<?= number_format((float)$c['precio'], 2, '.', '') ?>">
                <?= esc($c['nombre']) ?><?= $c['localidad'] ? ' — ' . esc($c['localidad']) : '' ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>

      <!-- Campo Dirección -->
      <div class="col-md-12 mb-3">
        <label class="form-label">Dirección (opcional)</label>
        <input type="text" id="direccionInput" name="direccion" class="form-control" placeholder="Ej: Carrera 50 45-23">
        <div id="direccionMsg" class="form-text"></div>
      </div>

      <div class="mb-3">
        <label class="form-label">Monto (precio del cuadrante)</label>
        <div class="form-control-plaintext fw-semibold" id="montoView">$0.00</div>
        <input type="hidden" name="monto" id="montoHidden" value="0.00"><!-- opcional -->
      </div>

      <div class="d-flex gap-2 justify-content-end">
        <button id="previewBtn" type="button" class="btn btn-outline-primary">
          <i class="bi bi-eye"></i> Previsualizar
        </button>
        <button class="btn btn-outline-secondary btn-sm" type="submit" data-loading-text="Guardando ⏳">
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

<?php
$scripts = <<<'HTML'
<script src="https://cdn.jsdelivr.net/npm/@turf/turf@6.5.0/turf.min.js"></script>
<script>
(async function(){
  const sel       = document.getElementById('cuadranteSelect');
  const montoView = document.getElementById('montoView');
  const montoHidden = document.getElementById('montoHidden');
  const dirInput  = document.getElementById('direccionInput');
  const msg       = document.getElementById('direccionMsg');

  // ===============================
  // Helpers
  // ===============================
  function setMsg(kind, text){
    msg.classList.remove('text-success','text-warning','text-danger');
    if (kind) msg.classList.add(kind);
    msg.textContent = text || '';
  }
  function updateMonto(){
    const opt = sel.options[sel.selectedIndex];
    const precio = opt?.dataset?.precio ?? '0';
    const val = parseFloat(precio || '0').toFixed(2);
    montoView.textContent = '$' + val;
    if (montoHidden) montoHidden.value = val;
  }
  function normalizeAddress(address) {
    return address
      .replace(/\s*#\s*/g, " ")
      .replace(/\s+No\.?\s*/gi, " ")
      .replace(/\s+/g, " ")
      .trim();
  }
  function optionFromProps(p){
    const opt = document.createElement('option');
    opt.value = p.id; // id interno (cuadrante_id)
    opt.dataset.precio = p.precio ?? 0;
    opt.textContent = p.nombre + (p.localidad ? (' — ' + p.localidad) : '');
    return opt;
  }

  // ===============================
  // Geocodificación con Nominatim
  // ===============================
  async function geocodeAddress(address) {
    const params = new URLSearchParams({
      format: 'json',
      limit: '1',
      q: `${address}, Barranquilla, Colombia`
    });
    const url = `https://nominatim.openstreetmap.org/search?${params.toString()}`;
    try {
      const res = await fetch(url, { headers: { 'Accept': 'application/json', 'User-Agent': 'AppDomicilios/1.0 (contacto@tuapp.com)' } });
      const data = await res.json();
      if (!data || data.length === 0) return null;
      return [parseFloat(data[0].lon), parseFloat(data[0].lat)]; // [lon, lat]
    } catch (err) {
      console.error('Error geocoding', err);
      return null;
    }
  }

  // ===============================
  // Cargar GeoJSON unificado (DB + archivo)
  // ===============================
  let fcCuadrantes = { type: 'FeatureCollection', features: [] };
  try {
    const res = await fetch('/pedidos/cuadrantes-geojson');
    fcCuadrantes = await res.json();
  } catch(e) {
    console.error("Error cargando cuadrantes unificados", e);
  }

  // Popular el <select> SOLO con el GeoJSON unificado (si hay features)
  if (Array.isArray(fcCuadrantes.features) && fcCuadrantes.features.length) {
    sel.innerHTML = '<option value="">-- Selecciona --</option>';
    fcCuadrantes.features.forEach(f => {
      const p = f.properties || {};
      sel.appendChild(optionFromProps(p));
    });
  }
  // listeners de monto
  sel.addEventListener('change', updateMonto);
  updateMonto();

  // ===============================
  // Listener en dirección (contención exacta, sin buffer)
  // ===============================
  dirInput.addEventListener('blur', async () => {
    const dirRaw = dirInput.value.trim();
    if (!dirRaw) return;

    setMsg(null, "Buscando coordenadas...");
    const dir = normalizeAddress(dirRaw);
    const coords = await geocodeAddress(dir);
    if (!coords) {
      setMsg('text-danger', "No se pudo encontrar la dirección");
      return;
    }

    const point = turf.point(coords); // [lon, lat]

    // Contención exacta
    const encontrados = fcCuadrantes.features.filter(f => turf.booleanPointInPolygon(point, f));

    if (encontrados.length) {
      const detectado = encontrados[0];
      const p = detectado.properties || {};
      sel.value = p.id;
      sel.dispatchEvent(new Event('change'));
      setMsg('text-success', `Pertenece al cuadrante: ${p.nombre}${p.localidad ? ' — ' + p.localidad : ''} (puedes cambiarlo manualmente)`);
      return;
    }

    // (Opcional) aviso de cercanía: <= 20 m al borde más cercano
    let mejor = null, minDist = Infinity;
    for (const f of fcCuadrantes.features) {
      const line = turf.polygonToLine(f); // sirve para Polygon y MultiPolygon
      const d = turf.pointToLineDistance(point, line, { units: 'meters' });
      if (d < minDist) { minDist = d; mejor = f; }
    }

    if (minDist <= 20 && mejor) {
      const p = (mejor.properties || {});
      setMsg('text-warning', `La dirección está a ~${minDist.toFixed(0)} m del borde de ${p.nombre}. Revísalo.`);
    } else {
      setMsg('text-danger', "La dirección no pertenece a ningún cuadrante detectado. Selección manual.");
    }
  });

  // ===============================
  // Previsualizar factura
  // ===============================
  document.getElementById('previewBtn')?.addEventListener('click', ()=>{
    const domSel = document.getElementById('domiciliarioSelect');
    const domText = domSel.options[domSel.selectedIndex]?.text || '-';
    const cuaText = sel.options[sel.selectedIndex]?.text || '-';
    document.getElementById('f-domiciliario').innerText = domText;
    document.getElementById('f-cuadrante').innerText   = cuaText;
    document.getElementById('f-monto').innerText        = montoView.textContent;

    new bootstrap.Modal(document.getElementById('facturaModal')).show();
  });

})();
</script>
HTML;
?>

<?php $content = ob_get_clean();
echo view("layouts/app", compact("content", "title", "scripts")); ?>
