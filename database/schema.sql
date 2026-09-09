-- Portal Ciudadano - Esquema de base de datos
-- Ejecutar una vez en el hosting: mysql -u usuario -p < schema.sql

CREATE DATABASE IF NOT EXISTS portal_ciudadano
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE portal_ciudadano;

CREATE TABLE IF NOT EXISTS categorias (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL,
  descripcion VARCHAR(255) DEFAULT NULL,
  activo TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB;

INSERT INTO categorias (nombre, descripcion) VALUES
  ('Seguridad', 'Hechos delictivos, violencia o inseguridad'),
  ('Servicios públicos', 'Fallas en luz, agua, gas, recolección de residuos'),
  ('Vía pública', 'Baches, semáforos, alumbrado, señalización'),
  ('Corrupción', 'Uso indebido de fondos o funciones públicas'),
  ('Medio ambiente', 'Contaminación, maltrato animal, deforestación'),
  ('Consumidor', 'Estafas, incumplimientos comerciales'),
  ('Otro', 'Cualquier otro motivo no listado');

CREATE TABLE IF NOT EXISTS denuncias (
  id INT AUTO_INCREMENT PRIMARY KEY,
  codigo_seguimiento VARCHAR(20) NOT NULL UNIQUE,
  categoria_id INT NOT NULL,
  es_anonima TINYINT(1) NOT NULL DEFAULT 0,
  nombre VARCHAR(150) DEFAULT NULL,
  email VARCHAR(150) DEFAULT NULL,
  telefono VARCHAR(50) DEFAULT NULL,
  ubicacion VARCHAR(255) DEFAULT NULL,
  fecha_hecho DATE DEFAULT NULL,
  descripcion TEXT NOT NULL,
  adjunto_path VARCHAR(255) DEFAULT NULL,
  estado ENUM('pendiente','en_revision','resuelta','rechazada') NOT NULL DEFAULT 'pendiente',
  notas_admin TEXT DEFAULT NULL,
  ip_origen VARCHAR(45) DEFAULT NULL,
  creado_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  actualizado_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (categoria_id) REFERENCES categorias(id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS admins (
  id INT AUTO_INCREMENT PRIMARY KEY,
  usuario VARCHAR(100) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  nombre_completo VARCHAR(150) DEFAULT NULL,
  creado_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Usuario admin por defecto: admin / CambiarEsta123!
-- (el hash se genera con password_hash en PHP; ver instrucciones en README)
