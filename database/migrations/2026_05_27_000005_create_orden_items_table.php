<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orden_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('orden_id')->constrained('ordenes')->cascadeOnDelete();
            $table->enum('tipo', ['mano_obra', 'repuesto']);
            $table->string('descripcion', 255);
            $table->decimal('cantidad', 8, 2)->default(1);
            $table->decimal('precio_unitario', 10, 2);
            $table->decimal('subtotal', 10, 2);
            $table->timestamps();

            $table->index('orden_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orden_items');
    }
};
