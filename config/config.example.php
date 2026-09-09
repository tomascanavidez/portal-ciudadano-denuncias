<?php
// Copiar este archivo como config.php y completar con los datos reales del hosting.
// config.php está excluido de git para no exponer credenciales.

define('DB_HOST', 'localhost');
define('DB_NAME', 'a0190001_port');
define('DB_USER', 'tu_usuario');
define('DB_PASS', 'tu_password');
define('DB_CHARSET', 'utf8mb4');

define('SITE_NAME', 'Portal Ciudadano');
define('SITE_SLOGAN', 'Gestión de trámites y denuncias en línea');
define('SITE_URL', 'http://localhost/dev/denu');

define('MAIL_FROM_ADDRESS', 'denuncias@tudominio.com');
define('MAIL_FROM_NAME', 'Portal Ciudadano - Denuncias');

define('UPLOAD_DIR', __DIR__ . '/../uploads/denuncias/');
define('UPLOAD_URL', SITE_URL . '/uploads/denuncias/');
define('MAX_UPLOAD_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_UPLOAD_TYPES', ['image/jpeg', 'image/png', 'application/pdf']);

session_start();
date_default_timezone_set('America/Argentina/Buenos_Aires');
error_reporting(E_ALL);
ini_set('display_errors', '0'); // poner en '1' solo en desarrollo local
