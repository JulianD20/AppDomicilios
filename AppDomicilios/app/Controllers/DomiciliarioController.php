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
        helper('feedback');
        $model = new DomiciliarioModel();

        $data = [
            'nombre'        => $this->request->getPost('nombre'),
            'telefono'      => $this->request->getPost('telefono'),
            'cedula'        => $this->request->getPost('cedula'),
            'estado'        => $this->request->getPost('estado'),
            'fecha_ingreso' => $this->request->getPost('fecha_ingreso'),
        ];

        $model->save($data);
        flash_guardado('El domiciliario se guardó correctamente.', null, 'toast'); 
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
        helper('feedback');
        $model = new DomiciliarioModel();

        $data = [
            'nombre'        => $this->request->getPost('nombre'),
            'telefono'      => $this->request->getPost('telefono'),
            'cedula'        => $this->request->getPost('cedula'),
            'estado'        => $this->request->getPost('estado'),
            'fecha_ingreso' => $this->request->getPost('fecha_ingreso'),
        ];

        $model->update($id, $data);
        flash_editado('Actualizamos la información del domiciliario.', null, 'alert');
        return redirect()->to('/domiciliarios')->with('success', 'Domiciliario actualizado correctamente.');
    }

    // Mostrar detalle
    public function show($id)
    {
        $model = new DomiciliarioModel();
        $data['domiciliario'] = $model->find($id);

        if (!$data['domiciliario']) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Domiciliario no encontrado");
        }

        return view('domiciliarios/show', $data);
    }


    //Eliminar
    public function delete($id)
    {
        helper('feedback');
        $model = new DomiciliarioModel();
        $model->delete($id);
        flash_eliminado('El domiciliario fue eliminado del sistema.', null, 'modal');
        return redirect()->to('/domiciliarios')->with('success', 'Domiciliario eliminado correctamente.');
    }
}
