<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Bitacora, SolicitudArco};

class BitacoraController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $bitacoras = Bitacora::with('usuario')->latest('fecha_hora')->paginate(20);
        $solicitudesArco = SolicitudArco::with(['cliente', 'historial'])->latest('fecha_solicitud')->get();

        return view('bitacora.index', compact('bitacoras', 'solicitudesArco'));
    }
}