<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('production_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->foreignId('recipe_id')->nullable()->constrained('recipes')->onDelete('set null');
            $table->foreignId('product_id')->nullable()->constrained('products')->onDelete('set null');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->date('fecha_produccion');
            $table->decimal('cantidad_planificada', 10, 4);
            $table->decimal('cantidad_producida', 10, 4)->default(0);
            $table->string('unidad')->default('UNIDAD');
            $table->enum('estado', ['planificado', 'en_proceso', 'completado', 'cancelado'])->default('planificado');
            $table->decimal('costo_total', 12, 4)->default(0);
            $table->text('notas')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('production_orders');
    }
};
