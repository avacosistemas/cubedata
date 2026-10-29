# Evaluación y guía de migración a PHP 8.3

## Alcance revisado

El sitio es principalmente estático. El único PHP de aplicación activo encontrado es `ajax/enviarEmail.php`, llamado desde `js/validar.js` para enviar el formulario de contacto de `index.html`. También hay una copia vendorizada de PHPMailer 5.1 en la raíz (`class.phpmailer.php`, `class.smtp.php`, `class.pop3.php`). `OLD/index_const*.php` son páginas HTML de mantenimiento, no código PHP ejecutable. No se encontró `composer.json`, `composer.lock`, configuración de base de datos ni otros endpoints PHP.

## Hallazgos que bloquean o arriesgan PHP 8.3

1. **PHPMailer 5.1 es incompatible y está fuera de soporte.** Usa `each()` (eliminada en PHP 8.0), `preg_replace()` con el modificador `/e` (eliminado) y `set_magic_quotes_runtime()` (eliminada). En particular, el método de transporte SMTP recorre líneas con `each()`, así que puede fallar al enviar. No conviene parchear esta copia antigua: migrar a una versión mantenida.
2. **La configuración SMTP no selecciona SMTP.** `ajax/enviarEmail.php` asigna `Host`, usuario y contraseña pero no llama a `IsSMTP()`. PHPMailer 5.1 por defecto usa el transporte `mail()`, así que esos valores probablemente no se usan. En PHP sobre Windows normalmente hace falta configurar un MTA local para `mail()`; para SMTP autenticado debe configurarse explícitamente el transporte y TLS.
3. **Credencial SMTP incrustada en el código.** La contraseña aparece como literal en `ajax/enviarEmail.php`. Tratarla como comprometida: rotarla en el proveedor y pasar los nuevos valores por variables de entorno/configuración fuera del directorio público. No copiar el valor a documentación, repositorios ni logs.
4. **Entrada sin validación en el servidor.** Se leen directamente claves `$_POST`; llamadas GET o campos omitidos producen valores indefinidos y, desde PHP 8, advertencias. Los valores del usuario se concatenan directamente en HTML, permitiendo inyección de HTML en los correos. Validar método, presencia/tipo, tamaño, email y escapar con `htmlspecialchars()` antes de insertar en HTML.
5. **El cliente no define `name` en los campos.** `index.html` usa IDs y `js/validar.js` arma el POST por AJAX, por lo que funciona solo con JavaScript. La validación del navegador es auxiliar: el endpoint PHP debe validar por sí mismo. El AJAX es síncrono (`async: false`) y su callback de éxito no verifica si el cuerpo de respuesta indica que el envío falló.

## Cambios recomendados

### Dependencias

- Eliminar el uso de los tres archivos PHPMailer 5.1 en la raíz después de migrar sus consumidores.
- Incorporar Composer y PHPMailer mantenido. A la fecha de esta revisión, el repositorio oficial recomienda Composer y la rama `^7.0.0`; usar `composer require phpmailer/phpmailer:^7.0` y guardar `composer.lock` en control de versiones. Confirmar la versión resuelta y los requisitos de PHP en el entorno de destino antes del despliegue.
- Sustituir el `require "../class.phpmailer.php"` por `require __DIR__ . '/../vendor/autoload.php';`, importar `PHPMailer\PHPMailer\PHPMailer` y `PHPMailer\PHPMailer\Exception`, y crear `new PHPMailer(true)`.
- Configurar en el código `isSMTP()`, host, usuario y contraseña desde el entorno, `SMTPAuth = true`, TLS con STARTTLS/587 o SMTPS/465 según el proveedor, remitente autorizado y destinatario. No asumir que `localhost:25` acepta autenticación/TLS.
- Extensiones: habilitar `openssl` para SMTP cifrado; no se aprecia uso de base de datos ni necesidad actual de `mysqli`/`pdo_mysql`. `mbstring` es recomendable para contenido UTF-8, pero revisar requisitos del paquete elegido.

