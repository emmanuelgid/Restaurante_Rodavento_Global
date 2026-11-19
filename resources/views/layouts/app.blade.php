<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Restaurante Rodavento') - Sistema de Gestión Gastronómica</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,500;0,600;0,700;1,400&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">

    <style>
        :root {
            --rv-navy: #0b1d26;
            --rv-navy-light: #16303d;
            --rv-gold: #fbd784;
            --rv-gold-dark: #d8b257;
            --rv-forest: #1f3a2c;
            --rv-cream: #fbf9f5;
            --rv-border: rgba(11, 29, 38, 0.08);
        }

        body {
            font-family: 'Montserrat', sans-serif;
            background-color: #f4f6f8;
            color: #2c3e50;
            min-height: 100vh;
        }

        h1, h2, h3, .font-serif, .navbar-brand {
            font-family: 'Playfair Display', serif;
        }

        /* Top Luxury Navbar */
        .navbar-rodavento {
            background-color: var(--rv-navy);
            border-bottom: 2px solid var(--rv-gold);
            padding: 0.75rem 1.5rem;
        }

        .navbar-brand-logo {
            font-size: 1.35rem;
            color: #ffffff !important;
            font-weight: 700;
            letter-spacing: 1px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .navbar-brand-logo span {
            color: var(--rv-gold);
            font-size: 0.8rem;
            letter-spacing: 2px;
            font-family: 'Montserrat', sans-serif;
            text-transform: uppercase;
            display: block;
            font-weight: 400;
        }

        .nav-link-custom {
            color: rgba(255, 255, 255, 0.82) !important;
            font-weight: 500;
            font-size: 0.88rem;
            padding: 0.5rem 0.9rem !important;
            border-radius: 6px;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .nav-link-custom:hover, .nav-link-custom.active {
            color: var(--rv-gold) !important;
            background-color: rgba(251, 215, 132, 0.12);
        }

        /* Subheader Bar */
        .subheader-bar {
            background: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            padding: 1rem 0;
            margin-bottom: 1.75rem;
        }

        /* Cards & Buttons */
        .card-custom {
            border: 1px solid rgba(0,0,0,0.06);
            border-radius: 10px;
            background: #ffffff;
            box-shadow: 0 4px 12px rgba(0,0,0,0.03);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .card-custom:hover {
            box-shadow: 0 6px 18px rgba(0,0,0,0.06);
        }

        .btn-rv-primary {
            background-color: var(--rv-navy);
            color: #ffffff;
            font-weight: 600;
            border: 1px solid var(--rv-navy);
            padding: 0.5rem 1.25rem;
            border-radius: 6px;
            transition: all 0.2s ease;
        }

        .btn-rv-primary:hover {
            background-color: var(--rv-navy-light);
            color: var(--rv-gold);
            border-color: var(--rv-gold);
        }

        .btn-rv-gold {
            background-color: var(--rv-gold);
            color: var(--rv-navy);
            font-weight: 600;
            border: 1px solid var(--rv-gold-dark);
            padding: 0.5rem 1.25rem;
            border-radius: 6px;
            transition: all 0.2s ease;
        }

        .btn-rv-gold:hover {
            background-color: var(--rv-gold-dark);
            color: #000000;
        }

        /* Loyalty Badges */
        .badge-black {
            background: linear-gradient(135deg, #111, #333);
            color: #fbd784;
            border: 1px solid #fbd784;
        }
        .badge-oro {
            background: linear-gradient(135deg, #d4af37, #f3e5ab);
            color: #0b1d26;
            font-weight: 600;
        }
        .badge-plata {
            background: linear-gradient(135deg, #bdc3c7, #2c3e50);
            color: #fff;
        }
        .badge-bronce {
            background: linear-gradient(135deg, #a77044, #cd7f32);
            color: #fff;
        }

        /* Status Pills */
        .status-pill {
            padding: 0.28rem 0.75rem;
            border-radius: 50rem;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
    </style>
    @stack('styles')
</head>
<body>
<div id="app">
    <nav class="navbar navbar-expand-lg navbar-dark navbar-rodavento sticky-top">
        <div class="container-fluid px-lg-4">
            <a class="navbar-brand navbar-brand-logo" href="{{ url('/home') }}">
                <i class="bi bi-compass text-warning fs-3"></i>
                <div>
                    HOTEL RODAVENTO
                    <span>Restaurante & Experiencias</span>
                </div>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                @auth
                    <!-- Main Navigation Links -->
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-3">
                        <li class="nav-item">
                            <a class="nav-link-custom {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">
                                <i class="bi bi-speedometer2"></i> Dashboard
                            </a>
                        </li>

                        @can('reservaciones.gestionar')
                            <li class="nav-item">
                                <a class="nav-link-custom {{ request()->routeIs('reservaciones.*') ? 'active' : '' }}" href="{{ route('reservaciones.index') }}">
                                    <i class="bi bi-calendar-check"></i> Reservaciones
                                </a>
                            </li>
                        @endcan

                        @can('comandas.operar')
                            <li class="nav-item">
                                <a class="nav-link-custom {{ request()->routeIs('comandas.*') ? 'active' : '' }}" href="{{ route('comandas.index') }}">
                                    <i class="bi bi-receipt"></i> Comandas & POS
                                </a>
                            </li>
                        @endcan

                        @can('menu.ver')
                            <li class="nav-item">
                                <a class="nav-link-custom {{ request()->routeIs('platillos.*') ? 'active' : '' }}" href="{{ route('platillos.index') }}">
                                    <i class="bi bi-egg-fried"></i> Carta & Menú
                                </a>
                            </li>
                        @endcan

                        @can('clientes.admin')
                            <li class="nav-item">
                                <a class="nav-link-custom {{ request()->routeIs('clientes.*') ? 'active' : '' }}" href="{{ route('clientes.index') }}">
                                    <i class="bi bi-people"></i> Clientes
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link-custom {{ request()->routeIs('lealtad.*') ? 'active' : '' }}" href="{{ route('lealtad.index') }}">
                                    <i class="bi bi-star-fill text-warning"></i> Lealtad VIP
                                </a>
                            </li>
                        @endcan

                        @canany(['sucursales.admin', 'mesas.ver', 'empleados.ver', 'auditoria.ver', 'empleados.admin'])
                            <li class="nav-item dropdown">
                                <a class="nav-link-custom dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="bi bi-gear"></i> Más Módulos
                                </a>
                                <ul class="dropdown-menu shadow border-0">
                                    @can('sucursales.admin')
                                        <li><a class="dropdown-item py-2" href="{{ route('sucursales.index') }}"><i class="bi bi-buildings me-2 text-primary"></i> Sucursales</a></li>
                                    @endcan

                                    @can('mesas.ver')
                                        <li><a class="dropdown-item py-2" href="{{ route('mesas.index') }}"><i class="bi bi-grid-3x3-gap me-2 text-success"></i> Mesas & Sala</a></li>
                                    @endcan

                                    @can('empleados.ver')
                                        <li><a class="dropdown-item py-2" href="{{ route('empleados.index') }}"><i class="bi bi-person-badge me-2 text-info"></i> Personal & Turnos</a></li>
                                    @endcan

                                    <!-- Opción exclusiva para aprobación de cuentas de nuevos empleados -->
                                    @can('empleados.admin')
                                        <li><a class="dropdown-item py-2" href="{{ route('admin.empleados.pendientes') }}"><i class="bi bi-person-check-fill me-2 text-danger"></i> Aprobación de Cuentas</a></li>
                                    @endcan

                                    @if(auth()->user()->canAny(['sucursales.admin', 'mesas.ver', 'empleados.ver', 'empleados.admin']) && auth()->user()->can('auditoria.ver'))
                                        <li><hr class="dropdown-divider"></li>
                                    @endif

                                    @can('auditoria.ver')
                                        <li><a class="dropdown-item py-2" href="{{ route('bitacora.index') }}"><i class="bi bi-shield-lock me-2 text-danger"></i> Auditoría & Bitácora</a></li>
                                    @endcan

                                    <li><a class="dropdown-item py-2" href="{{ route('welcome') }}" target="_blank"><i class="bi bi-globe me-2 text-secondary"></i> Ver Sitio Web Público</a></li>
                                </ul>
                            </li>
                        @endcanany
                    </ul>

                    <!-- User and Branch Profile -->
                    <ul class="navbar-nav ms-auto align-items-center">
                        <li class="nav-item dropdown">
                            <a id="navbarDropdown" class="nav-link dropdown-toggle text-white d-flex align-items-center gap-2" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                <div class="rounded-circle bg-warning text-dark fw-bold d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 0.85rem;">
                                    {{ substr(Auth::user()->name, 0, 1) }}
                                </div>
                                <div class="text-start">
                                    <div class="fw-semibold text-white small" style="line-height: 1.2;">{{ Auth::user()->name }}</div>
                                    <div class="text-warning text-uppercase" style="font-size: 0.68rem;">{{ Auth::user()->rol ? Auth::user()->rol->nombre : 'Usuario' }}</div>
                                </div>
                            </a>

                            <div class="dropdown-menu dropdown-menu-end shadow border-0" aria-labelledby="navbarDropdown">
                                <a class="dropdown-item py-2 text-danger" href="{{ route('logout') }}"
                                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="bi bi-box-arrow-right me-2"></i> Cerrar Sesión
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </div>
                        </li>
                    </ul>
                @else
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link text-white" href="{{ route('login') }}"><i class="bi bi-box-arrow-in-right me-1"></i> Iniciar Sesión</a>
                        </li>
                    </ul>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Flash Alert Messages -->
    <div class="container-fluid px-lg-4 mt-3">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 d-flex align-items-center" role="alert">
                <i class="bi bi-check-circle-fill fs-5 me-2"></i>
                <div>{{ session('success') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 d-flex align-items-center" role="alert">
                <i class="bi bi-exclamation-triangle-fill fs-5 me-2"></i>
                <div>{{ session('error') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-warning alert-dismissible fade show shadow-sm border-0" role="alert">
                <div class="fw-bold mb-1"><i class="bi bi-exclamation-circle-fill me-1"></i> Verifique los siguientes campos:</div>
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
    </div>

    <!-- Main Body Content -->
    <main class="py-3">
        @yield('content')
    </main>

    <footer class="mt-5 py-4 bg-white border-top text-center text-muted small">
        <div class="container">
            <div class="fw-semibold text-dark mb-1">Hotel & Restaurante Rodavento — Sistema de Gestión Integral</div>
            <div>Valle de Bravo, Estado de México</div>
        </div>
    </footer>
</div>

<!-- Bootstrap 5 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
