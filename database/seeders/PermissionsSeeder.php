<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class PermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            ['name' => 'Ver Dashboard', 'slug' => 'view_dashboard', 'module' => 'dashboard', 'description' => 'Ver el dashboard'],
            ['name' => 'Ver Empresas', 'slug' => 'view_companies', 'module' => 'companies', 'description' => 'Ver lista de empresas'],
            ['name' => 'Crear Empresas', 'slug' => 'create_companies', 'module' => 'companies', 'description' => 'Crear empresas'],
            ['name' => 'Editar Empresas', 'slug' => 'edit_companies', 'module' => 'companies', 'description' => 'Editar empresas'],
            ['name' => 'Eliminar Empresas', 'slug' => 'delete_companies', 'module' => 'companies', 'description' => 'Eliminar empresas'],
            ['name' => 'Ver Usuarios', 'slug' => 'view_users', 'module' => 'users', 'description' => 'Ver lista de usuarios'],
            ['name' => 'Crear Usuarios', 'slug' => 'create_users', 'module' => 'users', 'description' => 'Crear usuarios'],
            ['name' => 'Editar Usuarios', 'slug' => 'edit_users', 'module' => 'users', 'description' => 'Editar usuarios'],
            ['name' => 'Eliminar Usuarios', 'slug' => 'delete_users', 'module' => 'users', 'description' => 'Eliminar usuarios'],
            ['name' => 'Ver Clientes', 'slug' => 'view_customers', 'module' => 'customers', 'description' => 'Ver lista de clientes'],
            ['name' => 'Crear Clientes', 'slug' => 'create_customers', 'module' => 'customers', 'description' => 'Crear clientes'],
            ['name' => 'Editar Clientes', 'slug' => 'edit_customers', 'module' => 'customers', 'description' => 'Editar clientes'],
            ['name' => 'Eliminar Clientes', 'slug' => 'delete_customers', 'module' => 'customers', 'description' => 'Eliminar clientes'],
            ['name' => 'Ver Productos', 'slug' => 'view_products', 'module' => 'products', 'description' => 'Ver lista de productos'],
            ['name' => 'Crear Productos', 'slug' => 'create_products', 'module' => 'products', 'description' => 'Crear productos'],
            ['name' => 'Editar Productos', 'slug' => 'edit_products', 'module' => 'products', 'description' => 'Editar productos'],
            ['name' => 'Eliminar Productos', 'slug' => 'delete_products', 'module' => 'products', 'description' => 'Eliminar productos'],
            ['name' => 'Ver Categorías', 'slug' => 'view_categories', 'module' => 'categories', 'description' => 'Ver lista de categorías'],
            ['name' => 'Crear Categorías', 'slug' => 'create_categories', 'module' => 'categories', 'description' => 'Crear categorías'],
            ['name' => 'Editar Categorías', 'slug' => 'edit_categories', 'module' => 'categories', 'description' => 'Editar categorías'],
            ['name' => 'Eliminar Categorías', 'slug' => 'delete_categories', 'module' => 'categories', 'description' => 'Eliminar categorías'],
            ['name' => 'Ver Comprobantes', 'slug' => 'view_invoices', 'module' => 'invoices', 'description' => 'Ver lista de comprobantes'],
            ['name' => 'Crear Comprobantes', 'slug' => 'create_invoices', 'module' => 'invoices', 'description' => 'Crear comprobantes'],
            ['name' => 'Enviar SUNAT', 'slug' => 'send_sunat', 'module' => 'invoices', 'description' => 'Enviar comprobantes a SUNAT'],
            ['name' => 'Ver Compras', 'slug' => 'view_purchases', 'module' => 'purchases', 'description' => 'Ver lista de compras'],
            ['name' => 'Crear Compras', 'slug' => 'create_purchases', 'module' => 'purchases', 'description' => 'Crear compras'],
            ['name' => 'Ver Proveedores', 'slug' => 'view_suppliers', 'module' => 'suppliers', 'description' => 'Ver lista de proveedores'],
            ['name' => 'Crear Proveedores', 'slug' => 'create_suppliers', 'module' => 'suppliers', 'description' => 'Crear proveedores'],
            ['name' => 'Ver Caja', 'slug' => 'view_cashregisters', 'module' => 'cashregisters', 'description' => 'Ver caja'],
            ['name' => 'Abrir Caja', 'slug' => 'open_cashregister', 'module' => 'cashregisters', 'description' => 'Abrir caja'],
            ['name' => 'Cerrar Caja', 'slug' => 'close_cashregister', 'module' => 'cashregisters', 'description' => 'Cerrar caja'],
            ['name' => 'Ver POS', 'slug' => 'view_pos', 'module' => 'pos', 'description' => 'Ver punto de venta'],
            ['name' => 'Usar POS', 'slug' => 'use_pos', 'module' => 'pos', 'description' => 'Usar punto de venta'],
            ['name' => 'Ver Roles', 'slug' => 'view_roles', 'module' => 'users', 'description' => 'Ver roles'],
            ['name' => 'Crear Roles', 'slug' => 'create_roles', 'module' => 'users', 'description' => 'Crear roles'],
            ['name' => 'Editar Roles', 'slug' => 'edit_roles', 'module' => 'users', 'description' => 'Editar roles'],
            ['name' => 'Ver Permisos', 'slug' => 'view_permissions', 'module' => 'users', 'description' => 'Ver permisos'],
            ['name' => 'Crear Permisos', 'slug' => 'create_permissions', 'module' => 'users', 'description' => 'Crear permisos'],
            ['name' => 'Ver Series', 'slug' => 'view_series', 'module' => 'series', 'description' => 'Ver series'],
            ['name' => 'Ver Recetas', 'slug' => 'view_recipes', 'module' => 'produccion', 'description' => 'Ver recetas y fórmulas'],
            ['name' => 'Crear Recetas', 'slug' => 'create_recipes', 'module' => 'produccion', 'description' => 'Crear recetas'],
            ['name' => 'Editar Recetas', 'slug' => 'edit_recipes', 'module' => 'produccion', 'description' => 'Editar recetas'],
            ['name' => 'Eliminar Recetas', 'slug' => 'delete_recipes', 'module' => 'produccion', 'description' => 'Eliminar recetas'],
            ['name' => 'Ver Órdenes Producción', 'slug' => 'view_production_orders', 'module' => 'produccion', 'description' => 'Ver órdenes de producción'],
            ['name' => 'Crear Órdenes Producción', 'slug' => 'create_production_orders', 'module' => 'produccion', 'description' => 'Crear órdenes de producción'],
            ['name' => 'Gestionar Producción', 'slug' => 'manage_production', 'module' => 'produccion', 'description' => 'Iniciar/completar/cancelar producción'],
            ['name' => 'Ver Mermas', 'slug' => 'view_waste', 'module' => 'produccion', 'description' => 'Ver registros de mermas'],
            ['name' => 'Crear Mermas', 'slug' => 'create_waste', 'module' => 'produccion', 'description' => 'Registrar mermas'],
            ['name' => 'Ver Pedidos Programados', 'slug' => 'view_scheduled_orders', 'module' => 'pedidos', 'description' => 'Ver pedidos programados'],
            ['name' => 'Crear Pedidos Programados', 'slug' => 'create_scheduled_orders', 'module' => 'pedidos', 'description' => 'Crear pedidos programados'],
            ['name' => 'Gestionar Pedidos', 'slug' => 'manage_scheduled_orders', 'module' => 'pedidos', 'description' => 'Gestionar pedidos programados'],
            ['name' => 'Ver Repartos', 'slug' => 'view_deliveries', 'module' => 'reparto', 'description' => 'Ver repartos y entregas'],
            ['name' => 'Crear Repartos', 'slug' => 'create_deliveries', 'module' => 'reparto', 'description' => 'Crear repartos'],
            ['name' => 'Gestionar Repartos', 'slug' => 'manage_deliveries', 'module' => 'reparto', 'description' => 'Asignar/gestionar repartos'],
            ['name' => 'Ver Zonas Reparto', 'slug' => 'view_delivery_zones', 'module' => 'reparto', 'description' => 'Ver zonas de reparto'],
            ['name' => 'Ver Repartidores', 'slug' => 'view_delivery_persons', 'module' => 'reparto', 'description' => 'Ver repartidores'],
            ['name' => 'Ver Elementos Auxiliares', 'slug' => 'view_auxiliary_items', 'module' => 'productos', 'description' => 'Ver elementos auxiliares'],
            ['name' => 'Ver Impresoras', 'slug' => 'view_printers', 'module' => 'printers', 'description' => 'Ver configuración de impresoras'],
            ['name' => 'Ver Cola Impresión', 'slug' => 'view_print_queue', 'module' => 'printers', 'description' => 'Ver cola de impresión'],
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['slug' => $perm['slug']], $perm);
        }

        $adminRole = Role::firstOrCreate(['slug' => 'admin'], [
            'name' => 'Administrador',
            'description' => 'Rol de administrador con todos los permisos',
            'is_system' => true,
            'status' => true,
        ]);
        $adminRole->syncPermissions(Permission::whereIn('slug', [
            'view_dashboard', 'view_companies', 'create_companies', 'edit_companies',
            'view_users', 'create_users', 'edit_users', 'delete_users',
            'view_customers', 'create_customers', 'edit_customers', 'delete_customers',
            'view_products', 'create_products', 'edit_products', 'delete_products',
            'view_categories', 'create_categories', 'edit_categories', 'delete_categories',
            'view_invoices', 'create_invoices', 'send_sunat',
            'view_purchases', 'create_purchases',
            'view_suppliers', 'create_suppliers',
            'view_cashregisters', 'open_cashregister', 'close_cashregister',
            'view_pos', 'use_pos',
            'view_recipes', 'create_recipes', 'edit_recipes', 'delete_recipes',
            'view_production_orders', 'create_production_orders', 'manage_production',
            'view_waste', 'create_waste',
            'view_scheduled_orders', 'create_scheduled_orders', 'manage_scheduled_orders',
            'view_deliveries', 'create_deliveries', 'manage_deliveries',
            'view_delivery_zones', 'view_delivery_persons',
            'view_auxiliary_items',
            'view_roles', 'create_roles', 'edit_roles',
            'view_permissions', 'create_permissions',
            'view_series',
            'view_printers', 'view_print_queue',
        ])->pluck('id')->toArray());

        $panaderoRole = Role::firstOrCreate(['slug' => 'panadero'], [
            'name' => 'Panadero',
            'description' => 'Personal de producción',
            'is_system' => true,
            'status' => true,
        ]);
        $panaderoRole->syncPermissions(Permission::whereIn('slug', [
            'view_dashboard',
            'view_recipes',
            'view_production_orders', 'manage_production',
            'view_waste', 'create_waste',
        ])->pluck('id')->toArray());

        $cajeroRole = Role::firstOrCreate(['slug' => 'cajero'], [
            'name' => 'Cajero',
            'description' => 'Personal de caja y punto de venta',
            'is_system' => true,
            'status' => true,
        ]);
        $cajeroRole->syncPermissions(Permission::whereIn('slug', [
            'view_dashboard',
            'view_invoices', 'create_invoices',
            'view_pos', 'use_pos',
            'view_cashregisters', 'open_cashregister', 'close_cashregister',
            'view_scheduled_orders', 'create_scheduled_orders', 'manage_scheduled_orders',
            'view_deliveries', 'create_deliveries', 'manage_deliveries',
        ])->pluck('id')->toArray());

        $userRole = Role::firstOrCreate(['slug' => 'user'], [
            'name' => 'Usuario',
            'description' => 'Usuario general del sistema',
            'is_system' => true,
            'status' => true,
        ]);
        $userRole->syncPermissions(Permission::whereIn('slug', [
            'view_dashboard',
            'view_invoices',
            'view_pos', 'use_pos',
            'view_cashregisters',
            'view_products', 'view_categories',
            'view_customers',
            'view_recipes',
            'view_scheduled_orders',
        ])->pluck('id')->toArray());
    }
}