@extends('layouts.app')

@section('title', 'Programa de Lealtad VIP')

@section('content')
    <div class="container-fluid px-lg-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                    <h1 class="h3 fw-bold text-dark mb-0">Programa de Fidelización & Beneficios VIP</h1>
                    @can('sucursales.admin')
                        <span class="badge rounded-pill {{ ($sucursalActivaId ?? null) ? 'bg-warning text-dark' : 'bg-dark text-warning' }} px-3 py-2">
                        <i class="bi bi-building me-1"></i>
                        Sucursal: {{ $sucursalActivaNombre ?? 'Todas las sucursales' }}
                    </span>
                    @endcan
                </div>
                <p class="text-muted mb-0 small">Configuración de niveles de membresía, catálogo de beneficios exclusivos y reglas de acumulación de puntos.</p>
            </div>
            <button type="button" class="btn btn-rv-primary btn-sm px-3" data-bs-toggle="modal" data-bs-target="#modalNuevoBeneficioGlobal">
                <i class="bi bi-plus-lg me-1"></i>+ Nuevo Beneficio Global
            </button>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-4 shadow-sm" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(isset($errors) && $errors->any())
            <div class="alert alert-danger alert-dismissible fade show mb-4 shadow-sm" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ $errors->first() }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Tarjetas de Niveles -->
        <div class="row g-4 mb-4">
            @foreach($niveles as $nv)
                @php
                    $beneficiosNivelIds = $nv->beneficios->pluck('id')->toArray();
                @endphp
                <div class="col-sm-6 col-lg-3">
                    <div class="card card-custom h-100 p-4 border-top border-4 {{ str_contains($nv->nombre, 'Black') ? 'border-dark' : 'border-warning' }} d-flex flex-column justify-content-between shadow-sm">
                        <div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h5 class="fw-bold text-dark mb-0">{{ $nv->nombre }}</h5>
                                <i class="bi bi-award-fill text-warning fs-4"></i>
                            </div>
                            <div class="fs-4 fw-bold text-primary my-2">{{ number_format($nv->puntos_minimos) }} <span class="fs-6 text-muted">pts mín.</span></div>
                            <p class="small text-muted mb-3">Socios activos: <strong>{{ $nv->clientes->count() }}</strong></p>

                            <div class="pt-2 border-top">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="small fw-semibold text-dark">Beneficios incluidos:</span>
                                    <span class="badge bg-light text-muted border">{{ $nv->beneficios->count() }}</span>
                                </div>
                                <ul class="list-unstyled small mb-0">
                                    @forelse($nv->beneficios as $ben)
                                        <li class="d-flex justify-content-between align-items-center py-1 border-bottom border-light">
                                    <span class="text-truncate me-2" title="{{ $ben->nombre }}">
                                        <i class="bi bi-check-circle-fill text-success me-1"></i>{{ $ben->nombre }}
                                    </span>
                                            <form method="POST" action="{{ route('lealtad.quitarBeneficio', [$nv->id, $ben->id]) }}" class="d-inline" onsubmit="return confirm('¿Quitar \'{{ $ben->nombre }}\' del nivel {{ $nv->nombre }}?');">
                                                @csrf
                                                <button type="submit" class="btn btn-link text-danger p-0 border-0" title="Quitar beneficio de este nivel">
                                                    <i class="bi bi-x-circle text-danger"></i>
                                                </button>
                                            </form>
                                        </li>
                                    @empty
                                        <li class="text-muted py-1 fst-italic">Acumulación estándar de puntos.</li>
                                    @endforelse
                                </ul>
                            </div>
                        </div>

                        <!-- Botón para Agregar / Gestionar Beneficios -->
                        <div class="pt-3 border-top mt-3">
                            <button type="button" class="btn btn-outline-warning btn-sm w-100 fw-semibold text-dark" data-bs-toggle="modal" data-bs-target="#modalBeneficioNivel{{ $nv->id }}">
                                <i class="bi bi-plus-circle me-1 text-primary"></i> + Agregar Beneficio
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Modal para Agregar Beneficio a este Nivel -->
                <div class="modal fade" id="modalBeneficioNivel{{ $nv->id }}" tabindex="-1" aria-labelledby="modalLabel{{ $nv->id }}" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content shadow border-0">
                            <div class="modal-header bg-light">
                                <h5 class="modal-title fw-bold text-dark fs-6" id="modalLabel{{ $nv->id }}">
                                    <i class="bi bi-award-fill text-warning me-2"></i>Beneficios para: {{ $nv->nombre }}
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body p-4">
                                <!-- Pestañas: Asignar existente o Crear nuevo -->
                                <ul class="nav nav-pills nav-fill mb-3" id="pills-tab-{{ $nv->id }}" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active small py-2 fw-semibold" id="tab-asignar-{{ $nv->id }}" data-bs-toggle="pill" data-bs-target="#pane-asignar-{{ $nv->id }}" type="button" role="tab">
                                            <i class="bi bi-list-check me-1"></i> Asignar del Catálogo
                                        </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link small py-2 fw-semibold" id="tab-crear-{{ $nv->id }}" data-bs-toggle="pill" data-bs-target="#pane-crear-{{ $nv->id }}" type="button" role="tab">
                                            <i class="bi bi-plus-lg me-1"></i> Crear Nuevo Beneficio
                                        </button>
                                    </li>
                                </ul>

                                <div class="tab-content" id="pills-tabContent-{{ $nv->id }}">
                                    <!-- Pestaña 1: Asignar existente -->
                                    <div class="tab-pane fade show active" id="pane-asignar-{{ $nv->id }}" role="tabpanel">
                                        <form method="POST" action="{{ route('lealtad.asignarBeneficio', $nv->id) }}">
                                            @csrf
                                            <div class="mb-3">
                                                <label class="form-label small fw-semibold">Seleccionar Beneficio del Catálogo:</label>
                                                <select name="beneficio_id" class="form-select" required>
                                                    <option value="">Seleccione un beneficio...</option>
                                                    @foreach($beneficios as $b)
                                                        @if(!in_array($b->id, $beneficiosNivelIds))
                                                            <option value="{{ $b->id }}">
                                                                {{ $b->nombre }} — ({{ Str::limit($b->descripcion, 40) }})
                                                            </option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                                <div class="form-text small">Solo se muestran beneficios que aún no han sido asignados a este nivel.</div>
                                            </div>
                                            <div class="d-flex justify-content-end gap-2 pt-2">
                                                <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                                                <button type="submit" class="btn btn-rv-primary btn-sm px-3">
                                                    <i class="bi bi-check-lg me-1"></i> Asignar al Nivel
                                                </button>
                                            </div>
                                        </form>
                                    </div>

                                    <!-- Pestaña 2: Crear nuevo beneficio -->
                                    <div class="tab-pane fade" id="pane-crear-{{ $nv->id }}" role="tabpanel">
                                        <form method="POST" action="{{ route('lealtad.crearBeneficio') }}">
                                            @csrf
                                            <input type="hidden" name="nivel_id" value="{{ $nv->id }}">
                                            <div class="mb-3">
                                                <label class="form-label small fw-semibold">Nombre del Beneficio *</label>
                                                <input type="text" name="nombre" class="form-control form-control-sm" required placeholder="Ej: Botella de Vino de Bienvenida, Check-out tardío...">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label small fw-semibold">Descripción *</label>
                                                <textarea name="descripcion" class="form-control form-control-sm" rows="2" required placeholder="Describe las condiciones o cortesía..."></textarea>
                                            </div>
                                            <div class="row g-2 mb-3">
                                                <div class="col-6">
                                                    <label class="form-label small text-muted">Vigencia Inicio</label>
                                                    <input type="date" name="vigencia_ini" class="form-control form-control-sm" value="{{ date('Y-m-d') }}">
                                                </div>
                                                <div class="col-6">
                                                    <label class="form-label small text-muted">Vigencia Fin</label>
                                                    <input type="date" name="vigencia_fin" class="form-control form-control-sm" value="{{ date('Y-m-d', strtotime('+1 year')) }}">
                                                </div>
                                            </div>
                                            <div class="d-flex justify-content-end gap-2 pt-2">
                                                <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                                                <button type="submit" class="btn btn-success btn-sm px-3">
                                                    <i class="bi bi-sparkles me-1"></i> Crear y Asignar a {{ $nv->nombre }}
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Regla de Puntos y Movimientos Recientes -->
        <div class="row g-4">
            <div class="col-lg-5">
                <div class="card card-custom p-4 mb-4 shadow-sm">
                    <h5 class="fw-bold text-dark border-bottom pb-2 mb-3"><i class="bi bi-gear-fill me-2 text-primary"></i> Regla de Acumulación Activa</h5>
                    @foreach($reglas as $reg)
                        <div class="p-3 bg-light rounded border mb-2">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="fw-bold text-dark">${{ number_format($reg->monto_por_punto, 2) }} MXN gastados</span>
                                <span class="badge bg-success">= 1 Punto de Lealtad</span>
                            </div>
                            <small class="text-muted">Vigencia: {{ \Carbon\Carbon::parse($reg->vigencia_inicio)->format('d/m/Y') }} a {{ $reg->vigencia_fin ? \Carbon\Carbon::parse($reg->vigencia_fin)->format('d/m/Y') : 'Indefinida' }}</small>
                        </div>
                    @endforeach
                </div>

                <!-- Catálogo de Beneficios -->
                <div class="card card-custom p-4 shadow-sm">
                    <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
                        <h5 class="fw-bold text-dark mb-0"><i class="bi bi-gift-fill me-2 text-danger"></i> Catálogo de Beneficios VIP</h5>
                        <button type="button" class="btn btn-outline-primary btn-sm py-0 px-2" data-bs-toggle="modal" data-bs-target="#modalNuevoBeneficioGlobal">
                            <i class="bi bi-plus-lg me-1"></i>+ Nuevo
                        </button>
                    </div>
                    <ul class="list-group list-group-flush">
                        @foreach($beneficios as $b)
                            <li class="list-group-item px-0 py-2">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <div class="fw-semibold text-dark">{{ $b->nombre }}</div>
                                        <small class="text-muted">{{ $b->descripcion }}</small>
                                    </div>
                                    <span class="badge bg-light text-dark border ms-2">
                                {{ $b->nivelesLealtad->count() }} {{ Str::plural('nivel', $b->nivelesLealtad->count()) }}
                            </span>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <div class="col-lg-7">
                <div class="card card-custom shadow-sm">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-clock-history me-2 text-info"></i> Últimos Movimientos de Puntos de la Cadena</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light small text-uppercase">
                                <tr>
                                    <th>Fecha</th>
                                    <th>Huésped</th>
                                    <th>Tipo</th>
                                    <th>Puntos</th>
                                    <th>Nota</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($ultimosMovimientos as $mov)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($mov->fecha)->format('d/m/Y H:i') }}</td>
                                        <td>
                                            <a href="{{ route('clientes.show', $mov->cliente_id) }}" class="fw-semibold text-dark text-decoration-none">
                                                {{ $mov->cliente ? $mov->cliente->nombre_completo : 'Cliente' }}
                                            </a>
                                        </td>
                                        <td><span class="badge bg-light text-dark border">{{ $mov->tipo }}</span></td>
                                        <td class="fw-bold {{ $mov->puntos > 0 ? 'text-success' : 'text-danger' }}">
                                            {{ $mov->puntos > 0 ? '+' : '' }}{{ number_format($mov->puntos) }} pts
                                        </td>
                                        <td>
                                            @if($mov->notaReservacion)
                                                <span class="small text-muted">Comanda #{{ $mov->notaReservacion->id }}</span>
                                            @else
                                                <span class="small text-muted">Manual</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">Sin movimientos registrados.</td>
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

    <!-- Modal para Crear Beneficio Global (General) -->
    <div class="modal fade" id="modalNuevoBeneficioGlobal" tabindex="-1" aria-labelledby="modalGlobalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content shadow border-0">
                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-bold text-dark fs-6" id="modalGlobalLabel">
                        <i class="bi bi-gift-fill text-danger me-2"></i>Registrar Nuevo Beneficio VIP
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="{{ route('lealtad.crearBeneficio') }}">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Nombre del Beneficio *</label>
                            <input type="text" name="nombre" class="form-control form-control-sm" required placeholder="Ej: 20% de Descuento en Spa Rodavento">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Descripción Detallada *</label>
                            <textarea name="descripcion" class="form-control form-control-sm" rows="3" required placeholder="Condiciones de aplicación, vigencia o restricciones..."></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Asignar a Nivel (Opcional):</label>
                            <select name="nivel_id" class="form-select form-select-sm">
                                <option value="">Dejar solo en catálogo general</option>
                                @foreach($niveles as $nv)
                                    <option value="{{ $nv->id }}">{{ $nv->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="row g-2">
                            <div class="col-6">
                                <label class="form-label small text-muted">Vigencia Inicio</label>
                                <input type="date" name="vigencia_ini" class="form-control form-control-sm" value="{{ date('Y-m-d') }}">
                            </div>
                            <div class="col-6">
                                <label class="form-label small text-muted">Vigencia Fin</label>
                                <input type="date" name="vigencia_fin" class="form-control form-control-sm" value="{{ date('Y-m-d', strtotime('+1 year')) }}">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light py-2">
                        <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-rv-primary btn-sm px-4">Guardar Beneficio</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
