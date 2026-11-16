<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Sucursal, Mesa, MesaSucursal, Bitacora};
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class SucursalController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $sucursales = Sucursal::with(['mesas.mesa', 'empleados.empleado'])->get();
        return view('sucursales.index', compact('sucursales'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100|unique:cadena.sucursal,nombre',
            'direccion' => 'required|string|max:250',
            'telefono' => 'nullable|string|max:20',
        ]);

        $sucursal = Sucursal::create([
            'nombre' => $request->nombre,
            'direccion' => $request->direccion,
            'telefono' => $request->telefono,
            'activa' => true,
        ]);

        Bitacora::create([
            'usuario_id' => Auth::id(),
            'accion' => 'CREAR_SUC',
            'registro_id' => "SUCURSAL_{$sucursal->id}",
            'fecha_hora' => Carbon::now(),
        ]);

        return redirect()->route('sucursales.index')->with('success', 'Sucursal registrada con éxito.');
    }

    public function update(Request $request, Sucursal $sucursal)
    {
        $request->validate([
            'nombre' => 'required|string|max:100|unique:cadena.sucursal,nombre,' . $sucursal->id,
            'direccion' => 'required|string|max:250',
            'telefono' => 'nullable|string|max:20',
            'activa' => 'boolean',
        ]);

        $sucursal->update([
            'nombre' => $request->nombre,
            'direccion' => $request->direccion,
            'telefono' => $request->telefono,
            'activa' => $request->has('activa'),
        ]);

        return redirect()->route('sucursales.index')->with('success', 'Sucursal actualizada.');
    }
}