@extends('layouts.app')

@section('title', 'Auditoría & Bitácora')

@section('content')
<div class="container-fluid px-lg-4">
    <div class="mb-4">
        <h1 class="h3 fw-bold text-dark mb-1">Auditoría del Sistema & Solicitudes ARCO</h1>
        <p class="text-muted mb-0 small">Trazabilidad inmutable de operaciones, accesos y cumplimiento normativo de privacidad de datos.</p>
    </div>

    <!-- Card de Filtros de Búsqueda -->
    <div class="card card-custom mb-4 shadow-sm">
        <div class="card-body p-3 bg-light">
            <form method="GET" action="{{ route('bitacora.index') }}" class="row g-3 align-items-end">
                <!-- Filtro Fecha Inicio -->
                <div class="col-md-3 col-sm-6">
                    <label class="form-label small fw-semibold text-muted">Fecha Desde</label>
                    <input type="date" name="fecha_inicio" class="form-control form-control-sm" value="{{ request('fecha_inicio') }}">
                </div>

                <!-- Filtro Fecha Fin -->
                <div class="col-md-3 col-sm-6">
                    <label class="form-label small fw-semibold text-muted">Fecha Hasta</label>
                    <input type="date" name="fecha_fin" class="form-control form-control-sm" value="{{ request('fecha_fin') }}">
                </div>

                <!-- Filtro por Acción -->
                <div class="col-md-3 col-sm-6">
                    <label class="form-label small fw-semibold text-muted">Filtrar por Acción</label>
                    <select name="accion" class="form-select form-select-sm">
                        <option value="">Todas las acciones...</option>
                        @foreach($accionesDisponibles as $acc)
                            <option value="{{ $acc }}" {{ request('accion') == $acc ? 'selected' : '' }}>
                                {{ $acc }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Botones de Acción -->
                <div class="col-md-3 col-sm-6 d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm flex-fill fw-semibold">
                        <i class="bi bi-funnel-fill me-1"></i> Filtrar
                    </button>
                    @if(request()->hasAny(['fecha_inicio', 'fecha_fin', 'accion']))
                        <a href="{{ route('bitacora.index') }}" class="btn btn-outline-secondary btn-sm" title="Limpiar Filtros">
                            <i class="bi bi-x-circle"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla de Eventos -->
    <div class="card card-custom mb-4 shadow-sm">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-shield-check me-2 text-danger"></i> Registro de Actividades (Bitácora)</h5>
            <span class="badge bg-secondary">Total: {{ $bitacoras->total() }} eventos</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 font-monospace small">
                    <thead class="table-light text-uppercase">
                        <tr>
                            <th>ID</th>
                            <th>Fecha & Hora</th>
                            <th>Usuario Responsable</th>
                            <th>Acción</th>
                            <th>Registro Afectado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bitacoras as $bit)
                        <tr>
                            <td>#{{ $bit->id }}</td>
                            <td>{{ \Carbon\Carbon::parse($bit->fecha_hora)->format('Y-m-d H:i:s') }}</td>
                            <td>
                                <strong>{{ $bit->usuario ? $bit->usuario->name : 'SISTEMA' }}</strong>
                            </td>
                            <td>
                                <span class="badge bg-dark">{{ $bit->accion }}</span>
                            </td>
                            <td>{{ $bit->registro_id }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">No se encontraron eventos coincidentes con los filtros especificados.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Paginación con estilos Bootstrap 5 -->
    <div class="d-flex justify-content-end mb-4">
        {{ $bitacoras->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection