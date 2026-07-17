# Documentación del Sistema FacturaPanadería

---

## 1. Visión General

**FacturaPanadería** es un sistema integral de gestión de panadería y pastelería con facturación electrónica SUNAT (Perú), desarrollado en **Laravel 13.x** con **MySQL** y **Node.js**.

Basado en FacturaFácil, ha sido transformado de un sistema de gestión de restaurante a un sistema especializado en panadería, manteniendo toda la infraestructura de facturación electrónica.

### Arquitectura General

```
┌─────────────────────────────────────────────────────────────┐
│                   Navegador Web (Cliente)                    │
│  ┌──────────┐  ┌──────────┐  ┌──────────┐  ┌────────────┐  │
│  │POS/Ventas│  │Producción│  │Pedidos   │  │Admin Panel │  │
│  └──────────┘  └──────────┘  └──────────┘  └────────────┘  │
└──────────────────────────┬──────────────────────────────────┘
                           │ HTTP
┌──────────────────────────▼──────────────────────────────────┐
│                   Servidor Laravel                           │
│  ┌─────────────┐  ┌──────────────┐  ┌──────────────────┐   │
│  │ Controllers  │  │  Services    │  │   Models/Eloquent│   │
│  └─────────────┘  └──────────────┘  └──────────────────┘   │
│                          │                                   │
│  ┌───────────────────────▼──────────────────────────────┐   │
│  │               Base de Datos MySQL                     │   │
│  └──────────────────────────────────────────────────────┘   │
└──────────────────────────────────────────────────────────────┘
                           │ HTTP (solo impresión servidor)
┌──────────────────────────▼──────────────────────────────────┐
│              Print Server Node.js (localhost:9100)           │
│  ┌──────────┐  ┌──────────────┐  ┌──────────────────────┐  │
│  │Impresora  │  │ Impresora    │  │   Cajón de Efectivo │  │
│  │ Local USB │  │ de Red (IP)  │  │   (Drawer Kick)     │  │
│  └──────────┘  └──────────────┘  └──────────────────────┘  │
└──────────────────────────────────────────────────────────────┘
```

### Tecnologías

| Componente | Tecnología |
|-----------|-----------|
| Backend | PHP 8.2+ / Laravel 13.x |
| Frontend | Blade + AdminLTE 3.2 + Chart.js |
| Base de Datos | MySQL 8.0+ / MariaDB 10.4+ |
| Facturación SUNAT | Greenter 5.x |
| Print Server | Node.js 18+ / Express |
| Impresión Térmica | ESC/POS via raw-print.ps1 |
| PDF | mpdf, Greenter HtmlToPdf |
| Build Tools | Vite + Tailwind CSS |

### Módulos del Sistema

| Módulo | Descripción |
|--------|-------------|
| Dashboard | KPIs de ventas, producción, mermas y pedidos |
| POS | Punto de venta rápido con búsqueda de productos |
| Facturación Electrónica | Facturas, Boletas, NC/ND, Resumen Diario SUNAT |
| Recetas | Fórmulas de producción con ingredientes y costos |
| Órdenes de Producción | Planificación y ejecución de producción |
| Mermas | Registro de pérdidas y desperdicios |
| Pedidos Programados | Encargos de clientes con fecha de entrega |
| Reparto | Gestión de delivery: zonas, repartidores, seguimiento |
| Caja | Apertura, cierre y conciliación |
| Inventario | Stock, compras, consumo interno, productos compuestos |

---

## 2. Base de Datos

### 2.1 Tablas Principales

| Tabla | Propósito |
|-------|-----------|
| `companies` | Empresas (RUC, datos SUNAT, certificado, IGV) |
| `users` | Usuarios del sistema (roles: admin, user, panadero, cajero, superadmin) |
| `customers` | Clientes (DNI/RUC, dirección, ubigeo) |
| `products` | Productos (código, precio, precio_compra, stock, IGV, is_composite) |
| `product_components` | Componentes de productos compuestos |
| `categories` | Categorías de productos |
| `invoices` | Comprobantes emitidos (facturas, boletas, NV) |
| `invoice_items` | Items de comprobantes |
| `series` | Series documentales (F001, B001, NV01, etc.) |
| `recipes` | Recetas y fórmulas de producción |
| `recipe_ingredients` | Ingredientes de cada receta |
| `production_orders` | Órdenes de producción |
| `waste_records` | Registro de mermas |
| `scheduled_orders` | Pedidos programados / encargos |
| `scheduled_order_items` | Items de pedidos programados |
| `deliveries` | Repartos / entregas a domicilio |
| `delivery_zones` | Zonas de reparto |
| `delivery_persons` | Repartidores |
| `cashregisters` | Registros de apertura/cierre de caja |
| `printers` | Configuración de impresoras |
| `print_jobs` | Cola de impresión |
| `purchases` | Compras a proveedores |
| `purchase_items` | Items de compras |
| `suppliers` | Proveedores |
| `auxiliary_items` | Elementos auxiliares para productos |
| `ubigeos` | Catálogo de ubigeos (departamento, provincia, distrito) |
| `roles` | Roles del sistema |
| `permissions` | Permisos del sistema |
| `role_user` | Asignación de roles a usuarios |
| `permission_role` | Asignación de permisos a roles |

### 2.2 Relaciones Clave

```
companies ──┬── users
            ├── customers
            ├── products ─── categories
            │             └── product_components (self-referencing)
            ├── invoices ─── invoice_items ─── products
            ├── series
            ├── recipes ─── recipe_ingredients ─── products
            │           └── production_orders
            ├── waste_records ─── products
            ├── scheduled_orders ─── scheduled_order_items ─── products
            │                   └── customers
            ├── deliveries ─── delivery_zones
            │             ├── delivery_persons
            │             └── invoices
            ├── cashregisters
            └── purchases ─── purchase_items
```

### 2.3 Migraciones

| Migración | Descripción |
|-----------|-------------|
| `0001_01_01_000000` | Tabla de usuarios |
| `2024_01_01_000001` | Tabla de empresas |
| `2024_01_01_000002` | Tabla de clientes |
| `2024_01_01_000003` | Tabla de productos |
| `2024_01_01_000005` | Tabla de comprobantes |
| `2024_01_01_000006` | Items de comprobantes |
| `2026_04_27_000001` | Categorías |
| `2026_04_27_000002` | Stock, proveedores, compras |
| `2026_05_01_create_cashregisters` | Caja registradora |
| `2026_05_13_000001` | Roles y permisos |
| `2026_05_14_170403` | Impresoras |
| `2026_05_15_104058` | Cola de impresión |
| `2026_05_21_083855` | Configuración IGV en empresas |
| `2026_05_31_000001` | Consumo interno (stock outputs) |
| `2026_06_12_000001` | Documentos especiales |
| `2026_07_03_085441` | Elementos auxiliares |
| `2026_07_05_000002` | Productos compuestos (is_composite) |
| `2026_07_05_000003` | Componentes de productos compuestos |
| `2026_07_07_181559` | Precio de compra en productos |
| `2026_07_16_000001` | **Recetas** |
| `2026_07_16_000002` | **Ingredientes de recetas** |
| `2026_07_16_000003` | **Órdenes de producción** |
| `2026_07_16_000004` | **Registro de mermas** |
| `2026_07_16_000005` | **Zonas de reparto** |
| `2026_07_16_000006` | **Repartidores** |
| `2026_07_16_000007` | **Repartos / Deliveries** |
| `2026_07_16_000008` | **Pedidos programados** |

---

## 3. Modelos

### 3.1 User

```php
role: admin | user | panadero | cajero | superadmin
company_id → companies

// Métodos clave:
isAdmin()          // role === 'admin'
isUser()           // role === 'user'
isSuperAdmin()     // role === 'superadmin'
hasPermission($slug)  // Verifica permisos vía roles o role string
```

### 3.2 Company

```php
ruc, razon_social, nombre_comercial, direccion
departamento, provincia, distrito, ubigeo
telefono, email, logo
certificado_path, certificado_password, certificado_vence
tipo_contribuyente, estado
soap_type_id (01=Beta, 02=Producción)
soap_username, soap_password
tax_type (general | restaurant)
igv_percent (default 18.00)
reduced_igv_percent (default 10.50)

// Métodos clave:
getActiveIgvPercent()  // Retorna % según tax_type
getIgvRate()           // getActiveIgvPercent() / 100
getMainCompany()       // Empresa principal o primera activa
```

### 3.3 Product

```php
company_id → companies
category_id → categories
codigo, codigo_barras, descripcion, codigo_sunat
umedida_codigo, precio, precio_minimo, precio_compra
tipo_afectacion, igv_percent, estado
stock, is_composite (boolean)

// Relaciones
components() → hasMany(ProductComponent::class, 'parent_product_id')
recipeIngredients() → hasMany(RecipeIngredient::class)

// Métodos
isComposite(): bool
scopeSimple($query)
scopeComposite($query)
```

### 3.4 Invoice

```php
company_id → companies
customer_id → customers
tipo_documento: 01=Factura | 03=Boleta | NV=NotaVenta
serie, numero, full_number
fecha_emision, hora_emision, fecha_vencimiento
moneda, subtotal, gravado, igv, total, total_letras
metodo_pago, referencia_pago
sunat_estado: PENDIENTE | ENVIADO | ACEPTADO | RECHAZADO | ANULADO
codigo_hash (para QR)
```

### 3.5 Recipe (Receta)

```php
company_id → companies
result_product_id → products (producto final de la receta)
nombre, descripcion
cantidad_producida, unidad (UNIDAD, KG, LT, DOCENA)
tiempo_preparacion_min
instrucciones
costo_estimado (calculado de ingredientes)
activa (boolean)

// Relaciones
ingredients() → hasMany(RecipeIngredient::class)
productionOrders() → hasMany(ProductionOrder::class)
```

### 3.6 RecipeIngredient (Ingrediente de Receta)

```php
recipe_id → recipes
product_id → products
cantidad, unidad
merma_porcentaje
costo_unitario

// Relaciones
recipe() → belongsTo(Recipe::class)
product() → belongsTo(Product::class)
```

### 3.7 ProductionOrder (Orden de Producción)

```php
company_id → companies
recipe_id → recipes (nullable)
product_id → products (nullable, si no usa receta)
user_id → users
fecha_produccion
cantidad_planificada, cantidad_producida
unidad
estado: planificado | en_proceso | completado | cancelado
costo_total
notas

// Relaciones
recipe() → belongsTo(Recipe::class)
product() → belongsTo(Product::class)
user() → belongsTo(User::class)
```

### 3.8 WasteRecord (Registro de Merma)

```php
company_id → companies
product_id → products
user_id → users
fecha
cantidad, unidad
motivo: vencido | danado | devolucion | no_vendido | produccion | otro
costo_perdida
notas

// Relaciones
product() → belongsTo(Product::class)
user() → belongsTo(User::class)
```

### 3.9 ScheduledOrder (Pedido Programado)

