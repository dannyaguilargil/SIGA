<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Acceso') · SIGA</title>
    <style>
        :root { color-scheme: light; --ink:#182235; --muted:#637086; --line:#dce2ec; --brand:#4b42ef; --brand-dark:#3830d5; --surface:#fff; --canvas:#f5f7fb; --danger:#be123c; }
        * { box-sizing:border-box; } body { margin:0; min-height:100vh; background:radial-gradient(circle at 18% 0%, #e7e9ff 0, transparent 31rem),var(--canvas); color:var(--ink); font-family:Inter,ui-sans-serif,system-ui,-apple-system,"Segoe UI",sans-serif; }
        .page { width:min(100% - 2rem, 1080px); min-height:100vh; margin:auto; display:grid; grid-template-columns:1fr 1.1fr; align-items:center; gap:4rem; padding:3rem 0; }
        .brand { padding:2rem; } .mark { display:inline-grid; place-items:center; width:54px; height:54px; border-radius:16px; background:var(--brand); color:white; font-size:1.35rem; font-weight:800; letter-spacing:-.08em; box-shadow:0 14px 30px #4b42ef42; }
        h1 { font-size:clamp(2.3rem,5vw,4.2rem); line-height:1.02; letter-spacing:-.065em; margin:1.35rem 0 .85rem; max-width:500px; } .brand p { max-width:430px; color:var(--muted); font-size:1.08rem; line-height:1.7; }
        .card { width:min(100%, 470px); justify-self:end; background:var(--surface); border:1px solid var(--line); border-radius:24px; box-shadow:0 22px 65px #28375b14; padding:42px; }
        .card h2 { margin:0; font-size:1.8rem; letter-spacing:-.045em; } .intro { color:var(--muted); margin:.65rem 0 1.75rem; line-height:1.5; }
        label { display:block; margin:1.1rem 0 .45rem; color:#364152; font-size:.91rem; font-weight:650; } input,select { appearance:none; width:100%; padding:.86rem 1rem; border:1px solid #abb5c4; border-radius:10px; background:white; color:var(--ink); font:inherit; outline:none; transition:.15s; }
        input:focus,select:focus { border-color:var(--brand); box-shadow:0 0 0 4px #4b42ef1a; } select { appearance:auto; } .password { position:relative; } .password input { padding-right:3.1rem; } .toggle { position:absolute; right:.5rem; top:.45rem; min-width:38px; min-height:38px; padding:0; border:0; color:var(--brand); background:transparent; cursor:pointer; font-weight:700; }
        .primary, .google { width:100%; min-height:52px; border-radius:999px; font:inherit; font-weight:750; cursor:pointer; transition:transform .15s, background .15s; } .primary { margin-top:1.7rem; border:0; background:var(--brand); color:white; } .primary:hover { background:var(--brand-dark); transform:translateY(-1px); }
        .divider { display:flex; align-items:center; gap:14px; color:var(--muted); margin:1.45rem 0; font-size:.85rem; } .divider::before,.divider::after { content:""; height:1px; background:var(--line); flex:1; }
        .google { display:flex; align-items:center; justify-content:center; gap:12px; border:1px solid #182235; background:white; color:#263142; text-decoration:none; } .google:hover { background:#f8f9fc; transform:translateY(-1px); } .google span { font-size:1.25rem; font-weight:800; background:conic-gradient(from -35deg,#4285f4 0 25%,#34a853 0 43%,#fbbc05 0 61%,#ea4335 0 82%,#4285f4 0); -webkit-background-clip:text; background-clip:text; color:transparent; }
        .options { display:flex; justify-content:space-between; align-items:center; gap:.75rem; margin-top:1.35rem; font-size:.88rem; } .check { display:flex; align-items:center; gap:.5rem; white-space:nowrap; } .check input { width:17px; height:17px; accent-color:var(--brand); padding:0; } a { color:var(--brand); text-decoration:none; } a:hover { text-decoration:underline; }
        .alert { margin:1rem 0; padding:.8rem 1rem; border-radius:10px; font-size:.9rem; } .alert-error { color:#9f1239; background:#fff1f2; } .alert-success { color:#166534; background:#f0fdf4; } .errors { margin:.5rem 0 0; padding:0; list-style:none; color:var(--danger); font-size:.82rem; }
        .footer { margin-top:1.65rem; color:var(--muted); font-size:.88rem; text-align:center; } .back { display:inline-block; margin-top:1.3rem; font-size:.9rem; } .organization-help { margin:.7rem 0 0; color:var(--muted); font-size:.84rem; } .organization-help.is-match { color:#166534; }
        @media (max-width:800px) { .page { grid-template-columns:1fr; gap:0; width:min(100% - 1.5rem, 470px); padding:1.5rem 0; } .brand { text-align:center; padding:1.4rem .6rem 2rem; } .brand h1 { font-size:2.3rem; margin:.8rem auto .5rem; } .brand p { font-size:.96rem; margin:auto; } .mark { width:44px; height:44px; border-radius:13px; } .card { justify-self:stretch; padding:30px 24px; border-radius:20px; } }
    </style>
</head>
<body>
    <main class="page">
        <section class="brand" aria-label="SIGA">
            <div class="mark">S</div>
            <h1>SIGA</h1>
            <p><strong>Sistema de Gestión de Identidades y Accesos.</strong> Un acceso simple y seguro para las personas y servicios de tu organización.</p>
        </section>
        <section class="card">
            @yield('content')
        </section>
    </main>
    <script>
        document.querySelectorAll('[data-password-toggle]').forEach((button) => button.addEventListener('click', () => {
            const input = document.getElementById(button.dataset.passwordToggle);
            input.type = input.type === 'password' ? 'text' : 'password';
            button.setAttribute('aria-label', input.type === 'password' ? 'Mostrar contraseña' : 'Ocultar contraseña');
            button.textContent = input.type === 'password' ? 'Mostrar' : 'Ocultar';
        }));
        const organizationName = document.getElementById('organization_name');
        const organizationId = document.getElementById('organization_id');
        const organizationHelp = document.getElementById('organization-help');

        if (organizationName && organizationId && organizationHelp) {
            const organizations = [...document.querySelectorAll('#organization-options option')];
            const normalize = (value) => value.trim().normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLocaleLowerCase('es');

            const selectOrganization = () => {
                const match = organizations.find((option) => normalize(option.value) === normalize(organizationName.value));

                organizationId.value = match?.dataset.organizationId ?? '';
                organizationHelp.classList.toggle('is-match', Boolean(match));
                organizationHelp.textContent = match
                    ? `Organización encontrada: ${match.value}.`
                    : 'No existe una coincidencia exacta; se creará al registrar tu cuenta.';
            };

            organizationName.addEventListener('input', selectOrganization);
            organizationName.addEventListener('change', selectOrganization);
            selectOrganization();
        }
    </script>
</body>
</html>
