@extends('layouts.app')

@section('title', 'Abrir Comanda')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="card card-custom p-4">
                <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
                    <div>
                        <h2 class="h4 fw-bold text-dark mb-1">Abrir Nueva Comanda</h2>
                        <p class="text-muted small mb-0">Asocia una mesa o reservación activa y el mesero responsable.</p>
                    </div>
                    <a href="{{ route('comandas.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-arrow-left"></i> Volver
                    </a>
                </div>

                <form method="POST" action="{{ route('comandas.store') }}">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Sucursal *</label>
                            <select name="sucursal_id" class="form-select" required>
                                <option value="">Seleccione sucursal...</option>
                                @foreach($sucursales as $suc)
                                    <option value="{{ $suc->id }}" {{ old('sucursal_id') == $suc->id ? 'selected' : '' }}>
                                        {{ $suc->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Reservación / Mesa Activa (Opcional)</label>
                            <select name="reservacion_id" class="form-select">
                                <option value="">Venta directa en salón (Sin reservación previa)</option>
                                @foreach($reservaciones as $res)
                                    <option value="{{ $res->id }}" {{ old('reservacion_id') == $res->id ? 'selected' : '' }}>
                                        #RES-{{ $res->id }} — {{ $res->cliente ? $res->cliente->nombre_completo : 'Cliente' }} ({{ $res->cantidad_personas }} pax)
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Mesero / Capitán de Sala a Cargo *</label>
                            <select name="empleado_id" class="form-select" required>
                                <option value="">Seleccione mesero...</option>
                                @foreach($empleados as $emp)
                                    <option value="{{ $emp->id }}" {{ old('empleado_id') == $emp->id ? 'selected' : '' }}>
                                        {{ $emp->nombre_completo }} — {{ $emp->puesto }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 pt-4 border-top d-flex justify-content-end gap-2">
                            <a href="{{ route('comandas.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                            <button type="submit" class="btn btn-rv-primary px-4">Abrir Comanda & Ordenar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection