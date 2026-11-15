<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{
    Reservacion, Cliente, Platillo, Sucursal, Pedido,
    NotaReservacion, Bitacora, Mesa
};
use Carbon\Carbon;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $totalReservaciones = Reservacion::count();
        $reservacionesHoy = Reservacion::whereDate('fecha_hora_inicio', Carbon::today())->count();
        $totalClientes = Cliente::count();
        $totalPlatillos = Platillo::where('disponible', true)->count();
        $totalSucursales = Sucursal::where('activa', true)->count();
        $comandasAbiertas = Pedido::whereIn('estatus', ['abierto', 'en_proceso'])->count();
        $ventasTotales = (float) NotaReservacion::sum('total');
        $totalMesas = Mesa::where('activa', true)->count();

        $ultimasReservaciones = Reservacion::with(['cliente'])
            ->latest('id')
            ->take(6)
            ->get();

        $comandasRecientes = Pedido::with(['notaReservacion.sucursal', 'detalles.platillo'])
            ->latest('id')
            ->take(5)
            ->get();

        $topClientes = Cliente::with('nivelLealtad')
            ->orderByDesc('puntos_saldo')
            ->take(5)
            ->get();

        $bitacoras = Bitacora::with('usuario')
            ->latest('fecha_hora')
            ->take(6)
            ->get();

        return view('home', compact(
            'totalReservaciones',
            'reservacionesHoy',
            'totalClientes',
            'totalPlatillos',
            'totalSucursales',
            'comandasAbiertas',
            'ventasTotales',
            'totalMesas',
            'ultimasReservaciones',
            'comandasRecientes',
            'topClientes',
            'bitacoras'
        ));
    }
}