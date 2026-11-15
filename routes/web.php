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
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    // Platillos / Carta
    Route::get('/platillos', [PlatilloController::class, 'index'])->name('platillos.index');
    Route::get('/platillos/crear', [PlatilloController::class, 'create'])->name('platillos.create');
    Route::post('/platillos', [PlatilloController::class, 'store'])->name('platillos.store');
    Route::get('/platillos/{platillo}/editar', [PlatilloController::class, 'edit'])->name('platillos.edit');
    Route::put('/platillos/{platillo}', [PlatilloController::class, 'update'])->name('platillos.update');
    Route::post('/platillos/{platillo}/toggle', [PlatilloController::class, 'toggle'])->name('platillos.toggle');
    Route::delete('/platillos/{platillo}', [PlatilloController::class, 'destroy'])->name('platillos.destroy');

    // Reservaciones
    Route::get('/reservaciones', [ReservacionController::class, 'index'])->name('reservaciones.index');
    Route::get('/reservaciones/crear', [ReservacionController::class, 'create'])->name('reservaciones.create');
    Route::post('/reservaciones', [ReservacionController::class, 'store'])->name('reservaciones.store');
    Route::get('/reservaciones/{reservacion}', [ReservacionController::class, 'show'])->name('reservaciones.show');
    Route::post('/reservaciones/{reservacion}/estatus', [ReservacionController::class, 'updateEstatus'])->name('reservaciones.estatus');

    // Comandas & POS
    Route::get('/comandas', [ComandaController::class, 'index'])->name('comandas.index');
    Route::get('/comandas/crear', [ComandaController::class, 'create'])->name('comandas.create');
    Route::post('/comandas', [ComandaController::class, 'store'])->name('comandas.store');
    Route::get('/comandas/{id}', [ComandaController::class, 'show'])->name('comandas.show');
    Route::post('/comandas/pedido/{pedidoId}/agregar', [ComandaController::class, 'agregarPlatillo'])->name('comandas.agregar');
    Route::post('/comandas/detalle/{detalleId}/ajustar', [ComandaController::class, 'ajustarPlatillo'])->name('comandas.ajustar');
    Route::post('/comandas/{notaId}/cerrar', [ComandaController::class, 'cerrarCuenta'])->name('comandas.cerrar');

    // Clientes & Fidelización
    Route::get('/clientes', [ClienteController::class, 'index'])->name('clientes.index');
    Route::get('/clientes/crear', [ClienteController::class, 'create'])->name('clientes.create');
    Route::post('/clientes', [ClienteController::class, 'store'])->name('clientes.store');
    Route::get('/clientes/{cliente}', [ClienteController::class, 'show'])->name('clientes.show');
    Route::post('/clientes/{cliente}/alergia', [ClienteController::class, 'agregarAlergia'])->name('clientes.alergia');
    Route::post('/clientes/{cliente}/puntos', [ClienteController::class, 'ajustarPuntos'])->name('clientes.puntos');

    // Programa de Lealtad
    Route::get('/lealtad', [LealtadController::class, 'index'])->name('lealtad.index');

    // Sucursales & Mesas
    Route::get('/sucursales', [SucursalController::class, 'index'])->name('sucursales.index');
    Route::post('/sucursales', [SucursalController::class, 'store'])->name('sucursales.store');
    Route::put('/sucursales/{sucursal}', [SucursalController::class, 'update'])->name('sucursales.update');

    Route::get('/mesas', [MesaController::class, 'index'])->name('mesas.index');
    Route::post('/mesas', [MesaController::class, 'store'])->name('mesas.store');

    // Personal
    Route::get('/empleados', [EmpleadoController::class, 'index'])->name('empleados.index');
    Route::post('/empleados', [EmpleadoController::class, 'store'])->name('empleados.store');

    // Auditoría & Bitácora
    Route::get('/bitacora', [BitacoraController::class, 'index'])->name('bitacora.index');
});
