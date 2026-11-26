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

    public function index(Request $request)
    {
        $puedeElegirSucursal = auth()->user()?->can('sucursales.admin') ?? false;

        if ($puedeElegirSucursal) {
            $sucursales = Sucursal::where('activa', true)->orderBy('nombre')->get();

            // Si viene el parámetro en el formulario (?sucursal_id=...)
            if ($request->has('sucursal_id')) {
                $idSolicitado = $request->query('sucursal_id');

                if ($idSolicitado !== null && $idSolicitado !== '' && $sucursales->contains('id', (int) $idSolicitado)) {
                    session(['admin_sucursal_id' => (int) $idSolicitado]);
                } else {
                    session()->forget('admin_sucursal_id');
                }
            }

            $sucursalId = session('admin_sucursal_id');
            $sucursalActual = $sucursalId ? $sucursales->firstWhere('id', (int) $sucursalId) : null;

            // Si la sucursal guardada ya no existe o se desactivó, limpiar sesión
            if ($sucursalId && !$sucursalActual) {
                session()->forget('admin_sucursal_id');
                $sucursalId = null;
            }
        } else {
            session()->forget('admin_sucursal_id');
            $sucursales = collect();
            $sucursalId = null;
            $sucursalActual = null;
        }

        $sucursales = Sucursal::where('activa', true)->orderBy('nombre')->get();
        $sucursalId = $request->query('sucursal_id');
        $sucursalActual = $sucursalId ? Sucursal::find($sucursalId) : null;

        // 1. Base queries para métricas
        $reservacionesHoyQuery = Reservacion::whereDate('fecha_hora_inicio', Carbon::today())
            ->whereNotIn('estatus', ['cancelada']);
        $totalReservacionesQuery = Reservacion::query();
        $comandasAbiertasQuery = Pedido::whereIn('estatus', ['abierto', 'en_proceso']);
        $ventasTotalesQuery = NotaReservacion::query();
        $totalMesasQuery = Mesa::where('activa', true);

        // 2. Próximas Reservaciones (SOLO PENDIENTES / ACTIVAS, NUNCA FINALIZADAS NI CANCELADAS)
        $ultimasReservacionesQuery = Reservacion::with([
            'cliente',
            'mesasAsignadas.mesaSucursal.mesa',
            'mesasAsignadas.mesaSucursal.sucursal',
            'nota.sucursal'
        ])
        ->whereNotIn('estatus', ['finalizada', 'cancelada', 'concluida'])
        ->orderBy('fecha_hora_inicio', 'asc');

        // 3. Monitoreo de Comandas VIVAS (SOLO ABIERTAS Y EN PROCESO, NUNCA CERRADAS)
        $comandasRecientesQuery = Pedido::with([
            'notaReservacion.sucursal',
            'detalles.platillo'
        ])
        ->whereIn('estatus', ['abierto', 'en_proceso'])
        ->latest('id');

        // 4. Filtrado por Sucursal / Lugar (si fue seleccionado)
        if ($sucursalId) {
            $filtroRes = function ($q) use ($sucursalId) {
                $q->where(function ($sub) use ($sucursalId) {
                    $sub->whereHas('mesasAsignadas.mesaSucursal', function ($sq) use ($sucursalId) {
                        $sq->where('sucursal_id', $sucursalId);
                    })->orWhereHas('nota', function ($sq) use ($sucursalId) {
                        $sq->where('sucursal_id', $sucursalId);
                    });
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

        // Conteos y totales
        $reservacionesHoy = $reservacionesHoyQuery->count();
        $totalReservaciones = $totalReservacionesQuery->count();
        $comandasAbiertas = $comandasAbiertasQuery->count();
        $ventasTotales = (float) $ventasTotalesQuery->sum('total');
        $totalMesas = $totalMesasQuery->count();
        $totalClientes = Cliente::count();
        $totalPlatillos = Platillo::where('disponible', true)->count();
        $totalSucursales = $sucursales->count();

        // Evitar duplicados con unique('id') y limitar registros
        $ultimasReservaciones = $ultimasReservacionesQuery->get()->unique('id')->values()->take(6);
        $comandasRecientes = $comandasRecientesQuery->get()->unique('id')->values()->take(6);

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
