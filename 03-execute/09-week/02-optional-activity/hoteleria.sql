-- Script de Creación de Base de Datos: Sistema de Hotelería
-- Generado según la estructura de módulos y entidades proporcionada.

CREATE DATABASE IF NOT EXISTS sistema_hoteleria;
USE sistema_hoteleria;

-- ==========================================
-- 7. MÓDULO: SEGURIDAD (Base para auditoría)
-- ==========================================

CREATE TABLE persona (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    tipo_documento VARCHAR(20) NOT NULL,
    numero_documento VARCHAR(50) NOT NULL UNIQUE,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    telefono VARCHAR(20),
    correo VARCHAR(100),
    -- Auditoría
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
    status VARCHAR(30) NOT NULL DEFAULT 'ACTIVE'
);

CREATE TABLE usuario (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    persona_id BIGINT UNSIGNED NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    ultimo_acceso DATETIME,
    bloqueado BOOLEAN DEFAULT FALSE,
    -- Auditoría
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
    status VARCHAR(30) NOT NULL DEFAULT 'ACTIVE',
    CONSTRAINT fk_usuario_persona FOREIGN KEY (persona_id) REFERENCES persona(id)
);

CREATE TABLE rol (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    descripcion TEXT,
    status VARCHAR(30) NOT NULL DEFAULT 'ACTIVE'
);

CREATE TABLE usuario_rol (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    usuario_id BIGINT UNSIGNED NOT NULL,
    rol_id BIGINT UNSIGNED NOT NULL,
    CONSTRAINT fk_userrol_user FOREIGN KEY (usuario_id) REFERENCES usuario(id),
    CONSTRAINT fk_userrol_rol FOREIGN KEY (rol_id) REFERENCES rol(id)
);

-- ==========================================
-- 1. MÓDULO: PARAMETRIZACIÓN
-- ==========================================

CREATE TABLE empresa (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    nit VARCHAR(20) NOT NULL UNIQUE,
    razon_social VARCHAR(150),
    telefono VARCHAR(20),
    correo VARCHAR(100),
    direccion VARCHAR(255),
    sitio_web VARCHAR(100),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    status VARCHAR(30) DEFAULT 'ACTIVE'
);

CREATE TABLE cliente (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    tipo_documento VARCHAR(20) NOT NULL,
    numero_documento VARCHAR(50) NOT NULL UNIQUE,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    telefono VARCHAR(20),
    correo VARCHAR(100),
    direccion VARCHAR(255),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    status VARCHAR(30) DEFAULT 'ACTIVE'
);

CREATE TABLE empleado (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    persona_id BIGINT UNSIGNED NOT NULL,
    cargo VARCHAR(100),
    fecha_ingreso DATE,
    telefono_laboral VARCHAR(20),
    correo_laboral VARCHAR(100),
    CONSTRAINT fk_empleado_persona FOREIGN KEY (persona_id) REFERENCES persona(id)
);

CREATE TABLE tipo_dia (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50),
    descripcion TEXT,
    fecha DATE,
    aplica_temporada BOOLEAN,
    aplica_feriado BOOLEAN,
    aplica_especial BOOLEAN
);

CREATE TABLE metodo_pago (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    descripcion TEXT,
    requiere_referencia BOOLEAN,
    permite_pago_parcial BOOLEAN
);

-- ==========================================
-- 2. MÓDULO: DISTRIBUCIÓN
-- ==========================================

CREATE TABLE sede (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    empresa_id BIGINT UNSIGNED NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    direccion VARCHAR(255),
    ciudad VARCHAR(100),
    telefono VARCHAR(20),
    correo VARCHAR(100),
    CONSTRAINT fk_sede_empresa FOREIGN KEY (empresa_id) REFERENCES empresa(id)
);

CREATE TABLE tipo_habitacion (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    descripcion TEXT,
    capacidad_base INT,
    capacidad_maxima INT
);

CREATE TABLE estado_habitacion (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    descripcion TEXT,
    permite_reserva BOOLEAN,
    permite_check_in BOOLEAN
);

