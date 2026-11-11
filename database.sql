-- Base de datos para Mariscos El Puerto.
-- Este archivo se utiliza cuando se instala el proyecto desde cero.

-- Crear la base de datos solo si todavía no existe.
-- utf8mb4 permite guardar correctamente acentos y otros caracteres.
CREATE DATABASE IF NOT EXISTS mariscos_db
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

-- Indicar que las siguientes tablas se crearán dentro de mariscos_db.
USE mariscos_db;

-- CLIENTES
-- Guarda los datos de las personas que pueden hacer pedidos.
CREATE TABLE clientes (
    -- ID único que MySQL asigna automáticamente a cada registro.
    -- Identificador único que MySQL genera automáticamente. Cada tabla tiene su propio ID.
    id INT AUTO_INCREMENT PRIMARY KEY,
    -- Nombre del cliente o proveedor. NOT NULL significa que es obligatorio.
    -- Nombre de la persona o del registro.
    nombre VARCHAR(120) NOT NULL,
    -- Teléfono de contacto. Puede quedar vacío.
    -- Número de teléfono de contacto.
    telefono VARCHAR(20),
    -- Correo electrónico de contacto.
    -- Dirección de correo electrónico.
    correo VARCHAR(120),
    -- Dirección del cliente.
    -- Dirección del cliente.
    direccion VARCHAR(180)
) ENGINE=InnoDB;

-- PROVEEDORES
-- Guarda los datos de los negocios o personas que suministran productos.
CREATE TABLE proveedores (
    -- Identificador único que MySQL genera automáticamente. Cada tabla tiene su propio ID.
    id INT AUTO_INCREMENT PRIMARY KEY,
    -- Nombre de la persona o del registro.
    nombre VARCHAR(120) NOT NULL,
    -- Número de teléfono de contacto.
    telefono VARCHAR(20),
    -- Dirección de correo electrónico.
    correo VARCHAR(120),
    -- Nombre del producto.
    producto VARCHAR(120)
) ENGINE=InnoDB;

-- PRODUCTOS QUE HAY EN EL ALMACÉN
-- Guarda las existencias y el costo de cada producto.
CREATE TABLE almacen (
    -- Identificador único que MySQL genera automáticamente. Cada tabla tiene su propio ID.
    id INT AUTO_INCREMENT PRIMARY KEY,
    -- Nombre del producto registrado o suministrado.
    -- Nombre del producto.
    producto VARCHAR(120) NOT NULL,
    -- Categoría del producto dentro del almacén.
    -- Grupo al que pertenece el producto.
    categoria VARCHAR(80),
    -- Cantidad disponible. Si no se indica, comienza en cero.
    -- Cantidad de unidades disponibles.
    existencia INT NOT NULL DEFAULT 0,
    -- Unidad en la que se cuenta, por ejemplo kg o piezas.
    -- Forma en que se mide el producto, como kg o piezas.
    unidad VARCHAR(30),
    -- Costo del producto con dos decimales.
    -- Costo del producto con dos decimales.
    costo DECIMAL(10,2) NOT NULL DEFAULT 0
) ENGINE=InnoDB;

-- MESAS DEL RESTAURANTE
-- Cada mesa tiene un número y un estado: Libre u Ocupada.
CREATE TABLE mesas (
    -- Identificador único que MySQL genera automáticamente. Cada tabla tiene su propio ID.
    id INT AUTO_INCREMENT PRIMARY KEY,
    -- Número visible de la mesa. UNIQUE evita repetir números.
    -- Número de la mesa; no se puede repetir.
    numero INT NOT NULL UNIQUE,
    -- ENUM limita el estado a estas dos opciones.
    -- Estado actual, limitado a las opciones indicadas en ENUM.
    estado ENUM('Libre', 'Ocupada') DEFAULT 'Libre'
) ENGINE=InnoDB;

-- CATEGORÍAS DEL MENÚ
-- Sirven para agrupar los platillos, por ejemplo Bebidas o Pescados.
CREATE TABLE categorias (
    -- Identificador único que MySQL genera automáticamente. Cada tabla tiene su propio ID.
    id INT AUTO_INCREMENT PRIMARY KEY,
    -- Nombre de la persona o del registro.
    nombre VARCHAR(80) NOT NULL
) ENGINE=InnoDB;

