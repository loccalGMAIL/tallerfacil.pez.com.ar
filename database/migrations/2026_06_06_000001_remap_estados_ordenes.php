<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    // Mapeo viejo => nuevo
    private array $mapeo = [
        'presupuesto' => 'cotizacion',
        'aprobada'    => 'reparacion',
        'en_proceso'  => 'reparacion',
        'finalizada'  => 'listo',
        'entregada'   => 'entregado',
        'cancelada'   => 'cancelado',
    ];

    private array $estadosNuevos = [
        'recepcion', 'cotizacion', 'reparacion', 'listo', 'entregado', 'cancelado',
    ];

    private array $estadosViejos = [
        'presupuesto', 'aprobada', 'en_proceso', 'finalizada', 'entregada', 'cancelada',
    ];

    public function up(): void
    {
        // MySQL: los enums se modifican con ALTER TABLE MODIFY COLUMN.
        // SQLite no soporta enums nativos; se omite en ese driver.
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        // 1) Ampliar ambos enums al union (viejos + nuevos) para poder migrar sin perder filas
        $union = $this->enumList(array_merge($this->estadosViejos, $this->estadosNuevos));
        DB::statement("ALTER TABLE ordenes MODIFY COLUMN estado ENUM($union) NOT NULL DEFAULT 'presupuesto'");
        DB::statement("ALTER TABLE orden_estados_historial MODIFY COLUMN estado ENUM($union) NOT NULL");

        // 2) Migrar los datos existentes
        foreach ($this->mapeo as $viejo => $nuevo) {
            DB::table('ordenes')->where('estado', $viejo)->update(['estado' => $nuevo]);
            DB::table('orden_estados_historial')->where('estado', $viejo)->update(['estado' => $nuevo]);
        }

        // 3) Reducir los enums al set nuevo, con default 'recepcion'
        $nuevos = $this->enumList($this->estadosNuevos);
        DB::statement("ALTER TABLE ordenes MODIFY COLUMN estado ENUM($nuevos) NOT NULL DEFAULT 'recepcion'");
        DB::statement("ALTER TABLE orden_estados_historial MODIFY COLUMN estado ENUM($nuevos) NOT NULL");
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        // Ampliar al union
        $union = $this->enumList(array_merge($this->estadosViejos, $this->estadosNuevos));
        DB::statement("ALTER TABLE ordenes MODIFY COLUMN estado ENUM($union) NOT NULL DEFAULT 'recepcion'");
        DB::statement("ALTER TABLE orden_estados_historial MODIFY COLUMN estado ENUM($union) NOT NULL");

        // Revertir datos (nota: aprobada/en_proceso colapsaron en reparacion -> vuelve a 'aprobada')
        $inverso = [
            'cotizacion' => 'presupuesto',
            'reparacion' => 'aprobada',
            'listo'      => 'finalizada',
            'entregado'  => 'entregada',
            'cancelado'  => 'cancelada',
            'recepcion'  => 'presupuesto',
        ];
        foreach ($inverso as $nuevo => $viejo) {
            DB::table('ordenes')->where('estado', $nuevo)->update(['estado' => $viejo]);
            DB::table('orden_estados_historial')->where('estado', $nuevo)->update(['estado' => $viejo]);
        }

        $viejos = $this->enumList($this->estadosViejos);
        DB::statement("ALTER TABLE ordenes MODIFY COLUMN estado ENUM($viejos) NOT NULL DEFAULT 'presupuesto'");
        DB::statement("ALTER TABLE orden_estados_historial MODIFY COLUMN estado ENUM($viejos) NOT NULL");
    }

    private function enumList(array $valores): string
    {
        return collect(array_unique($valores))
            ->map(fn ($v) => "'" . $v . "'")
            ->implode(', ');
    }
};