CREATE TABLE habitacion (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    sede_id BIGINT UNSIGNED NOT NULL,
    tipo_habitacion_id BIGINT UNSIGNED NOT NULL,
    estado_habitacion_id BIGINT UNSIGNED NOT NULL,
    numero VARCHAR(10) NOT NULL,
    piso INT,
    capacidad INT,
    descripcion TEXT,
    CONSTRAINT fk_hab_sede FOREIGN KEY (sede_id) REFERENCES sede(id),
    CONSTRAINT fk_hab_tipo FOREIGN KEY (tipo_habitacion_id) REFERENCES tipo_habitacion(id),
    CONSTRAINT fk_hab_estado FOREIGN KEY (estado_habitacion_id) REFERENCES estado_habitacion(id)
);

-- Precio dinámico (vinculado a tipo_habitacion y tipo_dia)
CREATE TABLE precio (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    tipo_habitacion_id BIGINT UNSIGNED NOT NULL,
    tipo_dia_id BIGINT UNSIGNED NOT NULL,
    valor DECIMAL(12,2) NOT NULL,
    fecha_inicio DATE,
    fecha_fin DATE,
    condicion TEXT,
    CONSTRAINT fk_precio_tipo FOREIGN KEY (tipo_habitacion_id) REFERENCES tipo_habitacion(id),
    CONSTRAINT fk_precio_dia FOREIGN KEY (tipo_dia_id) REFERENCES tipo_dia(id)
);

-- ==========================================
-- 3. MÓDULO: PRESTACIÓN DE SERVICIO
-- ==========================================

CREATE TABLE reserva_habitacion (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    cliente_id BIGINT UNSIGNED NOT NULL,
    habitacion_id BIGINT UNSIGNED NOT NULL,
    fecha_inicio DATE NOT NULL,
    fecha_fin DATE NOT NULL,
    cantidad_persona INT,
    estado_reserva VARCHAR(30),
    valor_estimado DECIMAL(12,2),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_reserva_cliente FOREIGN KEY (cliente_id) REFERENCES cliente(id),
    CONSTRAINT fk_reserva_habitacion FOREIGN KEY (habitacion_id) REFERENCES habitacion(id)
);

CREATE TABLE estadia (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    reserva_habitacion_id BIGINT UNSIGNED NOT NULL,
    cliente_id BIGINT UNSIGNED NOT NULL,
    habitacion_id BIGINT UNSIGNED NOT NULL,
    fecha_inicio DATE,
    fecha_fin DATE,
    estado_estadia VARCHAR(30),
    CONSTRAINT fk_estadia_reserva FOREIGN KEY (reserva_habitacion_id) REFERENCES reserva_habitacion(id),
    CONSTRAINT fk_estadia_cliente FOREIGN KEY (cliente_id) REFERENCES cliente(id),
    CONSTRAINT fk_estadia_hab FOREIGN KEY (habitacion_id) REFERENCES habitacion(id)
);

CREATE TABLE check_in (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    reserva_habitacion_id BIGINT UNSIGNED NOT NULL,
    empleado_id BIGINT UNSIGNED NOT NULL,
    fecha_hora_ingreso DATETIME NOT NULL,
    observacion TEXT,
    CONSTRAINT fk_ci_reserva FOREIGN KEY (reserva_habitacion_id) REFERENCES reserva_habitacion(id),
    CONSTRAINT fk_ci_empleado FOREIGN KEY (empleado_id) REFERENCES empleado(id)
);

CREATE TABLE check_out (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    estadia_id BIGINT UNSIGNED NOT NULL,
    empleado_id BIGINT UNSIGNED NOT NULL,
    fecha_hora_salida DATETIME NOT NULL,
    observacion TEXT,
    valor_total DECIMAL(12,2),
    CONSTRAINT fk_co_estadia FOREIGN KEY (estadia_id) REFERENCES estadia(id),
    CONSTRAINT fk_co_empleado FOREIGN KEY (empleado_id) REFERENCES empleado(id)
);

-- ==========================================
-- 5. MÓDULO: INVENTARIO
-- ==========================================

CREATE TABLE proveedor (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    nit VARCHAR(20) UNIQUE,
    telefono VARCHAR(20),
    correo VARCHAR(100),
    direccion VARCHAR(255)
);

