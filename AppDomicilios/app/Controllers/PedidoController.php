<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\PedidoModel;
use App\Models\DomiciliarioModel;
use App\Models\CuadranteModel;

class PedidoController extends BaseController
{
    public function index()
    {
        $req = $this->request;

        $q         = trim((string) $req->getGet('q'));
        $estado    = (string) $req->getGet('estado');        // '' | 'pagado' | 'pendiente'
        $domId     = (int) ($req->getGet('domiciliario_id') ?? 0);
        $cuaId     = (int) ($req->getGet('cuadrante_id') ?? 0);
        $desde     = (string) $req->getGet('desde');         // YYYY-MM-DD
        $hasta     = (string) $req->getGet('hasta');         // YYYY-MM-DD
        $mmin      = $req->getGet('mmin');                   // monto min
        $mmax      = $req->getGet('mmax');                   // monto max
        $perPage   = (int) ($req->getGet('per_page') ?? 10);

        $pedidoModel = new \App\Models\PedidoModel();

        // builder con joins
        $builder = $pedidoModel->select('
                pedidos.id, pedidos.direccion, pedidos.monto, pedidos.created_at, pedidos.pagado, pedidos.pagado_at,
                d.nombre AS domiciliario, c.nombre AS cuadrante,
                pedidos.domiciliario_id, pedidos.cuadrante_id
            ')
            ->join('domiciliarios d', 'd.id = pedidos.domiciliario_id', 'left')
            ->join('cuadrantes c', 'c.id = pedidos.cuadrante_id', 'left');

        if ($q !== '') {
            $builder->groupStart()
                ->like('d.nombre', $q)
                ->orLike('c.nombre', $q)
                ->orLike('pedidos.direccion', $q)
                ->groupEnd();
        }
        if ($estado === 'pagado')    $builder->where('pedidos.pagado', 1);
        if ($estado === 'pendiente') $builder->where('pedidos.pagado', 0);

        if ($domId > 0) $builder->where('pedidos.domiciliario_id', $domId);
        if ($cuaId > 0) $builder->where('pedidos.cuadrante_id', $cuaId);

        if ($desde !== '') $builder->where('DATE(pedidos.created_at) >=', $desde);
        if ($hasta !== '') $builder->where('DATE(pedidos.created_at) <=', $hasta);

        if ($mmin !== null && $mmin !== '' && is_numeric($mmin)) $builder->where('pedidos.monto >=', (float)$mmin);
        if ($mmax !== null && $mmax !== '' && is_numeric($mmax)) $builder->where('pedidos.monto <=', (float)$mmax);

        $builder->orderBy('pedidos.id', 'DESC');

        // usar paginate con builder manual
        $data['pedidos'] = $builder->paginate($perPage);
        $data['pager']   = $pedidoModel->pager;

        // selects
        $domModel = new \App\Models\DomiciliarioModel();
        $cuaModel = new \App\Models\CuadranteModel();
        $data['domiciliarios'] = $domModel->where('estado','Activo')->orderBy('nombre','ASC')->findAll();
        $data['cuadrantes']    = $cuaModel->where('estado','Activo')->orderBy('nombre','ASC')->findAll();

        $data['filters'] = compact('q','estado','domId','cuaId','desde','hasta','mmin','mmax','perPage');

        return view('pedidos/index', $data);
    }


    public function create()
    {
        // Poblar selects con activos
        $domModel = new DomiciliarioModel();
        $cuaModel = new CuadranteModel();

        $data['domiciliarios'] = $domModel->where('estado', 'Activo')->orderBy('nombre', 'ASC')->findAll();
        $data['cuadrantes']    = $cuaModel->where('estado', 'Activo')->orderBy('nombre', 'ASC')->findAll();

        return view('pedidos/create', $data);
    }

    public function store()
    {
        helper('feedback');
        $rules = [
            'domiciliario_id' => 'required|is_natural_no_zero|is_not_unique[domiciliarios.id]',
            'cuadrante_id'    => 'required|is_natural_no_zero|is_not_unique[cuadrantes.id]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Validar que ambos estén Activos
        $domModel = new DomiciliarioModel();
        $cuaModel = new CuadranteModel();

        $dom = $domModel->find($this->request->getPost('domiciliario_id'));
        $cua = $cuaModel->find($this->request->getPost('cuadrante_id'));

        if (! $dom || ($dom['estado'] ?? '') !== 'Activo') {
            return redirect()->back()->withInput()->with('error', 'El domiciliario no existe o no está activo.');
        }
        if (! $cua || ($cua['estado'] ?? '') !== 'Activo') {
            return redirect()->back()->withInput()->with('error', 'El cuadrante no existe o no está activo.');
        }

        $monto = (float) ($cua['precio'] ?? 0);

        $pedidoModel = new PedidoModel();
        $id = $pedidoModel->insert([
            'domiciliario_id' => (int) $this->request->getPost('domiciliario_id'),
            'cuadrante_id'    => (int) $this->request->getPost('cuadrante_id'),
            'direccion'       => $this->request->getPost('direccion'),
            'monto'           => $monto,
        ], true); // true = return insert ID
        flash_guardado('El pedido se guardó correctamente.', null, 'toast');
        return redirect()->to("/pedidos/factura/{$id}")
            ->with('success', 'Pedido asignado correctamente.');
    }

    public function edit(int $id)
    {
        $pedidoModel = new PedidoModel();
        $domModel    = new DomiciliarioModel();
        $cuaModel    = new CuadranteModel();

        $pedido = $pedidoModel->find($id);
        if (!$pedido) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Pedido no encontrado');
        }

        $data['pedido']        = $pedido;
        $data['domiciliarios'] = $domModel->where('estado', 'Activo')->orderBy('nombre', 'ASC')->findAll();
        $data['cuadrantes']    = $cuaModel->where('estado', 'Activo')->orderBy('nombre', 'ASC')->findAll();

        return view('pedidos/edit', $data);
    }

    public function update(int $id)
    {
        helper('feedback');
        $rules = [
            'domiciliario_id' => 'required|is_natural_no_zero|is_not_unique[domiciliarios.id]',
            'cuadrante_id'    => 'required|is_natural_no_zero|is_not_unique[cuadrantes.id]',
        ];
        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $domModel = new DomiciliarioModel();
        $cuaModel = new CuadranteModel();
        $dom = $domModel->find($this->request->getPost('domiciliario_id'));
        $cua = $cuaModel->find($this->request->getPost('cuadrante_id'));

        if (! $dom || ($dom['estado'] ?? '') !== 'Activo') {
            return redirect()->back()->withInput()->with('error', 'El domiciliario no existe o no está activo.');
        }
        if (! $cua || ($cua['estado'] ?? '') !== 'Activo') {
            return redirect()->back()->withInput()->with('error', 'El cuadrante no existe o no está activo.');
        }

        $monto = (float) ($cua['precio'] ?? 0);

        $pedidoModel = new PedidoModel();
        $pedidoModel->update($id, [
            'domiciliario_id' => (int) $this->request->getPost('domiciliario_id'),
            'cuadrante_id'    => (int) $this->request->getPost('cuadrante_id'),
            'monto'           => $monto,
        ]);
        flash_editado('Actualizamos la información del pedido.', null, 'alert');
        return redirect()->to("/pedidos/factura/{$id}")
            ->with('success', 'Pedido actualizado correctamente.');
    }

    // Factura del día para un domiciliario
    public function facturaDia()
    {
        $domiciliarioId = (int) ($this->request->getGet('domiciliario_id') ?? 0);
        $fecha          = $this->request->getGet('fecha');

        if ($domiciliarioId <= 0 || ! $fecha) {
            return redirect()->to('/pedidos')
                ->with('showFacturaDiaModal', true)
                ->with('fd_error', 'Debes seleccionar domiciliario y fecha.');
        }

        $domModel = new DomiciliarioModel();
        $dom = $domModel->find($domiciliarioId);
        if (! $dom || ($dom['estado'] ?? '') !== 'Activo') {
            return redirect()->to('/pedidos')
                ->with('showFacturaDiaModal', true)
                ->with('fd_error', 'El domiciliario no existe o no está activo.');
        }

        $pedidoModel = new PedidoModel();

        // Solo pendientes del día
        $pendientes = $pedidoModel->pedidosDeDia($domiciliarioId, $fecha, 0);

        // Corridas ya hechas HOY (sobre pedidos de ese día)
        $corridasPrevias = $pedidoModel->corridasEnDia($domiciliarioId, $fecha);

        if (empty($pendientes)) {
            $msg = $corridasPrevias > 0
                ? "No hay pedidos pendientes para {$dom['nombre']} el {$fecha} (ya van {$corridasPrevias} corridas de pago hoy)."
                : "No se encontraron pedidos para {$dom['nombre']} el {$fecha}.";
            return redirect()->back()
                ->withInput()
                ->with('showFacturaDiaModal', true)
                ->with('fd_domiciliario_id', $domiciliarioId)
                ->with('fd_fecha', $fecha)
                ->with('fd_error', $msg);
        }

        // === AQUI APLICAMOS LA REGLA DE PAGO ===
        usort($pendientes, static function ($a, $b) {
            $ta = strtotime($a['created_at'] ?? '1970-01-01 00:00:00');
            $tb = strtotime($b['created_at'] ?? '1970-01-01 00:00:00');
            if ($ta === $tb) {
                return ((int)($a['id'] ?? 0)) <=> ((int)($b['id'] ?? 0));
            }
            return $ta <=> $tb;
        });

        $pendientesCalc  = [];
        $totalPendientes = 0.0;

        foreach ($pendientes as $idx => $p) {
            $base    = (float)($p['monto'] ?? 0);
            $aplicar = ($idx === 0) ? $base : ($base / 2); // 1º = 100%, siguientes = 50%
            $p['monto_calculado'] = $aplicar;
            $pendientesCalc[]     = $p;
            $totalPendientes     += $aplicar;
        }

        $conteo    = count($pendientes);
        $reglaPago = ($conteo > 1)
            ? 'Primer pedido 100%, siguientes 50%'
            : 'Pago completo por único pedido';

        $data = [
            'fecha'           => $fecha,
            'domiciliario'    => $dom['nombre'] ?? 'N/D',
            'domiciliarioId'  => $domiciliarioId,
            'pedidos'         => $pendientesCalc,
            'total'           => $totalPendientes,
            'corridaNumero'   => $corridasPrevias + 1,
            'reglaPago'       => $reglaPago,
            'factorPago'      => ($conteo > 1) ? null : 1.0,
        ];

        return view('pedidos/factura_dia', $data);
    }

    public function factura(int $id)
    {
        $pedidoModel = new PedidoModel();
        $pedido = $pedidoModel->getWithRelations($id);

        if (empty($pedido)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Pedido no encontrado');
        }

        return view('pedidos/factura', ['pedido' => $pedido]);
    }

    // Eliminar
    public function delete($id)
    {
        helper('feedback');
        $model = new PedidoModel();
        $model->delete($id);

        flash_eliminado('El pedido fue eliminado del sistema.', null, 'modal');
        return redirect()->to('/pedidos')->with('success', 'Pedido eliminado correctamente.');
    }

    // Pagar pedidos del día
    public function pagarDia()
    {
        $domiciliarioId = (int) $this->request->getPost('domiciliario_id');
        $fecha          = $this->request->getPost('fecha');

        if ($domiciliarioId <= 0 || ! $fecha) {
            return redirect()->back()->with('error', 'Datos incompletos.');
        }

        $inicioUTC = \CodeIgniter\I18n\Time::parse($fecha . ' 00:00:00', 'America/Bogota')->setTimezone('UTC')->toDateTimeString();
        $finUTC    = \CodeIgniter\I18n\Time::parse($fecha . ' 23:59:59', 'America/Bogota')->setTimezone('UTC')->toDateTimeString();

        $pedidoModel = new PedidoModel();
        $pedidoModel->where('domiciliario_id', $domiciliarioId)
            ->where('created_at >=', $inicioUTC)
            ->where('created_at <=', $finUTC)
            ->where('pagado', 0)
            ->set(['pagado' => 1, 'pagado_at' => date('Y-m-d H:i:s')])
            ->update();

        return redirect()->to('/pedidos')->with('success', 'Pedidos del día marcados como pagados.');
    }

    /**
     * (Compat) JSON "ligero" desde la DB.
     * Puedes dejarlo igual; el front nuevo debería consumir /pedidos/cuadrantes-geojson
     */
    public function cuadrantesJson()
    {
        $cuaModel = new CuadranteModel();

        $cuadrantes = $cuaModel
            ->select('id, nombre, precio, coords_json')
            ->where('estado', 'Activo')
            ->findAll();

        return $this->response->setJSON($cuadrantes);
    }

    /**
     * NUEVO: Unifica DB + GeoJSON externo y entrega un GeoJSON listo para Turf.
     * - Empareja por (localidad + nombre de barrio) y luego por nombre.
     * - Fallback a coords_json de la DB (convierte [lat,lon] -> [lon,lat] y cierra el anillo).
     */
    public function cuadrantesGeojson()
    {
        $cuaModel = new CuadranteModel();

        // Tu tabla: id, nombre, localidad, barrios, precio, coords_json
        $cuadrantes = $cuaModel
            ->select('id, nombre, localidad, barrios, precio, coords_json')
            ->where('estado', 'Activo')
            ->findAll();

        // Cargar archivo público
        $path = FCPATH . 'geojson/BarriosBarranquilla.geojson';
        $features = [];
        if (is_file($path)) {
            $geo = json_decode(file_get_contents($path), true);
            $features = $geo['features'] ?? [];
        }

        // Índices: por nombre y por (localidad|nombre)
        $byName    = [];
        $byNameLoc = [];
        foreach ($features as $f) {
            $props = $f['properties'] ?? [];
            $gNombre    = $props['nombre']    ?? null;
            $gLocalidad = $props['localidad'] ?? null;

            if ($gNombre) {
                $sn = $this->slug($gNombre);
                $byName[$sn] = [
                    'geometry' => $f['geometry'] ?? null,
                    'props'    => [
                        'nombre'     => $props['nombre']    ?? null,
                        'localidad'  => $props['localidad'] ?? null,
                        'pieza_urba' => $props['pieza_urba'] ?? null,
                    ],
                ];
                if ($gLocalidad) {
                    $key = $this->slug($gLocalidad) . '|' . $sn;
                    $byNameLoc[$key] = $byName[$sn];
                }
            }
        }

        $out = ['type' => 'FeatureCollection', 'features' => []];

        foreach ($cuadrantes as $c) {
            $geom   = null;
            $src    = 'db';
            $gProps = ['localidad' => null, 'pieza_urba' => null, 'nombre' => $c['nombre']];

            // Construir candidatos de nombre de barrio
            $candidateNames = [];
            if (!empty($c['barrios'])) {
                $parts = preg_split('/[;,]/', (string)$c['barrios']);
                foreach ($parts as $p) {
                    $p = trim($p);
                    if ($p !== '') $candidateNames[] = $p;
                }
            }
            // Fallback: usar el nombre del cuadrante
            if (empty($candidateNames) && !empty($c['nombre'])) {
                $candidateNames[] = $c['nombre'];
            }

            // 1) Emparejar por (localidad|nombre)
            if (!empty($c['localidad']) && $candidateNames) {
                foreach ($candidateNames as $nm) {
                    $key = $this->slug($c['localidad']) . '|' . $this->slug($nm);
                    if (isset($byNameLoc[$key])) {
                        $geom   = $byNameLoc[$key]['geometry'];
                        $gProps = array_merge($gProps, $byNameLoc[$key]['props']);
                        $src    = 'geojson';
                        break;
                    }
                }
            }

            // 2) Si no, por nombre solo
            if (!$geom && $candidateNames) {
                foreach ($candidateNames as $nm) {
                    $sn = $this->slug($nm);
                    if (isset($byName[$sn])) {
                        $geom   = $byName[$sn]['geometry'];
                        $gProps = array_merge($gProps, $byName[$sn]['props']);
                        $src    = 'geojson';
                        break;
                    }
                }
            }

            // 3) Fallback: usar coords_json de la DB (suponiendo [lat,lon] en tu DB)
            if (!$geom) {
                $ring = json_decode($c['coords_json'] ?? '[]', true) ?: [];
                // Convertir [lat,lon] -> [lon,lat]
                $ring = array_map(static fn($p) => [ (float)$p[1], (float)$p[0] ], $ring);

                // Cerrar anillo si hace falta
                if ($ring && ($ring[0][0] !== $ring[count($ring)-1][0] || $ring[0][1] !== $ring[count($ring)-1][1])) {
                    $ring[] = $ring[0];
                }

                if ($ring) {
                    $geom = ['type' => 'Polygon', 'coordinates' => [ $ring ]];
                }
            }

            if ($geom) {
                $out['features'][] = [
                    'type'       => 'Feature',
                    'geometry'   => $geom, // Puede ser Polygon o MultiPolygon del archivo
                    'properties' => [
                        'id'         => (int)$c['id'],         // id interno del cuadrante
                        'nombre'     => $c['nombre'],
                        'localidad'  => $c['localidad'],
                        'barrios'    => $c['barrios'],
                        'precio'     => (float)$c['precio'],
                        'source'     => $src,                   // 'geojson' o 'db'
                    ],
                ];
            }
        }

        return $this->response->setJSON($out);
    }

    // Normalizador simple para comparar cadenas (quita acentos, espacios múltiples, etc.)
    protected function slug(?string $s): string
    {
        $s = $s ?? '';
        $s = trim(mb_strtolower($s, 'UTF-8'));
        $s = strtr($s, ['á'=>'a','é'=>'e','í'=>'i','ó'=>'o','ú'=>'u','ü'=>'u','ñ'=>'n']);
        $s = preg_replace('/\s+/', ' ', $s);
        return $s;
    }
}
