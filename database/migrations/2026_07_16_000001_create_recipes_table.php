<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recipes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->foreignId('result_product_id')->nullable()->constrained('products')->onDelete('set null');
            $table->decimal('cantidad_producida', 10, 4)->default(0);
            $table->string('unidad')->default('UNIDAD');
            $table->integer('tiempo_preparacion_min')->nullable();
            $table->text('instrucciones')->nullable();
            $table->decimal('costo_estimado', 12, 4)->default(0);
            $table->boolean('activa')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recipes');
    }
};
