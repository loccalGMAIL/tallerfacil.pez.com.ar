<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrdenItemRequest;
use App\Http\Requests\StoreOrdenRequest;
use App\Models\Orden;
use App\Models\OrdenItem;
use App\Models\Usuario;
use App\Models\Vehiculo;
use App\Services\OrdenService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrdenController extends Controller
{
    public function __construct(private OrdenService $ordenService) {}

    public function index(Request $request): View
    {
        $query = Orden::with(['vehiculo.cliente', 'mecanico'])
            ->when($request->estado, fn ($q) => $q->where('estado', $request->estado))
            ->when($request->mecanico_id, fn ($q) => $q->where('mecanico_id', $request->mecanico_id))
            ->when($request->vehiculo_id, fn ($q) => $q->where('vehiculo_id', $request->vehiculo_id))
            ->when($request->fecha_desde, fn ($q) => $q->where('fecha_ingreso', '>=', $request->fecha_desde))
            ->when($request->fecha_hasta, fn ($q) => $q->where('fecha_ingreso', '<=', $request->fecha_hasta));

        // Mecánico solo ve sus órdenes
        if (auth()->user()->rol === 'mecanico') {
            $query->where('mecanico_id', auth()->id());
        }

        $ordenes   = $query->orderBy('fecha_ingreso', 'desc')->paginate(20)->withQueryString();
        $mecanicos = Usuario::where('rol', 'mecanico')->where('activo', true)->orderBy('nombre')->get();

        return view('ordenes.index', compact('ordenes', 'mecanicos'));
    }

    public function create(Request $request): View
    {
        $vehiculo  = $request->vehiculo_id ? Vehiculo::with('cliente')->findOrFail($request->vehiculo_id) : null;
        $vehiculos = Vehiculo::with('cliente')->activos()->orderBy('patente')->get();
        $mecanicos = Usuario::where('rol', 'mecanico')->where('activo', true)->orderBy('nombre')->get();

        return view('ordenes.create', compact('vehiculo', 'vehiculos', 'mecanicos'));
    }

    public function store(StoreOrdenRequest $request): RedirectResponse
    {
        $orden = $this->ordenService->crear($request->validated(), auth()->user());

        return redirect()->route('ordenes.show', $orden)
            ->with('success', "Orden {$orden->numero} creada correctamente.");
    }

    public function show(Orden $orden): View
    {
        $orden->load(['vehiculo.cliente', 'mecanico', 'items', 'historial.usuario', 'waMensajes']);
        $mecanicos       = Usuario::where('rol', 'mecanico')->where('activo', true)->orderBy('nombre')->get();
        $transiciones    = Orden::TRANSICIONES[$orden->estado] ?? [];

        return view('ordenes.show', compact('orden', 'mecanicos', 'transiciones'));
    }

    public function cambiarEstado(Request $request, Orden $orden): RedirectResponse
    {
        $request->validate([
            'estado'            => ['required', 'string'],
            'notas'             => ['nullable', 'string', 'max:255'],
            'actualizar_service' => ['nullable', 'boolean'],
        ]);

        try {
            $this->ordenService->cambiarEstado(
                $orden,
                $request->estado,
                auth()->user(),
                $request->notas,
                $request->boolean('actualizar_service')
            );

            return back()->with('success', "Estado cambiado a «{$request->estado}» correctamente.");
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function asignarMecanico(Request $request, Orden $orden): RedirectResponse
    {
        $request->validate(['mecanico_id' => ['nullable', 'exists:usuarios,id']]);

        $orden->update(['mecanico_id' => $request->mecanico_id]);

        return back()->with('success', 'Mecánico asignado correctamente.');
    }

    public function storeItem(StoreOrdenItemRequest $request, Orden $orden): RedirectResponse
    {
        if (!$orden->estaAbierta()) {
            return back()->with('error', 'No se pueden agregar ítems a una orden cerrada.');
        }

        $orden->items()->create($request->validated());
        $orden->recalcularTotal();

        return back()->with('success', 'Ítem agregado correctamente.');
    }

    public function destroyItem(Orden $orden, OrdenItem $item): RedirectResponse
    {
        if (!$orden->estaAbierta()) {
            return back()->with('error', 'No se pueden eliminar ítems de una orden cerrada.');
        }

        $item->delete();
        $orden->recalcularTotal();

        return back()->with('success', 'Ítem eliminado.');
    }
}
