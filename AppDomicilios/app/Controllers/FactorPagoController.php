<?php
namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\PedidoModel;
use App\Models\DomiciliarioModel;


class FactorPagoController extends BaseController
{
    public function index()
    {
        // Poblar selects con domiciliarios activos
        $domModel = new DomiciliarioModel();
        $data['domiciliarios'] = $domModel->where('estado', 'Activo')->orderBy('nombre', 'ASC')->findAll();

        // Permite reabrir el modal si vienes de un error o si faltaron datos
        $data['showFacturaDiaModal'] = session('showFacturaDiaModal')
            || session('fd_error')
            || session('fd_domiciliario_id')
            || session('fd_fecha');

        return view('factor_pago/index', $data);
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

        // === REGLA DE PAGO ===
        usort($pendientes, static function ($a, $b) {
            $ta = strtotime($a['created_at'] ?? '1970-01-01 00:00:00');
            $tb = strtotime($b['created_at'] ?? '1970-01-01 00:00:00');
            if ($ta === $tb) {
                return ((int)($a['id'] ?? 0)) <=> ((int)($b['id'] ?? 0));
            }
            return $ta <=> $tb; // más antiguo primero
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
