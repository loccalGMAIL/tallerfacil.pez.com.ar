<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $cols = ['auto_recepcion', 'auto_reparacion', 'auto_listo', 'auto_entregado'];

    public function up(): void
    {
        Schema::table('wa_recordatorio_config', function (Blueprint $table) {
            foreach ($this->cols as $col) {
                if (!Schema::hasColumn('wa_recordatorio_config', $col)) {
                    $table->boolean($col)->default(false);
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('wa_recordatorio_config', function (Blueprint $table) {
            foreach ($this->cols as $col) {
                if (Schema::hasColumn('wa_recordatorio_config', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
