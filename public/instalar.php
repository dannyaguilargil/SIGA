<?php

declare(strict_types=1);

/*
 * Instalador de una sola ejecución para hosting compartido.
 *
 * En SiteGround, coloque la aplicación en ../siga y el contenido de
 * public/ en public_html. El instalador detecta también la estructura de
 * desarrollo, por lo que no depende de una ruta absoluta del servidor.
 */

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Artisan;

function applicationPath(): string
{
    foreach ([dirname(__DIR__), dirname(__DIR__).'/siga'] as $path) {
        if (is_file($path.'/vendor/autoload.php') && is_file($path.'/bootstrap/app.php')) {
            return $path;
        }
    }

    throw new RuntimeException('No se encontró la aplicación. Debe estar en una carpeta llamada "siga" junto a public_html e incluir vendor/.');
}

function envValue(string $value): string
{
    return '"'.str_replace(['\\', '"', "\n", "\r"], ['\\\\', '\\"', '', ''], $value).'"';
}

$error = null;
$installed = false;

try {
    $basePath = applicationPath();
    $installedFile = $basePath.'/storage/framework/siga-installed';
    $installed = is_file($installedFile);

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && ! $installed) {
        $required = ['app_url', 'db_host', 'db_database', 'db_username', 'db_password'];

        foreach ($required as $field) {
            if (trim((string) ($_POST[$field] ?? '')) === '') {
                throw new RuntimeException('Completa todos los campos de la base de datos.');
            }
        }

        $appUrl = filter_var((string) $_POST['app_url'], FILTER_VALIDATE_URL);
        if (! $appUrl) {
            throw new RuntimeException('La URL del sitio no es válida. Ejemplo: https://pruebas.tudominio.com');
        }

        $env = implode("\n", [
            'APP_NAME='.envValue('SIGA'),
            'APP_ENV=production',
            'APP_KEY=base64:'.base64_encode(random_bytes(32)),
            'APP_DEBUG=false',
            'APP_URL='.envValue(rtrim($appUrl, '/')),
            'APP_LOCALE=es',
            'APP_FALLBACK_LOCALE=es',
            'LOG_CHANNEL=stack',
            'LOG_LEVEL=error',
            'DB_CONNECTION=mysql',
            'DB_HOST='.envValue((string) $_POST['db_host']),
            'DB_PORT='.envValue((string) ($_POST['db_port'] ?: '3306')),
            'DB_DATABASE='.envValue((string) $_POST['db_database']),
            'DB_USERNAME='.envValue((string) $_POST['db_username']),
            'DB_PASSWORD='.envValue((string) $_POST['db_password']),
            'SESSION_DRIVER=file',
            'CACHE_STORE=file',
            'QUEUE_CONNECTION=sync',
            'MAIL_MAILER=log',
            'GOOGLE_CLIENT_ID=',
            'GOOGLE_CLIENT_SECRET=',
            'GOOGLE_REDIRECT_URI='.envValue(rtrim($appUrl, '/').'/auth/google/callback'),
            '',
        ]);

        if (file_put_contents($basePath.'/.env', $env, LOCK_EX) === false) {
            throw new RuntimeException('No fue posible crear .env. Revisa los permisos de escritura de la carpeta siga.');
        }

        chdir($basePath);
        require $basePath.'/vendor/autoload.php';
        $app = require $basePath.'/bootstrap/app.php';
        $app->make(Kernel::class)->bootstrap();

        Artisan::call('config:clear');
        Artisan::call('migrate', ['--force' => true]);
        Artisan::call('storage:link', ['--force' => true]);

        if (file_put_contents($installedFile, date(DATE_ATOM), LOCK_EX) === false) {
            throw new RuntimeException('La base de datos fue creada, pero no se pudo bloquear el instalador. Revisa permisos de storage/framework.');
        }

        header('Location: /login');
        exit;
    }
} catch (Throwable $exception) {
    $error = $exception->getMessage();
}

$defaultUrl = (! empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http').'://'.($_SERVER['HTTP_HOST'] ?? '');
?><!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Instalar SIGA</title>
    <style>body{font-family:system-ui,sans-serif;background:#f5f7fb;color:#172033;margin:0;padding:2rem}main{max-width:620px;margin:auto;background:#fff;padding:2rem;border-radius:12px;box-shadow:0 3px 20px #17203318}label{display:block;margin-top:1rem;font-weight:600}input{box-sizing:border-box;width:100%;padding:.65rem;margin-top:.35rem;border:1px solid #b9c2d0;border-radius:6px}button{margin-top:1.5rem;padding:.75rem 1rem;border:0;border-radius:6px;background:#0b5bd3;color:#fff;font-weight:700;cursor:pointer}.error{background:#fff0f0;color:#8b1e1e;padding:1rem;border-radius:6px}.ok{background:#eefbf2;color:#155b2c;padding:1rem;border-radius:6px}</style>
</head>
<body><main>
    <h1>Instalación de SIGA</h1>
    <?php if ($installed) { ?>
        <p class="ok">SIGA ya está instalado. <a href="/login">Ir al inicio de sesión</a>.</p>
    <?php } elseif ($error) { ?>
        <p class="error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
    <?php } ?>
    <?php if (! $installed) { ?>
        <p>Introduce los datos de la base MySQL creada en Site Tools. Este formulario solo se puede ejecutar una vez.</p>
        <form method="post">
            <label>URL del sitio<input type="url" name="app_url" value="<?= htmlspecialchars($defaultUrl, ENT_QUOTES, 'UTF-8') ?>" required></label>
            <label>Servidor MySQL<input name="db_host" value="localhost" required></label>
            <label>Puerto MySQL<input name="db_port" value="3306" required></label>
            <label>Nombre de base de datos<input name="db_database" required></label>
            <label>Usuario MySQL<input name="db_username" required></label>
            <label>Contraseña MySQL<input type="password" name="db_password" required></label>
            <button type="submit">Instalar y abrir SIGA</button>
        </form>
    <?php } ?>
</main></body>
</html>
