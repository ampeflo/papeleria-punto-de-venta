<?php
/**
 * PUNTO DE ENTRADA (Front Controller)
 * Todas las peticiones llegan aquí gracias a .htaccess.
 *
 *   Vue  ->  /api/productos  ->  public/index.php  ->  Router  ->  Controller  ->  Model  ->  MySQL
 */

require __DIR__ . '/../app/bootstrap.php';

// 1. Registrar las rutas
$router = new Router();
require __DIR__ . '/../routes/api.php';

// 2. Obtener la ruta pedida, sin la carpeta del proyecto.
//    Ej.: /papeleria/backend/public/api/productos  ->  /api/productos
$url     = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$carpeta = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
$ruta    = '/' . trim(substr($url, strlen($carpeta)), '/');

// 3. Ejecutar el controlador que corresponde
$router->despachar($_SERVER['REQUEST_METHOD'], $ruta);
