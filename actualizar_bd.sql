-- Este archivo lo voy a utilizar para agregar las tablas que necesita el módulo de pedidos a una base de datos que ya existe. agregar las funciones de pedidos.
-- Este archivo lo voy a utilizar no borra los clientes, proveedores ni productos existentes.

-- Ahora selecciono la base de datos que ya utiliza el proyecto.
USE mariscos_db;

-- Primero voy creando las tablas que todavía hagan falta. que todavía no existan.
-- Crear la tabla de mesas únicamente si todavía no existe.
CREATE TABLE IF NOT EXISTS mesas (
    -- Identificador único que MySQL genera automáticamente. Cada tabla tiene su propio ID.
    id INT AUTO_INCREMENT PRIMARY KEY,
    -- Número de la mesa; no se puede repetir.
    numero INT NOT NULL UNIQUE,
    -- Estado actual, limitado a las opciones indicadas en ENUM.
    estado ENUM('Libre', 'Ocupada') DEFAULT 'Libre'
) ENGINE=InnoDB;

-- Crear el catálogo de categorías si hace falta.
CREATE TABLE IF NOT EXISTS categorias (
    -- Identificador único que MySQL genera automáticamente. Cada tabla tiene su propio ID.
    id INT AUTO_INCREMENT PRIMARY KEY,
    -- Nombre de la persona o del registro.
    nombre VARCHAR(80) NOT NULL
) ENGINE=InnoDB;

-- Crear la tabla del menú si todavía no existe.
CREATE TABLE IF NOT EXISTS platillos (
    -- Identificador único que MySQL genera automáticamente. Cada tabla tiene su propio ID.
    id INT AUTO_INCREMENT PRIMARY KEY,
    -- Nombre de la persona o del registro.
    nombre VARCHAR(120) NOT NULL,
    -- Texto breve que explica de qué trata el platillo.
    descripcion VARCHAR(255),
    -- Precio del platillo o del producto.
    precio DECIMAL(10,2) NOT NULL,
    -- Dirección de la imagen que se puede mostrar en el menú.
    imagen VARCHAR(255) DEFAULT NULL,
    -- ID de la categoría relacionada con este registro.
    categoria_id INT NULL,
    -- Indica si el platillo se puede mostrar y vender (1 sí, 0 no).
    disponible TINYINT(1) DEFAULT 1,
    -- Esta relación conecta cada platillo con su categoría.
    CONSTRAINT fk_platillos_categoria
        FOREIGN KEY (categoria_id) REFERENCES categorias(id)
        ON DELETE SET NULL
) ENGINE=InnoDB;

-- Crear la tabla que guarda los datos generales de cada pedido.
CREATE TABLE IF NOT EXISTS pedidos (
    -- Identificador único que MySQL genera automáticamente. Cada tabla tiene su propio ID.
    id INT AUTO_INCREMENT PRIMARY KEY,
    -- ID de la mesa donde se registró el pedido.
    mesa_id INT NOT NULL,
    -- ID del cliente relacionado; puede quedar vacío cuando es opcional.
    cliente_id INT NULL,
    -- Estado actual, limitado a las opciones indicadas en ENUM.
    estado ENUM('Pendiente', 'Preparando', 'Entregado', 'Pagado') DEFAULT 'Pendiente',
    -- Importe total calculado para el pedido o solicitud.
    total DECIMAL(10,2) NOT NULL DEFAULT 0,
    -- Fecha y hora que MySQL guarda automáticamente.
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    -- Esta relación conecta el pedido con una mesa existente.
    CONSTRAINT fk_pedidos_mesa
        FOREIGN KEY (mesa_id) REFERENCES mesas(id),
    -- Esta relación conecta el pedido con un cliente, si se registró uno.
    CONSTRAINT fk_pedidos_cliente
        FOREIGN KEY (cliente_id) REFERENCES clientes(id)
        ON DELETE SET NULL
) ENGINE=InnoDB;

