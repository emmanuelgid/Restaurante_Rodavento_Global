<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Platillo, CategoriaPlatillo, Temporada, Ingrediente, Alergia, Sucursal, PlatilloSucursal, Bitacora};
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class PlatilloController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        // Solo platillos con estatus activo Y marcados como disponibles en carta
        $query = Platillo::with(['categoria', 'temporada', 'alergenos', 'ingredientes'])
            ->where('estatus', 'activo')
            ->where('disponible', true);

        if ($request->filled('categoria_id')) {
            $query->where('categoria_id', $request->categoria_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'ILIKE', "%{$search}%")
                ->orWhere('descripcion', 'ILIKE', "%{$search}%");
            });
        }

        if ($request->filled('estatus')) {
            $query->where('estatus', $request->estatus);
        }

        $platillos = $query->orderBy('categoria_id')->orderBy('nombre')->paginate(12);
        $categorias = CategoriaPlatillo::orderBy('nombre')->get();

        return view('platillos.index', compact('platillos', 'categorias'));
    }

    public function create()
    {
        $categorias = CategoriaPlatillo::orderBy('nombre')->get();
        $temporadas = Temporada::orderBy('nombre')->get();
        $ingredientes = Ingrediente::orderBy('nombre')->get();
        $alergias = Alergia::orderBy('nombre')->get();
        $sucursales = Sucursal::where('activa', true)->get();

        return view('platillos.create', compact('categorias', 'temporadas', 'ingredientes', 'alergias', 'sucursales'));
    }

    public function store(Request $request)
    {
        $rules = [
            'nombre' => 'required|string|max:120',
            'categoria_id' => 'required',
            'temporada_id' => 'nullable',
            'precio' => 'required|numeric|min:0',
            'descripcion' => 'nullable|string',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ];

        if ($request->categoria_id === 'nueva') {
            $rules['nueva_categoria_nombre'] = 'required|string|max:100';
            $rules['nueva_categoria_descripcion'] = 'nullable|string';
        } else {
            $rules['categoria_id'] = 'required|exists:categoria_platillo,id';
        }

        if ($request->filled('temporada_id') && $request->temporada_id === 'nueva') {
            $rules['nueva_temporada_nombre'] = 'required|string|max:100';
            $rules['nueva_temporada_inicio'] = 'nullable|date';
            $rules['nueva_temporada_fin'] = 'nullable|date';
        } elseif ($request->filled('temporada_id')) {
            $rules['temporada_id'] = 'exists:temporada,id';
        }

        $request->validate($rules);

        return DB::transaction(function () use ($request) {
            // 1. Manejo dinámico de Categoría
            if ($request->categoria_id === 'nueva') {
                $categoria = CategoriaPlatillo::firstOrCreate(
                    ['nombre' => trim($request->nueva_categoria_nombre)],
                    ['descripcion' => $request->nueva_categoria_descripcion]
                );
                $categoriaId = $categoria->id;
            } else {
                $categoriaId = $request->categoria_id;
            }

            // 2. Manejo dinámico de Temporada / Rama
            $temporadaId = null;
            if ($request->filled('temporada_id')) {
                if ($request->temporada_id === 'nueva') {
                    $inicio = $request->nueva_temporada_inicio ?: Carbon::now()->toDateString();
                    $fin = $request->nueva_temporada_fin ?: Carbon::now()->addMonths(6)->toDateString();
                    $temporada = Temporada::firstOrCreate(
                        ['nombre' => trim($request->nueva_temporada_nombre)],
                        [
                            'fecha_inicio' => $inicio,
                            'fecha_final' => $fin,
                        ]
                    );
                    $temporadaId = $temporada->id;
                } else {
                    $temporadaId = $request->temporada_id;
                }
            }

            // 3. Procesar archivo de imagen subido
            $imagenUrl = 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=600&q=80';
            if ($request->hasFile('imagen')) {
                $path = $request->file('imagen')->store('platillos', 'public');
                $imagenUrl = asset('storage/' . $path);
            }

            // 4. Crear Platillo
            $platillo = Platillo::create([
                'nombre' => $request->nombre,
                'categoria_id' => $categoriaId,
                'temporada_id' => $temporadaId,
                'precio' => $request->precio,
                'descripcion' => $request->descripcion,
                'imagen_url' => $imagenUrl,
                'estatus' => 'activo',
                'disponible' => true,
            ]);

            // 5. Manejo de múltiples ingredientes (existentes + dinámicos nuevos)
            $ingredientesIds = [];
            if ($request->filled('ingredientes') && is_array($request->ingredientes)) {
                $ingredientesIds = array_map('intval', $request->ingredientes);
            }

            // Nuevos ingredientes agregados dinámicamente uno a uno
            if ($request->filled('nuevos_ingredientes') && is_array($request->nuevos_ingredientes)) {
                foreach ($request->nuevos_ingredientes as $nuevoNombre) {
                    $nombreLimpio = trim($nuevoNombre);
                    if (!empty($nombreLimpio)) {
                        $nuevoIng = Ingrediente::firstOrCreate(
                            ['nombre' => $nombreLimpio],
                            ['descripcion' => 'Ingrediente registrado desde ficha técnica de platillo']
                        );
                        $ingredientesIds[] = $nuevoIng->id;
                    }
                }
            }

            // Nuevos ingredientes por texto en lote (separados por coma o salto de línea)
            if ($request->filled('nuevos_ingredientes_texto')) {
                $partes = preg_split('/[\r\n,]+/', $request->nuevos_ingredientes_texto);
                foreach ($partes as $parte) {
                    $nombreLimpio = trim($parte);
                    if (!empty($nombreLimpio)) {
                        $nuevoIng = Ingrediente::firstOrCreate(
                            ['nombre' => $nombreLimpio],
                            ['descripcion' => 'Ingrediente registrado desde ficha técnica de platillo']
                        );
                        $ingredientesIds[] = $nuevoIng->id;
                    }
                }
            }

            $ingredientesIds = array_values(array_unique(array_filter($ingredientesIds)));
            if (!empty($ingredientesIds)) {
                $platillo->ingredientes()->sync($ingredientesIds);
            }

            // 6. Alérgenos asociados
            if ($request->filled('alergias')) {
                $platillo->alergenos()->sync($request->alergias);
            }

            // 7. Habilitar en todas las sucursales activas
            $sucursales = Sucursal::where('activa', true)->get();
            foreach ($sucursales as $suc) {
                PlatilloSucursal::updateOrCreate([
                    'sucursal_id' => $suc->id,
                    'platillo_id' => $platillo->id,
                ], [
                    'precio_local' => $platillo->precio,
                    'disponible' => true,
                ]);
            }

            // 8. Registro en Bitácora de auditoría
            Bitacora::create([
                'usuario_id' => Auth::id(),
                'accion' => 'CREAR_PLAT',
                'registro_id' => "PLATILLO_{$platillo->id}",
                'fecha_hora' => Carbon::now(),
            ]);

            return redirect()->route('platillos.index')->with('success', "Platillo '{$platillo->nombre}' agregado al menú correctamente con sus categorías e ingredientes.");
        });
    }

    public function edit(Platillo $platillo)
    {
        $platillo->load(['ingredientes', 'alergenos']);
        $categorias = CategoriaPlatillo::orderBy('nombre')->get();
        $temporadas = Temporada::orderBy('nombre')->get();
        $ingredientes = Ingrediente::orderBy('nombre')->get();
        $alergias = Alergia::orderBy('nombre')->get();

        return view('platillos.edit', compact('platillo', 'categorias', 'temporadas', 'ingredientes', 'alergias'));
    }

    public function update(Request $request, Platillo $platillo)
    {
        $rules = [
            'nombre' => 'required|string|max:120',
            'categoria_id' => 'required',
            'temporada_id' => 'nullable',
            'precio' => 'required|numeric|min:0',
            'descripcion' => 'nullable|string',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'disponible' => 'boolean',
        ];

        if ($request->categoria_id === 'nueva') {
            $rules['nueva_categoria_nombre'] = 'required|string|max:100';
            $rules['nueva_categoria_descripcion'] = 'nullable|string';
        } else {
            $rules['categoria_id'] = 'required|exists:categoria_platillo,id';
        }

        if ($request->filled('temporada_id') && $request->temporada_id === 'nueva') {
            $rules['nueva_temporada_nombre'] = 'required|string|max:100';
            $rules['nueva_temporada_inicio'] = 'nullable|date';
            $rules['nueva_temporada_fin'] = 'nullable|date';
        } elseif ($request->filled('temporada_id')) {
            $rules['temporada_id'] = 'exists:temporada,id';
        }

        $request->validate($rules);

        return DB::transaction(function () use ($request, $platillo) {
            // 1. Resolver Categoría
            if ($request->categoria_id === 'nueva') {
                $categoria = CategoriaPlatillo::firstOrCreate(
                    ['nombre' => trim($request->nueva_categoria_nombre)],
                    ['descripcion' => $request->nueva_categoria_descripcion]
                );
                $categoriaId = $categoria->id;
            } else {
                $categoriaId = $request->categoria_id;
            }

            // 2. Resolver Temporada
            $temporadaId = null;
            if ($request->filled('temporada_id')) {
                if ($request->temporada_id === 'nueva') {
                    $inicio = $request->nueva_temporada_inicio ?: Carbon::now()->toDateString();
                    $fin = $request->nueva_temporada_fin ?: Carbon::now()->addMonths(6)->toDateString();
                    $temporada = Temporada::firstOrCreate(
                        ['nombre' => trim($request->nueva_temporada_nombre)],
                        [
                            'fecha_inicio' => $inicio,
                            'fecha_final' => $fin,
                        ]
                    );
                    $temporadaId = $temporada->id;
                } else {
                    $temporadaId = $request->temporada_id;
                }
            }

            // 3. Procesar nueva imagen si se subió una
            $imagenUrl = $platillo->imagen_url;
            if ($request->hasFile('imagen')) {
                $path = $request->file('imagen')->store('platillos', 'public');
                $imagenUrl = asset('storage/' . $path);
            }

            $platillo->update([
                'nombre' => $request->nombre,
                'categoria_id' => $categoriaId,
                'temporada_id' => $temporadaId,
                'precio' => $request->precio,
                'descripcion' => $request->descripcion,
                'imagen_url' => $imagenUrl,
                'disponible' => $request->has('disponible'),
            ]);

            // 4. Resolver Ingredientes
            $ingredientesIds = [];
            if ($request->filled('ingredientes') && is_array($request->ingredientes)) {
                $ingredientesIds = array_map('intval', $request->ingredientes);
            }

            if ($request->filled('nuevos_ingredientes') && is_array($request->nuevos_ingredientes)) {
                foreach ($request->nuevos_ingredientes as $nuevoNombre) {
                    $nombreLimpio = trim($nuevoNombre);
                    if (!empty($nombreLimpio)) {
                        $nuevoIng = Ingrediente::firstOrCreate(
                            ['nombre' => $nombreLimpio],
                            ['descripcion' => 'Ingrediente registrado desde ficha técnica de platillo']
                        );
                        $ingredientesIds[] = $nuevoIng->id;
                    }
                }
            }

            if ($request->filled('nuevos_ingredientes_texto')) {
                $partes = preg_split('/[\r\n,]+/', $request->nuevos_ingredientes_texto);
                foreach ($partes as $parte) {
                    $nombreLimpio = trim($parte);
                    if (!empty($nombreLimpio)) {
                        $nuevoIng = Ingrediente::firstOrCreate(
                            ['nombre' => $nombreLimpio],
                            ['descripcion' => 'Ingrediente registrado desde ficha técnica de platillo']
                        );
                        $ingredientesIds[] = $nuevoIng->id;
                    }
                }
            }

            $platillo->ingredientes()->sync(array_values(array_unique(array_filter($ingredientesIds))));
            $platillo->alergenos()->sync($request->alergias ?? []);

            Bitacora::create([
                'usuario_id' => Auth::id(),
                'accion' => 'EDIT_PLAT',
                'registro_id' => "PLATILLO_{$platillo->id}",
                'fecha_hora' => Carbon::now(),
            ]);

            return redirect()->route('platillos.index')->with('success', 'Platillo actualizado exitosamente.');
        });
    }

    public function toggle(Platillo $platillo)
    {
        $platillo->disponible = !$platillo->disponible;
        $platillo->save();

        Bitacora::create([
            'usuario_id' => Auth::id(),
            'accion' => 'DISP_PLAT',
            'registro_id' => "PLATILLO_{$platillo->id}_DISP_{$platillo->disponible}",
            'fecha_hora' => Carbon::now(),
        ]);

        return back()->with('success', 'Disponibilidad de platillo actualizada.');
    }

    public function destroy(Platillo $platillo)
    {
        $platillo->estatus = 'inactivo';
        $platillo->disponible = false;
        $platillo->save();

        return redirect()->route('platillos.index')->with('success', 'Platillo desactivado de la carta.');
    }
}