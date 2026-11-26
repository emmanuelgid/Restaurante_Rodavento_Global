@extends('layouts.app')

@section('title', 'Directorio de Clientes VIP')

@section('content')
    <div class="container-fluid px-lg-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
            <div>
                <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                    <h1 class="h3 fw-bold text-dark mb-0">Directorio de Huéspedes & Socios Lealtad</h1>
                    @can('sucursales.admin')
                        <span class="badge rounded-pill {{ ($sucursalActivaId ?? null) ? 'bg-warning text-dark' : 'bg-dark text-warning' }} px-3 py-2">
                        <i class="bi bi-building me-1"></i>
                        Sucursal: {{ $sucursalActivaNombre ?? 'Todas las sucursales' }}
                    </span>
                    @endcan
                </div>
                <p class="text-muted mb-0 small">Expedientes 360°, monitoreo de puntos, preferencias gastronómicas y alérgenos.</p>
            </div>
            <div class="mt-3 mt-md-0">
                <a href="{{ route('clientes.create') }}" class="btn btn-rv-gold btn-sm d-flex align-items-center gap-1 shadow-sm">
                    <i class="bi bi-person-plus"></i> Registrar Cliente
                </a>
            </div>
        </div>

        <!-- Filtros -->
        <div class="card card-custom p-3 mb-4">
            <form method="GET" action="{{ route('clientes.index') }}" class="row g-2 align-items-center">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Buscar por nombre, correo o teléfono..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <select name="nivel_id" class="form-select">
                        <option value="">Todos los Niveles de Lealtad</option>
                        @foreach($niveles as $nv)
                            <option value="{{ $nv->id }}" {{ request('nivel_id') == $nv->id ? 'selected' : '' }}>
                                {{ $nv->nombre }} (Desde {{ number_format($nv->puntos_minimos) }} pts)
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-rv-primary w-100">Buscar</button>
                    @if(request()->hasAny(['search', 'nivel_id']))
                        <a href="{{ route('clientes.index') }}" class="btn btn-outline-secondary"><i class="bi bi-x-lg"></i></a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Listado -->
        <div class="card card-custom">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light small text-uppercase">
                        <tr>
                            <th>Huésped / Cliente</th>
                            <th>Nivel Lealtad</th>
                            <th>Saldo de Puntos</th>
                            <th>Contacto</th>
                            <th>Alergias Conocidas</th>
                            <th>Acciones</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($clientes as $cli)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-circle bg-dark text-warning p-2 fw-bold small">
                                            {{ substr($cli->nombres, 0, 1) }}{{ substr($cli->apellidos, 0, 1) }}
                                        </div>
                                        <div>
                                            <a href="{{ route('clientes.show', $cli->id) }}" class="fw-bold text-dark text-decoration-none d-block">
                                                {{ $cli->nombre_completo }}
                                            </a>
                                            <small class="text-muted">Miembro desde {{ \Carbon\Carbon::parse($cli->fecha_alta)->format('M Y') }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                <span class="badge {{ $cli->nivelLealtad && str_contains($cli->nivelLealtad->nombre, 'Black') ? 'badge-black' : 'badge-oro' }}">
                                    {{ $cli->nivelLealtad ? $cli->nivelLealtad->nombre : 'Bronce' }}
                                </span>
                                </td>
                                <td>
                                    <span class="fw-bold text-dark fs-6">{{ number_format($cli->puntos_saldo) }}</span>
                                    <small class="text-muted d-block">{{ number_format($cli->puntos_acumulados) }} acumulados</small>
                                </td>
                                <td>
                                    <div><i class="bi bi-envelope me-1 text-muted"></i>{{ $cli->correo }}</div>
                                    <small class="text-muted"><i class="bi bi-telephone me-1"></i>{{ $cli->telefono ?? 'Sin teléfono' }}</small>
                                </td>
                                <td>
                                    @if($cli->alergias->isNotEmpty())
                                        @foreach($cli->alergias as $al)
                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle small">
                                            {{ $al->nombre }}
                                        </span>
                                        @endforeach
                                    @else
                                        <span class="badge bg-success-subtle text-success border border-success-subtle small">Ninguna</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('clientes.show', $cli->id) }}" class="btn btn-sm btn-light border" title="Ver Expediente">
                                        <i class="bi bi-eye"></i> Ver Expediente
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">No se encontraron clientes registrados.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="mt-4">
            {{ $clientes->links() }}
        </div>
    </div>
@endsection
