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
    <form id="form-pedido" method="post" action="/pedidos/store">
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
            <?php foreach ($cuadrantes as $c): ?>
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
        <input type="text" id="direccionInput" name="direccion" class="form-control" placeholder="Ej: Carrera 16 #45-23">
        <div id="direccionMsg" class="form-text text-danger"></div>
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

<?php
$scripts = <<<'HTML'
<script src="https://cdn.jsdelivr.net/npm/@turf/turf@6.5.0/turf.min.js"></script>
<script>
(async function(){
  const sel = document.getElementById('cuadranteSelect');
  const montoView = document.getElementById('montoView');
  const montoHidden = document.getElementById('montoHidden');

  // ===============================
  // Actualizar monto
  // ===============================
  function updateMonto(){
    const opt = sel.options[sel.selectedIndex];
    const precio = opt?.dataset?.precio ?? '0';
    const val = parseFloat(precio || '0').toFixed(2);
    montoView.textContent = '$' + val;
    if (montoHidden) montoHidden.value = val; 
  }
  sel.addEventListener('change', updateMonto);
  updateMonto(); 

  // ===============================
  // Geocodificación con Nominatim
  // ===============================
  async function geocodeAddress(address) {
    const url = `https://nominatim.openstreetmap.org/search?format=json&limit=1&q=${encodeURIComponent(address)}, Barranquilla, Colombia`;
    try {
      const res = await fetch(url);
      const data = await res.json();
      if (!data || data.length === 0) return null;
      return [parseFloat(data[0].lon), parseFloat(data[0].lat)]; // [lon, lat]
    } catch (err) {
      console.error('Error geocoding', err);
      return null;
    }
  }

  // ===============================
  // Cargar cuadrantes del sistema
  // ===============================
  let cuadrantesSistema = [];
  try {
    const res = await fetch('/pedidos/cuadrantes-json'); 
    cuadrantesSistema = await res.json(); // [{id, nombre, coords_json}, ...]
  } catch(e) {
    console.error("Error cargando cuadrantes", e);
  }

// ===============================
// Normalizar dirección
// ===============================
function normalizeAddress(address) {
  return address
    .replace(/\s*#\s*/g, " ")      // reemplaza "#" por espacio
    .replace(/\s+No\.?\s*/gi, " ") // reemplaza "No." por espacio
    .replace(/\s+/g, " ")          // espacios múltiples → uno
    .trim();
}

// ===============================
// Listener en dirección
// ===============================
document.getElementById('direccionInput').addEventListener('blur', async () => {
  const dirRaw = document.getElementById('direccionInput').value.trim();
  const msg = document.getElementById('direccionMsg');
  if (!dirRaw) return;

  msg.textContent = "Buscando coordenadas...";

  // ✅ Normalizamos antes de buscar
  const dir = normalizeAddress(dirRaw);

  const coords = await geocodeAddress(dir);
  if (!coords) {
    msg.textContent = "No se pudo encontrar la dirección";
    return;
  }

  const point = turf.point(coords); // coords = [lon, lat]
  let cuadrantesEncontrados = [];

  for (const c of cuadrantesSistema) {
    try {
      const coordsArray = JSON.parse(c.coords_json); 
      let polyCoords = coordsArray.map(p => [p[1], p[0]]); 

      if (polyCoords[0][0] !== polyCoords.at(-1)[0] || polyCoords[0][1] !== polyCoords.at(-1)[1]) {
        polyCoords.push(polyCoords[0]);
      }

      const polygon = turf.polygon([polyCoords]);

      // ✅ Buffer de 100m
      const buffered = turf.buffer(polygon, 0.1, { units: 'kilometers' });

      if (turf.booleanPointInPolygon(point, buffered)) {
        cuadrantesEncontrados.push(c);
      }
    } catch(e) { console.warn("Error procesando cuadrante", c, e); }
  }

  // ===============================
  // Mostrar SIEMPRE todos los cuadrantes
  // ===============================
  sel.innerHTML = '<option value="">-- Selecciona cuadrante --</option>';
  cuadrantesSistema.forEach(c => {
    sel.innerHTML += `<option value="${c.id}" data-precio="${c.precio ?? 0}">
                        ${c.nombre}
                      </option>`;
  });

  if (cuadrantesEncontrados.length >= 1) {
    // ✅ Seleccionar el primero detectado
    const detectado = cuadrantesEncontrados[0];
    sel.value = detectado.id;
    sel.dispatchEvent(new Event('change'));

    msg.classList.remove('text-danger','text-warning');
    msg.classList.add('text-success');
    msg.textContent = `Pertenece al cuadrante: ${detectado.nombre} (puedes cambiarlo manualmente)`;
  } 
  else {
    // ❌ Ninguno encontrado → aviso rojo
    msg.classList.remove('text-success');
    msg.classList.add('text-danger');
    msg.textContent = "La dirección no pertenece a ningún cuadrante detectado, selecciona uno manualmente";
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