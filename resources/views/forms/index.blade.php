@extends('layouts.workspace')

@section('title', 'Formularios')
@section('eyebrow', 'Administración')
@section('heading', 'Formularios de solicitud')

@section('page_styles')
    .top-actions{margin:-58px 0 28px;text-align:right}.new,.edit{display:inline-block;padding:.78rem 1rem;background:var(--brand);color:#fff;border:0;border-radius:9px;text-decoration:none;font-weight:750;font-size:.88rem}.intro{margin:8px 0 28px}.list{display:grid;gap:12px}.form-card{display:flex;align-items:center;justify-content:space-between;gap:16px;padding:20px;background:var(--surface);border:1px solid var(--line);border-radius:14px}.form-card h2{margin:0 0 5px;font-size:1rem;font-weight:750}.form-card p{margin:0;color:var(--muted);font-size:.83rem}.actions{display:flex;gap:10px;align-items:center}.tag{padding:5px 8px;color:var(--brand);background:#f0efff;border-radius:999px;font-size:.72rem;font-weight:800}.edit{background:#edecff;color:var(--brand)}.delete{padding:.78rem 1rem;color:var(--danger);background:#fff1f2;border:0;border-radius:9px;font-size:.88rem;font-weight:750;cursor:pointer}.empty{padding:40px;background:#fff;border:1px dashed #bfc7d7;border-radius:14px;text-align:center}@media(max-width:560px){.top-actions{text-align:left;margin:0 0 20px}.form-card{align-items:flex-start;flex-direction:column}}
@endsection

@section('content')
    <p class="intro">Crea y ajusta los datos que los miembros deben completar para solicitar credenciales.</p><div class="top-actions"><a class="new" href="{{ route('forms.create') }}">+ Crear formulario</a></div>
    <div class="list">@forelse ($forms as $form)<article class="form-card"><div><h2>{{ $form->name }}</h2><p>{{ $form->description ?: 'Sin descripción' }} · {{ $form->requests_count }} solicitudes</p></div><div class="actions"><span class="tag">{{ $form->is_active ? 'ACTIVO' : 'INACTIVO' }}</span><a class="edit" href="{{ route('forms.edit', $form) }}">Editar</a><form method="POST" action="{{ route('forms.destroy', $form) }}" onsubmit="return confirm('¿Eliminar este formulario y sus solicitudes asociadas?')">@csrf @method('DELETE')<button class="delete" type="submit">Eliminar</button></form></div></article>@empty<div class="empty">Aún no has creado formularios. Crea el primero para que los miembros puedan enviar solicitudes.</div>@endforelse</div>
@endsection
