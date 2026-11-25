<?php
/**
 * Modelo Producto: consultas de la tabla productos.
 */

class Producto
{
    /** Lista de productos con el nombre de su categoría (JOIN). Permite buscar y filtrar. */
    public static function todos(string $buscar = '', int $categoria = 0): array
    {
        $sql = 'SELECT p.*, c.nombre AS categoria
                FROM productos p
                JOIN categorias c ON c.id_categoria = p.id_categoria
                WHERE (p.nombre LIKE ? OR p.codigo LIKE ?)';
        $params = ["%$buscar%", "%$buscar%"];

        if ($categoria > 0) {
            $sql .= ' AND p.id_categoria = ?';
            $params[] = $categoria;
        }

        $stmt = Database::conexion()->prepare($sql . ' ORDER BY p.nombre');
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function crear(array $d): void
    {
        $sql = 'INSERT INTO productos (codigo, nombre, id_categoria, precio, stock, stock_minimo)
                VALUES (?, ?, ?, ?, ?, ?)';
        Database::conexion()->prepare($sql)->execute([
            $d['codigo'], $d['nombre'], $d['id_categoria'], $d['precio'], $d['stock'], $d['stock_minimo'],
        ]);
    }

    public static function actualizar(int $id, array $d): void
    {
        $sql = 'UPDATE productos
                SET codigo = ?, nombre = ?, id_categoria = ?, precio = ?, stock = ?, stock_minimo = ?
                WHERE id_producto = ?';
        Database::conexion()->prepare($sql)->execute([
            $d['codigo'], $d['nombre'], $d['id_categoria'], $d['precio'], $d['stock'], $d['stock_minimo'], $id,
        ]);
    }

    /** Si el producto ya se vendió, la llave foránea lo impide (error 1451). */
    public static function eliminar(int $id): void
    {
        Database::conexion()->prepare('DELETE FROM productos WHERE id_producto = ?')->execute([$id]);
    }
}
