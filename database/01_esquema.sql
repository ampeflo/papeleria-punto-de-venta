-- =============================================================
--  Punto de Venta - Papelería "El Lápiz Feliz"
--  Script 01: Creación de la base de datos (MySQL 8)
--  7 tablas normalizadas hasta 3FN
--
--  IMPORTANTE: todas las tablas usan ENGINE = InnoDB.
--  WampServer trae MyISAM como motor por defecto, y MyISAM NO respeta
--  las llaves foráneas ni las transacciones. InnoDB sí.
-- =============================================================

DROP DATABASE IF EXISTS papeleria_db;
CREATE DATABASE papeleria_db CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish_ci;
USE papeleria_db;

-- 1. ROLES: niveles de acceso (Administrador, Cajero)
CREATE TABLE roles (
    id_rol  INT         PRIMARY KEY AUTO_INCREMENT,
    nombre  VARCHAR(30) NOT NULL UNIQUE
) ENGINE = InnoDB;

-- 2. USUARIOS: quienes inician sesión. La contraseña va cifrada.
CREATE TABLE usuarios (
    id_usuario     INT          PRIMARY KEY AUTO_INCREMENT,
    nombre         VARCHAR(100) NOT NULL,
    usuario        VARCHAR(50)  NOT NULL UNIQUE,
    password_hash  VARCHAR(255) NOT NULL,
    id_rol         INT          NOT NULL,
    FOREIGN KEY (id_rol) REFERENCES roles (id_rol)
) ENGINE = InnoDB;

-- 3. CATEGORIAS de productos
CREATE TABLE categorias (
    id_categoria  INT         PRIMARY KEY AUTO_INCREMENT,
    nombre        VARCHAR(50) NOT NULL UNIQUE
) ENGINE = InnoDB;

-- 4. PRODUCTOS
CREATE TABLE productos (
    id_producto   INT           PRIMARY KEY AUTO_INCREMENT,
    codigo        VARCHAR(20)   NOT NULL UNIQUE,
    nombre        VARCHAR(100)  NOT NULL,
    id_categoria  INT           NOT NULL,
    precio        DECIMAL(10,2) NOT NULL CHECK (precio > 0),
    stock         INT           NOT NULL DEFAULT 0 CHECK (stock >= 0),
    stock_minimo  INT           NOT NULL DEFAULT 5 CHECK (stock_minimo >= 0),
    FOREIGN KEY (id_categoria) REFERENCES categorias (id_categoria)
) ENGINE = InnoDB;

-- Índice: acelera la búsqueda de productos por nombre en la caja
CREATE INDEX idx_productos_nombre ON productos (nombre);

-- 5. CLIENTES
CREATE TABLE clientes (
    id_cliente  INT          PRIMARY KEY AUTO_INCREMENT,
    nombre      VARCHAR(100) NOT NULL,
    telefono    VARCHAR(10)  NULL,
    email       VARCHAR(100) NULL UNIQUE
) ENGINE = InnoDB;

-- 6. VENTAS: encabezado del ticket
--    id_cliente puede ser NULL = "Público en general".
--    No se guarda el total porque se calcula del detalle (3FN).
CREATE TABLE ventas (
    id_venta     INT      PRIMARY KEY AUTO_INCREMENT,
    fecha        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    id_usuario   INT      NOT NULL,
    id_cliente   INT      NULL,
    metodo_pago  ENUM('efectivo', 'tarjeta') NOT NULL,
    FOREIGN KEY (id_usuario) REFERENCES usuarios (id_usuario),
    FOREIGN KEY (id_cliente) REFERENCES clientes (id_cliente)
) ENGINE = InnoDB;

-- Índice: acelera los reportes por fecha
CREATE INDEX idx_ventas_fecha ON ventas (fecha);

-- 7. DETALLE_VENTA: productos de cada venta
--    precio_unitario guarda el precio del día de la venta (historial).
CREATE TABLE detalle_venta (
    id_detalle       INT           PRIMARY KEY AUTO_INCREMENT,
    id_venta         INT           NOT NULL,
    id_producto      INT           NOT NULL,
    cantidad         INT           NOT NULL CHECK (cantidad > 0),
    precio_unitario  DECIMAL(10,2) NOT NULL CHECK (precio_unitario > 0),
    FOREIGN KEY (id_venta)    REFERENCES ventas (id_venta) ON DELETE CASCADE,
    FOREIGN KEY (id_producto) REFERENCES productos (id_producto)
) ENGINE = InnoDB;

-- VISTA: cada venta con su total calculado (evita repetir el JOIN y el SUM)
CREATE VIEW vista_ventas AS
SELECT  v.id_venta,
        v.fecha,
        v.metodo_pago,
        u.nombre                                 AS cajero,
        COALESCE(c.nombre, 'Público en general') AS cliente,
        SUM(d.cantidad)                          AS articulos,
        SUM(d.cantidad * d.precio_unitario)      AS total
FROM ventas v
JOIN usuarios u      ON u.id_usuario = v.id_usuario
LEFT JOIN clientes c ON c.id_cliente = v.id_cliente
JOIN detalle_venta d ON d.id_venta   = v.id_venta
GROUP BY v.id_venta, v.fecha, v.metodo_pago, u.nombre, c.nombre;
