<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Pago;
use App\Models\Suscripcion;
use App\Models\Taller;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $totalTalleres       = Taller::count();
        $talleresActivos     = Taller::where('activo', true)->count();
        $suscripcionesActivas = Suscripcion::whereIn('estado', ['activo', 'prueba'])->count();
        $suscripcionesVencidas = Suscripcion::where('estado', 'vencido')->count();
        $pagosMes            = Pago::where('estado', 'aprobado')
            ->whereMonth('fecha_pago', now()->month)
            ->whereYear('fecha_pago', now()->year)
            ->sum('monto');

        $ultimosTalleres = Taller::latest()->limit(5)->get();

        return view('superadmin.dashboard.index', compact(
            'totalTalleres',
            'talleresActivos',
            'suscripcionesActivas',
            'suscripcionesVencidas',
            'pagosMes',
            'ultimosTalleres',
        ));
    }
}
