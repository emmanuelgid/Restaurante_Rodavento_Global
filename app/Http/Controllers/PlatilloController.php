<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Platillo, CategoriaPlatillo, Temporada, Ingrediente, Alergia, Sucursal, PlatilloSucursal, Bitacora};
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PlatilloController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = Platillo::with(['categoria', 'temporada', 'alergenos', 'ingredientes']);

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
        $request->validate([
            'nombre' => 'required|string|max:120',
            'categoria_id' => 'required|exists:categoria_platillo,id',
            'temporada_id' => 'nullable|exists:temporada,id',
            'precio' => 'required|numeric|min:0',
            'descripcion' => 'nullable|string',
            'imagen_url' => 'nullable|url|max:500',
        ]);

        $platillo = Platillo::create([
            'nombre' => $request->nombre,
            'categoria_id' => $request->categoria_id,
            'temporada_id' => $request->temporada_id,
            'precio' => $request->precio,
            'descripcion' => $request->descripcion,
            'imagen_url' => $request->imagen_url ?? 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=600&q=80',
            'estatus' => 'activo',
            'disponible' => true,
        ]);

        if ($request->filled('ingredientes')) {
            $platillo->ingredientes()->sync($request->ingredientes);
        }

        if ($request->filled('alergias')) {
            $platillo->alergenos()->sync($request->alergias);
        }

        // Habilitar en todas las sucursales activas
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

        Bitacora::create([
            'usuario_id' => Auth::id(),
            'accion' => 'CREAR_PLAT',
            'registro_id' => "PLATILLO_{$platillo->id}",
            'fecha_hora' => Carbon::now(),
        ]);

        return redirect()->route('platillos.index')->with('success', 'Platillo agregado al menú correctamente.');
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
        $request->validate([
            'nombre' => 'required|string|max:120',
            'categoria_id' => 'required|exists:categoria_platillo,id',
            'temporada_id' => 'nullable|exists:temporada,id',
            'precio' => 'required|numeric|min:0',
            'descripcion' => 'nullable|string',
            'imagen_url' => 'nullable|url|max:500',
            'disponible' => 'boolean',
        ]);

        $platillo->update([
            'nombre' => $request->nombre,
            'categoria_id' => $request->categoria_id,
            'temporada_id' => $request->temporada_id,
            'precio' => $request->precio,
            'descripcion' => $request->descripcion,
            'imagen_url' => $request->imagen_url ?? $platillo->imagen_url,
            'disponible' => $request->has('disponible'),
        ]);

        $platillo->ingredientes()->sync($request->ingredientes ?? []);
        $platillo->alergenos()->sync($request->alergias ?? []);

        Bitacora::create([
            'usuario_id' => Auth::id(),
            'accion' => 'EDIT_PLAT',
            'registro_id' => "PLATILLO_{$platillo->id}",
            'fecha_hora' => Carbon::now(),
        ]);

        return redirect()->route('platillos.index')->with('success', 'Platillo actualizado exitosamente.');
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