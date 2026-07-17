# FacturaPanadería — Sistema de Gestión de Panadería con Facturación Electrónica

Sistema integral para panaderías y pastelerías con facturación electrónica SUNAT (Perú), producción, pedidos programados, reparto, POS, impresión térmica ESC/POS y caja registradora.

Basado en FacturaFácil, transformado para el negocio de panadería manteniendo toda la infraestructura de facturación electrónica.

---

## Módulos del Sistema

### Facturación Electrónica SUNAT
- Emisión de **Facturas** (01), **Boletas** (03), **Notas de Venta** (NV), **Notas de Crédito** (07), **Notas de Débito** (08)
- Envío automático a SUNAT vía Greenter 5.x
- Firma digital con certificado .p12 o .pem (PEM-first para OpenSSL 3.0)
- PDF en formato A4 y Ticket 80mm con código QR
- Descarga de XML firmado y CDR
- Resumen Diario para boletas
- Documentos especiales: Retenciones, Guías de Remisión, Percepciones
- Series configurables por tipo de documento

### POS (Punto de Venta)
- Interfaz simplificada para ventas rápidas
- Búsqueda de productos por nombre o código de barras
- Selección de cliente con búsqueda SUNAT y creación rápida
- Múltiples métodos de pago: Efectivo, Tarjeta, Yape, Plin, Mixto
- Control de caja (apertura/cierre con arqueo)
- Apertura de cajón de efectivo

### Recetas y Producción
- **Recetas**: fórmulas con ingredientes del inventario, cantidades, merma %, costo estimado
- **Órdenes de Producción**: planificar → iniciar → completar
- Al iniciar: descuenta automáticamente ingredientes del inventario (con merma)
- Al completar: incrementa stock del producto terminado
- Cálculo automático de costo total de producción

### Mermas
- Registro de pérdidas por: vencido, dañado, devolución, no vendido, producción, otro
- Descuenta stock automáticamente del inventario
- Cálculo de costo de pérdida (precio_compra × cantidad)
- Al eliminar un registro: restaura el stock
- Reporte de mermas del mes en el dashboard

### Pedidos Programados (Encargos)
- Pedidos de clientes para fechas futuras con fecha y hora de entrega
- Numeración automática: P-000001, P-000002, ...
- Ciclo de vida: pendiente → confirmado → en_producción → listo → entregado
- Anticipo y saldo por cobrar
- Items con producto del catálogo o descripción personalizada (tortas, pedidos especiales)
- Opción "Para delivery" que vincula con el módulo de reparto
- **Comanda impresa** en impresora térmica con detalle completo del pedido

### Reparto / Delivery
- **Zonas de reparto**: nombre, precio de envío, tiempo estimado
- **Repartidores**: nombre, teléfono, vehículo, activo
- **Repartos**: tracking de estado (pendiente → en_ruta → entregado)
- Asignación de repartidor, registro de fecha/hora de entrega
- Vinculación con facturas y pedidos programados

### Caja Registradora
- Apertura y cierre con nombre de referencia (ej: "15-07-mañana", "15-07-tarde")
- Resumen de ventas por tipo de documento y método de pago
- Bloqueo de cierre si hay pedidos programados pendientes
- Ticket 80mm y PDF A4 con formato columnas

### Dashboard
- Ventas del mes con crecimiento vs mes anterior
- Distribución de documentos (Facturas, Boletas, NV)
- **KPIs de panadería**: producción planificada, en proceso, completada
- Pedidos pendientes, mermas del mes
- Gráfico de ventas diarias (30 días)
- Top productos más vendidos

### Impresión Térmica ESC/POS
- Print Server Node.js local en cada máquina cliente (localhost:9100)
- 7 slots de impresora: Cocina 1, Cocina 2, Bar 1, Precuenta 1/2/3, Caja
- Impresoras locales (USB) y de red (TCP puerto 9100)
- **Comandas** para pedidos programados y producción
- Cola de impresión con reintentos automáticos (hasta 3 intentos)
- Encoding CP850 con caracteres especiales (ñ, tildes)
- Auto-reinicio del servidor si se detiene

### Roles y Permisos
- Roles: **Administrador**, **Cajero**, **Panadero**, **Usuario**
- Permisos granulares por módulo (protección, pedidos, reparto, recetas)
- Control de acceso vía `@can('permission', 'slug')` en vistas y `$this->authorize()` en controladores
- Admin/superadmin con bypass automático de todos los permisos

### Gestión de Empresas
- Soporte multi-empresa con series separadas
- Configuración de **IGV**: General (18%) o Reducido (10.5%), ambos editables
- Certificado digital por empresa (PEM-first para OpenSSL 3.0)
- Datos SUNAT: tipo contribuyente, ubigeo, etc.
- Logotipo personalizado

### Compras y Consumo Interno
- Búsqueda de productos en tiempo real al agregar items
- Gestión de proveedores
- Consumo interno con cantidades fraccionarias (4 decimales)
- Anulación con reincorporación automática al stock

### Productos Compuestos
- Productos formados por uno o más componentes (ej: "Promo Navideña")
- Al vender un producto compuesto: descuenta automáticamente el stock de cada componente
- Útil para promociones, packs, combos

---

## Arquitectura de Impresión

```
Navegador / Sistema
  │
  ├── Imprimir Comanda (Pedido Programado)
  │   └── PrintService::printScheduledOrderComanda()
  │       ├── Genera texto ESC/POS con PlainTextTicket
  │       ├── Encola PrintJob en base de datos
  │       └── ProcessQueue() → HTTP POST → localhost:9100/print
  │
  ├── Cliente imprime factura/ticket de venta
  │   └── PrintService::printInvoice()
  │
  └── Clic en "Caja" (abrir cajón)
      └── POST /pos/open-drawer → comando ESC/POS → localhost:9100/print
```

