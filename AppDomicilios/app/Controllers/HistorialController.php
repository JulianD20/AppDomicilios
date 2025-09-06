<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;

class HistorialController extends BaseController
{
    use ResponseTrait;

    public function index()
    {
        $domiId = $this->request->getGet('domiciliario_id');
        $cuadId = $this->request->getGet('cuadrante_id');
        $desde  = $this->request->getGet('desde');  // YYYY-MM-DD
        $hasta  = $this->request->getGet('hasta');  // YYYY-MM-DD

        $db = db_connect();
        $sql = 'SELECT * FROM vw_historial WHERE 1=1';
        $params = [];

        if ($domiId) { $sql .= ' AND domiciliario = (SELECT nombre FROM domiciliario WHERE id = ?)'; $params[] = (int)$domiId; }
        if ($cuadId) { $sql .= ' AND cuadrante = (SELECT nombre FROM cuadrante WHERE id = ?)';      $params[] = (int)$cuadId; }
        if ($desde)  { $sql .= ' AND fecha >= ?'; $params[] = $desde; }
        if ($hasta)  { $sql .= ' AND fecha <= ?'; $params[] = $hasta; }

        $sql .= ' ORDER BY fecha, domiciliario, cuadrante';
        $rows = $db->query($sql, $params)->getResultArray();

        return $this->respond($rows);
    }
}
