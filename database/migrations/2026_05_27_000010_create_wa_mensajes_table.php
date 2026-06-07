<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wa_mensajes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes');
            $table->foreignId('orden_id')->nullable()->constrained('ordenes');
            $table->foreignId('vehiculo_id')->nullable()->constrained('vehiculos');
            $table->enum('tipo', ['presupuesto', 'recepcion', 'recordatorio', 'listo', 'reparacion', 'entregado', 'manual']);
            $table->text('contenido');
            $table->enum('origen', ['php', 'n8n'])->default('php');
            $table->string('evolution_message_id', 100)->nullable();
            $table->enum('estado_entrega', ['pendiente', 'enviado', 'entregado', 'leido', 'fallido'])->default('pendiente');
            $table->text('error_detalle')->nullable();
            $table->unsignedTinyInteger('intentos')->default(0);
            $table->timestamp('fecha_hora')->useCurrent();
            $table->timestamp('fecha_entregado')->nullable();
            $table->timestamp('fecha_leido')->nullable();
            $table->timestamps();

            $table->index('evolution_message_id');
            $table->index('cliente_id');
            $table->index(['tipo', 'estado_entrega']);
            $table->index('fecha_hora');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wa_mensajes');
    }
};
