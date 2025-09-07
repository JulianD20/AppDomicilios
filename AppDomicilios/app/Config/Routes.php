<?php

use CodeIgniter\Router\RouteCollection;


/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'CuadranteController::index');

// --------------------------------------------------------------------
// Rutas para Cuadrantes
// --------------------------------------------------------------------
$routes->get('/cuadrantes', 'CuadranteController::index');
$routes->get('/cuadrantes/create', 'CuadranteController::create');
$routes->post('/cuadrantes/store', 'CuadranteController::store');

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
$routes->get('/pedidos/factura/(:num)', 'PedidoController::factura/$1');

