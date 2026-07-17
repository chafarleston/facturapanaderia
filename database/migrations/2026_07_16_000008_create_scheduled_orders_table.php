<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('scheduled_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->foreignId('customer_id')->nullable()->constrained('customers')->onDelete('set null');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('order_number')->unique();
            $table->date('fecha_pedido');
            $table->date('fecha_entrega');
            $table->time('hora_entrega')->nullable();
            $table->enum('estado', ['pendiente', 'confirmado', 'en_produccion', 'listo', 'entregado', 'cancelado'])->default('pendiente');
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('igv', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->decimal('anticipo', 12, 2)->default(0);
            $table->decimal('saldo', 12, 2)->default(0);
            $table->text('notas')->nullable();
            $table->text('descripcion')->nullable();
            $table->string('telefono_contacto')->nullable();
            $table->boolean('para_delivery')->default(false);
            $table->timestamps();
        });

        Schema::create('scheduled_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('scheduled_order_id')->constrained('scheduled_orders')->onDelete('cascade');
            $table->foreignId('product_id')->nullable()->constrained('products')->onDelete('set null');
            $table->string('descripcion_personalizada')->nullable();
            $table->decimal('cantidad', 10, 4);
            $table->decimal('precio_unitario', 12, 4);
            $table->decimal('subtotal', 12, 2);
            $table->text('notas')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scheduled_order_items');
        Schema::dropIfExists('scheduled_orders');
    }
};
