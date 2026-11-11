-- ACTUALIZACIÓN DE LA BASE DE DATOS DE MARISCOS EL PUERTO
-- Este archivo sirve para completar una instalación que ya existe.
-- No elimina tablas ni registros. Solo crea lo que falte y agrega ejemplos que todavía no estén.

CREATE DATABASE IF NOT EXISTS mariscos_db
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE mariscos_db;

-- TABLAS PRINCIPALES
-- Si alguna de estas tablas ya existe, MySQL conserva su estructura y sus datos.
CREATE TABLE IF NOT EXISTS clientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(120) NOT NULL,
    telefono VARCHAR(20),
    correo VARCHAR(120),
    direccion VARCHAR(180)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS proveedores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(120) NOT NULL,
    telefono VARCHAR(20),
    correo VARCHAR(120),
    producto VARCHAR(120)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS almacen (
    id INT AUTO_INCREMENT PRIMARY KEY,
    producto VARCHAR(120) NOT NULL,
    categoria VARCHAR(80),
    existencia INT NOT NULL DEFAULT 0,
    unidad VARCHAR(30),
    costo DECIMAL(10,2) NOT NULL DEFAULT 0
) ENGINE=InnoDB;

-- MESAS Y CATÁLOGOS
CREATE TABLE IF NOT EXISTS mesas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    numero INT NOT NULL UNIQUE,
    estado ENUM('Libre', 'Ocupada') DEFAULT 'Libre'
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(80) NOT NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS platillos (
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

-- Algunas instalaciones anteriores ya tenían platillos, pero todavía no guardaban imágenes.
-- Reviso si la columna imagen existe y solo la agrego cuando hace falta.
SET @imagen_existe = (
    SELECT COUNT(*)
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'platillos'
      AND COLUMN_NAME = 'imagen'
);

SET @consulta_sql = IF(
    @imagen_existe = 0,
    'ALTER TABLE platillos ADD COLUMN imagen VARCHAR(255) DEFAULT NULL',
    'SELECT 1'
);

PREPARE consulta FROM @consulta_sql;
EXECUTE consulta;
DEALLOCATE PREPARE consulta;

-- PEDIDOS Y SUS DETALLES
CREATE TABLE IF NOT EXISTS pedidos (
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

CREATE TABLE IF NOT EXISTS detalle_pedido (
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

-- FACTURACIÓN
-- También creo esta tabla aquí para evitar que falte al actualizar una instalación anterior.
CREATE TABLE IF NOT EXISTS facturacion (
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

-- CLIENTES DE EJEMPLO
-- Uso el correo como referencia para no insertar dos veces al mismo cliente de muestra.
INSERT INTO clientes (nombre, telefono, correo, direccion)
SELECT s.nombre, s.telefono, s.correo, s.direccion
FROM (
    SELECT 'Ana López' nombre, '8110000001' telefono, 'ana@mail.com' correo, 'Centro' direccion
    UNION ALL SELECT 'Luis Pérez', '8110000002', 'luis@mail.com', 'Guadalupe'
    UNION ALL SELECT 'María García', '8110000003', 'maria@mail.com', 'Contry'
    UNION ALL SELECT 'Carlos Ruiz', '8110000004', 'carlos@mail.com', 'Linda Vista'
    UNION ALL SELECT 'Sofía Torres', '8110000005', 'sofia@mail.com', 'La Pastora'
    UNION ALL SELECT 'Jorge Díaz', '8110000006', 'jorge@mail.com', 'San Rafael'
    UNION ALL SELECT 'Elena Flores', '8110000007', 'elena@mail.com', 'Dos Ríos'
    UNION ALL SELECT 'Pedro Sánchez', '8110000008', 'pedro@mail.com', 'Cañada Blanca'
    UNION ALL SELECT 'Valeria Cruz', '8110000009', 'valeria@mail.com', 'Azteca'
    UNION ALL SELECT 'Diego Ramos', '8110000010', 'diego@mail.com', 'Jardines'
) s
LEFT JOIN clientes c ON c.correo = s.correo
WHERE c.id IS NULL;

-- PROVEEDORES DE EJEMPLO
INSERT INTO proveedores (nombre, telefono, correo, producto)
SELECT s.nombre, s.telefono, s.correo, s.producto
FROM (
    SELECT 'Pescados del Norte' nombre, '8181000001' telefono, 'ventas@pescadosnorte.mx' correo, 'Pescado' producto
    UNION ALL SELECT 'Mariscos La Costa', '8181000002', 'ventas@lacosta.mx', 'Camarón'
    UNION ALL SELECT 'Abarrotes Sol', '8181000003', 'sol@mail.com', 'Abarrotes'
    UNION ALL SELECT 'Hielo Polar', '8181000004', 'hielo@mail.com', 'Hielo'
    UNION ALL SELECT 'Verduras Frescas', '8181000005', 'verduras@mail.com', 'Verduras'
    UNION ALL SELECT 'Bebidas Regias', '8181000006', 'bebidas@mail.com', 'Bebidas'
    UNION ALL SELECT 'Empaques MTY', '8181000007', 'empaques@mail.com', 'Empaques'
    UNION ALL SELECT 'Lácteos del Valle', '8181000008', 'lacteos@mail.com', 'Lácteos'
    UNION ALL SELECT 'Limpieza Total', '8181000009', 'limpieza@mail.com', 'Limpieza'
    UNION ALL SELECT 'Distribuidora Pacífico', '8181000010', 'pacifico@mail.com', 'Pulpo'
) s
LEFT JOIN proveedores p ON p.correo = s.correo
WHERE p.id IS NULL;

-- PRODUCTOS DE ALMACÉN DE EJEMPLO
INSERT INTO almacen (producto, categoria, existencia, unidad, costo)
SELECT s.producto, s.categoria, s.existencia, s.unidad, s.costo
FROM (
    SELECT 'Camarón mediano' producto, 'Mariscos' categoria, 25 existencia, 'kg' unidad, 180.00 costo
    UNION ALL SELECT 'Filete de pescado', 'Mariscos', 30, 'kg', 120.00
    UNION ALL SELECT 'Pulpo', 'Mariscos', 12, 'kg', 210.00
    UNION ALL SELECT 'Limón', 'Verduras', 40, 'kg', 28.00
    UNION ALL SELECT 'Aguacate', 'Verduras', 18, 'kg', 55.00
    UNION ALL SELECT 'Tomate', 'Verduras', 22, 'kg', 25.00
    UNION ALL SELECT 'Cebolla', 'Verduras', 15, 'kg', 22.00
    UNION ALL SELECT 'Refresco', 'Bebidas', 48, 'piezas', 14.00
    UNION ALL SELECT 'Agua natural', 'Bebidas', 36, 'piezas', 9.00
    UNION ALL SELECT 'Aceite', 'Abarrotes', 10, 'litros', 48.00
) s
LEFT JOIN almacen a ON a.producto = s.producto
WHERE a.id IS NULL;

-- DIEZ MESAS DE EJEMPLO
INSERT IGNORE INTO mesas (numero, estado) VALUES
(1, 'Libre'), (2, 'Libre'), (3, 'Libre'), (4, 'Libre'), (5, 'Libre'),
(6, 'Libre'), (7, 'Libre'), (8, 'Libre'), (9, 'Libre'), (10, 'Libre');

-- DIEZ CATEGORÍAS PARA ORGANIZAR EL MENÚ
INSERT INTO categorias (nombre)
SELECT s.nombre
FROM (
    SELECT 'Ceviches' nombre
    UNION ALL SELECT 'Cócteles'
    UNION ALL SELECT 'Tostadas'
    UNION ALL SELECT 'Pescados'
    UNION ALL SELECT 'Camarones'
    UNION ALL SELECT 'Bebidas'
    UNION ALL SELECT 'Entradas'
    UNION ALL SELECT 'Especialidades'
    UNION ALL SELECT 'Postres'
    UNION ALL SELECT 'Paquetes'
) s
LEFT JOIN categorias c ON c.nombre = s.nombre
WHERE c.id IS NULL;

-- DIEZ PLATILLOS DE EJEMPLO
-- Busco cada categoría por su nombre para no depender del número de ID que tenga en esta instalación.
INSERT INTO platillos (nombre, descripcion, precio, categoria_id, disponible)
SELECT s.nombre, s.descripcion, s.precio, c.id, 1
FROM (
    SELECT 'Ceviche de pescado' nombre, 'Pescado fresco con limón y verduras.' descripcion, 115.00 precio, 'Ceviches' categoria
    UNION ALL SELECT 'Cóctel de camarón', 'Camarones con salsa coctelera.', 145.00, 'Cócteles'
    UNION ALL SELECT 'Tostadas de atún', 'Atún con aguacate.', 95.00, 'Tostadas'
    UNION ALL SELECT 'Filete empanizado', 'Filete con ensalada y arroz.', 165.00, 'Pescados'
    UNION ALL SELECT 'Camarones al ajillo', 'Camarones salteados con ajo.', 185.00, 'Camarones'
    UNION ALL SELECT 'Agua de limón', 'Agua fresca.', 35.00, 'Bebidas'
    UNION ALL SELECT 'Aguachile verde', 'Camarón con salsa verde.', 175.00, 'Ceviches'
    UNION ALL SELECT 'Pulpo a las brasas', 'Pulpo a la plancha.', 220.00, 'Especialidades'
    UNION ALL SELECT 'Tacos gobernador', 'Tacos de camarón.', 130.00, 'Camarones'
    UNION ALL SELECT 'Caldo de mariscos', 'Caldo con selección del mar.', 160.00, 'Pescados'
) s
JOIN categorias c ON c.nombre = s.categoria
LEFT JOIN platillos p ON p.nombre = s.nombre
WHERE p.id IS NULL;

-- DIEZ PEDIDOS DE EJEMPLO
-- Se registran como pagados para que las mesas sigan disponibles al abrir el sistema.
-- Cada pedido queda relacionado con una mesa, un cliente y un platillo existentes.
INSERT INTO pedidos (mesa_id, cliente_id, estado, total)
SELECT m.id, c.id, 'Pagado', p.precio * s.cantidad
FROM (
    SELECT 1 mesa_numero, 'ana@mail.com' correo, 'Ceviche de pescado' platillo, 1 cantidad
    UNION ALL SELECT 2, 'luis@mail.com', 'Cóctel de camarón', 1
    UNION ALL SELECT 3, 'maria@mail.com', 'Tostadas de atún', 1
    UNION ALL SELECT 4, 'carlos@mail.com', 'Filete empanizado', 1
    UNION ALL SELECT 5, 'sofia@mail.com', 'Camarones al ajillo', 1
    UNION ALL SELECT 6, 'jorge@mail.com', 'Agua de limón', 1
    UNION ALL SELECT 7, 'elena@mail.com', 'Aguachile verde', 1
    UNION ALL SELECT 8, 'pedro@mail.com', 'Pulpo a las brasas', 1
    UNION ALL SELECT 9, 'valeria@mail.com', 'Tacos gobernador', 1
    UNION ALL SELECT 10, 'diego@mail.com', 'Caldo de mariscos', 1
) s
JOIN mesas m ON m.numero = s.mesa_numero
JOIN clientes c ON c.correo = s.correo
JOIN platillos p ON p.nombre = s.platillo
WHERE NOT EXISTS (
    SELECT 1
    FROM pedidos existente
    WHERE existente.mesa_id = m.id
      AND existente.cliente_id = c.id
      AND existente.total = p.precio * s.cantidad
      AND existente.estado = 'Pagado'
);

-- DIEZ DETALLES DE PEDIDO
-- Se agregan solo cuando el pedido todavía no tiene ese platillo.
INSERT INTO detalle_pedido (pedido_id, platillo_id, cantidad, precio)
SELECT pe.id, p.id, s.cantidad, p.precio
FROM (
    SELECT 1 mesa_numero, 'ana@mail.com' correo, 'Ceviche de pescado' platillo, 1 cantidad
    UNION ALL SELECT 2, 'luis@mail.com', 'Cóctel de camarón', 1
    UNION ALL SELECT 3, 'maria@mail.com', 'Tostadas de atún', 1
    UNION ALL SELECT 4, 'carlos@mail.com', 'Filete empanizado', 1
    UNION ALL SELECT 5, 'sofia@mail.com', 'Camarones al ajillo', 1
    UNION ALL SELECT 6, 'jorge@mail.com', 'Agua de limón', 1
    UNION ALL SELECT 7, 'elena@mail.com', 'Aguachile verde', 1
    UNION ALL SELECT 8, 'pedro@mail.com', 'Pulpo a las brasas', 1
    UNION ALL SELECT 9, 'valeria@mail.com', 'Tacos gobernador', 1
    UNION ALL SELECT 10, 'diego@mail.com', 'Caldo de mariscos', 1
) s
JOIN mesas m ON m.numero = s.mesa_numero
JOIN clientes c ON c.correo = s.correo
JOIN platillos p ON p.nombre = s.platillo
JOIN pedidos pe
    ON pe.mesa_id = m.id
   AND pe.cliente_id = c.id
   AND pe.total = p.precio * s.cantidad
   AND pe.estado = 'Pagado'
LEFT JOIN detalle_pedido d
    ON d.pedido_id = pe.id AND d.platillo_id = p.id
WHERE d.id IS NULL;

-- DIEZ SOLICITUDES DE FACTURACIÓN DE EJEMPLO
-- Son registros de práctica; no representan comprobantes fiscales reales.
INSERT INTO facturacion
    (cliente_id, rfc, razon_social, codigo_postal, uso_cfdi, total, estado)
SELECT c.id, s.rfc, s.razon_social, s.codigo_postal, s.uso_cfdi, s.total, 'Pendiente'
FROM (
    SELECT 'LOAA900101ABC' rfc, 'Ana López' razon_social, '67100' codigo_postal, 'G03' uso_cfdi, 115.00 total, 'ana@mail.com' correo
    UNION ALL SELECT 'PELU850202DEF', 'Luis Pérez', '67110', 'G03', 145.00, 'luis@mail.com'
    UNION ALL SELECT 'GAMA920303GHI', 'María García', '67120', 'G01', 95.00, 'maria@mail.com'
    UNION ALL SELECT 'RUIC880404JKL', 'Carlos Ruiz', '67130', 'G03', 165.00, 'carlos@mail.com'
    UNION ALL SELECT 'TOSO950505MNO', 'Sofía Torres', '67140', 'G03', 185.00, 'sofia@mail.com'
    UNION ALL SELECT 'DIJO870606PQR', 'Jorge Díaz', '67150', 'G01', 35.00, 'jorge@mail.com'
    UNION ALL SELECT 'FOEL910707STU', 'Elena Flores', '67160', 'G03', 175.00, 'elena@mail.com'
    UNION ALL SELECT 'SAXP930808VWX', 'Pedro Sánchez', '67170', 'G03', 220.00, 'pedro@mail.com'
    UNION ALL SELECT 'CRVA960909YZA', 'Valeria Cruz', '67180', 'G01', 130.00, 'valeria@mail.com'
    UNION ALL SELECT 'RADE891010BCD', 'Diego Ramos', '67190', 'G03', 160.00, 'diego@mail.com'
) s
JOIN clientes c ON c.correo = s.correo
LEFT JOIN facturacion f ON f.rfc = s.rfc
WHERE f.id IS NULL;

-- Al terminar, cada tabla principal y auxiliar cuenta con al menos diez registros de ejemplo.
-- Si vuelves a ejecutar este archivo, los registros de muestra no se duplican.