```php
company_id → companies
customer_id → customers
user_id → users
order_number (P-000001, P-000002, ...)
fecha_pedido, fecha_entrega, hora_entrega
estado: pendiente | confirmado | en_produccion | listo | entregado | cancelado
subtotal, igv, total, anticipo, saldo
descripcion, notas, telefono_contacto
para_delivery (boolean)

// Relaciones
items() → hasMany(ScheduledOrderItem::class)
customer() → belongsTo(Customer::class)
```

### 3.10 Delivery (Reparto)

```php
company_id → companies
invoice_id → invoices (nullable)
delivery_zone_id → delivery_zones (nullable)
delivery_person_id → delivery_persons (nullable)
direccion, referencia, telefono_contacto
costo_envio
estado: pendiente | en_ruta | entregado | cancelado
fecha_entrega, notas

// Relaciones
invoice() → belongsTo(Invoice::class)
deliveryZone() → belongsTo(DeliveryZone::class)
deliveryPerson() → belongsTo(DeliveryPerson::class)
```

### 3.11 CashRegister

```php
company_id, user_id
monto_apertura, monto_cierre
ventas_efectivo, ventas_tarjeta, ventas_yape, ventas_plin, ventas_otro
cantidad_ventas, total_ventas
estado: ABIERTA | CERRADA
fecha_apertura, fecha_cierre
observaciones, referencia
```

---

## 4. Controladores

### 4.1 RecipeController (Recetas)

| Método | Ruta | Propósito |
|--------|------|-----------|
| `index()` | GET `/recipes` | Lista de recetas |
| `create()` | GET `/recipes/create` | Formulario nueva receta |
| `store()` | POST `/recipes` | Guarda receta con ingredientes |
| `show()` | GET `/recipes/{id}` | Ver detalle de receta |
| `edit()` | GET `/recipes/{id}/edit` | Editar receta |
| `update()` | PUT `/recipes/{id}` | Actualizar receta e ingredientes |
| `destroy()` | DELETE `/recipes/{id}` | Eliminar receta |

#### Flujo de Crear Receta

```
POST /recipes → store() [PHP]:
    1. Valida: nombre, unidad, cantidad_producida, ingredientes[]
    2. Crea Recipe con company_id y costo_estimado=0
    3. Por cada ingrediente:
       - Guarda RecipeIngredient (product_id, cantidad, unidad, merma%, costo_unitario)
       - Suma: costoTotal += costo_unitario × cantidad
    4. Actualiza Recipe.costo_estimado = costoTotal
```

### 4.2 ProductionOrderController (Órdenes de Producción)

| Método | Ruta | Propósito |
|--------|------|-----------|
| `index()` | GET `/production-orders` | Lista de órdenes |
| `create()` | GET `/production-orders/create` | Nueva orden |
| `store()` | POST `/production-orders` | Guardar orden |
| `show()` | GET `/production-orders/{id}` | Ver detalle |
| `start()` | POST `/production-orders/{id}/start` | Iniciar producción |
| `complete()` | POST `/production-orders/{id}/complete` | Completar producción |
| `cancel()` | POST `/production-orders/{id}/cancel` | Cancelar orden |
| `destroy()` | DELETE `/production-orders/{id}` | Eliminar orden |

#### Flujo de Iniciar Producción

```
POST /production-orders/{id}/start → start() [PHP]:
    1. Cambia estado a "en_proceso"
    2. Si tiene receta asociada:
       Por cada ingrediente de la receta:
         - Cantidad necesaria = ingrediente.cantidad × orden.cantidad_planificada
         - Merma = cantidad × (ingrediente.merma% / 100)
         - Total a descontar = cantidad + merma
         - Decrementa producto.stock -= total_descontar
    3. Ingredientes descontados del inventario automáticamente
```

#### Flujo de Completar Producción

```
POST /production-orders/{id}/complete → complete() [PHP]:
    1. Valida: cantidad_producida requerida
    2. Cambia estado a "completado"
    3. Guarda cantidad_producida
    4. Si tiene product_id: incrementa stock += cantidad_producida
    5. Producto terminado ingresa al inventario
```

### 4.3 WasteRecordController (Mermas)

| Método | Ruta | Propósito |
|--------|------|-----------|
| `index()` | GET `/waste` | Lista de mermas con total de pérdidas |
| `create()` | GET `/waste/create` | Nueva merma |
| `store()` | POST `/waste` | Guardar merma |
| `destroy()` | DELETE `/waste/{id}` | Eliminar y restaurar stock |

#### Flujo de Registrar Merma

```
POST /waste → store() [PHP]:
    1. Valida: producto, fecha, cantidad, motivo
    2. Calcula costo_perdida = producto.precio_compra × cantidad
    3. Crea WasteRecord
    4. Decrementa producto.stock -= cantidad
    5. Inventario actualizado automáticamente
```

### 4.4 ScheduledOrderController (Pedidos Programados)

| Método | Ruta | Propósito |
|--------|------|-----------|
| `index()` | GET `/scheduled-orders` | Lista de pedidos |
| `create()` | GET `/scheduled-orders/create` | Nuevo pedido |
| `store()` | POST `/scheduled-orders` | Guardar pedido |
| `show()` | GET `/scheduled-orders/{id}` | Ver detalle |
| `confirm()` | POST `/scheduled-orders/{id}/confirm` | Confirmar pedido |
| `startProduction()` | POST `/scheduled-orders/{id}/start-production` | Enviar a producción |
| `markReady()` | POST `/scheduled-orders/{id}/mark-ready` | Marcar listo |
| `deliver()` | POST `/scheduled-orders/{id}/deliver` | Marcar entregado |
| `cancel()` | POST `/scheduled-orders/{id}/cancel` | Cancelar pedido |
| `printComanda()` | GET `/scheduled-orders/{id}/print-comanda` | Imprimir comanda |

#### Flujo de Vida del Pedido

```
1. pendiente       → Cliente hace encargo, queda registrado
2. confirmado      → Se confirma disponibilidad y fecha
3. en_produccion   → Panadero comienza elaboración
4. listo           → Producto terminado, listo para recoger/entregar
5. entregado       → Cliente recibe el pedido
   └─ cancelado    → En cualquier punto puede cancelarse
```

#### Generación de Comanda

```
GET /scheduled-orders/{id}/print-comanda → printComanda() [PHP]:
    → PrintService::printScheduledOrderComanda($order)
        → PlainTextTicket::scheduledOrderComanda($order)
            → Ticket ESC/POS con: pedido, cliente, items, total, anticipo, saldo
        → queuePrint() + processQueue()
    → Se imprime en impresora configurada como "caja"
```

### 4.5 Delivery Controllers (Reparto)

| Controlador | Rutas | Propósito |
|-------------|-------|-----------|
| DeliveryController | `/deliveries` | CRUD repartos + assign/start/complete/cancel |
| DeliveryZoneController | `/delivery-zones` | CRUD zonas de reparto |
| DeliveryPersonController | `/delivery-persons` | CRUD repartidores |

#### Flujo de Reparto

```
1. pendiente   → Reparto creado, sin repartidor asignado
2. en_ruta     → Repartidor asignado, en camino
3. entregado   → Cliente recibió el pedido
   └─ cancelado → Reparto cancelado
```

### 4.6 PosController (Punto de Venta)

| Método | Ruta | Propósito |
|--------|------|-----------|
| `index()` | GET `/pos` | Vista POS (verifica caja abierta) |
| `store()` | POST `/pos` | Procesa venta |
| `sendToSunat()` | POST `/pos/sunat/{id}` | Envía a SUNAT |
| `openDrawer()` | POST `/pos/open-drawer` | Abre cajón de efectivo |

### 4.7 CashRegisterController (Caja)

| Método | Ruta | Propósito |
|--------|------|-----------|
| `index()` | GET `/cashregisters` | Vista caja (abrir/cerrar/historial) |
| `open()` | POST `/cashregister/open` | Abre caja |
| `close()` | POST `/cashregister/close` | Cierra caja |
| `show()` | GET `/cashregisters/{id}` | Resumen de caja |
| `pdf()` | GET `/cashregisters/{id}/pdf` | PDF A4 |
| `ticketPdf()` | GET `/cashregisters/{id}/ticket` | Ticket 80mm |

#### Flujo de Cierre de Caja

```
close() [PHP]:
    1. Validar: monto_cierre requerido
    2. Verificar que no esté ya cerrada
    3. Verificar que no haya pedidos programados pendientes
    4. Obtener ventas del periodo (por datetime exacto)
    5. Calcular totales por método de pago y tipo documento
    6. Actualizar registro con montos
    7. Redirigir a resumen
```

### 4.8 InvoiceController (Facturación)

| Método | Ruta | Propósito |
|--------|------|-----------|
| `index()` | GET `/invoices` | Lista de comprobantes |
| `create()` | GET `/invoices/create` | Formulario de facturación |
| `store()` | POST `/invoices` | Guarda comprobante |
| `sendToSunat()` | GET `/invoices/{id}/send` | Envía a SUNAT |
| `creditNoteForm()` | GET `/invoices/{id}/credit-note` | Formulario NC |
| `debitNoteForm()` | GET `/invoices/{id}/debit-note` | Formulario ND |
| `generatePdf()` | GET `/invoices/{id}/pdf` | PDF A4 |
| `generateTicketPdf()` | GET `/invoices/{id}/ticket` | Ticket 80mm |

---

## 5. Servicios

### 5.1 PrintService (`app/Services/PrintService.php`)

Maneja la impresión de tickets ESC/POS.

```php
printScheduledOrderComanda($order)  // Comanda de pedido programado
printKitchenOrder($order)           // Ticket de cocina (adaptado para producción)
printInvoice($invoice)              // Factura/Boleta
processQueue()                      // Procesa cola de impresión
```

### 5.2 PlainTextTicket (`app/Services/PlainTextTicket.php`)

Genera texto ESC/POS para tickets térmicos.

```php
scheduledOrderComanda($order)  // Comanda con items, total, anticipo, saldo
invoiceTicket($invoice)         // Factura
cashRegisterSummary($cashregister, $data)  // Resumen de caja
```

**Encoding**: Usa CP850 (PC850) con tabla de mapeo manual para ñ, tildes y mayúsculas acentuadas.

### 5.3 GreenterService (`app/Services/GreenterService.php`)

Integración con SUNAT para facturación electrónica.

```php
sendInvoice($invoice)          // Envía factura (01) a SUNAT (BillSender)
sendCreditNote($invoice, ...)  // Envía NC (07)
sendDebitNote($invoice, ...)   // Envía ND (08)
voidInvoice($invoice)          // Da de baja factura (Voided)
generatePdf($invoice)          // Genera PDF A4
generateTicketPdf($invoice)    // Genera PDF ticket 80mm
```

setupSee() PEM-first: busca `{ruc}_certificate.pem`, si no existe usa .p12 con contraseña.

### 5.4 SummaryService (`app/Services/SummaryService.php`)

