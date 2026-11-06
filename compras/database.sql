CREATE DATABASE IF NOT EXISTS compras CHARACTER SET utf8mb4;
USE compras;

CREATE TABLE departamentos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL UNIQUE,
  estado TINYINT NOT NULL DEFAULT 1
);

CREATE TABLE empleados (
  id INT AUTO_INCREMENT PRIMARY KEY,
  cedula VARCHAR(20) NOT NULL UNIQUE,
  nombre VARCHAR(80) NOT NULL,
  apellido VARCHAR(80) NOT NULL,
  email VARCHAR(120) NULL,
  departamento_id INT NOT NULL,
  estado TINYINT NOT NULL DEFAULT 1,
  FOREIGN KEY (departamento_id) REFERENCES departamentos(id)
);

CREATE TABLE articulos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  codigo VARCHAR(30) NOT NULL UNIQUE,
  nombre VARCHAR(120) NOT NULL,
  marca VARCHAR(80) NULL,
  unidad VARCHAR(20) NOT NULL,
  existencia INT NOT NULL DEFAULT 0,
  estado TINYINT NOT NULL DEFAULT 1
);

CREATE TABLE proveedores (
  id INT AUTO_INCREMENT PRIMARY KEY,
  rnc VARCHAR(15) NOT NULL UNIQUE,
  nombre VARCHAR(120) NOT NULL,
  telefono VARCHAR(20) NULL,
  email VARCHAR(120) NULL,
  estado TINYINT NOT NULL DEFAULT 1
);

INSERT INTO departamentos (nombre) VALUES ('Tecnología'),('Recursos Humanos'),('Contabilidad'),('Operaciones');
INSERT INTO articulos (codigo,nombre,marca,unidad,existencia) VALUES
('PAP-001','Resma de papel carta','Xerox','Paquete',85),
('TEC-001','Teclado USB','Logitech','Unidad',40),
('HER-001','Destornillador','Stanley','Unidad',25);
