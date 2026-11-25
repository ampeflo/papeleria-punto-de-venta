<?php
/**
 * RUTAS DE LA API
 * Formato:  $router->metodo('/url', [Controlador::class, 'funcion'], 'middleware');
 *
 * Middleware:
 *   null    -> pública
 *   'login' -> cualquier usuario con sesión (Administrador o Cajero)
 *   'admin' -> solo Administrador
 */

// --- Sesión ---
$router->post('/api/login',  [AuthController::class, 'login']);
$router->post('/api/logout', [AuthController::class, 'logout']);
$router->get('/api/sesion',  [AuthController::class, 'sesion'], 'login');

// --- Panel (reportes) ---
$router->get('/api/panel', [PanelController::class, 'index'], 'login');

// --- Productos ---
$router->get('/api/productos',         [ProductoController::class, 'index'],   'login');
$router->post('/api/productos',        [ProductoController::class, 'store'],   'admin');
$router->put('/api/productos/{id}',    [ProductoController::class, 'update'],  'admin');
$router->delete('/api/productos/{id}', [ProductoController::class, 'destroy'], 'admin');

// --- Categorías ---
$router->get('/api/categorias',         [CategoriaController::class, 'index'],   'login');
$router->post('/api/categorias',        [CategoriaController::class, 'store'],   'admin');
$router->put('/api/categorias/{id}',    [CategoriaController::class, 'update'],  'admin');
$router->delete('/api/categorias/{id}', [CategoriaController::class, 'destroy'], 'admin');

// --- Clientes ---
$router->get('/api/clientes',         [ClienteController::class, 'index'],   'login');
$router->post('/api/clientes',        [ClienteController::class, 'store'],   'admin');
$router->put('/api/clientes/{id}',    [ClienteController::class, 'update'],  'admin');
$router->delete('/api/clientes/{id}', [ClienteController::class, 'destroy'], 'admin');

// --- Ventas ---
$router->get('/api/ventas',      [VentaController::class, 'index'], 'login');
$router->get('/api/ventas/{id}', [VentaController::class, 'show'],  'login');
$router->post('/api/ventas',     [VentaController::class, 'store'], 'login');
