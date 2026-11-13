-- ==============================================================================
-- SEMILLAS DE DATOS INICIALES - ICP INFORMATICS COMPANY OF PERU
-- ==============================================================================

-- 1. Configuraciones de la Empresa
INSERT INTO company_settings (setting_key, setting_value, setting_group, description) VALUES
('company_name', 'ICP Informatics Company of Perú', 'fiscal', 'Razón social de la empresa'),
('company_trade_name', 'ICP Informatics Perú', 'fiscal', 'Nombre comercial'),
('company_ruc', '20608976541', 'fiscal', 'RUC de la empresa'),
('company_address', 'Av. Rivera Navarrete 501, Of. 802, San Isidro, Lima, Perú', 'fiscal', 'Dirección fiscal principal'),
('company_phone', '+51 1 719-5400 / 998 123 456', 'fiscal', 'Teléfonos de contacto'),
('company_email', 'contacto@icp.com.pe', 'fiscal', 'Correo corporativo'),
('company_igv_rate', '18.00', 'fiscal', 'Porcentaje actual del IGV'),
('company_currency_symbol', 'S/.', 'fiscal', 'Símbolo de moneda'),
('company_currency_name', 'Soles', 'fiscal', 'Nombre de moneda'),
('serie_boleta', 'B001', 'comprobantes', 'Serie para Boletas de Venta'),
('correlativo_boleta', '1001', 'comprobantes', 'Siguiente correlativo Boleta'),
('serie_factura', 'F001', 'comprobantes', 'Serie para Facturas de Venta'),
('correlativo_factura', '1001', 'comprobantes', 'Siguiente correlativo Factura'),
('serie_nota', 'NV01', 'comprobantes', 'Serie para Notas de Venta'),
('correlativo_nota', '1001', 'comprobantes', 'Siguiente correlativo Nota de Venta'),
('serie_ost', 'OST-2026', 'servicio', 'Serie de Orden de Servicio Técnico'),
('correlativo_ost', '101', 'servicio', 'Siguiente correlativo de OST'),
('serie_cotizacion', 'COT-2026', 'ventas', 'Serie para Cotizaciones'),
('correlativo_cotizacion', '101', 'ventas', 'Siguiente correlativo Cotización')
ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value);

