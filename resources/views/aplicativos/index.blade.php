@extends('layouts.workspace')

@section('title', 'Aplicativos y módulos')
@section('eyebrow', 'Administración')
@section('heading', 'Aplicativos y módulos')

@section('page_styles')
    .intro{margin:-24px 0 28px}.grid{display:grid;grid-template-columns:330px 1fr;gap:20px}.card h2{margin:0 0 5px;font-size:1.05rem}.card p{font-size:.84rem}.app{padding:18px 0;border-top:1px solid var(--line)}.app:first-child{border-top:0}.app-title{display:flex;justify-content:space-between;gap:12px;align-items:center}.app h3{margin:0;font-size:1rem}.tag{padding:4px 7px;color:var(--brand);background:#edecff;border-radius:99px;font-size:.7rem;font-weight:800}.items{display:flex;gap:7px;flex-wrap:wrap;margin:13px 0}.item{padding:6px 9px;background:#f5f7fb;border-radius:7px;font-size:.8rem}.small{display:grid;grid-template-columns:1fr 110px auto;gap:8px}@media(max-width:800px){.grid{grid-template-columns:1fr}}@media(max-width:560px){.small{grid-template-columns:1fr}.primary{width:100%}}
@endsection

@section('content')
    <p class="intro">Registra los aplicativos de tu organización y sus módulos o roles disponibles.</p>
    <div class="grid">
        <section class="card"><h2>Nuevo aplicativo</h2><p>Crea primero el aplicativo y luego agrega sus módulos o roles.</p><form method="POST" action="{{ route('applications.store') }}">@csrf<input class="field" name="nombre_aplicativo" placeholder="Ej. Portal institucional" value="{{ old('nombre_aplicativo') }}" required>@error('nombre_aplicativo')<small style="color:#be123c">{{ $message }}</small>@enderror<button class="primary" type="submit">+ Agregar aplicativo</button></form></section>
        <section class="card"><h2>Catálogo</h2>@forelse($aplicativos as $aplicativo)<article class="app"><div class="app-title"><h3>{{ $aplicativo->nombre_aplicativo }}</h3><span class="tag">{{ $aplicativo->activo ? 'ACTIVO' : 'INACTIVO' }}</span></div><div class="items">@forelse($aplicativo->modulos as $modulo)<span class="item">{{ $modulo->tipo === 'rol' ? 'Rol' : 'Módulo' }}: {{ $modulo->nombre_modulo }}</span>@empty<span class="item">Sin módulos ni roles</span>@endforelse</div><form class="small" method="POST" action="{{ route('applications.elements.store', $aplicativo) }}">@csrf<input class="field" style="margin:0" name="nombre_modulo" placeholder="Nombre del módulo o rol" required><select class="field" style="margin:0" name="tipo"><option value="modulo">Módulo</option><option value="rol">Rol</option></select><button class="primary" type="submit">Agregar</button></form></article>@empty<p class="intro">Aún no hay aplicativos creados.</p>@endforelse</section>
    </div>
@endsection
