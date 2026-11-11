-- Base de datos para Mariscos El Puerto.
-- Este archivo se utiliza cuando se instala el proyecto desde cero.

CREATE DATABASE IF NOT EXISTS mariscos_db
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE mariscos_db;

-- CLIENTES
CREATE TABLE clientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(120) NOT NULL,
    telefono VARCHAR(20),
    correo VARCHAR(120),
    direccion VARCHAR(180)
) ENGINE=InnoDB;

-- PROVEEDORES
CREATE TABLE proveedores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(120) NOT NULL,
    telefono VARCHAR(20),
    correo VARCHAR(120),
    producto VARCHAR(120)
) ENGINE=InnoDB;

-- PRODUCTOS QUE HAY EN EL ALMACÉN
CREATE TABLE almacen (
    id INT AUTO_INCREMENT PRIMARY KEY,
    producto VARCHAR(120) NOT NULL,
    categoria VARCHAR(80),
    existencia INT NOT NULL DEFAULT 0,
    unidad VARCHAR(30),
    costo DECIMAL(10,2) NOT NULL DEFAULT 0
) ENGINE=InnoDB;

-- MESAS DEL RESTAURANTE
CREATE TABLE mesas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    numero INT NOT NULL UNIQUE,
    estado ENUM('Libre', 'Ocupada') DEFAULT 'Libre'
) ENGINE=InnoDB;

-- CATEGORÍAS DEL MENÚ
CREATE TABLE categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(80) NOT NULL
) ENGINE=InnoDB;

-- PLATILLOS QUE PUEDEN AGREGARSE A UN PEDIDO
CREATE TABLE platillos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(120) NOT NULL,
    descripcion VARCHAR(255),
    precio DECIMAL(10,2) NOT NULL,
    imagen VARCHAR(255) DEFAULT NULL,
    categoria_id INT NULL,
    disponible TINYINT(1) DEFAULT 1,
    CONSTRAINT fk_platillos_categoria
        FOREIGN KEY (categoria_id) REFERENCES categorias(id)
        ON DELETE SET NULL
) ENGINE=InnoDB;

-- PEDIDOS QUE REGISTRAN LOS MESEROS
CREATE TABLE pedidos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    mesa_id INT NOT NULL,
    cliente_id INT NULL,
    estado ENUM('Pendiente', 'Preparando', 'Entregado', 'Pagado') DEFAULT 'Pendiente',
    total DECIMAL(10,2) NOT NULL DEFAULT 0,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_pedidos_mesa
        FOREIGN KEY (mesa_id) REFERENCES mesas(id),
    CONSTRAINT fk_pedidos_cliente
        FOREIGN KEY (cliente_id) REFERENCES clientes(id)
        ON DELETE SET NULL
) ENGINE=InnoDB;

-- CADA PEDIDO PUEDE TENER UNO O VARIOS PLATILLOS
CREATE TABLE detalle_pedido (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pedido_id INT NOT NULL,
    platillo_id INT NOT NULL,
    cantidad INT NOT NULL,
    precio DECIMAL(10,2) NOT NULL,
    CONSTRAINT fk_detalle_pedido
        FOREIGN KEY (pedido_id) REFERENCES pedidos(id)
        ON DELETE CASCADE,
    CONSTRAINT fk_detalle_platillo
        FOREIGN KEY (platillo_id) REFERENCES platillos(id)
) ENGINE=InnoDB;

-- DATOS PARA EL SEGUIMIENTO DE FACTURACIÓN
CREATE TABLE facturacion (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cliente_id INT NOT NULL,
    pedido_id INT NULL,
    rfc VARCHAR(13),
    razon_social VARCHAR(150),
    codigo_postal VARCHAR(5),
    uso_cfdi VARCHAR(30),
    total DECIMAL(10,2) NOT NULL DEFAULT 0,
    estado ENUM('Pendiente', 'Solicitada', 'Emitida') DEFAULT 'Pendiente',
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_facturacion_cliente
        FOREIGN KEY (cliente_id) REFERENCES clientes(id),
    CONSTRAINT fk_facturacion_pedido
        FOREIGN KEY (pedido_id) REFERENCES pedidos(id)
        ON DELETE SET NULL
) ENGINE=InnoDB;

-- REGISTROS DE EJEMPLO PARA CLIENTES
INSERT INTO clientes (nombre, telefono, correo, direccion) VALUES
('Ana López', '8110000001', 'ana@mail.com', 'Centro'),
('Luis Pérez', '8110000002', 'luis@mail.com', 'Guadalupe'),
('María García', '8110000003', 'maria@mail.com', 'Contry'),
('Carlos Ruiz', '8110000004', 'carlos@mail.com', 'Linda Vista'),
('Sofía Torres', '8110000005', 'sofia@mail.com', 'La Pastora'),
('Jorge Díaz', '8110000006', 'jorge@mail.com', 'San Rafael'),
('Elena Flores', '8110000007', 'elena@mail.com', 'Dos Ríos'),
('Pedro Sánchez', '8110000008', 'pedro@mail.com', 'Cañada Blanca'),
('Valeria Cruz', '8110000009', 'valeria@mail.com', 'Azteca'),
('Diego Ramos', '8110000010', 'diego@mail.com', 'Jardines');

