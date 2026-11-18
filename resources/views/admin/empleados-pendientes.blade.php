@extends('layouts.app')

@section('title', 'Aprobación de Cuentas de Empleados')

@section('content')
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 fw-bold text-dark mb-1">Aprobación y Activación de Cuentas</h1>
                <p class="text-muted small mb-0">Gestione el acceso del personal nuevo asignando su rol correspondiente.</p>
            </div>
            <a href="{{ route('home') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left"></i> Volver al Dashboard
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card card-custom shadow-sm border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-dark small text-uppercase">
                        <tr>
                            <th>Nombre Completo</th>
                            <th>Correo Electrónico</th>
                            <th>Fecha de Registro</th>
                            <th style="width: 35%;">Asignar Rol</th>
                            <th class="text-center" style="width: 20%;">Acción</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($usuariosPendientes as $user)
                            <form action="{{ route('admin.empleados.aprobar', $user->id) }}" method="POST">
                                @csrf
                                <tr>
                                    <td class="fw-bold text-dark">{{ $user->name }}</td>
                                    <td><span class="text-muted">{{ $user->email }}</span></td>
                                    <td>{{ $user->created_at ? $user->created_at->format('d/m/Y H:i') : 'N/A' }}</td>

                                    <!-- Selector de Roles -->
                                    <td>
                                        <select name="rol_id" class="form-select form-select-sm" required>
                                            <option value="" disabled selected>Seleccione un rol...</option>
                                            @foreach($roles as $rol)
                                                <option value="{{ $rol->id }}">{{ $rol->nombre }}</option>
                                            @endforeach
                                        </select>
                                    </td>

                                    <!-- Botón de Aprobación -->
                                    <td class="text-center">
                                        <button type="submit" class="btn btn-rv-gold btn-sm w-100 fw-bold shadow-sm">
                                            <i class="bi bi-check-lg"></i> Aprobar y Activar
                                        </button>
                                    </td>
                                </tr>
                            </form>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox fs-2 d-block mb-2 text-secondary"></i>
                                    No hay solicitudes de empleados pendientes de aprobación en este momento.
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