Resumen Diario para boletas y NC/ND de boletas.

### 5.5 SpecialDocumentService (`app/Services/SpecialDocumentService.php`)

Documentos especiales SUNAT: Retenciones (R), Guías (T), Percepciones (P).

### 5.6 PrintServerService (`app/Services/PrintServerService.php`)

Comunicación con el Print Server Node.js (localhost:9100).

---

## 6. Módulo de Impresión

### 6.1 Arquitectura

```
Laravel (servidor) ─── HTTP POST ───→ Print Server (localhost:9100)
                                              │
                                    ┌─────────┴─────────┐
                                    │                   │
                              raw-print.ps1      Socket TCP
                              (USB/Local)       (IP:9100)
```

### 6.2 Print Server Node.js

**Ubicación:** `print-server-node/server.js`
**Puerto:** 9100

| Endpoint | Método | Propósito |
|----------|--------|-----------|
| `/status` | GET | Health check |
| `/printers` | GET | Lista impresoras del sistema |
| `/print` | POST | Imprime datos ESC/POS en base64 |
| `/print-raw` | POST | Imprime texto plano |
| `/print-escpos-text` | POST | Genera ticket desde texto |
| `/open-drawer` | GET | Abre cajón de efectivo |

**Slots de Impresora (configurables en `/printers`):**

| Slot | assigned_to | Uso |
|------|-------------|-----|
| Cocina 1 | cocina-1 | Comandas de producción |
| Cocina 2 | cocina-2 | Producción secundaria |
| Bar 1 | bar-1 | Sección bebidas/postres |
| Precuenta | precuenta | Precuenta 1 |
| Precuenta 2 | precuenta2 | Precuenta 2 |
| Precuenta 3 | precuenta3 | Precuenta 3 |
| Caja | caja | Cajón + tickets de venta |

### 6.3 Comandos ESC/POS Soportados

| Comando | Hexadecimal | Propósito |
|---------|------------|-----------|
| INIT | 1B 40 | Inicializar impresora |
| CP850 | 1B 74 02 | Encoding latino |
| BOLD ON | 1B 45 01 | Negrita |
| BOLD OFF | 1B 45 00 | Fin negrita |
| ALIGN LEFT | 1B 61 00 | Alinear izquierda |
| ALIGN CENTER | 1B 61 01 | Centrar |
| ALIGN RIGHT | 1B 61 02 | Alinear derecha |
| DOUBLE ON | 1B 21 30 | Doble altura/ancho |
| CUT | 1D 56 00 | Corte parcial |
| FEED | 1B 64 05 | Avanzar 5 líneas |
| DRAWER KICK | 1B 70 00 32 FF | Abrir cajón |

---

## 7. Módulo de Roles y Permisos

### 7.1 Roles del Sistema

| Rol (slug) | Descripción | Permisos Clave |
|-------------|-------------|----------------|
| `admin` | Acceso completo | Todos los módulos |
| `cajero` | Ventas, caja, pedidos, repartos | POS, facturación, caja, pedidos programados, delivery |
| `panadero` | Producción | Recetas, órdenes de producción, mermas |
| `user` | Consultas y ventas básicas | Dashboard, POS, comprobantes, productos, recetas |
| `superadmin` | Acceso completo | Equivalente a admin |

### 7.2 Permisos por Módulo

| Módulo | Permisos |
|--------|----------|
| Dashboard | view_dashboard |
| Empresas | view/create/edit/delete_companies |
| Usuarios | view/create/edit/delete_users |
| Roles | view/create/edit_roles |
| Permisos | view/create_permissions |
| Clientes | view/create/edit/delete_customers |
| Productos | view/create/edit/delete_products, view_categories |
| Comprobantes | view/create_invoices, send_sunat |
| Compras | view/create_purchases |
| Proveedores | view/create_suppliers |
| Caja | view_cashregisters, open_cashregister, close_cashregister |
| POS | view_pos, use_pos |
| **Recetas** | view/create/edit/delete_recipes |
| **Producción** | view/create_production_orders, manage_production |
| **Mermas** | view/create_waste |
| **Pedidos Programados** | view/create_scheduled_orders, manage_scheduled_orders |
| **Reparto** | view/create/manage_deliveries, view_delivery_zones, view_delivery_persons |
| Elementos Auxiliares | view_auxiliary_items |
| Series | view_series |
| Impresoras | view_printers, view_print_queue |

### 7.3 Verificación de Permisos

```php
1. Si es admin/superadmin → TRUE (todo permitido)
2. Si tiene un rol en role_user con el permiso → TRUE
3. Si su string role coincide con un slug de rol con el permiso → TRUE
```

En vistas: `@can('permission', 'slug')`
En controladores: `$this->authorize('permission', 'slug')`

---

## 8. Configuración de IGV

### 8.1 Tipos de Impuesto

| Tipo | Porcentaje | Uso |
|------|-----------|-----|
| **General** | 18% (por defecto, editable) | IGV estándar |
| **Restaurante** | 10.5% (por defecto, editable) | Ley MYPE (aplica a panaderías) |

### 8.2 Dónde se Aplica

| Proceso | Archivo | Línea(s) |
|---------|---------|----------|
| Venta POS | `PosController::store()` | `$company->getIgvRate()` |
| Factura desde módulo | `InvoiceController::store()` | `$company->getIgvRate()` |
| Pedido programado | `ScheduledOrderController::store()` | IGV fijo 18% |
| XML SUNAT | `GreenterService::buildInvoice()` | `$company->getIgvRate()` |

---

## 9. Módulo de Caja

### 9.1 Flujo de Operación

```
1. ABRIR CAJA
   → POST /cashregister/open
   → Requiere permiso: open_cashregister
   → Validación: solo una caja abierta por empresa
   
2. OPERACIONES (durante turno)
   → Ventas POS: se registran en Invoice
   → Ventas desde módulo facturación: se registran en Invoice
   → Stock: se descuenta (permite negativo)
   
3. CERRAR CAJA
   → POST /cashregister/close
   → Validación: no pedidos programados pendientes (no entregados ni cancelados)
   → Filtro ventas: por datetime exacto (fecha_emision + hora_emision)
   → Calcula: totales por método de pago y tipo documento
```

---

## 10. Procesos de Stock

| Acción | Efecto en Stock | Controlador |
|--------|----------------|-------------|
| Vender en POS | Decremento | `PosController::store()` |
| Facturar desde módulo | Decremento | `InvoiceController::store()` |
| Iniciar producción | Decremento de ingredientes | `ProductionOrderController::start()` |
| Completar producción | Incremento de producto terminado | `ProductionOrderController::complete()` |
| Registrar merma | Decremento | `WasteRecordController::store()` |
| Eliminar merma | Incremento (restaura) | `WasteRecordController::destroy()` |
| Comprar (ingresar) | Incremento + actualiza precio_compra | `PurchaseController::store()` |
| Consumo interno | Decremento | `StockOutputController::store()` |

### Productos Compuestos

- Los productos compuestos tienen `is_composite = true` y `stock = 0` (no manejan stock propio)
- Al vender un producto compuesto, se descuenta automáticamente el stock de cada componente
- Fórmula: `stock_componente -= cantidad_componente × cantidad_vendida`

---

## 11. Dashboard

### KPIs de Panadería

| Indicador | Fuente |
|-----------|--------|
| Ventas del mes | Suma de invoices del mes actual |
| Crecimiento vs mes anterior | Comparación con mes anterior |
| Total documentos | Conteo del mes |
| Aceptados SUNAT | Conteo con estado ACEPTADO |
| Pendientes | Conteo con estado PENDIENTE/ENVIADO |
| **Producción planificada** | Órdenes con estado 'planificado' |
| **Producción en proceso** | Órdenes con estado 'en_proceso' |
| **Producción completada (mes)** | Órdenes completadas del mes |
| **Pedidos pendientes** | Pedidos no entregados ni cancelados |
| **Mermas del mes** | Suma de costo_perdida del mes |
| Gráfico 30 días | Ventas diarias del último mes |
| Top productos | Productos más vendidos del mes |

---

## 12. Seeders

| Seeder | Datos que crea |
|--------|---------------|
| `AdminUserSeeder` | Empresa demo + usuarios admin |
| `SuperAdminSeeder` | Usuario Cajero |
| `TestUsersSeeder` | Usuarios demo (admin, panadero, user) + roles en pivot |
| `SeriesSeeder` | Series F001, B001, NV01, FC01, BC01, FD01, BD01 |
| `SunatProductSeeder` | Productos de ejemplo |
| `PermissionsSeeder` | 50+ permisos + roles (admin, panadero, cajero, user) |
| `PrinterSeeder` | 7 slots de impresora: cocina-1, cocina-2, bar-1, precuenta, precuenta2, precuenta3, caja |
| `UbigeoSeeder` | 1874 registros de ubigeos |
| `CustomerSeeder` | Cliente "Clientes Varios" (DNI 88888888) |

---

## 13. Comandos Artisan

| Comando | Propósito |
|---------|-----------|
| `php artisan print:process-queue` | Procesa cola de impresión |
| `php artisan sunat:send-daily-summary` | Envía resumen diario de boletas |
| `php artisan sunat:check-summaries` | Consulta estado de tickets |
| `php artisan config:clear` | Limpia cache de configuración |
| `php artisan view:clear` | Limpia cache de vistas |
| `php artisan route:clear` | Limpia cache de rutas |
| `php artisan cache:clear` | Limpia cache de Laravel |
| `php artisan migrate` | Ejecuta migraciones |
| `php artisan db:seed` | Ejecuta seeders |

---

## 14. Facturación Electrónica SUNAT

### 14.1 Flujo según Tipo de Documento

| Documento | Envío | Servicio |
|-----------|-------|----------|
| Factura (01) | Individual (BillSender) | GreenterService::sendInvoice() |
| Boleta (03) | Resumen Diario (SummarySender) | SummaryService::sendBoletaToSummary() |
| NC Factura (FC01, 07) | Individual (BillSender) | GreenterService::sendCreditNote() |
| NC Boleta (BC01, 07) | Resumen Diario | sendNoteViaSummary() |
| ND Factura (FD01, 08) | Individual (BillSender) | GreenterService::sendDebitNote() |
| ND Boleta (BD01, 08) | Resumen Diario | sendNoteViaSummary() |
| Retención (R001, 20) | Individual | SpecialDocumentService::sendRetention() |
| Guía Remisión (T001, 09) | Individual | SpecialDocumentService::sendDespatch() |
| Percepción (P001, 40) | Individual | SpecialDocumentService::sendPerception() |

Nota de Venta (NV) no se envía a SUNAT.

### 14.2 Certificado Digital (PEM-first con OpenSSL 3.0)

```php
// Busca primero el PEM (compatible con OpenSSL 3.0)
$pemPath = storage_path("app/certificates/{$company->ruc}_certificate.pem");
if (file_exists($pemPath)) {
    $this->see->setCertificate(file_get_contents($pemPath));
} else {
    // Fallback a PKCS12 (.p12) con contraseña
    $cert = new X509Certificate($pfxContent, $password);
    $this->see->setCertificate($cert->export(X509ContentType::PEM));
}
```

