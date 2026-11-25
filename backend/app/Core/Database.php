<?php
/**
 * Conexión única a MySQL con PDO.
 * Los modelos la obtienen con Database::conexion().
 */

class Database
{
    private static ?PDO $pdo = null;

    public static function conexion(): PDO
    {
        if (self::$pdo === null) {
            $c = require __DIR__ . '/../../config/config.php';

            self::$pdo = new PDO(
                "mysql:host={$c['db_host']};dbname={$c['db_name']};charset=utf8mb4",
                $c['db_user'],
                $c['db_pass'],
                [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // errores como excepciones
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // filas como arreglos
                ]
            );
        }
        return self::$pdo;
    }
}
