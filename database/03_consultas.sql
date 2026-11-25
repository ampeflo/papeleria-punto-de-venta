-- =============================================================
--  Script 03: Consultas importantes (para la documentación)
--  Las consultas 1 a 5 son las que usa el sistema en el Panel.
-- =============================================================

USE papeleria_db;

-- 1. JOIN: ticket de una venta (ventas + usuarios + clientes + detalle + productos)
SELECT  v.id_venta, v.fecha, u.nombre AS cajero,
        COALESCE(c.nombre, 'Público en general') AS cliente,
        p.nombre AS producto, d.cantidad, d.precio_unitario,
        d.cantidad * d.precio_unitario AS importe
FROM ventas v
JOIN usuarios u       ON u.id_usuario  = v.id_usuario
LEFT JOIN clientes c  ON c.id_cliente  = v.id_cliente
JOIN detalle_venta d  ON d.id_venta    = v.id_venta
JOIN productos p      ON p.id_producto = d.id_producto
WHERE v.id_venta = 2;

-- 2. GROUP BY: total vendido por día
SELECT  DATE(v.fecha) AS dia,
        COUNT(DISTINCT v.id_venta) AS num_ventas,
        SUM(d.cantidad * d.precio_unitario) AS total
FROM ventas v
JOIN detalle_venta d ON d.id_venta = v.id_venta
GROUP BY DATE(v.fecha)
ORDER BY dia;

-- 3. GROUP BY + ORDER BY + LIMIT: los 5 productos más vendidos
SELECT  p.nombre, SUM(d.cantidad) AS unidades
FROM detalle_venta d
JOIN productos p ON p.id_producto = d.id_producto
GROUP BY p.id_producto, p.nombre
ORDER BY unidades DESC
LIMIT 5;

-- 4. SUBCONSULTA (NOT IN): productos que nunca se han vendido
SELECT  codigo, nombre, stock
FROM productos
WHERE id_producto NOT IN (SELECT id_producto FROM detalle_venta)
ORDER BY nombre;

-- 5. Productos con stock bajo (por reabastecer)
SELECT  codigo, nombre, stock, stock_minimo
FROM productos
WHERE stock <= stock_minimo
ORDER BY stock;

-- 6. GROUP BY: ventas por cajero
SELECT  u.nombre AS cajero, COUNT(*) AS num_ventas, SUM(vv.total) AS total
FROM vista_ventas vv
JOIN ventas v   ON v.id_venta   = vv.id_venta
JOIN usuarios u ON u.id_usuario = v.id_usuario
GROUP BY u.id_usuario, u.nombre;

-- 7. LEFT JOIN + GROUP BY: número de productos por categoría (incluye las vacías)
SELECT  c.nombre, COUNT(p.id_producto) AS productos
FROM categorias c
LEFT JOIN productos p ON p.id_categoria = c.id_categoria
GROUP BY c.id_categoria, c.nombre;

-- 8. SUBCONSULTA en WHERE: ventas mayores al promedio
SELECT  id_venta, fecha, cliente, total
FROM vista_ventas
WHERE total > (SELECT AVG(total) FROM vista_ventas)
ORDER BY total DESC;

-- 9. TRANSACCIÓN: así guarda el sistema una venta (ver backend/api/ventas.php)
-- START TRANSACTION;
--   INSERT INTO ventas (id_usuario, id_cliente, metodo_pago) VALUES (2, NULL, 'efectivo');
--   SET @venta = LAST_INSERT_ID();
--   INSERT INTO detalle_venta (id_venta, id_producto, cantidad, precio_unitario) VALUES (@venta, 1, 2, 45.00);
--   UPDATE productos SET stock = stock - 2 WHERE id_producto = 1;
-- COMMIT;   -- si algo falla: ROLLBACK
