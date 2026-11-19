<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\{
    Cliente, NivelLealtad, Sucursal, Alergia, SeveridadAlergia,
    Preferencia, MovimientoPuntos, Bitacora, Consentimiento, SolicitudArco
};
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ClienteController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = Cliente::with(['nivelLealtad', 'sucursalAlta', 'alergias']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nombres', 'ILIKE', "%{$search}%")
                    ->orWhere('apellidos', 'ILIKE', "%{$search}%")
                    ->orWhere('correo', 'ILIKE', "%{$search}%")
                    ->orWhere('telefono', 'LIKE', "%{$search}%");
            });
        }

        if ($request->filled('nivel_id')) {
            $query->where('nivel_lealtad_id', $request->nivel_id);
        }

        $clientes = $query->orderBy('puntos_saldo', 'desc')->paginate(12);
        $niveles = NivelLealtad::all();

        return view('clientes.index', compact('clientes', 'niveles'));
    }

    public function create()
    {
        $niveles = NivelLealtad::all();
        $sucursales = Sucursal::where('activa', true)->get();
        $alergias = Alergia::all();
        $severidades = SeveridadAlergia::all();
        $preferencias = Preferencia::all();

        return view('clientes.create', compact('niveles', 'sucursales', 'alergias', 'severidades', 'preferencias'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombres' => 'required|string|max:150',
            'apellidos' => 'required|string|max:150',
            'correo' => 'required|email|max:150|unique:cliente,correo',
            'telefono' => 'nullable|string|max:20',
            'fecha_nacimiento' => 'nullable|date',
            'sucursal_alta_id' => 'required|exists:sucursal,id',
            'nivel_lealtad_id' => 'required|exists:nivel_lealtad,id',
            'nueva_alergia_nombre' => 'nullable|string|max:80',
            'nueva_alergia_descripcion' => 'nullable|string',
        ]);

        return DB::transaction(function () use ($request) {
            $cliente = Cliente::create([
                'nombres' => $request->nombres,
                'apellidos' => $request->apellidos,
                'correo' => $request->correo,
                'telefono' => $request->telefono,
                'fecha_nacimiento' => $request->fecha_nacimiento,
                'sucursal_alta_id' => $request->sucursal_alta_id,
                'nivel_lealtad_id' => $request->nivel_lealtad_id,
                'fecha_alta' => Carbon::now(),
                'puntos_saldo' => 0,
                'puntos_acumulados' => 0,
                'activo' => true,
            ]);

            // Determinar alergia (existente o nueva)
            $alergiaId = $request->alergia_id;
            if ($alergiaId === 'nueva' || $request->filled('nueva_alergia_nombre')) {
                if ($request->filled('nueva_alergia_nombre')) {
                    $nuevaAlergia = Alergia::firstOrCreate(
                        ['nombre' => trim($request->nueva_alergia_nombre)],
                        ['descripcion' => $request->nueva_alergia_descripcion ?? 'Registrada desde alta de cliente']
                    );
                    $alergiaId = $nuevaAlergia->id;
                } else {
                    $alergiaId = null;
                }
            }

            if ($alergiaId && is_numeric($alergiaId)) {
                DB::table('cadena.cliente_alergia')->insert([
                    'cliente_id' => $cliente->id,
                    'alergia_id' => (int) $alergiaId,
                    'severidad_id' => $request->severidad_id ?? 1,
                    'notas' => $request->notas_alergia ?? 'Registrado al alta',
                ]);
            }

            Bitacora::create([
                'usuario_id' => Auth::id(),
                'accion' => 'CREAR_CLI',
                'registro_id' => "CLIENTE_{$cliente->id}",
                'fecha_hora' => Carbon::now(),
            ]);

            return redirect()->route('clientes.show', $cliente->id)->with('success', 'Cliente registrado exitosamente en el sistema de lealtad.');
        });
    }

    public function show(Cliente $cliente)
    {
        $cliente->load([
            'nivelLealtad.beneficios',
            'sucursalAlta',
            'alergias',
            'preferencias',
            'reservaciones.mesasAsignadas.mesaSucursal.mesa',
            'movimientosPuntos.notaReservacion',
            'fechasEspeciales',
            'consentimientos',
            'solicitudesArco.historial'
        ]);

        $alergiasDisponibles = Alergia::all();
        $severidades = SeveridadAlergia::all();
        $preferenciasDisponibles = Preferencia::all();

        return view('clientes.show', compact('cliente', 'alergiasDisponibles', 'severidades', 'preferenciasDisponibles'));
    }

    public function agregarAlergia(Request $request, Cliente $cliente)
    {
        $request->validate([
            'alergia_id' => 'required',
            'severidad_id' => 'required|exists:severidad_alergia,id',
            'notas' => 'nullable|string',
            'nueva_alergia_nombre' => 'nullable|string|max:80',
            'nueva_alergia_descripcion' => 'nullable|string',
        ]);

        $alergiaId = $request->alergia_id;
        if ($alergiaId === 'nueva' || $request->filled('nueva_alergia_nombre')) {
            if (!$request->filled('nueva_alergia_nombre')) {
                return back()->withErrors(['nueva_alergia_nombre' => 'Debe ingresar el nombre de la nueva alergia.']);
            }
            $nuevaAlergia = Alergia::firstOrCreate(
                ['nombre' => trim($request->nueva_alergia_nombre)],
                ['descripcion' => $request->nueva_alergia_descripcion ?? 'Registrada desde expediente del cliente']
            );
            $alergiaId = $nuevaAlergia->id;
        }

        DB::table('cadena.cliente_alergia')->updateOrInsert(
            ['cliente_id' => $cliente->id, 'alergia_id' => (int) $alergiaId],
            ['severidad_id' => $request->severidad_id, 'notas' => $request->notas]
        );

        return back()->with('success', 'Alérgeno vinculado al expediente del cliente.');
    }

    public function ajustarPuntos(Request $request, Cliente $cliente)
    {
        $request->validate([
            'puntos' => 'required|integer|min:1',
            'tipo'   => 'required|in:acumulacion,redencion,bonificacion,ajuste',
            'motivo' => 'required|string|max:200',
        ]);

        $cantPuntos = (int) $request->puntos;
        $tipo = $request->tipo;

        if ($tipo === 'redencion') {
            // Validar saldo suficiente
            if ($cliente->puntos_saldo < $cantPuntos) {
                return back()->withErrors([
                    'puntos' => "El cliente no cuenta con saldo suficiente. Saldo disponible: {$cliente->puntos_saldo} pts."
                ]);
            }

            // Deducción de saldo disponible (sin afectar el histórico de puntos acumulados)
            $cliente->puntos_saldo -= $cantPuntos;
            $puntosMovimiento = -$cantPuntos; // Guardar valor negativo (-500) para el historial
        } else {
            // Adición a saldo disponible y acumulado histórico
            $cliente->puntos_saldo += $cantPuntos;
            $cliente->puntos_acumulados += $cantPuntos;
            $puntosMovimiento = $cantPuntos; // Guardar valor positivo (+500)
        }

        $cliente->save();

        MovimientoPuntos::create([
            'cliente_id' => $cliente->id,
            'puntos'     => $puntosMovimiento,
            'tipo'       => $tipo,
            'fecha'      => Carbon::now(),
        ]);

        Bitacora::create([
            'usuario_id'  => Auth::id(),
            'accion'      => 'PTS_AJUSTE',
            'registro_id' => "CLI_{$cliente->id}_PTS_{$puntosMovimiento}_{$tipo}",
            'fecha_hora'  => Carbon::now(),
        ]);

        $mensaje = ($tipo === 'redencion')
            ? "Se han canjeado exitosamente {$cantPuntos} puntos."
            : "Ajuste de +{$cantPuntos} puntos bonificados correctamente.";

        return back()->with('success', $mensaje);
    }
}