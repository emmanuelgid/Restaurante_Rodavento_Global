<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\{
    HomeController,
    PlatilloController,
    ReservacionController,
    ComandaController,
    ClienteController,
    SucursalController,
    MesaController,
    EmpleadoController,
    LealtadController,
    BitacoraController
};

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Auth::routes();

Route::middleware('auth')->group(function () {

    // 1. Dashboard / Inicio (cualquier usuario autenticado puede entrar al home,
    // o puedes protegerlo con ->middleware('can:dashboard.ver') si todos los roles tienen el permiso 1)
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    // 2. Platillos / Carta
    // Lectura (Mesero, Capitán, Chef, Gerente, Admin) -> Permiso: menu.ver (ID 14)
    Route::get('/platillos', [PlatilloController::class, 'index'])
        ->middleware('can:menu.ver')
        ->name('platillos.index');

    // Administración (Chef, Gerente, Admin) -> Permiso: menu.admin (ID 3)
    Route::middleware('can:menu.admin')->group(function () {
        Route::get('/platillos/crear', [PlatilloController::class, 'create'])->name('platillos.create');
        Route::post('/platillos', [PlatilloController::class, 'store'])->name('platillos.store');
        Route::get('/platillos/{platillo}/editar', [PlatilloController::class, 'edit'])->name('platillos.edit');
        Route::put('/platillos/{platillo}', [PlatilloController::class, 'update'])->name('platillos.update');
        Route::post('/platillos/{platillo}/toggle', [PlatilloController::class, 'toggle'])->name('platillos.toggle');
        Route::delete('/platillos/{platillo}', [PlatilloController::class, 'destroy'])->name('platillos.destroy');
    });

    // 3. Reservaciones -> Permiso: reservaciones.gestionar (ID 5)
    Route::middleware('can:reservaciones.gestionar')->group(function () {
        Route::get('/reservaciones', [ReservacionController::class, 'index'])->name('reservaciones.index');
        Route::get('/reservaciones/crear', [ReservacionController::class, 'create'])->name('reservaciones.create');
        Route::post('/reservaciones', [ReservacionController::class, 'store'])->name('reservaciones.store');
        Route::get('/reservaciones/{reservacion}', [ReservacionController::class, 'show'])->name('reservaciones.show');
        Route::post('/reservaciones/{reservacion}/estatus', [ReservacionController::class, 'updateEstatus'])->name('reservaciones.estatus');
    });

    // 4. Comandas & POS (dividido según el nivel de responsabilidad de tus permisos)
    // Operación básica (Mesero, Capitán, Gerente, Admin) -> Permiso: comandas.operar (ID 6)
    Route::middleware('can:comandas.operar')->group(function () {
        Route::get('/comandas', [ComandaController::class, 'index'])->name('comandas.index');
        Route::get('/comandas/crear', [ComandaController::class, 'create'])->name('comandas.create');
        Route::post('/comandas', [ComandaController::class, 'store'])->name('comandas.store');
        Route::get('/comandas/{id}', [ComandaController::class, 'show'])->name('comandas.show');
        Route::post('/comandas/pedido/{pedidoId}/agregar', [ComandaController::class, 'agregarPlatillo'])->name('comandas.agregar');
    });

    // Cancelaciones y cortesías (Capitán, Gerente, Admin) -> Permiso: comandas.ajustar (ID 7)
    Route::post('/comandas/detalle/{detalleId}/ajustar', [ComandaController::class, 'ajustarPlatillo'])
        ->middleware('can:comandas.ajustar')
        ->name('comandas.ajustar');

    // Cobro y cierre de cuenta (Cajero, Gerente, Admin) -> Permiso: caja.cobrar (ID 8)
    Route::match(['get', 'post'], '/comandas/{notaId}/cerrar', [ComandaController::class, 'cerrarCuenta'])
        ->middleware('can:caja.cobrar')
        ->name('comandas.cerrar');

    // 5. Clientes, Alergias y Programa de Lealtad -> Permiso: clientes.admin (ID 4)
    Route::middleware('can:clientes.admin')->group(function () {
        Route::get('/clientes', [ClienteController::class, 'index'])->name('clientes.index');
        Route::get('/clientes/crear', [ClienteController::class, 'create'])->name('clientes.create');
        Route::post('/clientes', [ClienteController::class, 'store'])->name('clientes.store');
        Route::get('/clientes/{cliente}', [ClienteController::class, 'show'])->name('clientes.show');
        Route::post('/clientes/{cliente}/alergia', [ClienteController::class, 'agregarAlergia'])->name('clientes.alergia');
        Route::post('/clientes/{cliente}/puntos', [ClienteController::class, 'ajustarPuntos'])->name('clientes.puntos');

        // Programa de Lealtad
        Route::get('/lealtad', [LealtadController::class, 'index'])->name('lealtad.index');
        Route::post('/lealtad/nivel/{nivelId}/beneficio/asignar', [LealtadController::class, 'asignarBeneficio'])->name('lealtad.asignarBeneficio');
        Route::post('/lealtad/nivel/{nivelId}/beneficio/{beneficioId}/quitar', [LealtadController::class, 'quitarBeneficio'])->name('lealtad.quitarBeneficio');
        Route::post('/lealtad/beneficio/crear', [LealtadController::class, 'crearBeneficio'])->name('lealtad.crearBeneficio');
    });

// 6A. Sucursales (Exclusivo Administrador) -> Permiso: sucursales.admin (ID 2)
    Route::middleware('can:sucursales.admin')->group(function () {
        Route::get('/sucursales', [SucursalController::class, 'index'])->name('sucursales.index');
        Route::post('/sucursales', [SucursalController::class, 'store'])->name('sucursales.store');
        Route::put('/sucursales/{sucursal}', [SucursalController::class, 'update'])->name('sucursales.update');
    });

    // 6B. Mesas (Lectura: Gerente y Admin | Creación: Solo Admin)
    Route::get('/mesas', [MesaController::class, 'index'])
        ->middleware('can:mesas.ver')
        ->name('mesas.index');
    Route::post('/mesas', [MesaController::class, 'store'])
        ->middleware('can:mesas.admin')
        ->name('mesas.store');

    // 6C. Empleados (Lectura: Gerente y Admin | Registro: Solo Admin)
    Route::get('/empleados', [EmpleadoController::class, 'index'])
        ->middleware('can:empleados.ver')
        ->name('empleados.index');
    Route::post('/empleados', [EmpleadoController::class, 'store'])
        ->middleware('can:empleados.admin')
        ->name('empleados.store');



    // 7. Auditoría & Bitácora -> Permiso: auditoria.ver (ID 9)
    Route::get('/bitacora', [BitacoraController::class, 'index'])
        ->middleware('can:auditoria.ver')
        ->name('bitacora.index');
});
