<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#071318">
    <meta name="description" content="Hotel & Restaurante Rodavento en Valle de Bravo. Experiencia de lujo inmersa en el bosque, suites sobre el lago y alta cocina de autor con trucha de manantial y brasas.">

    <title>Hotel & Restaurante Rodavento | Valle de Bravo - Alta Gastronomía & Hospedaje</title>

    <!-- Bootstrap 5.3 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;0,700;1,400;1,600&family=Inter:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,500;0,600;0,700;1,400;1,600&display=swap" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root {
            --bg-dark: #071318;
            --bg-surface: #0c1c23;
            --bg-card: rgba(14, 32, 41, 0.78);
            --bg-card-hover: rgba(19, 44, 56, 0.92);
            --accent-gold: #d4af37;
            --accent-gold-light: #f5e4a8;
            --accent-gold-hover: #c49f2c;
            --forest-teal: #1a424c;
            --forest-teal-light: #286372;
            --text-light: #f8fafc;
            --text-muted: rgba(248, 250, 252, 0.65);
            --border-subtle: rgba(255, 255, 255, 0.08);
            --border-gold: rgba(212, 175, 55, 0.28);
            --border-gold-strong: rgba(212, 175, 55, 0.65);
        }

        * {
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            margin: 0;
            background-color: var(--bg-dark);
            color: var(--text-light);
            font-family: 'Inter', sans-serif;
            overflow-x: hidden;
            line-height: 1.6;
        }

        h1, h2, h3, h4, .font-serif {
            font-family: 'Playfair Display', serif;
        }

        .font-cormorant {
            font-family: 'Cormorant Garamond', serif;
        }

        /* Top Announcement Bar */
        .top-announcement {
            background: linear-gradient(90deg, #09171e 0%, #15323c 50%, #09171e 100%);
            border-bottom: 1px solid var(--border-gold);
            font-size: 0.78rem;
            letter-spacing: 1px;
            color: var(--accent-gold-light);
            padding: 8px 16px;
        }

        /* Navbar */
        .luxury-navbar {
            background: rgba(7, 19, 24, 0.90);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-bottom: 1px solid var(--border-subtle);
            transition: all 0.35s ease;
            padding: 14px 0;
            z-index: 1040;
        }

        .luxury-navbar.scrolled {
            background: rgba(7, 19, 24, 0.98);
            padding: 10px 0;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
            border-bottom-color: var(--border-gold);
        }

        .brand-logo-container {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            color: var(--text-light);
        }

        .brand-emblem {
            width: 44px;
            height: 44px;
            border: 1px solid var(--accent-gold);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--accent-gold);
            font-size: 1.35rem;
            border-radius: 6px;
            background: radial-gradient(circle, rgba(212, 175, 55, 0.15) 0%, transparent 70%);
            transition: all 0.3s ease;
        }

        .brand-logo-container:hover .brand-emblem {
            background-color: var(--accent-gold);
            color: var(--bg-dark);
            transform: scale(1.05);
            box-shadow: 0 0 18px rgba(212, 175, 55, 0.45);
        }

        .brand-text h1 {
            font-size: 1.25rem;
            margin: 0;
            letter-spacing: 3.5px;
            font-weight: 600;
            line-height: 1.1;
            color: #ffffff;
        }

        .brand-text span {
            font-size: 0.65rem;
            letter-spacing: 2.2px;
            text-transform: uppercase;
            color: var(--accent-gold);
            display: block;
        }

        .nav-link-custom {
            color: rgba(255, 255, 255, 0.8) !important;
            font-size: 0.84rem;
            font-weight: 500;
            letter-spacing: 1px;
            text-transform: uppercase;
            padding: 8px 14px !important;
            position: relative;
            transition: color 0.3s ease;
        }

        .nav-link-custom:hover {
            color: var(--accent-gold-light) !important;
        }

        .nav-link-custom::after {
            content: '';
            position: absolute;
            bottom: 2px;
            left: 50%;
            width: 0%;
            height: 2px;
            background: var(--accent-gold);
            transition: all 0.3s ease;
            transform: translateX(-50%);
        }

        .nav-link-custom:hover::after {
            width: 60%;
        }

        /* Botones de Acceso en el Navbar ÚNICAMENTE */
        .btn-auth-login {
            color: #ffffff;
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.20);
            padding: 8px 18px;
            font-size: 0.82rem;
            font-weight: 600;
            letter-spacing: 0.8px;
            border-radius: 4px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 7px;
            transition: all 0.3s ease;
        }

        .btn-auth-login:hover {
            color: var(--accent-gold-light);
            border-color: var(--accent-gold);
            background: rgba(212, 175, 55, 0.12);
            transform: translateY(-2px);
        }

        .btn-auth-register {
            background: linear-gradient(135deg, #d4af37 0%, #e6c55d 100%);
            color: #071318;
            border: 1px solid #d4af37;
            padding: 8px 20px;
            font-size: 0.82rem;
            font-weight: 700;
            letter-spacing: 0.8px;
            border-radius: 4px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 7px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(212, 175, 55, 0.25);
        }

        .btn-auth-register:hover {
            background: linear-gradient(135deg, #e6c55d 0%, #f3d77d 100%);
            color: #050e12;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(212, 175, 55, 0.4);
        }

        .btn-auth-panel {
            background: rgba(26, 66, 76, 0.8);
            border: 1px solid rgba(212, 175, 55, 0.4);
            color: #ffffff;
            padding: 8px 18px;
            font-size: 0.82rem;
            font-weight: 600;
            border-radius: 4px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 7px;
            transition: all 0.3s ease;
        }

        .btn-auth-panel:hover {
            background: var(--forest-teal);
            color: var(--accent-gold);
            border-color: var(--accent-gold);
            transform: translateY(-2px);
        }

        /* Hero Section */
        .hero-section {
            position: relative;
            min-height: 92vh;
            display: flex;
            align-items: center;
            padding: 130px 0 80px;
            background:
                linear-gradient(to bottom, rgba(7, 19, 24, 0.65) 0%, rgba(7, 19, 24, 0.88) 75%, var(--bg-dark) 100%),
                url("{{ asset('images/Hotel.jpeg') }}") center center / cover no-repeat;
            overflow: hidden;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(212, 175, 55, 0.12);
            border: 1px solid var(--border-gold);
            color: var(--accent-gold-light);
            padding: 6px 16px;
            border-radius: 30px;
            font-size: 0.72rem;
            letter-spacing: 2.5px;
            text-transform: uppercase;
            font-weight: 600;
            margin-bottom: 24px;
            animation: fadeInDown 0.9s ease both;
        }

        .hero-title {
            font-size: clamp(2.4rem, 5.5vw, 4.3rem);
            font-weight: 600;
            line-height: 1.15;
            letter-spacing: 1.5px;
            margin-bottom: 24px;
            text-shadow: 0 4px 25px rgba(0, 0, 0, 0.6);
        }

        .hero-title span {
            color: var(--accent-gold);
            font-style: italic;
            font-family: 'Cormorant Garamond', serif;
        }

        .hero-lead {
            font-size: clamp(1.02rem, 1.35vw, 1.25rem);
            color: rgba(255, 255, 255, 0.82);
            max-width: 680px;
            font-weight: 300;
            line-height: 1.8;
            margin-bottom: 38px;
        }

        .hero-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 16px;
            margin-bottom: 45px;
        }

        .btn-hero-primary {
            background: linear-gradient(135deg, #d4af37 0%, #e2be4c 100%);
            color: #071318;
            padding: 14px 28px;
            font-weight: 700;
            font-size: 0.9rem;
            letter-spacing: 1px;
            text-transform: uppercase;
            border-radius: 4px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s ease;
            box-shadow: 0 8px 25px rgba(212, 175, 55, 0.35);
        }

        .btn-hero-primary:hover {
            background: linear-gradient(135deg, #e2be4c 0%, #f1d16d 100%);
            color: #040c10;
            transform: translateY(-3px);
            box-shadow: 0 12px 30px rgba(212, 175, 55, 0.5);
        }

        .btn-hero-call {
            background: rgba(26, 66, 76, 0.85);
            border: 1px solid var(--accent-gold);
            color: var(--accent-gold-light);
            padding: 14px 28px;
            font-weight: 600;
            font-size: 0.9rem;
            letter-spacing: 1px;
            text-transform: uppercase;
            border-radius: 4px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .btn-hero-call:hover {
            background: var(--forest-teal);
            color: #ffffff;
            border-color: #ffffff;
            transform: translateY(-3px);
            box-shadow: 0 8px 22px rgba(26, 66, 76, 0.5);
        }

        .btn-hero-outline {
            border: 1px solid var(--border-subtle);
            background: rgba(255, 255, 255, 0.05);
            color: #ffffff;
            padding: 14px 24px;
            font-weight: 600;
            font-size: 0.88rem;
            letter-spacing: 0.8px;
            border-radius: 4px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 9px;
            transition: all 0.3s ease;
        }

        .btn-hero-outline:hover {
            background: rgba(212, 175, 55, 0.12);
            color: var(--accent-gold-light);
            border-color: var(--accent-gold);
            transform: translateY(-3px);
        }

        /* Stats Floating Bar */
        .hero-stats-bar {
            background: rgba(14, 32, 41, 0.8);
            border: 1px solid var(--border-gold);
            border-radius: 8px;
            padding: 22px 30px;
            backdrop-filter: blur(16px);
            box-shadow: 0 20px 45px rgba(0, 0, 0, 0.4);
        }

        .stat-item {
            text-align: center;
            padding: 0 16px;
        }

        .stat-number {
            font-family: 'Playfair Display', serif;
            font-size: 2.1rem;
            font-weight: 700;
            color: var(--accent-gold);
            line-height: 1;
            margin-bottom: 6px;
        }

        .stat-label {
            font-size: 0.72rem;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: var(--text-muted);
            margin: 0;
        }

        /* Section Global Headers */
        .section-eyebrow {
            color: var(--accent-gold);
            font-size: 0.75rem;
            letter-spacing: 3.5px;
            text-transform: uppercase;
            font-weight: 600;
            margin-bottom: 12px;
            display: block;
        }

        .section-title {
            font-size: clamp(2rem, 3.8vw, 3rem);
            font-weight: 600;
            letter-spacing: 1px;
            margin-bottom: 18px;
            line-height: 1.2;
        }

        .section-desc {
            color: var(--text-muted);
            font-size: 1rem;
            max-width: 680px;
            margin: 0 auto 40px;
            line-height: 1.8;
            font-weight: 300;
        }

        .gold-divider {
            width: 70px;
            height: 2px;
            background: var(--accent-gold);
            margin: 0 auto 30px;
        }

        /* Hotel Experience Section */
        .hotel-feature-card {
            background: var(--bg-card);
            border: 1px solid var(--border-subtle);
            border-radius: 10px;
            overflow: hidden;
            transition: all 0.4s ease;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .hotel-feature-card:hover {
            transform: translateY(-8px);
            border-color: var(--border-gold);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.5);
            background: var(--bg-card-hover);
        }

        .hotel-card-img-wrapper {
            position: relative;
            height: 240px;
            overflow: hidden;
        }

        .hotel-card-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.6s ease;
        }

        .hotel-feature-card:hover .hotel-card-img {
            transform: scale(1.08);
        }

        .hotel-badge-overlay {
            position: absolute;
            top: 14px;
            right: 14px;
            background: rgba(7, 19, 24, 0.85);
            border: 1px solid var(--border-gold);
            color: var(--accent-gold-light);
            font-size: 0.68rem;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            padding: 4px 12px;
            border-radius: 20px;
            backdrop-filter: blur(8px);
        }

        .hotel-card-body {
            padding: 26px;
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .hotel-card-body h3 {
            font-size: 1.35rem;
            margin-bottom: 12px;
            color: #ffffff;
        }

        .hotel-card-body p {
            color: var(--text-muted);
            font-size: 0.88rem;
            line-height: 1.7;
            margin-bottom: 20px;
        }

        .hotel-card-feature-list {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .hotel-card-feature-list li {
            font-size: 0.8rem;
            color: rgba(255, 255, 255, 0.85);
            display: flex;
            align-items: center;
            gap: 9px;
        }

        .hotel-card-feature-list li i {
            color: var(--accent-gold);
            font-size: 0.9rem;
        }

        /* Gastronomy Pillars */
        .pillar-box {
            background: rgba(255, 255, 255, 0.025);
            border: 1px solid var(--border-subtle);
            border-radius: 8px;
            padding: 32px 24px;
            text-align: center;
            height: 100%;
            transition: all 0.35s ease;
        }

        .pillar-box:hover {
            background: rgba(212, 175, 55, 0.05);
            border-color: var(--border-gold);
            transform: translateY(-6px);
        }

        .pillar-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: rgba(212, 175, 55, 0.12);
            color: var(--accent-gold);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.6rem;
            margin-bottom: 20px;
            border: 1px solid var(--border-gold);
        }

        .pillar-title {
            font-size: 1.15rem;
            font-weight: 600;
            margin-bottom: 12px;
            color: #ffffff;
        }

        .pillar-desc {
            font-size: 0.84rem;
            color: var(--text-muted);
            line-height: 1.65;
            margin: 0;
        }

        /* Menu Section & Dish Cards */
        .menu-filter-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 10px;
            margin-bottom: 35px;
        }

        .menu-filter-btn {
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid var(--border-subtle);
            color: var(--text-light);
            padding: 8px 20px;
            font-size: 0.82rem;
            letter-spacing: 1px;
            text-transform: uppercase;
            font-weight: 500;
            border-radius: 30px;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .menu-filter-btn:hover,
        .menu-filter-btn.active {
            background: var(--accent-gold);
            color: #071318;
            border-color: var(--accent-gold);
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(212, 175, 55, 0.3);
        }

        .search-box-wrapper {
            max-width: 480px;
            margin: 0 auto 40px;
            position: relative;
        }

        .search-dish-input {
            width: 100%;
            background: rgba(14, 32, 41, 0.85);
            border: 1px solid var(--border-gold);
            border-radius: 30px;
            padding: 12px 20px 12px 46px;
            color: #ffffff;
            font-size: 0.88rem;
            outline: none;
            transition: all 0.3s ease;
        }

        .search-dish-input:focus {
            box-shadow: 0 0 20px rgba(212, 175, 55, 0.3);
            border-color: var(--accent-gold);
        }

        .search-dish-icon {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--accent-gold);
            font-size: 1.1rem;
        }

        /* Platillo Card */
        .dish-card {
            background: var(--bg-card);
            border: 1px solid var(--border-subtle);
            border-radius: 10px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            height: 100%;
            transition: all 0.4s ease;
            position: relative;
        }

        .dish-card:hover {
            transform: translateY(-8px);
            border-color: var(--border-gold);
            box-shadow: 0 20px 45px rgba(0, 0, 0, 0.55);
            background: var(--bg-card-hover);
        }

        .dish-card-img-wrapper {
            position: relative;
            height: 220px;
            overflow: hidden;
            background-color: #09171e;
        }

        .dish-card-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.6s ease;
        }

        .dish-card:hover .dish-card-img {
            transform: scale(1.08);
        }

        .dish-price-tag {
            position: absolute;
            bottom: 12px;
            right: 12px;
            background: rgba(7, 19, 24, 0.9);
            border: 1px solid var(--accent-gold);
            color: var(--accent-gold-light);
            font-family: 'Playfair Display', serif;
            font-size: 1.05rem;
            font-weight: 700;
            padding: 4px 12px;
            border-radius: 4px;
            backdrop-filter: blur(8px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.5);
        }

        .dish-cat-badge {
            position: absolute;
            top: 12px;
            left: 12px;
            background: rgba(26, 66, 76, 0.88);
            border: 1px solid rgba(255, 255, 255, 0.15);
            color: #ffffff;
            font-size: 0.68rem;
            letter-spacing: 1px;
            text-transform: uppercase;
            padding: 3px 10px;
            border-radius: 20px;
            backdrop-filter: blur(6px);
        }

        .dish-card-body {
            padding: 22px;
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .dish-title {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 10px;
            color: #ffffff;
            line-height: 1.35;
        }

        .dish-desc {
            color: var(--text-muted);
            font-size: 0.84rem;
            line-height: 1.65;
            margin-bottom: 20px;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .dish-card-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-top: 15px;
            border-top: 1px solid rgba(255, 255, 255, 0.07);
        }

        .dish-btn-detail {
            color: var(--accent-gold);
            background: transparent;
            border: 1px solid var(--border-gold);
            font-size: 0.78rem;
            font-weight: 600;
            letter-spacing: 0.8px;
            padding: 6px 14px;
            border-radius: 4px;
            transition: all 0.3s ease;
            text-transform: uppercase;
        }

        .dish-btn-detail:hover {
            background: var(--accent-gold);
            color: #071318;
            border-color: var(--accent-gold);
        }

        /* Banner Reservación Telefónica Exclusiva */
        .call-reservation-banner {
            background:
                linear-gradient(135deg, rgba(7, 19, 24, 0.94) 0%, rgba(14, 32, 41, 0.92) 100%),
                url("{{ asset('images/Bosque.jpeg') }}") center center / cover no-repeat;
            border-top: 1px solid var(--border-gold);
            border-bottom: 1px solid var(--border-gold);
            padding: 80px 0;
            position: relative;
        }

        .call-reservation-box {
            background: rgba(14, 32, 41, 0.85);
            border: 1px solid var(--border-gold-strong);
            border-radius: 12px;
            padding: 45px 35px;
            backdrop-filter: blur(20px);
            box-shadow: 0 30px 70px rgba(0, 0, 0, 0.55);
        }

        /* Sucursales Section */
        .sucursal-card {
            background: var(--bg-card);
            border: 1px solid var(--border-subtle);
            border-radius: 10px;
            padding: 30px 26px;
            height: 100%;
            transition: all 0.35s ease;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .sucursal-card:hover {
            border-color: var(--border-gold);
            background: var(--bg-card-hover);
            transform: translateY(-6px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.45);
        }

        .sucursal-name {
            font-size: 1.3rem;
            color: #ffffff;
            margin-bottom: 15px;
        }

        .sucursal-info-item {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            margin-bottom: 14px;
            font-size: 0.85rem;
            color: var(--text-muted);
        }

        .sucursal-info-item i {
            color: var(--accent-gold);
            font-size: 1rem;
            margin-top: 2px;
        }

        /* Modal Styles */
        .modal-content-luxury {
            background: #0c1c23;
            border: 1px solid var(--border-gold);
            border-radius: 12px;
            color: #ffffff;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.7);
        }

        .modal-header-luxury {
            border-bottom: 1px solid var(--border-subtle);
            padding: 20px 26px;
        }

        .modal-footer-luxury {
            border-top: 1px solid var(--border-subtle);
            padding: 16px 26px;
        }

        .phone-call-card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid var(--border-subtle);
            border-radius: 8px;
            padding: 18px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: all 0.3s ease;
            margin-bottom: 14px;
            text-decoration: none;
            color: #ffffff;
        }

        .phone-call-card:hover {
            background: rgba(212, 175, 55, 0.1);
            border-color: var(--accent-gold);
            color: var(--accent-gold-light);
            transform: translateX(4px);
        }

        /* Testimonials */
        .testimonial-card {
            background: rgba(255, 255, 255, 0.025);
            border: 1px solid var(--border-subtle);
            border-radius: 8px;
            padding: 30px 24px;
            height: 100%;
            transition: all 0.3s ease;
        }

        .testimonial-card:hover {
            border-color: var(--border-gold);
            background: rgba(212, 175, 55, 0.04);
        }

        .testimonial-stars {
            color: var(--accent-gold);
            font-size: 0.95rem;
            margin-bottom: 16px;
        }

        .testimonial-text {
            font-style: italic;
            font-size: 0.92rem;
            color: rgba(255, 255, 255, 0.85);
            line-height: 1.7;
            margin-bottom: 20px;
        }

        .testimonial-author {
            font-weight: 600;
            font-size: 0.85rem;
            color: #ffffff;
            margin: 0;
        }

        .testimonial-role {
            font-size: 0.72rem;
            color: var(--accent-gold);
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        /* Footer */
        .luxury-footer {
            background: #040d11;
            border-top: 1px solid var(--border-subtle);
            padding: 70px 0 30px;
            color: var(--text-muted);
            font-size: 0.85rem;
        }

        .footer-logo-title {
            font-family: 'Playfair Display', serif;
            font-size: 1.4rem;
            letter-spacing: 3px;
            color: #ffffff;
            margin-bottom: 14px;
        }

        .footer-link {
            color: var(--text-muted);
            text-decoration: none;
            display: block;
            margin-bottom: 10px;
            transition: color 0.3s ease;
        }

        .footer-link:hover {
            color: var(--accent-gold);
            transform: translateX(4px);
        }

        .footer-social-icon {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            border: 1px solid var(--border-subtle);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: var(--text-light);
            text-decoration: none;
            transition: all 0.3s ease;
            margin-right: 8px;
        }

        .footer-social-icon:hover {
            border-color: var(--accent-gold);
            color: var(--accent-gold);
            background: rgba(212, 175, 55, 0.1);
        }

        /* Animations */
        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 991px) {
            .hero-stats-bar {
                margin-top: 30px;
            }
            .nav-actions-mobile {
                margin-top: 15px;
                padding-top: 15px;
                border-top: 1px solid var(--border-subtle);
                display: flex;
                flex-direction: column;
                gap: 10px;
            }
        }
    </style>
</head>
<body>

    <!-- TOP ANNOUNCEMENT BAR (ENFASIS EN RESERVAS TELEFÓNICAS) -->
    <div class="top-announcement d-none d-md-block text-center">
        <div class="container d-flex justify-content-between align-items-center">
            <span><i class="bi bi-geo-alt-fill text-warning me-1"></i> Valle de Bravo, Edo. Méx. · Hotel Boutique & Restaurante de Alta Cocina</span>
            <span><i class="bi bi-telephone-fill text-warning me-1"></i> Reservaciones exclusivas por llamada: <a href="tel:7262628900" class="text-warning text-decoration-none fw-bold">(726) 262-8900</a></span>
            <span><i class="bi bi-clock-fill text-warning me-1"></i> Horario telefónico: 8:00 AM - 10:00 PM</span>
        </div>
    </div>

    <!-- MAIN NAVBAR: AQUÍ ESTÁN LIMPIAMENTE LOS BOTONES DE INICIAR SESIÓN Y REGISTRARTE -->
    <nav id="navbar" class="navbar navbar-expand-lg luxury-navbar sticky-top">
        <div class="container">
            <!-- Brand -->
            <a class="brand-logo-container" href="{{ url('/') }}">
                <div class="brand-emblem">
                    <i class="bi bi-tree-fill"></i>
                </div>
                <div class="brand-text">
                    <h1>RODAVENTO</h1>
                    <span>Hotel & Restaurante · Valle de Bravo</span>
                </div>
            </a>

            <!-- Mobile Hamburger -->
            <button class="navbar-toggler border-0 text-white" type="button" data-bs-toggle="collapse" data-bs-target="#navContent" aria-controls="navContent" aria-expanded="false" aria-label="Toggle navigation">
                <i class="bi bi-list fs-2 text-warning"></i>
            </button>

            <!-- Menu items -->
            <div class="collapse navbar-collapse" id="navContent">
                <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link nav-link-custom" href="#inicio">Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-link-custom" href="#hotel">El Hotel</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-link-custom" href="#gastronomia">Gastronomía</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-link-custom" href="#menu">Platillos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-link-custom" href="#experiencias">Experiencias</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-link-custom" href="#sucursales">Ubicaciones</a>
                    </li>
                </ul>

                <!-- Acceso & Registro: LOS DOS BOTONES EN SU LUGAR NATURAL Y LIMPIO -->
                <div class="d-flex align-items-center gap-2 nav-actions-mobile">
                    @auth
                        <a href="{{ route('home') }}" class="btn-auth-panel" title="Ingresar a la plataforma de administración">
                            <i class="bi bi-speedometer2"></i>
                            <span>Mi Panel</span>
                        </a>
                        <form action="{{ route('logout') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn-auth-login" title="Cerrar sesión">
                                <i class="bi bi-box-arrow-right"></i>
                            </button>
                        </form>
                    @else
                        <!-- 1. BOTÓN INICIAR SESIÓN -->
                        <a href="{{ route('login') }}" class="btn-auth-login" title="Acceso de empleados y clientes registrados">
                            <i class="bi bi-box-arrow-in-right"></i>
                            <span>Iniciar sesión</span>
                        </a>

                        <!-- 2. BOTÓN REGISTRARTE -->
                        <a href="{{ route('register') }}" class="btn-auth-register" title="Crear una cuenta nueva o registrarse">
                            <i class="bi bi-person-plus-fill"></i>
                            <span>Registrarte</span>
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- HERO SECTION: ENFOCADO 100% EN EL HOTEL, GASTRONOMÍA Y RESERVAS POR LLAMADA -->
    <header id="inicio" class="hero-section">
        <div class="container position-relative" style="z-index: 2;">
            <div class="row align-items-center min-vh-75">
                <div class="col-lg-8">
                    <div class="hero-badge">
                        <i class="bi bi-stars"></i>
                        <span>Experiencia Gastronómica & Hospedaje Exclusivo</span>
                    </div>

                    <h1 class="hero-title">
                        Donde el bosque de Valle de Bravo despierta <span>su paladar</span>
                    </h1>

                    <p class="hero-lead">
                        Envuélvase en la tranquilidad de un bosque de pinos centenarios y nuestro lago privado.
                        Descubra una experiencia de hospitalidad inigualable junto a una propuesta culinaria que exalta
                        la trucha de manantial fresca, las brasas de encino y los tesoros de nuestro huerto orgánico.
                    </p>

                    <!-- Acciones principales del Hero: Platillos, Llamada y Hotel -->
                    <div class="hero-actions">
                        <!-- Botón 1: Explorar Platillos -->
                        <a href="#menu" class="btn-hero-primary">
                            <i class="bi bi-journal-text fs-5"></i>
                            <span>Ver Platillos & Carta</span>
                        </a>

                        <!-- Botón 2: Reservar por Llamada -->
                        <button type="button" class="btn-hero-call" data-bs-toggle="modal" data-bs-target="#callReservationModal">
                            <i class="bi bi-telephone-fill fs-5 text-warning"></i>
                            <span>Reservar por Llamada</span>
                        </button>

                        <!-- Botón 3: Conocer el Hotel -->
                        <a href="#hotel" class="btn-hero-outline">
                            <i class="bi bi-tree-fill fs-5 text-success"></i>
                            <span>Conocer el Hotel</span>
                        </a>
                    </div>
                </div>

                <div class="col-lg-4">
                    <!-- Stats Floating Card -->
                    <div class="hero-stats-bar">
                        <div class="row g-3">
                            <div class="col-6 stat-item border-end border-secondary">
                                <div class="stat-number">4.9 <span class="fs-5">★</span></div>
                                <p class="stat-label">Calificación de Huéspedes</p>
                            </div>
                            <div class="col-6 stat-item">
                                <div class="stat-number">100%</div>
                                <p class="stat-label">Pesca & Huerto Local</p>
                            </div>
                            <div class="col-12 my-2 border-top border-secondary"></div>
                            <div class="col-6 stat-item border-end border-secondary">
                                <div class="stat-number">3</div>
                                <p class="stat-label">Espacios Únicos</p>
                            </div>
                            <div class="col-6 stat-item">
                                <div class="stat-number">25+</div>
                                <p class="stat-label">Años de Tradición</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- SECTION: EL HOTEL RODAVENTO -->
    <section id="hotel" class="py-5" style="background-color: #071318;">
        <div class="container py-5">
            <div class="text-center">
                <span class="section-eyebrow">HOSPEDAJE DE CLASE MUNDIAL</span>
                <h2 class="section-title">El Refugio Natural de Valle de Bravo</h2>
                <div class="gold-divider"></div>
                <p class="section-desc">
                    Diseñado en armonía total con la geografía de la montaña, Hotel Rodavento ofrece suites de lujo suspendidas
                    entre copas de árboles y frente a un lago privado donde el confort se fusiona con la libertad silvestre.
                </p>
            </div>

            <div class="row g-4 mt-2">
                <!-- Tarjeta 1: Suites -->
                <div class="col-lg-4 col-md-6">
                    <div class="hotel-feature-card">
                        <div class="hotel-card-img-wrapper">
                            <img src="{{ asset('images/Hotel.jpeg') }}" alt="Suites sobre el Lago Rodavento" class="hotel-card-img">
                            <div class="hotel-badge-overlay">Confort Sustentable</div>
                        </div>
                        <div class="hotel-card-body">
                            <div>
                                <h3>Suites sobre el Bosque y Lago</h3>
                                <p>Arquitectura orgánica con terrazas panorámicas privadas, chimenea de leña tradicional y bañeras exentas con vista a los pinos.</p>
                            </div>
                            <ul class="hotel-card-feature-list">
                                <li><i class="bi bi-check-circle-fill"></i> Camas King Size con plumón hipoalergénico</li>
                                <li><i class="bi bi-check-circle-fill"></i> Chimenea con servicio de leña aromática</li>
                                <li><i class="bi bi-check-circle-fill"></i> Amenidades botánicas artesanales</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Tarjeta 2: Spa -->
                <div class="col-lg-4 col-md-6">
                    <div class="hotel-feature-card">
                        <div class="hotel-card-img-wrapper">
                            <img src="{{ asset('images/Bosque.jpeg') }}" alt="Spa Rodavento entre Cascada y Árboles" class="hotel-card-img">
                            <div class="hotel-badge-overlay">Bienestar Holístico</div>
                        </div>
                        <div class="hotel-card-body">
                            <div>
                                <h3>Spa en la Intimidad del Bosque</h3>
                                <p>Un santuario de hidroterapia con albercas templadas al aire libre, rituales de temazcal y masajes con hierbas medicinales de la región.</p>
                            </div>
                            <ul class="hotel-card-feature-list">
                                <li><i class="bi bi-check-circle-fill"></i> Circuito de aguas y jacuzzis en la naturaleza</li>
                                <li><i class="bi bi-check-circle-fill"></i> Terapias con aceites esenciales de pino y lavanda</li>
                                <li><i class="bi bi-check-circle-fill"></i> Cabinas privadas con sonido del agua</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Tarjeta 3: Aventura -->
                <div class="col-lg-4 col-md-12">
                    <div class="hotel-feature-card">
                        <div class="hotel-card-img-wrapper">
                            <img src="https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&w=800&q=80" alt="Aventura y Actividades en Rodavento" class="hotel-card-img">
                            <div class="hotel-badge-overlay">Experiencias Vivas</div>
                        </div>
                        <div class="hotel-card-body">
                            <div>
                                <h3>Aventura & Desconexión Plena</h3>
                                <p>Navegue en kayak o paddleboard por el lago privado, recorra tirolesas suspendidas en las copas de los árboles o disfrute de senderismo guiado.</p>
                            </div>
                            <ul class="hotel-card-feature-list">
                                <li><i class="bi bi-check-circle-fill"></i> Kayak y paddle en aguas tranquilas privadas</li>
                                <li><i class="bi bi-check-circle-fill"></i> Tiro con arco y circuito de tirolesas</li>
                                <li><i class="bi bi-check-circle-fill"></i> Cabalgatas al atardecer por la montaña</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- SECTION: NUESTRA FILOSOFÍA GASTRONÓMICA -->
    <section id="gastronomia" class="py-5" style="background-color: #0b1a20; border-top: 1px solid var(--border-subtle);">
        <div class="container py-5">
            <div class="row align-items-center mb-5">
                <div class="col-lg-6">
                    <span class="section-eyebrow">COCINA DE ORIGEN & FUEGO</span>
                    <h2 class="section-title">El Arte Culinario de Rodavento</h2>
                    <p class="text-white-50" style="font-size: 1.05rem; line-height: 1.8;">
                        Nuestra cocina rinde tributo a la riqueza del Estado de México. Cada ingrediente tiene una historia:
                        la trucha arcoíris criada en aguas cristalinas de deshielo, las hortalizas cosechadas al amanecer en huertos
                        vecinos y las brasas que sellan la memoria de nuestros comensales.
                    </p>
                    <div class="d-flex gap-3 mt-4">
                        <a href="#menu" class="btn btn-outline-warning rounded-pill px-4 py-2">
                            <i class="bi bi-arrow-down-circle me-1"></i> Explorar Platillos
                        </a>
                        <button type="button" class="btn btn-warning rounded-pill px-4 py-2 text-dark fw-bold" data-bs-toggle="modal" data-bs-target="#callReservationModal">
                            <i class="bi bi-telephone-fill me-1"></i> Reservar por Llamada
                        </button>
                    </div>
                </div>
                <div class="col-lg-6 mt-4 mt-lg-0">
                    <div class="position-relative p-2 rounded" style="background: rgba(212, 175, 55, 0.15); border: 1px solid var(--border-gold);">
                        <img src="https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&w=900&q=80" alt="Gastronomía de Alta Cocina Rodavento" class="img-fluid rounded shadow">
                    </div>
                </div>
            </div>

            <!-- 4 Pilares -->
            <div class="row g-4 mt-3">
                <div class="col-lg-3 col-sm-6">
                    <div class="pillar-box">
                        <div class="pillar-icon">
                            <i class="bi bi-water"></i>
                        </div>
                        <h4 class="pillar-title">Trucha de Manantial</h4>
                        <p class="pillar-desc">Captura sustentable en manantiales de Valle de Bravo, preparada con mantequillas de hierbas y frutos locales.</p>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6">
                    <div class="pillar-box">
                        <div class="pillar-icon">
                            <i class="bi bi-fire"></i>
                        </div>
                        <h4 class="pillar-title">Brasas & Fuego Vivo</h4>
                        <p class="pillar-desc">Cocción lenta en cazuelas de barro y leña de encino para cortes de res premium y hongos silvestres.</p>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6">
                    <div class="pillar-box">
                        <div class="pillar-icon">
                            <i class="bi bi-flower1"></i>
                        </div>
                        <h4 class="pillar-title">Huerto & Montaña</h4>
                        <p class="pillar-desc">Quesos de cabra de Avándaro, betabeles rostizados, piñón rosa y miel pura de abeja de bosque de niebla.</p>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6">
                    <div class="pillar-box">
                        <div class="pillar-icon">
                            <i class="bi bi-cup-straw"></i>
                        </div>
                        <h4 class="pillar-title">Mixología Botánica</h4>
                        <p class="pillar-desc">Cocteles de autor con esencias de romero silvestre, ginebras botánicas, ahumados de ocote y cava mexicana.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- SECTION: PLATILLOS ESTRELLA & MENÚ INTERACTIVO -->
    <section id="menu" class="py-5" style="background-color: #071318;">
        <div class="container py-5">
            <div class="text-center">
                <span class="section-eyebrow">CARTA DE AUTOR & SABORES DE ESTACIÓN</span>
                <h2 class="section-title">Nuestros Platillos Emblemáticos</h2>
                <div class="gold-divider"></div>
                <p class="section-desc">
                    Explore la selección gastronómica activa de nuestros restaurantes. Cada creación está diseñada
                    por nuestro equipo de chefs para llevarle a un viaje sensorial por los paisajes de Valle de Bravo.
                </p>
            </div>

            <!-- Buscador en tiempo real de Platillos -->
            <div class="search-box-wrapper">
                <i class="bi bi-search search-dish-icon"></i>
                <input type="text" id="searchDishesInput" class="search-dish-input" placeholder="Buscar platillos (ej. trucha, chocolate, carnes, betabel, gin)...">
            </div>

            <!-- Botones de Filtro por Categoría -->
            <div class="menu-filter-container">
                <button type="button" class="menu-filter-btn active" data-filter="all">Todos los Platillos</button>
                @foreach($categorias as $cat)
                    <button type="button" class="menu-filter-btn" data-filter="cat-{{ $cat->id }}">
                        {{ $cat->nombre }}
                    </button>
                @endforeach
            </div>

            <!-- Grid de Platillos -->
            <div class="row g-4" id="dishesGrid">
                @forelse($platillos as $platillo)
                    @php
                        // Imagen de respaldo según categoría si la del platillo es genérica o ausente
                        $img = $platillo->imagen_url;
                        if (!$img || $img === 'https://unsplash.com') {
                            $catName = strtolower($platillo->categoria->nombre ?? '');
                            if (str_contains($catName, 'pesca') || str_contains($catName, 'trucha')) {
                                $img = 'https://images.unsplash.com/photo-1519708227418-c8fd9a32b7a2?auto=format&fit=crop&w=700&q=80';
                            } elseif (str_contains($catName, 'carne') || str_contains($catName, 'fuego')) {
                                $img = 'https://images.unsplash.com/photo-1544025162-d76694265947?auto=format&fit=crop&w=700&q=80';
                            } elseif (str_contains($catName, 'postre')) {
                                $img = 'https://images.unsplash.com/photo-1579372786545-d24232daf58c?auto=format&fit=crop&w=700&q=80';
                            } elseif (str_contains($catName, 'mixolog') || str_contains($catName, 'coctel')) {
                                $img = 'https://images.unsplash.com/photo-1514362545857-3bc16c4c7d1b?auto=format&fit=crop&w=700&q=80';
                            } else {
                                $img = 'https://images.unsplash.com/photo-1540420773420-3366772f4999?auto=format&fit=crop&w=700&q=80';
                            }
                        }
                    @endphp

                    <div class="col-lg-4 col-md-6 dish-item cat-{{ $platillo->categoria_id }}"
                         data-name="{{ strtolower($platillo->nombre) }}"
                         data-desc="{{ strtolower($platillo->descripcion) }}"
                         data-cat="{{ strtolower($platillo->categoria->nombre ?? '') }}">
                        <div class="dish-card">
                            <div class="dish-card-img-wrapper">
                                <img src="{{ $img }}" alt="{{ $platillo->nombre }}" class="dish-card-img" loading="lazy">
                                <span class="dish-cat-badge">
                                    <i class="bi bi-tag-fill me-1"></i>{{ $platillo->categoria->nombre ?? 'Especialidad' }}
                                </span>
                                <span class="dish-price-tag">
                                    ${{ number_format($platillo->precio, 2) }} <small class="fs-6 text-white-50">MXN</small>
                                </span>
                            </div>

                            <div class="dish-card-body">
                                <div>
                                    <h3 class="dish-title">{{ $platillo->nombre }}</h3>
                                    <p class="dish-desc">{{ $platillo->descripcion }}</p>
                                </div>

                                <div class="dish-card-footer">
                                    <div class="d-flex align-items-center gap-1 text-warning small">
                                        <i class="bi bi-star-fill"></i>
                                        <i class="bi bi-star-fill"></i>
                                        <i class="bi bi-star-fill"></i>
                                        <i class="bi bi-star-fill"></i>
                                        <i class="bi bi-star-fill"></i>
                                        <span class="text-white-50 ms-1" style="font-size: 0.72rem;">Chef Pick</span>
                                    </div>

                                    <button type="button"
                                            class="dish-btn-detail btn-open-dish-modal"
                                            data-name="{{ $platillo->nombre }}"
                                            data-price="${{ number_format($platillo->precio, 2) }} MXN"
                                            data-cat="{{ $platillo->categoria->nombre ?? 'Especialidad' }}"
                                            data-desc="{{ $platillo->descripcion }}"
                                            data-img="{{ $img }}">
                                        <i class="bi bi-eye me-1"></i> Detalles
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-5">
                        <i class="bi bi-egg-fried fs-1 text-warning mb-3"></i>
                        <h4 class="text-white">Nuestra carta de temporada se está actualizando</h4>
                        <p class="text-white-50">Por favor comuníquese por llamada a recepción para consultar las opciones del día.</p>
                    </div>
                @endforelse
            </div>

            <!-- Estado sin resultados -->
            <div id="noDishesFound" class="text-center py-5 d-none">
                <i class="bi bi-search fs-2 text-warning mb-3"></i>
                <h4 class="text-white">No encontramos platillos con ese término</h4>
                <p class="text-white-50">Intente con palabras como "trucha", "betabel", "vino", "chocolate" o explore todas las categorías.</p>
                <button type="button" class="btn btn-outline-warning btn-sm rounded-pill mt-2" onclick="resetFilters()">
                    Restablecer Filtros
                </button>
            </div>
        </div>
    </section>

    <!-- SECTION: EXPERIENCIAS GASTRONÓMICAS EXCLUSIVAS -->
    <section id="experiencias" class="py-5" style="background-color: #0b1a20; border-top: 1px solid var(--border-subtle);">
        <div class="container py-5">
            <div class="text-center">
                <span class="section-eyebrow">MOMENTOS INOLVIDABLES</span>
                <h2 class="section-title">Experiencias Culinarias a Medida</h2>
                <div class="gold-divider"></div>
                <p class="section-desc">
                    Diseñadas para celebrar aniversarios, reencuentros o simplemente regalarse una vivencia extraordinaria.
                </p>
            </div>

            <div class="row g-4 mt-2">
                <div class="col-lg-4 col-md-6">
                    <div class="hotel-feature-card">
                        <div class="hotel-card-img-wrapper">
                            <img src="https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?auto=format&fit=crop&w=700&q=80" alt="Cena en el Muelle del Lago" class="hotel-card-img">
                            <div class="hotel-badge-overlay">Exclusivo Parejas</div>
                        </div>
                        <div class="hotel-card-body">
                            <div>
                                <h3>Cena a la Luz de las Velas en el Muelle</h3>
                                <p>Mesa privada sobre la plataforma del lago con menú degustación de 5 tiempos, antorchas de fuego vivo y maridaje premium.</p>
                            </div>
                            <button type="button" class="btn btn-outline-warning btn-sm w-100 rounded-pill" data-bs-toggle="modal" data-bs-target="#callReservationModal">
                                <i class="bi bi-telephone-fill me-1"></i> Reservar por Llamada
                            </button>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6">
                    <div class="hotel-feature-card">
                        <div class="hotel-card-img-wrapper">
                            <img src="https://images.unsplash.com/photo-1510812431401-41d2bd2722f3?auto=format&fit=crop&w=700&q=80" alt="Cata y Maridaje en la Cava" class="hotel-card-img">
                            <div class="hotel-badge-overlay">Sommelier Guiado</div>
                        </div>
                        <div class="hotel-card-body">
                            <div>
                                <h3>Cata de Vinos Mexicanos & Quesos Artesanales</h3>
                                <p>Un recorrido por los mejores terruños del Valle de Guadalupe y Parras, acompañados de quesos de oveja y cabra de Avándaro.</p>
                            </div>
                            <button type="button" class="btn btn-outline-warning btn-sm w-100 rounded-pill" data-bs-toggle="modal" data-bs-target="#callReservationModal">
                                <i class="bi bi-telephone-fill me-1"></i> Reservar por Llamada
                            </button>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-12">
                    <div class="hotel-feature-card">
                        <div class="hotel-card-img-wrapper">
                            <img src="https://images.unsplash.com/photo-1476224203421-9ac39bcb3327?auto=format&fit=crop&w=700&q=80" alt="Desayuno Bosque y Manantial" class="hotel-card-img">
                            <div class="hotel-badge-overlay">Al Amanecer</div>
                        </div>
                        <div class="hotel-card-body">
                            <div>
                                <h3>Desayuno Huerto & Manantial</h3>
                                <p>Pan recién salido de nuestro horno de leña, café de altura infusionado en olla de barro, mermeladas de frutos silvestres y jugo fresco.</p>
                            </div>
                            <button type="button" class="btn btn-outline-warning btn-sm w-100 rounded-pill" data-bs-toggle="modal" data-bs-target="#callReservationModal">
                                <i class="bi bi-telephone-fill me-1"></i> Reservar por Llamada
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- SECTION: NUESTRAS SUCURSALES -->
    <section id="sucursales" class="py-5" style="background-color: #071318;">
        <div class="container py-5">
            <div class="text-center">
                <span class="section-eyebrow">DESTINOS GASTRONÓMICOS</span>
                <h2 class="section-title">Nuestras Ubicaciones</h2>
                <div class="gold-divider"></div>
                <p class="section-desc">
                    Viva la hospitalidad y sabor de la familia Rodavento en tres locaciones emblemáticas de Valle de Bravo.
                </p>
            </div>

            <div class="row g-4 mt-2">
                @forelse($sucursales as $sucursal)
                    <div class="col-lg-4 col-md-6">
                        <div class="sucursal-card">
                            <div>
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <span class="badge" style="background: rgba(212, 175, 55, 0.15); color: var(--accent-gold); border: 1px solid var(--border-gold);">
                                        <i class="bi bi-geo-alt-fill me-1"></i> Sucursal Oficial
                                    </span>
                                    <span class="text-success small"><i class="bi bi-circle-fill" style="font-size: 8px;"></i> Abierto</span>
                                </div>

                                <h3 class="sucursal-name">{{ $sucursal->nombre }}</h3>

                                <div class="sucursal-info-item">
                                    <i class="bi bi-pin-map-fill"></i>
                                    <span>{{ $sucursal->direccion }}</span>
                                </div>

                                <div class="sucursal-info-item">
                                    <i class="bi bi-telephone-fill"></i>
                                    <a href="tel:{{ $sucursal->telefono }}" class="text-white text-decoration-none">
                                        {{ $sucursal->telefono }}
                                    </a>
                                </div>

                                <div class="sucursal-info-item">
                                    <i class="bi bi-clock-fill"></i>
                                    <span>Lunes a Domingo: 08:00 - 23:00 hrs</span>
                                </div>
                            </div>

                            <div class="mt-4 pt-3 border-top border-secondary">
                                <a href="tel:{{ preg_replace('/[^0-9+]/', '', $sucursal->telefono) }}" class="btn btn-outline-warning btn-sm w-100 rounded-pill">
                                    <i class="bi bi-telephone-fill me-1"></i> Llamar para Reservar
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-lg-4">
                        <div class="sucursal-card">
                            <h3 class="sucursal-name">Restaurante Lago Rodavento</h3>
                            <p class="text-white-50">Km 3.5 Carretera Valle de Bravo - Los Álamos, Valle de Bravo.</p>
                            <a href="tel:7262628900" class="btn btn-outline-warning btn-sm w-100 rounded-pill">
                                <i class="bi bi-telephone-fill me-1"></i> Llamar: 7262628900
                            </a>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- SECTION: BANNER DE RESERVACIONES EXCLUSIVAS VÍA TELEFÓNICA -->
    <section class="call-reservation-banner">
        <div class="container position-relative" style="z-index: 2;">
            <div class="call-reservation-box text-center">
                <span class="section-eyebrow">ATENCIÓN PERSONALIZADA</span>
                <h2 class="section-title text-white">Reservaciones Exclusivas por Llamada</h2>
                <div class="gold-divider"></div>
                <p class="text-white-50 max-w-700 mx-auto mb-4" style="font-size: 1.05rem; max-width: 650px;">
                    Para garantizar la exclusividad, la vista deseada hacia el lago o la chimenea y preparar sus preferencias especiales,
                    todas las reservaciones en Hotel y Restaurantes Rodavento se atienden personalmente por teléfono.
                </p>

                <div class="d-flex flex-wrap justify-content-center gap-3">
                    <button type="button" class="btn-hero-primary px-5 py-3 fs-6" data-bs-toggle="modal" data-bs-target="#callReservationModal">
                        <i class="bi bi-telephone-outbound-fill fs-5"></i>
                        <span>Ver Números y Llamar Ahora</span>
                    </button>
                </div>

                <div class="mt-4 pt-2 text-white-50 small">
                    <i class="bi bi-headset text-warning me-1"></i> Nuestro concierge le atenderá con gusto de 8:00 AM a 10:00 PM.
                </div>
            </div>
        </div>
    </section>

    <!-- SECTION: TESTIMONIOS -->
    <section class="py-5" style="background-color: #071318;">
        <div class="container py-5">
            <div class="text-center">
                <span class="section-eyebrow">OPINIONES DE HUÉSPEDES</span>
                <h2 class="section-title">Palabras que nos Inspiran</h2>
                <div class="gold-divider"></div>
            </div>

            <div class="row g-4 mt-2">
                <div class="col-lg-4 col-md-6">
                    <div class="testimonial-card">
                        <div class="testimonial-stars">
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                        </div>
                        <p class="testimonial-text">
                            "La trucha salmonada con avellanas y el costillar al vino tinto son simplemente supremos.
                            Cenar sobre el muelle con el reflejo de los pinos en el agua es una de las vivencias más mágicas de México."
                        </p>
                        <p class="testimonial-author">Mariana Sotomayor</p>
                        <span class="testimonial-role">Huésped Suite Lago</span>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6">
                    <div class="testimonial-card">
                        <div class="testimonial-stars">
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                        </div>
                        <p class="testimonial-text">
                            "El cóctel Niebla de Rodavento con notas de romero y ocote es una obra de arte. La atención en el restaurante
                            y la calidez del personal hacen que quieras regresar cada fin de semana."
                        </p>
                        <p class="testimonial-author">Carlos De La Vega</p>
                        <span class="testimonial-role">Crítico Gastronómico & Viajero</span>
                    </div>
                </div>

                <div class="col-lg-4 col-md-12">
                    <div class="testimonial-card">
                        <div class="testimonial-stars">
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                        </div>
                        <p class="testimonial-text">
                            "Dormir escuchando el viento entre los árboles y despertar con un desayuno de pan artesanal y frutos locales
                            te renueva por completo. Es el estándar de oro en hotelería boutique."
                        </p>
                        <p class="testimonial-author">Elena & Fernando Méndez</p>
                        <span class="testimonial-role">Visita Aniversario</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <footer id="contacto" class="luxury-footer">
        <div class="container">
            <div class="row g-4 mb-5">
                <div class="col-lg-4">
                    <div class="footer-logo-title">HOTEL RODAVENTO</div>
                    <p class="text-white-50 small pe-lg-4">
                        Refugio boutique y templo gastronómico inmerso en los bosques de Valle de Bravo. Excelencia en cada detalle,
                        sustentabilidad y amor por la hospitalidad mexicana.
                    </p>
                    <div class="mt-3">
                        <a href="https://instagram.com" target="_blank" class="footer-social-icon" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
                        <a href="https://facebook.com" target="_blank" class="footer-social-icon" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
                        <a href="https://tripadvisor.com" target="_blank" class="footer-social-icon" aria-label="TripAdvisor"><i class="bi bi-airplane-engines"></i></a>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <h5 class="text-white fs-6 mb-3 font-serif">Explorar</h5>
                    <a href="#inicio" class="footer-link">Inicio</a>
                    <a href="#hotel" class="footer-link">El Hotel</a>
                    <a href="#gastronomia" class="footer-link">Gastronomía</a>
                    <a href="#menu" class="footer-link">Nuestros Platillos</a>
                    <a href="#experiencias" class="footer-link">Experiencias</a>
                    <a href="#sucursales" class="footer-link">Sucursales</a>
                </div>

                <div class="col-lg-2 col-6">
                    <h5 class="text-white fs-6 mb-3 font-serif">Reservaciones</h5>
                    <a href="#" class="footer-link text-warning" data-bs-toggle="modal" data-bs-target="#callReservationModal">
                        <i class="bi bi-telephone-fill me-1"></i> Llamar a Concierge
                    </a>
                    <a href="tel:7262628900" class="footer-link">
                        (726) 262-8900
                    </a>
                    <a href="tel:7262629100" class="footer-link">
                        (726) 262-9100
                    </a>
                </div>

                <div class="col-lg-3">
                    <h5 class="text-white fs-6 mb-3 font-serif">Contacto Directo</h5>
                    <p class="small text-white-50 mb-2">
                        <i class="bi bi-geo-alt-fill text-warning me-2"></i> Km 3.5 Valle de Bravo - Los Álamos, Edo. Méx.
                    </p>
                    <p class="small text-white-50 mb-2">
                        <i class="bi bi-telephone-fill text-warning me-2"></i> +52 (726) 262-8900
                    </p>
                    <p class="small text-white-50 mb-3">
                        <i class="bi bi-envelope-fill text-warning me-2"></i> concierge@hotelrodavento.com
                    </p>

                    <!-- Acceso discreto para empleados / colaboradores al final del footer -->
                    <div class="pt-2 border-top border-secondary">
                        <span class="text-white-50" style="font-size: 0.72rem;">Portal Administrativo:</span><br>
                        <a href="{{ route('login') }}" class="text-warning text-decoration-none small me-2">
                            <i class="bi bi-box-arrow-in-right me-1"></i>Iniciar sesión
                        </a>
                        <span class="text-white-50">·</span>
                        <a href="{{ route('register') }}" class="text-warning text-decoration-none small ms-2">
                            <i class="bi bi-person-plus me-1"></i>Registrarte
                        </a>
                    </div>
                </div>
            </div>

            <div class="border-top border-secondary pt-4 text-center text-white-50 small">
                <div class="row align-items-center">
                    <div class="col-md-6 text-md-start mb-2 mb-md-0">
                        &copy; {{ date('Y') }} Hotel & Restaurante Rodavento. Todos los derechos reservados.
                    </div>
                    <div class="col-md-6 text-md-end">
                        <span class="text-warning">Lujo Rústico · Sostenibilidad · Alta Cocina</span>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- MODAL: DETALLES DE PLATILLO INTERACTIVO -->
    <div class="modal fade" id="dishDetailModal" tabindex="-1" aria-labelledby="dishDetailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content modal-content-luxury">
                <div class="modal-header modal-header-luxury d-flex justify-content-between align-items-center">
                    <div>
                        <span id="modalDishCategory" class="badge bg-warning text-dark text-uppercase small">Categoría</span>
                        <h4 class="modal-title font-serif text-white mt-1" id="modalDishTitle">Nombre del Platillo</h4>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-4 align-items-center">
                        <div class="col-md-6 text-center">
                            <img id="modalDishImg" src="" alt="Platillo Rodavento" class="img-fluid rounded shadow border border-secondary" style="max-height: 280px; width: 100%; object-fit: cover;">
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <span class="text-white-50 small">Precio Sugerido:</span>
                                <h3 id="modalDishPrice" class="text-warning font-serif m-0">$0.00 MXN</h3>
                            </div>
                            <h5 class="text-white font-serif fs-6 mb-2">Descripción del Chef:</h5>
                            <p id="modalDishDesc" class="text-white-50 small" style="line-height: 1.7;"></p>

                            <div class="p-3 rounded mb-3" style="background: rgba(255, 255, 255, 0.04); border: 1px solid var(--border-gold);">
                                <div class="d-flex align-items-center gap-2 text-warning small mb-1">
                                    <i class="bi bi-patch-check-fill"></i>
                                    <strong>Garantía de Frescura Rodavento</strong>
                                </div>
                                <span class="text-white-50" style="font-size: 0.78rem;">
                                    Ingredientes orgánicos de estación provenientes de productores locales y manantiales limpios de Valle de Bravo.
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer modal-footer-luxury d-flex justify-content-between align-items-center">
                    <span class="text-white-50 small"><i class="bi bi-info-circle text-warning me-1"></i> Consulte maridaje sugerido con el sommelier.</span>
                    <button type="button" class="btn btn-warning text-dark fw-bold rounded-pill px-4" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#callReservationModal">
                        <i class="bi bi-telephone-fill me-1"></i> Llamar para Reservar Mesa
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL EXCLUSIVO: LÍNEAS TELEFÓNICAS DE RESERVACIONES -->
    <div class="modal fade" id="callReservationModal" tabindex="-1" aria-labelledby="callReservationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content modal-content-luxury">
                <div class="modal-header modal-header-luxury">
                    <div>
                        <h4 class="modal-title font-serif text-white m-0">
                            <i class="bi bi-telephone-outbound text-warning me-2"></i>Línea de Reservaciones
                        </h4>
                        <span class="text-white-50 small">Hotel & Restaurantes Rodavento · Valle de Bravo</span>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="p-3 rounded mb-4" style="background: rgba(212, 175, 55, 0.08); border: 1px solid var(--border-gold);">
                        <div class="d-flex align-items-start gap-2">
                            <i class="bi bi-info-circle-fill text-warning fs-5"></i>
                            <div class="small text-white-50">
                                Para brindarle una atención exclusiva y asignarle la mejor mesa con vista al lago o chimenea, <strong>todas nuestras reservaciones se gestionan únicamente por llamada telefónica</strong>.
                            </div>
                        </div>
                    </div>

                    <h6 class="text-uppercase small text-warning fw-bold mb-3" style="letter-spacing: 1px;">
                        Seleccione la sucursal para llamar:
                    </h6>

                    <!-- Opción 1: Restaurante Lago Rodavento -->
                    <a href="tel:7262628900" class="phone-call-card">
                        <div>
                            <strong class="d-block text-white font-serif fs-6">Restaurante Lago Rodavento</strong>
                            <small class="text-white-50">Hotel Rodavento · Km 3.5 Los Álamos</small>
                        </div>
                        <div class="d-flex align-items-center gap-2 text-warning fw-bold">
                            <i class="bi bi-telephone-fill fs-5"></i>
                            <span>(726) 262-8900</span>
                        </div>
                    </a>

                    <!-- Opción 2: Cinco Rodavento Rooftop -->
                    <a href="tel:7262629100" class="phone-call-card">
                        <div>
                            <strong class="d-block text-white font-serif fs-6">Cinco Rodavento Rooftop</strong>
                            <small class="text-white-50">Centro Histórico · Terraza Panorámica</small>
                        </div>
                        <div class="d-flex align-items-center gap-2 text-warning fw-bold">
                            <i class="bi bi-telephone-fill fs-5"></i>
                            <span>(726) 262-9100</span>
                        </div>
                    </a>

                    <!-- Opción 3: Casa Rodavento -->
                    <a href="tel:+525531451191" class="phone-call-card">
                        <div>
                            <strong class="d-block text-white font-serif fs-6">Casa Rodavento</strong>
                            <small class="text-white-50">Callejón de las Ratas · Intimidad & Cava</small>
                        </div>
                        <div class="d-flex align-items-center gap-2 text-warning fw-bold">
                            <i class="bi bi-telephone-fill fs-5"></i>
                            <span>55 3145 1191</span>
                        </div>
                    </a>

                    <div class="mt-4 pt-3 border-top border-secondary text-center text-white-50 small">
                        <i class="bi bi-clock-fill text-warning me-1"></i> Horario de atención: Lunes a Domingo de 8:00 AM a 10:00 PM.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5.3 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Interactive Scripts for Filtering, Search, Modals and Navbar -->
    <script>
        // Navbar scroll effect
        window.addEventListener('scroll', () => {
            const navbar = document.getElementById('navbar');
            if (window.scrollY > 40) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });

        // Filter and Search Dishes
        const filterBtns = document.querySelectorAll('.menu-filter-btn');
        const dishItems = document.querySelectorAll('.dish-item');
        const searchInput = document.getElementById('searchDishesInput');
        const noDishesFound = document.getElementById('noDishesFound');

        let currentFilter = 'all';

        filterBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                filterBtns.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                currentFilter = btn.getAttribute('data-filter');
                applyFilterAndSearch();
            });
        });

        if (searchInput) {
            searchInput.addEventListener('input', () => {
                applyFilterAndSearch();
            });
        }

        function applyFilterAndSearch() {
            const query = (searchInput ? searchInput.value : '').toLowerCase().trim();
            let visibleCount = 0;

            dishItems.forEach(item => {
                const matchCategory = (currentFilter === 'all') || item.classList.contains(currentFilter);
                const name = item.getAttribute('data-name') || '';
                const desc = item.getAttribute('data-desc') || '';
                const cat = item.getAttribute('data-cat') || '';

                const matchQuery = !query || name.includes(query) || desc.includes(query) || cat.includes(query);

                if (matchCategory && matchQuery) {
                    item.style.display = 'block';
                    visibleCount++;
                } else {
                    item.style.display = 'none';
                }
            });

            if (noDishesFound) {
                if (visibleCount === 0) {
                    noDishesFound.classList.remove('d-none');
                } else {
                    noDishesFound.classList.add('d-none');
                }
            }
        }

        function resetFilters() {
            if (searchInput) searchInput.value = '';
            filterBtns.forEach(b => b.classList.remove('active'));
            if (filterBtns[0]) filterBtns[0].classList.add('active');
            currentFilter = 'all';
            applyFilterAndSearch();
        }

        // Dish Modal Details trigger
        const detailButtons = document.querySelectorAll('.btn-open-dish-modal');
        const dishModal = new bootstrap.Modal(document.getElementById('dishDetailModal'));

        detailButtons.forEach(button => {
            button.addEventListener('click', () => {
                const name = button.getAttribute('data-name');
                const price = button.getAttribute('data-price');
                const cat = button.getAttribute('data-cat');
                const desc = button.getAttribute('data-desc');
                const img = button.getAttribute('data-img');

                document.getElementById('modalDishTitle').textContent = name;
                document.getElementById('modalDishPrice').textContent = price;
                document.getElementById('modalDishCategory').textContent = cat;
                document.getElementById('modalDishDesc').textContent = desc;
                document.getElementById('modalDishImg').src = img;

                dishModal.show();
            });
        });
    </script>
</body>
</html>
