<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\CuadranteModel;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\HTTP\ResponseInterface;

class CuadranteController extends BaseController
{
    use ResponseTrait;

    public function index()
    {
        $q      = $this->request->getGet('q');           // búsqueda por nombre
        $activo = $this->request->getGet('activo');      // 0/1
        $limit  = (int)($this->request->getGet('limit') ?? 100);
        $offset = (int)($this->request->getGet('offset') ?? 0);
        $pretty = $this->request->getGet('pretty') === '1';

        $db = db_connect();
        $builder = $db->table('cuadrante')
                      ->select('id,nombre,lat,lon,localidad,barrios,precio_base,activo,coords_json,creado_en');

        if ($q) {
            $builder->like('nombre', $q);
        }
        if ($activo !== null && $activo !== '') {
            $builder->where('activo', (int)$activo);
        }

        $rows = $builder->orderBy('id','DESC')->limit($limit, $offset)->get()->getResultArray();

        if ($pretty) {
            foreach ($rows as &$r) {
                $r['coords_json'] = json_encode(json_decode($r['coords_json'], true), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            }
        }

        return $this->respond($rows);
    }

    public function show($id = null)
    {
        $model = new CuadranteModel();
        $row = $model->find($id);
        return $row ? $this->respond($row) : $this->failNotFound('Cuadrante no encontrado');
    }

    public function create()
    {
        $data = $this->request->getJSON(true);
        if (!$data) {
            return $this->failValidationErrors('JSON body requerido');
        }

        // coords_json puede venir como string o como array JS
        if (!isset($data['coords_json'])) {
            return $this->failValidationErrors('coords_json es requerido');
        }
        if (is_array($data['coords_json'])) {
            $data['coords_json'] = json_encode($data['coords_json'], JSON_UNESCAPED_UNICODE);
        }
        if (!is_array(json_decode($data['coords_json'], true))) {
            return $this->failValidationErrors('coords_json debe ser JSON de arreglo [[lat,lon], ...]');
        }

        try {
            $model = new CuadranteModel();
            if (!$model->insert($data)) {
                return $this->failValidationErrors($model->errors());
            }
            return $this->respondCreated(['id' => $model->getInsertID()]);
        } catch (\Throwable $e) {
            // Mensajes de los triggers (SQLSTATE 45000) llegan aquí
            return $this->fail($e->getMessage(), ResponseInterface::HTTP_BAD_REQUEST);
        }
    }

    public function update($id = null)
    {
        $model = new CuadranteModel();
        if (!$model->find($id)) {
            return $this->failNotFound('Cuadrante no encontrado');
        }

        $data = $this->request->getJSON(true) ?? $this->request->getRawInput();

        if (isset($data['coords_json'])) {
            if (is_array($data['coords_json'])) {
                $data['coords_json'] = json_encode($data['coords_json'], JSON_UNESCAPED_UNICODE);
            }
            if (!is_array(json_decode($data['coords_json'], true))) {
                return $this->failValidationErrors('coords_json debe ser JSON válido (arreglo)');
            }
        }

        try {
            if (!$model->update($id, $data)) {
                return $this->failValidationErrors($model->errors());
            }
            return $this->respondUpdated(['id' => (int)$id]);
        } catch (\Throwable $e) {
            return $this->fail($e->getMessage(), ResponseInterface::HTTP_BAD_REQUEST);
        }
    }

    public function delete($id = null)
    {
        $model = new CuadranteModel();
        if (!$model->find($id)) {
            return $this->failNotFound('Cuadrante no encontrado');
        }

        try {
            $model->delete($id);
            return $this->respondDeleted(['id' => (int)$id]);
        } catch (\Throwable $e) {
            // FK constraint (pedido/pago/historial referencian cuadrante)
            $msg = $e->getMessage();
            if (strpos($msg, '1451') !== false || stripos($msg, 'foreign key') !== false) {
                return $this->failResourceExists('No se puede eliminar: tiene registros relacionados (pedidos/pagos/historial).', 409);
            }
            return $this->fail($msg, ResponseInterface::HTTP_BAD_REQUEST);
        }
    }
}
