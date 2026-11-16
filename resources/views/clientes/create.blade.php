@extends('layouts.app')

@section('title', 'Registrar Cliente')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card card-custom p-4">
                <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
                    <div>
                        <h2 class="h4 fw-bold text-dark mb-1">Registrar Nuevo Huésped / Cliente</h2>
                        <p class="text-muted small mb-0">Alta en el sistema de lealtad, preferencias y expediente dietético.</p>
                    </div>
                    <a href="{{ route('clientes.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-arrow-left"></i> Volver
                    </a>
                </div>

                <form method="POST" action="{{ route('clientes.store') }}">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nombres *</label>
                            <input type="text" name="nombres" class="form-control" required value="{{ old('nombres') }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Apellidos *</label>
                            <input type="text" name="apellidos" class="form-control" required value="{{ old('apellidos') }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Correo Electrónico *</label>
                            <input type="email" name="correo" class="form-control" required value="{{ old('correo') }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Teléfono</label>
                            <input type="text" name="telefono" class="form-control" placeholder="7221234567" value="{{ old('telefono') }}">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Fecha de Nacimiento</label>
                            <input type="date" name="fecha_nacimiento" class="form-control" value="{{ old('fecha_nacimiento') }}">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Sucursal de Alta *</label>
                            <select name="sucursal_alta_id" class="form-select" required>
                                @foreach($sucursales as $suc)
                                    <option value="{{ $suc->id }}" {{ old('sucursal_alta_id') == $suc->id ? 'selected' : '' }}>
                                        {{ $suc->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Nivel Inicial de Lealtad *</label>
                            <select name="nivel_lealtad_id" class="form-select" required>
                                @foreach($niveles as $nv)
                                    <option value="{{ $nv->id }}" {{ old('nivel_lealtad_id') == $nv->id ? 'selected' : '' }}>
                                        {{ $nv->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Alergia Inicial -->
                        <div class="col-12 pt-3 border-top">
                            <label class="form-label fw-bold text-danger"><i class="bi bi-shield-exclamation me-1"></i> Alergia Conocida Inicial (Opcional)</label>
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <select name="alergia_id" class="form-select">
                                        <option value="">Sin alergias conocidas</option>
                                        @foreach($alergias as $al)
                                            <option value="{{ $al->id }}">{{ $al->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <select name="severidad_id" class="form-select">
                                        @foreach($severidades as $sev)
                                            <option value="{{ $sev->id }}">{{ $sev->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12">
                                    <input type="text" name="notas_alergia" class="form-control" placeholder="Detalles de la reacción o advertencias para el mesero...">
                                </div>
                            </div>
                        </div>

                        <div class="col-12 pt-4 border-top d-flex justify-content-end gap-2">
                            <a href="{{ route('clientes.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                            <button type="submit" class="btn btn-rv-primary px-4">Guardar y Abrir Expediente</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection