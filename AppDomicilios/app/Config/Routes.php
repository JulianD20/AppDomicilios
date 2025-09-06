<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Cuadrantes');   // controlador WEB (vistas)
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
// $routes->setAutoRoute(false); // recomendado en prod

// --------------------------------------------------
// Rutas WEB (HTML)
// --------------------------------------------------
$routes->get('/',                 'Cuadrantes::index');
$routes->get('cuadrantes',        'Cuadrantes::index');
$routes->get('domiciliarios',        'Domiciliarios::index');
$routes->get('pedidos',        'Pedidos::index');

$routes->get ('cuadrantes/create', 'Cuadrantes::create');
$routes->post('cuadrantes/store',  'Cuadrantes::store');

$routes->get ('domiciliarios/create',  'Domiciliarios::create');
$routes->post('domiciliarios/store',   'Domiciliarios::store');

$routes->get ('pedidos/create',        'Pedidos::create');
$routes->post('pedidos/store',         'Pedidos::store');
$routes->get ('pedidos/factura/(:num)','Pedidos::factura/$1');

// --------------------------------------------------
// Rutas API (JSON) bajo /api
// --------------------------------------------------
$routes->group('api', ['namespace' => 'App\Controllers\Api'], static function ($routes) {

    // Salud de BD
    $routes->get('health/db',     'Health::db');

    // Cuadrantes (API)
    $routes->get   ('cuadrantes',           'CuadranteController::index');
    $routes->get   ('cuadrantes/(:num)',    'CuadranteController::show/$1');
    $routes->post  ('cuadrantes',           'CuadranteController::create');
    $routes->put   ('cuadrantes/(:num)',    'CuadranteController::update/$1');
    $routes->patch ('cuadrantes/(:num)',    'CuadranteController::update/$1');
    $routes->delete('cuadrantes/(:num)',    'CuadranteController::delete/$1');

    // Domiciliarios (API)  -> controladores: App\Controllers\Api\Domiciliarios
    $routes->get   ('domiciliarios',        'Domiciliarios::index');
    $routes->get   ('domiciliarios/(:num)', 'Domiciliarios::show/$1');
    $routes->post  ('domiciliarios',        'Domiciliarios::create');
    $routes->put   ('domiciliarios/(:num)', 'Domiciliarios::update/$1');
    $routes->patch ('domiciliarios/(:num)', 'Domiciliarios::update/$1');
    $routes->delete('domiciliarios/(:num)', 'Domiciliarios::delete/$1');

    // Pedidos (API)
    $routes->get   ('pedidos',              'Pedidos::index');
    $routes->get   ('pedidos/(:num)',       'Pedidos::show/$1');
    $routes->post  ('pedidos',              'Pedidos::create');
    $routes->put   ('pedidos/(:num)',       'Pedidos::update/$1');
    $routes->patch ('pedidos/(:num)',       'Pedidos::update/$1');
    $routes->delete('pedidos/(:num)',       'Pedidos::delete/$1');

    // Pagos (solo lectura en API)
    $routes->get   ('pagos',                'Pagos::index');
    $routes->get   ('pagos/(:num)',         'Pagos::show/$1');

    // Liquidaciones via SP
    $routes->get   ('preview',              'Liquidaciones::preview');
    $routes->post  ('liquidar',             'Liquidaciones::generar');
    $routes->delete('liquidar/(:segment)',  'Liquidaciones::borrar/$1');

    // Historial (vista materializada de pagos)
    $routes->get   ('historial',            'Historial::index');
});
