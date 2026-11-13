-- ==============================================================================
-- SISTEMA DE GESTION WEB - ICP INFORMATICS COMPANY OF PERU
-- Motor de Base de Datos: MySQL 8.0 / InnoDB / UTF8MB4
-- ==============================================================================

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS service_order_status_history;
DROP TABLE IF EXISTS service_order_parts;
DROP TABLE IF EXISTS technical_service_orders;
DROP TABLE IF EXISTS sale_items;
DROP TABLE IF EXISTS sales;
DROP TABLE IF EXISTS quotation_items;
DROP TABLE IF EXISTS quotations;
DROP TABLE IF EXISTS kardex;
DROP TABLE IF EXISTS products;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS clients;
DROP TABLE IF EXISTS audit_logs;
DROP TABLE IF EXISTS company_settings;
DROP TABLE IF EXISTS users;
SET FOREIGN_KEY_CHECKS = 1;

-- ------------------------------------------------------------------------------
-- 1. TABLA: users (Usuarios del sistema y control de acceso)
-- ------------------------------------------------------------------------------
CREATE TABLE users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    email VARCHAR(191) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('Administrador', 'Vendedor', 'Técnico') NOT NULL,
    status ENUM('Activo', 'Inactivo', 'Bloqueado') NOT NULL DEFAULT 'Activo',
    failed_attempts INT UNSIGNED NOT NULL DEFAULT 0,
    lockout_until DATETIME NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 2. TABLA: audit_logs (Bitácora de auditoría detallada)
-- ------------------------------------------------------------------------------
CREATE TABLE audit_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NULL,
    action VARCHAR(60) NOT NULL,
    entity_table VARCHAR(100) NOT NULL,
    entity_id VARCHAR(50) NULL,
    ip_address VARCHAR(45) NOT NULL,
    user_agent VARCHAR(255) NULL,
    details JSON NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_audit_user (user_id),
    INDEX idx_audit_table_entity (entity_table, entity_id),
    INDEX idx_audit_created_at (created_at),
    CONSTRAINT fk_audit_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 3. TABLA: company_settings (Configuración global del sistema)