---

## 15. Procedimientos Detallados — Código

### 15.1 Instalación del Sistema

```bash
git clone <repo> facturapanaderia
cd facturapanaderia
composer install
cp .env.example .env   # configurar DB: facturapanaderia
php artisan key:generate
php artisan migrate
php artisan db:seed
php artisan storage:link
cd print-server-node
npm install
```

---

### 15.2 Módulo de Recetas (Recipes)

#### 15.2.1 Modelo `Recipe`

```php
// app/Models/Recipe.php — $fillable y $casts
protected $fillable = [
    'company_id', 'nombre', 'descripcion', 'result_product_id',
    'cantidad_producida', 'unidad', 'tiempo_preparacion_min',
    'instrucciones', 'costo_estimado', 'activa',
];

protected $casts = [
    'cantidad_producida' => 'decimal:4',
    'costo_estimado' => 'decimal:4',
    'activa' => 'boolean',
];

// Relaciones
public function resultProduct()  // Producto final que produce esta receta
public function ingredients()    // hasMany RecipeIngredient
public function productionOrders() // hasMany ProductionOrder
```

#### 15.2.2 Modelo `RecipeIngredient`

```php
// app/Models/RecipeIngredient.php
protected $fillable = [
    'recipe_id', 'product_id', 'cantidad', 'unidad',
    'merma_porcentaje', 'costo_unitario',
];

protected $casts = [
    'cantidad' => 'decimal:4',
    'merma_porcentaje' => 'decimal:2',
    'costo_unitario' => 'decimal:4',
];

public function recipe()    // belongsTo Recipe
public function product()   // belongsTo Product
```

#### 15.2.3 Esquema de Base de Datos

```sql
-- Tabla: recipes (migración 2026_07_16_000001)
CREATE TABLE recipes (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id BIGINT UNSIGNED NOT NULL,
    nombre VARCHAR(255) NOT NULL,
    descripcion TEXT NULL,
    result_product_id BIGINT UNSIGNED NULL,
    cantidad_producida DECIMAL(10,4) DEFAULT 0,
    unidad VARCHAR(50) DEFAULT 'UNIDAD',
    tiempo_preparacion_min INT NULL,
    instrucciones TEXT NULL,
    costo_estimado DECIMAL(12,4) DEFAULT 0,
    activa TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
    FOREIGN KEY (result_product_id) REFERENCES products(id) ON DELETE SET NULL
);

-- Tabla: recipe_ingredients (migración 2026_07_16_000002)
CREATE TABLE recipe_ingredients (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    recipe_id BIGINT UNSIGNED NOT NULL,
    product_id BIGINT UNSIGNED NOT NULL,
    cantidad DECIMAL(10,4) NOT NULL,
    unidad VARCHAR(50) DEFAULT 'KG',
    merma_porcentaje DECIMAL(5,2) DEFAULT 0,
    costo_unitario DECIMAL(12,4) DEFAULT 0,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (recipe_id) REFERENCES recipes(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);
```

#### 15.2.4 Crear Receta — Flujo Completo de Código

```
1. GET /recipes/create
   → RecipeController::create()
   → Carga $products = Product::where('company_id', $this->companyId())->orderBy('descripcion')->get()
   → Retorna vista recipes.create con listado de productos del inventario

2. Frontend (recipes/create.blade.php):
   → Formulario con campos: nombre, descripcion, result_product_id (select),
     cantidad_producida, unidad, tiempo_preparacion_min, instrucciones
   → Sección "Ingredientes" con botón "+ Agregar Ingrediente"
   → Cada fila de ingrediente tiene: product_id (select), cantidad, unidad,
     merma_porcentaje, costo_unitario, botón "×" para eliminar
   → JavaScript maneja add/remove dinámico de filas

3. POST /recipes
   → RecipeController::store($request)
```

```php
// RecipeController::store() — Línea 34-77
public function store(Request $request)
{
    // 1. Validación
    $validated = $request->validate([
        'nombre' => 'required|string|max:255',
        'descripcion' => 'nullable|string',
        'result_product_id' => 'nullable|exists:products,id',
        'cantidad_producida' => 'required|numeric|min:0',
        'unidad' => 'required|string|max:50',
        'tiempo_preparacion_min' => 'nullable|integer|min:0',
        'instrucciones' => 'nullable|string',
        'ingredients' => 'nullable|array',
        'ingredients.*.product_id' => 'required|exists:products,id',
        'ingredients.*.cantidad' => 'required|numeric|min:0',
        'ingredients.*.unidad' => 'required|string|max:50',
        'ingredients.*.merma_porcentaje' => 'nullable|numeric|min:0|max:100',
        'ingredients.*.costo_unitario' => 'nullable|numeric|min:0',
    ]);

    // 2. Asignar company_id y costo inicial 0
    $validated['company_id'] = $this->companyId();
    $validated['costo_estimado'] = 0;

    // 3. Crear la receta
    $recipe = Recipe::create($validated);

    // 4. Crear ingredientes y calcular costo total
    $costoTotal = 0;
    if ($request->has('ingredients')) {
        foreach ($request->ingredients as $ing) {
            // costo = costo_unitario × cantidad (ej: S/5.00/KG × 2.5KG = S/12.50)
            $costo = ($ing['costo_unitario'] ?? 0) * $ing['cantidad'];
            $costoTotal += $costo;

            RecipeIngredient::create([
                'recipe_id' => $recipe->id,
                'product_id' => $ing['product_id'],
                'cantidad' => $ing['cantidad'],
                'unidad' => $ing['unidad'],
                'merma_porcentaje' => $ing['merma_porcentaje'] ?? 0,
                'costo_unitario' => $ing['costo_unitario'] ?? 0,
            ]);
        }
    }

    // 5. Actualizar costo_estimado con el total calculado
    $recipe->update(['costo_estimado' => $costoTotal]);

    return redirect()->route('recipes.index')
        ->with('success', 'Receta creada exitosamente.');
}
```

#### 15.2.5 Actualizar Receta — Flujo

```
PUT /recipes/{id}
   → RecipeController::update($request, $recipe)
```

```php
// RecipeController::update() — Línea 93-134
public function update(Request $request, Recipe $recipe)
{
    // 1. Validar (mismas reglas que store)
    $validated = $request->validate([...]);

    // 2. Actualizar datos de la receta
    $recipe->update($validated);

    // 3. ELIMINAR TODOS los ingredientes anteriores
    $recipe->ingredients()->delete();

    // 4. RECREAR los ingredientes desde el formulario
    $costoTotal = 0;
    if ($request->has('ingredients')) {
        foreach ($request->ingredients as $ing) {
            $costo = ($ing['costo_unitario'] ?? 0) * $ing['cantidad'];
            $costoTotal += $costo;
            RecipeIngredient::create([...]);
        }
    }

    // 5. Actualizar costo estimado
    $recipe->update(['costo_estimado' => $costoTotal]);

    return redirect()->route('recipes.index')
        ->with('success', 'Receta actualizada exitosamente.');
}
```

#### 15.2.6 Eliminar Receta

```php
// RecipeController::destroy() — Línea 136-143
public function destroy(Recipe $recipe)
{
    // 1. Eliminar ingredientes primero (integridad referencial)
    $recipe->ingredients()->delete();

    // 2. Eliminar la receta
    $recipe->delete();

    return redirect()->route('recipes.index')
        ->with('success', 'Receta eliminada exitosamente.');
}
```

**Archivos involucrados:**
- `app/Models/Recipe.php`
- `app/Models/RecipeIngredient.php`
- `app/Http/Controllers/RecipeController.php`
- `database/migrations/2026_07_16_000001_create_recipes_table.php`
- `database/migrations/2026_07_16_000002_create_recipe_ingredients_table.php`
- `resources/views/recipes/{index,create,edit,show}.blade.php`

---

### 15.3 Módulo de Órdenes de Producción (Production Orders)

#### 15.3.1 Modelo `ProductionOrder`

```php
// app/Models/ProductionOrder.php
protected $fillable = [
    'company_id', 'recipe_id', 'product_id', 'user_id',
    'fecha_produccion', 'cantidad_planificada', 'cantidad_producida',
    'unidad', 'estado', 'costo_total', 'notas',
];

protected $casts = [
    'fecha_produccion' => 'date',
    'cantidad_planificada' => 'decimal:4',
    'cantidad_producida' => 'decimal:4',
    'costo_total' => 'decimal:4',
];

// Estados posibles: planificado | en_proceso | completado | cancelado

public function recipe()   // belongsTo Recipe (nullable)
public function product()  // belongsTo Product (nullable — si no usa receta)
public function user()     // belongsTo User
```

#### 15.3.2 Esquema de Base de Datos

```sql
-- Tabla: production_orders (migración 2026_07_16_000003)
CREATE TABLE production_orders (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id BIGINT UNSIGNED NOT NULL,
    recipe_id BIGINT UNSIGNED NULL,
    product_id BIGINT UNSIGNED NULL,
    user_id BIGINT UNSIGNED NULL,
    fecha_produccion DATE NOT NULL,
    cantidad_planificada DECIMAL(10,4) NOT NULL,
    cantidad_producida DECIMAL(10,4) DEFAULT 0,
    unidad VARCHAR(50) DEFAULT 'UNIDAD',
    estado ENUM('planificado','en_proceso','completado','cancelado') DEFAULT 'planificado',
    costo_total DECIMAL(12,4) DEFAULT 0,
    notas TEXT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
    FOREIGN KEY (recipe_id) REFERENCES recipes(id) ON DELETE SET NULL,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);
```

#### 15.3.3 Crear Orden de Producción

```
1. GET /production-orders/create
   → ProductionOrderController::create()
   → Carga recetas activas + productos del inventario
   → Retorna vista con dos selects: receta (opcional) o producto directo (opcional)

2. POST /production-orders/store
   → ProductionOrderController::store()
```

```php
// ProductionOrderController::store() — Línea 40-68
public function store(Request $request)
{
    $validated = $request->validate([
        'recipe_id' => 'nullable|exists:recipes,id',
        'product_id' => 'nullable|exists:products,id',
        'fecha_produccion' => 'required|date',
        'cantidad_planificada' => 'required|numeric|min:0',
        'unidad' => 'required|string|max:50',
        'notas' => 'nullable|string',
    ]);

    $validated['company_id'] = $this->companyId();
    $validated['user_id'] = Auth::id();
    $validated['cantidad_producida'] = 0;
    $validated['estado'] = 'planificado';
    $validated['costo_total'] = 0;

    // Si usa receta, calcular costo estimado
    if ($request->filled('recipe_id')) {
        $recipe = Recipe::find($request->recipe_id);
        if ($recipe) {
            // costo_total = costo_estimado de la receta × cantidad planificada
            $validated['costo_total'] = $recipe->costo_estimado * $request->cantidad_planificada;
        }
    }

    ProductionOrder::create($validated);

    return redirect()->route('production-orders.index')
        ->with('success', 'Orden de producción creada exitosamente.');
}
```

