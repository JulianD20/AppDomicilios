<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\HTTP\ResponseInterface;

class Pagos extends BaseController
{
    use ResponseTrait;

    public function index()
    {
        $domiId = $this->request->getGet('domiciliario_id');
        $cuadId = $this->request->getGet('cuadrante_id');
        $fecha  = $this->request->getGet('fecha'); // YYYY-MM-DD
        $limit  = (int)($this->request->getGet('limit') ?? 100);
        $offset = (int)($this->request->getGet('offset') ?? 0);

        $db = db_connect();
        $b = $db->table('pago')->select('*');

        if ($domiId) $b->where('domiciliario_id', (int)$domiId);
        if ($cuadId !== null && $cuadId !== '') $b->where('cuadrante_id', $cuadId); // permite NULL si no viene
        if ($fecha)  $b->where('fecha_liquidacion', $fecha);

        $rows = $b->orderBy('fecha_liquidacion', 'DESC')->limit($limit, $offset)->get()->getResultArray();
        return $this->respond($rows);
    }

    public function show($id = null)
    {
        $db = db_connect();
        $row = $db->table('pago')->where('id', (int)$id)->get()->getRowArray();
        return $row ? $this->respond($row) : $this->failNotFound('Pago no encontrado');
    }

    // Crear / actualizar / borrar -> no aquí (usar endpoints de Liquidaciones)
    public function create()  { return $this->fail('Usa POST /api/liquidar para generar pagos', 405); }
    public function update($id = null) { return $this->fail('Actualización directa de pagos no permitida', 405); }
    public function delete($id = null) { return $this->fail('Borrado directo no permitido. Usa DELETE /api/liquidar/{fecha}', 405); }
}
