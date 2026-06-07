<?php

namespace App\Http\Controllers;

use App\Models\Suscripcion;
use App\Services\MercadoPagoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SuscripcionController extends Controller
{
    public function __construct(private readonly MercadoPagoService $mp) {}

    public function index(): View
    {
        $taller       = app('taller.actual');
        $suscripcion  = $taller->suscripcionActual;

        return view('suscripcion.index', compact('taller', 'suscripcion'));
    }

    public function iniciarCheckout(Request $request): RedirectResponse
    {
        $request->validate([
            'plan' => ['required', 'in:basico,estandar,premium'],
        ]);

        $taller  = app('taller.actual');
        $backUrl = url('/suscripcion');

        $checkoutUrl = $this->mp->crearPreapproval($taller, $request->plan, $backUrl);

        if (!$checkoutUrl) {
            return back()->with('error', 'No pudimos iniciar el checkout. Intentá de nuevo.');
        }

        return redirect()->away($checkoutUrl);
    }

    public function retorno(Request $request): RedirectResponse
    {
        $status = $request->query('status');

        return redirect()->route('suscripcion.index')->with(
            $status === 'approved' ? 'success' : 'error',
            $status === 'approved'
                ? 'Tu suscripción fue activada. ¡Gracias!'
                : 'El pago no se completó. Podés intentarlo de nuevo.'
        );
    }
}
