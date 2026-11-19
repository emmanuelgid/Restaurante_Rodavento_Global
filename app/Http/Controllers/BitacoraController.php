<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bitacora;

class BitacoraController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = Bitacora::with('usuario');

        if ($request->filled('fecha_inicio')) {
            $query->whereDate('fecha_hora', '>=', $request->fecha_inicio);
        }

        if ($request->filled('fecha_fin')) {
            $query->whereDate('fecha_hora', '<=', $request->fecha_fin);
        }

        if ($request->filled('accion')) {
            $query->where('accion', $request->accion);
        }

        $bitacoras = $query->orderBy('fecha_hora', 'desc')
            ->paginate(15)
            ->appends($request->all());

        $accionesDisponibles = Bitacora::select('accion')
            ->distinct()
            ->orderBy('accion')
            ->pluck('accion');

        return view('bitacora.index', compact('bitacoras', 'accionesDisponibles'));
    }
}