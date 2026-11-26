@extends('layouts.app')

@section('title', 'Abrir Comanda')

@push('styles')
    <style>
        .select-tile {
            cursor: pointer;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            background-color: #ffffff;
            transition: all 0.18s ease-in-out;
            position: relative;
        }

        .select-tile:hover {
            border-color: var(--rv-gold-dark);
            background-color: #fffdf8;
            transform: translateY(-2px);
            box-shadow: 0 6px 14px rgba(11, 29, 38, 0.06);
        }

        .tile-input:checked + .select-tile {
            border-color: var(--rv-navy);
            background-color: rgba(251, 215, 132, 0.14);
            box-shadow: 0 6px 16px rgba(11, 29, 38, 0.1);
        }

        .tile-input:checked + .select-tile .tile-check {
            opacity: 1;
            transform: scale(1);
        }

        .tile-check {
            position: absolute;
            top: 10px;
            right: 10px;
            width: 22px;
            height: 22px;
            border-radius: 50%;
            background-color: var(--rv-navy);
            color: var(--rv-gold);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.78rem;
            opacity: 0;
            transform: scale(0.7);
            transition: all 0.18s ease;
        }
    </style>
@endpush

@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card card-custom p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
                        <div>
                            <h2 class="h4 fw-bold text-dark mb-1">Abrir Nueva Comanda</h2>
                            <p class="text-muted small mb-0">Selecciona la sucursal, la mesa o reservación activa y el mesero responsable.</p>
                        </div>
                        <a href="{{ route('comandas.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-arrow-left"></i> Volver
                        </a>
                    </div>

                    <form method="POST" action="{{ route('comandas.store') }}">
                        @csrf

                        <!-- 1. SUCURSAL -->
                        <div class="mb-4">
                            <label class="form-label fw-bold text-dark d-flex align-items-center gap-2 mb-3">
                                <span class="badge rounded-pill" style="background-color: var(--rv-navy); color: var(--rv-gold);">1</span>
                                Sucursal *
                            </label>
                            <div class="row g-3">
                                @foreach($sucursales as $suc)
                                    @php
                                        $isCheckedSuc = old('sucursal_id') == $suc->id || ($sucursales->count() === 1 && !old('sucursal_id'));
                                    @endphp
                                    <div class="col-sm-6 col-md-4">
                                        <input type="radio"
                                               class="btn-check tile-input"
                                               name="sucursal_id"
                                               id="sucursal_{{ $suc->id }}"
                                               value="{{ $suc->id }}"
                                               {{ $isCheckedSuc ? 'checked' : '' }}
                                               required>
                                        <label class="select-tile d-block p-3 h-100" for="sucursal_{{ $suc->id }}">
                                            <span class="tile-check"><i class="bi bi-check-lg"></i></span>
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="rounded-3 p-2 fs-4" style="background-color: rgba(11, 29, 38, 0.06); color: var(--rv-navy);">
                                                    <i class="bi bi-buildings"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-bold text-dark">{{ $suc->nombre }}</div>
                                                    <small class="text-muted">{{ $suc->ciudad ?? 'Sucursal Activa' }}</small>
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- 2. RESERVACIÓN / MESA ACTIVA -->
                        <div class="mb-4 pt-3 border-top">
                            <label class="form-label fw-bold text-dark d-flex align-items-center gap-2 mb-3">
                                <span class="badge rounded-pill" style="background-color: var(--rv-navy); color: var(--rv-gold);">2</span>
                                Reservación / Mesa Activa (Opcional)
                            </label>
                            <div class="row g-3">
                                <!-- Opción Venta Directa -->
                                <div class="col-sm-6 col-md-4">
                                    <input type="radio"
                                           class="btn-check tile-input"
                                           name="reservacion_id"
                                           id="res_directa"
                                           value=""
                                        {{ old('reservacion_id') === null || old('reservacion_id') === '' ? 'checked' : '' }}>
                                    <label class="select-tile d-block p-3 h-100" for="res_directa">
                                        <span class="tile-check"><i class="bi bi-check-lg"></i></span>
                                        <div class="d-flex align-items-start gap-3">
                                            <div class="rounded-3 p-2 fs-4 bg-warning-subtle text-warning">
                                                <i class="bi bi-shop"></i>
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark">Venta directa en salón</div>
                                                <small class="text-muted d-block">Sin reservación previa (Walk-in)</small>
                                            </div>
                                        </div>
                                    </label>
                                </div>

                                <!-- Reservaciones Activas -->
                                @foreach($reservaciones as $res)
                                    @php
                                        $mesaCodigo = optional(optional($res->mesasAsignadas->first())->mesaSucursal)->mesa->codigo_mesa ?? null;
                                    @endphp
                                    <div class="col-sm-6 col-md-4">
                                        <input type="radio"
                                               class="btn-check tile-input"
                                               name="reservacion_id"
                                               id="res_{{ $res->id }}"
                                               value="{{ $res->id }}"
                                            {{ old('reservacion_id') == $res->id ? 'checked' : '' }}>
                                        <label class="select-tile d-block p-3 h-100" for="res_{{ $res->id }}">
                                            <span class="tile-check"><i class="bi bi-check-lg"></i></span>
                                            <div class="d-flex justify-content-between align-items-center mb-1 pe-4">
                                                <span class="badge bg-dark">#RES-{{ $res->id }}</span>
                                                @if($mesaCodigo)
                                                    <span class="badge bg-success-subtle text-success border border-success-subtle">
                                                    Mesa {{ $mesaCodigo }}
                                                </span>
                                                @endif
                                            </div>
                                            <div class="fw-bold text-dark text-truncate mt-1">
                                                {{ $res->cliente ? $res->cliente->nombre_completo : 'Cliente' }}
                                            </div>
                                            <div class="small text-muted mt-1">
                                                <i class="bi bi-people me-1"></i>{{ $res->cantidad_personas }} pax
                                                @if($res->fecha_hora_inicio)
                                                    • <i class="bi bi-clock ms-1 me-1"></i>{{ \Carbon\Carbon::parse($res->fecha_hora_inicio)->format('H:i') }}
                                                @endif
                                            </div>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- 3. MESERO / CAPITÁN DE SALA -->
                        <div class="mb-4 pt-3 border-top">
                            <label class="form-label fw-bold text-dark d-flex align-items-center gap-2 mb-3">
                                <span class="badge rounded-pill" style="background-color: var(--rv-navy); color: var(--rv-gold);">3</span>
                                Mesero / Capitán de Sala a Cargo *
                            </label>
                            <div class="row g-3">
                                @forelse($empleados as $emp)
                                    <div class="col-sm-6 col-md-4">
                                        <input type="radio"
                                               class="btn-check tile-input"
                                               name="empleado_id"
                                               id="emp_{{ $emp->id }}"
                                               value="{{ $emp->id }}"
                                               {{ old('empleado_id') == $emp->id ? 'checked' : '' }}
                                               required>
                                        <label class="select-tile d-block p-3 h-100" for="emp_{{ $emp->id }}">
                                            <span class="tile-check"><i class="bi bi-check-lg"></i></span>
                                            <div class="d-flex align-items-center gap-3 pe-3">
                                                <div class="rounded-circle fw-bold d-flex align-items-center justify-content-center flex-shrink-0"
                                                     style="width: 40px; height: 40px; background-color: var(--rv-navy); color: var(--rv-gold);">
                                                    {{ substr($emp->nombres ?? $emp->nombre_completo, 0, 1) }}
                                                </div>
                                                <div class="overflow-hidden">
                                                    <div class="fw-bold text-dark text-truncate">{{ $emp->nombre_completo }}</div>
                                                    <small class="text-muted d-block text-truncate">{{ $emp->puesto }}</small>
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                @empty
                                    <div class="col-12">
                                        <div class="alert alert-warning mb-0 small">
                                            No hay empleados activos disponibles en esta sucursal.
                                        </div>
                                    </div>
                                @endforelse
                            </div>
                        </div>

                        <!-- ACCIONES -->
                        <div class="pt-4 border-top d-flex justify-content-end gap-2">
                            <a href="{{ route('comandas.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                            <button type="submit" class="btn btn-rv-primary px-4">
                                <i class="bi bi-receipt-cutoff me-1"></i> Abrir Comanda & Ordenar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
