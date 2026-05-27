<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wa_recordatorio_config', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('umbral_meses')->nullable()->default(6);
            $table->unsignedInteger('umbral_km')->nullable()->default(10000);
            $table->unsignedSmallInteger('ventana_minima_dias')->default(30);
            $table->boolean('activo')->default(true);
            $table->unsignedSmallInteger('tope_diario')->default(50);
            $table->unsignedSmallInteger('delay_min_seg')->default(30);
            $table->unsignedSmallInteger('delay_max_seg')->default(90);
            $table->timestamp('updated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wa_recordatorio_config');
    }
};
