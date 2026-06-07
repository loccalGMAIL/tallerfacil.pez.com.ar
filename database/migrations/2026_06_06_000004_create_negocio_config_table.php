<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('negocio_config', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 150)->default('TallerFácil');
            $table->string('razon_social', 150)->nullable();
            $table->string('cuit', 20)->nullable();
            $table->string('telefono', 30)->nullable();
            $table->string('email', 150)->nullable();
            $table->string('direccion', 255)->nullable();
            $table->string('horario', 255)->nullable();
            $table->text('notas')->nullable();
            $table->timestamps();
        });

        // Fila singleton inicial
        DB::table('negocio_config')->insert([
            'id'         => 1,
            'nombre'     => 'TallerFácil',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('negocio_config');
    }
};