-- ------------------------------------------------------------------------------
CREATE TABLE company_settings (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value TEXT NULL,
    setting_group VARCHAR(50) NOT NULL DEFAULT 'general',
    description VARCHAR(255) NULL,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 4. TABLA: clients (Clientes comerciales)
-- ------------------------------------------------------------------------------
CREATE TABLE clients (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    document_type ENUM('DNI', 'RUC', 'CE', 'PASAPORTE') NOT NULL DEFAULT 'RUC',
    document_number VARCHAR(20) NOT NULL UNIQUE,
    business_name VARCHAR(255) NOT NULL,
    address VARCHAR(255) NULL,
    phone VARCHAR(50) NULL,
    email VARCHAR(191) NULL,
    status ENUM('Activo', 'Inactivo') NOT NULL DEFAULT 'Activo',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_client_doc (document_number),
    INDEX idx_client_name (business_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 5. TABLA: categories (Categorías de productos)
-- ------------------------------------------------------------------------------
CREATE TABLE categories (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    description VARCHAR(255) NULL,
    status ENUM('Activo', 'Inactivo') NOT NULL DEFAULT 'Activo',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 6. TABLA: products (Inventario de productos y repuestos)
-- ------------------------------------------------------------------------------
CREATE TABLE products (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    category_id INT UNSIGNED NOT NULL,
    code VARCHAR(50) NOT NULL UNIQUE,
    name VARCHAR(255) NOT NULL,
    description TEXT NULL,
    purchase_price DECIMAL(12, 2) NOT NULL DEFAULT 0.00,
    sale_price DECIMAL(12, 2) NOT NULL DEFAULT 0.00,
    stock INT NOT NULL DEFAULT 0,
    min_stock INT NOT NULL DEFAULT 5,
    status ENUM('Activo', 'Inactivo') NOT NULL DEFAULT 'Activo',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_product_code (code),
    INDEX idx_product_category (category_id),
    CONSTRAINT fk_product_category FOREIGN KEY (category_id) REFERENCES categories(id) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 7. TABLA: kardex (Registro histórico de movimientos de almacén)
-- ------------------------------------------------------------------------------
CREATE TABLE kardex (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_id INT UNSIGNED NOT NULL,
    user_id INT UNSIGNED NOT NULL,
    movement_type ENUM('INGRESO', 'SALIDA', 'AJUSTE_POSITIVO', 'AJUSTE_NEGATIVO', 'VENTA', 'ANULACION_VENTA', 'SERVICIO_TECNICO') NOT NULL,
    reference_type ENUM('VENTA', 'SERVICIO', 'AJUSTE_MANUAL', 'INICIAL', 'COMPRA') NOT NULL,
    reference_id VARCHAR(50) NULL,
    quantity INT NOT NULL,
    previous_stock INT NOT NULL,
    new_stock INT NOT NULL,
    unit_cost DECIMAL(12, 2) NOT NULL DEFAULT 0.00,
    reason VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_kardex_product (product_id),
    INDEX idx_kardex_user (user_id),
    INDEX idx_kardex_date (created_at),
    CONSTRAINT fk_kardex_product FOREIGN KEY (product_id) REFERENCES products(id) ON UPDATE CASCADE,
    CONSTRAINT fk_kardex_user FOREIGN KEY (user_id) REFERENCES users(id) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 8. TABLA: quotations (Cotizaciones comerciales)
-- ------------------------------------------------------------------------------
CREATE TABLE quotations (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    quotation_number VARCHAR(30) NOT NULL UNIQUE,
    client_id INT UNSIGNED NOT NULL,
    user_id INT UNSIGNED NOT NULL,
    subtotal DECIMAL(12, 2) NOT NULL DEFAULT 0.00,
    discount DECIMAL(12, 2) NOT NULL DEFAULT 0.00,
    igv DECIMAL(12, 2) NOT NULL DEFAULT 0.00,
    total DECIMAL(12, 2) NOT NULL DEFAULT 0.00,
    status ENUM('PENDIENTE', 'APROBADA', 'RECHAZADA', 'CONVERTIDA') NOT NULL DEFAULT 'PENDIENTE',
    valid_until DATE NULL,
    notes TEXT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_quote_client (client_id),
    INDEX idx_quote_user (user_id),
    CONSTRAINT fk_quote_client FOREIGN KEY (client_id) REFERENCES clients(id) ON UPDATE CASCADE,
    CONSTRAINT fk_quote_user FOREIGN KEY (user_id) REFERENCES users(id) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 9. TABLA: quotation_items (Detalle de cotizaciones)
-- ------------------------------------------------------------------------------
CREATE TABLE quotation_items (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    quotation_id INT UNSIGNED NOT NULL,
    product_id INT UNSIGNED NOT NULL,
    product_name VARCHAR(255) NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(12, 2) NOT NULL,
    discount DECIMAL(12, 2) NOT NULL DEFAULT 0.00,
    subtotal DECIMAL(12, 2) NOT NULL,
    total DECIMAL(12, 2) NOT NULL,
    CONSTRAINT fk_quote_item_quote FOREIGN KEY (quotation_id) REFERENCES quotations(id) ON DELETE CASCADE,
    CONSTRAINT fk_quote_item_product FOREIGN KEY (product_id) REFERENCES products(id) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 10. TABLA: sales (Ventas y comprobantes de venta)
-- ------------------------------------------------------------------------------
CREATE TABLE sales (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    quotation_id INT UNSIGNED NULL,
    client_id INT UNSIGNED NOT NULL,
    user_id INT UNSIGNED NOT NULL,
    voucher_type ENUM('BOLETA', 'FACTURA', 'NOTA_VENTA') NOT NULL,
    series VARCHAR(10) NOT NULL,
    correlative INT UNSIGNED NOT NULL,
    payment_condition ENUM('EFECTIVO', 'TRANSFERENCIA', 'CREDITO') NOT NULL DEFAULT 'EFECTIVO',
    payment_status ENUM('PAGADO', 'PENDIENTE') NOT NULL DEFAULT 'PAGADO',
    subtotal DECIMAL(12, 2) NOT NULL DEFAULT 0.00,
    igv DECIMAL(12, 2) NOT NULL DEFAULT 0.00,
    total DECIMAL(12, 2) NOT NULL DEFAULT 0.00,
    status ENUM('EMITIDA', 'ANULADA') NOT NULL DEFAULT 'EMITIDA',
    notes TEXT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_voucher (voucher_type, series, correlative),
    INDEX idx_sale_client (client_id),
    INDEX idx_sale_user (user_id),
    INDEX idx_sale_created (created_at),
    CONSTRAINT fk_sale_quote FOREIGN KEY (quotation_id) REFERENCES quotations(id) ON DELETE SET NULL,
    CONSTRAINT fk_sale_client FOREIGN KEY (client_id) REFERENCES clients(id) ON UPDATE CASCADE,
    CONSTRAINT fk_sale_user FOREIGN KEY (user_id) REFERENCES users(id) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 11. TABLA: sale_items (Detalle de venta)
-- ------------------------------------------------------------------------------
CREATE TABLE sale_items (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    sale_id INT UNSIGNED NOT NULL,
    product_id INT UNSIGNED NOT NULL,
    product_name VARCHAR(255) NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(12, 2) NOT NULL,
    subtotal DECIMAL(12, 2) NOT NULL,
    total DECIMAL(12, 2) NOT NULL,
    CONSTRAINT fk_sale_item_sale FOREIGN KEY (sale_id) REFERENCES sales(id) ON DELETE CASCADE,
    CONSTRAINT fk_sale_item_product FOREIGN KEY (product_id) REFERENCES products(id) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 12. TABLA: technical_service_orders (Órdenes de Servicio Técnico)
-- ------------------------------------------------------------------------------
CREATE TABLE technical_service_orders (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_number VARCHAR(30) NOT NULL UNIQUE,
    client_id INT UNSIGNED NOT NULL,
    technician_id INT UNSIGNED NOT NULL,
    equipment_type VARCHAR(100) NOT NULL,
    brand VARCHAR(100) NOT NULL,
    model VARCHAR(100) NOT NULL,
    serial_number VARCHAR(100) NULL,
    problem_description TEXT NOT NULL,
    diagnostic TEXT NULL,
    work_performed TEXT NULL,
    priority ENUM('BAJA', 'MEDIA', 'ALTA', 'URGENTE') NOT NULL DEFAULT 'MEDIA',
    status ENUM('REGISTRADA', 'EN_PROCESO', 'EN_ESPERA_REPUESTO', 'ATENDIDA', 'ENTREGADA') NOT NULL DEFAULT 'REGISTRADA',
    hours_worked DECIMAL(6, 2) NOT NULL DEFAULT 0.00,
    hourly_rate DECIMAL(12, 2) NOT NULL DEFAULT 0.00,
    labor_cost DECIMAL(12, 2) NOT NULL DEFAULT 0.00,
    parts_cost DECIMAL(12, 2) NOT NULL DEFAULT 0.00,
    total_cost DECIMAL(12, 2) NOT NULL DEFAULT 0.00,
    customer_signature_date DATETIME NULL,
    customer_notes TEXT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_ost_client (client_id),
    INDEX idx_ost_tech (technician_id),
    INDEX idx_ost_status (status),
    CONSTRAINT fk_ost_client FOREIGN KEY (client_id) REFERENCES clients(id) ON UPDATE CASCADE,
    CONSTRAINT fk_ost_tech FOREIGN KEY (technician_id) REFERENCES users(id) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 13. TABLA: service_order_parts (Repuestos utilizados en Servicio Técnico)
-- ------------------------------------------------------------------------------
CREATE TABLE service_order_parts (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    service_order_id INT UNSIGNED NOT NULL,
    product_id INT UNSIGNED NULL,
    part_name VARCHAR(255) NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    unit_price DECIMAL(12, 2) NOT NULL DEFAULT 0.00,
    subtotal DECIMAL(12, 2) NOT NULL DEFAULT 0.00,
    CONSTRAINT fk_sop_order FOREIGN KEY (service_order_id) REFERENCES technical_service_orders(id) ON DELETE CASCADE,
    CONSTRAINT fk_sop_product FOREIGN KEY (product_id) REFERENCES products(id) ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 14. TABLA: service_order_status_history (Trazabilidad de estados de Servicio)
-- ------------------------------------------------------------------------------
CREATE TABLE service_order_status_history (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    service_order_id INT UNSIGNED NOT NULL,
    user_id INT UNSIGNED NOT NULL,
    from_status VARCHAR(50) NULL,
    to_status VARCHAR(50) NOT NULL,
    comments VARCHAR(255) NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_sosh_order FOREIGN KEY (service_order_id) REFERENCES technical_service_orders(id) ON DELETE CASCADE,
    CONSTRAINT fk_sosh_user FOREIGN KEY (user_id) REFERENCES users(id) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

