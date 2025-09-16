<?php $title = 'Crear Cuadrante'; ob_start(); ?>
<div class="container">
  <div class="card-glass p-4">
    <div class="d-flex align-items-center mb-4">
      <i class="fa-solid fa-draw-polygon fa-lg me-2 text-muted"></i>
      <h4 class="mb-0">Crear Cuadrante</h4>
    </div>

    <form method="post" action="/cuadrantes/store" class="mt-3" id="form-cuadrante" data-loading-submit>
      <?= csrf_field() ?> <!-- Seguridad contra CSRF -->
      
      <div class="row g-3 mb-3">
        <!-- Nombre -->
        <div class="col-md-6">
          <label class="form-label">Nombre</label>
          <div class="input-group">
            <span class="input-group-text"><i class="fa-solid fa-tag"></i></span>
            <input class="form-control" name="nombre" placeholder="Nombre del cuadrante" required>
          </div>
        </div>

        <!-- Localidad -->
        <div class="col-md-6">
          <label class="form-label">Localidad</label>
          <div class="input-group">
            <span class="input-group-text"><i class="fa-solid fa-tag"></i></span>
            <input class="form-control" name="localidad" placeholder="Ej:(Norte–Centro, Sur Oriente, ...)" required>
          </div>
        </div>

        <!-- Precio -->
        <div class="col-md-6">
          <label class="form-label">Precio del cuadrante</label>
          <div class="input-group">
            <span class="input-group-text"><i class="fa-solid fa-dollar-sign"></i></span>
            <input class="form-control" name="precio" placeholder="Ej: 5000" type="number" min="0" step="0.01" required>
          </div>
        </div>

        <!-- Estado -->
        <div class="col-md-6">
          <label class="form-label">Estado</label>
          <div class="input-group">
            <span class="input-group-text"><i class="fa-solid fa-toggle-on"></i></span>
            <select class="form-select" name="estado" required>
              <option value="Activo" selected>Activo</option>
              <option value="Inactivo">Inactivo</option>
            </select>
          </div>
        </div>
      </div>

      <!-- Coordenadas -->
      <div class="mb-3 position-relative">
        <label class="form-label">Coordenadas (formato [[lat,lon],[lat,lon],...])</label>
        <div class="input-group">
          <span class="input-group-text"><i class="fa-solid fa-map-pin"></i></span>
          <textarea class="form-control" id="coordsDisplay" rows="3" name="coords_json" required placeholder="Ej: [[10.97,-74.79],[10.98,-74.78],[10.96,-74.78]]"></textarea>
        </div>
        <small class="text-muted">Puedes editar manualmente o hacer click en el mapa para ir agregando puntos.</small>
        <div id="coordsMsg" class="form-text mt-1"></div>
      </div>

      <!-- Barrios (solo lectura) -->
      <div class="mb-3 position-relative">
        <label class="form-label">Barrios</label>
        <div class="input-group">
          <span class="input-group-text"><i class="fa-solid fa-city"></i></span>
          <textarea class="form-control" id="barriosDisplay" rows="2" readonly placeholder="Ej: Rebolo, 3 postes..."></textarea>
        </div>
        <small class="text-muted">Los barrios se llenarán automáticamente al definir el cuadrante en el mapa.</small>
        <input type="hidden" name="barrios" id="barrios">
      </div>

      <!-- Mapa -->
      <label class="form-label">Mapa</label>
      <div id="map" class="mb-3" style="height:400px;border-radius:12px;"></div>

      <!-- Botones -->
      <div class="d-flex gap-2 mt-3">
        <button class="btn btn-outline-secondary btn-sm" type="submit" data-loading-text="Guardando ⏳"><i class="fa-solid fa-save me-1"></i>Guardar</button>
        <a href="/cuadrantes" class="btn btn-outline-secondary"><i class="fa-solid fa-arrow-left me-1"></i>Cancelar</a>
        <button type="button" id="resetMap" class="btn btn-outline-danger ms-auto"><i class="fa-solid fa-trash me-1"></i>Limpiar</button>
      </div>

    </form>
  </div>
</div>