#### 15.3.4 Iniciar Producción — Descuento de Ingredientes

```
POST /production-orders/{id}/start
   → ProductionOrderController::start()
```

```php
// ProductionOrderController::start() — Línea 101-119
public function start(ProductionOrder $productionOrder)
{
    // 1. Cambiar estado
    $productionOrder->update(['estado' => 'en_proceso']);

    // 2. Si la orden usa una receta, descontar ingredientes del inventario
    if ($productionOrder->recipe) {
        foreach ($productionOrder->recipe->ingredients as $ingredient) {
            $product = $ingredient->product;
            if ($product && $product->stock) {
                // Ejemplo: Receta de 10 panes usa 2.5KG de harina
                // Si se planifica producir 100 panes (10×):
                //   cantidadNecesaria = 2.5 × 10 = 25KG
                $cantidadNecesaria = $ingredient->cantidad * $productionOrder->cantidad_planificada;

                //   merma = 25 × (5% / 100) = 1.25KG
                $merma = $cantidadNecesaria * ($ingredient->merma_porcentaje / 100);

                //   totalDescontar = 25 + 1.25 = 26.25KG
                $totalDescontar = $cantidadNecesaria + $merma;

                // Decrementa el stock
                $product->decrement('stock', $totalDescontar);
                // SQL: UPDATE products SET stock = stock - 26.25 WHERE id = ?
            }
        }
    }

    return redirect()->route('production-orders.index')
        ->with('success', 'Producción iniciada. Ingredientes descontados del inventario.');
}
```

#### 15.3.5 Completar Producción — Ingreso de Producto Terminado

```
POST /production-orders/{id}/complete
   → ProductionOrderController::complete()
   → El frontend muestra un modal para ingresar la cantidad real producida
```

```php
// ProductionOrderController::complete() — Línea 121-139
public function complete(Request $request, ProductionOrder $productionOrder)
{
    // 1. Validar cantidad real producida
    $request->validate([
        'cantidad_producida' => 'required|numeric|min:0',
    ]);

    // 2. Actualizar orden
    $productionOrder->update([
        'estado' => 'completado',
        'cantidad_producida' => $request->cantidad_producida,
    ]);

    // 3. Si la orden tiene un producto asociado, incrementar stock
    if ($productionOrder->product_id && $request->cantidad_producida > 0) {
        Product::where('id', $productionOrder->product_id)
            ->increment('stock', $request->cantidad_producida);
        // SQL: UPDATE products SET stock = stock + 100 WHERE id = ?
    }

    return redirect()->route('production-orders.index')
        ->with('success', 'Producción completada. Stock actualizado.');
}
```

#### 15.3.6 Cancelar Orden

```php
// ProductionOrderController::cancel() — Línea 141-147
public function cancel(ProductionOrder $productionOrder)
{
    // Simplemente cambia el estado — NO restaura ingredientes
    $productionOrder->update(['estado' => 'cancelado']);

    return redirect()->route('production-orders.index')
        ->with('success', 'Orden de producción cancelada.');
}
```

**Flujo completo de una orden de producción:**

```
┌──────────────────────────────────────────────────────────────┐
│ 1. CREAR ORDEN (estado: planificado)                         │
│    - Seleccionar receta o producto directo                   │
│    - Fecha, cantidad planificada, notas                      │
│    - Si tiene receta: costo_total = receta.costo × cantidad  │
└───────────────────────┬──────────────────────────────────────┘
                        │ POST /production-orders/{id}/start
┌───────────────────────▼──────────────────────────────────────┐
│ 2. INICIAR (estado: en_proceso)                              │
│    - DESCUENTA ingredientes del inventario                   │
│    - Por cada ingrediente:                                   │
│      stock -= (cantidad × cant_planificada) + merma%       │
└───────────────────────┬──────────────────────────────────────┘
                        │ POST /production-orders/{id}/complete
┌───────────────────────▼──────────────────────────────────────┐
│ 3. COMPLETAR (estado: completado)                            │
│    - Ingresar cantidad real producida                        │
│    - INCREMENTA stock del producto terminado                 │
│      stock += cantidad_producida                             │
└──────────────────────────────────────────────────────────────┘
```

**Archivos involucrados:**
- `app/Models/ProductionOrder.php`
- `app/Http/Controllers/ProductionOrderController.php`
- `database/migrations/2026_07_16_000003_create_production_orders_table.php`
- `resources/views/production-orders/{index,create,edit,show}.blade.php`

---

### 15.4 Módulo de Mermas (Waste)

#### 15.4.1 Modelo `WasteRecord`

```php
// app/Models/WasteRecord.php
protected $fillable = [
    'company_id', 'product_id', 'user_id', 'fecha',
    'cantidad', 'unidad', 'motivo', 'costo_perdida', 'notas',
];

protected $casts = [
    'fecha' => 'date',
    'cantidad' => 'decimal:4',
    'costo_perdida' => 'decimal:4',
];

// Motivos: vencido | danado | devolucion | no_vendido | produccion | otro

public function product()  // belongsTo Product
public function user()     // belongsTo User
```

#### 15.4.2 Esquema

```sql
-- Tabla: waste_records (migración 2026_07_16_000004)
CREATE TABLE waste_records (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id BIGINT UNSIGNED NOT NULL,
    product_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NULL,
    fecha DATE NOT NULL,
    cantidad DECIMAL(10,4) NOT NULL,
    unidad VARCHAR(50) DEFAULT 'UNIDAD',
    motivo ENUM('vencido','danado','devolucion','no_vendido','produccion','otro') DEFAULT 'danado',
    costo_perdida DECIMAL(12,4) DEFAULT 0,
    notas TEXT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);
```

#### 15.4.3 Registrar Merma

```php
// WasteRecordController::store() — Línea 35-59
public function store(Request $request)
{
    // 1. Validación
    $validated = $request->validate([
        'product_id' => 'required|exists:products,id',
        'fecha' => 'required|date',
        'cantidad' => 'required|numeric|min:0.0001',
        'unidad' => 'required|string|max:50',
        'motivo' => 'required|in:vencido,danado,devolucion,no_vendido,produccion,otro',
        'notas' => 'nullable|string',
    ]);

    $validated['company_id'] = $this->companyId();
    $validated['user_id'] = Auth::id();

    // 2. Calcular costo de pérdida
    $product = Product::find($request->product_id);
    $costoUnitario = $product ? $product->precio_compra : 0;
    // costo_perdida = precio_compra × cantidad (ej: S/5.00 × 3 = S/15.00)
    $validated['costo_perdida'] = $costoUnitario * $request->cantidad;

    // 3. Crear registro
    $waste = WasteRecord::create($validated);

    // 4. DESCONTAR del inventario
    $product->decrement('stock', $request->cantidad);
    // SQL: UPDATE products SET stock = stock - 3 WHERE id = ?

    return redirect()->route('waste.index')
        ->with('success', 'Merma registrada exitosamente.');
}
```

#### 15.4.4 Eliminar Merma — Restaurar Stock

```php
// WasteRecordController::destroy() — Línea 91-102
public function destroy(WasteRecord $waste)
{
    // 1. RESTAURAR el stock que se había descontado
    $product = Product::find($waste->product_id);
    if ($product) {
        $product->increment('stock', $waste->cantidad);
        // SQL: UPDATE products SET stock = stock + 3 WHERE id = ?
    }

    // 2. Eliminar el registro
    $waste->delete();

    return redirect()->route('waste.index')
        ->with('success', 'Registro de merma eliminado. Stock restaurado.');
}
```

**Archivos involucrados:**
- `app/Models/WasteRecord.php`
- `app/Http/Controllers/WasteRecordController.php`
- `database/migrations/2026_07_16_000004_create_waste_records_table.php`
- `resources/views/waste/{index,create,edit,show}.blade.php`

---

### 15.5 Módulo de Pedidos Programados (Scheduled Orders)

#### 15.5.1 Modelo `ScheduledOrder`

```php
// app/Models/ScheduledOrder.php
protected $fillable = [
    'company_id', 'customer_id', 'user_id', 'order_number',
    'fecha_pedido', 'fecha_entrega', 'hora_entrega',
    'estado', 'subtotal', 'igv', 'total', 'anticipo', 'saldo',
    'notas', 'descripcion', 'telefono_contacto', 'para_delivery',
];

protected $casts = [
    'fecha_pedido' => 'date',
    'fecha_entrega' => 'date',
    'subtotal' => 'decimal:2',
    'igv' => 'decimal:2',
    'total' => 'decimal:2',
    'anticipo' => 'decimal:2',
    'saldo' => 'decimal:2',
    'para_delivery' => 'boolean',
];

// Estados: pendiente | confirmado | en_produccion | listo | entregado | cancelado

public function items()    // hasMany ScheduledOrderItem
public function customer() // belongsTo Customer
public function user()     // belongsTo User
```

#### 15.5.2 Modelo `ScheduledOrderItem`

```php
// app/Models/ScheduledOrderItem.php
protected $fillable = [
    'scheduled_order_id', 'product_id', 'descripcion_personalizada',
    'cantidad', 'precio_unitario', 'subtotal', 'notas',
];

protected $casts = [
    'cantidad' => 'decimal:4',
    'precio_unitario' => 'decimal:4',
    'subtotal' => 'decimal:2',
];

public function scheduledOrder() // belongsTo ScheduledOrder
public function product()        // belongsTo Product
```

#### 15.5.3 Esquema

```sql
-- Tabla: scheduled_orders (migración 2026_07_16_000008)
CREATE TABLE scheduled_orders (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id BIGINT UNSIGNED NOT NULL,
    customer_id BIGINT UNSIGNED NULL,
    user_id BIGINT UNSIGNED NULL,
    order_number VARCHAR(255) UNIQUE NOT NULL,
    fecha_pedido DATE NOT NULL,
    fecha_entrega DATE NOT NULL,
    hora_entrega TIME NULL,
    estado ENUM('pendiente','confirmado','en_produccion','listo','entregado','cancelado')
        DEFAULT 'pendiente',
    subtotal DECIMAL(12,2) DEFAULT 0,
    igv DECIMAL(12,2) DEFAULT 0,
    total DECIMAL(12,2) DEFAULT 0,
    anticipo DECIMAL(12,2) DEFAULT 0,
    saldo DECIMAL(12,2) DEFAULT 0,
    notas TEXT NULL,
    descripcion TEXT NULL,
    telefono_contacto VARCHAR(20) NULL,
    para_delivery TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE SET NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Tabla: scheduled_order_items
CREATE TABLE scheduled_order_items (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    scheduled_order_id BIGINT UNSIGNED NOT NULL,
    product_id BIGINT UNSIGNED NULL,
    descripcion_personalizada VARCHAR(255) NULL,
    cantidad DECIMAL(10,4) NOT NULL,
    precio_unitario DECIMAL(12,4) NOT NULL,
    subtotal DECIMAL(12,2) NOT NULL,
    notas TEXT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (scheduled_order_id) REFERENCES scheduled_orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL
);
```

