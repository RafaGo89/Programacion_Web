CREATE DATABASE login_db;

USE login_db;

CREATE TABLE estatus_usuario (
  id TINYINT UNSIGNED PRIMARY KEY,
  descripcion VARCHAR(50)
) ENGINE=InnoDB;

-- Insertamos los 5 estatus
INSERT INTO estatus_usuario (id, descripcion) VALUES
(1, 'Activo'),
(2, 'Inactivo'),
(3, 'Suspendido'),
(4, 'Pendiente'),
(5, 'Eliminado');

CREATE TABLE roles (
  id TINYINT UNSIGNED PRIMARY KEY,
  nombre VARCHAR(30) NOT NULL
) ENGINE=InnoDB;

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
) ENGINE=InnoDB;

-- Usuario 1: Activo, Administrador
-- Usuario 2: Suspendido, Moderador
-- Usuario 3: Pendiente, Invitado

INSERT INTO usuarios (nombres, a_paterno, a_materno, correo, password, estatus_id, rol_id)
VALUES 
('Ana María', 'González', 'Sánchez', 'ana.gonzalez@example.com', 'Copia aquí el hash que generes con el archivo password_hash', 1, 1),
('Luis Fernando', 'Martínez', 'Torres', 'luis.martinez@example.com', 'Copia aquí el hash que generes con el archivo password_hash', 3, 3),
('Carla Paola', 'Ramos', 'Delgado', 'carla.ramos@example.com', 'Copia aquí el hash que generes con el archivo password_hash', 4, 4);