@extends('layouts.app')

@section('title', 'Nuevo Platillo')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="card card-custom p-4 shadow-sm border-0">
                <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
                    <div>
                        <h2 class="h4 fw-bold text-dark mb-1">Registrar Platillo en Carta</h2>
                        <p class="text-muted small mb-0">Completa la ficha técnica gastronómica, temporada/rama, ingredientes y alérgenos.</p>
                    </div>
                    <a href="{{ route('platillos.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-arrow-left"></i> Volver a la Carta
                    </a>
                </div>

                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
                        <strong><i class="bi bi-exclamation-triangle-fill me-1"></i> Por favor revisa los campos requeridos:</strong>
                        <ul class="mb-0 mt-1 small">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <!-- Se agrega enctype="multipart/form-data" para permitir subida de archivos -->
                <form method="POST" action="{{ route('platillos.store') }}" id="form_platillo" enctype="multipart/form-data">
                    @csrf
                    <div class="row g-3">
                        <!-- Nombre del Platillo -->
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Nombre del Platillo *</label>
                            <input type="text" name="nombre" class="form-control" required placeholder="Ej. Trucha Salmonada al Eneldo y Mantequilla Avellanada" value="{{ old('nombre') }}">
                        </div>

                        <!-- Precio -->
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Precio ($ MXN) *</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.50" name="precio" class="form-control" required placeholder="0.00" value="{{ old('precio') }}">
                            </div>
                        </div>

                        <!-- Categoría con Opción de Crear Nueva -->
                        <div class="col-md-6">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="form-label fw-semibold mb-0">Categoría *</label>
                                <button type="button" class="btn btn-link btn-sm text-decoration-none p-0 fw-semibold text-success" id="btn_toggle_nueva_categoria">
                                    <i class="bi bi-plus-circle me-1"></i>+ Nueva Categoría
                                </button>
                            </div>
                            <select name="categoria_id" id="categoria_id_select" class="form-select" required>
                                <option value="">Seleccione categoría...</option>
                                <option value="nueva" class="fw-bold text-success" {{ old('categoria_id') == 'nueva' ? 'selected' : '' }}>
                                    ➕ Registrar nueva categoría...
                                </option>
                                @foreach($categorias as $cat)
                                    <option value="{{ $cat->id }}" {{ old('categoria_id') == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->nombre }}
                                    </option>
                                @endforeach
                            </select>

                            <!-- Panel Dinámico: Nueva Categoría -->
                            <div id="nueva_categoria_container" class="mt-2 p-3 bg-light rounded border border-success" style="display: {{ old('categoria_id') == 'nueva' ? 'block' : 'none' }};">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="fw-bold text-success small"><i class="bi bi-bookmark-plus me-1"></i> Registrar Nueva Categoría</span>
                                    <span class="badge bg-success-subtle text-success border border-success small">Se guardará automáticamente</span>
                                </div>
                                <div class="row g-2">
                                    <div class="col-12">
                                        <label class="form-label small text-muted mb-1">Nombre de la Categoría *</label>
                                        <input type="text" name="nueva_categoria_nombre" id="nueva_categoria_nombre" class="form-control form-control-sm" placeholder="Ej: Especialidades del Chef, Coctelería de Autor, Brasas & Ahumados..." value="{{ old('nueva_categoria_nombre') }}">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label small text-muted mb-1">Descripción (Opcional)</label>
                                        <input type="text" name="nueva_categoria_descripcion" class="form-control form-control-sm" placeholder="Ej: Platillos preparados a fuego vivo en horno de leña..." value="{{ old('nueva_categoria_descripcion') }}">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Temporada / Rama con Opción de Crear Nueva -->
                        <div class="col-md-6">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="form-label fw-semibold mb-0">Temporada / Rama (Opcional)</label>
                                <button type="button" class="btn btn-link btn-sm text-decoration-none p-0 fw-semibold text-primary" id="btn_toggle_nueva_temporada">
                                    <i class="bi bi-plus-circle me-1"></i>+ Nueva Temporada
                                </button>
                            </div>
                            <select name="temporada_id" id="temporada_id_select" class="form-select">
                                <option value="">Menú Permanente / Sin temporada</option>
                                <option value="nueva" class="fw-bold text-primary" {{ old('temporada_id') == 'nueva' ? 'selected' : '' }}>
                                    ➕ Registrar nueva temporada / rama...
                                </option>
                                @foreach($temporadas as $temp)
                                    <option value="{{ $temp->id }}" {{ old('temporada_id') == $temp->id ? 'selected' : '' }}>
                                        {{ $temp->nombre }}
                                    </option>
                                @endforeach
                            </select>

                            <!-- Panel Dinámico: Nueva Temporada -->
                            <div id="nueva_temporada_container" class="mt-2 p-3 bg-light rounded border border-primary" style="display: {{ old('temporada_id') == 'nueva' ? 'block' : 'none' }};">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="fw-bold text-primary small"><i class="bi bi-calendar-event me-1"></i> Registrar Nueva Temporada o Rama</span>
                                    <span class="badge bg-primary-subtle text-primary border border-primary small">Vigencia gastronómica</span>
                                </div>
                                <div class="row g-2">
                                    <div class="col-12">
                                        <label class="form-label small text-muted mb-1">Nombre de la Temporada *</label>
                                        <input type="text" name="nueva_temporada_nombre" id="nueva_temporada_nombre" class="form-control form-control-sm" placeholder="Ej: Temporada de Hongos de Lluvia, Festival de Invierno..." value="{{ old('nueva_temporada_nombre') }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small text-muted mb-1">Fecha de Inicio</label>
                                        <input type="date" name="nueva_temporada_inicio" class="form-control form-control-sm" value="{{ old('nueva_temporada_inicio', date('Y-m-d')) }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small text-muted mb-1">Fecha Final</label>
                                        <input type="date" name="nueva_temporada_fin" class="form-control form-control-sm" value="{{ old('nueva_temporada_fin', date('Y-m-d', strtotime('+6 months'))) }}">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Descripción -->
                        <div class="col-12">
                            <label class="form-label fw-semibold">Descripción Gastronómica</label>
                            <textarea name="descripcion" rows="3" class="form-control" placeholder="Describe los ingredientes principales, métodos de cocción y maridaje sugerido...">{{ old('descripcion') }}</textarea>
                        </div>

                        <!-- Subir Archivo de Imagen -->
                        <div class="col-12">
                            <label class="form-label fw-semibold"><i class="bi bi-image me-1"></i> Fotografía del Platillo (Imagen)</label>
                            <input type="file" name="imagen" class="form-control" accept="image/png, image/jpeg, image/jpg, image/webp">
                            <small class="text-muted">Selecciona una imagen desde tu equipo (JPG, PNG, WEBP — Máx. 2MB).</small>
                        </div>

                        <!-- Sección de Ingredientes: Existentes + Múltiples Nuevos -->
                        <div class="col-12 pt-3 border-top">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div>
                                    <label class="form-label fw-bold text-dark mb-0 fs-6">
                                        <i class="bi bi-tree me-1 text-success"></i> Ingredientes del Platillo (Pueden existir muchos)
                                    </label>
                                    <p class="text-muted small mb-0">Selecciona del catálogo actual o registra múltiples ingredientes nuevos al vuelo.</p>
                                </div>
                                <span class="badge bg-success-subtle text-success border border-success">
                                    {{ count($ingredientes) }} ingredientes en catálogo
                                </span>
                            </div>

                            <!-- Buscador rápido de ingredientes existentes -->
                            <div class="input-group input-group-sm mb-2">
                                <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                                <input type="text" id="filtro_ingredientes" class="form-control" placeholder="Buscar ingrediente en catálogo existente...">
                            </div>

                            <!-- Lista de Ingredientes Existentes en Catálogo -->
                            <div class="p-3 bg-light rounded border mb-3" style="max-height: 180px; overflow-y: auto;">
                                <div class="row g-2" id="contenedor_ingredientes_catalogo">
                                    @foreach($ingredientes as $ing)
                                        <div class="col-sm-6 col-md-4 ingrediente-item" data-nombre="{{ strtolower($ing->nombre) }}">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="ingredientes[]" value="{{ $ing->id }}" id="ing_{{ $ing->id }}" {{ is_array(old('ingredientes')) && in_array($ing->id, old('ingredientes')) ? 'checked' : '' }}>
                                                <label class="form-check-label small" for="ing_{{ $ing->id }}">
                                                    {{ $ing->nombre }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Panel para Agregar Múltiples Ingredientes Nuevos -->
                            <div class="p-3 bg-light rounded border border-success-subtle">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="fw-bold text-dark small">
                                        <i class="bi bi-plus-square-fill text-success me-1"></i> Registrar Nuevos Ingredientes al Platillo
                                    </span>
                                    <button type="button" class="btn btn-outline-success btn-sm py-0 px-2 fw-semibold" id="btn_agregar_fila_ingrediente">
                                        <i class="bi bi-plus-lg me-1"></i>+ Agregar otro ingrediente
                                    </button>
                                </div>
                                <p class="text-muted small mb-3">Si algún ingrediente no existe en el catálogo, puedes agregarlo aquí uno por uno o varios separados por comas:</p>

                                <!-- Fila dinámica de ingredientes nuevos -->
                                <div id="filas_nuevos_ingredientes" class="row g-2 mb-3">
                                    <div class="col-md-6 col-12 fila-ingrediente">
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text bg-white text-success"><i class="bi bi-check2"></i></span>
                                            <input type="text" name="nuevos_ingredientes[]" class="form-control" placeholder="Nombre de nuevo ingrediente...">
                                            <button type="button" class="btn btn-outline-danger btn-eliminar-fila" title="Quitar"><i class="bi bi-x-lg"></i></button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Entrada masiva por comas o líneas -->
                                <div class="mt-2">
                                    <label class="form-label small text-muted mb-1">
                                        <i class="bi bi-card-text me-1"></i> O ingresa varios ingredientes separados por coma o salto de línea:
                                    </label>
                                    <textarea name="nuevos_ingredientes_texto" class="form-control form-control-sm" rows="2" placeholder="Ej: Sal ahumada de Colima, Mantequilla artesanal de rancho, Brotes de cilantro silvestre, Hongos porcini frescos...">{{ old('nuevos_ingredientes_texto') }}</textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Alérgenos -->
                        <div class="col-12 pt-3 border-top">
                            <label class="form-label fw-bold text-danger mb-1">
                                <i class="bi bi-shield-exclamation me-1"></i> Alérgenos Presentes (Para seguridad alimentaria del comensal)
                            </label>
                            <p class="text-muted small mb-2">Marca los alérgenos que contiene este platillo para alertar en sala y cocina:</p>
                            <div class="row g-2 p-3 bg-light rounded border border-danger-subtle">
                                @foreach($alergias as $al)
                                    <div class="col-sm-6 col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="alergias[]" value="{{ $al->id }}" id="alergia_{{ $al->id }}" {{ is_array(old('alergias')) && in_array($al->id, old('alergias')) ? 'checked' : '' }}>
                                            <label class="form-check-label small" for="alergia_{{ $al->id }}">
                                                {{ $al->nombre }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Botones de Acción -->
                        <div class="col-12 pt-4 border-top d-flex justify-content-end gap-2">
                            <a href="{{ route('platillos.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                            <button type="submit" class="btn btn-rv-primary px-4 fw-semibold">
                                <i class="bi bi-check-circle me-1"></i> Guardar Platillo
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // 1. Manejo de Nueva Categoría
    const catSelect = document.getElementById('categoria_id_select');
    const catContainer = document.getElementById('nueva_categoria_container');
    const catNombreInput = document.getElementById('nueva_categoria_nombre');
    const btnToggleCat = document.getElementById('btn_toggle_nueva_categoria');

    function toggleNuevaCategoria(show) {
        if (show) {
            catContainer.style.display = 'block';
            catSelect.value = 'nueva';
            if (catNombreInput) catNombreInput.focus();
        } else {
            catContainer.style.display = 'none';
        }
    }

    if (catSelect) {
        catSelect.addEventListener('change', function () {
            toggleNuevaCategoria(this.value === 'nueva');
        });
    }

    if (btnToggleCat) {
        btnToggleCat.addEventListener('click', function () {
            const isHidden = catContainer.style.display === 'none' || catContainer.style.display === '';
            toggleNuevaCategoria(isHidden);
            if (!isHidden) {
                catSelect.value = '';
            }
        });
    }

    // 2. Manejo de Nueva Temporada / Rama
    const tempSelect = document.getElementById('temporada_id_select');
    const tempContainer = document.getElementById('nueva_temporada_container');
    const tempNombreInput = document.getElementById('nueva_temporada_nombre');
    const btnToggleTemp = document.getElementById('btn_toggle_nueva_temporada');

    function toggleNuevaTemporada(show) {
        if (show) {
            tempContainer.style.display = 'block';
            tempSelect.value = 'nueva';
            if (tempNombreInput) tempNombreInput.focus();
        } else {
            tempContainer.style.display = 'none';
        }
    }

    if (tempSelect) {
        tempSelect.addEventListener('change', function () {
            toggleNuevaTemporada(this.value === 'nueva');
        });
    }

    if (btnToggleTemp) {
        btnToggleTemp.addEventListener('click', function () {
            const isHidden = tempContainer.style.display === 'none' || tempContainer.style.display === '';
            toggleNuevaTemporada(isHidden);
            if (!isHidden) {
                tempSelect.value = '';
            }
        });
    }

    // 3. Filtrado rápido de ingredientes existentes
    const filtroInput = document.getElementById('filtro_ingredientes');
    const itemsIngredientes = document.querySelectorAll('.ingrediente-item');

    if (filtroInput) {
        filtroInput.addEventListener('input', function () {
            const val = this.value.toLowerCase().trim();
            itemsIngredientes.forEach(function (item) {
                const nombre = item.getAttribute('data-nombre') || '';
                if (nombre.includes(val)) {
                    item.style.display = '';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    }

    // 4. Agregar y eliminar filas dinámicas de nuevos ingredientes
    const btnAgregarFila = document.getElementById('btn_agregar_fila_ingrediente');
    const contenedorFilas = document.getElementById('filas_nuevos_ingredientes');

    if (btnAgregarFila && contenedorFilas) {
        btnAgregarFila.addEventListener('click', function () {
            const nuevaCol = document.createElement('div');
            nuevaCol.className = 'col-md-6 col-12 fila-ingrediente';
            nuevaCol.innerHTML = `
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white text-success"><i class="bi bi-check2"></i></span>
                    <input type="text" name="nuevos_ingredientes[]" class="form-control" placeholder="Nombre de nuevo ingrediente...">
                    <button type="button" class="btn btn-outline-danger btn-eliminar-fila" title="Quitar"><i class="bi bi-x-lg"></i></button>
                </div>
            `;
            contenedorFilas.appendChild(nuevaCol);
            const input = nuevaCol.querySelector('input');
            if (input) input.focus();
        });

        contenedorFilas.addEventListener('click', function (e) {
            const btnEliminar = e.target.closest('.btn-eliminar-fila');
            if (btnEliminar) {
                const fila = btnEliminar.closest('.fila-ingrediente');
                if (fila) {
                    fila.remove();
                }
            }
        });
    }
});
</script>
@endpush