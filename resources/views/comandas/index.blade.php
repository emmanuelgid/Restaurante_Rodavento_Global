@extends('layouts.app')

@section('title', 'Comandas & Terminal POS')

@section('content')
<div class="container-fluid px-lg-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
        <div>
            <h1 class="h3 fw-bold text-dark mb-1">Comandas en Sala & Punto de Venta</h1>
            <p class="text-muted mb-0 small">Seguimiento de pedidos activos a cocina y barra, control de cuentas y facturación.</p>
        </div>
        <div class="mt-3 mt-md-0">
            <a href="{{ route('comandas.create') }}" class="btn btn-rv-gold btn-sm d-flex align-items-center gap-1 shadow-sm">
                <i class="bi bi-receipt"></i> Abrir Nueva Comanda
            </a>
        </div>
    </div>

    <!-- Filtro por Sucursal -->
    <div class="card card-custom p-3 mb-4">
        <form method="GET" action="{{ route('comandas.index') }}" class="row g-2 align-items-center">
            <div class="col-md-8">
                <select name="sucursal_id" class="form-select">
                    <option value="">Todas las Sucursales</option>
                    @foreach($sucursales as $suc)
                        <option value="{{ $suc->id }}" {{ request('sucursal_id') == $suc->id ? 'selected' : '' }}>
                            {{ $suc->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 d-flex gap-2">
                <button type="submit" class="btn btn-rv-primary w-100">Filtrar</button>
                @if(request()->filled('sucursal_id'))
                    <a href="{{ route('comandas.index') }}" class="btn btn-outline-secondary"><i class="bi bi-x-lg"></i></a>
                @endif
            </div>
        </form>
    </div>

    <!-- Grid de Comandas -->
    <div class="row g-4">
        @forelse($notas as $nota)
        <div class="col-md-6 col-xl-4">
            <div class="card card-custom h-100 d-flex flex-column">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
                    <div>
                        <span class="fw-bold text-dark fs-6">Comanda #{{ $nota->id }}</span>
                        <div class="small text-muted">{{ $nota->sucursal->nombre ?? 'Sucursal' }}</div>
                    </div>
                    <span class="badge {{ $nota->total > 0 ? 'bg-success' : 'bg-primary' }} fs-6">
                        ${{ number_format($nota->total, 2) }}
                    </span>
                </div>

                <div class="card-body p-3 flex-grow-1">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted small">Mesa / Sala:</span>
                        <strong class="text-dark">
                            @if($nota->reservacion && $nota->reservacion->mesasAsignadas->isNotEmpty())
                                Mesa {{ $nota->reservacion->mesasAsignadas->first()->mesaSucursal->mesa->codigo_mesa ?? 'Mesa' }}
                            @else
                                Barra / Directo
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
                        <div class="small text-muted mb-1">Pedidos enviados:</div>
                        @php $totalItems = 0; @endphp
                        @foreach($nota->pedidos as $p)
                            @php $totalItems += $p->detalles->count(); @endphp
                        @endforeach
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="badge bg-secondary-subtle text-dark border">{{ $nota->pedidos->count() }} órdenes enviadas</span>
                            <span class="small fw-semibold text-primary">{{ $totalItems }} platillos en total</span>
                        </div>
                    </div>
                </div>

                <div class="card-footer bg-white py-3 border-top d-flex justify-content-between align-items-center">
                    <span class="small text-muted"><i class="bi bi-clock me-1"></i>{{ \Carbon\Carbon::parse($nota->fecha_emision)->format('d/m H:i') }}</span>
                    <a href="{{ route('comandas.show', $nota->id) }}" class="btn btn-sm btn-rv-primary">
                        <i class="bi bi-arrow-right-circle me-1"></i> Gestionar Cuenta
                    </a>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12 text-center py-5">
            <i class="bi bi-receipt-cutoff text-muted" style="font-size: 4rem;"></i>
            <h4 class="mt-3 text-muted">No hay comandas registradas</h4>
            <p class="text-muted small">Abra una nueva comanda para comenzar a ordenar platillos.</p>
        </div>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $notas->links() }}
    </div>
</div>
@endsection