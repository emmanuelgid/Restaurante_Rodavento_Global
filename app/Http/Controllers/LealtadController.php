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
}