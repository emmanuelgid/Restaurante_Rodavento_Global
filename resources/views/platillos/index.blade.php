@extends('layouts.app')

@section('title', 'Carta & Menú Gastronómico')

@section('content')
    <div class="container-fluid px-lg-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
            <div>
                <h1 class="h3 fw-bold text-dark mb-1">Carta & Menú Gastronómico</h1>
                <p class="text-muted mb-0 small">Catálogo de recetas, ingredientes de proximidad, gestión de alérgenos y precios por sucursal.</p>
            </div>
            @can('menu.admin')
                <div class="mt-3 mt-md-0">
                    <a href="{{ route('platillos.create') }}" class="btn btn-rv-gold btn-sm d-flex align-items-center gap-1 shadow-sm">
                        <i class="bi bi-plus-circle"></i> Nuevo Platillo
                    </a>
                </div>
            @endcan
        </div>

        <!-- Filtros de Búsqueda -->
        <div class="card card-custom p-3 mb-4">
            <form method="GET" action="{{ route('platillos.index') }}" class="row g-2 align-items-center">
                <div class="col-md-5">
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Buscar por nombre o ingrediente..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <select name="categoria_id" class="form-select">
                        <option value="">Todas las Categorías</option>
                        @foreach($categorias as $cat)
                            <option value="{{ $cat->id }}" {{ request('categoria_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-rv-primary w-100">Filtrar</button>
                    @if(request()->hasAny(['search', 'categoria_id']))
                        <a href="{{ route('platillos.index') }}" class="btn btn-outline-secondary"><i class="bi bi-x-lg"></i></a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Grid de Platillos -->
        <div class="row g-4">
            @forelse($platillos as $pl)
                {{-- Omitir platillos inactivos o cancelados --}}
                @if(isset($pl->estatus) && $pl->estatus !== 'activo')
                    @continue
                @endif

                <div class="col-sm-6 col-lg-4 col-xl-3">
                    <div class="card card-custom h-100 d-flex flex-column overflow-hidden">
                        <div style="height: 190px; position: relative;">
                            <img src="{{ $pl->imagen_url ?? 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=600&q=80' }}"
                                 class="w-100 h-100 object-fit-cover" alt="{{ $pl->nombre }}">
                            <div class="position-absolute top-0 end-0 m-2">
                                <span class="badge {{ $pl->disponible ? 'bg-success' : 'bg-danger' }}">
                                    {{ $pl->disponible ? 'En Carta' : 'Agotado' }}
                                </span>
                            </div>
                            <div class="position-absolute bottom-0 start-0 m-2">
                                <span class="badge bg-dark bg-opacity-75 text-white">
                                    {{ $pl->categoria->nombre ?? 'General' }}
                                </span>
                            </div>
                        </div>

                        <div class="card-body d-flex flex-column p-3">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h5 class="card-title fw-bold text-dark fs-6 mb-0">{{ $pl->nombre }}</h5>
                                <div class="fs-6 fw-bold text-success">${{ number_format($pl->precio, 2) }}</div>
                            </div>
                            <p class="card-text text-muted small flex-grow-1 mb-3" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                {{ $pl->descripcion }}
                            </p>

                            <!-- Alérgenos -->
                            @if($pl->alergenos && $pl->alergenos->isNotEmpty())
                                <div class="mb-3">
                                    <small class="text-danger fw-bold d-block mb-1" style="font-size: 0.72rem;">
                                        <i class="bi bi-exclamation-triangle"></i> Alérgenos:
                                    </small>
                                    <div class="d-flex flex-wrap gap-1">
                                        @foreach($pl->alergenos as $al)
                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle" style="font-size: 0.68rem;">
                                                {{ $al->nombre }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            @can('menu.admin')
                                <div class="d-flex justify-content-between align-items-center pt-2 border-top mt-auto">
                                    <form method="POST" action="{{ route('platillos.toggle', $pl->id) }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm {{ $pl->disponible ? 'btn-outline-warning' : 'btn-outline-success' }}" title="Cambiar Disponibilidad">
                                            <i class="bi {{ $pl->disponible ? 'bi-slash-circle' : 'bi-check-circle' }}"></i>
                                            {{ $pl->disponible ? 'Agotar' : 'Habilitar' }}
                                        </button>
                                    </form>
                                    <a href="{{ route('platillos.edit', $pl->id) }}" class="btn btn-sm btn-light border text-dark">
                                        <i class="bi bi-pencil-square"></i> Editar
                                    </a>
                                </div>
                            @endcan
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-5">
                    <i class="bi bi-egg-fried text-muted" style="font-size: 4rem;"></i>
                    <h4 class="mt-3 text-muted">No se encontraron platillos disponibles</h4>
                    <p class="text-muted small">Intente cambiar los filtros o agregue un nuevo platillo a la carta.</p>
                </div>
            @endforelse
        </div>

        <div class="mt-4">
            {{ $platillos->links() }}
        </div>
    </div>
@endsection