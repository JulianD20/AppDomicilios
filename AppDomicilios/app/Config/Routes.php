<?php

use CodeIgniter\Router\RouteCollection;


/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'CuadranteController::index');

// --------------------------------------------------------------------
// Rutas para el Auth
// --------------------------------------------------------------------
$routes->get('/auth/login', 'AuthController::login');

// --------------------------------------------------------------------
// Rutas para Cuadrantes
// --------------------------------------------------------------------
$routes->get('/cuadrantes', 'CuadranteController::index');
$routes->get('/cuadrantes/create', 'CuadranteController::create');
$routes->post('/cuadrantes/store', 'CuadranteController::store');
$routes->get('/cuadrantes/edit/(:num)', 'CuadranteController::edit/$1');
$routes->put('/cuadrantes/update/(:num)', 'CuadranteController::update/$1');
$routes->delete('/cuadrantes/delete/(:num)', 'CuadranteController::delete/$1');
$routes->get('/domiciliarios/show/(:num)', 'DomiciliarioController::show/$1');

// --------------------------------------------------------------------
// Rutas para Domiciliarios
// --------------------------------------------------------------------
$routes->get('/domiciliarios', 'DomiciliarioController::index');
$routes->get('/domiciliarios/create', 'DomiciliarioController::create');
$routes->post('/domiciliarios/store', 'DomiciliarioController::store');
$routes->get('/domiciliarios/edit/(:num)', 'DomiciliarioController::edit/$1');
$routes->put('/domiciliarios/update/(:num)', 'DomiciliarioController::update/$1');
$routes->delete('/domiciliarios/delete/(:num)', 'DomiciliarioController::delete/$1');

// --------------------------------------------------------------------
// Rutas para Pedidos (asignar pedido + factura)
// --------------------------------------------------------------------
$routes->get('/pedidos', 'PedidoController::index');
$routes->get('/pedidos/create', 'PedidoController::create');
$routes->post('/pedidos/store', 'PedidoController::store');
$routes->get('/pedidos/edit/(:num)', 'PedidoController::edit/$1');
$routes->post('/pedidos/update/(:num)', 'PedidoController::update/$1');
$routes->delete('/pedidos/delete/(:num)', 'PedidoController::delete/$1');
$routes->get('/pedidos/factura/(:num)', 'PedidoController::factura/$1');
$routes->get('/pedidos/factura-dia', 'PedidoController::facturaDia');
$routes->post('/pedidos/pagar-dia', 'PedidoController::pagarDia');