Ejemplo de instalación/despliegue desde la raíz del proyecto:

```powershell
composer require phpmailer/phpmailer:^7.0
composer check-platform-reqs
composer install --no-dev --prefer-dist --optimize-autoloader
```

En el servidor de producción se despliegan `composer.json`, `composer.lock` y `vendor/` (o se ejecuta `composer install` durante el build). Nunca depender de una instalación global no fijada.

### Endpoint y formulario

- En `ajax/enviarEmail.php`: comprobar `$_SERVER['REQUEST_METHOD'] === 'POST'`; validar y normalizar cada campo con `filter_input()`/validación explícita; aplicar límites de longitud; validar email con `filter_var()`; rechazar entradas inválidas con HTTP 400/405 y una respuesta JSON consistente.
- Escapar todos los campos interpolados en el cuerpo HTML con `htmlspecialchars($valor, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')`; establecer `CharSet = 'UTF-8'` y proporcionar un `AltBody` legible.
- No devolver detalles SMTP ni credenciales al navegador. Registrar errores de envío en el log privado del servidor y responder con estado HTTP acorde.
- En `js/validar.js` manejar la respuesta estructurada (éxito/error), retirar `async: false` y evitar mostrar éxito si el servidor respondió que no pudo enviar.
- Opcionalmente añadir protección antispam/rate limit y un campo `name` real para permitir fallback sin JavaScript; el procesamiento hoy depende de AJAX.

## Levantar en PHP 8.3

1. Configurar Apache/Nginx/IIS con PHP-FPM o FastCGI en PHP 8.3 y publicar la raíz del proyecto como document root, dado que `index.html`, `assets/`, `css/` y `js/` están en esa raíz. Asegurar que `/ajax/enviarEmail.php` se ejecute por PHP, no se sirva como texto.
2. Instalar PHP 8.3 y Composer en el build/servidor, habilitar `openssl` y desplegar dependencias con el `composer.lock` correspondiente.
3. Definir credenciales y host/puerto SMTP mediante variables de entorno protegidas. Rotar antes la contraseña que estaba hardcodeada. Validar desde el servidor salida de red al puerto SMTP y certificado/TLS.
4. Configurar `date.timezone` explícitamente, `display_errors=Off` en producción, `log_errors=On` y un destino de logs fuera del directorio público. No usar `phpinfo()` publicado.
5. Ejecutar PHP 8.3 CLI: `php -v`, `php --ini`, `php -m` y `composer check-platform-reqs`. La consola disponible durante esta revisión es PHP **7.2.34**, por lo que no sirvió para certificar ejecución en PHP 8.3.
6. Antes del cambio de tráfico, probar el endpoint en staging con envío correcto, campos ausentes/inválidos, método GET, caracteres acentuados y fallo/conexión SMTP. Revisar logs y confirmar que el correo llega con HTML y texto alternativo.

## Orden sugerido

1. Rotar la credencial SMTP expuesta y acordar con el proveedor los parámetros SMTP/TLS.
2. Migrar a PHPMailer mantenido con Composer y ajustar el endpoint para usar SMTP explícitamente.
3. Implementar validación/escape/respuestas HTTP y ajustar el cliente AJAX.
4. Desplegar en staging con PHP 8.3, verificar plataforma y flujo de correo; luego desplegar y monitorizar errores.

## Referencias

- Inventario: `ajax/enviarEmail.php`, `js/validar.js`, `index.html`, `class.phpmailer.php`, `class.smtp.php`, `class.pop3.php`.
- Manual PHP: cambios incompatibles en PHP 8.0 (incluye eliminación de `each()`): <https://www.php.net/manual/en/migration80.incompatible.php>.
- PHPMailer oficial: instalación por Composer, ejemplo SMTP y guía para versiones heredadas: <https://github.com/PHPMailer/PHPMailer>.
