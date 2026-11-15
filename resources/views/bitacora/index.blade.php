@extends('layouts.app')

@section('title', 'Auditoría & Bitácora')

@section('content')
<div class="container-fluid px-lg-4">
    <div class="mb-4">
        <h1 class="h3 fw-bold text-dark mb-1">Auditoría del Sistema & Solicitudes ARCO</h1>
        <p class="text-muted mb-0 small">Trazabilidad inmutable de operaciones, accesos y cumplimiento normativo de privacidad de datos.</p>
    </div>

    <div class="card card-custom mb-4">
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
                            <td colspan="5" class="text-center py-4 text-muted">Sin eventos registrados.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div>
        {{ $bitacoras->links() }}
    </div>
</div>
@endsection