#### 15.5.4 Generación de Número de Pedido

```php
// ScheduledOrderController::generateOrderNumber() — Línea 19-26
protected function generateOrderNumber()
{
    $prefix = 'P-';
    // Busca el último pedido creado por ID
    $lastOrder = ScheduledOrder::where('company_id', $this->companyId())
        ->orderBy('id', 'desc')->first();

    // Extrae el número y suma 1
    $next = $lastOrder ? intval(substr($lastOrder->order_number, 2)) + 1 : 1;

    // Formato: P-000001, P-000002, ..., P-999999
    return $prefix . str_pad($next, 6, '0', STR_PAD_LEFT);
}
```

#### 15.5.5 Crear Pedido Programado — Flujo Completo

```php
// ScheduledOrderController::store() — Línea 47-111
public function store(Request $request)
{
    // 1. Validación de datos del pedido e items
    $validated = $request->validate([
        'customer_id' => 'required|exists:customers,id',
        'fecha_pedido' => 'required|date',
        'fecha_entrega' => 'required|date',
        'hora_entrega' => 'nullable',
        'descripcion' => 'nullable|string',
        'notas' => 'nullable|string',
        'telefono_contacto' => 'nullable|string|max:20',
        'anticipo' => 'nullable|numeric|min:0',
        'para_delivery' => 'boolean',
        'items' => 'required|array|min:1',
        'items.*.product_id' => 'nullable|exists:products,id',
        'items.*.descripcion_personalizada' => 'nullable|string',
        'items.*.cantidad' => 'required|numeric|min:0.0001',
        'items.*.precio_unitario' => 'required|numeric|min:0',
    ]);

    // 2. Calcular subtotal sumando todos los items
    $subtotal = 0;
    foreach ($request->items as $item) {
        $subtotal += $item['cantidad'] * $item['precio_unitario'];
    }

    // 3. Calcular IGV y totales
    $igvPercent = 0.18;
    // IGV = subtotal × 0.18 / 1.18  (extración del IGV)
    $igv = $subtotal * $igvPercent / (1 + $igvPercent);
    $total = $subtotal;        // El precio ya incluye IGV
    $anticipo = $request->anticipo ?? 0;
    $saldo = $total - $anticipo;

    // 4. Crear el pedido
    $order = ScheduledOrder::create([
        'company_id' => $this->companyId(),
        'customer_id' => $request->customer_id,
        'user_id' => Auth::id(),
        'order_number' => $this->generateOrderNumber(),   // P-000001
        'fecha_pedido' => $request->fecha_pedido,
        'fecha_entrega' => $request->fecha_entrega,
        'hora_entrega' => $request->hora_entrega,
        'estado' => 'pendiente',
        'subtotal' => $subtotal,
        'igv' => $igv,
        'total' => $total,
        'anticipo' => $anticipo,
        'saldo' => $saldo,
        'descripcion' => $request->descripcion,
        'notas' => $request->notas,
        'telefono_contacto' => $request->telefono_contacto,
        'para_delivery' => $request->para_delivery ?? false,
    ]);

    // 5. Crear items del pedido
    foreach ($request->items as $item) {
        ScheduledOrderItem::create([
            'scheduled_order_id' => $order->id,
            'product_id' => $item['product_id'] ?? null,
            'descripcion_personalizada' => $item['descripcion_personalizada'] ?? null,
            'cantidad' => $item['cantidad'],
            'precio_unitario' => $item['precio_unitario'],
            'subtotal' => $item['cantidad'] * $item['precio_unitario'],
            'notas' => $item['notas'] ?? null,
        ]);
    }

    return redirect()->route('scheduled-orders.index')
        ->with('success', 'Pedido programado creado exitosamente.');
}
```

#### 15.5.6 Ciclo de Vida del Pedido — Transiciones de Estado

Cada transición de estado es un método independiente en el controlador:

```php
// Estado: pendiente → confirmado
// POST /scheduled-orders/{id}/confirm
public function confirm(ScheduledOrder $scheduledOrder)
{
    $scheduledOrder->update(['estado' => 'confirmado']);
    return redirect()->route('scheduled-orders.index')
        ->with('success', 'Pedido confirmado.');
}

// Estado: confirmado → en_produccion
// POST /scheduled-orders/{id}/start-production
public function startProduction(ScheduledOrder $scheduledOrder)
{
    $scheduledOrder->update(['estado' => 'en_produccion']);
    return redirect()->route('scheduled-orders.index')
        ->with('success', 'Pedido en producción.');
}

// Estado: en_produccion → listo
// POST /scheduled-orders/{id}/mark-ready
public function markReady(ScheduledOrder $scheduledOrder)
{
    $scheduledOrder->update(['estado' => 'listo']);
    return redirect()->route('scheduled-orders.index')
        ->with('success', 'Pedido listo para entrega.');
}

// Estado: listo → entregado
// POST /scheduled-orders/{id}/deliver
public function deliver(ScheduledOrder $scheduledOrder)
{
    $scheduledOrder->update(['estado' => 'entregado']);
    return redirect()->route('scheduled-orders.index')
        ->with('success', 'Pedido entregado.');
}

// Cualquier estado → cancelado
// POST /scheduled-orders/{id}/cancel
public function cancel(ScheduledOrder $scheduledOrder)
{
    $scheduledOrder->update(['estado' => 'cancelado']);
    return redirect()->route('scheduled-orders.index')
        ->with('success', 'Pedido cancelado.');
}
```

**Diagrama de estados:**

```
                    ┌─────────────┐
                    │  pendiente  │ ← Cliente hace encargo
                    └──────┬──────┘
                           │ confirm()
                    ┌──────▼──────┐
                    │ confirmado  │ ← Se confirma disponibilidad
                    └──────┬──────┘
                           │ startProduction()
                    ┌──────▼──────┐
                    │en_produccion│ ← Panadero elabora
                    └──────┬──────┘
                           │ markReady()
                    ┌──────▼──────┐
                    │   listo     │ ← Producto terminado
                    └──────┬──────┘
                           │ deliver()
                    ┌──────▼──────┐
                    │ entregado   │ ← Cliente recibe
                    └─────────────┘

         En cualquier punto: cancel() → cancelado
```

#### 15.5.7 Impresión de Comanda

```
GET /scheduled-orders/{id}/print-comanda
   → ScheduledOrderController::printComanda()
```

```php
// ScheduledOrderController::printComanda() — Línea 196-202
public function printComanda(ScheduledOrder $scheduledOrder)
{
    $scheduledOrder->load('items.product', 'customer');

    // Por ahora retorna a la vista de detalle con mensaje
    // La integración con PrintService está disponible para usarse:
    // $printService = app(\App\Services\PrintService::class);
    // $printService->printScheduledOrderComanda($scheduledOrder);

    return redirect()->route('scheduled-orders.show', $scheduledOrder)
        ->with('success', 'Comanda enviada a impresión.');
}
```

**Servicio de impresión de comanda:**

```php
// PrintService::printScheduledOrderComanda() — Línea 22-32
public function printScheduledOrderComanda($order): void
{
    // 1. Buscar impresora configurada como "caja"
    $printer = $this->getPrinter('caja');
    if (!$printer) {
        \Log::warning('No hay impresora configurada para comandas');
        return;
    }

    // 2. Generar texto ESC/POS
    $text = PlainTextTicket::scheduledOrderComanda($order);

    // 3. Encolar trabajo de impresión
    $this->queuePrint($printer, $text, 'comanda', get_class($order), $order->id);

    // 4. Procesar cola (enviar al Print Server inmediatamente)
    $this->processQueue();
}
```

**Generación del ticket ESC/POS:**

```php
// PlainTextTicket::scheduledOrderComanda() — Línea 290-325
public static function scheduledOrderComanda($order): string
{
    $t = new self('escpos');

    // Cabecera
    $t->center('*** COMANDA ***', '*');
    $t->blank();
    $t->text('Pedido: ' . $order->order_number);
    $t->text('Cliente: ' . ($order->customer->nombre ?? 'N/A'));
    $t->text('Telefono: ' . ($order->telefono_contacto ?? 'N/A'));
    $t->text('Fecha Entrega: ' . $order->fecha_entrega->format('d/m/Y'));
    if ($order->hora_entrega) $t->text('Hora: ' . $order->hora_entrega);
    $t->separator();

    // Items del pedido
    if ($order->items) {
        foreach ($order->items as $item) {
            $desc = $item->product
                ? $item->product->descripcion
                : ($item->descripcion_personalizada ?? 'Sin descripcion');
            $t->text($item->cantidad . ' x ' . $desc);
            if ($item->notas) $t->text('  Nota: ' . $item->notas);
            $t->separator('-');
        }
    }

    // Totales
    $t->blank();
    $t->twoColumns('TOTAL:', 'S/ ' . number_format($order->total, 2));
    if ($order->anticipo > 0) {
        $t->twoColumns('Anticipo:', 'S/ ' . number_format($order->anticipo, 2));
        $t->twoColumns('Saldo:', 'S/ ' . number_format($order->saldo, 2));
    }

    // Notas
    $t->blank();
    if ($order->descripcion) $t->text('Descripcion: ' . $order->descripcion);
    if ($order->notas) $t->text('Notas: ' . $order->notas);
    $t->blank();
    $t->text('Fecha: ' . now()->format('d/m/Y H:i'));

    return $t->getEscPos();
}
```

**Formato del ticket impreso:**

```
         *** COMANDA ***
                                   
    Pedido: P-000001
    Cliente: María García
    Telefono: 987654321
    Fecha Entrega: 25/07/2026
    Hora: 15:00
    ---------------------------------
    2 x Torta de Chocolate
      Nota: Sin lactosa
    ---------------------------------
    1 x Pan Integral x10 Und
    ---------------------------------
    3 x Empanadas de Pollo
    ---------------------------------                       
    TOTAL:          S/ 145.00
    Anticipo:       S/  50.00
    Saldo:          S/  95.00
                                   
    Descripcion: Torta con nombre "Feliz Cumpleaños Ana"
    Notas: Entregar antes de las 3pm
                                   
    Fecha: 15/07/2026 10:30
```

**Formato del comprobante en el que se imprime la comanda:**
```
         *** COMANDA ***
                                   
    Pedido: P-000001
    Cliente: María García
    Telefono: 987654321
    Fecha Entrega: 25/07/2026
    Hora: 15:00
    ---------------------------------
    2 x Torta de Chocolate
      Nota: Sin lactosa
    ---------------------------------
    1 x Pan Integral x10 Und
    ---------------------------------
    3 x Empanadas de Pollo
    ---------------------------------                       
    TOTAL:          S/ 145.00
    Anticipo:       S/  50.00
    Saldo:          S/  95.00
                                   
    Descripcion: Torta con nombre "Feliz Cumpleaños Ana"
    Notas: Entregar antes de las 3pm
                                   
    Fecha: 15/07/2026 10:30
```

