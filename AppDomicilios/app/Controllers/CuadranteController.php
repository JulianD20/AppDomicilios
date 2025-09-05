<?php
namespace App\Controllers;

use App\Controllers\BaseController;

class CuadranteController extends BaseController
{
    public function index()
    {
        return view('cuadrantes/index');
    }

    public function create()
    {
        return view('cuadrantes/create');
    }
}
