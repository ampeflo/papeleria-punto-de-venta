<?php
/**
 * Modelo Categoria: consultas de la tabla categorias.
 */

class Categoria
{
    /** LEFT JOIN + GROUP BY: cada categoría con su número de productos (incluye las vacías). */
    public static function todas(): array
    {
        $sql = 'SELECT c.id_categoria, c.nombre, COUNT(p.id_producto) AS num_productos
                FROM categorias c
                LEFT JOIN productos p ON p.id_categoria = c.id_categoria
                GROUP BY c.id_categoria, c.nombre
                ORDER BY c.nombre';
        return Database::conexion()->query($sql)->fetchAll();
    }

    public static function crear(string $nombre): void
    {
        Database::conexion()->prepare('INSERT INTO categorias (nombre) VALUES (?)')->execute([$nombre]);
    }

    public static function actualizar(int $id, string $nombre): void
    {
        Database::conexion()->prepare('UPDATE categorias SET nombre = ? WHERE id_categoria = ?')->execute([$nombre, $id]);
    }

    /** Si tiene productos, MySQL lo impide por la llave foránea (error 1451). */
    public static function eliminar(int $id): void
    {
        Database::conexion()->prepare('DELETE FROM categorias WHERE id_categoria = ?')->execute([$id]);
    }
}