-- PLATILLOS QUE PUEDEN AGREGARSE A UN PEDIDO
-- Aquí se guarda el nombre, precio, descripción, imagen y categoría de cada platillo.
CREATE TABLE platillos (
    -- Identificador único que MySQL genera automáticamente. Cada tabla tiene su propio ID.
    id INT AUTO_INCREMENT PRIMARY KEY,
    -- Nombre de la persona o del registro.
    nombre VARCHAR(120) NOT NULL,
    -- Texto breve para describir el platillo.
    -- Texto breve que explica de qué trata el platillo.
    descripcion VARCHAR(255),
    -- Precio de venta. DECIMAL permite guardar cantidades con centavos.
    -- Precio del platillo o del producto.
    precio DECIMAL(10,2) NOT NULL,
    -- Dirección de la imagen del platillo. Puede quedar sin imagen.
    -- Dirección de la imagen que se puede mostrar en el menú.
    imagen VARCHAR(255) DEFAULT NULL,
    -- ID de la categoría a la que pertenece el platillo.
    -- ID de la categoría relacionada con este registro.
    categoria_id INT NULL,
    -- 1 significa que aparece en el menú; 0 significa que está oculto.
    -- Indica si el platillo se puede mostrar y vender (1 sí, 0 no).
    disponible TINYINT(1) DEFAULT 1,
    -- Relacionar el platillo con una categoría. Si se borra la categoría, el campo queda vacío.
    -- Esta relación conecta cada platillo con su categoría.
    CONSTRAINT fk_platillos_categoria
        FOREIGN KEY (categoria_id) REFERENCES categorias(id)
        ON DELETE SET NULL
) ENGINE=InnoDB;

-- PEDIDOS QUE REGISTRAN LOS MESEROS
-- Esta tabla guarda los datos generales de cada pedido.
CREATE TABLE pedidos (
    -- Identificador único que MySQL genera automáticamente. Cada tabla tiene su propio ID.
    id INT AUTO_INCREMENT PRIMARY KEY,
    -- ID de la mesa donde se realizó el pedido.
    -- ID de la mesa donde se registró el pedido.
    mesa_id INT NOT NULL,
    -- ID del cliente. Puede quedar vacío si no se registró uno.
    -- ID del cliente relacionado; puede quedar vacío cuando es opcional.
    cliente_id INT NULL,
    -- Estado por el que va pasando el pedido.
    -- Estado actual, limitado a las opciones indicadas en ENUM.
    estado ENUM('Pendiente', 'Preparando', 'Entregado', 'Pagado') DEFAULT 'Pendiente',
    -- Importe total de todos los platillos del pedido.
    -- Importe total calculado para el pedido o solicitud.
    total DECIMAL(10,2) NOT NULL DEFAULT 0,
    -- Fecha y hora que MySQL guarda al crear el pedido.
    -- Fecha y hora que MySQL guarda automáticamente.
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    -- Relacionar cada pedido con una mesa existente.
    -- Esta relación conecta el pedido con una mesa existente.
    CONSTRAINT fk_pedidos_mesa
        FOREIGN KEY (mesa_id) REFERENCES mesas(id),
    -- Relacionar el pedido con un cliente; si se elimina, el pedido se conserva sin cliente.
    -- Esta relación conecta el pedido con un cliente, si se registró uno.
    CONSTRAINT fk_pedidos_cliente
        FOREIGN KEY (cliente_id) REFERENCES clientes(id)
        ON DELETE SET NULL
) ENGINE=InnoDB;

