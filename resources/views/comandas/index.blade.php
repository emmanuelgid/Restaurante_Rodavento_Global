@extends('layouts.app')

@section('title', 'Comandas & Terminal POS')

@section('content')
<div class="container-fluid px-lg-4">
    <!-- Encabezado Principal -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
        <div>
            <h1 class="h3 fw-bold text-dark mb-1">Comandas en Sala & Punto de Venta</h1>
            <p class="text-muted mb-0 small">Control de pedidos en cocina, cuentas en servicio y comandas finalizadas.</p>
        </div>
        <div class="mt-3 mt-md-0 d-flex gap-2">
            <a href="{{ route('comandas.create') }}" class="btn btn-rv-gold btn-sm d-flex align-items-center gap-1 shadow-sm px-3">
                <i class="bi bi-plus-circle-fill"></i> Abrir Nueva Comanda
            </a>
        </div>
    </div>

    <!-- Métricas Rápidas -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card card-custom p-3 border-start border-warning border-4 shadow-sm">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small text-uppercase fw-bold">En Sala / Pendientes</div>
                        <div class="h3 fw-bold text-warning-emphasis mb-0">{{ $comandasPendientes->count() }}</div>
                    </div>
                    <div class="bg-warning-subtle text-warning p-3 rounded-circle">
                        <i class="bi bi-clock-history fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card card-custom p-3 border-start border-success border-4 shadow-sm">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small text-uppercase fw-bold">Finalizadas / Cobradas</div>
                        <div class="h3 fw-bold text-success mb-0">{{ $comandasFinalizadas->count() }}</div>
                    </div>
                    <div class="bg-success-subtle text-success p-3 rounded-circle">
                        <i class="bi bi-check2-circle fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card card-custom p-3 border-start border-primary border-4 shadow-sm">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small text-uppercase fw-bold">Facturado Liquidado</div>
                        <div class="h3 fw-bold text-primary mb-0">${{ number_format($comandasFinalizadas->sum('total'), 2) }}</div>
                    </div>
                    <div class="bg-primary-subtle text-primary p-3 rounded-circle">
                        <i class="bi bi-cash-stack fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card card-custom p-3 border-start border-dark border-4 shadow-sm">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small text-uppercase fw-bold">Total Comandas</div>
                        <div class="h3 fw-bold text-dark mb-0">{{ $todasNotas->count() }}</div>
                    </div>
                    <div class="bg-light text-dark p-3 rounded-circle border">
                        <i class="bi bi-receipt fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtro por Sucursal -->
    <div class="card card-custom p-3 mb-4">
        <form method="GET" action="{{ route('comandas.index') }}" class="row g-2 align-items-center">
            <div class="col-md-9">
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="bi bi-geo-alt text-muted"></i></span>
                    <select name="sucursal_id" class="form-select">
                        <option value="">Todas las Sucursales</option>
                        @foreach($sucursales as $suc)
                            <option value="{{ $suc->id }}" {{ request('sucursal_id') == $suc->id ? 'selected' : '' }}>
                                {{ $suc->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-rv-primary w-100"><i class="bi bi-funnel me-1"></i> Filtrar</button>
                @if(request()->filled('sucursal_id'))
                    <a href="{{ route('comandas.index') }}" class="btn btn-outline-secondary" title="Limpiar filtro"><i class="bi bi-x-lg"></i></a>
                @endif
            </div>
        </form>
    </div>

    <!-- Pestañas de División: Pendientes vs Finalizadas -->
    <ul class="nav nav-pills mb-4 gap-2 bg-white p-2 rounded shadow-sm border" id="comandasTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active rounded-pill px-4 fw-semibold" id="pendientes-tab" data-bs-toggle="pill" data-bs-target="#tab-pendientes" type="button" role="tab" aria-selected="true">
                <i class="bi bi-hourglass-split me-1 text-warning"></i> Comandas Pendientes / En Mesa
                <span class="badge bg-warning text-dark ms-2 rounded-pill">{{ $comandasPendientes->count() }}</span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link rounded-pill px-4 fw-semibold" id="finalizadas-tab" data-bs-toggle="pill" data-bs-target="#tab-finalizadas" type="button" role="tab" aria-selected="false">
                <i class="bi bi-check2-all me-1 text-success"></i> Comandas Terminadas / Finalizadas
                <span class="badge bg-success ms-2 rounded-pill">{{ $comandasFinalizadas->count() }}</span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link rounded-pill px-4 fw-semibold text-secondary" id="todas-tab" data-bs-toggle="pill" data-bs-target="#tab-todas" type="button" role="tab" aria-selected="false">
                <i class="bi bi-collection me-1"></i> Todas las Comandas
                <span class="badge bg-secondary ms-2 rounded-pill">{{ $todasNotas->count() }}</span>
            </button>
        </li>
    </ul>

    <!-- Contenido de las Pestañas -->
    <div class="tab-content" id="comandasTabContent">

        <!-- 1. PESTAÑA: COMANDAS PENDIENTES (EN SERVICIO) -->
        <div class="tab-pane fade show active" id="tab-pendientes" role="tabpanel" aria-labelledby="pendientes-tab">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="fw-bold text-dark"><i class="bi bi-fire text-danger me-1"></i> Comandas Abiertas Actualmente en Sala</div>
                <span class="text-muted small">Mostrando {{ $comandasPendientes->count() }} cuentas por liquidar</span>
            </div>

            <div class="row g-4">
                @forelse($comandasPendientes as $nota)
                <div class="col-md-6 col-xl-4">
                    <div class="card card-custom h-100 d-flex flex-column border-warning-subtle shadow-sm">
                        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
                            <div>
                                <span class="fw-bold text-dark fs-6">Comanda #{{ $nota->id }}</span>
                                <div class="small text-muted">{{ $nota->sucursal->nombre ?? 'Sucursal' }}</div>
                            </div>
                            <div class="text-end">
                                <span class="badge bg-warning text-dark mb-1 d-block"><i class="bi bi-clock-history me-1"></i>EN SERVICIO</span>
                                <span class="badge bg-primary fs-6">${{ number_format($nota->subtotal, 2) }}</span>
                            </div>
                        </div>

                        <div class="card-body p-3 flex-grow-1">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-muted small">Mesa / Salón:</span>
                                <strong class="text-dark">
                                    @if($nota->reservacion && $nota->reservacion->mesasAsignadas->isNotEmpty())
                                        Mesa {{ $nota->reservacion->mesasAsignadas->first()->mesaSucursal->mesa->codigo_mesa ?? 'Mesa' }}
                                    @else
                                        Barra / Venta Directa
                                    @endif
                                </strong>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-muted small">Huésped / Titular:</span>
                                <span class="fw-semibold text-truncate" style="max-width: 180px;">
                                    {{ $nota->reservacion && $nota->reservacion->cliente ? $nota->reservacion->cliente->nombre_completo : 'Venta General' }}
                                </span>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="text-muted small">Mesero a Cargo:</span>
                                <span class="small">{{ $nota->empleado ? $nota->empleado->nombre_completo : 'No asignado' }}</span>
                            </div>

                            <div class="p-2 bg-light rounded border mb-3">
                                <div class="small text-muted mb-1">Órdenes activas en cocina:</div>
                                @php $totalItems = 0; @endphp
                                @foreach($nota->pedidos as $p)
                                    @php $totalItems += $p->detalles->count(); @endphp
                                @endforeach
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="badge bg-secondary-subtle text-dark border">{{ $nota->pedidos->count() }} pedidos</span>
                                    <span class="small fw-semibold text-primary">{{ $totalItems }} platillos ordenados</span>
                                </div>
                            </div>
                        </div>

                        <div class="card-footer bg-white py-3 border-top d-flex justify-content-between align-items-center">
                            <span class="small text-muted"><i class="bi bi-clock me-1"></i>{{ \Carbon\Carbon::parse($nota->fecha_emision)->format('d/m H:i') }}</span>
                            <a href="{{ route('comandas.show', $nota->id) }}" class="btn btn-sm btn-rv-gold px-3 fw-semibold">
                                <i class="bi bi-pencil-square me-1"></i> Gestionar & Cobrar
                            </a>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12 text-center py-5 bg-white rounded border shadow-sm">
                    <i class="bi bi-check2-circle text-success" style="font-size: 3.5rem;"></i>
                    <h5 class="mt-3 text-dark fw-bold">¡Sin comandas pendientes!</h5>
                    <p class="text-muted small mb-3">No hay mesas o comandas en servicio pendientes de liquidación.</p>
                    <a href="{{ route('comandas.create') }}" class="btn btn-sm btn-rv-primary">
                        <i class="bi bi-plus-circle me-1"></i> Abrir Nueva Comanda
                    </a>
                </div>
                @endforelse
            </div>
        </div>

        <!-- 2. PESTAÑA: COMANDAS FINALIZADAS (TERMINADAS) -->
        <div class="tab-pane fade" id="tab-finalizadas" role="tabpanel" aria-labelledby="finalizadas-tab">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="fw-bold text-dark"><i class="bi bi-check-circle-fill text-success me-1"></i> Historial de Comandas Liquidadas y Facturadas</div>
                <span class="text-muted small">Total cobrado: ${{ number_format($comandasFinalizadas->sum('total'), 2) }} MXN</span>
            </div>

            <div class="row g-4">
                @forelse($comandasFinalizadas as $nota)
                <div class="col-md-6 col-xl-4">
                    <div class="card card-custom h-100 d-flex flex-column border-success-subtle shadow-sm">
                        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
                            <div>
                                <span class="fw-bold text-dark fs-6">Comanda #{{ $nota->id }}</span>
                                <div class="small text-muted">{{ $nota->sucursal->nombre ?? 'Sucursal' }}</div>
                            </div>
                            <div class="text-end">
                                <span class="badge bg-success mb-1 d-block"><i class="bi bi-check2-circle me-1"></i>LIQUIDADA</span>
                                <span class="badge bg-success-subtle text-success border border-success fs-6">${{ number_format($nota->total, 2) }}</span>
                            </div>
                        </div>

                        <div class="card-body p-3 flex-grow-1">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-muted small">Mesa / Salón:</span>
                                <strong class="text-dark">
                                    @if($nota->reservacion && $nota->reservacion->mesasAsignadas->isNotEmpty())
                                        Mesa {{ $nota->reservacion->mesasAsignadas->first()->mesaSucursal->mesa->codigo_mesa ?? 'Mesa' }}
                                    @else
                                        Barra / Venta Directa
                                    @endif
                                </strong>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-muted small">Huésped / Titular:</span>
                                <span class="fw-semibold text-truncate" style="max-width: 180px;">
                                    {{ $nota->reservacion && $nota->reservacion->cliente ? $nota->reservacion->cliente->nombre_completo : 'Venta General' }}
                                </span>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="text-muted small">Mesero / Cerró:</span>
                                <span class="small">{{ $nota->empleado ? $nota->empleado->nombre_completo : 'No asignado' }}</span>
                            </div>

                            <div class="p-2 bg-light rounded border mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="small text-muted">Subtotal:</span>
                                    <span class="small fw-semibold">${{ number_format($nota->subtotal, 2) }}</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="small text-muted">Propina recibida:</span>
                                    <span class="small text-success">+${{ number_format($nota->propina, 2) }}</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center border-top pt-1">
                                    <span class="small fw-bold text-dark">Total Liquidado:</span>
                                    <span class="small fw-bold text-success fs-6">${{ number_format($nota->total, 2) }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="card-footer bg-white py-3 border-top d-flex justify-content-between align-items-center">
                            <span class="small text-muted"><i class="bi bi-calendar-check me-1"></i>{{ \Carbon\Carbon::parse($nota->fecha_emision)->format('d/m H:i') }}</span>
                            <a href="{{ route('comandas.show', $nota->id) }}" class="btn btn-sm btn-outline-success px-3">
                                <i class="bi bi-receipt me-1"></i> Ver Ticket Liquidado
                            </a>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12 text-center py-5 bg-white rounded border shadow-sm">
                    <i class="bi bi-receipt text-muted" style="font-size: 3.5rem;"></i>
                    <h5 class="mt-3 text-muted">No hay comandas finalizadas</h5>
                    <p class="text-muted small">Las comandas que liquide aparecerán en este historial.</p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- 3. PESTAÑA: TODAS LAS COMANDAS -->
        <div class="tab-pane fade" id="tab-todas" role="tabpanel" aria-labelledby="todas-tab">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="fw-bold text-dark"><i class="bi bi-collection me-1"></i> Vista Global de Todas las Comandas</div>
                <span class="text-muted small">Total: {{ $todasNotas->count() }} comandas</span>
            </div>

            <div class="row g-4">
                @forelse($todasNotas as $nota)
                <div class="col-md-6 col-xl-4">
                    <div class="card card-custom h-100 d-flex flex-column {{ $nota->esta_cerrada ? 'border-success-subtle' : 'border-warning-subtle' }} shadow-sm">
                        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
                            <div>
                                <span class="fw-bold text-dark fs-6">Comanda #{{ $nota->id }}</span>
                                <div class="small text-muted">{{ $nota->sucursal->nombre ?? 'Sucursal' }}</div>
                            </div>
                            <div class="text-end">
                                @if($nota->esta_cerrada)
                                    <span class="badge bg-success mb-1 d-block">LIQUIDADA</span>
                                    <span class="badge bg-success-subtle text-success border border-success fs-6">${{ number_format($nota->total, 2) }}</span>
                                @else
                                    <span class="badge bg-warning text-dark mb-1 d-block">EN SERVICIO</span>
                                    <span class="badge bg-primary fs-6">${{ number_format($nota->subtotal, 2) }}</span>
                                @endif
                            </div>
                        </div>

                        <div class="card-body p-3 flex-grow-1">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-muted small">Mesa / Salón:</span>
                                <strong class="text-dark">
                                    @if($nota->reservacion && $nota->reservacion->mesasAsignadas->isNotEmpty())
                                        Mesa {{ $nota->reservacion->mesasAsignadas->first()->mesaSucursal->mesa->codigo_mesa ?? 'Mesa' }}
                                    @else
                                        Barra / Venta Directa
                                    @endif
                                </strong>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-muted small">Huésped / Titular:</span>
                                <span class="fw-semibold text-truncate" style="max-width: 180px;">
                                    {{ $nota->reservacion && $nota->reservacion->cliente ? $nota->reservacion->cliente->nombre_completo : 'Venta General' }}
                                </span>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="text-muted small">Mesero:</span>
                                <span class="small">{{ $nota->empleado ? $nota->empleado->nombre_completo : 'No asignado' }}</span>
                            </div>
                        </div>

                        <div class="card-footer bg-white py-3 border-top d-flex justify-content-between align-items-center">
                            <span class="small text-muted"><i class="bi bi-clock me-1"></i>{{ \Carbon\Carbon::parse($nota->fecha_emision)->format('d/m H:i') }}</span>
                            <a href="{{ route('comandas.show', $nota->id) }}" class="btn btn-sm {{ $nota->esta_cerrada ? 'btn-outline-success' : 'btn-rv-gold' }} px-3">
                                @if($nota->esta_cerrada)
                                    <i class="bi bi-receipt me-1"></i> Ver Ticket
                                @else
                                    <i class="bi bi-pencil-square me-1"></i> Gestionar
                                @endif
                            </a>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12 text-center py-5">
                    <i class="bi bi-receipt-cutoff text-muted" style="font-size: 3.5rem;"></i>
                    <h5 class="mt-3 text-muted">No hay comandas registradas</h5>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
