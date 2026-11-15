@extends('layouts.app')

@section('title', 'Nuevo Platillo')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card card-custom p-4">
                <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
                    <div>
                        <h2 class="h4 fw-bold text-dark mb-1">Registrar Platillo en Carta</h2>
                        <p class="text-muted small mb-0">Completa la ficha técnica gastronómica y alérgenos para sala y cocina.</p>
                    </div>
                    <a href="{{ route('platillos.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-arrow-left"></i> Volver
                    </a>
                </div>

                <form method="POST" action="{{ route('platillos.store') }}">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Nombre del Platillo *</label>
                            <input type="text" name="nombre" class="form-control" required placeholder="Ej. Trucha Salmonada al Eneldo" value="{{ old('nombre') }}">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Precio ($ MXN) *</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.50" name="precio" class="form-control" required placeholder="0.00" value="{{ old('precio') }}">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Categoría *</label>
                            <select name="categoria_id" class="form-select" required>
                                <option value="">Seleccione categoría...</option>
                                @foreach($categorias as $cat)
                                    <option value="{{ $cat->id }}" {{ old('categoria_id') == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Temporada (Opcional)</label>
                            <select name="temporada_id" class="form-select">
                                <option value="">Menú Permanente / Sin temporada</option>
                                @foreach($temporadas as $temp)
                                    <option value="{{ $temp->id }}" {{ old('temporada_id') == $temp->id ? 'selected' : '' }}>
                                        {{ $temp->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Descripción Gastronómica</label>
                            <textarea name="descripcion" rows="3" class="form-control" placeholder="Describe los ingredientes, métodos de cocción y maridaje sugerido...">{{ old('descripcion') }}</textarea>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">URL de Fotografía</label>
                            <input type="url" name="imagen_url" class="form-control" placeholder="https://..." value="{{ old('imagen_url') }}">
                        </div>

                        <!-- Alérgenos -->
                        <div class="col-12 pt-3 border-top">
                            <label class="form-label fw-bold text-danger"><i class="bi bi-shield-exclamation me-1"></i> Alérgenos Presentes (Para seguridad alimentaria del huésped)</label>
                            <div class="row g-2">
                                @foreach($alergias as $al)
                                    <div class="col-sm-6 col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="alergias[]" value="{{ $al->id }}" id="alergia_{{ $al->id }}">
                                            <label class="form-check-label small" for="alergia_{{ $al->id }}">
                                                {{ $al->nombre }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Ingredientes -->
                        <div class="col-12 pt-3 border-top">
                            <label class="form-label fw-bold text-dark"><i class="bi bi-tree me-1 text-success"></i> Ingredientes Locales / Proximidad</label>
                            <div class="row g-2">
                                @foreach($ingredientes as $ing)
                                    <div class="col-sm-6 col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="ingredientes[]" value="{{ $ing->id }}" id="ing_{{ $ing->id }}">
                                            <label class="form-check-label small" for="ing_{{ $ing->id }}">
                                                {{ $ing->nombre }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="col-12 pt-4 border-top d-flex justify-content-end gap-2">
                            <a href="{{ route('platillos.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                            <button type="submit" class="btn btn-rv-primary px-4">Guardar Platillo</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection