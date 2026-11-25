<?php
/**
 * Modelo Usuario: consultas de la tabla usuarios (con su rol).
 */

class Usuario
{
    /** Busca un usuario por su nombre de usuario, incluyendo el nombre del rol. */
    public static function buscarPorUsuario(string $usuario): ?array
    {
        $sql = 'SELECT u.id_usuario, u.nombre, u.usuario, u.password_hash, r.nombre AS rol
                FROM usuarios u
                JOIN roles r ON r.id_rol = u.id_rol
                WHERE u.usuario = ?';
        $stmt = Database::conexion()->prepare($sql);
        $stmt->execute([$usuario]);
        return $stmt->fetch() ?: null;
    }
}
