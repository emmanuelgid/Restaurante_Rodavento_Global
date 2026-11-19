<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Empleado;
use App\Models\EmpleadoSucursal;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    use RegistersUsers;

    protected $redirectTo = '/login';

    public function __construct()
    {
        $this->middleware('guest');
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'nombres' => ['required', 'string', 'max:150'],
            'apellidos' => ['required', 'string', 'max:150'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'telefono' => ['required', 'string', 'max:20'],
            'sucursal_id' => ['required', 'exists:cadena.sucursal,id'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    protected function create(array $data)
    {
        return DB::transaction(function () use ($data) {

            // 1. Creamos el registro en cadena.empleado con nombres y apellidos reales
            $empleado = Empleado::create([
                'nombres' => trim($data['nombres']),
                'apellidos' => trim($data['apellidos']),
                'telefono' => $data['telefono'],
                'puesto' => 'Pendiente de asignar',
                'estatus' => 'pendiente',
                'fecha_ingreso' => now()->toDateString(),
            ]);

            // 2. Vinculamos la sucursal en la tabla pivote cadena.empleado_sucursal
            EmpleadoSucursal::create([
                'empleado_id' => $empleado->id,
                'sucursal_id' => $data['sucursal_id'],
            ]);

            // 3. Creamos el usuario concatenando nombre completo para la tabla users
            return User::create([
                'name' => trim($data['nombres']) . ' ' . trim($data['apellidos']),
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'empleado_id' => $empleado->id,
                'rol_id' => null,
                'activo' => false,
            ]);
        });
    }

    protected function registered(Request $request, $user)
    {
        Auth::logout();

        return redirect()->route('login')
            ->with('status', '¡Registro exitoso! Su cuenta ha sido creada y se encuentra pendiente de aprobación por el administrador.');
    }
}
