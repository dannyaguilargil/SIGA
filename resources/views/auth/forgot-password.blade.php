@extends('layouts.auth')

@section('title', 'Restablecer contraseña')

@section('content')
    <h2>Restablece tu contraseña</h2>
    <p class="intro">Te enviaremos un enlace seguro para recuperar el acceso.</p>
    @if (session('status')) <div class="alert alert-success">{{ session('status') }}</div> @endif
    @if (session('error')) <div class="alert alert-error">{{ session('error') }}</div> @endif
    <form method="POST" action="{{ route('password.email') }}">
        @csrf
        <label for="email">Correo electrónico</label>
        <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required autofocus>
        @error('email') <ul class="errors"><li>{{ $message }}</li></ul> @enderror
        <button class="primary" type="submit">ENVIAR ENLACE</button>
    </form>
    <a class="back" href="{{ route('login') }}">← Volver al inicio de sesión</a>
@endsection
