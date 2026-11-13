# Sistema de Gestión Web Empresarial - ICP Informatics Company of Perú

Sistema de gestión web centralizado desarrollado desde cero para automatizar los procesos comerciales, control de inventario físico y taller de servicio técnico de **ICP Informatics Company of Perú**.

---

## 1. Stack Tecnológico Estricto

- **Backend:** PHP 8.x Puro (Vanilla PHP), sin frameworks comerciales (no Laravel, no Symfony).
- **Estándares:** PSR-4 para carga automática de clases con Composer / autoloader nativo y PSR-12 para estilo y formato de código.
- **Frontend:** HTML5 semántico, diseño responsivo con **Tailwind CSS 3.x** y dinamismo interactivo con **Vanilla JavaScript** puro.
- **Base de Datos:** **MySQL 8.0** con motor **InnoDB**, soporte íntegro para llaves foráneas (`FOREIGN KEY`) e integridad referencial y transacciones ACID.

---

## 2. Arquitectura de Software y Patrones de Diseño

El sistema implementa una arquitectura desacoplada por capas:
- **Capa de Presentación (Vistas):** Plantillas PHP puras ubicadas en `views/`, organizadas en layouts (`main`, `auth`, `print`) y componentes parciales reutilizables.
- **Capa de Controladores:** Controladores en `src/Controllers/` que reciben la petición HTTP, validan los datos de entrada y despachan respuestas.
- **Capa de Lógica de Negocio (Servicios):** Servicios en `src/Services/` que encapsulan las reglas de negocio del dominio (cálculos de impuestos, transacciones de stock, máquina de estados).
- **Capa de Acceso a Datos (Repositorios):** **Repository Pattern** con contratos de interfaz en `src/Repositories/Contracts/` e implementaciones en `src/Repositories/MySQL/`.
- **Inyección de Dependencias (DI Container):** Contenedor de dependencias en `src/Core/Container.php` que asocia interfaces a sus implementaciones concretas.
- **Patrón Front Controller:** Único punto de entrada HTTP en `public/index.php`, concentrando el enrutamiento y ejecución de middlewares.

---

## 3. Estructura del Proyecto

```text
Pagina Elvis/
├── bin/
│   └── backup.php                    # Script CLI para respaldo programable (Cron/Task Scheduler)
├── config/
│   ├── app.php                       # Configuración general y seguridad
│   └── database.php                  # Configuración de conexión MySQL PDO
├── database/
│   ├── schema.sql                    # Definición de tablas InnoDB, índices y relaciones FK
│   ├── seeds.sql                     # Datos semilla iniciales (usuarios, catálogo, clientes)
│   └── migrate.php                   # Ejecutor de migración e inicialización de BD
├── public/
│   ├── index.php                     # Front Controller (Punto de entrada único)
│   ├── .htaccess                     # Reescritura de URLs para servidores Apache
│   └── assets/
│       ├── css/
│       │   └── custom.css            # Estilos personalizados y reglas print
│       └── js/
│           └── app.js                # Scripts Vanilla JS para modales y dinamismo
├── src/
│   ├── autoload.php                  # Autoloader PSR-4 nativo
│   ├── Core/                         # Componentes del núcleo (Request, Response, Router, etc.)
│   ├── Repositories/
│   │   ├── Contracts/                # Interfaces de repositorios
│   │   └── MySQL/                    # Implementaciones MySQL con PDO preparado
│   ├── Services/                     # Capa de servicios y lógica de negocio
│   ├── Controllers/                  # Controladores MVC
│   └── Middlewares/                  # Middlewares (Auth, Roles, CSRF)
├── views/
│   ├── layouts/                      # Layouts principales (main, auth, print)
│   ├── partials/                     # Parciales (sidebar, navbar, alerts)
│   ├── auth/                         # Vistas de autenticación
│   ├── dashboard/                    # Panel principal de control
│   ├── clients/                      # CRUD comercial de clientes
│   ├── categories/                   # Categorías de productos
│   ├── products/                     # Catálogo e inventario
│   ├── inventory/                    # Kardex y ajustes de existencias
│   ├── quotations/                   # Cotizaciones y conversión
│   ├── sales/                        # Ventas y comprobantes correlativos
│   ├── technical_service/            # Órdenes de servicio técnico y conformidad
│   ├── reports/                      # Reportes consolidados y exportaciones
│   ├── settings/                     # Configuración fiscal y backups
│   └── errors/                       # Páginas de error 403 y 404
├── composer.json                     # Definición de paquete y autocarga PSR-4
└── router.php                        # Enrutador para servidor de desarrollo PHP
```

