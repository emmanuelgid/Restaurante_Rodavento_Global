@extends('layouts.app')

@section('title', 'Dashboard Ejecutivo')

@section('content')
<div class="container-fluid px-lg-4">
    <!-- Header Title -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
        <div>
            <h1 class="h3 fw-bold text-dark mb-1">Panel de Control & Operaciones</h1>
            <p class="text-muted mb-0 small">Bienvenido, <strong>{{ Auth::user()->name }}</strong>. Monitoreo en tiempo real de sucursales, sala, comandas y fidelización VIP.</p>
        </div>
        <div class="d-flex gap-2 mt-3 mt-md-0">
            <a href="{{ route('reservaciones.create') }}" class="btn btn-rv-gold btn-sm d-flex align-items-center gap-1 shadow-sm">
                <i class="bi bi-calendar-plus"></i> Nueva Reservación
            </a>
            <a href="{{ route('comandas.create') }}" class="btn btn-rv-primary btn-sm d-flex align-items-center gap-1 shadow-sm">
                <i class="bi bi-receipt-cutoff"></i> Abrir Comanda
            </a>
        </div>
    </div>

    <!-- Metric Cards Row -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card card-custom h-100 p-3 border-start border-4 border-warning">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small text-uppercase fw-semibold">Reservaciones Hoy</div>
                        <div class="fs-3 fw-bold text-dark my-1">{{ $reservacionesHoy }}</div>
                        <div class="small text-muted">Total registradas: <strong>{{ $totalReservaciones }}</strong></div>
                    </div>
                    <div class="rounded-3 bg-warning-subtle text-warning p-3 fs-3">
                        <i class="bi bi-calendar-event"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card card-custom h-100 p-3 border-start border-4 border-primary">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small text-uppercase fw-semibold">Comandas Activas</div>
                        <div class="fs-3 fw-bold text-dark my-1">{{ $comandasAbiertas }}</div>
                        <div class="small text-primary"><i class="bi bi-fire"></i> En sala y cocina</div>
                    </div>
                    <div class="rounded-3 bg-primary-subtle text-primary p-3 fs-3">
                        <i class="bi bi-receipt"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card card-custom h-100 p-3 border-start border-4 border-success">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small text-uppercase fw-semibold">Ventas Facturadas</div>
                        <div class="fs-3 fw-bold text-dark my-1">${{ number_format($ventasTotales, 2) }}</div>
                        <div class="small text-success"><i class="bi bi-cash-stack"></i> Ingresos notas cerradas</div>
                    </div>
                    <div class="rounded-3 bg-success-subtle text-success p-3 fs-3">
                        <i class="bi bi-currency-dollar"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card card-custom h-100 p-3 border-start border-4 border-dark">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small text-uppercase fw-semibold">Clientes & VIP</div>
                        <div class="fs-3 fw-bold text-dark my-1">{{ $totalClientes }}</div>
                        <div class="small text-muted"><i class="bi bi-star-fill text-warning"></i> Programa Lealtad</div>
                    </div>
                    <div class="rounded-3 bg-dark-subtle text-dark p-3 fs-3">
                        <i class="bi bi-person-check-fill"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Grid Content -->
    <div class="row g-4">
        <!-- Columna Izquierda: Reservaciones del Día & Comandas -->
        <div class="col-lg-8">
            <!-- Card Reservaciones -->
            <div class="card card-custom mb-4">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
                    <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-calendar3 me-2 text-warning"></i> Próximas Reservaciones</h5>
                    <a href="{{ route('reservaciones.index') }}" class="btn btn-outline-secondary btn-sm">Ver todas</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light small text-uppercase">
                                <tr>
                                    <th>Folio</th>
                                    <th>Huésped / Cliente</th>
                                    <th>Fecha & Hora</th>
                                    <th>Pax</th>
                                    <th>Mesa</th>
                                    <th>Estatus</th>
                                    <th>Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($ultimasReservaciones as $res)
                                <tr>
                                    <td class="fw-bold">#RES-{{ $res->id }}</td>
                                    <td>
                                        <div class="fw-semibold text-dark">{{ $res->cliente ? $res->cliente->nombre_completo : 'Cliente no asignado' }}</div>
                                        <small class="text-muted">{{ $res->cliente ? $res->cliente->correo : '' }}</small>
                                    </td>
                                    <td>
                                        <div><i class="bi bi-clock me-1 text-muted"></i>{{ \Carbon\Carbon::parse($res->fecha_hora_inicio)->format('d/m/Y H:i') }}</div>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $res->cantidad_personas }} pers.</span>
                                    </td>
                                    <td>
                                        @if($res->mesasAsignadas->isNotEmpty())
                                            <span class="badge bg-dark">{{ $res->mesasAsignadas->first()->mesaSucursal->mesa->codigo_mesa ?? 'Mesa' }}</span>
                                        @else
                                            <span class="text-muted small">Sin mesa</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $pillClass = match($res->estatus) {
                                                'confirmada' => 'bg-success-subtle text-success border border-success',
                                                'en_mesa' => 'bg-primary-subtle text-primary border border-primary',
                                                'concluida' => 'bg-secondary-subtle text-secondary',
                                                'cancelada' => 'bg-danger-subtle text-danger',
                                                default => 'bg-warning-subtle text-warning'
                                            };
                                        @endphp
                                        <span class="status-pill {{ $pillClass }}">{{ $res->estatus }}</span>
                                    </td>
                                    <td>
                                        <a href="{{ route('reservaciones.show', $res->id) }}" class="btn btn-sm btn-light border">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">No hay reservaciones registradas.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Card Comandas Recientes -->
            <div class="card card-custom">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
                    <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-receipt-cutoff me-2 text-primary"></i> Monitoreo de Comandas Vivas</h5>
                    <a href="{{ route('comandas.index') }}" class="btn btn-outline-secondary btn-sm">Ver comandas</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light small text-uppercase">
                                <tr>
                                    <th>Comanda</th>
                                    <th>Sucursal</th>
                                    <th>Items</th>
                                    <th>Total Parcial</th>
                                    <th>Estatus</th>
                                    <th>Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($comandasRecientes as $com)
                                <tr>
                                    <td class="fw-bold">#ORD-{{ $com->id }}</td>
                                    <td>{{ $com->notaReservacion->sucursal->nombre ?? 'Rodavento' }}</td>
                                    <td>
                                        <span class="badge bg-info-subtle text-info border border-info">{{ $com->detalles->count() }} platillos</span>
                                    </td>
                                    <td class="fw-semibold text-success">
                                        ${{ number_format($com->notaReservacion->total ?? 0, 2) }}
                                    </td>
                                    <td>
                                        <span class="badge {{ $com->estatus === 'cerrado' ? 'bg-secondary' : 'bg-primary' }}">
                                            {{ $com->estatus }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('comandas.show', $com->nota_reservacion_id) }}" class="btn btn-sm btn-light border">
                                            <i class="bi bi-arrow-right"></i> Comanda
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">No hay comandas activas en este momento.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Columna Derecha: Top Clientes VIP y Auditoría Reciente -->
        <div class="col-lg-4">
            <!-- Top Clientes Lealtad -->
            <div class="card card-custom mb-4">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
                    <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-award-fill me-2 text-warning"></i> Socios VIP / Lealtad</h5>
                    <a href="{{ route('clientes.index') }}" class="btn btn-link btn-sm text-decoration-none">Ver todos</a>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @forelse($topClientes as $cli)
                        <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                            <div class="d-flex align-items-center gap-2">
                                <div class="rounded-circle bg-dark text-warning p-2 small fw-bold">
                                    {{ substr($cli->nombres, 0, 1) }}{{ substr($cli->apellidos, 0, 1) }}
                                </div>
                                <div>
                                    <a href="{{ route('clientes.show', $cli->id) }}" class="fw-semibold text-dark text-decoration-none d-block">
                                        {{ $cli->nombre_completo }}
                                    </a>
                                    <span class="badge {{ $cli->nivelLealtad && str_contains($cli->nivelLealtad->nombre, 'Black') ? 'badge-black' : 'badge-oro' }} small">
                                        {{ $cli->nivelLealtad ? $cli->nivelLealtad->nombre : 'Bronce' }}
                                    </span>
                                </div>
                            </div>
                            <div class="text-end">
                                <div class="fw-bold text-dark">{{ number_format($cli->puntos_saldo) }} pts</div>
                                <small class="text-muted">saldo</small>
                            </div>
                        </li>
                        @empty
                        <li class="list-group-item text-center py-3 text-muted">Sin clientes registrados.</li>
                        @endforelse
                    </ul>
                </div>
            </div>

            <!-- Bitácora de Auditoría en Vivo -->
            <div class="card card-custom">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
                    <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-shield-check me-2 text-danger"></i> Bitácora de Auditoría</h5>
                    <a href="{{ route('bitacora.index') }}" class="btn btn-link btn-sm text-decoration-none">Ver log</a>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush small">
                        @forelse($bitacoras as $bit)
                        <li class="list-group-item py-2 px-3">
                            <div class="d-flex justify-content-between">
                                <span class="badge bg-secondary-subtle text-secondary font-monospace">{{ $bit->accion }}</span>
                                <span class="text-muted">{{ \Carbon\Carbon::parse($bit->fecha_hora)->diffForHumans() }}</span>
                            </div>
                            <div class="text-dark mt-1 text-truncate" style="max-width: 280px;">
                                <strong>{{ $bit->usuario ? $bit->usuario->name : 'Sistema' }}</strong>: {{ $bit->registro_id }}
                            </div>
                        </li>
                        @empty
                        <li class="list-group-item text-center py-3 text-muted">Sin registros de auditoría.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
