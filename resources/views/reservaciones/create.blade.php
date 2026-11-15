@extends('layouts.app')

@section('title', 'Nueva Reservación')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card card-custom p-4">
                <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
                    <div>
                        <h2 class="h4 fw-bold text-dark mb-1">Crear Nueva Reservación</h2>
                        <p class="text-muted small mb-0">Asigna cliente, horario de estancia y mesa en sucursal.</p>
                    </div>
                    <a href="{{ route('reservaciones.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-arrow-left"></i> Volver
                    </a>
                </div>

                <form method="POST" action="{{ route('reservaciones.store') }}">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Cliente / Huésped *</label>
                            <select name="cliente_id" class="form-select" required>
                                <option value="">Seleccione al cliente...</option>
                                @foreach($clientes as $cli)
                                    <option value="{{ $cli->id }}" {{ old('cliente_id') == $cli->id ? 'selected' : '' }}>
                                        {{ $cli->nombre_completo }} — {{ $cli->correo }} ({{ $cli->puntos_saldo }} pts)
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text small">¿Es un cliente nuevo? <a href="{{ route('clientes.create') }}" target="_blank">Regístralo aquí</a>.</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Fecha y Hora de Llegada *</label>
                            <input type="datetime-local" name="fecha_hora_inicio" class="form-control" required value="{{ old('fecha_hora_inicio', now()->addHour()->format('Y-m-d\TH:i')) }}">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Duración Estimada</label>
                            <select name="horas_duracion" class="form-select">
                                <option value="2">2 Horas</option>
                                <option value="3">3 Horas</option>
                                <option value="4">4 Horas (Sobremesa)</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Personas (Pax) *</label>
                            <input type="number" name="cantidad_personas" class="form-control" min="1" max="50" required value="{{ old('cantidad_personas', 2) }}">
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Seleccionar Mesa en Sucursal *</label>
                            <select name="mesa_sucursal_id" class="form-select" required>
                                <option value="">Seleccionar mesa disponible...</option>
                                @foreach($sucursales as $suc)
                                    <optgroup label="{{ $suc->nombre }}">
                                        @foreach($suc->mesas as $ms)
                                            <option value="{{ $ms->id }}">
                                                Mesa {{ $ms->mesa->codigo_mesa ?? 'Mesa' }} (Capacidad: {{ $ms->mesa->capacidad ?? 4 }} personas)
                                            </option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Notas Especiales / Peticiones del Huésped</label>
                            <textarea name="notas" rows="3" class="form-control" placeholder="Ej. Celebración de aniversario, preferencia por mesa junto al ventanal del lago, silla alta para bebé...">{{ old('notas') }}</textarea>
                        </div>

                        <div class="col-12 pt-4 border-top d-flex justify-content-end gap-2">
                            <a href="{{ route('reservaciones.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                            <button type="submit" class="btn btn-rv-primary px-4">Confirmar y Asignar Mesa</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection