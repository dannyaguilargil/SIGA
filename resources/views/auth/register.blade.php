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
        <label for="organization_id">Organización</label>
        <select id="organization_id" name="organization_id">
            <option value="" disabled @selected(! old('organization_id'))>Seleccionar organización</option>
            @foreach ($organizations as $organization)
                <option value="{{ $organization->id }}" @selected(old('organization_id') == $organization->id)>{{ $organization->name }}</option>
            @endforeach
        </select>
        @error('organization_id') <ul class="errors"><li>{{ $message }}</li></ul> @enderror
        <p class="organization-help">¿No encuentras tu organización? <button class="text-button" type="button" data-organization-create>+ Crear nueva organización</button></p>
        <div id="new-organization" class="create-organization @if(old('organization_name')) is-visible @endif">
            <label for="organization_name">Nombre de la nueva organización</label>
            <input id="organization_name" name="organization_name" value="{{ old('organization_name') }}" maxlength="255">
            @error('organization_name') <ul class="errors"><li>{{ $message }}</li></ul> @enderror
        </div>
        <label for="password">Contraseña <small>(mínimo 12 caracteres)</small></label>
        <div class="password"><input id="password" name="password" type="password" autocomplete="new-password" required><button class="toggle" type="button" data-password-toggle="password" aria-label="Mostrar contraseña">Mostrar</button></div>
        <label for="password_confirmation">Confirma tu contraseña</label>
        <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required>
        @error('password') <ul class="errors"><li>{{ $message }}</li></ul> @enderror
        <button class="primary" type="submit">CREAR CUENTA</button>
    </form>
    <p class="footer">¿Ya tienes cuenta? <a href="{{ route('login') }}">Inicia sesión.</a></p>
@endsection
