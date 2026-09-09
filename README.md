# Portal Ciudadano

Sitio institucional con módulo de denuncias ciudadanas en PHP vanilla + MySQL.

## Características

- Landing pública con información de servicios.
- Formulario de denuncias con categorías, adjuntos y opción de anonimato.
- Seguimiento de denuncias por código único.
- Envío de mail de confirmación al denunciante.
- Panel de administración con login, filtros, y actualización de estado.

## Instalación

1. Crear la base de datos vacía desde el panel del hosting (cPanel u otro) con el nombre que corresponda (por ejemplo `a0190001_port`) y asignarle un usuario con permisos.
2. Importar las tablas ejecutando `database/schema.sql` (que ya usa ese nombre de base con `USE`):
   ```
   mysql -u usuario -p < database/schema.sql
   ```
3. Copiar `config/config.example.php` a `config/config.php` y completar con los datos reales del hosting (DB, URL del sitio, remitente de mail).
4. Crear el usuario administrador:
   ```
   php database/crear_admin.php usuario contraseña "Nombre Completo"
   ```
5. Dar permisos de escritura al usuario del servidor web sobre `uploads/denuncias/`.
6. Acceder a `admin/login.php` con las credenciales creadas.

## Estructura

- `index.php`, `denuncias.php`, `seguimiento.php` — páginas públicas.
- `actions/guardar_denuncia.php` — procesamiento del formulario.
- `admin/` — panel de administración.
- `includes/` — conexión a base de datos, helpers y plantilla de mail.
- `database/` — esquema SQL y script de creación de administrador.