---

## 4. Módulos Desarrollados

### Módulo 1: Seguridad y Gestión de Accesos
- **Autenticación Segura:** Inicio y cierre de sesión con verificación de contraseñas mediante **bcrypt** (`PASSWORD_BCRYPT`).
- **Control de Acceso Basado en Roles (RBAC):**
  - **Administrador:** Acceso irrestricto a todos los módulos y configuraciones.
  - **Vendedor:** Acceso comercial a cotizaciones, ventas, clientes e inventario.
  - **Técnico:** Acceso especializado al taller de servicio técnico (OST) y catálogo de repuestos.
- **Protección contra Fuerza Bruta:** Bloqueo temporal de la cuenta por 15 minutos al acumular 5 intentos fallidos consecutivos de inicio de sesión.
- **Bitácora de Auditoría Automática:** Registro de cada creación, modificación, anulación o inicio de sesión en la tabla `audit_logs`, almacenando usuario, fecha, hora, dirección IP, agente y datos antes/después en formato JSON.

### Módulo 2: Gestión Comercial (Clientes y Ventas)
- **CRUD de Clientes:** Registro, búsqueda y actualización de clientes con RUC (11 dígitos), DNI (8 dígitos), razón social y contactos.
- **Cotizaciones con Conversión Automática:** Cálculo de precios, descuentos por ítem y global, cálculo automático del IGV (18%) y botón directo de conversión a **Orden de Venta** sin necesidad de volver a digitar los productos.
- **Emisión de Comprobantes:** Registro de Boletas, Facturas y Notas de Venta con **series y correlativos secuenciales automáticos** (ej. `F001-00001001`).
- **Condiciones de Pago:** Soporte para pagos en **Efectivo**, **Transferencia** o **Crédito**.
- **Formato Apto para Impresión:** Vistas de comprobante con maquetación `@media print` optimizada para impresión en A4 o ticket.

### Módulo 3: Control de Inventario y Kardex
- **Catálogo de Productos:** Código único (SKU/código de barras), descripción, categoría, precio de compra y venta, existencias actuales y umbral de seguridad.
- **Transacciones ACID de Stock:** Al emitir una venta, una transacción en la base de datos descuenta automáticamente el stock y genera el respectivo movimiento en el Kardex. En caso de anulación de la venta, el inventario se repone automáticamente.
- **Alertas Visuales de Stock Mínimo:** Distintivos de advertencia en el listado y banners de alerta en el Dashboard cuando las existencias son iguales o inferiores al stock mínimo predefinido.
- **Kardex Físico y Valorizado:** Historial completo con fecha, tipo de movimiento (`INGRESO`, `SALIDA`, `VENTA`, `ANULACION_VENTA`, `SERVICIO_TECNICO`, `AJUSTE_POSITIVO`, `AJUSTE_NEGATIVO`), cantidad, stock previo, nuevo stock y motivo justificado.

### Módulo 4: Servicio Técnico y Taller
- **Recepción de Equipos:** Registro de Órdenes de Servicio Técnico (OST) con cliente, datos del equipo (tipo, marca, modelo, serie), síntoma reportado y prioridad (`BAJA`, `MEDIA`, `ALTA`, `URGENTE`).
- **Máquina de Estados Estricta:** Trazabilidad con validación de transiciones permitidas:
  $$\text{REGISTRADA} \longrightarrow \text{EN PROCESO} \rightleftarrows \text{EN ESPERA DE REPUESTO} \longrightarrow \text{ATENDIDA} \longrightarrow \text{ENTREGADA}$$
