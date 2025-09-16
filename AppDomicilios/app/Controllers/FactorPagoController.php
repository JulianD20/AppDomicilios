<?php
namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\PedidoModel;
use App\Models\DomiciliarioModel;
use App\Models\FactorPagoConfigModel;

class FactorPagoController extends BaseController
{
    /**
     * Lee y normaliza las reglas de pago por tramos desde la BD.
     * Formato: [ ['from'=>1,'to'=>1,'percent'=>100], ['from'=>2,'to'=>null,'percent'=>50], ... ]
     */
    protected function getRules(): array
    {
        $m = new FactorPagoConfigModel();
        return $m->getRules(); // ya viene normalizado y ordenado por 'from'
    }

    public function index()
    {
        // Poblar selects con domiciliarios activos
        $domModel = new DomiciliarioModel();
        $data['domiciliarios'] = $domModel->where('estado', 'Activo')->orderBy('nombre', 'ASC')->findAll();

        // Config actual (reglas por tramos) para mostrar en la vista
        $cfgModel = new FactorPagoConfigModel();
        $data['rules']     = $cfgModel->getRules();
        $data['rulesText'] = $cfgModel->summarize($data['rules']); // "1–1 al 100%, 2–4 al 70%, 5+ al 50%"

        // Reabrir el modal si vienes de error/faltan datos
        $data['showFacturaDiaModal'] = session('showFacturaDiaModal')
            || session('fd_error')
            || session('fd_domiciliario_id')
            || session('fd_fecha');

        return view('factor_pago/index', $data);
    }

    /**
     * Guarda las reglas escalonadas (JSON) desde el index.
     * Espera un POST 'reglas_json' (string JSON) con un array de filas {from, to|null, percent}.
     */
    public function updateConfig()
    {
        helper('feedback');

        $json  = (string) $this->request->getPost('reglas_json');
        $rules = json_decode($json, true);

        if (!is_array($rules) || empty($rules)) {
            return redirect()->back()->withInput()->with('error', 'Debes definir al menos un tramo.');
        }

        // Validación básica de cada fila
        foreach ($rules as $i => $r) {
            if (!isset($r['from']) || !isset($r['percent'])) {
                return redirect()->back()->withInput()->with('error', "Fila #".($i+1).": faltan campos.");
            }
            $from = (int)$r['from'];
            $to   = ($r['to'] === '' || $r['to'] === null) ? null : (int)$r['to'];
            $pct  = (float)$r['percent'];

            if ($from < 1 || $pct < 0 || $pct > 100) {
                return redirect()->back()->withInput()->with('error', "Fila #".($i+1).": valores fuera de rango.");
            }
            if ($to !== null && $to < $from) {
                return redirect()->back()->withInput()->with('error', "Fila #".($i+1).": 'hasta' no puede ser menor que 'desde'.");
            }
        }

        // Guardar
        $model = new FactorPagoConfigModel();
        $ok = $model->saveRules($rules);

        return redirect()->to('/factor-pago')->with($ok ? 'success' : 'error',
            $ok ? 'Reglas actualizadas.' : 'No se pudo guardar.');
    }

    public function facturaDia()
    {
        $domiciliarioId = (int) ($this->request->getGet('domiciliario_id') ?? 0);
        $fecha          = $this->request->getGet('fecha');

        if ($domiciliarioId <= 0 || ! $fecha) {
            return redirect()->to('/factor-pago')
                ->with('showFacturaDiaModal', true)
                ->with('fd_error', 'Debes seleccionar domiciliario y fecha.');
        }

        $domModel = new DomiciliarioModel();
        $dom = $domModel->find($domiciliarioId);
        if (! $dom || ($dom['estado'] ?? '') !== 'Activo') {
            return redirect()->to('/factor-pago')
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
            return redirect()->to('/factor-pago')
                ->with('showFacturaDiaModal', true)
                ->with('fd_domiciliario_id', $domiciliarioId)
                ->with('fd_fecha', $fecha)
                ->with('fd_error', $msg);
        }

        // Orden: más antiguo primero
        usort($pendientes, static function ($a, $b) {
            $ta = strtotime($a['created_at'] ?? '1970-01-01 00:00:00');
            $tb = strtotime($b['created_at'] ?? '1970-01-01 00:00:00');
            if ($ta === $tb) {
                return ((int)($a['id'] ?? 0)) <=> ((int)($b['id'] ?? 0));
            }
            return $ta <=> $tb;
        });

        // Reglas escalonadas
        $rules = $this->getRules(); // [{from,to|null,percent}, ...] ordenadas por 'from'

        // Aplica la PRIMERA regla que coincida con el número de pedido del día
        $aplicar = function (int $nroPedido, float $montoBase) use ($rules): array {
            foreach ($rules as $r) {
                $from = (int)$r['from'];
                $to   = $r['to'] === null ? PHP_INT_MAX : (int)$r['to'];
                if ($nroPedido >= $from && $nroPedido <= $to) {
                    $pct = max(0.0, min(100.0, (float)$r['percent']));
                    return [$montoBase * ($pct / 100.0), $pct];
                }
            }
            // Sin regla explícita => 100%
            return [$montoBase, 100.0];
        };

        // Calcular montos
        $pendientesCalc  = [];
        $totalPendientes = 0.0;

        foreach ($pendientes as $idx => $p) {
            $base    = (float)($p['monto'] ?? 0);
            $nro     = $idx + 1; // 1,2,3...
            [$monto, $pct] = $aplicar($nro, $base);

            $p['nro_en_dia']           = $nro;
            $p['porcentaje_aplicado']  = $pct;
            $p['monto_calculado']      = $monto;

            $pendientesCalc[] = $p;
            $totalPendientes += $monto;
        }

        // Texto legible de la regla
        $cfgModel  = new FactorPagoConfigModel();
        $reglaPago = $cfgModel->summarize($rules);

        $data = [
            'fecha'           => $fecha,
            'domiciliario'    => $dom['nombre'] ?? 'N/D',
            'domiciliarioId'  => $domiciliarioId,
            'pedidos'         => $pendientesCalc,
            'total'           => $totalPendientes,
            'corridaNumero'   => $corridasPrevias + 1,
            'reglaPago'       => $reglaPago,
            'factorPago'      => null, // ya no se usa un único factor fijo
            'cfg'             => ['rules' => $rules],
        ];

        return view('factor_pago/factura_dia', $data);
    }

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

        return redirect()->to('/factor-pago')->with('success', 'Pedidos del día marcados como pagados.');
    }
}
