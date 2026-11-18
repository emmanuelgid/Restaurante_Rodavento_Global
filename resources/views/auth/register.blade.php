@extends('layouts.app')

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

        .register-wrapper {
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

        .register-card {
            width: 100%;
            max-width: 850px;
            display: grid;
            grid-template-columns: .85fr 1.15fr;
            background: rgba(255,255,255,.025);
            border: 1px solid var(--border);
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 25px 70px rgba(0,0,0,.35);
            animation: cardAppear .8s ease both;
        }

        .register-brand {
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

        .register-symbol {
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

        .register-overline {
            font-size: .65rem;
            color: var(--accent-gold);
            letter-spacing: 3px;
            text-transform: uppercase;
            margin-bottom: 18px;
        }

        .register-brand h1 {
            font-family: 'Playfair Display', serif;
            font-size: 2.6rem;
            line-height: 1.2;
            font-weight: 500;
            letter-spacing: 2px;
            color: var(--white);
        }

        .register-line {
            width: 55px;
            height: 2px;
            background: var(--accent-gold);
            margin: 23px 0;
        }

        .register-brand-description {
            color: var(--muted);
            font-size: .79rem;
            line-height: 1.9;
            font-weight: 300;
        }

        .register-brand-footer {
            color: rgba(255,255,255,.35);
            font-size: .62rem;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-top: 45px;
        }

        .register-form-panel {
            padding: 40px 45px;
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
            margin-bottom: 8px;
        }

        .form-title {
            font-family: 'Playfair Display', serif;
            color: var(--white);
            font-size: 1.8rem;
            font-weight: 500;
            margin-bottom: 6px;
        }

        .form-description {
            color: var(--muted);
            font-size: .74rem;
            line-height: 1.6;
            margin-bottom: 20px;
        }

        .register-field {
            margin-bottom: 14px;
        }

        .register-label {
            display: block;
            color: rgba(255,255,255,.75);
            font-size: .70rem;
            font-weight: 500;
            margin-bottom: 6px;
            letter-spacing: .3px;
        }

        .register-label i {
            color: var(--accent-gold);
            margin-right: 6px;
        }

        .register-input-wrapper {
            position: relative;
        }

        .register-input {
            width: 100%;
            height: 42px;
            padding: 0 14px;
            border-radius: 4px;
            border: 1px solid rgba(255,255,255,.13);
            outline: none;
            background: rgba(255,255,255,.035);
            color: var(--white);
            font-family: 'Inter', sans-serif;
            font-size: .75rem;
            transition: all .3s ease;
        }

        .register-input::placeholder {
            color: rgba(255,255,255,.3);
        }

        .register-input:focus {
            border-color: var(--accent-gold);
            background: rgba(212,175,55,.035);
            box-shadow: 0 0 0 3px rgba(212,175,55,.08);
            color: var(--white);
        }

        .register-input:-webkit-autofill,
        .register-input:-webkit-autofill:hover,
        .register-input:-webkit-autofill:focus {
            -webkit-text-fill-color: white;
            transition: background-color 5000s ease-in-out 0s;
        }

        .register-input.is-invalid {
            border-color: #dc7777;
        }

        .register-input option {
            background: var(--bg-primary);
            color: white;
        }

        .register-error {
            display: block;
            color: #e59a9a;
            font-size: .66rem;
            margin-top: 5px;
        }

        .password-toggle {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: rgba(255,255,255,.5);
            cursor: pointer;
            padding: 5px;
            font-size: .9rem;
        }

        .password-toggle:hover {
            color: var(--accent-gold);
        }

        .btn-register {
            width: 100%;
            height: 46px;
            margin-top: 6px;
            border: 1px solid var(--accent-gold);
            border-radius: 4px;
            background: var(--accent-gold);
            color: var(--bg-primary);
            font-size: .74rem;
            font-weight: 700;
            letter-spacing: .8px;
            text-transform: uppercase;
            transition: all .3s ease;
        }

        .btn-register:hover {
            background: var(--accent-gold-hover);
            border-color: var(--accent-gold-hover);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(212,175,55,.15);
        }

        .btn-register i {
            margin-left: 8px;
        }

        .register-back {
            text-align: center;
            margin-top: 18px;
            padding-top: 15px;
            border-top: 1px solid var(--border);
            font-size: .70rem;
            color: var(--muted);
        }

        .register-back a {
            color: var(--accent-gold);
            text-decoration: none;
            font-weight: 600;
            margin-left: 4px;
            transition: color .3s ease;
        }

        .register-back a:hover {
            color: #f1d77e;
        }

        .register-alert {
            padding: 10px 12px;
            margin-bottom: 15px;
            border: 1px solid rgba(220,119,119,.4);
            border-radius: 4px;
            color: #e9a0a0;
            background: rgba(220,119,119,.07);
            font-size: .70rem;
        }

        @keyframes cardAppear {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media(max-width: 700px) {
            .register-card {
                grid-template-columns: 1fr;
                max-width: 480px;
            }
            .register-brand {
                padding: 30px;
                border-right: none;
                border-bottom: 1px solid var(--border);
            }
            .register-brand h1 {
                font-size: 2.1rem;
            }
            .register-form-panel {
                padding: 30px 25px;
            }
        }
    </style>

    <div class="register-wrapper">
        <div class="register-card">

            <!-- IDENTIDAD DEL HOTEL -->
            <div class="register-brand">
                <div>
                    <div class="register-symbol">
                        <i class="bi bi-buildings"></i>
                    </div>
                    <div class="register-overline">
                        Portal Operativo
                    </div>
                    <h1>
                        HOTEL<br>RODAVENTO
                    </h1>
                    <div class="register-line"></div>
                    <p class="register-brand-description">
                        Forme parte de nuestro equipo.
                        Registre sus datos para obtener acceso
                        a la plataforma interna de operaciones
                        de Hotel Rodavento.
                    </p>
                </div>
                <div class="register-brand-footer">
                    Excelencia en cada detalle
                </div>
            </div>

            <!-- FORMULARIO -->
            <div class="register-form-panel">
                <div class="form-overline">
                    Gestión de personal
                </div>
                <h2 class="form-title">
                    Registrar empleado
                </h2>
                <p class="form-description">
                    Complete los siguientes campos para crear una nueva cuenta.
                </p>

                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    <!-- NOMBRE(S) -->
                    <div class="register-field">
                        <label for="nombres" class="register-label">
                            <i class="bi bi-person"></i> Nombre(s)
                        </label>
                        <input id="nombres" type="text" class="register-input @error('nombres') is-invalid @enderror" name="nombres" value="{{ old('nombres') }}" placeholder="Ej. Emiliano" required autocomplete="given-name" autofocus>
                        @error('nombres')
                        <span class="register-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- APELLIDOS -->
                    <div class="register-field">
                        <label for="apellidos" class="register-label">
                            <i class="bi bi-person-badge"></i> Apellidos
                        </label>
                        <input id="apellidos" type="text" class="register-input @error('apellidos') is-invalid @enderror" name="apellidos" value="{{ old('apellidos') }}" placeholder="Ej. Hernández" required autocomplete="family-name">
                        @error('apellidos')
                        <span class="register-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- CORREO -->
                    <div class="register-field">
                        <label for="email" class="register-label">
                            <i class="bi bi-envelope"></i> Correo electrónico
                        </label>
                        <input id="email" type="email" class="register-input @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" placeholder="ejemplo@correo.com" required autocomplete="email">
                        @error('email')
                        <span class="register-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- TELÉFONO -->
                    <div class="register-field">
                        <label for="telefono" class="register-label">
                            <i class="bi bi-telephone"></i> Teléfono de contacto
                        </label>
                        <input id="telefono" type="text" class="register-input @error('telefono') is-invalid @enderror" name="telefono" value="{{ old('telefono') }}" placeholder="7221234567" required>
                        @error('telefono')
                        <span class="register-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- SUCURSAL -->
                    <div class="register-field">
                        <label for="sucursal_id" class="register-label">
                            <i class="bi bi-geo-alt"></i> Sucursal asignada
                        </label>
                        <select id="sucursal_id" name="sucursal_id" class="register-input @error('sucursal_id') is-invalid @enderror" required>
                            <option value="" disabled selected>Seleccione una sucursal...</option>
                            @foreach(\App\Models\Sucursal::all() as $sucursal)
                                <option value="{{ $sucursal->id }}" {{ old('sucursal_id') == $sucursal->id ? 'selected' : '' }}>
                                    {{ $sucursal->nombre }}
                                </option>
                            @endforeach
                        </select>
                        @error('sucursal_id')
                        <span class="register-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- CONTRASEÑA -->
                    <div class="register-field">
                        <label for="password" class="register-label">
                            <i class="bi bi-lock"></i> Contraseña
                        </label>
                        <div class="register-input-wrapper">
                            <input id="password" type="password" class="register-input @error('password') is-invalid @enderror" name="password" placeholder="Ingrese una contraseña" required autocomplete="new-password">
                            <button type="button" class="password-toggle" onclick="togglePassword('password', this)" aria-label="Mostrar contraseña">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                        @error('password')
                        <span class="register-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- CONFIRMAR CONTRASEÑA -->
                    <div class="register-field">
                        <label for="password-confirm" class="register-label">
                            <i class="bi bi-shield-lock"></i> Confirmar contraseña
                        </label>
                        <div class="register-input-wrapper">
                            <input id="password-confirm" type="password" class="register-input" name="password_confirmation" placeholder="Repita la contraseña" required autocomplete="new-password">
                            <button type="button" class="password-toggle" onclick="togglePassword('password-confirm', this)" aria-label="Mostrar contraseña">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>

                    <!-- BOTÓN -->
                    <button type="submit" class="btn-register">
                        Crear cuenta <i class="bi bi-arrow-right"></i>
                    </button>
                </form>

                <!-- REGRESAR AL LOGIN -->
                <div class="register-back">
                    ¿Ya tiene una cuenta?
                    <a href="{{ route('login') }}">
                        Iniciar sesión <i class="bi bi-arrow-up-right"></i>
                    </a>
                </div>
            </div>

        </div>
    </div>

    <script>
        function togglePassword(inputId, button) {
            const input = document.getElementById(inputId);
            const icon = button.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.className = 'bi bi-eye-slash';
                button.setAttribute('aria-label', 'Ocultar contraseña');
            } else {
                input.type = 'password';
                icon.className = 'bi bi-eye';
                button.setAttribute('aria-label', 'Mostrar contraseña');
            }
        }
    </script>

@endsection

