<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('talleres', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 150)->default('TallerFácil');
            $table->string('razon_social', 150)->nullable();
            $table->string('cuit', 20)->nullable();
            $table->string('telefono', 30)->nullable();
            $table->string('email', 150)->nullable();
            $table->string('direccion', 255)->nullable();
            $table->string('horario', 255)->nullable();
            $table->text('notas')->nullable();
            $table->string('logo', 255)->nullable();
            $table->string('subdominio', 50)->unique();
            $table->boolean('activo')->default(true);
            $table->boolean('en_prueba')->default(true);
            $table->timestamps();

            $table->index('activo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('talleres');
    }
};
