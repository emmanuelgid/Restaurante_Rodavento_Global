<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Empleado, Sucursal, EmpleadoSucursal, Bitacora};
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class EmpleadoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $empleados = Empleado::with(['sucursales', 'user.rol'])->orderBy('nombres')->get();
        $sucursales = Sucursal::where('activa', true)->get();

        return view('empleados.index', compact('empleados', 'sucursales'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombres' => 'required|string|max:150',
            'apellidos' => 'required|string|max:150',
            'telefono' => 'nullable|string|max:20',
            'puesto' => 'required|string|max:60',
            'sucursal_id' => 'required|exists:cadena.sucursal,id',
        ]);

        $empleado = Empleado::create([
            'nombres' => $request->nombres,
            'apellidos' => $request->apellidos,
            'telefono' => $request->telefono,
            'puesto' => $request->puesto,
            'fecha_ingreso' => Carbon::today(),
            'estatus' => 'activo',
        ]);

        EmpleadoSucursal::create([
            'empleado_id' => $empleado->id,
            'sucursal_id' => $request->sucursal_id,
        ]);

        Bitacora::create([
            'usuario_id' => Auth::id(),
            'accion' => 'CREAR_EMP',
            'registro_id' => "EMP_{$empleado->id}",
            'fecha_hora' => Carbon::now(),
        ]);

        return redirect()->route('empleados.index')->with('success', 'Colaborador registrado exitosamente.');
    }
}