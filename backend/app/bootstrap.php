<?php
/**
 * Arranque de la aplicación: configuración, carga de clases,
 * sesión y manejo de errores. Lo incluye public/index.php.
 */

$config = require __DIR__ . '/../config/config.php';
date_default_timezone_set($config['zona_horaria']);

header('Content-Type: application/json; charset=utf-8');
ini_set('display_errors', '0');
session_start();

/**
 * Carga automática de clases: cuando se usa "new ProductoController()"
 * PHP busca el archivo ProductoController.php en estas carpetas.
 */
spl_autoload_register(function (string $clase) {
    foreach (['Core', 'Models', 'Controllers', 'Middleware'] as $carpeta) {
        $archivo = __DIR__ . "/$carpeta/$clase.php";
        if (file_exists($archivo)) {
            require $archivo;
            return;
        }
    }
});

/**
 * Manejo de errores: cualquier error se convierte en un mensaje claro en JSON.
 */
set_exception_handler(function (Throwable $e) {
    $codigo = ($e instanceof PDOException) ? ($e->errorInfo[1] ?? (int) $e->getCode()) : 0;

    switch ($codigo) {
        case 1062: Response::error('Ya existe un registro con ese dato (duplicado).', 409);
        case 1451: Response::error('No se puede eliminar porque está siendo usado en otros registros.', 409);
        case 1452: Response::error('El registro relacionado no existe (por ejemplo, la categoría).', 422);
        case 3819: Response::error('Los datos no cumplen las reglas de la base de datos.', 422);
        case 1045:
        case 1049:
        case 2002: Response::error('No hay conexión con la base de datos. ¿Está encendido WampServer?', 503);
    }

    error_log($e->getMessage());
    Response::error('Ocurrió un error en el servidor.', 500);
});
