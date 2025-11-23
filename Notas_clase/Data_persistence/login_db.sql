CREATE DATABASE login_db;

USE login_db;

CREATE TABLE estatus (
  id TINYINT UNSIGNED PRIMARY KEY,
  descripcion VARCHAR(50)
);

-- Insertamos los 5 estatus
INSERT INTO estatus (id, descripcion) VALUES
(1, 'Activo'),
(2, 'Inactivo'),
(3, 'Suspendido'),
(4, 'Pendiente'),
(5, 'Eliminado');

CREATE TABLE roles (
  id TINYINT UNSIGNED PRIMARY KEY,
  nombre VARCHAR(30) NOT NULL
);

-- Insertamos algunos roles comunes
INSERT INTO roles (id, nombre) VALUES
(1, 'Administrador'),
(2, 'Profesor'),
(3, 'Estudiante'),
(4, 'Invitado');

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombres VARCHAR(100) NOT NULL,
    a_paterno VARCHAR(50) NOT NULL,
    a_materno VARCHAR(50),
    correo VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    rol_id TINYINT UNSIGNED NOT NULL DEFAULT 2,
    FOREIGN KEY (rol_id) REFERENCES roles(id),
    estatus_id TINYINT UNSIGNED NOT NULL DEFAULT 1,
    FOREIGN KEY (estatus_id) REFERENCES estatus_usuario(id),
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP    
);

-- Usuario 1: Activo, Administrador
-- Usuario 2: Suspendido, Moderador
-- Usuario 3: Pendiente, Invitado

-- Credenciales --
-- Usuario: ana.gonzalez@example.com  | Contraseña: ana123
-- Usuario: luis.martinez@example.com | Contraseña: luis456
-- Usuario: carla.ramos@example.com   | Contraseña: carla789

INSERT INTO usuarios (nombres, a_paterno, a_materno, correo, password, estatus_id, rol_id)
VALUES 
('Ana María', 'González', 'Sánchez', 'ana.gonzalez@example.com', '$2y$10$NlYNhWHau4RmMQveeFqMVu2oSk5ivSqmt2fwfblu.SBkrcUmVUdrO', 1, 1),
('Luis Fernando', 'Martínez', 'Torres', 'luis.martinez@example.com', '$2y$10$c1tUJCGGMV7z9lOurnzMwufD0WVrlfmRcalCGxwmvDBYNqzlTKzwq', 3, 3),
('Carla Paola', 'Ramos', 'Delgado', 'carla.ramos@example.com', '$2y$10$p5hp4ppQnNwWbUeKd025SOb6YU4barEhHbb.C.AMwd4B6h1.ilcvm', 4, 4);