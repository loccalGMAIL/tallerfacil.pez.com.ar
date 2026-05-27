<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orden_estados_historial', function (Blueprint $table) {
            $table->id();
            $table->foreignId('orden_id')->constrained('ordenes');
            $table->enum('estado', [
                'presupuesto',
                'aprobada',
                'en_proceso',
                'finalizada',
                'entregada',
                'cancelada',
            ]);
            $table->foreignId('usuario_id')->nullable()->constrained('usuarios');
            $table->string('notas', 255)->nullable();
            $table->timestamp('fecha_hora')->useCurrent();

            $table->index('orden_id');
            $table->index('fecha_hora');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orden_estados_historial');
    }
};
