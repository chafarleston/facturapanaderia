<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deliveries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->foreignId('invoice_id')->nullable()->constrained('invoices')->onDelete('cascade');
            $table->foreignId('delivery_zone_id')->nullable()->constrained('delivery_zones')->onDelete('set null');
            $table->foreignId('delivery_person_id')->nullable()->constrained('delivery_persons')->onDelete('set null');
            $table->string('direccion');
            $table->string('referencia')->nullable();
            $table->string('telefono_contacto')->nullable();
            $table->decimal('costo_envio', 10, 2)->default(0);
            $table->enum('estado', ['pendiente', 'en_ruta', 'entregado', 'cancelado'])->default('pendiente');
            $table->dateTime('fecha_entrega')->nullable();
            $table->text('notas')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deliveries');
    }
};
