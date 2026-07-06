<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Taller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Activitylog\Models\Activity;

class LogsController extends Controller
{
    public function index(Request $request): View
    {
        $query = Activity::with('causer')
            ->latest();

        if ($request->taller_id) {
            $query->where('properties->taller_id', $request->taller_id);
        }

        if ($request->log_name) {
            $query->where('log_name', $request->log_name);
        }

        if ($request->desde) {
            $query->whereDate('created_at', '>=', $request->desde);
        }

        if ($request->hasta) {
            $query->whereDate('created_at', '<=', $request->hasta);
        }

        $logs    = $query->paginate(50);
        $talleres = Taller::orderBy('nombre')->get(['id', 'nombre']);

        return view('superadmin.logs.index', compact('logs', 'talleres'));
    }
}