-- REGISTROS DE EJEMPLO PARA PROVEEDORES
INSERT INTO proveedores (nombre, telefono, correo, producto) VALUES
('Pescados del Norte', '8181000001', 'ventas@pescadosnorte.mx', 'Pescado'),
('Mariscos La Costa', '8181000002', 'ventas@lacosta.mx', 'Camarón'),
('Abarrotes Sol', '8181000003', 'sol@mail.com', 'Abarrotes'),
('Hielo Polar', '8181000004', 'hielo@mail.com', 'Hielo'),
('Verduras Frescas', '8181000005', 'verduras@mail.com', 'Verduras'),
('Bebidas Regias', '8181000006', 'bebidas@mail.com', 'Bebidas'),
('Empaques MTY', '8181000007', 'empaques@mail.com', 'Empaques'),
('Lácteos del Valle', '8181000008', 'lacteos@mail.com', 'Lácteos'),
('Limpieza Total', '8181000009', 'limpieza@mail.com', 'Limpieza'),
('Distribuidora Pacífico', '8181000010', 'pacifico@mail.com', 'Pulpo');

-- REGISTROS DE EJEMPLO PARA ALMACÉN
INSERT INTO almacen (producto, categoria, existencia, unidad, costo) VALUES
('Camarón mediano', 'Mariscos', 25, 'kg', 180),
('Filete de pescado', 'Mariscos', 30, 'kg', 120),
('Pulpo', 'Mariscos', 12, 'kg', 210),
('Limón', 'Verduras', 40, 'kg', 28),
('Aguacate', 'Verduras', 18, 'kg', 55),
('Tomate', 'Verduras', 22, 'kg', 25),
('Cebolla', 'Verduras', 15, 'kg', 22),
('Refresco', 'Bebidas', 48, 'piezas', 14),
('Agua natural', 'Bebidas', 36, 'piezas', 9),
('Aceite', 'Abarrotes', 10, 'litros', 48);

-- MESAS DISPONIBLES
INSERT INTO mesas (numero, estado) VALUES
(1, 'Libre'), (2, 'Libre'), (3, 'Libre'), (4, 'Libre'), (5, 'Libre'),
(6, 'Libre'), (7, 'Libre'), (8, 'Libre'), (9, 'Libre'), (10, 'Libre');

-- CATEGORÍAS DEL MENÚ
INSERT INTO categorias (nombre) VALUES
('Ceviches'),
('Cócteles'),
('Tostadas'),
('Pescados'),
('Camarones'),
('Bebidas');

-- PLATILLOS DE EJEMPLO
INSERT INTO platillos (nombre, descripcion, precio, categoria_id, disponible) VALUES
('Ceviche de pescado', 'Pescado fresco con limón y verduras.', 115, 1, 1),
('Cóctel de camarón', 'Camarones con salsa coctelera.', 145, 2, 1),
('Tostadas de atún', 'Atún con aguacate.', 95, 3, 1),
('Filete empanizado', 'Filete con ensalada y arroz.', 165, 4, 1),
('Camarones al ajillo', 'Camarones salteados con ajo.', 185, 5, 1),
('Agua de limón', 'Agua fresca.', 35, 6, 1),
('Aguachile verde', 'Camarón con salsa verde.', 175, 1, 1),
('Pulpo a las brasas', 'Pulpo a la plancha.', 220, 4, 1),
('Tacos gobernador', 'Tacos de camarón.', 130, 5, 1),
('Caldo de mariscos', 'Caldo con selección del mar.', 160, 4, 1);

-- SOLICITUDES DE FACTURACIÓN DE EJEMPLO
INSERT INTO facturacion
    (cliente_id, rfc, razon_social, codigo_postal, uso_cfdi, total, estado)
VALUES
(1, 'LOAA900101ABC', 'Ana López', '67100', 'G03', 115, 'Pendiente'),
(2, 'PELU850202DEF', 'Luis Pérez', '67110', 'G03', 145, 'Pendiente'),
(3, 'GAMA920303GHI', 'María García', '67120', 'G01', 95, 'Pendiente'),
(4, 'RUIC880404JKL', 'Carlos Ruiz', '67130', 'G03', 165, 'Pendiente'),
(5, 'TOSO950505MNO', 'Sofía Torres', '67140', 'G03', 185, 'Pendiente'),
(6, 'DIJO870606PQR', 'Jorge Díaz', '67150', 'G01', 35, 'Pendiente'),
(7, 'FOEL910707STU', 'Elena Flores', '67160', 'G03', 175, 'Pendiente'),
(8, 'SAXP930808VWX', 'Pedro Sánchez', '67170', 'G03', 220, 'Pendiente'),
(9, 'CRVA960909YZA', 'Valeria Cruz', '67180', 'G01', 130, 'Pendiente'),
(10, 'RADE891010BCD', 'Diego Ramos', '67190', 'G03', 160, 'Pendiente');
