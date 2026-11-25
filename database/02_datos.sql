-- =============================================================
--  Script 02: Datos de ejemplo
--  Las fechas son relativas a NOW() para que el panel tenga datos recientes.
--
--  Usuarios de prueba:
--    admin / admin123   (Administrador)
--    luis  / cajero123  (Cajero)
-- =============================================================

USE papeleria_db;

INSERT INTO roles (id_rol, nombre) VALUES
(1, 'Administrador'),
(2, 'Cajero');

-- Contraseñas cifradas con password_hash() de PHP
INSERT INTO usuarios (nombre, usuario, password_hash, id_rol) VALUES
('Carmen Ruiz',    'admin', '$2y$10$jfqX.wrygYwtn9b82knpzudtFYUVUayHCpbHdLI8p4d4VU4dx2BeS', 1),
('Luis Hernández', 'luis',  '$2y$10$CoccdnsgZ.rwtczoXkciC.QQ12yU7xgASBqASnDgdvADDDV9wg6E.', 2);

INSERT INTO categorias (id_categoria, nombre) VALUES
(1, 'Cuadernos'),
(2, 'Escritura'),
(3, 'Arte y manualidades'),
(4, 'Papel'),
(5, 'Oficina'),
(6, 'Geometría');

INSERT INTO productos (id_producto, codigo, nombre, id_categoria, precio, stock, stock_minimo) VALUES
( 1, 'CUA-001', 'Cuaderno profesional raya 100 hojas',         1,  45.00,  60, 10),
( 2, 'CUA-002', 'Cuaderno profesional cuadro chico 100 hojas', 1,  45.00,   3, 10),
( 3, 'CUA-003', 'Cuaderno italiano raya 100 hojas',            1,  30.00,  40, 10),
( 4, 'CUA-004', 'Libreta de dibujo',                           1,  40.00,  15,  5),
( 5, 'ESC-001', 'Lápiz Mirado No. 2',                          2,   8.00, 150, 30),
( 6, 'ESC-002', 'Pluma BIC negra',                             2,   7.00, 120, 30),
( 7, 'ESC-003', 'Pluma BIC azul',                              2,   7.00, 110, 30),
( 8, 'ESC-004', 'Pluma BIC roja',                              2,   7.00,   4, 30),
( 9, 'ESC-005', 'Marcatextos amarillo',                        2,  15.00,  45, 10),
(10, 'ESC-006', 'Goma blanca',                                 2,   6.00,  80, 20),
(11, 'ESC-007', 'Sacapuntas metálico',                         2,   8.00,  60, 15),
(12, 'ART-001', 'Colores 12 piezas',                           3,  89.00,  20,  5),
(13, 'ART-002', 'Crayolas 12 piezas',                          3,  35.00,  25,  8),
(14, 'ART-003', 'Resistol 850 110 g',                          3,  28.00,  30, 10),
(15, 'ART-004', 'Tijeras escolares',                           3,  25.00,  35, 10),
(16, 'ART-005', 'Plastilina 10 barras',                        3,  32.00,   0,  8),
(17, 'PAP-001', 'Hojas blancas carta paquete 500',             4, 125.00,  25,  5),
(18, 'PAP-002', 'Cartulina blanca',                            4,   6.00, 100, 20),
(19, 'PAP-003', 'Papel crepé',                                 4,   8.00,  50, 15),
(20, 'OFI-001', 'Folder tamaño carta',                         5,   4.00, 200, 40),
(21, 'OFI-002', 'Engrapadora',                                 5,  90.00,   8,  3),
(22, 'OFI-003', 'Cinta adhesiva',                              5,  15.00,  50, 12),
(23, 'OFI-004', 'Pegamento en barra',                          5,  17.00,  45, 10),
(24, 'GEO-001', 'Juego de geometría',                          6,  58.00,  18,  5),
(25, 'GEO-002', 'Calculadora científica',                      6, 399.00,   6,  3),
(26, 'GEO-003', 'Compás escolar',                              6,  42.00,   2,  5),
(27, 'GEO-004', 'Regla 30 cm',                                 6,  10.00,  60, 15);

INSERT INTO clientes (id_cliente, nombre, telefono, email) VALUES
(1, 'Ana López García',               '5512345678', 'ana.lopez@correo.com'),
(2, 'Escuela Primaria Benito Juárez', '5523456789', 'direccion@primariabj.edu.mx'),
(3, 'Roberto Sánchez',                '5534567890', NULL),
(4, 'María Fernanda Torres',          '5556789012', 'mafer@correo.com');

-- Ventas de los últimos 7 días (usuario 1 = admin, 2 = luis)
INSERT INTO ventas (id_venta, fecha, id_usuario, id_cliente, metodo_pago) VALUES
( 1, NOW() - INTERVAL 6 DAY,  2, NULL, 'efectivo'),
( 2, NOW() - INTERVAL 6 DAY,  2, 1,    'efectivo'),
( 3, NOW() - INTERVAL 5 DAY,  1, 2,    'tarjeta'),
( 4, NOW() - INTERVAL 4 DAY,  2, NULL, 'efectivo'),
( 5, NOW() - INTERVAL 3 DAY,  2, 3,    'efectivo'),
( 6, NOW() - INTERVAL 3 DAY,  1, NULL, 'tarjeta'),
( 7, NOW() - INTERVAL 2 DAY,  2, 4,    'tarjeta'),
( 8, NOW() - INTERVAL 1 DAY,  2, NULL, 'efectivo'),
( 9, NOW() - INTERVAL 1 DAY,  1, 1,    'efectivo'),
(10, NOW() - INTERVAL 2 HOUR, 2, NULL, 'efectivo'),
(11, NOW() - INTERVAL 1 HOUR, 2, 2,    'tarjeta');

INSERT INTO detalle_venta (id_venta, id_producto, cantidad, precio_unitario) VALUES
( 1,  1, 3,  45.00), ( 1,  5, 4,   8.00),
( 2,  2, 5,  45.00), ( 2,  6, 3,   7.00), ( 2, 15, 1, 25.00),
( 3, 17, 4, 125.00), ( 3, 20,50,   4.00),
( 4, 25, 1, 399.00), ( 4, 24, 1,  58.00),
( 5,  1, 2,  45.00), ( 5, 12, 1,  89.00),
( 6,  5,10,   8.00), ( 6, 11, 2,   8.00),
( 7, 18, 5,   6.00), ( 7, 19, 3,   8.00), ( 7, 14, 1, 28.00),
( 8,  3, 2,  30.00), ( 8,  5, 3,   8.00),
( 9,  1, 4,  45.00), ( 9, 27, 2,  10.00),
(10,  5, 5,   8.00), (10, 10, 3,   6.00),
(11, 20,30,   4.00), (11, 17, 2, 125.00);
-- Nota: varios productos nunca se han vendido (sirve para la subconsulta NOT IN)
