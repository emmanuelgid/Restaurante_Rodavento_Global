<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{
    Reservacion, Cliente, Platillo, Sucursal, Pedido,
    NotaReservacion, Bitacora, Mesa, MesaSucursal
};
use Carbon\Carbon;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $sucursales = Sucursal::where('activa', true)->orderBy('nombre')->get();
        $sucursalId = $request->query('sucursal_id');
        $sucursalActual = $sucursalId ? Sucursal::find($sucursalId) : null;

        // Base queries
        $reservacionesHoyQuery = Reservacion::whereDate('fecha_hora_inicio', Carbon::today());
        $totalReservacionesQuery = Reservacion::query();
        $comandasAbiertasQuery = Pedido::whereIn('estatus', ['abierto', 'en_proceso']);
        $ventasTotalesQuery = NotaReservacion::query();
        $totalMesasQuery = Mesa::where('activa', true);
        
        $ultimasReservacionesQuery = Reservacion::with([
            'cliente',
            'mesasAsignadas.mesaSucursal.mesa',
            'mesasAsignadas.mesaSucursal.sucursal',
            'nota.sucursal'
        ])->latest('id');

        $comandasRecientesQuery = Pedido::with([
            'notaReservacion.sucursal',
            'detalles.platillo'
        ])->latest('id');

        // Filtrado por Sucursal / Lugar si fue seleccionado
        if ($sucursalId) {
            $filtroRes = function ($q) use ($sucursalId) {
                $q->whereHas('mesasAsignadas.mesaSucursal', function ($sq) use ($sucursalId) {
                    $sq->where('sucursal_id', $sucursalId);
                })->orWhereHas('nota', function ($sq) use ($sucursalId) {
                    $sq->where('sucursal_id', $sucursalId);
                });
            };

            $reservacionesHoyQuery->where($filtroRes);
            $totalReservacionesQuery->where($filtroRes);
            $ultimasReservacionesQuery->where($filtroRes);

            $comandasAbiertasQuery->whereHas('notaReservacion', function ($q) use ($sucursalId) {
                $q->where('sucursal_id', $sucursalId);
            });

            $comandasRecientesQuery->whereHas('notaReservacion', function ($q) use ($sucursalId) {
                $q->where('sucursal_id', $sucursalId);
            });

            $ventasTotalesQuery->where('sucursal_id', $sucursalId);

            $totalMesasQuery->whereHas('mesasSucursal', function ($q) use ($sucursalId) {
                $q->where('sucursal_id', $sucursalId);
            });
        }

        $reservacionesHoy = $reservacionesHoyQuery->count();
        $totalReservaciones = $totalReservacionesQuery->count();
        $comandasAbiertas = $comandasAbiertasQuery->count();
        $ventasTotales = (float) $ventasTotalesQuery->sum('total');
        $totalMesas = $totalMesasQuery->count();
        $totalClientes = Cliente::count();
        $totalPlatillos = Platillo::where('disponible', true)->count();
        $totalSucursales = $sucursales->count();

        $ultimasReservaciones = $ultimasReservacionesQuery->take(6)->get();
        $comandasRecientes = $comandasRecientesQuery->take(5)->get();

        $topClientes = Cliente::with('nivelLealtad')
            ->orderByDesc('puntos_saldo')
            ->take(5)
            ->get();

        $bitacoras = Bitacora::with('usuario')
            ->latest('fecha_hora')
            ->take(6)
            ->get();

        return view('home', compact(
            'sucursales',
            'sucursalId',
            'sucursalActual',
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
