@extends('layouts.app')

@section('title', 'Nueva Reservación')

@push('styles')
    <!-- Tom Select CSS (Estilos para el buscador dinámico) -->
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
    <style>
        /* Ajuste visual para que coincida con la estética del formulario */
        .ts-control {
            border-radius: 0.375rem;
            padding: 0.375rem 0.75rem;
        }

        /* Estilos para la cuadrícula de mesas */
        .mesa-card-label {
            cursor: pointer;
            border: 2px solid #e2e8f0;
            border-radius: 0.65rem;
            background-color: #ffffff;
            transition: all 0.18s ease-in-out;
            user-select: none;
        }

        .mesa-card-label:hover {
            border-color: #d8b257;
            background-color: #fbf9f5;
            transform: translateY(-2px);
        }

        .btn-check:checked + .mesa-card-label {
            background-color: #0b1d26;
            border-color: #fbd784;
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(11, 29, 38, 0.2);
        }

        .btn-check:checked + .mesa-card-label .mesa-titulo {
            color: #fbd784 !important;
        }

        .btn-check:checked + .mesa-card-label .mesa-capacidad {
            color: rgba(255, 255, 255, 0.85) !important;
        }
    </style>
@endpush

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
                                <select id="select-cliente" name="cliente_id" class="form-select" required>
                                    <option value="">Seleccione o busque al cliente...</option>
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
                                    <option value="2" {{ old('horas_duracion') == '2' ? 'selected' : '' }}>2 Horas</option>
                                    <option value="3" {{ old('horas_duracion') == '3' ? 'selected' : '' }}>3 Horas</option>
                                    <option value="4" {{ old('horas_duracion') == '4' ? 'selected' : '' }}>4 Horas (Sobremesa)</option>

                                    @if($hayEventoActivo)
                                        <option value="0" {{ old('horas_duracion') == '0' ? 'selected' : '' }}>Evento (Indefinido)</option>
                                    @endif
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Personas (Pax) *</label>
                                <input type="number" name="cantidad_personas" class="form-control" min="1" max="50" required value="{{ old('cantidad_personas', 2) }}">
                            </div>

                            <!-- Cuadrícula de Selección de Mesa -->
                            <div class="col-12">
                                <label class="form-label fw-semibold d-block mb-2">Seleccionar Mesa en Sucursal *</label>

                                @forelse($sucursales as $suc)
                                    <div class="mb-3 p-3 rounded-3 bg-light border">
                                        <div class="small fw-bold text-uppercase text-secondary mb-2">
                                            <i class="bi bi-building me-1"></i> {{ $suc->nombre }}
                                        </div>

                                        @if($suc->mesas->isNotEmpty())
                                            <div class="row g-2">
                                                @foreach($suc->mesas as $ms)
                                                    <div class="col-6 col-sm-4 col-md-3">
                                                        <input type="radio"
                                                               class="btn-check"
                                                               name="mesa_sucursal_id"
                                                               id="mesa_{{ $ms->id }}"
                                                               value="{{ $ms->id }}"
                                                               autocomplete="off"
                                                               {{ (string) old('mesa_sucursal_id') === (string) $ms->id ? 'checked' : '' }}
                                                               required>
                                                        <label class="mesa-card-label w-100 h-100 p-2 text-center d-flex flex-column justify-content-center align-items-center" for="mesa_{{ $ms->id }}">
                                                            <i class="bi bi-grid-1x2-fill fs-5 mb-1 mesa-titulo text-warning"></i>
                                                            <div class="fw-bold small mesa-titulo text-dark">
                                                                {{ $ms->mesa->codigo_mesa ?? 'Mesa' }}
                                                            </div>
                                                            <div class="mesa-capacidad text-muted" style="font-size: 0.75rem;">
                                                                <i class="bi bi-people"></i> {{ $ms->mesa->capacidad ?? 4 }} pers.
                                                            </div>
                                                        </label>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <div class="text-muted small fst-italic">No hay mesas registradas en esta sucursal.</div>
                                        @endif
                                    </div>
                                @empty
                                    <div class="alert alert-warning small mb-0">No hay sucursales con mesas disponibles.</div>
                                @endforelse

                                @error('mesa_sucursal_id')
                                <div class="text-danger small fw-bold mt-1">
                                    <i class="bi bi-exclamation-circle"></i> {{ $message }}
                                </div>
                                @enderror
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

@push('scripts')
    <!-- Tom Select JS (Inicialización del buscador) -->
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            new TomSelect('#select-cliente', {
                create: false,
                sortField: {
                    field: "text",
                    direction: "asc"
                },
                placeholder: "Escribe el nombre o correo del cliente..."
            });
        });
    </script>
@endpush
