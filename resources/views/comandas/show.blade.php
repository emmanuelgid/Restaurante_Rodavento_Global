@extends('layouts.app')

@section('title', 'Comanda #' . $nota->id . ' - Punto de Venta')

@section('content')
<div class="container-fluid px-lg-4">
    <!-- Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3">
        <div>
            <div class="d-flex align-items-center gap-2">
                <h1 class="h3 fw-bold text-dark mb-0">Comanda #{{ $nota->id }}</h1>
                <span class="badge bg-dark">{{ $nota->sucursal->nombre ?? 'Sucursal' }}</span>
                @if($nota->reservacion && $nota->reservacion->mesasAsignadas->isNotEmpty())
                    <span class="badge bg-warning text-dark fw-bold">Mesa {{ $nota->reservacion->mesasAsignadas->first()->mesaSucursal->mesa->codigo_mesa ?? 'Mesa' }}</span>
                @endif
            </div>
            <p class="text-muted small mb-0">
                Atendido por: <strong>{{ $nota->empleado ? $nota->empleado->nombre_completo : 'Mesero' }}</strong> &bull; Emisión: {{ \Carbon\Carbon::parse($nota->fecha_emision)->format('d/m/Y H:i') }}
            </p>
        </div>
        <div class="mt-3 mt-md-0 d-flex gap-2">
            <a href="{{ route('comandas.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left"></i> Volver a Comandas
            </a>
        </div>
    </div>

    @php
        $estaCerrada = $nota->pedidos->isNotEmpty() && $nota->pedidos->every(fn($p) => $p->estatus === 'cerrado');
    @endphp

    @if($estaCerrada)
    <div class="alert alert-success d-flex justify-content-between align-items-center mb-4 p-3 shadow-sm border-0">
        <div class="d-flex align-items-center">
            <i class="bi bi-patch-check-fill fs-2 me-3 text-success"></i>
            <div>
                <h6 class="fw-bold mb-0">Cuenta Liquidada & Comanda Cerrada</h6>
                <div class="small text-muted">Total liquidado: ${{ number_format($nota->total, 2) }} MXN &bull; Propina: ${{ number_format($nota->propina, 2) }} MXN</div>
            </div>
        </div>
        <span class="badge bg-success px-3 py-2 fs-6"><i class="bi bi-check2-circle me-1"></i> LIQUIDADA</span>
    </div>
    @endif

    <!-- Advertencia de Alergias del Huésped -->
    @if($nota->reservacion && $nota->reservacion->cliente && $nota->reservacion->cliente->alergias->isNotEmpty())
    <div class="alert alert-danger shadow-sm py-2 px-3 mb-3 d-flex align-items-center">
        <i class="bi bi-shield-fill-exclamation fs-4 me-2"></i>
        <div class="small">
            <strong>ALERTA DE SEGURIDAD ALIMENTARIA:</strong> El comensal {{ $nota->reservacion->cliente->nombre_completo }} tiene alergia a:
            @foreach($nota->reservacion->cliente->alergias as $al)
                <span class="badge bg-danger ms-1">{{ $al->nombre }}</span>
            @endforeach
        </div>
    </div>
    @endif

    <div class="row g-4">
        <!-- Columna Izquierda: Ticket Actual de la Comanda -->
        <div class="col-lg-7">
            <div class="card card-custom mb-4">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-receipt me-2 text-primary"></i> Platillos en Comanda</h5>
                    <span class="badge bg-primary-subtle text-primary border border-primary">
                        {{ $nota->pedidos->count() }} Órdenes
                    </span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light small text-uppercase">
                                <tr>
                                    <th>Platillo</th>
                                    <th>Cant.</th>
                                    <th>P. Unitario</th>
                                    <th>Importe</th>
                                    <th>Estatus</th>
                                    <th>Ajuste</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $hasItems = false; @endphp
                                @foreach($nota->pedidos as $pedido)
                                    @foreach($pedido->detalles as $det)
                                        @php $hasItems = true; @endphp
                                        <tr>
                                            <td>
                                                <div class="fw-bold text-dark">{{ $det->platillo ? $det->platillo->nombre : 'Platillo' }}</div>
                                                @if($det->notas)
                                                    <small class="text-danger"><i class="bi bi-chat-dots me-1"></i>{{ $det->notas }}</small>
                                                @endif
                                                @if($det->es_cortesia)
                                                    <span class="badge bg-warning text-dark small ms-1">Cortesía de la Casa</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-dark fs-6">{{ $det->cantidad }}</span>
                                                @if($det->cantidad_cancelada > 0)
                                                    <small class="text-danger d-block">(-{{ $det->cantidad_cancelada }})</small>
                                                @endif
                                            </td>
                                            <td>${{ number_format($det->precio_unitario, 2) }}</td>
                                            <td class="fw-bold text-dark">
                                                ${{ number_format($det->subtotal, 2) }}
                                            </td>
                                            <td>
                                                <span class="badge {{ $det->estatus === 'activo' ? 'bg-success' : ($det->estatus === 'cancelado' ? 'bg-danger' : 'bg-warning text-dark') }}">
                                                    {{ $det->estatus }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($det->estatus !== 'cancelado')
                                                <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#ajusteModal{{ $det->id }}" title="Cancelar / Cortesía">
                                                    <i class="bi bi-tools"></i>
                                                </button>

                                                <!-- Modal Ajuste -->
                                                <div class="modal fade" id="ajusteModal{{ $det->id }}" tabindex="-1" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <form method="POST" action="{{ route('comandas.ajustar', $det->id) }}">
                                                                @csrf
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title fw-bold">Ajustar Platillo: {{ $det->platillo ? $det->platillo->nombre : '' }}</h5>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <div class="mb-3">
                                                                        <label class="form-label fw-semibold">Tipo de Ajuste</label>
                                                                        <select name="tipo" class="form-select" required>
                                                                            <option value="cancelacion">Cancelación de platillo</option>
                                                                            <option value="cortesia">Marcar como Cortesía (Gerencia/VIP)</option>
                                                                            <option value="merma">Merma de cocina / Error de comanda</option>
                                                                        </select>
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <label class="form-label fw-semibold">Cantidad a afectar</label>
                                                                        <input type="number" name="cantidad" class="form-control" min="1" max="{{ $det->cantidad }}" value="1" required>
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <label class="form-label fw-semibold">Motivo del Ajuste *</label>
                                                                        <input type="text" name="motivo" class="form-control" placeholder="Ej. Cambio de opinión del huésped / Plato quemado" required>
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <label class="form-label fw-semibold">Destino</label>
                                                                        <input type="text" name="destino" class="form-control" value="cocina" required>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                                                    <button type="submit" class="btn btn-danger">Confirmar Ajuste y Auditar</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                @endforeach

                                @if(!$hasItems)
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">
                                        No hay platillos ordenados aún en esta comanda. Utilice el selector de la derecha para agregar.
                                    </td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Resumen Financiero de la Nota -->
                <div class="card-footer bg-light p-4 border-top">
                    <div class="row align-items-center">
                        <div class="col-sm-6">
                            <div class="d-flex justify-content-between mb-1 small text-muted">
                                <span>Subtotal de Alimentos & Bebidas:</span>
                                <span>${{ number_format($nota->subtotal, 2) }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-1 small text-muted">
                                <span>Descuento aplicado:</span>
                                <span class="text-danger">-${{ number_format($nota->descuento, 2) }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-1 small text-muted">
                                <span>IVA (16%):</span>
                                <span>${{ number_format($nota->impuestos, 2) }}</span>
                            </div>
                            <div class="d-flex justify-content-between small text-muted">
                                <span>Propina de servicio:</span>
                                <span>${{ number_format($nota->propina, 2) }}</span>
                            </div>
                        </div>
                        <div class="col-sm-6 text-sm-end mt-3 mt-sm-0">
                            <div class="small text-uppercase text-muted fw-bold">Total Cuenta</div>
                            <div class="display-6 fw-bold text-success">${{ number_format($nota->total, 2) }}</div>
                        </div>
                    </div>
                </div>
            </div>

            @if($estaCerrada)
            <!-- Panel de Comanda Liquidada y Saldo de Puntos VIP -->
            <div class="card card-custom p-4 border-success border-2 shadow-sm mb-4">
                <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-3">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-check-circle-fill text-success fs-2 me-3"></i>
                        <div>
                            <h5 class="fw-bold mb-0 text-dark">Comanda Liquidada & Cerrada</h5>
                            <span class="small text-muted">Folio de cobro comanda #{{ $nota->id }}</span>
                        </div>
                    </div>
                    <span class="badge bg-success px-3 py-2 fs-6">COBRADA</span>
                </div>

                @if($nota->reservacion && $nota->reservacion->cliente)
                    @php $cliente = $nota->reservacion->cliente; @endphp
                    <!-- Tarjeta de Puntos VIP -->
                    <div class="p-3 mb-3 bg-light rounded border border-warning-subtle">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-bold text-dark"><i class="bi bi-award-fill text-warning me-1"></i> Programa de Lealtad Rodavento</span>
                            <span class="badge bg-dark text-warning">{{ $cliente->nivelLealtad->nombre ?? 'VIP' }}</span>
                        </div>
                        <div class="row g-2 align-items-center">
                            <div class="col-sm-6">
                                <div class="small text-muted">Huésped Titular:</div>
                                <div class="fw-bold text-dark fs-6">{{ $cliente->nombre_completo }}</div>
                            </div>
                            <div class="col-sm-6 text-sm-end">
                                <div class="small text-muted">Puntos Bonificados por esta Cuenta:</div>
                                <div class="fw-bold text-success fs-5">
                                    +{{ number_format($nota->movimientosPuntos->sum('puntos') ?: floor(($nota->subtotal - $nota->descuento) / 10)) }} pts
                                </div>
                            </div>
                            <div class="col-12 mt-2 pt-2 border-top d-flex justify-content-between align-items-center">
                                <span class="fw-bold text-primary">Saldo Total Disponible del Cliente:</span>
                                <span class="badge bg-primary fs-5 px-3 py-2">{{ number_format($cliente->puntos_saldo) }} PUNTOS VIP</span>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center pt-2">
                    <a href="{{ route('comandas.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i> Volver a Comandas
                    </a>
                    @if($nota->reservacion && $nota->reservacion->cliente)
                    <a href="{{ route('clientes.show', $nota->reservacion->cliente->id) }}" class="btn btn-rv-gold">
                        <i class="bi bi-person-badge me-1"></i> Ver Expediente del Huésped
                    </a>
                    @endif
                </div>

                <!-- Acordeón para ajustes contables si se requiere -->
                <div class="mt-4 pt-3 border-top">
                    <a class="small text-muted text-decoration-none" data-bs-toggle="collapse" href="#ajusteCobroCollapse" role="button">
                        <i class="bi bi-pencil-square me-1"></i> Modificar datos de facturación / propina (Auditoría)
                    </a>
                    <div class="collapse mt-3" id="ajusteCobroCollapse">
                        <form method="POST" action="{{ route('comandas.cerrar', $nota->id) }}">
                            @csrf
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <label class="form-label small">Descuento ($ MXN)</label>
                                    <input type="number" step="0.50" name="descuento" class="form-control form-control-sm" value="{{ $nota->descuento }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small">Propina ($ MXN)</label>
                                    <input type="number" step="0.50" name="propina" class="form-control form-control-sm" value="{{ $nota->propina }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small">Calificación</label>
                                    <select name="calificacion" class="form-select form-select-sm">
                                        <option value="5" {{ $nota->calificacion == 5 ? 'selected' : '' }}>⭐⭐⭐⭐⭐ Excelente (5)</option>
                                        <option value="4" {{ $nota->calificacion == 4 ? 'selected' : '' }}>⭐⭐⭐⭐ Muy Bueno (4)</option>
                                        <option value="3" {{ $nota->calificacion == 3 ? 'selected' : '' }}>⭐⭐⭐ Regular (3)</option>
                                    </select>
                                </div>
                                <div class="col-md-8">
                                    <label class="form-label small">Comentarios</label>
                                    <input type="text" name="comentarios" class="form-control form-control-sm" value="{{ $nota->comentarios }}">
                                </div>
                                <div class="col-12 text-end pt-2">
                                    <button type="submit" class="btn btn-sm btn-outline-success">Actualizar Cobro</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            @else
            <!-- Formulario de Cobro y Cierre -->
            <div class="card card-custom p-4">
                <h5 class="fw-bold text-dark border-bottom pb-2 mb-3"><i class="bi bi-cash-coin me-2 text-success"></i> Cerrar Cuenta y Facturar</h5>
                <form method="POST" action="{{ route('comandas.cerrar', $nota->id) }}">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Descuento ($ MXN)</label>
                            <input type="number" step="0.50" name="descuento" class="form-control" value="{{ $nota->descuento }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Propina Sugerida ($ MXN)</label>
                            <input type="number" step="0.50" name="propina" class="form-control" value="{{ $nota->propina ?: round($nota->subtotal * 0.15, 2) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Calificación del Servicio</label>
                            <select name="calificacion" class="form-select">
                                <option value="5" selected>⭐⭐⭐⭐⭐ Excelente (5)</option>
                                <option value="4">⭐⭐⭐⭐ Muy Bueno (4)</option>
                                <option value="3">⭐⭐⭐ Regular (3)</option>
                            </select>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Comentarios del Huésped</label>
                            <input type="text" name="comentarios" class="form-control" placeholder="Opinión del cliente sobre la cena...">
                        </div>
                        <div class="col-12 text-end pt-2">
                            <button type="submit" class="btn btn-success px-4 py-2 fw-semibold">
                                <i class="bi bi-check2-circle me-1"></i> Cerrar Cuenta & Bonificar Puntos VIP
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            @endif
        </div>

        <!-- Columna Derecha: Catálogo Rápido para Agregar a Comanda -->
        <div class="col-lg-5">
            <div class="card card-custom p-4">
                <h5 class="fw-bold text-dark border-bottom pb-2 mb-3"><i class="bi bi-plus-circle me-2 text-warning"></i> Enviar Orden a Cocina / Barra</h5>

                @if($estaCerrada)
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-lock-fill fs-1 text-secondary mb-2 d-block"></i>
                        <h6 class="fw-bold text-dark">Comanda Liquidada y Cerrada</h6>
                        <p class="small text-muted mb-3">No es posible agregar más órdenes a una cuenta cerrada. Si el comensal desea ordenar más productos, abra una nueva comanda.</p>
                        <a href="{{ route('comandas.create') }}" class="btn btn-sm btn-rv-primary">
                            <i class="bi bi-plus-circle me-1"></i> Abrir Nueva Comanda
                        </a>
                    </div>
                @else
                    @php $firstPedido = $nota->pedidos->first(); @endphp
                    @if($firstPedido)
                    <form method="POST" action="{{ route('comandas.agregar', $firstPedido->id) }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Seleccionar Platillo *</label>
                            <select name="platillo_id" class="form-select form-select-lg" required>
                                <option value="">Selecciona del menú...</option>
                                @foreach($platillosDisponibles as $plat)
                                    <option value="{{ $plat->id }}">
                                        [{{ $plat->categoria ? $plat->categoria->nombre : 'General' }}] {{ $plat->nombre }} — ${{ number_format($plat->precio, 2) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label fw-semibold">Cantidad *</label>
                                <input type="number" name="cantidad" class="form-control" value="1" min="1" max="20" required>
                            </div>
                            <div class="col-6 d-flex align-items-end">
                                <div class="form-check pb-2">
                                    <input class="form-check-input" type="checkbox" name="es_cortesia" value="1" id="es_cortesia">
                                    <label class="form-check-label fw-semibold small" for="es_cortesia">¿Es Cortesía?</label>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Instrucciones Especiales para Cocina</label>
                            <input type="text" name="notas" class="form-control" placeholder="Ej. Sin cebolla, término medio, aderezo aparte...">
                        </div>

                        <button type="submit" class="btn btn-rv-gold w-100 py-2 fw-semibold">
                            <i class="bi bi-send-fill me-1"></i> Agregar a Comanda
                        </button>
                    </form>
                    @endif
                @endif
            </div>
        </div>
    </div>
</div>

@if(session('cuenta_cerrada_modal'))
    @php $modalData = session('cuenta_cerrada_modal'); @endphp
    <!-- Modal Celebración de Cierre y Puntos -->
    <div class="modal fade" id="modalCuentaCerrada" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-success text-white py-3">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-check-circle-fill me-2"></i> ¡Comanda #{{ $modalData['nota_id'] }} Cerrada con Éxito!
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center p-4">
                    <div class="display-6 fw-bold text-success mb-1">
                        ${{ number_format($modalData['total'], 2) }} <span class="fs-6 text-muted">MXN</span>
                    </div>
                    <div class="text-muted small mb-3">Cuenta liquidada y facturada correctamente</div>

                    @if($modalData['cliente_nombre'])
                        <div class="p-3 bg-light rounded border border-warning my-3 text-start">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="fw-bold text-dark"><i class="bi bi-person-fill text-primary me-1"></i> {{ $modalData['cliente_nombre'] }}</span>
                                <span class="badge bg-dark text-warning">{{ $modalData['nivel_lealtad'] ?? 'VIP' }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center py-1">
                                <span class="text-muted small">Puntos ganados en esta cuenta:</span>
                                <span class="fw-bold text-success fs-5">+{{ number_format($modalData['puntos_ganados']) }} pts</span>
                            </div>
                            <hr class="my-2">
                            <div class="d-flex justify-content-between align-items-center py-1">
                                <span class="fw-bold text-primary">NUEVO SALDO DE PUNTOS:</span>
                                <span class="badge bg-primary fs-5 px-3 py-2">{{ number_format($modalData['saldo_total']) }} PUNTOS VIP</span>
                            </div>
                        </div>
                    @endif
                </div>
                <div class="modal-footer d-flex justify-content-between bg-light">
                    <a href="{{ route('comandas.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i> Ir a Comandas
                    </a>
                    @if(!empty($modalData['cliente_id']))
                    <a href="{{ route('clientes.show', $modalData['cliente_id']) }}" class="btn btn-rv-gold">
                        <i class="bi bi-person-badge me-1"></i> Ver Expediente del Huésped
                    </a>
                    @else
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Aceptar</button>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endif

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var modalEl = document.getElementById('modalCuentaCerrada');
    if (modalEl) {
        var myModal = new bootstrap.Modal(modalEl);
        myModal.show();
    }
});
</script>
@endpush
@endsection