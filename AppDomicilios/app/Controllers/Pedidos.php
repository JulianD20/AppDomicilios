<?php
namespace App\Controllers;

use App\Models\PedidoModel;
use App\Models\DomiciliarioModel;
use App\Models\CuadranteModel;
use CodeIgniter\Database\Exceptions\DatabaseException;

class Pedidos extends BaseController
{
    public function index()
    {
        // Traemos con nombres legibles
        $db = db_connect();
        $rows = $db->query("
            SELECT p.*,
                   d.nombre AS domiciliario,
                   c.nombre AS cuadrante
            FROM pedido p
            JOIN domiciliario d ON d.id = p.domiciliario_id
            JOIN cuadrante   c ON c.id = p.cuadrante_id
            ORDER BY p.fecha_hora DESC, p.id DESC
        ")->getResultArray();

        return view('pedidos/index', ['pedidos' => $rows]);
    }

    // GET /pedidos/create
    public function create()
    {
        $domiciliarios = (new DomiciliarioModel())
            ->where('activo', 1)
            ->orderBy('nombre')->findAll();

        $cuadrantes = (new CuadranteModel())
            ->where('activo', 1)
            ->orderBy('nombre')->findAll();

        return view('pedidos/create', [
            'domiciliarios' => $domiciliarios,
            'cuadrantes'    => $cuadrantes,
        ]);
    }

    // POST /pedidos/store
    public function store()
    {
        $domiId = (int) $this->request->getPost('domiciliario_id');
        $cuaId  = (int) $this->request->getPost('cuadrante_id');
        $monto  = $this->request->getPost('monto');            // del form
        $estado = 'asignado';                                  // por ahora lo dejamos asignado
        $direccion = $this->request->getPost('direccion') ?: null;
        $notas     = $this->request->getPost('notas') ?: null;

        if (!$domiId || !$cuaId) {
            return redirect()->back()->with('error', 'Selecciona domiciliario y cuadrante')->withInput();
        }

        // Si no vino monto, usamos precio_base del cuadrante (snapshot)
        if ($monto === null || $monto === '' || !is_numeric($monto)) {
            $cuad = (new CuadranteModel())->select('precio_base')->find($cuaId);
            if (!$cuad) {
                return redirect()->back()->with('error', 'Cuadrante inválido')->withInput();
            }
            $monto = (float) $cuad['precio_base'];
        } else {
            $monto = (float) $monto;
        }

        $data = [
            'domiciliario_id' => $domiId,
            'cuadrante_id'    => $cuaId,
            'direccion'       => $direccion,
            'fecha_hora'      => date('Y-m-d H:i:s'),   // ahora
            'estado'          => $estado,               // asignado
            'precio_unitario' => $monto,                // snapshot
            'notas'           => $notas,
        ];

        try {
            $model = new PedidoModel();
            if (!$model->insert($data)) {
                return redirect()->back()->with('error', implode('; ', $model->errors()))->withInput();
            }
            return redirect()->to('/pedidos')->with('success', 'Pedido creado');
        } catch (DatabaseException $dbEx) {
            return redirect()->back()->with('error', $dbEx->getMessage())->withInput();
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    // (Opcional) vista de factura simple /pedidos/factura/{id}
    public function factura(int $id)
    {
        $db  = db_connect();
        $row = $db->query("
            SELECT p.*,
                   d.nombre AS domiciliario,
                   c.nombre AS cuadrante
            FROM pedido p
            JOIN domiciliario d ON d.id = p.domiciliario_id
            JOIN cuadrante   c ON c.id = p.cuadrante_id
            WHERE p.id = ?
        ", [$id])->getRowArray();

        if (!$row) {
            return redirect()->to('/pedidos')->with('error', 'Pedido no encontrado');
        }

        return view('pedidos/factura', ['pedido' => $row]);
    }
}
