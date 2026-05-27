<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wa_config', function (Blueprint $table) {
            $table->id();
            $table->string('url_base', 255)->default('');
            $table->string('api_key', 255)->default('');
            $table->string('instancia', 100)->default('');
            $table->string('webhook_secret', 255)->default('');
            $table->enum('estado_conexion', ['conectado', 'desconectado', 'desconocido'])->default('desconocido');
            $table->timestamp('updated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wa_config');
    }
};
