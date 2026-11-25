<?php
/**
 * Modelo Reporte: consultas de resumen que se muestran en el Panel.
 */

class Reporte
{
    /** Número de ventas y total vendido hoy. */
    public static function ventasHoy(): array
    {
        return Database::conexion()->query(
            'SELECT COUNT(*) AS num_ventas, COALESCE(SUM(total), 0) AS total
             FROM vista_ventas
             WHERE DATE(fecha) = CURDATE()'
        )->fetch();
    }

    /** JOIN + GROUP BY + LIMIT: los 5 productos más vendidos. */
    public static function topProductos(): array
    {
        return Database::conexion()->query(
            'SELECT p.nombre, SUM(d.cantidad) AS unidades
             FROM detalle_venta d
             JOIN productos p ON p.id_producto = d.id_producto
             GROUP BY p.id_producto, p.nombre
             ORDER BY unidades DESC
             LIMIT 5'
        )->fetchAll();
    }

    /** Productos con stock igual o menor al mínimo. */
    public static function stockBajo(): array
    {
        return Database::conexion()->query(
            'SELECT codigo, nombre, stock, stock_minimo
             FROM productos
             WHERE stock <= stock_minimo
             ORDER BY stock'
        )->fetchAll();
    }

    /** SUBCONSULTA (NOT IN): productos que nunca se han vendido. */
    public static function sinVentas(): array
    {
        return Database::conexion()->query(
            'SELECT codigo, nombre, stock
             FROM productos
             WHERE id_producto NOT IN (SELECT id_producto FROM detalle_venta)
             ORDER BY nombre'
        )->fetchAll();
    }
}
