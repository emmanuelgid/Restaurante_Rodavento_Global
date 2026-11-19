<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Empleado;
use App\Models\Rol;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EmpleadoAprobacionController extends Controller
{
    public function index()
    {
        $usuariosPendientes = User::where('activo', false)
            ->orWhereNull('rol_id')
            ->with('empleado')
            ->get();

        $roles = Rol::all();

        return view('admin.empleados-pendientes', compact('usuariosPendientes', 'roles'));
    }

    public function aprobar(Request $request, $id)
    {
        $request->validate([
            'rol_id' => 'required|exists:cadena.rol,id',
        ]);

        $usuario = User::findOrFail($id);

        // Buscamos el rol seleccionado para extraer su nombre (ej. "Mesero", "Administrador")
        $rolSeleccionado = Rol::findOrFail($request->rol_id);

        DB::transaction(function () use ($request, $usuario, $rolSeleccionado) {
            // 1. Actualizamos el usuario con su rol y lo activamos
            $usuario->update([
                'rol_id' => $request->rol_id,
                'activo' => true,
            ]);

            // 2. Asignamos automáticamente el puesto en la tabla empleados usando el nombre del rol
            if ($usuario->empleado_id) {
                $empleado = Empleado::find($usuario->empleado_id);
                if ($empleado) {
                    $empleado->update([
                        'puesto' => $rolSeleccionado->nombre,
                        'estatus' => 'activo',
                    ]);
                }
            }
        });

        return redirect()->route('admin.empleados.pendientes')
            ->with('success', '¡Empleado aprobado y rol asignado correctamente! Ya puede iniciar sesión.');
    }
}
