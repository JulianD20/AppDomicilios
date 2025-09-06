<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\HTTP\ResponseInterface;

class LiquidacionesController extends BaseController
{
    use ResponseTrait;

    public function preview()
    {
        $fecha  = $this->request->getGet('fecha');
        $umbral = $this->request->getGet('umbral');
        $canc   = $this->request->getGet('cancelados');
        $por    = $this->request->getGet('por_cuadrante');

        if (!$fecha) return $this->failValidationErrors('Parámetro fecha es requerido (YYYY-MM-DD)');

        $db = db_connect();
        $q  = $db->query('CALL sp_preview_liquidacion(?,?,?,?)', [
            $fecha,
            $umbral !== null ? (int)$umbral : null,
            $canc   !== null ? (int)$canc   : null,
            $por    !== null ? (int)$por    : null,
        ]);
        $rows = $q->getResultArray();
        $q->freeResult();
        return $this->respond($rows);
    }

    public function generar()
    {
        $body   = $this->request->getJSON(true) ?? [];
        $fecha  = $body['fecha']  ?? null;
        $umbral = $body['umbral'] ?? null;
        $canc   = $body['cancelados'] ?? null;
        $por    = $body['por_cuadrante'] ?? null;
        $obs    = $body['observacion'] ?? null;

        if (!$fecha) return $this->failValidationErrors('Campo fecha es requerido (YYYY-MM-DD)');

        $db = db_connect();
        $ok = $db->simpleQuery('CALL sp_generar_liquidaciones(?,?,?,?,?)', [
            $fecha,
            $umbral !== null ? (int)$umbral : null,
            $canc   !== null ? (int)$canc   : null,
            $por    !== null ? (int)$por    : null,
            $obs,
        ]);

        if (!$ok) {
            return $this->fail($db->error(), ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
        return $this->respond(['status' => 'ok', 'fecha' => $fecha]);
    }

    public function borrar(string $fecha)
    {
        $db = db_connect();
        $ok = $db->simpleQuery('CALL sp_borrar_liquidaciones_dia(?)', [$fecha]);
        if (!$ok) {
            return $this->fail($db->error(), ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
        return $this->respondDeleted(['status' => 'ok', 'fecha' => $fecha]);
    }
}
