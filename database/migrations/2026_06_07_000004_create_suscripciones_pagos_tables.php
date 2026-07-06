<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('suscripciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('taller_id')->constrained('talleres')->cascadeOnDelete();
            $table->enum('plan', ['basico', 'estandar', 'premium'])->default('basico');
            $table->enum('estado', ['prueba', 'activo', 'vencido', 'cancelado'])->default('prueba');
            $table->timestamp('fecha_inicio')->useCurrent();
            $table->timestamp('fecha_vencimiento')->nullable();
            $table->string('mp_preapproval_id', 100)->nullable()->unique();
            $table->timestamps();

            $table->index(['taller_id', 'estado']);
        });

        Schema::create('pagos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('suscripcion_id')->constrained('suscripciones')->cascadeOnDelete();
            $table->foreignId('taller_id')->constrained('talleres')->cascadeOnDelete();
            $table->decimal('monto', 10, 2);
            $table->string('moneda', 3)->default('ARS');
            $table->enum('estado', ['pendiente', 'aprobado', 'rechazado', 'reembolsado'])->default('pendiente');
            $table->string('mp_payment_id', 100)->nullable()->unique();
            $table->timestamp('fecha_pago')->nullable();
            $table->json('detalle_json')->nullable();
            $table->timestamps();

            $table->index(['taller_id', 'estado']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pagos');
        Schema::dropIfExists('suscripciones');
    }
};
