@extends('layouts.app')

@section('title', 'Iniciar Sesión - Restaurante Rodavento')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card card-custom border-0 shadow-lg overflow-hidden">
                <div class="p-4 text-center text-white" style="background-color: var(--rv-navy); border-bottom: 3px solid var(--rv-gold);">
                    <i class="bi bi-compass text-warning" style="font-size: 2.75rem;"></i>
                    <h3 class="fw-bold mt-2 mb-0 font-serif">Hotel Rodavento</h3>
                    <small class="text-white-50 text-uppercase letter-spacing-1">Acceso a Gestión Gastronómica & Sala</small>
                </div>

                <div class="card-body p-4 p-md-5 bg-white">
                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="email" class="form-label fw-semibold small text-uppercase text-muted">Correo Electrónico</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="bi bi-envelope"></i></span>
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                                       name="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="nombre@rodavento.com">
                            </div>
                            @error('email')
                                <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label fw-semibold small text-uppercase text-muted">Contraseña</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="bi bi-lock"></i></span>
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
                                       name="password" required autocomplete="current-password" placeholder="••••••••">
                            </div>
                            @error('password')
                                <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember" checked>
                                <label class="form-check-label small" for="remember">
                                    Recordar credenciales
                                </label>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-rv-primary w-100 py-2 fs-6 mb-0">
                            <i class="bi bi-box-arrow-in-right me-1"></i> Entrar al Sistema
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
