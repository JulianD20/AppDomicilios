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
            'monto'           => $monto,
        ], true); // true = return insert ID

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
        $model = new PedidoModel();
        $model->delete($id);

        return redirect()->to('/pedidos')->with('success', 'Pedido eliminado correctamente.');
    }
}
