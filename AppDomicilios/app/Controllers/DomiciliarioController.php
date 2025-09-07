<?php
namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\DomiciliarioModel;

class DomiciliarioController extends BaseController
{
    // Listar todos los domiciliarios
    public function index()
    {
        $model = new DomiciliarioModel();
        $data['domiciliarios'] = $model->paginate(10); // registros por página
        $data['pager'] = $model->pager; // pasamos el paginador

        return view('domiciliarios/index', $data);
    }

    // Mostrar formulario de creación
    public function create()
    {
        return view('domiciliarios/create');
    }

    // Guardar en la BD
    public function store()
    {
        $model = new DomiciliarioModel();

        $data = [
            'nombre'        => $this->request->getPost('nombre'),
            'telefono'      => $this->request->getPost('telefono'),
            'cedula'        => $this->request->getPost('cedula'),
            'estado'        => $this->request->getPost('estado'),
            'fecha_ingreso' => $this->request->getPost('fecha_ingreso'),
        ];

        $model->save($data);

        return redirect()->to('/domiciliarios')->with('success', 'Domiciliario creado correctamente.');
    }

    //Editar
    public function edit($id)
    {
        $model = new DomiciliarioModel();
        $data['domiciliario'] = $model->find($id);

        return view('domiciliarios/edit', $data);
    }

    //Actualizar
    public function update($id)
    {
        $model = new DomiciliarioModel();

        $data = [
            'nombre'        => $this->request->getPost('nombre'),
            'telefono'      => $this->request->getPost('telefono'),
            'cedula'        => $this->request->getPost('cedula'),
            'estado'        => $this->request->getPost('estado'),
            'fecha_ingreso' => $this->request->getPost('fecha_ingreso'),
        ];

        $model->update($id, $data);

        return redirect()->to('/domiciliarios')->with('success', 'Domiciliario actualizado correctamente.');
    }

    //Eliminar
    public function delete($id)
    {
        $model = new DomiciliarioModel();
        $model->delete($id);

        return redirect()->to('/domiciliarios')->with('success', 'Domiciliario eliminado correctamente.');
    }
}
