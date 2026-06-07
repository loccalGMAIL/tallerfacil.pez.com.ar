<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ordenes', function (Blueprint $table) {
            $table->id();
            $table->string('numero', 20)->unique();
            $table->foreignId('vehiculo_id')->constrained('vehiculos');
            $table->foreignId('mecanico_id')->nullable()->constrained('usuarios');
            $table->date('fecha_ingreso');
            $table->unsignedInteger('km_ingreso')->nullable();
            $table->text('descripcion')->nullable();
            $table->enum('estado', [
                'recepcion',
                'cotizacion',
                'reparacion',
                'listo',
                'entregado',
                'cancelado',
            ])->default('recepcion');
            $table->decimal('total_estimado', 10, 2)->default(0);
            $table->timestamps();

            $table->index('estado');
            $table->index('vehiculo_id');
            $table->index('mecanico_id');
            $table->index('fecha_ingreso');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ordenes');
    }
};
