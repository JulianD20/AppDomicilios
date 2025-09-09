<?php
namespace App\Controllers;

use App\Controllers\BaseController;

class AuthController extends BaseController
{
    // Mostrar vista de login
    public function login()
    {
        return view('auth/login');
    }
}
