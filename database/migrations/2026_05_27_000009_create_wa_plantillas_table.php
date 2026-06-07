<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wa_plantillas', function (Blueprint $table) {
            $table->id();
            $table->enum('tipo', ['presupuesto', 'recepcion', 'recordatorio', 'listo', 'reparacion', 'entregado'])->unique();
            $table->text('texto');
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wa_plantillas');
    }
};
