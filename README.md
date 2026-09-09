# Portal Ciudadano

Sitio institucional con módulo de denuncias ciudadanas en PHP vanilla + MySQL.

## Características

- Landing pública con información de servicios.
- Formulario de denuncias con categorías, adjuntos y opción de anonimato.
- Seguimiento de denuncias por código único.
- Envío de mail de confirmación al denunciante.
- Panel de administración con login, filtros, y actualización de estado.

## Instalación

1. Crear la base de datos ejecutando `database/schema.sql`:
   ```
   mysql -u usuario -p < database/schema.sql
   ```
2. Copiar `config/config.example.php` a `config/config.php` y completar con los datos reales del hosting (DB, URL del sitio, remitente de mail).
3. Crear el usuario administrador:
   ```
   php database/crear_admin.php usuario contraseña "Nombre Completo"
   ```
4. Dar permisos de escritura al usuario del servidor web sobre `uploads/denuncias/`.
5. Acceder a `admin/login.php` con las credenciales creadas.

## Estructura

- `index.php`, `denuncias.php`, `seguimiento.php` — páginas públicas.
- `actions/guardar_denuncia.php` — procesamiento del formulario.
- `admin/` — panel de administración.
- `includes/` — conexión a base de datos, helpers y plantilla de mail.
- `database/` — esquema SQL y script de creación de administrador.
