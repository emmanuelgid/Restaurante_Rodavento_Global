<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#0b1d26">

    <title>Hotel Rodavento | Portal Interno</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <style>
        :root {
            --bg-primary: #0b1d26;
            --accent-gold: #d4af37;
            --accent-gold-hover: #c5a028;
            --white: #ffffff;
            --muted: rgba(255,255,255,.55);
            --border: rgba(255,255,255,.10);
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: 'Inter', sans-serif;
            color: var(--white);
            background:
                radial-gradient(ellipse at 15% 15%, rgba(212,175,55,.09), transparent 35%),
                radial-gradient(ellipse at 85% 85%, rgba(31,75,86,.22), transparent 40%),
                var(--bg-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 35px 20px;
            overflow-x: hidden;
        }

        /* DECORACIÓN DEL FONDO */

        body::before,
        body::after {
            content: "";
            position: fixed;
            border: 1px solid rgba(212,175,55,.10);
            border-radius: 50%;
            pointer-events: none;
        }

        body::before {
            width: 540px;
            height: 540px;
            top: -320px;
            right: -180px;
        }

        body::after {
            width: 420px;
            height: 420px;
            bottom: -290px;
            left: -170px;
        }

        /* PANTALLA DE CARGA */

        #loader-screen {
            position: fixed;
            inset: 0;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            background: var(--bg-primary);
            transition: opacity .7s ease, visibility .7s ease;
        }

        .loader-logo {
            font-family: 'Playfair Display', serif;
            font-size: 2.5rem;
            letter-spacing: 5px;
            font-weight: 600;
            animation: fadeUp .8s ease both;
        }

        .loader-subtitle {
            margin-top: 8px;
            font-size: .68rem;
            letter-spacing: 4px;
            text-transform: uppercase;
            color: var(--muted);
        }

        .loader-decoration {
            width: 55px;
            height: 1px;
            background: var(--accent-gold);
            margin: 32px 0;
        }

        .loader-icon {
            font-size: 2.5rem;
            color: var(--accent-gold);
            height: 55px;
            display: flex;
            align-items: center;
            animation: pulse 1.2s infinite alternate;
        }

        .progress-container {
            width: 230px;
            height: 2px;
            background: rgba(255,255,255,.12);
            margin-top: 25px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            width: 0;
            background: var(--accent-gold);
            animation: progress 2.8s ease forwards;
        }

        .loading-text {
            margin-top: 18px;
            color: var(--muted);
            font-size: .7rem;
            letter-spacing: 1.5px;
            text-transform: uppercase;
        }

        /* CONTENIDO PRINCIPAL */

        #main-content {
            width: 100%;
            max-width: 900px;
            opacity: 0;
            visibility: hidden;
            transform: translateY(18px);
            transition: all .8s ease;
            position: relative;
            z-index: 1;
        }

        #main-content.active {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        /* TARJETA PRINCIPAL */

        .portal-card {
            display: grid;
            grid-template-columns: 1.05fr .95fr;
            min-height: 480px;
            border: 1px solid var(--border);
            border-radius: 12px;
            overflow: hidden;
            background: rgba(255,255,255,.025);
            box-shadow: 0 30px 80px rgba(0,0,0,.38);
            backdrop-filter: blur(15px);
        }

        /* PANEL DE IDENTIDAD */

        .brand-panel {
            padding: 55px 45px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            background:
                linear-gradient(145deg, rgba(255,255,255,.045), transparent 65%);
            border-right: 1px solid var(--border);
        }

        .brand-top {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .brand-symbol {
            width: 43px;
            height: 43px;
            border: 1px solid rgba(212,175,55,.55);
            color: var(--accent-gold);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.35rem;
        }

        .brand-top span {
            font-size: .66rem;
            letter-spacing: 2px;
            color: var(--muted);
            text-transform: uppercase;
        }

        .brand-main {
            margin-top: 55px;
        }

        .brand-overline {
            color: var(--accent-gold);
            font-size: .68rem;
            letter-spacing: 3px;
            text-transform: uppercase;
            margin-bottom: 20px;
        }

        .brand-title {
            font-family: 'Playfair Display', serif;
            font-size: clamp(2.5rem, 5vw, 3.5rem);
            font-weight: 500;
            letter-spacing: 3px;
            line-height: 1.1;
            margin: 0;
        }

        .brand-line {
            width: 65px;
            height: 2px;
            background: var(--accent-gold);
            margin: 25px 0;
        }

        .brand-description {
            max-width: 310px;
            color: var(--muted);
            font-size: .85rem;
            line-height: 1.9;
            font-weight: 300;
        }

        .brand-footer {
            margin-top: 50px;
            font-size: .65rem;
            letter-spacing: 2px;
            color: rgba(255,255,255,.35);
            text-transform: uppercase;
        }

        /* PANEL DE ACCESO */

        .access-panel {
            padding: 55px 42px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .access-label {
            color: var(--accent-gold);
            text-transform: uppercase;
            letter-spacing: 2.5px;
            font-size: .65rem;
            font-weight: 600;
            margin-bottom: 15px;
        }

        .access-title {
            font-family: 'Playfair Display', serif;
            font-size: 2rem;
            font-weight: 500;
            margin-bottom: 12px;
        }

        .access-description {
            font-size: .79rem;
            line-height: 1.8;
            color: var(--muted);
            margin-bottom: 35px;
        }

        /* BOTONES */

        .access-options {
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .btn-access {
            width: 100%;
            min-height: 56px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 19px;
            border-radius: 4px;
            text-decoration: none;
            font-family: 'Inter', sans-serif;
            font-size: .78rem;
            font-weight: 600;
            letter-spacing: .5px;
            transition: all .3s ease;
        }

        .btn-primary-access {
            background: var(--accent-gold);
            color: var(--bg-primary);
            border: 1px solid var(--accent-gold);
        }

        .btn-primary-access:hover {
            background: var(--accent-gold-hover);
            border-color: var(--accent-gold-hover);
            color: var(--bg-primary);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(212,175,55,.15);
        }

        .btn-secondary-access {
            background: transparent;
            color: var(--white);
            border: 1px solid rgba(212,175,55,.38);
        }

        .btn-secondary-access:hover {
            background: rgba(212,175,55,.08);
            color: var(--accent-gold);
            border-color: var(--accent-gold);
            transform: translateY(-2px);
        }

        .btn-content {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .btn-content i {
            font-size: 1.05rem;
        }

        .btn-arrow {
            font-size: .9rem;
            opacity: .7;
            transition: transform .3s ease;
        }

        .btn-access:hover .btn-arrow {
            transform: translateX(4px);
        }

        .access-note {
            display: flex;
            align-items: center;
            gap: 9px;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid var(--border);
            color: rgba(255,255,255,.38);
            font-size: .67rem;
            line-height: 1.6;
        }

        .access-note i {
            color: var(--accent-gold);
            font-size: .9rem;
        }

        /* PIE DE PÁGINA */

        .page-footer {
            text-align: center;
            margin-top: 25px;
            color: rgba(255,255,255,.35);
            font-size: .65rem;
            letter-spacing: 1px;
        }

        .page-footer span {
            color: var(--accent-gold);
        }

        /* ANIMACIONES */

        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(15px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes pulse {
            from {
                opacity: .6;
                transform: scale(.95);
            }
            to {
                opacity: 1;
                transform: scale(1.05);
            }
        }

        @keyframes progress {
            from { width: 0; }
            to { width: 100%; }
        }

        /* RESPONSIVE */

        @media (max-width: 700px) {
            body {
                padding: 25px 15px;
            }

            .portal-card {
                grid-template-columns: 1fr;
            }

            .brand-panel {
                padding: 35px 30px 25px;
                border-right: none;
                border-bottom: 1px solid var(--border);
            }

            .brand-main {
                margin-top: 35px;
            }

            .brand-title {
                font-size: 2.6rem;
            }

            .brand-footer {
                margin-top: 30px;
            }

            .access-panel {
                padding: 35px 30px;
            }

            .access-title {
                font-size: 1.8rem;
            }
        }

        @media (max-width: 380px) {
            .brand-panel,
            .access-panel {
                padding-left: 23px;
                padding-right: 23px;
            }

            .brand-title {
                font-size: 2.2rem;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                animation-duration: .01ms !important;
                transition-duration: .01ms !important;
            }
        }
    </style>
</head>

<body>

<!-- PANTALLA DE CARGA -->

<div id="loader-screen">

    <div class="loader-logo">RODAVENTO</div>
    <div class="loader-subtitle">Hotel · Experiencias · Hospitalidad</div>

    <div class="loader-decoration"></div>

    <div class="loader-icon">
        <i id="loading-icon" class="bi bi-building"></i>
    </div>

    <div class="progress-container">
        <div class="progress-fill"></div>
    </div>

    <div id="loading-status-text" class="loading-text">
        Preparando entorno operativo...
    </div>

</div>


<!-- PORTAL PRINCIPAL -->

<main id="main-content">

    <section class="portal-card">

        <!-- IDENTIDAD DEL HOTEL -->

        <div class="brand-panel">

            <div class="brand-top">
                <div class="brand-symbol">
                    <i class="bi bi-buildings"></i>
                </div>

                <span>Portal Operativo</span>
            </div>

            <div class="brand-main">

                <div class="brand-overline">
                    Bienvenido a
                </div>

                <h1 class="brand-title">
                    HOTEL<br>RODAVENTO
                </h1>

                <div class="brand-line"></div>

                <p class="brand-description">
                    Plataforma de gestión interna destinada
                    al acceso y administración de las
                    operaciones del hotel.
                </p>

            </div>

            <div class="brand-footer">
                Excelencia en cada detalle
            </div>

        </div>


        <!-- ACCESO AL SISTEMA -->

        <div class="access-panel">

            <div class="access-label">
                Sistema de operaciones
            </div>

            <h2 class="access-title">
                Acceso al portal
            </h2>

            <p class="access-description">
                Seleccione una opción para ingresar a la
                plataforma o registrar un nuevo empleado
                en el sistema.
            </p>


            <div class="access-options">

                <!-- INICIAR SESIÓN -->

                <a href="{{ route('login') }}"
                   class="btn-access btn-primary-access">

                        <span class="btn-content">
                            <i class="bi bi-box-arrow-in-right"></i>
                            Iniciar sesión
                        </span>

                    <i class="bi bi-arrow-right btn-arrow"></i>

                </a>


                <!-- REGISTRAR EMPLEADO -->

                <a href="{{ route('register') }}"
                   class="btn-access btn-secondary-access">

                        <span class="btn-content">
                            <i class="bi bi-person-plus"></i>
                            Registrar empleado
                        </span>

                    <i class="bi bi-arrow-right btn-arrow"></i>

                </a>

            </div>


            <div class="access-note">
                <i class="bi bi-shield-lock"></i>

                <span>
                        Acceso exclusivo para personal autorizado
                        de Hotel Rodavento.
                    </span>
            </div>

        </div>

    </section>


    <!-- PIE DE PÁGINA -->

    <footer class="page-footer">
        HOTEL RODAVENTO <span>·</span> SISTEMA INTERNO
    </footer>

</main>


<!-- ANIMACIONES DE CARGA -->

<script>

    const loadingIcons = [
        {
            icon: 'bi-building',
            text: 'Preparando instalaciones...'
        },
        {
            icon: 'bi-cup-hot',
            text: 'Configurando restaurante y bar...'
        },
        {
            icon: 'bi-egg-fried',
            text: 'Preparando áreas gastronómicas...'
        },
        {
            icon: 'bi-moon-stars',
            text: 'Configurando suites de hospedaje...'
        },
        {
            icon: 'bi-compass',
            text: 'Cargando experiencias y servicios...'
        }
    ];

    let currentStep = 0;

    const iconElement = document.getElementById('loading-icon');
    const textElement = document.getElementById('loading-status-text');

    const loader = document.getElementById('loader-screen');
    const mainContent = document.getElementById('main-content');

    const iconInterval = setInterval(() => {

        currentStep = (currentStep + 1) % loadingIcons.length;

        iconElement.className = 'bi ' + loadingIcons[currentStep].icon;

        textElement.textContent = loadingIcons[currentStep].text;

    }, 550);


    window.addEventListener('DOMContentLoaded', () => {

        setTimeout(() => {

            clearInterval(iconInterval);

            loader.style.opacity = '0';
            loader.style.visibility = 'hidden';

            setTimeout(() => {
                mainContent.classList.add('active');
            }, 150);

        }, 2800);

    });

</script>

</body>
</html>

