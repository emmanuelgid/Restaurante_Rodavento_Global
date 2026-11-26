<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Mesa, Sucursal, MesaSucursal, Bitacora};
use App\Support\SucursalActiva;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class MesaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $sucursalActiva = SucursalActiva::id();

        $mesas = Mesa::with(['mesasSucursal' => function ($query) use ($sucursalActiva) {
            if ($sucursalActiva) {
                $query->where('sucursal_id', $sucursalActiva);
            }
            $query->with('sucursal');
        }])
            ->when($sucursalActiva, function ($query) use ($sucursalActiva) {
                $query->whereHas('mesasSucursal', function ($q) use ($sucursalActiva) {
                    $q->where('sucursal_id', $sucursalActiva);
                });
            })
            ->orderBy('codigo_mesa')
            ->get();

        $sucursales = Sucursal::where('activa', true)
            ->when($sucursalActiva, fn($q) => $q->where('id', $sucursalActiva))
            ->get();

        return view('mesas.index', compact('mesas', 'sucursales'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'codigo_mesa' => 'required|string|max:10|unique:mesa,codigo_mesa',
            'capacidad' => 'required|integer|min:1|max:30',
            'sucursal_id' => 'required|exists:sucursal,id',
        ]);

        $sucursalActiva = SucursalActiva::id();
        $sucursalId = $sucursalActiva ?: $request->sucursal_id;

        $mesa = Mesa::create([
            'codigo_mesa' => strtoupper($request->codigo_mesa),
            'capacidad' => $request->capacidad,
            'activa' => true,
        ]);

        MesaSucursal::create([
            'sucursal_id' => $sucursalId,
            'mesa_id' => $mesa->id,
        ]);

        Bitacora::create([
            'usuario_id' => Auth::id(),
            'accion' => 'CREAR_MESA',
            'registro_id' => "MESA_{$mesa->id}",
            'fecha_hora' => Carbon::now(),
        ]);

        return redirect()->route('mesas.index')->with('success', "Mesa {$mesa->codigo_mesa} registrada y asignada a la sucursal.");
    }
}
