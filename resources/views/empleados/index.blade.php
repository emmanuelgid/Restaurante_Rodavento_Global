@extends('layouts.app')

@section('title', 'Directorio de Personal')

@section('content')
<div class="container-fluid px-lg-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
        <div>
            <h1 class="h3 fw-bold text-dark mb-1">Equipo & Colaboradores</h1>
            <p class="text-muted mb-0 small">Directorio de personal de cocina, sala, capitanes, chefs y gerencia.</p>
        </div>
        <div class="mt-3 mt-md-0">
            @can('empleados.admin')
                <button type="button" class="btn btn-rv-gold btn-sm" data-bs-toggle="modal" data-bs-target="#empleadoModal">
                    <i class="bi bi-person-plus"></i> Registrar Colaborador
                </button>
            @endcan

        </div>
    </div>

    <!-- Modal Agregar Empleado -->
    <div class="modal fade" id="empleadoModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="{{ route('empleados.store') }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">Registrar Colaborador</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nombres *</label>
                            <input type="text" name="nombres" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Apellidos *</label>
                            <input type="text" name="apellidos" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Puesto / Cargo *</label>
                            <input type="text" name="puesto" class="form-control" required placeholder="Ej. Mesero, Chef de Partie, Sommelier, Capitán">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Teléfono</label>
                            <input type="text" name="telefono" class="form-control" placeholder="7221234567">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Sucursal Asignada *</label>
                            <select name="sucursal_id" class="form-select" required>
                                @foreach($sucursales as $s)
                                    <option value="{{ $s->id }}">{{ $s->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Registrar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Tabla de Empleados -->
    <div class="card card-custom">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light small text-uppercase">
                        <tr>
                            <th>Colaborador</th>
                            <th>Puesto</th>
                            <th>Sucursal</th>
                            <th>Teléfono</th>
                            <th>Fecha de Ingreso</th>
                            <th>Estatus</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($empleados as $emp)
                        <tr>
                            <td>
                                <div class="fw-bold text-dark">{{ $emp->nombre_completo }}</div>
                                @if($emp->user)
                                    <small class="text-primary"><i class="bi bi-shield-lock me-1"></i>Usuario del sistema: {{ $emp->user->email }}</small>
                                @endif
                            </td>
                            <td><span class="badge bg-light text-dark border">{{ $emp->puesto }}</span></td>
                            <td>
                                @foreach($emp->sucursales as $s)
                                    <span class="badge bg-dark">{{ $s->nombre }}</span>
                                @endforeach
                            </td>
                            <td>{{ $emp->telefono ?: 'N/D' }}</td>
                            <td>{{ \Carbon\Carbon::parse($emp->fecha_ingreso)->format('d/m/Y') }}</td>
                            <td>
                                <span class="badge bg-success-subtle text-success border border-success-subtle">
                                    {{ $emp->estatus }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">Sin colaboradores registrados.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
