<!-- app/Views/layouts/app.php -->
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title><?= isset($title) ? $title : 'App Domicilios' ?></title>

  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Leaflet -->
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

  <!-- Font Awesome + Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

  <style>
    :root {
      --brand: #FF6B00;
      --dark: #0F1724;
      --bg: #F8FAFC;
      --card-shadow: 0 8px 20px rgba(15, 23, 36, 0.08);
    }

    body {
      background: var(--bg);
      color: #0b1220;
      font-family: Inter, system-ui, Arial;
      margin: 0;
      display: flex;
    }

    /* Sidebar */
    .sidebar {
      width: 70px;
      background: var(--dark);
      height: 100vh;
      position: fixed;
      top: 0;
      left: 0;
      transition: width 0.3s ease;
      overflow: hidden;
      z-index: 1000;
    }

    .sidebar:hover {
      width: 230px;
    }

    .sidebar .brand {
      color: var(--brand);
      font-weight: 700;
      font-size: 1.2rem;
      padding: 20px;
      white-space: nowrap;
    }

    /* User info */
    .user-info {
      display: flex;
      align-items: center;
      gap: 10px;
      color: white;
      padding: 14px 20px;
      font-weight: 500;
      white-space: nowrap;
    }

    .user-info i {
      font-size: 1.5rem;
      min-width: 28px;
      text-align: center;
      color: var(--brand);
    }

    .user-info span {
      display: block;
      max-width: 150px;
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
    }

    .sidebar ul {
      list-style: none;
      padding: 0;
      margin: 20px 0 0 0;
    }

    .sidebar li {
      margin-bottom: 12px;
    }

    .sidebar a {
      display: flex;
      align-items: center;
      gap: 14px;
      color: white;
      text-decoration: none;
      padding: 14px 20px;
      transition: background 0.2s;
      white-space: nowrap;
    }

    .sidebar a:hover {
      background: rgba(255, 255, 255, 0.08);
    }

    .sidebar i {
      font-size: 1.3rem;
      min-width: 28px;
      text-align: center;
    }

    /* Texto oculto cuando sidebar está colapsado */
    .sidebar span {
      opacity: 0;
      transition: opacity 0.2s ease;
    }

    .sidebar:hover span {
      opacity: 1;
    }

    /* Main content */
    .view-wrap {
      flex: 1;
      margin-left: 70px;
      padding: 28px;
      transition: margin-left 0.3s ease;
    }

    .sidebar:hover~.view-wrap {
      margin-left: 230px;
    }

    /* Cards */
    .card-glass {
      background: white;
      border: 0;
      border-radius: 12px;
      box-shadow: var(--card-shadow);
    }

    /* Map container */
    #map {
      height: 360px;
      border-radius: 10px;
      border: 1px solid rgba(0, 0, 0, 0.06);
    }

    /* Logout link at bottom */
    .logout-link {
      position: absolute;
      bottom: 20px;
      width: 100%;
    }

    .logout-link a {
      display: flex;
      align-items: center;
      gap: 14px;
      color: #ff4d4d;
      text-decoration: none;
      padding: 14px 20px;
      transition: background 0.2s;
      white-space: nowrap;
    }

    .logout-link a:hover {
      background: rgba(255, 77, 77, 0.15);
    }
  </style>
</head>

<body>
  
  <!-- Banners/Toasts/Modales de feedback -->
  <?= view('partials/feedback_alert') ?>
  <?= view('partials/feedback_toast') ?>
  <?= view('partials/feedback_modal') ?>

  <!-- Contenido -->
  <?= $this->renderSection('content') ?>

  <!-- Bootstrap 5 JS (bundle con Popper) y nuestro JS -->
  <script src="<?= base_url('assets/js/ui-bootstrap.js') ?>" defer></script>

  <!-- Sidebar -->
  <div class="sidebar">
    <div class="brand">
      <i class="fa-solid fa-motorcycle"></i> <span>AppDomicilios</span>
    </div>

    <!-- Info usuario logueado -->
    <div class="user-info">
      <i class="fa-solid fa-user-circle"></i>
      <span><?= session('name') ?></span>
    </div>

    <ul>
      <li><a href="/domiciliarios"><i class="fa-solid fa-user"></i><span>Domiciliarios</span></a></li>
      <li><a href="/cuadrantes"><i class="fa-solid fa-draw-polygon"></i><span>Cuadrantes</span></a></li>
      <li><a href="/pedidos"><i class="fa-solid fa-box"></i><span>Pedidos</span></a></li>
    </ul>

    <!-- Logout al final -->
    <div class="logout-link">
      <a href="<?= base_url('/auth/logout') ?>" class="text-danger">
        <i class="fa-solid fa-right-from-bracket"></i>
        <span>Cerrar sesión</span>
      </a>
    </div>
  </div>

  <!-- Main content -->
  <main class="view-wrap">
    <?= $content ?? '' ?>
  </main>

  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

  <?= isset($scripts) ? $scripts : '' ?>
</body>

</html>