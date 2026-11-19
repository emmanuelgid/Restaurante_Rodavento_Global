<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PlatilloController;
use App\Http\Controllers\ReservacionController;
use App\Http\Controllers\ComandaController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\SucursalController;
use App\Http\Controllers\MesaController;
use App\Http\Controllers\EmpleadoController;
use App\Http\Controllers\LealtadController;
use App\Http\Controllers\BitacoraController;
use App\Http\Controllers\Auth\EmpleadoAprobacionController;

Route::get('/', function () {
    try {
        $platillos = \App\Models\Platillo::with(['categoria', 'ingredientes', 'alergenos'])
            ->where('disponible', true)
            ->orderBy('id', 'asc')
            ->get();
        $sucursales = \App\Models\Sucursal::where('activa', true)->get();
        $categorias = \App\Models\CategoriaPlatillo::whereHas('platillos', function ($q) {
            $q->where('disponible', true);
        })->get();
    } catch (\Throwable $e) {
        $platillos = collect();
        $sucursales = collect();
        $categorias = collect();
    }

    return view('welcome', compact('platillos', 'sucursales', 'categorias'));
})->name('welcome');

Auth::routes();

Route::middleware('auth')->group(function () {

    // 1. Dashboard / Inicio
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    // 2. Platillos / Carta
    Route::get('/platillos', [PlatilloController::class, 'index'])
        ->middleware('can:menu.ver')
        ->name('platillos.index');

    Route::middleware('can:menu.admin')->group(function () {
        Route::get('/platillos/crear', [PlatilloController::class, 'create'])->name('platillos.create');
        Route::post('/platillos', [PlatilloController::class, 'store'])->name('platillos.store');
        Route::get('/platillos/{platillo}/editar', [PlatilloController::class, 'edit'])->name('platillos.edit');
        Route::put('/platillos/{platillo}', [PlatilloController::class, 'update'])->name('platillos.update');
        Route::post('/platillos/{platillo}/toggle', [PlatilloController::class, 'toggle'])->name('platillos.toggle');
        Route::delete('/platillos/{platillo}', [PlatilloController::class, 'destroy'])->name('platillos.destroy');
    });

    // 3. Reservaciones
    Route::middleware('can:reservaciones.gestionar')->group(function () {
        Route::get('/reservaciones', [ReservacionController::class, 'index'])->name('reservaciones.index');
        Route::get('/reservaciones/crear', [ReservacionController::class, 'create'])->name('reservaciones.create');
        Route::post('/reservaciones', [ReservacionController::class, 'store'])->name('reservaciones.store');
        Route::get('/reservaciones/{reservacion}', [ReservacionController::class, 'show'])->name('reservaciones.show');
        Route::post('/reservaciones/{reservacion}/estatus', [ReservacionController::class, 'updateEstatus'])->name('reservaciones.estatus');
    });

    // 4. Comandas & POS
    Route::middleware('can:comandas.operar')->group(function () {
        Route::get('/comandas', [ComandaController::class, 'index'])->name('comandas.index');
        Route::get('/comandas/crear', [ComandaController::class, 'create'])->name('comandas.create');
        Route::post('/comandas', [ComandaController::class, 'store'])->name('comandas.store');
        Route::get('/comandas/{id}', [ComandaController::class, 'show'])->name('comandas.show');
        Route::post('/comandas/pedido/{pedidoId}/agregar', [ComandaController::class, 'agregarPlatillo'])->name('comandas.agregar');
    });

    Route::post('/comandas/detalle/{detalleId}/ajustar', [ComandaController::class, 'ajustarPlatillo'])
        ->middleware('can:comandas.ajustar')
        ->name('comandas.ajustar');

    Route::match(['get', 'post'], '/comandas/{notaId}/cerrar', [ComandaController::class, 'cerrarCuenta'])
        ->middleware('can:caja.cobrar')
        ->name('comandas.cerrar');

    // 5. Clientes y Lealtad
    Route::middleware('can:clientes.admin')->group(function () {
        Route::get('/clientes', [ClienteController::class, 'index'])->name('clientes.index');
        Route::get('/clientes/crear', [ClienteController::class, 'create'])->name('clientes.create');
        Route::post('/clientes', [ClienteController::class, 'store'])->name('clientes.store');
        Route::get('/clientes/{cliente}', [ClienteController::class, 'show'])->name('clientes.show');
        Route::post('/clientes/{cliente}/alergia', [ClienteController::class, 'agregarAlergia'])->name('clientes.alergia');
        Route::post('/clientes/{cliente}/puntos', [ClienteController::class, 'ajustarPuntos'])->name('clientes.puntos');

        Route::get('/lealtad', [LealtadController::class, 'index'])->name('lealtad.index');
        Route::post('/lealtad/nivel/{nivelId}/beneficio/asignar', [LealtadController::class, 'asignarBeneficio'])->name('lealtad.asignarBeneficio');
        Route::post('/lealtad/nivel/{nivelId}/beneficio/{beneficioId}/quitar', [LealtadController::class, 'quitarBeneficio'])->name('lealtad.quitarBeneficio');
        Route::post('/lealtad/beneficio/crear', [LealtadController::class, 'crearBeneficio'])->name('lealtad.crearBeneficio');
    });

    // 6A. Sucursales
    Route::middleware('can:sucursales.admin')->group(function () {
        Route::get('/sucursales', [SucursalController::class, 'index'])->name('sucursales.index');
        Route::post('/sucursales', [SucursalController::class, 'store'])->name('sucursales.store');
        Route::put('/sucursales/{sucursal}', [SucursalController::class, 'update'])->name('sucursales.update');
    });

    // 6B. Mesas
    Route::get('/mesas', [MesaController::class, 'index'])
        ->middleware('can:mesas.ver')
        ->name('mesas.index');
    Route::post('/mesas', [MesaController::class, 'store'])
        ->middleware('can:mesas.admin')
        ->name('mesas.store');

    // 6C. Empleados
    Route::get('/empleados', [EmpleadoController::class, 'index'])
        ->middleware('can:empleados.ver')
        ->name('empleados.index');
    Route::post('/empleados', [EmpleadoController::class, 'store'])
        ->middleware('can:empleados.admin')
        ->name('empleados.store');

    // 6D. Aprobación de Cuentas de Empleados (Exclusivo Administrador)
    Route::middleware('can:empleados.admin')->group(function () {
        Route::get('/admin/empleados-pendientes', [EmpleadoAprobacionController::class, 'index'])->name('admin.empleados.pendientes');
        Route::post('/admin/empleados/{id}/aprobar', [EmpleadoAprobacionController::class, 'aprobar'])->name('admin.empleados.aprobar');
        Route::put('/empleados/{id}', [App\Http\Controllers\EmpleadoController::class, 'update'])->name('empleados.update');
        Route::delete('/empleados/{id}', [App\Http\Controllers\EmpleadoController::class, 'destroy'])->name('empleados.destroy');
    });

    // 7. Auditoría & Bitácora
    Route::get('/bitacora', [BitacoraController::class, 'index'])
        ->middleware('can:auditoria.ver')
        ->name('bitacora.index');
});