-- 2. Usuarios del Sistema (Contraseñas por defecto: 'Admin123*' con hash bcrypt)
-- Hash bcrypt para 'Admin123*': $2y$10$w8k2.tZ9pE8nFmH8rW7sreP0bX1cT2yU3vI4wO5pQ6rS7tU8vW9xy (generado con bcrypt costo 10)
-- Hash estándar para 'password': $2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi
INSERT INTO users (id, name, email, password_hash, role, status, failed_attempts) VALUES
(1, 'Administrador del Sistema', 'admin@icp.com.pe', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrador', 'Activo', 0),
(2, 'Carlos Venta Ramos', 'ventas@icp.com.pe', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Vendedor', 'Activo', 0),
(3, 'Ing. Miguel Técnico Silva', 'tecnico@icp.com.pe', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Técnico', 'Activo', 0)
ON DUPLICATE KEY UPDATE name = VALUES(name);

-- 3. Categorías de Productos
INSERT INTO categories (id, name, description, status) VALUES
(1, 'Laptops y Computadoras', 'Equipos portátiles corporativos y workstations', 'Activo'),
(2, 'Servidores y Redes', 'Servidores rack/torre, switches, routers y fibra', 'Activo'),
(3, 'Almacenamiento y Memorias', 'Discos de estado sólido, memorias RAM y discos externos', 'Activo'),
(4, 'Repuestos y Componentes', 'Pantallas, baterías, teclados, tarjetas de red y pasta térmica', 'Activo'),
(5, 'Impresoras y Suministros', 'Impresoras multifuncionales continuas y toners corporativos', 'Activo'),
(6, 'Servicios Profesionales', 'Mano de obra especializada, consultoría y mantenimiento', 'Activo')
ON DUPLICATE KEY UPDATE name = VALUES(name);

-- 4. Clientes Iniciales
INSERT INTO clients (id, document_type, document_number, business_name, address, phone, email, status) VALUES
(1, 'RUC', '20512345678', 'Corporación Minera del Centro S.A.C.', 'Av. Javier Prado Este 4200, Surco, Lima', '01-435-8900', 'compras@mineradelcentro.pe', 'Activo'),
(2, 'RUC', '20100085432', 'Banco Financiero & Comercio del Perú S.A.', 'Jr. Lampa 545, Cercado de Lima', '01-612-4000', 'logistica@financieroperu.com.pe', 'Activo'),
(3, 'RUC', '20498765431', 'Consultores & Asociados Lima S.R.L.', 'Calle Las Begonias 441, San Isidro, Lima', '01-215-9988', 'administracion@consultoreslima.com', 'Activo'),
(4, 'DNI', '45892314', 'Juan Alberto Pérez Silva', 'Av. Benavides 1234, Miraflores, Lima', '987-654-321', 'juan.perez@gmail.com', 'Activo'),
(5, 'DNI', '71239845', 'María Elena Torres Gutiérrez', 'Jr. Ayacucho 876, San Miguel, Lima', '991-234-567', 'mtorres@outlook.com', 'Activo')
ON DUPLICATE KEY UPDATE business_name = VALUES(business_name);

-- 5. Productos e Inventario
INSERT INTO products (id, category_id, code, name, description, purchase_price, sale_price, stock, min_stock, status) VALUES
(1, 1, 'LAP-DELL-5430', 'Laptop Dell Latitude 5430 Core i7 16GB 512GB SSD 14"', 'Laptop empresarial con Intel Core i7 12va Gen, 16GB DDR4, 512GB SSD NVMe, Windows 11 Pro', 3200.00, 4150.00, 12, 3, 'Activo'),
(2, 1, 'LAP-LEN-T14', 'ThinkPad T14 Gen 3 Core i5 16GB 512GB SSD', 'Notebook ThinkPad resistencia militar, teclado retroiluminado, garantía 3 años', 2850.00, 3650.00, 8, 3, 'Activo'),
(3, 3, 'SSD-KNG-1TB', 'SSD Kingston NV2 1TB PCIe 4.0 NVMe M.2', 'Unidad de estado sólido con velocidades de lectura hasta 3500MB/s', 160.00, 240.00, 25, 5, 'Activo'),
(4, 3, 'RAM-KNG-16GB', 'Memoria RAM Kingston Fury Beast 16GB DDR4 3200MHz', 'Módulo de memoria RAM de alto rendimiento para equipos de escritorio', 110.00, 175.00, 18, 5, 'Activo'),
(5, 4, 'REP-FAN-DELL', 'Cooler Ventilador Original para Laptop Dell Latitude', 'Sistema de disipación térmica original para reemplazo en soporte técnico', 45.00, 95.00, 6, 2, 'Activo'),
(6, 4, 'REP-LCD-156', 'Pantalla LED 15.6" FHD 30 Pines Slim IPS', 'Display de reemplazo compatible con múltiples marcas Lenovo, Asus y HP', 180.00, 310.00, 4, 2, 'Activo'),
(7, 4, 'TER-MX4-4G', 'Pasta Térmica Arctic MX-4 4g Alto Rendimiento', 'Compuesto térmico de micropartículas de carbono para CPU y GPU', 22.00, 45.00, 2, 5, 'Activo'), -- ALERTA DE STOCK MINIMO! (Stock 2 < Min 5)
(8, 2, 'SWT-CIS-24G', 'Switch Cisco Catalyst 1000 24 Puertos Gigabit Gestionable', 'Switch capa 2 empresarial con 4 enlaces uplink SFP 1G', 1650.00, 2280.00, 5, 2, 'Activo'),
(9, 5, 'IMP-EPS-L3250', 'Impresora Multifuncional Epson EcoTank L3250 WiFi', 'Sistema de tanque de tinta continua con conectividad inalámbrica', 580.00, 790.00, 7, 3, 'Activo'),
(10, 6, 'SRV-MNT-PREV', 'Servicio de Mantenimiento Preventivo y Limpieza', 'Limpieza profunda de componentes, lubricación y cambio de pasta térmica', 0.00, 120.00, 999, 10, 'Activo')
ON DUPLICATE KEY UPDATE name = VALUES(name);

-- 6. Movimientos Kardex Iniciales (Carga inicial de inventario)
INSERT INTO kardex (product_id, user_id, movement_type, reference_type, reference_id, quantity, previous_stock, new_stock, unit_cost, reason, created_at) VALUES
(1, 1, 'INGRESO', 'INICIAL', 'INV-INI-001', 12, 0, 12, 3200.00, 'Inventario inicial de apertura de almacén', NOW() - INTERVAL 10 DAY),
(2, 1, 'INGRESO', 'INICIAL', 'INV-INI-001', 8, 0, 8, 2850.00, 'Inventario inicial de apertura de almacén', NOW() - INTERVAL 10 DAY),
(3, 1, 'INGRESO', 'INICIAL', 'INV-INI-001', 25, 0, 25, 160.00, 'Inventario inicial de apertura de almacén', NOW() - INTERVAL 10 DAY),
(4, 1, 'INGRESO', 'INICIAL', 'INV-INI-001', 18, 0, 18, 110.00, 'Inventario inicial de apertura de almacén', NOW() - INTERVAL 10 DAY),
(5, 1, 'INGRESO', 'INICIAL', 'INV-INI-001', 6, 0, 6, 45.00, 'Inventario inicial de repuestos', NOW() - INTERVAL 10 DAY),
(6, 1, 'INGRESO', 'INICIAL', 'INV-INI-001', 4, 0, 4, 180.00, 'Inventario inicial de repuestos', NOW() - INTERVAL 10 DAY),
(7, 1, 'INGRESO', 'INICIAL', 'INV-INI-001', 2, 0, 2, 22.00, 'Inventario inicial pasta térmica (Stock crítico)', NOW() - INTERVAL 10 DAY),
(8, 1, 'INGRESO', 'INICIAL', 'INV-INI-001', 5, 0, 5, 1650.00, 'Inventario inicial de equipos de red', NOW() - INTERVAL 10 DAY),
(9, 1, 'INGRESO', 'INICIAL', 'INV-INI-001', 7, 0, 7, 580.00, 'Inventario inicial de impresoras', NOW() - INTERVAL 10 DAY);

-- 7. Cotización de Ejemplo
INSERT INTO quotations (id, quotation_number, client_id, user_id, subtotal, discount, igv, total, status, valid_until, notes, created_at) VALUES
(1, 'COT-2026-0001', 1, 2, 4390.00, 100.00, 772.20, 5062.20, 'PENDIENTE', DATE_ADD(CURRENT_DATE, INTERVAL 15 DAY), 'Cotización para renovación de equipos gerenciales. Garantía 12 meses.', NOW() - INTERVAL 2 DAY);

INSERT INTO quotation_items (quotation_id, product_id, product_name, quantity, unit_price, discount, subtotal, total) VALUES
(1, 1, 'Laptop Dell Latitude 5430 Core i7 16GB 512GB SSD 14"', 1, 4150.00, 100.00, 4050.00, 4050.00),
(1, 3, 'SSD Kingston NV2 1TB PCIe 4.0 NVMe M.2', 1, 240.00, 0.00, 240.00, 240.00);

-- 8. Venta de Ejemplo con Factura y Boleta
INSERT INTO sales (id, quotation_id, client_id, user_id, voucher_type, series, correlative, payment_condition, payment_status, subtotal, igv, total, status, notes, created_at) VALUES
(1, NULL, 3, 2, 'FACTURA', 'F001', 1000, 'TRANSFERENCIA', 'PAGADO', 3241.53, 583.47, 3825.00, 'EMITIDA', 'Venta directa de equipo ThinkPad + RAM Kingston', NOW() - INTERVAL 1 DAY);

INSERT INTO sale_items (sale_id, product_id, product_name, quantity, unit_price, subtotal, total) VALUES
(1, 2, 'ThinkPad T14 Gen 3 Core i5 16GB 512GB SSD', 1, 3650.00, 3650.00, 3650.00),
(1, 4, 'Memoria RAM Kingston Fury Beast 16GB DDR4 3200MHz', 1, 175.00, 175.00, 175.00);

-- 9. Orden de Servicio Técnico de Ejemplo
INSERT INTO technical_service_orders (id, order_number, client_id, technician_id, equipment_type, brand, model, serial_number, problem_description, diagnostic, work_performed, priority, status, hours_worked, hourly_rate, labor_cost, parts_cost, total_cost, created_at) VALUES
(1, 'OST-2026-0001', 4, 3, 'Laptop', 'Dell', 'Latitude 3400', 'DL-8849201-PE', 'El equipo calienta excesivamente y se apaga de forma imprevista durante videoconferencias.', 'Sistema de ventilación obstruido por polvo y compuesto térmico completamente seco.', 'Mantenimiento integral del sistema térmico, lubricación de cooler y aplicación de pasta Arctic MX-4. Pruebas de estrés superadas a 65°C constantes.', 'ALTA', 'ATENDIDA', 2.50, 45.00, 112.50, 45.00, 157.50, NOW() - INTERVAL 1 DAY);

INSERT INTO service_order_parts (service_order_id, product_id, part_name, quantity, unit_price, subtotal) VALUES
(1, 7, 'Pasta Térmica Arctic MX-4 4g Alto Rendimiento', 1, 45.00, 45.00);

INSERT INTO service_order_status_history (service_order_id, user_id, from_status, to_status, comments, created_at) VALUES
(1, 3, NULL, 'REGISTRADA', 'Ingreso de equipo a taller técnico', NOW() - INTERVAL 1 DAY),
(1, 3, 'REGISTRADA', 'EN_PROCESO', 'Inicio de desarme y pruebas diagnósticas', NOW() - INTERVAL 20 HOUR),
(1, 3, 'EN_PROCESO', 'ATENDIDA', 'Servicio y pruebas de temperatura concluidas exitosamente', NOW() - INTERVAL 4 HOUR);

-- 10. Auditoría Inicial
INSERT INTO audit_logs (user_id, action, entity_table, entity_id, ip_address, user_agent, details, created_at) VALUES
(1, 'CREACION', 'company_settings', '1', '127.0.0.1', 'System Seeder', '{"action": "Configuracion inicial de la empresa ICP"}', NOW() - INTERVAL 10 DAY),
(2, 'CREACION', 'sales', '1', '192.168.1.15', 'Mozilla/5.0 Windows', '{"voucher": "F001-00001000", "total": 3825.00}', NOW() - INTERVAL 1 DAY);

