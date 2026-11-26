@extends('layouts.app')

@section('title', 'Directorio de Personal')

@section('content')
    <div class="container-fluid px-lg-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
            <div>
                <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                    <h1 class="h3 fw-bold text-dark mb-0">Equipo & Colaboradores</h1>
                    @can('sucursales.admin')
                        <span class="badge rounded-pill {{ ($sucursalActivaId ?? null) ? 'bg-warning text-dark' : 'bg-dark text-warning' }} px-3 py-2">
                            <i class="bi bi-building me-1"></i>
                            Sucursal: {{ $sucursalActivaNombre ?? 'Todas las sucursales' }}
                        </span>
                    @endcan
                </div>
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

        <!-- Barra de Búsqueda -->
        <div class="card card-custom mb-4 shadow-sm border-0">
            <div class="card-body py-3">
                <form method="GET" action="{{ route('empleados.index') }}" class="row g-3 align-items-center">
                    <div class="col-md-10">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                            <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="Buscar por nombre, correo electrónico o teléfono..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-2 d-grid">
                        <button type="submit" class="btn btn-dark btn-sm">Buscar</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Agregar Empleado -->
        @can('empleados.admin')
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
                                            <option value="{{ $s->id }}" {{ (($sucursalActivaId ?? null) == $s->id) ? 'selected' : '' }}>
                                                {{ $s->nombre }}
                                            </option>
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
        @endcan

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
                            @if(auth()->user() && (auth()->user()->email === 'admin@rodavento.com' || optional(auth()->user()->rol)->nombre === 'Administrador'))
                                <th class="text-end">Acciones</th>
                            @endif
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
                                @if(auth()->user() && (auth()->user()->email === 'admin@rodavento.com' || optional(auth()->user()->rol)->nombre === 'Administrador'))
                                    <td class="text-end">
                                        <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editModal{{ $emp->id }}" title="Editar">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <form action="{{ route('empleados.destroy', $emp->id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Estás seguro de eliminar este colaborador?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                @endif
                            </tr>

                            <!-- Modal de Edición para cada empleado -->
                            @if(auth()->user() && (auth()->user()->email === 'admin@rodavento.com' || optional(auth()->user()->rol)->nombre === 'Administrador'))
                                <div class="modal fade" id="editModal{{ $emp->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form method="POST" action="{{ route('empleados.update', $emp->id) }}">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-header">
                                                    <h5 class="modal-title fw-bold">Editar Colaborador</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body text-start">
                                                    <div class="mb-3">
                                                        <label class="form-label fw-semibold">Nombres *</label>
                                                        <input type="text" name="nombres" class="form-control" value="{{ $emp->nombres }}" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label fw-semibold">Apellidos *</label>
                                                        <input type="text" name="apellidos" class="form-control" value="{{ $emp->apellidos }}" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label fw-semibold">Puesto / Cargo *</label>
                                                        <input type="text" name="puesto" class="form-control" value="{{ $emp->puesto }}" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label fw-semibold">Teléfono</label>
                                                        <input type="text" name="telefono" class="form-control" value="{{ $emp->telefono }}">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label fw-semibold">Estatus *</label>
                                                        <select name="estatus" class="form-select" required>
                                                            <option value="activo" {{ $emp->estatus == 'activo' ? 'selected' : '' }}>Activo</option>
                                                            <option value="pendiente" {{ $emp->estatus == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                                                            <option value="inactivo" {{ $emp->estatus == 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">Sin colaboradores registrados o coincidentes con la búsqueda.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
