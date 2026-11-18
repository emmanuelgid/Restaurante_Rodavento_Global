@extends('layouts.app')

@section('title', 'Mesas & Distribución de Sala')

@section('content')
<div class="container-fluid px-lg-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
        <div>
            <h1 class="h3 fw-bold text-dark mb-1">Distribución de Mesas en Sala</h1>
            <p class="text-muted mb-0 small">Catálogo de mesas, capacidades y vinculación con sucursales.</p>
        </div>
        <div class="mt-3 mt-md-0">
            @can('mesas.admin')
            <button type="button" class="btn btn-rv-gold btn-sm" data-bs-toggle="modal" data-bs-target="#mesaModal">
                <i class="bi bi-plus-circle"></i> Agregar Mesa
            </button>
            @endcan
        </div>
    </div>

    <!-- Modal Agregar Mesa -->
    <div class="modal fade" id="mesaModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="{{ route('mesas.store') }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">Registrar Mesa</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Código de Mesa *</label>
                            <input type="text" name="codigo_mesa" class="form-control" required placeholder="Ej. LAGO-04, ROOF-03" maxlength="10">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Capacidad de Huéspedes *</label>
                            <input type="number" name="capacidad" class="form-control" required min="1" max="30" value="4">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Sucursal a la que pertenece *</label>
                            <select name="sucursal_id" class="form-select" required>
                                @foreach($sucursales as $s)
                                    <option value="{{ $s->id }}">{{ $s->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Registrar Mesa</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Grid de Mesas -->
    <div class="row g-3">
        @foreach($mesas as $m)
        <div class="col-sm-6 col-md-4 col-xl-3">
            <div class="card card-custom p-3 text-center h-100">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="badge bg-dark fs-6">{{ $m->codigo_mesa }}</span>
                    <span class="badge {{ $m->activa ? 'bg-success' : 'bg-danger' }}">
                        {{ $m->activa ? 'Habilitada' : 'Fuera de Servicio' }}
                    </span>
                </div>
                <div class="my-3">
                    <i class="bi bi-grid-fill text-warning" style="font-size: 2.5rem;"></i>
                    <div class="fs-5 fw-bold text-dark mt-2">{{ $m->capacidad }} Personas</div>
                    <small class="text-muted">Capacidad de Comensales</small>
                </div>
                <div class="pt-2 border-top small text-muted">
                    @if($m->mesasSucursal->isNotEmpty())
                        {{ $m->mesasSucursal->first()->sucursal->nombre ?? 'Rodavento' }}
                    @else
                        Sin sucursal
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
