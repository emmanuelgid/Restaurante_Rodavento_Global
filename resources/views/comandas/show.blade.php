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
                Atendido por: <strong>{{ $nota->empleado ? $nota->empleado->nombre_completo : 'Mesero' }}</strong> • Emisión: {{ \Carbon\Carbon::parse($nota->fecha_emision)->format('d/m/Y H:i') }}
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
                <div class="small text-muted">Total liquidado: ${{ number_format($nota->total, 2) }} MXN • Propina: ${{ number_format($nota->propina, 2) }} MXN</div>
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
                                    <th>Platillo / Instrucción</th>
                                    <th>Cant.</th>
                                    <th>P. Unitario</th>
                                    <th>Importe</th>
                                    <th>Estatus</th>
                                    @if(!$estaCerrada)
                                        <th>Ajuste</th>
                                    @else
                                        <th>Auditoría</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $todosDetalles = $nota->pedidos->flatMap->detalles;
                                @endphp

                                @if($todosDetalles->isNotEmpty())
                                    @php
                                        // Extraer tiempo de servicio y agrupar platillos
                                        $detallesProcesados = $todosDetalles->map(function($det) {
                                            $notaTexto = trim($det->notas ?? '');
                                            $tiempo = '1er Tiempo';
                                            $instrucciones = $notaTexto;

                                            if (preg_match('/^\[(.*?)\]\s*(.*)$/', $notaTexto, $matches)) {
                                                $tiempo = $matches[1];
                                                $instrucciones = $matches[2];
                                            }

                                            $det->tiempo_parsed = $tiempo;
                                            $det->instrucciones_parsed = $instrucciones;
                                            return $det;
                                        });

                                        // Agrupar por Tiempo de Servicio
                                        $gruposPorTiempo = $detallesProcesados->groupBy('tiempo_parsed');
                                    @endphp

                                    @foreach($gruposPorTiempo as $tiempo => $detalles)
                                        @php
                                            $badgeColor = match(strtolower($tiempo)) {
                                                '1er tiempo' => 'bg-primary',
                                                '2do tiempo' => 'bg-info text-dark',
                                                'postre', 'postres' => 'bg-purple text-white',
                                                'barra', 'bebidas' => 'bg-warning text-dark',
                                                default => 'bg-dark'
                                            };
                                        @endphp
                                        <tr class="table-light border-top border-bottom">
                                            <td colspan="6" class="py-2 px-3 fw-bold bg-body-tertiary">
                                                <span class="badge {{ $badgeColor }} px-2 py-1 me-2 fs-6">
                                                    <i class="bi bi-clock-history me-1"></i>{{ $tiempo }}
                                                </span>
                                            </td>
                                        </tr>

                                        @foreach($detalles as $det)
                                            <tr>
                                                <td class="ps-4">
                                                    <div class="fw-bold text-dark">{{ $det->platillo ? $det->platillo->nombre : 'Platillo' }}</div>

                                                    @php
                                                        $ajusteCancelacion = $det->ajustes ? $det->ajustes->whereIn('tipo', ['cancelacion', 'merma'])->last() : null;
                                                    @endphp

                                                    @if(($det->estatus === 'cancelado' || $det->cantidad_cancelada > 0) && $ajusteCancelacion)
                                                        <small class="text-danger fw-semibold d-block mt-1">
                                                            <i class="bi bi-x-circle-fill me-1"></i>Motivo cancelación: {{ $ajusteCancelacion->motivo }}
                                                        </small>
                                                    @endif

                                                    @if($det->instrucciones_parsed)
                                                        <small class="text-danger d-block mt-1">
                                                            <i class="bi bi-chat-left-dots-fill me-1"></i>{{ $det->instrucciones_parsed }}
                                                        </small>
                                                    @endif

                                                    @if($det->es_cortesia)
                                                        <span class="badge bg-warning text-dark small mt-1">Cortesía de la Casa</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="badge bg-dark fs-6">{{ $det->cantidad }}</span>
                                                    @if($det->cantidad_cancelada > 0)
                                                        <small class="text-danger d-block">(-{{ $det->cantidad_cancelada }})</small>
                                                    @endif
                                                </td>
                                                <td>${{ number_format($det->precio_unitario, 2) }}</td>
                                                <td class="fw-bold text-dark">${{ number_format($det->subtotal, 2) }}</td>
                                                <td>
                                                    <span class="badge {{ $det->estatus === 'activo' ? 'bg-success' : ($det->estatus === 'cancelado' ? 'bg-danger' : 'bg-warning text-dark') }}">
                                                        {{ $det->estatus }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @if(!$estaCerrada)
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
                                                    @else
                                                        <span class="badge bg-light text-muted border py-1 px-2" title="Cuenta liquidada. Sin modificaciones permitidas.">
                                                            <i class="bi bi-lock-fill me-1"></i>Cerrado
                                                        </span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endforeach
                                @else
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
                            <div class="row g-3">
                                <!-- Bloque de Descuento con Botones de Porcentaje -->
                                <div class="col-md-6 contenedor-descuento" data-subtotal="{{ $nota->subtotal ?? 0 }}">
                                    <label class="form-label small fw-semibold">Descuento Aplicado</label>
                                    
                                    <div class="d-flex gap-2 mb-2">
                                        <button type="button" class="btn btn-outline-secondary btn-sm flex-fill btn-descuento-pct" data-pct="10">10%</button>
                                        <button type="button" class="btn btn-outline-secondary btn-sm flex-fill btn-descuento-pct" data-pct="20">20%</button>
                                        <button type="button" class="btn btn-outline-secondary btn-sm flex-fill btn-descuento-pct" data-pct="30">30%</button>
                                        <button type="button" class="btn btn-outline-secondary btn-sm flex-fill btn-descuento-pct" data-pct="40">40%</button>
                                    </div>

                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text bg-light text-muted small">%</span>
                                        <input type="number" class="form-control input-pct-descuento" placeholder="Ej. 17" min="0" max="100" step="1">
                                        
                                        <span class="input-group-text bg-light text-muted small">$ MXN</span>
                                        <input type="number" step="any" name="descuento" class="form-control fw-bold text-danger input-monto-descuento" value="{{ $nota->descuento }}" placeholder="0.00">
                                    </div>
                                    <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">Selecciona %, escribe % libre o ingresa el monto en pesos.</small>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold">Propina ($ MXN)</label>
                                    <input type="number" step="any" name="propina" class="form-control form-control-sm" value="{{ $nota->propina }}">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label small fw-semibold">Calificación</label>
                                    <select name="calificacion" class="form-select form-select-sm">
                                        <option value="5" {{ $nota->calificacion == 5 ? 'selected' : '' }}>⭐⭐⭐⭐⭐ Excelente (5)</option>
                                        <option value="4" {{ $nota->calificacion == 4 ? 'selected' : '' }}>⭐⭐⭐⭐ Muy Bueno (4)</option>
                                        <option value="3" {{ $nota->calificacion == 3 ? 'selected' : '' }}>⭐⭐⭐ Regular (3)</option>
                                        <option value="2" {{ $nota->calificacion == 2 ? 'selected' : '' }}>⭐⭐ Malo (2)</option>
                                        <option value="1" {{ $nota->calificacion == 1 ? 'selected' : '' }}>⭐ Pésimo (1)</option>
                                    </select>
                                </div>

                                <div class="col-md-8">
                                    <label class="form-label small fw-semibold">Comentarios</label>
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
                        <div class="col-md-6 contenedor-descuento" data-subtotal="{{ $nota->subtotal ?? 0 }}">
                            <label class="form-label fw-semibold">Descuento Aplicado</label>
                            
                            <div class="d-flex gap-2 mb-2">
                                <button type="button" class="btn btn-outline-secondary btn-sm flex-fill btn-descuento-pct" data-pct="10">10%</button>
                                <button type="button" class="btn btn-outline-secondary btn-sm flex-fill btn-descuento-pct" data-pct="20">20%</button>
                                <button type="button" class="btn btn-outline-secondary btn-sm flex-fill btn-descuento-pct" data-pct="30">30%</button>
                                <button type="button" class="btn btn-outline-secondary btn-sm flex-fill btn-descuento-pct" data-pct="40">40%</button>
                            </div>

                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted small">%</span>
                                <input type="number" class="form-control input-pct-descuento" placeholder="Ej. 17" min="0" max="100" step="1">
                                
                                <span class="input-group-text bg-light text-muted small">$ MXN</span>
                                <input type="number" step="any" name="descuento" class="form-control fw-bold text-danger input-monto-descuento" value="{{ $nota->descuento }}" placeholder="0.00">
                            </div>
                            <small class="text-muted d-block mt-1">Selecciona un porcentaje, escribe uno libre o ingresa el monto en pesos.</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Propina Sugerida ($ MXN)</label>
                            <input type="number" step="any" name="propina" class="form-control" value="{{ $nota->propina ?: round($nota->subtotal * 0.15, 2) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Calificación del Servicio</label>
                            <select name="calificacion" class="form-select">
                                <option value="5" {{ (isset($nota) && $nota->calificacion == 5) || !isset($nota) ? 'selected' : '' }}>⭐⭐⭐⭐⭐ Excelente (5)</option>
                                <option value="4" {{ isset($nota) && $nota->calificacion == 4 ? 'selected' : '' }}>⭐⭐⭐⭐ Muy Bueno (4)</option>
                                <option value="3" {{ isset($nota) && $nota->calificacion == 3 ? 'selected' : '' }}>⭐⭐⭐ Regular (3)</option>
                                <option value="2" {{ isset($nota) && $nota->calificacion == 2 ? 'selected' : '' }}>⭐⭐ Malo (2)</option>
                                <option value="1" {{ isset($nota) && $nota->calificacion == 1 ? 'selected' : '' }}>⭐ Pésimo (1)</option>
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

        <!-- Columna Derecha: Armar Orden Múltiple para Cocina -->
        <div class="col-lg-5">
            <div class="card card-custom p-4 shadow-sm">
                <h5 class="fw-bold text-dark border-bottom pb-2 mb-3"><i class="bi bi-plus-circle me-2 text-warning"></i> Armar Orden para Cocina / Barra</h5>

                @if($estaCerrada)
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-lock-fill fs-1 text-secondary mb-2 d-block"></i>
                        <h6 class="fw-bold text-dark">Comanda Liquidada y Cerrada</h6>
                        <p class="small text-muted mb-3">No es posible agregar más órdenes a una cuenta cerrada.</p>
                    </div>
                @else
                    @php $firstPedido = $nota->pedidos->first(); @endphp
                    @if($firstPedido)
                    <form method="POST" action="{{ route('comandas.agregar', $firstPedido->id) }}" id="formEnviarComanda">
                        @csrf
                        
                        <!-- Seleccionar Platillo -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Seleccionar Platillo *</label>
                            <div class="input-group mb-2">
                                <span class="input-group-text bg-white text-muted border-end-0">
                                    <i class="bi bi-search"></i>
                                </span>
                                <input type="text" id="buscarPlatillo" class="form-control border-start-0 ps-0" placeholder="Buscar por nombre o categoría...">
                            </div>

                            <select id="selectPlatillo" class="form-select form-select-lg">
                                <option value="" disabled selected hidden>Selecciona del menú...</option>
                                @foreach($platillosDisponibles as $plat)
                                    <option value="{{ $plat->id }}" data-nombre="{{ $plat->nombre }}" data-precio="{{ $plat->precio }}">
                                        [{{ $plat->categoria ? $plat->categoria->nombre : 'General' }}] {{ $plat->nombre }} — ${{ number_format($plat->precio, 2) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Cantidad y Cortesía -->
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label fw-semibold">Cantidad *</label>
                                <input type="number" id="inputCantidad" class="form-control" value="1" min="1" max="20">
                            </div>
                            <div class="col-6 d-flex align-items-end">
                                <div class="form-check pb-2">
                                    <input class="form-check-input" type="checkbox" id="checkCortesia">
                                    <label class="form-check-label fw-semibold small" for="checkCortesia">¿Es Cortesía?</label>
                                </div>
                            </div>
                        </div>

                        <!-- Tiempo de Servicio -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold d-block">Tiempo de Servicio / Marcha *</label>
                            <div class="btn-group w-100" role="group">
                                <input type="radio" class="btn-check" name="tiempo_servicio_opt" id="t1" value="1er Tiempo" checked>
                                <label class="btn btn-outline-secondary btn-sm" for="t1">1er Tiempo</label>

                                <input type="radio" class="btn-check" name="tiempo_servicio_opt" id="t2" value="2do Tiempo">
                                <label class="btn btn-outline-secondary btn-sm" for="t2">2do Tiempo</label>

                                <input type="radio" class="btn-check" name="tiempo_servicio_opt" id="t3" value="Postre">
                                <label class="btn btn-outline-secondary btn-sm" for="t3">Postre</label>

                                <input type="radio" class="btn-check" name="tiempo_servicio_opt" id="t4" value="Barra">
                                <label class="btn btn-outline-secondary btn-sm" for="t4">Barra</label>
                            </div>
                        </div>

                        <!-- Instrucciones Especiales -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Instrucciones Especiales</label>
                            <input type="text" id="inputNotas" class="form-control" placeholder="Ej. Sin cebolla, término medio...">
                        </div>

                        <!-- Botón para añadir a la pre-lista -->
                        <button type="button" id="btnAgregarAPrelista" class="btn btn-outline-dark w-100 mb-3 fw-semibold">
                            <i class="bi bi-plus-lg me-1"></i> Añadir Platillo a la Orden
                        </button>

                        <!-- Pre-lista de Platillos antes de Enviar -->
                        <div class="border rounded p-3 mb-3 bg-light" id="boxPrelista" style="display: none;">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="fw-bold mb-0 text-dark small text-uppercase"><i class="bi bi-list-check me-1"></i> Pre-Lista por Enviar</h6>
                                <span class="badge bg-primary" id="badgeCantidadItems">0 ítems</span>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered align-middle mb-0 bg-white">
                                    <thead class="table-light extra-small text-muted">
                                        <tr>
                                            <th>Tiempo</th>
                                            <th>Platillo</th>
                                            <th class="text-center">Cant.</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbodyPrelista"></tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Input oculto que envía el JSON al controlador -->
                        <input type="hidden" name="items" id="itemsInputJson">

                        <!-- Botón Submit General para Cocina -->
                        <button type="submit" id="btnEnviarTodoCocina" class="btn btn-rv-gold w-100 py-2 fw-semibold" disabled>
                            <i class="bi bi-send-fill me-1"></i> Enviar Comanda Completa a Cocina
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
    // 1. Mostrar modal de celebración/cierre si existe en la sesión
    const modalEl = document.getElementById('modalCuentaCerrada');
    if (modalEl) {
        const myModal = new bootstrap.Modal(modalEl);
        myModal.show();
    }

    // 2. Lógica para Pre-Lista de Platillos en Comanda (Carrito Múltiple)
    let preLista = [];

    const selectPlatillo = document.getElementById('selectPlatillo');
    const inputCantidad = document.getElementById('inputCantidad');
    const checkCortesia = document.getElementById('checkCortesia');
    const inputNotas = document.getElementById('inputNotas');
    const btnAgregarAPrelista = document.getElementById('btnAgregarAPrelista');
    const boxPrelista = document.getElementById('boxPrelista');
    const tbodyPrelista = document.getElementById('tbodyPrelista');
    const badgeCantidadItems = document.getElementById('badgeCantidadItems');
    const itemsInputJson = document.getElementById('itemsInputJson');
    const btnEnviarTodoCocina = document.getElementById('btnEnviarTodoCocina');

    if (btnAgregarAPrelista) {
        btnAgregarAPrelista.addEventListener('click', function () {
            const optionSeleccionada = selectPlatillo.options[selectPlatillo.selectedIndex];
            
            if (!selectPlatillo.value || optionSeleccionada.disabled) {
                alert('Por favor selecciona un platillo del menú.');
                return;
            }

            const tiempoRadio = document.querySelector('input[name="tiempo_servicio_opt"]:checked');
            const tiempo = tiempoRadio ? tiempoRadio.value : '1er Tiempo';

            const item = {
                platillo_id: selectPlatillo.value,
                nombre: optionSeleccionada.getAttribute('data-nombre') || optionSeleccionada.text,
                cantidad: parseInt(inputCantidad.value) || 1,
                es_cortesia: checkCortesia.checked ? 1 : 0,
                tiempo_servicio: tiempo,
                notas: inputNotas.value.trim()
            };

            preLista.push(item);
            actualizarRenderPrelista();

            // Limpiar selección
            inputNotas.value = '';
            inputCantidad.value = 1;
            checkCortesia.checked = false;
            selectPlatillo.selectedIndex = 0;
        });
    }

    function actualizarRenderPrelista() {
        tbodyPrelista.innerHTML = '';

        if (preLista.length === 0) {
            boxPrelista.style.display = 'none';
            btnEnviarTodoCocina.disabled = true;
            itemsInputJson.value = '';
            return;
        }

        boxPrelista.style.display = 'block';
        btnEnviarTodoCocina.disabled = false;
        badgeCantidadItems.textContent = `${preLista.length} ítem(s)`;

        preLista.forEach((item, index) => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td><span class="badge bg-secondary extra-small">${item.tiempo_servicio}</span></td>
                <td>
                    <div class="fw-bold extra-small text-dark">${item.nombre}</div>
                    ${item.notas ? `<small class="text-muted d-block" style="font-size: 0.7rem;">${item.notas}</small>` : ''}
                    ${item.es_cortesia ? `<span class="badge bg-warning text-dark extra-small">Cortesía</span>` : ''}
                </td>
                <td class="text-center font-semibold">${item.cantidad}</td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-link text-danger p-0 ms-1 btn-eliminar-item" data-index="${index}">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            `;
            tbodyPrelista.appendChild(tr);
        });

        itemsInputJson.value = JSON.stringify(preLista);

        document.querySelectorAll('.btn-eliminar-item').forEach(btn => {
            btn.addEventListener('click', function () {
                const idx = parseInt(this.getAttribute('data-index'));
                preLista.splice(idx, 1);
                actualizarRenderPrelista();
            });
        });
    }

    // 3. Buscador rápido de platillos en el menú
    const inputBuscar = document.getElementById('buscarPlatillo');
    if (inputBuscar && selectPlatillo) {
        const opcionesOriginales = Array.from(selectPlatillo.options);

        inputBuscar.addEventListener('input', function () {
            const busqueda = this.value.toLowerCase().trim();

            selectPlatillo.innerHTML = '';
            
            opcionesOriginales.forEach(function (opcion) {
                if (opcion.value === '') {
                    selectPlatillo.appendChild(opcion);
                    return;
                }

                if (opcion.text.toLowerCase().includes(busqueda)) {
                    selectPlatillo.appendChild(opcion);
                }
            });

            if (busqueda !== '' && selectPlatillo.options.length > 1) {
                selectPlatillo.selectedIndex = 1;
            } else {
                selectPlatillo.selectedIndex = 0;
            }
        });
    }

    // 4. Calculadora y botones rápidos de porcentaje de descuento
    document.querySelectorAll('.contenedor-descuento').forEach(function (contenedor) {
        const subtotal = parseFloat(contenedor.dataset.subtotal || 0);
        const inputPct = contenedor.querySelector('.input-pct-descuento');
        const inputMonto = contenedor.querySelector('.input-monto-descuento');
        const botonesPct = contenedor.querySelectorAll('.btn-descuento-pct');

        if (subtotal > 0 && inputMonto && parseFloat(inputMonto.value) > 0) {
            const pctInicial = Math.round((parseFloat(inputMonto.value) / subtotal) * 100);
            if (inputPct) inputPct.value = pctInicial;

            botonesPct.forEach(b => {
                if (parseInt(b.getAttribute('data-pct')) === pctInicial) {
                    b.classList.replace('btn-outline-secondary', 'btn-primary');
                }
            });
        }

        botonesPct.forEach(function (btn) {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                const pct = parseFloat(this.getAttribute('data-pct'));

                botonesPct.forEach(b => b.classList.replace('btn-primary', 'btn-outline-secondary'));
                this.classList.replace('btn-outline-secondary', 'btn-primary');

                if (inputPct) inputPct.value = pct;
                if (inputMonto) {
                    inputMonto.value = (subtotal * (pct / 100)).toFixed(2);
                }
            });
        });

        if (inputPct) {
            inputPct.addEventListener('input', function () {
                const pct = parseFloat(this.value) || 0;

                botonesPct.forEach(b => {
                    if (parseInt(b.getAttribute('data-pct')) === pct) {
                        b.classList.replace('btn-outline-secondary', 'btn-primary');
                    } else {
                        b.classList.replace('btn-primary', 'btn-outline-secondary');
                    }
                });

                if (inputMonto) {
                    inputMonto.value = (subtotal * (pct / 100)).toFixed(2);
                }
            });
        }

        if (inputMonto) {
            inputMonto.addEventListener('input', function () {
                const monto = parseFloat(this.value) || 0;
                const pctCalculado = subtotal > 0 ? Math.round((monto / subtotal) * 100) : 0;

                if (inputPct) inputPct.value = pctCalculado;

                botonesPct.forEach(b => {
                    if (parseInt(b.getAttribute('data-pct')) === pctCalculado) {
                        b.classList.replace('btn-outline-secondary', 'btn-primary');
                    } else {
                        b.classList.replace('btn-primary', 'btn-outline-secondary');
                    }
                });
            });
        }
    });
});
</script>
@endpush
@endsection