<!DOCTYPE html>
<html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Inicio · SIGA</title></head>
<body style="margin:0;display:grid;min-height:100vh;place-items:center;background:#f5f7fb;font-family:system-ui,sans-serif;color:#182235">
<main style="max-width:600px;padding:3rem;text-align:center;background:white;border:1px solid #dce2ec;border-radius:24px"><strong style="color:#4b42ef">SIGA</strong><h1>Acceso concedido</h1><p>Hola, {{ auth()->user()->name }}. Has iniciado sesión correctamente.</p><form method="POST" action="{{ route('logout') }}">@csrf<button style="border:0;border-radius:999px;padding:.75rem 1.2rem;background:#4b42ef;color:white;font-weight:700;cursor:pointer">Cerrar sesión</button></form></main>
</body></html>
