<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $taller = DB::table('talleres')->first();

        if (! $taller) {
            $this->command->error('No existe ningún taller. Ejecutá primero las migraciones.');
            return;
        }

        $tallerId = $taller->id;

        if (DB::table('clientes')->where('taller_id', $tallerId)->count() > 0) {
            $this->command->warn('El seeder de demo ya fue ejecutado para este taller. Saltando...');
            return;
        }

        $this->command->info("Creando datos de demo para el taller: {$taller->nombre}");

        $this->seedServicios($tallerId);
        $clienteIds = $this->seedClientes($tallerId);
        $vehiculoIds = $this->seedVehiculos($tallerId, $clienteIds);
        $this->seedOrdenes($tallerId, $vehiculoIds);

        $this->command->info('✓ Datos de demo creados exitosamente.');
    }

    private function seedServicios(int $tallerId): void
    {
        $now = now();
        $servicios = [
            ['nombre' => 'Cambio de aceite motor', 'tipo' => 'mano_obra', 'precio' => 8500],
            ['nombre' => 'Cambio de filtro de aceite', 'tipo' => 'mano_obra', 'precio' => 3200],
            ['nombre' => 'Cambio de filtro de aire', 'tipo' => 'mano_obra', 'precio' => 2800],
            ['nombre' => 'Cambio de bujías (4 unid.)', 'tipo' => 'mano_obra', 'precio' => 9600],
            ['nombre' => 'Alineación y balanceo', 'tipo' => 'mano_obra', 'precio' => 14000],
            ['nombre' => 'Cambio de pastillas de freno (eje)', 'tipo' => 'mano_obra', 'precio' => 12500],
            ['nombre' => 'Cambio de discos de freno (eje)', 'tipo' => 'mano_obra', 'precio' => 18000],
            ['nombre' => 'Revisión general / pre-viaje', 'tipo' => 'mano_obra', 'precio' => 6000],
            ['nombre' => 'Cambio de correa de distribución', 'tipo' => 'mano_obra', 'precio' => 35000],
            ['nombre' => 'Diagnóstico electrónico', 'tipo' => 'mano_obra', 'precio' => 7500],
            ['nombre' => 'Aceite motor 15W40 (1L)', 'tipo' => 'repuesto', 'precio' => 4200],
            ['nombre' => 'Filtro de aceite', 'tipo' => 'repuesto', 'precio' => 3800],
            ['nombre' => 'Filtro de aire', 'tipo' => 'repuesto', 'precio' => 4500],
            ['nombre' => 'Bujía NGK estándar', 'tipo' => 'repuesto', 'precio' => 2200],
            ['nombre' => 'Pastillas freno Brembo (par)', 'tipo' => 'repuesto', 'precio' => 18500],
            ['nombre' => 'Líquido de frenos DOT4 (500ml)', 'tipo' => 'repuesto', 'precio' => 2800],
            ['nombre' => 'Correa dentada distribución', 'tipo' => 'repuesto', 'precio' => 22000],
        ];

        foreach ($servicios as $s) {
            DB::table('servicios')->insert(array_merge($s, [
                'taller_id'   => $tallerId,
                'descripcion' => null,
                'activo'      => true,
                'created_at'  => $now,
                'updated_at'  => $now,
            ]));
        }

        $this->command->info("  → {$this->count('servicios', $tallerId)} servicios");
    }

    private function seedClientes(int $tallerId): array
    {
        $now = now();
        $clientes = [
            ['nombre' => 'Roberto Almada',     'tipo_doc' => 'DNI',  'nro_doc' => '28741562', 'tel' => '1145237890', 'cod' => '011', 'email' => 'r.almada@gmail.com',    'dir' => 'Av. Corrientes 3421, CABA'],
            ['nombre' => 'Graciela Benítez',   'tipo_doc' => 'DNI',  'nro_doc' => '31209847', 'tel' => '1167452310', 'cod' => '011', 'email' => 'gracib@hotmail.com',     'dir' => 'Laprida 892, Villa del Parque'],
            ['nombre' => 'Marcos Castellano',  'tipo_doc' => 'DNI',  'nro_doc' => '25678901', 'tel' => '3514782356', 'cod' => '0351', 'email' => null,                    'dir' => 'Bv. San Juan 1204, Córdoba'],
            ['nombre' => 'Sandra Ferreyra',    'tipo_doc' => 'DNI',  'nro_doc' => '33456789', 'tel' => '3515621478', 'cod' => '0351', 'email' => 's.ferreyra@yahoo.com',  'dir' => null],
            ['nombre' => 'Diego Gutiérrez',    'tipo_doc' => 'DNI',  'nro_doc' => '29123456', 'tel' => '2214563210', 'cod' => '0221', 'email' => 'dgutierrez@gmail.com',  'dir' => 'Calle 7 n° 845, La Plata'],
            ['nombre' => 'Repuestos Norte SRL','tipo_doc' => 'CUIT', 'nro_doc' => '30712345678', 'tel' => '1145001234', 'cod' => '011', 'email' => 'compras@repnorte.com.ar', 'dir' => 'Av. Constituyentes 5200, CABA'],
            ['nombre' => 'Verónica Iriarte',   'tipo_doc' => 'DNI',  'nro_doc' => '36890123', 'tel' => '2615478963', 'cod' => '0261', 'email' => 'veri.iriarte@gmail.com','dir' => 'Las Heras 412, Mendoza'],
            ['nombre' => 'Gustavo Juárez',     'tipo_doc' => 'DNI',  'nro_doc' => '27654321', 'tel' => '3414523698', 'cod' => '0341', 'email' => null,                    'dir' => 'Mitre 1560, Rosario'],
            ['nombre' => 'Lorena Medina',      'tipo_doc' => 'DNI',  'nro_doc' => '34567012', 'tel' => '3412356987', 'cod' => '0341', 'email' => 'lorena.m@outlook.com',  'dir' => null],
            ['nombre' => 'Fabián Núñez',       'tipo_doc' => 'DNI',  'nro_doc' => '26789034', 'tel' => '1158741230', 'cod' => '011', 'email' => 'fnunez@gmail.com',       'dir' => 'Segurola 1147, Flores, CABA'],
            ['nombre' => 'Patricia Olivares',  'tipo_doc' => 'DNI',  'nro_doc' => '30145678', 'tel' => '3875412360', 'cod' => '0387', 'email' => 'poli78@gmail.com',      'dir' => 'España 234, Salta'],
            ['nombre' => 'Hernán Paz',         'tipo_doc' => 'DNI',  'nro_doc' => '22345670', 'tel' => '3814563210', 'cod' => '0381', 'email' => null,                    'dir' => 'San Martín 820, Tucumán'],
            ['nombre' => 'Constructora Pampa', 'tipo_doc' => 'CUIT', 'nro_doc' => '30598765432', 'tel' => '2374521000', 'cod' => '0237', 'email' => 'flota@cpampa.com',  'dir' => 'Ruta 188 km 210, Pehuajó'],
            ['nombre' => 'Claudia Rodríguez',  'tipo_doc' => 'DNI',  'nro_doc' => '35012349', 'tel' => '1163214587', 'cod' => '011', 'email' => 'claudiaro@gmail.com',    'dir' => 'Monroe 3456, Belgrano, CABA'],
            ['nombre' => 'Ignacio Soria',      'tipo_doc' => 'DNI',  'nro_doc' => '28901234', 'tel' => '2664523001', 'cod' => '0266', 'email' => 'nachosoria@gmail.com',  'dir' => 'Rivadavia 678, San Luis'],
        ];

        $ids = [];
        foreach ($clientes as $c) {
            $tel = $c['tel'];
            $cod = $c['cod'];
            // Normalizar: 549 + área sin 0 + número sin 15
            $area    = ltrim($cod, '0');
            $display = "{$cod}-{$tel}";
            $norm    = '549' . $area . $tel;

            $ids[] = DB::table('clientes')->insertGetId([
                'taller_id'            => $tallerId,
                'nombre'               => $c['nombre'],
                'tipo_doc'             => $c['tipo_doc'],
                'nro_doc'              => $c['nro_doc'],
                'telefono_normalizado' => $norm,
                'telefono_display'     => $display,
                'email'                => $c['email'],
                'direccion'            => $c['dir'],
                'notas'                => null,
                'activo'               => true,
                'created_at'           => $now,
                'updated_at'           => $now,
            ]);
        }

        $this->command->info("  → {$this->count('clientes', $tallerId)} clientes");
        return $ids;
    }

    private function seedVehiculos(int $tallerId, array $clienteIds): array
    {
        $now = now();

        // [$clienteIdx, patente, marca, modelo, anio, km, combustible, fUltService, kmUltService]
        $vehiculos = [
            [0, 'GHJ 423', 'Volkswagen', 'Gol Trend',     2018, 87000, 'nafta',   '2024-11-15', 82000],
            [0, 'AC123RD', 'Renault',    'Sandero',        2022, 34000, 'nafta',   '2025-03-20', 30000],
            [1, 'FMT 891', 'Ford',       'Fiesta Kinetic', 2015, 145000, 'nafta',  '2024-08-10', 138000],
            [2, 'ILL 374', 'Chevrolet',  'Onix',           2020, 62000, 'nafta',   '2025-01-05', 58000],
            [2, 'AA456BB', 'Toyota',     'Hilux',          2021, 95000, 'diesel',  '2024-12-01', 90000],
            [3, 'MBB 017', 'Peugeot',    '208',            2019, 73000, 'nafta',   '2025-02-14', 70000],
            [4, 'NHT 552', 'Fiat',       'Palio',          2012, 198000, 'gnc',    '2024-06-30', 190000],
            [5, 'JKL 680', 'Volkswagen', 'Vento',          2017, 112000, 'nafta',  '2024-09-22', 108000],
            [5, 'CB789EF', 'Ford',       'Transit',        2020, 158000, 'diesel', '2025-04-10', 155000],
            [6, 'PHO 233', 'Renault',    'Duster',         2021, 48000, 'nafta',   '2025-03-01', 44000],
            [7, 'QVZ 109', 'Chevrolet',  'Cruze',          2016, 134000, 'nafta',  '2024-07-18', 128000],
            [8, 'AKM 756', 'Volkswagen', 'Polo',           2023, 18000, 'nafta',   '2025-05-12', 15000],
            [9, 'DTX 321', 'Ford',       'Focus',          2014, 167000, 'nafta',  '2024-05-25', 160000],
            [10, 'EYB 844', 'Toyota',    'Corolla',        2022, 41000, 'nafta',   '2025-04-02', 38000],
            [11, 'FZC 198', 'Fiat',      'Punto',          2011, 210000, 'gnc',    '2023-11-08', 205000],
            [12, 'GH012IJ', 'Volkswagen','Amarok',         2019, 121000, 'diesel', '2025-01-20', 118000],
            [12, 'HI345KL', 'Renault',   'Logan',          2016, 143000, 'gnc',    '2024-10-14', 140000],
            [13, 'MNO 567', 'Peugeot',   '308',            2020, 56000, 'nafta',   '2025-02-28', 52000],
            [14, 'JQR 789', 'Chevrolet', 'Agile',          2013, 187000, 'nafta',  '2024-04-15', 182000],
            [14, 'KST 456', 'Honda',     'City',           2024, 9000, 'nafta',    null, null],
        ];

        $ids = [];
        foreach ($vehiculos as [$cidx, $patente, $marca, $modelo, $anio, $km, $comb, $fUlt, $kmUlt]) {
            $ids[] = DB::table('vehiculos')->insertGetId([
                'taller_id'             => $tallerId,
                'cliente_id'            => $clienteIds[$cidx],
                'patente'               => $patente,
                'marca'                 => $marca,
                'modelo'                => $modelo,
                'anio'                  => $anio,
                'km_actual'             => $km,
                'combustible'           => $comb,
                'fecha_ultimo_service'  => $fUlt,
                'km_ultimo_service'     => $kmUlt,
                'notas'                 => null,
                'activo'                => true,
                'created_at'            => $now,
                'updated_at'            => $now,
            ]);
        }

        $this->command->info("  → {$this->count('vehiculos', $tallerId)} vehículos");
        return $ids;
    }

    private function seedOrdenes(int $tallerId, array $vehiculoIds): void
    {
        // [vidx, estado, fecha_ingreso, km_ingreso, descripcion, [items], [tareas]]
        $ordenes = [
            // --- ENTREGADAS (históricas) ---
            [0, 'entregado', '-120 days', 85000,
                'Service de los 85.000 km. Cambio de aceite, filtros y revisión general.',
                [['mano_obra', 'Cambio de aceite motor', 1, 8500], ['mano_obra', 'Cambio de filtro de aceite', 1, 3200], ['repuesto', 'Aceite motor 15W40 (4L)', 4, 4200], ['repuesto', 'Filtro de aceite', 1, 3800]],
                [['Drenar aceite viejo', true], ['Reemplazar filtro', true], ['Cargar aceite nuevo', true], ['Controlar niveles', true]],
            ],
            [2, 'entregado', '-100 days', 142000,
                'Pastillas de freno delantera muy desgastadas, ruido al frenar.',
                [['mano_obra', 'Cambio de pastillas de freno (eje)', 1, 12500], ['repuesto', 'Pastillas freno Brembo (par)', 1, 18500], ['repuesto', 'Líquido de frenos DOT4 (500ml)', 1, 2800]],
                [['Retirar ruedas delanteras', true], ['Cambiar pastillas', true], ['Verificar discos', true]],
            ],
            [4, 'entregado', '-90 days', 93000,
                'Service completo camioneta. Cambio aceite, filtros, revisión general.',
                [['mano_obra', 'Cambio de aceite motor', 1, 8500], ['mano_obra', 'Cambio de filtro de aire', 1, 2800], ['repuesto', 'Aceite motor 15W40 (4L)', 4, 4200], ['repuesto', 'Filtro de aire', 1, 4500]],
                [],
            ],
            [6, 'entregado', '-85 days', 196000,
                'Consumo excesivo de GNC. Limpieza de inyectores y revisión de boquillas.',
                [['mano_obra', 'Diagnóstico electrónico', 1, 7500], ['mano_obra', 'Limpieza de inyectores GNC', 1, 15000]],
                [['Conectar scanner', true], ['Limpiar inyectores', true], ['Prueba de ruta', true]],
            ],
            [7, 'entregado', '-75 days', 110000,
                'Alineación y balanceo. Cliente nota vibración en volante a alta velocidad.',
                [['mano_obra', 'Alineación y balanceo', 1, 14000]],
                [],
            ],
            [9, 'entregado', '-60 days', 46000,
                'Revisión pre-viaje larga distancia.',
                [['mano_obra', 'Revisión general / pre-viaje', 1, 6000], ['repuesto', 'Líquido de frenos DOT4 (500ml)', 1, 2800]],
                [['Revisar frenos', true], ['Controlar luces', true], ['Verificar neumáticos', true], ['Revisar correas', true]],
            ],
            [11, 'entregado', '-55 days', 207000,
                'Traqueteo al arrancar en frío. Revisión de tren delantero y bieletas.',
                [['mano_obra', 'Diagnóstico electrónico', 1, 7500], ['mano_obra', 'Cambio de bieletas (par)', 1, 18000], ['repuesto', 'Bieleta de barra estabilizadora', 2, 5500]],
                [],
            ],
            [13, 'entregado', '-45 days', 53000,
                'Cambio de bujías preventivo en service de 50.000 km.',
                [['mano_obra', 'Cambio de bujías (4 unid.)', 1, 9600], ['repuesto', 'Bujía NGK estándar', 4, 2200], ['mano_obra', 'Cambio de filtro de aire', 1, 2800], ['repuesto', 'Filtro de aire', 1, 4500]],
                [['Retirar tapa de culata', true], ['Cambiar bujías', true], ['Reemplazar filtro de aire', true]],
            ],
            [15, 'entregado', '-40 days', 119000,
                'Correa de distribución rota. El motor no arranca.',
                [['mano_obra', 'Cambio de correa de distribución', 1, 35000], ['repuesto', 'Correa dentada distribución', 1, 22000], ['repuesto', 'Tensor de correa', 1, 8500]],
                [['Desmontar tapa distribución', true], ['Cambiar correa y tensor', true], ['Verificar sincronismo', true], ['Prueba de marcha', true]],
            ],
            [17, 'entregado', '-30 days', 54000,
                'Ruido en suspensión delantera derecha, amortiguador con pérdida.',
                [['mano_obra', 'Cambio de amortiguador delantero', 1, 16000], ['repuesto', 'Amortiguador Sachs delantero', 1, 28000]],
                [],
            ],
            // --- CANCELADAS ---
            [3, 'cancelado', '-95 days', 71500,
                'Cliente trajo el auto por ruido de motor. Se cotizó rectificación pero canceló.',
                [['mano_obra', 'Diagnóstico electrónico', 1, 7500]],
                [],
            ],
            [18, 'cancelado', '-50 days', 185000,
                'Pedido de presupuesto frenos. Cliente no aprobó presupuesto.',
                [['mano_obra', 'Cambio de pastillas de freno (eje)', 1, 12500], ['repuesto', 'Pastillas freno Brembo (par)', 1, 18500]],
                [],
            ],
            // --- LISTAS PARA ENTREGAR ---
            [1, 'listo', '-8 days', 32000,
                'Cambio de aceite y service de los 30.000 km.',
                [['mano_obra', 'Cambio de aceite motor', 1, 8500], ['mano_obra', 'Cambio de filtro de aceite', 1, 3200], ['repuesto', 'Aceite motor 15W40 (4L)', 4, 4200], ['repuesto', 'Filtro de aceite', 1, 3800]],
                [['Drenar aceite', true], ['Cambiar filtro', true], ['Cargar aceite', true], ['Controlar todos los niveles', true]],
            ],
            [5, 'listo', '-5 days', 156000,
                'Cambio de pastillas y discos delanteros. Freno pulsaba.',
                [['mano_obra', 'Cambio de discos de freno (eje)', 1, 18000], ['mano_obra', 'Cambio de pastillas de freno (eje)', 1, 12500], ['repuesto', 'Pastillas freno Brembo (par)', 1, 18500]],
                [['Desmontar ruedas', true], ['Cambiar discos', true], ['Cambiar pastillas', true], ['Probar frenada', true]],
            ],
            [10, 'listo', '-4 days', 40000,
                'Alineación y balanceo por desgaste irregular de neumáticos.',
                [['mano_obra', 'Alineación y balanceo', 1, 14000]],
                [],
            ],
            [19, 'listo', '-3 days', 8500,
                'Revisión de garantía. Cambio de aceite de motor nuevo.',
                [['mano_obra', 'Cambio de aceite motor', 1, 8500], ['repuesto', 'Aceite motor 15W40 (4L)', 4, 4200], ['repuesto', 'Filtro de aceite', 1, 3800]],
                [['Verificar niveles generales', true], ['Cambiar aceite', true]],
            ],
            // --- EN REPARACIÓN ---
            [8, 'reparacion', '-12 days', 155000,
                'Fuga de aceite en tapa de válvulas. Reparar juntas.',
                [['mano_obra', 'Cambio de junta tapa de válvulas', 1, 22000], ['repuesto', 'Junta tapa válvulas', 1, 6500]],
                [['Limpiar zona de fuga', false], ['Cambiar junta', false], ['Verificar estanqueidad', false]],
            ],
            [12, 'reparacion', '-10 days', 120500,
                'Service de los 120.000 km. Cambio de correa y service completo.',
                [['mano_obra', 'Cambio de correa de distribución', 1, 35000], ['mano_obra', 'Cambio de aceite motor', 1, 8500], ['repuesto', 'Correa dentada distribución', 1, 22000], ['repuesto', 'Aceite motor 15W40 (4L)', 4, 4200]],
                [['Desmontar tapa distribución', true], ['Cambiar correa', false], ['Service aceite', false], ['Prueba de marcha', false]],
            ],
            [16, 'reparacion', '-7 days', 141000,
                'Calentamiento del motor. Se detectó pérdida en manguera de agua.',
                [['mano_obra', 'Cambio de manguera radiador', 1, 12000], ['repuesto', 'Manguera radiador superior', 1, 3800], ['repuesto', 'Líquido refrigerante (1L)', 2, 2400]],
                [['Vaciar circuito refrigeración', true], ['Reemplazar manguera', false], ['Cargar líquido refrigerante', false]],
            ],
            // --- EN COTIZACIÓN ---
            [14, 'cotizacion', '-3 days', 186000,
                'Múltiples ruidos. Diagnóstico en curso, presupuestando repuestos.',
                [['mano_obra', 'Diagnóstico electrónico', 1, 7500]],
                [['Realizar diagnóstico electrónico', true], ['Enviar presupuesto al cliente', false]],
            ],
            [6, 'cotizacion', '-2 days', 198500,
                'Service de los 200.000 km. Cliente pide presupuesto completo.',
                [['mano_obra', 'Cambio de aceite motor', 1, 8500], ['mano_obra', 'Cambio de bujías (4 unid.)', 1, 9600], ['mano_obra', 'Revisión general / pre-viaje', 1, 6000]],
                [['Relevar estado general', true], ['Preparar presupuesto detallado', false]],
            ],
            [9, 'cotizacion', '-1 days', 47500,
                'Luz de check engine encendida. Diagnóstico pendiente de aprobación.',
                [['mano_obra', 'Diagnóstico electrónico', 1, 7500]],
                [],
            ],
            // --- EN RECEPCIÓN (más recientes) ---
            [11, 'recepcion', 'today', 210500,
                'Ingresa para service. Trae el auto para evaluación.',
                [],
                [],
            ],
            [3, 'recepcion', 'today', 72800,
                'Golpe en puerta delantera derecha. Evaluar carrocería.',
                [],
                [],
            ],
            [17, 'recepcion', 'today', 55500,
                'Revisión de frenos. Cliente nota ruido al frenar a bajas velocidades.',
                [],
                [],
            ],
            [1, 'recepcion', '-1 days', 34100,
                'Cambio de aceite. Turno programado.',
                [],
                [],
            ],
        ];

        $numero   = 1;
        $totalOrd = 0;
        $totalIt  = 0;
        $totalTar = 0;

        foreach ($ordenes as [$vidx, $estado, $fechaStr, $km, $desc, $items, $tareas]) {
            $fecha = $fechaStr === 'today'
                ? Carbon::today()
                : Carbon::today()->modify($fechaStr);

            $ordenNumero = 'ORD-' . str_pad($numero, 5, '0', STR_PAD_LEFT);
            $numero++;

            $total = 0;
            foreach ($items as [, , $cant, $precio]) {
                $total += $cant * $precio;
            }

            $ordenId = DB::table('ordenes')->insertGetId([
                'taller_id'      => $tallerId,
                'numero'         => $ordenNumero,
                'vehiculo_id'    => $vehiculoIds[$vidx],
                'mecanico_id'    => null,
                'fecha_ingreso'  => $fecha->toDateString(),
                'km_ingreso'     => $km,
                'descripcion'    => $desc,
                'estado'         => $estado,
                'total_estimado' => $total,
                'created_at'     => $fecha,
                'updated_at'     => $fecha,
            ]);
            $totalOrd++;

            // Historial de estado
            $this->insertHistorial($ordenId, $estado, $fecha);

            // Items
            foreach ($items as [$tipo, $descripcion, $cantidad, $precio]) {
                DB::table('orden_items')->insert([
                    'orden_id'        => $ordenId,
                    'tipo'            => $tipo,
                    'descripcion'     => $descripcion,
                    'cantidad'        => $cantidad,
                    'precio_unitario' => $precio,
                    'subtotal'        => $cantidad * $precio,
                    'created_at'      => $fecha,
                    'updated_at'      => $fecha,
                ]);
                $totalIt++;
            }

            // Tareas
            foreach ($tareas as $pos => [$descTarea, $completada]) {
                DB::table('orden_tareas')->insert([
                    'orden_id'    => $ordenId,
                    'descripcion' => $descTarea,
                    'posicion'    => $pos,
                    'completada'  => $completada,
                    'created_at'  => $fecha,
                    'updated_at'  => $fecha,
                ]);
                $totalTar++;
            }
        }

        $this->command->info("  → {$totalOrd} órdenes, {$totalIt} items, {$totalTar} tareas");
    }

    private function insertHistorial(int $ordenId, string $estadoFinal, Carbon $fechaBase): void
    {
        $flujo = ['recepcion', 'cotizacion', 'reparacion', 'listo', 'entregado'];

        if ($estadoFinal === 'cancelado') {
            // Solo registrar recepcion + cancelado
            DB::table('orden_estados_historial')->insert([
                'orden_id'   => $ordenId,
                'estado'     => 'recepcion',
                'usuario_id' => null,
                'notas'      => null,
                'fecha_hora' => $fechaBase,
            ]);
            DB::table('orden_estados_historial')->insert([
                'orden_id'   => $ordenId,
                'estado'     => 'cancelado',
                'usuario_id' => null,
                'notas'      => null,
                'fecha_hora' => $fechaBase->copy()->addDays(1),
            ]);
            return;
        }

        $idx  = array_search($estadoFinal, $flujo);
        $dias = 0;

        for ($i = 0; $i <= $idx; $i++) {
            DB::table('orden_estados_historial')->insert([
                'orden_id'   => $ordenId,
                'estado'     => $flujo[$i],
                'usuario_id' => null,
                'notas'      => null,
                'fecha_hora' => $fechaBase->copy()->addDays($dias),
            ]);
            $dias += rand(1, 2);
        }
    }

    private function count(string $tabla, int $tallerId): int
    {
        return DB::table($tabla)->where('taller_id', $tallerId)->count();
    }
}
