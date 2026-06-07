<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        * { font-family: DejaVu Sans, sans-serif; }
        body { color: #1f2937; font-size: 12px; margin: 0; padding: 24px; }
        .header { border-bottom: 3px solid #f59e0b; padding-bottom: 12px; margin-bottom: 16px; }
        .header h1 { margin: 0; font-size: 22px; color: #111827; }
        .header .sub { color: #6b7280; font-size: 11px; }
        .meta { width: 100%; margin-bottom: 16px; }
        .meta td { vertical-align: top; padding: 2px 0; }
        .label { color: #6b7280; font-size: 10px; text-transform: uppercase; letter-spacing: .5px; }
        table.items { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
        table.items th { text-align: left; background: #f3f4f6; padding: 6px 8px; font-size: 10px; text-transform: uppercase; color: #6b7280; }
        table.items td { padding: 6px 8px; border-bottom: 1px solid #f3f4f6; }
        .right { text-align: right; }
        .group-title { font-weight: bold; font-size: 13px; margin: 14px 0 4px; color: #374151; }
        .group-sub { color: #6b7280; font-weight: normal; }
        .totales { width: 100%; margin-top: 8px; }
        .totales td { padding: 4px 8px; }
        .total-final { font-size: 16px; font-weight: bold; border-top: 2px solid #111827; }
        .foot { margin-top: 30px; color: #9ca3af; font-size: 10px; text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $negocio->nombre }}</h1>
        <div class="sub">
            Cotización — Orden {{ $orden->numero }}
            @if($negocio->telefono) · Tel: {{ $negocio->telefono }} @endif
            @if($negocio->direccion) · {{ $negocio->direccion }} @endif
            @if($negocio->cuit) · CUIT: {{ $negocio->cuit }} @endif
        </div>
    </div>

    <table class="meta">
        <tr>
            <td width="50%">
                <div class="label">Cliente</div>
                <div><strong>{{ $orden->vehiculo->cliente->nombre }}</strong></div>
                <div>{{ $orden->vehiculo->cliente->telefono_display }}</div>
            </td>
            <td width="50%">
                <div class="label">Vehículo</div>
                <div><strong>{{ $orden->vehiculo->patente }}</strong> — {{ $orden->vehiculo->marca }} {{ $orden->vehiculo->modelo }} {{ $orden->vehiculo->anio }}</div>
                <div class="label" style="margin-top:6px;">Fecha</div>
                <div>{{ $orden->fecha_ingreso->format('d/m/Y') }}</div>
            </td>
        </tr>
    </table>

    @php
        $totalServicios = $servicios->sum('subtotal');
        $totalRepuestos = $repuestos->sum('subtotal');
    @endphp

    @if($servicios->count())
    <div class="group-title">Servicios <span class="group-sub">({{ $servicios->count() }}) — ${{ number_format($totalServicios, 0, ',', '.') }}</span></div>
    <table class="items">
        <thead><tr><th>Descripción</th><th class="right">Cant.</th><th class="right">P. Unit.</th><th class="right">Subtotal</th></tr></thead>
        <tbody>
            @foreach($servicios as $item)
            <tr>
                <td>{{ $item->descripcion }}</td>
                <td class="right">{{ $item->cantidad }}</td>
                <td class="right">${{ number_format($item->precio_unitario, 2, ',', '.') }}</td>
                <td class="right">${{ number_format($item->subtotal, 2, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    @if($repuestos->count())
    <div class="group-title">Repuestos <span class="group-sub">({{ $repuestos->count() }}) — ${{ number_format($totalRepuestos, 0, ',', '.') }}</span></div>
    <table class="items">
        <thead><tr><th>Descripción</th><th class="right">Cant.</th><th class="right">P. Unit.</th><th class="right">Subtotal</th></tr></thead>
        <tbody>
            @foreach($repuestos as $item)
            <tr>
                <td>{{ $item->descripcion }}</td>
                <td class="right">{{ $item->cantidad }}</td>
                <td class="right">${{ number_format($item->precio_unitario, 2, ',', '.') }}</td>
                <td class="right">${{ number_format($item->subtotal, 2, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    @if(!$servicios->count() && !$repuestos->count())
    <p style="color:#9ca3af; text-align:center; padding:20px;">Sin ítems cargados.</p>
    @endif

    <table class="totales">
        <tr>
            <td class="right" width="80%">Subtotal</td>
            <td class="right">${{ number_format($orden->total_estimado, 2, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="right total-final">TOTAL</td>
            <td class="right total-final">${{ number_format($orden->total_estimado, 2, ',', '.') }}</td>
        </tr>
    </table>

    <div class="foot">Cotización generada el {{ now()->format('d/m/Y H:i') }} — TallerFácil</div>
</body>
</html>
