<?php
namespace App\Controllers;

use App\Models\DomiciliarioModel;

class Domiciliarios extends BaseController
{
    public function index()
    {
        $rows = (new DomiciliarioModel())
            ->orderBy('id', 'DESC')
            ->findAll();

        return view('domiciliarios/index', ['domiciliarios' => $rows]);
    }

    // GET /domiciliarios/create
    public function create()
    {
        return view('domiciliarios/create');
    }

    // POST /domiciliarios/store
    public function store()
    {
        $data = [
            'nombre'        => trim($this->request->getPost('nombre')),
            'telefono'      => trim($this->request->getPost('telefono')) ?: null,
            // 'estado' del formulario -> mapeo a activo 1/0
            'cedula'        => trim($this->request->getPost('cedula')),     // <—
            'activo'        => (int) ($this->request->getPost('estado') === 'Activo'),
            // si no se envía, la BD tiene DEFAULT (CURRENT_DATE)
            'fecha_ingreso' => $this->request->getPost('fecha_ingreso') ?: date('Y-m-d'),
        ];

        // Validación mínima
        if ($data['nombre'] === '' || $data['cedula'] === '') {
            return redirect()->back()->with('error', 'Nombre y cédula son obligatorios')->withInput();
        }

        try {
            $model = new \App\Models\DomiciliarioModel();
            if (!$model->insert($data)) {
                return redirect()->back()->with('error', implode('; ', $model->errors()))->withInput();
            }
            return redirect()->to('/domiciliarios')->with('success', 'Domiciliario creado');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }
}
