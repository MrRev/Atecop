-- ====================================================
-- Script de Creación de Base de Datos - Sistema ATECOP
-- Versión: 1.0
-- Base de Datos: MySQL 8.0+
-- Optimizado para phpMyAdmin
-- ====================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- Crear y seleccionar base de datos
DROP DATABASE IF EXISTS atecop_db;
CREATE DATABASE IF NOT EXISTS atecop_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE atecop_db;

-- ====================================================
-- TABLAS CATÁLOGO (DE SOPORTE)
-- ====================================================

-- Tabla: tiposocio
DROP TABLE IF EXISTS tiposocio;
CREATE TABLE tiposocio (
    idtiposocio INT AUTO_INCREMENT PRIMARY KEY,
    nombretipo VARCHAR(100) NOT NULL UNIQUE COMMENT 'Ej: Fundador, Efectivo, Honorario, Institucional, Asociado',
    descripcion TEXT,
    INDEX idx_nombretipo (nombretipo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tipos de socio según estatutos';

-- Tabla: profesion
DROP TABLE IF EXISTS profesion;
CREATE TABLE profesion (
    idprofesion INT AUTO_INCREMENT PRIMARY KEY,
    nombreprofesion VARCHAR(150) NOT NULL UNIQUE COMMENT 'Ej: Ingeniero Civil, Arquitecto, Abogado',
    INDEX idx_nombreprofesion (nombreprofesion)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Catálogo de profesiones';

-- Tabla: metodopago
DROP TABLE IF EXISTS metodopago;
CREATE TABLE metodopago (
    idmetodopago INT AUTO_INCREMENT PRIMARY KEY,
    nombremetodo VARCHAR(50) NOT NULL UNIQUE COMMENT 'Ej: Efectivo, Yape, Transferencia Bancaria, PLIN'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Métodos de pago aceptados';

-- ====================================================
-- TABLAS PRINCIPALES (CORE)
-- ====================================================

-- Tabla: planmembresia
DROP TABLE IF EXISTS planmembresia;
CREATE TABLE planmembresia (
    idplan INT AUTO_INCREMENT PRIMARY KEY,
    nombreplan VARCHAR(100) NOT NULL UNIQUE,
    duracionmeses INT NOT NULL COMMENT 'Ej: 1 (Mensual), 3 (Trimestral), 12 (Anual)',
    costo DECIMAL(10, 2) NOT NULL DEFAULT 0,
    estado VARCHAR(20) NOT NULL DEFAULT 'Activo' COMMENT 'Activo / Inactivo',
    INDEX idx_estado (estado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Planes de membresía';

-- Tabla: ponente
DROP TABLE IF EXISTS ponente;
CREATE TABLE ponente (
    idponente INT AUTO_INCREMENT PRIMARY KEY,
    nombrecompleto VARCHAR(255) NOT NULL,
    dni VARCHAR(15) UNIQUE COMMENT 'DNI del ponente',
    email VARCHAR(100) UNIQUE,
    telefono VARCHAR(20),
    estado VARCHAR(20) NOT NULL DEFAULT 'Activo' COMMENT 'Activo / Inactivo',
    idprofesion INT,
    FOREIGN KEY (idprofesion) REFERENCES profesion(idprofesion) ON DELETE SET NULL,
    INDEX idx_dni (dni),
    INDEX idx_estado (estado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Ponentes de cursos';

-- Tabla: socio
DROP TABLE IF EXISTS socio;
CREATE TABLE socio (
    idsocio INT AUTO_INCREMENT PRIMARY KEY,
    dni VARCHAR(15) NOT NULL UNIQUE COMMENT 'DNI (8 dígitos) o RUC (11 dígitos)',
    nombrecompleto VARCHAR(255) NOT NULL,
    fechanacimiento DATE,
    direccion VARCHAR(255),
    email VARCHAR(100) UNIQUE COMMENT 'Email único',
    telefono VARCHAR(20),
    numcuentabancaria VARCHAR(50) COMMENT 'Número de cuenta bancaria',
    estado VARCHAR(20) NOT NULL DEFAULT 'Activo' COMMENT 'Activo, Moroso, Inactivo, Pendiente',
    fechavencimiento DATE NOT NULL COMMENT 'Fecha del próximo vencimiento de membresía',
    fechacreacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha de registro',
    idtiposocio INT NOT NULL,
    idplan INT NOT NULL,
    idprofesion INT,
    FOREIGN KEY (idtiposocio) REFERENCES tiposocio(idtiposocio) ON DELETE RESTRICT,
    FOREIGN KEY (idplan) REFERENCES planmembresia(idplan) ON DELETE RESTRICT,
    FOREIGN KEY (idprofesion) REFERENCES profesion(idprofesion) ON DELETE SET NULL,
    INDEX idx_socio_dni (dni),
    INDEX idx_socio_estado (estado),
    INDEX idx_socio_vencimiento (fechavencimiento)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Socios de ATECOP';

-- Tabla: pago
DROP TABLE IF EXISTS pago;
CREATE TABLE pago (
    idpago INT AUTO_INCREMENT PRIMARY KEY,
    monto DECIMAL(10, 2) NOT NULL,
    fechapago DATE NOT NULL,
    concepto VARCHAR(255) COMMENT 'Ej: Pago Cuota Anual 2025',
    urlcomprobante VARCHAR(255) COMMENT 'Ruta al archivo del comprobante',
    estado VARCHAR(20) NOT NULL DEFAULT 'Registrado' COMMENT 'Registrado / Anulado',
    idsocio INT NOT NULL,
    idmetodopago INT NOT NULL,
    FOREIGN KEY (idsocio) REFERENCES socio(idsocio) ON DELETE CASCADE,
    FOREIGN KEY (idmetodopago) REFERENCES metodopago(idmetodopago) ON DELETE RESTRICT,
    INDEX idx_pago_socio (idsocio),
    INDEX idx_pago_fecha (fechapago),
    INDEX idx_pago_estado (estado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Registro de pagos';

-- Tabla: curso
DROP TABLE IF EXISTS curso;
CREATE TABLE curso (
    idcurso INT AUTO_INCREMENT PRIMARY KEY,
    nombrecurso VARCHAR(255) NOT NULL,
    descripcion TEXT,
    cupostotales INT NOT NULL DEFAULT 0,
    costoinscripcion DECIMAL(10, 2) NOT NULL DEFAULT 0,
    fechainicio DATE COMMENT 'Fecha de inicio',
    fechafin DATE COMMENT 'Fecha de fin',
    urlenlacevirtual VARCHAR(255) COMMENT 'Enlace virtual (Zoom, Meet)',
    estado VARCHAR(20) NOT NULL DEFAULT 'Programado' COMMENT 'Programado, En Curso, Finalizado, Cancelado',
    idponente INT,
    FOREIGN KEY (idponente) REFERENCES ponente(idponente) ON DELETE SET NULL,
    INDEX idx_curso_fechainicio (fechainicio),
    INDEX idx_curso_estado (estado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Cursos ofrecidos';

-- ====================================================
-- TABLAS ASOCIATIVAS
-- ====================================================

-- Tabla: cursoinscrito
DROP TABLE IF EXISTS cursoinscrito;
CREATE TABLE cursoinscrito (
    idsocio INT NOT NULL,
    idcurso INT NOT NULL,
    fechainscripcion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    estadopagocurso VARCHAR(20) NOT NULL DEFAULT 'Pendiente' COMMENT 'Pendiente / Pagado / Exonerado',
    PRIMARY KEY (idsocio, idcurso),
    FOREIGN KEY (idsocio) REFERENCES socio(idsocio) ON DELETE CASCADE,
    FOREIGN KEY (idcurso) REFERENCES curso(idcurso) ON DELETE CASCADE,
    INDEX idx_cursoinscrito_socio (idsocio),
    INDEX idx_cursoinscrito_curso (idcurso)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Inscripciones de socios a cursos';

-- ====================================================
-- TABLAS DE SISTEMA
-- ====================================================

-- Tabla: administrador
DROP TABLE IF EXISTS administrador;
CREATE TABLE administrador (
    idadmin INT AUTO_INCREMENT PRIMARY KEY,
    usuario VARCHAR(50) NOT NULL UNIQUE,
    clavehash VARCHAR(255) NOT NULL COMMENT 'Contraseña hasheada con password_hash()',
    nombrecompleto VARCHAR(150),
    estado VARCHAR(20) NOT NULL DEFAULT 'Activo' COMMENT 'Activo / Inactivo',
    INDEX idx_admin_usuario (usuario)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Administradores del sistema';

-- Tabla: usuario
DROP TABLE IF EXISTS usuario;
CREATE TABLE usuario (
    idusuario INT AUTO_INCREMENT PRIMARY KEY,
    dni VARCHAR(15) NOT NULL UNIQUE COMMENT 'DNI validado por API',
    nombrecompleto VARCHAR(255) NOT NULL COMMENT 'Nombre completo del usuario (no editable, validado por API)',
    nombreusuario VARCHAR(100) NOT NULL UNIQUE COMMENT 'Formato: primer_nombre primer_apellido',
    email VARCHAR(100) UNIQUE,
    telefono VARCHAR(20),
    clavehash VARCHAR(255) NOT NULL COMMENT 'Contraseña hasheada con password_hash()',
    direccion VARCHAR(255),
    rol VARCHAR(50) NOT NULL DEFAULT 'administrador' COMMENT 'Por defecto administrador, preparado para roles futuros',
    idsocio INT NULL COMMENT 'Relación opcional con socio',
    estado VARCHAR(20) NOT NULL DEFAULT 'Activo' COMMENT 'Activo / Inactivo',
    fechacreacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fechamodificacion TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (idsocio) REFERENCES socio(idsocio) ON DELETE SET NULL,
    INDEX idx_usuario_dni (dni),
    INDEX idx_usuario_nombreusuario (nombreusuario),
    INDEX idx_usuario_email (email),
    INDEX idx_usuario_rol (rol),
    INDEX idx_usuario_estado (estado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Usuarios del sistema con validación de DNI y roles';

-- Tabla: configuracion
DROP TABLE IF EXISTS configuracion;
CREATE TABLE configuracion (
    llave VARCHAR(50) PRIMARY KEY COMMENT 'Nombre de la configuración',
    valor VARCHAR(255) NOT NULL COMMENT 'Valor de la configuración'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Configuraciones del sistema (clave-valor)';

-- ====================================================
-- DATOS INICIALES
-- ====================================================

-- Insertar tipos de socio
INSERT INTO tiposocio (nombretipo, descripcion) VALUES
('Fundador', 'Socio fundador de ATECOP'),
('Efectivo', 'Socio efectivo con todos los derechos'),
('Honorario', 'Socio honorario por méritos especiales'),
('Institucional', 'Institución asociada'),
('Asociado', 'Socio asociado');

-- Insertar profesiones comunes
INSERT INTO profesion (nombreprofesion) VALUES
('Ingeniero Civil'),
('Arquitecto'),
('Ingeniero Industrial'),
('Ingeniero Mecánico'),
('Ingeniero Eléctrico'),
('Abogado'),
('Contador Público'),
('Administrador'),
('Economista'),
('Otro');

-- Insertar métodos de pago
INSERT INTO metodopago (nombremetodo) VALUES
('Efectivo'),
('Yape'),
('Transferencia Bancaria'),
('PLIN'),
('Depósito Bancario');

-- Insertar planes de membresía por defecto
INSERT INTO planmembresia (nombreplan, duracionmeses, costo, estado) VALUES
('Mensual', 1, 50.00, 'Activo'),
('Trimestral', 3, 135.00, 'Activo'),
('Semestral', 6, 255.00, 'Activo'),
('Anual', 12, 480.00, 'Activo');

-- Insertar administrador por defecto (usuario: admin, contraseña: admin123)
INSERT INTO administrador (usuario, clavehash, nombrecompleto, estado) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrador Principal', 'Activo');

-- Insertar configuraciones del sistema
INSERT INTO configuracion (llave, valor) VALUES
('API_KEY_PERUDEV', 'tu_api_key_aqui'),
('IGV_ACTUAL', '0.18'),
('NOMBRE_SISTEMA', 'Sistema de Gestión ATECOP'),
('VERSION_SISTEMA', '1.0.0');

-- Insertar usuario administrador por defecto
-- Usuario: mimi
-- Contraseña: 123
-- DNI y nombre inventados pero válidos
INSERT INTO usuario (
    dni,
    nombrecompleto,
    nombreusuario,
    email,
    telefono,
    clavehash,
    direccion,
    rol,
    estado
) VALUES (
    '45678912',
    'Miriam Flores Castillo',
    'mimi',
    'mimi@atecop.com',
    '987654321',
    '$2y$10$4feLCdBI5vPmdwZZRKDkoOTLbrlbezc5.hcyF.Xip76.f/o8xv9wy', -- hash de '123'
    'Av. Los Ingenieros 123, Lima',
    'administrador',
    'Activo'
);
INSERT INTO administrador (usuario, clavehash, nombrecompleto, estado)
VALUES (
  'mimi',
  '$2y$10$4feLCdBI5vPmdwZZRKDkoOTLbrlbezc5.hcyF.Xip76.f/o8xv9wy',
  'Miriam Flores Castillo',
  'Activo'
);

COMMIT;

-- ====================================================
-- FIN DEL SCRIPT
-- ====================================================