<?php $scripts = '
<script src="https://cdn.jsdelivr.net/npm/@turf/turf@6.5.0/turf.min.js"></script>
<script>
  // ======= MAPA =======
  const map = L.map("map").setView([10.96854,-74.78132], 12);
  const base = L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png",{maxZoom:19}).addTo(map);

  // ======= UI =======
  const coordsDisplay  = document.getElementById("coordsDisplay");
  const barriosDisplay = document.getElementById("barriosDisplay");
  const barriosHidden  = document.getElementById("barrios");
  const form           = document.getElementById("form-cuadrante");
  const submitBtn      = form.querySelector("[type=submit]");
  const coordsMsg      = document.getElementById("coordsMsg");
  function setMsg(kind, text){ coordsMsg.className="form-text mt-1 " + (kind||""); coordsMsg.textContent=text||""; }
  function setBarrios(v){ barriosDisplay.value=v; barriosHidden.value=v; }

  // ======= CAPAS =======
  let polyNew = null; // el cuadrante que estás dibujando
  const layerOrganizados = L.geoJSON(null, { style:{ color:"#1e90ff", weight:2, fillOpacity:0.15 }}).addTo(map); // existentes ORGANIZADOS (recortados mínimo)
  const layerConfOutline = L.geoJSON(null, { style:{ color:"#d9534f", weight:3, fillOpacity:0 }}).addTo(map);   // bordes en conflicto
  const layerOverlap     = L.geoJSON(null, { style:{ color:"#d9534f", weight:2, fillColor:"#d9534f", fillOpacity:0.35 }}).addTo(map);
  L.control.layers({ "OpenStreetMap": base }, { "Cuadrantes (organizados)": layerOrganizados, "Áreas en conflicto": layerOverlap }).addTo(map);

  // ======= HELPERS GEO =======
  function toRingLngLat(latlngs){
    const ring = latlngs.map(p => [p[1], p[0]]);
    if (ring.length && (ring[0][0]!==ring[ring.length-1][0] || ring[0][1]!==ring[ring.length-1][1])) ring.push(ring[0]);
    return ring;
  }
  function polyFromLatLngs(points){
    if (!Array.isArray(points) || points.length < 3) return null;
    return turf.polygon([toRingLngLat(points)]);
  }
  function safeTruncate(g){ try{ return turf.truncate(g,{precision:6,coordinates:2}); }catch(_){ return g; } }
  function safeUnion(a,b){
    try{ return turf.union(a,b); }catch(_){ return turf.union(safeTruncate(a), safeTruncate(b)); }
  }
  function safeDifference(a,b){
    try{ return turf.difference(a,b); }catch(_){
      try{ return turf.difference(safeTruncate(a), safeTruncate(b)); }catch(__){ return null; }
    }
  }

  // ======= BARRIOS (igual que tenías) =======
  let barriosFC=null;
  fetch("/geojson/BarriosBarranquilla.geojson").then(r=>r.json()).then(j=>barriosFC=j).catch(()=>{});
  function obtenerBarrios(points){
    if(!barriosFC || points.length<3) return "";
    const nuevo = polyFromLatLngs(points); if(!nuevo) return "";
    const out=[];
    (barriosFC.features||[]).forEach(f=>{
      if(!f.geometry) return;
      const g = f.geometry.type==="Polygon" ? turf.polygon(f.geometry.coordinates) : turf.multiPolygon(f.geometry.coordinates);
      if(turf.booleanIntersects(nuevo,g)) out.push(f.properties?.nombre);
    });
    return out.filter(Boolean).join(", ");
  }

  // ======= CARGA EXISTENTES DESDE BD (ORIGINALES) =======
  // Mantendremos dos colecciones:
  // - fcOriginales: EXACTO como en BD (para validación/conflicto)
  // - fcOrganizados: recortado incremental SOLO para visualización
  let fcOriginales = { type:"FeatureCollection", features:[] };
  let fcOrganizados = { type:"FeatureCollection", features:[] };

  fetch("/pedidos/cuadrantes-json")
    .then(r=>r.json())
    .then(rows=>{
      // 1) construir features originales (GeoJSON [lng,lat])
      const feats = [];
      rows
        // TODO: si tienes created_at, ordénalos aquí para que el más antiguo “gane”
        // .sort((a,b)=> (a.created_at < b.created_at ? -1 : 1))
        .sort((a,b)=> (a.id - b.id)) // fallback: id ASC
        .forEach(r=>{
          let pts=[]; try{ pts = JSON.parse(r.coords_json||"[]"); }catch(_){}
          if(!Array.isArray(pts) || pts.length<3) return;
          feats.push({
            type:"Feature",
            properties:{ id:r.id, nombre:r.nombre, localidad:r.localidad||null },
            geometry:{ type:"Polygon", coordinates:[ toRingLngLat(pts) ] }
          });
        });
      fcOriginales.features = feats;

      // 2) construir “organizados” (recortado mínimo para que NO se pisen visualmente)
      let mask=null;
      feats.forEach(f=>{
        const current = f.geometry.type==="Polygon" ? turf.polygon(f.geometry.coordinates, f.properties)
                                                   : turf.multiPolygon(f.geometry.coordinates, f.properties);
        let visible = current;
        if(mask){
          const diff = safeDifference(current, mask);
          if(diff) visible = diff;
          else visible = null; // totalmente cubierto → no se pinta
        }
        if(visible){ fcOrganizados.features.push(visible); }
        mask = mask ? (safeUnion(mask, current) || mask) : current;
      });

      // 3) dibujar SOLO los organizados (como en tu vista “mapa”)
      layerOrganizados.addData(fcOrganizados);
      try{
        const b = layerOrganizados.getBounds();
        if (b && b.isValid()) map.fitBounds(b.pad(0.12));
      }catch(_){}
      if (coordsDisplay.value.trim()) validarActual();
    });

  // ======= VALIDACIÓN (siempre contra ORIGINALES) =======
  function validarContraOriginales(points){
    const res = { dentroDe:null, overlaps:[], interGeoms:[] };
    if(!fcOriginales.features.length) return res;
    const nuevo = polyFromLatLngs(points); if(!nuevo) return res;

    for(const f of fcOriginales.features){
      const ex = f.geometry.type==="Polygon" ? turf.polygon(f.geometry.coordinates) : turf.multiPolygon(f.geometry.coordinates);

      if (turf.booleanWithin(nuevo, ex)) {
        res.dentroDe = f; res.interGeoms.push(nuevo); break; // todo el nuevo está dentro
      }
      try{
        const inter = turf.intersect(nuevo, ex);
        if(inter){
          res.interGeoms.push(inter);
          const area = Math.max(1, Math.round(turf.area(inter)));
          res.overlaps.push({ feat:f, area });
        }
      }catch(_){}
    }
    return res;
  }

  function aplicarResultadoValidacion(v){
    layerConfOutline.clearLayers(); layerOverlap.clearLayers(); submitBtn.disabled=false;

    if (v.dentroDe){
      const p=v.dentroDe.properties||{}, tit=[p.nombre,p.localidad].filter(Boolean).join(" — ");
      setMsg("text-danger", `Ya existe un cuadrante en esa zona: ${tit}.`);
      layerConfOutline.addData(v.dentroDe);
      v.interGeoms.forEach(g=>layerOverlap.addData(g));
      submitBtn.disabled=true; return;
    }
    if (v.overlaps.length){
      try{
        let u=v.interGeoms[0]; for(let i=1;i<v.interGeoms.length;i++){ u = turf.union(u, v.interGeoms[i]) || u; }
        layerOverlap.addData(u);
      }catch(_){ v.interGeoms.forEach(g=>layerOverlap.addData(g)); }
      layerConfOutline.addData(v.overlaps.map(o=>o.feat));
      const top=v.overlaps.slice().sort((a,b)=>b.area-a.area)[0];
      const p=top.feat.properties||{}, tit=[p.nombre,p.localidad].filter(Boolean).join(" — ");
      const m2=top.area.toString().replace(/\B(?=(\d{3})+(?!\d))/g,".");
      setMsg("text-danger", `Se superpone ~${m2} m² con ${tit}. Corrige límites para poder guardar.`);
      submitBtn.disabled=true; return;
    }
    setMsg("text-success","Sin conflictos con cuadrantes existentes.");
  }

  function validarActual(){
    let pts=[]; try{ pts = JSON.parse(coordsDisplay.value || "[]"); }catch(_){}
    if(!Array.isArray(pts) || pts.length<3){
      setMsg("", ""); layerConfOutline.clearLayers(); layerOverlap.clearLayers(); submitBtn.disabled=false; return;
    }
    aplicarResultadoValidacion( validarContraOriginales(pts) );
  }

  // ======= DIBUJO / EDICIÓN =======
  coordsDisplay.addEventListener("input", ()=>{
    try{
      const pts=JSON.parse(coordsDisplay.value);
      if(Array.isArray(pts) && pts.length>=3){
        if(polyNew) map.removeLayer(polyNew);
        polyNew = L.polygon(pts,{color:"#FF6B00", fillOpacity:0.2}).addTo(map);
        map.fitBounds(polyNew.getBounds());
        setBarrios( obtenerBarrios(pts) );
        validarActual();
      }
    }catch(_){
      setMsg("text-danger","Formato inválido. Usa [[lat,lon],[lat,lon],...]");
      layerConfOutline.clearLayers(); layerOverlap.clearLayers(); submitBtn.disabled=true;
    }
  });

  map.on("click", (e)=>{
    const latlng=[e.latlng.lat, e.latlng.lng];
    let pts=[]; if(coordsDisplay.value){ try{ pts=JSON.parse(coordsDisplay.value);}catch(_){ pts=[]; } }
    pts.push(latlng);
    if(polyNew) map.removeLayer(polyNew);
    polyNew = L.polygon(pts,{color:"#FF6B00", fillOpacity:0.2}).addTo(map);
    coordsDisplay.value = JSON.stringify(pts);
    setBarrios( obtenerBarrios(pts) );
    validarActual();
  });

  document.getElementById("resetMap").addEventListener("click", ()=>{
    coordsDisplay.value=""; barriosDisplay.value=""; setMsg("");
    if(polyNew) map.removeLayer(polyNew);
    layerConfOutline.clearLayers(); layerOverlap.clearLayers(); submitBtn.disabled=false;
  });

  // Seguridad en submit
  form.addEventListener("submit", (ev)=>{
    let pts=[]; try{ pts=JSON.parse(coordsDisplay.value||"[]"); }catch(_){}
    const v = validarContraOriginales(pts);
    if(v.dentroDe || v.overlaps.length){ ev.preventDefault(); aplicarResultadoValidacion(v); }
  });
</script>
'; ?>





<?php $content = ob_get_clean(); echo view("layouts/app", compact("content","title","scripts")); ?>







