<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    /**
     * Sobrescribimos el método credentials para asegurarnos de que
     * solo los usuarios activos (aprobados por el admin) puedan iniciar sesión.
     */
    protected function credentials(Request $request)
    {
        // Obtenemos las credenciales normales (email y password)
        $credentials = $request->only($this->username(), 'password');

        // Agregamos la condición extra: el usuario debe estar activo
        $credentials['activo'] = true;

        return $credentials;
    }

    /**
     * Personalizamos el mensaje de error cuando las credenciales fallan
     * o el usuario está inactivo (pendiente de aprobación).
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        throw \Illuminate\Validation\ValidationException::withMessages([
            $this->username() => ['Sus datos ya fueron enviados. Su cuenta se encuentra pendiente de aprobación por el Administrador o los datos son incorrectos.'],
        ]);
    }
}
