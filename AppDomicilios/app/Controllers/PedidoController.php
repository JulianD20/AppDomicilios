<?php
namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\PedidoModel;
use App\Models\DomiciliarioModel;
use App\Models\CuadranteModel;

class PedidoController extends BaseController
{
public function index()
    {
        $pedidoModel = new PedidoModel();
        $data['pedidos'] = $pedidoModel->getWithRelations();

        $domModel = new DomiciliarioModel();
        $data['domiciliarios'] = $domModel->where('estado', 'Activo')->orderBy('nombre', 'ASC')->findAll();

        return view('pedidos/index', $data);
    }

    public function create()
    {
        // Poblar selects con activos
        $domModel = new DomiciliarioModel();
        $cuaModel = new CuadranteModel();

        $data['domiciliarios'] = $domModel->where('estado', 'Activo')->orderBy('nombre', 'ASC')->findAll();
        $data['cuadrantes']    = $cuaModel->where('estado', 'Activo')->orderBy('nombre', 'ASC')->findAll();

        return view('pedidos/create', $data);
    }

    public function store()
    {
        helper('feedback');
        $rules = [
            'domiciliario_id' => 'required|is_natural_no_zero|is_not_unique[domiciliarios.id]',
            'cuadrante_id'    => 'required|is_natural_no_zero|is_not_unique[cuadrantes.id]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Validar que ambos estén Activos
        $domModel = new DomiciliarioModel();
        $cuaModel = new CuadranteModel();

        $dom = $domModel->find($this->request->getPost('domiciliario_id'));
        $cua = $cuaModel->find($this->request->getPost('cuadrante_id'));

        if (! $dom || ($dom['estado'] ?? '') !== 'Activo') {
            return redirect()->back()->withInput()->with('error', 'El domiciliario no existe o no está activo.');
        }
        if (! $cua || ($cua['estado'] ?? '') !== 'Activo') {
            return redirect()->back()->withInput()->with('error', 'El cuadrante no existe o no está activo.');
        }

        $monto = (float) ($cua['precio'] ?? 0);

        $pedidoModel = new PedidoModel();
        $id = $pedidoModel->insert([
            'domiciliario_id' => (int) $this->request->getPost('domiciliario_id'),
            'cuadrante_id'    => (int) $this->request->getPost('cuadrante_id'),
            'direccion'       => $this->request->getPost('direccion'),
            'monto'           => $monto,
        ], true); // true = return insert ID
        flash_guardado('El pedido se guardó correctamente.', null, 'toast'); 
        return redirect()->to("/pedidos/factura/{$id}")
            ->with('success', 'Pedido asignado correctamente.');
    }

        public function edit(int $id)
    {
        $pedidoModel = new PedidoModel();
        $domModel    = new DomiciliarioModel();
        $cuaModel    = new CuadranteModel();

        $pedido = $pedidoModel->find($id);
        if (!$pedido) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Pedido no encontrado');
        }

        $data['pedido']        = $pedido;
        $data['domiciliarios'] = $domModel->where('estado', 'Activo')->orderBy('nombre', 'ASC')->findAll();
        $data['cuadrantes']    = $cuaModel->where('estado', 'Activo')->orderBy('nombre', 'ASC')->findAll();

        return view('pedidos/edit', $data);
    }

    public function update(int $id)
    {
        helper('feedback');
        $rules = [
            'domiciliario_id' => 'required|is_natural_no_zero|is_not_unique[domiciliarios.id]',
            'cuadrante_id'    => 'required|is_natural_no_zero|is_not_unique[cuadrantes.id]',
        ];
        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $domModel = new DomiciliarioModel();
        $cuaModel = new CuadranteModel();
        $dom = $domModel->find($this->request->getPost('domiciliario_id'));
        $cua = $cuaModel->find($this->request->getPost('cuadrante_id'));

        if (! $dom || ($dom['estado'] ?? '') !== 'Activo') {
            return redirect()->back()->withInput()->with('error', 'El domiciliario no existe o no está activo.');
        }
        if (! $cua || ($cua['estado'] ?? '') !== 'Activo') {
            return redirect()->back()->withInput()->with('error', 'El cuadrante no existe o no está activo.');
        }

        $monto = (float) ($cua['precio'] ?? 0); 


        $pedidoModel = new PedidoModel();
        $pedidoModel->update($id, [
            'domiciliario_id' => (int) $this->request->getPost('domiciliario_id'),
            'cuadrante_id'    => (int) $this->request->getPost('cuadrante_id'),
            'monto'           => $monto,
        ]);
        flash_editado('Actualizamos la información del pedido.', null, 'alert');
        return redirect()->to("/pedidos/factura/{$id}")
            ->with('success', 'Pedido actualizado correctamente.');
    }


