<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>AppDomicilios - Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="css/style.css">

  <style>
    body {
      height: 100vh;
      margin: 0;
      display: flex;
      justify-content: center;
      align-items: center;
      background: 
        linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)),
        url("<?= base_url('images/login-hero.png') ?>") no-repeat center center fixed;
      background-size: cover;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .login-card {
      width: 100%;
      max-width: 450px;
      padding: 2.5rem;
      border-radius: 1.5rem;
      background: rgba(255, 255, 255, 0.85);
      backdrop-filter: blur(10px);
      box-shadow: 0 10px 30px rgba(0,0,0,0.5);
      color: #333;
      text-align: center;
    }

    .app-title {
      font-size: 2.2rem;
      font-weight: 700;
      margin-bottom: 1.5rem;
      color: #ff4500;
    }

    .form-label {
      color: #444;
      font-weight: 500;
    }

    .form-control {
      border-radius: 10px;
      background: #fff;
    }

    .form-control:focus {
      box-shadow: 0 0 10px rgba(255, 69, 0, 0.5);
      border-color: #ff4500;
    }

    .btn-brand {
      background-color: #ff4500;
      border: none;
      border-radius: 10px;
      color: #fff;
      transition: all 0.3s ease;
      font-weight: 600;
    }

    .btn-brand:hover {
      background-color: #e03d00;
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(255,69,0,0.4);
    }

    .nav-tabs {
      border: none;
      justify-content: center;
    }

    .nav-tabs .nav-link {
      border: none;
      color: #555;
      font-weight: 500;
      margin: 0 5px;
      border-radius: 8px;
      padding: 10px 20px;
      transition: background 0.3s;
    }

    .nav-tabs .nav-link.active {
      background-color: #ff4500;
      color: #fff !important;
    }

    .tab-content {
      margin-top: 1.5rem;
      text-align: left;
    }

    a {
      color: #ff4500;
      font-weight: 500;
    }

    a:hover {
      color: #e03d00;
    }
  </style>
</head>
<body>

  <div class="login-card">
    <h1 class="app-title"> <i class="fa-solid fa-motorcycle"></i> AppDomicilios</h1>

    <!-- Tabs -->
    <ul class="nav nav-tabs" id="loginTabs" role="tablist">
      <li class="nav-item" role="presentation">
        <button class="nav-link active" id="login-tab" data-bs-toggle="tab" data-bs-target="#login" type="button" role="tab">Iniciar Sesión</button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="register-tab" data-bs-toggle="tab" data-bs-target="#register" type="button" role="tab">Crear Cuenta</button>
      </li>
    </ul>

    <!-- Contenido Tabs -->
    <div class="tab-content">

      <!-- LOGIN -->
      <div class="tab-pane fade show active" id="login" role="tabpanel">
        <form method="post" action="<?= base_url('/auth/login') ?>">
          <?= csrf_field() ?>

          <div class="mb-3">
            <label class="form-label">Correo electrónico</label>
            <div class="input-group">
              <span class="input-group-text"><i class="fa-solid fa-envelope"></i></span>
              <input type="email" class="form-control" name="email" placeholder="ejemplo@correo.com" required>
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label">Contraseña</label>
            <div class="input-group">
              <span class="input-group-text"><i class="fa-solid fa-lock"></i></span>
              <input type="password" class="form-control" name="password" placeholder="********" required>
            </div>
          </div>

          <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="remember" name="remember">
              <label class="form-check-label" for="remember">Recuérdame</label>
            </div>
            <a href="#" class="text-decoration-none">¿Olvidaste tu contraseña?</a>
          </div>

          <button type="submit" class="btn btn-brand w-100 mt-2">
            <i class="fa-solid fa-sign-in-alt me-2"></i>Ingresar
          </button>
        </form>
      </div>

      <!-- REGISTRO -->
      <div class="tab-pane fade" id="register" role="tabpanel">
        <form method="post" action="<?= base_url('/auth/register') ?>"> <?= csrf_field() ?>
          <div class="mb-3">
            <label class="form-label">Nombre completo</label>
            <div class="input-group">
              <span class="input-group-text"><i class="fa-solid fa-user"></i></span>
              <input type="text" class="form-control" name="name" placeholder="Tu nombre completo" required>
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label">Correo electrónico</label>
            <div class="input-group">
              <span class="input-group-text"><i class="fa-solid fa-envelope"></i></span>
              <input type="email" class="form-control" name="email" placeholder="ejemplo@correo.com" required>
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label">Contraseña</label>
            <div class="input-group">
              <span class="input-group-text"><i class="fa-solid fa-lock"></i></span>
              <input type="password" class="form-control" name="password" placeholder="********" required>
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label">Confirmar contraseña</label>
            <div class="input-group">
              <span class="input-group-text"><i class="fa-solid fa-lock"></i></span>
              <input type="password" class="form-control" name="password_confirm" placeholder="********" required>
            </div>
          </div>

          <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" id="terms" required>
            <label class="form-check-label" for="terms">
              Acepto los <a href="#">términos y condiciones</a>
            </label>
          </div>

          <button type="submit" class="btn btn-brand w-100 mt-2">
            <i class="fa-solid fa-user-plus me-2"></i>Registrarme
          </button>
        </form>
      </div>

    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

