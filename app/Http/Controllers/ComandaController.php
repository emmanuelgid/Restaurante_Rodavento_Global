<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{
    NotaReservacion, Pedido, PedidoDetalle, PedidoDetalleAjuste,
    Platillo, Reservacion, Sucursal, Empleado, MovimientoPuntos,
    ReglaPuntos, Bitacora
};
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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

        $todasNotas = $query->orderBy('id', 'desc')->get();

        $comandasPendientes = $todasNotas->filter(function ($nota) {
            return !$nota->esta_cerrada;
        })->values();

        $comandasFinalizadas = $todasNotas->filter(function ($nota) {
            return $nota->esta_cerrada;
        })->values();

        $sucursales = Sucursal::where('activa', true)->get();

        return view('comandas.index', compact(
            'comandasPendientes',
            'comandasFinalizadas',
            'todasNotas',
            'sucursales'
        ));
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
            'sucursal_id' => 'required|exists:sucursal,id',
            'reservacion_id' => 'nullable|exists:reservacion,id|unique:nota_reservacion,reservacion_id',
            'empleado_id' => 'required|exists:empleado,id',
        ]);

        return DB::transaction(function () use ($request) {
            $nota = NotaReservacion::create([
                'sucursal_id' => $request->sucursal_id,
                'reservacion_id' => $request->filled('reservacion_id') ? $request->reservacion_id : null,
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
        });
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
            'platillo_id' => 'required|exists:platillo,id',
            'cantidad' => 'required|integer|min:1|max:20',
            'tiempo_servicio' => 'nullable|string|max:50',
            'es_cortesia' => 'nullable|boolean',
            'notas' => 'nullable|string|max:250',
        ]);

        $pedido = Pedido::findOrFail($pedidoId);
        $platillo = Platillo::findOrFail($request->platillo_id);

        // Formatear nota con tiempo de servicio (ej: "[1er Tiempo] Sin cebolla")
        $tiempo = $request->input('tiempo_servicio', '1er Tiempo');
        $notasTexto = trim($request->input('notas', ''));
        $notaFinal = '[' . $tiempo . ']' . ($notasTexto !== '' ? ' ' . $notasTexto : '');
        $esCortesia = $request->has('es_cortesia');

        // Buscar si ya existe el mismo platillo con el mismo tiempo, notas y estado de cortesía
        $detalleExistente = PedidoDetalle::where('pedido_id', $pedido->id)
            ->where('platillo_id', $platillo->id)
            ->where('es_cortesia', $esCortesia)
            ->where('notas', $notaFinal)
            ->first();

        if ($detalleExistente) {
            // Si ya existe exactamente igual, acumular la cantidad
            $detalleExistente->cantidad += $request->cantidad;
            $detalleExistente->save();
        } else {
            // Si no existe o difiere en instrucciones/cortesía, crear nuevo renglón
            PedidoDetalle::create([
                'pedido_id' => $pedido->id,
                'platillo_id' => $platillo->id,
                'cantidad' => $request->cantidad,
                'cantidad_cancelada' => 0,
                'precio_unitario' => $platillo->precio,
                'es_cortesia' => $esCortesia,
                'notas' => $notaFinal,
            ]);
        }

        $this->recalcularTotales($pedido->nota_reservacion_id);

        return back()->with('success', "Se agregó {$request->cantidad}x {$platillo->nombre} ({$tiempo}) a la comanda.");
    }

    public function ajustarPlatillo(Request $request, $detalleId)
    {
        $detalle = PedidoDetalle::with('pedido.nota')->findOrFail($detalleId);

        // Validar si la comanda o el pedido ya están cerrados
        if ($detalle->pedido->estatus === 'cerrado' || $detalle->pedido->nota->esta_cerrada) {
            return back()->with('error', 'Seguridad: No se pueden realizar ajustes a platillos de una comanda liquidada.');
        }

        $request->validate([
            'tipo' => 'required|in:cancelacion,merma,cortesia,cambio',
            'cantidad' => 'required|integer|min:1',
            'motivo' => 'required|string|max:250',
            'destino' => 'required|string|max:15',
        ]);

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
        if ($request->isMethod('get')) {
            return redirect()->route('comandas.show', $notaId);
        }

        $request->validate([
            'descuento' => 'nullable|numeric|min:0',
            'propina' => 'nullable|numeric|min:0',
            'calificacion' => 'nullable|integer|min:1|max:5',
            'comentarios' => 'nullable|string',
        ]);

        return DB::transaction(function () use ($request, $notaId) {
            $nota = NotaReservacion::with('pedidos.detalles', 'reservacion.cliente')->findOrFail($notaId);

            // Calcular subtotal únicamente de platillos activos y que no sean cortesía
            $subtotal = 0;
            foreach ($nota->pedidos as $ped) {
                foreach ($ped->detalles as $det) {
                    if ($det->estatus === 'activo' && !$det->es_cortesia) {
                        $cantEfectiva = max(0, $det->cantidad - $det->cantidad_cancelada);
                        $subtotal += ($cantEfectiva * $det->precio_unitario);
                    }
                }
            }

            // Obtener el descuento procesado
            $descuento = (float) ($request->descuento ?? 0);
            $base = max(0, $subtotal - $descuento);
            $impuestos = round($base * 0.16, 2);
            $propina = (float) ($request->propina ?? 0);
            $total = $base + $impuestos + $propina;

            // Guardar valores recalculados
            $nota->update([
                'subtotal' => $subtotal,
                'descuento' => $descuento,
                'impuestos' => $impuestos,
                'propina' => $propina,
                'total' => $total,
                'calificacion' => $request->calificacion,
                'comentarios' => $request->comentarios,
            ]);

            // Cerrar los pedidos de la comanda
            foreach ($nota->pedidos as $ped) {
                $ped->estatus = 'cerrado';
                $ped->save();
            }

            if ($nota->reservacion) {
                $nota->reservacion->estatus = 'finalizada';
                $nota->reservacion->save();
            }

            // Lógica de puntos VIP...
            $puntosGanados = 0;
            $saldoTotal = 0;
            $clienteNombre = null;
            $clienteId = null;
            $nivelNombre = null;

            if ($nota->reservacion && $nota->reservacion->cliente) {
                $cliente = $nota->reservacion->cliente;
                $clienteNombre = $cliente->nombre_completo;
                $clienteId = $cliente->id;
                $nivelNombre = $cliente->nivelLealtad ? $cliente->nivelLealtad->nombre : 'Estándar';

                $regla = ReglaPuntos::where('activa', true)->latest('vigencia_inicio')->first();
                $montoPorPunto = $regla ? (float) $regla->monto_por_punto : 10.0;
                $puntosGanados = (int) floor($base / max(1, $montoPorPunto));

                $movimientoExistente = MovimientoPuntos::where('nota_reservacion_id', $nota->id)->first();
                if (!$movimientoExistente) {
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
                } else {
                    $puntosGanados = $movimientoExistente->puntos;
                }

                $saldoTotal = $cliente->puntos_saldo;
            }

            Bitacora::create([
                'usuario_id' => Auth::id(),
                'accion' => 'COBRO_NOTA',
                'registro_id' => "NOTA_{$nota->id}_TOTAL_{$total}",
                'fecha_hora' => Carbon::now(),
            ]);

            $successMsg = "Cuenta cerrada exitosamente por $" . number_format($total, 2);
            if ($clienteNombre) {
                $successMsg .= " • Se acreditaron +{$puntosGanados} puntos a {$clienteNombre}. Saldo actual: " . number_format($saldoTotal) . " puntos (Nivel {$nivelNombre}).";
            }

            return redirect()->route('comandas.show', $nota->id)
                ->with('success', $successMsg)
                ->with('cuenta_cerrada_modal', [
                    'nota_id' => $nota->id,
                    'total' => $total,
                    'descuento' => $descuento,
                    'propina' => $propina,
                    'cliente_nombre' => $clienteNombre,
                    'cliente_id' => $clienteId,
                    'puntos_ganados' => $puntosGanados,
                    'saldo_total' => $saldoTotal,
                    'nivel_lealtad' => $nivelNombre,
                ]);
        });
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