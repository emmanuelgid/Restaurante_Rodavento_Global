<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\Models\{
    Rol, Permiso, Empleado, User, Sucursal, Mesa, MesaSucursal,
    EmpleadoSucursal, NivelLealtad, Beneficio, ReglaPuntos,
    Alergia, SeveridadAlergia, Preferencia, CategoriaPlatillo,
    Temporada, Ingrediente, Platillo, PlatilloSucursal,
    Cliente, Reservacion, ReservacionMesa, NotaReservacion,
    Pedido, PedidoDetalle, Bitacora
};

class RestauranteRodaventoSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Roles
        $rolesData = [
            ['nombre' => 'Administrador', 'descripcion' => 'Acceso total y configuración de la cadena'],
            ['nombre' => 'Gerente de Sucursal', 'descripcion' => 'Supervisión de operaciones, reportes y personal'],
            ['nombre' => 'Capitán de Meseros', 'descripcion' => 'Gestión de sala, asignación de mesas y atención VIP'],
            ['nombre' => 'Mesero', 'descripcion' => 'Toma de comandas, servicio y notas de consumo'],
            ['nombre' => 'Chef Ejecutivo', 'descripcion' => 'Control de cocina, recetas y disponibilidad de menú'],
            ['nombre' => 'Hostess', 'descripcion' => 'Recepción, check-in de reservaciones y asignación'],
            ['nombre' => 'Cajero', 'descripcion' => 'Cierre de cuentas, cobro y facturación'],
        ];
        $roles = [];
        foreach ($rolesData as $r) {
            $roles[$r['nombre']] = Rol::firstOrCreate(['nombre' => $r['nombre']], $r);
        }

        // 2. Permisos
        $permisosData = [
            ['codigo' => 'dashboard.ver', 'descripcion' => 'Ver métricas e indicadores generales'],
            ['codigo' => 'sucursales.admin', 'descripcion' => 'Administrar sucursales y planos de mesas'],
            ['codigo' => 'menu.admin', 'descripcion' => 'Crear y editar platillos e ingredientes'],
            ['codigo' => 'clientes.admin', 'descripcion' => 'Gestionar clientes, alergias y puntos'],
            ['codigo' => 'reservaciones.gestionar', 'descripcion' => 'Crear, confirmar y cancelar reservaciones'],
            ['codigo' => 'comandas.operar', 'descripcion' => 'Abrir cuentas, tomar pedidos y enviar a cocina'],
            ['codigo' => 'comandas.ajustar', 'descripcion' => 'Aplicar cancelaciones y cortesías'],
            ['codigo' => 'caja.cobrar', 'descripcion' => 'Cobrar y cerrar notas de reservación'],
            ['codigo' => 'auditoria.ver', 'descripcion' => 'Consultar bitácora y solicitudes ARCO'],
        ];
        foreach ($permisosData as $p) {
            $perm = Permiso::firstOrCreate(['codigo' => $p['codigo']], $p);
            // Asignar todos al Administrador
            DB::table('cadena.rol_permiso')->updateOrInsert([
                'rol_id' => $roles['Administrador']->id,
                'permiso_id' => $perm->id,
            ]);
        }

        // 3. Empleados y Usuarios
        $adminEmp = Empleado::firstOrCreate(
            ['telefono' => '7221234567'],
            [
                'nombres' => 'Carlos',
                'apellidos' => 'Mendoza Varela',
                'puesto' => 'Director de Operaciones',
                'fecha_ingreso' => '2023-01-15',
                'estatus' => 'activo',
            ]
        );

        $gerenteEmp = Empleado::firstOrCreate(
            ['telefono' => '7229876543'],
            [
                'nombres' => 'Valeria',
                'apellidos' => 'Gómez Fuentes',
                'puesto' => 'Gerente General',
                'fecha_ingreso' => '2023-06-01',
                'estatus' => 'activo',
            ]
        );

        $meseroEmp = Empleado::firstOrCreate(
            ['telefono' => '7225551234'],
            [
                'nombres' => 'Mateo',
                'apellidos' => 'López Salinas',
                'puesto' => 'Capitán de Sala',
                'fecha_ingreso' => '2024-02-10',
                'estatus' => 'activo',
            ]
        );

        // Crear User Admin si no existe
        $adminUser = User::where('email', 'admin@rodavento.com')->first();
        if (!$adminUser) {
            $adminUser = User::create([
                'name' => 'Carlos Mendoza (Admin)',
                'email' => 'admin@rodavento.com',
                'password' => Hash::make('rodavento123'),
                'empleado_id' => $adminEmp->id,
                'rol_id' => $roles['Administrador']->id,
                'activo' => true,
            ]);
        }

        // 4. Sucursales
        $sucursal1 = Sucursal::firstOrCreate(
            ['nombre' => 'Restaurante Lago Rodavento'],
            [
                'direccion' => 'Km 3.5 Carretera Valle de Bravo - Los Álamos, Valle de Bravo, Estado de México',
                'telefono' => '7262628900',
                'activa' => true,
            ]
        );

        $sucursal2 = Sucursal::firstOrCreate(
            ['nombre' => 'Cinco Rodavento Rooftop'],
            [
                'direccion' => 'Callejón de Alfareros 4, Centro, Valle de Bravo, Estado de México',
                'telefono' => '7262629100',
                'activa' => true,
            ]
        );

        // Asignar empleados a sucursales
        DB::table('cadena.empleado_sucursal')->updateOrInsert(['empleado_id' => $adminEmp->id, 'sucursal_id' => $sucursal1->id]);
        DB::table('cadena.empleado_sucursal')->updateOrInsert(['empleado_id' => $gerenteEmp->id, 'sucursal_id' => $sucursal1->id]);
        DB::table('cadena.empleado_sucursal')->updateOrInsert(['empleado_id' => $meseroEmp->id, 'sucursal_id' => $sucursal1->id]);

        // 5. Mesas
        $mesasConfig = [
            ['codigo_mesa' => 'LAGO-01', 'capacidad' => 4],
            ['codigo_mesa' => 'LAGO-02', 'capacidad' => 4],
            ['codigo_mesa' => 'LAGO-03', 'capacidad' => 6],
            ['codigo_mesa' => 'BOSQ-01', 'capacidad' => 2],
            ['codigo_mesa' => 'BOSQ-02', 'capacidad' => 4],
            ['codigo_mesa' => 'BOSQ-03', 'capacidad' => 8],
            ['codigo_mesa' => 'CAVA-01', 'capacidad' => 12],
            ['codigo_mesa' => 'TERR-01', 'capacidad' => 4],
            ['codigo_mesa' => 'ROOF-01', 'capacidad' => 4],
            ['codigo_mesa' => 'ROOF-02', 'capacidad' => 6],
        ];

        foreach ($mesasConfig as $mc) {
            $mesa = Mesa::firstOrCreate(['codigo_mesa' => $mc['codigo_mesa']], [
                'capacidad' => $mc['capacidad'],
                'activa' => true,
            ]);

            $targetSucursal = str_starts_with($mc['codigo_mesa'], 'ROOF') ? $sucursal2->id : $sucursal1->id;
            MesaSucursal::firstOrCreate([
                'sucursal_id' => $targetSucursal,
                'mesa_id' => $mesa->id,
            ]);
        }

        // 6. Alergias y Severidades
        $severidades = [
            ['nombre' => 'Leve (Preferencia/Sensibilidad)', 'prioridad' => 1],
            ['nombre' => 'Moderada (Intolerancia)', 'prioridad' => 2],
            ['nombre' => 'Alta (Reacción Alérgica Severa)', 'prioridad' => 3],
            ['nombre' => 'Crítica (Shock Anafiláctico)', 'prioridad' => 4],
        ];
        foreach ($severidades as $s) {
            SeveridadAlergia::firstOrCreate(['nombre' => $s['nombre']], $s);
        }

        $alergias = [
            ['nombre' => 'Gluten / Celíaco', 'descripcion' => 'Presente en harinas de trigo, cebada y derivados'],
            ['nombre' => 'Mariscos y Crustáceos', 'descripcion' => 'Camarón, langosta, pulpo y moluscos'],
            ['nombre' => 'Frutos Secos', 'descripcion' => 'Nueces, avellanas, piñón y cacahuates'],
            ['nombre' => 'Lácteos / Lactosa', 'descripcion' => 'Leche, crema y quesos no curados'],
            ['nombre' => 'Huevo', 'descripcion' => 'Clara y yema de huevo de ave'],
        ];
        foreach ($alergias as $a) {
            Alergia::firstOrCreate(['nombre' => $a['nombre']], $a);
        }

        // 7. Preferencias
        $preferencias = [
            ['tipo' => 'Ubicación', 'descripcion' => 'Mesa con vista directa al lago'],
            ['tipo' => 'Ubicación', 'descripcion' => 'Junto a la chimenea interior'],
            ['tipo' => 'Dieta', 'descripcion' => 'Vegetariano estricto'],
            ['tipo' => 'Dieta', 'descripcion' => 'Bajo en sodio / Sin sal añadida'],
            ['tipo' => 'Ambiente', 'descripcion' => 'Zona tranquila para conversación de negocios'],
        ];
        foreach ($preferencias as $p) {
            Preferencia::firstOrCreate(['tipo' => $p['tipo'], 'descripcion' => $p['descripcion']], $p);
        }

        // 8. Niveles de Lealtad y Beneficios
        $niveles = [
            ['nombre' => 'Huésped Bronce', 'puntos_minimos' => 0],
            ['nombre' => 'Socio Plata', 'puntos_minimos' => 1500],
            ['nombre' => 'Selecto Oro', 'puntos_minimos' => 5000],
            ['nombre' => 'Rodavento Black', 'puntos_minimos' => 12000],
        ];
        $nivelesModel = [];
        foreach ($niveles as $n) {
            $nivelesModel[$n['nombre']] = NivelLealtad::firstOrCreate(['nombre' => $n['nombre']], $n);
        }

        $beneficios = [
            ['nombre' => 'Coctel de Bienvenida de Autor', 'descripcion' => 'Bebida insignia preparada por nuestro mixólogo', 'vigencia_ini' => '2026-01-01', 'vigencia_fin' => '2026-12-31'],
            ['nombre' => '15% de Descuento en Cava de Vinos', 'descripcion' => 'Válido en botellas seleccionadas de bodega mexicana', 'vigencia_ini' => '2026-01-01', 'vigencia_fin' => '2026-12-31'],
            ['nombre' => 'Reserva Prioritaria y Mesa VIP', 'descripcion' => 'Garantía de ubicación en Terraza Lago sin espera', 'vigencia_ini' => '2026-01-01', 'vigencia_fin' => '2026-12-31'],
            ['nombre' => 'Postre de Degustación del Chef', 'descripcion' => 'Cortesía dulce al término de la cena', 'vigencia_ini' => '2026-01-01', 'vigencia_fin' => '2026-12-31'],
        ];
        foreach ($beneficios as $b) {
            $ben = Beneficio::firstOrCreate(['nombre' => $b['nombre']], $b);
            // Asignar beneficios a Oro y Black
            DB::table('cadena.nivel_beneficio')->updateOrInsert([
                'nivel_lealtad_id' => $nivelesModel['Rodavento Black']->id,
                'beneficio_id' => $ben->id,
            ]);
        }

        // 9. Regla de Puntos
        ReglaPuntos::firstOrCreate(
            ['vigencia_inicio' => '2026-01-01'],
            [
                'monto_por_punto' => 10.00,
                'vigencia_fin' => '2026-12-31',
                'activa' => true,
            ]
        );

        // 10. Categorías y Temporada
        $temporada = Temporada::firstOrCreate(
            ['nombre' => 'Cosecha de Otoño & Raíces de Valle'],
            [
                'fecha_inicio' => '2026-09-01',
                'fecha_final' => '2026-12-31',
            ]
        );

        $categorias = [
            ['nombre' => 'Entradas & Del Huerto', 'descripcion' => 'Vegetales frescos cosechados en huertos locales y quesos artesanales'],
            ['nombre' => 'Pescas & Trucha de Valle', 'descripcion' => 'Trucha arcoíris sustentable y capturas frescas de temporada'],
            ['nombre' => 'Fuego & Carnes del Bosque', 'descripcion' => 'Cortes al carbón de encino y recetas de cocción lenta'],
            ['nombre' => 'Postres Artesanales', 'descripcion' => 'Creaciones dulces con frutos de temporada y miel de bosque'],
            ['nombre' => 'Mixología & Bodega', 'descripcion' => 'Coctelería de autor y selección de vinos mexicanos'],
        ];
        $cats = [];
        foreach ($categorias as $c) {
            $cats[$c['nombre']] = CategoriaPlatillo::firstOrCreate(['nombre' => $c['nombre']], $c);
        }

        // 11. Ingredientes
        $ingredientes = [
            'Trucha Arcoíris de Valle de Bravo',
            'Hongos Silvestres Porcini',
            'Queso de Cabra de Rancho Avándaro',
            'Miel de Abeja de Bosque de Niebla',
            'Piñón Rosa de Nevado',
            'Ribeye Angus Choice al Carbón',
            'Chocolate Amargo de Metate 75%',
            'Mantequilla de Hierbas Locales',
        ];
        $ings = [];
        foreach ($ingredientes as $ing) {
            $ings[$ing] = Ingrediente::firstOrCreate(['nombre' => $ing], ['descripcion' => "Ingrediente fresco: $ing"]);
        }

        // 12. Platillos Insignia
        $platillosData = [
            [
                'nombre' => 'Trucha Salmonada al Eneldo y Mantequilla de Avellana',
                'descripcion' => 'Filete de trucha de manantial de Valle de Bravo con emulsión tibia de avellanas tostadas y brotes tiernos.',
                'categoria' => 'Pescas & Trucha de Valle',
                'precio' => 460.00,
                'imagen_url' => 'https://images.unsplash.com/photo-1519708227418-c8fd9a32b7a2?auto=format&fit=crop&w=600&q=80',
            ],
            [
                'nombre' => 'Carpaccio de Betabel Rustico con Queso de Cabra Avándaro',
                'descripcion' => 'Láminas finas de betabel rostizado a las brasas, queso cremoso de cabra, piñones tostados y vinagreta de miel de bosque.',
                'categoria' => 'Entradas & Del Huerto',
                'precio' => 290.00,
                'imagen_url' => 'https://images.unsplash.com/photo-1540420773420-3366772f4999?auto=format&fit=crop&w=600&q=80',
            ],
            [
                'nombre' => 'Costillar de Res Braseado al Vino Tinto y Hongos de San Cayetano',
                'descripcion' => 'Cocción lenta por 14 horas en cazuela de barro, puré terso de papa trufada y jugo concentrado de asado.',
                'categoria' => 'Fuego & Carnes del Bosque',
                'precio' => 640.00,
                'imagen_url' => 'https://images.unsplash.com/photo-1544025162-d76694265947?auto=format&fit=crop&w=600&q=80',
            ],
            [
                'nombre' => 'Tarta Tibia de Chocolate de Metate con Helado de Lavanda',
                'descripcion' => 'Corazón fundente de cacao oaxaqueño con helado artesanal infusionado en flores frescas de nuestro jardín.',
                'categoria' => 'Postres Artesanales',
                'precio' => 230.00,
                'imagen_url' => 'https://images.unsplash.com/photo-1579372786545-d24232daf58c?auto=format&fit=crop&w=600&q=80',
            ],
            [
                'nombre' => 'Coctel Niebla de Rodavento',
                'descripcion' => 'Gin infusionado con romero silvestre, licor de saúco, manzana verde prensada en frío y perfume ahumado de ocote.',
                'categoria' => 'Mixología & Bodega',
                'precio' => 245.00,
                'imagen_url' => 'https://images.unsplash.com/photo-1514362545857-3bc16c4c7d1b?auto=format&fit=crop&w=600&q=80',
            ],
        ];

        foreach ($platillosData as $pData) {
            $platillo = Platillo::firstOrCreate(
                ['nombre' => $pData['nombre']],
                [
                    'categoria_id' => $cats[$pData['categoria']]->id,
                    'temporada_id' => $temporada->id,
                    'descripcion' => $pData['descripcion'],
                    'precio' => $pData['precio'],
                    'imagen_url' => $pData['imagen_url'],
                    'estatus' => 'activo',
                    'disponible' => true,
                ]
            );

            // Disponibilidad por sucursal
            PlatilloSucursal::firstOrCreate([
                'sucursal_id' => $sucursal1->id,
                'platillo_id' => $platillo->id,
            ], [
                'precio_local' => $pData['precio'],
                'disponible' => true,
            ]);
        }

        // 13. Clientes VIP
        $cliente1 = Cliente::firstOrCreate(
            ['correo' => 'fernando.alvarez@luxurytravel.com'],
            [
                'nombres' => 'Fernando',
                'apellidos' => 'Álvarez del Castillo',
                'telefono' => '5554329876',
                'fecha_nacimiento' => '1982-04-18',
                'sucursal_alta_id' => $sucursal1->id,
                'nivel_lealtad_id' => $nivelesModel['Rodavento Black']->id,
                'puntos_saldo' => 14500,
                'puntos_acumulados' => 18000,
                'tratamiento_bloqueado' => false,
                'activo' => true,
            ]
        );

        $cliente2 = Cliente::firstOrCreate(
            ['correo' => 'sofia.villalobos@artevalle.org'],
            [
                'nombres' => 'Sofía',
                'apellidos' => 'Villalobos Morales',
                'telefono' => '5543217890',
                'fecha_nacimiento' => '1990-11-25',
                'sucursal_alta_id' => $sucursal1->id,
                'nivel_lealtad_id' => $nivelesModel['Selecto Oro']->id,
                'puntos_saldo' => 6200,
                'puntos_acumulados' => 7800,
                'tratamiento_bloqueado' => false,
                'activo' => true,
            ]
        );

        // 14. Reservación de demostración
        $reservacion = Reservacion::firstOrCreate(
            ['cliente_id' => $cliente1->id, 'fecha_hora_inicio' => Carbon::now()->addHours(2)],
            [
                'fecha_hora_final' => Carbon::now()->addHours(4),
                'cantidad_personas' => 4,
                'estatus' => 'confirmada',
                'notas' => 'Celebración de aniversario. Solicitó mesa con vista despejada al lago.',
            ]
        );

        // Asignar mesa LAGO-01
        $mesaSuc = MesaSucursal::where('sucursal_id', $sucursal1->id)->first();
        if ($mesaSuc) {
            DB::table('cadena.reservacion_mesa')->updateOrInsert([
                'reservacion_id' => $reservacion->id,
                'mesa_sucursal_id' => $mesaSuc->id,
            ], [
                'periodo' => "[\"{$reservacion->fecha_hora_inicio}\",\"{$reservacion->fecha_hora_final}\")",
                'bloquea' => true,
            ]);
        }

        // 15. Comanda / Nota de Reservación
        $nota = NotaReservacion::firstOrCreate(
            ['reservacion_id' => $reservacion->id],
            [
                'sucursal_id' => $sucursal1->id,
                'empleado_id' => $meseroEmp->id,
                'subtotal' => 2095.00,
                'descuento' => 150.00,
                'impuestos' => 311.20,
                'propina' => 300.00,
                'total' => 2556.20,
                'calificacion' => 5,
                'comentarios' => 'Servicio impecable y experiencia gastronómica de primer nivel.',
                'fecha_emision' => Carbon::now(),
            ]
        );

        // Pedido
        $pedido = Pedido::firstOrCreate(
            ['nota_reservacion_id' => $nota->id],
            [
                'estatus' => 'en_proceso',
                'fecha_hora' => Carbon::now(),
            ]
        );

        // Detalle de pedido
        $platillos = Platillo::take(3)->get();
        foreach ($platillos as $pl) {
            PedidoDetalle::firstOrCreate([
                'pedido_id' => $pedido->id,
                'platillo_id' => $pl->id,
            ], [
                'cantidad' => 2,
                'cantidad_cancelada' => 0,
                'precio_unitario' => $pl->precio,
                'es_cortesia' => false,
                'notas' => 'Término medio para carnes y salsa por separado.',
            ]);
        }

        // Registrar en bitácora
        Bitacora::create([
            'usuario_id' => $adminUser->id,
            'accion' => 'SEED_INIT',
            'registro_id' => 'SISTEMA_RESTAURANTE_RODAVENTO',
            'fecha_hora' => Carbon::now(),
        ]);
    }
}