CREATE TABLE producto (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    proveedor_id BIGINT UNSIGNED NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    valor_venta DECIMAL(12,2),
    stock_actual INT,
    stock_minimo INT,
    CONSTRAINT fk_prod_prov FOREIGN KEY (proveedor_id) REFERENCES proveedor(id)
);

CREATE TABLE servicio (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    valor_venta DECIMAL(12,2),
    disponible BOOLEAN DEFAULT TRUE
);

-- ==========================================
-- 4. MÓDULO: FACTURACIÓN
-- ==========================================

CREATE TABLE factura (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    cliente_id BIGINT UNSIGNED NOT NULL,
    estadia_id BIGINT UNSIGNED,
    numero_factura VARCHAR(50) NOT NULL UNIQUE,
    fecha_emision DATETIME DEFAULT CURRENT_TIMESTAMP,
    subtotal DECIMAL(12,2),
    impuesto DECIMAL(12,2),
    descuento DECIMAL(12,2),
    total DECIMAL(12,2),
    estado_factura VARCHAR(30),
    CONSTRAINT fk_fact_cliente FOREIGN KEY (cliente_id) REFERENCES cliente(id),
    CONSTRAINT fk_fact_estadia FOREIGN KEY (estadia_id) REFERENCES estadia(id)
);

CREATE TABLE detalle_compra (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    factura_id BIGINT UNSIGNED NOT NULL,
    producto_id BIGINT UNSIGNED,
    servicio_id BIGINT UNSIGNED,
    descripcion TEXT,
    cantidad INT,
    valor_unitario DECIMAL(12,2),
    valor_total DECIMAL(12,2),
    CONSTRAINT fk_det_factura FOREIGN KEY (factura_id) REFERENCES factura(id),
    CONSTRAINT fk_det_prod FOREIGN KEY (producto_id) REFERENCES producto(id),
    CONSTRAINT fk_det_serv FOREIGN KEY (servicio_id) REFERENCES servicio(id)
);

CREATE TABLE pago_parcial (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    reserva_habitacion_id BIGINT UNSIGNED,
    factura_id BIGINT UNSIGNED,
    metodo_pago_id BIGINT UNSIGNED NOT NULL,
    valor DECIMAL(12,2) NOT NULL,
    fecha_pago DATETIME DEFAULT CURRENT_TIMESTAMP,
    referencia_pago VARCHAR(100),
    CONSTRAINT fk_pago_reserva FOREIGN KEY (reserva_habitacion_id) REFERENCES reserva_habitacion(id),
    CONSTRAINT fk_pago_factura FOREIGN KEY (factura_id) REFERENCES factura(id),
    CONSTRAINT fk_pago_metodo FOREIGN KEY (metodo_pago_id) REFERENCES metodo_pago(id)
);

-- ==========================================
-- 8. MÓDULO: MANTENIMIENTO
-- ==========================================

CREATE TABLE mantenimiento_habitacion (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    habitacion_id BIGINT UNSIGNED NOT NULL,
    empleado_id BIGINT UNSIGNED NOT NULL,
    tipo_mantenimiento VARCHAR(50), -- Uso o Remodelación
    fecha_inicio DATETIME,
    fecha_fin DATETIME,
    estado_mantenimiento VARCHAR(30),
    observacion TEXT,
    CONSTRAINT fk_mant_hab FOREIGN KEY (habitacion_id) REFERENCES habitacion(id),
    CONSTRAINT fk_mant_emp FOREIGN KEY (empleado_id) REFERENCES empleado(id)
);

-- ==========================================
-- 6. MÓDULO: NOTIFICACIÓN
-- ==========================================

CREATE TABLE alerta (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    cliente_id BIGINT UNSIGNED NOT NULL,
    reserva_habitacion_id BIGINT UNSIGNED,
    titulo VARCHAR(100),
    mensaje TEXT,
    canal VARCHAR(50), -- Email, SMS, App
    fecha_envio DATETIME,
    CONSTRAINT fk_alerta_cli FOREIGN KEY (cliente_id) REFERENCES cliente(id)
);
