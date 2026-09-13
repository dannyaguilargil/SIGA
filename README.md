# SIGA

Sistema de Gestión de Identidades y Accesos, construido con Laravel.

## Requisitos

- PHP 8.2 o superior
- Composer

## Inicio rápido

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan serve
```

La aplicación quedará disponible en `http://127.0.0.1:8000`.

La configuración local inicial utiliza SQLite en `database/database.sqlite`; el
archivo de base de datos está ignorado por Git. Las sesiones y la caché se
mantienen en archivos. Para crear las tablas ejecuta `php artisan migrate`.

> El PHP actual de este entorno no tiene habilitado el controlador SQLite. En un
> equipo de desarrollo instala o habilita la extensión `pdo_sqlite` antes de
> ejecutar las migraciones.

## Datos sensibles y Google OAuth

`.env` está excluido del control de versiones; nunca subas ese archivo ni claves
de Google. Para activar el inicio de sesión con Google, crea unas credenciales
OAuth 2.0 de tipo **Aplicación web** en Google Cloud y define en tu `.env`:

```dotenv
GOOGLE_CLIENT_ID=
GOOGLE_CLIENT_SECRET=
GOOGLE_REDIRECT_URI="${APP_URL}/auth/google/callback"
```

Registra exactamente esa misma URL como URI de redirección autorizada en Google.
