<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{
    NotaReservacion, Pedido, PedidoDetalle, PedidoDetalleAjuste,
    Platillo, Reservacion, Sucursal, Empleado, MovimientoPuntos,
    ReglaPuntos, Bitacora
};
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ComandaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = NotaReservacion::with([
            'sucursal',
            'reservacion.cliente',
            'reservacion.mesasAsignadas.mesaSucursal.mesa',
            'empleado',
            'pedidos.detalles.platillo'
        ]);

        if ($request->filled('sucursal_id')) {
            $query->where('sucursal_id', $request->sucursal_id);
        }

        $notas = $query->orderBy('id', 'desc')->paginate(10);
        $sucursales = Sucursal::where('activa', true)->get();

        return view('comandas.index', compact('notas', 'sucursales'));
    }

    public function create()
    {
        $sucursales = Sucursal::where('activa', true)->get();
        $reservaciones = Reservacion::whereIn('estatus', ['confirmada', 'en_mesa'])
            ->with('cliente')
            ->get();
        $empleados = Empleado::where('estatus', 'activo')->get();

        return view('comandas.create', compact('sucursales', 'reservaciones', 'empleados'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'sucursal_id' => 'required|exists:cadena.sucursal,id',
            'reservacion_id' => 'nullable|exists:cadena.reservacion,id|unique:cadena.nota_reservacion,reservacion_id',
            'empleado_id' => 'required|exists:cadena.empleado,id',
        ]);

        $nota = NotaReservacion::create([
            'sucursal_id' => $request->sucursal_id,
            'reservacion_id' => $request->reservacion_id,
            'empleado_id' => $request->empleado_id,
            'subtotal' => 0,
            'descuento' => 0,
            'impuestos' => 0,
            'propina' => 0,
            'total' => 0,
            'fecha_emision' => Carbon::now(),
        ]);

        Pedido::create([
            'nota_reservacion_id' => $nota->id,
            'estatus' => 'abierto',
            'fecha_hora' => Carbon::now(),
        ]);

        Bitacora::create([
            'usuario_id' => Auth::id(),
            'accion' => 'CREAR_NOTA',
            'registro_id' => "NOTA_{$nota->id}",
            'fecha_hora' => Carbon::now(),
        ]);

        return redirect()->route('comandas.show', $nota->id)->with('success', 'Comanda abierta correctamente.');
    }

    public function show($id)
    {
        $nota = NotaReservacion::with([
            'sucursal',
            'reservacion.cliente.nivelLealtad',
            'reservacion.cliente.alergias',
            'reservacion.mesasAsignadas.mesaSucursal.mesa',
            'empleado',
            'pedidos.detalles.platillo.categoria',
            'pedidos.detalles.ajustes'
        ])->findOrFail($id);

        $platillosDisponibles = Platillo::where('disponible', true)
            ->where('estatus', 'activo')
            ->orderBy('categoria_id')
            ->orderBy('nombre')
            ->get();

        return view('comandas.show', compact('nota', 'platillosDisponibles'));
    }

    public function agregarPlatillo(Request $request, $pedidoId)
    {
        $request->validate([
            'platillo_id' => 'required|exists:cadena.platillo,id',
            'cantidad' => 'required|integer|min:1|max:20',
            'es_cortesia' => 'nullable|boolean',
            'notas' => 'nullable|string|max:250',
        ]);

        $pedido = Pedido::findOrFail($pedidoId);
        $platillo = Platillo::findOrFail($request->platillo_id);

        PedidoDetalle::create([
            'pedido_id' => $pedido->id,
            'platillo_id' => $platillo->id,
            'cantidad' => $request->cantidad,
            'cantidad_cancelada' => 0,
            'precio_unitario' => $platillo->precio,
            'es_cortesia' => $request->has('es_cortesia'),
            'notas' => $request->notas,
        ]);

        $this->recalcularTotales($pedido->nota_reservacion_id);

        return back()->with('success', "Se agregó {$request->cantidad}x {$platillo->nombre} a la comanda.");
    }

    public function ajustarPlatillo(Request $request, $detalleId)
    {
        $request->validate([
            'tipo' => 'required|in:cancelacion,merma,cortesia,cambio',
            'cantidad' => 'required|integer|min:1',
            'motivo' => 'required|string|max:250',
            'destino' => 'required|string|max:15',
        ]);

        $detalle = PedidoDetalle::findOrFail($detalleId);

        PedidoDetalleAjuste::create([
            'pedido_detalle_id' => $detalle->id,
            'tipo' => $request->tipo,
            'cantidad' => $request->cantidad,
            'motivo' => $request->motivo,
            'destino' => $request->destino,
            'usuario_id' => Auth::id(),
            'fecha_hora' => Carbon::now(),
        ]);

        if ($request->tipo === 'cancelacion') {
            $detalle->cantidad_cancelada = min($detalle->cantidad, $detalle->cantidad_cancelada + $request->cantidad);
            $detalle->save();
        } elseif ($request->tipo === 'cortesia') {
            $detalle->es_cortesia = true;
            $detalle->save();
        }

        $pedido = Pedido::find($detalle->pedido_id);
        if ($pedido) {
            $this->recalcularTotales($pedido->nota_reservacion_id);
        }

        return back()->with('success', 'Ajuste de comanda registrado con auditoría.');
    }

    public function cerrarCuenta(Request $request, $notaId)
    {
        $request->validate([
            'descuento' => 'nullable|numeric|min:0',
            'propina' => 'nullable|numeric|min:0',
            'calificacion' => 'nullable|integer|min:1|max:5',
            'comentarios' => 'nullable|string',
        ]);

        $nota = NotaReservacion::with('pedidos.detalles')->findOrFail($notaId);

        $subtotal = 0;
        foreach ($nota->pedidos as $ped) {
            foreach ($ped->detalles as $det) {
                $subtotal += $det->subtotal;
            }
        }

        $descuento = (float) ($request->descuento ?? 0);
        $base = max(0, $subtotal - $descuento);
        $impuestos = round($base * 0.16, 2);
        $propina = (float) ($request->propina ?? 0);
        $total = $base + $impuestos + $propina;

        $nota->update([
            'subtotal' => $subtotal,
            'descuento' => $descuento,
            'impuestos' => $impuestos,
            'propina' => $propina,
            'total' => $total,
            'calificacion' => $request->calificacion,
            'comentarios' => $request->comentarios,
        ]);

        // Cerrar los pedidos
        foreach ($nota->pedidos as $ped) {
            $ped->estatus = 'cerrado';
            $ped->save();
        }

        // Si hay reservación y cliente, abonar puntos de lealtad
        if ($nota->reservacion && $nota->reservacion->cliente) {
            $cliente = $nota->reservacion->cliente;
            $regla = ReglaPuntos::where('activa', true)->latest('vigencia_inicio')->first();
            $montoPorPunto = $regla ? (float) $regla->monto_por_punto : 10.0;
            $puntosGanados = (int) floor($base / max(1, $montoPorPunto));

            if ($puntosGanados > 0) {
                MovimientoPuntos::create([
                    'cliente_id' => $cliente->id,
                    'nota_reservacion_id' => $nota->id,
                    'regla_id' => $regla ? $regla->id : null,
                    'puntos' => $puntosGanados,
                    'tipo' => 'acumulacion',
                    'fecha' => Carbon::now(),
                ]);

                $cliente->puntos_saldo += $puntosGanados;
                $cliente->puntos_acumulados += $puntosGanados;
                $cliente->save();
            }
        }

        Bitacora::create([
            'usuario_id' => Auth::id(),
            'accion' => 'COBRO_NOTA',
            'registro_id' => "NOTA_{$nota->id}_TOTAL_{$total}",
            'fecha_hora' => Carbon::now(),
        ]);

        return redirect()->route('comandas.show', $nota->id)->with('success', "Cuenta cerrada exitosamente por un total de $" . number_format($total, 2));
    }

    private function recalcularTotales($notaId)
    {
        $nota = NotaReservacion::with('pedidos.detalles')->find($notaId);
        if (!$nota) return;

        $subtotal = 0;
        foreach ($nota->pedidos as $ped) {
            foreach ($ped->detalles as $det) {
                $subtotal += $det->subtotal;
            }
        }

        $descuento = (float) $nota->descuento;
        $base = max(0, $subtotal - $descuento);
        $impuestos = round($base * 0.16, 2);
        $total = $base + $impuestos + (float) $nota->propina;

        $nota->update([
            'subtotal' => $subtotal,
            'impuestos' => $impuestos,
            'total' => $total,
        ]);
    }
}