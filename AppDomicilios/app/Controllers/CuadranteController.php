<?php
namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CuadranteModel;

class CuadranteController extends BaseController
{
    // Listar con filtros y paginación
    public function index()
    {
        $req = $this->request;

        $q         = trim((string) $req->getGet('q'));
        $estado    = (string) $req->getGet('estado');         // Activo | Inactivo | ''
        $pmin      = $req->getGet('pmin');                    // precio mínimo
        $pmax      = $req->getGet('pmax');                    // precio máximo
        $perPage   = (int) ($req->getGet('per_page') ?? 10);

        $model = new CuadranteModel();

        if ($q !== '') {
            $model->groupStart()
                ->like('nombre', $q)
                ->orLike('localidad', $q)
                ->orLike('barrios', $q)
                ->groupEnd();
        }

        if ($estado !== '') {
            $model->where('estado', $estado);
        }

        if ($pmin !== null && $pmin !== '' && is_numeric($pmin)) {
            $model->where('precio >=', (float)$pmin);
        }
        if ($pmax !== null && $pmax !== '' && is_numeric($pmax)) {
            $model->where('precio <=', (float)$pmax);
        }

        $model->orderBy('id', 'DESC');

        $data['cuadrantes'] = $model->paginate($perPage);
        $data['pager']      = $model->pager;
        $data['filters']    = [
            'q'       => $q,
            'estado'  => $estado,
            'pmin'    => $pmin,
            'pmax'    => $pmax,
            'perPage' => $perPage,
        ];

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
        helper('feedback');
        $coords = $this->request->getPost('coords_json');

        if ($coords === null || trim($coords) === '') {
            return redirect()->back()->withInput()
                ->with('error', 'Debes definir el polígono (coords_json no puede estar vacío).');
        }

        // valida que sea JSON y que sea un array de puntos
        $decoded = json_decode($coords, true);
        if (!is_array($decoded) || count($decoded) < 3) {
            return redirect()->back()->withInput()
                ->with('error', 'coords_json debe ser JSON válido con al menos 3 puntos.');
        }

        $model = new CuadranteModel();

        $data = [
            'nombre'        => $this->request->getPost('nombre'),
            'localidad'     => $this->request->getPost('localidad'),
            'barrios'       => $this->request->getPost('barrios'),
            'precio'        => $this->request->getPost('precio'),
            'coords_json'   => $coords,
            'estado'        => $this->request->getPost('estado'),
        ];

        $model->save($data);
        flash_guardado('El cuadrante se guardó correctamente.', null, 'toast'); 
        return redirect()->to('/cuadrantes')->with('success', 'Cuadrante creado correctamente.');
    }

    //Editar
    public function edit($id)
    {
        $model = new CuadranteModel();
        $data['cuadrante'] = $model->find($id);

        return view('cuadrantes/edit', $data);
    }

    //Actualizar
    public function update($id)
    {
        helper('feedback');        
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
        flash_editado('Actualizamos la información del cuadrante.', null, 'alert');
        return redirect()->to('/cuadrantes')->with('success', 'Cuadrante actualizado correctamente.');
    }

    //Eliminar
    public function delete($id)
    {
        helper('feedback');
        $model = new CuadranteModel();
        $model->delete($id);
        flash_eliminado('El cuadrante fue eliminado del sistema.', null, 'modal');
        return redirect()->to('/cuadrantes')->with('success', 'Cuadrante eliminado correctamente.');
    }

    public function mapa()
    {
        $model = new \App\Models\CuadranteModel();
        $cuadrantes = $model->findAll();

        return view('cuadrantes/mapa', compact('cuadrantes'));
    }
}
