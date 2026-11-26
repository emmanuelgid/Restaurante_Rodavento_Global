<?php

namespace App\Http\Controllers;

use App\Support\SucursalActiva;
use Illuminate\Http\Request;
use App\Models\{Reservacion, Cliente, Sucursal, Mesa, MesaSucursal, ReservacionMesa, NotaReservacion, Bitacora};
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Temporada;

class ReservacionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $sucursalId = SucursalActiva::id();

        $query = Reservacion::with([
            'cliente.nivelLealtad',
            'mesasAsignadas.mesaSucursal.mesa',
            'mesasAsignadas.mesaSucursal.sucursal',
            'nota'
        ]);

        if ($sucursalId) {
            $query->where(function ($q) use ($sucursalId) {
                $q->whereHas('mesasAsignadas.mesaSucursal', function ($sub) use ($sucursalId) {
                    $sub->where('sucursal_id', $sucursalId);
                })->orWhereHas('nota', function ($sub) use ($sucursalId) {
                    $sub->where('sucursal_id', $sucursalId);
                });
            });
        }

        if ($request->filled('estatus')) {
            $query->where('estatus', $request->estatus);
        }

        if ($request->filled('fecha')) {
            $query->whereDate('fecha_hora_inicio', $request->fecha);
        }

        $reservaciones = $query->orderBy('fecha_hora_inicio', 'desc')->paginate(10);
        $clientes = Cliente::where('activo', true)->orderBy('nombres')->get();
        $sucursales = Sucursal::where('activa', true)
            ->when($sucursalId, fn ($q) => $q->where('id', $sucursalId))
            ->get();

        return view('reservaciones.index', compact('reservaciones', 'clientes', 'sucursales', 'sucursalId'));
    }

    public function create()
    {
        $sucursalId = SucursalActiva::id();
        $clientes = Cliente::where('activo', true)->orderBy('nombres')->get();
        $sucursales = Sucursal::where('activa', true)
            ->when($sucursalId, fn ($q) => $q->where('id', $sucursalId))
            ->with('mesas.mesa')
            ->get();

        $hoy = Carbon::today();
        $hayEventoActivo = Temporada::where('fecha_inicio', '<=', $hoy)
            ->where('fecha_final', '>=', $hoy)
            ->exists();

        return view('reservaciones.create', compact('clientes', 'sucursales', 'hayEventoActivo', 'sucursalId'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'cliente_id' => 'required|exists:cliente,id',
            'fecha_hora_inicio' => 'required|date',
            'horas_duracion' => 'nullable|integer|min:0|max:8',
            'cantidad_personas' => 'required|integer|min:1|max:50',
            'mesa_sucursal_id' => 'required|exists:mesa_sucursal,id',
            'notas' => 'nullable|string',
        ]);

        $inicio = Carbon::parse($request->fecha_hora_inicio);
        $horas = (int) ($request->horas_duracion ?: 2);
        $fin = $inicio->copy()->addHours($horas);

        // 1. Validar si la mesa ya está ocupada en ese rango de tiempo
        $existeEmpalme = Reservacion::where('estatus', '!=', 'cancelada')
            ->whereHas('mesasAsignadas', function ($query) use ($request) {
                $query->where('mesa_sucursal_id', $request->mesa_sucursal_id);
            })
            ->where('fecha_hora_inicio', '<', $fin)
            ->where('fecha_hora_final', '>', $inicio)
            ->exists();

        if ($existeEmpalme) {
            return back()
                ->withInput()
                ->withErrors([
                    'mesa_sucursal_id' => 'La mesa seleccionada ya se encuentra reservada en ese horario. Por favor elige otra mesa u otro horario.'
                ]);
        }

        // 2. Si la mesa está libre, proceder a guardar
        return DB::transaction(function () use ($request, $inicio, $fin) {
            $reservacion = Reservacion::create([
                'cliente_id' => $request->cliente_id,
                'fecha_hora_inicio' => $inicio,
                'fecha_hora_final' => $fin,
                'cantidad_personas' => (int) $request->cantidad_personas,
                'estatus' => 'confirmada',
                'notas' => $request->notas,
            ]);

            $periodoRange = "[\"{$inicio->format('Y-m-d H:i:sP')}\",\"{$fin->format('Y-m-d H:i:sP')}\")";

            DB::table('cadena.reservacion_mesa')->insert([
                'reservacion_id' => $reservacion->id,
                'mesa_sucursal_id' => $request->mesa_sucursal_id,
                'periodo' => $periodoRange,
                'bloquea' => true,
            ]);

            Bitacora::create([
                'usuario_id' => Auth::id(),
                'accion' => 'CREAR_RES',
                'registro_id' => "RES_{$reservacion->id}",
                'fecha_hora' => Carbon::now(),
            ]);

            return redirect()->route('reservaciones.index')->with('success', 'Reservación creada y mesa asignada con éxito.');
        });
    }

    public function show(Reservacion $reservacion)
    {
        $reservacion->load([
            'cliente.alergias',
            'cliente.preferencias',
            'cliente.nivelLealtad',
            'mesasAsignadas.mesaSucursal.mesa',
            'mesasAsignadas.mesaSucursal.sucursal',
            'nota.pedidos.detalles.platillo'
        ]);

        return view('reservaciones.show', compact('reservacion'));
    }

    public function updateEstatus(Request $request, Reservacion $reservacion)
    {
        $request->validate([
            'estatus' => 'required|in:pendiente,confirmada,en_mesa,concluida,cancelada',
        ]);

        $reservacion->estatus = $request->estatus;
        $reservacion->save();

        if ($request->estatus === 'en_mesa' && !$reservacion->nota) {
            $mesaSuc = $reservacion->mesasAsignadas->first();
            $sucursalId = $mesaSuc ? $mesaSuc->mesaSucursal->sucursal_id : 1;

            NotaReservacion::create([
                'sucursal_id' => $sucursalId,
                'reservacion_id' => $reservacion->id,
                'empleado_id' => Auth::user()->empleado_id ?? 1,
                'subtotal' => 0,
                'descuento' => 0,
                'impuestos' => 0,
                'propina' => 0,
                'total' => 0,
                'fecha_emision' => Carbon::now(),
            ]);
        }

        Bitacora::create([
            'usuario_id' => Auth::id(),
            'accion' => 'EST_RES',
            'registro_id' => "RES_{$reservacion->id}_TO_{$request->estatus}",
            'fecha_hora' => Carbon::now(),
        ]);

        return back()->with('success', "Estado de la reservación actualizado a: {$request->estatus}.");
    }
}
