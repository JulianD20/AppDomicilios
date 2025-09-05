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
        return view('pedidos/index');
    }

    public function create()
    {
        return view('pedidos/create');
    }
}
