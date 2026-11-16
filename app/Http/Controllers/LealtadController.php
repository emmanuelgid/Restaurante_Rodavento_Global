<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{NivelLealtad, Beneficio, ReglaPuntos, Cliente, MovimientoPuntos};

class LealtadController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $niveles = NivelLealtad::with(['beneficios', 'clientes'])->orderBy('puntos_minimos')->get();
        $beneficios = Beneficio::with('nivelesLealtad')->get();
        $reglas = ReglaPuntos::orderByDesc('vigencia_inicio')->get();
        $ultimosMovimientos = MovimientoPuntos::with(['cliente', 'notaReservacion'])->latest('fecha')->take(10)->get();

        return view('lealtad.index', compact('niveles', 'beneficios', 'reglas', 'ultimosMovimientos'));
    }

    public function asignarBeneficio(Request $request, $nivelId)
    {
        $request->validate([
            'beneficio_id' => 'required|exists:beneficio,id',
        ]);

        $nivel = NivelLealtad::findOrFail($nivelId);
        $nivel->beneficios()->syncWithoutDetaching([$request->beneficio_id]);

        return back()->with('success', "Beneficio asignado exitosamente al nivel '{$nivel->nombre}'.");
    }

    public function quitarBeneficio($nivelId, $beneficioId)
    {
        $nivel = NivelLealtad::findOrFail($nivelId);
        $beneficio = Beneficio::findOrFail($beneficioId);
        $nivel->beneficios()->detach($beneficioId);

        return back()->with('success', "Beneficio '{$beneficio->nombre}' retirado del nivel '{$nivel->nombre}'.");
    }

    public function crearBeneficio(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'descripcion' => 'required|string|max:250',
            'nivel_id' => 'nullable|exists:nivel_lealtad,id',
            'vigencia_ini' => 'nullable|date',
            'vigencia_fin' => 'nullable|date',
        ]);

        $beneficio = Beneficio::create([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'vigencia_ini' => $request->vigencia_ini ?: now()->toDateString(),
            'vigencia_fin' => $request->vigencia_fin ?: now()->addYear()->toDateString(),
        ]);

        if ($request->filled('nivel_id')) {
            $nivel = NivelLealtad::findOrFail($request->nivel_id);
            $nivel->beneficios()->syncWithoutDetaching([$beneficio->id]);
            return back()->with('success', "Nuevo beneficio '{$beneficio->nombre}' creado y asignado al nivel '{$nivel->nombre}'.");
        }

        return back()->with('success', "Beneficio '{$beneficio->nombre}' registrado en el catálogo VIP.");
    }
}