**Archivos involucrados:**
- `app/Models/ScheduledOrder.php`
- `app/Models/ScheduledOrderItem.php`
- `app/Http/Controllers/ScheduledOrderController.php`
- `app/Services/PrintService.php` (método `printScheduledOrderComanda`)
- `app/Services/PlainTextTicket.php` (método `scheduledOrderComanda`)
- `database/migrations/2026_07_16_000008_create_scheduled_orders_table.php`
- `resources/views/scheduled-orders/{index,create,edit,show}.blade.php`

---

### 15.6 Módulo de Reparto (Delivery)

#### 15.6.1 Modelos

```php
// DeliveryZone — app/Models/DeliveryZone.php
protected $fillable = ['company_id', 'nombre', 'precio_envio', 'tiempo_estimado_min', 'activa'];
protected $casts = ['precio_envio' => 'decimal:2', 'activa' => 'boolean'];
public function deliveries() // hasMany Delivery

// DeliveryPerson — app/Models/DeliveryPerson.php
protected $fillable = ['company_id', 'nombre', 'telefono', 'vehiculo', 'activo'];
protected $casts = ['activo' => 'boolean'];
public function deliveries() // hasMany Delivery

// Delivery — app/Models/Delivery.php
protected $fillable = [
    'company_id', 'invoice_id', 'delivery_zone_id', 'delivery_person_id',
    'direccion', 'referencia', 'telefono_contacto', 'costo_envio',
    'estado', 'fecha_entrega', 'notas',
];
protected $casts = ['costo_envio' => 'decimal:2', 'fecha_entrega' => 'datetime'];
// Estados: pendiente | en_ruta | entregado | cancelado
public function invoice()        // belongsTo Invoice
public function deliveryZone()   // belongsTo DeliveryZone
public function deliveryPerson() // belongsTo DeliveryPerson
```

#### 15.6.2 Esquemas SQL

```sql
-- delivery_zones (migración 2026_07_16_000005)
CREATE TABLE delivery_zones (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id BIGINT UNSIGNED NOT NULL,
    nombre VARCHAR(255) NOT NULL,
    precio_envio DECIMAL(10,2) DEFAULT 0,
    tiempo_estimado_min INT NULL,
    activa TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP NULL, updated_at TIMESTAMP NULL,
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE
);

-- delivery_persons (migración 2026_07_16_000006)
CREATE TABLE delivery_persons (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id BIGINT UNSIGNED NOT NULL,
    nombre VARCHAR(255) NOT NULL,
    telefono VARCHAR(20) NULL,
    vehiculo VARCHAR(100) NULL,
    activo TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP NULL, updated_at TIMESTAMP NULL,
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE
);

-- deliveries (migración 2026_07_16_000007)
CREATE TABLE deliveries (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id BIGINT UNSIGNED NOT NULL,
    invoice_id BIGINT UNSIGNED NULL,
    delivery_zone_id BIGINT UNSIGNED NULL,
    delivery_person_id BIGINT UNSIGNED NULL,
    direccion VARCHAR(500) NOT NULL,
    referencia VARCHAR(500) NULL,
    telefono_contacto VARCHAR(20) NULL,
    costo_envio DECIMAL(10,2) DEFAULT 0,
    estado ENUM('pendiente','en_ruta','entregado','cancelado') DEFAULT 'pendiente',
    fecha_entrega DATETIME NULL,
    notas TEXT NULL,
    created_at TIMESTAMP NULL, updated_at TIMESTAMP NULL,
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
    FOREIGN KEY (invoice_id) REFERENCES invoices(id) ON DELETE CASCADE,
    FOREIGN KEY (delivery_zone_id) REFERENCES delivery_zones(id) ON DELETE SET NULL,
    FOREIGN KEY (delivery_person_id) REFERENCES delivery_persons(id) ON DELETE SET NULL
);
```

#### 15.6.3 Crear Reparto

```php
// DeliveryController::store() — Línea 43-63
public function store(Request $request)
{
    $validated = $request->validate([
        'invoice_id' => 'nullable|exists:invoices,id',
        'delivery_zone_id' => 'nullable|exists:delivery_zones,id',
        'delivery_person_id' => 'nullable|exists:delivery_persons,id',
        'direccion' => 'required|string|max:500',
        'referencia' => 'nullable|string|max:500',
        'telefono_contacto' => 'nullable|string|max:20',
        'costo_envio' => 'nullable|numeric|min:0',
        'notas' => 'nullable|string',
    ]);

    $validated['company_id'] = $this->companyId();
    $validated['estado'] = 'pendiente';  // Estado inicial

    Delivery::create($validated);

    return redirect()->route('deliveries.index')
        ->with('success', 'Reparto creado exitosamente.');
}
```

#### 15.6.4 Transiciones de Estado del Reparto

```php
// Asignar repartidor: pendiente → (sin cambio de estado, solo asigna persona)
// POST /deliveries/{id}/assign
public function assign(Request $request, Delivery $delivery)
{
    $request->validate(['delivery_person_id' => 'required|exists:delivery_persons,id']);
    $delivery->update(['delivery_person_id' => $request->delivery_person_id]);
    return redirect()->route('deliveries.index')
        ->with('success', 'Repartidor asignado.');
}

// Iniciar ruta: pendiente → en_ruta
// POST /deliveries/{id}/start
public function startRoute(Delivery $delivery)
{
    $delivery->update(['estado' => 'en_ruta']);
    return redirect()->route('deliveries.index')
        ->with('success', 'Reparto en ruta.');
}

// Completar: en_ruta → entregado
// POST /deliveries/{id}/complete
public function complete(Request $request, Delivery $delivery)
{
    $delivery->update([
        'estado' => 'entregado',
        'fecha_entrega' => now(),  // Registra fecha/hora de entrega
    ]);
    return redirect()->route('deliveries.index')
        ->with('success', 'Reparto completado.');
}

// Cancelar: cualquier estado → cancelado
// POST /deliveries/{id}/cancel
public function cancel(Delivery $delivery)
{
    $delivery->update(['estado' => 'cancelado']);
    return redirect()->route('deliveries.index')
        ->with('success', 'Reparto cancelado.');
}
```

**Archivos involucrados:**
- `app/Models/Delivery.php`, `DeliveryZone.php`, `DeliveryPerson.php`
- `app/Http/Controllers/DeliveryController.php`
- `app/Http/Controllers/DeliveryZoneController.php`
- `app/Http/Controllers/DeliveryPersonController.php`
- `database/migrations/2026_07_16_000005_create_delivery_zones_table.php`
- `database/migrations/2026_07_16_000006_create_delivery_persons_table.php`
- `database/migrations/2026_07_16_000007_create_deliveries_table.php`
- `resources/views/deliveries/{index,create,edit,show}.blade.php`
- `resources/views/delivery-zones/{index,create,edit}.blade.php`
- `resources/views/delivery-persons/{index,create,edit}.blade.php`

---

### 15.7 Cierre de Caja — Validación de Pedidos Pendientes

```
POST /cashregister/close
   → CashRegisterController::close()
```

```php
// CashRegisterController::close() — extracto (línea 81-104)
public function close(Request $request)
{
    // ... validaciones previas ...

    $companyId = $caja->company_id;

    // Verificar pedidos programados pendientes (REEMPLAZA la verificación
    // de mesas abiertas del restaurante original)
    $openOrders = ScheduledOrder::where('company_id', $companyId)
        ->whereNotIn('estado', ['entregado', 'cancelado'])
        ->count();

    if ($openOrders > 0) {
        return back()->with('error',
            "No se puede cerrar caja: {$openOrders} pedido(s) programado(s) pendientes. "
            . "Complete o cancele todos los pedidos antes de cerrar caja."
        );
    }

    // ... obtener ventas, calcular totales, cerrar caja ...
}
```

---

### 15.8 Dashboard — Controller (Extracto de KPIs Bakery)

```php
// DashboardController::index() — extracto de estadísticas de panadería
$startOfMonth = Carbon::now()->startOfMonth();
$endOfMonth = Carbon::now()->endOfMonth();

// Órdenes planificadas (pendientes de iniciar)
$stats['prod_planificadas'] = ProductionOrder::where('company_id', $companyId)
    ->where('estado', 'planificado')->count();

// Órdenes en producción actualmente
$stats['prod_en_proceso'] = ProductionOrder::where('company_id', $companyId)
    ->where('estado', 'en_proceso')->count();

// Órdenes completadas este mes
$stats['prod_completadas'] = ProductionOrder::where('company_id', $companyId)
    ->whereBetween('fecha_produccion', [$startOfMonth, $endOfMonth])
    ->where('estado', 'completado')->count();

// Pedidos programados sin entregar ni cancelar
$stats['pedidos_pendientes'] = ScheduledOrder::where('company_id', $companyId)
    ->whereNotIn('estado', ['entregado', 'cancelado'])->count();

// Total de pérdidas por mermas del mes
$stats['mermas_mes'] = WasteRecord::where('company_id', $companyId)
    ->whereBetween('fecha', [$startOfMonth, $endOfMonth])
    ->sum('costo_perdida');
```

---

### 15.9 Sistema de Impresión — Flujo Completo

```
┌─────────────────────────────────────────────────────────────┐
│ 1. APLICACIÓN (PHP)                                         │
│    PrintService::printScheduledOrderComanda($order)         │
│    ├── getPrinter('caja') → busca Printer donde             │
│    │   assigned_to='caja' y active=true                     │
│    ├── PlainTextTicket::scheduledOrderComanda($order)       │
│    │   → genera texto ESC/POS con comandos de formato       │
│    ├── queuePrint() → crea PrintJob en BD                   │
│    │   ┌──────────────────────────────────────┐             │
│    │   │ printer_name: "EPSON TM-T88V"        │             │
│    │   │ job_type: "comanda"                  │             │
│    │   │ reference_type: "App\Models\..."     │             │
│    │   │ reference_id: 1                      │             │
│    │   │ data: BASE64_ENCODED                 │             │
│    │   │ status: "pending"                    │             │
│    │   │ attempts: 0                          │             │
│    │   └──────────────────────────────────────┘             │
│    └── processQueue() → envía al Print Server               │
└────────────────────┬────────────────────────────────────────┘
                     │ HTTP POST http://localhost:9100/print
┌────────────────────▼────────────────────────────────────────┐
│ 2. PRINT SERVER (Node.js — server.js)                       │
│    POST /print { printer: "EPSON", data: "BASE64",          │
│                  mode: "escpos" }                           │
│    ├── Decodifica base64 → buffer binario                   │
│    ├── Detecta encoding (UTF-8)                             │
│    ├── Convierte a CP850 (iconv-lite)                       │
│    ├── Inserta ESC t 0x02 (seleccionar code page 850)      │
│    └── Envía a impresora:                                   │
│        ├── USB: raw-print.ps1 -printerName "EPSON"         │
│        └── Red: socket TCP → IP:9100                       │
└────────────────────┬────────────────────────────────────────┘
                     │
┌────────────────────▼────────────────────────────────────────┐
│ 3. IMPRESORA TÉRMICA                                        │
│    ┌──────────────────────────────────────────┐             │
│    │        *** COMANDA ***                    │             │
│    │                                           │             │
│    │   Pedido: P-000001                        │             │
│    │   Cliente: María García                   │             │
│    │   ...                                     │             │
│    │   TOTAL:          S/ 145.00               │             │
│    │                                           │             │
│    │   Fecha: 15/07/2026 10:30                 │             │
│    └──────────────────────────────────────────┘             │
└─────────────────────────────────────────────────────────────┘

REINTENTOS (si falla):
┌─────────────────────────────────────────────────────────────┐
│ php artisan print:process-queue (cada 1 minuto)             │
│ → PrintProcessQueue::handle()                               │
│   → Busca PrintJobs con status=pending|failed               │
│   → attempts < 3                                            │
│   → Reenvía al Print Server                                 │
│   → Éxito: status=completed                                 │
│   → Falla (3 intentos): status=failed definitivo            │
└─────────────────────────────────────────────────────────────┘
```

