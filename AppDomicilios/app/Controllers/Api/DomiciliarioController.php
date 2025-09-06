<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\DomiciliarioModel;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\HTTP\ResponseInterface;

class DomiciliarioController extends BaseController
{
    use ResponseTrait;

    public function index()
    {
        $q      = $this->request->getGet('q');       // búsqueda por nombre
        $activo = $this->request->getGet('activo');  // 0/1
        $limit  = (int)($this->request->getGet('limit') ?? 100);
        $offset = (int)($this->request->getGet('offset') ?? 0);

        $db = db_connect();
        $builder = $db->table('domiciliario')
                      ->select('id,nombre,telefono,cedula,activo,fecha_ingreso,creado_en');

        if ($q)       $builder->like('nombre', $q);
        if ($activo !== null) $builder->where('activo', (int)$activo);

        $rows = $builder->limit($limit, $offset)->get()->getResultArray();
        return $this->respond($rows);
    }

    public function show($id = null)
    {
        $model = new DomiciliarioModel();
        $row = $model->find($id);
        return $row ? $this->respond($row) : $this->failNotFound('Domiciliario no encontrado');
    }

    public function create()
    {
        $data = $this->request->getJSON(true);
        if (!$data) return $this->failValidationErrors('JSON body requerido');

        $model = new DomiciliarioModel();
        if (!$model->insert($data)) {
            return $this->failValidationErrors($model->errors());
        }
        return $this->respondCreated(['id' => $model->getInsertID()]);
    }

    public function update($id = null)
    {
        $data = $this->request->getJSON(true) ?? $this->request->getRawInput();
        $model = new DomiciliarioModel();
        if (!$model->find($id)) return $this->failNotFound('Domiciliario no encontrado');

        if (!$model->update($id, $data)) {
            return $this->failValidationErrors($model->errors());
        }
        return $this->respondUpdated(['id' => (int)$id]);
    }

    public function delete($id = null)
    {
        $model = new DomiciliarioModel();
        if (!$model->find($id)) return $this->failNotFound('Domiciliario no encontrado');

        $model->delete($id);
        return $this->respondDeleted(['id' => (int)$id]);
    }
}
