<?php
namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\DomiciliarioModel;

class DomiciliarioController extends BaseController
{
    public function index()
    {
        // Intentamos cargar datos reales desde el modelo; si no hay BD, usamos ejemplo.
        // $data = [];
        // try {
        //     $model = new DomiciliarioModel();
        //     $items = $model->findAll();
        //     if (empty($items)) {
        //         // fallback si tabla vacía
        //         $items = [
        //             ['id'=>1,'nombre'=>'Juan Pérez','telefono'=>'3001112222','cedula'=>'12345678']
        //         ];
        //     }
        // } catch(\Throwable $e) {
        //     // fallback si no existe la BD / tabla
        //     $items = [
        //         ['id'=>1,'nombre'=>'Juan Pérez','telefono'=>'3001112222','cedula'=>'12345678']
        //     ];
        // }
        return view('domiciliarios/index'/*,['domiciliarios' => $items]*/);
    }

    public function create()
    {
        return view('domiciliarios/create');
    }

    // public function store()
    // {
    //     // Guardar si existe modelo; si no, redirigir con flash message.
    //     try {
    //         $model = new DomiciliarioModel();
    //         $model->insert([
    //             'nombre' => $this->request->getPost('nombre'),
    //             'telefono' => $this->request->getPost('telefono'),
    //             'cedula' => $this->request->getPost('cedula'),
    //         ]);
    //         session()->setFlashdata('msg', 'Domiciliario creado');
    //     } catch(\Throwable $e) {
    //         session()->setFlashdata('msg', 'No se pudo crear (sin BD): ' . $e->getMessage());
    //     }
    //     return redirect()->to('/domiciliarios');
    // }
}