**Código de processQueue():**

```php
// PrintService::processQueue() — Línea 120-150
const MAX_ATTEMPTS = 3;

public function processQueue(): void
{
    // Verificar que el Print Server esté corriendo
    if (!$this->printServer->isServerRunning()) return;

    // Obtener trabajos pendientes (máximo 3 intentos)
    $jobs = PrintJob::whereIn('status', ['pending', 'failed'])
        ->where('attempts', '<', self::MAX_ATTEMPTS)
        ->orderBy('id')
        ->get();

    foreach ($jobs as $job) {
        $job->update(['status' => 'processing', 'attempts' => $job->attempts + 1]);

        try {
            $payload = [
                'data' => $job->data,
                'mode' => 'escpos',
                'type' => $job->type
            ];

            if ($job->type === 'network') {
                $payload['ip'] = $job->printer_ip;
                $payload['port'] = $job->printer_port;
            } else {
                $payload['printer'] = $job->printer_name;
            }

            // POST a http://localhost:9100/print
            $response = \Illuminate\Support\Facades\Http::timeout(5)
                ->post(config('print-server.url', 'http://127.0.0.1:9100') . '/print', $payload);

            if ($response->successful()) {
                $job->update(['status' => 'completed', 'completed_at' => now()]);
            } else {
                $job->update(['status' => 'failed', 'error_message' => $response->body()]);
            }
        } catch (\Exception $e) {
            $job->update(['status' => 'failed', 'error_message' => $e->getMessage()]);
        }
    }
}
```

---

### 15.10 POS (Punto de Venta)

#### Flujo de Venta

```
1. GET /pos → PosController::index()
   → Verifica caja abierta (sin filtrar por usuario)
   → Renderiza vista con grid de productos, búsqueda y carrito

2. POST /pos → PosController::store()
```

```php
// PosController::store() — flujo resumido
public function store(Request $request)
{
    // 1. Verificar caja abierta
    $cajaAbierta = CashRegister::where('company_id', $companyId)
        ->where('estado', 'ABIERTA')->first();
    if (!$cajaAbierta) {
        return back()->with('error', 'Debe abrir caja primero');
    }

    // 2. Obtener datos del request (JSON)
    $items = $request->items;
    $tipoDocumento = $request->document_type;  // 01, 03, NV
    $metodoPago = $request->payment_method;
    $customerId = $request->customer_id;

    // 3. Buscar/crear serie y número correlativo
    $serie = Serie::where('company_id', $companyId)
        ->where('tipo_documento', $tipoDocumento)->first();
    $numero = $serie->getNextNumber();

    // 4. Calcular IGV dinámico según empresa
    $igvRate = $company->getIgvRate();

    // 5. Crear Invoice + InvoiceItems
    $invoice = Invoice::create([...]);
    foreach ($items as $item) {
        InvoiceItem::create([...]);
        // Descontar stock
        $product = Product::find($item['product_id']);
        $product->decrement('stock', $item['cantidad']);
    }

    // 6. Incrementar serie
    $serie->increment('numero_actual');

    // 7. Actualizar caja registradora
    $cajaAbierta->increment('total_ventas', $invoice->total);
    $cajaAbierta->increment('cantidad_ventas', 1);

    return redirect()->route('pos.success', $invoice->id);
}
```

---

## 16. Print Server Node.js (Referencia Rápida)

### Instalación en Cliente

```bash
cd print-server-node
npm install
```

### Inicio

| Método | Comando | Ventana |
|--------|---------|---------|
| Visible | `start.bat` | Sí (con autoreinicio) |
| Minimizado | `start-minimized.vbs` | Minimizada |
| Oculto | `start-hidden.vbs` | No (recomendado) |

### Endpoints

```bash
# Verificar estado
curl http://localhost:9100/status

# Listar impresoras
curl http://localhost:9100/printers

# Imprimir
curl -X POST http://localhost:9100/print \
  -H "Content-Type: application/json" \
  -d '{"printer":"EPSON","data":"BASE64","mode":"escpos"}'

# Abrir cajón
curl "http://localhost:9100/open-drawer?printer=EPSON"
```

---

## 17. Solución de Problemas Comunes

| Error | Causa | Solución |
|-------|-------|----------|
| `MissingAppKeyException` | APP_KEY inválida o cache desactualizado | `php artisan key:generate && php artisan config:clear` |
| `Column 'company_id' cannot be null` | Usuario sin empresa asignada | `php artisan db:seed` o asignar manualmente |
| `Connection refused localhost:8080` | Reverb configurado sin servidor | `BROADCAST_DRIVER=log` en `.env` |
| Print Server no responde | Quick Edit Mode de Windows | Usar `disable-quick-edit.ps1` o `start-hidden.vbs` |
| Cash drawer no abre | CORS bloqueando fetch local | Usar `mode: no-cors` |
| `Class "RestaurantOrder" not found` | Código referencia modelo eliminado | Verificar que no queden imports residuales |
| `SQLSTATE[42S02]: Table not found` | Migración no ejecutada | `php artisan migrate --force` |
| Error al iniciar producción | Receta sin ingredientes o stock insuficiente | Verificar receta e inventario |

---

## 18. Glosario

| Término | Significado |
|---------|-------------|
| ESC/POS | Lenguaje de comandos para impresoras térmicas |
| CP850 | Code Page 850 (encoding latino para impresoras) |
| SUNAT | Superintendencia Nacional de Aduanas y Administración Tributaria |
| Greenter | Librería PHP para facturación electrónica SUNAT |
| IGV | Impuesto General a las Ventas (18% o 10.5%) |
| NV | Nota de Venta (comprobante sin envío SUNAT) |
| CDR | Constancia de Recepción SUNAT |
| .p12/.pfx | Formato de certificado digital |
| SOAP | Protocolo de comunicación con SUNAT |
| MYPE | Micro y Pequeña Empresa (IGV reducido 10.5%) |
| Comanda | Ticket impreso con detalles del pedido para producción |
| Merma | Pérdida de producto por vencimiento, daño, devolución, etc. |
| Receta | Fórmula con ingredientes y cantidades para producir un producto |
| Encargo | Pedido programado de un cliente para fecha futura |

---

## 19. Estructura de Archivos del Proyecto

```
facturapanaderia/
├── app/
│   ├── Console/Commands/     # Comandos artisan (print, sunat, etc.)
│   ├── CoreFacturalo/        # Motor alternativo de facturación
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/         # Autenticación (Breeze)
│   │   │   ├── Admin/        # PrinterController
│   │   │   ├── RecipeController.php
│   │   │   ├── ProductionOrderController.php
│   │   │   ├── WasteRecordController.php
│   │   │   ├── ScheduledOrderController.php
│   │   │   ├── DeliveryController.php
│   │   │   ├── DeliveryZoneController.php
│   │   │   ├── DeliveryPersonController.php
│   │   │   ├── PosController.php
│   │   │   ├── CashRegisterController.php
│   │   │   ├── InvoiceController.php
│   │   │   └── ... (más controladores)
│   │   └── Middleware/
│   ├── Models/
│   │   ├── Recipe.php
│   │   ├── RecipeIngredient.php
│   │   ├── ProductionOrder.php
│   │   ├── WasteRecord.php
│   │   ├── ScheduledOrder.php
│   │   ├── ScheduledOrderItem.php
│   │   ├── Delivery.php
│   │   ├── DeliveryZone.php
│   │   ├── DeliveryPerson.php
│   │   └── ... (más modelos)
│   └── Services/
│       ├── PrintService.php
│       ├── PlainTextTicket.php
│       ├── GreenterService.php
│       ├── SummaryService.php
│       └── SpecialDocumentService.php
├── database/migrations/      # 40+ migraciones
├── print-server-node/        # Servidor de impresión Node.js
├── resources/views/
│   ├── recipes/              # Vistas de recetas
│   ├── production-orders/    # Vistas de producción
│   ├── waste/                # Vistas de mermas
│   ├── scheduled-orders/     # Vistas de pedidos programados
│   ├── deliveries/           # Vistas de repartos
│   ├── delivery-zones/       # Vistas de zonas
│   ├── delivery-persons/     # Vistas de repartidores
│   ├── pos/                  # Punto de venta
│   ├── invoices/             # Comprobantes
│   ├── products/             # Productos
│   ├── cashregisters/        # Caja
│   └── layouts/              # Layouts AdminLTE
├── routes/
│   ├── web.php               # Rutas principales
│   ├── api.php               # API pública
│   └── auth.php              # Rutas de autenticación
└── DOCUMENTACION_SISTEMA.md  # Este documento
```

---

## 20. Notas de Migración (de FacturaFácil a FacturaPanadería)

### Elementos Eliminados

- Módulo de restaurante (pisos, mesas, órdenes de restaurante, KDS, mozo)
- Módulo de kiosko/autopedido
- Eventos `KitchenOrderUpdated`
- Modelos: `Floor`, `RestaurantTable`, `RestaurantOrder`, `RestaurantOrderItem`

### Elementos Conservados

- Facturación electrónica SUNAT (Greenter 5.x) - **Completa**
- POS y caja registradora
- Clientes, productos, categorías
- Compras, proveedores, consumo interno
- Print Server Node.js (con comandas para pedidos programados)
- Multi-empresa, roles/permisos, series, padrón SUNAT
- Productos compuestos, inventario, reportes

### Elementos Nuevos

- Recetas con ingredientes y costos
- Órdenes de producción con workflow completo
- Registro de mermas
- Pedidos programados (6 estados, anticipo/saldo, comanda impresa)
- Sistema de reparto (zonas, repartidores, tracking)
- Dashboard con KPIs de panadería
- Rol `panadero` (reemplaza `mozo`)
