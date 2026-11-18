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

    public function index(Request $request)
    {
        $search = $request->input('search');

        $query = Empleado::with(['sucursales', 'user.rol']);

        // Filtro de búsqueda por nombre, teléfono o correo del usuario
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nombres', 'LIKE', "%{$search}%")
                    ->orWhere('apellidos', 'LIKE', "%{$search}%")
                    ->orWhere('telefono', 'LIKE', "%{$search}%")
                    ->orWhereHas('user', function($uq) use ($search) {
                        $uq->where('email', 'LIKE', "%{$search}%")
                            ->orWhere('name', 'LIKE', "%{$search}%");
                    });
            });
        }

        $empleados = $query->orderBy('nombres')->get();
        $sucursales = Sucursal::where('activa', true)->get();

        return view('empleados.index', compact('empleados', 'sucursales', 'search'));
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

    // Métodos para Editar y Eliminar exclusivos para el Superadmin
    public function update(Request $request, $id)
    {
        // Validar que sea el superadmin
        if (Auth::user()->email !== 'admin@rodavento.com' && optional(Auth::user()->rol)->nombre !== 'Administrador') {
            abort(403, 'Acción no autorizada.');
        }

        $empleado = Empleado::findOrFail($id);

        $request->validate([
            'nombres' => 'required|string|max:150',
            'apellidos' => 'required|string|max:150',
            'telefono' => 'nullable|string|max:20',
            'puesto' => 'required|string|max:60',
            'estatus' => 'required|string',
        ]);

        $empleado->update([
            'nombres' => $request->nombres,
            'apellidos' => $request->apellidos,
            'telefono' => $request->telefono,
            'puesto' => $request->puesto,
            'estatus' => $request->estatus,
        ]);

        return redirect()->route('empleados.index')->with('success', 'Colaborador actualizado correctamente.');
    }

    public function destroy($id)
    {
        // Validar que sea el superadmin
        if (Auth::user()->email !== 'admin@rodavento.com' && optional(Auth::user()->rol)->nombre !== 'Administrador') {
            abort(403, 'Acción no autorizada.');
        }

        $empleado = Empleado::findOrFail($id);

        // Eliminar usuario y relaciones si es necesario
        if ($empleado->user) {
            $empleado->user->delete();
        }

        $empleado->sucursales()->detach();
        $empleado->delete();

        return redirect()->route('empleados.index')->with('success', 'Colaborador eliminado correctamente.');
    }
}
