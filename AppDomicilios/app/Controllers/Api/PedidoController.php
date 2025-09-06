<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\PedidoModel;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\HTTP\ResponseInterface;


class PedidosController extends BaseController
{
    use ResponseTrait;

    public function index()
    {
        $domiId = $this->request->getGet('domiciliario_id');
        $cuadId = $this->request->getGet('cuadrante_id');
        $estado = $this->request->getGet('estado'); // asignado|entregado|cancelado
        $desde  = $this->request->getGet('desde');  // YYYY-MM-DD
        $hasta  = $this->request->getGet('hasta');  // YYYY-MM-DD
        $limit  = (int)($this->request->getGet('limit') ?? 100);
        $offset = (int)($this->request->getGet('offset') ?? 0);

        $db = db_connect();
        $builder = $db->table('pedido')->select('*');

        if ($domiId) $builder->where('domiciliario_id', (int)$domiId);
        if ($cuadId) $builder->where('cuadrante_id', (int)$cuadId);
        if ($estado) $builder->where('estado', $estado);
        if ($desde)  $builder->where('fecha_hora >=', $desde . ' 00:00:00');
        if ($hasta)  $builder->where('fecha_hora <=', $hasta . ' 23:59:59');

        $rows = $builder->orderBy('fecha_hora', 'DESC')->limit($limit, $offset)->get()->getResultArray();
        return $this->respond($rows);
    }

    public function show($id = null)
    {
        $model = new PedidoModel();
        $row = $model->find($id);
        return $row ? $this->respond($row) : $this->failNotFound('Pedido no encontrado');
    }

    public function create()
    {
        $data = $this->request->getJSON(true);
        if (!$data) return $this->failValidationErrors('JSON body requerido');

        // Si no envían precio_unitario, tomar snapshot del precio_base del cuadrante
        if (!isset($data['precio_unitario'])) {
            $cuadId = $data['cuadrante_id'] ?? null;
            if (!$cuadId) return $this->failValidationErrors('cuadrante_id es requerido para auto fijar precio_unitario');
            $precio = db_connect()->table('cuadrante')->select('precio_base')->where('id', (int)$cuadId)->get()->getRow('precio_base');
            if ($precio === null) return $this->failValidationErrors('cuadrante_id no existe');
            $data['precio_unitario'] = $precio; // snapshot
        }

        $model = new PedidoModel();
        if (!$model->insert($data)) {
            return $this->failValidationErrors($model->errors());
        }
        return $this->respondCreated(['id' => $model->getInsertID()]);
    }

    public function update($id = null)
    {
        $data = $this->request->getJSON(true) ?? $this->request->getRawInput();
        $model = new PedidoModel();
        if (!$model->find($id)) return $this->failNotFound('Pedido no encontrado');

        if (!$model->update($id, $data)) {
            return $this->failValidationErrors($model->errors());
        }
        return $this->respondUpdated(['id' => (int)$id]);
    }

    public function delete($id = null)
    {
        $model = new PedidoModel();
        if (!$model->find($id)) return $this->failNotFound('Pedido no encontrado');

        $model->delete($id);
        return $this->respondDeleted(['id' => (int)$id]);
    }
}