-- Crear la tabla que guarda los platillos y cantidades de cada pedido.
CREATE TABLE IF NOT EXISTS detalle_pedido (
    -- Identificador único que MySQL genera automáticamente. Cada tabla tiene su propio ID.
    id INT AUTO_INCREMENT PRIMARY KEY,
    -- ID del pedido al que pertenece este detalle.
    pedido_id INT NOT NULL,
    -- ID del platillo que se agregó al pedido.
    platillo_id INT NOT NULL,
    -- Número de unidades de este platillo en el pedido.
    cantidad INT NOT NULL,
    -- Precio del platillo o del producto.
    precio DECIMAL(10,2) NOT NULL,
    -- Esta relación conecta el detalle con su pedido.
    CONSTRAINT fk_detalle_pedido
        FOREIGN KEY (pedido_id) REFERENCES pedidos(id)
        ON DELETE CASCADE,
    -- Esta relación comprueba que el platillo exista.
    CONSTRAINT fk_detalle_platillo
        FOREIGN KEY (platillo_id) REFERENCES platillos(id)
) ENGINE=InnoDB;

-- También considero las instalaciones anteriores, porque algunas ya tenían la tabla de platillos pero todavía no tenían el campo para guardar su imagen. anteriores, la tabla de platillos ya existía
-- pero no tenía el campo para guardar la dirección de la imagen.
-- Para saber si ese campo ya existe, reviso la información que MySQL guarda sobre las columnas. para saber si la columna imagen ya está creada.
-- Con esta consulta obtengo un número: será 1 si el campo existe y 0 si todavía falta. 1 si existe y 0 si todavía falta.
SET @imagen_existe = (
    SELECT COUNT(*)
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'platillos'
      AND COLUMN_NAME = 'imagen'
);

-- Con el resultado anterior decido qué instrucción voy a ejecutar. SQL que se va a ejecutar.
-- Si falta la columna preparo un ALTER TABLE; si ya está creada, no necesito modificar la tabla., se prepara un ALTER TABLE; si ya existe, no se modifica nada.
SET @consulta_sql = IF(
    @imagen_existe = 0,
    'ALTER TABLE platillos ADD COLUMN imagen VARCHAR(255) DEFAULT NULL',
    'SELECT 1'
);

-- Ahora preparo la instrucción que guardé como texto. prepara la instrucción que se guardó como texto.
PREPARE consulta FROM @consulta_sql;
-- Después ejecuto esa instrucción. ejecuta la instrucción preparada.
EXECUTE consulta;
-- Por último libero la instrucción preparada porque ya terminé de utilizarla. la instrucción preparada porque ya se utilizó.
DEALLOCATE PREPARE consulta;

-- Ahora agrego las mesas de ejemplo, cuidando no repetir las que ya existen. de ejemplo sin repetir las que ya están creadas.
-- Uso INSERT IGNORE para que MySQL omita los números de mesa que ya estén registrados. agrega las mesas sin repetir los números que ya existen.
INSERT IGNORE INTO mesas (numero, estado) VALUES
(1, 'Libre'), (2, 'Libre'), (3, 'Libre'), (4, 'Libre'), (5, 'Libre'),
(6, 'Libre'), (7, 'Libre'), (8, 'Libre'), (9, 'Libre'), (10, 'Libre');

-- Después agrego las categorías que todavía no estén registradas. que hagan falta.
-- Antes de insertar cada una, reviso que no exista otra con el mismo nombre. se usa NOT EXISTS para no agregar nombres repetidos.
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

-- Al final agrego los platillos de ejemplo que todavía no estén en el menú. de ejemplo que todavía no estén en el menú.
-- Reviso cada platillo por su nombre para no duplicarlo. se agrega solo cuando todavía no existe otro con el mismo nombre.
-- También uso una subconsulta para encontrar el ID de la categoría y relacionarlo con el platillo. obtiene el ID de su categoría para guardar la relación correcta.
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
