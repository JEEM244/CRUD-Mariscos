CREATE DATABASE IF NOT EXISTS mariscos_db
CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE mariscos_db;

CREATE TABLE IF NOT EXISTS categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(80) NOT NULL
);

CREATE TABLE IF NOT EXISTS platillos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(120) NOT NULL,
    descripcion TEXT,
    precio DECIMAL(10,2) NOT NULL,
    imagen VARCHAR(500),
    categoria_id INT,
    disponible TINYINT(1) DEFAULT 1,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (categoria_id) REFERENCES categorias(id)
        ON DELETE SET NULL
);

INSERT INTO categorias (nombre) VALUES
('Ceviches'), ('Cócteles'), ('Tostadas'), ('Pescados'), ('Camarones'), ('Bebidas');

INSERT INTO platillos (nombre, descripcion, precio, imagen, categoria_id) VALUES
('Ceviche de pescado', 'Pescado fresco preparado con limón, tomate, cebolla y cilantro.', 115.00, '', 1),
('Cóctel de camarón', 'Camarones con salsa coctelera, aguacate, cebolla y cilantro.', 145.00, '', 2),
('Tostadas de atún', 'Atún fresco con aguacate y un toque de limón.', 95.00, '', 3),
('Filete empanizado', 'Filete de pescado acompañado de ensalada y arroz.', 165.00, '', 4),
('Camarones al ajillo', 'Camarones salteados con ajo y mantequilla.', 185.00, '', 5),
('Agua de limón', 'Agua fresca preparada al momento.', 35.00, '', 6);
