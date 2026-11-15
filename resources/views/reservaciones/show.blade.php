@extends('layouts.app')

@section('title', 'Detalle de Reservación #RES-' . $reservacion->id)

@section('content')
<div class="container-fluid px-lg-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <h1 class="h3 fw-bold text-dark mb-0">Reservación #RES-{{ $reservacion->id }}</h1>
                @php
                    $pillClass = match($reservacion->estatus) {
                        'confirmada' => 'bg-success-subtle text-success border border-success',
                        'en_mesa' => 'bg-primary-subtle text-primary border border-primary',
                        'concluida' => 'bg-secondary-subtle text-secondary',
                        'cancelada' => 'bg-danger-subtle text-danger',
                        default => 'bg-warning-subtle text-warning'
                    };
                @endphp
                <span class="status-pill {{ $pillClass }}">{{ $reservacion->estatus }}</span>
            </div>
            <p class="text-muted mb-0 small">Registrada el {{ \Carbon\Carbon::parse($reservacion->created_at)->format('d/m/Y H:i') }}</p>
        </div>
        <div class="d-flex gap-2 mt-3 mt-md-0">
            <a href="{{ route('reservaciones.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left"></i> Volver a Lista
            </a>
            @if($reservacion->nota)
                <a href="{{ route('comandas.show', $reservacion->nota->id) }}" class="btn btn-rv-primary btn-sm">
                    <i class="bi bi-receipt"></i> Ver Comanda Abierta
                </a>
            @endif
        </div>
    </div>

    <!-- Alertas de Alérgenos si el cliente tiene -->
    @if($reservacion->cliente && $reservacion->cliente->alergias->isNotEmpty())
    <div class="alert alert-danger shadow-sm border-danger d-flex align-items-center mb-4" role="alert">
        <i class="bi bi-shield-fill-exclamation fs-3 me-3 text-danger"></i>
        <div>
            <div class="fw-bold fs-6">¡ATENCIÓN SALA Y COCINA! El Huésped presenta las siguientes alergias:</div>
            <div class="d-flex flex-wrap gap-2 mt-1">
                @foreach($reservacion->cliente->alergias as $al)
                    <span class="badge bg-danger text-white">
                        {{ $al->nombre }} ({{ $al->pivot->notas ?? 'Sin notas' }})
                    </span>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <div class="row g-4">
        <!-- Columna Izquierda: Información de Reservación y Cliente -->
        <div class="col-lg-5">
            <!-- Card Cliente -->
            <div class="card card-custom p-4 mb-4">
                <h5 class="fw-bold text-dark border-bottom pb-2 mb-3"><i class="bi bi-person-circle me-2 text-warning"></i> Expediente del Huésped</h5>
                @if($reservacion->cliente)
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="rounded-circle bg-dark text-warning p-3 fs-4 fw-bold">
                            {{ substr($reservacion->cliente->nombres, 0, 1) }}{{ substr($reservacion->cliente->apellidos, 0, 1) }}
                        </div>
                        <div>
                            <h5 class="mb-0 fw-bold">{{ $reservacion->cliente->nombre_completo }}</h5>
                            <div class="text-muted small">{{ $reservacion->cliente->correo }}</div>
                            <div class="text-muted small"><i class="bi bi-telephone me-1"></i>{{ $reservacion->cliente->telefono ?? 'Sin teléfono' }}</div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between p-2 rounded bg-light border mb-3">
                        <div>
                            <div class="small text-muted">Nivel de Lealtad</div>
                            <span class="badge {{ $reservacion->cliente->nivelLealtad && str_contains($reservacion->cliente->nivelLealtad->nombre, 'Black') ? 'badge-black' : 'badge-oro' }}">
                                {{ $reservacion->cliente->nivelLealtad->nombre ?? 'Bronce' }}
                            </span>
                        </div>
                        <div class="text-end">
                            <div class="small text-muted">Puntos Disponibles</div>
                            <div class="fw-bold text-dark">{{ number_format($reservacion->cliente->puntos_saldo) }} pts</div>
                        </div>
                    </div>

                    @if($reservacion->cliente->preferencias->isNotEmpty())
                    <div class="mb-2">
                        <span class="small fw-bold text-dark d-block mb-1">Preferencias Registradas:</span>
                        <ul class="list-unstyled small mb-0">
                            @foreach($reservacion->cliente->preferencias as $pref)
                                <li><i class="bi bi-check2 text-success me-1"></i><strong>{{ $pref->tipo }}:</strong> {{ $pref->descripcion }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                @endif
            </div>

            <!-- Card Control de Estatus -->
            <div class="card card-custom p-4">
                <h5 class="fw-bold text-dark border-bottom pb-2 mb-3"><i class="bi bi-arrow-repeat me-2 text-primary"></i> Transición de Estado</h5>
                <p class="small text-muted">Al pasar a <strong>"En Mesa"</strong>, se genera automáticamente la nota de comanda para que los meseros puedan cargar pedidos.</p>

                <form method="POST" action="{{ route('reservaciones.estatus', $reservacion->id) }}">
                    @csrf
                    <div class="mb-3">
                        <select name="estatus" class="form-select form-select-lg">
                            <option value="pendiente" {{ $reservacion->estatus == 'pendiente' ? 'selected' : '' }}>1. Pendiente</option>
                            <option value="confirmada" {{ $reservacion->estatus == 'confirmada' ? 'selected' : '' }}>2. Confirmada</option>
                            <option value="en_mesa" {{ $reservacion->estatus == 'en_mesa' ? 'selected' : '' }}>3. En Mesa (Ocupada)</option>
                            <option value="concluida" {{ $reservacion->estatus == 'concluida' ? 'selected' : '' }}>4. Concluida (Liberar Mesa)</option>
                            <option value="cancelada" {{ $reservacion->estatus == 'cancelada' ? 'selected' : '' }}>5. Cancelada</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-rv-primary w-100">Actualizar Estado</button>
                </form>
            </div>
        </div>

        <!-- Columna Derecha: Detalles del Servicio y Mesa -->
        <div class="col-lg-7">
            <div class="card card-custom p-4 mb-4">
                <h5 class="fw-bold text-dark border-bottom pb-2 mb-3"><i class="bi bi-grid-3x3-gap-fill me-2 text-success"></i> Asignación de Mesa & Estancia</h5>
                <div class="row g-3">
                    <div class="col-sm-6">
                        <div class="p-3 bg-light rounded border">
                            <div class="text-muted small">Horario de Llegada</div>
                            <div class="fs-5 fw-bold text-dark"><i class="bi bi-clock me-1 text-primary"></i>{{ \Carbon\Carbon::parse($reservacion->fecha_hora_inicio)->format('d/m/Y H:i') }}</div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="p-3 bg-light rounded border">
                            <div class="text-muted small">Horario de Salida Estimado</div>
                            <div class="fs-5 fw-bold text-dark"><i class="bi bi-clock-history me-1 text-secondary"></i>{{ \Carbon\Carbon::parse($reservacion->fecha_hora_final)->format('d/m/Y H:i') }}</div>
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div class="p-3 bg-light rounded border">
                            <div class="text-muted small">Capacidad / Ocupantes</div>
                            <div class="fs-5 fw-bold text-dark"><i class="bi bi-people-fill me-1 text-info"></i>{{ $reservacion->cantidad_personas }} Personas</div>
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div class="p-3 bg-light rounded border">
                            <div class="text-muted small">Mesa en Sala</div>
                            <div class="fs-5 fw-bold text-dark">
                                @if($reservacion->mesasAsignadas->isNotEmpty())
                                    <span class="badge bg-dark fs-6">{{ $reservacion->mesasAsignadas->first()->mesaSucursal->mesa->codigo_mesa ?? 'Mesa' }}</span>
                                    <span class="small text-muted">({{ $reservacion->mesasAsignadas->first()->mesaSucursal->sucursal->nombre ?? 'Rodavento' }})</span>
                                @else
                                    <span class="badge bg-danger">Sin Mesa</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="p-3 bg-light rounded border">
                            <div class="text-muted small mb-1">Notas de Servicio & Peticiones</div>
                            <p class="mb-0 text-dark">{{ $reservacion->notas ?: 'Sin notas adicionales para esta reservación.' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Resumen de Comanda vinculada si existe -->
            @if($reservacion->nota)
            <div class="card card-custom p-4">
                <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
                    <h5 class="fw-bold text-dark mb-0"><i class="bi bi-receipt-cutoff me-2 text-primary"></i> Comanda de Consumo #{{ $reservacion->nota->id }}</h5>
                    <a href="{{ route('comandas.show', $reservacion->nota->id) }}" class="btn btn-sm btn-rv-gold">Administrar Comanda</a>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <span class="text-muted small">Mesero asignado:</span>
                        <strong class="d-block">{{ $reservacion->nota->empleado ? $reservacion->nota->empleado->nombre_completo : 'No asignado' }}</strong>
                    </div>
                    <div class="text-end">
                        <span class="text-muted small">Total acumulado:</span>
                        <h4 class="mb-0 fw-bold text-success">${{ number_format($reservacion->nota->total, 2) }}</h4>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection