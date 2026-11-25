<?php
/**
 * Modelo Venta: consultas de ventas y detalle_venta.
 */

class Venta
{
    /** Historial entre dos fechas (usa la vista que calcula el total). */
    public static function entreFechas(string $desde, string $hasta): array
    {
        $stmt = Database::conexion()->prepare(
            'SELECT * FROM vista_ventas WHERE DATE(fecha) BETWEEN ? AND ? ORDER BY fecha DESC'
        );
        $stmt->execute([$desde, $hasta]);
        return $stmt->fetchAll();
    }

    /** Una venta con sus productos (JOIN), para el ticket. */
    public static function buscar(int $id): ?array
    {
        $db = Database::conexion();

        $stmt = $db->prepare('SELECT * FROM vista_ventas WHERE id_venta = ?');
        $stmt->execute([$id]);
        $venta = $stmt->fetch();
        if (!$venta) return null;

        $stmt = $db->prepare(
            'SELECT p.nombre, d.cantidad, d.precio_unitario, d.cantidad * d.precio_unitario AS importe
             FROM detalle_venta d
             JOIN productos p ON p.id_producto = d.id_producto
             WHERE d.id_venta = ?'
        );
        $stmt->execute([$id]);
        $venta['productos'] = $stmt->fetchAll();
        return $venta;
    }

    /**
     * Registra una venta en una TRANSACCIÓN:
     * se guarda todo (venta + detalle + stock) o no se guarda nada.
     * Devuelve [id_venta, total].
     */
    public static function crear(int $idUsuario, ?int $idCliente, string $metodo, array $productos): array
    {
        $db = Database::conexion();
        $db->beginTransaction();

        try {
            // 1. Encabezado de la venta
            $db->prepare('INSERT INTO ventas (id_usuario, id_cliente, metodo_pago) VALUES (?, ?, ?)')
               ->execute([$idUsuario, $idCliente, $metodo]);
            $idVenta = (int) $db->lastInsertId();

            // FOR UPDATE bloquea el producto para que dos cajas no vendan la última pieza a la vez
            $buscar  = $db->prepare('SELECT nombre, precio, stock FROM productos WHERE id_producto = ? FOR UPDATE');
            $detalle = $db->prepare('INSERT INTO detalle_venta (id_venta, id_producto, cantidad, precio_unitario) VALUES (?, ?, ?, ?)');
            $stock   = $db->prepare('UPDATE productos SET stock = stock - ? WHERE id_producto = ?');
            $total   = 0;

            foreach ($productos as $item) {
                $idProducto = (int) ($item['id_producto'] ?? 0);
                $cantidad   = (int) ($item['cantidad'] ?? 0);

                // 2. Revisar que exista y que haya stock suficiente
                $buscar->execute([$idProducto]);
                $p = $buscar->fetch();
                if (!$p || $cantidad <= 0) {
                    throw new InvalidArgumentException('Hay un producto no válido en la venta.');
                }
                if ($p['stock'] < $cantidad) {
                    throw new InvalidArgumentException("Stock insuficiente de \"{$p['nombre']}\": solo hay {$p['stock']}.");
                }

                // 3. Guardar el renglón con el precio actual y descontar el stock
                $detalle->execute([$idVenta, $idProducto, $cantidad, $p['precio']]);
                $stock->execute([$cantidad, $idProducto]);
                $total += $p['precio'] * $cantidad;
            }

            $db->commit();       // todo bien: se guardan los cambios
            return [$idVenta, $total];

        } catch (Throwable $e) {
            $db->rollBack();     // algo falló: se deshace todo
            throw $e;
        }
    }
}
