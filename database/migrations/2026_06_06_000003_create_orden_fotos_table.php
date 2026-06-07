<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orden_fotos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('orden_id')->constrained('ordenes')->cascadeOnDelete();
            $table->string('ruta');
            $table->enum('tipo', ['patente', 'recepcion', 'trabajo'])->default('recepcion');
            $table->string('descripcion', 255)->nullable();
            $table->timestamps();

            $table->index('orden_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orden_fotos');
    }
};
