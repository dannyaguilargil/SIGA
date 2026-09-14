# Instalar SIGA en SiteGround

Esta aplicación se distribuye como Laravel. Para que la URL principal abra el
inicio de sesión, el servidor debe exponer únicamente la carpeta `public`.

## Carga inicial sin SSH

1. En **Site Tools > Site > MySQL**, crea una base de datos y un usuario, y
   asígnale acceso a esa base.
2. En el administrador de archivos crea, al mismo nivel, estas carpetas:

   ```text
   /.../siga/          (aplicación privada)
   /.../public_html/   (sitio visible)
   ```

3. Sube el proyecto completo a `siga`, incluyendo `vendor/`, pero sin `.env`.
   No subas `node_modules`.
4. Copia **el contenido** de `siga/public/` (no la carpeta `public` misma) a
   `public_html/`.
5. Edita `public_html/index.php`. Sustituye las rutas `../vendor` y
   `../bootstrap` por `../siga/vendor` y `../siga/bootstrap` respectivamente.
   Haz lo mismo con la ruta de mantenimiento `../storage`, que debe ser
   `../siga/storage`.
6. Abre `https://tu-dominio/instalar.php`, completa los datos MySQL y pulsa
   **Instalar y abrir SIGA**. Se crea el `.env`, se ejecutan las migraciones y
   se bloquea el instalador. La redirección final es a `/login`.

La carpeta `siga/storage` y `siga/bootstrap/cache` deben permitir escritura al
usuario web. En SiteGround normalmente basta con permisos 775.

## Actualizaciones posteriores

Mantén siempre el `.env`, `storage/` y la base de datos. Reemplaza solamente
el código de `siga` y el contenido público. No vuelvas a abrir el instalador:
el archivo `storage/framework/siga-installed` evita una segunda instalación.

## Google y correo

Tras instalar, añade al `.env` `GOOGLE_CLIENT_ID` y
`GOOGLE_CLIENT_SECRET`, y registra `https://tu-dominio/auth/google/callback`
en Google Cloud. Para recuperación de contraseña configura un proveedor SMTP;
por defecto los correos se guardan en el log y no se envían.
