<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel Rodavento - Sistema Interno</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600;700&family=Inter:wght@300;400;500&display=swap" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <style>
        :root {
            --bg-primary: #0b1d26;
            --accent-gold: #d4af37;
            --accent-gold-hover: #c5a028;
        }

        body {
            background-color: var(--bg-primary);
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            overflow: hidden;
            color: #fff;
        }

        /* --- PANTALLA DE CARGA CON ICONOS ROTATIVOS DE HOTEL --- */
        #loader-screen {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: var(--bg-primary);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            transition: opacity 0.8s ease, visibility 0.8s ease;
        }

        .loader-brand {
            font-family: 'Playfair Display', serif;
            font-size: 2.5rem;
            letter-spacing: 4px;
            font-weight: 600;
            margin-bottom: 0.25rem;
            opacity: 0;
            animation: fadeInDown 1s ease forwards 0.2s;
        }

        .loader-subtitle {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 4px;
            color: rgba(255, 255, 255, 0.5);
            margin-bottom: 2.5rem;
            opacity: 0;
            animation: fadeInUp 1s ease forwards 0.4s;
        }

        .loader-icon-container {
            font-size: 2.8rem;
            color: var(--accent-gold);
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.5rem;
            animation: pulseIcon 1.2s infinite alternate;
        }

        .progress-bar-container {
            width: 260px;
            height: 2px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 2px;
            overflow: hidden;
            position: relative;
        }

        .progress-bar-fill {
            position: absolute;
            top: 0;
            left: 0;
            height: 100%;
            width: 0%;
            background-color: var(--accent-gold);
            animation: loadProgress 3.2s cubic-bezier(0.65, 0, 0.35, 1) forwards 0.3s;
        }

        .loading-text {
            font-size: 0.8rem;
            letter-spacing: 2px;
            color: rgba(255, 255, 255, 0.4);
            margin-top: 1rem;
            text-transform: uppercase;
            opacity: 0;
            animation: fadeIn 1s ease forwards 0.5s;
        }

        /* --- CONTENIDO PRINCIPAL --- */
        #main-content {
            opacity: 0;
            visibility: hidden;
            transform: translateY(20px);
            transition: all 1s cubic-bezier(0.16, 1, 0.3, 1);
            width: 100%;
            max-width: 440px;
            padding: 1.5rem;
        }

        #main-content.active {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .auth-card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 12px;
            backdrop-filter: blur(15px);
            padding: 2.5rem;
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.7);
            text-align: center;
        }

        .auth-brand h2 {
            font-family: 'Playfair Display', serif;
            font-weight: 600;
            letter-spacing: 2px;
            color: #fff;
            margin-bottom: 0.25rem;
            font-size: 1.8rem;
        }

        .auth-brand p {
            color: rgba(255, 255, 255, 0.45);
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 2.5px;
            margin-bottom: 2rem;
        }

        .btn-gold {
            background-color: var(--accent-gold);
            color: var(--bg-primary);
            font-weight: 600;
            border-radius: 6px;
            padding: 0.75rem;
            width: 100%;
            border: none;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
        }

        .btn-gold:hover {
            background-color: var(--accent-gold-hover);
            color: var(--bg-primary);
            transform: translateY(-1px);
        }

        .btn-outline-gold {
            background: transparent;
            color: var(--accent-gold);
            border: 1px solid rgba(212, 175, 55, 0.4);
            font-weight: 500;
            border-radius: 6px;
            padding: 0.75rem;
            width: 100%;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
        }

        .btn-outline-gold:hover {
            background: rgba(212, 175, 55, 0.08);
            border-color: var(--accent-gold);
            color: #fff;
            transform: translateY(-1px);
        }

        @keyframes fadeInDown {
            from { opacity: 0; transform: translateY(-15px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(15px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        @keyframes loadProgress {
            0% { width: 0%; }
            50% { width: 75%; }
            100% { width: 100%; }
        }
        @keyframes pulseIcon {
            0% { transform: scale(0.95); opacity: 0.7; }
            100% { transform: scale(1.05); opacity: 1; }
        }
    </style>
</head>
<body>

<!-- PANTALLA DE CARGA CON ICONOS ROTATIVOS DE HOTEL Y GASTRONOMÍA -->
<div id="loader-screen">
    <div class="loader-brand">HOTEL RODAVENTO</div>
    <div class="loader-subtitle">Preparando entorno operativo</div>

    <div class="loader-icon-container">
        <i id="loading-icon" class="bi bi-shop"></i>
    </div>

    <div class="progress-bar-container">
        <div class="progress-bar-fill"></div>
    </div>
    <div id="loading-status-text" class="loading-text">Cargando instalaciones...</div>
</div>

<!-- CONTENIDO PRINCIPAL CON LOS BOTONES DE ENLACE -->
<div id="main-content">
    <div class="auth-card">
        <div class="auth-brand">
            <h2>RODAVENTO</h2>
            <p>Portal de Gestión Interna</p>
        </div>
        <p class="text-white-50 small mb-4">Seleccione una opción para continuar con el acceso al sistema de operaciones.</p>

        <div class="d-grid gap-3">
            <!-- Enlace directo a login.blade.php -->
            <a href="{{ route('login') }}" class="btn btn-gold">
                <i class="bi bi-box-arrow-in-right me-2"></i> Iniciar Sesión
            </a>

            <!-- Enlace directo a register.blade.php -->
            <a href="{{ route('register') }}" class="btn btn-outline-gold">
                <i class="bi bi-person-plus me-2"></i> Registrar Empleado
            </a>
        </div>
    </div>
</div>

<!-- Script de Animaciones y Transiciones de Carga -->
<script>
    const loadingIcons = [
        { icon: 'bi-shop', text: 'Cargando instalaciones...' },
        { icon: 'bi-cup-hot-fill', text: 'Sincronizando restaurante & bar...' },
        { icon: 'bi-egg-fried', text: 'Preparando áreas gastronómicas...' },
        { icon: 'bi-moon-stars-fill', text: 'Configurando suites de hospedaje...' },
        { icon: 'bi-airplane-fill', text: 'Cargando experiencias y tours...' }
    ];

    let currentStep = 0;
    const iconElement = document.getElementById('loading-icon');
    const textElement = document.getElementById('loading-status-text');

    const iconInterval = setInterval(() => {
        currentStep = (currentStep + 1) % loadingIcons.length;
        iconElement.className = `bi ${loadingIcons[currentStep].icon}`;
        textElement.textContent = loadingIcons[currentStep].text;
    }, 650);

    window.addEventListener('DOMContentLoaded', () => {
        setTimeout(() => {
            clearInterval(iconInterval);
            const loader = document.getElementById('loader-screen');
            const mainContent = document.getElementById('main-content');

            loader.style.opacity = '0';
            loader.style.visibility = 'hidden';

            setTimeout(() => {
                mainContent.classList.add('active');
            }, 200);
        }, 3200);
    });
</script>
</body>
</html>
