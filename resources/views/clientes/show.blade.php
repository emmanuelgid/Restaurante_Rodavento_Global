@extends('layouts.app')

@section('title', 'Expediente Huésped: ' . $cliente->nombre_completo)

@section('content')
<div class="container-fluid px-lg-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-circle bg-dark text-warning p-3 fs-3 fw-bold">
                {{ substr($cliente->nombres, 0, 1) }}{{ substr($cliente->apellidos, 0, 1) }}
            </div>
            <div>
                <div class="d-flex align-items-center gap-2">
                    <h1 class="h3 fw-bold text-dark mb-0">{{ $cliente->nombre_completo }}</h1>
                    <span class="badge {{ $cliente->nivelLealtad && str_contains($cliente->nivelLealtad->nombre, 'Black') ? 'badge-black' : 'badge-oro' }}">
                        {{ $cliente->nivelLealtad ? $cliente->nivelLealtad->nombre : 'Bronce' }}
                    </span>
                </div>
                <p class="text-muted small mb-0">{{ $cliente->correo }} &bull; {{ $cliente->telefono ?? 'Sin teléfono' }}</p>
            </div>
        </div>
        <div class="d-flex gap-2 mt-3 mt-md-0">
            <a href="{{ route('clientes.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left"></i> Volver a Lista
            </a>
            <button type="button" class="btn btn-rv-gold btn-sm" data-bs-toggle="modal" data-bs-target="#puntosModal">
                <i class="bi bi-star-fill"></i> Bonificar / Ajustar Puntos
            </button>
        </div>
    </div>

    <!-- Modal Ajustar Puntos -->
    <div class="modal fade" id="puntosModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="{{ route('clientes.puntos', $cliente->id) }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">Ajuste de Puntos de Lealtad</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Tipo de Movimiento</label>
                            <select name="tipo" class="form-select" required>
                                <option value="bonificacion">Bonificación por Cortesía / Gerencia (+)</option>
                                <option value="redencion">Redención / Canje por Beneficio (-)</option>
                                <option value="ajuste">Ajuste Manual de Auditoría</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Puntos</label>
                            <input type="number" name="puntos" class="form-control" required placeholder="Ej. 500">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Motivo del Ajuste *</label>
                            <input type="text" name="motivo" class="form-control" required placeholder="Ej. Celebración de cumpleaños en sucursal">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Aplicar Movimiento</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Columna Izquierda: Información y Alergias -->
        <div class="col-lg-5">
            <!-- Saldo de Puntos Card -->
            <div class="card card-custom p-3 mb-4 bg-dark text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="small text-warning text-uppercase fw-semibold">Saldo Disponible</div>
                        <div class="display-6 fw-bold text-white my-1">{{ number_format($cliente->puntos_saldo) }} pts</div>
                        <div class="small text-white-50">Histórico acumulado: {{ number_format($cliente->puntos_acumulados) }} pts</div>
                    </div>
                    <i class="bi bi-gem text-warning" style="font-size: 3rem;"></i>
                </div>
            </div>

            <!-- Alergias y Dietas -->
            <div class="card card-custom p-4 mb-4 border-start border-4 border-danger">
                <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
                    <h5 class="fw-bold text-danger mb-0"><i class="bi bi-shield-fill-exclamation me-2"></i> Alergias Registradas</h5>
                    <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#alergiaModal">
                        <i class="bi bi-plus-lg"></i> Agregar
                    </button>
                </div>

                @forelse($cliente->alergias as $al)
                    <div class="p-2 mb-2 bg-danger-subtle text-danger rounded border border-danger-subtle d-flex justify-content-between align-items-center">
                        <div>
                            <strong>{{ $al->nombre }}</strong>
                            <div class="small text-muted">{{ $al->pivot->notas ?: 'Sin especificaciones' }}</div>
                        </div>
                        <span class="badge bg-danger">Alerta</span>
                    </div>
                @empty
                    <p class="text-muted small mb-0">No tiene alergias reportadas en su expediente.</p>
                @endforelse
            </div>

            <!-- Modal Agregar Alergia -->
            <div class="modal fade" id="alergiaModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form method="POST" action="{{ route('clientes.alergia', $cliente->id) }}">
                            @csrf
                            <div class="modal-header">
                                <h5 class="modal-title fw-bold">Vincular Alergia</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Alérgeno</label>
                                    <select name="alergia_id" class="form-select" required>
                                        @foreach($alergiasDisponibles as $ad)
                                            <option value="{{ $ad->id }}">{{ $ad->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Severidad</label>
                                    <select name="severidad_id" class="form-select" required>
                                        @foreach($severidades as $sev)
                                            <option value="{{ $sev->id }}">{{ $sev->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Notas</label>
                                    <input type="text" name="notas" class="form-control" placeholder="Reacciones o indicaciones">
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                <button type="submit" class="btn btn-danger">Guardar Alergia</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Preferencias -->
            <div class="card card-custom p-4">
                <h5 class="fw-bold text-dark border-bottom pb-2 mb-3"><i class="bi bi-heart-fill me-2 text-primary"></i> Preferencias del Huésped</h5>
                <ul class="list-unstyled mb-0">
                    @forelse($cliente->preferencias as $pref)
                        <li class="mb-2 pb-2 border-bottom">
                            <span class="badge bg-light text-dark border me-1">{{ $pref->tipo }}</span>
                            <span>{{ $pref->descripcion }}</span>
                        </li>
                    @empty
                        <li class="text-muted small">Sin preferencias registradas.</li>
                    @endforelse
                </ul>
            </div>
        </div>

        <!-- Columna Derecha: Historial de Reservaciones & Puntos -->
        <div class="col-lg-7">
            <!-- Reservaciones -->
            <div class="card card-custom mb-4">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-calendar-event me-2 text-warning"></i> Historial de Visitas & Reservaciones</h5>
                    <a href="{{ route('reservaciones.create') }}" class="btn btn-sm btn-rv-primary">+ Reservar</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light small text-uppercase">
                                <tr>
                                    <th>Folio</th>
                                    <th>Fecha</th>
                                    <th>Pax</th>
                                    <th>Mesa</th>
                                    <th>Estatus</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($cliente->reservaciones as $res)
                                <tr>
                                    <td class="fw-bold">#RES-{{ $res->id }}</td>
                                    <td>{{ \Carbon\Carbon::parse($res->fecha_hora_inicio)->format('d/m/Y H:i') }}</td>
                                    <td>{{ $res->cantidad_personas }} pers.</td>
                                    <td>
                                        @if($res->mesasAsignadas->isNotEmpty())
                                            <span class="badge bg-dark">{{ $res->mesasAsignadas->first()->mesaSucursal->mesa->codigo_mesa ?? 'Mesa' }}</span>
                                        @else
                                            <span class="text-muted small">Sin mesa</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $res->estatus }}</span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">Aún no cuenta con reservaciones registradas.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Movimientos de Puntos -->
            <div class="card card-custom">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-clock-history me-2 text-info"></i> Movimientos de Puntos Recientes</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light small text-uppercase">
                                <tr>
                                    <th>Fecha</th>
                                    <th>Tipo</th>
                                    <th>Puntos</th>
                                    <th>Referencia</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($cliente->movimientosPuntos as $mov)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($mov->fecha)->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <span class="badge bg-light text-dark border">{{ $mov->tipo }}</span>
                                    </td>
                                    <td class="fw-bold {{ $mov->puntos > 0 ? 'text-success' : 'text-danger' }}">
                                        {{ $mov->puntos > 0 ? '+' : '' }}{{ number_format($mov->puntos) }}
                                    </td>
                                    <td>
                                        @if($mov->notaReservacion)
                                            <span class="small text-muted">Comanda #{{ $mov->notaReservacion->id }}</span>
                                        @else
                                            <span class="small text-muted">Ajuste Manual</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">Sin movimientos de puntos registrados.</td>
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