<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - Sistema Domiciliarios</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

  <style>
    body {
      height: 100vh;
      margin: 0;
      display: flex;
      justify-content: center;
      align-items: center;
      background: linear-gradient(135deg, #ff7f50, #ff4500); /* Naranja cálido */
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .login-card {
      width: 100%;
      max-width: 950px;
      display: flex;
      overflow: hidden;
      border-radius: 1.5rem;
      box-shadow: 0 10px 25px rgba(0,0,0,0.3);
      background: #fff;
    }

    .login-image {
    flex: 1;
    display: flex;
    justify-content: center;
    align-items: center;
    background-color: #fff;       
    border-radius: 4rem 0 0 4rem; 
    padding: 2rem;                
    }

    .login-image::before {
    content: "";
    background: url("<?= base_url('images/login-hero.png') ?>") no-repeat center center;
    background-size: contain;   
    width: 100%;
    height: 100%;
    border-radius: 4rem;        
    }


    .login-form {
      flex: 1;
      padding: 3rem;
      display: flex;
      flex-direction: column;
      justify-content: center;
    }

    .form-control, .btn {
      border-radius: 10px;
    }

    .btn-brand {
      background-color: #ff4500;
      border: none;
      color: #fff;
      transition: all 0.3s ease;
    }

    .btn-brand:hover {
      background-color: #e03d00;
    }

    /* Responsive: en móviles solo se muestra el formulario */
    @media (max-width: 768px) {
      .login-card {
        flex-direction: column;
      }
      .login-image {
        display: none;
      }
    }
  </style>
</head>
<body>

  <div class="login-card">
    <!-- Imagen     -->
    <div class="login-image"></div>

    <!-- Formulario -->
    <div class="login-form">
      <h2 class="mb-4 text-center">Bienvenido 👋</h2>
      <p class="text-muted text-center mb-4">Inicia sesión para acceder al sistema</p>

      <form method="post" action="<?= base_url('/login') ?>">
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

        <button type="submit" class="btn btn-brand w-100 mt-3">
          <i class="fa-solid fa-sign-in-alt me-2"></i>Ingresar
        </button>
      </form>

      <p class="text-center mt-4 text-muted">
        ¿Olvidaste tu contraseña? <a href="#" class="text-decoration-none">Recupérala aquí</a>
      </p>
    </div>
  </div>

</body>
</html>


