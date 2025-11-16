-- Si existe una base de datos con ese nombre la eliminamos
DROP DATABASE IF EXISTS centro_integra;

CREATE DATABASE centro_integra;

USE centro_integra;

-- Tabla roles
-- Almacena los roles posibles de los usuarios
CREATE TABLE roles (
	id TINYINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
	nombre VARCHAR(30) NOT NULL
) ENGINE=InnoDB;

INSERT INTO roles (nombre) VALUES
('Aministrador'),
('Profesor'),
('Estudiante'),
('Invitado');

-- Tabla Usuarios
-- Almacena información de nuestros usuarios
CREATE TABLE usuarios (
	id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
	nombres VARCHAR(40) NOT NULL,
	a_paterno VARCHAR(50) NOT NULL,
	a_materno VARCHAR(50) NOT NULL,
	correo VARCHAR(100) NOT NULL UNIQUE,
	contrasena VARCHAR(255) NOT NULL,
	id_rol TINYINT UNSIGNED NOT NULL,
	fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL,
	fecha_modificacion DATETIME,
	
	FOREIGN KEY (id_rol) REFERENCES roles(id)
	ON UPDATE CASCADE
	ON DELETE RESTRICT
) ENGINE=INNODB;

-- Tabla materias
-- Guarda información acerca de las materias y que profesor la imparté
CREATE TABLE materias (
	id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
	nombre VARCHAR(50) NOT NULL,
	descripcion TEXT,
	id_profesor INT UNSIGNED NOT NULL,
	fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL,
	fecha_modificacion DATETIME,
	
	FOREIGN KEY (id_profesor) REFERENCES usuarios(id)
	ON UPDATE CASCADE
	ON DELETE RESTRICT
) ENGINE=InnoDB;

-- Tabla solicitudes
-- Almacena las solicitudes de los alumnos a una materia en especifico
CREATE TABLE solicitudes (
	id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
	id_alumno INT UNSIGNED NOT NULL,
	id_materia INT UNSIGNED NOT NULL,
	estado ENUM('Pendiente', 'Aprobado', 'Rechazado') DEFAULT 'Pendiente' NOT NULL,
	fecha_solicitud DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL,
	
	FOREIGN KEY (id_alumno) REFERENCES usuarios(id)
	ON UPDATE CASCADE
	ON DELETE CASCADE,
	FOREIGN KEY (id_materia) REFERENCES materias(id)
	ON UPDATE CASCADE
	ON DELETE CASCADE
) ENGINE=INNODB;

-- Tabla tareas
-- Almacena información de las tareas que los profesores crean
CREATE TABLE tareas (
	id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
	id_materia INT UNSIGNED NOT NULL,
	titulo VARCHAR(100) NOT NULL,
	descripcion TEXT,
	fecha_limite DATETIME NOT NULL,
	ponderacion DECIMAL(5,2) NOT NULL COMMENT 'en porcentaje', -- 20.00%
	fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL,
	fecha_modificacion DATETIME,
	
	FOREIGN KEY (id_materia) REFERENCES materias(id)
	ON UPDATE CASCADE
	ON DELETE RESTRICT
) ENGINE=INNODB;

-- Tabla calificaciones
-- Esta tabla guarda la nota del alumno para una tarea específica
CREATE TABLE calificaciones (
	id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
	id_tarea INT UNSIGNED NOT NULL,
	id_alumno INT UNSIGNED NOT NULL,
	calificacion DECIMAL(5,2) NOT NULL,
	fecha_entrega DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL,
	
	FOREIGN KEY (id_tarea) REFERENCES tareas(id)
	ON UPDATE CASCADE
	ON DELETE CASCADE,
	FOREIGN KEY (id_alumno) REFERENCES usuarios(id)
	ON UPDATE CASCADE
	ON DELETE CASCADE
) ENGINE=INNODB;