-- ============================================================
-- Base de Datos: tecnosoluciones_db
-- Sistema de Gestión de Proyectos TecnoSoluciones S.A.
-- ============================================================

CREATE DATABASE IF NOT EXISTS tecnosoluciones_db
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE tecnosoluciones_db;

-- ============================================================
-- TABLA: usuarios
-- ============================================================
CREATE TABLE IF NOT EXISTS usuarios (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nombres VARCHAR(100) NOT NULL,
    apellidos VARCHAR(100) NOT NULL,
    usuario VARCHAR(50) NOT NULL UNIQUE,
    correo VARCHAR(120) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    rol ENUM('ADMIN','EMPLEADO','CLIENTE') NOT NULL,
    estado ENUM('ACTIVO','INACTIVO') DEFAULT 'ACTIVO',
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLA: clientes
-- ============================================================
CREATE TABLE IF NOT EXISTS clientes (
    id_cliente INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL UNIQUE,
    empresa VARCHAR(120) NULL,
    telefono VARCHAR(20) NULL,
    direccion VARCHAR(200) NULL,
    estado ENUM('ACTIVO','INACTIVO') DEFAULT 'ACTIVO',
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE,
    INDEX idx_id_usuario (id_usuario)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLA: empleados
-- ============================================================
CREATE TABLE IF NOT EXISTS empleados (
    id_empleado INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL UNIQUE,
    cargo VARCHAR(100) NULL,
    especialidad VARCHAR(120) NULL,
    estado ENUM('ACTIVO','INACTIVO') DEFAULT 'ACTIVO',
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE,
    INDEX idx_id_usuario (id_usuario)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLA: solicitudes_proyecto
-- ============================================================
CREATE TABLE IF NOT EXISTS solicitudes_proyecto (
    id_solicitud INT AUTO_INCREMENT PRIMARY KEY,
    id_cliente INT NOT NULL,
    titulo VARCHAR(150) NOT NULL,
    descripcion TEXT NOT NULL,
    tipo_sistema VARCHAR(120) NOT NULL,
    urgencia ENUM('BAJA','MEDIA','ALTA','URGENTE') DEFAULT 'MEDIA',
    estado ENUM('PENDIENTE','COTIZADO','ACEPTADO','RECHAZADO') DEFAULT 'PENDIENTE',
    motivo_rechazo TEXT NULL,
    precio_propuesto DECIMAL(10,2) NULL,
    fecha_solicitud TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_cliente) REFERENCES clientes(id_cliente) ON DELETE CASCADE,
    INDEX idx_id_cliente (id_cliente),
    INDEX idx_estado (estado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLA: proyectos
-- ============================================================
CREATE TABLE IF NOT EXISTS proyectos (
    id_proyecto INT AUTO_INCREMENT PRIMARY KEY,
    id_solicitud INT NOT NULL UNIQUE,
    titulo VARCHAR(150) NOT NULL,
    descripcion TEXT NOT NULL,
    prioridad ENUM('BAJA','MEDIA','ALTA','URGENTE') DEFAULT 'MEDIA',
    precio DECIMAL(10,2) NOT NULL DEFAULT 0,
    fecha_inicio DATE NULL,
    fecha_entrega DATE NULL,
    estado ENUM('EN_ESPERA','EN_PROGRESO','FINALIZADO','CANCELADO') DEFAULT 'EN_ESPERA',
    porcentaje_general INT DEFAULT 0,
    FOREIGN KEY (id_solicitud) REFERENCES solicitudes_proyecto(id_solicitud) ON DELETE CASCADE,
    INDEX idx_id_solicitud (id_solicitud),
    INDEX idx_estado (estado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLA: asignaciones_proyecto
-- ============================================================
CREATE TABLE IF NOT EXISTS asignaciones_proyecto (
    id_asignacion INT AUTO_INCREMENT PRIMARY KEY,
    id_proyecto INT NOT NULL,
    id_empleado INT NOT NULL,
    rol_en_proyecto VARCHAR(120) NULL,
    observacion VARCHAR(255) NULL,
    FOREIGN KEY (id_proyecto) REFERENCES proyectos(id_proyecto) ON DELETE CASCADE,
    FOREIGN KEY (id_empleado) REFERENCES empleados(id_empleado) ON DELETE CASCADE,
    INDEX idx_id_proyecto (id_proyecto),
    INDEX idx_id_empleado (id_empleado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLA: avances_proyecto
-- ============================================================
CREATE TABLE IF NOT EXISTS avances_proyecto (
    id_avance INT AUTO_INCREMENT PRIMARY KEY,
    id_proyecto INT NOT NULL,
    id_empleado INT NOT NULL,
    porcentaje INT NOT NULL,
    observacion TEXT NULL,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_proyecto) REFERENCES proyectos(id_proyecto) ON DELETE CASCADE,
    FOREIGN KEY (id_empleado) REFERENCES empleados(id_empleado) ON DELETE CASCADE,
    INDEX idx_id_proyecto (id_proyecto),
    INDEX idx_id_empleado (id_empleado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLA: reportes_pdf
-- ============================================================
CREATE TABLE IF NOT EXISTS reportes_pdf (
    id_reporte INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(150) NOT NULL,
    tipo_reporte VARCHAR(100) NOT NULL,
    ruta_archivo VARCHAR(255) NOT NULL,
    fecha_generacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_tipo_reporte (tipo_reporte)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLA: sesiones (Para hosts con cookies restringidas)
-- ============================================================
CREATE TABLE IF NOT EXISTS sesiones (
    id_sesion VARCHAR(64) PRIMARY KEY,
    datos LONGTEXT NOT NULL,
    creada_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expira_en TIMESTAMP NOT NULL,
    INDEX idx_expira_en (expira_en)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- DATOS DE PRUEBA
-- ============================================================

-- Administrador
INSERT INTO usuarios (nombres, apellidos, usuario, correo, password, rol) VALUES
('Admin', 'TecnoSoluciones', 'admin', 'admin@tecnosoluciones.com', '$2y$12$9n/8v9n8v9n8v9n8v9n8v9n8v9n8v9n8v9n8v9n8v9n8v9n8v9n8v9', 'ADMIN');

-- Cliente de prueba
INSERT INTO usuarios (nombres, apellidos, usuario, correo, password, rol) VALUES
('Juan', 'Pérez', 'jpperez', 'juan@email.com', '$2y$12$9n/8v9n8v9n8v9n8v9n8v9n8v9n8v9n8v9n8v9n8v9n8v9n8v9n8v9', 'CLIENTE');

-- Empleado de prueba
INSERT INTO usuarios (nombres, apellidos, usuario, correo, password, rol) VALUES
('Carlos', 'García', 'cgarcia', 'carlos@email.com', '$2y$12$9n/8v9n8v9n8v9n8v9n8v9n8v9n8v9n8v9n8v9n8v9n8v9n8v9n8v9', 'EMPLEADO');

-- Perfil de cliente
INSERT INTO clientes (id_usuario, empresa, telefono, direccion) VALUES
(2, 'Tech Company', '555-1234', 'Calle Principal 123, Ciudad');

-- Perfil de empleado
INSERT INTO empleados (id_usuario, cargo, especialidad) VALUES
(3, 'Desarrollador', 'Backend PHP');

-- ============================================================
-- FIN DEL SCRIPT SQL
-- ============================================================
