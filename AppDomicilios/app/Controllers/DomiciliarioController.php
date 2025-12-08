<?php
namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\DomiciliarioModel;

class DomiciliarioController extends BaseController
{
    // Listar con filtros y paginación
    public function index()
    {
        $request = $this->request;

        $q      = trim((string) $request->getGet('q'));
        $estado = (string) $request->getGet('estado');         
        $desde  = (string) $request->getGet('desde');        
        $hasta  = (string) $request->getGet('hasta');       
        $perPage = (int) ($request->getGet('per_page') ?? 10); 

        $model = new DomiciliarioModel();

        // Construcción de filtros
        if ($q !== '') {
            $model->groupStart()
                ->like('nombre', $q)
                ->orLike('telefono', $q)
                ->orLike('cedula', $q)
                ->groupEnd();
        }

        if ($estado !== '') {
            $model->where('estado', $estado);
        }

        if ($desde !== '') {
            $model->where('DATE(fecha_ingreso) >=', $desde);
        }

        if ($hasta !== '') {
            $model->where('DATE(fecha_ingreso) <=', $hasta);
        }

        $model->orderBy('fecha_ingreso', 'DESC');

        $data['domiciliarios'] = $model->paginate($perPage);
        $data['pager']         = $model->pager;

        // Para re-poblar el form y mostrar chips activos
        $data['filters'] = compact('q','estado','desde','hasta','perPage');

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
