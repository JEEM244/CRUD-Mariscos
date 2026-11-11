-- Actualización para agregar las funciones de pedidos.
-- Este archivo no borra los clientes, proveedores ni productos existentes.

USE mariscos_db;

-- Crear las tablas que todavía no existan.
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

-- En algunas instalaciones anteriores, la tabla de platillos ya existía
-- pero no tenía el campo para guardar la dirección de la imagen.
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

-- Agregar las mesas de ejemplo sin repetir las que ya están creadas.
INSERT IGNORE INTO mesas (numero, estado) VALUES
(1, 'Libre'), (2, 'Libre'), (3, 'Libre'), (4, 'Libre'), (5, 'Libre'),
(6, 'Libre'), (7, 'Libre'), (8, 'Libre'), (9, 'Libre'), (10, 'Libre');

-- Agregar las categorías que hagan falta.
INSERT INTO categorias (nombre)
SELECT 'Ceviches' WHERE NOT EXISTS (
    SELECT 1 FROM categorias WHERE nombre = 'Ceviches'
);

INSERT INTO categorias (nombre)
SELECT 'Cócteles' WHERE NOT EXISTS (
    SELECT 1 FROM categorias WHERE nombre = 'Cócteles'
);

INSERT INTO categorias (nombre)
SELECT 'Tostadas' WHERE NOT EXISTS (
    SELECT 1 FROM categorias WHERE nombre = 'Tostadas'
);

INSERT INTO categorias (nombre)
SELECT 'Pescados' WHERE NOT EXISTS (
    SELECT 1 FROM categorias WHERE nombre = 'Pescados'
);

INSERT INTO categorias (nombre)
SELECT 'Camarones' WHERE NOT EXISTS (
    SELECT 1 FROM categorias WHERE nombre = 'Camarones'
);

INSERT INTO categorias (nombre)
SELECT 'Bebidas' WHERE NOT EXISTS (
    SELECT 1 FROM categorias WHERE nombre = 'Bebidas'
);

-- Agregar los platillos de ejemplo que todavía no estén en el menú.
INSERT INTO platillos (nombre, descripcion, precio, categoria_id, disponible)
SELECT
    'Ceviche de pescado',
    'Pescado fresco con limón y verduras.',
    115,
    (SELECT id FROM categorias WHERE nombre = 'Ceviches' LIMIT 1),
    1
WHERE NOT EXISTS (
    SELECT 1 FROM platillos WHERE nombre = 'Ceviche de pescado'
);

INSERT INTO platillos (nombre, descripcion, precio, categoria_id, disponible)
SELECT
    'Cóctel de camarón',
    'Camarones con salsa coctelera.',
    145,
    (SELECT id FROM categorias WHERE nombre = 'Cócteles' LIMIT 1),
    1
WHERE NOT EXISTS (
    SELECT 1 FROM platillos WHERE nombre = 'Cóctel de camarón'
);

INSERT INTO platillos (nombre, descripcion, precio, categoria_id, disponible)
SELECT
    'Tostadas de atún',
    'Atún con aguacate.',
    95,
    (SELECT id FROM categorias WHERE nombre = 'Tostadas' LIMIT 1),
    1
WHERE NOT EXISTS (
    SELECT 1 FROM platillos WHERE nombre = 'Tostadas de atún'
);

INSERT INTO platillos (nombre, descripcion, precio, categoria_id, disponible)
SELECT
    'Filete empanizado',
    'Filete con ensalada y arroz.',
    165,
    (SELECT id FROM categorias WHERE nombre = 'Pescados' LIMIT 1),
    1
WHERE NOT EXISTS (
    SELECT 1 FROM platillos WHERE nombre = 'Filete empanizado'
);

INSERT INTO platillos (nombre, descripcion, precio, categoria_id, disponible)
SELECT
    'Camarones al ajillo',
    'Camarones salteados con ajo.',
    185,
    (SELECT id FROM categorias WHERE nombre = 'Camarones' LIMIT 1),
    1
WHERE NOT EXISTS (
    SELECT 1 FROM platillos WHERE nombre = 'Camarones al ajillo'
);

INSERT INTO platillos (nombre, descripcion, precio, categoria_id, disponible)
SELECT
    'Agua de limón',
    'Agua fresca.',
    35,
    (SELECT id FROM categorias WHERE nombre = 'Bebidas' LIMIT 1),
    1
WHERE NOT EXISTS (
    SELECT 1 FROM platillos WHERE nombre = 'Agua de limón'
);

INSERT INTO platillos (nombre, descripcion, precio, categoria_id, disponible)
SELECT
    'Aguachile verde',
    'Camarón con salsa verde.',
    175,
    (SELECT id FROM categorias WHERE nombre = 'Ceviches' LIMIT 1),
    1
WHERE NOT EXISTS (
    SELECT 1 FROM platillos WHERE nombre = 'Aguachile verde'
);

INSERT INTO platillos (nombre, descripcion, precio, categoria_id, disponible)
SELECT
    'Pulpo a las brasas',
    'Pulpo a la plancha.',
    220,
    (SELECT id FROM categorias WHERE nombre = 'Pescados' LIMIT 1),
    1
WHERE NOT EXISTS (
    SELECT 1 FROM platillos WHERE nombre = 'Pulpo a las brasas'
);

INSERT INTO platillos (nombre, descripcion, precio, categoria_id, disponible)
SELECT
    'Tacos gobernador',
    'Tacos de camarón.',
    130,
    (SELECT id FROM categorias WHERE nombre = 'Camarones' LIMIT 1),
    1
WHERE NOT EXISTS (
    SELECT 1 FROM platillos WHERE nombre = 'Tacos gobernador'
);

INSERT INTO platillos (nombre, descripcion, precio, categoria_id, disponible)
SELECT
    'Caldo de mariscos',
    'Caldo con selección del mar.',
    160,
    (SELECT id FROM categorias WHERE nombre = 'Pescados' LIMIT 1),
    1
WHERE NOT EXISTS (
    SELECT 1 FROM platillos WHERE nombre = 'Caldo de mariscos'
);
