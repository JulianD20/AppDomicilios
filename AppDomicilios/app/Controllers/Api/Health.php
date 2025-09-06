<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\HTTP\ResponseInterface;

class Health extends BaseController
{
    use ResponseTrait;

    public function db()
    {
        $started = microtime(true);

        try {
            $db = db_connect(); // usa config de .env

            // 1) prueba mínima
            $db->simpleQuery('SELECT 1');

            // 2) metadatos del servidor
            $ver = $db->query('SELECT @@version AS version, @@version_comment AS comment')
                      ->getRowArray();

            // 3) checks rápidos sobre tus tablas
            $cuadrantes = $db->table('cuadrante')->countAll();
            $domis      = $db->table('domiciliario')->countAll();
            $pedidos    = $db->table('pedido')->countAll();
            $pagos      = $db->table('pago')->countAll();
            $hist       = $db->table('historial')->countAll();

            $elapsed = round((microtime(true) - $started) * 1000, 2);

            // nombre de BD desde la config
            $dbCfg = config('Database');
            $dbName = $dbCfg->default['database'] ?? null;

            return $this->respond([
                'ok'             => true,
                'driver'         => $db->DBDriver,
                'database'       => $dbName,
                'server_version' => $ver['version'] ?? null,
                'server_comment' => $ver['comment'] ?? null,
                'latency_ms'     => $elapsed,
                'counts' => [
                    'cuadrante'   => $cuadrantes,
                    'domiciliario'=> $domis,
                    'pedido'      => $pedidos,
                    'pago'        => $pagos,
                    'historial'   => $hist,
                ],
                'time' => date('c'),
            ]);
        } catch (\Throwable $e) {
            return $this->fail([
                'ok'    => false,
                'error' => $e->getMessage(),
                'code'  => $e->getCode(),
            ], ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