**Print Server** (Node.js en `print-server-node/server.js`):
- Recibe datos ESC/POS en base64 vía REST API
- Convierte a CP850 y envía a impresora local o de red
- Endpoints: `GET /status`, `GET /printers`, `POST /print`, `POST /print-escpos-text`, `GET /open-drawer`

**Reintentos**: `php artisan print:process-queue` cada minuto vía scheduler (hasta 3 intentos).

---

## Requisitos

- **PHP** 8.2+
- **MySQL** 8.0+ / MariaDB 10.4+
- **Composer**
- **Node.js** 18+ (para Print Server)
- Extensiones PHP: `openssl`, `xml`, `zip`, `mbstring`, `pdo_mysql`, `curl`, `soap`, `intl`

---

## Instalación

```bash
# 1. Clonar
git clone <repo-url> facturapanaderia
cd facturapanaderia

# 2. Dependencias PHP
composer install

# 3. Configurar .env
cp .env.example .env
# Editar DB_DATABASE, DB_USERNAME, DB_PASSWORD
php artisan key:generate

# 4. Migrar y seedear
php artisan migrate
php artisan db:seed

# 5. Link storage
php artisan storage:link

# 6. Print Server (en cada máquina cliente)
cd print-server-node
npm install
```

### Instalación en Linux (VPS)

```bash
# Permisos de directorios
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# Permisos para mpdf (generación de PDFs)
chmod -R 775 vendor/mpdf/mpdf/ttfontdata
mkdir -p storage/fonts
chmod -R 775 storage/fonts

# Scheduler (crontab)
# * * * * * cd /ruta/proyecto && php artisan schedule:run >> /dev/null 2>&1

# Optimización para producción
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Iniciar Print Server (Windows)

```bash
# Oculto en segundo plano (recomendado):
start-hidden.vbs

# Con ventana visible (autoreinicio):
start.bat

# Instalación definitiva (acceso directo + inicio automático):
install.bat
```

---

## Configuración

### Impresoras

Los slots se configuran en `/printers`:

| Slot | assigned_to | Uso |
|------|-------------|-----|
| Cocina 1 | cocina-1 | Comandas de producción |
| Cocina 2 | cocina-2 | Producción secundaria |
| Bar 1 | bar-1 | Sección bebidas/postres |
| Precuenta | precuenta | Precuenta 1 |
| Precuenta 2 | precuenta2 | Precuenta 2 |
| Precuenta 3 | precuenta3 | Precuenta 3 |
| Caja | caja | Tickets de venta + cajón |

### IGV Configurable

En `/companies/{id}/edit`:
- **General**: IGV 18% (por defecto)
- **Restaurante**: IGV 10.5% (Ley MYPE — aplica a panaderías)
- Ambos porcentajes son editables

---

## Uso

### POS (Punto de Venta)
1. `/pos` — Abrir caja primero, luego vender
2. Buscar productos por nombre o código de barras
3. Agregar al carrito, seleccionar cliente y método de pago
4. Cobrar → emite comprobante con opción de envío a SUNAT

### Recetas
1. `/recipes` — Lista de recetas con costo estimado
2. Crear receta: nombre, producto resultante, ingredientes del inventario
3. Cada ingrediente: producto, cantidad, unidad, merma %, costo unitario
4. El costo estimado se calcula automáticamente

### Órdenes de Producción
1. `/production-orders` — Lista de órdenes por estado
2. Crear: seleccionar receta o producto, fecha, cantidad planificada
3. **Iniciar**: descuenta ingredientes del inventario automáticamente
4. **Completar**: ingresa cantidad real producida, incrementa stock

### Mermas
1. `/waste` — Registro de pérdidas con total del mes
2. Nueva merma: producto, fecha, cantidad, motivo
3. Calcula costo de pérdida y descuenta stock automáticamente
4. Al eliminar: restaura stock

### Pedidos Programados
1. `/scheduled-orders` — Encargos de clientes
2. Nuevo pedido: cliente, fecha de entrega, items, anticipo
3. Seguimiento de estado: pendiente → confirmado → en_producción → listo → entregado
4. **Imprimir Comanda**: ticket con detalle para el área de producción

### Reparto
1. Configurar zonas (`/delivery-zones`) y repartidores (`/delivery-persons`)
2. Crear reparto (`/deliveries`): factura, zona, dirección
3. Asignar repartidor, iniciar ruta, completar entrega

### Caja Registradora
1. `/cashregisters` — Abrir caja con nombre de referencia
2. Durante el turno se registran todas las ventas
3. Al cerrar: verifica que no haya pedidos programados pendientes
4. Resumen en web, ticket 80mm y PDF A4

---

## Credenciales por Defecto

| Email | Contraseña | Rol |
|-------|-----------|-----|
| admin@example.com | secret123 | Administrador |
| manager@example.com | adminpass | Administrador |
| Caja@gmail.com | 222938 | Cajero |
| panadero@gmail.com | panadero123 | Panadero |
| demo@example.com | password | Usuario |

---

## Comandos Útiles

```bash
# Cache
php artisan cache:clear
php artisan view:clear
php artisan config:clear
php artisan route:clear

# Seeders específicos
php artisan db:seed --class=PrinterSeeder
php artisan db:seed --class=PermissionsSeeder
php artisan db:seed --class=UbigeoSeeder

# SUNAT
php artisan sunat:send-daily-summary    # Enviar resumen diario
php artisan sunat:check-summaries       # Consultar tickets pendientes

# Cola de impresión
php artisan print:process-queue

# Ver rutas
php artisan route:list

# Logs
tail -50 storage/logs/laravel.log
```

---

## Licencia

MIT
