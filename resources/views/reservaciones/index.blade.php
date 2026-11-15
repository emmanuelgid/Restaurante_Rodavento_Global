@extends('layouts.app')

@section('title', 'Gestión de Reservaciones')

@section('content')
<div class="container-fluid px-lg-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
        <div>
            <h1 class="h3 fw-bold text-dark mb-1">Agenda & Reservaciones de Mesa</h1>
            <p class="text-muted mb-0 small">Control de ocupación, asignación de salas, tiempos de estancia y recepción de huéspedes.</p>
        </div>
        <div class="mt-3 mt-md-0">
            <a href="{{ route('reservaciones.create') }}" class="btn btn-rv-gold btn-sm d-flex align-items-center gap-1 shadow-sm">
                <i class="bi bi-calendar-plus"></i> Nueva Reservación
            </a>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card card-custom p-3 mb-4">
        <form method="GET" action="{{ route('reservaciones.index') }}" class="row g-2 align-items-center">
            <div class="col-md-4">
                <label class="form-label small text-muted mb-1">Filtrar por Fecha</label>
                <input type="date" name="fecha" class="form-control" value="{{ request('fecha') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label small text-muted mb-1">Estatus</label>
                <select name="estatus" class="form-select">
                    <option value="">Todos los Estatus</option>
                    <option value="pendiente" {{ request('estatus') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                    <option value="confirmada" {{ request('estatus') == 'confirmada' ? 'selected' : '' }}>Confirmada</option>
                    <option value="en_mesa" {{ request('estatus') == 'en_mesa' ? 'selected' : '' }}>En Mesa</option>
                    <option value="concluida" {{ request('estatus') == 'concluida' ? 'selected' : '' }}>Concluida</option>
                    <option value="cancelada" {{ request('estatus') == 'cancelada' ? 'selected' : '' }}>Cancelada</option>
                </select>
            </div>
            <div class="col-md-4 d-flex align-items-end gap-2">
                <button type="submit" class="btn btn-rv-primary w-100">Aplicar Filtro</button>
                @if(request()->hasAny(['fecha', 'estatus']))
                    <a href="{{ route('reservaciones.index') }}" class="btn btn-outline-secondary"><i class="bi bi-x-lg"></i></a>
                @endif
            </div>
        </form>
    </div>

    <!-- Listado de Reservaciones -->
    <div class="card card-custom">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light small text-uppercase">
                        <tr>
                            <th>Folio</th>
                            <th>Cliente / Huésped</th>
                            <th>Nivel Lealtad</th>
                            <th>Fecha & Horario</th>
                            <th>Personas</th>
                            <th>Mesa Asignada</th>
                            <th>Estatus</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reservaciones as $res)
                        <tr>
                            <td class="fw-bold">#RES-{{ $res->id }}</td>
                            <td>
                                @if($res->cliente)
                                    <a href="{{ route('clientes.show', $res->cliente->id) }}" class="fw-semibold text-dark text-decoration-none d-block">
                                        {{ $res->cliente->nombre_completo }}
                                    </a>
                                    <small class="text-muted"><i class="bi bi-telephone me-1"></i>{{ $res->cliente->telefono ?? 'Sin teléfono' }}</small>
                                @else
                                    <span class="text-muted">Sin cliente</span>
                                @endif
                            </td>
                            <td>
                                @if($res->cliente && $res->cliente->nivelLealtad)
                                    <span class="badge {{ str_contains($res->cliente->nivelLealtad->nombre, 'Black') ? 'badge-black' : 'badge-oro' }}">
                                        {{ $res->cliente->nivelLealtad->nombre }}
                                    </span>
                                @else
                                    <span class="badge bg-secondary">Bronce</span>
                                @endif
                            </td>
                            <td>
                                <div class="fw-semibold text-dark">
                                    <i class="bi bi-calendar3 me-1 text-muted"></i>{{ \Carbon\Carbon::parse($res->fecha_hora_inicio)->format('d/m/Y') }}
                                </div>
                                <small class="text-muted">
                                    <i class="bi bi-clock me-1"></i>{{ \Carbon\Carbon::parse($res->fecha_hora_inicio)->format('H:i') }} - {{ \Carbon\Carbon::parse($res->fecha_hora_final)->format('H:i') }}
                                </small>
                            </td>
                            <td>
                                <span class="badge bg-secondary-subtle text-dark border">{{ $res->cantidad_personas }} pax</span>
                            </td>
                            <td>
                                @if($res->mesasAsignadas->isNotEmpty())
                                    @foreach($res->mesasAsignadas as $ma)
                                        <span class="badge bg-dark">
                                            <i class="bi bi-grid-fill me-1 text-warning"></i>
                                            {{ $ma->mesaSucursal->mesa->codigo_mesa ?? 'Mesa' }}
                                        </span>
                                    @endforeach
                                @else
                                    <span class="text-danger small">No asignada</span>
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
                                <div class="d-flex gap-1">
                                    <a href="{{ route('reservaciones.show', $res->id) }}" class="btn btn-sm btn-light border" title="Ver Detalle">
                                        <i class="bi bi-eye"></i> Detalle
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="bi bi-calendar-x fs-1 d-block mb-2 text-muted"></i>
                                No se encontraron reservaciones para el criterio seleccionado.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-4">
        {{ $reservaciones->links() }}
    </div>
</div>
@endsection