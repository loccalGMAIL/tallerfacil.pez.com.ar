<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 150);
            $table->enum('tipo_doc', ['DNI', 'CUIT', 'CUIL'])->default('DNI');
            $table->string('nro_doc', 20)->nullable();
            $table->string('telefono_normalizado', 20);
            $table->string('telefono_display', 30);
            $table->string('email', 150)->nullable();
            $table->string('direccion', 255)->nullable();
            $table->text('notas')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->index('telefono_normalizado');
            $table->index('nro_doc');
            $table->index('nombre');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
