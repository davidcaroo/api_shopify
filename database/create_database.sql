-- ================================================
-- Script de Creación de Base de Datos
-- API Shopify CRUD - Base de Datos de Soporte
-- ================================================
-- Crear la base de datos
CREATE DATABASE IF NOT EXISTS `shopify_api_db` CHARACTER
SET
    utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Usar la base de datos
USE `shopify_api_db`;

-- ================================================
-- Tabla de Productos (Cache/Respaldo local)
-- ================================================
CREATE TABLE
    IF NOT EXISTS `productos` (
        `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        `shopify_id` BIGINT UNSIGNED NOT NULL UNIQUE,
        `title` VARCHAR(255) NOT NULL,
        `body_html` TEXT,
        `vendor` VARCHAR(100),
        `product_type` VARCHAR(100),
        `handle` VARCHAR(255),
        `status` ENUM ('active', 'archived', 'draft') DEFAULT 'active',
        `created_at_shopify` DATETIME,
        `updated_at_shopify` DATETIME,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX `idx_shopify_id` (`shopify_id`),
        INDEX `idx_status` (`status`),
        INDEX `idx_product_type` (`product_type`),
        INDEX `idx_vendor` (`vendor`)
    ) ENGINE = InnoDB;

-- ================================================
-- Tabla de Variantes de Productos
-- ================================================
CREATE TABLE
    IF NOT EXISTS `product_variants` (
        `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        `product_id` BIGINT UNSIGNED NOT NULL,
        `shopify_variant_id` BIGINT UNSIGNED NOT NULL UNIQUE,
        `title` VARCHAR(255),
        `price` DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
        `sku` VARCHAR(100),
        `inventory_quantity` INT DEFAULT 0,
        `weight` DECIMAL(8, 3),
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (`product_id`) REFERENCES `productos` (`id`) ON DELETE CASCADE,
        INDEX `idx_shopify_variant_id` (`shopify_variant_id`),
        INDEX `idx_product_id` (`product_id`),
        INDEX `idx_sku` (`sku`)
    ) ENGINE = InnoDB;

-- ================================================
-- Tabla de Logs de API (Para auditoría)
-- ================================================
CREATE TABLE
    IF NOT EXISTS `api_logs` (
        `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        `method` ENUM ('GET', 'POST', 'PUT', 'DELETE') NOT NULL,
        `endpoint` VARCHAR(255) NOT NULL,
        `action` VARCHAR(100),
        `request_data` JSON,
        `response_data` JSON,
        `response_code` INT,
        `ip_address` VARCHAR(45),
        `user_agent` TEXT,
        `execution_time` DECIMAL(6, 3),
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX `idx_method` (`method`),
        INDEX `idx_action` (`action`),
        INDEX `idx_response_code` (`response_code`),
        INDEX `idx_created_at` (`created_at`)
    ) ENGINE = InnoDB;

-- ================================================
-- Tabla de Configuraciones
-- ================================================
CREATE TABLE
    IF NOT EXISTS `configuraciones` (
        `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        `clave` VARCHAR(100) NOT NULL UNIQUE,
        `valor` TEXT,
        `descripcion` VARCHAR(255),
        `tipo` ENUM ('string', 'number', 'boolean', 'json') DEFAULT 'string',
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX `idx_clave` (`clave`)
    ) ENGINE = InnoDB;

-- ================================================
-- Tabla de Códigos de Descuento (Cache local)
-- ================================================
CREATE TABLE
    IF NOT EXISTS `discount_codes` (
        `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        `shopify_discount_id` BIGINT UNSIGNED,
        `code` VARCHAR(100) NOT NULL,
        `discount_type` ENUM ('percentage', 'fixed_amount') NOT NULL,
        `value` DECIMAL(10, 2) NOT NULL,
        `usage_limit` INT DEFAULT NULL,
        `usage_count` INT DEFAULT 0,
        `status` ENUM ('active', 'expired', 'disabled') DEFAULT 'active',
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        UNIQUE KEY `unique_code` (`code`),
        INDEX `idx_shopify_discount_id` (`shopify_discount_id`),
        INDEX `idx_status` (`status`)
    ) ENGINE = InnoDB;

-- ================================================
-- Insertar configuraciones por defecto
-- ================================================
INSERT INTO
    `configuraciones` (`clave`, `valor`, `descripcion`, `tipo`)
VALUES
    (
        'api_rate_limit',
        '60',
        'Límite de peticiones por minuto',
        'number'
    ),
    (
        'cache_duration',
        '300',
        'Duración del cache en segundos',
        'number'
    ),
    (
        'log_retention_days',
        '30',
        'Días de retención de logs',
        'number'
    ),
    (
        'sync_interval',
        '3600',
        'Intervalo de sincronización con Shopify (segundos)',
        'number'
    ),
    (
        'debug_mode',
        'false',
        'Modo debug activado',
        'boolean'
    ) ON DUPLICATE KEY
UPDATE `valor` =
VALUES
    (`valor`),
    `updated_at` = CURRENT_TIMESTAMP;

-- ================================================
-- Crear usuario específico para la aplicación (OPCIONAL)
-- ================================================
-- Descomenta las siguientes líneas si quieres crear un usuario específico:
-- CREATE USER IF NOT EXISTS 'shopify_api_user'@'localhost' IDENTIFIED BY 'tu_password_seguro';
-- GRANT SELECT, INSERT, UPDATE, DELETE ON shopify_api_db.* TO 'shopify_api_user'@'localhost';
-- FLUSH PRIVILEGES;
-- ================================================
-- Script completado exitosamente
-- ================================================
SELECT
    'Base de datos shopify_api_db configurada exitosamente!' as mensaje;