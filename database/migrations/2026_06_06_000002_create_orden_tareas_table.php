<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orden_tareas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('orden_id')->constrained('ordenes')->cascadeOnDelete();
            $table->string('descripcion', 255);
            $table->unsignedInteger('posicion')->default(0);
            $table->boolean('completada')->default(false);
            $table->timestamps();

            $table->index('orden_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orden_tareas');
    }
};
