-- Si existe una base de datos con ese nombre la eliminamos
DROP DATABASE IF EXISTS integra;

CREATE DATABASE integra;

USE integra;

-- Tabla roles
CREATE TABLE roles (
	id TINYINT PRIMARY KEY AUTO_INCREMENT,
	nombre_rol VARCHAR(30) NOT NULL
);

-- Tabla Usuarios
CREATE TABLE usuarios (
	id INT PRIMARY KEY AUTO_INCREMENT,
	nombre VARCHAR(40) NOT NULL,
	a_paterno VARCHAR(50) NOT NULL,
	a_materno VARCHAR(50) NOT NULL,
	correo VARCHAR(100) NOT NULL UNIQUE,
	contrasena VARCHAR(255) NOT NULL,
	id_rol TINYINT NOT NULL,
	
	FOREIGN KEY (id_rol) REFERENCES roles(id)
	ON UPDATE CASCADE
	ON DELETE RESTRICT
);

-- Tabla materias
CREATE TABLE materias (
	id INT PRIMARY KEY AUTO_INCREMENT,
	nombre VARCHAR(50) NOT NULL,
	descripcion TEXT,
	fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL,
	id_profesor INT NOT NULL,
	
	FOREIGN KEY (id_profesor) REFERENCES usuarios(id)
	ON UPDATE CASCADE
	ON DELETE RESTRICT
);

-- Tabla solicitudes
CREATE TABLE solicitudes (
	id INT PRIMARY KEY AUTO_INCREMENT,
	id_alumno INT NOT NULL,
	id_materia INT NOT NULL,
	fecha_solicitud DATETIME DEFAULT CURRENT_TIMESTAMP,
	
	FOREIGN KEY (id_alumno) REFERENCES alumnos(id)
	ON UPDATE CASCADE
	ON DELETE CASCADE,
	FOREIGN KEY (id_materia) REFERENCES materias(id)
	ON UPDATE CASCADE
	ON DELETE CASCADE
);