-- CADA PEDIDO PUEDE TENER UNO O VARIOS PLATILLOS
-- Esta tabla conecta un pedido con los platillos que incluye.
CREATE TABLE detalle_pedido (
    -- Identificador único que MySQL genera automáticamente. Cada tabla tiene su propio ID.
    id INT AUTO_INCREMENT PRIMARY KEY,
    -- ID del pedido al que pertenece este detalle.
    -- ID del pedido al que pertenece este detalle.
    pedido_id INT NOT NULL,
    -- ID del platillo que se agregó al pedido.
    -- ID del platillo que se agregó al pedido.
    platillo_id INT NOT NULL,
    -- Número de unidades de ese platillo.
    -- Número de unidades de este platillo en el pedido.
    cantidad INT NOT NULL,
    -- Precio del platillo o del producto.
    precio DECIMAL(10,2) NOT NULL,
    -- Si se elimina un pedido, también se eliminan sus detalles.
    -- Esta relación conecta el detalle con su pedido.
    CONSTRAINT fk_detalle_pedido
        FOREIGN KEY (pedido_id) REFERENCES pedidos(id)
        ON DELETE CASCADE,
    -- Comprobar que el platillo agregado exista en la tabla platillos.
    -- Esta relación comprueba que el platillo exista.
    CONSTRAINT fk_detalle_platillo
        FOREIGN KEY (platillo_id) REFERENCES platillos(id)
) ENGINE=InnoDB;

-- DATOS PARA EL SEGUIMIENTO DE FACTURACIÓN
-- Guarda la información fiscal y el estado de cada solicitud.
CREATE TABLE facturacion (
    -- Identificador único que MySQL genera automáticamente. Cada tabla tiene su propio ID.
    id INT AUTO_INCREMENT PRIMARY KEY,
    -- ID del cliente relacionado; puede quedar vacío cuando es opcional.
    cliente_id INT NOT NULL,
    -- ID del pedido al que pertenece este detalle.
    pedido_id INT NULL,
    -- RFC capturado para preparar la solicitud de facturación.
    -- RFC capturado en los datos de facturación.
    rfc VARCHAR(13),
    -- Nombre fiscal de la persona o empresa.
    -- Nombre fiscal de la persona o empresa.
    razon_social VARCHAR(150),
    -- Código postal relacionado con los datos fiscales.
    -- Código postal relacionado con los datos fiscales.
    codigo_postal VARCHAR(5),
    -- Clave que indica el uso que se daría al CFDI.
    -- Clave que identifica el uso indicado para el CFDI.
    uso_cfdi VARCHAR(30),
    -- Importe total calculado para el pedido o solicitud.
    total DECIMAL(10,2) NOT NULL DEFAULT 0,
    -- Estado de seguimiento de la solicitud de facturación.
    -- Estado actual, limitado a las opciones indicadas en ENUM.
    estado ENUM('Pendiente', 'Solicitada', 'Emitida') DEFAULT 'Pendiente',
    -- Fecha y hora que MySQL guarda automáticamente.
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    -- Relacionar la solicitud con el cliente al que pertenece.
    -- Esta relación conecta la solicitud con un cliente.
    CONSTRAINT fk_facturacion_cliente
        FOREIGN KEY (cliente_id) REFERENCES clientes(id),
    -- Relacionar la solicitud con el pedido, si se capturó uno.
    -- Esta relación conecta la solicitud con un pedido, cuando existe.
    CONSTRAINT fk_facturacion_pedido
        FOREIGN KEY (pedido_id) REFERENCES pedidos(id)
        ON DELETE SET NULL
) ENGINE=InnoDB;

-- REGISTROS DE EJEMPLO PARA CLIENTES
-- Se agregan diez clientes para poder probar el módulo desde el inicio.
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
-- Se agregan diez proveedores de muestra.
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
-- Cada registro contiene producto, categoría, cantidad, unidad y costo.
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
-- Crear diez mesas y dejarlas libres para los primeros pedidos.
INSERT INTO mesas (numero, estado) VALUES
(1, 'Libre'), (2, 'Libre'), (3, 'Libre'), (4, 'Libre'), (5, 'Libre'),
(6, 'Libre'), (7, 'Libre'), (8, 'Libre'), (9, 'Libre'), (10, 'Libre');

-- CATEGORÍAS DEL MENÚ
-- Estas categorías se relacionan con platillos mediante categoria_id.
INSERT INTO categorias (nombre) VALUES
('Ceviches'),
('Cócteles'),
('Tostadas'),
('Pescados'),
('Camarones'),
('Bebidas');

-- PLATILLOS DE EJEMPLO
-- Los números de categoria_id corresponden al orden de las categorías anteriores.
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
-- Son datos de práctica, no representan facturas fiscales reales.
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