- **Liquidación de Costos:** Cálculo automático de horas de trabajo invertidas $\times$ tarifa horaria + costo de repuestos utilizados (descontados del almacén).
- **Hoja de Conformidad Imprimible:** Formato con diagnóstico, procedimientos realizados, repuestos, garantía y recuadros para la firma del técnico y la firma de conformidad del cliente.

### Módulo 5: Dashboard y Reportes Gerenciales
- **Panel en Tiempo Real:** Indicadores de ventas del período, comprobantes emitidos, ticket promedio, distribución del taller técnico y lista de productos más vendidos.
- **Herramienta de Reportes:** Filtros dinámicos por rango de fechas, estado y comprobante.
- **Exportación:** Exportación nativa a archivos **CSV** y generación de reportes listos para imprimir o exportar a **PDF**.

### Módulo 6: Configuración del Sistema y Respaldo
- **Datos Fiscales:** Administración de Razón Social, RUC, dirección, teléfono, correo y porcentaje del IGV.
- **Series y Correlativos:** Configuración directa de las series para Boletas, Facturas, Notas de Venta y OST.
- **Respaldos de Base de Datos:**
  - Módulo web para generar y descargar copias de seguridad SQL.
  - Script CLI ejecutable en `bin/backup.php` diseñado para programar respaldos diarios automáticos mediante Cron o el Programador de Tareas de Windows.

---

## 5. Reglas de Seguridad Implementadas

1. **Inyección SQL:** 100% de las consultas a la base de datos utilizan sentencias preparadas con **PDO** y enlaces de parámetros tipados (`execute(['param' => $value])`).
2. **Ataques XSS:** Todas las variables presentadas en las vistas se escapan mediante la función nativa `htmlspecialchars($val, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')` a través del helper `e()`.
3. **Protección CSRF:** Todos los formularios de acción (`POST`, `PUT`, `DELETE`) incluyen un token criptográfico seguro `_csrf_token` generado con `random_bytes(32)` y validado con `hash_equals()`.
4. **Seguridad de Sesión:** Cookies seguras con directivas `HttpOnly`, `SameSite=Lax` y regeneración periódica del ID de sesión para prevenir Session Fixation.

---

## 6. Instalación y Puesta en Marcha

### Prerrequisitos
- PHP 8.0 o superior (con extensiones `pdo_mysql`, `mbstring`, `openssl`).
- MySQL 8.0 o MariaDB equivalente.
- Servidor web Apache / Nginx o el servidor embebido de PHP.

### Paso 1: Configurar la Base de Datos
Copie el archivo de ejemplo de variables de entorno y ajuste las credenciales de su base de datos si fuera necesario:
```bash
cp .env.example .env
```
*(O edite directamente `config/database.php` con su host, usuario y clave de MySQL).*

### Paso 2: Ejecutar la Migración y Carga de Semillas
Ejecute el script de migración automática desde su terminal:
```bash
php database/migrate.php
```
*Alternativamente, puede importar directamente `database/schema.sql` seguido de `database/seeds.sql` mediante phpMyAdmin o MySQL Workbench.*

### Paso 3: Iniciar el Servidor de Desarrollo
Para arrancar el sistema de inmediato con el servidor integrado de PHP:
```bash
php -S localhost:8000 router.php
```
Luego ingrese a su navegador en: **`http://localhost:8000`**

---

## 7. Credenciales de Acceso Predeterminadas

| Rol | Correo Electrónico | Contraseña |
| :--- | :--- | :--- |
| **Administrador** | `admin@icp.com.pe` | `Admin123*` |
| **Vendedor** | `ventas@icp.com.pe` | `Admin123*` |
| **Técnico** | `tecnico@icp.com.pe` | `Admin123*` |

