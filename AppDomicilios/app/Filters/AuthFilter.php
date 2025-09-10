<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Si el usuario NO está logueado, lo redirigimos al login
        if (!session()->get('logged_in')) {
            return redirect()->to('/auth/login')->with('error', 'Debes iniciar sesión primero.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Aquí puedes poner lógica que se ejecute después de cargar la página
        // Ejemplo: evitar que usuarios logueados vuelvan al login
        if (session()->get('logged_in') && (current_url() === base_url('/auth/login'))) {
            return redirect()->to('/domiciliarios');
        }
    }
}
