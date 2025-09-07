<?php
namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CuadranteModel;

class CuadranteController extends BaseController
{
// Listar todos los cuadrantes
    public function index()
    {
        $model = new CuadranteModel();
        $data['cuadrantes'] = $model->paginate(10); // registros por página
        $data['pager'] = $model->pager; // pasamos el paginador

        return view('cuadrantes/index', $data);
    }

    // Mostrar formulario de creación
    public function create()
    {
        return view('cuadrantes/create');
    }

    // Guardar en la BD
    public function store()
    {
        $model = new CuadranteModel();

        $data = [
            'nombre'        => $this->request->getPost('nombre'),
            'localidad'     => $this->request->getPost('localidad'),
            'barrios'       => $this->request->getPost('barrios'),
            'precio'        => $this->request->getPost('precio'),
            'coords_json'   => $this->request->getPost('coords_json'),
            'estado'        => $this->request->getPost('estado'),
        ];

        $model->save($data);

        return redirect()->to('/cuadrantes')->with('success', 'Cuadrante creado correctamente.');
    }

    //Editar
    public function edit($id)
    {
        $model = new CuadranteModel();
        $data['Cuadrante'] = $model->find($id);

        return view('cuadrantes/edit', $data);
    }

    //Actualizar
    public function update($id)
    {
        $model = new CuadranteModel();

        $data = [
            'nombre'        => $this->request->getPost('nombre'),
            'localidad'     => $this->request->getPost('localidad'),
            'barrios'       => $this->request->getPost('barrios'),
            'precio'        => $this->request->getPost('precio'),
            'coords_json'   => $this->request->getPost('coords_json'),
            'estado'        => $this->request->getPost('estado'),
        ];

        $model->update($id, $data);

        return redirect()->to('/cuadrantes')->with('success', 'Cuadrante actualizado correctamente.');
    }

    //Eliminar
    public function delete($id)
    {
        $model = new CuadranteModel();
        $model->delete($id);

        return redirect()->to('/cuadrantes')->with('success', 'Cuadrante eliminado correctamente.');
    }
}
