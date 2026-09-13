@extends('layouts.auth')

@section('title', 'Nueva contraseña')

@section('content')
    <h2>Crea una nueva contraseña</h2>
    <p class="intro">Elige una contraseña robusta para proteger tu cuenta.</p>
    <form method="POST" action="{{ route('password.update') }}">
        @csrf
        <input name="token" type="hidden" value="{{ $token }}">
        <label for="email">Correo electrónico</label>
        <input id="email" name="email" type="email" value="{{ old('email', $email) }}" autocomplete="email" required autofocus>
        @error('email') <ul class="errors"><li>{{ $message }}</li></ul> @enderror
        <label for="password">Nueva contraseña <small>(mínimo 12 caracteres)</small></label>
        <div class="password"><input id="password" name="password" type="password" autocomplete="new-password" required><button class="toggle" type="button" data-password-toggle="password" aria-label="Mostrar contraseña">Mostrar</button></div>
        <label for="password_confirmation">Confirma tu contraseña</label>
        <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required>
        @error('password') <ul class="errors"><li>{{ $message }}</li></ul> @enderror
        <button class="primary" type="submit">ACTUALIZAR CONTRASEÑA</button>
    </form>
@endsection
