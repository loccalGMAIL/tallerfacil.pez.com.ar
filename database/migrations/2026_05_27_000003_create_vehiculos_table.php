<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehiculos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes');
            $table->string('patente', 10)->unique();
            $table->string('marca', 50);
            $table->string('modelo', 100);
            $table->year('anio')->nullable();
            $table->unsignedInteger('km_actual')->nullable();
            $table->enum('combustible', ['nafta', 'diesel', 'gnc', 'electrico', 'hibrido', 'otro'])->nullable();
            $table->date('fecha_ultimo_service')->nullable();
            $table->unsignedInteger('km_ultimo_service')->nullable();
            $table->text('notas')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->index('cliente_id');
            $table->index('fecha_ultimo_service');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehiculos');
    }
};