    public function factura(int $id)
    {
        $pedidoModel = new PedidoModel();
        $pedido = $pedidoModel->getWithRelations($id);

        if (empty($pedido)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Pedido no encontrado');
        }

        return view('pedidos/factura', ['pedido' => $pedido]);
    }

    //Eliminar
    public function delete($id)
    {
        helper('feedback');
        $model = new PedidoModel();
        $model->delete($id);

        flash_eliminado('El pedido fue eliminado del sistema.', null, 'modal');
        return redirect()->to('/pedidos')->with('success', 'Pedido eliminado correctamente.');
    }
    
    //Pagar pedidos del día
    public function pagarDia()
    {
        $domiciliarioId = (int) $this->request->getPost('domiciliario_id');
        $fecha          = $this->request->getPost('fecha');

        if ($domiciliarioId <= 0 || ! $fecha) {
            return redirect()->back()->with('error', 'Datos incompletos.');
        }

        $inicioUTC = \CodeIgniter\I18n\Time::parse($fecha.' 00:00:00', 'America/Bogota')->setTimezone('UTC')->toDateTimeString();
        $finUTC    = \CodeIgniter\I18n\Time::parse($fecha.' 23:59:59', 'America/Bogota')->setTimezone('UTC')->toDateTimeString();

        $pedidoModel = new PedidoModel();
        $pedidoModel->where('domiciliario_id', $domiciliarioId)
            ->where('created_at >=', $inicioUTC)
            ->where('created_at <=', $finUTC)
            ->where('pagado', 0)
            ->set(['pagado' => 1, 'pagado_at' => date('Y-m-d H:i:s')])
            ->update();
        
        return redirect()->to('/pedidos')->with('success', 'Pedidos del día marcados como pagados.');
    }
    

    // Factura del día para un domiciliario

    public function facturaDia()
    {
        $domiciliarioId = (int) ($this->request->getGet('domiciliario_id') ?? 0);
        $fecha          = $this->request->getGet('fecha');

        if ($domiciliarioId <= 0 || ! $fecha) {
            return redirect()->to('/pedidos')
                ->with('showFacturaDiaModal', true)
                ->with('fd_error', 'Debes seleccionar domiciliario y fecha.');
        }

        $domModel = new DomiciliarioModel();
        $dom = $domModel->find($domiciliarioId);
        if (! $dom || ($dom['estado'] ?? '') !== 'Activo') {
            return redirect()->to('/pedidos')
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
            return redirect()->back()
                ->withInput()
                ->with('showFacturaDiaModal', true)
                ->with('fd_domiciliario_id', $domiciliarioId)
                ->with('fd_fecha', $fecha)
                ->with('fd_error', $msg);
        }

        $totalPendientes = 0;
        foreach ($pendientes as $p) {
            $totalPendientes += (float) $p['monto'];
        }

        $data = [
            'fecha'           => $fecha,
            'domiciliario'    => $dom['nombre'] ?? 'N/D',
            'domiciliarioId'  => $domiciliarioId,
            'pedidos'         => $pendientes,       // solo pendientes
            'total'           => $totalPendientes,  // total pendientes
            'corridaNumero'   => $corridasPrevias + 1, // ← esta sería la N° corrida si se paga ahora
        ];

        return view('pedidos/factura_dia', $data);
    }

    public function cuadrantesJson()
    {
        $cuaModel = new CuadranteModel();

        $cuadrantes = $cuaModel
            ->select('id, nombre, precio, coords_json') // solo lo necesario
            ->where('estado', 'Activo')
            ->findAll();

        return $this->response->setJSON($cuadrantes);
    }


}
