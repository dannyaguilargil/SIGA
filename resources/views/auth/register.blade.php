@extends('layouts.auth')

@section('title', 'Crear cuenta')

@section('content')
    <h2>Crea tu cuenta</h2>
    <p class="intro">Comienza a usar SIGA con tus credenciales.</p>
    <form method="POST" action="{{ route('register.store') }}">
        @csrf
        <label for="name">Nombre completo</label>
        <input id="name" name="name" value="{{ old('name') }}" autocomplete="name" required autofocus>
        @error('name') <ul class="errors"><li>{{ $message }}</li></ul> @enderror
        <label for="email">Correo electrónico</label>
        <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required>
        @error('email') <ul class="errors"><li>{{ $message }}</li></ul> @enderror
        <label for="organization_name">Organización</label>
        <input
            id="organization_name"
            name="organization_name"
            list="organization-options"
            value="{{ old('organization_name', $organizations->firstWhere('id', old('organization_id'))?->name) }}"
            placeholder="Escribe para buscar una organización"
            autocomplete="off"
            maxlength="255"
            required
        >
        <input id="organization_id" name="organization_id" type="hidden" value="{{ old('organization_id') }}">
        <datalist id="organization-options">
            @foreach ($organizations as $organization)
                <option value="{{ $organization->name }}" data-organization-id="{{ $organization->id }}"></option>
            @endforeach
        </datalist>
        @error('organization_id') <ul class="errors"><li>{{ $message }}</li></ul> @enderror
        @error('organization_name') <ul class="errors"><li>{{ $message }}</li></ul> @enderror
        <p id="organization-help" class="organization-help">Escribe para buscar. Si no existe una coincidencia exacta, se creará al registrar tu cuenta.</p>
        <label for="password">Contraseña <small>(mínimo 12 caracteres)</small></label>
        <div class="password"><input id="password" name="password" type="password" autocomplete="new-password" required><button class="toggle" type="button" data-password-toggle="password" aria-label="Mostrar contraseña">Mostrar</button></div>
        <label for="password_confirmation">Confirma tu contraseña</label>
        <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required>
        @error('password') <ul class="errors"><li>{{ $message }}</li></ul> @enderror
        <button class="primary" type="submit">CREAR CUENTA</button>
    </form>
    <p class="footer">¿Ya tienes cuenta? <a href="{{ route('login') }}">Inicia sesión.</a></p>
@endsection
