@extends('layouts.auth')

@section('title', 'Iniciar sesión')

@section('content')
    <h2>Bienvenido de nuevo</h2>
    <p class="intro">Ingresa a tu cuenta SIGA.</p>

    @if (session('status')) <div class="alert alert-success">{{ session('status') }}</div> @endif
    @if ($errors->has('google')) <div class="alert alert-error">{{ $errors->first('google') }}</div> @endif

    <form method="POST" action="{{ route('login.store') }}">
        @csrf
        <label for="email">Correo electrónico</label>
        <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required autofocus>
        @error('email') <ul class="errors"><li>{{ $message }}</li></ul> @enderror

        <label for="password">Contraseña</label>
        <div class="password">
            <input id="password" name="password" type="password" autocomplete="current-password" required>
            <button class="toggle" type="button" data-password-toggle="password" aria-label="Mostrar contraseña">Mostrar</button>
        </div>
        @error('password') <ul class="errors"><li>{{ $message }}</li></ul> @enderror

        <button class="primary" type="submit">INICIAR SESIÓN</button>

        <div class="divider">o</div>
        <a class="google" href="{{ route('google.redirect') }}"><span aria-hidden="true">G</span> Continuar con Google</a>

        <div class="options">
            <label class="check" for="remember"><input id="remember" name="remember" type="checkbox" value="1"> Recordarme</label>
            <a href="{{ route('password.request') }}">¿Olvidaste tu contraseña?</a>
        </div>
    </form>

    <p class="footer">¿Aún no tienes cuenta? <a href="{{ route('register') }}">Créala ahora.</a></p>
@endsection
