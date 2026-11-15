@extends('layouts.app')

@section('title', 'Programa de Lealtad VIP')

@section('content')
<div class="container-fluid px-lg-4">
    <div class="mb-4">
        <h1 class="h3 fw-bold text-dark mb-1">Programa de Fidelización & Beneficios VIP</h1>
        <p class="text-muted mb-0 small">Configuración de niveles de membresía, catálogo de beneficios exclusivos y reglas de acumulación de puntos.</p>
    </div>

    <!-- Tarjetas de Niveles -->
    <div class="row g-4 mb-4">
        @foreach($niveles as $nv)
        <div class="col-sm-6 col-lg-3">
            <div class="card card-custom h-100 p-4 border-top border-4 {{ str_contains($nv->nombre, 'Black') ? 'border-dark' : 'border-warning' }}">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h5 class="fw-bold text-dark mb-0">{{ $nv->nombre }}</h5>
                    <i class="bi bi-award-fill text-warning fs-4"></i>
                </div>
                <div class="fs-4 fw-bold text-primary my-2">{{ number_format($nv->puntos_minimos) }} <span class="fs-6 text-muted">pts mín.</span></div>
                <p class="small text-muted mb-3">Socios activos: <strong>{{ $nv->clientes->count() }}</strong></p>

                <div class="pt-2 border-top">
                    <span class="small fw-semibold text-dark d-block mb-1">Beneficios incluidos:</span>
                    <ul class="list-unstyled small mb-0">
                        @forelse($nv->beneficios as $ben)
                            <li><i class="bi bi-check-circle-fill text-success me-1"></i>{{ $ben->nombre }}</li>
                        @empty
                            <li class="text-muted">Acumulación estándar de puntos.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Regla de Puntos y Movimientos Recientes -->
    <div class="row g-4">
        <div class="col-lg-5">
            <div class="card card-custom p-4 mb-4">
                <h5 class="fw-bold text-dark border-bottom pb-2 mb-3"><i class="bi bi-gear-fill me-2 text-primary"></i> Regla de Acumulación Activa</h5>
                @foreach($reglas as $reg)
                <div class="p-3 bg-light rounded border mb-2">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="fw-bold text-dark">${{ number_format($reg->monto_por_punto, 2) }} MXN gastados</span>
                        <span class="badge bg-success">= 1 Punto de Lealtad</span>
                    </div>
                    <small class="text-muted">Vigencia: {{ \Carbon\Carbon::parse($reg->vigencia_inicio)->format('d/m/Y') }} a {{ $reg->vigencia_fin ? \Carbon\Carbon::parse($reg->vigencia_fin)->format('d/m/Y') : 'Indefinida' }}</small>
                </div>
                @endforeach
            </div>

            <!-- Catálogo de Beneficios -->
            <div class="card card-custom p-4">
                <h5 class="fw-bold text-dark border-bottom pb-2 mb-3"><i class="bi bi-gift-fill me-2 text-danger"></i> Catálogo de Beneficios VIP</h5>
                <ul class="list-group list-group-flush">
                    @foreach($beneficios as $b)
                    <li class="list-group-item px-0 py-2">
                        <div class="fw-semibold text-dark">{{ $b->nombre }}</div>
                        <small class="text-muted">{{ $b->descripcion }}</small>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card card-custom">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-clock-history me-2 text-info"></i> Últimos Movimientos de Puntos de la Cadena</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light small text-uppercase">
                                <tr>
                                    <th>Fecha</th>
                                    <th>Huésped</th>
                                    <th>Tipo</th>
                                    <th>Puntos</th>
                                    <th>Nota</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($ultimosMovimientos as $mov)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($mov->fecha)->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <a href="{{ route('clientes.show', $mov->cliente_id) }}" class="fw-semibold text-dark text-decoration-none">
                                            {{ $mov->cliente ? $mov->cliente->nombre_completo : 'Cliente' }}
                                        </a>
                                    </td>
                                    <td><span class="badge bg-light text-dark border">{{ $mov->tipo }}</span></td>
                                    <td class="fw-bold {{ $mov->puntos > 0 ? 'text-success' : 'text-danger' }}">
                                        {{ $mov->puntos > 0 ? '+' : '' }}{{ number_format($mov->puntos) }} pts
                                    </td>
                                    <td>
                                        @if($mov->notaReservacion)
                                            <span class="small text-muted">Comanda #{{ $mov->notaReservacion->id }}</span>
                                        @else
                                            <span class="small text-muted">Manual</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">Sin movimientos registrados.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection