<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Reservacion, Cliente, Sucursal, Mesa, MesaSucursal, ReservacionMesa, NotaReservacion, Bitacora};
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReservacionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = Reservacion::with(['cliente.nivelLealtad', 'mesasAsignadas.mesaSucursal.mesa', 'mesasAsignadas.mesaSucursal.sucursal', 'nota']);

        if ($request->filled('estatus')) {
            $query->where('estatus', $request->estatus);
        }

        if ($request->filled('fecha')) {
            $query->whereDate('fecha_hora_inicio', $request->fecha);
        }

        $reservaciones = $query->orderBy('fecha_hora_inicio', 'desc')->paginate(10);
        $clientes = Cliente::where('activo', true)->orderBy('nombres')->get();
        $sucursales = Sucursal::where('activa', true)->get();

        return view('reservaciones.index', compact('reservaciones', 'clientes', 'sucursales'));
    }

    public function create()
    {
        $clientes = Cliente::where('activo', true)->orderBy('nombres')->get();
        $sucursales = Sucursal::where('activa', true)->with('mesas.mesa')->get();

        return view('reservaciones.create', compact('clientes', 'sucursales'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'cliente_id' => 'required|exists:cadena.cliente,id',
            'fecha_hora_inicio' => 'required|date',
            'horas_duracion' => 'nullable|integer|min:1|max:8',
            'cantidad_personas' => 'required|integer|min:1|max:50',
            'mesa_sucursal_id' => 'required|exists:cadena.mesa_sucursal,id',
            'notas' => 'nullable|string',
        ]);

        $inicio = Carbon::parse($request->fecha_hora_inicio);
        $fin = $inicio->copy()->addHours($request->horas_duracion ?? 2);

        $reservacion = Reservacion::create([
            'cliente_id' => $request->cliente_id,
            'fecha_hora_inicio' => $inicio,
            'fecha_hora_final' => $fin,
            'cantidad_personas' => $request->cantidad_personas,
            'estatus' => 'confirmada',
            'notas' => $request->notas,
        ]);

        DB::table('cadena.reservacion_mesa')->insert([
            'reservacion_id' => $reservacion->id,
            'mesa_sucursal_id' => $request->mesa_sucursal_id,
            'periodo' => "[\"{$inicio}\",\"{$fin}\")",
            'bloquea' => true,
        ]);

        Bitacora::create([
            'usuario_id' => Auth::id(),
            'accion' => 'CREAR_RES',
            'registro_id' => "RES_{$reservacion->id}",
            'fecha_hora' => Carbon::now(),
        ]);

        return redirect()->route('reservaciones.index')->with('success', 'Reservación creada y mesa asignada con éxito.');
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

        // Si pasa a 'en_mesa', crear la NotaReservacion si no existe
        if ($request->estatus === 'en_mesa' && !$reservacion->nota) {
            $mesaSuc = $reservacion->mesasAsignadas->first();
            $sucursalId = $mesaSuc ? $mesaSuc->mesaSucursal->sucursal_id : 1;

            NotaReservacion::create([
                'sucursal_id' => $sucursalId,
                'reservacion_id' => $reservacion->id,
                'empleado_id' => Auth::user()->empleado_id,
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