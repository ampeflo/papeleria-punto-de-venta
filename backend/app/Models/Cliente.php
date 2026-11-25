<?php
/**
 * Modelo Cliente: consultas de la tabla clientes.
 */

class Cliente
{
    /** Clientes con su número de compras (subconsulta). */
    public static function todos(string $buscar = ''): array
    {
        $sql = 'SELECT c.*,
                       (SELECT COUNT(*) FROM ventas v WHERE v.id_cliente = c.id_cliente) AS num_compras
                FROM clientes c
                WHERE c.nombre LIKE ? OR c.telefono LIKE ?
                ORDER BY c.nombre';
        $stmt = Database::conexion()->prepare($sql);
        $stmt->execute(["%$buscar%", "%$buscar%"]);
        return $stmt->fetchAll();
    }

    public static function crear(array $d): void
    {
        Database::conexion()
            ->prepare('INSERT INTO clientes (nombre, telefono, email) VALUES (?, ?, ?)')
            ->execute([$d['nombre'], $d['telefono'], $d['email']]);
    }

    public static function actualizar(int $id, array $d): void
    {
        Database::conexion()
            ->prepare('UPDATE clientes SET nombre = ?, telefono = ?, email = ? WHERE id_cliente = ?')
            ->execute([$d['nombre'], $d['telefono'], $d['email'], $id]);
    }

    /** Si el cliente tiene compras, la llave foránea lo impide (error 1451). */
    public static function eliminar(int $id): void
    {
        Database::conexion()->prepare('DELETE FROM clientes WHERE id_cliente = ?')->execute([$id]);
    }
}
