<?php
namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;

class AuthController extends BaseController
{
    // Mostrar vista de login/registro
    public function login()
    {
        return view('auth/login');
    }

    // Registrar usuario
    public function register()
    {
        $userModel = new UserModel();

        $data = [
            'name'     => $this->request->getPost('name'),
            'email'    => $this->request->getPost('email'),
            'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
        ];

        // Validación simple
        if ($this->request->getPost('password') !== $this->request->getPost('password_confirm')) {
            return redirect()->back()->with('error', 'Las contraseñas no coinciden.');
        }

        if (!$userModel->save($data)) {
            return redirect()->back()->with('error', 'Error al crear usuario.')->withInput();
        }

        return redirect()->to('/auth/login')->with('success', 'Cuenta creada correctamente. Ahora puedes iniciar sesión.');
    }

    // Procesar login
    public function doLogin()
    {
        $userModel = new UserModel();

        $email    = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        $user = $userModel->where('email', $email)->first();

        if ($user && password_verify($password, $user['password'])) {
            // Guardamos sesión
            session()->set([
                'user_id' => $user['id'],
                'name'    => $user['name'],
                'email'   => $user['email'],
                'logged_in' => true,
            ]);
            return redirect()->to('/domiciliarios');
        }

        return redirect()->back()->with('error', 'Credenciales inválidas.');
    }

    // Cerrar sesión
    public function logout()
    {
        session()->destroy();
        return redirect()->to('/auth/login');
    }
}

