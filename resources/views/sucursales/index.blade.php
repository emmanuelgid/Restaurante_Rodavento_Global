@extends('layouts.app')

@section('title', 'Sucursales')

@section('content')
<div class="container-fluid px-lg-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
        <div>
            <h1 class="h3 fw-bold text-dark mb-1">Sucursales del Grupo Rodavento</h1>
            <p class="text-muted mb-0 small">Administración de unidades gastronómicas, ubicaciones y asignación de salones.</p>
        </div>
        <div class="mt-3 mt-md-0">
            <button type="button" class="btn btn-rv-gold btn-sm" data-bs-toggle="modal" data-bs-target="#sucursalModal">
                <i class="bi bi-building-add"></i> Nueva Sucursal
            </button>
        </div>
    </div>

    <!-- Modal Nueva Sucursal -->
    <div class="modal fade" id="sucursalModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="{{ route('sucursales.store') }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">Registrar Sucursal</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nombre de Sucursal *</label>
                            <input type="text" name="nombre" class="form-control" required placeholder="Ej. Rodavento Valle de Bravo">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Dirección Completa *</label>
                            <input type="text" name="direccion" class="form-control" required placeholder="Calle, Número, Colonia, Valle de Bravo">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Teléfono de Contacto</label>
                            <input type="text" name="telefono" class="form-control" placeholder="7262628900">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar Sucursal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="row g-4">
        @foreach($sucursales as $suc)
        <div class="col-md-6">
            <div class="card card-custom h-100 p-4">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <h4 class="fw-bold text-dark mb-1">{{ $suc->nombre }}</h4>
                        <div class="text-muted small"><i class="bi bi-geo-alt me-1 text-danger"></i>{{ $suc->direccion }}</div>
                        <div class="text-muted small"><i class="bi bi-telephone me-1 text-primary"></i>{{ $suc->telefono ?? 'Sin teléfono' }}</div>
                    </div>
                    <span class="badge {{ $suc->activa ? 'bg-success' : 'bg-secondary' }}">
                        {{ $suc->activa ? 'Operando' : 'Inactiva' }}
                    </span>
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <div class="p-2 bg-light rounded text-center border">
                            <div class="fs-4 fw-bold text-dark">{{ $suc->mesas->count() }}</div>
                            <small class="text-muted">Mesas en Salón</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-2 bg-light rounded text-center border">
                            <div class="fs-4 fw-bold text-dark">{{ $suc->empleados->count() }}</div>
                            <small class="text-muted">Personal Asignado</small>
                        </div>
                    </div>
                </div>

                <div class="mt-auto pt-2 border-top d-flex justify-content-between align-items-center">
                    <a href="{{ route('mesas.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-grid-3x3-gap"></i> Ver Plano de Mesas
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
