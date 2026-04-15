-- CREACIÓN
CREATE DATABASE puntoamigo;
GO

USE puntoamigo;
GO

CREATE TABLE usuarios (
    id INT IDENTITY(1,1) PRIMARY KEY,
    nombre VARCHAR(100),
    correo VARCHAR(100),
    edad INT
);

CREATE TABLE servicios (
    id INT IDENTITY(1,1) PRIMARY KEY,
    nombre VARCHAR(100),
    precio DECIMAL(10,2),
    id_usuario INT,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id)
);

-- INSERCIONES
INSERT INTO usuarios (nombre, correo, edad) VALUES
('Juan', 'juan@gmail.com', 20),
('Maria', 'maria@gmail.com', 25),
('Carlos', 'carlos@gmail.com', 30),
('Ana', 'ana@gmail.com', 22),
('Luis', 'luis@gmail.com', 28);

INSERT INTO servicios (nombre, precio, id_usuario) VALUES
('Diseño web', 500, 1),
('Programación', 800, 2),
('Soporte técnico', 300, 3),
('Marketing digital', 600, 4),
('SEO', 400, 5);

-- ACTUALIZACIONES
UPDATE usuarios SET edad = 21 WHERE id = 1;
UPDATE usuarios SET correo = 'nuevo_maria@gmail.com' WHERE id = 2;

UPDATE servicios SET precio = 550 WHERE id = 1;
UPDATE servicios SET nombre = 'Desarrollo de software' WHERE id = 2;

-- ELIMINACIONES
DELETE FROM servicios WHERE id = 5;
DELETE FROM servicios WHERE id = 4;

DELETE FROM usuarios WHERE id = 5;
DELETE FROM usuarios WHERE id = 4;

-- CONSULTAS
SELECT * FROM usuarios WHERE edad > 23;
SELECT nombre, correo FROM usuarios WHERE edad < 30;

SELECT * FROM servicios WHERE precio > 400;
SELECT nombre, precio FROM servicios WHERE precio < 600;

-- JOIN
SELECT u.nombre, s.nombre AS servicio, s.precio
FROM usuarios u
JOIN servicios s ON u.id = s.id_usuario;