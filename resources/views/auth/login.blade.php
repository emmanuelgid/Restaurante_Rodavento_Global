
@extends('layouts.app')

@section('title', 'Iniciar Sesión - Hotel Rodavento')

@section('content')

    <style>
        :root {
            --bg-primary: #0b1d26;
            --accent-gold: #d4af37;
            --accent-gold-hover: #c5a028;
            --white: #ffffff;
            --muted: rgba(255,255,255,.55);
            --border: rgba(255,255,255,.11);
        }

        /* CONTENEDOR PRINCIPAL */

        .login-wrapper {
            min-height: calc(100vh - 70px);
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 45px 20px;
            font-family: 'Inter', sans-serif;
            background:
                radial-gradient(circle at 15% 15%,
                rgba(212,175,55,.08), transparent 35%),
                radial-gradient(circle at 85% 85%,
                rgba(31,75,86,.20), transparent 40%),
                var(--bg-primary);
        }

        /* TARJETA */

        .login-card {
            width: 100%;
            max-width: 850px;
            display: grid;
            grid-template-columns: .85fr 1.15fr;
            background: rgba(255,255,255,.025);
            border: 1px solid var(--border);
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 25px 70px rgba(0,0,0,.35);
            animation: loginAppear .8s ease both;
        }

        /* PANEL INSTITUCIONAL */

        .login-brand {
            padding: 45px 35px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            background: linear-gradient(
                145deg,
                rgba(255,255,255,.045),
                transparent
            );
            border-right: 1px solid var(--border);
        }

        .login-symbol {
            width: 48px;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid rgba(212,175,55,.55);
            color: var(--accent-gold);
            font-size: 1.5rem;
            margin-bottom: 30px;
        }

        .login-overline {
            font-size: .65rem;
            color: var(--accent-gold);
            letter-spacing: 3px;
            text-transform: uppercase;
            margin-bottom: 18px;
        }

        .login-brand h1 {
            font-family: 'Playfair Display', serif;
            font-size: 2.6rem;
            line-height: 1.2;
            font-weight: 500;
            letter-spacing: 2px;
            color: var(--white);
        }

        .login-line {
            width: 55px;
            height: 2px;
            background: var(--accent-gold);
            margin: 23px 0;
        }

        .login-brand-description {
            color: var(--muted);
            font-size: .79rem;
            line-height: 1.9;
            font-weight: 300;
        }

        .login-brand-footer {
            color: rgba(255,255,255,.35);
            font-size: .62rem;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-top: 45px;
        }

        /* PANEL DE ACCESO */

        .login-form-panel {
            padding: 45px 45px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .form-overline {
            color: var(--accent-gold);
            font-size: .65rem;
            letter-spacing: 2.5px;
            text-transform: uppercase;
            font-weight: 600;
            margin-bottom: 12px;
        }

        .form-title {
            font-family: 'Playfair Display', serif;
            color: var(--white);
            font-size: 2rem;
            font-weight: 500;
            margin-bottom: 10px;
        }

        .form-description {
            color: var(--muted);
            font-size: .76rem;
            line-height: 1.8;
            margin-bottom: 30px;
        }

        /* CAMPOS */

        .login-field {
            margin-bottom: 22px;
        }

        .login-label {
            display: block;
            color: rgba(255,255,255,.75);
            font-size: .72rem;
            font-weight: 500;
            margin-bottom: 9px;
            letter-spacing: .3px;
        }

        .login-label i {
            color: var(--accent-gold);
            margin-right: 7px;
        }

        .login-input-wrapper {
            position: relative;
        }

        .login-input {
            width: 100%;
            height: 49px;
            padding: 0 15px;
            border-radius: 4px;
            border: 1px solid rgba(255,255,255,.13);
            outline: none;
            background: rgba(255,255,255,.035);
            color: var(--white);
            font-family: 'Inter', sans-serif;
            font-size: .77rem;
            transition: all .3s ease;
        }

        .login-input::placeholder {
            color: rgba(255,255,255,.3);
        }

        .login-input:focus {
            border-color: var(--accent-gold);
            background: rgba(212,175,55,.035);
            box-shadow: 0 0 0 3px rgba(212,175,55,.08);
            color: var(--white);
        }

        .login-input.is-invalid {
            border-color: #dc7777;
        }

        .login-input:-webkit-autofill,
        .login-input:-webkit-autofill:hover,
        .login-input:-webkit-autofill:focus {
            -webkit-text-fill-color: white;
            transition: background-color 5000s ease-in-out 0s;
        }

        .login-error {
            display: block;
            color: #e59a9a;
            font-size: .68rem;
            margin-top: 7px;
        }

        /* MOSTRAR CONTRASEÑA */

        .login-password-toggle {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: rgba(255,255,255,.5);
            cursor: pointer;
            padding: 5px;
            font-size: .95rem;
        }

        .login-password-toggle:hover {
            color: var(--accent-gold);
        }

        /* RECORDAR CREDENCIALES */

        .login-remember {
            display: flex;
            align-items: center;
            gap: 9px;
            margin: 5px 0 28px;
        }

        .login-remember input {
            width: 15px;
            height: 15px;
            accent-color: var(--accent-gold);
            cursor: pointer;
        }

        .login-remember label {
            color: var(--muted);
            font-size: .7rem;
            cursor: pointer;
        }

        /* BOTÓN */

        .btn-login {
            width: 100%;
            height: 51px;
            border: 1px solid var(--accent-gold);
            border-radius: 4px;
            background: var(--accent-gold);
            color: var(--bg-primary);
            font-size: .76rem;
            font-weight: 700;
            letter-spacing: .8px;
            text-transform: uppercase;
            transition: all .3s ease;
        }

        .btn-login:hover {
            background: var(--accent-gold-hover);
            border-color: var(--accent-gold-hover);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(212,175,55,.15);
        }

        .btn-login i {
            margin-left: 8px;
        }

        /* AVISO DE SEGURIDAD */

        .login-security {
            display: flex;
            align-items: center;
            gap: 9px;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid var(--border);
            color: rgba(255,255,255,.4);
            font-size: .67rem;
            line-height: 1.6;
        }

        .login-security i {
            color: var(--accent-gold);
            font-size: .95rem;
        }

        /* ERRORES GENERALES */

        .login-alert {
            padding: 12px 14px;
            margin-bottom: 20px;
            border: 1px solid rgba(220,119,119,.4);
            border-radius: 4px;
            color: #e9a0a0;
            background: rgba(220,119,119,.07);
            font-size: .72rem;
        }

        /* ANIMACIÓN */

        @keyframes loginAppear {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* RESPONSIVE */

        @media(max-width: 700px) {
            .login-card {
                grid-template-columns: 1fr;
                max-width: 480px;
            }

            .login-brand {
                padding: 30px;
                border-right: none;
                border-bottom: 1px solid var(--border);
            }

            .login-brand h1 {
                font-size: 2.1rem;
            }

            .login-brand-footer {
                margin-top: 25px;
            }

            .login-form-panel {
                padding: 35px 30px;
            }
        }

        @media(max-width: 380px) {
            .login-form-panel,
            .login-brand {
                padding: 25px 20px;
            }
        }
    </style>


    <div class="login-wrapper">

        <div class="login-card">

            <!-- IDENTIDAD DEL HOTEL -->

            <div class="login-brand">

                <div>
                    <div class="login-symbol">
                        <i class="bi bi-buildings"></i>
                    </div>

                    <div class="login-overline">
                        Portal Operativo
                    </div>

                    <h1>
                        HOTEL<br>RODAVENTO
                    </h1>

                    <div class="login-line"></div>

                    <p class="login-brand-description">
                        Acceda a la plataforma interna de
                        gestión y operaciones de Hotel Rodavento.
                        Un espacio destinado al personal
                        autorizado.
                    </p>
                </div>

                <div class="login-brand-footer">
                    Excelencia en cada detalle
                </div>

            </div>


            <!-- FORMULARIO DE ACCESO -->

            <div class="login-form-panel">

                <div class="form-overline">
                    Sistema de operaciones
                </div>

                <h2 class="form-title">
                    Bienvenido
                </h2>

                <p class="form-description">
                    Ingrese sus credenciales para acceder
                    a la plataforma institucional.
                </p>


                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <!-- CORREO ELECTRÓNICO -->

                    <div class="login-field">

                        <label for="email" class="login-label">
                            <i class="bi bi-envelope"></i>
                            Correo electrónico
                        </label>

                        <input
                            id="email"
                            type="email"
                            class="login-input @error('email') is-invalid @enderror"
                            name="email"
                            value="{{ old('email') }}"
                            placeholder="nombre@rodavento.com"
                            required
                            autocomplete="email"
                            autofocus
                        >

                        @error('email')
                        <span class="login-error" role="alert">
                            {{ $message }}
                        </span>
                        @enderror

                    </div>


                    <!-- CONTRASEÑA -->

                    <div class="login-field">

                        <label for="password" class="login-label">
                            <i class="bi bi-lock"></i>
                            Contraseña
                        </label>

                        <div class="login-input-wrapper">

                            <input
                                id="password"
                                type="password"
                                class="login-input @error('password') is-invalid @enderror"
                                name="password"
                                placeholder="Ingrese su contraseña"
                                required
                                autocomplete="current-password"
                            >

                            <button
                                type="button"
                                class="login-password-toggle"
                                onclick="toggleLoginPassword()"
                                aria-label="Mostrar contraseña"
                            >
                                <i id="password-icon" class="bi bi-eye"></i>
                            </button>

                        </div>

                        @error('password')
                        <span class="login-error" role="alert">
                            {{ $message }}
                        </span>
                        @enderror

                    </div>


                    <!-- RECORDAR CREDENCIALES -->

                    <div class="login-remember">

                        <input
                            type="checkbox"
                            name="remember"
                            id="remember"
                            value="1"
                            {{ old('remember') ? 'checked' : '' }}
                        >

                        <label for="remember">
                            Recordar mi sesión
                        </label>

                    </div>


                    <!-- ERRORES DE AUTENTICACIÓN -->

                    @if ($errors->has('email') || $errors->has('password'))
                        <div class="login-alert">
                            <i class="bi bi-exclamation-circle me-2"></i>
                            Verifique sus credenciales antes de continuar.
                        </div>
                    @endif


                    <!-- BOTÓN DE ACCESO -->

                    <button type="submit" class="btn-login">
                        Iniciar sesión
                        <i class="bi bi-arrow-right"></i>
                    </button>

                </form>


                <!-- SEGURIDAD -->

                <div class="login-security">
                    <i class="bi bi-shield-lock"></i>

                    <span>
                    Acceso exclusivo para personal autorizado
                    de Hotel Rodavento.
                </span>
                </div>

            </div>

        </div>

    </div>


    <!-- MOSTRAR / OCULTAR CONTRASEÑA -->

    <script>
        function toggleLoginPassword() {

            const password = document.getElementById('password');
            const icon = document.getElementById('password-icon');

            if (password.type === 'password') {

                password.type = 'text';
                icon.className = 'bi bi-eye-slash';

            } else {

                password.type = 'password';
                icon.className = 'bi bi-eye';

            }
        }
    </script>

@endsection
