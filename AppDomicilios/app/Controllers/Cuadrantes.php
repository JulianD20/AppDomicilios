<?php
namespace App\Controllers;

use App\Models\CuadranteModel;

class Cuadrantes extends BaseController
{
    public function index()
    {
        $cuadrantes = (new CuadranteModel())->orderBy('id','DESC')->findAll();
        return view('cuadrantes/index', ['cuadrantes' => $cuadrantes]);
    }

    // GET /cuadrantes/create  -> muestra el formulario
    public function create()
    {
        return view('cuadrantes/create'); // asegúrate que existe app/Views/cuadrantes/create.php
    }

    // POST /cuadrantes/store  -> guarda en la BD
    public function store()
    {
        $post = $this->request->getPost();

        // mapear nombres del form -> columnas reales
        $nombre   = trim($post['nombre'] ?? '');
        $precio   = (float)($post['precio'] ?? 0);          // en BD: precio_base
        $coords   = $post['coordenadas'] ?? '[]';           // en BD: coords_json
        $localidad= $post['localidad'] ?? null;
        $barrios  = $post['barrios'] ?? null;               // opcional (puede venir del mapa)

        // calcular lat/lon de referencia (centroide simple por promedio)
        $lat = null; $lon = null;
        $pts = json_decode($coords, true);
        if (is_array($pts) && count($pts) > 0) {
            $sumLat = 0; $sumLon = 0;
            foreach ($pts as $p) { $sumLat += (float)$p[0]; $sumLon += (float)$p[1]; }
            $lat = $sumLat / count($pts);
            $lon = $sumLon / count($pts);
        }

        // validar mínimo (lado servidor)
        if ($nombre === '' || !is_array($pts) || count($pts) < 3) {
            return redirect()->back()->with('error', 'Nombre y polígono son obligatorios');
        }

        $data = [
            'nombre'      => $nombre,
            'lat'         => $lat ?? 0,
            'lon'         => $lon ?? 0,
            'localidad'   => $localidad,
            'barrios'     => $barrios,
            'precio_base' => $precio,
            'coords_json' => is_string($coords) ? $coords : json_encode($coords, JSON_UNESCAPED_UNICODE),
            'activo'      => 1,
        ];

        try {
            $model = new CuadranteModel();
            if (!$model->insert($data)) {
                return redirect()->back()->with('error', implode('; ', $model->errors()))->withInput();
            }
            return redirect()->to('/cuadrantes')->with('success', 'Cuadrante creado');
        } catch (\Throwable $e) {
            // Los triggers de la BD (JSON inválido, precio < 0, etc.) caerán aquí
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }
}
